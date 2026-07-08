<?php get_header();
$page_photo_id = get_field('hero_banner');
$page_photo_mobile_id = get_field('hero_banner_mob');
$event_title = get_field('event_title');
$event_description = get_field('event_text');
$purchase_label = get_field('purchase_label');
$purchase_url = get_field('purchase_url');
$bubbles = get_field('event_bubbles');
$team_title = get_field('event_team_title');
$team_text = get_field('event_team_text');
$team = get_field('event_team');
$team_hide = get_field('event_team2');
$stage_id = (int) get_field('stage');
$ticket_text = get_field('event_ticket_text');
$ticket_btn_text = get_field('event_ticket_btn');
$ticket_btn_link = get_field('event_ticket_btn_link');
$similar_title = get_field('event_similar_title');
$similar_text = get_field('event_similar_text');
$similar_btn = get_field('event_similar_btn');
$similar_btn_link = get_field('event_similar_btn_link');
$similar = get_field('event_similar');

?>
<?php if ($page_photo_id || $page_photo_mobile_id):
	$desktop_url = $page_photo_id ? wp_get_attachment_image_url($page_photo_id, 'full') : '';
	$mobile_url  = $page_photo_mobile_id ? wp_get_attachment_image_url($page_photo_mobile_id, 'full') : '';
	?>
	<section class="home_slider">
		<div class="swiper homeSwiper">
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<picture>
						<?php if ($mobile_url): ?>
							<source media="(max-width: 767px)" srcset="<?php echo esc_url($mobile_url); ?>">
						<?php endif; ?>
						<img src="<?php echo esc_url($desktop_url ?: $mobile_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
					</picture>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>
<section class="event-format">
	<div class="container">
		<div class="event-format__content">
			<div class="event-format__left _max">
				<?php if ($event_title !== ''): ?>
					<h2 class="event-format__title"><?php echo esc_html($event_title); ?></h2>
				<?php endif; ?>
				<?php if ($event_description !== ''): ?>
					<div class="event-format__text"><?php echo $event_description; ?></div>
				<?php endif; ?>
				<?php if ($purchase_label !== '' && $purchase_url !== ''): ?>
					<a class="event-format__link event-format__link--desk btn event__btn" href="<?php echo esc_url($purchase_url); ?>"><span class="active"><?php echo esc_html($purchase_label); ?></span></a>
				<?php endif; ?>
			</div>
			<?php if (!empty($bubbles)): ?>
				<div class="event-format__right">
					<?php foreach ($bubbles as $bubble): ?>
						<div class="event-format__bubble">
							<div class="title_block"><?php echo esc_html($bubble['bubble_title'] ?? ''); ?></div>
							<div class="description_block"><?php echo esc_html($bubble['bubble_description'] ?? ''); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ($purchase_label !== '' && $purchase_url !== ''): ?>
				<a class="event-format__link event-format__link--mob btn" href="<?php echo esc_url($purchase_url); ?>"><span><?php echo esc_html($purchase_label); ?></span></a>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php if (!empty($team) || !empty($team_hide)): ?>
    <section class="comic_list">
	<div class="container">
		<?php if ($team_title !== ''): ?>
			<h2><?php echo esc_html($team_title); ?></h2>
		<?php endif; ?>
		<div class="top">
			<?php if ($team_text !== ''): ?>
				<div class="desription_title"><?php echo esc_html($team_text); ?></div>
			<?php endif; ?>
            <div class="btn"><a href="<?php echo get_home_url(); ?>/comedian/" class="btn_all_commic">смотреть всех комиков</a></div>
		</div>
	</div>
	<div class="container container_swiper">
		<div class="swiper comic_swiper">
			<div class="swiper-edge swiper-edge--left"></div>
			<div class="swiper-edge swiper-edge--right"></div>
			<div class="swiper-wrapper">
				<?php foreach ($team as $member):
					$name_parts = explode(' ', $member->post_title, 2);
					?>
					<div class="swiper-slide">
						<div class="comedian_card">
							<a href="<?php echo esc_url(get_permalink($member)); ?>">
								<div class="comedian_thumb">
									<?php echo get_the_post_thumbnail($member, 'medium', ['alt' => $member->post_title]); ?>
								</div>
								<div class="comedian_name"><?php echo esc_html($name_parts[0]); ?><?php if (isset($name_parts[1])): ?><br><?php echo esc_html($name_parts[1]); ?><?php endif; ?></div>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
                <?php foreach ($team_hide as $item):
                    $item_sex = $item['sex'] ?? '';
                    ?>
                    <div class="swiper-slide">
                        <div class="comedian_card _hide">
                            <div class="comedian_thumb">
                                <?php if ($item_sex == "male"): ?>
                                    <img src="<?php echo bloginfo('template_url'); ?>/assets/images/male.png">
                                <?php elseif ($item_sex == "female") : ?>
                                    <img src="<?php echo bloginfo('template_url'); ?>/assets/images/female.png">
                                <?php endif; ?>
                            </div>
                            <div class="comedian_name">Секретный <br>комик</div>
                        </div>
                    </div>
                <?php endforeach; ?>
			</div>
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
		</div>
	</div>
