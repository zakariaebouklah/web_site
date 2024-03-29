/** @type {import('tailwindcss').Config} */
module.exports = {
  mode: 'jit',
  darkMode: 'class',
  content: [
      "./assets/**/*.{vue,js,ts,jsx,tsx}",
      "./templates/**/*.{html,twig}",
      "./templates/web_site/*.{html,twig}"
  ],
  theme: {
      screens: {
          '2xl': {'max': '1535px'},
          // => @media (max-width: 1535px) { ... }

          'xl': {'max': '1285px'},
          // => @media (max-width: 1285px) { ... }

          'lg': {'max': '1023px'},
          // => @media (max-width: 1023px) { ... }

          'md': '545px',
          // => @media (min-width: 545px) { ... }

          'sm': {'max': '670px'},
          // => @media (max-width: 670px) { ... }

          'small': {'max': '545px'},
          // => @media (max-width: 545px) { ... }
      },
    extend: {
        boxShadow: {
            '3xl': '0 3px 10px rgb(0 0 0 / 0.2)',
        },
        colors: {
            primary: 'hsl(219.6,43.9%,21%)',
            secondary: 'hsl(39.6,100%,59.6%)',
            thirdly: 'hsl(210.9,52.2%,86.9%)'
        },
        animation : {
            'blink': 'blinker 1s linear infinite',
            'rotation': 'spinner 3s ease-in-out infinite'
        },
        keyframes: {
            blinker: {
                    '50%': {'opacity': '0'}
            },
            spinner: {
                '0%': { transform: 'rotate(0deg)' },
                '100%': { transform: 'rotate(360deg)' },
            }
        }
    },
  },
  plugins: [],
}
