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
    extend: {
        colors: {
            primary: 'hsl(219.6,43.9%,21%)',
            secondary: 'hsl(39.6,100%,59.6%)',
            thirdly: 'hsl(210.9,52.2%,86.9%)'
        },
        animation : {
            'blink': 'blinker 1s linear infinite'
        },
        keyframes: {
            blinker: {
                    '50%': {'opacity': '0'}
            }
        }
    },
  },
  plugins: [],
}
