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
    safelist: [
        'bg-red-500', 'border-red-600',
        'bg-orange-500', 'border-orange-600',
        'bg-amber-500', 'border-amber-600',
        'bg-emerald-500', 'border-emerald-600',
        'bg-teal-500', 'border-teal-600',
        'bg-blue-500', 'border-blue-600',
        'bg-indigo-500', 'border-indigo-600',
        'bg-purple-500', 'border-purple-600',
        'bg-pink-500', 'border-pink-600',
        'bg-rose-500', 'border-rose-600',

    ],
    plugins: [forms],
};
