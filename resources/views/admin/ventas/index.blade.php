@extends('layouts.sidebaradmin')

@section('title', 'Reportes de Ventas y Analítica')

@section('content')
<div
    x-data="adminVentasManager()"
    class="space-y-6">

    {{-- ==================== ENCABEZADO ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-2.5">
                <span class="text-2xl">💰</span>
                Reportes y Analítica de Ventas
            </h1>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Monitorea el rendimiento comercial, ingresos facturados y productos líderes en ventas</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pagos.index') }}" class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200 font-bold text-xs rounded-xl border border-gray-200 dark:border-slate-700 shadow-2xs transition flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-emerald-600 dark:text-emerald-400"></i>
                <span>Ver Pagos</span>
            </a>
            <a href="{{ route('admin.pedidos.index') }}" class="px-4 py-2.5 bg-[#0f172a] dark:bg-[#7c3aed] hover:bg-black dark:hover:bg-[#6d28d9] text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-yellow-400"></i>
                <span>Ver Pedidos</span>
            </a>
        </div>
    </div>

    {{-- ==================== TARJETAS DE MÉTRICAS COMERCIALES ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total Ventas Históricas --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-gray-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-400 dark:text-slate-400 block uppercase">Ingresos Totales</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 block">${{ number_format($totalIngresos, 0, ',', '.') }}</span>
                <span class="text-[10px] text-gray-400 dark:text-slate-500 mt-0.5 block">{{ $totalOrdenesVenta }} órdenes concluidas</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold border border-emerald-100 dark:border-emerald-800/40">
                💵
            </div>
        </div>

        {{-- Ventas del Mes --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-gray-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-blue-500 dark:text-blue-400 block uppercase">Ventas Este Mes</span>
                <span class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-0.5 block">${{ number_format($ventasMes, 0, ',', '.') }}</span>
                <span class="text-[10px] text-gray-400 dark:text-slate-500 mt-0.5 block">Mes en curso</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-bold border border-blue-100 dark:border-blue-800/40">
                📅
            </div>
        </div>

        {{-- Ticket Promedio --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-gray-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-violet-500 dark:text-violet-400 block uppercase">Ticket Promedio</span>
                <span class="text-2xl font-black text-violet-700 dark:text-violet-400 mt-0.5 block">${{ number_format($ticketPromedio, 0, ',', '.') }}</span>
                <span class="text-[10px] text-gray-400 dark:text-slate-500 mt-0.5 block">Por pedido de cliente</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-violet-50 dark:bg-violet-950/40 text-[#7c3aed] dark:text-violet-400 flex items-center justify-center text-xl font-bold border border-violet-100 dark:border-violet-800/40">
                🛒
            </div>
        </div>

        {{-- Unidades Vendidas --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-gray-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-500 dark:text-amber-400 block uppercase">Artículos Vendidos</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white mt-0.5 block">{{ $totalUnidadesVendidas }}</span>
                <span class="text-[10px] text-gray-400 dark:text-slate-500 mt-0.5 block">Unidades despachadas</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold border border-amber-100 dark:border-amber-800/40">
                📦
            </div>
        </div>

    </div>

    {{-- ==================== TOP 5 PRODUCTOS MÁS VENDIDOS ==================== --}}
    @if($topProductos->isNotEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200/80 dark:border-slate-800 shadow-xs p-5">
            <h2 class="text-sm font-black text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                <span class="text-base">🏆</span>
                <span>Top 5 Productos Más Vendidos</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                @foreach($topProductos as $idx => $top)
                    @php
                        $prod = $top->producto;
                        $foto = $prod && $prod->imagenes->first() ? $prod->imagenes->first()->imagen : null;
                    @endphp
                    <div class="bg-gray-50/70 dark:bg-slate-800/60 rounded-xl p-3 border border-gray-200/70 dark:border-slate-700/60 flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-yellow-400 text-gray-950 font-black text-[10px] flex items-center justify-center shadow-2xs">
                            #{{ $idx + 1 }}
                        </div>

                        <div class="flex items-center gap-2.5 mb-2">
                            <div class="w-10 h-10 bg-white dark:bg-slate-900 rounded-lg p-1 border border-gray-200 dark:border-slate-700 shrink-0 flex items-center justify-center overflow-hidden">
                                @if($foto && file_exists(public_path('imagenes_productos/' . $foto)))
                                    <img src="{{ asset('imagenes_productos/' . $foto) }}" alt="{{ $prod->nombre }}" class="max-h-full max-w-full object-contain">
                                @else
                                    <span class="text-xs">💡</span>
                                @endif
                            </div>
                            <div class="min-w-0 pr-4">
                                <p class="text-xs font-bold text-gray-900 dark:text-slate-100 truncate" title="{{ $prod->nombre ?? 'Producto' }}">
                                    {{ $prod->nombre ?? 'Producto' }}
                                </p>
                                <p class="text-[10px] text-gray-400 dark:text-slate-400 font-semibold">Stock: {{ $prod->stock ?? 0 }}</p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-gray-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs">
                            <span class="font-black text-[#7c3aed] dark:text-violet-400">{{ $top->total_vendido }} unid.</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($top->total_ingresos, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ==================== FILTROS Y BÚSQUEDA AVANZADA ==================== --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-gray-200/80 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('admin.ventas.index') }}" class="space-y-3 text-xs">
            
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                
                {{-- Buscador --}}
                <div class="sm:col-span-4 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 text-xs"></i>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}" 
                        placeholder="Buscar por factura, pedido o cliente..." 
                        class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                </div>

                {{-- Filtro Período Rápido --}}
                <div class="sm:col-span-3">
                    <select name="periodo" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 px-3 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="todos" {{ request('periodo') === 'todos' ? 'selected' : '' }}>Histórico Completo</option>
                        <option value="hoy" {{ request('periodo') === 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana" {{ request('periodo') === 'semana' ? 'selected' : '' }}>Esta Semana</option>
                        <option value="mes" {{ request('periodo') === 'mes' ? 'selected' : '' }}>Este Mes</option>
                        <option value="anio" {{ request('periodo') === 'anio' ? 'selected' : '' }}>Este Año</option>
                    </select>
                </div>

                {{-- Filtro Método de Pago --}}
                <div class="sm:col-span-3">
                    <select name="metodo" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 px-3 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="todos">Todos los Métodos de Pago</option>
                        <option value="Transferencia" {{ request('metodo') === 'Transferencia' ? 'selected' : '' }}>Transferencia Bancaria</option>
                        <option value="Nequi" {{ request('metodo') === 'Nequi' ? 'selected' : '' }}>Nequi</option>
                        <option value="Bancolombia" {{ request('metodo') === 'Bancolombia' ? 'selected' : '' }}>Bancolombia</option>
                        <option value="Contra Entrega" {{ request('metodo') === 'Contra Entrega' ? 'selected' : '' }}>Contra Entrega</option>
                        <option value="Tarjeta" {{ request('metodo') === 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                    </select>
                </div>

                {{-- Botón Filtrar y Limpiar --}}
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button 
                        type="submit" 
                        class="flex-1 py-2.5 px-4 bg-[#0f172a] dark:bg-[#7c3aed] hover:bg-black dark:hover:bg-[#6d28d9] text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['q', 'periodo', 'metodo', 'fecha_desde', 'fecha_hasta']))
                        <a href="{{ route('admin.ventas.index') }}" class="p-2.5 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-600 dark:text-slate-300 rounded-xl transition font-bold" title="Limpiar filtros">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

            </div>

            {{-- Rango Personalizado de Fechas --}}
            <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-gray-100 dark:border-slate-800 text-[11px] text-gray-500 dark:text-slate-400">
                <span class="font-bold text-gray-700 dark:text-slate-300">Rango de Fechas Personalizado:</span>
                <div class="flex items-center gap-1.5">
                    <span>Desde:</span>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 px-2 py-1 text-xs">
                </div>
                <div class="flex items-center gap-1.5">
                    <span>Hasta:</span>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 px-2 py-1 text-xs">
                </div>
            </div>

        </form>
    </div>

    {{-- ==================== TABLA DE HISTORIAL DE VENTAS ==================== --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 dark:text-slate-300">
                <thead class="bg-gray-50/80 dark:bg-slate-800/60 text-[11px] uppercase tracking-wider text-gray-500 dark:text-slate-400 border-b border-gray-200/70 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 font-bold">Factura / Venta</th>
                        <th class="px-6 py-4 font-bold">Cliente</th>
                        <th class="px-6 py-4 font-bold">Fecha / Hora</th>
                        <th class="px-6 py-4 font-bold">Artículos</th>
                        <th class="px-6 py-4 font-bold">Método Pago</th>
                        <th class="px-6 py-4 font-bold">Total Facturado</th>
                        <th class="px-6 py-4 font-bold text-right">Ver Productos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 font-medium">
                    @forelse($ventas as $venta)
                        @php
                            $ventaData = [
                                'id' => $venta->id,
                                'factura' => $venta->pago->factura ?? 'FAC-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT),
                                'fecha' => $venta->fecha_pedido ? $venta->fecha_pedido->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                                'total' => $venta->total_pedido,
                                'total_formateado' => number_format($venta->total_pedido, 0, ',', '.'),
                                'metodo_pago' => $venta->pago->metodo_pago ?? 'Tarjeta',
                                'estado_pago' => $venta->pago->estado_pago ?? 'Aprobado',
                                'cliente_nombre' => $venta->cliente && $venta->cliente->persona ? $venta->cliente->persona->nombre_persona : 'Consumidor Final',
                                'cliente_doc' => $venta->cliente && $venta->cliente->persona ? $venta->cliente->persona->documento : '222222222222',
                                'cliente_tel' => $venta->cliente && $venta->cliente->persona ? $venta->cliente->persona->telefono : 'No registra',
                                'direccion_envio' => $venta->envio ? $venta->envio->direccion_envio : ($venta->cliente ? $venta->cliente->direccion : 'Dirección registrada'),
                                'empresa_envios' => $venta->envio ? $venta->envio->empresa_envios : 'Servientrega Express',
                                'costo_envio' => $venta->envio ? $venta->envio->costo : 0,
                                'items' => $venta->detalles->map(function($d) {
                                    $p = $d->producto;
                                    $foto = $p && $p->imagenes->first() ? $p->imagenes->first()->imagen : null;
                                    return [
                                        'nombre' => $p->nombre ?? 'Producto Eléctrico',
                                        'cantidad' => $d->cantidad,
                                        'precio_unitario' => number_format($d->precio_unitario, 0, ',', '.'),
                                        'subtotal' => number_format($d->subtotal, 0, ',', '.'),
                                        'foto' => $foto ? asset('imagenes_productos/' . $foto) : null,
                                    ];
                                })
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-slate-800/40 transition">
                            
                            {{-- Factura / Venta --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-black text-xs shrink-0 border border-emerald-100 dark:border-emerald-800/40">
                                        🧾
                                    </div>
                                    <div>
                                        <span class="font-black text-gray-950 dark:text-white text-sm block">{{ $venta->pago->factura ?? 'FAC-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <span class="text-[10px] text-gray-400 dark:text-slate-500 font-bold block">Orden #{{ $venta->id }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Cliente --}}
                            <td class="px-6 py-4">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-white block text-xs">
                                        {{ $venta->cliente && $venta->cliente->persona ? $venta->cliente->persona->nombre_persona : 'Consumidor Final' }}
                                    </span>
                                    <span class="text-[11px] text-gray-400 dark:text-slate-400 block">
                                        CC: {{ $venta->cliente && $venta->cliente->persona && $venta->cliente->persona->documento ? $venta->cliente->persona->documento : 'Consumidor Final' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Fecha / Hora --}}
                            <td class="px-6 py-4 text-[11px] text-gray-600 dark:text-slate-400">
                                {{ $venta->fecha_pedido ? $venta->fecha_pedido->format('d/m/Y H:i') : now()->format('d/m/Y') }}
                            </td>

                            {{-- Artículos --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-900 dark:text-slate-200">{{ $venta->detalles->sum('cantidad') }} unid.</span>
                                    <span class="text-[10px] text-gray-400 dark:text-slate-500">({{ $venta->detalles->count() }} refs)</span>
                                </div>
                            </td>

                            {{-- Método de Pago --}}
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-800 dark:text-slate-200 font-bold text-[11px] inline-flex items-center gap-1.5 border border-gray-200 dark:border-slate-700">
                                    <i class="fa-solid fa-credit-card text-yellow-500 text-[10px]"></i>
                                    <span>{{ $venta->pago->metodo_pago ?? 'Tarjeta' }}</span>
                                </span>
                            </td>

                            {{-- Total Facturado --}}
                            <td class="px-6 py-4">
                                <span class="font-black text-emerald-600 dark:text-emerald-400 text-base block">
                                    ${{ number_format($venta->total_pedido, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-gray-400 dark:text-slate-500 font-medium">COP</span>
                            </td>

                            {{-- Botón de Ojo para Ver Productos --}}
                            <td class="px-6 py-4 text-right">
                                <button
                                    type="button"
                                    @click="abrirProductosModal({{ json_encode($ventaData) }})"
                                    class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-slate-800 hover:bg-[#7c3aed] dark:hover:bg-[#7c3aed] text-gray-600 dark:text-slate-300 hover:text-white dark:hover:text-white transition flex items-center justify-center shadow-2xs cursor-pointer ml-auto"
                                    title="Ver Productos Vendidos">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <i class="fa-solid fa-sack-dollar text-3xl mb-2 text-gray-300 dark:text-slate-600"></i>
                                <p class="text-xs font-bold">No se encontraron ventas para los filtros seleccionados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($ventas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800">
                {{ $ventas->links() }}
            </div>
        @endif
    </div>

    {{-- ==================== MODAL DE PRODUCTOS VENDIDOS ==================== --}}
    <div 
        x-show="modalProductos" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="modalProductos = false"
        @keydown.escape.window="modalProductos = false">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl border border-gray-100 dark:border-slate-800 relative my-8" @click.stop x-show="ventaActiva">
            
            {{-- Encabezado Modal --}}
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-violet-50 dark:bg-violet-950/40 text-[#7c3aed] dark:text-violet-400 flex items-center justify-center text-lg font-bold border border-violet-100 dark:border-violet-800/40">
                        🛍️
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white flex items-center gap-2">
                            <span>Productos de la Venta</span>
                            <span class="text-xs text-[#7c3aed] dark:text-violet-400 font-bold" x-text="ventaActiva ? ventaActiva.factura : ''"></span>
                        </h3>
                        <p class="text-[11px] text-gray-400 dark:text-slate-400" x-text="ventaActiva ? 'Cliente: ' + ventaActiva.cliente_nombre + ' • ' + ventaActiva.fecha : ''"></p>
                    </div>
                </div>

                <button 
                    type="button" 
                    @click="modalProductos = false" 
                    class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-500 dark:text-slate-400 flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Lista de Productos Vendidos --}}
            <div class="space-y-3 max-h-[55vh] overflow-y-auto pr-1">
                <template x-if="ventaActiva && ventaActiva.items">
                    <template x-for="(item, idx) in ventaActiva.items" :key="idx">
                        <div class="p-3 bg-gray-50/80 dark:bg-slate-800/60 rounded-2xl border border-gray-200/70 dark:border-slate-700/60 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-12 bg-white dark:bg-slate-900 rounded-xl p-1 border border-gray-200 dark:border-slate-700 shrink-0 flex items-center justify-center overflow-hidden">
                                    <template x-if="item.foto">
                                        <img :src="item.foto" :alt="item.nombre" class="max-h-full max-w-full object-contain">
                                    </template>
                                    <template x-if="!item.foto">
                                        <span class="text-base">💡</span>
                                    </template>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 dark:text-slate-100 truncate" x-text="item.nombre"></p>
                                    <p class="text-[11px] text-gray-400 dark:text-slate-400 mt-0.5">
                                        Cantidad: <strong class="text-gray-700 dark:text-slate-200" x-text="item.cantidad"></strong> × <span x-text="'$' + item.precio_unitario"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-black text-gray-900 dark:text-white block" x-text="'$' + item.subtotal"></span>
                                <span class="text-[10px] text-gray-400 dark:text-slate-500 font-medium">COP</span>
                            </div>
                        </div>
                    </template>
                </template>
            </div>

            {{-- Resumen y Totales --}}
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-800 space-y-1.5 text-xs">
                <div class="flex justify-between text-gray-500 dark:text-slate-400">
                    <span>Método de Pago:</span>
                    <span class="font-bold text-gray-800 dark:text-slate-200" x-text="ventaActiva ? ventaActiva.metodo_pago : ''"></span>
                </div>
                <div class="flex justify-between text-gray-500 dark:text-slate-400">
                    <span>Costo de Envío:</span>
                    <span class="font-bold text-gray-800 dark:text-slate-200" x-text="ventaActiva && ventaActiva.costo_envio > 0 ? '$' + Number(ventaActiva.costo_envio).toLocaleString('es-CO') : 'GRATIS'"></span>
                </div>
                <div class="pt-2 border-t border-gray-100 dark:border-slate-800 flex justify-between items-baseline font-black text-sm text-gray-900 dark:text-white">
                    <span>Total Facturado:</span>
                    <span class="text-emerald-600 dark:text-emerald-400 text-base" x-text="ventaActiva ? '$' + ventaActiva.total_formateado + ' COP' : ''"></span>
                </div>
            </div>

            {{-- Botón Cerrar --}}
            <div class="mt-5 pt-3 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end">
                <button 
                    type="button" 
                    @click="modalProductos = false" 
                    class="w-full sm:w-auto px-5 py-2.5 bg-[#0f172a] dark:bg-[#7c3aed] hover:bg-black dark:hover:bg-[#6d28d9] text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    Cerrar
                </button>
            </div>

        </div>
    </div>

</div>

<script>
function adminVentasManager() {
    return {
        modalProductos: false,
        ventaActiva: null,

        abrirProductosModal(venta) {
            this.ventaActiva = venta;
            this.modalProductos = true;
        }
    };
}
</script>
@endsection

