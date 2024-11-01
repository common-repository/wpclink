<?php
/**
 * CLink Creation Functions
 *
 * CLink creation post and pages functions
 *
 * @package Clink
 * @subpackage Content Manager
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Creation Post ID Save
 * 
 * @param integer $post_id 
 * @param integer $referent_post_id 
 * @param string $site_url 
 */
function wpclink_add_post_id_linked($post_id = 0, $referent_post_id = 0, $site_url = NULL){
	
	global $wpdb;
		
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	
	$license_data = wpclink_get_license_by_referent_post_id_and_site_url($referent_post_id,$site_url);
	
	// IP
	$updated = $wpdb->update($clink_sites_table,
					  array('post_id' => $post_id),
					  array('license_id' => $license_data['license_id']));
	
	
	
}
/**
 * CLink Request to Care.CLink.Meda for register the content on CLink.ID
 * 
 * @param integer $post_id 
 * @param object $post 
 * 
 */
function wpclink_register_creation( $post_id, $post) {
	
	// Post ID
	$post_id = $post_id->ID;
	if($content_register_restrict = get_post_meta($post_id,'wpclink_post_register_status', true)){
		if($content_register_restrict == '1'){
			return false;
		}
	}
	
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
	
	// GUID
	$guid = get_the_guid($post_id);
	
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));  
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];

	
	// Creation ID
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	// Get User and Creator and Party
	
	$reg_creators = wpclink_get_option('authorized_creators');
	$clink_party_id = wpclink_get_option('authorized_contact');
	// RULE #1
	// Creator OR REGISTERED + EDITOR OR ADMIN
	if(wpclink_user_has_creator_list($current_user_id) || 
	   (!empty($contentID) and (current_user_can('editor') || current_user_can('administrator'))) ){
		
	}else{
		return false;	
	}
	
	// RULE #2
	$territory = wpclink_get_option('territory_code');
	if(empty($territory)){
		return false;
	}
		
	// RULE #3 (Quota not for personal edition)
	if(wpclink_clink_domain_quota() > 0){
	}else{
		return false;
	}
	
	// RULE #4 Not Import Mode
	if(wpclink_import_mode()){
		return false;
	}
 
	// RULE #5 Author of Content OR EDIOR OR ADMN
	if(($current_user_id == $post_author_id) || current_user_can('editor') || current_user_can('administrator')){
	}else{
		return false;
	}
	// Get Post Content
	$post_content_check = get_post_field('post_content', $post_id);
	
	// RULE #6 Remove License if content contains the linked image
	if(wpclink_check_license_from_post_content($post_content_check,$post_id) > 0){
		
		$post_type = get_post_type($post_id);
	
		if($post_type == 'post'){
			
			if($referent_posts = wpclink_get_option('referent_posts')){
				// Remove from Database
			  $remove_referent_ids = array_diff($referent_posts,$post_id);
			  wpclink_update_option('referent_posts',array_unique($remove_referent_ids));
			  // Remove License Class
			  delete_post_meta( $post_id, 'wpclink_creation_license_class' );
			  // Remove from CLink.ID
			  wpclink_register_remove_referent($post_id);
			  // Remove License Programmatic
			  delete_post_meta( $post_id, 'wpclink_programmatic_right_categories');
			}
		}else if($post_type == 'page'){
			
			if($referent_pages = wpclink_get_option('referent_pages')){
				// Remove from Database
			  $remove_referent_ids = array_diff($referent_pages,$post_id);
			  wpclink_update_option('referent_pages',array_unique($remove_referent_ids));
			  // Remove License Class
			  delete_post_meta( $post_id, 'wpclink_creation_license_class' );
			  // Remove from CLink.ID
			  wpclink_register_remove_referent($post_id);
			  // Remove License Programmatic
			  delete_post_meta( $post_id, 'wpclink_programmatic_right_categories');
			}
		}
				
	}
	
	
	
	
	
	// API REQUEST
	$post_title = get_the_title($post_id);
	
	// Manually GUID
	if(get_post_type($post_id) == 'page'){
		$guid = get_bloginfo('url').'/?page_id='.$post_id;
	}else{
		$guid = get_bloginfo('url').'/?p='.$post_id;
	}
	
	// Get Permalink
	$permalink = get_permalink($post_id);
	
	
	
	
	
	$creator_id = get_user_meta($author_id,'wpclink_party_ID',true);
	$post_excerpts = get_the_excerpt($post_id);
	$post_type = get_post_type($post_id);
	$post_character = 'language';
	$clink_party_id = get_user_meta($clink_party_id,'wpclink_party_ID',true);
	
	// Customer ID
	$party_access_key = get_user_meta($author_id,'wpclink_party_access_key',true);
	
	
	// Taxonomy Permission
	if($taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true)){
	}else{
		$taxonomy_permission = '';
	}
	
	
	$content = apply_filters('the_content', get_post_field('post_content', $post_id));
	$content_without_html = strip_tags($content);
	$content_word_count = str_word_count($content_without_html);
	
	// Right Holder ID
	$right_holder_user_id = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	$right_holder_creation_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	$creator_display_name =  get_userdata($author_id);
	
		/* ====== Creation Right Holder ========= */
	if($creation_right_holder_user_id = get_post_meta($post_id,'wpclink_rights_holder_user_id',true)){
	}else{
		$creation_right_holder_user_id = $right_holder_id;
	}
	
	$creation_right_holder_data = get_userdata($creation_right_holder_user_id);
	$creation_right_holder_display_name = $creation_right_holder_data->display_name;
	$creation_right_holder_identifier = get_user_meta($creation_right_holder_user_id,'wpclink_party_ID',true);
	
	// Changed
	$creation_right_holder_identifier = get_user_meta($author_id,'wpclink_party_ID',true);
	
		/* ====== Creator Display Name ========== */
	$creator_user_id =  get_userdata($author_id);
	$creator_user_data = get_userdata($author_id);
	$creator_user_display = $creator_user_data->display_name;
	$right_holder_user_info = get_userdata($right_holder_id);
	if($wpclink_creation_access_key = get_post_meta($post_id,'wpclink_creation_access_key',true)){
		$wpclink_creation_access_key = $wpclink_creation_access_key;
	}
	
	
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
	
	// For AddToTaxonomy
	if($referent_cat = get_post_meta($post_id,'wpclink_referent_categories',true)){
		if($taxonomy_permission == 'AddToTaxonomy'){
			$post_categories = array_unique(array_merge($referent_cat,$post_categories));
		}
	}
	
	$cats = array();     
	foreach($post_categories as $c){
		$cat = get_category( $c );
		$cats[] = $cat->name;
	}
		
	$post_tags = wp_get_post_tags( $post_id );
	$tags = array();
	foreach($post_tags as $t){
		$tags[] = $t->name;
	}
	$linked_tags = (isset($_POST['cl_linked_tags'])) ?  (array)$_POST['cl_linked_tags'] : '';
	
	/*____URL____*/	
	$url_content = WPCLINK_CREATION_API;
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	// Reuse
	if($license_class = get_post_meta($post_id,'wpclink_creation_license_class',true)){
		$reuse_GUID = $guid;
	}else{
		$reuse_GUID = '';
	}
	
	if($clink_referent_creation_identifier = get_post_meta($post_id,'wpclink_referent_creation_ID',true)){
	}else{
		$clink_referent_creation_identifier = '';
	}
	
	if($referent_creator_party_ID = get_post_meta($post_id,'wpclink_referent_creator_party_ID',true)){
	}else{
		$referent_creator_party_ID = '';
	}
	
	
	// Get Last Archive if has
	$archive_link = wpclink_get_last_archive($post_id);
	
	
	if(!empty($contentID)){
		
	// CL Version
	$cl_version = get_post_meta($post_id,'wpclink_post_version_execute',true);
		
	// Record Archive Version
	$last_version = get_post_meta($post_id,'wpclink_post_record_last_version',true);
		
	// Clean Up
	delete_post_meta($post_id, 'wpclink_post_version_execute');
	delete_post_meta($post_id, 'wpclink_post_record_last_version');
	
	// If Version
	if(isset($cl_version) and $cl_version == 1){
	
		$sending_version = array(
                    'body' => array(
						'CLinkContentID' => $contentID,
                        'post_title'  => html_entity_decode($post_title),
                        'guid'	=> $guid,
						'creation_uri' => $permalink,
						'reuseGUID' => $reuse_GUID,
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
						// Referent Creator ID
						'referent_clink_creatorID' => $referent_creator_party_ID,
						'clink_party_link_role' => $clink_party_link_role,
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'creation_access_key' => $wpclink_creation_access_key,
						'clink_language' => $clink_language,
						'clink_taxonomy_permission' => $taxonomy_permission,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'clink_edition' => 'personal',
						'site_address'  => get_bloginfo('url'),
						'action'=> 'version'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
	
		
		if($creation_license_class = get_post_meta( $post_id, 'wpclink_creation_license_class', true )){
			// Rescricted License Only				
			if($creation_license_class == 'uc-at-um' || $creation_license_class == 'uc-ut-um'){	
				$sending['body']['clink_license_version'] = '0.9';
				$sending['body']['clink_license_class'] = wpclink_get_license_class_label($creation_license_class);
			}
		}
		wpclink_debug_log('SENDING VERSION'.print_r($sending_version,true));
		$response = wp_remote_post(
                $url_content,
				apply_filters( 'wpclink_register_version', $sending_version, $post_id)
            );
			

            if ( is_wp_error( $response ) ) {
               $resposne_Array = is_wp_error( $response );
				
				$wp_error = is_wp_error( $response );
				
			   // Response Debug
				wpclink_debug_log('PUBLISH CONTENT VERSION WP ERROR: '.print_r($response,true));
				
							
			if($wp_error == 1){
				$response_check = wpclink_return_wp_error($response);			
				update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
				return false;
			}
 
            }else {
				
				// Response Debug
				wpclink_debug_log('PUBLISH CONTENT VERSION: '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
				
				
				$return_response = wpclink_return_api_reponse($response);
				if($return_response === true){
					/* Check response has is_error */
					$response_check = wpclink_response_check($resposne_Array);
					if($response_check == false){
					}else{
						// Error
						update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
						update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
						return false;
					}
				}else{	
						// Error
						update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
						update_post_meta( $post_id, 'wpclink_loader_error_data', $return_response );
						return false;
				}
  
  
	if($resposne_Array['status'] == 'version'){
		
		// RESPONSE
		if(!empty($resposne_Array['data']['id'])){
			
			$version_id = $resposne_Array['data']['id'];
			$version_ready = array(
				'status' => 'ready',
				'version_identifier' => $version_id,
				'version_identifier_encrypt' => $resposne_Array['data']['encrypt_creation_id']
			);
			
			
			update_post_meta($post_id,'wpclink_version_ready',$version_ready);
			
			if($last_version == 1){
				update_post_meta($post_id,'wpclink_version_last_archive',$version_id);
			}
			
			
			// Version
			if($prev_data = get_post_meta($post_id,'wpclink_versions',true)){	
				$version_id = array($resposne_Array['data']['id']);
				$final_array = array_merge($prev_data,$version_id);
				update_post_meta($post_id,'wpclink_versions',$final_array);
			}else{
				$version_id = array($resposne_Array['data']['id']);
				update_post_meta($post_id,'wpclink_versions',$version_id);
			}
			
			// Version Time
			if($version_modified_time = get_post_meta($post_id,'wpclink_versions_time',true)){
				$version_modified_time[$resposne_Array['data']['id' ]] = $get_post_modified_date;
				update_post_meta($post_id,'wpclink_versions_time',$version_modified_time);
	
			}else{
				$version_modified_time = array();
				$version_modified_time[$resposne_Array['data']['id']] = $get_post_modified_date;
				update_post_meta($post_id,'wpclink_versions_time',$version_modified_time);
			}
			
					
			
			// Delete Trail
			wpclink_delete_option('promotion_trial');
		}
	}
}
	
}else{
	
	
	if($clink_referent_creation_identifier = get_post_meta($post_id,'wpclink_referent_creation_ID',true)){	
	}else{
		$clink_referent_creation_identifier = '';
	}
	
	if($referent_creator_party_ID = get_post_meta($post_id,'wpclink_referent_creator_party_ID',true)){
	}else{
		$referent_creator_party_ID = '';
	}
	
	if($license_class = get_post_meta($post_id,'wpclink_creation_license_class',true)){
		$reuse_GUID = $guid;
	}else{
		$reuse_GUID = '';
	}
	
	// RULE # 7 if not author of content and CREATOR OR ADMIN
	if($author_id != $current_user_id){
		if(current_user_can('editor') || current_user_can('administrator')){
			// Go
		}else{
			return false;
		}
	}
	
	$post_guid = get_the_guid($post_id);
	$archive_link = wpclink_get_last_archive($post_id);
	
	$sending = array(
                    'body' => array(
						'CLinkContentID' => $contentID,
                        'post_title'  => html_entity_decode($post_title),
                        'guid'	=> $guid,
						'creation_uri' => $permalink,
						'reuseGUID' => $reuse_GUID,
                        'post_excerpts' => html_entity_decode($post_excerpts),
						'post_type' => strtolower($post_type),
						'clink_word_count' => $content_word_count,
						'post_charcter' => $post_character,
						'clink_partyID' => $clink_party_id,
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
						// Referent Creation
						'clink_referent_creation_identifier' => $clink_referent_creation_identifier,
						// Referent Creator ID
						'referent_clink_creatorID' => $referent_creator_party_ID,
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
						'clink_party_link_role' => $clink_party_link_role,
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'clink_language' => $clink_language,
						'clink_taxonomy_permission' => $taxonomy_permission,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'creation_access_key' => $wpclink_creation_access_key,
						'site_address'  => get_bloginfo('url'),
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
		$sending['body']['referent_clink_creatorID'] = $referent_creator_party_ID;
		$sending['body']['referent_creation_rights_holder_display_name'] = $referent_rights_holder_display_name;
		$sending['body']['referent_creation_rights_holder_party_ID'] = $clink_referent_creation_identifier;
		if($referent_creator_display_name = get_post_meta($post_id,'wpclink_referent_creator_display_name',true)){
				$sending['body']['referent_creator_display_name'] = $referent_creator_display_name;
		}
		$sending['body']['reuseGUID'] = $referent_uri;
	}
	
	
	wpclink_debug_log('UPDATE GUL '.print_r($sending,true));
	
/* ========= UPDATE ============ */
	$response = wp_remote_post(
                $url_content,
				apply_filters( 'wpclink_register_update', $sending, $post_id)
                
            );
						
			
 
            if ( is_wp_error( $response ) ) {
               $wp_error = is_wp_error( $response );
				
			   // Response Debug
				wpclink_debug_log('UPDATE CONTENT WP ERROR: '.print_r($response,true));
				
							
			if($wp_error == 1){
				$response_check = wpclink_return_wp_error($response);			
				update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
				return false;
			}
			  
			   
            }else {
				
				// Response Debug
				wpclink_debug_log('UPDATE CONTENT: '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   
			 				
		$return_response = wpclink_return_api_reponse($response);
		if($return_response === true){
			
			/* Check response has is_error */
			$response_check = wpclink_response_check($resposne_Array);
			
			if($response_check == false){
				
				
				
			}else{
				// Error
				update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
				return false;
			}
			
		}else{	
				// Error
				update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $post_id, 'wpclink_loader_error_data', $return_response );
				return false;
		}
  
			   
	// Success
	update_post_meta( $post_id, 'wpclink_loader_status', 'complete' ); 			   
			 
  
  
	if($resposne_Array['status'] == 'update'){
	
	// Response
	update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
	update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
	update_post_meta($post_id,'wpclink_rights_holder_user_id',$author_id);
		
		
	if(!empty($resposne_Array['data']['creation_access_key'])){
		// Encrypt
		update_post_meta($post_id,'wpclink_creation_access_key',$resposne_Array['data']['creation_access_key']);
	}
		
	// Delete Trail
	wpclink_delete_option('promotion_trial');
		
	}
}
}
	}else{
		
		
	// RULE  #7 PUBLISH + NOT AUTHOR
	if ( get_post_status ( $post_id ) == 'publish' and $author_id != $current_user_id  ) {
		return false;	
	}
	
		
/* ================= CREATE =================== */
		
	$sending_create =  array(
                    'body' => array(
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
						'party_access_key' => $party_access_key,
						'clink_language' => $clink_language,
						'clink_taxonomy_permission' => $taxonomy_permission,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'site_address'  => get_bloginfo('url'),
						'action'=> 'create'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
	$response = wp_remote_post(
                $url_content,
                apply_filters( 'wpclink_register_create',$sending_create, $post_id)
            );
		
		
            if ( is_wp_error( $response ) ) {
 
               $wp_error = is_wp_error( $response );
			   
			   // Response Debug
				wpclink_debug_log('PUBLISH CONTENT WP ERROR: '.print_r($response,true));
				
					
			if($wp_error == 1){
				$response_check = wpclink_return_wp_error($response);			
				update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
				return false;
			}
			  
 
            
			}else {
				// Response Debug
				wpclink_debug_log('PUBLISH CONTENT: '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
				
				
		$return_response = wpclink_return_api_reponse($response);
		if($return_response === true){
			
			/* Check response has is_error */
			$response_check = wpclink_response_check($resposne_Array);
			
			if($response_check == false){
				
				
				
			}else{
				// Error
				update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $post_id, 'wpclink_loader_error_data', $response_check );
				return false;
			}
			
		}else{	
				// Error
				update_post_meta( $post_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $post_id, 'wpclink_loader_error_data', $return_response );
				return false;
		}
  
			   
	// Success
	update_post_meta( $post_id, 'wpclink_loader_status', 'complete' ); 			   
			 
	if($resposne_Array['status'] == 'create'){
		
	// Response
	update_post_meta($post_id,'wpclink_creation_ID',$resposne_Array['data']['clink_contentID']);
	update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
	update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
		
	// Encrypted
	if(!empty($resposne_Array['data']['creation_access_key'])){
		// Encrypt
		update_post_meta($post_id,'wpclink_creation_access_key',$resposne_Array['data']['creation_access_key']);
	}
		
	// Right ID
	if(!empty($resposne_Array['data']['clink_rightID'])){
		// Encrypt
		update_post_meta($post_id,'wpclink_right_ID',$resposne_Array['data']['clink_rightID']);
	}
		
		
	// Right Create Time
	if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_creation_date' ] ) ) {
		$right_time =  explode('.',$resposne_Array[ 'data' ][ 'clink_right_creation_date' ]);
		// Update Rights Create Time ID
		update_post_meta( $post_id, 'wpclink_right_created_time', $right_time[0].'Z'  );
	}
		
		
		
	
	
		
	// Right Holder ID Should same as Author ID
	$right_holder = wpclink_get_option('rights_holder');
	$right_holder_id = $author_id;
	
	
	$right_holder_user_id = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	
	update_post_meta($post_id,'wpclink_rights_holder_party_ID',$right_holder_user_id);
	update_post_meta($post_id,'wpclink_rights_holder_user_id',$author_id);
		
	// Delete Trail
	wpclink_delete_option('promotion_trial');
	
	}else if($resposne_Array['status'] == 'promotion_disallow'){
		
		// Promotion Message
		wpclink_update_option('promotion_trial','1'); 
		
		if($referent_uri = get_post_meta($post_id,'wpclink_referent_post_uri',true)){
			wpclink_update_option('last_linked_creation_date',$resposne_Array['data']['last_registration']); 
		}else{
			wpclink_update_option('last_registered_creation_date',$resposne_Array['data']['last_registration']); 
		}
	
	}
	
}
}
}
/**
 * CLink Create Right Assignment
 * 
 * @param array $data right assignment data
 * 
 */
function wpclink_create_right_transaction($data = array()){
	
	if(empty($data)) return false;
	
	$url_right_transaction = WPCLINK_RIGHT_TRANSACTION_API;
			
	$response = wp_remote_post(
                $url_right_transaction,
                array(
                    'body' => $data,'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
	
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );			   
			   // Response Debug
				wpclink_debug_log('RIGHT TRANSACTION ERROR'.print_r($resposne_Array,true));
				
			}else {
				// Response Debug
				wpclink_debug_log('RIGHT TRANSACTION '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
				
				if($resposne_Array['status'] == 'create'){
					
					// Right
					return $resposne_Array['data']['clinkRightsTransactionID'];
					//clinkRightsTransactionID
					
				}
			}
}
/**
 * CLink Publish Content
 * 
 * @param integer $ID post id
 * @param object $post post data
 * 
 */
function wpclink_publish_creation( $ID, $post ) {
    // Register request on publish post trigger
	add_action( 'rest_after_insert_post', 'wpclink_register_creation', 10, 3 );
	// Register request on publish page trigger
	add_action( 'rest_after_insert_page', 'wpclink_register_creation', 10, 3 );
	
}
// Register request on publish page trigger
add_action('publish_post', 'wpclink_publish_creation', 10, 2);
// Register request on publish page trigger
add_action('publish_page', 'wpclink_publish_creation', 10, 2);
/**
 * CLink Future Requests to Care.CLink.Meda for register the content on CLink.ID
 * 
 * @param integer $post_id 
 * 
 */
function wpclink_register_scheduled_creation($post_id) {
	
	
	// RULE #1
	$territory = wpclink_get_option('territory_code');
	if(empty($territory)){
		// Error Message
		
		return false;
		
	}
	
	// RULE #2
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
	$author_id = get_post_field ('post_author', $post_id);
	$creator_id = get_user_meta($author_id,'wpclink_party_ID',true);
	$post_excerpts = get_the_excerpt($post_id);
	$post_type = get_post_type($post_id);
	$post_character = 'language';
	$clink_party_id = get_user_meta($clink_party_id,'wpclink_party_ID',true);
	
	// Customer ID
	$party_access_key = get_user_meta($author_id,'wpclink_party_access_key',true);
	
	
	$right_holder = wpclink_get_option('rights_holder');
	
	$right_holder_id = $author_id;
		
	
	
	$content = apply_filters('the_content', get_post_field('post_content', $post_id));
	$content_without_html = strip_tags($content);
	$content_word_count = str_word_count($content_without_html);
	
	$right_holder_user_id = $right_holder_id;
	$right_holder_user_id = get_user_meta($right_holder_user_id,'wpclink_party_ID',true);
	
	
	// If content is linked and has referent copyright Owner
	if($referent_copyright_id = get_post_meta($post_id,'wpclink_referent_rights_holder_party_ID',true)){
		
		$right_holder_user_id = $referent_copyright_id;
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
	$creation_date = $time;
	
$get_post_date  = get_the_date('Y-m-d',$post_id);
$get_post_date .= 'T';
$get_post_date .= get_the_date('G:i:s',$post_id);
$get_post_date .= 'Z';
			
$get_post_modified_date  = get_the_modified_time('Y-m-d',$post_id);
$get_post_modified_date .= 'T';
$get_post_modified_date .= get_the_modified_time('G:i:s',$post_id);
$get_post_modified_date .= 'Z';
	
	
	$modified_date = get_the_modified_time('U',$post_id);
	
	$domain_access_key = wpclink_get_option('domain_access_key');
	
	
	$post_categories = wp_get_post_categories($post_id);
	
	
	$cats = array();
     
	foreach($post_categories as $c){
		$cat = get_category( $c );
		$cats[] = $cat->name;
	}
	
	
	
	$post_tags = wp_get_post_tags( $post_id );
	
	$tags = array();
	
	foreach($post_tags as $t){
		$tags[] = $t->name;
	}
	
	/*____URL____*/
	
	$url_content = WPCLINK_CREATION_API;
	
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	// If referent URI
	if($referent_uri = get_post_meta($post_id,'wpclink_referent_post_uri',true)){
		$permalink = $referent_uri;
	}
	
	if(!empty($contentID)){
/* ========= UPDATE ============ */
	$response = wp_remote_post(
                $url_content,
                array(
                    'body' => array(
						'CLinkContentID' => $contentID,
                        'post_title'  => $post_title,
                        'guid'	=> $guid,
						'creation_uri' => $permalink,
                        'post_excerpts' => $post_excerpts,
						'post_type' => strtolower($post_type),
						'clink_word_count' => $content_word_count,
						'post_charcter' => $post_character,
						'clink_partyID' => $clink_party_id,
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
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
						'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
						'clink_party_link_role' => $clink_party_link_role,
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'clink_language' => $clink_language,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'clink_edition' => 'personal',						
						'action'=> 'update'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
						
			
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
			   
			   // Response Debug
				wpclink_debug_log('PUBLISH CONTENT FUTURE '.print_r($response,true));
			  
 
            }else {
				
				// Response Debug
				wpclink_debug_log('PUBLISH CONTENT FUTURE '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
  
  
	if($resposne_Array['status'] == 'update'){
		// RESPONSE
		
	update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
	update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
	
		
	}
}
	}else{
/* ========= CREATE ============ */
	$response = wp_remote_post(
                $url_content,
                array(
                    'body' => array(
                        'post_title'  => $post_title,
                        'guid'	=> $guid,
						'creation_uri' => $permalink,
                        'post_excerpts' => $post_excerpts,
						'post_type' => strtolower($post_type),
						'clink_word_count' => $content_word_count,
						'post_charcter' => $post_character,
						'clink_partyID' => $clink_party_id,
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
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
						'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
						'clink_party_link_role' => $clink_party_link_role,
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'party_access_key' => $party_access_key,
						'clink_language' => $clink_language,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'site_address'  => get_bloginfo('url'),
						'action'=> 'create'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
						
			
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
			   
				// Response Debug
				wpclink_debug_log('PUBLISH CONTENT FUTURE'.print_r($response,true)); 
 
            }else {
				
				// Response Debug
				wpclink_debug_log('PUBLISH CONTENT FUTURE'.print_r($response,true)); 
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
  
			   
			   
			  
	if($resposne_Array['status'] == 'create'){
		// RESPONSE
		
	update_post_meta($post_id,'wpclink_creation_ID',$resposne_Array['data']['clink_contentID']);
	update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
	update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
		
		
		// Now Send to Referent Site
		$send_referent = wpclink_send_linked_creation_ID($post_id_old,$resposne_Array['data']['clink_contentID'],$post_id);
		
		// Save Post ID
		wpclink_add_post_id_linked($post_id,$post_id_old,$link_site_url);
		
		// Response Debug
		wpclink_debug_log('SEND REFERENT POST '.print_r($send_referent ,true));
	
		
	}else if($resposne_Array['status'] == 'promotion_disallow'){
		
		if(!empty($post_id)){
			
			
			
		}
		
		// Adding 24 Hours
		$after_24h = strtotime("+24 hours", strtotime($resposne_Array['data']['last_registration']));
		$after_24h_format =  date('Y-m-d h:i:s', $after_24h);
		
		// Notification
		wpclink_notif_print('You have registered a Creation at <strong>'.$resposne_Array['data']['last_registration'].' (UTC)</strong> Personal Edition allows one registration per 24h.  Please try after <strong>'.$after_24h_format.' (UTC)</strong>','error');
		return false;
	
		
	}
	
	
}
	}
}
// Register future request to us-customers.clink.id function
add_action( 'publish_future_post', 'wpclink_register_scheduled_creation' );
/**
 * Clink Create Linked Multi Select Page
 * 
 * @return interger page id
 */
function wpclink_register_linked_creations_bulk($creation_type = 'post'){
	
	if($creation_type == 'post'){
			
$cl_options = wpclink_get_option( 'preferences_general' );
		
if(isset($cl_options['sync_site'])){
	$get_sync_site = $cl_options['sync_site'];
}else{
	$get_sync_site = '';
}
		
// Site URL
$link_site_url = apply_filters('cl_site_link',$get_sync_site);
// Validate
if(empty($_POST['post']) or !isset($_POST['action']) or !isset($_POST['action2'])) return false;
if(($_POST['action'] == 'sync' and $_POST['action2'] == '-1') or ($_POST['action'] == '-1' and $_POST['action2'] == 'sync')){
}else{
	return false;
}
if(wpclink_is_acl_r_page()){
	return false;
}
/* ====== PREPARING REQUEST ======= */
$request_query = array();
$all_posts = $_POST['post'];
$comma_posts = implode(",",$all_posts);
$request_query['post__in'] = $comma_posts;
$request_query['post_type'] = 'c-post';
$request_query['nopaging'] = true;
$request_query['get_type'] = 'content';
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
$origin_data = array();
$build_query = build_query( $request_query );
/* ====== QUERY REQUEST ======= */
$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
$xml = simplexml_load_string($xml);
$result_update = false;
foreach($xml->channel->item as $single){ 
/* ===== PREPARING DATA ===== */
// Post ID
$post_id_old = (int)$single->post_id; 
// Post Title
$post_title = (string)$single->title;
// Post link
$post_link = (string)$single->link;
// Post GUID
$reuse_guid_link = (string)$single->guid;
// Language
$language = (string)$single->language;
// NEW
$referent_creator_party_ID = (string)$single->referent_creator_party_ID;
$referent_creator_display_name = (string)$single->referent_creator_display_name;
$referent_rights_holder_party_ID = (string)$single->referent_rights_holder_party_ID;
$referent_rights_holder_display_name = (string)$single->referent_rights_holder_display_name;
// ContentID
$post_contentID = (string)$single->content_id;
// License Class
$post_license_class = (string)$single->post_license_class;
// Post Taxonomy Permission
$post_taxonomy_permission = (string)$single->post_taxonomy_permission;	
// Post Creator ID
$post_creator_clinkid = (string)$single->post_creator_clink_id;
// Post Creator
$post_creator = (string)$single->post_creator;
// Post Copyright Owner
$post_rights_holder = (string)$single->post_rights_holder;
// Post Author
$ns_dc = $single->children('http://purl.org/dc/elements/1.1/');
$post_author = $ns_dc->creator_id;
// Post Content 
$post_content = (string)$single->children("content", true);
// Post Excerpt 
$post_excerpt = (string)$single->children("excerpt", true);
// Modified Time
$modified_time = (string)$single->modified_time;
// Image URL		  
if($single->children('wp', true)->post_thumbnail){
	$image_url = $single->children('wp', true)->post_thumbnail->children()->post_thumbnail_url;
}else{
	$image_url = NULL;
}
// Post Copyright Owner
$post_rights_holder = (string)$single->post_rights_holder;
/* ===== GET CATEGORY NAME ==== */
$assign_cat = array();
foreach($single->category as $single_cat){
$category_arr = $single_cat->attributes();
if($category_arr["domain"] == 'category'){
	$assign_cat[] = (string)$single_cat;
}
}
/* ====== INSERT POST ======= */
// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;
	
	// Setup the author, slug, and title for the post
	
	$selected_author = wpclink_get_option('post_author_assign');
	if(empty($selected_author) or $selected_author == false){
		$author_id = get_current_user_id();
	}else{
		$author_id = $selected_author;
	}
	
	$time = current_time( 'Y-m-d', true );
	$origin_data = array();
	$found_id = array();
		
	
	$selected_cat = wpclink_get_option('post_cat_assign');
	if((empty($selected_cat)) or ($selected_cat == false) or ($selected_cat == '-1')){
		
	foreach($assign_cat as $cat_single){
	
		$term = term_exists($cat_single, 'category');
		if ($term !== 0 && $term !== null) {
			$found_id[]= $term['term_id'];
		}else{
			$found_id[] = wp_create_category( $cat_single, '0' );
		}
	
	}
		
	}else{
		$found_id = array($selected_cat);
	}
	
	// If the page doesn't already exist, then create it
	
		// Set the post ID so that we know the post was created successfully
		if($post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_title'		=>	$post_title,
				'post_status'		=>	'publish',
				'post_content'  => $post_content,
				'post_excerpt' => $post_excerpt,
				'post_type'		=>	'post',
				'post_category' => $found_id
				
			),true
		)){
		
		  // Origin
		  $time = current_time( 'Y-m-d', true );
		  $origin_data[$link_site_url] = array('origin_id' => $post_id_old, 'last_sync' => $time, 'sync_status' => true,'canonical' => $post_link);
		  
		  
		  
		  // Origin Url_shorten(
		  update_post_meta($post_id, 'wpclink_referent_post_uri', $post_link);
		  update_post_meta($post_id, 'wpclink_link_flag', 1);
		  
		  
			
			
			// NEW
			update_post_meta($post_id, 'wpclink_referent_creator_party_ID', $referent_creator_party_ID);
			update_post_meta($post_id, 'wpclink_referent_creator_display_name', $referent_creator_display_name);
			update_post_meta($post_id, 'wpclink_referent_rights_holder_display_name', $referent_rights_holder_display_name);	
		  
		  // License Class
		  update_post_meta($post_id, 'wpclink_creation_license_class', $post_license_class);
		  update_post_meta($post_id, 'wpclink_programmatic_right_categories', $post_taxonomy_permission);
		  
		  
		  if($post_license_class == 'uc-at-um'){
			 // Referent Copyright Owner
		 	 update_post_meta($post_id, 'wpclink_referent_rights_holder_party_ID', $referent_rights_holder_party_ID); 
		  }
		  
		  // Update post
		  if($updated_post_meta = update_post_meta($post_id, 'wpclink_referent_post_link', $origin_data)){	  
		  $result_update = $updated_post_meta;
		  $updated_post_id = $updated_post_meta;
		  
		  
		 	 if(!empty($image_url)){
						// Set Image
				wpclink_post_thumbnail_generator($post_id,$image_url);
			}
		  
		  		  
		  // Referent Categories
		  update_post_meta($post_id, 'wpclink_referent_categories', $found_id);
		  
		  // License Class
		  $license_class_post = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url));
		  
		  $content = apply_filters('the_content', get_post_field('post_content', $post_id));
		  $content_without_html = strip_tags($content);
		  $content_word_count = str_word_count($content_without_html);
		  
		  
		  $tags_catch = array();
		  $tag_on = false;
  		  // Get All Tags
		  foreach($single->category as $single_tag){
		  
			$tags_arr = $single_tag->attributes();
			if($tags_arr["domain"] == 'post_tag'){	
				$tags_catch[] = (string)$single_tag;
				$tag_on = true;	
			}
		  }
		 
		  
		  foreach($tags_catch as $tag_single){
	
		  $term_tag = term_exists($tag_single, 'post_tag');
		  if ($term_tag !== 0 && $term_tag !== null) {
			  
			  // Assign
			  $term_id = $term_tag['term_id'];
			  $term = get_term( $term_id, 'post_tag' );
			  wp_set_post_tags( $post_id, $term->name, true );
			  
		  }else{
			  wp_set_post_tags( $post_id, $tag_single, true );
		  }
	
		}
			
			// Referent Tags
		  update_post_meta($post_id, 'wpclink_referent_tags', $tags_catch);
			
			 						
// CODRA
	// RULE #1
	$current_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option('authorized_creators');
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
			  
	// RULE #2
	// Copyright Owner or Creator
	if($right_holder_id == $current_user_id || wpclink_user_has_creator_list($current_user_id)){
	}else{
		return false;	
	}
			  
	
	
	// RULE #3
	$territory = wpclink_get_option('territory_code');
	if(empty($territory)){
		// Error Message
		
		return false;
		
	}
	
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
	$author_id = get_post_field ('post_author', $post_id);
	$creator_id = get_user_meta($author_id,'wpclink_party_ID',true);
	$post_excerpts = get_the_excerpt($post_id);
	$post_type = get_post_type($post_id);
	$post_character = 'language';
	$clink_party_id = get_user_meta($clink_party_id,'wpclink_party_ID',true);
			  
	$right_holder_id = $creator_id;
			  
	// Customer ID
	$party_access_key = get_user_meta($author_id,'wpclink_party_access_key',true);	
			  
	// Taxonomy Permission
	$taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true);	
	
	
	$right_holder = wpclink_get_option('rights_holder');
	
	
	$right_holder_user_id = $right_holder_id;
	$right_holder_user_id = get_user_meta($right_holder_user_id,'wpclink_party_ID',true);
	
	$post_year = get_the_date( 'Y', $post_id );
	$clink_language = wpclink_get_current_site_lang();
	$clink_terriory_code = wpclink_get_current_terriority_name();
	$content_id = '';
	$clink_referent_creation_identifier = $post_contentID;
	$clink_party_link_role = 'Licensee';
	$clink_creation_link_role = 'LicensedContent';
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
	
	
	$cats = array();
     
	foreach($post_categories as $c){
		$cat = get_category( $c );
		$cats[] = $cat->name;
	}
	
	
	$post_tags = wp_get_post_tags( $post_id );
	
	$tags = array();
	
	foreach($post_tags as $t){
		$tags[] = $t->name;
	}
	
	/*____URL____*/
	
	$url_content = WPCLINK_CREATION_API;
	
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	$sending = array(
                    'body' => array(
                        'post_title'  => html_entity_decode($post_title),
                        'guid'	=> $guid,
						'creation_uri' => $permalink,
						'reuseGUID' => $reuse_guid_link,
						'clink_creatorID' => $creator_id,
                        'post_excerpts' => html_entity_decode($post_excerpts),
						'post_type' => strtolower($post_type),
						'clink_word_count' => $content_word_count,
						'post_charcter' => $post_character,
						'clink_partyID' => $clink_party_id,
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
						'clink_party_link_role' => '',
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'party_access_key' => $party_access_key,
						'clink_license_class' => wpclink_get_license_class_label($post_license_class),
						'clink_license_version' => '0.9',
						'clink_taxonomy_permission' => $taxonomy_permission,
						'clink_language' => $language,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'clink_edition' => 'personal',
						'site_address'  => get_bloginfo('url'),
						'action'=> 'create'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
/* ========= CREATE ============ */
	$response = wp_remote_post(
                $url_content,
                $sending 
            );
						
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );   
				
				// Response Debug
				wpclink_debug_log('GENERATE POST MULTIPLE'.print_r($response,true));
			  
 
            }else {
				
				// Response Debug
				wpclink_debug_log('GENERATE POST MULTIPLE'.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
  			   
			  
		if($resposne_Array['status'] == 'create'){
			// RESPONSE
		update_post_meta($post_id,'wpclink_creation_ID',$resposne_Array['data']['clink_contentID']);
		// REFERENT ID
		update_post_meta($post_id,'wpclink_referent_creation_ID',$post_contentID);
		update_post_meta($post_id,'wpclink_referent_creator_party_ID',$post_creator_clinkid);
		update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
		update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
			
			
		// Now Send to Referent Site
		$send_referent = wpclink_send_linked_creation_ID($post_id_old,$resposne_Array['data']['clink_contentID'],$post_id);
			
		// Save Post ID
		wpclink_add_post_id_linked($post_id,$post_id_old,$link_site_url);
		
		// Response Debug
		wpclink_debug_log('SEND REFERENT POST '.print_r($send_referent ,true));
			
		if(!empty($resposne_Array['data']['creation_access_key'])){
		// Encrypt
		update_post_meta($post_id,'wpclink_creation_access_key',$resposne_Array['data']['creation_access_key']);
		}	
			
		}else if($resposne_Array['status'] == 'promotion_disallow'){
		
		if(!empty($post_id)){
			
			
			
		}
		
		// Adding 24 Hours
		$after_24h = strtotime("+24 hours", strtotime($resposne_Array['data']['last_registration']));
		$after_24h_format =  date('Y-m-d h:i:s', $after_24h);
		
		// Notification
		wpclink_notif_print('You have registered a Creation at <strong>'.$resposne_Array['data']['last_registration'].' (UTC)</strong> Personal Edition allows one registration per 24h.  Please try after <strong>'.$after_24h_format.' (UTC)</strong>','error');
		return false;
		
		
	}
			}
		  		  	
			  }else{
		  }
	}
	
	}	// Return Post ID
	return $result_update;
		
	
	}else if($creation_type == 'page'){
		
$cl_options = wpclink_get_option( 'preferences_general' );
		
if(isset($cl_options['sync_site'])){
	$get_sync_site = $cl_options['sync_site'];
}else{
	$get_sync_site = '';
}
	
		
// Site URL
$link_site_url = apply_filters('cl_site_link',$get_sync_site);
// Validate
if(empty($_POST['post']) or !isset($_POST['action']) or !isset($_POST['action2'])) return false;
if(($_POST['action'] == 'sync' and $_POST['action2'] == '-1') or ($_POST['action'] == '-1' and $_POST['action2'] == 'sync')){
}else{
	return false;
}
if(wpclink_is_acl_r_page()){
	return false;
}
/* ====== PREPARING REQUEST ======= */
$request_query = array();
$all_posts = $_POST['post'];
$comma_posts = implode(",",$all_posts);
$request_query['post__in'] = $comma_posts;
$request_query['nopaging'] = true;
$request_query['post_type'] = 'c-page';
$request_query['get_type'] = 'content';
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
$origin_data = array();
$build_query = build_query( $request_query );
/* ====== QUERY REQUEST ======= */
$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
$xml = simplexml_load_string($xml);
$result_update = false;
foreach($xml->channel->item as $single){ 
/* ===== PREPARING DATA ===== */
// Post ID
$post_id_old = (int)$single->post_id; 
// Post Title
$post_title = (string)$single->title;
// Post Link
$post_link = (string)$single->link;
// Post Author
$ns_dc = $single->children('http://purl.org/dc/elements/1.1/');
$post_author = $ns_dc->creator_id;
// Post Content 
$post_content = (string)$single->children("content", true);
// Post Excerpt 
$post_excerpt = (string)$single->children("excerpt", true);
  // Post Parent		  
$post_parent = (string)$single->children('wp', true)->post_parent;
// Modified Time
$modified_time = (string)$single->modified_time;
// License Class
$post_license_class = (string)$single->post_license_class;
// Taxonomy Permission
$post_taxonomy_permission = (string)$single->post_taxonomy_permission;	
// Post Copyright Owner
$post_rights_holder = (string)$single->post_rights_holder;
// ContentID
$post_contentID = (string)$single->content_id;
// Language
$language = (string)$single->language;
// NEW
$referent_creator_party_ID = (string)$single->referent_creator_party_ID;
$referent_creator_display_name = (string)$single->referent_creator_display_name;
$referent_rights_holder_party_ID = (string)$single->referent_rights_holder_party_ID;
$referent_rights_holder_display_name = (string)$single->referent_rights_holder_display_name;
// Post Creator ID
$post_creator_clinkid = (string)$single->post_creator_clink_id;
// Post Creator
$post_creator = (string)$single->post_creator;
// Image URL		  
if($single->children('wp', true)->post_thumbnail){
	$image_url = $single->children('wp', true)->post_thumbnail->children()->post_thumbnail_url;
}else{
	$image_url = NULL;
}
/* ====== INSERT POST ======= */
// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;
	
	// Setup the author, slug, and title for the post
	
	$selected_author = wpclink_get_option('page_author_assign');
	if(empty($selected_author) or $selected_author == false){
		$author_id = get_current_user_id();
	}else{
		$author_id = $selected_author;
	}
	
	$time = current_time( 'Y-m-d', true );
	$origin_data = array();
	$found_id = array();
	
	
	// No Found Parent
	$found_parent = 0;
	
	
	// If the page doesn't already exist, then create it
	
	
	/* == PAGE PARENT PREPATION == */
	$all_posts = get_posts( array( 'meta_key' => 'wpclink_referent_post_link','posts_per_page' => -1, 'post_type' => 'page') );
	
	foreach ( $all_posts as $post_see ) {
		$data_post_see = get_post_meta($post_see->ID,'wpclink_referent_post_link',true);
		$origin_id_get = $data_post_see[$link_site_url]['origin_id'];
		//print_r($origin_id_get);
		
		echo $post_parent."->". $origin_id_get;
		echo "<br />";
			
		if($post_parent == $origin_id_get){
			$found_parent = $post_see->ID;
			break;
		}
	}
	
	
		// Set the post ID so that we know the post was created successfully
		if($post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_title'		=>	$post_title,
				'post_status'		=>	'publish',
				'post_content'  => $post_content,
				'post_excerpt' => $post_excerpt,
				'post_type'		=>	'page',
				'post_parent' => $found_parent,
				
			),true
		)){
		  // Origin
		  $time = current_time( 'Y-m-d', true);
		  $origin_data[$link_site_url] = array('origin_id' => $post_id_old, 'last_sync' => $time, 'sync_status' => true,'sync_parent'=> $post_parent, 'canonical' => $post_link);
		  
		  $post_parent = 0;
		  
		 update_post_meta($post_id, 'wpclink_referent_post_uri', $post_link);
		 update_post_meta($post_id, 'wpclink_link_flag', 1);
		 update_post_meta($post_id, 'wpclink_creation_license_class', $post_license_class);
		 update_post_meta($post_id, 'wpclink_programmatic_right_categories', $post_taxonomy_permission);
		
			
		// NEW
		update_post_meta($post_id, 'wpclink_referent_creator_party_ID', $referent_creator_party_ID);
		update_post_meta($post_id, 'wpclink_referent_creator_display_name', $referent_creator_display_name);
		update_post_meta($post_id, 'wpclink_referent_rights_holder_display_name', $referent_rights_holder_display_name);	
		 
		 // License Class
		  $license_class_post = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url));
		  
		  if($post_license_class == 'uc-at-um'){
			 // Referent Copyright Owner
		 	 update_post_meta($post_id, 'wpclink_referent_rights_holder_party_ID', $post_rights_holder); 
		  }
		  
		  $content = apply_filters('the_content', get_post_field('post_content', $post_id));
		  $content_without_html = strip_tags($content);
		  $content_word_count = str_word_count($content_without_html);
		  
		  // Update post
		  if($updated_post_meta = update_post_meta($post_id, 'wpclink_referent_post_link', $origin_data)){	  
		  		if($updated_post_meta = update_post_meta($post_id, 'origin_parent', $post_parent)){	  
				
					$result_update = $updated_post_meta;
					$updated_post_id = $updated_post_meta;
					
					if(!empty($image_url)){
						// Set Image
					wpclink_post_thumbnail_generator($post_id,$image_url);
					}
					
					
					
				}
			  }else{
		  	
		  }
		  
		  
		  
// CODRA
	// RULE #1
	$current_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option('authorized_creators');
	
	
	$right_holder = wpclink_get_option('rights_holder');
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
	
	// RULE #2
	// Copyright Owner or Creator
	if($right_holder_id == $current_user_id || wpclink_user_has_creator_list($current_user_id)){
	}else{
		return false;	
	}
	
	
	// RULE #3
	$territory = wpclink_get_option('territory_code');
	if(empty($territory)){
		// Error Message
		
		return false;
		
	}
	
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
	$author_id = get_post_field ('post_author', $post_id);
	$creator_id = get_user_meta($author_id,'wpclink_party_ID',true);
	$post_excerpts = get_the_excerpt($post_id);
	$post_type = get_post_type($post_id);
	$post_character = 'language';
	$clink_party_id = get_user_meta($clink_party_id,'wpclink_party_ID',true);
			
			
	// Customer ID
	$party_access_key = get_user_meta($author_id,'wpclink_party_access_key',true);	
	
	// Taxonomy Permission
	$taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true);
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
	$right_holder_user_id = $right_holder_id;
	$right_holder_user_id = get_user_meta($right_holder_user_id,'wpclink_party_ID',true);
	
	$post_year = get_the_date( 'Y', $post_id );
	$clink_language = $clink_language = wpclink_get_current_site_lang();
	$clink_terriory_code = wpclink_get_current_terriority_name();
	$content_id = '';
	$clink_referent_creation_identifier = $post_contentID;
	$clink_party_link_role = 'Licensee';
	$clink_creation_link_role = 'LicensedContent';
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
	
	
	$cats = array();
	
	
	$post_tags = wp_get_post_tags( $post_id );
	
	$tags = array();
	
	/*____URL____*/
	
	$url_content = WPCLINK_CREATION_API;
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	
/* ========= CREATE ============ */
	$response = wp_remote_post(
                $url_content,
                array(
                    'body' => array(
                        'post_title'  => html_entity_decode($post_title),
                        'guid'	=> $guid,
						'creation_uri' => $permalink,
						'reuseGUID' => $post_link,
						'clink_creatorID' => $creator_id,
                        'post_excerpts' => html_entity_decode($post_excerpts),
						'post_type' => strtolower($post_type),
						'clink_word_count' => $content_word_count,
						'post_charcter' => $post_character,
						'clink_partyID' => $clink_party_id,
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
						'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
						'clink_party_link_role' => '',
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'party_access_key' => $party_access_key,
						// Creator ID							
						'referent_clink_creatorID' => $referent_creator_party_ID, 
						// Creation Right Holder Display Name
						'referent_creation_rights_holder_display_name' => $referent_rights_holder_display_name, 
						// Creation Right Holder Identifier
						'referent_creation_rights_holder_party_ID' => $referent_rights_holder_party_ID,
						// Creation Right Holder Identifier
						'referent_creator_display_name' => $referent_creator_display_name,
						// Referent Creation Identifier
						'referent_creation_ID'=> $clink_referent_creation_identifier,
						'clink_license_class' => wpclink_get_license_class_label($post_license_class),
						'clink_license_version' => '0.9',
						'clink_taxonomy_permission' => $taxonomy_permission,
						'clink_language' => $language,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'clink_edition' => 'personal',
						'site_address'  => get_bloginfo('url'),
						'action'=> 'create'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
						
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
				
				// Response Debug
				wpclink_debug_log('GENERATE PAGE MULTIPLE '.print_r($response,true));
			   
 
            }else {
				
				// Response Debug
				wpclink_debug_log('GENERATE PAGE MULTIPLE '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
  
			   
			  
	if($resposne_Array['status'] == 'create'){
		// RESPONSE
		
		update_post_meta($post_id,'wpclink_creation_ID',$resposne_Array['data']['clink_contentID']);
		// REFERENT ID
		update_post_meta($post_id,'wpclink_referent_creation_ID',$post_contentID);
		update_post_meta($post_id,'wpclink_referent_creator_party_ID',$post_creator_clinkid);
		update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
		update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
		
		
		// Now Send to Referent Site
		$send_referent = wpclink_send_linked_creation_ID($post_id_old,$resposne_Array['data']['clink_contentID'],$post_id,'page');
		
		// Save Post ID
		wpclink_add_post_id_linked($post_id,$post_id_old,$link_site_url);
		
		// Response Debug
		wpclink_debug_log('SEND REFERENT POST '.print_r($send_referent ,true));
		
	}else if($resposne_Array['status'] == 'promotion_disallow'){
		
		if(!empty($post_id)){
			
			
			
		}
		
		// Adding 24 Hours
		$after_24h = strtotime("+24 hours", strtotime($resposne_Array['data']['last_registration']));
		$after_24h_format =  date('Y-m-d h:i:s', $after_24h);
		
		
		wpclink_notif_print('You have registered a Creation at <strong>'.$resposne_Array['data']['last_registration'].' (UTC)</strong> Personal Edition allows one registration per 24h.  Please try after <strong>'.$after_24h_format.' (UTC)</strong>','error');
		return false;
		
		
	}
			}
	  
	}
}	// Return Post ID
	return $result_update;
	
	}
}
/**
 * CLink Update Linked Page
 * 
 * 
 * @return integer page id
 */
function wpclink_update_page(){
	
$cl_options = wpclink_get_option( 'preferences_general' );
// Site URL
$link_site_url = apply_filters('cl_site_link',$cl_options['sync_site']);;
// Validate
if(!isset($_GET['p_sync']) and $_GET['action'] != 'sync_update' and $_GET['cl_type'] != 'page' and !isset($_GET['update_to'])) return false;	
// Update Page
$update_post_id = (int)$_GET['update_to'];
/* ====== PREPARING REQUEST ======= */
$request_query = array();
$request_query['post__in'] = $_GET['p_sync'];
$request_query['get_type'] = 'content';
$request_query['post_type'] = 'c-page';
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
$origin_data = array();
$build_query = build_query( $request_query );
/* ====== QUERY REQUEST ======= */
$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
$xml = simplexml_load_string($xml);
$result_update = false;
foreach($xml->channel->item as $single){ 
/* ===== PREPARING DATA ===== */
// Post ID
$post_id_old = (int)$single->post_id; 
// Post Title
$post_title = (string)$single->title;
// Post Link
$post_link = (string)$single->link;
// Post Author
$ns_dc = $single->children('http://purl.org/dc/elements/1.1/');
$post_author = $ns_dc->creator_id;
// Post Content 
$post_content = (string)$single->children("content", true);
// Post Excerpt 
$post_excerpt = (string)$single->children("excerpt", true);
// Post Parent		  
$post_parent = (string)$single->children('wp', true)->post_parent;
// Image URL		  
$image_url = (string)$single->children('wp', true)->post_thumbnail->children()->post_thumbnail_url;
// Modified Time
$modified_time = (string)$single->modified_time;
/* ====== INSERT POST ======= */
// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;
	// Setup the author, slug, and title for the post
	
	$selected_author = wpclink_get_option('page_author_assign');
	if(empty($selected_author) or $selected_author == false){
		$author_id = get_current_user_id();
	}else{
		$author_id = $selected_author;
	}
	
	$time = current_time( 'Y-m-d', true );
	$origin_data = array();
	
	
	
	// No Found Parent
	$found_parent = 0;
	
	
	// If the page doesn't already exist, then create it
	
	
	/* == PAGE PARENT PREPATION == */
	$all_posts = get_posts( array( 'meta_key' => 'wpclink_referent_post_link','posts_per_page' => -1, 'post_type' => 'page') );
	
	foreach ( $all_posts as $post_see ) {
		$data_post_see = get_post_meta($post_see->ID,'wpclink_referent_post_link',true);
		$origin_id_get = $data_post_see[$link_site_url]['origin_id'];
		//print_r($origin_id_get);
		
		echo $post_parent."->". $origin_id_get;
		echo "<br />";
			
		if($post_parent == $origin_id_get){
			$found_parent = $post_see->ID;
			break;
		}
	}
	
	
		// Set the post ID so that we know the post was created successfully
		if($post_id = wp_update_post(
			array(
				'ID'				=> $update_post_id,
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_title'		=>	$post_title,
				'post_status'		=>	'publish',
				'post_content'  => $post_content,
				'post_excerpt' => $post_excerpt,
				'post_type'		=>	'page',
				'post_parent'	=> $found_parent
				
			),true
		)){
			
			// CLEAN
		  delete_post_meta($update_post_id, 'wpclink_referent_post_link');
		
		  // Origin
		  $time = current_time( 'Y-m-d', true );
		  $origin_data[$link_site_url] = array(	'origin_id' => $post_id_old, 
		  																'last_sync' => $time,
																		'sync_status' => true,
																		'sync_parent' => $post_parent,
																		'canonical'   => $post_link,
																		'modified_time' => $modified_time);
		  
		
		  
		
		  // Update post
		  if($updated_post_meta = update_post_meta($update_post_id, 'wpclink_referent_post_link', $origin_data)){
			  
				if($updated_post_meta = update_post_meta($update_post_id, 'origin_parent', $post_parent)){	  
				}
				
					$result_update = $updated_post_meta;
					$updated_post_id = $updated_post_meta;
					
					if(!empty($image_url)){
						// Set Image
					wpclink_post_thumbnail_generator($update_post_id,$image_url);
					}
					
					
				
		  	
			  }else{
				  
				 		  
		  }
	}
	// Otherwise, we'll stop
	// end if
}	// Return Post ID
	return $result_update;
}
/**
 * CLink Send Linked Creation Identifier
 *  
 * @return interger post id
 */
function wpclink_send_linked_creation_ID($requested_post_id = 0, $linked_creation_ID = 0,$linked_post_id = 0,$type = 'post'){
	
	$current_time_gmt = gmdate('Y-m-d H:i:s');
	$get_date = $current_time_gmt;
    $date_string = strtotime($get_date);
	
	
	$request_query = array();
	$request_query['post__in'] = $requested_post_id;
	
	if($type == 'attachment'){
		$request_query['post_type'] = 'c-attachment';
	}else if($type == 'page'){
		$request_query['post_type'] = 'c-page';
	}else{
		$request_query['post_type'] = 'c-post';
	}
	$request_query['get_type'] = 'content';
	$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));
	$request_query['save_creation_ID'] = 1;
	$request_query['linked_creation_ID'] = urlencode($linked_creation_ID);
	$request_query['referent_publish_date'] = urlencode($date_string);
	$request_query['linked_post_id'] = urlencode($linked_post_id);
	
	
	
	$origin_data = array();
	$build_query = build_query( $request_query );
	/* ====== QUERY REQUEST ======= */
	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
	$xml = simplexml_load_string($xml);
	
	foreach($xml->channel->item as $single){
		
		$linked_creation_update = (string)$single->linked_creation_update;
		
		if($linked_creation_update == 1){
			return true;
		}
	}
	
	return false;
}
/**
 * CLink Register Linked Creation
 *  
 * @return interger post id
 */
function wpclink_register_linked_creation($creation_type = 'post'){
	
if($creation_type == 'post'){
		
		
	
$cl_options = wpclink_get_option( 'preferences_general' );
	
if(isset($cl_options['sync_site'])){
	$get_sync_site = $cl_options['sync_site'];
}else{
	$get_sync_site = '';
}
	
// Site URL
$link_site_url = apply_filters('cl_site_link',$get_sync_site);
if (!isset($_GET['_action_nonce_get']) || !wp_verify_nonce($_GET['_action_nonce_get'], 'content_get_post')) {
	return false;
}
if(wpclink_is_acl_r_page()){
	return false;
}
// Validate
if(!isset($_GET['p_sync']) and $_GET['action'] != 'sync') return false;	
/* ====== PREPARING REQUEST ======= */
$request_query = array();
$request_query['post__in'] = $_GET['p_sync'];
$request_query['post_type'] = 'c-post';
$request_query['get_type'] = 'content';
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
$origin_data = array();
$build_query = build_query( $request_query );
/* ====== QUERY REQUEST ======= */
$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
$xml = simplexml_load_string($xml);
$result_update = false;
foreach($xml->channel->item as $single){ 
/* ===== PREPARING DATA ===== */
// Post ID
$post_id_old = (int)$single->post_id; 
// Post Title
$post_title = (string)$single->title;
// Post Link
$post_link = (string)$single->link;
// Post GUID
$reuse_guid_link = (string)$single->guid;
// ContentID
$post_contentID = (string)$single->content_id;
// License Class
$post_license_class = (string)$single->post_license_class;
// License Class
$post_taxonomy_permission = (string)$single->post_taxonomy_permission;
// License Name
$post_license_name = (string)$single->post_license_name;
// License URL
$post_license_url = (string)$single->post_license_url;
// Post Creator ID
$post_creator_clinkid = (string)$single->post_creator_clink_id;
// Post Creator
$post_creator = (string)$single->post_creator;
// Language
$language = (string)$single->language;
	// Time of Creation
$time_of_creation = (string)$single->time_of_creation;
	
	// Rights
	
$time_of_right = (string)$single->time_of_right;
// NEW
$referent_creator_party_ID = (string)$single->referent_creator_party_ID;
$referent_creator_display_name = (string)$single->referent_creator_display_name;
$referent_rights_holder_party_ID = (string)$single->referent_rights_holder_party_ID;
$referent_rights_holder_display_name = (string)$single->referent_rights_holder_display_name;
// Post Copyright Owner
$post_rights_holder = (string)$single->post_rights_holder;
// Post Author
$ns_dc = $single->children('http://purl.org/dc/elements/1.1/');
$post_author = $ns_dc->creator_id;
$post_author_creator = (string)$ns_dc->creator;
// Post Content 
$post_content = (string)$single->children("content", true);
// Post Excerpt 
$post_excerpt = (string)$single->children("excerpt", true);
// Image Attached Data
$post_attached_meta = (string)$single->post_attached_data;
	
// Image URL		  
if($single->children('wp', true)->post_thumbnail){
	$image_url = $single->children('wp', true)->post_thumbnail->children()->post_thumbnail_url;
}else{
	$image_url = NULL;
}
// Modified Time
$modified_time = (string)$single->modified_time;
/* ===== GET CATEGORY NAME ==== */
$assign_cat = array();
foreach($single->category as $single_cat){
$category_arr = $single_cat->attributes();
if($category_arr["domain"] == 'category'){
	$assign_cat[] = (string)$single_cat;
}
}
/* ====== INSERT POST ======= */
// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;
	
	// Setup the author, slug, and title for the post
	
	$selected_author = wpclink_get_option('post_author_assign');
	if(empty($selected_author) or $selected_author == false){
		$author_id = get_current_user_id();
	}else{
		$author_id = $selected_author;
	}
	
	$time = current_time( 'Y-m-d', true );
	$origin_data = array();
	$found_id = array();
	
	
	
	
	$selected_cat = wpclink_get_option('post_cat_assign');
	if((empty($selected_cat)) or ($selected_cat == false) or ($selected_cat == '-1')){
		
	foreach($assign_cat as $cat_single){
	
		$term = term_exists($cat_single, 'category');
		if ($term !== 0 && $term !== null) {
			$found_id[]= $term['term_id'];
		}else{
			$found_id[] = wp_create_category( $cat_single, '0' );
		}
	
	}
		
	}else{
		$found_id = array($selected_cat);
	}
	
		// Set the post ID so that we know the post was created successfully
		if($post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_title'		=>	$post_title,
				'post_status'		=>	'publish',
				'post_content'  => $post_content,
				'post_excerpt' => $post_excerpt,
				'post_type'		=>	'post',
				'post_category' => $found_id
				
			),true
		)){
		  // Origin
		  $time = current_time( 'Y-m-d', true );
		  $origin_data[$link_site_url] = array('origin_id' => $post_id_old, 'last_sync' => $time, 'sync_status' => true,'canonical'	=>	$post_link);
			
			
			$data_license = wpclink_get_license_by_referent_post_id_and_site_url($post_id_old,$link_site_url);
			
			
			
			if(is_array($data_license)){
				if(!empty($data_license)){
					$right_transaction_ID = $data_license['rights_transaction_ID'];
				}
			}
			
			
		  
		  
		  // Origin Url_shorten(
		  update_post_meta($post_id, 'wpclink_referent_post_uri', $post_link);
		  update_post_meta($post_id, 'wpclink_link_flag', 1);
			// NEW
			update_post_meta($post_id, 'wpclink_referent_creator_party_ID', $referent_creator_party_ID);
			update_post_meta($post_id, 'wpclink_referent_creator_display_name', $referent_creator_display_name);
			update_post_meta($post_id, 'wpclink_referent_rights_holder_display_name', $referent_rights_holder_display_name);			
			
		  update_post_meta($post_id, 'wpclink_referent_creator_display_name', $post_creator);
		 
		  
		  // License Class
		  update_post_meta($post_id, 'wpclink_creation_license_class', $post_license_class);
		  update_post_meta($post_id, 'wpclink_programmatic_right_categories', $post_taxonomy_permission);
		  
		  if($post_license_class == 'personal'){
			 // Referent Copyright Owner
		 	 update_post_meta($post_id, 'wpclink_referent_rights_holder_party_ID', $referent_rights_holder_party_ID); 
		  }
		  
		  // Update post
		  if($updated_post_meta = update_post_meta($post_id, 'wpclink_referent_post_link', $origin_data)){	  
		  $result_update = $updated_post_meta;
		  $updated_post_id = $updated_post_meta;
		  
		  
		  if(!empty($image_url)){
		  // Set Image
		  wpclink_post_thumbnail_generator($post_id,$image_url);
		  }
		  
		  if(!empty($post_attached_meta)){
			  update_post_meta($post_id,'wpclink_referent_media_attributes',json_decode($post_attached_meta,true));
		  }
				  
		  // Referent Categories
		  update_post_meta($post_id, 'wpclink_referent_categories', $found_id);
		  
		  // License Class
		  $license_class_post = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url));
		  
		  $content = apply_filters('the_content', get_post_field('post_content', $post_id));
		  $content_without_html = strip_tags($content);
		  $content_word_count = str_word_count($content_without_html);
		  
		  
		  $tags_catch = array();
		  $tag_on = false;
  		  // Get All Tags
		  foreach($single->category as $single_tag){
		  
			$tags_arr = $single_tag->attributes();
			if($tags_arr["domain"] == 'post_tag'){	
				$tags_catch[] = (string)$single_tag;
				$tag_on = true;	
			}
		  }
		 
		  
		  foreach($tags_catch as $tag_single){
	
		  $term_tag = term_exists($tag_single, 'post_tag');
		  if ($term_tag !== 0 && $term_tag !== null) {
			  
			  // Assign
			  $term_id = $term_tag['term_id'];
			  $term = get_term( $term_id, 'post_tag' );
			  wp_set_post_tags( $post_id, $term->name, true );
			  
		  }else{
			  wp_set_post_tags( $post_id, $tag_single, true );
		  }
	
		}
		
		
		
		// Referent Tags
		  update_post_meta($post_id, 'wpclink_referent_tags', $tags_catch);
		
			
			
// CODRA
// RULE #1
	$current_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option('authorized_creators');
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
// RULE #2	
	// Copyright Owner or Creator
	if($right_holder_id == $current_user_id || wpclink_user_has_creator_list($current_user_id)){
	}else{
		return false;	
	}
	
	
// RULE #3
	$territory = wpclink_get_option('territory_code');
	if(empty($territory)){
		// Error Message
		
		return false;
		
	}
	
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
	$author_id = get_post_field ('post_author', $post_id);
	$creator_id = get_user_meta($author_id,'wpclink_party_ID',true);
	$post_excerpts = get_the_excerpt($post_id);
	$post_type = get_post_type($post_id);
	$post_character = 'language';
	$clink_party_id = get_user_meta($clink_party_id,'wpclink_party_ID',true);
			  
			  
	// Customer ID
	$party_access_key = get_user_meta($author_id,'wpclink_party_access_key',true);		  
	
	
	// Taxonomy Permission
	$taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true);		 
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
	$right_holder_user_id = $right_holder_id;
	$right_holder_user_id = get_user_meta($right_holder_id,'wpclink_party_ID',true);
			  
			  
	/* ====== Creation Right Holder ========= */
	$creation_right_holder_data = get_userdata($right_holder_id);
	$creation_right_holder_display_name = $creation_right_holder_data->display_name;
	$creation_right_holder_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	/* ====== Creator Display Name ========== */
	$creator_user_id =  get_userdata($author_id);
	$creator_user_data = get_userdata($author_id);
	$creator_user_display = $creator_user_data->display_name;
	
	$post_year = get_the_date( 'Y', $post_id );
	$clink_language = wpclink_get_current_site_lang();
	$clink_terriory_code = wpclink_get_current_terriority_name();
	$content_id = '';
	$clink_referent_creation_identifier = $post_contentID;
	$clink_party_link_role = 'Licensee';
	$clink_creation_link_role = 'LicensedContent';
	$time = current_time('Y-m-d',true);
	$time .= 'T';
	$time .= current_time('G:i:s:v',true);
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
	
	
	$cats = array();
     
	foreach($post_categories as $c){
		$cat = get_category( $c );
		$cats[] = $cat->name;
	}
	
			  
		// Create GUID
	$guid = get_bloginfo('url').'/?p='.$post_id;
	
	
	$post_tags = wp_get_post_tags( $post_id );
	
	$tags = array();
	
	foreach($post_tags as $t){
		$tags[] = $t->name;
	}
	
	/*____URL____*/
	
	$url_content = WPCLINK_CREATION_API;
	
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
		
	$sending = array(
                    'body' => array(
                        'post_title'  => html_entity_decode($post_title),
                        'guid'	=> $guid,
						'reuseGUID' => $reuse_guid_link,
						'creation_uri' => $permalink,
						'creationGUID_uri' => $guid,
                        'post_excerpts' => html_entity_decode($post_excerpts),
						'post_type' => strtolower($post_type),
						'clink_word_count' => $content_word_count,
						'post_charcter' => $post_character,
						'clink_partyID' => $clink_party_id,
						// Creator ID							
						'clink_creatorID' => $creator_id, 
						// Creation Right Holder Display Name
						'creation_rights_holder_display_name' => $creation_right_holder_display_name, 
						// Creation Right Holder Identifier
						'creation_rights_holder_party_ID' => $creation_right_holder_identifier,
						// Creation Right Holder Identifier
						'creator_display_name' => $creator_user_display,					
						// Referent Creator ID							
						'referent_clink_creatorID' => $referent_creator_party_ID, 
						// Referent Creation Right Holder Display Name
						'referent_creation_rights_holder_display_name' => $referent_rights_holder_display_name, 
						// Referent Creation Right Holder Identifier
						'referent_creation_rights_holder_party_ID' => $referent_rights_holder_party_ID,
						// Referent Creation Right Holder Identifier
						'referent_creator_display_name' => $referent_creator_display_name,
						// Referent Creation Identifier
						'referent_creation_ID'=> $clink_referent_creation_identifier,
						'clink_license_version' => '0.9',
						'clink_license_url' => $post_license_url,
						'clink_license_name' => $post_license_name,
						'clink_right_transaction_ID' => $right_transaction_ID,
						'clink_referent_creator_display_name' => $post_creator,
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
						'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
						'clink_party_link_role' => '',
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'party_access_key' => $party_access_key,
						'clink_license_class' => $post_license_class,
						'clink_taxonomy_permission' => $taxonomy_permission,
						'clink_license_version' => '0.9',
						'clink_language' => $language,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'clink_edition' => 'personal',
						'site_address'  => get_bloginfo('url'),
						'action'=> 'create'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
/* ========= CREATE ============ */
	$response = wp_remote_post(
                $url_content,
                $sending 
            );
			  
			  wpclink_debug_log('GENERATE PRE POST '.print_r($sending,true));
						
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
				
				// Response Debug
				wpclink_debug_log('GENERATE POST '.print_r($response,true));
			   
            }else {
				
				// Response Debug
				wpclink_debug_log('GENERATE POST '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
  
		
			  
	if($resposne_Array['status'] == 'create'){
		// RESPONSE
		
		update_post_meta($post_id,'wpclink_creation_ID',$resposne_Array['data']['clink_contentID']);
		// REFERENT ID
		update_post_meta($post_id,'wpclink_referent_creation_ID',$post_contentID);
		update_post_meta($post_id,'wpclink_referent_creator_party_ID',$post_creator_clinkid);
		update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
		update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
		
	if(!empty($resposne_Array['data']['creation_access_key'])){
		// Encrypt
		update_post_meta($post_id,'wpclink_creation_access_key',$resposne_Array['data']['creation_access_key']);
	}
		update_post_meta($post_id,'wpclink_rights_holder_user_id', $author_id);
		
		// Time of creation (Used in media update)
		 update_post_meta($post_id, 'wpclink_time_of_creation', $time_of_creation);
		
		// update_post_meta($post_id, 'wpclink_time_of_right', $time_of_right);
		
		// Now Send to Referent Site
		$send_referent = wpclink_send_linked_creation_ID($post_id_old,$resposne_Array['data']['clink_contentID'],$post_id);
		
		// Save Post ID
		wpclink_add_post_id_linked($post_id,$post_id_old,$link_site_url);
		
		// Right ID
	if(!empty($resposne_Array['data']['clink_rightID'])){
		// Encrypt
		update_post_meta($post_id,'wpclink_right_ID',$resposne_Array['data']['clink_rightID']);
	}
		
		
				
	// Right Create Time
	if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_creation_date' ] ) ) {
		$right_time =  explode('.',$resposne_Array[ 'data' ][ 'clink_right_creation_date' ]);
		// Update Rights Create Time ID
		update_post_meta( $post_id, 'wpclink_right_created_time', $right_time[0].'Z'  );
	}
		
		// Response Debug
		wpclink_debug_log('SEND REFERENT POST '.print_r($send_referent ,true));
		
		
	}else if($resposne_Array['status'] == 'promotion_disallow'){
		
		if(!empty($post_id)){
			
			
			
		}
		
		// Adding 24 Hours
		$after_24h = strtotime("+24 hours", strtotime($resposne_Array['data']['last_registration']));
		$after_24h_format =  date('Y-m-d h:i:s', $after_24h);
		
		
		wpclink_notif_print('You have registered a Creation at <strong>'.$resposne_Array['data']['last_registration'].' (UTC)</strong> Personal Edition allows one registration per 24h.  Please try after <strong> (UTC)'.$after_24h_format.'</strong>','error');
		return false;
		
		
	}
	
			}
		  	
			  }else{
		  }
	}
	
}	// Return Post ID
	return $result_update;
		
	
	
	}else if($creation_type == 'page'){
		
		
		
	
$cl_options = wpclink_get_option( 'preferences_general' );
	
if(isset($cl_options['sync_site'])){
	$get_sync_site = $cl_options['sync_site'];
}else{
	$get_sync_site = '';
}
	
// Site URL
$link_site_url = apply_filters('cl_site_link',$get_sync_site);
	
	
if (!isset($_GET['_action_nonce_get']) || !wp_verify_nonce($_GET['_action_nonce_get'], 'content_get_post')) {
	return false;
}
if(wpclink_is_acl_r_page()){
		return false;
}
// Validate
if(!isset($_GET['p_sync']) and $_GET['action'] != 'sync') return false;	
/* ====== PREPARING REQUEST ======= */
$request_query = array();
$request_query['post__in'] = $_GET['p_sync'];
$request_query['get_type'] = 'content';
$request_query['post_type'] = 'c-page';
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
$origin_data = array();
$build_query = build_query( $request_query );
/* ====== QUERY REQUEST ======= */
$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
$xml = simplexml_load_string($xml);
$result_update = false;
foreach($xml->channel->item as $single){ 
/* ===== PREPARING DATA ===== */
// Post ID
$post_id_old = (int)$single->post_id; 
// Post Title
$post_title = (string)$single->title;
// Post Link
$post_link = (string)$single->link;
// Post Author
$ns_dc = $single->children('http://purl.org/dc/elements/1.1/');
$post_author = $ns_dc->creator_id;
// Post Content 
$post_content = (string)$single->children("content", true);
// Post Excerpt 
$post_excerpt = (string)$single->children("excerpt", true);
// Image Attached Data
$post_attached_meta = (string)$single->post_attached_data;
// Post Parent		  
$post_parent = (string)$single->children('wp', true)->post_parent;
// License Class
$post_license_class = (string)$single->post_license_class;
// Post Taxonomy Permission
$post_taxonomy_permission = (string)$single->post_taxonomy_permission;
// Post Copyright Owner
$post_rights_holder = (string)$single->post_rights_holder;
// ContentID
$post_contentID = (string)$single->content_id;
// Post Creator ID
$post_creator_clinkid = (string)$single->post_creator_clink_id;
// Post Creator
$post_creator = (string)$single->post_creator;
// Time of Creation
$time_of_creation = (string)$single->time_of_creation;
	
$time_of_right = (string)$single->time_of_right;
// NEW
$referent_creator_party_ID = (string)$single->referent_creator_party_ID;
$referent_creator_display_name = (string)$single->referent_creator_display_name;
$referent_rights_holder_party_ID = (string)$single->referent_rights_holder_party_ID;
$referent_rights_holder_display_name = (string)$single->referent_rights_holder_display_name;
// Language
$language = (string)$single->language;
// Image URL		  
if($single->children('wp', true)->post_thumbnail){
	$image_url = $single->children('wp', true)->post_thumbnail->children()->post_thumbnail_url;
}else{
	$image_url = NULL;
}
// Modified Time
$modified_time = (string)$single->modified_time;
/* ====== INSERT POST ======= */
// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;
	// Setup the author, slug, and title for the post
	
	$selected_author = wpclink_get_option('page_author_assign');
	if(empty($selected_author) or $selected_author == false){
		$author_id = get_current_user_id();
	}else{
		$author_id = $selected_author;
	}
	
	$time = current_time( 'Y-m-d', true );
	$origin_data = array();
	
	
	
	// No Found Parent
	$found_parent = 0;
	
	
	// If the page doesn't already exist, then create it
	
	
	/* == PAGE PARENT PREPATION == */
	$all_posts = get_posts( array( 'meta_key' => 'wpclink_referent_post_link','posts_per_page' => -1, 'post_type' => 'page') );
	
	foreach ( $all_posts as $post_see ) {
		$data_post_see = get_post_meta($post_see->ID,'wpclink_referent_post_link',true);
		$origin_id_get = $data_post_see[$link_site_url]['origin_id'];
		// Debug
		//print_r($origin_id_get);
		
		echo $post_parent."->". $origin_id_get;
		echo "<br />";
			
		if($post_parent == $origin_id_get){
			$found_parent = $post_see->ID;
			break;
		}
	}
	
		// Set the post ID so that we know the post was created successfully
		if($post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_title'		=>	$post_title,
				'post_status'		=>	'publish',
				'post_content'  => $post_content,
				'post_excerpt' => $post_excerpt,
				'post_type'		=>	'page',
				'post_parent'	=> $found_parent
				
			),true
		)){
		  // Origin
		  $time = current_time( 'Y-m-d', true );
		  $origin_data[$link_site_url] = array(	'origin_id' => $post_id_old, 
		  																'last_sync' => $time,
																		'sync_status' => true,
																		'sync_parent' => $post_parent,
																		'canonical'   => $post_link);
																		
																		
		 update_post_meta($post_id, 'wpclink_referent_post_uri', $post_link);
		 update_post_meta($post_id, 'wpclink_link_flag', 1);
		 update_post_meta($post_id, 'wpclink_creation_license_class', $post_license_class);
		 update_post_meta($post_id, 'wpclink_programmatic_right_categories', $post_taxonomy_permission);
	
		
			
					// NEW
		update_post_meta($post_id, 'wpclink_referent_creator_party_ID', $referent_creator_party_ID);
		update_post_meta($post_id, 'wpclink_referent_creator_display_name', $referent_creator_display_name);
		update_post_meta($post_id, 'wpclink_referent_rights_holder_display_name', $referent_rights_holder_display_name);	
			
		
		 if(!empty($post_attached_meta)){
			  update_post_meta($post_id,'wpclink_referent_media_attributes',json_decode($post_attached_meta,true));
		  }
		
		// Post Creator ID
		$post_creator_clinkid = (string)$single->post_creator_clink_id;
		// Post Creator
		$post_creator = (string)$single->post_creator;
		 
		  // License Class
		  $license_class_post = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url));
		  
		  if($post_license_class == 'uc-at-um'){
			 // Referent Copyright Owner
		 	 update_post_meta($post_id, 'wpclink_referent_rights_holder_party_ID', $post_rights_holder); 
		  }
		  
		  
		  
		  $content = apply_filters('the_content', get_post_field('post_content', $post_id));
		  $content_without_html = strip_tags($content);
		  $content_word_count = str_word_count($content_without_html);
		  
		  // Update post
		  if($updated_post_meta = update_post_meta($post_id, 'wpclink_referent_post_link', $origin_data)){
				if($updated_post_meta = update_post_meta($post_id, 'origin_parent', $post_parent)){	  
				
					$result_update = $updated_post_meta;
					$updated_post_id = $updated_post_meta;
					
					if(!empty($image_url)){
						// Set Image
					wpclink_post_thumbnail_generator($post_id,$image_url);
					}
					
							  
					
				}
				
			
// CODRA
// RULE #1
	$current_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option('authorized_creators');
	
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
	
	
// RULE #2
	if($right_holder_id == $current_user_id || wpclink_user_has_creator_list($current_user_id)){
	}else{
		return false;
	}
	
// RULE #3
	$territory = wpclink_get_option('territory_code');
	if(empty($territory)){
		// Error Message
		
		return false;
		
	}
	
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
	$author_id = get_post_field ('post_author', $post_id);
	$creator_id = get_user_meta($author_id,'wpclink_party_ID',true);
	$post_excerpts = get_the_excerpt($post_id);
	$post_type = get_post_type($post_id);
	$post_character = 'language';
	$clink_party_id = get_user_meta($clink_party_id,'wpclink_party_ID',true);
	
			  
	// Customer ID
	$party_access_key = get_user_meta($author_id,'wpclink_party_access_key',true);
	
	// Taxonomy Permission
	$taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true);	
			  
	
	
	$right_holder = wpclink_get_option('rights_holder');
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
	$right_holder_user_id = $right_holder_id;
	$right_holder_user_id = get_user_meta($right_holder_user_id,'wpclink_party_ID',true);
			  
			  
	/* ====== Creation Right Holder ========= */
	$creation_right_holder_data = get_userdata($right_holder_id);
	$creation_right_holder_display_name = $creation_right_holder_data->display_name;
	$creation_right_holder_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	/* ====== Creator Display Name ========== */
	$creator_user_id =  get_userdata($author_id);
	$creator_user_data = get_userdata($author_id);
	$creator_user_display = $creator_user_data->display_name;
	
	$post_year = get_the_date( 'Y', $post_id );
	$clink_language = wpclink_get_current_site_lang();
	$clink_terriory_code = wpclink_get_current_terriority_name();
	$content_id = '';
	$clink_referent_creation_identifier = $post_contentID;
	$clink_party_link_role = 'Licensee';
	$clink_creation_link_role = 'LicensedContent';
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
	
	
	$cats = array();
	
	
	$post_tags = wp_get_post_tags( $post_id );
	
	$tags = array();
	
	/*____URL____*/
	
	$url_content = WPCLINK_CREATION_API;
			  
	// Create GUID
	$guid = get_bloginfo('url').'/?page_id='.$post_id;
	
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	
/* ========= CREATE ============ */
	$response = wp_remote_post(
                $url_content,
                array(
                    'body' => array(
                        'post_title'  => html_entity_decode($post_title),
                        'guid'	=> $guid,
						'reuseGUID' => $post_link,
						'creation_uri' => $permalink,
						'clink_creatorID' => $creator_id,
                        'post_excerpts' => html_entity_decode($post_excerpts),
						'post_type' => strtolower($post_type),
						'clink_word_count' => $content_word_count,
						'post_charcter' => $post_character,
						'clink_partyID' => $clink_party_id,
						// Creator ID							
						'clink_creatorID' => $creator_id, 
						// Creation Right Holder Display Name
						'creation_rights_holder_display_name' => $creation_right_holder_display_name, 
						// Creation Right Holder Identifier
						'creation_rights_holder_party_ID' => $creation_right_holder_identifier,
						// Creation Right Holder Identifier
						'creator_display_name' => $creator_user_display,
						// Referent Creator ID							
						'referent_clink_creatorID' => $referent_creator_party_ID, 
						// Referent Creation Right Holder Display Name
						'referent_creation_rights_holder_display_name' => $referent_rights_holder_display_name, 
						// Referent Creation Right Holder Identifier
						'referent_creation_rights_holder_party_ID' => $referent_rights_holder_party_ID,
						// Referent Creation Right Holder Identifier
						'referent_creator_display_name' => $referent_creator_display_name,
						// Referent Creation Identifier
						'referent_creation_ID'=> $clink_referent_creation_identifier,				
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
						'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
						'clink_party_link_role' => '',
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'party_access_key' => $party_access_key,
						'clink_license_class' => wpclink_get_license_class_label($post_license_class),
						'clink_license_version' => '0.9',
						'clink_taxonomy_permission' => $taxonomy_permission,
						'clink_language' => $language,
						'clink_post_creation_date' =>$get_post_date,
						'clink_post_modification_date'=> $get_post_modified_date,
						'domain_access_key' => $domain_access_key,
						'clink_edition' => 'personal',
						'site_address'  => get_bloginfo('url'),
						'action'=> 'create'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
						
			
						 
            if ( is_wp_error( $response ) ) {
 
               $wp_error = is_wp_error( $response );
				
				// Response Debug
				wpclink_debug_log('GENERATE PAGE '.print_r($response,true));
				
				
		
			    
            }else {
				
				// Response Debug
				wpclink_debug_log('GENERATE PAGE '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
				
			
		
 			   
			  
	if($resposne_Array['status'] == 'create'){
		// RESPONSE
		
	update_post_meta($post_id,'wpclink_creation_ID',$resposne_Array['data']['clink_contentID']);
	// REFERENT ID
	update_post_meta($post_id,'wpclink_referent_creation_ID',$post_contentID);
	update_post_meta($post_id,'wpclink_referent_creator_party_ID',$post_creator_clinkid);
	update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
	update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
	
	// Time of creation (Used in media update)
	update_post_meta($post_id, 'wpclink_time_of_creation', $time_of_creation);
		
	//update_post_meta($post_id, 'wpclink_time_of_right', $time_of_right);
		
	update_post_meta($post_id,'wpclink_rights_holder_user_id', $author_id);
		
		
	// Right Create Time
	if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_creation_date' ] ) ) {
		$right_time =  explode('.',$resposne_Array[ 'data' ][ 'clink_right_creation_date' ]);
		// Update Rights Create Time ID
		update_post_meta( $post_id, 'wpclink_right_created_time', $right_time[0].'Z'  );
	}
		
		
		
	// Now Send to Referent Site
		$send_referent = wpclink_send_linked_creation_ID($post_id_old,$resposne_Array['data']['clink_contentID'],$post_id,'page');
		
		// Save Post ID
		wpclink_add_post_id_linked($post_id,$post_id_old,$link_site_url);
		
		// Right ID
	if(!empty($resposne_Array['data']['clink_rightID'])){
		// Encrypt
		update_post_meta($post_id,'wpclink_right_ID',$resposne_Array['data']['clink_rightID']);
	}
		
		// Response Debug
		wpclink_debug_log('SEND REFERENT POST '.print_r($send_referent ,true));
		
	}else if($resposne_Array['status'] == 'promotion_disallow'){
		
		
		if(!empty($post_id)){
			
			
		
		}
		
		// Adding 24 Hours
		$after_24h = strtotime("+24 hours", strtotime($resposne_Array['data']['last_registration']));
		$after_24h_format =  date('Y-m-d h:i:s', $after_24h);
		
		
		wpclink_notif_print('You have registered a Creation at <strong>'.$resposne_Array['data']['last_registration'].' (UTC)</strong> Personal Edition allows one registration per 24h.  Please try after <strong>'.$after_24h_format.' (UTC)</strong>','error');
		return false;
	}
	
			}
		  	
			  }else{
		 
		  }
	}
	
}	// Return Post ID
	return $result_update;
	
}else if($creation_type == 'attachment'){
	
wpclink_update_option('wpclink_loader_status_linked', '0' );
	
$error_exif = array(
			'complete' => 'failed',
			'status' => '',
			'code' => 'EXIF',
			'error_type' => 'exiftool',
			'error_headline' => __( 'ExifTool Error', 'cl_text' ),
			'error_text' => __( 'Please try again later! <br> If the problem persist please contact support.', 'cl_text' ),
			'clink_error_status' => '1104',
			'clink_internal_error_code' => '',
			'clink_internal_error_location' => '',
			'message' => 'ExifTool error',
			'data' => array()
		);
$cl_options = wpclink_get_option( 'preferences_general' );
	
if(isset($cl_options['sync_site'])){
	$get_sync_site = $cl_options['sync_site'];
}else{
	$get_sync_site = '';
}
// Site URL
$link_site_url = apply_filters('cl_site_link',$get_sync_site);
if(wpclink_is_acl_r_page()){
		return false;
}
$get_action = (isset($_GET['action'])) ? $_GET['action'] : '';
// Validate
if(!isset($_GET['p_sync']) and $get_action != 'sync') return false;	
/* ====== PREPARING REQUEST ======= */
$request_query = array();
$request_query['post__in'] = $_GET['p_sync'];
$request_query['get_type'] = 'content';
$request_query['post_type'] = 'c-attachment';
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
$origin_data = array();
$build_query = build_query( $request_query );
/* ====== QUERY REQUEST ======= */
$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
$xml = simplexml_load_string($xml);
$result_update = false;
	
		
		
$site_name = (string)$xml->channel->title;
foreach($xml->channel->item as $single){ 
/* ===== PREPARING DATA ===== */
// Post ID
$post_id_old = (int)$single->post_id; 
// Post Title
$post_title = (string)$single->title;
// Post Link
$post_link = (string)$single->link;
// Post Author
$ns_dc = $single->children('http://purl.org/dc/elements/1.1/');
$post_author = $ns_dc->creator_id;
// Post Excerpt 
$post_excerpt = (string)$single->children("excerpt", true);
// Post Parent		  
$post_parent = (string)$single->children('wp', true)->post_parent;
// License Class
$post_license_class = (string)$single->post_license_class;
// Post Taxonomy Permission
$post_taxonomy_permission = (string)$single->post_taxonomy_permission;
// Post Copyright Owner
$post_rights_holder = (string)$single->post_rights_holder;
// ContentID
$post_contentID = (string)$single->content_id;
// Licensor URL
$licensor_url = (string)$single->licensor_url;
// Time of Creation
$time_of_creation = (string)$single->time_of_creation;
	
$time_of_right = (string)$single->time_of_right;
// File URL
$file_url = (string)$single->guid;
// Post Creator ID
$post_creator_clinkid = (string)$single->post_creator_clink_id;
// Post Creator
$post_creator = (string)$single->post_creator;
// NEW
$referent_creator_party_ID = (string)$single->referent_creator_party_ID;
$referent_creator_display_name = (string)$single->referent_creator_display_name;
$referent_rights_holder_party_ID = (string)$single->referent_rights_holder_party_ID;
$referent_rights_holder_display_name = (string)$single->referent_rights_holder_display_name;
// Language
$language = (string)$single->language;
// Modified Time
$modified_time = (string)$single->modified_time;
	
/* ====== Right Holder ========= */
	
	$right_holder_id =  get_current_user_id();
			
	$right_holder_user_id = $right_holder_id;
	$right_holder_user_id = get_user_meta($right_holder_user_id,'wpclink_party_ID',true);
	
/* ====== Creation Right Holder ========= */
	$creation_right_holder_data = get_userdata($right_holder_id);
	$creation_right_holder_display_name = $creation_right_holder_data->display_name;
	$creation_right_holder_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	
	
/* ====== Creator Display Name ========== */
	// Author ID
	$creator_user_data = get_userdata($right_holder_id);
	$creator_user_display = $creator_user_data->display_name;
	
/* Referent Creation Identifier */
$clink_referent_creation_identifier = $post_contentID;
	
/* ====== INSERT POST ======= */
// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;
	// Setup the author, slug, and title for the post
	$selected_author = wpclink_get_option('page_author_assign');
	if(empty($selected_author) or $selected_author == false){
		$author_id = get_current_user_id();
	}else{
		$author_id = $selected_author;
	}
	
	$time = current_time( 'Y-m-d', true );
	$origin_data = array();
	$attachment_data = array('excerpt' => (string)$single->description, 'title' => $post_title);
	
	$post_id = wpclink_attachment_generator($file_url, $attachment_data);
	
	
	// is generated?
	if($post_id == false) return false;
		
			// IPTC Fields and Data
			$iptc_metadata = array(
				'metadata_iptc_headline' => 'image_title',
				'metadata_iptc_creator' => 'creator',
				'metadata_iptc_description' => 'description',
				'metadata_iptc_keywords' => 'keywords',
				'metadata_iptc_title' => 'title',
				'metadata_iptc_credit' => 'credit',
				'metadata_iptc_copyright_notice' => 'copyright_notice',
				'metadata_copyrightownerid' => 'image_creator_ID',
				'metadata_copyrightownername' => 'image_creator_name',
				'metadata_copyrightownerimageid' => 'creation_ID',
				'metadata_licensoremail' => 'licensor_email',
				'metadata_licensorid' => 'licensor_ID',
				'metadata_licensorname' => 'licensor_display_name',
				'metadata_licensorimageid' => 'licensor_image_ID',
				'metadata_licensorurl' => 'licensor_url',
				'metadata_termsandconditionsurl' => 'termsandcondition_url',
				'metadata_xmp-xmprights_webstatement' => 'webstatement',
				'metadata_copyrightstatus' => 'linked_CopyrightStatus',
				'metadata_photoshop_source' => 'photoshop_source',
			);
	
			$fileimageconstraints_array = array(
			'IF-MFN' => "Maintain File Name",
			'IF-MID' => "Maintain ID in File Name",
			'IF-MMD' => "Maintain Metadata",
			'IF-MFT' => "Maintain File Type");
	
			$creditlinerequired_array = array(
			'CR-NRQ' => "Not Required",
			'CR-COI' => "Credit on Image",
			'CR-CAI' => "Credit Adjacent To Image",
			'CR-CCA' => "Credit in Credits Area");
	
			$copyrightstatus_array = array(
			'CS-PRO' => "Protected",
			'CS-PUB' => "Public Domain",
			'CS-UNK' => "Unknown");
			
				
			$attachment_path = wpclink_iptc_image_path( $post_id, 'full' );			
				
			foreach($iptc_metadata as $key => $iptc_xml_tag){
							
				if(isset($single->{$key})){
					
					
					if($iptc_xml_tag == "linked_CopyrightStatus"){
					$metatag_value	= (string)$single->{$key};		
					$xmp_metadata_array[$iptc_xml_tag] = $copyrightstatus_array[$metatag_value];
						
					}else{
						if($key == "metadata_licensorurl"){
						// Set Values					
						$xmp_metadata_array[$iptc_xml_tag] = urldecode((string)$single->{$key});
						}else{
						// Set Values					
						$xmp_metadata_array[$iptc_xml_tag] = (string)$single->{$key};
						}
					}
				}
				
			}
	
	
			$post_description = (string)$single->description;
			$xmp_metadata_array['description'] = $post_description;
	
			//Creator ID
			if($clink_creator_ID = get_user_meta(get_current_user_id(),'wpclink_party_ID',true)){
				$xmp_metadata_array['licensee_ID'] = WPCLINK_ID_URL.'/#objects/'.$clink_creator_ID;
				$xmp_metadata_array['caption_writer'] = WPCLINK_ID_URL.'/#objects/'.$clink_creator_ID;
			}
	
			
	
				// Linked Metadata
				$xmp_metadata_array['mediaconstraints'] = 'WordPress';
				$xmp_metadata_array['linked_ImageFileConstraints'] = "Maintain Metadata";
				$xmp_metadata_array['linked_CreditLineRequired'] = 'Credit on Image';
				$xmp_metadata_array['mediasummarycode'] = '|PLUS|V0121|U001|1IAK1UNA2BFU3PTZ4SKG5VUY6QUL7DWM8RAA8IPO8LAA9ENE| ';
	
	
			if(!empty($post_taxonomy_permission)){
				if($post_taxonomy_permission != 'un-editable'){
					$xmp_metadata_array['linked_other_conditions'] = $post_taxonomy_permission;
				}
			}
		
// Writes embeded metadata
wpclink_update_option('wpclink_loader_status_linked','writesimage');
	
try{
	// Write
	wpclink_metadata_writter($attachment_path,$xmp_metadata_array,$post_id);
	
}catch (Exception $e){
		
	$error_exif['message'] = $e->getMessage();
	wpclink_update_option(  'wpclink_loader_status_linked', 'error' );
	wpclink_update_option(  'wpclink_loader_linked_error_data', $error_exif );
	
	if( wp_delete_attachment( $post_id, true )) {
			// Delete
		}
	return false;
		
		
}
	
	// No Found Parent
	$found_parent = 0;
		// Set the post ID so that we know the post was created successfully
		if($post_id){
			
		  // Origin
		  $time = current_time( 'Y-m-d', true );
		  $origin_data[$link_site_url] = array(	'origin_id' => $post_id_old, 
		  																'last_sync' => $time,
																		'sync_status' => true,
																		'sync_parent' => $post_parent,
																		'canonical'   => $post_link);
																
		 update_post_meta($post_id, 'wpclink_referent_post_uri', $post_link);
		 update_post_meta($post_id, 'wpclink_link_flag', 1);
		 update_post_meta($post_id, 'wpclink_creation_license_class', $post_license_class);
		 update_post_meta($post_id, 'wpclink_programmatic_right_categories', $post_taxonomy_permission);
		 update_post_meta($post_id, 'wpclink_custom_url', $licensor_url);
		 update_post_meta($post_id, 'wpclink_referent_site_name', $site_name);
			
		 // Time of creation (Used in media update)
		 update_post_meta($post_id, 'wpclink_time_of_creation', $time_of_creation);
			
		//update_post_meta($post_id, 'wpclink_time_of_creation', $time_of_right);
			
		// NEW
		update_post_meta($post_id, 'wpclink_referent_creator_party_ID', $referent_creator_party_ID);
		update_post_meta($post_id, 'wpclink_referent_creator_display_name', $referent_creator_display_name);
		update_post_meta($post_id, 'wpclink_referent_rights_holder_display_name', $referent_rights_holder_display_name);	
			
		
		$data_license = wpclink_get_license_by_referent_post_id_and_site_url($post_id_old,$link_site_url);
			
			
		if(is_array($data_license)){
				if(!empty($data_license)){
					$right_transaction_ID = $data_license['rights_transaction_ID'];
					
					$xmp_metadata_array['right_transaction_ID'] = WPCLINK_ID_URL.'/#objects/'.$right_transaction_ID;
				}
		}
		
		// Post Creator ID
		$post_creator_clinkid = (string)$single->post_creator_clink_id;
		// Post Creator
		$post_creator = (string)$single->post_creator;
		 
		// License Class
		$license_class_post = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url));
		if($post_license_class == 'uc-at-um'){
			  
			 // Referent Copyright Owner
		 	 update_post_meta($post_id, 'wpclink_referent_rights_holder_party_ID', $post_rights_holder); 
		}
		  $content = apply_filters('the_content', get_post_field('post_content', $post_id));
		  $content_without_html = strip_tags($content);
		  $content_word_count = str_word_count($content_without_html);
		  
		  // Update post
		  if($updated_post_meta = update_post_meta($post_id, 'wpclink_referent_post_link', $origin_data)){
			  
				if($updated_post_meta = update_post_meta($post_id, 'origin_parent', $post_parent)){	 
					$result_update = $updated_post_meta;
					$updated_post_id = $updated_post_meta;
				}
			  
			  
			  
			  
	/* -- REGISTRATION SECTION -- */		  
	$metadata_image_array = wpclink_get_image_metadata_value($attachment_path,array('IPTC:By-line','IPTC:CopyrightNotice','IPTC:Credit','IPTC:ObjectName','IPTC:Keywords'));
			  
	// Fix		  
	$attachment_id = $post_id;
	$creator_user_id = get_current_user_id();
	// Title
	$attachment_title = get_the_title( $attachment_id );
	// Title
	$attachment_post_url = wp_get_attachment_url( $attachment_id );
	// File URL
	$file_url = wp_get_attachment_url( $attachment_id );
	// Excerpt
	$attachment_excerpt = wp_get_attachment_caption( $attachment_id );
	// Creator
	$creator_user_info = get_userdata( $creator_user_id );
	// Domain ID
	$domain_access_key = wpclink_get_option( 'domain_access_key' );
	// Copyright Notice
	$copyright_notice = $metadata_image_array['IPTC:CopyrightNotice'];
	// Credit Line
	$creditline = $metadata_image_array['IPTC:Credit'];
	//Creator ID
	$creator_id = get_user_meta($creator_user_id,'wpclink_party_ID',true);
	// IPTC Title
	$iptc_title = $metadata_image_array['IPTC:ObjectName'];
	// Keywords
	$keywords = $metadata_image_array['IPTC:Keywords'];
			  
	// CLink Language
	$clink_language = wpclink_get_current_site_lang();
	// Terriotory
	$clink_terriory_code = wpclink_get_current_terriority_name();
	if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
		$action = 'update';
		$time = get_the_date('Y-m-d',$attachment_id);
		$time .= 'T';
		$time .= get_the_date('G:i:s',$attachment_id);
		$time .= 'Z';
	} else {
		$action = 'create';
		$creationID = '';
		$time = get_the_date('Y-m-d',$attachment_id);
		$time .= 'T';
		$time .= get_the_date('G:i:s',$attachment_id);
		$time .= 'Z';
	}
			  
$get_post_date  = get_the_date('Y-m-d',$attachment_id);
$get_post_date .= 'T';
$get_post_date .= get_the_date('G:i:s',$attachment_id);
$get_post_date .= 'Z';
			
$get_post_modified_date  = get_the_modified_time('Y-m-d',$attachment_id);
$get_post_modified_date .= 'T';
$get_post_modified_date .= get_the_modified_time('G:i:s',$attachment_id);
$get_post_modified_date .= 'Z';
	$creation_access_key = get_post_meta( $attachment_id, 'wpclink_creation_access_key', true );
	$post_guid = wp_get_original_image_url($attachment_id);
			  
	$archive_link = wpclink_get_last_archive($attachment_id);  
			  
			  
    // Reuse Guid
	$reuse_GUID = $licensor_url;
	$iscc = '0';
	
	
	if($clink_taxonomy_permission = get_post_meta( $attachment_id, 'wpclink_programmatic_right_categories', true )){
	}else{
		$clink_taxonomy_permission = '';
	}
	
  	// Writing to registry
  	wpclink_update_option('wpclink_loader_status_linked','writesregistry');
			  
	$url_media = WPCLINK_MEDIA_API;
			  
	// Register to CLink.ID
	$response = wp_remote_post(
		$url_media,
		array(
			'body' => array(
				'CLinkContentID' => $creationID,
				'post_title' => html_entity_decode( $attachment_title ),
				'iptc_title' => $iptc_title,
				'keywords' => $keywords,
				'creator_uri' => $creator_user_info->user_url,
				'creation_GUID' => wp_get_original_image_url($attachment_id),
				'attachment_post_url' => preg_replace("/^http:/i", "https:", $attachment_post_url),
				'reuseGUID' => $reuse_GUID,
				'clink_taxonomy_permission' => $clink_taxonomy_permission,
				'creator_display_name' => $creator_user_info->display_name,
				'creator_email' => $creator_user_info->user_email,
				'post_excerpts' => $attachment_excerpt,
				'clink_right_transaction_ID' => $right_transaction_ID,
				'mediasummarycode' => '|PLUS|V0121|U001|1IAK1UNA2BFU3PTZ4SKG5VUY6QUL7DWM8RAA8IPO8LAA9ENE| ',
				'time_of_creation' => $time_of_creation,
				'time_of_modification' => $get_post_modified_date,
				'domain_access_key' => $domain_access_key,
				'site_address' => get_bloginfo( 'url' ),
				// Creation Right Holder Display Name
				'creation_rights_holder_display_name' => $creation_right_holder_display_name, 
				// Creation Right Holder Identifier
				'creation_rights_holder_party_ID' => $creation_right_holder_identifier,
				// Referent Creator ID							
				'referent_clink_creatorID' => $referent_creator_party_ID, 
				// Referent Creation Right Holder Display Name
				'referent_creation_rights_holder_display_name' => $referent_rights_holder_display_name, 
				// Referent Creation Right Holder Identifier
				'referent_creation_rights_holder_party_ID' => $referent_rights_holder_party_ID,
				// Referent Creation Right Holder Identifier
				'referent_creator_display_name' => $referent_creator_display_name,
				// Referent Creation Identifier
				'referent_creation_ID'=> $clink_referent_creation_identifier,
				// Referent Creation Identifier
				'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
				'iscc' => $iscc,
				'creditline' => $creditline,
				'clink_creatorID' => $creator_id,
				'creation_access_key' => '',
				'copyright_notice' => $copyright_notice,
				'clink_language' => $clink_language,
				'clink_territory_code' => $clink_terriory_code,
				'archive_web_url' => $archive_link,
				'action' => 'create'
			), 'timeout' => WPCLINK_API_TIMEOUT, 'method' => 'POST'
		)
	);
			  
			  wpclink_debug_log( 'RIT' . $right_transaction_ID );
	if ( is_wp_error( $response ) ) {
			$wp_error = is_wp_error( $response );
			// Response Debug
			wpclink_debug_log( 'PUBLISH MEDIA LINKED ERROR' . print_r( $response, true ) );
		
		// WP ERROR
		if($wp_error == 1){
			$response_check = wpclink_return_wp_error($response);
			
			wpclink_update_option('wpclink_loader_status_linked', 'error' );
			wpclink_update_option('wpclink_loader_linked_error_data', $response_check );
			
			if( wp_delete_attachment( $attachment_id, true )) {
				
			
				
			// Done
			}
			return false;
		}
		/* --------------------------------------------- */
		
		
	} else {
		// Response Debug
		wpclink_debug_log( 'PUBLISH MEDIA LINKED ' . print_r( $response, true ) );
		$response_json = $response[ 'body' ];
		$resposne_Array = json_decode( $response_json, true );
		
		// SERVER / CLINKID ERROR 
		$return_response = wpclink_return_api_reponse($response);
		if($return_response === true){
			
			/* Check response has is_error */
			$response_check = wpclink_response_check($resposne_Array);
			
			if($response_check == false){
				
			}else{
				wpclink_debug_log( 'PUBLISH MEDIA LINKED CUSTOMERROR:CHECK' . print_r( $response_check, true ) );
				wpclink_update_option( 'wpclink_loader_status_linked', 'error' );
				wpclink_update_option( 'wpclink_loader_linked_error_data', $response_check );
				if( wp_delete_attachment( $attachment_id, true )) {
				// Done
						
				}
				return false;
			}
			
		}else{	
				wpclink_debug_log( 'PUBLISH MEDIA LINKED CUSTOMERROR:RETURNAPI' . print_r( $return_response, true ) );
				wpclink_update_option( 'wpclink_loader_status_linked', 'error' );
				wpclink_update_option( 'wpclink_loader_linked_error_data', $return_response );
				if( wp_delete_attachment( $attachment_id, true )) {
				// Done
					
						
				}
				return false;
		}
		
		
		
		if ( $resposne_Array[ 'status' ] == 'create' ) {
			if ( !empty( $resposne_Array[ 'data' ][ 'clink_creationID' ] ) ) {
				// Update Creation ID
				update_post_meta( $attachment_id, 'wpclink_creation_ID', $resposne_Array[ 'data' ][ 'clink_creationID' ] );
				
				// REFERENT ID
				update_post_meta($attachment_id,'wpclink_referent_creation_ID',$post_contentID);
				update_post_meta($attachment_id,'wpclink_referent_creator_party_ID',$post_creator_clinkid);
				update_post_meta($attachment_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
				update_post_meta($attachment_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
				
				if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_identifier' ] ) ) {
					// Update Rights ID
					update_post_meta( $attachment_id, 'wpclink_right_ID', $resposne_Array[ 'data' ][ 'clink_right_identifier' ] );
				}
				
				
				if ( !empty( $resposne_Array[ 'data' ][ 'clink_transaction_time' ] ) ) {
					$xmp_metadata_array['right_transaction_time'] = $resposne_Array[ 'data' ][ 'clink_transaction_time' ];
					
				}
				
				
				// Right Create Time
				if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_create_date' ] ) ) {
					$right_time =  explode('.',$resposne_Array[ 'data' ][ 'clink_right_create_date' ]);
					// Update Rights Create Time ID
					update_post_meta( $attachment_id, 'wpclink_right_created_time', $right_time[0].'Z'  );
				}
				
				update_post_meta($attachment_id,'wpclink_rights_holder_user_id', $author_id);
				
				// Now Send to Referent Site
				$send_referent = wpclink_send_linked_creation_ID($post_id_old,$resposne_Array['data']['clink_creationID'],$attachment_id,'attachment');
				// Save Post ID
				wpclink_add_post_id_linked($attachment_id,$post_id_old,$link_site_url);
			}
			
			
			// Encrypted
			if ( !empty( $resposne_Array[ 'data' ][ 'creation_access_key' ] ) ) {
				// Encrypt
				update_post_meta( $attachment_id, 'wpclink_creation_access_key', $resposne_Array[ 'data' ][ 'creation_access_key' ] );
			}
		} 
	}
	// It is reused
	update_post_meta($post_id, 'wpclink_registration_disallow', '0');
			  }else{
		 
		  }
	}
	
	if($get_right_transaction_ID  = get_post_meta( $attachment_id, 'wpclink_right_ID', true )){
		$xmp_metadata_array['webstatement'] = WPCLINK_ID_URL.'/#objects/'.$get_right_transaction_ID;		
	}
	
	
	if($get_creation_ID  = get_post_meta( $attachment_id, 'wpclink_creation_ID', true )){
		
		$xmp_metadata_array['linked_creation_ID'] = WPCLINK_ID_URL.'/#objects/'.$get_creation_ID;	
		$xmp_metadata_array['registry_item_ID'] = WPCLINK_ID_URL.'/#objects/'.$get_creation_ID;
		$licensee_display_name = $creator_user_info->first_name .' '.$creator_user_info->last_name;
		$xmp_metadata_array['licensee_display_name'] = $licensee_display_name;
		
	
	}
	
	 // Updating Embeded Metadata
  	wpclink_update_option('wpclink_loader_status_linked','updatingembeded');
	
	try{
		// Write
		wpclink_metadata_writter($attachment_path,$xmp_metadata_array,$post_id);
	}catch (Exception $e){
		$error_exif['message'] = $e->getMessage();
		wpclink_update_option(  'wpclink_loader_status_linked', 'error' );
		wpclink_update_option(  'wpclink_loader_linked_error_data', $error_exif );
		
		if( wp_delete_attachment( $post_id, true )) {
			// Delete
		}
		
		return false;
	}
	
}	// Return Post ID
		
		
	// For fix the webstatement of rights
	update_post_meta( $post_id, 'wpclink_registration_disallow', '0' );
	return $result_update;
	
	}
}
/**
 * CLink Update Linked Post
 * 
 * 
 * @return integer post id
 */
