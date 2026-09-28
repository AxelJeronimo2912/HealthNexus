<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Detalle del Evento</h2>
            <a href="{{ route('auditoria.index') }}" class="text-sm text-gray-600 hover:underline">← Volver</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-4">

            <div class="flex justify-between items-start border-b pb-3">
                <div>
                    <span class="px-2 py-1 rounded text-xs {{ $log->evento_color }}">
                        {{ $log->evento_label }}
                    </span>
                    <p class="text-lg font-bold mt-2">{{ $log->descripcion }}</p>
                </div>
                <div class="text-right text-xs text-gray-500">
                    <p>{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-3 text-sm">
                <dt class="font-semibold">Usuario:</dt>
                <dd>{{ $log->user_nombre }} ({{ $log->user_rol }})</dd>

                <dt class="font-semibold">Módulo:</dt>
                <dd>{{ $log->modulo_label }}</dd>

                <dt class="font-semibold">Evento:</dt>
                <dd>{{ $log->evento }}</dd>

                <dt class="font-semibold">Severidad:</dt>
                <dd>{{ ucfirst($log->severidad) }}</dd>

                <dt class="font-semibold">Sensible:</dt>
                <dd>{{ $log->es_sensible ? 'Sí' : 'No' }}</dd>
            </dl>

            @if ($log->metadata)
                <div class="border-t pt-4">
                    <p class="font-semibold text-sm mb-2">Metadatos</p>
                    <pre class="text-xs bg-gray-50 p-3 rounded overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif

            @if ($log->datos_antes && count($log->datos_antes))
                <div class="border-t pt-4">
                    <p class="font-semibold text-sm mb-2">Datos antes</p>
                    <pre class="text-xs bg-red-50 p-3 rounded overflow-x-auto">{{ json_encode($log->datos_antes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif

            @if ($log->datos_despues && count($log->datos_despues))
                <div class="border-t pt-4">
                    <p class="font-semibold text-sm mb-2">Datos después</p>
                    <pre class="text-xs bg-green-50 p-3 rounded overflow-x-auto">{{ json_encode($log->datos_despues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
