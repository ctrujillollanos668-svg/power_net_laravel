@extends('layouts.tienda')

@section('title', $producto->nombre)

@section('content')

@php
    $precioFinal = $producto->ofertaActiva ? $producto->ofertaActiva->precio_oferta : $producto->precio;
    $tieneOferta = (bool)$producto->ofertaActiva;
    
    // Filtrar imágenes válidas
    $imagenesValidas = $producto->imagenes ? $producto->imagenes->filter(fn($i) => !empty($i->imagen)) : collect();
    $fotoPrincipal = $imagenesValidas->count() > 0 
        ? asset('imagenes_productos/' . $imagenesValidas->first()->imagen) 
        : '';
        
    $sku = 'PWN-' . str_pad($producto->id, 5, '0', STR_PAD_LEFT);
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 lg:py-8" x-data="{
    fotoActual: '{{ $fotoPrincipal }}',
    cantidad: 1,
    stockMax: {{ $producto->stock }},
    precioUnitario: {{ $precioFinal }},
    tabActiva: 'descripcion',
    modalZoom: false,
    skuCopiado: false,
    copiarSku() {
        navigator.clipboard.writeText('{{ $sku }}');
        this.skuCopiado = true;
        setTimeout(() => this.skuCopiado = false, 2000);
    },
    formatoMoneda(valor) {
        return '$' + new Intl.NumberFormat('es-CO').format(valor);
    }
}">

    {{-- ===== BREADCRUMB MINIMALISTA ===== --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 flex-wrap">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-yellow-600 font-medium transition flex items-center gap-1">
            <i class="fa-solid fa-house text-slate-400 text-[11px]"></i>
            <span>Inicio</span>
        </a>
        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
        <a href="{{ route('tienda.catalogo') }}" class="hover:text-yellow-600 font-medium transition">
            Catálogo
        </a>
        @if($producto->categoria)
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ route('tienda.categoria', $producto->categoria->id) }}" class="hover:text-yellow-600 font-medium transition">
                {{ $producto->categoria->nombre_categoria }}
            </a>
        @endif
        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
        <span class="text-slate-800 font-bold truncate max-w-xs sm:max-w-md">
            {{ $producto->nombre }}
        </span>
    </nav>

    {{-- ===== FICHA DEL PRODUCTO (BALANCEADA 50/50 Y PROPORCIONADA) ===== --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-center">

            {{-- ===== COLUMNA IZQUIERDA: FOTO PROPORCIONADA ===== --}}
            <div class="flex flex-col items-center justify-center space-y-4">
                
                {{-- Contenedor de la Imagen Principal con altura fija y balanceada --}}
                <div class="relative w-full max-w-md h-[360px] sm:h-[400px] bg-white rounded-2xl border border-slate-200/80 p-6 flex items-center justify-center group shadow-2xs"
                     x-data="{ liked: {{ ($esFavorito ?? false) ? 'true' : 'false' }} }">
                    
                    {{-- Badge de Descuento --}}
                    @if($tieneOferta)
                        <span class="absolute top-3 left-3 bg-red-600 text-white text-[11px] font-black uppercase px-2.5 py-1 rounded-lg shadow-sm z-10 flex items-center gap-1">
                            <i class="fa-solid fa-bolt text-yellow-300 text-[10px]"></i>
                            -{{ $producto->ofertaActiva->descuento }}% OFF
                        </span>
                    @endif

                    {{-- Botón Me Gusta --}}
                    <button
                        type="button"
                        @click="toggleFavoritoGlobal({{ $producto->id }}, data => liked = data.is_favorite)"
                        title="Guardar en favoritos"
                        class="absolute top-3 right-3 w-9 h-9 rounded-full bg-slate-50 hover:bg-white text-slate-400 hover:text-red-500 border border-slate-200/60 shadow-2xs flex items-center justify-center text-xs transition z-20 hover:scale-110 cursor-pointer">
                        <i :class="liked ? 'fa-solid fa-heart text-red-500 scale-110' : 'fa-regular fa-heart'" class="transition-transform duration-150"></i>
                    </button>

                    {{-- Botón Ampliar --}}
                    <template x-if="fotoActual">
                        <button
                            type="button"
                            @click="modalZoom = true"
                            title="Ampliar imagen"
                            class="absolute bottom-3 right-3 w-8 h-8 rounded-lg bg-white/90 hover:bg-white text-slate-600 shadow-2xs border border-slate-200 flex items-center justify-center text-xs transition opacity-0 group-hover:opacity-100 z-20 cursor-pointer">
                            <i class="fa-solid fa-expand"></i>
                        </button>
                    </template>

                    {{-- Imagen Activa --}}
                    <template x-if="fotoActual">
                        <img :src="fotoActual"
                             alt="{{ $producto->nombre }}"
                             @click="modalZoom = true"
                             class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-102 cursor-zoom-in select-none">
                    </template>

                    {{-- Fallback Sin Imagen --}}
                    <template x-if="!fotoActual">
                        <div class="flex flex-col items-center justify-center text-slate-300 select-none py-12">
                            <i class="fa-solid fa-image text-4xl text-slate-300 mb-2"></i>
                            <span class="text-xs font-semibold text-slate-400">Sin imagen disponible</span>
                        </div>
                    </template>
                </div>

                {{-- Miniaturas horizontales solo si hay más de 1 imagen --}}
                @if($imagenesValidas->count() > 1)
                    <div class="flex items-center gap-2.5 overflow-x-auto max-w-md w-full py-1">
                        @foreach($imagenesValidas as $img)
                            @php
                                $imgUrl = asset('imagenes_productos/' . $img->imagen);
                            @endphp
                            <button
                                type="button"
                                @click="fotoActual = '{{ $imgUrl }}'"
                                :class="fotoActual === '{{ $imgUrl }}' ? 'border-yellow-400 ring-2 ring-yellow-400/40' : 'border-slate-200 hover:border-slate-300 opacity-70 hover:opacity-100'"
                                class="w-14 h-14 rounded-xl border-2 overflow-hidden bg-white shrink-0 transition-all p-1 flex items-center justify-center cursor-pointer">
                                <img src="{{ $imgUrl }}" alt="Miniatura" class="max-h-full max-w-full object-contain rounded-lg">
                            </button>
                        @endforeach
                    </div>
                @endif

            </div>

            {{-- ===== COLUMNA DERECHA: INFORMACIÓN Y ACCIONES ===== --}}
            <div class="flex flex-col justify-center space-y-5">
                
                <div>
                    {{-- Categoría, Proveedor & SKU --}}
                    <div class="flex items-center justify-between gap-2 flex-wrap mb-1.5">
                        <div class="flex items-center gap-2 text-xs">
                            @if($producto->categoria)
                                <a href="{{ route('tienda.categoria', $producto->categoria->id) }}" 
                                   class="font-bold text-amber-600 hover:text-amber-700 uppercase tracking-wide">
                                    {{ $producto->categoria->nombre_categoria }}
                                </a>
                            @endif
                            @if($producto->proveedor)
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-500">
                                    {{ $producto->proveedor->nombre_proveedor }}
                                </span>
                            @endif
                        </div>

                        {{-- SKU --}}
                        <button type="button" 
                                @click="copiarSku()"
                                class="text-[11px] font-mono text-slate-400 hover:text-slate-600 bg-slate-100 px-2 py-0.5 rounded transition cursor-pointer flex items-center gap-1"
                                title="Copiar referencia">
                            <span x-text="skuCopiado ? '¡Copiado!' : '{{ $sku }}'"></span>
                            <i :class="skuCopiado ? 'fa-solid fa-check text-emerald-600' : 'fa-regular fa-copy'"></i>
                        </button>
                    </div>

                    {{-- Título Principal --}}
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight capitalize leading-tight mb-2">
                        {{ $producto->nombre }}
                    </h1>

                    {{-- Estrellas y Badge --}}
                    <div class="flex items-center gap-2 text-xs mb-4">
                        <div class="flex items-center text-amber-400 text-xs">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <span class="font-bold text-slate-700">5.0</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-emerald-700 font-semibold text-[11px]">
                            <i class="fa-solid fa-shield-check mr-0.5"></i> Producto Garantizado
                        </span>
                    </div>

                    {{-- ===== PRECIO ===== --}}
                    <div class="py-3 border-y border-slate-100">
                        @if($tieneOferta)
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm text-slate-400 line-through">
                                    ${{ number_format($producto->precio, 0, ',', '.') }}
                                </span>
                                <span class="text-xs font-black text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded">
                                    Ahorras ${{ number_format($producto->precio - $producto->ofertaActiva->precio_oferta, 0, ',', '.') }} (-{{ $producto->ofertaActiva->descuento }}%)
                                </span>
                            </div>
                            <div class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                                ${{ number_format($producto->ofertaActiva->precio_oferta, 0, ',', '.') }}
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                IVA incluido • Facturación electrónica
                            </p>
                        @else
                            <div class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                                ${{ number_format($producto->precio, 0, ',', '.') }}
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Precio con IVA incluido • Factura legal
                            </p>
                        @endif
                    </div>

                    {{-- Estado de Stock --}}
                    <div class="flex items-center justify-between gap-2 my-4">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-bold text-slate-800">Disponibilidad:</span>
                            @if($producto->stock > 0)
                                <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 rounded-full text-xs">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                    En stock ({{ $producto->stock }} unidades)
                                </span>
                            @else
                                <span class="font-bold text-red-600 bg-red-50 px-2.5 py-0.5 rounded-full text-xs">Agotado temporalmente</span>
                            @endif
                        </div>

                        @if($producto->stock > 0 && $producto->stock <= 5)
                            <span class="text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">
                                ¡Pocas unidades!
                            </span>
                        @endif
                    </div>

                    {{-- ===== CANTIDAD Y BOTONES DE COMPRA ===== --}}
                    @if($producto->stock > 0)
                        <div class="space-y-3.5 pt-1">
                            
                            {{-- Selector de Cantidad --}}
                            <div class="flex items-center justify-between bg-slate-50/80 p-3 rounded-2xl border border-slate-200/60">
                                <span class="text-xs font-bold text-slate-700">Cantidad:</span>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center border border-slate-300 rounded-xl overflow-hidden bg-white">
                                        <button
                                            type="button"
                                            @click="if(cantidad > 1) cantidad--"
                                            :disabled="cantidad <= 1"
                                            class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 font-bold disabled:opacity-30 cursor-pointer">
                                            -
                                        </button>
                                        <span class="w-8 text-center text-xs font-bold text-slate-900 select-none" x-text="cantidad"></span>
                                        <button
                                            type="button"
                                            @click="if(cantidad < stockMax) cantidad++"
                                            :disabled="cantidad >= stockMax"
                                            class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 font-bold disabled:opacity-30 cursor-pointer">
                                            +
                                        </button>
                                    </div>
                                    <span class="text-xs text-slate-500 font-medium" x-show="cantidad > 1">
                                        Total: <strong class="text-slate-900" x-text="formatoMoneda(cantidad * precioUnitario)"></strong>
                                    </span>
                                </div>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <button
                                    type="button"
                                    @click="agregarAlCarritoGlobal({{ $producto->id }}, cantidad)"
                                    class="w-full bg-yellow-400 hover:bg-yellow-500 text-slate-900 font-extrabold py-3.5 px-4 rounded-2xl transition shadow-2xs hover:shadow-sm flex items-center justify-center gap-2 text-sm cursor-pointer">
                                    <i class="fa-solid fa-cart-plus text-xs"></i>
                                    <span>Agregar al carrito</span>
                                </button>
                                
                                <button
                                    type="button"
                                    @click="comprarProductoGlobal({{ $producto->id }}, cantidad)"
                                    class="w-full bg-[#0b1220] hover:bg-black text-white font-bold py-3.5 px-4 rounded-2xl transition shadow-sm flex items-center justify-center gap-2 text-sm cursor-pointer">
                                    <i class="fa-solid fa-bolt text-yellow-400 text-xs"></i>
                                    <span>Comprar ahora</span>
                                </button>
                            </div>

                            {{-- WhatsApp --}}
                            <div class="text-center pt-1">
                                <a href="https://wa.me/573000000000?text={{ urlencode('Hola PowerNet, deseo asesoría sobre: ' . $producto->nombre . ' (' . $sku . ')') }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:text-emerald-800 transition">
                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                                    <span>¿Dudas? Consulta con un asesor por WhatsApp</span>
                                </a>
                            </div>

                        </div>
                    @endif

                </div>

            </div>

        </div>

        {{-- ===== PESTAÑAS DE DETALLES ===== --}}
        <div class="mt-10 pt-6 border-t border-slate-100">
            
            {{-- Barra de Pestañas --}}
            <div class="flex items-center gap-6 border-b border-slate-200 pb-px overflow-x-auto">
                <button
                    type="button"
                    @click="tabActiva = 'descripcion'"
                    :class="tabActiva === 'descripcion' ? 'border-amber-400 text-slate-900 font-extrabold' : 'border-transparent text-slate-400 hover:text-slate-700 font-medium'"
                    class="pb-3 border-b-2 text-sm transition cursor-pointer whitespace-nowrap">
                    Descripción del producto
                </button>
                <button
                    type="button"
                    @click="tabActiva = 'especificaciones'"
                    :class="tabActiva === 'especificaciones' ? 'border-amber-400 text-slate-900 font-extrabold' : 'border-transparent text-slate-400 hover:text-slate-700 font-medium'"
                    class="pb-3 border-b-2 text-sm transition cursor-pointer whitespace-nowrap">
                    Ficha Técnica
                </button>
                <button
                    type="button"
                    @click="tabActiva = 'garantia'"
                    :class="tabActiva === 'garantia' ? 'border-amber-400 text-slate-900 font-extrabold' : 'border-transparent text-slate-400 hover:text-slate-700 font-medium'"
                    class="pb-3 border-b-2 text-sm transition cursor-pointer whitespace-nowrap">
                    Garantía
                </button>
            </div>

            {{-- Tab 1: Descripción --}}
            <div x-show="tabActiva === 'descripcion'" x-cloak class="pt-5">
                <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-200/60 max-w-3xl">
                    @if($producto->descripcion)
                        <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                            {{ $producto->descripcion }}
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">No hay descripción disponible para este producto.</p>
                    @endif
                </div>
            </div>

            {{-- Tab 2: Ficha Técnica --}}
            <div x-show="tabActiva === 'especificaciones'" x-cloak class="pt-5 max-w-xl">
                <div class="border border-slate-200 rounded-2xl overflow-hidden divide-y divide-slate-100 text-xs sm:text-sm">
                    <div class="flex justify-between p-3 bg-slate-50/60">
                        <span class="font-medium text-slate-500">Referencia SKU</span>
                        <span class="font-bold font-mono text-slate-800">{{ $sku }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-white">
                        <span class="font-medium text-slate-500">Categoría</span>
                        <span class="font-bold text-slate-800">{{ $producto->categoria->nombre_categoria ?? 'General' }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-slate-50/60">
                        <span class="font-medium text-slate-500">Proveedor</span>
                        <span class="font-bold text-slate-800">{{ $producto->proveedor->nombre_proveedor ?? 'PowerNet Certificado' }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-white">
                        <span class="font-medium text-slate-500">Estado</span>
                        <span class="font-bold text-emerald-700">Nuevo - Original</span>
                    </div>
                </div>
            </div>

            {{-- Tab 3: Garantía --}}
            <div x-show="tabActiva === 'garantia'" x-cloak class="pt-5 max-w-2xl">
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/80 text-xs sm:text-sm text-slate-600 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-slate-900">
                        <i class="fa-solid fa-award text-amber-500"></i>
                        <span>Respaldo y Garantía PowerNet</span>
                    </div>
                    <p class="leading-relaxed">
                        Cuentas con respaldo oficial de fábrica contra defectos de fabricación con cambio o asistencia prioritaria.
                    </p>
                </div>
            </div>

        </div>

    </div>

    {{-- ===== MODAL LIGHTBOX PARA ZOOM ===== --}}
    <div x-show="modalZoom" 
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="modalZoom = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-xs p-4">
        
        <button type="button" 
                @click="modalZoom = false" 
                class="absolute top-6 right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg transition z-50 cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="relative max-w-3xl max-h-[80vh] p-4 flex items-center justify-center" @click.outside="modalZoom = false">
            <img :src="fotoActual" alt="{{ $producto->nombre }}" class="max-w-full max-h-[75vh] object-contain rounded-2xl shadow-2xl">
        </div>
    </div>

    {{-- ===== PRODUCTOS RELACIONADOS ===== --}}
    @if(isset($relacionados) && $relacionados->count() > 0)
        <div class="mt-10">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                        Productos Relacionados
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Podría interesarte también</p>
                </div>
                <a href="{{ route('tienda.categoria', $producto->categoria_id) }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                    <span>Ver más</span>
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($relacionados as $rel)
                    @php
                        $relValidas = $rel->imagenes ? $rel->imagenes->filter(fn($i) => !empty($i->imagen)) : collect();
                        $relFoto = $relValidas->count() > 0 ? $relValidas->first()->imagen : null;
                        $esFav = in_array($rel->id, $favoritosIds ?? []);
                        $relTieneOferta = (bool)$rel->ofertaActiva;
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 hover:shadow-md hover:border-amber-300 transition-all flex flex-col justify-between group relative" 
                         x-data="{ liked: {{ $esFav ? 'true' : 'false' }} }">
                        
                        {{-- Botón Favorito --}}
                        <button
                            type="button"
                            @click.stop.prevent="toggleFavoritoGlobal({{ $rel->id }}, data => liked = data.is_favorite)"
                            title="Me encanta"
                            class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/90 text-slate-400 hover:text-red-500 shadow-2xs flex items-center justify-center text-xs transition z-20 hover:scale-110 cursor-pointer">
                            <i :class="liked ? 'fa-solid fa-heart text-red-500 scale-110' : 'fa-regular fa-heart'" class="transition-transform duration-150"></i>
                        </button>

                        <div>
                            {{-- Imagen --}}
                            <a href="{{ route('tienda.detalle', $rel->id) }}" class="block aspect-square bg-white rounded-xl overflow-hidden mb-3 p-3 flex items-center justify-center border border-slate-100">
                                @if($relFoto)
                                    <img src="{{ asset('imagenes_productos/' . $relFoto) }}" alt="{{ $rel->nombre }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <i class="fa-solid fa-image text-3xl"></i>
                                    </div>
                                @endif
                            </a>

                            {{-- Categoría y Nombre --}}
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">
                                {{ $rel->categoria->nombre_categoria ?? 'General' }}
                            </span>
                            <a href="{{ route('tienda.detalle', $rel->id) }}" class="block text-xs sm:text-sm font-bold text-slate-800 line-clamp-2 hover:text-amber-600 mt-1 leading-snug">
                                {{ $rel->nombre }}
                            </a>
                        </div>

                        {{-- Precios y Botón Ver --}}
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="mb-2">
                                @if($relTieneOferta)
                                    <span class="text-base font-black text-red-600">
                                        ${{ number_format($rel->ofertaActiva->precio_oferta, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-slate-400 line-through ml-1">
                                        ${{ number_format($rel->precio, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-base font-black text-slate-900">
                                        ${{ number_format($rel->precio, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('tienda.detalle', $rel->id) }}" 
                               class="w-full bg-slate-100 hover:bg-yellow-400 hover:text-slate-900 text-slate-800 font-bold text-xs py-2 rounded-xl text-center transition flex items-center justify-center gap-1.5">
                                <span>Ver detalle</span>
                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

@endsection
