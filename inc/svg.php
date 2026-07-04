<?php

// разрешаем загрузку SVG только админам — в теме санитайзинга нет, ставить Safe SVG плагином при необходимости
add_filter('upload_mimes', function ($mimes) {
	if (!current_user_can('manage_options')) {
		return $mimes;
	}
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
});

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
	if (!empty($data['type'])) {
		return $data;
	}
	if (!current_user_can('manage_options')) {
		return $data;
	}
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	if ($ext !== 'svg' && $ext !== 'svgz') {
		return $data;
	}
	return [
		'ext'             => $ext,
		'type'            => 'image/svg+xml',
		'proper_filename' => $filename,
	];
}, 10, 4);

// показываем сам SVG в превью медиатеки и ACF-image полях
add_filter('wp_prepare_attachment_for_js', function ($response, $attachment) {
	if (($response['mime'] ?? '') !== 'image/svg+xml') {
		return $response;
	}
	$url = wp_get_attachment_url($attachment->ID);
	if (!$url) {
		return $response;
	}
	$response['sizes'] = [
		'thumbnail' => ['url' => $url, 'orientation' => 'landscape'],
		'medium'    => ['url' => $url, 'orientation' => 'landscape'],
		'large'     => ['url' => $url, 'orientation' => 'landscape'],
		'full'      => ['url' => $url, 'orientation' => 'landscape'],
	];
	$response['icon'] = $url;
	return $response;
}, 10, 2);

add_action('admin_head', function () {
	echo '<style>td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail, .media-modal img[src$=".svg"] { width: 100% !important; height: auto !important; }</style>';
});
