<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-slate-800">Expedientes</h2>
                <p class="text-sm text-slate-400">Historial clínico de todos los pacientes</p>
            </div>
            <a href="{{ route('pacientes.index') }}"
                class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                Ver todos los pacientes
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-900 p-8 shadow-xl shadow-indigo-200 text-white">
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-white/5"></div>
            <div class="absolute right-32 -bottom-24 w-52 h-52 rounded-full bg-white/5"></div>

            <div class="relative">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-3xl font-extrabold tracking-tight">Expedientes clínicos</h3>
                        <p class="text-indigo-100 mt-1">Busca un paciente para ver su historial completo.</p>
                    </div>
                    <div class="self-start rounded-2xl bg-white/10 border border-white/20 px-5 py-3 backdrop-blur">
                        <p class="text-xs font-bold tracking-wider text-indigo-200 uppercase">
                            {{ $busqueda ? 'Resultados' : 'Total' }}
                        </p>
                        <p class="text-3xl font-extrabold leading-none mt-1" id="total-pacientes">
                            {{ $pacientes->total() }}
                        </p>
                    </div>
                </div>

                <form method="GET" action="{{ route('expedientes.index') }}" id="form-busqueda"
                    class="flex flex-col sm:flex-row gap-3" onsubmit="return false;">
                    <div class="relative flex-1">
                        <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                        </svg>

                        <input type="text" name="buscar" id="input-buscar" value="{{ $busqueda }}"
                            placeholder="Buscar por nombre, apellidos o CURP" autocomplete="off"
                            class="w-full pl-12 pr-10 py-3 rounded-2xl border-0 bg-white text-slate-800 placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-300">

                        {{-- Spinner de carga (oculto por defecto) --}}
                        <div id="spinner-busqueda" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <button type="submit"
                        class="hidden sm:inline-block px-6 py-3 rounded-2xl bg-white/15 hover:bg-white/25 border border-white/20 text-sm font-semibold transition">
                        Buscar
                    </button>

                    <a href="{{ route('expedientes.index') }}" id="btn-limpiar"
                        class="px-6 py-3 rounded-2xl bg-white text-indigo-700 hover:bg-indigo-50 text-sm font-semibold text-center transition {{ $busqueda ? '' : 'hidden' }}">
                        Limpiar
                    </a>
                </form>
            </div>
        </div>

        <section id="contenedor-resultados">
            @include('expedientes._tabla', ['pacientes' => $pacientes, 'busqueda' => $busqueda])
        </section>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('input-buscar');
            const form = document.getElementById('form-busqueda');
            const contenedor = document.getElementById('contenedor-resultados');
            const spinner = document.getElementById('spinner-busqueda');
            const btnLimpiar = document.getElementById('btn-limpiar');
            const totalSpan = document.getElementById('total-pacientes');

            if (!input || !contenedor) return;

            const baseUrl = "{{ route('expedientes.index') }}";
            let debounceTimer = null;
            let controlador = null; // AbortController para cancelar fetch anterior

            // Mostrar / ocultar spinner
            function mostrarSpinner(show) {
                if (!spinner) return;
                spinner.classList.toggle('hidden', !show);
            }

            // Mostrar / ocultar botón "Limpiar"
            function toggleLimpiar(show) {
                if (!btnLimpiar) return;
                btnLimpiar.classList.toggle('hidden', !show);
            }

            async function buscar(termino) {
                // Cancela el fetch anterior si sigue en curso
                if (controlador) controlador.abort();
                controlador = new AbortController();

                mostrarSpinner(true);
                toggleLimpiar(termino.length > 0);

                const url = new URL(baseUrl);
                if (termino) url.searchParams.set('buscar', termino);
                url.searchParams.set('_partial', '1'); // flag para el controlador/vista

                try {
                    const resp = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                        signal: controlador.signal,
                    });

                    if (!resp.ok) throw new Error('Error en la búsqueda');

                    const html = await resp.text();

                    // El backend devuelve solo el HTML del partial (tabla + paginación)
                    contenedor.innerHTML = html;

                    // Actualiza el contador del banner leyendo el nuevo total
                    const nuevoTotal = contenedor.querySelector('[data-total-pacientes]');
                    if (nuevoTotal && totalSpan) {
                        totalSpan.textContent = nuevoTotal.dataset.totalPacientes;
                    }

                    // Actualiza la URL del navegador SIN recargar (opcional, mejora UX)
                    const urlVisible = new URL(window.location);
                    if (termino) urlVisible.searchParams.set('buscar', termino);
                    else urlVisible.searchParams.delete('buscar');
                    history.replaceState({}, '', urlVisible.toString());

                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.error('Error búsqueda:', err);
                    }
                } finally {
                    mostrarSpinner(false);
                }
            }

            // Evento input con debounce (300ms)
            input.addEventListener('input', function() {
                const termino = this.value.trim();

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    buscar(termino);
                }, 300);
            });

            // Evita submit normal (ya se busca en vivo)
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                buscar(input.value.trim());
            });

            // Botón limpiar: vacía y vuelve a buscar
            btnLimpiar?.addEventListener('click', function(e) {
                e.preventDefault();
                input.value = '';
                buscar('');
                input.focus();
            });

            // Al cargar la página, si ya había término, mostrar el botón limpiar
            if (input.value.trim()) {
                toggleLimpiar(true);
            }
        });
    </script>
</x-app-layout>

