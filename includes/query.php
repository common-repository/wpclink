<?php
/**
 * CLink Query Functions 
 *
 * CLink query and conditional functions
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink User Has Creators List
 * 
 * Check the User has exists in creator list.
 * 
 * @return boolean
 */
if(!function_exists('wpclink_user_has_creator_list')){
	function wpclink_user_has_creator_list($user_id = 0){

		$creator_array = wpclink_get_option('authorized_creators');
		if(in_array($user_id,$creator_array)){
			return true;
		}else{
			return false;
		}
	}
}
function wpclink_update_creator_list($user_id = 0){
	
	$creator_list = wpclink_get_option('authorized_creators');
	
	// Empty
	if(empty($creator_list) || $creator_list == false){
		$creator_list = array();
	}
	
	$creator_list_update = array_merge($creator_list,array($user_id));
	$creator_list_final = array_unique($creator_list_update);
	
	// Update Creator
	$updated = wpclink_update_option('authorized_creators',$creator_list_final,'no');
	
	return $updated;
	
}
/**
 * CLink Referent Mode Check
 * 
 * Conditional method for check the referent mode of CLink Plugin
 * 
 * @return boolean
 */
function wpclink_import_mode(){
	 $save_option = wpclink_get_option( 'preferences_general' );
	if($save_option['cl_mode'] == 'import'){
		return true;	
	}else{
		return false;
	}
}
/**
 * CLink Referent Mode Check
 * 
 * Conditional method for check the referent mode of CLink Plugin
 * 
 * @return boolean
 */
function wpclink_can_perform_license($post_id){
	// Linked
	$sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true );
	if(!empty($sync_origin) || 
	   (wpclink_import_mode() == false) || 
	   (wpclink_post_in_post_referent_list($post_id)) ||
	   (wpclink_check_license_by_post_id($post_id) > 0)){
		return true;
	}
	return false;
}
/**
 * CLink Both Mode Check
 * 
 * Conditional method for check the referent and linked mode of CLink Plugin
 * 
 * @return boolean
 */
function wpclink_both_mode(){
	 $save_option = wpclink_get_option( 'preferences_general' );
	if($save_option['cl_mode'] == 'both'){
		return true;	
	}else{
		return false;
	}
}
/**
 * CLink Linked Mode Check
 * 
 * Conditional method for check the linked mode of CLink Plugin
 * 
 * @return boolean
 */
function wpclink_export_mode(){
	 $save_option = wpclink_get_option( 'preferences_general' );
	if($save_option['cl_mode'] == 'export'){
		return true;	
	}else{
		return false;
	}
}
/**
 * CLink Current Linked Site
 *
 * Mainly use for link of the site.
 */
function wpclink_linked_site(){
	$complete_url = '';	
	$complete_link_url = apply_filters( 'cl_linked_filter', $complete_url );
	$cleaned = strval(str_replace("\0", "", $complete_link_url));
	return $cleaned;
	
}
/**
 * Verify the Current Active Site Link is UP (200)
 * 
 * @return boolean
 */
function wpclink_check_path(){
	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	$xml=@file_get_contents(wpclink_linked_site(),false,$context);
	
	if($xml === FALSE){
		return false;	
	}elseif($xml == '0'){
		return false;
	}else{
		return true;
	}
}
/**
 * CLink Check is Site is Register on Care.Cink.Meda
 * 
 * @param string $site_url site address
 * 
 * @return boolean true if found false on not found
 */
function wpclink_is_site_url_verified_hub($site_url = false){
	
	if($site_url == false) return false;
	
	
	  // Encode
	  $site_url_ready = urlencode($site_url);
		
	  global $wp_version;
	  $args = array(
		  'timeout'     => WPCLINK_API_TIMEOUT,
		  'redirection' => 5,
		  'httpversion' => '1.0',
		  'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
		  'blocking'    => true,
		  'headers'     => array(),
		  'cookies'     => array(),
		  'body'        => null,
		  'compress'    => false,
		  'decompress'  => false,
		  'sslverify'   => false,
		  'stream'      => false,
		  'filename'    => null
	  ); 
	  $response = wp_remote_get( WPCLINK_CARE_SERVER.'/?cl_site=1&url='.$site_url_ready.'&time='.time(),$args);
	
		if ( is_array( $response ) ) {
			$body = $response['body']; // use the content
			$site_exists = json_decode($body,true);
			if($site_exists['site'] == '1'){
				return true;
			}else{
				return false;	
			}
		}
	
  return false;
}
/**
 * CLink Get Reuse Response
 * 
 * @param string $site_url site address
 * 
 * @return boolean true if found false on not found
 */
