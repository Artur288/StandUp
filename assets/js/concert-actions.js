document.addEventListener('click', function (e) {
  const buyBtn = e.target.closest('.buy_tickerts');
  if (buyBtn) {
    if (buyBtn.querySelector('a')) return;
    const controls = buyBtn.closest('.controls');
    if (controls) controls.classList.add('active');
    return;
  }
  const hideBtn = e.target.closest('.buy_tickerts_hide');
  if (hideBtn) {
    const controls = hideBtn.closest('.controls');
    if (controls) controls.classList.remove('active');
  }
});
