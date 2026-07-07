<?php

const STANDUP_RU_MONTHS = [
	1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
	5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
	9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь',
];

const STANDUP_RU_MONTHS_GENITIVE = [
	1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
	5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
	9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря',
];

const STANDUP_RU_WEEKDAYS_SHORT = ['вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'];

// "HH:MM" → "HHMM" для лексикографической сортировки. пусто/мусор → "9999" (в конец)
function standup_time_sort_key(string $time): string {
	if (preg_match('~^\s*(\d{1,2})\s*[:.\-]\s*(\d{2})~', $time, $m)) {
		return sprintf('%02d%02d', (int) $m[1], (int) $m[2]);
	}
	return '9999';
}

// минимальный sort_key из массива schedule-строк ACF (с ключом 'time')
function standup_schedule_min_time_key(array $schedule): string {
	$keys = [];
	foreach ($schedule as $row) {
		$keys[] = standup_time_sort_key((string) ($row['time'] ?? ''));
	}
	return $keys ? min($keys) : '9999';
}

// "HH:MM" минус N минут → "HH:MM" (для «сбор гостей за полчаса до начала»)
function standup_time_minus_minutes(string $time, int $minutes): string {
	if (!preg_match('~^\s*(\d{1,2})[:.](\d{2})~', $time, $m)) {
		return '';
	}
	$ts = mktime((int) $m[1], (int) $m[2], 0) - $minutes * 60;
	return date('H:i', $ts);
}

// возрастное ограничение события: своё значение или дефолт формата
function standup_event_is_18_plus(int $event_id, int $format_term_id): bool {
	$override = (string) get_field('age_18_plus', $event_id);
	if ($override === 'yes') return true;
	if ($override === 'no') return false;
	return $format_term_id ? (bool) get_field('default_18_plus', 'format_' . $format_term_id) : false;
}

// наличие мата на событии: своё значение или дефолт формата
function standup_event_has_mat(int $event_id, int $format_term_id): bool {
	$override = (string) get_field('has_mat', $event_id);
	if ($override === 'yes') return true;
	if ($override === 'no') return false;
	return $format_term_id ? (bool) get_field('default_has_mat', 'format_' . $format_term_id) : false;
}

// продолжительность события: своё значение или дефолт формата
function standup_event_duration(int $event_id, int $format_term_id): string {
	$own = (string) get_field('duration', $event_id);
	if ($own !== '') return $own;
	return $format_term_id ? (string) get_field('default_duration', 'format_' . $format_term_id) : '';
}

// шапка попапов бесплатного мероприятия: «10.09 20:00 / сбор гостей в 19:30 / 18+»
function standup_free_event_header(int $event_id, int $format_term_id): string {
	$event_date = (string) get_field('event_date', $event_id);
	$schedule   = get_field('schedule', $event_id) ?: [];
	usort($schedule, fn($a, $b) => strcmp(
		standup_time_sort_key((string) ($a['time'] ?? '')),
		standup_time_sort_key((string) ($b['time'] ?? ''))
	));
	$first_time = (string) ($schedule[0]['time'] ?? '');
	$gathering  = $first_time !== '' ? standup_time_minus_minutes($first_time, 30) : '';

	$parts = [];
	if (strlen($event_date) === 8) {
		$parts[] = substr($event_date, 6, 2) . '.' . substr($event_date, 4, 2) . ($first_time !== '' ? ' ' . $first_time : '');
	}
	if ($gathering !== '') $parts[] = 'сбор гостей в ' . $gathering;
	if (standup_event_is_18_plus($event_id, $format_term_id)) $parts[] = '18+';
	return implode(' / ', $parts);
}

// текущий объект для get_field: ID поста, term-строка или null (на post_type_archive контекста нет)
function standup_current_acf_object_id() {
	if (is_singular()) return get_the_ID();
	if (is_tax() || is_category() || is_tag()) {
		$t = get_queried_object();
		return ($t && !is_wp_error($t)) ? $t->taxonomy . '_' . $t->term_id : null;
	}
	if (is_front_page()) {
		$front = (int) get_option('page_on_front');
		return $front ?: null;
	}
	return null;
}

// бул — нужно ли скрыть концерты на текущей странице
function standup_should_hide_concerts(): bool {
	$id = standup_current_acf_object_id();
	if (!$id) return false;
	return (bool) get_field('hide_concerts', $id);
}

