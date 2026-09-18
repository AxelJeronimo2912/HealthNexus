<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Sincroniza el estado del checkbox de sección
        function actualizarSeccion(index) {
            const items = document.querySelectorAll(`.item-checkbox[data-seccion="${index}"]`);
            const toggle = document.querySelector(`.seccion-toggle[data-seccion="${index}"]`);
            if (!toggle) return;

            const total = items.length;
            const marcados = [...items].filter(i => i.checked).length;

            toggle.checked = marcados === total && total > 0;
            toggle.indeterminate = marcados > 0 && marcados < total;
        }

        // Al hacer clic en el checkbox de sección, marcar/desmarcar todos sus items
        document.querySelectorAll('.seccion-toggle').forEach(toggle => {
            const index = toggle.dataset.seccion;
            const items = document.querySelectorAll(`.item-checkbox[data-seccion="${index}"]`);

            // Estado inicial
            actualizarSeccion(index);

            toggle.addEventListener('change', () => {
                items.forEach(i => i.checked = toggle.checked);
            });

            items.forEach(item => {
                item.addEventListener('change', () => actualizarSeccion(index));
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filtro = document.getElementById('filtro-menus');
        const contador = document.getElementById('contador-seleccionados');
        const sinResultados = document.getElementById('sin-resultados');
        const seccionToggles = document.querySelectorAll('.seccion-toggle');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const bloques = document.querySelectorAll('.seccion-bloque');

        if (!filtro || !contador) return; // por si el partial se incluye en otra página sin este UI

        /* ---------- Contador de seleccionados ---------- */
        const actualizarContador = () => {
            const total = document.querySelectorAll('.item-checkbox:checked').length;
            contador.textContent = `${total} seleccionado${total === 1 ? '' : 's'}`;
        };

        /* ---------- Sincronizar checkbox de sección ---------- */
        const sincronizarSeccion = (seccionIndex) => {
            const toggle = document.querySelector(`.seccion-toggle[data-seccion="${seccionIndex}"]`);
            if (!toggle) return;

            const itemsSeccion = document.querySelectorAll(
                `.item-checkbox[data-seccion="${seccionIndex}"]`
            );
            const total = itemsSeccion.length;
            const marcados = [...itemsSeccion].filter(c => c.checked).length;

            toggle.checked = marcados > 0 && marcados === total;
            toggle.indeterminate = marcados > 0 && marcados < total;
        };

        /* ---------- "Seleccionar todo" por sección ---------- */
        seccionToggles.forEach(toggle => {
            toggle.addEventListener('change', () => {
                const seccionIndex = toggle.dataset.seccion;
                document.querySelectorAll(
                    `.item-checkbox[data-seccion="${seccionIndex}"]`
                ).forEach(cb => {
                    // Solo afecta a los items visibles (respetando el filtro)
                    const label = cb.closest('.item-menu');
                    if (label && label.style.display !== 'none') {
                        cb.checked = toggle.checked;
                    }
                });
                sincronizarSeccion(seccionIndex);
                actualizarContador();
            });
        });

        /* ---------- Cambio en un item ---------- */
        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                sincronizarSeccion(cb.dataset.seccion);
                actualizarContador();
            });
        });

        /* ---------- Estado inicial ---------- */
        bloques.forEach(b => sincronizarSeccion(b.dataset.seccion));
        actualizarContador();

        /* ---------- Filtro en tiempo real ---------- */
        filtro.addEventListener('input', () => {
            const q = filtro.value.trim().toLowerCase();
            let visiblesTotales = 0;

            bloques.forEach(bloque => {
                let visibles = 0;
                bloque.querySelectorAll('.item-menu').forEach(item => {
                    const nombre = item.dataset.nombre || '';
                    const coincide = q === '' || nombre.includes(q);
                    item.style.display = coincide ? '' : 'none';
                    if (coincide) visibles++;
                });

                bloque.style.display = visibles > 0 ? '' : 'none';
                visiblesTotales += visibles;
            });

            if (sinResultados) {
                sinResultados.classList.toggle('hidden', visiblesTotales > 0);
            }
        });
    });
</script>
