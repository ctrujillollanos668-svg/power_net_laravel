<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mi Cuenta') | {{ config('app.name', 'PowerNet') }}</title>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Tailwind CSS & Alpine.js --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 antialiased text-gray-900">
    <div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

        {{-- ===== SIDEBAR CLIENTE ===== --}}
        <aside class="w-64 min-h-screen bg-[#0b1220] text-gray-300 flex flex-col shrink-0 border-r border-white/10 z-30">
            
            {{-- Encabezado con Logo (Igual a Welcome) --}}
            <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
                <a href="{{ route('tienda.inicio') }}" class="flex items-center gap-2.5 group">
                    <span class="w-9 h-9 bg-white rounded-xl flex items-center justify-center text-lg shadow-xs group-hover:rotate-6 transition-transform">💡</span>
                    <div class="leading-tight">
                        <div class="font-extrabold text-white text-base tracking-tight">
                            Power<span class="text-yellow-400">Net</span>
                        </div>
                        <div class="text-[10px] text-gray-400 font-medium tracking-wide">Portal Cliente</div>
                    </div>
                </a>
            </div>

            {{-- Navegación del Cliente --}}
            <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-6">
                
                {{-- Sección: Mi Cuenta --}}
                <div>
                    <p class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 uppercase">Mi Cuenta</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-yellow-400 text-gray-900 shadow-sm' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                                <i class="fa-solid fa-gauge w-4 text-center {{ request()->routeIs('dashboard') ? 'text-gray-900' : 'text-yellow-400' }}"></i>
                                <span>Panel Principal</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/mis-pedidos') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->is('mis-pedidos*') ? 'bg-yellow-400 text-gray-900 shadow-sm' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                                <i class="fa-solid fa-clipboard-list w-4 text-center {{ request()->is('mis-pedidos*') ? 'text-gray-900' : 'text-yellow-400' }}"></i>
                                <span>Mis Pedidos</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Sección: Compras y Catálogo --}}
                <div>
                    <p class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 uppercase">Tienda</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('tienda.inicio') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-300 hover:bg-white/10 hover:text-white transition">
                                <i class="fa-solid fa-store w-4 text-center text-yellow-400"></i>
                                <span>Ir a la Tienda</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tienda.catalogo') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-300 hover:bg-white/10 hover:text-white transition">
                                <i class="fa-solid fa-boxes-stacked w-4 text-center text-yellow-400"></i>
                                <span>Catálogo de Productos</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tienda.ofertas') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-300 hover:bg-white/10 hover:text-white transition">
                                <i class="fa-solid fa-fire w-4 text-center text-red-400"></i>
                                <span>Ofertas y Descuentos</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/carrito') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-300 hover:bg-white/10 hover:text-white transition">
                                <i class="fa-solid fa-cart-shopping w-4 text-center text-yellow-400"></i>
                                <span>Mi Carrito</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Sección: Configuración --}}
                <div>
                    <p class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 uppercase">Ajustes</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('profile.*') ? 'bg-yellow-400 text-gray-900 shadow-sm' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                                <i class="fa-solid fa-user-gear w-4 text-center {{ request()->routeIs('profile.*') ? 'text-gray-900' : 'text-yellow-400' }}"></i>
                                <span>Mi Perfil</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </nav>

            {{-- Pie del Sidebar --}}
            <div class="p-4 border-t border-white/10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white text-xs font-bold transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>

        </aside>

        {{-- ===== CONTENEDOR PRINCIPAL ===== --}}
        <div class="flex-1 flex flex-col min-h-screen">

            {{-- Topbar Superior --}}
            <header class="h-16 bg-white border-b border-gray-200 px-6 sm:px-8 flex items-center justify-between shrink-0 shadow-xs">
                
                {{-- Título de la página actual --}}
                <div class="flex items-center gap-3">
                    <h1 class="text-gray-800 text-sm sm:text-base font-extrabold flex items-center gap-2">
                        @yield('title', 'Mi Cuenta')
                    </h1>
                </div>

                {{-- Enlace a la tienda y Menú de Usuario --}}
                <div class="flex items-center gap-4">
                    
                    {{-- Botón rápido a la tienda pública --}}
                    <a href="{{ route('tienda.inicio') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold text-xs transition shadow-2xs">
                        <i class="fa-solid fa-bag-shopping text-xs"></i>
                        <span>Ver Tienda</span>
                    </a>

                    {{-- Dropdown Usuario --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                                class="flex items-center gap-3 px-2 py-1.5 rounded-xl hover:bg-gray-50 transition border border-gray-200/60">
                            
                            {{-- Avatar con iniciales --}}
                            <div class="w-8 h-8 rounded-lg bg-yellow-400 text-gray-900 flex items-center justify-center text-xs font-black shadow-2xs">
                                {{ strtoupper(substr(Auth::user()->name ?? 'CL', 0, 2)) }}
                            </div>

                            <div class="leading-tight text-left hidden sm:block">
                                <p class="text-xs font-bold text-gray-800">{{ Auth::user()->name ?? 'Cliente' }}</p>
                                <p class="text-[10px] text-yellow-600 font-semibold">Cliente PowerNet</p>
                            </div>

                            <i class="fa-solid fa-chevron-down text-[9px] text-gray-400 ml-1 transition-transform" :class="open && 'rotate-180'"></i>
                        </button>

                        {{-- Menú desplegable --}}
                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-2xl shadow-xl py-2 z-50">
                            
                            <div class="px-4 py-2 border-b border-gray-100 sm:hidden">
                                <p class="text-xs font-bold text-gray-800">{{ Auth::user()->name ?? 'Cliente' }}</p>
                                <p class="text-[10px] text-gray-400">{{ Auth::user()->email ?? '' }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition">
                                <i class="fa-solid fa-user-pen text-gray-400"></i>
                                <span>Editar Perfil</span>
                            </a>

                            <a href="{{ url('/mis-pedidos') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition">
                                <i class="fa-solid fa-receipt text-gray-400"></i>
                                <span>Historial de Pedidos</span>
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-50 transition text-left">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Cerrar Sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </header>

            {{-- Contenido inyectado de la vista --}}
            <main class="flex-1 bg-gray-50 p-6 sm:p-8">
                @yield('content')
            </main>

        </div>

    </div>
</body>

</html>
