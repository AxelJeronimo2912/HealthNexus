<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Turnos
                </h2>
                <p class="text-xs text-gray-500 mt-1">Gestiona los horarios operativos y la asignación de personal.</p>
            </div>
            
            <button x-data @click="$dispatch('open-modal', 'modal-crear-turno')" 
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-all gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo Turno
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stat Cards Estilo Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Total Turnos</span>
                        <span class="text-xl font-bold text-gray-800">{{ $turnos->count() }}</span>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Estado Sistema</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Activo
                        </span>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Registros hoy</span>
                        <span class="text-xl font-bold text-gray-800">Actualizado</span>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm border border-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($turnos as $turno)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between overflow-hidden">
                        
                        <div class="p-5 border-b border-gray-100">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-xs font-mono font-bold tracking-wider px-2 py-0.5 rounded bg-gray-100 text-gray-700 uppercase">
                                        {{ $turno->codigo }}
                                    </span>
                                    <h3 class="text-lg font-bold text-gray-900 mt-2">
                                        {{ $turno->nombre }}
                                    </h3>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $turno->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $turno->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5 space-y-4 flex-1">
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50/50 border border-gray-100 text-sm">
                                <div>
                                    <span class="block text-[10px] uppercase font-bold text-gray-400">Entrada</span>
                                    <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i A') }}</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                <div>
                                    <span class="block text-[10px] uppercase font-bold text-gray-400">Salida</span>
                                    <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i A') }}</span>
                                </div>
                            </div>

                            @if($turno->descripcion)
                                <p class="text-xs text-gray-600 line-clamp-2">{{ $turno->descripcion }}</p>
                            @endif
                        </div>

                        <!-- Acciones con Iconos SVG Limpios -->
                        <div class="px-5 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button x-data @click="$dispatch('open-modal', 'modal-ver-turno-{{ $turno->id }}')" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Ver Detalle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button x-data @click="$dispatch('open-modal', 'modal-editar-turno-{{ $turno->id }}')" class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button x-data @click="$dispatch('open-modal', 'modal-eliminar-turno-{{ $turno->id }}')" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Ver Detalle Completo -->
                    <x-modal name="modal-ver-turno-{{ $turno->id }}" focusable>
                        <div class="p-6 bg-white text-gray-900 space-y-4">
                            <div class="flex justify-between items-start border-b pb-3">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $turno->nombre }}</h3>
                                    <p class="text-xs font-mono text-gray-500 font-semibold">{{ $turno->codigo }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $turno->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $turno->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>

                            <dl class="grid grid-cols-2 gap-4 text-sm py-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <div>
                                    <dt class="text-xs font-bold text-gray-400 uppercase">Horario Rango</dt>
                                    <dd class="font-semibold text-gray-800 mt-0.5">
                                        {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold text-gray-400 uppercase">Duración Total</dt>
                                    <dd class="font-semibold text-gray-800 mt-0.5">{{ $turno->duracion_horas ?? '8.0' }} Horas</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold text-gray-400 uppercase">Cruza Medianoche</dt>
                                    <dd class="font-semibold text-gray-800 mt-0.5">{{ isset($turno->cruza_medianoche) ? ($turno->cruza_medianoche ? 'Sí' : 'No') : 'No' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold text-gray-400 uppercase">Descripción</dt>
                                    <dd class="font-semibold text-gray-800 mt-0.5">{{ $turno->descripcion ?? 'Sin descripción' }}</dd>
                                </div>
                            </dl>

                            <!-- Lista de Usuarios Asignados (Formato Tabla/Badge estilo Pacientes) -->
                            <div class="pt-3 border-t">
                                <h4 class="font-bold text-sm text-gray-800 mb-2">
                                    Usuarios asignados ({{ $turno->users ? $turno->users->count() : 0 }})
                                </h4>
                                <div class="max-h-40 overflow-y-auto space-y-1 pr-1">
                                    @forelse ($turno->users ?? [] as $u)
                                        <div class="text-xs py-2 px-3 bg-gray-50 rounded-lg border border-gray-100 flex justify-between items-center">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-[10px]">
                                                    {{ strtoupper(substr($u->name ?? 'U', 0, 2)) }}
                                                </div>
                                                <span class="font-medium text-gray-800">{{ $u->nombre_completo ?: $u->name }}</span>
                                            </div>
                                            <span class="text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full font-medium text-[11px]">
                                                {{ method_exists($u, 'getRoleNames') ? ($u->getRoleNames()->first() ?? 'Sin Rol') : 'Sin Rol' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-400 italic py-2">Sin usuarios asignados a este turno.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-2 pt-2 border-t">
                                <button x-data @click="$dispatch('close'); $dispatch('open-modal', 'modal-editar-turno-{{ $turno->id }}')" class="px-4 py-2 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
                                    Editar
                                </button>
                                <x-secondary-button x-on:click="$dispatch('close')">Cerrar</x-secondary-button>
                            </div>
                        </div>
                    </x-modal>

                    <!-- Modal Editar -->
                    <x-modal name="modal-editar-turno-{{ $turno->id }}" focusable>
                        <form method="POST" action="{{ route('admin.turnos.update', $turno) }}" class="p-6 bg-white text-gray-900">
                            @csrf
                            @method('PUT')
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Editar Turno: {{ $turno->nombre }}</h2>
                            @include('turnos._form', ['turno' => $turno])
                            <div class="mt-6 flex justify-end gap-3 pt-3 border-t">
                                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                                <x-primary-button>Actualizar Turno</x-primary-button>
                            </div>
                        </form>
                    </x-modal>

                    <!-- Modal Eliminar -->
                    <x-modal name="modal-eliminar-turno-{{ $turno->id }}" focusable>
                        <form method="POST" action="{{ route('admin.turnos.destroy', $turno) }}" class="p-6 bg-white text-gray-900">
                            @csrf
                            @method('DELETE')
                            <h2 class="text-lg font-bold text-gray-900">¿Eliminar turno?</h2>
                            <p class="text-sm text-gray-500 mt-2">Esta acción no se puede deshacer.</p>
                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                                <x-danger-button>Eliminar</x-danger-button>
                            </div>
                        </form>
                    </x-modal>

                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">Sin turnos registrados.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Crear -->
    <x-modal name="modal-crear-turno" focusable>
        <form method="POST" action="{{ route('admin.turnos.store') }}" class="p-6 bg-white text-gray-900">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 mb-4">Crear Nuevo Turno</h2>
            @include('turnos._form', ['turno' => null])
            <div class="mt-6 flex justify-end gap-3 pt-3 border-t">
                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                <x-primary-button>Guardar Turno</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>