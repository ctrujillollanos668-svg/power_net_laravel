@extends('layouts.tienda')

@section('titulo', 'Mi Carrito de Compras')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8" x-data="carritoManager()">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-gray-900 transition">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-gray-900">Carrito de Compras</span>
    </nav>

    {{-- Título y Total Items --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                <span>Mi Carrito</span>
                <span class="text-sm font-bold text-slate-900 bg-slate-100 px-3 py-1 rounded-full border border-slate-200" 
                      x-text="totalItems + ' artículo' + (totalItems === 1 ? '' : 's')">
                    {{ $totalItems }} artículos
                </span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Revisa tus productos seleccionados y gestiona las cantidades antes de pagar.</p>
        </div>

        <button 
            type="button" 
            x-show="totalItems > 0"
            x-cloak
            @click="vaciarCarrito()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200/80 transition shrink-0 self-start sm:self-auto cursor-pointer">
            <i class="fa-regular fa-trash-can"></i>
            <span>Vaciar Carrito</span>
        </button>
    </div>

    {{-- Alerta Flash --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Contenedor Principal --}}
    <template x-if="totalItems === 0">
        {{-- Carrito Vacío --}}
        <div class="bg-white rounded-3xl p-12 text-center border border-gray-200/80 shadow-xs max-w-2xl mx-auto my-8">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center text-4xl text-slate-800 mx-auto mb-4 border border-slate-200">
                🛒
            </div>
            <h2 class="text-xl font-black text-gray-900 mb-2">Tu carrito está vacío</h2>
            <p class="text-xs text-gray-500 max-w-md mx-auto mb-6 leading-relaxed">
                Aún no has agregado productos a tu carrito. Explora nuestro catálogo de productos eléctricos de alta calidad y encuentra lo que necesitas.
            </p>
            <a href="{{ route('tienda.inicio') }}#productos-seccion" 
               class="inline-flex items-center gap-2 px-8 py-3.5 bg-yellow-400 hover:bg-yellow-500 text-gray-950 font-black text-xs rounded-2xl transition shadow-sm hover:shadow-md">
                <i class="fa-solid fa-bolt"></i>
                <span>Explorar Productos</span>
            </a>
        </div>
    </template>

    <template x-if="totalItems > 0">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Columna Izquierda: Lista de Artículos (8 columnas) --}}
            <div class="lg:col-span-8 space-y-4">
                
                {{-- Banner Envío Gratis Progreso --}}
                <div class="bg-gradient-to-r from-violet-50 to-amber-50 rounded-2xl p-4 border border-violet-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🚚</span>
                        <div>
                            <p class="text-xs font-black text-gray-900" x-show="totalSinEnvio >= 150000">
                                ¡Felicidades! Tienes <span class="text-emerald-600 uppercase">Envío Gratis</span> en esta compra.
                            </p>
                            <p class="text-xs font-bold text-gray-700" x-show="totalSinEnvio < 150000">
                                Agrega <span class="text-[#7c3aed] font-black" x-text="formatoMoneda(150000 - totalSinEnvio)"></span> más para obtener <span class="text-emerald-600">Envío Gratis</span>.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Contenedor de Items --}}
                <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs divide-y divide-gray-100 overflow-hidden">
                    <template x-for="(item, id) in items" :key="id">
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition">
                            
                            {{-- Info Producto e Imagen --}}
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-20 h-20 bg-gray-50 rounded-2xl p-2 border border-gray-100 shrink-0 flex items-center justify-center overflow-hidden">
                                    <template x-if="item.imagen">
                                        <img :src="'/imagenes_productos/' + item.imagen" :alt="item.nombre" class="max-h-full max-w-full object-contain">
                                    </template>
                                    <template x-if="!item.imagen">
                                        <i class="fa-solid fa-bolt text-2xl text-yellow-400"></i>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] font-bold text-yellow-600 uppercase tracking-wider block" x-text="item.categoria"></span>
                                    <a :href="'/producto/' + item.id" class="text-sm sm:text-base font-black text-gray-900 hover:text-[#7c3aed] transition line-clamp-1" x-text="item.nombre"></a>
                                    
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <template x-if="item.precio_oferta">
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="text-xs sm:text-sm font-black text-gray-900" x-text="formatoMoneda(item.precio_oferta)"></span>
                                                <span class="text-[11px] text-gray-400 line-through" x-text="formatoMoneda(item.precio)"></span>
                                                <span class="text-[9px] font-black text-red-600 bg-red-50 px-1.5 py-0.5 rounded" x-text="'-' + item.descuento + '%'"></span>
                                            </div>
                                        </template>
                                        <template x-if="!item.precio_oferta">
                                            <span class="text-xs sm:text-sm font-bold text-gray-900" x-text="formatoMoneda(item.precio)"></span>
                                        </template>
                                    </div>
                                    <span class="text-[11px] font-semibold text-gray-500 block mt-0.5" x-text="'Stock disponible: ' + (item.stock || 0) + ' unid.'"></span>
                                </div>
                            </div>

                            {{-- Selectores de Cantidad, Subtotal y Eliminar --}}
                            <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-6 pt-2 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                                
                                {{-- Cápsula Cantidad con Límite de Stock --}}
                                <div class="bg-gray-100 rounded-xl p-1 flex items-center justify-between w-28 shrink-0">
                                    <button 
                                        type="button" 
                                        @click="cambiarCantidad(id, item.cantidad - 1)" 
                                        :disabled="item.cantidad <= 1"
                                        :class="item.cantidad <= 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-200 cursor-pointer'"
                                        class="w-7 h-7 rounded-lg bg-white shadow-2xs text-gray-900 font-black text-xs flex items-center justify-center transition">
                                        <i class="fa-solid fa-minus text-[9px]"></i>
                                    </button>
                                    <span class="font-black text-xs text-gray-900 select-none px-2" x-text="item.cantidad"></span>
                                    <button 
                                        type="button" 
                                        @click="if(item.cantidad < (item.stock || 1)) { cambiarCantidad(id, item.cantidad + 1) } else { window.alertaAdvertencia('No puedes añadir más unidades. Solo hay ' + (item.stock || 1) + ' disponibles en stock.', 'Límite de Stock') }" 
                                        :disabled="item.cantidad >= (item.stock || 1)"
                                        :class="item.cantidad >= (item.stock || 1) ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-200 cursor-pointer'"
                                        class="w-7 h-7 rounded-lg bg-white shadow-2xs text-gray-900 font-black text-xs flex items-center justify-center transition">
                                        <i class="fa-solid fa-plus text-[9px]"></i>
                                    </button>
                                </div>

                                {{-- Subtotal del Item --}}
                                <div class="text-right shrink-0 min-w-[90px]">
                                    <span class="text-xs text-gray-400 block sm:hidden">Total:</span>
                                    <span class="text-sm sm:text-base font-black text-gray-900" x-text="formatoMoneda((item.precio_oferta || item.precio) * item.cantidad)"></span>
                                </div>

                                {{-- Botón Eliminar --}}
                                <button 
                                    type="button" 
                                    @click="eliminarItem(id)" 
                                    title="Eliminar producto" 
                                    class="w-8 h-8 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition flex items-center justify-center shrink-0 cursor-pointer">
                                    <i class="fa-regular fa-trash-can text-sm"></i>
                                </button>
                            </div>

                        </div>
                    </template>
                </div>

                {{-- Botón Seguir Comprando --}}
                <div class="pt-2">
                    <a href="{{ route('tienda.inicio') }}#productos-seccion" 
                       class="inline-flex items-center gap-2 text-xs font-bold text-gray-700 hover:text-[#7c3aed] transition">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Seguir explorando productos</span>
                    </a>
                </div>

            </div>

            {{-- Columna Derecha: Resumen del Pedido (4 columnas) --}}
            <div class="lg:col-span-4 sticky top-28 space-y-4">
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm">
                    <h2 class="text-lg font-black text-gray-900 mb-5 pb-3 border-b border-gray-100 flex items-center justify-between">
                        <span>Resumen del Pedido</span>
                        <span class="text-xs font-bold text-gray-400" x-text="totalItems + ' producto(s)'"></span>
                    </h2>

                    <div class="space-y-3.5 text-xs">
                        
                        {{-- Subtotal --}}
                        <div class="flex items-center justify-between text-gray-600">
                            <span>Subtotal productos:</span>
                            <span class="font-extrabold text-gray-900" x-text="formatoMoneda(subtotal)"></span>
                        </div>

                        {{-- Descuentos --}}
                        <div class="flex items-center justify-between text-emerald-600" x-show="descuentoTotal > 0">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-tag"></i>
                                <span>Descuentos en oferta:</span>
                            </span>
                            <span class="font-black" x-text="'-' + formatoMoneda(descuentoTotal)"></span>
                        </div>

                        {{-- Costo de Envío --}}
                        <div class="flex items-center justify-between text-gray-600">
                            <span>Envío estimado:</span>
                            <span class="font-extrabold" :class="costoEnvio === 0 ? 'text-emerald-600' : 'text-gray-900'" 
                                  x-text="costoEnvio === 0 ? 'Gratis' : formatoMoneda(costoEnvio)"></span>
                        </div>

                        <div class="border-t border-gray-100 my-4"></div>

                        {{-- Total Final --}}
                        <div class="flex items-baseline justify-between pt-1">
                            <div>
                                <span class="text-sm font-black text-gray-900 block">Total a Pagar</span>
                                <span class="text-[10px] text-gray-400">IVA incluido</span>
                            </div>
                            <span class="text-2xl font-black text-gray-900" x-text="formatoMoneda(totalFinal)"></span>
                        </div>

                    </div>

                    {{-- Botón de Checkout / Proceder al Pago --}}
                    <div class="mt-6 space-y-2.5">
                        <a href="{{ route('checkout.index') }}" 
                           @guest @click.prevent="$dispatch('abrir-login')" @endguest
                           class="w-full bg-[#0f172a] hover:bg-black text-white font-black text-xs uppercase tracking-wider py-4 px-6 rounded-2xl text-center transition shadow-md hover:shadow-lg flex items-center justify-center gap-2 group cursor-pointer">
                            <span>Continuar con el Pago</span>
                            <i class="fa-solid fa-arrow-right text-xs text-yellow-400 group-hover:translate-x-1 transition-transform"></i>
                        </a>

                        @guest
                            <div class="text-center pt-1">
                                <button 
                                    type="button" 
                                    @click="$dispatch('abrir-login')"
                                    class="text-[11px] font-bold text-gray-500 hover:text-gray-900 transition cursor-pointer">
                                    ¿Ya tienes cuenta? <span class="text-[#7c3aed] underline font-extrabold">Inicia sesión para comprar</span>
                                </button>
                            </div>
                        @endguest
                    </div>

                    {{-- Garantías y Sellos --}}
                    <div class="mt-6 pt-5 border-t border-gray-100 space-y-2.5 text-[11px] text-gray-500 font-medium">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-shield-halved text-emerald-500 text-sm"></i>
                            <span>Compra 100% segura y protegida</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-truck-fast text-blue-500 text-sm"></i>
                            <span>Envíos rápidos a todo el país</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-rotate-left text-purple-500 text-sm"></i>
                            <span>Garantía oficial y devoluciones fáciles</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </template>

