<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\CuentaItem;
use App\Models\Paciente;
use App\Models\Pago;
use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        // Validar que el descuento no supere el subtotal de la línea
        $subtotalLinea = $data['cantidad'] * (float) $data['precio_unitario'];
        $descuento     = (float) ($data['descuento'] ?? 0);

        if ($descuento > $subtotalLinea) {
            return back()->with('error',
                'El descuento no puede superar el subtotal de la línea ($' .
                number_format($subtotalLinea, 2) . ').'
            )->withInput();
        }

        $cuenta->items()->create([
            'servicio_id'      => $data['servicio_id'] ?? null,
            'user_id'          => auth()->id(),
            'concepto'         => $data['concepto'],
            'cantidad'         => $data['cantidad'],
            'precio_unitario'  => $data['precio_unitario'],
            'descuento'        => $descuento,
            'motivo_descuento' => $data['motivo_descuento'] ?? null,
            'notas'            => $data['notas'] ?? null,
        ]);

        return back()->with('success', 'Cargo agregado a la cuenta.');
    }

    /* =========================================================
     |  ELIMINAR CARGO
     ========================================================= */
    public function destroyItem(CuentaItem $item): RedirectResponse
    {
        if ($item->cuenta->estado !== 'abierta') {
            return back()->with('error', 'No puedes modificar una cuenta cerrada.');
        }

        $item->delete();

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

        $cuenta->update([
            'descuento_global' => $data['descuento_global'],
            'motivo_descuento' => $data['motivo_descuento'] ?? null,
        ]);

        $cuenta->recalcular();

        // Si el descuento dejó el total en 0, se cierra automáticamente
        $this->cerrarSiSaldoCero($cuenta);

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

        $data = $request->validate([
            'monto'      => ['required', 'numeric', 'min:0.01'],
            'metodo'     => ['required', 'in:efectivo,tarjeta,transferencia,otro'],
            'referencia' => ['nullable', 'string', 'max:100'],
            'pagado_en'  => ['nullable', 'date'],
            'notas'      => ['nullable', 'string'],
        ]);

        // No permitir pagar más que el saldo pendiente
        if ((float) $data['monto'] > (float) $cuenta->saldo) {
            return back()->with('error',
                'El monto no puede superar el saldo pendiente ($' .
                number_format((float) $cuenta->saldo, 2) . ').'
            )->withInput();
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

        // El evento saved en Pago recalcula la cuenta automáticamente
        $cuenta->refresh();

        // 🎯 CIERRE AUTOMÁTICO: si el saldo llegó a 0, cerramos la cuenta
        $cerrada = $this->cerrarSiSaldoCero($cuenta);

        $mensaje = $cerrada
            ? "Pago registrado. Folio: {$pago->folio}. ✅ Cuenta cerrada automáticamente."
            : "Pago registrado. Folio: {$pago->folio}. Saldo pendiente: $" .
              number_format((float) $cuenta->saldo, 2);

        return back()->with('success', $mensaje);
    }

    /* =========================================================
     |  CANCELAR PAGO
     ========================================================= */
    public function cancelarPago(Request $request, Pago $pago): RedirectResponse
    {
        if ($pago->estado === 'cancelado') {
            return back()->with('error', 'Este pago ya estaba cancelado.');
        }

        // Si la cuenta ya está cerrada, no se puede cancelar el pago
        // (habría que reabrir la cuenta primero)
        if ($pago->cuenta->estado !== 'abierta') {
            return back()->with('error',
                'No puedes cancelar pagos de una cuenta cerrada. Reabre la cuenta primero.'
            );
        }

        $data = $request->validate([
            'motivo_cancelacion' => ['required', 'string', 'max:500'],
        ]);

        $pago->update([
            'estado'             => 'cancelado',
            'motivo_cancelacion' => $data['motivo_cancelacion'],
            'cancelado_en'       => now(),
            'cancelado_por'      => auth()->id(),
        ]);

        $pago->cuenta->recalcular();

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
            return back()->with('error',
                'No puedes cerrar la cuenta. Aún hay un saldo pendiente de $' .
                number_format((float) $cuenta->saldo, 2) . '.'
            );
        }

        $cuenta->update([
            'estado'     => 'cerrada',
            'cerrada_en' => now(),
        ]);

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

    // inline: se ve en el navegador (visor de PDF)
    // attachment: se descarga directo
    return $pdf->stream("recibo-{$pago->folio}.pdf");
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

        return $pdf->stream("estado-cuenta-{$cuenta->folio}.pdf");
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