import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
            },
            colors: {
                // Fleet Fox Dark Theme Color Palette
                'fleet': {
                    'dark': '#1e1b4b',      // Dark indigo background
                    'darker': '#312e81',    // Darker indigo for cards
                    'accent': '#f97316',    // Neon orange for borders/accents
                    'accent-light': '#fb923c', // Lighter orange for hover states
                    'text': '#e2e8f0',      // Light text
                    'text-muted': '#94a3b8', // Muted text
                    'success': '#10b981',   // Green for completed tasks
                    'warning': '#f59e0b',  // Yellow for due soon
                    'danger': '#ef4444',   // Red for overdue
                }
            },
            animation: {
                'glow': 'glow 2s ease-in-out infinite alternate',
                'float': 'float 3s ease-in-out infinite',
            },
            keyframes: {
                glow: {
                    '0%': { boxShadow: '0 0 5px #f97316, 0 0 10px #f97316, 0 0 15px #f97316' },
                    '100%': { boxShadow: '0 0 10px #f97316, 0 0 20px #f97316, 0 0 30px #f97316' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-5px)' },
                }
            },
            backdropBlur: {
                'xs': '2px',
            }
        },
    },

    plugins: [forms],
};
