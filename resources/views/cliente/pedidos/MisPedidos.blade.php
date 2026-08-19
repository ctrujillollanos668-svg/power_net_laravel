@extends('layouts.tienda')

@section('titulo', 'Mis Pedidos - PowerNet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8" x-data="misPedidosManager()">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-gray-900 transition">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-gray-900">Mis Pedidos</span>
    </nav>

    {{-- Mensajes Flash --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    {{-- Encabezado --}}
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
            <span>Mis Pedidos</span>
            <span class="text-xs font-bold text-[#7c3aed] bg-violet-50 px-3 py-1 rounded-full border border-violet-200">
                Historial de Compras
            </span>
        </h1>
        <p class="text-xs text-gray-500 mt-1">Rastrea el estado de tus compras, descarga tus facturas y gestiona garantías o devoluciones.</p>
    </div>

    @if($pedidos->isEmpty())
        {{-- Estado Sin Pedidos --}}
        <div class="bg-white rounded-3xl p-12 text-center border border-gray-200/80 shadow-xs max-w-xl mx-auto my-8">
            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 border border-amber-100">
                📦
            </div>
            <h2 class="text-lg font-black text-gray-900 mb-1">Aún no tienes pedidos registrados</h2>
            <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                Cuando realices compras en la tienda, podrás ver el seguimiento en tiempo real de tus envíos y facturas aquí.
            </p>
            <a href="{{ route('tienda.inicio') }}#productos-seccion" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-gray-950 font-black text-xs rounded-2xl transition shadow-sm">
                <i class="fa-solid fa-bolt"></i>
                <span>Explorar Productos</span>
            </a>
        </div>
    @else
        {{-- Lista de Tarjetas de Pedidos --}}
        <div class="space-y-4">
            @foreach($pedidos as $pedido)
                @php
                    $ultimaDevolucion = $pedido->devoluciones->last();
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
                @endphp

                {{-- Tarjeta de Pedido (Diseño Horizontal) --}}
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-5 sm:p-6 hover:shadow-xs transition">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 sm:gap-6 items-center">
                        
                        {{-- 1. Columna: Pedido e Imágenes de Productos --}}
                        <div class="flex items-center gap-3">
                            {{-- Miniaturas de Productos Comprados --}}
                            <div class="flex -space-x-2.5 shrink-0">
                                @forelse($pedido->detalles->take(3) as $d)
                                    @php
                                        $img = $d->producto && $d->producto->imagenes && $d->producto->imagenes->first() ? $d->producto->imagenes->first()->imagen : null;
                                    @endphp
                                    <div class="w-11 h-11 rounded-xl bg-gray-50 border-2 border-white shadow-2xs p-1 flex items-center justify-center overflow-hidden shrink-0" title="{{ $d->producto->nombre ?? 'Producto' }} (Cant: {{ $d->cantidad }})">
                                        @if($img && file_exists(public_path('imagenes_productos/' . $img)))
                                            <img src="{{ asset('imagenes_productos/' . $img) }}" alt="{{ $d->producto->nombre }}" class="max-h-full max-w-full object-contain">
                                        @else
                                            <span class="text-sm">💡</span>
                                        @endif
                                    </div>
                                @empty
                                    <div class="w-11 h-11 rounded-xl bg-gray-100 border-2 border-white shadow-2xs flex items-center justify-center text-sm shrink-0">
                                        📦
                                    </div>
                                @endforelse

                                @if($pedido->detalles->count() > 3)
                                    <div class="w-11 h-11 rounded-xl bg-violet-100 border-2 border-white text-[#7c3aed] font-black text-xs flex items-center justify-center shadow-2xs shrink-0" title="Más productos">
                                        +{{ $pedido->detalles->count() - 3 }}
                                    </div>
                                @endif
                            </div>

                            <div>
                                <span class="text-gray-400 font-medium block text-xs mb-0.5">Pedido</span>
                                <span class="font-black text-gray-950 text-xl tracking-tight">#{{ $pedido->id }}</span>
                            </div>
                        </div>

                        {{-- 2. Columna: Fecha --}}
                        <div>
                            <span class="text-gray-400 font-medium block text-xs mb-1">Fecha</span>
                            <span class="font-bold text-gray-800 text-xs sm:text-sm block leading-snug">
                                {{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        {{-- 3. Columna: Total --}}
                        <div>
                            <span class="text-gray-400 font-medium block text-xs mb-1">Total</span>
                            <span class="font-black text-emerald-600 text-base sm:text-lg block tracking-tight">
                                ${{ number_format($pedido->total_pedido, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- 4. Columna: Estado Pedido & Devolución --}}
                        <div>
                            <span class="text-gray-400 font-medium block text-xs mb-1.5">Estado pedido</span>
                            @php
                                $estadoLower = strtolower($pedido->estado_pedido ?? 'en preparación');
                            @endphp
                            @if(str_contains($estadoLower, 'cancel'))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80 shadow-2xs whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                    Cancelado
                                </span>
                            @elseif(str_contains($estadoLower, 'enviado') || str_contains($estadoLower, 'camino'))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                    Enviado
                                </span>
                            @elseif(str_contains($estadoLower, 'entreg'))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                    Entregado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                                    En preparación
                                </span>
                            @endif

                            {{-- Indicador de Devolución si existe --}}
                            @if($ultimaDevolucion)
                                <div class="mt-1">
                                    @php $estDev = strtolower($ultimaDevolucion->estado); @endphp
                                    @if(str_contains($estDev, 'aprob') || str_contains($estDev, 'complet'))
                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            ✅ Devolución Aprobada
                                        </span>
                                    @elseif(str_contains($estDev, 'rechaz'))
                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-black bg-red-100 text-red-800 border border-red-200">
                                            ❌ Devolución Rechazada
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-black bg-amber-100 text-amber-800 border border-amber-200">
                                            🔄 Devolución en Revisión
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- 5. Columna: Pago --}}
                        <div>
                            <span class="text-gray-400 font-medium block text-xs mb-1.5">Pago</span>
                            <div>
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black text-white bg-emerald-700 shadow-2xs">
                                    {{ $pedido->pago && $pedido->pago->estado_pago === 'Pendiente al entregar' ? 'Pendiente' : 'Pagado' }}
                                </span>
                                <span class="text-xs text-gray-500 font-medium block mt-1 lowercase truncate">
                                    {{ $pedido->pago->metodo_pago ?? 'transferencia' }}
                                </span>
                            </div>
                        </div>

                        {{-- 6. Columna: Botones Verticales en la Derecha --}}
                        <div class="col-span-2 sm:col-span-3 md:col-span-1 flex md:flex-col items-center md:items-end justify-end gap-1.5">
                            
                            {{-- Botón 1: Ver Detalle --}}
                            <a href="{{ route('pedidos.show', $pedido->id) }}" 
                               class="px-4 py-1 bg-white hover:bg-gray-100 text-gray-800 font-bold text-xs rounded-lg border border-gray-300 shadow-2xs transition inline-flex items-center justify-center w-full md:w-12 text-center"
                               title="Ver Detalle">
                                Ver
                            </a>

                            {{-- Botón 2: Factura POS (Verde con icono) --}}
                            <button 
                                type="button"
                                @click="abrirFactura({{ json_encode($pedidoData) }})"
                                class="w-full md:w-12 h-7 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg shadow-2xs transition flex items-center justify-center cursor-pointer"
                                title="Ver Factura POS">
                                <i class="fa-solid fa-receipt text-xs"></i>
                            </button>

                            {{-- Botón 3: Solicitar / Ver Devolución --}}
                            <button 
                                type="button"
                                @click="abrirDevolucionModal({{ json_encode($pedidoData) }})"
                                class="w-full md:w-12 h-7 {{ $ultimaDevolucion ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-gray-100 hover:bg-[#7c3aed] text-gray-700 hover:text-white border border-gray-200' }} font-bold text-xs rounded-lg shadow-2xs transition flex items-center justify-center cursor-pointer"
                                title="{{ $ultimaDevolucion ? 'Ver Estado de Devolución' : 'Solicitar Devolución o Garantía' }}">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                            </button>

                        </div>

                    </div>
                </div>
            @endforeach

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $pedidos->links() }}
            </div>
        </div>
    @endif

    {{-- ==================== MODAL DE FACTURA POS ==================== --}}
    <div 
        x-show="modalFactura" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="modalFactura = false"
        @keydown.escape.window="modalFactura = false">
        
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-gray-100 relative my-8" @click.stop x-show="pedidoActivo">
            
            {{-- Encabezado Modal --}}
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100 no-imprimir">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🧾</span>
                    <div>
                        <h3 class="text-xs font-black text-gray-900">Factura Modo POS</h3>
                        <p class="text-[10px] text-gray-400">Tirilla térmica 80mm</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        onclick="window.print()" 
                        class="px-3 py-1.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <i class="fa-solid fa-print text-yellow-400"></i>
                        <span>Imprimir</span>
                    </button>
                    <button 
                        type="button" 
                        @click="modalFactura = false" 
                        class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Tirilla POS Imprimible --}}
            <div id="imprimible-pos" class="bg-white p-4 rounded-2xl border border-dashed border-gray-300 font-mono text-[11px] leading-tight text-black max-h-[70vh] overflow-y-auto">
                
                {{-- Encabezado Comercio --}}
                <div class="text-center pb-2">
                    <div class="font-black text-sm tracking-widest uppercase mb-0.5">⚡ POWERNET S.A.S.</div>
                    <div class="text-[10px] font-semibold text-gray-700">MATERIALES Y SOLUCIONES ELÉCTRICAS</div>
                    <div class="text-[9px] text-gray-600 mt-1">NIT: 901.458.729-1</div>
                    <div class="text-[9px] text-gray-600">Cra. 15 # 45-20, Bogotá D.C.</div>
                    <div class="text-[9px] text-gray-600">Tel: +57 300 892 4110</div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Datos Factura --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="text-center font-black uppercase tracking-wider py-0.5">FACTURA DE VENTA POS</div>
                    <div class="flex justify-between">
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
                        <span class="font-bold" x-text="pedidoActivo ? pedidoActivo.metodo_pago : ''"></span>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Datos Cliente --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="font-bold uppercase text-[9px] text-gray-700">CLIENTE / COMPRADOR:</div>
                    <div><span class="font-bold">Nombre:</span> <span x-text="pedidoActivo ? pedidoActivo.cliente_nombre : ''"></span></div>
                    <div><span class="font-bold">CC/NIT:</span> <span x-text="pedidoActivo ? pedidoActivo.cliente_doc : ''"></span></div>
                    <div><span class="font-bold">Tel:</span> <span x-text="pedidoActivo ? pedidoActivo.cliente_tel : ''"></span></div>
                    <div class="break-words"><span class="font-bold">Dir:</span> <span x-text="pedidoActivo ? pedidoActivo.direccion_envio : ''"></span></div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Tabla Productos --}}
                <div>
                    <div class="flex justify-between font-bold border-b border-gray-300 pb-1 mb-1 text-[10px]">
                        <span>CANT. ARTÍCULO</span>
                        <span>TOTAL</span>
                    </div>

                    <div class="space-y-1.5 pt-0.5 text-[10px]">
                        <template x-if="pedidoActivo && pedidoActivo.items">
                            <template x-for="(item, idx) in pedidoActivo.items" :key="idx">
                                <div>
                                    <div class="flex justify-between items-baseline">
                                        <span class="font-bold" x-text="item.cantidad + 'x ' + item.nombre"></span>
                                        <span class="font-bold shrink-0 pl-1" x-text="'$' + item.subtotal"></span>
                                    </div>
                                    <div class="text-[9px] text-gray-500 pl-3" x-text="'Unit: $' + item.precio_unitario"></div>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Totales --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="flex justify-between">
                        <span>Envío:</span>
                        <span x-text="pedidoActivo && pedidoActivo.costo_envio > 0 ? '$' + Number(pedidoActivo.costo_envio).toLocaleString('es-CO') : 'GRATIS'"></span>
                    </div>
                    <div class="border-t border-gray-300 pt-1 flex justify-between font-black text-xs">
                        <span>TOTAL DE VENTA:</span>
                        <span x-text="pedidoActivo ? '$' + pedidoActivo.total_formateado + ' COP' : ''"></span>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Pie Legal --}}
                <div class="text-center text-[8px] text-gray-600 space-y-1 pt-1">
                    <div class="font-bold">Resolución DIAN No. 18764002910</div>
                    <div>Habilita Facturación POS Nº FAC-00001 a FAC-99999</div>
                    <div class="font-black text-[9px] text-black">¡GRACIAS POR SU COMPRA!</div>
                    <div class="pt-1 text-center text-[10px] tracking-widest">
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
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="modalDevolucion = false"
        @keydown.escape.window="modalDevolucion = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-gray-100 relative my-8" @click.stop x-show="pedidoActivo">
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🔄</span>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">
                            <span x-text="pedidoActivo && pedidoActivo.devolucion ? 'Estado de tu Devolución' : 'Solicitar Devolución o Garantía'"></span>
                        </h3>
                        <p class="text-[10px] text-gray-400" x-text="pedidoActivo ? 'Pedido #' + pedidoActivo.id + ' • Total: $' + pedidoActivo.total_formateado : ''"></p>
                    </div>
                </div>
                <button type="button" @click="modalDevolucion = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- CASO 1: YA EXISTE UNA SOLICITUD RADICADA --}}
            <template x-if="pedidoActivo && pedidoActivo.devolucion">
                <div class="space-y-4 text-xs">
                    <div class="p-4 rounded-2xl border" :class="{
                        'bg-amber-50 border-amber-200 text-amber-900': pedidoActivo.devolucion.estado === 'Pendiente',
                        'bg-emerald-50 border-emerald-200 text-emerald-900': pedidoActivo.devolucion.estado === 'Aprobada' || pedidoActivo.devolucion.estado === 'Completada',
                        'bg-red-50 border-red-200 text-red-900': pedidoActivo.devolucion.estado === 'Rechazada'
                    }">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold uppercase text-[10px] tracking-wider">Estado de la Solicitud:</span>
                            <span class="font-black px-2.5 py-0.5 rounded-full text-xs" :class="{
                                'bg-amber-200 text-amber-950': pedidoActivo.devolucion.estado === 'Pendiente',
                                'bg-emerald-200 text-emerald-950': pedidoActivo.devolucion.estado === 'Aprobada' || pedidoActivo.devolucion.estado === 'Completada',
                                'bg-red-200 text-red-950': pedidoActivo.devolucion.estado === 'Rechazada'
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
                        <span class="font-bold text-gray-700 block uppercase text-[10px] mb-1">Motivo Radicado:</span>
                        <div class="p-3 bg-gray-50 border border-gray-200 text-gray-800 rounded-xl leading-relaxed text-xs" x-text="pedidoActivo.devolucion.motivo"></div>
                    </div>

                    <template x-if="pedidoActivo.devolucion.motivo_rechazo">
                        <div>
                            <span class="font-bold text-red-600 block uppercase text-[10px] mb-1">Respuesta del Administrador:</span>
                            <div class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl leading-relaxed text-xs" x-text="pedidoActivo.devolucion.motivo_rechazo"></div>
                        </div>
                    </template>

                    <div class="pt-3 border-t border-gray-100 flex justify-end">
                        <button type="button" @click="modalDevolucion = false" class="px-5 py-2 bg-[#0f172a] text-white font-bold text-xs rounded-xl">
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
                        <label class="block font-bold text-gray-700 uppercase mb-1">Tipo de Solicitud / Categoría *</label>
                        <select name="motivo_categoria" required class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold focus:ring-1 focus:ring-[#7c3aed]">
                            <option value="Garantía por defecto técnico">⚡ Garantía por falla técnica / producto defectuoso</option>
                            <option value="Producto no corresponde a lo comprado">📦 Producto incorrecto / no corresponde a lo pedido</option>
                            <option value="Pedido averiado en transporte">🚚 Producto averiado / golpeado en transporte</option>
                            <option value="Inconformidad del cliente">💬 Desistimiento / Inconformidad</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Describe detalladamente el problema *</label>
                        <textarea name="descripcion" rows="4" required placeholder="Por favor explica qué sucedió con el producto para procesar tu solicitud..." class="w-full rounded-xl border border-gray-300 p-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]"></textarea>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-[11px] text-gray-500 space-y-1">
                        <div class="flex justify-between">
                            <span>Monto a reembolsar:</span>
                            <span class="font-bold text-gray-900" x-text="pedidoActivo ? '$' + pedidoActivo.total_formateado + ' COP' : ''"></span>
                        </div>
                        <p class="text-[10px] text-gray-400">Nuestro equipo de servicio al cliente validará tu solicitud en un plazo máximo de 24 a 48 horas hábiles.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="modalDevolucion = false" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-bold text-xs hover:bg-gray-50">
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
