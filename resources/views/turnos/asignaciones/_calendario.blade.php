@php

    $bloquesJs = collect($bloques)
        ->map(
            fn($b) => [
                'id' => $b['id'],
                'pivot_id' => $b['pivot_id'],
                'nombre' => $b['nombre'],
                'rango' => $b['rango'],
                'dia_semana' => $b['dia_semana'],
                'top' => $b['top'],
                'alto' => $b['alto'],
                'color' => $b['color'],
                'area' => $b['area'],
                'fecha' => $b['fecha'] ?? null,
                'fecha_inicio' => $b['fecha_inicio'] ?? null,
                'fecha_fin' => $b['fecha_fin'] ?? null,
            ],
        )
        ->values();

    $altoTotal = count($rangoHoras) * $altoPorHora;
    $clickable = $clickable ?? false;
    $vistaInicial = $vistaInicial ?? 'mes'; // 'mes' | 'semana'
@endphp

<div x-data="calendarioTurnos({
    bloques: @js($bloquesJs),
    altoPorHora: {{ $altoPorHora }},
    horaMin: {{ $rangoHoras[0] }},
    horaMax: {{ end($rangoHoras) }},
    clickable: {{ $clickable ? 'true' : 'false' }},
    vista: '{{ $vistaInicial }}',
})" x-init="init()" @keydown.window="alPresionarTecla($event)" class="space-y-4">

    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">

        {{-- Navegación ◀ Hoy ▶ --}}
        <div class="flex items-center gap-2">
            <button type="button" @click="anterior()"
                class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-slate-600 transition"
                :aria-label="vista === 'mes' ? 'Mes anterior' : 'Semana anterior'">‹</button>

            <button type="button" @click="hoy()"
                class="px-3 py-1.5 rounded-lg bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition">
                Hoy
            </button>

            <button type="button" @click="siguiente()"
                class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 font-bold text-slate-600 transition"
                :aria-label="vista === 'mes' ? 'Mes siguiente' : 'Semana siguiente'">›</button>
        </div>

        {{-- Título + Selectores de mes y año --}}
        <div class="flex flex-wrap items-center gap-3">
            <p class="text-sm font-extrabold text-slate-700 capitalize" x-text="tituloActual"></p>

            <select x-model.number="mesSeleccionado" @change="irAlMes()"
                class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:border-brand-500 outline-none">
                <template x-for="(nombre, idx) in meses" :key="idx">
                    <option :value="idx" x-text="nombre" :selected="idx === mesSeleccionado"></option>
                </template>
            </select>

            <select x-model.number="anioSeleccionado" @change="irAlMes()"
                class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:border-brand-500 outline-none">
                <template x-for="a in aniosDisponibles" :key="a">
                    <option :value="a" x-text="a" :selected="a === anioSeleccionado"></option>
                </template>
            </select>
        </div>

        {{-- Selector de vista --}}
        <div class="inline-flex rounded-lg border border-slate-200 overflow-hidden text-xs font-bold">
            <button type="button" @click="vista = 'mes'"
                :class="vista === 'mes' ? 'bg-brand-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                class="px-3 py-1.5 transition">Mes</button>
            <button type="button" @click="vista = 'semana'"
                :class="vista === 'semana' ? 'bg-brand-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                class="px-3 py-1.5 transition border-l border-slate-200">Semana</button>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Filtros:</span>

        <select x-model="filtroTurno"
            class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:border-brand-500 outline-none">
            <option value="">Todos los turnos</option>
            <template x-for="t in bloquesUnicos" :key="t.id">
                <option :value="String(t.id)" x-text="t.nombre"></option>
            </template>
        </select>

        <select x-model="filtroArea"
            class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-700 focus:border-brand-500 outline-none">
            <option value="">Todas las áreas</option>
            <template x-for="a in areasDisponibles" :key="a">
                <option :value="a" x-text="a"></option>
            </template>
        </select>

        <button type="button" x-show="filtroTurno !== '' || filtroArea !== ''" @click="limpiarFiltros()"
            class="text-xs font-bold text-brand-600 hover:underline">
            Limpiar filtros
        </button>

        <p class="ml-auto text-xs text-slate-400">
            <span class="font-bold text-slate-700" x-text="contadorBloques"></span>
            <span x-text="vista === 'mes' ? 'turnos en el mes' : 'turnos en la semana'"></span>
        </p>
    </div>

    {{-- ══════════════════════════════════════════════
         VISTA MENSUAL (todos los días del mes)
         ══════════════════════════════════════════════ --}}
    <div x-show="vista === 'mes'" x-cloak class="overflow-x-auto">
        <div class="min-w-[760px] border border-slate-100 rounded-xl overflow-hidden">

            {{-- Cabecera de días --}}
            <div class="grid grid-cols-7 bg-slate-50/70 border-b border-slate-100">
                <template x-for="(nombre, idx) in diasCortos" :key="idx">
                    <div class="px-2 py-2 text-center text-xs font-bold uppercase tracking-wider text-slate-400"
                        :class="idx > 0 ? 'border-l border-slate-100' : ''" x-text="nombre"></div>
                </template>
            </div>

            {{-- Semanas del mes --}}
            <template x-for="(semana, si) in semanasDelMes" :key="'sem-' + si">
                <div class="grid grid-cols-7">
                    <template x-for="(celda, ci) in semana" :key="celda.fecha">
                        <div @click="irADia(celda.fecha)"
                            class="min-h-[110px] p-1.5 border-b border-slate-100 cursor-pointer hover:bg-brand-50/40 transition-colors"
                            :class="[
                                ci > 0 ? 'border-l' : '',
                                celda.delMes ? 'bg-white' : 'bg-slate-50/60',
                                celda.esHoy ? 'bg-brand-50/40' : ''
                            ]"
                            :title="'Ver semana del ' + celda.numero">

                            <div class="flex items-center justify-between mb-1">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-extrabold"
                                    :class="celda.esHoy ?
                                        'bg-brand-600 text-white' :
                                        (celda.delMes ? 'text-slate-700' : 'text-slate-300')"
                                    x-text="celda.numero"></span>
                                <span x-show="celda.bloques.length > 0" class="text-[9px] font-bold text-slate-400"
                                    x-text="celda.bloques.length"></span>
                            </div>

                            <div class="space-y-1">
                                <template x-for="b in celda.bloques.slice(0, 3)" :key="b.pivot_id">
                                    <div :title="b.nombre + ' · ' + b.rango + ' · ' + (b.area || 'Sin área')"
                                        class="rounded px-1 py-0.5 border-l-4 text-[9px] font-bold leading-tight truncate"
                                        :class="[b.color, celda.delMes ? '' : 'opacity-50']">
                                        <span x-text="b.nombre"></span>
                                        <span class="font-mono opacity-80" x-text="' ' + b.rango"></span>
                                    </div>
                                </template>
                                <p x-show="celda.bloques.length > 3" class="text-[9px] font-bold text-brand-600"
                                    x-text="'+' + (celda.bloques.length - 3) + ' más'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         VISTA SEMANAL (grid por horas)
         ══════════════════════════════════════════════ --}}
    <div x-show="vista === 'semana'" x-cloak class="overflow-x-auto">
        <div class="min-w-[800px]">

            {{-- Cabecera de días --}}
            <div class="grid grid-cols-[60px_repeat(7,1fr)] border-b border-slate-100 bg-slate-50/70">
                <div class="px-2 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">
                    Hora
                </div>
                <template x-for="(dia, idx) in diasDeLaSemana" :key="dia.fecha">
                    <div class="px-2 py-2 text-center border-l border-slate-100"
                        :class="dia.esHoy ? 'bg-brand-50' : ''">
                        <p class="text-xs font-bold uppercase tracking-wider"
                            :class="dia.esHoy ? 'text-brand-700' : 'text-slate-400'" x-text="dia.nombreCorto"></p>
                        <p class="text-[13px] font-extrabold mt-0.5"
                            :class="dia.esHoy ? 'text-brand-700' : 'text-slate-700'" x-text="dia.numero"></p>
                    </div>
                </template>
            </div>

            {{-- Cuerpo --}}
            <div class="grid grid-cols-[60px_repeat(7,1fr)] relative" style="height: {{ $altoTotal }}px;">

                {{-- Columna de horas --}}
                <div class="border-r border-slate-100 bg-slate-50/30">
                    @foreach ($rangoHoras as $h)
                        <div class="text-xs font-bold text-slate-400 text-right pr-2 border-b border-slate-100/60"
                            style="height: {{ $altoPorHora }}px;">
                            <span class="relative -top-1.5">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</span>
                        </div>
                    @endforeach
                </div>

                {{-- Columnas por día --}}
                <template x-for="(dia, col) in diasDeLaSemana" :key="dia.fecha">
                    <div class="relative border-r border-slate-100" :class="dia.esHoy ? 'bg-brand-50/30' : ''"
                        style="background-image: repeating-linear-gradient(
                            to bottom,
                            #e2e8f0 0, #e2e8f0 1px,
                            transparent 1px, transparent {{ $altoPorHora }}px
                        );">

                        {{-- Capas clickeables por hora --}}
                        @if ($clickable)
                            @foreach ($rangoHoras as $i => $h)
                                <div class="absolute left-0 right-0 cursor-pointer hover:bg-brand-100/40 transition-colors"
                                    style="top: {{ $i * $altoPorHora }}px; height: {{ $altoPorHora }}px;"
                                    title="Clic para asignar a las {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00"
                                    @click="$dispatch('precargar-turno', {
                                        dia_semana: dia.diaSemana,
                                        hora: '{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00',
                                        fecha: dia.fecha
                                    })">
                                </div>
                            @endforeach
                        @endif

                        {{-- Bloques de este día (incluye los de "todos los días"), ya filtrados por fecha --}}
                        <template x-for="b in bloquesPosicionadosDe(dia)" :key="b.pivot_id">
                            <div :title="b.nombre + ' · ' + b.rango + ' · ' + (b.area || 'Sin área')"
                                class="absolute rounded-lg px-1.5 py-1 border-l-4 text-[9px] font-bold overflow-hidden shadow-sm z-10"
                                :class="b.color"
                                :style="`top: ${b.top}px; height: ${b.alto}px; left: calc(${b._left}% + 2px); width: calc(${b._width}% - 4px);`">
                                <p class="truncate leading-tight" x-text="b.nombre"></p>
                                <p class="opacity-80 font-mono truncate leading-tight" x-text="b.rango"></p>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Estado vacío --}}
    <p x-show="contadorBloques === 0" x-cloak class="text-center text-[12px] text-slate-400 py-4">
        No hay turnos asignados en este periodo con los filtros actuales.
    </p>

    {{-- Leyenda --}}
    <div x-show="bloques.length > 0" class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
        <span class="font-bold text-slate-400 uppercase tracking-wider">Turnos activos:</span>
        <template x-for="b in bloquesUnicos" :key="b.id">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm border-l-4" :class="b.color"></span>
                <span x-text="b.nombre"></span>
            </span>
        </template>
    </div>

    {{-- Atajos de teclado (hint) --}}
    <p class="text-xs text-slate-400 text-right">
        Atajos: <kbd class="px-1.5 py-0.5 bg-slate-100 rounded text-[9px] font-bold">←</kbd>
        <kbd class="px-1.5 py-0.5 bg-slate-100 rounded text-[9px] font-bold">→</kbd> anterior / siguiente ·
        <kbd class="px-1.5 py-0.5 bg-slate-100 rounded text-[9px] font-bold">H</kbd> hoy ·
        <kbd class="px-1.5 py-0.5 bg-slate-100 rounded text-[9px] font-bold">M</kbd> mes ·
        <kbd class="px-1.5 py-0.5 bg-slate-100 rounded text-[9px] font-bold">S</kbd> semana
    </p>
