import type { Config } from 'tailwindcss';

export default {
  content: ['./index.html', './src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#f3faf9',
          100: '#d7f0ec',
          200: '#afe2da',
          300: '#7dcdbf',
          400: '#4cb3a3',
          500: '#2d9b8c',
          600: '#217e72',
          700: '#1d655d',
          800: '#1b524c',
          900: '#1a4540'
        }
      }
    }
  },
  plugins: [],
} satisfies Config;
