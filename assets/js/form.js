document.addEventListener('DOMContentLoaded', function () {

  var thanksModal = document.getElementById('formThanksModal');

  function openThanks() {
    if (!thanksModal) return;
    thanksModal.classList.add('is-open');
    thanksModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow_active');
  }

  function closeThanks() {
    if (!thanksModal) return;
    thanksModal.classList.remove('is-open');
    thanksModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow_active');
  }

  if (thanksModal) {
    thanksModal.querySelectorAll('[data-form-thanks-close]').forEach(function (el) {
      el.addEventListener('click', closeThanks);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && thanksModal.classList.contains('is-open')) closeThanks();
    });
  }

  document.querySelectorAll('.about_faq__form').forEach(function (form) {
    var submitBtn = form.querySelector('button[type="submit"], .btn');
    if (!submitBtn) return;

    form.querySelectorAll('input, textarea').forEach(function (input) {
      input.addEventListener('focus', function () {
        var field = this.closest('.about_faq__field');
        if (field) field.classList.remove('field--error');
      });
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      handleSubmit();
    });

    submitBtn.addEventListener('click', function (e) {
      if (submitBtn.tagName === 'BUTTON') return;
      e.preventDefault();
      handleSubmit();
    });

    function handleSubmit() {
      var name = form.querySelector('[name="name"]');
      var phone = form.querySelector('[name="phone"]');
      var email = form.querySelector('[name="email"]');
      var message = form.querySelector('[name="message"]');
      var formTypeInput = form.querySelector('[name="form_type"]');

      form.querySelectorAll('.field--error').forEach(function (el) {
        el.classList.remove('field--error');
      });

      var errors = [];

      if (!name.value.trim()) {
        errors.push('Введите ФИО');
        name.closest('.about_faq__field').classList.add('field--error');
      }
      if (!phone.value.trim()) {
        errors.push('Введите телефон');
        phone.closest('.about_faq__field').classList.add('field--error');
      }
      if (!email.value.trim()) {
        errors.push('Введите e-mail');
        email.closest('.about_faq__field').classList.add('field--error');
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        errors.push('Некорректный e-mail');
        email.closest('.about_faq__field').classList.add('field--error');
      }

      if (errors.length) {
        if (typeof window.showNotify === 'function') {
          window.showNotify(errors.join('\n'), 'error');
        } else {
          alert(errors.join('\n'));
        }
        return;
      }

      submitBtn.style.pointerEvents = 'none';
      submitBtn.style.opacity = '0.5';

      var data = new URLSearchParams();
      data.append('action', 'about_faq_send');
      data.append('nonce', (window.aboutAjax && window.aboutAjax.nonce) || '');
      if (formTypeInput) data.append('form_type', formTypeInput.value);
      data.append('name', name.value.trim());
      data.append('phone', phone.value.trim());
      data.append('email', email.value.trim());
      data.append('message', message ? message.value.trim() : '');

      var url = (window.aboutAjax && window.aboutAjax.ajax_url) || '';
      if (!url) return;

      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: data.toString()
      })
        .then(function (res) { return res.json(); })
        .then(function (res) {
          if (res && res.success) {
            form.reset();
            openThanks();
          } else {
            var msg = (res && res.data && res.data.message) || 'Ошибка отправки';
            if (typeof window.showNotify === 'function') {
              window.showNotify(msg, 'error');
            } else {
              alert(msg);
            }
          }
        })
        .catch(function () {
          if (typeof window.showNotify === 'function') {
            window.showNotify('Ошибка соединения', 'error');
          } else {
            alert('Ошибка соединения');
          }
        })
        .finally(function () {
          submitBtn.style.pointerEvents = '';
          submitBtn.style.opacity = '';
        });
    }
  });
});
