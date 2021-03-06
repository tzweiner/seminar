<?php 
if(!isset($saving))
    header("Content-type: text/css");

if (isset($_GET['frm_weight'])){
    $important_style = isset($_GET['frm_important_style']) ? $_GET['frm_important_style'] : 0;
    
    $form_width = $_GET['frm_form_width'];
    $form_align = $_GET['frm_form_align'];
    $fieldset = $_GET['frm_fieldset'];
    $fieldset_color = $_GET['frm_fieldset_color'];
    $fieldset_padding = $_GET['frm_fieldset_padding'];
    
    $font = stripslashes($_GET['frm_font']);
    $font_size = $_GET['frm_font_size'];
    $label_color = $_GET['frm_label_color'];
    $weight = $_GET['frm_weight']; 
    $position = $_GET['frm_position'];
    $align = $_GET['frm_align'];
    $width = $_GET['frm_width'];
    $required_color = $_GET['frm_required_color'];
    $required_weight = $_GET['frm_required_weight'];
    
    $description_font = stripslashes($_GET['frm_description_font']);
    $description_font_size = $_GET['frm_description_font_size'];
    $description_color = $_GET['frm_description_color'];
    $description_weight = $_GET['frm_description_weight'];
    $description_style = $_GET['frm_description_style']; 
    $description_align = $_GET['frm_description_align'];
    
    $field_font_size = $_GET['frm_field_font_size'];
	$field_height = $_GET['frm_field_height'];
	$line_height = ($field_height == '' || $field_height == 'auto') ? 'normal' : $field_height;
    $field_width = $_GET['frm_field_width'];
    $auto_width = isset($_GET['frm_auto_width']) ? $_GET['frm_auto_width'] : 0;
    $field_pad = $_GET['frm_field_pad'];
    $field_margin = $_GET['frm_field_margin'];
    
    $text_color = $_GET['frm_text_color'];
    $bg_color = $_GET['frm_bg_color'];
    $border_color = $_GET['frm_border_color'];
    $field_border_width = $_GET['frm_field_border_width'];
    $field_border_style = $_GET['frm_field_border_style'];
    
    //$bg_color_hv = $_GET['frm_bg_color_hv'];
    //$border_color_hv = $_GET['frm_border_color_hv'];
    
    $bg_color_active = $_GET['frm_bg_color_active'];
    $border_color_active = $_GET['frm_border_color_active'];
    
    $text_color_error = $_GET['frm_text_color_error'];
    $bg_color_error = $_GET['frm_bg_color_error'];
    $border_color_error = $_GET['frm_border_color_error'];
    $border_width_error = $_GET['frm_border_width_error'];
    $border_style_error = $_GET['frm_border_style_error'];
    
    $radio_align = $_GET['frm_radio_align'];
    $check_align = $_GET['frm_check_align'];
    $check_font = stripslashes($_GET['frm_check_font']);
    $check_font_size = $_GET['frm_check_font_size'];
    $check_label_color = $_GET['frm_check_label_color'];
    $check_weight = $_GET['frm_check_weight'];
    
    $submit_style = isset($_GET['frm_submit_style']) ? $_GET['frm_submit_style'] : 0;
    $submit_font_size = $_GET['frm_submit_font_size'];
    $submit_width = $_GET['frm_submit_width'];
    $submit_height = $_GET['frm_submit_height'];
    $submit_bg_color = $_GET['frm_submit_bg_color'];
    $submit_bg_color2 = $_GET['frm_submit_bg_color2'];
    $submit_bg_img = $_GET['frm_submit_bg_img'];
    $submit_border_color = $_GET['frm_submit_border_color'];
    $submit_border_width = $_GET['frm_submit_border_width'];
    $submit_text_color = $_GET['frm_submit_text_color'];
    $submit_weight = $_GET['frm_submit_weight'];
    $submit_border_radius = $_GET['frm_submit_border_radius'];
    $submit_margin = $_GET['frm_submit_margin'];
    $submit_padding = $_GET['frm_submit_padding'] . ($important_style ? '' : ' !important');
    $submit_shadow_color = $_GET['frm_submit_shadow_color'];
    
    $border_radius = $_GET['frm_border_radius'];
    
    $error_bg = $_GET['frm_error_bg'];
    $error_border = $_GET['frm_error_border'];
    $error_text = $_GET['frm_error_text'];
    $error_font_size = $_GET['frm_error_font_size'];
    
    $success_bg_color = $_GET['frm_success_bg_color'];
    $success_border_color = $_GET['frm_success_border_color'];
    $success_text_color = $_GET['frm_success_text_color'];
    $success_font_size = $_GET['frm_success_font_size'];
    
    $custom_css = $_GET['frm_custom_css'];
}else{    
    if(isset($use_saved) and $use_saved){
        $css = get_transient('frmpro_css');
        if($css){
            echo $css;
            die();
        }
    }

    global $frmpro_settings;
    extract((array)$frmpro_settings);
}
$important = empty($important_style) ? '' : ' !important';
$label_margin = (int)$width + 15; ?>
.frm_forms.with_frm_style{max-width:<?php echo $form_width . $important ?>;}
.with_frm_style, .with_frm_style form{text-align:<?php echo $form_align . $important ?>;}
.with_frm_style fieldset{border:<?php echo $fieldset ?> solid #<?php echo $fieldset_color . $important ?>;margin:0;padding:<?php echo $fieldset_padding . $important ?>;}
.with_frm_style label.frm_primary_label, .with_frm_style.frm_login_form label{font-family:<?php echo stripslashes($font) ?>;font-size:<?php echo $font_size . $important ?>;color:#<?php echo $label_color . $important ?>;font-weight:<?php echo $weight . $important ?>;text-align:<?php echo $align . $important ?>;margin:0;padding:0;width:auto;display:block;}
.with_frm_style .form-field{margin-bottom:<?php echo $field_margin. $important ?>;}
.with_frm_style .form-field.frm_col_field{clear:none;float:left;margin-right:20px;}
.with_frm_style p.description, .with_frm_style div.description, .with_frm_style div.frm_description, .with_frm_style .frm_error{margin:0;padding:0;font-family:<?php echo stripslashes($description_font) . $important ?>;font-size:<?php echo
$description_font_size . $important ?>;color:#<?php echo $description_color . $important ?>;font-weight:<?php echo $description_weight . $important ?>;text-align:<?php echo $description_align . $important ?>;font-style:<?php echo $description_style . $important ?>;max-width:100%;}
.with_frm_style .frm_left_container p.description, .with_frm_style .frm_left_container div.description, .with_frm_style .frm_left_container div.frm_description, .with_frm_style .frm_left_container .frm_error{margin-left:<?php echo $label_margin ?>px;}
.with_frm_style .form-field.frm_col_field div.frm_description{width:<?php echo ($field_width == '' ? 'auto' : $field_width)  . $important ?>;max-width:100%;}
.with_frm_style .frm_left_container .attachment-thumbnail{clear:both;margin-left:<?php echo $label_margin ?>px<?php echo $important ?>;}
.with_frm_style .frm_right_container p.description, .with_frm_style .frm_right_container div.description, .with_frm_style .frm_right_container div.frm_description, .with_frm_style .frm_right_container .frm_error{margin-right:<?php echo $label_margin ?>px<?php echo $important ?>;}
.with_frm_style label.frm_primary_label{max-width:100%;}
.with_frm_style .frm_top_container label.frm_primary_label, .with_frm_style .frm_hidden_container label.frm_primary_label, .with_frm_style .frm_pos_top{display:block;float:none;width:auto;}
.with_frm_style .frm_inline_container label.frm_primary_label, .with_frm_style .frm_inline_container .frm_opt_container {display:inline<?php echo $important ?>;margin-right:10px;}
.with_frm_style .frm_left_container label.frm_primary_label{display:inline<?php echo $important ?>;float:left;margin-right:10px;width:<?php echo $width; ?>;}
.with_frm_style .frm_right_container label.frm_primary_label, .with_frm_style .frm_pos_right{display:inline<?php echo $important ?>;float:right;margin-left:10px;width:<?php echo $width . $important; ?>;}
.with_frm_style .frm_none_container label.frm_primary_label, .with_frm_style .frm_pos_none{display:none<?php echo $important ?>;}
.with_frm_style .frm_hidden_container label.frm_primary_label, .with_frm_style .frm_pos_hidden{visibility:hidden;}
.with_frm_style .frm_scale{margin-right:10px;text-align:center;float:left;}
.with_frm_style .frm_scale input{display:block;margin-bottom:5px;}
.with_frm_style .frm_scale label{font-weight:<?php echo $check_weight . $important ?>;}
.with_frm_style .frm_required{color:#<?php echo $required_color . $important; ?>;font-weight:<?php echo $required_weight . $important; ?>;}
.with_frm_style input[type=text], .with_frm_style input[type=password], .with_frm_style input[type=email], .with_frm_style input[type=number], .with_frm_style input[type=url], .with_frm_style input[type=tel], .with_frm_style select, .with_frm_style textarea, #content .with_frm_style input:not([type=submit]):not([type=button]), #content .with_frm_style select, #content .with_frm_style textarea, .with_frm_style .chosen-container{font-family:<?php echo stripslashes($font)  . $important ?>;font-size:<?php echo $field_font_size ?>;margin-bottom:0<?php echo $important ?>;}
.with_frm_style input[type=text], .with_frm_style input[type=password], .with_frm_style input[type=email], .with_frm_style input[type=number], .with_frm_style input[type=url], .with_frm_style input[type=tel], .with_frm_style select, .with_frm_style textarea, .frm_form_fields_style, .with_frm_style .frm_scroll_box .frm_opt_container, .frm_form_fields_active_style, .frm_form_fields_error_style, .with_frm_style .chosen-container-multi .chosen-choices, .with_frm_style .chosen-container-single .chosen-single{color:#<?php echo $text_color . $important ?>;background-color:#<?php echo $bg_color . $important;
if (!empty($important) ){
echo ';background-image:none'. $important;
} ?>;border-color:#<?php echo $border_color . $important ?>;border-width:<?php echo $field_border_width . $important ?>;border-style:<?php echo $field_border_style . $important ?>;-moz-border-radius:<?php echo $border_radius . $important ?>;-webkit-border-radius:<?php echo $border_radius . $important ?>;border-radius:<?php echo $border_radius . $important ?>;width:<?php echo ($field_width == '' ? 'auto' : $field_width) . $important ?>;max-width:100%;font-size:<?php echo $field_font_size . $important ?>;padding:<?php echo $field_pad . $important ?>;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;outline:none<?php echo $important ?>;font-weight:normal;box-shadow:0 1px 1px rgba(0, 0, 0, 0.075) inset;}
.with_frm_style input[type=text], .with_frm_style input[type=password], .with_frm_style input[type=email], .with_frm_style input[type=number], .with_frm_style input[type=url], .with_frm_style input[type=tel], .with_frm_style input[type=file], .with_frm_style select {height:<?php echo ($field_height == '' ? 'auto' : $field_height) . $important  ?>;line-height:<?php echo $line_height . $important ?>;}
.with_frm_style select[multiple="multiple"]{height:auto <?php echo $important ?>;line-height:normal <?php echo $important ?>;}
.with_frm_style input[type=file]{color:#<?php echo $text_color . $important ?>;border:none;padding:0px;line-height:normal<?php echo $important ?>;font-family:<?php echo stripslashes($font) ?>;font-size:<?php echo $field_font_size ?>;}
.with_frm_style .frm_default, .with_frm_style .placeholder, .with_frm_style .chosen-container-multi .chosen-choices li.search-field .default, .with_frm_style .chosen-container-single .chosen-default{color:#<?php echo $text_color . $important ?>;font-style:italic;}
.with_frm_style select{width:<?php echo ($auto_width ? 'auto' : $field_width) . $important ?>;max-width:100%;}
.with_frm_style input[type=radio], .with_frm_style input[type=checkbox]{width:auto;border:none;background:transparent;padding:0;}
.with_frm_style .frm_catlevel_2, .with_frm_style .frm_catlevel_3, .with_frm_style .frm_catlevel_4, .with_frm_style .frm_catlevel_5{margin-left:18px;}
.with_frm_style .wp-editor-wrap{width:<?php echo $field_width . $important ?>;max-width:100%;}
.with_frm_style .wp-editor-container{border:1px solid #e5e5e5;}
.with_frm_style .quicktags-toolbar input{font-size:12px !important;}
.with_frm_style .wp-editor-container textarea{border:none<?php echo $important ?>;}
.with_frm_style .mceIframeContainer{background-color:#<?php echo $bg_color . $important ?>;}
.with_frm_style .nicEdit-selectTxt{line-height:14px;}
.with_frm_style .nicEdit-panelContain{border-color:#<?php echo $border_color ?> !important;}
.with_frm_style .nicEdit-main{margin:0 !important;padding:4px;width:auto !important;outline:none;color:#<?php echo $text_color . $important ?>;background-color:#<?php echo $bg_color . $important ?>;border-color:#<?php echo $border_color ?> !important;border-width:1px;border-style:<?php echo $field_border_style ?>;border-top:none;}
.with_frm_style .auto_width input, .with_frm_style input.auto_width, .with_frm_style select.auto_width, .with_frm_style textarea.auto_width{width:auto<?php echo $important ?>;}
.with_frm_style input[disabled], .with_frm_style select[disabled], .with_frm_style textarea[disabled], .with_frm_style input[readonly], .with_frm_style select[readonly], .with_frm_style textarea[readonly]{opacity:.5;filter:alpha(opacity=50);}
.frm_set_select .with_frm_style select, .frm_set_select .with_frm_style select.auto_width{width:100%;}
.with_frm_style .form-field input:focus, .with_frm_style select:focus, .with_frm_style textarea:focus, .with_frm_style .frm_focus_field input[type=text], .with_frm_style .frm_focus_field input[type=password], .with_frm_style .frm_focus_field input[type=email], .with_frm_style .frm_focus_field input[type=number], .with_frm_style .frm_focus_field input[type=url], .with_frm_style .frm_focus_field input[type=tel], .frm_form_fields_active_style, .with_frm_style .chosen-container-active .chosen-choices{background-color:#<?php echo $bg_color_active . $important ?>;border-color:#<?php echo $border_color_active . $important ?>;box-shadow:0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(<?php echo FrmProFormsHelper::hex2rgb($border_color_active) ?>, 0.6);}
<?php if(!$submit_style){ ?>
.with_frm_style .frm_submit input[type=submit], .with_frm_style .frm_submit input[type=button], .frm_form_submit_style, .with_frm_style.frm_login_form input[type=submit]{width:<?php echo ($submit_width == '' ? 'auto' : $submit_width) . $important ?>;font-family:<?php echo stripslashes($font) ?>;font-size:<?php echo $submit_font_size; ?>;height:<?php echo $submit_height . $important ?>;line-height:normal <?php echo $important ?>;text-align:center;background:#<?php echo $submit_bg_color ?> url(<?php echo $submit_bg_img ?>)<?php echo $important ?>;border-width:<?php echo $submit_border_width ?>;border-color:#<?php echo $submit_border_color . $important ?>;border-style:solid;color:#<?php echo $submit_text_color . $important ?>;cursor:pointer;font-weight:<?php echo $submit_weight . $important ?>;-moz-border-radius:<?php echo $submit_border_radius . $important ?>;-webkit-border-radius:<?php echo $submit_border_radius . $important ?>;border-radius:<?php echo $submit_border_radius . $important ?>;text-shadow:none;padding:<?php echo $submit_padding . $important ?>;-moz-box-sizing:content-box;box-sizing:content-box;-ms-box-sizing:content-box;-moz-box-shadow:0 1px 1px #<?php echo $submit_shadow_color; ?>;-webkit-box-shadow:0px 1px 1px #<?php echo $submit_shadow_color; ?>;box-shadow:0 1px 1px #<?php echo $submit_shadow_color; ?>;-ms-filter:"progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#<?php echo $submit_shadow_color; ?>')";filter:progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#<?php echo $submit_shadow_color; ?>');}
.with_frm_style p.submit, .with_frm_style div.frm_submit, .with_frm_style.frm_login_form p.login-submit{padding-top:<?php echo $submit_margin ?>;padding-bottom:<?php echo $submit_margin ?>}
<?php if(empty($submit_bg_img)){ 
?>.with_frm_style .frm_submit input[type=submit]:hover, .with_frm_style .frm_submit input[type=button]:hover, .with_frm_style .frm_submit input[type=submit]:focus, .with_frm_style .frm_submit input[type=button]:focus, .with_frm_style.frm_login_form input[type=submit]:focus, .with_frm_style.frm_login_form input[type=submit]:hover{background:#<?php echo $submit_bg_color2  . $important ?>;}
<?php }
} ?>
.frm_ajax_loading{visibility:hidden;}
.frm_form_submit_style{height:auto;}
a.frm_save_draft{cursor:pointer;font-family:<?php echo stripslashes($font) ?>;font-size:<?php echo $submit_font_size ?>;font-weight:<?php echo $submit_weight ?>;}
.with_frm_style #frm_field_cptch_number_container{font-family:<?php echo stripslashes($font) ?>;font-size:<?php echo $font_size . $important ?>;color:#<?php echo $label_color . $important ?>;font-weight:<?php echo $weight . $important ?>;clear:both;}
.with_frm_style .frm_radio{display:<?php echo $radio_align . $important ?>;}
.with_frm_style .frm_left_container .frm_radio{margin<?php echo ($radio_align == 'block' ? "-left:{$label_margin}px;" : ':0') . $important; ?>}
.with_frm_style .frm_right_container .frm_radio{margin<?php echo ($radio_align == 'block' ? "-right:{$label_margin}px;" : ':0') . $important; ?>}
.with_frm_style .horizontal_radio .frm_radio{margin:0 5px 0 0<?php echo $important ?>;}
.with_frm_style .frm_checkbox{display:<?php echo $check_align . $important ?>;}
.with_frm_style .frm_left_container .frm_checkbox{margin<?php echo ($check_align == 'block') ? "-left:{$label_margin}px;" : ':0'; ?>}
.with_frm_style .frm_right_container .frm_checkbox{margin<?php echo ($check_align == 'block') ? "-right:{$label_margin}px;" : ':0'; ?>}
.with_frm_style .horizontal_radio .frm_checkbox{margin:0;margin-right:5px;}
.with_frm_style .vertical_radio .frm_checkbox, .with_frm_style .vertical_radio .frm_radio, .vertical_radio .frm_catlevel_1{display:block;}
.with_frm_style .horizontal_radio .frm_checkbox, .with_frm_style .horizontal_radio .frm_radio, .horizontal_radio .frm_catlevel_1{display:inline-block<?php echo $important ?>;}
.with_frm_style .frm_radio label, .with_frm_style .frm_checkbox label{font-family:<?php echo stripslashes($check_font) . $important ?>;font-size:<?php echo $check_font_size . $important ?>;color:#<?php echo $check_label_color . $important ?>;font-weight:<?php echo $check_weight . $important ?>;display:inline;}
.with_frm_style .frm_radio label .frm_file_container, .with_frm_style .frm_checkbox label .frm_file_container{display:inline-block;margin:5px;vertical-align:middle;}
.with_frm_style .frm_radio input[type=radio] {-webkit-appearance:radio;}
.with_frm_style .frm_checkbox input[type=checkbox] {-webkit-appearance:checkbox;}
.with_frm_style .frm_radio input[type=radio], .with_frm_style .frm_checkbox input[type=checkbox]{margin-right:5px;width:auto;height:auto;display:inline;}
.with_frm_style input[type=radio],.with_frm_style input[type=checkbox]{width:auto;}
.with_frm_style .frm_blank_field input[type=text], .with_frm_style .frm_blank_field input[type=password], .with_frm_style .frm_blank_field input[type=url], .with_frm_style .frm_blank_field input[type=tel], .with_frm_style .frm_blank_field input[type=number], .with_frm_style .frm_blank_field input[type=email], .with_frm_style .frm_blank_field textarea, .with_frm_style .frm_blank_field select, .frm_form_fields_error_style, .with_frm_style .frm_blank_field #recaptcha_area, .with_frm_style .frm_blank_field .chosen-container-multi .chosen-choices{color:#<?php echo $text_color_error . $important ?>;background-color:#<?php echo $bg_color_error ?>;border-color:#<?php echo $border_color_error . $important ?>;border-width:<?php echo $border_width_error . $important ?>;border-style:<?php echo $border_style_error . $important ?>;}
.with_frm_style :invalid, .with_frm_style :-moz-submit-invalid, .with_frm_style :-moz-ui-invalid {box-shadow:none;}
.with_frm_style .frm_error{font-weight:<?php echo $weight . $important ?>;}
.with_frm_style .frm_blank_field label, .with_frm_style .frm_error{color:#<?php echo $border_color_error . $important ?>;}
.with_frm_style .frm_error_style{background-color:#<?php echo $error_bg . $important ?>;border:1px solid #<?php echo $error_border . $important ?>;color:#<?php echo $error_text . $important ?>;font-size:<?php echo $error_font_size . $important ?>;margin:0;margin-bottom:<?php echo $field_margin ?>;}
.with_frm_style .frm_error_style img{padding-right:10px;vertical-align:middle;border:none;}
.with_frm_style .frm_trigger{cursor:pointer;}
.with_frm_style .frm_message, .frm_success_style{border:1px solid #<?php echo $success_border_color ?>;background-color:#<?php echo $success_bg_color ?>;color:#<?php echo $success_text_color ?>;}
.with_frm_style .frm_error_style, .with_frm_style .frm_message, .frm_success_style{-moz-border-radius:4px;-webkit-border-radius:4px;border-radius:4px;padding:15px;}
.with_frm_style .frm_message{margin:5px 0 15px;font-size:<?php echo $success_font_size . $important ?>;}
.with_frm_style .frm_message p{margin-bottom:5px;}
.frm_form_fields_style, .frm_form_fields_active_style, .frm_form_fields_error_style, .frm_form_submit_style{width:auto;}
.with_frm_style .frm_trigger span{float:left;}
.with_frm_style table.frm-grid, #content .with_frm_style table.frm-grid{border-collapse:collapse;border:none;}
.with_frm_style .frm-grid td, .frm-grid th{padding:5px;border-width:1px;border-style:solid;border-color:#<?php echo $border_color ?>;border-top:none;border-left:none;border-right:none;}
.form_results.with_frm_style{border-color:<?php echo $field_border_width ?> solid #<?php echo $border_color . $important ?>;}
.form_results.with_frm_style tr td{text-align:left;color:#<?php echo $text_color . $important ?>;padding:7px 9px;border-top:<?php echo $field_border_width ?> solid #<?php echo $border_color . $important ?>;}
.form_results.with_frm_style tr.frm_even, .frm-grid .frm_even{background-color:#<?php echo $bg_color . $important ?>;}
.form_results.with_frm_style tr.frm_odd, .frm-grid .frm_odd{background-color:#<?php echo $bg_color_active . $important ?>;}
.with_frm_style .frm_uploaded_files{padding:5px 0;}
.with_frm_style .frm_file_names{display:block;}
.frm_collapse .ui-icon{display:inline-block;}
.frm_toggle_container{margin-left:15px;}
.frm_toggle_container ul{margin-left:0;padding-left:0;}
#frm_loading{display:none;position:fixed;top:0;left:0;width:100%;height:100%;}
#frm_loading h3{font-weight:500;padding-bottom:15px;color:#fff;font-size:24px;}
#frm_loading_content{position:fixed;top:20%;left:33%;width:33%;text-align:center;padding-top:30px;font-weight:bold;z-index:9999999;}
#frm_loading img{max-width:100%;}
#frm_loading .progress{border-radius:4px;box-shadow:0 1px 2px rgba(0, 0, 0, 0.1) inset;height:20px;margin-bottom:20px;overflow:hidden;}
#frm_loading .progress.active .progress-bar{animation:2s linear 0s normal none infinite progress-bar-stripes;}
#frm_loading .progress-striped .progress-bar{background-image:linear-gradient(45deg, #<?php echo $border_color ?> 25%, rgba(0, 0, 0, 0) 25%, rgba(0, 0, 0, 0) 50%, #<?php echo $border_color ?> 50%, #<?php echo $border_color ?> 75%, rgba(0, 0, 0, 0) 75%, rgba(0, 0, 0, 0));background-size:40px 40px;}
#frm_loading .progress-bar{background-color:#<?php echo $bg_color ?>;box-shadow:0 -1px 0 rgba(0, 0, 0, 0.15) inset;float:left;height:100%;line-height:20px;text-align:center;transition:width 0.6s ease 0s;width:100%;}
.frm_pagination_cont ul.frm_pagination{display:inline-block;list-style:none;}
.frm_pagination_cont ul.frm_pagination > li{display:inline;list-style:none;margin:2px;background-image:none;}
.frmcal{padding-top:30px;}
.frmcal-title{font-size:116%;}
.frmcal table.frmcal-calendar{border-collapse:collapse;margin-top:20px;color:#<?php echo $text_color . $important ?>;}
.frmcal table.frmcal-calendar, .frmcal table.frmcal-calendar tbody tr td{border:1px solid #<?php echo $border_color . $important ?>;}
.frmcal table.frmcal-calendar, .frmcal, .frmcal-header{width:100%;}
.frmcal-header{text-align:center;}
.frmcal-prev{margin-right:10px;}
.frmcal-prev, .frmcal-dropdown{float:left;}
.frmcal-dropdown{margin-left:5px;}
.frmcal-next{float:right;}
.frmcal table.frmcal-calendar thead tr th{text-align:center;padding:2px 4px;}
.frmcal table.frmcal-calendar tbody tr td{height:110px;width:14.28%;vertical-align:top;padding:0 !important;color:#<?php echo $text_color . $important ?>;font-size:12px;}
table.frmcal-calendar .frmcal_date{background-color:#<?php echo $bg_color . $important ?>;padding:0 5px;text-align:right;-moz-box-shadow:0 2px 5px #<?php echo $border_color . $important ?>;-webkit-box-shadow:0 2px 5px #<?php echo $border_color ?>;box-shadow:0 2px 5px #<?php echo $border_color . $important ?>;-ms-filter:"progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=180, Color='#<?php echo $border_color ?>')";filter:progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=180, Color='#<?php echo $border_color ?>');}
table.frmcal-calendar .frmcal-today .frmcal_date{background-color:#<?php echo $bg_color_active ?>;padding:0 5px;text-align:right;-moz-box-shadow:0 2px 5px #<?php echo $border_color_active ?>;-webkit-box-shadow:0 2px 5px #<?php echo $border_color_active ?>;box-shadow:0 2px 5px #<?php echo $border_color_active ?>;-ms-filter:"progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=180, Color='#<?php echo $border_color_active ?>')";filter:progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=180, Color='#<?php echo $border_color_active ?>');}
.frmcal_num{display:inline;}
.frmcal-content{padding:2px 4px;}
.frm-loading-img{background:url(<?php echo FrmAppHelper::plugin_url() ?>/images/ajax_loader.gif) no-repeat center center;padding:6px 12px;}
#ui-datepicker-div{display:none;z-index:999 !important;}
.frm_form_fields div.rating-cancel{display:none !important;}
.frm_form_fields div.rating-cancel, .frm_form_fields div.star-rating{float:left;width:17px;height:17px;cursor:pointer;display:block;background:transparent;overflow:hidden;}
.frm_form_fields div.rating-cancel a:before{font:16px/1 'dashicons';content:'\f460';color:#CDCDCD;}
.frm_form_fields div.star-rating:before, .frm_form_fields div.star-rating a:before{font:16px/1 'dashicons';content:'\f154';color:#F0AD4E;}
.frm_form_fields div.rating-cancel a, .frm_form_fields div.star-rating a{display:block;width:16px;height:100%;border:0;}
.frm_form_fields div.star-rating-on:before, .frm_form_fields div.star-rating-on a:before{content:'\f155';}
.frm_form_fields div.star-rating-hover:before, .frm_form_fields div.star-rating-hover a:before{content:'\f155';}
.frm_form_fields div.frm_half_star:before, .frm_form_fields div.frm_half_star a:before{content:'\f459';}
.frm_form_fields div.rating-cancel.star-rating-hover a:before{color:#B63E3F;}
.frm_form_fields div.star-rating-readonly, .frm_form_fields div.star-rating-readonly a{cursor:default !important;}
.frm_form_fields div.star-rating{overflow:hidden!important;}
.with_frm_style .frm_form_field{clear:both;}
.frm_form_field.frm_third, .frm_form_field.frm_fourth, .frm_form_field.frm_fifth, .frm_form_field.frm_inline{clear:none;float:left;}
.frm_form_field.frm_left_half, .frm_form_field.frm_left_third, .frm_form_field.frm_left_two_thirds, .frm_form_field.frm_left_fourth, .frm_form_field.frm_left_fifth, .frm_form_field.frm_left_inline, .frm_form_field.frm_first_half, .frm_form_field.frm_first_third, .frm_form_field.frm_first_two_thirds, .frm_form_field.frm_first_fourth, .frm_form_field.frm_first_fifth, .frm_form_field.frm_first_inline{clear:left;float:left;}
.frm_form_field.frm_right_half, .frm_form_field.frm_right_third, .frm_form_field.frm_right_two_thirds, .frm_form_field.frm_right_fourth, .frm_form_field.frm_right_fifth, .frm_form_field.frm_right_inline, .frm_form_field.frm_last_half, .frm_form_field.frm_last_third, .frm_form_field.frm_last_two_thirds, .frm_form_field.frm_last_fourth, .frm_form_field.frm_last_fifth, .frm_form_field.frm_last_inline{clear:none;float:right;}
.frm_form_field.frm_left_half, .frm_form_field.frm_right_half, .frm_form_field.frm_first_half, .frm_form_field.frm_last_half{width:48%;}
.frm_form_field.frm_left_half, .frm_form_field.frm_first_half{margin-right:4%;}
.frm_form_field.frm_left_half.frm_left_container .frm_primary_label, .frm_form_field.frm_right_half.frm_left_container .frm_primary_label, .frm_form_field.frm_left_half.frm_right_container .frm_primary_label, .frm_form_field.frm_right_half.frm_right_container .frm_primary_label, .frm_form_field.frm_first_half.frm_left_container .frm_primary_label, .frm_form_field.frm_last_half.frm_left_container .frm_primary_label, .frm_form_field.frm_first_half.frm_right_container .frm_primary_label, .frm_form_field.frm_last_half.frm_right_container .frm_primary_label{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;max-width:25%;margin-right:0;margin-left:0;}
.frm_form_field.frm_left_half.frm_left_container .frm_primary_label, .frm_form_field.frm_right_half.frm_left_container .frm_primary_label, .frm_form_field.frm_first_half.frm_left_container .frm_primary_label, .frm_form_field.frm_last_half.frm_left_container .frm_primary_label{padding-right:10px;}
.frm_form_field.frm_left_half.frm_right_container .frm_primary_label, .frm_form_field.frm_right_half.frm_right_container .frm_primary_label, .frm_form_field.frm_first_half.frm_right_container .frm_primary_label, .frm_form_field.frm_last_half.frm_right_container .frm_primary_label{padding-left:10px;}
.frm_form_field.frm_left_container input, .frm_form_field.frm_left_container select, .frm_form_field.frm_left_container textarea, .frm_form_field.frm_right_container input, .frm_form_field.frm_right_container select, .frm_form_field.frm_right_container textarea{max-width:75%;}
.frm_form_field.frm_left_third, .frm_form_field.frm_third, .frm_form_field.frm_right_third, .frm_form_field.frm_first_third, .frm_form_field.frm_last_third{width:30%;}
.frm_form_field.frm_left_two_thirds, .frm_form_field.frm_right_two_thirds, .frm_form_field.frm_first_two_thirds, .frm_form_field.frm_last_two_thirds {width:65%;}
.frm_form_field.frm_left_third, .frm_form_field.frm_first_third, .frm_form_field.frm_third, .frm_form_field.frm_left_two_thirds, .frm_form_field.frm_first_two_thirds {margin-right:5%;}
.frm_form_field.frm_left_fourth, .frm_form_field.frm_fourth, .frm_form_field.frm_right_fourth, .frm_form_field.frm_first_fourth, .frm_form_field.frm_last_fourth{width:22%;}
.frm_form_field.frm_left_fourth, .frm_form_field.frm_fourth, .frm_form_field.frm_first_fourth{margin-right:4%;}
.frm_form_field.frm_left_fifth, .frm_form_field.frm_fifth, .frm_form_field.frm_right_fifth, .frm_form_field.frm_first_fifth, .frm_form_field.frm_last_fifth{width:16%;}
.frm_form_field.frm_left_fifth, .frm_form_field.frm_fifth, .frm_form_field.frm_first_fifth{margin-right:5%;}
.frm_form_field.frm_left_inline, .frm_form_field.frm_first_inline, .frm_form_field.frm_inline, .frm_form_field.frm_right_inline, .frm_form_field.frm_last_inline{width:auto;}
.frm_form_field.frm_left_inline, .frm_form_field.frm_first_inline, .frm_form_field.frm_inline{margin-right:4%;}
.frm_full, .frm_full .wp-editor-wrap, .frm_full input, .frm_full select, .frm_full textarea{width:100% !important;}
.frm_full .wp-editor-wrap input{width:auto !important;}
.wp-editor-wrap *, .wp-editor-wrap *:after, .wp-editor-wrap *:before{-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;}
.with_frm_style .frm_grid, .with_frm_style .frm_grid_first, .with_frm_style .frm_grid_odd{clear:both;margin-bottom:0 !important;padding:5px;border-width:1px;border-style:solid;border-color:#<?php echo $border_color ?>;border-left:none;border-right:none;}
.frm_grid, .frm_grid_odd{border-top:none;}
.frm_grid .frm_error, .frm_grid_first .frm_error, .frm_grid_odd .frm_error{display:none;}
.frm_grid.frm_blank_field, .frm_grid_first.frm_blank_field, .frm_grid_odd.frm_blank_field{background-color:#<?php echo $error_bg ?>;border-color:#<?php echo $error_border ?>;}
.frm_grid:after, .frm_grid_first:after, .frm_grid_odd:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}
.frm_grid_first{margin-top:20px;}
.frm_grid_first, .frm_grid_odd{background-color:#<?php echo $bg_color ?>;}
.frm_grid{background-color:#<?php echo $bg_color_active ?>;}
.frm_grid .frm_primary_label, .frm_grid_first .frm_primary_label, .frm_grid_odd .frm_primary_label, .frm_grid .frm_radio, .frm_grid_first .frm_radio, .frm_grid_odd .frm_radio, .frm_grid .frm_checkbox, .frm_grid_first .frm_checkbox, .frm_grid_odd .frm_checkbox{float:left !important;display:block;margin-top:0;margin-left:0 !important;}
.frm_grid_first .frm_radio label, .frm_grid .frm_radio label, .frm_grid_odd .frm_radio label, .frm_grid_first .frm_checkbox label, .frm_grid .frm_checkbox label, .frm_grid_odd .frm_checkbox label{visibility:hidden;white-space:nowrap;text-align:left;}
.frm_grid_first .frm_radio label input, .frm_grid .frm_radio label input, .frm_grid_odd .frm_radio label input, .frm_grid_first .frm_checkbox label input, .frm_grid .frm_checkbox label input, .frm_grid_odd .frm_checkbox label input{visibility:visible;margin:2px 0 0;float:right;}
.frm_grid .frm_radio, .frm_grid_first .frm_radio, .frm_grid_odd .frm_radio, .frm_grid .frm_checkbox, .frm_grid_first .frm_checkbox, .frm_grid_odd .frm_checkbox{display:inline;}
.frm_grid_2 .frm_radio, .frm_grid_2 .frm_checkbox, .frm_grid_2 label.frm_primary_label{width:48% !important;}
.frm_grid_2 .frm_radio, .frm_grid_2 .frm_checkbox{margin-right:4%;}
.frm_grid_3 .frm_radio, .frm_grid_3 .frm_checkbox, .frm_grid_3 label.frm_primary_label{width:30% !important;}
.frm_grid_3 .frm_radio, .frm_grid_3 .frm_checkbox{margin-right:3%;}
.frm_grid_4 .frm_radio, .frm_grid_4 .frm_checkbox{width:20% !important;}
.frm_grid_4 label.frm_primary_label{width:28% !important;}
.frm_grid_4 .frm_radio, .frm_grid_4 .frm_checkbox{margin-right:4%;}
.frm_grid_5 label.frm_primary_label, .frm_grid_7 label.frm_primary_label{width:24% !important;}
.frm_grid_5 .frm_radio, .frm_grid_5 .frm_checkbox{width:17% !important;margin-right:2%;}
.frm_grid_6 label.frm_primary_label{width:25% !important;}
.frm_grid_6 .frm_radio, .frm_grid_6 .frm_checkbox{width:14% !important;margin-right:1%;}
.frm_grid_7 label.frm_primary_label{width:22% !important;}
.frm_grid_7 .frm_radio, .frm_grid_7 .frm_checkbox{width:12% !important;margin-right:1%;}
.frm_grid_8 label.frm_primary_label{width:23% !important;}
.frm_grid_8 .frm_radio, .frm_grid_8 .frm_checkbox{width:10% !important;margin-right:1%;}
.with_frm_style .frm_inline_container.frm_grid_first label.frm_primary_label, .with_frm_style .frm_inline_container.frm_grid label.frm_primary_label, .with_frm_style .frm_inline_container.frm_grid_odd label.frm_primary_label, .with_frm_style .frm_inline_container.frm_grid_first .frm_opt_container, .with_frm_style .frm_inline_container.frm_grid .frm_opt_container, .with_frm_style .frm_inline_container.frm_grid_odd .frm_opt_container {margin-right:0;}
.frm_form_field.frm_two_col .frm_radio, .frm_form_field.frm_three_col .frm_radio, .frm_form_field.frm_four_col .frm_radio, .frm_form_field.frm_two_col .frm_checkbox, .frm_form_field.frm_three_col .frm_checkbox, .frm_form_field.frm_four_col .frm_checkbox {float:left;}
.frm_form_field.frm_two_col .frm_radio, .frm_form_field.frm_two_col .frm_checkbox{width:48%;margin-right:2%;}
.frm_form_field.frm_three_col .frm_radio, .frm_form_field.frm_three_col .frm_checkbox{width:31%;margin-right:2%;}
.frm_form_field.frm_four_col .frm_radio, .frm_form_field.frm_four_col .frm_checkbox{width:22%;margin-right:3%;}
.frm_form_field.frm_scroll_box .frm_opt_container{height:100px;overflow:auto;}
.frm_form_field.frm_html_scroll_box{height:100px;overflow:auto;background-color:#<?php echo $bg_color . $important;?>;border-color:#<?php echo $border_color . $important ?>;border-width:<?php echo $field_border_width . $important ?>;border-style:<?php echo $field_border_style . $important ?>;-moz-border-radius:<?php echo $border_radius . $important ?>;-webkit-border-radius:<?php echo $border_radius . $important ?>;border-radius:<?php echo $border_radius . $important ?>;width:<?php echo ($field_width == '' ? 'auto' : $field_width) . $important ?>;max-width:100%;font-size:<?php echo $field_font_size . $important ?>;padding:<?php echo $field_pad . $important ?>;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;outline:none<?php echo $important ?>;font-weight:normal;box-shadow:0 1px 1px rgba(0, 0, 0, 0.075) inset;}
.frm_form_field.frm_two_col .frm_opt_container:after, .frm_form_field.frm_three_col .frm_opt_container:after, .frm_form_field.frm_four_col .frm_opt_container:after{content:".";display:block;clear:both;visibility:hidden;line-height:0;height:0;}
.frm_form_field.frm_total input, .frm_form_field.frm_total textarea{opacity:1; background-color:transparent<?php echo $important ?>;border:none<?php echo $important ?>;font-weight:bold;-moz-box-shadow:none;-webkit-box-shadow:none;box-shadow:none;display:inline<?php echo $important ?>;width:auto<?php echo $important ?>;}
.frm_form_field.frm_total input:focus, .frm_form_field.frm_total textarea:focus{background-color:none;border:none;-moz-box-shadow:none;-webkit-box-shadow:none;box-shadow:none;}
.frm_text_block{margin-left:20px;}
.frm_text_block input, .frm_text_block label.frm_primary_label{margin-left:-20px;}
.frm_text_block .frm_checkbox input[type=checkbox], .frm_text_block .frm_radio input[type=radio]{margin-right:4px;}
.frm_clearfix:after{content:".";display:block;clear:both;visibility:hidden;line-height:0;height:0;}
.frm_clearfix{display:inline-block;}
html[xmlns] .frm_clearfix{display:block;}
* html .frm_clearfix{height:1%;}

.with_frm_style .chosen-container{font-size:<?php echo $field_font_size . $important ?>;position:relative;display:inline-block;zoom:1;vertical-align:middle;-webkit-user-select:none;-moz-user-select:none;user-select:none;
*display:inline;
}
.with_frm_style .chosen-container .chosen-drop{background:#fff;border:1px solid #aaa;border-top:0;position:absolute;top:100%;left:-9999px;box-shadow:0 4px 5px rgba(0,0,0,.15);z-index:1010;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;width:100%;}
.with_frm_style .chosen-container.chosen-with-drop .chosen-drop{left:0;}
.with_frm_style .chosen-container a{cursor:pointer;}
.with_frm_style .chosen-container-single .chosen-single{position:relative;display:block;overflow:hidden;padding:0 0 0 8px <?php echo $important ?>;height:<?php echo ($field_height == 'auto' || $field_height == '') ? '23px' : $field_height ?>;background:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(20%, #ffffff), color-stop(50%, #f6f6f6), color-stop(52%, #eeeeee), color-stop(100%, #f4f4f4));background:-webkit-linear-gradient(top, #ffffff 20%, #f6f6f6 50%, #eeeeee 52%, #f4f4f4 100%);background:-moz-linear-gradient(top, #ffffff 20%, #f6f6f6 50%, #eeeeee 52%, #f4f4f4 100%);background:-o-linear-gradient(top, #ffffff 20%, #f6f6f6 50%, #eeeeee 52%, #f4f4f4 100%);background:linear-gradient(top, #ffffff 20%, #f6f6f6 50%, #eeeeee 52%, #f4f4f4 100%);background-clip:padding-box;box-shadow:0 0 3px white inset, 0 1px 1px rgba(0, 0, 0, 0.1);
text-decoration:none;white-space:nowrap;line-height:<?php echo ($line_height == 'normal' ? '23px' : $line_height) . $important ?>;}
.with_frm_style .chosen-container-single .chosen-single span{margin-right:26px;display:block;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;}
.with_frm_style .chosen-container-single .chosen-single-with-deselect span{margin-right:38px;}

.with_frm_style .chosen-container-single .chosen-single abbr{
    display:block;position:absolute;right:26px;top:6px;width:12px;height:12px;font-size:1px;background:url('<?php echo FrmAppHelper::plugin_url() ?>/pro/images/chosen-sprite.png') -42px 1px no-repeat;
}
.with_frm_style .chosen-container-single .chosen-single abbr:hover{background-position:-42px -10px;}
.with_frm_style .chosen-container-single.chosen-disabled .chosen-single abbr:hover{background-position:-42px -10px;}
.with_frm_style .chosen-container-single .chosen-single div{position:absolute;right:0;top:0;display:block;height:100%;width:18px;}
.with_frm_style .chosen-container-single .chosen-single div b{background:url('<?php echo FrmAppHelper::plugin_url() ?>/pro/images/chosen-sprite.png') no-repeat 0px 2px;display:block;width:100%;height:100%;}
.with_frm_style .chosen-container-single .chosen-search{padding:3px 4px;position:relative;margin:0;white-space:nowrap;z-index:1010;}
.with_frm_style .chosen-container-single .chosen-search input[type="text"]{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;width:100%;height:<?php echo ($field_height == 'auto' || $field_height == '') ? 'auto' : $field_height ?>;background:white url('<?php echo FrmAppHelper::plugin_url() ?>/pro/images/chosen-sprite.png') no-repeat 100% -20px;background:url('<?php echo FrmAppHelper::plugin_url() ?>/pro/images/chosen-sprite.png') no-repeat 100% -20px;font-size:1em;font-family:sans-serif;line-height:normal;border-radius:0;
}
.with_frm_style .chosen-container-single .chosen-drop{margin-top:-1px;border-radius:0 0 4px 4px;background-clip:padding-box;}
.with_frm_style .chosen-container-single.chosen-container-single-nosearch .chosen-search{position:absolute;left:-9999px;}
.with_frm_style .chosen-container .chosen-results{cursor:text;overflow-x:hidden;overflow-y:auto;position:relative;margin:0 4px 4px 0;padding:0 0 0 4px;max-height:240px;-webkit-overflow-scrolling:touch;}
.with_frm_style .chosen-container .chosen-results li{display:none;margin:0;padding:5px 6px;list-style:none;line-height:15px;-webkit-touch-callout:none;}
.with_frm_style .chosen-container .chosen-results li.active-result{display:list-item;cursor:pointer;}
.with_frm_style .chosen-container .chosen-results li.disabled-result{display:list-item;color:#ccc;cursor:default;}
.with_frm_style .chosen-container .chosen-results li.highlighted{background-color:#3875d7;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(20%, #3875d7), color-stop(90%, #2a62bc));background-image:-webkit-linear-gradient(#3875d7 20%, #2a62bc 90%);background-image:-moz-linear-gradient(#3875d7 20%, #2a62bc 90%);background-image:-o-linear-gradient(#3875d7 20%, #2a62bc 90%);background-image:linear-gradient(#3875d7 20%, #2a62bc 90%);color:#fff;}
.with_frm_style .chosen-container .chosen-results li.no-results{display:list-item;background:#f4f4f4;}
.with_frm_style .chosen-container .chosen-results li.group-result{display:list-item;font-weight:bold;cursor:default;}
.with_frm_style .chosen-container .chosen-results li.group-option{padding-left:15px;}
.with_frm_style .chosen-container .chosen-results li em{font-style:normal;text-decoration:underline;}

.with_frm_style .chosen-container-multi .chosen-choices{position:relative;overflow:hidden;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;margin:0;padding:0;width:100%;height:auto !important;height:1%;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(1%, #eeeeee), color-stop(15%, #ffffff));background-image:-webkit-linear-gradient(#eeeeee 1%, #ffffff 15%);background-image:-moz-linear-gradient(#eeeeee 1%, #ffffff 15%);background-image:-o-linear-gradient(#eeeeee 1%, #ffffff 15%);background-image:linear-gradient(#eeeeee 1%, #ffffff 15%);cursor:text;}
.with_frm_style .chosen-container-multi .chosen-choices li{float:left;list-style:none;}
.with_frm_style .chosen-container-multi .chosen-choices li.search-field{margin:0;padding:0;white-space:nowrap;}
.with_frm_style .chosen-container-multi .chosen-choices li.search-field input[type="text"]{margin:1px 0;padding:5px;height:15px<?php echo $important ?>;outline:0;border:0 !important;background:transparent !important;box-shadow:none;color:#666;font-size:100%;font-family:sans-serif;line-height:normal;border-radius:0;}
.with_frm_style .chosen-container-multi .chosen-choices li.search-choice{position:relative;margin:3px 0 3px 5px;padding:3px 20px 3px 5px;border:1px solid #aaa;border-radius:3px;background-color:#e4e4e4;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(20%, #f4f4f4), color-stop(50%, #f0f0f0), color-stop(52%, #e8e8e8), color-stop(100%, #eeeeee));background-image:-webkit-linear-gradient(#f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);background-image:-moz-linear-gradient(#f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);background-image:-o-linear-gradient(#f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);background-image:linear-gradient(#f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);background-clip:padding-box;box-shadow:0 0 2px white inset, 0 1px 0 rgba(0, 0, 0, 0.05);color:#333;line-height:13px;cursor:default;}
.with_frm_style .chosen-container-multi .chosen-choices li.search-choice .search-choice-close{position:absolute;top:4px;right:3px;display:block;width:12px;height:12px;background:url('<?php echo FrmAppHelper::plugin_url() ?>/pro/images/chosen-sprite.png') -42px 1px no-repeat;font-size:1px;}
.with_frm_style .chosen-container-multi .chosen-choices li.search-choice .search-choice-close:hover{background-position:-42px -10px;}
.with_frm_style .chosen-container-multi .chosen-choices li.search-choice-disabled{padding-right:5px;border:1px solid #ccc;background-color:#e4e4e4;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(20%, #f4f4f4), color-stop(50%, #f0f0f0), color-stop(52%, #e8e8e8), color-stop(100%, #eeeeee));background-image:-webkit-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);background-image:-moz-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);background-image:-o-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);background-image:linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);color:#666;}
.with_frm_style .chosen-container-multi .chosen-choices li.search-choice-focus{background:#d4d4d4;}
.with_frm_style .chosen-container-multi .chosen-choices li.search-choice-focus .search-choice-close{background-position:-42px -10px;}
.with_frm_style .chosen-container-multi .chosen-results{margin:0;padding:0;}
.with_frm_style .chosen-container-multi .chosen-drop .result-selected{display:list-item;color:#ccc;cursor:default;}

.with_frm_style .chosen-container-active .chosen-single{border:1px solid #5897fb;box-shadow:0 0 5px rgba(0, 0, 0, 0.3);}
.with_frm_style .chosen-container-active.chosen-with-drop .chosen-single{border:1px solid #aaa;-moz-border-radius-bottomright:0;border-bottom-right-radius:0;-moz-border-radius-bottomleft:0;border-bottom-left-radius:0;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(20%, #eeeeee), color-stop(80%, #ffffff));background-image:-webkit-linear-gradient(#eeeeee 20%, #ffffff 80%);background-image:-moz-linear-gradient(#eeeeee 20%, #ffffff 80%);background-image:-o-linear-gradient(#eeeeee 20%, #ffffff 80%);background-image:linear-gradient(#eeeeee 20%, #ffffff 80%);box-shadow:0 1px 0 #fff inset;}
.with_frm_style .chosen-container-active.chosen-with-drop .chosen-single div{border-left:none;background:transparent;}
.with_frm_style .chosen-container-active.chosen-with-drop .chosen-single div b{background-position:-18px 2px;}
.with_frm_style .chosen-container-active .chosen-choices li.search-field input[type="text"]{color:#111 !important;}

.with_frm_style .chosen-disabled{opacity:0.5 !important;cursor:default;}
.with_frm_style .chosen-disabled .chosen-single{cursor:default;}
.with_frm_style .chosen-disabled .chosen-choices .search-choice .search-choice-close{cursor:default;}

.with_frm_style .chosen-rtl{text-align:right;}
.with_frm_style .chosen-rtl .chosen-single{overflow:visible;padding:0 8px 0 0;}
.with_frm_style .chosen-rtl .chosen-single span{margin-right:0;margin-left:26px;direction:rtl;}
.with_frm_style .chosen-rtl .chosen-single-with-deselect span{margin-left:38px;}
.with_frm_style .chosen-rtl .chosen-single div{right:auto;left:3px;}
.with_frm_style .chosen-rtl .chosen-single abbr{right:auto;left:26px;}
.with_frm_style .chosen-rtl .chosen-choices li{float:right;}
.with_frm_style .chosen-rtl .chosen-choices li.search-field input[type="text"]{direction:rtl;}
.with_frm_style .chosen-rtl .chosen-choices li.search-choice{margin:3px 5px 3px 0;padding:3px 5px 3px 19px;}
.with_frm_style .chosen-rtl .chosen-choices li.search-choice .search-choice-close{right:auto;left:4px;}
.with_frm_style .chosen-rtl.chosen-container-single-nosearch .chosen-search, .with_frm_style .chosen-rtl .chosen-drop{left:9999px;}
.with_frm_style .chosen-rtl.chosen-container-single .chosen-results{margin:0 0 4px 4px;padding:0 4px 0 0;}
.with_frm_style .chosen-rtl .chosen-results li.group-option{padding-right:15px;padding-left:0;}
.with_frm_style .chosen-rtl.chosen-container-active.chosen-with-drop .chosen-single div{border-right:none;}
.with_frm_style .chosen-rtl .chosen-search input[type="text"]{padding:4px 5px 4px 20px;background:white url('<?php echo FrmAppHelper::plugin_url() ?>/pro/images/chosen-sprite.png') no-repeat -30px -20px;background:url('<?php echo FrmAppHelper::plugin_url() ?>/pro/images/chosen-sprite.png') no-repeat -30px -20px;direction:rtl;}
.with_frm_style .chosen-rtl.chosen-container-single .chosen-single div b{background-position:6px 2px;}
.with_frm_style .chosen-rtl.chosen-container-single.chosen-with-drop .chosen-single div b{background-position:-12px 2px;}

@media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min-resolution: 144dpi){
.with_frm_style .chosen-rtl .chosen-search input[type="text"], .with_frm_style .chosen-container-single .chosen-single abbr, .with_frm_style .chosen-container-single .chosen-single div b, .with_frm_style .chosen-container-single .chosen-search input[type="text"], .with_frm_style .chosen-container-multi .chosen-choices .search-choice .search-choice-close, .with_frm_style .chosen-container .chosen-results-scroll-down span, .with_frm_style .chosen-container .chosen-results-scroll-up span{background-image:url('<?php echo FrmAppHelper::plugin_url() ?>/pro/images/chosen-sprite2x.png') !important;background-size:52px 37px !important;background-repeat:no-repeat !important;}
}
<?php echo $custom_css; ?>