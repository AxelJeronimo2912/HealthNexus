{{--
    Indicador clave.
    <x-kpi label="Camas disponibles" :value="$camas" icon="home-modern" tone="emerald"
           hint="De 40 camas" :href="route('camas.index')" badge="72% ocupado" />
    tone: brand | accent | emerald | amber | red | sky
--}}
@props(['label', 'value', 'icon' => 'chart-bar', 'tone' => 'brand', 'hint' => null, 'href' => null, 'badge' => null, 'badgeTone' => null, 'cta' => 'Ver detalle'])

@php
    $tones = [
        'brand' => ['bg-brand-50 text-brand-600 ring-brand-100', 'text-brand-600'],
        'accent' => ['bg-accent-50 text-accent-600 ring-accent-100', 'text-accent-600'],
        'emerald' => ['bg-emerald-50 text-emerald-600 ring-emerald-100', 'text-emerald-700'],
        'amber' => ['bg-amber-50 text-amber-600 ring-amber-100', 'text-amber-700'],
        'red' => ['bg-red-50 text-red-600 ring-red-100', 'text-red-600'],
        'sky' => ['bg-sky-50 text-sky-600 ring-sky-100', 'text-sky-700'],
    ];
    [$iconCls, $ctaCls] = $tones[$tone] ?? $tones['brand'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'group relative flex flex-col bg-white rounded-2xl p-5 ring-1 ring-slate-200/70 shadow-card transition-all duration-200' . ($href ? ' hover:ring-slate-300 hover:shadow-md' : '')]) }}>
    <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg ring-1 ring-inset {{ $iconCls }}">
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-5 w-5" />
        </div>
    </div>
    <div class="mt-2 flex items-baseline gap-2 flex-wrap">
        <p class="text-3xl font-bold tracking-tight text-slate-900 tabular">{{ $value }}</p>
        @if ($badge)
            <x-badge :tone="$badgeTone ?? $tone" size="sm">{{ $badge }}</x-badge>
        @endif
    </div>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    {{ $slot }}
    @if ($href)
        <p class="mt-4 pt-3 border-t border-slate-100 inline-flex items-center gap-1 text-xs font-semibold {{ $ctaCls }}">
            {{ $cta }}
            <x-heroicon-m-arrow-right class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />
        </p>
    @endif
</{{ $tag }}>
