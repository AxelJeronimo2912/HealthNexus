<div id="modal-medicos" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal('modal-medicos')"></div>

        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            {{-- ============ HEADER ============ --}}
            <div
                class="px-6 py-5 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white rounded-t-3xl z-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-lg">Médicos de la Especialidad</h3>
                        <p class="text-[11px] text-slate-400" id="medicos-subtitle">Asignación y gestión</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-medicos')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">

                {{-- ============ ANUNCIO / AVISO ============ --}}
                <div
                    class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 via-indigo-50/70 to-white p-4 flex gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-white border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-extrabold text-indigo-900">Buenas prácticas al asignar médicos</p>
                        <ul
                            class="mt-1 space-y-0.5 text-[11px] text-indigo-800/80 leading-relaxed list-disc list-inside marker:text-indigo-400">
                            <li>Solo puede haber <strong>un médico principal</strong> por especialidad.</li>
                            <li>Registra la <strong>cédula de especialidad</strong> si aplica (opcional).</li>
                            <li>El médico debe estar <strong>activo</strong> y tener rol clínico.</li>
                        </ul>
                    </div>
                </div>

                {{-- ============ ASIGNAR MÉDICO ============ --}}
                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-extrabold text-slate-800 text-sm">Asignar médico</h4>
                            <p class="text-[11px] text-slate-400">Agrega un médico a esta especialidad</p>
                        </div>
                        <span
                            class="ml-auto hidden sm:inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                            Paso 1
                        </span>
                    </div>

                    <form id="form-asignar-medico" method="POST"
                        class="p-5 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                        @csrf

                        {{-- Médico --}}
                        <div class="md:col-span-3">
                            <label class="{{ $labelCls }}">Médico <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select name="user_id" required class="{{ $inputCls }} appearance-none pr-9">
                                    <option value="">— Selecciona un médico —</option>
                                    @foreach ($medicosDisponibles as $u)
                                        <option value="{{ $u->id }}">
                                            {{ $u->nombre_completo ?: $u->name }}
                                            ({{ $u->getRoleNames()->first() ?? 'sin rol' }})
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Solo se listan médicos activos con rol clínico.
                            </p>
                        </div>

                        {{-- Cédula --}}
                        <div class="md:col-span-2">
                            <label class="{{ $labelCls }}">Cédula de especialidad</label>
                            <div
                                class="flex rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:bg-white bg-slate-50 transition-colors">
                                <span
                                    class="inline-flex items-center px-3 text-[11px] font-bold text-slate-500 bg-slate-100 border-r border-slate-200">
                                    CED
                                </span>
                                <input type="text" name="numero_cedula_especialidad" maxlength="50"
                                    placeholder="Ej: 12345678"
                                    class="flex-1 bg-transparent border-0 focus:ring-0 outline-none px-3 py-2.5 text-xs text-slate-800 placeholder:text-slate-400">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Opcional. Se guarda con la asignación.</p>
                        </div>

                        {{-- Principal --}}
                        <div class="md:col-span-1">
                            <label class="{{ $labelCls }}">Rol</label>
                            <label
                                class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:bg-white transition-colors">
                                <span class="text-[11px] font-bold text-slate-700">Principal</span>
                                <input type="checkbox" name="es_principal" value="1" class="peer sr-only">
                                <span
                                    class="relative w-9 h-5 rounded-full bg-slate-300 peer-checked:bg-amber-500 transition-colors">
                                    <span
                                        class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform peer-checked:translate-x-4"></span>
                                </span>
                            </label>
                            <p class="text-[10px] text-slate-400 mt-1">Solo uno por especialidad.</p>
                        </div>

                        <div
                            class="md:col-span-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 pt-3 border-t border-slate-100">
                            <p class="text-[10px] text-slate-400">
                                <span class="text-rose-500">*</span> Campos obligatorios
                            </p>
                            <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Asignar médico
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ============ MÉDICOS ASIGNADOS ============ --}}
                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-3 flex-wrap">
                        <div
                            class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-extrabold text-slate-800 text-sm">Médicos asignados</h4>
                            <p class="text-[11px] text-slate-400" id="medicos-count">0 médicos</p>
                        </div>
                        <span
                            class="ml-auto hidden sm:inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Activo
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50/70 border-b border-slate-100">
                                <tr>
                                    <th class="{{ $thCls }} text-left">Médico</th>
                                    <th class="{{ $thCls }} text-left">Rol</th>
                                    <th class="{{ $thCls }} text-left">Cédula</th>
                                    <th class="{{ $thCls }} text-center">Principal</th>
                                    <th class="px-5 py-3.5"></th>
                                </tr>
                            </thead>
                            <tbody id="medicos-tbody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>

                    {{-- Estado vacío (fallback visual si el JS no ha rellenado todavía) --}}
                    <div id="medicos-empty-hint" class="px-5 py-8 text-center">
                        <div
                            class="mx-auto w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-400">Sin médicos asignados aún.</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Usa el formulario de arriba para asignar el
                            primero.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
