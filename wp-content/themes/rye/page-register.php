<?php // check if registration is closed ?>
<?php

$today = date ( 'Y-m-d' );

$banktransfer_closed_date = date ( 'Y-m-d', strtotime ( get_field ( 'registration_closed_date', 'option' ) ) );
$ok_banktransfer = $today <= $banktransfer_closed_date;

$reg_closed_date = date ( 'Y-m-d', strtotime ( get_field ( 'seminar_end_date', 'option' ) ) - 2880 ); // 2 days before
$ok_to_register = $today <= $reg_closed_date;

//$ok_to_register = $_GET ["dev"] == 'dev';

if (! $ok_to_register) : // registration is closed	?>
<?php get_header(); ?>
<?php if ( have_posts() ) : ?>

   <?php while (have_posts()) : the_post(); ?>
<h2>
	<span><?php the_title(); ?></span>
</h2>
<div class="c12 row">
      <?php echo get_red_note(); ?>
      <h3><?php the_content(); ?></h3>
</div>
<?php
		
endwhile
		;
	
endif;
	?>
<?php get_footer(); ?>

<?php else: // registration is open ?>


<?php get_header(); ?>
<?php if ( have_posts() ) : ?>

   <?php while (have_posts()) : the_post(); ?>
<h2>
	<span><?php the_title(); ?></span>
</h2>
<div class="c12 row register">
      <?php echo get_red_note(); ?>


      <?php
			
