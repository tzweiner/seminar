<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<title><?php
/*
 * Print the <title> tag based on what is being viewed.
 */
global $page, $paged;

wp_title ( '|', true, 'right' );

// Add the blog name.
bloginfo ( 'name' );

// Add the blog description for the home/front page.
$site_description = get_bloginfo ( 'description', 'display' );
if ($site_description && (is_home () || is_front_page ()))
	echo " | $site_description";
	
	// Add a page number if necessary:
if ($paged >= 2 || $page >= 2)
	echo ' | ' . sprintf ( 'Page %s', max ( $paged, $page ) );

?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all"
	href="<?php bloginfo('stylesheet_url'); ?>" />
<link rel="stylesheet" type="text/css" media="screen"
	href="<?= get_bloginfo('template_directory').'/assets/css/jquery.fancybox.css' ?>" />
<link rel="stylesheet" type="text/css" media="screen"
	href="<?= get_bloginfo('template_directory').'/assets/css/style.css' ?>" />
<link rel="stylesheet" type="text/css" media="print"
	href="<?= get_bloginfo('template_directory').'/assets/css/print.css' ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<link rel="shortcut icon" href="/wp-content/themes/rye/favicon.ico"
	type="image/x-icon">
<link rel="icon" href="/wp-content/themes/rye/favicon.ico"
	type="image/x-icon">

<!-- fonts from Google -->
<link
	href='http://fonts.googleapis.com/css?family=Tinos&subset=latin,cyrillic-ext'
	rel='stylesheet' type='text/css'>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript"
	src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-54d7c94713efa311"
	async="async"></script>
<?php wp_head(); ?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-59345929-1', 'auto');
  ga('send', 'pageview');

</script>

</head>

<body <?php body_class(); ?>>

	<div class="sticky">
		<!-- NAVBAR ================================================== -->
		<div class="top-bar">
			<div class="container clearfix">


				<ul class="social">
					<li class="share-wrapper"><a href="<?php the_permalink(); ?>"
						class="share">Share +</a>
						<div class="addthis_sharing_toolbox"
							data-url="<?php echo get_permalink(); ?>"
							data-title="<?php if(is_front_page()) echo bloginfo('name'); else get_the_title (); ?>"></li>
					<li><a href="<?php echo get_field ('facebook_url', 'option'); ?>"
						class="fb" target="_blank"></a></li>
					<!-- <li><a href="#" class="gp"></a></li>
          <li><a href="#" class="tw"></a></li>
          <li><a href="#" class="in"></a></li> -->
				</ul>
			</div>
		</div>


		<div class="main-menu-wrapper">
			<div class="container clearfix">
				<!-- <h1><a class="navbar-brand" href="/"><img src="/wp-content/themes/rye/assets/images/seminarlogo1<?php if (is_front_page() ) echo '_small'; ?>.jpg" alt="banner" /></a></h1> -->
		    <?php
						// ABOUT Mega Menu
						$defaults = array (
								'theme_location' => '',
								'menu' => '3',
								'container' => 'div',
								'container_class' => '',
								'container_id' => '',
								'menu_class' => 'menu',
								'menu_id' => '10',
								'echo' => true,
								'fallback_cb' => 'wp_page_menu',
								'before' => '',
								'after' => '',
								'link_before' => '',
								'link_after' => '',
								'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'depth' => 0,
								'walker' => '' 
						);
						wp_nav_menu ( $defaults );
						?>
	    </div>
			<!-- END container -->
		</div>
		<!-- END main-menu-wrapper -->
    
    <?php
				
				// generate man nav for <= table size
				$args = array (
						'order' => 'ASC',
						'orderby' => 'menu_order',
						'output' => ARRAY_A,
						'output_key' => 'menu_order',
						'nopaging' => true,
						'update_post_term_cache' => false 
				);
				
				$menu_items = wp_get_nav_menu_items ( '3' ); // Main Nav
				if ($menu_items) :
					?>
    
    
    
    <div class="navbar-wrapper">
			<div class="container">
				<div class="navbar navbar-inverse navbar-static-top clearfix"
					role="navigation">
					<div class="navbar-header clearfix">
						<div class="by-line">
							<span class="label">Bulgarian Folk Music &<br />Dance Seminar
							</span><br />
							<span class="date">July 18-24, 2018</span>
						</div>
						<button type="button" class="navbar-toggle" data-toggle="collapse"
							data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span> <span
								class="icon-bar"></span> <span class="icon-bar"></span> <span
								class="icon-bar"></span>
						</button>
						<h1>
							<a class="navbar-brand" href="/"><img
								src="/wp-content/themes/rye/assets/images/seminarlogo1<?php if (is_front_page() ) echo '_small'; ?>.jpg"
								alt="banner" /></a>
						</h1>
					</div>
					<div class="navbar-collapse collapse">
						<ul class="nav navbar-nav">              	
              <?php
					// Main nav
					foreach ( $menu_items as $key => $menu_item ) :
						?>
              	<li
								<?php if ($menu_item->url == get_permalink()) echo ' class="current-menu-item"'; ?>><a
								href="<?php echo $menu_item->url; ?>"><?php echo $menu_item->title; ?></a></li>
              
              <?php endforeach; ?>
              </ul>
					</div>
				</div>
			</div>
		</div>
    
    <?php endif; // end if $menu_items ?>

    
    <?php
				
if (is_front_page ()) :
					$slides = get_field ( 'slides' );
					?>
    <!--  SLIDER  -->
		<div class="slider-wrapper">
			<div class="cycle-pager"></div>
    <?php
					foreach ( $slides as $index => $slide ) :
						?>
      <div class="slide">
      <?php $thumbnail = $slide ['slide_image']; ?>
      
      	<img src="<?php echo $thumbnail; ?>" alt="" />
			</div>      
      <?php endforeach; ?>
    	<a class="arrow-prev"><span class="chevron-left">&lsaquo;</span></a>
			<a class="arrow-next"><span class="chevron-right">&rsaquo;</span></a>
		</div>
		<!--  END SLIDER -->
	<?php endif; ?>
	

    <!-- Content -->

		<div
			class="container clearfix mb-20 <?php if (!is_front_page()) echo 'mt-100 content-wrapper'; else echo ' homepage-splash'; ?>">