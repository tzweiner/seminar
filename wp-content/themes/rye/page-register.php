<?php // check if registration is closed ?>
<?php 
	$today = date('Y-m-d');
	$reg_closed_date = date ('Y-m-d', strtotime (get_field ('registration_closed_date', 'option')));
	$ok_to_register = $today <= $reg_closed_date;

	if (!$ok_to_register): 	// registration is closed ?>
<?php get_header(); ?>
<?php if ( have_posts() ) : ?>

	<?php while (have_posts()) : the_post(); ?>
	<h2><span><?php the_title(); ?></span></h2>
	<div class="c12 row">
		<?php echo get_red_note(); ?>
		<h3>Registration is not yet open for the next seminar year. Please check back with us later.</h3>
	</div>
<?php endwhile;
endif; ?>
<?php get_footer(); ?>

<?php else: // registration is open ?>


<?php get_header(); ?>
<?php if ( have_posts() ) : ?>

	<?php while (have_posts()) : the_post(); ?>
	<h2><span><?php the_title(); ?></span></h2>
	<div class="c12 row register">
		<?php echo get_red_note(); ?>
		
		
		<?php if (isset($_POST['go_submit']) && $_POST['go_submit'] != ''):	
		
			
			// get all the POST values
			if (isset ($_POST ['txt-ime']) && trim ($_POST ['txt-ime']) != '')
				$first_name = trim (mysql_real_escape_string ($_POST ['txt-ime']));
			
			if (isset ($_POST ['txt-ime']) && trim ($_POST ['txt-familia']) != '')
				$last_name = trim (mysql_real_escape_string ($_POST ['txt-familia']));
			
			if (isset ($_POST ['txt-adres']) && trim ($_POST ['txt-adres']) != '')
				$address1 = trim (mysql_real_escape_string ($_POST ['txt-adres']));
			else 
				$address1 = NULL;
			
			if (isset ($_POST ['txt-adres2']) && trim ($_POST ['txt-adres2']) != '')
				$address2 = trim (mysql_real_escape_string ($_POST ['txt-adres2']));
			else 
				$address2 = NULL;
			
			if (isset ($_POST ['txt-grad']) && trim ($_POST ['txt-grad']) != '')
				$city = trim (mysql_real_escape_string ($_POST ['txt-grad']));
			else
				$city = NULL;
			
			if (isset ($_POST ['txt-state']) && trim ($_POST ['txt-state']) != '')
				$state = trim (mysql_real_escape_string ($_POST ['txt-state']));
			else 
				$state = NULL;
			
			if (isset ($_POST ['txt-state']) && trim ($_POST ['txt-state']) != '')
				$zip = trim (mysql_real_escape_string ($_POST ['txt-kod']));
			else 
				$zip = NULL;
			
			if (isset ($_POST ['txt-strana']) && trim ($_POST ['txt-strana']) != '')
				$country = trim (mysql_real_escape_string ($_POST ['txt-strana']));
			else
				$country = NULL;
			
			if (isset ($_POST ['txt-tel']) && trim ($_POST ['txt-tel']) != '')
				$phone = trim (mysql_real_escape_string($_POST ['txt-tel']));
			else $phone = NULL;
			
			if (isset ($_POST ['txt-poshta']) && trim ($_POST ['txt-poshta']) != '') {
				$email = trim (mysql_real_escape_string($_POST ['txt-poshta']));
				$is_primary = 1;
			}
			else {
				$email = NULL;
				$is_primary = 0;
			}
			
						
			$num_days = $_POST ['select-broi-dni'];
			
			$age = $_POST ['radio-age'];
			
			$eefc = ($_POST ['radio-eefc'] == 'no') ? 0 : 1;

			$dvd = ($_POST ['radio-dvd'] == 'no') ? 0 : 1;
			
			
			if (isset ($_POST['radio-dvd-format']) && $_POST['radio-dvd-format']) {
				$dvd_format = $_POST['radio-dvd-format'];
			}
			else $dvd_format = NULL;
			//exit(var_dump($dvd_format));
			
			
			$transport = $_POST ['radio-transport'];
			if ($transport == 'one-way') {
				$transport = 1;
			}
			else if ($transport == 'round-trip') {
				$transport = 2;
			}
			else {		// No
				$transport = 0;
			}
			
			
			if (isset ($_POST ['radio-payment']))
				$payment = trim (mysql_real_escape_string($_POST ['radio-payment']));
			else {
				if (isset ($_POST ['registration_id']) && trim ($_POST ['registration_id']) != '') {
					$reg_id = $_POST ['registration_id'];
					$registration = getRegistrationRows ($reg_id);
					$payment = $registration[0]->payment;
				}
				else { // just a default value so we can at least calculate balance
					$payment = 'transfer';
				}
			}
				
			
			date_default_timezone_set('America/New_York');
			$date = date('Y-m-d H:i:s');
			
			if (isset ($_POST ['registration_id']) && trim ($_POST ['registration_id']) != '') {
				$reg_id = $_POST ['registration_id'];
				$registration = getRegistrationRows ($reg_id);
			}
			else $reg_id = $email . '_' . microtime();
			
			$registration = getRegistrationRows ($reg_id);
			
			$reg_year = date ('Y', strtotime (get_start_date()));
			$reg_slot = count ($registration) + 1;
			
			
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
					num_days = $num_days,
					age = '$age',
					is_eefc = $eefc,
					payment = '$payment',
					transport = $transport,
					dvd = $dvd,
					dvd_format = '$dvd_format',
					cancel = 0,
					balance = ". get_balance_individual ($num_days, $age, $eefc, $payment, $dvd, $transport);
			
			
			$wpdb->query(
				$sql
			);
			
			// get classes this person selected
			$classes_selected = $_POST['ckb_class'];
			foreach ($classes_selected as $class_id):
			
				$rent_bring = $_POST['radio-rent-bring-' . $class_id] == 'rent' ? 1 : 0;
				$level = $_POST['radio-level-' . $class_id];
				
				// add to classes table
				$sql = "INSERT INTO $table_classes
						SET reg_id = '$reg_id',
						reg_slot = $reg_slot,
						class_id = $class_id,
						rent = $rent_bring,
						level = '$level'";
					
				$wpdb->query(
					$sql
				);
		
			endforeach;
			
			// get all rows after we've inserted new registrant
			// Display captured info
			
			$registration = getRegistrationRows ($reg_id); 
			$reg_number = intval ($registration[0]->id); 
			$num_people = count ($registration); 
			$case = $num_people > 1 ? 'people' : 'person';
			$payment_option = $registration[0]->payment == 'on-site' ? 'On site' : 'Bank Transfer (instructions will be emailed to you when you complete registration)'; ?>
		
		<p class="bigger red">NOTE: Clicking the Back button on your browser may result in a duplicate registration.</p> 
		<h3>Summary</h3>
		
		<div class="registrant-wrapper">
			<?php echo $num_people; ?> <?php echo $case; ?> registered.<br />
			Balance: EURO <?php echo number_format (get_balance ($reg_id), 2); ?><br />
			Payment: <?php echo $payment_option; ?>
		</div>
		<p class="bigger">To complete your registration, please press "Confirm and Complete". After that, a confirmation email will be sent to you within 24 hours.</p>
		<p class="bigger">To add another registrant, please press "Add Another Person".</p>
		
		<form id="continue-form" method="post">
			
			<input type="hidden" name="registration_id" value="<?php echo $reg_id; ?>" />
			<div class="form-buttons">
			
				<div class="frm_submit">

					<p class="submit-button"><input type="submit" value="Confirm and Complete" name="go_confirm" id="go_confirm" /></p>
					
				</div>
				
				<div class="frm_submit">

					<p class="submit-button"><input type="submit" value="Add Another Person" name="go_add" id="go_add" /></p>
					
				</div>
				
				<div class="frm_submit cancel">

					<p class="submit-button"><input type="submit" value="Cancel" name="go_cancel" id="go_cancel" /></p>
					
				</div>
				
				
			</div>
		</form>
		
		<?php elseif (isset($_POST['go_confirm']) && $_POST['go_confirm'] != ''): ?>
		<input type="hidden" name="registration_id" value="<?php echo $reg_id; ?>" />
		
		<?php $reg_id = $_POST ['registration_id']; 
		$registration = getRegistrationRows ($reg_id);
		confirmRegistration ($reg_id);
		sendRegisterEmail ($registration);		
		?>
		
		<h3>Registration #<?php echo $registration[0]->id; ?> complete.</h3>
		<?php 
		$num_people = count ($registration);
		$case = $num_people > 1 ? 'people' : 'person';
		$payment_option = $registration[0]->payment == 'on-site' ? 'On site' : 'Bank Transfer (look for instructions in confirmation email)';					
		?>
		<div class="registrant-wrapper">
			<?php echo $num_people; ?> <?php echo $case; ?> registered.<br />
			Balance: EURO <?php echo number_format (get_balance ($reg_id), 2); ?><br />
			Payment: <?php echo $payment_option; ?>
		</div>
		
		<p>We have sent a confirmation email to you. Please refer to that email for further details.<br />
		Please <a href="mailto:contact@folkseminarplovdiv.net">contact us</a> if you did not receive a confirmation email.<br />
		Thank you and we look forward to seeing you in Plovdiv!</p>
		<p>Larry Weiner & Dilyana Kurdova<br />International Program Coordinators</p>
		
		
		<?php elseif (isset($_POST['go_cancel']) && $_POST['go_cancel'] != ''): ?>
		<?php $reg_id = $_POST ['registration_id']; 
		$registration = getRegistrationRows ($reg_id);
		cancelRegistration ($reg_id); ?>		
		<p>Registration #<?php echo $registration[0]->id; ?> cancelled.</p>
		<?php 
		// send email
		$email = $registration[0]->email;
		wp_mail(
			$email,
			'Seminar Registration #' . $registration[0]->id . ' cancelled',
			"Seminar Registration #" . $registration[0]->id . " cancelled" . "\r\n\r\n" .
			"Email Address: " . $email . "\r\n\r\n",
			'From: ' . get_bloginfo('name').' <' . get_bloginfo('admin_email') . '>' . "\r\n" .
			'Reply-To: ' . get_bloginfo('admin_email')
		);
		
		?>
		
		
		<?php elseif (isset($_POST['go_add']) && $_POST['go_add'] != ''): ?>
		<?php $reg_id = $_POST ['registration_id']; ?>
		

		<p class="flt-l">* Indicates a required field.</p>
		
		<div class="feedback">Please correct errors on form.</div>
		
		<div class="form-wrapper">
			<form id="add-form" method="post">
				<div class="general-info">
				
					<input type="hidden" name="registration_id" value="<?php echo $reg_id; ?>" />
					<div class="input-row">
						<label for="txt-ime">* First Name: </label>
						<input name="txt-ime" id="txt-ime" type="text" class="required"<?php if (isset ($_POST['txt-ime']) && trim($_POST['txt-ime']) != '') echo ' value="' . $_POST['txt-ime'] . '"'; ?> />
					</div>	
					
					<div class="input-row">
						<label for="txt-familia">* Last Name: </label>
						<input name="txt-familia" id="txt-familia" type="text" class="required"<?php if (isset ($_POST['txt-familia']) && trim($_POST['txt-familia']) != '') echo ' value="' . $_POST['txt-familia'] . '"'; ?> />
					</div>	

					<div class="input-row">
						<label for="select-broi-dni">* How many days you are planning to attend the Folk Seminar?</label>
						<select id="select-broi-dni" name="select-broi-dni" class="required">
							<option value=""<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == '') echo ' selected="selected"'; ?>>-Select-</option>
							<option value="1"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 1) echo ' selected="selected"'; ?>>1 day</option>
							<option value="2"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 2) echo ' selected="selected"'; ?>>2 days</option>
							<option value="3"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 3) echo ' selected="selected"'; ?>>3 days</option>
							<option value="4"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 4) echo ' selected="selected"'; ?>>4 days</option>
							<option value="5"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 5) echo ' selected="selected"'; ?>>5 days</option>
							<option value="6"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 6) echo ' selected="selected"'; ?>>Entire Seminar</option>
						</select>
					</div>	
					
					<div class="input-row">					
						<label for="radio-age">* Please choose your type of registration based on your age group:</label>
						<div>
							<input name="radio-age" value="adult" type="radio" class="required"<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'adult') echo ' checked="checked"'; ?> /> Adult<br />
							<input name="radio-age" value="student" type="radio"<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'adult') echo ' checked="checked"'; ?> /> Full-Time College Student (see <a href="/faqs">FAQs</a>)<br />
							<input name="radio-age" value="child" type="radio"<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'child') echo ' checked="checked"'; ?> /> Child (born in or after <?php echo get_of_age_year(); ?>)
						</div>
					</div>	
					
					<div class="input-row">
						<label for="radio-eefc">* Member of the East European Folklife Center (EEFC) for the <?php echo get_seminar_year(); ?> calendar year? (See <a href="/faqs">FAQs</a>):</label>
						<div>
							<input name="radio-eefc" value="yes" type="radio" class="required"<?php if (isset ($_POST['radio-eefc']) && $_POST['radio-eefc'] == 'yes') echo ' checked="checked"'; ?> /> Yes<br />
							<input name="radio-eefc" value="no" type="radio"<?php if (isset ($_POST['radio-eefc']) && $_POST['radio-eefc'] == 'no') echo ' checked="checked"'; ?> /> No
						</div>
					</div>	
					
					
					
					<!-- CLASSES for additional -->
					<div class="input-row">
					<?php 
					$args = array (
						'post_type'	=> 'classes',
						'post_status'	=> 'publish',
						'posts_per_page'	=> -1,
						'orderby' => 'menu_order',
						'order' => 'ASC',
						
					);
					
					$my_query = new WP_Query ($args);
					
					
					if ($my_query->have_posts()): ?>
						<div class="classes-wrapper">
							<p class="center bigger"><strong>CLASSES<br />
							Please make your selections and indicate for each whether you are bringing or renting an instrument and your level of proficiency.</strong></p>
							<?php while ($my_query->have_posts()): $my_query->the_post();
							$class_id = get_the_ID(); ?>
							<div class="class-row" id="class-row-<?php echo $class_id; ?>">
								<div class="class-title one-third">									
									<input id="class-<?php echo $class_id; ?>" type='checkbox' multiple='multiple' name='ckb_class[]' value="<?php echo $class_id; ?>" />
									<label for="class-<?php echo $class_id; ?>"><?php the_title(); ?></label>
								</div>
								<div class="class-bring-rent one-third">
								<?php $rent_bring = get_field ('rent_bring'); 
								if ($rent_bring): 
								foreach ($rent_bring as $rb_option): 
									if ($rb_option == 'rent') $option = 'Rent';   
									else $option = 'Bring instrument'; ?>
									<div class="radioboxes">
										<input type="radio" name="radio-rent-bring-<?php echo $class_id; ?>" id="radio-<?php echo $rb_option; ?>-<?php echo $class_id; ?>" value="<?php echo $rb_option; ?>"<?php if (isset($_POST ['radio-rent-bring-' . $class_id]) && $_POST ['radio-rent-bring-' . $class_id] == $rb_option) echo " checked"; ?> disabled />
										<label for="radio-<?php echo $rb_option; ?>-<?php echo $class_id; ?>"><?php echo $option; ?></label>
									</div>
								<?php endforeach;
								else: ?>
								<p class="mobile-hide">N/A</p> <?php 
								endif; ?>
								</div>
								<div class="class-level one-third">
								<?php $level = get_field ('class_levels'); 
								if ($level): 
								foreach ($level as $level_option): 
									if ($level_option == 'experienced') $option = 'Experienced';   
									else $option = 'Beginner'; ?>
									<div class="radioboxes">
										<input type="radio" name="radio-level-<?php echo $class_id; ?>" id="radio-<?php echo $level_option; ?>-<?php echo $class_id; ?>" value="<?php echo $level_option; ?>"<?php if (isset($_POST ['radio-level-' . $class_id]) && $_POST ['radio-level-' . $class_id] == $level_option) echo " checked"; ?> disabled />
										<label for="radio-<?php echo $level_option; ?>-<?php echo $class_id; ?>"><?php echo $option; ?></label>
									</div>
								<?php endforeach;
								else: ?>
								<p class="mobile-hide">N/A</p> <?php
								endif; ?>
								</div>
							</div>						
							<?php endwhile; ?>
						</div>					
					<?php endif;
					wp_reset_query();
					wp_reset_postdata(); ?>
					</div>
					<!-- END CLASSES -->
					
					<?php if (get_field ('show_dvd_available_field')): ?>
					<div class="input-row">
						<label for="radio-dvd">* <?php the_field ('question_text_dvd_available'); ?></label>
						<div>
							<input name="radio-dvd" value="yes" type="radio" class="required"<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' checked="checked"'; ?> /> Yes (Add <?php the_field ('dvd_price'); ?> EURO)<br />
							<div class="dvd-format-wrapper<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' show-me'; ?>">
								<input name="radio-dvd-format" value="ntsc" type="radio" class="<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo 'required'; ?>"<?php if (isset ($_POST['radio-dvd-format']) && $_POST['radio-dvd-format'] == 'ntsc') echo ' checked="checked"'; ?> /> NTSC format<br />
								<input name="radio-dvd-format" value="pal" type="radio"<?php if (isset ($_POST['radio-dvd-format']) && $_POST['radio-dvd-format'] == 'pal') echo ' checked="checked"'; ?> /> PAL format
							</div>
							<input name="radio-dvd" value="no" type="radio"<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'no') echo ' checked="checked"'; ?> /> No
						</div>
					</div>
					<?php endif; ?>
					
					<?php if (get_field ('show_koprivshtitsa_transportation_field')): 
					$fee = get_field ('koprivshtitsa_transportation_fee'); ?>
					<div class="input-row">
						<label for="radio-transport">* <?php the_field ('question_text_koprivshtitsa_transporation'); ?></label>
						<div>
							<input name="radio-transport" value="one-way" type="radio" class="required"<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'yes') echo ' checked="checked"'; ?> /> One-way (Add <?php echo $fee; ?> EURO)<br />
							<input name="radio-transport" value="round-trip" type="radio" class="required"<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'yes') echo ' checked="checked"'; ?> /> Round Trip (Add <?php echo 2 * $fee; ?> EURO)<br />
							<input name="radio-transport" value="no" type="radio"<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'no') echo ' checked="checked"'; ?> /> No
						</div>
					</div>
					<?php endif; ?>
					
					<div class="feedback">Please correct errors on form.</div>
					
					<div class="form-buttons">
						<div class="frm_submit">
	
							<p class="submit-button"><input type="submit" value="Continue" name="go_submit" id="go_submit" /></p>
							
						</div>
						
						<div class="frm_submit">
	
							<p class="submit-button"><input type="reset" value="Clear Form" name="go_clear" id="go_clear" /></p>
							
						</div>
						
						<div class="frm_submit cancel">

							<p class="submit-button"><input type="submit" value="Cancel" name="go_cancel" id="go_cancel" /></p>
							
						</div>
					</div>
					
					
				</div>
			
			
			</form>
		
		</div><!-- END form-wrapper -->
		
		
		
		<?php else: ?>
		<?php the_content(); ?>
		
		<p class="flt-l">* Indicates a required field.</p>
		<div class="paper-registration-wrapper">
			<div class="paper-registration-links">
			<?php $pdf = get_field ('paper_registration_pdf', 'option');
					$word = get_field ('paper_registration_word_doc', 'option'); ?>
				<a href="<?php echo $pdf ['url']; ?>">Download form in PDF</a>
				<div class="spacer-border"></div> 
				<a href="<?php echo $word ['url']; ?>">Download in Word</a>
			</div>
		</div>
		
		<div class="feedback">Please correct errors on form.</div>
		
		<div class="form-wrapper">
			<form id="register-form" method="post">
				<div class="general-info">
					<div class="input-row">
						<label for="txt-ime">* First Name: </label>
						<input name="txt-ime" id="txt-ime" type="text" class="required"<?php if (isset ($_POST['txt-ime']) && trim($_POST['txt-ime']) != '') echo ' value="' . $_POST['txt-ime'] . '"'; ?> />
					</div>	
					
					<div class="input-row">
						<label for="txt-familia">* Last Name: </label>
						<input name="txt-familia" id="txt-familia" type="text" class="required"<?php if (isset ($_POST['txt-familia']) && trim($_POST['txt-familia']) != '') echo ' value="' . $_POST['txt-familia'] . '"'; ?> />
					</div>	
					
					<div class="input-row">
						<label for="txt-adres">* Address (# Street, apt.): </label>
						<input name="txt-adres" id="txt-adres" type="text" class="required"<?php if (isset ($_POST['txt-adres']) && trim($_POST['txt-adres']) != '') echo ' value="' . $_POST['txt-adres'] . '"'; ?> />	
					</div>	
					
					<div class="input-row">
						<label for="txt-adres2">Address 2: </label>
						<input name="txt-adres2" id="txt-adres2" type="text"<?php if (isset ($_POST['txt-adres2']) && trim($_POST['txt-adres2']) != '') echo ' value="' . $_POST['txt-adres2'] . '"'; ?> />
					</div>	
					
					<div class="input-row">
						<label for="txt-grad">* City: </label>
						<input name="txt-grad" id="txt-grad" type="text" class="required"<?php if (isset ($_POST['txt-grad']) && trim($_POST['txt-grad']) != '') echo ' value="' . $_POST['txt-grad'] . '"'; ?> />
					</div>	
					
					<div class="input-row">
						<label for="txt-state">* State/Province: </label>
						<input name="txt-state" id="txt-state" type="text" class="required"<?php if (isset ($_POST['txt-state']) && trim($_POST['txt-state']) != '') echo ' value="' . $_POST['txt-state'] . '"'; ?> />
					</div>	
					
					<div class="input-row">
						<label for="txt-kod">* Zip Code/Postal: </label>
						<input name="txt-kod" id="txt-kod" type="text" class="required"<?php if (isset ($_POST['txt-kod']) && trim($_POST['txt-kod']) != '') echo ' value="' . $_POST['txt-kod'] . '"'; ?> />
					</div>	
					
					<div class="input-row">
						<label for="txt-strana">* Country: </label>
						<input name="txt-strana" id="txt-strana" type="text" class="required"<?php if (isset ($_POST['txt-strana']) && trim($_POST['txt-strana']) != '') echo ' value="' . $_POST['txt-strana'] . '"'; ?> />
					</div>	
					
					<div class="input-row">
						<label for="txt-tel">* Phone # (starting with area code): </label>
						<input name="txt-tel" id="txt-tel" type="tel" class="required"<?php if (isset ($_POST['txt-tel']) && trim($_POST['txt-tel']) != '') echo ' value="' . $_POST['txt-tel'] . '"'; ?> />	
					</div>	
					
					<div class="input-row">
						<label for="txt-poshta">* Email: </label>
						<input name="txt-poshta" id="txt-poshta" type="email" class="required"<?php if (isset ($_POST['txt-poshta']) && trim($_POST['txt-poshta']) != '') echo ' value="' . $_POST['txt-poshta'] . '"'; ?> />
					</div>
					
					<div class="input-row">
						<label for="txt-poshta2">* Confirm Email:<span class="error-password-mismatch"><br />Password mismatch</span></label>
						<input name="txt-poshta2" id="txt-poshta2" type="email" class="required" />
					</div>	
					
					<div class="input-row">
						<label for="select-broi-dni">* How many days you are planning to attend the Folk Seminar?</label>
						<select id="select-broi-dni" name="select-broi-dni" class="required">
							<option value=""<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == '') echo ' selected="selected"'; ?>>-Select-</option>
							<option value="1"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 1) echo ' selected="selected"'; ?>>1 day</option>
							<option value="2"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 2) echo ' selected="selected"'; ?>>2 days</option>
							<option value="3"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 3) echo ' selected="selected"'; ?>>3 days</option>
							<option value="4"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 4) echo ' selected="selected"'; ?>>4 days</option>
							<option value="5"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 5) echo ' selected="selected"'; ?>>5 days</option>
							<option value="6"<?php if (isset ($_POST['select-broi-dni']) && $_POST['select-broi-dni'] == 6) echo ' selected="selected"'; ?>>Entire Seminar</option>
						</select>
					</div>	
					
					<div class="input-row">					
						<label for="radio-age">* Please choose your type of registration based on your age group:</label>
						<div>
							<input name="radio-age" value="adult" type="radio" class="required"<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'adult') echo ' checked="checked"'; ?> /> Adult<br />
							<input name="radio-age" value="student" type="radio"<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'adult') echo ' checked="checked"'; ?> /> Full-Time College Student (see <a href="/faqs">FAQs</a>)<br />
							<input name="radio-age" value="child" type="radio"<?php if (isset ($_POST['radio-age']) && $_POST['radio-age'] == 'child') echo ' checked="checked"'; ?> /> Child (born in or after <?php echo get_of_age_year(); ?>)
						</div>
					</div>	
					
					<div class="input-row">
						<label for="radio-eefc">* Are you (or is your family) a member of the East European Folklife Center (EEFC) for the <?php echo get_seminar_year(); ?> calendar year? (See <a href="/faqs">FAQs</a>):</label>
						<div>
							<input name="radio-eefc" value="yes" type="radio" class="required"<?php if (isset ($_POST['radio-eefc']) && $_POST['radio-eefc'] == 'yes') echo ' checked="checked"'; ?> /> Yes<br />
							<input name="radio-eefc" value="no" type="radio"<?php if (isset ($_POST['radio-eefc']) && $_POST['radio-eefc'] == 'no') echo ' checked="checked"'; ?> /> No
						</div>
					</div>	
					
					<div class="input-row">
						<label for="radio-payment">* What method of payment do you prefer?</label>
						<div>
							<input name="radio-payment" value="transfer" type="radio" class="required"<?php if (isset ($_POST['radio-payment']) && $_POST['radio-payment'] == 'transfer') echo ' checked="checked"'; ?> /> Bank Transfer in Euros - Instructions will be emailed to you in your registration confirmation email.<br />
							<input name="radio-payment" value="on-site" type="radio"<?php if (isset ($_POST['radio-payment']) && $_POST['radio-payment'] == 'on-site') echo ' checked="checked"'; ?> /> On-site Payment
						</div>
					</div>
					
					
					<!-- CLASSES -->
					<div class="input-row">
					<?php 
					$args = array (
						'post_type'	=> 'classes',
						'post_status'	=> 'publish',
						'posts_per_page'	=> -1,
						'orderby' => 'menu_order',
						'order' => 'ASC',
						
					);
					
					$my_query = new WP_Query ($args);
					
					
					if ($my_query->have_posts()): ?>
						<div class="classes-wrapper">
							<p class="center bigger"><strong>CLASSES<br />
							Please make your selections and indicate for each whether you are bringing or renting an instrument and your level of proficiency.</strong></p>
							<?php while ($my_query->have_posts()): $my_query->the_post();
							$class_id = get_the_ID(); ?>
							<div class="class-row" id="class-row-<?php echo $class_id; ?>">
								<div class="class-title one-third">									
									<input id="class-<?php echo $class_id; ?>" type='checkbox' multiple='multiple' name='ckb_class[]' value="<?php echo $class_id; ?>" <?php //if (in_array ($class_id, $_POST['ckb_class'])) echo " checked"; ?> />
									<label for="class-<?php echo $class_id; ?>"><?php the_title(); ?></label>
								</div>
								<div class="class-bring-rent one-third">
								<?php $rent_bring = get_field ('rent_bring'); 
								if ($rent_bring): 
								foreach ($rent_bring as $rb_option): 
									if ($rb_option == 'rent') $option = 'Rent';   
									else $option = 'Bring instrument'; ?>
									<div class="radioboxes">
										<input type="radio" name="radio-rent-bring-<?php echo $class_id; ?>" id="radio-<?php echo $rb_option; ?>-<?php echo $class_id; ?>" value="<?php echo $rb_option; ?>"<?php if (isset($_POST ['radio-rent-bring-' . $class_id]) && $_POST ['radio-rent-bring-' . $class_id] == $rb_option) echo " checked"; ?> disabled />
										<label for="radio-<?php echo $rb_option; ?>-<?php echo $class_id; ?>"><?php echo $option; ?></label>
									</div>
								<?php endforeach;
								else: ?>
								<p class="mobile-hide">N/A</p> <?php 
								endif; ?>
								</div>
								<div class="class-level one-third">
								<?php $level = get_field ('class_levels'); 
								if ($level): 
								foreach ($level as $level_option): 
									if ($level_option == 'experienced') $option = 'Experienced';   
									else $option = 'Beginner'; ?>
									<div class="radioboxes">
										<input type="radio" name="radio-level-<?php echo $class_id; ?>" id="radio-<?php echo $level_option; ?>-<?php echo $class_id; ?>" value="<?php echo $level_option; ?>"<?php if (isset($_POST ['radio-level-' . $class_id]) && $_POST ['radio-level-' . $class_id] == $level_option) echo " checked"; ?> disabled />
										<label for="radio-<?php echo $level_option; ?>-<?php echo $class_id; ?>"><?php echo $option; ?></label>
									</div>
								<?php endforeach;
								else: ?>
								<p class="mobile-hide">N/A</p> <?php 
								endif; ?>
								</div>
							</div>						
							<?php endwhile; ?>
						</div>					
					<?php endif;
					wp_reset_query();
					wp_reset_postdata(); ?>
					</div>
					<!-- END CLASSES -->
					
					<?php if (get_field ('show_dvd_available_field')): ?>
					<div class="input-row">
						<label for="radio-dvd">* <?php the_field ('question_text_dvd_available'); ?></label>
						<div>
							<input name="radio-dvd" value="yes" type="radio" class="required"<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' checked="checked"'; ?> /> Yes (Add <?php the_field ('dvd_price'); ?> EURO)<br />
							<div class="dvd-format-wrapper<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo ' show-me'; ?>">
								<input name="radio-dvd-format" value="ntsc" type="radio" class="<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'yes') echo 'required'; ?>"<?php if (isset ($_POST['radio-dvd-format']) && $_POST['radio-dvd-format'] == 'ntsc') echo ' checked="checked"'; ?> /> NTSC format<br />
								<input name="radio-dvd-format" value="pal" type="radio"<?php if (isset ($_POST['radio-dvd-format']) && $_POST['radio-dvd-format'] == 'pal') echo ' checked="checked"'; ?> /> PAL format
							</div>
							<input name="radio-dvd" value="no" type="radio"<?php if (isset ($_POST['radio-dvd']) && $_POST['radio-dvd'] == 'no') echo ' checked="checked"'; ?> /> No
						</div>
					</div>
					<?php endif; ?>
					
					<?php if (get_field ('show_koprivshtitsa_transportation_field')): 
					$fee = get_field ('koprivshtitsa_transportation_fee'); ?>
					<div class="input-row">
						<label for="radio-transport">* <?php the_field ('question_text_koprivshtitsa_transporation'); ?></label>
						<div>
							<input name="radio-transport" value="one-way" type="radio" class="required"<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'yes') echo ' checked="checked"'; ?> /> One-way (Add <?php echo $fee; ?> EURO)<br />
							<input name="radio-transport" value="round-trip" type="radio" class="required"<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'yes') echo ' checked="checked"'; ?> /> Round Trip (Add <?php echo 2 * $fee; ?> EURO)<br />
							<input name="radio-transport" value="no" type="radio"<?php if (isset ($_POST['radio-transport']) && $_POST['radio-transport'] == 'no') echo ' checked="checked"'; ?> /> No
						</div>
					</div>
					<?php endif; ?>
					
					<div class="input-row math">
					<?php include ('includes/inc-generate-math-problem.php'); ?>
					</div>
					
					<div class="feedback">Please correct errors on form.</div>
					
					<div class="form-buttons">
						<div class="frm_submit">
	
							<p class="submit-button"><input type="submit" value="Continue" name="go_submit" id="go_submit" /></p>
							
						</div>
						
						<div class="frm_submit">
	
							<p class="submit-button"><input type="reset" value="Clear Form" name="go_clear" id="go_clear" /></p>
							
						</div>
					</div>
					
					
				</div>
			
			
			</form>
		
		</div><!-- END form-wrapper -->
		
		<?php endif; ?>
		
	</div> 
	
	<?php endwhile;
endif; ?>
<?php get_footer(); ?>

<?php endif; // END check if registration is open ?>