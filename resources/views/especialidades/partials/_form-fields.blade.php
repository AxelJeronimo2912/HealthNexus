@php
    $prefix = $prefix ?? 'create';
    $isError = $isError ?? false;

    $inputCls =
        'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none';
    $inputErrCls =
        'w-full bg-rose-50 border border-rose-400 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-rose-500 focus:ring-0 outline-none';
    $labelCls = 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5';
    $errCls = 'text-xs text-rose-600 mt-1 font-semibold';

    $cls = fn($field) => $isError && $errors->has($field) ? $inputErrCls : $inputCls;
@endphp

{{-- Errores generales --}}
@if ($isError)
    <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold">
        <p class="font-bold mb-1">Corrige los siguientes errores:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Código --}}
    <div>
        <label class="{{ $labelCls }}">Código *</label>
        <input type="text" name="codigo" id="{{ $prefix }}_codigo" value="{{ $isError ? old('codigo') : '' }}"
            maxlength="20" required placeholder="Ej: CARD-01" class="{{ $cls('codigo') }}">
        @if ($isError && $errors->has('codigo'))
            <p class="{{ $errCls }}">{{ $errors->first('codigo') }}</p>
        @endif
    </div>

    {{-- Nombre --}}
    <div>
        <label class="{{ $labelCls }}">Nombre *</label>
        <input type="text" name="nombre" id="{{ $prefix }}_nombre" value="{{ $isError ? old('nombre') : '' }}"
            maxlength="150" required placeholder="Ej: Cardiología" class="{{ $cls('nombre') }}">
        @if ($isError && $errors->has('nombre'))
            <p class="{{ $errCls }}">{{ $errors->first('nombre') }}</p>
        @endif
    </div>

    {{-- Grupo --}}
    <div>
        <label class="{{ $labelCls }}">Grupo *</label>
        @php $grupoSel = $isError ? old('grupo', 'clinica') : 'clinica'; @endphp
        <select name="grupo" id="{{ $prefix }}_grupo" required class="{{ $cls('grupo') }}">
            <option value="clinica" @selected($grupoSel === 'clinica')>Clínica</option>
            <option value="quirurgica" @selected($grupoSel === 'quirurgica')>Quirúrgica</option>
            <option value="diagnostica" @selected($grupoSel === 'diagnostica')>Diagnóstica</option>
            <option value="basica" @selected($grupoSel === 'basica')>Básica</option>
            <option value="otra" @selected($grupoSel === 'otra')>Otra</option>
        </select>
        @if ($isError && $errors->has('grupo'))
            <p class="{{ $errCls }}">{{ $errors->first('grupo') }}</p>
        @endif
    </div>

    {{-- Duración --}}
    <div>
        <label class="{{ $labelCls }}">Duración por defecto (min) *</label>
        <input type="number" name="duracion_consulta_default" id="{{ $prefix }}_duracion_consulta_default"
            value="{{ $isError ? old('duracion_consulta_default', 30) : 30 }}" min="5" max="240"
            step="5" required class="{{ $cls('duracion_consulta_default') }}">
        @if ($isError && $errors->has('duracion_consulta_default'))
            <p class="{{ $errCls }}">{{ $errors->first('duracion_consulta_default') }}</p>
        @endif
    </div>

    {{-- Color --}}
    <div>
        <label class="{{ $labelCls }}">Color</label>
        @php $colorVal = $isError ? old('color', '#3B82F6') : '#3B82F6'; @endphp
        <div class="flex gap-2">
            <input type="color" id="{{ $prefix }}_color_picker" value="{{ $colorVal }}"
                class="w-12 h-[42px] rounded-xl border border-slate-200 cursor-pointer bg-white"
                onchange="document.getElementById('{{ $prefix }}_color').value = this.value.toUpperCase()">
            <input type="text" name="color" id="{{ $prefix }}_color" value="{{ $colorVal }}"
                maxlength="7" placeholder="#3B82F6" class="{{ $cls('color') }}">
        </div>
        @if ($isError && $errors->has('color'))
            <p class="{{ $errCls }}">{{ $errors->first('color') }}</p>
        @endif
    </div>

    {{-- Activo --}}
    <div class="flex items-end">
        <label class="inline-flex items-center gap-2 cursor-pointer pb-2.5">
            <input type="checkbox" name="activo" id="{{ $prefix }}_activo" value="1"
                @checked($isError ? (bool) old('activo') : true)
                class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-0 cursor-pointer">
            <span class="text-xs font-bold text-slate-700">Especialidad activa</span>
        </label>
    </div>
</div>

{{-- Descripción --}}
<div>
    <label class="{{ $labelCls }}">Descripción</label>
    <textarea name="descripcion" id="{{ $prefix }}_descripcion" rows="3" maxlength="1000"
        placeholder="Descripción breve de la especialidad..." class="{{ $cls('descripcion') }}">{{ $isError ? old('descripcion') : '' }}</textarea>
    @if ($isError && $errors->has('descripcion'))
        <p class="{{ $errCls }}">{{ $errors->first('descripcion') }}</p>
    @endif
</div>
