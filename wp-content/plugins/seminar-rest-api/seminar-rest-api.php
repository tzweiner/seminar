<?php
/**
 * Plugin Name: Seminar REST Api
 * Description: Exposes Seminar-related data via a custom REST API namespace.
 * Version:     1.0.0
 * Author:      Tzvety Dosseva
 * Text Domain: seminar-rest-api
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Register Seminar REST routes.
 */
add_action('rest_api_init', function () {

    // Example: expose global ACF options (if ACF is active)
    register_rest_route(
        'seminar/v1',
        '/options',
        [
            'methods'             => 'GET',
            'callback'            => 'seminar_rest_api_get_acf_options',
            'permission_callback' => '__return_true', // Keep public, or tighten if needed
        ]
    );

    // Contact form endpoint with nonce authentication
    register_rest_route(
        'seminar/v1',
        '/contact',
        [
            'methods'             => 'POST',
            'callback'            => 'seminar_handle_contact_form',
            'permission_callback' => 'seminar_verify_nonce',
        ]
    );

    // Endpoint to get nonce for forms
    register_rest_route(
        'seminar/v1',
        '/nonce',
        [
            'methods'             => 'GET',
            'callback'            => 'seminar_get_nonce',
            'permission_callback' => '__return_true',
        ]
    );

    // Menu endpoint
    register_rest_route(
        'seminar/v1',
        '/nav-menu',
        [
            'methods'             => 'GET',
            'callback'            => 'seminar_get_main_menu',
            'permission_callback' => '__return_true',
        ]
    );

    // Top Bar Nav endpoint
    register_rest_route(
        'seminar/v1',
        '/top-bar-nav',
        [
            'methods'             => 'GET',
            'callback'            => 'seminar_get_top_bar_menu',
            'permission_callback' => '__return_true',
        ]
    );

    // Shortcode endpoint e.g. https://folkseminarplovdiv.net/wp-json/seminar/v1/shortcode/sound_bites
    register_rest_route(
        'seminar/v1',
        '/shortcode/(?P<shortcode>[a-zA-Z0-9_-]+)',
        [
            'methods'             => 'GET',
            'callback'            => 'seminar_execute_shortcode',
            'permission_callback' => '__return_true',
        ]
    );

    register_rest_route( 'seminar/v1', '/classes-and-teachers', array(
        'methods' => 'GET',
        'callback' => 'seminar_get_classes_and_teacher',
        'permission_callback' => '__return_true'
    ) );
});

/**
 * Callback: return ACF options as JSON.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function seminar_rest_api_get_acf_options(WP_REST_Request $request)
{
    // Safely check if ACF is available
    $fields = function_exists('get_fields') ? get_fields('option') : [];

    if (!$fields) {
        // Return an empty object instead of null/false
        $fields = new stdClass();
    }

    return new WP_REST_Response($fields, 200);
}

/**
 * Get nonce for form submissions
 */
function seminar_get_nonce(WP_REST_Request $request)
{
    return new WP_REST_Response([
        'nonce' => wp_create_nonce('seminar_form_nonce')
    ], 200);
}

/**
 * Verify nonce for form submissions
 */
function seminar_verify_nonce(WP_REST_Request $request)
{
    $nonce = $request->get_header('X-WP-Nonce') ?: $request->get_param('nonce');
    return wp_verify_nonce($nonce, 'seminar_form_nonce');
}

/**
 * Get Main Nav menu items
 */
function seminar_get_main_menu(WP_REST_Request $request)
{
    // Get all menus for debugging
    $menus = wp_get_nav_menus();
    $menu_names = [];
    foreach ($menus as $menu_obj) {
        $menu_names[] = $menu_obj->name;
    }
    
    $menu = wp_get_nav_menu_object('Main Nav');
    
    if (!$menu) {
        return new WP_REST_Response([
            'error' => 'Menu not found',
            'available_menus' => $menu_names
        ], 404);
    }
    
    $menu_items = wp_get_nav_menu_items($menu->term_id);
    
    if (!$menu_items) {
        return new WP_REST_Response([], 200);
    }
    
    $items = [];
    foreach ($menu_items as $item) {
        $items[] = [
            'id' => $item->ID,
            'title' => $item->title,
            'url' => $item->url,
            'target' => $item->target,
            'parent' => $item->menu_item_parent,
            'order' => $item->menu_order,
            'classes' => implode(' ', $item->classes),
            'description' => $item->description
        ];
    }
    
    return new WP_REST_Response($items, 200);
}

