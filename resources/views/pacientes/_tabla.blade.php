<table class="w-full text-left border-collapse">
    <thead>
        <tr class="bg-slate-50/70 border-b border-slate-100">
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Paciente</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Contacto</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Edad</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Sexo</th>
            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 text-sm">
        @forelse ($pacientes as $paciente)
            <tr data-paciente class="hover:bg-slate-50/80 transition-colors group">
                <!-- Nombre + Avatar -->
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-600 font-bold flex items-center justify-center text-xs group-hover:bg-nexus-primary group-hover:text-nexus-accent transition-colors">
                            {{ strtoupper(substr($paciente->nombre_completo, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800">{{ $paciente->nombre_completo }}</p>
                            <span class="text-[11px] text-slate-400">ID: #{{ $paciente->id }}</span>
                        </div>
                    </div>
                </td>

                <!-- Correo y Teléfono -->
                <td class="px-6 py-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5 text-xs text-slate-600">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $paciente->correo_electronico ?? 'Sin correo' }}
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <!-- Acciones -->
                <td class="px-6 py-4 text-right">
                    <div class="inline-flex items-center gap-2">
                        <!-- Ver -->
                        <button type="button"
                            onclick="window.abrirModalPaciente && window.abrirModalPaciente('show', '{{ route('pacientes.show', $paciente) }}?partial=1')"
                            class="p-2 text-nexus-primary hover:bg-nexus-primary/10 rounded-2xl transition-all"
                            title="Ver detalles">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>

                        <!-- Editar -->
                        <button type="button"
                            onclick="window.abrirModalPaciente && window.abrirModalPaciente('edit', '{{ route('pacientes.edit', $paciente) }}?partial=1')"
                            class="p-2 text-nexus-secondary hover:bg-nexus-secondary/10 rounded-2xl transition-all"
                            title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        <!-- Eliminar -->
                        <button type="button"
                            onclick="window.abrirModalEliminarPaciente && window.abrirModalEliminarPaciente({{ $paciente->id }}, '{{ $paciente->nombre_completo }}', '{{ route('pacientes.destroy', $paciente) }}')"
                            class="p-2 text-rose-500 hover:bg-rose-50 rounded-2xl transition-all" title="Eliminar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                        </div>
                        <p class="text-slate-500 font-medium text-sm">
                            @if (!empty($busqueda))
                                Sin resultados para "<span
                                    class="font-semibold text-slate-700">{{ $busqueda }}</span>"
                            @else
                                Sin pacientes registrados
                            @endif
                        </p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if ($pacientes->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $pacientes->appends(['q' => $busqueda])->links() }}
    </div>
@endif
