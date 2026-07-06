<?php
/*
Template Name: Стать спонсором
*/
get_header();

$hero_slides         = get_field('hero_slides') ?: [];
$stats_section_title = (string) get_field('stats_section_title');
$stats               = get_field('stats') ?: [];
$about_tabs_title    = (string) get_field('about_tabs_title');
$about_tab_descr     = (string) get_field('description');
$about_tab_bubbles   = get_field('bubbles') ?: [];
$hide_bubbles        = (bool) get_field('hide_bubbles');
$photo_text_title    = (string) get_field('photo_text_title');
$photo_text_rows     = get_field('photo_text_rows') ?: [];
$faq_title           = (string) get_field('faq_title');
$faq_description     = (string) get_field('faq_description');
$faq_form_title      = (string) get_field('faq_form_title');
$faq_form_description = (string) get_field('faq_form_description');

$opt_phone     = (string) get_field('phone', 'option');
$opt_email     = (string) get_field('email', 'option');
$opt_telegram  = (string) get_field('telegram_url', 'option');
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

<?php if (!empty($stats) || $stats_section_title !== ''): ?>
	<section class="statistics sponsor-statistics">
		<div class="container">
			<?php if ($stats_section_title !== ''): ?>
				<h2><?php echo nl2br(esc_html($stats_section_title)); ?></h2>
			<?php endif; ?>
			<?php if (!empty($stats)): ?>
				<div class="statistics_list">
					<div class="statistics_block">
						<?php foreach ($stats as $stat): ?>
							<div class="stat_item">
								<div class="number"><?php echo esc_html($stat['number'] ?? ''); ?></div>
								<div class="info"><?php echo wp_kses_post($stat['label'] ?? ''); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ($about_tab_descr !== '' && !$hide_bubbles): ?>
	<section class="concert">
		<div class="container">
			<div class="about_description__panels">
				<div class="about_description__panel active">
					<div class="event-format sponsor-format">
						<div class="event-format__content sponsor-content">
							<div class="event-format__left">
								<?php if ($about_tabs_title !== ''): ?>
									<h2 class="event-format__title"><?php echo esc_html($about_tabs_title); ?></h2>
								<?php endif; ?>
								<?php if ($about_tab_descr !== ''): ?>
									<div class="event-format__text active"><?php echo $about_tab_descr; ?></div>
								<?php endif; ?>
							</div>
							<?php if (!empty($about_tab_bubbles)): ?>
								<div class="event-format__right">
									<?php foreach ($about_tab_bubbles as $bubble): ?>
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
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (!empty($photo_text_rows) || $photo_text_title !== ''): ?>
	<section class="about_photo_text">
		<div class="container">
			<?php if ($photo_text_title !== ''): ?>
				<h2><?php echo esc_html($photo_text_title); ?></h2>
			<?php endif; ?>
			<?php foreach ($photo_text_rows as $i => $row):
				$image_id = (int) ($row['image'] ?? 0);
				$reverse = $i % 2 === 1;
				?>
				<div class="sponsor-row about_photo_text__row<?php echo $reverse ? ' reverse' : ''; ?>">
					<div class="about_photo_text__image">
						<?php if ($image_id): ?>
							<?php echo wp_get_attachment_image($image_id, 'large', false, ['alt' => '']); ?>
						<?php endif; ?>
					</div>
					<div class="about_photo_text__info">
						<?php if (!empty($row['description'])): ?>
							<p><?php echo esc_html($row['description']); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>

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
				<?php get_template_part('template-parts/forms/faq', null, ['form_type' => 'sponsor']); ?>
			</div>
		</div>
	</div>
</section>

<?php get_template_part('template-parts/blocks/comedians'); ?>

<?php get_template_part('template-parts/blocks/videos'); ?>

<?php get_template_part('template-parts/blocks/formats'); ?>

<?php get_template_part('template-parts/blocks/social-cta'); ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>

<?php get_footer();
