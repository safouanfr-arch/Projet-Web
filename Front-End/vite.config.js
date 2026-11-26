import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const { PRESS_BACKEND_URL } = loadEnv(mode, process.cwd(), 'PRESS_')
  const target = PRESS_BACKEND_URL || 'http://127.0.0.1:8080'
  return {
    plugins: [vue()],
    server: {
      proxy: {
        '/index.php': { target, changeOrigin: true },
        '/media': { target, changeOrigin: true },
      },
    },
  }
})
