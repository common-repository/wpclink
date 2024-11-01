<?php
/**
 * CLink Schedule Functions
 *
 * CLink schedule of canonical URLs
 *
 * @package CLink
 * @subpackage System
 */
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
register_activation_hook(WPCLINK_MAIN_FILE, 'wpclink_activation_schedule');
/**
 * Schedule to check linked is updated content
 * 
 */
function wpclink_activation_schedule() {
    if (! wp_next_scheduled ( 'cl_notify_me' )) {
	wp_schedule_event(time(), 'hourly', 'cl_notify_me');
    }
}
// Decative Schedule for deactive plugin
register_deactivation_hook(WPCLINK_MAIN_FILE, 'wpclink_deactivation_schedule');
/**
 * Deactive Schedule
 */
function wpclink_deactivation_schedule() {
	wp_clear_scheduled_hook('cl_notify_me');
}


/**

 * CLink Tool SSL Checker Run
 * 
 */
function wpclink_tool_ssl_checker_runcheck(){
	
if(is_admin()){

 // Information
  $date_format = get_option( 'date_format' );
  $time_format = get_option( 'time_format' );
  $https_url_with = site_url( null, 'https' );
  $https_url_without = explode("://",$https_url_with);
  $https_url_without = $https_url_without[1];
	
	
  // Get SSL Certificate status
  $orignal_parse = parse_url($https_url_with, PHP_URL_HOST);
  $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
  $read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
    
    
  // If SSL is false
  if(!$read){
    wpclink_notif_print("ERROR: Unable to check SSL certificate validity.",'error'); 
  } else{
      
    $cert = stream_context_get_params($read);
    $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
      
    // Provide SSL Details in email content
    $altnames = str_replace ( "DNS:", "", $certinfo['extensions']['subjectAltName'] );
      
    // Check if any of the alternate names match the site URL 
    $altnames=str_replace ( " ", "", $altnames );
    $altnames=explode ( "," , $altnames );
  
  
    
    // Check SSL Certificate expiry 
    $localtz = get_option( 'timezone_string' );
    $tzdiff = get_option('gmt_offset') * 60 * 60;
    $localts = $certinfo['validTo_time_t'] + $tzdiff;    
    $days_to_expiry = $localts - time();
    $days_to_expiry = $days_to_expiry / 60 / 60 / 24;
      	  
	 // Reminder
	if ( round($days_to_expiry) == 7 || 
		 round($days_to_expiry) == 3 || 
		 round($days_to_expiry) == 1 ) { 
        wpclink_notif_print("The SSL certificate will expire on ".date($date_format.' '.$time_format, $localts )." ".$localtz. " (".round($days_to_expiry,0). ") days,  if not renewed the wpCLink plugin will be deactivated.",'error'); 
    }
	// Expired
	if ( round($days_to_expiry) <= 0){
		if( is_plugin_active('wpclink-extensions/wpclink-extensions') ){
			deactivate_plugins('wpclink/wpclink.php');
		}
		if( is_plugin_active('wpclink/wpclink.php') ){
			deactivate_plugins('wpclink/wpclink.php');
		}

		wpclink_notif_print("The SSL certificate has expired. wpCLink has been deactivated",'error');
	}
	  
	
  }
		
	}
    
}
add_action('admin_init','wpclink_tool_ssl_checker_runcheck');
/**

 * CLink Canonical Verification Schedule Run
 * 
 */
