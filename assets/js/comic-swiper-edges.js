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
});
