if (document.querySelector(".js-search")) {
  let searchToggle = document.querySelector(".js-search-toggle");
  let searchBar = document.querySelector(".js-search");
  searchToggle.addEventListener("click", function() {
    this.classList.remove("lg:inline-block");
    searchBar.classList.remove("lg:w-0");
    setTimeout(() => {
      searchBar.classList.remove("lg:overflow-hidden");
    }, 300);
    searchBar.classList.add("open");
    document.getElementById("search").focus();
  });
}