function wpclink_do_canonical_verification(){
	
	
	
	global $wpdb;
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses'; 
	
	$agreements = wpclink_get_all_license_referent();
			
	foreach($agreements as $single_agree){
		
		$post_type = get_post_type( $single_agree['post_id']);
		
		if (  $post_type == 'post' || 
			  $post_type == 'page' ) {
			
		}else{
			continue;
		}
	
		$cl_connect_site = $single_agree['site_url'];
		
			
			// REQUESTED QUERY
			$request_query['client_site'] = urlencode(get_bloginfo('url')."/");
			$request_query['s_agree'] = 1;
			$request_query['cl_action'] = 'cl_list';
			$request_query['content_id'] = $single_agree['post_id'];
			$build_query = build_query( $request_query );
			
	
			$xml=wpclink_fetch_content_by_curl($cl_connect_site.'?'.$build_query,true);
			
		
			if($xml === false){
				
				// Server Error
if($cs_server = wpclink_get_license_meta($single_agree['license_id'],'creation_status',true)){		wpclink_update_license_meta($single_agree['license_id'],'creation_status','servererror');	
}else{
	wpclink_add_license_meta($single_agree['license_id'],'creation_status','servererror');
}
				continue;
			}
		
			// XML
			libxml_use_internal_errors(true);
			$xml = simplexml_load_string($xml);
			
		
						
		
			// If post is not found.
		
			if(isset($xml->channel->item->status_code) && ($xml->channel->item->status_code == '404')){
				
				
if($cs_not_found = wpclink_get_license_meta($single_agree['license_id'],'creation_status',true)){		wpclink_update_license_meta($single_agree['license_id'],'creation_status','notfound');	
}else{
	wpclink_add_license_meta($single_agree['license_id'],'creation_status','notfound');
}
				
				continue;
			}
			
		
		
			
			foreach($xml->channel->item as $single){
				
				$link = (string)$single->link;
				$match = wpclink_is_canonical_valid($link,get_bloginfo('url'));
				
				
				if(!empty($link)){
					

					
if($cs_found = wpclink_get_license_meta($single_agree['license_id'],'creation_status',true)){		wpclink_update_license_meta($single_agree['license_id'],'creation_status','found');	
}else{
	wpclink_add_license_meta($single_agree['license_id'],'creation_status','found');
}
				}
				

				
				if($match == false){
					
					
					if($canonical_fail_value = wpclink_get_option('verification_canonical_fail_threshold')){
						
					}else{
						$canonical_fail_value = 3;
					}
					
			 
					 
					 // RESTART
					wpclink_update_license_meta($single_agree['license_id'],'canonical_success_attemps',0);
					
					// ATTEMPS
					
					$attemps = wpclink_get_license_meta($single_agree['license_id'],'wpclink_license_attemps',true);
					
					if(($attemps >= 0)){
						
						$attemps_save = $attemps+1;
						
						wpclink_update_license_meta($single_agree['license_id'],'wpclink_license_attemps',$attemps_save);
						
					}
					
					if($attemps === false){
						wpclink_add_license_meta($single_agree['license_id'],'wpclink_license_attemps',1);
						}
										
					// GET ATTEMPS
					$attemps_check = wpclink_get_license_meta($single_agree['license_id'],'wpclink_license_attemps',true);
					
					
					// MORE THAN THREE
					if($attemps_check >= $canonical_fail_value){
					
					// VIOLATION
					$wpdb->update($clink_sites_table,array('verification_status' => 'fail'), array('license_id' => $single_agree['license_id'],'mode' => 'referent'));
					
					// BLACK LIST
					if($black_list_array = wpclink_get_option('wpclink_site_blacklist')){
						
						$blacklist = array_merge($black_list_array,array($cl_connect_site));
						wpclink_update_option('wpclink_site_blacklist',array_unique($blacklist));
						
					}else{
						wpclink_update_option('wpclink_site_blacklist',array($cl_connect_site));
					}
					
					// RESTART
					wpclink_update_license_meta($single_agree['license_id'],'wpclink_license_attemps',0);
					
					}
					
					
				}
				if($match == true){
					
					if($canonical_success_value = wpclink_get_option('verification_canonical_success_threshold')){
						
					}else{
						$canonical_success_value = 3;
					}
					
					
					
					// RESTART
					wpclink_update_license_meta($single_agree['license_id'],'wpclink_license_attemps',0);
					
					
					$attemps_success = wpclink_get_license_meta($single_agree['license_id'],'canonical_success_attemps',true);
					
					if($attemps_success >= 0){
						
						$attemps_success_save = $attemps_success+1;
						
						wpclink_update_license_meta($single_agree['license_id'],'canonical_success_attemps',$attemps_success_save);
						
					}
					
					if($attemps_success  === false){
						
						
						wpclink_add_license_meta($single_agree['license_id'],'canonical_success_attemps',1);
						
						
					}
										
					// GET ATTEMPS
					$attemps_success_check = wpclink_get_license_meta($single_agree['license_id'],'canonical_success_attemps',true);
					
					
					// MORE THAN THREE
					if($attemps_success_check >= $canonical_success_value){
					
					// GOOD CONDITION
					$wpdb->update($clink_sites_table,array('verification_status' => 'pass'), array('license_id' => $single_agree['license_id'],'mode' => 'referent'));
					
					// BLACK LIST
					if($black_list_array = wpclink_get_option('wpclink_site_blacklist')){
						
						
						if(in_array($cl_connect_site,$black_list_array)){
							
							// REMOVE SITE
							$blacklist = array_diff($black_list_array,array($cl_connect_site));
							
						}
						
						wpclink_update_option('wpclink_site_blacklist',$blacklist);
						
					}
					
					// RESTART
					wpclink_update_license_meta($single_agree['license_id'],'canonical_success_attemps',0);
					
					}
					
					
					
				}
				
			
			}
	}
	
	}
