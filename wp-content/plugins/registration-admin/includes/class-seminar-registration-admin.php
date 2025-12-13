<?php
// File: `wp-content/plugins/registration-admin/includes/class-seminar-registration-admin.php`
if ( ! class_exists( 'Seminar_Registration_Admin' ) ) {

    class Seminar_Registration_Admin {

        private $reg_year;
        private $table; // will point to the view name

        public function __construct() {
            global $wpdb;

            // Use the DB view (prefixed) as the source for media-orders.
            // Example view name: wp_view_media_orders -> use $wpdb->prefix . 'view_media_orders'
            $this->table = $wpdb->prefix . 'view_media_orders';

            $end_date = get_field( 'seminar_end_date', 'option' );
            if ( $end_date ) {
                $this->reg_year = date( 'Y', strtotime( $end_date ) );
            } else {
                $this->reg_year = date( 'Y' );
            }
        }

        public function admin_menu() {
            $capability = 'edit_users';
            add_submenu_page(
                'tools.php',                                 // parent slug (Tools)
                'Seminar Registration Admin Tool',           // page title (browser / heading)
                'Seminar Registration Admin Tool',           // menu title (visible in Tools menu)
                $capability,
                'seminar-registration-admin-media',
                array( $this, 'render_page' )
            );
        }

        public function enqueue_assets() {
            // only enqueue on our plugin page
            if ( isset( $_GET['page'] ) && $_GET['page'] === 'seminar-registration-admin-media' ) {
                wp_register_style( 'sr-admin', SR_ASSETS_URL . '/css/admin.css', array(), '0.1' );
                wp_enqueue_style( 'sr-admin' );
            }
        }

        public function render_page() {
            // handle POST action
            $display_rows = array();
            if ( isset( $_POST['view-media-orders-names-and-addresses'] ) ) {

                if ( ! current_user_can( 'edit_users' ) ) {
                    echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                    return;
                }

                // verify nonce if present (template adds it)
                if ( isset( $_POST['sr_media_orders_nonce'] ) && ! wp_verify_nonce( $_POST['sr_media_orders_nonce'], 'sr_media_orders' ) ) {
                    echo '<div class="wrap"><p>Invalid request.</p></div>';
                    return;
                }

                $rows = $this->getRegistrantsWithMediaOrders();
                if ( ! empty( $rows ) ) {
                    foreach ( $rows as $registrant ) {
                        $display_rows[] = $this->parse_registrant_for_display( $registrant );
                    }
                }
            }

            // load template and pass $display_rows and $this->reg_year
            $template = SR_PLUGIN_DIR . '/templates/media-orders.php';
            if ( file_exists( $template ) ) {
                $reg_year = $this->reg_year;
                include $template;
            } else {
                echo '<div class="wrap"><h1>Registrants with media orders</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
        }

        /**
         * Normalize a single registrant row for display.
         */
        private function parse_registrant_for_display( $registrant ) {
            $first_name = isset( $registrant->first_name ) ? (string) $registrant->first_name : '';
            $last_name  = isset( $registrant->last_name ) ? (string) $registrant->last_name : '';
            $name = trim( $first_name . ' ' . $last_name );
            $registration_number = isset( $registrant->registration_number ) ? $registrant->registration_number : $registrant->registrant_id;

            $address1 = isset( $registrant->address1 ) ? trim( (string) $registrant->address1 ) : '';
            $address2 = isset( $registrant->address2 ) ? trim( (string) $registrant->address2 ) : '';
            $city     = isset( $registrant->city ) ? trim( (string) $registrant->city ) : '';
            $state    = isset( $registrant->state ) ? trim( (string) $registrant->state ) : '';
            $zip      = isset( $registrant->zip ) ? trim( (string) $registrant->zip ) : '';
            $country  = isset( $registrant->country ) ? trim( (string) $registrant->country ) : '';
            $email    = isset( $registrant->email ) ? trim( (string) $registrant->email ) : '';

            return (object) array(
                'name'     => esc_html( $name ),
                'registration_number'     => esc_html( $registration_number ),
                'address1' => esc_html( $address1 ),
                'address2' => esc_html( $address2 ),
                'city'     => esc_html( $city ),
                'state'    => esc_html( $state ),
                'zip'      => esc_html( $zip ),
                'country'  => esc_html( $country ),
                'email'    => esc_html( $email ),
            );
        }

        private function getRegistrantsWithMediaOrders() {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array();
            }
            $sql_template = Seminar_Registration_Queries::registrants_with_media_orders_sql();
            $sql = str_replace( '{table}', esc_sql( $this->table ), $sql_template );
            $prepared = $wpdb->prepare( $sql, intval( $this->reg_year ) );
            return $wpdb->get_results( $prepared );
        }

        private function getPrimaryRegistrant( $registration_event_id ) {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return null;
            }
            $sql_template = Seminar_Registration_Queries::primary_registrant_by_event_sql();
            $sql = str_replace( '{table}', esc_sql( $this->table ), $sql_template );
            $prepared = $wpdb->prepare( $sql, $registration_event_id, intval( $this->reg_year ) );
            $res = $wpdb->get_results( $prepared );
            return ! empty( $res ) ? $res[0] : null;
        }

    }
}
