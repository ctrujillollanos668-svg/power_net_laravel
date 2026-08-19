@extends('layouts.sidebarcliente')

@section('title', 'Mi Panel')

@section('content')

<div class="space-y-8">

    {{-- ===== BANNER DE BIENVENIDA ===== --}}
    <div class="bg-gradient-to-r from-[#0b1220] via-slate-900 to-[#0b1220] rounded-3xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden border border-white/10">
        <div class="relative z-10 max-w-xl">
            <span class="inline-flex items-center gap-1.5 bg-yellow-400/20 text-yellow-300 text-xs font-bold px-3 py-1 rounded-full mb-3">
                <i class="fa-solid fa-sparkles"></i>
                Portal de Compras PowerNet
            </span>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                ¡Hola, {{ Auth::user()->name ?? 'Cliente' }}!
            </h2>
            <p class="text-gray-300 text-xs sm:text-sm leading-relaxed">
                Desde aquí puedes gestionar tus pedidos, revisar tus envíos y acceder directamente al catálogo con ofertas exclusivas.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('tienda.catalogo') }}" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-extrabold text-xs px-5 py-2.5 rounded-xl transition shadow-xs inline-flex items-center gap-2">
                    <i class="fa-solid fa-bag-shopping"></i>
                    Explorar Catálogo
                </a>
                <a href="{{ route('tienda.ofertas') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition border border-white/20 inline-flex items-center gap-2">
                    <i class="fa-solid fa-fire text-red-400"></i>
                    Ver Ofertas
                </a>
            </div>
        </div>

        <div class="absolute right-6 -bottom-6 text-9xl text-white/5 font-black select-none pointer-events-none">
            💡
        </div>
    </div>

    {{-- ===== TARJETAS DE RESUMEN ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- Pedidos Totales --}}
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Mis Pedidos</p>
                <p class="text-2xl font-black text-gray-800 mt-1">0</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
        </div>

        {{-- Envíos en Camino --}}
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Envíos Activos</p>
                <p class="text-2xl font-black text-amber-600 mt-1">0</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
        </div>

        {{-- Carrito --}}
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Carrito</p>
                <p class="text-2xl font-black text-violet-600 mt-1">0</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
        </div>

        {{-- Ofertas Disponibles --}}
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ofertas Activas</p>
                <p class="text-2xl font-black text-red-600 mt-1">
                    {{ \App\Models\Oferta::where('estado', 'activa')->count() }}
                </p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-fire"></i>
            </div>
        </div>

    </div>

    {{-- ===== ACCESOS RÁPIDOS Y ESTADO ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Pedidos Recientes --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-xs border border-gray-200/80 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                <h3 class="font-extrabold text-sm text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-yellow-500"></i>
                    Últimos Pedidos
                </h3>
                <a href="{{ url('/mis-pedidos') }}" class="text-xs font-bold text-yellow-600 hover:underline">
                    Ver todos →
                </a>
            </div>

            <div class="py-12 text-center text-gray-400">
                <i class="fa-solid fa-box-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm font-semibold text-gray-700">Aún no tienes pedidos registrados</p>
                <p class="text-xs text-gray-400 mt-1">Cuando realices compras en la tienda, aquí podrás rastrear el estado de cada pedido.</p>
                <a href="{{ route('tienda.catalogo') }}" class="inline-block mt-4 px-5 py-2.5 bg-yellow-400 text-gray-900 font-extrabold text-xs rounded-xl hover:bg-yellow-500 transition shadow-2xs">
                    Comenzar a comprar
                </a>
            </div>
        </div>

        {{-- Accesos Directos --}}
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-6 space-y-4">
            <h3 class="font-extrabold text-sm text-gray-900 pb-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-compass text-yellow-500"></i>
                Accesos Directos
            </h3>

            <div class="space-y-2.5">
                <a href="{{ route('tienda.catalogo') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-yellow-50 hover:text-yellow-800 transition text-xs font-bold text-gray-700 group">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-boxes-stacked text-yellow-500"></i>
                        Ver Catálogo Completo
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 group-hover:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('tienda.ofertas') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-red-50 hover:text-red-800 transition text-xs font-bold text-gray-700 group">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-fire text-red-500"></i>
                        Sección de Descuentos
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 group-hover:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition text-xs font-bold text-gray-700 group">
                    <span class="flex items-center gap-2.5">
                        <i class="fa-solid fa-user-gear text-gray-500"></i>
                        Actualizar Mi Perfil
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

    </div>

</div>

@endsection
