<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ---- Cálculo automático de edad ----
        const fechaInput = document.getElementById('fecha_nacimiento');
        const edadInput = document.getElementById('edad');

        function calcularEdad() {
            if (!fechaInput || !edadInput) return;
            if (!fechaInput.value) {
                edadInput.value = '';
                return;
            }
            const nacimiento = new Date(fechaInput.value);
            const hoy = new Date();
            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const m = hoy.getMonth() - nacimiento.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }
            edadInput.value = edad >= 0 ? edad + ' años' : '';
        }

        if (fechaInput) {
            fechaInput.addEventListener('change', calcularEdad);
            calcularEdad();
        }

        // ---- Nacionalidad: CURP/Pasaporte + Estado/País de nacimiento ----
        const nacionalidadSelect = document.getElementById('nacionalidad');
        const curpContainer = document.getElementById('curp-container');
        const pasaporteContainer = document.getElementById('pasaporte-container');
        const estadoNacimientoContainer = document.getElementById('estado_nacimiento_container');
        const paisNacimientoContainer = document.getElementById('pais_nacimiento_container');

        function toggleDocumento() {
            if (!nacionalidadSelect) return;
            const esMexicana = nacionalidadSelect.value === 'MEXICANA';

            if (curpContainer) curpContainer.classList.toggle('hidden', !esMexicana);
            if (pasaporteContainer) pasaporteContainer.classList.toggle('hidden', esMexicana);

            if (!esMexicana && curpContainer) {
                const curpInput = curpContainer.querySelector('input');
                if (curpInput) curpInput.value = '';
            } else if (esMexicana && pasaporteContainer) {
                const pasaporteInput = pasaporteContainer.querySelector('input');
                if (pasaporteInput) pasaporteInput.value = '';
            }
        }

        function toggleNacimiento() {
            if (!nacionalidadSelect) return;
            const esMexicana = nacionalidadSelect.value === 'MEXICANA';

            if (estadoNacimientoContainer) {
                estadoNacimientoContainer.classList.toggle('hidden', !esMexicana);
            }
            if (paisNacimientoContainer) {
                paisNacimientoContainer.classList.toggle('hidden', esMexicana);
            }

            if (!esMexicana && estadoNacimientoContainer) {
                const sel = estadoNacimientoContainer.querySelector('select');
                if (sel) sel.value = '';
            }
            if (esMexicana && paisNacimientoContainer) {
                const input = paisNacimientoContainer.querySelector('input');
                if (input) input.value = '';
            }
        }

        if (nacionalidadSelect) {
            nacionalidadSelect.addEventListener('change', function() {
                toggleDocumento();
                toggleNacimiento();
            });
            toggleDocumento();
            toggleNacimiento();
        }

        // ---- Municipios dinámicos según estado ----
        const estadoSelect = document.getElementById('estado_id');
        const municipioSelect = document.getElementById('municipio_id');

        if (estadoSelect && municipioSelect) {
            estadoSelect.addEventListener('change', function() {
                const estadoId = this.value;
                municipioSelect.innerHTML = '<option value="">Cargando...</option>';

                if (!estadoId) {
                    municipioSelect.innerHTML = '<option value="">— Selecciona un municipio —</option>';
                    return;
                }

                fetch(`/estados/${estadoId}/municipios`)
                    .then(r => r.json())
                    .then(data => {
                        municipioSelect.innerHTML =
                            '<option value="">— Selecciona un municipio —</option>';
                        data.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.id;
                            opt.textContent = m.nombre;
                            municipioSelect.appendChild(opt);
                        });
                    })
                    .catch(() => {
                        municipioSelect.innerHTML = '<option value="">Error al cargar</option>';
                    });
            });
        }
    });
</script>
