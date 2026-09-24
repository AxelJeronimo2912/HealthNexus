<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\RoleHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Services\DispositivoService;

class PinController extends Controller
{
    private const SESSION_USER_ID = 'pin_pending_user_id';
    private const SESSION_REMEMBER = 'pin_pending_remember';

    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has(self::SESSION_USER_ID)) {
            return redirect()->route('login');
        }

        return view('auth.pin');
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pin' => ['required', 'digits:4'],
        ], [
            'pin.required' => 'Debes ingresar tu PIN.',
            'pin.digits' => 'El PIN debe tener exactamente 4 dígitos.',
        ]);

        $userId = $request->session()->get(self::SESSION_USER_ID);

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user || ! $user->pin || ! Hash::check($validated['pin'], $user->pin)) {
            throw ValidationException::withMessages([
                'pin' => 'PIN incorrecto.',
            ]);
        }

        // PIN correcto
        Auth::login($user, $request->session()->get(self::SESSION_REMEMBER, false));
DispositivoService::registrarDesdeRequest($request, $user->id);

        $request->session()->forget([self::SESSION_USER_ID, self::SESSION_REMEMBER]);
        $request->session()->regenerate();

        return redirect()->intended(
            RoleHelper::tieneRol($user, 'administrador')
                ? route('admin.dashboard', absolute: false)
                : route('dashboard', absolute: false)
        );
    }

    private function cancelPinFlow(Request $request): RedirectResponse
    {
        $request->session()->forget([
            self::SESSION_USER_ID,
            self::SESSION_REMEMBER,
        ]);

        $request->session()->regenerate();

        return redirect()->route('login')
            ->with('status', 'Verificación cancelada. Inicia sesión nuevamente.');
    }
}