<?php
$args     = $args ?? [];
$event_id = (int) ($args['event_id'] ?? 0);
if (!$event_id) {
	return;
}
$event = get_post($event_id);
if (!$event || $event->post_type !== 'event') {
	return;
}

$event_date = (string) get_field('event_date', $event_id);
$schedule   = get_field('schedule', $event_id) ?: [];
usort($schedule, fn($a, $b) => strcmp(
	standup_time_sort_key((string) ($a['time'] ?? '')),
	standup_time_sort_key((string) ($b['time'] ?? ''))
));
$lineup     = get_field('lineup', $event_id) ?: [];
$stage_id    = (int) get_field('stage', $event_id);
$stage       = $stage_id ? get_post($stage_id) : null;
$stage_addr  = $stage_id ? (string) get_field('address', $stage_id) : '';
$stage_metro = $stage_id ? (string) get_field('metro', $stage_id) : '';
$address_line = trim(implode(' / ', array_filter([$stage_metro, $stage_addr])));

$format_terms   = get_the_terms($event_id, 'format');
$format         = (!empty($format_terms) && !is_wp_error($format_terms)) ? $format_terms[0] : null;
$format_term_id = $format ? (int) $format->term_id : 0;
$format_bubbles = $format && !get_field('hide_bubbles', 'format_' . $format->term_id) ? (get_field('bubbles', 'format_' . $format->term_id) ?: []) : [];
$format_gallery = $format ? (get_field('gallery', 'format_' . $format->term_id) ?: []) : [];

$date_long       = $event_date ? standup_format_event_date_long($event_date) : '';
$schedule_times  = array_values(array_filter(array_map(fn($s) => $s['time'] ?? '', $schedule)));
$first_ticket    = $schedule[0]['ticket_url'] ?? '';
$description     = ($format && $format->description !== '') ? wpautop(wp_kses_post($format->description)) : '';

// бесплатное мероприятие: своя шапка (дата/время/сбор гостей/18+) и бабблы вместо обычных
$is_free_event      = $format_term_id ? (bool) get_field('is_free_event', 'format_' . $format_term_id) : false;
$free_event_header  = '';
$free_event_bubbles = [];
if ($is_free_event) {
	$free_event_header = standup_free_event_header($event_id, $format_term_id);

	$is_18_plus = standup_event_is_18_plus($event_id, $format_term_id);
	$has_mat    = standup_event_has_mat($event_id, $format_term_id);
	$duration   = standup_event_duration($event_id, $format_term_id);

	if ($duration !== '') $free_event_bubbles[] = ['bubble_title' => $duration, 'bubble_description' => 'продолжительность'];
	if ($is_18_plus)      $free_event_bubbles[] = ['bubble_title' => '18+', 'bubble_description' => 'возрастное ограничение'];
	if ($has_mat)         $free_event_bubbles[] = ['bubble_title' => 'Мат', 'bubble_description' => 'может присутствовать'];
}
$info_bubbles = $is_free_event ? $free_event_bubbles : $format_bubbles;
?>
<svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none" class="concert_modal__close">
	<path d="M9.49027 11.2812L32.7614 29.5541" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
	<path d="M8.77689 29.5488L32.048 11.276" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
</svg>

<?php if ($is_free_event): ?>
	<?php if ($free_event_header !== ''): ?>
		<div class="date"><?php echo esc_html($free_event_header); ?></div>
	<?php endif; ?>
<?php elseif ($date_long): ?>
	<div class="date"><?php echo esc_html($date_long); ?></div>
<?php endif; ?>

<?php if (!$is_free_event && !empty($lineup)): ?>
	<div class="event_comics">
		<div class="title">Состав:</div>
		<div class="swiper event_comics_list">
			<div class="swiper-wrapper">
				<?php foreach ($lineup as $comedian_id):
					$comedian = get_post($comedian_id);
					if (!$comedian) {
						continue;
					}
					$thumb_id   = get_post_thumbnail_id($comedian_id);
					$hover_id   = (int) get_field('thumbnail_hover', $comedian_id);
					$name_parts = explode(' ', $comedian->post_title, 2);
					$tagline    = (string) get_field('tagline', $comedian_id);
					?>
					<div class="swiper-slide">
						<div class="event_comic">
							<a href="<?php echo esc_url(get_permalink($comedian)); ?>">
								<div class="comedian_thumb<?php echo $hover_id ? ' hover_event' : ''; ?>">
									<?php echo wp_get_attachment_image($thumb_id, 'medium', false, ['alt' => $comedian->post_title]); ?>
								</div>
								<div class="name"><?php echo esc_html($name_parts[0]); ?><?php if (isset($name_parts[1])): ?><br><?php echo esc_html($name_parts[1]); ?><?php endif; ?></div>
								<?php if ($tagline !== ''): ?>
									<div class="description"><?php echo esc_html($tagline); ?></div>
								<?php endif; ?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<div class="info_by_event">
	<div class="title">О формате:</div>
	<div class="groups">
		<div class="info_list">
			<?php foreach ($info_bubbles as $bubble):
				$btitle = (string) ($bubble['bubble_title'] ?? '');
				$bdescr = (string) ($bubble['bubble_description'] ?? '');
				if ($btitle === '' && $bdescr === '') continue;
				?>
				<div class="item">
					<?php if ($btitle !== ''): ?>
						<div class="title_block"><?php echo esc_html($btitle); ?></div>
					<?php endif; ?>
					<?php if ($bdescr !== ''): ?>
						<div class="description_block"><?php echo esc_html($bdescr); ?></div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		$modal_image_id = (int) get_field('modal_image', $event_id) ?: get_post_thumbnail_id($event_id);
		if ($modal_image_id) {
			echo wp_get_attachment_image($modal_image_id, 'large', false, ['alt' => $event->post_title]);
		}
		?>
	</div>
	<?php if ($description !== ''): ?>
		<div class="description_event"><?php echo $description; ?></div>
		<div class="btn_readmore">
			<span class="open">Подробнее</span><span class="close">Скрыть</span>
			<svg xmlns="http://www.w3.org/2000/svg" width="19" height="9" viewBox="0 0 19 9" fill="none">
				<path d="M0.635742 0.771973L9.13574 7.77197L17.6357 0.771973" stroke="#EEEDDE" stroke-opacity="0.7" stroke-width="2" stroke-linejoin="round"/>
			</svg>
		</div>
	<?php endif; ?>
</div>

<?php if ($stage): ?>
	<div class="event_address">
		<div class="title"><?php echo esc_html($stage->post_title); ?></div>
		<?php if ($address_line !== ''): ?>
			<div class="address"><?php echo esc_html($address_line); ?></div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if (!empty($format_gallery)): ?>
	<div class="gallery_block">
		<div class="swiper gallery_list">
			<div class="swiper-wrapper">
				<?php foreach ($format_gallery as $image_id): ?>
					<div class="swiper-slide"><?php echo wp_get_attachment_image($image_id, 'large'); ?></div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php if ($is_free_event): ?>
	<div class="btn js-free-event-book" data-target="freeEventBookingModal-<?php echo (int) $event_id; ?>"><span>Бронировать</span></div>
<?php elseif ($first_ticket !== ''): ?>
	<div class="btn">
		<a href="<?php echo esc_url($first_ticket); ?>" target="_blank" rel="noopener">Купить билет</a>
	</div>
<?php endif; ?>
