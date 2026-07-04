<?php
$logo_id           = function_exists('get_field') ? get_field('logo', 'option') : null;
$phone             = function_exists('get_field') ? get_field('phone', 'option') : '';
$hours             = function_exists('get_field') ? get_field('hours', 'option') : '';
$email             = function_exists('get_field') ? get_field('email', 'option') : '';
$address           = function_exists('get_field') ? get_field('address', 'option') : '';
$inn               = function_exists('get_field') ? get_field('inn', 'option') : '';
$ogrnip            = function_exists('get_field') ? get_field('ogrnip', 'option') : '';
$registration_date = function_exists('get_field') ? get_field('registration_date', 'option') : '';
$copyright         = function_exists('get_field') ? get_field('copyright', 'option') : '';
$has_footer_menu   = has_nav_menu('footer');
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="footer_top">
			<div class="logo">
				<?php if ($logo_id): ?>
					<?php echo wp_get_attachment_image($logo_id, 'full', false, ['alt' => get_bloginfo('name')]); ?>
				<?php else: ?>
					<span class="logo logo--text"><?php echo esc_html(get_bloginfo('name')); ?></span>
				<?php endif; ?>
			</div>
			<div class="info">
				<div class="contact_mobile">
					<?php if ($phone): ?><p><?php echo esc_html($phone); ?></p><?php endif; ?>
					<?php if ($hours): ?><p><?php echo esc_html($hours); ?></p><?php endif; ?>
					<br>
					<?php if ($email): ?><p><?php echo esc_html($email); ?></p><?php endif; ?>
				</div>
				<?php if ($address): ?>
					<div class="address"><?php echo esc_html($address); ?></div><br>
				<?php endif; ?>
				<div class="fop">
					<?php if ($inn): ?>ИНН <?php echo esc_html($inn); ?><br><?php endif; ?>
					<?php if ($ogrnip): ?>ОГРНИП № <?php echo esc_html($ogrnip); ?><?php endif; ?>
					<?php if ($registration_date): ?> от <?php echo esc_html($registration_date); ?>г.<br><?php endif; ?>
				</div>
			</div>
			<div class="contact">
				<?php if ($phone): ?><p><?php echo esc_html($phone); ?></p><?php endif; ?>
				<?php if ($hours): ?><p><?php echo esc_html($hours); ?></p><?php endif; ?>
				<br>
				<?php if ($email): ?><p><?php echo esc_html($email); ?></p><?php endif; ?>
			</div>
		</div>
		<div class="footer_bottom">
			<?php if ($copyright): ?><span><?php echo esc_html($copyright); ?></span><?php endif; ?>
			<?php if ($has_footer_menu): ?>
				<?php
				wp_nav_menu([
					'theme_location' => 'footer',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'depth'          => 1,
					'fallback_cb'    => false,
				]);
				?>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php get_template_part('template-parts/modals/order-event'); ?>

<?php get_template_part('template-parts/modals/form-thanks'); ?>

<?php wp_footer(); ?>
</body>
</html>
