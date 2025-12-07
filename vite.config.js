import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1',   // IMPORTANT : forcer IPv4
        port: 5173,
        strictPort: true,    // ne pas changer de port
    },
});