function wpclink_reuse_api_request($site_url = false, $token = false){
	
	if($site_url == false || $token == false) return false;
	
	
	  // Encode
	  $site_url_ready = urlencode($site_url);
	
	  // token
	  $token_decode = urlencode($token);
	
		
	  global $wp_version;
	  $args = array(
		  'timeout'     => WPCLINK_API_TIMEOUT,
		  'redirection' => 5,
		  'httpversion' => '1.0',
		  'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
		  'blocking'    => true,
		  'headers'     => array(),
		  'cookies'     => array(),
		  'body'        => null,
		  'compress'    => false,
		  'decompress'  => false,
		  'sslverify'   => false,
		  'stream'      => false,
		  'filename'    => null
	  );
	
	
	
	  $paid = apply_filters( 'wpclink_reuse_paid_offer', '', $token );
	  $response = wp_remote_get( WPCLINK_CARE_SERVER.'/?reuse_api_data=1&weburl='.$site_url_ready.'&token='.$token_decode.$paid,$args);
	
 
	if ( is_array( $response ) ) {
		$body = $response['body']; // use the content
		$reuse_response = json_decode($body,true);
		if($reuse_response['response'] == 'added'){
			return $reuse_response['clink_url'];
		}else if ($reuse_response['response'] == 'already'){
			return $reuse_response['clink_url'];
		}else if($reuse_response['response'] == 'error' and $reuse_response['code'] == '400'){
			return '400';
		}else if($reuse_response['response'] == 'error' and $reuse_response['code'] == '404'){
			return '404';
		}
		
	}
  
  return false;
}
/**
 * Get Copyright Owner ID
 * 
 * @return integer right_holder_id 
 * 
 */
function wpclink_get_rights_holder_id(){
	
	$right_holder = wpclink_get_option('rights_holder');
	$creator_array = wpclink_get_option('authorized_creators');
	$user_id_asso = $creator_array[0];
	$party_id = wpclink_get_option('authorized_contact');
	
	if($right_holder == 'party'){
		$right_holder_id = wpclink_get_option('authorized_contact');
	}else if($right_holder == 'creator'){
		$right_holder_id = $user_id_asso;
	}
	
	return $right_holder_id;
}
/**
 * CLink Display Taxonomy Permission
 * 
 * @param string taxonomy_label slug of taxonomy permission
 * 
 */
function wpclink_programmatic_right_categories_label($taxonomy_label = false){
	
	
	$taxonomy_label = explode(",",$taxonomy_label);
	
	if(in_array('non-editable',$taxonomy_label)){
		echo 'NA';
	}elseif(in_array('AddToTaxonomy',$taxonomy_label)){
		echo 'Add to Taxonomy';
	}elseif(in_array('ModifyTaxonomy',$taxonomy_label)){
		echo 'Modify Taxonomy';
	}elseif(empty($taxonomy_label)){
		echo 'N/A';
	}elseif($taxonomy_label){
		foreach ($taxonomy_label as $single){
			if($single == 'ModifyDescription'){
				echo "Modify Description"."<br />";
			}else if($single == 'ModifyKeywords'){
				echo "Modify Keywords"."<br />";
			}else if($single == 'ModifyHeadline'){
				echo "Modify Headline"."<br />";	
			}
		}
	}
}

/**
 * CLink Link Creation Status Label
 * 
 * @param string $creation_slug  creation status slug
 * 
 */
