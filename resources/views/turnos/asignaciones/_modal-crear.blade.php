<div x-data="modalAsignarTurno(@js(
    $turnos
        ->map(
            fn($t) => [
                'id' => $t->id,
                'nombre' => $t->nombre,
                'codigo' => $t->codigo,
                'hora_inicio' => substr($t->hora_inicio, 0, 5),
                'hora_fin' => substr($t->hora_fin, 0, 5),
                'cruza' => (bool) $t->cruza_medianoche,
                'rango' => substr($t->hora_inicio, 0, 5) . ' — ' . substr($t->hora_fin, 0, 5),
            ],
        )
        ->values(),
))" x-init="init()" @precargar-turno.window="precargar($event.detail)" x-show="abierto"
    x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity>

    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="cerrar()"></div>

    <div class="relative min-h-full flex items-start justify-center p-4 sm:p-6">
        <div x-show="abierto" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4" @click.stop
            class="relative w-full max-w-2xl bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden">

            <form action="{{ route('admin.users.turnos.store', $user) }}" method="POST">
                @csrf

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-extrabold text-slate-800 text-base">Asignar turno</h3>
                        <p class="text-xs text-slate-400">
                            <span x-text="diaLegible || 'Sin día'"></span>
                            <template x-if="form.turno_id">
                                <span> · <strong x-text="turnoSel?.rango || ''"></strong></span>
                            </template>
                        </p>
                    </div>
                    <button type="button" @click="cerrar()"
                        class="w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">

                    {{-- Turno --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Turno *
                        </label>
                        <select name="turno_id" x-model="form.turno_id" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none">
                            <option value="">— Selecciona un turno —</option>
                            <template x-for="t in data.turnos" :key="t.id">
                                <option :value="t.id" x-text="t.nombre + ' (' + t.rango + ')'"></option>
                            </template>
                        </select>

                        <template x-if="turnoSel">
                            <div
                                class="mt-3 bg-brand-50/60 border border-brand-100 rounded-2xl p-3 flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-brand-100 flex items-center justify-center text-brand-600 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-xs min-w-0 flex-1">
                                    <p class="font-bold text-slate-800 truncate" x-text="turnoSel.nombre"></p>
                                    <p class="text-slate-500 font-mono"
                                        x-text="turnoSel.hora_inicio + ' — ' + turnoSel.hora_fin"></p>
                                    <template x-if="turnoSel.cruza">
                                        <p class="text-xs font-bold text-amber-600 mt-0.5">⚠ Cruza medianoche</p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Día --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Día de la semana
                        </label>
                        <select name="dia_semana" x-model="form.dia_semana"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none">
                            <option value="">— Todos los días —</option>
                            <template x-for="(nombre, idx) in dias" :key="idx">
                                <option :value="idx" x-text="nombre"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Vigencia --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                Fecha de inicio *
                            </label>
                            <input type="date" name="fecha_inicio" x-model="form.fecha_inicio" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                Fecha de fin
                            </label>
                            <input type="date" name="fecha_fin" x-model="form.fecha_fin"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none">
                            <p class="text-xs text-slate-400 mt-1.5">
                                <template x-if="esTodosLosDias && form.fecha_fin">
                                    <span>Auto: fin de mes (puedes cambiarlo).</span>
                                </template>
                                <template x-if="!esTodosLosDias || !form.fecha_fin">
                                    <span>Vacío = indefinido.</span>
                                </template>
                            </p>
                        </div>
                    </div>

                    {{-- Indicador del rango a asignar --}}
                    <template x-if="form.fecha_inicio && form.turno_id">
                        <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-3 flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-xs min-w-0 flex-1">
                                <p class="font-bold text-emerald-800">Resumen de la asignación</p>
                                <p class="text-emerald-700 mt-1 leading-relaxed">
                                    <span x-text="turnoSel?.nombre || '—'"></span> ·
                                    <strong x-text="diaLegible"></strong>
                                    <br>
                                    Del <strong x-text="fechaLegible(form.fecha_inicio)"></strong>
                                    <template x-if="form.fecha_fin">
                                        <span> al <strong x-text="fechaLegible(form.fecha_fin)"></strong></span>
                                    </template>
                                    <template x-if="!form.fecha_fin">
                                        <span> en adelante (indefinido)</span>
                                    </template>
                                </p>
                            </div>
                        </div>
                    </template>

                    {{-- Área --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Área / Servicio
                        </label>
                        <input type="text" name="area" x-model="form.area"
                            placeholder="Ej. Urgencias, Hospitalización"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none">
                    </div>

                    {{-- Notas --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Notas
                        </label>
                        <textarea name="notas" x-model="form.notas" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none resize-none"></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                    <button type="button" @click="cerrar()"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Asignar turno
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function modalAsignarTurno(turnos) {
        return {
            abierto: false,
            data: {
                turnos
            },
            dias: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            form: {
                turno_id: '',
                dia_semana: '',
                fecha_inicio: '{{ now()->format('Y-m-d') }}',
                fecha_fin: '',
                area: '',
                notas: '',
            },

            /* ─────── GETTERS ─────── */

            get turnoSel() {
                return this.data.turnos.find(t => t.id == this.form.turno_id) || null;
            },

            get diaLegible() {
                if (this.form.dia_semana === '' || this.form.dia_semana === null) {
                    return 'Todos los días';
                }
                return this.dias[this.form.dia_semana] || '—';
            },

            get esTodosLosDias() {
                return this.form.dia_semana === '' || this.form.dia_semana === null;
            },

            /* ─────── HELPERS ─────── */

            /**
             * Último día del mes de una fecha YYYY-MM-DD.
             */
            finDeMes(fechaStr) {
                if (!fechaStr) return '';
                const f = new Date(fechaStr + 'T00:00:00');
                const ultimo = new Date(f.getFullYear(), f.getMonth() + 1, 0);
                const p = n => String(n).padStart(2, '0');
                return `${ultimo.getFullYear()}-${p(ultimo.getMonth() + 1)}-${p(ultimo.getDate())}`;
            },

            /**
             * Formatea YYYY-MM-DD a "12 oct 2026".
             */
            fechaLegible(fechaStr) {
                if (!fechaStr) return '—';
                const d = new Date(fechaStr + 'T00:00:00');
                return d.toLocaleDateString('es-MX', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                });
            },

            /* ─────── CICLO DE VIDA ─────── */

            init() {
                // Cuando el día cambia a "Todos los días" o a uno específico,
                // recalculamos la fecha de fin.
                this.$watch('form.dia_semana', (valor) => {
                    const esTodos = valor === '' || valor === null || valor === undefined;
                    if (esTodos && this.form.fecha_inicio) {
                        this.form.fecha_fin = this.finDeMes(this.form.fecha_inicio);
                    }
                });

                // Si el usuario cambia la fecha de inicio y ya es "Todos los días",
                // recalculamos el fin de mes.
                this.$watch('form.fecha_inicio', (valor) => {
                    if (this.esTodosLosDias && valor) {
                        this.form.fecha_fin = this.finDeMes(valor);
                    }
                });
            },

            abrir() {
                this.abierto = true;
            },

            cerrar() {
                this.abierto = false;
                this.form = {
                    turno_id: '',
                    dia_semana: '',
                    fecha_inicio: '{{ now()->format('Y-m-d') }}',
                    fecha_fin: '',
                    area: '',
                    notas: '',
                };
            },

            /* ─────── PRECARGA DESDE EL CALENDARIO ─────── */

            precargar(d = {}) {
                // 1. Día de la semana (si viene desde el calendario)
                if (d.dia_semana !== undefined && d.dia_semana !== null) {
                    this.form.dia_semana = d.dia_semana;
                }

                // 2. Fecha clickeada → fecha_inicio
                if (d.fecha) {
                    this.form.fecha_inicio = d.fecha;
                }

                // 3. Si es "Todos los días", limitamos al fin del mes
                if (this.esTodosLosDias && this.form.fecha_inicio) {
                    this.form.fecha_fin = this.finDeMes(this.form.fecha_inicio);
                }

                // 4. Auto-seleccionar el turno cuya hora coincida con la clickeada
                if (d.hora) {
                    const turnoMatch = this.data.turnos.find(t => t.hora_inicio === d.hora);
                    if (turnoMatch) this.form.turno_id = turnoMatch.id;
                }

                this.abrir();
            },
        };
    }
</script>
