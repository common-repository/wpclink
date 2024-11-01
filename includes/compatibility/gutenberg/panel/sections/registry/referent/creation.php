<?php
/**
 * CLink Registry Referent
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
$clink_id = get_post_meta($post_id,'wpclink_creation_ID',true);
 if(!empty($clink_id)){
					// CLink ID
				 
				 
				 	if($iscc_status = get_post_meta($post_id,'wpclink_iscc_status',true)){
						 $iscc_label = 'Update';
					 }else{
						 $iscc_label = 'Add'; 
					 }
					if($clink_versions = get_post_meta( $post_id, 'wpclink_versions', true )){
						 $show_version_button = 'show';
					 }else{
						 $show_version_button = 'hide';
					}
	 
	 			 $current_user_id = get_current_user_id();
	 
				 if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator')){
						 $make_version = 'make_version';
						 $disabled = '';
						 $iscc_generate = 'onclick=\"wpclink_generate_iscc()\"';
					 }else{
						 $make_version = '';
						 $disabled = 'disabled=\"disabled\"';
						 $iscc_generate = '';
					 }
				?>
			jQuery( ".clinksidebar" ).html( '<?php echo '<span class=\"clinkid\"><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$clink_id.'\" target=\"_blank\">'.wpclink_do_icon_clink_ID($clink_id).'</a></span>'; ?>' ); <?php
				
	 
	 		// Print Verions
			if(!empty($clink_id)){
				
				// Hook
				$archive_section = apply_filters('wpclink_clink_panel_button_section','',$post_id);
				
				echo 'jQuery( ".version-slot" ).html( "<div class=\"clink_post_id\"><span class=\"clinkico\"></span> <span class=\"version_label\">Versions</span><a  '.$disabled.' class=\"button cl-small '.$make_version.' \">Add</a> <a class=\"cl-small show_version '.$show_version_button.' \"><span class=\"dashicons dashicons-plus\"></span> See more</a><div class=\"all_versions\" style=\"display:none;\">'.$html.'</div><div class=\"small-label category iscc_wrap\"><span class=\"iscc_label\">ISCC</span><a  '.$disabled.' class=\"button cl-small generate_iscc components-button iscc-04 \" '.$iscc_generate.'>'.$iscc_label.'</a></div></div>'.$archive_section.'<input type=\"hidden\" id=\"cl_version\" name=\"cl_version\" value=\"0\" /> " );';
			}
				 
				 
				 // Get Versiion by Ajax Request
				echo "wpclink_get_versions_ajax('".$post_id."');";
	 
	 
	 	 
 }
				 