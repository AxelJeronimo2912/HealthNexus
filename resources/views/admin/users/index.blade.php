<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Gestión de Colaboradores
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Usuarios, roles, turnos y especialidades del personal</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="bg-brand-600 hover:bg-brand-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Colaborador
            </a>
            
        </div>
    </x-slot>

    @php
        $thCls = 'px-5 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">


        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Colaboradores registrados</h3>
                <p class="text-xs text-slate-400">{{ $users->total() }} colaboradores en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Foto</th>
                            <th class="{{ $thCls }} text-left">Nombre</th>
                            <th class="{{ $thCls }} text-left">CURP</th>
                            <th class="{{ $thCls }} text-left">Correo</th>
                            <th class="{{ $thCls }} text-left">Rol</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            @php
                                $esMedico = $user->roles->contains(function ($rol) {
                                    $n = strtolower($rol->name);
                                    return str_contains($n, 'medic') ||
                                        str_contains($n, 'doctor') ||
                                        str_contains($n, 'médic');
                                });
                            @endphp
                            <tr class="hover:bg-brand-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    @if ($user->foto_perfil)
                                        <img src="{{ asset('storage/' . $user->foto_perfil) }}"
                                            class="w-10 h-10 rounded-2xl object-cover border border-slate-200">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-700 border border-brand-100 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($user->nombre ?? $user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">
                                        {{ $user->nombre_completo ?: $user->name }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($user->curp)
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono font-bold">
                                            {{ $user->curp }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">{{ $user->email }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full text-xs font-bold uppercase tracking-wide">
                                        {{ $user->getRoleNames()->first() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($user->activo)
                                        <span
                                            class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-xs font-bold uppercase tracking-wide">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-xs font-bold uppercase tracking-wide">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap justify-end gap-2 whitespace-nowrap">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all">
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg text-xs font-bold transition-all">
                                            Editar
                                        </a>
                                        <a href="{{ route('admin.users.turnos.index', $user) }}"
                                            class="px-3 py-1.5 bg-accent-50 hover:bg-accent-100 text-accent-700 rounded-lg text-xs font-bold transition-all">
                                            Turnos
                                        </a>

                                        @if ($esMedico)
                                            <a href="{{ route('admin.users.especialidades', $user) }}"
                                                class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-bold transition-all">
                                                Especialidades
                                            </a>
                                        @endif

                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este colaborador?')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-all">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin colaboradores registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
