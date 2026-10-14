{{-- Modal de Gestión de Personal --}}
<div id="modal-personal-servicio" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-personal-titulo">
    <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto border border-slate-100">

        <div class="flex justify-between items-start mb-6 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h3 id="modal-personal-titulo" class="font-extrabold text-slate-800 text-lg">
                        Gestión de Personal
                    </h3>
                    <p class="text-[11px] text-slate-400">Asigna o retira personal médico y administrativo</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalPersonal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-2" aria-label="Cerrar">&times;</button>
        </div>

        {{-- Contenido inyectado dinámicamente vía AJAX --}}
        <div id="modal-personal-content" class="space-y-6 text-sm">
            <!-- Se carga de forma dinámica -->
        </div>

        <div class="flex justify-end pt-6 mt-6 border-t border-slate-100">
            <button type="button" onclick="cerrarModalPersonal()"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 rounded-xl text-xs font-bold transition-all">
                Cerrar
            </button>
        </div>
    </div>
</div>