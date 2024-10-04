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
        sans: ["Montserrat", ...defaultTheme.fontFamily.sans],
      },
      typography: {
        DEFAULT: {
          css: {
            color: "#000",
            img: {
              display: "inline-block",
            },
            blockquote: {
              color: "#f15d22",
              strong: {
                color: "#f15d22",
              },
            },
            h2: {
              marginTop: "1.5rem",
              marginBottom: "0.75rem",
            },
            h3: {
              marginTop: "1.5rem",
              marginBottom: "0.75rem",
            },
            h4: {
              marginTop: "1.5rem",
              marginBottom: "0.75rem",
            },
            h5: {
              marginTop: "1.5rem",
              marginBottom: "0.75rem",
            },
          },
        },
        primary: {
          css: {
            color: "#000",
            a: {
              color: primary.DEFAULT,
              fontWeight: "inherit",
              "&:hover": {
                color: "#c2410c",
              },
            },
          },
        },
        center: {
          css: {
            margin: "auto",
          },
        },
        narrow: {
          css: {
            maxWidth: "768px",
          },
        },
        wide: {
          css: {
            maxWidth: "none",
          },
        },
      },
      colors: {
        primary: primary,
        secondary: secondary,
        gray: colors.neutral,
      },
      container: {
        center: true,
        padding: "1rem",
      },
      maxWidth: {
        "1/3": "33.3%",
        "1/2": "50%",
      },
    },
  },
  plugins: [
    require("@tailwindcss/aspect-ratio"),
    require("@tailwindcss/forms"),
    require("@tailwindcss/typography"),
  ],
};
