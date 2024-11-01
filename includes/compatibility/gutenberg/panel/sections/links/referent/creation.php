<?php
/**
 * CLink Links Referent Creaion
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


		$linked_creation_count = count($linked_creation_ID_list);

		$creation_label = ($linked_creation_count > 1)? 'Creations' : 'Creation';
		$html_creation_link = '';
		$count_linkeds = 0;

		foreach($linked_creation_ID_list as $single_links_creation){
			$html_creation_link .= '<a target="_blank" class="refid-hyperlink"  href="'.WPCLINK_ID_URL.'/#objects/'.$single_links_creation.'">'.wpclink_do_icon_clink_ID($single_links_creation).'</a>';



			if($count_linkeds >= 3) break;
			$count_linkeds++;
		}
			
	
			?>	
		// Links
		jQuery( ".plugin-sidebar-content-links" ).html( '<div class="accordion links-box"><h3>Links</h3><div><div class="ref-urls-slot"><span class="small-label underline">Linked <?php echo $creation_label; ?></span><?php echo $links_html; ?></div><div class="ref-clinkid-slot"><?php echo $html_creation_link; ?></div><span class="small-label underline seemore"><a href="<?php echo $inbound_menu; ?>"><span class="dashicons dashicons-plus"></span> See More</a></span></div></div>' );

<?php
// EOF