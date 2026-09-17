@php
    $user = auth()->user();
    $rol = $user->getRoleNames()->first();

    $linkBase = 'group/link flex items-center px-3 py-2.5 rounded-xl hover:bg-[#7C3AED]/20 text-slate-300 hover:text-white transition-colors duration-150 min-w-[240px]';
    $linkActivo = 'bg-[#7C3AED]/30 text-white font-medium border-l-4 border-[#A78BFA]';
@endphp

<!-- SIDEBAR DESPLEGABLE CON HOVER -->
<aside
    class="group/sidebar fixed top-0 left-0 z-50 h-full bg-[#172554] text-slate-200 transition-all duration-300 ease-in-out w-16 hover:w-64 shadow-2xl flex flex-col justify-between overflow-hidden border-r border-[#172554]/50"
    :class="abierto ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0'">

    <!-- HEADER / LOGO -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-slate-800/60 min-w-[256px]">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <div class="w-9 h-9 min-w-[36px] rounded-xl bg-[#7C3AED]/20 border border-[#A78BFA]/30 flex items-center justify-center text-[#A78BFA]">
                <x-heroicon-s-heart class="w-5 h-5" />
            </div>

            <span class="text-base font-bold text-white whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                HealthNexus
            </span>
        </a>

        <button @click="abierto = false" class="lg:hidden text-slate-400 hover:text-white mr-2">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
    </div>

    <!-- ROL ACTIVO -->
    <div class="px-4 py-3 border-b border-slate-800/60 text-xs min-w-[256px] flex items-center gap-3">
        <div class="w-2.5 h-2.5 rounded-full bg-[#10B981] shrink-0 shadow-sm shadow-[#10B981]/50"></div>

        <div class="opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap overflow-hidden">
            <p class="text-slate-400 text-[10px] uppercase tracking-wider leading-none">
                Rol activo
            </p>

            <p class="text-white font-semibold text-xs mt-0.5">
                {{ ucfirst($rol ?? 'Sin rol') }}
            </p>
        </div>
    </div>

    <!-- MENU -->
    <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-1 text-sm scrollbar-thin scrollbar-thumb-slate-700">

        {{-- PANEL --}}
        <p class="px-3 pt-2 pb-1 text-[10px] uppercase text-[#A78BFA]/70 tracking-wider font-semibold opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
            Panel
        </p>

        <a href="{{ route('dashboard') }}"
            class="{{ $linkBase }} {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? $linkActivo : '' }}">
            <x-heroicon-o-home class="w-5 h-5 shrink-0 text-[#A78BFA]" />
            <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                Dashboard
            </span>
        </a>


        {{-- GESTION HOSPITALARIA --}}
        @canany(['pacientes.ver', 'admision.ver', 'servicios.ver', 'especialidades.ver', 'citas.ver', 'turnos.ver', 'camas.ver'])

            <p class="px-3 pt-4 pb-1 text-[10px] uppercase text-[#A78BFA]/70 tracking-wider font-semibold opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Gestion Hospitalaria
            </p>

            @can('pacientes.ver')
                <a href="{{ route('pacientes.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.pacientes.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-user-group class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Pacientes
                    </span>
                </a>
            @endcan

            @can('admision.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Admision
                    </span>
                </a>
            @endcan

            @can('servicios.ver')
                <a href="{{ route('servicios.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('servicios.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-building-office-2 class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Servicios
                    </span>
                </a>
            @endcan

            @can('especialidades.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-academic-cap class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Especialidades
                    </span>
                </a>
            @endcan

            @can('citas.ver')
                <a href="{{ route('citas.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('citas.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-calendar-days class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Citas
                    </span>
                </a>
            @endcan

            @can('turnos.ver')
                <a href="{{ route('admin.turnos.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.turnos.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-clock class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Turnos
                    </span>
                </a>
            @endcan

            @can('camas.ver')
                <a href="{{ route('camas.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('camas.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-home-modern class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Camas
                    </span>
                </a>
            @endcan

        @endcanany


        {{-- AGENDA MEDICA --}}
        @can('agenda.ver')

            <p class="px-3 pt-4 pb-1 text-[10px] uppercase text-[#A78BFA]/70 tracking-wider font-semibold opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Agenda
            </p>

            <a href="{{ route('agenda.index') }}"
                class="{{ $linkBase }} {{ request()->routeIs('agenda.index') ? $linkActivo : '' }}">
                <x-heroicon-o-calendar-days class="w-5 h-5 shrink-0" />
                <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                    Calendario de Citas
                </span>
            </a>

            <a href="{{ route('agenda.create') }}"
                class="{{ $linkBase }} {{ request()->routeIs('agenda.create') ? $linkActivo : '' }}">
                <x-heroicon-o-plus-circle class="w-5 h-5 shrink-0" />
                <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                    Nueva Cita
                </span>
            </a>

        @endcan


        {{-- ATENCION CLINICA --}}
        @canany(['expediente.ver', 'consultas.ver', 'enfermeria.ver', 'seguimiento.ver', 'signos-vitales.ver'])

            <p class="px-3 pt-4 pb-1 text-[10px] uppercase text-[#A78BFA]/70 tracking-wider font-semibold opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Atencion Clinica
            </p>

            @can('expediente.ver')
                <a href="{{ route('expedientes.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('expedientes.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-document-text class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Expediente
                    </span>
                </a>
            @endcan

            @can('consultas.ver')
                <a href="{{ route('citas.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('citas.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Consultas
                    </span>
                </a>
            @endcan

            @can('enfermeria.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-heart class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Enfermeria
                    </span>
                </a>
            @endcan

            @can('seguimiento.ver')
                <a href="{{ route('seguimientos.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('seguimientos.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-chart-bar class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Seguimiento
                    </span>
                </a>
            @endcan

            @can('signos-vitales.ver')
                <a href="{{ route('signos-vitales.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('signos-vitales.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-heart class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Signos Vitales
                    </span>
                </a>
            @endcan

        @endcanany


        {{-- FARMACIA E INVENTARIO --}}
        @canany(['medicamentos.ver', 'dispensaciones.ver', 'existencias.ver', 'movimientos.ver', 'prediccion.ver'])

            <p class="px-3 pt-4 pb-1 text-[10px] uppercase text-[#A78BFA]/70 tracking-wider font-semibold opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Farmacia e Inventario
            </p>

            @can('medicamentos.ver')
                <a href="{{ route('medicamentos.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('medicamentos.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-beaker class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Medicamentos
                    </span>
                </a>
            @endcan

            @can('dispensaciones.ver')
                <a href="{{ route('dispensaciones.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('dispensaciones.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Recetas
                    </span>
                </a>
            @endcan

            @can('existencias.ver')
                <a href="{{ route('existencias.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('existencias.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-archive-box class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Existencias
                    </span>
                </a>
            @endcan

            @can('movimientos.ver')
                <a href="{{ route('movimientos.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('movimientos.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-arrow-path class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Movimientos
                    </span>
                </a>
            @endcan

            @can('prediccion.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-cpu-chip class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Prediccion IA
                    </span>
                </a>
            @endcan

        @endcanany


        {{-- SEGURIDAD E INTELIGENCIA --}}
        @canany(['usuarios.ver', 'roles.ver', 'dispositivos.ver', 'auditoria.ver', 'asistente.ver', 'alertas.ver'])

            <p class="px-3 pt-4 pb-1 text-[10px] uppercase text-[#A78BFA]/70 tracking-wider font-semibold opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                Seguridad e Inteligencia
            </p>

            @can('usuarios.ver')
                <a href="{{ route('admin.users.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.users.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-users class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Usuarios
                    </span>
                </a>
            @endcan

            @can('roles.ver')
                <a href="{{ route('admin.roles.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.roles.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-shield-check class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Roles y Permisos
                    </span>
                </a>
            @endcan

            @can('dispositivos.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-device-phone-mobile class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Dispositivos
                    </span>
                </a>
            @endcan

            @can('auditoria.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Auditoria
                    </span>
                </a>
            @endcan

            @can('asistente.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-sparkles class="w-5 h-5 shrink-0" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Asistente IA
                    </span>
                </a>
            @endcan

            @can('alertas.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-bell-alert class="w-5 h-5 shrink-0 text-[#FBBF24]" />
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                        Alertas Inteligentes
                    </span>
                </a>
            @endcan

        @endcanany

    </nav>

    <!-- FOOTER / CERRAR SESION -->
    <div class="border-t border-slate-800/60 p-2 min-w-[256px]">

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                class="w-full text-slate-300 hover:text-[#EF4444] hover:bg-[#EF4444]/10 {{ $linkBase }}">

                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 shrink-0 text-[#EF4444]" />

                <span class="ml-3 whitespace-nowrap opacity-0 group-hover/sidebar:opacity-100 transition-opacity duration-300">
                    Cerrar sesion
                </span>
            </button>
        </form>

    </div>

</aside>

<!-- BOTON MOVIL -->
<button
    @click="abierto = true"
    class="fixed top-3 left-3 z-40 lg:hidden bg-[#172554] text-white p-2 rounded-xl shadow-lg border border-[#172554]/50">

    <x-heroicon-o-bars-3 class="w-5 h-5" />

</button>