if (isset ( $_POST ['go_submit'] ) && $_POST ['go_submit'] != '') :
				
				// get all the POST values
				if (isset ( $_POST ['txt-ime'] ) && trim ( $_POST ['txt-ime'] ) != '')
					$first_name = trim ( ( $_POST ['txt-ime'] ) );
				
				if (isset ( $_POST ['txt-ime'] ) && trim ( $_POST ['txt-familia'] ) != '')
					$last_name = trim ( ( $_POST ['txt-familia'] ) );
				
				if (isset ( $_POST ['txt-adres'] ) && trim ( $_POST ['txt-adres'] ) != '')
					$address1 = trim ( ( $_POST ['txt-adres'] ) );
				else
					$address1 = NULL;
				
				if (isset ( $_POST ['txt-adres2'] ) && trim ( $_POST ['txt-adres2'] ) != '')
					$address2 = trim ( ( $_POST ['txt-adres2'] ) );
				else
					$address2 = NULL;
				
				if (isset ( $_POST ['txt-grad'] ) && trim ( $_POST ['txt-grad'] ) != '')
					$city = trim ( ( $_POST ['txt-grad'] ) );
				else
					$city = NULL;
				
				if (isset ( $_POST ['txt-state'] ) && trim ( $_POST ['txt-state'] ) != '')
					$state = trim ( ( $_POST ['txt-state'] ) );
				else
					$state = NULL;
				
				if (isset ( $_POST ['txt-kod'] ) && trim ( $_POST ['txt-kod'] ) != '')
					$zip = trim ( ( $_POST ['txt-kod'] ) );
				else
					$zip = NULL;
				
				if (isset ( $_POST ['txt-strana'] ) && trim ( $_POST ['txt-strana'] ) != '')
					$country = trim ( ( $_POST ['txt-strana'] ) );
				else
					$country = NULL;
				
				if (isset ( $_POST ['txt-tel'] ) && trim ( $_POST ['txt-tel'] ) != '')
					$phone = trim ( ( $_POST ['txt-tel'] ) );
				else
					$phone = NULL;
				
				if (isset ( $_POST ['txt-poshta'] ) && trim ( $_POST ['txt-poshta'] ) != '') {
					$email = trim ( ( $_POST ['txt-poshta'] ) );
					$is_primary = 1;
				} else {
					$email = NULL;
					$is_primary = 0;
				}
				
				if (isset ( $_POST ['txt-speshnost'] ) && trim ( $_POST ['txt-speshnost'] ) != '') {
					$emergency = trim ( ( $_POST ['txt-speshnost'] ) );
					$is_primary = 1;
				} else {
					$emergency = NULL;
					$is_primary = 0;
				}
				
				$num_days = $_POST ['select-broi-dni'];
				
				$age = $_POST ['radio-age'];
				
				$eefc = ($_POST ['radio-eefc'] == 'no') ? 0 : 1;
				
				$dvd = ($_POST ['radio-dvd'] == 'no') ? 0 : 1;
				
				$fleita = ($_POST ['radio-fleita'] == 'no') ? 0 : 1;
				
				$gala = ($_POST ['radio-gala'] == 'No') ? 0 : 1;
				
				$gala_option = $_POST ['radio-gala-option'];
				
				if (isset ( $_POST ['radio-dvd-format'] ) && $_POST ['radio-dvd-format']) {
					$dvd_format = $_POST ['radio-dvd-format'];
				} else
					$dvd_format = NULL;
					// exit(var_dump($dvd_format));
				
				$transport = $_POST ['radio-transport'];
				if ($transport == 'one-way') {
					$transport = 1;
				} else if ($transport == 'round-trip') {
					$transport = 2;
				} else { // No
					$transport = 0;
				}
				
				if (isset ( $_POST ['radio-payment'] ))
					$payment = trim ( ( $_POST ['radio-payment'] ) );
				else {
					if (isset ( $_POST ['registration_id'] ) && trim ( $_POST ['registration_id'] ) != '') {
						$reg_id = $_POST ['registration_id'];
						$registration = getRegistrationRows ( $reg_id );
						$payment = $registration [0]->payment;
					} else { // just a default value so we can at least calculate balance
						$payment = 'transfer';
					}
				}
				
				date_default_timezone_set ( 'America/New_York' );
				$date = date ( 'Y-m-d H:i:s' );
				
				if (isset ( $_POST ['registration_id'] ) && trim ( $_POST ['registration_id'] ) != '') {
					$reg_id = $_POST ['registration_id'];
					$registration = getRegistrationRows ( $reg_id );
				} else
					$reg_id = $email . '_' . microtime ();
				
				$registration = getRegistrationRows ( $reg_id );
				
				$reg_year = date ( 'Y', strtotime ( get_start_date () ) );
				$reg_slot = count ( $registration ) + 1;
				
				// parse classes this person selected and determine rental fees where applicable
				$classes_selected = $_POST ['ckb_class']; // need this here for rental fees on classes and later for adding to table
				
				$rental_total = 0;
				foreach ( $classes_selected as $class_id ) :
					if ($_POST ['radio-rent-bring-' . $class_id] == 'rent' && in_array ( 'rent', get_field ( 'rent_bring', $class_id ) )) {
						$rental_fee = get_field ( 'rental_fee', $class_id );
						$rental_total += ($rental_fee * $num_days);
					}
				endforeach
				;
				
				// Add to database
				
				global $wpdb;
				$table = 'wp_Seminar_registrations';
				$table_classes = 'wp_Seminar_classes';
				
				// add to registration table
				$sql = "INSERT INTO $table
               SET reg_id = '$reg_id',
               is_primary = $is_primary,
               date = '$date',
               reg_year = $reg_year,
               reg_slot = $reg_slot,
               first_name = '$first_name',
               last_name = '$last_name',
               address1 = '$address1',
               address2 = '$address2',
               city = '$city',
               state = '$state',
               zip = '$zip',
               country = '$country',
               phone = '$phone',
               email = '$email',
               emergency = '$emergency',
               num_days = $num_days,
               gala = $gala,
               meal_option = '$gala_option',
               age = '$age',
               is_eefc = $eefc,
               payment = '$payment',
               transport = $transport,
               dvd = $dvd,
               dvd_format = '$dvd_format',
               flute = $fleita,
               cancel = 0,
               balance = " . (get_balance_individual ( $num_days, $gala, $age, $eefc, $payment, $dvd, $transport ) + $rental_total);
				
				$wpdb->query ( $sql );
				
				// parse classes this person selected and add to table
				foreach ( $classes_selected as $class_id ) :
					
					$rent_bring = $_POST ['radio-rent-bring-' . $class_id] == 'rent' ? 1 : 0;
					$level = $_POST ['radio-level-' . $class_id];
					
					// add to classes table
					$sql = "INSERT INTO $table_classes
                  SET reg_id = '$reg_id',
                  reg_slot = $reg_slot,
                  class_id = $class_id,
                  rent = $rent_bring,
                  level = '$level'";
					
					$wpdb->query ( $sql );
				endforeach
				;
				
				// get all rows after we've inserted new registrant
				// Display captured info
				
				$registration = getRegistrationRows ( $reg_id );
				$reg_number = intval ( $registration [0]->id );
				$num_people = count ( $registration );
				$case = $num_people > 1 ? 'people' : 'person';
				$payment_option = $registration [0]->payment == 'on-site' ? 'On site' : 'Bank Transfer (instructions will be emailed to you when you complete registration)';
				?>

      <p class="bigger red">NOTE: Clicking the Back button on your
		browser may result in a duplicate registration.</p>
	<h3>Summary</h3>

	<div class="registrant-wrapper">
         <?php echo $num_people; ?> <?php echo $case; ?> registered.<br />
         Balance: EURO <?php echo number_format (get_balance ($reg_id), 2); ?><br />
         Payment: <?php echo $payment_option; ?>
      </div>
	<p class="bigger">To complete your registration, please press "Confirm
		and Complete". After that, a confirmation email will be sent to you
		within 24 hours.</p>
	<p class="bigger">To add another registrant, please press "Add Another
		Person".</p>
		
	<div class="registration-details-container">
		<h3>Details for Registration #<?php echo $registration[0]->id; ?></h3>
		Email address: <?php echo $registration[0]->email; ?><br />
		Total: EURO <?php echo number_format (get_balance ($registration[0]->reg_id), 2); ?><br />
		Payment: <?php echo $payment_option; ?>	
		
		<?php $classes = getClassesRows ($registration[0]->reg_id); ?>
		
		<?php if (strrpos($payment_option, 'Bank Transfer') !== false): ?>
		
		<p>You have indicated that you will be paying by Bank Transfer. Please provide your bank with the following routing information for your Bank Transfer:</p>
		<table>
			<tbody>
				<tr>
					<td>Bank Name:</td>
					<td>UNICREDIT BULBANK</td>
				</tr>
				<tr>
					<td>Bank Address:</td>
					<td>31 Ivan Vazov Str., Plovdiv 4000, Bulgaria</td>
				</tr>
				<tr>
					<td>Bank SWIFT code:</td>
					<td>UNCRBGSF</td>
				</tr>
				<tr>
					<td>Account Name:</td>
					<td>Academy of Music Dance and Fine Arts Plovdiv</td>
				</tr>
				<tr>
					<td>Account Address:</td>
					<td>2 Todor Samodumov Str., Plovdiv 4000, Bulgaria</td>
				</tr>
				<tr>
					<td>Account Number (in EURO):</td>
					<td>3498641407</td>
				</tr>
				<tr>
					<td>IBAN Number (in EURO):</td>
					<td>BG56UNCR75273498641407</td>
				</tr>
			</tbody>
		</table>
		
		<p>Bank Transfers should be initiated before  <?php echo date ('F j, Y', strtotime(get_late_registration_deadline ())) ?> to qualify for the fee calculated above. When you have completed your Bank Transfer, please email us at <a mailto:contact@folkseminarplovdiv.net>contact@folkseminarplovdiv.net</a>  to indicate the date of your Bank Transfer.  This will assist Seminar administrative staff with tracking your payment.  If the Bank Transfer is originating from a bank account that is not in the registrant's name, or funds are being transferred for more than one registrant, please include the name of the person who owns the account, and all registrant names associated with your Bank Transfer.</p>
		
		<?php else: // payment onsite ?>
		<p>You have indicated that you will be paying on site.</p>
		<?php endif; ?>
		
		<?php foreach ($registration as $index => $registrant):?>
		<div class="registrant-container">
			<h3>Registrant <?php echo ($index + 1); ?></h3>
			<table>
				<tbody>
					<tr>
						<td>Name:</td>
						<td><?php echo $registrant->first_name; ?> <?php echo $registrant->last_name; ?></td>
					</tr>
					<?php if ($registrant->is_primary == 1): ?>
					<tr>
						<td>Address:</td>
						<td><?php echo $registrant->address1; ?>
							<?php if ($registrant->address2): ?>
							, <?php echo $registrant->address2; ?>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td>City:</td>
						<td><?php echo $registrant->city; ?>, <?php echo $registrant->state; ?>, <?php echo $registrant->zip; ?></td>
					</tr>
					<tr>
						<td>Country:</td>
						<td><?php echo $registrant->country; ?></td>
					</tr>
					<tr>
						<td>Phone:</td>
						<td><?php echo $registrant->phone; ?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?php echo $registrant->email; ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td>Gala:</td>
						<td><?php echo ($registrant->gala == '1' ? 'Yes' : 'No'); ?>
							<?php if ($registrant->gala == '1'):?>
							, <?php echo $registrant->meal_option; ?>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td>EEFC Member:</td>
						<td><?php echo ($registrant->is_eefc == '1' ? 'Yes' : 'No'); ?></td>
					</tr>
					<tr>
						<td>Days attending:</td>
						<td><?php echo $registrant->num_days; ?></td>
					</tr>
					<tr>
						<td>Registration type:</td>
						<td><?php echo $registrant->age; ?></td>
					</tr>
					<?php if (get_field ( 'show_koprivshtitsa_transportation_field' )) : ?>
					<tr>
						<td>Transportation To Koprivshtitsa:</td>
						<td><?php echo ($registrant->transport == '1' ? 'Yes' : 'No'); ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td>DVD:</td>
						<td><?php echo ($registrant->dvd == '1' ? 'Yes' : 'No'); if(isset($registrant->dvd_format) && $registrant->dvd_format != ''): echo ': ' . strtoupper($registrant->dvd_format); endif; ?></td>
					</tr>
					<tr>
						<td>Flute class:</td>
						<td><?php echo ($registrant->flute == '1' ? 'Yes' : 'No'); ?></td>
					</tr>
				</tbody>
			</table>
			
			<?php if (count($classes)): ?>
			<h3>Classes <?php echo $registrant->first_name; ?> <?php echo $registrant->last_name; ?> is attending:</h3>
			<?php foreach ($classes as $classKey=>$class_row): ?>
				<?php if ($class_row->reg_slot == ($index + 1)): ?>
					<?php 
					$class_id = $class_row->class_id;
					$levels = get_field ('class_levels', $class_id);
					$rent_options = get_field ('rent_bring', $class_id);
					
					$class_obj = get_post ($class_id);
					?>
					<p><?php echo $class_obj->post_title; ?>
					<?php 
					$classDets = '';
					if ($rent_options || $levels) {
						$classDets .= ': ';
					
						if ($rent_options) {
							$rent = NULL;
							if (isset ($class_row->rent) && $class_row->rent != NULL && $class_row->rent != '') {
								$rent = $class_row->rent == 1 ? 'would like to rent' : 'bringing my own';
							}
							if ($class_row->rent == 1) {
								$classDets .= $rent . ', daily fee of ' . get_field('rental_fee', $class_id) . ' EURO applies';
					
							}
							else {
								if ($rent) {
									$classDets .= $rent;
								}
							}
						}
						
						if ($levels) {
							$level = null;
							if (isset ($class_row->level) && $class_row->level != NULL && $class_row->level != '') {
								$level = $class_row->level;
							}
							if (isset ($level) && $level != '' && $level != null) {
								if ($rent_options && isset ($rent)) {
									$classDets .= ',';
								}
								$classDets .= ' ' . $level;
							}
						}
					}
					?>
					<?php echo $classDets; ?></p>
					<?php if (count($classes) > $classKey + 1): ?>
					<hr />
					<?php endif; ?>
					
				<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
		
		<?php endforeach; ?>
	</div>
	<form id="continue-form" method="post">

		<input type="hidden" name="registration_id"
			value="<?php echo $reg_id; ?>" />
		<div class="form-buttons">

			<div class="frm_submit">

				<p class="submit-button">
					<input type="submit" value="Confirm and Complete" name="go_confirm"
						id="go_confirm" />
				</p>

			</div>

			<div class="frm_submit">

				<p class="submit-button">
					<input type="submit" value="Add Another Person" name="go_add"
						id="go_add" />
				</p>

			</div>

			<div class="frm_submit cancel">

				<p class="submit-button">
					<input type="submit" value="Cancel" name="go_cancel" id="go_cancel" />
				</p>

			</div>


		</div>
	</form>

      <?php elseif (isset($_POST['go_confirm']) && $_POST['go_confirm'] != ''): ?>
      <input type="hidden" name="registration_id"
		value="<?php echo $reg_id; ?>" />

      <?php
				