function wpclink_update_post(){
		
$cl_options = wpclink_get_option( 'preferences_general' );
// Site URL
$link_site_url = apply_filters('cl_site_link',$cl_options['sync_site']);
// Validate
if(!isset($_GET['p_sync']) and $_GET['action'] != 'sync_update' and !isset($_GET['update_to'])) return false;	
// ONLY POST
if($_GET['cl_type'] != 'post') return false;
/* ====== PREPARING REQUEST ======= */
$request_query = array();
$request_query['post__in'] = $_GET['p_sync'];
$request_query['get_type'] = 'content';
$request_query['post_type'] = 'c-post';
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
$origin_data = array();
$update_post_id = (int)$_GET['update_to'];
$build_query = build_query( $request_query );
/* ====== QUERY REQUEST ======= */
$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
$xml = simplexml_load_string($xml);
$result_update = false;
foreach($xml->channel->item as $single){ 
/* ===== PREPARING DATA ===== */
// Post ID
$post_id_old = (int)$single->post_id; 
// Post Title
$post_title = (string)$single->title;
// Post Link
$post_link = (string)$single->link;
// Post Author
$ns_dc = $single->children('http://purl.org/dc/elements/1.1/');
$post_author = $ns_dc->creator_id;
// Post Content 
$post_content = (string)$single->children("content", true);
// Post Excerpt 
$post_excerpt = (string)$single->children("excerpt", true);
// Image URL		  
$image_url = (string)$single->children('wp', true)->post_thumbnail->children()->post_thumbnail_url;
// Modified Time
$modified_time = (string)$single->modified_time;
/* ===== GET CATEGORY NAME ==== */
$assign_cat = array();
foreach($single->category as $single_cat){
$category_arr = $single_cat->attributes();
if($category_arr["domain"] == 'category'){
	$assign_cat[] = (string)$single_cat;
}
}
/* ====== INSERT POST ======= */
// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;
	
	// Setup the author, slug, and title for the post
	
	$selected_author = wpclink_get_option('post_author_assign');
	if(empty($selected_author) or $selected_author == false){
		$author_id = get_current_user_id();
	}else{
		$author_id = $selected_author;
	}
	
	$time = current_time( 'Y-m-d', true );
	$origin_data = array();
	$found_id = array();
	
	
	
	
	$selected_cat = wpclink_get_option('post_cat_assign');
	if((empty($selected_cat)) or ($selected_cat == false) or ($selected_cat == '-1')){
		
	foreach($assign_cat as $cat_single){
	
		$term = term_exists($cat_single, 'category');
		if ($term !== 0 && $term !== null) {
			$found_id[]= $term['term_id'];
		}else{
			$found_id[] = wp_create_category( $cat_single, '0' );
		}
	
	}
		
	}else{
		$found_id = array($selected_cat);
	}
	
	// If the page doesn't already exist, then create it
	//if( null == get_page_by_title( $post_title ) ) 
		// Set the post ID so that we know the post was created successfully
		if($post_id = wp_update_post(
			array(
				'ID'				=> $update_post_id,
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_title'		=>	$post_title,
				'post_status'		=>	'publish',
				'post_content'  => $post_content,
				'post_excerpt' => $post_excerpt,
				'post_type'		=>	'post',
				'post_category' => $found_id
				
			),true
		)){
		  // Origin
		  $time = current_time( 'Y-m-d', true );
		  
		  $origin_data[$link_site_url] = array('origin_id' => $post_id_old, 'last_sync' => $time, 'sync_status' => true,'canonical' =>	$post_link);
		  
		  
		  // CLEAN
		  delete_post_meta($update_post_id, 'wpclink_referent_post_link');
		  
		  // Update post
		  if($updated_post_meta = update_post_meta($update_post_id, 'wpclink_referent_post_link', $origin_data)){	  
		  $result_update = $updated_post_meta;
		  $updated_post_id = $updated_post_meta;
		  
		  
		  if(!empty($image_url)){
		  // Set Image
		  wpclink_post_thumbnail_generator($post_id,$image_url);
		  }
		  	 
				  
		  
		  $tags_catch = array();
		  $tag_on = false;
  		  // Get All Tags
		  foreach($single->category as $single_tag){
		  
			$tags_arr = $single_tag->attributes();
			if($tags_arr["domain"] == 'post_tag'){	
				$tags_catch[] = (string)$single_tag;
				$tag_on = true;	
			}
		  }
		 
		  
		  foreach($tags_catch as $tag_single){
	
		  $term_tag = term_exists($tag_single, 'post_tag');
		  if ($term_tag !== 0 && $term_tag !== null) {
			  
			  // Assign
			  $term_id = $term_tag['term_id'];
			  $term = get_term( $term_id, 'post_tag' );
			  wp_set_post_tags( $post_id, $term->name, true );
			  
		  }else{
			  wp_set_post_tags( $post_id, $tag_single, true );
		  }
	
		}
		  	
			  }else{
		  }
	}
	
}	// Return Post ID
	return $result_update;
}
/**
 * CLink Remove Meta Boxes on the Linked Content
 * 
 */
