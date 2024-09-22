module.exports = {
  plugins: [
    {
      name: 'preset-default',
      params: {
        overrides: {
          removeViewBox: false,
          inlineStyles: {
            onlyMatchedOnce: false,
          },
        },
      },
    },
    // {
    //   name: 'convertColors',
    //   params: {
    //     currentColor: true,
    //   },
    // },
    'removeStyleElement',
    'removeDimensions',
    // 'prefixIds',
    {
      name: 'addAttributesToSVGElement',
      params: {
        attributes: [{ 'aria-hidden': 'true' }],
      },
    },
    // {
    //   name: 'addClassesToSVGElement',
    //   params: {
    //     className: ['h-full max-w-full'],
    //   },
    // },
  ],
}
