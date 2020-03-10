<?php get_header(); ?>
<?php if ( have_posts() ) : ?>

	<?php while (have_posts()) : the_post(); ?>
<h2>
	<span><?php the_title(); ?></span>
</h2>
<div class="c12 row">
		<?php the_content(); ?>
		
		<?php
		$args = array (
				'post_type' => 'hotels',
				'post_status' => 'publish',
				'posts_per_page' => - 1,
				'orderby' => 'date',
				'order' => 'DESC' 
		);
		
		$my_query = new WP_Query ( $args );
		
		if ($my_query->have_posts ()) :
			?>
		<h3>Hotel & Hostel Information</h3>
		<?php
			while ( $my_query->have_posts () ) :
				$my_query->the_post ();
				?>
		
		<?php
				$link = get_field ( 'link' );
				if (! $link) :
					?>
		<p class="heading"><?php echo get_the_title(); ?></p>
		<?php else: ?>
		<p class="heading">
		<a href="<?php echo $link; ?>" target="_blank"><?php echo get_the_title(); ?></a>
	</p>
		<?php endif; ?>
		<?php if ($post->post_content != ''): ?>
		<span><?php echo $post->post_content; ?></span>
		<?php endif; ?>
		
		<?php
			endwhile
			;
		 else :
			?>
		<p>No information found.</p>
		
		
		<?php
endif;
		
		wp_reset_postdata ();
		wp_reset_query ();
		?>
	</div>

<?php
	
endwhile
	;

endif;
?>
<?php get_footer(); ?>
