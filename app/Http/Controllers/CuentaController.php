<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cuenta;
use App\Models\CuentaItem;
use App\Models\Paciente;
use App\Models\Pago;
use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Enums\AuditEvent;
use App\Services\AuditoriaService;

class CuentaController extends Controller
{
    /* =========================================================
     |  LISTADO DE CUENTAS
     ========================================================= */
    public function index(Request $request): View
    {
        $filtroEstado = $request->input('estado');   // abierta | cerrada | todas
        $soloConSaldo = $request->boolean('con_saldo', false);
        $buscar       = $request->input('buscar');

        $query = Cuenta::query()
            ->with(['paciente', 'items'])
            ->orderByDesc('created_at');

        if ($filtroEstado && $filtroEstado !== 'todas') {
            $query->where('estado', $filtroEstado);
        }

        if ($soloConSaldo) {
            $query->where('saldo', '>', 0);
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('folio', 'like', "%{$buscar}%")
                  ->orWhereHas('paciente', function ($sub) use ($buscar) {
                      $sub->where('nombre', 'like', "%{$buscar}%")
                          ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                          ->orWhere('curp', 'like', "%{$buscar}%");
                  });
            });
        }

        $cuentas = $query->paginate(20)->withQueryString();

        $stats = [
            'abiertas'     => Cuenta::where('estado', 'abierta')->count(),
            'por_cobrar'   => (float) Cuenta::where('saldo', '>', 0)->sum('saldo'),
            'cobrado_hoy'  => (float) Cuenta::whereDate('updated_at', today())
                                    ->where('estado', '!=', 'cancelada')
                                    ->sum('pagado'),
            'cerradas_mes' => Cuenta::where('estado', 'cerrada')
                                    ->whereMonth('cerrada_en', now()->month)
                                    ->whereYear('cerrada_en', now()->year)
                                    ->count(),
        ];

        return view('cuentas.index', compact('cuentas', 'filtroEstado', 'soloConSaldo', 'buscar', 'stats'));
    }

    /* =========================================================
     |  MOSTRAR CUENTA DEL PACIENTE
     ========================================================= */
    public function delPaciente(Paciente $paciente): View
    {
        $cuenta = $paciente->cuentaAbierta
            ?? $paciente->cuentas()->latest('id')->first();

        if (! $cuenta) {
            $cuenta = $paciente->obtenerCuentaAbierta();
        }

        $cuenta->load([
            'items.servicio',
            'items.cita',
            'paciente',
            'pagos.user',
            'pagos.canceladoPor',
        ]);

        $servicios = Servicio::where('activo', true)
            ->where('precio', '>', 0)
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre', 'precio', 'precio_descripcion']);

        return view('cuentas.show', compact('paciente', 'cuenta', 'servicios'));
    }

    /* =========================================================
     |  AGREGAR CARGO (servicio o manual)
     ========================================================= */
    public function agregarItem(Request $request, Cuenta $cuenta): RedirectResponse
    {
        if ($cuenta->estado !== 'abierta') {
            return back()->with('error', 'No puedes modificar una cuenta cerrada.');
        }

        $data = $request->validate([
            'servicio_id'      => ['nullable', 'exists:servicios,id'],
            'concepto'         => ['required', 'string', 'max:200'],
            'cantidad'         => ['required', 'integer', 'min:1', 'max:999'],
            'precio_unitario'  => ['required', 'numeric', 'min:0'],
            'descuento'        => ['nullable', 'numeric', 'min:0'],
            'motivo_descuento' => ['nullable', 'string', 'max:200'],
            'notas'            => ['nullable', 'string'],
        ]);

        $subtotalLinea = $data['cantidad'] * (float) $data['precio_unitario'];
        $descuento     = (float) ($data['descuento'] ?? 0);

        if ($descuento > $subtotalLinea) {
            return back()->with('error',
                'El descuento no puede superar el subtotal de la línea ($' .
                number_format($subtotalLinea, 2) . ').'
            )->withInput();
        }

        $item = $cuenta->items()->create([
            'servicio_id'      => $data['servicio_id'] ?? null,
            'user_id'          => auth()->id(),
            'concepto'         => $data['concepto'],
            'cantidad'         => $data['cantidad'],
            'precio_unitario'  => $data['precio_unitario'],
            'descuento'        => $descuento,
            'motivo_descuento' => $data['motivo_descuento'] ?? null,
            'notas'            => $data['notas'] ?? null,
        ]);

        AuditoriaService::registrar(
            AuditEvent::CUENTA_CARGO_AGREGADO,
            'cuentas',
            "Cargo agregado a cuenta {$cuenta->folio}: {$item->concepto} (x{$item->cantidad})",
            $cuenta,
            [],
            [
                'item_id'         => $item->id,
                'concepto'        => $item->concepto,
                'cantidad'        => $item->cantidad,
                'precio_unitario' => (float) $item->precio_unitario,
                'descuento'       => $descuento,
            ]
        );

        return back()->with('success', 'Cargo agregado a la cuenta.');
    }

    /* =========================================================
     |  COBRO AUTOMÁTICO DE CITA A LA CUENTA DEL PACIENTE
     ========================================================= */
    /**
     * Agrega automáticamente el cargo de una cita a la cuenta abierta
     * del paciente. Es idempotente: si la cita ya fue cobrada, no la
     * vuelve a agregar.
     *
     * Puede llamarse desde CitaController u otro flujo:
     *   app(CuentaController::class)->cobrarCita($cita);
     */
    public function cobrarCita(Cita $cita): ?CuentaItem
    {
        $paciente = $cita->paciente;

        if (! $paciente) {
            return null;
        }

        // Cuenta abierta (se crea si no existe)
        $cuenta = $paciente->obtenerCuentaAbierta();

        if (! $cuenta || $cuenta->estado !== 'abierta') {
            return null;
        }

        // Idempotencia: no duplicar el cargo de la misma cita
        $existente = $cuenta->items()->where('cita_id', $cita->id)->first();
        if ($existente) {
            return $existente;
        }

        // Datos del cargo a partir de la cita
        $servicio = $cita->servicio;

        $concepto = $servicio
            ? "Servicio: {$servicio->nombre}"
            : 'Consulta / procedimiento';

        $precio = (float) ($cita->precio ?? $servicio?->precio ?? 0);

        if ($precio <= 0) {
            // Sin precio no generamos cargo (evita líneas en $0)
            return null;
        }

        $item = $cuenta->items()->create([
            'servicio_id'     => $servicio?->id,
            'cita_id'         => $cita->id,
            'user_id'         => auth()->id(),
            'concepto'        => $concepto,
            'cantidad'        => 1,
            'precio_unitario' => $precio,
            'descuento'       => 0,
            'notas'           => "Cargo automático por cita #{$cita->id}",
        ]);

        AuditoriaService::registrar(
            AuditEvent::CUENTA_CARGO_AGREGADO,
            'cuentas',
            "Cargo automático por cita {$cita->id} en cuenta {$cuenta->folio}: {$item->concepto}",
            $cuenta,
            [],
            [
                'item_id'         => $item->id,
                'cita_id'         => $cita->id,
                'concepto'        => $item->concepto,
                'cantidad'        => $item->cantidad,
                'precio_unitario' => (float) $item->precio_unitario,
                'automatico'      => true,
            ]
        );

        return $item;
    }

    /**
     * Revierte (elimina) el cargo automático asociado a una cita.
     * Útil al cancelar una cita. Devuelve true si se eliminó algo.
     */
    public function revertirCargoCita(Cita $cita): bool
    {
        $item = CuentaItem::where('cita_id', $cita->id)->first();

        if (! $item) {
            return false;
        }

        $cuenta = $item->cuenta;

        if (! $cuenta || $cuenta->estado !== 'abierta') {
            return false;
        }

        $snapshot = [
            'item_id'         => $item->id,
            'cita_id'         => $cita->id,
            'concepto'        => $item->concepto,
            'cantidad'        => $item->cantidad,
            'precio_unitario' => (float) $item->precio_unitario,
            'importe'         => (float) $item->importe,
        ];

        $item->delete();

        AuditoriaService::registrar(
            AuditEvent::CUENTA_CARGO_ELIMINADO,
            'cuentas',
            "Cargo automático revertido por cancelación de cita {$cita->id} en cuenta {$cuenta->folio}: {$snapshot['concepto']}",
            $cuenta,
            $snapshot,
            [],
            ['automatico' => true, 'motivo' => 'cancelacion_cita']
        );

        return true;
    }

    /* =========================================================
     |  ELIMINAR CARGO
     ========================================================= */
    public function destroyItem(CuentaItem $item): RedirectResponse
    {
        $cuenta = $item->cuenta;

        if ($cuenta->estado !== 'abierta') {
            return back()->with('error', 'No puedes modificar una cuenta cerrada.');
        }

        // Guardar snapshot antes del delete
        $snapshot = [
            'item_id'         => $item->id,
            'concepto'        => $item->concepto,
            'cantidad'        => $item->cantidad,
            'precio_unitario' => (float) $item->precio_unitario,
            'importe'         => (float) $item->importe,
        ];

        $item->delete();

        AuditoriaService::registrar(
            AuditEvent::CUENTA_CARGO_ELIMINADO,
            'cuentas',
            "Cargo eliminado de cuenta {$cuenta->folio}: {$snapshot['concepto']}",
            $cuenta,
            $snapshot,
            []
        );

        return back()->with('success', 'Cargo eliminado.');
    }

    /* =========================================================
     |  APLICAR DESCUENTO GLOBAL
     ========================================================= */
    public function aplicarDescuento(Request $request, Cuenta $cuenta): RedirectResponse
    {
        if ($cuenta->estado !== 'abierta') {
            return back()->with('error', 'No puedes modificar una cuenta cerrada.');
        }

        $data = $request->validate([
            'descuento_global' => ['required', 'numeric', 'min:0'],
            'motivo_descuento' => ['nullable', 'string', 'max:200'],
        ]);

        if ((float) $data['descuento_global'] > (float) $cuenta->subtotal) {
            return back()->with('error',
                'El descuento no puede superar el subtotal de la cuenta ($' .
                number_format((float) $cuenta->subtotal, 2) . ').'
            )->withInput();
        }

        $antes = (float) $cuenta->descuento_global;

        $cuenta->update([
            'descuento_global' => $data['descuento_global'],
            'motivo_descuento' => $data['motivo_descuento'] ?? null,
        ]);

        $cuenta->recalcular();
        $this->cerrarSiSaldoCero($cuenta);

        AuditoriaService::registrar(
            AuditEvent::CUENTA_DESCUENTO,
            'cuentas',
            "Descuento global aplicado a cuenta {$cuenta->folio}: $" .
            number_format((float) $data['descuento_global'], 2),
            $cuenta,
            ['descuento_global' => $antes],
            ['descuento_global' => (float) $data['descuento_global']],
            ['motivo' => $data['motivo_descuento'] ?? null]
        );

        return back()->with('success', 'Descuento aplicado.');
    }

    /* =========================================================
     |  REGISTRAR PAGO
     ========================================================= */
   
