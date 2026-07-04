(function () {
  const html = document.documentElement;
  const body = document.body;
  const swipers = new WeakMap();

  function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
    body.classList.add('overflow_active');
    html.classList.add('overflow_active');
    initGallery(modal);
    checkMore(modal);
  }

  function closeModal(modal) {
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
    body.classList.remove('overflow_active');
    html.classList.remove('overflow_active');
  }

  // swiper инитим только при открытии — пока модалка display:none, размеры считаются неверно
  function initGallery(modal) {
    const el = modal.querySelector('.stage-info-swiper');
    if (!el || swipers.has(el) || !window.Swiper) return;
    const nextBtn = modal.querySelector('.stage-info-swiper__next');
    const sw = new Swiper(el, {
      slidesPerView: 'auto',
      spaceBetween: 11,
      grabCursor: true,
      navigation: nextBtn ? { nextEl: nextBtn } : false,
    });
    const syncEnd = function () { el.classList.toggle('is-end', sw.isEnd); };
    sw.on('progress', syncEnd);
    syncEnd();
    swipers.set(el, sw);
  }

  function checkMore(modal) {
    const desc = modal.querySelector('.stage_info_modal__desc');
    const more = modal.querySelector('.js-stage-info-more');
    if (!desc || !more) return;
    if (desc.classList.contains('is-expanded')) { more.style.display = ''; return; }
    const overflows = desc.scrollHeight > desc.clientHeight + 1;
    more.style.display = overflows ? '' : 'none';
  }

  document.addEventListener('click', function (e) {
    const openBtn = e.target.closest('.js-stage-info-open');
    if (openBtn) {
      e.preventDefault();
      openModal(openBtn.getAttribute('data-target'));
      return;
    }

    const closeBtn = e.target.closest('.js-stage-info-close');
    if (closeBtn) {
      e.preventDefault();
      const modal = closeBtn.closest('.stage_info_modal');
      if (modal) closeModal(modal);
      return;
    }

    const moreBtn = e.target.closest('.js-stage-info-more');
    if (moreBtn) {
      e.preventDefault();
      const wrap = moreBtn.closest('.stage_info_modal__desc-wrap');
      const desc = wrap ? wrap.querySelector('.stage_info_modal__desc') : null;
      if (desc) desc.classList.toggle('is-expanded');
      moreBtn.classList.toggle('active');
      moreBtn.querySelectorAll('.open, .close').forEach(function (s) { s.classList.toggle('active'); });
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    const open = document.querySelector('.stage_info_modal.active');
    if (open) closeModal(open);
  });

  window.addEventListener('resize', function () {
    const open = document.querySelector('.stage_info_modal.active');
    if (open) checkMore(open);
  });
})();
