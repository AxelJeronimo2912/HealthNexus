<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Editar Rol: {{ ucfirst($role->name) }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST"
            class="space-y-6 bg-white p-6 rounded-lg shadow">
            @csrf
            @method('PUT')

            {{-- Nombre del rol --}}
            <div>
                <label class="block text-sm font-medium">Nombre del Rol</label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                @error('name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Menús del navbar --}}
            @php
                $permisosActuales = old('permissions', $role->permissions->pluck('name')->toArray());
            @endphp

            <div>
                <label class="block text-sm font-medium mb-1">¿Qué menús verá este rol en el navbar?</label>
                <p class="text-xs text-gray-500 mb-4">
                    Marca los menús que este rol podrá ver. Solo aparecerán en su sidebar los que selecciones.
                </p>

                <div class="space-y-4">
                    @foreach ($menus as $seccion)
                        <div class="border rounded-md overflow-hidden">
                            <div class="bg-gray-50 px-3 py-2 flex items-center justify-between border-b">
                                <div class="flex items-center">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm seccion-toggle"
                                        data-seccion="{{ $loop->index }}">
                                    <span class="ml-2 font-semibold text-sm">{{ $seccion['titulo'] }}</span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ count($seccion['items']) }} menús
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 p-3">
                                @foreach ($seccion['items'] as $item)
                                    <label
                                        class="inline-flex items-center text-sm p-2 rounded hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $item['permiso'] }}"
                                            @checked(in_array($item['permiso'], $permisosActuales))
                                            class="rounded border-gray-300 text-blue-600 shadow-sm item-checkbox"
                                            data-seccion="{{ $loop->parent->index }}">
                                        @if (!empty($item['icono']))
                                            <x-dynamic-component :component="'heroicon-o-' . $item['icono']" class="w-4 h-4 ml-2 text-gray-500" />
                                        @endif
                                        <span class="ml-2">{{ $item['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('admin.roles.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Actualizar Rol
                </button>
            </div>
        </form>
    </div>

    @include('admin.roles._scripts')
</x-app-layout>
