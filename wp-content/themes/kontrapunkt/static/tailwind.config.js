const colors = require("tailwindcss/colors");
const defaultTheme = require("tailwindcss/defaultTheme");

const supernova = {
  'DEFAULT': '#ffcc02',
  '50': '#fefce8',
  '100': '#fffac2',
  '200': '#fff287',
  '300': '#ffe343',
  '400': '#ffcc02', // default
  '500': '#efb603',
  '600': '#ce8c00',
  '700': '#a46304',
  '800': '#884d0b',
  '900': '#733f10',
  '950': '#432005',
}


const primary = supernova;
const secondary = colors.blue;

module.exports = {
  content: [
    // jit mode not supporting options https://github.com/ngneat/tailwind/issues/100
    "./templates/**/*.{twig,js,css}",
    "./styleguide/**/*.{twig,js,css}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Anonymous Pro", ...defaultTheme.fontFamily.sans],
      },
      typography: {
        DEFAULT: {
          css: {
            color: "#000",
            img: {
              display: "inline-block",
            },
          },
        },
      },
      colors: {
        primary: primary,
        secondary: secondary,
        gray: colors.neutral,
        yellow: primary,
      },
      container: {
        center: true,
        padding: "1rem",
      },
    },
  },
  plugins: [
    require("@tailwindcss/aspect-ratio"),
    require("@tailwindcss/forms"),
    require("@tailwindcss/typography"),
  ],
};
