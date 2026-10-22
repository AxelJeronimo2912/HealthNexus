{{--
    Estructura común de las pantallas de acceso (login, PIN, recuperación).
    Izquierda: formulario. Derecha: panel de marca (solo escritorio).
--}}
@props(['title', 'subtitle' => null])

<div class="flex min-h-screen bg-white">
    {{-- Formulario --}}
    <div class="flex w-full flex-col px-6 py-8 sm:px-12 lg:w-[46%] lg:px-16 xl:px-24">
        <a href="{{ url('/') }}" class="inline-flex">
            <x-application-logo wordmark size="h-10 w-10" />
        </a>

        <div class="mx-auto flex w-full max-w-sm flex-1 flex-col justify-center py-10">
            <h1 class="text-[28px] font-bold tracking-tight text-slate-900">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-2 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
            <div class="mt-8">
                {{ $slot }}
            </div>
        </div>

        <p class="text-xs text-slate-400">© {{ date('Y') }} HealthNexus. Todos los derechos reservados.</p>
    </div>

    {{-- Panel de marca --}}
    <div class="relative hidden flex-1 overflow-hidden bg-brand-950 lg:flex">
        <div class="absolute inset-0 bg-ecg opacity-80"></div>
        <div class="absolute -right-40 -top-40 h-[520px] w-[520px] rounded-full bg-accent-600/30 blur-[120px]"></div>
        <div class="absolute -bottom-48 -left-24 h-[480px] w-[480px] rounded-full bg-brand-500/30 blur-[120px]"></div>

        <div class="relative z-10 flex w-full flex-col justify-between p-14 xl:p-20">
            <x-badge tone="accent" class="self-start !bg-white/10 !text-accent-200 !ring-white/15">Plataforma clínica integral</x-badge>

            <div>
                <div class="mb-8 grid h-20 w-20 place-items-center rounded-3xl bg-white shadow-2xl shadow-black/30">
                    <img src="{{ asset('images/logo-mark.png') }}" alt="" class="h-14 w-14 object-contain">
                </div>
                <h2 class="max-w-md text-4xl font-bold leading-tight tracking-tight text-white">
                    Tecnología que <span class="bg-gradient-to-r from-accent-300 to-brand-200 bg-clip-text text-transparent">cuida vidas</span>.
                </h2>
                <p class="mt-4 max-w-md text-base leading-relaxed text-slate-300">
                    Expedientes, agenda, farmacia y alertas clínicas en un solo lugar, con acceso según el rol de cada profesional.
                </p>

                <ul class="mt-10 grid max-w-lg grid-cols-1 gap-4 xl:grid-cols-3">
                    @foreach ([['document-text', 'Expediente clínico'], ['calendar-days', 'Agenda médica'], ['bell-alert', 'Alertas en tiempo real']] as [$icono, $texto])
                        <li class="flex items-center gap-3 rounded-2xl bg-white/[.06] px-4 py-3 ring-1 ring-inset ring-white/10 backdrop-blur">
                            <x-dynamic-component :component="'heroicon-o-' . $icono" class="h-5 w-5 shrink-0 text-accent-300" />
                            <span class="text-sm font-medium text-slate-200">{{ $texto }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="flex items-center gap-2 text-xs text-slate-400">
                <x-heroicon-o-lock-closed class="h-4 w-4" />
                Acceso protegido · Verificación con PIN · Permisos por rol
            </p>
        </div>
    </div>
</div>
