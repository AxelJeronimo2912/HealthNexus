<script>
    (function() {
        // ========== Configuración ==========
        const form = document.getElementById('filters-form');
        const input = document.getElementById('filter-buscar');
        const select = document.getElementById('filter-grupo');
        const spinner = document.getElementById('filter-spinner');
        const clearBtn = document.getElementById('filter-clear-buscar');
        const clearAll = document.getElementById('filter-limpiar-todo');
        const chipsWrap = document.getElementById('filter-chips');
        const chipBuscar = document.getElementById('chip-buscar');
        const chipBuscarValue = document.getElementById('chip-buscar-value');
        const chipGrupo = document.getElementById('chip-grupo');
        const chipGrupoValue = document.getElementById('chip-grupo-value');

        const tbody = document.getElementById('table-rows');
        const pagination = document.getElementById('table-pagination');
        const tableFrom = document.getElementById('table-from');
        const tableTo = document.getElementById('table-to');
        const tableTotal = document.getElementById('table-total');
        const tableLoading = document.getElementById('table-loading');
        const tableOverlay = document.getElementById('table-overlay');

        if (!form || !input || !select || !tbody) return; // seguridad

        const urlBase = @json(route('especialidades.index'));

        let debounceTimer = null;
        let currentController = null;
        let lastQuery = '';

        // ========== Helpers ==========
        function buildParams() {
            const params = new URLSearchParams();
            const buscar = input.value.trim();
            const grupo = select.value;
            if (buscar) params.set('buscar', buscar);
            if (grupo) params.set('grupo', grupo);
            return params;
        }

        function setLoading(state) {
            if (state) {
                spinner?.classList.remove('hidden');
                tableLoading?.classList.remove('hidden');
                tableOverlay?.classList.remove('hidden');
                tbody.classList.add('opacity-60');
            } else {
                spinner?.classList.add('hidden');
                tableLoading?.classList.add('hidden');
                tableOverlay?.classList.add('hidden');
                tbody.classList.remove('opacity-60');
            }
        }

        function updateClearButtons() {
            const hasBuscar = input.value.trim() !== '';
            const hasGrupo = select.value !== '';

            clearBtn.classList.toggle('hidden', !hasBuscar);
            clearAll.classList.toggle('hidden', !(hasBuscar || hasGrupo));
            chipsWrap.classList.toggle('hidden', !(hasBuscar || hasGrupo));

            chipBuscar.classList.toggle('hidden', !hasBuscar);
            chipGrupo.classList.toggle('hidden', !hasGrupo);

            if (hasBuscar) chipBuscarValue.textContent = `"${input.value.trim()}"`;
            if (hasGrupo) {
                const label = select.options[select.selectedIndex].textContent.trim();
                chipGrupoValue.textContent = label;
            }
        }

        function updateUrl() {
            const params = buildParams();
            const qs = params.toString();
            const newUrl = qs ? `${urlBase}?${qs}` : urlBase;
            window.history.replaceState({}, '', newUrl);
        }

        function updatePaginationInfo(data) {
            if (tableFrom) tableFrom.textContent = data.from ?? 0;
            if (tableTo) tableTo.textContent = data.to ?? 0;
            if (tableTotal) tableTotal.textContent = data.total ?? 0;
        }

        // ========== Fetch principal ==========
        async function fetchResults() {
            const params = buildParams();
            const qs = params.toString();

            // Evitar fetch redundante
            if (qs === lastQuery) return;
            lastQuery = qs;

            // Cancelar petición previa
            if (currentController) currentController.abort();
            currentController = new AbortController();

            setLoading(true);

            try {
                const url = qs ? `${urlBase}?${qs}` : urlBase;
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    signal: currentController.signal,
                });

                if (!response.ok) throw new Error('Error en la petición');

                const data = await response.json();

                // Reemplazar filas
                tbody.innerHTML = data.html;

                // Reemplazar paginador
                if (pagination) pagination.innerHTML = data.pagination;

                // Actualizar contador
                updatePaginationInfo(data);

                // Actualizar URL
                updateUrl();
            } catch (err) {
                if (err.name === 'AbortError') return; // ignorar cancelaciones
                console.error('Error al filtrar:', err);
                // Fallback: recargar página con el form normal
                window.location.href = qs ? `${urlBase}?${qs}` : urlBase;
            } finally {
                setLoading(false);
            }
        }

        // ========== Debounce ==========
        function debouncedFetch(delay = 300) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchResults, delay);
        }

        // ========== Eventos ==========
        input.addEventListener('input', function() {
            updateClearButtons();
            debouncedFetch(300);
        });

        select.addEventListener('change', function() {
            updateClearButtons();
            fetchResults(); // inmediato para selects
        });

        // Limpiar input
        clearBtn.addEventListener('click', function() {
            input.value = '';
            updateClearButtons();
            fetchResults();
            input.focus();
        });

        // Limpiar todo
        clearAll.addEventListener('click', function() {
            input.value = '';
            select.value = '';
            updateClearButtons();
            fetchResults();
        });

        // Chips: botón X
        chipsWrap.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-chip-clear]');
            if (!btn) return;
            const field = btn.getAttribute('data-chip-clear');
            if (field === 'buscar') input.value = '';
            if (field === 'grupo') select.value = '';
            updateClearButtons();
            fetchResults();
        });

        // Delegación para el paginador (por si Laravel usa enlaces normales)
        if (pagination) {
            pagination.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link || !link.href) return;

                // Si el link no es de esta ruta, dejamos que siga
                if (!link.href.startsWith(urlBase)) return;

                e.preventDefault();
                const url = new URL(link.href);
                const page = url.searchParams.get('page') || '1';

                const params = buildParams();
                if (page !== '1') params.set('page', page);

                const qs = params.toString();
                const finalUrl = qs ? `${urlBase}?${qs}` : urlBase;

                // Forzamos fetch cambiando lastQuery para que no lo ignore
                lastQuery = '__skip__';
                // Ejecutamos manualmente
                (async function() {
                    if (currentController) currentController.abort();
                    currentController = new AbortController();
                    setLoading(true);
                    try {
                        const res = await fetch(finalUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            signal: currentController.signal,
                        });
                        const data = await res.json();
                        tbody.innerHTML = data.html;
                        pagination.innerHTML = data.pagination;
                        updatePaginationInfo(data);
                        window.history.replaceState({}, '', finalUrl);
                    } catch (err) {
                        if (err.name !== 'AbortError') window.location.href = finalUrl;
                    } finally {
                        setLoading(false);
                        lastQuery = qs;
                    }
                })();
            });
        }

        // Estado inicial
        updateClearButtons();

        // Atajo: '/' enfoca la búsqueda
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && document.activeElement !== input &&
                !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                input.focus();
            }
        });
    })();
</script>
