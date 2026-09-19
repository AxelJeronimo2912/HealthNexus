@if (request('partial'))
    {{-- ============ MODO MODAL ============ --}}
    @include('pacientes._form', ['paciente' => null, 'estados' => $estados, 'municipios' => $municipios])
    @include('pacientes._scripts')
@else
    {{-- ============ MODO PÁGINA COMPLETA ============ --}}
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800">Nuevo Paciente</h2>
        </x-slot>

        <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                @include('pacientes._form', [
                    'paciente' => null,
                    'estados' => $estados,
                    'municipios' => $municipios,
                ])
            </div>
        </div>

        @include('pacientes._scripts')
    </x-app-layout>
@endif
