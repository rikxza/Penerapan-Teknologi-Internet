import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // 1. Tambahkan baris ini agar dark mode bisa dikontrol pakai class 'dark' di HTML
    darkMode: 'class', 

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Figtree sudah oke, ini font standar Laravel yang bersih
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // 2. Tambahkan border radius ekstra jika ingin sudut yang lebih halus seperti di gambar
            borderRadius: {
                '4xl': '2rem',
                '5xl': '3rem',
            },
            // 3. Opsi: Tambahkan warna kustom jika ingin persis seperti referensi
            colors: {
                sidebar: '#F8FAFC',
            }
        },
    },

    plugins: [forms],
};