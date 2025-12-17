<?php
// File: `wp-content/plugins/registration-admin/includes/class-seminar-registration-confirmed.php`
if ( ! class_exists( 'Seminar_Registration_Confirmed' ) ) {

    class Seminar_Registration_Confirmed {

        private $reg_year;
        private $table;
        private $page_slug = 'seminar-registration-confirmed';

        public function __construct() {
            global $wpdb;
            $this->table = $wpdb->prefix . 'view_confirmed_registrants';

            $end_date = get_field( 'seminar_end_date', 'option' );
            $this->reg_year = $end_date ? date( 'Y', strtotime( $end_date ) ) : date( 'Y' );

            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

            // admin-post handler for CSV export
            add_action( 'admin_post_sr_export_confirmed_registrants', array( $this, 'handle_export_confirmed_registrants' ) );
        }

        public function admin_menu() {
//            $capability = 'edit_users';
//            $capability = 'manage_options';
//            add_submenu_page(
//                Seminar_Admin_Tools_Menu::SLUG,
//                'Seminar Confirmed Registrants',
//                'Seminar Confirmed Registrants',
//                $capability,
//                $this->page_slug,
//                array( $this, 'render_page' )
//            );
        }

        public function enqueue_assets( $hook ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] === $this->page_slug ) {
                if ( defined( 'SR_ASSETS_URL' ) ) {
                    wp_register_style( 'sr-admin-confirmed', SR_ASSETS_URL . '/css/confirmed_registrations.css', array(), '0.1' );
                    wp_enqueue_style( 'sr-admin-confirmed' );
                }
            }
        }

        public function render_page() {
            if ( ! current_user_can( 'edit_users' ) ) {
                echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                return;
            }

            $rows = array();
            $display_rows = array();

            // form submit to view results should set this input name and a nonce
            if ( isset( $_POST['view-confirmed-names-and-addresses'] ) ) {

                if ( ! current_user_can( 'edit_users' ) ) {
                    echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                    return;
                }

                if ( ! isset( $_POST['sr_confirmed_nonce'] ) || ! wp_verify_nonce( $_POST['sr_confirmed_nonce'], 'sr_confirmed' ) ) {
                    echo '<div class="wrap"><p>Invalid request.</p></div>';
                    return;
                }

                $rows = $this->getConfirmedRegistrants();
                if ( ! empty( $rows ) ) {
                    $display_rows = $this->build_display_rows( $rows );
                }
            }

            $template = SR_PLUGIN_DIR . '/templates/confirmed-registrants.php';
            if ( file_exists( $template ) ) {
                // make both raw and display rows available to the template
                $reg_year = $this->reg_year;
                include $template;
            } else {
                echo '<div class="wrap"><h1>Confirmed Registrants</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
        }

        public function handle_export_confirmed_registrants() {
            if ( ! current_user_can( 'edit_users' ) ) {
                wp_die( 'Insufficient permissions.' );
            }

            check_admin_referer( 'sr_confirmed', 'sr_confirmed_nonce' );

            $rows = $this->getConfirmedRegistrants();
            $this->outputCsv( $rows );
        }

        /**
         * Convert raw DB rows into escaped display objects using parse_registrant_for_display().
         *
         * @param array $rows
         * @return array
         */
        private function build_display_rows( $rows ) {
            $display = array();
            foreach ( $rows as $registrant ) {
                $display[] = $this->parse_registrant_for_display( $registrant );
            }
            return $display;
        }

        private function parse_registrant_for_display( $registrant ) {
            $first_name = isset( $registrant->first_name ) ? (string) $registrant->first_name : '';
            $last_name  = isset( $registrant->last_name ) ? (string) $registrant->last_name : '';
            $name = trim( $first_name . ' ' . $last_name );
            $registration_number = isset( $registrant->registration_event_id ) ? intval( $registrant->registration_event_id ) : '';

            $address1 = isset( $registrant->address1 ) ? trim( (string) $registrant->address1 ) : '';
            $address2 = isset( $registrant->address2 ) ? trim( (string) $registrant->address2 ) : '';
            $city     = isset( $registrant->city ) ? trim( (string) $registrant->city ) : '';
            $state    = isset( $registrant->state ) ? trim( (string) $registrant->state ) : '';
            $zip      = isset( $registrant->zip ) ? trim( (string) $registrant->zip ) : '';
            $country  = isset( $registrant->country ) ? trim( (string) $registrant->country ) : '';
            $email    = isset( $registrant->email ) ? trim( (string) $registrant->email ) : '';
            $phone  = isset( $registrant->phone ) ? trim( (string) $registrant->phone ) : '';


            return (object) array(
                'registration_number' => $registration_number,
                'name'     => esc_html( $name ),
                'address1' => esc_html( $address1 ),
                'address2' => esc_html( $address2 ),
                'city'     => esc_html( $city ),
                'state'    => esc_html( $state ),
                'zip'      => esc_html( $zip ),
                'country'  => esc_html( $country ),
                'email'    => esc_html( $email ),
                'phone'    => esc_html( $phone )
            );
        }

        private function outputCsv( $rows ) {
            if ( empty( $rows ) ) {
                wp_die( 'No data to export.' );
            }

            $filename = 'confirmed-registrants-' . $this->reg_year . '.csv';

            while ( ob_get_level() ) {
                ob_end_clean();
            }

            header( 'Content-Description: File Transfer' );
            header( 'Content-Type: text/csv; charset=UTF-8' );
            header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Pragma: public' );

            // BOM for Excel/UTF-8
            echo "\xEF\xBB\xBF";

            $out = fopen( 'php://output', 'w' );
            if ( $out === false ) {
                wp_die( 'Unable to open output stream.' );
            }

            $headers = array( 'Registration Number', 'Name', 'Email', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'Country', 'Phone' );
            fputcsv( $out, $headers );

            foreach ( $rows as $r ) {
                $registration_number = isset( $r->registration_event_id ) ? intval( $r->registration_event_id ) : '';
                $name  = trim( (string) ( $r->first_name ?? '' ) . ' ' . ( $r->last_name ?? '' ) );
                $email = wp_strip_all_tags( $r->email ?? '' );
                $address1 = wp_strip_all_tags( $r->address1 ?? '' );
                $address2 = wp_strip_all_tags( $r->address2 ?? '' );
                $city = wp_strip_all_tags( $r->city ?? '' );
                $state = wp_strip_all_tags( $r->state ?? '' );
                $zip = wp_strip_all_tags( $r->zip ?? '' );
                $country = wp_strip_all_tags( $r->country ?? '' );
                $phone = wp_strip_all_tags( $r->phone ?? '' );

                $line = array(
                    $registration_number,
                    $name,
                    $email,
                    $address1,
                    $address2,
                    $city,
                    $state,
                    $zip,
                    $country,
                    $phone
                );

                fputcsv( $out, $line );
            }

            fclose( $out );
            exit;
        }

        private function getConfirmedRegistrants() {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array();
            }

            if ( ! method_exists( 'Seminar_Registration_Queries', 'registrants_confirmed_sql' ) ) {
                return array();
            }

            $sql_template = Seminar_Registration_Queries::registrants_confirmed_sql();
            $sql = str_replace( '{table}', esc_sql( $this->table ), $sql_template );
            $prepared = $wpdb->prepare( $sql, intval( $this->reg_year ) );
            
            if ( $prepared === false ) {
                return array();
            }

            return $wpdb->get_results( $prepared );
        }

    }
}

/* instantiate when included (guarded to avoid double instantiation) */
if ( class_exists( 'Seminar_Registration_Confirmed' ) && ! isset( $seminar_registration_confirmed ) ) {
    $seminar_registration_confirmed = new Seminar_Registration_Confirmed();
}
