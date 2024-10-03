<!DOCTYPE html>
<link rel="icon" href="favicon.ico" type="image/x-icon">

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel' ) }}</title>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMjRv6Gx0KScIQt3MoY4VZa4HSH3Zjpqq8KQ50" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

      
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    

    <!-- Dark Mode Styling -->
    <style>
        /* Estilos para o modo escuro */
        .dark {
            background-color: #1a202c;
            color: #cbd5e0;
        }
        .dark .bg-white {
            background-color: #2d3748 !important; /* Força a cor de fundo escura */
        }
        .dark .text-gray-900 {
            color: #cbd5e0 !important; /* Força a cor do texto clara */
        }
        .dark .text-gray-500 {
            color: #cbd5e0 !important; /* Força a cor do texto clara */
        }
        .dark .hover\:text-gray-700:hover {
            color: #cbd5e0 !important; /* Força a cor do texto clara ao passar o mouse */
        }
        .dark .bg-gray-100 {
            background-color: #4a5568 !important; /* Força a cor de fundo escura */
        }
        .dark .shadow {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); /* Sombra mais escura */
        }
        .dark .dropdown-menu {
            background-color: #2d3748 !important; /* Cor de fundo do dropdown no modo escuro */
        }
        .dark .dropdown-item {
            color: #cbd5e0 !important; /* Cor do texto dos itens do dropdown no modo escuro */
        }
        .dark .dropdown-item:hover {
            background-color: #4a5568 !important; /* Cor de fundo ao passar o mouse no modo escuro */
            color: #ffffff !important; /* Cor do texto ao passar o mouse no modo escuro */
        }
    </style>
</head>
<body class="font-sans antialiased dark:bg-gray-900 dark:text-gray-300">
    <x-banner />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-800">
        @livewire('navigation-menu')
        

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow dark:bg-gray-800 dark:text-gray-300">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('logo3.jpg') }}" class="h-12 w-auto" alt="Logo">
            </div>
            <p class="mb-4">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            <div class="mb-4">
                <a href="mailto:contato@brserver.com" class="text-gray-400 hover:text-gray-200 mx-2">
                    <i class="fas fa-envelope"></i> contato@brserver.com
                </a>
                <a href="https://wa.me/1234567890" class="text-gray-400 hover:text-gray-200 mx-2">
                    <i class="fab fa-whatsapp"></i> +55 (34)99944-2627
                </a>
            </div>
            <div class="flex justify-center space-x-4">
                <a href="https://www.facebook.com" class="text-gray-400 hover:text-gray-200">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.twitter.com" class="text-gray-400 hover:text-gray-200">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.instagram.com" class="text-gray-400 hover:text-gray-200">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.linkedin.com" class="text-gray-400 hover:text-gray-200">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>
    </footer>

    @stack('modals')

    @livewireScripts
      

    
</body>
</html>
