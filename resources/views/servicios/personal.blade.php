<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Personal del servicio: {{ $servicio->nombre }}
            </h2>
            <a href="{{ route('servicios.show', $servicio) }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- Formulario para asignar personal --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-3">Asignar nuevo personal</h3>

            @if ($disponibles->isEmpty())
                <p class="text-sm text-gray-500">
                    No hay usuarios disponibles para asignar (todos los activos ya están en este servicio).
                </p>
            @else
                <form action="{{ route('servicios.personal.asignar', $servicio) }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    @csrf

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium">Usuario</label>
                        <select name="user_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">— Selecciona —</option>
                            @foreach ($disponibles as $u)
                                <option value="{{ $u->id }}">
                                    {{ $u->nombre_completo ?: $u->name }}
                                    ({{ $u->getRoleNames()->first() ?? 'sin rol' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Rol en servicio</label>
                        <input type="text" name="rol_en_servicio" placeholder="Ej. jefe, médico"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                            Asignar
                        </button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Lista actual de personal --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-3">
                Personal asignado ({{ $servicio->users->count() }})
            </h3>

            @if ($servicio->users->isEmpty())
                <p class="text-sm text-gray-500">Sin personal asignado todavía.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Nombre</th>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Rol del sistema</th>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Rol en servicio</th>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Desde</th>
                            <th class="px-3 py-2 text-right text-xs text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($servicio->users as $u)
                            <tr>
                                <td class="px-3 py-2">
                                    <div class="font-medium">{{ $u->nombre_completo ?: $u->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $u->email }}</div>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">
                                        {{ $u->getRoleNames()->first() ?? 'sin rol' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    {{ $u->pivot->rol_en_servicio ?? '—' }}
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-500">
                                    {{ $u->pivot->fecha_inicio ? \Carbon\Carbon::parse($u->pivot->fecha_inicio)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <form action="{{ route('servicios.personal.quitar', [$servicio, $u->pivot->id]) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('¿Quitar a este usuario del servicio?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline text-xs">
                                            Quitar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="pt-4">
            <a href="{{ route('servicios.show', $servicio) }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Volver al servicio</a>
        </div>
    </div>
</x-app-layout>
