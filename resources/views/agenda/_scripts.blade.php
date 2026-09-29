<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============ Referencias del DOM ============
        const pacienteSelect = document.getElementById('paciente_id');
        const medicoSelect = document.getElementById('medico_id');
        const especialidadSelect = document.getElementById('especialidad_id');
        const servicioSelect = document.getElementById('servicio_id');
        const fechaInput = document.getElementById('fecha');
        const horaInput = document.getElementById('hora');
        const duracionSelect = document.getElementById('duracion_minutos');
        const avisoBloqueo = document.getElementById('aviso-bloqueo');
        const loadingMedicos = document.getElementById('loading-medicos');

        // ============ Detectar médico asignado al paciente ============
        function obtenerMedicoAsignado() {
            if (!pacienteSelect) return {
                id: null,
                nombre: null
            };
            const opt = pacienteSelect.options[pacienteSelect.selectedIndex];
            return {
                id: opt?.dataset?.medicoAsignado || null,
                nombre: opt?.dataset?.medicoNombre || null,
            };
        }

        // ============ Reset del select de médicos ============
        function resetMedicoSelect(mensaje, disabled = true) {
            medicoSelect.innerHTML = `<option value="">${mensaje}</option>`;
            medicoSelect.disabled = disabled;
        }

        // ============ Cargar médicos disponibles ============
        async function cargarMedicos() {
            const pacienteId = pacienteSelect?.value || '';
            const fecha = fechaInput?.value || '';
            const hora = horaInput?.value || '';
            const duracion = duracionSelect?.value || 30;
            const especialidadId = especialidadSelect?.value || '';
            const servicioId = servicioSelect?.value || '';

            // ⛔ Regla 1: se requieren fecha y hora
            if (!fecha || !hora) {
                resetMedicoSelect('— Selecciona paciente, fecha y hora —');
                return;
            }

            // ⛔ Regla 2: se requiere al menos especialidad o servicio
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
                const res = await fetch(`{{ route('agenda.medicos-disponibles') }}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}`);
                }

                const medicos = await res.json();

                // Si el backend devuelve { error: true, message: "..." }
                if (medicos && medicos.error) {
                    resetMedicoSelect(medicos.message || 'Error al cargar médicos');
                    return;
                }

                medicoSelect.innerHTML = '<option value="">— Selecciona un médico —</option>';

                if (!Array.isArray(medicos) || medicos.length === 0) {
                    resetMedicoSelect('— Sin médicos disponibles —');
                    return;
                }

                medicos.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = `${m.nombre} (${m.rol})` + (m.ocupado ? ' — OCUPADO' : '');
                    opt.dataset.ocupado = m.ocupado ? '1' : '0';
                    if (m.ocupado) opt.disabled = true;
                    medicoSelect.appendChild(opt);
                });

                medicoSelect.disabled = false;

                // ============ Autoselección de médico asignado ============
                const asignado = obtenerMedicoAsignado();
                if (asignado.id) {
                    const opcionAsignada = Array.from(medicoSelect.options)
                        .find(o => String(o.value) === String(asignado.id));

                    if (opcionAsignada && !opcionAsignada.disabled) {
                        medicoSelect.value = asignado.id;
                    }
                    avisoBloqueo?.classList.remove('hidden');
                } else {
                    avisoBloqueo?.classList.add('hidden');
                }
            } catch (e) {
                console.error('Error al cargar médicos:', e);
                resetMedicoSelect('— Error al cargar médicos —');
            } finally {
                loadingMedicos?.classList.add('hidden');
            }
        }

        // ============ Listeners ============
        pacienteSelect?.addEventListener('change', cargarMedicos);
        fechaInput?.addEventListener('change', cargarMedicos);
        horaInput?.addEventListener('change', cargarMedicos);
        duracionSelect?.addEventListener('change', cargarMedicos);
        especialidadSelect?.addEventListener('change', cargarMedicos);
        servicioSelect?.addEventListener('change', cargarMedicos);

        // ============ Aviso de bloqueo al cambiar paciente ============
        pacienteSelect?.addEventListener('change', function() {
            const asignado = obtenerMedicoAsignado();
            if (asignado.id) {
                avisoBloqueo?.classList.remove('hidden');
                const aviso = avisoBloqueo?.querySelector('.aviso-nombre');
                if (aviso && asignado.nombre) {
                    aviso.textContent = asignado.nombre;
                }
            } else {
                avisoBloqueo?.classList.add('hidden');
            }
        });

        // ============ Carga inicial (si viene con old()) ============
        if (fechaInput?.value && horaInput?.value) {
            cargarMedicos();
        } else {
            resetMedicoSelect('— Selecciona paciente, fecha y hora —');
        }
    });
</script>
