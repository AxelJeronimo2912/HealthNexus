{{--
    Botón del sistema de diseño.
    <x-button>Guardar</x-button>
    <x-button variant="secondary" href="{{ route(...) }}" icon="arrow-left">Volver</x-button>
    Variantes: primary | secondary | ghost | danger | accent    Tamaños: sm | md | lg
--}}
@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'icon' => null, 'type' => 'button'])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none active:scale-[.98] whitespace-nowrap';
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-sm',
    ];
    $variants = [
        'primary' => 'bg-brand-600 text-white shadow-sm hover:bg-brand-700 focus-visible:ring-brand-500',
        'accent' => 'bg-accent-600 text-white shadow-sm hover:bg-accent-700 focus-visible:ring-accent-500',
        'secondary' => 'bg-white text-slate-700 ring-1 ring-inset ring-slate-200 shadow-sm hover:bg-slate-50 hover:text-slate-900 focus-visible:ring-brand-500',
        'ghost' => 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-brand-500',
        'danger' => 'bg-red-600 text-white shadow-sm hover:bg-red-700 focus-visible:ring-red-500',
    ];
    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
    $iconSize = $size === 'sm' ? 'w-4 h-4' : 'w-[18px] h-[18px]';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-dynamic-component :component="'heroicon-o-' . $icon" class="{{ $iconSize }} -ml-0.5" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-dynamic-component :component="'heroicon-o-' . $icon" class="{{ $iconSize }} -ml-0.5" />@endif
        {{ $slot }}
    </button>
@endif
