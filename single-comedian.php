<?php
get_header();

if (!have_posts()) {
	get_footer();
	return;
}
the_post();

$comedian_id = get_the_ID();
$page_photo_id        = (int) get_field('thumbnail_hover');
$page_photo_mobile_id = (int) get_field('page_photo_mobile');
$bio_section_title    = (string) get_field('bio_section_title', 'option');
$upcoming_events_title = (string) get_field('upcoming_events_title', 'option');

$dob         = (string) get_field('date_of_birth');
$patronymic  = (string) get_field('patronymic');
$birthplace  = (string) get_field('birthplace');

$dob_field        = function_exists('get_field_object') ? get_field_object('date_of_birth') : null;
$patronymic_field = function_exists('get_field_object') ? get_field_object('patronymic') : null;
$birthplace_field = function_exists('get_field_object') ? get_field_object('birthplace') : null;

$dob_label        = $dob_field ? mb_strtolower($dob_field['label']) : '';
$patronymic_label = $patronymic_field ? mb_strtolower($patronymic_field['label']) : '';
$birthplace_label = $birthplace_field ? mb_strtolower($birthplace_field['label']) : '';

$bio = apply_filters('the_content', get_the_content());

// события, где этот комик в составе, начиная с сегодня
$today = wp_date('Ymd');
$upcoming_events = get_posts([
	'post_type'      => 'event',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'meta_query'     => [
		'relation' => 'AND',
		['key' => 'event_date', 'value' => $today, 'compare' => '>=', 'type' => 'NUMERIC'],
		[
			'key'     => 'lineup',
			'value'   => '"' . $comedian_id . '"',
			'compare' => 'LIKE',
		],
	],
	'meta_key'       => 'event_date',
	'orderby'        => 'meta_value_num',
	'order'          => 'ASC',
]);

// добиваем сортировку по времени в schedule — внутри одной даты раньше идёт более ранний сеанс
usort($upcoming_events, function ($a, $b) {
	$da = (string) get_field('event_date', $a->ID);
	$db = (string) get_field('event_date', $b->ID);
	if ($da !== $db) return strcmp($da, $db);
	$ta = standup_schedule_min_time_key(get_field('schedule', $a->ID) ?: []);
	$tb = standup_schedule_min_time_key(get_field('schedule', $b->ID) ?: []);
	return strcmp($ta, $tb);
});
?>

<?php if ($page_photo_id || $page_photo_mobile_id):
	$desktop_url = $page_photo_id ? wp_get_attachment_image_url($page_photo_id, 'full') : '';
	$mobile_url  = $page_photo_mobile_id ? wp_get_attachment_image_url($page_photo_mobile_id, 'full') : '';
	?>
	<section class="home_slider">
		<div class="swiper homeSwiper">
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<picture>
						<?php if ($mobile_url): ?>
							<source media="(max-width: 767px)" srcset="<?php echo esc_url($mobile_url); ?>">
						<?php endif; ?>
						<img src="<?php echo esc_url($desktop_url ?: $mobile_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
					</picture>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ($bio !== ''): ?>
