<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')
            ->orderByDesc('created_at');

        // Filtros
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('evento')) {
            $query->where('evento', $request->evento);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('severidad')) {
            $query->where('severidad', $request->severidad);
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('descripcion', 'like', "%{$buscar}%")
                  ->orWhere('user_nombre', 'like', "%{$buscar}%");
            });
        }

        $logs = $query->paginate(30)->withQueryString();

        // Estadísticas
        $stats = [
            'total_hoy' => AuditLog::whereDate('created_at', today())->count(),
            'criticos_hoy' => AuditLog::where('severidad', 'critical')->whereDate('created_at', today())->count(),
            'accesos_denegados' => AuditLog::where('evento', AuditEvent::ACCESO_DENEGADO->value)
                ->whereDate('created_at', today())->count(),
            'total_semana' => AuditLog::where('created_at', '>=', now()->subWeek())->count(),
        ];

        // Opciones para filtros
        $modulos = AuditLog::distinct()->pluck('modulo')->sort()->values();
        $usuarios = User::orderBy('nombre')->get(['id', 'nombre', 'apellido_paterno']);
        $eventos = AuditEvent::cases();

        return view('auditoria.index', compact('logs', 'stats', 'modulos', 'usuarios', 'eventos'));
    }

    public function show(AuditLog $log): View
    {
        $log->load('user');
        return view('auditoria.show', compact('log'));
    }
}