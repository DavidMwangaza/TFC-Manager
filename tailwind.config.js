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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Lora', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                primary: {
                    DEFAULT: 'hsl(218, 62%, 18%)',
                    light: 'hsl(218, 55%, 30%)',
                    dark: 'hsl(218, 70%, 12%)',
                },
                accent: {
                    DEFAULT: 'hsl(45, 95%, 55%)',
                    warm: 'hsl(15, 80%, 55%)',
                },
                success: 'hsl(152, 60%, 40%)',
                warning: 'hsl(38, 92%, 50%)',
                danger: 'hsl(0, 72%, 51%)',
                info: 'hsl(200, 80%, 50%)',
                surface: {
                    primary: 'hsl(220, 20%, 97%)',
                    card: 'hsl(0, 0%, 100%)',
                    elevated: 'hsl(220, 25%, 95%)',
                }
            }
        },
    },

    plugins: [forms],
};
