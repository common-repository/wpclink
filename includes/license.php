<?php
/**
 * CLink License Functions
 *
 * CLink creation license functions
 *
 * @package CLink
 * @subpackage Link Manager
 */

// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Request to Update the Content License Class for CLink.ID
 * 
 * @param integer $post_id post id for update
 * @param integer $license_class license Class UC-UT-UM | UC-AT-UM
 * 
 */

function wpclink_register_make_referent( $post_id, $license_class = '',$admin_page = '') {
	
	// RULE #1
	$current_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option('authorized_creators');
	
	
	$right_holder = wpclink_get_option('rights_holder');
	
	
	// RULE #4
	if(wpclink_clink_domain_quota() > 0){
		
	}else{
		return false;
	}
	
	
	
	// Party
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	
	// API REQUEST
	$post_title = get_the_title($post_id);
	$guid = get_the_guid($post_id);
	$permalink = get_permalink($post_id);
	// If Rights holder is not exist then automatically author is the rights holder 
	if($creation_right_holder_user_id = get_post_meta($post_id,'wpclink_rights_holder_user_id',true)){
		$right_holder_id = $creation_right_holder_user_id;
		$author_id = $creation_right_holder_user_id;
		$post_author_id = $creation_right_holder_user_id;
		$current_user_id = $creation_right_holder_user_id;
	}else{
		// Author ID
		$author_id = get_post_field ('post_author', $post_id);
		$right_holder_id = $author_id;
		$post_author_id = get_post_field( 'post_author', $post_id );
		$current_user_id = get_current_user_id();
	}
	$creator_id = get_user_meta($author_id,'wpclink_party_ID',true);
	$post_excerpts = get_the_excerpt($post_id);
	$post_type = get_post_type($post_id);
	$post_character = 'language';
	$clink_party_id = get_user_meta($clink_party_id,'wpclink_party_ID',true);
		
	
	$right_holder = wpclink_get_option('rights_holder');
	
	
	$content = apply_filters('the_content', get_post_field('post_content', $post_id));
	$content_without_html = strip_tags($content);
	$content_word_count = str_word_count($content_without_html);
	
	// Taxonomy Permission
	if($taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true)){
	}else{
		$taxonomy_permission = '';
	}
	$right_holder_creation_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	
	$right_holder_user_info = get_userdata($right_holder_id);
	
	
	// If content is linked and has referent copyright Owner
	if($referent_copyright_id = get_post_meta($post_id,'wpclink_referent_rights_holder_party_ID',true)){
		
		$right_holder_user_id = $referent_copyright_id;
	}
	
	
	if($wpclink_creation_access_key = get_post_meta($post_id,'wpclink_creation_access_key',true)){
		
		$wpclink_creation_access_key = $wpclink_creation_access_key;
	}
	
	
/* ====== Creation Right Holder ========= */
$creation_right_holder_user_id = get_post_meta($post_id,'wpclink_rights_holder_user_id',true);
$creation_right_holder_data = get_userdata($creation_right_holder_user_id);
$creation_right_holder_display_name = $creation_right_holder_data->display_name;
$creation_right_holder_identifier = get_user_meta($creation_right_holder_user_id,'wpclink_party_ID',true);
	
