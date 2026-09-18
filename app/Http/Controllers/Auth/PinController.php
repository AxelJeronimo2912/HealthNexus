<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PinController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (!session()->has('pin_pending_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.pin');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'digits:4'],
        ], [
            'pin.required' => 'Debes ingresar tu PIN.',
            'pin.digits' => 'El PIN debe tener exactamente 4 dígitos.',
        ]);

        $userId = session('pin_pending_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (!$user || !$user->pin || !Hash::check($request->pin, $user->pin)) {
            throw ValidationException::withMessages([
                'pin' => 'PIN incorrecto.',
            ]);
        }

        // PIN correcto → iniciar sesión de verdad
        Auth::login($user, session('pin_pending_remember', false));

        $request->session()->forget(['pin_pending_user_id', 'pin_pending_remember']);
        $request->session()->regenerate();

        if (\App\Support\RoleHelper::tieneRol($user, 'administrador')) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
   
      private function cancelPinFlow(Request $request): RedirectResponse
{
    $request->session()->forget(['pin_pending_user_id', 'pin_pending_remember']);
    $request->session()->regenerate();

    return redirect()->route('login')
        ->with('status', 'Verificación cancelada. Inicia sesión nuevamente.');
}
}