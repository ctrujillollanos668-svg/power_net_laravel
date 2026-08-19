@extends('layouts.sidebaradmin')

@section('title', 'Kardex - Historial de Movimientos')

@section('content')
<div class="space-y-6">

    {{-- ==================== ENCABEZADO ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg shadow-2xs border border-violet-100">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Kardex de Inventario</h1>
                    <p class="text-xs text-slate-500 font-medium">Registro cronológico detallado de todas las entradas, salidas y ajustes de stock.</p>
                </div>
            </div>
        </div>

        <div>
            <a href="{{ route('admin.inventario.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition shadow-2xs">
                <i class="fa-solid fa-arrow-left text-slate-500"></i>
                <span>Volver al Inventario</span>
            </a>
        </div>
    </div>

    {{-- ==================== TARJETAS DE TOTALES DE MOVIMIENTO ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Registros</span>
                <p class="text-2xl font-black text-slate-900 leading-none">{{ $movimientos->total() }}</p>
                <span class="text-[11px] text-slate-500 mt-1 block">Movimientos en Kardex</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-list-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Total Entradas</span>
                <p class="text-2xl font-black text-emerald-700 leading-none">+{{ number_format($totalEntradas, 0, ',', '.') }}</p>
                <span class="text-[11px] text-slate-500 mt-1 block">Unidades ingresadas</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-arrow-down"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block mb-1">Total Salidas</span>
                <p class="text-2xl font-black text-amber-700 leading-none">-{{ number_format($totalSalidas, 0, ',', '.') }}</p>
                <span class="text-[11px] text-slate-500 mt-1 block">Unidades egresadas / vendidas</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-arrow-up"></i>
            </div>
        </div>

    </div>

    {{-- ==================== FILTRO DE MOVIMIENTOS ==================== --}}
    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs">
        <form method="GET" action="{{ route('admin.inventario.movimientos') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            
            <div class="sm:col-span-8 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}" 
                       placeholder="Buscar por producto, motivo o ID..."
                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-violet-500 transition">
            </div>

            <div class="sm:col-span-4">
                <select name="tipo" 
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-white focus:outline-none focus:border-violet-500 transition">
                    <option value="todos">Todos los tipos de movimiento</option>
                    <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>🟢 Entradas (+)</option>
                    <option value="salida" {{ request('tipo') === 'salida' ? 'selected' : '' }}>🟠 Salidas (-)</option>
                </select>
            </div>

        </form>
    </div>

    {{-- ==================== TABLA KARDEX ==================== --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-extrabold tracking-wider text-slate-400 uppercase border-b border-slate-100">
                        <th class="px-6 py-3.5"># ID</th>
                        <th class="px-6 py-3.5">Fecha & Hora</th>
                        <th class="px-6 py-3.5">Producto</th>
                        <th class="px-6 py-3.5">Tipo</th>
                        <th class="px-6 py-3.5">Cantidad</th>
                        <th class="px-6 py-3.5">Stock Anterior</th>
                        <th class="px-6 py-3.5">Stock Resultante</th>
                        <th class="px-6 py-3.5">Motivo / Justificación</th>
                        <th class="px-6 py-3.5 text-right">Referencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($movimientos as $m)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-mono font-bold text-slate-400">#{{ $m->id }}</td>
                            <td class="px-6 py-4 text-slate-600 font-medium whitespace-nowrap">
                                {{ $m->created_at ? $m->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $m->producto->nombre ?? 'Producto Eliminado' }}</div>
                                <span class="text-[10px] text-slate-400">ID Ref: #{{ $m->producto_id }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($m->tipo === 'entrada')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Entrada (+)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Salida (-)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-black text-xs whitespace-nowrap {{ $m->tipo === 'entrada' ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $m->tipo === 'entrada' ? '+' . $m->cantidad : '-' . $m->cantidad }} unid.
                            </td>
                            <td class="px-6 py-4 text-slate-500 font-medium whitespace-nowrap">
                                {{ $m->stock_anterior }} unid.
                            </td>
                            <td class="px-6 py-4 font-black text-slate-900 whitespace-nowrap">
                                {{ $m->stock_nuevo }} unid.
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium max-w-xs">
                                {{ $m->motivo }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($m->pedido_id)
                                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold text-[10px] border border-blue-200">
                                        Pedido #{{ $m->pedido_id }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 font-semibold italic">Ajuste Manual</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left text-3xl mb-2 text-slate-300"></i>
                                <p class="text-xs font-bold">No hay registros de movimientos en el Kardex.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación de 10 en 10 --}}
        @if($movimientos->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $movimientos->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
