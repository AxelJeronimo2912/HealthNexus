<div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h3 class="font-extrabold text-slate-800 text-base">Catálogo de especialidades</h3>
            <p class="text-xs text-slate-400">
                Mostrando
                <span id="table-from" class="font-bold text-slate-600">{{ $especialidades->firstItem() ?? 0 }}</span>
                –
                <span id="table-to" class="font-bold text-slate-600">{{ $especialidades->lastItem() ?? 0 }}</span>
                de
                <span id="table-total" class="font-bold text-slate-600">{{ $especialidades->total() }}</span>
                registros
            </p>
        </div>

        {{-- Indicador de "actualizando" --}}
        <span id="table-loading"
            class="hidden inline-flex items-center gap-1.5 px-2.5 py-1 bg-brand-50 text-brand-600 border border-brand-100 rounded-full text-xs font-bold">
            <span class="w-3 h-3 border-2 border-brand-500 border-t-transparent rounded-full animate-spin"></span>
            Actualizando…
        </span>
    </div>

    <div id="table-container" class="overflow-x-auto relative">
        {{-- Overlay sutil mientras carga --}}
        <div id="table-overlay"
            class="hidden absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 transition-opacity duration-150"></div>

        <table class="min-w-full">
            <thead class="bg-slate-50/70 border-b border-slate-100">
                <tr>
                    <th class="{{ $thCls }} text-left">Código</th>
                    <th class="{{ $thCls }} text-left">Nombre</th>
                    <th class="{{ $thCls }} text-left">Grupo</th>
                    <th class="{{ $thCls }} text-center">Duración</th>
                    <th class="{{ $thCls }} text-center">Médicos</th>
                    <th class="{{ $thCls }} text-center">Servicios</th>
                    <th class="{{ $thCls }} text-center">Consultas</th>
                    <th class="{{ $thCls }} text-left">Estado</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody id="table-rows" class="divide-y divide-slate-100">
                @include('especialidades.partials._table-rows', ['especialidades' => $especialidades])
            </tbody>
        </table>
    </div>
</div>

{{-- Paginador --}}
<div id="table-pagination" class="mt-4">
    {{ $especialidades->links() }}
</div>
