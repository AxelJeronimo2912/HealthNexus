<x-guest-layout>
    <!-- Fondo General Oscuro/Degradado (#172554) -->
    <div class="min-h-screen bg-nexus-primary flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-hidden">
        
        <!-- Destellos decorativos de fondo -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-nexus-secondary/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-nexus-accent/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- TARJETA CONTENEDORA PRINCIPAL -->
        <div class="w-full max-w-5xl bg-nexus-card rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[580px] z-10 border border-white/10">
            
            <!-- SECCION IZQUIERDA: Formulario de Login -->
            <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-between bg-white relative">
                
                <div class="absolute top-0 left-0 w-20 h-20 bg-gradient-to-br from-nexus-accent/20 to-transparent rounded-br-full pointer-events-none"></div>

                <!-- Branding / Espacio para Logo -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-12 w-12 rounded-2xl bg-nexus-primary flex items-center justify-center shadow-md text-nexus-accent border border-nexus-accent/20">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>

                    <div class="border-l border-slate-200 pl-4">
                        <h1 class="text-xl font-bold tracking-tight text-nexus-primary">NexusHealth</h1>
                        <p class="text-xs text-nexus-muted font-medium">Doctor Web App | Login</p>
                    </div>
                </div>

                <!-- Formulario -->
                <div class="my-auto">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Correo Electronico -->
                        <div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-full text-nexus-text placeholder-nexus-muted text-sm focus:outline-none focus:ring-2 focus:ring-nexus-secondary focus:bg-white transition-all shadow-sm"
                                placeholder="Correo electronico o Usuario" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
                        </div>

                        <!-- Contrasena -->
                        <div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-full text-nexus-text placeholder-nexus-muted text-sm focus:outline-none focus:ring-2 focus:ring-nexus-secondary focus:bg-white transition-all shadow-sm"
                                placeholder="Contrasena" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
                        </div>

                        <!-- Opciones auxiliares -->
                        <div class="flex items-center justify-between px-2 text-xs text-nexus-muted font-medium">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer hover:text-nexus-text transition-colors">
                                <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-nexus-secondary focus:ring-nexus-secondary h-3.5 w-3.5">
                                <span class="ms-1.5">Recordarme</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="hover:text-nexus-secondary transition-colors underline" href="{{ route('password.request') }}">
                                    ¿Olvidaste tu contrasena?
                                </a>
                            @endif
                        </div>

                        <!-- Boton Login -->
                        <div class="pt-2">
                            <button type="submit" class="w-full py-3.5 px-6 rounded-full shadow-lg shadow-nexus-secondary/30 text-sm font-semibold text-white bg-nexus-secondary hover:bg-nexus-secondary-hover focus:outline-none focus:ring-2 focus:ring-nexus-secondary transform active:scale-95 transition-all">
                                Iniciar Sesion
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-xs text-slate-400 mt-6">
                    © {{ date('Y') }} NexusHealth System. Todos los derechos reservados.
                </div>
            </div>

            <!-- SECCION DERECHA: Ilustracion con Estetoscopio y Pulso Cardiaco -->
            <div class="hidden md:flex md:w-1/2 bg-nexus-primary relative flex-col justify-between p-12 overflow-hidden text-white">
                
                <!-- Divisoria estilo onda -->
                <div class="absolute inset-y-0 -left-1 w-24 pointer-events-none text-white hidden md:block">
                    <svg class="h-full w-full fill-current" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0,0 C30,20 0,50 40,70 C70,85 20,100 0,100 Z"></path>
                    </svg>
                </div>

                <!-- Corazon Flotante -->
                <div class="absolute top-1/2 -left-6 -translate-y-1/2 z-20 w-16 h-16 bg-gradient-to-tr from-cyan-400 to-nexus-secondary rounded-full flex items-center justify-center shadow-xl border-4 border-white animate-bounce" style="animation-duration: 3s;">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                    </svg>
                </div>

                <!-- Icono de Estetoscopio + Pulso Cardiaco (Segun tu imagen) -->
                <div class="my-auto z-10 flex flex-col items-center text-center pl-6">
                    <div class="relative w-72 h-64 flex items-center justify-center">
                        <div class="absolute inset-0 bg-nexus-secondary/20 rounded-full blur-2xl"></div>
                        
                        <!-- SVG Estetoscopio y Pulso -->
                        <svg class="w-72 h-64 text-nexus-accent relative z-10 drop-shadow-2xl" viewBox="0 0 240 200" fill="none" stroke="currentColor">
                            <!-- Linea de pulso cardiaco (ECG) -->
                            <path stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" d="M 10 100 L 45 100 L 55 80 L 65 120 L 75 40 L 88 150 L 98 100 L 230 100" />
                            
                            <!-- Auriculares / Olivas superiores -->
                            <circle cx="130" cy="35" r="5" fill="currentColor" />
                            <circle cx="155" cy="35" r="5" fill="currentColor" />
                            
                            <!-- Arcada superior del estetoscopio -->
                            <path stroke-width="4" stroke-linecap="round" stroke-linejoin="round" d="M 130 40 C 120 70 120 110 142.5 125 C 165 110 165 70 155 40" />
                            
                            <!-- Tubo curvado y campana del estetoscopio -->
                            <path stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" d="M 142.5 125 C 120 145 105 130 110 110 C 115 90 175 90 180 130 C 185 175 125 180 110 155" />
                            
                            <!-- Campana del estetoscopio -->
                            <circle cx="108" cy="150" r="10" fill="currentColor" />
                        </svg>
                    </div>

                    <h2 class="text-2xl font-bold text-white tracking-wide mt-2">Atencion Medica Digital</h2>
                    <p class="text-xs text-nexus-accent max-w-xs mt-2 font-light leading-relaxed">
                        Accede a expedientes, citas clinicas e historial del paciente de forma instantanea.
                    </p>
                </div>

                <div class="z-10 text-right text-xs text-nexus-accent/80 font-medium">
                    Plataforma Segura • HIPAA Compliant
                </div>
            </div>
    </div>
</x-guest-layout>