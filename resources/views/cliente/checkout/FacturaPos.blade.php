<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura POS - Pedido #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }} - PowerNet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Estilos específicos para impresión en rollo térmico POS de 80mm */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .pos-receipt {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 80mm !important;
                padding: 0 !important;
                margin: 0 auto !important;
            }
            @page {
                size: 80mm auto;
                margin: 2mm;
            }
        }

        .ticket-font {
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4 font-sans text-gray-900">

    {{-- Barra de Herramientas de Impresión y Descarga (Se oculta al imprimir) --}}
    <div class="max-w-md mx-auto mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 no-print flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="text-xl">🧾</span>
            <div>
                <h2 class="text-xs font-black text-gray-900">Factura Modo POS</h2>
                <p class="text-[10px] text-gray-400">Formato tirilla térmica de 80mm</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button 
                type="button" 
                onclick="window.print()" 
                class="px-4 py-2 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-print text-yellow-400"></i>
                <span>Imprimir / PDF</span>
            </button>
            <a 
                href="{{ route('checkout.confirmacion', $pedido->id) }}" 
                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>

    {{-- Tirilla Térmica Factura POS --}}
    <div class="pos-receipt max-w-[340px] mx-auto bg-white p-6 rounded-2xl shadow-md border border-gray-200 ticket-font text-xs leading-tight text-black">
        
        {{-- Encabezado del Comercio --}}
        <div class="text-center pb-2">
            <div class="font-black text-base tracking-widest uppercase mb-0.5">⚡ POWERNET S.A.S.</div>
            <div class="text-[11px] font-semibold text-gray-700">SOLUCIONES Y MATERIALES ELÉCTRICOS</div>
            <div class="text-[10px] text-gray-600 mt-1">NIT: 901.458.729-1</div>
            <div class="text-[10px] text-gray-600">Régimen Común - Responsable de IVA</div>
            <div class="text-[10px] text-gray-600">Cra. 15 # 45-20, Bogotá D.C.</div>
            <div class="text-[10px] text-gray-600">Tel / WhatsApp: +57 300 892 4110</div>
            <div class="text-[10px] text-gray-600">contacto@powernet.com</div>
        </div>

        <div class="border-t border-dashed border-gray-400 my-2"></div>

        {{-- Datos de la Factura --}}
        <div class="text-[11px] space-y-0.5">
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

        {{-- Datos del Cliente --}}
        <div class="text-[11px] space-y-0.5">
            <div class="font-bold uppercase text-[10px] text-gray-700">DATOS DEL ADQUIRIENTE:</div>
            <div><span class="font-bold">Cliente:</span> {{ $pedido->cliente->persona->nombre_persona ?? 'Consumidor Final' }}</div>
            <div><span class="font-bold">CC / NIT:</span> {{ $pedido->cliente->persona->documento ?: '222222222222' }}</div>
            <div><span class="font-bold">Teléfono:</span> {{ $pedido->cliente->persona->telefono ?: 'No registra' }}</div>
            <div class="break-words"><span class="font-bold">Dirección:</span> {{ $pedido->envio->direccion_envio ?? $pedido->cliente->direccion }}</div>
        </div>

        <div class="border-t border-dashed border-gray-400 my-2"></div>

        {{-- Tabla de Productos --}}
        <div class="text-[11px]">
            <div class="flex justify-between font-bold border-b border-gray-300 pb-1 mb-1">
                <span>CANT. DESCRIPCIÓN</span>
                <span>TOTAL</span>
            </div>

            <div class="space-y-1.5 pt-0.5">
                @php 
                    $subtotalCalculado = 0; 
                @endphp
                @foreach($pedido->detalles as $item)
                    @php
                        $subtotalCalculado += $item->subtotal;
                    @endphp
                    <div>
                        <div class="flex justify-between items-baseline">
                            <span class="font-bold">{{ $item->cantidad }}x {{ $item->producto->nombre ?? 'Producto Eléctrico' }}</span>
                            <span class="font-bold shrink-0 pl-1">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-[10px] text-gray-600 pl-4">
                            Unitario: ${{ number_format($item->precio_unitario, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="border-t border-dashed border-gray-400 my-2"></div>

        {{-- Totales y Liquidación --}}
        <div class="text-[11px] space-y-1">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>${{ number_format($subtotalCalculado, 0, ',', '.') }}</span>
            </div>

            @if($pedido->envio && $pedido->envio->costo > 0)
                <div class="flex justify-between">
                    <span>Costo de Envío:</span>
                    <span>${{ number_format($pedido->envio->costo, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="flex justify-between text-gray-700">
                    <span>Costo de Envío:</span>
                    <span>GRATIS</span>
                </div>
            @endif

            <div class="border-t border-gray-300 pt-1 flex justify-between font-black text-sm">
                <span>TOTAL A PAGAR:</span>
                <span>${{ number_format($pedido->total_pedido, 0, ',', '.') }} COP</span>
            </div>
        </div>

        <div class="border-t border-dashed border-gray-400 my-2"></div>

        {{-- Información de Pago y Despacho --}}
        <div class="text-[10px] space-y-0.5 text-gray-700">
            <div><span class="font-bold">Forma de Pago:</span> {{ $pedido->pago->metodo_pago ?? 'Tarjeta / Transferencia' }}</div>
            <div><span class="font-bold">Estado Pago:</span> {{ $pedido->pago->estado_pago ?? 'Aprobado' }}</div>
            <div><span class="font-bold">Transportadora:</span> {{ $pedido->envio->empresa_envios ?? 'Servientrega Express' }}</div>
            <div><span class="font-bold">Estado Pedido:</span> {{ $pedido->estado_pedido ?? 'En preparación' }}</div>
        </div>

        <div class="border-t border-dashed border-gray-400 my-2"></div>

        {{-- Pie Legal y Código de Barra --}}
        <div class="text-center text-[9px] text-gray-600 space-y-1 pt-1">
            <div class="font-bold">Resolución DIAN No. 18764002910</div>
            <div>Habilita Facturación POS Nº FAC-00001 a FAC-99999</div>
            <div class="font-black text-[10px] text-black pt-1">¡GRACIAS POR SU COMPRA!</div>
            <div>Conserve este comprobante para cualquier garantía o soporte técnico.</div>
            <div class="pt-2 text-center text-xs tracking-widest font-mono">
                |||| | ||||| || |||||| |||| | |||| ||
            </div>
            <div class="text-[8px] text-gray-400">PowerNet Ecommerce System v2.0</div>
        </div>

    </div>

</body>
</html>
