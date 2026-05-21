<x-guest-layout>
    <!-- Contenedor principal del formulario -->
    <div class="w-full max-w-[380px] mx-auto flex flex-col items-center">
        
        <!-- Logo y Encabezado -->
        <div class="text-center mb-10 w-full flex flex-col items-center">
            <img src="{{ asset('imagenes/federacion cafeteros logo.png') }}" alt="Logo Federación de Cafeteros" class="h-[100px] w-auto mb-4 object-contain">
            
            <h2 class="text-[#9e052b] text-lg font-bold tracking-wide">Comité de Cafeteros</h2>
            <p class="text-[#9e052b] text-sm mb-8">del Tolima</p>
            
            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Acceso Institucional</h3>
            <p class="text-gray-500 text-sm mt-1">Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Estado de la sesión -->
        <x-auth-session-status class="mb-4 w-full" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="w-full">
            @csrf

            <!-- Correo electrónico -->
            <div class="mb-5">
                <label for="email" class="block font-bold text-xs text-gray-700 mb-1.5">Correo electrónico</label>
                <input id="email" 
                       class="block w-full border-0 bg-[#f8f9fa] rounded-lg focus:ring-2 focus:ring-[#9e052b] focus:bg-white px-4 py-3.5 text-sm text-gray-900 placeholder-gray-400 transition-colors" 
                       type="email" 
                       name="email" 
                       :value="old('email')" 
                       required 
                       autofocus 
                       autocomplete="username" 
                       placeholder="nombre@cafedecolombia.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Contraseña -->
            <div class="mb-5">
                <label for="password" class="block font-bold text-xs text-gray-700 mb-1.5">Contraseña</label>
                <input id="password" 
                       class="block w-full border-0 bg-[#f8f9fa] rounded-lg focus:ring-2 focus:ring-[#9e052b] focus:bg-white px-4 py-3.5 text-sm text-gray-900 placeholder-gray-400 transition-colors tracking-widest"
                       type="password"
                       name="password"
                       required 
                       autocomplete="current-password" 
                       placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Checkbox (Solicitar recuperación / Recordar) -->
            <div class="flex items-center mb-8">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#9e052b] shadow-sm focus:ring-[#9e052b] cursor-pointer w-4 h-4" name="remember">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="ms-2 text-[13px] text-gray-600 hover:text-[#9e052b] hover:underline transition-colors">
                            Solicitar recuperación de contraseña
                        </a>
                    @else
                        <span class="ms-2 text-[13px] text-gray-600">Solicitar recuperación de contraseña</span>
                    @endif
                </label>
            </div>

            <!-- Botón Ingresar -->
            <div>
                <button type="submit" class="w-full bg-[#9e052b] hover:bg-[#7a0421] text-white font-semibold py-3.5 px-4 rounded-lg transition duration-200 ease-in-out shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9e052b] text-sm">
                    Ingresar al sistema
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
