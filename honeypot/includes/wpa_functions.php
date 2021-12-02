<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function wpa_load_scripts(){
	if (current_user_can('activate_plugins') && (get_option('wpa_disable_test_widget') != 'yes')){
    	$wpa_add_test = 'yes';
	} else {
		$wpa_add_test = 'no';
	}

	$wpa_unique_id 	=	rand(111, 99999);

	$wpa_hidden_field 			= "<span class='wpa_hidden_field' style='display:none;height:0;width:0;'><input type='text' name='".$GLOBALS['wpa_field_name']."' value='".$wpa_unique_id."' /></span>";

	wp_enqueue_script( 'wpascript',  plugins_url( '/js/wpa.js', __FILE__ ), array ( 'jquery' ), '1.8.5', true);
	wp_add_inline_script( 'wpascript', 'var wpa_hidden_field = "'.$wpa_hidden_field.'"; var wpa_add_test = "'.$wpa_add_test.'"; var wpa_field_name = "'.$GLOBALS['wpa_field_name'].'"; wpa_unique_id = "'.$wpa_unique_id.'"; ');
	wp_enqueue_style( 'wpa-css', plugins_url( '/css/wpa.css', __FILE__ ), array(), '1.8.5');
}

function wpa_plugin_menu(){
    add_menu_page( 'WP Armour', 'WP Armour', 'manage_options', 'wp-armour', 'wpa_options','dashicons-shield');
}

function wpa_options(){
	$wpa_tabs = array(
				'settings' => array('name'=>'Settings','path'=>'wpa_settings.php'),
				'stats' => array('name'=>'Statistics','path'=>'wpa_stats.php'),
				'extended_version' => array('name'=>"What's in WP Armour Extended ?",'path'=>'wpa_extended_version.php')
	);

	$wpa_tabs = apply_filters( 'wpa_tabs_filter', $wpa_tabs);

	include 'views/wpa_main.php';
}

function wpa_save_settings(){	
	if ( isset($_POST['wpa_nonce']) && wp_verify_nonce($_POST['wpa_nonce'], 'wpa_save_settings')) {		
		if (empty($_POST['wpa_field_name'])){
			$return['status']   = 'error';
			$return['body'] 	= "Honey Pot Field Name can't be empty";
		} else {
			update_option('wpa_field_name',sanitize_title_with_dashes($_POST['wpa_field_name']));
			update_option('wpa_error_message',sanitize_text_field($_POST['wpa_error_message']));
			update_option('wpa_disable_test_widget',sanitize_text_field($_POST['wpa_disable_test_widget']));

			$GLOBALS['wpa_field_name'] 				= get_option('wpa_field_name');
			$GLOBALS['wpa_error_message'] 			= get_option('wpa_error_message');

			$return['status']   = 'ok';
			$return['body'] 	= 'Settings Saved';
		}
	} else {
		$return['status']   = 'error';
		$return['body'] 	= 'Sorry, your nonce did not verify. Please try again.';
	}
	return $return;
}

function wpa_save_stats($wp_system, $data){
	$currentStats 	= json_decode(get_option('wpa_stats'), true);
	$timeArray 		= array('today','week','month');	

	if (!array_key_exists($wp_system,$currentStats)){
		$currentStats[$wp_system]['today']['count']  			= 0;
		$currentStats[$wp_system]['week']['count']  			= 0;
		$currentStats[$wp_system]['month']['count']  			= 0;
		$currentStats[$wp_system]['today']['date']  			= date('Ymd');
		$currentStats[$wp_system]['week']['date']  				= date('Ymd');
		$currentStats[$wp_system]['month']['date']  			= date('Ymd');
	}

	foreach ($timeArray as $key => $time) {
		if (wpa_check_date($currentStats['total'][$time]['date'],$time)){
			$currentStats['total'][$time]['count']  			+= 1;			
		} else {
			$currentStats['total'][$time]['count'] 			= 1;				
		}

		if (wpa_check_date($currentStats[$wp_system][$time]['date'],$time)){
			$currentStats[$wp_system][$time]['count']  			+= 1;			
		} else {
			$currentStats[$wp_system][$time]['count'] 			= 1;				
		}

		$currentStats['total'][$time]['date'] 				= date('Ymd');
		$currentStats[$wp_system][$time]['date'] 			= date('Ymd');
	}
	
	$currentStats['total']['all_time'] += 1;
	@$currentStats[$wp_system]['all_time'] += 1;
	update_option('wpa_stats', json_encode($currentStats));
}

function wpa_check_date($timestamp, $comparision){
	switch ($comparision) {
		case 'today':
			if (date('Ymd') == $timestamp){
				return true;
			} else {
				return false;
			}
		break;

		case 'week':
			$firstWeekDay 		= date("Ymd", strtotime('monday this week'));  
			$lastWeekDay 		= date("Ymd", strtotime('sunday this week'));  

			if($timestamp >= $firstWeekDay && $timestamp <= $lastWeekDay) {
				return true;
			} else {
				return false;
			}
		break;

		case 'month':
			if(date('Ym',strtotime($timestamp)) == date('Ym')) {
				return true;
			} else {
				return false;
			}
		break;
	}
}

function wpa_unqiue_field_name(){
	$permitted_chars = 'abcdefghijklmnopqrstuvwxyz';
	return substr(str_shuffle($permitted_chars), 0, 6).rand(1,9999);
}

function wpa_check_is_spam($form_data){
	if (isset($form_data[$GLOBALS['wpa_field_name']])){
	//if (isset($form_data[$GLOBALS['wpa_field_name']]) && ($form_data[$GLOBALS['wpa_field_name']] == $_COOKIE['wpa_unique_id'])){
		return false;
	} else {
		return true;
	}
}