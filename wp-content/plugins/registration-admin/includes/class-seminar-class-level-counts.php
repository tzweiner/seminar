<?php
if ( ! class_exists('Seminar_Class_Level_Counts') ) {

    class Seminar_Class_Level_Counts {

        private $reg_year;
        private $table;
        private $page_slug = 'seminar-class-level-counts';

        public function __construct() {
            global $wpdb;
            $this->table = $wpdb->prefix . 'view_class_level_counts';

            $end_date = get_field( 'seminar_end_date', 'option' );
            $this->reg_year = $end_date ? date( 'Y', strtotime( $end_date ) ) : date( 'Y' );

            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        }

        public function admin_menu() {
            $capability = 'edit_users';
            add_submenu_page(
                'tools.php',
                'Seminar Class Level Counts',
                'Seminar Class Level Counts',
                $capability,
                $this->page_slug,
                array( $this, 'render_page' )
            );
        }

        public function enqueue_assets( $hook ) {
            if ( isset( $_GET['page'] ) && $_GET['page'] === $this->page_slug ) {
                if ( defined( 'SR_ASSETS_URL' ) ) {
                    wp_register_style( 'sr-admin-class-level-counts', SR_ASSETS_URL . '/css/class_level_counts.css', array(), '1.0' );
                    wp_enqueue_style( 'sr-admin-class-level-counts' );
                }
            }
        }

        public function render_page() {
            $display_rows = array();
            if ( isset( $_POST['view-class-level-counts'] ) ) {

                if ( ! current_user_can( 'edit_users' ) ) {
                    echo '<div class="wrap"><p>Insufficient permissions.</p></div>';
                    return;
                }

                if ( ! isset( $_POST['sr_class_level_counts_nonce'] ) || ! wp_verify_nonce( $_POST['sr_class_level_counts_nonce'], 'sr_class_level_counts' ) ) {
                    echo '<div class="wrap"><p>Invalid request.</p></div>';
                    return;
                }

                $rows = $this->getClassLevelCounts();
                if ( ! empty( $rows ) ) {
                    foreach ( $rows as $countRow ) {
                        $display_rows[] = $this->parse_class_level_counts_row( $countRow );
                    }
                }
            }

            $template = SR_PLUGIN_DIR . '/templates/class-level-counts.php';
            if ( file_exists( $template ) ) {
                $reg_year = $this->reg_year;
                include $template;
            } else {
                echo '<div class="wrap"><h1>Class Level Counts</h1>';
                echo '<p>Template file missing: ' . esc_html( $template ) . '</p></div>';
            }
        }

        private function parse_class_level_counts_row($row) {
            $class_name = $row->class_name ?? '';
            $level  = $row->level ?? '';
            $level_count = $row->level_count ?? '';

            return (object) array(
                'class_name'    => $class_name,
                'level'         => $level,
                'count'         => $level_count
            );
        }

        private function getClassLevelCounts() {
            global $wpdb;
            if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {
                return array();
            }
            $sql_template = Seminar_Registration_Queries::get_class_level_counts_sql();
            $sql = str_replace( '{table}', esc_sql( $this->table ), $sql_template );
            $prepared = $wpdb->prepare( $sql, intval( $this->reg_year ) );
            return $wpdb->get_results( $prepared );
        }

    }
}

if ( class_exists( 'Seminar_Class_Level_Counts' ) && ! isset( $seminar_class_level_counts ) ) {
    $seminar_class_level_counts = new Seminar_Class_Level_Counts();
}
