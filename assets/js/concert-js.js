document.addEventListener('DOMContentLoaded', () => {

  const modal = document.getElementById('concertModal');
  if (modal) {
      const modalContent = modal.querySelector('.concert_modal__content');
  const body = document.querySelector('body');
  const html = document.querySelector('html');
  const fallbackContent = modalContent.innerHTML;


  document.addEventListener('click', function (e) {

    const infoBtn = e.target.closest('.info_btn');
    if (!infoBtn) return;

    const eventId = infoBtn.dataset.eventId;

    modal.classList.add('active');
    body.classList.add('overflow_active');
    // html.classList.add('overflow_active');

    if (typeof concertAjax === 'undefined' || concertAjax.ajax_url === '#') {
      modalContent.innerHTML = fallbackContent;
      initEventComicsSwiper();
      toggleDescription();
      return;
    }

    modalContent.innerHTML = '<div class="loader">Загрузка…</div>';

    fetch(concertAjax.ajax_url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'get_event_modal',
        event_id: eventId,
        nonce: concertAjax.nonce
      })
    })
      .then(res => res.text())
      .then(html => {
        modalContent.innerHTML = html;

        initEventComicsSwiper();
        toggleDescription();

      });

  });

  document.addEventListener('click', function (e) {
    if (
      e.target.closest('.concert_modal__close') ||
      e.target.classList.contains('concert_modal__overlay')
    ) {
      modal.classList.remove('active');
      body.classList.remove('overflow_active');
      // html.classList.remove('overflow_active');

    }
  });

  function initEventComicsSwiper() {
    const slider = document.querySelector('.event_comics_list');

    if (!slider || typeof Swiper === 'undefined') return;

    if (slider.swiper) {
      slider.swiper.destroy(true, true);
    }

    new Swiper(slider, {
      loop: true,
      slidesPerView: 'auto',
      spaceBetween: 20,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      },
    });

    const gallerySlider = document.querySelector('.gallery_list.swiper');

    if (gallerySlider && typeof Swiper !== 'undefined') {

      if (gallerySlider.swiper) {
        gallerySlider.swiper.destroy(true, true);
      }

      new Swiper(gallerySlider, {
        loop: true,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        slidesPerView: 1,
        spaceBetween: 16,
        breakpoints: {
          1200: { slidesPerView: 3 },
          425: { slidesPerView: 2 },
          0: { slidesPerView: 1 },
        },
      });
    }
  }

  function toggleDescription() {

    const btn_readmore = modal.querySelector('.btn_readmore');
    btn_readmore.addEventListener('click', function () {

      btn_readmore.classList.toggle('active');
      const description_event = modal.querySelector('.description_event');
      description_event.classList.toggle('active');

    })
  }
   }

});
