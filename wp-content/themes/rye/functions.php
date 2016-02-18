<?php
/**
 *  Include Rye coniguration file.
 */
require_once('_config.php');

/**
 *  Filters.
 *  Miscellaneous theme specific utility filters.
 */

global $INSTRUMENT_NAMES;
$INSTRUMENT_NAMES = array ('tupan' => 'T&ucirc;pan', 'gudulka' => 'G&ucirc;dulka');

// indent and hide some item in the left nav in the CMS
//add_action( 'admin_menu', 'configure_amin_menu_items' );
function configure_amin_menu_items() {

	add_submenu_page('edit.php?post_type=classes', 'Slots', 'Slots', 'edit_posts', 'edit.php?post_type=slots');
	remove_menu_page('edit.php?post_type=slots'); // because we're adding slots as submenu to Classes, we need to remove it from the primary navigation

}


// Populate Daily slots select from slots post type
function acf_load_select_actions( $field ) {

	$exclude = array ();

	// get our slots
	$args = array (
			'post_type' 	=> 'slots',
			'posts_per_page' => -1,
			'post_status' 	=> 'publish',
			'orderby' 		=> 'menu_order',
			'order' 		=> 'ASC'
	);

	$slots = get_posts ($args);

	// Reset choices
	$field['choices'] = array();


	$actions = array ();
	$actions [] = '- Select Slot -';
	
	foreach ($slots as $key => $value) {
		if (!in_array ($value->post_name, $exclude)) {
			$actions[] = $value->post_title;
		}
	}

	// Populate choices
	foreach( $actions as $action ) {
		$field['choices'][ $action ] = $action;
	}

	// Return choices
	return $field;

}
add_filter('acf/load_field/key=field_547a4bbc6c83f', 'acf_load_select_actions');	// Action sub-field

// Populate Classes select on Teacher meta
function acf_load_select_actions_teachers( $field ) {

	$exclude = array ();

	// get our slots
	$args = array (
			'post_type' 	=> 'classes',
			'posts_per_page' => -1,
			'post_status' 	=> 'publish',
			'orderby' 		=> 'title',
			'order' 		=> 'ASC'
	);

	$classes = get_posts ($args);

	// Reset choices
	$field['choices'] = array();


	$actions = array ();
	$actions [] = '- Select Specialty -';

	foreach ($classes as $key => $value) {
		if ($value->post_title == 'Dance') continue;
		if (!in_array ($value->post_name, $exclude)) {
			$actions[] = $value->post_title;
		}
	}

	// Populate choices
	foreach( $actions as $action ) {
		$field['choices'][ $action ] = $action;
	}

	// Return choices
	return $field;

}
add_filter('acf/load_field/key=field_547e75ae6f91f', 'acf_load_select_actions_teachers');


/**
 *  Theme specific methods.
 *  Other methods which make the theme function.
 */

define ('FIRST_YEAR', 2005);
define ('YEAR', date('Y'));
define ('PREVIOUS_YEAR', YEAR - 1);
define ('REDNOTE', 'Please note that this web page will be updated shortly with information about the ' . YEAR . ' Folk Seminar. We have left the ' . PREVIOUS_YEAR . ' Seminar content on-line for your reference.');


// current year
// [year]
function year_func ( $atts ){
	return date ('Y', strtotime('now'));
}
add_shortcode( 'year', 'year_func' );

// seminar_year (may be dufferent than current calendar year)
function seminar_year_func ( $atts ){
	return date ('Y', strtotime (get_field ('seminar_start_date', 'option')));
}
add_shortcode( 'seminar_year', 'seminar_year_func' );

// seminar_start_date
function seminar_start_date_func ( $atts ){
	return date ('F j, Y', strtotime (get_field ('seminar_start_date', 'option')));
}
add_shortcode( 'seminar_start_date', 'seminar_start_date_func' );

// previous_seminar_year
function previous_seminar_year_func ( $atts ){
	return intval (date ('Y', strtotime (get_field ('seminar_start_date', 'option'))) - 1);
}
add_shortcode( 'previous_seminar_year', 'previous_seminar_year_func' );

// late registration deadline
// [late_reg_deadline]
function late_reg_deadline_func ( $atts ){
	return date ('F j, Y', strtotime (get_field ('late_registration_deadline', 'option')));
}
add_shortcode( 'late_reg_deadline', 'late_reg_deadline_func' );

