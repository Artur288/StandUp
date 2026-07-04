<?php
$event_id = $args['event_id'] ?? 0;
if (!$event_id) return;

$title    = get_the_title($event_id);
$ymd      = (string) get_field('event_date', $event_id);
$gallery  = get_field('gallery', $event_id) ?: [];
$desc     = (string) get_field('popup_description', $event_id);
$price    = get_field('price_from', $event_id);
$schedule = get_field('schedule', $event_id) ?: [];

$ticket = '';
foreach ($schedule as $row) {
	$t = (string) ($row['ticket_url'] ?? '');
	if ($t !== '') { $ticket = $t; break; }
}

$stage_id = (int) get_field('stage', $event_id);
$metro    = $stage_id ? (string) get_field('metro', $stage_id) : '';
$address  = $stage_id ? (string) get_field('address', $stage_id) : '';
$meta     = trim($metro . ($metro !== '' && $address !== '' ? ' / ' : '') . $address);

$date_label = '';
if (preg_match('~^(\d{4})(\d{2})(\d{2})$~', $ymd, $m)) {
	$date_label = (int) $m[3] . ' ' . (STANDUP_RU_MONTHS_GENITIVE[(int) $m[2]] ?? '');
}
?>
<div class="stage_info_modal" id="eventInfoModal-<?php echo (int) $event_id; ?>" aria-hidden="true">
	<div class="stage_info_modal__overlay js-stage-info-close" tabindex="-1" aria-label="Закрыть"></div>
	<div class="stage_info_modal__content" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr($title); ?>">
		<button type="button" class="stage_info_modal__close js-stage-info-close" aria-label="Закрыть">
			<svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none">
				<path d="M9.49027 11.2812L32.7614 29.5541" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
				<path d="M8.77689 29.5488L32.048 11.276" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
			</svg>
		</button>
		<div class="stage_info_modal__header">
			<?php if ($date_label !== ''): ?>
				<div class="stage_info_modal__date"><?php echo esc_html($date_label); ?></div>
			<?php endif; ?>
			<div class="stage_info_modal__title"><?php echo esc_html($title); ?></div>
			<?php if ($meta !== ''): ?>
				<div class="stage_info_modal__meta"><?php echo esc_html($meta); ?></div>
			<?php endif; ?>
		</div>

		<?php if (!empty($gallery)): ?>
			<div class="stage_info_modal__gallery">
				<div class="swiper stage-info-swiper">
					<div class="swiper-wrapper">
						<?php foreach ($gallery as $img_id): ?>
							<div class="swiper-slide">
								<?php echo wp_get_attachment_image($img_id, 'large', false, ['alt' => $title]); ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="stage-info-swiper__edge"></div>
					<button type="button" class="stage-info-swiper__next" aria-label="Дальше">
						<svg xmlns="http://www.w3.org/2000/svg" width="13" height="34" viewBox="0 0 13 34" fill="none">
							<path d="M2 2L11 17L2 32" stroke="#EEEDDE" stroke-width="2" stroke-linejoin="round"/>
						</svg>
					</button>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($desc !== ''): ?>
			<div class="stage_info_modal__desc-wrap">
				<div class="stage_info_modal__desc"><?php echo wp_kses_post($desc); ?></div>
				<div class="btn_readmore event-format__link js-stage-info-more">
					<span class="open active">Подробнее</span>
					<span class="close">Скрыть</span>
					<svg xmlns="http://www.w3.org/2000/svg" width="19" height="9" viewBox="0 0 19 9" fill="none">
						<path d="M0.635742 0.771973L9.13574 7.77197L17.6357 0.771973" stroke="#EEEDDE" stroke-opacity="0.7" stroke-width="2" stroke-linejoin="round"></path>
					</svg>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($price !== '' && $price !== null): ?>
			<a class="btn stage_info_modal__buy"<?php echo $ticket !== '' ? ' href="' . esc_url($ticket) . '" target="_blank" rel="noopener"' : ''; ?>><span>Купить билет от <?php echo esc_html(number_format((int) $price, 0, '', ' ')); ?> руб.</span></a>
		<?php endif; ?>
	</div>
</div>