public function registrarPago(Request $request, Cuenta $cuenta): RedirectResponse
{
    if ($cuenta->estado !== 'abierta') {
        return back()->with('error', 'No puedes registrar pagos en una cuenta cerrada.');
    }

    // Normalizar método a minúsculas y quitar espacios
    $request->merge([
        'metodo'     => is_string($request->input('metodo'))
                            ? strtolower(trim($request->input('metodo')))
                            : $request->input('metodo'),
        'referencia' => is_string($request->input('referencia'))
                            ? trim($request->input('referencia'))
                            : $request->input('referencia'),
        'monto'      => is_string($request->input('monto'))
                            ? str_replace([',', ' '], '', trim($request->input('monto')))
                            : $request->input('monto'),
    ]);

    $data = $request->validate(
        [
            'monto' => [
                'required',
                'numeric',
                'decimal:0,2',       // máximo 2 decimales
                'min:0.01',
                'max:999999.99',
            ],
            'metodo' => [
                'required',
                'string',
                'in:efectivo,tarjeta,transferencia,otro',
            ],
            'referencia' => [
                'nullable',
                'string',
                'max:100',
                // Requerida si el método es tarjeta o transferencia
                function (string $attribute, $value, $fail) use ($request) {
                    $metodo = $request->input('metodo');
                    if (in_array($metodo, ['tarjeta', 'transferencia'], true)
                        && (is_null($value) || trim((string) $value) === '')) {
                        $fail('La referencia es obligatoria para pagos con tarjeta o transferencia.');
                    }
                },
            ],
            'pagado_en' => [
                'nullable',
                'date',
                'before_or_equal:now',
                function (string $attribute, $value, $fail) use ($cuenta) {
                    if ($cuenta->created_at && $value) {
                        try {
                            $fechaPago = \Carbon\Carbon::parse($value);
                            if ($fechaPago->lt($cuenta->created_at->startOfDay())) {
                                $fail('La fecha de pago no puede ser anterior a la creación de la cuenta.');
                            }
                        } catch (\Throwable $e) {
                            // Si no se puede parsear, ya lo cubre 'date'
                        }
                    }
                },
            ],
            'notas' => ['nullable', 'string', 'max:1000'],
        ],
        [
            'monto.required'        => 'El monto del pago es obligatorio.',
            'monto.numeric'         => 'El monto debe ser un número válido.',
            'monto.decimal'         => 'El monto solo puede tener hasta 2 decimales.',
            'monto.min'             => 'El monto debe ser mayor a $0.00.',
            'monto.max'             => 'El monto excede el límite permitido.',
            'metodo.required'       => 'Debes seleccionar un método de pago.',
            'metodo.in'             => 'El método de pago no es válido. Opciones: efectivo, tarjeta, transferencia, otro.',
            'referencia.max'        => 'La referencia no puede superar los 100 caracteres.',
            'pagado_en.date'        => 'La fecha de pago no es válida.',
            'pagado_en.before_or_equal' => 'La fecha de pago no puede ser futura.',
            'notas.max'             => 'Las notas no pueden superar los 1000 caracteres.',
        ]
    );

    // Validación de negocio: no permitir pagos en $0
    if ((float) $data['monto'] <= 0) {
        return back()
            ->withErrors(['monto' => 'El monto debe ser mayor a $0.00.'])
            ->withInput();
    }

    // Validación de negocio: no superar el saldo pendiente
    if ((float) $data['monto'] > (float) $cuenta->saldo) {
        return back()
            ->withErrors([
                'monto' => 'El monto no puede superar el saldo pendiente ($' .
                           number_format((float) $cuenta->saldo, 2) . ').',
            ])
            ->withInput();
    }

    // Validación de negocio: si la cuenta no tiene saldo, no tiene sentido registrar pago
    if ((float) $cuenta->saldo <= 0) {
        return back()
            ->withErrors(['monto' => 'Esta cuenta no tiene saldo pendiente.'])
            ->withInput();
    }

    $pago = $cuenta->pagos()->create([
        'user_id'    => auth()->id(),
        'folio'      => Pago::generarFolio(),
        'monto'      => $data['monto'],
        'metodo'     => $data['metodo'],
        'referencia' => $data['referencia'] ?? null,
        'pagado_en'  => $data['pagado_en'] ?? now(),
        'notas'      => $data['notas'] ?? null,
        'estado'     => 'aplicado',
    ]);

    $cuenta->refresh();
    $cerrada = $this->cerrarSiSaldoCero($cuenta);

    AuditoriaService::registrar(
        AuditEvent::CUENTA_PAGO,
        'cuentas',
        "Pago registrado en cuenta {$cuenta->folio}: {$pago->folio} por $" .
        number_format((float) $pago->monto, 2) . " ({$pago->metodo})",
        $cuenta,
        [],
        [],
        [
            'pago_id'   => $pago->id,
            'folio'     => $pago->folio,
            'monto'     => (float) $pago->monto,
            'metodo'    => $pago->metodo,
            'referencia'=> $pago->referencia,
            'cerro_cuenta' => $cerrada,
        ]
    );

    $mensaje = $cerrada
        ? "Pago registrado. Folio: {$pago->folio}.  Cuenta cerrada automáticamente."
        : "Pago registrado. Folio: {$pago->folio}. Saldo pendiente: $" .
          number_format((float) $cuenta->saldo, 2);

    return back()->with('success', $mensaje);
}

    /* =========================================================
     |  CANCELAR PAGO
     ========================================================= */
    
