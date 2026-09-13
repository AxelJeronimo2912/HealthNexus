<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Nueva Consulta — {{ $cita->paciente->nombre_completo }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('consultas.store', $cita) }}" method="POST"
            class="space-y-8 bg-white p-6 rounded-lg shadow" id="form-consulta">
            @csrf

            @include('consultas._form', [
                'consulta' => null,
                'cita' => $cita,
                'paciente' => $cita->paciente,
                'signo' => $signo,
                'medicamentos' => $medicamentos,
            ])

            <div class="flex justify-between items-center pt-4 border-t">
                <a href="{{ route('citas.show', $cita) }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <div class="space-x-2">
                    <button type="submit" name="finalizar" value="0"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                        Guardar borrador
                    </button>
                    <button type="submit" name="finalizar" value="1"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        Finalizar consulta
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
