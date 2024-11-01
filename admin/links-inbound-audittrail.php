<?php
/**
 * CLink Audit Trail
 *
 * clink audit trail admin page
 *
 * @package CLink
 * @subpackage System
 */
 
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
 
 // Register clink audit trail admin menu
add_action('admin_menu', 'wpclink_audit_trial_menu');
/**
 * CLink Audit Trail Admin Menu
 * 
 */
function wpclink_audit_trial_menu(){
	$creator_array = wpclink_get_option('authorized_creators');
	$current_user_id = get_current_user_id();
	$clink_party_id = wpclink_get_option('authorized_contact');
if (wpclink_is_acl_r_page()){
	// Not Availble
}else {
	if (current_user_can('administrator') and wpclink_user_has_creator_list($current_user_id)) {
		if (wpclink_import_mode() || wpclink_both_mode()) {
			add_submenu_page('cl_mainpage.php', 'Audit Trail', 'Audit Trail', 'manage_options', 'cl-audit-trail', 'wpclink_audit_trial_page');
		}
	}
	elseif (current_user_can('administrator') and $current_user_id == $clink_party_id) {
		if (wpclink_import_mode() || wpclink_both_mode()) {
			add_submenu_page('cl_mainpage.php', 'Audit Trail', 'Audit Trail', 'manage_options', 'cl-audit-trail', 'wpclink_audit_trial_page');
		}
	}
	elseif (wpclink_user_has_creator_list($current_user_id)) {
		if (wpclink_import_mode() || wpclink_both_mode()) {
			add_submenu_page('content_link_post.php', 'Audit Trail', 'Audit Trail', 'edit_posts', 'cl-audit-trail', 'wpclink_audit_trial_page');
		}
	}
	elseif ($current_user_id == $clink_party_id) {
		if (wpclink_import_mode() || wpclink_both_mode()) {
			add_submenu_page('cl_mainpage.php', 'Audit Trail', 'Audit Trail', 'edit_posts', 'cl-audit-trail', 'wpclink_audit_trial_page');
		}
	}
	else {
		if (wpclink_import_mode() || wpclink_both_mode()) {
			add_submenu_page('cl_mainpage.php', 'Audit Trail', 'Audit Trail', 'manage_options', 'cl-audit-trail', 'wpclink_audit_trial_page');
		}
	}
}} 
/**
 * CLink Audit Trail Admin Page Render
 * 
 */