public function cancelarPago(Request $request, Pago $pago): RedirectResponse
{
    // Validación de estado del pago
    if ($pago->estado === 'cancelado') {
        return back()
            ->withErrors(['motivo_cancelacion' => 'Este pago ya estaba cancelado.'])
            ->withInput();
    }

    // Validación: el pago debe tener cuenta
    $cuenta = $pago->cuenta;
    if (! $cuenta) {
        return back()
            ->withErrors(['motivo_cancelacion' => 'El pago no está asociado a una cuenta válida.']);
    }

    if ($cuenta->estado !== 'abierta') {
        return back()
            ->withErrors([
                'motivo_cancelacion' =>
                    'No puedes cancelar pagos de una cuenta cerrada. Reabre la cuenta primero.',
            ]);
    }

    // Normalizar motivo
    $request->merge([
        'motivo_cancelacion' => is_string($request->input('motivo_cancelacion'))
            ? trim($request->input('motivo_cancelacion'))
            : $request->input('motivo_cancelacion'),
    ]);

    $data = $request->validate(
        [
            'motivo_cancelacion' => [
                'required',
                'string',
                'min:5',           // mínimo razonable
                'max:500',
                'not_regex:/^\s*$/', // no solo espacios
            ],
        ],
        [
            'motivo_cancelacion.required'  => 'Debes indicar el motivo de la cancelación.',
            'motivo_cancelacion.string'    => 'El motivo de cancelación no es válido.',
            'motivo_cancelacion.min'       => 'El motivo de cancelación debe tener al menos 5 caracteres.',
            'motivo_cancelacion.max'       => 'El motivo de cancelación no puede superar los 500 caracteres.',
            'motivo_cancelacion.not_regex' => 'El motivo de cancelación no puede estar vacío.',
        ]
    );

    $pago->update([
        'estado'             => 'cancelado',
        'motivo_cancelacion' => $data['motivo_cancelacion'],
        'cancelado_en'       => now(),
        'cancelado_por'      => auth()->id(),
    ]);

    $cuenta->recalcular();
    $cuenta->refresh();

    AuditoriaService::registrar(
        AuditEvent::CUENTA_PAGO_CANCELADO,
        'cuentas',
        "Pago cancelado {$pago->folio} de cuenta {$cuenta->folio}. Motivo: {$data['motivo_cancelacion']}",
        $cuenta,
        ['estado' => 'aplicado'],
        ['estado' => 'cancelado'],
        [
            'pago_id'            => $pago->id,
            'folio'              => $pago->folio,
            'monto'              => (float) $pago->monto,
            'motivo_cancelacion' => $data['motivo_cancelacion'],
        ]
    );

    return back()->with('success', 'Pago cancelado.');
}

    /* =========================================================
     |  CERRAR CUENTA MANUALMENTE
     ========================================================= */
    public function cerrar(Cuenta $cuenta): RedirectResponse
    {
        if ($cuenta->estado !== 'abierta') {
            return back()->with('error', 'Esta cuenta ya no está abierta.');
        }

        if ((float) $cuenta->saldo > 0) {
            AuditoriaService::registrar(
                AuditEvent::ACCESO_DENEGADO,
                'cuentas',
                "Intento de cerrar cuenta {$cuenta->folio} con saldo pendiente de $" .
                number_format((float) $cuenta->saldo, 2),
                $cuenta
            );

            return back()->with('error',
                'No puedes cerrar la cuenta. Aún hay un saldo pendiente de $' .
                number_format((float) $cuenta->saldo, 2) . '.'
            );
        }

        $cuenta->update([
            'estado'     => 'cerrada',
            'cerrada_en' => now(),
        ]);

        AuditoriaService::registrar(
            AuditEvent::CUENTA_CERRADA,
            'cuentas',
            "Cuenta {$cuenta->folio} cerrada manualmente",
            $cuenta,
            ['estado' => 'abierta'],
            ['estado' => 'cerrada']
        );

        return back()->with('success', 'Cuenta cerrada correctamente.');
    }

    /* =========================================================
     |  RECIBO DE PAGO (vista HTML)
     ========================================================= */
    public function recibo(Pago $pago)
    {
        $pago->load(['cuenta.paciente', 'user', 'canceladoPor']);

        $pdf = Pdf::loadView('cuentas.pdf.recibo', compact('pago'))
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true);

        $output = $pdf->output();

        AuditoriaService::registrar(
            AuditEvent::RECIBO_PDF_GENERADO,
            'cuentas',
            "Recibo PDF generado: {$pago->folio} (cuenta {$pago->cuenta->folio})",
            $pago->cuenta,
            [],
            [],
            [
                'pago_id'      => $pago->id,
                'folio'        => $pago->folio,
                'monto'        => (float) $pago->monto,
                'tamano_bytes' => strlen($output),
            ]
        );

        return response($output, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="recibo-' . $pago->folio . '.pdf"',
        ]);
    }

    /* =========================================================
     |  PDF ESTADO DE CUENTA
     ========================================================= */
    public function pdf(Cuenta $cuenta)
    {
        $cuenta->load([
            'paciente',
            'items.servicio',
            'items.cita',
            'pagos.user',
            'pagos.canceladoPor',
        ]);

        $pdf = Pdf::loadView('cuentas.pdf.estado-cuenta', compact('cuenta'))
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true);

        $output = $pdf->output();

        AuditoriaService::registrar(
            AuditEvent::CUENTA_PDF_GENERADO,
            'cuentas',
            "Estado de cuenta PDF generado: {$cuenta->folio} (total $" .
            number_format((float) $cuenta->total, 2) . ")",
            $cuenta,
            [],
            [],
            [
                'folio'        => $cuenta->folio,
                'total'        => (float) $cuenta->total,
                'saldo'        => (float) $cuenta->saldo,
                'tamano_bytes' => strlen($output),
            ]
        );

        return response($output, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="estado-cuenta-' . $cuenta->folio . '.pdf"',
        ]);
    }

    /* =========================================================
     |  MÉTODOS PRIVADOS
     ========================================================= */

    /**
     * Cierra la cuenta automáticamente si el saldo es 0 y aún está abierta.
     * Devuelve true si la cuenta se cerró en esta llamada.
     */
    private function cerrarSiSaldoCero(Cuenta $cuenta): bool
    {
        // Refrescamos para leer los valores actualizados
        $cuenta->refresh();

        if ($cuenta->estado === 'abierta' && (float) $cuenta->saldo <= 0) {
            $cuenta->update([
                'estado'     => 'cerrada',
                'cerrada_en' => now(),
            ]);

            return true;
        }

        return false;
    }
}