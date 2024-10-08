import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/app.css',
                'resources/css/texteditor.css',
                'resources/js/app.js',
                'resources/js/calendar.js',
                'resources/js/home.js',
                'resources/js/bootstrap.js',
                'resources/js/flunkydj.js',
                'resources/js/search-user.js',
                'resources/js/texteditor.js',
                'resources/js/user-export.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        cssCodeSplit: true,
        assetsInlineLimit: 0,
    },
});

