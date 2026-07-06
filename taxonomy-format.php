<?php
get_header();

$term = get_queried_object();
if (!$term || empty($term->term_id)) {
	get_footer();
	return;
}

$term_id          = (int) $term->term_id;
$hero_slides      = get_field('hero_slides', 'format_' . $term_id) ?: [];
$bubbles          = get_field('bubbles', 'format_' . $term_id) ?: [];
$hide_bubbles     = (bool) get_field('hide_bubbles', 'format_' . $term_id);
$events_layout    = (string) get_field('events_layout', 'format_' . $term_id);
$events_layout    = ($events_layout === 'list') ? 'list' : 'blocks';
$is_excursion     = (bool) get_field('is_excursion', 'format_' . $term_id);
$is_free_event    = (bool) get_field('is_free_event', 'format_' . $term_id);

$format_city_id   = (int) get_field('city', 'format_' . $term_id);
// у дочернего терма город не указан — берём город родителя
if (!$format_city_id && $term->parent) {
	$format_city_id = (int) get_field('city', 'format_' . $term->parent);
}
$related_ids      = array_map('intval', (array) get_field('related_formats', 'format_' . $term_id));

$hedliter_title           = (string) get_field('format_hedliter_title', 'option');
$concert_section_title    = (string) get_field('format_concert_title', 'option');
$about_section_title      = (string) get_field('format_about_title', 'option');
$f_comedians_title        = (string) get_field('format_comedians_title', 'option');
$f_comedians_description  = (string) get_field('format_comedians_description', 'option');

$show_hedliter            = (bool) get_field('show_hedliter', 'format_' . $term_id);
$show_comedians_cta       = (bool) get_field('show_comedians_cta', 'format_' . $term_id);
$hide_comedians           = (bool) get_field('hide_comedians', 'format_' . $term_id);
$term_videos_title        = (string) get_field('videos_title', 'format_' . $term_id);
$term_comedians_title     = (string) get_field('comedians_title', 'format_' . $term_id);
$term_comedians_descr     = (string) get_field('comedians_description', 'format_' . $term_id);

// табы: для каждого связанного формата — таб его города. сортировка по term_id города ASC.
$city_tabs = [];
foreach ($related_ids as $rid) {
	$rcity_id = (int) get_field('city', 'format_' . $rid);
	if (!$rcity_id) continue;
	$rcity = get_term($rcity_id, 'city');
	if (!$rcity || is_wp_error($rcity)) continue;
	$rterm = get_term($rid, 'format');
	if (!$rterm || is_wp_error($rterm)) continue;
	$city_tabs[$rcity->slug] = [
		'name'    => $rcity->name,
		'url'     => get_term_link($rterm),
		'term_id' => $rcity_id,
	];
}
uasort($city_tabs, fn($a, $b) => $a['term_id'] <=> $b['term_id']);
// текущий город — активный таб
$current_city = $format_city_id ? get_term($format_city_id, 'city') : null;

// табы дочерних термов формата (например, конкретные площадки одной концепции).
// если у терма есть родитель — берём всех детей родителя (себя + братьев), иначе — своих детей
$format_subtabs = [];
$subtabs_parent_id = $term->parent ?: $term_id;
$sibling_terms = get_terms([
	'taxonomy'   => 'format',
	'parent'     => $subtabs_parent_id,
	'hide_empty' => false,
	'orderby'    => 'term_id',
	'order'      => 'ASC',
]);
if (!is_wp_error($sibling_terms) && !empty($sibling_terms)) {
	foreach ($sibling_terms as $sib) {
		$format_subtabs[] = [
			'name'   => $sib->name,
			'url'    => get_term_link($sib),
			'active' => ($sib->term_id === $term_id),
		];
	}
}

