{{--
    Lógica JS del modal de registro rápido.
    Requiere que exista #paciente_id (select de paciente) y la función
    global actualizarTriage() definida en la vista padre.
--}}

<script>
    (function() {
        'use strict';

        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modal-paciente');
            const form = document.getElementById('form-paciente-rapido');
            const errorBox = document.getElementById('error-paciente');
            const btnNuevo = document.getElementById('btn-nuevo-paciente');
            const btnCancelar = document.getElementById('btn-cancelar-paciente');
            const btnCerrar = document.getElementById('btn-cerrar-paciente');
            const btnGuardar = document.getElementById('btn-guardar-paciente');
            const selectPaciente = document.getElementById('paciente_id');

            if (!modal || !form || !btnNuevo || !selectPaciente) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const storeUrl = @json(route('admisiones.paciente-rapido'));

            function abrirModal() {
                modal.classList.remove('hidden');
                form.querySelector('input[name="nombre"]')?.focus();
            }

            function cerrarModal() {
                modal.classList.add('hidden');
                form.reset();
                mostrarErrores([]);
            }

            function mostrarErrores(errores) {
                if (!errores.length) {
                    errorBox.classList.add('hidden');
                    errorBox.innerHTML = '';
                    return;
                }
                errorBox.innerHTML = errores.map(e => `<p>• ${e}</p>`).join('');
                errorBox.classList.remove('hidden');
            }

            function extraerErrores(json) {
                if (json?.errors) {
                    return Object.values(json.errors).flat();
                }
                return [json?.message || 'No se pudo registrar el paciente.'];
            }

            // ─── Eventos de apertura/cierre ─────────────────────────
            btnNuevo.addEventListener('click', abrirModal);
            btnCancelar.addEventListener('click', cerrarModal);
            btnCerrar.addEventListener('click', cerrarModal);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) cerrarModal();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    cerrarModal();
                }
            });

            // ─── Submit AJAX ────────────────────────────────────────
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                mostrarErrores([]);
                btnGuardar.disabled = true;
                btnGuardar.textContent = 'Guardando...';

                try {
                    const res = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                    });

                    const json = await res.json().catch(() => ({}));

                    if (!res.ok || !json.ok) {
                        mostrarErrores(extraerErrores(json));
                        return;
                    }

                    // Insertar el nuevo paciente en el select y seleccionarlo
                    const opt = new Option(json.paciente.nombre_completo, json.paciente.id,
                        true, true);
                    opt.dataset.triage = '';
                    selectPaciente.add(opt);
                    selectPaciente.value = json.paciente.id;
                    selectPaciente.dispatchEvent(new Event('change'));

                    // Notificar a la vista padre (por si quiere refrescar triage, etc.)
                    document.dispatchEvent(new CustomEvent('paciente-rapido:creado', {
                        detail: json.paciente,
                    }));

                    cerrarModal();
                } catch (err) {
                    mostrarErrores(['Error de red. Verifica tu conexión e intenta de nuevo.']);
                } finally {
                    btnGuardar.disabled = false;
                    btnGuardar.textContent = 'Guardar paciente';
                }
            });
        });
    })();
</script>