</div>

{{-- Script de Alpine para Gestión del Carrito --}}
<script>
    function carritoManager() {
        return {
            items: @json($cart),
            subtotal: {{ $subtotal }},
            descuentoTotal: {{ $descuentoTotal }},
            costoEnvio: {{ $costoEnvio }},
            totalFinal: {{ $total }},
            totalItems: {{ $totalItems }},

            get totalSinEnvio() {
                return this.subtotal - this.descuentoTotal;
            },

            formatoMoneda(monto) {
                return '$' + new Intl.NumberFormat('es-CO').format(monto);
            },

            async cambiarCantidad(id, cantidad) {
                if (cantidad < 1) {
                    this.eliminarItem(id);
                    return;
                }

                try {
                    const response = await fetch('/carrito/actualizar/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ cantidad: cantidad })
                    });

                    const data = await response.json();
                    if (data.success) {
                        if (data.eliminado) {
                            delete this.items[id];
                            if (data.message) window.alertaAdvertencia(data.message);
                        } else if (this.items[id]) {
                            this.items[id].cantidad = data.item_cantidad !== undefined ? data.item_cantidad : cantidad;
                            if (data.stock_maximo !== undefined) {
                                this.items[id].stock = data.stock_maximo;
                            }
                            if (data.warning) {
                                window.alertaAdvertencia(data.warning);
                            }
                        }

                        this.subtotal = data.subtotal;
                        this.descuentoTotal = data.descuentoTotal;
                        this.costoEnvio = data.costoEnvio;
                        this.totalFinal = data.total;
                        this.totalItems = data.cart_count;

                        // Actualizar badge global
                        window.dispatchEvent(new CustomEvent('carrito-actualizado', { detail: { count: data.cart_count } }));
                    }
                } catch (error) {
                    console.error('Error al actualizar:', error);
                }
            },

            async eliminarItem(id) {
                const nombreItem = this.items[id]?.nombre || 'este producto';
                const confirmado = await window.alertaConfirmar({
                    titulo: '¿Eliminar producto?',
                    texto: `¿Deseas retirar "${nombreItem}" de tu carrito de compras?`,
                    icono: 'warning',
                    textoConfirmar: 'Sí, eliminar',
                    textoCancelar: 'Conservar',
                    esPeligroso: true
                });

                if (!confirmado) return;

                try {
                    const response = await fetch('/carrito/eliminar/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        delete this.items[id];
                        this.totalItems = data.cart_count;
                        this.recalcularTotales();

                        window.alertaToast('Producto retirado del carrito', 'info');

                        // Actualizar badge global
                        window.dispatchEvent(new CustomEvent('carrito-actualizado', { detail: { count: data.cart_count } }));
                    }
                } catch (error) {
                    console.error('Error al eliminar:', error);
                }
            },

            async vaciarCarrito() {
                const confirmado = await window.alertaConfirmar({
                    titulo: '¿Vaciar carrito de compras?',
                    texto: 'Se eliminarán todos los productos que tienes seleccionados actualmente.',
                    icono: 'warning',
                    textoConfirmar: 'Sí, vaciar todo',
                    textoCancelar: 'Cancelar',
                    esPeligroso: true
                });

                if (!confirmado) return;

                try {
                    const response = await fetch('/carrito/vaciar', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.items = {};
                        this.subtotal = 0;
                        this.descuentoTotal = 0;
                        this.costoEnvio = 0;
                        this.totalFinal = 0;
                        this.totalItems = 0;

                        window.alertaToast('El carrito ha sido vaciado correctamente', 'success');

                        // Actualizar badge global
                        window.dispatchEvent(new CustomEvent('carrito-actualizado', { detail: { count: 0 } }));
                    }
                } catch (error) {
                    console.error('Error al vaciar:', error);
                }
            },

            recalcularTotales() {
                let sub = 0;
                let desc = 0;
                let count = 0;

                Object.values(this.items).forEach(item => {
                    sub += item.precio * item.cantidad;
                    if (item.precio_oferta && item.precio_oferta < item.precio) {
                        desc += (item.precio - item.precio_oferta) * item.cantidad;
                    }
                    count += item.cantidad;
                });

                this.subtotal = sub;
                this.descuentoTotal = desc;
                this.totalItems = count;
                this.costoEnvio = (sub - desc > 150000 || count === 0) ? 0 : 12000;
                this.totalFinal = (sub - desc) + this.costoEnvio;
            }
        };
    }
</script>
@endsection
