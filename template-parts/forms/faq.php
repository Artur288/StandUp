<?php
$args      = $args ?? [];
$form_type = (string) ($args['form_type'] ?? 'perform');
$form_id   = 'faq-form-' . $form_type;
?>
<form class="about_faq__form" data-form-type="<?php echo esc_attr($form_type); ?>" novalidate>
	<input type="hidden" name="form_type" value="<?php echo esc_attr($form_type); ?>">
	<div class="about_faq__field">
		<label for="<?php echo esc_attr($form_id); ?>-name">ФИО</label>
		<input type="text" id="<?php echo esc_attr($form_id); ?>-name" name="name" placeholder="ФИО" autocomplete="name">
	</div>
	<div class="about_faq__field-row">
		<div class="about_faq__field">
			<label for="<?php echo esc_attr($form_id); ?>-phone">Телефон</label>
			<input type="tel" class="js-phone-mask" id="<?php echo esc_attr($form_id); ?>-phone" name="phone" placeholder="+7 (999) 000–00–00" autocomplete="tel">
		</div>
		<div class="about_faq__field">
			<label for="<?php echo esc_attr($form_id); ?>-email">E-mail</label>
			<input type="email" id="<?php echo esc_attr($form_id); ?>-email" name="email" placeholder="gmail@gmail.com" autocomplete="email">
		</div>
	</div>
	<div class="about_faq__field">
		<label for="<?php echo esc_attr($form_id); ?>-message">Сообщение</label>
		<textarea id="<?php echo esc_attr($form_id); ?>-message" name="message" rows="5" placeholder="Напишите ваше сообщение..."></textarea>
	</div>
	<div class="btn"><span>Отправить
		<svg xmlns="http://www.w3.org/2000/svg" width="10" height="20" viewBox="0 0 10 20" fill="none">
			<path d="M1.17188 18.5723L8.17187 9.75227L1.17187 0.932266" stroke="#EEEDDE" stroke-width="3" stroke-linejoin="round"/>
		</svg>
	</span></div>
</form>
