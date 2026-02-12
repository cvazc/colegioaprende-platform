import type { Config } from 'tailwindcss';

export default {
  content: ['./index.html', './src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#f4f2fc',
          100: '#e9e4f8',
          200: '#d2c8f1',
          300: '#b6a5e7',
          400: '#977dde',
          500: '#7e5fd2',
          600: '#6847be',
          700: '#5132a4',
          800: '#3f2781',
          900: '#312278',
        },
        accent: {
          50: '#effcfd',
          100: '#d4f7fa',
          200: '#aeedf3',
          300: '#75deea',
          400: '#33c8d8',
          500: '#02afc0',
          600: '#058ca0',
          700: '#0b7081',
          800: '#115967',
          900: '#134b57',
        },
      }
    }
  },
  plugins: [],
} satisfies Config;