$reg_id = $_POST ['registration_id'];
				$registration = getRegistrationRows ( $reg_id );
				confirmRegistration ( $reg_id );
				sendRegisterEmail ( $registration );
				?>

      <h3>Registration #<?php echo $registration[0]->id; ?> complete.</h3>
      <?php
				$num_people = count ( $registration );
				$case = $num_people > 1 ? 'people' : 'person';
				$payment_option = $registration [0]->payment == 'on-site' ? 'On site' : 'Bank Transfer (look for instructions in confirmation email)';
				?>
      <div class="registrant-wrapper">
         <?php echo $num_people; ?> <?php echo $case; ?> registered.<br />
         Balance: EURO <?php echo number_format (get_balance ($reg_id), 2); ?><br />
         Payment: <?php echo $payment_option; ?>
      </div>

	<p>
		We have sent a confirmation email to you. Please refer to that email
		for further details.<br /> 
		<span class="red">If you do not receive the confirmation email, please check your "spam" folder.  (We know this has been an issue with Gmail.)</span>
		Please <a
			href="mailto:contact@folkseminarplovdiv.net">contact us</a> if you
		did not receive the confirmation email.<br /> Thank you and we look
		forward to seeing you in Plovdiv!
	</p>
	<p>
		Larry Weiner & Dilyana Kurdova<br />International Program Coordinators
	</p>

      <?php elseif (isset($_POST['go_cancel']) && $_POST['go_cancel'] != ''): ?>
      <?php
				
