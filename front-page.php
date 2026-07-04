<?php
get_header();

$city_id = (int) get_field('city');
get_template_part('template-parts/blocks/city-home', null, ['city_term_id' => $city_id]);

get_footer();
