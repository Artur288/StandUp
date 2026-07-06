<?php
/*
Template Name: Сертификаты
*/
get_header();

$hero_slides              = get_field('hero_slides') ?: [];
$certificate_title        = (string) get_field('certificate_title');
$certificate_description  = (string) get_field('certificate_description');
$bubbles                  = get_field('bubbles') ?: [];
$hide_bubbles             = (bool) get_field('hide_bubbles');
$purchase_label           = (string) get_field('purchase_label');
$purchase_url             = (string) get_field('purchase_url');
$how_it_works_title       = (string) get_field('how_it_works_title');
$how_it_works_description = (string) get_field('how_it_works_description');
$stats_section_title      = (string) get_field('stats_section_title');
$stats                    = get_field('stats') ?: [];
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

<?php if (!$hide_bubbles): ?>
<section class="event-format">
	<div class="container">
		<div class="event-format__content">
			<div class="event-format__left _max">
				<?php if ($certificate_title !== ''): ?>
					<h2 class="event-format__title"><?php echo esc_html($certificate_title); ?></h2>
				<?php endif; ?>
				<?php if ($certificate_description !== ''): ?>
					<div class="event-format__text"><?php echo $certificate_description; ?></div>
				<?php endif; ?>
				<?php if ($purchase_label !== '' && $purchase_url !== ''): ?>
					<a class="event-format__link event-format__link--desk btn" href="<?php echo esc_url($purchase_url); ?>"><span><?php echo esc_html($purchase_label); ?></span></a>
				<?php endif; ?>
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
			<?php if ($purchase_label !== '' && $purchase_url !== ''): ?>
				<a class="event-format__link event-format__link--mob btn" href="<?php echo esc_url($purchase_url); ?>"><span><?php echo esc_html($purchase_label); ?></span></a>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ($how_it_works_title !== '' || $how_it_works_description !== ''): ?>
	<section class="event-format">
		<div class="container">
			<div class="event-format__content">
				<div class="event-format__left _full">
					<?php if ($how_it_works_title !== ''): ?>
						<h2 class="event-format__title"><?php echo esc_html($how_it_works_title); ?></h2>
					<?php endif; ?>
					<?php if ($how_it_works_description !== ''): ?>
						<div class="event-format__text"><?php echo $how_it_works_description; ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/comedians'); ?>

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

<?php get_template_part('template-parts/blocks/formats'); ?>

<?php get_template_part('template-parts/blocks/social-cta'); ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>

<?php get_footer();
