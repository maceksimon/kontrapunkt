"use strict";
(globalThis["webpackChunkstatic_base"] = globalThis["webpackChunkstatic_base"] || []).push([["templates_component_slider_js_slider_js"],{

/***/ "./templates/component/slider/js/slider.js":
/*!*************************************************!*\
  !*** ./templates/component/slider/js/slider.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var swiper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! swiper */ "./node_modules/swiper/swiper.mjs");
/* harmony import */ var swiper_modules__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! swiper/modules */ "./node_modules/swiper/modules/index.mjs");


new swiper__WEBPACK_IMPORTED_MODULE_0__["default"]('.component-slider .swiper-container', {
  modules: [swiper_modules__WEBPACK_IMPORTED_MODULE_1__.Navigation],
  slidesPerView: 3,
  spaceBetween: 20,
  watchOverflow: true,
  navigation: {
    nextEl: ".component-slider .custom-button-next",
    prevEl: ".component-slider .custom-button-prev"
  },
  breakpoints: {
    250: {
      slidesPerView: 1,
      spaceBetween: 24
    },
    768: {
      slidesPerView: 2,
      spaceBetween: 24
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 24
    }
  }
});

/***/ })

}]);
//# sourceMappingURL=templates_component_slider_js_slider_js.babd6754fbb855002e36.js.map