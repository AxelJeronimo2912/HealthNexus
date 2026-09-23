<?php

namespace App\Services;

use App\Models\Dispositivo;
use Illuminate\Http\Request;

class DispositivoService
{
    /**
     * Detecta si el request viene de un dispositivo ya conocido.
     * Si no, lo registra. Devuelve el dispositivo.
     */
    public static function registrarDesdeRequest(Request $request, int $userId): Dispositivo
    {
        $huella = self::generarHuella($request);

        $dispositivo = Dispositivo::where('huella', $huella)->first();

        if ($dispositivo) {
            // Actualizar último acceso
            $dispositivo->update([
                'ip_ultimo_acceso' => $request->ip(),
                'ultimo_acceso' => now(),
                'total_accesos' => $dispositivo->total_accesos + 1,
            ]);

            return $dispositivo;
        }

        // Nuevo dispositivo
        return Dispositivo::create([
            'user_id' => $userId,
            'huella' => $huella,
            'nombre' => self::detectarNombre($request),
            'tipo' => self::detectarTipo($request),
            'sistema_operativo' => self::detectarSO($request),
            'navegador' => self::detectarNavegador($request),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
            'ip_registro' => $request->ip(),
            'ip_ultimo_acceso' => $request->ip(),
            'confiable' => false,
            'activo' => true,
            'ultimo_acceso' => now(),
            'total_accesos' => 1,
        ]);
    }

    /**
     * Genera una huella única combinando user agent + IP + hash.
     * Nota: la IP cambia (móvil, wifi), así que la huella se basa
     * principalmente en user agent + un hash del navegador si existe.
     * Para producción sería ideal usar fingerprintjs.
     */
    public static function generarHuella(Request $request): string
    {
        $ua = $request->userAgent() ?? 'unknown';
        $accept = $request->header('Accept-Language') ?? '';
        $encoding = $request->header('Accept-Encoding') ?? '';

        return hash('sha256', $ua . '|' . $accept . '|' . $encoding);
    }

    public static function detectarTipo(Request $request): string
    {
        $ua = strtolower($request->userAgent() ?? '');

        if (preg_match('/mobile|android|iphone|ipod/', $ua)) return 'mobile';
        if (preg_match('/tablet|ipad/', $ua)) return 'tablet';
        return 'desktop';
    }

    public static function detectarSO(Request $request): string
    {
        $ua = $request->userAgent() ?? '';

        return match (true) {
            stripos($ua, 'Windows NT 10') !== false => 'Windows 10/11',
            stripos($ua, 'Windows NT') !== false => 'Windows',
            stripos($ua, 'Mac OS X') !== false => 'macOS',
            stripos($ua, 'Android') !== false => 'Android',
            stripos($ua, 'iPhone') !== false, stripos($ua, 'iPad') !== false => 'iOS',
            stripos($ua, 'Linux') !== false => 'Linux',
            default => 'Desconocido',
        };
    }

    public static function detectarNavegador(Request $request): string
    {
        $ua = $request->userAgent() ?? '';

        return match (true) {
            stripos($ua, 'Edg/') !== false => 'Microsoft Edge',
            stripos($ua, 'Chrome/') !== false && stripos($ua, 'Edg') === false => 'Google Chrome',
            stripos($ua, 'Firefox/') !== false => 'Mozilla Firefox',
            stripos($ua, 'Safari/') !== false && stripos($ua, 'Chrome') === false => 'Safari',
            stripos($ua, 'Opera') !== false, stripos($ua, 'OPR/') !== false => 'Opera',
            default => 'Desconocido',
        };
    }

    public static function detectarNombre(Request $request): string
    {
        $so = self::detectarSO($request);
        $nav = self::detectarNavegador($request);
        return "{$nav} en {$so}";
    }

    /**
     * Genera un nombre legible con el nombre del usuario.
     */
    public static function nombreAmigable(Request $request, string $nombreUsuario): string
    {
        $so = self::detectarSO($request);
        return "{$nombreUsuario} ({$so})";
    }
}