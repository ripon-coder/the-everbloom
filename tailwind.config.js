/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
        './config/demo/**/*.php',
    ],
    theme: {
        screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1440px',
        },
        extend: {
            colors: {
                forest: {
                    DEFAULT: '#304C3D',
                    50: '#F2F5F3',
                    100: '#E1E8E3',
                    200: '#C2D1C7',
                    300: '#9DB3A5',
                    400: '#6E8B78',
                    500: '#304C3D',
                    600: '#284033',
                    700: '#203329',
                    800: '#17251E',
                    900: '#0E1713',
                },
                sage: {
                    DEFAULT: '#A8B7A0',
                    light: '#E8EEE5',
                    dark: '#7A8C71',
                },
                ivory: {
                    DEFAULT: '#F8F6F0',
                    light: '#FCFBF8',
                    dark: '#EFECE2',
                },
                beige: {
                    DEFAULT: '#E8DFD0',
                    light: '#F4EFE6',
                    dark: '#D8CCA8',
                },
                ink: {
                    DEFAULT: '#292D29',
                    light: '#424741',
                },
                muted: {
                    DEFAULT: '#585D55',
                    light: '#777D74',
                    dark: '#424741',
                },
            },
            fontFamily: {
                serif: ['"Playfair Display"', 'Georgia', 'serif'],
                sans: ['"Inter"', 'system-ui', '-apple-system', 'sans-serif'],
            },
            letterSpacing: {
                label: '0.2em',
                tightest: '-0.03em',
            },
            borderRadius: {
                none: '0px',
                sm: '2px',
                DEFAULT: '2px',
            },
            boxShadow: {
                soft: '0 4px 20px -2px rgba(41, 45, 41, 0.05)',
                drawer: '0 0 40px rgba(41, 45, 41, 0.15)',
            },
            transitionDuration: {
                DEFAULT: '200ms',
            },
        },
    },
    plugins: [],
};
