document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.cities-list.js-city-filter').forEach(function (tabs) {
    const scope = tabs.dataset.filterScope === 'page'
      ? document
      : (tabs.closest('section') || document);

    const active = tabs.querySelector('.cities-list__item.active');
    if (!active) return;
    const citySlug = active.dataset.city || '';

    scope.querySelectorAll('[data-city]').forEach(function (item) {
      if (item.classList.contains('cities-list__item')) return;
      const itemCities = (item.dataset.city || '').split(',').map(s => s.trim()).filter(Boolean);
      const visible = itemCities.includes(citySlug);
      item.classList.toggle('is-hidden', !visible);
    });
  });
});
