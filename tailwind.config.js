import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/**
 * HealthNexus — sistema de diseño
 *
 * La paleta se deriva del logotipo oficial (public/images/logo-healthnexus.png):
 *   brand  → azul marino/índigo de la cruz y la palabra "Health"
 *   accent → violeta de la "N" y la palabra "Nexus"
 * Los colores de estado (rojo, ámbar, verde) se reservan para estados clínicos.
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    // Modo oscuro solo bajo demanda (clase .dark). Antes se activaba con el
    // sistema operativo y dejaba pantallas a medio pintar.
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './config/navegacion.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#EEF0FB',
                    100: '#DDE1F7',
                    200: '#BCC3EF',
                    300: '#939DE3',
                    400: '#6A74D3',
                    500: '#4C55C0',
                    600: '#3A40A6',
                    700: '#2F3488',
                    800: '#262A6B',
                    900: '#1E2257',
                    950: '#141739',
                },
                accent: {
                    50: '#F4F1FE',
                    100: '#EAE4FD',
                    200: '#D6CBFB',
                    300: '#B9A6F7',
                    400: '#9A7DF0',
                    500: '#7E5AE6',
                    600: '#6B45D6',
                    700: '#5A37B8',
                    800: '#4A2F95',
                    900: '#3E2A78',
                    950: '#261953',
                },
                // Alias heredados: se conservan para no romper vistas existentes,
                // pero ahora apuntan a la paleta oficial.
                nexus: {
                    primary: '#1E2257',
                    secondary: '#3A40A6',
                    accent: '#9A7DF0',
                    success: '#10B981',
                    warning: '#F59E0B',
                    danger: '#EF4444',
                    bg: '#F7F8FC',
                    card: '#FFFFFF',
                    text: '#1E293B',
                    muted: '#64748B',
                    'secondary-hover': '#2F3488',
                },
            },
            boxShadow: {
                card: '0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.06)',
                pop: '0 10px 30px -10px rgb(20 23 57 / 0.25)',
            },
            keyframes: {
                'toast-in': {
                    '0%': { opacity: 0, transform: 'translateY(8px) scale(.98)' },
                    '100%': { opacity: 1, transform: 'translateY(0) scale(1)' },
                },
            },
            animation: {
                'toast-in': 'toast-in .25s ease-out',
            },
        },
    },

    plugins: [forms],
};
