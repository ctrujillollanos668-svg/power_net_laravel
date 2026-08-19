@extends('layouts.tienda')

@section('titulo', 'Mis Devoluciones y Garantías - PowerNet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8" x-data="clienteDevolucionesManager()">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-gray-900 transition">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <a href="{{ route('pedidos.index') }}" class="hover:text-gray-900 transition">Mis Pedidos</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-gray-900">Mis Devoluciones</span>
    </nav>

    {{-- Mensajes Flash --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    {{-- Encabezado con Botón de Solicitud --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                <span>↪️ Mis Devoluciones</span>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Garantías y Reembolsos
                </span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Supervisa y radica reclamaciones de garantía y devoluciones de tus compras.</p>
        </div>

        @if($pedidos->isNotEmpty())
            <button 
                type="button" 
                @click="modalNueva = true" 
                class="px-5 py-2.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer shrink-0">
                <i class="fa-solid fa-plus text-yellow-400"></i>
                <span>Solicitar Devolución</span>
            </button>
        @endif
    </div>

    {{-- Tarjetas Resumen --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-400 block uppercase">Total Solicitudes</span>
                <span class="text-2xl font-black text-gray-900 mt-0.5 block">{{ $totalDevoluciones }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-gray-50 text-gray-700 flex items-center justify-center text-lg font-bold border border-gray-100">
                📋
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-500 block uppercase">En Revisión</span>
                <span class="text-2xl font-black text-amber-600 mt-0.5 block">{{ $pendientes }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold border border-amber-100">
                ⏳
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-600 block uppercase">Aprobadas / Éxito</span>
                <span class="text-2xl font-black text-emerald-700 mt-0.5 block">{{ $aprobadas }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold border border-emerald-100">
                ✅
            </div>
        </div>
    </div>

    @if($devoluciones->isEmpty())
        {{-- Estado Vacío --}}
        <div class="bg-white rounded-3xl p-12 text-center border border-gray-200/80 shadow-xs max-w-xl mx-auto my-8">
            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 border border-amber-100">
                🔄
            </div>
            <h2 class="text-lg font-black text-gray-900 mb-1">No tienes devoluciones en curso</h2>
            <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                Si tuviste algún problema con un producto de tus compras, puedes radicar una garantía o devolución en cualquier momento.
            </p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('pedidos.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition shadow-2xs">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Ver Mis Pedidos</span>
                </a>
                @if($pedidos->isNotEmpty())
                    <button 
                        type="button" 
                        @click="modalNueva = true" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0f172a] hover:bg-black text-white font-bold text-xs rounded-xl transition shadow-xs">
                        <i class="fa-solid fa-plus text-yellow-400"></i>
                        <span>Radicar Solicitud</span>
                    </button>
                @endif
            </div>
        </div>
    @else
        {{-- Lista de Devoluciones del Cliente --}}
        <div class="space-y-4">
            @foreach($devoluciones as $dev)
                @php
                    $pedido = $dev->pedido;
                    $estDev = strtolower($dev->estado);
                @endphp
                <div class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs p-5 sm:p-6 hover:shadow-xs transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-black text-xs border border-amber-100 shrink-0">
                                🔄
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900">
                                    Devolución #{{ $dev->id }}
                                </h3>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    Pedido asociado: <a href="{{ route('pedidos.show', $dev->pedido_id) }}" class="font-bold text-[#7c3aed] hover:underline">#{{ $dev->pedido_id }}</a> 
                                    • Radicada: {{ $dev->fecha_devolucion ? $dev->fecha_devolucion->format('d/m/Y H:i') : '' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            {{-- Badge de Estado --}}
                            @if(str_contains($estDev, 'aprob') || str_contains($estDev, 'complet'))
                                <span class="px-3 py-1 rounded-md text-[11px] font-black text-white bg-emerald-600 shadow-2xs">
                                    ✅ Aprobada
                                </span>
                            @elseif(str_contains($estDev, 'rechaz'))
                                <span class="px-3 py-1 rounded-md text-[11px] font-black text-white bg-red-600 shadow-2xs">
                                    ❌ Rechazada
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-md text-[11px] font-black text-white bg-amber-500 shadow-2xs">
                                    ⏳ En Revisión
                                </span>
                            @endif

                            <div class="text-right">
                                <span class="font-black text-red-600 text-sm block">${{ number_format($dev->monto_devolucion, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-gray-400">COP</span>
                            </div>
                        </div>
                    </div>

                    {{-- Motivo y Respuesta --}}
                    <div class="pt-4 space-y-3 text-xs">
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-200/70">
                            <span class="font-bold text-gray-700 block uppercase text-[10px] mb-1">Motivo Radicado:</span>
                            <p class="text-gray-800 leading-relaxed">{{ $dev->motivo }}</p>
                        </div>

                        @if($dev->motivo_rechazo)
                            <div class="bg-red-50 rounded-xl p-3 border border-red-200">
                                <span class="font-bold text-red-700 block uppercase text-[10px] mb-1">Respuesta del Administrador:</span>
                                <p class="text-red-900 leading-relaxed">{{ $dev->motivo_rechazo }}</p>
                            </div>
                        @endif

                        {{-- Productos Asociados --}}
                        @if($dev->detalles->isNotEmpty())
                            <div class="pt-1">
                                <span class="font-bold text-gray-600 block uppercase text-[10px] mb-2">Artículos en Reclamación:</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($dev->detalles as $det)
                                        @php
                                            $prod = $det->producto;
                                            $foto = $prod && $prod->imagenes->first() ? $prod->imagenes->first()->imagen : null;
                                        @endphp
                                        <div class="flex items-center gap-2 p-2 bg-white rounded-xl border border-gray-200 text-xs shadow-2xs">
                                            <div class="w-7 h-7 bg-gray-50 rounded-lg p-0.5 border border-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                                @if($foto && file_exists(public_path('imagenes_productos/' . $foto)))
                                                    <img src="{{ asset('imagenes_productos/' . $foto) }}" class="max-h-full max-w-full object-contain">
                                                @else
                                                    <span>💡</span>
                                                @endif
                                            </div>
                                            <span class="font-bold text-gray-800 truncate max-w-[160px]">{{ $prod->nombre ?? 'Producto' }}</span>
                                            <span class="text-gray-400 font-semibold">({{ $det->cantidad }} unid.)</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $devoluciones->links() }}
            </div>
        </div>
    @endif

    {{-- ==================== MODAL RADICAR NUEVA DEVOLUCIÓN ==================== --}}
    <div 
        x-show="modalNueva" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="modalNueva = false"
        @keydown.escape.window="modalNueva = false">
        
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-gray-100 relative my-8" @click.stop>
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🔄</span>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Solicitar Devolución o Garantía</h3>
                        <p class="text-[10px] text-gray-400">Selecciona el pedido y describe el problema</p>
                    </div>
                </div>
                <button type="button" @click="modalNueva = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form action="{{ route('cliente.devoluciones.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                {{-- Seleccionar Pedido --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Seleccionar Pedido *</label>
                    <select name="pedido_id" required class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="">Selecciona tu pedido...</option>
                        @foreach($pedidos as $p)
                            <option value="{{ $p->id }}">
                                Pedido #{{ $p->id }} - {{ $p->fecha_pedido ? $p->fecha_pedido->format('d/m/Y') : '' }} (${{ number_format($p->total_pedido, 0, ',', '.') }} COP)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Categoría Motivo --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Tipo de Reclamo *</label>
                    <select name="motivo_categoria" required class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="Garantía por defecto técnico">⚡ Garantía por falla técnica / producto defectuoso</option>
                        <option value="Producto no corresponde a lo comprado">📦 Producto incorrecto / no corresponde a lo pedido</option>
                        <option value="Pedido averiado en transporte">🚚 Producto averiado / golpeado en transporte</option>
                        <option value="Inconformidad del cliente">💬 Desistimiento / Inconformidad</option>
                    </select>
                </div>

                {{-- Descripción Detallada --}}
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Describe detalladamente lo sucedido *</label>
                    <textarea name="descripcion" rows="4" required placeholder="Explica detalladamente el problema o falla del producto..." class="w-full rounded-xl border border-gray-300 p-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]"></textarea>
                </div>

                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-[10px] text-gray-500 leading-relaxed">
                    ℹ️ Una vez enviada la solicitud, el equipo técnico revisará el caso y te responderá en un plazo de 24 a 48 horas.
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="modalNueva = false" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-bold text-xs hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition">
                        Radicar Devolución
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
function clienteDevolucionesManager() {
    return {
        modalNueva: false,
    };
}
</script>
@endsection
