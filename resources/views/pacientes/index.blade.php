

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center border border-nexus-primary/20 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Directorio de Pacientes
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Control de expedientes clínicos y datos de
                        contacto de HealthNexus</p>
                </div>
            </div>

            <!-- Botón Crear Paciente -->
            <button @click="$dispatch('open-modal', 'modal-crear-paciente')"
                class="group relative inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-nexus-accent bg-nexus-primary rounded-2xl shadow-md hover:shadow-lg hover:bg-slate-900 transition-all duration-200">
                <svg class="w-5 h-5 me-2 group-hover:scale-110 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Nuevo Paciente</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen" x-data="{
        pacienteSeleccionado: null,
        setPaciente(p) { this.pacienteSeleccionado = p; },
        busqueda: '',
        coincide(texto) {
            if (!this.busqueda) return true;
            const q = this.busqueda.toLowerCase().trim();
            return (texto || '').toLowerCase().includes(q);
        },
        hayResultados() {
            const filas = [...$el.querySelectorAll('tbody tr[data-paciente]')];
            return filas.some(r => r.style.display !== 'none');
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- TARJETAS DE MÉTRICAS RÁPIDAS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pacientes</p>
                        <h4 class="text-xl font-bold text-slate-800">{{ $pacientes->total() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-nexus-secondary/10 text-nexus-secondary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Estado Sistema</p>
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activo
                        </span>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Registros Hoy</p>
                        <h4 class="text-xl font-bold text-slate-800">Actualizado</h4>
                    </div>
                </div>
            </div>

            <!-- ALERTA DE ÉXITO -->
            @if (session('success'))
                <div
                    class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-3 shadow-sm">
                    <div
                        class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- BUSCADOR EN TIEMPO REAL (sin recargar) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-4 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                </div>

                <input type="text" x-model="busqueda" autocomplete="off"
                    placeholder="Buscar por nombre, correo, teléfono o ID..."
                    class="w-full border-0 bg-transparent text-sm focus:ring-0 placeholder:text-slate-400">

                <button x-show="busqueda" @click="busqueda = ''" type="button"
                    class="text-xs font-bold text-slate-400 hover:text-slate-600 px-2 whitespace-nowrap">
                    Limpiar
                </button>
            </div>

            <!-- TABLA DE PACIENTES -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Paciente
                                </th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Contacto
                                </th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Edad
                                </th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Sexo
                                </th>
                                <th
                                    class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($pacientes as $paciente)
                                <tr data-paciente class="hover:bg-slate-50/80 transition-colors group"
                                    x-show="coincide('{{ $paciente->nombre_completo }} {{ $paciente->correo_electronico }} {{ $paciente->telefono_principal }} {{ $paciente->id }}')">
                                    <!-- Nombre + Avatar -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-600 font-bold flex items-center justify-center text-xs group-hover:bg-nexus-primary group-hover:text-nexus-accent transition-colors">
                                                {{ strtoupper(substr($paciente->nombre_completo, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">{{ $paciente->nombre_completo }}
                                                </p>
                                                <span class="text-[11px] text-slate-400">ID:
                                                    #{{ $paciente->id }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Correo y Teléfono -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $paciente->correo_electronico ?? 'Sin correo' }}
                                            </div>
                                            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                {{ $paciente->telefono_principal ?? 'Sin teléfono' }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Edad -->
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $paciente->edad }} años
                                        </span>
                                    </td>

                                    <!-- Sexo -->
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ strtolower($paciente->sexo) === 'hombre' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                            {{ ucfirst($paciente->sexo) }}
                                        </span>
                                    </td>

                                    <!-- Botones de Acción con Íconos -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <!-- Botón Ver -->
                                            <button
                                                @click="setPaciente({{ json_encode($paciente) }}); $dispatch('open-modal', 'modal-ver-paciente')"
                                                title="Ver detalles"
                                                class="p-2 text-nexus-primary hover:bg-nexus-primary/10 rounded-2xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- Botón Editar -->
                                            <button
                                                @click="setPaciente({{ json_encode($paciente) }}); $dispatch('open-modal', 'modal-editar-paciente')"
                                                title="Editar"
                                                class="p-2 text-nexus-secondary hover:bg-nexus-secondary/10 rounded-2xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <!-- Botón Eliminar -->
                                            <button
                                                @click="setPaciente({{ json_encode($paciente) }}); $dispatch('open-modal', 'modal-eliminar-paciente')"
                                                title="Eliminar"
                                                class="p-2 text-rose-500 hover:bg-rose-50 rounded-2xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="max-w-xs mx-auto text-center space-y-3">
                                            <div
                                                class="w-12 h-12 bg-slate-100 rounded-2xl text-slate-400 flex items-center justify-center mx-auto">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <p class="text-slate-500 font-medium text-sm">Sin pacientes registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Mensaje cuando el buscador no encuentra nada -->
                            <tr x-show="busqueda && !hayResultados()">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="max-w-xs mx-auto text-center space-y-3">
                                        <div
                                            class="w-12 h-12 bg-slate-100 rounded-2xl text-slate-400 flex items-center justify-center mx-auto">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-medium text-sm">
                                            Sin resultados para "<span class="font-semibold text-slate-700"
                                                x-text="busqueda"></span>"
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if ($pacientes->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $pacientes->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- ================= MODAL: CREAR PACIENTE ================= -->
        <x-modal name="modal-crear-paciente" focusable>
            <form method="POST" action="{{ route('pacientes.store') }}" class="p-6 sm:p-8">
                @csrf
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-10 h-10 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Registrar Nuevo Paciente</h3>
                        <p class="text-xs text-slate-400">Completa los datos esenciales del paciente</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Nombre Completo</label>
                        <input type="text" name="nombre_completo" required
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-primary focus:ring-nexus-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Correo Electrónico</label>
                        <input type="email" name="correo_electronico"
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-primary focus:ring-nexus-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Teléfono</label>
                        <input type="text" name="telefono_principal"
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-primary focus:ring-nexus-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Edad</label>
                        <input type="number" name="edad" required
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-primary focus:ring-nexus-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Sexo</label>
                        <select name="sexo" required
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-primary focus:ring-nexus-primary">
                            <option value="hombre">Hombre</option>
                            <option value="mujer">Mujer</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close')"
                        class="px-5 py-2.5 text-xs font-bold text-slate-500 rounded-2xl hover:bg-slate-100 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 text-xs font-bold text-nexus-accent bg-nexus-primary rounded-2xl hover:bg-slate-900 shadow-md">
                        Guardar Paciente
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- ================= MODAL: VER PACIENTE ================= -->
        <x-modal name="modal-ver-paciente" focusable>
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-3xl bg-nexus-primary text-nexus-accent flex items-center justify-center font-extrabold text-lg shadow-md"
                        x-text="pacienteSeleccionado?.nombre_completo ? pacienteSeleccionado.nombre_completo.substring(0,2).toUpperCase() : ''">
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800" x-text="pacienteSeleccionado?.nombre_completo">
                        </h3>
                        <p class="text-xs text-nexus-secondary font-semibold">Expediente Clínico Activo</p>
                    </div>
                </div>

                <div class="space-y-3 text-sm bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-6">
                    <div class="flex justify-between items-center"><span
                            class="text-xs text-slate-400 font-medium">Correo Electrónico:</span> <span
                            class="font-semibold text-slate-700"
                            x-text="pacienteSeleccionado?.correo_electronico || '—'"></span></div>
                    <div class="flex justify-between items-center"><span
                            class="text-xs text-slate-400 font-medium">Teléfono:</span> <span
                            class="font-semibold text-slate-700"
                            x-text="pacienteSeleccionado?.telefono_principal || '—'"></span></div>
                    <div class="flex justify-between items-center"><span
                            class="text-xs text-slate-400 font-medium">Edad:</span> <span
                            class="font-semibold text-slate-700"
                            x-text="(pacienteSeleccionado?.edad || 0) + ' años'"></span></div>
                    <div class="flex justify-between items-center"><span
                            class="text-xs text-slate-400 font-medium">Sexo:</span> <span
                            class="font-semibold text-slate-700 capitalize"
                            x-text="pacienteSeleccionado?.sexo"></span></div>
                </div>

                <div class="flex justify-end">
                    <button type="button" @click="$dispatch('close')"
                        class="px-6 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 rounded-2xl hover:bg-slate-200 transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </x-modal>

        <!-- ================= MODAL: EDITAR PACIENTE ================= -->
        <x-modal name="modal-editar-paciente" focusable>
            <form method="POST" :action="`/pacientes/${pacienteSeleccionado?.id}`" class="p-6 sm:p-8">
                @csrf
                @method('PUT')
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-10 h-10 rounded-2xl bg-nexus-secondary/10 text-nexus-secondary flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Editar Información</h3>
                        <p class="text-xs text-slate-400">Actualiza los datos del expediente</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Nombre Completo</label>
                        <input type="text" name="nombre_completo" :value="pacienteSeleccionado?.nombre_completo"
                            required
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-secondary focus:ring-nexus-secondary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Correo Electrónico</label>
                        <input type="email" name="correo_electronico"
                            :value="pacienteSeleccionado?.correo_electronico"
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-secondary focus:ring-nexus-secondary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Teléfono</label>
                        <input type="text" name="telefono_principal"
                            :value="pacienteSeleccionado?.telefono_principal"
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-secondary focus:ring-nexus-secondary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Edad</label>
                        <input type="number" name="edad" :value="pacienteSeleccionado?.edad" required
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-secondary focus:ring-nexus-secondary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Sexo</label>
                        <select name="sexo" :value="pacienteSeleccionado?.sexo" required
                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-nexus-secondary focus:ring-nexus-secondary">
                            <option value="hombre">Hombre</option>
                            <option value="mujer">Mujer</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close')"
                        class="px-5 py-2.5 text-xs font-bold text-slate-500 rounded-2xl hover:bg-slate-100 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 text-xs font-bold text-white bg-nexus-secondary rounded-2xl hover:opacity-90 shadow-md">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- ================= MODAL: ELIMINAR PACIENTE ================= -->
        <x-modal name="modal-eliminar-paciente" focusable>
            <form method="POST" :action="`/pacientes/${pacienteSeleccionado?.id}`" class="p-6 sm:p-8">
                @csrf
                @method('DELETE')

                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-slate-800">¿Confirmas la eliminación?</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Estás a punto de eliminar a <strong class="text-slate-800"
                        x-text="pacienteSeleccionado?.nombre_completo"></strong>. Esta acción eliminará su registro
                    permanentemente.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close')"
                        class="px-5 py-2.5 text-xs font-bold text-slate-500 rounded-2xl hover:bg-slate-100 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 text-xs font-bold text-white bg-rose-600 rounded-2xl hover:bg-rose-700 shadow-md">
                        Eliminar
                    </button>
                </div>
            </form>
        </x-modal>

    </div>
</x-app-layout>
