import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Fira Sans', ...defaultTheme.fontFamily.sans],
                mono: ['Fira Code', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                navy: {
                    50: '#F0F1F3',
                    100: '#D4D6DB',
                    200: '#B3B7C0',
                    300: '#9298A5',
                    400: '#7A8191',
                    500: '#0F172A',
                    600: '#0D1525',
                    700: '#0B1120',
                    800: '#090E1A',
                    900: '#060A13',
                },
                sky: {
                    50: '#E6F2F9',
                    100: '#B8DBF0',
                    200: '#8AC3E6',
                    300: '#5CABDC',
                    400: '#3A98D4',
                    500: '#0369A1',
                    600: '#035E8E',
                    700: '#02507A',
                    800: '#024266',
                    900: '#01304A',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    secondary: '#F8FAFC',
                    muted: '#E8ECF1',
                },
                ink: {
                    DEFAULT: '#020617',
                    muted: '#64748B',
                    light: '#94A3B8',
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgba(15, 23, 42, 0.06), 0 1px 2px -1px rgba(15, 23, 42, 0.06)',
                'card-hover': '0 4px 12px 0 rgba(15, 23, 42, 0.08), 0 2px 4px -2px rgba(15, 23, 42, 0.08)',
                'dropdown': '0 4px 16px 0 rgba(15, 23, 42, 0.12)',
            },
        },
    },

    plugins: [forms, typography],
};
