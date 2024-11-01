<?php

/**

 * CLink Notification Functions

 *

 * CLink all notification messages for admin pages functions

 *

 * @package CLink

 * @subpackage System

 */

 

 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Notification Print 
 *
 * @param string $msg Message for Print Notification
 * @param string $class_name notification type
 * 
 */
function wpclink_notif_print($msg = false, $class_name = false){
	
	$class = 'notice notice-'.$class_name.' is-dismissible';
	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $msg ); 
}
/**
 * CLink Add Class in Admin if Voilated Linked Site Canonical
 * 
 * @param  $classes default wordpress classes
 * 
 * @return string
 */
function wpclink_add_notice_class_canonical($classes){
	
	
	$screen = get_current_screen();
	if($screen->post_type == 'page'){
		if(isset($_GET['post'])){
			
			if($post_canonical_violation = get_post_meta($_GET['post'],'wpclink_canonical_violation')){
				return "$classes notice-canonical ";
			}
		}
	}elseif($screen->post_type == 'post'){
		if(isset($_GET['post'])){
			
			if($post_canonical_violation = get_post_meta($_GET['post'],'wpclink_canonical_violation')){
				return "$classes notice-canonical ";
			}
		}
	}	
	
	return "$classes ";
	
}
// Register canonical classes
add_filter( 'admin_body_class', 'wpclink_add_notice_class_canonical' );
/**
 * CLink Territory Selection Notification Message on all Admin Pages
 * 
 */
function wpclink_notify_territory_all_pages(){
	
	
	$pt = get_current_screen()->post_type;
	$territory = wpclink_get_option('territory_code');
	
	if(isset($_GET['page'])){
		$page = $_GET['page'];
	}else{
		$page = '';
	}
	
	if ( $pt == 'post' || $pt == 'page' || $page == 'cl_mainpage.php'){
	}else{
		if(empty($territory)){
			
			$current_user_id = get_current_user_id();
			$accept = get_user_meta( $current_user_id, 'wpclink_terms_accept', true );
			
			if($accept == false || $accept == 1){
							
				}else if($accept == 2){
				
				echo '<div class="notice notice-error is-dismissible"><p>Please select <strong>Territory</strong> before selecting CLink Users <a class="button" href="'.menu_page_url( 'cl_mainpage.php', false ).'">Select</a></p></div>';
			}
			
		}
	}
	
}
// Register territory notification message on all admin pages
add_action('admin_notices','wpclink_notify_territory_all_pages');
/**
 * CLink Grid View Notification
 * 
 */
function wpclink_media_grid_view_notice(){
	
	$media_library_mode = get_user_option( 'media_library_mode', get_current_user_id() );
	
	$screen = get_current_screen();
	$screen_id = $screen->id;
	
	 if($screen_id == 'media' || $screen_id == 'upload'){
		if($media_library_mode == 'grid'){
			echo '<div class="notice notice-info is-dismissible"><p>wpCLink features are supported through the <a href="'.esc_url( admin_url( 'upload.php?mode=list' ) ).'">List View.</a></p></div>';
		}
	 }
	
}
// Register grid view notification message on all admin pages
add_action('admin_notices','wpclink_media_grid_view_notice');
/**
 * CLink Classic Editor Notification
 * 
 */
function wpclink_classic_editor_notice(){
	
	$current_screen = get_current_screen();
	 if($current_screen->id == 'post' || $current_screen->id == 'page'){
		echo '<div class="notice notice-warning is-dismissible"><p>wpCLink features are not supported with the classic editor.</a></p></div>';
	 }
}
// Register classic editor notification message on all admin pages
add_action('admin_notices','wpclink_classic_editor_notice');

/**
 * CLink Media updated Notice
 * 
 */
function wpclink_media_updated_notice(){
	
	$screen = get_current_screen();
	$screen_id = $screen->id;
	
	 if($screen_id == 'attachment'){
		 
		 if(isset($_GET['media_updated']) and $_GET['media_updated'] == 1 ){

				 // Only Jpeg support CLink Plugin
				$media_type = get_post_mime_type($post_id);
				if($media_type == 'image/jpeg' || $media_type == 'image/jpg'){

					echo '<div class="notice notice-success is-dismissible cl-default-updated"><p>Default image updated.</p></div>';

				}
			}
			 
		 }
	 }
