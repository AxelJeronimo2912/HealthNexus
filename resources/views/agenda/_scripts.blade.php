<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pacienteSelect = document.getElementById('paciente_id');
        const medicoSelect = document.getElementById('medico_id');
        const especialidadSelect = document.getElementById('especialidad_id');
        const fechaInput = document.getElementById('fecha');
        const horaInput = document.getElementById('hora');
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

        // ============ Cargar médicos disponibles ============
        async function cargarMedicos() {
            const pacienteId = pacienteSelect?.value || '';
            const fecha = fechaInput?.value || '';
            const hora = horaInput?.value || '';
            const especialidadId = especialidadSelect?.value || '';

            if (!fecha || !hora) return;

            loadingMedicos?.classList.remove('hidden');

            const params = new URLSearchParams({
                fecha,
                hora
            });
            if (pacienteId) params.append('paciente_id', pacienteId);
            if (especialidadId) params.append('especialidad_id', especialidadId);

            try {
                const res = await fetch(`{{ route('agenda.medicos-disponibles') }}?${params}`);
                const medicos = await res.json();

                medicoSelect.innerHTML = '<option value="">— Selecciona un médico —</option>';

                if (!Array.isArray(medicos) || medicos.length === 0) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = '— Sin médicos disponibles —';
                    opt.disabled = true;
                    medicoSelect.appendChild(opt);
                    medicoSelect.disabled = true;
                } else {
                    medicos.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.id;

                        // Etiqueta con aviso si está ocupado
                        opt.textContent = `${m.nombre} (${m.rol})` + (m.ocupado ? ' — OCUPADO' :
                            '');
                        opt.dataset.ocupado = m.ocupado ? '1' : '0';

                        // Deshabilitar si está ocupado
                        if (m.ocupado) {
                            opt.disabled = true;
                        }

                        medicoSelect.appendChild(opt);
                    });

                    medicoSelect.disabled = false;
                }

                // Si el paciente tiene médico asignado, autoseleccionarlo
                const asignado = obtenerMedicoAsignado();
                if (asignado.id) {
                    // Buscar la opción que corresponde al médico asignado
                    const opcionAsignada = Array.from(medicoSelect.options)
                        .find(o => o.value == asignado.id);

                    if (opcionAsignada && !opcionAsignada.disabled) {
                        medicoSelect.value = asignado.id;
                        avisoBloqueo?.classList.remove('hidden');
                    } else {
                        // El médico asignado no está disponible → mostrar aviso
                        avisoBloqueo?.classList.remove('hidden');
                    }
                } else {
                    avisoBloqueo?.classList.add('hidden');
                }
            } catch (e) {
                console.error('Error al cargar médicos:', e);
                medicoSelect.innerHTML = '<option value="">Error al cargar médicos</option>';
                medicoSelect.disabled = true;
            } finally {
                loadingMedicos?.classList.add('hidden');
            }
        }

        // ============ Listeners ============
        pacienteSelect?.addEventListener('change', cargarMedicos);
        fechaInput?.addEventListener('change', cargarMedicos);
        horaInput?.addEventListener('change', cargarMedicos);
        especialidadSelect?.addEventListener('change', cargarMedicos);

        // Cargar al inicio si hay valores
        if (pacienteSelect?.value && fechaInput?.value && horaInput?.value) {
            cargarMedicos();
        }

        // ============ Mostrar aviso de bloqueo al cambiar paciente ============
        pacienteSelect?.addEventListener('change', function() {
            const asignado = obtenerMedicoAsignado();
            if (asignado.id) {
                avisoBloqueo?.classList.remove('hidden');
                if (asignado.nombre) {
                    const aviso = avisoBloqueo?.querySelector('.aviso-nombre');
                    if (aviso) aviso.textContent = asignado.nombre;
                }
            } else {
                avisoBloqueo?.classList.add('hidden');
            }
        });
    });
</script>
