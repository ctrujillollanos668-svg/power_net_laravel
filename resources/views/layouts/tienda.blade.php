<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inicio') | {{ config('app.name', 'PowerNet') }} - Electricidad y Materiales</title>
    
    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Tailwind CSS & Alpine.js --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-100 text-gray-900 antialiased min-h-screen flex flex-col" 
      x-data="{ modalLogin: false, modalRegistro: false, menuCategorias: false }"
      @abrir-login.window="modalLogin = true">

    {{-- ===== SMART STICKY HEADER CONTAINER (AUTO-HIDE ON SCROLL DOWN / REVEAL ON SCROLL UP) ===== --}}
    <div x-data="{
            navVisible: true,
            lastScroll: 0,
            init() {
                window.addEventListener('scroll', () => {
                    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                    if (currentScroll <= 20) {
                        this.navVisible = true;
                    } else if (currentScroll > this.lastScroll && currentScroll > 60) {
                        // Al bajar la pantalla -> Se esconde
                        this.navVisible = false;
                    } else if (currentScroll < this.lastScroll) {
                        // Al subir un tantico -> Vuelve a aparecer
                        this.navVisible = true;
                    }
                    this.lastScroll = currentScroll <= 0 ? 0 : currentScroll;
                }, { passive: true });
            }
         }"
         class="sticky top-0 z-40 transition-transform duration-300 ease-out will-change-transform"
         :class="navVisible ? 'translate-y-0' : '-translate-y-full'">

        {{-- ===== TOP BAR ===== --}}
        <header class="bg-[#0b1220] text-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">

                {{-- Logo --}}
                <a href="{{ route('tienda.inicio') }}" class="flex items-center gap-2.5 shrink-0 group">
                    <div class="w-10 h-10 bg-slate-900 rounded-xl p-0.5 border border-white/10 flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform overflow-hidden">
                        <img src="{{ asset('img/logo_powernet.jpg') }}" alt="PowerNet" class="w-full h-full object-cover rounded-lg">
                    </div>
                    <div class="leading-tight">
                        <div class="font-extrabold text-lg tracking-tight text-white">
                            Power<span class="text-yellow-400">Net</span>
                        </div>
                        <div class="text-[10px] text-gray-300 font-medium tracking-wide">Electricidad</div>
                    </div>
                </a>

                {{-- Buscador Principal (Cápsula Blanca con Botón Amarillo) --}}
                <form action="{{ route('tienda.inicio') }}" method="GET" class="flex-1 max-w-2xl mx-2 sm:mx-4">
                    <div class="relative w-full flex items-center bg-white rounded-full p-1 shadow-sm">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Buscar productos..."
                            class="w-full bg-transparent px-4 sm:px-5 py-2 text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none">
                        <button
                            type="submit"
                            class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-black text-xs uppercase px-5 sm:px-7 py-2.5 rounded-full transition shrink-0 shadow-xs">
                            BUSCAR
                        </button>
                    </div>
                </form>

                {{-- Acciones Derecha (Carrito Púrpura) --}}
                <div class="flex items-center gap-3 shrink-0">
                    {{-- Botón Carrito Púrpura con Contador Dinámico --}}
                    @php
                        $initialCartCount = array_sum(array_column(session('cart', []), 'cantidad'));
                    @endphp
                    <a href="{{ url('/carrito') }}"
                        x-data="{ cartCount: {{ $initialCartCount }} }"
                        @carrito-actualizado.window="cartCount = $event.detail.count"
                        class="w-11 h-11 rounded-full bg-[#7c3aed] hover:bg-[#6d28d9] text-white flex items-center justify-center transition shrink-0 relative shadow-[0_0_15px_rgba(124,58,237,0.5)] group"
                        title="Carrito de compras">
                        <i class="fa-solid fa-cart-shopping text-sm group-hover:scale-110 transition-transform"></i>
                        <span 
                            x-show="cartCount > 0"
                            x-cloak
                            class="absolute -top-1 -right-1 bg-yellow-400 text-gray-950 font-black text-[10px] min-w-[20px] h-5 px-1 rounded-full flex items-center justify-center border-2 border-[#0b1220] shadow-sm animate-bounce"
                            x-text="cartCount > 99 ? '99+' : cartCount">
                            {{ $initialCartCount > 0 ? ($initialCartCount > 99 ? '99+' : $initialCartCount) : '' }}
                        </span>
                    </a>
                </div>

            </div>
        </header>

        {{-- ===== SEGUNDA BARRA DE NAVEGACIÓN (SUBHEADER) ===== --}}
        <nav class="bg-[#f3f4f6] border-b border-gray-200/80 shadow-2xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2.5 flex items-center justify-center gap-4 text-xs sm:text-sm font-semibold relative">
                
                {{-- Enlaces Centrales (centrados en la barra) --}}
                <div class="flex items-center gap-6 sm:gap-10">
                    <a href="{{ route('tienda.inicio') }}" 
                       class="transition whitespace-nowrap {{ request()->routeIs('tienda.inicio') ? 'text-gray-900 font-extrabold' : 'text-gray-700 hover:text-gray-900 font-medium' }}">
                        Inicio
                    </a>

                    {{-- Menú Desplegable de Categorías --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button
                            type="button"
                            @click.stop="open = !open"
                            class="flex items-center gap-1.5 transition whitespace-nowrap text-gray-700 hover:text-gray-900 font-medium focus:outline-none cursor-pointer select-none">
                            <span>Categorias</span>
                            <i class="fa-solid fa-caret-down text-xs text-gray-500 transition-transform duration-200" :class="open && 'rotate-180'"></i>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute left-0 top-full mt-2 w-60 bg-white rounded-2xl shadow-2xl border border-gray-200/80 py-2 z-50">
                            
                            {{-- Opción Todas las categorías --}}
                            <a href="{{ route('tienda.inicio') }}#productos-seccion" 
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs sm:text-sm font-bold text-gray-900 hover:bg-yellow-50 hover:text-yellow-800 transition border-b border-gray-100">
                                <span>📦</span>
                                <span>Todas las categorías</span>
                            </a>

                            @php
                                $navCategorias = \App\Models\Categoria::where('estado', 1)->get();
                            @endphp

                            <div class="py-1 max-h-80 overflow-y-auto">
                                @forelse($navCategorias as $cat)
                                    <a href="{{ route('tienda.inicio', ['categoria' => $cat->id]) }}#productos-seccion" 
                                       class="block px-4 py-2 text-xs sm:text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-800 transition {{ request('categoria') == $cat->id ? 'font-black text-yellow-700 bg-yellow-50/70' : '' }}">
                                        {{ $cat->nombre_categoria }}
                                    </a>
                                @empty
                                    <span class="block px-4 py-2 text-xs text-gray-400">Sin categorías</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('tienda.ofertas') }}" 
                       class="transition whitespace-nowrap {{ request()->routeIs('tienda.ofertas') ? 'text-gray-900 font-extrabold' : 'text-gray-700 hover:text-gray-900 font-medium' }}">
                        Ofertas
                    </a>

                    <a href="{{ Auth::check() ? url('/mis-pedidos') : '#' }}" 
                       @guest @click.prevent="modalLogin = true" @endguest
                       class="text-gray-700 hover:text-gray-900 transition whitespace-nowrap font-medium">
                        Mis Pedidos
                    </a>
                </div>

                {{-- Menú de Usuario a la Derecha --}}
                <div class="flex items-center gap-2 shrink-0 absolute right-4 sm:right-6">
                    @auth
                        {{-- Dropdown para Cliente Logueado --}}
                        <div class="relative shrink-0" x-data="{ openCuenta: false }" @click.outside="openCuenta = false">
                            <button
                                type="button"
                                @click="openCuenta = !openCuenta"
                                class="flex items-center gap-2 px-4 py-1.5 bg-[#e5e7eb] hover:bg-[#d1d5db] rounded-full text-gray-800 text-xs font-bold transition border border-gray-300 shadow-2xs">
                                <i class="fa-solid fa-user text-xs text-gray-700"></i>
                                <span>Mi cuenta</span>
                                <i class="fa-solid fa-caret-down text-[11px] text-gray-600 transition-transform duration-200" :class="openCuenta && 'rotate-180'"></i>
                            </button>

                            <div
                                x-show="openCuenta"
                                x-cloak
                                x-transition
                                class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                                
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                    <span>Mi perfil</span>
                                </a>

                                <a href="{{ url('/carrito') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                    <span>🛒 Mi carrito</span>
                                </a>

                                <a href="{{ route('pedidos.index') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                    <span>📦 Mis pedidos</span>
                                </a>

                                <a href="{{ route('cliente.devoluciones.index') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                    <span>↪️ Mis devoluciones</span>
                                </a>

                                <a href="{{ route('favoritos.index') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                    <span>❤️ Mis favoritos</span>
                                </a>

                                <a href="{{ route('cliente.metodospago.index') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                    <span>💳 Métodos de pago</span>
                                </a>

                                <div class="border-t border-gray-100 my-1"></div>

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-50 transition text-left">
                                        Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>

            </div>
        </nav>

    </div>

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-[#0b1220] text-gray-400 border-t border-gray-800 mt-16">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                {{-- Columna 1: Info Marca --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-lg">💡</span>
                        <div class="font-extrabold text-white text-lg">
                            Power<span class="text-yellow-400">Net</span>
                        </div>
                    </div>
                    <p class="text-xs leading-relaxed text-gray-400">
                        Tu tienda de confianza para materiales eléctricos, bombillos, herramientas e iluminación con la máxima calidad y respaldo.
                    </p>
                </div>

                {{-- Columna 2: Enlaces Rápidos --}}
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Navegación</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('tienda.inicio') }}" class="hover:text-yellow-400 transition">Inicio</a></li>
                        <li><a href="{{ route('tienda.catalogo') }}" class="hover:text-yellow-400 transition">Catálogo completo</a></li>
                        <li><a href="{{ route('tienda.ofertas') }}" class="hover:text-yellow-400 transition text-red-400 font-semibold">Ofertas especiales</a></li>
                    </ul>
                </div>

                {{-- Columna 3: Categorías Populares --}}
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Categorías</h4>
                    <ul class="space-y-2 text-xs">
                        @foreach(\App\Models\Categoria::where('estado', 1)->take(4)->get() as $c)
                            <li>
                                <a href="{{ route('tienda.categoria', $c->id) }}" class="hover:text-yellow-400 transition">
                                    {{ $c->nombre_categoria }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Columna 4: Contacto --}}
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Contacto & Soporte</h4>
                    <ul class="space-y-2 text-xs">
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-yellow-400"></i>
                            <span>Atención telefónica directa</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-yellow-400"></i>
                            <span>contacto@powernet.com</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-yellow-400"></i>
                            <span>Compras 100% seguras</span>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-gray-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
                <p>&copy; {{ date('Y') }} PowerNet Electricidad. Todos los derechos reservados.</p>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-bolt text-yellow-400"></i> Energía que transforma</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- ===== MODAL DE REGISTRO MODERNO ===== --}}
    <div
        x-show="modalRegistro"
        x-cloak
        x-data="{ verPassReg: false, verPassConf: false }"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
        @click.self="modalRegistro = false"
        @keydown.escape.window="modalRegistro = false">
        
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" @click="modalRegistro = false"></div>

        <div class="relative w-full max-w-md rounded-3xl bg-white p-7 sm:p-8 shadow-2xl z-10 my-8 border border-slate-100" @click.stop>
            
            {{-- Botón Cerrar --}}
            <button
                type="button"
                @click="modalRegistro = false"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            {{-- Encabezado con Logo --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 p-1 flex items-center justify-center overflow-hidden shadow-md shadow-amber-500/10 shrink-0">
                    <img src="{{ asset('img/logo_powernet.jpg') }}" alt="PowerNet" class="w-full h-full object-cover rounded-xl">
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-1.5">
                        <span>Crear Cuenta</span>
                        <span class="text-amber-500">✨</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">Únete a PowerNet y compra con beneficios exclusivos</p>
                </div>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Nombre Completo --}}
                <div>
                    <label for="name" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nombre Completo
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user text-xs"></i>
                        </div>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ej. Juan Pérez"
                            required
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                    </div>
                    @error('name')
                        <p class="text-red-500 text-[11px] font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Correo Electrónico --}}
                <div>
                    <label for="reg_email" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </div>
                        <input
                            type="email"
                            id="reg_email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="ejemplo@correo.com"
                            required
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-[11px] font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="reg_password" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                        Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </div>
                        <input
                            :type="verPassReg ? 'text' : 'password'"
                            id="reg_password"
                            name="password"
                            placeholder="Mínimo 8 caracteres"
                            required
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                        <button 
                            type="button" 
                            @click="verPassReg = !verPassReg" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                            <i class="fa-solid" :class="verPassReg ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-[11px] font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmar Contraseña --}}
                <div>
                    <label for="password_confirmation" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                        Confirmar Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                        </div>
                        <input
                            :type="verPassConf ? 'text' : 'password'"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repita la contraseña"
                            required
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                        <button 
                            type="button" 
                            @click="verPassConf = !verPassConf" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                            <i class="fa-solid" :class="verPassConf ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                {{-- Botón Registrarse --}}
                <button
                    type="submit"
                    class="w-full mt-2 rounded-2xl bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 hover:from-yellow-500 hover:to-amber-500 text-slate-950 font-black text-xs py-3.5 shadow-lg shadow-yellow-400/25 hover:shadow-yellow-400/40 hover:scale-[1.01] active:scale-[0.99] transition flex items-center justify-center gap-2 cursor-pointer">
                    <span>Crear Mi Cuenta</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                {{-- Footer de Registro --}}
                <div class="pt-3 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500">
                        ¿Ya tienes una cuenta? 
                        <button type="button" @click="modalRegistro = false; modalLogin = true" class="text-[#7c3aed] font-extrabold hover:underline cursor-pointer">
                            Inicia sesión aquí
                        </button>
                    </p>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL DE LOGIN MODERNO ===== --}}
    <div
        x-show="modalLogin"
        x-cloak
        x-data="{ verPassLog: false }"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
        @click.self="modalLogin = false"
        @keydown.escape.window="modalLogin = false">
        
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" @click="modalLogin = false"></div>

        <div class="relative w-full max-w-md rounded-3xl bg-white p-7 sm:p-8 shadow-2xl z-10 my-8 border border-slate-100" @click.stop>
            
            {{-- Botón Cerrar --}}
            <button
                type="button"
                @click="modalLogin = false"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            {{-- Encabezado con Logo --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 p-1 flex items-center justify-center overflow-hidden shadow-md shadow-amber-500/10 shrink-0">
                    <img src="{{ asset('img/logo_powernet.jpg') }}" alt="PowerNet" class="w-full h-full object-cover rounded-xl">
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-1.5">
                        <span>Iniciar Sesión</span>
                        <span class="text-amber-500">🔑</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">Ingresa a tu cuenta para gestionar pedidos y pagos</p>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Correo Electrónico --}}
                <div>
                    <label for="login_email" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </div>
                        <input
                            type="email"
                            id="login_email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="ejemplo@correo.com"
                            required
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-[11px] font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="login_password" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                            Contraseña
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-[#7c3aed] hover:underline">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </div>
                        <input
                            :type="verPassLog ? 'text' : 'password'"
                            id="login_password"
                            name="password"
                            placeholder="Ingrese su contraseña"
                            required
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition">
                        <button 
                            type="button" 
                            @click="verPassLog = !verPassLog" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                            <i class="fa-solid" :class="verPassLog ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-[11px] font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Recordarme --}}
                <div class="flex items-center justify-between text-xs py-0.5">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded-md border-slate-300 text-yellow-400 focus:ring-yellow-400 cursor-pointer">
                        <span class="text-slate-600 font-semibold text-xs">Mantener sesión iniciada</span>
                    </label>
                </div>

                {{-- Botón Entrar --}}
                <button
                    type="submit"
                    class="w-full mt-2 rounded-2xl bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 hover:from-yellow-500 hover:to-amber-500 text-slate-950 font-black text-xs py-3.5 shadow-lg shadow-yellow-400/25 hover:shadow-yellow-400/40 hover:scale-[1.01] active:scale-[0.99] transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    <span>Acceder a Mi Cuenta</span>
                </button>

                {{-- Footer de Login --}}
                <div class="pt-3 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500">
                        ¿Aún no tienes cuenta? 
                        <button type="button" @click="modalLogin = false; modalRegistro = true" class="text-[#7c3aed] font-extrabold hover:underline cursor-pointer">
                            Regístrate gratis
                        </button>
                    </p>
                </div>

                {{-- Sello de Seguridad --}}
                <div class="pt-2 flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                    <i class="fa-solid fa-lock text-emerald-500"></i>
                    <span>Conexión 100% segura y encriptada</span>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== NOTIFICACIÓN TOAST FLOTANTE ===== --}}
    <div 
        x-data="{ visible: false, mensaje: '', link: '{{ url('/carrito') }}' }"
        @toast-carrito.window="mensaje = $event.detail.msg; visible = true; setTimeout(() => visible = false, 4000)"
        x-show="visible"
        x-cloak
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4 scale-95"
        class="fixed bottom-5 right-5 z-50 max-w-sm w-full bg-[#0f172a] text-white p-4 rounded-2xl shadow-2xl border border-gray-700 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <span class="w-9 h-9 rounded-xl bg-[#7c3aed] text-white flex items-center justify-center shrink-0 text-sm shadow-sm">
                🛒
            </span>
            <div class="min-w-0">
                <p class="text-xs font-black text-white truncate" x-text="mensaje"></p>
                <a :href="link" class="text-[11px] font-bold text-yellow-400 hover:text-yellow-300 underline mt-0.5 inline-block">
                    Ver carrito de compras &rarr;
                </a>
            </div>
        </div>
        <button type="button" @click="visible = false" class="text-gray-400 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>

    {{-- Script Global para Manejo de Favoritos y Carrito --}}
    <script>
        // Manejo Global de Favoritos
        window.toggleFavoritoGlobal = function(productoId, callback) {
            @guest
                window.dispatchEvent(new CustomEvent('abrir-login'));
                return;
            @endguest

            fetch('{{ url('/favoritos/toggle') }}/' + productoId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => {
                if(res.status === 401) {
                    window.dispatchEvent(new CustomEvent('abrir-login'));
                    return null;
                }
                return res.json();
            })
            .then(data => {
                if(data && callback) {
                    callback(data);
                }
            })
            .catch(err => console.error('Error al actualizar favorito:', err));
        };

        // Estado de autenticación del usuario
        window.isUserLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

        // Abrir modal de Login
        window.abrirModalLogin = function() {
            window.dispatchEvent(new CustomEvent('abrir-login'));
        };

        // Función Global de Compra Rápida (Botón Comprar)
        window.comprarProductoGlobal = function(productoId, cantidad = 1) {
            if (!window.isUserLoggedIn) {
                // Si NO está logueado: NO agrega nada al carrito, abre directamente el login
                window.abrirModalLogin();
                return;
            }

            // Si SÍ está logueado: flujo normal (agrega al carrito y va al carrito)
            window.agregarAlCarritoGlobal(productoId, cantidad, () => {
                window.location.href = '{{ url('/carrito') }}';
            });
        };

        // Manejo Global de Carrito de Compras
        window.agregarAlCarritoGlobal = function(productoId, cantidad = 1, callback = null) {
            fetch('{{ url('/carrito/agregar') }}/' + productoId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cantidad: cantidad })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Actualizar badge en header
                    window.dispatchEvent(new CustomEvent('carrito-actualizado', { detail: { count: data.cart_count } }));
                    
                    // Mostrar notificación Toast
                    window.dispatchEvent(new CustomEvent('toast-carrito', { detail: { msg: data.message } }));

                    if(callback) {
                        callback(data);
                    }
                } else {
                    alert(data.message || 'No se pudo agregar el producto.');
                }
            })
            .catch(err => console.error('Error al agregar al carrito:', err));
        };
    </script>

</body>
</html>