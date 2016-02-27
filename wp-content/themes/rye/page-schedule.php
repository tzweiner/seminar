<?php get_header(); ?>
<?php if ( have_posts() ) : ?>

	<?php while (have_posts()) : the_post(); ?>
<h2>
	<span><?php the_title(); ?></span>
</h2>
<div class="c12 row">
		<?php echo get_red_note(); ?>			
		
		<?php the_content(); ?>
				
	</div>

<?php
	
endwhile
	;

endif;
?>
<?php get_footer(); ?>
