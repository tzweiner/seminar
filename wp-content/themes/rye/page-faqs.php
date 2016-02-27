<?php get_header(); ?>
<?php if ( have_posts() ) : ?>

	<?php while (have_posts()) : the_post(); ?>
<h2>
	<span><?php the_title(); ?></span>
</h2>
<div class="c12 row">
		
		<?php echo get_red_note(); ?>
		
		<?php the_content(); ?>
		<p class="clr_both txt-right" style="margin: 1em 0 4px;">
		<a href="#" id="expand-all">Expand All</a> | <a href="#"
			id="collapse-all">Collapse All</a>
	</p>
		<?php
		$faqs = get_field ( 'faqs_pairs' );
		if ($faqs) :
			?>
		<div class="accordion"> <?php
			foreach ( $faqs as $faq ) :
				?>
			<div class="q-a-pair">
			<div class="question">
				<a href="#"><?php echo strip_tags (apply_filters ('the_content', $faq['question'])); ?></a>
			</div>
			<div class="answer"><?php echo apply_filters ('the_content', $faq['answer']); ?></div>
		</div>
		<?php endforeach; ?>
		</div> 
		<?php 
		endif;
		?>
	</div>

<?php
	
endwhile
	;

endif;
?>
<?php get_footer(); ?>
