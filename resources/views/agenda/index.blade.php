<x-app-layout>
    @php
        $esMedico = auth()
            ->user()
            ->getRoleNames()
            ->contains(function ($rol) {
                $rol = strtolower(trim($rol));
                return $rol === 'm' || str_starts_with($rol, 'medic');
            });
        $puedeCrear = !$esMedico;
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">Agenda Médica</h2>
                <p class="text-xs text-slate-400 mt-0.5">Control de citas y disponibilidad de especialistas</p>
            </div>

            @if ($puedeCrear)
                <button type="button" @click="$dispatch('abrir-modal-crear')"
                    class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm hover:shadow-indigo-200 hover:shadow-lg transition-all duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Cita
                </button>
            @endif
        </div>
    </x-slot>

    {{-- Estilos FullCalendar --}}
    <style>
        .fc {
            --fc-border-color: #e2e8f0;
            --fc-button-bg-color: #4f46e5;
            --fc-button-border-color: #4f46e5;
            --fc-button-hover-bg-color: #4338ca;
            --fc-button-hover-border-color: #4338ca;
            --fc-button-active-bg-color: #3730a3;
            --fc-button-active-border-color: #3730a3;
            --fc-today-bg-color: rgba(99, 102, 241, .07);
            --fc-event-border-color: transparent;
            font-size: 12px;
        }

        .fc .fc-toolbar-title {
            font-size: 1.1rem;
            font-weight: 900;
            color: #1e293b;
            text-transform: capitalize;
        }

        .fc .fc-button {
            font-size: 11px;
            font-weight: 700;
            border-radius: 12px;
            padding: .45rem .9rem;
            text-transform: capitalize;
        }

        .fc .fc-col-header-cell-cushion {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            padding: 8px 4px;
        }

        .fc-event {
            cursor: pointer;
            border-radius: 8px;
            padding: 1px 3px;
            font-weight: 600;
        }

        .fc-theme-standard .fc-scrollgrid {
            border-radius: 16px;
            overflow: hidden;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Agenda diaria */
        .dia-col-cabecera {
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .dia-cita {
            transition: filter .15s, transform .15s;
        }

        .dia-cita:hover {
            filter: brightness(1.08);
            z-index: 15;
        }

        .dia-hueco-hover {
            position: absolute;
            left: 0;
            right: 0;
            pointer-events: none;
            background: rgba(99, 102, 241, .10);
            border-top: 1px dashed #6366f1;
            z-index: 5;
        }

        @media (prefers-reduced-motion: reduce) {
            .dia-cita {
                transition: none;
            }
        }

        /* Tooltip de ayuda de la agenda */
        .ayuda-tooltip {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 320px;
            z-index: 50;
        }

        .ayuda-tooltip::before {
            content: '';
            position: absolute;
            top: -6px;
            right: 14px;
            width: 12px;
            height: 12px;
            background: #ffffff;
            border-left: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            transform: rotate(45deg);
        }
    </style>

    @include('agenda._scripts')

    {{-- ==================== LÓGICA DE LA AGENDA DIARIA ====================
         Se compone con agendaData() (de _scripts) sin modificarlo:
         x-data="agendaCompleta()" = agendaData() + agendaDiaria()
         Lee las citas del calendario (FullCalendar) o de `this.citas` si existe.
         Campos esperados en extendedProps de cada evento:
         medico_id, paciente_id, estado, triage, especialidad_id
    --}}
    <script>
        // Fecha local en formato YYYY-MM-DD (evita el desfase de UTC de toISOString)
        function fechaLocal(d = new Date()) {
            const p = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`;
        }

        // Une objetos conservando getters (el spread {...a, ...b} los evaluaría y los perdería)
        function mezclarObjetos(...objs) {
            const destino = {};
            objs.forEach(o => Object.defineProperties(destino, Object.getOwnPropertyDescriptors(o)));
            return destino;
        }

        function agendaDiaria(base, medicosIniciales, puedeCrear) {
            return {
                // ---------- estado ----------
                vista: 'calendario',
                fechaDia: fechaLocal(),
                medicosLista: medicosIniciales,
                puedeCrear: puedeCrear,
                citasDia: [],
                horaIni: 7,
                horaFin: 21,
                pxHora: 64,
                huecoY: null,
                huecoMedico: null,
                ahoraMin: null,

                // ---------- ciclo de vida ----------
                initCalendar() {
                    base.initCalendar.call(this);

                    // Mantiene la agenda diaria sincronizada con el calendario
                    this.$nextTick(() => {
                        const cal = this.calendarioRef();
                        try {
                            cal?.on('eventsSet', () => this.refrescarDia());
                        } catch (e) {}
                        this.refrescarDia();
                    });

                    this.$watch('fechaDia', () => this.refrescarDia());
                    this.$watch('vista', v => {
                        if (v === 'calendario') this.$nextTick(() => this.calendarioRef()?.updateSize());
                        if (v === 'diaria') this.refrescarDia();
                    });
                    this.$watch('filtros', () => this.refrescarDia(), {
                        deep: true
                    });

                    this.actualizarAhora();
                    setInterval(() => this.actualizarAhora(), 60000);
                },

                calendarioRef() {
                    return this.calendar || window.calendar || null;
                },

                actualizarAhora() {
                    const d = new Date();
                    this.ahoraMin = d.getHours() * 60 + d.getMinutes();
                },

                // ---------- datos ----------
                refrescarDia() {
                    let origen = [];
                    if (Array.isArray(this.citas)) {
                        origen = this.citas.map(c => ({
                            id: c.id,
                            title: c.title,
                            start: new Date(c.start),
                            end: c.end ? new Date(c.end) : new Date(new Date(c.start).getTime() + 30 * 60000),
                            color: c.color || c.backgroundColor,
                            ...(c.extendedProps || {}),
                            medico_id: c.medico_id ?? c.extendedProps?.medico_id,
                            paciente_id: c.paciente_id ?? c.extendedProps?.paciente_id,
                            estado: c.estado ?? c.extendedProps?.estado,
                            triage: c.triage ?? c.extendedProps?.triage,
                            especialidad_id: c.especialidad_id ?? c.extendedProps?.especialidad_id,
                        }));
                    } else {
                        const cal = this.calendarioRef();
                        origen = (cal ? cal.getEvents() : []).map(e => ({
                            id: e.id,
                            title: e.title,
                            start: e.start,
                            end: e.end || new Date(e.start.getTime() + 30 * 60000),
                            color: e.backgroundColor,
                            ...e.extendedProps,
                        }));
                    }
                    this.citasDia = origen.filter(c => c.start);
                },

                get horas() {
                    return Array.from({
                        length: this.horaFin - this.horaIni
                    }, (_, i) => this.horaIni + i);
                },

                get medicosDia() {
                    const id = this.filtros?.medico_id;
                    return id ? this.medicosLista.filter(m => m.id == id) : this.medicosLista;
                },

                // Aplica los mismos filtros del panel (estado, triage, especialidad)
                pasaFiltros(c) {
                    const f = this.filtros || {};
                    if (f.estados?.length && !f.estados.includes(c.estado)) return false;
                    if (f.triages?.length && !f.triages.includes(c.triage)) return false;
                    if (f.especialidad_id && c.especialidad_id != f.especialidad_id) return false;
                    return true;
                },

                citasDeMedico(medicoId) {
                    return this.citasDia
                        .filter(c => c.medico_id == medicoId &&
                            fechaLocal(c.start) === this.fechaDia &&
                            c.estado !== 'cancelada' &&
                            this.pasaFiltros(c))
                        .sort((a, b) => a.start - b.start);
                },

                get totalDia() {
                    return this.medicosDia.reduce((n, m) => n + this.citasDeMedico(m.id).length, 0);
                },

                // ---------- fechas ----------
                hoy() {
                    return fechaLocal();
                },
                esHoy() {
                    return this.fechaDia === fechaLocal();
                },

                cambiarDia(n) {
                    const d = new Date(this.fechaDia + 'T00:00:00');
                    d.setDate(d.getDate() + n);
                    this.fechaDia = fechaLocal(d);
                },

                fechaLegible() {
                    return new Date(this.fechaDia + 'T00:00:00')
                        .toLocaleDateString('es-MX', {
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                },

                minutos(d) {
                    d = new Date(d);
                    return d.getHours() * 60 + d.getMinutes();
                },
                horaCorta(d) {
                    return new Date(d).toLocaleTimeString('es-MX', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });
                },
                etiquetaHora(h) {
                    return String(h).padStart(2, '0') + ':00';
                },

                // ---------- posicionamiento ----------
                estiloCita(c) {
                    const ppm = this.pxHora / 60;
                    const top = (this.minutos(c.start) - this.horaIni * 60) * ppm;
                    const alto = Math.max((this.minutos(c.end) - this.minutos(c.start)) * ppm, 24);
                    return `top:${top}px;height:${alto - 2}px;background:${c.color || '#64748b'}`;
                },

                get estiloAhora() {
                    if (!this.esHoy() || this.ahoraMin === null) return 'display:none';
                    const top = (this.ahoraMin - this.horaIni * 60) * (this.pxHora / 60);
                    if (top < 0 || top > this.horas.length * this.pxHora) return 'display:none';
                    return `top:${top}px`;
                },

                // ---------- clic en hueco ----------
                minutoDesdeClic(e) {
                    const y = e.clientY - e.currentTarget.getBoundingClientRect().top;
                    return Math.floor(((y / this.pxHora) * 60) / 15) * 15 + this.horaIni * 60;
                },

                moverHueco(e, medico) {
                    if (!this.puedeCrear) return;
                    if (e.target !== e.currentTarget) {
                        this.huecoY = null;
                        return;
                    }
                    const min = this.minutoDesdeClic(e);
                    this.huecoMedico = medico.id;
                    this.huecoY = (min - this.horaIni * 60) * (this.pxHora / 60);
                },

                clickHueco(e, medico) {
                    if (!this.puedeCrear) return;
                    const min = this.minutoDesdeClic(e);
                    const hora = `${String(Math.floor(min / 60)).padStart(2, '0')}:${String(min % 60).padStart(2, '0')}`;
                    this.$dispatch('abrir-modal-crear', {
                        medico_id: medico.id,
                        fecha: this.fechaDia,
                        hora
                    });
                },

                verDetalle(c) {
                    // Reutiliza el modal de detalle existente
                    if (typeof base.verDetalle === 'function') return base.verDetalle.call(this, c);
                    this.$dispatch('abrir-modal-detalle', {
                        id: c.id
                    });
                },

                // ---------- modal crear (con datos precargados) ----------
                abrirModal(d = {}) {
                    if (!this.puedeCrear) return;
                    base.abrirModal.call(this);
                    const f = this.form;
                    if (f) {
                        if (d.medico_id) f.medico_id = d.medico_id;
                        f.fecha = d.fecha ?? f.fecha ?? this.fechaDia;
                        if (d.hora) f.hora = d.hora;
                    }
                    // Por si tu modal escucha un evento en lugar de usar `form`
                    this.$dispatch('precargar-cita', d);
                },

                // ---------- traslapes ----------
                solapan(a, b) {
                    return a.start < b.end && a.end > b.start;
                },

                // Úsalo desde el modal: busca choques de médico y de paciente
                conflictos({
                    medico_id,
                    paciente_id,
                    inicio,
                    fin,
                    ignorarId = null
                }) {
                    const ini = new Date(inicio),
                        f = new Date(fin);
                    const choca = c => c.id != ignorarId && c.estado !== 'cancelada' && c.start < f && c.end > ini;
                    return {
                        medico: medico_id ? this.citasDia.filter(c => c.medico_id == medico_id && choca(c)) : [],
                        paciente: paciente_id ? this.citasDia.filter(c => c.paciente_id == paciente_id && choca(c)) : [],
                    };
                },

                // Citas del día que ya se traslapan entre sí (se marcan en rojo)
                get idsConflicto() {
                    const ids = new Set();
                    this.medicosDia.forEach(m => {
                        const lista = this.citasDeMedico(m.id);
                        lista.forEach((a, i) => lista.slice(i + 1).forEach(b => {
                            if (this.solapan(a, b)) {
                                ids.add(a.id);
                                ids.add(b.id);
                            }
                        }));
                    });
                    return ids;
                },
            };
        }

        window.agendaCompleta = function(medicos, puedeCrear = true) {
            const base = agendaData();
            return mezclarObjetos(base, agendaDiaria(base, medicos, puedeCrear));
        };
    </script>

    <div x-data="agendaCompleta(@js($medicos->map(fn($m) => ['id' => $m->id, 'nombre' => trim($m->name . ' ' . $m->apellido_paterno)])->values()), @js($puedeCrear))" x-init="initCalendar()" @abrir-modal-crear.window="abrirModal($event.detail || {})"
        class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-2xl text-xs font-semibold">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Métricas --}}
        @php
            $metricas = [
                ['TOTAL SEMANA', $stats['total_semana'] ?? 0, 'text-slate-800'],
                ['PROGRAMADAS', $stats['programadas'] ?? 0, 'text-indigo-600'],
                ['ATENDIDAS', $stats['atendidas'] ?? 0, 'text-emerald-600'],
                ['CANCELADAS', $stats['canceladas'] ?? 0, 'text-rose-600'],
            ];
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($metricas as [$label, $valor, $textColor])
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
                    <p class="text-2xl font-extrabold {{ $textColor }} mt-0.5">{{ $valor }}</p>
                </div>
            @endforeach
        </div>

        {{-- Leyenda de triage --}}
        <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500">
            <span class="text-slate-400 uppercase tracking-wider font-bold">Triage:</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-rose-500"></i> Rojo</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-amber-500"></i> Naranja</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-yellow-400"></i> Amarillo</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-emerald-500"></i> Verde</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-sky-500"></i> Azul</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-slate-400"></i> Sin
                triage</span>
        </div>

        {{-- ==================== FILTROS ==================== --}}
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-100 shadow-sm space-y-4">

            {{-- Header de filtros --}}
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h3 class="font-extrabold text-slate-700 text-sm">Filtros</h3>

                    {{-- Badge de filtros activos --}}
                    <span x-show="filtrosActivos > 0" x-cloak x-text="filtrosActivos"
                        class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 rounded-full bg-indigo-600 text-white text-[10px] font-bold">
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-[11px] font-semibold transition-all duration-200"
                        :class="filtrando ? 'text-indigo-600 scale-110' : 'text-slate-400'">
                        <span x-text="citasFiltradas"></span> de <span x-text="citasTotales"></span> citas
                    </span>

                    <button type="button" @click="limpiarFiltros()" x-show="filtrosActivos > 0" x-cloak
                        class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                        Limpiar filtros
                    </button>
                </div>
            </div>

            {{-- Fila 1: Estados --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Estado</p>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="est in estadosDisponibles" :key="est.value">
                        <button type="button" @click="toggleEstado(est.value)"
                            :class="filtros.estados.includes(est.value) ?
                                'bg-indigo-600 text-white border-indigo-600' :
                                'bg-white text-slate-600 border-slate-200 hover:border-slate-300'"
                            class="px-3 py-1.5 rounded-lg border text-[11px] font-bold transition-all">
                            <span x-text="est.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Fila 2: Triage + Médico + Especialidad --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                {{-- Triage --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Triage</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="tr in triagesDisponibles" :key="tr.value">
                            <button type="button" @click="toggleTriage(tr.value)"
                                :class="filtros.triages.includes(tr.value) ?
                                    'ring-2 ring-offset-1 ring-slate-400 border-slate-400' :
                                    'border-slate-200 hover:border-slate-300'"
                                class="flex items-center gap-1 px-2 py-1 rounded-lg border bg-white text-[10px] font-bold text-slate-600 transition-all">
                                <span class="w-2 h-2 rounded-full" :style="`background-color: ${tr.color}`"></span>
                                <span x-text="tr.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Médico (admin y enfermería) --}}
                @if ($medicos->isNotEmpty())
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Médico</p>
                        <select x-model="filtros.medico_id" @change="aplicarFiltros()"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-[11px] text-slate-700 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="">— Todos los médicos —</option>
                            @foreach ($medicos as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} {{ $m->apellido_paterno }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Especialidad --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Especialidad</p>
                    <select x-model="filtros.especialidad_id" @change="aplicarFiltros()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-[11px] text-slate-700 focus:bg-white focus:border-indigo-500 outline-none">
                        <option value="">— Todas las especialidades —</option>
                        @foreach ($especialidades as $esp)
                            <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ==================== SELECTOR DE VISTA + AYUDA ==================== --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="inline-flex bg-slate-100 p-1 rounded-xl text-[11px] font-bold" role="tablist">
                <button type="button" role="tab" :aria-selected="vista === 'calendario'"
                    @click="vista = 'calendario'"
                    :class="vista === 'calendario' ? 'bg-white shadow text-indigo-600' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-1.5 rounded-lg transition">Calendario</button>
                <button type="button" role="tab" :aria-selected="vista === 'diaria'"
                    @click="vista = 'diaria'"
                    :class="vista === 'diaria' ? 'bg-white shadow text-indigo-600' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-1.5 rounded-lg transition">Agenda diaria por médico</button>
            </div>

            {{-- Botón de ayuda (?) con tooltip explicativo --}}
            <div x-data="{ ayudaAbierta: false }" class="relative">
                <button type="button" @click="ayudaAbierta = !ayudaAbierta" @click.outside="ayudaAbierta = false"
                    class="w-8 h-8 rounded-full bg-white border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 text-slate-500 hover:text-indigo-600 font-bold text-sm flex items-center justify-center shadow-sm transition-all"
                    aria-label="Ayuda sobre la agenda" :aria-expanded="ayudaAbierta">
                    ?
                </button>

                <div x-show="ayudaAbierta" x-cloak x-transition.opacity.duration.150ms
                    class="ayuda-tooltip bg-white border border-slate-200 rounded-2xl shadow-xl p-4 text-left">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 mb-2">
                        Guía rápida de la agenda
                    </p>

                    <ul class="space-y-2.5 text-[11px] text-slate-600 leading-relaxed">
                        <li class="flex items-start gap-2">
                            <span
                                class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center flex-shrink-0">1</span>
                            <span><strong class="text-slate-800">Calendario:</strong> vista mensual/semanal con todas
                                las citas. Haz clic en un evento para ver el detalle.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center flex-shrink-0">2</span>
                            <span><strong class="text-slate-800">Agenda diaria:</strong> una columna por médico con las
                                citas posicionadas por hora. Útil para ver traslapes.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center flex-shrink-0">3</span>
                            <span><strong class="text-slate-800">Filtros:</strong> filtra por estado, triage, médico o
                                especialidad. El contador muestra cuántas citas coinciden.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center flex-shrink-0">4</span>
                            <span><strong class="text-slate-800">Colores de triage:</strong> el color del evento indica
                                la prioridad (rojo = crítico, azul = sin urgencia).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center flex-shrink-0">5</span>
                            <span><strong class="text-slate-800">Traslapes:</strong> las citas que se encimen se marcan
                                con un anillo rojo y la etiqueta <em>⚠ Traslape</em>.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center flex-shrink-0">6</span>
                            <span><strong class="text-slate-800">Crear cita:</strong> haz clic en un espacio libre de
                                la agenda diaria para agendar en ese horario.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-700 font-bold text-[10px] flex items-center justify-center flex-shrink-0">★</span>
                            <span><strong class="text-slate-800">Línea roja:</strong> indica la hora actual del día en
                                curso.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ==================== CALENDARIO ==================== --}}
        <div x-show="vista === 'calendario'"
            class="bg-white p-4 sm:p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div id="calendar"></div>
        </div>

        {{-- ==================== AGENDA DIARIA POR MÉDICO ==================== --}}
        <div x-show="vista === 'diaria'" x-cloak
            class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

            {{-- Navegación de fecha --}}
            <div class="flex items-center justify-between flex-wrap gap-3 p-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <button type="button" @click="cambiarDia(-1)" aria-label="Día anterior"
                        class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-slate-600">‹</button>
                    <input type="date" x-model="fechaDia"
                        class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-700 outline-none focus:border-indigo-500">
                    <button type="button" @click="cambiarDia(1)" aria-label="Día siguiente"
                        class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-slate-600">›</button>
                    <button type="button" @click="fechaDia = hoy()"
                        class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold transition">Hoy</button>
                </div>

                <div class="text-center">
                    <p class="text-sm font-extrabold text-slate-700 capitalize" x-text="fechaLegible()"></p>
                    <p class="text-[11px] text-slate-400"><span x-text="totalDia"></span> citas en el día</p>
                </div>

                <div class="flex items-center gap-3 text-[11px] text-slate-400">
                    <span class="flex items-center gap-1">
                        <i class="w-3 h-3 rounded ring-2 ring-red-600 bg-white inline-block"></i> Traslape
                    </span>
                    <span x-show="puedeCrear">Haz clic en un espacio libre para agendar</span>
                </div>
            </div>

            <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                <div class="flex min-w-max">

                    {{-- Columna de horas --}}
                    <div class="w-14 shrink-0 border-r border-slate-100 bg-slate-50/50 sticky left-0 z-30">
                        <div class="h-12 border-b border-slate-100 bg-slate-50 dia-col-cabecera"></div>
                        <template x-for="h in horas" :key="h">
                            <div class="text-[10px] font-bold text-slate-400 text-right pr-2"
                                :style="`height:${pxHora}px`">
                                <span class="relative -top-1.5" x-text="etiquetaHora(h)"></span>
                            </div>
                        </template>
                    </div>

                    {{-- Una columna por médico --}}
                    <template x-for="m in medicosDia" :key="m.id">
                        <div class="w-56 shrink-0 border-r border-slate-100">
                            <div
                                class="h-12 flex flex-col items-center justify-center border-b border-slate-100 bg-slate-50 dia-col-cabecera">
                                <p class="text-[11px] font-extrabold text-slate-700 truncate max-w-full px-2"
                                    x-text="m.nombre"></p>
                                <p class="text-[10px] text-slate-400" x-text="citasDeMedico(m.id).length + ' citas'">
                                </p>
                            </div>

                            <div class="relative" :class="puedeCrear ? 'cursor-pointer' : ''"
                                :style="`height:${horas.length * pxHora}px;
                                                                                                                                                                                                                                                                    background-image: repeating-linear-gradient(to bottom, #e2e8f0 0, #e2e8f0 1px, transparent 1px, transparent ${pxHora / 2}px)`"
                                @mousemove="moverHueco($event, m)" @mouseleave="huecoY = null"
                                @click="$event.target === $event.currentTarget && clickHueco($event, m)">

                                {{-- Resalte del hueco bajo el cursor --}}
                                <div x-show="huecoY !== null && huecoMedico === m.id" class="dia-hueco-hover"
                                    :style="`top:${huecoY}px;height:${pxHora / 4}px`"></div>

                                {{-- Línea de hora actual --}}
                                <div class="absolute left-0 right-0 z-10 border-t-2 border-rose-500 pointer-events-none"
                                    :style="estiloAhora"></div>

                                <template x-for="c in citasDeMedico(m.id)" :key="c.id">
                                    <div @click.stop="verDetalle(c)" role="button" tabindex="0"
                                        @keydown.enter="verDetalle(c)"
                                        class="dia-cita absolute left-1 right-1 rounded-lg px-2 py-1 text-white text-[10px] font-semibold overflow-hidden shadow-sm z-10"
                                        :class="idsConflicto.has(c.id) ? 'ring-2 ring-red-600 ring-offset-1' : ''"
                                        :style="estiloCita(c)">
                                        <p class="truncate" x-text="c.title"></p>
                                        <p class="opacity-80" x-text="horaCorta(c.start) + ' – ' + horaCorta(c.end)">
                                        </p>
                                        <p x-show="idsConflicto.has(c.id)" class="font-black">⚠ Traslape</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div x-show="medicosDia.length === 0" class="p-10 text-xs text-slate-400">
                        No hay médicos para mostrar con los filtros actuales.
                    </div>
                </div>
            </div>
        </div>

        {{-- MODALES --}}
        @include('agenda.modal-crear')
        @include('agenda.modal-detalle')
    </div>
</x-app-layout>
