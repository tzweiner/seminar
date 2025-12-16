<?php
if ( ! class_exists( 'Seminar_Registrant_by_ID' ) ) {

    class Seminar_Registrant_by_ID {

        private $reg_year;
        private $registrant_table;
        private $classes_table;
        private $page_slug = 'seminar-registrant-by-id';

        public function __construct() {
            global $wpdb;
            $this->registrant_table = $wpdb->prefix . 'view_registrant';
            $this->classes_table = $wpdb->prefix . 'view_registrant_classes';

            $end_date = get_field( 'seminar_end_date', 'option' );
            $this->reg_year = $end_date ? date( 'Y', strtotime( $end_date ) ) : date( 'Y' );

            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        }

        public function admin_menu() {
            $capability = 'edit_users';
            add_submenu_page(
                'tools.php',
                'Seminar Registrant By ID',
                'Seminar Registrant By ID',
                $capability,
                $this->page_slug,
                array( $this, 'render_page' )
            );
        }

        public function enqueue_assets( $hook ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] === $this->page_slug ) {
                if ( defined( 'SR_ASSETS_URL' ) ) {
                    wp_register_style( 'sr-admin-registrant-by-id', SR_ASSETS_URL . '/css/registrant_by_id', array(), '0.1' );
                    wp_enqueue_style( 'sr-admin-registrant-by-id' );
                }
            }
        }

        public function render_page() {
            if ( ! current_user_can( 'edit_users' ) ) {
                echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                return;
            }

            $rows = array();
            $registrant = array();
            $classes = array();
            $error_message = '';
            $success_message = '';

            // form submit to view results should set this input name and a nonce
            if ( isset( $_POST['view-registrant-by-id'] ) ) {

                if ( ! current_user_can( 'edit_users' ) ) {
                    echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                    return;
                }

                if ( ! isset( $_POST['sr_registrant_by_id_nonce'] ) || ! wp_verify_nonce( $_POST['sr_registrant_by_id_nonce'], 'sr_registrant_by_id' ) ) {
                    echo '<div class="wrap"><p>Invalid request.</p></div>';
                    return;
                }

                $input_id = intval( $_POST['txt-registrant-by-id'] ?? 0 );
                if ( $input_id <= 0 ) {
                    $error_message = 'ERROR! No registrant ID entered. Please enter a numeric ID.';
                } else {
                    $rows = $this->getRegistrantById( );
                    if ( $rows['error'] ) {
                        $error_message = $rows['message'];
                    } else {
                        $registrant = $this->build_display_rows($rows['registrant']);
                        $classes = $rows['classes'];
                    }
                }
            }

            $template = SR_PLUGIN_DIR . '/templates/registrant-by-id.php';
            if ( file_exists( $template ) ) {
                // make both raw and display rows available to the template
                $reg_year = $this->reg_year;
                include $template;
            } else {
                echo '<div class="wrap"><h1>Registrant By ID</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
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

        private function getRegistrantById() {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array();
            }

            if ( ! method_exists( 'Seminar_Registration_Queries', 'get_registrant_sql' ) ) {
                return array();
            }

            $sql_template = Seminar_Registration_Queries::get_registrant_sql();
            $sql = str_replace( '{registrant_table}', esc_sql( $this->registrant_table ), $sql_template );
            $prepared = $wpdb->prepare( $sql, intval( $this->reg_year ) );
            
            if ( $prepared === false ) {
                return array();
            }

            $registrant = $wpdb->get_row( $prepared );

            if ( $registrant !== null ) {
                $sql_template_classes = Seminar_Registration_Queries::get_registrant_classes_sql();
                $sql = str_replace( '{classes_table}', esc_sql( $this->classes_table ), $sql_template_classes );
                $prepared_classes = $wpdb->prepare( $sql, intval( $this->reg_year ) );

                $registrant_classes = $wpdb->get_results( $prepared_classes );

                $combined_response = new stdClass();
                $combined_response->registrant = $registrant;
                $combined_response->classes = $registrant_classes ?? array();
                return $combined_response;
            }

            return array();
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
if ( class_exists( 'Seminar_Registrant_by_ID' ) && ! isset( $seminar_registrant_by_id ) ) {
    $seminar_registrant_by_id = new Seminar_Registrant_by_ID();
}
