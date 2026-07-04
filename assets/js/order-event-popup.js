(function () {
  const modal = document.getElementById('orderEventModal');
  if (!modal) return;

  const html = document.documentElement;
  const body = document.body;
  const form = modal.querySelector('#orderEventForm');
  const formatInput = form ? form.querySelector('input[name="event_format"]') : null;
  const thanksModal = document.getElementById('formThanksModal');

  function openModal() {
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
    body.classList.add('overflow_active');
    html.classList.add('overflow_active');
  }

  function closeModal() {
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
    if (!thanksModal || !thanksModal.classList.contains('is-open')) {
      body.classList.remove('overflow_active');
      html.classList.remove('overflow_active');
    }
  }

  function openThanks() {
    if (!thanksModal) return;
    thanksModal.classList.add('is-open');
    thanksModal.setAttribute('aria-hidden', 'false');
    body.classList.add('overflow_active');
  }

  function resolveFormat(openBtn) {
    const panel = openBtn.closest('.corporate-holiday-format__panel');
    if (panel) {
      const title = panel.querySelector('.corporate-holiday-format__panel-title');
      if (title) return title.textContent.trim();
    }
    return '';
  }

  document.addEventListener('click', function (e) {
    const openBtn = e.target.closest('.js-order-event-open');
    if (openBtn) {
      e.preventDefault();
      if (formatInput) formatInput.value = resolveFormat(openBtn);
      openModal();
    }
  });

  modal.querySelectorAll('.js-order-event-close').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      closeModal();
    });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
  });

  if (window.jQuery && window.jQuery.fn && window.jQuery.fn.inputmask) {
    window.jQuery('.js-phone-mask').inputmask('+7 (999) 999-99-99');
    window.jQuery('.js-date-mask').inputmask('99.99.9999', {
      placeholder: '_',
      clearIncomplete: true,
    });
  }

  if (!form) return;

  form.querySelectorAll('input, textarea').forEach(function (input) {
    input.addEventListener('focus', function () {
      const field = this.closest('.order_event_field');
      if (field) field.classList.remove('field--error');
    });
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const name = form.querySelector('[name="name"]');
    const phone = form.querySelector('[name="phone"]');
    const email = form.querySelector('[name="email"]');
    const date = form.querySelector('[name="date"]');

    form.querySelectorAll('.field--error').forEach(function (el) {
      el.classList.remove('field--error');
    });

    const errors = [];

    if (!name.value.trim()) {
      errors.push('Введите ФИО');
      name.closest('.order_event_field').classList.add('field--error');
    }

    if (!phone.value.trim()) {
      errors.push('Введите телефон');
      phone.closest('.order_event_field').classList.add('field--error');
    }

    if (!email.value.trim()) {
      errors.push('Введите e-mail');
      email.closest('.order_event_field').classList.add('field--error');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      errors.push('Некорректный e-mail');
      email.closest('.order_event_field').classList.add('field--error');
    }

    const dateValue = date.value.trim();
    const dateMatch = dateValue.match(/^(\d{2})\.(\d{2})\.(\d{4})$/);
    let parsedDate = null;

    if (dateMatch) {
      const d = parseInt(dateMatch[1], 10);
      const mo = parseInt(dateMatch[2], 10);
      const y = parseInt(dateMatch[3], 10);
      const dt = new Date(y, mo - 1, d);
      if (dt.getFullYear() === y && dt.getMonth() === mo - 1 && dt.getDate() === d && y >= 1900) {
        parsedDate = dt;
      }
    }

    if (!parsedDate) {
      errors.push('Укажите корректную дату');
      date.closest('.order_event_field').classList.add('field--error');
    } else {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      if (parsedDate < today) {
        errors.push('Дата уже прошла');
        date.closest('.order_event_field').classList.add('field--error');
      }
    }

    if (errors.length) {
      if (typeof window.showNotify === 'function') {
        window.showNotify(errors.join('\n'), 'error');
      }
      return;
    }

    const submitBtn = form.querySelector('.order_event_submit');
    if (submitBtn) {
      submitBtn.style.pointerEvents = 'none';
      submitBtn.style.opacity = '0.5';
    }

    const fd = new FormData(form);
    fd.append('action', 'order_event_send');
    fd.append('nonce', (window.orderEventAjax && window.orderEventAjax.nonce) || '');

    const url = (window.orderEventAjax && window.orderEventAjax.ajax_url) || '';
    if (!url) return;

    fetch(url, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data && data.success) {
          form.reset();
          closeModal();
          openThanks();
        } else {
          if (typeof window.showNotify === 'function') {
            window.showNotify((data && data.data && data.data.message) || 'Ошибка отправки', 'error');
          }
        }
      })
      .catch(function () {
        if (typeof window.showNotify === 'function') {
          window.showNotify('Ошибка соединения', 'error');
        }
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.style.pointerEvents = '';
          submitBtn.style.opacity = '';
        }
      });
  });

})();
