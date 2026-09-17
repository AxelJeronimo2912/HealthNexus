<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Panel de Administracion — HealthNexus
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- TARJETA BIENVENIDA -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Sesion Activa
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">
                        Bienvenido, {{ auth()->user()->name }}
                    </h3>
                    <p class="text-sm text-slate-500">
                        Rol activo: <span class="font-semibold text-nexus-secondary capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'Administrador' }}</span>
                    </p>
                </div>

                <!-- Espacio preparado para tu Logo cuando lo tengas -->
                <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 rounded-xl bg-nexus-primary flex items-center justify-center text-nexus-accent shadow-sm">
                        <!-- Icono temporal de Corazon/Logo -->
                        <svg class="w-6 h-6 fill-current text-cyan-400" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="pr-2">
                        <p class="text-xs font-bold text-slate-700">HealthNexus</p>
                        <p class="text-[10px] text-slate-400">Sistema Medico</p>
                    </div>
                </div>
            </div>

            <!-- SECCION ACCESOS RAPIDOS / TARJETAS ESTILO MYCLOUD -->
            <div>
                <h4 class="text-base font-bold text-slate-700 mb-4 px-1">Acceso Rapido</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    <!-- Tarjeta 1: Configuracion del Hospital -->
                    <a href="#" class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0v4m0 0h4m-4 0H7" />
                                </svg>
                            </div>
                            <h5 class="font-bold text-slate-800 text-base group-hover:text-indigo-600 transition-colors">
                                Configuracion del Hospital
                            </h5>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Datos generales, servicios y areas clinicas.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center text-xs font-semibold text-indigo-600">
                            <span>Gestionar</span>
                            <svg class="w-4 h-4 ms-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>

                    <!-- Tarjeta 2: Usuarios -->
                    <a href="{{ route('admin.users.index') }}" class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h5 class="font-bold text-slate-800 text-base group-hover:text-emerald-600 transition-colors">
                                Usuarios
                            </h5>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Alta, edicion y administracion de roles.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center text-xs font-semibold text-emerald-600">
                            <span>Ver lista</span>
                            <svg class="w-4 h-4 ms-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>

                    <!-- Tarjeta 3: Roles y Permisos -->
                    <a href="#" class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h5 class="font-bold text-slate-800 text-base group-hover:text-purple-600 transition-colors">
                                Roles y Permisos
                            </h5>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Control de acceso y privilegios del sistema.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center text-xs font-semibold text-purple-600">
                            <span>Configurar</span>
                            <svg class="w-4 h-4 ms-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>

                    <!-- Tarjeta 4: Auditoria -->
                    <a href="#" class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h5 class="font-bold text-slate-800 text-base group-hover:text-amber-600 transition-colors">
                                Auditoria
                            </h5>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Registro de actividad, eventos y dispositivos.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center text-xs font-semibold text-amber-600">
                            <span>Revisar logs</span>
                            <svg class="w-4 h-4 ms-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>