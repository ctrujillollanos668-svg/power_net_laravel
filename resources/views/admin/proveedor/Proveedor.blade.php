@extends('layouts.sidebaradmin')

@section('title', 'Proveedores')

@section('content')

{{-- ========================================================= --}}
{{-- CONTENEDOR PRINCIPAL DE ALPINE                            --}}
{{-- ========================================================= --}}

<div
    x-data="{
        openModal: false,
        editModal: false,
        q: '',
        filtroEstado: 'todos',

        // Control de acordeón de productos por proveedor
        openProductos: {},

        // Datos para editar proveedor
        editId: null,
        editNombre: '',
        editCorreo: '',
        editTelefono: '',
        editEstado: '1',
        editUrl: '',

        abrirEditar(proveedor) {
            this.editId = proveedor.id;
            this.editNombre = proveedor.nombre_proveedor;
            this.editCorreo = proveedor.correo;
            this.editTelefono = proveedor.telefono;
            this.editEstado = proveedor.estado ? '1' : '0';
            this.editUrl = '{{ url('/proveedores') }}/' + proveedor.id;
            this.editModal = true;
        }
    }"
    x-cloak>

    {{-- ==================== TÍTULO Y BOTÓN ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2.5">
                <span class="text-2xl">🏭</span>
                Gestión de Proveedores
            </h1>
            <p class="text-xs text-gray-500 mt-1">Administra tu red de proveedores y supervisa los productos suministrados</p>
        </div>

        <button
            type="button"
            @click="openModal = true"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-lg font-semibold hover:bg-black transition shadow-sm">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo Proveedor
        </button>
    </div>


    {{-- ==================== MENSAJES FLASH ==================== --}}
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

    @if(session('Error'))
        <div class="mt-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                <span class="text-sm font-medium">{{ session('Error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif


    {{-- ==================== CARDS DE RESUMEN ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-6">
        
        {{-- Total Proveedores --}}
        <div class="bg-white rounded-xl shadow-xs border border-gray-200/80 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Proveedores</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ $proveedores->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-industry"></i>
            </div>
        </div>

        {{-- Activos --}}
        <div class="bg-white rounded-xl shadow-xs border border-gray-200/80 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Activos</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $proveedores->where('estado', 1)->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        {{-- Inactivos --}}
        <div class="bg-white rounded-xl shadow-xs border border-gray-200/80 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Inactivos</p>
                <p class="text-2xl font-black text-gray-500 mt-1">{{ $proveedores->where('estado', 0)->count() }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-pause"></i>
            </div>
        </div>

        {{-- Total Productos Suministrados --}}
        <div class="bg-white rounded-xl shadow-xs border border-gray-200/80 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Productos Suministrados</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ $proveedores->sum('productos_count') }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
        </div>

    </div>


    {{-- ==================== FILTROS Y BUSCADOR ==================== --}}
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        
        {{-- Buscador --}}
        <div class="relative w-full sm:max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                type="text"
                x-model="q"
                placeholder="Buscar por nombre, correo o teléfono..."
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent shadow-xs">
        </div>

        {{-- Filtro de estado --}}
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button
                type="button"
                @click="filtroEstado = 'todos'"
                :class="filtroEstado === 'todos' ? 'bg-gray-900 text-white font-semibold' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-3 py-1.5 rounded-lg text-xs transition">
                Todos ({{ $proveedores->count() }})
            </button>
            <button
                type="button"
                @click="filtroEstado = '1'"
                :class="filtroEstado === '1' ? 'bg-emerald-600 text-white font-semibold' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-3 py-1.5 rounded-lg text-xs transition">
                Activos ({{ $proveedores->where('estado', 1)->count() }})
            </button>
            <button
                type="button"
                @click="filtroEstado = '0'"
                :class="filtroEstado === '0' ? 'bg-gray-600 text-white font-semibold' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-3 py-1.5 rounded-lg text-xs transition">
                Inactivos ({{ $proveedores->where('estado', 0)->count() }})
            </button>
        </div>

    </div>


    {{-- ==================== TABLA DE PROVEEDORES ==================== --}}
    <div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">

        <div class="px-6 py-3.5 border-b border-gray-100 flex items-center justify-between text-xs text-gray-500 bg-white">
            <div>
                Listado de proveedores registrados en el sistema
            </div>
            <div>
                Total: <span class="font-bold text-gray-800">{{ $proveedores->count() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50/80 text-[11px] font-bold tracking-wider text-gray-500 uppercase border-b border-gray-100">
                        <th class="px-6 py-3.5">PROVEEDOR</th>
                        <th class="px-6 py-3.5">CORREO ELECTRÓNICO</th>
                        <th class="px-6 py-3.5">TELÉFONO</th>
                        <th class="px-6 py-3.5 text-center">PRODUCTOS</th>
                        <th class="px-6 py-3.5">ESTADO</th>
                        <th class="px-6 py-3.5 text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse ($proveedores as $proveedor)

                    <tr
                        x-show="(q === '' || '{{ strtolower($proveedor->nombre_proveedor) }}'.includes(q.toLowerCase()) || '{{ strtolower($proveedor->correo) }}'.includes(q.toLowerCase()) || '{{ $proveedor->telefono }}'.includes(q)) && (filtroEstado === 'todos' || filtroEstado === '{{ $proveedor->estado ? 1 : 0 }}')"
                        class="hover:bg-gray-50/70 transition-colors">

                        {{-- Proveedor con iniciales e ID --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($proveedor->nombre_proveedor, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 capitalize">{{ $proveedor->nombre_proveedor }}</p>
                                    <span class="text-[10px] text-gray-400">ID: #{{ $proveedor->id }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Correo --}}
                        <td class="px-6 py-4">
                            <a href="mailto:{{ $proveedor->correo }}" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                                <i class="fa-solid fa-envelope text-[11px] text-gray-400"></i>
                                {{ $proveedor->correo }}
                            </a>
                        </td>

                        {{-- Teléfono --}}
                        <td class="px-6 py-4 text-xs text-gray-700 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-[10px] text-gray-400"></i>
                                {{ $proveedor->telefono }}
                            </span>
                        </td>

                        {{-- Cantidad de productos (Clickable Badge acordeón) --}}
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <button
                                type="button"
                                @click="openProductos[{{ $proveedor->id }}] = !openProductos[{{ $proveedor->id }}]"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 hover:border-blue-300 transition cursor-pointer shadow-xs"
                                title="Click para ver los productos de este proveedor">
                                <i class="fa-solid fa-box text-[11px]" :class="openProductos[{{ $proveedor->id }}] ? 'text-blue-600' : 'text-blue-500'"></i>
                                <span>{{ $proveedor->productos_count }} {{ $proveedor->productos_count === 1 ? 'producto' : 'productos' }}</span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-blue-400 transition-transform duration-200" :class="openProductos[{{ $proveedor->id }}] && 'rotate-180'"></i>
                            </button>
                        </td>

                        {{-- Estado --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($proveedor->estado)
                                <span class="inline-flex items-center px-3 py-0.5 text-xs font-semibold rounded-full bg-[#0f6848] text-white">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-0.5 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">

                                {{-- Switch toggle de estado --}}
                                <form action="{{ route('proveedores.estado', $proveedor->id) }}" method="POST" class="inline-flex items-center">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $proveedor->estado ? 'bg-blue-600' : 'bg-gray-300' }}"
                                        title="{{ $proveedor->estado ? 'Desactivar proveedor' : 'Activar proveedor' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out {{ $proveedor->estado ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </form>

                                {{-- Editar --}}
                                <button
                                    type="button"
                                    @click="abrirEditar({
                                        id: {{ $proveedor->id }},
                                        nombre_proveedor: @js($proveedor->nombre_proveedor),
                                        correo: @js($proveedor->correo),
                                        telefono: @js($proveedor->telefono),
                                        estado: {{ $proveedor->estado ? 1 : 0 }}
                                    })"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-blue-400 text-blue-500 hover:bg-blue-50 hover:text-blue-600 transition shadow-xs"
                                    title="Editar">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>

                                {{-- Eliminar --}}
                                <form
                                    action="{{ route('proveedores.eliminar', $proveedor->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este proveedor?');">
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

                    {{-- ==================== SUBTABLA DESPLEGABLE DE PRODUCTOS ==================== --}}
                    <tr
                        x-show="openProductos[{{ $proveedor->id }}]"
                        x-cloak
                        x-transition
                        class="bg-gray-50/70 border-b border-gray-200">
                        <td colspan="6" class="p-4 sm:p-6">
                            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">

                                {{-- Encabezado de la sub-tabla --}}
                                <div class="px-5 py-3.5 bg-gray-50/90 border-b border-gray-200 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-boxes-stacked text-gray-500 text-sm"></i>
                                        <h3 class="font-semibold text-gray-800 text-sm">
                                            Productos suministrados por <span class="capitalize">{{ $proveedor->nombre_proveedor }}</span>
                                        </h3>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">
                                            {{ $proveedor->productos->count() }}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="openProductos[{{ $proveedor->id }}] = false"
                                        class="text-xs text-gray-400 hover:text-gray-600 flex items-center gap-1 font-medium transition">
                                        <i class="fa-solid fa-xmark"></i> Ocultar
                                    </button>
                                </div>

                                {{-- Contenido de productos --}}
                                @if($proveedor->productos && $proveedor->productos->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left">
                                            <thead>
                                                <tr class="bg-gray-50/50 text-[11px] font-bold tracking-wider text-gray-500 uppercase border-b border-gray-200">
                                                    <th class="px-5 py-3">IMAGEN</th>
                                                    <th class="px-5 py-3">NOMBRE</th>
                                                    <th class="px-5 py-3">PRECIO</th>
                                                    <th class="px-5 py-3">PRECIO COMPRA</th>
                                                    <th class="px-5 py-3">STOCK</th>
                                                    <th class="px-5 py-3">CATEGORÍA</th>
                                                    <th class="px-5 py-3">ESTADO</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                @foreach($proveedor->productos as $producto)
                                                    <tr class="hover:bg-gray-50/80 transition">
                                                        {{-- Imagen --}}
                                                        <td class="px-5 py-3">
                                                            @if($producto->imagenes && $producto->imagenes->count() > 0)
                                                                <img
                                                                    src="{{ asset('imagenes_productos/' . $producto->imagenes->first()->imagen) }}"
                                                                    alt="{{ $producto->nombre }}"
                                                                    class="w-10 h-10 object-cover rounded-lg border border-gray-200 shadow-xs">
                                                            @else
                                                                <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-300">
                                                                    <i class="fa-solid fa-image text-xs"></i>
                                                                </div>
                                                            @endif
                                                        </td>

                                                        {{-- Nombre --}}
                                                        <td class="px-5 py-3 font-semibold text-gray-800">
                                                            {{ $producto->nombre }}
                                                        </td>

                                                        {{-- Precio --}}
                                                        <td class="px-5 py-3 font-semibold text-gray-800 whitespace-nowrap">
                                                            ${{ number_format($producto->precio, 0, ',', '.') }}
                                                        </td>

                                                        {{-- Precio Compra --}}
                                                        <td class="px-5 py-3 text-gray-600 whitespace-nowrap">
                                                            ${{ number_format($producto->precio_compra, 0, ',', '.') }}
                                                        </td>

                                                        {{-- Stock --}}
                                                        <td class="px-5 py-3 font-medium text-gray-700">
                                                            {{ $producto->stock }}
                                                        </td>

                                                        {{-- Categoría --}}
                                                        <td class="px-5 py-3 text-gray-600">
                                                            {{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}
                                                        </td>

                                                        {{-- Estado --}}
                                                        <td class="px-5 py-3 whitespace-nowrap">
                                                            @if($producto->disponibilidad)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full bg-[#0f6848] text-white">
                                                                    Activo
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                                                    Inactivo
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="px-5 py-8 text-center text-gray-400">
                                        <i class="fa-solid fa-box-open text-2xl mb-2 text-gray-300"></i>
                                        <p class="text-xs">Este proveedor no tiene productos asignados actualmente.</p>
                                    </div>
                                @endif

                            </div>
                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fa-solid fa-industry text-3xl mb-2 text-gray-300"></i>
                            <p class="text-sm">No hay proveedores registrados.</p>
                        </td>
                    </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        @if($proveedores->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $proveedores->links() }}
            </div>
        @endif

    </div>

    {{-- ==================== MODAL NUEVO PROVEEDOR ==================== --}}
    <div
        x-show="openModal"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="openModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden z-10 p-6" @click.stop>

            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-amber-600"></i>
                    Nuevo Proveedor
                </h2>
                <button type="button" @click="openModal = false" class="text-gray-400 hover:text-gray-700 text-2xl font-bold">
                    &times;
                </button>
            </div>

            <form action="{{ route('proveedores.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="nombre_proveedor" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Nombre del Proveedor
                    </label>
                    <input
                        type="text"
                        name="nombre_proveedor"
                        id="nombre_proveedor"
                        required
                        class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm"
                        placeholder="Ej. Distribuciones Perez S.A.S.">
                </div>

                <div>
                    <label for="correo" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Correo Electrónico
                    </label>
                    <input
                        type="email"
                        name="correo"
                        id="correo"
                        required
                        class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm"
                        placeholder="ejemplo@proveedor.com">
                </div>

                <div>
                    <label for="telefono" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Teléfono / Contacto
                    </label>
                    <input
                        type="text"
                        name="telefono"
                        id="telefono"
                        required
                        class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm"
                        placeholder="Ej. 3224037962">
                </div>

                <div>
                    <label for="estado" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Estado Inicial
                    </label>
                    <select
                        name="estado"
                        id="estado"
                        class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                        <option value="1">Activo (Habilitado)</option>
                        <option value="0">Inactivo (Deshabilitado)</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button
                        type="button"
                        @click="openModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm transition">
                        Guardar Proveedor
                    </button>
                </div>

            </form>

        </div>

    </div>



       {{-- ==================== MODAL EDITAR PROVEEDOR ==================== --}}
    <div
        x-show="editModal"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="editModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden z-10 p-6" @click.stop>

            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                    Editar Proveedor
                </h2>
                <button type="button" @click="editModal = false" class="text-gray-400 hover:text-gray-700 text-2xl font-bold">
                    &times;
                </button>
            </div>

            <form :action="editUrl" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="edit_nombre_proveedor" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Nombre del Proveedor
                    </label>
                    <input
                        type="text"
                        name="nombre_proveedor"
                        id="edit_nombre_proveedor"
                        x-model="editNombre"
                        required
                        class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div>
                    <label for="edit_correo" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Correo Electrónico
                    </label>
                    <input
                        type="email"
                        name="correo"
                        id="edit_correo"
                        x-model="editCorreo"
                        required
                        class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div>
                    <label for="edit_telefono" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Teléfono / Contacto
                    </label>
                    <input
                        type="text"
                        name="telefono"
                        id="edit_telefono"
                        x-model="editTelefono"
                        required
                        class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div>
                    <label for="edit_estado" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                        Estado
                    </label>
                    <select
                        name="estado"
                        id="edit_estado"
                        x-model="editEstado"
                        class="w-full px-3.5 py-2 rounded-lg border border-gray-300 shadow-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button
                        type="button"
                        @click="editModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                        Guardar Cambios
                    </button>
                </div>

            </form>

        </div>

    </div>


</div>

@endsection