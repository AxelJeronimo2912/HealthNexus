<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\InventarioService;

class MedicamentoController extends Controller
{
        public function index(Request $request)
    {
        $busqueda = $request->input('search'); 

        $medicamentos = Medicamento::query()
            ->when($busqueda, function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('sustancia_activa', 'like', "%{$busqueda}%")
                  ->orWhere('codigo_barras', 'like', "%{$busqueda}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('medicamentos.partials._tabla', compact('medicamentos'))->render();
        }

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
            'sustancia_activa' => ['required', 'string', 'max:150'],
            'presentacion' => ['required', 'string', 'max:100'],
            'concentracion' => ['required', 'string', 'max:100'],
            'via_administracion' => ['nullable', 'string', 'max:50'],
            'laboratorio' => ['nullable', 'string', 'max:150'],
            
            'codigo_barras' => ['nullable', 'string', 'max:100', 'unique:medicamentos,codigo_barras,' . $id],
            'registro_sanitario' => ['nullable', 'string', 'max:100'],
            'grupo_terapeutico' => ['nullable', 'string', 'max:100'],
            'unidad_medida' => ['required', 'string', 'max:50'],
            
           
            'stock_minimo' => ['required', 'integer', 'min:1', 'max:99999'], 
            'stock_maximo' => ['required', 'integer', 'min:1', 'gte:stock_minimo', 'max:99999'], 
            
            
            'precio_compra' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            
            'precio_venta' => ['required', 'numeric', 'min:0.01', 'gte:precio_compra', 'max:999999.99'],
            
            'psicotropico' => ['nullable', 'boolean'],
            'antibiotico' => ['nullable', 'boolean'],
            'controlado' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ], [
            'nombre.required' => 'El nombre comercial es obligatorio.',
            'sustancia_activa.required' => 'La sustancia activa es obligatoria (Regla de Farmacia).',
            'presentacion.required' => 'La presentación es obligatoria (Regla de Farmacia).',
            'concentracion.required' => 'La concentración es obligatoria (Regla de Farmacia).',
            
            'codigo_barras.unique' => 'Este código de barras ya está registrado con otro medicamento.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero.',
            'stock_minimo.min' => 'El stock mínimo debe ser al menos 1.',
            'stock_minimo.max' => 'El stock mínimo no puede superar las 99,999 unidades.',
            'stock_maximo.required' => 'El stock máximo es obligatorio.',
            'stock_maximo.integer' => 'El stock máximo debe ser un número entero.',
            'stock_maximo.gte' => 'El stock máximo no puede ser menor al stock mínimo.',
            'stock_maximo.max' => 'El stock máximo no puede superar las 99,999 unidades.',
            
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_compra.numeric' => 'El precio de compra debe ser un número válido.',
            'precio_compra.min' => 'El precio de compra debe ser mayor a $0.00.',
            
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número válido.',
            'precio_venta.min' => 'El precio de venta debe ser mayor a $0.00.',
            'precio_venta.gte' => 'El precio de venta NO puede ser menor al precio de compra. ¡Estarías perdiendo dinero!',
        ]);
    }
}