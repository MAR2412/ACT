<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ACT - Educación para todos</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#FDFDFC] text-slate-800">

{{-- NAVBAR --}}
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
                <a href="{{ route('login') }}"
                   class="px-4 py-2 text-slate-600 hover:text-emerald-600 transition">
                    Login
                </a>

            @endauth
        </div>
    </div>
</nav>


<section class="max-w-7xl mx-auto px-6 py-24 grid lg:grid-cols-2 gap-16 items-center">

    <div>
        <h1 class="text-4xl lg:text-5xl font-semibold text-slate-900 leading-tight mb-6">
            Educación para todos, <br>
            <span class="text-emerald-600">a tu ritmo y en tu horario</span>
        </h1>

        <p class="text-lg text-slate-600 mb-8">
            ACT ofrece módulos de inglés y tutorias de Matemáticas, Español y Física <strong>virtuales y presenciales</strong>,
            diseñados para adaptarse a tu tiempo, nivel y presupuesto.
        </p>

        <div class="flex flex-wrap gap-4">
            

            <a href="{{ route('login') }}"
               class="px-6 py-3 rounded-lg border border-emerald-600 text-emerald-700 font-medium hover:bg-emerald-50 transition">
                Ya tengo cuenta
            </a>
        </div>
    </div>

    {{-- Imagen / Logo --}}
    <div class="flex justify-center">
        <img src="{{ asset('Logo/ACT_Logo.png') }}"
             class="h-64 lg:h-80 w-auto"
             alt="ACT Logo grande">
    </div>
</section>

{{-- BENEFICIOS --}}
<section class="bg-emerald-50 py-20">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-semibold text-center mb-12 text-slate-900">
            ¿Por qué elegir ACT?
        </h2>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold mb-3 text-emerald-600">
                    📚 Módulos y Tutorias flexibles
                </h3>
                <p class="text-slate-600">
                    Avanza por módulos y tutorias a tu nivel y objetivos personales.
                </p>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold mb-3 text-emerald-600">
                    🕒 Horarios a tu medida
                </h3>
                <p class="text-slate-600">
                    Estudia cuando puedas, sin presiones ni horarios rígidos.
                </p>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold mb-3 text-emerald-600">
                    💻 Virtual, Hibrido y presencial
                </h3>
                <p class="text-slate-600">
                    Elige la modalidad que mejor se adapte a tu estilo de vida.
                </p>
            </div>
        </div>
    </div>
</section>


<section class="py-24 text-center">
    <h2 class="text-3xl font-semibold mb-6 text-slate-900">
        Aprende con ACT
    </h2>

    <p class="text-slate-600 mb-8 max-w-xl mx-auto">
        Precios flexibles, metodología clara y acompañamiento constante.
    </p>

    
</section>

<footer class="border-t py-6 text-center text-sm text-slate-500">
    © {{ date('Y') }} ACT · Educación para todos
</footer>

</body>
</html>
