<div class="space-y-6">
    {{-- Lista de asignados --}}
    <div>
        <h4 class="font-bold text-slate-800 mb-3">Personal actual ({{ $servicio->users->count() }})</h4>
        @if ($servicio->users->isEmpty())
            <p class="text-sm text-slate-500 italic bg-slate-50 p-3 rounded-xl text-center">No hay personal asignado a
                este servicio.</p>
        @else
            <ul class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">
                @foreach ($servicio->users as $user)
                    <li class="flex items-center justify-between p-3 bg-white hover:bg-slate-50">
                        <div>
                            <p class="font-bold text-slate-800">{{ $user->nombre }}</p>
                            <p class="text-xs text-slate-500">{{ $user->email ?? '' }}</p>
                        </div>
                        <form action="{{ route('servicios.personal.quitar', [$servicio, $user->pivot->id]) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-xs font-bold text-rose-500 hover:text-rose-700 bg-rose-50 px-3 py-1.5 rounded-lg transition">Quitar</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Formulario para asignar --}}
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
        <h4 class="font-bold text-slate-800 mb-3">Asignar nuevo personal</h4>
        <form action="{{ route('servicios.personal.asignar', $servicio) }}" method="POST" class="flex flex-col gap-3">
            @csrf
            <select name="user_id" required
                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-nexus-primary outline-none">
                <option value="">Selecciona un usuario...</option>
                @foreach ($disponibles as $disponible)
                    <option value="{{ $disponible->id }}">{{ $disponible->nombre }}</option>
                @endforeach
            </select>

            <input type="text" name="rol_en_servicio" placeholder="Rol (opcional, ej: Enfermero)"
                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-nexus-primary outline-none">

            <button type="submit"
                class="px-4 py-2 bg-nexus-primary text-white text-sm font-bold rounded-lg hover:bg-slate-900 transition">
                Asignar Personal
            </button>
        </form>
    </div>
</div>
