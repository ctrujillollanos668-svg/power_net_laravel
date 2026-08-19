@extends('layouts.tienda')

@section('titulo', 'Finalizar Compra - Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8" x-data="checkoutManager()">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-gray-900 transition">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <a href="{{ route('carrito.index') }}" class="hover:text-gray-900 transition">Carrito</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-gray-900">Finalizar Compra</span>
    </nav>

    {{-- Encabezado --}}
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
            <span>Checkout Seguro</span>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200 flex items-center gap-1.5">
                <i class="fa-solid fa-lock text-[10px]"></i>
                <span>Encriptación SSL 256-bit</span>
            </span>
        </h1>
        <p class="text-xs text-gray-500 mt-1">Completa los datos de envío y selecciona tu método de pago para procesar tu pedido.</p>
    </div>

    {{-- Alerta de Error si Hubo Fallos --}}
    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <form action="{{ route('checkout.procesar') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Columna Izquierda: Formulario de Envío y Pagos (7 columnas) --}}
            <div class="lg:col-span-7 space-y-6">
                
                {{-- PASO 1: DATOS DE ENVÍO Y CONTACTO --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-xs">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <span class="w-8 h-8 rounded-full bg-[#0f172a] text-white flex items-center justify-center font-black text-xs">
                            1
                        </span>
                        <div>
                            <h2 class="text-base font-black text-gray-900">Información de Entrega</h2>
                            <p class="text-[11px] text-gray-400">¿A dónde y a nombre de quién enviamos tu pedido?</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        
                        {{-- Nombre Completo --}}
                        <div class="sm:col-span-2">
                            <label for="nombre" class="block font-bold text-gray-700 uppercase mb-1.5">Nombre Completo *</label>
                            <input 
                                type="text" 
                                id="nombre" 
                                name="nombre" 
                                value="{{ old('nombre', $user->name ?? '') }}" 
                                placeholder="Ej. Carlos Rodríguez" 
                                required
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                            @error('nombre') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Correo Electrónico --}}
                        <div>
                            <label for="email" class="block font-bold text-gray-700 uppercase mb-1.5">Correo Electrónico *</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $user->email ?? '') }}" 
                                placeholder="tuemail@ejemplo.com" 
                                required
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                            @error('email') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Teléfono / WhatsApp --}}
                        <div>
                            <label for="telefono" class="block font-bold text-gray-700 uppercase mb-1.5">Teléfono / WhatsApp *</label>
                            <input 
                                type="tel" 
                                id="telefono" 
                                name="telefono" 
                                value="{{ old('telefono', $user->telefono ?? '') }}" 
                                placeholder="Ej. 300 123 4567" 
                                required
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                            @error('telefono') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Documento de Identidad (Cédula / NIT) --}}
                        <div class="sm:col-span-2">
                            <label for="documento" class="block font-bold text-gray-700 uppercase mb-1.5">Cédula / NIT (Para Factura)</label>
                            <input 
                                type="text" 
                                id="documento" 
                                name="documento" 
                                value="{{ old('documento') }}" 
                                placeholder="Ej. 1020304050" 
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                        </div>

                        {{-- Dirección de Entrega --}}
                        <div class="sm:col-span-2">
                            <label for="direccion" class="block font-bold text-gray-700 uppercase mb-1.5">Dirección de Entrega *</label>
                            <input 
                                type="text" 
                                id="direccion" 
                                name="direccion" 
                                value="{{ old('direccion', $user->direccion ?? '') }}" 
                                placeholder="Ej. Carrera 15 # 45-20, Apto 302" 
                                required
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                            @error('direccion') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Ciudad / Municipio --}}
                        <div>
                            <label for="ciudad" class="block font-bold text-gray-700 uppercase mb-1.5">Ciudad / Municipio *</label>
                            <input 
                                type="text" 
                                id="ciudad" 
                                name="ciudad" 
                                value="{{ old('ciudad', 'Bogotá D.C.') }}" 
                                required
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                            @error('ciudad') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Departamento --}}
                        <div>
                            <label for="departamento" class="block font-bold text-gray-700 uppercase mb-1.5">Departamento *</label>
                            <input 
                                type="text" 
                                id="departamento" 
                                name="departamento" 
                                value="{{ old('departamento', 'Cundinamarca') }}" 
                                required
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                            @error('departamento') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Indicaciones / Notas Adicionales --}}
                        <div class="sm:col-span-2">
                            <label for="notas" class="block font-bold text-gray-700 uppercase mb-1.5">Notas de Entrega (Opcional)</label>
                            <textarea 
                                id="notas" 
                                name="notas" 
                                rows="2" 
                                placeholder="Ej. Timbre 201, dejar en portería, casa esquinera color blanco..." 
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">{{ old('notas') }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- PASO 2: SELECCIÓN DE MÉTODO DE PAGO (CONFIGURADOS POR EL ADMINISTRADOR) --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-xs">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <span class="w-8 h-8 rounded-full bg-[#0f172a] text-white flex items-center justify-center font-black text-xs">
                            2
                        </span>
                        <div>
                            <h2 class="text-base font-black text-gray-900">Método de Pago</h2>
                            <p class="text-[11px] text-gray-400">Selecciona una de las formas de pago habilitadas por la tienda</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($metodosPago as $index => $metodo)
                            <label 
                                class="flex flex-col p-4 rounded-2xl border-2 transition cursor-pointer"
                                :class="metodoSeleccionado == {{ $metodo->id }} ? 'border-[#7c3aed] bg-violet-50/40' : 'border-gray-200 hover:border-gray-300'">
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <input 
                                            type="radio" 
                                            name="metodo_pago_id" 
                                            value="{{ $metodo->id }}" 
                                            x-model="metodoSeleccionado" 
                                            required
                                            class="text-[#7c3aed] focus:ring-[#7c3aed]">
                                        <div>
                                            <span class="text-xs sm:text-sm font-black text-gray-900 block">{{ $metodo->nombre }}</span>
                                            @if($metodo->numero || $metodo->titular)
                                                <span class="text-[11px] text-gray-500 font-medium">
                                                    {{ $metodo->numero ? 'Cuenta/Número: ' . $metodo->numero : '' }} 
                                                    {{ $metodo->titular ? ' - ' . $metodo->titular : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xl">
                                        @if($metodo->tipo == 'tarjeta') 💳
                                        @elseif($metodo->tipo == 'nequi') 📱
                                        @elseif($metodo->tipo == 'contraentrega') 💵
                                        @elseif($metodo->tipo == 'transferencia') 🏦
                                        @else ⚡
                                        @endif
                                    </span>
                                </div>

                                {{-- Instrucciones configuradas por el Administrador --}}
                                @if(!empty($metodo->instrucciones))
                                    <div x-show="metodoSeleccionado == {{ $metodo->id }}" x-cloak class="mt-3 pt-3 border-t border-violet-100 text-xs text-gray-600 bg-white p-3 rounded-xl border border-violet-100">
                                        <p class="font-bold text-gray-900 mb-0.5">💡 Instrucciones:</p>
                                        <p>{{ $metodo->instrucciones }}</p>
                                    </div>
                                @endif
                            </label>
                        @empty
                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold text-center">
                                No hay métodos de pago activos en este momento. Por favor contacte al administrador.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Columna Derecha: Resumen del Pedido y Botón Confirmar (5 columnas) --}}
            <div class="lg:col-span-5 sticky top-28 space-y-4">
                
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-sm">
                    <h2 class="text-base font-black text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center justify-between">
                        <span>Resumen de tu Pedido</span>
                        <span class="text-xs font-bold text-[#7c3aed]">{{ $totalItems }} artículo(s)</span>
                    </h2>

                    {{-- Lista de Artículos Mini --}}
                    <div class="max-h-60 overflow-y-auto space-y-3 pr-1 divide-y divide-gray-100 mb-5">
                        @foreach($cart as $item)
                            <div class="flex items-center justify-between gap-3 pt-3 first:pt-0">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-12 h-12 bg-gray-50 rounded-xl p-1 border border-gray-100 shrink-0 flex items-center justify-center overflow-hidden">
                                        @if(!empty($item['imagen']))
                                            <img src="{{ asset('imagenes_productos/' . $item['imagen']) }}" alt="{{ $item['nombre'] }}" class="max-h-full max-w-full object-contain">
                                        @else
                                            <i class="fa-solid fa-bolt text-yellow-400 text-sm"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-gray-900 truncate">{{ $item['nombre'] }}</p>
                                        <p class="text-[10px] text-gray-500 font-semibold">Cant: <strong class="text-gray-900">{{ $item['cantidad'] }}</strong> × ${{ number_format($item['precio_oferta'] ?? $item['precio'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-gray-900 shrink-0">
                                    ${{ number_format(($item['precio_oferta'] ?? $item['precio']) * $item['cantidad'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desglose Financiero --}}
                    <div class="space-y-3 text-xs pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900">${{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        @if($descuentoTotal > 0)
                            <div class="flex items-center justify-between text-emerald-600 font-bold">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-tag"></i>
                                    <span>Descuentos en oferta:</span>
                                </span>
                                <span>-${{ number_format($descuentoTotal, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-gray-600">
                            <span>Envío nacional:</span>
                            @if($costoEnvio === 0)
                                <span class="font-extrabold text-emerald-600 uppercase text-[11px]">Gratis</span>
                            @else
                                <span class="font-bold text-gray-900">${{ number_format($costoEnvio, 0, ',', '.') }}</span>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 my-3"></div>

                        <div class="flex items-baseline justify-between pt-1">
                            <div>
                                <span class="text-sm font-black text-gray-900 block">Total a Pagar</span>
                                <span class="text-[10px] text-gray-400">Impuestos y factura incluidos</span>
                            </div>
                            <span class="text-2xl font-black text-gray-900">
                                ${{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Botón Confirmar Compra --}}
                    <button 
                        type="submit" 
                        class="w-full mt-6 bg-[#0f172a] hover:bg-black text-white font-black text-xs uppercase tracking-wider py-4 px-6 rounded-2xl text-center transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2 cursor-pointer group">
                        <i class="fa-solid fa-circle-check text-yellow-400 text-sm group-hover:scale-110 transition-transform"></i>
                        <span>Confirmar y Realizar Pedido</span>
                    </button>

                    {{-- Garantías --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 space-y-2 text-[11px] text-gray-500 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-emerald-500 text-xs"></i>
                            <span>Garantía de fábrica 100% original</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-blue-500 text-xs"></i>
                            <span>Factura legal de venta con tu pedido</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </form>

</div>

<script>
function checkoutManager() {
    return {
        metodoSeleccionado: {{ $metodosPago->first() ? $metodosPago->first()->id : 'null' }}
    };
}
</script>
@endsection
