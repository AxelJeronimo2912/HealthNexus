<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteDescargaController extends Controller
{
    public function __invoke(Request $request, string $archivo): StreamedResponse
    {
        // 1. Verificamos que el archivo sea un PDF (seguridad básica)
        abort_unless(str_ends_with(strtolower($archivo), '.pdf'), 404);

        // 2. Construimos la ruta segura
        $ruta = 'reportes/' . basename($archivo);

        // 3. Verificamos que exista
        abort_unless(Storage::disk('local')->exists($ruta), 404, 'El archivo no existe o ha expirado.');

        // 4. Lo devolvemos para descarga
        return Storage::disk('local')->response($ruta, $archivo, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-store, private',
        ]);
    }
}