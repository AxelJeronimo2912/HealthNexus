<script>
    window.initPacienteForm = function() {
        // ============ Cálculo de edad ============
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
            if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) edad--;
            edadInput.value = edad >= 0 ? edad + ' años' : '';
        }

        if (fechaInput && !fechaInput.dataset.init) {
            fechaInput.dataset.init = '1';
            fechaInput.addEventListener('change', calcularEdad);
            calcularEdad();
        }

        // ============ Nacionalidad ============
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
                const i = curpContainer.querySelector('input');
                if (i) i.value = '';
            } else if (esMexicana && pasaporteContainer) {
                const i = pasaporteContainer.querySelector('input');
                if (i) i.value = '';
            }
        }

        function toggleNacimiento() {
            if (!nacionalidadSelect) return;
            const esMexicana = nacionalidadSelect.value === 'MEXICANA';
            if (estadoNacimientoContainer) estadoNacimientoContainer.classList.toggle('hidden', !esMexicana);
            if (paisNacimientoContainer) paisNacimientoContainer.classList.toggle('hidden', esMexicana);
            if (!esMexicana && estadoNacimientoContainer) {
                const s = estadoNacimientoContainer.querySelector('select');
                if (s) s.value = '';
            }
            if (esMexicana && paisNacimientoContainer) {
                const i = paisNacimientoContainer.querySelector('input');
                if (i) i.value = '';
            }
        }

        if (nacionalidadSelect && !nacionalidadSelect.dataset.init) {
            nacionalidadSelect.dataset.init = '1';
            nacionalidadSelect.addEventListener('change', function() {
                toggleDocumento();
                toggleNacimiento();
            });
            toggleDocumento();
            toggleNacimiento();
        }

        // ============ Municipios ============
        const estadoSelect = document.getElementById('estado_id');
        const municipioSelect = document.getElementById('municipio_id');

        if (estadoSelect && municipioSelect && !estadoSelect.dataset.init) {
            estadoSelect.dataset.init = '1';
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
    };

    document.addEventListener('DOMContentLoaded', window.initPacienteForm);
</script>
