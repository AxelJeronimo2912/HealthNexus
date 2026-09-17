import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                nexus: {
                    primary: '#172554',   // 🔵 Azul oscuro (Médicos)
                    secondary: '#7C3AED', // 🟣 Violeta (Enfermería)
                    accent: '#A78BFA',    // 💜 Lavanda (Laboratorio)
                    success: '#10B981',   // 🟢 Verde (Farmacia)
                    warning: '#FBBF24',   // 🟡 Amarillo (Citas)
                    danger: '#EF4444',    // 🔴 Rojo (Urgencias)
                    bg: '#F8FAFC',        // ⚪ Gris muy claro (Fondo)
                    card: '#FFFFFF',      // ⚪ Blanco (Tarjetas)
                    text: '#1E293B',      // ⚫ Gris oscuro (Texto principal)
                    muted: '#64748B',     // 🔘 Gris (Texto secundario / Inventario)
                    
                    'secondary-hover': '#6D28D9', // Hover para botones secundarios
                }
            }
        },
    },

    plugins: [forms],
};