// данные блока «Видео»: поле за полем оверрайдится локальным контекстом, fallback на options
function standup_get_videos_block(): array {
	$id     = standup_current_acf_object_id();
	$title  = $id ? (string) get_field('videos_section_title', $id) : '';
	$videos = $id ? (get_field('videos', $id) ?: []) : [];
	if ($title === '')  $title  = (string) get_field('videos_section_title', 'option');
	if (empty($videos)) $videos = get_field('videos', 'option') ?: [];
	return ['title' => $title, 'videos' => $videos];
}

// слаг активного города из ?gorod=. используется только в архивах stage/comedian.
function standup_resolve_active_city_slug(array $valid_slugs): string {
	if (empty($valid_slugs)) return '';
	$requested = isset($_GET['gorod']) ? sanitize_title(wp_unslash($_GET['gorod'])) : '';
	if ($requested !== '' && in_array($requested, $valid_slugs, true)) {
		return $requested;
	}
	return (string) reset($valid_slugs);
}

// слаги городов комика — берутся напрямую из таксономии city на посте комика
function standup_get_comedian_city_slugs(int $comedian_id): array {
	if (!$comedian_id) {
		return [];
	}
	$terms = get_the_terms($comedian_id, 'city');
	if (empty($terms) || is_wp_error($terms)) {
		return [];
	}
	return array_map(fn($t) => $t->slug, $terms);
}

// термины городов, к которым привязан хотя бы один опубликованный комик
function standup_get_active_comedian_city_terms(): array {
	$terms = get_terms([
		'taxonomy'   => 'city',
		'hide_empty' => false,
		'object_ids' => get_posts([
			'post_type'      => 'comedian',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		]),
	]);
	if (is_wp_error($terms) || empty($terms)) {
		return [];
	}
	$unique = [];
	foreach ($terms as $t) {
		$unique[$t->term_id] = $t;
	}
	return array_values($unique);
}

// события одного города (опционально — одного формата). возвращает [ 'dates' => [...], 'events' => [...] ] для активного города
function standup_get_city_schedule(int $city_term_id, ?int $format_term_id = null): array {
	if ($city_term_id <= 0) {
		return ['dates' => [], 'events' => []];
	}
	$today = wp_date('Ymd');

	// собираем id площадок в этом городе
	$stage_ids = get_posts([
		'post_type'      => 'stage',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'tax_query'      => [
			['taxonomy' => 'city', 'field' => 'term_id', 'terms' => $city_term_id],
		],
	]);
	if (empty($stage_ids)) {
		return ['dates' => [], 'events' => []];
	}

	$query_args = [
		'post_type'      => 'event',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'     => 'event_date',
				'value'   => $today,
				'compare' => '>=',
				'type'    => 'NUMERIC',
			],
			[
				'key'     => 'stage',
				'value'   => $stage_ids,
				'compare' => 'IN',
			],
		],
		'meta_key'       => 'event_date',
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
	];
	if ($format_term_id !== null) {
		$query_args['tax_query'] = [
			['taxonomy' => 'format', 'field' => 'term_id', 'terms' => $format_term_id],
		];
	}
	$query = new WP_Query($query_args);

	$dates  = [];
	$events = [];

	foreach ($query->posts as $event) {
		$event_date = (string) get_field('event_date', $event->ID);
		if (strlen($event_date) !== 8) continue;

		$stage_id = (int) get_field('stage', $event->ID);
		if (!$stage_id || !in_array($stage_id, $stage_ids, true)) continue;
		$stage = get_post($stage_id);
		if (!$stage) continue;

		$format_terms = get_the_terms($event->ID, 'format');
		$format_name  = (!empty($format_terms) && !is_wp_error($format_terms)) ? $format_terms[0]->name : '';

		$year  = (int) substr($event_date, 0, 4);
		$month = (int) substr($event_date, 4, 2);
		$day   = (int) substr($event_date, 6, 2);
		$month_name  = STANDUP_RU_MONTHS[$month] ?? '';
		$timestamp   = mktime(0, 0, 0, $month, $day, $year);
		$weekday_idx = (int) wp_date('w', $timestamp);
		$weekday     = STANDUP_RU_WEEKDAYS_SHORT[$weekday_idx];

		$dates[$month_name][$event_date] = [
			'day'     => (string) $day,
			'weekday' => $weekday,
		];

		$schedule = get_field('schedule', $event->ID) ?: [];
		usort($schedule, fn($a, $b) => strcmp(
			standup_time_sort_key((string) ($a['time'] ?? '')),
			standup_time_sort_key((string) ($b['time'] ?? ''))
		));
		$schedule_data = [];
		foreach ($schedule as $row) {
			$schedule_data[] = [
				'time_event' => $row['time'] ?? '',
				'bilet'      => $row['ticket_url'] ?? '#',
			];
		}

		$thumb_id  = get_post_thumbnail_id($event->ID);
		$thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';
		$hover_id  = (int) get_field('thumbnail_hover', $event->ID);
		$hover_url = $hover_id ? wp_get_attachment_image_url($hover_id, 'large') : '';

		// на странице конкретного формата — своё название события; на общей странице города (форматы вперемешку) — название формата
		$card_title = $format_term_id !== null ? $event->post_title : $format_name;

		$events[$event_date][] = [
			'id'              => $event->ID,
			'title'           => $card_title,
			'description'     => trim(wp_strip_all_tags($event->post_content)),
			'position'        => $stage->post_title,
			'schedule'        => $schedule_data,
			'thumbnail'       => $thumb_url,
			'thumbnail_hover' => $hover_url,
		];
	}

	// внутри одной даты — сортируем события по самому раннему времени из schedule
	foreach ($events as $date_key => &$day_events) {
		usort($day_events, function ($a, $b) {
			$ka = $a['schedule'][0]['time_event'] ?? '';
			$kb = $b['schedule'][0]['time_event'] ?? '';
			return strcmp(standup_time_sort_key((string) $ka), standup_time_sort_key((string) $kb));
		});
	}
	unset($day_events);

	return ['dates' => $dates, 'events' => $events];
}

