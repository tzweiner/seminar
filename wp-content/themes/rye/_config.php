<?php
/**
 *      --   Rye Configuration    --
 *
 *  Here you can declare your javascript files, custom menus, 
 *  widgetized areas, custom post types and taxonomies.
 *
 * 
 *  Project Name: <Name>
 *  Created: <MM-DD-YYYY>
 * 
 */



/**
 *  Site configurations.
 */
$rye_config = array(
    /**
     *  Place JavaScripts in footer. This tends to break some plugins that rely on
     *  jQuery in the header. Enable with caution.
     *  http://developer.yahoo.com/performance/rules.html#js_bottom
     */
    'place_javascript_in_footer' => false,



    /**
     *  Path to JavaScript files.
     *  http://codex.wordpress.org/Function_Reference/wp_register_script
     */
    'javascripts' => array(
		'jquery'  		=> '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js',
    	'modernizr'  	=> get_bloginfo('template_directory').'/assets/js/modernizr-2.6.1.min.js',
    	'selectivizr'	=> get_bloginfo('template_directory').'/assets/js/selectivizr-min.js',
        'chart' 		=> get_bloginfo('template_directory').'/assets/js/chart.js',
		'fancybox' 		=> get_bloginfo('template_directory').'/assets/js/fancybox/source/jquery.fancybox.js',
		'cycle2' 		=> get_bloginfo('template_directory').'/assets/js/jquery.cycle2.js',
		'cycle2swipe' 	=> get_bloginfo('template_directory').'/assets/js/jquery.cycle2.swipe.js',
		//'fancybox-pack' => get_bloginfo('template_directory').'/assets/js/fancybox/source/jquery.fancybox.pack.js',		
		'audio-js'		=> get_bloginfo('template_directory').'/assets/js/audiojs/audio.min.js',
        'script'  		=> get_bloginfo('template_directory').'/assets/js/script.js'

    ),
    
    
    
    /**
     *  Path to JavaScript files.
     *  http://codex.wordpress.org/Function_Reference/add_image_size
     *
     *  '<image-size-name>' => array(<width>, <height>, <crop>)
     */
    'image_sizes' => array(
        /*
        'featured_post'    => array(500, 500, false),
        'featured_article' => array(200, 200, true)
        */
    ),



    /**
     *  Declare Custom Menu Regions.
     *  http://codex.wordpress.org/Function_Reference/register_nav_menus
     */
    'menus' => array(
        /*
        'main-nav'    => 'Main Navigation',
        'sub-nav'     => 'Sub Navigation',
        */
    ),
    

    
    /**
     *  Declare Custom Menu Regions.
     *  http://codex.wordpress.org/Function_Reference/register_nav_menus
     *
     *  '<feature>' => array('<arg>', '<arg>')
     */
    'theme_support' => array(
    	'post-thumbnails' => array ('classes', 'teachers', 'dance-teachers')
        /*
        'post-thumbnails' => array('post', 'articles'),
        'post-formats'    => array('aside', 'gallery')
        */
    ),



    /**
     *  Declare "Widgetized" Regions.
     *  http://codex.wordpress.org/Function_Reference/register_sidebar
     */
    'widgetized_regions' => array(
        /*
        array(
            'name'          => '<Region Name>',
            'description'   => '<Region Description>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
        ),
        array(
            'name'          => '<Region Name>',
            'description'   => '<Region Description>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
        )
        */
        array(
            'name'          => 'Top Header Menu',
            'id'			=> 'page-menu',
            'description'   => '<Region Description>',
            'before_title'  => false,
            'after_title'   => false,
            'before_widget' => false,
            'after_widget'  => false,
        )
    ),
    


    /**
     *  Declare Custom Post Types.
     *  http://codex.wordpress.org/Function_Reference/register_post_type
     */
    'post_types' => array(
		/*'event' => array(
            'labels'             => array('name' => 'Event', 'add_new_item' => 'Add New Event', 'edit_item' => 'Edit Event', 'new_item' => 'New Event', 'view_item' => 'View Event', 'search_item' => 'Search Event', 'not_found' => 'No event found'),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true, 
            'show_in_menu'       => true, 
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true, 
            'hierarchical'       => false,
            'menu_position'      => 4,
            'supports'           => array('title','thumbnail','custom-fields')
        ),*/
        'hotels' => array(
            'labels'             => array('name' => 'Hotels/Hostels', 'add_new_item' => 'Add New Hotel/Hostel', 'edit_item' => 'Edit Hotel/Hostel', 'new_item' => 'New Hotel/Hostel', 'view_item' => 'View Hotel/Hostel', 'search_item' => 'Search Hotels/Hostels', 'not_found' => 'No hotels/hostels found'),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true, 
            'show_in_menu'       => true, 
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true, 
            'hierarchical'       => true,
            'menu_position'      => 0,
            'supports'           => array('title','editor','page-attributes')
        ),
        'classes' => array(
        		'labels'             => array('name' => 'Classes', 'add_new_item' => 'Add New Class', 'edit_item' => 'Edit Class', 'new_item' => 'New Class', 'view_item' => 'View Class', 'search_item' => 'Search Classes', 'not_found' => 'No classes found'),
        		'public'             => false,
        		'publicly_queryable' => false,
        		'show_ui'            => true,
        		'show_in_menu'       => true,
        		'query_var'          => true,
        		'rewrite'            => true,
        		'capability_type'    => 'page',
        		'has_archive'        => true,
        		'hierarchical'       => true,
        		'menu_position'      => 0,
        		'supports'           => array('title','thumbnail')
        ),
        'slots' => array(
        		'labels'             => array('name' => 'Slots', 'add_new_item' => 'Add New Slot', 'edit_item' => 'Edit Slot', 'new_item' => 'New Slot', 'view_item' => 'View Slot', 'search_item' => 'Search Slots', 'not_found' => 'No Slots found'),
        		'public'             => false,
        		'publicly_queryable' => false,
        		'show_ui'            => true,
        		'show_in_menu'       => true,
        		'query_var'          => true,
        		'rewrite'            => true,
        		'capability_type'    => 'page',
        		'has_archive'        => true,
        		'hierarchical'       => true,
        		'menu_position'      => 0,
        		'supports'           => array('title','page-attributes')
        ),
        'teachers' => array(
        		'labels'             => array('name' => 'Teachers', 'add_new_item' => 'Add New Teacher', 'edit_item' => 'Edit Teacher', 'new_item' => 'New Teacher', 'view_item' => 'View Teacher', 'search_item' => 'Search Teachers', 'not_found' => 'No Teachers found'),
        		'public'             => false,
        		'publicly_queryable' => false,
        		'show_ui'            => true,
        		'show_in_menu'       => true,
        		'query_var'          => true,
        		'rewrite'            => true,
        		'capability_type'    => 'page',
        		'has_archive'        => true,
        		'hierarchical'       => true,
        		'menu_position'      => 0,
        		'supports'           => array('title','editor','thumbnail','page-attributes'),
        		'taxonomies'         => array('category')
        ),
        'dance-teachers' => array(
        		'labels'             => array('name' => 'Dance Teachers', 'add_new_item' => 'Add New Dance Teacher', 'edit_item' => 'Edit Dance Teacher', 'new_item' => 'New Dance Teacher', 'view_item' => 'View Dance Teacher', 'search_item' => 'Search Dance Teachers', 'not_found' => 'No Dance Teachers found'),
        		'public'             => false,
        		'publicly_queryable' => false,
        		'show_ui'            => true,
        		'show_in_menu'       => true,
        		'query_var'          => true,
        		'rewrite'            => true,
        		'capability_type'    => 'page',
        		'has_archive'        => true,
        		'hierarchical'       => false,
        		'menu_position'      => 0,
        		'supports'           => array('title','editor','thumbnail')
        )
        /*
        'some_type' => array(
            'labels'             => array('name' => 'Some Type'),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true, 
            'show_in_menu'       => true, 
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true, 
            'hierarchical'       => false,
            'menu_position'      => 4,
            'supports'           => array('title','thumbnail','custom-fields')
        ),
        'some_type' => array(
            'labels'             => array('name' => 'Some Type'),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true, 
            'show_in_menu'       => true, 
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true, 
            'hierarchical'       => false,
            'menu_position'      => 4,
            'supports'           => array('title','thumbnail','custom-fields')
        )
        */
    ),



    /**
     *  Declare Custom Post Types.
     *  http://codex.wordpress.org/Function_Reference/register_taxonomy
     */
    'taxonomies' => array(
        /*
        array(
            'tax_name', 'postype_name', array(
            'hierarchical'    => false,
            'labels'          => array('name' => '<Tax Name>'),
            'show_ui'         => true,
            'query_var'       => true,
            'rewrite'         => array('slug' => 'tax-name'),
            )
        ),
        array(
            'tax_name', 'postype_name', array(
            'hierarchical'    => false,
            'labels'          => array('name' => '<Tax Name>'),
            'show_ui'         => true,
            'query_var'       => true,
            'rewrite'         => array('slug' => 'tax-name'),
            )
        )
        */
    )
);




