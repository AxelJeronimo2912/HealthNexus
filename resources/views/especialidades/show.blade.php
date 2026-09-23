<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Especialidad: {{ $especialidad->nombre }}
            </h2>
            <a href="{{ route('especialidades.index') }}" class="text-sm text-gray-600 hover:underline">← Volver</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Encabezado --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2">
                        @if ($especialidad->color)
                            <span class="w-4 h-4 rounded-full"
                                style="background-color: {{ $especialidad->color_hex }}"></span>
                        @endif
                        <p class="text-xs text-gray-500 font-mono">{{ $especialidad->codigo }}</p>
                    </div>
                    <p class="text-2xl font-bold mt-1">{{ $especialidad->nombre }}</p>
                    <span class="inline-block mt-2 px-2 py-1 rounded text-xs {{ $especialidad->grupo_color }}">
                        {{ $especialidad->grupo_label }}
                    </span>
                </div>
                <div class="text-right">
                    @if ($especialidad->activo)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm">Activa</span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded text-sm">Inactiva</span>
                    @endif
                    <p class="text-xs text-gray-500 mt-2">
                        Duración por defecto: {{ $especialidad->duracion_consulta_default }} min
                    </p>
                </div>
            </div>

            @if ($especialidad->descripcion)
                <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $especialidad->descripcion }}</p>
            @endif
        </div>

        {{-- Estadísticas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Médicos</p>
                <p class="text-2xl font-bold text-blue-600">{{ $especialidad->medicos->count() }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Servicios</p>
                <p class="text-2xl font-bold text-purple-600">{{ $especialidad->servicios->count() }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Citas</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['citas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Consultas</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['consultas'] }}</p>
            </div>
        </div>

        {{-- Médicos --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-gray-800">
                    Médicos asignados ({{ $especialidad->medicos->count() }})
                </h3>
                <a href="{{ route('especialidades.medicos', $especialidad) }}"
                    class="text-xs text-blue-600 hover:underline">Gestionar médicos →</a>
            </div>

            @if ($especialidad->medicos->isEmpty())
                <p class="text-sm text-gray-500">Sin médicos asignados.</p>
            @else
                <ul class="divide-y">
                    @foreach ($especialidad->medicos as $m)
                        <li class="py-2 flex justify-between items-center text-sm">
                            <div>
                                <span class="font-medium">{{ $m->nombre_completo ?: $m->name }}</span>
                                @if ($m->pivot->es_principal)
                                    <span class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs">
                                        Principal
                                    </span>
                                @endif
                                @if ($m->pivot->numero_cedula_especialidad)
                                    <span class="ml-2 text-xs text-gray-500">
                                        Cédula: {{ $m->pivot->numero_cedula_especialidad }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $m->getRoleNames()->first() ?? 'sin rol' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Servicios --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-gray-800">
                    Servicios asociados ({{ $especialidad->servicios->count() }})
                </h3>
                <a href="{{ route('especialidades.servicios', $especialidad) }}"
                    class="text-xs text-blue-600 hover:underline">Gestionar servicios →</a>
            </div>

            @if ($especialidad->servicios->isEmpty())
                <p class="text-sm text-gray-500">Sin servicios asociados.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach ($especialidad->servicios as $s)
                        <a href="{{ route('servicios.show', $s) }}"
                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                            {{ $s->nombre }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="pt-4 flex space-x-2">
            <a href="{{ route('especialidades.edit', $especialidad) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Editar</a>
            <a href="{{ route('especialidades.medicos', $especialidad) }}"
                class="px-4 py-2 bg-purple-600 text-white rounded-md text-sm">Gestionar médicos</a>
            <a href="{{ route('especialidades.servicios', $especialidad) }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">Gestionar servicios</a>
            <a href="{{ route('especialidades.index') }}" class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
        </div>
    </div>
</x-app-layout>
