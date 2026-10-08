<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle del Dispositivo
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Información técnica, accesos y estado</p>
            </div>
            <a href="{{ route('dispositivos.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">

            {{-- ============ ENCABEZADO ============ --}}
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 rounded-3xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                        <x-dynamic-component :component="'heroicon-o-' . $dispositivo->tipo_icono" class="w-6 h-6" />
                    </div>
                    <div class="min-w-0">
                        <p class="{{ $labelCls }}">Dispositivo</p>
                        <p class="text-2xl font-black text-slate-800 tracking-tight mt-0.5 truncate">
                            {{ $dispositivo->nombre ?? 'Dispositivo desconocido' }}
                        </p>
                        <p class="text-xs font-medium text-slate-400 mt-0.5">
                            {{ $dispositivo->navegador }} — {{ $dispositivo->sistema_operativo }}
                        </p>
                    </div>
                </div>
                <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $dispositivo->estado_color }} self-start">
                    {{ $dispositivo->estado_label }}
                </span>
            </div>

            {{-- ============ INFO ============ --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                    <dt class="{{ $labelCls }}">Usuario</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $dispositivo->user?->nombre_completo ?? '—' }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Tipo</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ ucfirst($dispositivo->tipo ?? '—') }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Navegador</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $dispositivo->navegador ?? '—' }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                    <dt class="{{ $labelCls }}">Sistema operativo</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $dispositivo->sistema_operativo ?? '—' }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">IP de registro</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                            {{ $dispositivo->ip_registro ?? '—' }}
                        </span>
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Última IP</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                            {{ $dispositivo->ip_ultimo_acceso ?? '—' }}
                        </span>
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Último acceso</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $dispositivo->ultimo_acceso?->format('d/m/Y H:i') ?? '—' }}
                    </dd>
                </div>

                <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Total de accesos</dt>
                    <dd class="text-lg font-black text-indigo-700 tracking-tight mt-0.5">
                        {{ $dispositivo->total_accesos }}
                    </dd>
                </div>

                @if ($dispositivo->aprobadoPor)
                    <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-3 sm:col-span-2">
                        <dt class="{{ $labelCls }}">Aprobado por</dt>
                        <dd class="text-xs font-extrabold text-emerald-800 mt-1">
                            {{ $dispositivo->aprobadoPor->nombre_completo }}
                            <span class="font-medium text-emerald-600">
                                el {{ $dispositivo->aprobado_en?->format('d/m/Y H:i') }}
                            </span>
                        </dd>
                    </div>
                @endif
            </dl>

            {{-- ============ USER AGENT ============ --}}
            <div class="pt-5 border-t border-slate-100">
                <p class="{{ $labelCls }} mb-2">User Agent</p>
                <p
                    class="text-[11px] font-mono text-slate-600 bg-slate-50 border border-slate-100 rounded-2xl p-4 break-all leading-relaxed">
                    {{ $dispositivo->user_agent ?? '—' }}
                </p>
            </div>

            {{-- ============ ACCIONES ============ --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                <a href="{{ route('dispositivos.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Volver
                </a>

                @if (auth()->user()->hasRole('administrador') && !$dispositivo->confiable && $dispositivo->activo)
                    <form action="{{ route('dispositivos.confiar', $dispositivo) }}" method="POST">
                        @csrf
                        <button
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Marcar como confiable
                        </button>
                    </form>
                @endif

                @if ($dispositivo->activo)
                    <form action="{{ route('dispositivos.bloquear', $dispositivo) }}" method="POST"
                        onsubmit="return confirm('¿Bloquear este dispositivo?')">
                        @csrf
                        <button
                            class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Bloquear
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