function wpclink_get_license_creation_status_label($creation_slug = false){
	
	
	if($creation_slug == 'notfound'){			
		echo 'Not Found';
	}elseif($creation_slug == 'servererror'){
		echo 'Server Error';
	}elseif($creation_slug == 'found'){
		echo 'Found';
	}else{
		echo 'N/A';
	}
}
/**
 * CLink License Content Status Label
 * 
 * @param string $content_slug  content status slug
 * 
 */
function wpclink_get_license_delivery_status_label($content_slug = false,$icon = false){
	
	$image_1 = plugins_url( 'wpclink/admin/images/license-deliver.png', dirname(WPCLINK_MAIN_FILE));
		
		$image_2 = plugins_url( 'wpclink/admin/images/content-deliver.png', dirname(WPCLINK_MAIN_FILE));
	
	if($content_slug == 'delivered'){			
		if($icon) $icon = '<img width="16" height="16" src="'.$image_1.'" />';
		echo $icon.' License delivered';
	}elseif($content_slug == 'content_delivered'){
		if($icon) $icon = '<img width="16" height="16" src="'.$image_2.'" />';
		echo $icon.' Content delivered';
	}elseif($content_slug == 'published'){
		echo 'Published';
	}else{
		echo 'N/A';
	}
}
/**
 * CLink License Version Slug to Label
 * 
 * @param string $license_slug license slug
 * 
 * @return string
 */
function wpclink_get_license_version_label($license_slug = false){
	
	$license_ver_array = wpclink_get_option('license_versions');
	
	if(empty($license_ver_array)){
		return 'N/A';
	}else{
		return $license_ver_array[$license_slug];
	}
}
/**
 * CLink License Class Slug to Label
 * 
 * @param string $license_slug license slug
 * 
 * @return string
 */
function wpclink_get_license_class_label($license_slug = false){
	if($license_slug == 'personal'){
		return 'Personal';
	}else{
		return 'N/A';
	}
}
/**
 * CLink CLink.ID HTML Hyperlink Generate
 * 
 * @param string $clinkid  clink id 
 * 
 * @return string hyperlink
 */
function wpclink_add_hyperlink_to_clink_ID($clinkid = ''){
	return '<a href="'.WPCLINK_ID_URL.'/#objects/'.$clinkid.'" target="_blank">'.$clinkid.'</a>';
}
/**
 * CLink Check Content is in Linked Site List
 * 
 * @param integer $content_id  content id
 * 
 * @return mixed content id | false
 */
function wpclink_check_license_by_post_id($content_id = 0){
	
	global $wpdb;
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$mylink = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'referent' AND verification_status = 'pass' ",ARRAY_A );
	
	
	
	if ( NULL !== $mylink ) {	

	$return_data = array();
	
	foreach($mylink as $license_single){
		
		$content_id_match = $license_single['post_id'];
		
		if($content_id == $content_id_match){
			return $content_id_match;
		}
		
	}
	
	
		
		
	}else{
		return false;	
	}	
}
/**
 * CLink Check the Site Content is in Linked Site List
 * 
 * @param integer $get_content content id
 * 
 * @return boolean
 */
function wpclink_is_licensed_by_post_id($get_content = 0){
	
	$content_return = wpclink_check_license_by_post_id($get_content);
	if($content_return == $get_content){
		return false;	
	}else{
		return true;	
	}
}
/**
 * CLink Display Name from User ID
 * 
 * @param integer $user_id  user id
 * 
 * @return string
 */
function wpclink_display_display_name_by_user_id($user_id = false){
	
	if($user_id == false) return false;	
	
	// DATA
	$user_data = get_userdata( $user_id );
	return $user_data->display_name;
}
/**
 * CLink Clean URL from Protocol and Slash
 * 
 * @param string $url site url
 * 
 * @return string site url
 */
function wpclink_clean_url($url = ''){
	
	$url = preg_replace('{/$}', '', $url);
	$url = parse_url($url);  
	
	$host = (isset($url['host'])) ? $url['host'] : '';
	$path = (isset($url['path'])) ? $url['path'] : '';
	$query = (isset($url['query'])) ? $url['query'] : '';
	
	$url_clean = $host.$path.$query;
	
	return $url_clean;
}

