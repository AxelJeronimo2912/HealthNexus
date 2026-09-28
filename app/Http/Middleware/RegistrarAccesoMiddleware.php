<?php

namespace App\Http\Middleware;

use App\Enums\AuditEvent;
use App\Services\AuditoriaService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrarAccesoMiddleware
{
    /**
     * Rutas que se consideran sensibles y se auditan.
     */
    private const RUTAS_SENSIBLES = [
        'pacientes.show',
        'expedientes.show',
        'consultas.show',
        'consultas.pdf',
        'consultas.receta.pdf',
        'admisiones.show',
        'admisiones.pase-salida',
        'prediccion.show',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Auditar accesos denegados
        if ($response->getStatusCode() === 403 || $response->getStatusCode() === 401) {
            AuditoriaService::registrar(
                AuditEvent::ACCESO_DENEGADO,
                'seguridad',
                "Acceso denegado a {$request->path()}",
                null,
                [],
                [],
                ['status_code' => $response->getStatusCode()]
            );
        }

        // Auditar consultas sensibles
        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, self::RUTAS_SENSIBLES)) {
            AuditoriaService::registrar(
                AuditEvent::CONSULTA_SENSIBLE,
                'expedientes',
                "Consultó: {$routeName}",
                null,
                [],
                [],
                ['route' => $routeName, 'params' => $request->route()->parameters()]
            );
        }

        return $response;
    }
}