function wpclink_remove_meta_box_linked_content() {
	
	
	
	if(isset($_GET['post'])){
		
	// post id
	$post_id = $_GET['post'];
	
		// Linked
		if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
			
			$taxonomy_permission = get_post_meta( $post_id, 'wpclink_programmatic_right_categories', true );
		
			// For Posts
			// Modity Taxonomy
			if($taxonomy_permission != 'ModifyTaxonomy'){
				remove_meta_box( 'categorydiv', 'post', 'normal' );
				remove_meta_box( 'tagsdiv-post_tag', 'post', 'normal' );
			}
			remove_meta_box( 'slugdiv', 'post', 'normal' );
			remove_meta_box( 'authordiv', 'post', 'normal' );
			remove_meta_box( 'postcustom', 'post', 'normal' );
			remove_meta_box( 'postimagediv', 'post', 'normal' );
			remove_meta_box( 'revisionsdiv', 'page', 'normal' );
			remove_meta_box( 'revisionsdiv', 'post', 'normal' );
			
			// For Pages
			remove_meta_box( 'postcustom', 'page', 'normal' );
			remove_meta_box( 'slugdiv', 'page', 'normal' );
			remove_meta_box( 'postcustom' , 'page' , 'normal' ); 
			
		}
	}
	 
	 
}
// Register the clink remove meta boxes function 
add_action( 'admin_menu', 'wpclink_remove_meta_box_linked_content' );
/**
 * CLink Remove Featured Image Meta Box on Linked Content
 * 
 */
