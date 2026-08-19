@extends('layouts.sidebaradmin')

@section('title', 'Productos')

@section('content')

<div
    x-data="{
        openModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        editModal: false,
        ofertaModal: false,
        
        // Datos producto editar
        editId: null,
        nombre: '',
        descripcion: '',
        categoria_id: '',
        proveedor_id: '',
        stock: '',
        disponibilidad: '1',
        precio: '',
        precio_compra: '',
        editUrl: '',
        imagenesActuales: [],

        // Datos oferta modal
        ofertaProductoId: null,
        ofertaProductoNombre: '',
        ofertaPrecioOriginal: 0,
        ofertaPrecioOferta: 0,
        ofertaDescuento: 10,
        ofertaFechaInicio: new Date().toISOString().split('T')[0],
        ofertaFechaFin: '',
        ofertaEstado: 'activa',

        abrirEditar(producto) {
            this.editId = producto.id;
            this.nombre = producto.nombre;
            this.descripcion = producto.descripcion ?? '';
            this.categoria_id = producto.categoria_id;
            this.proveedor_id = producto.proveedor_id ?? '';
            this.stock = producto.stock;
            this.disponibilidad = producto.disponibilidad ? '1' : '0';
            this.precio = producto.precio;
            this.precio_compra = producto.precio_compra;
            this.imagenesActuales = producto.imagenes ?? [];
            this.editUrl = '{{ url('/productos') }}/' + producto.id;
            this.editModal = true;
        },

        abrirOferta(producto) {
            this.ofertaProductoId = producto.id;
            this.ofertaProductoNombre = producto.nombre;
            this.ofertaPrecioOriginal = parseFloat(producto.precio) || 0;
            
            if (producto.oferta) {
                this.ofertaPrecioOferta = parseFloat(producto.oferta.precio_oferta) || 0;
                this.ofertaDescuento = producto.oferta.descuento || 0;
                this.ofertaFechaInicio = producto.oferta.fecha_inicio || this.ofertaFechaInicio;
                this.ofertaFechaFin = producto.oferta.fecha_fin || '';
                this.ofertaEstado = producto.oferta.estado || 'activa';
            } else {
                this.ofertaDescuento = 10;
                this.calcDesdeDescuento();
                let hoy = new Date();
                let enUnMes = new Date();
                enUnMes.setDate(hoy.getDate() + 30);
                this.ofertaFechaInicio = hoy.toISOString().split('T')[0];
                this.ofertaFechaFin = enUnMes.toISOString().split('T')[0];
                this.ofertaEstado = 'activa';
            }
            this.ofertaModal = true;
        },

        calcDesdeDescuento() {
            if (this.ofertaPrecioOriginal > 0) {
                let desc = Math.max(0, Math.min(100, parseFloat(this.ofertaDescuento) || 0));
                this.ofertaPrecioOferta = Math.round(this.ofertaPrecioOriginal * (1 - (desc / 100)));
            }
        },

        calcDesdePrecio() {
            if (this.ofertaPrecioOriginal > 0 && this.ofertaPrecioOferta >= 0) {
                let precioOf = parseFloat(this.ofertaPrecioOferta) || 0;
                let desc = Math.round(((this.ofertaPrecioOriginal - precioOf) / this.ofertaPrecioOriginal) * 100);
                this.ofertaDescuento = Math.max(0, Math.min(100, desc));
            }
        }
    }">

    {{-- ==================== TÍTULO Y BOTÓN ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Gestión de Productos
            </h1>
            <p class="text-xs text-gray-500 mt-1">Administra el catálogo de productos, precios, ofertas y disponibilidad</p>
        </div>

        <button
            type="button"
            @click="openModal = true"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-lg font-semibold hover:bg-black transition shadow-sm">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo Producto
        </button>
    </div>


    {{-- ==================== MENSAJES DE ESTADO Y ERRORES ==================== --}}
    @if(session('Mensaje'))
        <div class="mt-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span class="text-sm font-medium">{{ session('Mensaje') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mt-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg shadow-xs">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                <span class="text-sm">Por favor corrige los siguientes errores:</span>
            </div>
            <ul class="list-disc pl-7 text-xs space-y-0.5 font-semibold">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- ==================== TABLA Y CONTADOR ==================== --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">

        {{-- Barra de información de paginación superior --}}
        <div class="px-6 py-3.5 border-b border-gray-100 flex items-center justify-between text-xs text-gray-500 bg-white">
            <div>
                Mostrando <span class="font-bold text-gray-700">{{ $productos->count() }}</span> de <span class="font-bold text-gray-700">{{ $productos->total() }}</span> productos
            </div>
            <div>
                Página <span class="font-bold text-gray-700">{{ $productos->currentPage() }}</span> de <span class="font-bold text-gray-700">{{ $productos->lastPage() }}</span>
            </div>
        </div>

        {{-- Tabla responsiva --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50/80 text-[11px] font-bold tracking-wider text-gray-500 uppercase border-b border-gray-100">
                        <th class="px-5 py-3.5">IMAGEN</th>
                        <th class="px-5 py-3.5">NOMBRE</th>
                        <th class="px-5 py-3.5">PRECIO</th>
                        <th class="px-5 py-3.5">PRECIO COMPRA</th>
                        <th class="px-5 py-3.5">STOCK</th>
                        <th class="px-5 py-3.5">CATEGORÍA</th>
                        <th class="px-5 py-3.5">PROVEEDOR</th>
                        <th class="px-5 py-3.5">ESTADO</th>
                        <th class="px-5 py-3.5 text-center">OFERTA</th>
                        <th class="px-5 py-3.5 text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse($productos as $producto)
                    @php
                        $ofertaActiva = $producto->ofertas->where('estado', 'activa')->first();
                    @endphp

                    <tr class="hover:bg-gray-50/70 transition-colors">

                        {{-- 1. IMAGEN --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($producto->imagenes->count() > 0)
                                    @foreach($producto->imagenes as $img)
                                        <div class="relative group inline-block shrink-0">
                                            <img
                                                src="{{ asset('imagenes_productos/' . $img->imagen) }}"
                                                alt="{{ $producto->nombre }}"
                                                class="w-10 h-10 object-cover rounded-lg border border-gray-200 shadow-xs">
                                            
                                            {{-- Badge rojo (x) para eliminar foto individual --}}
                                            <form
                                                action="{{ route('productos.imagen.eliminar', $img->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Eliminar esta imagen?');"
                                                class="absolute -top-1.5 -right-1.5">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    title="Eliminar imagen"
                                                    class="w-4 h-4 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center text-[9px] font-black shadow-sm transition">
                                                    <i class="fa-solid fa-xmark text-[8px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center text-gray-300">
                                        <i class="fa-solid fa-image text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- 2. NOMBRE --}}
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-gray-800">{{ $producto->nombre }}</p>
                            @if($producto->descripcion)
                                <p class="text-[11px] text-gray-400 truncate max-w-xs">{{ $producto->descripcion }}</p>
                            @endif
                        </td>

                        {{-- 3. PRECIO --}}
                        <td class="px-5 py-3.5 font-normal text-gray-800 whitespace-nowrap">
                            ${{ number_format($producto->precio, 0, ',', '.') }}
                        </td>

                        {{-- 4. PRECIO COMPRA --}}
                        <td class="px-5 py-3.5 font-normal text-gray-800 whitespace-nowrap">
                            ${{ number_format($producto->precio_compra, 0, ',', '.') }}
                        </td>

                        {{-- 5. STOCK --}}
                        <td class="px-5 py-3.5 font-normal text-gray-800">
                            {{ $producto->stock }}
                        </td>

                        {{-- 6. CATEGORÍA --}}
                        <td class="px-5 py-3.5 text-gray-700">
                            {{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}
                        </td>

                        {{-- 7. PROVEEDOR --}}
                        <td class="px-5 py-3.5 text-gray-700">
                            {{ $producto->proveedor->nombre_proveedor ?? 'Sin proveedor' }}
                        </td>

                        {{-- 8. ESTADO --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($producto->disponibilidad)
                                <span class="inline-flex items-center px-3 py-0.5 text-xs font-semibold rounded-full bg-[#0f6848] text-white">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-0.5 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        {{-- 9. OFERTA --}}
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            <button
                                type="button"
                                @click="abrirOferta({
                                    id: {{ $producto->id }},
                                    nombre: @js($producto->nombre),
                                    precio: {{ (float)$producto->precio }},
                                    oferta: @js($ofertaActiva)
                                })"
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-amber-400 text-amber-500 hover:bg-amber-50 hover:text-amber-600 transition shadow-xs relative"
                                title="{{ $ofertaActiva ? 'Oferta Activa: -' . $ofertaActiva->descuento . '% ($' . number_format($ofertaActiva->precio_oferta, 0, ',', '.') . ')' : 'Crear oferta para este producto' }}">
                                <i class="fa-solid fa-tag text-xs"></i>
                                @if($ofertaActiva)
                                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-amber-500 rounded-full ring-2 ring-white" title="Oferta activa"></span>
                                @endif
                            </button>
                        </td>

                        {{-- 10. ACCIONES --}}
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">

                                {{-- TOGGLE SWITCH DE ESTADO --}}
                                <form action="{{ route('productos.estado', $producto->id) }}" method="POST" class="inline-flex items-center">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $producto->disponibilidad ? 'bg-blue-600' : 'bg-gray-300' }}"
                                        title="{{ $producto->disponibilidad ? 'Click para desactivar' : 'Click para activar' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out {{ $producto->disponibilidad ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </form>

                                {{-- EDITAR --}}
                                <button
                                    type="button"
                                    @click="abrirEditar({
                                        id: {{ $producto->id }},
                                        nombre: @js($producto->nombre),
                                        descripcion: @js($producto->descripcion),
                                        categoria_id: {{ $producto->categoria_id }},
                                        proveedor_id: {{ $producto->proveedor_id ?? 'null' }},
                                        stock: {{ $producto->stock }},
                                        disponibilidad: {{ $producto->disponibilidad ? 1 : 0 }},
                                        precio: {{ (float)$producto->precio }},
                                        precio_compra: {{ (float)$producto->precio_compra }},
                                        imagenes: @js($producto->imagenes)
                                    })"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-blue-400 text-blue-500 hover:bg-blue-50 hover:text-blue-600 transition shadow-xs"
                                    title="Editar">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>

                                {{-- ELIMINAR --}}
                                <form
                                    action="{{ route('productos.eliminar', $producto->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-red-400 text-red-500 hover:bg-red-50 hover:text-red-600 transition shadow-xs"
                                        title="Eliminar">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                            <i class="fa-solid fa-box-open text-3xl mb-2 text-gray-300"></i>
                            <p class="text-sm">No hay productos registrados.</p>
                        </td>
                    </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Paginación inferior --}}
        @if($productos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $productos->links() }}
            </div>
        @endif

    </div>


    {{-- ==================== MODAL CREAR / GESTIONAR OFERTA ==================== --}}
    <div
        x-show="ofertaModal"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        {{-- Fondo oscuro --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="ofertaModal = false"></div>

        {{-- Contenedor del modal --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden z-10" @click.stop>
            
            {{-- Encabezado con toque ámbar --}}
            <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 bg-white/20 rounded-lg text-lg">🏷️</span>
                    <div>
                        <h2 class="text-lg font-bold">Crear / Gestionar Oferta</h2>
                        <p class="text-xs text-amber-100" x-text="'Producto: ' + ofertaProductoNombre"></p>
                    </div>
                </div>
                <button type="button" @click="ofertaModal = false" class="text-white/80 hover:text-white text-2xl font-bold">
                    &times;
                </button>
            </div>

            {{-- Formulario de Oferta --}}
            <form action="{{ route('ofertas.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="producto_id" :value="ofertaProductoId">

                {{-- Resumen de precios --}}
                <div class="grid grid-cols-2 gap-4 p-3 bg-amber-50/70 border border-amber-200/80 rounded-xl">
                    <div>
                        <span class="text-[11px] uppercase tracking-wider text-gray-500 font-semibold">Precio Original</span>
                        <p class="text-lg font-bold text-gray-800" x-text="'$' + new Intl.NumberFormat('es-CO').format(ofertaPrecioOriginal)"></p>
                    </div>
                    <div>
                        <span class="text-[11px] uppercase tracking-wider text-amber-700 font-semibold">Precio Oferta</span>
                        <p class="text-lg font-extrabold text-amber-600" x-text="'$' + new Intl.NumberFormat('es-CO').format(ofertaPrecioOferta)"></p>
                    </div>
                </div>

                {{-- Descuento (%) y Precio de oferta --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="oferta_descuento" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Descuento (%)
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                name="descuento"
                                id="oferta_descuento"
                                x-model="ofertaDescuento"
                                @input="calcDesdeDescuento()"
                                min="1"
                                max="100"
                                required
                                class="w-full pr-8 pl-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-amber-400 focus:border-amber-400 text-sm font-semibold">
                            <span class="absolute right-3 top-2.5 text-gray-400 text-xs font-bold">%</span>
                        </div>
                    </div>

                    <div>
                        <label for="oferta_precio_oferta" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Precio de Oferta ($)
                        </label>
                        <input
                            type="number"
                            name="precio_oferta"
                            id="oferta_precio_oferta"
                            x-model="ofertaPrecioOferta"
                            @input="calcDesdePrecio()"
                            step="1"
                            min="0"
                            required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-amber-400 focus:border-amber-400 text-sm font-semibold">
                    </div>
                </div>

                {{-- Fechas Inicio y Fin --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="oferta_fecha_inicio" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Fecha Inicio
                        </label>
                        <input
                            type="date"
                            name="fecha_inicio"
                            id="oferta_fecha_inicio"
                            x-model="ofertaFechaInicio"
                            required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-amber-400 focus:border-amber-400 text-sm">
                    </div>

                    <div>
                        <label for="oferta_fecha_fin" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Fecha Fin
                        </label>
                        <input
                            type="date"
                            name="fecha_fin"
                            id="oferta_fecha_fin"
                            x-model="ofertaFechaFin"
                            required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-amber-400 focus:border-amber-400 text-sm">
                    </div>
                </div>

                {{-- Estado de la oferta --}}
                <div>
                    <label for="oferta_estado" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Estado de la Oferta
                    </label>
                    <select
                        name="estado"
                        id="oferta_estado"
                        x-model="ofertaEstado"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-amber-400 focus:border-amber-400 text-sm">
                        <option value="activa">Activa (Publicada)</option>
                        <option value="inactiva">Inactiva (Pausada)</option>
                    </select>
                </div>

                {{-- Botones de acción --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button
                        type="button"
                        @click="ofertaModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-sm transition flex items-center gap-2">
                        <i class="fa-solid fa-check text-xs"></i>
                        Guardar Oferta
                    </button>
                </div>
            </form>

        </div>
    </div>


    {{-- ==================== MODAL NUEVO PRODUCTO ==================== --}}
    <div
        x-show="openModal"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="openModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10 p-6" @click.stop>

            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-box text-violet-600"></i>
                    Nuevo Producto
                </h2>
                <button type="button" @click="openModal = false" class="text-gray-400 hover:text-gray-700 text-2xl font-bold">
                    &times;
                </button>
            </div>

            <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="nombre" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nombre</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                           class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm"
                           placeholder="Ej. Lámpara LED Inteligente">
                </div>

                <div>
                    <label for="descripcion" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3"
                              class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm"
                              placeholder="Breve descripción del producto...">{{ old('descripcion') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="categoria_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Categoría</label>
                        <select name="categoria_id" id="categoria_id" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm">
                            <option value="">Seleccionar</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre_categoria }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="proveedor_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Proveedor</label>
                        <select name="proveedor_id" id="proveedor_id" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm">
                            <option value="">Seleccionar</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                    {{ $proveedor->nombre_proveedor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="stock" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Stock</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" required
                               class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm">
                    </div>

                    <div>
                        <label for="disponibilidad" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Disponibilidad</label>
                        <select name="disponibilidad" id="disponibilidad" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm">
                            <option value="1" {{ old('disponibilidad', '1') == '1' ? 'selected' : '' }}>Disponible</option>
                            <option value="0" {{ old('disponibilidad') == '0' ? 'selected' : '' }}>No disponible</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="precio" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Precio de Venta ($)</label>
                        <input type="number" name="precio" id="precio" value="{{ old('precio') }}" step="0.01" min="0" required
                               class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm"
                               placeholder="Ej. 100000">
                    </div>

                    <div>
                        <label for="precio_compra" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Precio de Compra ($)</label>
                        <input type="number" name="precio_compra" id="precio_compra" value="{{ old('precio_compra') }}" step="0.01" min="0" required
                               class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm"
                               placeholder="Ej. 60000">
                    </div>
                </div>

                <div>
                    <label for="imagenes" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Imágenes del producto</label>
                    <input type="file" name="imagenes[]" id="imagenes" accept="image/*" multiple
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-violet-600 hover:bg-violet-700 rounded-lg shadow-sm transition">
                        Guardar Producto
                    </button>
                </div>
            </form>

        </div>
    </div>


    {{-- ==================== MODAL EDITAR PRODUCTO ==================== --}}
    <div
        x-show="editModal"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="editModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10 p-6" @click.stop>

            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                    Editar Producto
                </h2>
                <button type="button" @click="editModal = false" class="text-gray-400 hover:text-gray-700 text-2xl font-bold">
                    &times;
                </button>
            </div>

            <form :action="editUrl" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="edit_nombre" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nombre</label>
                    <input type="text" name="nombre" id="edit_nombre" x-model="nombre" required
                           class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div>
                    <label for="edit_descripcion" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Descripción</label>
                    <textarea name="descripcion" id="edit_descripcion" rows="3" x-model="descripcion"
                              class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_categoria_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Categoría</label>
                        <select name="categoria_id" id="edit_categoria_id" x-model="categoria_id" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Seleccionar</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre_categoria }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit_proveedor_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Proveedor</label>
                        <select name="proveedor_id" id="edit_proveedor_id" x-model="proveedor_id" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Seleccionar</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}">{{ $proveedor->nombre_proveedor }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_stock" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Stock</label>
                        <input type="number" name="stock" id="edit_stock" x-model="stock" min="0" required
                               class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <label for="edit_disponibilidad" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Disponibilidad</label>
                        <select name="disponibilidad" id="edit_disponibilidad" x-model="disponibilidad" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="1">Disponible</option>
                            <option value="0">No disponible</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_precio" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Precio de Venta ($)</label>
                        <input type="number" name="precio" id="edit_precio" x-model="precio" step="0.01" min="0" required
                               class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <label for="edit_precio_compra" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Precio de Compra ($)</label>
                        <input type="number" name="precio_compra" id="edit_precio_compra" x-model="precio_compra" step="0.01" min="0" required
                               class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>

                {{-- Mostrar fotos actuales en modal de edición --}}
                <template x-if="imagenesActuales.length > 0">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-2">Imágenes actuales</label>
                        <div class="flex items-center gap-2 flex-wrap">
                            <template x-for="img in imagenesActuales" :key="img.id">
                                <div class="relative group">
                                    <img :src="'{{ asset('imagenes_productos') }}/' + img.imagen" class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div>
                    <label for="edit_imagenes" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Agregar más imágenes <span class="text-gray-400 font-normal lowercase">(opcional)</span>
                    </label>
                    <input type="file" name="imagenes[]" id="edit_imagenes" accept="image/*" multiple
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="editModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                        Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

@endsection