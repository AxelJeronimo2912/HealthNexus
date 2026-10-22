{{--
    Logotipo único de HealthNexus.
    <x-application-logo />                          → solo el símbolo
    <x-application-logo wordmark />                 → símbolo + nombre
    <x-application-logo wordmark variant="light" /> → para fondos oscuros
    <x-application-logo full />                     → logotipo oficial completo (con lema)
--}}
@props(['wordmark' => false, 'variant' => 'dark', 'full' => false, 'size' => 'h-9 w-9'])

@if ($full)
    <img src="{{ asset('images/logo-healthnexus.png') }}" alt="HealthNexus — Tecnología que cuida vidas"
        {{ $attributes->merge(['class' => 'h-40 w-auto object-contain']) }} />
@else
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
        <span class="{{ $size }} shrink-0 grid place-items-center rounded-xl {{ $variant === 'light' ? 'bg-white shadow-sm' : 'bg-transparent' }}">
            <img src="{{ asset('images/logo-mark.png') }}" alt="" class="w-[78%] h-[78%] object-contain" />
        </span>
        @if ($wordmark)
            <span class="text-[17px] font-bold tracking-tight leading-none whitespace-nowrap">
                <span class="{{ $variant === 'light' ? 'text-white' : 'text-brand-900' }}">Health</span><span class="{{ $variant === 'light' ? 'text-accent-300' : 'text-accent-600' }}">Nexus</span>
            </span>
        @endif
    </span>
@endif
