<?php

namespace App\Support;

use Illuminate\Support\Str;

class RoleHelper
{
    /**
     * Normaliza un nombre de rol: minúsculas, sin acentos, sin espacios extras.
     */
    public static function normalizar(?string $rol): string
    {
        if (!$rol) return '';
        return strtolower(Str::ascii(trim($rol)));
    }

    /**
     * Verifica si el usuario tiene un rol, ignorando mayúsculas y acentos.
     * También acepta alias cortos (m, e, f, a).
     */
    public static function tieneRol($user, string $rolBuscado): bool
    {
        $buscado = self::normalizar($rolBuscado);

        foreach ($user->getRoleNames() as $rolUsuario) {
            if (self::normalizar($rolUsuario) === $buscado) {
                return true;
            }
        }

        $alias = [
            'm' => 'medico',
            'e' => 'enfermeria',
            'f' => 'farmacia',
            'a' => 'administrador',
        ];

        if (isset($alias[$buscado])) {
            return self::tieneRol($user, $alias[$buscado]);
        }

        return false;
    }

    /**
     * Verifica si el usuario es médico con cualquier variante:
     * medico, médico, MEDICO, medico a, medicina general, m, etc.
     */
    public static function esMedico($user): bool
    {
        foreach ($user->getRoleNames() as $rol) {
            $normalizado = self::normalizar($rol);

            // Coincidencia exacta o alias "m"
            if ($normalizado === 'medico' || $normalizado === 'm') {
                return true;
            }

            // Cualquier rol que empiece con "medic" → medico, médico, medicina, medico a...
            if (Str::startsWith($normalizado, 'medic')) {
                return true;
            }
        }

        return false;
    }
}