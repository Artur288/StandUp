<?php
$logo_id = function_exists('get_field') ? get_field('logo', 'option') : null;
$phone   = function_exists('get_field') ? get_field('phone', 'option') : '';
$hours   = function_exists('get_field') ? get_field('hours', 'option') : '';
$tg_url  = function_exists('get_field') ? get_field('telegram_url', 'option') : '';
$vk_url  = function_exists('get_field') ? get_field('vk_url', 'option') : '';
$max_url  = function_exists('get_field') ? get_field('max_url', 'option') : '';
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="site-header">
	<div class="container">
		<a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
			<?php if ($logo_id): ?>
				<?php echo wp_get_attachment_image($logo_id, 'full', false, ['class' => 'logo', 'alt' => get_bloginfo('name')]); ?>
			<?php else: ?>
				<span class="logo logo--text"><?php echo esc_html(get_bloginfo('name')); ?></span>
			<?php endif; ?>
		</a>
		<nav class="site-nav" role="navigation" aria-label="Main menu">
			<?php
			wp_nav_menu([
				'theme_location'  => 'primary',
				'menu_id'         => 'menu-primary',
				'menu_class'      => 'menu',
				'container'       => 'div',
				'container_class' => 'menu-primary-container',
				'depth'           => 3,
				'fallback_cb'     => false,
			]);
			?>
		</nav>
		<div class="right_nav">
			<div class="info">
				<?php if ($hours): ?><p><?php echo esc_html($hours); ?></p><?php endif; ?>
				<?php if ($phone): ?><p><?php echo esc_html($phone); ?></p><?php endif; ?>
			</div>
			<div class="social">
				<?php if ($tg_url): ?>
					<a href="<?php echo esc_url($tg_url); ?>" target="_blank" rel="noopener"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/tg_icon.png')); ?>" alt="Telegram"></a>
				<?php endif; ?>
				<?php if ($vk_url): ?>
					<a href="<?php echo esc_url($vk_url); ?>" target="_blank" rel="noopener"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/vk_icon.png')); ?>" alt="VK"></a>
				<?php endif; ?>
				<?php if ($max_url): ?>
					<a href="<?php echo esc_url($max_url); ?>" target="_blank" rel="noopener"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/max_icon.png')); ?>" alt="Max"></a>
				<?php endif; ?>
			</div>
		</div>
		<div class="burger">
			<svg xmlns="http://www.w3.org/2000/svg" width="35" height="15" viewBox="0 0 35 15" fill="none">
				<path d="M0.597168 0.597656H33.5075" stroke="#EEEDDE" stroke-width="1.19444" stroke-linecap="square"/>
				<path d="M14.7739 7.41309L34.0138 7.41309" stroke="#EEEDDE" stroke-width="1.19444" stroke-linecap="square"/>
				<path d="M0.597168 14.2285H33.5075" stroke="#EEEDDE" stroke-width="1.19444" stroke-linecap="square"/>
			</svg>
		</div>
	</div>
</header>

<div class="mobile_menu">
	<div class="top">
		<svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none" class="close_mobile_menu">
			<path d="M9.49027 11.2812L32.7614 29.5541" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
			<path d="M8.77713 29.5488L32.0483 11.276" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
		</svg>
	</div>
	<div class="container_mob_menu">
		<nav class="site-nav" role="navigation" aria-label="Main menu">
			<?php
			wp_nav_menu([
				'theme_location'  => 'primary',
				'menu_id'         => 'menu-primary-mob',
				'menu_class'      => 'menu',
				'container'       => 'div',
				'container_class' => 'menu-primary-container',
				'depth'           => 3,
				'fallback_cb'     => false,
			]);
			?>
		</nav>
		<div class="bottom_nav">
			<div class="info">
				<?php if ($hours): ?><p><?php echo esc_html($hours); ?></p><?php endif; ?>
				<?php if ($phone): ?><p><?php echo esc_html($phone); ?></p><?php endif; ?>
			</div>
			<div class="social">
				<?php if ($vk_url): ?>
					<a href="<?php echo esc_url($vk_url); ?>" target="_blank" rel="noopener"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/vk_icon.png')); ?>" alt="VK"></a>
				<?php endif; ?>
				<?php if ($tg_url): ?>
					<a href="<?php echo esc_url($tg_url); ?>" target="_blank" rel="noopener"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/tg_icon.png')); ?>" alt="Telegram"></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<main>
