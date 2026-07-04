<?php
$args  = $args ?? [];
$block = standup_get_videos_block();
$title = (string) ($args['title_override'] ?? '');
if ($title === '') $title = $block['title'];
$videos = $block['videos'];
if (empty($videos)) {
	return;
}

$play_icon = '<svg class="video_card__play-icon" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
	. '<circle cx="48" cy="48" r="44" fill="rgba(0,0,0,0.55)" stroke="#EEEDDE" stroke-width="2"/>'
	. '<path d="M40 32 L66 48 L40 64 Z" fill="#EEEDDE"/>'
	. '</svg>';

$is_video_url = static function (string $url): bool {
	if ($url === '') return false;
	$lower = strtolower($url);
	if (str_contains($lower, 'youtube.com') || str_contains($lower, 'youtu.be') || str_contains($lower, 'vimeo.com')) {
		return true;
	}
	return (bool) preg_match('~\.(mp4|webm|mov|m4v|ogv)(\?|#|$)~', $lower);
};
?>
<section class="video_concert_list">
	<div class="container">
		<?php if ($title !== ''): ?>
			<h2><?php echo esc_html($title); ?></h2>
		<?php endif; ?>
	</div>
	<div class="container container_swiper">
		<div class="swiper video_swiper">
			<div class="swiper-edge swiper-edge--left"></div>
			<div class="swiper-edge swiper-edge--right"></div>
			<div class="swiper-wrapper">
				<?php foreach ($videos as $video):
					$thumb_id  = (int) ($video['thumbnail'] ?? 0);
					$video_url = (string) ($video['video_url'] ?? '');
					if (!$thumb_id) continue;
					?>
					<div class="swiper-slide">
						<div class="video_card">
							<?php if ($video_url !== ''): ?>
								<a class="video_card__link" href="<?php echo esc_url($video_url); ?>" data-fancybox="videos">
									<?php echo wp_get_attachment_image($thumb_id, 'large'); ?>
									<?php if ($is_video_url($video_url)): ?>
										<span class="video_card__play"><?php echo $play_icon; ?></span>
									<?php endif; ?>
								</a>
							<?php else: ?>
								<?php echo wp_get_attachment_image($thumb_id, 'large'); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="swiper-button-next"></div>
		</div>
	</div>
</section>
