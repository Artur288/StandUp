document.addEventListener('DOMContentLoaded', () => {
  const body = document.querySelector('body');
  const header = document.querySelector('header');
  const burger = header.querySelector('.burger');
  const mobile_menu = document.querySelector('.mobile_menu');
  const close = mobile_menu.querySelector('.close_mobile_menu');

  const parents = mobile_menu.querySelectorAll('.menu-item-has-children');
  parents.forEach(element => {
    const parent_link = element.querySelector(':scope > a');
    parent_link.addEventListener('click', function (e) {
      if (!e.target.closest('.menu-arrow')) return;
      e.preventDefault();
      element.classList.toggle('is-open');
    });
  });

  // Маска для телефона
  jQuery("input[type='tel']").inputmask("+7 (999) 999-99-99", {
      showMaskOnFocus: !0,
      showMaskOnHover: !1,
      onBeforeMask: function(value) {
          if (value && value.charAt(0) === '8') {
              return value.substring(1);
          }
          return value;
      }
  });

  function openMenu() {
    mobile_menu.classList.add('active');
    body.classList.add('overflow_active');
    document.documentElement.classList.add('overflow_active');
  }

  function closeMenu() {
    mobile_menu.classList.remove('active');
    body.classList.remove('overflow_active');
    document.documentElement.classList.remove('overflow_active');

    mobile_menu.querySelectorAll('.menu-item-has-children.is-open')
      .forEach(item => item.classList.remove('is-open'));
  }

  burger.addEventListener('click', openMenu);
  close.addEventListener('click', closeMenu);

  document.addEventListener('click', (e) => {
    if (mobile_menu.classList.contains('active') && !e.target.closest('.mobile_menu') && !e.target.closest('.burger')) {
      closeMenu();
    }
  });



  const slider = document.querySelector('.homeSwiper');

  if (slider) {
    const swiper = new Swiper(slider, {
      loop: true,
      speed: 1000,

      slidesPerView: 1,
      spaceBetween: 0,

      // navigation: {
      //   nextEl: '.slider-btn.next',
      //   prevEl: '.slider-btn.prev',
      // },

      // pagination: {
      //   el: '.slider-dots',
      //   clickable: true,
      //   bulletClass: 'slider-dot',
      //   bulletActiveClass: 'is-active',
      // },

      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },

      effect: 'fade',

      on: {
        init() {
          slider.classList.add('is-ready');
        }
      }
    });
  }

  document.querySelectorAll('.formats_icons_swiper').forEach(function (el) {
    new Swiper(el, {
      slidesPerView: 'auto',
      spaceBetween: 16,
      grabCursor: true,
    });
  });

  document.querySelectorAll('.stages_rowlist_swiper').forEach(function (el) {
    new Swiper(el, {
      slidesPerView: 'auto',
      spaceBetween: 30,
      breakpoints: {
        0:   { spaceBetween: 14 },
        768: { spaceBetween: 30 },
      },
    });
  });

  document.querySelectorAll('.comic_swiper').forEach(function (el) {
    new Swiper(el, {
      loop: true,
      spaceBetween: 20,
      slidesPerView: 5,
      navigation: {
        nextEl: el.querySelector('.swiper-button-next'),
        prevEl: el.querySelector('.swiper-button-prev'),
      },
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },

      breakpoints: {
        1400: {
          slidesPerView: 5,
          autoplay: false,
          centeredSlides: false,
          navigation: {
            nextEl: el.querySelector('.swiper-button-next'),
            prevEl: el.querySelector('.swiper-button-prev'),
          },
        },
        1200: {
          slidesPerView: 4,
          autoplay: false,
          centeredSlides: false,
          navigation: {
            nextEl: el.querySelector('.swiper-button-next'),
            prevEl: el.querySelector('.swiper-button-prev'),
          },
        },
        800: {
          slidesPerView: 3,
          centeredSlides: true,
          watchSlidesProgress: true,
          autoplay: {
            delay: 5000,
            disableOnInteraction: false,
          },
          navigation: false,
        },
        425: {
          slidesPerView: 3,
          centeredSlides: true,
          watchSlidesProgress: true,
          autoplay: {
            delay: 5000,
            disableOnInteraction: false,
          },
          navigation: false,
        },
        0: {
          slidesPerView: 3,
          centeredSlides: true,
          watchSlidesProgress: true,
          autoplay: {
            delay: 5000,
            disableOnInteraction: false,
          },
          navigation: false,
        },
      },
    });
  });

  document.querySelectorAll('.video_swiper').forEach(function (el) {
    new Swiper(el, {
      slidesPerView: 2.5,
      spaceBetween: 20,
      loop: true,
      navigation: {
        nextEl: el.querySelector('.swiper-button-next'),
      },
      breakpoints: {
        768: {
          slidesPerView: 2.5,
        },
        480: {
          slidesPerView: 1.5,
        },
        320: {
          slidesPerView: 1.2,
        }
      }
    });
  });

  document.querySelectorAll('.statistics_swiper').forEach(function (el) {
    new Swiper(el, {
      spaceBetween: 20,
      loop: true,
      slidesPerView: 1,
      allowTouchMove: false,
      autoplay: false,
      breakpoints: {

        1200: {
          slidesPerView: 5,
          loop: true,
          allowTouchMove: false,
        },
        1024: {
          slidesPerView: 4.5,
          loop: true,
          allowTouchMove: true,
          spaceBetween: 20,
        },

        0: {
          slidesPerView: 3.3,
          loop: true,
          allowTouchMove: true,
          spaceBetween: 5,

        }
      }
    });
  });


  const categorySwiper = document.querySelector('.category_swiper');

  if (categorySwiper) {

    new Swiper(categorySwiper, {
      slidesPerView: 4,
      spaceBetween: 20,
      loop: false,
      navigation: {
        nextEl: categorySwiper.querySelector('.swiper-button-next'),
        prevEl: categorySwiper.querySelector('.swiper-button-prev'),
      },
      breakpoints: {
        1200: { slidesPerView: 4 },
        1024: { slidesPerView: 3.5 },
        // 768: { slidesPerView: 2 },
        430: { slidesPerView: 2.5 },
        320: { slidesPerView: 1.5 },
      }
    });
  }

  const concert_item = document.querySelectorAll('.concert_item');
  if (concert_item) {
    concert_item.forEach(element => {
      const buy_tickerts = element.querySelector('.buy_tickerts');
      const buy_tickerts_hide = element.querySelector('.buy_tickerts_hide');
      const controls = element.querySelector('.controls');
      buy_tickerts.addEventListener('click', function () {

      })
      buy_tickerts_hide.addEventListener('click', function () {
        controls.classList.remove('active');
      })
    });
  }
  const comic_list_current_month = document.querySelector('.swiper-comic_list_current_month');

  if (comic_list_current_month) {

    const comicSwiper = new Swiper(comic_list_current_month, {

      slidesPerView: 'auto',
      spaceBetween: 5,
      speed: 600,

      grabCursor: true,

      navigation: {
        nextEl: comic_list_current_month.querySelector('.swiper-button-next'),
        prevEl: comic_list_current_month.querySelector('.swiper-button-prev'),
      },

      pagination: {
        el: comic_list_current_month.querySelector('.swiper-pagination'),
        clickable: true,
      },

    });

    const fadeLeft  = comic_list_current_month.querySelector('.comic-fade--left');
    const fadeRight = comic_list_current_month.querySelector('.comic-fade--right');

    if (fadeLeft && fadeRight) {
      // показ плашек по позиции свайпера: левая — если уехали с начала, правая — если не в конце
      const syncFades = function () {
        fadeLeft.classList.toggle('is-active', !comicSwiper.isBeginning);
        fadeRight.classList.toggle('is-active', !comicSwiper.isEnd);
      };
      comicSwiper.on('progress', syncFades); // ловит и свайп, и resize
      syncFades();

      // увод плашки при подведении курсора к краю (плашки pointer-events:none, поэтому по mousemove)
      comic_list_current_month.addEventListener('mousemove', function (e) {
        const rect = comic_list_current_month.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const w = fadeRight.offsetWidth || 100;
        fadeLeft.classList.toggle('is-faded', x < w);
        fadeRight.classList.toggle('is-faded', x > rect.width - w);
      });
      comic_list_current_month.addEventListener('mouseleave', function () {
        fadeLeft.classList.remove('is-faded');
        fadeRight.classList.remove('is-faded');
      });
    }

  }
  
  const list_hedliter = document.querySelector('.swiper-list_hedliter');
  if (list_hedliter) {

    new Swiper(list_hedliter, {

      slidesPerView: 'auto',
      spaceBetween: 5,
      speed: 600,

      grabCursor: true,

      navigation: {
        nextEl: list_hedliter.querySelector('.swiper-button-next'),
        prevEl: list_hedliter.querySelector('.swiper-button-prev'),
      },

      pagination: {
        el: list_hedliter.querySelector('.swiper-pagination'),
        clickable: true,
      },

    });

  }

  //скрытый текст
  document.querySelectorAll('.btn_readmore.event-format__link').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const wrap = this.closest('.event-format__left') || this.parentElement;
      const text = wrap ? wrap.querySelector('.event-format__text') : null;
      if (!text) return;
      const openText = this.querySelector('.open');
      const closeText = this.querySelector('.close');

      text.classList.toggle('active');
      if (openText) openText.classList.toggle('active');
      if (closeText) closeText.classList.toggle('active');
    });
  });

  const holidayFormat = document.querySelector('.corporate-holiday-format');
  if (holidayFormat) {
    const tabButtons = holidayFormat.querySelectorAll('[data-format-tab]');
    const panels = holidayFormat.querySelectorAll('[data-format-panel]');

    tabButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const id = btn.getAttribute('data-format-tab');
        if (!id) return;

        tabButtons.forEach(function (b) {
          const on = b === btn;
          b.classList.toggle('is-active', on);
          b.setAttribute('aria-selected', on ? 'true' : 'false');
        });

        panels.forEach(function (p) {
          const on = p.getAttribute('data-format-panel') === id;
          p.classList.toggle('is-active', on);
        });
      });
    });
  }

});

/* === Глобальные уведомления === */
window.showNotify = function (text, type) {
  var notify = document.createElement('div');
  notify.className = 'notify notify--' + type;
  notify.innerHTML = text.replace(/\n/g, '<br>');
  document.body.appendChild(notify);

  setTimeout(function () { notify.classList.add('active'); }, 10);
  setTimeout(function () {
    notify.classList.remove('active');
    setTimeout(function () { notify.remove(); }, 300);
  }, 3000);
};
