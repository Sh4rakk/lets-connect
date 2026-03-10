import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

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
        },
        colors: {
            ...colors,
            'white': '#ffffff',
            'black': '#000000',
            'gray': {
                50: '#f9fafb',
                100: '#f3f4f6',
                200: '#e5e7eb',
                300: '#d1d5db',
                400: '#9ca3af',
                500: '#6b7280',
                600: '#4b5563',
                700: '#374151',
                800: '#1f2937',
                900: '#111827',
            },
            'deltion-blue': {
                50:  '#f0f0f5',
                100: '#e1e1ed',
                200: '#c3c3db',
                300: '#a5a5c9',
                400: '#8787b7',
                500: '#6969a5',
                600: '#545487',
                700: '#3f3f69',
                800: '#2a2a4b',
                900: '#343469',
                950: '#1c1c38',
            },
            'deltion-orange': {
                50:  '#fffaf5',
                100: '#fff0e0',
                200: '#fed9b8',
                300: '#fdc18f',
                400: '#fca966',
                500: '#fb9140',
                600: '#fa7a1a',
                700: '#e86c0f',
                800: '#c65b0d',
                900: '#f58220',
                950: '#b35f18',
            },
        },
    },

    plugins: [forms],
};