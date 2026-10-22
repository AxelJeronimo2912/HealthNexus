<x-guest-layout>
    <x-auth-shell title="Verificación de PIN" subtitle="Ingresa tu PIN de 4 dígitos para completar el acceso.">
        {{-- Errores --}}
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
    </x-auth-shell>
</x-guest-layout>
