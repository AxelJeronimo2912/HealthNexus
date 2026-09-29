<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\CuentaItem;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CuentaController extends Controller
{
    /**
     * Muestra la cuenta abierta del paciente (o la crea).
     */
    public function delPaciente(Paciente $paciente): View
    {
        $cuenta = $paciente->obtenerCuentaAbierta();
        $cuenta->load(['items.servicio', 'items.cita', 'paciente']);

        return view('cuentas.show', compact('paciente', 'cuenta'));
    }

    /**
     * Elimina un item (solo si la cuenta está abierta).
     */
    public function destroyItem(CuentaItem $item): RedirectResponse
    {
        if ($item->cuenta->estado !== 'abierta') {
            return back()->with('error', 'No puedes modificar una cuenta cerrada.');
        }

        $item->delete();

        return back()->with('success', 'Cargo eliminado.');
    }

    /**
     * Cierra la cuenta (ya no se puede modificar).
     */
    public function cerrar(Cuenta $cuenta): RedirectResponse
    {
        if ($cuenta->estado !== 'abierta') {
            return back()->with('error', 'Esta cuenta ya no está abierta.');
        }

        $cuenta->update([
            'estado'     => 'cerrada',
            'cerrada_en' => now(),
        ]);

        return back()->with('success', 'Cuenta cerrada correctamente.');
    }

    public function index(Request $request): View
{
    $filtroEstado  = $request->input('estado');   // abierta | cerrada | todas
    $soloConSaldo  = $request->boolean('con_saldo', false);
    $buscar        = $request->input('buscar');

    $query = Cuenta::query()
        ->with(['paciente', 'items'])
        ->orderByDesc('created_at');

    // Filtro por estado
    if ($filtroEstado && $filtroEstado !== 'todas') {
        $query->where('estado', $filtroEstado);
    }

    // Filtro "solo con saldo pendiente"
    if ($soloConSaldo) {
        $query->where('saldo', '>', 0);
    }

    // Búsqueda por folio, paciente, CURP
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

    $cuentas = $query->paginate(20)->withQueryString();

    // KPIs
    $stats = [
        'abiertas'       => Cuenta::where('estado', 'abierta')->count(),
        'por_cobrar'     => (float) Cuenta::where('saldo', '>', 0)->sum('saldo'),
        'cobrado_hoy'    => (float) Cuenta::whereDate('updated_at', today())
                                ->where('estado', '!=', 'cancelada')
                                ->sum('pagado'),
        'cerradas_mes'   => Cuenta::where('estado', 'cerrada')
                                ->whereMonth('cerrada_en', now()->month)
                                ->whereYear('cerrada_en', now()->year)
                                ->count(),
    ];

    return view('cuentas.index', compact('cuentas', 'filtroEstado', 'soloConSaldo', 'buscar', 'stats'));
}
}