// late fee percent
// [late_fee_percent]
function late_fee_percent_func ( $atts ){
	return get_field ('late_registration_fee_percentage', 'option');
}
add_shortcode( 'late_fee_percent', 'late_fee_percent_func' );

// EEFC discount
// [eefc_discount]
function eefc_discount_func ( $atts ){
	return get_field ('eefc_percent', 'option');
}
add_shortcode( 'eefc_discount', 'eefc_discount_func' );

// registration closed date
// [reg_closed_date]
function reg_closed_func ( $atts ){
	return date ('F j, Y', strtotime (get_field ('registration_closed_date', 'option')));
}
add_shortcode( 'reg_closed_date', 'reg_closed_func' );

// child cut off year
// [child_cutoff_year]
function child_cutoff_year_func ( $atts ){
	return get_of_age_year();
}
add_shortcode( 'child_cutoff_year', 'child_cutoff_year_func' );


// regular prices table
// [prices_regular]
function prices_regular_func ( $atts ){
	$price = intval (get_regular_price ());
	
	$html ='
	<table class="scheduleTable">
		<tbody>			
			<tr>
				<td>&nbsp;</td>
				<td><strong>Per Day</strong></td>
				<td><strong>Entire Seminar</strong></td>
			</tr>
			<tr>
				<td><strong>Adult</strong></td>
				<td>' . round ($price) . ' EURO</td>
				<td>' . round ($price * 5.5) . ' EURO</td>
			</tr>
			<tr>
				<td><strong>Student</strong></td>
				<td>' . round ($price * 0.75) . ' EURO</td>
				<td>' . round (round ($price * 0.75) * 5.5) . ' EURO</td>
			</tr>
			<tr>
				<td><strong>Children</strong></td>
				<td>' . round ($price * 0.25) . ' EURO</td>
				<td>' . round (round ($price * 0.25) * 5.5) . ' EURO</td>
			</tr>
		</tbody>
	</table>';
	
	return $html;
}
add_shortcode( 'prices_regular', 'prices_regular_func' );


// late prices table
// [prices_late]
function prices_late_func ( $atts ){
	$price = get_regular_price();
	
	$html = '
	<table class="scheduleTable">
		<tbody>			
			<tr>
				<td>&nbsp;</td>
				<td><strong>Per Day</strong></td>
				<td><strong>Entire Seminar</strong></td>
			</tr>
			<tr>
				<td><strong>Adult</strong></td>
				<td>' . round ($price * 1.25) . ' EURO</td>
				<td>' . round ($price * 1.25 * 5.5) . ' EURO</td>
			</tr>
			<tr>
				<td><strong>Student</strong></td>
				<td>' . round (round($price * 0.75) * 1.25) . ' EURO</td>
				<td>' . round (round($price * 0.75) * 1.25 * 5.5) . ' EURO</td>
			</tr>
			<tr>
				<td><strong>Children</strong></td>
				<td>' . round (round($price * 0.25) * 1.25) . ' EURO</td>
				<td>' . round (round($price * 0.25) * 1.25 * 5.5) . ' EURO</td>
			</tr>
		</tbody>
	</table>';
	
	return $html;
	
}
add_shortcode( 'prices_late', 'prices_late_func' );


// onsite prices table
// [prices_onsite]
function prices_onsite_func ( $atts ){
	$price = get_regular_price();
	$html = '
	<table class="scheduleTable">
		<tbody>			
			<tr>
				<td>&nbsp;</td>
				<td><strong>Per Day</strong></td>
				<td><strong>Entire Seminar</strong></td>
			</tr>
			<tr>
				<td><strong>Adult</strong></td>
				<td>' . round ($price * 1.37) . ' EURO</td>
				<td>' . round ($price * 1.37 * 5.5) . ' EURO</td>
			</tr>
			<tr>
				<td><strong>Student</strong></td>
				<td>' . round (round($price * 0.75) * 1.37) . ' EURO</td>
				<td>' . round (round($price * 0.75) * 1.37 * 5.5) . ' EURO</td>
			</tr>
			<tr>
				<td><strong>Children</strong></td>
				<td>' . round (round($price * 0.25) * 1.37) . ' EURO</td>
				<td>' . round (round($price * 0.25) * 1.37 * 5.5) . ' EURO</td>
			</tr>
		</tbody>
	</table>';
	
	return $html;
}
add_shortcode( 'prices_onsite', 'prices_onsite_func' );