/* ====== Creator Display Name ========== */
$creator_user_id =  get_userdata($author_id);
$creator_user_data = get_userdata($author_id);
$creator_user_display = $creator_user_data->display_name;
	
	
	$post_year = get_the_date( 'Y', $post_id );
	$clink_language = wpclink_get_current_site_lang();
	$clink_terriory_code = wpclink_get_current_terriority_name();
	$content_id = '';
	$clink_referent_creation_identifier = '';
	$clink_party_link_role = '';
	$clink_creation_link_role = '';
	
	
	$time = current_time('Y-m-d',true);
	$time .= 'T';
	$time .= current_time('G:i:s',true);
	$time .= 'Z';
	
	$get_post_date  = get_the_date('Y-m-d',$post_id);
	$get_post_date .= 'T';
	$get_post_date .= get_the_date('G:i:s',$post_id);
	$get_post_date .= 'Z';
			
	$get_post_modified_date  = get_the_modified_time('Y-m-d',$post_id);
	$get_post_modified_date .= 'T';
	$get_post_modified_date .= get_the_modified_time('G:i:s',$post_id);
	$get_post_modified_date .= 'Z';
		
	$creation_date = $time;
	
	$modified_date = get_the_modified_time('U',$post_id);
	
	$domain_access_key = wpclink_get_option('domain_access_key');
	
	
	$post_categories = wp_get_post_categories($post_id);
	//$post_categories = get_the_terms( $post_id, 'category' );
	
	
	$creator_display_name =  get_userdata($author_id);
	
	
	
	
	$cats = array();
     
	foreach($post_categories as $c){
		$cat = get_category( $c );
		$cats[] = $cat->name;
	}
	
	wpclink_debug_log('CATEGORY CONTENT '.print_r($cats,true));
	wpclink_debug_log('ALL DATA '.print_r($_REQUEST,true));
	
	
	$post_tags = wp_get_post_tags( $post_id );
	
	$tags = array();
	
	foreach($post_tags as $t){
		$tags[] = $t->name;
	}
	
	/*____URL____*/
	
	$url_content = WPCLINK_CREATION_API;
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	

	if($clink_referent_creation_identifier = get_post_meta($post_id,'wpclink_referent_creation_ID',true)){
		
	}else{
		$clink_referent_creation_identifier = '';
	}
	
	$archive_link = wpclink_get_last_archive($post_id);
	
	$sending = array(
                    'body' => array(
						'CLinkContentID' => $contentID,
                        'post_title'  => html_entity_decode($post_title),
                        'guid'	=> $guid,
						'reuseGUID' => $guid,
						'creation_uri' => $permalink,
                        'post_excerpts' => html_entity_decode($post_excerpts),
						'post_type' => strtolower($post_type),
						'clink_word_count' => $content_word_count,
						'post_charcter' => $post_character,
						'clink_partyID' => $clink_party_id,
						'clink_creator_display_name' => $creator_display_name->display_name,
						// Creator ID							
						'clink_creatorID' => $creator_id, 
						// Creation Right Holder Display Name
						'creation_rights_holder_display_name' => $creation_right_holder_display_name, 
						// Creation Right Holder Identifier
						'creation_rights_holder_party_ID' => $creation_right_holder_identifier,
						// Creation Right Holder Identifier
						'creator_display_name' => $creator_user_display,
						// Referent Creation Identifier
						'referent_creation_ID'=> $clink_referent_creation_identifier,
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
						'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
						'clink_party_link_role' => $clink_party_link_role,
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'clink_language' => $clink_language,
						'clink_taxonomy_permission' => $taxonomy_permission,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'creation_access_key' => $wpclink_creation_access_key,
						'archive_web_url' => $archive_link,
						'action'=> 'update'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
	
	if($creation_license_class = get_post_meta( $post_id, 'wpclink_creation_license_class', true )){
		// Rescricted License Only				
		if($creation_license_class == 'uc-at-um' || $creation_license_class == 'uc-ut-um'){	
			$sending['body']['clink_license_version'] = '0.9';
			$sending['body']['clink_license_class'] = wpclink_get_license_class_label($creation_license_class);
		}
	}
	
	if($referent_uri = get_post_meta($post_id,'wpclink_referent_post_uri',true)){
		
			$sending['body']['reuseGUID'] = $referent_uri;
	}
	
	wpclink_debug_log('UPDATE LICENSE GUL '.print_r($sending,true));
	
/* ========= UPDATE ============ */
	$response = wp_remote_post(
                $url_content,
                apply_filters('wpclink_register_update',$sending,$post_id)
            );
						
			
 
            if ( is_wp_error( $response ) ) {
 
               $wp_error = is_wp_error( $response );
				
			   // Response Debug
				wpclink_debug_log('UPDATE CONTENT '.print_r($response,true));
				
				
				if($wp_error == 1){
					$response_check = wpclink_return_wp_error($response);	
					
					
					if(!empty($admin_page)){
						
						update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
						update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
						
						$admin_page = add_query_arg( 'post_error', '1', $admin_page );
						$admin_page = add_query_arg( 'post_id', $post_id, $admin_page );
						wp_redirect( $admin_page, 301 );
						exit;
					}
					
					
					// Error
					echo json_encode($response_check);
					die();
				}
			   
			 
 
            }else {
				
				// Response Debug
				wpclink_debug_log('UPDATE CONTENT '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   
	$return_response = wpclink_return_api_reponse($response);
		if($return_response === true){
			
			/* Check response has is_error */
			$response_check = wpclink_response_check($resposne_Array);
			
			if($response_check == false){
				// OK
			}else{
				
					if(!empty($admin_page)){
						
						update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
						update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
						
						
						$admin_page = add_query_arg( 'post_error', '1', $admin_page );
						$admin_page = add_query_arg( 'post_id', $post_id, $admin_page );
						wp_redirect( $admin_page, 301 );
						exit;
					}
				
				
				// Error
				echo json_encode($response_check);
				die();
			}
			
		}else{
			
					if(!empty($admin_page)){
						
						update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
						update_post_meta( $post_id, 'wpclink_loader_error_data', $return_response );
						
						$admin_page = add_query_arg( 'post_error', '1', $admin_page );
						$admin_page = add_query_arg( 'post_id', $post_id, $admin_page );
						wp_redirect( $admin_page, 301 );
						exit;
					}
				// Error
				echo json_encode($return_response);
				die();
				
		}
		
  
  
	if($resposne_Array['status'] == 'update'){
		// RESPONSE
		
	update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
	update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
	//update_post_meta($post_id,'wpclink_rights_holder_user_id',$author_id);
	if(!empty($resposne_Array['data']['creation_access_key'])){
		// Encrypt
		update_post_meta($post_id,'wpclink_creation_access_key',$resposne_Array['data']['creation_access_key']);
	}
		
	// Right Create Time
		if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_creation_date' ] ) ) {

			$right_time =  explode('.',$resposne_Array[ 'data' ][ 'clink_right_creation_date' ]);

			// Update Rights Create Time ID
			update_post_meta( $post_id, 'wpclink_right_created_time', $right_time[0].'Z'  );

		}
	
	
		
	}
}
}
function wpclink_register_remove_referent_media( $attachment_id ) {
	
	$creator_user_id = get_post_field( 'post_author', $attachment_id );
	
	$attachment_url = wpclink_iptc_image_path( $attachment_id, 'full' );
	
	$reg_creators = wpclink_get_option( 'authorized_creators' );
	
	
	// Meta data Creator Names	
	$metadata_image_array = wpclink_get_image_metadata_value($attachment_url,array('IPTC:By-line','IPTC:CopyrightNotice','IPTC:Credit','IPTC:ObjectName','IPTC:Keywords'));
	
	$creator_name_metadata = $metadata_image_array['IPTC:By-line'];
	
	/* -- REGISTRATION -- */
	
	// Title
	$attachment_title = get_the_title( $attachment_id );
	// Title
	$attachment_post_url = wpclink_get_image_URL( $attachment_id );
	// File URL
	$file_url = wpclink_get_image_URL( $attachment_id );
	// Excerpt
	$attachment_excerpt = wp_get_attachment_caption( $attachment_id );
	// Creator
	$creator_user_info = get_userdata( $creator_user_id );
	// Domain ID
	$domain_access_key = wpclink_get_option( 'domain_access_key' );
	
	// Copyright Notice
	$copyright_notice = $metadata_image_array['IPTC:CopyrightNotice'];
	
	// Credit Line
	$creditline =  $metadata_image_array['IPTC:Credit'];
	
	//Creator ID
	$creator_id = get_user_meta($creator_user_id,'wpclink_party_ID',true);
	
	// IPTC Title
	$iptc_title = $metadata_image_array['IPTC:ObjectName'];
	
	// Keywords
	$keywords =  $metadata_image_array['IPTC:Keywords'];
	
	if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
		$action = 'update';
		$time = get_the_date('Y-m-d',$attachment_id);
		$time .= 'T';
		$time .= get_the_date('G:i:s',$attachment_id);
		$time .= 'Z';
	}
	
	$time_data = wpclink_get_image_datetime($attachment_id);
	$time_of_creation = $time_data['created'];
	$time_of_modification = $time_data['modified'];
	
		$post_guid = wp_get_original_image_url($attachment_id);
	
	
	$archive_link = wpclink_get_last_archive($attachment_id);
	
	if($programattic_rights_category = get_post_meta($attachment_id,'wpclink_programmatic_right_categories',true)){
		// Reuse GUID
		$media_license_url = get_bloginfo('url');
		$media_license_url = add_query_arg( 'clink_media_license', '', $media_license_url);
		$reuse_GUID = add_query_arg( 'id', $attachment_id, $media_license_url);
	}else{
		$reuse_GUID = '';
	}
	
	
	if($clink_taxonomy_permission = get_post_meta( $attachment_id, 'wpclink_programmatic_right_categories', true )){
	}else{
		$clink_taxonomy_permission = '';
	}
	
	if($clink_referent_creation_ID = get_post_meta( $attachment_id, 'wpclink_referent_creation_ID', true )){
	}else{
		$clink_referent_creation_ID = '';
	}
	
	if($clink_referent_creator_ID = get_post_meta( $attachment_id, 'wpclink_referent_creator_party_ID', true )){
	}else{
		$clink_referent_creator_ID = '';
	}
	
	$archive_link = wpclink_get_last_archive($attachment_id);
	
	$url_media = WPCLINK_MEDIA_API;
	// Register to CLink.ID
	$response = wp_remote_post(
		$url_media,
		array(
			'body' => array(
				'CLinkContentID' => $creationID,
				'post_title' => html_entity_decode( $attachment_title ),
				'iptc_title' => $iptc_title,
				'clink_referent_creation_identifier' => $clink_referent_creation_ID,
				'referent_clink_creatorID' => $clink_referent_creator_ID,
				'keywords' => $keywords,
				'creator_uri' => $creator_user_info->user_url,
				'attachment_post_url' => $attachment_post_url,
				'creation_GUID' => get_the_guid($attachment_id),
				'reuseGUID' => $reuse_GUID,
				'creator_display_name' => $creator_user_info->display_name,
				'creator_email' => $creator_user_info->user_email,
				'post_excerpts' => $attachment_excerpt,
				'time_of_creation' => $time_of_creation,
				'time_of_modification' => $time_of_modification,
				'domain_access_key' => $domain_access_key,
				'site_address' => get_bloginfo( 'url' ),
				'iscc' => $iscc,
				'creditline' => $creditline,
				'clink_creatorID' => $creator_id,
				'creation_access_key' => $creation_access_key,
				'copyright_notice' => $copyright_notice,
				'archive_web_url' => $archive_link,
				'action' => $action
			), 'timeout' => WPCLINK_API_TIMEOUT, 'method' => 'POST'
		)
	);
	
	
			
	
}
/**
 * CLink Request to Remove the Content License Class for CLink.ID
 * 
 * @param integer $post_id post id for update
 * 
 */
