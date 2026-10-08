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

        $dispositivo = Dispositivo::where('user_id', $userId)
            ->where('huella', $huella)
            ->first();

        if ($dispositivo) {
            // Actualizar último acceso
            $dispositivo->update([
                'ip_ultimo_acceso' => $request->ip(),
                'ultimo_acceso'    => now(),
                'total_accesos'    => $dispositivo->total_accesos + 1,
                'user_agent'       => substr($request->userAgent() ?? '', 0, 500),
                'tipo'              => self::detectarTipo($request),
                'sistema_operativo' => self::detectarSO($request),
                'navegador'         => self::detectarNavegador($request),
                'nombre'            => self::detectarNombre($request),
            ]);

            return $dispositivo;
        }

        // Nuevo dispositivo
        return Dispositivo::create([
            'user_id'           => $userId,
            'huella'            => $huella,
            'nombre'            => self::detectarNombre($request),
            'tipo'              => self::detectarTipo($request),
            'sistema_operativo' => self::detectarSO($request),
            'navegador'         => self::detectarNavegador($request),
            'user_agent'        => substr($request->userAgent() ?? '', 0, 500),
            'ip_registro'       => $request->ip(),
            'ip_ultimo_acceso'  => $request->ip(),
            'confiable'         => false,
            'activo'            => true,
            'ultimo_acceso'     => now(),
            'total_accesos'     => 1,
        ]);
    }

    /**
     * Genera una huella ESTABLE.
     *
     * - Si el cliente manda X-Device-Fingerprint (React Native, web con fingerprintjs), se usa.
     * - Si no, se usa SOLO el User-Agent (estable por dispositivo/app).
     *
 
     */
    public static function generarHuella(Request $request): string
    {
        // 1) Fingerprint enviado por el cliente (ideal)
        $clientFp = $request->header('X-Device-Fingerprint');
        if (!empty($clientFp)) {
            return hash('sha256', $clientFp);
        }

        // 2) Fallback: solo User-Agent (estable en React Native)
        $ua = $request->userAgent() ?? 'unknown';

        return hash('sha256', $ua);
    }

    /**
     * Detecta el tipo de dispositivo.
     * Reconoce navegadores móviles, tablets y apps React Native.
     */
    public static function detectarTipo(Request $request): string
    {
        $ua = strtolower($request->userAgent() ?? '');

        
        if (preg_match('/okhttp|cfnetwork|darwin|react-native|dart|flutter/', $ua)) {
            if (preg_match('/ipad|tablet/', $ua)) {
                return 'tablet';
            }
            return 'mobile';
        }

        if (preg_match('/tablet|ipad/', $ua)) return 'tablet';
        if (preg_match('/mobile|android|iphone|ipod/', $ua)) return 'mobile';

        return 'desktop';
    }

    /**
     * Detecta el sistema operativo.
     * Incluye detección para React Native (okhttp = Android, CFNetwork/Darwin = iOS).
     */
    public static function detectarSO(Request $request): string
    {
        $ua = $request->userAgent() ?? '';

        if (stripos($ua, 'okhttp') !== false) {
            return 'Android';
        }
        if (stripos($ua, 'CFNetwork') !== false || stripos($ua, 'Darwin') !== false) {
            return 'iOS';
        }

        return match (true) {
            stripos($ua, 'Windows NT 10') !== false => 'Windows 10/11',
            stripos($ua, 'Windows NT') !== false    => 'Windows',
            stripos($ua, 'Mac OS X') !== false      => 'macOS',
            stripos($ua, 'Android') !== false       => 'Android',
            stripos($ua, 'iPhone') !== false,
            stripos($ua, 'iPad') !== false          => 'iOS',
            stripos($ua, 'Linux') !== false         => 'Linux',
            default                                 => 'Desconocido',
        };
    }

    /**
     * Detecta el navegador o cliente HTTP.
     * Reconoce React Native (okhttp en Android, CFNetwork en iOS).
     */
    public static function detectarNavegador(Request $request): string
    {
        $ua = $request->userAgent() ?? '';

        // 📱 React Native
        if (stripos($ua, 'okhttp') !== false) {
            return 'React Native (Android)';
        }
        if (stripos($ua, 'CFNetwork') !== false || stripos($ua, 'Darwin') !== false) {
            return 'React Native (iOS)';
        }
        if (stripos($ua, 'react-native') !== false) {
            return 'React Native';
        }

        return match (true) {
            stripos($ua, 'Edg/') !== false                                     => 'Microsoft Edge',
            stripos($ua, 'Chrome/') !== false && stripos($ua, 'Edg') === false => 'Google Chrome',
            stripos($ua, 'Firefox/') !== false                                 => 'Mozilla Firefox',
            stripos($ua, 'Safari/') !== false && stripos($ua, 'Chrome') === false => 'Safari',
            stripos($ua, 'Opera') !== false,
            stripos($ua, 'OPR/') !== false                                     => 'Opera',
            default                                                            => 'Desconocido',
        };
    }

    /**
     * Genera un nombre legible: "Navegador en SO".
     */
    public static function detectarNombre(Request $request): string
    {
        $so  = self::detectarSO($request);
        $nav = self::detectarNavegador($request);

        // Si no se pudo detectar nada útil, dar un nombre genérico
        if ($nav === 'Desconocido' && $so === 'Desconocido') {
            return 'Dispositivo desconocido';
        }

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