/**
 * CLink Read Only 
 * 
 * CLink read only for those user which have not access the certain pages it will be disable buttons and update funtions for perform action 
 * 
 * @return boolean
 */
function wpclink_is_acl_r_page(){
	
	// CURRENT USER
	$current_user_id = get_current_user_id();
	
	
	// CREATOR
	$creator_array = wpclink_get_option('authorized_creators');
	
	
	// PARTY
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	
	// COPYRIGHT OWNER
	$right_holder = wpclink_get_option('rights_holder');
	
		
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
	if(isset($_GET['page'])){
		$current_page = $_GET['page'];
	}else{
		return false;
	}
	
	// ONLY ADMN
	if(!wpclink_user_has_creator_list($current_user_id) and current_user_can('administrator') and $current_user_id != $clink_party_id){
		
		if($current_page == 'cl-restriction' ||
		$current_page == 'cl-restriction-page-available' ||
		$current_page == 'cl-restriction-reuse' ||
		$current_page == 'cl-restriction-page' ||
		$current_page == 'content_link_post.php' ||
		$current_page == 'content_link_page.php' ||
		$current_page == 'clink-links-inbound' ||
		$current_page == 'cl-canonical' ||
		$current_page == 'cl-audit-trail' ||
		$current_page == 'cl_templates' ||
		$current_page == 'clink-links-outbound'){
			return true;	
		}
	
	// ONLY CREATOR
	}elseif(wpclink_user_has_creator_list($current_user_id) and !current_user_can('administrator') and $right_holder_id != $current_user_id){
		
		if($current_page == 'cl_mainpage.php' || 
		$current_page == 'cl_users' || 
		$current_page == 'cl-restriction-reuse' || 
		$current_page == 'cl-restriction-page' || 
		$current_page == 'cl-canonical' ||
		$current_page == 'cl-audit-trail' ||
		$current_page == 'clink-links-inbound'){
			return true;
		}
	
	// CREATOR + COPYRGHT OWNER
	}elseif(wpclink_user_has_creator_list($current_user_id) and !current_user_can('administrator') and $right_holder_id == $current_user_id and $current_user_id != $clink_party_id){
		
		if($current_page == 'cl_mainpage.php' || 
		$current_page == 'cl_users'){
			return true;
		}
		
	}elseif(wpclink_user_has_creator_list($current_user_id) and !current_user_can('administrator') and $right_holder_id == $current_user_id and $current_user_id == $clink_party_id){
		
		if($current_page == 'cl_mainpage.php'){
			return true;
		}
		
	// CREATOR + ADMINISTRATOR
	}elseif(wpclink_user_has_creator_list($current_user_id) and current_user_can('administrator') and $right_holder_id != $current_user_id){
		
		if($current_page == 'cl-restriction-reuse' || 
		$current_page == 'cl-restriction-page' ||
		$current_page == 'cl-canonical' ||
		$current_page == 'cl-audit-trail' ||
		$current_page == 'clink-links-inbound'){
			return true;
		}
		
	// CREATOR + ADMN + COPY
	}elseif(wpclink_user_has_creator_list($current_user_id) and current_user_can('administrator') and $right_holder_id == $current_user_id){
	
	
	// CREATOR + PARTY
	}elseif(wpclink_user_has_creator_list($current_user_id) and !current_user_can('administrator') and $current_user_id == $clink_party_id and $right_holder_id != $current_user_id){
		
		if($current_page == 'cl_mainpage.php' || 
		$current_page == 'cl-restriction-reuse' || 
		$current_page == 'cl-restriction-page' ||
		$current_page == 'cl-canonical' ||
		$current_page == 'cl-audit-trail' ||
		$current_page == 'clink-links-inbound'){
			return true;
		}
		
	// CREATOR + PARTY + COPYRIGHT OWNER
	}elseif(wpclink_user_has_creator_list($current_user_id) and !current_user_can('administrator') and $current_user_id == $clink_party_id and wpclink_user_has_creator_list($current_user_id)){
		
		if($current_page == 'cl_mainpage.php'){
			return true;
		}
		
	// CREATOR + PARTY + COPYRIGHT OWNER + ADMN	
	}elseif(wpclink_user_has_creator_list($current_user_id) and current_user_can('administrator') and $current_user_id == $clink_party_id and wpclink_user_has_creator_list($current_user_id)){
		
		
		
	// CREATOR + ADMN + COPY
	}elseif(wpclink_user_has_creator_list($current_user_id) and current_user_can('administrator') and wpclink_user_has_creator_list($current_user_id)){
		
	// ONLY PARTY	
	}elseif($current_user_id == $clink_party_id and !current_user_can('administrator') and !wpclink_user_has_creator_list($current_user_id)){
		
		if($current_page == 'cl_mainpage.php' ||
		$current_page == 'cl_templates' ||
		$current_page == 'cl-restriction' ||
		$current_page == 'cl-restriction-page-available' ||
		$current_page == 'cl-restriction-reuse' ||
		$current_page == 'cl-restriction-page' ||
		$current_page == 'content_link_post.php' ||
		$current_page == 'content_link_page.php' ||
		$current_page == 'clink-links-inbound' ||
		$current_page == 'cl-canonical' ||
		$current_page == 'cl-audit-trail' ||
		$current_page == 'clink-links-outbound' 
		){
			return true;
		}
	
	// PARTY + COPY
	}elseif($current_user_id == $clink_party_id and !current_user_can('administrator') and wpclink_user_has_creator_list($current_user_id)){
		
		if($current_page == 'cl_mainpage.php' ||
		$current_page == 'content_link_post.php' ||
		$current_page == 'content_link_page.php' ||
		$current_page == 'clink-links-outbound' 
		){
			return true;
		}
		
	// PARTY + ADMN
	}elseif($current_user_id == $clink_party_id and current_user_can('administrator') and !wpclink_user_has_creator_list($current_user_id)){
		
		if($current_page == 'cl_templates' ||
		$current_page == 'cl-restriction' ||
		$current_page == 'cl-restriction-page-available' ||
		$current_page == 'cl-restriction-reuse' ||
		$current_page == 'cl-restriction-page' ||
		$current_page == 'content_link_post.php' ||
		$current_page == 'content_link_page.php' ||
		$current_page == 'clink-links-inbound' ||
		$current_page == 'cl-canonical' ||
		$current_page == 'cl-audit-trail' ||
		$current_page == 'clink-links-outbound' 
		){
			return true;
		}
			
	// PARTY + ADMN + COPY
	}elseif($current_user_id == $clink_party_id and current_user_can('administrator') and wpclink_user_has_creator_list($current_user_id)){
		
		if($current_page == 'content_link_post.php' ||
		$current_page == 'content_link_page.php' ||
		$current_page == 'clink-links-outbound' 
		){
			return true;
		}
			
	}
	
	
	// AT LAST
	return false;
}
/**
 * CLink Check the Tokem ie exists
 * @param string $token token name
 * 
 * @return boolean true | false
 */
