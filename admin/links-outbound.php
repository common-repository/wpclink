<?php
/**
 * CLink Links Linked
 *
 * CLink links linked admin page.
 *
 * @package CLink
 * @subpackage System
 */
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Register my agreemnets menu page on admin menus
add_action('admin_menu', 'wpclink_register_links_outbound_menu');
/**
 * CLink My Agreements Menu 
 * 
 */
function wpclink_register_links_outbound_menu(){
	
	// Users
	$creator_array = wpclink_get_option('authorized_creators');
	$current_user_id = get_current_user_id();
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	// User Capability Check
	if(current_user_can('administrator') and wpclink_user_has_creator_list($current_user_id)){
		
		if(wpclink_import_mode() || wpclink_both_mode()){
			add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'manage_options', 'clink-links-outbound', 'wpclink_display_links_outbound_page' );
		}
		
	}elseif( current_user_can('administrator') and $current_user_id == $clink_party_id){
		
		if(wpclink_import_mode() || wpclink_both_mode()){
			add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'manage_options', 'clink-links-outbound', 'wpclink_display_links_outbound_page' );
		}
	
	}elseif(wpclink_user_has_creator_list($current_user_id)){
	
	
        if(wpclink_import_mode() || wpclink_both_mode()){
			add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'edit_posts', 'clink-links-outbound', 'wpclink_display_links_outbound_page' );
		}
		
	}elseif($current_user_id == $clink_party_id){
		
		if(wpclink_import_mode() || wpclink_both_mode()){
			add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'edit_posts', 'clink-links-outbound', 'wpclink_display_links_outbound_page' );
		}
		
	}else{
		
		if(wpclink_import_mode() || wpclink_both_mode()){
			add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'manage_options', 'clink-links-outbound', 'wpclink_display_links_outbound_page' );
		}
		
	}
		
} 
/**
 * CLink My Agreements Admin Page Render
 * 
 */
