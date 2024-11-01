<?php
/**
 * CLink Links Referent
 *
 * CLink links referent admin page
 *
 * @package CLink
 * @subpackage System
 */
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
 
 

// Register site agreements menu page 
add_action('admin_menu', 'wpclink_register_menu_links_inbound');
/**
 * CLink Site Agreement Menu
 * 
 */
function wpclink_register_menu_links_inbound(){
	
	$creator_array = wpclink_get_option('authorized_creators');
	$current_user_id = get_current_user_id();
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	if(current_user_can('administrator') and wpclink_user_has_creator_list($current_user_id)){
		if(wpclink_export_mode() || wpclink_both_mode()){
		add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'manage_options', 'clink-links-inbound', 'wpclink_display_links_inbound' );
		}
		
	}elseif( current_user_can('administrator') and $current_user_id == $clink_party_id){
		if(wpclink_export_mode() || wpclink_both_mode()){
		add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'manage_options', 'clink-links-inbound', 'wpclink_display_links_inbound' );
		}
	
	}elseif(wpclink_user_has_creator_list($current_user_id)){
	
        if(wpclink_export_mode() || wpclink_both_mode()){
		add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'edit_posts', 'clink-links-inbound', 'wpclink_display_links_inbound' );
		}
	}elseif($current_user_id == $clink_party_id){
		if(wpclink_export_mode() || wpclink_both_mode()){
		add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'edit_posts', 'clink-links-inbound', 'wpclink_display_links_inbound' );
		}
	}else{
		if(wpclink_export_mode() || wpclink_both_mode()){
		add_submenu_page('cl_mainpage.php', 'Links', 'Links', 'manage_options', 'clink-links-inbound', 'wpclink_display_links_inbound' );
		}
		
	}
		
} 

/**
 * CLink Site Agreement Admin Page Render
 * 
 * 
 */
