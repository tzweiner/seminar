<?php
/*
Plugin Name: Seminar Registration Admin
Description: An interface for admin activities for Seminar registrants
Version: 1
Author: Tzvety Weiner
*/

if( ! class_exists('Seminar_Registration_Admin') ) {

	/**
	 * @author Tz Weiner
	 */
	class Seminar_Registration_Admin {

		private $parent_slug;
		private $slug;
		private $title;

		private $table;
		private $table_classes;
		private $wp_posts;
		
		private $reg_page;
		private $reg_year;
		
		public function __construct ( ) {
			global $wpdb;

			$this->parent_slug = 'tools.php';
			$this->title = __('Seminar Registration Admin', __CLASS__);
			$this->slug = __CLASS__;

			$this->db_prefix = $wpdb->prefix . 'Seminar_';
			$this->table = $this->db_prefix . 'registrations';
			$this->table_classes = $this->db_prefix . 'classes';
			$this->wp_posts = 'wp_posts';
			
			$this->reg_page = $this->getPostByTitle ('page', 'Register');	// get register page object
			
			$this->reg_year = date ('Y', strtotime(get_field ('seminar_end_date', 'option')));

		}
		
		
		/**
		 * Import Form
		 */
		public function import_form ( ) {
			
			?>
			<style>
				.blue {
					background: blue;
				}
				
				.red {
					background: #cc0000;
				}
				
				.blue td, .red td {
					color: #fff;
				}
				
				table {
					background: #fff;
					border-collapse: collapse;					
				}
				/* .tools_page_Seminar_Registration_Admin table {
					height: 500px;
				} */
				.tools_page_Seminar_Registration_Admin table,
				.tools_page_Seminar_Registration_Admin tr,
				.tools_page_Seminar_Registration_Admin th,
				.tools_page_Seminar_Registration_Admin td {
					border-collapse: collapse;
					border: 1px solid #ddd;
					background: #fff;
				}
				
				tr.blue, tr.blue td {
					background: blue;
				}
				tr.red,tr.red td {
					background: #cc0000;
				}
				td,th {
					min-width: 100px; 
					padding: 10px;
				}
				.tools_page_Seminar_Registration_Admin tr:first-child th {
					color: #fff;
					background: #333;				
				}
				td:first-child, th:first-child {
					min-width: 30px;
					text-align: center;
				}
				.export-link {
					float: right;
				}
				.export-link a {
					display: block;
					width: 200px;
					padding: 10px;
					color: #fff;
					text-decoration: none;
					font-size: 22px;
					background: orange;
					text-align: center;
				}
				
			</style>
			<?php 
			
			if (isset ($_POST['view-registration-by-id'])): 
				if (isset ($_POST['txt-registration-by-id']) && trim ($_POST['txt-registration-by-id']) != ''): ?>
				<p>Will get info.</p> <?php 
				else: ?>
				<p>ERROR! No registration ID entered. Please click the back button and enter an ID.</p>
				<?php endif; ?>
				<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			
			<?php 
			elseif (isset ($_POST ['get-num-per-class'])): ?>
			<p>Will get info.</p>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			
			<?php 
			elseif (isset ($_POST ['get-levels-per-class'])): ?>
			<p>Will get info.</p>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			
			<?php 
			
			elseif (isset ($_POST ['get-rentals-per-class'])): ?>
			<p>Will get info.</p>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			
			<?php 
			elseif (isset ($_POST ['get-dvd'])): ?>
			<p>Will get info.</p>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
				
			<?php 
			elseif (isset ($_POST ['get-transport'])): ?>
			<p>Will get info.</p>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
						
			<?php 
			elseif (isset ($_POST ['get-all'])): 
			$rows = $this->getAllRegistrants();
			if (!empty ($rows)): ?>
			
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			<p>All registrants to date. This does not include information about each person's selection of classes.</p>
			<p class="export-link"><a href="#" onclick="javascript: alert('will generate');">Export to CSV</a></p>
			<h3>Color coding</h3>
			<p><span style="color: #fff; background: #cc0000;">Red: Cancelled</span> | <span style="color: #fff; background: blue;">Blue: Not confirmed</span></p> 
			
			<div style="overflow:scroll; width: 960px; max-height: 500px; ">
			<table>
				<tbody>
					<tr>
						<th>ID</th>
						<th>DATE</th>
						<th>FIRST NAME</th>
						<th>LAST NAME</th>
						<th>ADDRESS1</th>
						<th>ADDRESS2</th>
						<th>CITY</th>
						<th>STATE</th>
						<th>ZIP</th>
						<th>COUNTRY</th>
						<th>PHONE</th>
						<th>EMAIL</th>
						<th>NUMBER OF DAYS</th>
						<th>AGE GROUP</th>
						<th>EEFC MEMBER</th>
						<th>PAYMENT OPTION</th>
						<th>TRANSPORTATION</th>
						<th>DVD</th>
						<th>DVD FORMAT</th>
						<th>BALANCE</th>
						<th>CANCELLED</th>
						<th>CONFIRMED</th>
					</tr>
					<?php foreach ($rows as $row): ?>
					<tr class="<?php if ($row->cancel == 1) echo 'red'; else if ($row->confirmed == 0) echo 'blue'; ?>">
						<td><?php echo $row->id ; ?></td>
						<td><?php echo $row->date ; ?></td>
						<td><?php echo $row->first_name ; ?></td>
						<td><?php echo $row->last_name ; ?></td>
						<td><?php echo $row->address1 ; ?></td>
						<td><?php echo $row->address2 ; ?></td>
						<td><?php echo $row->city ; ?></td>
						<td><?php echo $row->state ; ?></td>
						<td><?php echo $row->zip ; ?></td>
						<td><?php echo $row->country ; ?></td>
						<td><?php echo $row->phone ; ?></td>
						<td><?php echo $row->email ; ?></td>
						<td><?php echo $row->num_days ; ?></td>
						<td><?php echo $row->age ; ?></td>
						<?php if ($row->is_eefc): ?>
						<td><?php echo 'Yes'; ?></td>
						<?php else: ?>
						<td><?php echo 'No'; ?></td>
						<?php endif; ?>
						<td><?php echo $row->payment ; ?></td>
						<?php if ($row->transport == -1): ?>
						<td><?php echo 'N/A' ; ?></td>
						<?php elseif ($row->transport == 0):?>
						<td><?php echo "No"; ?></td>
						<?php elseif ($row->transport == 2):  ?>
						<td><?php echo "Round trip"; ?></td>
						<?php else: ?>
						<td><?php echo "One-way"; ?></td>
						<?php endif; ?></td>
						<?php if ($row->dvd == 1): ?>
						<td><?php echo 'Yes' ; ?></td>
						<?php elseif ($row->dvd == 0): ?>
						<td><?php echo 'No' ; ?></td>
						<?php else: ?>
						<td><?php echo 'N/A'; ?></td>
						<?php endif; ?>
						
						<?php if ($row->dvd_format == 'ntsc'): ?>
						<td><?php echo 'NTSC' ; ?></td>
						<?php elseif ($row->dvd_format == 'pal'): ?>
						<td><?php echo 'PAL' ; ?></td>
						<?php else: ?>
						<td><?php echo 'N/A'; ?></td>
						<?php endif; ?>
						
						<td><?php echo $row->balance ; ?></td>
						<?php if ($row->cancel): ?>
						<td><?php echo 'Yes'; ?></td>
						<?php else: ?>
						<td><?php echo 'No'; ?></td>
						<?php endif; ?>
						<?php if ($row->confirmed): ?>
						<td><?php echo 'Yes'; ?></td>
						<?php else: ?>
						<td><?php echo 'No'; ?></td>
						<?php endif; ?>						
					</tr>
					<?php endforeach; ?>
				</tbody>				
			</table>
			</div>
			<?php else: ?>
			<p>No registrants found.</p>
			<?php endif; ?>	
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			
			<?php 		
			else: 
		?>
			<style type="text/css">
				#import form,
				#import div.white {
					-webkit-box-sizing: border-box;
					-moz-box-sizing: border-box;
					box-sizing: border-box;
					padding: 3%;
					border: 1px solid #ccc;
					background: #fff;
					margin-bottom: 2em;
				}
				#import div.white form {
					border: 0;
					margin: 0;
					padding: 0;
				}
				#import label {
					margin-right: 20px;
				}
				
				#import form h3 {
					margin-top: 0;
				}
				#import .radioboxes div {
					margin-bottom: 10px;
				}
				#import label.margin-left {
					margin-left: 20px;
				}
				#import .text {
					
				}
				#import p, #import pre {
					clear: left;
				}

				#import .grey-box {
					padding: 15px;
					border: 1px #ddd solid;
					margin-bottom: 10px;
				}
				
		
				
			</style>


			<div id="import" class="wrap">
				<h2><?php echo $this->title; ?></h2>
								
				<p class="text">Please use the forms below to query the database.<br />Results are generated for one form at a time.</p>
				
				<div class="white">
					<?php $total = $this->get_total(); ?>
					<p>There are total of <strong><?php echo $total; ?></strong> confirmed registrants for Seminar <?php echo date ('Y', strtotime (get_field('seminar_start_date', 'option'))); ?>.</p>
					<?php if($total): ?>
					<form name="form-view-rentals-per-class" method="post">
						<input type="submit" name="get-all" value="Get All Registrants" />
					</form>
					<?php endif; ?>
				</div>
				
				<?php if ($total): ?>
				<!-- Registrant levels by class -->
				<form name="form-view-levels-per-class" method="post">
										
					<input type="submit" name="get-levels-per-class" value="Get Registrant Levels per Class" />
				</form>
				
				<!-- Registrant rentals by class -->
				<form name="form-view-rentals-per-class" method="post">
										
					<input type="submit" name="get-rentals-per-class" value="Get Rent/Borrow per Class" />
				</form>
				
				<!-- Registrants by class -->
				<form name="form-view-numbers-per-class" method="post">
										
					<input type="submit" name="get-num-per-class" value="Get Number of Registrants per Class" />
				</form>
				
				<!-- View registration -->
				<form name="form-view-registration-by-id" method="post">
					<h3>Registration information by id</h3>
					<p class="text">Enter a registration number and click Get Info to get all the information in the database for this registration.</p>
					<label for="txt-registration-by-id">Registration ID *</label>
					<input type="text" name="txt-registration-by-id" id="txt-registration-by-id" />
					<input type="submit" name="view-registration-by-id" value="Get Info" />
				</form>
				
				<?php 
				if (get_field ('show_dvd_available_field', $this->reg_page->ID)):
				?>
				<!-- Registrants with DVD orders -->
				<form name="form-dvd" method="post">
										
					<input type="submit" name="get-dvd" value="Get DVD orders" />
				</form>
				
				<?php endif; ?>
				
				<?php 
				if (get_field ('show_koprivshtitsa_transportation_field', $this->reg_page->ID)):
				?>
				<!-- Registrants with transportation orders -->
				<form name="form-transport" method="post">
										
					<input type="submit" name="get-transport" value="Get Transportation orders" />
				</form>
				
				<?php endif; ?>
				
				<!-- Re-send email -->
				<!-- <form name="form-send-email" method="post">
					<h3>Re-send registration email</h3>
					<p class="text">Enter a registration number and click Send Email to re-send registration email.</p>
					<label for="txt-send-email">Registration ID *</label>
					<input type="text" name="txt-send-email" id="txt-send-email" />
					<input type="submit" name="send-email" value="Send Email" />
				</form> -->
				
				<?php endif; ?>
				
			</div>
			<?php
			endif; 
		}


		/**
		 * admin_menu hook
		 *
		 * Adds an admin menu item for plugin
		 */
		public function admin_menu ( ) {

			$capability = 'edit_users';
			$function = array($this, 'import_form'); // Calls a function in this class
			add_submenu_page($this->parent_slug, $this->title, $this->title, $capability, $this->slug, $function);
		}

		public function install ( ) {
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);

			add_option($this->slug . '_db_version', $this->version);
		}
		
		private function get_total () {

			global $wpdb;
			
			$sql = "SELECT id
					FROM $this->table
					WHERE cancel = 0
					AND confirmed = 1";
			
			$results = $wpdb->get_results ($sql);
			
			return count ($results);

		}
		
		private function getPostByTitle ($type, $name) {
			$args = array (
					'post_type' 	=> $type,
					'posts_per_page' => -1,
					'post_status' 	=> 'publish',
					'orderby' 		=> 'title',
					'order' 		=> 'ASC'
			);
			
			$all_posts = get_posts ($args);
			
			foreach ($all_posts as $this_post) {
				if ($this_post->post_title == $name) return $this_post;
			}
			
			return false;
		}

		private function getAllRegistrants () {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
			
			$sql = "SELECT * 
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . " 
					ORDER BY id ASC";
			
			$results = $wpdb->get_results ($sql);
			
			return $results;
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
		
		private function getColorCoding ($row) {
			if ($row->cancel == 1) return 'red';
			if ($row->confirmed != 1) return 'blue';
			return '';
			
		}
	}

} // end of if( !class_exists('Registration_Database_Update') )

if( class_exists('Seminar_Registration_Admin') ) {

	$seminar_segistration_admin = new Seminar_Registration_Admin();

	register_activation_hook(__FILE__, array(&$seminar_segistration_admin, 'install'));

	// add a admin menu option
	add_action('admin_menu', array(&$seminar_segistration_admin, 'admin_menu'));
}

?>