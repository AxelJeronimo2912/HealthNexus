
<x-slot name="header">
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div>
            <nav class="text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                <a href="{{ route('servicios.index') }}" class="hover:underline">Servicios</a>
                <span class="mx-1">/</span>
                <a href="{{ route('servicios.show', $servicio) }}" class="hover:underline">
                    {{ $servicio->nombre }}
                </a>
                <span class="mx-1">/</span>
                <span class="text-gray-700 font-medium">Personal</span>
            </nav>
            <h2 class="font-semibold text-xl text-gray-800 flex items-center gap-2">
                Personal del servicio
                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                    {{ $servicio->users->count() }} asignados
                </span>
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $servicio->nombre }}</p>
        </div>
        <a href="{{ route('servicios.show', $servicio) }}"
            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Volver
        </a>
    </div>
</x-slot>

<div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

    @if (session('success'))
        <div role="alert"
            class="flex items-start gap-2 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div role="alert" class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Asignar nuevo personal</h3>
                <p class="text-xs text-gray-500">Selecciona un usuario y define su rol dentro del servicio.</p>
            </div>
        </div>

        <div class="p-6">
            @if ($disponibles->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700">No hay usuarios disponibles</p>
                    <p class="text-xs text-gray-500 mt-1">Todos los usuarios activos ya están asignados a este servicio.</p>
                </div>
            @else
                <form action="{{ route('servicios.personal.asignar', $servicio) }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    @csrf

                    <div class="md:col-span-5">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Usuario <span class="text-rose-500">*</span>
                        </label>
                        <select name="user_id" id="user_id" required
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 @error('user_id') border-rose-500 @enderror">
                            <option value="">— Selecciona un usuario —</option>
                            @foreach ($disponibles as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>
                                    {{ $u->nombre_completo ?: $u->name }}
                                    · {{ $u->getRoleNames()->first() ?? 'sin rol' }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-4">
                        <label for="rol_en_servicio" class="block text-sm font-medium text-gray-700 mb-1">
                            Rol en servicio
                        </label>
                        <input type="text" name="rol_en_servicio" id="rol_en_servicio"
                            value="{{ old('rol_en_servicio') }}" placeholder="Ej. jefe, médico, enfermero"
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 @error('rol_en_servicio') border-rose-500 @enderror">
                        @error('rol_en_servicio')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Opcional. Se mostrará en la tabla.</p>
                    </div>

                    <div class="md:col-span-3 flex items-end">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-md text-sm font-medium shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Asignar
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">
                Personal asignado
                <span class="ml-1 text-sm font-normal text-gray-500">({{ $servicio->users->count() }})</span>
            </h3>
        </div>

        @if ($servicio->users->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center px-6">
                <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a3 3 0 10-3-3" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Sin personal asignado</p>
                <p class="text-xs text-gray-500 mt-1">Usa el formulario superior para asignar el primer usuario.</p>
            </div>
        @else
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Usuario
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Rol del sistema
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Rol en servicio
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Desde
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($servicio->users as $u)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($u->nombre_completo ?: $u->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-800 truncate">
                                                {{ $u->nombre_completo ?: $u->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 truncate">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    @php
                                        $role = $u->getRoleNames()->first();
                                        $roleColors = match ($role) {
                                            'admin', 'super-admin' => 'bg-purple-100 text-purple-800',
                                            'jefe', 'jefe-servicio' => 'bg-amber-100 text-amber-800',
                                            'medico', 'médico' => 'bg-emerald-100 text-emerald-800',
                                            'enfermero', 'enfermera' => 'bg-sky-100 text-sky-800',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColors }}">
                                        {{ $role ?? 'sin rol' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-gray-700">
                                    {{ $u->pivot->rol_en_servicio ?: '—' }}
                                </td>
                                <td class="px-3 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $u->pivot->fecha_inicio ? \Carbon\Carbon::parse($u->pivot->fecha_inicio)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <form
                                        action="{{ route('servicios.personal.quitar', [$servicio, $u->pivot->id]) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('¿Quitar a {{ $u->nombre_completo ?: $u->name }} del servicio {{ $servicio->nombre }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            aria-label="Quitar a {{ $u->nombre_completo ?: $u->name }}"
                                            class="w-9 h-9 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg inline-flex items-center justify-center transition-colors"
                                            title="Quitar del servicio">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <ul class="md:hidden divide-y divide-gray-100">
                @foreach ($servicio->users as $u)
                    <li class="p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($u->nombre_completo ?: $u->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate">
                                    {{ $u->nombre_completo ?: $u->name }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">{{ $u->email }}</p>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">
                                        {{ $u->getRoleNames()->first() ?? 'sin rol' }}
                                    </span>
                                    @if ($u->pivot->rol_en_servicio)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs">
                                            {{ $u->pivot->rol_en_servicio }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    Desde
                                    {{ $u->pivot->fecha_inicio ? \Carbon\Carbon::parse($u->pivot->fecha_inicio)->format('d/m/Y') : '—' }}
                                </p>
                            </div>
                            <form action="{{ route('servicios.personal.quitar', [$servicio, $u->pivot->id]) }}"
                                method="POST"
                                onsubmit="return confirm('¿Quitar a {{ $u->nombre_completo ?: $u->name }} del servicio?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="Quitar a {{ $u->nombre_completo ?: $u->name }}"
                                    class="w-9 h-9 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg inline-flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 01-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
