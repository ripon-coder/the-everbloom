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
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  plugins: [
    forms,          // ✅ Tailwind Forms
    flowbitePlugin, // ✅ Flowbite
  ],
}