// карта активных городов для табов: [ city_slug => ['name', 'url', 'term_id'] ]
// порядок: front-city первый, дальше остальные с заполненным hero_slides
function standup_get_city_home_map(): array {
	$map = [];

	// 1) собираем страницы с page-template city-home
	$city_to_page_url = [];
	$pages = get_posts([
		'post_type'      => 'page',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'page-city-home.php',
		'fields'         => 'ids',
	]);
	foreach ($pages as $page_id) {
		$cid = (int) get_field('city', $page_id);
		if ($cid && !isset($city_to_page_url[$cid])) {
			$city_to_page_url[$cid] = get_permalink($page_id);
		}
	}

	// 2) front-page → её город
	$front_id      = (int) get_option('page_on_front');
	$front_city_id = $front_id ? (int) get_field('city', $front_id) : 0;

	// 3) активные термы city — сортировка по term_id ASC (сначала старые)
	$terms = get_terms([
		'taxonomy'   => 'city',
		'hide_empty' => false,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	]);
	if (is_wp_error($terms) || empty($terms)) return [];

	$first  = [];
	$others = [];
	foreach ($terms as $term) {
		$has_page   = isset($city_to_page_url[$term->term_id]);
		$is_front   = $front_city_id === $term->term_id;
		$has_slides = !empty(get_field('hero_slides', 'city_' . $term->term_id));
		if (!$has_page && !$is_front && !$has_slides) continue;

		$url = $city_to_page_url[$term->term_id] ?? null;
		if (!$url && $is_front)                $url = home_url('/');
		if (!$url)                             $url = get_term_link($term);

		$row = [
			'name'    => $term->name,
			'url'     => is_string($url) ? $url : '',
			'term_id' => $term->term_id,
		];
		if ($is_front) {
			$first[$term->slug] = $row;
		} else {
			$others[$term->slug] = $row;
		}
	}
	return $first + $others;
}

function standup_format_event_date_long(string $event_date): string {
	if (strlen($event_date) !== 8) {
		return '';
	}
	$year  = (int) substr($event_date, 0, 4);
	$month = (int) substr($event_date, 4, 2);
	$day   = (int) substr($event_date, 6, 2);
	$month_name = STANDUP_RU_MONTHS_GENITIVE[$month] ?? '';
	return $day . ' ' . $month_name . ' ' . $year;
}
