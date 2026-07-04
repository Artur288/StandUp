<?php

// стрелка справа у пунктов с подменю — на 1 и 2 уровне (для десктопа и мобилы общая разметка)
add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {
	if (!isset($args->menu_id) || !in_array($args->menu_id, ['menu-primary', 'menu-primary-mob'], true)) {
		return $item_output;
	}
	if ($depth > 1 || !in_array('menu-item-has-children', (array) $item->classes, true)) {
		return $item_output;
	}
	$arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="8" viewBox="0 0 17 8" fill="none" class="menu-arrow"><path d="M0.45 0.6L8.45 6.6L16.45 0.6" stroke="#EEEDDE" stroke-width="1.5"/></svg>';
	return preg_replace('#</a>(\s*)$#', $arrow . '</a>$1', $item_output, 1);
}, 10, 4);
