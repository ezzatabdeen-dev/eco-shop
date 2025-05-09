// Banner Slider
let bannerLider = new Swiper(".controlPannerSlider", {
  slidesPerView: 1,
  loop: true,
  autoplay: {
    delay: 5000,
    disableOnInteraction: false,
  },
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
});

// New Products Slider
const newProdutsSlider = new Swiper(".newProdutsSlider", {
  loop: true,
  autoplay: {
    delay: 5000,
    disableOnInteraction: false,
  },
  spaceBetween: 20,
  slidesPerView: 4,
  navigation: {
    nextEl: ".product-next",
    prevEl: ".product-prev",
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    576: {
      slidesPerView: 2,
    },
    768: {
      slidesPerView: 3,
    },
    1400: {
      slidesPerView: 4,
    },
  },
});

// Top Selected Slider
const topSelectedProdutsSlider = new Swiper(".topSelectedProdutsSlider", {
  loop: true,
  autoplay: {
    delay: 5000,
    disableOnInteraction: false,
  },
  spaceBetween: 20,
  slidesPerView: 4,
  navigation: {
    nextEl: ".topSelectedProduct-next",
    prevEl: ".topSelectedProduct-prev",
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    576: {
      slidesPerView: 2,
    },
    768: {
      slidesPerView: 3,
    },
    1400: {
      slidesPerView: 4,
    },
  },
});
