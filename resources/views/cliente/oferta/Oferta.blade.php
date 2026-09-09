@extends('layouts.tienda')

@section('title', 'Ofertas y Promociones')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-yellow-600">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
        <span class="text-gray-900 font-bold">Ofertas y Promociones</span>
    </nav>

    {{-- Banner de Ofertas --}}
    <div class="bg-gradient-to-r from-[#0b1220] via-red-950 to-[#0b1220] rounded-3xl p-8 lg:p-12 text-white shadow-xl mb-10 relative overflow-hidden border border-red-900/40">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-2 bg-red-600/90 text-white text-xs font-black uppercase px-4 py-1.5 rounded-full mb-4 shadow-xs">
                <i class="fa-solid fa-fire text-yellow-300"></i>
                Precios Especiales de Temporada
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight mb-3">
                Grandes Descuentos en Material Eléctrico
            </h1>
            <p class="text-gray-300 text-sm sm:text-base leading-relaxed">
                Aprovecha nuestras promociones exclusivas con hasta el 50% de descuento en bombillos, luminarias y suministros de primera calidad.
            </p>
        </div>

        <div class="absolute right-6 -bottom-6 text-9xl text-white/5 font-black select-none pointer-events-none">
            %
        </div>
    </div>

    {{-- Grid de Ofertas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($ofertas as $oferta)
            @php
                $producto = $oferta->producto;
                $foto = $producto && $producto->imagenes && $producto->imagenes->count() > 0 ? $producto->imagenes->first()->imagen : null;
                $esFav = $producto ? in_array($producto->id, $favoritosIds ?? []) : false;
                $stockTotal = $producto ? ($producto->stock ?? 10) : 0;
            @endphp            @if($producto)
                <div class="bg-white rounded-[28px] border border-slate-200/70 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-4 sm:p-5 flex flex-col justify-between group relative" 
                     x-data="{ liked: {{ $esFav ? 'true' : 'false' }}, qty: 1, maxStock: {{ $stockTotal > 0 ? $stockTotal : 1 }} }">
                    
                    {{-- Parte Superior (Imagen, Badges, Info, Precio y Cantidad) --}}
                    <div>
                        {{-- Imagen del Producto con Badges --}}
                        <div class="relative w-full aspect-[4/3] rounded-[22px] bg-slate-50 border border-slate-100/80 flex items-center justify-center p-0 mb-3.5 overflow-hidden group-hover:shadow-md transition">
                            
                            {{-- Badge de Oferta (Esquina Superior Izquierda) --}}
                            <span class="absolute top-2.5 left-2.5 bg-[#ef4444] text-white text-[11px] font-bold uppercase px-2.5 py-1 rounded-lg z-20 shadow-sm flex items-center gap-1.5">
                                <i class="fa-solid fa-tag text-[10px]"></i>
                                <span>-{{ $oferta->descuento }}%</span>
                            </span>

                            {{-- Botón Me Encanta (Corazón) (Esquina Superior Derecha) --}}
                            <button
                                type="button"
                                @click.stop.prevent="toggleFavoritoGlobal({{ $producto->id }}, data => liked = data.is_favorite)"
                                title="Me encanta"
                                class="absolute top-2.5 right-2.5 w-9 h-9 rounded-full bg-white/90 backdrop-blur-xs shadow-sm border border-slate-100/80 flex items-center justify-center text-slate-400 hover:text-rose-500 transition z-20 hover:scale-110 cursor-pointer">
                                <i :class="liked ? 'fa-solid fa-heart text-rose-500 scale-110' : 'fa-regular fa-heart text-slate-400'" class="text-sm transition-transform duration-150"></i>
                            </button>

                            {{-- Imagen --}}
                            <a href="{{ route('tienda.detalle', $producto->id) }}" class="w-full h-full flex items-center justify-center">
                                @if($foto)
                                    <img
                                        src="{{ asset('imagenes_productos/' . $foto) }}"
                                        alt="{{ $producto->nombre }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-400 p-4">
                                        <i class="fa-solid fa-bolt text-4xl text-amber-400 mb-1"></i>
                                        <span class="text-[10px] font-bold uppercase tracking-wider">PowerNet</span>
                                    </div>
                                @endif
                            </a>

                            {{-- Píldora de Categoría (Esquina Inferior Izquierda sobre la foto) --}}
                            <div class="absolute bottom-2.5 left-2.5 z-20">
                                <span class="bg-slate-950/85 backdrop-blur-md text-white font-bold text-[10px] tracking-wider uppercase px-2.5 py-1 rounded-lg border border-white/10 shadow-xs">
                                    {{ $producto->categoria->nombre ?? 'BOMBILLO LED' }}
                                </span>
                            </div>
                        </div>

                        {{-- Fila: Marca y Código PN --}}
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs font-bold text-slate-500">PowerNet</span>
                            <span class="text-[11px] font-mono font-medium text-slate-400">PN-{{ str_pad($producto->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        {{-- Nombre del Producto --}}
                        <div class="mb-1">
                            <a href="{{ route('tienda.detalle', $producto->id) }}" class="block text-base font-black text-slate-900 line-clamp-1 hover:text-[#7c3aed] transition leading-tight" title="{{ $producto->nombre }}">
                                {{ $producto->nombre }}
                            </a>
                        </div>

                        {{-- Descripción corta --}}
                        <p class="text-xs text-slate-400 font-normal line-clamp-2 leading-relaxed mb-3 min-h-[34px]">
                            {{ $producto->descripcion ?? 'Iluminación y bombillos de alta durabilidad y eficiencia energética' }}
                        </p>

                        {{-- Fila de Precio y Stock --}}
                        <div class="mb-3 flex items-center justify-between gap-2">
                            {{-- Precio --}}
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                    ${{ number_format($oferta->precio_oferta, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-slate-400 line-through font-semibold">
                                    ${{ number_format($producto->precio, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Badge de Stock a la derecha --}}
                            <div>
                                @if($stockTotal > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-[#ecfdf5] text-[#059669] border border-emerald-200/60">
                                        Stock: {{ $stockTotal }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-[#fef2f2] text-[#e11d48] border border-rose-200/60">
                                        Agotado
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Selector de Cantidad (- 1 +) en su propia fila independiente --}}
                        <div class="mb-3.5">
                            <div class="bg-[#f1f5f9] rounded-full px-3 py-1 flex items-center justify-between w-32 border border-slate-200/40">
                                <button
                                    type="button"
                                    @click="if(qty > 1) qty--"
                                    class="w-6 h-6 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 transition cursor-pointer">
                                    <i class="fa-solid fa-minus text-[10px]"></i>
                                </button>
                                <span class="font-bold text-sm text-slate-900 select-none px-2" x-text="qty"></span>
                                <button
                                    type="button"
                                    @click="if(qty < maxStock) qty++"
                                    class="w-6 h-6 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 transition cursor-pointer">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Fila Inferior de Botones de Acción (Comprar / Sin Stock + Carrito) --}}
                    <div class="flex items-center gap-2.5">
                        @if($stockTotal > 0)
                            <button
                               type="button"
                               @click="comprarProductoGlobal({{ $producto->id }}, qty)"
                               class="flex-1 bg-black hover:bg-slate-800 text-white font-black text-sm py-3 px-4 rounded-2xl text-center transition shadow-sm hover:shadow cursor-pointer flex items-center justify-center">
                                Comprar
                            </button>
                            <button
                               type="button"
                               @click="agregarAlCarritoGlobal({{ $producto->id }}, qty)"
                               title="Añadir al carrito"
                               class="w-12 h-12 rounded-2xl bg-[#f1f5f9] hover:bg-[#e2e8f0] text-slate-600 hover:text-slate-900 flex items-center justify-center transition shrink-0 cursor-pointer">
                                <i class="fa-solid fa-cart-shopping text-sm"></i>
                            </button>
                        @else
                            <button
                               type="button"
                               disabled
                               class="flex-1 bg-[#737373] text-white font-bold text-sm py-3 px-4 rounded-2xl text-center cursor-not-allowed flex items-center justify-center">
                                Sin Stock
                            </button>
                            <button
                               type="button"
                               disabled
                               class="w-12 h-12 rounded-2xl bg-[#f8fafc] text-slate-300 flex items-center justify-center shrink-0 cursor-not-allowed">
                                <i class="fa-solid fa-cart-shopping text-sm"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full bg-white rounded-3xl p-16 text-center text-gray-400 border border-gray-200">
                <i class="fa-solid fa-tags text-5xl text-gray-300 mb-3"></i>
                <h3 class="text-base font-bold text-gray-700">No hay ofertas activas en este momento</h3>
                <p class="text-xs text-gray-400 mt-1">Vuelve pronto para descubrir nuevas promociones y descuentos.</p>
                <a href="{{ route('tienda.catalogo') }}" class="inline-block mt-4 px-6 py-2.5 bg-yellow-400 text-gray-900 font-bold text-xs rounded-xl hover:bg-yellow-500 transition">
                    Explorar catálogo general
                </a>
            </div>
        @endforelse
    </div>

    {{-- Paginación si aplica --}}
    <div class="mt-8">
        {{ $ofertas->links() }}
    </div>

</div>

@endsection
