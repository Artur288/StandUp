<?php

add_action('init', function () {
	register_taxonomy('format', ['event'], [
		'labels' => [
			'name'          => 'Форматы',
			'singular_name' => 'Формат',
			'all_items'     => 'Все форматы',
			'edit_item'     => 'Редактировать формат',
			'view_item'     => 'Посмотреть формат',
			'update_item'   => 'Обновить формат',
			'add_new_item'  => 'Добавить формат',
			'new_item_name' => 'Название формата',
			'search_items'  => 'Найти формат',
			'menu_name'     => 'Форматы',
		],
		'public'            => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => ['slug' => 'format', 'with_front' => false],
	]);
});

// убираем /format/ из публичных ссылок термов — get_term_link() возвращает сразу /{slug}/
add_filter('term_link', function ($url, $term, $taxonomy) {
	if ($taxonomy !== 'format') {
		return $url;
	}
	return home_url('/' . $term->slug . '/');
}, 10, 3);

// маршрутизация: если запрашивается корневой URL и слаг совпадает с термом формата —
// переписываем запрос так, чтобы WP отдал term-архив, а не искал страницу
add_filter('request', function ($query_vars) {
	$slug = $query_vars['pagename'] ?? $query_vars['name'] ?? '';
	if ($slug === '') {
		return $query_vars;
	}
	$term = get_term_by('slug', $slug, 'format');
	if ($term && !is_wp_error($term)) {
		return ['format' => $slug];
	}
	return $query_vars;
});

// 301-редирект со старого /format/{slug}/ на новый /{slug}/
add_action('template_redirect', function () {
	if (!is_tax('format')) {
		return;
	}
	global $wp;
	if (strpos($wp->request, 'format/') === 0) {
		$term = get_queried_object();
		if ($term) {
			wp_safe_redirect(get_term_link($term), 301);
			exit;
		}
	}
});