function wpclink_is_token_valid($token = NULL){
	
	if($token == NULL) return false;
	
	global $wpdb;
		// Table Prefix
	$table_token = $wpdb->prefix . 'wpclink_tokens';
	
	$token_data = $wpdb->get_row( "SELECT * FROM $table_token WHERE token = '$token'", ARRAY_A );
	
	if ( NULL !== $token_data ) {
		
		return true;
		
	}else{
		
		return false;
	}
	
}
/**
 * Clink Get Posts
 * 
 * CLink get post list which  are on the referenet list allow to be get only linked sites.
 * 
 * @param array $get_perameter perameter
 * 
 * @return array post ids
 */
function wpclink_get_posts($get_perameter = array()){
	// CONTENT LICENSE
	$license_content_ids = wpclink_get_all_referent_post_ids_by_IP();
	
	// REQUESTED POSTS
	$requested = $get_perameter; 	
	
	// ALL REFRENT POSTS
	$referent_posts = wpclink_get_option('referent_posts');
	
	if(empty($referent_posts)) return array(0);
	
	
	$complete = array();
	
	foreach($requested as $req){
		if(in_array($req, $referent_posts)){
				$complete[] = $req;			
		}
	}
	
	
	$posts_ids = array();
	
	foreach($license_content_ids as $single_id){
		if(in_array($single_id, $complete)){
				$posts_ids[] = $single_id;			
		}
	}
	
	return $posts_ids;
					
}
/**
 * Clink Get Pages
 * 
 * CLink get pages list which  are on the referenet list allow to be get only linked sites.
 * 
 * @param array $get_perameter perameter
 * 
 * @return array pages ids
 */
