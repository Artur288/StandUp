<?php

add_action('init', function () {
	register_taxonomy('city', ['stage', 'comedian'], [
		'labels' => [
			'name'              => 'Города',
			'singular_name'     => 'Город',
			'all_items'         => 'Все города',
			'edit_item'         => 'Редактировать город',
			'view_item'         => 'Посмотреть город',
			'update_item'       => 'Обновить город',
			'add_new_item'      => 'Добавить город',
			'new_item_name'     => 'Название города',
			'search_items'      => 'Найти город',
			'menu_name'         => 'Города',
		],
		'public'            => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => ['slug' => 'city', 'with_front' => false],
	]);
});

// убираем /city/ из публичных ссылок термов — get_term_link() возвращает сразу /{slug}/
add_filter('term_link', function ($url, $term, $taxonomy) {
	if ($taxonomy !== 'city') {
		return $url;
	}
	return home_url('/' . $term->slug . '/');
}, 10, 3);

// маршрутизация: если запрашивается корневой URL и слаг совпадает с термом города —
// переписываем запрос так, чтобы WP отдал term-архив, а не искал страницу
add_filter('request', function ($query_vars) {
	$slug = $query_vars['pagename'] ?? $query_vars['name'] ?? '';
	if ($slug === '') {
		return $query_vars;
	}
	$term = get_term_by('slug', $slug, 'city');
	if ($term && !is_wp_error($term)) {
		return ['city' => $slug];
	}
	return $query_vars;
});

// 301-редирект со старого /city/{slug}/ на новый /{slug}/
add_action('template_redirect', function () {
	if (!is_tax('city')) {
		return;
	}
	global $wp;
	if (strpos($wp->request, 'city/') === 0) {
		$term = get_queried_object();
		if ($term) {
			wp_safe_redirect(get_term_link($term), 301);
			exit;
		}
	}
});
