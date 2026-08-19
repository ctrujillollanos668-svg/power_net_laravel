@extends('layouts.sidebaradmin')

@section('title', 'Gestión de Devoluciones y Garantías')

@section('content')
<div
    x-data="adminDevolucionesManager()"
    class="space-y-6">

    {{-- ==================== ENCABEZADO ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 flex items-center gap-2.5">
                <span class="text-2xl">🔄</span>
                Devoluciones y Garantías
            </h1>
            <p class="text-xs text-gray-500 mt-1">Supervisa solicitudes de reembolso, garantías de productos y reposición de inventario</p>
        </div>

        <div class="flex items-center gap-2">
            <button 
                type="button" 
                @click="modalCrear = true" 
                class="px-4 py-2.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus text-yellow-400"></i>
                <span>Registrar Devolución</span>
            </button>
        </div>
    </div>

    {{-- ==================== MENSAJES FLASH ==================== --}}
    @if(session('success'))
        <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    {{-- ==================== TARJETAS DE MÉTRICAS ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total Solicitudes --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-400 block uppercase">Total Solicitudes</span>
                <span class="text-2xl font-black text-gray-900 mt-0.5 block">{{ $totalDevoluciones }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-violet-50 text-[#7c3aed] flex items-center justify-center text-xl font-bold border border-violet-100">
                🔄
            </div>
        </div>

        {{-- Pendientes por Revisar --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-500 block uppercase">Por Revisar</span>
                <span class="text-2xl font-black text-amber-600 mt-0.5 block">{{ $pendientesRevision }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold border border-amber-100">
                ⏳
            </div>
        </div>

        {{-- Aprobadas --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-600 block uppercase">Aprobadas / Éxito</span>
                <span class="text-2xl font-black text-emerald-700 mt-0.5 block">{{ $aprobadas }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                ✅
            </div>
        </div>

        {{-- Total Reembolsado --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-red-500 block uppercase">Total Reembolsado</span>
                <span class="text-xl font-black text-red-600 mt-0.5 block">${{ number_format($totalReembolsado, 0, ',', '.') }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl font-bold border border-red-100">
                💸
            </div>
        </div>

    </div>

    {{-- ==================== FILTROS Y BÚSQUEDA ==================== --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.devoluciones.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            
            {{-- Buscador --}}
            <div class="sm:col-span-6 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar por # devolución, # pedido, cliente o motivo..." 
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-1 focus:ring-[#7c3aed]">
            </div>

            {{-- Filtro Estado --}}
            <div class="sm:col-span-3">
                <select name="estado" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                    <option value="todos">Todos los Estados</option>
                    <option value="Pendiente" {{ request('estado') === 'Pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                    <option value="Aprobada" {{ request('estado') === 'Aprobada' ? 'selected' : '' }}>✅ Aprobada</option>
                    <option value="Completada" {{ request('estado') === 'Completada' ? 'selected' : '' }}>🎉 Completada</option>
                    <option value="Rechazada" {{ request('estado') === 'Rechazada' ? 'selected' : '' }}>❌ Rechazada</option>
                </select>
            </div>

            {{-- Botón Filtrar y Limpiar --}}
            <div class="sm:col-span-3 flex items-center gap-2">
                <button 
                    type="submit" 
                    class="flex-1 py-2.5 px-4 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl transition shadow-xs">
                    Filtrar
                </button>
                @if(request()->hasAny(['q', 'estado']))
                    <a href="{{ route('admin.devoluciones.index') }}" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition font-bold" title="Limpiar filtros">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- ==================== TABLA DE DEVOLUCIONES ==================== --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50/80 text-[11px] uppercase tracking-wider text-gray-500 border-b border-gray-200/70">
                    <tr>
                        <th class="px-6 py-4 font-bold"># Devolución / Pedido</th>
                        <th class="px-6 py-4 font-bold">Cliente</th>
                        <th class="px-6 py-4 font-bold">Fecha</th>
                        <th class="px-6 py-4 font-bold">Motivo de Garantía / Reclamo</th>
                        <th class="px-6 py-4 font-bold">Monto Devolución</th>
                        <th class="px-6 py-4 font-bold">Estado</th>
                        <th class="px-6 py-4 font-bold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($devoluciones as $dev)
                        @php
                            $pedido = $dev->pedido;
                            $devData = [
                                'id' => $dev->id,
                                'pedido_id' => $dev->pedido_id,
                                'fecha' => $dev->fecha_devolucion ? $dev->fecha_devolucion->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                                'motivo' => $dev->motivo,
                                'monto' => $dev->monto_devolucion,
                                'monto_formateado' => number_format($dev->monto_devolucion, 0, ',', '.'),
                                'estado' => $dev->estado,
                                'motivo_rechazo' => $dev->motivo_rechazo,
                                'cliente_nombre' => $pedido && $pedido->cliente && $pedido->cliente->persona ? $pedido->cliente->persona->nombre_persona : 'Consumidor Final',
                                'cliente_tel' => $pedido && $pedido->cliente && $pedido->cliente->persona ? $pedido->cliente->persona->telefono : 'No registra',
                                'items' => $dev->detalles->map(function($d) {
                                    $p = $d->producto;
                                    $foto = $p && $p->imagenes->first() ? $p->imagenes->first()->imagen : null;
                                    return [
                                        'nombre' => $p->nombre ?? 'Producto',
                                        'cantidad' => $d->cantidad ?? 1,
                                        'motivo' => $d->motivo,
                                        'foto' => $foto ? asset('imagenes_productos/' . $foto) : null,
                                    ];
                                })
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition">
                            
                            {{-- # Devolución / Pedido --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-black text-xs shrink-0 border border-amber-100">
                                        🔄
                                    </div>
                                    <div>
                                        <span class="font-black text-gray-950 text-sm block">Devolución #{{ $dev->id }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold block">Pedido #{{ $dev->pedido_id }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Cliente --}}
                            <td class="px-6 py-4">
                                <div>
                                    <span class="font-bold text-gray-900 block text-xs">
                                        {{ $pedido && $pedido->cliente && $pedido->cliente->persona ? $pedido->cliente->persona->nombre_persona : 'Consumidor Final' }}
                                    </span>
                                    <span class="text-[11px] text-gray-400 block">
                                        {{ $pedido && $pedido->cliente && $pedido->cliente->persona ? $pedido->cliente->persona->telefono : 'Sin tel' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Fecha --}}
                            <td class="px-6 py-4 text-[11px] text-gray-600">
                                {{ $dev->fecha_devolucion ? $dev->fecha_devolucion->format('d/m/Y H:i') : now()->format('d/m/Y') }}
                            </td>

                            {{-- Motivo --}}
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-xs text-gray-800 line-clamp-2" title="{{ $dev->motivo }}">
                                    {{ $dev->motivo }}
                                </p>
                                @if($dev->motivo_rechazo)
                                    <span class="text-[10px] text-red-600 font-bold block mt-1">Rechazo: {{ $dev->motivo_rechazo }}</span>
                                @endif
                            </td>

                            {{-- Monto --}}
                            <td class="px-6 py-4">
                                <span class="font-black text-red-600 text-sm block">
                                    ${{ number_format($dev->monto_devolucion, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-gray-400">COP</span>
                            </td>

                            {{-- Estado --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $est = strtolower($dev->estado ?? 'pendiente'); @endphp
                                @if(str_contains($est, 'rechaz'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        Rechazada
                                    </span>
                                @elseif(str_contains($est, 'aprob') || str_contains($est, 'complet'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        Aprobada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                                        Pendiente
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    {{-- 1. Ver Productos / Detalle (Ojo) --}}
                                    <button
                                        type="button"
                                        @click="abrirDetalleDevolucion({{ json_encode($devData) }})"
                                        class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-[#7c3aed] text-gray-600 hover:text-white transition flex items-center justify-center shadow-2xs cursor-pointer"
                                        title="Ver Productos y Detalle">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>

                                    {{-- 2. Aprobar Devolución (Si está pendiente) --}}
                                    @if($dev->estado === 'Pendiente')
                                        <form action="{{ route('admin.devoluciones.estado', $dev->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estado" value="Aprobada">
                                            <input type="hidden" name="reponer_stock" value="1">
                                            <button
                                                type="submit"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition inline-flex items-center gap-1 shadow-2xs cursor-pointer"
                                                title="Aprobar Devolución y Reponer Stock">
                                                <i class="fa-solid fa-check text-xs"></i>
                                                <span>Aprobar</span>
                                            </button>
                                        </form>

                                        {{-- 3. Rechazar Devolución --}}
                                        <button
                                            type="button"
                                            @click="abrirRechazarModal({{ json_encode($devData) }})"
                                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-red-100 text-gray-500 hover:text-red-700 transition flex items-center justify-center cursor-pointer"
                                            title="Rechazar Devolución">
                                            <i class="fa-solid fa-ban text-xs"></i>
                                        </button>
                                    @endif

                                    {{-- 4. Eliminar --}}
                                    <form action="{{ route('admin.devoluciones.destroy', $dev->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta devolución #{{ $dev->id }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-red-100 text-gray-400 hover:text-red-700 transition flex items-center justify-center"
                                            title="Eliminar Registro">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-rotate-left text-3xl mb-2 text-gray-300"></i>
                                <p class="text-xs font-bold">No hay devoluciones registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($devoluciones->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $devoluciones->links() }}
            </div>
        @endif
    </div>

    {{-- ==================== MODAL NUEVA DEVOLUCIÓN ==================== --}}
    <div 
        x-show="modalCrear" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="modalCrear = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-gray-100 relative my-8">
            <div class="flex items-center justify-between pb-3 mb-5 border-b border-gray-100">
                <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                    <span>🔄</span>
                    <span>Registrar Solicitud de Devolución</span>
                </h3>
                <button type="button" @click="modalCrear = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form action="{{ route('admin.devoluciones.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                {{-- Seleccionar Pedido --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Seleccionar Pedido *</label>
                    <select name="pedido_id" required class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-medium focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="">Selecciona un pedido...</option>
                        @foreach($pedidosRecientes as $p)
                            <option value="{{ $p->id }}">
                                Pedido #{{ $p->id }} - {{ $p->cliente->persona->nombre_persona ?? 'Cliente' }} (${{ number_format($p->total_pedido, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Motivo de Devolución --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Motivo / Reclamación *</label>
                    <textarea name="motivo" rows="3" required placeholder="Describe el motivo de la garantía o devolución..." class="w-full rounded-xl border border-gray-300 p-2.5 text-xs"></textarea>
                </div>

                {{-- Monto a Devolver --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Monto a Devolver ($ COP) *</label>
                    <input type="number" step="0.01" name="monto_devolucion" required placeholder="Ej. 150000" class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold">
                </div>

                {{-- Estado Inicial --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Estado Inicial *</label>
                    <select name="estado" class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold">
                        <option value="Pendiente">⏳ Pendiente de Revisión</option>
                        <option value="Aprobada">✅ Aprobada (Repone Stock)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="modalCrear = false" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-bold text-xs hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#0f172a] hover:bg-black text-white font-bold text-xs shadow-xs">
                        Guardar Devolución
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL VER PRODUCTOS / DETALLE ==================== --}}
    <div 
        x-show="modalDetalle" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="modalDetalle = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-gray-100 relative my-8" @click.stop x-show="devActiva">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                    <span>🔄</span>
                    <span>Devolución <strong class="text-[#7c3aed]" x-text="devActiva ? '#' + devActiva.id : ''"></strong></span>
                </h3>
                <button type="button" @click="modalDetalle = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="p-3 bg-gray-50 rounded-2xl space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-400 font-medium">Pedido Asociado:</span>
                        <span class="font-bold text-gray-900" x-text="devActiva ? '#' + devActiva.pedido_id : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 font-medium">Cliente:</span>
                        <span class="font-bold text-gray-900" x-text="devActiva ? devActiva.cliente_nombre : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 font-medium">Fecha:</span>
                        <span class="font-bold text-gray-900" x-text="devActiva ? devActiva.fecha : ''"></span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-gray-200">
                        <span class="text-gray-700 font-bold">Monto Reembolso:</span>
                        <span class="font-black text-red-600 text-sm" x-text="devActiva ? '$' + devActiva.monto_formateado + ' COP' : ''"></span>
                    </div>
                </div>

                {{-- Motivo --}}
                <div>
                    <span class="font-bold text-gray-700 block uppercase text-[10px] mb-1">Motivo / Reclamación:</span>
                    <p class="p-3 bg-amber-50/60 border border-amber-200 text-amber-900 rounded-xl leading-relaxed text-xs" x-text="devActiva ? devActiva.motivo : ''"></p>
                </div>

                {{-- Si hay motivo de rechazo --}}
                <template x-if="devActiva && devActiva.motivo_rechazo">
                    <div>
                        <span class="font-bold text-red-600 block uppercase text-[10px] mb-1">Motivo de Rechazo:</span>
                        <p class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl leading-relaxed text-xs" x-text="devActiva.motivo_rechazo"></p>
                    </div>
                </template>

                {{-- Productos Afectados --}}
                <template x-if="devActiva && devActiva.items && devActiva.items.length > 0">
                    <div>
                        <span class="font-bold text-gray-700 block uppercase text-[10px] mb-1">Productos en Devolución:</span>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            <template x-for="(item, idx) in devActiva.items" :key="idx">
                                <div class="p-2 bg-gray-50 rounded-xl flex items-center justify-between gap-2 border border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center p-0.5 overflow-hidden">
                                            <template x-if="item.foto">
                                                <img :src="item.foto" class="max-h-full max-w-full object-contain">
                                            </template>
                                            <template x-if="!item.foto">
                                                <span>💡</span>
                                            </template>
                                        </div>
                                        <span class="font-bold text-gray-800 text-xs truncate" x-text="item.nombre"></span>
                                    </div>
                                    <span class="font-black text-gray-900 shrink-0" x-text="item.cantidad + ' unid.'"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-5 pt-3 border-t border-gray-100 flex justify-end">
                <button type="button" @click="modalDetalle = false" class="px-5 py-2 bg-[#0f172a] text-white font-bold text-xs rounded-xl">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== MODAL RECHAZAR DEVOLUCIÓN ==================== --}}
    <div 
        x-show="modalRechazar" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4"
        @click.self="modalRechazar = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-gray-100 relative">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <h3 class="text-sm font-black text-red-600 flex items-center gap-2">
                    <span>❌</span>
                    <span>Rechazar Devolución <strong class="text-gray-900" x-text="rechazoData ? '#' + rechazoData.id : ''"></strong></span>
                </h3>
                <button type="button" @click="modalRechazar = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form :action="rechazoUrl" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PATCH')
                <input type="hidden" name="estado" value="Rechazada">

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Motivo del Rechazo de Garantía *</label>
                    <textarea name="motivo_rechazo" rows="3" required placeholder="Indica la razón por la que no procede la garantía o devolución..." class="w-full rounded-xl border border-gray-300 p-2.5 text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="modalRechazar = false" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-bold text-xs hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-xs">
                        Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function adminDevolucionesManager() {
    return {
        modalCrear: false,
        modalDetalle: false,
        modalRechazar: false,
        devActiva: null,
        rechazoData: null,
        rechazoUrl: '',

        abrirDetalleDevolucion(dev) {
            this.devActiva = dev;
            this.modalDetalle = true;
        },

        abrirRechazarModal(dev) {
            this.rechazoData = dev;
            this.rechazoUrl = '{{ url('/admin/devoluciones') }}/' + dev.id + '/estado';
            this.modalRechazar = true;
        }
    };
}
</script>
@endsection
