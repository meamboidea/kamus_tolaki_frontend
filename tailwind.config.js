import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                brand: {
                    50: '#fdfbe7',
                    100: '#fbf7c3',
                    200: '#f7ed89',
                    300: '#f1db48',
                    400: '#eac51c',
                    500: '#d9a710',
                    600: '#b87f0b',
                    700: '#935c0c',
                    800: '#76490f',
                    900: '#643d11',
                    950: '#3a2005',
                }
            }
        },
    },

    plugins: [forms],
};
