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
        'methods' => 'POST',
        'callback' => 'seminar_get_classes_and_teachers',
        'permission_callback' => '__return_true',
    ) );

    register_rest_route( 'seminar/v1', '/save-registrants', array(
        'methods' => 'POST',
        'callback' => 'seminar_save_registrants',
        'permission_callback' => function(WP_REST_Request $request) {
            $nonce = $request->get_header('X-WP-Nonce');

            if (empty($nonce)) {
                return new WP_Error('missing_nonce', 'Nonce is required', array('status' => 403));
            }

            if (!wp_verify_nonce($nonce, 'wp_rest')) {
                return new WP_Error('invalid_nonce', 'Invalid nonce', array('status' => 403));
            }

            return true;
        }
    ) );

    register_rest_route( 'seminar/v1', '/confirm-registrants', array(
        'methods' => 'POST',
        'callback' => 'seminar_confirm_registrants',
        'permission_callback' => function(WP_REST_Request $request) {
            $nonce = $request->get_header('X-WP-Nonce');

            if (empty($nonce)) {
                return new WP_Error('missing_nonce', 'Nonce is required', array('status' => 403));
            }

            if (!wp_verify_nonce($nonce, 'wp_rest')) {
                return new WP_Error('invalid_nonce', 'Invalid nonce', array('status' => 403));
            }

            return true;
        }
    ) );

    register_rest_route('seminar/v1', '/save-registration-event',
        [
            'methods'             => 'POST',
            'callback'            => 'seminar_save_registration_event',
            'permission_callback' => function(WP_REST_Request $request) {
                $nonce = $request->get_header('X-WP-Nonce');

                if (empty($nonce)) {
                    return new WP_Error('missing_nonce', 'Nonce is required', array('status' => 403));
                }

                if (!wp_verify_nonce($nonce, 'wp_rest')) {
                    return new WP_Error('invalid_nonce', 'Invalid nonce', array('status' => 403));
                }

                return true;
            }
        ]
    );

    register_rest_route( 'seminar/v1', '/dance-teachers', array(
        'methods' => 'POST',
        'callback' => 'seminar_get_dance_teacher',
        'permission_callback' => '__return_true'
    ) );

    register_rest_field(
        'teachers', // Or 'page', 'product', or your custom post type
        'featured_image_url', // The name of your custom field
        array(
            'get_callback'    => 'seminar_get_featured_image_url_callback',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field(
        'team',
        'featured_image_url',
        array(
            'get_callback'    => 'seminar_get_featured_image_url_callback',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field(
        'classes',
        'featured_image_url',
        array(
            'get_callback'    => 'seminar_get_featured_image_url_callback',
            'update_callback' => null,
            'schema'          => null,
        )
    );
});

// Bypass cookie authentication for public seminar endpoints
add_filter('rest_authentication_errors', function($result) {
    if (!empty($result)) {
        return $result;
    }

    $route = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($route, '/seminar/v1/save-registrants') !== false ||
        strpos($route, '/seminar/v1/confirm-registrants') !== false) {
        return true;
    }

    return $result;
});

function seminar_get_featured_image_url_callback( $object, $field_name, $request ) {
    if ( empty( $object['featured_media'] ) ) {
        return null; // No featured image set
    }

    $image_id = $object['featured_media'];
    $image_url = wp_get_attachment_image_url( $image_id, 'full' ); // 'full' for the original size, or specify a different size

    return $image_url;
}

/**
 * Callback: return ACF options as JSON.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function seminar_rest_api_get_acf_options(WP_REST_Request $request)
{
    if (!function_exists('get_field_objects')) {
        return new WP_REST_Response(new stdClass(), 200);
    }

    $field_objects = get_field_objects('option');
    $processed_fields = [];

    if ($field_objects) {
        foreach ($field_objects as $field_name => $field_object) {
            $value = get_field($field_name, 'option');
            if ($field_object['type'] === 'true_false') {
                $processed_fields[$field_name] = (bool) $value;
            } else {
                $processed_fields[$field_name] = $value;
            }
        }
    }

    return new WP_REST_Response($processed_fields ?: new stdClass(), 200);
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

function seminar_save_registration_event( WP_REST_Request $request ) {
    global $wpdb;

    $payload = $request->get_json_params();
    $participants = $payload['participants'] ?? [];
    if ( empty( $participants ) ) {
        return new WP_REST_Response( [ 'success' => false, 'error' => 'No participants provided' ], 400 );
    }

    $primary_participant = null;
    foreach ( $participants as $index => $p ) {
        if (empty( $p['isAdditional'] )) {
            $primary_participant = $p;
            break;
        }
    }
    if ( !$primary_participant ) {
        return new WP_REST_Response( [ 'success' => false, 'error' => 'No primary participants provided' ], 400 );
    }

    // sanitize event-level fields
    $submitted_at = sanitize_text_field( $payload['submittedAt'] ?? current_time( 'mysql' ) );
    $reg_year = intval( $primary_participant['regYear'] ?? 0 );
    $address1 = sanitize_text_field( $primary_participant['address'] ?? '' );
    $address2 = sanitize_text_field( $primary_participant['address2'] ?? '' );
    $city = sanitize_text_field( $primary_participant['city'] ?? '' );
    $state = sanitize_text_field( $primary_participant['stateProvince'] ?? '' );
    $zip = sanitize_text_field( $primary_participant['zipPostal'] ?? '' );
    $country = sanitize_text_field( $primary_participant['country'] ?? '' );
    $phone = sanitize_text_field( $primary_participant['phoneNumber'] ?? '' );
    $email = sanitize_email( $primary_participant['email'] ?? '' );
    $emergency = sanitize_text_field( $primary_participant['emergencyContact'] ?? '' );
    $payment = sanitize_text_field( $primary_participant['paymentMethod'] ?? '' );
    $registration_status = 'confirmed';

    $table_events = $wpdb->prefix . 'Seminar_registration_events';
    $table_registrants = $wpdb->prefix . 'Seminar_registrants';
    $table_classes = $wpdb->prefix . 'Seminar_classes';

    $primary_registrant_id = null;
    $participant_count = count( $participants );

    // Start transaction
    $wpdb->query( 'START TRANSACTION' );
    try {
        // Insert event row
        $wpdb->insert(
            $table_events,
            [
                'registration_date' => $submitted_at,
                'reg_year' => $reg_year,
                'address1' => $address1,
                'address2' => $address2,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'country' => $country,
                'phone' => $phone,
                'email' => $email,
                'emergency' => $emergency,
                'payment' => $payment,
                'registration_status' => $registration_status,
                'registration_email_sent' => 0
            ],
            [ '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d' ]
        );
        if ( $wpdb->last_error ) {
            throw new Exception( 'Event insert failed: ' . $wpdb->last_error );
        }
        $registration_event_id = intval( $wpdb->insert_id );

        // Insert registrants and classes
        foreach ( $participants as $index => $p ) {
            $numDays = $p['seminarDays'];
            if ($numDays == 'all') {
                $numDays = 6;
            }
            $is_primary = empty( $p['isAdditional'] ) ? 1 : 0;
            $first_name = sanitize_text_field( $p['firstName'] ?? '' );
            $last_name = sanitize_text_field( $p['lastName'] ?? '' );
            $num_days = intval( $numDays ?? 0 );
            $gala = !empty( $p['galaDinner'] ) ? 1 : 0;
            $meal_option = sanitize_text_field( $p['dinnerType'] ?? '' );
            $age = sanitize_text_field( $p['registrationType'] ?? '' );
            $is_eefc = !empty( $p['eefcMember'] ) ? 1 : 0;
//            $is_bulgarian = !empty( $p['isBulgarian'] ) ? 1 : 0;
            $media = $p['media'] ? 1 : 0;
            $balance = floatval( $p['total'] ?? 0 );

            $transport_map = [
                'no' => 0,
                'plovdiv-koprivshtitsa' => 1,
                'koprivshtitsa-sofia' => 2,
                'both' => 3
            ];
            $transport_string = sanitize_text_field( $p['transportation'] ?? 'no' );
            $transport = $transport_map[$transport_string] ?? 0;

            $wpdb->insert(
                $table_registrants,
                [
                    'registration_event_id' => $registration_event_id,
                    'is_primary' => $is_primary,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'num_days' => $num_days,
                    'gala' => $gala,
                    'meal_option' => $meal_option,
                    'age' => $age,
                    'is_eefc' => $is_eefc,
//                    'is_bulgarian' => $is_bulgarian,
                    'transport' => $transport,
                    'media' => $media,
                    'balance' => $balance
                ],
                [ '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%d', '%f' ]
            );
            if ( $wpdb->last_error ) {
                throw new Exception( 'Registrant insert failed: ' . $wpdb->last_error );
            }
            $registrant_id = intval( $wpdb->insert_id );

            if ( $is_primary && $primary_registrant_id === null ) {
                $primary_registrant_id = $registrant_id;
            }

            // classes per registrant (if provided)
            if ( !empty( $p['selectedClasses'] ) && is_array( $p['selectedClasses'] ) ) {
                foreach ( $p['selectedClasses'] as $class ) {
                    $rent = ( isset( $class['rent_bring'] ) && $class['rent_bring'] === 'rent' ) ? 1 : 0;
                    $level = sanitize_text_field( $class['level'] ?? '' );
                    $class_id = intval( $class['id'] ?? 0 );

                    $wpdb->insert(
                        $table_classes,
                        [
                            'class_id' => $class_id,
                            'rent' => $rent,
                            'level' => $level,
                            'registrant_id' => $registrant_id
                        ],
                        [ '%d', '%d', '%s', '%d' ]
                    );

                    if ( $wpdb->last_error ) {
                        throw new Exception( 'Class insert failed: ' . $wpdb->last_error );
                    }
                }
            }
        }

        // Commit
        $wpdb->query( 'COMMIT' );

    } catch ( Exception $e ) {
        $wpdb->query( 'ROLLBACK' );
        return new WP_REST_Response( [
            'success' => false,
            'error' => $e->getMessage()
        ], 500 );
    }

    // schedule async email job (does not affect success)
    if ( ! empty( $registration_event_id ) ) {
        if ( ! wp_next_scheduled( 'seminar_send_registration_email_event', [ $registration_event_id ] ) ) {
            wp_schedule_single_event( time() + 10, 'seminar_send_registration_email_event', [ $registration_event_id ] );
        }
    }

    // Calculate total balance from all participants
    $total_balance = 0;
    foreach ($participants as $p) {
        $total_balance += floatval($p['total'] ?? 0);
    }

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Registration saved successfully',
        'registration_event_id' => $registration_event_id,
        'primary_registrant_id' => $primary_registrant_id,
        'participant_count' => $participant_count,
        'total_balance' => $total_balance,
        'payment_method' => $payment
    ], 200);
}

// Async worker: load event + registrants + classes, call existing send_registration_email(), update event flags
add_action( 'seminar_send_registration_email_event', function( $registration_event_id ) {
    global $wpdb;

    $table_events      = $wpdb->prefix . 'Seminar_registration_events';
    $table_registrants = $wpdb->prefix . 'Seminar_registrants';
    $table_classes     = $wpdb->prefix . 'Seminar_classes';

    $registration_event_id = intval( $registration_event_id );
    if ( $registration_event_id <= 0 ) {
        return;
    }

    $event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_events} WHERE registration_event_id = %d", $registration_event_id ) );
    if ( ! $event ) {
        return;
    }

    // Get registrants, primary first
    $registrants = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$table_registrants} WHERE registration_event_id = %d ORDER BY is_primary DESC, registrant_id ASC",
        $registration_event_id
    ) );
    if ( empty( $registrants ) ) {
        return;
    }

    // Fetch classes for these registrants
    $registrant_ids = array_map( 'intval', wp_list_pluck( $registrants, 'registrant_id' ) );
    $classes = [];
    if ( ! empty( $registrant_ids ) ) {
        $in = implode( ',', $registrant_ids ); // sanitized by intval above
        $classes = $wpdb->get_results( "SELECT * FROM {$table_classes} WHERE registrant_id IN ({$in})" );
    }

    // Build classes map keyed by registrant_id
    $classes_by_registrant = [];
    foreach ( $classes as $c ) {
        $rid = intval( $c->registrant_id ?? 0 );
        if ( $rid === 0 ) {
            continue;
        }
        if ( ! isset( $classes_by_registrant[ $rid ] ) ) {
            $classes_by_registrant[ $rid ] = [];
        }
        $classes_by_registrant[ $rid ][] = $c;
    }

    // Attach classes to registrants
    $registrants_with_classes = array_map( function( $r ) use ( $classes_by_registrant ) {
        $obj = is_object( $r ) ? $r : (object) $r;
        $rid = intval( $obj->registrant_id ?? 0 );
        $obj->classes = $classes_by_registrant[ $rid ] ?? [];
        return $obj;
    }, $registrants );

    // Call email sender
    $email_sent = false;
    try {
        $email_sent = (bool) send_registration_email( $event, $registrants_with_classes );
    } catch ( Exception $e ) {
        $email_sent = false;
        error_log( 'send_registration_email error: ' . $e->getMessage() );
    }

    // Update event flags on success (do not roll back registration if email fails)
    if ( $email_sent ) {
        $updated = $wpdb->update(
            $table_events,
            [
                'registration_email_sent' => 1,
                'registration_email_sent_timestamp' => current_time( 'mysql', 1 )
            ],
            [ 'registration_event_id' => $registration_event_id ],
            [ '%d', '%s' ],
            [ '%d' ]
        );
        if ( $wpdb->last_error ) {
            error_log( 'Failed updating registration_email_sent for event ' . $registration_event_id . ': ' . $wpdb->last_error );
        }
    }
} );



//function seminar_save_registrants( WP_REST_Request $request ) {
//    global $wpdb;
//
//    $payload = $request->get_json_params();
//
//    // Early validation error - no data saved
//    if ( empty( $payload['participants'] ) ) {
//        return new WP_REST_Response( [
//            'success' => false,
//            'error' => 'No participants provided'
//        ], 400 );
//    }
//
//    $table = $wpdb->prefix . 'Seminar_registrations';
//    $table_classes = $wpdb->prefix . 'Seminar_classes';
//    $participants = $payload['participants'];
//    $submitted_at = sanitize_text_field( $payload['submittedAt'] );
//    $participant_count = count( $participants );
//
//    // Start transaction
//    $wpdb->query('START TRANSACTION');
//
//    try {
//        // Process each participant
//        foreach ( $participants as $index => $participant ) {
//            $is_primary = empty( $participant['isAdditional'] ) ? 1 : 0;
//
//            // Sanitize all fields
//            $first_name = sanitize_text_field( $participant['firstName'] );
//            $last_name = sanitize_text_field( $participant['lastName'] );
//            $address1 = sanitize_text_field( $participant['address'] ?? '' );
//            $address2 = sanitize_text_field( $participant['address2'] ?? '' );
//            $city = sanitize_text_field( $participant['city'] ?? '' );
//            $state = sanitize_text_field( $participant['stateProvince'] ?? '' );
//            $zip = sanitize_text_field( $participant['zipPostal'] ?? '' );
//            $country = sanitize_text_field( $participant['country'] ?? '' );
//            $phone = sanitize_text_field( $participant['phoneNumber'] ?? '' );
//            $email = sanitize_email( $participant['email'] ?? '' );
//            $emergency = sanitize_text_field( $participant['emergencyContact'] ?? '' );
//            $num_days = intval( $participant['seminarDays'] );
//            $gala = $participant['galaDinner'] ? 1 : 0;
//            $gala_option = sanitize_text_field( $participant['dinnerType'] ?? '' );
//            $age = sanitize_text_field( $participant['registrationType'] );
//            $eefc = $participant['eefcMember'] ? 1 : 0;
//            $payment = sanitize_text_field( $participant['paymentMethod'] );
//            $media = $p['media'] ? 1 : 0;
//            $reg_year = intval( $participant['regYear'] );
//            $balance = floatval( $participant['total'] ?? 0 );
//
//            // Convert transportation string to integer
//            $transport_map = [
//                'no' => 0,
//                'plovdiv-koprivshtitsa' => 1,
//                'koprivshtitsa-sofia' => 2,
//                'both' => 3
//            ];
//            $transport_string = sanitize_text_field( $participant['transportation'] ?? 'no' );
//            $transport = $transport_map[$transport_string] ?? 0;
//
//            // Insert into registrations table
//            $wpdb->insert(
//                $table,
//                [
//                    'is_primary' => $is_primary,
//                    'date' => $submitted_at,
//                    'reg_year' => $reg_year,
//                    'first_name' => $first_name,
//                    'last_name' => $last_name,
//                    'address1' => $address1,
//                    'address2' => $address2,
//                    'city' => $city,
//                    'state' => $state,
//                    'zip' => $zip,
//                    'country' => $country,
//                    'phone' => $phone,
//                    'email' => $email,
//                    'emergency' => $emergency,
//                    'num_days' => $num_days,
//                    'gala' => $gala,
//                    'meal_option' => $gala_option,
//                    'age' => $age,
//                    'is_eefc' => $eefc,
//                    'payment' => $payment,
//                    'transport' => $transport,
//                    'media' => $media,
//                    'cancel' => 0,
//                    'balance' => $balance
//                ],
//                [
//                    '%s', '%d', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s',
//                    '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s',
//                    '%d', '%s', '%d', '%d', '%s', '%d', '%d', '%f'
//                ]
//            );
//
//            if ( $wpdb->last_error ) {
//                throw new Exception( 'Participant insert failed: ' . $wpdb->last_error );
//            }
//
//            // Insert selected classes
//            if ( !empty( $participant['selectedClasses'] ) ) {
//                foreach ( $participant['selectedClasses'] as $class ) {
//                    $rent_bring = ( $class['rent_bring'] === 'rent' ) ? 1 : 0;
//                    $level = sanitize_text_field( $class['level'] ?? '' );
//
//                    $wpdb->insert(
//                        $table_classes,
//                        [
//                            'registration_event_id' => $registration_event_id,
//                            'class_id' => $class['id'],
//                            'rent' => $rent_bring,
//                            'level' => $level
//                        ],
//                        ['%d', '%d', '%d', '%s']
//                    );
//
//                    if ( $wpdb->last_error ) {
//                        throw new Exception( 'Class insert failed: ' . $wpdb->last_error );
//                    }
//                }
//            }
//        }
//
//        // Commit transaction if all successful
//        $wpdb->query('COMMIT');
//
//    } catch ( Exception $e ) {
//        // Rollback on any error
//        $wpdb->query('ROLLBACK');
//
//        return new WP_REST_Response( [
//            'success' => false,
//            'error' => $e->getMessage(),
//            'failed_at_participant' => $index ?? 0
//        ], 500 );
//    }
//
//    return new WP_REST_Response( [
//        'success' => true,
//        'message' => 'Registration saved successfully',
//        'registration_event_id' => $registration_event_id,
//        'participant_count' => $participant_count,
//        'registration_data' => $payload
//    ], 200 );
//}
//
//function seminar_confirm_registrants( WP_REST_Request $request ) {
//    global $wpdb;
//
//    $payload = $request->get_json_params();
//
//    if ( empty( $payload['registrationId'] ) ) {
//        return new WP_REST_Response( [
//            'success' => false,
//            'error' => 'No registration ID provided'
//        ], 400 );
//    }
//
//    $registration_event_id = sanitize_text_field( $payload['registrationId'] );
//    $table = $wpdb->prefix . 'Seminar_registrations';
//    $table_classes = $wpdb->prefix . 'Seminar_classes';
//
//    // Start transaction
//    $wpdb->query('START TRANSACTION');
//
//    try {
//        // Get all participants for this registration
//        $registration = $wpdb->get_results(
//            $wpdb->prepare(
//                "SELECT * FROM $table WHERE registration_event_id = %s ORDER BY reg_slot ASC",
//                $registration_event_id
//            )
//        );
//
//        if ( $wpdb->last_error ) {
//            throw new Exception( 'Database error fetching registration: ' . $wpdb->last_error );
//        }
//
//        if ( empty( $registration ) ) {
//            $wpdb->query('ROLLBACK');
//            return new WP_REST_Response( [
//                'success' => false,
//                'error' => 'Registration not found'
//            ], 404 );
//        }
//
//        // Update confirmed status
//        $updated = $wpdb->update(
//            $table,
//            ['confirmed' => 1],
//            ['registration_event_id' => $registration_event_id],
//            ['%d'],
//            ['%s']
//        );
//
//        if ( $wpdb->last_error ) {
//            throw new Exception( 'Database error updating confirmation: ' . $wpdb->last_error );
//        }
//
//        if ( $updated === false ) {
//            throw new Exception( 'Failed to update confirmation status' );
//        }
//
//        // Get all classes for this registration
//        $classes = $wpdb->get_results(
//            $wpdb->prepare(
//                "SELECT * FROM $table_classes WHERE registration_event_id = %s",
//                $registration_event_id
//            )
//        );
//
//        if ( $wpdb->last_error ) {
//            throw new Exception( 'Database error fetching classes: ' . $wpdb->last_error );
//        }
//
//        // Commit transaction before sending email
//        $wpdb->query('COMMIT');
//
//        // Send confirmation email (outside transaction)
//        $email_sent = send_registration_email( $registration, $classes );
//
//        return new WP_REST_Response( [
//            'success' => true,
//            'message' => 'Registration confirmed' . ( $email_sent ? ' and email sent' : ' but email failed' ),
//            'registration_event_id' => $registration_event_id,
//            'email_sent' => $email_sent
//        ], 200 );
//
//    } catch ( Exception $e ) {
//        // Rollback on any error
//        $wpdb->query('ROLLBACK');
//
//        return new WP_REST_Response( [
//            'success' => false,
//            'error' => $e->getMessage()
//        ], 500 );
//    }
//}

function send_registration_email( $event, $registrants ) {
    if ( empty( $event ) || empty( $registrants ) ) {
        return false;
    }

    // Normalize registrants to objects, ensure primary first
    $registrants = array_map( function( $r ) { return is_object( $r ) ? $r : (object) $r; }, $registrants );
    usort( $registrants, function( $a, $b ) {
        $pa = intval( $a->is_primary ?? 0 );
        $pb = intval( $b->is_primary ?? 0 );
        if ( $pa === $pb ) {
            return intval( $a->registrant_id ?? 0 ) - intval( $b->registrant_id ?? 0 );
        }
        return $pa > $pb ? -1 : 1;
    } );

    $primary = $registrants[0];
    $registration_event_id = intval( $event->registration_event_id ?? $primary->registration_event_id ?? 0 );
    $email = sanitize_email( $event->email ?? $primary->email ?? '' );
    if ( empty( $email ) ) {
        return false;
    }

    // Header
    $message = "Seminar Registration #" . intval( $primary->registration_event_id ) . "\r\n";
    $message .= "Email Address: " . $email . "\r\n";
    $message .= "Total: EURO " . get_registration_balance( $registration_event_id, $registrants ) . "\r\n";
    $paymentOpt = $event->payment ?? $primary->payment ?? 'N/A';
    if ($paymentOpt == 'bank') {
        $paymentOpt = 'Bank Transfer';
    }
    $message .= "Payment: " . $paymentOpt . "\r\n\r\n";

    // Payment instructions
    $payment_method = strtolower( trim( $event->payment ?? $primary->payment ?? '' ) );
    if ( $payment_method === 'onsite' || $payment_method === 'on-site' ) {
        $message .= "You have indicated that you will be submitting payment On-site (at the Music Academy). Please bring a copy of your registration for verification purposes.\r\n\r\n";
    } else {
        $message .= "You have indicated that you will be paying by Bank Transfer. Please provide your bank with the following routing information for your Bank Transfer:\r\n\r\n";
        $message .= "Bank Name:\tUNICREDIT BULBANK\r\n";
        $message .= "Bank Address:\t31 Ivan Vazov Str., Plovdiv 4000, Bulgaria\r\n";
        $message .= "Bank SWIFT code:\tUNCRBGSF\r\n";
        $message .= "Account Name:\tAcademy of Music Dance and Fine Arts Plovdiv\r\n";
        $message .= "Account Address:\t2 Todor Samodumov Str., Plovdiv 4000, Bulgaria\r\n";
        $message .= "Account Number (in EURO):\t3498641407\r\n";
        $message .= "IBAN Number (in EURO):\tBG56UNCR75273498641407\r\n\r\n";

        $late_deadline = function_exists('get_field') ? get_field( 'late_registration_deadline', 'option' ) : null;
        if ( $late_deadline ) {
            $message .= "Bank Transfers should be initiated before " . date( 'F j, Y', strtotime( $late_deadline ) ) . " to qualify for the fee calculated above.\r\n";
        }
        $message .= "When you have completed your Bank Transfer, please email us at contact@folkseminarplovdiv.net to indicate the date of your Bank Transfer. This will assist Seminar administrative staff with tracking your payment. If the Bank Transfer is originating from a bank account that is not in the registrant's name, or funds are being transferred for more than one registrant, please include the name of the person who owns the account, and all registrant names associated with your Bank Transfer.\r\n\r\n";
    }

    $message .= "Thank you and see you in Plovdiv!\r\nLarry Weiner & Dilyana Kurdova\r\nInternational Program Coordinators\r\n\r\n";

    // Event-level contact details (primary contact)
    $message .= "*** REGISTRATION " . intval( $event->registration_event_id ) . "***\r\n";
    $message .= "Registration Date: " . ( $event->registration_date ? get_date_from_gmt( $event->registration_date, 'M j, Y g:i A' ) : 'N/A' ) . "\r\n";
    $addr = trim( ($event->address1 ?? '') . ( !empty($event->address2) ? ', ' . $event->address2 : '' ) );
    $message .= "Address: " . ( $addr ?: 'N/A' ) . "\r\n";
    $message .= "City: " . ( $event->city ?? 'N/A' ) . ", " . ( $event->state ?? '' ) . ", " . ( $event->zip ?? '' ) . "\r\n";
    $message .= "Country: " . ( $event->country ?? 'N/A' ) . "\r\n";
    $message .= "Phone: " . ( $event->phone ?? 'N/A' ) . "\r\n";
    if ( ! empty( $event->emergency ) ) {
        $message .= "Emergency Info: " . $event->emergency . "\r\n";
    }
    $message .= "\r\n";

    // Per-registrant details and their classes (attached as ->classes)
    foreach ( $registrants as $index => $participant) {
        $message .= "\r\n*** REGISTRANT " . (intval( $index ) + 1) . " ***\r\n";
        $message .= "----------------------------------\r\n";
        $message .= "Name: " . trim( ($participant->first_name ?? '') . ' ' . ($participant->last_name ?? '') ) . "\r\n";

        $participant_classes = $participant->classes ?? [];
        if ( ! empty( $participant_classes ) ) {
            $message .= "\r\nCLASSES:\r\n\r\n";
            foreach ( $participant_classes as $class_row ) {
                $class_post = get_post( intval( $class_row->class_id ?? 0 ) );
                if ( $class_post ) {
                    $message .= $class_post->post_title;
                    $levels = function_exists('get_field') ? get_field( 'class_levels', $class_row->class_id ) : null;
                    $rent_options = function_exists('get_field') ? get_field( 'rent_bring', $class_row->class_id ) : null;

                    if ( $rent_options || $levels ) {
                        $parts = [];
                        if ( $rent_options && isset( $class_row->rent ) ) {
                            $rent_text = intval( $class_row->rent ) === 1 ? 'would like to rent' : 'bringing my own';
                            if ( intval( $class_row->rent ) === 1 ) {
                                $rental_fee = function_exists('get_field') ? get_field( 'rental_fee', $class_row->class_id ) : '';
                                if ( intval( $class_row->class_id ) === 249 ) {
                                    $parts[] = $rent_text . ', daily fee of ' . $rental_fee . ' EURO per day, if available - payable at first tupan class';
                                } else {
                                    $parts[] = $rent_text . ( $rental_fee ? ', daily fee of ' . $rental_fee . ' EURO applies' : '' );
                                }
                            } else {
                                $parts[] = $rent_text;
                            }
                        }
                        if ( $levels && ! empty( $class_row->level ) ) {
                            $parts[] = $class_row->level;
                        }
                        if ( ! empty( $parts ) ) {
                            $message .= ': ' . implode( ', ', $parts );
                        }
                    }
                    $message .= "\r\n";
                }
            }
            $message .= "\r\n";
        }

        // Media
        $message .= "Video ordered: " . ( intval( $participant->media ?? -1 ) === 1 ? 'Yes' : 'No' ) . "\r\n";

        // Gala dinner
        $message .= "Gala dinner: " . ( ! empty( $participant->gala ) ? 'Yes, ' . ( $participant->meal_option ?? '' ) : 'No' ) . "\r\n";

        // Transportation, conditional
        $show_transport = function_exists('get_field') ? get_field( 'show_koprivshtitsa_transportation_field', 'option' ) : null;
        if ( $show_transport && isset( $participant->transport ) && intval( $participant->transport ) !== -1 ) {
            $transport_labels = [
                0 => 'No',
                1 => 'Plovdiv to Koprivshtitsa',
                2 => 'Koprivshtitsa to Sofia',
                3 => 'Plovdiv to Koprivshtitsa and Koprivshtitsa to Sofia'
            ];
            $message .= "Transportation: " . ( $transport_labels[ intval( $participant->transport ) ] ?? 'No' ) . "\r\n";
        }

        $message .= "Days attending: " . intval( $participant->num_days ?? 0 ) . "\r\n";
        $message .= "Registration type: " . ( $participant->age ?? 'N/A' ) . "\r\n";
        $message .= "EEFC member: " . ( intval( $participant->is_eefc ?? 0 ) === 1 ? 'Yes' : 'No' ) . "\r\n";
//        $message .= "Bulgarian: " . ( intval( $participant->is_bulgarian ?? 0 ) === 1 ? 'Yes' : 'No' ) . "\r\n";

        if ( intval( $participant->is_primary ?? 0 ) === 1 ) {
            $message .= "Payment: " . ( $event->payment ?? $participant->payment ?? 'N/A' ) . "\r\n";
        }
        $message .= "Balance: EURO " . number_format( floatval( $participant->balance ?? 0 ), 2 ) . "\r\n";
    }

    // Send email
    $seminar_year = function_exists('get_field') ? get_field( 'seminar_year', 'option' ) : '';
    $site_name = get_bloginfo( 'name' );
    $admin_email = get_bloginfo( 'admin_email' );
//    $admin_email = 'tzvetydosseva@gmail.com';

    $headers = [
        'From: ' . $site_name . ( $seminar_year ? ' ' . $seminar_year : '' ) . ' <' . $admin_email . '>',
        'Reply-To: ' . $admin_email,
        'Cc: ' . $admin_email
    ];

    $subject = 'Folk Seminar Plovdiv ' . trim( $seminar_year ) . ' | Registration #' . intval( $primary->registration_event_id );

    return (bool) wp_mail( $email, $subject, $message, $headers );
}



function get_registration_balance( $registration_event_id, $registration ) {
    $total_balance = 0;
    foreach ( $registration as $participant ) {
        $total_balance += floatval( $participant->balance );
    }
    return $total_balance;
}

function seminar_get_classes_and_teachers( WP_REST_Request $request ) {
    // Get the data from the POST request body
    $payload = $request->get_params();
    // Sanitize and save data
    $year = sanitize_text_field( $payload['year'] );
    $class_categories = array (
        7,
        6,
        8,
        9
    ); // 'instrument', 'singing', 'dance', 'language'

    $classes = get_posts( array(
        'posts_per_page' => -1,
        'post_type' => 'classes',
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ) );

    $data = [];
    foreach ( $classes as $classItem ) {
        $className = $classItem->post_title;

        $teachers = get_posts( array(
            'post_type' => 'teachers',
            'post_status' => 'publish',
            'posts_per_page' => - 1,
//            'orderby' => 'menu_order',
            'order' => 'ASC',
            'tag' => $year,
            'meta_query' => array (
                array (
                    'key' => 'specialty',
                    'value' => $className == 'Bulgarian Singing' ? 'Singing' : $className,
                    'compare' => '='
                )
            )
        ) );

        foreach($teachers as $teacher){
            $thumbnail_url = null;
            $post_thumbnail_id = get_post_thumbnail_id( $teacher );
            if ($post_thumbnail_id) {
                $thumbnail_url = wp_get_attachment_image_url( $post_thumbnail_id, 'post-thumbnail' );
            }

            if ($thumbnail_url) {
                $teacher->thumbnail = $thumbnail_url;
            }
            $teacher->acf = get_fields( $teacher->ID );
        }

        $data[$className] = $teachers;
    }

    return new WP_REST_Response( $data, 200 );
}

function seminar_get_dance_teacher( WP_REST_Request $request ) {
    // Get the data from the POST request body
    $payload = $request->get_params();
    // Sanitize and save data
    $year = sanitize_text_field( $payload['year'] );

    $danceTeachers = get_posts( array(
        'post_type' => 'dance_teachers',
        'post_status' => 'publish',
        'posts_per_page' => - 1,
        'meta_key' => 'from_date',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'tag' => $year
    ) );

    $items = [];
    foreach ($danceTeachers as $item) {
        $thumbnail_url = null;
        $post_thumbnail_id = get_post_thumbnail_id( $item );
        if ($post_thumbnail_id) {
            $thumbnail_url = wp_get_attachment_image_url( $post_thumbnail_id, 'post-thumbnail' );
        }

        $items[] = [
            'id' => $item->ID,
            'title' => $item->post_title,
            'order' => $item->menu_order,
            'thumbnail' => $thumbnail_url,
            'content' => $item->post_content,
            'acf' => get_fields( $item->ID )
        ];
    }

//    $post_data = get_post( $classItem ); // Get the standard post data
//    $custom_field_value = get_post_meta( $post->ID, 'your_custom_field_key', true );

//    $data[] = [
//        'id' => $post->ID,
//        'title' => $post->post_title,
//        'content' => $post->post_content,
//        'your_custom_field' => $custom_field_value,
//    ];

    return new WP_REST_Response( $items, 200 );
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
        'taxonomies' => ['category', 'post_tag']
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
add_theme_support('post-thumbnails', array('post', 'page', 'team', 'classes', 'teachers', 'dance_teachers'));

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


/**
 * CACHING
 */


// PHP

// --- REST response caching for public GET endpoints ---
// Configure which routes (regex) to cache and their TTLs (seconds)
function seminar_rest_cacheable_routes()
{
    return [
        '~^/wp/v2/pages/\d+$~' => DAY_IN_SECONDS * 30, // single pages
        '~^/wp/v2/pages$~' => DAY_IN_SECONDS * 30,     // pages collection (with query string)
        '~^/wp/v2/classes$~' => DAY_IN_SECONDS * 30,   // custom post type collection
        '~^/wp/v2/classes-and-teachers$~' => DAY_IN_SECONDS * 30,   // custom post type collection
        '~^/wp/v2/dance-teachers$~' => DAY_IN_SECONDS * 30,   // custom post type collection
        '~^/wp/v2/classes\?~' => DAY_IN_SECONDS * 30,
        '~^/wp/v2/tags$~' => DAY_IN_SECONDS * 30,            // tags
        '~^/wp/v2/categories$~' => DAY_IN_SECONDS * 30,      // categories
        '~^/seminar/v1/nav-menu$~' => DAY_IN_SECONDS * 30,
        '~^/seminar/v1/top-bar-nav$~' => DAY_IN_SECONDS * 30,
        '~^/seminar/v1/options$~' => DAY_IN_SECONDS * 30,

    ];
}

// Build deterministic cache key including version and sorted query params
function seminar_rest_cache_build_key(\WP_REST_Request $request)
{
    $version = seminar_rest_cache_get_version();
    $method = strtoupper($request->get_method());
    $route = $request->get_route(); // e.g. /wp/v2/pages/9
    $query = $request->get_query_params() ?: [];
    ksort($query);
    $query_serial = serialize($query);
    return 'seminar_rest_cache_v' . intval($version) . '_' . md5($method . '|' . $route . '|' . $query_serial);
}

function seminar_rest_cache_get_version()
{
    $v = wp_cache_get('seminar_rest_cache_version', 'seminar_rest');
    if ($v === false) {
        $v = get_transient('seminar_rest_cache_version');
        if ($v === false) {
            $v = time(); // initial version
            set_transient('seminar_rest_cache_version', $v, 0);
        }
        wp_cache_set('seminar_rest_cache_version', $v, 'seminar_rest', 0);
    }
    return $v;
}

function seminar_rest_cache_bump_version()
{
    $v = intval(seminar_rest_cache_get_version());
    $v++;
    set_transient('seminar_rest_cache_version', $v, 0);
    wp_cache_set('seminar_rest_cache_version', $v, 'seminar_rest', 0);
    return $v;
}

// Check if route is cacheable (matches regex list)
function seminar_rest_route_is_cacheable(\WP_REST_Request $request)
{
    if (strtoupper($request->get_method()) !== 'GET') {
        return false;
    }
    $route = $request->get_route();
    $map = seminar_rest_cacheable_routes();
    foreach ($map as $pattern => $ttl) {
        if (preg_match($pattern, $route)) {
            return $ttl;
        }
    }
    return false;
}

// Safer rest_pre_dispatch that won't cause a fatal error on edge cases
add_filter('rest_pre_dispatch', function ($result, $server, $request) {
    try {
        // Only proceed if route is cacheable
        $ttl = seminar_rest_route_is_cacheable($request);
        if (!$ttl) {
            return $result;
        }

        // Guard: ensure $request is a WP_REST_Request before using it
        $refresh = false;
        if ($request instanceof \WP_REST_Request) {
            $refresh = filter_var($request->get_param('refresh'), FILTER_VALIDATE_BOOLEAN);
        }

        // Only allow admin bypass when current_user_can exists and returns true
        if ($refresh && function_exists('current_user_can') && current_user_can('manage_options')) {
            return $result; // bypass cache for admins
        }

        $key = seminar_rest_cache_build_key($request);

        // Try object cache safely
        $cached = false;
        if (function_exists('wp_cache_get')) {
            $cached = wp_cache_get($key, 'seminar_rest');
        }

        if ($cached !== false) {
            $resp = new WP_REST_Response($cached['data'], $cached['status']);
            if (!empty($cached['headers']) && is_array($cached['headers'])) {
                $resp->set_headers($cached['headers']);
            }
            return $resp;
        }

        // Fallback to transient
        if (function_exists('get_transient')) {
            $cached = get_transient($key);
            if ($cached !== false) {
                // repopulate object cache for speed if available
                if (function_exists('wp_cache_set')) {
                    wp_cache_set($key, $cached, 'seminar_rest', 0);
                }
                $resp = new WP_REST_Response($cached['data'], $cached['status']);
                if (!empty($cached['headers']) && is_array($cached['headers'])) {
                    $resp->set_headers($cached['headers']);
                }
                return $resp;
            }
        }

        return $result; // cache miss
    } catch (\Throwable $e) {
        // Avoid bringing the site down; log error if debugging is enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('seminar_rest_cache error: ' . $e->getMessage());
        }
        return $result;
    }
}, 5, 3);