// Register only media support notification message on attachment pages
add_action('admin_notices','wpclink_media_updated_notice');
/**
 * CLink Metadata Only JPG Supports
 * 
 */
function wpclink_media_jpg_support(){
	
	$screen = get_current_screen();
	$screen_id = $screen->id;
	
	 if($screen_id == 'attachment'){
		 
		 if(isset($_GET['post'])){
		 	$post_id = $_GET['post'];
		 
			 // Only Jpeg
			$media_type = get_post_mime_type($post_id);
			if($media_type == 'image/jpeg' || $media_type == 'image/jpg'){
				
			}else{
				
				$filetype = wp_check_filetype(wpclink_get_image_URL($post_id));
			 	echo '<div class="notice notice-warning is-dismissible"><p>wpCLink features not yet supported on '.strtoupper($filetype['ext']).' files</p></div>';
				
				}
			}
			 
		 }
	 }
// Register only media support notification message on attachment pages
add_action('admin_notices','wpclink_media_jpg_support');
/**
 * CLink Media Feature Disabled Notice
 * 
 */
function wpclink_media_continue_to_clink_notices(){
	
	$screen = get_current_screen();
	$screen_id = $screen->id;
	
	 if($screen_id == 'attachment'){
		 
		 if(isset($_GET['post'])){
		 	$post_id = $_GET['post'];
		 
			 // Only Jpeg
			$media_type = get_post_mime_type($post_id);
			if($media_type == 'image/jpeg' || $media_type == 'image/jpg'){
				
								
				$continue_to_image = get_post_meta($post_id, 'wpclink_continue_to_image', true );
				if ( $continue_to_image == 1 ) {
					
					echo '<div class="notice notice-warning is-dismissible"><p>wpCLink features are not available because either the metadata of the image is incompatible with the installed version of the plugin or the image has been deleted.</p></div>';
						
					return false;
				}
			}
		 }
	 }
}
// Register clink media disable notice
add_action('admin_notices','wpclink_media_continue_to_clink_notices');				
/**
 * CLink Metadata and Rights Notices
 * 
 */
function wpclink_media_metadata_notice_right_holder(){
	
	$screen = get_current_screen();
	$screen_id = $screen->id;
	
	 if($screen_id == 'attachment'){
		 
		 if(isset($_GET['post'])){
		 	$post_id = $_GET['post'];
			 
			 // Current User
			$current_user_id = get_current_user_id();
			 
		 		
			 	// Origin Creator for Register Media
				if ( $creationID = get_post_meta( $post_id, 'wpclink_creation_ID', true ) ) {
					if($right_holder_id = get_post_meta( $post_id, 'wpclink_rights_holder_user_id', true )){
						if($right_holder_id != $current_user_id){
							
							$author_info = get_userdata($right_holder_id);
		 					$author_displayname = $author_info->display_name;
							
							echo '<div class="notice notice-warning is-dismissible"><p>'.$author_displayname.' claimed sole attribution and rights for this media and it is registered accordingly.  Attribution and rights cannot be changed with the current version of the wpCLink plugin..</p></div>';
						}
					}
				}
			 
			}
			 
		 }
	 }
// Register copyright metadata and rights information
add_action('admin_notices','wpclink_media_metadata_notice_right_holder');				
/**
 * CLink Metadata and Rights Notices
 * 
 */
