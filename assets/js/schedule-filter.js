document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.concert[data-schedule]').forEach(function (section) {
    const dateContainers = section.querySelectorAll('.date_list .dates-container');
    const events = section.querySelectorAll('.concert_list > [data-city][data-date]');
    const loadMoreBtn = section.querySelector('.btn_load_more');
    const loadMoreWrap = section.querySelector('.concert_more');
    const step = parseInt(section.dataset.loadStep || '0', 10);
    let visibleLimit = step;

    function applyFilter() {
      const activeCity = section.querySelector('.cities-list__item.active')?.dataset.city || '';
      const activeDate = section.querySelector('.dates__item.active')?.dataset.date || '';

      dateContainers.forEach(c => {
        c.classList.toggle('is-hidden', c.dataset.city !== activeCity);
      });

      let shown = 0;
      let totalMatching = 0;
      events.forEach(item => {
        const matches = item.dataset.city === activeCity && item.dataset.date === activeDate;
        if (!matches) {
          item.classList.add('is-hidden');
          return;
        }
        totalMatching++;
        if (step > 0 && shown >= visibleLimit) {
          item.classList.add('is-hidden');
        } else {
          item.classList.remove('is-hidden');
          shown++;
        }
      });

      if (loadMoreWrap) {
        loadMoreWrap.style.display = (step > 0 && totalMatching > visibleLimit) ? '' : 'none';
      }
    }

    // на страницах без ссылок в табах (например, главная) клики обрабатывает JS
    section.querySelectorAll('.cities-list__item').forEach(tab => {
      if (tab.querySelector('a')) return;
      tab.addEventListener('click', () => {
        section.querySelectorAll('.cities-list__item').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const container = section.querySelector('.date_list .dates-container[data-city="' + tab.dataset.city + '"]');
        section.querySelectorAll('.dates__item').forEach(d => d.classList.remove('active'));
        container?.querySelector('.dates__item')?.classList.add('active');
        visibleLimit = step;
        applyFilter();
      });
    });

    section.querySelectorAll('.dates__item').forEach(date => {
      date.addEventListener('click', () => {
        section.querySelectorAll('.dates__item').forEach(d => d.classList.remove('active'));
        date.classList.add('active');
        visibleLimit = step;
        applyFilter();
      });
    });

    if (loadMoreBtn) {
      loadMoreBtn.addEventListener('click', () => {
        visibleLimit += step || 5;
        applyFilter();
      });
    }

    applyFilter();
  });
});
