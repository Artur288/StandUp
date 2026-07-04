<?php

add_action('wp_enqueue_scripts', function () {
	$ver = wp_get_theme()->get('Version');

	wp_enqueue_style('standup-reset', get_theme_file_uri('assets/css/reset.css'), [], $ver);
	wp_enqueue_style('standup-swiper', get_theme_file_uri('assets/css/swiper.min.css'), [], $ver);
	wp_enqueue_style('standup-style', get_theme_file_uri('assets/css/style.css'), ['standup-reset', 'standup-swiper'], $ver);
	wp_enqueue_style('standup-mobile', get_theme_file_uri('assets/css/mobile.css'), ['standup-style'], $ver);
	wp_enqueue_style('standup-fancybox', get_theme_file_uri('assets/css/fancybox.min.css'), [], $ver);
	wp_enqueue_style('standup-theme', get_theme_file_uri('assets/css/theme.css'), ['standup-style'], $ver);

	wp_enqueue_script('standup-swiper', get_theme_file_uri('assets/js/swiper.min.js'), [], $ver, true);
	wp_enqueue_script('standup-inputmask', get_theme_file_uri('assets/js/jquery.inputmask.min.js'), ['jquery'], $ver, true);
	wp_enqueue_script('standup-fancybox', get_theme_file_uri('assets/js/fancybox.min.js'), ['jquery'], $ver, true);
	wp_enqueue_script('standup-main', get_theme_file_uri('assets/js/script.js'), ['jquery', 'standup-swiper', 'standup-inputmask'], $ver, true);
	wp_enqueue_script('standup-concert', get_theme_file_uri('assets/js/concert-js.js'), ['jquery', 'standup-main'], $ver, true);
	wp_enqueue_script('standup-concert-actions', get_theme_file_uri('assets/js/concert-actions.js'), ['standup-main'], $ver, true);
	wp_enqueue_script('standup-order-event', get_theme_file_uri('assets/js/order-event-popup.js'), ['jquery', 'standup-main'], $ver, true);
	wp_enqueue_script('standup-faq-form', get_theme_file_uri('assets/js/form.js'), ['standup-main'], $ver, true);
	wp_enqueue_script('standup-fancybox-init', get_theme_file_uri('assets/js/fancybox-init.js'), ['standup-fancybox'], $ver, true);
	wp_enqueue_script('standup-city-filter', get_theme_file_uri('assets/js/city-filter.js'), [], $ver, true);
	wp_enqueue_script('standup-readmore-check', get_theme_file_uri('assets/js/readmore-check.js'), [], $ver, true);
	wp_enqueue_script('standup-schedule-filter', get_theme_file_uri('assets/js/schedule-filter.js'), [], $ver, true);
	wp_enqueue_script('standup-swiper-edges', get_theme_file_uri('assets/js/swiper-edges.js'), ['standup-main'], $ver, true);
	wp_enqueue_script('standup-stage-info', get_theme_file_uri('assets/js/stage-info-popup.js'), ['standup-main', 'standup-swiper'], $ver, true);

	// постранично подключаем CSS и JS из мокапа
	if (is_page_template('page-about.php') || is_singular( 'event' )) {
		wp_enqueue_style('standup-about', get_theme_file_uri('assets/css/about.css'), ['standup-style'], $ver);
	} elseif (is_page_template('page-certificates.php')) {
		wp_enqueue_style('standup-cert', get_theme_file_uri('assets/css/cert.css'), ['standup-style'], $ver);
	} elseif (is_page_template('page-cooperation.php')) {
		wp_enqueue_style('standup-cooperation', get_theme_file_uri('assets/css/cooperation.css'), ['standup-style'], $ver);
	} elseif (is_page_template('page-corporate.php')) {
		wp_enqueue_style('standup-corporate', get_theme_file_uri('assets/css/corporate.css'), ['standup-style'], $ver);
	}

	// about.js — табы и swiper, нужен на about + cooperation (та же разметка .about_description)
	if (is_page_template('page-about.php') || is_page_template('page-cooperation.php') || is_singular( 'event' )) {
		wp_enqueue_script('standup-about', get_theme_file_uri('assets/js/about.js'), ['jquery', 'standup-main', 'standup-swiper'], $ver, true);
	}

	$ajax = [
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce'    => wp_create_nonce('standup_form'),
	];
	wp_localize_script('standup-main', 'concertAjax', $ajax);
	wp_localize_script('standup-main', 'orderEventAjax', $ajax);
	wp_localize_script('standup-main', 'aboutAjax', $ajax);
});
