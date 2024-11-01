<?php
/**
 * CLink Connection Functions
 *
 * CLink quota users and party connection functions
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink get content from URL
 * 
 * @return string data content fetch from URL
 */
function wpclink_fetch_content_by_curl($url,$header_xml = false)
{
    $ch = curl_init();
   curl_setopt($ch, CURLOPT_HEADER, 0);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
   $header = array();
   if($header_xml == true){
		$header[] = 'Accept: application/xml';
		$header[] = 'Content-Type: text/xml';
	}
	$header[] = 'Connection: close';
   curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
   $data = curl_exec($ch);
   curl_close($ch);
   return $data;
}
/**
 * CLink License Agreement API
 * 
 */
function wpclink_generate_license_offer_uri(){
	if(isset($_GET['s_agree']) and $_GET['s_agree'] == 1){
		wpclink_do_api_license_xml();
		die();
	}
	
}
// Register license api load when WordPress loaded
add_action('wp_loaded','wpclink_generate_license_offer_uri');
/**
 * CLink Response for Quota
 */
function wpclink_server_error_notification(){
global $wpCLink_quota_response;
$response = $wpCLink_quota_response;
if ($response == false) return false;
// Check the response code
$response_code = wp_remote_retrieve_response_code($response);
$response_message = wp_remote_retrieve_response_message($response);
if (200 != $response_code && !empty($response_message)) {
	echo '<div class="notice notice-error is-dismissible"><p><strong>Error ' . $response_code . '</strong> CLink cannot reach the Server. Try again or <a href="https//care.clink.media/submit-ticket/">Contact support</a></p></div>';
}
elseif (200 != $response_code) {
	echo '<div class="notice notice-error is-dismissible"><p><strong>Error ' . $response_code . '</strong> CLink cannot reach the Server. Try again or <a href="https://us-customers.clink.id/submit-ticket/">Contact support</a></p></div>';
}
else {
}
	
}
add_action( 'admin_notices', 'wpclink_server_error_notification' );
/**
 * CLink Generate XML URL
 * 
 */
function wpclink_start_magic(){
		// Activated Link
		$saved_options = wpclink_get_option( 'preferences_general' );
		$new_activated_link = wpclink_get_option('DYNAMIC_URL_POSTFIX');
		if(isset($_GET['go_live']) and $_GET['go_live'] == $new_activated_link and ($saved_options['cl_mode'] == 'export' || $saved_options['cl_mode'] == 'both')){
			wpclink_do_api_content_xml();
			die();
		}elseif(isset($_GET['go_live']) and $_GET['go_live'] != $new_activated_link and ($saved_options['cl_mode'] == 'export' || $saved_options['cl_mode'] == 'both')){
			die('0');			
		}elseif(isset($_GET['go_live']) and $saved_options['cl_mode'] == 'import'){
			die('0');	
		}
}
// Register generate xml url function on wordpress load
add_action('wp_loaded','wpclink_start_magic');
/**
 * CLink If No Link Selected
 * 
 * @return boolean
 */
function wpclink_if_no_primary_link(){
	global $wpdb;
	$agreements = wpclink_get_all_license_linked();
	
	foreach($agreements as $agreements_single){
		return false;
	}
	if(wpclink_get_option('primary_linked_site') == 'clink_group'){
		return false;
	}
	return true;
}
/**
 * CLink Primary Site Selector Dropdown
 * 
 */
function wpclink_display_menu_dropdown_referent_sites(){
	global $wpdb;
	$menu_page = menu_page_url( 'content_link_post.php', false );	
	$get_primary_site = wpclink_get_option('primary_linked_site');
	$get_super_site = wpclink_get_option('preferences_general');
	?>
<form method="post" action="">
  Site
  <select id="primary_site" name="primary_site">
    <?php 	do_action('cl_more_sites_link'); ?>
  </select>
  <input id="submit" class="button button-primary button-large" type="submit" value="Get" />
</form>
<?php
}
// Register primary site selector on clink post list
add_action('cl_before_post_page','wpclink_display_menu_dropdown_referent_sites');
// Register primary site selector on clink page list
add_action('cl_before_page_page','wpclink_display_menu_dropdown_referent_sites');
// Register primary site selector on clink notification page
add_action('cl_before_notification_page','wpclink_display_menu_dropdown_referent_sites');
/**
 * CLink Primary Site Update
 * 
 */
function wpclink_update_referent_site_selected(){
	$post_data = $_POST;
	if(isset($post_data['primary_site']) and !empty($post_data['primary_site'])){ 
		// Update
		wpclink_update_option('primary_linked_site',$post_data['primary_site']);
		//wpclink_update_basic_keys();
	}
}
// Register primary site update function
add_action('init','wpclink_update_referent_site_selected');
/**
 * CLink Get Site and Unique Adress
 * 
 * Clink get site address and pass keys from clink links.
 * 
 */
