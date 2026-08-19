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
            <div class="bg-white rounded-[28px] border border-slate-100/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col justify-between group relative" 
                 x-data="{ qty: 1, maxStock: {{ $stockTotal > 0 ? $stockTotal : 1 }} }">
                
                {{-- Parte Superior (Imagen y Badges) --}}
                <div>
                    <div class="relative w-full aspect-square bg-transparent flex items-center justify-center p-2 mb-3 overflow-hidden">
                        
                        {{-- Botón Quitar de Favoritos --}}
                        <form action="{{ route('favoritos.destroy', $product->id) }}" method="POST" class="absolute top-2 left-2 z-20">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                title="Quitar de favoritos"
                                class="w-9 h-9 rounded-full bg-white shadow-sm border border-slate-100/90 flex items-center justify-center text-rose-500 hover:bg-rose-500 hover:text-white transition hover:scale-110 cursor-pointer">
                                <i class="fa-solid fa-heart text-sm"></i>
                            </button>
                        </form>

                        {{-- Badge de Oferta --}}
                        @if($tieneOferta)
                            <span class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-black uppercase px-2.5 py-1 rounded-full z-10 shadow-sm">
                                -{{ $product->ofertaActiva->descuento }}%
                            </span>
                        @endif

                        <a href="{{ route('tienda.detalle', $product->id) }}" class="w-full h-full flex items-center justify-center">
                            @if($foto)
                                <img
                                    src="{{ asset('imagenes_productos/' . $foto) }}"
                                    alt="{{ $product->nombre }}"
                                    class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="flex flex-col items-center justify-center text-slate-300">
                                    <i class="fa-solid fa-bolt text-4xl text-amber-400/60 mb-1"></i>
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">PowerNet</span>
                                </div>
                            @endif
                        </a>
                    </div>

                    {{-- Nombre del Producto --}}
                    <div class="mb-1">
                        <a href="{{ route('tienda.detalle', $product->id) }}" class="block text-base font-extrabold text-slate-900 line-clamp-1 hover:text-[#7c3aed] transition leading-tight" title="{{ $product->nombre }}">
                            {{ $product->nombre }}
                        </a>
                    </div>

                    {{-- Precio en Negro --}}
                    <div class="mt-1 mb-3.5 flex items-baseline gap-2">
                        @if($tieneOferta)
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                ${{ number_format($product->ofertaActiva->precio_oferta, 0, ',', '.') }}
                            </span>
                            <span class="text-xs text-slate-400 line-through font-bold">
                                ${{ number_format($product->precio, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                ${{ number_format($product->precio, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    {{-- Selector de Cantidad (- 1 +) --}}
                    <div class="bg-[#f1f5f9] rounded-2xl p-1 flex items-center justify-between w-32 mb-4">
                        <button
                            type="button"
                            @click="if(qty > 1) qty--"
                            class="w-8 h-8 rounded-xl bg-white shadow-xs hover:bg-slate-50 text-slate-800 font-black text-sm flex items-center justify-center transition cursor-pointer">
                            <i class="fa-solid fa-minus text-[10px]"></i>
                        </button>
                        <span class="font-black text-sm text-slate-900 select-none px-2" x-text="qty"></span>
                        <button
                            type="button"
                            @click="if(qty < maxStock) qty++"
                            class="w-8 h-8 rounded-xl bg-white shadow-xs hover:bg-slate-50 text-slate-800 font-black text-sm flex items-center justify-center transition cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                        </button>
                    </div>
                </div>

                {{-- Parte Inferior (Botón Comprar, Icono Carrito y Stock) --}}
                <div>
                    <div class="flex items-center gap-2 mb-2.5">
                        <button
                           type="button"
                           @click="comprarProductoGlobal({{ $product->id }}, qty)"
                           class="flex-1 bg-[#111827] hover:bg-black text-white font-black text-sm py-3 px-5 rounded-2xl text-center transition shadow-md shadow-slate-900/10 cursor-pointer flex items-center justify-center">
                            Comprar
                        </button>
                        <button
                           type="button"
                           @click="agregarAlCarritoGlobal({{ $product->id }}, qty)"
                           title="Añadir al carrito"
                           class="w-12 h-12 rounded-2xl bg-[#f1f5f9] hover:bg-[#e2e8f0] text-slate-700 flex items-center justify-center transition shrink-0 shadow-2xs group/cart cursor-pointer">
                            <i class="fa-solid fa-cart-shopping text-sm text-slate-500 group-hover/cart:text-slate-900 transition"></i>
                        </button>
                    </div>

                    {{-- Disponibilidad en Stock (Verde) --}}
                    <div class="text-xs font-bold {{ $stockTotal > 0 ? 'text-[#10b981]' : 'text-rose-500' }}">
                        @if($stockTotal > 0)
                            <span>{{ $stockTotal }} disponibles</span>
                        @else
                            <span>Agotado</span>
                        @endif
                    </div>
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
