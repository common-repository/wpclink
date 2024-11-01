<?php
/**
 * CLink Process Functions 
 *
 * CLink process of creator and party functions
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Quick Update User Creator for Care
 * 
 * @param intger $user_id  user id
 * @param integer $user_id_before user id before it was
 * 
 */
function wpclink_quick_update_creator($user_id = 0, $user_id_before = 0){
	
	$submitted = $_POST;
	$current_user_id = get_current_user_id();
		
	// PARTY ID
	$party_user_id = wpclink_get_option('authorized_contact');
	
		// Admin Page
	$return_admin_page = menu_page_url( 'cl_users', false );

	
	$before_creator_identifier = get_user_meta($user_id_before,'wpclink_party_ID',true);
	
		
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
	
	/*____URL____*/
	
	$url_creator = WPCLINK_CREATOR_API;
	
		
	// PARTY TO CUSTOMER ID
	$current_party_id = wpclink_get_option('authorized_contact');
	$party_access_key = get_user_meta($user_id,'wpclink_party_access_key',true);
	
	// Previous Identifier
	$previous_creator_user = get_userdata( $user_id_before ); 
	$previous_creator_name = $previous_creator_user->first_name;
	$previous_creator_displayname = $previous_creator_user->display_name;
	$previous_creator_lastname = $previous_creator_user->last_name;
	$previous_creator_email = $previous_creator_user->user_email;
	$previous_creator_url = $previous_creator_user->user_url;
	$previous_creator_identifier = get_user_meta($user_id_before,'wpclink_party_ID',true);
	
	
	// Teriority
	$territory = wpclink_get_option('territory_code');
	
	
	
	// Creator ID
	$creator_ID = get_user_meta($user_id,'wpclink_party_ID',true);
	
	
	if(!empty($creator_ID)){
		$action = 'update';
	}else{
		$action = 'update';		
	}
	
	
	$create_data = array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'creator_name'	=> $first_name,
						'creator_lastname' => $last_name,
						'creator_display_name' => $creator_user->display_name,
                        'creator_email' => $creator_user->user_email,
						'clink_site_domain' => get_bloginfo('url'),
						'party_nickname' => $nick_name,
						'party_description' => $description,
						'party_webaddress' => $creator_user->user_url,
						'creator_url' => $creator_user->user_url,
						'creator_identifier' => $creator_ID,
						'previous_creator_identifier' => $previous_creator_identifier,
						'previous_creator_email' => urlencode($previous_creator_email),
						'previous_creator_url' => $previous_creator_url,
						'previous_creator_lastname' => $previous_creator_lastname,
						'previous_creator_name' => $previous_creator_name,
						'previous_creator_displayname' => $previous_creator_displayname,
						'party_access_key' => $party_access_key,
						'party_territory' => strtoupper($territory),
						'creator_associated_party'=> $ass_array,
						'creator_alternative_identifier_type' => 'DOI',
						'creator_alternative_identifier' => array('XYZ'),
						'clink_edition' => 'personal',
						'return_page' => $return_admin_page,
						'action'=> $action
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
	
	
	$response = wp_remote_post(
                $url_creator,
                $create_data
            );
								
			
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
			   
				// Response Debug
				wpclink_debug_log('UPDATE CREATOR'.print_r($response,true));
			   
			  
 
            }else {
				
				// Response Debug
				wpclink_debug_log('UPDATE CREATOR'.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   			   
			   
			  
	if($resposne_Array['status'] == 'update'){
		
			
	// Update Creator List
	wpclink_update_creator_list($user_id);
	
	
	// User Identifier
	update_user_meta($user_id,'wpclink_party_ID',$resposne_Array['data']['creator_identifier']);
	
	
	// CUSTOMER KEYS
	if(empty($resposne_Array['data']['new_party_access_key'])){
		update_user_meta($user_id,'wpclink_party_access_key',$resposne_Array['data']['party_access_key']);
	}else{
		update_user_meta($user_id,'wpclink_party_access_key',$resposne_Array['data']['new_party_access_key']);
	}
	
	
	update_user_meta($user_id,'wpclink_user_status_creator','saved');
	update_user_meta($user_id,'wpclink_terms_accept',2);
	
	
	// #CREATOR_CREATE_NOTICE
	if($_GET['page'] != 'cl_users'){
					
		if($current_user_id == $user_id){
			wpclink_notif_print('You have been selected for CLink Creator role','success');
		}else{
			$user_data = get_userdata($user_id);
			wpclink_notif_print($user_data->display_name.' been selected for CLink Creator role','success');
		}
	}
	
	
	
	// ALREADY
	}elseif($resposne_Array['status'] == 'already'){
		
		$creator_flag = 'already';
		$already_clink_id = $resposne_Array['data']['creator_clinkID'];
		$already_domain = $resposne_Array['data']['creator_domain'];
		
	}elseif($resposne_Array['status'] == 'already_another'){
		
		$creator_flag = 'already_another';
		$already_clink_id_another = $resposne_Array['data']['creator_clinkID'];
		$already_domain_another = $resposne_Array['data']['another_domain'];
		$another_displayname = $resposne_Array['data']['another_displayname'];
		$clinkid_all_domains = $resposne_Array['data']['clinkid_all_domains'];
		$clinkid_found = $resposne_Array['data']['clinkid_found'];
		
		
	}elseif($resposne_Array['status'] == 'create'){
	
	// Update Creator List
	wpclink_update_creator_list($user_id);
	
	$submitted_user_id = $user_id;
	
	
	// User Identifier
	update_user_meta($submitted_user_id,'wpclink_party_ID',$resposne_Array['data']['creator_identifier']);
	
	
		
	// CUSTOMER KEYS
	if(empty($resposne_Array['data']['new_party_access_key'])){
		update_user_meta($user_id,'wpclink_party_access_key',$resposne_Array['data']['party_access_key']);
	}else{
		update_user_meta($user_id,'wpclink_party_access_key',$resposne_Array['data']['new_party_access_key']);
	}
	
	
	
	
	update_user_meta($submitted_user_id,'wpclink_user_status_creator','saved');
	update_user_meta($submitted_user_id,'wpclink_terms_accept',2);
	}elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to <br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
				   
			   }
		}
		
}
/**
 * CLink Quick Create Creator for Care
 * 
 * @param integer $user_id  user id
 * @param integer $user_id_before  user id before it was
 * 
 */
