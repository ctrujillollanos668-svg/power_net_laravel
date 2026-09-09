@extends('layouts.tienda')

@section('title', 'Mis Favoritos')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-yellow-600">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
        <span class="text-gray-900 font-bold">Mis Favoritos</span>
    </nav>

    {{-- Encabezado --}}
    <div class="bg-white rounded-3xl shadow-xs border border-gray-200/80 p-6 sm:p-8 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 flex items-center gap-2.5">
                <span class="text-red-500">❤️</span>
                Mis Productos Favoritos
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Aquí encuentras todos los artículos que te encantan y guardaste para comprar después.
            </p>
        </div>

        <div>
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 text-red-700 text-xs font-bold border border-red-200">
                <i class="fa-solid fa-heart text-red-500"></i>
                <span>{{ $totalFavoritos }} {{ $totalFavoritos === 1 ? 'producto guardado' : 'productos guardados' }}</span>
            </span>
        </div>
    </div>

    {{-- Alerta de éxito --}}
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3 text-emerald-800 text-xs font-bold shadow-2xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Grid de Favoritos --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($favoritos as $product)
            @php
                $foto = $product->imagenes && $product->imagenes->count() > 0 ? $product->imagenes->first()->imagen : null;
                $tieneOferta = $product->ofertaActiva;
                $stockTotal = $product->stock ?? 10;
            @endphp
            <div class="bg-white rounded-[2rem] border border-slate-100/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-4 sm:p-5 flex flex-col justify-between group relative" 
                 x-data="{ qty: 1, maxStock: {{ $stockTotal > 0 ? $stockTotal : 1 }} }">
                
                {{-- Parte Superior (Imagen, Badges y Categoría) --}}
                <div>
                    <div class="relative w-full aspect-[4/3] rounded-2xl bg-slate-900 flex items-center justify-center p-3 mb-3.5 overflow-hidden group-hover:shadow-md transition">
                        
                        {{-- Badge de Oferta (Esquina Superior Izquierda) --}}
                        @if($tieneOferta)
                            <span class="absolute top-2.5 left-2.5 bg-[#ef4444] text-white text-[11px] font-black uppercase px-2.5 py-1 rounded-lg z-20 shadow-sm flex items-center gap-1.5">
                                <i class="fa-solid fa-tag text-[10px]"></i>
                                <span>Oferta</span>
                            </span>
                        @endif

                        {{-- Botón Quitar de Favoritos (Esquina Superior Derecha) --}}
                        <form action="{{ route('favoritos.destroy', $product->id) }}" method="POST" class="absolute top-2.5 right-2.5 z-20">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                title="Quitar de favoritos"
                                class="w-9 h-9 rounded-full bg-white/90 backdrop-blur-xs shadow-sm border border-slate-100/80 flex items-center justify-center text-rose-500 hover:bg-rose-500 hover:text-white transition hover:scale-110 cursor-pointer">
                                <i class="fa-solid fa-heart text-sm"></i>
                            </button>
                        </form>

                        {{-- Imagen del Producto --}}
                        <a href="{{ route('tienda.detalle', $product->id) }}" class="w-full h-full flex items-center justify-center">
                            @if($foto)
                                <img
                                    src="{{ asset('imagenes_productos/' . $foto) }}"
                                    alt="{{ $product->nombre }}"
                                    class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-bolt text-4xl text-amber-400 mb-1"></i>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">PowerNet</span>
                                </div>
                            @endif
                        </a>

                        {{-- Píldora de Categoría (Esquina Inferior Izquierda sobre la foto) --}}
                        <div class="absolute bottom-2.5 left-2.5 z-20">
                            <span class="bg-slate-950/80 backdrop-blur-md text-white font-black text-[10px] tracking-wider uppercase px-2.5 py-1 rounded-lg border border-white/10 shadow-xs">
                                {{ $product->categoria->nombre ?? 'BOMBILLO LED' }}
                            </span>
                        </div>
                    </div>

                    {{-- Fila: Marca y Código PN --}}
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-xs font-bold text-slate-500">PowerNet</span>
                        <span class="text-[10px] font-mono font-bold text-slate-400">PN-{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    {{-- Nombre del Producto --}}
                    <div class="mb-1">
                        <a href="{{ route('tienda.detalle', $product->id) }}" class="block text-base font-black text-slate-900 line-clamp-1 hover:text-[#7c3aed] transition leading-tight" title="{{ $product->nombre }}">
                            {{ $product->nombre }}
                        </a>
                    </div>

                    {{-- Descripción corta --}}
                    <p class="text-xs text-slate-400 font-medium line-clamp-2 leading-relaxed mb-3 h-8">
                        {{ $product->descripcion ?? 'Iluminación y bombillos de alta durabilidad y eficiencia energética' }}
                    </p>

                    {{-- Fila de Precio y Stock --}}
                    <div class="mb-4 flex items-center justify-between gap-2">
                        {{-- Precio --}}
                        <div class="flex items-baseline gap-1.5">
                            @if($tieneOferta)
                                <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                    ${{ number_format($product->ofertaActiva->precio_oferta, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-slate-400 line-through font-bold">
                                    ${{ number_format($product->precio, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                    ${{ number_format($product->precio, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>

                        {{-- Badge de Stock a la derecha --}}
                        <div>
                            @if($stockTotal > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/80">
                                    Stock: {{ $stockTotal }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-500 border border-rose-200/80">
                                    Agotado
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Fila Inferior de Controles (Selector Cantidad, Botón Comprar, Botón Carrito) --}}
                <div class="pt-2 flex items-center gap-2">
                    {{-- Selector de Cantidad (- 1 +) --}}
                    <div class="bg-slate-100 rounded-full p-1 flex items-center justify-between w-28 shrink-0">
                        <button
                            type="button"
                            @click="if(qty > 1) qty--"
                            class="w-7 h-7 rounded-full bg-transparent hover:bg-white text-slate-500 hover:text-slate-800 font-black text-xs flex items-center justify-center transition cursor-pointer">
                            <i class="fa-solid fa-minus text-[9px]"></i>
                        </button>
                        <span class="font-black text-xs text-slate-800 select-none px-1" x-text="qty"></span>
                        <button
                            type="button"
                            @click="if(qty < maxStock) qty++"
                            class="w-7 h-7 rounded-full bg-transparent hover:bg-white text-slate-500 hover:text-slate-800 font-black text-xs flex items-center justify-center transition cursor-pointer">
                            <i class="fa-solid fa-plus text-[9px]"></i>
                        </button>
                    </div>

                    {{-- Botón Comprar / Sin Stock --}}
                    @if($stockTotal > 0)
                        <button
                           type="button"
                           @click="comprarProductoGlobal({{ $product->id }}, qty)"
                           class="flex-1 bg-black hover:bg-slate-800 text-white font-black text-xs py-3 px-3 rounded-2xl text-center transition shadow-xs cursor-pointer flex items-center justify-center">
                            Comprar
                        </button>
                        <button
                           type="button"
                           @click="agregarAlCarritoGlobal({{ $product->id }}, qty)"
                           title="Añadir al carrito"
                           class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition shrink-0 cursor-pointer">
                            <i class="fa-solid fa-cart-shopping text-xs text-slate-600"></i>
                        </button>
                    @else
                        <button
                           type="button"
                           disabled
                           class="flex-1 bg-slate-500 text-white font-bold text-xs py-3 px-3 rounded-2xl text-center cursor-not-allowed flex items-center justify-center">
                            Sin Stock
                        </button>
                        <button
                           type="button"
                           disabled
                           class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center shrink-0 cursor-not-allowed">
                            <i class="fa-solid fa-cart-shopping text-xs"></i>
                        </button>
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-16 text-center text-gray-400 border border-gray-200 shadow-xs">
                <div class="w-20 h-20 mx-auto rounded-full bg-red-50 text-red-400 flex items-center justify-center text-4xl mb-4">
                    ❤️
                </div>
                <h3 class="text-lg font-bold text-gray-800">Aún no tienes productos favoritos</h3>
                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto leading-relaxed">
                    Navega por la tienda y haz clic en el icono del corazón en los productos que te gusten para tenerlos siempre a mano.
                </p>
                <a href="{{ route('tienda.catalogo') }}" class="inline-block mt-5 px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-extrabold text-xs rounded-xl transition shadow-xs">
                    Explorar catálogo ahora
                </a>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    <div class="mt-8">
        {{ $favoritos->links() }}
    </div>

</div>

@endsection
