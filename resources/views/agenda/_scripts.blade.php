@push('scripts')
@if (request()->routeIs('agenda.index'))
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>
@endif
<script>
    function agendaData() {
        let calendar = null;

        return {
            modalCrear: false,
            modalDetalle: false,
            citaDetalle: {},
            calendarError: '',

            avisoBloqueo: false,
            avisoNombreMedico: '',
            cargandoMedicos: false,
            medicos: [],
            mensajeMedicos: '— Selecciona fecha y hora —',
            medicoSelectDisabled: true,

            form: {
                paciente_id: @js(old('paciente_id', '')),
                especialidad_id: @js(old('especialidad_id', '')),
                servicio_id: @js(old('servicio_id', '')),
                medico_id: @js(old('medico_id', '')),
                fecha: @js(old('fecha', $fechaSeleccionada ?? now() - > format('Y-m-d'))),
                hora: @js(old('hora', $horaSeleccionada ?? '09:00')),
                duracion_minutos: @js(old('duracion_minutos', '30')),
                motivo: @js(old('motivo', '')),
                notas: @js(old('notas', '')),
            },

            initCalendar() {
                const element = document.getElementById('calendar');

                if (!element || !window.FullCalendar) {
                    this.calendarError = 'No se pudo cargar el calendario. Recarga la página para intentarlo de nuevo.';
                    console.error('FullCalendar no está disponible.');
                    return;
                }

                calendar = new FullCalendar.Calendar(element, {
                    locale: 'es',
                    initialDate: @js(isset($fecha) ? $fecha - > toDateString() : ($fechaSeleccionada ?? now() - > format('Y-m-d'))),
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
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
                    },
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día',
                        list: 'Lista',
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                    },
                    events: {
                        url: @js(route('agenda.eventos')),
                        success: () => {
                            this.calendarError = '';
                        },
                        failure: () => {
                            this.calendarError = 'No se pudieron cargar las citas.';
                            console.error('No se pudieron cargar las citas del calendario.');
                        },
                    },
                    eventClick: (info) => {
                        info.jsEvent.preventDefault();
                        this.abrirDetalle(info.event.extendedProps);
                    },
                    dateClick: (info) => {
                        const fecha = info.dateStr.slice(0, 10);
                        const hora = info.dateStr.includes('T') ? info.dateStr.slice(11, 16) : null;

                        if (fecha < @js(now() - > toDateString())) {
                            return;
                        }

                        this.abrirModal(fecha, hora);
                    },
                });

                calendar.render();

                @if($errors - > any())
                this.modalCrear = true;
                this.$nextTick(() => this.cargarMedicos());
                @endif
            },

            abrirModal(fecha = null, hora = null) {
                if (fecha) {
                    this.form.fecha = fecha;
                }
                if (hora) {
                    this.form.hora = hora;
                }

                this.modalCrear = true;
                this.$nextTick(() => this.cargarMedicos());
            },

            abrirDetalle(data) {
                this.citaDetalle = data;
                this.modalDetalle = true;
            },

            obtenerMedicoAsignado() {
                const select = document.getElementById('paciente_id');
                if (!select) {
                    return {
                        id: null,
                        nombre: null
                    };
                }

                const option = select.options[select.selectedIndex];

                return {
                    id: option?.dataset?.medicoAsignado || null,
                    nombre: option?.dataset?.medicoNombre || null,
                };
            },

            resetMedicoSelect(mensaje, disabled = true) {
                this.mensajeMedicos = mensaje;
                this.medicoSelectDisabled = disabled;
                this.medicos = [];
                this.form.medico_id = '';
            },

            async cargarMedicos() {
                const {
                    paciente_id,
                    especialidad_id,
                    servicio_id,
                    fecha,
                    hora,
                    duracion_minutos,
                } = this.form;
                const medicoPreseleccionado = this.form.medico_id;

                if (!fecha || !hora) {
                    this.resetMedicoSelect('— Selecciona fecha y hora —');
                    return;
                }

                if (!especialidad_id && !servicio_id) {
                    this.resetMedicoSelect('— Selecciona una especialidad o servicio —');
                    this.avisoBloqueo = false;
                    this.avisoNombreMedico = '';
                    return;
                }

                this.cargandoMedicos = true;
                this.resetMedicoSelect('— Cargando médicos... —');

                const params = new URLSearchParams({
                    fecha,
                    hora,
                    duracion_minutos: duracion_minutos || '30',
                });
                if (paciente_id) params.append('paciente_id', paciente_id);
                if (especialidad_id) params.append('especialidad_id', especialidad_id);
                if (servicio_id) params.append('servicio_id', servicio_id);

                try {
                    const response = await fetch(
                        `{{ route('agenda.medicos-disponibles') }}?${params.toString()}`, {
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        }
                    );

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();

                    if (data?.error) {
                        this.resetMedicoSelect(data.message || 'Error al cargar médicos');
                        return;
                    }

                    if (!Array.isArray(data) || data.length === 0) {
                        this.resetMedicoSelect('— Sin médicos disponibles —');
                        this.avisoBloqueo = false;
                        this.avisoNombreMedico = '';
                        return;
                    }

                    this.medicos = data;
                    this.mensajeMedicos = '— Selecciona un médico —';
                    this.medicoSelectDisabled = false;

                    const asignado = this.obtenerMedicoAsignado();
                    this.avisoBloqueo = Boolean(asignado.id);
                    this.avisoNombreMedico = asignado.nombre || '';

                    if (asignado.id) {
                        const medicoAsignado = this.medicos.find(
                            (medico) => String(medico.id) === String(asignado.id) && !medico.ocupado
                        );
                        if (medicoAsignado) {
                            this.form.medico_id = String(medicoAsignado.id);
                        }
                    } else if (medicoPreseleccionado) {
                        const medicoPreseleccionadoDisponible = this.medicos.find(
                            (medico) => String(medico.id) === String(medicoPreseleccionado) && !medico.ocupado
                        );
                        if (medicoPreseleccionadoDisponible) {
                            this.form.medico_id = String(medicoPreseleccionadoDisponible.id);
                        }
                    }
                } catch (error) {
                    console.error('Error al cargar médicos:', error);
                    this.resetMedicoSelect('— Error al cargar médicos —');
                } finally {
                    this.cargandoMedicos = false;
                }
            },
        };
    }
