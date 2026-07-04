<?php

add_action('init', function () {
	register_post_type('form_submission', [
		'labels' => [
			'name'               => 'Заявки',
			'singular_name'      => 'Заявка',
			'edit_item'          => 'Просмотр заявки',
			'view_item'          => 'Просмотр заявки',
			'search_items'       => 'Найти заявку',
			'not_found'          => 'Заявок нет',
			'not_found_in_trash' => 'В корзине заявок нет',
			'menu_name'          => 'Заявки',
			'all_items'          => 'Все заявки',
		],
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		// заявки создаются только обработчиком формы, не из админки
		'capabilities'        => ['create_posts' => 'do_not_allow'],
		'menu_icon'           => 'dashicons-email-alt',
		'menu_position'       => 25,
		'supports'            => ['title'],
	]);
});
