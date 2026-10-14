{{-- Modal de Confirmación de Eliminación --}}
<div id="modal-delete-servicio" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-delete-titulo">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-8 border border-slate-100">

        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div>
                <h3 id="modal-delete-titulo" class="font-extrabold text-slate-800 text-lg">
                    Eliminar Servicio
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    ¿Estás seguro de que deseas eliminar el servicio <strong id="delete-nombre-servicio" class="text-slate-800"></strong>? Esta acción no se puede deshacer.
                </p>
            </div>
        </div>

        <form id="form-delete-servicio" method="POST" class="flex justify-end gap-2 pt-4 border-t border-slate-100">
            @csrf
            @method('DELETE')
            <button type="button" onclick="cerrarModalDelete()"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                Cancelar
            </button>
            <button type="submit"
                class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                Sí, eliminar
            </button>
        </form>
        <button type="button" onclick="cerrarModalShow()"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 rounded-xl text-xs font-bold transition-all">
                Cerrar
            </button>
    </div>
</div>