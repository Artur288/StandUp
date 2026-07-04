<?php
$tg_url = (string) get_field('telegram_url', 'option');
$vk_url = (string) get_field('vk_url', 'option');

if ($tg_url === '' && $vk_url === '') {
	return;
}

$title       = (string) get_field('social_cta_title', 'option');
$description = (string) get_field('social_cta_description', 'option');
$tg_label    = (string) get_field('social_cta_telegram_label', 'option');
$vk_label    = (string) get_field('social_cta_vk_label', 'option');
?>
<section class="social_link">
	<div class="container">
		<?php if ($title !== ''): ?>
			<h2><?php echo esc_html($title); ?></h2>
		<?php endif; ?>
		<div class="info">
			<?php if ($description !== ''): ?>
				<div class="description_title"><?php echo esc_html($description); ?></div>
			<?php endif; ?>
			<div class="social_list">
				<?php if ($tg_url !== '' && $tg_label !== ''): ?>
					<div class="btn"><a href="<?php echo esc_url($tg_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($tg_label); ?></a></div>
				<?php endif; ?>
				<?php if ($vk_url !== '' && $vk_label !== ''): ?>
					<div class="btn"><a href="<?php echo esc_url($vk_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($vk_label); ?></a></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
