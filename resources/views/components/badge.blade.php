{{--
    Etiqueta de estado. Usa el tono por semántica, no por gusto:
      emerald = activo/disponible · amber = advertencia/pendiente · red = crítico/inactivo
      sky = informativo · brand/accent = categorías · slate = neutro
    <x-badge tone="emerald" dot>Activo</x-badge>
--}}
@props(['tone' => 'slate', 'dot' => false, 'size' => 'md'])

@php
    $tones = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'brand' => 'bg-brand-50 text-brand-700 ring-brand-100',
        'accent' => 'bg-accent-50 text-accent-700 ring-accent-100',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-100',
        'red' => 'bg-red-50 text-red-700 ring-red-100',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-100',
    ];
    $dots = [
        'slate' => 'bg-slate-400', 'brand' => 'bg-brand-500', 'accent' => 'bg-accent-500',
        'emerald' => 'bg-emerald-500', 'amber' => 'bg-amber-500', 'red' => 'bg-red-500', 'sky' => 'bg-sky-500',
    ];
    $sizeCls = $size === 'sm' ? 'px-2 py-0.5 text-[11px]' : 'px-2.5 py-1 text-xs';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full font-semibold ring-1 ring-inset whitespace-nowrap $sizeCls " . ($tones[$tone] ?? $tones['slate'])]) }}>
    @if ($dot)<span class="h-1.5 w-1.5 rounded-full {{ $dots[$tone] ?? $dots['slate'] }}"></span>@endif
    {{ $slot }}
</span>
