<?php
$args         = $args ?? [];
$exclude_id   = isset($args['exclude_term_id']) ? (int) $args['exclude_term_id'] : 0;
$city_term_id = isset($args['city_term_id']) ? (int) $args['city_term_id'] : 0;
$mode         = $args['mode'] ?? 'all';

$featured_ids = array_map('intval', (array) get_field('featured_formats', 'option'));

$term_args = [
	'taxonomy'   => 'format',
	'hide_empty' => false,
];
if ($exclude_id > 0) {
	$term_args['exclude'] = [$exclude_id];
}
if (!empty($featured_ids)) {
	$term_args['include'] = $featured_ids;
	$term_args['orderby'] = 'include';
}
$formats = get_terms($term_args);
if (empty($formats) || is_wp_error($formats)) {
	return;
}
if ($city_term_id > 0) {
	$formats = array_values(array_filter($formats, function ($t) use ($city_term_id) {
		return (int) get_field('city', 'format_' . $t->term_id) === $city_term_id;
	}));
	if (empty($formats)) return;
}

if ($mode === 'recommend') {
	$title       = (string) get_field('recommend_block_title', 'option');
	$description = '';
} else {
	$title       = (string) get_field('formats_block_title', 'option');
	$description = (string) get_field('formats_block_description', 'option');
}
?>
<section class="category_list">
	<div class="container">
		<?php if ($title !== ''): ?>
			<h2><?php echo esc_html($title); ?></h2>
		<?php endif; ?>
		<?php if ($description !== ''): ?>
			<div class="desription_title"><?php echo esc_html($description); ?></div>
		<?php endif; ?>
	</div>
	<div class="container container_swiper">
		<div class="swiper category_swiper">
			<div class="swiper-edge swiper-edge--left"></div>
			<div class="swiper-edge swiper-edge--right"></div>
			<div class="swiper-wrapper">
				<?php foreach ($formats as $format):
					$card_img_id = (int) get_field('card_image', 'format_' . $format->term_id);
					?>
					<div class="swiper-slide">
						<a href="<?php echo esc_url(get_term_link($format)); ?>" class="category_card">
							<div class="category_thumb">
								<?php if ($card_img_id): ?>
									<?php echo wp_get_attachment_image($card_img_id, 'large', false, ['alt' => $format->name]); ?>
								<?php endif; ?>
							</div>
							<div class="category_name">
								<div class="border"></div>
								<div class="name"><?php echo esc_html($format->name); ?></div>
								<div class="border"></div>
							</div>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
		</div>
	</div>
</section>