</div>

<script>
    function calendarioTurnos(opts) {
        return {
            bloques: opts.bloques || [],
            altoPorHora: opts.altoPorHora,
            horaMin: opts.horaMin,
            horaMax: opts.horaMax,
            clickable: opts.clickable,

            // Estado de navegación
            vista: opts.vista === 'semana' ? 'semana' : 'mes',
            fechaBase: new Date(),
            mesSeleccionado: new Date().getMonth(),
            anioSeleccionado: new Date().getFullYear(),

            // Filtros
            filtroTurno: '',
            filtroArea: '',

            meses: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ],
            diasCortos: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],

            init() {
                this.sincronizarSelects();
                // Cada vez que cambie fechaBase, sincroniza los selects
                this.$watch('fechaBase', () => this.sincronizarSelects());
            },

            /* ─────────── UTILIDADES ─────────── */

            // Fecha local → 'YYYY-MM-DD' (toISOString convierte a UTC y puede correr el día)
            fmt(d) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${dd}`;
            },

            sincronizarSelects() {
                this.mesSeleccionado = this.fechaBase.getMonth();
                this.anioSeleccionado = this.fechaBase.getFullYear();
            },

            lunesDe(fecha) {
                const d = new Date(fecha);
                const dia = d.getDay(); // 0=Dom
                d.setDate(d.getDate() + (dia === 0 ? -6 : 1 - dia));
                d.setHours(0, 0, 0, 0);
                return d;
            },

            /**
             * ¿El bloque aplica en esta fecha concreta?
             * - dia_semana null/undefined  => aplica todos los días
             * - fecha                      => solo ese día exacto
             * - fecha_inicio / fecha_fin   => rango de vigencia (opcionales)
             */
            aplicaEnFecha(b, fechaStr, diaSemana) {
                if (b.dia_semana !== null && b.dia_semana !== undefined && b.dia_semana !== '') {
                    if (Number(b.dia_semana) !== diaSemana) return false;
                }
                if (b.fecha && b.fecha !== fechaStr) return false;
                if (b.fecha_inicio && fechaStr < b.fecha_inicio) return false;
                if (b.fecha_fin && fechaStr > b.fecha_fin) return false;
                return true;
            },

            /* ─────────── GETTERS ─────────── */

            get aniosDisponibles() {
                const base = new Date().getFullYear();
                const anios = new Set([base - 2, base - 1, base, base + 1, base + 2]);
                anios.add(this.fechaBase.getFullYear()); // asegura que el año actual de navegación exista
                return Array.from(anios).sort((a, b) => a - b);
            },

            get areasDisponibles() {
                const set = new Set();
                this.bloques.forEach(b => {
                    if (b.area) set.add(b.area);
                });
                return Array.from(set).sort();
            },

            get bloquesUnicos() {
                const mapa = new Map();
                this.bloques.forEach(b => {
                    if (!mapa.has(b.id)) mapa.set(b.id, b);
                });
                return Array.from(mapa.values());
            },

            // Bloques tras aplicar filtros de turno y área
            get bloquesFiltrados() {
                return this.bloques.filter(b => {
                    if (this.filtroTurno !== '' && String(b.id) !== String(this.filtroTurno)) return false;
                    if (this.filtroArea !== '' && (b.area || '') !== this.filtroArea) return false;
                    return true;
                });
            },

            // ----- Semana -----
            get lunesDeLaSemana() {
                return this.lunesDe(this.fechaBase);
            },

            get diasDeLaSemana() {
                const lunes = this.lunesDeLaSemana;
                const hoy = this.fmt(new Date());
                const resultado = [];
                for (let i = 0; i < 7; i++) {
                    const d = new Date(lunes);
                    d.setDate(lunes.getDate() + i);
                    const fecha = this.fmt(d);
                    resultado.push({
                        fecha,
                        numero: d.getDate(),
                        nombreCorto: this.diasCortos[i],
                        diaSemana: d.getDay(), // 0=Dom ... 6=Sáb
                        esHoy: fecha === hoy,
                    });
                }
                return resultado;
            },

            // ----- Mes -----
            get semanasDelMes() {
                const anio = this.fechaBase.getFullYear();
                const mes = this.fechaBase.getMonth();
                const primero = new Date(anio, mes, 1);
                const ultimo = new Date(anio, mes + 1, 0);
                const hoy = this.fmt(new Date());

                let cursor = this.lunesDe(primero);
                const semanas = [];

                while (cursor <= ultimo) {
                    const semana = [];
                    for (let i = 0; i < 7; i++) {
                        const d = new Date(cursor);
                        d.setDate(cursor.getDate() + i);
                        const fecha = this.fmt(d);
                        const diaSemana = d.getDay();
                        semana.push({
                            fecha,
                            numero: d.getDate(),
                            diaSemana,
                            delMes: d.getMonth() === mes,
                            esHoy: fecha === hoy,
                            bloques: this.bloquesDeFecha(fecha, diaSemana),
                        });
                    }
                    semanas.push(semana);
                    cursor.setDate(cursor.getDate() + 7);
                }
                return semanas;
            },

            get tituloActual() {
                if (this.vista === 'mes') {
                    return `${this.meses[this.fechaBase.getMonth()]} ${this.fechaBase.getFullYear()}`;
                }
                const dias = this.diasDeLaSemana;
                const ini = new Date(dias[0].fecha + 'T00:00:00');
                const fin = new Date(dias[6].fecha + 'T00:00:00');
                const fmt = {
                    day: 'numeric',
                    month: 'short'
                };
                return `${ini.toLocaleDateString('es-MX', fmt)} — ${fin.toLocaleDateString('es-MX', fmt)} ${fin.getFullYear()}`;
            },

            get contadorBloques() {
                if (this.vista === 'mes') {
                    const mes = this.fechaBase.getMonth();
                    return this.semanasDelMes
                        .flat()
                        .filter(c => c.delMes)
                        .reduce((n, c) => n + c.bloques.length, 0);
                }
                return this.diasDeLaSemana.reduce((n, d) => n + this.bloquesDeFecha(d.fecha, d.diaSemana).length,
                    0);
            },

            /* ─────────── MÉTODOS ─────────── */

            // Bloques que aplican en una fecha, ordenados por hora de inicio
            bloquesDeFecha(fechaStr, diaSemana) {
                return this.bloquesFiltrados
                    .filter(b => this.aplicaEnFecha(b, fechaStr, diaSemana))
                    .sort((a, b) => a.top - b.top);
            },

            /**
             * Para la vista semanal: devuelve los bloques del día con _left/_width (%)
             * para que los turnos que se traslapan se vean lado a lado y no encimados.
             */
            bloquesPosicionadosDe(dia) {
                const lista = this.bloquesDeFecha(dia.fecha, dia.diaSemana).map(b => ({
                    ...b,
                    _left: 0,
                    _width: 100
                }));

                let cluster = [];
                let finCluster = -1;

                const cerrar = () => {
                    if (!cluster.length) return;
                    const carriles = []; // fin (px) de cada carril
                    cluster.forEach(b => {
                        let c = carriles.findIndex(fin => fin <= b.top);
                        if (c === -1) {
                            c = carriles.length;
                            carriles.push(0);
                        }
                        carriles[c] = b.top + b.alto;
                        b._carril = c;
                    });
                    const n = carriles.length;
                    cluster.forEach(b => {
                        b._width = 100 / n;
                        b._left = (100 / n) * b._carril;
                    });
                    cluster = [];
                };

                lista.forEach(b => {
                    if (cluster.length && b.top >= finCluster) cerrar();
                    cluster.push(b);
                    finCluster = Math.max(cluster.length === 1 ? 0 : finCluster, b.top + b.alto);
                });
                cerrar();

                return lista;
            },

            limpiarFiltros() {
                this.filtroTurno = '';
                this.filtroArea = '';
            },

            // ---- Navegación (según vista) ----
            anterior() {
                const d = new Date(this.fechaBase);
                if (this.vista === 'mes') {
                    this.fechaBase = new Date(d.getFullYear(), d.getMonth() - 1, 1);
                } else {
                    d.setDate(d.getDate() - 7);
                    this.fechaBase = d;
                }
            },

            siguiente() {
                const d = new Date(this.fechaBase);
                if (this.vista === 'mes') {
                    this.fechaBase = new Date(d.getFullYear(), d.getMonth() + 1, 1);
                } else {
                    d.setDate(d.getDate() + 7);
                    this.fechaBase = d;
                }
            },

            hoy() {
                this.fechaBase = new Date();
            },

            irAlMes() {
                // Día 1 del mes/año seleccionado (el select puede devolver strings)
                this.fechaBase = new Date(Number(this.anioSeleccionado), Number(this.mesSeleccionado), 1);
            },

            irADia(fechaStr) {
                this.fechaBase = new Date(fechaStr + 'T00:00:00');
                this.vista = 'semana';
            },

            alPresionarTecla(e) {
                // Ignora si el usuario está escribiendo en un input/select/textarea
                const tag = (e.target.tagName || '').toLowerCase();
                if (['input', 'textarea', 'select'].includes(tag)) return;
                if (e.ctrlKey || e.metaKey || e.altKey) return;

                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.anterior();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.siguiente();
                } else if (e.key === 'h' || e.key === 'H') {
                    this.hoy();
                } else if (e.key === 'm' || e.key === 'M') {
                    this.vista = 'mes';
                } else if (e.key === 's' || e.key === 'S') {
                    this.vista = 'semana';
                }
            },
        };
    }
</script>
