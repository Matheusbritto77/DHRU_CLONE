<!-- Menu Lateral (Sidebar Desktop & Mobile) -->
<aside x-data="{ openModal: false }" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white/90 dark:bg-[#1d1d1f]/90 backdrop-blur-[30px] border-r border-gray-100 dark:border-gray-800/60 transform lg:translate-x-0 transition-transform duration-300 ease-[cubic-bezier(0.4,0.0,0.2,1)] flex flex-col shadow-2xl lg:shadow-[4px_0_24px_rgba(0,0,0,0.02)] lg:relative shrink-0">
    
    <!-- Cabeçalho Lateral / Logo -->
    <div class="h-[88px] flex justify-between items-center px-6 border-b border-gray-100 dark:border-gray-800/80 shrink-0">
        <a href="{{ route('dashboard') }}" class="block">
            <img src="{{ asset('logo.png') }}" class="block max-h-12 w-auto object-contain rounded-xl shadow-sm" alt="BR Server" onerror="this.src='{{ asset('logo3.jpg') }}'">
        </a>
        <!-- Botão p/ fechar menu nativo no mobile visível -->
        <button @click="sidebarOpen = false" class="lg:hidden w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Navegação e Links (Rolável via Y) -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-2 custom-scrollbar">
        
        <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-2 mb-2">Principal</p>
        
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-[16px] transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-apple-blue shadow-md text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/60' }}">
            <i class="fas fa-chart-pie w-6 flex justify-center text-[18px] {{ request()->routeIs('dashboard') ? 'text-white' : '' }}"></i>
            <span class="text-[15px]">Painel de Controle</span>
        </a>

        <a href="{{ route('admin.order.history') }}" class="flex items-center space-x-3 px-4 py-3 rounded-[16px] transition-all duration-200 {{ request()->routeIs('admin.order.history') ? 'bg-apple-blue shadow-md text-white font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/60' }}">
            <i class="fas fa-clock-rotate-left w-6 flex justify-center text-[18px] {{ request()->routeIs('admin.order.history') ? 'text-white' : '' }}"></i>
            <span class="text-[15px]">Histórico Administrativo</span>
        </a>

        <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-8 mb-2">Gerenciamento</p>

        <!-- Dropdowns convertidos em Accordions Laterais -->
        <!-- Seção: Serviços -->
        <div x-data="{ expanded: false }" class="mt-2 text-sm">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-3 rounded-[16px] text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-all duration-200">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-layer-group w-6 flex justify-center text-[18px]"></i>
                    <span class="text-[15px] font-medium">Serviços</span>
                </div>
                <i class="fas fa-chevron-down text-[12px] transform transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="expanded" x-collapse x-transition.opacity class="pl-13 pr-4 space-y-1 block mt-1 ml-4 border-l-2 border-gray-100 dark:border-gray-800 py-1">
                <button @click="openModal = true" class="w-full text-left ml-2 px-4 py-2 text-[14px] font-medium text-gray-500 dark:text-gray-400 hover:text-apple-blue rounded-[12px] hover:bg-apple-blue/10 dark:hover:bg-apple-blue/20 transition-colors flex items-center">
                    <i class="fas fa-percentage text-[10px] mr-3"></i> Margens de lucro
                </button>
                <a href="{{ route('Admin.services') }}" class="w-full text-left ml-2 px-4 py-2 text-[14px] font-medium text-gray-500 dark:text-gray-400 hover:text-apple-blue rounded-[12px] hover:bg-apple-blue/10 dark:hover:bg-apple-blue/20 transition-colors flex items-center">
                    <i class="fas fa-edit text-[10px] mr-3"></i> Editar serviços
                </a>
            </div>
        </div>

        <!-- Seção: API -->
        <div x-data="{ expanded: false }" class="mt-2 text-sm">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-3 rounded-[16px] text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-all duration-200">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-plug w-6 flex justify-center text-[18px]"></i>
                    <span class="text-[15px] font-medium">Conexão API</span>
                </div>
                <i class="fas fa-chevron-down text-[12px] transform transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="expanded" x-collapse x-transition.opacity class="pl-13 pr-4 space-y-1 block mt-1 ml-4 border-l-2 border-gray-100 dark:border-gray-800 py-1">
                <a href="{{ route('imei.index') }}" class="w-full text-left ml-2 px-4 py-2 text-[14px] font-medium text-gray-500 dark:text-gray-400 hover:text-apple-blue rounded-[12px] hover:bg-apple-blue/10 dark:hover:bg-apple-blue/20 transition-colors flex items-center">
                    <i class="fas fa-link text-[10px] mr-3"></i> Conectar API Dhru
                </a>
                <a href="server-services" class="w-full text-left ml-2 px-4 py-2 text-[14px] font-medium text-gray-500 dark:text-gray-400 hover:text-apple-blue rounded-[12px] hover:bg-apple-blue/10 dark:hover:bg-apple-blue/20 transition-colors flex items-center">
                    <i class="fas fa-edit text-[10px] mr-3"></i> Editar API
                </a>
            </div>
        </div>

        <!-- Seção: Créditos -->
        <div x-data="{ expanded: false }" class="mt-2 text-sm">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-3 rounded-[16px] text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-all duration-200">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-wallet w-6 flex justify-center text-[18px] text-yellow-500"></i>
                    <span class="text-[15px] font-medium">Balanço / Créditos</span>
                </div>
                <i class="fas fa-chevron-down text-[12px] transform transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="expanded" x-collapse x-transition.opacity class="pl-13 pr-4 space-y-1 block mt-1 ml-4 border-l-2 border-gray-100 dark:border-gray-800 py-1">
                <a href="{{ route('imei.history') }}" class="w-full text-left ml-2 px-4 py-2 text-[14px] font-medium text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 rounded-[12px] hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors flex items-center">
                    <i class="fas fa-plus-circle text-[10px] mr-3"></i> Adicionar
                </a>
                <a href="{{ route('Server.history') }}" class="w-full text-left ml-2 px-4 py-2 text-[14px] font-medium text-gray-500 dark:text-gray-400 hover:text-red-500 rounded-[12px] hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center">
                    <i class="fas fa-minus-circle text-[10px] mr-3"></i> Remover Saldo
                </a>
            </div>
        </div>

    </nav>

    <!-- Rodapé Embutido -> Crédito + Perfil de Usuário -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-800/80 shrink-0 bg-white/50 dark:bg-black/10">
        
        <!-- Bloco de Créditos Destacado -->
        <div class="bg-gray-50 dark:bg-[#2c2c2e] rounded-[20px] p-4 flex flex-col items-center justify-center space-y-1 mb-4 border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group hover:border-apple-blue/30 transition-colors">
            <!-- Icon Background decorativo -->
            <i class="fas fa-coins absolute -right-4 -bottom-4 text-[60px] text-black/5 dark:text-white/5 transform group-hover:scale-110 transition-transform"></i>
            
            <span class="text-[10px] uppercase tracking-widest text-gray-400 dark:text-gray-500 font-bold w-full text-left relative z-10">Total na Conta</span>
            <div class="text-[28px] font-bold text-apple-dark dark:text-white tracking-tighter w-full text-left relative z-10 font-mono">
                ${{ app('App\Http\Controllers\CreditController')->getTotalCredits() ?? '0.00' }}
            </div>
        </div>

        <!-- Botão Menus do Usuário -->
        <div x-data="{ profileOpen: false }" class="relative w-full">
            <button @click="profileOpen = !profileOpen" class="w-full flex items-center justify-between px-3 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-[16px] hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none">
                <div class="flex items-center">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="h-10 w-10 rounded-full object-cover mr-3 shadow-sm border border-gray-200 dark:border-gray-700" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    @else
                        <div class="h-10 w-10 rounded-full bg-apple-blue/10 dark:bg-apple-blue/20 text-apple-blue border border-apple-blue/20 flex items-center justify-center mr-3 font-semibold text-lg shadow-sm">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </div>
                    @endif
                    <div class="flex flex-col items-start truncate max-w-[120px]">
                        <span class="text-[14px] text-apple-dark dark:text-white font-semibold truncate w-full">{{ Auth::user()->name ?? 'Administrador' }}</span>
                        <span class="text-[12px] text-gray-400 dark:text-gray-500 font-normal truncate w-full">{{ Auth::user()->email ?? 'admin@brserver' }}</span>
                    </div>
                </div>
                <i class="fas fa-chevron-up text-gray-400 text-[10px] transition-transform" :class="profileOpen ? 'rotate-180' : ''"></i>
            </button>

            <!-- Popup de Conta (Voa para cima do botão) -->
            <div x-show="profileOpen" @click.away="profileOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute bottom-full left-0 mb-3 w-full bg-white dark:bg-[#2c2c2e] border border-gray-100 dark:border-gray-700/80 rounded-[20px] shadow-[0_10px_40px_rgba(0,0,0,0.1)] py-2 z-50 overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-50 dark:border-gray-700/50 mb-1 pointer-events-none">
                    <span class="block text-[10px] uppercase tracking-widest text-gray-400 dark:text-gray-500 font-bold">Conta</span>
                </div>
                
                <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2.5 text-[14px] font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-apple-dark dark:hover:text-white transition">
                    <i class="fas fa-user-circle w-5 mr-1 opacity-50"></i> Meu Perfil
                </a>
                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <a href="{{ route('api-tokens.index') }}" class="flex items-center px-4 py-2.5 text-[14px] font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-apple-dark dark:hover:text-white transition">
                        <i class="fas fa-key w-5 mr-1 opacity-50"></i> API Tokens
                    </a>
                @endif
                <div class="border-t border-gray-50 dark:border-gray-700/50 my-1"></div>
                <!-- Sair / Logout -->
                <form method="POST" action="{{ route('logout') }}" class="m-0 group">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2.5 text-[14px] font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <i class="fas fa-sign-out-alt w-5 mr-1 group-hover:-translate-x-1 transition-transform"></i> Encerrar Sessão
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal do painel (lucros) encapsulado da rota e arquivos mortos -->
    @includeIf('admin.modal.lucro')

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.4); 
            border-radius: 20px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.8); 
        }
    </style>
</aside>
