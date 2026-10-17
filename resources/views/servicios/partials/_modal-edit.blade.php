{{-- Modal de Edición de Servicio --}}
<div id="modal-edit-servicio"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">

    {{-- Fondo difuminado (Backdrop) --}}
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="cerrarModalEdit()"></div>

    {{-- Contenedor del Modal con Animación --}}
    <div
        class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl border border-slate-100/50 flex flex-col max-h-full transform transition-all animate-in zoom-in-95 duration-200">

        {{-- Header Fijo --}}
        <div
            class="flex justify-between items-center px-6 sm:px-8 py-5 border-b border-slate-100 bg-slate-50/50 rounded-t-3xl shrink-0">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-indigo-100/50 text-indigo-600 flex items-center justify-center shadow-sm shrink-0 border border-indigo-200/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xl tracking-tight">Editar Servicio</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Modifica los datos y la configuración del área
                    </p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalEdit()"
                class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 hover:border-rose-100 flex items-center justify-center transition-all duration-200 shadow-sm"
                aria-label="Cerrar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Contenido inyectado dinámicamente vía AJAX --}}
        <div id="modal-edit-content" class="p-6 sm:p-8 overflow-y-auto custom-scrollbar">
            {{-- Skeleton loader mientras carga (opcional) --}}
            <div class="animate-pulse space-y-4">
                <div class="h-10 bg-slate-100 rounded-xl w-full"></div>
                <div class="h-10 bg-slate-100 rounded-xl w-3/4"></div>
                <div class="h-10 bg-slate-100 rounded-xl w-full"></div>
            </div>
        </div>


    </div>
</div>
