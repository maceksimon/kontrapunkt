import once from "@drupal/once";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

// var $component = once("processed", ".component-header-search").shift();
// if ($component) {
//   import("../../templates/component/header-search/js/header-search");
// }
var $component = once("processed", ".component-gallery").shift();
if ($component) {
  import("../../templates/component/gallery/js/gallery");
}
var $component = once("processed", ".component-slider").shift();
if ($component) {
  import("../../templates/component/slider/js/slider");
}
var $component = once("processed", ".component-wp-form").shift();
if ($component) {
  import("../../templates/component/wp-form/js/wp-form");
}

var $component = once("processed", ".component-calendar").shift();
if ($component) {
  import("../../templates/component/calendar/js/calendar");
}

// var $component = once("processed", "body").shift();
// if ($component && typeof ccnstL !== "undefined") {
//   import("../../templates/component/cookieconsent/js/cookieconsent");
// }
