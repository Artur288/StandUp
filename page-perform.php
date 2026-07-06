<?php
/*
Template Name: Выступать у нас
*/
get_header();

$hero_slides         = get_field('hero_slides') ?: [];

$steps_title         = (string) get_field('steps_title');
$steps               = get_field('steps') ?: [];

$contacts_title       = (string) get_field('contacts_title');
$contacts_description = (string) get_field('contacts_description');

$comedians_title       = (string) get_field('comedians_title');
$comedians_description = (string) get_field('comedians_description');

$stages_title       = (string) get_field('stages_title');
$stages_description = (string) get_field('stages_description');
$stages_cta_label   = (string) get_field('stages_cta_label');
$stage_ids          = get_field('stages') ?: [];

$faq_title            = (string) get_field('faq_title');
$faq_description       = (string) get_field('faq_description');
$faq_form_title        = (string) get_field('faq_form_title');
$faq_form_description  = (string) get_field('faq_form_description');

$stats_section_title = (string) get_field('stats_section_title');
$stats               = get_field('stats') ?: [];

$opt_phone     = (string) get_field('phone', 'option');
$opt_email     = (string) get_field('email', 'option');
$opt_telegram  = (string) get_field('telegram_url', 'option');

$stages = [];
if (!empty($stage_ids)) {
	$stages = get_posts([
		'post_type'      => 'stage',
		'post__in'       => $stage_ids,
		'orderby'        => 'post__in',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	]);
}
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

<?php if (!empty($steps) || $steps_title !== ''): ?>
	<section class="statistics preform-statistics">
		<div class="container">
			<?php if ($steps_title !== ''): ?>
				<h2><?php echo nl2br(esc_html($steps_title)); ?></h2>
			<?php endif; ?>
			<?php if (!empty($steps)): ?>
				<div class="statistics_list">
					<div class="preform-statistics_block">
						<?php foreach ($steps as $step): ?>
							<div class="stat_item">
								<div class="number"><?php echo esc_html($step['number'] ?? ''); ?></div>
								<div class="info"><?php echo wp_kses_post($step['label'] ?? ''); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ($contacts_title !== '' || $contacts_description !== ''): ?>
	<section class="about_faq perform-faq">
		<div class="container">
			<?php if ($contacts_title !== ''): ?>
				<h2><?php echo esc_html($contacts_title); ?></h2>
			<?php endif; ?>
			<?php if ($contacts_description !== ''): ?>
				<p class="about_faq__desc"><?php echo esc_html($contacts_description); ?></p>
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
		</div>
	</section>
<?php endif; ?>

<?php get_template_part('template-parts/blocks/comedians', null, [
	'title_override'       => $comedians_title,
	'description_override' => $comedians_description,
]); ?>

<?php if (!empty($stages) || $stages_title !== ''): ?>
	<section class="comic_list about_team preform_list">
		<div class="container">
			<?php if ($stages_title !== ''): ?>
				<h2><?php echo esc_html($stages_title); ?></h2>
			<?php endif; ?>
			<div class="top">
				<?php if ($stages_description !== ''): ?>
					<div class="desription_title"><?php echo wp_kses_post($stages_description); ?></div>
				<?php endif; ?>
				<?php if ($stages_cta_label !== '' && $stages_archive_url): ?>
					<div class="btn"><a href="<?php echo esc_url($stages_archive_url); ?>" class="btn_all_commic"><?php echo esc_html($stages_cta_label); ?></a></div>
				<?php endif; ?>
			</div>
		</div>
		<div class="container">
			<div class="swiper about_team_swiper preform-slider">
				<div class="swiper-wrapper">
					<?php foreach ($stages as $stage): ?>
						<div class="swiper-slide">
							<div class="about_team__card">
								<div class="about_team__thumb">
									<?php echo get_the_post_thumbnail($stage, 'medium', ['alt' => $stage->post_title]); ?>
								</div>
								<div class="about_team_content">
									<div class="about_team__name"><?php echo esc_html($stage->post_title); ?></div>
									<div class="about_team__role"><?php echo esc_html((string) get_field('metro', $stage->ID)); ?></div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
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
				<?php get_template_part('template-parts/forms/faq', null, ['form_type' => 'perform']); ?>
			</div>
		</div>
	</div>
</section>

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