function wpclink_get_pages($get_perameter = array(),$site_url = ''){
	
	if ( defined( 'WPCLINK_IP_CHECK' ) ) {
		if(WPCLINK_IP_CHECK){
			$license_content_ids = wpclink_get_all_referent_post_ids_by_IP();
		}else{
			// Function
			
			$license_content_ids = wpclink_get_all_referent_post_ids_by_site_url($site_url);
		}
	}else{
		$license_content_ids = wpclink_get_all_referent_post_ids_by_IP();
	}
	

	// REQUESTED POSTS
	$requested = $get_perameter; 	
	
	// ALL REFRENT POSTS
	$referent_pages = wpclink_get_option('referent_pages');
	
	if(empty($referent_pages)) return array(0);
	
	
	$complete = array();
	
	foreach($requested as $req){
		if(in_array($req, $referent_pages)){
				$complete[] = $req;			
		}
	}
	
	
	$pages_ids = array();
	
	foreach($license_content_ids as $single_id){
		if(in_array($single_id, $complete)){
				$pages_ids[] = $single_id;			
		}
	}
	
	return $pages_ids;
	
	
}
/**
 * Clink Get Attachments
 * 
 * CLink get pages list which  are on the referenet list allow to be get only linked sites.
 * 
 * @param array $get_perameter perameter
 * 
 * @return array pages ids
 */
function wpclink_get_attachments($get_perameter = array(),$site_url = ''){
	
	if ( defined( 'WPCLINK_IP_CHECK' ) ) {
		if(WPCLINK_IP_CHECK){
			$license_content_ids = wpclink_get_all_referent_post_ids_by_IP();
		}else{
			// Function
			
			$license_content_ids = wpclink_get_all_referent_post_ids_by_site_url($site_url);
		}
	}else{
		$license_content_ids = wpclink_get_all_referent_post_ids_by_IP();
	}
	

	// REQUESTED POSTS
	$requested = $get_perameter; 	
	
	// ALL REFRENT POSTS
	$referent_attachments = wpclink_get_option('referent_attachments');
	
	if(empty($referent_attachments)) return array(0);
	
	
	$complete = array();
	
	foreach($requested as $req){
		if(in_array($req, $referent_attachments)){
				$complete[] = $req;			
		}
	}
	
	
	$pages_ids = array();
	
	foreach($license_content_ids as $single_id){
		if(in_array($single_id, $complete)){
				$pages_ids[] = $single_id;			
		}
	}
	
	return $pages_ids;
	
	
}
/**
 * Clink Get Content
 * 
 * CLink get all content which  are on the referenet list allow to be get only linked sites.
 * 
 * @param array $get_perameter perameter
 * @param array $post_type post type
 * 
 * @return array content ids
 */
function wpclink_get_all_posts($get_perameter = false, $post_type = 'post', $site_url = ''){
	if($post_type == 'post'){
		return wpclink_get_posts($get_perameter,$site_url);
	}elseif($post_type == 'page'){
		return wpclink_get_pages($get_perameter,$site_url);
	}elseif($post_type == 'attachment'){
		return wpclink_get_attachments($get_perameter,$site_url);
	}
	
}
/**
 * CLink Get Posts by post type
 * 
 * @param string $post_type post type
 * @param string site_url site Address
 * 
 * @return array post ids
 */
