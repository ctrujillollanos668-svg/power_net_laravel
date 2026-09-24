@extends('layouts.tienda')

@section('titulo', 'Mis Pedidos - PowerNet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8" x-data="misPedidosManager()">

    {{-- Breadcrumb Moderno --}}
    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-amber-600 transition flex items-center gap-1.5">
            <i class="fa-solid fa-house text-slate-400 text-[11px]"></i>
            <span>Inicio</span>
        </a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
        <span class="text-slate-900 font-bold bg-slate-100 px-2.5 py-0.5 rounded-md">Mis Pedidos</span>
    </nav>

    {{-- Mensajes Flash --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50/90 border border-emerald-200/80 text-emerald-900 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-xs backdrop-blur-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <i class="fa-solid fa-check text-sm"></i>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="w-7 h-7 rounded-lg hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50/90 border border-rose-200/80 text-rose-900 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-xs backdrop-blur-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="w-7 h-7 rounded-lg hover:bg-rose-100 text-rose-600 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    {{-- Encabezado Principal & Acciones --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-400 text-slate-950 flex items-center justify-center text-lg shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Mis Pedidos</span>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-100/70 border border-amber-300/50 px-3 py-0.5 rounded-full">
                            Historial
                        </span>
                    </h1>
                </div>
            </div>
            <p class="text-xs text-slate-500 max-w-xl">
                Rastrea el progreso en tiempo real de tus envíos, descarga comprobantes de venta térmica POS y solicita garantías directamente.
            </p>
        </div>

        <a href="{{ route('tienda.inicio') }}#productos-seccion" 
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow transition transform hover:-translate-y-0.5 self-start md:self-auto">
            <i class="fa-solid fa-cart-plus text-amber-400"></i>
            <span>Seguir Comprando</span>
        </a>
    </div>

    @if($pedidos->isEmpty())
        {{-- Estado Sin Pedidos --}}
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs max-w-xl mx-auto my-12">
            <div class="w-20 h-20 bg-gradient-to-br from-amber-50 to-amber-100 rounded-3xl flex items-center justify-center text-3xl mx-auto mb-4 border border-amber-200/60 shadow-inner">
                📦
            </div>
            <h2 class="text-lg font-black text-slate-900 mb-1">Aún no tienes pedidos registrados</h2>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                Cuando realices compras en nuestra tienda, podrás ver el seguimiento en tiempo real, tus facturas y garantías aquí.
            </p>
            <a href="{{ route('tienda.inicio') }}#productos-seccion" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs rounded-xl transition shadow-md shadow-amber-400/20">
                <i class="fa-solid fa-bolt"></i>
                <span>Explorar Catálogo</span>
            </a>
        </div>
    @else
        @php
            $totalPedidosCount = $pedidos->total() ?? $pedidos->count();
            $pedidosEnProceso = $pedidos->filter(function($p) {
                $st = strtolower($p->estado_pedido ?? '');
                return !str_contains($st, 'entreg') && !str_contains($st, 'cancel');
            })->count();
            $pedidosEntregados = $pedidos->filter(function($p) {
                return str_contains(strtolower($p->estado_pedido ?? ''), 'entreg');
            })->count();
            $montoTotalGastado = $pedidos->sum('total_pedido');
        @endphp

        {{-- Tarjetas KPI de Resumen --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0 border border-blue-100">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Compras</span>
                    <span class="text-lg font-black text-slate-900">{{ $totalPedidosCount }} {{ $totalPedidosCount == 1 ? 'pedido' : 'pedidos' }}</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base shrink-0 border border-amber-100">
                    <i class="fa-solid fa-truck-fast animate-pulse"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">En Proceso</span>
                    <span class="text-lg font-black text-amber-600">{{ $pedidosEnProceso }} activos</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base shrink-0 border border-emerald-100">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Entregados</span>
                    <span class="text-lg font-black text-emerald-600">{{ $pedidosEntregados }} completados</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base shrink-0 border border-slate-200">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Invertido</span>
                    <span class="text-base sm:text-lg font-black text-slate-900">${{ number_format($montoTotalGastado, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Barra de Filtro Rápido y Búsqueda --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-2xs mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            {{-- Tabs de Filtro --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                <button 
                    type="button" 
                    @click="filtro = 'todos'"
                    :class="filtro === 'todos' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                    Todos ({{ $pedidos->count() }})
                </button>
                <button 
                    type="button" 
                    @click="filtro = 'proceso'"
                    :class="filtro === 'proceso' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                    En Preparación / Camino
                </button>
                <button 
                    type="button" 
                    @click="filtro = 'entregado'"
                    :class="filtro === 'entregado' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                    Entregados
                </button>
                <button 
                    type="button" 
                    @click="filtro = 'cancelado'"
                    :class="filtro === 'cancelado' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                    Cancelados
                </button>
            </div>

            {{-- Buscador en Vivo --}}
            <div class="relative w-full sm:w-64 shrink-0">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    x-model="busqueda" 
                    placeholder="Buscar por # pedido o producto..." 
                    class="w-full pl-9 pr-3.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:bg-white transition">
            </div>
        </div>

        {{-- Lista de Tarjetas de Pedidos Rediseñadas --}}
        <div class="space-y-5">
            @foreach($pedidos as $pedido)
                @php
                    $ultimaDevolucion = $pedido->devoluciones->last();
                    $estadoLower = strtolower($pedido->estado_pedido ?? 'en preparación');
                    $isCancelado = str_contains($estadoLower, 'cancel');
                    $isEntregado = str_contains($estadoLower, 'entreg');
                    $isEnviado = str_contains($estadoLower, 'enviado') || str_contains($estadoLower, 'camino');
                    $isPreparacion = !$isCancelado && !$isEntregado && !$isEnviado;

                    // Determinación del paso activo (1 a 4)
                    $pasoActivo = 1;
                    if ($isPreparacion) $pasoActivo = 2;
                    if ($isEnviado) $pasoActivo = 3;
                    if ($isEntregado) $pasoActivo = 4;

                    $pedidoData = [
                        'id' => $pedido->id,
                        'id_padded' => str_pad($pedido->id, 5, '0', STR_PAD_LEFT),
                        'factura' => $pedido->pago->factura ?? 'FAC-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT),
                        'fecha' => $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                        'total' => $pedido->total_pedido,
                        'total_formateado' => number_format($pedido->total_pedido, 0, ',', '.'),
                        'estado_pedido' => $pedido->estado_pedido ?? 'Enviado',
                        'metodo_pago' => $pedido->pago->metodo_pago ?? 'transferencia',
                        'estado_pago' => $pedido->pago->estado_pago ?? 'Pagado',
                        'empresa_envios' => $pedido->envio->empresa_envios ?? 'Servientrega Express',
                        'costo_envio' => $pedido->envio->costo ?? 0,
                        'direccion_envio' => $pedido->envio->direccion_envio ?? 'Dirección registrada',
                        'cliente_nombre' => $pedido->cliente->persona->nombre_persona ?? Auth::user()->name ?? 'Cliente',
                        'cliente_doc' => $pedido->cliente->persona->documento ?? '222222222222',
                        'cliente_tel' => $pedido->cliente->persona->telefono ?? Auth::user()->telefono ?? 'No registra',
                        'devolucion' => $ultimaDevolucion ? [
                            'id' => $ultimaDevolucion->id,
                            'estado' => $ultimaDevolucion->estado,
                            'motivo' => $ultimaDevolucion->motivo,
                            'motivo_rechazo' => $ultimaDevolucion->motivo_rechazo,
                            'monto' => number_format($ultimaDevolucion->monto_devolucion, 0, ',', '.'),
                            'fecha' => $ultimaDevolucion->fecha_devolucion ? $ultimaDevolucion->fecha_devolucion->format('d/m/Y H:i') : '',
                        ] : null,
                        'items' => $pedido->detalles->map(function($d) {
                            return [
                                'nombre' => $d->producto->nombre ?? 'Producto Eléctrico',
                                'cantidad' => $d->cantidad,
                                'precio_unitario' => number_format($d->precio_unitario, 0, ',', '.'),
                                'subtotal' => number_format($d->subtotal, 0, ',', '.'),
                            ];
                        })
                    ];

                    $categoriaFiltro = 'proceso';
                    if ($isEntregado) $categoriaFiltro = 'entregado';
                    if ($isCancelado) $categoriaFiltro = 'cancelado';

                    $textoBusqueda = strtolower($pedido->id . ' ' . $pedido->detalles->pluck('producto.nombre')->join(' ') . ' ' . ($pedido->pago->metodo_pago ?? ''));
                @endphp

                {{-- Tarjeta de Pedido Premium --}}
                <div 
                    x-show="cumpleFiltro('{{ $categoriaFiltro }}', '{{ addslashes($textoBusqueda) }}')" 
                    class="bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
                    
                    {{-- 1. Cabecera de la Tarjeta --}}
                    <div class="bg-gradient-to-r from-slate-50 via-slate-50/70 to-slate-100/50 px-5 sm:px-6 py-4 border-b border-slate-200/70 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-xl bg-slate-900 text-amber-400 font-black text-xs flex items-center justify-center shadow-2xs">
                                    #{{ $pedido->id }}
                                </span>
                                <div>
                                    <span class="text-[10px] uppercase font-black text-slate-400 tracking-wider block">Pedido</span>
                                    <span class="font-extrabold text-slate-900 text-xs sm:text-sm">PowerNet-{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>

                            <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                            <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                <i class="fa-regular fa-calendar text-slate-400 text-[11px]"></i>
                                <span class="font-medium text-[11px] sm:text-xs">
                                    {{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y') : now()->format('d/m/Y') }} 
                                    <span class="text-slate-400">a las</span> 
                                    {{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('H:i') : now()->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        {{-- Badges de Estado y Pago --}}
                        <div class="flex items-center gap-2">
                            {{-- Badge Pago --}}
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-tight {{ $pedido->pago && $pedido->pago->estado_pago === 'Pendiente al entregar' ? 'bg-amber-100/80 text-amber-900 border border-amber-200' : 'bg-emerald-100/80 text-emerald-900 border border-emerald-200' }}">
                                <i class="fa-solid fa-credit-card text-[9px]"></i>
                                <span>{{ $pedido->pago && $pedido->pago->estado_pago === 'Pendiente al entregar' ? 'Pago Pendiente' : 'Pagado' }}</span>
                            </span>

                            {{-- Badge Estado Pedido --}}
                            @if($isCancelado)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-200 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Cancelado
                                </span>
                            @elseif($isEnviado)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-200 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    En Camino
                                </span>
                            @elseif($isEntregado)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Entregado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-200 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                    En Preparación
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- 2. Stepper / Barra de Progreso de Envío (Si no está cancelado) --}}
                    @if(!$isCancelado)
                        <div class="px-5 sm:px-6 pt-5 pb-2">
                            <div class="relative">
                                {{-- Barra de fondo --}}
                                <div class="absolute top-1/2 left-4 right-4 -translate-y-1/2 h-1 bg-slate-100 rounded-full z-0"></div>
                                {{-- Barra de progreso activa --}}
                                <div class="absolute top-1/2 left-4 -translate-y-1/2 h-1 bg-gradient-to-r from-amber-400 to-emerald-500 rounded-full z-0 transition-all duration-500"
                                     style="width: {{ $pasoActivo == 1 ? '10%' : ($pasoActivo == 2 ? '40%' : ($pasoActivo == 3 ? '75%' : '95%')) }};"></div>

                                {{-- Puntos del Stepper --}}
                                <div class="relative z-10 grid grid-cols-4 text-center">
                                    {{-- Paso 1 --}}
                                    <div class="flex flex-col items-center">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-black {{ $pasoActivo >= 1 ? 'bg-amber-400 text-slate-950 ring-4 ring-amber-100 shadow-xs' : 'bg-slate-200 text-slate-500' }}">
                                            <i class="fa-solid fa-receipt"></i>
                                        </div>
                                        <span class="text-[10px] font-bold mt-1.5 {{ $pasoActivo >= 1 ? 'text-slate-900' : 'text-slate-400' }}">Confirmado</span>
                                    </div>

                                    {{-- Paso 2 --}}
                                    <div class="flex flex-col items-center">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-black {{ $pasoActivo >= 2 ? 'bg-amber-400 text-slate-950 ring-4 ring-amber-100 shadow-xs' : 'bg-slate-200 text-slate-500' }}">
                                            <i class="fa-solid fa-box-open"></i>
                                        </div>
                                        <span class="text-[10px] font-bold mt-1.5 {{ $pasoActivo >= 2 ? 'text-slate-900' : 'text-slate-400' }}">Preparando</span>
                                    </div>

                                    {{-- Paso 3 --}}
                                    <div class="flex flex-col items-center">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-black {{ $pasoActivo >= 3 ? 'bg-blue-500 text-white ring-4 ring-blue-100 shadow-xs' : 'bg-slate-200 text-slate-500' }}">
                                            <i class="fa-solid fa-truck-fast"></i>
                                        </div>
                                        <span class="text-[10px] font-bold mt-1.5 {{ $pasoActivo >= 3 ? 'text-slate-900' : 'text-slate-400' }}">En Camino</span>
                                    </div>

                                    {{-- Paso 4 --}}
                                    <div class="flex flex-col items-center">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-black {{ $pasoActivo >= 4 ? 'bg-emerald-500 text-white ring-4 ring-emerald-100 shadow-xs' : 'bg-slate-200 text-slate-500' }}">
                                            <i class="fa-solid fa-house-chimney-check"></i>
                                        </div>
                                        <span class="text-[10px] font-bold mt-1.5 {{ $pasoActivo >= 4 ? 'text-emerald-700' : 'text-slate-400' }}">Entregado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 3. Cuerpo: Lista Visual de Productos Comprados --}}
                    <div class="p-5 sm:p-6">
                        <div class="divide-y divide-slate-100">
                            @foreach($pedido->detalles as $detalle)
                                @php
                                    $img = $detalle->producto && $detalle->producto->imagenes && $detalle->producto->imagenes->first() ? $detalle->producto->imagenes->first()->imagen : null;
                                @endphp
                                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        {{-- Miniatura de Producto --}}
                                        <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-200/80 p-1.5 flex items-center justify-center overflow-hidden shrink-0 shadow-2xs group-hover:border-amber-300 transition">
                                            @if($img && file_exists(public_path('imagenes_productos/' . $img)))
                                                <img src="{{ asset('imagenes_productos/' . $img) }}" alt="{{ $detalle->producto->nombre }}" class="max-h-full max-w-full object-contain">
                                            @else
                                                <span class="text-xl">💡</span>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 truncate" title="{{ $detalle->producto->nombre ?? 'Producto Eléctrico' }}">
                                                {{ $detalle->producto->nombre ?? 'Producto Eléctrico' }}
                                            </h3>
                                            <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                                                <span class="inline-flex items-center font-bold px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md text-[10px]">
                                                    Cant: {{ $detalle->cantidad }}
                                                </span>
                                                <span class="text-[11px] text-slate-400">
                                                    ${{ number_format($detalle->precio_unitario, 0, ',', '.') }} c/u
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right shrink-0">
                                        <span class="text-xs sm:text-sm font-black text-slate-900">
                                            ${{ number_format($detalle->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Devolución Activa Banner si existe --}}
                        @if($ultimaDevolucion)
                            @php $estDev = strtolower($ultimaDevolucion->estado); @endphp
                            <div class="mt-4 p-3.5 rounded-2xl border flex items-center justify-between gap-3 text-xs {{ str_contains($estDev, 'aprob') ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : (str_contains($estDev, 'rechaz') ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-amber-50 border-amber-200 text-amber-900') }}">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid {{ str_contains($estDev, 'aprob') ? 'fa-circle-check text-emerald-600' : (str_contains($estDev, 'rechaz') ? 'fa-circle-xmark text-rose-600' : 'fa-clock-rotate-left text-amber-600') }} text-base"></i>
                                    <div>
                                        <strong class="font-bold">Garantía / Devolución:</strong> {{ $ultimaDevolucion->estado }}
                                        <span class="text-[11px] opacity-80 block sm:inline sm:ml-2">({{ $ultimaDevolucion->motivo }})</span>
                                    </div>
                                </div>
                                <button type="button" @click="abrirDevolucionModal({{ json_encode($pedidoData) }})" class="underline font-bold text-[11px] hover:opacity-80 shrink-0">
                                    Ver Estado
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- 4. Pie de la Tarjeta (Total y Botones de Acción) --}}
                    <div class="bg-slate-50/90 px-5 sm:px-6 py-4 border-t border-slate-200/70 flex flex-col sm:flex-row items-center justify-between gap-4">
                        
                        {{-- Información de Envío y Dirección --}}
                        <div class="flex items-center gap-2 text-xs text-slate-500 w-full sm:w-auto">
                            <i class="fa-solid fa-location-dot text-amber-500 text-xs"></i>
                            <span class="truncate max-w-xs font-medium" title="{{ $pedido->envio->direccion_envio ?? 'Entrega en tienda / registrada' }}">
                                {{ $pedido->envio->direccion_envio ?? 'Entrega en tienda / domicilio registrado' }}
                            </span>
                        </div>

                        {{-- Total y Acciones --}}
                        <div class="flex items-center justify-between sm:justify-end gap-3.5 w-full sm:w-auto">
                            <div class="text-left sm:text-right pr-2">
                                <span class="text-[10px] uppercase font-black text-slate-400 tracking-wider block">Total Pagado</span>
                                <span class="text-base sm:text-lg font-black text-emerald-600 tracking-tight">
                                    ${{ number_format($pedido->total_pedido, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Botón Factura POS --}}
                                <button 
                                    type="button"
                                    @click="abrirFactura({{ json_encode($pedidoData) }})"
                                    class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200/80 shadow-2xs transition flex items-center gap-1.5 cursor-pointer hover:scale-[1.02] active:scale-95"
                                    title="Ver Tirilla POS">
                                    <i class="fa-solid fa-receipt text-emerald-600"></i>
                                    <span class="hidden md:inline">Factura</span>
                                </button>

                                {{-- Botón Garantía / Devolución --}}
                                <button 
                                    type="button"
                                    @click="abrirDevolucionModal({{ json_encode($pedidoData) }})"
                                    class="px-3.5 py-2 {{ $ultimaDevolucion ? 'bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200' }} font-bold text-xs rounded-xl shadow-2xs transition flex items-center gap-1.5 cursor-pointer hover:scale-[1.02] active:scale-95"
                                    title="{{ $ultimaDevolucion ? 'Ver Garantía' : 'Solicitar Garantía o Devolución' }}">
                                    <i class="fa-solid fa-shield-halved text-amber-600"></i>
                                    <span class="hidden md:inline">{{ $ultimaDevolucion ? 'Garantía' : 'Devolución' }}</span>
                                </button>

                                {{-- Botón Ver Detalle Principal --}}
                                <a href="{{ route('pedidos.show', $pedido->id) }}" 
                                   class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 hover:scale-[1.02] active:scale-95">
                                    <span>Detalles</span>
                                    <i class="fa-solid fa-arrow-right text-[10px] text-amber-400"></i>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            @endforeach

            {{-- Paginación --}}
            <div class="mt-8">
                {{ $pedidos->links() }}
            </div>
        </div>
    @endif

    {{-- ==================== MODAL DE FACTURA POS ==================== --}}
    <div 
        x-show="modalFactura" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 overflow-y-auto"
        @click.self="modalFactura = false"
        @keydown.escape.window="modalFactura = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-200 relative my-8 animate-scale-up" @click.stop x-show="pedidoActivo">
            
            {{-- Encabezado Modal --}}
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 no-imprimir">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg border border-emerald-100 shadow-2xs">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Comprobante POS</h3>
                        <p class="text-[11px] text-slate-400">Tirilla térmica autorizada 80mm</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        onclick="window.print()" 
                        class="px-3.5 py-1.5 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-xs cursor-pointer hover:scale-105 active:scale-95">
                        <i class="fa-solid fa-print text-amber-400"></i>
                        <span>Imprimir</span>
                    </button>
                    <button 
                        type="button" 
                        @click="modalFactura = false" 
                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Tirilla POS Imprimible --}}
            <div id="imprimible-pos" class="bg-slate-50/50 p-4 sm:p-5 rounded-2xl border border-dashed border-slate-300 font-mono text-[11px] leading-tight text-slate-950 max-h-[70vh] overflow-y-auto shadow-inner">
                
                {{-- Encabezado Comercio --}}
                <div class="text-center pb-2">
                    <div class="font-black text-sm tracking-widest uppercase mb-0.5">⚡ POWERNET S.A.S.</div>
                    <div class="text-[10px] font-semibold text-slate-700">MATERIALES Y SOLUCIONES ELÉCTRICAS</div>
                    <div class="text-[9px] text-slate-600 mt-1">NIT: 901.458.729-1</div>
                    <div class="text-[9px] text-slate-600">Cra. 15 # 45-20, Bogotá D.C.</div>
                    <div class="text-[9px] text-slate-600">Tel: +57 300 892 4110</div>
                </div>

                <div class="border-t border-dashed border-slate-400 my-2.5"></div>

                {{-- Datos Factura --}}
                <div class="space-y-1 text-[10px]">
                    <div class="text-center font-black uppercase tracking-wider py-0.5 bg-slate-200/60 rounded">FACTURA DE VENTA POS</div>
                    <div class="flex justify-between pt-1">
                        <span class="font-bold">Factura Nº:</span>
                        <span class="font-bold" x-text="pedidoActivo ? pedidoActivo.factura : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Orden ID:</span>
                        <span x-text="pedidoActivo ? '#' + pedidoActivo.id : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Fecha:</span>
                        <span x-text="pedidoActivo ? pedidoActivo.fecha : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Método de Pago:</span>
                        <span class="font-bold uppercase" x-text="pedidoActivo ? pedidoActivo.metodo_pago : ''"></span>
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-400 my-2.5"></div>

                {{-- Datos Cliente --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="font-bold uppercase text-[9px] text-slate-700">CLIENTE / COMPRADOR:</div>
                    <div><span class="font-bold">Nombre:</span> <span x-text="pedidoActivo ? pedidoActivo.cliente_nombre : ''"></span></div>
                    <div><span class="font-bold">CC/NIT:</span> <span x-text="pedidoActivo ? pedidoActivo.cliente_doc : ''"></span></div>
                    <div><span class="font-bold">Tel:</span> <span x-text="pedidoActivo ? pedidoActivo.cliente_tel : ''"></span></div>
                    <div class="break-words"><span class="font-bold">Dir:</span> <span x-text="pedidoActivo ? pedidoActivo.direccion_envio : ''"></span></div>
                </div>

                <div class="border-t border-dashed border-slate-400 my-2.5"></div>

                {{-- Tabla Productos --}}
                <div>
                    <div class="flex justify-between font-bold border-b border-slate-300 pb-1 mb-1.5 text-[10px]">
                        <span>CANT. ARTÍCULO</span>
                        <span>TOTAL</span>
                    </div>

                    <div class="space-y-2 pt-0.5 text-[10px]">
                        <template x-if="pedidoActivo && pedidoActivo.items">
                            <template x-for="(item, idx) in pedidoActivo.items" :key="idx">
                                <div>
                                    <div class="flex justify-between items-baseline">
                                        <span class="font-bold text-slate-900" x-text="item.cantidad + 'x ' + item.nombre"></span>
                                        <span class="font-bold shrink-0 pl-1" x-text="'$' + item.subtotal"></span>
                                    </div>
                                    <div class="text-[9px] text-slate-500 pl-2" x-text="'Unit: $' + item.precio_unitario"></div>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-400 my-2.5"></div>

                {{-- Totales --}}
                <div class="space-y-1 text-[10px]">
                    <div class="flex justify-between">
                        <span>Envío:</span>
                        <span x-text="pedidoActivo && pedidoActivo.costo_envio > 0 ? '$' + Number(pedidoActivo.costo_envio).toLocaleString('es-CO') : 'GRATIS'"></span>
                    </div>
                    <div class="border-t border-slate-300 pt-1.5 flex justify-between font-black text-xs text-slate-950">
                        <span>TOTAL DE VENTA:</span>
                        <span x-text="pedidoActivo ? '$' + pedidoActivo.total_formateado + ' COP' : ''"></span>
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-400 my-2.5"></div>

                {{-- Pie Legal --}}
                <div class="text-center text-[8px] text-slate-600 space-y-1 pt-1">
                    <div class="font-bold">Resolución DIAN No. 18764002910</div>
                    <div>Habilita Facturación POS Nº FAC-00001 a FAC-99999</div>
                    <div class="font-black text-[9px] text-slate-950">¡GRACIAS POR SU COMPRA!</div>
                    <div class="pt-1 text-center text-[10px] tracking-widest text-slate-800">
                        |||| | ||||| || |||||| |||| | |||| ||
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- ==================== MODAL SOLICITUD DE DEVOLUCIÓN / GARANTÍA ==================== --}}
    <div 
        x-show="modalDevolucion" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 overflow-y-auto"
        @click.self="modalDevolucion = false"
        @keydown.escape.window="modalDevolucion = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-200 relative my-8 animate-scale-up" @click.stop x-show="pedidoActivo">
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg border border-amber-100 shadow-2xs">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">
                            <span x-text="pedidoActivo && pedidoActivo.devolucion ? 'Estado de Garantía' : 'Solicitar Garantía / Devolución'"></span>
                        </h3>
                        <p class="text-[10px] text-slate-400" x-text="pedidoActivo ? 'Pedido #' + pedidoActivo.id + ' • Total: $' + pedidoActivo.total_formateado : ''"></p>
                    </div>
                </div>
                <button type="button" @click="modalDevolucion = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- CASO 1: YA EXISTE UNA SOLICITUD RADICADA --}}
            <template x-if="pedidoActivo && pedidoActivo.devolucion">
                <div class="space-y-4 text-xs">
                    <div class="p-4 rounded-2xl border" :class="{
                        'bg-amber-50 border-amber-200 text-amber-900': pedidoActivo.devolucion.estado === 'Pendiente',
                        'bg-emerald-50 border-emerald-200 text-emerald-900': pedidoActivo.devolucion.estado === 'Aprobada' || pedidoActivo.devolucion.estado === 'Completada',
                        'bg-rose-50 border-rose-200 text-rose-900': pedidoActivo.devolucion.estado === 'Rechazada'
                    }">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold uppercase text-[10px] tracking-wider">Estado de la Solicitud:</span>
                            <span class="font-black px-2.5 py-0.5 rounded-full text-xs" :class="{
                                'bg-amber-200 text-amber-950': pedidoActivo.devolucion.estado === 'Pendiente',
                                'bg-emerald-200 text-emerald-950': pedidoActivo.devolucion.estado === 'Aprobada' || pedidoActivo.devolucion.estado === 'Completada',
                                'bg-rose-200 text-rose-950': pedidoActivo.devolucion.estado === 'Rechazada'
                            }" x-text="pedidoActivo.devolucion.estado"></span>
                        </div>
                        <p class="text-[11px]">
                            <strong class="font-bold">Fecha de Solicitud:</strong> <span x-text="pedidoActivo.devolucion.fecha"></span>
                        </p>
                        <p class="text-[11px] mt-1">
                            <strong class="font-bold">Monto en Reclamación:</strong> <span x-text="'$' + pedidoActivo.devolucion.monto + ' COP'"></span>
                        </p>
                    </div>

                    <div>
                        <span class="font-bold text-slate-700 block uppercase text-[10px] mb-1">Motivo Radicado:</span>
                        <div class="p-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl leading-relaxed text-xs" x-text="pedidoActivo.devolucion.motivo"></div>
                    </div>

                    <template x-if="pedidoActivo.devolucion.motivo_rechazo">
                        <div>
                            <span class="font-bold text-rose-600 block uppercase text-[10px] mb-1">Respuesta del Administrador:</span>
                            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl leading-relaxed text-xs" x-text="pedidoActivo.devolucion.motivo_rechazo"></div>
                        </div>
                    </template>

                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="button" @click="modalDevolucion = false" class="px-5 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs">
                            Cerrar
                        </button>
                    </div>
                </div>
            </template>

            {{-- CASO 2: FORMULARIO PARA RADICAR NUEVA DEVOLUCIÓN --}}
            <template x-if="pedidoActivo && !pedidoActivo.devolucion">
                <form :action="devolucionUrl" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1 text-[11px]">Tipo de Reclamación / Causal *</label>
                        <select name="motivo_categoria" required class="w-full rounded-xl border border-slate-300 p-2.5 text-xs font-semibold focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400">
                            <option value="Garantía por defecto técnico">⚡ Garantía por falla técnica / producto defectuoso</option>
                            <option value="Producto no corresponde a lo comprado">📦 Producto incorrecto / no corresponde a lo pedido</option>
                            <option value="Pedido averiado en transporte">🚚 Producto averiado / golpeado en transporte</option>
                            <option value="Inconformidad del cliente">💬 Desistimiento / Inconformidad</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1 text-[11px]">Describe detalladamente el problema *</label>
                        <textarea name="descripcion" rows="4" required placeholder="Explica detalladamente qué sucedió con el producto..." class="w-full rounded-xl border border-slate-300 p-2.5 text-xs focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400"></textarea>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-500 space-y-1">
                        <div class="flex justify-between">
                            <span>Monto a reembolsar:</span>
                            <span class="font-black text-slate-900" x-text="pedidoActivo ? '$' + pedidoActivo.total_formateado + ' COP' : ''"></span>
                        </div>
                        <p class="text-[10px] text-slate-400">Nuestro equipo de soporte revisará la solicitud en un plazo de 24 a 48 horas.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="modalDevolucion = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition">
                            Enviar Solicitud
                        </button>
                    </div>
                </form>
            </template>

        </div>
    </div>

</div>

<script>
function misPedidosManager() {
    return {
        modalFactura: false,
        modalDevolucion: false,
        pedidoActivo: null,
        devolucionUrl: '',
        filtro: 'todos',
        busqueda: '',

        cumpleFiltro(categoria, textoBusqueda) {
            // Filtro por pestaña
            let pasaFiltro = false;
            if (this.filtro === 'todos') pasaFiltro = true;
            else if (this.filtro === categoria) pasaFiltro = true;

            if (!pasaFiltro) return false;

            // Filtro por texto de búsqueda
            if (this.busqueda.trim() === '') return true;
            return textoBusqueda.includes(this.busqueda.toLowerCase().trim());
        },

        abrirFactura(pedido) {
            this.pedidoActivo = pedido;
            this.modalFactura = true;
        },

        abrirDevolucionModal(pedido) {
            this.pedidoActivo = pedido;
            this.devolucionUrl = '{{ url('/mis-pedidos') }}/' + pedido.id + '/devolucion';
            this.modalDevolucion = true;
        }
    };
}
</script>

<style>
@keyframes scaleUp {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.animate-scale-up {
    animation: scaleUp 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@media print {
    body * {
        visibility: hidden;
    }
    #imprimible-pos, #imprimible-pos * {
        visibility: visible;
    }
    #imprimible-pos {
        position: absolute;
        left: 0;
        top: 0;
        width: 80mm;
        margin: 0 auto;
        padding: 0;
        border: none !important;
        max-height: none !important;
        overflow: visible !important;
    }
    .no-imprimir {
        display: none !important;
    }
}
</style>
@endsection
