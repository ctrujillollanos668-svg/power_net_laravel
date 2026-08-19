@extends('layouts.sidebaradmin')

@section('title', 'Gestión de Envíos y Despachos')

@section('content')
<div
    x-data="adminEnviosManager()"
    class="space-y-6">

    {{-- ==================== ENCABEZADO ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 flex items-center gap-2.5">
                <span class="text-2xl">🚚</span>
                Control de Envíos y Despachos
            </h1>
            <p class="text-xs text-gray-500 mt-1">Supervisa transportadoras, entregas a domicilio y guías de transporte a nivel nacional</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pedidos.index') }}" class="px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-800 font-bold text-xs rounded-xl border border-gray-200 shadow-2xs transition flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-yellow-500"></i>
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

    {{-- ==================== TARJETAS DE MÉTRICAS ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total Envíos --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-400 block uppercase">Total Envíos</span>
                <span class="text-2xl font-black text-gray-900 mt-0.5 block">{{ $totalEnvios }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-violet-50 text-[#7c3aed] flex items-center justify-center text-xl font-bold border border-violet-100">
                🚚
            </div>
        </div>

        {{-- Pendientes por Despachar --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-500 block uppercase">Por Despachar</span>
                <span class="text-2xl font-black text-gray-900 mt-0.5 block">{{ $enviosPendientes }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold border border-amber-100">
                📦
            </div>
        </div>

        {{-- En Camino / Tránsito --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-blue-500 block uppercase">En Camino</span>
                <span class="text-2xl font-black text-gray-900 mt-0.5 block">{{ $enviosEnCamino }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold border border-blue-100">
                🛣️
            </div>
        </div>

        {{-- Entregados con Éxito --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-600 block uppercase">Entregados</span>
                <span class="text-2xl font-black text-emerald-700 mt-0.5 block">{{ $enviosEntregados }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                ✅
            </div>
        </div>

    </div>

    {{-- ==================== FILTROS Y BÚSQUEDA ==================== --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.envios.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            
            {{-- Buscador --}}
            <div class="sm:col-span-5 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar por # envío, pedido, cliente, dirección o transportadora..." 
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-1 focus:ring-[#7c3aed]">
            </div>

            {{-- Filtro Estado Envío --}}
            <div class="sm:col-span-3">
                <select name="estado" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                    <option value="todos">Todos los Estados</option>
                    <option value="En preparación" {{ request('estado') === 'En preparación' ? 'selected' : '' }}>⏳ En preparación</option>
                    <option value="Enviado" {{ request('estado') === 'Enviado' ? 'selected' : '' }}>🚚 Enviado / En camino</option>
                    <option value="Entregado" {{ request('estado') === 'Entregado' ? 'selected' : '' }}>✅ Entregado</option>
                    <option value="Cancelado" {{ request('estado') === 'Cancelado' ? 'selected' : '' }}>❌ Cancelado</option>
                </select>
            </div>

            {{-- Filtro Transportadora --}}
            <div class="sm:col-span-2">
                <select name="empresa" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                    <option value="todos">Todas las Empresas</option>
                    <option value="Servientrega" {{ request('empresa') === 'Servientrega' ? 'selected' : '' }}>Servientrega</option>
                    <option value="Coordinadora" {{ request('empresa') === 'Coordinadora' ? 'selected' : '' }}>Coordinadora</option>
                    <option value="Inter Rapidísimo" {{ request('empresa') === 'Inter Rapidísimo' ? 'selected' : '' }}>Inter Rapidísimo</option>
                    <option value="Envía" {{ request('empresa') === 'Envía' ? 'selected' : '' }}>Envía</option>
                    <option value="Domicilio Propio" {{ request('empresa') === 'Domicilio Propio' ? 'selected' : '' }}>Domicilio Propio</option>
                </select>
            </div>

            {{-- Botón Filtrar y Limpiar --}}
            <div class="sm:col-span-2 flex items-center gap-2">
                <button 
                    type="submit" 
                    class="flex-1 py-2.5 px-4 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl transition shadow-xs">
                    Filtrar
                </button>
                @if(request()->hasAny(['q', 'estado', 'empresa']))
                    <a href="{{ route('admin.envios.index') }}" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition font-bold" title="Limpiar filtros">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- ==================== TABLA DE ENVÍOS ==================== --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50/80 text-[11px] uppercase tracking-wider text-gray-500 border-b border-gray-200/70">
                    <tr>
                        <th class="px-6 py-4 font-bold"># Envío / Pedido</th>
                        <th class="px-6 py-4 font-bold">Cliente</th>
                        <th class="px-6 py-4 font-bold">Destino / Dirección</th>
                        <th class="px-6 py-4 font-bold">Transportadora</th>
                        <th class="px-6 py-4 font-bold">Costo</th>
                        <th class="px-6 py-4 font-bold">Estado de Envío</th>
                        <th class="px-6 py-4 font-bold">Fecha Despacho</th>
                        <th class="px-6 py-4 font-bold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($envios as $envio)
                        @php
                            $pedido = $envio->pedido;
                            $envioData = [
                                'id' => $envio->id,
                                'pedido_id' => $envio->pedido_id,
                                'empresa_envios' => $envio->empresa_envios,
                                'estado' => $envio->estado,
                                'costo' => $envio->costo,
                                'direccion_envio' => $envio->direccion_envio,
                                'fecha' => $envio->fecha_hora ? $envio->fecha_hora->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                                'cliente_nombre' => $pedido && $pedido->cliente && $pedido->cliente->persona ? $pedido->cliente->persona->nombre_persona : 'Cliente',
                                'cliente_doc' => $pedido && $pedido->cliente && $pedido->cliente->persona ? $pedido->cliente->persona->documento : '',
                                'cliente_tel' => $pedido && $pedido->cliente && $pedido->cliente->persona ? $pedido->cliente->persona->telefono : '',
                                'total_pedido' => $pedido ? number_format($pedido->total_pedido, 0, ',', '.') : '0',
                                'factura' => $pedido && $pedido->pago ? ($pedido->pago->factura ?? 'FAC-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT)) : 'FAC-0000',
                                'items' => $pedido ? $pedido->detalles->map(function($d) {
                                    return [
                                        'nombre' => $d->producto->nombre ?? 'Producto',
                                        'cantidad' => $d->cantidad,
                                        'precio_unitario' => number_format($d->precio_unitario, 0, ',', '.'),
                                        'subtotal' => number_format($d->subtotal, 0, ',', '.'),
                                    ];
                                }) : []
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition">
                            
                            {{-- # Envío & Pedido --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-violet-50 text-[#7c3aed] flex items-center justify-center font-bold text-sm shrink-0 border border-violet-100">
                                        🚚
                                    </div>
                                    <div>
                                        <span class="font-black text-gray-950 text-sm block">Envío #{{ $envio->id }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold block">Pedido #{{ $envio->pedido_id }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Cliente --}}
                            <td class="px-6 py-4">
                                <div>
                                    <span class="font-bold text-gray-900 block text-xs">
                                        {{ $pedido && $pedido->cliente && $pedido->cliente->persona ? $pedido->cliente->persona->nombre_persona : 'Cliente' }}
                                    </span>
                                    <span class="text-[11px] text-gray-400 block">
                                        {{ $pedido && $pedido->cliente && $pedido->cliente->persona ? $pedido->cliente->persona->telefono : 'Sin tel' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Destino --}}
                            <td class="px-6 py-4 max-w-xs">
                                <div class="flex items-start gap-1.5">
                                    <i class="fa-solid fa-location-dot text-red-500 text-xs mt-0.5 shrink-0"></i>
                                    <span class="text-xs text-gray-700 font-medium line-clamp-2 leading-relaxed">
                                        {{ $envio->direccion_envio }}
                                    </span>
                                </div>
                            </td>

                            {{-- Transportadora --}}
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-800 font-black text-[11px] inline-flex items-center gap-1 border border-gray-200">
                                    <i class="fa-solid fa-truck text-yellow-500 text-[10px]"></i>
                                    <span>{{ $envio->empresa_envios }}</span>
                                </span>
                            </td>

                            {{-- Costo --}}
                            <td class="px-6 py-4 font-bold text-xs">
                                @if($envio->costo > 0)
                                    <span class="text-gray-900">${{ number_format($envio->costo, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-emerald-600 font-black">GRATIS</span>
                                @endif
                            </td>

                            {{-- Estado Envío --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $est = strtolower($envio->estado ?? 'en preparación');
                                @endphp
                                @if(str_contains($est, 'cancel') || str_contains($est, 'devuel'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                        Cancelado
                                    </span>
                                @elseif(str_contains($est, 'enviado') || str_contains($est, 'camino') || str_contains($est, 'tránsito'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        Enviado
                                    </span>
                                @elseif(str_contains($est, 'entreg'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        Entregado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                                        En preparación
                                    </span>
                                @endif
                            </td>

                            {{-- Fecha --}}
                            <td class="px-6 py-4 text-[11px] text-gray-500">
                                {{ $envio->fecha_hora ? $envio->fecha_hora->format('d/m/Y H:i') : now()->format('d/m/Y') }}
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    {{-- 1. Editar Guía / Transportadora --}}
                                    <button
                                        type="button"
                                        @click="abrirEditarEnvio({{ json_encode($envioData) }})"
                                        class="px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-violet-100 text-gray-700 hover:text-violet-900 font-bold text-xs transition inline-flex items-center gap-1 cursor-pointer"
                                        title="Actualizar Despacho">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        <span>Editar</span>
                                    </button>

                                    {{-- 2. Rótulo de Despacho para empaque --}}
                                    <button
                                        type="button"
                                        @click="abrirRotuloPOS({{ json_encode($envioData) }})"
                                        class="w-7 h-7 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white flex items-center justify-center transition shadow-2xs cursor-pointer"
                                        title="Ver Rótulo de Despacho">
                                        <i class="fa-solid fa-tag text-xs"></i>
                                    </button>

                                    {{-- 3. Cancelar Despacho --}}
                                    <form action="{{ route('admin.envios.destroy', $envio->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas cancelar este envío #{{ $envio->id }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-100 text-gray-500 hover:text-red-700 flex items-center justify-center transition"
                                            title="Cancelar Envío">
                                            <i class="fa-solid fa-ban text-xs"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-truck-fast text-3xl mb-2 text-gray-300"></i>
                                <p class="text-xs font-bold">No se encontraron envíos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($envios->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $envios->links() }}
            </div>
        @endif
    </div>

    {{-- ==================== MODAL EDITAR DESPACHO / GUÍA ==================== --}}
    <div
        x-show="modalEditar"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4"
        @click.self="modalEditar = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-gray-100 relative">
            <div class="flex items-center justify-between pb-3 mb-5 border-b border-gray-100">
                <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                    <span>🚚</span>
                    <span>Actualizar Despacho <strong class="text-[#7c3aed]" x-text="editData ? '#' + editData.id : ''"></strong></span>
                </h3>
                <button type="button" @click="modalEditar = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form :action="editUrl" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                {{-- Empresa Transportadora --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Empresa Transportadora *</label>
                    <select name="empresa_envios" x-model="editData.empresa_envios" class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="Servientrega Express">Servientrega Express</option>
                        <option value="Coordinadora Mercantil">Coordinadora Mercantil</option>
                        <option value="Inter Rapidísimo">Inter Rapidísimo</option>
                        <option value="Envía Colvanes">Envía Colvanes</option>
                        <option value="Domicilio Propio PowerNet">Domicilio Propio PowerNet</option>
                    </select>
                </div>

                {{-- Estado del Envío --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Estado del Envío *</label>
                    <select name="estado" x-model="editData.estado" class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="En preparación">⏳ En preparación</option>
                        <option value="Enviado">🚚 Enviado / En camino</option>
                        <option value="Entregado">✅ Entregado con Éxito</option>
                        <option value="Cancelado">❌ Cancelado / Devuelto</option>
                    </select>
                </div>

                {{-- Costo de Envío --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Costo de Envío ($ COP)</label>
                    <input type="number" step="0.01" name="costo" x-model="editData.costo" class="w-full rounded-xl border border-gray-300 p-2.5 text-xs">
                </div>

                {{-- Dirección de Entrega --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Dirección de Entrega *</label>
                    <input type="text" name="direccion_envio" x-model="editData.direccion_envio" required class="w-full rounded-xl border border-gray-300 p-2.5 text-xs">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="modalEditar = false" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-bold text-xs hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#0f172a] hover:bg-black text-white font-bold text-xs shadow-xs">
                        Guardar Despacho
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL RÓTULO DE DESPACHO ==================== --}}
    <div 
        x-show="modalRotulo" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="modalRotulo = false"
        @keydown.escape.window="modalRotulo = false">
        
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-gray-100 relative my-8" @click.stop x-show="envioActivo">
            
            {{-- Encabezado Modal --}}
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100 no-imprimir">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🏷️</span>
                    <div>
                        <h3 class="text-xs font-black text-gray-900">Rótulo de Despacho</h3>
                        <p class="text-[10px] text-gray-400">Tirilla térmica 80mm para empaque</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        onclick="window.print()" 
                        class="px-3 py-1.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <i class="fa-solid fa-print text-yellow-400"></i>
                        <span>Imprimir Rótulo</span>
                    </button>
                    <button 
                        type="button" 
                        @click="modalRotulo = false" 
                        class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Tirilla POS Imprimible --}}
            <div id="imprimible-pos" class="bg-white p-4 rounded-2xl border border-dashed border-gray-300 font-mono text-[11px] leading-tight text-black max-h-[70vh] overflow-y-auto">
                
                {{-- Encabezado Remitente --}}
                <div class="text-center pb-2">
                    <div class="font-black text-sm tracking-widest uppercase mb-0.5">⚡ POWERNET S.A.S.</div>
                    <div class="text-[10px] font-semibold text-gray-700">GUÍA DE DESPACHO Y TRANSPORTE</div>
                    <div class="text-[9px] text-gray-600 mt-1">Cra. 15 # 45-20, Bogotá D.C.</div>
                    <div class="text-[9px] text-gray-600">Tel: +57 300 892 4110</div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Datos Envío y Transportadora --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="text-center font-black uppercase tracking-wider py-0.5">DATOS DE TRANSPORTE</div>
                    <div class="flex justify-between">
                        <span class="font-bold">Guía/Envío Nº:</span>
                        <span class="font-bold" x-text="envioActivo ? '#ENV-' + envioActivo.id : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pedido ID:</span>
                        <span x-text="envioActivo ? '#' + envioActivo.pedido_id : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Transportadora:</span>
                        <span class="font-bold" x-text="envioActivo ? envioActivo.empresa_envios : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Fecha:</span>
                        <span x-text="envioActivo ? envioActivo.fecha : ''"></span>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Datos Destinatario / Cliente --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="font-bold uppercase text-[9px] text-gray-700">DESTINATARIO (ENTREGAR EN):</div>
                    <div><span class="font-bold">Nombre:</span> <span x-text="envioActivo ? envioActivo.cliente_nombre : ''"></span></div>
                    <div><span class="font-bold">CC/NIT:</span> <span x-text="envioActivo ? envioActivo.cliente_doc : ''"></span></div>
                    <div><span class="font-bold">Tel:</span> <span x-text="envioActivo ? envioActivo.cliente_tel : ''"></span></div>
                    <div class="break-words font-black text-xs pt-1"><span class="font-bold">Dirección:</span> <span x-text="envioActivo ? envioActivo.direccion_envio : ''"></span></div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Resumen de Artículos --}}
                <div>
                    <div class="flex justify-between font-bold border-b border-gray-300 pb-1 mb-1 text-[10px]">
                        <span>CANT. ARTÍCULO EN PAQUETE</span>
                        <span>TOTAL</span>
                    </div>

                    <div class="space-y-1.5 pt-0.5 text-[10px]">
                        <template x-if="envioActivo && envioActivo.items">
                            <template x-for="(item, idx) in envioActivo.items" :key="idx">
                                <div class="flex justify-between items-baseline">
                                    <span class="font-bold" x-text="item.cantidad + 'x ' + item.nombre"></span>
                                    <span class="font-bold shrink-0 pl-1" x-text="'$' + item.subtotal"></span>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Totales --}}
                <div class="space-y-0.5 text-[10px]">
                    <div class="flex justify-between">
                        <span>Costo Envío:</span>
                        <span x-text="envioActivo && envioActivo.costo > 0 ? '$' + Number(envioActivo.costo).toLocaleString('es-CO') : 'GRATIS'"></span>
                    </div>
                    <div class="border-t border-gray-300 pt-1 flex justify-between font-black text-xs">
                        <span>VALOR DECLARADO:</span>
                        <span x-text="envioActivo ? '$' + envioActivo.total_pedido + ' COP' : ''"></span>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                {{-- Código de Barras y Firma --}}
                <div class="text-center text-[8px] text-gray-600 space-y-1 pt-1">
                    <div class="font-black text-[9px] text-black">PAQUETE REVISADO Y SELLADO</div>
                    <div class="pt-1 text-center text-[10px] tracking-widest">
                        |||| | ||||| || |||||| |||| | |||| ||
                    </div>
                    <div class="pt-4 border-b border-dashed border-gray-400 w-3/4 mx-auto"></div>
                    <div class="text-[8px]">Firma y Cédula de Recibido</div>
                </div>

            </div>

        </div>
    </div>

</div>

<script>
function adminEnviosManager() {
    return {
        modalEditar: false,
        modalRotulo: false,
        envioActivo: null,
        editData: {},
        editUrl: '',

        abrirEditarEnvio(envio) {
            this.editData = Object.assign({}, envio);
            this.editUrl = '{{ url('/admin/envios') }}/' + envio.id;
            this.modalEditar = true;
        },

        abrirRotuloPOS(envio) {
            this.envioActivo = envio;
            this.modalRotulo = true;
        }
    };
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #imprimible-pos, #imprimible-pos * {
        visibility: visible;
    }
    #imprimible-pos {
        position: absolute;
        left: 0;
        top: 0;
        width: 80mm;
        margin: 0 auto;
        padding: 0;
        border: none !important;
        max-height: none !important;
        overflow: visible !important;
    }
    .no-imprimir {
        display: none !important;
    }
}
</style>
@endsection
