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

        /**
         * SQL template for the primary registrant lookup.
         * Expecting %s for registration_event_id and %d for reg_year.
         */
        public static function primary_registrant_by_event_sql() {
            return "
            SELECT * FROM {table}
            WHERE registration_event_id = %s
              AND is_primary = 1
              AND reg_year = %d
            LIMIT 1
        ";
        }
    }
}
