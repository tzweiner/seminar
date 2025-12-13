<?php
/*
Plugin Name: Seminar Registration Admin Tool
Description: Minimal, modular admin UI scaffold for Seminar Registration Admin (step 1)
Version: 2.1
Author: Tzvety Dosseva
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* paths & urls used by the plugin */
if ( ! defined( 'SR_PLUGIN_DIR' ) ) {
    define( 'SR_PLUGIN_DIR', __DIR__ );
}
if ( ! defined( 'SR_INCLUDES_DIR' ) ) {
    define( 'SR_INCLUDES_DIR', SR_PLUGIN_DIR . '/includes' );
}
if ( ! defined( 'SR_ASSETS_URL' ) ) {
    define( 'SR_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets' );
}

/* load SQL repository and class if they exist (created in later steps) */
if ( file_exists( SR_INCLUDES_DIR . '/queries.php' ) ) {
    require_once SR_INCLUDES_DIR . '/queries.php';
}

if ( file_exists( SR_INCLUDES_DIR . '/class-seminar-registration-admin.php' ) ) {
    require_once SR_INCLUDES_DIR . '/class-seminar-registration-admin.php';
}

/* instantiate and hook admin callbacks only when the class is available */
if ( class_exists( 'Seminar_Registration_Admin' ) ) {
    $seminar_registration_admin = new Seminar_Registration_Admin();

    add_action( 'admin_menu', array( $seminar_registration_admin, 'admin_menu' ) );
    add_action( 'admin_enqueue_scripts', array( $seminar_registration_admin, 'enqueue_assets' ) );

    /* later steps will add specific AJAX hooks or activation hooks here */
}