// комики из событий этого формата (в городе формата)
$format_event_ids = [];
if ($format_city_id) {
	$stage_ids = get_posts([
		'post_type'      => 'stage',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'tax_query'      => [
			['taxonomy' => 'city', 'field' => 'term_id', 'terms' => $format_city_id],
		],
	]);
	if (!empty($stage_ids)) {
		$format_event_ids = get_posts([
			'post_type'      => 'event',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'tax_query'      => [
				['taxonomy' => 'format', 'field' => 'term_id', 'terms' => $term_id],
			],
			'meta_query'     => [
				['key' => 'stage', 'value' => $stage_ids, 'compare' => 'IN'],
			],
		]);
	}
}
$format_comedian_ids = [];
foreach ($format_event_ids as $eid) {
	foreach ((array) get_field('lineup', $eid) as $cid) {
		$format_comedian_ids[(int) $cid] = true;
	}
}
$format_comedian_ids = array_keys($format_comedian_ids);

$schedule = $format_city_id ? standup_get_city_schedule($format_city_id, $term_id) : ['dates' => [], 'events' => []];
$dates    = $schedule['dates'];
$events   = $schedule['events'];
uksort($dates, function ($a, $b) use ($dates) {
	return min(array_keys($dates[$a])) <=> min(array_keys($dates[$b]));
});

$hedliters = [];
if ($show_hedliter) {
	$hedliter_query = [
		'post_type'      => 'comedian',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => [['key' => 'is_headliner', 'value' => '1']],
	];
	if ($format_city_id) {
		$hedliter_query['tax_query'] = [
			['taxonomy' => 'city', 'field' => 'term_id', 'terms' => $format_city_id],
		];
	}
	$hedliters = get_posts($hedliter_query);
}
?>

