(function () {
  function check() {
    document.querySelectorAll('.event-format__text').forEach(function (text) {
      const wrap = text.closest('.event-format__left') || text.closest('.event-format__content') || text.parentElement;
      if (!wrap) return;
      const link = wrap.querySelector('.btn_readmore.event-format__link');
      if (!link) return;
      // если уже раскрыт пользователем — кнопка нужна для скрытия
      if (text.classList.contains('active')) {
        link.style.display = '';
        return;
      }
      const overflows = text.scrollHeight > text.clientHeight + 1;
      link.style.display = overflows ? '' : 'none';
    });
  }

  let t;
  function debounced() {
    clearTimeout(t);
    t = setTimeout(check, 100);
  }

  if (document.readyState !== 'loading') {
    check();
  } else {
    document.addEventListener('DOMContentLoaded', check);
  }
  window.addEventListener('load', check);
  window.addEventListener('resize', debounced);
})();
