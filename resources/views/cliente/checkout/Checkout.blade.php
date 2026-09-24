
@extends('layouts.tienda')

@section('titulo', 'Finalizar Compra - Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8" x-data="checkoutManager()">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-gray-900 transition">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <a href="{{ route('carrito.index') }}" class="hover:text-gray-900 transition">Carrito</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-gray-900">Finalizar Compra</span>
    </nav>

    {{-- Encabezado --}}
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
            <span>Checkout Seguro</span>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200 flex items-center gap-1.5">
                <i class="fa-solid fa-lock text-[10px]"></i>
                <span>Encriptación SSL 256-bit</span>
            </span>
        </h1>
        <p class="text-xs text-gray-500 mt-1">Completa los datos de envío y selecciona tu método de pago para procesar tu pedido.</p>
    </div>

    {{-- Alerta de Error si Hubo Fallos --}}
    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <form action="{{ route('checkout.procesar') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Columna Izquierda: Formulario de Envío y Pagos (7 columnas) --}}
            <div class="lg:col-span-7 space-y-6">
                
                {{-- PASO 1: DATOS DE ENVÍO Y CONTACTO --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-xs">
                    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-[#0f172a] text-white flex items-center justify-center font-black text-xs">
                                1
                            </span>
                            <div>
                                <h2 class="text-base font-black text-gray-900">Información de Entrega</h2>
                                <p class="text-[11px] text-gray-400">¿A dónde y a nombre de quién enviamos tu pedido?</p>
                            </div>
                        </div>

                        {{-- Botón Agregar Nueva Dirección si ya tiene direcciones guardadas --}}
                        <template x-if="direcciones.length > 0 && !mostrarFormulario">
                            <button 
                                type="button" 
                                @click="abrirFormularioNuevaDireccion()" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-violet-50 hover:bg-violet-100 text-[#7c3aed] text-xs font-black transition shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Agregar otra dirección</span>
                            </button>
                        </template>
                    </div>

                    {{-- LISTADO DE DIRECCIONES GUARDADAS DEL CLIENTE (MUESTRA TODAS LAS QUE TENGA EL CLIENTE) --}}
                    <template x-if="direcciones.length > 0 && !mostrarFormulario">
                        <div class="space-y-3 mb-3">
                            <div class="flex items-center justify-between text-xs font-bold text-gray-500 mb-1">
                                <span>Selecciona la dirección donde deseas recibir este pedido:</span>
                                <span class="text-[11px] text-gray-400 font-medium" x-text="direcciones.length + ' registrada(s)'"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <template x-for="(dir, index) in direcciones" :key="dir.id">
                                    <div 
                                        class="p-4 rounded-2xl border-2 transition-all relative cursor-pointer flex flex-col justify-between"
                                        :class="direccionSeleccionadaId == dir.id ? 'border-[#7c3aed] bg-violet-50/40 shadow-xs' : 'border-gray-200 hover:border-gray-300 bg-white'"
                                        @click="seleccionarDireccion(dir)">
                                        
                                        <div>
                                            {{-- Encabezado de la Tarjeta con Alias y Botones de Editar/Eliminar --}}
                                            <div class="flex items-start justify-between gap-2 mb-2 pb-2 border-b border-gray-100/80">
                                                <div class="flex items-center gap-2">
                                                    <span 
                                                        class="w-4 h-4 rounded-full flex items-center justify-center border-2 transition shrink-0"
                                                        :class="direccionSeleccionadaId == dir.id ? 'border-[#7c3aed] bg-[#7c3aed] text-white' : 'border-gray-300 bg-white'">
                                                        <i class="fa-solid fa-check text-[8px]" x-show="direccionSeleccionadaId == dir.id"></i>
                                                    </span>
                                                    <span class="text-xs font-black text-slate-800 tracking-tight" x-text="dir.alias || ('Dirección ' + (index + 1))"></span>
                                                </div>

                                                {{-- Acciones: Editar y Eliminar --}}
                                                <div class="flex items-center gap-1 shrink-0" @click.stop>
                                                    <button 
                                                        type="button" 
                                                        @click="editarDireccion(dir)" 
                                                        title="Editar esta dirección"
                                                        class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-[#7c3aed] flex items-center justify-center transition cursor-pointer">
                                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                                    </button>
                                                    <button 
                                                        type="button" 
                                                        @click="eliminarDireccion(dir.id)" 
                                                        title="Eliminar esta dirección"
                                                        class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 flex items-center justify-center transition cursor-pointer">
                                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Dirección, Ciudad y Departamento --}}
                                            <p class="text-xs font-black text-slate-800" x-text="dir.direccion"></p>
                                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                                <span x-text="dir.ciudad"></span>, <span x-text="dir.departamento"></span>
                                            </p>
                                        </div>

                                        {{-- Badge de Seleccionada --}}
                                        <div class="mt-3 pt-2 border-t border-gray-100/60 flex items-center justify-between text-[11px]">
                                            <template x-if="direccionSeleccionadaId == dir.id">
                                                <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full text-[10px]">
                                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Usar para este pedido
                                                </span>
                                            </template>
                                            <template x-if="direccionSeleccionadaId != dir.id">
                                                <span class="text-slate-400 font-medium group-hover:text-slate-600">
                                                    Clic para elegir
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- FORMULARIO DE DIRECCIÓN (VISIBLE CUANDO SE AGREGA, SE EDITA O SI NO HAY DIRECCIONES) --}}
                    <div x-show="mostrarFormulario || direcciones.length === 0" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-4">

                        <div class="flex items-center justify-between pb-2 mb-1 border-b border-gray-100" x-show="direcciones.length > 0">
                            <span class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fa-solid text-[#7c3aed]" :class="editandoDireccionId ? 'fa-pen-to-square' : 'fa-map-location-dot'"></i>
                                <span x-text="editandoDireccionId ? 'Modificar dirección seleccionada:' : 'Ingresa una nueva dirección:'"></span>
                            </span>
                            <button 
                                type="button" 
                                @click="cancelarFormulario()" 
                                class="text-xs font-bold text-[#7c3aed] hover:underline flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-arrow-left text-[10px]"></i> Volver a mis direcciones
                            </button>
                        </div>

                        {{-- Hidden cliente_id para el pedido --}}
                        <input type="hidden" name="cliente_id" :value="direccionSeleccionadaId">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            
                            {{-- Etiqueta / Nombre opcional (Casa, Oficina...) --}}
                            <div class="sm:col-span-2">
                                <label for="alias" class="block font-bold text-gray-700 uppercase mb-1.5">Nombre / Etiqueta de esta dirección (Opcional)</label>
                                <input 
                                    type="text" 
                                    id="alias" 
                                    name="alias" 
                                    x-model="alias"
                                    placeholder="Ej. Casa, Oficina, Apartamento, Bodega..." 
                                    class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                            </div>

                            {{-- Nombre Completo --}}
                            <div class="sm:col-span-2">
                                <label for="nombre" class="block font-bold text-gray-700 uppercase mb-1.5">Nombre Completo *</label>
                                <input 
                                    type="text" 
                                    id="nombre" 
                                    name="nombre" 
                                    x-model="nombre"
                                    placeholder="Ej. Carlos Rodríguez" 
                                    required
                                    class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                                @error('nombre') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Correo Electrónico --}}
                            <div>
                                <label for="email" class="block font-bold text-gray-700 uppercase mb-1.5">Correo Electrónico *</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    x-model="email"
                                    placeholder="tuemail@ejemplo.com" 
                                    required
                                    class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                                @error('email') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Teléfono / WhatsApp --}}
                            <div>
                                <label for="telefono" class="block font-bold text-gray-700 uppercase mb-1.5">Teléfono / WhatsApp *</label>
                                <input 
                                    type="tel" 
                                    id="telefono" 
                                    name="telefono" 
                                    x-model="telefono"
                                    placeholder="Ej. 300 123 4567" 
                                    required
                                    class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                                @error('telefono') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Documento de Identidad (Cédula / NIT) --}}
                            <div class="sm:col-span-2">
                                <label for="documento" class="block font-bold text-gray-700 uppercase mb-1.5">Cédula / NIT (Para Factura)</label>
                                <input 
                                    type="text" 
                                    id="documento" 
                                    name="documento" 
                                    x-model="documento"
                                    placeholder="Ej. 1020304050" 
                                    class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                            </div>

                            {{-- Dirección de Entrega --}}
                            <div class="sm:col-span-2">
                                <label for="direccion" class="block font-bold text-gray-700 uppercase mb-1.5">Dirección de Entrega *</label>
                                <input 
                                    type="text" 
                                    id="direccion" 
                                    name="direccion" 
                                    x-model="direccion"
                                    placeholder="Ej. Carrera 15 # 45-20, Apto 302" 
                                    required
                                    class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                                @error('direccion') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Ciudad / Municipio --}}
                            <div>
                                <label for="ciudad" class="block font-bold text-gray-700 uppercase mb-1.5">Ciudad / Municipio *</label>
                                <input 
                                    type="text" 
                                    id="ciudad" 
                                    name="ciudad" 
                                    x-model="ciudad"
                                    required
                                    class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                                @error('ciudad') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Departamento --}}
                            <div>
                                <label for="departamento" class="block font-bold text-gray-700 uppercase mb-1.5">Departamento *</label>
                                <input 
                                    type="text" 
                                    id="departamento" 
                                    name="departamento" 
                                    x-model="departamento"
                                    required
                                    class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none">
                                @error('departamento') <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Indicaciones / Notas Adicionales --}}
                            <div class="sm:col-span-2">
                                <label for="notas" class="block font-bold text-gray-700 uppercase mb-1.5">Notas de Entrega (Opcional)</label>
                                <textarea 
                                    id="notas" 
                                    name="notas" 
                                    x-model="notas"
                                    rows="2" 
                                    placeholder="Ej. Timbre 201, dejar en portería, casa esquinera color blanco..." 
                                    class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-xs sm:text-sm text-gray-900 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed]/20 focus:outline-none"></textarea>
                            </div>

                            {{-- Botón Guardar Dirección Editada / Cancelar --}}
                            <div class="sm:col-span-2 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        @click="guardarCambiosDireccion()" 
                                        :disabled="guardandoDireccion"
                                        class="px-5 py-2.5 bg-[#7c3aed] hover:bg-violet-700 text-white rounded-xl text-xs font-black shadow-md hover:shadow-lg transition flex items-center gap-2 cursor-pointer disabled:opacity-50">
                                        <template x-if="!guardandoDireccion">
                                            <i class="fa-solid fa-floppy-disk text-white text-xs"></i>
                                        </template>
                                        <template x-if="guardandoDireccion">
                                            <i class="fa-solid fa-spinner fa-spin text-white text-xs"></i>
                                        </template>
                                        <span x-text="guardandoDireccion ? 'Guardando...' : (editandoDireccionId ? 'Guardar cambios' : 'Guardar y usar esta dirección')"></span>
                                    </button>

                                    <button 
                                        type="button" 
                                        x-show="direcciones.length > 0"
                                        @click="cancelarFormulario()" 
                                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition cursor-pointer">
                                        Cancelar
                                    </button>
                                </div>
                                <span class="text-[11px] text-gray-400">
                                    <i class="fa-solid fa-shield-check text-emerald-500 mr-1"></i> Guarda tus cambios para este y futuros pedidos
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- PASO 2: SELECCIÓN DE MÉTODO DE PAGO (CONFIGURADOS POR EL ADMINISTRADOR) --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-xs">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <span class="w-8 h-8 rounded-full bg-[#0f172a] text-white flex items-center justify-center font-black text-xs">
                            2
                        </span>
                        <div>
                            <h2 class="text-base font-black text-gray-900">Método de Pago</h2>
                            <p class="text-[11px] text-gray-400">Selecciona una de las formas de pago habilitadas por la tienda</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($metodosPago as $index => $metodo)
                            <label 
                                class="flex flex-col p-4 rounded-2xl border-2 transition cursor-pointer"
                                :class="metodoSeleccionado == {{ $metodo->id }} ? 'border-[#7c3aed] bg-violet-50/40' : 'border-gray-200 hover:border-gray-300'">
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <input 
                                            type="radio" 
                                            name="metodo_pago_id" 
                                            value="{{ $metodo->id }}" 
                                            x-model="metodoSeleccionado" 
                                            required
                                            class="text-[#7c3aed] focus:ring-[#7c3aed]">
                                        <div>
                                            <span class="text-xs sm:text-sm font-black text-gray-900 block">{{ $metodo->nombre }}</span>
                                            @if($metodo->numero || $metodo->titular)
                                                <span class="text-[11px] text-gray-500 font-medium">
                                                    {{ $metodo->numero ? 'Cuenta/Número: ' . $metodo->numero : '' }} 
                                                    {{ $metodo->titular ? ' - ' . $metodo->titular : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xl">
                                        @if($metodo->tipo == 'tarjeta') 💳
                                        @elseif($metodo->tipo == 'nequi') 📱
                                        @elseif($metodo->tipo == 'contraentrega') 💵
                                        @elseif($metodo->tipo == 'transferencia') 🏦
                                        @else ⚡
                                        @endif
                                    </span>
                                </div>

                                {{-- Instrucciones configuradas por el Administrador --}}
                                @if(!empty($metodo->instrucciones))
                                    <div x-show="metodoSeleccionado == {{ $metodo->id }}" x-cloak class="mt-3 pt-3 border-t border-violet-100 text-xs text-gray-600 bg-white p-3 rounded-xl border border-violet-100">
                                        <p class="font-bold text-gray-900 mb-0.5">💡 Instrucciones:</p>
                                        <p>{{ $metodo->instrucciones }}</p>
                                    </div>
                                @endif
                            </label>
                        @empty
                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold text-center">
                                No hay métodos de pago activos en este momento. Por favor contacte al administrador.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Columna Derecha: Resumen del Pedido y Botón Confirmar (5 columnas) --}}
            <div class="lg:col-span-5 sticky top-28 space-y-4">
                
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-sm">
                    <h2 class="text-base font-black text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center justify-between">
                        <span>Resumen de tu Pedido</span>
                        <span class="text-xs font-bold text-[#7c3aed]">{{ $totalItems }} artículo(s)</span>
                    </h2>

                    {{-- Lista de Artículos Mini --}}
                    <div class="max-h-60 overflow-y-auto space-y-3 pr-1 divide-y divide-gray-100 mb-5">
                        @foreach($cart as $item)
                            <div class="flex items-center justify-between gap-3 pt-3 first:pt-0">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-12 h-12 bg-gray-50 rounded-xl p-1 border border-gray-100 shrink-0 flex items-center justify-center overflow-hidden">
                                        @if(!empty($item['imagen']))
                                            <img src="{{ asset('imagenes_productos/' . $item['imagen']) }}" alt="{{ $item['nombre'] }}" class="max-h-full max-w-full object-contain">
                                        @else
                                            <i class="fa-solid fa-bolt text-yellow-400 text-sm"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-gray-900 truncate">{{ $item['nombre'] }}</p>
                                        <p class="text-[10px] text-gray-500 font-semibold">Cant: <strong class="text-gray-900">{{ $item['cantidad'] }}</strong> × ${{ number_format($item['precio_oferta'] ?? $item['precio'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-gray-900 shrink-0">
                                    ${{ number_format(($item['precio_oferta'] ?? $item['precio']) * $item['cantidad'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desglose Financiero --}}
                    <div class="space-y-3 text-xs pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900">${{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        @if($descuentoTotal > 0)
                            <div class="flex items-center justify-between text-emerald-600 font-bold">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-tag"></i>
                                    <span>Descuentos en oferta:</span>
                                </span>
                                <span>-${{ number_format($descuentoTotal, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-gray-600">
                            <span>Envío nacional:</span>
                            @if($costoEnvio === 0)
                                <span class="font-extrabold text-emerald-600 uppercase text-[11px]">Gratis</span>
                            @else
                                <span class="font-bold text-gray-900">${{ number_format($costoEnvio, 0, ',', '.') }}</span>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 my-3"></div>

                        <div class="flex items-baseline justify-between pt-1">
                            <div>
                                <span class="text-sm font-black text-gray-900 block">Total a Pagar</span>
                                <span class="text-[10px] text-gray-400">Impuestos y factura incluidos</span>
                            </div>
                            <span class="text-2xl font-black text-gray-900">
                                ${{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Botón Confirmar Compra --}}
                    <button 
                        type="submit" 
                        class="w-full mt-6 bg-[#0f172a] hover:bg-black text-white font-black text-xs uppercase tracking-wider py-4 px-6 rounded-2xl text-center transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2 cursor-pointer group">
                        <i class="fa-solid fa-circle-check text-yellow-400 text-sm group-hover:scale-110 transition-transform"></i>
                        <span>Confirmar y Realizar Pedido</span>
                    </button>

                    {{-- Garantías --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 space-y-2 text-[11px] text-gray-500 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-emerald-500 text-xs"></i>
                            <span>Garantía de fábrica 100% original</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-blue-500 text-xs"></i>
                            <span>Factura legal de venta con tu pedido</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </form>

</div>

<script>
function checkoutManager() {
    const rawDirecciones = @json($direccionesGuardadas ?? []);
    return {
        metodoSeleccionado: {{ $metodosPago->first() ? $metodosPago->first()->id : 'null' }},
        direcciones: rawDirecciones,
        direccionSeleccionadaId: rawDirecciones.length > 0 ? rawDirecciones[0].id : null,
        editandoDireccionId: null,
        mostrarFormulario: rawDirecciones.length === 0,
        guardandoDireccion: false,

        alias: '',
        nombre: @json(old('nombre', $persona->nombre_persona ?? ($user->name ?? ''))),
        email: @json(old('email', $user->email ?? '')),
        telefono: @json(old('telefono', $telefonoGuardado ?? '')),
        documento: @json(old('documento', $documentoGuardado ?? '')),
        direccion: @json(old('direccion', $direccionCalle ?? ($direccionGuardada ?? ''))),
        ciudad: @json(old('ciudad', $ciudadGuardada ?? 'Bogotá D.C.')),
        departamento: @json(old('departamento', $departamentoGuardado ?? 'Cundinamarca')),
        notas: @json(old('notas', '')),

        seleccionarDireccion(dir) {
            this.direccionSeleccionadaId = dir.id;
            this.direccion = dir.direccion;
            this.ciudad = dir.ciudad;
            this.departamento = dir.departamento;
            this.alias = dir.alias || '';
        },

        abrirFormularioNuevaDireccion() {
            this.editandoDireccionId = null;
            this.alias = '';
            this.direccion = '';
            this.ciudad = 'Bogotá D.C.';
            this.departamento = 'Cundinamarca';
            this.mostrarFormulario = true;
        },

        editarDireccion(dir) {
            this.editandoDireccionId = dir.id;
            this.direccionSeleccionadaId = dir.id;
            this.alias = dir.alias || '';
            this.direccion = dir.direccion;
            this.ciudad = dir.ciudad;
            this.departamento = dir.departamento;
            this.mostrarFormulario = true;
        },

        cancelarFormulario() {
            if (this.direcciones.length > 0) {
                const seleccionada = this.direcciones.find(d => d.id === this.direccionSeleccionadaId) || this.direcciones[0];
                if (seleccionada) {
                    this.seleccionarDireccion(seleccionada);
                }
                this.mostrarFormulario = false;
                this.editandoDireccionId = null;
            }
        },

        guardarCambiosDireccion() {
            if (!this.direccion || !this.nombre || !this.telefono || !this.ciudad || !this.departamento) {
                if (typeof window.alertaAdvertencia === 'function') {
                    window.alertaAdvertencia('Por favor completa los campos obligatorios: Nombre, Teléfono, Dirección, Ciudad y Departamento.');
                } else {
                    alert('Por favor completa los campos obligatorios: Nombre, Teléfono, Dirección, Ciudad y Departamento.');
                }
                return;
            }

            this.guardandoDireccion = true;

            fetch('{{ route("checkout.guardar-direccion") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: this.editandoDireccionId,
                    alias: this.alias,
                    nombre: this.nombre,
                    telefono: this.telefono,
                    documento: this.documento,
                    direccion: this.direccion,
                    ciudad: this.ciudad,
                    departamento: this.departamento
                })
            })
            .then(res => res.json())
            .then(data => {
                this.guardandoDireccion = false;
                if (data.success && data.data) {
                    const nueva = data.data;
                    if (this.editandoDireccionId) {
                        const idx = this.direcciones.findIndex(d => d.id === this.editandoDireccionId);
                        if (idx !== -1) {
                            this.direcciones[idx] = nueva;
                        }
                    } else {
                        this.direcciones.push(nueva);
                    }
                    this.direccionSeleccionadaId = nueva.id;
                    this.direccion = nueva.direccion;
                    this.ciudad = nueva.ciudad;
                    this.departamento = nueva.departamento;
                    this.alias = nueva.alias;
                    this.mostrarFormulario = false;
                    this.editandoDireccionId = null;

                    if (typeof window.alertaToast === 'function') {
                        window.alertaToast(data.message || '¡Dirección guardada exitosamente!');
                    }
                } else {
                    if (typeof window.alertaAdvertencia === 'function') {
                        window.alertaAdvertencia(data.message || 'No se pudo guardar la dirección.');
                    } else {
                        alert(data.message || 'No se pudo guardar la dirección.');
                    }
                }
            })
            .catch(err => {
                this.guardandoDireccion = false;
                this.mostrarFormulario = false;
                if (typeof window.alertaToast === 'function') {
                    window.alertaToast('¡Dirección aplicada a tu pedido actual!');
                }
            });
        },

        async eliminarDireccion(id) {
            let confirmado = false;
            if (typeof window.alertaConfirmar === 'function') {
                confirmado = await window.alertaConfirmar({
                    titulo: '¿Eliminar dirección?',
                    texto: '¿Deseas quitar esta dirección de tu lista de opciones de entrega?',
                    icono: 'warning',
                    textoConfirmar: 'Sí, eliminar',
                    textoCancelar: 'Cancelar',
                    esPeligroso: true
                });
            } else if (typeof Swal !== 'undefined') {
                const res = await Swal.fire({
                    title: '¿Eliminar dirección?',
                    text: '¿Deseas quitar esta dirección de tu lista de opciones de entrega?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b'
                });
                confirmado = res.isConfirmed;
            } else {
                confirmado = confirm('¿Estás seguro de que deseas eliminar esta dirección?');
            }

            if (!confirmado) {
                return;
            }

            fetch(`/checkout/eliminar-direccion/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.direcciones = this.direcciones.filter(d => d.id !== id);
                    if (this.direccionSeleccionadaId === id) {
                        if (this.direcciones.length > 0) {
                            this.seleccionarDireccion(this.direcciones[0]);
                        } else {
                            this.direccionSeleccionadaId = null;
                            this.mostrarFormulario = true;
                        }
                    }
                    if (typeof window.alertaToast === 'function') {
                        window.alertaToast(data.message || '¡Dirección eliminada correctamente!');
                    }
                } else {
                    if (typeof window.alertaError === 'function') {
                        window.alertaError(data.message || 'No se pudo eliminar la dirección.');
                    } else {
                        alert(data.message || 'No se pudo eliminar la dirección.');
                    }
                }
            })
            .catch(err => {
                if (typeof window.alertaError === 'function') {
                    window.alertaError('Ocurrió un error al intentar eliminar la dirección.');
                } else {
                    alert('Ocurrió un error al intentar eliminar la dirección.');
                }
            });
        }
    };
}
</script>
@endsection
