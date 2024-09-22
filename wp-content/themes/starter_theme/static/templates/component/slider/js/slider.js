import Swiper from 'swiper'
import { Navigation } from 'swiper/modules';

new Swiper('.component-slider .swiper-container', {
  modules: [ Navigation ],
  slidesPerView: 3,
  spaceBetween: 20,
  watchOverflow: true,
  navigation: {
    nextEl: ".component-slider .custom-button-next",
    prevEl: ".component-slider .custom-button-prev",
  },
  breakpoints: {
    250: {
      slidesPerView: 1,
      spaceBetween: 24,
    },
    768: {
      slidesPerView: 2,
      spaceBetween: 24,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 24,
    },
  },
});
