<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PowerNet') }} - Autenticación</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans text-slate-800 antialiased bg-[#0b1220] min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden">

    {{-- Efectos de Fondo Luminoso --}}
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-yellow-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-violet-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 my-8">
        
        {{-- Logo Header --}}
        <div class="text-center mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-700 p-1 flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-105 transition overflow-hidden">
                    <img src="{{ asset('img/logo_powernet.jpg') }}" alt="PowerNet" class="w-full h-full object-cover rounded-xl">
                </div>
                <div class="text-left">
                    <span class="text-xl font-black text-white tracking-tight leading-none block">
                        Power<span class="text-yellow-400">Net</span>
                    </span>
                    <span class="text-[10px] font-bold text-amber-400/90 tracking-widest uppercase block mt-1">Iluminación & Bombillos</span>
                </div>
            </a>
        </div>

        {{-- Tarjeta Principal --}}
        <div class="w-full bg-white rounded-3xl p-7 sm:p-9 shadow-2xl border border-slate-100 relative">
            {{ $slot }}
        </div>

        {{-- Enlace Volver a la Tienda --}}
        <div class="text-center mt-6">
            <a href="{{ url('/') }}" class="text-xs font-bold text-slate-400 hover:text-white transition inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Volver a la tienda principal</span>
            </a>
        </div>

    </div>
</body>
</html>
