<?php
$args         = $args ?? [];
$city_id      = isset($args['city_term_id']) ? (int) $args['city_term_id'] : 0;
if ($city_id <= 0) {
	return;
}
$city_term = get_term($city_id, 'city');
if (!$city_term || is_wp_error($city_term)) {
	return;
}

$hero_slides           = get_field('hero_slides', 'city_' . $city_id) ?: [];
$concert_section_title = (string) get_field('concert_section_title', 'city_' . $city_id);
$stats_section_title   = (string) get_field('stats_section_title', 'city_' . $city_id);
$stats                 = get_field('stats', 'city_' . $city_id) ?: [];

$schedule = standup_get_city_schedule($city_id);
$dates    = $schedule['dates'];
$events   = $schedule['events'];
uksort($dates, function ($a, $b) use ($dates) {
	return min(array_keys($dates[$a])) <=> min(array_keys($dates[$b]));
});

$city_map = standup_get_city_home_map();
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
						. '<img src="' . esc_url($desktop_url ?: $mobile_url) . '" alt="">'
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

<?php get_template_part('template-parts/blocks/formats-icons', null, ['city_term_id' => $city_id]); ?>

<?php if (!empty($events)):
	$first_date_key = '';
	$first_dates_in_month = [];
	foreach ($dates as $month_name => $month_dates) {
		ksort($month_dates);
		$dates[$month_name] = $month_dates;
		if ($first_date_key === '') {
			$first_date_key = array_key_first($month_dates);
		}
	}
	?>
	<section class="concert" data-schedule>
		<div class="top">
			<div class="container">
				<div class="row1">
					<?php if ($concert_section_title !== ''): ?>
						<h2><?php echo esc_html($concert_section_title); ?></h2>
					<?php endif; ?>
					<?php if (!empty($city_map)): ?>
						<ul class="cities-list is-tabs">
							<?php foreach ($city_map as $slug => $row):
								$is_active = $row['term_id'] === $city_id;
								?>
								<li class="cities-list__item<?php echo $is_active ? ' active' : ''; ?>" data-city="<?php echo esc_attr($slug); ?>">
									<?php if ($is_active): ?>
										<span><?php echo esc_html($row['name']); ?></span>
									<?php else: ?>
										<a href="<?php echo esc_url($row['url']); ?>"><?php echo esc_html($row['name']); ?></a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="row2">
					<div class="date_list">
						<div class="dates-container" data-city="<?php echo esc_attr($city_term->slug); ?>">
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
		<div class="container">
			<div class="concert_list">
				<?php foreach ($events as $date_key => $day_events):
					foreach ($day_events as $ev):
						$is_visible   = ($date_key === $first_date_key);
						$schedule_arr = $ev['schedule'] ?? [];
						$times_str    = implode(' / ', array_filter(array_map(fn ($s) => $s['time_event'] ?? '', $schedule_arr)));
						$count_time   = !empty($schedule_arr) ? (count($schedule_arr) < 2 ? 10 : (int) floor(count($schedule_arr) / 2) * 10) : 0;
						$has_hover    = !empty($ev['thumbnail_hover']);
						?>
						<div class="concert_item<?php echo $has_hover ? '' : ' concert_item--no-hover'; ?><?php echo $is_visible ? '' : ' is-hidden'; ?>" data-city="<?php echo esc_attr($city_term->slug); ?>" data-date="<?php echo esc_attr($date_key); ?>">
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
								<div class="btn info_btn" data-event-id="<?php echo (int) $ev['id']; ?>"><span>Инфо</span></div>
							</div>
						</div>
					<?php endforeach;
				endforeach; ?>
			</div>
		</div>
		<div class="concert_modal" id="concertModal">
			<div class="concert_modal__overlay"></div>
			<div class="concert_modal__content"></div>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/comedians', null, ['city_term_id' => $city_id]); ?>

<?php get_template_part('template-parts/blocks/videos'); ?>

<?php if (!empty($stats) || $stats_section_title !== ''): ?>
	<section class="statistics">
		<div class="container">
			<?php if ($stats_section_title !== ''): ?>
				<h2><?php echo nl2br(esc_html($stats_section_title)); ?></h2>
			<?php endif; ?>
			<?php if (!empty($stats)): ?>
				<div class="statistics_list">
					<div class="swiper statistics_swiper">
						<div class="swiper-wrapper">
							<?php foreach ($stats as $stat): ?>
								<div class="swiper-slide">
									<div class="stat_item">
										<div class="number"><?php echo esc_html($stat['number'] ?? ''); ?></div>
										<div class="info"><?php echo wp_kses_post($stat['label'] ?? ''); ?></div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/formats', null, ['city_term_id' => $city_id]); ?>

<?php get_template_part('template-parts/blocks/social-cta'); ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>
