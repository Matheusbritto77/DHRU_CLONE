<x-guest-layout>
    <div class="w-full sm:max-w-[420px] mt-6 px-10 py-10 bg-white/80 dark:bg-[#1d1d1f]/80 backdrop-blur-xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden sm:rounded-[24px]">
        
        <div class="text-center mb-8">
            <a href="/">
                <img src="logo3.jpg" class="h-16 w-auto mx-auto mb-4 rounded-[14px] shadow-sm hover:scale-105 transition-transform" alt="BR Server Logo">
            </a>
            <h3 class="text-[28px] font-semibold text-apple-dark dark:text-white tracking-tight">Entrar</h3>
            <p class="text-gray-500 dark:text-gray-400 text-[15px] mt-1 font-light">Acesse sua conta para continuar</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Erros de Validação -->
            @if ($errors->any())
                <div class="mb-4 text-red-600 text-sm text-center bg-red-50 dark:bg-red-900/30 p-3 rounded-xl border border-red-100 dark:border-red-900">
                    <ul class="list-none">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 text-center bg-green-50 dark:bg-green-900/30 p-3 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="E-mail" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
            </div>

            <div>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Senha" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
            </div>

            <div class="flex items-center justify-between pt-1 pb-2">
                <label class="flex items-center text-gray-500 dark:text-gray-400 cursor-pointer group">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-apple-blue bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-apple-blue focus:ring-2 focus:ring-offset-0 transition-colors cursor-pointer">
                    <span class="ml-2 text-[14px] group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">Lembrar-me</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-[14px] text-apple-blue hover:text-blue-700 dark:hover:text-blue-400 transition-colors" href="{{ route('password.request') }}">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 px-4 bg-[#0071e3] hover:bg-[#0077ED] active:bg-[#0051a8] text-white font-medium rounded-[14px] text-[17px] tracking-wide transition-all duration-200 shadow-sm outline-none">
                    Fazer Login
                </button>
            </div>
            
            @if (Route::has('register'))
            <div class="text-center mt-6">
                <p class="text-sm border-t border-gray-100 dark:border-gray-800 pt-6 text-gray-500 dark:text-gray-400">
                    Ainda não tem conta? <a href="{{ route('register') }}" class="text-apple-blue hover:underline">Crie agora</a>
                </p>
            </div>
            @endif
        </form>
    </div>
</x-guest-layout>