// Register clink canonical verification schedule on plugin activatation
register_activation_hook(WPCLINK_MAIN_FILE, 'wpclink_schedule_canonical_verification');
/**
 * CLink Canonical Verification Schedule Time on Activation
 * 
 * Verfication Schedule Time
 * - Daily 
 * - Hourly 
 * - Twice a Day
 * 
 */
function wpclink_schedule_canonical_verification() {
	$saved_options = wpclink_get_option( 'preferences_general' );
	
	if(!isset($saved_options['canonical_time'])){
		if (! wp_next_scheduled ( 'cl_daily_canonical_script' )) {
			wp_schedule_event(time(), 'daily', 'cl_daily_canonical_script');
    	}
	}elseif($saved_options['canonical_time'] == 'hourly'){
		if (! wp_next_scheduled ( 'cl_hourly_canonical_script' )) {
			wp_schedule_event(time(), 'hourly', 'cl_hourly_canonical_script');
    	}
	}elseif($saved_options['canonical_time'] == 'twicedaily'){
		if (! wp_next_scheduled ( 'cl_twicedaily_canonical_script' )) {
			wp_schedule_event(time(), 'twicedaily', 'cl_twicedaily_canonical_script');
    	}
	}elseif($saved_options['canonical_time'] == 'daily'){
		if (! wp_next_scheduled ( 'cl_daily_canonical_script' )) {
			wp_schedule_event(time(), 'daily', 'cl_daily_canonical_script');
    	}
	}
}
// Register Hourly Verfication
add_action('cl_hourly_canonical_script', 'wpclink_hourly_canonical_script_func');
/**
 * CLink Register Hourly Verification 
 *
 */
function wpclink_hourly_canonical_script_func() {	
	wpclink_do_canonical_verification();
	wpclink_verify_canonical_all_links();
	}
// Register Twice a Day Verfication
add_action('cl_twicedaily_canonical_script', 'wpclink_set_canonical_verification_12h');
/**
 * CLink Register Twice a Day Verification 
 *
 */
function wpclink_set_canonical_verification_12h() {	
	wpclink_do_canonical_verification();
	wpclink_verify_canonical_all_links();
}
// Register Daily Verification
add_action('cl_daily_canonical_script', 'wpclink_daily_canonical_script_func');
/**
 * CLink Register Daily Verification 
 *
 */
function wpclink_daily_canonical_script_func() {	
	wpclink_do_canonical_verification();
	wpclink_verify_canonical_all_links();
}
/**
 * CLink Canonical Verification Schedule Time Reloader
 * 
 */
function wpclink_reschedule_canonical_verification(){
	$saved_options = wpclink_get_option( 'preferences_general' );
	
	// FLUSH
	wp_clear_scheduled_hook('cl_hourly_canonical_script');
	wp_clear_scheduled_hook('cl_twicedaily_canonical_script');
	wp_clear_scheduled_hook('cl_daily_canonical_script');
	
	// LOAD
	if(!isset($saved_options['canonical_time'])){
		if (! wp_next_scheduled ( 'cl_daily_canonical_script' )) {
			wp_schedule_event(time(), 'daily', 'cl_daily_canonical_script');
    	}
	}elseif($saved_options['canonical_time'] == 'hourly'){
		if (! wp_next_scheduled ( 'cl_hourly_canonical_script' )) {
			wp_schedule_event(time(), 'hourly', 'cl_hourly_canonical_script');
    	}
	}elseif($saved_options['canonical_time'] == 'twicedaily'){
		if (! wp_next_scheduled ( 'cl_twicedaily_canonical_script' )) {
			wp_schedule_event(time(), 'twicedaily', 'cl_twicedaily_canonical_script');
    	}
	}elseif($saved_options['canonical_time'] == 'daily'){
		if (! wp_next_scheduled ( 'cl_daily_canonical_script' )) {
			wp_schedule_event(time(), 'daily', 'cl_daily_canonical_script');
    	}
	}
}
// Deactivate Verification Schedule on Plugin Deactivation
register_deactivation_hook(WPCLINK_MAIN_FILE, 'wpclink_unschedule_canonical_verification');
/**
 * CLink Canonical Verification Schedule Deactivation
 * 
 */
function wpclink_unschedule_canonical_verification() {
	wp_clear_scheduled_hook('cl_hourly_canonical_script');
	wp_clear_scheduled_hook('cl_twicedaily_canonical_script');
	wp_clear_scheduled_hook('cl_daily_canonical_script');
}

