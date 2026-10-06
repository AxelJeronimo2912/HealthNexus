<div x-show="modalDetalle" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
    style="display: none;">
    <div x-show="modalDetalle" @click.away="modalDetalle = false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="scale-90 translate-y-4 opacity-0"
        x-transition:enter-end="scale-100 translate-y-0 opacity-100"
        class="w-full max-w-md space-y-5 rounded-3xl border border-slate-100 bg-white p-6 shadow-xl">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700"
                    x-text="citaDetalle.iniciales"></div>
                <div>
                    <h3 class="text-lg font-extrabold leading-tight text-slate-800" x-text="citaDetalle.paciente"></h3>
                    <p class="text-xs font-medium text-slate-400" x-text="'ID: #' + citaDetalle.paciente_id"></p>
                </div>
            </div>
            <button type="button" @click="modalDetalle = false"
                class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="space-y-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-4 text-xs">
            <div class="flex items-center justify-between border-b border-slate-200/50 py-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Fecha</span>
                <span class="font-bold text-slate-700" x-text="citaDetalle.fecha"></span>
            </div>
            <div class="flex items-center justify-between border-b border-slate-200/50 py-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Hora</span>
                <span class="font-extrabold text-slate-800" x-text="citaDetalle.hora + ' hrs'"></span>
            </div>
            <div class="flex items-center justify-between border-b border-slate-200/50 py-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Médico</span>
                <span class="font-bold text-slate-700" x-text="'Dr. ' + citaDetalle.medico"></span>
            </div>
            <div class="flex items-center justify-between border-b border-slate-200/50 py-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Especialidad</span>
                <span class="font-bold text-slate-700" x-text="citaDetalle.especialidad"></span>
            </div>
            <div class="flex items-center justify-between border-b border-slate-200/50 py-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Triage</span>
                <span
                    class="rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-[10px] font-bold text-indigo-700"
                    x-text="citaDetalle.triage"></span>
            </div>
            <div class="flex items-center justify-between py-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Estado</span>
                <span class="font-bold text-slate-700" x-text="citaDetalle.estado"></span>
            </div>
            <div class="border-t border-slate-200/60 pt-2">
                <span class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-400">Motivo</span>
                <p class="rounded-xl border border-slate-100 bg-white p-2.5 italic text-slate-700"
                    x-text="citaDetalle.motivo"></p>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="button" @click="modalDetalle = false"
                class="rounded-xl bg-slate-100 px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-200">
                Cerrar
            </button>
        </div>
    </div>
</div>
