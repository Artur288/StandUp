<?php
get_header();

$term = get_queried_object();
if (!$term || empty($term->term_id)) {
	get_footer();
	return;
}

get_template_part('template-parts/blocks/city-home', null, ['city_term_id' => (int) $term->term_id]);

get_footer();
