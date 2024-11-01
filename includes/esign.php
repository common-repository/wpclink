<?php
/**
 * CLink Electronic Signature Functions
 *
 * CLink electronic signature of licensee and licensor functions
 *
 * @package CLink
 * @subpackage Link Manager
 */
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Get Electronic Signature by ID
 * 
 * @param integer $post_id  esign id
 * 
 * @return array
 */
function wpclink_get_esign_by_post_id($post_id = 0){
	
	global $wpdb;
		// Table Prefix
	$table_esign = $wpdb->prefix . 'wpclink_esigns';
	
	$esign = $wpdb->get_row( "SELECT * FROM $table_esign WHERE post_id = '$post_id'", ARRAY_A );
	
	return $esign;
	
}
/**
 * Get Electronic Signature by ID
 * 
 * @param integer $esign_id esign id 
 * 
 * @return array
 */
function wpclink_get_esign($esign_id = 0){
	
	global $wpdb;
		// Table Prefix
	$table_esign = $wpdb->prefix . 'wpclink_esigns';
	
	$esign = $wpdb->get_row( "SELECT * FROM $table_esign WHERE esign_id = $esign_id", ARRAY_A );
	
	return $esign;
	
} 
/**
 * Delete Electronic Signature by ID
 * 
 * @param integer $where_id  esign id
 * 
 * @return integer deleted id
 */
function wpclink_delete_esign($where_id = 0){
		global $wpdb;
		// Table Prefix
		$table_esign = $wpdb->prefix . 'wpclink_esigns';
		
		$deleted_esign = $wpdb->delete( $table_esign, array( 'esign_id' => $where_id ) );
		
		return $deleted_esign;
}
/**
 * Update Electronic Signature by ID
 * 
 * @param array $data electronic signature data  
 * @param integer $where_id esign id
 * 
 * @return integer
 */
function wpclink_update_esign($data = array(),$where_id = 0){
	
		global $wpdb;
		// Table Prefix
		$table_esign = $wpdb->prefix . 'wpclink_esigns';
		
		$updated_esign = $wpdb->update( 
			$table_esign, 
			$data, 
			array( 'esign_id' => $where_id )
		);
		
	  if($updated_esign > 0){
		  
		  $esign_id = $wpdb->insert_id;
		 
	  }
	  
	  return $updated_esign;	
	
}
/**
 * Update Electronic Signature by Token
 * 
 * @param array $data electronic signature data
 * @param string $token token value
 * 
 * @return integer
 */
function wpclink_update_esign_by_token($data = array(),$token = ''){
	
		global $wpdb;
		// Table Prefix
		$table_esign = $wpdb->prefix . 'wpclink_esigns';
		
		$updated_esign = $wpdb->update( 
			$table_esign, 
			$data, 
			array( 'esign_key' => $token )
		);
		
	  if($updated_esign > 0){
		  
		  $esign_id = $wpdb->insert_id;
		 
	  }
	  
	  return $updated_esign;	
	
}
/**
 * Add Electronic Signature
 * 
 * @param string $signed_by who signed the agrement 
 * @param string $reason reason for sign aggrement
 * @param string $sign_date signature date
 * @param string $license_url license url
 * @param string $token token of license
 * @param string $sign_status status of signature
 * @param string $view_date view date of license
 * @param string $signed_email email address of the person who signed the license
 * @param string $sign_ip ip of license
 * @param integer $post_id id of esign
 * @param boolean $license_id id of the license
 * @param string $esign_html render html of esign
 * 
 * @return integer
 */
function wpclink_add_esign($signed_by = NULL, $reason = NULL, $sign_date = 0, $view_date = 0, $token = NULL, $signed_email = NULL, $sign_ip = NULL, $post_id = 0, $license_id = '0', $esign_html = NULL){
	
	global $wpdb;
	// Table Prefix
	$table_esign = $wpdb->prefix . 'wpclink_esigns';
	
		
	$table_esign = $wpdb->insert($table_esign, 
	  array( 
		  'esign_by' => $signed_by, 
		  'esign_reason' => $reason,
		  'esign_date' => $sign_date,
		  'view_date' => $view_date,
		  'esign_email' => $signed_email,
		  'esign_IP' => $sign_ip,
		  'post_id' => $post_id,
		  'esign_html' => $esign_html,
		  'esign_key' => $token,
		  'license_id' => $license_id
	  ));
	  
	  if($table_esign > 0){
		  
		  return $esign_id = $wpdb->insert_id;
		 
	  }
	  
	  return $table_esign;	
}
/**
 * CLink Electronic Signature Print
 * 
 * @param string $signed_by username whos signed the signature
 * @param string $get_reason reason for signature
 * @param string $stamp_time datetime of electronic signature
 * @param string $esign_copyright_identifier clink.id identifer of the user
 * 
 */
