<?php
$event_id = (int) ($args['event_id'] ?? 0);
if (!$event_id) return;

$format_terms   = get_the_terms($event_id, 'format');
$format_term_id = (!empty($format_terms) && !is_wp_error($format_terms)) ? (int) $format_terms[0]->term_id : 0;

$header_line = standup_free_event_header($event_id, $format_term_id);
$important   = $format_term_id ? (string) get_field('free_event_important_info', 'format_' . $format_term_id) : '';

$title = get_the_title($event_id);
?>
<div class="stage_info_modal" id="freeEventBookingModal-<?php echo (int) $event_id; ?>" aria-hidden="true">
	<div class="stage_info_modal__overlay js-stage-info-close" tabindex="-1" aria-label="Закрыть"></div>
	<div class="stage_info_modal__content" role="dialog" aria-modal="true" aria-label="Бронирование мест">
		<button type="button" class="stage_info_modal__close js-stage-info-close" aria-label="Закрыть">
			<svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none">
				<path d="M9.49027 11.2812L32.7614 29.5541" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
				<path d="M8.77689 29.5488L32.048 11.276" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
			</svg>
		</button>
		<div class="stage_info_modal__header">
			<?php if ($header_line !== ''): ?>
				<div class="stage_info_modal__date"><?php echo esc_html($header_line); ?></div>
			<?php endif; ?>
		</div>

		<?php if ($important !== ''): ?>
			<div class="free_event_booking_modal__form-title">Важная информация:</div>
			<div class="free_event_booking_modal__important"><?php echo wp_kses_post($important); ?></div>
		<?php endif; ?>

		<div class="free_event_booking_modal__form-title --last">Бронирование мест:</div>
		<p class="free_event_booking_modal__important">После заполнения формы Вы получите подтверждение на почту. В день события с Вами свяжутся, чтобы напомнить о мероприятии и получить подтверждение.</p>

		<form class="about_faq__form free_event_booking_form" data-form-type="free_event" novalidate>
			<input type="hidden" name="form_type" value="free_event">
			<input type="hidden" name="event_id" value="<?php echo (int) $event_id; ?>">
			<div class="about_faq__field-row">
				<div class="about_faq__field">
					<label for="free-event-booking-<?php echo (int) $event_id; ?>-name">ФИО</label>
					<input type="text" id="free-event-booking-<?php echo (int) $event_id; ?>-name" name="name" placeholder="ФИО" autocomplete="name">
				</div>
				<div class="about_faq__field">
					<label for="free-event-booking-<?php echo (int) $event_id; ?>-email">E-mail</label>
					<input type="email" id="free-event-booking-<?php echo (int) $event_id; ?>-email" name="email" placeholder="gmail@gmail.com" autocomplete="email">
				</div>
			</div>
			<div class="about_faq__field-row">
				<div class="about_faq__field">
					<label for="free-event-booking-<?php echo (int) $event_id; ?>-phone">Телефон</label>
					<input type="tel" class="js-phone-mask" id="free-event-booking-<?php echo (int) $event_id; ?>-phone" name="phone" placeholder="+7 (999) 000–00–00" autocomplete="tel">
				</div>
				<div class="about_faq__field">
					<label for="free-event-booking-<?php echo (int) $event_id; ?>-guests">Количество гостей</label>
					<input type="number" min="1" id="free-event-booking-<?php echo (int) $event_id; ?>-guests" name="guests" placeholder="0">
				</div>
			</div>
			<label class="free_event_booking_form__consent">
				<input type="checkbox" name="consent" required>
				<span>Отправляя данную форму, вы даете согласие на <a href="<?php echo esc_url(get_privacy_policy_url() ?: '#'); ?>" target="_blank">обработку персональных данных</a></span>
			</label>
			<div class="btn free_event_booking_form__btn"><span>Бронировать</span></div>
		</form>
	</div>
</div>
