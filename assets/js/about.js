document.addEventListener('DOMContentLoaded', function () {

  /* === Табы "О Стендап Мск" === */
  var tabs = document.querySelectorAll('.about_description__tabs li');
  var panels = document.querySelectorAll('.about_description__panel');

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var target = this.getAttribute('data-tab');

      tabs.forEach(function (t) { t.classList.remove('active'); });
      panels.forEach(function (p) { p.classList.remove('active'); });

      this.classList.add('active');
      var panel = document.querySelector('[data-tab-panel="' + target + '"]');
      if (panel) panel.classList.add('active');
    });
  });

  /* === Swiper: Наши сотрудники === */
  if (document.querySelector('.about_team_swiper')) {
    new Swiper('.about_team_swiper', {
      slidesPerView: 'auto',
      spaceBetween: 30,
      breakpoints: {
        0: { spaceBetween: 14 },
        768: { spaceBetween: 30 }
      }
    });
  }
});
