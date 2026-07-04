<?php
get_header();

$hero_slides       = get_field('stages_archive_hero', 'option') ?: [];
$section_title     = (string) get_field('stages_archive_title', 'option');
$section_descr     = (string) get_field('stages_archive_description', 'option');
$cities            = get_terms(['taxonomy' => 'city', 'hide_empty' => true]);

$all_stages = get_posts([
	'post_type'      => 'stage',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
]);
?>

<?php if (!empty($hero_slides)): ?>
	<section class="home_slider">
		<div class="swiper homeSwiper">
			<div class="swiper-wrapper">
				<?php foreach ($hero_slides as $slide):
					$desktop_id = (int) ($slide['image_desktop'] ?? 0);
					$mobile_id  = (int) ($slide['image_mobile'] ?? 0);
					$link       = (string) ($slide['link'] ?? '');
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

<?php if (!empty($cities) && !is_wp_error($cities)): ?>
	<section class="stages">
		<div class="container">
			<div class="top">
				<?php if ($section_title !== ''): ?>
					<h2><?php echo esc_html($section_title); ?></h2>
				<?php endif; ?>
				<?php
				$valid_city_slugs = array_map(fn($c) => $c->slug, $cities);
				$active_city_slug = standup_resolve_active_city_slug($valid_city_slugs);
				?>
				<ul class="cities-list is-tabs js-city-filter">
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
			</div>
			<?php if ($section_descr !== ''): ?>
				<div class="description">
					<p><?php echo esc_html($section_descr); ?></p>
				</div>
			<?php endif; ?>
			<div class="stages_list">
				<?php foreach ($all_stages as $stage):
					$stage_cities = get_the_terms($stage->ID, 'city');
					if (empty($stage_cities) || is_wp_error($stage_cities)) continue;
					$city_slugs = implode(',', wp_list_pluck($stage_cities, 'slug'));

					$thumb_id    = get_post_thumbnail_id($stage->ID);
					$hover_id    = (int) get_field('thumbnail_hover', $stage->ID);
					$description = trim(wp_strip_all_tags($stage->post_content));
					$metro       = (string) get_field('metro', $stage->ID);
					$address     = (string) get_field('address', $stage->ID);
					$map_url     = (string) get_field('map_url', $stage->ID);
					$info_url    = (string) get_field('info_url', $stage->ID);
					?>
					<div class="stages_item<?php echo $hover_id ? '' : ' stages_item--no-hover'; ?>" data-city="<?php echo esc_attr($city_slugs); ?>">
						<div class="images">
							<?php if ($thumb_id): ?>
								<?php echo wp_get_attachment_image($thumb_id, 'large', false, ['alt' => $stage->post_title, 'class' => 'image_1']); ?>
							<?php endif; ?>
							<?php if ($hover_id): ?>
								<?php echo wp_get_attachment_image($hover_id, 'large', false, ['alt' => $stage->post_title, 'class' => 'image_2']); ?>
							<?php endif; ?>
						</div>
						<div class="bottom_image">
							<?php if ($metro !== ''): ?>
								<span class="town"><?php echo esc_html($metro); ?></span>
							<?php endif; ?>
							<?php if ($address !== ''): ?>
								<span class="address"><?php echo esc_html($address); ?></span>
							<?php endif; ?>
						</div>
						<h3 class="stage_title">
							<?php echo esc_html($stage->post_title); ?>
							<?php if ($description !== ''): ?>
								<span><?php echo esc_html($description); ?></span>
							<?php endif; ?>
						</h3>
						<div class="controls">
							<?php if ($map_url !== ''): ?>
								<a href="<?php echo esc_url($map_url); ?>" class="btn btn_map" target="_blank" rel="noopener"><span>Показать на карте</span></a>
							<?php endif; ?>
							<a href="#" class="btn btn_map js-stage-info-open" data-target="stageInfoModal-<?php echo $stage->ID; ?>"><span>Инфо</span></a>
						</div>
					</div>
					<?php get_template_part('template-parts/modals/stage-info', null, ['stage_id' => $stage->ID]); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/videos'); ?>

<?php get_template_part('template-parts/blocks/comedians'); ?>

<?php get_template_part('template-parts/blocks/formats'); ?>

<?php get_template_part('template-parts/blocks/social-cta'); ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>

<?php get_footer();
