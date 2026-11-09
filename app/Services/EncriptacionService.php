<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class EncriptacionService
{
    const CHUNK_SIZE = 1024 * 1024; // 1 MB por bloque

    /**
     * Encripta un archivo en bloques y lo guarda en su lugar.
     * Formato: [4 bytes longitud][bloque encriptado][4 bytes longitud][bloque encriptado]...
     */
    public static function encriptarArchivo(string $rutaCompleta): bool
    {
        if (! file_exists($rutaCompleta)) {
            return false;
        }

        $rutaTemporal = $rutaCompleta . '.enc.tmp';
        $in  = fopen($rutaCompleta, 'rb');
        $out = fopen($rutaTemporal, 'wb');

        if (! $in || ! $out) {
            return false;
        }

        while (! feof($in)) {
            $bloque = fread($in, self::CHUNK_SIZE);
            if ($bloque === false || $bloque === '') {
                break;
            }

            $bloqueEncriptado = Crypt::encryptString($bloque);
            // Escribir longitud (4 bytes) + bloque
            fwrite($out, pack('N', strlen($bloqueEncriptado)));
            fwrite($out, $bloqueEncriptado);
        }

        fclose($in);
        fclose($out);

        unlink($rutaCompleta);
        rename($rutaTemporal, $rutaCompleta);

        return true;
    }

    /**
     * Desencripta un archivo encriptado por bloques y devuelve el contenido completo.
     * Para archivos grandes, usa desencriptarAStream().
     */
    public static function desencriptarArchivo(string $rutaCompleta): ?string
    {
        if (! file_exists($rutaCompleta)) {
            return null;
        }

        $in = fopen($rutaCompleta, 'rb');
        if (! $in) {
            return null;
        }

        $resultado = '';

        while (! feof($in)) {
            $lenBytes = fread($in, 4);
            if (strlen($lenBytes) < 4) {
                break;
            }
            $len = unpack('N', $lenBytes)[1];
            $bloqueEncriptado = fread($in, $len);

            if (strlen($bloqueEncriptado) < $len) {
                break;
            }

            try {
                $resultado .= Crypt::decryptString($bloqueEncriptado);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                report($e);
                fclose($in);
                return null;
            }
        }

        fclose($in);
        return $resultado;
    }

    /**
     * Desencripta y guarda temporalmente para poder servirlo con response()->file().
     * Devuelve la ruta temporal (el caller debe eliminarla después).
     */
    public static function desencriptarATemporal(string $rutaCompleta, string $extension): string
    {
        $contenido = self::desencriptarArchivo($rutaCompleta);
        
        if ($contenido === null) {
            abort(500, 'No se pudo desencriptar el archivo.');
        }

        $rutaTemp = storage_path('app/temp/' . uniqid('dec_') . '.' . $extension);
        
        if (! is_dir(dirname($rutaTemp))) {
            mkdir(dirname($rutaTemp), 0755, true);
        }

        file_put_contents($rutaTemp, $contenido);

        return $rutaTemp;
    }
}