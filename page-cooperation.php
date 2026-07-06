<?php
/*
Template Name: Сотрудничество
*/
get_header();

$hero_slides           = get_field('hero_slides') ?: [];
$tabs_title            = (string) get_field('cooperation_tabs_title');
$tabs                  = get_field('cooperation_tabs') ?: [];
$partner_title         = (string) get_field('partner_stages_title');
$partner_description   = (string) get_field('partner_stages_description');
$partner_link_label    = (string) get_field('partner_stages_link_label');
$faq_title             = (string) get_field('faq_title');
$faq_description       = (string) get_field('faq_description');
$faq_form_title        = (string) get_field('faq_form_title');
$faq_form_description  = (string) get_field('faq_form_description');

$opt_phone     = (string) get_field('phone', 'option');
$opt_email     = (string) get_field('email', 'option');
$opt_telegram  = (string) get_field('telegram_url', 'option');

$partner_stages = get_posts([
	'post_type'      => 'stage',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
]);
$stages_archive_url = get_post_type_archive_link('stage');
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

<?php if (!empty($tabs)): ?>
	<section class="concert about_description">
		<div class="top">
			<div class="container">
				<div class="row1">
					<?php if ($tabs_title !== ''): ?>
						<h2><?php echo esc_html($tabs_title); ?></h2>
					<?php endif; ?>
					<ul class="cities-list is-tabs about_description__tabs">
						<?php $is_first_tab = true; foreach ($tabs as $i => $tab):
							if (!empty($tab['hide_bubbles'])) continue;
							?>
							<li data-tab="<?php echo (int) $i; ?>"<?php echo $is_first_tab ? ' class="active"' : ''; ?>><span><?php echo esc_html($tab['tab_title'] ?? ''); ?></span></li>
							<?php $is_first_tab = false; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="about_description__panels">
				<?php $is_first_panel = true; foreach ($tabs as $i => $tab):
					if (!empty($tab['hide_bubbles'])) continue;
					$tab_bubbles = $tab['bubbles'] ?? [];
					?>
					<div class="about_description__panel<?php echo $is_first_panel ? ' active' : ''; ?>" data-tab-panel="<?php echo (int) $i; ?>">
						<div class="event-format">
							<div class="event-format__content">
								<div class="event-format__left">
									<?php if (!empty($tab['tab_title'])): ?>
										<h3 class="event-format__title"><?php echo esc_html($tab['tab_title']); ?></h3>
									<?php endif; ?>
									<?php if (!empty($tab['description'])): ?>
										<div class="event-format__text active"><?php echo $tab['description']; ?></div>
									<?php endif; ?>
								</div>
								<?php if (!empty($tab_bubbles)): ?>
									<div class="event-format__right">
										<?php foreach ($tab_bubbles as $bubble): ?>
											<div class="event-format__bubble">
												<div class="title_block"><?php echo esc_html($bubble['bubble_title'] ?? ''); ?></div>
												<div class="description_block"><?php echo esc_html($bubble['bubble_description'] ?? ''); ?></div>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php $is_first_panel = false; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (!empty($partner_stages)): ?>
	<section class="stages_rowlist">
		<div class="container">
			<div class="stages_rowlist__head">
				<div class="stages_rowlist__head-left">
					<?php if ($partner_title !== ''): ?>
						<h2><?php echo esc_html($partner_title); ?></h2>
					<?php endif; ?>
					<?php if ($partner_description !== ''): ?>
						<div class="desription_title"><?php echo nl2br(esc_html($partner_description)); ?></div>
					<?php endif; ?>
				</div>
				<?php if ($stages_archive_url && $partner_link_label !== ''): ?>
					<a class="stages_rowlist__all stages_rowlist__all--desktop" href="<?php echo esc_url($stages_archive_url); ?>">
						<span><?php echo esc_html($partner_link_label); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="container container_swiper">
			<div class="swiper stages_rowlist_swiper">
				<div class="swiper-edge swiper-edge--left swiper-edge--stages"></div>
				<div class="swiper-edge swiper-edge--right swiper-edge--stages"></div>
				<div class="swiper-wrapper">
					<?php foreach ($partner_stages as $stage):
						$thumb_id = get_post_thumbnail_id($stage->ID);
						$metro = (string) get_field('metro', $stage->ID);
						?>
						<div class="swiper-slide">
							<?php if ($stages_archive_url): ?>
								<a href="<?php echo esc_url($stages_archive_url); ?>" class="category_card stages_card">
							<?php else: ?>
								<div class="category_card stages_card">
							<?php endif; ?>
								<div class="category_thumb">
									<?php if ($thumb_id): ?>
										<?php echo wp_get_attachment_image($thumb_id, 'large', false, ['alt' => $stage->post_title]); ?>
									<?php endif; ?>
								</div>
								<div class="category_name">
									<div class="name"><?php echo esc_html($stage->post_title); ?></div>
									<?php if ($metro !== ''): ?>
										<div class="metro"><?php echo esc_html($metro); ?></div>
									<?php endif; ?>
									<div class="border"></div>
								</div>
							<?php if ($stages_archive_url): ?></a><?php else: ?></div><?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php if ($stages_archive_url && $partner_link_label !== ''): ?>
				<div class="container">
					<a class="stages_rowlist__all stages_rowlist__all--mobile" href="<?php echo esc_url($stages_archive_url); ?>">
						<span><?php echo esc_html($partner_link_label); ?></span>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/comedians'); ?>

<section class="about_faq">
	<div class="container">
		<?php if ($faq_title !== ''): ?>
			<h2><?php echo esc_html($faq_title); ?></h2>
		<?php endif; ?>
		<?php if ($faq_description !== ''): ?>
			<p class="about_faq__desc"><?php echo esc_html($faq_description); ?></p>
		<?php endif; ?>
		<?php if ($opt_phone !== '' || $opt_email !== '' || $opt_telegram !== ''): ?>
			<div class="about_faq__contacts">
				<?php if ($opt_phone !== ''): ?>
					<div class="about_faq__contact">
						<span class="about_faq__contact-label">Телефон</span>
						<a href="tel:<?php echo esc_attr(preg_replace('/[^\d+]/', '', $opt_phone)); ?>"><?php echo esc_html($opt_phone); ?></a>
					</div>
				<?php endif; ?>
				<?php if ($opt_email !== ''): ?>
					<div class="about_faq__contact">
						<span class="about_faq__contact-label">E-mail</span>
						<a href="mailto:<?php echo esc_attr($opt_email); ?>"><?php echo esc_html($opt_email); ?></a>
					</div>
				<?php endif; ?>
				<?php if ($opt_telegram !== ''): ?>
					<div class="about_faq__contact">
						<span class="about_faq__contact-label">Telegram</span>
						<a href="<?php echo esc_url($opt_telegram); ?>" target="_blank" rel="noopener"><?php echo esc_html($opt_telegram); ?></a>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="about_faq__grid">
			<div class="about_faq__left">
				<div class="about_faq__card">
					<?php if ($faq_form_title !== ''): ?>
						<h3><?php echo esc_html($faq_form_title); ?></h3>
					<?php endif; ?>
					<?php if ($faq_form_description !== ''): ?>
						<p><?php echo esc_html($faq_form_description); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<div class="about_faq__right">
				<?php get_template_part('template-parts/forms/faq', null, ['form_type' => 'cooperation']); ?>
			</div>
		</div>
	</div>
</section>

<?php get_template_part('template-parts/blocks/videos'); ?>

<?php get_template_part('template-parts/blocks/formats'); ?>

<?php get_template_part('template-parts/blocks/social-cta'); ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>

<?php get_footer();
