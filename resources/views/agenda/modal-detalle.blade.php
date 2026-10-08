{{-- MODAL DE DETALLE --}}
<div x-show="modalDetalle" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;">

    <div x-show="modalDetalle" @click.away="modalDetalle = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 space-y-5 border border-slate-100">

        <div class="flex justify-between items-start">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm shrink-0"
                    x-text="citaDetalle.iniciales"></div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-lg leading-tight" x-text="citaDetalle.paciente"></h3>
                    <p class="text-xs text-slate-400 font-medium" x-text="'ID: #' + citaDetalle.paciente_id"></p>
                </div>
            </div>
            <button type="button" @click="modalDetalle = false"
                class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="space-y-3 bg-slate-50/70 p-4 rounded-2xl border border-slate-100 text-xs">
            <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Fecha</span>
                <span class="font-extrabold text-slate-800 capitalize" x-text="citaDetalle.fecha"></span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Hora</span>
                <span class="font-extrabold text-slate-800" x-text="citaDetalle.hora + ' hrs'"></span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Médico</span>
                <span class="font-bold text-slate-700" x-text="'Dr. ' + citaDetalle.medico"></span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Especialidad</span>
                <span class="font-bold text-slate-700" x-text="citaDetalle.especialidad"></span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Triage</span>
                <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100"
                    x-text="citaDetalle.triage"></span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Estado</span>
                <span class="font-bold text-slate-700" x-text="citaDetalle.estado"></span>
            </div>
            <div class="pt-2 border-t border-slate-200/60">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Motivo</span>
                <p class="text-slate-700 italic bg-white p-2.5 rounded-xl border border-slate-100"
                    x-text="citaDetalle.motivo"></p>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" @click="modalDetalle = false"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold">
                Cerrar
            </button>
        </div>
    </div>
</div>
