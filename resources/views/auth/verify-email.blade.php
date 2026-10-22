<x-guest-layout>
<x-auth-shell title="Verifica tu correo">
    <div class="mb-4 text-sm text-slate-600">
        Te enviamos un enlace de verificación a tu correo. Si no lo recibiste, podemos enviarte otro.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Enviamos un nuevo enlace de verificación a tu correo.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Reenviar correo de verificación
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-slate-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-auth-shell>
</x-guest-layout>