function wpclink_audit_trial_page(){
	
	global $select_options, $radio_options;
	
	$current_user_id = get_current_user_id();
	$creator_array = wpclink_get_option('authorized_creators');

	$audit_id = $_GET['audit'];
	
	if(!isset($_GET['audit']) || !is_numeric($_GET['audit']) ) return false;
		
	?><div class="wrap">
  <?php wpclink_display_tabs_links_inbound(); ?>
  <div class="cl_subtabs_box">
    <?php
	global $wpdb;
	
	$license_data = wpclink_get_license_referent($audit_id);
	$content_id = $license_data['post_id'];
	$user_id = get_post_meta($content_id,'wpclink_rights_holder_user_id',true);
	
	$user_info = get_userdata($user_id);
	
	$display_name = $user_info->display_name;
	
	$image_1 = plugins_url( 'wpclink/admin/images/license-deliver.png', dirname(WPCLINK_MAIN_FILE));
		
	$image_2 = plugins_url( 'wpclink/admin/images/content-deliver.png', dirname(WPCLINK_MAIN_FILE));
	
	if($as_view_date = get_user_meta($user_id,'wpclink_license_uc-ut-um_view_date',true)){
		$license_class = 'AS-IS';
		$time_license_view = $as_view_date;
	}elseif($rs_date = get_user_meta($user_id,'wpclink_license_uc-at-um_view_date',true)){
		$license_class = 'RESTRICTED';
		$time_license_view  = $rs_date;
	}
	
	$creator_array = wpclink_get_option('authorized_creators');
	$party_id = wpclink_get_option('authorized_contact');
	
	$identifier = get_user_meta($user_id,'wpclink_party_ID',true);
	
	
	$pre_auth_view_data = get_post_meta($content_id,'wpclink_reuse_pre_auth_view_date',true);
	$pre_auth_accept_data = get_post_meta($content_id,'wpclink_reuse_pre_auth_accept_date',true);
	$pre_auth_effect_date = get_post_meta($content_id,'wpclink_reuse_pre_auth_effective_date',true);
	
	$creation_identifier = get_post_meta($content_id,'wpclink_creation_ID',true);
	
	
	$licensee_data = wpclink_get_license_meta($audit_id,'licensee_linked_variables');
	$licensee_data = unserialize($licensee_data); 
	
	// License Deliver Time
	$license_id = $license_data['license_id'];
	$token_name = wpclink_get_license_meta($license_id,'license_key');
	$token_data = wpclink_get_token_data_by_token_name($token_name);
	
	$link_create_date = $license_data['license_date'];
	
	$licensee_accept_datetime = wpclink_get_license_meta($audit_id,'license_offer_accept_date');
	
	$current = $token_data['license_delivery_date'];
	$interval = $licensee_accept_datetime;
	
	$secs = strtotime($interval)-strtotime("00:00:00");
	$total_interval_time = date("Y-m-d H:i:s",strtotime($current)+$secs);
	
	
	
	$creation_publish_date = wpclink_get_license_meta($audit_id,'linked_creation_date');
	
	$cl_my_agrements_page = menu_page_url( 'clink-links-inbound', false );
	if(isset($_GET['type'])){
		$cl_my_agrements_page = add_query_arg( 'type', $_GET['type'], $cl_my_agrements_page );
	}
	  
	 ?>
    <?php if($audit_id > 0){ ?>
    <div id="audit-trial">
      <div class="audit-list">
        <ul>      
        <?php if(!empty($as_view_date) || !empty($rs_date)){ ?>
          <li>
            <div class="icon"><span class="dashicons dashicons-visibility"></span></div>
            <h3><?php echo 'License '.$license_class .' viewed <br /><span class="nextline"><i>by</i> <span class="normal">'.$display_name.'</span>  <a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$identifier.'">'.wpclink_do_icon_clink_ID($identifier).'</a></span>';	?></h3>
            <h5><?php echo wpclink_convert_date_to_iso($time_license_view) ?></h5>
          </li>
          <?php } ?>
           <?php if(!empty($pre_auth_view_data)){ ?>
          <li>
            <div class="icon"><span class="dashicons dashicons-visibility"></span></div>
            <h3><?php echo 'Pre-authorization to license and e-sign viewed <br /><span class="nextline"><i>by</i> <span class="normal">'.$display_name.'</span>  <a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$identifier.'">'.wpclink_do_icon_clink_ID($identifier).'</a></span>';	?></h3>
            <h5><?php echo wpclink_convert_date_to_iso($pre_auth_view_data) ?></h5>
          </li>
          <?php } ?>
	      <?php if(!empty($pre_auth_effect_date)){ ?>
          <li>
            <div class="icon"><span class="dashicons dashicons-controls-repeat"></span></div>
            <h3><?php echo 'Pre-authorization to license and e-sign accepted & effective <br /><span class="nextline"><i>for Creation</i> <a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$creation_identifier.'">'.wpclink_do_icon_clink_ID($creation_identifier).'</a></span>'; ?></h3>
            <h5><?php echo wpclink_convert_date_to_iso($pre_auth_effect_date) ?></h5>
          </li>
          <?php } ?>
           <?php if(!empty($token_data['license_delivery_date'])){ ?>
          <li>
            <div class="icon"><span class="dashicons dashicons-edit"></span></div>
			<h3><?php echo 'License offer e-signed <br /><span class="nextline"><i>by</i> <span class="normal">'.$display_name.'</span> <a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$identifier.'">'.wpclink_do_icon_clink_ID($identifier).'</a></span>';	?></h3>
            <h5><?php echo wpclink_convert_date_to_iso($token_data['license_delivery_date']); ?></h5>
          </li>
          <?php } ?>
           <?php if(!empty($licensee_accept_datetime)){ ?>
          <li>
            <div class="icon"><span class="dashicons dashicons-yes"></span></div>
            <h3><?php echo 'License offer accepted <br /><span class="nextline"><i>by</i>  <span class="normal">'.$licensee_data['licensee_display_name'].'</span> <a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$licensee_data['licensee_identifier'].'">'.wpclink_do_icon_clink_ID($licensee_data['licensee_identifier']).'</a></span>';	?></h3><h5><?php echo wpclink_convert_date_to_iso($total_interval_time) ?></h5>
          </li>
          <?php } ?>
          
          <?php if(!empty($licensee_data)){ ?>
          <li>
            <div class="icon"><img width="16" height="16" src="<?php echo $image_1; ?>'" /></div>
            <h3><?php echo 'License delivered <br /><span class="nextline"><i>for Creation</i> <a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$creation_identifier.'">'.wpclink_do_icon_clink_ID($creation_identifier).'</a></span>';	?></h3><h5><?php echo wpclink_convert_date_to_iso($link_create_date); ?></h5>
          </li>
          <?php } ?>
		<?php if(!empty($creation_publish_date)){ ?>
          <li>
            <div class="icon"><img width="16" height="16" src="<?php echo $image_2; ?>'" /></div>
            <h3><?php echo 'Content delivered <br /><span class="nextline"><i>for Creation</i> <a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$creation_identifier.'">'.wpclink_do_icon_clink_ID($creation_identifier).'</a></span>';	?></h3><h5><?php echo wpclink_convert_date_to_iso($creation_publish_date); ?></h5>
          </li>
          <?php } ?>
        </ul>
      </div>
    </div>
	 <div class="audit-action">
    <a href="<?php echo $cl_my_agrements_page; ?>" class="button-primary"><?php _e('Back','cl_text'); ?></a>
	  </div>
    <?php }else{ ?>
    <div id="audit-trial">
    <p><?php _e('Audit Trial does not exists','cl_text'); ?></p>
    </div>
    <?php } ?>
  </div>
	<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
</div>
<?php
}