<?php
/**
 * CLink License Section
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

if($current_screen->id == "post" || $current_screen->id == "page"){
?>
// Basic Structure of Linked Content

jQuery( ".plugin-sidebar-content-type" ).html( '<div class="accordion license-box"><h3>License</h3><div class="none-slot license-slot"></div></div>').hide();

<?php

$clink_id = get_post_meta($post_id,'wpclink_creation_ID',true);


		if(!empty($clink_id)){
			
			if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){ 
		 		//  Linked Creation
				include('linked/creation.php'); 
			}

			if(wpclink_check_license_by_post_id($post_id) > 0){ 
				//  Referent Creation
				include('referent/creation.php'); 	
			}
			
		}
} 	
		