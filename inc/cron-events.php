<?php

const STANDUP_TRASH_EVENTS_HOOK = 'standup_trash_past_events';

add_action('init', function () {
	if (!wp_next_scheduled(STANDUP_TRASH_EVENTS_HOOK)) {
		wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', STANDUP_TRASH_EVENTS_HOOK);
	}
});

add_action('switch_theme', function () {
	wp_clear_scheduled_hook(STANDUP_TRASH_EVENTS_HOOK);
});

add_action(STANDUP_TRASH_EVENTS_HOOK, function () {
	$today = wp_date('Ymd');

	$query = new WP_Query([
		'post_type'      => 'event',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'meta_query'     => [
			[
				'key'     => 'event_date',
				'value'   => $today,
				'compare' => '<',
				'type'    => 'NUMERIC',
			],
		],
	]);

	foreach ($query->posts as $event_id) {
		wp_trash_post($event_id);
	}
});
