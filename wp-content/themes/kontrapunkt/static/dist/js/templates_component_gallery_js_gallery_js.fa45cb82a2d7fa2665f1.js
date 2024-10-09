"use strict";
(globalThis["webpackChunkstatic_base"] = globalThis["webpackChunkstatic_base"] || []).push([["templates_component_gallery_js_gallery_js"],{

/***/ "./templates/component/gallery/js/gallery.js":
/*!***************************************************!*\
  !*** ./templates/component/gallery/js/gallery.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var lightgallery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lightgallery */ "./node_modules/lightgallery/lightgallery.es5.js");


const elArray = document.getElementsByClassName("js-gallery");
for (let i = 0; i < elArray.length; i++) {
  const gallery = (0,lightgallery__WEBPACK_IMPORTED_MODULE_0__["default"])(elArray[i], {
    selector: ".js-lightbox"
  });
}

/***/ })

}]);
//# sourceMappingURL=templates_component_gallery_js_gallery_js.fa45cb82a2d7fa2665f1.js.map