/**
 * Get Top Bar Nav menu items
 */
function seminar_get_top_bar_menu(WP_REST_Request $request)
{
    $menu = wp_get_nav_menu_object('Top Bar Nav');
    
    if (!$menu) {
        return new WP_Error('menu_not_found', 'Top Bar Nav menu not found', ['status' => 404]);
    }
    
    $menu_items = wp_get_nav_menu_items($menu->term_id);
    
    if (!$menu_items) {
        return new WP_REST_Response([], 200);
    }
    
    $items = [];
    foreach ($menu_items as $item) {
        $items[] = [
            'id' => $item->ID,
            'title' => $item->title,
            'url' => $item->url,
            'target' => $item->target,
            'parent' => $item->menu_item_parent,
            'order' => $item->menu_order,
            'classes' => implode(' ', $item->classes),
            'description' => $item->description
        ];
    }
    
    return new WP_REST_Response($items, 200);
}

/**
 * Execute shortcode and return result
 */
function seminar_execute_shortcode(WP_REST_Request $request)
{
    $shortcode = $request->get_param('shortcode');
    $atts = $request->get_params();
    unset($atts['shortcode']); // Remove shortcode name from attributes
    
    // Execute the shortcode
    $result = do_shortcode('[' . $shortcode . ']');
    
    if (empty($result)) {
        return new WP_Error('shortcode_not_found', 'Shortcode not found or returned empty', ['status' => 404]);
    }
    
    // Clean up the HTML - remove extra whitespace and decode entities
    $result = html_entity_decode($result, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $result = preg_replace('/\s+/', ' ', $result); // Replace multiple whitespace with single space
    $result = trim($result);
    
    return new WP_REST_Response([
        'shortcode' => $shortcode,
        'content' => $result
    ], 200);
}

/**
 * Handle contact form submission
 */
function seminar_handle_contact_form(WP_REST_Request $request)
{
    $name = sanitize_text_field($request->get_param('name'));
    $email = sanitize_email($request->get_param('email'));
    $message = sanitize_textarea_field($request->get_param('message'));
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        return new WP_Error('missing_fields', 'All fields are required.', ['status' => 400]);
    }
    
    // Send email or save to database
    $to = get_option('admin_email');
    $subject = 'Contact Form Submission from ' . $name;
    $body = "Name: {$name}\nEmail: {$email}\nMessage: {$message}";
    
    $sent = wp_mail($to, $subject, $body);
    
    if ($sent) {
        return new WP_REST_Response(['message' => 'Form submitted successfully'], 200);
    } else {
        return new WP_Error('email_failed', 'Failed to send email.', ['status' => 500]);
    }
}

function seminar_get_classes_and_teacher( WP_REST_Request $request ) {
    $posts = get_posts( array(
        'posts_per_page' => -1,
        'post_type' => 'classes',
    ) );

    $data = [];
    foreach ( $posts as $post ) {
        $post_data = get_post( $post ); // Get the standard post data
        $custom_field_value = get_post_meta( $post->ID, 'your_custom_field_key', true );

        $data[] = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'your_custom_field' => $custom_field_value,
        ];
    }

    return new WP_REST_Response( $data, 200 );
}

// Ensure a global ACF Options page exists when ACF is active.
if ( function_exists('acf_add_options_page') ) {
    acf_add_options_page();
}

