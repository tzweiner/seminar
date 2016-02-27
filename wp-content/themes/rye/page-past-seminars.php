<?php get_header(); ?>
<?php if ( have_posts() ) : ?>

	<?php while (have_posts()) : the_post(); ?>
<h2>
	<span><?php the_title(); ?></span>
</h2>
<div class="c12">
	<!-- c12 row -->
		<?php echo get_red_note(); ?>
		
		<?php the_content(); ?>
		
		<h3 class="margin">Teaching Staff Archive</h3>
		<?php
		$previous_years = get_field ( 'previous_years_archive', 13 );
		?>
		
		<div class="previous-years-wrapper">
		<select name="previous_years" class="previous_years">
			<option value="">- Select Year -</option> <?php
		foreach ( $previous_years as $year ) :
			?>
				<option value="<?php echo $year ['pdf_of_teaching_staff']; ?>"><?php echo $year ['teaching_staff_archive_year']; ?></option>
				<?php endforeach; ?>
			</select> <a href="#" class="fancypdf" name="all"
			id="previous_years_button">View Staff</a>
	</div>
</div>

<?php
	
endwhile
	;

endif;
?>
<?php get_footer(); ?>
