import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import flowbitePlugin from 'flowbite/plugin'

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class', // ✅ Enable class-based dark mode for manual theme switching
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './resources/**/*.jsx',
    "./node_modules/flowbite/**/*.js" // ✅ required for Flowbite
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'system-ui', 'Roboto', 'Helvetica Neue', 'Arial', ...defaultTheme.fontFamily.sans],
      },

      colors: {
        // Primary - Deep Emerald Botanical Green
        primary: {
          50: '#ecfdf5',
          100: '#d1fae5',
          200: '#a7f3d0',
          300: '#6ee7b7',
          400: '#34d399',
          500: '#10b981',
          600: '#059669',
          700: '#047857',
          800: '#065f46',
          900: '#064e3b',
          950: '#022c22',
          DEFAULT: '#059669',
          dark: '#047857',
          light: '#d1fae5',
        },

        // Accent - Warm Amber & Honey Gold (Replaced red/rose completely)
        accent: {
          50: '#fffbeb',
          100: '#fef3c7',
          200: '#fde68a',
          300: '#fcd34d',
          400: '#fbbf24',
          500: '#f59e0b',
          600: '#d97706',
          700: '#b45309',
          800: '#92400e',
          900: '#78350f',
          950: '#451a03',
          DEFAULT: '#f59e0b',
          dark: '#d97706',
          light: '#fde68a',
        },

        // Sage - Soft Botanical Muted Greens
        sage: {
          50: '#f4f7f4',
          100: '#e3ebe4',
          200: '#c6d8c8',
          300: '#9ebf9f',
          400: '#749e75',
          500: '#558156',
          600: '#426743',
          700: '#355236',
          800: '#2c422c',
          900: '#263826',
          DEFAULT: '#749e75',
        },

        // Brand Palette (Deep Emerald Primary + Warm Amber Accent)
        brand: {
          50: '#ecfdf5',
          100: '#d1fae5',
          200: '#a7f3d0',
          300: '#6ee7b7',
          400: '#34d399',
          500: '#10b981',
          600: '#059669',
          700: '#047857',
          800: '#065f46',
          900: '#064e3b',
          DEFAULT: '#059669',
          dark: '#047857',
          light: '#d1fae5',
          amber: '#F59E0B',
          gold: '#D97706',
        },

        // Gold & Warm Metallic Highlights
        gold: {
          50: '#fffbeb',
          100: '#fef3c7',
          200: '#fde68a',
          300: '#fcd34d',
          400: '#fbbf24',
          500: '#f59e0b',
          600: '#d97706',
          700: '#b45309',
          DEFAULT: '#f59e0b',
        },

        // Neutral Surfaces
        surface: {
          DEFAULT: '#F8FAFC',  // Main app container bg
          subtle: '#F1F5F9',   // Card hover / secondary background
          dark: '#1E293B',     // Dark mode card surface
          'dark-subtle': '#0F172A',
          card: '#FFFFFF',     // Clean white card background
          'card-dark': '#1E293B',
        },

        // Page Base Background
        base: {
          DEFAULT: '#FFFFFF', // Light mode base canvas
          dark: '#0B0F19',    // Deep obsidian dark base canvas
        },

        // Border Colors
        border: {
          DEFAULT: '#E2E8F0', // Soft gray border
          subtle: '#F1F5F9',  // Ultra light divider border
          dark: '#334155',    // Dark mode border
        },

        // Typography Ink Colors
        ink: {
          DEFAULT: '#0F172A', // Slate 900 primary body text
          heading: '#022C22', // Deep emerald dark heading text
          muted: '#64748B',   // Slate 500 secondary text
          subtle: '#94A3B8',  // Slate 400 helper text
          dark: '#F8FAFC',    // Light text on dark background
          'heading-dark': '#ECFDF5',
          'muted-dark': '#94A3B8',
        },

        // Semantic Status Colors (No bright red)
        success: {
          DEFAULT: '#10B981',
          light: '#D1FAE5',
          dark: '#047857',
        },
        warning: {
          DEFAULT: '#F59E0B',
          light: '#FEF3C7',
          dark: '#B45309',
        },
        danger: {
          DEFAULT: '#EA580C', // Warm rust orange warning
          light: '#FFEDD5',
          dark: '#C2410C',
        },
        info: {
          DEFAULT: '#3B82F6',
          light: '#DBEAFE',
          dark: '#1D4ED8',
        },
      },

      // Custom Shadows for Modern Card & UI Depth
      boxShadow: {
        'glow-emerald': '0 0 25px -5px rgba(16, 185, 129, 0.3)',
        'glow-amber': '0 0 25px -5px rgba(245, 158, 11, 0.3)',
        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.06)',
        'soft-card': '0 10px 30px -5px rgba(0, 0, 0, 0.05)',
      },
    },
  },

  plugins: [
    forms,          // ✅ Tailwind Forms
    flowbitePlugin, // ✅ Flowbite
  ],
}