function wpclink_quick_create_creator($user_id = 0, $user_id_before = 0){
	
	// Submitted
	$current_user_id = get_current_user_id();
	
	$submitted = $_POST;
	$submitted_user_id = $user_id;
	
	
		
	// PARTY ID
	$party_user_id = wpclink_get_option('authorized_contact');
	
		// Admin Page
	$return_admin_page = menu_page_url( 'cl_users', false );

		
	if($associated_user = get_user_meta($party_user_id,'wpclink_party_ID',true)){
		$ass_array = $associated_user;
	}else{
		$ass_array = '';
	}
	
	
	/* ____________ CLINK MEDIA _______________ */
	
	/* _____________ VARIABLES __________________*/
	
	$creator_user = get_userdata( $submitted_user_id ); 
	$site_address = get_bloginfo('url');
	$first_name = get_user_meta($submitted_user_id,'first_name',true);
	$nick_name = get_user_meta($submitted_user_id,'nickname',true);
	$last_name = get_user_meta($submitted_user_id,'last_name',true);
	$description = get_user_meta($submitted_user_id,'description',true);
	$display_email_creator = get_user_meta($submitted_user_id,'wpclink_display_email',true);
	$display_email_party = 0;
	
	
	// DOMAIN ID
	$domain_access_key = wpclink_get_option('domain_access_key');
	
	
	// PARTY TO CUSTOMER ID
	$current_party_id = wpclink_get_option('authorized_contact');
	$party_access_key = get_user_meta($current_party_id,'wpclink_party_access_key',true);
	
	
	// Teriority
	$territory = wpclink_get_option('territory_code');
	
	
		// Previous Identifier
	$previous_creator_user = get_userdata( $user_id_before ); 
	$previous_creator_name = $previous_creator_user-> first_name;
	$previous_creator_displayname = $previous_creator_user->display_name;
	$previous_creator_lastname = $previous_creator_user->last_name;
	$previous_creator_email = $previous_creator_user->user_email;
	$previous_creator_identifier = get_user_meta($user_id_before,'wpclink_party_ID',true);
	$previous_creator_url = $previous_creator_user->user_url;
	
	/*____URL____*/
	
	$url_creator = WPCLINK_CREATOR_API;
	
	$response = wp_remote_post(
                $url_creator,
                array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'creator_name'	=> $first_name,
						'creator_lastname' => $last_name,
						'creator_display_name' => $creator_user->display_name,
                        'creator_email' => $creator_user->user_email,
						'clink_site_domain' => get_bloginfo('url'),
						'previous_creator_identifier' => $previous_creator_identifier,
						'previous_creator_url' => $previous_creator_url,
						'previous_creator_name' => $previous_creator_name,
						'previous_creator_displayname' => $previous_creator_displayname,
						'previous_creator_lastname' => $previous_creator_lastname,
						'previous_creator_email' => urlencode($previous_creator_email),
						'party_nickname' => $nick_name,
						'party_description' => $description,
						'party_webaddress' => $creator_user->user_url,
						'creator_url' => $creator_user->user_url,
						'creator_associated_party'=> $ass_array,
						'creator_alternative_identifier_type' => 'DOI',
						'creator_alternative_identifier' => array('XYZ'),
						'party_territory' => strtoupper($territory),
						'domain_access_key' => $domain_access_key,
						'party_ID' => $party_access_key,
						'clink_edition' => 'personal',
						'return_page' => $return_admin_page,
						'action'=> 'create'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
						
			
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
				
				// Response Debug
				wpclink_debug_log('CREATE CREATOR '.print_r($response,true));
			   
			  		  
 
            }else {
				
				// Response Debug
				wpclink_debug_log('CREATE CREATOR '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   			   
			   
			   
			   
			   
	// CREATE  
	if($resposne_Array['status'] == 'create'){
	
		
			
	// Update Creator List
	wpclink_update_creator_list($submitted_user_id);
	
	
	// User Identifier
	update_user_meta($submitted_user_id,'wpclink_party_ID',$resposne_Array['data']['creator_identifier']);
	
			
	// CUSTOMER KEYS
	if(empty($resposne_Array['data']['new_party_access_key'])){
		update_user_meta($submitted_user_id,'wpclink_party_access_key',$resposne_Array['data']['party_access_key']);
	}else{
		update_user_meta($submitted_user_id,'wpclink_party_access_key',$resposne_Array['data']['new_party_access_key']);
	}
	
		
	update_user_meta($submitted_user_id,'wpclink_user_status_creator','saved');
	update_user_meta($submitted_user_id,'wpclink_terms_accept',2);
	
	
	// #CREATOR_CREATE_NOTICE
	if($_GET['page'] != 'cl_users'){
					
		if($current_user_id == $submitted_user_id){
			wpclink_notif_print('You have been selected for CLink Creator role','success');
		}else{
			$user_data = get_userdata($submitted_user_id);
			wpclink_notif_print($user_data->display_name.' been selected for CLink Creator role','success');
		}
	}
		
	// ALREADY
	}elseif($resposne_Array['status'] == 'already'){
		
		$creator_flag = 'already';
		$already_clink_id = $resposne_Array['data']['creator_clinkID'];
		$already_domain = $resposne_Array['data']['creator_domain'];
		
	}elseif($resposne_Array['status'] == 'already_another'){
		
		$creator_flag = 'already_another';
		$already_clink_id_another = $resposne_Array['data']['creator_clinkID'];
		$already_domain_another = $resposne_Array['data']['another_domain'];
		$another_displayname = $resposne_Array['data']['another_displayname'];
		$clinkid_all_domains = $resposne_Array['data']['clinkid_all_domains'];
		$clinkid_found = $resposne_Array['data']['clinkid_found'];
		
		
	}elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to <br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
				   
			   }
	}
			
}
/**
 * CLink Quick Update Party on Care
 * 
 * @param integer $user_id  user id
 * @param integer $previous_party_id  previous user id was
 * 
 */
