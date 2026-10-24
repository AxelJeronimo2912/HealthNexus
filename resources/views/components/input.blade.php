{{--
    Campo de formulario con etiqueta, ayuda y error.
    <x-input name="nombre" label="Nombre" :value="old('nombre')" required />
    <x-input name="buscar" icon="magnifying-glass" placeholder="Buscar…" />
--}}
@props(['name' => null, 'label' => null, 'hint' => null, 'icon' => null, 'type' => 'text'])

@php $id = $attributes->get('id', $name); $error = $name ? $errors->first($name) : null; @endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-[18px] w-[18px]" />
            </span>
        @endif
        <input type="{{ $type }}" @if ($name) name="{{ $name }}" @endif id="{{ $id }}"
            {{ $attributes->merge(['class' => 'block w-full rounded-xl border-0 bg-white py-2.5 text-sm text-slate-900 shadow-sm ring-1 ring-inset placeholder:text-slate-400 focus:ring-2 focus:ring-inset transition ' . ($icon ? 'pl-10 pr-3.5 ' : 'px-3.5 ') . ($error ? 'ring-red-300 focus:ring-red-500' : 'ring-slate-200 focus:ring-brand-500')]) }} />
    </div>
    @if ($error)
        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $error }}</p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
