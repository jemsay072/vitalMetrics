import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    
    resolve: {
        alias: {
            '@': '/resources/js',
        }
    },

    server: {
        host: "www.sandbox.vitalmetrics.com",
        port: 5173,
        hmr:{
            host: "www.sandbox.vitalmetrics.com"
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
