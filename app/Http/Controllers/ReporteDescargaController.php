<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Descarga de los reportes PDF que genera el asistente (ReporteService).
 * Requiere sesión y un enlace firmado vigente emitido para el mismo usuario.
 */
class ReporteDescargaController extends Controller
{
    public function __invoke(Request $request, string $archivo): StreamedResponse
    {
        if ((int) $request->query('usuario') !== (int) $request->user()->id) {
            AuditoriaService::registrar(AuditEvent::ACCESO_DENEGADO, 'reportes', "Intento de abrir el reporte {$archivo} generado por otro usuario");
            abort(403);
        }

        $ruta = 'reportes/'.basename($archivo);
        abort_unless(Storage::disk('local')->exists($ruta), 404);

        return Storage::disk('local')->response($ruta, $archivo, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