function wpclink_update_basic_keys(){
	// Super Site
	
	$get_primary_site = wpclink_get_option('primary_linked_site');
	
	$option_saved = wpclink_get_option( 'preferences_general' );
	
	if($get_primary_site == 'super'){
		// Nothing
	}elseif($get_primary_site < 0){
		
	$new_primary_site = abs($get_primary_site);
		
	global $wpdb;
	$results = wpclink_get_license_linked($new_primary_site);
	
	$link_url = $results['site_url'];
	$auth_code = $results['auth_code'];
	$secret_key = $results['secret_key'];
	
	
	$option_saved['sync_site'] = $link_url;
	$option_saved['sync_secretkey'] = $secret_key;
	$option_saved['sync_authcode'] = $auth_code;
	
	wpclink_update_option('preferences_general',$option_saved);
	
	
		
	}else{
		
	// Linked Sites
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'agg_cte';
	$results = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $get_primary_site");
	
	$link_url = $results->name;
	$auth_code = $results->auth_code;
	$secret_key = $results->secret_key;	
	
	$option_saved['sync_site'] = $link_url;
	$option_saved['sync_secretkey'] = $secret_key;
	$option_saved['sync_authcode'] = $auth_code;
	
	wpclink_update_option('preferences_general',$option_saved);	
	
	}
	
	
}
/**
 * CLink Linked Site Address 
 * 
 * @return mixed url | false
 */
function wpclink_get_uri_link_outbound_selected(){
	// Super Site
	
	$get_primary_site = wpclink_get_option('primary_linked_site');
	
	if($get_primary_site == 'super'){
	
	$cl_saved_option = wpclink_get_option( 'preferences_general' );
	$auth_code = $cl_saved_option['sync_authcode'];
	$secret_key = $cl_saved_option['sync_secretkey'];
	
	$link_decrypt = wpclink_new_decrypt($auth_code,$secret_key);
	$site_url_link = $cl_saved_option['sync_site'];
	
	$complete_url = $site_url_link.$link_decrypt;
	
	return $complete_url.'&direct';
		
	}elseif($get_primary_site < 0){
		
	$new_primary_site = abs($get_primary_site);
		
	global $wpdb;
	
	$results = wpclink_get_license_linked($new_primary_site);
	
	$auth_code = $results['auth_code'];
	$secret_key = $results['secret_key'];	
	$link_url = $results['site_url'];
	
	// Decode
	$link_decrypt = wpclink_new_decrypt($auth_code,$secret_key);
	
	$complete_url = $link_url.$link_decrypt;
	
	return $complete_url;
		
	// GROUP LINK <---
	}elseif($get_primary_site == 'clink_group'){
		
		global $wpdb;
		$group_invite = wpclink_get_option('wpclink_group_invite_link');
		if ( $group_invite = wpclink_get_option('wpclink_group_invite_link') ) {
			// Invite Link
			$invited_link = $group_invite;
					
					$request_query = array();
		
					$mywebsite = get_bloginfo('url').'/';
					$mywebsite_title = get_bloginfo('name');
					
					// Prepare
					$request_query['cl_group_site'] = urlencode($mywebsite);
					$request_query['cl_site_name'] = urlencode($mywebsite_title);
					$request_query['cl_group_action'] = 'connect';
					
					// Finally Build Query
					$build_query = build_query( $request_query );
					
					$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
					$xml=file_get_contents($invited_link.'&'.$build_query,false,$context);
					
					$xml = simplexml_load_string($xml);
					
					$link_url = (string)$xml->channel->access->site_address;
					$secret_key = (string)$xml->channel->access->secret_key;
					$auth_code = (string)$xml->channel->access->auth_code;
					
					
					// Decode
					$link_decrypt = wpclink_new_decrypt($auth_code,$secret_key);
					
					$complete_url = $link_url.$link_decrypt.'&direct';
					
					return $complete_url;
		
		}
	
		
	}else{
			
	return false;
	
	}
}
// Register clink linked site address function
add_filter('cl_linked_filter','wpclink_get_uri_link_outbound_selected');
function wpclink_insert_site_verification_id(){
	
	if(isset($_GET['clink-site-verfication-id'])){
		
		
		if($_GET['method'] == 'update'){
		
			// Code
			$clink_verification_id = $_GET['clink-site-verfication-id'];
		
			// Update
			wpclink_update_option('site_verification_id',$clink_verification_id);
			
			echo 'updated';
			
			
		}elseif($_GET['method'] == 'get'){
			
			// Print
			$site_verification_id = wpclink_get_option('site_verification_id');
			echo 'clink-site-verification:'.$site_verification_id;
			
		}
		
		
		die();
	}
}
add_action('wp_loaded','wpclink_insert_site_verification_id');
