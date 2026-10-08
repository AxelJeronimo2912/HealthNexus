<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Editar Colaborador: {{ $user->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Actualiza la información del colaborador</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            @csrf
            @method('PUT')

            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Datos del colaborador</h3>
                    <p class="text-[11px] text-slate-400">Los campos marcados con * son obligatorios</p>
                </div>
            </div>

            <div class="p-6">
                @include('admin.users._form', ['user' => $user])
            </div>

            <div
                class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <p class="text-[11px] font-medium text-slate-400">
                    Revisa y guarda los cambios cuando todo esté listo.
                </p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Actualizar Colaborador
                    </button>
                </div>
            </div>
        </form>
    </div>

    @include('admin.users._scripts', ['u' => $user])
</x-app-layout>
