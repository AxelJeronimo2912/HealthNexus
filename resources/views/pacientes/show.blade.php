<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle del Paciente</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-6">

            <div>
                <h3 class="text-lg font-bold">{{ $paciente->nombre_completo }}</h3>
                <p class="text-sm text-gray-500">
                    {{ $paciente->edad }} años — {{ ucfirst($paciente->sexo) }}
                </p>
            </div>

            {{-- Identidad --}}
            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Identidad</h4>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <dt class="font-semibold">Fecha de Nacimiento:</dt>
                    <dd>{{ $paciente->fecha_nacimiento?->format('d/m/Y') }}</dd>
                    <dt class="font-semibold">Estado Civil:</dt>
                    <dd>{{ $paciente->estado_civil ?? '—' }}</dd>
                    <dt class="font-semibold">Nacionalidad:</dt>
                    <dd>{{ $paciente->nacionalidad ?? '—' }}</dd>
                    <dt class="font-semibold">Estado de Nacimiento:</dt>
                    <dd>{{ $paciente->estado_nacimiento ?? '—' }}</dd>
                    @if ($paciente->curp)
                        <dt class="font-semibold">CURP:</dt>
                        <dd>{{ $paciente->curp }}</dd>
                    @endif
                    @if ($paciente->pasaporte)
                        <dt class="font-semibold">Pasaporte:</dt>
                        <dd>{{ $paciente->pasaporte }}</dd>
                    @endif
                </dl>
            </div>

            {{-- Contacto --}}
            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Contacto</h4>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <dt class="font-semibold">Teléfono:</dt>
                    <dd>{{ $paciente->telefono_principal ?? '—' }}</dd>
                    <dt class="font-semibold">Correo:</dt>
                    <dd>{{ $paciente->correo_electronico ?? '—' }}</dd>
                    <dt class="font-semibold">Ocupación:</dt>
                    <dd>{{ $paciente->ocupacion ?? '—' }}</dd>
                    <dt class="font-semibold">Responsable:</dt>
                    <dd>{{ $paciente->responsable_nombre ?? '—' }}</dd>
                </dl>
            </div>

            {{-- Salud --}}
            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Datos de salud</h4>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <dt class="font-semibold">Tipo Sanguíneo:</dt>
                    <dd>{{ $paciente->tipo_sanguineo ?? '—' }}</dd>
                    <dt class="font-semibold">Alergias:</dt>
                    <dd>{{ $paciente->alergias ?? '—' }}</dd>
                    <dt class="font-semibold">Enfermedades Crónicas:</dt>
                    <dd>{{ $paciente->enfermedades_cronicas ?? '—' }}</dd>
                </dl>
            </div>

            {{-- Domicilio --}}
            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Domicilio</h4>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <dt class="font-semibold">Estado:</dt>
                    <dd>{{ $paciente->estado->nombre ?? '—' }}</dd>
                    <dt class="font-semibold">Municipio:</dt>
                    <dd>{{ $paciente->municipio->nombre ?? '—' }}</dd>
                    <dt class="font-semibold">Colonia:</dt>
                    <dd>{{ $paciente->colonia ?? '—' }}</dd>
                    <dt class="font-semibold">Calle:</dt>
                    <dd>{{ $paciente->calle ?? '—' }}</dd>
                    <dt class="font-semibold">Núm. Exterior:</dt>
                    <dd>{{ $paciente->numero_exterior ?? '—' }}</dd>
                    <dt class="font-semibold">Núm. Interior:</dt>
                    <dd>{{ $paciente->numero_interior ?? '—' }}</dd>
                </dl>
            </div>

            {{-- Estado --}}
            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Estado</h4>
                @if ($paciente->activo)
                    <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded text-sm">
                        Activo
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded text-sm">
                        Inactivo
                    </span>
                @endif
            </div>

            <div class="pt-4 flex space-x-2">
                <a href="{{ route('pacientes.edit', $paciente) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Editar</a>
                <a href="{{ route('pacientes.index') }}" class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
