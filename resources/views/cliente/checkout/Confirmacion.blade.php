@extends('layouts.tienda')

@section('titulo', '¡Pedido Confirmado! - PowerNet')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10" x-data="{ modalFactura: false, modalDevolucion: false }">

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

    {{-- Banner de Éxito y Felicitaciones --}}
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-200/80 shadow-sm text-center mb-8 relative overflow-hidden">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-4 border border-emerald-200 shadow-sm">
            <i class="fa-solid fa-check"></i>
        </div>

        <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-800 text-xs font-black uppercase px-3.5 py-1.5 rounded-full mb-3 border border-emerald-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            ¡Compra Procesada Exitosamente!
        </span>

        <h1 class="text-2xl sm:text-4xl font-black text-gray-900 mb-2 tracking-tight">
            ¡Gracias por tu compra!
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 max-w-lg mx-auto leading-relaxed">
            Hemos recibido tu orden y ya estamos preparando tus productos eléctricos con todo el cuidado de <strong class="text-gray-900">PowerNet</strong>.
        </p>

        <div class="mt-6 inline-flex flex-wrap items-center justify-center gap-4 bg-gray-50 p-3.5 rounded-2xl border border-gray-200/70 text-xs">
            <div>
                <span class="text-gray-400 block text-[10px] font-bold uppercase">Número de Pedido:</span>
                <span class="font-black text-gray-900 text-sm">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>
            <div>
                <span class="text-gray-400 block text-[10px] font-bold uppercase">Factura:</span>
                <span class="font-black text-[#7c3aed] text-sm">{{ $pedido->pago->factura ?? 'FAC-0000' }}</span>
            </div>
            <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>
            <div>
                <span class="text-gray-400 block text-[10px] font-bold uppercase">Estado:</span>
                <span class="font-black text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full text-[11px]">
                    {{ $pedido->estado_pedido ?? 'En preparación' }}
                </span>
            </div>
            <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>
            <div>
                <button 
                    type="button" 
                    @click="modalFactura = true" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                    <i class="fa-solid fa-receipt text-yellow-400"></i>
                    <span>Factura POS</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Desglose del Pedido y Entrega --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        {{-- Tarjeta 1: Información de Envío y Contacto --}}
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-gray-100">
                    <span class="text-xl">🚚</span>
                    <h2 class="text-sm font-black text-gray-900">Datos de Entrega</h2>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-gray-400 font-bold block text-[10px] uppercase">Destinatario:</span>
                        <span class="font-bold text-gray-800">{{ $pedido->cliente->persona->nombre_persona ?? 'Cliente PowerNet' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 font-bold block text-[10px] uppercase">Teléfono / WhatsApp:</span>
                        <span class="font-bold text-gray-800">{{ $pedido->cliente->persona->telefono ?? 'No especificado' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 font-bold block text-[10px] uppercase">Dirección de Envío:</span>
                        <span class="font-bold text-gray-800">{{ $pedido->envio->direccion_envio ?? $pedido->cliente->direccion }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 font-bold block text-[10px] uppercase">Transportadora Asignada:</span>
                        <span class="font-bold text-gray-800">{{ $pedido->envio->empresa_envios ?? 'Servientrega Express' }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-400 flex items-center gap-2">
                <i class="fa-solid fa-clock text-yellow-500"></i>
                <span>Tiempo de entrega estimado: 2 a 4 días hábiles</span>
            </div>
        </div>

        {{-- Tarjeta 2: Resumen de Pago y Facturación --}}
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-gray-100">
                    <span class="text-xl">💳</span>
                    <h2 class="text-sm font-black text-gray-900">Detalles del Pago</h2>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-gray-400 font-bold block text-[10px] uppercase">Método de Pago:</span>
                        <span class="font-black text-gray-900">{{ $pedido->pago->metodo_pago ?? 'Tarjeta' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 font-bold block text-[10px] uppercase">Estado de la Transacción:</span>
                        <span class="font-bold text-emerald-600">{{ $pedido->pago->estado_pago ?? 'Aprobado' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 font-bold block text-[10px] uppercase">Fecha de Orden:</span>
                        <span class="font-bold text-gray-800">{{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y h:i A') : now()->format('d/m/Y') }}</span>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-baseline justify-between">
                        <span class="text-xs font-bold text-gray-700">Total Facturado:</span>
                        <span class="text-xl font-black text-gray-900">${{ number_format($pedido->total_pedido, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-400 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                <span>Transacción segura y respaldada por PowerNet</span>
            </div>
        </div>

    </div>

    {{-- Lista de Productos Comprados con Botones de Factura y PDF --}}
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-xs mb-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-gray-100">
            <div>
                <h2 class="text-sm font-black text-gray-900 flex items-center gap-2">
                    <span>📦</span>
                    <span>Productos en tu Pedido</span>
                </h2>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $pedido->detalles->count() }} producto(s) en esta orden</p>
            </div>

            {{-- Botones de Factura, Devolución y PDF en la misma sección --}}
            <div class="flex flex-wrap items-center gap-2">
                @php $devPedido = $pedido->devoluciones->last(); @endphp
                <button 
                    type="button" 
                    @click="modalDevolucion = true" 
                    class="px-3.5 py-2 {{ $devPedido ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-gray-100 hover:bg-amber-500 hover:text-white text-gray-700' }} font-black text-xs rounded-xl transition inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    <span>{{ $devPedido ? 'Estado Devolución' : 'Garantía / Devolución' }}</span>
                </button>

                <button 
                    type="button" 
                    @click="modalFactura = true" 
                    class="px-3.5 py-2 bg-violet-50 hover:bg-violet-100 text-[#7c3aed] font-black text-xs rounded-xl border border-violet-200 transition inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-receipt text-xs"></i>
                    <span>Ver Factura POS</span>
                </button>

                <a 
                    href="{{ route('pedido.factura.pos', $pedido->id) }}" 
                    target="_blank"
                    class="px-3.5 py-2 bg-[#0f172a] hover:bg-black text-white font-black text-xs rounded-xl transition inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                    <i class="fa-solid fa-file-pdf text-yellow-400 text-xs"></i>
                    <span>Descargar PDF</span>
                </a>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            @foreach($pedido->detalles as $detalle)
                @php
                    $producto = $detalle->producto;
                    $foto = $producto && $producto->imagenes && $producto->imagenes->first() ? $producto->imagenes->first()->imagen : null;
                @endphp
                <div class="py-3.5 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 bg-gray-50 rounded-xl p-1 border border-gray-100 shrink-0 flex items-center justify-center overflow-hidden">
                            @if($foto && file_exists(public_path('imagenes_productos/' . $foto)))
                                <img src="{{ asset('imagenes_productos/' . $foto) }}" alt="{{ $producto->nombre }}" class="max-h-full max-w-full object-contain">
                            @else
                                <div class="w-full h-full bg-amber-50 rounded-lg flex items-center justify-center text-amber-500 font-black text-sm">
                                    💡
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $producto->nombre ?? 'Producto PowerNet' }}</p>
                            <p class="text-[11px] text-gray-400">Cantidad: <strong class="text-gray-700">{{ $detalle->cantidad }}</strong> × ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-black text-gray-900 shrink-0">
                        ${{ number_format($detalle->subtotal, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Barra de Acciones y Totales al Pie de la Lista --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-gray-50/60 p-3.5 rounded-2xl">
            <div class="text-xs">
                <span class="text-gray-500 font-medium">Factura: <strong class="text-gray-900 font-bold">{{ $pedido->pago->factura ?? 'FAC-0000' }}</strong></span>
                <span class="mx-2 text-gray-300">|</span>
                <span class="text-gray-500 font-medium">Total: <strong class="text-gray-900 font-black">${{ number_format($pedido->total_pedido, 0, ',', '.') }}</strong></span>
            </div>

            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    @click="modalFactura = true" 
                    class="px-3 py-1.5 bg-white hover:bg-gray-100 text-gray-800 font-bold text-xs rounded-lg border border-gray-200 shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-eye text-[#7c3aed]"></i>
                    <span>Ver Factura</span>
                </button>
                <button 
                    type="button" 
                    @click="modalFactura = true; setTimeout(() => window.print(), 350)"
                    class="px-3 py-1.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-lg shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-download text-yellow-400"></i>
                    <span>Descargar PDF</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Botones de Navegación --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <button 
            type="button"
            @click="modalFactura = true" 
            class="w-full sm:w-auto px-8 py-3.5 bg-violet-600 hover:bg-violet-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl transition shadow-sm inline-flex items-center justify-center gap-2 cursor-pointer">
            <i class="fa-solid fa-receipt text-yellow-300"></i>
            <span>Ver Factura POS</span>
        </button>
        @auth
            <a href="{{ route('pedidos.index') }}" 
               class="w-full sm:w-auto px-8 py-3.5 bg-[#0f172a] hover:bg-black text-white font-black text-xs uppercase tracking-wider rounded-2xl transition shadow-sm inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-box-archive text-yellow-400"></i>
                <span>Ver Mis Pedidos</span>
            </a>
        @endauth
        <a href="{{ route('tienda.inicio') }}" 
           class="w-full sm:w-auto px-8 py-3.5 bg-yellow-400 hover:bg-yellow-500 text-gray-950 font-black text-xs uppercase tracking-wider rounded-2xl transition shadow-sm inline-flex items-center justify-center gap-2">
            <i class="fa-solid fa-bolt"></i>
            <span>Seguir Comprando</span>
        </a>
    </div>

    {{-- ==================== MODAL DE FACTURA MODO POS ==================== --}}
    <div 
        x-show="modalFactura" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="modalFactura = false"
        @keydown.escape.window="modalFactura = false">
        
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-gray-100 relative my-8" @click.stop>
            
            {{-- Encabezado del Modal con Botones de Acción --}}
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
                        class="px-3 py-1.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-xs cursor-pointer"
                        title="Imprimir o guardar PDF">
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

            {{-- Contenido Imprimible de la Tirilla POS --}}
            <div id="imprimible-pos" class="bg-white p-4 rounded-2xl border border-dashed border-gray-300 font-mono text-[11px] leading-tight text-black max-h-[70vh] overflow-y-auto">
                
                {{-- Encabezado Comercio --}}
                <div class="text-center pb-2">
                    <div class="font-black text-sm tracking-widest uppercase mb-0.5">⚡ POWERNET S.A.S.</div>
                    <div class="text-[10px] font-semibold text-gray-700">MATERIALES Y SOLUCIONES ELÉCTRICAS</div>
                    <div class="text-[9px] text-gray-600 mt-1">NIT: 901.458.729-1</div>
                    <div class="text-[9px] text-gray-600">Régimen Común - Responsable de IVA</div>
                    <div class="text-[9px] text-gray-600">Cra. 15 # 45-20, Bogotá D.C.</div>
                    <div class="text-[9px] text-gray-600">Tel: +57 300 892 4110</div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Datos Factura --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="text-center font-black uppercase tracking-wider py-0.5">FACTURA DE VENTA POS</div>
                    <div class="flex justify-between">
                        <span class="font-bold">Factura Nº:</span>
                        <span class="font-bold">{{ $pedido->pago->factura ?? 'FAC-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pedido ID:</span>
                        <span>#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Fecha:</span>
                        <span>{{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Datos Cliente --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="font-bold uppercase text-[9px] text-gray-700">CLIENTE:</div>
                    <div><span class="font-bold">Nombre:</span> {{ $pedido->cliente->persona->nombre_persona ?? 'Consumidor Final' }}</div>
                    <div><span class="font-bold">CC/NIT:</span> {{ $pedido->cliente->persona->documento ?: '222222222222' }}</div>
                    <div><span class="font-bold">Tel:</span> {{ $pedido->cliente->persona->telefono ?: 'No registra' }}</div>
                    <div class="break-words"><span class="font-bold">Dir:</span> {{ $pedido->envio->direccion_envio ?? $pedido->cliente->direccion }}</div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Tabla Productos --}}
                <div>
                    <div class="flex justify-between font-bold border-b border-gray-300 pb-1 mb-1 text-[10px]">
                        <span>CANT. ARTÍCULO</span>
                        <span>TOTAL</span>
                    </div>

                    <div class="space-y-1.5 pt-0.5 text-[10px]">
                        @php $subtotalCalculado = 0; @endphp
                        @foreach($pedido->detalles as $item)
                            @php $subtotalCalculado += $item->subtotal; @endphp
                            <div>
                                <div class="flex justify-between items-baseline">
                                    <span class="font-bold">{{ $item->cantidad }}x {{ $item->producto->nombre ?? 'Producto Eléctrico' }}</span>
                                    <span class="font-bold shrink-0 pl-1">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-[9px] text-gray-500 pl-3">
                                    Unit: ${{ number_format($item->precio_unitario, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Totales --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>${{ number_format($subtotalCalculado, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Envío:</span>
                        <span>{{ ($pedido->envio && $pedido->envio->costo > 0) ? '$' . number_format($pedido->envio->costo, 0, ',', '.') : 'GRATIS' }}</span>
                    </div>
                    <div class="border-t border-gray-300 pt-1 flex justify-between font-black text-xs">
                        <span>TOTAL:</span>
                        <span>${{ number_format($pedido->total_pedido, 0, ',', '.') }} COP</span>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Info Pago --}}
                <div class="text-[9px] space-y-0.5 text-gray-700">
                    <div><span class="font-bold">Pago:</span> {{ $pedido->pago->metodo_pago ?? 'Tarjeta' }} ({{ $pedido->pago->estado_pago ?? 'Aprobado' }})</div>
                    <div><span class="font-bold">Envío:</span> {{ $pedido->envio->empresa_envios ?? 'Servientrega Express' }}</div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Pie Legal --}}
                <div class="text-center text-[8px] text-gray-600 space-y-0.5 pt-1">
                    <div class="font-bold">Resolución DIAN No. 18764002910</div>
                    <div>Habilita Facturación POS Nº FAC-00001 a FAC-99999</div>
                    <div class="font-black text-[9px] text-black pt-1">¡GRACIAS POR SU COMPRA!</div>
                    <div>Conserve este comprobante para garantías.</div>
                    <div class="pt-1 text-center text-[10px] tracking-widest">
                        |||| | ||||| || |||||| |||| | |||| ||
                    </div>
                </div>

    {{-- ==================== MODAL SOLICITUD DE DEVOLUCIÓN / GARANTÍA ==================== --}}
    <div 
        x-show="modalDevolucion" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="modalDevolucion = false"
        @keydown.escape.window="modalDevolucion = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-gray-100 relative my-8" @click.stop>
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🔄</span>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">
                            {{ $devPedido ? 'Estado de tu Devolución' : 'Solicitar Devolución o Garantía' }}
                        </h3>
                        <p class="text-[10px] text-gray-400">Pedido #{{ $pedido->id }} • Total: ${{ number_format($pedido->total_pedido, 0, ',', '.') }} COP</p>
                    </div>
                </div>
                <button type="button" @click="modalDevolucion = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            @if($devPedido)
                {{-- CASO 1: YA EXISTE UNA SOLICITUD RADICADA --}}
                <div class="space-y-4 text-xs">
                    <div class="p-4 rounded-2xl border 
                        {{ $devPedido->estado === 'Pendiente' ? 'bg-amber-50 border-amber-200 text-amber-900' : '' }}
                        {{ in_array($devPedido->estado, ['Aprobada', 'Completada']) ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : '' }}
                        {{ $devPedido->estado === 'Rechazada' ? 'bg-red-50 border-red-200 text-red-900' : '' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold uppercase text-[10px] tracking-wider">Estado de la Solicitud:</span>
                            <span class="font-black px-2.5 py-0.5 rounded-full text-xs
                                {{ $devPedido->estado === 'Pendiente' ? 'bg-amber-200 text-amber-950' : '' }}
                                {{ in_array($devPedido->estado, ['Aprobada', 'Completada']) ? 'bg-emerald-200 text-emerald-950' : '' }}
                                {{ $devPedido->estado === 'Rechazada' ? 'bg-red-200 text-red-950' : '' }}">
                                {{ $devPedido->estado }}
                            </span>
                        </div>
                        <p class="text-[11px]">
                            <strong class="font-bold">Fecha de Solicitud:</strong> {{ $devPedido->fecha_devolucion ? $devPedido->fecha_devolucion->format('d/m/Y H:i') : '' }}
                        </p>
                        <p class="text-[11px] mt-1">
                            <strong class="font-bold">Monto en Reclamación:</strong> ${{ number_format($devPedido->monto_devolucion, 0, ',', '.') }} COP
                        </p>
                    </div>

                    <div>
                        <span class="font-bold text-gray-700 block uppercase text-[10px] mb-1">Motivo Radicado:</span>
                        <div class="p-3 bg-gray-50 border border-gray-200 text-gray-800 rounded-xl leading-relaxed text-xs">
                            {{ $devPedido->motivo }}
                        </div>
                    </div>

                    @if($devPedido->motivo_rechazo)
                        <div>
                            <span class="font-bold text-red-600 block uppercase text-[10px] mb-1">Respuesta del Administrador:</span>
                            <div class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl leading-relaxed text-xs">
                                {{ $devPedido->motivo_rechazo }}
                            </div>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-gray-100 flex justify-end">
                        <button type="button" @click="modalDevolucion = false" class="px-5 py-2 bg-[#0f172a] text-white font-bold text-xs rounded-xl">
                            Cerrar
                        </button>
                    </div>
                </div>
            @else
                {{-- CASO 2: FORMULARIO PARA RADICAR NUEVA DEVOLUCIÓN --}}
                <form action="{{ route('pedidos.devolucion', $pedido->id) }}" method="POST" class="space-y-4 text-xs">
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
                            <span class="font-bold text-gray-900">${{ number_format($pedido->total_pedido, 0, ',', '.') }} COP</span>
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
            @endif

        </div>
    </div>

</div>

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
