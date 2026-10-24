{{-- Modal de Detalles de Servicio --}}
<div id="modal-show-servicio" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-show-titulo">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto border border-slate-100">

        <div class="flex justify-between items-start mb-6 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <div>
                    <h3 id="modal-show-titulo" class="font-extrabold text-slate-800 text-lg">
                        Detalles del Servicio
                    </h3>
                    <p class="text-xs text-slate-400">Información general y estado actual</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalShow()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-2" aria-label="Cerrar">&times;</button>
        </div>

        {{-- Contenido limpio inyectado --}}
        <div id="modal-show-content" class="space-y-4 text-sm text-slate-700">
            <!-- Se carga limpiamente vía AJAX -->
        </div>

        <div class="flex justify-end pt-6 mt-6 border-t border-slate-100">
            <button type="button" onclick="cerrarModalShow()"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 rounded-xl text-xs font-bold transition-all">
                Cerrar
            </button>
        </div>
    </div>
</div>