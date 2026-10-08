<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle de Administración
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Información completa del medicamento administrado</p>
            </div>
            <a href="{{ route('enfermeria.administraciones.index') }}"
                class="bg-white hover:bg-pink-50 text-slate-700 hover:text-pink-600 border border-slate-200 hover:border-pink-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
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

            {{-- ============ ALERTA REACCIÓN ADVERSA ============ --}}
            @if ($administracion->reaccion_adversa)
                <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl flex items-start gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-extrabold text-rose-800">Reacción adversa reportada</p>
                        <p class="text-xs text-rose-600 mt-0.5">Este registro requiere seguimiento clínico.</p>
                    </div>
                </div>
            @endif

            {{-- ============ DATOS ============ --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Fecha de administración</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $administracion->administrado_en->format('d/m/Y H:i') }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Paciente</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $administracion->paciente?->nombre_completo ?? '—' }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                    <dt class="{{ $labelCls }}">Medicamento</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $administracion->medicamento?->nombre }}
                        @if ($administracion->medicamento?->concentracion)
                            <span class="text-slate-400 font-medium">·
                                {{ $administracion->medicamento->concentracion }}</span>
                        @endif
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Dosis</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                            {{ $administracion->dosis }}
                        </span>
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Vía</dt>
                    <dd class="mt-1">
                        <span
                            class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                            {{ $administracion->via }}
                        </span>
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                    <dt class="{{ $labelCls }}">Administrado por</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $administracion->user?->nombre_completo ?? '—' }}
                    </dd>
                </div>
            </dl>

            {{-- ============ OBSERVACIONES ============ --}}
            @if ($administracion->observaciones)
                <div class="border-t border-slate-100 pt-5">
                    <p class="{{ $labelCls }} mb-2">Observaciones</p>
                    <p
                        class="whitespace-pre-line text-xs text-slate-600 leading-relaxed bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                        {{ $administracion->observaciones }}
                    </p>
                </div>
            @endif

            {{-- ============ ACCIONES ============ --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                <a href="{{ route('enfermeria.administraciones.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Volver
                </a>
                @if (auth()->user()->hasRole('administrador'))
                    <form action="{{ route('enfermeria.administraciones.destroy', $administracion) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar y devolver stock?')">
                        @csrf @method('DELETE')
                        <button
                            class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar y devolver stock
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
