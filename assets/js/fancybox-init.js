document.addEventListener('DOMContentLoaded', function () {
  if (!window.jQuery || !window.jQuery.fn.fancybox) return;
  window.jQuery('[data-fancybox]').fancybox({
    buttons: ['zoom', 'close'],
    loop: true,
    transitionEffect: 'fade',
    animationEffect: 'zoom',
  });
});
