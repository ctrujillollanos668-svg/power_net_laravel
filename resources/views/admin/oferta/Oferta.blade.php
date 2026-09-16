@extends('layouts.sidebaradmin')

@section('title', 'Ofertas')

@section('content')

<div x-data="{
    openModal: false,
    editModal: false,
    q: '',
    editId: null,
    editProductoId: '',
    editPrecioOriginal: 0,
    editPrecioOferta: 0,
    editDescuento: 0,
    editFechaInicio: '',
    editFechaFin: '',
    editEstado: 'activa',
    editUrl: '',

    abrirEditar(oferta) {
        this.editId = oferta.id;
        this.editProductoId = oferta.producto_id;
        this.editPrecioOriginal = parseFloat(oferta.producto?.precio || 0);
        this.editPrecioOferta = parseFloat(oferta.precio_oferta || 0);
        this.editDescuento = oferta.descuento || 0;
        this.editFechaInicio = oferta.fecha_inicio;
        this.editFechaFin = oferta.fecha_fin;
        this.editEstado = oferta.estado;
        this.editUrl = '{{ url('/ofertas') }}/' + oferta.id;
        this.editModal = true;
    }
}">

    {{-- ==================== TÍTULO ==================== --}}
    <div class="flex items-center justify-between">

        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <span>🏷️</span> Ofertas y Promociones
        </h1>

        <button
            type="button"
            @click="openModal = true"
            class="px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-gray-900 rounded-xl font-bold text-xs transition shadow-sm cursor-pointer">
            + Nueva Oferta
        </button>

    </div>


    {{-- ==================== MENSAJE ==================== --}}
    @if(session('Mensaje'))
        <div class="mt-4 px-4 py-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs font-bold shadow-xs">
            {{ session('Mensaje') }}
        </div>
    @endif


    {{-- ==================== CARDS RESUMEN ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-gray-100 dark:border-slate-800 p-6">
            <p class="text-xs font-bold text-gray-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Total ofertas</p>
            <p class="text-3xl font-extrabold text-gray-800 dark:text-white">{{ $ofertas->count() }}</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-gray-100 dark:border-slate-800 p-6">
            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mb-2 uppercase tracking-wider">Activas</p>
            <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $ofertas->where('estado', 'activa')->count() }}</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-gray-100 dark:border-slate-800 p-6">
            <p class="text-xs font-bold text-gray-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Inactivas / Vencidas</p>
            <p class="text-3xl font-extrabold text-gray-700 dark:text-slate-300">{{ $ofertas->where('estado', '!=', 'activa')->count() }}</p>
        </div>

    </div>


    {{-- ==================== BUSCADOR ==================== --}}
    <div class="mt-6 max-w-md">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 text-sm"></i>
            <input
                type="text"
                x-model="q"
                placeholder="Buscar oferta por producto..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent shadow-xs">
        </div>
    </div>


    {{-- ==================== TABLA ==================== --}}
    <div class="mt-6 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/80 dark:bg-slate-800/60 text-left text-[11px] font-bold tracking-wider text-gray-500 dark:text-slate-400 uppercase border-b border-gray-100 dark:border-slate-800">
                    <th class="px-6 py-3.5">#</th>
                    <th class="px-6 py-3.5">Producto</th>
                    <th class="px-6 py-3.5">Precio original</th>
                    <th class="px-6 py-3.5">Precio oferta</th>
                    <th class="px-6 py-3.5">Descuento</th>
                    <th class="px-6 py-3.5">Inicio</th>
                    <th class="px-6 py-3.5">Fin</th>
                    <th class="px-6 py-3.5">Estado</th>
                    <th class="px-6 py-3.5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">

                @forelse($ofertas as $oferta)

                    <tr
                        x-show="q === '' || '{{ strtolower($oferta->producto->nombre ?? '') }}'.includes(q.toLowerCase())"
                        class="hover:bg-gray-50/70 dark:hover:bg-slate-800/40 transition">

                        <td class="px-6 py-4 font-semibold text-gray-800 dark:text-slate-400">#{{ $oferta->id }}</td>

                        <td class="px-6 py-4 font-semibold text-gray-800 dark:text-white">
                            {{ $oferta->producto->nombre ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-gray-400 dark:text-slate-500 line-through">
                            ${{ number_format($oferta->producto->precio ?? 0, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4 font-bold text-gray-800 dark:text-white">
                            ${{ number_format($oferta->precio_oferta, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-black rounded-lg bg-rose-600 text-white shadow-2xs">
                                -{{ $oferta->descuento }}%
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-500 dark:text-slate-400 whitespace-nowrap text-xs">
                            {{ \Carbon\Carbon::parse($oferta->fecha_inicio)->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4 text-gray-500 dark:text-slate-400 whitespace-nowrap text-xs">
                            {{ \Carbon\Carbon::parse($oferta->fecha_fin)->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4">

                            @if($oferta->estado === 'activa')

                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    Activa
                                </span>

                            @elseif($oferta->estado === 'vencida')

                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-400 text-gray-900">
                                    Vencida
                                </span>

                            @else

                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300">
                                    Inactiva
                                </span>

                            @endif

                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">

                                {{-- ACTIVAR / DESACTIVAR --}}
                                <form action="{{ route('ofertas.estado', $oferta->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if($oferta->estado === 'activa')
                                        <button
                                            type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-amber-300 dark:border-amber-700 text-amber-500 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 transition cursor-pointer"
                                            title="Desactivar oferta">
                                            <i class="fa-solid fa-pause text-xs"></i>
                                        </button>
                                    @else
                                        <button
                                            type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-emerald-300 dark:border-emerald-700 text-emerald-500 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 transition cursor-pointer"
                                            title="Activar oferta">
                                            <i class="fa-solid fa-play text-xs"></i>
                                        </button>
                                    @endif
                                </form>

                                {{-- EDITAR --}}
                                <button
                                    type="button"
                                    @click="abrirEditar({{ json_encode($oferta) }})"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-blue-200 dark:border-blue-700 text-blue-500 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 transition cursor-pointer"
                                    title="Editar">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>

                                {{-- ELIMINAR --}}
                                <form action="{{ route('ofertas.eliminar', $oferta->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta oferta?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-red-200 dark:border-red-700 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/50 transition cursor-pointer"
                                        title="Eliminar">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500 text-xs font-bold">
                            No hay ofertas registradas.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>

        @if($ofertas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/40">
                {{ $ofertas->links() }}
            </div>
        @endif

    </div>


    {{-- ==================== MODAL NUEVA OFERTA ==================== --}}
    <div
        x-show="openModal"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="openModal = false"></div>

        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto z-10 border border-gray-100 dark:border-slate-800" @click.stop>

            <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100 dark:border-slate-800">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                    Nueva Oferta
                </h2>
                <button type="button" @click="openModal = false" class="text-gray-500 hover:text-gray-800 dark:hover:text-slate-200 text-2xl cursor-pointer">
                    &times;
                </button>
            </div>

            <form action="{{ route('ofertas.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label for="producto_id" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Producto *</label>
                    <select name="producto_id" id="producto_id" required
                            class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500">
                        <option value="">Seleccionar producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}">{{ $producto->nombre }} (${{ number_format($producto->precio, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="descuento" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Descuento (%)</label>
                        <input type="number" name="descuento" id="descuento" min="1" max="100"
                               class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500"
                               placeholder="Ej. 15">
                    </div>

                    <div>
                        <label for="precio_oferta" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Precio de oferta ($) *</label>
                        <input type="number" name="precio_oferta" id="precio_oferta" step="1" min="0" required
                               class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500"
                               placeholder="Ej. 85000">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="fecha_inicio" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Fecha inicio *</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ date('Y-m-d') }}" required
                               class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label for="fecha_fin" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Fecha fin *</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required
                               class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label for="estado" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Estado</label>
                    <select name="estado" id="estado" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500">
                        <option value="activa">Activa</option>
                        <option value="inactiva">Inactiva</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-slate-800">
                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-700 text-xs font-bold cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-gray-900 rounded-xl font-bold text-xs shadow-xs cursor-pointer">
                        Guardar oferta
                    </button>
                </div>
            </form>

        </div>
    </div>


    {{-- ==================== MODAL EDITAR OFERTA ==================== --}}
    <div
        x-show="editModal"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="editModal = false"></div>

        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto z-10 border border-gray-100 dark:border-slate-800" @click.stop>

            <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100 dark:border-slate-800">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                    Editar Oferta
                </h2>
                <button type="button" @click="editModal = false" class="text-gray-500 hover:text-gray-800 dark:hover:text-slate-200 text-2xl cursor-pointer">
                    &times;
                </button>
            </div>

            <form :action="editUrl" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_descuento" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Descuento (%)</label>
                        <input type="number" name="descuento" id="edit_descuento" x-model="editDescuento" min="1" max="100"
                               class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label for="edit_precio_oferta" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Precio de oferta ($) *</label>
                        <input type="number" name="precio_oferta" id="edit_precio_oferta" x-model="editPrecioOferta" step="1" min="0" required
                               class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_fecha_inicio" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Fecha inicio *</label>
                        <input type="date" name="fecha_inicio" id="edit_fecha_inicio" x-model="editFechaInicio" required
                               class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label for="edit_fecha_fin" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Fecha fin *</label>
                        <input type="date" name="fecha_fin" id="edit_fecha_fin" x-model="editFechaFin" required
                               class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label for="edit_estado" class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Estado</label>
                    <select name="estado" id="edit_estado" x-model="editEstado" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-amber-500">
                        <option value="activa">Activa</option>
                        <option value="inactiva">Inactiva</option>
                        <option value="vencida">Vencida</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-slate-800">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-700 text-xs font-bold cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-gray-900 rounded-xl font-bold text-xs shadow-xs cursor-pointer">
                        Actualizar oferta
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

@endsection