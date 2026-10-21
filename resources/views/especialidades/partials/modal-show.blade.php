<div id="modal-show" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal('modal-show')"></div>

        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div
                class="px-6 py-5 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white rounded-t-3xl z-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-lg" id="show-title">Detalle de la Especialidad
                        </h3>
                        <p class="text-[11px] text-slate-400" id="show-subtitle">Información completa</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-show')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="show-codigo"
                            class="px-2 py-1 bg-white border border-slate-200 text-slate-600 rounded-lg text-[11px] font-mono font-bold"></span>
                        <span id="show-grupo"
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide"></span>
                        <span id="show-estado"
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide"></span>
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        <span id="show-color" class="w-4 h-4 rounded-full shrink-0 hidden"></span>
                        <h4 id="show-nombre" class="text-2xl font-black text-slate-800"></h4>
                    </div>
                    <p id="show-descripcion" class="text-xs text-slate-600 mt-3 whitespace-pre-line leading-relaxed">
                    </p>

                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div class="bg-white border border-slate-100 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Duración por
                                defecto</p>
                            <p id="show-duracion" class="text-sm font-extrabold text-slate-800 mt-0.5"></p>
                        </div>
                        <div class="bg-white border border-slate-100 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total médicos</p>
                            <p id="show-total-medicos" class="text-sm font-extrabold text-indigo-600 mt-0.5"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100">
                        <h5 class="font-extrabold text-slate-800 text-sm">Médicos asignados</h5>
                        <p class="text-[11px] text-slate-400" id="show-medicos-count">0 médicos</p>
                    </div>
                    <ul id="show-medicos-list" class="divide-y divide-slate-100"></ul>
                </div>

                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100">
                        <h5 class="font-extrabold text-slate-800 text-sm">Servicios asociados</h5>
                        <p class="text-[11px] text-slate-400" id="show-servicios-count">0 servicios</p>
                    </div>
                    <div id="show-servicios-list" class="p-5 flex flex-wrap gap-2"></div>
                </div>

                <div class="flex flex-wrap gap-2 pt-4 border-t border-slate-100">
                    <button type="button" id="show-btn-editar"
                        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        Editar
                    </button>
                    <button type="button" id="show-btn-medicos"
                        class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        Gestionar médicos
                    </button>
                    <button type="button" id="show-btn-servicios"
                        class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        Gestionar servicios
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