function wpclink_display_links_inbound(){
	
	global $select_options, $radio_options;
	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;
	
	if(isset($_GET['type'])){
		if($_GET['type'] == 'post'){
			$link_type = 'post';
		}else if($_GET['type'] == 'page'){
			$link_type = 'page';
		}else if($_GET['type'] == 'attachment'){
			$link_type = 'attachment';
		}
	}else{
		$link_type = 'post';
	}
		
	?>
	<div class="wrap cl-page">
    <?php wpclink_display_tabs_links_inbound(); ?>
      <div class="cl_subtabs_box">
  <?php
	global $wpdb;
	
	$cl_my_agrements_page = menu_page_url( 'clink-links-inbound', false );
	$cl_my_agrements_page = add_query_arg( 'type', $link_type, $cl_my_agrements_page);
	
	$audit_trial_page = menu_page_url( 'cl-audit-trail', false );
	
	$request = $_GET;
	$cencel_id = (isset($_GET['cencel_agr'])) ? $_GET['cencel_agr'] : '';
	
	if(isset($cencel_id) and is_numeric($cencel_id)){
		
	if(wpclink_is_acl_r_page()){
		wpclink_notif_print('Action cannot be perform.','error');
	}else{
		
	// Cencel
	$deleted_agr = wpclink_delete_license_referent($cencel_id);
	}
	}
	
	$mypost_id = (isset($_GET['post_id'])) ? $_GET['post_id'] : '';
	$post_id_filter = urldecode($mypost_id);
	
	// Query
	if(!empty($post_id_filter)){
		$agreements = wpclink_get_all_license_referent_by_filter($post_id_filter);
	}else{
		$agreements = wpclink_get_data_links_inbound_by_filters();
	}
	
	
	// Reverse Array
	$agreements = array_reverse($agreements);
	
	?>
    
    <?php 
	
	if(isset($deleted_agr)){
	}else{
		$deleted_agr = '';
	}
	
	if ( true == $deleted_agr ) : ?>
  <div class="updated fade">
    <p><strong>
      <?php _e( 'License is Unlinked', 'cl_text' ); ?>
      </strong></p>
  </div>
  <?php endif; ?>
<form id="posts-filter" method="get" url="<?php echo $cl_my_agrements_page; ?>">
<div id="links-filter">
		<div class="alignleft actions">
			<input name="page" type="hidden" value="clink-links-inbound" />
			<input name="type" type="hidden" value="<?php echo $link_type; ?>" />
			<label for="filter-by-site-url" class="screen-reader-text">Filter by site</label>
			<select name="site_url" id="filter-by-site-url">
				<option value="all">All Sites</option>
				<?php wpclink_display_menu_dropdown_filter_links_inbound('site_url'); ?>
			</select>
			<label class="screen-reader-text" for="cat">Filter by License Status</label>
			<select name="license_status" id="cat" class="postform">
				<option value="all" selected="selected">All License Status</option>
				<?php wpclink_display_menu_dropdown_filter_links_inbound('license_status'); ?>
			</select>
			<select name="delivery_status" id="delivery-status">
				<option value="all" selected="selected">All Delivery Status</option>
				<?php wpclink_display_menu_dropdown_filter_links_inbound('delivery_status'); ?>
			</select>
			<select name="creation_status" id="creation-status">
				<option value="all" selected="selected">All Creation Status</option>
				<?php wpclink_display_menu_dropdown_filter_links_inbound('creation_status'); ?>
			</select>
			<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
		</div>
	</div>
</form>  
  <table class="wp-list-table widefat fixed striped links">
    <thead>
      <tr>
      <th scope="col" class="manage-column column-content-url column-content-url"><span>
          <?php _e('Title','cl_text'); ?>
          </span></th>
		  
        	<th scope="col" class="manage-column column-name column-primary"><span class="clinkico-24-text"><?php _e("Linked Creation",'cl_text'); ?></span></th>
        	<th scope="col" id="ip" class="manage-column column-name column-primary"><span>
          <?php _e("Site IP",'cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-site-url column-site-url"><span>
          <?php _e('Site URL','cl_text'); ?>
          </span></th>
		   <th scope="col" class="manage-column column-license"><span class="clinkico-24-text">
          <?php _e('License','cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-license"><span>
          <?php _e('Documents','cl_text'); ?>
          </span></th>
        	<th scope="col" class="manage-column column-agreement"><span>
          <?php _e('License Status','cl_text'); ?>
          </span></th>		  
          <th scope="col" class="manage-column column-cstatus"><span>
          <?php _e('Delivery Status','cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-cstatus"><span>
          <?php _e('Creation Status','cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-canonical"><span>
          <?php _e('Verification','cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-date"><span>
          <?php _e('Date ','cl_text'); ?>
          </span></th>
      </tr>
      
    </thead>
    <tbody id="the-list" data-wp-lists="list:link">
      <?php
	$loop_counter = 0;
    if($agreements){
	foreach($agreements as $single_agreement) {
		$content_id = $single_agreement['post_id'];
		if(get_post_type($content_id) == $link_type){
			
		}else{
			continue;
		}
		
		$content_status = wpclink_get_license_meta($single_agreement['license_id'],'delivery_status',true);
		$creation_status = wpclink_get_license_meta($single_agreement['license_id'],'creation_status',true);
		$clink_contentID = get_post_meta($content_id,'wpclink_creation_ID',true);
		$esign_data = wpclink_get_esign_by_post_id($content_id);
		$clink_fails_attemps = wpclink_get_license_meta($single_agreement['license_id'],'wpclink_license_attemps',true);
		$clink_success_attemps = wpclink_get_license_meta($single_agreement['license_id'],'canonical_success_attemps',true);
		$ip_warning = wpclink_get_license_meta($single_agreement['license_id'],'site_ip_flag',true);
		if(empty($clink_success_attemps)) $clink_success_attemps = 0;
		if(empty($clink_fails_attemps)) $clink_fails_attemps = 0; 
		
		$licensee_linked_variables_serialized = wpclink_get_license_meta($single_agreement['license_id'],'licensee_linked_variables',true);
		$licensee_linked_variables = unserialize($licensee_linked_variables_serialized);
		
		
		$linked_creation_ID = wpclink_get_license_meta($single_agreement['license_id'],'linked_creation_ID',true);
		
		$loop_counter++;
		 ?>
      <tr class="site <?php if($single_agreement['verification_status'] != 'pass') echo "not-connect"; ?>">
      <td class="content-url column-content-url" data-colname="Content_URL"><strong><?php  ?><a class="row-title" href="<?php echo 'post.php?post='.$single_agreement['post_id'].'&action=edit'; ?>"><?php echo get_the_title($single_agreement['post_id']); ?></a></strong>
      </td>
		  
        <td class="name column-name has-row-actions column-primary" data-colname="Site Address"><?php echo wpclink_add_hyperlink_to_clink_ID_no_prefix($linked_creation_ID); ?></td>
        <td class="ip column-ip" data-colname="IP"><em><?php echo $single_agreement['site_IP']; ?></em> <?php if($ip_warning == 'yes') echo '<a data-license-id="'.$single_agreement['license_id'].'" data-licensor-site="'.$single_agreement['site_url'].'" data-licensee-email="'.$licensee_linked_variables['licensee_email'].'" class="ip_warning_action"><span class="dashicons dashicons-warning"></span></a>'; ?></td>
		  <td class="name column-site-url has-row-actions column-primary" data-colname="Site Address"><?php echo $single_agreement['site_url']; ?> <a target="_blank" href="<?php echo $single_agreement['site_url']; ?>"><span class="dashicons dashicons-external"></span></a></td>
		  <td class="agreement column-agreement" data-colname="Agreement"><?php echo wpclink_add_hyperlink_to_clink_ID_no_prefix($single_agreement['rights_transaction_ID']); ?></td>
		  <td class="license column-license" data-colname="license">
<a target="_blank" href="<?php bloginfo('url'); ?>?license_show_id=<?php echo $single_agreement['license_id']; ?>" class="button"><?php _e('View'); ?></a><br />
<a href="<?php echo $audit_trial_page; ?>&audit=<?php echo $single_agreement['license_id']; ?>&type=<?php echo get_post_type($content_id); ?>" class="button"><?php _e('Audit Trail'); ?></a><br />
<a href="<?php bloginfo('url'); ?>?license_show_id=<?php echo $single_agreement['license_id']; ?>&download=1" class="button-primary"><?php _e('Download'); ?></a>
      </td> 
		  <td class="agreement column-agreement" data-colname="Agreement"><em>
          <?php if($single_agreement['verification_status'] == 'pass'){ echo 'Good Standing'; }else { echo 'Violation'; } ?>
          </em></td>
		    
      <td class="license column-cstatus" data-colname="cstatus">
      <?php wpclink_get_license_delivery_status_label($content_status,true);?>
      </td>
	  <td class="license column-creation-status" data-colname="creation-status">
      <?php wpclink_get_license_creation_status_label($creation_status,true);?>
      </td>
	   <td class="canonical column-canonical" data-colname="canonical">
		<?php 
		if(get_post_type($content_id) == 'attachment'){
			echo 'N/A';
		}else{
		if($creation_status == 'notfound' || $creation_status == 'servererror'){
			 echo 'N/A';
		 }else{ ?>
		<a class="button" href="<?php print wp_nonce_url('admin.php?page=cl-canonical&cl_site_id='. $single_agreement['license_id'].'&content_id='.$content_id.'&type'. get_post_type($content_id), 'wpclink_canonical_verify', 'nonces'); ?>"><?php _e('Canonical','cl_text'); ?></a>	 
		<?php  }
		} ?>
	   </td>
		<td class="license column-date" data-colname="date">
      Linked<br>
	 <span title="<?php echo date('m/d/Y h:i:s A', strtotime($single_agreement['license_date'])); ?>"><?php echo date('m/d/Y', strtotime($single_agreement['license_date'])); ?></span>
      </td>
      </tr>
	 <?php }
		if($loop_counter == 0){
			echo '<tr><td colspan="11">'.__('No Link Found!','cl_text').'</td></tr>';
		}
	} else{
		echo '<tr><td colspan="11">'.__('No Link Found!','cl_text').'</td></tr>';
	}?>
    </tbody>
    <tfoot>
      <tr>
      <th scope="col" class="manage-column column-content-url column-content-url"><span>
          <?php _e('Title','cl_text'); ?>
          </span></th>
        <th scope="col" class="manage-column column-name column-primary"><span class="clinkico-24-text">
          <?php _e("Linked Creation",'cl_text'); ?>
          </span></th>
        <th scope="col" id="ip" class="manage-column column-name column-primary"><span>
          <?php _e("Site IP",'cl_text'); ?>
          </span></th>
		   <th scope="col" class="manage-column column-content-url column-site-url"><span>
          <?php _e('Site URL','cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-license"><span class="clinkico-24-text"><?php _e("License",'cl_text'); ?></span></th>
		   <th scope="col" class="manage-column column-license"><span ><?php _e("Documents",'cl_text'); ?></span></th>
		  <th scope="col" class="manage-column column-agreement"><span>
          <?php _e('License Status','cl_text'); ?>
          </span></th>	 
		   <th scope="col" class="manage-column column-cstatus"><span>
          <?php _e('Delivery Status','cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-creation-status"><span>
          <?php _e('Creation Status','cl_text'); ?>
          </span></th>
		  <th scope="col" class="manage-column column-canonical"><span>
          <?php _e('Verification','cl_text'); ?>
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
<script>
jQuery(document).ready(function(){
	   
	
		 jQuery(".ip_warning_action").click(function(){
			 
			 // License ID
			 var get_license_id = jQuery(this).attr("data-license-id");
			 
			 // License ID
			 var get_licensor_site = jQuery(this).attr("data-licensor-site");
			 
			 // License Email
			 var get_licensee_email = jQuery(this).attr("data-licensee-email");
			 
			 // Show Popup
			 jQuery('#cl_ip_warning_popup').show(200);
			 
			 // Licensee Site
			 jQuery('.fill_licensor_site').html(get_licensor_site.replace(/(^\w+:|^)\/\//, ''));
			 
			 // Licensee Email
			 jQuery('.fill_licensee_email').html(get_licensee_email);
			 
			 // IP Warning Trigger
			 jQuery('.warning_update_ip').click(function(){
			
			 // Action Link
			 var license_page_url = '<?php echo $cl_my_agrements_page; ?>';			 
			 window.location.href = license_page_url+'&license_id='+get_license_id+'&update_ip=1'; 
			 return false;
				 
			 });
			 
			 jQuery('.close-pbox').click(function(){
				jQuery('#cl_ip_warning_popup').hide(200); 
			 });
		 });
	});
	</script>
	<?php
}