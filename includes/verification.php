<?php
/**
 * CLink Verification Functions 
 *
 * CLink verification of content functions
 *
 * @package CLink
 * @subpackage Link Manager
 */
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink IP Verification Secuirty Layer
 *  
 * Verify the Site IP before access the link is IP is same which has recorded in CLink Plugin Database
 * 
 * @return boolean
 */
function wpclink_extention_signal(){
	
	if ( defined( 'WPCLINK_IP_CHECK' ) ) {
		if(WPCLINK_IP_CHECK){
			$signal = apply_filters( 'cl_extention_filter', true, $arg1);
		}else{
			$signal = true;	
		}
	}else{
			$signal = apply_filters( 'cl_extention_filter', true, $arg1);
	}
	return $signal;
}
/**
 * CLink Verify IP 
 * 
 * @param string $default 
 * 
 * @return boolean
 */
function wpclink_is_site_ip_linked_valid($default){
	
	global $wpdb;
	
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	
	$ip_address = wpclink_get_client_ip();
	
	// QUERY
	$ip_search = $wpdb->get_results( "SELECT * FROM $clink_sites_table WHERE site_IP = '$ip_address' AND verification_status = 'pass' AND mode = 'referent' ", ARRAY_A );
	
	if($ip_search){
		return true;
	}else{
		return false;
	}
}
// Register ip verification on cl_extention_filter
add_action('cl_extention_filter','wpclink_is_site_ip_linked_valid');