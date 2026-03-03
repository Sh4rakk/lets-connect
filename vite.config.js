import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const corsOrigin = env.APP_URL || 'http://localhost';

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
        server: {
            host: '127.0.0.1',
            port: 5173,
            strictPort: true,
            cors: {
                origin: [corsOrigin],
            },
            hmr: {
                host: '127.0.0.1',
            },
        },
    };
});
