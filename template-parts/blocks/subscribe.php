<?php
$description = (string) get_field('subscribe_description', 'option');
$cta_label   = (string) get_field('subscribe_cta_label', 'option');
$cta_url     = (string) get_field('subscribe_cta_url', 'option');

if ($description === '' && $cta_label === '') {
	return;
}
?>
<section class="subscribe">
	<div class="container">
		<?php if ($description !== ''): ?>
			<div class="description_info"><?php echo esc_html($description); ?></div>
		<?php endif; ?>
		<?php if ($cta_label !== '' && $cta_url !== ''): ?>
			<div class="btn">
				<a href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
