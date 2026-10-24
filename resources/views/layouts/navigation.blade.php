{{--
    Topbar de HealthNexus (reemplaza la barra de Laravel Breeze).
    Breadcrumb · búsqueda / paleta de comandos (Ctrl+K) · alertas · menú de usuario.
--}}
@php
    use App\Support\Navegacion;

    $user = auth()->user();
    $rol = $user?->getRoleNames()->first();
    $migas = Navegacion::breadcrumb($user);
    $puedeAlertas = $user?->can('alertas.ver') && Route::has('alertas.index');
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/80 backdrop-blur-md supports-[backdrop-filter]:bg-white/70">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">

        {{-- Menú móvil --}}
        <button type="button" @click="mobileOpen = true"
            class="-ml-1 rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 lg:hidden" aria-label="Abrir menú">
            <x-heroicon-o-bars-3 class="h-6 w-6" />
        </button>

        {{-- Breadcrumb --}}
        <nav class="hidden min-w-0 flex-1 md:block" aria-label="Ruta">
            <ol class="flex items-center gap-1.5 text-sm">
                <li>
                    <a href="{{ Navegacion::rutaDashboard($user) }}" class="text-slate-400 hover:text-slate-700" title="Inicio">
                        <x-heroicon-m-home class="h-4 w-4" />
                    </a>
                </li>
                @foreach ($migas as $miga)
                    <li class="flex min-w-0 items-center gap-1.5">
                        <x-heroicon-m-chevron-right class="h-4 w-4 shrink-0 text-slate-300" />
                        @if (!empty($miga['url']))
                            <a href="{{ $miga['url'] }}" class="truncate font-medium text-slate-500 hover:text-slate-800">{{ $miga['label'] }}</a>
                        @else
                            <span class="truncate {{ $loop->last ? 'font-semibold text-slate-800' : 'text-slate-500' }}">{{ $miga['label'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <div class="flex flex-1 items-center justify-end gap-1.5 sm:gap-2 md:flex-none">

            {{-- Búsqueda / paleta --}}
            <button type="button" @click="paletteOpen = true"
                class="group flex h-10 w-10 items-center justify-center gap-2 rounded-xl text-sm text-slate-500 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-50 hover:text-slate-700 sm:w-56 sm:justify-start sm:px-3">
                <x-heroicon-o-magnifying-glass class="h-[18px] w-[18px] shrink-0" />
                <span class="hidden flex-1 truncate whitespace-nowrap text-left sm:inline">Buscar…</span>
                <kbd class="hidden rounded-md bg-slate-100 px-1.5 py-0.5 font-sans text-[11px] font-semibold text-slate-500 ring-1 ring-inset ring-slate-200 sm:inline">Ctrl K</kbd>
            </button>

            {{-- Alertas --}}
            @if ($puedeAlertas)
                <a href="{{ route('alertas.index') }}"
                    x-data="{ total: 0 }"
                    x-init="fetch(@js(route('alertas.contar')), { headers: { 'Accept': 'application/json' } }).then(r => r.json()).then(d => total = d.total ?? 0).catch(() => {})"
                    class="relative grid h-10 w-10 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                    :title="total > 0 ? total + ' alertas activas' : 'Alertas'">
                    <x-heroicon-o-bell class="h-[22px] w-[22px]" />
                    <span x-show="total > 0" x-cloak x-text="total > 99 ? '99+' : total"
                        class="absolute right-1 top-1 min-w-[18px] rounded-full bg-red-500 px-1 text-center text-[10px] font-bold leading-[18px] text-white ring-2 ring-white tabular"></span>
                </a>
            @endif

            <div class="mx-1 hidden h-6 w-px bg-slate-200 sm:block"></div>

            {{-- Usuario --}}
            <div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2.5 rounded-xl py-1 pl-1 pr-2 transition hover:bg-slate-100" :aria-expanded="open">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-accent-500 to-brand-600 text-xs font-bold text-white">
                        {{ Navegacion::iniciales($user?->name) }}
                    </span>
                    <span class="hidden text-left leading-tight lg:block">
                        <span class="block max-w-[160px] truncate text-sm font-semibold text-slate-800">{{ $user?->name }}</span>
                        <span class="block text-xs text-slate-500">{{ ucfirst($rol ?? 'Sin rol') }}</span>
                    </span>
                    <x-heroicon-m-chevron-down class="hidden h-4 w-4 text-slate-400 lg:block" />
                </button>

                <div x-show="open" x-cloak
                    x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="absolute right-0 mt-2 w-64 origin-top-right overflow-hidden rounded-2xl bg-white shadow-pop ring-1 ring-slate-200">
                    <div class="border-b border-slate-100 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $user?->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $user?->email }}</p>
                        <x-badge tone="accent" size="sm" class="mt-2">{{ ucfirst($rol ?? 'Sin rol') }}</x-badge>
                    </div>
                    <div class="p-1.5">
                        @if (Route::has('profile.edit'))
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                <x-heroicon-o-user-circle class="h-[18px] w-[18px] text-slate-400" /> Mi perfil
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                <x-heroicon-o-arrow-right-on-rectangle class="h-[18px] w-[18px]" /> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Paleta de comandos (Ctrl / ⌘ + K) --}}
<div x-show="paletteOpen" x-cloak class="fixed inset-0 z-[90]"
    x-data="hnPalette(@js(Navegacion::paraPaleta($user)), @js($user?->can('pacientes.ver') && Route::has('pacientes.index') ? route('pacientes.index') : null))"
    x-effect="paletteOpen && open()"
    @keydown.window.ctrl.k.prevent="paletteOpen = !paletteOpen"
    @keydown.window.meta.k.prevent="paletteOpen = !paletteOpen"
    @keydown.escape.window="paletteOpen = false">
    <div class="absolute inset-0 bg-brand-950/40 backdrop-blur-sm" @click="paletteOpen = false" x-show="paletteOpen" x-transition.opacity></div>

    <div class="relative mx-auto mt-[12vh] w-[calc(100%-2rem)] max-w-xl overflow-hidden rounded-2xl bg-white shadow-pop ring-1 ring-slate-200"
        x-show="paletteOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-[.98]" x-transition:enter-end="opacity-100 scale-100">
        <div class="flex items-center gap-3 border-b border-slate-100 px-4">
            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400" />
            <input x-ref="input" x-model="q" @input="active = 0" type="text"
                @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)" @keydown.enter.prevent="go()"
                class="h-14 flex-1 border-0 bg-transparent p-0 text-[15px] text-slate-900 placeholder:text-slate-400 focus:ring-0"
                placeholder="Ir a un módulo o buscar un paciente…" />
            <kbd class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[11px] font-semibold text-slate-500">Esc</kbd>
        </div>

        <ul class="max-h-80 overflow-y-auto p-2" role="listbox">
            <template x-for="(item, i) in results" :key="item.url">
                <li>
                    <button type="button" @click="go(item)" @mouseenter="active = i"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left"
                        :class="active === i ? 'bg-brand-50' : ''">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg"
                            :class="item.search ? 'bg-accent-50 text-accent-600' : 'bg-slate-100 text-slate-500'">
                            <x-heroicon-o-arrow-up-right class="h-4 w-4" x-show="!item.search" />
                            <x-heroicon-o-user class="h-4 w-4" x-show="item.search" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-slate-800" x-text="item.label"></span>
                            <span class="block truncate text-xs text-slate-500" x-text="item.section"></span>
                        </span>
                        <x-heroicon-m-arrow-turn-down-left class="h-4 w-4 text-slate-400" x-show="active === i" />
                    </button>
                </li>
            </template>
            <li x-show="results.length === 0" class="px-3 py-8 text-center text-sm text-slate-500">
                Sin resultados para “<span x-text="q"></span>”.
            </li>
        </ul>
        <div class="flex items-center gap-4 border-t border-slate-100 bg-slate-50/60 px-4 py-2.5 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1"><kbd class="rounded bg-white px-1 ring-1 ring-slate-200">↑</kbd><kbd class="rounded bg-white px-1 ring-1 ring-slate-200">↓</kbd> navegar</span>
            <span class="inline-flex items-center gap-1"><kbd class="rounded bg-white px-1 ring-1 ring-slate-200">Enter</kbd> abrir</span>
        </div>
    </div>
</div>
