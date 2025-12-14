<?php
/*
Plugin Name: Seminar Registration Admin Tool
Description: Admin UI for Seminar Registration. View and export registrant and classes data.
Version: 2.2
Author: Tzvety Dosseva
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* plugin paths & urls */
if ( ! defined( 'SR_PLUGIN_DIR' ) ) {
    define( 'SR_PLUGIN_DIR', __DIR__ );
}
if ( ! defined( 'SR_INCLUDES_DIR' ) ) {
    define( 'SR_INCLUDES_DIR', SR_PLUGIN_DIR . '/includes' );
}
if ( ! defined( 'SR_ASSETS_URL' ) ) {
    define( 'SR_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets' );
}

/* load SQL repository */
if ( file_exists( SR_INCLUDES_DIR . '/queries.php' ) ) {
    require_once SR_INCLUDES_DIR . '/queries.php';
}

/* load admin classes (each class file will instantiate itself when required) */
if ( file_exists( SR_INCLUDES_DIR . '/class-seminar-registration-confirmed.php' ) ) {
    require_once SR_INCLUDES_DIR . '/class-seminar-registration-confirmed.php';
}

if ( file_exists( SR_INCLUDES_DIR . '/class-seminar-all-registrations.php' ) ) {
    require_once SR_INCLUDES_DIR . '/class-seminar-all-registrations.php';
}

if ( file_exists( SR_INCLUDES_DIR . '/class-seminar-media-orders.php' ) ) {
    require_once SR_INCLUDES_DIR . '/class-seminar-media-orders.php';
}

if ( file_exists( SR_INCLUDES_DIR . '/class-seminar-transportation-counts.php' ) ) {
    require_once SR_INCLUDES_DIR . '/class-seminar-transportation-counts.php';
}

if ( file_exists( SR_INCLUDES_DIR . '/class-seminar-onsite-payment.php' ) ) {
    require_once SR_INCLUDES_DIR . '/class-seminar-onsite-payment.php';
}

if ( file_exists( SR_INCLUDES_DIR . '/class-seminar-counts-per-class.php' ) ) {
    require_once SR_INCLUDES_DIR . '/class-seminar-counts-per-class.php';
}

//if ( file_exists( SR_INCLUDES_DIR . '/class-seminar-bulgarian-registrants.php' ) ) {
//    require_once SR_INCLUDES_DIR . '/class-seminar-bulgarian-registrants.php';
//}

if ( file_exists( SR_INCLUDES_DIR . '/class-cancel-registration-by-id.php' ) ) {
    require_once SR_INCLUDES_DIR . '/class-cancel-registration-by-id.php';
}