function wpclink_display_links_outbound_page(){
	
	global $select_options, $radio_options;
	
	$current_user_id = get_current_user_id();
	$creator_array = wpclink_get_option('authorized_creators');
	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;
		
	?>
<div class="wrap">
  <?php wpclink_display_tabs_links(); ?>
 <div class="cl_subtabs_box">
  <?php
	global $wpdb;
	
	$cl_my_agrements_page = menu_page_url( 'clink-links-outbound', false ); 
	
	
	$request = $_GET;
	$cencel_id = (isset($request['cencel_agr'])) ? $request['cencel_agr'] : '';
	
	if(isset($cencel_id) and is_numeric($cencel_id)){
		
		
		
		if(wpclink_is_acl_r_page() || !wp_verify_nonce( $_GET['nonces'], 'wpclink_cancel_link')){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{

			$get_primary_site = wpclink_get_option('primary_linked_site');

			if('-'.$cencel_id == $get_primary_site){

				// If ID deleted goes to default
				wpclink_update_option('primary_linked_site','super');
			}

			$agree_del = wpclink_get_license_linked($cencel_id);

			$request_query['client_site'] = urlencode(get_bloginfo('url'));
			$request_query['s_agree'] = 1;
			$request_query['cl_action'] = 'disconnect';
			$build_query = build_query( $request_query );


			// COMPLETE QUERY
			$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
			$xml=file_get_contents($agree_del['site_url'].'?'.$build_query,false,$context);

			$xml = simplexml_load_string($xml);
			$cencel_agree = $xml->channel->cencel_agree;


			$deleted_agr = wpclink_delete_license_linked($cencel_id);
		}
	
	}
	
	// Audit Trial
	$audit_trial_page = menu_page_url( 'cl-audit-trail', false );
	
	// Query
	$agreements = wpclink_get_all_license_linked();
	
	
	
	
	// Array Reverse
	$agreements = array_reverse($agreements);
	
	$deleted_agr = (isset($deleted_agr)) ? $deleted_agr : '';
	if ( true == $deleted_agr ) : ?>
  <div class="updated fade">
    <p><strong>
      <?php _e( 'License has been terminated!', 'cl_text' ); ?>
      </strong></p>
  </div>
  <?php endif; ?>
  
  <?php 
	$cencel_agree = (isset($cencel_agree)) ? $cencel_agree : '';
	if ( $cencel_agree == 1) : ?>
  <div class="updated fade">
    <p><strong>
      <?php _e( 'License has been terminated! link has been Unlink', 'cl_text' ); ?>
      </strong></p>
  </div>
  <?php endif; ?>
  <table class="wp-list-table widefat fixed striped links">
    <thead>
      <tr>
       <th scope="col" class="manage-column column-content-url column-content-url"><span>
          <?php _e('URL','cl_text'); ?>
          </span></th>
		
		<th scope="col" id="referent" class="manage-column column-referent column-referent"><span class="clinkico-24-text">
          <?php _e("Referent",'cl_text'); ?>
		</span></th>
        <th scope="col" id="ip" class="manage-column column-name column-primary"><span>
          <?php _e("Site IP",'cl_text'); ?>
          </span></th>
		 <th scope="col" class="manage-column column-site-url column-site-url"><span>
          <?php _e('Site URL','cl_text'); ?>
          </span></th>
		 <th scope="col" class="manage-column column-license"><span class="clinkico-24-text">
          <?php _e('License ','cl_text'); ?>
          </span></th>
        <th scope="col" class="manage-column column-agreement"><span>
          <?php _e('License Status','cl_text'); ?>
          </span></th>
		 <th scope="col" class="manage-column column-type"><span>
          <?php _e('Type ','cl_text'); ?>
          </span></th>
        <th scope="col" class="manage-column column-documents"><span>
          <?php _e('Documents ','cl_text'); ?>
          </span></th>
        <th scope="col" class="manage-column column-date"><span>
          <?php _e('Date ','cl_text'); ?>
          </span></th>
      </tr>
    </thead>
    <tbody id="the-list" data-wp-lists="list:link">
      <?php
    if($agreements){
	foreach($agreements as $single_agreement) {
		$single_license_id = $single_agreement['license_id'];
		$token = wpclink_get_license_meta($single_license_id,'license_token',true);
		$download_id = wpclink_get_license_meta($single_license_id,'license_download_id',true);
		$esign_data = wpclink_get_esign_by_token($token);
		
		$ref_uri =  get_post_meta($single_agreement['post_id'],'wpclink_referent_post_uri',true);
		
	?>
      <tr>
		<td class="content-url column-content-url" data-colname="Content">
        <a class="row-title" target="_blank" href="<?php echo $ref_uri; ?>"><?php echo $ref_uri; ?></a><div class="row-actions"><span class="delete"><a href="<?php print wp_nonce_url( $cl_my_agrements_page.'&cencel_agr='. $single_agreement['license_id'], 'wpclink_cancel_link', 'nonces'); ?>">Unlink</a></span></div></td>
		  
		<td class="referent column-referent" data-colname="Referent"><?php
		$get_post_id = wpclink_get_license_id_by_referent_post_id($single_agreement['referent_post_id'],$single_agreement['site_url']);
		
		if($referent_creation_id = get_post_meta($get_post_id,'wpclink_referent_creation_ID',true)){
			echo wpclink_add_hyperlink_to_clink_ID_no_prefix($referent_creation_id);
		}else{
			echo 'N/A';
		}
		?></td>
        <td class="ip column-ip" data-colname="IP"><em><?php echo $single_agreement['site_IP']; ?></em></td>
		  <td class="name column-site-url " data-colname="Site Address"><?php echo $single_agreement['site_url']; ?> <a target="_blank" href="<?php echo $single_agreement['site_url']; ?>"><span class="dashicons dashicons-external"></span></a></td>
		<td class="agreement column-license" data-colname="License"><?php echo wpclink_add_hyperlink_to_clink_ID_no_prefix($single_agreement['rights_transaction_ID']); ?></td>
		  
        <td class="agreement column-agreement" data-colname="Agreement"><em>
          <?php if($single_agreement['verification_status'] == 'pass'){ echo 'Good Standing '; }else { echo 'Violation'; } ?>
          </em></td>
		  <td class="license column-type" data-colname="date">
     <?php 
		$type =  get_post_type($single_agreement['post_id']);
		$type_obj = get_post_type_object( $type );
		echo $type_obj->labels->singular_name;  ?>
      </td>
		  
          <td class="license column-documents" data-colname="documents">
      <a target="_blank" href="<?php bloginfo('url'); ?>?license_my_show_id=<?php echo $single_agreement['license_id']; ?>&download_id=<?php echo $download_id; ?>" class="button"><?php _e('View'); ?></a><br />
<a href="<?php bloginfo('url'); ?>?license_my_show_id=<?php echo $single_agreement['license_id']; ?>&download=1&download_id=<?php echo $download_id; ?>" class="button-primary"><?php _e('Download'); ?></a>
      </td>
      <td class="license column-date" data-colname="date">
      Linked<br>
	 <span title="<?php echo date('m/d/Y h:i:s A', strtotime($single_agreement['license_date'])); ?>"><?php echo date('m/d/Y', strtotime($single_agreement['license_date'])); ?></span>
      </td>
      </tr>
      <?php } 
	} else{
		echo '<tr><td colspan="9">'.__('No Agreement Found!','cl_text').'</td></tr>';
	}?>
    </tbody>
    <tfoot>
      <tr>
      <th scope="col" class="manage-column column-content column-content-url"><span>
          <?php _e('URL','cl_text'); ?>
          </span></th>
	<th scope="col" class="manage-column column-referent"><span class="clinkico-24-text">
          <?php _e('Referent','cl_text'); ?>
          </span></th>
        <th scope="col" id="ip" class="manage-column column-name column-primary"><span>
          <?php _e("Site IP",'cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-site-url">
          <?php _e('Site URL','cl_text'); ?>
          </th>
		  <th scope="col" class="manage-column column-license"><span class="clinkico-24-text">
          <?php _e('License','cl_text'); ?>
          </span></th>
        <th scope="col" class="manage-column column-agreement"><span>
          <?php _e('License Status','cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-type"><span>
          <?php _e('Type','cl_text'); ?>
          </span></th>
        <th scope="col" class="manage-column column-documents"><span>
          <?php _e('Documents','cl_text'); ?>
          </span></th>
           <th scope="col" class="manage-column column-date"><span>
          <?php _e('Date','cl_text'); ?>
          </span></th>
      </tr>
    </tfoot>
  </table>
</div>
<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
</div>
<?php
}
