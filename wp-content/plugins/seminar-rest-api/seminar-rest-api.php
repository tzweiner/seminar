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

    // Add more routes here as needed, for example:
    // register_rest_route('seminar/v1', '/something', [...]);
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
}
add_action('init', 'register_custom_post_types', 0);

// Flush rewrite rules on plugin activation
register_activation_hook(__FILE__, 'seminar_flush_rewrite_rules');
function seminar_flush_rewrite_rules() {
    register_custom_post_types();
    flush_rewrite_rules();
}

// Migrate existing dance-teachers posts to dance_teachers
function migrate_dance_teachers_posts() {
    global $wpdb;
    $wpdb->update(
        $wpdb->posts,
        ['post_type' => 'dance_teachers'],
        ['post_type' => 'dance-teachers']
    );
}
register_activation_hook(__FILE__, 'migrate_dance_teachers_posts');