$reg_id = $_POST ['registration_id'];
				$registration = getRegistrationRows ( $reg_id );
				cancelRegistration ( $reg_id );
				?>
      <p>Registration #<?php echo $registration[0]->id; ?> cancelled.</p>
      <?php
				// send email
				$email = $registration [0]->email;
				wp_mail ( $email, 'Seminar Registration #' . $registration [0]->id . ' cancelled', "Seminar Registration #" . $registration [0]->id . " cancelled" . "\r\n\r\n" . "Email Address: " . $email . "\r\n\r\n", 'From: ' . get_bloginfo ( 'name' ) . ' <' . get_bloginfo ( 'admin_email' ) . '>' . "\r\n" . 'Reply-To: ' . get_bloginfo ( 'admin_email' ) );
				
				?>


      <?php elseif (isset($_POST['go_add']) && $_POST['go_add'] != ''): ?>
      <?php $reg_id = $_POST ['registration_id']; ?>


      <p class="flt-l">* Indicates a required field.</p>

	<div class="feedback">Please correct errors on form.</div>

	<div class="form-wrapper">
		<form id="add-form" method="post">
			<div class="general-info">

				<input type="hidden" name="registration_id"
					value="<?php echo $reg_id; ?>" />
				<div class="input-row">
					<label for="txt-ime">* First Name: </label> <input name="txt-ime"
						id="txt-ime" type="text" class="required"
						<?php if (isset ($_POST['txt-ime']) && trim($_POST['txt-ime']) != '') echo ' value="' . $_POST['txt-ime'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-familia">* Last Name: </label> <input
						name="txt-familia" id="txt-familia" type="text" class="required"
						<?php if (isset ($_POST['txt-familia']) && trim($_POST['txt-familia']) != '') echo ' value="' . $_POST['txt-familia'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="select-broi-dni">* How many days you are planning to
						attend the Folk Seminar?</label> <select id="select-broi-dni"
						name="select-broi-dni" class="required">
						<option value=""
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == '') echo ' selected="selected"'; ?>>-Select-</option>
						<option value="1"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 1) echo ' selected="selected"'; ?>>1
							day</option>
						<option value="2"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 2) echo ' selected="selected"'; ?>>2
							days</option>
						<option value="3"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 3) echo ' selected="selected"'; ?>>3
							days</option>
						<option value="4"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 4) echo ' selected="selected"'; ?>>4
							days</option>
						<option value="5"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 5) echo ' selected="selected"'; ?>>5
							days</option>
						<option value="6"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 6) echo ' selected="selected"'; ?>>Entire
							Seminar</option>
					</select>
				</div>

				<div class="input-row">
					<label for="radio-gala">* Will you attend the Tuesday night gala
						dinner:</label>
					<div>
						<input name="radio-gala" value="Yes" type="radio" class="required"
							<?php if (isset ($_POST['radio-gala'])) echo ' checked="checked"'; ?> />
						Yes<br />
						<div class="gala-option indent">
							<input name="radio-gala-option" value="Vegetarian" type="radio"
								class="<?php if (isset ($_POST['radio-gala']) && $_POST['radio-gala'] == 'Yes') echo 'required'; ?>"
								<?php if (isset ($_POST['radio-gala-option'])  && $_POST['radio-gala-option'] == 'Vegetarian') echo ' checked="checked"'; ?> />
							Vegetarian<br /> <input name="radio-gala-option" value="Non-vegetarian" type="radio" class=""
								<?php if (isset ($_POST['radio-gala-option']) && $_POST['radio-gala-option'] == 'Non-vegetarian') echo ' checked="checked"'; ?> />
							Non-vegetarian<br />
						</div>
						<input name="radio-gala" value="No" type="radio"
							<?php if (isset ($_POST['radio-gala']) && $_POST['radio-gala'] == 'No') echo ' checked="checked"'; ?> />
						No
						<p class="gala-vkluchena waive green">Gala dinner fee of <?php echo get_gala_dinner_fee(); ?> EURO will be waived because you are attendng for the entire duration of the seminar.</p>
						<p class="gala-vkluchena add red">Gala dinner fee of <?php echo get_gala_dinner_fee(); ?> EURO will be added to your total.</p>
					</div>
				</div>

				<div class="input-row">
					<label for="radio-age">* Please choose your type of registration
						based on your age group:</label>
					<div>
						<input name="radio-age" value="adult" type="radio"
							class="required"
							<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'adult') echo ' checked="checked"'; ?> />
						Adult<br /> <input name="radio-age" value="student" type="radio"
							<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'adult') echo ' checked="checked"'; ?> />
						Full-Time College Student (see <a href="/faqs">FAQs</a>)<br /> <input
							name="radio-age" value="child" type="radio"
							<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'child') echo ' checked="checked"'; ?> /> Child (born in or after <?php echo get_of_age_year(); ?>)
                  </div>
				</div>

				<div class="input-row">
					<label for="radio-eefc">* Are you (or is your family) a member of the East European Folklife Center (EEFC) for the <?php echo get_seminar_year(); ?> calendar year? (See <a
						href="/faqs">FAQs</a>):
					</label>
					<div>
						<input name="radio-eefc" value="yes" type="radio" class="required"
							<?php if (isset ($_POST['radio-eefc']) && $_POST['radio-eefc'] == 'yes') echo ' checked="checked"'; ?> />
						Yes<br /> <input name="radio-eefc" value="no" type="radio"
							<?php if (isset ($_POST['radio-eefc']) && $_POST['radio-eefc'] == 'no') echo ' checked="checked"'; ?> />
						No
					</div>
				</div>



				<!-- CLASSES for additional -->
				<div class="input-row">
               <?php
				$args = array (
						'post_type' => 'classes',
						'post_status' => 'publish',
						'posts_per_page' => - 1,
						'orderby' => 'menu_order',
						'order' => 'ASC' 
				)
				;
				
				$my_query = new WP_Query ( $args );
				
				if ($my_query->have_posts ()) :
					?>
                  <div class="classes-wrapper">
						<p class="center bigger">
							<strong>CLASSES<br /> Please make your selections and indicate
								your level of proficiency and whether you're renting or bringing
								an instrument, if applicable. See FAQs.
							</strong>
						</p>
                     <?php
					
