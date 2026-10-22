<x-guest-layout>
    <x-auth-shell title="Recupera tu acceso"
        subtitle="Escribe tu correo y te enviaremos un enlace para restablecer tu contraseña.">

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <x-input name="email" type="email" label="Correo electrónico" icon="envelope"
                :value="old('email')" required autofocus placeholder="nombre@hospital.mx" />

            <x-button type="submit" size="lg" class="w-full" icon="paper-airplane">Enviar enlace de recuperación</x-button>

            <a href="{{ route('login') }}"
                class="flex items-center justify-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800">
                <x-heroicon-m-arrow-left class="h-4 w-4" /> Volver al inicio de sesión
            </a>
        </form>
    </x-auth-shell>
</x-guest-layout>
