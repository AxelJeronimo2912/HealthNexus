<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="'Hola, ' . \Illuminate\Support\Str::before(auth()->user()->name, ' ')"
            :subtitle="ucfirst(now()->translatedFormat('l d \d\e F \d\e Y'))" />
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card>
                <x-empty-state icon="squares-2x2" title="Bienvenido a HealthNexus"
                    description="Usa el menú lateral o presiona Ctrl + K para ir directamente a un módulo." />
            </x-card>
        </div>
    </div>
</x-app-layout>
