<!-- Componente encapsulado em um único div -->
<div>
     <!-- Top Bar -->
    <div class="bg-gray-700 text-sm py-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-4 lg:px-2">
            <div class="flex justify-between items-center">
                <div>
                    <span>💻 <a href="mailto:contato@brserver.tech" class="underline text-blue-400 hover:text-blue-600">contato@brserver.tech</a></span>
                </div>
                <div>
                    <span> 📲 <a href="https://wa.me/5534999442627" class="underline text-blue-400 hover:text-blue-600">+55(34)999442627</a></span>
                </div>
            </div>
        </div>
    </div>
<header>

    <!-- Primary Navigation Menu -->
    <nav x-data="{ open: false }" x-cloak class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left Side -->
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('dashboard') }}">
                            <img src="{{ asset('logo3.jpg') }}" class="block h-16 w-auto mx-auto" alt="Logo">
                        </a>
                    </div>
                </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center ml-auto space-x-4">
                <!-- Dashboard Link -->
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-gray-300 hover:text-white">
                    {{ __('Dashboard') }}
                </x-nav-link>

             <!-- Make Order Dropdown -->
<div class="relative">
    <x-dropdown align="right" width="60">
        <!-- Dropdown Trigger -->
        <x-slot name="trigger">
            <button type="button" class="inline-flex items-center text-gray-300 hover:text-white focus:outline-none" :class="{ 'active': isActive }">
                Fazer Pedido
                <svg class="ml-2 -mr-0.5 h-4 w-4 text-gray-400 group-hover:text-gray-300 transition">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                </svg>
            </button>
        </x-slot>
        <!-- Dropdown Content -->
        <x-slot name="content">
            <div class="w-60 bg-gray-800">
                <x-dropdown-link href="{{ route('imei.index') }}" @click="isActive = 'imei'" class="text-white hover:bg-gray-700">
                    IMEI Services
                </x-dropdown-link>
                <x-dropdown-link href="server-services" @click="isActive = 'server'" class="text-white hover:bg-gray-700">
                    Server Services
                </x-dropdown-link>
                <!-- Add more links as needed -->
            </div>
        </x-slot>
    </x-dropdown>
</div>


             <!-- Histórico Dropdown -->
<div class="relative">
    <x-dropdown align="right" width="60">
        <!-- Dropdown Trigger -->
        <x-slot name="trigger">
            <button type="button" class="inline-flex items-center text-gray-300 hover:text-white focus:outline-none" :class="{ 'active': isActive }">
                Histórico
                <svg class="ml-2 -mr-0.5 h-4 w-4 text-gray-400 group-hover:text-gray-300 transition">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                </svg>
            </button>
        </x-slot>
        <!-- Dropdown Content -->
        <x-slot name="content">
            <div class="w-60 bg-gray-800">
                <x-dropdown-link href="{{ route('imei.history') }}" @click="isActive = 'imei'" class="text-white hover:bg-gray-700 py-2 px-4 block">
                    Histórico de IMEI
                </x-dropdown-link>
                <x-dropdown-link href="{{ route('Server.history') }}" @click="isActive = 'server'" class="text-white hover:bg-gray-700 py-2 px-4 block">
                    Histórico de Server
                </x-dropdown-link>
                <!-- Adicione mais links conforme necessário -->
            </div>
        </x-slot>
    </x-dropdown>
</div>



                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="relative">
                        <x-dropdown align="right" width="60">
                            <!-- Dropdown Trigger -->
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-gray-400 focus:outline-none focus:bg-gray-700 focus:text-gray-400 active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ Auth::user()->currentTeam->name }}
                                        <svg class="ml-2 -mr-0.5 h-4 w-4 text-gray-400 group-hover:text-gray-300 transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>
                            <!-- Dropdown Content -->
                            <x-slot name="content">
                                <div class="w-60 bg-gray-800 border border-gray-700">
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="text-gray-300 hover:text-white">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>
                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}" class="text-gray-300 hover:text-white">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-700"></div>
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>
                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" class="text-gray-300 hover:text-white" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif
<!-- Profile Dropdown -->
<div class="relative">
    <x-dropdown align="right" width="48">
        <!-- Dropdown Trigger -->
        <x-slot name="trigger">
            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                </button>
            @else
                <span class="inline-flex rounded-md">
                    <button type="button" class="inline-flex items-center text-gray-300 hover:text-white focus:outline-none">
                        {{ Auth::user()->name }}
                        <svg class="ml-2 -mr-0.5 h-4 w-4 text-gray-400 group-hover:text-gray-300 transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                </span>
            @endif
        </x-slot>
        
        <!-- Dropdown Content -->
        <x-slot name="content">
            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('Manage Account') }}
            </div>
            <x-dropdown-link href="{{ route('profile.show') }}" class="text-white hover:text-gray-900 focus:text-gray-900">
                {{ __('Profile') }}
            </x-dropdown-link>
            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                <x-dropdown-link href="{{ route('api-tokens.index') }}" class="text-white hover:text-gray-900 focus:text-gray-900">
                    {{ __('API Tokens') }}
                </x-dropdown-link>
            @endif

            <!-- Adicionar Créditos -->
            <x-dropdown-link href="{{ route('add-credits') }}" class="text-white hover:text-gray-900 focus:text-gray-900">
                {{ __('Adicionar Créditos') }}
            </x-dropdown-link>
            
            <div class="border-t border-gray-700"></div>
            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="text-white hover:text-gray-900 focus:text-gray-900">
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>





                <!-- Display Total Credits -->
                <div class="ml-4 hidden sm:flex items-center text-sm text-gray-300">
                    Créditos: {{ app('App\Http\Controllers\CreditController')->getTotalCredits() }}
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open" @click.away="open = false" class="sm:hidden bg-gray-900">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-gray-300 hover:text-white">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('imei.index') }}" class="text-gray-300 hover:text-white">
                IMEI Services
            </x-responsive-nav-link>
            <x-responsive-nav-link href="server-services" class="text-gray-300 hover:text-white">
                Server Services
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('imei.history') }}" class="text-gray-300 hover:text-white">
                Histórico de IMEI
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('Server.history') }}" class="text-gray-300 hover:text-white">
                Histórico de Server
            </x-responsive-nav-link>
            <x-dropdown-link href="{{ route('add-credits') }}" class="text-white hover:text-gray-900 focus:text-gray-900">
    {{ __('Adicionar Créditos') }}
</x-dropdown-link>

            
            <!-- Add more links as needed -->
        </div>
        <div class="pt-4 pb-1 border-t border-gray-800">
            <div class="px-4">
                <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')" class="text-gray-300 hover:text-white">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                               <x-responsive-nav-link href="{{ route('teams.create') }}" class="text-gray-300 hover:text-white">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-700"></div>
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>
                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" class="text-gray-300 hover:text-white" />
                        @endforeach
                    @endif
                @endif
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" class="text-gray-300 hover:text-white">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" class="text-gray-300 hover:text-white">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="text-gray-300 hover:text-white">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
            
        </div>
    </div>
</nav>

