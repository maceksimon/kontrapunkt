import "lightgallery";
import lightGallery from "lightgallery";

const elArray = document.getElementsByClassName("js-gallery");

for (let i = 0; i < elArray.length; i++) {
  var gallery = lightGallery(elArray[i], {
    selector: ".js-lightbox",
  });
}
