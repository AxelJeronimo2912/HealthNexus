{{--
    Encabezado de página.
    <x-page-header title="Medicamentos" subtitle="Catálogo e inventario">
        <x-slot:actions> <x-button icon="plus">Nuevo</x-button> </x-slot:actions>
    </x-page-header>
--}}
@props(['title', 'subtitle' => null, 'icon' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="flex items-center gap-3.5 min-w-0">
        @if ($icon)
            <div class="hidden sm:grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-inset ring-brand-100">
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-6 w-6" />
            </div>
        @endif
        <div class="min-w-0">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 truncate">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2 shrink-0">{{ $actions }}</div>
    @endisset
</div>
