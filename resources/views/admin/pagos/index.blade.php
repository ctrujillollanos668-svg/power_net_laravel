@extends('layouts.sidebaradmin')

@section('title', 'Gestión de Pagos y Finanzas')

@section('content')
<div class="space-y-6">

    {{-- ==================== ENCABEZADO ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 flex items-center gap-2.5">
                <span class="text-2xl">💳</span>
                Control de Pagos y Finanzas
            </h1>
            <p class="text-xs text-gray-500 mt-1">Conciliación de transferencias, verificación de pagos y control de ingresos en tiempo real</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('metodospago.index') }}" class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-800 font-bold text-xs rounded-xl border border-gray-200 shadow-2xs transition flex items-center gap-2">
                <i class="fa-solid fa-credit-card text-yellow-500"></i>
                <span>Métodos de Pago</span>
            </a>
            <a href="{{ route('admin.pedidos.index') }}" class="px-4 py-2.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-yellow-400"></i>
                <span>Ver Pedidos</span>
            </a>
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

    {{-- ==================== TARJETAS DE MÉTRICAS FINANCIERAS ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total Recaudado / Aprobado --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-600 block uppercase">Total Recaudado</span>
                <span class="text-2xl font-black text-emerald-700 mt-0.5 block">${{ number_format($totalRecaudado, 0, ',', '.') }}</span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">{{ $transaccionesAprobadas }} transacciones aprobadas</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                💰
            </div>
        </div>

        {{-- Pendiente por Cobrar --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-500 block uppercase">Por Recaudar</span>
                <span class="text-2xl font-black text-amber-600 mt-0.5 block">${{ number_format($totalPendiente, 0, ',', '.') }}</span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">{{ $transaccionesPendientes }} pendientes / contra entrega</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold border border-amber-100">
                ⏳
            </div>
        </div>

        {{-- Total Transacciones --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-400 block uppercase">Transacciones</span>
                <span class="text-2xl font-black text-gray-900 mt-0.5 block">{{ $totalTransacciones }}</span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">Historial registrado</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-violet-50 text-[#7c3aed] flex items-center justify-center text-xl font-bold border border-violet-100">
                🧾
            </div>
        </div>

        {{-- Efectividad de Cobro --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-blue-500 block uppercase">Efectividad</span>
                @php
                    $efectividad = $totalTransacciones > 0 ? round(($transaccionesAprobadas / $totalTransacciones) * 100) : 100;
                @endphp
                <span class="text-2xl font-black text-blue-600 mt-0.5 block">{{ $efectividad }}%</span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">Tasa de pagos exitosos</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold border border-blue-100">
                📊
            </div>
        </div>

    </div>

    {{-- ==================== FILTROS Y BÚSQUEDA ==================== --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.pagos.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            
            {{-- Buscador --}}
            <div class="sm:col-span-5 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar por factura, # pedido, cliente, documento o método..." 
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-1 focus:ring-[#7c3aed]">
            </div>

            {{-- Filtro Estado Pago --}}
            <div class="sm:col-span-3">
                <select name="estado" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                    <option value="todos">Todos los Estados</option>
                    <option value="Aprobado" {{ request('estado') === 'Aprobado' ? 'selected' : '' }}>✅ Aprobado / Pagado</option>
                    <option value="Pendiente al entregar" {{ request('estado') === 'Pendiente al entregar' ? 'selected' : '' }}>💵 Pendiente al entregar</option>
                    <option value="Pendiente" {{ request('estado') === 'Pendiente' ? 'selected' : '' }}>⏳ Pendiente de Verificación</option>
                    <option value="Rechazado" {{ request('estado') === 'Rechazado' ? 'selected' : '' }}>❌ Rechazado</option>
                </select>
            </div>

            {{-- Filtro Método de Pago --}}
            <div class="sm:col-span-2">
                <select name="metodo" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                    <option value="todos">Todos los Métodos</option>
                    <option value="Transferencia" {{ request('metodo') === 'Transferencia' ? 'selected' : '' }}>Transferencia Bancaria</option>
                    <option value="Nequi" {{ request('metodo') === 'Nequi' ? 'selected' : '' }}>Nequi</option>
                    <option value="Daviplata" {{ request('metodo') === 'Daviplata' ? 'selected' : '' }}>Daviplata</option>
                    <option value="Bancolombia" {{ request('metodo') === 'Bancolombia' ? 'selected' : '' }}>Bancolombia</option>
                    <option value="Contra Entrega" {{ request('metodo') === 'Contra Entrega' ? 'selected' : '' }}>Contra Entrega</option>
                    <option value="Tarjeta" {{ request('metodo') === 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                </select>
            </div>

            {{-- Botón Filtrar y Limpiar --}}
            <div class="sm:col-span-2 flex items-center gap-2">
                <button 
                    type="submit" 
                    class="flex-1 py-2.5 px-4 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl transition shadow-xs">
                    Filtrar
                </button>
                @if(request()->hasAny(['q', 'estado', 'metodo']))
                    <a href="{{ route('admin.pagos.index') }}" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition font-bold" title="Limpiar filtros">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- ==================== TABLA DE PAGOS ==================== --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50/80 text-[11px] uppercase tracking-wider text-gray-500 border-b border-gray-200/70">
                    <tr>
                        <th class="px-6 py-4 font-bold">Factura / Pedido</th>
                        <th class="px-6 py-4 font-bold">Cliente</th>
                        <th class="px-6 py-4 font-bold">Fecha Pago</th>
                        <th class="px-6 py-4 font-bold">Método</th>
                        <th class="px-6 py-4 font-bold">Monto Liquidado</th>
                        <th class="px-6 py-4 font-bold">Estado del Pago</th>
                        <th class="px-6 py-4 font-bold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($pagos as $pago)
                        @php
                            $pedido = $pago->pedido;
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition">
                            
                            {{-- Factura & Pedido --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-xs shrink-0 border border-emerald-100">
                                        🧾
                                    </div>
                                    <div>
                                        <span class="font-black text-gray-950 text-sm block">{{ $pago->factura }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold block">Pedido #{{ $pago->pedido_id }}</span>
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
                                {{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y H:i') : ($pago->created_at ? $pago->created_at->format('d/m/Y H:i') : now()->format('d/m/Y')) }}
                            </td>

                            {{-- Método de Pago --}}
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-800 font-bold text-[11px] inline-flex items-center gap-1.5 border border-gray-200">
                                    <i class="fa-solid fa-credit-card text-yellow-500 text-[10px]"></i>
                                    <span>{{ $pago->metodo_pago }}</span>
                                </span>
                            </td>

                            {{-- Monto Liquidado --}}
                            <td class="px-6 py-4">
                                <span class="font-black text-emerald-600 text-sm block">
                                    ${{ number_format($pago->monto, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-gray-400 font-medium">COP</span>
                            </td>

                            {{-- Estado del Pago --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $estPago = strtolower($pago->estado_pago ?? 'aprobado');
                                @endphp
                                @if(str_contains($estPago, 'rechaz') || str_contains($estPago, 'anul'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        Rechazado
                                    </span>
                                @elseif(str_contains($estPago, 'aprob') || str_contains($estPago, 'pagad'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        Aprobado
                                    </span>
                                @elseif(str_contains($estPago, 'entregar'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        Contra Entrega
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                                        Pendiente
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones (Verificación y Factura POS) --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    {{-- 1. Botón Rápido Aprobar Pago (Si está pendiente) --}}
                                    @if($pago->estado_pago !== 'Aprobado')
                                        <form action="{{ route('admin.pagos.estado', $pago->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estado_pago" value="Aprobado">
                                            <button
                                                type="submit"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition inline-flex items-center gap-1 shadow-2xs cursor-pointer"
                                                title="Aprobar Pago">
                                                <i class="fa-solid fa-circle-check text-xs"></i>
                                                <span>Aprobar</span>
                                            </button>
                                        </form>
                                    @endif



                                    {{-- 3. Rechazar Pago --}}
                                    @if($pago->estado_pago !== 'Rechazado')
                                        <form action="{{ route('admin.pagos.destroy', $pago->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas marcar como Rechazado este pago {{ $pago->factura }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-100 text-gray-500 hover:text-red-700 flex items-center justify-center transition cursor-pointer"
                                                title="Rechazar Pago">
                                                <i class="fa-solid fa-ban text-xs"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-file-invoice-dollar text-3xl mb-2 text-gray-300"></i>
                                <p class="text-xs font-bold">No se encontraron pagos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($pagos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $pagos->links() }}
            </div>
        @endif
    </div>

@endsection