function wpclink_media_metadata_notices(){
	
	$screen = get_current_screen();
	$screen_id = $screen->id;
	
	 if($screen_id == 'attachment'){
		 
		 if(isset($_GET['post'])){
		 	$post_id = $_GET['post'];
		 
			 // Only Jpeg
			$media_type = get_post_mime_type($post_id);
			if($media_type == 'image/jpeg' || $media_type == 'image/jpg'){
				
				$continue_to_image = get_post_meta($post_id, 'wpclink_continue_to_image', true );
				
			/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
				if ( $continue_to_image_final == 1 ) {
					return false;
				} 
				
				$linked_media = get_post_meta($post_id,'wpclink_referent_post_link',true);
				
				if(!empty($linked_media)){
					return false;
				}
				
					// Registration
					$registration_disallow = get_post_meta($post_id,'wpclink_registration_disallow',true);
					if($registration_disallow == 1){
						
						echo '<div class="notice notice-warning is-dismissible"><p>wpCLink features are not available when Licensor URL and Web Statement of Rights metadata already exist on the image.</p></div>';
						
						return false;
					}
				
					$creator_not_matach_warning = get_post_meta($post_id,'wpclink_creator_not_match_warning',true);
				
					if($creator_not_matach_warning == 1){
						
						echo '<div class="notice notice-warning is-dismissible"><p>wpCLink features are not available when the name of the Creator to be entered into the IPTC field does not match the name in the WordPress user profile.</p></div>';
						
						// Reset
						update_post_meta($post_id,'wpclink_creator_not_match_warning','0');
						return false;
					}
				
					// Image Path
					$attachment_url =  wpclink_iptc_image_path($post_id,'full');
					$attachment_meta = wp_get_attachment_metadata($post_id);
					// Only for Creator
					$current_user_id = get_current_user_id();
					$reg_creators = wpclink_get_option('authorized_creators');
					// Creator
					$creator_user_info = get_userdata($current_user_id);
					// Current Creator Names
					$current_creator_array = array(
						strtolower($creator_user_info->first_name),
						strtolower($creator_user_info->last_name)
					);
					$display_name = $creator_user_info->display_name;
					$display_name_array = explode(' ',$display_name);
					$display_name_array = array_map( 'strtolower', $display_name_array );
					// Meta data Creator Names
					$creator_name_metadata = wpclink_get_image_metadata_value($attachment_url,'IPTC:By-line');
				
					if(!empty($creator_name_metadata)){
						$metadata_creator_names = explode(' ',$creator_name_metadata);
						
			$creator_name_match = wpclink_match_creator_names(
								$metadata_creator_names,
								$current_creator_array,
								$display_name_array);
						
			/*
			* Apply the Creator Name Match
			*
			* - 'creator_name_match' is the value being filtered. */
			$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
						
						
							if( $creator_name_match_final ){
							}else{
								
								echo '<div class="notice notice-warning is-dismissible"><p>wpCLink features are not available when the name of the Creator on the image in the IPTC field does not match the name in the WordPress user profile.</p></div>';
							}
					}
				}
			}
			 
		 }
	 }
// Register copyright metadata and rights information
add_action('admin_notices','wpclink_media_metadata_notices');
/**
 * CLink Territory Selection Notification Message on all Admin Pages
 * 
 */
function wpclink_notify_ip_warning(){
	
		if(wpclink_get_ip_warnings()){
			
			if( isset($_GET['page']) and $_GET['page'] == 'clink-links-inbound'){
				$link = '';
			}else{
				$link = 'at <a href="'.menu_page_url( 'clink-links-inbound', false ).'">CLink > Links > Inbound menu </a>';
			}
			echo '<div class="notice notice-error is-dismissible"><p>The IP address of at least one licensee’s site has been changed since the license offer has been accepted. Please click on the <span class="dashicons dashicons-warning"></span> sign '.$link.' for further instructions. </p></div>';
		}
}
// Register territory notification message on all admin pages
add_action('admin_notices','wpclink_notify_ip_warning');
/**
 * CLink Domain Access Key Notification
 * 
 */
function wpclink_domain_access_key_notice(){
	
		$screen = get_current_screen();
		$screen_id = $screen->id;
	
		if($screen_id == 'clink_page_cl-clink') return;
	
	
		$party_id = wpclink_get_option('authorized_contact');
	
		if(!empty($party_id)){
			$domain_access_key = wpclink_get_option('domain_access_key');
			
			if(empty($domain_access_key)){
				echo '<div class="notice notice-error is-dismissible"><p>wpCLink features are not available, because the domain access key of your site cannot be verified. Please <a href="#" class="wpclink-support">contact</a> support.</p></div>';
				return;
			}
		}
}
// Register domain access key notification
add_action('admin_notices','wpclink_domain_access_key_notice');
/**
 * CLink Is Domain Access Key Not Exists
 * 
 */
