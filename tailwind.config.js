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
                sans: ['figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#6366F1',
                'text-light': '#E8E3FD',
            },
        },
    },

    plugins: [forms],
};
