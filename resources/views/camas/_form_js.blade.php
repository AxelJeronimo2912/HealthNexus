<div class="space-y-5 text-sm">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Código *</label>
            <input type="text" name="codigo" x-model="modalEditar.codigo" required placeholder="Ej. CAMA-101"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm uppercase">
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Nombre descriptivo</label>
            <input type="text" name="nombre" x-model="modalEditar.nombre" placeholder="Ej. Cama junto a ventana"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Área</label>
            <select name="area" x-model="modalEditar.area" class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
                <option value="">Seleccionar</option>
                @foreach (['Hospitalización', 'Urgencias', 'UCI', 'Pediatría', 'Ginecología', 'Cirugía', 'Recuperación', 'Aislamiento'] as $area)
                    <option value="{{ $area }}">{{ $area }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Piso</label>
            <input type="text" name="piso" x-model="modalEditar.piso" placeholder="Ej. Piso 1"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Ala</label>
            <input type="text" name="ala" x-model="modalEditar.ala" placeholder="Ej. Norte"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Habitación</label>
            <input type="text" name="habitacion" x-model="modalEditar.habitacion" placeholder="Ej. 101"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Tipo *</label>
            <select name="tipo" x-model="modalEditar.tipo" required class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
                @foreach (['general' => 'General', 'pediatrica' => 'Pediátrica', 'uci' => 'UCI', 'aislamiento' => 'Aislamiento', 'recuperacion' => 'Recuperación', 'urgencias' => 'Urgencias'] as $key => $lbl)
                    <option value="{{ $key }}">{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Estado *</label>
            <select name="estado" x-model="modalEditar.estado" required class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
                <option value="disponible">Disponible</option>
                <option value="ocupada">Ocupada</option>
                <option value="mantenimiento">Mantenimiento</option>
                <option value="limpieza">Limpieza</option>
                <option value="fuera_servicio">Fuera de Servicio</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block font-semibold text-slate-600 mb-1">Notas adicionales</label>
        <textarea name="notas" rows="2" x-model="modalEditar.notas" class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm" placeholder="Observaciones de la cama..."></textarea>
    </div>
</div>