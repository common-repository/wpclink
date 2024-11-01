<?php 
/**
 * CLink Error Handler Functions 
 *
 * CLink error dialoge and handlers
 *
 * @package CLink
 * @subpackage System
 */
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Hub Errors List
 * 
 * @return array error list
 */
function wpclink_clink_hub_errors(){
	
	$errors_list = array(
		'domain_not_found' => array(
							'error_headline' => 'Request cannot be completed',
							'error_type' => 'clink',
							'error_location' => 'hub',
							'error_text' => 'Secured key associated with the content is missing or not matching with the registry records. Please contact support.',
							'error_message' => 'CLink Error : Creation Access Key Mismatch',
							'error_code' => '1102'
		),
		'domain_id_missing' => array(
							'error_headline' => 'Request cannot be completed',
							'error_type' => 'clink',
							'error_location' => 'hub',
							'error_text' => 'Domain is not register or domain is missing with the registry records. Please contact support.',
							'error_message' => 'CLink Error : Domain is Missing',
							'error_code' => '1103'
		)
	);
	
	return $errors_list;
	
}
/**
 * CLink Response Check
 * 
 * @return mixed boolean | array 
 */
function wpclink_response_check($reponse_body){
	
	if(array_key_exists($reponse_body['status'],wpclink_clink_hub_errors())){
		
		$error_list = wpclink_clink_hub_errors();
		$error_list_data = $error_list[$reponse_body['status']];
		
		$response_back = array(
			'complete' => 'failed',
			'status' => '',
			'code' => $error_list_data['error_code'],
			'error_type' => $error_list_data['error_type'],
			'error_location' => $error_list_data['error_location'],
			'error_message' => $error_list_data['error_message'],
			'error_headline' => $error_list_data['error_headline'],
			'error_text' => $error_list_data['error_text'],
			'clink_error_status' => '',
			'clink_internal_error_code' => '',
			'clink_internal_error_location' => '',
			'message' => $error_list_data['error_message'],
			'data' => array()
		);
		
		return $response_back;
		
		
	}else if(isset($reponse_body['is_error']) and $reponse_body['is_error'] == 1){
			
		$response_back = array(
			'complete' => 'failed',
			'status' => '',
			'code' => $reponse_body['error_code'],
			'error_type' => $reponse_body['error_type'],
			'error_location' => $reponse_body['error_location'],
			'error_message' => $reponse_body['error_message'],
			'error_headline' => __( 'Service is temporarily unavailable', 'cl_text' ),
			'error_text' => __( 'Please try again later! <br> If the problem persist please contact support.', 'cl_text' ),
			'clink_error_status' => '',
			'clink_internal_error_code' => '',
			'clink_internal_error_location' => '',
			'message' => $reponse_body['error_message'],
			'data' => array()
		);
		
		return $response_back;
			
		
	}else{
		
		return false;
	}
}
/**
 * CLink Exif Tool Error Checker
 * 
 * @return void
 */
function wpclink_exiftool_error($attachment_id = 0){
	
	if($attachment_id == 0) return false;
	
	$response_back = array(
			'complete' => 'failed',
			'status' => '',
			'code' => 'EXIF',
			'error_type' => 'exiftool',
			'error_headline' => __( 'ExifTool Error', 'cl_text' ),
			'error_text' => __( 'Please try again later! <br> If the problem persist please contact support.', 'cl_text' ),
			'clink_error_status' => '1104',
			'clink_internal_error_code' => '',
			'clink_internal_error_location' => '',
			'message' => 'ExifTool error',
			'data' => array()
		);
		
		
		update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
		update_post_meta( $attachment_id, 'wpclink_loader_error_data', $response_back );
	
			
}
/**
 * CLink WP Error Return
 * 
 * @return array error
 */
function wpclink_return_wp_error($response){
	
	if(isset($response->errors['http_request_failed'])){
		
		$response_back = array(
			'complete' => 'failed',
			'status' => '',
			'code' => 'cURL-28',
			'error_type' => 'timout',
			'error_headline' => __( 'Service is temporarily unavailable', 'cl_text' ),
			'error_text' => __( 'Please try again later! <br> If the problem persist please contact support.', 'cl_text' ),
			'clink_error_status' => '',
			'clink_internal_error_code' => '',
			'clink_internal_error_location' => '',
			'message' => $response->errors['http_request_failed'][0],
			'data' => array()
		);
		
		return $response_back;
		
	}else{
		
		$response_back = array(
			'complete' => 'failed',
			'status' => '',
			'code' => '',
			'error_type' => 'internal',
			'error_headline' => __( 'Service is temporarily unavailable', 'cl_text' ),
			'error_text' => __( 'Please try again later! <br> If the problem persist please contact support.', 'cl_text' ),
			'clink_error_status' => '',
			'clink_internal_error_code' => '',
			'clink_internal_error_location' => '',
			'message' => 'Internal Error',
			'data' => array()
		);
		return $response_back;
	}
	
}
/**
 * CLink Return API Response from Server
 * 
 * @return mixed boolean | array
 */
function wpclink_return_api_reponse($response = array()){
	
	// Response Code
	$server_status_code = $response['response']['code'];
	// Response Message
	$server_status_message = $response['response']['message'];
	// Range 500 - 599
	$server_error_range = range(500,599);
	
	if(in_array($server_status_code,$server_error_range)){
		$response_back = array(
			'complete' => 'failed',
			'status' => '',
			'code' => $server_status_code,
			'error_type' => 'server',
			'error_headline' => __( 'Service is temporarily unavailable', 'cl_text' ),
			'error_text' => __( 'Please try again later! <br> If the problem persist please contact support.', 'cl_text' ),
			'clink_error_status' => '',
			'clink_internal_error_code' => '',
			'clink_internal_error_location' => '',
			'message' => $server_status_message,
			'data' => array()
		);
		return $response_back;
		
	}else{
		return true;
	}
	
}