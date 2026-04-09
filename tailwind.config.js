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
            colors:{
                neutral: {
                    primary: '#fff',
                    'primary-soft': '#f8fafc',
                    secondary: '#f1f5f9',
                    'secondary-soft': '#f9fafb',
                    'secondary-medium': '#e5e7eb',
                     default: '#d1d5db',
                     'default-soft': '#e5e7eb',
                },
                brand: {
                    DEFAULT: '#3b82f6',
                    medium: '#60a5fa',
                },
            },
        },
    },

    plugins: [forms],
};
