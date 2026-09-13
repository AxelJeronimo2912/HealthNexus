<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Diagnostico;
use App\Models\Medicamento;
use App\Models\SignoVital;
use App\Services\InventarioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConsultaController extends Controller
{
    /* =========================================================
     |  INICIAR CONSULTA
     ========================================================= */
    public function iniciar(Cita $cita): View|RedirectResponse
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');

        if (!$esAdmin && $cita->medico_id !== $user->id) {
            abort(403, 'Esta cita no te corresponde.');
        }

        if (!in_array($cita->estado, ['confirmada', 'en_curso'])) {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'La cita debe estar confirmada o en curso para iniciar la consulta.');
        }

        if ($cita->consulta) {
            return redirect()->route('consultas.edit', $cita->consulta);
        }

        $signo = SignoVital::ultimoDe($cita->paciente_id);

        if (!$signo) {
            return redirect()->route('signos-vitales.create', [
                'paciente_id' => $cita->paciente_id,
                'cita_id' => $cita->id,
            ])->with('warning', 'Antes de iniciar la consulta debes registrar los signos vitales y la somatometría del paciente.');
        }

        $diasMaximos = 7;
        if ($signo->created_at->diffInDays(now()) > $diasMaximos) {
            return redirect()->route('signos-vitales.create', [
                'paciente_id' => $cita->paciente_id,
                'cita_id' => $cita->id,
            ])->with('warning', "Los signos vitales del paciente tienen más de {$diasMaximos} días. Registra unos nuevos antes de la consulta.");
        }

        if ($cita->estado === 'confirmada') {
            $cita->update(['estado' => 'en_curso']);
        }

        $medicamentos  = Medicamento::where('activo', true)->orderBy('nombre')->get();
        $diagnosticos  = Diagnostico::where('activo', true)->orderBy('codigo')->get();

        return view('consultas.create', compact('cita', 'signo', 'medicamentos', 'diagnosticos'));
    }

    /* =========================================================
     |  GUARDAR CONSULTA
     ========================================================= */
    public function store(Request $request, Cita $cita): RedirectResponse
    {
        $data = $this->validar($request);

        $data['cita_id']       = $cita->id;
        $data['paciente_id']   = $cita->paciente_id;
        $data['medico_id']     = $cita->medico_id;
        $data['estado']        = $request->boolean('finalizar') ? 'finalizada' : 'borrador';

        if ($data['estado'] === 'finalizada') {
            $data['finalizada_en'] = now();
        }

        // Validar stock ANTES de crear la consulta
        $this->validarStock($request);

        $consulta = Consulta::create($data);

        $this->guardarMedicamentos($consulta, $request);

        if ($data['estado'] === 'finalizada') {
            $cita->update(['estado' => 'atendida']);
        }

        return redirect()->route('consultas.show', $consulta)
            ->with('success', 'Consulta guardada correctamente.');
    }

    /* =========================================================
     |  MOSTRAR CONSULTA
     ========================================================= */
    public function show(Consulta $consulta): View
    {
        $this->autorizarAcceso($consulta);

        $consulta->load([
            'paciente',
            'medico',
            'cita',
            'medicamentos',
            'diagnosticoPrincipal',
            'diagnosticoSecundario',
        ]);

        return view('consultas.show', compact('consulta'));
    }

    /* =========================================================
     |  EDITAR CONSULTA
     ========================================================= */
    public function edit(Consulta $consulta): View
    {
        $this->autorizarAcceso($consulta);

        $consulta->load([
            'paciente',
            'medico',
            'cita',
            'medicamentos',
            'diagnosticoPrincipal',
            'diagnosticoSecundario',
        ]);

        $medicamentos = Medicamento::where('activo', true)->orderBy('nombre')->get();
        $diagnosticos = Diagnostico::where('activo', true)->orderBy('codigo')->get();

        return view('consultas.edit', compact('consulta', 'medicamentos', 'diagnosticos'));
    }

    /* =========================================================
     |  ACTUALIZAR CONSULTA
     ========================================================= */
    public function update(Request $request, Consulta $consulta): RedirectResponse
    {
        $this->autorizarAcceso($consulta);

        $data = $this->validar($request);

        if ($request->boolean('finalizar')) {
            $data['estado']        = 'finalizada';
            $data['finalizada_en'] = now();
        }

        // Validar stock antes de guardar cambios (por si se agregan nuevos)
        $this->validarStock($request, $consulta);

        $consulta->update($data);

        $this->guardarMedicamentos($consulta, $request);

        if ($consulta->estado === 'finalizada') {
            $consulta->cita->update(['estado' => 'atendida']);
        }

        return redirect()->route('consultas.show', $consulta)
            ->with('success', 'Consulta actualizada correctamente.');
    }

    /* =========================================================
     |  PDF CONSULTA
     ========================================================= */
    public function pdf(Consulta $consulta)
    {
        $this->autorizarAcceso($consulta);

        $consulta->load([
            'paciente',
            'medico',
            'cita',
            'medicamentos',
            'diagnosticoPrincipal',
            'diagnosticoSecundario',
        ]);

        $pdf = Pdf::loadView('consultas.pdf.consulta', compact('consulta'));

        return $pdf->stream('consulta-' . $consulta->id . '.pdf');
    }

    /* =========================================================
     |  PDF RECETA
     ========================================================= */
    public function pdfReceta(Consulta $consulta)
    {
        $this->autorizarAcceso($consulta);

        $consulta->load(['paciente', 'medico', 'medicamentos', 'diagnosticoPrincipal']);

        $pdf = Pdf::loadView('consultas.pdf.receta', compact('consulta'));

        return $pdf->stream('receta-' . $consulta->id . '.pdf');
    }

    /* =========================================================
     |  VALIDACIÓN DE DATOS
     ========================================================= */
    private function validar(Request $request): array
    {
        return $request->validate([
            'subjetivo'                 => ['nullable', 'string'],
            'objetivo'                  => ['nullable', 'string'],
            'analisis'                  => ['nullable', 'string'],
            'plan'                      => ['nullable', 'string'],
            'diagnostico_principal_id'  => ['nullable', 'exists:diagnosticos,id'],
            'diagnostico_secundario_id' => ['nullable', 'exists:diagnosticos,id'],
            'temperatura'               => ['nullable', 'numeric', 'min:30', 'max:45'],
            'frecuencia_cardiaca'       => ['nullable', 'integer', 'min:20', 'max:250'],
            'frecuencia_respiratoria'   => ['nullable', 'integer', 'min:5', 'max:80'],
            'presion_arterial'          => ['nullable', 'string', 'max:20'],
            'saturacion_oxigeno'        => ['nullable', 'integer', 'min:0', 'max:100'],
            'glucosa'                   => ['nullable', 'integer', 'min:0', 'max:1000'],
            'peso'                      => ['nullable', 'numeric', 'min:0.5', 'max:400'],
            'talla'                     => ['nullable', 'numeric', 'min:0.3', 'max:2.5'],
            'perimetro_abdominal'       => ['nullable', 'numeric', 'min:0', 'max:300'],
            'receta_libre'              => ['nullable', 'string'],
            'notas'                     => ['nullable', 'string'],
            'medicamentos'              => ['nullable', 'array'],
            'medicamentos.*.id'         => ['required_with:medicamentos', 'exists:medicamentos,id'],
            'medicamentos.*.dosis'      => ['nullable', 'string', 'max:100'],
            'medicamentos.*.via'        => ['nullable', 'string', 'max:50'],
            'medicamentos.*.frecuencia' => ['nullable', 'string', 'max:100'],
            'medicamentos.*.duracion'   => ['nullable', 'string', 'max:100'],
            'medicamentos.*.indicaciones' => ['nullable', 'string'],
        ]);
    }

    /* =========================================================
     |  VALIDAR STOCK DISPONIBLE
     ========================================================= */
    /**
     * Valida que haya stock suficiente para los medicamentos NUEVOS
     * (en edición, se excluyen los que ya estaban en la consulta).
     */
    private function validarStock(Request $request, ?Consulta $consulta = null): void
    {
        $meds = $request->input('medicamentos', []);
        if (empty($meds)) return;

        // IDs ya asociados a la consulta (en edición)
        $anteriores = $consulta
            ? $consulta->medicamentos()->pluck('medicamentos.id')->toArray()
            : [];

        foreach ($meds as $m) {
            if (empty($m['id'])) continue;

            // Si ya estaba en la consulta, no hay que volver a validar stock
            if (in_array($m['id'], $anteriores)) continue;

            $med = Medicamento::find($m['id']);
            if (!$med) continue;

            if (!$med->tieneStock(1)) {
                throw ValidationException::withMessages([
                    'medicamentos' => "Sin stock suficiente de {$med->nombre}. Disponible: {$med->stock_actual}.",
                ]);
            }
        }
    }

    /* =========================================================
     |  GUARDAR MEDICAMENTOS Y MOVER INVENTARIO
     ========================================================= */
    private function guardarMedicamentos(Consulta $consulta, Request $request): void
    {
        $meds = $request->input('medicamentos', []);

        // 1. Construir el array para sync
        $nuevos = [];
        foreach ($meds as $m) {
            if (empty($m['id'])) continue;

            $nuevos[$m['id']] = [
                'dosis'        => $m['dosis']        ?? null,
                'via'          => $m['via']          ?? null,
                'frecuencia'   => $m['frecuencia']   ?? null,
                'duracion'     => $m['duracion']     ?? null,
                'indicaciones' => $m['indicaciones'] ?? null,
            ];
        }

        // 2. Obtener los medicamentos que ya tenía la consulta (si es edición)
        $anteriores = $consulta->exists
            ? $consulta->medicamentos()->pluck('medicamentos.id')->toArray()
            : [];

        // 3. Devolver al stock los medicamentos que se quitaron
        foreach ($anteriores as $medId) {
            if (!isset($nuevos[$medId])) {
                $med = Medicamento::find($medId);
                if ($med) {
                    InventarioService::entrada(
                        $med,
                        1,
                        'Devolución por edición de consulta',
                        ['tipo' => 'consulta', 'id' => $consulta->id]
                    );
                }
            }
        }

        // 4. Descontar del stock los medicamentos NUEVOS
        foreach (array_keys($nuevos) as $medId) {
            if (!in_array($medId, $anteriores)) {
                $med = Medicamento::find($medId);
                if (!$med) continue;

                if (!$med->tieneStock(1)) {
                    throw ValidationException::withMessages([
                        'medicamentos' => "Sin stock suficiente de {$med->nombre}. Disponible: {$med->stock_actual}.",
                    ]);
                }

                InventarioService::salida(
                    $med,
                    1,
                    'Receta médica',
                    ['tipo' => 'consulta', 'id' => $consulta->id]
                );
            }
        }

        // 5. Guardar la receta
        $consulta->medicamentos()->sync($nuevos);
    }

    /* =========================================================
     |  AUTORIZACIÓN DE ACCESO
     ========================================================= */
    private function autorizarAcceso(Consulta $consulta): void
    {
        $user = auth()->user();

        if ($user->hasRole('administrador')) return;

        $esMedico = $user->roles->contains(function ($rol) {
            $n = strtolower($rol->name);
            return str_contains($n, 'medic') || str_contains($n, 'doctor') || str_contains($n, 'médic');
        });

        if ($esMedico && $consulta->medico_id !== $user->id) {
            abort(403, 'No tienes permiso para ver esta consulta.');
        }
    }
}