<?php if (!empty($hero_slides)): ?>
	<section class="home_slider">
		<div class="swiper homeSwiper">
			<div class="swiper-wrapper">
				<?php foreach ($hero_slides as $slide):
					$desktop_id  = (int) ($slide['image_desktop'] ?? 0);
					$mobile_id   = (int) ($slide['image_mobile'] ?? 0);
					$link        = (string) ($slide['link'] ?? '');
					$desktop_url = $desktop_id ? wp_get_attachment_image_url($desktop_id, 'full') : '';
					$mobile_url  = $mobile_id ? wp_get_attachment_image_url($mobile_id, 'full') : '';
					if (!$desktop_url && !$mobile_url) continue;
					$inner = '<picture>'
						. ($mobile_url ? '<source media="(max-width: 767px)" srcset="' . esc_url($mobile_url) . '">' : '')
						. '<img src="' . esc_url($desktop_url ?: $mobile_url) . '" alt="' . esc_attr($term->name) . '">'
						. '</picture>';
					?>
					<div class="swiper-slide">
						<?php if ($link !== ''): ?>
							<a href="<?php echo esc_url($link); ?>"><?php echo $inner; ?></a>
						<?php else: ?>
							<?php echo $inner; ?>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ($show_hedliter && !empty($hedliters)): ?>
	<section class="hedliter">
		<div class="container">
			<?php if ($hedliter_title !== ''): ?>
				<h2><?php echo esc_html($hedliter_title); ?></h2>
			<?php endif; ?>
			<div class="swiper swiper-list_hedliter">
				<div class="swiper-edge swiper-edge--right"></div>
				<div class="swiper-wrapper">
					<?php foreach ($hedliters as $comedian):
						$name_parts        = explode(' ', $comedian->post_title, 2);
						$headliner_photo_id = (int) get_field('headliner_photo', $comedian->ID);
						?>
						<div class="swiper-slide">
							<div class="hedliter_card">
								<a href="<?php echo esc_url(get_permalink($comedian)); ?>">
									<div class="comedian_thumb">
										<?php if ($headliner_photo_id): ?>
											<?php echo wp_get_attachment_image($headliner_photo_id, 'medium', false, ['alt' => $comedian->post_title]); ?>
										<?php else: ?>
											<?php echo get_the_post_thumbnail($comedian, 'medium', ['alt' => $comedian->post_title]); ?>
										<?php endif; ?>
									</div>
									<div class="comedian_name"><?php echo esc_html($name_parts[0]); ?><?php if (isset($name_parts[1])): ?><br><?php echo esc_html($name_parts[1]); ?><?php endif; ?></div>
								</a>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="swiper-button-next"></div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (!empty($events)):
	$first_date_key = '';
	foreach ($dates as $mn => $md) {
		ksort($md);
		$dates[$mn] = $md;
		if ($first_date_key === '') $first_date_key = array_key_first($md);
	}
	?>
	<section class="concert" data-schedule data-load-step="5">
		<div class="top">
			<div class="container">
				<div class="row1">
					<?php if ($concert_section_title !== ''): ?>
						<h2><?php echo esc_html($concert_section_title); ?></h2>
					<?php endif; ?>
					<?php if ($current_city || !empty($city_tabs)): ?>
						<ul class="cities-list is-tabs">
							<?php if ($current_city): ?>
								<li class="cities-list__item active" data-city="<?php echo esc_attr($current_city->slug); ?>">
									<span><?php echo esc_html($current_city->name); ?></span>
								</li>
							<?php endif; ?>
							<?php foreach ($city_tabs as $slug => $row): ?>
								<li class="cities-list__item" data-city="<?php echo esc_attr($slug); ?>">
									<a href="<?php echo esc_url($row['url']); ?>"><?php echo esc_html($row['name']); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="row2">
					<div class="date_list">
						<div class="dates-container" data-city="<?php echo esc_attr($current_city ? $current_city->slug : ''); ?>">
							<?php $is_first = true; foreach ($dates as $month_name => $month_dates): ?>
								<div class="item">
									<div class="month"><?php echo esc_html($month_name); ?></div>
									<ul class="dates">
										<?php foreach ($month_dates as $date_key => $date):
											$is_active = $is_first;
											if ($is_first) $is_first = false;
											?>
											<li class="dates__item<?php echo $is_active ? ' active' : ''; ?>" data-date="<?php echo esc_attr($date_key); ?>">
												<span class="dates__day"><?php echo esc_html($date['day']); ?></span>
												<span class="dates__weekday"><?php echo esc_html($date['weekday']); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php if (!empty($format_subtabs)): ?>
			<div class="container">
				<div class="cities-list_wrapper">
					<ul class="cities-list is-tabs is-wide">
						<?php foreach ($format_subtabs as $st): ?>
							<li class="cities-list__item<?php echo $st['active'] ? ' active' : ''; ?>">
								<?php if ($st['active']): ?>
									<span><?php echo esc_html($st['name']); ?></span>
								<?php else: ?>
									<a href="<?php echo esc_url($st['url']); ?>"><?php echo esc_html($st['name']); ?></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>
		<div class="container">
			<div class="concert_list<?php echo $events_layout === 'list' ? ' concert_row' : ''; ?>">
				<?php
				$concert_modal_ids = [];
				$ru_months_genitive = ['', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
				$ru_months_short    = ['', 'янв', 'февр', 'март', 'апр', 'мая', 'июня', 'июля', 'авг', 'сент', 'окт', 'нояб', 'дек'];
				$ru_weekdays_short  = ['вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'];
				foreach ($events as $date_key => $day_events):
					foreach ($day_events as $ev):
						$is_visible   = ($date_key === $first_date_key);
						$schedule_arr = $ev['schedule'] ?? [];
						$times_str    = implode(' / ', array_filter(array_map(fn ($s) => $s['time_event'] ?? '', $schedule_arr)));
						$count_time   = !empty($schedule_arr) ? (count($schedule_arr) < 2 ? 10 : (int) floor(count($schedule_arr) / 2) * 10) : 0;
						$has_hover    = !empty($ev['thumbnail_hover']);

						$year = (int) substr($date_key, 0, 4);
						$mon  = (int) substr($date_key, 4, 2);
						$day  = substr($date_key, 6, 2);
						$ts   = mktime(0, 0, 0, $mon, (int) $day, $year);
						$weekday        = $ru_weekdays_short[(int) wp_date('w', $ts)] ?? '';
						$month_genitive = $ru_months_genitive[$mon] ?? '';
						$month_short    = $ru_months_short[$mon] ?? '';
						?>
						<?php if ($events_layout === 'list'): ?>
							<div class="concert_item<?php echo $is_visible ? '' : ' is-hidden'; ?>" data-city="<?php echo esc_attr($current_city ? $current_city->slug : ''); ?>" data-date="<?php echo esc_attr($date_key); ?>">
								<div class="event-card">
									<div class="event-date">
										<div class="left">
											<div class="event-date-day"><?php echo esc_html($day); ?></div>
											<div class="mob_month"><?php echo esc_html($month_short); ?></div>
										</div>
										<div class="right">
											<span class="event-date-weekday"><?php echo esc_html($weekday); ?> /</span>
											<span class="event-date-time"><?php echo esc_html($times_str); ?></span>
											<div class="event-subtitle"><?php echo esc_html($ev['position'] ?? ''); ?></div>
										</div>
										<div class="event-date-meta">
											<span class="event-date-weekday"><?php echo esc_html($weekday); ?> /</span>
											<span class="event-date-time"><?php echo esc_html($times_str); ?></span>
											<div class="event-date-full"><?php echo esc_html($month_genitive); ?></div>
										</div>
									</div>
									<div class="event-subtitle"><?php echo esc_html($ev['position'] ?? ''); ?></div>
									<div class="event-content">
										<h3 class="event-title"><?php echo esc_html($ev['title']); ?></h3>
										<div class="event-description"><?php echo esc_html($ev['description'] ?? ''); ?></div>
									</div>
									<div class="event-controls bottom<?php echo (int) $count_time; ?>">
										<?php if ($is_excursion): ?><div class="btn js-stage-info-open" data-target="eventInfoModal-<?php echo (int) $ev['id']; ?>"><span>Инфо</span></div><?php else: ?><div class="btn info_btn" data-event-id="<?php echo (int) $ev['id']; ?>"><span>Инфо</span></div><?php endif; ?>
										<div class="event-buy controls">
											<div class="buy_block">
												<div class="btn buy_tickerts"><span>Купить билет</span></div>
												<div class="btn buy_tickerts_hide"><span>Скрыть время</span></div>
												<div class="buy_tickerts_info_time">
													<?php foreach ($schedule_arr as $s): ?>
														<div class="btn tickets_list"><a href="<?php echo esc_url($s['bilet'] ?? '#'); ?>" class="ticket_time"><?php echo esc_html($s['time_event'] ?? ''); ?></a></div>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php else: ?>
							<div class="concert_item<?php echo $has_hover ? '' : ' concert_item--no-hover'; ?><?php echo $is_visible ? '' : ' is-hidden'; ?>" data-city="<?php echo esc_attr($current_city ? $current_city->slug : ''); ?>" data-date="<?php echo esc_attr($date_key); ?>">
								<div class="images">
									<img src="<?php echo esc_url($ev['thumbnail']); ?>" alt="" class="image_1">
									<?php if ($has_hover): ?>
										<img src="<?php echo esc_url($ev['thumbnail_hover']); ?>" alt="" class="image_2">
									<?php endif; ?>
								</div>
								<div class="bottom_image">
									<span class="time"><?php echo esc_html($times_str); ?></span>
									<span class="position"><?php echo esc_html($ev['position'] ?? ''); ?></span>
								</div>
								<h3 class="concert_title"><?php echo esc_html($ev['title']); ?><?php if (!empty($ev['description'])): ?><span><?php echo esc_html($ev['description']); ?></span><?php endif; ?></h3>
								<div class="controls bottom<?php echo (int) $count_time; ?>">
									<div class="buy_block">
										<div class="btn buy_tickerts"><span>Купить билет</span></div>
										<div class="btn buy_tickerts_hide"><span>Скрыть время</span></div>
										<div class="buy_tickerts_info_time">
											<?php foreach ($schedule_arr as $s): ?>
												<div class="btn tickets_list"><a href="<?php echo esc_url($s['bilet'] ?? '#'); ?>" class="ticket_time"><?php echo esc_html($s['time_event'] ?? ''); ?></a></div>
											<?php endforeach; ?>
										</div>
									</div>
									<?php if ($is_excursion): ?><div class="btn js-stage-info-open" data-target="eventInfoModal-<?php echo (int) $ev['id']; ?>"><span>Инфо</span></div><?php else: ?><div class="btn info_btn" data-event-id="<?php echo (int) $ev['id']; ?>"><span>Инфо</span></div><?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
						<?php if ($is_excursion || $is_free_event): $concert_modal_ids[] = (int) $ev['id']; endif; ?>
					<?php endforeach;
				endforeach; ?>
			</div>
			<?php foreach ($concert_modal_ids as $modal_event_id): ?>
				<?php if ($is_excursion): ?>
					<?php get_template_part('template-parts/modals/event-info', null, ['event_id' => $modal_event_id]); ?>
				<?php elseif ($is_free_event): ?>
					<?php get_template_part('template-parts/modals/free-event-booking', null, ['event_id' => $modal_event_id]); ?>
				<?php endif; ?>
			<?php endforeach; ?>
			<div class="concert_more">
				<div class="btn_load_more"><span>Показать ещё</span>
					<svg xmlns="http://www.w3.org/2000/svg" width="19" height="9" viewBox="0 0 19 9" fill="none">
						<path d="M0.635742 0.771973L9.13574 7.77197L17.6357 0.771973" stroke="#EEEDDE" stroke-opacity="0.7" stroke-width="2" stroke-linejoin="round"></path>
					</svg>
				</div>
			</div>
		</div>
		<div class="concert_modal" id="concertModal">
			<div class="concert_modal__overlay"></div>
			<div class="concert_modal__content"></div>
		</div>
	</section>
<?php endif; ?>

<?php if ($term->description !== '' && !$hide_bubbles): ?>
<section class="event-format">
	<div class="container">
		<div class="event-format__content">
			<div class="event-format__left">
				<?php if ($about_section_title !== ''): ?>
					<h2 class="event-format__title"><?php echo esc_html($about_section_title); ?></h2>
				<?php endif; ?>
				<div class="event-format__text"><?php echo wpautop(wp_kses_post($term->description)); ?></div>
				<div class="btn_readmore event-format__link">
					<span class="open active">Подробнее</span>
					<span class="close">Скрыть</span>
					<svg xmlns="http://www.w3.org/2000/svg" width="19" height="9" viewBox="0 0 19 9" fill="none">
						<path d="M0.635742 0.771973L9.13574 7.77197L17.6357 0.771973" stroke="#EEEDDE" stroke-opacity="0.7" stroke-width="2" stroke-linejoin="round"></path>
					</svg>
				</div>
			</div>
			<?php if (!empty($bubbles)): ?>
				<div class="event-format__right">
					<?php foreach ($bubbles as $bubble): ?>
						<div class="event-format__bubble">
							<div class="title_block"><?php echo esc_html($bubble['bubble_title'] ?? ''); ?></div>
							<div class="description_block"><?php echo esc_html($bubble['bubble_description'] ?? ''); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/videos', null, [
	'title_override' => $term_videos_title !== '' ? $term_videos_title : '',
]); ?>

<?php if (!$hide_comedians): ?>
	<?php get_template_part('template-parts/blocks/comedians', null, [
		'title_override'       => $term_comedians_title !== '' ? $term_comedians_title : $f_comedians_title,
		'description_override' => $term_comedians_descr  !== '' ? $term_comedians_descr  : $f_comedians_description,
		'show_cta'             => $show_comedians_cta,
		'comedian_ids'         => $format_comedian_ids,
	]); ?>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>

<?php get_template_part('template-parts/blocks/formats', null, [
	'exclude_term_id' => $term_id,
	'city_term_id'    => $format_city_id,
	'mode'            => 'recommend',
]); ?>

<?php get_footer();
