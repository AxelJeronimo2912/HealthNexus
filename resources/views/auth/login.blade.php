<x-guest-layout>
    <x-auth-shell title="Bienvenido de nuevo" subtitle="Ingresa con tu cuenta institucional para continuar.">

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ ver: false, enviando: false }" @submit="enviando = true">
            @csrf

            <x-input name="email" type="email" label="Correo electrónico" icon="envelope"
                :value="old('email')" required autofocus autocomplete="username" placeholder="nombre@hospital.mx" />

            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-slate-700">Contraseña</label>
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-brand-600 hover:text-brand-700" href="{{ route('password.request') }}">
                            ¿La olvidaste?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-heroicon-o-lock-closed class="h-[18px] w-[18px]" />
                    </span>
                    <input id="password" name="password" :type="ver ? 'text' : 'password'" type="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="block w-full rounded-xl border-0 bg-white py-2.5 pl-10 pr-11 text-sm text-slate-900 shadow-sm ring-1 ring-inset {{ $errors->has('password') ? 'ring-red-300 focus:ring-red-500' : 'ring-slate-200 focus:ring-brand-500' }} placeholder:text-slate-400 focus:ring-2 focus:ring-inset" />
                    <button type="button" @click="ver = !ver" class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600"
                        :aria-label="ver ? 'Ocultar contraseña' : 'Mostrar contraseña'">
                        <x-heroicon-o-eye class="h-[18px] w-[18px]" x-show="!ver" />
                        <x-heroicon-o-eye-slash class="h-[18px] w-[18px]" x-show="ver" x-cloak />
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
            </div>

            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" name="remember"
                    class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                Mantener la sesión iniciada
            </label>

            <button type="submit" :disabled="enviando"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-brand-600/30 transition hover:bg-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 disabled:opacity-70">
                <svg x-show="enviando" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="3" />
                    <path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                </svg>
                <span x-text="enviando ? 'Verificando…' : 'Iniciar sesión'">Iniciar sesión</span>
            </button>
        </form>

        <p class="mt-8 flex items-start gap-2 rounded-xl bg-slate-50 px-4 py-3 text-xs leading-relaxed text-slate-500 ring-1 ring-inset ring-slate-100">
            <x-heroicon-o-shield-check class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
            Después de tu contraseña te pediremos tu PIN de 4 dígitos para completar el acceso.
        </p>
    </x-auth-shell>
</x-guest-layout>
