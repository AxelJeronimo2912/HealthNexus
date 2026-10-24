{{--
    Estado vacío para tablas y listas.
    <x-empty-state icon="beaker" title="Sin medicamentos" description="Registra el primero para comenzar.">
        <x-button icon="plus">Nuevo medicamento</x-button>
    </x-empty-state>
--}}
@props(['icon' => 'inbox', 'title' => 'Sin registros', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center px-6 py-14']) }}>
    <div class="relative mb-4">
        <div class="absolute inset-0 rounded-full bg-brand-100 blur-xl opacity-60"></div>
        <div class="relative grid h-14 w-14 place-items-center rounded-2xl bg-white ring-1 ring-slate-200 shadow-card text-brand-500">
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-7 w-7" />
        </div>
    </div>
    <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $description }}</p>
    @endif
    @if (trim($slot))
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
