@extends(Auth::user()->role_id == 1 ? 'layouts.sidebaradmin' : 'layouts.tienda')

@section('title', 'Mi Perfil')

@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8" x-data="{ modalEliminar: false }">

    {{-- Breadcrumb para vista de cliente --}}
    @if(Auth::user()->role_id != 1)
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
            <a href="{{ route('tienda.inicio') }}" class="hover:text-yellow-600">Inicio</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
            <span class="text-gray-900 font-bold">Mi Perfil</span>
        </nav>
    @endif

    {{-- Encabezado Principal --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 flex items-center gap-2.5">
                <span class="text-2xl">👤</span>
                Mi Perfil y Cuenta
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Administra tu información personal, dirección de entrega y la seguridad de tu acceso a PowerNet.
            </p>
        </div>

        {{-- Badge de Rol --}}
        <div>
            @if(Auth::user()->role_id == 1)
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-violet-100 text-violet-800 text-xs font-bold border border-violet-200">
                    <i class="fa-solid fa-shield-halved"></i>
                    Administrador del Sistema
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-bold border border-yellow-200">
                    <i class="fa-solid fa-user-check"></i>
                    Cliente PowerNet
                </span>
            @endif
        </div>
    </div>

    {{-- Alerta de Notificación / Éxito --}}
    @if (session('status') === 'profile-updated')
        <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3 text-emerald-800 text-xs font-bold shadow-2xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
            <span>Tus datos de perfil han sido actualizados correctamente.</span>
        </div>
    @elseif (session('status') === 'password-updated')
        <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3 text-emerald-800 text-xs font-bold shadow-2xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
            <span>Tu contraseña se ha cambiado exitosamente.</span>
        </div>
    @endif

    {{-- Errores Generales --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 text-red-800 text-xs shadow-2xs space-y-1">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-sm"></i>
                <span>Por favor corrige los siguientes errores:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ===== COLUMNA IZQUIERDA: RESUMEN DE USUARIO ===== --}}
        <div class="space-y-6">
            
            <div class="bg-white rounded-3xl shadow-xs border border-gray-200/80 p-6 text-center">
                {{-- Avatar con Iniciales --}}
                <div class="w-24 h-24 mx-auto rounded-3xl bg-gradient-to-tr from-[#0b1220] to-slate-800 text-yellow-400 flex items-center justify-center text-3xl font-black shadow-md border-4 border-yellow-400/40 mb-4">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(Auth::user()->apellido ?? '', 0, 1)) }}
                </div>

                <h3 class="text-lg font-black text-gray-900">
                    {{ Auth::user()->name }} {{ Auth::user()->apellido }}
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ Auth::user()->email }}</p>

                <div class="mt-5 pt-4 border-t border-gray-100 space-y-2 text-left text-xs">
                    <div class="flex items-center justify-between text-gray-600 py-1">
                        <span class="text-gray-400 font-medium flex items-center gap-2">
                            <i class="fa-solid fa-phone text-yellow-500 w-4 text-center"></i> Teléfono:
                        </span>
                        <span class="font-bold text-gray-800">{{ Auth::user()->telefono ?? 'No registrado' }}</span>
                    </div>

                    <div class="flex items-center justify-between text-gray-600 py-1">
                        <span class="text-gray-400 font-medium flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-yellow-500 w-4 text-center"></i> Dirección:
                        </span>
                        <span class="font-bold text-gray-800 text-right line-clamp-1 max-w-[150px]">{{ Auth::user()->direccion ?? 'No registrada' }}</span>
                    </div>

                    <div class="flex items-center justify-between text-gray-600 py-1">
                        <span class="text-gray-400 font-medium flex items-center gap-2">
                            <i class="fa-solid fa-calendar text-yellow-500 w-4 text-center"></i> Miembro desde:
                        </span>
                        <span class="font-bold text-gray-800">{{ Auth::user()->created_at ? Auth::user()->created_at->format('d/m/Y') : 'Reciente' }}</span>
                    </div>
                </div>
            </div>

            {{-- Enlaces Rápidos --}}
            @if(Auth::user()->role_id != 1)
                <div class="bg-white rounded-3xl shadow-xs border border-gray-200/80 p-5 space-y-2">
                    <h4 class="text-xs font-black uppercase text-gray-400 tracking-wider mb-3">Accesos del Cliente</h4>
                    
                    <a href="{{ url('/mis-pedidos') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-yellow-50 hover:text-yellow-800 transition text-xs font-bold text-gray-700 group">
                        <span class="flex items-center gap-2.5">
                            <i class="fa-solid fa-box-open text-yellow-500"></i>
                            Mis Pedidos y Envíos
                        </span>
                        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ url('/carrito') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-yellow-50 hover:text-yellow-800 transition text-xs font-bold text-gray-700 group">
                        <span class="flex items-center gap-2.5">
                            <i class="fa-solid fa-cart-shopping text-yellow-500"></i>
                            Mi Carrito de Compras
                        </span>
                        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ route('tienda.inicio') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-yellow-50 hover:text-yellow-800 transition text-xs font-bold text-gray-700 group">
                        <span class="flex items-center gap-2.5">
                            <i class="fa-solid fa-store text-yellow-500"></i>
                            Explorar Tienda
                        </span>
                        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            @endif

        </div>

        {{-- ===== COLUMNA DERECHA: FORMULARIOS ===== --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- ===== SECCIÓN 1: DATOS PERSONALES ===== --}}
            <div class="bg-white rounded-3xl shadow-xs border border-gray-200/80 p-6 sm:p-8">
                <div class="pb-4 mb-6 border-b border-gray-100">
                    <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-pen text-yellow-500 text-sm"></i>
                        Información Personal y de Contacto
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">Actualiza tus datos para tus futuras compras y entregas.</p>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nombre --}}
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name', $user->name) }}"
                                required
                                autofocus
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none">
                            @error('name')
                                <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Apellido --}}
                        <div>
                            <label for="apellido" class="block text-xs font-bold text-gray-700 uppercase mb-1">Apellido</label>
                            <input
                                id="apellido"
                                name="apellido"
                                type="text"
                                value="{{ old('apellido', $user->apellido) }}"
                                placeholder="Ej. Pérez Gómez"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none">
                            @error('apellido')
                                <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Correo Electrónico --}}
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-700 uppercase mb-1">Correo Electrónico</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none">
                            @error('email')
                                <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Teléfono --}}
                        <div>
                            <label for="telefono" class="block text-xs font-bold text-gray-700 uppercase mb-1">Teléfono / Celular</label>
                            <input
                                id="telefono"
                                name="telefono"
                                type="text"
                                value="{{ old('telefono', $user->telefono) }}"
                                placeholder="Ej. 3224037962"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none">
                            @error('telefono')
                                <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Dirección --}}
                    <div>
                        <label for="direccion" class="block text-xs font-bold text-gray-700 uppercase mb-1">Dirección de Entrega / Residencia</label>
                        <input
                            id="direccion"
                            name="direccion"
                            type="text"
                            value="{{ old('direccion', $user->direccion) }}"
                            placeholder="Ej. Calle 45 # 12-34, Barrio El Centro"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none">
                        @error('direccion')
                            <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botón Guardar --}}
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button
                            type="submit"
                            class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-extrabold text-xs px-6 py-3 rounded-xl transition shadow-xs flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Guardar Información
                        </button>
                    </div>
                </form>
            </div>

            {{-- ===== SECCIÓN 2: CAMBIO DE CONTRASEÑA ===== --}}
            <div class="bg-white rounded-3xl shadow-xs border border-gray-200/80 p-6 sm:p-8">
                <div class="pb-4 mb-6 border-b border-gray-100">
                    <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-yellow-500 text-sm"></i>
                        Seguridad y Contraseña
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">Asegúrate de usar una contraseña larga y difícil de adivinar.</p>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    {{-- Contraseña Actual --}}
                    <div>
                        <label for="update_password_current_password" class="block text-xs font-bold text-gray-700 uppercase mb-1">Contraseña Actual</label>
                        <input
                            id="update_password_current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none">
                        @error('current_password', 'updatePassword')
                            <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nueva Contraseña --}}
                        <div>
                            <label for="update_password_password" class="block text-xs font-bold text-gray-700 uppercase mb-1">Nueva Contraseña</label>
                            <input
                                id="update_password_password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none">
                            @error('password', 'updatePassword')
                                <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirmar Contraseña --}}
                        <div>
                            <label for="update_password_password_confirmation" class="block text-xs font-bold text-gray-700 uppercase mb-1">Confirmar Contraseña</label>
                            <input
                                id="update_password_password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 focus:outline-none">
                            @error('password_confirmation', 'updatePassword')
                                <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Botón Cambiar Contraseña --}}
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button
                            type="submit"
                            class="bg-[#0b1220] hover:bg-black text-white font-bold text-xs px-6 py-3 rounded-xl transition shadow-xs flex items-center gap-2">
                            <i class="fa-solid fa-key text-yellow-400"></i>
                            Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>

            {{-- ===== SECCIÓN 3: ZONA DE PELIGRO ===== --}}
            <div class="bg-red-50/50 rounded-3xl border border-red-200/80 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-sm font-black text-red-700 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                            Eliminar Cuenta
                        </h2>
                        <p class="text-xs text-gray-500 mt-1 max-w-md">
                            Una vez eliminada la cuenta, todos sus recursos y datos se borrarán de forma permanente.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="modalEliminar = true"
                        class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl transition shadow-2xs shrink-0 flex items-center gap-2">
                        <i class="fa-solid fa-trash-can"></i>
                        Eliminar mi cuenta
                    </button>
                </div>
            </div>

        </div>

    </div>

    {{-- ===== MODAL CONFIRMAR ELIMINACIÓN ===== --}}
    <div
        x-show="modalEliminar"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4">
        
        <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" @click="modalEliminar = false"></div>

        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl z-10" @click.stop>
            <div class="mb-4">
                <h3 class="text-lg font-black text-red-600 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    ¿Estás seguro de eliminar tu cuenta?
                </h3>
                <p class="text-xs text-gray-500 mt-2">
                    Por favor ingresa tu contraseña actual para confirmar que deseas eliminar permanentemente tu cuenta de PowerNet.
                </p>
            </div>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="password_delete" class="block text-xs font-bold text-gray-700 uppercase mb-1">Contraseña</label>
                    <input
                        id="password_delete"
                        name="password"
                        type="password"
                        placeholder="Ingresa tu contraseña"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-red-400 focus:border-red-400 focus:outline-none">
                    @error('password', 'userDeletion')
                        <p class="text-red-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button
                        type="button"
                        @click="modalEliminar = false"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-xs transition">
                        Confirmar Eliminación
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
