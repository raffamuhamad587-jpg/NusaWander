import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Epilogue', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    bg: '#eefdf5',
                    dark: '#003527',
                    text: '#404945',
                    input: '#e8f7ef',
                    primary: '#174c3c',
                    primaryHover: '#003527',
                    footer: 'rgba(232,247,239,0.7)',
                    header: 'rgba(238,253,245,0.85)',
                },
            },
            borderRadius: {
                'card': '1.5rem', // 24px
            },
        },
    },

    plugins: [forms],
};