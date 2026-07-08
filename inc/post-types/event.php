<?php

add_action('init', function () {
	register_post_type('event', [
		'labels' => [
			'name'               => 'События',
			'singular_name'      => 'Событие',
			'add_new'            => 'Добавить',
			'add_new_item'       => 'Добавить событие',
			'edit_item'          => 'Редактировать событие',
			'new_item'           => 'Новое событие',
			'view_item'          => 'Посмотреть событие',
			'search_items'       => 'Найти событие',
			'not_found'          => 'Событий не найдено',
			'not_found_in_trash' => 'В корзине событий нет',
			'menu_name'          => 'События',
			'all_items'          => 'Все события',
		],
		'public'        => true,
		'has_archive'   => false,
		'rewrite'       => ['slug' => 'event', 'with_front' => false],
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-calendar-alt',
		'menu_position' => 5,
		'supports'      => ['title', 'editor', 'thumbnail', 'page-attributes'],
	]);
});

// event без base в URL: /название вместо /event/название
add_filter('post_type_link', function ($link, $post) {
	if ($post->post_type === 'event' && $post->post_status === 'publish') {
		$link = str_replace('/event/', '/', $link);
	}
	return $link;
}, 10, 2);

// голый slug WP по умолчанию ищет среди post/page — добавляем сюда event
add_action('pre_get_posts', function ($query) {
	if (!$query->is_main_query() || count($query->query) !== 2 || !isset($query->query['page'])) {
		return;
	}
	if (!empty($query->query['name'])) {
		$query->set('post_type', ['post', 'page', 'event']);
	}
});

add_action('save_post_event', function ($post_id, $post) {
	static $running = false;
	if ($running) {
		return;
	}
	if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
		return;
	}
	if ($post->post_status !== 'publish') {
		return;
	}

	$terms = wp_get_object_terms($post_id, 'format', ['fields' => 'ids']);
	if (empty($terms) || is_wp_error($terms)) {
		$running = true;
		wp_update_post(['ID' => $post_id, 'post_status' => 'draft']);
		$running = false;
		set_transient('standup_event_no_format_' . $post_id, 1, 60);
	}
}, 10, 2);

add_action('admin_notices', function () {
	global $post;
	if (!$post || $post->post_type !== 'event') {
		return;
	}
	if (get_transient('standup_event_no_format_' . $post->ID)) {
		delete_transient('standup_event_no_format_' . $post->ID);
		echo '<div class="notice notice-error"><p>Событие нельзя опубликовать без формата. Выбери формат в сайдбаре и сохрани снова.</p></div>';
	}
});
