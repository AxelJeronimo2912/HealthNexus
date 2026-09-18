
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

    class PinController extends Controller
    {
        private const SESSION_USER_ID  = 'pin_pending_user_id';
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
                'pin.digits'   => 'El PIN debe tener exactamente 4 dígitos.',
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

            $request->session()->forget([self::SESSION_USER_ID, self::SESSION_REMEMBER]);
            $request->session()->regenerate();

            return redirect()->intended(
                RoleHelper::tieneRol($user, 'administrador')
                    ? route('admin.dashboard', absolute: false)
                    : route('dashboard', absolute: false)
            );
        }
    }


En la carpeta resources/views/auth/pin.blade.php remplazar el código existente por este
<x-guest-layout>
    <div class="w-full max-w-sm mx-auto">
        {{-- Encabezado --}}
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 11h14v10H5V11z" />
                </svg>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900">Verificación de PIN</h1>
            <p class="mt-2 text-sm text-gray-500">
                Ingresa tu PIN de 4 dígitos para completar el acceso.
            </p>
        </div>

        {{-- Errores --}}
        @if ($errors->any())
            <div
                class="mb-5 flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3m0 3h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.pin.verify') }}" class="space-y-6" id="pin-form">
            @csrf

            {{-- Input PIN (visualmente 4 cajas, un solo input real) --}}
            <div>
                <label for="pin"
                    class="mb-2 block text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                    PIN
                </label>

                <div class="relative" x-data="{ value: '' }">
                    {{-- Input real oculto, pero funcional y accesible --}}
                    <input id="pin" name="pin" type="password" inputmode="numeric" pattern="\d{4}"
                        maxlength="4" autofocus autocomplete="off" x-model="value"
                        class="peer absolute inset-0 h-full w-full cursor-pointer opacity-0">

                    {{-- Cajas visuales --}}
                    <div class="pointer-events-none flex justify-center gap-3">
                        <template x-for="i in 4" :key="i">
                            <div class="flex h-14 w-12 items-center justify-center rounded-xl border-2 bg-white text-2xl font-semibold text-gray-900 shadow-sm transition-all"
                                :class="{
                                    'border-blue-500 ring-2 ring-blue-100': value.length === i - 1,
                                    'border-gray-300': value.length !== i - 1 && value.length < i,
                                    'border-blue-500 bg-blue-50': value.length >= i
                                }">
                                <template x-if="value.length >= i">
                                    <span>•</span>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-blue-600 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50">
                Verificar PIN
            </button>

            <a href="{{ route('login') }}"
                class="block text-center text-sm font-medium text-gray-500 transition hover:text-gray-700 hover:underline">
                Volver al inicio de sesión
            </a>
        </form>
    </div>
</x-guest-layout>