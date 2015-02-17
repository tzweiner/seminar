<?php get_header(); ?>
<?php if (have_posts()): 
	while (have_posts()): the_post(); ?>
	<div class="c12 row banner">
		<div class="seminar-banner"><img src="/wp-content/uploads/2015/02/seminar_banner_2015.jpg" alt="banner" /></div>
	</div>
	<div class="c12 row">
        <p class="welcome-text">Thank you for your interest in the <?php echo get_year_order (); ?> annual Bulgarian Folk Music & Dance Seminar, taking place in the lovely, historic town of Plovdiv, Bulgaria.</p>
      </div>
     
      <!-- <h2>Testimonials</h2> -->
      <?php the_content(); ?>
           
      <?php echo get_red_note(); ?>
 <?php 
 endwhile;
 endif; 
 ?>
<?php get_footer(); ?>