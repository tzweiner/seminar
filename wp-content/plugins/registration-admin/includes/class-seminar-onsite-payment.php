<?php
if ( ! class_exists('Seminar_Onsite_Payment') ) {

    class Seminar_Onsite_Payment {

        private $reg_year;
        private $table;
        private $page_slug = 'seminar-onsite-payment';

        public function __construct() {
            global $wpdb;
            $this->table = $wpdb->prefix . 'view_onsite_registrants';

            $end_date = get_field( 'seminar_end_date', 'option' );
            $this->reg_year = $end_date ? date( 'Y', strtotime( $end_date ) ) : date( 'Y' );

            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
            add_action( 'admin_post_sr_export_onsite_registrants', array( $this, 'handle_export_onsite_registrants' ) );
        }

        public function admin_menu() {
            $capability = 'edit_users';
            add_submenu_page(
                'tools.php',
                'Seminar Onsite Payment',
                'Seminar Onsite Payment',
                $capability,
                $this->page_slug,
                array( $this, 'render_page' )
            );
        }

        public function enqueue_assets( $hook ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] === $this->page_slug ) {
                if ( defined( 'SR_ASSETS_URL' ) ) {
                    wp_register_style( 'sr-admin-onsite_registrants', SR_ASSETS_URL . '/css/common.css', array(), '1.1' );
                    wp_enqueue_style( 'sr-admin-onsite_registrants' );
                }
            }
        }

        public function render_page() {
            $display_rows = array();
            if ( isset( $_POST['view-onsite-registrants'] ) ) {

                if ( ! current_user_can( 'edit_users' ) ) {
                    echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                    return;
                }

                if ( ! isset( $_POST['sr_onsite_registrants_nonce'] ) || ! wp_verify_nonce( $_POST['sr_onsite_registrants_nonce'], 'sr_onsite_registrants' ) ) {
                    echo '<div class="wrap"><p>Invalid request.</p></div>';
                    return;
                }

                $rows = $this->getRegistrantsWithOnsitePayment();
                if ( ! empty( $rows ) ) {
                    foreach ( $rows as $registrant ) {
                        $display_rows[] = $this->parse_registrant_for_display( $registrant );
                    }
                }
            }

            $template = SR_PLUGIN_DIR . '/templates/onsite-payment.php';
            if ( file_exists( $template ) ) {
                $reg_year = $this->reg_year;
                include $template;
            } else {
                echo '<div class="wrap"><h1>Registrants with onsite payment</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
        }

        public function handle_export_onsite_registrants() {
            if ( ! current_user_can( 'edit_users' ) ) {
                wp_die( 'Insufficient permissions.' );
            }

            check_admin_referer( 'sr_onsite_registrants', 'sr_onsite_registrants_nonce' );

            $rows = $this->getRegistrantsWithOnsitePayment();
            $this->outputCsv( $rows );
        }

        private function parse_registrant_for_display( $registrant ) {
            $first_name = isset( $registrant->first_name ) ? (string) $registrant->first_name : '';
            $last_name  = isset( $registrant->last_name ) ? (string) $registrant->last_name : '';
            $name = trim( $first_name . ' ' . $last_name );
            $registration_number = isset( $registrant->registration_number ) ? intval( $registrant->registration_number ) : '';

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

        private function outputCsv( $rows ) {
            if ( empty( $rows ) ) {
                wp_die( 'No data to export.' );
            }

            $filename = 'registrants-with-onsite-payment-' . $this->reg_year . '.csv';

            while ( ob_get_level() ) {
                ob_end_clean();
            }

            header( 'Content-Description: File Transfer' );
            header( 'Content-Type: text/csv; charset=UTF-8' );
            header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Pragma: public' );

            echo "\xEF\xBB\xBF";

            $out = fopen( 'php://output', 'w' );
            if ( $out === false ) {
                wp_die( 'Unable to open output stream.' );
            }

            $headers = array( 'Registration Number', 'Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'Country', 'Email' );
            fputcsv( $out, $headers );

            foreach ( $rows as $r ) {
                $registration_number = isset( $r->registration_number ) ? intval( $r->registration_number ) : '';
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

        private function getRegistrantsWithOnsitePayment() {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array();
            }
            $sql_template = Seminar_Registration_Queries::get_onsite_payment_sql();
            $sql = str_replace( '{table}', esc_sql( $this->table ), $sql_template );
            $prepared = $wpdb->prepare( $sql, intval( $this->reg_year ) );
            return $wpdb->get_results( $prepared );
        }

    }
}

if ( class_exists( 'Seminar_Onsite_Payment' ) && ! isset( $seminar_onsite_payment ) ) {
    $seminar_onsite_payment = new Seminar_Onsite_Payment();
}
