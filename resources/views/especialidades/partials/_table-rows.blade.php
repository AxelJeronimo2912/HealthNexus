@forelse ($especialidades as $esp)
    @php
        $espJson = json_encode(
            $esp->only([
                'id',
                'codigo',
                'nombre',
                'descripcion',
                'grupo',
                'duracion_consulta_default',
                'color',
                'activo',
            ]),
            JSON_HEX_APOS | JSON_HEX_QUOT,
        );
    @endphp
    <tr class="hover:bg-brand-50/30 transition-colors">
        <td class="px-5 py-3.5">
            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono font-bold">
                {{ $esp->codigo }}
            </span>
        </td>
        <td class="px-5 py-3.5">
            <div class="flex items-center gap-2">
                @if ($esp->color)
                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $esp->color_hex }}"></span>
                @endif
                <p class="text-xs font-bold text-slate-800">{{ $esp->nombre }}</p>
            </div>
        </td>
        <td class="px-5 py-3.5">
            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $esp->grupo_color }}">
                {{ $esp->grupo_label }}
            </span>
        </td>
        <td class="px-5 py-3.5 text-center text-xs font-semibold text-slate-700">
            {{ $esp->duracion_consulta_default }} min
        </td>
        <td class="px-5 py-3.5 text-center">
            <span
                class="px-2.5 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full text-xs font-bold">
                {{ $esp->medicos_count }}
            </span>
        </td>
        <td class="px-5 py-3.5 text-center">
            <span
                class="px-2.5 py-1 bg-accent-50 text-accent-700 border border-accent-100 rounded-full text-xs font-bold">
                {{ $esp->servicios_count }}
            </span>
        </td>
        <td class="px-5 py-3.5 text-center">
            <span
                class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-xs font-bold">
                {{ $esp->consultas_count }}
            </span>
        </td>
        <td class="px-5 py-3.5">
            @if ($esp->activo)
                <span
                    class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-xs font-bold uppercase tracking-wide">
                    Activa
                </span>
            @else
                <span
                    class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-xs font-bold uppercase tracking-wide">
                    Inactiva
                </span>
            @endif
        </td>
        <td class="px-5 py-3.5">
            <div class="flex justify-end gap-2 whitespace-nowrap">
                <button type="button" data-especialidad="{{ $espJson }}" onclick="openShowModal(this)"
                    class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all">
                    Ver
                </button>
                <button type="button" data-especialidad="{{ $espJson }}" onclick="openEditModal(this)"
                    class="px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg text-xs font-bold transition-all">
                    Editar
                </button>
                <button type="button" data-especialidad="{{ $espJson }}" onclick="openMedicosModal(this)"
                    class="px-3 py-1.5 bg-accent-50 hover:bg-accent-100 text-accent-700 rounded-lg text-xs font-bold transition-all">
                    Médicos
                </button>
                <button type="button" data-especialidad="{{ $espJson }}" onclick="openServiciosModal(this)"
                    class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-bold transition-all">
                    Servicios
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="px-5 py-12 text-center">
            <p class="text-xs font-semibold text-slate-400">Sin especialidades registradas.</p>
        </td>
    </tr>
@endforelse
