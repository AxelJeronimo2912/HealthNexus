<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Construye la navegación visible para el usuario actual a partir de
 * config/navegacion.php. La usan el sidebar, el breadcrumb y la paleta
 * de comandos, así los tres se mantienen sincronizados.
 */
class Navegacion
{
    /** Ruta del dashboard según el rol (misma lógica que tenía el sidebar). */
    public static function rutaDashboard($user): string
    {
        $rol = strtolower(trim($user?->getRoleNames()->first() ?? ''));

        return match (true) {
            str_starts_with($rol, 'admin') => route('admin.dashboard'),
            str_starts_with($rol, 'medic') || $rol === 'm' => route('medico.dashboard'),
            str_starts_with($rol, 'enferm') => route('enfermeria.dashboard'),
            str_starts_with($rol, 'farmac') => route('farmacia.dashboard'),
            default => route('dashboard'),
        };
    }

    public static function dashboardActivo(): bool
    {
        return request()->routeIs('admin.dashboard', 'medico.dashboard', 'enfermeria.dashboard', 'farmacia.dashboard', 'dashboard');
    }

    /**
     * Secciones con sus items visibles para el usuario.
     *
     * @return array<int, array{titulo: string, items: array<int, array>}>
     */
    public static function secciones($user): array
    {
        $secciones = [];

        foreach (config('navegacion', []) as $seccion) {
            $items = [];

            foreach ($seccion['items'] as $item) {
                if ($item['permiso'] && ! $user?->can($item['permiso'])) {
                    continue;
                }
                if (! Route::has($item['ruta'])) {
                    continue;
                }

                $item['url'] = route($item['ruta']);
                $item['es_activo'] = request()->routeIs(...(array) $item['activo']);
                $item['url_contador'] = isset($item['contador']) && Route::has($item['contador'])
                    ? route($item['contador'])
                    : null;

                $items[] = $item;
            }

            if ($items) {
                $secciones[] = ['titulo' => $seccion['titulo'], 'items' => $items];
            }
        }

        return $secciones;
    }

    /** Lista plana para la paleta de comandos. */
    public static function paraPaleta($user): array
    {
        $lista = [[
            'label' => 'Dashboard',
            'section' => 'Panel',
            'url' => self::rutaDashboard($user),
            'icon' => 'home',
        ]];

        foreach (self::secciones($user) as $seccion) {
            foreach ($seccion['items'] as $item) {
                $lista[] = [
                    'label' => $item['label'],
                    'section' => $seccion['titulo'],
                    'url' => $item['url'],
                    'icon' => $item['icono'],
                ];
            }
        }

        if (Route::has('profile.edit')) {
            $lista[] = ['label' => 'Mi perfil', 'section' => 'Cuenta', 'url' => route('profile.edit'), 'icon' => 'user-circle'];
        }

        return $lista;
    }

    /**
     * Migas de pan: [['label' => 'Farmacia e inventario'], ['label' => 'Medicamentos', 'url' => ...], ['label' => 'Editar']]
     */
    public static function breadcrumb($user): array
    {
        if (self::dashboardActivo()) {
            return [['label' => 'Dashboard']];
        }

        $nombre = (string) request()->route()?->getName();

        foreach (self::secciones($user) as $seccion) {
            foreach ($seccion['items'] as $item) {
                if (! $item['es_activo']) {
                    continue;
                }

                $migas = [
                    ['label' => $seccion['titulo']],
                    ['label' => $item['label'], 'url' => $item['url']],
                ];

                $accion = match (true) {
                    str_ends_with($nombre, '.create') => 'Nuevo registro',
                    str_ends_with($nombre, '.edit') => 'Editar',
                    str_ends_with($nombre, '.show') => 'Detalle',
                    default => null,
                };

                if ($accion) {
                    $migas[] = ['label' => $accion];
                } else {
                    unset($migas[1]['url']);
                }

                return $migas;
            }
        }

        if (str_starts_with($nombre, 'profile.')) {
            return [['label' => 'Cuenta'], ['label' => 'Mi perfil']];
        }

        return [];
    }

    public static function iniciales(?string $nombre): string
    {
        $partes = preg_split('/\s+/', trim((string) $nombre)) ?: [];
        $ini = mb_substr($partes[0] ?? '', 0, 1) . mb_substr($partes[1] ?? '', 0, 1);

        return mb_strtoupper($ini ?: 'HN');
    }
}
