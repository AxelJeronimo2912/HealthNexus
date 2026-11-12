<?php

namespace App\Http\Controllers;

use App\Models\ArchivoEstudio;
use App\Models\SolicitudEstudio;
use App\Services\EncriptacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Enums\AuditEvent;
use App\Services\AuditoriaService;

class ArchivoEstudioController extends Controller
{
    public function store(Request $request, SolicitudEstudio $solicitud): RedirectResponse
    {
        $this->autorizarAcceso($solicitud);

        $request->validate([
            'archivos'    => ['required', 'array', 'min:1'],
            'archivos.*'  => ['file', 'max:51200', 'mimes:pdf,jpg,jpeg,png,webp,dicom,dcm'], // 50MB máx
            'descripcion' => ['nullable', 'string', 'max:200'],
        ], [
            'archivos.*.max'   => 'Cada archivo no puede superar los 50 MB.',
            'archivos.*.mimes' => 'Solo se permiten PDF, JPG, PNG, WEBP o DICOM.',
        ]);

        foreach ($request->file('archivos') as $file) {
            $mime = $file->getMimeType();
            $ext  = strtolower($file->getClientOriginalExtension());

            $tipo = match (true) {
                $mime === 'application/pdf'      => 'pdf',
                str_starts_with($mime, 'image/') => 'imagen',
                in_array($ext, ['dcm', 'dicom']) => 'otro',
                default                          => 'otro',
            };

            $carpeta = "estudios/{$solicitud->id}";
            $nombre  = Str::uuid() . '.' . $ext;
            
            $ruta = $file->storeAs($carpeta, $nombre, 'estudios_privados');

            $rutaCompleta = Storage::disk('estudios_privados')->path($ruta);
            EncriptacionService::encriptarArchivo($rutaCompleta);

            ArchivoEstudio::create([
                'solicitud_estudio_id' => $solicitud->id,
                'user_id'              => auth()->id(),
                'nombre_original'      => $file->getClientOriginalName(),
                'ruta'                 => $ruta,
                'mime_type'            => $mime,
                'tamano_bytes'         => $file->getSize(),
                'tipo'                 => $tipo,
                'descripcion'          => $request->input('descripcion'),
            ]);
        }

        if ($solicitud->estado === 'en_proceso' || $solicitud->estado === 'solicitado') {
            $solicitud->update(['estado' => 'resultado_listo', 'resultado_en' => now()]);
        }

        AuditoriaService::registrar(
            AuditEvent::UPDATED,
            'estudios',
            "Archivos cargados y encriptados en solicitud {$solicitud->folio}",
            $solicitud,
            [],
            [],
            ['cantidad_archivos' => count($request->file('archivos'))]
        );

        return back()->with('success', 'Archivos cargados y encriptados correctamente.');
    }

    /**
     * Sirve el archivo desencriptado en el navegador.
     */
    public function ver(ArchivoEstudio $archivo)
    {
        $this->autorizarAcceso($archivo->solicitud);

        $rutaCompleta = Storage::disk('estudios_privados')->path($archivo->ruta);

        if (! file_exists($rutaCompleta)) {
            abort(404, 'Archivo no encontrado en el servidor.');
        }
        $ext = pathinfo($archivo->nombre_original, PATHINFO_EXTENSION) ?: 'bin';
        $rutaTemp = EncriptacionService::desencriptarATemporal($rutaCompleta, $ext);

        if (! $rutaTemp) {
            abort(500, 'Error de integridad: No se pudo desencriptar el archivo.');
        }

        return response()->file($rutaTemp, [
            'Content-Type'        => $archivo->mime_type,
            'Content-Disposition' => 'inline; filename="' . $archivo->nombre_original . '"',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Descarga el archivo desencriptado.
     */
    public function descargar(ArchivoEstudio $archivo)
    {
        $this->autorizarAcceso($archivo->solicitud);

        $rutaCompleta = Storage::disk('estudios_privados')->path($archivo->ruta);

        if (! file_exists($rutaCompleta)) {
            abort(404, 'Archivo no encontrado en el servidor.');
        }

        $ext = pathinfo($archivo->nombre_original, PATHINFO_EXTENSION) ?: 'bin';
        $rutaTemp = EncriptacionService::desencriptarATemporal($rutaCompleta, $ext);

        if (! $rutaTemp) {
            abort(500, 'Error de integridad: No se pudo desencriptar el archivo.');
        }

        return response()->download(
            $rutaTemp,
            $archivo->nombre_original,
            ['Content-Type' => $archivo->mime_type]
        )->deleteFileAfterSend(true);
    }

    public function destroy(ArchivoEstudio $archivo): RedirectResponse
    {
        $solicitud = $archivo->solicitud;

        $this->autorizarAcceso($solicitud);

        Storage::disk('estudios_privados')->delete($archivo->ruta);
        $archivo->delete();

        AuditoriaService::registrar(
            AuditEvent::DELETED,
            'estudios',
            "Archivo eliminado de solicitud {$solicitud->folio}: {$archivo->nombre_original}",
            $solicitud
        );

        return back()->with('success', 'Archivo eliminado correctamente.');
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
                abort(403, 'No tienes permiso para ver o modificar los archivos de este estudio.');
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