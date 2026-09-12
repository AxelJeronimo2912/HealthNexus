<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nuevo Colaborador</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-8 bg-white p-6 rounded-lg shadow">
            @csrf

            @include('admin.users._form')

            <hr>

            <section class="flex justify-between items-center">
                <p class="text-sm text-gray-500">Revisa y guarda los cambios cuando todo esté listo.</p>
                <div class="space-x-2">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        Guardar Colaborador
                    </button>
                </div>
            </section>
        </form>
    </div>

    @include('admin.users._scripts', ['u' => null])
</x-app-layout>
