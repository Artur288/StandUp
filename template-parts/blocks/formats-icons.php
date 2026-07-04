<?php
$args         = $args ?? [];
$city_term_id = isset($args['city_term_id']) ? (int) $args['city_term_id'] : 0;
$title        = $city_term_id > 0
	? (string) get_field('formats_icons_title', 'city_' . $city_term_id)
	: (string) get_field('formats_icons_title');

$featured_ids = array_map('intval', (array) get_field('featured_formats', 'option'));
$term_args    = ['taxonomy' => 'format', 'hide_empty' => false];
if (!empty($featured_ids)) {
	$term_args['include'] = $featured_ids;
	$term_args['orderby'] = 'include';
}
$terms = get_terms($term_args);
if (empty($terms) || is_wp_error($terms)) {
	return;
}
$items = [];
foreach ($terms as $term) {
	if ($city_term_id > 0 && (int) get_field('city', 'format_' . $term->term_id) !== $city_term_id) {
		continue;
	}
	$icon_id = (int) get_field('icon', 'format_' . $term->term_id);
	if ($icon_id) {
		$items[] = ['term' => $term, 'icon_id' => $icon_id];
	}
}
if (empty($items)) {
	return;
}
?>
<section class="formats-icons">
	<div class="container">
		<?php if ($title !== ''): ?>
			<h2><?php echo esc_html($title); ?></h2>
		<?php endif; ?>
	</div>
	<div class="container container_swiper">
		<div class="swiper formats_icons_swiper">
			<div class="swiper-edge swiper-edge--left"></div>
			<div class="swiper-edge swiper-edge--right"></div>
			<div class="swiper-wrapper">
				<?php foreach ($items as $item): ?>
					<div class="swiper-slide">
						<a href="<?php echo esc_url(get_term_link($item['term'])); ?>" class="formats-icons__item">
							<div class="formats-icons__photo">
								<?php echo wp_get_attachment_image($item['icon_id'], 'medium', false, ['alt' => $item['term']->name]); ?>
							</div>
							<div class="formats-icons__name"><?php echo esc_html($item['term']->name); ?></div>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