function register_custom_post_types()
{
    // Register ACF Options as a post type for REST API
    register_post_type('acf_options', [
        'labels' => [
            'name' => 'ACF Options',
            'singular_name' => 'ACF Option'
        ],
        'public' => false,
        'show_in_rest' => true,
        'rest_base' => 'acf-options',
        'supports' => ['custom-fields'],
    ]);

    // Register Hotels/Hostels
    register_post_type('hotels', [
        'labels' => [
            'name' => 'Hotels/Hostels',
            'add_new_item' => 'Add New Hotel/Hostel',
            'edit_item' => 'Edit Hotel/Hostel',
            'new_item' => 'New Hotel/Hostel',
            'view_item' => 'View Hotel/Hostel',
            'search_item' => 'Search Hotels/Hostels',
            'not_found' => 'No hotels/hostels found'
        ],
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'hotels',
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => true,
        'supports' => ['title', 'editor', 'page-attributes', 'custom-fields']
    ]);

    // Register Classes
    register_post_type('classes', [
        'labels' => [
            'name' => 'Classes',
            'add_new_item' => 'Add New Class',
            'edit_item' => 'Edit Class',
            'new_item' => 'New Class',
            'view_item' => 'View Class',
            'search_item' => 'Search Classes',
            'not_found' => 'No classes found'
        ],
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'classes',
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => true,
        'supports' => ['title', 'thumbnail', 'custom-fields']
    ]);

    // Register Slots
    register_post_type('slots', [
        'labels' => [
            'name' => 'Slots',
            'add_new_item' => 'Add New Slot',
            'edit_item' => 'Edit Slot',
            'new_item' => 'New Slot',
            'view_item' => 'View Slot',
            'search_item' => 'Search Slots',
            'not_found' => 'No Slots found'
        ],
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'slots',
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => true,
        'supports' => ['title', 'page-attributes', 'custom-fields']
    ]);

    // Register Teachers
    register_post_type('teachers', [
        'labels' => [
            'name' => 'Teachers',
            'add_new_item' => 'Add New Teacher',
            'edit_item' => 'Edit Teacher',
            'new_item' => 'New Teacher',
            'view_item' => 'View Teacher',
            'search_item' => 'Search Teachers',
            'not_found' => 'No Teachers found'
        ],
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'teachers',
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields'],
        'taxonomies' => ['category', 'post_tag']
    ]);

    // Register Dance Teachers
    register_post_type('dance_teachers', [
        'labels' => [
            'name' => 'Dance Teachers',
            'singular_name' => 'Dance Teacher'
        ],
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'dance-teachers',
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ]);

    // Register Team
    register_post_type('team', [
        'labels' => [
            'name' => 'Team',
            'add_new_item' => 'Add New Team Member',
            'edit_item' => 'Edit Team Member',
            'new_item' => 'New Team Member',
            'view_item' => 'View Team Member',
            'search_item' => 'Search Team',
            'not_found' => 'No Team found'
        ],
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'team',
        'supports' => ['title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields'],
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => true
    ]);
}
add_action('init', 'register_custom_post_types', 0);
add_theme_support('post-thumbnails', array('post', 'page', 'team'));

// Flush rewrite rules on plugin activation
register_activation_hook(__FILE__, 'seminar_flush_rewrite_rules');
function seminar_flush_rewrite_rules() {
    register_custom_post_types();
    flush_rewrite_rules();
}

// Allow 'menu_order' as orderby parameter in REST API queries
add_filter('rest_endpoints', function($endpoints) {
    foreach ($endpoints as $route => &$endpoint) {
        foreach ($endpoint as &$handler) {
            if (isset($handler['args']['orderby'])) {
                $handler['args']['orderby']['enum'][] = 'menu_order';
            }
        }
    }
    return $endpoints;
});

// Security: Restrict REST API write access
function seminar_restrict_rest_api_access($result, $server, $request) {
    $route = $request->get_route();
    $method = $request->get_method();
    
    // Allow GET requests (read-only)
    if ($method === 'GET') {
        return $result;
    }
    
    // Allow our custom endpoints with nonce verification
    if (strpos($route, '/seminar/v1/') === 0) {
        return $result; // Let the endpoint handle its own permission_callback
    }
    
    // Block POST, PUT, DELETE for non-authenticated users on WP core endpoints
    if (!is_user_logged_in()) {
        return new WP_Error(
            'rest_forbidden',
            'Write access forbidden for anonymous users.',
            ['status' => 403]
        );
    }
    
    return $result;
}
add_filter('rest_pre_dispatch', 'seminar_restrict_rest_api_access', 10, 3);

// Security: Disable REST API user enumeration
function seminar_disable_rest_user_endpoints($endpoints) {
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
}
add_filter('rest_endpoints', 'seminar_disable_rest_user_endpoints');

// Security: Add rate limiting headers
function seminar_add_security_headers() {
    if (strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
    }
}
add_action('send_headers', 'seminar_add_security_headers');
