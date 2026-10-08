<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>

<script>
    function agendaData() {
        let calendar = null;

        return {
            // ============================================================
            // ESTADO
            // ============================================================
            modalCrear: false,
            modalDetalle: false,
            citaDetalle: {},

            avisoBloqueo: false,
            avisoNombreMedico: '',

            cargandoMedicos: false,
            medicos: [],
            mensajeMedicos: '— Selecciona fecha y hora —',
            medicoSelectDisabled: true,

            form: {
                paciente_id: '',
                especialidad_id: '',
                servicio_id: '',
                medico_id: '',
                fecha: '{{ now()->format('Y-m-d') }}',
                hora: '09:00',
                duracion_minutos: '30',
                motivo: '',
                notas: '',
            },

            // ============================================================
            // FILTROS
            // ============================================================
            filtros: {
                estados: [],
                triages: [],
                medico_id: '',
                especialidad_id: '',
            },

            estadosDisponibles: [{
                    value: 'programada',
                    label: 'Programada'
                },
                {
                    value: 'confirmada',
                    label: 'Confirmada'
                },
                {
                    value: 'en_curso',
                    label: 'En curso'
                },
                {
                    value: 'atendida',
                    label: 'Atendida'
                },
                {
                    value: 'cancelada',
                    label: 'Cancelada'
                },
            ],

            triagesDisponibles: [{
                    value: 'rojo',
                    label: 'Rojo',
                    color: '#f43f5e'
                },
                {
                    value: 'naranja',
                    label: 'Naranja',
                    color: '#f59e0b'
                },
                {
                    value: 'amarillo',
                    label: 'Amarillo',
                    color: '#facc15'
                },
                {
                    value: 'verde',
                    label: 'Verde',
                    color: '#10b981'
                },
                {
                    value: 'azul',
                    label: 'Azul',
                    color: '#0ea5e9'
                },
                {
                    value: 'sin_triage',
                    label: 'Sin triage',
                    color: '#94a3b8'
                },
            ],

            citasTotales: 0,
            citasFiltradas: 0,
            filtrando: false,
            _filtrandoTimeout: null,
            _eventosOriginales: [],

            get filtrosActivos() {
                let count = 0;
                if (this.filtros.estados.length) count += this.filtros.estados.length;
                if (this.filtros.triages.length) count += this.filtros.triages.length;
                if (this.filtros.medico_id) count += 1;
                if (this.filtros.especialidad_id) count += 1;
                return count;
            },

            // ============================================================
            // INICIALIZACIÓN
            // ============================================================
            initCalendar() {
                this.cargarFiltrosGuardados();

                const el = document.getElementById('calendar');

                calendar = new FullCalendar.Calendar(el, {
                    locale: 'es',
                    timeZone: 'local',
                    initialView: window.innerWidth < 768 ? 'listWeek' : 'timeGridWeek',
                    firstDay: 1,
                    height: 'auto',
                    nowIndicator: true,
                    navLinks: true,
                    allDaySlot: false,
                    slotMinTime: '07:00:00',
                    slotMaxTime: '21:00:00',
                    slotDuration: '00:30:00',
                    expandRows: true,
                    dayMaxEvents: 3,

                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día',
                        list: 'Lista'
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    },

                    events: {
                        url: @js(route('agenda.eventos')),
                        failure: () => console.error('No se pudieron cargar las citas'),
                        success: (eventos) => {
                            this._eventosOriginales = eventos;
                            this.citasTotales = eventos.length;
                            this.aplicarFiltros();
                        },
                    },

                    eventClick: (info) => {
                        info.jsEvent.preventDefault();
                        this.abrirDetalle(info.event.extendedProps);
                    },

                    dateClick: (info) => {
                        const [fecha, resto] = info.dateStr.split('T');
                        const hoy = new Date().toISOString().slice(0, 10);
                        if (fecha < hoy) return;
                        this.abrirModal(fecha, resto ? resto.slice(0, 5) : null);
                    },
                });

                calendar.render();

                @if ($errors->any())
                    this.modalCrear = true;
                @endif
            },

            // ============================================================
            // MODALES
            // ============================================================
            abrirModal(fecha = null, hora = null) {
                if (fecha) this.form.fecha = fecha;
                if (hora) this.form.hora = hora;
                this.modalCrear = true;
                this.$nextTick(() => this.cargarMedicos());
            },

            abrirDetalle(data) {
                this.citaDetalle = data;
                this.modalDetalle = true;
            },

            // ============================================================
            // HELPERS
            // ============================================================
            obtenerMedicoAsignado() {
                const select = document.getElementById('paciente_id');
                if (!select) return {
                    id: null,
                    nombre: null
                };

                const opt = select.options[select.selectedIndex];
                return {
                    id: opt?.dataset?.medicoAsignado || null,
                    nombre: opt?.dataset?.medicoNombre || null,
                };
            },

            resetMedicoSelect(mensaje, disabled = true) {
                this.mensajeMedicos = mensaje;
                this.medicoSelectDisabled = disabled;
                this.medicos = [];
                this.form.medico_id = '';
            },

            // ============================================================
            // CARGA DE MÉDICOS DISPONIBLES
            // ============================================================
            async cargarMedicos() {
                const {
                    paciente_id,
                    especialidad_id,
                    servicio_id,
                    fecha,
                    hora,
                    duracion_minutos,
                } = this.form;

                if (!fecha || !hora) {
                    this.resetMedicoSelect('— Selecciona fecha y hora —');
                    return;
                }

                if (!especialidad_id && !servicio_id) {
                    this.resetMedicoSelect('— Selecciona una especialidad o servicio —');
                    return;
                }

                this.cargandoMedicos = true;
                this.medicos = [];
                this.form.medico_id = '';
                this.medicoSelectDisabled = true;

                const params = new URLSearchParams({
                    fecha,
                    hora,
                    duracion_minutos: duracion_minutos || 30,
                });
                if (paciente_id) params.append('paciente_id', paciente_id);
                if (especialidad_id) params.append('especialidad_id', especialidad_id);
                if (servicio_id) params.append('servicio_id', servicio_id);

                try {
                    const res = await fetch(`{{ route('agenda.medicos-disponibles') }}?${params}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!res.ok) throw new Error(`HTTP ${res.status}`);

                    const data = await res.json();

                    if (data && data.error) {
                        this.resetMedicoSelect(data.message || 'Error al cargar médicos');
                        return;
                    }

                    if (!Array.isArray(data) || data.length === 0) {
                        this.resetMedicoSelect('— Sin médicos disponibles —');
                        return;
                    }

                    this.medicos = data;
                    this.mensajeMedicos = '— Selecciona un médico —';
                    this.medicoSelectDisabled = false;

                    const asignado = this.obtenerMedicoAsignado();

                    if (asignado.id) {
                        const medico = this.medicos.find(
                            m => String(m.id) === String(asignado.id) && !m.ocupado
                        );
                        if (medico) this.form.medico_id = medico.id;

                        this.avisoBloqueo = true;
                        this.avisoNombreMedico = asignado.nombre || '';
                    } else {
                        this.avisoBloqueo = false;
                        this.avisoNombreMedico = '';
                    }
                } catch (e) {
                    console.error('Error al cargar médicos:', e);
                    this.resetMedicoSelect('— Error al cargar médicos —');
                } finally {
                    this.cargandoMedicos = false;
                }
            },

            // ============================================================
            // FILTROS
            // ============================================================
            cargarFiltrosGuardados() {
                try {
                    const guardado = JSON.parse(localStorage.getItem('agenda.filtros') || '{}');
                    if (Array.isArray(guardado.estados)) this.filtros.estados = guardado.estados;
                    if (Array.isArray(guardado.triages)) this.filtros.triages = guardado.triages;
                    if (guardado.medico_id) this.filtros.medico_id = guardado.medico_id;
                    if (guardado.especialidad_id) this.filtros.especialidad_id = guardado.especialidad_id;
                } catch (e) {
                    /* ignorar */
                }
            },

            guardarFiltros() {
                try {
                    localStorage.setItem('agenda.filtros', JSON.stringify(this.filtros));
                } catch (e) {
                    /* ignorar */
                }
            },

            toggleEstado(valor) {
                const idx = this.filtros.estados.indexOf(valor);
                if (idx >= 0) this.filtros.estados.splice(idx, 1);
                else this.filtros.estados.push(valor);
                this.aplicarFiltros();
            },

            toggleTriage(valor) {
                const idx = this.filtros.triages.indexOf(valor);
                if (idx >= 0) this.filtros.triages.splice(idx, 1);
                else this.filtros.triages.push(valor);
                this.aplicarFiltros();
            },

            limpiarFiltros() {
                this.filtros = {
                    estados: [],
                    triages: [],
                    medico_id: '',
                    especialidad_id: '',
                };
                this.aplicarFiltros();
            },

            aplicarFiltros() {
                // Feedback visual en el contador
                this.filtrando = true;
                clearTimeout(this._filtrandoTimeout);
                this._filtrandoTimeout = setTimeout(() => this.filtrando = false, 300);

                const todosLosEventos = calendar ? calendar.getEvents() : [];

                let visibles = 0;

                todosLosEventos.forEach(ev => {
                    const p = ev.extendedProps || {};
                    let cumple = true;

                    // Estado
                    if (this.filtros.estados.length && !this.filtros.estados.includes(p.estado_raw)) {
                        cumple = false;
                    }

                    // Triage
                    if (cumple && this.filtros.triages.length) {
                        const triageVal = p.triage_raw || 'sin_triage';
                        if (!this.filtros.triages.includes(triageVal)) cumple = false;
                    }

                    // Médico
                    if (cumple && this.filtros.medico_id && String(p.medico_id) !== String(this.filtros
                            .medico_id)) {
                        cumple = false;
                    }

                    // Especialidad
                    if (cumple && this.filtros.especialidad_id && String(p.especialidad_id) !== String(this
                            .filtros.especialidad_id)) {
                        cumple = false;
                    }

                    ev.setProp('display', cumple ? 'auto' : 'none');

                    if (cumple) visibles++;
                });

                this.citasFiltradas = visibles;
                this.guardarFiltros();
            },
        };
    }
</script>

