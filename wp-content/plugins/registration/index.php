<?php
/*
Plugin Name: Registration Database Update
Description: Creates a table for seminar registration
Version: 1
Author: Tzvety Dosseva
*/

if( ! class_exists('Registration_Database_Update') ) {

	/**
	 * @author Tz Dosseva
	 */
	class Registration_Database_Update {

		private $parent_slug;
		private $slug;
		private $title;

		private $table;
		private $table_classes;
		
		public function __construct ( ) {
			global $wpdb;

			$this->parent_slug = 'tools.php';
			$this->title = __('Registration Database Update', __CLASS__);
			$this->slug = __CLASS__;

			$this->db_prefix = $wpdb->prefix . 'Seminar_';
			$this->table = $this->db_prefix . 'registrations';
			$this->table_classes = $this->db_prefix . 'classes';

		}
		
		
		/**
		 * Import Form
		 */
		public function import_form ( ) {

		?>
			<style type="text/css">
				#import label {
					margin-right: 20px;
				}
				#import .radioboxes div {
					margin-bottom: 10px;
				}
				#import label.margin-left {
					margin-left: 20px;
				}
				#import .text {
					width: 30em;
				}
				#import p, #import pre {
					clear: left;
				}

				#import .grey-box {
					padding: 15px;
					border: 1px #ddd solid;
					margin-bottom: 10px;
				}
				
				#import ul ul li {
					list-style: disc;
					padding: 0;
					margin: 5px 0 5px 20px;
				}
			</style>


			<div id="import" class="wrap">
				<h2><?php echo $this->title; ?></h2>

				<p>Nothing to do. Custom table has already been created.</p>	
			</div>
			<?php
		}


		/**
		 * admin_menu hook
		 *
		 * Adds an admin menu item for plugin
		 */
		public function admin_menu ( ) {

			$capability = 'edit_users';
			$function = array($this, 'import_form'); // Calls a function in this class
			//add_submenu_page($this->parent_slug, $this->title, $this->title, $capability, $this->slug, $function);
		}

		public function install ( ) {
			global $wpdb;

			// Only install if we haven't already
			//if ( get_option($this->slug . '_db_version', false) !== false )
			//	return;

			$sql = "CREATE TABLE $this->table (
					id BIGINT(20) NOT NULL AUTO_INCREMENT,
					reg_id VARCHAR(60) NOT NULL,
					is_primary INT NOT NULL DEFAULT 0,
					date DATETIME NOT NULL,
					reg_year INT NOT NULL,
					reg_slot INT NOT NULL,
					first_name VARCHAR(60) NOT NULL,
					last_name VARCHAR(60) NOT NULL,
					address1 VARCHAR(60) NULL,
					address2 VARCHAR(60) NULL,
					city VARCHAR(60) NULL,
					state VARCHAR(60) NULL,
					zip VARCHAR(60) NULL,
					country VARCHAR(60) NULL,
					phone VARCHAR(60) NULL,
					email VARCHAR(60) NULL,
					num_days INT NOT NULL,
					age VARCHAR(60) NOT NULL,
					is_eefc INT NOT NULL DEFAULT 0,
					payment VARCHAR(60) NULL,
					transport INT NOT DEFAULT -1,
					dvd INT NOT DEFAULT -1,
					dvd_format VARCHAR(60) NULL,
					balance DECIMAL (7,2) NOT NULL,
					cancel INT NULL DEFAULT 0,
					confirmed INT NOT NULL DEFAULT 0,
					PRIMARY KEY  id (id),
					KEY reg_id (reg_id),
					KEY is_primary (is_primary),
					KEY reg_year (reg_year),
					KEY reg_slot (reg_slot),
					KEY cancel (cancel)
					);";
			
			$sql .= "CREATE TABLE $this->table_classes (
					id BIGINT(20) NOT NULL AUTO_INCREMENT,
					reg_id VARCHAR(60) NOT NULL,
					reg_slot INT NOT NULL,
					class_id INT NOT NULL,
					rent INT NULL DEFAULT NULL,
					level VARCHAR (12) NULL DEFAULT NULL,					
					PRIMARY KEY  id (id),
					KEY reg_slot (reg_slot),
					KEY class_id (class_id),
					KEY rent (rent),
					KEY level (level)
					);";
			
			

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);

			add_option($this->slug . '_db_version', $this->version);
		}

		
		/*
		 * $type = what post type are we look at
		 * Updates $array by reference with posts IDs
		 */
		
		/**
		 * Updates the display with a message
		 *
		 * @param string $message
		 */
		private function update ( $message ) {

			echo htmlspecialchars($message) . "\n";
		}
	}

} // end of if( !class_exists('Registration_Database_Update') )

if( class_exists('Registration_Database_Update') ) {

	$registration_database_update = new Registration_Database_Update();

	register_activation_hook(__FILE__, array(&$registration_database_update, 'install'));

	// add a admin menu option
	add_action('admin_menu', array(&$registration_database_update, 'admin_menu'));
}

?>