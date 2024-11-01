<?php
/**
 * CLink Registry Section
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

	// Content is Linked List
		if(wpclink_check_license_by_post_id($post_id) > 0){  ?>		

				<?php // Remove if has
				echo 'jQuery(".non-editable").remove(); ';
	
				// Referent 
				echo' jQuery(".interface-complementary-area").before( "<div class=\"non-editable\"><strong>Referent Creation</strong> <br /> <a href=\"https://docs.clink.media/wordpress-plugins/creations/referent-creation#ReferentCreation-REFERENTCREATIONISUNEDITABLEONCEITISLINKED!\" target=\"_blank\" >Why is it uneditable? </a></div>" ); ';
	
		// Referent Content 
		}elseif($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){ ?>
	
				<?php echo 'jQuery(".non-editable").remove();';
																							 
	
				// Referent Message
				echo 'jQuery(".interface-complementary-area").before( "<div class=\"non-editable\"><strong>Linked Content</strong> <br /> <a href=\"https://docs.clink.media/wordpress-plugins/creations/linked-creation#LinkedCreation-LINKEDCREATIONCANBEEDITEDONLYACCORDINGTOITSLICENSE\" target=\"_blank\" >Why is it uneditable? </a></div>" );';
																							 
		}else{

		if($content_register_restrict = get_post_meta($post_id,'wpclink_post_register_status', true)){
			if($content_register_restrict == '1'){
				$post_register_status_flag_restrict = 1;
			}else{
				$post_register_status_flag_restrict = 0;
			}
		}else{
			$post_register_status_flag_restrict = 0;
		}
		$show_registration = wpclink_get_option('show_registration');
		$show_linked_mode = wpclink_get_option('show_linked_mode');
			 
			 

		if($post_register_status_flag_restrict == 0){

			
			if($show_registration == 1){
				
				echo 'jQuery(".set-registration").remove();';
				
				// Set to Register
				echo 'jQuery(".interface-complementary-area").before( "<div class=\"set-registration\"><strong>';
				
				if(wpclink_import_mode() and ($show_linked_mode == 1)) echo 'Linked Mode | ';
				
				echo 'Registration Enabled</strong></div>" );';
			
			 }else if($show_linked_mode ==1 and wpclink_import_mode()){
				
				echo 'jQuery(".set-registration").remove();';
				
				// Set to Register
				echo 'jQuery(".interface-complementary-area").before( "<div class=\"set-registration\"><strong>Linked Mode</strong></div>" );';
				
			 }
			}else if($show_linked_mode ==1 and wpclink_import_mode()){
			
				echo 'jQuery(".set-registration").remove();';
				// Set to Register
				echo 'jQuery(".interface-complementary-area").before( "<div class=\"set-registration\"><strong>Linked Mode</strong></div>" );';
			}
		} 