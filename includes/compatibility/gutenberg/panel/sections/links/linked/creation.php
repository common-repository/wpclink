<?php

/**
 * CLink Links Linked Creation
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


// Linked
if($referent_id = get_post_meta( $post_id, 'wpclink_referent_creation_ID', true )){
	
	if(empty($referent_id)){
		$referent_clinkid = 'N/A';
	}else{
		$referent_clinkid = '<a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$referent_id.'">'.wpclink_do_icon_clink_ID($referent_id).'</a>';
	}
	if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
		$referent_url = '<a target="_blank" href="'.$sync_origin.'">'.$sync_origin.' <span class="dashicons dashicons-external"></span></a>';
	}	
}


?>

// Links
jQuery( ".plugin-sidebar-content-links" ).html( '<div class="accordion links-box"><h3>Links</h3><div><span class="small-label underline">Referent Creation</span><div class="ref-clinkid-slot"><?php echo $referent_clinkid; ?></div><div class="ref-urls-slot"><?php echo $referent_url; ?></div></div></div>' );

<?php 
// EOF