<?php
/**
 * Seminar Angular Theme functions
 */

// Remove ALL WordPress default scripts and styles
function seminar_ng_remove_all_wp_assets() {
    // Don't run in admin
    if (is_admin()) {
        return;
    }

    // Deregister WordPress scripts
    wp_deregister_script('jquery');
    wp_deregister_script('jquery-core');
    wp_deregister_script('jquery-migrate');
    wp_deregister_script('wp-embed');
    
    // Remove all other enqueued scripts and styles
    global $wp_scripts, $wp_styles;
    if ($wp_scripts) $wp_scripts->queue = [];
    if ($wp_styles) $wp_styles->queue = [];

    // Remove emoji detection
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Remove WordPress embed script
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // Remove REST API links from head
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_resource_hints', 2);

    // Remove generator meta tag
    remove_action('wp_head', 'wp_generator');
}
add_action('wp_enqueue_scripts', 'seminar_ng_remove_all_wp_assets', 999);

// Hide admin bar completely on front-end
add_filter('show_admin_bar', '__return_false');

// Enqueue ONLY Angular assets
function seminar_ng_enqueue_assets() {
    // Don't run in admin
    if (is_admin()) {
        return;
    }

    $theme_dir = get_stylesheet_directory();
    $theme_uri = get_stylesheet_directory_uri();
    $angular_dir = $theme_dir . '/angular';

    // Find and enqueue styles-*.css
    $styles = glob($angular_dir . '/styles-*.css');
    if (!empty($styles)) {
        $styles_file = basename($styles[0]);
        wp_enqueue_style(
            'seminar-ng-styles',
            $theme_uri . '/angular/' . $styles_file,
            [],
            filemtime($angular_dir . '/' . $styles_file)
        );
    }

    // No separate polyfills or runtime files needed - bundled in main.js

    // Find and enqueue main-*.js
    $main = glob($angular_dir . '/main-*.js');
    if (!empty($main)) {
        $main_file = basename($main[0]);
        wp_enqueue_script(
            'seminar-ng-main',
            $theme_uri . '/angular/' . $main_file,
            [],
            filemtime($angular_dir . '/' . $main_file),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'seminar_ng_enqueue_assets', 1000);


