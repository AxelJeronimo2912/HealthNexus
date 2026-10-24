{{--
    Sidebar de HealthNexus.
    - Escritorio: fijable (expandido 256px / colapsado 72px), se recuerda en localStorage.
      Colapsado muestra tooltips con el nombre de cada módulo.
    - Móvil: drawer con fondo oscurecido, se abre desde el topbar.
    Los módulos vienen de config/navegacion.php (App\Support\Navegacion) y respetan permisos.
    Requiere estar dentro de x-data="hnLayout" (layouts/app.blade.php).
--}}
@php
    use App\Support\Navegacion;

    $user = auth()->user();
    $rol = $user?->getRoleNames()->first();
    $secciones = Navegacion::secciones($user);
    $dashboardRoute = Navegacion::rutaDashboard($user);
    $dashboardActivo = Navegacion::dashboardActivo();

    $linkBase = 'group/link relative flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition-colors duration-150';
    $linkIdle = 'text-slate-300/90 hover:bg-white/[.06] hover:text-white';
    $linkActive = 'bg-white/[.10] text-white shadow-[inset_0_0_0_1px_rgb(255_255_255/0.06)]';
@endphp

{{-- Fondo oscurecido en móvil --}}
<div x-show="mobileOpen" x-cloak x-transition.opacity @click="mobileOpen = false"
    class="fixed inset-0 z-40 bg-brand-950/50 backdrop-blur-sm lg:hidden"></div>

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-brand-950 text-slate-200 transition-[width,transform] duration-300 ease-in-out lg:translate-x-0"
    :class="{
        '!translate-x-0': mobileOpen,
        'lg:w-64': pinned,
        'lg:w-[72px]': !pinned
    }"
    aria-label="Navegación principal">

    {{-- Brillo sutil de marca --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-48 bg-gradient-to-b from-accent-600/15 to-transparent"></div>

    {{-- LOGO --}}
    <div class="relative flex h-16 shrink-0 items-center justify-between px-[18px]">
        <a href="{{ $dashboardRoute }}" class="flex min-w-0 items-center gap-2.5" title="HealthNexus">
            <x-application-logo variant="light" size="h-9 w-9" />
            <span class="whitespace-nowrap text-[17px] font-bold leading-none tracking-tight" :class="!pinned && 'lg:hidden'">
                <span class="text-white">Health</span><span class="text-accent-300">Nexus</span>
            </span>
        </a>
        <button type="button" @click="mobileOpen = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Cerrar menú">
            <x-heroicon-o-x-mark class="h-5 w-5" />
        </button>
    </div>

    {{-- MENÚ --}}
    <nav class="relative flex-1 overflow-y-auto overflow-x-hidden scroll-thin px-3 pb-4 pt-2"
        @mouseover="const a = $event.target.closest('a'); if (a) a.style.setProperty('--tip-y', (a.getBoundingClientRect().top + a.offsetHeight / 2) + 'px')">

        <a href="{{ $dashboardRoute }}" class="{{ $linkBase }} {{ $dashboardActivo ? $linkActive : $linkIdle }}"
            :class="!pinned && 'lg:justify-center lg:px-0'" @if ($dashboardActivo) aria-current="page" @endif>
            @if ($dashboardActivo)
                <span class="absolute left-0 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-r-full bg-accent-400"></span>
            @endif
            <x-heroicon-o-squares-2x2 class="h-5 w-5 shrink-0 {{ $dashboardActivo ? 'text-accent-300' : 'text-slate-400 group-hover/link:text-slate-200' }}" />
            <span class="truncate" :class="!pinned && 'lg:hidden'">Dashboard</span>
            <span class="nav-tip hidden" :class="!pinned && 'lg:block'">Dashboard</span>
        </a>

        @foreach ($secciones as $seccion)
            <div class="mt-5">
                <p class="mb-1.5 px-3 text-[11px] font-semibold uppercase tracking-[.08em] text-slate-500"
                    :class="!pinned && 'lg:hidden'">
                    {{ $seccion['titulo'] }}
                </p>
                <div class="mx-auto mb-2 hidden h-px w-8 bg-white/10" :class="!pinned && 'lg:block'"></div>

                <div class="space-y-0.5">
                    @foreach ($seccion['items'] as $item)
                        <a href="{{ $item['url'] }}"
                            class="{{ $linkBase }} {{ $item['es_activo'] ? $linkActive : $linkIdle }}"
                            :class="!pinned && 'lg:justify-center lg:px-0'"
                            @if ($item['es_activo']) aria-current="page" @endif
                            @if ($item['url_contador'])
                                x-data="{ total: 0 }"
                                x-init="fetch(@js($item['url_contador']), { headers: { 'Accept': 'application/json' } }).then(r => r.json()).then(d => total = d.total ?? 0).catch(() => {})"
                            @endif>

                            @if ($item['es_activo'])
                                <span class="absolute left-0 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-r-full bg-accent-400"></span>
                            @endif

                            <span class="relative shrink-0">
                                <x-dynamic-component :component="'heroicon-o-' . $item['icono']"
                                    class="h-5 w-5 {{ $item['es_activo'] ? 'text-accent-300' : 'text-slate-400 group-hover/link:text-slate-200' }}" />
                                @if ($item['url_contador'])
                                    <span x-show="total > 0 && !pinned" x-cloak
                                        class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-brand-950"></span>
                                @endif
                            </span>

                            <span class="truncate" :class="!pinned && 'lg:hidden'">{{ $item['label'] }}</span>

                            @if ($item['url_contador'])
                                <span x-show="total > 0" x-cloak x-text="total"
                                    class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-[11px] font-bold text-white tabular"
                                    :class="!pinned && 'lg:hidden'"></span>
                            @endif

                            <span class="nav-tip hidden" :class="!pinned && 'lg:block'">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    {{-- PIE: usuario + fijar sidebar --}}
    <div class="relative shrink-0 border-t border-white/[.08] p-3">
        <div class="flex items-center gap-3 rounded-xl px-2 py-2" :class="!pinned && 'lg:justify-center lg:px-0'">
            <div class="relative grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-accent-400 to-brand-500 text-xs font-bold text-white">
                {{ Navegacion::iniciales($user?->name) }}
                <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-400 ring-2 ring-brand-950" title="En línea"></span>
            </div>
            <div class="min-w-0 flex-1" :class="!pinned && 'lg:hidden'">
                <p class="truncate text-sm font-semibold text-white">{{ $user?->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ ucfirst($rol ?? 'Sin rol') }}</p>
            </div>
        </div>

        <button type="button" @click="togglePinned()"
            class="mt-1 hidden w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-400 transition hover:bg-white/[.06] hover:text-white lg:flex"
            :class="!pinned && 'lg:justify-center lg:px-0'"
            :title="pinned ? 'Colapsar menú' : 'Expandir menú'">
            <span class="shrink-0 transition-transform duration-300" :class="!pinned && 'rotate-180'">
                <x-heroicon-o-chevron-double-left class="h-5 w-5" />
            </span>
            <span :class="!pinned && 'lg:hidden'">Colapsar menú</span>
        </button>
    </div>
</aside>
