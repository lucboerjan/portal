import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';  // <-- toevoegen

export default defineConfig({
    plugins: [
        tailwindcss(),  // <-- toevoegen, VOOR laravel plugin
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
            ],
            refresh: true,
        }),
    ],
    server: {
        cors: true,
        hmr: {
            host: '127.0.0.1',
        },
    },
});