<section class="event-format">
	<div class="container">
		<div class="event-format__content">
			<div class="event-format__left">
				<?php if ($bio_section_title !== ''): ?>
					<h2 class="event-format__title"><?php echo esc_html($bio_section_title); ?></h2>
				<?php endif; ?>
				<div class="event-format__text"><?php echo $bio; ?></div>
				<div class="btn_readmore event-format__link">
					<span class="open active">Подробнее</span>
					<span class="close">Скрыть</span>
					<svg xmlns="http://www.w3.org/2000/svg" width="19" height="9" viewBox="0 0 19 9" fill="none">
						<path d="M0.635742 0.771973L9.13574 7.77197L17.6357 0.771973" stroke="#EEEDDE" stroke-opacity="0.7" stroke-width="2" stroke-linejoin="round"></path>
					</svg>
				</div>
			</div>
			<div class="event-format__right">
				<?php if ($dob !== '' && $dob_label !== ''): ?>
					<div class="event-format__bubble">
						<div class="title_block"><?php echo $dob; ?></div>
						<div class="description_block"><?php echo $dob_label; ?></div>
					</div>
				<?php endif; ?>
				<?php if ($patronymic !== '' && $patronymic_label !== ''): ?>
					<div class="event-format__bubble">
						<div class="title_block"><?php echo $patronymic; ?></div>
						<div class="description_block"><?php echo $patronymic_label; ?></div>
					</div>
				<?php endif; ?>
				<?php if ($birthplace !== '' && $birthplace_label !== ''): ?>
					<div class="event-format__bubble">
						<div class="title_block"><?php echo $birthplace; ?></div>
						<div class="description_block"><?php echo $birthplace_label; ?></div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if (!empty($upcoming_events)): ?>
	<section class="concert_filter">
		<div class="container">
			<?php if ($upcoming_events_title !== ''): ?>
				<h2><?php echo esc_html($upcoming_events_title); ?></h2>
			<?php endif; ?>
		</div>
		<div class="concert">
			<div class="container">
				<div class="concert_list">
					<?php foreach ($upcoming_events as $event):
						$schedule = get_field('schedule', $event->ID) ?: [];
						usort($schedule, fn($a, $b) => strcmp(
							standup_time_sort_key((string) ($a['time'] ?? '')),
							standup_time_sort_key((string) ($b['time'] ?? ''))
						));
						$stage_id = (int) get_field('stage', $event->ID);
						$stage    = $stage_id ? get_post($stage_id) : null;
						$format_terms = get_the_terms($event->ID, 'format');
						$format_name  = (!empty($format_terms) && !is_wp_error($format_terms)) ? $format_terms[0]->name : '';
						$thumb_id     = get_post_thumbnail_id($event->ID);
						$thumb_url    = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';
						$hover_id     = (int) get_field('thumbnail_hover', $event->ID);
						$hover_url    = $hover_id ? wp_get_attachment_image_url($hover_id, 'large') : '';

						$times_str = implode(' / ', array_filter(array_map(fn($s) => $s['time'] ?? '', $schedule)));
						$count = count($schedule);
						$count_class = $count > 0 ? ($count < 2 ? 10 : (int) floor($count / 2) * 10) : 0;
						?>
						<div class="concert_item<?php echo $hover_url ? '' : ' concert_item--no-hover'; ?>">
							<div class="images">
								<img src="<?php echo esc_url($thumb_url); ?>" alt="" class="image_1">
								<?php if ($hover_url): ?>
									<img src="<?php echo esc_url($hover_url); ?>" alt="" class="image_2">
								<?php endif; ?>
							</div>
							<div class="bottom_image">
								<span class="time"><?php echo esc_html($times_str); ?></span>
								<span class="position"><?php echo $stage ? esc_html($stage->post_title) : ''; ?></span>
							</div>
							<h3 class="concert_title"><?php echo esc_html($format_name); ?><?php if ($event->post_title): ?><span><?php echo esc_html($event->post_title); ?></span><?php endif; ?></h3>
							<div class="controls bottom<?php echo (int) $count_class; ?>">
								<div class="buy_block">
									<div class="btn buy_tickerts"><span>Купить билет</span></div>
									<div class="btn buy_tickerts_hide"><span>Скрыть время</span></div>
									<div class="buy_tickerts_info_time">
										<?php foreach ($schedule as $row): ?>
											<div class="btn tickets_list">
												<a href="<?php echo esc_url($row['ticket_url'] ?? '#'); ?>" class="ticket_time"><?php echo esc_html($row['time'] ?? ''); ?></a>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
								<div class="btn info_btn" data-event-id="<?php echo (int) $event->ID; ?>"><span>Инфо</span></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="concert_modal" id="concertModal">
				<div class="concert_modal__overlay"></div>
				<div class="concert_modal__content"></div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/videos'); ?>

<?php get_template_part('template-parts/blocks/formats'); ?>

<?php get_template_part('template-parts/blocks/social-cta'); ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>

<?php get_footer();
