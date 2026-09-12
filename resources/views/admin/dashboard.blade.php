<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel de Administración — HealthNexus
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-2">
                        Bienvenido, {{ auth()->user()->name }}
                    </h3>
                    <p>Rol activo: <strong>{{ auth()->user()->getRoleNames()->first() }}</strong></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                <a href="#" class="block bg-white p-6 rounded-lg shadow hover:shadow-lg">
                    <h4 class="font-bold text-blue-600">Configuración del Hospital</h4>
                    <p class="text-sm text-gray-600 mt-2">Datos generales, servicios y áreas.</p>
                </a>

                <a href="{{ route('admin.users.index') }}" class="block bg-white p-6 rounded-lg shadow hover:shadow-lg">
                    <h4 class="font-bold text-blue-600">Usuarios</h4>
                    <p class="text-sm text-gray-600 mt-2">Alta, edición y roles.</p>
                </a>

                <a href="#" class="block bg-white p-6 rounded-lg shadow hover:shadow-lg">
                    <h4 class="font-bold text-blue-600">Roles y Permisos</h4>
                    <p class="text-sm text-gray-600 mt-2">Control de acceso.</p>
                </a>
                <a href="#" class="block bg-white p-6 rounded-lg shadow hover:shadow-lg">
                    <h4 class="font-bold text-blue-600">Auditoría</h4>
                    <p class="text-sm text-gray-600 mt-2">Registro de actividad y dispositivos.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
