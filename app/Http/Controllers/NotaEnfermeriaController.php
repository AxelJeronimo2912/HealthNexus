<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Seguimiento;
use App\Models\CamaPaciente;
use App\Services\InventarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotaEnfermeriaController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');

        $pacienteId = $request->input('paciente_id');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $buscar = $request->input('buscar');

        $query = Seguimiento::with(['paciente', 'user', 'cama'])
            ->where('tipo', 'nota_enfermeria')
            ->orderByDesc('created_at');

        if ($pacienteId) {
            $query->where('paciente_id', $pacienteId);
        }
        if ($desde) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('created_at', '<=', $hasta);
        }
        if ($buscar) {
            $query->whereHas('paciente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                  ->orWhere('apellido_materno', 'like', "%{$buscar}%");
            });
        }

        if (!$esAdmin) {
            $query->where('user_id', $user->id);
        }

        $notas = $query->paginate(20)->withQueryString();

        $pacientes = Paciente::where(function($q) {
            $q->whereHas('camas', function($subQ) {
                $subQ->where('activa', true);
            })->orWhereHas('signosVitales', function($subQ) {
                $subQ->whereIn('triage', ['rojo', 'naranja', 'amarillo'])
                     ->where('created_at', '>=', now()->subHours(24));
            });
        })->orderBy('apellido_paterno')->get();

        $stats = [
            'total' => Seguimiento::where('tipo', 'nota_enfermeria')->count(),
            'hoy' => Seguimiento::where('tipo', 'nota_enfermeria')->whereDate('created_at', today())->count(),
            'esta_semana' => Seguimiento::where('tipo', 'nota_enfermeria')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        return view('enfermeria.notas.index', compact('notas', 'pacientes', 'pacienteId', 'desde', 'hasta', 'buscar', 'stats'));
    }

    public function create(Request $request)
    {
        $pacienteId = $request->input('paciente_id');
        $paciente = $pacienteId ? Paciente::find($pacienteId) : null;

        $queryPacientes = Paciente::where(function($q) {
            $q->whereHas('camas', function($subQ) {
                $subQ->where('activa', true);
            })->orWhereHas('seguimientos', function($subQ) {
                $subQ->where('tipo', 'nota_enfermeria');
            });
        })->orderBy('apellido_paterno');

        if ($request->input('modal') === 'administrar') {
            $pacientes = $queryPacientes->get();
            $medicamentos = \App\Models\Medicamento::where('activo', true)->orderBy('nombre')->get();
            
            return view('enfermeria.notas.partials._modal-administrar', compact('pacientes', 'medicamentos'))->render();
        }

        $pacientes = $queryPacientes->get();

        if ($request->ajax()) {
            return view('enfermeria.notas.create', compact('pacientes', 'paciente'))->render();
        }

        return view('enfermeria.notas.create', compact('pacientes', 'paciente'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'contenido' => ['required', 'string', 'min:10', 'max:5000'],
            'estado_paciente' => ['nullable', 'in:estable,mejorando,grave,critico'],
            'temperatura' => ['required', 'numeric', 'min:34', 'max:42'],
            'frecuencia_cardiaca' => ['required', 'integer', 'min:30', 'max:200'],
            'frecuencia_respiratoria' => ['required', 'integer', 'min:8', 'max:60'],
            'presion_arterial' => ['required', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'saturacion_oxigeno' => ['required', 'integer', 'min:50', 'max:100'],
        ], [
            'contenido.required' => 'El contenido de la nota es obligatorio.',
            'contenido.min' => 'La nota debe ser más detallada (mínimo 10 caracteres).',
            'temperatura.required' => 'La temperatura es obligatoria.',
            'frecuencia_cardiaca.required' => 'La frecuencia cardíaca es obligatoria.',
            'frecuencia_respiratoria.required' => 'La frecuencia respiratoria es obligatoria.',
            'presion_arterial.required' => 'La presión arterial es obligatoria.',
            'presion_arterial.regex' => 'El formato de la presión arterial debe ser similar a 120/80.',
            'saturacion_oxigeno.required' => 'La saturación de oxígeno es obligatoria.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $paciente = Paciente::findOrFail($request->paciente_id);
        $cama = CamaPaciente::where('paciente_id', $paciente->id)->where('activa', true)->first();

        if (!$cama && !auth()->user()->hasRole('administrador')) {
            return response()->json(['success' => false, 'message' => 'El paciente no tiene una cama activa.'], 422);
        }

        $estado = $request->estado_paciente;
        if (empty($estado)) {
            $estado = $this->calcularEstado(
                $request->temperatura,
                $request->frecuencia_cardiaca,
                $request->frecuencia_respiratoria,
                $request->saturacion_oxigeno
            );
        }

        try {
            Seguimiento::create([
                'paciente_id' => $paciente->id,
                'user_id' => auth()->id(),
                'cama_id' => $cama?->cama_id,
                'tipo' => 'nota_enfermeria',
                'estado_paciente' => $estado,
                'contenido' => $request->contenido,
                'temperatura' => $request->temperatura,
                'frecuencia_cardiaca' => $request->frecuencia_cardiaca,
                'frecuencia_respiratoria' => $request->frecuencia_respiratoria,
                'presion_arterial' => $request->presion_arterial,
                'saturacion_oxigeno' => $request->saturacion_oxigeno,
            ]);

            return response()->json(['success' => true, 'redirect' => route('enfermeria.notas.index')]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error en BD: ' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, Seguimiento $nota)
    {
        if ($nota->tipo !== 'nota_enfermeria') {
            abort(404);
        }

        if ($this->esEnfermeria(auth()->user()) && $nota->user_id !== auth()->id() && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No tienes permiso para ver esta nota.');
        }

        $nota->load(['paciente', 'user', 'cama', 'administracion']);

        if ($request->ajax()) {
            return view('enfermeria.notas.show', compact('nota'))->render();
        }

        return view('enfermeria.notas.show', compact('nota'));
    }

    public function destroy(Seguimiento $nota): RedirectResponse
    {
        if ($nota->tipo !== 'nota_enfermeria') {
            abort(404);
        }

        if ($nota->user_id !== auth()->id() && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No puedes eliminar notas de otros usuarios.');
        }

        $nota->delete();

        return redirect()->route('enfermeria.notas.index')->with('success', 'Nota eliminada.');
    }

    private function calcularEstado($temp, $fc, $fr, $spo2): string
    {
        if ($spo2 < 90 || $temp >= 39 || $fc > 120 || $fr > 30) {
            return 'critico';
        }
        
        if ($temp >= 37.5 || $fc > 100 || $fr > 20 || $spo2 < 94) {
            return 'grave';
        }
        
        if ($temp >= 37.2) {
            return 'mejorando';
        }

        return 'estable';
    }

    private function esEnfermeria($user): bool
    {
        return $user->roles->contains(function ($rol) {
            return str_contains(strtolower($rol->name), 'enfermer');
        });
    }

    public function getMedicamentos(Request $request)
    {
        $medicamentos = \App\Models\Medicamento::where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'nombre' => $m->nombre,
                    'concentracion' => $m->concentracion,
                    'stock' => $m->stock_total_calculado
                ];
            });

        return response()->json($medicamentos);
    }

    public function administrarMedicamento(Request $request)
    {
        // 1. Validamos los datos del formulario (NO validamos 'tipo')
        $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medicamento_id' => ['required', 'exists:medicamentos,id'],
            'dosis' => ['required', 'string', 'max:50'],
            'via' => ['required', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $paciente = Paciente::findOrFail($request->paciente_id);
        $medicamento = \App\Models\Medicamento::findOrFail($request->medicamento_id);

        $cama = CamaPaciente::where('paciente_id', $paciente->id)->where('activa', true)->first();
        if (!$cama && !auth()->user()->hasRole('administrador')) {
            return response()->json(['success' => false, 'message' => 'El paciente no está hospitalizado.'], 400);
        }

        if (!InventarioService::tieneStock($medicamento, 1)) {
            return response()->json(['success' => false, 'message' => "Sin stock suficiente de {$medicamento->nombre}."], 400);
        }

        DB::beginTransaction();
        try {
            // 2. Descontar el stock
            InventarioService::salida(
                $medicamento,
                1,
                'Administración de enfermería: ' . $request->dosis . ' vía ' . $request->via,
                ['tipo' => 'nota_enfermeria', 'id' => $paciente->id]
            );

            // 🔴 3. INSERTAMOS DIRECTAMENTE EN LA BD (Saltando el ENUM estricto de Eloquent)
            $notaId = DB::table('seguimientos')->insertGetId([
                'paciente_id' => $paciente->id,
                'user_id' => auth()->id(),
                'cama_id' => $cama?->cama_id,
                'tipo' => 'nota_enfermeria', // Se inserta a la fuerza
                'contenido' => "Se administró {$medicamento->nombre} ({$medicamento->concentracion}). Dosis: {$request->dosis}. Vía: {$request->via}. " . ($request->observaciones ?? ''),
                'estado_paciente' => 'estable',
                'temperatura' => null,
                'frecuencia_cardiaca' => null,
                'frecuencia_respiratoria' => null,
                'presion_arterial' => null,
                'saturacion_oxigeno' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Guardar en la tabla de administraciones
            \App\Models\AdministracionMedicamento::create([
                'paciente_id' => $paciente->id,
                'medicamento_id' => $medicamento->id,
                'user_id' => auth()->id(),
                'consulta_id' => null,
                'seguimiento_id' => $notaId,
                'dosis' => $request->dosis,
                'via' => $request->via,
                'administrado_en' => now(),
                'reaccion_adversa' => false,
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Medicamento administrado.',
                'redirect' => route('enfermeria.notas.show', $notaId) 
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

