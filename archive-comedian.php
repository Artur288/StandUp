<?php
get_header();

$cities = standup_get_active_comedian_city_terms();

$current_month_num = (int) wp_date('n');
$current_month_name = STANDUP_RU_MONTHS[$current_month_num] ?? '';
$month_start = wp_date('Ymd', mktime(0, 0, 0, $current_month_num, 1, (int) wp_date('Y')));
$month_end   = wp_date('Ymd', mktime(0, 0, 0, $current_month_num + 1, 0, (int) wp_date('Y')));

$events_this_month = get_posts([
	'post_type'      => 'event',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'meta_query'     => [
		'relation' => 'AND',
		['key' => 'event_date', 'value' => $month_start, 'compare' => '>=', 'type' => 'NUMERIC'],
		['key' => 'event_date', 'value' => $month_end,   'compare' => '<=', 'type' => 'NUMERIC'],
	],
]);

$featured_comedian_ids = [];
foreach ($events_this_month as $event) {
	$lineup = get_field('lineup', $event->ID) ?: [];
	foreach ($lineup as $comedian_id) {
		$featured_comedian_ids[(int) $comedian_id] = true;
	}
}
$featured_comedians = !empty($featured_comedian_ids)
	? get_posts([
		'post_type'      => 'comedian',
		'post__in'       => array_keys($featured_comedian_ids),
		'orderby'        => 'post__in',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	])
	: [];

$all_comedians = get_posts([
	'post_type'      => 'comedian',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
]);

$featured_month_title  = (string) get_field('featured_month_title', 'option');
$comedians_block_title = (string) get_field('comedians_block_title', 'option');
$comedians_block_desc  = (string) get_field('comedians_block_description', 'option');

$hero_desktop_id = (int) get_field('comedians_archive_hero_desktop', 'option');
$hero_mobile_id  = (int) get_field('comedians_archive_hero_mobile', 'option');
$hero_desktop_url = $hero_desktop_id ? wp_get_attachment_image_url($hero_desktop_id, 'full') : '';
$hero_mobile_url  = $hero_mobile_id ? wp_get_attachment_image_url($hero_mobile_id, 'full') : '';
?>

<?php if ($hero_desktop_url): ?>
	<section class="home_slider">
		<div class="swiper homeSwiper">
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<picture>
						<?php if ($hero_mobile_url): ?>
							<source media="(max-width: 767px)" srcset="<?php echo esc_url($hero_mobile_url); ?>">
						<?php endif; ?>
						<img src="<?php echo esc_url($hero_desktop_url); ?>" alt="">
					</picture>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (!empty($featured_comedians)): ?>
	<section class="comic_list_current_month" data-city="moskva">
		<div class="container">
			<?php if ($featured_month_title !== ''): ?>
				<h2><?php echo esc_html($featured_month_title); ?></h2>
			<?php endif; ?>
			<?php if ($current_month_name !== ''): ?>
				<div class="current_month"><?php echo esc_html($current_month_name); ?></div>
			<?php endif; ?>
			<div class="swiper swiper-comic_list_current_month">
				<div class="swiper-wrapper">
					<?php foreach ($featured_comedians as $comedian):
						$name_parts = explode(' ', $comedian->post_title, 2);
						?>
						<div class="swiper-slide">
							<div class="comedian_card">
								<div class="comedian_thumb">
									<?php echo get_the_post_thumbnail($comedian, 'medium', ['alt' => $comedian->post_title]); ?>
								</div>
								<div class="comedian_name"><a href="<?php echo esc_url(get_permalink($comedian)); ?>"><?php echo esc_html($name_parts[0]); ?><?php if (isset($name_parts[1])): ?><br><?php echo esc_html($name_parts[1]); ?><?php endif; ?></a></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="comic-fade comic-fade--left"></div>
				<div class="comic-fade comic-fade--right"></div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (!empty($all_comedians)): ?>
	<section class="row_comedian_list">
		<div class="container">
			<div class="top">
				<?php if ($comedians_block_title !== ''): ?>
					<h2><?php echo esc_html($comedians_block_title); ?></h2>
				<?php endif; ?>
				<?php if (!empty($cities) && !is_wp_error($cities)):
					$valid_city_slugs = array_map(fn($c) => $c->slug, $cities);
					$active_city_slug = standup_resolve_active_city_slug($valid_city_slugs);
					?>
					<ul class="cities-list is-tabs js-city-filter" data-filter-scope="page">
						<?php foreach ($cities as $city):
							$is_active = $city->slug === $active_city_slug;
							?>
							<li class="cities-list__item<?php echo $is_active ? ' active' : ''; ?>" data-city="<?php echo esc_attr($city->slug); ?>">
								<?php if ($is_active): ?>
									<span><?php echo esc_html($city->name); ?></span>
								<?php else: ?>
									<a href="<?php echo esc_url(add_query_arg('gorod', $city->slug)); ?>"><?php echo esc_html($city->name); ?></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php if ($comedians_block_desc !== ''): ?>
				<div class="description_title"><?php echo esc_html($comedians_block_desc); ?></div>
			<?php endif; ?>
			<ul class="comedian-list">
				<?php foreach ($all_comedians as $comedian):
					$comedian_cities = standup_get_comedian_city_slugs($comedian->ID);
					if (empty($comedian_cities)) {
						continue;
					}
					$name_parts = explode(' ', $comedian->post_title, 2);
					$city_slugs = implode(',', $comedian_cities);
					?>
					<li class="comedian-item" data-city="<?php echo esc_attr($city_slugs); ?>">
						<a href="<?php echo esc_url(get_permalink($comedian)); ?>">
							<div class="image"><?php echo get_the_post_thumbnail($comedian, 'medium', ['alt' => $comedian->post_title]); ?></div>
							<div class="name"><?php echo esc_html($name_parts[0]); ?><?php if (isset($name_parts[1])): ?><br><?php echo esc_html($name_parts[1]); ?><?php endif; ?></div>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/videos'); ?>

<?php get_template_part('template-parts/blocks/formats'); ?>

<?php get_template_part('template-parts/blocks/social-cta'); ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>

<?php get_footer();
