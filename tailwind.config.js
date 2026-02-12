import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',

    // 👇 IMPORTANTES (para componentes Blade + clases en PHP)
    './resources/views/components/**/*.blade.php',
    './app/View/Components/**/*.php',

    // Opcional: JS/Vue
    './resources/**/*.js',
    './resources/**/*.vue',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        gv: {
          black: '#0B0B0C',
          gold: '#C8A24A',
          goldSoft: '#E8D7A6',
          ink: '#141416',
          line: '#232326',
          paper: '#0F0F11',
        },
      },
      boxShadow: {
        gv: '0 10px 25px -10px rgba(0,0,0,.55)',
      },
    },
  },

  plugins: [forms],
};
