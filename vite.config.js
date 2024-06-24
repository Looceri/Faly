import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/app.js',
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  server: {
    host: 'localhost',
    port: 5173,
    hmr: {
      port: 5500, // Explicitly define the port for HMR WebSocket connection
    },
    proxy: {
      '/chatgpt': {
        target: 'http://localhost:8000', // Laravel server
        changeOrigin: true,
        secure: false,
      },
    },
  },
});
