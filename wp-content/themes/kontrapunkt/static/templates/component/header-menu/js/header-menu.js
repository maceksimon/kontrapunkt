if (document.querySelector(".js-menu-checkbox")) {
  let body = document.body;
  let menuCollapse = document.querySelector(".js-menu-checkbox");
  let menu = document.querySelector(".js-top-menu");
  if (menuCollapse) {
    menuCollapse.addEventListener("change", function() {
      if (this.checked) {
        this.parentNode.classList.add("active");
        menu.classList.add("active");
        body.classList.add("menu-open");
      } else {
        this.parentNode.classList.remove("active");
        menu.classList.remove("active");
        body.classList.remove("menu-open");
      }
    });
  }

  let toggle = document.querySelectorAll(".js-toggle");
  if (toggle) {
    toggle.forEach(function(toggleItem) {
      toggleItem.addEventListener("click", function() {
        toggleItem.parentNode.parentNode.classList.toggle("active");
      });
    });
  }
}
