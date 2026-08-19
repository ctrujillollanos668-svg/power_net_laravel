@extends('layouts.tienda')

@section('title', 'Inicio')

@section('content')

    {{-- ===== HERO BANNER (DISEÑO MODERNO, LUMINOSO Y ELEGANTE) ===== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
        <div class="bg-gradient-to-r from-amber-50/70 via-white to-yellow-50/50 rounded-3xl p-8 sm:p-10 border border-amber-200/60 shadow-xs relative overflow-hidden flex flex-col lg:flex-row items-center justify-between gap-8 sm:gap-10">

            {{-- Columna Texto Hero --}}
            <div class="flex-1 max-w-xl z-10">
                <span class="inline-flex items-center gap-2 bg-yellow-400/20 text-yellow-900 text-xs font-black uppercase px-3.5 py-1.5 rounded-full mb-4 border border-yellow-400/40 shadow-2xs">
                    <i class="fa-solid fa-bolt text-yellow-500"></i>
                    Materiales Eléctricos Certificados
                </span>
                
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight tracking-tight mb-4 text-gray-900">
                    Todo para tu hogar y tus proyectos.
                </h1>
                
                <p class="text-gray-600 mb-8 text-xs sm:text-sm leading-relaxed max-w-md">
                    Bombillos LED, cables, iluminación y suministros eléctricos garantizados al mejor precio con el respaldo de <strong class="text-gray-900">PowerNet</strong>.
                </p>

                <div class="flex flex-wrap gap-3">
                    <a href="#productos-seccion"
                        class="bg-yellow-400 hover:bg-yellow-500 text-gray-950 font-black text-xs px-7 py-3.5 rounded-2xl transition shadow-sm hover:shadow-md inline-flex items-center gap-2">
                        <i class="fa-solid fa-bag-shopping"></i>
                        Explorar Catálogo
                    </a>
                    <a href="{{ route('tienda.ofertas') }}"
                        class="bg-white hover:bg-gray-50 text-gray-800 font-bold text-xs px-6 py-3.5 rounded-2xl transition border border-gray-300 shadow-2xs inline-flex items-center gap-2">
                        <i class="fa-solid fa-fire text-red-500"></i>
                        Ver Promociones
                    </a>
                </div>
            </div>

            {{-- Columna Imagen Hero --}}
            <div class="flex-1 w-full max-w-md relative z-10">
                <div class="relative rounded-2xl overflow-hidden shadow-lg border border-gray-200/80 bg-white aspect-[4/3] flex items-center justify-center">
                    @if(file_exists(public_path('img/ia.png')))
                        <img
                            src="{{ asset('img/ia.png') }}"
                            alt="Productos PowerNet"
                            class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center p-6 text-center">
                            <span class="text-6xl mb-2 animate-bounce">💡</span>
                            <h3 class="text-2xl font-black text-gray-900">Power<span class="text-yellow-500">Net</span></h3>
                            <p class="text-xs text-gray-400 mt-1">Calidad e Innovación Eléctrica</p>
                        </div>
                    @endif

                    {{-- Badge flotante de stock --}}
                    <div class="absolute bottom-3 left-3 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-xl text-xs font-bold text-gray-800 border border-gray-200/80 flex items-center gap-2 shadow-md">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ $productCount ?? 0 }} productos en inventario</span>
                    </div>
                </div>
            </div>

            {{-- Decoraciones suaves de Fondo --}}
            <div class="absolute -right-20 -top-20 w-72 h-72 rounded-full bg-yellow-200/30 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-72 h-72 rounded-full bg-amber-200/30 blur-3xl pointer-events-none"></div>
        </div>
    </div>

    {{-- ===== PRODUCTOS (FILTRADO DIRECTO EN INICIO) ===== --}}
    <div id="productos-seccion" class="max-w-7xl mx-auto px-4 sm:px-6 py-6 pb-16">
        
        {{-- Encabezado y Filtro Rápido --}}
        <div class="bg-white rounded-3xl shadow-xs border border-gray-200/80 p-5 sm:p-6 mb-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-[220px]">
                @if(isset($categoriaSeleccionada) && $categoriaSeleccionada)
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-base sm:text-lg font-black text-gray-900">Categoría:</span>
                        <span class="text-base sm:text-lg font-black text-yellow-600">{{ $categoriaSeleccionada->nombre_categoria }}</span>
                        <a href="{{ route('tienda.inicio') }}#productos-seccion" 
                           title="Quitar filtro de categoría"
                           class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-3 py-1 rounded-full inline-flex items-center gap-1 transition">
                            <span>Ver todas</span>
                            <i class="fa-solid fa-xmark text-[11px] text-gray-500"></i>
                        </a>
                    </div>
                @elseif(request('q'))
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-base sm:text-lg font-black text-gray-900">Búsqueda:</span>
                        <span class="text-base sm:text-lg font-black text-yellow-600">"{{ request('q') }}"</span>
                        <a href="{{ route('tienda.inicio') }}#productos-seccion" 
                           title="Limpiar búsqueda"
                           class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-3 py-1 rounded-full inline-flex items-center gap-1 transition">
                            <span>Limpiar</span>
                            <i class="fa-solid fa-xmark text-[11px] text-gray-500"></i>
                        </a>
                    </div>
                @else
                    <span class="text-base sm:text-lg font-black text-gray-900 whitespace-nowrap">Productos Destacados</span>
                @endif
                <div class="h-0.5 bg-gray-200 flex-1 hidden md:block"></div>
                <span class="text-gray-400 text-xs font-semibold whitespace-nowrap">({{ $products->count() }} de {{ $productCount ?? 0 }} productos)</span>
            </div>

            <form action="{{ route('tienda.inicio') }}#productos-seccion" method="GET" class="flex flex-wrap items-center gap-2">
                @if(request('categoria'))
                    <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                @endif
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Buscar..."
                    class="border border-gray-300 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none bg-white">
                <input
                    type="number"
                    name="precio_min"
                    value="{{ request('precio_min') }}"
                    placeholder="$ Mín"
                    class="border border-gray-300 rounded-xl px-3 py-2 text-xs w-20 focus:ring-1 focus:ring-yellow-400 focus:outline-none bg-white">
                <input
                    type="number"
                    name="precio_max"
                    value="{{ request('precio_max') }}"
                    placeholder="$ Máx"
                    class="border border-gray-300 rounded-xl px-3 py-2 text-xs w-20 focus:ring-1 focus:ring-yellow-400 focus:outline-none bg-white">
                <select name="orden" class="border border-gray-300 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-yellow-400 focus:outline-none bg-white font-medium text-gray-700">
                    <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>Más recientes</option>
                    <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                    <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                    <option value="nombre_asc" {{ request('orden') == 'nombre_asc' ? 'selected' : '' }}>Nombre: A-Z</option>
                </select>
                <button type="submit" class="bg-gray-900 hover:bg-black text-white font-bold text-xs px-5 py-2 rounded-xl transition shadow-2xs">
                    Filtrar
                </button>
            </form>
        </div>

        {{-- Grid de Productos con Estilo Moderno y Espacioso --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($products as $product)
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
                    <a href="{{ route('tienda.inicio') }}#productos-seccion" class="inline-block mt-3 px-4 py-2 bg-yellow-400 text-gray-900 font-bold text-xs rounded-xl hover:bg-yellow-500 transition">
                        Ver todos los productos
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>

@endsection