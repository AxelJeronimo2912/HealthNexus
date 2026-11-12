<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\SolicitudEstudio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Enums\AuditEvent;
use App\Services\AuditoriaService;

class SolicitudEstudioController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $buscar = $request->input('buscar');
        $estado = $request->input('estado');
        $servicioTipo = $request->input('tipo_servicio');

        $query = SolicitudEstudio::query()
            ->with(['paciente', 'servicio', 'archivos'])
            ->orderByDesc('created_at');

        if ($this->esMedico($user) && !$user->hasRole('administrador')) {
            $query->where('medico_solicita_id', $user->id);
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('folio', 'like', "%{$buscar}%")
                  ->orWhere('tipo_estudio', 'like', "%{$buscar}%")
                  ->orWhereHas('paciente', function ($sub) use ($buscar) {
                      $sub->where('nombre', 'like', "%{$buscar}%")
                          ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                          ->orWhere('curp', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($estado && $estado !== 'todas') {
            $query->where('estado', $estado);
        }

        if ($servicioTipo && $servicioTipo !== 'todos') {
            $query->whereHas('servicio', fn($q) => $q->where('tipo', $servicioTipo));
        }

        $solicitudes = $query->paginate(20)->withQueryString();

        $statsQuery = SolicitudEstudio::query();
        if ($this->esMedico($user) && !$user->hasRole('administrador')) {
            $statsQuery->where('medico_solicita_id', $user->id);
        }

        $stats = [
            'solicitados'   => (clone $statsQuery)->where('estado', 'solicitado')->count(),
            'en_proceso'    => (clone $statsQuery)->where('estado', 'en_proceso')->count(),
            'con_resultado' => (clone $statsQuery)->where('estado', 'resultado_listo')->count(),
            'total_mes'     => (clone $statsQuery)->whereMonth('created_at', now()->month)->count(),
        ];

        return view('estudios.index', compact('solicitudes', 'buscar', 'estado', 'servicioTipo', 'stats'));
    }

    public function create(Request $request): View
    {
        $pacienteId = $request->input('paciente_id');
        $paciente = $pacienteId ? Paciente::find($pacienteId) : null;

        $servicios = Servicio::where('activo', true)
            ->whereIn('tipo', ['imagenes', 'laboratorio'])
            ->orderBy('tipo')
            ->orderBy('nombre')
            ->get();

        return view('estudios.create', compact('paciente', 'servicios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'paciente_id'          => ['required', 'exists:pacientes,id'],
            'servicio_id'          => ['required', 'exists:servicios,id'],
            'tipo_estudio'         => ['required', 'string', 'max:100'],
            'indicaciones'         => ['nullable', 'string'],
            'notas'                => ['nullable', 'string'],
            'medico_solicita_id'   => ['nullable', 'exists:users,id'],
            'cargar_a_cuenta'      => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();
        try {
            $paciente = Paciente::findOrFail($data['paciente_id']);
            $servicio = Servicio::findOrFail($data['servicio_id']);

            $solicitud = SolicitudEstudio::create([
                'folio'              => SolicitudEstudio::generarFolio(),
                'paciente_id'        => $paciente->id,
                'servicio_id'        => $servicio->id,
                'medico_solicita_id' => $data['medico_solicita_id'] ?? null,
                'user_id'            => auth()->id(),
                'tipo_estudio'       => $data['tipo_estudio'],
                'indicaciones'       => $data['indicaciones'] ?? null,
                'notas'              => $data['notas'] ?? null,
                'estado'             => 'solicitado',
                'solicitado_en'      => now(),
            ]);

            if ($request->boolean('cargar_a_cuenta', true) && (float) $servicio->precio > 0) {
                $cuenta = $paciente->obtenerCuentaAbierta();

                if ($cuenta && $cuenta->estado === 'abierta') {
                    $item = $cuenta->items()->create([
                        'servicio_id'     => $servicio->id,
                        'user_id'         => auth()->id(),
                        'concepto'        => "Estudio: {$servicio->nombre} - {$solicitud->tipo_estudio}",
                        'cantidad'        => 1,
                        'precio_unitario' => (float) $servicio->precio,
                        'descuento'       => 0,
                        'notas'           => "Solicitud {$solicitud->folio}",
                    ]);

                    $solicitud->update([
                        'cuenta_id'      => $cuenta->id,
                        'cuenta_item_id' => $item->id,
                    ]);

                    AuditoriaService::registrar(
                        AuditEvent::CUENTA_CARGO_AGREGADO,
                        'cuentas',
                        "Cargo por estudio {$solicitud->folio} en cuenta {$cuenta->folio}",
                        $cuenta,
                        [],
                        ['item_id' => $item->id, 'concepto' => $item->concepto, 'solicitud' => $solicitud->folio]
                    );
                }
            }

            AuditoriaService::registrar(
                AuditEvent::CREATED,
                'estudios',
                "Solicitud de estudio creada: {$solicitud->folio} ({$solicitud->tipo_estudio})",
                $solicitud
            );

            DB::commit();

           return redirect()
                ->route('estudios.index')
                ->with('success', "Solicitud {$solicitud->folio} creada correctamente.");

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'No se pudo crear la solicitud: ' . $e->getMessage())->withInput();
        }
    }

    public function show(SolicitudEstudio $solicitud): View
    {
        $this->autorizarAcceso($solicitud);

        $solicitud->load([
            'paciente', 'servicio', 'cuenta', 'cuentaItem',
            'medicoSolicita', 'user', 'archivos.user',
        ]);

        return view('estudios.show', compact('solicitud'));
    }

    public function updateEstado(Request $request, SolicitudEstudio $solicitud): RedirectResponse
    {
        $this->autorizarAcceso($solicitud);

        $data = $request->validate([
            'estado' => ['required', 'in:solicitado,en_proceso,resultado_listo,entregado,cancelado'],
        ]);

        $antes = $solicitud->estado;

        $solicitud->update([
            'estado'        => $data['estado'],
            'resultado_en'  => $data['estado'] === 'resultado_listo' ? now() : $solicitud->resultado_en,
            'entregado_en'  => $data['estado'] === 'entregado' ? now() : $solicitud->entregado_en,
        ]);

        AuditoriaService::registrar(
            AuditEvent::UPDATED,
            'estudios',
            "Estado de solicitud {$solicitud->folio} cambiado: {$antes} → {$data['estado']}",
            $solicitud,
            ['estado' => $antes],
            ['estado' => $data['estado']]
        );

        return back()->with('success', 'Estado actualizado.');
    }

    public function cancelar(SolicitudEstudio $solicitud): RedirectResponse
    {
        $this->autorizarAcceso($solicitud);

        if ($solicitud->estado === 'cancelado') {
            return back()->with('error', 'Esta solicitud ya está cancelada.');
        }

        // Revertir cargo de la cuenta si existe y está abierta
        if ($solicitud->cuenta_item_id && $solicitud->cuenta && $solicitud->cuenta->estado === 'abierta') {
            $item = $solicitud->cuentaItem;
            if ($item) {
                $snapshot = [
                    'item_id'  => $item->id,
                    'concepto' => $item->concepto,
                    'importe'  => (float) $item->importe,
                ];
                $item->delete();

                AuditoriaService::registrar(
                    AuditEvent::CUENTA_CARGO_ELIMINADO,
                    'cuentas',
                    "Cargo revertido por cancelación de estudio {$solicitud->folio}",
                    $solicitud->cuenta,
                    $snapshot,
                    []
                );
            }
        }

        $solicitud->update([
            'estado'         => 'cancelado',
            'cuenta_item_id' => null,
        ]);

        AuditoriaService::registrar(
            AuditEvent::DELETED,
            'estudios',
            "Solicitud de estudio cancelada: {$solicitud->folio}",
            $solicitud
        );

        return back()->with('success', 'Solicitud cancelada y cargo revertido.');
    }

    /* =========================================================
     |  SEGURIDAD
     ========================================================= */
    private function autorizarAcceso(SolicitudEstudio $solicitud): void
    {
        $user = auth()->user();

        if (! $user) abort(401, 'No autenticado.');

        if ($this->esMedico($user) && !$user->hasRole('administrador')) {
            if ($solicitud->medico_solicita_id !== $user->id) {
                abort(403, 'No tienes permiso para ver esta solicitud.');
            }
        }
    }

    private function esMedico($user): bool
    {
        return $user->roles->contains(function ($rol) {
            $n = strtolower($rol->name);
            return str_contains($n, 'medic') || str_contains($n, 'doctor') || str_contains($n, 'médic');
        });
    }
}