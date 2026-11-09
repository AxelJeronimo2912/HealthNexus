<div class="space-y-6">

    @if (session('success'))
        <div class="p-3 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-sm">
            {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="p-3 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Paciente</p>
                <p class="font-bold text-slate-800">{{ $solicitud->paciente?->nombre_completo }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Servicio</p>
                <p class="font-medium text-slate-700">{{ $solicitud->servicio?->nombre }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tipo de estudio</p>
                <p class="font-medium text-slate-700">{{ $solicitud->tipo_estudio }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Estado</p>
                @can('estudios.editar')
                    <form method="POST" action="{{ route('estudios.estado', $solicitud) }}"
                        class="flex gap-2 items-center">
                        @csrf @method('PATCH')
                        <select name="estado"
                            class="bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-semibold focus:ring-0 focus:border-brand-500">
                            @foreach (['solicitado', 'en_proceso', 'resultado_listo', 'entregado', 'cancelado'] as $e)
                                <option value="{{ $e }}" @selected($solicitud->estado === $e)>
                                    {{ ucfirst(str_replace('_', ' ', $e)) }}
                                </option>
                            @endforeach
                        </select>
                        <button
                            class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg text-xs transition-colors">
                            Actualizar
                        </button>
                    </form>
                @else
                    <span @class([
                        'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide',
                        'bg-amber-100 text-amber-700' => $solicitud->estado === 'solicitado',
                        'bg-blue-100 text-blue-700' => $solicitud->estado === 'en_proceso',
                        'bg-emerald-100 text-emerald-700' =>
                            $solicitud->estado === 'resultado_listo',
                        'bg-slate-200 text-slate-700' => $solicitud->estado === 'entregado',
                        'bg-rose-100 text-rose-700' => $solicitud->estado === 'cancelado',
                    ])>
                        {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                    </span>
                @endcan
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Solicitado el</p>
                <p class="font-medium text-slate-700">{{ $solicitud->solicitado_en?->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cuenta</p>
                <p class="font-medium">
                    @if ($solicitud->cuenta)
                        <a href="{{ route('cuentas.paciente', $solicitud->paciente) }}" target="_blank"
                            class="text-brand-600 hover:underline inline-flex items-center gap-1">
                            <x-heroicon-o-document-text class="w-4 h-4" />
                            {{ $solicitud->cuenta->folio }}
                        </a>
                    @else
                        <span class="text-slate-400">Sin cargo</span>
                    @endif
                </p>
            </div>
        </div>

        @if ($solicitud->indicaciones || $solicitud->notas)
            <div class="mt-5 pt-5 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if ($solicitud->indicaciones)
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Indicaciones
                            clínicas</p>
                        <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            {{ $solicitud->indicaciones }}</p>
                    </div>
                @endif
                @if ($solicitud->notas)
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Notas internas</p>
                        <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            {{ $solicitud->notas }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    @can('estudios.editar')
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                    <x-heroicon-o-cloud-arrow-up class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Subir Resultados</h3>
                    <p class="text-xs text-slate-500">Archivos PDF, Imágenes o DICOM (Máx. 50MB)</p>
                </div>
            </div>

            <form method="POST" action="{{ route('estudios.archivos.store', $solicitud) }}" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                <div>
                    <input type="file" name="archivos[]" multiple required
                        accept=".pdf,.jpg,.jpeg,.png,.webp,.dcm,.dicom"
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition-colors cursor-pointer border border-slate-200 rounded-xl bg-slate-50">
                </div>
                <div>
                    <input type="text" name="descripcion" placeholder="Descripción opcional..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:border-brand-500 focus:ring-0 transition-colors">
                </div>
                <button
                    class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    Subir archivos
                </button>
            </form>

            @if ($errors->any())
                <div class="mt-4 p-4 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700">
                    @foreach ($errors->all() as $e)
                        <div class="flex items-center gap-2"><x-heroicon-o-exclamation-circle class="w-4 h-4" />
                            {{ $e }}</div>
                    @endforeach
                </div>
            @endif
        </div>
    @endcan

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <x-heroicon-o-document-duplicate class="w-5 h-5" />
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Archivos Adjuntos</h3>
                <p class="text-xs text-slate-500">{{ $solicitud->archivos->count() }} archivo(s) encriptado(s)</p>
            </div>
        </div>

        @if ($solicitud->archivos->isEmpty())
            <div class="text-center py-8 border-2 border-dashed border-slate-100 rounded-xl">
                <x-heroicon-o-folder-open class="w-10 h-10 text-slate-300 mx-auto mb-2" />
                <p class="text-sm font-medium text-slate-500">Aún no hay archivos cargados.</p>
            </div>
        @else
            <ul class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">
                @foreach ($solicitud->archivos as $archivo)
                    <li
                        class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white hover:bg-slate-50 transition-colors">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="p-2 bg-slate-100 rounded-lg text-slate-500 shrink-0">
                                @if ($archivo->tipo === 'pdf')
                                    <x-heroicon-o-document-text class="w-5 h-5" />
                                @elseif($archivo->tipo === 'imagen')
                                    <x-heroicon-o-photo class="w-5 h-5" />
                                @else
                                    <x-heroicon-o-cpu-chip class="w-5 h-5" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-sm text-slate-800 truncate"
                                    title="{{ $archivo->nombre_original }}">
                                    {{ $archivo->nombre_original }}
                                </p>
                                <p class="text-[11px] font-medium text-slate-400 mt-0.5">
                                    {{ strtoupper($archivo->tipo) }} ·
                                    {{ number_format($archivo->tamano_bytes / 1024, 1) }} KB ·
                                    {{ $archivo->created_at->format('d/m/Y H:i') }}
                                    @if ($archivo->user)
                                        · Subido por: {{ $archivo->user->name }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('estudios.archivos.ver', $archivo) }}" target="_blank"
                                class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5">
                                <x-heroicon-o-eye class="w-3.5 h-3.5" /> Ver
                            </a>
                            <a href="{{ route('estudios.archivos.descargar', $archivo) }}"
                                class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5">
                                <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" /> Descargar
                            </a>

                            @can('estudios.editar')
                                <form method="POST" action="{{ route('estudios.archivos.destroy', $archivo) }}"
                                    onsubmit="return confirm('¿Eliminar este archivo permanentemente?')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition-colors"
                                        title="Eliminar archivo">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
