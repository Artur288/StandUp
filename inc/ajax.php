<?php

add_action('wp_ajax_get_event_modal', 'standup_ajax_get_event_modal');
add_action('wp_ajax_nopriv_get_event_modal', 'standup_ajax_get_event_modal');
function standup_ajax_get_event_modal(): void {
	check_ajax_referer('standup_form', 'nonce');

	$event_id = absint($_POST['event_id'] ?? 0);
	if (!$event_id || get_post_type($event_id) !== 'event') {
		wp_die('', '', ['response' => 400]);
	}

	$is_local = defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'local';

	$cache_key = 'standup_event_modal_' . $event_id;
	if (!$is_local) {
		$cached = get_transient($cache_key);
		if ($cached !== false) {
			echo $cached;
			wp_die();
		}
	}

	ob_start();
	get_template_part('template-parts/blocks/event-modal', null, ['event_id' => $event_id]);
	$html = ob_get_clean();

	if (!$is_local) {
		set_transient($cache_key, $html, HOUR_IN_SECONDS);
	}
	echo $html;
	wp_die();
}

// инвалидация: при сохранении event/stage/comedian/format-термина — чистим кеш связанных модалок
add_action('save_post_event', function ($post_id) {
	delete_transient('standup_event_modal_' . $post_id);
});
add_action('save_post_stage', function ($stage_id) {
	$events = get_posts([
		'post_type'      => 'event',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'meta_query'     => [['key' => 'stage', 'value' => $stage_id, 'compare' => '=']],
	]);
	foreach ($events as $eid) {
		delete_transient('standup_event_modal_' . $eid);
	}
});
add_action('save_post_comedian', function ($comedian_id) {
	$events = get_posts([
		'post_type'      => 'event',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'meta_query'     => [['key' => 'lineup', 'value' => '"' . $comedian_id . '"', 'compare' => 'LIKE']],
	]);
	foreach ($events as $eid) {
		delete_transient('standup_event_modal_' . $eid);
	}
});
add_action('edited_format', function ($term_id) {
	$events = get_posts([
		'post_type'      => 'event',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'tax_query'      => [['taxonomy' => 'format', 'field' => 'term_id', 'terms' => $term_id]],
	]);
	foreach ($events as $eid) {
		delete_transient('standup_event_modal_' . $eid);
	}
});
