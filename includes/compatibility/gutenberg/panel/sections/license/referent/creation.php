<?php
/**
 * CLink License Referent Creation
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

						
$license_box ='<div>Personal | Version: 0.9</div><div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div><span class="small-label  category onwire"><a href="#" onclick="cl_present_popup()">Right Categories</a></span>';


if(wpclink_post_in_post_referent_list($post_id)){

	$pre_auth_effect_date = get_post_meta($post_id,'wpclink_reuse_pre_auth_effective_date',true);	

	$license_box.= '<div class="license-date-slot">';

		if(empty($pre_auth_effect_date)){
			$license_box.=  'N/A'; 
		}else{
			$license_box.=  '<span class="incolor"><strong>Date: <span class="icon-box" title="Pre-Authorized Date"></span> </strong>  '.wpclink_convert_date_to_iso($pre_auth_effect_date).'</span>'; 
		}
	$license_box.=  '</div>';

}
?>
// License Class // On load
jQuery( ".plugin-sidebar-content-type").html( '<div class="accordion license-box"><h3>License <?php echo $license_external_link; ?></h3><div class="none-slot license-slot"><?php echo $license_box; ?></div></div>' ).show();

jQuery( ".canonical-slot-box" ).html( '<span class="clinksidebar"><?php if(empty($license_data['verification_status'])){
		echo 'N/A'; 
	}else{ 
		if($license_data['verification_status'] == 'pass'){
			echo 'Good Standing'; 
		}elseif($license_data['verification_status'] == 'fail'){
			echo 'Voilated'; 
		} 
	} ?></span>' );
<?php 