while ( $my_query->have_posts () ) :
						$my_query->the_post ();
						$class_id = get_the_ID ();
						?>
                     <div class="class-row"
							id="class-row-<?php echo $class_id; ?>">
							<div class="class-title one-third">
								<input id="class-<?php echo $class_id; ?>" type='checkbox'
									multiple='multiple' name='ckb_class[]'
									value="<?php echo $class_id; ?>" /> <label
									for="class-<?php echo $class_id; ?>"><?php the_title(); ?></label>
							</div>
							<div class="class-level one-third">
                        <?php
						
$level = get_field ( 'class_levels' );
						if ($level) :
							foreach ( $level as $level_option ) :
								if ($level_option == 'experienced')
									$option = 'Experienced';
								else
									$option = 'Beginner';
								?>
                           <div class="radioboxes">
									<input type="radio" name="radio-level-<?php echo $class_id; ?>"
										id="radio-<?php echo $level_option; ?>-<?php echo $class_id; ?>"
										value="<?php echo $level_option; ?>"
										<?php if (isset($_POST ['radio-level-' . $class_id]) && $_POST ['radio-level-' . $class_id] == $level_option) echo " checked"; ?>
										disabled /> <label
										for="radio-<?php echo $level_option; ?>-<?php echo $class_id; ?>"><?php echo $option; ?></label>
								</div>
                        <?php endforeach;
						 else :
							?>
                        <p class="mobile-hide">N/A</p> 
						<?php endif;
						?>
                        </div>
							<div class="class-bring-rent one-third">
                        <?php
						
						$rent_bring = get_field ( 'rent_bring' );
						if ($rent_bring) :
							foreach ( $rent_bring as $rb_option ) :
								if ($rb_option == 'rent') {
									$available = get_available_count_for_rent ( $class_id, $reg_id );
									if ($available <= 0) continue;
									$option = 'Rent';
								} else $option = 'Bring instrument';
								?>
                           <div class="radioboxes">
									<input type="radio"
										name="radio-rent-bring-<?php echo $class_id; ?>"
										id="radio-<?php echo $rb_option; ?>-<?php echo $class_id; ?>"
										value="<?php echo $rb_option; ?>"
										<?php if (isset($_POST ['radio-rent-bring-' . $class_id]) && $_POST ['radio-rent-bring-' . $class_id] == $rb_option) echo " checked"; ?>
										disabled /> <label
										for="radio-<?php echo $rb_option; ?>-<?php echo $class_id; ?>"><?php echo $option; ?></label>
                              <?php if ($rb_option == 'rent'): ?> 
                              	<ul style="padding-bottom: 7px;">
										<li><?php echo $available; ?> available</li>
										<li class="red">Rental fee of <?php echo get_instrument_rental_fee($class_id); ?> EURO per day will apply</li>
									</ul>
                              <?php endif; ?>
                           </div>
                        <?php endforeach;
							if (in_array ( 'rent', $rent_bring ) && $available <= 0) :
								?>
								<div class="indent">None available for rental</div>
							
							<?php 
							endif;
						 else:
							?>
                        <p class="mobile-hide">N/A</p> 
						<?php endif;
						?>
                        </div>

						</div>
                     <?php endwhile; ?>
                  </div>
               
				<?php endif;
				wp_reset_query ();
				wp_reset_postdata ();
				?>
               </div>
				<!-- END CLASSES -->
				
				<div class="input-row">
					<label for="radio-fleita">We have just added a flute class to the program. Are you interested in taking the flute class?</label>
					<div>
						<input name="radio-fleita" value="yes" type="radio"
								<?php if (isset ($_POST['radio-fleita']) && $_POST['radio-fleita'] == 'yes') echo ' checked="checked"'; ?> />
							Yes<br /> <input name="radio-fleita" value="no" type="radio"
								<?php if (!isset ($_POST['radio-fleita']) || $_POST['radio-fleita'] == 'no') echo ' checked="checked"'; ?> />
							No
					</div>
				</div>

               <?php if (get_field ('show_dvd_available_field')): ?>
               <div class="input-row">
					<label for="radio-dvd">* <?php the_field ('question_text_dvd_available'); ?></label>
					<div>
						<input name="radio-dvd" value="yes" type="radio" class="required"
							<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' checked="checked"'; ?> /> Yes (Add <?php the_field ('dvd_price'); ?> EURO)<br />
						<div
							class="dvd-format-wrapper<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' show-me'; ?>">
							<input name="radio-dvd-format" value="ntsc" type="radio"
								class="<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo 'required'; ?>"
								<?php if (isset ($_POST['radio-dvd-format']) && $_POST['radio-dvd-format'] == 'ntsc') echo ' checked="checked"'; ?> />
							NTSC format<br /> 
							<input name="radio-dvd-format" value="pal" type="radio"
								<?php if (isset ($_POST['radio-dvd-format']) && $_POST['radio-dvd-format'] == 'pal') echo ' checked="checked"'; ?> />
							PAL format
						</div>
						<div
							class="dvd-format-info-wrapper<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' show-me'; ?>">
							<p>
								<a href="/wp-content/uploads/2016/01/PAL_NTSC_country_list.pdf"
									target="_blank">Pal/NTSC Country List</a>
							</p>
						</div>
						<input name="radio-dvd" value="no" type="radio"
							<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'no') echo ' checked="checked"'; ?> />
						No
					</div>
				</div>
               <?php endif; ?>

               <?php
				
if (get_field ( 'show_koprivshtitsa_transportation_field' )) :
					$fee = get_field ( 'koprivshtitsa_transportation_fee' );
					?>
               <div class="input-row">
					<label for="radio-transport">* <?php the_field ('question_text_koprivshtitsa_transporation'); ?></label>
					<div>
						<input name="radio-transport" value="one-way" type="radio"
							class="required"
							<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'yes') echo ' checked="checked"'; ?> /> One-way (Add <?php echo $fee; ?> EURO)<br />
