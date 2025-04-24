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

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                dark: {
                    '100': '#1a1a1a',
                    '200': '#2d2d2d',
                    '300': '#404040',
                    '400': '#525252',
                    '500': '#666666',
                    '600': '#808080',
                    '700': '#999999',
                    '800': '#b3b3b3',
                    '900': '#cccccc',
                },
            },
        },
    },

    plugins: [forms],
};
