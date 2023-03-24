/** @type {import('tailwindcss').Config} */

const colors = require('tailwindcss/colors')

module.exports = {
  content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
  theme: {
    extend: {},
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      'white': colors.white,
      'transparent-white': '#7C767F',
      'transparent-hover': '#CECBD0',
      'dark-purple': "#14081B",
      'dark-medium-purple': "#1B0F20",
      'medium-purple': "#2B1534",
      'electric-purple': "#B53FF0",
      'medium-light-purple': '#431857',
      'font-blue': "#5959BE",
      'profile-blue': "#7B7DFF",
    }
  },
  plugins: [],
};