function wpclink_is_domain_access_key_notexists(){
	
		$party_id = wpclink_get_option('authorized_contact');
	
		if(!empty($party_id)){
			$domain_access_key = wpclink_get_option('domain_access_key');
			
			if(empty($domain_access_key)){
				return true;
			}
		}
	
	return false;
}
/**
 * CLink Territory Selection Notification Message on all Admin Pages
 * 
 */
function wpclink_notify_ip_warning_update(){
	
		if(isset($_GET['update_ip_done']) and $_GET['update_ip_done'] == 1){
			echo '<div class="notice notice-success is-dismissible"><p>The IP has been updated. </p></div>';
		}
}
// Register territory notification message on all admin pages
add_action('admin_notices','wpclink_notify_ip_warning_update');


/**
 * CLink User Accept Terms and Condition for Firstime New User
 * 
 */
function wpclink_display_notification_esign_privacy_terms(){
	
	$current_user_id = get_current_user_id();
	$accept = get_user_meta( $current_user_id, 'wpclink_terms_accept', true );
		if($accept == false){
			echo '<div class="notice notice-error is-dismissible"><form action="" method="post"><p> <label><input type="checkbox" name="cl_user_accept_first" value="1"> I accept the <a href="'.WPCLINK_ID_URL.'/terms.html" target="_blank">Terms of Service</a> and <a  href="'.WPCLINK_ID_URL.'/privacy.html" target="_blank">Privacy Policy</a> of CLink Media, Inc. & <a class="electronic_record_btn_popup">Consent to use Electronic Records and Signatures.</a> </label><input type="submit" name="submit" value="Confirm and Continue" class="button"></p></form></div>';
		
		}elseif($accept == 1){
		echo '<div class="notice notice-error is-dismissible"><form action="" method="post"><p> <label><input type="checkbox" name="cl_user_accept" value="1"> I accept the <a href="'.WPCLINK_ID_URL.'/terms.html" target="_blank">Terms of Service</a> and <a  href="'.WPCLINK_ID_URL.'/privacy.html" target="_blank">Privacy Policy</a> of CLink Media, Inc. & <a class="electronic_record_btn_popup">Consent to use Electronic Records and Signatures.</a> </label><input type="submit" name="submit" value="Confirm and Continue" class="button"></p></form></div>';
		
		}
	
	
}
// Register user accept terms and condition
add_action('admin_notices','wpclink_display_notification_esign_privacy_terms');
/**
 * CLink Accept Popups
 * 
 */
function wpclink_display_popup_esign_privacy_terms() {
	$current_user_id = get_current_user_id();
	$accept = get_user_meta( $current_user_id, 'wpclink_terms_accept', true);
	if($accept == 1 || $accept == false){
		
echo '<script>jQuery( ".terms_btn_popup" ).click(function() { jQuery( "#terms_popup" ).dialog({maxWidth:600,maxHeight: 500,width: 600,height:500}); }); jQuery( ".privacy_btn_popup" ).click(function() { jQuery( "#privacy_popup" ).dialog({maxWidth:600,maxHeight:500,width:600,height:500}); }); jQuery( ".electronic_record_btn_popup" ).click(function() { jQuery( "#electronic_record_popup" ).dialog({maxWidth:600,maxHeight: 500,width: 600,height:500}); }); </script>';
		
echo '<div id="terms_popup" title="Terms and Conditions" style="display:none;">'.wpclink_get_option('terms_and_conditions').'</div>
<div id="privacy_popup" title="Privacy Policy" style="display:none;">
  <p>This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the icon.</p>
</div>
<div id="electronic_record_popup" title="Consent to use Electronic Records and Signatures." style="display:none;">
  <p>So long as you have not suspended licensing of the Creation(s) via the CLink plug-in, you authorize your electronic signature to automatically be applied to the selected standard License upon request from a Requester. In addition, you agree to such e-signed License to be delivered to the Requester along with a copy of the Creation(s) via the CLink Media, Inc. system. Upon receipt the Requester become a Licensee permitted to re-use the Creation subject to the terms and conditions set forth in the License. </p>
</div>';
	}
}
// Register accpet popups in footer
add_action('admin_footer', 'wpclink_display_popup_esign_privacy_terms');
