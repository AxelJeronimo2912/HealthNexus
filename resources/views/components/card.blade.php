{{--
    Tarjeta base.
    <x-card title="Pacientes recientes" subtitle="Últimos 7 días" :padding="false"> ... </x-card>
--}}
@props(['title' => null, 'subtitle' => null, 'padding' => true])

<section {{ $attributes->merge(['class' => 'bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card']) }}>
    @if ($title || isset($actions))
        <header class="flex items-start justify-between gap-4 px-5 py-4 border-b border-slate-100">
            <div>
                @if ($title)<h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>@endif
                @if ($subtitle)<p class="mt-0.5 text-xs text-slate-500">{{ $subtitle }}</p>@endif
            </div>
            @isset($actions)<div class="flex items-center gap-2">{{ $actions }}</div>@endisset
        </header>
    @endif
    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>
</section>
