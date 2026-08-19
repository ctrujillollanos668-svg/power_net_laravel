@extends('layouts.tienda')

@section('titulo', 'Métodos de Pago Oficiales - PowerNet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-6">
        <a href="{{ route('tienda.inicio') }}" class="hover:text-gray-900 transition">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-gray-900">Métodos de Pago</span>
    </nav>

    {{-- Encabezado --}}
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
            <span>💳 Métodos de Pago Oficiales</span>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                100% Seguros
            </span>
        </h1>
        <p class="text-xs text-gray-500 mt-1">Conoce los canales autorizados de recaudo, transferencias bancarias y formas de pago disponibles en PowerNet.</p>
    </div>

    {{-- Canales de Pago Activos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        
        {{-- Tarjeta 1: Bancolombia --}}
        <div class="bg-white rounded-3xl p-6 border border-gray-200/90 shadow-2xs hover:shadow-xs transition flex flex-col justify-between relative overflow-hidden">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl font-bold border border-yellow-100">
                        🏦
                    </div>
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase rounded-lg border border-emerald-200">
                        Transferencia Directa
                    </span>
                </div>

                <h3 class="text-base font-black text-gray-900 mb-1">Bancolombia</h3>
                <p class="text-xs text-gray-500 mb-4">Cuenta de Ahorros empresarial para transferencias por App o corresponsal.</p>

                <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-200/80 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Tipo de Cuenta:</span>
                        <span class="font-bold text-gray-900">Ahorros</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Número de Cuenta:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono font-black text-gray-950">245-000192-84</span>
                            <button 
                                type="button" 
                                onclick="navigator.clipboard.writeText('24500019284'); window.alertaToast('¡Número de cuenta copiado al portapapeles!');"
                                class="w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-600 hover:text-black flex items-center justify-center text-[10px] cursor-pointer" 
                                title="Copiar">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Titular:</span>
                        <span class="font-bold text-gray-900">PowerNet S.A.S.</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">NIT:</span>
                        <span class="font-bold text-gray-900">901.458.729-1</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2 text-[11px] text-gray-500">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>Acreditación en 15 - 30 minutos</span>
            </div>
        </div>

        {{-- Tarjeta 2: Nequi / Daviplata --}}
        <div class="bg-white rounded-3xl p-6 border border-gray-200/90 shadow-2xs hover:shadow-xs transition flex flex-col justify-between relative overflow-hidden">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-[#7c3aed] flex items-center justify-center text-xl font-bold border border-purple-100">
                        📱
                    </div>
                    <span class="px-2.5 py-1 bg-purple-50 text-[#7c3aed] text-[10px] font-black uppercase rounded-lg border border-purple-200">
                        Pago Móvil Rápido
                    </span>
                </div>

                <h3 class="text-base font-black text-gray-900 mb-1">Nequi & Daviplata</h3>
                <p class="text-xs text-gray-500 mb-4">Transfiere desde tu celular al instante sin costo adicional ni comisiones.</p>

                <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-200/80 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Plataformas:</span>
                        <span class="font-bold text-gray-900">Nequi / Daviplata</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Número Celular:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono font-black text-gray-950">300 892 4110</span>
                            <button 
                                type="button" 
                                onclick="navigator.clipboard.writeText('3008924110'); window.alertaToast('¡Número celular copiado al portapapeles!');"
                                class="w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-600 hover:text-black flex items-center justify-center text-[10px] cursor-pointer" 
                                title="Copiar">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Titular:</span>
                        <span class="font-bold text-gray-900">PowerNet Recaudos</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2 text-[11px] text-gray-500">
                <i class="fa-solid fa-bolt text-yellow-500"></i>
                <span>Acreditación inmediata</span>
            </div>
        </div>

        {{-- Tarjeta 3: Pago Contra Entrega --}}
        <div class="bg-white rounded-3xl p-6 border border-gray-200/90 shadow-2xs hover:shadow-xs transition flex flex-col justify-between relative overflow-hidden">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold border border-blue-100">
                        💵
                    </div>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-black uppercase rounded-lg border border-blue-200">
                        Paga en Casa
                    </span>
                </div>

                <h3 class="text-base font-black text-gray-900 mb-1">Pago Contra Entrega</h3>
                <p class="text-xs text-gray-500 mb-4">Paga en efectivo directamente al domiciliario cuando recibas tu pedido en tu puerta.</p>

                <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-200/80 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Modalidad:</span>
                        <span class="font-bold text-gray-900">Efectivo al recibir</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Cobertura:</span>
                        <span class="font-bold text-gray-900">Nacional e Intermunicipal</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Recargo:</span>
                        <span class="font-black text-emerald-600">$0 COP (Sin costo extra)</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2 text-[11px] text-gray-500">
                <i class="fa-solid fa-truck text-blue-500"></i>
                <span>Máxima seguridad y confianza</span>
            </div>
        </div>

        {{-- Tarjeta 4: Tarjetas de Crédito y Débito --}}
        <div class="bg-white rounded-3xl p-6 border border-gray-200/90 shadow-2xs hover:shadow-xs transition flex flex-col justify-between relative overflow-hidden">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                        💳
                    </div>
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase rounded-lg border border-emerald-200">
                        Online Seguro
                    </span>
                </div>

                <h3 class="text-base font-black text-gray-900 mb-1">Tarjetas de Crédito y Débito</h3>
                <p class="text-xs text-gray-500 mb-4">Aceptamos todas las franquicias con tecnología de encriptación TLS 256 bits.</p>

                <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-200/80 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Franquicias:</span>
                        <span class="font-bold text-gray-900">Visa, Mastercard, Amex, Diners</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Cuotas:</span>
                        <span class="font-bold text-gray-900">Hasta 36 cuotas</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Seguridad:</span>
                        <span class="font-black text-emerald-600">3D Secure / OTP</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2 text-[11px] text-gray-500">
                <i class="fa-solid fa-lock text-emerald-600"></i>
                <span>Procesamiento cifrado de punto a punto</span>
            </div>
        </div>

        {{-- Tarjeta 5: PSE --}}
        <div class="bg-white rounded-3xl p-6 border border-gray-200/90 shadow-2xs hover:shadow-xs transition flex flex-col justify-between relative overflow-hidden">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold border border-indigo-100">
                        ⚡
                    </div>
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase rounded-lg border border-indigo-200">
                        Débito Bancario
                    </span>
                </div>

                <h3 class="text-base font-black text-gray-900 mb-1">PSE (Pagos Seguros en Línea)</h3>
                <p class="text-xs text-gray-500 mb-4">Paga directamente con el débito de tu cuenta de ahorros o corriente en cualquier banco del país.</p>

                <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-200/80 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Bancos:</span>
                        <span class="font-bold text-gray-900">Todos los bancos de Colombia</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-medium">Confirmación:</span>
                        <span class="font-bold text-emerald-600">Automática en segundos</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2 text-[11px] text-gray-500">
                <i class="fa-solid fa-shield-halved text-indigo-600"></i>
                <span>Respaldado por la red bancaria nacional</span>
            </div>
        </div>

    </div>

    {{-- Garantías y Compromisos de Seguridad --}}
    <div class="bg-gradient-to-br from-gray-900 to-slate-900 text-white rounded-3xl p-8 sm:p-10 shadow-lg">
        <div class="max-w-2xl mb-8">
            <h2 class="text-xl sm:text-2xl font-black mb-2 flex items-center gap-2.5">
                <span>🛡️</span>
                <span>Tu compra en PowerNet está protegida</span>
            </h2>
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                Todas las operaciones se procesan bajo estrictos protocolos de ciberseguridad, con soporte continuo y emisión de factura electrónica legal.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-xs">
            <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                <div class="text-xl mb-2">🧾</div>
                <h4 class="font-black text-white mb-1">Facturación Legal DIAN</h4>
                <p class="text-gray-400 leading-relaxed text-[11px]">Emitimos tu factura POS / electrónica con toda la validez tributaria para garantías.</p>
            </div>

            <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                <div class="text-xl mb-2">⚡</div>
                <h4 class="font-black text-white mb-1">Despacho Rápido</h4>
                <p class="text-gray-400 leading-relaxed text-[11px]">Preparamos y despachamos tu pedido con Servientrega o Coordinadora el mismo día.</p>
            </div>

            <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                <div class="text-xl mb-2">🔄</div>
                <h4 class="font-black text-white mb-1">Garantía PowerNet</h4>
                <p class="text-gray-400 leading-relaxed text-[11px]">Radica devoluciones o cambios fácilmente desde tu panel de cliente ante cualquier novedad.</p>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-gray-300">
            <div>
                ¿Tienes dudas con tu pago? Contáctanos a <strong class="text-white">soporte@powernet.com</strong> o al WhatsApp <strong class="text-yellow-400">+57 300 892 4110</strong>
            </div>
            <a href="{{ route('tienda.inicio') }}#productos-seccion" class="px-5 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-gray-950 font-black rounded-xl transition shadow-xs self-start sm:self-auto">
                Ir a la Tienda
            </a>
        </div>
    </div>

</div>
@endsection
