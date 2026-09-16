@extends('layouts.sidebaradmin')

@section('title', 'Métodos de Pago')

@section('content')
<div
    x-data="{
        openModal: false,
        editModal: false,
        q: '',
        filtroEstado: 'todos',
        
        // Datos edición
        editId: null,
        nombre: '',
        tipo: 'tarjeta',
        numero: '',
        titular: '',
        instrucciones: '',
        estado: true,
        editUrl: '',

        abrirEditar(metodo) {
            this.editId = metodo.id;
            this.nombre = metodo.nombre ?? '';
            this.tipo = metodo.tipo ?? 'tarjeta';
            this.numero = metodo.numero ?? '';
            this.titular = metodo.titular ?? '';
            this.instrucciones = metodo.instrucciones ?? '';
            this.estado = Boolean(metodo.estado);
            this.editUrl = '{{ url('/admin/metodos-pago') }}/' + metodo.id;
            this.editModal = true;
        }
    }">

    {{-- ==================== TÍTULO Y BOTÓN ==================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2.5">
                <span class="text-2xl">💳</span>
                Gestión de Métodos de Pago
            </h1>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Configura las opciones de pago que verán tus clientes al finalizar su compra en el Checkout</p>
        </div>

        <button
            type="button"
            @click="openModal = true"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 dark:bg-violet-600 text-white text-xs font-bold rounded-xl hover:bg-black dark:hover:bg-violet-500 transition shadow-sm cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo Método de Pago
        </button>
    </div>

    {{-- ==================== MENSAJES FLASH ==================== --}}
    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-300 rounded-xl flex items-center justify-between text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    {{-- ==================== TABLA DE MÉTODOS DE PAGO ==================== --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600 dark:text-slate-300">
                <thead class="bg-gray-50/80 dark:bg-slate-800/60 text-[11px] uppercase tracking-wider text-gray-500 dark:text-slate-400 border-b border-gray-200/70 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 font-bold">Método / Nombre</th>
                        <th class="px-6 py-4 font-bold">Tipo</th>
                        <th class="px-6 py-4 font-bold">Cuenta / Número</th>
                        <th class="px-6 py-4 font-bold">Instrucciones</th>
                        <th class="px-6 py-4 font-bold text-center">Estado</th>
                        <th class="px-6 py-4 font-bold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 font-medium">
                    @forelse($metodos as $m)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-950/50 text-[#7c3aed] dark:text-violet-400 flex items-center justify-center font-bold text-sm shrink-0 border border-violet-100 dark:border-violet-900/50">
                                        @if($m->tipo == 'tarjeta') 💳
                                        @elseif($m->tipo == 'nequi') 📱
                                        @elseif($m->tipo == 'contraentrega') 💵
                                        @elseif($m->tipo == 'transferencia' || $m->tipo == 'bancolombia') 🏦
                                        @else ⚡
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-gray-900 dark:text-white block text-xs sm:text-sm">{{ $m->nombre ?? 'Sin nombre' }}</span>
                                        @if($m->titular)
                                            <span class="text-[11px] text-gray-400 dark:text-slate-400">Titular: {{ $m->titular }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider 
                                    {{ $m->tipo == 'tarjeta' ? 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900/50' : '' }}
                                    {{ $m->tipo == 'nequi' ? 'bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-900/50' : '' }}
                                    {{ $m->tipo == 'contraentrega' ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50' : '' }}
                                    {{ $m->tipo == 'transferencia' || $m->tipo == 'bancolombia' ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50' : '' }}">
                                    {{ $m->tipo }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800 dark:text-slate-200">{{ $m->numero ?: 'N/A' }}</span>
                            </td>

                            <td class="px-6 py-4 max-w-xs truncate text-[11px] text-gray-500 dark:text-slate-400">
                                {{ $m->instrucciones ?: 'Sin instrucciones adicionales' }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('metodospago.estado', $m->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button 
                                        type="submit" 
                                        class="px-3 py-1 rounded-full text-[11px] font-black transition cursor-pointer {{ $m->estado ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 hover:bg-emerald-200 dark:hover:bg-emerald-900/60 border border-emerald-300 dark:border-emerald-800' : 'bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-300 dark:hover:bg-slate-600' }}">
                                        {{ $m->estado ? '● Activo' : '○ Inactivo' }}
                                    </button>
                                </form>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        @click="abrirEditar({{ json_encode($m) }})"
                                        class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-yellow-100 dark:hover:bg-yellow-950/50 text-gray-700 dark:text-slate-300 hover:text-yellow-800 dark:hover:text-yellow-400 flex items-center justify-center transition border border-transparent dark:border-slate-700 cursor-pointer"
                                        title="Editar">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>

                                    <form action="{{ route('metodospago.eliminar', $m->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este método de pago?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-red-100 dark:hover:bg-red-950/50 text-gray-700 dark:text-slate-300 hover:text-red-800 dark:hover:text-red-400 flex items-center justify-center transition border border-transparent dark:border-slate-700 cursor-pointer"
                                            title="Eliminar">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <i class="fa-solid fa-credit-card text-3xl mb-2 text-gray-300 dark:text-slate-600"></i>
                                <p class="text-xs font-bold">No hay métodos de pago registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($metodos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800">
                {{ $metodos->links() }}
            </div>
        @endif
    </div>

    {{-- ==================== MODAL NUEVO MÉTODO ==================== --}}
    <div
        x-show="openModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
        @click.self="openModal = false">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100 dark:border-slate-800">
                <h3 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <span>💳</span>
                    Nuevo Método de Pago
                </h3>
                <button type="button" @click="openModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form action="{{ route('metodospago.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Nombre del Método *</label>
                    <input type="text" name="nombre" placeholder="Ej. Nequi / Daviplata" required class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Tipo de Pasarela / Forma *</label>
                    <select name="tipo" required class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="tarjeta">Tarjeta de Crédito / Débito</option>
                        <option value="nequi">Nequi / Daviplata</option>
                        <option value="contraentrega">Pago Contra Entrega</option>
                        <option value="transferencia">Transferencia Bancaria (Bancolombia / PSE)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Número de Cuenta / Teléfono</label>
                        <input type="text" name="numero" placeholder="Ej. 300 892 4110" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Titular</label>
                        <input type="text" name="titular" placeholder="Ej. PowerNet S.A.S." class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Instrucciones para el Cliente</label>
                    <textarea name="instrucciones" rows="2" placeholder="Ej. Transfiere el valor exacto a este número y adjunta tu comprobante..." class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="estado" id="estado_nuevo" value="1" checked class="rounded text-[#7c3aed]">
                    <label for="estado_nuevo" class="font-bold text-gray-700 dark:text-slate-300">Activo inmediatamente para los clientes</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                    <button type="button" @click="openModal = false" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 font-bold text-xs hover:bg-gray-50 dark:hover:bg-slate-800 cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0f172a] dark:bg-violet-600 hover:bg-black dark:hover:bg-violet-500 text-white font-bold text-xs shadow-xs cursor-pointer">
                        Guardar Método
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL EDITAR MÉTODO ==================== --}}
    <div
        x-show="editModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
        @click.self="editModal = false">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-gray-100 dark:border-slate-800">
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100 dark:border-slate-800">
                <h3 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <span>✏️</span>
                    Editar Método de Pago
                </h3>
                <button type="button" @click="editModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form :action="editUrl" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Nombre del Método *</label>
                    <input type="text" name="nombre" x-model="nombre" required class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Tipo de Pasarela / Forma *</label>
                    <select name="tipo" x-model="tipo" required class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs focus:ring-1 focus:ring-[#7c3aed]">
                        <option value="tarjeta">Tarjeta de Crédito / Débito</option>
                        <option value="nequi">Nequi / Daviplata</option>
                        <option value="contraentrega">Pago Contra Entrega</option>
                        <option value="transferencia">Transferencia Bancaria (Bancolombia / PSE)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Número de Cuenta / Teléfono</label>
                        <input type="text" name="numero" x-model="numero" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Titular</label>
                        <input type="text" name="titular" x-model="titular" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase mb-1">Instrucciones para el Cliente</label>
                    <textarea name="instrucciones" x-model="instrucciones" rows="2" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white px-3.5 py-2.5 text-xs"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="estado" id="estado_edit" value="1" x-model="estado" class="rounded text-[#7c3aed]">
                    <label for="estado_edit" class="font-bold text-gray-700 dark:text-slate-300">Activo para los clientes en Checkout</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 font-bold text-xs hover:bg-gray-50 dark:hover:bg-slate-800 cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0f172a] dark:bg-violet-600 hover:bg-black dark:hover:bg-violet-500 text-white font-bold text-xs shadow-xs cursor-pointer">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
