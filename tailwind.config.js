import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                /* Primária */
                primary: {
                    DEFAULT: '#7F8F5E',
                    600: '#6e7d52',
                    700: '#5f6d47',
                },

                /* Texto */
                text: {
                    DEFAULT: '#4C4C4C',
                    light: '#6B6B6B',
                },

                /* Cinzas */
                gray: {
                    700: '#4C4C4C',
                    400: '#BABABA',
                    300: '#B4B4B4',
                },

                /* Backgrounds */

                background: {
                    soft: '#F6F6F6',
                    white: '#FFFFFF',
                },

                /* Estados */
                danger: '#F05143',
                info: '#437DF0',

                /* Neutra */
                beige: '#C7AF94',
            }
        },
    },

    plugins: [
        forms,
        function ({ addUtilities }) {
            addUtilities({
                    '.required::after': {
                    content: '" *"',
                    color: '#F05143',
                },
            })
        }
    ],
};
