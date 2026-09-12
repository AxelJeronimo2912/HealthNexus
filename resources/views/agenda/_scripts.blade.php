<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pacienteSelect = document.getElementById('paciente_id');
        const medicoSelect = document.getElementById('medico_id');
        const fechaInput = document.getElementById('fecha');
        const horaInput = document.getElementById('hora');
        const avisoBloqueo = document.getElementById('aviso-bloqueo');
        const loadingMedicos = document.getElementById('loading-medicos');

        // Detectar si el paciente ya está asignado a un médico
        function obtenerMedicoAsignado() {
            const opt = pacienteSelect.options[pacienteSelect.selectedIndex];
            return {
                id: opt?.dataset?.medicoAsignado || null,
                nombre: opt?.dataset?.medicoNombre || null,
            };
        }

        // Cargar médicos disponibles
        async function cargarMedicos() {
            const pacienteId = pacienteSelect.value;
            const fecha = fechaInput.value;
            const hora = horaInput.value;

            if (!fecha || !hora) return;

            loadingMedicos.classList.remove('hidden');

            const params = new URLSearchParams({
                fecha,
                hora
            });
            if (pacienteId) params.append('paciente_id', pacienteId);

            try {
                const res = await fetch(`{{ route('agenda.medicos-disponibles') }}?${params}`);
                const medicos = await res.json();

                medicoSelect.innerHTML = '<option value="">— Selecciona un médico —</option>';
                medicos.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;

                    // Etiqueta con aviso si está ocupado
                    opt.textContent = `${m.nombre} (${m.rol})` + (m.ocupado ? ' — OCUPADO' : '');
                    opt.dataset.ocupado = m.ocupado ? '1' : '0';

                    // Deshabilitar si está ocupado
                    if (m.ocupado) {
                        opt.disabled = true;
                    }

                    medicoSelect.appendChild(opt);
                });

                medicoSelect.disabled = false;

                // Si el paciente tiene médico asignado, autoseleccionarlo
                const asignado = obtenerMedicoAsignado();
                if (asignado.id) {
                    medicoSelect.value = asignado.id;
                    avisoBloqueo.classList.remove('hidden');
                } else {
                    avisoBloqueo.classList.add('hidden');
                }
            } catch (e) {
                medicoSelect.innerHTML = '<option value="">Error al cargar médicos</option>';
            } finally {
                loadingMedicos.classList.add('hidden');
            }
        }

        // Escuchar cambios
        pacienteSelect.addEventListener('change', cargarMedicos);
        fechaInput.addEventListener('change', cargarMedicos);
        horaInput.addEventListener('change', cargarMedicos);

        // Cargar al inicio si hay valores
        if (pacienteSelect.value && fechaInput.value && horaInput.value) {
            cargarMedicos();
        }
    });
</script>
