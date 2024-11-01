<?php
/**
 * CLink Preferences General
 *
 * clink preference general admin page.
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
add_action( 'admin_init', 'wpclink_page_options_init' );
add_action( 'admin_menu', 'wpclink_options_add_page' );
/**
 * Register the CLink Page Setting
 */
function wpclink_page_options_init(){
	register_setting( 'wp2sync_options', 'preferences_general', 'wpclink_options_validate' );
}
/**
 * Load up the CLink Preferences Menu Page
 */
function wpclink_options_add_page() {
	
// content_link_post.php
$creator_array = wpclink_get_option('authorized_creators');
$current_user_id = get_current_user_id();
$clink_party_id = wpclink_get_option('authorized_contact');
// Administrator + Creator
if (current_user_can('administrator') and wpclink_user_has_creator_list($current_user_id)) {
	add_menu_page('CLink', 'CLink', 'manage_options', 'cl_mainpage.php', 'wpclink_options_do_page', WPCLINK_LOGO_BASE64);
	add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'manage_options', 'cl_mainpage.php');
// Administrator + Party
	
	
}
elseif (current_user_can('administrator') and $current_user_id == $clink_party_id) {
	add_menu_page('CLink', 'CLink', 'manage_options', 'cl_mainpage.php', 'wpclink_options_do_page', WPCLINK_LOGO_BASE64);
	add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'manage_options', 'cl_mainpage.php');
}
else
if (wpclink_user_has_creator_list($current_user_id)) {
	add_menu_page('CLink', 'CLink', 'edit_posts', 'cl_mainpage.php', 'wpclink_options_do_page', WPCLINK_LOGO_BASE64);
	add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'edit_posts', 'cl_mainpage.php');
}
elseif ($current_user_id == $clink_party_id) {
	add_menu_page('CLink', 'CLink', 'edit_posts', 'cl_mainpage.php', 'wpclink_options_do_page', WPCLINK_LOGO_BASE64);
	add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'edit_posts', 'cl_mainpage.php');
}
else {
	add_menu_page('CLink', 'CLink', 'manage_options', 'cl_mainpage.php', 'wpclink_options_do_page', WPCLINK_LOGO_BASE64);
	add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'manage_options', 'cl_mainpage.php');
}
}
/**
 * CLink Preferences Admin Page
 * 
 * Clink Preferences page has generic options.
 * - Mode
 * - Territory 
 * - Verification 
 * - Security 
 */
