const path = require("path");

module.exports = [
  {
    mode: "development",
    devtool: "source-map",
    entry: path.resolve(__dirname, "src/js/script.js"),
    module: {
      rules: [
        {
          test: /\.(js)$/,
          exclude: /node_modules/,
          use: ["babel-loader"],
        },
      ],
    },
    resolve: {
      extensions: [".*", ".js"],
      alias: {
        '@drupal/once': path.resolve(__dirname, 'node_modules/@drupal/once/dist/once.js'),
      },
    },
    output: {
      path: path.resolve(__dirname, "dist/js"),
      filename: "script.js",
      chunkFilename: "[name].[contenthash].js",
      clean: true,
    }
  },
];