function wpclink_remove_featured_image_meta_box_linked_content() {
	
	if(isset($_GET['post'])){
		
	// post id
	$post_id = $_GET['post'];
	
		// Linked
		if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
			
			remove_meta_box( 'postimagediv','post','side' );
			remove_meta_box( 'postimagediv','page','side' );
			
		}
	}
	
}
add_action( 'do_meta_boxes', 'wpclink_remove_featured_image_meta_box_linked_content' );
/**
 * CLink Remove TinyMce Features on the Linked Content
 * 
 * @param array $settings settings of the tinymce
 * @param integer $editor_id id of the tinymce editor
 * 
 * @return array
 */
function wpclink_remove_wp_tinymce_linked_content( $settings, $editor_id ) {
	
	// Only Post and Pages
    if ( $editor_id === 'content' && get_current_screen()->post_type === 'post' ||
		 $editor_id === 'content' && get_current_screen()->post_type === 'page'
	 ) {
		 
		 if(isset($_GET['post'])){
			 if(is_numeric($_GET['post'])){
		 
			 // Post ID
			$post_id = $_GET['post'];
		
			// Only Linked Content
			if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
			 
			 
			$settings['tinymce']   = false;
			$settings['quicktags'] = false;
			$settings['media_buttons'] = false;
		
			}
		}
		
		}
    }
    return $settings;
}
// Register clink remove tinymce function
add_filter( 'wp_editor_settings', 'wpclink_remove_wp_tinymce_linked_content', 10, 2 );
/**
 * CLink Disable Editing on the Referent Linked Contents and Remove Meta Boxes
 * 
 */
function wpclink_disable_editing_referent_creation_advanced_panel(){
	// Only Post and Pages
		 
		 if(isset($_GET['post'])){
			 if(is_numeric($_GET['post'])){
		 
				 // Post ID
				$post_id = $_GET['post'];
				
				if(wpclink_check_license_by_post_id($post_id) > 0){
					
					remove_meta_box( 'slugdiv', 'post', 'normal' );
					remove_meta_box( 'slugdiv', 'page', 'normal' );
					remove_meta_box( 'authordiv', 'post', 'normal' );
					remove_meta_box( 'authordiv', 'page', 'normal' );
					remove_meta_box( 'postcustom' , 'post' , 'normal' ); 
					remove_meta_box( 'postcustom' , 'page' , 'normal' ); 
					
				}
		  	
	}  	}
}
// Register clink disable editing on referent linked contents and remove meta boxes function
add_action( 'admin_menu', 'wpclink_disable_editing_referent_creation_advanced_panel' );
