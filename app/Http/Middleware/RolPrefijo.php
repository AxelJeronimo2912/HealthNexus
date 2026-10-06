<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolPrefijo
{
    /**
     * Uso: ->middleware('rol.prefijo:medic,enferm,farmac,admin')
     *
     * Acepta el acceso si el rol del usuario EMPIEZA con alguno de los prefijos.
     * Ej: 'medic' acepta 'medico', 'medico a', 'medicina', 'm' (caso especial).
     */
    public function handle(Request $request, Closure $next, string ...$prefijos): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'No autenticado.');
        }

        $rolActual = strtolower(trim($user->getRoleNames()->first() ?? ''));

        foreach ($prefijos as $prefijo) {
            $prefijo = strtolower(trim($prefijo));

            // Caso especial: "m" solo coincide exacto (médico corto)
            if ($prefijo === 'm' && $rolActual === 'm') {
                return $next($request);
            }

            if (str_starts_with($rolActual, $prefijo)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes el rol adecuado para acceder a esta sección.');
    }
}