<x-guest-layout>
<x-auth-shell title="Confirma tu contraseña">
    <div class="mb-4 text-sm text-slate-600">
        Esta es un área protegida. Confirma tu contraseña para continuar.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="'Contraseña'" />

            <x-text-input id="password" class="block mt-1.5 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                Confirmar
            </x-primary-button>
        </div>
    </form>
</x-auth-shell>
</x-guest-layout>