<!-- 						<input name="radio-transport" value="round-trip" type="radio" -->
<!-- 							class="required" -->
							<?php //if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'yes') echo ' checked="checked"'; ?><!-- Round Trip (Add --><?php //echo 2 * $fee; ?><!-- EURO)<br />-->
						<input name="radio-transport" value="no" type="radio"
							<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'no') echo ' checked="checked"'; ?> />
						No
					</div>
				</div>
               <?php endif; ?>

               <div class="feedback">Please correct errors on form.</div>

				<div class="form-buttons">
					<div class="frm_submit">

						<p class="submit-button">
							<input type="submit" value="Continue" name="go_submit"
								id="go_submit" />
						</p>

					</div>

					<div class="frm_submit">

						<p class="submit-button">
							<input type="reset" value="Clear Form" name="go_clear"
								id="go_clear" />
						</p>

					</div>

					<div class="frm_submit cancel">

						<p class="submit-button">
							<input type="submit" value="Cancel" name="go_cancel"
								id="go_cancel" />
						</p>

					</div>
				</div>


			</div>


		</form>

	</div>
	<!-- END form-wrapper -->



      <?php else: ?>
      <?php the_content(); ?>

      <p class="flt-l">* Indicates a required field.</p>
      
      <?php
				
$pdf = get_field ( 'paper_registration_pdf', 'option' );
				$word = get_field ( 'paper_registration_word_doc', 'option' );
				if ($pdf || $word) :
					?>
      <div class="paper-registration-wrapper">
		<div class="paper-registration-links">
         
         	<?php if ($pdf): ?>
            <a href="<?php echo $pdf ['url']; ?>">Download form in PDF</a>
			<div class="spacer-border"></div>
            <?php endif; ?>
          	<?php if ($word): ?>
            <a href="<?php echo $word ['url']; ?>">Download in Word</a>
            <?php endif; ?>
         </div>
	</div>
      <?php endif; ?>

      <div class="feedback">Please correct errors on form.</div>

	<div class="form-wrapper">
		<form id="register-form" method="post">
			<div class="general-info">
				<div class="input-row">
					<label for="txt-ime">* First Name: </label> <input name="txt-ime"
						id="txt-ime" type="text" class="required"
						<?php if (isset ($_POST['txt-ime']) && trim($_POST['txt-ime']) != '') echo ' value="' . $_POST['txt-ime'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-familia">* Last Name: </label> <input
						name="txt-familia" id="txt-familia" type="text" class="required"
						<?php if (isset ($_POST['txt-familia']) && trim($_POST['txt-familia']) != '') echo ' value="' . $_POST['txt-familia'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-adres">* Address (# Street, apt.): </label> <input
						name="txt-adres" id="txt-adres" type="text" class="required"
						<?php if (isset ($_POST['txt-adres']) && trim($_POST['txt-adres']) != '') echo ' value="' . $_POST['txt-adres'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-adres2">Address 2: </label> <input
						name="txt-adres2" id="txt-adres2" type="text"
						<?php if (isset ($_POST['txt-adres2']) && trim($_POST['txt-adres2']) != '') echo ' value="' . $_POST['txt-adres2'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-grad">* City: </label> <input name="txt-grad"
						id="txt-grad" type="text" class="required"
						<?php if (isset ($_POST['txt-grad']) && trim($_POST['txt-grad']) != '') echo ' value="' . $_POST['txt-grad'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-state">* State/Province: </label> <input
						name="txt-state" id="txt-state" type="text" class="required"
						<?php if (isset ($_POST['txt-state']) && trim($_POST['txt-state']) != '') echo ' value="' . $_POST['txt-state'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-kod">* Zip Code/Postal: </label> <input
						name="txt-kod" id="txt-kod" type="text" class="required"
						<?php if (isset ($_POST['txt-kod']) && trim($_POST['txt-kod']) != '') echo ' value="' . $_POST['txt-kod'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-strana">* Country: </label> <input
						name="txt-strana" id="txt-strana" type="text" class="required"
						<?php if (isset ($_POST['txt-strana']) && trim($_POST['txt-strana']) != '') echo ' value="' . $_POST['txt-strana'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-tel">* Phone # (starting with area code): </label>
					<input name="txt-tel" id="txt-tel" type="tel" class="required"
						<?php if (isset ($_POST['txt-tel']) && trim($_POST['txt-tel']) != '') echo ' value="' . $_POST['txt-tel'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-poshta">* Email: </label> <input name="txt-poshta"
						id="txt-poshta" type="email" class="required"
						<?php if (isset ($_POST['txt-poshta']) && trim($_POST['txt-poshta']) != '') echo ' value="' . $_POST['txt-poshta'] . '"'; ?> />
				</div>

				<div class="input-row">
					<label for="txt-poshta2">* Confirm Email:<span
						class="error-password-mismatch"><br />Email address mismatch</span></label>
					<input name="txt-poshta2" id="txt-poshta2" type="email"
						class="required" />
				</div>

				<div class="input-row">
					<label for="txt-speshnost">Emergency Contact<br /> <span
						class="smaller">(Name, address, telephone number, email address of
							a person in your home country)</span></label>
					<div class="emergency-wrapper">
						<div class="txt-right">
							Characters remaining: <span class="charCount bold">500</span>
						</div>
						<textarea name="txt-speshnost" id="txt-speshnost" rows="3"
							maxlength="500"></textarea>
					</div>
				</div>

				<div class="input-row">
					<label for="select-broi-dni">* How many days you are planning to
						attend the Folk Seminar?</label> <select id="select-broi-dni"
						name="select-broi-dni" class="required">
						<option value=""
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == '') echo ' selected="selected"'; ?>>-Select-</option>
						<option value="1"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 1) echo ' selected="selected"'; ?>>1
							day</option>
						<option value="2"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 2) echo ' selected="selected"'; ?>>2
							days</option>
						<option value="3"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 3) echo ' selected="selected"'; ?>>3
							days</option>
						<option value="4"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 4) echo ' selected="selected"'; ?>>4
							days</option>
						<option value="5"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 5) echo ' selected="selected"'; ?>>5
							days</option>
						<option value="6"
							<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 6) echo ' selected="selected"'; ?>>Entire
							Seminar</option>
					</select>
				</div>

				<div class="input-row">
					<label for="radio-gala">* Will you attend the Tuesday night gala
						dinner:</label>
					<div>
						<input name="radio-gala" value="Yes" type="radio" class="required"
							<?php if (isset ($_POST['radio-gala'])) echo ' checked="checked"'; ?> />
						Yes<br />
						<div class="gala-option indent">
							<input name="radio-gala-option" value="Vegetarian" type="radio"
								class="<?php if (isset ($_POST['radio-gala']) && $_POST['radio-gala'] == 'Yes') echo 'required'; ?>"
								<?php if (isset ($_POST['radio-gala-option'])  && $_POST['radio-gala-option'] == 'Vegetarian') echo ' checked="checked"'; ?> />
							Vegetarian<br /> <input name="radio-gala-option" value="Non-vegetarian" type="radio" class=""
								<?php if (isset ($_POST['radio-gala-option']) && $_POST['radio-gala-option'] == 'Non-vegetarian') echo ' checked="checked"'; ?> />
							Non-vegetarian<br />
						</div>
						<input name="radio-gala" value="No" type="radio"
							<?php if (isset ($_POST['radio-gala']) && $_POST['radio-gala'] == 'No') echo ' checked="checked"'; ?> />
						No
						<p class="gala-vkluchena waive green">Gala dinner fee of <?php echo get_gala_dinner_fee(); ?> EURO will be waived because you are attendng for the entire duration of the seminar.</p>
						<p class="gala-vkluchena add red">Gala dinner fee of <?php echo get_gala_dinner_fee(); ?> EURO will be added to your total.</p>
					</div>
				</div>

				<div class="input-row">
					<label for="radio-age">* Please choose your type of registration
						based on your age group:</label>
					<div>
						<input name="radio-age" value="adult" type="radio"
							class="required"
							<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'adult') echo ' checked="checked"'; ?> />
						Adult<br /> <input name="radio-age" value="student" type="radio"
							<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'adult') echo ' checked="checked"'; ?> />
						Full-Time College Student (see <a href="/faqs">FAQs</a>)<br /> <input
							name="radio-age" value="child" type="radio"
							<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'child') echo ' checked="checked"'; ?> /> Child (born in or after <?php echo get_of_age_year(); ?>)
                  </div>
				</div>

				<div class="input-row">
					<label for="radio-eefc">* Are you (or is your family) a member of the East European Folklife Center (EEFC) for the <?php echo get_seminar_year(); ?> calendar year? (See <a
						href="/faqs">FAQs</a>):
					</label>
					<div>
						<input name="radio-eefc" value="yes" type="radio" class="required"
							<?php if (isset ($_POST['radio-eefc']) && $_POST['radio-eefc'] == 'yes') echo ' checked="checked"'; ?> />
						Yes<br /> <input name="radio-eefc" value="no" type="radio"
							<?php if (isset ($_POST['radio-eefc']) && $_POST['radio-eefc'] == 'no') echo ' checked="checked"'; ?> />
						No
					</div>
				</div>

				<div class="input-row">
					<label for="radio-payment">* What method of payment do you prefer?</label>
					<div>
                            <?php if ($ok_banktransfer): ?>
                     <input name="radio-payment" value="transfer"
							type="radio" class="required"
							<?php if (isset ($_POST['radio-payment']) && $_POST['radio-payment'] == 'transfer') echo ' checked="checked"'; ?> />
						Bank Transfer in Euros - Instructions will be emailed to you in
						your registration confirmation email.<br />
                            <?php endif; ?>
                     <input name="radio-payment" value="on-site" class="<?php if (!$ok_banktransfer): echo 'required'; endif; ?>"
							type="radio"
							<?php if (isset ($_POST['radio-payment']) && $_POST['radio-payment'] == 'on-site') echo ' checked="checked"'; ?> />
						On-site Payment
					</div>
				</div>


				<!-- CLASSES -->
				<div class="input-row">
               <?php
				$args = array (
						'post_type' => 'classes',
						'post_status' => 'publish',
						'posts_per_page' => - 1,
						'orderby' => 'menu_order',
						'order' => 'ASC' 
				)
				;
				
				$my_query = new WP_Query ( $args );
				
				if ($my_query->have_posts ()) :
					?>
                  <div class="classes-wrapper">
						<p class="center bigger">
							<strong>CLASSES<br /> Please make your selections and indicate
								your level of proficiency and whether you're renting or bringing
								an instrument, if applicable. See FAQs.
							</strong>
						</p>
                     <?php
					
