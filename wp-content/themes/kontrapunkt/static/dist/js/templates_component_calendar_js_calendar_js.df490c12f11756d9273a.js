"use strict";
(globalThis["webpackChunkstatic_base"] = globalThis["webpackChunkstatic_base"] || []).push([["templates_component_calendar_js_calendar_js"],{

/***/ "./templates/component/calendar/js/calendar.js":
/*!*****************************************************!*\
  !*** ./templates/component/calendar/js/calendar.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _fullcalendar_core__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @fullcalendar/core */ "./node_modules/@fullcalendar/core/index.js");
/* harmony import */ var _fullcalendar_daygrid__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @fullcalendar/daygrid */ "./node_modules/@fullcalendar/daygrid/index.js");
/* harmony import */ var _useGetDataset__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./useGetDataset */ "./templates/component/calendar/js/useGetDataset.js");



const calendarEl = document.getElementById('calendar');
const calendarData = (0,_useGetDataset__WEBPACK_IMPORTED_MODULE_0__.useGetDataset)(calendarEl, {
  events: []
});
const calendar = new _fullcalendar_core__WEBPACK_IMPORTED_MODULE_1__.Calendar(calendarEl, {
  plugins: [_fullcalendar_daygrid__WEBPACK_IMPORTED_MODULE_2__["default"]],
  navLinks: false,
  events: calendarData.events,
  eventDisplay: 'block',
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    meridiem: false
  },
  displayEventEnd: true,
  initialView: 'dayGridMonth',
  locale: 'cs',
  headerToolbar: {
    left: 'title',
    center: '',
    right: 'prev,next'
  },
  eventColor: '#ffcc02',
  eventTextColor: '#000'
});
calendar.render();

/***/ }),

/***/ "./templates/component/calendar/js/useGetDataset.js":
/*!**********************************************************!*\
  !*** ./templates/component/calendar/js/useGetDataset.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useGetDataset: () => (/* binding */ useGetDataset)
/* harmony export */ });
const useGetDataset = (element, dataContainer) => {
  if (!element) {
    throw new Error(`useGetDataset: Element not found`);
  }
  const data = {};
  for (const key in element.dataset) {
    if (key in dataContainer) {
      const value = element.dataset[key];
      let parsedValue = value;

      // Try to parse value as JSON if possible
      try {
        parsedValue = JSON.parse(value);
      } catch (e) {}

      // Check if the value type matches the dataContainer
      if (typeof dataContainer[key] === typeof parsedValue) {
        data[key] = parsedValue;
      } else {
        throw new Error(`useGetDataset: Invalid data type for key ${key}`);
      }
    }
  }

  // Check if any keys are missing from the dataset
  for (const key in dataContainer) {
    if (!(key in data)) {
      throw new Error(`useGetDataset: Key ${key} not found in dataset`);
    }
  }
  return data;
};

/***/ })

}]);
//# sourceMappingURL=templates_component_calendar_js_calendar_js.df490c12f11756d9273a.js.map