function wpclink_posts_show($post_type = 'post', $site_url = ''){
	
	
			
	$license_content_ids = wpclink_get_all_referent_post_ids_by_site_url($site_url);
	
	
		
	if($post_type == 'post'){
		
		// ALL REFRENT POSTS
		$referent_posts = wpclink_get_option('referent_posts'); 
		$post_ids = array();
		
		if(empty($referent_posts)) return array(0);
		
		
		foreach($referent_posts as $refpost){
			if(in_array($refpost,$license_content_ids)){
				$post_ids[] = $refpost;
			}
		}
		
		if(empty($post_ids)) return array(0);	
		return $post_ids;
		
		
	}elseif($post_type == 'page'){
		
		
		$referent_pages = wpclink_get_option('referent_pages'); 	
		$page_ids = array();
		
		if(empty($referent_pages)) return array(0);	
			
		foreach($referent_pages as $refpage){
			if(in_array($refpage,$license_content_ids)){
				$page_ids[] = $refpage;
			}
		}
		
				
		if(empty($page_ids)) return array(0);	
		return $page_ids;
			
	}elseif($post_type == 'attachment'){
		
		
		$referent_attachments = wpclink_get_option('referent_attachments'); 	
		$attachment_ids = array();
		
		if(empty($referent_attachments)) return array(0);	
			
		foreach($referent_attachments as $refatt){
			if(in_array($refatt,$license_content_ids)){
				$attachment_ids[] = $refatt;
			}
		}
		
				
		if(empty($attachment_ids)) return array(0);	
		return $attachment_ids;
			
	}
	
}

/**
 * CLink Post in Posts Referent List
 * 
 * @param integer $post_id post id  
 * 
 * @return boolean
 */

function wpclink_post_in_post_referent_list($post_id = 0){
	// Post Referent List
	$restrict = wpclink_get_option('referent_posts');
	
	if(in_array($post_id,$restrict)){
		return true;
	}else{
		return false;
	}
}
/**
 * CLink Attachment in Attachments Referent List
 * 
 * @param integer $post_id post id  
 * 
 * @return boolean
 */
function wpclink_attachment_in_attachment_referent_list($post_id = 0){
	// Post Referent List
	$restrict = wpclink_get_option('referent_attachments');
	
	if(in_array($post_id,$restrict)){
		return true;
	}else{
		return false;
	}
}
/**
 * CLink Type CLink ID only
 * 
 * @param string $identifier Identifier 
 * 
 * @return string identifier
 */

function wpclink_strip_prefix_clink_ID($identifier = 0){
	
	if($clinkid_prefix = wpclink_get_option('clinkid_prefix')){
		return $identifier;
	}
	
	$replace_identifier = array('20.500.12200/','20.500.12200.');
	
	$return_identifier = str_replace($replace_identifier,'',$identifier);	
	
	return $return_identifier;
}
/**
 * CLink Type CLink.ID with Logo
 * 
 * @param string $identifier identifier  
 * @param boolean $single_quote quotes
 * 
 * @return string identifier
 */

function wpclink_do_icon_clink_ID($identifier = 0,$single_quote = false){
	
	if($clinkid_prefix = wpclink_get_option('clinkid_prefix')){
		return $identifier;
	}
	
	$replace_identifier = array('20.500.12200/','20.500.12200.');
	
	if($single_quote){
		$return_identifier = str_replace($replace_identifier,"<span class='clink-typo'></span>",$identifier);
	}else{
		$return_identifier = str_replace($replace_identifier,'<span class="clink-typo"></span>',$identifier);	
	}
	
	return $return_identifier;
}
/**
 * CLink Type CLink ID with Image
 * 
 * @param string $identifier  Identifier
 * @param string $margin margins css  
 * 
 * @return string identifier
 */

function wpclink_do_icon($identifier = 0, $margin = '0'){
	
	if($clinkid_prefix = wpclink_get_option('clinkid_prefix')){
		return $identifier;
	}
	
	$replace_identifier = array('20.500.12200/','20.500.12200.');
	
	$image_url = esc_url( plugins_url( 'wpclink/admin/images/ico-orange1.png', dirname( WPCLINK_MAIN_FILE ) ) );
	$return_identifier = str_replace($replace_identifier,'<img style="margin:'.$margin.'; width:15px; height:9px;" src="'.$image_url.'" /> ',$identifier);	
	
	return $return_identifier;
}
/**
 * CLink CLink.ID HTML Hyperlink Generate
 * 
 * @param string $clinkid  clink id 
 * 
 * @return string hyperlink
 */
