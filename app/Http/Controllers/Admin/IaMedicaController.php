<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\IaMedicaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IaMedicaController extends Controller
{
    public function __construct(private IaMedicaService $ia) {}

    /**
     * Métricas generales del modelo (accuracy, matriz, feature importance).
     */
    public function data(): JsonResponse
    {
        return response()->json([
            'metricas'      => $this->ia->metricasModelo(),
            'feature_importance' => $this->ia->featureImportance(),
            'accuracy_por_modelo' => $this->ia->accuracyPorModelo(),
        ]);
    }

    /**
     * Recibe 4 signos vitales y devuelve 5 predicciones.
     */
    public function predecir(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fc'   => 'required|integer|min:30|max:250',
            'spo2' => 'required|integer|min:50|max:100',
            'temp' => 'required|numeric|min:34|max:43',
            'edad' => 'required|integer|min:0|max:120',
        ]);

        $predicciones = $this->ia->predecir(
            (int) $data['fc'],
            (int) $data['spo2'],
            (float) $data['temp'],
            (int) $data['edad']
        );

        // Guardar caso si el usuario está autenticado
        if (auth()->check()) {
            $caso = $this->ia->guardarCaso($data, $predicciones, auth()->id());
            $predicciones['caso_id'] = $caso->id;
        }

        return response()->json($predicciones);
    }
}