function wpclink_register_remove_referent( $post_id, $admin_page = '' ) {
	
	// RULE #1
	$current_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option('authorized_creators');
	
	$right_holder_id = get_post_field( 'post_author', $post_id );
	
	$right_holder = wpclink_get_option('rights_holder');
	
	
	
	
	
	// RULE #4
	if(wpclink_clink_domain_quota() > 0){
		
	}else{
		return false;
	}
	
	
	
	// Party
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	
	// API REQUEST
	$post_title = get_the_title($post_id);
	$guid = get_the_guid($post_id);
	$permalink = get_permalink($post_id);
	// Author ID
		// If Rights holder is not exist then automatically author is the rights holder 
	if($creation_right_holder_user_id = get_post_meta($post_id,'wpclink_rights_holder_user_id',true)){
		$right_holder_id = $creation_right_holder_user_id;
		$author_id = $creation_right_holder_user_id;
		$post_author_id = $creation_right_holder_user_id;
		$current_user_id = $creation_right_holder_user_id;
	}else{
		// Author ID
		$author_id = get_post_field ('post_author', $post_id);
		$right_holder_id = $author_id;
		$post_author_id = get_post_field( 'post_author', $post_id );
		$current_user_id = get_current_user_id();
	}
	$creator_id = get_user_meta($author_id,'wpclink_party_ID',true);
	$post_excerpts = get_the_excerpt($post_id);
	$post_type = get_post_type($post_id);
	$post_character = 'language';
	$clink_party_id = get_user_meta($clink_party_id,'wpclink_party_ID',true);
	
		
	
	$right_holder = wpclink_get_option('rights_holder');
	
	
	$right_holder_id = $author_id;
		
	
	$content = apply_filters('the_content', get_post_field('post_content', $post_id));
	$content_without_html = strip_tags($content);
	$content_word_count = str_word_count($content_without_html);
	
	$right_holder_user_id = $right_holder_id;
	$right_holder_user_id = get_user_meta($right_holder_user_id,'wpclink_party_ID',true);
	
	$right_holder_creation_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	
	$right_holder_user_info = get_userdata($right_holder_id);
	
	$creator_display_name =  get_userdata($author_id);
	
	
	// If content is linked and has referent copyright Owner
	if($referent_copyright_id = get_post_meta($post_id,'wpclink_referent_rights_holder_party_ID',true)){
		
		$right_holder_user_id = $referent_copyright_id;
	}
	
	
	if($wpclink_creation_access_key = get_post_meta($post_id,'wpclink_creation_access_key',true)){
		
		$wpclink_creation_access_key = $wpclink_creation_access_key;
	}
	
/* ====== Creation Right Holder ========= */
$creation_right_holder_user_id = get_post_meta($post_id,'wpclink_rights_holder_user_id',true);
$creation_right_holder_data = get_userdata($creation_right_holder_user_id);
$creation_right_holder_display_name = $creation_right_holder_data->display_name;
$creation_right_holder_identifier = get_user_meta($creation_right_holder_user_id,'wpclink_party_ID',true);
	
/* ====== Creator Display Name ========== */
$creator_user_id =  get_userdata($author_id);
$creator_user_data = get_userdata($author_id);
$creator_user_display = $creator_user_data->display_name;

	
	
	$post_year = get_the_date( 'Y', $post_id );
	$clink_language = wpclink_get_current_site_lang();
	$clink_terriory_code = wpclink_get_current_terriority_name();
	$content_id = '';
	$clink_referent_creation_identifier = '';
	$clink_party_link_role = '';
	$clink_creation_link_role = '';
	
	
	$time = current_time('Y-m-d',true);
	$time .= 'T';
	$time .= current_time('G:i:s',true);
	$time .= 'Z';
	
	$get_post_date  = get_the_date('Y-m-d',$post_id);
	$get_post_date .= 'T';
	$get_post_date .= get_the_date('G:i:s',$post_id);
	$get_post_date .= 'Z';
			
	$get_post_modified_date  = get_the_modified_time('Y-m-d',$post_id);
	$get_post_modified_date .= 'T';
	$get_post_modified_date .= get_the_modified_time('G:i:s',$post_id);
	$get_post_modified_date .= 'Z';
		
	$creation_date = $time;
	
	$modified_date = get_the_modified_time('U',$post_id);
	
	$domain_access_key = wpclink_get_option('domain_access_key');
	
	
	$post_categories = wp_get_post_categories($post_id);
	//$post_categories = get_the_terms( $post_id, 'category' );
	
	
	
	
	$cats = array();
     
	foreach($post_categories as $c){
		$cat = get_category( $c );
		$cats[] = $cat->name;
	}
	
	wpclink_debug_log('CATEGORY CONTENT '.print_r($cats,true));
	wpclink_debug_log('POST CONTENT '.print_r($post,true));
	wpclink_debug_log('ALL DATA '.print_r($_REQUEST,true));
	
	
	$post_tags = wp_get_post_tags( $post_id );
	
	$tags = array();
	
	foreach($post_tags as $t){
		$tags[] = $t->name;
	}
	
	/*____URL____*/
	
	$url_content = WPCLINK_CREATION_API;
	
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	

	if($clink_referent_creation_identifier = get_post_meta($post_id,'wpclink_referent_creation_ID',true)){
		
	}else{
		$clink_referent_creation_identifier = '';
	}
	
	
	$archive_link = wpclink_get_last_archive($post_id);
	
	$sending = array(
                    'body' => array(
						'CLinkContentID' => $contentID,
                        'post_title'  => html_entity_decode($post_title),
                        'guid'	=> $guid,
						'creation_uri' => $permalink,
                        'post_excerpts' => html_entity_decode($post_excerpts),
						'post_type' => strtolower($post_type),
						'clink_word_count' => $content_word_count,
						'post_charcter' => $post_character,
						'clink_partyID' => $clink_party_id,
						'clink_creator_display_name' => $creator_display_name->display_name,
						// Creator ID							
						'clink_creatorID' => $creator_id, 
						// Creation Right Holder Display Name
						'creation_rights_holder_display_name' => $creation_right_holder_display_name, 
						// Creation Right Holder Identifier
						'creation_rights_holder_party_ID' => $creation_right_holder_identifier,
						// Creation Right Holder Identifier
						'creator_display_name' => $creator_user_display,
						// Referent Creation Identifier
						'referent_creation_ID'=> $clink_referent_creation_identifier,
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
						'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
						'clink_party_link_role' => $clink_party_link_role,
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'clink_language' => $clink_language,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'creation_access_key' => $wpclink_creation_access_key,
						'archive_web_url' => $archive_link,
						'action'=> 'update'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
	
	if($creation_license_class = get_post_meta( $post_id, 'wpclink_creation_license_class', true )){
		// Rescricted License Only				
		if($creation_license_class == 'uc-at-um' || $creation_license_class == 'uc-ut-um'){	
			$sending['body']['clink_license_version'] = '0.9';
			$sending['body']['clink_license_class'] = wpclink_get_license_class_label($creation_license_class);
		}
	}
	
	if($referent_uri = get_post_meta($post_id,'wpclink_referent_post_uri',true)){
		
			$sending['body']['reuseGUID'] = $referent_uri;
	}
	
	wpclink_debug_log('UPDATE LICENSE GUL '.print_r($sending,true));
	
/* ========= UPDATE ============ */
	$response = wp_remote_post(
                $url_content,
                $sending
            );
						
			
 
            if ( is_wp_error( $response ) ) {
 
              
	 
               $wp_error = is_wp_error( $response );
				
			   // Response Debug
				wpclink_debug_log('UPDATE CONTENT '.print_r($response,true));
				
				
				if($wp_error == 1){
					$response_check = wpclink_return_wp_error($response);	
					
					
					if(!empty($admin_page)){
						
						update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
						update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
						
						$admin_page = add_query_arg( 'post_error', '1', $admin_page );
						$admin_page = add_query_arg( 'post_id', $post_id, $admin_page );
						wp_redirect( $admin_page, 301 );
						exit;
					}
					
					
					// Error
					echo json_encode($response_check);
					die();
				}
	 
			   
			  
 
            }else {
				
				// Response Debug
				wpclink_debug_log('UPDATE CONTENT '.print_r($response,true));
  				$response_json = $response['body'];
	 			$resposne_Array=json_decode($response_json,true);
			   
		$return_response = wpclink_return_api_reponse($response);
		if($return_response === true){
			
			/* Check response has is_error */
			$response_check = wpclink_response_check($resposne_Array);
			
			if($response_check == false){
				// OK
			}else{
				
					if(!empty($admin_page)){
						
						update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
						update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
						
						
						$admin_page = add_query_arg( 'post_error', '1', $admin_page );
						$admin_page = add_query_arg( 'post_id', $post_id, $admin_page );
						wp_redirect( $admin_page, 301 );
						exit;
					}
				
				
				// Error
				echo json_encode($response_check);
				die();
			}
			
		}else{
			
					if(!empty($admin_page)){
						
						update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
						update_post_meta( $post_id, 'wpclink_loader_error_data', $return_response );
						
						$admin_page = add_query_arg( 'post_error', '1', $admin_page );
						$admin_page = add_query_arg( 'post_id', $post_id, $admin_page );
						wp_redirect( $admin_page, 301 );
						exit;
					}
				// Error
				echo json_encode($return_response);
				die();
				
		}
			   
			 			 
  
  
	if($resposne_Array['status'] == 'update'){
		// RESPONSE
		
	update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
	update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
	update_post_meta($post_id,'wpclink_rights_holder_user_id',$author_id);
		
		
	if(!empty($resposne_Array['data']['creation_access_key'])){
		// Encrypt
		update_post_meta($post_id,'wpclink_creation_access_key',$resposne_Array['data']['creation_access_key']);
	}
	
	
		
	}
}
}

/**
 * CLink Get All by Site Address
 * 
 * @return array content ids
 */
function wpclink_get_all_referent_post_ids_by_site_url($site_url = ''){
	
	global $wpdb;
	
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	
	// QUERY
	$license_promote = $wpdb->get_results( "SELECT * FROM $clink_sites_table WHERE  verification_status = 'pass' AND mode = 'referent' ", ARRAY_A );
	
	$content_ids = array();
	
	
	foreach($license_promote as $single_license){
		
		// License Site
		$licensed_site = $single_license['site_url'];
		$license_site_url = wpclink_clean_url($licensed_site);
		
		// Site URL
		$site_url = wpclink_clean_url($site_url);
		
		// Site match after filter
		if($license_site_url == $site_url){
			
		// Linked Post ID
		$content_ids[] = $single_license['post_id'];
			
		}
		
	}
	
	return $content_ids;
	
}
/**
 * CLink Host IP match to Link IP
 * 
 */
function wpclink_host_ip_match_to_link_ip($site_url = ''){
	
	global $wpdb;
	
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	
	$ip_address = wpclink_get_client_ip();
	
	// QUERY
	$license_promote = $wpdb->get_results( "SELECT * FROM $clink_sites_table WHERE mode = 'referent' ", ARRAY_A );
	
	$response = 'none';
	
	foreach($license_promote as $single_license){
		
		// License Site
		$licensed_site = $single_license['site_url'];
		$license_site_url = wpclink_clean_url($licensed_site);
		
		// Site URL
		$site_url = wpclink_clean_url($site_url);
		
		// Site match after filter
		if($license_site_url == $site_url){
			
			
			// Now IP match to Site
			if($single_license['site_IP'] == $ip_address){
				// Fine
				$response = 'match';
				break;
			}else{
				// Not Match
				$response = 'not_match';
				break;
			}
			

		}else{
			$response = 'none';
		}
		
	}
	
	return $response;
	
}
/**
 * CLink Get All Content IDs From IP
 * 
 * @return array content ids
 */
function wpclink_get_all_referent_post_ids_by_IP(){
	
	
	$ip_address = wpclink_get_client_ip();
	
	
	global $wpdb;
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	// QUERY
	$license_promote = $wpdb->get_results( "SELECT * FROM $clink_sites_table WHERE site_IP = '$ip_address' AND verification_status = 'pass' AND mode = 'referent' ", ARRAY_A );
		
		
	$content_ids = array();
	
	
	foreach($license_promote as $single_license){
		
		$content_ids[] = $single_license['post_id'];
	}
	
	return $content_ids;
	
}

/**
 * CLink Get Warning by Content ID
 * 
 * @param array content ids
 */
function wpclink_verify_site_IP_by_post_id($content_id = 0){
	
	global $wpdb;
		
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	
	$ip_address = wpclink_get_client_ip();
	
	
		// QUERY
	$license_ids_filter = $wpdb->get_results( "SELECT * FROM $clink_sites_table WHERE  verification_status = 'pass' AND mode = 'referent' ", ARRAY_A );
	
	foreach($license_ids_filter as $single_license){
	
	$content_id_match = $single_license['post_id'];
	
		if($content_id_match == $content_id){
			
			if($single_license['site_IP'] == $ip_address){
				return false;
			}else{
				return true;
			}

		}	
		
	}
	
	
	return false;
	
}
/**
 * CLink Get Warning by Content ID
 * 
 * @param array content ids
 */
function wpclink_update_new_ip($license_id = 0){
	
		global $wpdb;
		
		// CLINK  TABLE
		$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	
	
		$new_ip = wpclink_get_license_meta($license_id,'new_site_ip',true);
	
			
		// IP
		$updated = $wpdb->update($clink_sites_table,
					  array('site_IP' => $new_ip),
					  array('license_id' => $license_id,'mode' => 'referent'));
			
		wpclink_update_license_meta($license_id,'site_ip_flag','no');				
		
	
}

/**
 * CLink Get Warning by Content ID
 * 
 * @param integer content ids
 * @param string site url
 */
function wpclink_verify_linked_site_ip($content_id = 0, $site_url = ''){
	
	global $wpdb;
		
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	
	$ip_address = wpclink_get_client_ip();
	
	// QUERY
	$license_promote = $wpdb->get_results( "SELECT * FROM $clink_sites_table WHERE  verification_status = 'pass' AND mode = 'referent' ", ARRAY_A );
	
	$content_ids = array();
	
	
	
	foreach($license_promote as $single_license){
		
		
		$content_id_match = $single_license['post_id'];
		
		if($content_id == $content_id_match){
		
		
		// Now Filter The Site Address
		
		// License Site
		$licensed_site = $single_license['site_url'];
		$license_site_url = wpclink_clean_url($licensed_site);
		
		// Site URL
		$site_url = wpclink_clean_url($site_url);
		
		if($license_site_url == $site_url){
			
		if($single_license['site_IP'] == $ip_address){
			
			
			$ip_warning = wpclink_get_license_meta($single_license['license_id'],'site_ip_flag',true);
			
			if($ip_warning == 'no'){
				// Match
				wpclink_update_license_meta($single_license['license_id'],'site_ip_flag','no');
			}else{
				// Match
				wpclink_add_license_meta($single_license['license_id'],'site_ip_flag','no');	
			}
				
			
				
			}else{
			
			$ip_warning = wpclink_get_license_meta($single_license['license_id'],'site_ip_flag',true);
			
			if($ip_warning == 'yes' || $ip_warning == 'no'){
				// Not Match IP
				wpclink_update_license_meta($single_license['license_id'],'site_ip_flag','yes');	
				
				if(wpclink_get_license_meta($single_license['license_id'],'new_site_ip') == false){
					
					wpclink_add_license_meta($single_license['license_id'],'new_site_ip',$ip_address);
				}else{
					wpclink_update_license_meta($single_license['license_id'],'new_site_ip',$ip_address);
				}

			}else{
				// Not Match IP
				wpclink_add_license_meta($single_license['license_id'],'site_ip_flag','yes');	
				wpclink_add_license_meta($single_license['license_id'],'new_site_ip',$ip_address);
			}
			
				
				
			}
			
			
			
		}
		
	}
		
	}
	

	
}

/**
 * CLink Update License Status 
 * 
 * @param integer content ids
 * @param string site url
 */
function wpclink_update_license_delivery_status($content_id = 0, $site_url = ''){
	
	global $wpdb;
		
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	
	
	// QUERY
	$license_promote = $wpdb->get_results( "SELECT * FROM $clink_sites_table WHERE  verification_status = 'pass' AND mode = 'referent' ", ARRAY_A );
	
	$content_ids = array();
	
	
	foreach($license_promote as $single_license){
		
		
		// Now Filter The Site Address
		$content_id_match = $single_license['post_id'];
		
		if($content_id == $content_id_match){
		
		// License Site
		$licensed_site = $single_license['site_url'];
		$license_site_url = wpclink_clean_url($licensed_site);
		
		// Site URL
		$site_url = wpclink_clean_url($site_url);
		
		if($license_site_url == $site_url){
			
			// Content Delivered
			wpclink_update_license_meta($single_license['license_id'],'delivery_status','content_delivered');
			
		}	
	}
		
	}
}



/**
 * CLink Get License ID by Content ID
 * 
 * @param integer $content_id  content id
 * 
 * @return integer license id
 */
function wpclink_get_linked_license_id_by_referent_post_id($content_id = 0, $site_url = NULL){
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'linked' AND site_url = '$site_url' ", ARRAY_A);
	
	foreach($license_data as $single_license){
		
		$content_id_match =  wpclink_get_license_meta($single_license['license_id'],'referent_post_id',true);
		
		if($content_id == $content_id_match){
			
			return $single_license['license_id'];
			
		}
		
	}
	
	
}
/**
 * CLink Get Content ID by License ID
 * 
 * @param integer $license_id  license id
 * 
 * @return integer content id
 */
function wpclink_get_post_id_by_license_id($license_id = 0){
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = $wpdb->get_row( "SELECT * FROM $table_license WHERE license_id = '$license_id' AND mode = 'referent' ", ARRAY_A);	
	
	return $license_data['post_id'];
}
/**
 * CLink Get Licence Type by License ID
 * 
 * @param integer $license_id  license type
 * 
 * @return mixed false | licenser type
 */
function wpclink_get_license_class_by_license_id($license_id = false){
	global $wpdb;
		
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$license_data = $wpdb->get_row( "SELECT * FROM $table_license WHERE license_id = '$license_id' ", ARRAY_A);
	
	
	if ( NULL !== $license_data ) {	
		return $license_data['license_class'];
	}else{
		return false;	
	}	
}
/**
 * CLink Creation Pre Authorize Effect Date
 * 
 * @param integer $post_id  post id
 * 
 */
function wpclink_add_date_effect_pre_auth($post_id = 0){
	
	$effect_date = current_time("Y-m-d H:i:s",true);
	
	// Update
	update_post_meta($post_id,'wpclink_reuse_pre_auth_effective_date',$effect_date);
}
/**
 * Add the License Aggregated Link
 * 
 * @param string $site_url url of the aggregated site 
 * @param string $site_ip ip of the aggregated site 
 * @param boolean $clink_status status of the clink link
 * @param boolean $clink_canonical_status status of canonical
 * @param string $secret_key secret key of the site to aggregate
 * @param string $auth_code authorization code of the site to aggregate
 * @param string $license_template license template of the aggregated site
 * @param string $license_class license type of the aggregated site content either it is UC-UT-UM or uc-at-um	
 * 
 * @return integer added the aggregated link
 */
function wpclink_add_license_linked($site_url = '', $site_ip, $clink_status = 'pass', $clink_canonical_status = 1,$secret_key = '',$auth_code = '',$license_template = '',$license_class = '',$content_id = '',$content_url = '',$token = '',$unique = ' ', $right_assignID = ''){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$clink_mode = 'linked';
	
	$date = current_time("Y-m-d H:i:s",true);
	
	$added_license = $wpdb->insert($table_license, 
	  array( 
		  'mode' => $clink_mode, 
		  'site_url' => $site_url,
		  'site_IP' => $site_ip,
		  'post_id' => '',
		  'verification_status' => $clink_status,
		  'license_class' => $license_class,
		  'license_version' => '0.9',
		  'license_date' => $date,
		  'rights_transaction_ID' => $right_assignID
	  ), 
	  array( 
		  '%s', 
		  '%s',
		  '%s',
		  '%s',
		  '%s',
		  '%s',
		  '%s',
		  '%s',
		  '%s'
		  
	  ));
	  
	  if($added_license > 0){
		  
		  $add_license_id = $wpdb->insert_id;
		  // License Meta
		  wpclink_add_license_meta($add_license_id,'license',$license_template);
		  wpclink_add_license_meta($add_license_id,'license_secret_key',$secret_key);
		  wpclink_add_license_meta($add_license_id,'license_auth_code',$auth_code);
		  wpclink_add_license_meta($add_license_id,'license_token',$token);
		  wpclink_add_license_meta($add_license_id,'license_download_id',$unique);
		  
		  // Referent Post ID
		  wpclink_add_license_meta($add_license_id,'referent_post_id',$content_id);
		  
	  }
	  
	  return $add_license_id;
		
}
/**
 * Delete Aggregated License
 * 
 * @param integer $license_id  license id
 * 
 * @return integer
 */
function wpclink_delete_license_linked($license_id = false){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$table_licensemeta = $wpdb->prefix . 'wpclink_licensemeta'; 
	
	// Query
	$deleted = $wpdb->delete( $table_license, array( 'license_id' => $license_id, 'mode' => 'linked') );
	
	// DELETE META
	$delete_id = $wpdb->delete( $table_licensemeta, array( 'license_id' => $license_id ) );	
	
	if($deleted > 0){
		
		// KEYS
		wpclink_delete_license_meta($license_id,'license_secret_key');
		wpclink_delete_license_meta($license_id,'license_auth_code');
		// TOKEN
		wpclink_delete_license_meta($license_id,'license_token');
		// Referent Post ID
		wpclink_delete_license_meta($license_id,'referent_post_id');
		
			
	}
	
	return $deleted;	
	
}
/**
 * Get the License Aggregated
 * 
 * @param integer $license_id license id
 * 
 * @return array
 */
function wpclink_get_license_linked($license_id = false){
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = $wpdb->get_row( "SELECT * FROM $table_license WHERE license_id = '$license_id' AND mode = 'linked' ", ARRAY_A);
	
	// License Meta
	$secret_key = wpclink_get_license_meta($license_id,'license_secret_key');
	$auth_code = wpclink_get_license_meta($license_id,'license_auth_code');
	$referent_post_id = wpclink_get_license_meta($license_id,'referent_post_id');
	
	if ( NULL !== $license_data ) {
		
		return array_merge($license_data, array('secret_key' => $secret_key, 'auth_code' => $auth_code, 'referent_post_id' => $referent_post_id) );
		
	}else{
		return false;	
	}
		
}

/**
 * Get License Aggregated by Download ID
 * 
 * @param string $download_id download id of license template
 * 
 * @return array
 */
function wpclink_get_license_linked_by_download_id($download_id = false){
	
	global $wpdb;
	
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$table_licensemeta = $wpdb->prefix . 'wpclink_licensemeta'; 
	
	$get_id = $wpdb->get_row( "SELECT * FROM $table_licensemeta WHERE meta_value = '$download_id' " );
	if ( NULL !== $get_id ) {
		$license_id = $get_id->license_id;
	}
	 
	$license_data = $wpdb->get_row( "SELECT * FROM $table_license WHERE license_id = '$license_id' AND mode = 'linked' ", ARRAY_A);
	
	// License Meta
	$secret_key = wpclink_get_license_meta($license_id,'license_secret_key');
	$auth_code = wpclink_get_license_meta($license_id,'license_auth_code');
	$referent_post_id = wpclink_get_license_meta($license_id,'referent_post_id');
	
	if ( NULL !== $license_data ) {
		
		return array_merge($license_data, array('secret_key' => $secret_key, 'auth_code' => $auth_code, 'referent_post_id' => $referent_post_id) );
		
	}else{
		return false;	
	}
		
}
/**
 * Get One From Aggregated License
 * 
 * @return array
 */
function wpclink_sort_site_url_referent(){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$array_licenses = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'linked'", ARRAY_A );
	
	
	
	$clink_site_url_list = array();
	$new_array = array();
	$array_count = 0;
	
	foreach($array_licenses as $single_array){
	
	if(in_array($single_array['site_url'],$clink_site_url_list)){
	
	
		// ASSIGN
		$clink_site_url_list[] = $single_array['site_url'];
		
		
	}else{
		//ASSIGN
		$clink_site_url_list[] = $single_array['site_url'];
		
		$new_array[$array_count]['license_id'] = $single_array['license_id']; 
		$new_array[$array_count]['mode'] = $single_array['mode']; 
		$new_array[$array_count]['site_IP'] = $single_array['site_IP']; 
		$new_array[$array_count]['site_url'] = $single_array['site_url'];
		$new_array[$array_count]['verification_status'] = $single_array['verification_status'];  
		$new_array[$array_count]['referent_post_id'] = wpclink_get_license_meta($single_array['license_id'],'referent_post_id',true);
		
	}
	
	$array_count++;
		
	}
	
	return $new_array;
}
/**
 * Get All Aggregated License
 * 
 * @return array license
 */
function wpclink_get_all_license_linked(){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$license_data = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'linked'", ARRAY_A );
	
	
	$return_data = array();
	
	foreach($license_data as $license_single){
		
		$content_id = wpclink_get_license_meta($license_single['license_id'],'referent_post_id',true);

			
			$return_data[] = array(
				'license_id' => $license_single['license_id'],
				'mode' => $license_single['mode'],
				'post_id' => $license_single['post_id'],
				'referent_post_id' => $content_id,
				'license_class' => $license_single['license_class'],
				'license_version' => $license_single['license_version'],
				'rights_transaction_ID' => $license_single['rights_transaction_ID'],
				'license_date' => $license_single['license_date'],
				'site_url' => $license_single['site_url'],
				'site_IP' => $license_single['site_IP'],
				'verification_status' => $license_single['verification_status']
			);
		
		
	}
	
	return $return_data;
	
	
}
/**
 * Get All Links of Content ID
 * 
 * @return array license
 */
function wpclink_get_all_liceses_by_post_id($content_id = 0){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$license_data = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'referent' ", ARRAY_A );
	
	
	
	$return_data = array();
	
	foreach($license_data as $license_single){
		
		$content_id_match = $license_single['post_id'];
		
		if($content_id == $content_id_match){
			
			$return_data[] = array(
				'license_id' => $license_single['license_id'],
				'mode' => $license_single['mode'],
				'post_id' => $content_id_match,
				'license_class' => $license_single['license_class'],
				'license_version' => $license_single['license_version'],
				'rights_transaction_ID' => $license_single['rights_transaction_ID'],
				'license_date' => $license_single['license_date'],
				'site_url' => $license_single['site_url'],
				'site_IP' => $license_single['site_IP'],
				'verification_status' => $license_single['verification_status']
			);
		}
		
	}
	
	return $return_data;
	
}
/**
 * Get All License Meta
 * 
 * @return array tokens
 */
function wpclink_get_all_license_metas(){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licensemeta';
	$data_array = $wpdb->get_results( "SELECT * FROM $table_license WHERE meta_key = 'license_token'", ARRAY_A );
	
	if($data_array == NULL) return false;
	foreach($data_array as $single_array){
		$tokens_array[] =  $single_array['meta_value'];
	}
	
	return $tokens_array;
}
/**
 * Get All License Meta by Key
 * 
 * @return array tokens
 */
function wpclink_get_all_license_metas_by_key($meta_key = NULL){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licensemeta';
	$data_array = $wpdb->get_results( "SELECT * FROM $table_license WHERE meta_key = '$meta_key'", ARRAY_A );
	
	if($data_array == NULL) return false;
	foreach($data_array as $single_array){
		$meta_key_val[] =  $single_array['meta_value'];
	}
	
	return $meta_key_val;
}

/**
 * Get All License Meta
 * 
 * @return array tokens
 */
function wpclink_get_ip_warnings(){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licensemeta';
	
	
	$all_promoted_licenses = wpclink_get_all_license_referent();
	
	foreach($all_promoted_licenses as $single_license){
		
		$license_id = $single_license['license_id'];
		$ip_warning = wpclink_get_license_meta($license_id,'site_ip_flag',true);
		
		if($ip_warning == 'yes'){
			return true;
		}
		
	}
	
	return false;
	
}
/**
 * Get All Aggregated ID's
 * 
 * 
 * @return array
 */
function wpclink_get_all_license_ids_linked(){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$license_ids = $wpdb->get_results( "SELECT license_id FROM $table_license WHERE mode = 'linked'", ARRAY_A );
	
	$collect_ids = array();
	
	foreach($license_ids as $single_id){
		$collect_ids[] = $single_id['license_id'];
	}
	
	return $collect_ids;
	
}
/**
 * Get All License Promoted
 * 
 * @return array data
 */
function wpclink_get_all_license_referent(){
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$license_data = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'referent' ", ARRAY_A );
	
	$return_data = array();
	
	foreach($license_data as $license_single){
		
		$content_id_match = $license_single['post_id'];
		
		
		
					
			$return_data[] = array(
				'license_id' => $license_single['license_id'],
				'mode' => $license_single['mode'],
				'post_id' => $content_id_match,
				'license_class' => $license_single['license_class'],
				'license_version' => $license_single['license_version'],
				'rights_transaction_ID' => $license_single['rights_transaction_ID'],
				'license_date' => $license_single['license_date'],
				'site_url' => $license_single['site_url'],
				'site_IP' => $license_single['site_IP'],
				'verification_status' => $license_single['verification_status']
			);
			
			
		
		
		
		
	}
		
	return $return_data;
	
	
}
/**
 * Get All License Promoted by Filter
 * 
 * @return array data
 */
function wpclink_get_options_menu_dropdown_filter_links_inbound($column = 'all'){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'referent' ", ARRAY_A );
		
	$return_data = array();
	
	foreach($license_data as $license_single){
		
		
		switch ($column){
			case 'site_url':
				$return_data[] = $license_single['site_url'];
				break;
			case 'license_status':
				$return_data[] = $license_single['verification_status'];
				break;
			case 'creation_status':
				$return_data[] = wpclink_get_license_meta($license_single['license_id'],'creation_status',true);
				break;
			case 'delivery_status':
				$return_data[] = wpclink_get_license_meta($license_single['license_id'],'delivery_status',true);
				break;
			default:
				$return_data[] = array(
				'license_id' => $license_single['license_id'],
				'mode' => $license_single['mode'],
				'post_id' => $license_single['post_id'],
				'license_class' => $license_single['license_class'],
				'license_version' => $license_single['license_version'],
				'rights_transaction_ID' => $license_single['rights_transaction_ID'],
				'license_date' => $license_single['license_date'],
				'site_url' => $license_single['site_url'],
				'site_IP' => $license_single['site_IP'],
				'verification_status' => $license_single['verification_status']
			);
			break;
				
		}

	}
	
	$return_data = array_unique($return_data);
	return $return_data;
	
}
/**
 * Get All License Promoted
 * 
 * @return array data
 */
function wpclink_get_all_license_referent_by_filter($post_id = ''){
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$post_id = esc_sql($post_id);
	
	if(!empty($post_id)){
		
		$license_data = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'referent'  ", ARRAY_A );
		
		
		$return_data = array();
	
	foreach($license_data as $license_single){
		
		$content_id_match = $license_single['post_id'];

		
		if($post_id == $content_id_match){
		
			
			$return_data[] = array(
				'license_id' => $license_single['license_id'],
				'mode' => $license_single['mode'],
				'post_id' => $content_id_match,
				'license_class' => $license_single['license_class'],
				'license_version' => $license_single['license_version'],
				'rights_transaction_ID' => $license_single['rights_transaction_ID'],
				'license_date' => $license_single['license_date'],
				'site_url' => $license_single['site_url'],
				'site_IP' => $license_single['site_IP'],
				'verification_status' => $license_single['verification_status']
			);
			
			
		}
		
		
	}
		
		return $return_data;
		
		
	}else{
	
	$license_data = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'referent' ", ARRAY_A );
		
		
	$return_data = array();
	
	foreach($license_data as $license_single){
		
		$content_id_match = $license_single['post_id'];
		
		
		
					
			$return_data[] = array(
				'license_id' => $license_single['license_id'],
				'mode' => $license_single['mode'],
				'post_id' => $content_id_match,
				'license_class' => $license_single['license_class'],
				'license_version' => $license_single['license_version'],
				'rights_transaction_ID' => $license_single['rights_transaction_ID'],
				'license_date' => $license_single['license_date'],
				'site_url' => $license_single['site_url'],
				'site_IP' => $license_single['site_IP'],
				'verification_status' => $license_single['verification_status']
			);
			
			
		
		
		
		
	}
		
		return $return_data;
	}
	
}
/**
 * Get All License Promoted By Filter
 * 
 * @return array data
 */
function wpclink_get_data_links_inbound_by_filters(){
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$site_url_sql = '';
	$license_status_sql = '';
	
	$site_url = (isset($_GET['site_url'])) ? $_GET['site_url'] : '';
	
	if(!empty($site_url)){
		if($site_url != 'all'){
			// Validate URL
			if(wp_http_validate_url(urldecode($site_url))){
				// Escape SQL
				$site_url = esc_sql(urldecode($site_url));
				$site_url_sql = "AND site_url='".$site_url."'";

			}
		}
	}
	
	$license_status = (isset($_GET['license_status'])) ? $_GET['license_status'] : '';

	if(!empty($license_status)){
		if(!empty($license_status != 'all')){
			// Escape SQL
			$license_status = esc_sql(urldecode($license_status));
			$license_status_sql = "AND verification_status='".$license_status."'";
		}
	}
	
	
	$license_data = $wpdb->get_results( 
		$wpdb->prepare("SELECT * FROM $table_license WHERE mode = '%s' {$site_url_sql} {$license_status_sql} ",'referent'),
		ARRAY_A );
		
		
	$return_data = array();
	
	foreach($license_data as $license_single){
		
		$creation_status = (isset($_GET['creation_status'])) ? $_GET['creation_status'] : '';
		
		if(!empty($creation_status)){
			if($creation_status == 'found' || $creation_status == 'notfound' || $creation_status == 'servererror'){

				$creation_status_match = wpclink_get_license_meta($license_single['license_id'],'creation_status',true);

				if($creation_status == $creation_status_match){

				}else{
					continue;
				}

			}
		}
		
		$delivery_status = (isset($_GET['delivery_status'])) ? $_GET['delivery_status'] : '';
				
		if(!empty($delivery_status)){
			if($delivery_status == 'delivered' || $delivery_status == 'content_delivered'){

				$delivery_status_match = wpclink_get_license_meta($license_single['license_id'],'delivery_status',true);

				if($delivery_status == $delivery_status_match){

				}else{
					continue;
				}

			}
		}
				
			$return_data[] = array(
				'license_id' => $license_single['license_id'],
				'mode' => $license_single['mode'],
				'post_id' => $license_single['post_id'],
				'license_class' => $license_single['license_class'],
				'license_version' => $license_single['license_version'],
				'rights_transaction_ID' => $license_single['rights_transaction_ID'],
				'license_date' => $license_single['license_date'],
				'site_url' => $license_single['site_url'],
				'site_IP' => $license_single['site_IP'],
				'verification_status' => $license_single['verification_status']
			);
					
	}
	return $return_data;
	
}
/**
 * Add License Meta
 * 
 * @param integer $post_id post id licensee
 * @param string $meta_key key licensee
 * @param string $meta_value value of licensee
 * @param boolean $unique unique to load
 * 
 * @return integer license id
 */
function wpclink_add_license_meta($post_id, $meta_key, $meta_value, $unique = false ){
	global $wpdb;
	// Table Prefix
	$table_licensemeta = $wpdb->prefix . 'wpclink_licensemeta'; 
	
	if(empty($post_id)) return false;
	
	
	if($unique == true){
		$found = $wpdb->get_row( "SELECT * FROM $table_licensemeta WHERE license_id = '$post_id' AND meta_key = '$meta_key' " );	
		if( NULL !== $found){
			return false;
		}
	}
	
	
	// Insert	
	 $return = $wpdb->insert($table_licensemeta, 
	  array( 
		  'license_id' => $post_id, 
		  'meta_key' => $meta_key,
		  'meta_value' => $meta_value 
	  ), 
	  array( 
		  '%d', 
		  '%s',
		  '%s' 
	  ));
	  
	  return $return;
}
/**
 * Update the Liecense Meta
 * 
 * @param integer $post_id license id
 * @param string $meta_key license key
 * @param string $meta_value license value
 * @param string $prev_value  previous value
 * 
 * @return integer license meta id
 */
function wpclink_update_license_meta($post_id, $meta_key, $meta_value, $prev_value = ''){
	global $wpdb;
	// Table Prefix
	$table_licensemeta = $wpdb->prefix . 'wpclink_licensemeta'; 
	if(empty($post_id)) return false;
	
	
	$updated_id = $wpdb->update( 
	$table_licensemeta, 
	array( 
		'meta_value' => $meta_value
	), 
	array( 'license_id' => $post_id, 'meta_key' => $meta_key ), 
	array( 
		'%s'
	));
	
	
	return $updated_id;
	
}
/**
 * Get the License Meta 
 * 
 * @param integer $post_id license id
 * @param string $key license key
 * @param boolean $single if return the single value
 * 
 * @return boolean
 */
function wpclink_get_license_meta($post_id, $key = '', $single = false ){
	global $wpdb;
	// Table Prefix
	$table_licensemeta = $wpdb->prefix . 'wpclink_licensemeta'; 
	
	if(empty($post_id)) return false;
	
	
	$get_id = $wpdb->get_row( "SELECT * FROM $table_licensemeta WHERE license_id = '$post_id' AND meta_key = '$key'" );
	
	if ( NULL !== $get_id ) {
		return $get_id->meta_value;	
	}else{
		return false;
	}
	
	
}
/**
 * Delete the License Meta
 * 
 * @param integer $post_id license id
 * @param string $meta_key license key
 * @param string $meta_value license value 
 * 
 * @return integer deleted the license 
 */
function wpclink_delete_license_meta($post_id, $meta_key, $meta_value = ''){
	global $wpdb;
	// Table Prefix
	
	$table_licensemeta = $wpdb->prefix . 'wpclink_licensemeta'; 
	if(empty($post_id) || empty($meta_key)) return false;
	
	// DELETE
	$delete_id = $wpdb->delete( $table_licensemeta, array( 'license_id' => $post_id, 'meta_key' => $meta_key ) );	
	
	return $delete_id;
	
}
/**
 * Add License Promoted
 * 
 * @param string $site_url url of promote license
 * @param string $site_ip ip of the promoted license
 * @param boolean $clink_status license activate status
 * @param string $clink_canonical_status canonical url verification status
 * @param string $linked_firstname first name of the linked user
 * @param string $linked_lastname last name of the linked user
 * @param string $linked_displayname display name of the linked user
 * @param string $content_id content id which is linked
 * @param string $content_url content url
 * @param string $license_class type of license
 * @param array $extra_link extra linked user infomation
 * @param string $token license token
 * 
 * @return integer
 */
function wpclink_add_license_referent($site_url = '',
									  $site_ip,
									  $clink_status = 'pass',
									  $clink_canonical_status = 1,
									  $linked_firstname = '',
									  $linked_lastname = '',
									  $linked_displayname = '',
									  $content_id = '',
									  $content_url = '',
									  $license_class = '',
									  $extra_link = array(),
									  $token = NULL,
									  $right_assignID = NULL){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$clink_mode = 'referent';
	
	$date = current_time("Y-m-d H:i:s", true);
	
	$added_license = $wpdb->insert($table_license, 
	  array( 
		  'mode' => $clink_mode, 
		  'site_url' => $site_url,
		  'site_IP' => $site_ip,
		  'verification_status' => $clink_status,
		  'license_version' => '0.9',
		  'post_id' => $content_id,
		  'license_class' => $license_class,
		  'license_date' => $date,
		  'rights_transaction_ID' => $right_assignID
	  ), 
	  array( 
		  '%s', 
		  '%s',
		  '%s',
		  '%s',
		  '%s',
		  '%s',
		  '%s',
		  '%s',
		  '%s'
	  ));
	  
	  $inserted_license_id = $wpdb->insert_id;
	
	  
	  
	   // Licensee Liked Variables
	  $licensee_data = array('licensee_first_name'=>$linked_firstname,
	  						 'licensee_last_name' => $linked_lastname,
							 'licensee_display_name' => $linked_displayname,
							 'licensee_email'=> $extra_link['link_email'],
							 'licensee_identifier'=>$extra_link['link_identifier']);
	  
	 wpclink_add_license_meta($inserted_license_id,'licensee_linked_variables',serialize($licensee_data));
	 
	 wpclink_add_license_meta($inserted_license_id,'license_offer_accept_date',$extra_link['licensee_accept_datetime']);
	 wpclink_add_license_meta($inserted_license_id,'delivery_status','delivered');
	
	 wpclink_add_license_meta($inserted_license_id,'creation_status','found');
	
	 wpclink_add_license_meta($inserted_license_id,'license_key',$token);
	  
	  
	  return $inserted_license_id;
		
}
/**
 * Delete Promoted License
 * 
 * @param integer $license_id license id
 * 
 * @return integer
 */
function wpclink_delete_license_referent($license_id = false){
	
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$table_licensemeta = $wpdb->prefix . 'wpclink_licensemeta'; 
	
	// Query
	$deleted = $wpdb->delete( $table_license, array( 'license_id' => $license_id, 'mode' => 'referent' ) );
	
	// DELETE META
	$delete_id = $wpdb->delete( $table_licensemeta, array( 'license_id' => $license_id ) );	
		
	return $deleted;	
}
/**
 * Get License Promoted by ID
 * 
 * @param integer $license_id license id
 * 
 * @return array
 */
function wpclink_get_license_referent($license_id = false){
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = $wpdb->get_row( "SELECT * FROM $table_license WHERE license_id = '$license_id' AND mode = 'referent' ", ARRAY_A);
	
	
	if ( NULL !== $license_data ) {
		
			
	return $license_data;

	}else{
		return false;	
	}
		
}

/**
 * CLink License Data by Content ID
 * 
 * @param integer $content_id Content ID
 * 
 * @return array
 */
 function wpclink_get_license_by_post_id($content_id = 0){
	 
	 // Global
	global $wpdb;
	 
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'referent' ", ARRAY_A);
 
	 
	$return_data = array();
	
	foreach($license_data as $license_single){
		
		$content_id_match = $license_single['post_id'];
		
		
		
		if($content_id == $content_id_match){
			
			$return_data = array(
				'license_id' => $license_single['license_id'],
				'mode' => $license_single['mode'],
				'post_id' => $content_id_match,
				'license_class' => $license_single['license_class'],
				'license_version' => $license_single['license_version'],
				'rights_transaction_ID' => $license_single['rights_transaction_ID'],
				'license_date' => $license_single['license_date'],
				'site_url' => $license_single['site_url'],
				'site_IP' => $license_single['site_IP'],
				'verification_status' => $license_single['verification_status']
			);
			
			return $return_data;
		}
		
	}
	
	
	
	
}
/**
 * CLink Show License Template 
 * 
 * To Show or Download License Template 
 * 
 */
function wpclink_show_site_license(){
	if(isset($_GET['license_show_id'])){
		
		// License ID
		$license_id = $_GET['license_show_id'];
		
		if(!is_user_logged_in()) die('Access Denide');
		if(empty($license_id) || $license_id < 1) die('License ID is Incorrect');
	
		wpclink_generate_pdf_template();
	die();
	}
}
// Register clink show license template function
add_action('wp_loaded','wpclink_show_site_license');
/**
 * CLink Generate PDF Template
 * 
 */
function wpclink_generate_pdf_template(){
	
	// Extension
	require_once( dirname (WPCLINK_MAIN_FILE) . '/vendor/dompdf/dompdf.php' );
}
/**
 * CLink Show My License Template
 * 
 */
function wpclink_display_license_template_PDF(){
	if(isset($_GET['license_my_show_id'])){
		
		// License ID
		$license_id = $_GET['license_my_show_id'];
		
		$download_id = wpclink_get_license_meta($license_id,'license_download_id',true);
		
		//if(!is_user_logged_in()) die('Access Denide');
		if($_GET['download_id'] != $download_id) return die('Download ID is Incorrect');
		if(empty($license_id) || $license_id < 1) die('License ID is Incorrect');
	
		wpclink_my_generate_pdf_template();
	die();
	}
}
// Register clink show my license template function
add_action('wp_loaded','wpclink_display_license_template_PDF');
/**
 * CLink Generate My PDF Template
 * 
 */
function wpclink_my_generate_pdf_template(){
	// Extension
	require_once(  dirname (WPCLINK_MAIN_FILE) . '/vendor/dompdf/dompdf-mylicense.php' );
}
/**
 * CLink License Data by Content ID
 * 
 * @param integer $content_id Content ID
 * 
 * @return array
 */
 function wpclink_get_license_by_linked_post_id($content_id = 0){
	 
	 
	 	 // Global
	global $wpdb;
	 
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = $wpdb->get_row( "SELECT * FROM $table_license WHERE mode = 'linked' AND post_id = $content_id ", ARRAY_A);
	 
	 
	if ( NULL !== $license_data ) {
		
	return $license_data;

	}else{
		return false;	
	}
 
	
	
}

/**
 * CLink License Data by Content ID
 * 
 * @param integer $content_id Content ID
 * 
 * @return array
 */
 function wpclink_get_license_by_referent_post_id($content_id = 0){
	 
	 
	 	 // Global
	global $wpdb;
	 
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'linked' ", ARRAY_A);
 
	 
	$return_data = array();
	
	foreach($license_data as $license_single){
		
		$content_id_match = wpclink_get_license_meta($license_single['license_id'],'referent_post_id',true);
		
		
		
		if($content_id == $content_id_match){
			
			$return_data = array(
				'license_id' => $license_single['license_id'],
				'mode' => $license_single['mode'],
				'referent_post_id' => $content_id_match,
				'license_class' => $license_single['license_class'],
				'license_version' => $license_single['license_version'],
				'rights_transaction_ID' => $license_single['rights_transaction_ID'],
				'license_date' => $license_single['license_date'],
				'site_url' => $license_single['site_url'],
				'site_IP' => $license_single['site_IP'],
				'verification_status' => $license_single['verification_status']
			);
			
			return $return_data;
		}
		
	}
	
	
}

/**
 * CLink License Data by Content ID and Site
 * 
 * @param integer $content_id Content ID
 * @param string $site_address Site Adress
 * 
 * @return array
 */
 function wpclink_get_license_by_referent_post_id_and_site_url($content_id = 0, $site_address = NULL){
	 
	 // Global
	global $wpdb;
	 
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
 
	 
	 $license_ids_filter = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'linked' AND site_url = '$site_address' ", ARRAY_A );
	 
	 	 	
	foreach($license_ids_filter as $single_license){
	
	$content_id_match = wpclink_get_license_meta($single_license['license_id'],'referent_post_id',true);
	
		if($content_id_match == $content_id){
			
			$license_id = $single_license['license_id'];
			
			$license_row = $wpdb->get_row( "SELECT * FROM $table_license WHERE license_id = '$license_id' ", ARRAY_A );
			
			$license_return = array_merge(array('referent_post_id' => $content_id),$license_row); 
			
			 return $license_return;
		
		}
 }
	
	return false;
	 
	 

}


/**
 * CLink Get License Data by Content ID and Site Referent
 * 
 * @param integer $content_id Content ID
 * 
 * @return array
 */
 function wpclink_get_license_by_linked_post_id_and_site_url($content_id = 0, $site_address = NULL){
	 // Global
	global $wpdb;
	 
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
 
	 
	 $license_ids_filter = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'referent' AND site_url = '$site_address' ", ARRAY_A );
	 
	 	 	
	foreach($license_ids_filter as $single_license){
	
	$content_id_match = $single_license['post_id'];
	
		if($content_id_match == $content_id){
			
			$license_id = $single_license['license_id'];
			
			 $license_row = $wpdb->get_row( "SELECT * FROM $table_license WHERE  license_id = '$license_id' ", ARRAY_A );
			
			 $license_return = array_merge(array('post_id' => $content_id),$license_row); 
			
			 return $license_return;
		
		}
 }
	
	return false;
	 
	 
}
/**
 * CLink Get License ID by Referent Post ID
 * 
 * @param integer $referent_post_id Referent Post ID
 * @param string $referent_site Referent Site
 * 
 * @return array
 */
function wpclink_get_license_id_by_referent_post_id($referent_post_id = 0,$referent_site = NULL){
			// query for all posts with the pictureID meta key set
$args = array(

    'post_type'  => array('post','page','attachment'), // or your_custom_post_type
    'meta_query' => array(
        array(
            'key'     => 'wpclink_referent_post_link',
            'compare' => 'EXISTS',
        ),
    ),
);
// All Post Status
$args['post_status'] = 'any';
	
// create a custom query
$my_query = new WP_Query( $args );

// loop over your query, creating a custom The Loop
if ( $my_query->have_posts() ): while ( $my_query->have_posts() ): $my_query->the_post();
    // $post is now posts that have a pictureId meta value
	$post_id = get_the_ID();
	
	
	$data = get_post_meta($post_id,'wpclink_referent_post_link',true);
	
	$get_ref_id = $data[$referent_site]['origin_id'];
	
	if($get_ref_id == $referent_post_id){
	
		return $post_id;
		
	}
	
	

	endwhile; endif;
	// reset $post
	wp_reset_postdata();

}

/**
 * CLink Get Post ID by License ID
 * 
 * @param integer $content_id Content ID
 * 
 * @return array
 */
 function wpclink_get_post_id_by_license_id_linked($license_id = 0){
	 
	 
	 	 // Global
	global $wpdb;
	 
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = $wpdb->get_row( "SELECT * FROM $table_license WHERE mode = 'linked' AND license_id = $license_id ", ARRAY_A);
	 
	 
	if ( NULL !== $license_data ) {
		
	return $license_data;

	}else{
		return false;	
	}
 
	
	
}