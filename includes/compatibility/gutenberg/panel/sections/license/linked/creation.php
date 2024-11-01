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

	$clink_id = get_post_meta($post_id,'wpclink_creation_ID',true);
?>

// License Class // On load
jQuery( ".plugin-sidebar-content-type").html( '<div class="accordion license-box"><h3>License <?php echo $license_external_link; ?></h3><div class="none-slot license-slot"><div class="license-version-slot"><?php if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){ echo '<a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$license_rights_transaction_ID.'">'.wpclink_do_icon_clink_ID($license_rights_transaction_ID).'</a>'; } ?></div><div><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div></div></div></div>' ).show();

<?php
								  
}