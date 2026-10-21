{{-- MODAL ELIMINAR --}}
<template x-teleport="body">
    <div x-show="openDelete" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div @click.away="openDelete = false"
            class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-center">

            <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 mx-auto">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <div>
                <h3 class="text-base font-extrabold text-slate-800">¿Eliminar medicamento?</h3>
                <p class="text-xs text-slate-500 mt-1">Esta acción no se puede deshacer.</p>
            </div>

            <form action="{{ route('medicamentos.destroy', $med) }}" method="POST"
                class="flex justify-center gap-2 pt-4 border-t border-slate-100">
                @csrf
                @method('DELETE')
                <button @click="openDelete = false" type="button"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Sí, eliminar
                </button>
            </form>
        </div>
    </div>
</template>

