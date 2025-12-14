<?php
if ( ! class_exists('Seminar_Counts_Per_Class') ) {

    class Seminar_Counts_Per_Class {

        private $reg_year;
        private $table;
        private $page_slug = 'seminar-counts-per-class';

        public function __construct() {
            global $wpdb;
            $this->table = $wpdb->prefix . 'view_class_registration_counts';

            $end_date = get_field( 'seminar_end_date', 'option' );
            $this->reg_year = $end_date ? date( 'Y', strtotime( $end_date ) ) : date( 'Y' );

            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        }

        public function admin_menu() {
            $capability = 'edit_users';
            add_submenu_page(
                'tools.php',
                'Seminar Counts Per Class',
                'Seminar Counts Per Class',
                $capability,
                $this->page_slug,
                array( $this, 'render_page' )
            );
        }

        public function enqueue_assets( $hook ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] === $this->page_slug ) {
                if ( defined( 'SR_ASSETS_URL' ) ) {
                    wp_register_style( 'sr-admin-counts-per-class', SR_ASSETS_URL . '/css/counts_per_class.css', array(), '1.0' );
                    wp_enqueue_style( 'sr-admin-counts-per-class' );
                }
            }
        }

        public function render_page() {
            $display_rows = array();
            if ( isset( $_POST['view-counts-per-class'] ) ) {

                if ( ! current_user_can( 'edit_users' ) ) {
                    echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                    return;
                }

                if ( ! isset( $_POST['sr_counts_per_class_nonce'] ) || ! wp_verify_nonce( $_POST['sr_counts_per_class_nonce'], 'sr_counts_per_class' ) ) {
                    echo '<div class="wrap"><p>Invalid request.</p></div>';
                    return;
                }

                $rows = $this->getCountsPerClass();
                if ( ! empty( $rows ) ) {
                    foreach ( $rows as $countRow ) {
                        $display_rows[] = $this->parse_counts_per_class_row( $countRow );
                    }
                }
            }

            $template = SR_PLUGIN_DIR . '/templates/counts-per-class.php';
            if ( file_exists( $template ) ) {
                $reg_year = $this->reg_year;
                include $template;
            } else {
                echo '<div class="wrap"><h1>Counts Per Class</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
        }

        private function parse_counts_per_class_row($row) {
            $class_name = $row->class_name ?? '';
            $count  = $row->registrant_count ?? '';

            return (object) array(
                'class_name'    => $class_name,
                'count'         => $count
            );
        }

        private function getCountsPerClass() {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array();
            }
            $sql_template = Seminar_Registration_Queries::get_counts_per_class_sql();
            $sql = str_replace( '{table}', esc_sql( $this->table ), $sql_template );
            $prepared = $wpdb->prepare( $sql, intval( $this->reg_year ) );
            return $wpdb->get_results( $prepared );
        }

    }
}

if ( class_exists( 'Seminar_Counts_Per_Class' ) && ! isset( $seminar_counts_per_class ) ) {
    $seminar_counts_per_class = new Seminar_Counts_Per_Class();
}
