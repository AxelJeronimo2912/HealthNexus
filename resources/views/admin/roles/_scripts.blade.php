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
