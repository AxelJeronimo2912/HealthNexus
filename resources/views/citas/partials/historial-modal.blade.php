<div class="p-6 bg-white rounded-2xl">
    {{-- Encabezado y Botón Cerrar --}}
    <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Historial de Citas</h3>
            <p class="text-xs text-slate-500 mt-0.5">Consulta y actualiza el estado de la agenda</p>
        </div>
        <button type="button" onclick="cerrarModalHistorial()"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors text-xl leading-none border border-slate-200">&times;</button>
    </div>

    {{-- Filtros en Tiempo Real --}}
    <div class="flex flex-wrap items-end gap-3 mb-5 bg-slate-50 p-4 rounded-2xl border border-slate-100">
        <div class="flex-1 min-w-[220px] relative">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Buscar Paciente</label>
            <input type="text" id="filtro-buscar" name="buscar" value="{{ request('buscar') }}"
                placeholder="Nombre del paciente..."
                class="w-full border-slate-200 rounded-xl text-sm focus:ring-brand-500 focus:border-brand-500 py-2.5 pl-10 pr-3 shadow-sm">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 bottom-3.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <div class="w-full sm:w-[200px]">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Estado</label>
            <select id="filtro-estado" name="estado"
                class="w-full border-slate-200 rounded-xl text-sm focus:ring-brand-500 focus:border-brand-500 py-2.5 pl-3 pr-8 shadow-sm">
                <option value="">Todos los estados</option>
                <option value="confirmada" @selected(request('estado') == 'confirmada')>Confirmada</option>
                <option value="atendida" @selected(request('estado') == 'atendida')>Atendida</option>
                <option value="cancelada" @selected(request('estado') == 'cancelada')>Cancelada</option>
                <option value="no_asistio" @selected(request('estado') == 'no_asistio')>No asistió</option>
            </select>
        </div>
        <div class="w-full sm:w-auto {{ request('buscar') || request('estado') ? '' : 'hidden' }}"
            id="contenedor-btn-limpiar">
            <button type="button" onclick="limpiarFiltrosHistorial()"
                class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-100 transition-colors flex items-center justify-center gap-2 shadow-sm h-[42px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                Limpiar
            </button>
        </div>
    </div>

    {{-- Contenedor con ID para inyectar los resultados --}}
    <div id="resultados-historial" class="transition-opacity duration-200">
        @include('citas.partials.tabla-historial', ['historial' => $historial])
    </div>
</div>