// After dispatch, cache successful responses
add_filter('rest_post_dispatch', function ($result, $server, $request) {
    // Only cache if route is eligible
    $ttl = seminar_rest_route_is_cacheable($request);
    if (!$ttl) {
        return $result;
    }

    // Only cache WP_REST_Response with successful status
    if ($result instanceof WP_REST_Response) {
        $status = intval($result->get_status());
        if ($status >= 200 && $status < 300) {
            $data = $result->get_data();
            $headers = method_exists($result, 'get_headers') ? $result->get_headers() : (property_exists($result, 'headers') ? $result->headers : []);
            $cached = [
                'data' => $data,
                'status' => $status,
                'headers' => $headers
            ];
            $key = seminar_rest_cache_build_key($request);
            // store in both object cache and transient
            wp_cache_set($key, $cached, 'seminar_rest', 0);
            set_transient($key, $cached, $ttl);
        }
    }

    return $result;
}, 10, 3);

// Invalidate caches when relevant content changes occur by bumping version
add_action('save_post', function ($post_id, $post, $update) {
    // Only bump for public content types that affect frontend
    $public_types = ['page', 'post', 'classes', 'teachers', 'dance_teachers', 'team', 'acf_options'];
    if (in_array($post->post_type, $public_types, true)) {
        seminar_rest_cache_bump_version();
    }
}, 10, 3);

add_action('delete_post', function ($post_id) {
    seminar_rest_cache_bump_version();
}, 10, 1);

add_action('create_term', function () {
    seminar_rest_cache_bump_version();
}, 10, 3);
add_action('edit_term', function () {
    seminar_rest_cache_bump_version();
}, 10, 3);
add_action('delete_term', function () {
    seminar_rest_cache_bump_version();
}, 10, 3);

// Menus changes
add_action('wp_update_nav_menu', function () {
    seminar_rest_cache_bump_version();
});
add_action('wp_delete_nav_menu', function () {
    seminar_rest_cache_bump_version();
});

// Optional: API to programmatically clear cache (callable elsewhere)
function seminar_rest_cache_clear_all()
{
    seminar_rest_cache_bump_version();
    return true;
}

// bump cache version when ACF options are saved
if (function_exists('add_action')) {
    add_action('acf/save_post', function ($post_id) {
        if ($post_id === 'options') {
            seminar_rest_cache_bump_version();
        }
    });
}

/**
 * END CACHING
 */
