import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class', // Ativa o modo escuro baseado na classe 'dark' no HTML

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'Inter', 'sans-serif'], // Exemplo de extensão da família de fontes sans-serif
            },
            colors: {
                dark: {
                    bg: '#1a202c', // Cor de fundo escura
                    text: '#e2e8f0', // Cor do texto
                    white: '#ffffff', // Cor branca (se necessário)
                    gray: {
                        100: '#2d3748', // Tom de cinza para fundo
                        700: '#4a5568', // Outro tom de cinza para fundo
                        800: '#2b3847', // Um tom mais escuro de cinza para fundo
                    },
                },
            },
        },
    },

    plugins: [forms, typography], // Adiciona os plugins de forms e typography do Tailwind CSS
};
