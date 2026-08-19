@extends('layouts.sidebaradmin')

@section('title', 'Inventario')

@section('content')
<div x-data="{
    filtroNivel: '{{ request('nivel', 'todos') }}',
    busqueda: '{{ request('q', '') }}',
    modalAjuste: false,
    modalPrecios: false,
    selectedProducto: null,
    tipoMovimiento: 'entrada',
    cantidadAjuste: 1,
    motivoAjuste: '',
    
    abrirAjuste(prod, tipo = 'entrada') {
        this.selectedProducto = prod;
        this.tipoMovimiento = tipo;
        this.cantidadAjuste = 1;
        this.motivoAjuste = tipo === 'entrada' ? 'Reabastecimiento de mercancía' : 'Ajuste de inventario';
        this.modalAjuste = true;
    },

    abrirPrecios(prod) {
        this.selectedProducto = prod;
        this.modalPrecios = true;
    },

    get nuevoStockCalculado() {
        if (!this.selectedProducto) return 0;
        let actual = parseInt(this.selectedProducto.stock) || 0;
        let cant = parseInt(this.cantidadAjuste) || 0;
        return this.tipoMovimiento === 'entrada' ? (actual + cant) : Math.max(0, actual - cant);
    }
}" class="space-y-6">

    {{-- ==================== ALERTAS Y MENSAJES ==================== --}}
    @if(session('Mensaje'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-emerald-900">Operación Exitosa</h4>
                    <p class="text-xs font-medium text-emerald-700">{{ session('Mensaje') }}</p>
                </div>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-emerald-700 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 flex items-start gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-xs font-black text-rose-900">Atención</h4>
                <ul class="mt-1 list-disc list-inside text-xs text-rose-700 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ==================== FILA 1: 4 TARJETAS KPI (ESTILO EXACTO) ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Tarjeta 1: Total unidades --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-100/80 shadow-xs flex items-center gap-4 hover:shadow-md transition">
            <div class="w-13 h-13 rounded-2xl bg-[#eef4ff] text-[#3b82f6] flex items-center justify-center text-xl shrink-0 shadow-2xs border border-blue-100/50">
                <i class="fa-solid fa-cubes"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-500 block leading-tight">Total unidades</span>
                <p class="text-2xl font-black text-slate-900 tracking-tight mt-1 leading-none">
                    {{ number_format($unidadesTotales, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Tarjeta 2: Valor inventario --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-100/80 shadow-xs flex items-center gap-4 hover:shadow-md transition">
            <div class="w-13 h-13 rounded-2xl bg-[#ecfdf5] text-[#10b981] flex items-center justify-center text-xl shrink-0 shadow-2xs border border-emerald-100/50">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-500 block leading-tight">Valor inventario</span>
                <p class="text-2xl font-black text-[#10b981] tracking-tight mt-1 leading-none">
                    ${{ number_format($valorInventarioVenta, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Tarjeta 3: Stock crítico --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-100/80 shadow-xs flex items-center gap-4 hover:shadow-md transition">
            <div class="w-13 h-13 rounded-2xl bg-[#fffbeb] text-[#f59e0b] flex items-center justify-center text-xl shrink-0 shadow-2xs border border-amber-100/50">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-500 block leading-tight">Stock crítico</span>
                <p class="text-2xl font-black text-[#f59e0b] tracking-tight mt-1 leading-none">
                    {{ $stockCritico }}
                </p>
            </div>
        </div>

        {{-- Tarjeta 4: Agotados --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-100/80 shadow-xs flex items-center gap-4 hover:shadow-md transition">
            <div class="w-13 h-13 rounded-2xl bg-[#fef2f2] text-[#ef4444] flex items-center justify-center text-xl shrink-0 shadow-2xs border border-rose-100/50">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-500 block leading-tight">Agotados</span>
                <p class="text-2xl font-black text-[#ef4444] tracking-tight mt-1 leading-none">
                    {{ $stockAgotado }}
                </p>
            </div>
        </div>

    </div>

    {{-- ==================== FILA 2: ESTRUCTURA DE 2 COLUMNAS ==================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        {{-- ==================== COLUMNA IZQUIERDA: STOCK ACTUAL (7 DE 12) ==================== --}}
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
            
            {{-- Header de la sección Stock Actual --}}
            <div class="p-6 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    
                    {{-- Título y Subtítulo --}}
                    <div>
                        <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <span class="text-emerald-500 text-lg">📦</span>
                            <span>Stock actual</span>
                        </h2>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">
                            {{ $stockOptimo }} saludables · {{ $stockCritico }} críticos · {{ $stockAgotado }} agotados
                        </p>
                    </div>

                    {{-- Píldoras de Filtro (Todos / Críticos / Agotados) --}}
                    <div class="flex items-center gap-1.5 self-start sm:self-auto">
                        <a href="{{ route('admin.inventario.index', array_merge(request()->except('nivel', 'page'), ['nivel' => 'todos'])) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ !request('nivel') || request('nivel') === 'todos' ? 'bg-[#0f172a] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Todos
                        </a>
                        <a href="{{ route('admin.inventario.index', array_merge(request()->except('nivel', 'page'), ['nivel' => 'bajo'])) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ request('nivel') === 'bajo' ? 'bg-amber-500 text-white border-amber-500 shadow-xs' : 'border-amber-400 text-amber-600 hover:bg-amber-50' }}">
                            Críticos
                        </a>
                        <a href="{{ route('admin.inventario.index', array_merge(request()->except('nivel', 'page'), ['nivel' => 'agotado'])) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ request('nivel') === 'agotado' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'border-rose-300 text-rose-600 hover:bg-rose-50' }}">
                            Agotados
                        </a>
                    </div>

                </div>

                {{-- Buscador de Producto --}}
                <div class="mt-4">
                    <form method="GET" action="{{ route('admin.inventario.index') }}">
                        @if(request('nivel'))
                            <input type="hidden" name="nivel" value="{{ request('nivel') }}">
                        @endif
                        <div class="relative">
                            <input type="text" 
                                   name="q" 
                                   value="{{ request('q') }}" 
                                   placeholder="Buscar producto..."
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-500/10 transition">
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabla de Stock Actual --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] font-extrabold tracking-wider text-slate-400 uppercase border-b border-slate-100 bg-slate-50/40">
                            <th class="px-6 py-3.5">PRODUCTO</th>
                            <th class="px-6 py-3.5 text-center">STOCK</th>
                            <th class="px-6 py-3.5">VALOR</th>
                            <th class="px-6 py-3.5">ESTADO</th>
                            <th class="px-6 py-3.5 text-right">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($productos as $prod)
                            @php
                                $stock = (int)$prod->stock;
                                $precio = (float)$prod->precio;
                                $valorTotal = $stock * $precio;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition group">
                                
                                {{-- Producto: Nombre en negrita + Categoría abajo en mayúscula --}}
                                <td class="px-6 py-4">
                                    <div class="min-w-0">
                                        <p class="font-extrabold text-slate-900 text-xs">{{ $prod->nombre }}</p>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase block mt-0.5 tracking-wider">
                                            {{ $prod->categoria->nombre_categoria ?? 'SIN CATEGORÍA' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Stock: Píldora circular/ovalada coloreada --}}
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($stock <= 0)
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black bg-[#dc2626] text-white shadow-2xs min-w-[34px]">
                                            0
                                        </span>
                                    @elseif($stock <= 5)
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black bg-[#d97706] text-white shadow-2xs min-w-[34px]">
                                            {{ $stock }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black bg-[#0f6848] text-white shadow-2xs min-w-[34px]">
                                            {{ $stock }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Valor: Formato Moneda --}}
                                <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                                    ${{ number_format($valorTotal, 0, ',', '.') }}
                                </td>

                                {{-- Estado: Píldora suave con icono --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($stock <= 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#fef2f2] text-[#ef4444] border border-rose-200 shadow-2xs">
                                            <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                            <span>Agotado</span>
                                        </span>
                                    @elseif($stock <= 5)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#fffbeb] text-[#d97706] border border-amber-200 shadow-2xs">
                                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                            <span>Crítico</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#ecfdf5] text-[#059669] border border-emerald-200 shadow-2xs">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                            <span>Disponible</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones Rápidas (+ / - / Ajustar) --}}
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" 
                                                @click="abrirAjuste({{ json_encode($prod) }}, 'entrada')"
                                                class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white transition flex items-center justify-center shadow-2xs cursor-pointer"
                                                title="Entrada (+)">
                                            <i class="fa-solid fa-plus text-[10px]"></i>
                                        </button>

                                        <button type="button" 
                                                @click="abrirAjuste({{ json_encode($prod) }}, 'salida')"
                                                class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-600 text-amber-600 hover:text-white transition flex items-center justify-center shadow-2xs cursor-pointer"
                                                title="Salida (-)">
                                            <i class="fa-solid fa-minus text-[10px]"></i>
                                        </button>

                                        <button type="button" 
                                                @click="abrirPrecios({{ json_encode($prod) }})"
                                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-violet-600 text-slate-600 hover:text-white transition flex items-center justify-center shadow-2xs cursor-pointer"
                                                title="Ajustar Precios">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-300"></i>
                                    <p class="text-xs font-bold text-slate-600">No se encontraron productos con estos filtros.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación de 10 en 10 --}}
            @if($productos->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $productos->links() }}
                </div>
            @endif

        </div>

        {{-- ==================== COLUMNA DERECHA: ÚLTIMOS MOVIMIENTOS (5 DE 12) ==================== --}}
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
            
            {{-- Header de Últimos Movimientos --}}
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span class="text-blue-500 text-lg">⇄</span>
                        <span>Últimos movimientos</span>
                    </h2>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">
                        Entradas, salidas y ajustes recientes
                    </p>
                </div>

                <a href="{{ route('admin.inventario.movimientos') }}" 
                   class="text-xs font-bold text-violet-600 hover:text-violet-800 flex items-center gap-1">
                    <span>Ver todo</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            {{-- Tabla / Lista de Movimientos --}}
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] font-extrabold tracking-wider text-slate-400 uppercase border-b border-slate-100 bg-slate-50/40 sticky top-0 bg-white">
                            <th class="px-5 py-3.5">PRODUCTO</th>
                            <th class="px-5 py-3.5 text-center">TIPO</th>
                            <th class="px-5 py-3.5 text-center">CANT.</th>
                            <th class="px-5 py-3.5 text-right">FECHA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($movimientosRecientes as $mov)
                            <tr class="hover:bg-slate-50/60 transition">
                                
                                {{-- Producto y Motivo --}}
                                <td class="px-5 py-3.5">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-xs truncate max-w-[150px]">
                                            {{ $mov->producto->nombre ?? 'Producto' }}
                                        </p>
                                        <span class="text-[10px] text-slate-400 font-medium truncate max-w-[150px] block mt-0.5" title="{{ $mov->motivo }}">
                                            {{ $mov->motivo }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Tipo: Píldora con Flecha --}}
                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    @if($mov->tipo === 'entrada')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-[#dcfce7] text-[#15803d] shadow-2xs">
                                            <i class="fa-solid fa-arrow-down text-[10px]"></i>
                                            <span>Entrada</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-[#fee2e2] text-[#b91c1c] shadow-2xs">
                                            <i class="fa-solid fa-arrow-up text-[10px]"></i>
                                            <span>Salida</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Cantidad en negrita --}}
                                <td class="px-5 py-3.5 text-center font-extrabold text-xs text-slate-900 whitespace-nowrap">
                                    {{ $mov->cantidad }}
                                </td>

                                {{-- Fecha formateada d/m H:i --}}
                                <td class="px-5 py-3.5 text-right text-slate-500 text-[11px] font-medium whitespace-nowrap">
                                    {{ $mov->created_at ? $mov->created_at->format('d/m H:i') : '-' }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-clock-rotate-left text-2xl mb-1 text-slate-300"></i>
                                    <p class="text-xs font-bold">Sin movimientos recientes.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer con Botón para Registrar Nuevo Movimiento --}}
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <span class="text-[11px] text-slate-400 font-semibold">{{ $movimientosRecientes->count() }} movimientos recientes</span>
                <button type="button" 
                        @click="abrirAjuste(null, 'entrada')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#0f172a] hover:bg-black text-white font-bold text-xs transition shadow-xs cursor-pointer">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Nuevo Movimiento</span>
                </button>
            </div>

        </div>

    </div>

    {{-- ==================== MODAL: AJUSTE DE STOCK (ENTRADA / SALIDA) ==================== --}}
    <div x-show="modalAjuste" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
         @click.self="modalAjuste = false"
         @keydown.escape.window="modalAjuste = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-100 relative my-8" @click.stop>
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Registrar Movimiento de Stock</h3>
                        <p class="text-[10px] text-slate-400">Actualiza existencias físicas y guarda en Kardex</p>
                    </div>
                </div>
                <button type="button" @click="modalAjuste = false" class="text-slate-400 hover:text-slate-700 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.inventario.ajuste') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Selección de Producto --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Producto *</label>
                    <template x-if="selectedProducto">
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <input type="hidden" name="producto_id" :value="selectedProducto.id">
                                <p class="text-xs font-bold text-slate-900" x-text="selectedProducto.nombre"></p>
                                <span class="text-[10px] text-slate-500">Stock actual: <strong class="text-slate-800" x-text="selectedProducto.stock + ' unid.'"></strong></span>
                            </div>
                            <button type="button" @click="selectedProducto = null" class="text-[10px] text-violet-600 hover:underline font-bold">
                                Cambiar
                            </button>
                        </div>
                    </template>

                    <template x-if="!selectedProducto">
                        <select name="producto_id" 
                                required
                                @change="selectedProducto = {{ json_encode($todosProductos) }}.find(p => p.id == $event.target.value)"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-violet-500/20 focus:border-violet-600 transition">
                            <option value="">-- Selecciona un producto --</option>
                            @foreach($todosProductos as $tp)
                                <option value="{{ $tp->id }}">{{ $tp->nombre }} (Stock: {{ $tp->stock }})</option>
                            @endforeach
                        </select>
                    </template>
                </div>

                {{-- Tipo de Movimiento (Entrada / Salida) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Tipo de Movimiento *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" 
                                @click="tipoMovimiento = 'entrada'"
                                :class="tipoMovimiento === 'entrada' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="py-2.5 px-3 rounded-xl border font-bold text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                            <i class="fa-solid fa-arrow-down text-xs"></i>
                            <span>Entrada (+)</span>
                        </button>

                        <button type="button" 
                                @click="tipoMovimiento = 'salida'"
                                :class="tipoMovimiento === 'salida' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="py-2.5 px-3 rounded-xl border font-bold text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                            <i class="fa-solid fa-arrow-up text-xs"></i>
                            <span>Salida (-)</span>
                        </button>
                    </div>
                    <input type="hidden" name="tipo" :value="tipoMovimiento">
                </div>

                {{-- Cantidad y Vista Previa de Nuevo Stock --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Cantidad *</label>
                        <input type="number" 
                               name="cantidad" 
                               x-model.number="cantidadAjuste" 
                               min="1" 
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-black text-slate-900 focus:ring-2 focus:ring-violet-500/20 focus:border-violet-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase">Nuevo Stock</label>
                        <div class="px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-black text-slate-900 flex items-center justify-between">
                            <span x-text="nuevoStockCalculado + ' unid.'"></span>
                            <span class="text-[10px] font-bold" :class="tipoMovimiento === 'entrada' ? 'text-emerald-600' : 'text-rose-600'" x-text="tipoMovimiento === 'entrada' ? '(+' + cantidadAjuste + ')' : '(-' + cantidadAjuste + ')'"></span>
                        </div>
                    </div>
                </div>

                {{-- Motivo del Ajuste --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Motivo / Justificación *</label>
                    <input type="text" 
                           name="motivo" 
                           x-model="motivoAjuste" 
                           placeholder="Ej. Recepción proveedor, Conteo físico, Merma..." 
                           required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-violet-500/20 focus:border-violet-600 transition">
                    
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <template x-if="tipoMovimiento === 'entrada'">
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" @click="motivoAjuste = 'Recepción de proveedor'" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600">
                                    📦 Recepción Proveedor
                                </button>
                                <button type="button" @click="motivoAjuste = 'Ajuste admin'" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600">
                                    🔧 Ajuste Admin
                                </button>
                            </div>
                        </template>
                        <template x-if="tipoMovimiento === 'salida'">
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" @click="motivoAjuste = 'Compra cliente'" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600">
                                    🛒 Compra Cliente
                                </button>
                                <button type="button" @click="motivoAjuste = 'Merma / Producto dañado'" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600">
                                    ⚠️ Merma / Daño
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" 
                            @click="modalAjuste = false" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs shadow-md transition">
                        Guardar Movimiento
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- ==================== MODAL: EDITAR COSTO Y PRECIO ==================== --}}
    <div x-show="modalPrecios" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
         @click.self="modalPrecios = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-sm w-full shadow-2xl border border-slate-100 relative" @click.stop>
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-dollar-sign text-emerald-600"></i>
                    <span>Actualizar Costos & Precios</span>
                </h3>
                <button type="button" @click="modalPrecios = false" class="text-slate-400 hover:text-slate-700 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <template x-if="selectedProducto">
                <form :action="'{{ url('/admin/inventario') }}/' + selectedProducto.id + '/precios'" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                        <p class="text-xs font-bold text-slate-900 truncate" x-text="selectedProducto.nombre"></p>
                        <span class="text-[10px] text-slate-500">ID: #<span x-text="selectedProducto.id"></span></span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase">Costo de Compra ($ COP) *</label>
                        <input type="number" 
                               name="precio_compra" 
                               x-model.number="selectedProducto.precio_compra" 
                               min="0" 
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase">Precio de Venta ($ COP) *</label>
                        <input type="number" 
                               name="precio" 
                               x-model.number="selectedProducto.precio" 
                               min="0" 
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-black text-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                    </div>

                    <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-800 font-bold">Margen Bruto Calculado:</span>
                        <span class="font-black text-emerald-700" 
                              x-text="selectedProducto.precio > 0 ? Math.round(((selectedProducto.precio - selectedProducto.precio_compra) / selectedProducto.precio) * 100) + '%' : '0%'">
                        </span>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="modalPrecios = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs">
                            Actualizar Precios
                        </button>
                    </div>

                </form>
            </template>

        </div>
    </div>

</div>
@endsection
