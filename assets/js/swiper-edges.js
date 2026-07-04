document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.swiper-edge').forEach(function (edge) {
    const parent = edge.parentElement;
    if (!parent || parent._edgeListenerAttached) return;
    parent._edgeListenerAttached = true;

    const EDGE_WIDTH = parseInt(getComputedStyle(edge).width) || 300;

    parent.addEventListener('mousemove', function (e) {
      const rect = parent.getBoundingClientRect();
      const x = e.clientX - rect.left;
      parent.querySelectorAll('.swiper-edge').forEach(function (el) {
        const w = parseInt(getComputedStyle(el).width) || 300;
        const isLeft  = el.classList.contains('swiper-edge--left');
        const isRight = el.classList.contains('swiper-edge--right');
        if (isLeft)  el.classList.toggle('is-faded', x < w);
        if (isRight) el.classList.toggle('is-faded', x > rect.width - w);
      });
    });

    parent.addEventListener('mouseleave', function () {
      parent.querySelectorAll('.swiper-edge').forEach(function (el) {
        el.classList.remove('is-faded');
      });
    });
  });

  // скрываем edges, если слайдов меньше/равно слотов
  function realSlides(inst) {
    return Array.from(inst.slides).filter(function (s) {
      return !s.classList.contains('swiper-slide-duplicate');
    });
  }

  function updateEdges(swiperEl) {
    const inst = swiperEl.swiper;
    if (!inst) return;
    const parent = swiperEl.parentElement;
    const edges = [].concat(
      Array.from(swiperEl.querySelectorAll(':scope > .swiper-edge')),
      parent ? Array.from(parent.querySelectorAll(':scope > .swiper-edge')) : []
    );
    if (!edges.length) return;

    function recalc() {
      const slides = realSlides(inst);
      const perView = inst.params.slidesPerView;
      let hide;
      if (perView === 'auto') {
        const space = inst.params.spaceBetween || 0;
        const total = slides.reduce(function (s, el) { return s + el.offsetWidth; }, 0)
          + Math.max(0, slides.length - 1) * space;
        hide = total <= swiperEl.offsetWidth + 1;
      } else {
        hide = slides.length <= Math.ceil(perView);
      }
      edges.forEach(function (e) { e.classList.toggle('is-hidden', hide); });
    }

    recalc();
    inst.on('breakpoint', recalc);
    inst.on('slidesLengthChange', recalc);
    inst.on('resize', recalc);
  }

  requestAnimationFrame(function () {
    document.querySelectorAll('.swiper').forEach(updateEdges);
  });
});
