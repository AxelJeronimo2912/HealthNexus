<div id="modal-paciente" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" role="dialog"
    aria-modal="true" aria-labelledby="modal-paciente-titulo" aria-describedby="modal-paciente-descripcion">

    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto" id="modal-paciente-panel">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 id="modal-paciente-titulo" class="text-lg font-semibold text-gray-800">
                    Registro rápido de paciente
                </h3>
                <p id="modal-paciente-descripcion" class="text-xs text-gray-500 mt-0.5">
                    Solo datos esenciales para urgencias. El expediente se completa después.
                    Los campos con <span class="text-red-600" aria-hidden="true">*</span> son obligatorios.
                </p>
            </div>
            <button type="button" id="btn-cerrar-paciente"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none px-1"
                aria-label="Cerrar modal">&times;</button>
        </div>

        <form id="form-paciente-rapido" class="space-y-3" novalidate autocomplete="off"
            aria-describedby="error-paciente">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="rp_nombre" class="block text-xs font-medium">
                        Nombre <span class="text-red-600" aria-hidden="true">*</span>
                    </label>
                    <input type="text" id="rp_nombre" name="nombre" required autocomplete="given-name"
                        maxlength="100" aria-required="true"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label for="rp_apellido_paterno" class="block text-xs font-medium">
                        Apellido paterno <span class="text-red-600" aria-hidden="true">*</span>
                    </label>
                    <input type="text" id="rp_apellido_paterno" name="apellido_paterno" required
                        autocomplete="family-name" maxlength="100" aria-required="true"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label for="rp_apellido_materno" class="block text-xs font-medium">
                        Apellido materno
                    </label>
                    <input type="text" id="rp_apellido_materno" name="apellido_materno"
                        autocomplete="additional-name" maxlength="100"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label for="rp_fecha_nacimiento" class="block text-xs font-medium">
                        Fecha nacimiento <span class="text-red-600" aria-hidden="true">*</span>
                    </label>
                    <input type="date" id="rp_fecha_nacimiento" name="fecha_nacimiento" required min="1900-01-01"
                        max="{{ now()->subDay()->format('Y-m-d') }}" autocomplete="bday" aria-required="true"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label for="rp_sexo" class="block text-xs font-medium">
                        Sexo <span class="text-red-600" aria-hidden="true">*</span>
                    </label>
                    <select id="rp_sexo" name="sexo" required aria-required="true"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">—</option>
                        <option value="hombre">Hombre</option>
                        <option value="mujer">Mujer</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <div>
                    <label for="rp_telefono_principal" class="block text-xs font-medium">
                        Teléfono
                    </label>
                    <input type="tel" id="rp_telefono_principal" name="telefono_principal" autocomplete="tel"
                        inputmode="tel" maxlength="20" pattern="[0-9+\-\s()]{7,20}" placeholder="10 dígitos"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label for="rp_tipo_sanguineo" class="block text-xs font-medium">
                        Tipo sanguíneo
                    </label>
                    <select id="rp_tipo_sanguineo" name="tipo_sanguineo"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">—</option>
                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rp_alergias" class="block text-xs font-medium">
                        Alergias
                    </label>
                    <input type="text" id="rp_alergias" name="alergias" placeholder="Se desconoce"
                        autocomplete="off" maxlength="200"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label for="rp_enfermedades_cronicas" class="block text-xs font-medium">
                    Enfermedades crónicas
                </label>
                <input type="text" id="rp_enfermedades_cronicas" name="enfermedades_cronicas"
                    placeholder="Se desconoce" autocomplete="off" maxlength="200"
                    class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            {{-- Errores de validación --}}
            <div id="error-paciente" class="hidden text-red-600 text-xs space-y-1" role="alert"
                aria-live="polite"></div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" id="btn-cancelar-paciente"
                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                    Cancelar
                </button>
                <button type="submit" id="btn-guardar-paciente"
                    class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-md text-sm inline-flex items-center gap-2">
                    <svg id="spinner-paciente" class="hidden animate-spin h-4 w-4 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                    <span id="texto-btn-guardar">Guardar paciente</span>
                </button>
            </div>
        </form>
    </div>
</div>
