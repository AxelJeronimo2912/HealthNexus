<div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 space-y-3">
    <form id="filters-form" method="GET" action="{{ route('especialidades.index') }}"
        class="flex gap-2 flex-wrap items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="{{ $labelCls }}">Buscar</label>
            <div class="relative">
                <input type="text" name="buscar" id="filter-buscar" autocomplete="off" value="{{ $busqueda }}"
                    placeholder="Nombre o código..." class="{{ $inputCls }} pl-9 pr-9">

                {{-- Icono de búsqueda --}}
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                {{-- Spinner de carga --}}
                <span id="filter-spinner"
                    class="hidden absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></span>

                {{-- Botón limpiar input --}}
                <button type="button" id="filter-clear-buscar"
                    class="{{ $busqueda ? '' : 'hidden' }} absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-400 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="min-w-[180px]">
            <label class="{{ $labelCls }}">Grupo</label>
            <select name="grupo" id="filter-grupo" class="{{ $inputCls }}">
                <option value="">Todos los grupos</option>
                <option value="clinica" @selected($grupo === 'clinica')>Clínica</option>
                <option value="quirurgica" @selected($grupo === 'quirurgica')>Quirúrgica</option>
                <option value="diagnostica" @selected($grupo === 'diagnostica')>Diagnóstica</option>
                <option value="basica" @selected($grupo === 'basica')>Básica</option>
                <option value="otra" @selected($grupo === 'otra')>Otra</option>
            </select>
        </div>

        <button type="button" id="filter-limpiar-todo"
            class="{{ $busqueda || $grupo ? '' : 'hidden' }} px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Limpiar
        </button>
    </form>

    {{-- Chips de filtros activos --}}
    <div id="filter-chips" class="flex flex-wrap items-center gap-2 {{ $busqueda || $grupo ? '' : 'hidden' }}">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Filtros:</span>
        <span id="chip-buscar"
            class="{{ $busqueda ? '' : 'hidden' }} inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg text-[11px] font-bold">
            <span>Búsqueda: <span id="chip-buscar-value" class="font-mono">"{{ $busqueda }}"</span></span>
            <button type="button" data-chip-clear="buscar" class="hover:text-indigo-900">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </span>
        <span id="chip-grupo"
            class="{{ $grupo ? '' : 'hidden' }} inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-100 rounded-lg text-[11px] font-bold">
            <span>Grupo: <span id="chip-grupo-value">{{ ucfirst($grupo) }}</span></span>
            <button type="button" data-chip-clear="grupo" class="hover:text-purple-900">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </span>
    </div>
</div>
