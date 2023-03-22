import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [laravel({
        input: ['resources/js/app.js', 'resources/css/app.scss'], ssr: 'resources/js/ssr.js', refresh: true,
    }), vue({
        template: {
            transformAssetUrls: {
                base: null, includeAbsolute: false,
            },
        },
    })], css: {
        preprocessorOptions: {
            sass: {
                aditionalData: ['@import "./resources/css/app.scss"']
            }
        }
    }
});
