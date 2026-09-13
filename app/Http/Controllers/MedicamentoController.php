<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\InventarioService;

class MedicamentoController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->input('buscar');

        $medicamentos = Medicamento::query()
            ->when($busqueda, function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('sustancia_activa', 'like', "%{$busqueda}%")
                  ->orWhere('codigo_barras', 'like', "%{$busqueda}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('medicamentos.index', compact('medicamentos', 'busqueda'));
    }

    public function entrada(Request $request, Medicamento $medicamento): RedirectResponse
{
    $request->validate([
        'cantidad' => ['required', 'integer', 'min:1'],
        'motivo' => ['nullable', 'string', 'max:255'],
    ]);

    InventarioService::entrada(
        $medicamento,
        (int) $request->cantidad,
        $request->motivo ?? 'Entrada manual'
    );

    return back()->with('success', "Se agregaron {$request->cantidad} unidades al stock.");
}
    public function create(): View
    {
        return view('medicamentos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validar($request);

        $data['psicotropico'] = $request->boolean('psicotropico');
        $data['antibiotico'] = $request->boolean('antibiotico');
        $data['controlado'] = $request->boolean('controlado');
        $data['activo'] = $request->boolean('activo');

        Medicamento::create($data);

        return redirect()->route('medicamentos.index')
            ->with('success', 'Medicamento registrado correctamente.');
    }

    public function show(Medicamento $medicamento): View
    {
        return view('medicamentos.show', compact('medicamento'));
    }

    public function edit(Medicamento $medicamento): View
    {
        return view('medicamentos.edit', compact('medicamento'));
    }

    public function update(Request $request, Medicamento $medicamento): RedirectResponse
    {
        $data = $this->validar($request, $medicamento->id);

        $data['psicotropico'] = $request->boolean('psicotropico');
        $data['antibiotico'] = $request->boolean('antibiotico');
        $data['controlado'] = $request->boolean('controlado');
        $data['activo'] = $request->boolean('activo');

        $medicamento->update($data);

        return redirect()->route('medicamentos.index')
            ->with('success', 'Medicamento actualizado correctamente.');
    }

    public function destroy(Medicamento $medicamento): RedirectResponse
    {
        $medicamento->delete();

        return redirect()->route('medicamentos.index')
            ->with('success', 'Medicamento eliminado correctamente.');
    }

    /**
     * Reglas de validación reutilizables
     */
    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'sustancia_activa' => ['nullable', 'string', 'max:150'],
            'presentacion' => ['nullable', 'string', 'max:100'],
            'concentracion' => ['nullable', 'string', 'max:100'],
            'via_administracion' => ['nullable', 'string', 'max:50'],
            'laboratorio' => ['nullable', 'string', 'max:150'],
            'codigo_barras' => ['nullable', 'string', 'max:100', 'unique:medicamentos,codigo_barras,' . $id],
            'registro_sanitario' => ['nullable', 'string', 'max:100'],
            'grupo_terapeutico' => ['nullable', 'string', 'max:100'],
            'unidad_medida' => ['required', 'string', 'max:50'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'stock_maximo' => ['required', 'integer', 'min:0'],
            'precio_compra' => ['nullable', 'numeric', 'min:0'],
            'precio_venta' => ['nullable', 'numeric', 'min:0'],
        ], [
            'nombre.required' => 'El nombre del medicamento es obligatorio.',
            'codigo_barras.unique' => 'Este código de barras ya está registrado.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stock_maximo.required' => 'El stock máximo es obligatorio.',
        ]);
    }
}