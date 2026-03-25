<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="favicon.ico" type="image/x-icon">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts and Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        <script>
            // Anti-FOUC (Evita piscar na tela ao carregar modo escuro)
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        <style>
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                -webkit-font-smoothing: antialiased;
            }
        </style>
    </head>
    <body class="text-gray-900 dark:text-gray-100 antialiased bg-[#f5f5f7] dark:bg-gray-900 transition-colors duration-300 relative">
        <!-- Floating Theme Toggle -->
        <button id="theme-toggle" type="button" aria-label="Toggle Theme" class="absolute top-6 right-6 z-50 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 shadow hover:shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full w-12 h-12 flex items-center justify-center transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700">
            <i id="theme-toggle-dark-icon" class="fas fa-moon hidden text-lg"></i>
            <i id="theme-toggle-light-icon" class="fas fa-sun hidden text-lg"></i>
        </button>

        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0">
            {{ $slot }}
        </div>

        @livewireScripts
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
                const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
                const themeToggleBtn = document.getElementById('theme-toggle');

                if (document.documentElement.classList.contains('dark')) {
                    themeToggleLightIcon.classList.remove('hidden');
                } else {
                    themeToggleDarkIcon.classList.remove('hidden');
                }

                themeToggleBtn.addEventListener('click', function() {
                    themeToggleDarkIcon.classList.toggle('hidden');
                    themeToggleLightIcon.classList.toggle('hidden');

                    if (localStorage.getItem('color-theme') === 'light') {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    } else if (localStorage.getItem('color-theme') === 'dark') {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    } else {
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        }
                    }
                });
            });
        </script>
    </body>
</html>
