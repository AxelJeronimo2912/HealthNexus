{{-- Modal de Registro / Edición de Servicio --}}
<div id="modal-servicio" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-servicio-titulo">
    <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto border border-slate-100">

        <div class="flex justify-between items-start mb-6 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <h3 id="modal-servicio-titulo" class="font-extrabold text-slate-800 text-lg">
                        Nuevo Servicio
                    </h3>
                    <p class="text-[11px] text-slate-400">Configuración y detalles del área de atención</p>
                </div>
            </div>
            <button type="button" id="btn-cerrar-servicio" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-2" aria-label="Cerrar">&times;</button>
        </div>

        @php
            $inputCls = 'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
            $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        @endphp

        <form action="{{ route('servicios.store') }}" method="POST" class="space-y-6" id="form-servicio-modal">
            @csrf

            {{-- 1. Identificación --}}
            <section class="space-y-4">
                <h4 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-1">Identificación</h4>
                <p class="text-sm text-gray-500 mb-4">Datos básicos del servicio.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <div>
                        <label class="{{ $labelCls }}">Código *</label>
                        <input type="text" name="codigo" required placeholder="Ej. CONS-EXT, URG" class="{{ $inputCls }} uppercase">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Nombre *</label>
                        <input type="text" name="nombre" required placeholder="Ej. Consulta Externa" class="{{ $inputCls }}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="{{ $labelCls }}">Tipo *</label>
                        <select name="tipo" required class="{{ $inputCls }}">
                            <option value="">— Selecciona —</option>
                            @foreach ([
                                'consulta_externa' => 'Consulta Externa',
                                'urgencias' => 'Urgencias',
                                'hospitalizacion' => 'Hospitalización',
                                'quirofano' => 'Quirófano',
                                'farmacia' => 'Farmacia',
                                'enfermeria' => 'Enfermería',
                                'laboratorio' => 'Laboratorio',
                                'imagenologia' => 'Imagenología',
                                'otro' => 'Otro',
                            ] as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="{{ $labelCls }}">Descripción</label>
                        <textarea name="descripcion" rows="2" class="{{ $inputCls }} resize-none"></textarea>
                    </div>
                </div>
            </section>

            {{-- 2. Ubicación --}}
            <section class="space-y-4">
                <h4 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-1"> Ubicación</h4>
                <p class="text-sm text-gray-500 mb-4">Dónde se encuentra físicamente el servicio.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-3">
                        <label class="{{ $labelCls }}">Ubicación física</label>
                        <input type="text" name="ubicacion" placeholder="Ej. Edificio A, planta baja" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Piso</label>
                        <input type="text" name="piso" placeholder="Ej. Piso 1" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Ala</label>
                        <input type="text" name="ala" placeholder="Ej. Ala Norte" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Extensión Telefónica</label>
                        <input type="text" name="extension_telefonica" placeholder="Ej. 4102" class="{{ $inputCls }}">
                    </div>
                </div>
            </section>

            {{-- Horario  --}}
            <section class="space-y-4">
                <h4 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-1"> Horario </h4>
                <div class="space-y-3">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="abierto_24h" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                        <span class="ml-2 text-xs font-semibold text-slate-700">Abierto 24 horas</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="{{ $labelCls }}">Apertura</label>
                        <input type="time" name="hora_apertura" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Cierre</label>
                        <input type="time" name="hora_cierre" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Capacidad </label>
                        <p class="text-sm text-gray-500 mb-4">Número máximo de pacientes simultáneos, camas o recursos.</p>
                        <input type="number" name="capacidad" min="0" placeholder="Ej. 10" class="{{ $inputCls }}">
                    </div>
                </div>
            </section>

            {{-- 4. Costo y Notas --}}
            <section class="space-y-4">
                <h4 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-1">Costo </h4>
                <p class="text-sm text-gray-500 mb-4">Define si este servicio genera un cargo al paciente. Ej. Enfermería puede ser gratuito.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labelCls }}">Precio / Costo ($)</label>
                        <input type="number" name="precio" step="0.01" min="0" value="0.00" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Descripción del Costo</label>
                        <input type="text" name="precio_descripcion" placeholder="Ej. Por consulta" class="{{ $inputCls }}">
                    </div>
                </div>
                <div>
                    <label class="{{ $labelCls }}">Notas adicionales</label>
                    <textarea name="notas" rows="2" class="{{ $inputCls }} resize-none"></textarea>
                </div>
                <div class="flex items-center gap-4 pt-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="activo" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                        <span class="ml-2 text-xs font-semibold text-slate-700">Servicio activo</span>
                    </label>
                </div>
            </section>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" id="btn-cancelar-servicio"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar Servicio
                </button>
            </div>
        </form>
    </div>
</div>