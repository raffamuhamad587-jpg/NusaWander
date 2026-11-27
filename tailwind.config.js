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
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                heading: ['Poppins', ...defaultTheme.fontFamily.sans],
                body: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ocean: {
                    DEFAULT: '#173B57',
                    dark: '#00253E',
                    light: '#244e70',
                },
                teal: {
                    DEFAULT: '#1E6D66',
                    accent: '#278C83',
                    hover: '#16544F',
                    light: '#E6F4F2',
                },
                cream: {
                    DEFAULT: '#FFF8F1',
                    sand: '#F4EBDD',
                    soft: '#FFFEFA',
                },
                coral: {
                    DEFAULT: '#E98B70',
                    text: '#9A3412',
                    light: '#FDEEEA',
                },
                brand: {
                    bg: '#FFF8F1',
                    surface: '#FFFFFF',
                    dark: '#00253E',
                    text: '#1F2933',
                    muted: '#475569',
                    input: '#F4EBDD',
                    primary: '#1E6D66',
                    primaryHover: '#16544F',
                    accent: '#E98B70',
                    footer: '#F4EBDD',
                    header: 'rgba(255, 254, 250, 0.96)',
                },
            },
            borderRadius: {
                card: '0.75rem',
            },
        },
    },

    plugins: [forms],
};
