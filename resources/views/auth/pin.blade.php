<x-guest-layout>
    <x-auth-shell title="{{ $bloqueado ?? false ? 'PIN bloqueado' : 'Verificación de PIN' }}"
        subtitle="{{ $bloqueado ?? false
            ? 'Demasiados intentos fallidos. Revisa las instrucciones a continuación.'
            : 'Ingresa tu PIN de 4 dígitos para completar el acceso.' }}">

        @if ($bloqueado ?? false)
            {{-- ============================ --}}
            {{-- ESTADO BLOQUEADO             --}}
            {{-- ============================ --}}
            <div x-data="pinLockdown({{ (int) ($segundosRestantes ?? 0) }})" class="space-y-6">

                <div
                    class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <div>
                        <p class="font-semibold">Acceso temporalmente bloqueado</p>
                        <p class="mt-1">
                            Has superado el número máximo de intentos fallidos (5).
                        </p>
                        <p class="mt-2">
                            <strong>Contacta al administrador</strong> para solicitar un nuevo PIN
                            si no recuerdas el actual.
                        </p>
                    </div>
                </div>

                {{-- Cuenta regresiva --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-center">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Podrás reintentar en
                    </p>
                    <p class="mt-1 text-3xl font-bold tabular-nums text-slate-900" x-text="formatted"></p>

                    {{-- Barra de progreso --}}
                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-brand-500 transition-all duration-1000 ease-linear"
                            :style="`width: ${progress}%`"></div>
                    </div>
                </div>

                {{-- El formulario solo reaparece cuando termina el bloqueo --}}
                <template x-if="segundos <= 0">
                    <div
                        class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center text-sm text-emerald-700">
                        Ya puedes intentar de nuevo.
                    </div>
                </template>

                <div class="flex flex-col gap-3">
                    <a href="{{ route('login') }}"
                        class="block rounded-xl border border-slate-200 bg-white py-3 text-center text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Volver al inicio de sesión
                    </a>

                    <button type="button" x-show="segundos <= 0" x-cloak @click="recargar()"
                        class="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white shadow-sm shadow-brand-600/30 transition hover:bg-brand-700">
                        Reintentar PIN
                    </button>
                </div>
            </div>
        @else
            {{-- ============================ --}}
            {{-- ESTADO NORMAL (con intentos) --}}
            {{-- ============================ --}}

            @if ($errors->any())
                <div
                    class="mb-5 flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3m0 3h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Aviso de intentos restantes --}}
            @isset($intentosRestantes)
                @if ($intentosRestantes <= 2)
                    <div
                        class="mb-4 flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3m0 3h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                        <span>
                            Te quedan <strong>{{ $intentosRestantes }}</strong> intento(s) antes de bloquear el PIN por 5
                            minutos.
                        </span>
                    </div>
                @endif
            @endisset

            <form method="POST" action="{{ route('login.pin.verify') }}" class="space-y-6" id="pin-form">
                @csrf

                {{-- Input PIN (visualmente 4 cajas, un solo input real) --}}
                <div>
                    <label for="pin"
                        class="mb-2 block text-center text-xs font-medium uppercase tracking-wider text-slate-500">
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
                                <div class="flex h-16 w-14 items-center justify-center rounded-xl border-2 bg-white text-2xl font-semibold text-slate-900 shadow-sm transition-all"
                                    :class="{
                                        'border-brand-500 ring-4 ring-brand-100': value.length === i - 1,
                                        'border-slate-200': value.length !== i - 1 && value.length < i,
                                        'border-brand-500 bg-brand-50': value.length >= i
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
                    class="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white shadow-sm shadow-brand-600/30 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-50">
                    Verificar PIN
                </button>

                <a href="{{ route('login') }}"
                    class="block text-center text-sm font-medium text-slate-500 transition hover:text-gray-700 hover:underline">
                    Volver al inicio de sesión
                </a>
            </form>
        @endif
    </x-auth-shell>

    {{-- Componente Alpine para la cuenta regresiva del bloqueo --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pinLockdown', (segundosIniciales = 0) => ({
                segundos: Math.max(0, parseInt(segundosIniciales, 10) || 0),
                total: Math.max(1, parseInt(segundosIniciales, 10) || 1),
                timer: null,

                init() {
                    if (this.segundos > 0) {
                        this.timer = setInterval(() => {
                            this.segundos = Math.max(0, this.segundos - 1);
                            if (this.segundos <= 0) {
                                clearInterval(this.timer);
                                // Recarga automática para que el servidor
                                // ya no considere el bloqueo activo.
                                setTimeout(() => window.location.reload(), 1200);
                            }
                        }, 1000);
                    }
                },

                get formatted() {
                    const m = Math.floor(this.segundos / 60);
                    const s = this.segundos % 60;
                    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                },

                get progress() {
                    if (this.total <= 0) return 0;
                    return Math.max(0, Math.min(100, (this.segundos / this.total) * 100));
                },

                recargar() {
                    window.location.reload();
                }
            }));
        });
    </script>
</x-guest-layout>
