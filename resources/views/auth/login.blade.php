<x-guest-layout>
    <nav class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('Logo/ACT_Logo.png') }}" class="h-12 w-auto" alt="ACT Logo">
                <span class="text-lg font-semibold text-emerald-700">ACT</span>
            </a>

            <div class="flex gap-4 text-sm font-medium">
                @auth
                    <a href="{{ url('/dashboard') }}"
                    class="px-4 py-2 rounded-md border border-emerald-600 text-emerald-700 hover:bg-emerald-50 transition">
                        Dashboard
                    </a>
                @else
                    <a href="/"
                    class="px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition">
                    Home
                    </a>
                @endauth
            </div>
        </div>
    </nav>
    <div class="min-h-screen flex items-center justify-center bg-emerald-50 px-4">
            
        <div class="w-full max-w-5xl bg-white rounded-xl shadow-lg overflow-hidden flex">


            <!-- FORMULARIO -->
            <div class="w-full md:w-1/2 p-10">

                <h2 class="text-3xl font-bold text-emerald-700">
                    Bienvenido 
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Inicia sesión para continuar
                </p>

                <!-- espacio fijo para errores -->
                <div class="min-h-[72px] mt-4">
                    <x-validation-errors />
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-label for="email" value="Email o usuario" />
                        <x-input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            class="w-full border-slate-300 focus:border-emerald-400 focus:ring-emerald-400"
                        />
                    </div>

                    <div>
                        <div class="flex justify-between">
                            <x-label for="password" value="Contraseña" />
                            <a href="{{ route('password.request') }}"
                               class="text-sm text-sky-600 hover:underline">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                        <x-input
                            id="password"
                            type="password"
                            name="password"
                            required
                            class="w-full border-slate-300 focus:border-emerald-400 focus:ring-emerald-400"
                        />
                    </div>

                    <label class="flex items-center">
                        <x-checkbox class="text-emerald-500" />
                        <span class="ml-2 text-sm text-slate-600">Recordarme</span>
                    </label>

                    <x-button class="w-full bg-emerald-500 hover:bg-emerald-600 py-3">
                        Iniciar sesión
                    </x-button>
                </form>

                <p class="mt-6 text-sm text-center text-slate-600">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="text-sky-600 font-medium hover:underline">
                        Regístrate
                    </a>
                </p>
            </div>

            <!-- PANEL DERECHO -->
            <div class="hidden md:flex md:w-1/2 bg-sky-100 items-center justify-center">
                <img
                    src="{{ asset('Logo/ACT_Logo.png') }}"
                    class="h-64 lg:h-80 w-auto"
                    alt="Logo ACT">
            </div>

        </div>
    </div>
</x-guest-layout>
