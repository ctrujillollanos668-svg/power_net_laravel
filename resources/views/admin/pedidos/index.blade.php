@extends('layouts.sidebaradmin')

@section('title', 'Gestión de Pedidos')

@section('content')
<div
    x-data="adminPedidosManager()"
    class="space-y-6">

    {{-- ==================== ENCABEZADO ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white flex items-center gap-2.5 transition-colors">
                <span class="text-2xl">📦</span>
                Gestión de Pedidos
            </h1>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 transition-colors">Supervisa, actualiza estados y despacha las compras realizadas en la tienda</p>
        </div>
    </div>

    {{-- ==================== MENSAJES FLASH ==================== --}}
    @if(session('success'))
        <div class="px-4 py-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-300 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs transition">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    {{-- ==================== TARJETAS DE MÉTRICAS ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total Pedidos --}}
        <div class="bg-white dark:bg-slate-900/90 rounded-2xl p-5 border border-gray-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-bold text-gray-400 dark:text-slate-400 block uppercase">Total Pedidos</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white mt-0.5 block">{{ $totalPedidos }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-violet-50 dark:bg-violet-950/40 text-[#7c3aed] dark:text-violet-400 flex items-center justify-center text-xl font-bold border border-violet-100 dark:border-violet-800/40 shadow-2xs">
                📦
            </div>
        </div>

        {{-- En Preparación --}}
        <div class="bg-white dark:bg-slate-900/90 rounded-2xl p-5 border border-gray-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-bold text-amber-500 dark:text-amber-400 block uppercase">En Preparación</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white mt-0.5 block">{{ $pedidosPendientes }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold border border-amber-100 dark:border-amber-800/40 shadow-2xs">
                ⏳
            </div>
        </div>

        {{-- Enviados --}}
        <div class="bg-white dark:bg-slate-900/90 rounded-2xl p-5 border border-gray-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-bold text-blue-500 dark:text-blue-400 block uppercase">Enviados</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white mt-0.5 block">{{ $pedidosEnviados }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-bold border border-blue-100 dark:border-blue-800/40 shadow-2xs">
                🚚
            </div>
        </div>

        {{-- Total Ventas --}}
        <div class="bg-white dark:bg-slate-900/90 rounded-2xl p-5 border border-gray-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block uppercase">Total Facturado</span>
                <span class="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-0.5 block">${{ number_format($totalVentas, 0, ',', '.') }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold border border-emerald-100 dark:border-emerald-800/40 shadow-2xs">
                💰
            </div>
        </div>

    </div>

    {{-- ==================== FILTROS Y BÚSQUEDA ==================== --}}
    <div class="bg-white dark:bg-slate-900/90 rounded-2xl p-4 border border-gray-200/80 dark:border-slate-800 shadow-xs transition-colors">
        <form method="GET" action="{{ route('admin.pedidos.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            
            {{-- Buscador --}}
            <div class="sm:col-span-6 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 text-xs"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar por # pedido, cliente, cédula o factura..." 
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100 text-xs focus:ring-1 focus:ring-[#7c3aed] placeholder:text-gray-400 dark:placeholder:text-slate-500 transition">
            </div>

            {{-- Filtro Estado Pedido --}}
            <div class="sm:col-span-3">
                <select name="estado" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100 px-3 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed] transition">
                    <option value="todos">Todos los Estados</option>
                    <option value="En preparación" {{ request('estado') === 'En preparación' ? 'selected' : '' }}>⏳ En preparación</option>
                    <option value="Enviado" {{ request('estado') === 'Enviado' ? 'selected' : '' }}>🚚 Enviado</option>
                    <option value="Entregado" {{ request('estado') === 'Entregado' ? 'selected' : '' }}>✅ Entregado</option>
                    <option value="Cancelado" {{ request('estado') === 'Cancelado' ? 'selected' : '' }}>❌ Cancelado</option>
                </select>
            </div>

            {{-- Botón Filtrar y Limpiar --}}
            <div class="sm:col-span-3 flex items-center gap-2">
                <button 
                    type="submit" 
                    class="flex-1 py-2.5 px-4 bg-[#0f172a] dark:bg-violet-600 hover:bg-black dark:hover:bg-violet-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                    Filtrar
                </button>
                @if(request()->hasAny(['q', 'estado', 'pago']))
                    <a href="{{ route('admin.pedidos.index') }}" class="p-2.5 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-600 dark:text-slate-300 rounded-xl transition font-bold" title="Limpiar filtros">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- ==================== TABLA DE PEDIDOS ==================== --}}
    <div class="bg-white dark:bg-slate-900/90 rounded-2xl border border-gray-200/80 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 dark:text-slate-300">
                <thead class="bg-gray-50/80 dark:bg-slate-900 text-[11px] uppercase tracking-wider text-gray-500 dark:text-slate-400 border-b border-gray-200/70 dark:border-slate-800 transition-colors">
                    <tr>
                        <th class="px-6 py-4 font-bold">Pedido</th>
                        <th class="px-6 py-4 font-bold">Cliente</th>
                        <th class="px-6 py-4 font-bold">Fecha</th>
                        <th class="px-6 py-4 font-bold">Total</th>
                        <th class="px-6 py-4 font-bold">Pago</th>
                        <th class="px-6 py-4 font-bold">Estado Pedido</th>
                        <th class="px-6 py-4 font-bold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 font-medium bg-white dark:bg-slate-900/60 transition-colors">
                    @forelse($pedidos as $pedido)
                        @php
                            $pedidoData = [
                                'id' => $pedido->id,
                                'id_padded' => str_pad($pedido->id, 5, '0', STR_PAD_LEFT),
                                'factura' => $pedido->pago->factura ?? 'FAC-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT),
                                'fecha' => $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                                'total' => $pedido->total_pedido,
                                'total_formateado' => number_format($pedido->total_pedido, 0, ',', '.'),
                                'estado_pedido' => $pedido->estado_pedido ?? 'En preparación',
                                'metodo_pago' => $pedido->pago->metodo_pago ?? 'Tarjeta',
                                'estado_pago' => $pedido->pago->estado_pago ?? 'Aprobado',
                                'empresa_envios' => $pedido->envio->empresa_envios ?? 'Servientrega Express',
                                'costo_envio' => $pedido->envio->costo ?? 0,
                                'direccion_envio' => $pedido->envio->direccion_envio ?? $pedido->cliente->direccion ?? 'Dirección no registrada',
                                'cliente_nombre' => $pedido->cliente->persona->nombre_persona ?? 'Cliente',
                                'cliente_doc' => $pedido->cliente->persona->documento ?? 'No registra',
                                'cliente_tel' => $pedido->cliente->persona->telefono ?? 'No registra',
                                'items' => $pedido->detalles->map(function($d) {
                                    return [
                                        'nombre' => $d->producto->nombre ?? 'Producto',
                                        'cantidad' => $d->cantidad,
                                        'precio_unitario' => number_format($d->precio_unitario, 0, ',', '.'),
                                        'subtotal' => number_format($d->subtotal, 0, ',', '.'),
                                    ];
                                })
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-slate-800/60 transition">
                            
                            {{-- Pedido e Imágenes --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex -space-x-2 shrink-0">
                                        @forelse($pedido->detalles->take(2) as $d)
                                            @php
                                                $img = $d->producto && $d->producto->imagenes && $d->producto->imagenes->first() ? $d->producto->imagenes->first()->imagen : null;
                                            @endphp
                                            <div class="w-9 h-9 rounded-xl bg-gray-50 dark:bg-slate-800 border-2 border-white dark:border-slate-700 shadow-2xs p-0.5 flex items-center justify-center overflow-hidden shrink-0" title="{{ $d->producto->nombre ?? 'Producto' }}">
                                                @if($img && file_exists(public_path('imagenes_productos/' . $img)))
                                                    <img src="{{ asset('imagenes_productos/' . $img) }}" alt="{{ $d->producto->nombre }}" class="max-h-full max-w-full object-contain">
                                                @else
                                                    <span class="text-xs">💡</span>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="w-9 h-9 rounded-xl bg-gray-50 dark:bg-slate-800 border-2 border-white dark:border-slate-700 shadow-2xs flex items-center justify-center text-xs">
                                                📦
                                            </div>
                                        @endforelse
                                    </div>
                                    <div>
                                        <span class="font-black text-gray-950 dark:text-white text-sm block">#{{ $pedido->id }}</span>
                                        <span class="text-[10px] text-gray-400 dark:text-slate-400 font-bold block">{{ $pedido->pago->factura ?? 'FAC-0000' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Cliente --}}
                            <td class="px-6 py-4">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-slate-100 block text-xs">{{ $pedido->cliente->persona->nombre_persona ?? 'Cliente PowerNet' }}</span>
                                    <span class="text-[11px] text-gray-400 dark:text-slate-400 block">{{ $pedido->cliente->persona->telefono ?? 'Sin tel' }}</span>
                                </div>
                            </td>

                            {{-- Fecha --}}
                            <td class="px-6 py-4 text-[11px] text-gray-600 dark:text-slate-400">
                                {{ $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y H:i') : now()->format('d/m/Y') }}
                            </td>

                            {{-- Total --}}
                            <td class="px-6 py-4">
                                <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm block">
                                    ${{ number_format($pedido->total_pedido, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-gray-400 dark:text-slate-400">{{ $pedido->detalles->count() }} item(s)</span>
                            </td>

                            {{-- Pago --}}
                            <td class="px-6 py-4">
                                <div>
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black text-white 
                                        {{ $pedido->pago && $pedido->pago->estado_pago === 'Aprobado' ? 'bg-emerald-600 dark:bg-emerald-700' : 'bg-amber-500 dark:bg-amber-600' }}">
                                        {{ $pedido->pago->estado_pago ?? 'Aprobado' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 dark:text-slate-400 font-semibold block mt-0.5 lowercase">
                                        {{ $pedido->pago->metodo_pago ?? 'tarjeta' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Estado Pedido --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $est = strtolower($pedido->estado_pedido ?? 'en preparación');
                                @endphp
                                @if(str_contains($est, 'cancel'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800/40 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        Cancelado
                                    </span>
                                @elseif(str_contains($est, 'enviado') || str_contains($est, 'camino'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/80 dark:border-blue-800/40 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        Enviado
                                    </span>
                                @elseif(str_contains($est, 'entreg'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/40 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        Entregado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/40 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                                        En preparación
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    {{-- 1. Botón Editar Estado / Envío --}}
                                    <button
                                        type="button"
                                        @click="abrirEditarEstado({{ json_encode($pedidoData) }})"
                                        class="px-2.5 py-1.5 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-violet-100 dark:hover:bg-violet-900/50 text-gray-700 dark:text-slate-300 hover:text-violet-900 dark:hover:text-violet-200 font-bold text-xs transition inline-flex items-center gap-1 cursor-pointer"
                                        title="Cambiar Estado / Transportadora">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        <span>Estado</span>
                                    </button>

                                    {{-- 3. Cancelar Pedido --}}
                                    <form action="{{ route('admin.pedidos.eliminar', $pedido->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas cancelar este pedido #{{ $pedido->id }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-red-100 dark:hover:bg-red-950/60 text-gray-500 dark:text-slate-400 hover:text-red-700 dark:hover:text-red-300 flex items-center justify-center transition cursor-pointer"
                                            title="Cancelar Pedido">
                                            <i class="fa-solid fa-ban text-xs"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-gray-300 dark:text-slate-600"></i>
                                <p class="text-xs font-bold">No se encontraron pedidos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($pedidos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50">
                {{ $pedidos->links() }}
            </div>
        @endif
    </div>

    {{-- ==================== MODAL CAMBIAR ESTADO / ENVÍO ==================== --}}
    <div
        x-show="modalEditar"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4"
        @click.self="modalEditar = false">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-gray-100 dark:border-slate-800 relative">
            <div class="flex items-center justify-between pb-3 mb-5 border-b border-gray-100 dark:border-slate-800">
                <h3 class="text-sm font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <span>✏️</span>
                    <span>Actualizar Pedido <strong class="text-[#7c3aed] dark:text-violet-400" x-text="editData ? '#' + editData.id : ''"></strong></span>
                </h3>
                <button type="button" @click="modalEditar = false" class="text-gray-400 hover:text-gray-600 dark:text-slate-400 dark:hover:text-white cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form :action="editUrl" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PATCH')

                {{-- Estado del Pedido --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Estado del Pedido *</label>
                    <select name="estado_pedido" x-model="editData.estado_pedido" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100 p-2.5 text-xs font-bold focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="En preparación">⏳ En preparación</option>
                        <option value="Enviado">🚚 Enviado</option>
                        <option value="Entregado">✅ Entregado</option>
                        <option value="Cancelado">❌ Cancelado</option>
                    </select>
                </div>

                {{-- Estado del Pago --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Estado del Pago</label>
                    <select name="estado_pago" x-model="editData.estado_pago" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100 p-2.5 text-xs font-bold focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="Aprobado">✅ Aprobado / Pagado</option>
                        <option value="Pendiente al entregar">💵 Pendiente al entregar (Contra entrega)</option>
                        <option value="Pendiente">⏳ Pendiente de Verificación</option>
                        <option value="Rechazado">❌ Rechazado</option>
                    </select>
                </div>

                {{-- Empresa de Envíos --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Empresa Transportadora</label>
                    <input type="text" name="empresa_envios" x-model="editData.empresa_envios" placeholder="Ej. Servientrega Express, Coordinadora, Envía..." class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100 p-2.5 text-xs placeholder:text-gray-400 dark:placeholder:text-slate-500">
                </div>

                {{-- Dirección de Entrega --}}
                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Dirección de Entrega</label>
                    <input type="text" name="direccion_envio" x-model="editData.direccion_envio" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100 p-2.5 text-xs">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-slate-800">
                    <button type="button" @click="modalEditar = false" class="px-4 py-2 rounded-xl border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 font-bold text-xs hover:bg-gray-50 dark:hover:bg-slate-800 cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#0f172a] dark:bg-violet-600 hover:bg-black dark:hover:bg-violet-700 text-white font-bold text-xs shadow-xs cursor-pointer">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Fin Contenedor --}}
</div>

<script>
function adminPedidosManager() {
    return {
        modalEditar: false,
        editData: {},
        editUrl: '',

        abrirEditarEstado(pedido) {
            this.editData = Object.assign({}, pedido);
            this.editUrl = '{{ url('/admin/pedidos') }}/' + pedido.id + '/estado';
            this.modalEditar = true;
        }
    };
}
</script>
@endsection
