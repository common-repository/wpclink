<?php
/**
 * CLink Links Referent Canonical
 *
 * clink link referent canonical verification admin page
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Canonical Verification Menu
 * 
 */
function wpclink_register_menu_links_inbound_canonical_verification(){
        if((wpclink_export_mode()  || (wpclink_both_mode()))){
				if(wpclink_is_acl_r_page()){
				}else{
		add_submenu_page('cl_mainpage.php', 'Canonical Verification', 'Canonical Verification', 'manage_options', 'cl-canonical', 'wpclink_display_links_inbound_canonical_verification' );
				}
		}
		
}
// Register canonical verification menu function
add_action('admin_menu', 'wpclink_register_menu_links_inbound_canonical_verification');
/**
 * CLink Canonical Verification Admin Page Render
 * 
 */
function wpclink_display_links_inbound_canonical_verification(){
	
	global $select_options, $radio_options;
	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;
		
	$link_url = get_permalink($_GET['content_id']);
	if(	$_GET['canonical_run_done'] == 1) wpclink_notif_print('Canonical has been Run','success');
	if(	$_GET['restore_done'] == 1) wpclink_notif_print('Link <strong>'.$link_url.'</strong> has been restored','success');
	
	$cl_my_agrements_page = menu_page_url( 'clink-links-inbound', false );
	if(isset($_GET['type'])){
		$cl_my_agrements_page = add_query_arg( 'type', $_GET['type'], $cl_my_agrements_page );
	}
	
	// Verify Canonical
	if(!wp_verify_nonce( $_GET['nonces'], 'wpclink_canonical_verify')){
		 wp_die('The link you followed is expired.');
	}
	
	?>
<div class="wrap cl-page">

<?php if(isset($_GET['cl_site_id'])){
		
			
				
			$site_id = $_GET['cl_site_id'];
			$content_id = $_GET['content_id'];
			global $wpdb;
			
			$site_data = wpclink_get_license_referent($site_id);
			
			$url_param = array();
			if(isset($_GET['cl_site_id'])){
				$url_param['cl_site_id'] = $_GET['cl_site_id'];
			}
			if(isset($_GET['content_id'])){
				$url_param['content_id'] = $_GET['content_id'];
			}
			
			$menu_page = menu_page_url( 'cl-canonical', false );
			
			$complete_url = add_query_arg($url_param,$menu_page);
			
			
			$url_param_canonical = $url_param;
			$url_param_canonical['canonical_run'] = 1;
			if(isset($_GET['type'])){
			$url_param_canonical['type'] = $_GET['type'];	
			}
			$run_canonical = add_query_arg($url_param_canonical,$menu_page);
			
			$url_param_restore = $url_param;
			$url_param_restore['restore'] = 1;
			$url_restore = add_query_arg($url_param_restore,$menu_page);
			
					
			
			// STATUS
			$canonical_fail_value = wpclink_get_option('verification_canonical_fail_threshold');
			$canonical_success_value = wpclink_get_option('verification_canonical_success_threshold');
			
			$clink_fails_attemps = wpclink_get_license_meta($site_data['license_id'],'wpclink_license_attemps',true);
			$clink_success_attemps = wpclink_get_license_meta($site_data['license_id'],'canonical_success_attemps',true);
			$creation_status = wpclink_get_license_meta($site_data['license_id'],'creation_status',true);
			if(empty($clink_success_attemps)) $clink_success_attemps = 0;
			if(empty($clink_fails_attemps)) $clink_fails_attemps = 0;
			$canonical_status = $site_data['verification_status'];
			
					
			$xml = simplexml_load_string($xml); ?>
<br />
<table class="wp-list-table widefat fixed striped links">
  <thead>
    <tr>
      <th scope="col" class="manage-column column-link column-primary"><span>
        <?php _e('Link','cl_text'); ?>
        </span></th>
      <th scope="col" id="status" class="manage-column column-status column"><span>
        <?php _e('Status','cl_text'); ?>
        </span></th>
      <th scope="col" id="operations" class="manage-column column-operations column"><span>
        <?php _e('Manual Operations','cl_text'); ?>
        </span></th>
    </tr>
  </thead>
  <tbody id="the-list" data-wp-lists="list:link">
    
    <?php if($canonical_status == 'pass'){ ?>
    <tr>
      <td class="name column-name has-row-actions column-primary" data-colname="Link"><a class="row-title" href="<?php echo 'post.php?post='.$content_id.'&action=edit'; ?>"><?php echo get_the_title($content_id); ?></a></td>
      <td class="ip column-status" data-colname="Status">
      <?php
										  
if($creation_status == 'notfound'||$creation_status == 'servererror'){
	echo 'Creation Not Found';
}else{
	
	if($clink_fails_attemps == 0){ ?><span class="cl_green">Found</span><?php }else{ ?>
      <span class="cl_red"><?php echo $clink_fails_attemps; ?> of required <?php echo $canonical_fail_value; ?> failed verifications before Link is removed</span>
      <?php }
} ?>
        </td>
      <td class="operations column-operations" data-colname="operations">
		  <?php if($creation_status == 'notfound'||$creation_status == 'servererror'){
	echo 'N/A';
}else{ ?><a class="button" href="<?php echo $run_canonical; ?>"><?php _e('Run','cl_text'); ?></a><?php } ?></td>
    </tr>
<?php }else{ ?>
    <tr class="site cl_bg_yellow">
      <td class="name column-name has-row-actions column-primary" data-colname="Link"><a class="row-title" href="<?php echo 'post.php?post='.$content_id.'&action=edit'; ?>"><?php echo get_the_title($content_id); ?></a></td>
      <td class="ip column-status" data-colname="Status">
      <?php 
if($creation_status == 'notfound'||$creation_status == 'servererror'){
	echo 'Creation Not Found';
}else{
	
if($clink_success_attemps == 0){ ?>
      <span class="cl_red">Removed</span>
      <?php }else{ ?>
      <span class="cl_green"><?php echo $clink_success_attemps; ?> of required <?php echo $canonical_success_value; ?> successful verifications for the Link to be restored</span>
      <?php } 
} ?></td>
      <td class="operations column-operations" data-colname="operations">
		  <?php if($creation_status == 'notfound'||$creation_status == 'servererror'){
	echo 'N/A';
}else{ ?><a class="button" href="<?php echo $run_canonical; ?>"><?php _e('Run','cl_text'); ?></a> <a class="button" href="<?php echo $url_restore; ?>"><?php _e('Restore','cl_text'); ?></a><?php } ?></td>
    </tr>
<?php } ?>
  </tbody>
  <tfoot>
    <tr>
      <th scope="col" class="manage-column column-link column-primary"><span>
        <?php _e('Link','cl_text'); ?>
        </span></th>
      <th scope="col" id="ip" class="manage-column column-status column"><span>
        <?php _e('Status','cl_text'); ?>
        </span></th>
      <th scope="col" id="ip" class="manage-column column-operations column"><span>
        <?php _e('Manual Operations','cl_text'); ?>
        </span></th>
    </tr>
  </tfoot>
</table>
<p><a class="button" href="<?php echo $cl_my_agrements_page; ?>"><?php _e('Back','cl_text'); ?></a></p>
<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
<?php }
}