function wpclink_print_esign_linked($signed_by = NULL, $get_reason = 'ldc', $stamp_time = NULL, $esign_copyright_identifier = NULL){
	
	if($get_reason  == 'ldc'){
		$reason = 'Licensing  digital  content';	
	}
	// HTML
	$esign_html = '<div id="esign-stamp" style=" float:right; position:relative; -moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; -o-user-select: none;user-select: none; width:460px; display: inline-block;padding: 15px;"><div style="position:relative; z-index:5; width:50%; float: left; "><h2 style="font-family: Times New Roman, Times, serif;font-size: 30px;font-style: italic;font-weight: normal;margin: 20px 0 0;text-align: center;">'.$signed_by.'</h2><h5 style="margin:0; font-size:11px; text-align:center;text-indent: 8px;">'.$esign_copyright_identifier.'</h5></div><div style="width:50%; float: right; position:relative; z-index:5; "><h5 style="margin: 0;line-height: 24px;font-size: 12px; font-family:arial; ">Electronically  signed  by: '.$signed_by.'</h5><h5 style="margin: 0;line-height: 24px;font-size: 12px; font-family:arial; ">Reason: '.$reason.' </h5><h5 style="margin: 0;line-height: 24px;font-size: 12px; font-family:arial; ">Date: '.$stamp_time.'</h5></div><img style="position:absolute; z-index:1; top:10px; left:160px;"  src="[esign_watermark]" /></div>';
	
	$esign_html = '<div id="esign-placement">'.$esign_html.'</div>';
	return $esign_html;
	
}
/**
 * Electronic Signature Print for license template
 * 
 * @param string $signed_by username whos signed the signature
 * @param string $get_reason reason for signature
 * @param string $stamp_time datetime of electronic signature
 * @param string $esign_copyright_identifier clink.id identifer of the user
 * 
 */
function wpclink_print_esign_referent($signed_by = NULL, $get_reason = 'ldc', $stamp_time = NULL, $esign_copyright_identifier){
	
	// License Class
	if($get_reason  == 'ldc'){
		$reason = 'Licensing  digital  content';	
	}
	// HTML
	$esign_html = '<div id="esign-stamp" style="float:right; position:relative; -moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; -o-user-select: none;user-select: none; width:460px; display: inline-block;padding: 15px;"><div style="position:relative; z-index:5; width:50%; float: left; "><h2 style="font-family: Times New Roman, Times, serif;font-size: 30px;font-style: italic;font-weight: normal;margin: 20px 0 0;text-align: center;">'.$signed_by.'</h2><h5 style="margin:0; font-size:11px; text-align:center;text-indent: 8px;">'.$esign_copyright_identifier.'</h5></div><div style="width:50%; float: right; position:relative; z-index:5; "><h5 style="margin: 0;line-height: 24px;font-size: 12px; font-family:arial; ">Electronically  signed  by: '.$signed_by.'</h5><h5 style="margin: 0;line-height: 24px;font-size: 12px; font-family:arial; ">Reason: '.$reason.' </h5><h5 style="margin: 0;line-height: 24px;font-size: 12px; font-family:arial; ">Date: '.$stamp_time.'</h5></div><img style="position:absolute; z-index:1; top:10px; left:160px;"  src="[esign_watermark]" /></div>';
	$esign_html = '<div id="esign-placement">'.$esign_html.'</div>';
	return $esign_html;
	
}

/**
 * Get Electronic Signature ID by Taken value
 * 
 * @param string $token token value
 * 
 * @return array
 */
function wpclink_get_esign_by_token($token = 0){
	
	global $wpdb;
		// Table Prefix
	$table_esign = $wpdb->prefix . 'wpclink_esigns';
	
	$esign = $wpdb->get_row( "SELECT * FROM $table_esign WHERE esign_key = '$token'", ARRAY_A );
	
	return $esign;
	
}
/**
 * Get Electronic Signature ID by License ID
 * 
 * @param string $license_id license id
 * 
 * @return array
 */
function wpclink_get_esign_by_license_id($license_id = 0){
	
	global $wpdb;
		// Table Prefix
	$table_esign = $wpdb->prefix . 'wpclink_esigns';
	
	$esign = $wpdb->get_row( "SELECT * FROM $table_esign WHERE license_id = '$license_id'", ARRAY_A );
	
	return $esign;
	
}
