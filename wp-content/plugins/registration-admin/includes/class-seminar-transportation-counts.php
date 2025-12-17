<?php
if ( ! class_exists('Seminar_Transportation_Counts') ) {

    class Seminar_Transportation_Counts {

        private $reg_year;
        private $table;
        private $page_slug = 'seminar-transportation-counts';

        public function __construct() {
            global $wpdb;
            $this->table = $wpdb->prefix . 'view_transport_counts';

            $end_date = get_field( 'seminar_end_date', 'option' );
            $this->reg_year = $end_date ? date( 'Y', strtotime( $end_date ) ) : date( 'Y' );

            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        }

        public function admin_menu() {
            //            $capability = 'edit_users';
//            $capability = 'manage_options';
//            add_submenu_page(
//                Seminar_Admin_Tools_Menu::SLUG,
//                'Seminar Transportation Counts',
//                'Seminar Transportation Counts',
//                $capability,
//                $this->page_slug,
//                array( $this, 'render_page' )
//            );
        }

        public function enqueue_assets( $hook ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] === $this->page_slug ) {
                if ( defined( 'SR_ASSETS_URL' ) ) {
                    wp_register_style( 'sr-admin-transportation-counts', SR_ASSETS_URL . '/css/common.css', array(), '1.1' );
                    wp_enqueue_style( 'sr-admin-transportation-counts' );
                }
            }
        }

        public function render_page() {
            $display_rows = array();
            if ( isset( $_POST['view-transportation-counts'] ) ) {

                if ( ! current_user_can( 'edit_users' ) ) {
                    echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                    return;
                }

                if ( ! isset( $_POST['sr_transportation_counts_nonce'] ) || ! wp_verify_nonce( $_POST['sr_transportation_counts_nonce'], 'sr_transportation_counts' ) ) {
                    echo '<div class="wrap"><p>Invalid request.</p></div>';
                    return;
                }

                $rows = $this->getTransportationCounts();
                if ( ! empty( $rows ) ) {
                    foreach ( $rows as $countRow ) {
                        $display_rows[] = $this->parse_transportation_count_row( $countRow );
                    }
                }
            }

            $template = SR_PLUGIN_DIR . '/templates/transportation-counts.php';
            if ( file_exists( $template ) ) {
                $reg_year = $this->reg_year;
                include $template;
            } else {
                echo '<div class="wrap"><h1>Transportation Counts</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
        }

        private function parse_transportation_count_row($row) {
            $transport = $row->transport ?? '';
            $count  = $row->registrant_count ?? '';

            return (object) array(
                'transport_option'  => $this->transport_label($transport),
                'count'             => $count
            );
        }

        private function getTransportationCounts() {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array();
            }
            $sql_template = Seminar_Registration_Queries::get_transportation_counts_sql();
            $sql = str_replace( '{table}', esc_sql( $this->table ), $sql_template );
            $prepared = $wpdb->prepare( $sql, intval( $this->reg_year ) );
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

if ( class_exists( 'Seminar_Transportation_Counts' ) && ! isset( $seminar_transportation_counts ) ) {
    $seminar_transportation_counts = new Seminar_Transportation_Counts();
}
