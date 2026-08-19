@extends('layouts.tienda')

@section('title', isset($categoriaActual) ? $categoriaActual->nombre_categoria : 'Catálogo')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-yellow-600">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
        <a href="{{ route('tienda.catalogo') }}" class="hover:text-yellow-600">Catálogo</a>
        @if(isset($categoriaActual))
            <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
            <span class="text-gray-900 font-bold">{{ $categoriaActual->nombre_categoria }}</span>
        @endif
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ===== SIDEBAR DE FILTROS ===== --}}
        <aside class="w-full lg:w-64 shrink-0">
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-5 space-y-6 sticky top-20">

                {{-- Encabezado Filtros --}}
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="font-extrabold text-sm text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-filter text-yellow-500 text-xs"></i>
                        Filtros
                    </h3>
                    @if(request()->hasAny(['categoria', 'q', 'precio_min', 'precio_max', 'orden']))
                        <a href="{{ route('tienda.catalogo') }}" class="text-[11px] font-bold text-red-500 hover:underline">
                            Limpiar todo
                        </a>
                    @endif
                </div>

                {{-- Categorías --}}
                <div>
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Categorías</h4>
                    <ul class="space-y-1.5 text-xs">
                        <li>
                            <a href="{{ route('tienda.catalogo', request()->except('categoria', 'page')) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-xl transition {{ !request('categoria') && !isset($categoriaActual) ? 'bg-yellow-50 text-yellow-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                                <span>Todas las categorías</span>
                                <span class="text-[10px] text-gray-400 font-semibold">{{ $categorias->sum('productos_count') }}</span>
                            </a>
                        </li>
                        @foreach($categorias as $cat)
                            @php
                                $esSeleccionada = (request('categoria') == $cat->id) || (isset($categoriaActual) && $categoriaActual->id == $cat->id);
                            @endphp
                            <li>
                                <a href="{{ route('tienda.categoria', $cat->id) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-xl transition {{ $esSeleccionada ? 'bg-yellow-50 text-yellow-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                                    <span>{{ $cat->nombre_categoria }}</span>
                                    <span class="text-[10px] text-gray-400 font-semibold">{{ $cat->productos_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Rango de Precio --}}
                <form action="{{ route('tienda.catalogo') }}" method="GET" class="border-t border-gray-100 pt-4">
                    @if(request('categoria'))
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                    @endif
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    @if(request('orden'))
                        <input type="hidden" name="orden" value="{{ request('orden') }}">
                    @endif

                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Rango de Precio</h4>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-gray-400 font-semibold block mb-0.5">Mínimo ($)</label>
                            <input
                                type="number"
                                name="precio_min"
                                value="{{ request('precio_min') }}"
                                placeholder="0"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 font-semibold block mb-0.5">Máximo ($)</label>
                            <input
                                type="number"
                                name="precio_max"
                                value="{{ request('precio_max') }}"
                                placeholder="1000000"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none">
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-gray-900 hover:bg-black text-white text-xs font-semibold py-2 rounded-xl transition shadow-2xs">
                        Aplicar Precio
                    </button>
                </form>

            </div>
        </aside>

        {{-- ===== LISTADO DE PRODUCTOS ===== --}}
        <div class="flex-1">

            {{-- Barra Superior de Catálogo --}}
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg font-black text-gray-900">
                        @if(isset($categoriaActual))
                            Categoría: {{ $categoriaActual->nombre_categoria }}
                        @elseif(request('q'))
                            Resultados para: <span class="text-yellow-600 font-bold">"{{ request('q') }}"</span>
                        @else
                            Todos los Productos
                        @endif
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Mostrando <span class="font-bold text-gray-800">{{ $productos->count() }}</span> de <span class="font-bold text-gray-800">{{ $totalResultados }}</span> productos
                    </p>
                </div>

                {{-- Ordenamiento --}}
                <form action="{{ url()->current() }}" method="GET" class="flex items-center gap-2">
                    @foreach(request()->except('orden', 'page') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach

                    <label for="orden_select" class="text-xs text-gray-500 font-medium whitespace-nowrap">Ordenar por:</label>
                    <select
                        name="orden"
                        id="orden_select"
                        onchange="this.form.submit()"
                        class="border border-gray-300 rounded-xl px-3 py-1.5 text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none bg-gray-50 font-semibold text-gray-700">
                        <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>Más recientes</option>
                        <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                        <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                        <option value="nombre_asc" {{ request('orden') == 'nombre_asc' ? 'selected' : '' }}>Nombre: A-Z</option>
                    </select>
                </form>
            </div>

            {{-- Grid de Productos con Estilo Moderno y Espacioso --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($productos as $product)
                    @php
                        $foto = $product->imagenes && $product->imagenes->count() > 0 ? $product->imagenes->first()->imagen : null;
                        $tieneOferta = $product->ofertaActiva;
                        $esFav = in_array($product->id, $favoritosIds ?? []);
                        $stockTotal = $product->stock ?? 10;
                    @endphp
                    <div class="bg-white rounded-[28px] border border-slate-100/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col justify-between group relative" 
                         x-data="{ liked: {{ $esFav ? 'true' : 'false' }}, qty: 1, maxStock: {{ $stockTotal > 0 ? $stockTotal : 1 }} }">
                        
                        {{-- Parte Superior (Imagen y Badges) --}}
                        <div>
                            <div class="relative w-full aspect-square bg-transparent flex items-center justify-center p-2 mb-3 overflow-hidden">
                                
                                {{-- Botón Me Encanta (Corazón) --}}
                                <button
                                    type="button"
                                    @click.stop.prevent="toggleFavoritoGlobal({{ $product->id }}, data => liked = data.is_favorite)"
                                    title="Me encanta"
                                    class="absolute top-2 left-2 w-9 h-9 rounded-full bg-white shadow-sm border border-slate-100/90 flex items-center justify-center text-slate-400 hover:text-rose-500 transition z-20 hover:scale-110 cursor-pointer">
                                    <i :class="liked ? 'fa-solid fa-heart text-rose-500 scale-110' : 'fa-regular fa-heart text-slate-400'" class="text-sm transition-transform duration-150"></i>
                                </button>

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
                    <div class="col-span-full bg-white rounded-3xl p-12 text-center text-gray-400 border border-gray-200">
                        <i class="fa-solid fa-box-open text-4xl text-gray-300 mb-3"></i>
                        <p class="text-sm font-semibold text-gray-700">No se encontraron productos con los filtros seleccionados</p>
                        <a href="{{ route('tienda.catalogo') }}" class="inline-block mt-4 px-4 py-2 bg-yellow-400 text-gray-900 font-bold text-xs rounded-xl hover:bg-yellow-500 transition">
                            Restablecer filtros
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            <div class="mt-8">
                {{ $productos->links() }}
            </div>

        </div>

    </div>

</div>

@endsection
