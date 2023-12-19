/// <reference types="vitest" />
/// <reference types="vite/client" />
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react-swc';

export default defineConfig({
  plugins: [react()],
  resolve: {
    alias: {
      '@': '/src',
    },
  },
  test: {
    name: 'node',
    root: './src/__test__',
    environment: 'jsdom',
    setupFiles: ['./setup.node.ts'],
    globals: true
  },
});
