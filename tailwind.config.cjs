/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
const colorClasses = {
  color1 : "#081f3d",
          color2 : "#1f4287",
          color3 : "#278ea5",
          color4 : "#21e6c1",
          black: "#000000",
          dark: "#161616",
          white: "#FFFFFF",
          red: "#BF0404",
          green: "#02731E",
          grayBlue: "#c7d2da",
          gray: colors.gray,
          ligthGreen: "#54ffaf",
          redLight: "#ff6464",
          background: "#ececec",
          transparent: "transparent",
          zelle: "#6a1ccd",
          binance: "#F3BA2F",
};
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.svelte",
    ],
    safelist: [
      ...Object.keys(colorClasses).map((color) => `border-${color}`),
      ...Object.keys(colorClasses).map((color) => `hover:bg-${color}`)
    ],
    theme: {
        colors: {
          color1 : "#081f3d",
          color2 : "#1f4287",
          color3 : "#278ea5",
          color4 : "#21e6c1",
          black: "#000000",
          dark: "#161616",
          white: "#FFFFFF",
          red: "#BF0404",
          green: "#02731E",
          grayBlue: "#c7d2da",
          gray: colors.gray,
          ligthGreen: "#54ffaf",
          redLight: "#ff6464",
          background: "#ececec",
          transparent: "transparent",
          zelle: "#6a1ccd",
          binance: "#F3BA2F",
        },
        extend: {}
      },
    plugins: [],
}
