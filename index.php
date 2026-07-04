<?php
get_header();
?>
<div class="container">
	<?php if (have_posts()): while (have_posts()): the_post(); ?>
		<article class="def-post">
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</article>
	<?php endwhile; endif; ?>
</div>
<?php
get_footer();
