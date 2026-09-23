<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Detalle del Dispositivo</h2>
            <a href="{{ route('dispositivos.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-4">

            {{-- Encabezado --}}
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600">
                        <x-dynamic-component :component="'heroicon-o-' . $dispositivo->tipo_icono" class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-lg font-bold">{{ $dispositivo->nombre ?? 'Dispositivo desconocido' }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $dispositivo->navegador }} — {{ $dispositivo->sistema_operativo }}
                        </p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded text-sm {{ $dispositivo->estado_color }}">
                    {{ $dispositivo->estado_label }}
                </span>
            </div>

            {{-- Info --}}
            <dl class="grid grid-cols-2 gap-3 text-sm pt-4 border-t">
                <dt class="font-semibold">Usuario:</dt>
                <dd>{{ $dispositivo->user?->nombre_completo ?? '—' }}</dd>

                <dt class="font-semibold">Tipo:</dt>
                <dd>{{ ucfirst($dispositivo->tipo ?? '—') }}</dd>

                <dt class="font-semibold">Navegador:</dt>
                <dd>{{ $dispositivo->navegador ?? '—' }}</dd>

                <dt class="font-semibold">Sistema operativo:</dt>
                <dd>{{ $dispositivo->sistema_operativo ?? '—' }}</dd>

                <dt class="font-semibold">IP de registro:</dt>
                <dd class="font-mono">{{ $dispositivo->ip_registro ?? '—' }}</dd>

                <dt class="font-semibold">Última IP:</dt>
                <dd class="font-mono">{{ $dispositivo->ip_ultimo_acceso ?? '—' }}</dd>

                <dt class="font-semibold">Último acceso:</dt>
                <dd>{{ $dispositivo->ultimo_acceso?->format('d/m/Y H:i') ?? '—' }}</dd>

                <dt class="font-semibold">Total de accesos:</dt>
                <dd>{{ $dispositivo->total_accesos }}</dd>

                @if ($dispositivo->aprobadoPor)
                    <dt class="font-semibold">Aprobado por:</dt>
                    <dd>{{ $dispositivo->aprobadoPor->nombre_completo }}
                        el {{ $dispositivo->aprobado_en?->format('d/m/Y H:i') }}
                    </dd>
                @endif
            </dl>

            {{-- User Agent --}}
            <div class="pt-4 border-t">
                <p class="text-xs text-gray-500 mb-1">User Agent</p>
                <p class="text-xs font-mono bg-gray-50 p-2 rounded border break-all">
                    {{ $dispositivo->user_agent ?? '—' }}
                </p>
            </div>

            {{-- Acciones --}}
            <div class="pt-4 border-t flex space-x-2">
                <a href="{{ route('dispositivos.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Volver</a>

                @if (auth()->user()->hasRole('administrador') && !$dispositivo->confiable && $dispositivo->activo)
                    <form action="{{ route('dispositivos.confiar', $dispositivo) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white rounded-md text-sm">
                            Marcar como confiable
                        </button>
                    </form>
                @endif

                @if ($dispositivo->activo)
                    <form action="{{ route('dispositivos.bloquear', $dispositivo) }}" method="POST"
                        onsubmit="return confirm('¿Bloquear este dispositivo?')">
                        @csrf
                        <button class="px-4 py-2 bg-red-600 text-white rounded-md text-sm">Bloquear</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
