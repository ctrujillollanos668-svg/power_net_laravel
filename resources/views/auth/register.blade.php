<x-guest-layout>
    <div x-data="{ verPass: false, verPassConf: false }">
        
        <div class="mb-6">
            <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-1.5">
                <span>Crear Cuenta</span>
                <span class="text-amber-500">✨</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Regístrate para comprar y rastrear tus pedidos</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nombre Completo
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-user text-xs"></i>
                    </div>
                    <input 
                        id="name" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus 
                        placeholder="Ej. Juan Pérez"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-[11px] text-red-500 font-bold" />
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                    Correo Electrónico
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </div>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="ejemplo@correo.com"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-[11px] text-red-500 font-bold" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                    Contraseña
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <input 
                        id="password" 
                        :type="verPass ? 'text' : 'password'"
                        name="password"
                        required 
                        autocomplete="new-password"
                        placeholder="Mínimo 8 caracteres"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition" />
                    <button 
                        type="button" 
                        @click="verPass = !verPass" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                        <i class="fa-solid" :class="verPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-[11px] text-red-500 font-bold" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                    Confirmar Contraseña
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                    </div>
                    <input 
                        id="password_confirmation" 
                        :type="verPassConf ? 'text' : 'password'"
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        placeholder="Repita la contraseña"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition" />
                    <button 
                        type="button" 
                        @click="verPassConf = !verPassConf" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                        <i class="fa-solid" :class="verPassConf ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-[11px] text-red-500 font-bold" />
            </div>

            <!-- Botón Submit -->
            <button 
                type="submit" 
                class="w-full mt-2 rounded-2xl bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 hover:from-yellow-500 hover:to-amber-500 text-slate-950 font-black text-xs py-3.5 shadow-lg shadow-yellow-400/25 hover:shadow-yellow-400/40 hover:scale-[1.01] active:scale-[0.99] transition flex items-center justify-center gap-2 cursor-pointer">
                <span>Crear Mi Cuenta</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>

            <!-- Login Link -->
            <div class="pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    ¿Ya tienes una cuenta? 
                    <a href="{{ route('login') }}" class="text-[#7c3aed] font-extrabold hover:underline">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </form>

    </div>
</x-guest-layout>
