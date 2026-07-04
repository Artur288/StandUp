<?php
/*
Template Name: Корпоративы
*/
get_header();

$hero_slides            = get_field('hero_slides') ?: [];
$intro_title            = (string) get_field('intro_title');
$intro_description      = (string) get_field('intro_description');
$hero_bubbles           = get_field('hero_bubbles') ?: [];
$corporate_formats_title = (string) get_field('corporate_formats_title');
$corporate_formats      = get_field('corporate_formats') ?: [];
$comedians_section_title = (string) get_field('comedians_section_title');
$comedian_sections      = get_field('comedian_sections') ?: [];
$faq_title              = (string) get_field('faq_title');
$faq_description        = (string) get_field('faq_description');
$faq_form_title         = (string) get_field('faq_form_title');
$faq_form_description   = (string) get_field('faq_form_description');

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

<?php if ($intro_title !== '' || $intro_description !== '' || !empty($hero_bubbles)): ?>
	<section class="event-format">
		<div class="container">
			<div class="event-format__content">
				<div class="event-format__left _max">
					<?php if ($intro_title !== ''): ?>
						<h2 class="event-format__title"><?php echo esc_html($intro_title); ?></h2>
					<?php endif; ?>
					<?php if ($intro_description !== ''): ?>
						<div class="event-format__text"><?php echo $intro_description; ?></div>
						<div class="btn_readmore event-format__link">
							<span class="open active">Подробнее</span>
							<span class="close">Скрыть</span>
							<svg xmlns="http://www.w3.org/2000/svg" width="19" height="9" viewBox="0 0 19 9" fill="none">
								<path d="M0.635742 0.771973L9.13574 7.77197L17.6357 0.771973" stroke="#EEEDDE" stroke-opacity="0.7" stroke-width="2" stroke-linejoin="round"></path>
							</svg>
						</div>
					<?php endif; ?>
				</div>
				<?php if (!empty($hero_bubbles)): ?>
					<div class="event-format__right">
						<?php foreach ($hero_bubbles as $bubble): ?>
							<div class="event-format__bubble">
								<div class="title_block"><?php echo esc_html($bubble['title'] ?? ''); ?></div>
								<div class="description_block"><?php echo esc_html($bubble['description'] ?? ''); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (!empty($corporate_formats)): ?>
	<section class="corporate-holiday-format" aria-labelledby="corporate-holiday-format-title">
		<div class="container">
			<?php if ($corporate_formats_title !== ''): ?>
				<h2 id="corporate-holiday-format-title" class="corporate-holiday-format__title"><?php echo esc_html($corporate_formats_title); ?></h2>
			<?php endif; ?>
			<div class="corporate-holiday-format__tabs-bar">
				<div class="corporate-holiday-format__tabs" role="tablist" aria-label="Форматы мероприятия">
					<?php foreach ($corporate_formats as $i => $format): ?>
						<button type="button" class="corporate-holiday-format__tab<?php echo $i === 0 ? ' is-active' : ''; ?>" role="tab" id="tab-format-<?php echo (int) $i; ?>" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>" aria-controls="panel-format-<?php echo (int) $i; ?>" data-format-tab="<?php echo (int) $i; ?>"><?php echo esc_html($format['tab_title'] ?? ''); ?></button>
					<?php endforeach; ?>
				</div>
			</div>

			<?php foreach ($corporate_formats as $i => $format):
				$image_id  = (int) ($format['image'] ?? 0);
				$cta_label = (string) ($format['cta_label'] ?? '');
				$arrow_svg = '<span class="corporate-holiday-format__cta-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="18" viewBox="0 0 8 18" fill="none"><path d="M1 1L7 9L1 17" stroke="#EEEDDE" stroke-width="2" stroke-linejoin="round"/></svg></span>';
				?>
				<div class="corporate-holiday-format__panel<?php echo $i === 0 ? ' is-active' : ''; ?>" role="tabpanel" id="panel-format-<?php echo (int) $i; ?>" aria-labelledby="tab-format-<?php echo (int) $i; ?>" data-format-panel="<?php echo (int) $i; ?>">
					<?php if ($cta_label !== ''): ?>
						<button type="button" class="corporate-holiday-format__cta corporate-holiday-format__cta--mobile js-order-event-open">
							<span class="corporate-holiday-format__cta-text"><?php echo esc_html($cta_label); ?></span>
							<?php echo $arrow_svg; ?>
						</button>
					<?php endif; ?>
					<div class="corporate-holiday-format__media">
						<?php if ($image_id): ?>
							<?php echo wp_get_attachment_image($image_id, 'large', false, ['alt' => $format['tab_title'] ?? '', 'loading' => $i === 0 ? 'eager' : 'lazy']); ?>
						<?php endif; ?>
					</div>
					<div class="corporate-holiday-format__body">
						<?php if (!empty($format['tab_title'])): ?>
							<h3 class="corporate-holiday-format__panel-title"><?php echo esc_html($format['tab_title']); ?></h3>
						<?php endif; ?>
						<?php if (!empty($format['description'])): ?>
							<div class="corporate-holiday-format__text"><?php echo $format['description']; ?></div>
						<?php endif; ?>
						<?php if ($cta_label !== ''): ?>
							<button type="button" class="corporate-holiday-format__cta corporate-holiday-format__cta--desktop js-order-event-open">
								<span class="corporate-holiday-format__cta-text"><?php echo esc_html($cta_label); ?></span>
								<?php echo $arrow_svg; ?>
							</button>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>

<?php if (!empty($comedian_sections)): ?>
	<section class="comedians">
		<div class="container">
			<?php if ($comedians_section_title !== ''): ?>
				<h2><?php echo esc_html($comedians_section_title); ?></h2>
			<?php endif; ?>
			<div class="comedians-list">
				<?php foreach ($comedian_sections as $section):
					$section_label = (string) ($section['section_title'] ?? '');
					$comedian_ids  = $section['comedians'] ?? [];
					if (empty($comedian_ids)) continue;
					$comedians = get_posts([
						'post_type'      => 'comedian',
						'post__in'       => $comedian_ids,
						'orderby'        => 'post__in',
						'posts_per_page' => -1,
						'post_status'    => 'publish',
					]);
					if (empty($comedians)) continue;
					?>
					<section class="comic_list">
						<div class="container">
							<div class="top">
								<?php if ($section_label !== ''): ?>
									<div class="desription_title"><?php echo esc_html($section_label); ?></div>
								<?php endif; ?>
							</div>
						</div>
						<div class="container container_swiper">
							<div class="swiper comic_swiper">
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
				<?php endforeach; ?>
			</div>
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
				<?php get_template_part('template-parts/forms/faq', null, ['form_type' => 'corporate']); ?>
			</div>
		</div>
	</div>
</section>

<?php get_template_part('template-parts/blocks/videos'); ?>

<?php get_template_part('template-parts/blocks/formats'); ?>

<?php get_template_part('template-parts/blocks/social-cta'); ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>

<?php get_footer();
