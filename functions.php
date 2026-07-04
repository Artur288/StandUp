<?php

add_action('after_setup_theme', function () {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', ['search-form', 'gallery', 'caption', 'script', 'style']);
	add_theme_support('automatic-feed-links');

	register_nav_menus([
		'primary' => 'Основное меню',
		'footer'  => 'Футер',
	]);
});

// При активации темы: подцепляемся ПОСЛЕ регистрации CPT/таксономий и сбрасываем rewrite-правила,
// чтобы /event/, /comedian/, /stage/, /format/ заработали без ручного открытия Settings → Permalinks.
add_action('after_switch_theme', function () {
	add_action('init', 'flush_rewrite_rules', 999);
});

require_once __DIR__ . '/inc/post-types/event.php';
require_once __DIR__ . '/inc/post-types/comedian.php';
require_once __DIR__ . '/inc/post-types/stage.php';
require_once __DIR__ . '/inc/post-types/form-submission.php';
require_once __DIR__ . '/inc/taxonomies/city.php';
require_once __DIR__ . '/inc/taxonomies/format.php';
require_once __DIR__ . '/inc/acf.php';
require_once __DIR__ . '/inc/enqueue.php';
require_once __DIR__ . '/inc/menu.php';
require_once __DIR__ . '/inc/queries.php';
require_once __DIR__ . '/inc/ajax.php';
require_once __DIR__ . '/inc/forms.php';
require_once __DIR__ . '/inc/svg.php';
require_once __DIR__ . '/inc/cron-events.php';
