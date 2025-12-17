<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Seminar_Admin_Tools_Menu {

    const SLUG = 'seminar-admin-tools';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function register_menu() {

        // 1️⃣ Top-level menu = Dashboard
        add_menu_page(
            'Seminar Admin Tools',          // Page title
            'Seminar Admin Tools',          // Menu title
            'manage_options',               // Capability
            self::SLUG,                     // Menu slug
            [ $this, 'render_dashboard' ],  // Dashboard callback
            'dashicons-clipboard',          // Icon
            65                               // Position
        );

        // 2️⃣ First submenu = Dashboard (prevents WP auto-creating duplicate)
        add_submenu_page(
            self::SLUG,
            'Dashboard',                     // Page title
            'Dashboard',                     // Menu label
            'manage_options',
            self::SLUG,
            [ $this, 'render_dashboard' ]
        );

        // 3️⃣ Tools definitions
        $tools = [
            [ 'slug' => 'seminar-registration-confirmed', 'label' => 'Confirmed Registrations', 'instance' => 'seminar_registration_confirmed' ],
            [ 'slug' => 'seminar-all-registrations',       'label' => 'All Registrations',       'instance' => 'seminar_all_registrations' ],
            [ 'slug' => 'seminar-registration-media-orders',           'label' => 'Video Orders',           'instance' => 'seminar_registration_media_orders' ],
            [ 'slug' => 'seminar-transportation-counts',  'label' => 'Transportation Counts',  'instance' => 'seminar_transportation_counts' ],
            [ 'slug' => 'seminar-onsite-payment',         'label' => 'Onsite Payments',        'instance' => 'seminar_onsite_payment' ],
            [ 'slug' => 'seminar-counts-per-class',       'label' => 'Counts Per Class',       'instance' => 'seminar_counts_per_class' ],
            [ 'slug' => 'seminar-rented',                 'label' => 'Rented Instruments',     'instance' => 'seminar_rented' ],
            [ 'slug' => 'seminar-class-level-counts',     'label' => 'Class Level Counts',     'instance' => 'seminar_class_level_counts' ],
            [ 'slug' => 'seminar-registrant-by-id',      'label' => 'Registrant by ID',       'instance' => 'seminar_registrant_by_id' ],
//            [
//                'slug'     => 'seminar-bulgarian-registrants',
//                'label'    => 'Bulgarian Registrants',
//                'instance' => 'seminar_bulgarian_registrants',
//            ],
            [ 'slug' => 'seminar-cancel-registration-by-id',     'label' => 'Cancel Registration by ID',    'instance' => 'seminar_cancel_registration_by_id' ],
        ];

        // 4️⃣ Add each tool as a submenu that calls the actual sub-plugin page
        foreach ( $tools as $tool ) {
            add_submenu_page(
                self::SLUG,
                $tool['label'],  // Page title
                $tool['label'],  // Menu label
                'manage_options',
                $tool['slug'],
                function() use ( $tool ) {
                    global ${$tool['instance']};
                    if ( isset( ${$tool['instance']} ) ) {
                        ${$tool['instance']}->render_page();
                    } else {
                        echo '<div class="wrap"><p>Tool not available.</p></div>';
                    }
                }
            );
        }
    }

    // ------------------------------
    // Load CSS only on dashboard
    // ------------------------------
    public function enqueue_assets( $hook ) {
        if ( $hook !== 'toplevel_page_' . self::SLUG ) {
            return;
        }

        wp_enqueue_style(
            'sr-admin-dashboard',
            SR_ASSETS_URL . '/css/common.css',
            [],
            '1.1'
        );
    }

    // ------------------------------
    // Dashboard page (top-level)
    // ------------------------------
    public function render_dashboard() {
        echo '<div class="wrap">';
        echo '<h1>Seminar Admin Tools</h1>';
        echo '<p>Select a tool below.</p>';

        $tools = [
            [ 'slug' => 'seminar-registration-confirmed', 'label' => 'Confirmed Registrations', 'desc' => 'View and export confirmed registrants.' ],
            [ 'slug' => 'seminar-all-registrations',       'label' => 'All Registrations',       'desc' => 'View all registrations, including cancelled.' ],
            [ 'slug' => 'seminar-registration-media-orders',           'label' => 'Media Orders',           'desc' => 'Review and export media orders.' ],
            [ 'slug' => 'seminar-transportation-counts',  'label' => 'Transportation Counts',  'desc' => 'Bus and transportation summaries.' ],
            [ 'slug' => 'seminar-onsite-payment',         'label' => 'Onsite Payments',        'desc' => 'Track payments collected onsite.' ],
            [ 'slug' => 'seminar-counts-per-class',       'label' => 'Counts Per Class',       'desc' => 'Registration totals per class.' ],
            [ 'slug' => 'seminar-rented',                 'label' => 'Rented Items',           'desc' => 'View rented equipment and materials.' ],
            [ 'slug' => 'seminar-class-level-counts',     'label' => 'Class Level Counts',     'desc' => 'Counts grouped by class level.' ],
            [ 'slug' => 'seminar-registrant-by-id',      'label' => 'Registrant by ID',       'desc' => 'Lookup a registrant by ID.' ],
//            [ 'slug' => 'seminar-bulgarian-registrants',      'label' => 'Bulgarian Registrants',       'desc' => 'List of Bulgarian registrants.' ],
            [ 'slug' => 'seminar-cancel-registration-by-id',     'label' => 'Cancel Registration',    'desc' => 'Cancel a registration by ID.' ],
        ];

        echo '<div class="sr-admin-grid">';
        foreach ( $tools as $tool ) {
            $url = admin_url( 'admin.php?page=' . $tool['slug'] );

            echo '<div class="sr-admin-card">';
            echo '<h2><a href="' . esc_url( $url ) . '">' . esc_html( $tool['label'] ) . '</a></h2>';
            echo '<p>' . esc_html( $tool['desc'] ) . '</p>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
}

new Seminar_Admin_Tools_Menu();