while ( $my_query->have_posts () ) :
						$my_query->the_post ();
						$class_id = get_the_ID ();
						?>
                     <div class="class-row"
							id="class-row-<?php echo $class_id; ?>">
							<div class="class-title one-third">
								<input id="class-<?php echo $class_id; ?>" type='checkbox'
									multiple='multiple' name='ckb_class[]'
									value="<?php echo $class_id; ?>"
									<?php //if (in_array ($class_id, $_POST['ckb_class'])) echo " checked"; ?> />
								<label for="class-<?php echo $class_id; ?>"><?php the_title(); ?></label>
							</div>
							<div class="class-level one-third">
                        <?php
						
$level = get_field ( 'class_levels' );
						if ($level) :
							foreach ( $level as $level_option ) :
								if ($level_option == 'experienced')
									$option = 'Experienced';
								else
									$option = 'Beginner';
								?>
                           <div class="radioboxes">
									<input type="radio" name="radio-level-<?php echo $class_id; ?>"
										id="radio-<?php echo $level_option; ?>-<?php echo $class_id; ?>"
										value="<?php echo $level_option; ?>"
										<?php if (isset($_POST ['radio-level-' . $class_id]) && $_POST ['radio-level-' . $class_id] == $level_option) echo " checked"; ?>
										disabled /> <label
										for="radio-<?php echo $level_option; ?>-<?php echo $class_id; ?>"><?php echo $option; ?></label>
								</div>
                        <?php
							