</section>
<?php endif; ?>
<?php if ($stage_id): ?>
<section class="event-stage">
    <div class="container">
        <h2>Площадка: «<?php echo get_the_title($stage_id); ?>»</h2>
        <div class="event-stage__subtitle"><?php the_field('metro', $stage_id); ?> / <?php the_field('address', $stage_id); ?></div>
        <div class="event-stage_row">
            <img src="<?php echo get_the_post_thumbnail_url($stage_id, 'full'); ?>" alt="<?php echo get_the_title($stage_id); ?>" class="event-stage__img">
            <div class="event-stage__text"><?php echo the_field('popup_description', $stage_id); ?></div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php get_template_part('template-parts/blocks/videos'); ?>
<?php if ($ticket_text) : ?>
<section class="subscribe">
	<div class="container">
        <div class="description_info"><?php echo $ticket_text; ?></div>
            <div class="btn event-ticket__btn">
                <a href="<?php echo $ticket_btn_link; ?>"><?php echo $ticket_btn_text; ?></a>
            </div>
        </div>
</section>
<?php endif; ?>
<?php if ($similar) : ?>
    <section class="event-similar category_list">
        <div class="container">
            <h2><?php echo $similar_title; ?></h2>
            <div class="top">
                <?php if ($team_text !== ''): ?>
                    <div class="desription_title"><?php echo esc_html($similar_text); ?></div>
                <?php endif; ?>
                <div class="btn"><a href="<?php echo $similar_btn_link; ?>" class="btn_all_commic"><?php echo $similar_btn; ?></a></div>
            </div>
        </div>
        <div class="container container_swiper">
            <div class="swiper category_swiper">
                <div class="swiper-edge swiper-edge--left"></div>
                <div class="swiper-edge swiper-edge--right"></div>
                <div class="swiper-wrapper">
                    <?php foreach($similar as $item) :
                    $item_id = $item->ID;
                    ?>
                    <div class="swiper-slide">
                        <div class="category_card">
                            <div class="category_thumb">
                                <img src="<?php echo get_the_post_thumbnail_url($item_id, 'full'); ?>" alt="<?php echo get_the_title($item_id); ?>">
                            </div>
                            <div class="category_name">
								<div class="border"></div>
								<div class="name">
                                    <?php foreach (get_field('schedule', $item_id) as $i => $time) {
                                        echo (($i !== 0) ? '<br>' : '') . $time['time'];
                                    }?>
                                </div>
								<div class="border"></div>
							</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        </div>
    </section>
<?php endif; ?>
<?php get_template_part('template-parts/blocks/social-cta'); ?>

<?php get_template_part('template-parts/blocks/subscribe'); ?>
<?php get_footer(); ?>