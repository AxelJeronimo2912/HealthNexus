<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle del Evento
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Información completa del registro de auditoría</p>
            </div>
            <a href="{{ route('auditoria.index') }}"
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
                <div class="min-w-0">
                    <span
                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $log->evento_color }}">
                        {{ $log->evento_label }}
                    </span>
                    <p class="text-lg font-extrabold text-slate-800 mt-3 leading-snug">
                        {{ $log->descripcion }}
                    </p>
                </div>
                <div class="bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-right shrink-0">
                    <p class="{{ $labelCls }}">Fecha</p>
                    <p class="text-xs font-extrabold text-slate-800 mt-1 font-mono">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>
            </div>

            {{-- ============ DATOS ============ --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                    <dt class="{{ $labelCls }}">Usuario</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $log->user_nombre }}
                        @if ($log->user_rol)
                            <span class="font-medium text-slate-400">({{ $log->user_rol }})</span>
                        @endif
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Módulo</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-bold">
                            {{ $log->modulo_label }}
                        </span>
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Evento</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1 font-mono">
                        {{ $log->evento }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Severidad</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ ucfirst($log->severidad) }}
                    </dd>
                </div>

                <div
                    class="{{ $log->es_sensible ? 'bg-rose-50/60 border-rose-100' : 'bg-slate-50/70 border-slate-100' }} border rounded-2xl p-3">
                    <dt
                        class="text-[10px] font-bold uppercase tracking-wider {{ $log->es_sensible ? 'text-rose-600/70' : 'text-slate-400' }}">
                        Sensible
                    </dt>
                    <dd
                        class="text-xs font-extrabold mt-1 {{ $log->es_sensible ? 'text-rose-700' : 'text-slate-800' }}">
                        {{ $log->es_sensible ? 'Sí' : 'No' }}
                    </dd>
                </div>
            </dl>

            {{-- ============ METADATOS ============ --}}
            @if ($log->metadata)
                <div class="pt-5 border-t border-slate-100">
                    <p class="{{ $labelCls }} mb-2">Metadatos</p>
                    <pre
                        class="text-[11px] font-mono text-slate-700 bg-slate-50 border border-slate-100 rounded-2xl p-4 overflow-x-auto leading-relaxed">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif

            {{-- ============ DATOS ANTES ============ --}}
            @if ($log->datos_antes && count($log->datos_antes))
                <div class="pt-5 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </div>
                        <p class="{{ $labelCls }}">Datos antes</p>
                    </div>
                    <pre
                        class="text-[11px] font-mono text-rose-900 bg-rose-50 border border-rose-100 rounded-2xl p-4 overflow-x-auto leading-relaxed">{{ json_encode($log->datos_antes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif

            {{-- ============ DATOS DESPUÉS ============ --}}
            @if ($log->datos_despues && count($log->datos_despues))
                <div class="pt-5 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="{{ $labelCls }}">Datos después</p>
                    </div>
                    <pre
                        class="text-[11px] font-mono text-emerald-900 bg-emerald-50 border border-emerald-100 rounded-2xl p-4 overflow-x-auto leading-relaxed">{{ json_encode($log->datos_despues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
