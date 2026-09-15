@php
    $user = auth()->user();
    $rol = $user->getRoleNames()->first();

    $linkBase = 'flex items-center px-3 py-2 rounded-md hover:bg-slate-800 transition-colors';
    $linkActivo = 'bg-slate-800 text-white';
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-200 transform transition-transform
              lg:translate-x-0 lg:static lg:inset-0"
    :class="abierto ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    <div class="flex items-center justify-between h-16 px-4 border-b border-slate-800">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <x-heroicon-s-heart class="w-6 h-6 text-blue-400" />
            <span class="text-lg font-bold text-white">HealthNexus</span>
        </a>
        <button @click="abierto = false" class="lg:hidden text-slate-400 hover:text-white">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
    </div>

    <div class="px-4 py-3 border-b border-slate-800 text-xs">
        <p class="text-slate-400">Rol activo:</p>
        <p class="text-white font-semibold">{{ ucfirst($rol ?? 'Sin rol') }}</p>
    </div>

    <nav class="flex-1 overflow-y-auto px-2 py-4 space-y-1 text-sm">

        {{-- PANEL --}}
        <p class="px-3 pt-2 pb-1 text-xs uppercase text-slate-500 tracking-wider">Panel</p>
        <a href="{{ route('dashboard') }}"
            class="{{ $linkBase }} {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? $linkActivo : '' }}">
            <x-heroicon-o-home class="w-5 h-5" />
            <span class="ml-2">Dashboard</span>
        </a>

        {{-- GESTIÓN HOSPITALARIA --}}
        @canany(['pacientes.ver', 'admision.ver', 'servicios.ver', 'especialidades.ver', 'citas.ver', 'turnos.ver'])
            <p class="px-3 pt-4 pb-1 text-xs uppercase text-slate-500 tracking-wider">Gestión Hospitalaria</p>

            @can('pacientes.ver')
                <a href="{{ route('pacientes.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.pacientes.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-user-group class="w-5 h-5" />
                    <span class="ml-2">Pacientes</span>
                </a>
            @endcan

            @can('admision.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                    <span class="ml-2">Admisión</span>
                </a>
            @endcan

            @can('servicios.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-building-office-2 class="w-5 h-5" />
                    <span class="ml-2">Servicios</span>
                </a>
            @endcan

            @can('especialidades.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-academic-cap class="w-5 h-5" />
                    <span class="ml-2">Especialidades</span>
                </a>
            @endcan

            @can('citas.ver')
                <a href="{{ route('citas.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('citas.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-calendar-days class="w-5 h-5" />
                    <span class="ml-2">Citas</span>
                </a>
            @endcan

            @can('turnos.ver')
                <a href="{{ route('admin.turnos.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.turnos.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    <span class="ml-2">Turnos</span>
                </a>
            @endcan

            @can('camas.ver')
                <a href="{{ route('camas.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('camas.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-home-modern class="w-5 h-5" />
                    <span class="ml-2">Camas</span>
                </a>
            @endcan
        @endcanany
        {{-- AGENDA MÉDICA --}}
        @can('agenda.ver')
            <p class="px-3 pt-4 pb-1 text-xs uppercase text-slate-500 tracking-wider">Agenda</p>
            <a href="{{ route('agenda.index') }}"
                class="{{ $linkBase }} {{ request()->routeIs('agenda.*') ? $linkActivo : '' }}">
                <x-heroicon-o-calendar-days class="w-5 h-5" />
                <span class="ml-2">Calendario de Citas</span>
            </a>
            <a href="{{ route('agenda.create') }}"
                class="{{ $linkBase }} {{ request()->routeIs('agenda.create') ? $linkActivo : '' }}">
                <x-heroicon-o-plus-circle class="w-5 h-5" />
                <span class="ml-2">Nueva Cita</span>
            </a>
        @endcan

        {{-- ATENCIÓN CLÍNICA --}}
        @canany(['expediente.ver', 'consultas.ver', 'enfermeria.ver', 'seguimiento.ver'])
            <p class="px-3 pt-4 pb-1 text-xs uppercase text-slate-500 tracking-wider">Atención Clínica</p>

            @can('expediente.ver')
                <a href="{{ route('expedientes.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('expedientes.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-document-text class="w-5 h-5" />
                    <span class="ml-2">Expediente</span>
                </a>
            @endcan

            @can('consultas.ver')
                <a href="{{ route('citas.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('citas.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                    <span class="ml-2">Consultas</span>
                </a>
            @endcan

            @can('enfermeria.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-heart class="w-5 h-5" />
                    <span class="ml-2">Enfermería</span>
                </a>
            @endcan
            @can('seguimiento.ver')
                <a href="{{ route('seguimientos.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('seguimientos.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                    <span class="ml-2">Seguimiento</span>
                </a>
            @endcan
            @can('signos-vitales.ver')
                <a href="{{ route('signos-vitales.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('signos-vitales.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-heart class="w-5 h-5" />
                    <span class="ml-2">Signos Vitales</span>
                </a>
            @endcan
        @endcanany

        {{-- FARMACIA E INVENTARIO --}}
        @canany(['medicamentos.ver', 'existencias.ver', 'movimientos.ver', 'prediccion.ver'])
            <p class="px-3 pt-4 pb-1 text-xs uppercase text-slate-500 tracking-wider">Farmacia e Inventario</p>

            @can('medicamentos.ver')
                <a href="{{ route('medicamentos.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('medicamentos.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-beaker class="w-5 h-5" />
                    <span class="ml-2">Medicamentos</span>
                </a>
            @endcan
            @can('dispensaciones.ver')
                <a href="{{ route('dispensaciones.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('dispensaciones.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                    <span class="ml-2">Recetas</span>
                </a>
            @endcan
            @can('existencias.ver')
                <a href="{{ route('existencias.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('existencias.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-archive-box class="w-5 h-5" />
                    <span class="ml-2">Existencias</span>
                </a>
            @endcan

            @can('movimientos.ver')
                <a href="{{ route('movimientos.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('movimientos.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-arrow-path class="w-5 h-5" />
                    <span class="ml-2">Movimientos</span>
                </a>
            @endcan

            @can('prediccion.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-cpu-chip class="w-5 h-5" />
                    <span class="ml-2">Predicción IA</span>
                </a>
            @endcan
        @endcanany

        {{-- SEGURIDAD E INTELIGENCIA --}}
        @canany(['usuarios.ver', 'roles.ver', 'dispositivos.ver', 'auditoria.ver', 'asistente.ver', 'alertas.ver'])
            <p class="px-3 pt-4 pb-1 text-xs uppercase text-slate-500 tracking-wider">Seguridad e Inteligencia</p>

            @can('usuarios.ver')
                <a href="{{ route('admin.users.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.users.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-users class="w-5 h-5" />
                    <span class="ml-2">Usuarios</span>
                </a>
            @endcan

            @can('roles.ver')
                <a href="{{ route('admin.roles.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.roles.*') ? $linkActivo : '' }}">
                    <x-heroicon-o-shield-check class="w-5 h-5" />
                    <span class="ml-2">Roles y Permisos</span>
                </a>
            @endcan

            @can('dispositivos.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-device-phone-mobile class="w-5 h-5" />
                    <span class="ml-2">Dispositivos</span>
                </a>
            @endcan

            @can('auditoria.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                    <span class="ml-2">Auditoría</span>
                </a>
            @endcan

            @can('asistente.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-sparkles class="w-5 h-5" />
                    <span class="ml-2">Asistente IA</span>
                </a>
            @endcan

            @can('alertas.ver')
                <a href="#" class="{{ $linkBase }}">
                    <x-heroicon-o-bell-alert class="w-5 h-5" />
                    <span class="ml-2">Alertas Inteligentes</span>
                </a>
            @endcan
        @endcanany

    </nav>

    <div class="border-t border-slate-800 p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center {{ $linkBase }}">
                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                <span class="ml-2">Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>

<button @click="abierto = true" class="fixed top-3 left-3 z-50 lg:hidden bg-slate-900 text-white p-2 rounded-md">
    <x-heroicon-o-bars-3 class="w-5 h-5" />
</button>
