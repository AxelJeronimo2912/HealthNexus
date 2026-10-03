{{--
    Modal de registro rápido de paciente para urgencias.
    Requiere:
      - Un botón con id="btn-nuevo-paciente" en la vista padre.
      - Un <meta name="csrf-token"> en el layout.
      - La ruta admisiones.paciente-rapido definida.
--}}

<div id="modal-paciente" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" role="dialog"
    aria-modal="true" aria-labelledby="modal-paciente-titulo">

    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 id="modal-paciente-titulo" class="text-lg font-semibold text-gray-800">
                    Registro rápido de paciente
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Solo datos esenciales para urgencias. El expediente se completa después.
                </p>
            </div>
            <button type="button" id="btn-cerrar-paciente"
                class="text-gray-400 hover:text-gray-600 text-xl leading-none" aria-label="Cerrar">&times;</button>
        </div>

        <form id="form-paciente-rapido" class="space-y-3" novalidate>
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="rp_nombre" class="block text-xs font-medium">Nombre *</label>
                    <input type="text" id="rp_nombre" name="nombre" required autocomplete="off"
                        class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label for="rp_apellido_paterno" class="block text-xs font-medium">Apellido paterno *</label>
                    <input type="text" id="rp_apellido_paterno" name="apellido_paterno" required autocomplete="off"
                        class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label for="rp_apellido_materno" class="block text-xs font-medium">Apellido materno</label>
                    <input type="text" id="rp_apellido_materno" name="apellido_materno" autocomplete="off"
                        class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label for="rp_fecha_nacimiento" class="block text-xs font-medium">Fecha nacimiento *</label>
                    <input type="date" id="rp_fecha_nacimiento" name="fecha_nacimiento" required
                        max="{{ now()->subDay()->format('Y-m-d') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label for="rp_sexo" class="block text-xs font-medium">Sexo *</label>
                    <select id="rp_sexo" name="sexo" required class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">—</option>
                        <option value="hombre">Hombre</option>
                        <option value="mujer">Mujer</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label for="rp_telefono_principal" class="block text-xs font-medium">Teléfono</label>
                    <input type="text" id="rp_telefono_principal" name="telefono_principal" autocomplete="off"
                        class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label for="rp_tipo_sanguineo" class="block text-xs font-medium">Tipo sanguíneo</label>
                    <select id="rp_tipo_sanguineo" name="tipo_sanguineo"
                        class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">—</option>
                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rp_alergias" class="block text-xs font-medium">Alergias</label>
                    <input type="text" id="rp_alergias" name="alergias" placeholder="Se desconoce" autocomplete="off"
                        class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>

            <div>
                <label for="rp_enfermedades_cronicas" class="block text-xs font-medium">Enfermedades crónicas</label>
                <input type="text" id="rp_enfermedades_cronicas" name="enfermedades_cronicas"
                    placeholder="Se desconoce" autocomplete="off" class="w-full border-gray-300 rounded-md text-sm">
            </div>

            {{-- Errores de validación --}}
            <div id="error-paciente" class="hidden text-red-600 text-xs space-y-1"></div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" id="btn-cancelar-paciente"
                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                    Cancelar
                </button>
                <button type="submit" id="btn-guardar-paciente"
                    class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-md text-sm">
                    Guardar paciente
                </button>
            </div>
        </form>
    </div>
</div>