// daily schedule output
// [daily_schedule_table]
function daily_schedule_table_func ( $atts ){
	$html = '<table><tbody>';
	
	$args = array (
			'post_type' 	=> 'slots',
			'posts_per_page' => -1,
			'post_status' 	=> 'publish',
			'orderby' 		=> 'menu_order',
			'order' 		=> 'ASC'
			);
	
	$slots = get_posts ($args);
	
	// print slots names in a table row
	$html .= '<tr><th>&nbsp;</th>';
	foreach ($slots as $slot) {
		$html .= '<th>' . get_clean_slot_name ($slot->post_title) . '</th>';
	}	
	$html .= '</tr>';
	
	// print slots times in a table row
	$html .= '<tr><th>Class</th>';
	foreach ($slots as $slot) {
		$slot_time = get_field ('time_period', $slot->ID);
		$html .= '<td class="header">' . $slot_time . '</td>';
	}
	
	$html .= '</tr>';
	
	// get the specific classes and all their slots
	$slots_main_array = get_field ('daily_slots', 11);		// Schedule page is post id 11
	
	if ($slots_main_array) {
	
		foreach ($slots_main_array as $main_slot) {
		
			$class_name = $main_slot ['class_slot'];
		
			$class_slots = $main_slot ['time_slot_repeater'];
		
			$class_data = array ();
			
			
			foreach ($class_slots as $slot) {
				$slot_title = $slot ['time_slot'];
					
				//$slot_post = getPostByTitle ('slots', $slot_title);
					
				$class_data [$slot_title] = $slot ['mark'];
					
			}
			
			// print slots times for this class in a table row
			$html .= '<tr><th class="left">' . $class_name . '</th>';
			foreach ($slots as $slot) {
				$slot_title = $slot->post_title;
				
				if (array_key_exists ($slot->post_title, $class_data)) {
					$html .= '<td>' . $class_data [$slot->post_title] . '</td>';
				}
				else {
					$html .= '<td>&nbsp;</td>';
				}
				
			}
			
			$html .= '</tr>';
		
		
		}
		
		
	}
	
	
	
	$html .= '</tbody></table>';
	
	return $html;
}
add_shortcode( 'daily_schedule_table', 'daily_schedule_table_func' );


// soubd bites on Program page
// [sound_bites]
function sound_bites_func ( $atts ){
	
	$html = '<div class="sound-bites-wrapper">';
	
	$args = array (
		'post_type' 	=> 'classes',
		'posts_per_page' => -1,
		'post_status' 	=> 'publish',
		'orderby' 		=> 'menu_order',
		'order' 		=> 'ASC'
	);
	
	$classes = get_posts ($args);
	
	// generate links
	foreach ($classes as $index=>$class) {
		$class_id = $class->ID;
		$class_name = $class->post_title;
		$thumbnail = get_attachment_image_url (get_post_thumbnail_id( $class_id ), 'full');
		$sound_bites = get_field ('sound_bites', $class_id);
		$sound_bites_courtesy = get_field ('sound_bites_courtsey', $class_id);
	
		$exclude = array ('Bulgarian Language', 'Dance', 'Singing');
	
		if (!in_array ($class_name, $exclude)) {
			$html .= '<a href="#sound-bites-item-' . $index . '" class="sound-bites-item fancybox" rel="sound-bites-group" style="background-image: url(\'' . $thumbnail . '\')">';
			$html .= '<div class="hover-overlay trigger-fancybox"></div>';
			$html .= '<div class="bottom"><h2><span class="header-background" style="background-image: url(\'' . $thumbnail . '\')"></span><span class="header">' . $class_name . '</span></h2></div>';							

			$html .= '</a>';
		}
	
	}
	
	// generate divs for fancybox
	foreach ($classes as $index=>$class) {
		$class_id = $class->ID;
		$class_name = $class->post_title;
		$thumbnail = get_attachment_image_url (get_post_thumbnail_id( $class_id ), 'full');
		$sound_bites = get_field ('sound_bites', $class_id);
		$sound_bites_courtesy = get_field ('sound_bites_courtsey', $class_id);
		
		$exclude = array ('Bulgarian Language', 'Dance', 'Singing');
		
		if (!in_array ($class_name, $exclude)) {
			$html .= '<div id="sound-bites-item-' . $index . '" class="sound-bites-item-content" style="background-image: url(\'' . $thumbnail . '\')">';
			$html .= '<div class="left" style="background-image: url(\'' . $thumbnail . '\')"></div>';
			$html .= '<div class="bottom"><h2><span class="header-background" style="background-image: url(\'' . $thumbnail . '\')"></span><span class="header">' . $class_name . '</span></h2>';
			
			$html .= '
			<div class="media-wrapper">
				<div class="audio-wrapper">';
				if ($sound_bites) {
					$html .= '<audio src="' . $sound_bites .'" preload="auto" />';
				}
				else {
					$html .= 'No audio available';
				}
				
				$html .= '</div>
			</div>';
				
			if ($sound_bites_courtesy) {
			
				$html .= '<p>Sound bites courtesy of: ' . $sound_bites_courtesy . '</p>';
			
			}
			$html .= '</div>';
			
			$html .= '</div>';
		}

	}
	
	
	$html .= '</div>';
	return $html;
	
}
add_shortcode( 'sound_bites', 'sound_bites_func' );



