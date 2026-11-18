<?php

namespace App\Http\Controllers;

use App\Services\PrediccionCostosService;

class PrediccionCostosController extends Controller
{
    public function index(PrediccionCostosService $predictor)
    {
        // Datos (luego los puedes cambiar por datos reales de tu BD)
        $valores = [15000, 18000, 16500, 22000, 21000, 25000];
        $etiquetas = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];

        // Cálculos
        $prediccionLineal = $predictor->regresionLineal($valores, 3);
        $prediccionSuavizada = $predictor->suavizadoExponencial($valores, 0.5, 3);

        return [
            'valores' => $valores,
            'etiquetas' => $etiquetas,
            'prediccionLineal' => $prediccionLineal,
            'prediccionSuavizada' => $prediccionSuavizada,
        ];
    }
}