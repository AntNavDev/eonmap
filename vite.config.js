import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/map.js',
                'resources/js/browse.js',
                'resources/js/occurrence.js',
                'resources/js/taxon.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // vis-timeline + Leaflet are legitimately large; suppress the noise.
        chunkSizeWarningLimit: 750,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