function wpclink_options_do_page() {
	global $select_options, $radio_options, $canonical_attemps_options;
	
	$select_options = array(
	'0' => array(
		'value' =>	'hourly',
		'label' => __( 'Every Hour', 'cl_text' )
	),
	'1' => array(
		'value' =>	'daily',
		'label' => __( 'Once a Day', 'cl_text' )
	),
	'2' => array(
		'value' => 'twicedaily',
		'label' => __( 'Twice a Day', 'cl_text' )
	)
);
	
$cl_mode = array(
	'yes' => array(
		'value' => 'import',
		'label' => __( 'Linked', 'cl_text' )
	),
	'no' => array(
		'value' => 'export',
		'label' => __( 'Referent', 'cl_text' )
	),
	'both' => array(
		'value' => 'both',
		'label' => __( 'Both', 'cl_text' )
	)
);
$canonical_attemps_options = array(
	'1' => array(
		'value' => '1',
		'label' => __( '1', 'cl_text' )
	),
	'2' => array(
		'value' => '2',
		'label' => __( '2', 'cl_text' )
	),
	'3' => array(
		'value' => '3',
		'label' => __( '3', 'cl_text' )
	),
	'4' => array(
		'value' => '4',
		'label' => __( '4', 'cl_text' )
	),
	'5' => array(
		'value' => '5',
		'label' => __( '5', 'cl_text' )
	),
	'6' => array(
		'value' => '6',
		'label' => __( '6', 'cl_text' )
	),
	'7' => array(
		'value' => '7',
		'label' => __( '7', 'cl_text' )
	),
	'8' => array(
		'value' => '8',
		'label' => __( '8', 'cl_text' )
	),
	'9' => array(
		'value' => '9',
		'label' => __( '9', 'cl_text' )
	),
	'10' => array(
		'value' => '10',
		'label' => __( '10', 'cl_text' )
	)
);
$country_codes = wpclink_get_territories();
	

	// If Territory not selected
	if($country_codes_selected = wpclink_get_option('territory_code')){	
	}else{
		
	$country_codes = array_merge($country_codes,array( '0' => 'Select'));
	
	}
	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;
		
	if(isset($_GET['startup']) and $_GET['startup'] == 1){
		wpclink_update_option('wpclink_welcome_status',$_GET['startup']);
		wpclink_update_option('wpclink_terms_condition',1);
		
	}
	// Startup
	$option_startup = wpclink_get_option('wpclink_welcome_status');
	
	$current_user_id = get_current_user_id();
	?>
<div class="wrap">
  <?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
  <?php wpclink_reschedule_canonical_verification(); ?>
  <div class="updated fade">
    <p><strong>
      <?php _e( 'Options saved', 'cl_text' ); ?>
      </strong></p>
  </div>
  <?php endif; ?>
  <?php wpclink_if_no_primary_link(); ?>
   <?php wpclink_display_tabs_preferences_general(); ?>
	<div class="cl_subtabs_box" style="padding-left:45px; padding-top:35px;"> 
    
  <form method="post" action="<?php echo menu_page_url( 'cl_mainpage.php', false );?>" style="margin-bottom:15px;">
    <?php settings_fields( 'wp2sync_options' ); ?>
    <?php
    // GET
	$options = wpclink_get_option( 'preferences_general' ); 
	$updated = false;
	
	if(isset($_POST['preferences_general']['cl_mode'])){
		if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_modes'], 'wpclink_options_modes')){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{
		
		$options['cl_mode'] = $_POST['preferences_general']['cl_mode'];
		
		// Update
		wpclink_update_option('preferences_general',$options);
		$updated = true;
		
		}
	}
	
	
	if(isset($_POST['canonical_fail_attemps']) ){
		
		if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_canonical'], 'wpclink_options_canonical')){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{
		
		$canonical_fail_attemps_save = $_POST['canonical_fail_attemps'];
		
		// Update
		wpclink_update_option('verification_canonical_fail_threshold',$canonical_fail_attemps_save);
		$updated = true;
		}
	}
	
	
	if(isset($_POST['reuse_verticle_position']) ){
		
		if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_customization'], 'wpclink_options_customization')){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{
		
		$reuse_verticle_position = $_POST['reuse_verticle_position'];
		
		// Update
		wpclink_update_option('reuse_button_left_offset',$reuse_verticle_position);
		
			
		$reuse_verticle_position = $_POST['reuse_top_position'];
		
		// Update
		wpclink_update_option('reuse_button_top_offset',$reuse_verticle_position);
		$updated = true;
			
		}
	}
	
	

	
	if(isset($_POST['canonical_success_attemps'])){
		
		if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_canonical'], 'wpclink_options_canonical')){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{
		
		$canonical_success_attemps_save = $_POST['canonical_success_attemps'];
		
		// Update
		wpclink_update_option('verification_canonical_success_threshold',$canonical_success_attemps_save);
		$updated = true;
		}
	}
	
	if(isset($_POST['preferences_general']['canonical_time'])){
		if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_canonical'], 'wpclink_options_canonical')){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{
			
		$options['canonical_time'] = $_POST['preferences_general']['canonical_time'];
		
		// Update
		wpclink_update_option('preferences_general',$options);
		$updated = true;
		}
	}

	
	
	if(isset($_POST['licensor_url'])){
		if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_media'], 'wpclink_options_media')){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{
			
		$options['licensor_url'] = $_POST['licensor_url'];
		
		// Update
		wpclink_update_option('preferences_general',$options);
		$updated = true;
		}
	}
	
	// Fix if option not found
	 if(!isset($options['licensor_url']) || empty($options['licensor_url'])){
		 $options['licensor_url'] = 'reuse_guid';
		 wpclink_update_option('preferences_general',$options); 
	 } 

	
	if(isset($_POST['embeded_metadata'])){
		if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_media'], 'wpclink_options_media')){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{
			
		$options['embeded_metadata'] = $_POST['embeded_metadata'];
		
		// Update
		wpclink_update_option('preferences_general',$options);
		$updated = true;
		}
	}
	
	 // Fix if option not found
	 if(!isset($options['embeded_metadata']) || empty($options['embeded_metadata'])){
		 $options['embeded_metadata'] = 'ask';
		 wpclink_update_option('preferences_general',$options); 
	 } 
	

	
	if(isset($_POST['debug_enable_set'])){
		if(isset($_POST['debug_enable'])){
			if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_debug'], 'wpclink_options_debug')){
				wpclink_notif_print('Action cannot be perform.','error');
			}else{
				$debug_enable = $_POST['debug_enable'];
				// Update
				wpclink_update_option('debug_enable',$debug_enable);
				$updated = true;
			}
		}else{
			
				// Update
				wpclink_update_option('debug_enable',false);
				$updated = true;
			
		}
	}
	if(isset($_POST['clinkid_prefix_set']) ){
		if(isset($_POST['clinkid_prefix'])){
			if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_customization'], 'wpclink_options_customization')){
				wpclink_notif_print('Action cannot be perform.','error');
			}else{
				$clinkid_prefix = $_POST['clinkid_prefix'];
				// Update
				wpclink_update_option('clinkid_prefix',$clinkid_prefix);
				$updated = true;
			}
		}else{
			
				// Update
				wpclink_update_option('clinkid_prefix',false);
				$updated = true;
			
		}
	}
	
	if(isset($_POST['show_registration_set']) ){
		if(isset($_POST['show_registration'])){
			if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_registration'], 'wpclink_options_registration')){
				wpclink_notif_print('Action cannot be perform.','error');
			}else{
				$clinkid_prefix = $_POST['show_registration'];
				// Update
				wpclink_update_option('show_registration',$clinkid_prefix);
				$updated = true;
			}
		}else{
			
				// Update
				wpclink_update_option('show_registration',false);
				$updated = true;
			
		}
	}
	
	if(isset($_POST['show_linked_mode_set'])){
		if(isset($_POST['show_linked_mode'])){
			if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_registration'], 'wpclink_options_registration')){
				wpclink_notif_print('Action cannot be perform.','error');
			}else{
				$get_show_linked_mode = $_POST['show_linked_mode'];
				// Update
				wpclink_update_option('show_linked_mode',$get_show_linked_mode);
				$updated = true;
			}
		}else{
			
				// Update
				wpclink_update_option('show_linked_mode',false);
				$updated = true;
			
		}
	}
	if(isset($_POST['image_register_url_set'])){
		if(isset($_POST['image_register_url'])){
			if(wpclink_is_acl_r_page() || ! wp_verify_nonce( $_POST['wpclink_verify_options_media'], 'wpclink_options_media')){
				wpclink_notif_print('Action cannot be perform.','error');
			}else{
				$image_register_url = $_POST['image_register_url'];
				// Update
				wpclink_update_option('image_register_url',$image_register_url);
				$updated = true;
			}
		}else{
			
				// Update
				wpclink_update_option('image_register_url',false);
				$updated = true;
			
		}
	}
	
	$creator_array = wpclink_get_option('authorized_creators');
	
	
	$wpclink_party_id = get_user_meta( $current_user_id, 'wpclink_party_ID', true );
		
	if($updated == true){
		wpclink_notif_print('Changes have been saved.','success');
	}
	 // GET
	$options = wpclink_get_option( 'preferences_general' ); 
	$clink_security = wpclink_get_option('verification_linked_ip');
	$debug_enable = wpclink_get_option('debug_enable');
	$clinkid_prefix = wpclink_get_option('clinkid_prefix');
	$show_registration = wpclink_get_option('show_registration');
	$show_linked_mode = wpclink_get_option('show_linked_mode');
	$image_register_url = wpclink_get_option('image_register_url');
	?><h3><?php _e( 'Mode', 'cl_text' ); ?> <span class="icon-box" title="Operation modes allowing to curate and/or promote licensed content"></span></h3>
    <table class="form-table cl-modes-style fixed-box-390">
          <tr valign="top">
            <th scope="row"></th>
            <td><fieldset>
                <legend class="screen-reader-text"><span>
                <?php _e( 'Mode', 'cl_text' ); ?>
                </span></legend>
                <?php
				$count_label = 0;
							if ( ! isset( $checked ) )
								$checked = '';
							foreach ( $cl_mode as $option ) {
								$radio_setting = $options['cl_mode'];
								$count_label++;
								if ( '' != $cl_mode ) {
									if ( $options['cl_mode'] == $option['value'] ) {
										$checked = "checked=\"checked\"";
									} else {
										$checked = '';
									}
								}
								?>
                <label class="description clmode_space clm-<?php echo $count_label; ?>">
                  <input type="radio" name="preferences_general[cl_mode]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> />
                  <?php echo $option['label']; ?></label>
                <?php
							}
						?>
          <input type="submit" class="button-primary mode-update" value="<?php _e( 'Update', 'cl_text' ); ?>" />              
              </fieldset></td>
          </tr>
        </table>
        <input type="hidden" name="preferences_general[canonical_time]" value="<?php echo $options['canonical_time']; ?>" />
	  	<?php wp_nonce_field( 'wpclink_options_modes', 'wpclink_verify_options_modes' ); ?>
  </form>
