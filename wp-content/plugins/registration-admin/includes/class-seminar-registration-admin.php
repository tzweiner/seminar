<?php
// File: `wp-content/plugins/registration-admin/includes/class-seminar-registration-admin.php`
if ( ! class_exists( 'Seminar_Registration_Admin' ) ) {

    class Seminar_Registration_Admin {

        private $reg_year;
        private $table;

        public function __construct() {
            global $wpdb;
            $this->table = $wpdb->prefix . 'view_media_orders';

            $end_date = get_field( 'seminar_end_date', 'option' );
            if ( $end_date ) {
                $this->reg_year = date( 'Y', strtotime( $end_date ) );
            } else {
                $this->reg_year = date( 'Y' );
            }

            // Register admin-post handler for CSV export (runs before normal admin page output).
            add_action( 'admin_post_sr_export_media_orders', array( $this, 'handle_export_media_orders' ) );
        }

        public function admin_menu() {
            $capability = 'edit_users';
            add_submenu_page(
                'tools.php',
                'Seminar Registration Admin',
                'Seminar Registration Admin',
                $capability,
                'seminar-registration-admin-media',
                array( $this, 'render_page' )
            );
        }

        public function enqueue_assets() {
            if ( isset( $_GET['page'] ) && $_GET['page'] === 'seminar-registration-admin-media' ) {
                wp_register_style( 'sr-admin', SR_ASSETS_URL . '/css/admin.css', array(), '0.1' );
                wp_enqueue_style( 'sr-admin' );
            }
        }

        public function render_page() {
            $display_rows = array();
            if ( isset( $_POST['view-media-orders-names-and-addresses'] ) ) {

                if ( ! current_user_can( 'edit_users' ) ) {
                    echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                    return;
                }

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

            $template = SR_PLUGIN_DIR . '/templates/media-orders.php';
            if ( file_exists( $template ) ) {
                $reg_year = $this->reg_year; // make available to template
                include $template;
            } else {
                echo '<div class="wrap"><h1>Registrants with media orders</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
        }

        /**
         * Handle CSV export via admin-post.php.
         * This runs before normal page output so headers can be sent cleanly.
         */
        public function handle_export_media_orders() {
            if ( ! current_user_can( 'edit_users' ) ) {
                wp_die( 'Insufficient permissions.' );
            }

            // Verify nonce; use check_admin_referer which exits on failure
            check_admin_referer( 'sr_media_orders', 'sr_media_orders_nonce' );

            $rows = $this->getRegistrantsWithMediaOrders();
            $this->outputCsv( $rows );
            // outputCsv exits after sending
        }

        private function parse_registrant_for_display( $registrant ) {
            $first_name = isset( $registrant->first_name ) ? (string) $registrant->first_name : '';
            $last_name  = isset( $registrant->last_name ) ? (string) $registrant->last_name : '';
            $name = trim( $first_name . ' ' . $last_name );
            $registration_number = isset( $registrant->registrant_id ) ? intval( $registrant->registrant_id ) : '';

            $address1 = isset( $registrant->address1 ) ? trim( (string) $registrant->address1 ) : '';
            $address2 = isset( $registrant->address2 ) ? trim( (string) $registrant->address2 ) : '';
            $city     = isset( $registrant->city ) ? trim( (string) $registrant->city ) : '';
            $state    = isset( $registrant->state ) ? trim( (string) $registrant->state ) : '';
            $zip      = isset( $registrant->zip ) ? trim( (string) $registrant->zip ) : '';
            $country  = isset( $registrant->country ) ? trim( (string) $registrant->country ) : '';
            $email    = isset( $registrant->email ) ? trim( (string) $registrant->email ) : '';

            return (object) array(
                'registration_number' => $registration_number,
                'name'     => esc_html( $name ),
                'address1' => esc_html( $address1 ),
                'address2' => esc_html( $address2 ),
                'city'     => esc_html( $city ),
                'state'    => esc_html( $state ),
                'zip'      => esc_html( $zip ),
                'country'  => esc_html( $country ),
                'email'    => esc_html( $email )
            );
        }

        /**
         * Stream CSV for download. Exits after sending.
         *
         * @param array $rows Raw DB rows (objects)
         */
        private function outputCsv( $rows ) {
            if ( empty( $rows ) ) {
                wp_die( 'No data to export.' );
            }

            $filename = 'media-orders-' . $this->reg_year . '.csv';

            // Clean any previous output to avoid HTML leaking into CSV
            while ( ob_get_level() ) {
                ob_end_clean();
            }

            // Send download headers
            header( 'Content-Description: File Transfer' );
            header( 'Content-Type: text/csv; charset=UTF-8' );
            header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Pragma: public' );

            // UTF-8 BOM for Excel
            echo "\xEF\xBB\xBF";

            $out = fopen( 'php://output', 'w' );
            if ( $out === false ) {
                wp_die( 'Unable to open output stream.' );
            }

            // Column headers
            $headers = array( 'Registration Number', 'Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'Country', 'Email' );
            fputcsv( $out, $headers );

            foreach ( $rows as $r ) {
                $registration_number = isset( $r->registrant_id ) ? intval( $r->registrant_id ) : '';
                $name  = trim( (string) ( $r->first_name ?? '' ) . ' ' . ( $r->last_name ?? '' ) );
                $address1 = wp_strip_all_tags( $r->address1 ?? '' );
                $address2 = wp_strip_all_tags( $r->address2 ?? '' );
                $city = wp_strip_all_tags( $r->city ?? '' );
                $state = wp_strip_all_tags( $r->state ?? '' );
                $zip = wp_strip_all_tags( $r->zip ?? '' );
                $country = wp_strip_all_tags( $r->country ?? '' );
                $email = wp_strip_all_tags( $r->email ?? '' );

                $line = array(
                    $registration_number,
                    $name,
                    $address1,
                    $address2,
                    $city,
                    $state,
                    $zip,
                    $country,
                    $email
                );

                fputcsv( $out, $line );
            }

            fclose( $out );
            exit;
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

    }
}
