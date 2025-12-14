<?php
if ( ! class_exists( 'Seminar_All_Registrations' ) ) {

    class Seminar_All_Registrations {

        private $reg_year;
        private $table;
        private $page_slug = 'seminar-all-registrations';

        public function __construct() {
            global $wpdb;
            $this->table = $wpdb->prefix . 'Seminar_registrations';

            $end_date = get_field( 'seminar_end_date', 'option' );
            $this->reg_year = $end_date ? date( 'Y', strtotime( $end_date ) ) : date( 'Y' );

            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

            // admin-post handler for CSV export
            add_action( 'admin_post_sr_export_all_registrations', array( $this, 'handle_export_all_registrations' ) );
        }

        public function admin_menu() {
            $capability = 'edit_users';
            add_submenu_page(
                'tools.php',
                'Seminar All Registrations',
                'Seminar All Registrations',
                $capability,
                $this->page_slug,
                array( $this, 'render_page' )
            );
        }

        public function enqueue_assets( $hook ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] === $this->page_slug ) {
                if ( defined( 'SR_ASSETS_URL' ) ) {
                    wp_register_style( 'sr-admin-all-registrations', SR_ASSETS_URL . '/css/all_registrations.css', array(), '0.1' );
                    wp_enqueue_style( 'sr-admin-all-registrations' );
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
            if ( isset( $_POST['view-all-registrations'] ) ) {

                if ( ! current_user_can( 'edit_users' ) ) {
                    echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                    return;
                }

                if ( ! isset( $_POST['sr_all_registrations_nonce'] ) || ! wp_verify_nonce( $_POST['sr_all_registrations_nonce'], 'sr_all_registrations' ) ) {
                    echo '<div class="wrap"><p>Invalid request.</p></div>';
                    return;
                }

                $rows = $this->getAllRegistrations();
                if ( ! empty( $rows ) ) {
                    $display_rows = $this->build_display_rows( $rows );
                }
            }

            $template = SR_PLUGIN_DIR . '/templates/all-registrations.php';
            if ( file_exists( $template ) ) {
                // make both raw and display rows available to the template
                $reg_year = $this->reg_year;
                include $template;
            } else {
                echo '<div class="wrap"><h1>All Registrants</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
        }

        public function handle_export_all_registrations() {
            if ( ! current_user_can( 'edit_users' ) ) {
                wp_die( 'Insufficient permissions.' );
            }

            check_admin_referer( 'sr_all_registrations', 'sr_all_registrations_nonce' );

            $rows = $this->getAllRegistrations();
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
            $registration_number = isset( $registrant->registration_event_id ) ? intval( $registrant->registration_event_id ) : intval($registrant->registrant_id);

            $address1 = isset( $registrant->address1 ) ? trim( (string) $registrant->address1 ) : '';
            $address2 = isset( $registrant->address2 ) ? trim( (string) $registrant->address2 ) : '';
            $city     = isset( $registrant->city ) ? trim( (string) $registrant->city ) : '';
            $state    = isset( $registrant->state ) ? trim( (string) $registrant->state ) : '';
            $zip      = isset( $registrant->zip ) ? trim( (string) $registrant->zip ) : '';
            $country  = isset( $registrant->country ) ? trim( (string) $registrant->country ) : '';
            $email    = isset( $registrant->email ) ? trim( (string) $registrant->email ) : '';
            $phone  = isset( $registrant->phone ) ? trim( (string) $registrant->phone ) : '';
            $emergency  = isset( $registrant->emergency ) ? trim( (string) $registrant->emergency ) : '';
            $num_days  = isset( $registrant->num_days ) ? trim( (string) $registrant->num_days ) : '';
            $gala  = isset( $registrant->gala ) ? trim( (string) $registrant->gala ) : '';
            $meal_option  = isset( $registrant->meal_option ) ? trim( (string) $registrant->meal_option ) : '';
            $age  = isset( $registrant->age ) ? trim( (string) $registrant->age ) : '';
            $is_eefc  = isset( $registrant->is_eefc ) ? trim( (string) $registrant->is_eefc ) : '';
            $is_bulgarian  = isset( $registrant->is_bulgarian ) ? trim( (string) $registrant->is_bulgarian ) : '';
            $transport  = isset( $registrant->transport ) ? $this->transport_label(trim( (string) $registrant->transport )) : '';
            $media  = isset( $registrant->media ) ? trim( (string) $registrant->media ) : '';
            $balance  = isset( $registrant->balance ) ? trim( (string) $registrant->balance ) : '';
            $registration_date  = isset( $registrant->registration_date ) ? trim( (string) $registrant->registration_date ) : '';
            $payment  = isset( $registrant->payment ) ? trim( (string) $registrant->payment ) : '';
            $registration_email_sent  = isset( $registrant->registration_email_sent ) ? trim( (string) $registrant->registration_email_sent ) : '';
            $registration_email_sent_timestamp  = isset( $registrant->registration_email_sent_timestamp ) ? trim( (string) $registrant->registration_email_sent_timestamp ) : '';
            $registration_status  = isset( $registrant->registration_status ) ? trim( (string) $registrant->registration_status ) : '';

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
                'phone'    => esc_html( $phone ),
                'emergency' => esc_html( $emergency ),
                'num_days' => esc_html( $num_days ),
                'gala' => esc_html( $gala ),
                'meal_option' => esc_html( $meal_option ),
                'age' => esc_html( $age ),
                'is_eefc' => esc_html( $is_eefc ),
                'is_bulgarian' => esc_html( $is_bulgarian ),
                'transport' => esc_html( $transport ),
                'media' => esc_html( $media ),
                'balance' => esc_html( $balance ),
                'payment' => esc_html( $payment ),
                'registration_date' => esc_html( $registration_date ),
                'registration_email_sent' => esc_html( $registration_email_sent ),
                'registration_email_sent_timestamp' => esc_html( $registration_email_sent_timestamp ),
                'registration_status' => esc_html( $registration_status )
            );
        }

        private function outputCsv( $rows ) {
            if ( empty( $rows ) ) {
                wp_die( 'No data to export.' );
            }

            $filename = 'all-registrations-' . $this->reg_year . '.csv';

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

            $headers = array( 'Registration Number', 'Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'Country', 'Phone',
                'Email', 'Emergency', 'Number of Days', 'Gala', 'Age Group', 'EEFC Member', 'Bulgarian',
                'Transportation', 'Video Requested', 'Balance', 'Payment Option', 'Registration Date',
                'Registration Confirmation Email', 'Registration Status' );
            fputcsv( $out, $headers );

            foreach ( $rows as $r ) {
                $registration_number = isset( $r->registration_event_id ) ? intval( $r->registration_event_id ) : intval( $r->registrant_id );
                $name  = trim( (string) ( $r->first_name ?? '' ) . ' ' . ( $r->last_name ?? '' ) );
                $email = wp_strip_all_tags( $r->email ?? '' );
                $address1 = wp_strip_all_tags( $r->address1 ?? '' );
                $address2 = wp_strip_all_tags( $r->address2 ?? '' );
                $city = wp_strip_all_tags( $r->city ?? '' );
                $state = wp_strip_all_tags( $r->state ?? '' );
                $zip = wp_strip_all_tags( $r->zip ?? '' );
                $country = wp_strip_all_tags( $r->country ?? '' );
                $phone = wp_strip_all_tags( $r->phone ?? '' );
                $emergency = wp_strip_all_tags( $r->emergency ?? '' );
                $num_days = wp_strip_all_tags( $r->num_days ?? '' );
                $gala = wp_strip_all_tags( $r->gala ? 'Yes, ' > $r->meal_option : 'No' );
                $age = wp_strip_all_tags( $r->age ?? '' );
                $is_eefc = wp_strip_all_tags( $r->is_eefc ? 'Yes' : 'No' );
                $is_bulgarian = wp_strip_all_tags( $r->is_bulgarian ? 'Yes' : 'No' );
                $transport = $this->transport_label(wp_strip_all_tags( $r->transport ?? '' ));
                $media = wp_strip_all_tags( $r->media ? 'Yes' : 'No' );
                $balance = wp_strip_all_tags( $r->balance ?? '' );
                $payment = wp_strip_all_tags( $r->payment ?? '' );
                $registration_date = wp_strip_all_tags( $r->registration_date ?? '' );
                $registration_email_sent = wp_strip_all_tags( $r->registration_email_sent ? $r->registration_email_sent_timestamp : 'No' );
                $registration_status = wp_strip_all_tags( $r->registration_status ?? '' );

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
                    $phone,
                    $emergency,
                    $num_days,
                    $gala,
                    $age,
                    $is_eefc,
                    $is_bulgarian,
                    $transport,
                    $media,
                    $balance,
                    $payment,
                    $registration_date,
                    $registration_email_sent,
                    $registration_status
                );

                fputcsv( $out, $line );
            }

            fclose( $out );
            exit;
        }

        private function getAllRegistrations() {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array();
            }

            if ( ! method_exists( 'Seminar_Registration_Queries', 'all_registrants_sql' ) ) {
                return array();
            }

            $sql_template = Seminar_Registration_Queries::all_registrants_sql();
            $sql = str_replace( '{table}', esc_sql( $this->table ), $sql_template );
            $prepared = $wpdb->prepare( $sql, intval( $this->reg_year ) );
            
            if ( $prepared === false ) {
                return array();
            }

            return $wpdb->get_results( $prepared );
        }

        private function transport_label( $code ): string {
            $c = (string) $code;
            switch ( $c ) {
                case '1':
                    return 'Plovdiv to Koprivshtitsa';
                case '2':
                    return 'Koprivshtitsa to Sofia';
                case '3':
                    return 'Plovdiv to Koprivshtitsa and Koprivshtitsa to Sofia';
                case '0':
                case '':
                case 'null':
                    return 'No';
                default:
                    return 'Unknown';
            }
        }
    }
}

/* instantiate when included (guarded to avoid double instantiation) */
if ( class_exists( 'Seminar_All_Registrations' ) && ! isset( $seminar_all_registrations ) ) {
    $seminar_all_registrations = new Seminar_All_Registrations();
}
