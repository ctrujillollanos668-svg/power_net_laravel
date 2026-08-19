@extends('layouts.sidebaradmin')

@section('title', 'Panel Administrador - PowerNet')

@section('content')
<div class="space-y-6" x-data="{ searchQuery: '{{ request('buscar_usuario', '') }}' }">

    {{-- ==================== FLASH NOTIFICATIONS ==================== --}}
    @if(session('success'))
        <div class="px-4 py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs transition animate-fade-in">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-check"></i>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3.5 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs transition animate-fade-in">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    {{-- ==================== ENCABEZADO ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#0f172a] flex items-center gap-2.5 tracking-tight">
                <span class="text-2xl">📊</span>
                <span>Panel Administrador</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1 font-medium">Control global de métricas comerciales, inventario y gestión de usuarios</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('tienda.inicio') }}" target="_blank" class="px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 hover:text-black font-bold text-xs rounded-xl border border-gray-200/90 shadow-2xs transition flex items-center gap-2">
                <i class="fa-solid fa-store text-yellow-500"></i>
                <span>Ver Tienda</span>
            </a>
            <a href="{{ route('productos.index') }}" class="px-4 py-2.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                <i class="fa-solid fa-plus text-yellow-400"></i>
                <span>Nuevo Producto</span>
            </a>
        </div>
    </div>

    {{-- ==================== FILA 1: 4 KPIs PRINCIPALES ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4.5">
        
        {{-- 1. Productos --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <span class="text-xs font-bold text-gray-500 block">Productos</span>
            <div class="my-2">
                <span class="text-3xl font-black text-gray-900 tracking-tight">{{ $totalProductos }}</span>
            </div>
            <span class="text-[11px] text-gray-500 font-medium">
                {{ $productosActivos }} activos · {{ $productosInactivos }} inactivos
            </span>
        </div>

        {{-- 2. Valor Inventario --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <span class="text-xs font-bold text-gray-500 block">Valor inventario</span>
            <div class="my-2">
                <span class="text-3xl font-black text-gray-900 tracking-tight">${{ number_format($valorInventario, 0, ',', '.') }}</span>
            </div>
            <span class="text-[11px] text-amber-500 font-bold flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block animate-pulse"></span>
                <span>{{ $productosStockBajo }} con stock crítico</span>
            </span>
        </div>

        {{-- 3. Ventas Totales --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <span class="text-xs font-bold text-gray-500 block">Ventas totales</span>
            <div class="my-2">
                <span class="text-3xl font-black text-gray-900 tracking-tight">${{ number_format($totalVentas, 0, ',', '.') }}</span>
            </div>
            <span class="text-[11px] text-gray-500 font-medium">
                {{ $totalVentasCount }} ventas realizadas
            </span>
        </div>

        {{-- 4. Pedidos --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <span class="text-xs font-bold text-gray-500 block">Pedidos</span>
            <div class="my-2">
                <span class="text-3xl font-black text-gray-900 tracking-tight">{{ $totalPedidos }}</span>
            </div>
            <span class="text-[11px] text-gray-500 font-medium">
                {{ $pedidosPendientes }} pendientes
            </span>
        </div>

    </div>

    {{-- ==================== FILA 2: 3 TARJETAS DE ESTADO & STOCK ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4.5">
        
        {{-- Stock Bajo --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-600 block">Stock Bajo</span>
                <span class="text-3xl font-black text-amber-500 my-1 block">{{ $productosStockBajo }}</span>
                <span class="text-[11px] text-gray-400 font-medium">Productos críticos (≤5)</span>
            </div>
            <div class="w-12 h-12 rounded-full bg-amber-50 border border-amber-200/70 text-amber-500 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        {{-- Activos --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-600 block">Activos</span>
                <span class="text-3xl font-black text-emerald-500 my-1 block">{{ $productosActivos }}</span>
                <span class="text-[11px] text-gray-400 font-medium">Disponibles en tienda</span>
            </div>
            <div class="w-12 h-12 rounded-full bg-emerald-100/70 border border-emerald-200 text-emerald-600 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fa-solid fa-check"></i>
            </div>
        </div>

        {{-- Inactivos --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-600 block">Inactivos</span>
                <span class="text-3xl font-black text-gray-800 my-1 block">{{ $productosInactivos }}</span>
                <span class="text-[11px] text-gray-400 font-medium">No visibles</span>
            </div>
            <div class="w-12 h-12 rounded-full bg-gray-200/80 border border-gray-300 text-gray-500 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>

    </div>

    {{-- ==================== SECCIÓN: USUARIOS REGISTRADOS ==================== --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        
        {{-- Barra Superior: Título & Buscador --}}
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <h2 class="text-base font-black text-[#0f172a] flex items-center gap-2 tracking-tight">
                    <i class="fa-solid fa-users text-violet-600"></i>
                    <span>Usuarios Registrados</span>
                </h2>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-violet-50 text-violet-700 border border-violet-100">
                    {{ $totalUsuariosCount }} total
                </span>
            </div>

            {{-- Formulario de Búsqueda --}}
            <form action="{{ route('dashboard') }}" method="GET" class="w-full sm:w-80 relative">
                <div class="relative flex items-center">
                    <input 
                        type="text" 
                        name="buscar_usuario" 
                        value="{{ request('buscar_usuario') }}"
                        placeholder="Buscar usuario..." 
                        class="w-full pl-9 pr-8 py-2 rounded-xl border border-gray-200 bg-gray-50/50 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition shadow-2xs placeholder:text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 absolute left-3 text-xs pointer-events-none"></i>
                    
                    @if(request('buscar_usuario'))
                        <a href="{{ route('dashboard') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 p-0.5" title="Limpiar búsqueda">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabla de Usuarios --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50/80 text-[11px] uppercase tracking-wider text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 w-16">#</th>
                        <th class="px-6 py-3.5">Nombre</th>
                        <th class="px-6 py-3.5">Email</th>
                        <th class="px-6 py-3.5">Rol</th>
                        <th class="px-6 py-3.5 text-center w-28">Cambiar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($usuarios as $u)
                        <tr class="hover:bg-gray-50/60 transition group">
                            
                            {{-- ID --}}
                            <td class="px-6 py-4 font-bold text-gray-400 text-xs">
                                {{ $u->id }}
                            </td>

                            {{-- Nombre --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full {{ $u->role_id == 1 ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-700' }} font-black text-xs flex items-center justify-center shrink-0 uppercase border border-white shadow-2xs">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-gray-900 text-xs block">
                                        {{ $u->name }}
                                    </span>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 text-gray-600 text-xs font-mono">
                                {{ $u->email }}
                            </td>

                            {{-- Rol Badge --}}
                            <td class="px-6 py-4">
                                @if($u->role_id == 1)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-[#0f172a] text-white shadow-2xs">
                                        <i class="fa-solid fa-shield-halved text-yellow-400 text-[10px]"></i>
                                        <span>Admin</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-[#334155] text-white shadow-2xs">
                                        <span>Cliente</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Botón Cambiar Rol --}}
                            <td class="px-6 py-4 text-center">
                                @if(auth()->id() == $u->id)
                                    <span class="w-8 h-8 rounded-xl bg-gray-100 text-gray-300 inline-flex items-center justify-center cursor-not-allowed mx-auto" title="Tu usuario actual (en sesión)">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                    </span>
                                @else
                                    <form action="{{ route('admin.usuarios.cambiarRol', $u->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas cambiar el rol de {{ addslashes($u->name) }} a {{ $u->role_id == 1 ? 'Cliente' : 'Administrador' }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="w-8 h-8 rounded-xl border border-blue-300 hover:border-blue-600 bg-white hover:bg-blue-50 text-blue-500 hover:text-blue-700 inline-flex items-center justify-center transition shadow-2xs cursor-pointer group-hover:scale-105"
                                            title="Cambiar rol a {{ $u->role_id == 1 ? 'Cliente' : 'Administrador' }}">
                                            <i class="fa-solid fa-rotate text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-user-xmark text-3xl mb-2 text-gray-300"></i>
                                <p class="text-xs font-bold">No se encontraron usuarios registrados.</p>
                                @if(request('buscar_usuario'))
                                    <a href="{{ route('dashboard') }}" class="text-xs font-bold text-violet-600 hover:underline mt-2 inline-block">
                                        Limpiar filtros de búsqueda
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($usuarios->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $usuarios->links() }}
            </div>
        @endif

    </div>

</div>
@endsection