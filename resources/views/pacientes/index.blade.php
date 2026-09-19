<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center border border-nexus-primary/20 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">Directorio de Pacientes</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Control de expedientes clínicos y datos de
                        contacto de HealthNexus</p>
                </div>
            </div>

            <button type="button"
                onclick="window.abrirModalPaciente && window.abrirModalPaciente('create', '{{ route('pacientes.create') }}?partial=1')"
                class="group relative inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-nexus-accent bg-nexus-primary rounded-2xl shadow-md hover:shadow-lg hover:bg-slate-900 transition-all duration-200">
                <svg class="w-5 h-5 me-2 group-hover:scale-110 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Nuevo Paciente</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen" x-data="pacientesIndex()" x-init="init()">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- MÉTRICAS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pacientes</p>
                        <h4 class="text-xl font-bold text-slate-800" id="total-pacientes">{{ $pacientes->total() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-nexus-secondary/10 text-nexus-secondary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Estado Sistema</p>
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activo
                        </span>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Registros Hoy</p>
                        <h4 class="text-xl font-bold text-slate-800">Actualizado</h4>
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

            <!-- BUSCADOR -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-4 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                </div>

                <input type="text" x-model="busqueda" @input.debounce.400ms="buscar()" autocomplete="off"
                    placeholder="Buscar por nombre, correo, teléfono o ID..."
                    class="w-full border-0 bg-transparent text-sm focus:ring-0 placeholder:text-slate-400">

                <svg x-show="buscando" x-cloak class="animate-spin w-4 h-4 text-nexus-primary shrink-0" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>

                <button x-show="busqueda && !buscando" @click="limpiarBusqueda()" type="button"
                    class="text-xs font-bold text-slate-400 hover:text-slate-600 px-2 whitespace-nowrap">
                    Limpiar
                </button>
            </div>

            <!-- TABLA -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto" id="tabla-pacientes">
                    @include('pacientes._tabla', [
                        'pacientes' => $pacientes,
                        'busqueda' => $busqueda ?? '',
                    ])
                </div>
            </div>
        </div>

        <!-- ================= MODAL GLOBAL ================= -->
        <div x-show="modalAbierto" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @keydown.escape.window="cerrarModal()">

            <div x-show="modalAbierto" x-transition.opacity @click="cerrarModal()"
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div x-show="modalAbierto" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="relative bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-lg text-slate-800"
                        x-text="{
                            create: 'Nuevo Paciente',
                            edit: 'Editar Paciente',
                            show: 'Detalle del Paciente',
                            delete: 'Confirmar eliminación'
                        }[modalTipo] || ''">
                    </h3>

                    <button type="button" @click="cerrarModal()"
                        class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-2xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto p-6">
                    <div x-show="modalCargando" class="flex items-center justify-center py-16">
                        <svg class="animate-spin w-8 h-8 text-nexus-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </div>

                    <div x-show="modalError" x-cloak
                        class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm"
                        x-text="modalError"></div>

                    <!-- Contenido inyectado por fetch (create/edit/show) -->
                    <div x-show="!modalCargando && !modalError && modalTipo !== 'delete'" x-ref="modalBody"></div>

                    <!-- Confirmar eliminación -->
                    <div x-show="modalTipo === 'delete'" x-cloak class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.74-2.99l-7.07-12.14a2 2 0 00-3.48 0L2.19 16.01A2 2 0 004.93 19z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-700">
                                    ¿Seguro que deseas eliminar al paciente
                                    <strong class="text-slate-900" x-text="eliminarNombre"></strong>?
                                </p>
                                <p class="text-xs text-slate-500 mt-1">Esta acción no se puede deshacer.</p>
                            </div>
                        </div>

                        <form :action="eliminarUrl" method="POST" class="flex justify-end gap-3 pt-4">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="cerrarModal()"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</button>
                            <button type="submit"
                                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-md text-sm font-medium">
                                Sí, eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="toast.visible" x-cloak x-transition.opacity.duration.300ms
            class="fixed bottom-6 right-6 z-[100] px-5 py-3 bg-emerald-600 text-white rounded-2xl shadow-2xl text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span x-text="toast.mensaje"></span>
        </div>

    </div>

    <script>
        // FUNCIONES GLOBALES DEL MÓDULO PACIENTES

        function pacientesIndex() {
            return {
                // Buscador
                busqueda: '{{ $busqueda ?? '' }}',
                buscando: false,
                abortController: null,

                // Modal
                modalAbierto: false,
                modalTipo: null,
                modalContenido: '',
                modalCargando: false,
                modalError: null,
                eliminarNombre: '',
                eliminarUrl: '',

                // Toast
                toast: {
                    visible: false,
                    mensaje: ''
                },

                init() {
                    window.abrirModalPaciente = (tipo, url) => this.abrirModal(tipo, url);
                    window.abrirModalEliminarPaciente = (id, nombre, url) => this.abrirModalEliminar(id, nombre, url);
                    window.cerrarModalPaciente = () => this.cerrarModal();
                    window.recargarTablaPacientes = (mensaje) => this.recargarTabla(mensaje);
                    window.mostrarToast = (mensaje) => this.mostrarToast(mensaje);
                },

                // ============ BUSCADOR EN VIVO ============
                async buscar() {
                    if (this.abortController) this.abortController.abort();
                    this.abortController = new AbortController();
                    this.buscando = true;

                    try {
                        const res = await fetch(
                            `{{ route('pacientes.buscar') }}?q=${encodeURIComponent(this.busqueda)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                signal: this.abortController.signal
                            });
                        if (!res.ok) throw new Error();
                        const html = await res.text();
                        document.getElementById('tabla-pacientes').innerHTML = html;
                    } catch (e) {
                        if (e.name !== 'AbortError') {
                            console.error('Error en búsqueda:', e);
                        }
                    } finally {
                        this.buscando = false;
                    }
                },

                limpiarBusqueda() {
                    this.busqueda = '';
                    this.buscar();
                },

                // ============ RECARGAR TABLA ============
                async recargarTabla(mensaje = null) {
                    try {
                        const res = await fetch(
                            `{{ route('pacientes.buscar') }}?q=${encodeURIComponent(this.busqueda)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                        if (res.ok) {
                            document.getElementById('tabla-pacientes').innerHTML = await res.text();
                        }
                    } catch (e) {
                        console.error('Error recargando tabla:', e);
                    }

                    if (mensaje) this.mostrarToast(mensaje);
                },

                // ============ TOAST ============
                mostrarToast(mensaje) {
                    this.toast.mensaje = mensaje;
                    this.toast.visible = true;
                    setTimeout(() => {
                        this.toast.visible = false;
                    }, 3000);
                },

                // ============ MODALES ============
                async abrirModal(tipo, url) {
                    this.modalTipo = tipo;
                    this.modalAbierto = true;
                    this.modalCargando = true;
                    this.modalContenido = '';
                    this.modalError = null;

                    try {
                        const res = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error();
                        const html = await res.text();

                        this.modalCargando = false;

                        // Esperar a que el modal se pinte
                        this.$nextTick(() => {
                            const body = this.$refs.modalBody;
                            body.innerHTML = html;

                            if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                                window.Alpine.initTree(body);
                            }

                            // Inicializar los listeners (edad, municipios, etc.)
                            setTimeout(() => {
                                if (typeof window.initPacienteForm === 'function') {
                                    window.initPacienteForm();
                                }
                            }, 50);
                        });
                    } catch (e) {
                        this.modalError = 'No se pudo cargar el contenido.';
                        this.modalCargando = false;
                    }
                },

                abrirModalEliminar(id, nombre, url) {
                    this.eliminarNombre = nombre;
                    this.eliminarUrl = url;
                    this.modalTipo = 'delete';
                    this.modalAbierto = true;
                },

                cerrarModal() {
                    this.modalAbierto = false;
                    this.modalTipo = null;
                    this.modalContenido = '';
                    this.modalError = null;
                    this.eliminarNombre = '';
                    this.eliminarUrl = '';

                    // Limpiar el contenido inyectado
                    if (this.$refs.modalBody) {
                        this.$refs.modalBody.innerHTML = '';
                    }
                }
            };
        }

        // FORMULARIO DEL MODAL (validación + submit AJAX)
        function pacienteForm(config = {}) {
            return {
                errores: config.erroresServidor || {},
                enviando: false,

                init() {},

                tieneError(campo) {
                    return !!this.errores[campo];
                },

                mensajeError(campo) {
                    const msg = this.errores[campo];
                    return Array.isArray(msg) ? msg[0] : (msg || '');
                },

                hayErrores() {
                    return Object.keys(this.errores).length > 0;
                },

                limpiarErrores() {
                    this.errores = {};
                },

                async enviar() {
                    this.enviando = true;
                    this.limpiarErrores();

                    const form = this.$refs.formulario;
                    const formData = new FormData(form);
                    const url = form.action;

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                    formData.get('_token'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: formData,
                        });

                        if (res.status === 422) {
                            const data = await res.json();
                            this.errores = data.errors || {};
                            this.$nextTick(() => {
                                const primerError = document.querySelector('.border-rose-400');
                                primerError?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            });
                            return;
                        }

                        if (!res.ok) {
                            this.errores = {
                                general: ['Ocurrió un error inesperado. Intenta de nuevo.']
                            };
                            return;
                        }

                        const data = await res.json();

                        if (typeof window.cerrarModalPaciente === 'function') {
                            window.cerrarModalPaciente();
                        }
                        if (typeof window.recargarTablaPacientes === 'function') {
                            window.recargarTablaPacientes(data.mensaje || 'Guardado correctamente.');
                        } else {
                            window.location.reload();
                        }
                    } catch (err) {
                        console.error(err);
                        this.errores = {
                            general: ['Error de conexión. Revisa tu internet.']
                        };
                    } finally {
                        this.enviando = false;
                    }
                }
            };
        }

        // Exponer globalmente por si Alpine no las encuentra
        window.pacientesIndex = pacientesIndex;
        window.pacienteForm = pacienteForm;
    </script>

    @include('pacientes._scripts')

</x-app-layout>
