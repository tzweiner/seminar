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
					width: 244px;
					padding: 10px;
					color: #fff;
					text-decoration: none;
					font-size: 22px;
					background: orange;
					text-align: center;
				}
				#wpbody-content {
					width: 90%;
				}
			</style>
			<?php 
			
			if (isset ($_POST['view-registration-by-id'])): 
				if (isset ($_POST['txt-registration-by-id']) && trim ($_POST['txt-registration-by-id']) != ''): 
					$rows = $this->getRegistrant(trim ($_POST['txt-registration-by-id']));
					if (!empty($rows)): ?>
					<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
					<p>Information about an individual registrant.</p>
					<h3>Color coding</h3>
					<p><span style="color: #fff; background: #cc0000;">Red: Cancelled</span> | <span style="color: #fff; background: blue;">Blue: Not confirmed/Email not sent</span></p> 
			
					<div style="overflow:hidden; overflow-x: scroll; width: 100%; max-height: 500px; ">
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
									<th>EMERGENCY</th>
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
									<td><?php echo $row->emergency ; ?></td>
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
					
					<?php endif;  
				else: ?>
				<p>ERROR! No registration ID entered. Please click the back button and enter an ID.</p>
				<?php endif; ?>
				<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			
			<?php 
			elseif (isset ($_POST ['get-num-per-class'])): 
			$rows = $this->getNumberPerClass (); 
			if (!empty ($rows)):?>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			<p>Number of registrants per class.</p>
			<p class="export-link"><a href="#" class="generate-number-per-class">Export to spreadsheet</a></p>
			<div style="overflow:scroll; width: 100%; max-height: 500px; ">
			<table>
				<tbody>
					<tr>
						<th>CLASS</th>						
						<th>COUNT</th>						
					</tr>
					<?php foreach ($rows as $row): ?>
					<tr>
						<td><?php echo $row->class_name; ?></td>						
						<td><?php echo $row->class_count; ?></td>			
					</tr>
					<?php endforeach; ?>
				</tbody>				
			</table>
			</div>
			
			<?php else: ?>
			<p>Not available</p>
			<?php endif; ?>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			
			<?php 
			elseif (isset ($_POST ['get-levels-per-class'])): 
			$rows = $this->getLevelsPerClass (); 
			if (!empty ($rows)): ?>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			<p>Number of registrants in each class level.</p>
			<p class="export-link"><a href="#" class="generate-levels-per-class">Export to spreadsheet</a></p>
			<div style="overflow:scroll; width: 100%; max-height: 500px; ">
			<table>
				<tbody>
					<tr>
						<th>CLASS</th>
						<th>LEVEL</th>
						<th>COUNT</th>						
					</tr>
					<?php foreach ($rows as $row): ?>
					<tr>
						<td><?php echo $row->class_name; ?></td>
						<td><?php echo $row->level; ?></td>
						<td><?php echo $row->count; ?></td>			
					</tr>
					<?php endforeach; ?>
				</tbody>				
			</table>
			</div>
			<?php else: ?>
			<p>Not available</p>
			<?php endif; ?>
			
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			
			<?php 
			
			elseif (isset ($_POST ['get-rentals-per-class'])): 
			$rows = $this->getRentalsPerClass (); 
			if (!empty ($rows)): ?>
			<p>Number of rentals requested per class</p>
			<p class="export-link"><a href="#" class="generate-rentals-per-class">Export to spreadsheet</a></p>
			<div style="overflow:scroll; width: 100%; max-height: 500px; ">
			<table>
				<tbody>
					<tr>
						<th>CLASS</th>
						<th>NUMBER OF RENTALS</th>						
					</tr>
					<?php foreach ($rows as $row): ?>
					<tr>
						<td><?php echo $row->class; ?></td>
						<td><?php echo $row->rent_count; ?></td>		
					</tr>
					<?php endforeach; ?>
				</tbody>				
			</table>
			</div>
			<?php else: ?>
			<p>No rentals requested so far.</p>
			<?php endif; ?>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			
			<?php 
			elseif (isset ($_POST ['get-dvd'])): ?>
			<?php $results = $this->getDVDCounts (); ?>
			<h3>DVD Orders</h3>
			<p>PAL: <?php echo $results ['pal']; ?><br />
			NTSC: <?php echo $results ['ntsc']; ?></p>
			
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
				
			<?php 
			elseif (isset ($_POST ['get-transport'])): ?>
			<h3>Koprivshtitsa Transportation Requested</h3>
			<?php $results = $this->getTransportationRequests(); ?>
			<p>Round Trip: <?php echo $results['round_trip']; ?><br />
			One-way: <?php echo $results['one_way']; ?></p>
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
						
			<?php 
			elseif (isset ($_POST ['get-all'])): 
			$rows = $this->getAllRegistrants();
			if (!empty ($rows)): ?>
			
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			<p>All registrants to date. This does not include information about each person's selection of classes. "Export to CSV" will give you a CSV file of all confirmed registrants (ignoring cancelled and unconfirmed registrations). </p>
			<p class="export-link"><a href="#" class="generate-all-registrants">Export to spreadsheet</a></p>
			<h3>Color coding</h3>
			<p><span style="color: #fff; background: #cc0000;">Red: Cancelled</span> | <span style="color: #fff; background: blue;">Blue: Not confirmed/Email not sent</span></p> 
			
			<div style="overflow:scroll; width: 100%; max-height: 500px; ">
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
						<th>EMERGENCY</th>
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
						<td><?php echo $row->emergency ; ?></td>
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
			elseif (isset ($_POST ['get-onsite-payment'])): 
			$rows = $this->getOnsite();
			if (!empty ($rows)): ?>
			
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p>
			<p>Registrants who selected On-site payment option.</p>
			<p class="export-link"><a href="#" class="generate-onsite">Export to spreadsheet</a></p>
			<div style="overflow:scroll; width: 100%; max-height: 500px; ">
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
						<th>EMERGENCY</th>
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
						<td><?php echo $row->emergency ; ?></td>
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
			<p><a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to Seminar Admin Area</a></p><?php 
			
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
				
				<!-- On-site payments -->
				<form name="form-onsite-payment" method="post">
										
					<input type="submit" name="get-onsite-payment" value="Get Registrants with On-site payment option" />
				</form>
				
				
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
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND cancel = 0
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
		
		private function getTransportationRequests () {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
				
			$sql = "SELECT count(transport) one_way
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND transport = 1
					AND cancel <> 1
					AND confirmed = 1";
				
			$results = $wpdb->get_results ($sql);
				
			$one_way_count = 0;
			if (!empty ($results)) $one_way_count = $results[0]->one_way;
				
			$sql = "SELECT count(transport) round_trip
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND transport = 2
					AND cancel <> 1
					AND confirmed = 1";
			
			$results = $wpdb->get_results ($sql);
			
			$round_trip_count = 0;
			if (!empty ($results)) $round_trip_count = $results[0]->round_trip;
				
			return array ('round_trip'=>$round_trip_count, 'one_way'=>$one_way_count);

		}
		
		private function getDVDCounts () {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
			
			$sql = "SELECT count(dvd_format) PAL
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND dvd_format = 'pal'
					AND cancel <> 1
					AND confirmed = 1";
			
			$results = $wpdb->get_results ($sql);
			
			$pal_count = 0;
			if (!empty ($results)) $pal_count = $results[0]->PAL;
			
			$sql = "SELECT count(dvd_format) NTSC
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND dvd_format = 'ntsc'
					AND cancel <> 1
					AND confirmed = 1";
				
			$results = $wpdb->get_results ($sql);
				
			$ntsc_count = 0;
			if (!empty ($results)) $ntsc_count = $results[0]->NTSC;
			
			return array ('pal'=>$pal_count, 'ntsc'=>$ntsc_count);
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
		
		private function getAllRegistrantsClean () {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
				
			$sql = "SELECT *
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND cancel = 0
					AND confirmed = 1
					ORDER BY id ASC";
		
			$results = $wpdb->get_results ($sql);
						
					return $results;
		}
		
		private function getRegistrant ($id) {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
		
			$sql = "SELECT *
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND id = $id";
		
			$results = $wpdb->get_results ($sql);

			return $results;
		}
		
		private function getOnsite () {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
			
			$sql = "SELECT *
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND payment = 'on-site' 
					AND confirmed = 1
					AND cancel = 0 
					ORDER BY id ASC";

			$results = $wpdb->get_results ($sql);
	
			return $results;
		}
		
		private function getRentalsPerClass () {

			global $wpdb;
			$table = 'wp_Seminar_registrations';
			$table_classes = 'wp_Seminar_classes';
			$wp_posts = 'wp_posts';

			$sql = "SELECT T3.post_title class, SUM(T1.rent) rent_count
					FROM $table_classes T1
					LEFT JOIN $table T2
					ON (T1.reg_id = T2.reg_id AND T1.reg_slot = T2.reg_slot)
					LEFT JOIN $wp_posts T3
					ON T3.ID = T1.class_id
					WHERE T2.cancel = 0
					AND T1.rent= 1
					AND T2.confirmed = 1
					AND T2.reg_year = " . intval ($this->reg_year) . "
					AND T3.post_type = 'classes'
					AND T3.post_status = 'publish'
					GROUP BY T1.class_id, T1.rent"; 
			
			$results = $wpdb->get_results ($sql);
			
			return $results;
			
		}
		
		private function getLevelsPerClass () {
		
			global $wpdb;
			$table = 'wp_Seminar_registrations';
			$table_classes = 'wp_Seminar_classes';
			$wp_posts = 'wp_posts';
		
			$sql = "SELECT T1.post_title class_name, T2.level, COUNT(T2.level) count
					FROM $wp_posts T1
					LEFT JOIN $table_classes T2
					ON T1.ID = T2.class_id
					LEFT JOIN $table T3
					ON (T2.reg_id = T3.reg_id AND T2.reg_slot = T3.reg_slot)
					WHERE T1.post_status = 'publish'
					AND T1.post_type = 'classes'
					AND T3.reg_year = " . intval ($this->reg_year) . "
					AND T3.cancel = 0
					AND T3.confirmed = 1
					GROUP BY T1.ID, T2.level";
		
			$results = $wpdb->get_results ($sql);
						
			return $results;
				
		}
		
		private function getNumberPerClass () {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
			$table_classes = 'wp_Seminar_classes';
			$wp_posts = 'wp_posts';
			
			$sql = "SELECT T3.post_title class_name, COUNT(T1.class_id) class_count
					FROM $table_classes T1
					LEFT JOIN $wp_posts T3
					ON T3.ID = T1.class_id
					LEFT JOIN $table T2
					ON (T1.reg_id = T2.reg_id AND T1.reg_slot = T2.reg_slot)
					WHERE T3.post_type= 'classes'
					AND T3.post_status = 'publish'
					AND T2.cancel = 0
					AND T2.confirmed = 1
					AND T2.reg_year = " . intval ($this->reg_year) . "
					GROUP BY T1.class_id";

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
		
		public function admin_javascript() {
			?>
		    <script type="text/javascript">
		      jQuery(function ($) {
		        $('body').on('click', '.generate-all-registrants', function ( e ) {
		          e.preventDefault();

		          generateCVSAllRegistrants ();
		          
		          return false;
		        });

		        $('body').on('click', '.generate-onsite', function ( e ) {
			          e.preventDefault();

			          generateOnsiteRegistrants ();
			          
			          return false;
			        });

		        $('body').on('click', '.generate-rentals-per-class', function ( e ) {
			          e.preventDefault();

			          generateRentalsPerClass ();
			          
			          return false;
			        });

		        $('body').on('click', '.generate-levels-per-class', function ( e ) {
			          e.preventDefault();

			          generateLevelsPerClass ();
			          
			          return false;
			        });

		        $('body').on('click', '.generate-number-per-class', function ( e ) {
			          e.preventDefault();

			          generateNumberPerClass ();
			          
			          return false;
			        });
		        
		      });

				function generateCVSAllRegistrants() {
					window.open(ajaxurl + '?' + jQuery.param({ action: 'all_registrants_csv' }));
				}
				function generateOnsiteRegistrants () {
					window.open(ajaxurl + '?' + jQuery.param({ action: 'onsite_csv' }));
				}
				function generateRentalsPerClass () {
					window.open(ajaxurl + '?' + jQuery.param({ action: 'rentals_per_class_csv' }));
				}
				function generateLevelsPerClass () {
					window.open(ajaxurl + '?' + jQuery.param({ action: 'levels_per_class_csv' }));
				}
				function generateNumberPerClass () {
					window.open(ajaxurl + '?' + jQuery.param({ action: 'number_per_class_csv' }));
				}
		    </script>
		    <?php
		}
		
		public function admin_ajax_number_per_class_csv () {
			//helper vars
			$first = true; // on first iteration of results, create the first row of column names
			$count = 0; // keep count of current row
			$last_index = 0; // last index of 'good' columns, used to filter out columns that come after custom questions.
			
			$csv = '';
			$rows = $this->getNumberPerClass();
			$lines = array ();
			
			// first add the headers
			foreach ( $rows[0] as $index=>$value ) {
				if ($index == 'class_name') {
					$lines[0][0] = "'" . strtoupper ($index);
					continue;
				}
				$key = strtoupper ($index);
				$lines[0][] .= $key;
				$last_index++;
			}
			
			// now do the actual values
			foreach ( $rows as $row_index=>$row ) {
				$input_count = 0;
				foreach ( $row as $index=>$value ) {
					if( $input_count <= $last_index ){
							
						$lines[ ($row_index + 1) ][] = $this->cleanData ($value);
							
					}
					$input_count++;
				}
					
			}
			
			$csv = '';
			foreach ( $lines as $line ) {
				$csv .= $this->str_putcsv($line);
			}
				
			// Stream file
			header('Content-Type: text/csv');
			//header("Content-Type: application/vnd.ms-excel");
			header('Content-Disposition: attachment;filename="number-per-class-' . date('Y-m-d') . '.csv');
			echo $csv;
			die();
		}
		
		public function admin_ajax_levels_per_class_csv () {
			//helper vars
			$first = true; // on first iteration of results, create the first row of column names
			$count = 0; // keep count of current row
			$last_index = 0; // last index of 'good' columns, used to filter out columns that come after custom questions.
				
			$csv = '';
			$rows = $this->getLevelsPerClass();
			$lines = array ();
				
			// first add the headers
			foreach ( $rows[0] as $index=>$value ) {
				if ($index == 'class_name') {
					$lines[0][0] = "'" . strtoupper ($index);
					continue;
				}
				$key = strtoupper ($index);
				$lines[0][] .= $key;
				$last_index++;
			}
				
			// now do the actual values
			foreach ( $rows as $row_index=>$row ) {
				$input_count = 0;
				foreach ( $row as $index=>$value ) {
					if( $input_count <= $last_index ){
							
						$lines[ ($row_index + 1) ][] = $this->cleanData ($value);
							
					}
					$input_count++;
				}
					
			}
				
			$csv = '';
			foreach ( $lines as $line ) {
				$csv .= $this->str_putcsv($line);
			}
			
			// Stream file
			header('Content-Type: text/csv');
			//header("Content-Type: application/vnd.ms-excel");
			header('Content-Disposition: attachment;filename="levels-per-class-' . date('Y-m-d') . '.csv');
			echo $csv;
			die();

		}
		
		public function admin_ajax_rentals_per_class_csv () {
			//helper vars
			$first = true; // on first iteration of results, create the first row of column names
			$count = 0; // keep count of current row
			$last_index = 0; // last index of 'good' columns, used to filter out columns that come after custom questions.
			
			$csv = '';
			$rows = $this->getRentalsPerClass();
			$lines = array ();
			
			// first add the headers
			foreach ( $rows[0] as $index=>$value ) {
				if ($index == 'class') {
					$lines[0][0] = "'" . strtoupper ($index);
					continue;
				}
				$key = strtoupper ($index);
				$lines[0][] .= $key;
				$last_index++;
			}
			
			// now do the actual values
			foreach ( $rows as $row_index=>$row ) {
				$input_count = 0;
				foreach ( $row as $index=>$value ) {
					if( $input_count <= $last_index ){
							
						$lines[ ($row_index + 1) ][] = $this->cleanData ($value);
							
					}
					$input_count++;
				}
					
			}
			
			$csv = '';
			foreach ( $lines as $line ) {
				$csv .= $this->str_putcsv($line);
			}
				
			// Stream file
			header('Content-Type: text/csv');
			//header("Content-Type: application/vnd.ms-excel");
			header('Content-Disposition: attachment;filename="rentals-per-class-' . date('Y-m-d') . '.csv');
			echo $csv;
			die();

		}
		
		public function admin_ajax_onsite_csv () {
			//helper vars
			$first = true; // on first iteration of results, create the first row of column names
			$count = 0; // keep count of current row
			$last_index = 0; // last index of 'good' columns, used to filter out columns that come after custom questions.
				
			$csv = '';
			$rows = $this->getOnsite();
			$lines = array ();
				
			// first add the headers
			foreach ( $rows[0] as $index=>$value ) {
				if ($index == 'id') {
					$lines[0][0] = "'" . strtoupper ($index);
					continue;
				}
				$key = strtoupper ($index);
				$lines[0][] .= $key;
				$last_index++;
			}
				
			// now do the actual values
			foreach ( $rows as $row_index=>$row ) {
				$input_count = 0;
				foreach ( $row as $index=>$value ) {
					if( $input_count <= $last_index ){
			
						$lines[ ($row_index + 1) ][] = $this->cleanData ($value);
			
					}
					$input_count++;
				}
			
			}
				
			$csv = '';
			foreach ( $lines as $line ) {
				$csv .= $this->str_putcsv($line);
			}
			
			// Stream file
			header('Content-Type: text/csv');
			//header("Content-Type: application/vnd.ms-excel");
			header('Content-Disposition: attachment;filename="onsite-registrants-' . date('Y-m-d') . '.csv');
			echo $csv;
			die();
		}
		
		public function admin_ajax_all_registrants_csv () {
			
			//helper vars
			$first = true; // on first iteration of results, create the first row of column names
			$count = 0; // keep count of current row
			$last_index = 0; // last index of 'good' columns, used to filter out columns that come after custom questions.
			
			$csv = '';
			$rows = $this->getAllRegistrantsClean();
			$lines = array ();
			
			// first add the headers
			foreach ( $rows[0] as $index=>$value ) {
				if ($index == 'id') {
					$lines[0][0] = "'" . strtoupper ($index); 
					continue;
				}
				$key = strtoupper ($index);
				$lines[0][] .= $key;
				$last_index++;
			}
			
			// now do the actual values
			foreach ( $rows as $row_index=>$row ) {
				$input_count = 0;
				foreach ( $row as $index=>$value ) {
					if( $input_count <= $last_index ){						
						
						$lines[ ($row_index + 1) ][] = $this->cleanData ($value);
						
					}
					$input_count++;
				}
				
			} 
			
			$csv = '';
			foreach ( $lines as $line ) {
				$csv .= $this->str_putcsv($line);
			}

			// Stream file
			header("Cache-Control: must-revalidate");
			header("Pragma: must-revalidate");
			header('Content-Type: text/csv');
			//header("Content-Type: application/vnd.ms-excel");
			header('Content-Disposition: attachment;filename="all-registrants-' . date('Y-m-d') . '.csv');
		 	echo $csv;			
			die();
		}
		
		private function cleanData(&$str) {
			$str = preg_replace("/\t/", "\\t", $str);
		    $str = preg_replace("/\r?\n/", "\\n", $str);
		    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
		    return $str;
		}
		
		// this makes the rows of input all tab separated and nicely formatted for csv/excel
		private function str_putcsv( $input, $delimiter = ',', $enclosure = '"' ) {
			// Open a memory "file" for read/write...
			$fp = fopen('php://temp', 'r+');
			// ... write the $input array to the "file" using fputcsv()...
			fputcsv($fp, $input, $delimiter, $enclosure);
			// ... rewind the "file" so we can read what we just wrote...
			rewind($fp);
			// ... read the entire line into a variable...
			$data = fgets($fp);
			// ... close the "file"...
			fclose($fp);
			// ... and return the $data to the caller, leaving the trailing newline from fgets().
			return $data;
		}
	}

} // end of if( !class_exists('Registration_Database_Update') )

if( class_exists('Seminar_Registration_Admin') ) {

	$seminar_registration_admin = new Seminar_Registration_Admin();

	register_activation_hook(__FILE__, array(&$seminar_registration_admin, 'install'));

	// add a admin menu option
	add_action('admin_menu', array(&$seminar_registration_admin, 'admin_menu'));
	add_action('wp_ajax_all_registrants_csv', array(&$seminar_registration_admin, 'admin_ajax_all_registrants_csv'));
	add_action('wp_ajax_onsite_csv', array(&$seminar_registration_admin, 'admin_ajax_onsite_csv'));
	add_action('wp_ajax_rentals_per_class_csv', array(&$seminar_registration_admin, 'admin_ajax_rentals_per_class_csv'));
	add_action('wp_ajax_levels_per_class_csv', array(&$seminar_registration_admin, 'admin_ajax_levels_per_class_csv'));
	add_action('wp_ajax_number_per_class_csv', array(&$seminar_registration_admin, 'admin_ajax_number_per_class_csv'));
	add_action('admin_footer', array(&$seminar_registration_admin, 'admin_javascript'));
}

?>