function wpclink_add_hyperlink_to_clink_ID_no_prefix($clinkid = ''){
	return '<a href="'.WPCLINK_ID_URL.'/#objects/'.$clinkid.'" target="_blank">'.wpclink_strip_prefix_clink_ID($clinkid).'</a>';
}
/**
 * CLink Get Current Site Language
 * 
 * @return string language code
 */
function wpclink_get_current_site_lang(){
	
	// Local Language Installed
	$locale = get_locale();
	
	
	if(empty($locale) || $locale == 'en_US'){
		
		return 'English (United States)';
		
	}else{
	// Get Language Name
	$languages = get_site_transient( 'available_translations' );
	$language_data = $languages[$locale];
		
		return $language_data['english_name'];
	}
	
}
/**
 * CLink Get Get Current Terrriotity Name
 * 
 * @return string language code
 */
function wpclink_get_current_terriority_name(){
	
	// Selected
	$country_codes_selected = wpclink_get_option('territory_code');
	
	// List
	$contries_list = wpclink_get_territories();
	
	if(empty($country_codes_selected)){
		return 'United States';
	}else{
		return $contries_list[$country_codes_selected];
	}
	
}
/**
 * CLink Get Territories
 * 
 * @return array territories
 */
function wpclink_get_territories(){
	
	$countries = Array(
		'US' => 'United States',
		'AF' => 'Afghanistan',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AG' => 'Antigua And Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia And Herzegovina',
		'BW' => 'Botswana',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CO' => 'Colombia',
		'CG' => 'Congo',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Cote D\'ivoire',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'CD' => 'Democratic Republic of the Congo',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FO' => 'Faroe Islands',
		'FM' => 'Federated States Of Micronesia',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GN' => 'Guinea',
		'GW' => 'Guinea Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran',
		'IE' => 'Ireland',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Laos',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'MX' => 'Mexico',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'MD' => 'Republic Of Moldova',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russia',
		'RW' => 'Rwanda',
		'KN' => 'Saint Kitts And Nevis',
		'LC' => 'Saint Lucia',
		'VC' => 'Saint Vincent And The Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome And Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SG' => 'Singapore',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'ZA' => 'South Africa',
		'KR' => 'South Korea',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TG' => 'Togo',
		'TO' => 'Tonga',
		'TT' => 'Trinidad And Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Vietnam',
		'VG' => 'Virgin Islands British',
		'VI' => 'Virgin Islands U.S.',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe'
	);
	
	return $countries;
}
/**
 * CLink License Label
 * 
 * @return string label
 */
function wpclink_get_license_label($slug = ''){
	
	if($slug == 'pass'){
		$label =  'Good standing';
	}else if($slug == 'fail'){
		$label =  'Violation';
	}else if($slug == 'notfound'){
		$label =  'Not found';
	}else if($slug == 'found'){
		$label =  'Found';
	}else if($slug == 'servererror'){
		$label =  'Server error';
	}else if($slug == 'content_delivered'){
		$label =  'Content delivered';
	}else if($slug == 'delivered'){
		$label =  'License delivered';
	}else{
		$label = $slug;
	}
	
	return $label;
}
/**
 * CLink Inbound Link Dropdown Filter
 * 
 * @return string dropdown
 */
function wpclink_display_menu_dropdown_filter_links_inbound($filter = ''){
	
		if(empty($filter)) return;

		$get_filter = $_GET[$filter];

		$filter_list = wpclink_get_options_menu_dropdown_filter_links_inbound($filter);
		foreach($filter_list as $single_filter){
			$selected = '';

			if(urldecode($get_filter) == $single_filter){
				$selected = 'selected="selected"';
			}
			echo '<option '.$selected.' value="'.urlencode($single_filter).'">'.wpclink_get_license_label($single_filter).'</option>';
		}
}
