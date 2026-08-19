<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel de Control') - PowerNet Admin</title>
    
    <!-- Google Fonts Inter -->
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
        
        /* Custom Slim Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased font-sans">
<div class="flex min-h-screen">

    {{-- ==================== SIDEBAR MODERNO Y ELEGANTE (100% FIJO) ==================== --}}
    <aside class="fixed inset-y-0 left-0 w-64 h-full bg-[#080d1a] border-r border-white/5 text-slate-300 flex flex-col z-30 shadow-2xl overflow-hidden">
        
        {{-- Brand & Logo Header --}}
        <div class="px-5 py-5 border-b border-white/5 flex items-center justify-between shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-slate-900 border border-white/10 p-1 flex items-center justify-center overflow-hidden shadow-lg shadow-amber-500/20 group-hover:scale-105 transition duration-300 shrink-0">
                    <img src="{{ asset('img/logo_powernet.jpg') }}" alt="PowerNet" class="w-full h-full object-cover rounded-xl">
                </div>
                <div>
                    <div class="text-base font-black tracking-tight leading-none text-white flex items-center gap-1">
                        <span>Power</span><span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-yellow-300">Net</span>
                    </div>
                    <span class="text-[9px] font-extrabold text-amber-400/90 uppercase tracking-widest block mt-1">Iluminación & Bombillos</span>
                </div>
            </a>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-500/10 text-amber-300 border border-amber-500/20">
                PRO
            </span>
        </div>

        {{-- Navegación con Scroll Slim --}}
        <nav class="flex-1 sidebar-scroll overflow-y-auto px-3.5 py-5 space-y-6">
            
            {{-- SECCIÓN: PRINCIPAL --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-extrabold tracking-widest text-slate-500 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                    <span>Principal</span>
                </p>
                <ul class="space-y-1">
                    {{-- Dashboard --}}
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-amber-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-gauge-high text-xs"></i>
                            </div>
                            <span class="tracking-wide">Dashboard</span>
                        </a>
                    </li>

                    {{-- Pedidos --}}
                    <li>
                        <a href="{{ route('admin.pedidos.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.pedidos.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.pedidos.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-blue-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-clipboard-list text-xs"></i>
                            </div>
                            <span class="tracking-wide">Pedidos</span>
                        </a>
                    </li>

                    {{-- Envíos --}}
                    <li>
                        <a href="{{ route('admin.envios.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.envios.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.envios.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-cyan-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-truck-fast text-xs"></i>
                            </div>
                            <span class="tracking-wide">Envíos y Despachos</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- SECCIÓN: CATÁLOGO --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-extrabold tracking-widest text-slate-500 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>
                    <span>Catálogo & Stock</span>
                </p>
                <ul class="space-y-1">
                    {{-- Productos --}}
                    <li>
                        <a href="{{ route('productos.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('productos.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('productos.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-violet-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-box text-xs"></i>
                            </div>
                            <span class="tracking-wide">Productos</span>
                        </a>
                    </li>

                    {{-- Categorías --}}
                    <li>
                        <a href="{{ route('categorias.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('categorias.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('categorias.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-fuchsia-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-tags text-xs"></i>
                            </div>
                            <span class="tracking-wide">Categorías</span>
                        </a>
                    </li>

                    {{-- Inventario --}}
                    <li>
                        <a href="{{ route('admin.inventario.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.inventario.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.inventario.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-sky-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-warehouse text-xs"></i>
                            </div>
                            <span class="tracking-wide">Inventario</span>
                        </a>
                    </li>

                    {{-- Ofertas --}}
                    <li>
                        <a href="{{ route('ofertas.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('ofertas.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('ofertas.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-yellow-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-percent text-xs"></i>
                            </div>
                            <span class="tracking-wide">Ofertas</span>
                        </a>
                    </li>

                    {{-- Proveedores --}}
                    <li>
                        <a href="{{ route('proveedores.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('proveedores.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('proveedores.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-indigo-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-industry text-xs"></i>
                            </div>
                            <span class="tracking-wide">Proveedores</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- SECCIÓN: FINANZAS --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-extrabold tracking-widest text-slate-500 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Finanzas & Recaudo</span>
                </p>
                <ul class="space-y-1">
                    {{-- Métodos de Pago --}}
                    <li>
                        <a href="{{ route('metodospago.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('metodospago.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('metodospago.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-emerald-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-credit-card text-xs"></i>
                            </div>
                            <span class="tracking-wide">Métodos de Pago</span>
                        </a>
                    </li>

                    {{-- Pagos --}}
                    <li>
                        <a href="{{ route('admin.pagos.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.pagos.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.pagos.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-teal-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-file-invoice-dollar text-xs"></i>
                            </div>
                            <span class="tracking-wide">Pagos</span>
                        </a>
                    </li>

                    {{-- Ventas --}}
                    <li>
                        <a href="{{ route('admin.ventas.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.ventas.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.ventas.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-amber-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-sack-dollar text-xs"></i>
                            </div>
                            <span class="tracking-wide">Ventas & Analítica</span>
                        </a>
                    </li>

                    {{-- Devoluciones --}}
                    <li>
                        <a href="{{ route('admin.devoluciones.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.devoluciones.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.devoluciones.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-rose-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                            </div>
                            <span class="tracking-wide">Devoluciones</span>
                        </a>
                    </li>
                </ul>
            </div>

        </nav>

    </aside>

    {{-- ==================== CONTENIDO PRINCIPAL ==================== --}}
    <div class="flex-1 flex flex-col min-h-screen min-w-0 ml-64">

        {{-- Topbar Elegante --}}
        <header class="h-16 bg-white border-b border-slate-200/80 px-6 sm:px-8 flex items-center justify-between shrink-0 sticky top-0 z-20 shadow-2xs backdrop-blur-md">
            
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-slate-400">PowerNet</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                <h2 class="text-sm font-black text-slate-900">@yield('title', 'Panel de Control')</h2>
            </div>

            <div class="flex items-center gap-4">
                
                {{-- Menú de Usuario en Topbar --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open"
                            class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                        </div>
                        <div class="hidden sm:block text-left leading-none">
                            <span class="text-xs font-bold text-slate-800 block">{{ Auth::user()->name ?? 'Administrador' }}</span>
                            <span class="text-[10px] text-emerald-600 font-semibold block mt-0.5">En línea</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-0.5 transition-transform" :class="open && 'rotate-180'"></i>
                    </button>

                    {{-- Dropdown Topbar --}}
                    <div x-show="open" x-cloak x-transition
                         class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-50">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-xs font-black text-slate-900">{{ Auth::user()->name ?? 'Administrador' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                            <i class="fa-solid fa-user-gear text-slate-400 w-4"></i>
                            <span>Mi Perfil</span>
                        </a>

                        <div class="border-t border-slate-100 my-1"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition text-left">
                                <i class="fa-solid fa-right-from-bracket text-xs w-4"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- Contenido de cada vista --}}
        <main class="flex-1 bg-[#f8fafc] p-6 sm:p-8">
            @yield('content')
        </main>
    </div>

</div>
</body>
</html>