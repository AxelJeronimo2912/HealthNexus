<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center border border-nexus-primary/20 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">Servicios Hospitalarios</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Catálogo de servicios, precios y personal
                        asignado</p>
                </div>
            </div>

            @can('servicios.ver')
                <button type="button" id="btn-abrir-servicio"
                    class="group relative inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-nexus-accent bg-nexus-primary rounded-2xl shadow-md hover:shadow-lg hover:bg-slate-900 transition-all duration-200">
                    <svg class="w-5 h-5 me-2 group-hover:scale-110 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Nuevo Servicio</span>
                </button>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ============ MÉTRICAS ============ --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Servicios</p>
                        <h4 class="text-xl font-bold text-slate-800">{{ $stats['total'] }}</h4>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Servicios Activos</p>
                        <h4 class="text-xl font-bold text-emerald-600">{{ $stats['activos'] }}</h4>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tipos de Servicio</p>
                        <h4 class="text-xl font-bold text-indigo-600">{{ $stats['tipos'] }}</h4>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div
                    class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-3 shadow-sm">
                    <div
                        class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div
                    class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm flex items-center gap-3 shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- ============ BUSCADOR / FILTROS ============ --}}
            <form method="GET"
                class="bg-white rounded-3xl shadow-sm border border-slate-100 p-4 flex flex-wrap items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                </div>

                <input type="text" name="buscar" value="{{ $busqueda }}" autocomplete="off"
                    placeholder="Buscar por nombre, código o ubicación..."
                    class="flex-1 min-w-[200px] border-0 bg-transparent text-sm focus:ring-0 placeholder:text-slate-400">

                <select name="tipo"
                    class="min-w-[180px] bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none">
                    <option value="">Todos los tipos</option>
                    <option value="consulta_externa" @selected($tipo === 'consulta_externa')>Consulta Externa</option>
                    <option value="urgencias" @selected($tipo === 'urgencias')>Urgencias</option>
                    <option value="hospitalizacion" @selected($tipo === 'hospitalizacion')>Hospitalización</option>
                    <option value="quirofano" @selected($tipo === 'quirofano')>Quirófano</option>
                    <option value="farmacia" @selected($tipo === 'farmacia')>Farmacia</option>
                    <option value="enfermeria" @selected($tipo === 'enfermeria')>Enfermería</option>
                    <option value="laboratorio" @selected($tipo === 'laboratorio')>Laboratorio</option>
                    <option value="imagenologia" @selected($tipo === 'imagenologia')>Imagenología</option>
                    <option value="otro" @selected($tipo === 'otro')>Otro</option>
                </select>

                <button type="submit"
                    class="px-5 py-2.5 bg-nexus-primary hover:bg-slate-900 active:scale-95 text-white rounded-2xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>

                @if ($busqueda || $tipo)
                    <a href="{{ route('servicios.index') }}"
                        class="text-xs font-bold text-slate-400 hover:text-slate-600 px-2 whitespace-nowrap">
                        Limpiar
                    </a>
                @endif
            </form>

            {{-- ============ TABLA ============ --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/70 border-b border-slate-100">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Servicio</th>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Tipo</th>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Ubicación</th>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Horario</th>
                                <th
                                    class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Personal</th>
                                <th
                                    class="px-6 py-4 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Precio</th>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Estado</th>
                                <th
                                    class="px-6 py-4 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($servicios as $servicio)
                                <tr class="group hover:bg-slate-50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold shrink-0 transition-colors duration-200 group-hover:bg-nexus-primary group-hover:text-violet-400">
                                                {{ mb_strtoupper(mb_substr($servicio->nombre, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-800">{{ $servicio->nombre }}</p>
                                                <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                    {{ $servicio->codigo }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $servicio->tipo_color }}">
                                            {{ $servicio->tipo_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-xs text-slate-600">
                                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>{{ $servicio->ubicacion ?? '—' }}</span>
                                        </div>
                                        @if ($servicio->piso)
                                            <p class="text-[11px] text-slate-400 mt-1 ml-6">{{ $servicio->piso }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $servicio->horario }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold">
                                            {{ $servicio->users_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if ((float) $servicio->precio > 0)
                                            <p class="text-sm font-extrabold text-slate-800">
                                                {{ $servicio->precio_formateado }}</p>
                                            @if ($servicio->precio_descripcion)
                                                <p class="text-[10px] text-slate-400 mt-0.5">
                                                    {{ $servicio->precio_descripcion }}</p>
                                            @endif
                                        @else
                                            <span
                                                class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                                Sin costo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($servicio->activo)
                                            <span
                                                class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                                Activo
                                            </span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button"
                                                onclick="abrirModalVer('{{ route('servicios.show', $servicio) }}')"
                                                class="p-2 text-slate-800 hover:bg-slate-100 rounded-xl transition"
                                                title="Ver detalles">
                                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                onclick="abrirModalEditar('{{ route('servicios.edit', $servicio) }}')"
                                                class="p-2 text-purple-500 hover:bg-purple-50 rounded-xl transition"
                                                title="Editar servicio">
                                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                onclick="abrirModalPersonal('{{ route('servicios.personal', $servicio) }}')"
                                                class="p-2 text-indigo-500 hover:bg-indigo-50 rounded-xl transition"
                                                title="Personal asignado">
                                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                onclick="abrirModalDelete('{{ route('servicios.destroy', $servicio) }}', @js($servicio->nombre))"
                                                class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition"
                                                title="Eliminar servicio">
                                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-14 text-center">
                                        <p class="text-sm font-semibold text-slate-400">Sin servicios registrados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $servicios->links() }}</div>
        </div>
    </div>

    {{-- ============ INCLUSIÓN DE MODALES ============ --}}
    @include('servicios.partials._modal-servicio')
    @include('servicios.partials._modal-show')
    @include('servicios.partials._modal-edit')
    @include('servicios.partials._modal-personal')
    @include('servicios.partials._modal-delete')

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ---------- Modal Nuevo Servicio ----------
                const modalNuevo = document.getElementById('modal-servicio');
                const btnAbrirNuevo = document.getElementById('btn-abrir-servicio');
                const btnCerrarNuevo = document.getElementById('btn-cerrar-servicio');
                const btnCancelarNuevo = document.getElementById('btn-cancelar-servicio');

                function abrirModalNuevo() {
                    if (modalNuevo) modalNuevo.classList.remove('hidden');
                }

                function cerrarModalNuevo() {
                    if (modalNuevo) modalNuevo.classList.add('hidden');
                }

                if (btnAbrirNuevo) btnAbrirNuevo.addEventListener('click', abrirModalNuevo);
                if (btnCerrarNuevo) btnCerrarNuevo.addEventListener('click', cerrarModalNuevo);
                if (btnCancelarNuevo) btnCancelarNuevo.addEventListener('click', cerrarModalNuevo);

                // ---------- Cerrar con Escape o clic en el fondo ----------
                const modalesCierre = {
                    'modal-servicio': cerrarModalNuevo,
                    'modal-show-servicio': () => window.cerrarModalShow(),
                    'modal-edit-servicio': () => window.cerrarModalEdit(),
                    'modal-personal-servicio': () => window.cerrarModalPersonal(),
                    'modal-delete-servicio': () => window.cerrarModalDelete(),
                };

                Object.entries(modalesCierre).forEach(([id, cerrar]) => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.addEventListener('click', e => {
                        if (e.target === el) cerrar();
                    });
                });

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') Object.values(modalesCierre).forEach(cerrar => cerrar());
                });
            });

            // ---------- Helper AJAX para Ver / Editar / Personal ----------
            async function cargarModal(url, contentId, modalId) {
                try {
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        },
                        credentials: 'same-origin'
                    });

                    if (!res.ok) throw new Error('HTTP ' + res.status);

                    const html = await res.text();
                    document.getElementById(contentId).innerHTML = html;
                    document.getElementById(modalId).classList.remove('hidden');
                } catch (err) {
                    console.error('Error cargando modal:', modalId, err);
                    alert('No se pudo cargar el contenido. Revisa la consola.');
                }
            }

            // Ver
            window.abrirModalVer = url => cargarModal(url, 'modal-show-content', 'modal-show-servicio');
            window.cerrarModalShow = function() {
                document.getElementById('modal-show-servicio')?.classList.add('hidden');
            };

            // Editar
            window.abrirModalEditar = url => cargarModal(url, 'modal-edit-content', 'modal-edit-servicio');
            window.cerrarModalEdit = function() {
                document.getElementById('modal-edit-servicio')?.classList.add('hidden');
            };

            // Personal
            window.abrirModalPersonal = url => cargarModal(url, 'modal-personal-content', 'modal-personal-servicio');
            window.cerrarModalPersonal = function() {
                document.getElementById('modal-personal-servicio')?.classList.add('hidden');
            };

            // Eliminar
            window.abrirModalDelete = function(url, nombre) {
                document.getElementById('form-delete-servicio').action = url;
                document.getElementById('delete-nombre-servicio').textContent = nombre;
                document.getElementById('modal-delete-servicio').classList.remove('hidden');
            };
            window.cerrarModalDelete = function() {
                document.getElementById('modal-delete-servicio')?.classList.add('hidden');
            };

            // =====================================================================
            // FUNCIONES GLOBALES PARA EL MODAL DE PRECIO (Para Create y Edit)
            // =====================================================================

            window.toggleCostoCheckbox = function(checkbox) {
                const inputPrecio = document.getElementById('precio');
                const resumen = document.getElementById('precio-resumen');

                if (checkbox.checked) {
                    const p = parseFloat(inputPrecio.value) || 0;
                    if (p <= 0) {
                        window.abrirModalPrecio(); // Si no hay precio, abre el modal
                    }
                    if (resumen) resumen.style.display = 'flex';
                } else {
                    if (inputPrecio) inputPrecio.value = 0;
                    const inputDesc = document.getElementById('precio_descripcion');
                    if (inputDesc) inputDesc.value = '';
                    if (resumen) resumen.style.display = 'none';
                }
            };

            window.abrirModalPrecio = function() {
                const modal = document.getElementById('modal-precio');
                const inputPrecio = document.getElementById('precio');
                const modalPrecio = document.getElementById('modal_precio');
                const modalDesc = document.getElementById('modal_precio_descripcion');
                const inputDesc = document.getElementById('precio_descripcion');
                const modalError = document.getElementById('modal-error-precio');

                if (!modal) return;

                modalPrecio.value = inputPrecio.value && parseFloat(inputPrecio.value) > 0 ? parseFloat(inputPrecio.value)
                    .toFixed(2) : '';
                modalDesc.value = inputDesc.value || '';
                if (modalError) modalError.style.display = 'none';

                modal.style.display = 'flex';
                modalPrecio.focus();
            };

            window.cerrarModalPrecio = function() {
                const modal = document.getElementById('modal-precio');
                if (modal) modal.style.display = 'none';
            };

            window.guardarPrecio = function() {
                const modalPrecio = document.getElementById('modal_precio');
                const modalDesc = document.getElementById('modal_precio_descripcion');
                const modalError = document.getElementById('modal-error-precio');
                const inputPrecio = document.getElementById('precio');
                const inputDesc = document.getElementById('precio_descripcion');
                const chkTieneCosto = document.getElementById('tiene_costo');
                const resumenValor = document.getElementById('precio-resumen-valor');
                const resumenDesc = document.getElementById('precio-resumen-desc');

                const p = parseFloat(modalPrecio.value);

                if (isNaN(p) || p <= 0) {
                    modalError.textContent = 'Ingresa un precio mayor a 0.';
                    modalError.style.display = 'block';
                    return;
                }

                inputPrecio.value = p.toFixed(2);
                inputDesc.value = modalDesc.value.trim();
                chkTieneCosto.checked = true;

                resumenValor.textContent = p.toFixed(2);
                resumenDesc.textContent = inputDesc.value ? '/ ' + inputDesc.value : '';

                document.getElementById('precio-resumen').style.display = 'flex';

                window.cerrarModalPrecio();
            };
        </script>
    @endpush
</x-app-layout>
