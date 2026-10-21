<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white flex items-center justify-center font-extrabold text-lg shadow">
                    {{ strtoupper(mb_substr($cita->paciente->nombre_completo, 0, 1)) }}
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Nueva consulta</p>
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
                        {{ $cita->paciente->nombre_completo }}
                    </h2>
                </div>
            </div>
            <span
                class="inline-flex items-center gap-1.5 self-start md:self-auto px-3 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span> Borrador sin guardar
            </span>
        </div>
    </x-slot>

    <div class="w-full px-4 sm:px-6 lg:px-10 pt-6 bg-slate-50 min-h-screen">
        <form action="{{ route('consultas.store', $cita) }}" method="POST" id="form-consulta">
            @csrf

            @include('consultas._form', [
                'consulta' => null,
                'cita' => $cita,
                'paciente' => $cita->paciente,
                'signo' => $signo,
                'medicamentos' => $medicamentos,
            ])

            {{-- Barra de acciones fija --}}
            <div
                class="sticky bottom-0 z-20 -mx-4 sm:-mx-6 lg:-mx-10 mt-8 px-4 sm:px-6 lg:px-10 py-4 bg-white/90 backdrop-blur border-t border-slate-200">
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                    <a href="{{ route('citas.show', $cita) }}"
                        class="px-5 py-2.5 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">
                        Cancelar
                    </a>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="submit" name="finalizar" value="0"
                            class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-sm font-semibold transition">
                            Guardar borrador
                        </button>
                        <button type="submit" name="finalizar" value="1"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-sm transition">
                            Finalizar consulta
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
