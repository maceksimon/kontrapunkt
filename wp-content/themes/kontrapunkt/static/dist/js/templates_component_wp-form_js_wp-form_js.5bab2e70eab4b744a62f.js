(globalThis["webpackChunkstatic_base"] = globalThis["webpackChunkstatic_base"] || []).push([["templates_component_wp-form_js_wp-form_js"],{

/***/ "./templates/component/wp-form/js/wp-form.js":
/*!***************************************************!*\
  !*** ./templates/component/wp-form/js/wp-form.js ***!
  \***************************************************/
/***/ (() => {

var inputs = document.querySelectorAll('.wpcf7-form-control');
function updateLabel(event) {
  var input = event.target;
  var label = input.parentNode.parentNode.querySelector('.form-label');
  if (input.value || input === document.activeElement) {
    label.classList.add('active');
  } else {
    label.classList.remove('active');
  }
}
inputs.forEach(function (input) {
  input.addEventListener('input', updateLabel);
  input.addEventListener('focus', updateLabel);
  input.addEventListener('blur', updateLabel);
});

/***/ })

}]);
//# sourceMappingURL=templates_component_wp-form_js_wp-form_js.5bab2e70eab4b744a62f.js.map