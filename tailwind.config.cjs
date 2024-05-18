/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.svelte",
    ],
    theme: {
        colors: {
          black: "#000000",
          dark: "#161616",
          color1: "#011140",
          color2: "#ACD7F2",
          color3: "#0B8CD5",
          color4: "#2758DA",
          white: "#FFFFFF",
          red: "#BF0404",
          green: "#02731E",
          grayBlue: "#b2bdc4",
          gray: colors.gray,
          ligthGreen: "#54ffaf",
          redLight: "#ff6464",
          background: "#131724",
        },
        extend: {}
      },
    plugins: [],
}
