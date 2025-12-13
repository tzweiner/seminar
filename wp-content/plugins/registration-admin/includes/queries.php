<?php
// File: wp-content/plugins/registration-admin/includes/queries.php
if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {

    class Seminar_Registration_Queries {

        /**
         * SQL template for registrants who ordered media.
         * Replace `{table}` with the escaped table name before calling $wpdb->prepare().
         * Expecting a single %d placeholder for reg_year.
         */
        public static function registrants_with_media_orders_sql() {
            return "
            SELECT * FROM wp_view_media_orders
        ";
        }
    }
}
