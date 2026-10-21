<div id="modal-servicios" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal('modal-servicios')"></div>

        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div
                class="px-6 py-5 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white rounded-t-3xl z-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-lg">Servicios de la Especialidad</h3>
                        <p class="text-[11px] text-slate-400" id="servicios-subtitle">Asociación y gestión</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-servicios')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-sm">Asociar servicio</h4>
                            <p class="text-[11px] text-slate-400">Agrega un servicio a esta especialidad</p>
                        </div>
                    </div>

                    <form id="form-asignar-servicio" method="POST"
                        class="p-5 flex flex-col sm:flex-row gap-3 items-end">
                        @csrf
                        <div class="flex-1 w-full">
                            <label class="{{ $labelCls }}">Servicio *</label>
                            <select name="servicio_id" required class="{{ $inputCls }}">
                                <option value="">— Selecciona —</option>
                                @foreach ($serviciosDisponibles as $s)
                                    <option value="{{ $s->id }}">
                                        {{ $s->nombre }} ({{ $s->tipo_label ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Asociar servicio
                        </button>
                    </form>
                </div>

                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100">
                        <h4 class="font-extrabold text-slate-800 text-sm">Servicios asociados</h4>
                        <p class="text-[11px] text-slate-400" id="servicios-count">0 servicios</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50/70 border-b border-slate-100">
                                <tr>
                                    <th class="{{ $thCls }} text-left">Servicio</th>
                                    <th class="{{ $thCls }} text-left">Tipo</th>
                                    <th class="{{ $thCls }} text-left">Ubicación</th>
                                    <th class="px-5 py-3.5"></th>
                                </tr>
                            </thead>
                            <tbody id="servicios-tbody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
