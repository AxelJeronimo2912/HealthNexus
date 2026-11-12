<x-guest-layout>
    <x-auth-shell title="Recupera tu acceso"
        subtitle="Escribe tu correo y te enviaremos un enlace para restablecer tu contraseña.">

        {{-- Mensaje de éxito traducido --}}
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-200">
                @if (session('status') == 'We have emailed your password reset link.')
                    ¡Te hemos enviado un enlace de recuperación a tu correo!
                @else
                    {{ session('status') }}
                @endif
            </div>
        @endif

        {{-- Mensajes de error (por si el correo no existe) --}}
        @if ($errors->any())
            <div class="mb-4 font-medium text-sm text-red-600 bg-red-50 p-3 rounded-lg border border-red-200">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <x-input name="email" type="email" label="Correo electrónico" icon="envelope" :value="old('email')" required
                autofocus placeholder="nombre@hospital.mx" />

            <x-button type="submit" size="lg" class="w-full" icon="paper-airplane">Enviar enlace de
                recuperación</x-button>

            <a href="{{ route('login') }}"
                class="flex items-center justify-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800">
                <x-heroicon-m-arrow-left class="h-4 w-4" /> Volver al inicio de sesión
            </a>
        </form>
    </x-auth-shell>
</x-guest-layout>
