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
        screens: {
            sm: '540px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1536px',
        },
        extend: {
            fontFamily: {
                sans: ['DM Sans', 'Helvetica Neue', 'Helvetica', 'Arial', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', 'Helvetica Neue', 'Helvetica', 'Arial', ...defaultTheme.fontFamily.sans],
                mid: ['Poppins', 'Helvetica Neue', 'Helvetica', 'Arial', ...defaultTheme.fontFamily.sans],
                mono: ['Roboto', 'Helvetica Neue', 'Helvetica', 'Arial', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                'brand-blue': '#1456f0',
                'brand-pink': '#ea5ec1',
                'dark-surface': '#181e25',
                'text-primary': '#222222',
                'text-secondary': '#45515e',
                'text-muted': '#8e8e93',
            },
            boxShadow: {
                card: 'rgba(0, 0, 0, 0.08) 0px 4px 6px',
                ambient: 'rgba(0, 0, 0, 0.08) 0px 0px 22.576px',
                brand: 'rgba(44, 30, 116, 0.16) 0px 0px 15px',
                elevated: 'rgba(36, 36, 36, 0.08) 0px 12px 16px -4px',
            },
        },
    },

    plugins: [forms],
};
