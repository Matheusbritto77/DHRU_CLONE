<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMjRv6Gx0KScIQt3MoY4VZa4HSH3Zjpqq8KQ50" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- Alpine JS para gerenciamento seguro da Sidebar e Dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>
</head>
<body class="antialiased text-apple-dark dark:text-gray-200" style="background-color: #f5f5f7;">
    <script>
        // Restaura persistência global do dark mode imediatamente para evitar FOUC
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.body.style.backgroundColor = '#161617';
        } else {
            document.documentElement.classList.remove('dark');
            document.body.style.backgroundColor = '#f5f5f7';
        }
    </script>

    <x-banner />

    <!-- Container Flexível Pai (Sidebar Horizontal -> Lateral Vertical) -->
    <div class="flex h-screen overflow-hidden bg-[#f5f5f7] dark:bg-[#161617] w-full" x-data="{ sidebarOpen: false }">
        
        <!-- Overlay translúcido para mobile ao abrir a sidebar -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden transition-opacity" @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- O próprio arquivo Admin Navigation agora é a Sidebar Vertical -->
        @include('admin.navigation-menu')

        <!-- Conteúdo Principal - Flex 1 preenchendo o lado direito -->
        <div class="flex-1 flex flex-col h-screen overflow-y-auto relative w-full">
            
            <!-- Navbar Mobile Toggle (substitui navegação grande em telas pequenas) -->
            <header class="sticky top-0 z-30 lg:hidden bg-white/80 dark:bg-[#1d1d1f]/80 backdrop-blur-xl border-b border-gray-100 dark:border-gray-800 h-16 flex items-center justify-between px-4 sm:px-6">
                <!-- Botão Menu Mobile -->
                <button @click="sidebarOpen = true" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white transition-colors">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <!-- Logo Mobile -->
                <a href="{{ route('dashboard') }}" class="block pb-1">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="max-h-8 w-auto object-contain rounded" onerror="this.src='{{ asset('logo3.jpg') }}'">
                </a>
                <div class="w-10"></div> <!-- Placeholder simétrico para centralizar logo -->
            </header>

            <!-- Cabeçalho Específico da Página (Títulos) -->
            @if (isset($header))
                <header class="pt-8 pb-2 px-4 sm:px-6 lg:px-8 max-w-7xl w-full">
                    {{ $header }}
                </header>
            @endif

            <!-- Área Útil das Views (Dashboard, Históricos) -->
            <main class="flex-1 pb-16">
                {{ $slot }}
            </main>
        </div>
        
        <!-- Botão Flutuante Circular: Dark Mode Global -->
        <div class="fixed bottom-6 right-6 z-50">
            <button id="theme-toggle" class="w-12 h-12 rounded-full bg-white dark:bg-[#2c2c2e] shadow-[0_8px_30px_rgba(0,0,0,0.12)] border border-gray-100 dark:border-gray-700/50 flex items-center justify-center text-apple-dark dark:text-gray-200 hover:scale-110 active:scale-95 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-apple-blue focus:ring-offset-2 dark:focus:ring-offset-[#161617]">
                <i id="theme-icon" class="fas fa-moon text-lg drop-shadow-sm"></i>
            </button>
        </div>
    </div>

    <!-- Script Controlador de Tema do Botão -->
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');

        if (document.documentElement.classList.contains('dark')) {
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }

        themeToggleBtn.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) {
                localStorage.theme = 'dark';
                themeIcon.classList.replace('fa-moon', 'fa-sun');
                document.body.style.backgroundColor = '#161617';
            } else {
                localStorage.theme = 'light';
                themeIcon.classList.replace('fa-sun', 'fa-moon');
                document.body.style.backgroundColor = '#f5f5f7';
            }
        });
    </script>
    
    @stack('modals')
    @livewireScripts
</body>
</html>