endforeach
							;
						 else :
							?>
                        <p class="mobile-hide">N/A</p> 
						<?php endif;
						?>
                        </div>
							<div class="class-bring-rent one-third">
                        <?php
						
$rent_bring = get_field ( 'rent_bring' );
						if ($rent_bring) :
							foreach ( $rent_bring as $rb_option ) :
								if ($rb_option == 'rent') {
									$available = get_available_count_for_rent ( $class_id );
									if ($available <= 0)
										continue;
									$option = 'Rent';
								} else
									$option = 'Bring instrument';
								?>
                           <div class="radioboxes">
									<input type="radio"
										name="radio-rent-bring-<?php echo $class_id; ?>"
										id="radio-<?php echo $rb_option; ?>-<?php echo $class_id; ?>"
										value="<?php echo $rb_option; ?>"
										<?php if (isset($_POST ['radio-rent-bring-' . $class_id]) && $_POST ['radio-rent-bring-' . $class_id] == $rb_option) echo " checked"; ?>
										disabled /> <label
										for="radio-<?php echo $rb_option; ?>-<?php echo $class_id; ?>"><?php echo $option; ?></label>
                              <?php if ($rb_option == 'rent'): ?> 
                              	<ul>
										<li><?php echo $available; ?> available</li>
										<li class="red">Rental fee of <?php echo get_instrument_rental_fee($class_id); ?> EURO per day will apply</li>
									</ul>
                              <?php endif; ?>
                           </div>
                        <?php
							
endforeach
							;
							if (in_array ( 'rent', $rent_bring ) && $available <= 0) :
								?>
								<div class="indent">None available for rental</div>
							
							<?php 
							endif;
						 else :
							?>
                        N/A
								<p class="mobile-hide"></p>
								
								 
						<?php endif;
						?>
                        
							
							</div>

						</div>
                     <?php endwhile; ?>
                  </div>
               
				<?php endif;
				wp_reset_query ();
				wp_reset_postdata ();
				?>
               </div>
				<!-- END CLASSES -->
				
				<div class="input-row">
					<label for="radio-fleita">We have just added a flute class to the program. Are you interested in taking the flute class?</label>
					<div>
						<input name="radio-fleita" value="yes" type="radio"
								<?php if (isset ($_POST['radio-fleita']) && $_POST['radio-fleita'] == 'yes') echo ' checked="checked"'; ?> />
							Yes<br /> <input name="radio-fleita" value="no" type="radio"
								<?php if (!isset ($_POST['radio-fleita']) || $_POST['radio-fleita'] == 'no') echo ' checked="checked"'; ?> />
							No
					</div>
				</div>

               <?php if (get_field ('show_dvd_available_field')): ?>
               <div class="input-row">
					<label for="radio-dvd">* <?php the_field ('question_text_dvd_available'); ?></label>
					<div>
						<input name="radio-dvd" value="yes" type="radio" class="required"
							<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' checked="checked"'; ?> /> Yes (Add <?php the_field ('dvd_price'); ?> EURO)<br />
						<div
							class="dvd-format-wrapper<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' show-me'; ?>">
							<input name="radio-dvd-format" value="ntsc" type="radio"
								class="<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo 'required'; ?>"
								<?php if (isset ($_POST['radio-dvd-format']) && $_POST['radio-dvd-format'] == 'ntsc') echo ' checked="checked"'; ?> />
							NTSC format<br /> <input name="radio-dvd-format" value="pal"
								type="radio"
								<?php if (isset ($_POST['radio-dvd-format']) && $_POST['radio-dvd-format'] == 'pal') echo ' checked="checked"'; ?> />
							PAL format
						</div>
						<div
							class="dvd-format-info-wrapper<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' show-me'; ?>">
							<p>
								<a href="/wp-content/uploads/2016/01/PAL_NTSC_country_list.pdf"
									target="_blank">Pal/NTSC Country List</a>
							</p>
						</div>
						<input name="radio-dvd" value="no" type="radio"
							<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'no') echo ' checked="checked"'; ?> />
						No
					</div>
				</div>
               <?php endif; ?>

               <?php
				
if (get_field ( 'show_koprivshtitsa_transportation_field' )) :
					$fee = get_field ( 'koprivshtitsa_transportation_fee' );
					?>
               <div class="input-row">
					<label for="radio-transport">* <?php the_field ('question_text_koprivshtitsa_transporation'); ?></label>
					<div>
						<input name="radio-transport" value="one-way" type="radio"
							class="required"
							<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'yes') echo ' checked="checked"'; ?> /> One-way (Add <?php echo $fee; ?> EURO)<br />
						<!-- <input name="radio-transport" value="round-trip" type="radio"
							class="required" -->
							<?php //if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'yes') echo ' checked="checked"'; ?><!-- /> Round Trip (Add --><?php //echo 2 * $fee; ?><!-- EURO)<br /> -->
						<input name="radio-transport" value="no" type="radio"
							<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'no') echo ' checked="checked"'; ?> />
						No
					</div>
				</div>
               <?php endif; ?>

               <div class="input-row math">
               <?php include ('includes/inc-generate-math-problem.php'); ?>
               </div>

				<div class="feedback">Please correct errors on form.</div>

				<div class="form-buttons">
					<div class="frm_submit">

						<p class="submit-button">
							<input type="submit" value="Continue" name="go_submit"
								id="go_submit" />
						</p>

					</div>

					<div class="frm_submit">

						<p class="submit-button">
							<input type="reset" value="Clear Form" name="go_clear"
								id="go_clear" />
						</p>

					</div>
				</div>


			</div>


		</form>

	</div>
	<!-- END form-wrapper -->

      <?php endif; ?>

   </div>

<?php
		
endwhile
		;
	
endif;
	?>
<?php get_footer(); ?>

<?php endif; // END check if registration is open ?>