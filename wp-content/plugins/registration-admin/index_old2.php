<?php
/*
Plugin Name: Seminar Registration Admin
Description: An interface for admin activities for Seminar registrants
Version: 2.1
Author: Tzvety Dosseva
*/

if( ! class_exists('Seminar_Registration_Admin') ) {

	/**
	 * @author Tz Dosseva
	 */
	class Seminar_Registration_Admin {

		private $parent_slug;
		private $slug;
		private $title;

		private $table_registrants;
        private $table_registration_events;
		private $table_classes;
		private $wp_posts;
		
		private $registration_page;
		private $reg_year;
		
		public function __construct ( ) {
			global $wpdb;

			$this->parent_slug = 'tools.php';
			$this->title = __('Seminar Registration Admin Tools', __CLASS__);
			$this->slug = __CLASS__;

			$this->db_prefix = $wpdb->prefix . 'Seminar_';
			$this->table_registrants = $this->db_prefix . 'registrants';
			$this->table_classes = $this->db_prefix . 'classes';
            $this->table_registration_events = $this->db_prefix . 'registration_events';
			$this->wp_posts = 'wp_posts';
			
			$this->registration_page = $this->getPostByTitle ('page', 'Register');	// get register page object
			
			$this->reg_year = date ('Y', strtotime(get_field ('seminar_end_date', 'option')));

		}
		
		
		/**
		 * Import Form
		 */
		public function import_form ( ) { ?>
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

tr.red, tr.red td {
	background: #cc0000;
}

td, th {
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
			if (isset($_POST['view-media-orders-names-and-addresses'])):
			$registrants = $this->getRegistrantsWithMediaOrders ();
				
				if (isset($registrants) && !empty($registrants)): ?>
<p>Names and Addresses for Media Orders</p>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<p class="export-link">
	<a href="#" class="generate-media-orders-names-and-addresses">Export to
		spreadsheet</a>
</p>

<table>
	<tbody>
		<tr>
			<th>NAME</th>
			<th>ADDRESS1</th>
			<th>ADDRESS2</th>
			<th>CITY</th>
			<th>STATE</th>
			<th>ZIP</th>
			<th>COUNTRY</th>
			<th>EMAIL</th>
		</tr>
						<?php 
							foreach ($registrants as $registrant): 
							$notPrimary = false;
							if ($registrant->reg_slot != 1) $notPrimary = true;
							$result = $this->getPrimaryRegistrant ($registrant->registration_event_id);
							$primary = $result[0]; ?>
						<tr>
			<td><?php echo $registrant->first_name . ' ' . $registrant->last_name; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primary->first_name . ' ' . $primary->last_name . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->address1; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primary->first_name . ' ' . $primary->last_name . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->address2; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primary->first_name . ' ' . $primary->last_name . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->city; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primary->first_name . ' ' . $primary->last_name . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->state; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primary->first_name . ' ' . $primary->last_name . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->zip; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primary->first_name . ' ' . $primary->last_name . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->country;?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primary->first_name . ' ' . $primary->last_name . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->email; ?></td>
		</tr>
						<?php endforeach; ?>
					</tbody>
</table>

<?php else: ?>
<p>Bad form submission</p>

<?php endif; ?>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<?php 
			elseif (isset($_POST['view-names-for-rentals'])):
				$rentals = $this->getRentals();
				if (isset($rentals) && !empty($rentals)): ?>
<h3>Registrants requesting rentals</h3>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<p class="export-link">
	<a href="#" class="generate-names-for-rentals">Export to spreadsheet</a>
</p>
<table>
	<tbody>
		<tr>
			<th>NAME</th>
			<th>ADDRESS1</th>
			<th>ADDRESS2</th>
			<th>CITY</th>
			<th>STATE</th>
			<th>ZIP</th>
			<th>COUNTRY</th>
			<th>EMAIL</th>
			<th>INSTRUMENT</th>
		</tr>
						<?php 
						foreach($rentals as $rentalRow):
							$res = $this->getRegistrantByRegIdAndSlot ($rentalRow->registration_event_id, $rentalRow->reg_slot);
							if(isset($res) && !empty($res)):
								$registrant = $res[0];
								$notPrimary = false;
								if ($rentalRow->reg_slot != 1) $notPrimary = true;
								$cl = get_post($rentalRow->class_id);
								$result = $this->getPrimaryRegistrant ($rentalRow->registration_event_id);
								$primary = $result[0];
								$primaryName = $primary->first_name . ' ' . $primary->last_name;
								?>
								<tr>
			<td><?php echo $registrant->first_name . ' ' . $registrant->last_name; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primaryName . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->address1; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primaryName . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->address2; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $$primaryName . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->city; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primaryName . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->state; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primaryName . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->zip; ?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primaryName . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->country;?></td>
			<td><?php if ($notPrimary) echo '(company of ' . $primaryName . ', reg #' . $primary->registrant_id . ')'; else echo $registrant->email; ?></td>
			<td><?php echo $cl->post_title; ?></td>
		</tr>
							<?php 
							else:
								continue;
							endif;
						endforeach;
						?>
					</tbody>
</table>
<?php 
				else: ?>
<p>Bad form submission.</p>
<?php 
				endif;
			?>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<?php
			elseif (isset($_POST['view-names-and-emails'])):
				$registrants = $this->getAllRegistrantsClean ();
			
				if (isset($registrants) && !empty($registrants)): ?>
<p>Names and Email Addresses</p>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<p class="export-link">
	<a href="#" class="generate-names-and-emails">Export to spreadsheet</a>
</p>
<table>
	<tbody>
		<tr>
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
		</tr>
						<?php 
							foreach ($registrants as $registrant): ?>
						<tr>
			<td><?php echo $registrant->first_name; ?></td>
			<td><?php echo $registrant->last_name; ?></td>
			<td><?php echo $registrant->address1; ?></td>
			<td><?php echo $registrant->address2; ?></td>
			<td><?php echo $registrant->city; ?></td>
			<td><?php echo $registrant->state; ?></td>
			<td><?php echo $registrant->zip; ?></td>
			<td><?php echo $registrant->country;?></td>
			<td><?php echo $registrant->phone; ?></td>
			<td><?php if ($registrant->email) echo $registrant->email; else echo '(company of above)'; ?></td>
		</tr>
						<?php endforeach; ?>
					</tbody>
            </table>

            <?php
                            else: ?>
            <p>Bad form submission.</p>
            <?php
                            endif; ?>
            <p>
                <a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
                    Seminar Admin Area</a>
            </p>
<?php 
			
			elseif (isset ($_POST['view-registration-by-id'])): 
				if (isset ($_POST['txt-registration-by-id']) && trim ($_POST['txt-registration-by-id']) != ''): 
					$rows = $this->getRegistrant(trim ($_POST['txt-registration-by-id']));
			
					if (!empty($rows)): 
						$slot = $rows[0]->reg_slot;
						if ($slot):
							$classes = $this->getClassesPerId($rows[0]->registration_event_id, intval($slot));
						endif;
						?>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<p>Information about an individual registrant.</p>
<h3>Color coding</h3>
<p>
	<span style="color: #fff; background: #cc0000;">Red: Cancelled</span> |
	<span style="color: #fff; background: blue;">Blue: Not confirmed/Email
		not sent</span>
</p>

<div
	style="overflow: hidden; overflow-x: scroll; width: 100%; max-height: 500px;">
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
				<th>GALA</th>
				<th>MEAL OPTION</th>
				<th>AGE GROUP</th>
				<th>EEFC MEMBER</th>
				<th>PAYMENT OPTION</th>
				<th>TRANSPORTATION</th>
				<th>DVD</th>
				<th>DVD FORMAT</th>
				<th>BALANCE</th>
                <th>EMAIL SENT</th>
				<th>CANCELLED</th>
				<th>CONFIRMED</th>
			</tr>
								<?php foreach ($rows as $row): ?>
								<tr
				class="<?php if ($row->cancel == 1) echo 'red'; else if ($row->confirmed == 0) echo 'blue'; ?>">
				<td><?php echo $row->registrant_id ; ?></td>
				<td><?php echo $row->registration_date ; ?></td>
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
				<td><?php echo $row->gala ; ?></td>
				<td><?php echo $row->meal_option ; ?></td>
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
                                    <?php elseif ($row->transport == 1):  ?>
                                    <td><?php echo "Plovdiv to Koprivshtitsa"; ?></td>
                                    <?php elseif ($row->transport == 2):  ?>
                                    <td><?php echo "Koprivshtitsa to Sofia"; ?></td>
                                    <?php elseif ($row->transport == 3):  ?>
                                    <td><?php echo "Plovdiv to Koprivshtitsa and Koprivshtitsa to Sofia"; ?></td>
                                    <?php else: ?>
                                    <td><?php echo "N/A"; ?></td>
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
                        <?php if ($row->registration_email_sent): ?>
                            <td><?php echo $row->registration_email_sent_timestamp; ?></td>
                        <?php else: ?>
                            <td>No</td>
                        <?php endif; ?>
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

<?php if(isset($classes) && !empty($classes)): ?>
<p>CLASSES</p>
<table>
	<tr>
		<th>CLASS</th>
		<th>RENT</th>
		<th>LEVEL</th>
	</tr>
						<?php foreach ($classes as $classes_row): 
							$cl = get_post($classes_row->class_id);
						?>
						<tr>
		<td><?php if($cl) echo $cl->post_title; else echo '(class id: ' . $classes_row->class_id . ')'; ?></td>
		<td><?php if ($classes_row->rent && $classes_row->rent > 0) echo 'renting'; else echo 'bringing'; ?></td>
		<td><?php if ($classes_row->level) echo $classes_row->level; else echo ''; ?></td>
	</tr>
						<?php endforeach; ?>
					</table>
<?php endif; ?>
					
					<?php endif;  
				else: ?>
<p>ERROR! No registration ID entered. Please click the back button and
	enter an ID.</p>
<?php endif; ?>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>

<?php 
			elseif (isset ($_POST ['get-num-per-class'])): 
			$rows = $this->getNumberPerClass (); 
			if (!empty ($rows)):?>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<p>Number of registrants per class.</p>
<p class="export-link">
	<a href="#" class="generate-number-per-class">Export to spreadsheet</a>
</p>
<div style="overflow: scroll; width: 100%; max-height: 500px;">
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
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>

<?php 
			elseif (isset ($_POST ['get-levels-per-class'])): 
			$rows = $this->getLevelsPerClass (); 
			if (!empty ($rows)): ?>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<p>Number of registrants in each class level.</p>
<p class="export-link">
	<a href="#" class="generate-levels-per-class">Export to spreadsheet</a>
</p>
<div style="overflow: scroll; width: 100%; max-height: 500px;">
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

<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>

<?php 
			
			elseif (isset ($_POST ['get-rentals-per-class'])): 
			$rows = $this->getRentalsPerClass (); 
			if (!empty ($rows)): ?>
<p>Number of rentals requested per class</p>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<p class="export-link">
	<a href="#" class="generate-rentals-per-class">Export to spreadsheet</a>
</p>
<div style="overflow: scroll; width: 100%; max-height: 500px;">
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
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>

<?php 
			elseif (isset ($_POST ['get-dvd'])): ?>
			<?php $results = $this->getDVDCounts (); ?>
<h3>DVD Orders</h3>
<p>PAL: <?php echo $results ['pal']; ?><br />
			NTSC: <?php echo $results ['ntsc']; ?></p>

<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>

<?php 
			elseif (isset ($_POST ['get-transport'])): ?>
<h3>Transportation Requested</h3>
<?php $results = $this->getTransportationRequests(); ?>
<p>Plovdiv to Koprivshtitsa: <?php echo $results['p_to_k']; ?><br />
Koprivshtitsa to Sofia: <?php echo $results['k_to_s']; ?><br />
			Plovdiv to Koprivshtitsa to Sofia: <?php echo $results['p_to_k_to_s']; ?></p>
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>

<?php 
			elseif (isset ($_POST ['get-all'])): 
			$rows = $this->getAllRegistrants();
			if (!empty ($rows)): ?>

<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<p>All registrants to date. This does not include information about each
	person's selection of classes. "Export to CSV" will give you a CSV file
	of all confirmed registrants (ignoring cancelled and unconfirmed
	registrations).</p>
<p class="export-link">
	<a href="#" class="generate-all-registrants">Export to spreadsheet</a>
</p>
<h3>Color coding</h3>
<p>
	<span style="color: #fff; background: #cc0000;">Red: Cancelled</span> |
	<span style="color: #fff; background: blue;">Blue: Not confirmed/Email
		not sent</span>
</p>

<div style="overflow: scroll; width: 100%; max-height: 500px;">
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
				<th>GALA</th>
				<th>MEAL OPTION</th>
				<th>AGE GROUP</th>
				<th>EEFC MEMBER</th>
				<th>PAYMENT OPTION</th>
				<th>TRANSPORTATION</th>
				<th>DVD</th>
				<th>DVD FORMAT</th>
				<th>BALANCE</th>
                <th>EMAIL SENT</th>
				<th>CANCELLED</th>
				<th>CONFIRMED</th>
			</tr>
					<?php foreach ($rows as $row): ?>
					<tr
				class="<?php if ($row->cancel == 1) echo 'red'; else if ($row->confirmed == 0) echo 'blue'; ?>">
				<td><?php echo $row->registrant_id ; ?></td>
				<td><?php echo $row->registration_date ; ?></td>
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
				<td><?php echo $row->gala ; ?></td>
				<td><?php echo $row->meal_option ; ?></td>
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
						<?php elseif ($row->transport == 1):  ?>
                        <td><?php echo "Plovdiv to Koprivshtitsa"; ?></td>
                        <?php elseif ($row->transport == 2):  ?>
                        <td><?php echo "Koprivshtitsa to Sofia"; ?></td>
						<?php elseif ($row->transport == 3):  ?>
						<td><?php echo "Plovdiv to Koprivshtitsa and Koprivshtitsa to Sofia"; ?></td>
						<?php else: ?>
						<td><?php echo "N/A"; ?></td>
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
                        <?php if ($row->registration_email_sent): ?>
                            <td><?php echo $row->registration_email_sent_timestamp; ?></td>
                        <?php else: ?>
                            <td>No</td>
                        <?php endif; ?>
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
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>

<?php
			elseif (isset ($_POST ['get-onsite-payment'])): 
			$rows = $this->getOnsite();
			if (!empty ($rows)): ?>

<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p>
<p>Registrants who selected On-site payment option.</p>
<p class="export-link">
	<a href="#" class="generate-onsite">Export to spreadsheet</a>
</p>
<div style="overflow: scroll; width: 100%; max-height: 500px;">
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
				<th>GALA</th>
				<th>MEAL OPTION</th>
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
					<tr
				class="<?php if ($row->cancel == 1) echo 'red'; else if ($row->confirmed == 0) echo 'blue'; ?>">
				<td><?php echo $row->registrant_id ; ?></td>
				<td><?php echo $row->registration_date ; ?></td>
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
				<td><?php echo $row->gala ; ?></td>
				<td><?php echo $row->meal_option ; ?></td>
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
                        <?php elseif ($row->transport == 1):  ?>
                        <td><?php echo "Plovdiv to Koprivshtitsa"; ?></td>
                        <?php elseif ($row->transport == 2):  ?>
                        <td><?php echo "Koprivshtitsa to Sofia"; ?></td>
                        <?php elseif ($row->transport == 3):  ?>
                        <td><?php echo "Plovdiv to Koprivshtitsa and Koprivshtitsa to Sofia"; ?></td>
                        <?php else: ?>
                        <td><?php echo "N/A"; ?></td>
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
<p>
	<a href="/wp-admin/tools.php?page=Seminar_Registration_Admin">Back to
		Seminar Admin Area</a>
</p><?php 
			
			else: 
		?>
<style type="text/css">
#import form, #import div.white {
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

	<p class="text">
		Please use the forms below to query the database.<br />Results are
		generated for one form at a time.
	</p>

	<div class="white">
					<?php $totalRegistrants = $this->get_total(); ?>
					<p>
			There are total of <strong><?php echo $totalRegistrants; ?></strong> confirmed registrants for Seminar <?php echo date ('Y', strtotime (get_field('seminar_start_date', 'option'))); ?>.</p>
					<?php if($totalRegistrants): ?>
					<form name="form-view-rentals-per-class" method="post">
			<input type="submit" name="get-all" value="Get All Registrants" />
		</form>
					<?php endif; ?>
				</div>
				
				<?php if ($totalRegistrants): ?>
				<!-- Registrant levels by class -->
	<form name="form-view-levels-per-class" method="post">

		<input type="submit" name="get-levels-per-class"
			value="Get Registrant Levels per Class" />
	</form>

	<!-- Registrant rentals by class -->
	<form name="form-view-rentals-per-class" method="post">

		<input type="submit" name="get-rentals-per-class"
			value="Get Rent/Borrow per Class" />
	</form>

	<!-- Registrants by class -->
	<form name="form-view-numbers-per-class" method="post">

		<input type="submit" name="get-num-per-class"
			value="Get Number of Registrants per Class" />
	</form>

	<!-- View registration -->
	<form name="form-view-registration-by-id" method="post">
		<h3>Registration information by id</h3>
		<p class="text">Enter a registration number and click Get Info to get
			all the information in the database for this registration.</p>
		<label for="txt-registration-by-id">Registration ID *</label> <input
			type="text" name="txt-registration-by-id" id="txt-registration-by-id" />
		<input type="submit" name="view-registration-by-id" value="Get Info" />
	</form>

	<!-- Names and emails -->
	<form name="form-view-names-and-emails" method="post">
		<h3>Names and Email addresses</h3>
		<input type="submit" name="view-names-and-emails" value="Get Info" />
	</form>

	<!-- Names and addresses of DVD orders -->
	<form name="form-view-media-orders-names-and-addresses" method="post">
		<h3>Registrants with DVD orders</h3>
		<input type="submit" name="view-media-orders-names-and-addresses"
			value="Get Info" />
	</form>

	<!-- Names of registrants who have requested a rental -->
	<form name="form-view-names-for-rentals" method="post">
		<h3>Registrants with Rental orders</h3>
		<input type="submit" name="view-names-for-rentals" value="Get Info" />
	</form>
				
				<?php 
				if (get_field ('show_dvd_available_field', $this->registration_page->ID)):
				?>
				<!-- Registrants with DVD orders -->
	<form name="form-dvd" method="post">
		<input type="submit" name="get-dvd" value="Get number of DVD orders" />
	</form>
				
				<?php endif; ?>
				
				<?php 
				if (get_field ('show_koprivshtitsa_transportation_field', $this->registration_page->ID)):
				?>
				<!-- Registrants with transportation orders -->
	<form name="form-transport" method="post">

		<input type="submit" name="get-transport"
			value="Get Transportation orders" />
	</form>
				
				<?php endif; ?>
				
				<!-- On-site payments -->
	<form name="form-onsite-payment" method="post">
		<input type="submit" name="get-onsite-payment"
			value="Get Registrants with On-site payment option" />
	</form>

    <!-- Cancel registration by registration ID -->
    <hr />
    <div style="background-color: #ecb7b7; padding: 20px; border-radius: 5px; border: 1px solid #d85050; margin-bottom: 20px;">
        <form name="form-cancel-registration-by-id" method="post">
            <h3>Cancel Registration by Registration ID</h3>
            <p class="text">Enter a registration number and click View Registration. If information is correct, you can proceed to cancellation.</p>
            <label for="txt-cancel-registration-by-id">Registration ID *</label> <input
                    type="text" name="txt-cancel-registration-by-id" id="txt-cancel-registration-by-id" />
            <input type="submit" name="input-view-cancel-registration-by-id" value="View Registration" />
        </form>
        <!-- Cancel Registration by ID code -->
        <?php
        // View registrations for a given registration ID (lookup registration_event_id then show all rows)
        if ( isset( $_POST['input-view-cancel-registration-by-id'] ) ) {
            if ( ! current_user_can( 'edit_users' ) ) {
                echo '<p>Insufficient permissions.</p>';
            } else {
                $input_id = intval( $_POST['txt-cancel-registration-by-id'] ?? 0 );
                if ( $input_id <= 0 ) {
                    echo '<p>ERROR! No registration ID entered. Please enter a numeric ID.</p>';
                } else {
                    global $wpdb;
                    $table = $this->table_registrants;

                    // get registration_event_id for the specific row
                    $row = $wpdb->get_row( $wpdb->prepare(
                            "SELECT registration_event_id FROM {$table} WHERE registrant_id = %d",
                            $input_id
                    ) );

                    if ( ! $row || empty( $row->registration_event_id ) ) {
                        echo '<p>No registrations found for that ID.</p>';
                    } else {
                        $registrantion_event_id = $row->registration_event_id;
                        $rows = $wpdb->get_results( $wpdb->prepare(
                                "SELECT * FROM {$table} WHERE registrantion_event_id = %s ORDER BY registrant_id ASC",
                                $registrantion_event_id
                        ) );

                        if ( empty( $rows ) ) {
                            echo '<p>No rows found for registration ID: ' . esc_html( $input_id ) . '</p>';
                        } else {
                            echo '<p>Showing registration group for registration ID: ' . esc_html( $input_id ) . '</p>';
                            echo '<div style="overflow: auto; max-height: 300px;"><table><thead><tr>';
                            echo '<th>ID</th><th>NAME</th><th>EMAIL</th><th>CONFIRMED</th><th>CANCELLED</th>';
                            echo '</tr></thead><tbody>';
                            foreach ( $rows as $r ) {
                                $class = ( $r->cancel == 1 ) ? 'red' : ( $r->confirmed != 1 ? 'blue' : '' );
                                echo '<tr class="' . esc_attr( $class ) . '">';
                                echo '<td>' . esc_html( $r->registrant_id ) . '</td>';
                                echo '<td>' . esc_html( $r->first_name ) . ' ' . esc_html( $r->last_name ) . '</td>';
                                echo '<td>' . esc_html( $r->email ) . '</td>';
                                echo '<td>' . ( $r->confirmed ? 'Yes' : 'No' ) . '</td>';
                                echo '<td>' . ( $r->cancel ? 'Yes' : 'No' ) . '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>
                    <p>
                        <span style="color: #fff; background: #cc0000;">Red: Cancelled</span> |
                        <span style="color: #fff; background: blue;">Blue: Not confirmed/Email
                            not sent</span>
                    </p></div>';

                            // confirmation form (no nonce)
                            echo '<form method="post" style="margin-top:1em;">';
                            echo '<input type="hidden" name="cancel_registration_event_id" value="' . esc_attr( $registrantion_event_id ) . '" />';
                            echo '<input type="hidden" name="cancel_input_id" value="' . esc_attr( $input_id ) . '" />';
                            echo '<input type="submit" name="confirm-cancel-registration" value="Cancel Registration (mark all rows cancelled)" onclick="return confirm(\'Are you sure you want to cancel this registration group?\');" />';
                            echo '</form>';
                        }
                    }
                }
            }
        }

        // Confirmation handler: perform the UPDATE
        if ( isset( $_POST['confirm-cancel-registration'] ) ) {
            if ( ! current_user_can( 'edit_users' ) ) {
                echo '<p>Insufficient permissions.</p>';
            } else {
                $registrantion_event_id = sanitize_text_field( $_POST['cancel_registration_event_id'] ?? '' );
                $input_id = sanitize_text_field( $_POST['cancel_input_id'] ?? '' );
                if ( $registrantion_event_id === '' ) {
                    echo '<p>Missing registration group id.</p>';
                } else {
                    global $wpdb;
                    $table = $this->table_registrants;
                    $updated = $wpdb->query( $wpdb->prepare(
                            "UPDATE {$table} SET cancel = 1 WHERE registration_event_id = %s",
                            $registrantion_event_id
                    ) );

                    if ( $updated === false ) {
                        echo '<p>Database update failed.</p>';
                    } else {
                        echo '<p>Marked ' . intval( $updated ) . ' row(s) cancelled for registration ID: ' . esc_html( $input_id ) . '.</p>';
                    }
                }
            }
        }
        ?>
    </div>
    <!-- END Cancel Registration by ID code -->


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
			
			$sql = "SELECT registrant_id
					FROM $this->table_registrants
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

            $p_to_k_count = 0;
			$sql = "SELECT count(transport) p_to_k
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND transport = 1
					AND cancel <> 1
					AND confirmed = 1";
				
			$results = $wpdb->get_results ($sql);
			if (!empty ($results)) $p_to_k_count = $results[0]->p_to_k;

			$k_to_s_count = 0;
			$sql = "SELECT count(transport) k_to_s
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND transport = 2
					AND cancel <> 1
					AND confirmed = 1";

            $results = $wpdb->get_results ($sql);
            if (!empty ($results)) $k_to_s_count = $results[0]->k_to_s;

		    $p_to_k_to_s_count = 0;
            $sql = "SELECT count(transport) p_to_k_to_s
                    FROM $table
                    WHERE reg_year = " . intval ($this->reg_year) . "
                    AND transport = 3
                    AND cancel <> 1
                    AND confirmed = 1";
			$results = $wpdb->get_results ($sql);
			if (!empty ($results)) $p_to_k_to_s_count = $results[0]->p_to_k_to_s;
				
			return array ('p_to_k'=>$p_to_k_count,
			'k_to_s'=>$k_to_s_count,
			'p_to_k_to_s'=>$p_to_k_to_s_count);

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
					ORDER BY registrant_id ASC";
			
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
					ORDER BY registrant_id ASC";
		
			$results = $wpdb->get_results ($sql);
						
					return $results;
		}
		
		private function getRegistrant ($registrant_id) {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
		
			$sql = "SELECT *
					FROM $table
					WHERE reg_year = " . intval ($this->reg_year) . "
					AND registrant_id = $registrant_id";
		
			$results = $wpdb->get_results ($sql);

			return $results;
		}
		
		private function getClassesPerId ($registration_event_id, $reg_slot) {
			global $wpdb;

			$table = 'wp_Seminar_classes';
			
			$sql = "SELECT *
			FROM $table
			WHERE registration_event_id = '" . $registration_event_id . "' AND reg_slot = " . $reg_slot . "
			ORDER BY reg_slot ASC";
			
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
					ORDER BY registrant_id ASC";

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
					ON (T1.registration_event_id = T2.registration_event_id AND T1.reg_slot = T2.reg_slot)
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
		
		private function getRentals () {
			global $wpdb;
			$table_classes = 'wp_Seminar_classes';
			
			$sql = "Select * from " . $table_classes . "
					WHERE rent = 1";
			
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
					ON (T2.registration_event_id = T3.registration_event_id AND T2.reg_slot = T3.reg_slot)
					WHERE T1.post_status = 'publish'
					AND T1.post_type = 'classes'
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
					ON (T1.registration_event_id = T2.registration_event_id AND T1.reg_slot = T2.reg_slot)
					WHERE T3.post_type= 'classes'
					AND T3.post_status = 'publish'
					AND T2.cancel = 0
					AND T2.confirmed = 1
					AND T2.reg_year = " . intval ($this->reg_year) . "
					GROUP BY T1.class_id";

			$results = $wpdb->get_results ($sql);

			return $results;
		}
		
		private function getRegistrantsWithMediaOrders () {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
				
			$sql = "Select registrant_id, registration_event_id, reg_slot, first_name, last_name, address1, address2, city, state, zip, country, email, dvd_format from " . $table ."
					WHERE dvd > 0
					AND cancel = 0
					AND confirmed = 1
					AND reg_year = " . intval ($this->reg_year);
			
			$results = $wpdb->get_results ($sql);
			
			return $results;
		}
		
		private function getPrimaryRegistrant ($registration_event_id) {

			global $wpdb;
			$table = 'wp_Seminar_registrations';
			
			$sql = "Select * from " . $table . "
					WHERE registration_event_id = '" . $registration_event_id . "'
					AND reg_slot = 1
					AND reg_year = " . intval ($this->reg_year);
				
			$results = $wpdb->get_results ($sql);
			
			return $results;
	
		}
		
		private function getRegistrantByRegIdAndSlot ($registration_event_id, $slot) {
			global $wpdb;
			$table = 'wp_Seminar_registrations';
				
			$sql = "Select * from " . $table . "
					WHERE registration_event_id = '" . $registration_event_id . "'
					AND cancel <> 1
					AND confirmed = 1
					AND reg_slot = '" . $slot . "'
					AND reg_year = " . intval ($this->reg_year);
			
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

		        $('body').on('click', '.generate-media-orders-names-and-addresses', function ( e ) {
			          e.preventDefault();

			          generateDVDNamesAndAddresses ();
			          
			          return false;
			   });

		        $('body').on('click', '.generate-names-for-rentals', function ( e ) {
			          e.preventDefault();

			          generateNamesForRentals ();
			          
			          return false;
			   });

		        $('body').on('click', '.generate-names-and-emails', function ( e ) {
			          e.preventDefault();

			          generateNamesAndEmails ();
			          
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
				function generateDVDNamesAndAddresses () {
					window.open(ajaxurl + '?' + jQuery.param({ action: 'dvd_names_and_addresses_csv' }));
				}
				function generateNamesForRentals () {
					window.open(ajaxurl + '?' + jQuery.param({ action: 'names_for_rentals_csv' }));
				}
				function generateNamesAndEmails () {
					window.open(ajaxurl + '?' + jQuery.param({ action: 'names_and_emails_csv' }));
				}
		    </script>
<?php
		}
		
		public function admin_ajax_names_and_emails_csv () {
			//helper vars
			$first = true; // on first iteration of results, create the first row of column names
			$count = 0; // keep count of current row
			$last_index = 0; // last index of 'good' columns, used to filter out columns that come after custom questions.
			$white_list = array ('registrant_id', 'registration_event_id', 'reg_slot', 'first_name', 'last_name', 'address1', 'address2', 'city', 'state', 'zip', 'country', 'phone', 'email');
			$csv = '';
			$rows = $this->getAllRegistrantsClean();
			
			$lines = array ();
			
			// first add the headers
			foreach($white_list as $ind=>$header) {
				if ($ind == 0) {
					$lines[0][0] = "'" . strtoupper ($header);
				}
				else {
					$key = strtoupper ($header);
					$lines[0][] .= $key;
				}
				$last_index++;
			}
			
			// now do the actual values
			foreach ( $rows as $row_index=>$row ) {
				$input_count = 0;
				foreach ( $row as $index=>$value ) {
					if (in_array($index, $white_list)) {
						if( $input_count <= $last_index ){
								
							$lines[ ($row_index + 1) ][] = $this->cleanData ($value);
								
						}
						$input_count++;
					}
				}
					
			}
			
			$csv = '';
			foreach ( $lines as $line ) {
				$csv .= $this->str_putcsv($line);
			}
			
			// Stream file
			header('Content-Type: text/csv');
			//header("Content-Type: application/vnd.ms-excel");
			header('Content-Disposition: attachment;filename="dvd_names_and_addresses-' . date('Y-m-d') . '.csv"');
			echo $csv;
			die();
		}
		
		public function admin_ajax_names_for_rentals_csv () {
			//helper vars
			$first = true; // on first iteration of results, create the first row of column names
			$count = 0; // keep count of current row
			$last_index = 0; // last index of 'good' columns, used to filter out columns that come after custom questions.
			
			$csv = '';
			$rows = $this->getRentals();
			$white_list = array ('registrant_id', 'registration_event_id', 'reg_slot', 'first_name', 'last_name', 'address1', 'address2', 'city', 'state', 'zip', 'country', 'email', 'instrument');
			$lines = array ();
			
			// first add the headers
			foreach($white_list as $ind=>$header) {
				if ($ind == 0) {
					$lines[0][0] = "'" . strtoupper ($header);
				}
				else {
					$key = strtoupper ($header);
					$lines[0][] .= $key;
				}
				$last_index++;
			}

			// now find registrant
			foreach ( $rows as $row_index=>$row ) {
				$input_count = 0;
				$res = $this->getRegistrantByRegIdAndSlot ($row->registration_event_id, $row->reg_slot);
				if (isset($res) && !empty($res)) {
					$cl = get_post($row->class_id);
					
					if (!isset($res[0]->instrument)) {
						$res[0]->instrument = $cl->post_title;
					}
					
					// now do the actual values
					foreach ( $res[0] as $data_index=>$data) {
						if (in_array($data_index, $white_list)) {
					
							if( $input_count <= $last_index ){
					
								$lines[ ($row_index + 1) ][] = $this->cleanData ($data);
					
							}
							$input_count++;
						}
					}
				}				
			}
			
			
			$csv = '';
			foreach ( $lines as $line ) {
				$csv .= $this->str_putcsv($line);
			}
			
			// Stream file
			header('Content-Type: text/csv');
			//header("Content-Type: application/vnd.ms-excel");
			header('Content-Disposition: attachment;filename="dvd_names_and_addresses-' . date('Y-m-d') . '.csv');
			echo $csv;
			die();
		}
		
		public function admin_ajax_dvd_names_and_addresses_csv () {
			//helper vars
			$first = true; // on first iteration of results, create the first row of column names
			$count = 0; // keep count of current row
			$last_index = 0; // last index of 'good' columns, used to filter out columns that come after custom questions.
				
			$csv = '';
			$rows = $this->getRegistrantsWithMediaOrders();
		
			$lines = array ();

			// first add the headers
			foreach ( $rows[0] as $index=>$value ) {
				if ($index == 'registrant_id') {
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
			header('Content-Disposition: attachment;filename="dvd_names_and_addresses-' . date('Y-m-d') . '.csv');
			echo $csv;
			die();
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
			    if ($index == 'flute') {    // does not pertain for 2025
                    continue;
                }
				if ($index == 'registrant_id') {
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
				    if ($index == 'flute') {    // does not pertain for 2025
                        continue;
                    }
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
                if ($index == 'flute') {    // does not pertain for 2025
                    continue;
                }
				if ($index == 'registrant_id') {
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
				    if ($index == 'flute') {    // does not per tain for 2025
                        continue;
                    }
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
	add_action('wp_ajax_dvd_names_and_addresses_csv', array(&$seminar_registration_admin, 'admin_ajax_dvd_names_and_addresses_csv'));
	add_action('wp_ajax_names_for_rentals_csv', array(&$seminar_registration_admin, 'admin_ajax_names_for_rentals_csv'));
	add_action('wp_ajax_names_and_emails_csv', array(&$seminar_registration_admin, 'admin_ajax_names_and_emails_csv'));
	add_action('admin_footer', array(&$seminar_registration_admin, 'admin_javascript'));
}

?>
