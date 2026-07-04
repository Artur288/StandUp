<?php

const STANDUP_FORM_TYPES = [
	'order_event' => 'Заказать мероприятие',
	'cooperation' => 'Сотрудничество',
	'corporate'   => 'Корпоратив',
	'perform'     => 'Выступить',
];

add_action('wp_ajax_about_faq_send', 'standup_ajax_about_faq');
add_action('wp_ajax_nopriv_about_faq_send', 'standup_ajax_about_faq');
function standup_ajax_about_faq(): void {
	check_ajax_referer('standup_form', 'nonce');

	$form_type = sanitize_key($_POST['form_type'] ?? 'perform');
	if (!isset(STANDUP_FORM_TYPES[$form_type]) || $form_type === 'order_event') {
		$form_type = 'perform';
	}

	$name    = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
	$phone   = sanitize_text_field(wp_unslash($_POST['phone'] ?? ''));
	$email   = sanitize_email(wp_unslash($_POST['email'] ?? ''));
	$message = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

	if ($name === '' || ($phone === '' && $email === '')) {
		wp_send_json_error(['message' => 'Заполните обязательные поля']);
	}
	if ($email !== '' && !is_email($email)) {
		wp_send_json_error(['message' => 'Неверный e-mail']);
	}

	$post_id = standup_create_form_submission([
		'form_type' => $form_type,
		'name'      => $name,
		'phone'     => $phone,
		'email'     => $email,
		'message'   => $message,
	]);
	if (!$post_id) {
		wp_send_json_error(['message' => 'Не удалось сохранить заявку']);
	}

	standup_send_form_email($form_type, compact('name', 'phone', 'email', 'message'));

	wp_send_json_success();
}

add_action('wp_ajax_order_event_send', 'standup_ajax_order_event');
add_action('wp_ajax_nopriv_order_event_send', 'standup_ajax_order_event');
function standup_ajax_order_event(): void {
	check_ajax_referer('standup_form', 'nonce');

	$name         = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
	$phone        = sanitize_text_field(wp_unslash($_POST['phone'] ?? ''));
	$email        = sanitize_email(wp_unslash($_POST['email'] ?? ''));
	$event_date   = sanitize_text_field(wp_unslash($_POST['date'] ?? ''));
	$event_format = sanitize_text_field(wp_unslash($_POST['event_format'] ?? ''));
	$message      = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

	if ($name === '' || $phone === '' || $email === '') {
		wp_send_json_error(['message' => 'Заполните обязательные поля']);
	}
	if (!is_email($email)) {
		wp_send_json_error(['message' => 'Неверный e-mail']);
	}

	$post_id = standup_create_form_submission([
		'form_type'    => 'order_event',
		'name'         => $name,
		'phone'        => $phone,
		'email'        => $email,
		'message'      => $message,
		'event_date'   => $event_date,
		'event_format' => $event_format,
	]);
	if (!$post_id) {
		wp_send_json_error(['message' => 'Не удалось сохранить заявку']);
	}

	standup_send_form_email('order_event', compact('name', 'phone', 'email', 'message', 'event_date', 'event_format'));

	wp_send_json_success();
}

function standup_create_form_submission(array $data): int {
	$title = sprintf(
		'%s — %s — %s',
		STANDUP_FORM_TYPES[$data['form_type']] ?? $data['form_type'],
		$data['name'],
		wp_date('d.m.Y H:i')
	);
	$post_id = wp_insert_post([
		'post_type'   => 'form_submission',
		'post_status' => 'publish',
		'post_title'  => $title,
	], true);
	if (is_wp_error($post_id) || !$post_id) {
		return 0;
	}
	if (!function_exists('update_field')) {
		return (int) $post_id;
	}
	$keys = ['form_type', 'name', 'phone', 'email', 'message', 'event_date', 'event_format'];
	foreach ($keys as $key) {
		if (!empty($data[$key])) {
			update_field($key, $data[$key], $post_id);
		}
	}
	return (int) $post_id;
}

function standup_send_form_email(string $form_type, array $data): void {
	$admin_email = (string) get_field('email', 'option');
	if ($admin_email === '') {
		$admin_email = (string) get_option('admin_email');
	}
	if ($admin_email === '') {
		return;
	}

	$type_label = STANDUP_FORM_TYPES[$form_type] ?? $form_type;
	$subject    = sprintf('[%s] %s — %s', get_bloginfo('name'), $type_label, $data['name'] ?? '');

	$lines = [
		'Тип формы: ' . $type_label,
		'ФИО: ' . ($data['name'] ?? ''),
		'Телефон: ' . ($data['phone'] ?? ''),
		'E-mail: ' . ($data['email'] ?? ''),
	];
	if (!empty($data['event_date']))   $lines[] = 'Дата мероприятия: ' . $data['event_date'];
	if (!empty($data['event_format'])) $lines[] = 'Формат: ' . $data['event_format'];
	if (!empty($data['message']))      $lines[] = '' . "\nСообщение:\n" . $data['message'];

	wp_mail($admin_email, $subject, implode("\n", $lines));
}