<?php $options = wpclink_get_option( 'preferences_general' );  ?>        
<h3>Territory <span class="icon-box" title="Territory with which the Creations of this site is associated with"></span></h3>
<form method="post" class="form_territory" action="<?php echo menu_page_url( 'cl_mainpage.php', false );?>" style="margin-bottom:20px;">  
    <table class="form-table fixed-box-390">
      <tr valign="top">
      <th scope="row"></th>
					<td>
						<select name="teriority_code" id="teriority_code">
							<?php
								$selected = wpclink_get_option('territory_code');
								$p = '';
								$r = '';
								foreach ( $country_codes as $key=>$value ) {
									$label = $key;
									if ( $selected == $key ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" .$key."'>$value</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $key ) . "'>$value</option>";
								}
								echo $p . $r;
							?>
						</select>
						  <input type="submit" class="button-primary teriority_button" value="<?php _e( 'Update', 'cl_text' ); ?>" />
					</td>
				</tr>
    </table>
</form>
<h3>Media</h3>
<form method="post" class="form_territory" action="<?php echo menu_page_url( 'cl_mainpage.php', false ); ?>" style="margin-bottom:20px;">
	<table class="form-table fixed-box-390  align-btn">
      <tr valign="top">
      <th scope="row"></th>
					<td><p class="section-heading">Image URL <span class="icon-box" title="Option to Register Image URL"></span></p><label><label for="image_register_url"><input type="checkbox" id="image_register_url" name="image_register_url" value="1" <?php checked( $image_register_url, 1 ); ?>>Allow registration</label>
		<input type="hidden" name="image_register_url_set" value="1" />
						  <input type="submit" class="button-primary teriority_button" value="<?php _e( 'Update', 'cl_text' ); ?>" />
						
						
					</td>
				</tr>
    </table>
	<table class="form-table fixed-box-390  align-btn">
      <tr valign="top">
      <th scope="row"></th>
					<td><p class="section-heading">Licensor URL <span class="icon-box" title="See http://ns.useplus.org/LDF/ldf-XMPSpecification#LicensorURL"></span></p><label><input type="radio"  name="licensor_url" <?php checked( $options['licensor_url'], 'reuse_guid' ); ?> value="reuse_guid">Reuse GUID </label><br><label><input type="radio" name="licensor_url" <?php checked( $options['licensor_url'], 'party_object_id' ); ?> value="party_object_id"><a href="<?php echo WPCLINK_ID_URL.'/#objects/'.$wpclink_party_id ?>">Party Object URI</a> </label>
						  <input type="submit" class="button-primary teriority_button" value="<?php _e( 'Update', 'cl_text' ); ?>" />
					</td>
				</tr>
    </table>
	<table class="form-table fixed-box-390 align-btn">
      <tr valign="top">
      <th scope="row"></th>
					<td><p class="section-heading">EXIF Metadata <span class="icon-box" title="Options to remove all EXIF fields for privacy or retain dates only"></span></p><label><input type="radio" name="embeded_metadata" <?php checked( $options['embeded_metadata'], 'overwrite' ); ?> value="overwrite">Dates</label><br><label><input type="radio" name="embeded_metadata" <?php checked( $options['embeded_metadata'], 'remove' ); ?> value="remove">All</label><label><br><input type="radio"  name="embeded_metadata" <?php checked( $options['embeded_metadata'], 'ask' ); ?> value="ask">Manual <span class="icon-box" title="Prompted for selection during the first &quot;Update&quot; of the Media">  </label>
						  <input type="submit" class="button-primary teriority_button" value="<?php _e( 'Update', 'cl_text' ); ?>" />
					</td>
				</tr>
    </table>
<?php wp_nonce_field( 'wpclink_options_media', 'wpclink_verify_options_media' ); ?>
</form>
<h3>Notifications <span class="icon-box" title="Notifications displayed through the UI"></span></h3>
<form method="post" class="form_registration" action="<?php echo menu_page_url( 'cl_mainpage.php', false );?>" style="margin-bottom:20px;">  
    <table class="form-table fixed-box-390">
      <tr valign="top">
      <th scope="row"></th>
					<td>
						<p class="section-heading">Document Panel</p>
          <label for="show_registration"><input type="checkbox" id="show_registration" name="show_registration" value="1" <?php checked( $show_registration, 1 ); ?>>Show Registration Enabled</label>
		 <input type="hidden" name="show_registration_set" value="1" /><br>
          <label for="show_linked_mode"><input type="checkbox" id="show_linked_mode" name="show_linked_mode" value="1" <?php checked( $show_linked_mode, 1 ); ?>>Show Linked Mode</label>
		<input type="hidden" name="show_linked_mode_set" value="1" />
						  <input type="submit" class="button-primary teriority_button" value="<?php _e( 'Update', 'cl_text' ); ?>" />
					</td>
				</tr>
    </table>
<?php wp_nonce_field( 'wpclink_options_registration', 'wpclink_verify_options_registration' ); ?>
</form>
<form method="post" action="<?php echo menu_page_url( 'cl_mainpage.php', false );?>" style="margin-bottom:10px;">        
        <h3>Verifications <span class="icon-box" title="Periodic schedule verifying the compliance with some of the terms of the license"></span></h3>
    <table class="form-table fixed-box-390">
      <tr valign="top">
        <th scope="row"></th>
        <td>
        <p class="section-heading">Canonical <span class="icon-box" title="Link element pointing to the original content"></span></p>
        <select id="canonical_time" name="preferences_general[canonical_time]">
            <?php
								$selected = $options['canonical_time'];
								$p = '';
								$r = '';
								foreach ( $select_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
          </select>
          <label class="description" for="preferences_general[canonical_time]">
          </label>
           <a class="advance-btn button"><?php _e('Advanced ...','cl_text'); ?></a>
			<input type="submit" class="button-primary mode-update" value="<?php _e( 'Update', 'cl_text' ); ?>" /> 
           <div class="canonical_vtoggle">
          <p>
          <select id="canonical_fail_attemps" name="canonical_fail_attemps">
            <?php
								$selected = wpclink_get_option('verification_canonical_fail_threshold');
								$p = '';
								$r = '';
								foreach ( $canonical_attemps_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
          </select> <?php _e('Number of failed verifications before Link is removed','cl_text'); ?>
          </p>
          <p>
          <select id="canonical_success_attemps" name="canonical_success_attemps">
            <?php
								$selected = wpclink_get_option('verification_canonical_success_threshold');
								$p = '';
								$r = '';
								foreach ( $canonical_attemps_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
          </select> <?php _e('Number of successful verifications before Link is restored','cl_text'); ?></p>
          </div>
           <br />
			   
          </td>
      </tr>
    </table>
<?php wp_nonce_field( 'wpclink_options_canonical', 'wpclink_verify_options_canonical' ); ?>
    </form>
	<?php $reuse_verticle_position = wpclink_get_option( 'reuse_button_left_offset' );
		$reuse_top_position = wpclink_get_option( 'reuse_button_top_offset' ); ?>
	<form method="post" action="<?php echo menu_page_url( 'cl_mainpage.php', false );?>" style="margin-bottom:10px;">    
    <h3>Customization <span class="icon-box" title="Customization options for the WordPress UI"></span></h3>
	<table class="form-table fixed-box-390">
      <tr valign="top">
        <th scope="row"></th>
        <td>
			<p class="section-heading">CLink.ID</p>
          <label for="clinkid_prefix"><input type="checkbox" id="clinkid_prefix" name="clinkid_prefix" value="1" <?php checked( $clinkid_prefix, 1 ); ?>>Show Prefix</label>
                        <input type="hidden" name="clinkid_prefix_set" value="1" /><br><br>
			<p class="section-heading">Reuse Button Offset </p>
          <label for="reuse_verticle_position"><input type="number" id="reuse_verticle_position" name="reuse_verticle_position"
       min="-100" max="1024" value="<?php echo $reuse_verticle_position; ?>"> px <?php _e( 'to left', 'cl_text' ); ?></label> <label for="reuse_verticle_position"><input type="number" id="reuse_top_position" name="reuse_top_position"
       min="-100" max="1024" value="<?php echo $reuse_top_position; ?>"> px <?php _e( 'to top', 'cl_text' ); ?></label>
          <input type="submit" class="button-primary debug-update" value="<?php _e( 'Update', 'cl_text' ); ?>" />
          </td>
      </tr>
    </table>
<?php wp_nonce_field( 'wpclink_options_customization', 'wpclink_verify_options_customization' ); ?>
    </form>
	
	<form method="post" action="<?php echo menu_page_url( 'cl_mainpage.php', false );?>" style="margin-bottom:10px;">    
    <h3>Debug Log <span class="icon-box" title="Creates log file can be used for troubleshooting"></span></h3>
    <table class="form-table fixed-box-390">
      <tr valign="top">
        <th scope="row"></th>
        <td>
			<p class="download_btn">
          <label for="debug_enable"><input type="checkbox" id="debug_enable" name="debug_enable" value="1" <?php checked( $debug_enable, 1 ); ?>>Enable</label>
		   <?php if( $debug_enable == 1){
								
				// Filename
				$clink_debug_filename = wpclink_get_option('debug_filename');
				
				// Filepath
				$file_name_exists = dirname(WPCLINK_MAIN_FILE).'/log/'.$clink_debug_filename;
				$file_url = plugins_url( 'log/'.$clink_debug_filename, dirname(__FILE__));
								
					
				
				// Debug
				if(file_exists($file_name_exists)){
					
				
					
					echo '<br /><strong>File:</strong> <a download href="'.$file_url.'">'.$clink_debug_filename.'</a>';

				}
						 
		} ?>
          <input type="hidden" name="debug_enable_set" value="1" />
          <input type="submit" class="button-primary debug-update" value="<?php _e( 'Update', 'cl_text' ); ?>" />
				</p>
          </td>
		  
      </tr>
    </table>
		<table class="form-table fixed-box-390">
      <tr valign="top">
        <th scope="row"></th>
		  <td>
		
		 </td>
			</tr>
		</table>
<?php wp_nonce_field( 'wpclink_options_debug', 'wpclink_verify_options_debug' ); ?>
    </form> 
<script>
 jQuery(function() {
     jQuery('.cl_subtabs_box').tooltip();
     var icons = {
         header: "ui-icon-circle-arrow-e",
         activeHeader: "ui-icon-circle-arrow-s"
     };
     jQuery("#accordion").accordion({
         icons: icons,
         header: "h2",
         active: false
     });
 });
 jQuery(document).ready(function() {
     jQuery(".advance-btn").click(function() {
         jQuery('.canonical_vtoggle').slideToggle("fast");
     });
 });
</script>
</div>
	<?php
	// End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
<?php
}
/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function wpclink_options_validate( $input ) {
	return $input;
}
