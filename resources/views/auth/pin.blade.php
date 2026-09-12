<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-gray-800">Verificación de PIN</h1>
        <p class="text-sm text-gray-500 mt-1">
            Ingresa tu PIN de 4 dígitos para completar el acceso.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.pin.verify') }}" class="space-y-4">
        @csrf

        <div>
            <label for="pin" class="block text-sm font-medium text-gray-700">PIN</label>
            <input id="pin" name="pin" type="password" inputmode="numeric" pattern="\d{4}" maxlength="4"
                autofocus autocomplete="off"
                class="mt-1 block w-full text-center text-2xl tracking-widest border-gray-300 rounded-md shadow-sm"
                placeholder="••••">
        </div>

        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium">
            Verificar PIN
        </button>

        <a href="{{ route('login') }}" class="block text-center text-sm text-gray-500 hover:underline">
            Volver al inicio de sesión
        </a>
    </form>
</x-guest-layout>
