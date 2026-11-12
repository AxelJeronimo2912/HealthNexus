<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\RoleHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Services\DispositivoService;

class PinController extends Controller
{
    private const SESSION_USER_ID = 'pin_pending_user_id';
    private const SESSION_REMEMBER = 'pin_pending_remember';

    /** Intentos fallidos permitidos antes del bloqueo. */
    private const MAX_ATTEMPTS = 5;

    /** Segundos de bloqueo (5 minutos). */
    private const DECAY_SECONDS = 300;

    /**
     * Clave única del rate limiter para un usuario.
     */
    private function throttleKey(int $userId): string
    {
        return 'pin-verify:' . $userId;
    }

    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has(self::SESSION_USER_ID)) {
            return redirect()->route('login');
        }

        $userId = (int) $request->session()->get(self::SESSION_USER_ID);
        $key    = $this->throttleKey($userId);

        // Si está bloqueado, mostramos la vista con el aviso.
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $segundos = RateLimiter::availableIn($key);

            return view('auth.pin', [
                'bloqueado' => true,
                'segundosRestantes' => $segundos,
            ]);
        }

        return view('auth.pin', [
            'bloqueado' => false,
            'intentosRestantes' => RateLimiter::remaining($key, self::MAX_ATTEMPTS),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $userId = $request->session()->get(self::SESSION_USER_ID);

        if (! $userId) {
            return redirect()->route('login');
        }

        $key = $this->throttleKey((int) $userId);

        // 🔒 Bloqueo activo: ni siquiera validamos el PIN.
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $minutos = (int) ceil(RateLimiter::availableIn($key) / 60);

            throw ValidationException::withMessages([
                'pin' => "PIN bloqueado por demasiados intentos fallidos. "
                       . "Intenta de nuevo en {$minutos} minuto(s) o contacta al administrador para solicitar un nuevo PIN.",
            ]);
        }

        $validated = $request->validate([
            'pin' => ['required', 'digits:4'],
        ], [
            'pin.required' => 'Debes ingresar tu PIN.',
            'pin.digits'   => 'El PIN debe tener exactamente 4 dígitos.',
        ]);

        $user = User::find($userId);

        if (! $user || ! $user->pin || ! Hash::check($validated['pin'], $user->pin)) {
            // ❌ Intento fallido: registramos en el rate limiter.
            RateLimiter::hit($key, self::DECAY_SECONDS);

            $restantes = RateLimiter::remaining($key, self::MAX_ATTEMPTS);

            if ($restantes <= 0) {
                throw ValidationException::withMessages([
                    'pin' => 'PIN bloqueado por demasiados intentos fallidos. '
                           . 'Contacta al administrador para solicitar un nuevo PIN. '
                           . 'Podrás reintentar en 5 minutos.',
                ]);
            }

            throw ValidationException::withMessages([
                'pin' => "PIN incorrecto. Te quedan {$restantes} intento(s).",
            ]);
        }

        RateLimiter::clear($key);

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
}