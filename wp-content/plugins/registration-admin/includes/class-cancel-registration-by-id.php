<?php
if ( ! class_exists('Seminar_Cancel_Registration_by_ID') ) {

    class Seminar_Cancel_Registration_by_ID {

        private $reg_year;
        private $registration_events_table;
        private $registrants_table;
        private $page_slug = 'seminar-cancel-registration-by-id';

        public function __construct() {
            global $wpdb;
            $this->registration_events_table = $wpdb->prefix . 'Seminar_registration_events';
            $this->registrants_table = $wpdb->prefix . 'Seminar_registrants';

            $end_date = get_field( 'seminar_end_date', 'option' );
            $this->reg_year = $end_date ? date( 'Y', strtotime( $end_date ) ) : date( 'Y' );

            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        }

        public function admin_menu() {
            $capability = 'edit_users';
            add_submenu_page(
                'tools.php',
                'Seminar Cancel Registration by ID',
                'Seminar Cancel Registration by ID',
                $capability,
                $this->page_slug,
                array( $this, 'render_page' )
            );
        }

        public function enqueue_assets( $hook ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] === $this->page_slug ) {
                if ( defined( 'SR_ASSETS_URL' ) ) {
                    wp_register_style( 'sr-cancel-registration-by-id', SR_ASSETS_URL . '/css/cancel_registration_by_id.css', array(), '1.0' );
                    wp_enqueue_style( 'sr-cancel-registration-by-id' );
                }
            }
        }

        public function render_page() {
            $display_rows = array();
            $registration_event_id = '';
            $input_id = 0;
            $error_message = '';
            $success_message = '';

            // Handle view registration request
            if ( isset( $_POST['view-cancel-registration-by-id'] ) ) {
                if ( ! current_user_can( 'edit_users' ) ) {
                    $error_message = 'Insufficient permissions.';
                } elseif ( ! isset( $_POST['sr_cancel_registration_by_id_nonce'] ) || ! wp_verify_nonce( $_POST['sr_cancel_registration_by_id_nonce'], 'sr_cancel_registration_by_id' ) ) {
                    $error_message = 'Invalid request.';
                } else {
                    $input_id = intval( $_POST['txt-cancel-registration-by-id'] ?? 0 );
                    if ( $input_id <= 0 ) {
                        $error_message = 'ERROR! No registration event ID entered. Please enter a numeric ID.';
                    } else {
                        $result = $this->getRegistrationEntry( $input_id );
                        if ( $result['error'] ) {
                            $error_message = $result['message'];
                        } else {
                            $display_rows = $result['rows'];
                            $registration_event_id = $input_id;
                        }
                    }
                }
            }

            // Handle cancel confirmation
            if ( isset( $_POST['confirm-cancel-registration'] ) ) {
                if ( ! current_user_can( 'edit_users' ) ) {
                    $error_message = 'Insufficient permissions.';
                } else {
                    $registration_event_id = sanitize_text_field( $_POST['cancel_registration_event_id'] ?? '' );
                    $input_id = intval( $_POST['cancel_input_id'] ?? 0 );
                    if ( $registration_event_id === '' ) {
                        $error_message = 'Missing registration event id.';
                    } else {
                        // Get registration data to access email
                        $reg_result = $this->getRegistrationEntry( $registration_event_id );
                        $primary_email = !empty($reg_result['rows'][0]->email) ? $reg_result['rows'][0]->email : '';
                        
                        $result = $this->cancelRegistration( $registration_event_id );
                        if ( $result['error'] ) {
                            $error_message = $result['message'];
                        } else {
                            $success_message = 'Marked registration #' . $input_id . ' as CANCELLED.';
                            if ($primary_email) {
                                $success_message .= ' Notification will be sent to ' . esc_html( $primary_email ) . '.';

                                $seminar_year = function_exists('get_field') ? get_field( 'seminar_year', 'option' ) : '';
                                $site_name = get_bloginfo( 'name' );
                                $admin_email = get_bloginfo( 'admin_email' );

                                $headers = [
                                    'From: ' . $site_name . ( $seminar_year ? ' ' . $seminar_year : '' ) . ' <' . $admin_email . '>',
                                    'Reply-To: ' . $admin_email,
                                    'Cc: ' . $admin_email
                                ];

                                $subject = 'Folk Seminar Plovdiv ' . trim( $seminar_year ) . ' | Registration #' . intval( $registration_event_id );
                                $message = "Marked registration #" . $input_id . " as cancelled.\n\n--Folk Seminar Plovdiv Program Team";
                                wp_mail( $primary_email, $subject, $message, $headers );
                            }
                        }
                    }
                }
            }

            $template = SR_PLUGIN_DIR . '/templates/cancel-registration-by-id.php';
            if ( file_exists( $template ) ) {
                $reg_year = $this->reg_year;
                include $template;
            } else {
                echo '<div class="wrap"><h1>Cancel Registration by ID</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
        }

        private function getRegistrationEntry($registration_event_id ) {
            global $wpdb;
            
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array( 'error' => true, 'message' => 'Queries class not found' );
            }
            
            $sql_template = Seminar_Registration_Queries::get_registration_event_sql();
            $sql = str_replace( '{registration_events_table}', esc_sql( $this->registration_events_table ), $sql_template );
            $sql = str_replace( '{registrants_table}', esc_sql( $this->registrants_table ), $sql );
            
            $row = $wpdb->get_row( $wpdb->prepare( $sql, $registration_event_id ) );

            if ( empty( $row ) ) {
                return array( 'error' => true, 'message' => 'No registration found for registration event ID: ' . $registration_event_id );
            }

            return array(
                'error' => false,
                'rows' => array( $row )
            );
        }

        private function cancelRegistration( $registration_event_id ) {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array( 'error' => true, 'message' => 'Queries class not found' );
            }

            $sql_template = Seminar_Registration_Queries::cancel_registration_by_event_id_sql();
            $sql = str_replace( '{registration_events_table}', esc_sql( $this->registration_events_table ), $sql_template );
            $updated = $wpdb->query( $wpdb->prepare( $sql, $registration_event_id ) );

            if ( $updated === false ) {
                return array( 'error' => true, 'message' => 'Database update failed.' );
            }

            return array( 'error' => false, 'updated' => $updated );
        }

    }
}

/* instantiate when included (guarded to avoid double instantiation) */
if ( class_exists( 'Seminar_Cancel_Registration_by_ID' ) && ! isset( $seminar_cancel_registration_by_id ) ) {
    $seminar_cancel_registration_by_id = new Seminar_Cancel_Registration_by_ID();
}