/**
 *  Initialize the configuration array.
 */
function _rye_init($rye_config)
{
    global $rye_config;
    
    // Move all scripts to footer.
    if ($rye_config['place_javascript_in_footer']):
        remove_action('wp_head', 'wp_print_scripts');
        remove_action('wp_head', 'wp_print_head_scripts', 9);
        remove_action('wp_head', 'wp_enqueue_scripts', 1);
        add_action('wp_footer', 'wp_print_scripts', 5);
        add_action('wp_footer', 'wp_enqueue_scripts', 5);
        add_action('wp_footer', 'wp_print_head_scripts', 5);
    endif;
    
    // Queue JavaScripts.
    if ( ! is_admin()):
        foreach ($rye_config['javascripts'] as $name => $path):
            wp_deregister_script($name);
            
            wp_register_script($name, $path, array(), false, 
                $rye_config['place_javascript_in_footer']);
            
            wp_enqueue_script($name, $path, array(), false, 
                $rye_config['place_javascript_in_footer']);
        endforeach;
    endif;
    
    // Register Custom Menus.
    register_nav_menus($rye_config['menus']);
    
    // Register Sidebars.
    foreach ($rye_config['widgetized_regions'] as $region)
        register_sidebar($region);
    
    // Register Custom Post Types.
    foreach ($rye_config['post_types'] as $name => $type)
        register_post_type($name, $type);
    
    // Register Taxonomies.
    foreach($rye_config['taxonomies'] as $taxonomy)
        register_taxonomy($taxonomy[0], $taxonomy[1], $taxonomy[2]);
        
    // Register image sizes.
    foreach($rye_config['image_sizes'] as $name => $args)
        add_image_size($name, $args[0], $args[1], $args[2]);
    
    // Register theme support.
    foreach($rye_config['theme_support'] as $name => $args)
        add_theme_support($name, $args);
}


/**
 *  Hook the Rye initialization method with WordPress init.
 *  http://codex.wordpress.org/Function_Reference/add_action
 */
add_action('init', '_rye_init');




