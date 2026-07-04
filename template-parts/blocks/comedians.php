<?php
$args = $args ?? [];
$comedian_ids = $args['comedian_ids'] ?? null;
$city_term_id = isset($args['city_term_id']) ? (int) $args['city_term_id'] : 0;
if (is_array($comedian_ids) && empty($comedian_ids)) {
	return;
}
$query_args = [
	'post_type'      => 'comedian',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
];
if (is_array($comedian_ids)) {
	$query_args['post__in'] = $comedian_ids;
	$query_args['orderby']  = 'post__in';
}
if ($city_term_id > 0) {
	$query_args['tax_query'] = [
		['taxonomy' => 'city', 'field' => 'term_id', 'terms' => $city_term_id],
	];
}
$comedians = get_posts($query_args);

if (empty($comedians)) {
	return;
}

$title_raw       = $args['title_override']       ?? '';
$description_raw = $args['description_override'] ?? '';
$title       = $title_raw       !== '' ? (string) $title_raw       : (string) get_field('comedians_block_title', 'option');
$description = $description_raw !== '' ? (string) $description_raw : (string) get_field('comedians_block_description', 'option');
$cta_label   = (string) ($args['cta_label_override']   ?? get_field('comedians_block_cta_label', 'option'));
$show_cta    = $args['show_cta'] ?? true;
$archive_url = get_post_type_archive_link('comedian');
?>
<section class="comic_list">
	<div class="container">
		<?php if ($title !== ''): ?>
			<h2><?php echo esc_html($title); ?></h2>
		<?php endif; ?>
		<div class="top">
			<?php if ($description !== ''): ?>
				<div class="desription_title"><?php echo esc_html($description); ?></div>
			<?php endif; ?>
			<?php if ($show_cta && $archive_url && $cta_label !== ''): ?>
				<div class="btn"><a href="<?php echo esc_url($archive_url); ?>" class="btn_all_commic"><?php echo esc_html($cta_label); ?></a></div>
			<?php endif; ?>
		</div>
	</div>
	<div class="container container_swiper">
		<div class="swiper comic_swiper">
			<div class="swiper-edge swiper-edge--left"></div>
			<div class="swiper-edge swiper-edge--right"></div>
			<div class="swiper-wrapper">
				<?php foreach ($comedians as $comedian):
					$name_parts = explode(' ', $comedian->post_title, 2);
					?>
					<div class="swiper-slide">
						<div class="comedian_card">
							<a href="<?php echo esc_url(get_permalink($comedian)); ?>">
								<div class="comedian_thumb">
									<?php echo get_the_post_thumbnail($comedian, 'medium', ['alt' => $comedian->post_title]); ?>
								</div>
								<div class="comedian_name"><?php echo esc_html($name_parts[0]); ?><?php if (isset($name_parts[1])): ?><br><?php echo esc_html($name_parts[1]); ?><?php endif; ?></div>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
		</div>
	</div>
</section>
