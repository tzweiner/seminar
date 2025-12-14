<?php
// File: wp-content/plugins/registration-admin/includes/queries.php
if ( ! class_exists( 'Seminar_Registration_Queries' ) ) {

    class Seminar_Registration_Queries {

        /**
         * SQL registrants who ordered media.
         */
        public static function registrants_with_media_orders_sql() {
            return "
            SELECT * FROM wp_view_media_orders
        ";
        }

        /**
         * SQL for all confirmed registrants
         */
        public static function registrants_confirmed_sql() {
            return "
            SELECT * FROM wp_view_confirmed_registrants
        ";
        }

        /**
         * SQL for all registrants
         */
        public static function all_registrants_sql() {
            return "
            SELECT * FROM wp_Seminar_registrations
        ";
        }
    }
}
