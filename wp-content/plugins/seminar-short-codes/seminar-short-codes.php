<?php
/**
* Plugin Name: Seminar Shortcodes
* Description: Site-wide shortcode definitions
* Version: 1.0
* Author: Tzvety Dosseva
*/

/** SHORT CODES */

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
    $html = '<table class="scheduleTable"><tbody>';

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
                $slot_name = esc_attr(get_clean_slot_name($slot->post_title));
                if (array_key_exists($slot->post_title, $class_data)) {
                    $html .= '<td data-label="' . $slot_name . '">'
                        . $class_data[$slot->post_title] . '</td>';
                } else {
                    $html .= '<td data-label="' . $slot_name . '">&nbsp;</td>';
                }

            }

            $html .= '</tr>';
        }
    }
    $html .= '</tbody></table>';

    // Mobile responsive version
    $html .= '<div class="scheduleTableResponsive">';
    
    if ($slots_main_array) {
        foreach ($slots_main_array as $main_slot) {
            $class_name = $main_slot['class_slot'];
            $class_slots = $main_slot['time_slot_repeater'];
            
            $html .= '<div class="class-schedule">';
            $html .= '<h3>' . esc_html($class_name) . '</h3>';
            
            foreach ($class_slots as $slot) {
                $slot_title = $slot['time_slot'];
                $mark = $slot['mark'];
                
                if (!empty($mark) && trim($mark) !== '&nbsp;') {
                    $clean_slot_name = get_clean_slot_name($slot_title);
                    $html .= '<div class="session-item">' . esc_html($clean_slot_name . ' ' . $mark) . '</div>';
                }
            }
            
            $html .= '</div>';
        }
    }
    
    // Add special slots with times
    foreach ($slots as $slot) {
        if ($slot->post_title === 'Daily Student Gathering' || $slot->post_title === 'Lunch Break') {
            $slot_time = get_field('time_period', $slot->ID);
            $html .= '<div class="special-slot">' . esc_html($slot->post_title . ': ' . $slot_time) . '</div>';
        }
    }
    
    $html .= '</div>';

    return $html;
}
add_shortcode( 'daily_schedule_table', 'daily_schedule_table_func' );

// sound bites on Program page
// [sound_bites]
//function sound_bites_func ( $atts ){
//
//    $html = '<div class="sound-bites-wrapper">';
//
//    $args = array (
//        'post_type' 	=> 'classes',
//        'posts_per_page' => -1,
//        'post_status' 	=> 'publish',
//        'orderby' 		=> 'menu_order',
//        'order' 		=> 'ASC'
//    );
//
//    $classes = get_posts ($args);
//
//    // generate links
//    foreach ($classes as $index=>$class) {
//        $class_id = $class->ID;
//        $class_name = $class->post_title;
//        $thumbnail = get_attachment_image_url (get_post_thumbnail_id( $class_id ), 'full');
//        $sound_bites = get_field ('sound_bites', $class_id);
//        $sound_bites_courtesy = get_field ('sound_bites_courtsey', $class_id);
//
//        $exclude = array ('Bulgarian Language', 'Bulgarian Folk Dance', 'Bulgarian Singing', 'Bulgarian Folk Singing', 'Bulgarian Choral Singing', 'Singing' , 'Dance');
//
//        if (!in_array ($class_name, $exclude)) {
//            $html .= '<a href="#sound-bites-item-' . $index . '" class="sound-bites-item fancybox" rel="sound-bites-group" style="background-image: url(\'' . $thumbnail . '\')">';
//            $html .= '<div class="hover-overlay trigger-fancybox"></div>';
//            $html .= '<div class="bottom"><h2><span class="header-background" style="background-image: url(\'' . $thumbnail . '\')"></span><span class="header">' . $class_name . '</span></h2></div>';
//
//            $html .= '</a>';
//        }
//
//    }
//
//    // generate divs for fancybox
//    foreach ($classes as $index=>$class) {
//        $class_id = $class->ID;
//        $class_name = $class->post_title;
//        $thumbnail = get_attachment_image_url (get_post_thumbnail_id( $class_id ), 'full');
//        $sound_bites = get_field ('sound_bites', $class_id);
//        $sound_bites_courtesy = get_field ('sound_bites_courtsey', $class_id);
//
//        $exclude = array ('Bulgarian Language', 'Dance', 'Singing');
//
//        if (!in_array ($class_name, $exclude)) {
//            $html .= '<div id="sound-bites-item-' . $index . '" class="sound-bites-item-content" style="background-image: url(\'' . $thumbnail . '\')">';
//            $html .= '<div class="left" style="background-image: url(\'' . $thumbnail . '\')"></div>';
//            $html .= '<div class="bottom"><h2><span class="header-background" style="background-image: url(\'' . $thumbnail . '\')"></span><span class="header">' . $class_name . '</span></h2>';
//
//            $html .= '
//			<div class="media-wrapper">
//				<div class="audio-wrapper">';
//            if ($sound_bites) {
//                $html .= '<audio src="' . $sound_bites .'" preload="auto" />';
//            }
//            else {
//                $html .= 'No audio available';
//            }
//
//            $html .= '</div>
//			</div>';
//
//            if ($sound_bites_courtesy) {
//
//                $html .= '<p>Sound bites courtesy of: ' . $sound_bites_courtesy . '</p>';
//
//            }
//            $html .= '</div>';
//
//            $html .= '</div>';
//        }
//
//    }
//
//
//    $html .= '</div>';
//    return $html;
//
//}
//add_shortcode( 'sound_bites', 'sound_bites_func' );

// gala dinner fee
// [gala_dinner_fee]
function gala_dinner_fee_func ( $atts ){
    return get_gala_dinner_fee();
}
add_shortcode( 'gala_dinner_fee', 'gala_dinner_fee_func' );

// Shortcode helper functions
function get_regular_price () {
    return get_field ('price', 'option');
}

function get_of_age_year () {
    return intval (date ('Y', strtotime(get_end_date())) - 17);
}

// fee for gala dinner
// if this is not populated, gala dinner will not be charged for
// gala dinner is free for registrants who will attend for the duration
function get_gala_dinner_fee () {
    return get_field ('gala_price', 'option');
}

// this is legacy from when the slot names were looking like this switch
function get_clean_slot_name ($name) {
    switch ($name) {
        case 'Session 1 (am)':
            return 'Session 1';
        case 'Session 2 (am)':
            return 'Session 2';
        case 'Session 1 (pm)':
            return 'Session 3';
        case 'Session 2 (pm)':
            return 'Session 4';
        case 'Bulgarian Language':
            return 'Session 5';
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
    return $src ? $src[0] : false;
}

/** END SHORT CODES */
