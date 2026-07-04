<?php

add_action('init', function () {
	register_post_type('stage', [
		'labels' => [
			'name'               => 'Площадки',
			'singular_name'      => 'Площадка',
			'add_new'            => 'Добавить',
			'add_new_item'       => 'Добавить площадку',
			'edit_item'          => 'Редактировать площадку',
			'new_item'           => 'Новая площадка',
			'view_item'          => 'Посмотреть площадку',
			'search_items'       => 'Найти площадку',
			'not_found'          => 'Площадок не найдено',
			'not_found_in_trash' => 'В корзине площадок нет',
			'menu_name'          => 'Площадки',
			'all_items'          => 'Все площадки',
		],
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => ['slug' => 'stage', 'with_front' => false],
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-location-alt',
		'menu_position' => 7,
		'supports'      => ['title', 'editor', 'thumbnail'],
	]);
});

// детальной страницы у площадки нет — single-запрос редиректим на архив
add_action('template_redirect', function () {
	if (is_singular('stage')) {
		$archive = get_post_type_archive_link('stage');
		if ($archive) {
			wp_safe_redirect($archive, 301);
			exit;
		}
	}
});