</script>
<script>
    function inicializarFormularioAgenda() {
        if (document.getElementById('calendar') || !document.getElementById('form-cita')) {
            return;
        }

        const pacienteSelect = document.getElementById('paciente_id');
        const medicoSelect = document.getElementById('medico_id');
        const especialidadSelect = document.getElementById('especialidad_id');
        const servicioSelect = document.getElementById('servicio_id');
        const fechaInput = document.getElementById('fecha');
        const horaInput = document.getElementById('hora');
        const duracionSelect = document.getElementById('duracion_minutos');
        const avisoBloqueo = document.getElementById('aviso-bloqueo');
        const loadingMedicos = document.getElementById('loading-medicos');

        function obtenerMedicoAsignado() {
            const option = pacienteSelect.options[pacienteSelect.selectedIndex];

            return {
                id: option?.dataset?.medicoAsignado || null,
                nombre: option?.dataset?.medicoNombre || null,
            };
        }

        function resetMedicoSelect(mensaje, disabled = true) {
            medicoSelect.innerHTML = '';
            medicoSelect.add(new Option(mensaje, ''));
            medicoSelect.disabled = disabled;
        }

        async function cargarMedicos() {
            const pacienteId = pacienteSelect.value;
            const fecha = fechaInput.value;
            const hora = horaInput.value;
            const duracion = duracionSelect.value || '30';
            const especialidadId = especialidadSelect.value;
            const servicioId = servicioSelect.value;

            if (!fecha || !hora) {
                resetMedicoSelect('— Selecciona fecha y hora —');
                return;
            }

            if (!especialidadId && !servicioId) {
                resetMedicoSelect('— Selecciona una especialidad o servicio —');
                return;
            }

            loadingMedicos?.classList.remove('hidden');
            medicoSelect.disabled = true;

            const params = new URLSearchParams({
                fecha,
                hora,
                duracion_minutos: duracion,
            });
            if (pacienteId) params.append('paciente_id', pacienteId);
            if (especialidadId) params.append('especialidad_id', especialidadId);
            if (servicioId) params.append('servicio_id', servicioId);

            try {
                const response = await fetch(
                    `{{ route('agenda.medicos-disponibles') }}?${params.toString()}`, {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                if (data?.error) {
                    resetMedicoSelect(data.message || 'Error al cargar médicos');
                    return;
                }

                if (!Array.isArray(data) || data.length === 0) {
                    resetMedicoSelect('— Sin médicos disponibles —');
                    return;
                }

                medicoSelect.innerHTML = '<option value="">— Selecciona un médico —</option>';
                data.forEach((medico) => {
                    const option = new Option(
                        `${medico.nombre} (${medico.rol})${medico.ocupado ? ' — OCUPADO' : ''}`,
                        medico.id
                    );
                    option.disabled = Boolean(medico.ocupado);
                    medicoSelect.add(option);
                });
                medicoSelect.disabled = false;

                const asignado = obtenerMedicoAsignado();
                if (asignado.id) {
                    const optionAsignada = Array.from(medicoSelect.options).find(
                        (option) => String(option.value) === String(asignado.id)
                    );
                    if (optionAsignada && !optionAsignada.disabled) {
                        medicoSelect.value = asignado.id;
                    }
                    avisoBloqueo?.classList.remove('hidden');
                } else {
                    avisoBloqueo?.classList.add('hidden');
                }
            } catch (error) {
                console.error('Error al cargar médicos:', error);
                resetMedicoSelect('— Error al cargar médicos —');
            } finally {
                loadingMedicos?.classList.add('hidden');
            }
        }

        pacienteSelect.addEventListener('change', () => {
            const asignado = obtenerMedicoAsignado();
            avisoBloqueo?.classList.toggle('hidden', !asignado.id);
            cargarMedicos();
        });
        fechaInput.addEventListener('change', cargarMedicos);
        horaInput.addEventListener('change', cargarMedicos);
        duracionSelect.addEventListener('change', cargarMedicos);
        especialidadSelect.addEventListener('change', cargarMedicos);
        servicioSelect.addEventListener('change', cargarMedicos);

        if (fechaInput.value && horaInput.value && pacienteSelect.value) {
            cargarMedicos();
        } else {
            resetMedicoSelect('— Selecciona paciente, fecha y hora —');
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarFormularioAgenda, {
            once: true
        });
    } else {
        inicializarFormularioAgenda();
    }
</script>
@endpush
