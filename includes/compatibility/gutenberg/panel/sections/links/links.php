<?php
/**
 * CLink Links
 *
 * CLink panel registry section 
 *
 * @package CLink
 * @subpackage Link Manager
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$current_screen = get_current_screen();

// Global Post IO
if(isset($_GET['post'])){
    $post_id = $_GET['post'];
}else{
    $post_id = '';
}
		
if(wpclink_check_license_by_post_id($post_id) > 0){ 
	
	
	
	//  Referent Creation
	include('referent/creation.php'); 
	
	
	
	
 }else if($referent_id = get_post_meta( $post_id, 'wpclink_referent_creation_ID', true )){ 


	//  Linked Creation
	include('linked/creation.php'); 



 } 