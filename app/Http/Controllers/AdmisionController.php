<?php

namespace App\Http\Controllers;

use App\Models\Admision;
use App\Models\Cama;
use App\Models\Hospital;
use App\Models\Paciente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdmisionController extends Controller
{
    public function index(Request $request): View
    {
        $fecha = $request->input('fecha', today()->format('Y-m-d'));
        $filtroEstado = $request->input('estado');
        $buscar = $request->input('buscar');

        $query = Admision::with(['paciente', 'medico', 'cama', 'hospitalDerivado'])
            ->whereDate('fecha_hora_llegada', $fecha)
            ->orderByDesc('fecha_hora_llegada');

        if ($filtroEstado) {
            $query->where('estado', $filtroEstado);
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('folio', 'like', "%{$buscar}%")
                  ->orWhereHas('paciente', function ($sub) use ($buscar) {
                      $sub->where('nombre', 'like', "%{$buscar}%")
                          ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                          ->orWhere('curp', 'like', "%{$buscar}%");
                  });
            });
        }

        $admisiones = $query->paginate(20)->withQueryString();

        $disponibilidad = [
            'camas_libres' => Cama::where('estado', 'disponible')->where('activo', true)->count(),
            'camas_ocupadas' => Cama::where('estado', 'ocupada')->count(),
            'medicos_activos' => User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%medic%']))
                ->where('activo', true)->count(),
            'en_espera' => Admision::where('estado', 'en_espera')->whereDate('fecha_hora_llegada', $fecha)->count(),
        ];

        return view('admisiones.index', compact('admisiones', 'fecha', 'filtroEstado', 'buscar', 'disponibilidad'));
    }

    public function create(): View
    {
        $pacientes = Paciente::orderBy('apellido_paterno')->get();

        return view('admisiones.create', compact('pacientes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'tipo' => ['required', 'in:urgencias,consulta_externa,hospitalizacion,traslado'],
            'triage' => ['nullable', 'in:rojo,naranja,amarillo,verde,azul'],
            'motivo' => ['required', 'string'],
            'diagnostico_presuntivo' => ['nullable', 'string'],
            'notas' => ['nullable', 'string'],
        ]);

        $folio = 'ADM-' . now()->format('Ymd') . '-' . str_pad(
            Admision::whereDate('created_at', today())->count() + 1,
            3, '0', STR_PAD_LEFT
        );

        $admision = Admision::create([
            'paciente_id' => $data['paciente_id'],
            'user_id' => auth()->id(),
            'folio' => $folio,
            'fecha_hora_llegada' => now(),
            'tipo' => $data['tipo'],
            'triage' => $data['triage'] ?? null,
            'motivo' => $data['motivo'],
            'diagnostico_presuntivo' => $data['diagnostico_presuntivo'] ?? null,
            'estado' => 'en_espera',
            'notas' => $data['notas'] ?? null,
        ]);

        return redirect()->route('admisiones.show', $admision)
            ->with('success', 'Paciente admitido. Folio: ' . $folio);
    }

    public function show(Admision $admision): View
    {
        $admision->load(['paciente', 'medico', 'cama', 'hospitalDerivado', 'user']);

        $camasDisponibles = Cama::where('estado', 'disponible')->where('activo', true)->get();

        $medicosDisponibles = User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%medic%']))
            ->where('activo', true)->get();

        $hospitalActual = [
            'lat' => 19.4326,
            'lng' => -99.1332,
            'nombre' => config('app.name', 'HealthNexus'),
        ];

        $hospitales = Hospital::where('activo', true)->get()
            ->map(function ($h) use ($hospitalActual) {
                $h->distancia = $h->distanciaDesde($hospitalActual['lat'], $hospitalActual['lng']);
                return $h;
            })
            ->sortBy('distancia')
            ->values();

        return view('admisiones.show', compact(
            'admision', 'camasDisponibles', 'medicosDisponibles',
            'hospitales', 'hospitalActual'
        ));
    }

    public function asignar(Request $request, Admision $admision): RedirectResponse
    {
        $data = $request->validate([
            'cama_id' => ['required', 'exists:camas,id'],
            'medico_id' => ['required', 'exists:users,id'],
        ]);

        $admision->update([
            'cama_id' => $data['cama_id'],
            'medico_id' => $data['medico_id'],
            'estado' => 'hospitalizado',
        ]);

        Cama::find($data['cama_id'])->update(['estado' => 'ocupada']);

        return back()->with('success', 'Paciente hospitalizado correctamente.');
    }

    public function derivar(Request $request, Admision $admision): RedirectResponse
    {
        $data = $request->validate([
            'hospital_derivado_id' => ['required', 'exists:hospitales,id'],
            'motivo_derivacion' => ['required', 'string'],
        ]);

        $admision->update([
            'hospital_derivado_id' => $data['hospital_derivado_id'],
            'fecha_derivacion' => now(),
            'motivo_derivacion' => $data['motivo_derivacion'],
            'estado' => 'derivado',
        ]);

        return redirect()->route('admisiones.pase-salida', $admision)
            ->with('success', 'Paciente derivado. Generando pase de salida...');
    }

    public function paseSalida(Admision $admision)
    {
        $admision->load(['paciente', 'medico', 'hospitalDerivado', 'user']);

        if ($admision->estado !== 'derivado') {
            return back()->with('error', 'El paciente debe estar derivado para generar el pase.');
        }

        $carpeta = storage_path('app/public/pases-salida');
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $pdf = Pdf::loadView('admisiones.pdf.pase-salida', compact('admision'));
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setOption('isHtml5ParserEnabled', true);

        $output = $pdf->output();

        $nombreArchivo = 'pases-salida/pase-' . $admision->folio . '.pdf';
        \Storage::disk('public')->put($nombreArchivo, $output);
        $admision->update(['pase_salida_pdf' => $nombreArchivo]);

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="pase-' . $admision->folio . '.pdf"',
        ]);
    }

    public function destroy(Admision $admision): RedirectResponse
    {
        $admision->delete();

        return redirect()->route('admisiones.index')
            ->with('success', 'Admisión eliminada.');
    }
}