function wpclink_quick_update_party($user_id = 0, $previous_party_id = 0){
	
		
	// PARTY ID
	$current_user_id = get_current_user_id();
	
		// Admin Page
	$return_admin_page = menu_page_url( 'cl_users', false );

	
	
	$party_user_id = $user_id;
	wpclink_update_option('authorized_contact',$party_user_id);
	
	
	
	// Update Checkbox
	$display_email = $submitted['display_email'];
	
	
	$get_clink_display_email = 0;
		
	if($get_clink_display_email == '1'){
		$clink_display_email = 'yes';
	}else{
		$clink_display_email = 'no';
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
	
	/* _____________ VARIABLES __________________*/
	$creator_user = get_userdata( $party_user_id ); 
	$first_name = get_user_meta($party_user_id,'first_name',true);
	$nick_name = get_user_meta($party_user_id,'nickname',true);
	$last_name = get_user_meta($party_user_id,'last_name',true);
	$description = get_user_meta($party_user_id,'description',true);
	$display_email_creator = get_user_meta($party_user_id,'wpclink_display_email',true);
		
	// Teriority
	$territory = wpclink_get_option('territory_code');
	
	$party_info = get_userdata($party_user_id);
	
		
	$site_address = get_bloginfo('url');	
	
	// Current
	$party_identifier = get_user_meta($party_user_id,'wpclink_party_ID',true);	
	// Previous
	$previous_party_identifier = get_user_meta($previous_party_id,'wpclink_party_ID',true);
	$previous_user_data = get_userdata( $previous_party_id ); 
	$previous_party_name = $previous_user_data->display_name;
	$previous_url = $previous_user_data->user_url;
	$previous_party_contact_id = get_user_meta($previous_party_id, 'wpclink_party_access_key', true);
	
	
	$url_party = WPCLINK_PARTY_API;
	
	
	// Same Domain
	$same_domain_data = get_user_meta($party_user_id,'wpclink_user_approve_domain_status',true);
	
	if($same_domain_data == '1'){
		$same_domain = '1';
		// debug
		//echo '<h1>Same Domain 1</h1>';
	}else{
		$same_domain = '0';
	}
	
		
	// PREVIOUS PARTY ID
	$party_access_key = get_user_meta($party_user_id,'wpclink_party_access_key',true);
	
	$request_send = array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'party_name'	=> $party_info->display_name,
						'party_first_name' => $first_name,
						'party_last_name' => $last_name,
						'party_description' => $description,
						'party_nickname' => $nick_name,
						'party_identifier' => $party_identifier,
						'previous_party_identifier' => $previous_party_identifier,
						'previous_party_name' => $previous_party_name,
						'previous_party_url' => $previous_url,
						'previous_party_contact_id' => $previous_party_contact_id,
						'party_structure_type' => 'personal', // Check
						'party_email' => $creator_user->user_email,
                        'party_url' => $creator_user->user_url,
						'party_webaddress' => $creator_user->user_url,
						'party_territory' => strtoupper($territory),
						'clink_site_domain' => get_bloginfo('url'),
						'party_associated_user' => $associated_user,
						'party_alternative_identifier_type' => 'DOI',
						'party_alternative_identifier' => '',
						'party_modification_date' => get_user_meta($party_user_id, 'wpclink_party_modification_date',true),
						'party_access_key' => $party_access_key,
						'domain_access_key' => wpclink_get_option('domain_access_key'),
						'party_display_email' => $clink_display_email,
						'same_domain' => $same_domain,
						'clink_edition' => 'personal',
						'return_page' => $return_admin_page,						
						'action' => 'update'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
	
	$response = wp_remote_post(
                $url_party,
                $request_send
            );
	
			
	 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
				
				// Response Debug
				wpclink_debug_log('UPDATE PARTY '.print_r($response,true));
			   
	
            }else {
				
				// Response Debug
				wpclink_debug_log('UPDATE PARTY '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   			   
			   echo $resposne_Array['status'];
			   echo $resposne_Array['data']['creator_display_name'];
			   echo $resposne_Array['data']['creator_identifier'];
			   echo $resposne_Array['data']['client_site'];
			   
			   
			   if($resposne_Array['status'] == 'update'){
			   
			   	update_user_meta($party_user_id,'wpclink_party_ID',$resposne_Array['data']['clink_party_identifier']);
				update_user_meta($party_user_id,'wpclink_party_creation_date',$resposne_Array['data']['clink_party_creation_date']);
				update_user_meta($party_user_id,'wpclink_party_modification_date',$resposne_Array['data']['clink_party_modification_date']);
				
				$current_party_id = wpclink_get_option('authorized_contact');
				   
				// CUSTOMER KEYS
				if(empty($resposne_Array['data']['new_party_access_key'])){
					update_user_meta($party_user_id,'wpclink_party_access_key',$resposne_Array['data']['party_access_key']);
				}else{
					update_user_meta($party_user_id,'wpclink_party_access_key',$resposne_Array['data']['new_party_access_key']);
				}
				
				wpclink_update_option('domain_access_key',$resposne_Array['data']['domain_access_key']);
				update_user_meta($party_user_id,'wpclink_party_ID',$resposne_Array['data']['clink_party_identifier']);
				
				
				// #PARTY_CREATE_NOTICE
				if($_GET['page'] != 'cl_users'){
					
					if($current_user_id == $party_user_id){
						wpclink_notif_print('You have been selected for CLink Contact  role','success');
					}else{
						$user_data = get_userdata($party_user_id);
						
						wpclink_notif_print($user_data->display_name.' been selected for CLink Contact role','success');
					}
				}
				
				
							
				update_user_meta($party_user_id,'wpclink_user_status_contact','saved');
				update_user_meta($party_user_id,'wpclink_terms_accept',2);
				
			   
			   }elseif($resposne_Array['status'] == 'create'){
				   
				wpclink_update_option('authorized_contact',$party_user_id);
			   
			    update_user_meta($party_user_id,'wpclink_party_ID',$resposne_Array['data']['clink_party_identifier']);
				update_user_meta($party_user_id,'wpclink_party_creation_date',$resposne_Array['data']['clink_party_creation_date']);
				update_user_meta($party_user_id,'wpclink_party_modification_date',$resposne_Array['data']['clink_party_modification_date']);
				update_user_meta($party_user_id,'wpclink_party_ID',$resposne_Array['data']['clink_party_identifier']);
				
				$current_party_id = wpclink_get_option('authorized_contact');
				   
				// CUSTOMER KEYS
				if(empty($resposne_Array['data']['new_party_access_key'])){
					update_user_meta($party_user_id,'wpclink_party_access_key',$resposne_Array['data']['party_access_key']);
				}else{
					update_user_meta($party_user_id,'wpclink_party_access_key',$resposne_Array['data']['new_party_access_key']);
				}
					
				
				wpclink_update_option('domain_access_key',$resposne_Array['data']['domain_access_key']);
				
				// #PARTY_CREATE_NOTICE
				if($_GET['page'] != 'cl_users'){
					
					if($current_user_id == $party_user_id){
						wpclink_notif_print('You have been selected for CLink Contact role','success');
					}else{
						$user_data = get_userdata($party_user_id);
						
						wpclink_notif_print($user_data->display_name.' been selected for CLink Contact role','success');
					}
				}
				
				
			
				
				update_user_meta($party_user_id,'wpclink_user_status_contact','saved');
				
				update_user_meta($party_user_id,'wpclink_terms_accept',2);
			   
			   }elseif($resposne_Array['status'] == 'already_another'){
				
				// PARTY ON ANOTHER DOMAIN  
								 
				$party_already_clink_id = $resposne_Array['data']['party_clinkID'];
				$party_already_party_name = $resposne_Array['data']['party_name'];	
				   
				$party_flag = 'already_another'; ?>
                
				   <div class="notice notice-error">
        <p><?php _e('<strong>'.$party_already_party_name.'</strong> '.wpclink_add_hyperlink_to_clink_ID($party_already_clink_id).' already registered on another domain.','cl_text'); ?></p>
		<p><?php _e('Personal Edition is limited to be used for one domain','cl_text'); ?></p>
		</div>
			   <?php }elseif($resposne_Array['status'] == 'already'){
			   // PARTY ON SAME DOMAIN
				   
				   
				$party_flag = 'already';
				$party_already_clink_id = $resposne_Array['data']['party_clinkID'];
				$party_already_domain = $resposne_Array['data']['party_domain']; ?>
				
				<div class="notice notice-error">
                <p><?php _e('You may have lost your user settings in the WordPress CLink plugin','cl_text'); ?></p>
        <p><?php _e('Your Party ID: '.wpclink_add_hyperlink_to_clink_ID($party_already_clink_id).' and associated with domain '.$party_already_domain ,'cl_text'); ?></p>
        
        <p><?php _e('Please check your <a class="button" target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$party_already_clink_id.'">Registration</a>','cl_text'); ?></p>
         <p><?php _e('You can update the registry with the information you are about to send','cl_text'); ?></p>
                
				<p> <input class="button" type="submit" value="Update" name="party_already_update" onclick="cl_party_update()" /></p>
				</div>
				   
			   <?php }elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to <br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
				   
			   }
			  
			   
			   
			}
					
}
/**
 * CLink Quick Create Party on Care
 * 
 * @param integer $user_id user id 
 * 
 */
function wpclink_quick_create_party($user_id = 0){
		
	/* ========== CREATE PARTY =========== */
	
	$current_user_id = get_current_user_id();
	
	
		// Admin Page
	$return_admin_page = menu_page_url( 'cl_users', false );
	
	
	$party_user_id = $user_id;
	
		
	// GET ASSCOCIATED USER
	if($party_user_id > 0){
		if($associated_user = get_user_meta($party_user_id,'wpclink_party_ID',true)){	
		}else{
			$associated_user = '';
		}
	}else{
		$associated_user = '';
	}
	
	$party_name = '';
	$party_email = '';
	
	
	
	/* ____________ CLINK MEDIA _______________ */
	/* _____________ VARIABLES __________________*/
	$creator_user = get_userdata( $party_user_id ); 
	$site_address = get_bloginfo('url');
	$first_name = get_user_meta($party_user_id,'first_name',true);
	$nick_name = get_user_meta($party_user_id,'nickname',true);
	$last_name = get_user_meta($party_user_id,'last_name',true);
	$description = get_user_meta($party_user_id,'description',true);
	$display_email_creator = get_user_meta($party_user_id,'wpclink_display_email',true);
	
	// Teriority
	$territory = wpclink_get_option('territory_code');
	
	$party_info = get_userdata($party_user_id);
	
	
	
	if($get_clink_display_email == '1'){
		$clink_display_email = 'yes';
	}else{
		$clink_display_email = 'no';
	}
	
	
	$party_alternative_identifier = array();
	$party_alternative_identifier[] = 'XYZ';
	
	$url_party = WPCLINK_PARTY_API;
	
	
	$data_sent = array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'party_name'	=> $party_info->display_name,
						'party_first_name' => $first_name,
						'party_last_name' => $last_name,
						'party_description' => $description,
						'party_nickname' => $nick_name,
						'party_structure_type' => 'personal',
						'party_email' => $creator_user->user_email,
                        'party_url' => $creator_user->user_url,
						'party_webaddress' => $creator_user->user_url,
						'party_territory' => strtoupper($territory),
						'clink_site_domain' => get_bloginfo('url'),
						'party_associated_user' => $associated_user,
						'party_alternative_identifier_type' => 'DOI',
						'party_alternative_identifier' => '',
						'party_display_email' => $clink_display_email,
						'clink_edition' => 'personal',
						'return_page' => $return_admin_page,
						'action' => 'create'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
	
	
	$response = wp_remote_post(
                $url_party,$data_sent
                
            );
						
			
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
			   
				// Response Debug
				wpclink_debug_log('CREATE PARTY '.print_r($response,true));
			  
 
            }else {
				
				// Response Debug
				wpclink_debug_log('UPDATE PARTY '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   			   
			   echo $resposne_Array['status'];
			   echo $resposne_Array['data']['creator_display_name'];
			   echo $resposne_Array['data']['creator_identifier'];
			   echo $resposne_Array['data']['client_site'];
			   
			   
			   if($resposne_Array['status'] == 'create'){
				   
				wpclink_update_option('authorized_contact',$party_user_id);
			   
			    update_user_meta($party_user_id,'wpclink_party_ID',$resposne_Array['data']['clink_party_identifier']);
				update_user_meta($party_user_id,'wpclink_party_creation_date',$resposne_Array['data']['clink_party_creation_date']);
				update_user_meta($party_user_id,'wpclink_party_modification_date',$resposne_Array['data']['clink_party_modification_date']);
				update_user_meta($party_user_id,'wpclink_party_ID',$resposne_Array['data']['clink_party_identifier']);				
				
				$current_party_id = wpclink_get_option('authorized_contact');
				   
				// CUSTOMER KEYS
				if(empty($resposne_Array['data']['new_party_access_key'])){
					update_user_meta($party_user_id,'wpclink_party_access_key',$resposne_Array['data']['party_access_key']);
				}else{
					update_user_meta($party_user_id,'wpclink_party_access_key',$resposne_Array['data']['new_party_access_key']);
				}
				
				wpclink_update_option('domain_access_key',$resposne_Array['data']['domain_access_key']);
				
				update_user_meta($party_user_id,'wpclink_user_status_contact','saved');
				
				update_user_meta($party_user_id,'wpclink_terms_accept',2);
				
				
				// #PARTY_CREATE_NOTICE
				if($_GET['page'] != 'cl_users'){
					
					if($current_user_id == $party_user_id){
						wpclink_notif_print('You have been selected for CLink Contact role','success');
					}else{
						$user_data = get_userdata($party_user_id);
						
						wpclink_notif_print($user_data->display_name.' been selected for CLink Contact role','success');
					}
				}
				
				
			
				
			   
			   }elseif($resposne_Array['status'] == 'already_another'){
				// PARTY ON ANOTHER DOMAIN  
				 
				 
				$party_already_clink_id = $resposne_Array['data']['party_clinkID'];
				$party_already_party_name = $resposne_Array['data']['party_name'];	
				   
				$party_flag = 'already_another'; ?>
                
				   <div class="notice notice-error">
        <p><?php _e('<strong>'.$party_already_party_name.'</strong> '.wpclink_add_hyperlink_to_clink_ID($party_already_clink_id).' already registered on another domain.','cl_text'); ?></p>
		<p><?php _e('Personal Edition is limited to be used for one domain','cl_text'); ?></p>
		</div>
			   <?php }elseif($resposne_Array['status'] == 'already'){
			   // PARTY ON SAME DOMAIN
				   
				   
				$party_flag = 'already';
				$party_already_clink_id = $resposne_Array['data']['party_clinkID'];
				$party_already_domain = $resposne_Array['data']['party_domain']; ?>
				
				<div class="notice notice-error">
                <p><?php _e('You may have lost your user settings in the WordPress CLink plugin','cl_text'); ?></p>
        <p><?php _e('Your Party ID: '.wpclink_add_hyperlink_to_clink_ID($party_already_clink_id).' and associated with domain '.$party_already_domain ,'cl_text'); ?></p>
        
        <p><?php _e('Please check your <a class="button" target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$party_already_clink_id.'">Registration</a>','cl_text'); ?></p>
         <p><?php _e('You can update the registry with the information you are about to send','cl_text'); ?></p>
                
				<p> <input class="button" type="submit" value="Update" name="party_already_update" onclick="cl_party_update()" /></p>
				</div>
				   
			   <?php }elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to <br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
				   
			   }
			  
			     
			}
}
