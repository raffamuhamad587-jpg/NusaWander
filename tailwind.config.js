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
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                heading: ['"Playfair Display"', 'Epilogue', ...defaultTheme.fontFamily.sans],
                editorial: ['"Playfair Display"', 'Georgia', 'serif'],
                body: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ocean: {
                    DEFAULT: '#173B57',
                    dark: '#00253E',
                    light: '#244e70',
                },
                teal: {
                    DEFAULT: '#278C83',
                    accent: '#2CB7A6',
                    hover: '#1E6D66',
                    light: '#E6F4F2',
                },
                cream: {
                    DEFAULT: '#FFF8F1',
                    sand: '#F4EBDD',
                    soft: '#FFFEFA',
                },
                sage: {
                    DEFAULT: '#A8BFA3',
                    light: '#EBF2EA',
                    dark: '#2A3D28',
                },
                coral: {
                    DEFAULT: '#E98B70',
                    light: '#FDEEEA',
                },
                brand: {
                    bg: '#FFF8F1',
                    dark: '#00253E',
                    text: '#1F2933',
                    input: '#F4EBDD',
                    primary: '#278C83',
                    primaryHover: '#1E6D66',
                    footer: '#FFF8F1',
                    header: 'rgba(255,254,250,0.95)',
                },
            },
            borderRadius: {
                'card': '1.5rem', // 24px
            },
        },
    },

    plugins: [forms],
};