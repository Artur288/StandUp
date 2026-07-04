<?php

add_action('init', function () {
	register_post_type('comedian', [
		'labels' => [
			'name'               => 'Комики',
			'singular_name'      => 'Комик',
			'add_new'            => 'Добавить',
			'add_new_item'       => 'Добавить комика',
			'edit_item'          => 'Редактировать комика',
			'new_item'           => 'Новый комик',
			'view_item'          => 'Посмотреть комика',
			'search_items'       => 'Найти комика',
			'not_found'          => 'Комиков не найдено',
			'not_found_in_trash' => 'В корзине комиков нет',
			'menu_name'          => 'Комики',
			'all_items'          => 'Все комики',
		],
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => ['slug' => 'comedian', 'with_front' => false],
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-microphone',
		'menu_position' => 6,
		'supports'      => ['title', 'editor', 'thumbnail'],
	]);
});
