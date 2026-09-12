<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Pacientes</h2>
            <a href="{{ route('pacientes.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nuevo Paciente
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Edad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sexo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pacientes as $paciente)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $paciente->nombre_completo }}</td>
                            <td class="px-4 py-3 text-sm">{{ $paciente->correo_electronico ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $paciente->edad }} años</td>
                            <td class="px-4 py-3 text-sm">{{ ucfirst($paciente->sexo) }}</td>
                            <td class="px-4 py-3 text-sm">{{ $paciente->telefono_principal ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('pacientes.show', $paciente) }}"
                                    class="text-gray-600 hover:underline">Ver</a>
                                <a href="{{ route('pacientes.edit', $paciente) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" class="inline"
                                    onsubmit="return confirm('¿Eliminar este paciente?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">Sin pacientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $pacientes->links() }}</div>
    </div>
</x-app-layout>
