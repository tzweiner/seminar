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
            SELECT * FROM {table}
        ";
        }

        /**
         * SQL to get registration event by registration_event_id
         */
        public static function get_registration_event_sql() {
            return "SELECT * FROM {registration_events_table} WHERE registration_event_id = %s";
        }

        /**
         * SQL to cancel registration by registration_event_id
         */
        public static function cancel_registration_by_event_id_sql() {
            return "UPDATE {registration_events_table} SET registration_status = 'cancelled' WHERE registration_event_id = %s";
        }

        /**
         * SQL to get count for transport options
         */
        public static function get_transportation_counts_sql() {
            return "SELECT * FROM {table}";
        }

        /**
         * SQL to get registrants for onsite payment option
         */
        public static function get_onsite_payment_sql() {
            return "SELECT * FROM {table}";
        }

        /**
         * SQL to get Bulgarian registrants
         */
        public static function get_bulgarian_registrants_sql() {
            return "SELECT * FROM {table}";
        }

        /**
         * SQL to get count per class
         */
        public static function get_counts_per_class_sql() {
            return "SELECT * FROM {table}";
        }

        /**
         * SQL to get registrants with rented instruments
         */
        public static function get_rented_sql() {
            return "SELECT * FROM {table}";
        }

        /**
         * SQL to get registrant counts on class levels
         */
        public static function get_class_level_counts_sql() {
            return "SELECT * FROM {table}";
        }
    }
}
