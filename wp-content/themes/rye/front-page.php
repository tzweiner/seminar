<?php get_header(); ?>
<?php


if (have_posts ()) :
	while ( have_posts () ) :
		the_post ();
		?>
<div class="c12 row banner">
	<!-- /wp-content/uploads/2015/09/seminar_banner_2016.jpg -->

	<div class="seminar-banner">
		<img src="<?php the_field('seminar_banner'); ?>" alt="banner" />
	</div>
</div>
<div class="c12 row">
	<p class="welcome-text"><?php the_content(); ?></p>
</div>

<!-- <h2>Testimonials</h2> -->


<?php echo get_red_note(); ?>
 <?php
	endwhile
	;

 endif;
?>
<?php get_footer(); ?>