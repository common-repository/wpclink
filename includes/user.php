<?php
/**
 * CLink User Functions 
 *
 * CLink user and profile functions
 *
 * @package CLink
 * @subpackage Link Manager
 */
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Creator User Update on Care
 * 
 * @param intger $user_id user id
 * 
 */
function wpclink_creator_user_update($user_id, $make_version = false){
		
		
	// PARTY ID
	$party_user_id = wpclink_get_option('authorized_contact');
	
	
	$submitted = $_POST;
	
	
	if($associated_user = get_user_meta($party_user_id,'wpclink_party_ID',true)){
		$ass_array = $associated_user;
	}else{
		$ass_array = '';
	}
	
	
	/* ____________ CLINK MEDIA _______________ */
	/* _____________ VARIABLES __________________*/
	
	$creator_user = get_userdata( $user_id ); 
	$site_address = get_bloginfo('url');
	$first_name = get_user_meta($user_id,'first_name',true);
	$nick_name = get_user_meta($user_id,'nickname',true);
	$last_name = get_user_meta($user_id,'last_name',true);
	$description = get_user_meta($user_id,'description',true);
	$CreatorID = get_user_meta($user_id,'wpclink_party_ID',true);
	$party_access_key = get_user_meta($user_id,'wpclink_party_access_key',true);
	
	// ORCID
	if($wpclink_orcid = get_user_meta($user_id,'wpclink_orcid',true)){	
	}else{
		$wpclink_orcid ='';
	}
	
	// PLUSID
	if($wpclink_plusid = get_user_meta($user_id,'wpclink_plusid',true)){	
	}else{
		$wpclink_plusid ='';
	}
    
    // DID
	if($wpclink_did = get_user_meta($user_id,'wpclink_did',true)){
	}else{
		$wpclink_did ='';
	}
    
    
	// ISNI
	if($wpclink_isni = get_user_meta($user_id,'wpclink_isni',true)){
	}else{
		$wpclink_isni ='';
	}
	
	// Teriority
	$territory = wpclink_get_option('territory_code');
	
	$creator_display_email = get_user_meta($user_id,'wpclink_email_display_status',true);
	if($creator_display_email == 'yes'){
		$creator_display_email = '1';
	}
	
	
	$domain_access_key = wpclink_get_option('domain_access_key');
	
	
	/*____URL____*/
	
	$url_creator = WPCLINK_CREATOR_API;
	
	if($make_version == true){
		$action = 'version';
	}else{
		$action = 'profile';
	}
	
	
	$response = wp_remote_post(
                $url_creator,
                array(
                    'body' => array(
						'creator_identifier' => $CreatorID,
						'party_access_key' => $party_access_key,
						'domain_access_key' => $domain_access_key,
                        'site_address'  => urlencode($site_address),
						'party_webaddress'=> urlencode($creator_user->user_url),
                        'creator_name'	=> $first_name,
						'creator_lastname' => $last_name,
						'creator_display_name' => $creator_user->display_name,
                        'creator_email' => $creator_user->user_email,
						'party_territory' => strtoupper($territory),
						'creator_nickname' => $nick_name,
						'party_description' => $description,
						'creator_url' => $creator_user->user_url,
						'clink_site_domain' => get_bloginfo('url'),
						'display_email' => $creator_display_email,
						'isni' => $wpclink_isni,
						'orcid' => $wpclink_orcid,
						'plusid' => $wpclink_plusid,
                        'did' => $wpclink_did,
						'creator_associated_party'=> $ass_array,
						'creator_alternative_identifier_type' => 'DOI',
						'creator_alternative_identifier' => array('XYZ'),
						'action'=> $action
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
						
			
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
			   
			   // Response Debug
				wpclink_debug_log('CREATOR USER PROFILE UPDATE '.print_r($response,true));
			  
 
            }else {
				
				// Response Debug
				wpclink_debug_log('CREATOR USER PROFILE UPDATE '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   			   
			   
			   
			  
			   
			   
			  
	if($resposne_Array['status'] == 'profile'){
				   
	$creator_array = wpclink_get_option('authorized_creators');
	
	
	// User Identifier
	update_user_meta($user_id,'wpclink_party_ID',$resposne_Array['data']['creator_identifier']);
	
	}elseif($resposne_Array['status'] == 'version'){
		
		if($creator_verions = get_user_meta($user_id,'wpclink_versions',true)){
			
			// Version
			$version_id = $resposne_Array['data']['id'];
			// Merge
			$update_verion = array_merge($creator_verions,array( $version_id => array( 'id' => $version_id, 
																					   'time' => current_time( 'timestamp', 1 )) ));
			// Update
			update_user_meta($user_id,'wpclink_versions',$update_verion);
			
		}else{
			
			// Version
			$version_id = $resposne_Array['data']['id'];
			// Update
			update_user_meta($user_id,'wpclink_versions',array( $version_id => array( 'id' => $version_id, 
																					  'time' => current_time( 'timestamp', 1 )) ));
			
		}
		
	}
		}
	
}
/**
 * CLink Party User Update on CLink.ID
 * 
 * @param integer $user_id user id
 * 
 */
function wpclink_party_user_update($user_id,$make_version = false){
	
		
	// PARTY ID
	$party_user_id = wpclink_get_option('authorized_contact');
	$get_clink_display_email = get_user_meta($user_id,'wpclink_email_display_status',true);
	
	if($get_clink_display_email == 'yes'){
		$get_clink_display_email = '1';
	}
	
	
	$creator_array = wpclink_get_option('authorized_creators');
	
	
	
	if($party_user_id > 0){
		if($associated_user = get_user_meta($party_user_id,'wpclink_party_ID',true)){	
		}else{
			$associated_user = '';
		}
	}else{
		$associated_user = '';
	}
	
	
	/* ____________ CLINK MEDIA _______________ */	
	/* _____________ VARIABLES __________________*/
	$creator_user = get_userdata( $party_user_id ); 
	$first_name = get_user_meta($party_user_id,'first_name',true);
	$nick_name = get_user_meta($party_user_id,'nickname',true);
	$last_name = get_user_meta($party_user_id,'last_name',true);
	$description = get_user_meta($party_user_id,'description',true);
	
	// Teriority
	$territory = wpclink_get_option('territory_code');
	
	// ORCID
	if($wpclink_orcid = get_user_meta($party_user_id,'wpclink_orcid',true)){	
	}else{
		$wpclink_orcid ='';
	}
	
	// PLUSID
	if($wpclink_plusid = get_user_meta($party_user_id,'wpclink_plusid',true)){	
	}else{
		$wpclink_plusid ='';
	}
    
    // DID
	if($wpclink_did = get_user_meta($party_user_id,'wpclink_did',true)){
	}else{
		$wpclink_did ='';
	}
	
    // ISNI
	if($wpclink_isni = get_user_meta($party_user_id,'wpclink_isni',true)){
	}else{
		$wpclink_isni ='';
	}
		
	$party_info = get_userdata($user_id);
	$site_address = get_bloginfo('url');	
	
	$party_identifier = get_user_meta($party_user_id,'wpclink_party_ID',true);
	$url_party = WPCLINK_PARTY_API;
	
	if($make_version == true){
		$action = 'version';
	}else{
		$action = 'profile';
	}
	
	// PARTY TO CUSTOMER ID
	$current_party_id = wpclink_get_option('authorized_contact');
	$party_access_key = get_user_meta($current_party_id,'wpclink_party_access_key',true);
	
	
	$response = wp_remote_post(
                $url_party,
                array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'party_name'	=> $party_info->display_name,
						'party_webaddress'=> urlencode($party_info->user_url),
						'party_first_name' => $first_name,
						'party_last_name' => $last_name,
						'party_description' => $description,
						'party_identifier' => $party_identifier,
						'party_structure_type' => 'Individual', // Check
						'party_email' => $creator_user->user_email,
                        'party_url' => get_bloginfo('url'),
						
						'party_territory' => strtoupper($territory),
						'party_nickname' => $nick_name,
						'clink_site_domain' => get_bloginfo('url'),
						'party_associated_user' => $associated_user,
						'party_alternative_identifier_type' => 'DOI',
						'party_alternative_identifier' => '',
						'party_modification_date' => get_user_meta($party_user_id, 'wpclink_party_modification_date',true),
						'party_access_key' => $party_access_key,
						'domain_access_key' => wpclink_get_option('domain_access_key'),
						'display_email' => $get_clink_display_email,
						'isni' => $wpclink_isni,
						'orcid' => $wpclink_orcid,
						'plusid' => $wpclink_plusid,
                        'did' => $wpclink_did,
						'action' => $action
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
					
	
			
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
			   
			   // Response Debug
				wpclink_debug_log(print_r('PARTY USER PROFILE UPDATE '.$response,true));
			  
 
            }else {
				
			   $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
				
			 // Response Debug
				wpclink_debug_log(print_r('PARTY USER PROFILE UPDATE '.$resposne_Array,true));
 
              
			   			  			   
			   
			   if($resposne_Array['status'] == 'profile'){
			   
			   	update_user_meta($party_user_id,'wpclink_party_ID',$resposne_Array['data']['clink_party_identifier']);
				
				
				
				// EXTRA
				update_user_meta($party_user_id,'wpclink_party_ID',$resposne_Array['data']['clink_party_identifier']);
				
				
				$class = 'notice notice-success';
				$message = __( 'Party new is update succesfully', 'clink_text' );
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
			   
			   }elseif($resposne_Array['status'] == 'version'){
		
				if($party_verions = get_user_meta($user_id,'wpclink_versions',true)){
					// Version
					$version_id = $resposne_Array['data']['id'];
					// Merge
					$update_verion = array_merge($party_verions,array( $version_id => array( 'id' => $version_id, 
																					   'time' => current_time( 'timestamp', 1 )) ));
					// Update
					update_user_meta($user_id,'wpclink_versions',$update_verion);
				}else{
					// Version
					$version_id = $resposne_Array['data']['id'];
					// Update
					update_user_meta($user_id,'wpclink_versions',array( $version_id => array( 'id' => $version_id, 
																					   'time' => current_time( 'timestamp', 1 )) ));
				}
		
	}
			  
			   
			   
			}
}