// gala dinner fee
// [gala_dinner_fee]
function gala_dinner_fee_func ( $atts ){
	return get_gala_dinner_fee();
}
add_shortcode( 'gala_dinner_fee', 'gala_dinner_fee_func' );


/////////////////////////////////
// Functions
/////////////////////////////////

function get_seminar_year () {
	return date ('Y', strtotime (get_field ('seminar_start_date', 'option')));
}

function get_red_note () {
	if (get_field ('show_red_note', 'option')) return '<div class="red">' . get_field ('red_note', 'option') . '</div>';
}

function get_late_registration_deadline () {
	return get_field ('late_registration_deadline', 'option');
}

function get_late_registration_fee_percentage () {
	return get_field ('late_registration_fee_percentage', 'option');
}

function get_regular_price () {
	return get_field ('price', 'option');
}

function get_reg_closed_date () {
	return get_field ('registration_closed_date', 'option');
}

function get_eefc_discount () {
	return get_field ('eefc_percent', 'option');
}

function get_start_date () {
	return get_field ('seminar_start_date', 'option');
}

function get_end_date () {
	return get_field ('seminar_end_date', 'option');
}

function get_of_age_year () {
	return intval (date ('Y', strtotime(get_end_date())) - 17);
}

function getPostByTitle ($type, $name) {
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

function get_clean_slot_name ($name) {
	switch ($name) {
		case 'Session 1 (am)':		
		case 'Session 1 (pm)':
			return 'Session 1';
		case 'Session 2 (am)':
		case 'Session 2 (pm)':
			return 'Session 2';
		default:
			return $name;
	}
}

/**
 * Get Attachment Image URL
 * @desc A companion to wp_get_attachment_image_src which returns only the URL
 * @see wp_get_attachment_image_src
 *
 * @param int $attachment_id
 * @param string $size
 * @param bool $icon
 * @return string|false
 */
function get_attachment_image_url ( $attachment_id, $size = 'thumbnail', $icon = false ) {
	$src = wp_get_attachment_image_src($attachment_id, $size, $icon);

	if ( $src ) {
		return $src[0];
	} else {
		return false;
	}
}

function getCorrectClassName ($name) {
	global $INSTRUMENT_NAMES;
	
	if (array_key_exists (sanitize_title ($name), $INSTRUMENT_NAMES)) {
		return $INSTRUMENT_NAMES [sanitize_title ($name)];
	}
	
	return $name;
}

function get_year_order () {
	
	$current_year = date("Y", strtotime (get_end_date()));
	$start = 2005;
	
	$diff = $current_year - $start;
	return ($diff + 1) . get_ordinal_ending ($diff);
}

function get_ordinal_ending ($num) {
	
	if ($num > 9 && $num < 20) {
		return 'th';
	}
	
	switch ($num % 10) {
		case (1):
			return 'st';
		case (2):
			return 'nd';
		case (3):
			return 'rd';
		default:
			return 'th';
	}
}

// dance groups have From and To; this returns date in "August 5-7, 2014" format
function get_dancegroup_date ($event_id, $show_times = false) {

	$from = get_field ('from_date', $event_id);
	$to = get_field ('to_date', $event_id);

	if (!$to || $from == $to) {

		$date = date ('F j, Y', strtotime ($from));
		
		return $date;
	}

	else {
		$from_day = date ('j', strtotime ($from));
		$to_day = date ('j', strtotime ($to));

		$from_month = date ('F', strtotime ($from));
		$to_month = date ('F', strtotime ($to));

		$from_year = date ('Y', strtotime ($from));
		$to_year = date ('Y', strtotime ($to));

		if ($from_month != $to_month) {
				
				
			$from_date = date ('M j', strtotime ($from));
			$to_date = date ('M j', strtotime ($to));
				
			$date .= $from_date;
							
			$date .= ' &#8211; ' . $to_date;
							
		}
		else {
				
			$from_date = date ('M j', strtotime ($from));
			$to_date = date ('j', strtotime ($to));
				
			$date .= $from_date;
				
			
			$date .= ' &#8211; ' . $to_date;			
		}

		$date .= ', ' . $to_year;
		
		return $date;
	}
}

// fee for gala dinner
// if this is not populated, gala dinner will not be charged for
// gala dinner is free for registrants who will attend for the duration
function get_gala_dinner_fee () {
	return get_field ('gala_price', 'option');
}

/**
 * Trim Text
 *
 * @param string $text Text to trim
 * @param int|null|false $limit Limit the character count (null|false for unlimited)
 * @param string $more Text to append to trimmed text
 * @return string
 */
function trim_text ( $text, $limit = 200, $more = '...' ) {

	// Trim text
	$text = trim(strip_tags($text));

	// Enforce limit
	if ( empty($limit) || strlen($text) <= $limit ) {
		return $text;
	}

	$last_space = strrpos(substr($text, 0, $limit), ' ');
	$trimmed_text = substr($text, 0, $last_space);

	// Add more (...)
	$trimmed_text .= $more;

	return $trimmed_text;
}

// returns all rows that belong to a registration
function getRegistrationRows ($reg_id) {
	global $wpdb;
	
	$table = 'wp_Seminar_registrations';
	
	$sql = "SELECT *
			FROM $table
			WHERE reg_id = '$reg_id'
			ORDER BY reg_slot ASC";
	
	$results = $wpdb->get_results ($sql);
	
	return $results;
}

// returns all rows from Classes table for that reg_id
function getClassesRows ($reg_id) {
	global $wpdb;
	
	$table = 'wp_Seminar_classes';
	
	$sql = "SELECT *
			FROM $table
			WHERE reg_id = '$reg_id'
			ORDER BY reg_slot ASC";
	
	$results = $wpdb->get_results ($sql);
	
	return $results;
}

function get_balance_individual ($num_days, $gala, $age, $eefc, $payment, $dvd, $transport) {

	$date = date("Y-m-d");
	$reg_deadline = strtotime (get_late_registration_deadline ());
	$balance = 0;
	
	$price = intval (get_regular_price ());
	
	$today = strtotime($date);

	if ($payment == "on-site") {
		if ($age == "child") {
			if ($num_days == 6) $balance = round (round($price * 0.25) * 1.37 * 5.5);
			else $balance = round (round($price * 0.25) * 1.37) * $num_days;
		}
		else if ($age == "student") {
			if ($num_days == 6) $balance = round (round($price * 0.75) * 1.37 * 5.5);
			else $balance = round (round($price * 0.75) * 1.37) * $num_days;
		}
		else if ($age == "adult") {
			if ($num_days == 6) $balance = round ($price * 1.37 * 5.5);
			else $balance = round ($price * 1.37) * $num_days;
		}
		if ($eefc) {
			$eefc_discount = $balance - ($balance * 0.85);
			$balance = $balance - $eefc_discount;
		}
	}
	else {
		if ($age == "child") {
			if ($num_days == 6) $balance = round (round ($price * 0.25) * 5.5);
			else $balance = round ($price * 0.25) * $num_days;
		}
		else if ($age == "student") {
			if ($num_days == 6) $balance = round (round ($price * 0.75) * 5.5);
			else $balance = round ($price * 0.75) * $num_days;
		}
		else if ($age == "adult") {
			if ($num_days == 6) $balance = round ($price * 5.5);
			else $balance = round ($price) * $num_days;
		}

		if ($reg_deadline < $today) {
			$late_fee = number_format($balance*get_late_registration_fee_percentage ()/100, 2);
			$balance = round($balance + $late_fee);
		}
		
		if ($eefc) {
			$balance =  round($balance * (100 - get_eefc_discount())/100);
		}
	}
	
	// for transportation and DVD processing we need the Register page object
	// because those fields are set on there
	$register_page_obj = getPostByTitle ('page', 'Register');
	if ($dvd) {
		$balance += get_field ('dvd_price', $register_page_obj->ID);
	}
	
	if ($transport > 0) {
		$balance += $transport * get_field ('koprivshtitsa_transportation_fee', $register_page_obj->ID);
	}
	
	if ($gala) {
		$balance += get_gala_dinner_fee(); 
	}

	return $balance;
	
}

function get_balance ($reg_id) {
	global $wpdb;
	
	$table = 'wp_Seminar_registrations';
	$balance = 0;
	
	$sql = "SELECT *
			FROM $table
			WHERE reg_id = '$reg_id'
			AND cancel <> 1";
	
	$results = $wpdb->get_results ($sql);
	
	if (!empty($results)) {
		
		foreach ($results as $row) {
			$num_days = $row->num_days;
			$age = $row->age;
			$eefc = $row->is_eefc;
			$payment = $row->payment;
			
			$balance += $row->balance;
			
		}
		
		return number_format ($balance, 2);
	}
	
	return number_format ($balance, 2);
}

function cancelRegistration ($reg_id) {
	global $wpdb;
	
	$table = 'wp_Seminar_registrations';
	
	$sql = "UPDATE $table
			SET cancel = 1
			WHERE reg_id = '$reg_id'";
	
	$wpdb->query(
			$sql
	);
}

function sendRegisterEmail (&$registration) {
	
	$reg_id = $registration [0]->reg_id;
	$primary = $registration[0];
	
	$classes = getClassesRows ($reg_id);
	
	// send email
	$email = $primary->email;
	$message = "Seminar Registration #" . $primary->id . "\r\n" .
			"Email Address: " . $primary->email . "\r\n" .
			"Total: EURO " . get_balance ($reg_id) . "\r\n" .
			"Payment: $primary->payment " . "\r\n\r\n";
	
	if ($primary->payment == "on-site") {
		$message .= "You have indicated that you will be submitting payment On-site (at the Music Academy). Please bring a copy of your registration for verification purposes.\r\n\r\n";
	}
	else {
		$message .= "You have indicated that you will be paying by Bank Transfer. Please provide your bank with the following routing information for your Bank Transfer:\r\n\r\n";
			
		$message .= "Bank Name:	UNICREDIT BULBANK\r\n";
		$message .= "Bank Address:	31 Ivan Vazov Str., Plovdiv 4000, Bulgaria\r\n";
		$message .= "Bank SWIFT code:	UNCRBGSF\r\n";
		$message .= "Account Name:	Academy of Music Dance and Fine Arts Plovdiv\r\n";
		$message .= "Account Address:	2 Todor Samodumov Str., Plovdiv 4000, Bulgaria\r\n";
		$message .= "Account Number (in EURO):	3498641407\r\n";
		$message .= "IBAN Number (in EURO):	BG56UNCR75273498641407\r\n";
		//$message .= "Account Number (in US Dollars):	3454632602\r\n";
		//$message .= "IBAN Number (in US Dollars):	BG13UNCR75273454632602\r\n";
		$message .= "\r\n";
	
		$message .= "Bank Transfers should be initiated before " . date ('F j, Y', strtotime(get_late_registration_deadline ())) . " to qualify for the fee calculated above.\r\n";
		$message .= "When you have completed your Bank Transfer, please email us at contact@folkseminarplovdiv.net  to indicate the date of your Bank Transfer.  This will assist Seminar administrative staff with tracking your payment.  If the Bank Transfer is originating from a bank account that is not in the registrant's name, or funds are being transferred for more than one registrant, please include the name of the person who owns the account, and all registrant names associated with your Bank Transfer.\r\n\r\n";
	
	
	}
	$message .= "Thank you and see you in Plovdiv!\r\nLarry Weiner & Dilyana Kurdova\r\nInternational Program Coordinators";
	
	$message.= "\r\n\r\n";
	
	
	foreach ($registration as $index=>$row) {
		$message .= "\r\n*** REGISTRANT ".$row->reg_slot." ***\r\n----------------------------------\r\n";
		$message .= "Name: $row->first_name $row->last_name" . "\r\n";
		
		if ($index == 0) {
			$message .= "Address: $row->address1";
			if ($row->address2 != '') $message .= ', ' . $row->address2;
			$message .= "\r\n" . "City: $row->city, $row->state, $row->zip" . "\r\n";
			$message .= "Country: $row->country" . "\r\n";
			$message .= "Phone: $row->phone" . "\r\n";
			$message .= "Email: $row->email" . "\r\n";
			if ($row->emergency) {
				$message .= "Emergency Info: $row->emergency" . "\r\n";
			}
		}
		
		// classes this person has selected:
		if (count($classes)) {
			$message .=  "\r\n\r\n" . 'CLASSES:' . "\r\n\r\n";
			foreach ($classes as $class_row) {
				if ($class_row->reg_slot == ($index + 1)) {
					$class_id = $class_row->class_id;
					$levels = get_field ('class_levels', $class_id);
					$rent_options = get_field ('rent_bring', $class_id);
						
					$class_obj = get_post ($class_id);
					
					$message .= $class_obj->post_title;
					
					if ($rent_options || $levels) {
						$message .=  ': ';
						
						if ($rent_options) {
							if (isset ($class_row->rent) && $class_row->rent != NULL && $class_row->rent != '') {
								$rent = $class_row->rent == 1 ? 'would like to rent' : 'bringing my own';
							}
							if (isset ($rent)) {
								$message .= $rent;
							}
						}
						
						if ($levels) {
							if (isset ($class_row->level) && $class_row->level != NULL && $class_row->level != '') {
								$level = $class_row->level;
							}
							if (isset ($level)) {
								if ($rent_options && isset ($rent)) {
									$message .= ',';
								}
								$message .= ' ' . $level;
							}
						}
					}

					$message .= "\r\n";
				}
				
			}
			$message .= "\r\n";
		}

		if ($row->dvd == 1) {
			$message .= "DVD ordered: Yes, " . strtoupper ($row->dvd_format) ."\r\n";
		}		
		
		$transport = $row->transport;
		if ($transport != -1) {
			if ($transport == 0) {
				$message .= "Transport to Koprivshtitsa: No" ."\r\n";
			}
			else if ($transport == 1) {
				$message .= "Transport to Koprivshtitsa: One-way" ."\r\n";
			}
			else if ($transport == 2) {
				$message .= "Transport to Koprivshtitsa: Round trip" ."\r\n";
			}
		}
		
		$message .= "Days attending: $row->num_days" ."\r\n";
		$message .= "Registration type: $row->age" . "\r\n";
		
		$eefc = $row->is_eefc == 1 ? 'Yes' : 'No';
		$message .= "EEFC member: $eefc" . "\r\n";
		
		if ($index == 0) {
			$message .= "Payment: $primary->payment" . "\r\n";
		}
		$message .= "Balance: EURO $row->balance" . "\r\n";
	
		
	}
	
	
	$headers [] = 'From: ' . get_bloginfo('name') . ' ' . get_seminar_year () . ' <' . get_bloginfo('admin_email') . '>';
	$headers [] = 'Reply-To: ' . get_bloginfo('admin_email');
	$headers [] = 'Cc: ' . get_bloginfo('admin_email');
	
	wp_mail(
		$email,
		'Folk Seminar Plovdiv | Registration #' . $primary->id,
		$message,
		$headers
	);
}

// this executes when a user clicks Confirm
// it sets the "confirmed" table column to 1 for that registration id
function confirmRegistration ($reg_id) {
	global $wpdb;
	
	$table = 'wp_Seminar_registrations';
	
	$sql = "UPDATE $table
			SET confirmed = 1
			WHERE reg_id = '$reg_id'";
	
	$wpdb->query(
			$sql
	);
}

// generates a random string of length passed in
function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}