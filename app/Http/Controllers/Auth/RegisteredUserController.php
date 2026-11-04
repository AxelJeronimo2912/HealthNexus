<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
    public function reportePdf()
{
    $users = User::with('roles')
        ->orderBy('apellido_paterno')
        ->orderBy('nombre')
        ->get();

    $stats = [
        'total' => $users->count(),
        'activos' => $users->where('activo', true)->count(),
        'inactivos' => $users->where('activo', false)->count(),
    ];

    $fechaEmision = now()->format('d/m/Y H:i');

    $pdf = Pdf::loadView('admin.users.pdf.reporte', compact(
        'users',
        'stats',
        'fechaEmision'
    ));

    return $pdf->download(
        'reporte-colaboradores-' . now()->format('Y-m-d') . '.pdf'
    );
}
}
