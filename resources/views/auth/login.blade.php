<x-guest-layout>
    <div x-data="{ verPass: false }">
        
        <div class="mb-6">
            <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-1.5">
                <span>Iniciar Sesión</span>
                <span class="text-amber-500">🔑</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Ingresa tus credenciales para acceder a tu cuenta</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-xs font-bold text-emerald-600 bg-emerald-50 p-3 rounded-xl border border-emerald-200" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

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
                        autofocus 
                        placeholder="ejemplo@correo.com"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-[11px] text-red-500 font-bold" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                        Contraseña
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-[11px] font-bold text-[#7c3aed] hover:underline" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <input 
                        id="password" 
                        :type="verPass ? 'text' : 'password'"
                        name="password"
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
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

            <!-- Remember Me -->
            <div class="flex items-center justify-between py-0.5">
                <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                    <input id="remember_me" type="checkbox" class="w-4 h-4 rounded-md border-slate-300 text-yellow-400 focus:ring-yellow-400 cursor-pointer" name="remember">
                    <span class="text-xs font-semibold text-slate-600">Mantener sesión iniciada</span>
                </label>
            </div>

            <!-- Botón Submit -->
            <button 
                type="submit" 
                class="w-full mt-2 rounded-2xl bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 hover:from-yellow-500 hover:to-amber-500 text-slate-950 font-black text-xs py-3.5 shadow-lg shadow-yellow-400/25 hover:shadow-yellow-400/40 hover:scale-[1.01] active:scale-[0.99] transition flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-right-to-bracket text-xs"></i>
                <span>Acceder a Mi Cuenta</span>
            </button>

            <!-- Registro Link -->
            <div class="pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    ¿Aún no tienes una cuenta? 
                    <a href="{{ route('register') }}" class="text-[#7c3aed] font-extrabold hover:underline">
                        Regístrate gratis
                    </a>
                </p>
            </div>

            <div class="pt-1 flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                <i class="fa-solid fa-lock text-emerald-500"></i>
                <span>Protección SSL cifrada de punto a punto</span>
            </div>
        </form>

    </div>
</x-guest-layout>
