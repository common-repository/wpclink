<?php
/**
 * CLink Token Functions 
 *
 * CLink token function
 *
 * @package CLink
 * @subpackage Link Manager
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Get Token Data by Token Name
 * 
 * @param string $token_name token name
 * 
 * @return array
 */
function wpclink_get_token_data_by_token_name($token_name = NULL){
	
	global $wpdb;
		// Table Prefix
	$table_token = $wpdb->prefix . 'wpclink_tokens';
	
	$token_data = $wpdb->get_row( "SELECT * FROM $table_token WHERE token = '$token_name'", ARRAY_A );
	
	return $token_data;
	
}