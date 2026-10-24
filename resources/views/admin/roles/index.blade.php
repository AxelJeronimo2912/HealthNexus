<x-app-layout>
    @php
        $mapaPermisos = collect(config('menus.secciones'))
            ->flatMap(fn($s) => $s['items'])
            ->mapWithKeys(fn($i) => [$i['permiso'] => $i['label']]);
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Roles y Permisos
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Administración de roles y permisos del sistema</p>
            </div>
            <a href="{{ route('admin.roles.create') }}"
                class="bg-brand-600 hover:bg-brand-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Rol
            </a>
        </div>
    </x-slot>

    @php
        $thCls = 'px-5 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">


        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Roles registrados</h3>
                <p class="text-xs text-slate-400">{{ $roles->total() }} roles en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Rol</th>
                            <th class="{{ $thCls }} text-left">Permisos</th>
                            <th class="{{ $thCls }} text-left">Usuarios</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($roles as $role)
                            <tr class="hover:bg-brand-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ ucfirst($role->name) }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($role->permissions->isEmpty())
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold uppercase tracking-wide">
                                            Sin permisos
                                        </span>
                                    @else
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($role->permissions as $perm)
                                                <span
                                                    class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">
                                                    {{ $mapaPermisos[$perm->name] ?? $perm->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full text-xs font-bold">
                                        {{ $role->users_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap justify-end gap-2 whitespace-nowrap">
                                        <a href="{{ route('admin.roles.show', $role) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all">
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role) }}"
                                            class="px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg text-xs font-bold transition-all">
                                            Editar
                                        </a>
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este rol?')">
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
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin roles registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $roles->links() }}</div>
    </div>
</x-app-layout>
