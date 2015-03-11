	</div><!-- /.container -->
    <div class="push"></div>
  </div>
    <!--FOOTER-->
    <div class="footer">
      <div class="container clearfix">
      	<div class="left-column column">
	      	<h3>Bulgarian Folk Music & Dance Seminar <?php echo date ('Y', strtotime('now')); ?></h3>
	      	
	      	
	      	<?php 
		      // generate man nav for <= table size
		      $args = array(
		      		'order'                  => 'ASC',
		      		'orderby'                => 'menu_order',
		      		'output'                 => ARRAY_A,
		      		'output_key'             => 'menu_order',
		      		'nopaging'               => true,
		      		'update_post_term_cache' => false );
		      
		      $menu_items = wp_get_nav_menu_items ('4'); // footer menu
		
		      // Top right ?>
		      <ul> <?php 
		      foreach ($menu_items as $key=>$menu_item): ?>
		      	<li<?php if ($menu_item->url == get_permalink()) echo ' class="current-menu-item"'; ?>><a href="<?php echo $menu_item->url; ?>"><?php echo $menu_item->title; ?></a></li>
		              
		      <?php endforeach; ?>
		      </ul>
		      
		      <?php 
		      // generate man nav for <= table size
		      $args = array(
		      		'order'                  => 'ASC',
		      		'orderby'                => 'menu_order',
		      		'output'                 => ARRAY_A,
		      		'output_key'             => 'menu_order',
		      		'nopaging'               => true,
		      		'update_post_term_cache' => false );
		      
		      $menu_items = wp_get_nav_menu_items ('5'); // footer 2 menu
		
		      // Footer 2 ?>
		      <ul> <?php 
		      
		      $show_evaluation = get_field ('show_evaluation', 'option');
		      if ($show_evaluation):
		      $evaluation_url = get_field ('evaluation_url', 'option');
		      ?>	      	
		      	<li><a href="<?php echo $evaluation_url; ?>" target="_blank">Evaluation Form</a></li>
		      <?php endif;
		      		      
		      foreach ($menu_items as $key=>$menu_item): ?>
		      	<li<?php if ($menu_item->url == get_permalink()) echo ' class="current-menu-item"'; ?>><a href="<?php echo $menu_item->url; ?>"><?php echo $menu_item->title; ?></a></li>		              
		      <?php endforeach; ?>
		      	<?php $flyer = get_field ('seminar_flyer', 'option'); 
		      	if ($flyer): ?>
		      	<li><a href="<?php echo $flyer['url']; ?>" target="_blank">Seminar Flyer</a></li>
		      	<?php endif; ?>
		      	<li class="facebook-icon"><a href="<?php echo get_field ('facebook_url', 'option'); ?>" target="_blank"><img alt="Facebook button" src="/wp-content/uploads/2015/02/FB_FindUsOnFacebook-1024.png" /></a></li>
		      </ul>

      	</div>
      	
      	<?php $flyer_thumbnail = get_field ('seminar_flyer_thumbnail', 'option'); 
      	if ($flyer_thumbnail): ?>
      	<div class="middle-column column thumbnail">   
      		<h3>Seminar Flyer</h3>   		
      		<a href="<?php echo $flyer['url']; ?>" target="_blank"><img src=<?php echo $flyer_thumbnail; ?> alt="flyer thumbnail" /></a>
      	</div>
      	<?php endif; ?>
      	
      	<div class="right-column column">
      		<h3><a href="http://www.eefc.org/" target="_blank">Supporting Partner of the<br />East European Folklife Center</a></h3>
      		<ul>		      	
				<li><a href="http://www.eefc.org/" target="_blank"><img src="<?php echo get_bloginfo('template_directory'); ?>/assets/images/eefc_logo.png" alt="EEFC logo" /></a></li>
			</ul>
		      
		      
      		<p>
				<!-- Start of StatCounter Code -->
				
				<!-- <script type="text/javascript">
				var sc_project=615870; 
				var sc_invisible=0; 
				var sc_partition=46; 
				var sc_click_stat=1; 
				var sc_security="20592f3a"; 
				</script>
				s
				<script type="text/javascript" language="javascript" src="http://www.statcounter.com/counter/counter.js"></script> -->
				<!-- End of StatCounter Code -->
				<noscript></noscript>
	        </p>
      	
      	</div>
      	
		
      </div>
    </div>
    

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php echo get_bloginfo('template_directory').'/assets/js/bootstrap.js'; ?>"></script>	
<?php wp_footer(); ?>
</body>
</html>
