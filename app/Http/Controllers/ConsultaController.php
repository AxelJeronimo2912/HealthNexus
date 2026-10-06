<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultaRequest;
use App\Http\Requests\UpdateConsultaRequest;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Diagnostico;
use App\Models\Medicamento;
use App\Models\SignoVital;
use App\Services\InventarioService;
use App\Services\CobroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Services\RecetaValidator;

class ConsultaController extends Controller
{
    /* =========================================================
     |  INICIAR CONSULTA
     ========================================================= */
    public function iniciar(Cita $cita): View|RedirectResponse
    {
        $user    = auth()->user();
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
                'cita_id'     => $cita->id,
            ])->with('warning', 'Antes de iniciar la consulta debes registrar los signos vitales y la somatometría del paciente.');
        }

        $diasMaximos = 7;
        if ($signo->created_at->diffInDays(now()) > $diasMaximos) {
            return redirect()->route('signos-vitales.create', [
                'paciente_id' => $cita->paciente_id,
                'cita_id'     => $cita->id,
            ])->with('warning', "Los signos vitales del paciente tienen más de {$diasMaximos} días. Registra unos nuevos antes de la consulta.");
        }

        if ($cita->estado === 'confirmada') {
            $cita->update(['estado' => 'en_curso']);
        }

        $medicamentos = Medicamento::where('activo', true)->orderBy('nombre')->get();
        $diagnosticos = Diagnostico::where('activo', true)->orderBy('codigo')->get();

        return view('consultas.create', compact('cita', 'signo', 'medicamentos', 'diagnosticos'));
    }

    /* =========================================================
     |  GUARDAR CONSULTA
     ========================================================= */
    public function store(StoreConsultaRequest $request, Cita $cita): RedirectResponse
    {
        $data = $request->validated();

        $data['cita_id']     = $cita->id;
        $data['paciente_id'] = $cita->paciente_id;
        $data['medico_id']   = $cita->medico_id;
        $data['estado']      = $request->boolean('finalizar') ? 'finalizada' : 'borrador';

        if ($data['estado'] === 'finalizada') {
            $data['finalizada_en'] = now();
        }

        $this->validarStock($request);

        $consulta = Consulta::create($data);

        $this->guardarMedicamentos($consulta, $request);

        $cita->update(['estado' => 'atendida']);

        if ($consulta->estado === 'finalizada') {
            app(CobroService::class)->cobrarCita($cita);
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
    public function update(UpdateConsultaRequest $request, Consulta $consulta): RedirectResponse
    {
        $this->autorizarAcceso($consulta);

        $estadoAnterior = $consulta->estado;

        $data = $request->validated();

        if ($request->boolean('finalizar')) {
            $data['estado']        = 'finalizada';
            $data['finalizada_en'] = now();
        }

        $this->validarStock($request, $consulta);

        $consulta->update($data);

        $this->guardarMedicamentos($consulta, $request);

        $consulta->cita->update(['estado' => 'atendida']);

        if ($consulta->estado === 'finalizada' && $estadoAnterior !== 'finalizada') {
            app(CobroService::class)->cobrarCita($consulta->cita);
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

    RecetaValidator::validarParaImprimir($consulta); 

    $pdf = Pdf::loadView('consultas.pdf.receta', compact('consulta'));

    return $pdf->stream('receta-' . $consulta->id . '.pdf');
}

    /* =========================================================
     |  VALIDAR STOCK
     ========================================================= */
    private function validarStock(Request $request, ?Consulta $consulta = null): void
    {
        $meds = $request->input('medicamentos', []);
        if (empty($meds)) return;

        $anteriores = $consulta
            ? $consulta->medicamentos()->pluck('medicamentos.id')->toArray()
            : [];

        foreach ($meds as $m) {
            if (empty($m['id'])) continue;
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

        $anteriores = $consulta->exists
            ? $consulta->medicamentos()->pluck('medicamentos.id')->toArray()
            : [];

        // Devolver al stock los que se quitaron
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

        // Descontar del stock los nuevos
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