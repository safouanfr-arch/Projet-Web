import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    proxy: {
      '/index.php': {
        target: 'http://back-end/public',
        changeOrigin: true,
      },
      '/media': {
        target: 'http://back-end/public',
        changeOrigin: true,
      },
    },
  },
})
