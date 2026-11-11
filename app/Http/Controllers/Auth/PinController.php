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
use App\Enums\AuditEvent;
use App\Services\AuditoriaService;
use App\Services\DispositivoService;
use Illuminate\Support\Facades\RateLimiter;

class PinController extends Controller
{
    private const SESSION_USER_ID = 'pin_pending_user_id';
    private const SESSION_REMEMBER = 'pin_pending_remember';

    /** Intentos de PIN permitidos por cuenta antes de bloquear el paso del PIN. */
    private const MAX_INTENTOS = 5;

    /** Segundos que dura el bloqueo (aunque vuelva a escribir la contraseña). */
    private const BLOQUEO_SEGUNDOS = 900;

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
        $clave = 'pin:'.$userId;

        // El límite es por cuenta, no por sesión ni por IP: volver a escribir la contraseña no da más intentos.
        if (RateLimiter::tooManyAttempts($clave, self::MAX_INTENTOS)) {
            return $this->bloquear($request, $user, $clave);
        }

        if (! $user || ! $user->pin || ! Hash::check($validated['pin'], $user->pin)) {
            RateLimiter::hit($clave, self::BLOQUEO_SEGUNDOS);
            $restantes = RateLimiter::remaining($clave, self::MAX_INTENTOS);

            AuditoriaService::registrar(
                AuditEvent::PIN_FALLIDO,
                'auth',
                'PIN incorrecto'.($user ? " para {$user->email}" : ''),
                $user,
                [],
                [],
                ['intentos_restantes' => $restantes]
            );

            if ($restantes === 0) {
                return $this->bloquear($request, $user, $clave);
            }

            throw ValidationException::withMessages([
                'pin' => $restantes === 1 ? 'PIN incorrecto. Te queda 1 intento.' : "PIN incorrecto. Te quedan {$restantes} intentos.",
            ]);
        }

        // PIN correcto
        RateLimiter::clear($clave);
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

    /**
     * Demasiados PIN incorrectos: se cancela el paso del PIN, se avisa en la bitácora y se pide volver a
     * iniciar sesión cuando termine el bloqueo.
     */
    private function bloquear(Request $request, ?User $user, string $clave): RedirectResponse
    {
        $minutos = (int) ceil(RateLimiter::availableIn($clave) / 60);

        AuditoriaService::registrar(
            AuditEvent::ACTIVIDAD_INUSUAL,
            'auth',
            'Paso del PIN bloqueado por demasiados intentos'.($user ? " ({$user->email})" : ''),
            $user,
            [],
            [],
            ['bloqueo_minutos' => $minutos]
        );

        $request->session()->forget([self::SESSION_USER_ID, self::SESSION_REMEMBER]);
        $request->session()->regenerate();

        return redirect()->route('login')->withErrors([
            'email' => "Demasiados intentos de PIN. Por seguridad, espera {$minutos} minutos e inicia sesión de nuevo.",
        ]);
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