<?php
/**
 * CLink Preferences Users 
 *
 * clink preferences users admin page
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Register clink user registration admin menu
add_action('admin_menu', 'wpclink_register_menu_preferences_users');




/**
 * CLink Users Update
 * 
 */
function wpclink_register_menu_preferences_users(){
	
	$current_user_id = get_current_user_id();
	// Party
	$clink_party_id = wpclink_get_option('authorized_contact');
	$creator_array = wpclink_get_option('authorized_creators');
		
	// CLink User Page
	$wpclink_preferences_users = apply_filters('wpclink_preferences_user_function','wpclink_display_preferences_users');
	
	if(wpclink_both_mode() || wpclink_export_mode()){ 
		// Administrator + Creator
		if(current_user_can('administrator') and wpclink_user_has_creator_list($current_user_id)){
			add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'manage_options', 'cl_users', $wpclink_preferences_users );	
		}elseif(wpclink_user_has_creator_list($current_user_id)){
			add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'edit_posts', 'cl_users', $wpclink_preferences_users );	
		// Administrator + Party
		}elseif( current_user_can('administrator') and $current_user_id == $clink_party_id){	
			add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'manage_options', 'cl_users', $wpclink_preferences_users );	

		}elseif($current_user_id == $clink_party_id){
			add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'edit_posts', 'cl_users', $wpclink_preferences_users );	
		}else{
			add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'manage_options', 'cl_users', $wpclink_preferences_users );	
		}
	}
}
/**
 * CLink User Party Update
 * 
 */
function wpclink_party_update_func(){
	
	$submitted = $_POST;
	
	$auto_selection = (isset($_GET['auto_selection'])) ? $_GET['auto_selection'] : '';
	
	$current_user_id = get_current_user_id();
	
	if(wpclink_is_acl_r_page() || ((isset($_POST['wpclink_select_user_field'])) and ! wp_verify_nonce( $_POST['wpclink_select_user_field'], 'wpclink_select_user' ))){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{
		// Admin Page
	$return_admin_page = menu_page_url( 'cl_users', false );
	
	/* ---------------AUTO SELECTION -----------------*/
	if($auto_selection == 1){
		
		$get_email = $_GET['ReturnEmail'];
		$role = $_GET['role'];
		if($role == 'contact'){
			if(!empty($get_email)){
				$user = get_user_by( 'email', urldecode($get_email) );
				$user_id = $user->ID;

		$submitted = array();
				
		// PARTY ID		
		$party_user_id = $user_id;
		$submitted['clink_party_user_id'] = $user_id;
		// Previous Party ID
		$previous_party_id = wpclink_get_option('authorized_contact');	
				
				
		// CREATE OR UPDATE
		if(empty($previous_party_id)){
			$action = 'create';
		}else if($party_user_id == $previous_party_id){
			$action = 'create';
		}else{
			$action = 'update';
		}
				

		if($party_user_id == $previous_party_id){

			$party_identifier_verify = get_user_meta($party_user_id,'wpclink_party_ID',true);

			if(!empty($party_identifier_verify)){

				$class = 'notice notice-error';
				$message = __(wpclink_display_display_name_by_user_id($party_user_id).' has been already selected as Contact Role', 'clink_text' );
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 

				return false;
			}
		}

		// Update Checkbox
		$display_email = $submitted['display_email'];
		$get_clink_display_email = 0;
				
		if($get_clink_display_email == '1'){
			$clink_display_email = 'yes';
		}else{
			$clink_display_email = 'no';
		}


		$clink_license_url = menu_page_url( 'cl-clink', false );

		$creator_array = wpclink_get_option('authorized_creators');
		$user_id_asso = $creator_array[0];


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
		$display_email_creator = get_user_meta($user_id_asso,'wpclink_display_email',true);
				
		// Teriority
		$territory = wpclink_get_option('territory_code');
		$party_info = get_userdata($party_user_id);
		$site_address = get_bloginfo('url');	

		// Current
		$party_identifier = get_user_meta($party_user_id,'wpclink_party_ID',true);
				
		if($action == 'update'){
			// Previous
			$previous_party_identifier = get_user_meta($previous_party_id,'wpclink_party_ID',true);
			$previous_user_data = get_userdata( $previous_party_id ); 
			$previous_party_name = $previous_user_data->display_name;
			$previous_url = $previous_user_data->user_url;
			$previous_party_contact_id = get_user_meta($previous_party_id, 'wpclink_party_access_key', true);
		}

				
		$url_party = WPCLINK_PARTY_APPROVE_API;	
		$same_domain = '1';
		

		// CUSTOMER ID
		$wpclink_party_access_key = get_user_meta($party_user_id,'wpclink_party_access_key',true);

		$sending_data = array(
						'body' => array(
							'site_address'  => urlencode($site_address),
							'party_name'	=> $party_info->display_name,
							'party_first_name' => $first_name,
							'party_last_name' => $last_name,
							'party_desciption' => $description,
							'party_identifier' => $party_identifier,
							'party_nickname' => $nick_name,
							'party_structure_type' => 'personal', // Check
							'party_email' => $creator_user->user_email,
							'party_webaddress' => urlencode($creator_user->user_url),
							'party_territory' => strtoupper($territory),
							'clink_site_domain' => get_bloginfo('url'),
							'clink_license_url' => $clink_license_url,
							'party_associated_user' => $associated_user,
							'party_alternative_identifier_type' => 'DOI',
							'party_alternative_identifier' => '',
							'party_modification_date' => get_user_meta($party_user_id, 'wpclink_party_modification_date',true),
							'party_access_key' => $wpclink_party_access_key,
							'domain_access_key' => wpclink_get_option('domain_access_key'),
							'party_display_email' => $clink_display_email,
							'same_domain' => $same_domain,
							'clink_edition' => 'personal',
							'return_page' => $return_admin_page,
							'action' => $action

						),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
					);
			if($action == 'update'){
				$sending_data['body']['previous_party_identifier'] = $previous_party_identifier;
				$sending_data['body']['previous_party_name'] = $previous_party_name;
				$sending_data['body']['previous_party_url'] = $previous_url;
				$sending_data['body']['previous_party_contact_id'] = $previous_party_contact_id;
			}

		

		$response = wp_remote_post(
					$url_party,
					$sending_data
				);

				if ( is_wp_error( $response ) ) {

				   $resposne_Array = is_wp_error( $response );

					// Response Debug
					wpclink_debug_log('UPDATE PARTY APPROVE '.print_r($response,true));

				}else {

				   $response_json = $response['body'];
				   $resposne_Array=json_decode($response_json,true);

				  wpclink_debug_log('UPDATE PARTY APPROVE '.print_r($response,true));

				   if($resposne_Array['status'] == 'update'){

					update_user_meta($submitted['clink_party_user_id'],'wpclink_user_aprrove_status',$action);
					update_user_meta($submitted['clink_party_user_id'],'wpclink_previous_contact_id',$previous_party_id);
					update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_type','party');				
					update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_domain_status',$same_domain);	

					$already_accept = get_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',true);
					if($already_accept == 2){
						update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_mode',1);
					}else{
						update_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',1);
					}


	// Previous Party ID
	$previous_party_id = wpclink_get_option('authorized_contact');
	$previous_party_id_before_save = $previous_party_id;


	if($previous_party_id != $submitted['clink_party_user_id']){
	update_user_meta($submitted['clink_party_user_id'],'wpclink_previous_contact_id_selection',$previous_party_id);
	}

	// Now Update
	wpclink_update_option('authorized_contact',$submitted['clink_party_user_id']);



					$class = 'notice notice-success';

					if($submitted['clink_party_user_id'] == $current_user_id){
						$message = __('You have been selected for CLink Contact role', 'clink_text' );
					}else{
						$message = __(wpclink_display_display_name_by_user_id($submitted['clink_party_user_id']).' has been selected for CLink Contact role', 'clink_text' );
					}
					 // NOTIFICATION
					printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 

				   }elseif($resposne_Array['status'] == 'create'){

					wpclink_update_option('authorized_contact',$submitted['clink_party_user_id']);

					update_user_meta($submitted['clink_party_user_id'],'wpclink_user_aprrove_status',$action);
					update_user_meta($submitted['clink_party_user_id'],'wpclink_previous_contact_id',$previous_party_id);
					update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_type','party');
					update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_domain_status',$same_domain);	


					$already_accept = get_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',true);
					if($already_accept == 2){
						update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_mode',1);
					}else{
						update_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',1);
					}



					   $class = 'notice notice-success';
					// #PARTY_UPDATE_NOTICE
					if($submitted['clink_party_user_id'] == $current_user_id){

						$message = __('You have been selected for CLink Contact role', 'clink_text' );

					}else{

						$message = __(wpclink_display_display_name_by_user_id($submitted['clink_party_user_id']).' has been selected for CLink Contact role', 'clink_text' );
					}
					   printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 

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
					$party_already_user_id = $party_user_id;
					$party_already_clink_id = $resposne_Array['data']['party_clinkID'];
					$party_already_domain = $resposne_Array['data']['party_domain']; ?>

					<div class="notice notice-error">
					<p><?php _e('You may have lost your user settings in the WordPress CLink plugin','cl_text'); ?></p>
			<p><?php _e('Your Contact ID: '.wpclink_add_hyperlink_to_clink_ID($party_already_clink_id).' and associated with domain '.$party_already_domain ,'cl_text'); ?></p>

			<p><?php _e('Please check your <a class="button" target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$party_already_clink_id.'">Registration</a>','cl_text'); ?></p>
			 <p><?php _e('You can update your WordPress profile with your CLink.ID from the registry','cl_text'); ?></p>

					<p> <input class="button" type="submit" value="Update" name="party_already_update" onclick="cl_party_update(<?php echo $party_already_user_id; ?>)" /></p>
					</div>

				   <?php }elseif($resposne_Array['status'] == 'notverified'){
					   ?>
			<div class="notice notice-error">
			<p><?php _e('Check you email inbox! We sent a verification email to '.$creator_user->user_email.'.<br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
			<p><?php _e('If you can’t find the email, click here to resend: <input type="submit" class="button" value="Resend verification email" /> ','cl_text'); ?></p>	
			</div>
		<?php

				   }
				}




			}
		}
		
	
	}else if(isset($submitted['clink_party_user_id']) and $submitted['clink_party_user_id'] > 0 and isset($submitted['_record_update']) and $submitted['_record_update'] == 1){
		

	// PARTY ID
	$party_user_id = $submitted['clink_party_user_id'];
	
	$previous_party_id = wpclink_get_option('authorized_contact');
		if($previous_party_id != $submitted['clink_party_user_id']){
			$previous_party_id_get = $previous_party_id;
		}
		
	if($party_user_id == $previous_party_id_before_save){
		
		$party_identifier_verify = get_user_meta($party_user_id,'wpclink_party_ID',true);
		
		if(!empty($party_identifier_verify)){
			
			$class = 'notice notice-error';
			$message = __(wpclink_display_display_name_by_user_id($party_user_id).' has been already selected as Contact Role', 'clink_text' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
			
			return false;
		}
	}
		
	
	
	
	/* ____________ CLINK MEDIA _______________ */
	/* _____________ VARIABLES __________________*/
	$creator_user = get_userdata( $party_user_id ); 
	$party_info = get_userdata($party_user_id);
	$site_address = get_bloginfo('url');	
	
		
	// Previous Email
	$previous_user_data = get_userdata( $previous_party_id_get ); 
	$previous_party_email = $previous_user_data->user_email;

	
	$url_party = WPCLINK_PARTY_API;

	$sending = array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
						'previous_party_email' => $previous_party_email,
						'party_email' => $creator_user->user_email,
                        'clink_site_domain' => get_bloginfo('url'),
						'clink_edition' => 'personal',
						'action' => 'update_record'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
		
		
	$response = wp_remote_post(
                $url_party,
				$sending
            );
			
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
				
				// Response Debug
				wpclink_debug_log('UPDATE RECORD PARTY APPROVE '.print_r($response,true));
 
            }else {
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   			   
			  wpclink_debug_log('UPDATE RECORD PARTY APPROVE '.print_r($response,true));
			   
			   if($resposne_Array['status'] == 'update_record'){
				   
				 // Identifier
				 update_user_meta($submitted['clink_party_user_id'],'wpclink_party_ID',$resposne_Array['data']['clink_contact_identifier']);   
				   
				 // Versions
				 update_user_meta($submitted['clink_party_user_id'],'wpclink_versions',$resposne_Array['data']['clink_contact_version']);
				   
				 // Selected
				 wpclink_update_option('authorized_contact',$submitted['clink_party_user_id']);
				   
				 // User Status (Action Completed)
				 update_user_meta($submitted['clink_party_user_id'],'wpclink_user_status_contact','saved');
				 
				
				$class = 'notice notice-success';
				   
				if($submitted['clink_party_user_id'] == $current_user_id){
				
					$message = __('You have been selected for CLink Contact role', 'clink_text' );
				   
				}else{
					
					$message = __(wpclink_display_display_name_by_user_id($submitted['clink_party_user_id']).' has been selected for CLink Contact role', 'clink_text' );
				}
				   
				   
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
			   
			   
			   }elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to '.$creator_user->user_email.'.<br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
				   
			   }
			}
	
	}else if(isset($submitted['clink_party_user_id']) and $submitted['clink_party_user_id'] > 0 and !isset($submitted['_update_party'])){
		
	/* ========== CREATE PARTY =========== */
		
	$party_user_id = $submitted['clink_party_user_id'];
	
		
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
	$display_email_creator = get_user_meta($user_id_asso,'wpclink_display_email',true);
	
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
	
	$url_party = WPCLINK_PARTY_APPROVE_API;
	$clink_license_url = menu_page_url( 'cl-clink', false );
	
	$data_sent = array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'party_name'	=> $party_info->display_name,
						'party_first_name' => $first_name,
						'party_last_name' => $last_name,
						'party_nickname' => $nick_name,
						'party_structure_type' => 'personal',
						'party_email' => $creator_user->user_email,
                        'party_webaddress' => urlencode($creator_user->user_url),
						'party_territory' => strtoupper($territory),
						'party_desciption' => $description,
						'clink_site_domain' => get_bloginfo('url'),
						'party_associated_user' => $associated_user,
						'party_alternative_identifier_type' => 'DOI',
						'party_alternative_identifier' => '',
						'clink_license_url' => $clink_license_url,
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
				wpclink_debug_log('CREATE PARTY APPROVE '.print_r($response,true));
 
            }else {
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   			   
			   // Response Debug
				wpclink_debug_log('CREATE PARTY APPROVE '.print_r($response,true));
			   
			   
			   if($resposne_Array['status'] == 'create'){
				   
				wpclink_update_option('authorized_contact',$submitted['clink_party_user_id']);
				update_user_meta($submitted['clink_party_user_id'],'wpclink_user_aprrove_status','create');
				update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_type','party');
				
				
				$already_accept = get_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',true);
				if($already_accept == 2){
					update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_mode',1);
					
				}else{
					update_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',1);
				}
				
			   				
				$class = 'notice notice-success';
				   
				if($submitted['clink_party_user_id'] == $current_user_id){
				
					$message = __('You have been selected for CLink Contact role', 'clink_text' );
				   
				}else{
					$message = __(wpclink_display_display_name_by_user_id($submitted['clink_party_user_id']).' has been selected for CLink Contact role', 'clink_text' );
				}
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
			   
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
				$party_already_user_id = $party_user_id;
				$party_already_clink_id = $resposne_Array['data']['party_clinkID'];
				$party_already_domain = $resposne_Array['data']['party_domain']; ?>
				
				<div class="notice notice-error">
                <p><?php _e('You may have lost your user settings in the WordPress CLink plugin','cl_text'); ?></p>
        <p><?php _e('Your Contact ID: '.wpclink_add_hyperlink_to_clink_ID($party_already_clink_id).' and associated with domain '.$party_already_domain ,'cl_text'); ?></p>
        
        <p><?php _e('Please check your <a class="button" target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$party_already_clink_id.'">Registration</a>','cl_text'); ?></p>
         <p><?php _e('You can update your WordPress profile with your CLink.ID from the registry','cl_text'); ?></p>
                
				<p> <input class="button" type="submit" value="Update" name="party_already_update" onclick="cl_party_update(<?php echo $party_already_user_id; ?>)" /></p>
				</div>
				   
			   <?php }elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to '.$creator_user->user_email.'.<br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
		   
			   }
			}	
	}elseif(isset($submitted['_update_party'])){
	
		
	
	// Party ID
	$party_user_id = $submitted['clink_party_user_id'];
		
	// Previous Party ID
	$previous_party_id = get_user_meta($party_user_id,'wpclink_previous_contact_id_selection',true);
		
	if($party_user_id == $previous_party_id_before_save){
		$party_identifier_verify = get_user_meta($party_user_id,'wpclink_party_ID',true);
		
		if(!empty($party_identifier_verify)){
			$class = 'notice notice-error';
			$message = __(wpclink_display_display_name_by_user_id($party_user_id).' has been already selected as Contact Role', 'clink_text' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
			return false;
		}
	}
		
	// Update Checkbox
	$display_email = $submitted['display_email'];
	$get_clink_display_email = 0;
	
	
	if($get_clink_display_email == '1'){
		$clink_display_email = 'yes';
	}else{
		$clink_display_email = 'no';
	}
		
	
	if($submitted['_resend_email'] == 1){
		$resend_email = 1;
	}else{
		$resend_email = 0;
	}
	
	
	$clink_license_url = menu_page_url( 'cl-clink', false );
	$creator_array = wpclink_get_option('authorized_creators');
	$user_id_asso = $creator_array[0];
		
	
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
	$display_email_creator = get_user_meta($user_id_asso,'wpclink_display_email',true);
		
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
	
	$url_party = WPCLINK_PARTY_APPROVE_API;
	
	
	if(isset($submitted['_update_party_new'])){
		$same_domain = '1';
	}else{
		$same_domain = '0';
	}
	
	// Previous Party ID
	$wpclink_party_access_key = get_user_meta($party_user_id,'wpclink_party_access_key',true);

	$sending_data = array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'party_name'	=> $party_info->display_name,
						'party_first_name' => $first_name,
						'party_last_name' => $last_name,
						'party_desciption' => $description,
						'party_identifier' => $party_identifier,
						'party_nickname' => $nick_name,
						'previous_party_identifier' => $previous_party_identifier,
						'previous_party_name' => $previous_party_name,
						'previous_party_url' => $previous_url,
						'previous_party_contact_id' => $previous_party_contact_id,
						'party_structure_type' => 'personal', // Check
						'party_email' => $creator_user->user_email,
                        'party_webaddress' => urlencode($creator_user->user_url),
						'party_territory' => strtoupper($territory),
						'clink_site_domain' => get_bloginfo('url'),
						'clink_license_url' => $clink_license_url,
						'party_associated_user' => $associated_user,
						'party_alternative_identifier_type' => 'DOI',
						'party_alternative_identifier' => '',
						'party_modification_date' => get_user_meta($party_user_id, 'wpclink_party_modification_date',true),
						'party_access_key' => $wpclink_party_access_key,
						'domain_access_key' => wpclink_get_option('domain_access_key'),
						'party_display_email' => $clink_display_email,
						'same_domain' => $same_domain,
						'clink_edition' => 'personal',
						'resend_email' => $resend_email,
						'return_page' => $return_admin_page,
						'action' => 'update'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
		
	$response = wp_remote_post(
                $url_party,
                $sending_data
            );
			
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );		
				// Response Debug
				wpclink_debug_log('UPDATE PARTY APPROVE '.print_r($response,true));
 
            }else {
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
				
			  	wpclink_debug_log('UPDATE PARTY APPROVE '.print_r($response,true));
			   
			   if($resposne_Array['status'] == 'update'){
				   
				update_user_meta($submitted['clink_party_user_id'],'wpclink_user_aprrove_status','update');
				update_user_meta($submitted['clink_party_user_id'],'wpclink_previous_contact_id',$previous_party_id);
				update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_type','party');				
				update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_domain_status',$same_domain);	
				
				$already_accept = get_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',true);
				if($already_accept == 2){
					update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_mode',1);
				}else{
					update_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',1);
				}
			   
				   
// Previous Party ID
$previous_party_id = wpclink_get_option('authorized_contact');
$previous_party_id_before_save = $previous_party_id;


if($previous_party_id != $submitted['clink_party_user_id']){
	update_user_meta($submitted['clink_party_user_id'],'wpclink_previous_contact_id_selection',$previous_party_id);
}

// Now Update
wpclink_update_option('authorized_contact',$submitted['clink_party_user_id']);
			
			   		
				
				$class = 'notice notice-success';   
				if($submitted['clink_party_user_id'] == $current_user_id){
					$message = __('You have been selected for CLink Contact role', 'clink_text' );
				}else{
					$message = __(wpclink_display_display_name_by_user_id($submitted['clink_party_user_id']).' has been selected for CLink Contact role', 'clink_text' );
				}
				   
				   
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
			   
			   }elseif($resposne_Array['status'] == 'create'){
				   
				wpclink_update_option('authorized_contact',$submitted['clink_party_user_id']);
				update_user_meta($submitted['clink_party_user_id'],'wpclink_user_aprrove_status','update');
				update_user_meta($submitted['clink_party_user_id'],'wpclink_previous_contact_id',$previous_party_id);
				update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_type','party');
				update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_domain_status',$same_domain);	
								
				
				$already_accept = get_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',true);
				if($already_accept == 2){
					update_user_meta($submitted['clink_party_user_id'],'wpclink_user_approve_mode',1);
				}else{
					update_user_meta($submitted['clink_party_user_id'],'wpclink_terms_accept',1);
				}
			   
				   
				  
				   $class = 'notice notice-success';
				// #PARTY_UPDATE_NOTICE
				if($submitted['clink_party_user_id'] == $current_user_id){
					$message = __('You have been selected for CLink Contact role', 'clink_text' );
				   
				}else{
					$message = __(wpclink_display_display_name_by_user_id($submitted['clink_party_user_id']).' has been selected for CLink Contact role', 'clink_text' );
				}
				   printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
			   
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
				$party_already_user_id = $party_user_id;
				$party_already_clink_id = $resposne_Array['data']['party_clinkID'];
				$party_already_domain = $resposne_Array['data']['party_domain']; ?>
				
				<div class="notice notice-error">
                <p><?php _e('You may have lost your user settings in the WordPress CLink plugin','cl_text'); ?></p>
        <p><?php _e('Your Contact ID: '.wpclink_add_hyperlink_to_clink_ID($party_already_clink_id).' and associated with domain '.$party_already_domain ,'cl_text'); ?></p>
        
        <p><?php _e('Please check your <a class="button" target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$party_already_clink_id.'">Registration</a>','cl_text'); ?></p>
         <p><?php _e('You can update your WordPress profile with your CLink.ID from the registry','cl_text'); ?></p>
                
				<p> <input class="button" type="submit" value="Update" name="party_already_update" onclick="cl_party_update(<?php echo $party_already_user_id; ?>)" /></p>
				</div>
				   
			 <?php }elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to '.$creator_user->user_email.'.<br> Please click on the Verify email button in that message within 24 hours to complete your registration.<br> If you can’t find the email, click here to: <form id="party_form" method="post" action="'.menu_page_url( 'cl_users', false ).'">'.wp_nonce_field( 'wpclink_select_user', 'wpclink_select_user_field' ).'<input type="submit" class="button" value="Resend verification email" /><input type="hidden" name="clink_party_user_id" value="'.$submitted['clink_party_user_id'].'" /><input type="hidden" name="_update_party" value="'.$submitted['_update_party'].'" /><input type="hidden" name="_resend_email" value="1" /></form>','cl_text'); ?></p>	
		</div>
	<?php   
			   }
			}
	}
	
	}	
}
/**
 * CLink Update Copyright Owner
 * 
 */
function wpclink_update_right_holder(){
	
	if(wpclink_is_acl_r_page() ||  ((isset($_POST['wpclink_select_user_field'])) and ! wp_verify_nonce( $_POST['wpclink_select_user_field'], 'wpclink_select_user' ))){
			
		}else{
	
	if(isset($_POST['right_holder'])){
			
		$right_holder_save = $_POST['right_holder'];
		
		// Update
		wpclink_update_option('rights_holder',$right_holder_save);
		
		$right_holder = wpclink_get_option('rights_holder');
		if($right_holder == 'party'){
			$right_holder_id = wpclink_get_option('authorized_contact');
		}elseif($right_holder == 'creator'){
			$creator_array = wpclink_get_option('authorized_creators');
			$right_holder_id = $creator_array[0];
		}
		
		wpclink_notif_print(wpclink_display_display_name_by_user_id($right_holder_id).' has been selected for CLink Rights Holder role','success');
		
	}
	
		}
}
// Register CLink update copyright owener function
add_action('admin_init','wpclink_update_right_holder');
/**
 * CLink Users Admin Page Render
 * 
 */
function wpclink_display_preferences_users(){
	
	
	// Party Update
	wpclink_party_update_func();
	
	$creator_flag = '';
	
	// Copyright Info
	$submitted = $_POST;
	
		// Nonces
		if(wpclink_is_acl_r_page() || ((isset($_POST['wpclink_select_user_field'])) and ! wp_verify_nonce( $_POST['wpclink_select_user_field'], 'wpclink_select_user' ))){
			wpclink_notif_print('Action cannot be perform.','error');
		}else{
			
$auto_selection = (isset($_GET['auto_selection'])) ? $_GET['auto_selection'] : '';		
	// Admin Page
	$return_admin_page = menu_page_url( 'cl_users', false );		
if($auto_selection == 1){
	
	
		$get_email = $_GET['ReturnEmail'];
		$role = $_GET['role'];
		if($role == 'creator'){
			if(!empty($get_email)){
				$user = get_user_by( 'email', urldecode($get_email) );
	
	
	$user_id = $user->ID;			
	$submitted = array();
	$submitted['clink_user_id'] = $user_id;
	
		
	// PARTY ID
	$party_user_id = wpclink_get_option('authorized_contact');
	
	// BEFORE CUSTOMER ID AND IDENTIFIER
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
	
	$before_creator_identifier = get_user_meta($user_id_before,'wpclink_party_ID',true);
	
	
	$current_user_id = get_current_user_id();
				
				
	// Action
	if(empty($user_id_before)){
		$action = 'create';
	}else if($user_id_before == $user_id){
		$action = 'create';
	}else{
		$action = 'update';
	}
	
	
	
	$creator_array = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array[0];
	
	$user_id = $submitted['clink_user_id'];
	// if same user selected
	if($user_id_before == $user_id){
		
			$class = 'notice notice-error';
			$message = __(wpclink_display_display_name_by_user_id($user_id).' has been already selected as CLink Creator Role', 'clink_text' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
		
	}else{
	
		
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
	
	$url_creator = WPCLINK_CREATOR_APPROVE_API;
	
	
	// PARTY TO CUSTOMER ID
	$current_party_id = wpclink_get_option('authorized_contact');
	$party_access_key = get_user_meta($user_id,'wpclink_party_access_key',true);
	
	
	
	// Previous Identifier
	$previous_creator_user = get_userdata( $user_id_before ); 
	$previous_creator_name = $previous_creator_user->first_name;
	$previous_creator_displayname = $previous_creator_user->display_name;
	$previous_creator_lastname = $previous_creator_user->last_name;
	$previous_creator_email = $previous_creator_user->user_email;
	
	$previous_creator_identifier = get_user_meta($user_id_before,'wpclink_party_ID',true);
	
	// Teriority
	$territory = wpclink_get_option('territory_code');
	
	// Creator ID
	$creator_ID = get_user_meta($user_id,'wpclink_party_ID',true);
	
	
	
	
	$create_data = array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'creator_name'	=> $first_name,
						'creator_lastname' => $last_name,
						'creator_display_name' => $creator_user->display_name,
                        'creator_email' => $creator_user->user_email,
						'clink_site_domain' => get_bloginfo('url'),
						'party_nickname' => $nick_name,
						'party_desciption' => $description,
						'party_webaddress' => urlencode($creator_user->user_url),
						'creator_url' => $creator_user->user_url,
						'creator_identifier' => $creator_ID,
						'party_access_key' => $party_access_key,
						'creator_associated_party'=> $ass_array,
						'creator_alternative_identifier_type' => 'DOI',
						'creator_alternative_identifier' => array('XYZ'),
						'party_territory' => strtoupper($territory),
						'clink_edition' => 'personal',
						'return_page' => $return_admin_page,
						'action'=> $action
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
	
	
		if($action == 'update'){
			$create_data['body']['previous_creator_identifier'] = $previous_creator_identifier;
			$create_data['body']['previous_creator_email'] = urlencode($previous_creator_email);
			$create_data['body']['previous_creator_url'] = get_bloginfo('url');
			$create_data['body']['previous_creator_lastname'] = $previous_creator_lastname;
			$create_data['body']['previous_creator_name'] = $previous_creator_name;
			$create_data['body']['previous_creator_displayname'] = $previous_creator_displayname;
		}
		
	$response = wp_remote_post(
                $url_creator,
                $create_data
            );
		
	
 
            if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
			   
				// Response Debug
				wpclink_debug_log('CREATOR UPDATE APPROVE 2 '.print_r($response,true));
 
            }else {
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   			   
			   	// Response Debug		   
				wpclink_debug_log('CREATOR UPDATE APPROVE 3 '.print_r($response,true));
			   
			  
	if($resposne_Array['status'] == 'update'){
		
	// BEFORE CUSTOMER ID AND IDENTIFIER
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
		
	$creator_array = array();
	$creator_array[0] = $submitted['clink_user_id'];
	wpclink_update_option('authorized_creators',$creator_array,'no');
				   
	$creator_array = wpclink_get_option('authorized_creators');
	$user_id = $creator_array[0];
	
	update_user_meta($user_id,'wpclink_user_aprrove_status',$action);
	update_user_meta($user_id,'wpclink_user_approve_type','creator');
	
	update_user_meta($user_id,'wpclink_previous_creator_id',$user_id_before);
	
	
	$already_accept = get_user_meta($user_id,'wpclink_terms_accept',true);
	if($already_accept == 2){
		update_user_meta($user_id,'wpclink_user_approve_mode',1);
	}else{
		update_user_meta($user_id,'wpclink_terms_accept',1);
	}
	
	
	// #CREATOR_UPDATE_NOTICE
		$class = 'notice notice-success';
		if($user_id == $current_user_id){
			$message = __('You have been selected for CLink Creator role', 'clink_text' );
		}else{
			$message = __(wpclink_display_display_name_by_user_id($user_id).' has been selected for CLink Creator role', 'clink_text' );
		}
		
	 printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
	
	
	// ALREADY
	}elseif($resposne_Array['status'] == 'already'){
		
		$creator_flag = 'already';
		$already_user_id = $user_id;
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
	
	$submitted_user_id = $submitted['clink_user_id'];
	
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
	
	// UPDATE CREATOR ID
	$creator_array = array();
	$creator_array[0] = $submitted_user_id;
	wpclink_update_option('authorized_creators',$creator_array,'no');
	
		
	update_user_meta($submitted_user_id,'wpclink_user_aprrove_status','update');
	update_user_meta($submitted_user_id,'wpclink_user_approve_type','creator');
	update_user_meta($user_id,'wpclink_previous_creator_id',$user_id_before);
	
		
	$already_accept = get_user_meta($submitted_user_id,'wpclink_terms_accept',true);
	if($already_accept == 2){
		update_user_meta($submitted_user_id,'wpclink_user_approve_mode',1);
	}else{
		update_user_meta($submitted_user_id,'wpclink_terms_accept',1);
	}

	// ALREADY
	}elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to '.$creator_user->user_email.'.<br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
				   
			   }
	
	}
	}
	
			}
		}
	
	
}else if(isset($submitted['clink_user_id']) and $submitted['_record_update_creator'] == 1){
	
	
	
	
	$submitted = $_POST;
		
	// PARTY ID
	$party_user_id = wpclink_get_option('authorized_contact');
	
	// BEFORE CUSTOMER ID AND IDENTIFIER
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
	
	$before_creator_identifier = get_user_meta($user_id_before,'wpclink_party_ID',true);
	
	
	$current_user_id = get_current_user_id();
	
	
	
	$creator_array = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array[0];
	
	$user_id = $submitted['clink_user_id'];
	// if same user selected
	if($user_id_before == $user_id){
		
			$class = 'notice notice-error';
			$message = __(wpclink_display_display_name_by_user_id($user_id).' has been already selected as CLink Creator Role', 'clink_text' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
		
	}else{
	
		
	if($associated_user = get_user_meta($party_user_id,'wpclink_party_ID',true)){
		$ass_array = $associated_user;
	}else{
		$ass_array = '';
	}
	
	
	/* ____________ CLINK MEDIA _______________ */
	/* _____________ VARIABLES __________________*/
	
	$creator_user = get_userdata( $user_id ); 
	$site_address = get_bloginfo('url');
	
	$url_creator = WPCLINK_CREATOR_API;
		
	// Previous Identifier
	$previous_creator_user = get_userdata( $user_id_before ); 
	$previous_creator_name = $previous_creator_user->first_name;
	$previous_creator_email = $previous_creator_user->user_email;
	$previous_creator_identifier = get_user_meta($user_id_before,'wpclink_party_ID',true);
	
	// Creator ID
	$creator_ID = get_user_meta($user_id,'wpclink_party_ID',true);
	
	$create_data = array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'creator_email' => $creator_user->user_email,
						'clink_site_domain' => get_bloginfo('url'),
						'creator_identifier' => $creator_ID,
						'previous_creator_identifier' => $previous_creator_identifier,
						'previous_creator_email' => urlencode($previous_creator_email),
						'clink_edition' => 'personal',
						'action'=> 'update_record_creator'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                );
	
	$response = wp_remote_post(
                $url_creator,
                $create_data
            );
		
	
 
            if ( is_wp_error( $response ) ) {
               $resposne_Array = is_wp_error( $response );
			   
				// Response Debug
				wpclink_debug_log('CREATOR UPDATE RECORD APPROVE 2 '.print_r($response,true));
 
            }else {
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
				
			   	// Response Debug		   
				wpclink_debug_log('CREATOR UPDATE RECORD APPROVE 3 '.print_r($response,true));
			   
			  
	if($resposne_Array['status'] == 'update_record_creator'){
		
	// BEFORE CUSTOMER ID AND IDENTIFIER
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
		
	$creator_array = array();
	$creator_array[0] = $submitted['clink_user_id'];
	wpclink_update_option('authorized_creators',$creator_array,'no');
				   
	$creator_array = wpclink_get_option('authorized_creators');
	$user_id = $creator_array[0];
		
		
	// Identifier
	 update_user_meta($user_id,'wpclink_party_ID',$resposne_Array['data']['clink_creator_identifier']);   

	 // Versions
	 update_user_meta($user_id,'wpclink_versions',$resposne_Array['data']['clink_creator_version']);

	// User Status (Action Completed)
	update_user_meta($user_id,'wpclink_user_status_creator','saved');

		
	// #CREATOR_UPDATE_NOTICE
		$class = 'notice notice-success';
		if($user_id == $current_user_id){
			$message = __('You have been selected for CLink Creator role', 'clink_text' );
		}else{
			$message = __(wpclink_display_display_name_by_user_id($user_id).' has been selected for CLink Creator role', 'clink_text' );
		}
		
	 printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
	
	
	// ALREADY
	}elseif($resposne_Array['status'] == 'already'){
		
		$creator_flag = 'already';
		$already_user_id = $user_id;
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
	
	$submitted_user_id = $submitted['clink_user_id'];
	
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
	
	// UPDATE CREATOR ID
	$creator_array = array();
	$creator_array[0] = $submitted_user_id;
	wpclink_update_option('authorized_creators',$creator_array,'no');
	
		
	update_user_meta($submitted_user_id,'wpclink_user_aprrove_status','update');
	update_user_meta($submitted_user_id,'wpclink_user_approve_type','creator');
	update_user_meta($user_id,'wpclink_previous_creator_id',$user_id_before);
	
		
	$already_accept = get_user_meta($submitted_user_id,'wpclink_terms_accept',true);
	if($already_accept == 2){
		update_user_meta($submitted_user_id,'wpclink_user_approve_mode',1);
	}else{
		update_user_meta($submitted_user_id,'wpclink_terms_accept',1);
	}
		
	// ALREADY
	}elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to '.$creator_user->user_email.'.<br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
				   
			   }
	
		}
	}
}else if(isset($submitted['clink_user_id']) and $submitted['already_action'] == 'already_verify'){
	
	
	
		
	// Submitted
	$submitted = $_POST;
	$submitted_user_id = $submitted['clink_user_id'];
	// User Data
	$creator_user = get_userdata( $submitted_user_id ); 
	
	$site_address = get_bloginfo('url');
	
	/*____URL____*/
	$url_creator = WPCLINK_CREATOR_APPROVE_API;
	
	$response = wp_remote_post(
                $url_creator,
                array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'creator_email' => $creator_user->user_email,
						'clink_site_domain' => get_bloginfo('url'),
						'action'=> 'already_verify'
                    ),  'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
	
	 if ( is_wp_error( $response ) ) {
 
               $resposne_Array = is_wp_error( $response );
			   
			  // Response Debug
				wpclink_debug_log('ALREADY VERIFIED CREATOR '.print_r($response,true));
		 
            }else {
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   
	// CREATE  
	if($resposne_Array['status'] == 'already_verify'){
		
	// UPDATE CREATOR ID
	$creator_array = array();
	$creator_array[0] = $submitted_user_id;
	wpclink_update_option('authorized_creators',$creator_array,'no');
	
	
	// User Identifier
	update_user_meta($submitted_user_id,'wpclink_party_ID',$resposne_Array['data']['creator_identifier']);
	
	$update_user_array = array('ID' => $submitted_user_id, 
							   'first_name'=> $resposne_Array['data']['creator_first_name'],
							   'last_name' => $resposne_Array['data']['creator_last_name'],
							   'display_name' => $resposne_Array['data']['creator_display_name'],
							   'user_url' => $resposne_Array['data']['creator_domain']);
	
	wp_update_user($update_user_array);
	
	update_user_meta($submitted_user_id,'wpclink_party_access_key',$resposne_Array['data']['party_access_key']);
	
	
	// ALREADY
	}elseif($resposne_Array['status'] == 'already'){
		
		$creator_flag = 'already';
		$already_user_id = $submitted_user_id;
		$already_clink_id = $resposne_Array['data']['creator_clinkID'];
		$already_domain = $resposne_Array['data']['creator_domain'];
		
	}
	}

}elseif(isset($submitted['clink_user_id']) and $submitted['already_action'] == 'already_update'){
	
	
	
	// USER ID
	$submitted_user_id = $submitted['clink_user_id'];
	
	// PARTY ID
	$party_user_id = wpclink_get_option('authorized_contact');	
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
	//$display_email_party = get_user_meta($party_user_id,'wpclink_party_email_display_status',true);
	$display_email_party = 0;
	
	$domain_access_key = wpclink_get_option('domain_access_key');
	
	/*____URL____*/
	
	$url_creator = WPCLINK_CREATOR_API;

	$sending = array('site_address'  => urlencode($site_address),
                        'creator_name'	=> $first_name,
						'creator_lastname' => $last_name,
						'creator_display_name' => $creator_user->display_name,
					  	'party_desciption' => $description,
                        'creator_email' => $creator_user->user_email,
						'clink_site_domain' => get_bloginfo('url'),
						'creator_nickname' => $nick_name,
						'creator_url' => $creator_user->user_url,
						'creator_associated_party'=> $ass_array,
						'creator_alternative_identifier_type' => 'DOI',
						'creator_alternative_identifier' => array('XYZ'),
						'domain_access_key' => $domain_access_key,
						'clink_edition' => 'personal',
						'action'=> 'already_update'
                    );
					
	if($previous_creator_id = get_user_meta($submitted_user_id ,'wpclink_previous_creator_id',true)){
		if($previous_creator_id > 0){
	
	// Previous Identifier
	$previous_creator_user = get_userdata( $previous_creator_id ); 
	$previous_creator_name = $previous_creator_user->first_name;
	$previous_creator_displayname = $previous_creator_user->display_name;
	$previous_creator_lastname = $previous_creator_user->last_name;
	$previous_creator_identifier = get_user_meta($previous_creator_id,'wpclink_party_ID',true);
	
	$previous_array = array('previous_creator_identifier' => $previous_creator_identifier,
							'previous_creator_url' => get_bloginfo('url'),
							'previous_creator_lastname' => $previous_creator_lastname,
							'previous_creator_name' => $previous_creator_name,
							'previous_creator_displayname' => $previous_creator_displayname);
	
	$sending = array_merge($sending,$previous_array);
	
		}
	}
	
	$response = wp_remote_post(
                $url_creator,
                array(
                    'body' => $sending,'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );
						
			
 
            if ( is_wp_error( $response ) ) { 
               $resposne_Array = is_wp_error( $response );
				
				// Response Debug
				wpclink_debug_log('CREATOR ALREADY UPDATE '.print_r($response,true));
				
            }else {
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);	
				
			  // Response Debug
				wpclink_debug_log('CREATOR ALREADY UPDATE '.print_r($response,true));
			   
			   
	// CREATE  
	if($resposne_Array['status'] == 'already_update'){
		
	// UPDATE CREATOR ID
	$creator_array = array();
	$creator_array[0] = $submitted_user_id;
	wpclink_update_option('authorized_creators',$creator_array,'no');
	
	// User Identifier
	update_user_meta($submitted_user_id,'wpclink_party_ID',$resposne_Array['data']['creator_identifier']);
			
	// Modification INFO	
	update_user_meta($submitted_user_id,'wpclink_party_access_key',$resposne_Array['data']['party_access_key']);
	
	// ALREADY
	}elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to '.$creator_user->user_email.'.<br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
				   
		}
}
	
}elseif(isset($submitted['clink_user_id']) and !isset($submitted['_update_user'])){
	
	// Submitted
	$submitted = $_POST;
	$submitted_user_id = $submitted['clink_user_id'];
	
	$current_user_id = get_current_user_id();
	
	
	// BEFORE CUSTOMER ID AND IDENTIFIER
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
	
	// PARTY ID
	$party_user_id = wpclink_get_option('authorized_contact');
		
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
	
	// Previous Identifier
	$previous_creator_user = get_userdata( $user_id_before ); 
	$previous_creator_name = $previous_creator_user-> first_name;
	$previous_creator_displayname = $previous_creator_user->display_name;
	$previous_creator_email = $previous_creator_user->user_email;
	$previous_creator_lastname = $previous_creator_user->last_name;
	$previous_creator_identifier = get_user_meta($user_id_before,'wpclink_party_ID',true);
	$previous_creator_url = $previous_creator_user->user_url;
	
	// Teriority
	$territory = wpclink_get_option('territory_code');

	/*____URL____*/
	$url_creator = WPCLINK_CREATOR_APPROVE_API;
	
	$response = wp_remote_post(
                $url_creator,
                array(
                    'body' => array(
                        'site_address'  => urlencode($site_address),
                        'creator_name'	=> $first_name,
						'creator_lastname' => $last_name,
						'creator_display_name' => $creator_user->display_name,
                        'creator_email' => $creator_user->user_email,
						'party_desciption' => $description,
						'clink_site_domain' => get_bloginfo('url'),
						'previous_creator_identifier' => $previous_creator_identifier,
						'previous_creator_email' => urlencode($previous_creator_email),
						'previous_creator_url' => $previous_creator_url,
						'previous_creator_name' => $previous_creator_name,
						'previous_creator_displayname' => $previous_creator_displayname,
						'previous_creator_lastname' => $previous_creator_lastname,
						'party_nickname' => $nick_name,
						'party_webaddress' => urlencode($creator_user->user_url),
						'creator_url' => $creator_user->user_url,
						'creator_associated_party'=> $ass_array,
						'creator_alternative_identifier_type' => 'DOI',
						'creator_alternative_identifier' => array('XYZ'),
						'party_territory' => strtoupper($territory),
						'domain_access_key' => $domain_access_key,
						'party_access_key' => $party_access_key,
						'clink_edition' => 'personal',
						'return_page' => $return_admin_page,
						'action'=> 'create'
						
                    ),'timeout' => WPCLINK_API_TIMEOUT,'method' => 'POST'
                )
            );

		if ( is_wp_error( $response ) ) {
 
			$resposne_Array = is_wp_error( $response );
			
			  
			   // Response Debug
				wpclink_debug_log('CREATOR UPDATE APPROVE '.print_r($response,true));
			  
 
            }else {
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			
				// Response Debug
				wpclink_debug_log('CREATOR UPDATE APPROVE '.print_r($response,true));
			   			   
	
			   
	// CREATE  
	if($resposne_Array['status'] == 'create'){
		
	// UPDATE CREATOR ID
	$creator_array = array();
	$creator_array[0] = $submitted_user_id;
	wpclink_update_option('authorized_creators',$creator_array,'no');
	
	update_user_meta($submitted_user_id,'wpclink_user_aprrove_status','create');
	update_user_meta($submitted_user_id,'wpclink_user_approve_type','creator');
	
	
		$already_accept = get_user_meta($submitted_user_id,'wpclink_terms_accept',true);
		if($already_accept == 2){
			update_user_meta($submitted_user_id,'wpclink_user_approve_mode',1);
		}else{
			update_user_meta($submitted_user_id,'wpclink_terms_accept',1);
		}
	
		
	$class = 'notice notice-success';
		if($submitted_user_id == $current_user_id){
			$message = __('You have been selected for CLink Creator role', 'clink_text' );
		}else{
			$message = __(wpclink_display_display_name_by_user_id($submitted_user_id).' has been selected for CLink Creator role', 'clink_text' );
		}
		
	 printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
	
	
	// ALREADY
	}elseif($resposne_Array['status'] == 'already'){
		
		$creator_flag = 'already';
		$already_user_id = $submitted_user_id;
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
		<p><?php _e('Check you email inbox! We sent a verification email to '.$creator_user->user_email.'.<br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php

			}		
	}
	
	}elseif(isset($submitted['clink_user_id']) and isset($submitted['_update_user'])){
	
	
	$submitted = $_POST;
		
	// PARTY ID
	$party_user_id = wpclink_get_option('authorized_contact');
	
	// BEFORE CUSTOMER ID AND IDENTIFIER
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
	
	$before_creator_identifier = get_user_meta($user_id_before,'wpclink_party_ID',true);

	$current_user_id = get_current_user_id();
	
	$creator_array = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array[0];
	
	$user_id = $submitted['clink_user_id'];
	// if same user selected
	if($user_id_before == $user_id){
		
			$class = 'notice notice-error';
			$message = __(wpclink_display_display_name_by_user_id($user_id).' has been already selected as CLink Creator Role', 'clink_text' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
		
	}else{
	
		
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
	$url_creator = WPCLINK_CREATOR_APPROVE_API;
	
	// PARTY TO CUSTOMER ID
	$current_party_id = wpclink_get_option('authorized_contact');
	$party_access_key = get_user_meta($user_id,'wpclink_party_access_key',true);
	

	// Previous Identifier
	$previous_creator_user = get_userdata( $user_id_before ); 
	$previous_creator_name = $previous_creator_user->first_name;
	$previous_creator_displayname = $previous_creator_user->display_name;
	$previous_creator_lastname = $previous_creator_user->last_name;
	$previous_creator_email = $previous_creator_user->user_email;
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
						'party_desciption' => $description,
						'party_webaddress' => urlencode($creator_user->user_url),
						'creator_url' => $creator_user->user_url,
						'creator_identifier' => $creator_ID,
						'previous_creator_identifier' => $previous_creator_identifier,
						'previous_creator_email' => urlencode($previous_creator_email),
						'previous_creator_url' => get_bloginfo('url'),
						'previous_creator_lastname' => $previous_creator_lastname,
						'previous_creator_name' => $previous_creator_name,
						'previous_creator_displayname' => $previous_creator_displayname,
						'party_access_key' => $party_access_key,
						'creator_associated_party'=> $ass_array,
						'creator_alternative_identifier_type' => 'DOI',
						'creator_alternative_identifier' => array('XYZ'),
						'party_territory' => strtoupper($territory),
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
				wpclink_debug_log('CREATOR UPDATE APPROVE 2 '.print_r($response,true));
 
            }else {
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
			   			   
			   	// Response Debug		   
				wpclink_debug_log('CREATOR UPDATE APPROVE 3 '.print_r($response,true));
			   
			  
	if($resposne_Array['status'] == 'update'){
		
	// BEFORE CUSTOMER ID AND IDENTIFIER
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
		
	$creator_array = array();
	$creator_array[0] = $submitted['clink_user_id'];
	wpclink_update_option('authorized_creators',$creator_array,'no');
				   
	$creator_array = wpclink_get_option('authorized_creators');
	$user_id = $creator_array[0];
	
	update_user_meta($user_id,'wpclink_user_aprrove_status','update');
	update_user_meta($user_id,'wpclink_user_approve_type','creator');
	
	update_user_meta($user_id,'wpclink_previous_creator_id',$user_id_before);
	
	
	$already_accept = get_user_meta($user_id,'wpclink_terms_accept',true);
	if($already_accept == 2){
		update_user_meta($user_id,'wpclink_user_approve_mode',1);
	}else{
		update_user_meta($user_id,'wpclink_terms_accept',1);
	}
	
	
	// #CREATOR_UPDATE_NOTICE
		$class = 'notice notice-success';
		if($user_id == $current_user_id){
			$message = __('You have been selected for CLink Creator role', 'clink_text' );
		}else{
			$message = __(wpclink_display_display_name_by_user_id($user_id).' has been selected for CLink Creator role', 'clink_text' );
		}
		
	 printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
	
	
	// ALREADY
	}elseif($resposne_Array['status'] == 'already'){
		
		$creator_flag = 'already';
		$already_user_id = $user_id;
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
	
	$submitted_user_id = $submitted['clink_user_id'];
	
	$creator_array_before = wpclink_get_option('authorized_creators');
	$user_id_before = $creator_array_before[0];
	
	// UPDATE CREATOR ID
	$creator_array = array();
	$creator_array[0] = $submitted_user_id;
	wpclink_update_option('authorized_creators',$creator_array,'no');
	
		
	update_user_meta($submitted_user_id,'wpclink_user_aprrove_status','update');
	update_user_meta($submitted_user_id,'wpclink_user_approve_type','creator');
	update_user_meta($user_id,'wpclink_previous_creator_id',$user_id_before);
	
		
	$already_accept = get_user_meta($submitted_user_id,'wpclink_terms_accept',true);
	if($already_accept == 2){
		update_user_meta($submitted_user_id,'wpclink_user_approve_mode',1);
	}else{
		update_user_meta($submitted_user_id,'wpclink_terms_accept',1);
	}

	
	// ALREADY
					
		}elseif($resposne_Array['status'] == 'notverified'){
				   ?>
		<div class="notice notice-error">
		<p><?php _e('Check you email inbox! We sent a verification email to '.$creator_user->user_email.'.<br> Please click on the Verify email button in that message within 24 hours to complete your registration','cl_text'); ?></p>
		</div>
	<?php
	}
	
	}
	}
}
}
// Forced Run
wpclink_register_user(true);
	
$creator_array = wpclink_get_option('authorized_creators');
$user_id = $creator_array[0]; ?>
<div class="wrap agreement_area">
  <?php wpclink_display_tabs_preferences_general();
  $cl_register_content = wpclink_get_option('wpclink_register_content'); ?>
  <div class="cl_subtabs_box">
<?php 
	$my_all_userlist = array(
	'blog_id'      => $GLOBALS['blog_id'],
	'role'         => '',
	'meta_value'   => '',
	'meta_compare' => '',
	'role__in'     => array('editor','author','administrator'),
	'role__not_in' => array(),
	'meta_key'     => '',
	'meta_query'   => array(),
	'date_query'   => array(),        
	'include'      => array(),
	'exclude'      => array(),
	'orderby'      => 'login',
	'order'        => 'ASC',
	'offset'       => '',
	'search'       => '',
	'number'       => '',
	'count_total'  => false,
	'fields'       => 'all',
	'who'          => '',
 	); 
	
	$show_userlist = get_users( $my_all_userlist );
// CLINK USER ID
$creator_array = wpclink_get_option('authorized_creators');
$clink_creator = $creator_array[0];
// CLINK IDENTIFIER
$clink_id = get_user_meta($clink_creator,'wpclink_party_ID',true);
// PARTY ID
$clink_party = wpclink_get_option('authorized_contact');
// PARTY IDENTIFIER
$party_id = get_user_meta($clink_party,'wpclink_party_ID',true);
$user_party_info = get_userdata($clink_party);
if(!empty($clink_party)){ ?>
<form id="party_form" method="post" action="<?php echo menu_page_url( 'cl_users', false );?>">
    <table class="form-table">
          <tr valign="top">
            
            <td>
            <h3><?php _e( 'Contact', 'cl_text' ); ?> <span class="icon-box" title="CLink User who is the contact for registrations"></span></h3>
            <div class="party_form" style="display:none">
            <select name="clink_party_user_id" id="clink_party_user_id">
			<?php $cl_party_user_id =  wpclink_get_option('authorized_contact'); ?>
            
			  <?php foreach( $show_userlist as $user ) :?>
                <?php 
			  echo '<option value="'.$user->ID.'" ';
			  if(!empty($cl_party_user_id)){
			  		echo selected($cl_party_user_id, $user->ID );
			  }
  			  echo '>'.$user->user_login.' ('.ucfirst($user->roles[0]).')</option>'; ?>
                <?php endforeach; ?>
              </select>
              <?php if($party_selected = wpclink_get_option('authorized_contact')){ ?>
              
              <input type="hidden" name="_update_party" value="1" />
              <?php }else{ ?>
              
              <?php } ?>
              <a class="party_form_cencel button">Cancel</a>
              </div>
               <?php if($party_selected = wpclink_get_option('authorized_contact')){ ?>
<span class="infoshow party">
<table class="form-table infotable">
  
  <tr valign="top">
	<?php $user_party_info = get_userdata($clink_party); ?>
    <td scope="row" class="d_col">
    <?php
	
	if($get_selection = get_user_meta($clink_party,'wpclink_user_status_contact',true)){
		if($get_selection == 'saved'){
			$party_selection_made = true;
		}else{
			$party_selection_made = false;
		}
	}else{
		$party_selection_made = false;
	}
	
	 if($party_selection_made){  ?>
    <a target="_blank" href="<?php echo WPCLINK_ID_URL; ?>/#objects/<?php echo get_user_meta($clink_party,'wpclink_party_ID',true);  ?>"><?php echo $user_party_info->display_name;  ?></a>
    <?php }else{ ?>
    <strong><?php echo $user_party_info->display_name;  ?></strong>
    <?php } ?>
    </th>
  </tr>
  <tr valign="top">
	<td scope="row"><?php 
	if($party_selection_made){ 
		echo 'CLink Party ID &nbsp;&nbsp;&nbsp;'. wpclink_do_icon_clink_ID(get_user_meta($clink_party,'wpclink_party_ID',true));
	}else{_e('<span class="helpreg"><strong>Registration pending</strong></span>','cl_text'); ?>&nbsp;&nbsp;&nbsp; <a  class="accept_require" data-name="<?php echo $user_party_info->display_name; ?>"><?php _e('What does this mean?','cl_text'); ?></a>
	<?php } ?></th>
  </tr>
  <?php
	$display_email_party = (isset($display_email_party)) ? $display_email_party : '';
   if($display_email_party == '1'){ ?>
  <tr valign="top">
    <td scope="row" class="t_col"><?php _e('Email','cl_text'); ?></th>
    <td scope="row" class="d_col"><?php echo $user_party_info->user_email;  ?></th>
  </tr>
  <?php } ?>
</table></span><a class="party_form_btn button">Change</a>
               <?php } ?>
              </td>
          </tr>
</table>
<?php wp_nonce_field( 'wpclink_select_user', 'wpclink_select_user_field' ); ?>
</form>
<?php }elseif(empty($clink_party)){ ?>
<form id="party_form" method="post" action="<?php echo menu_page_url( 'cl_users', false );?>">
    <table class="form-table party_select">
          <tr valign="top">
            <th scope="row"><?php _e( 'Contact', 'cl_text' ); ?> <span class="icon-box" title="CLink User who is the contact for registrations"></span></th>
            <td>
            
            <select name="clink_party_user_id" id="clink_party_user_id">
			<?php $cl_party_user_id =  wpclink_get_option('authorized_contact'); ?>
            
			  <?php foreach( $show_userlist as $user ) :?>
                <?php 
			  echo '<option value="'.$user->ID.'" ';
			  if(!empty($cl_party_user_id)){
			  		echo selected($cl_party_user_id, $user->ID );
			  }
  			  echo '>'.$user->user_login.' ('.ucfirst($user->roles[0]).')</option>'; ?>
                <?php endforeach; ?>
              </select>
              <?php if($party_selected = wpclink_get_option('authorized_contact')){ ?>
           <input class="button-primary spaced" value="Select" type="submit">
              <?php }else{ ?>
              <input class="button-primary spaced" value="Select" type="submit">
              <?php } ?>
              </td>
          </tr>
          </table>
</form>
<?php } 
if(!empty($clink_party)){ 
 if(!empty($clink_creator)){ ?>
<form id="creator_form" method="post" action="<?php echo menu_page_url( 'cl_users', false );?>">
<?php wp_nonce_field( 'wpclink_select_user', 'wpclink_select_user_field' ); ?>
<table class="form-table">          
          <tr valign="top">
            
            <td>
            <?php // Basic Info 
			$creator_array = wpclink_get_option('authorized_creators');
			$user_id = $creator_array[0];
			$creator_info = get_userdata($user_id);
			?>
            <h3><?php _e( 'Creator', 'cl_text' ); ?> <span class="icon-box" title="CLink User to whom the Creation is attributed to"></span></h3>
            <div class="creator_form" style="display:none">
            <select name="clink_user_id" id="clink_user_id">
			<?php 
			$creator_array = wpclink_get_option('authorized_creators');
			$cl_user_id = $creator_array[0]; ?>
			  <?php foreach( $show_userlist as $user ) :?>
                <?php 
			  echo '<option value="'.$user->ID.'" ';
			  if(!empty($cl_user_id)){
			  		echo selected($cl_user_id, $user->ID );
			  }
  			  echo '>'.$user->user_login.' ('.ucfirst($user->roles[0]).')</option>'; ?>
                <?php endforeach; ?>
                </select>  <a class="creator_form_cencel button">Cancel</a>
               <?php
			   
			   $creator_array = wpclink_get_option('authorized_creators');
			   $clink_user_selected = $creator_array[0];
			   
			    if($clink_user_selected > 0){ ?>
               <input type="hidden" name="_update_user" value="1" />
               
               <?php }else{ ?>
              
              <?php } ?>
              </div>
              <?php if($clink_creator){ 
if($get_selection_creator = get_user_meta($clink_creator,'wpclink_user_status_creator',true)){
		if($get_selection_creator == 'saved'){
			$creator_selection_made = true;
		}else{
			$creator_selection_made = false;
		}
	}else{
		$creator_selection_made = false;
	}
?>
<span class="infoshow creator">
<table class="form-table infotable">
<?php wp_nonce_field( 'wpclink_select_user', 'wpclink_select_user_field' ); ?>
  <tr valign="top">
    <td scope="row" class="d_col">
    <?php if($creator_selection_made){ ?>
    <a target="_blank" href="<?php echo WPCLINK_ID_URL; ?>/#objects/<?php echo $clink_id;  ?>"><?php echo $creator_info->display_name; ?></a>
    <?php }else{ ?>
    <strong><?php echo $creator_info->display_name; ?></strong>
    <?php } ?>
    </td></th>
  </tr>
  <tr valign="top">
    <td scope="row">
    <?php if($creator_selection_made){ ?>
	<?php echo 'CLink Party ID &nbsp;&nbsp;&nbsp;'.wpclink_do_icon_clink_ID($clink_id);  ?>
    <?php }else{_e('<span class="helpreg"><strong>Registration pending</strong></span>','cl_text'); ?>&nbsp;&nbsp;&nbsp;<a  class="accept_require" data-name="<?php echo $creator_info->display_name; ?>"><?php _e('What does this mean?','cl_text'); ?></a>
    <?php } ?></th>
  </tr>
  
  <?php 
	$display_email_creator = (isset($display_email_creator)) ? $display_email_creator : '';
	if($display_email_creator == 1){ ?>
  <tr valign="top">
    <td scope="row" class="t_col"><?php _e('Email','cl_text'); ?></th>
    <td scope="row" class="d_col"><?php echo $creator_info->user_email; ?></th>
  </tr>
  <?php } ?>
</table>
</span><a class="creator_form_btn button">Change</a>
               <?php } ?>
              
              </td>
          </tr>
        </table>
        <?php if($creator_flag == 'already'){ ?>
        <div class="notice notice-error">
        <p><?php _e('You may have lost your user settings in the WordPress CLink plugin','cl_text'); ?></p>
        <p><?php _e('Your Creator ID: '.wpclink_add_hyperlink_to_clink_ID($already_clink_id),'cl_text'); ?></p>
        
        <p><?php _e('Please check your <a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$already_clink_id.'">registration</a>','cl_text'); ?></p>
         <p><?php _e('You can update your WordPress profile with your CLink.ID from the registry','cl_text'); ?></p>
        
        <p> <input class="button" type="submit" value="Update" name="already_update" onclick="cl_creator_update(<?php echo $already_user_id; ?>)" /></p>
        </div>
        <input type="hidden" value="" name="already_action" id="already_action" />
        <?php }elseif($creator_flag == 'already_another'){ ?>
        <div class="notice notice-error">
        <p><?php _e('<strong>'.$another_displayname.'</strong> '.wpclink_add_hyperlink_to_clink_ID($already_clink_id_another).' already registered for <strong>'.$already_domain_another.'</strong>','cl_text'); ?></p>
		<p><?php _e('Personal Edition is limited to be used for one domain','cl_text'); ?></p>
        
        
		</div>
		<?php } ?>
</form>
<?php }else{ ?>
<form id="creator_form" method="post" action="<?php echo menu_page_url( 'cl_users', false );?>">
<?php wp_nonce_field( 'wpclink_select_user', 'wpclink_select_user_field' ); ?>
<table class="form-table creator_select">          
          <tr valign="top">
            <th scope="row"><?php _e( 'Creator', 'cl_text' ); ?> <span class="icon-box" title="CLink User to whom the Creation is attributed to"></span></th>
            <td><select name="clink_user_id" id="clink_user_id">
			<?php
			
			$creator_array = wpclink_get_option('authorized_creators');
			$cl_user_id = $creator_array[0]; ?>
			  <?php foreach( $show_userlist as $user ) :?>
                <?php 
			  echo '<option value="'.$user->ID.'" ';
			  if(!empty($cl_user_id)){
			  		echo selected($cl_user_id, $user->ID );
			  }
  			  echo '>'.$user->user_login.' ('.ucfirst($user->roles[0]).')</option>'; ?>
                <?php endforeach; ?>
                </select> 
               <?php
               
			   	$creator_array = wpclink_get_option('authorized_creators');
				$clink_user_selected = $creator_array[0];
			   
			   if($clink_user_selected > 0){ ?>
               
               <span class="infoshow">Identifier: <?php echo get_user_meta($clink_user_selected,'wpclink_party_ID',true);  ?></span>
               <?php }else{ ?>
              <input class="button-primary spaced" value="Select" onClick="wpclink_copyright_change()" type="submit">
              <?php } ?>
              
              </td>
          </tr>
        </table>
        <?php if($creator_flag == 'already'){ ?>
        <div class="notice notice-error">
        <p><?php _e('You may have lost your user settings in the WordPress CLink plugin','cl_text'); ?></p>
        <p><?php _e('Your Creator ID: '.wpclink_add_hyperlink_to_clink_ID($already_clink_id),'cl_text'); ?></p>
        
        <p><?php _e('Please check your <a class="button" target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$already_clink_id.'">registration</a>','cl_text'); ?></p>
         <p><?php _e('You can update your WordPress profile with your CLink.ID from the registry','cl_text'); ?></p>
        
        <p> <input class="button" type="submit" value="Update" name="already_update" onclick="cl_creator_update(<?php echo $already_user_id; ?>)" /></p>
        </div>
        <input type="hidden" value="" name="already_action" id="already_action" />
        <?php }elseif($creator_flag == 'already_another'){ ?>
        <div class="notice notice-error">
        <p><?php _e('<strong>'.$another_displayname.'</strong> '.wpclink_add_hyperlink_to_clink_ID($already_clink_id_another).' already registered for <strong>'.$already_domain_another.'</strong>','cl_text'); ?></p>
		<p><?php _e('Personal Edition is limited to be used for one domain','cl_text'); ?></p>
        
        
		</div>
		<?php } ?>
</form>
<?php }
} ?>
<?php if(!empty($clink_party) and !empty($clink_creator)){
	$right_holder = wpclink_get_option('rights_holder');
	if(empty($right_holder)){ ?>
<form id="copyright_form" method="post" action="<?php echo menu_page_url( 'cl_users', false );?>">
<?php wp_nonce_field( 'wpclink_select_user', 'wpclink_select_user_field' ); ?>
<table class="form-table creator_select">
      <tbody>
      <tr class="copyright_company">
      <th scope="row"><?php _e( 'Rights Holder', 'cl_text' ); ?> <span class="icon-box" title="Sole owner of copyright to all registered content. Must be Creator or Party"></span></th><td>
<?php
// Party
$clink_party_id = wpclink_get_option('authorized_contact');
$party_user_info = get_userdata($clink_party_id);
// Creator
$creator_array = wpclink_get_option('authorized_creators');
$clink_creator_id = $creator_array[0];
$creator_user_info = get_userdata($clink_creator_id);
$right_holder = wpclink_get_option('rights_holder');
if($right_holder == 'party'){
	$right_holder_id = wpclink_get_option('authorized_contact');
	
	$identifier = get_user_meta($clink_party_id,'wpclink_party_ID',true);
	
}elseif($right_holder == 'creator'){
	
	$creator_array = wpclink_get_option('authorized_creators');
	$right_holder_id = $creator_array[0];
	
	$identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	
} ?>
<select name="right_holder" id="right_holder">
<option value="party" <?php if($right_holder == 'party') echo 'selected="selected"'; ?>><?php echo $party_user_info->display_name; ?> (Contact)</option>
<option value="creator" <?php if($right_holder == 'creator') echo 'selected="selected"'; ?>><?php echo $creator_user_info->display_name; ?> (Creator)</option>
</select>
<input class="button-primary spaced" value="Select" type="submit">
</td>
</tr>
</tbody>
</table>
</form>
<?php }else{ ?>
<form id="copyright_form" method="post" action="<?php echo menu_page_url( 'cl_users', false );?>">
<?php wp_nonce_field( 'wpclink_select_user', 'wpclink_select_user_field' ); ?>
<table class="form-table">
      <tbody>
       <tr class="copyright_company">
          <td>
<h3>Rights Holder <span class="icon-box hyperlink" title="Rights Holder on all succeeding registered content. Must be the Creator. See documentation."></span></h3>
			  
<?php 
			// Party
$clink_party_id = wpclink_get_option('authorized_contact');
$party_user_info = get_userdata($clink_party_id);
// Creator
$creator_array = wpclink_get_option('authorized_creators');
$clink_creator_id = $creator_array[0];
$creator_user_info = get_userdata($clink_creator_id);
$right_holder = wpclink_get_option('rights_holder');
if($right_holder == 'party'){
	$right_holder_id = wpclink_get_option('authorized_contact');
	
	$identifier = get_user_meta($clink_party_id,'wpclink_party_ID',true);
	
}elseif($right_holder == 'creator'){
	
	$creator_array = wpclink_get_option('authorized_creators');
	$right_holder_id = $creator_array[0];
	$identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	
}
			 ?>
<span class="infoshow copyright">
<table class="form-table infotable">
   	<?php if(empty($right_holder)){ ?>
    <tr valign="top">
    <td scope="row"><?php _e( 'Not Selected', 'cl_text' ); ?></td>
    </tr>
  	<?php }else{ ?>
  <tr valign="top">
    <td scope="row">
    <?php if(!empty($identifier)){  ?>
    <a href="<?php echo WPCLINK_ID_URL; ?>/#objects/<?php echo $identifier; ?>"><?php 
	$right_holder_info = get_userdata($right_holder_id);
	echo $right_holder_info->display_name;
	?></a>
	<?php }else{
		$right_holder_info = get_userdata($right_holder_id);
		echo '<strong>'.$right_holder_info->display_name.'</strong>'; 
	} ?>
	
	</td>
    </tr>
    <tr valign="top">
    <td><?php if($right_holder == 'creator'){
		echo (!empty($identifier) ? 'CLink Party ID &nbsp;&nbsp;&nbsp;'.wpclink_do_icon_clink_ID($identifier) : '<span class="helpreg"><strong>Registration pending</strong></span>&nbsp;&nbsp;&nbsp;<a class="accept_require" data-name="'.$right_holder_info->display_name.'">What does this mean?</a>');
	}elseif($right_holder == 'party'){
		echo (!empty($identifier) ? 'CLink Party ID &nbsp;&nbsp;&nbsp;'.wpclink_do_icon_clink_ID($identifier) : '<span class="helpreg"><strong>Registration pending</strong></span>&nbsp;&nbsp;&nbsp;<a class="accept_require" data-name="'.$right_holder_info->display_name.'">What does this mean?</a>');
	} ?></td>
    </tr>
    <?php } ?>
</table>
</span>
</td>
</tr>
</tbody>
</table>
</form>
<?php }
}
$current_user_id = get_current_user_id();
$accept = get_user_meta( $current_user_id, 'wpclink_terms_accept', true ); 
if($accept == false || $accept == 1){
	$action_allow = '1';
}else{
	$action_allow = '0';
}
?>
<script type="application/javascript">
	// Tooltip for Info 
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
	// Party Change
    jQuery('.party_form_btn').click(function() {
        jQuery('.party_form').slideToggle('fast', function() {
            if (jQuery('.party_form').css("display") == "none") {
               jQuery("#party_form").submit(); 
            } else {
            }
        });
        jQuery('.infoshow.party').slideToggle('fast');
    });
	// Creator Change
    jQuery('.creator_form_btn').click(function() {
        jQuery('.creator_form').slideToggle('fast', function() {
            if (jQuery('.creator_form').css("display") == "none") {
			jQuery("#creator_form").submit();
                
            } else {
            }
        });
        jQuery('.infoshow.creator').slideToggle('fast');
    });
	
	// Creator Cancel
    jQuery('.creator_form_cencel').click(function() {
        jQuery('.infoshow.creator').slideToggle('fast');
        jQuery('.creator_form').slideToggle('fast', function() {
        });
    });
	// Party Cancel
    jQuery('.party_form_cencel').click(function() {
        jQuery('.infoshow.party').slideToggle('fast');
        jQuery('.party_form').slideToggle('fast', function() {
        });
    });
	// Creator Verfiy
    function cl_creator_verify() {
        jQuery('#already_action').attr('value', 'already_verify');
        jQuery('#creator_form').submit();
    }
	// Creator Update
    function cl_creator_update(selected_val) {
 		jQuery('#creator_form').append('<input type="hidden" name="_record_update_creator" value="1" />');
        jQuery('#clink_user_id').val(selected_val);
        jQuery('#creator_form').submit();
    }
	// Party Update
    function cl_party_update(selected_val) {
        jQuery('#party_form').append('<input type="hidden" name="_update_party" value="1" />');
        jQuery('#party_form').append('<input type="hidden" name="_record_update" value="1" />');
        jQuery('#clink_party_user_id').val(selected_val);
        jQuery('#party_form').submit();
    }
	// Party Form Submit
    jQuery("#party_form").submit(function(event) {
        var action_allow = <?php echo $action_allow; ?>;
        if (action_allow == 1) {
            alert("Please accept above Terms and Conditions");
            event.preventDefault();
        }
    });
	// Creator Form Submit
    jQuery("#creator_form").submit(function(event) {
        var action_allow = <?php echo $action_allow; ?>;
        if (action_allow == 1) {
            alert("Please accept above Terms and Conditions");
            event.preventDefault();
        }
    });
	// Copyright Form Submit
    jQuery("#copyright_form").submit(function(event) {
        var action_allow = <?php echo $action_allow; ?>;
        if (action_allow == 1) {
            alert("Please accept above Terms and Conditions");
            event.preventDefault();
        }
    });
	jQuery(document).ready(function(){
		jQuery('.accept_require').click(function(){
			var clink_user_name;
			clink_user_name = jQuery(this).attr("data-name");
			jQuery('.cl_user_print').text(clink_user_name);
			jQuery( "#cl-acceptance-required" ).dialog();
		});

	});
	
</script>

<div id="cl-acceptance-required" title="What does it means?">
  <p><span class="cl_user_print"></span> <?php _e('has not accepted the Terms and Conditions required for CLink Users','cl_text'); ?></p>
</div>
  </div>
<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
</div>
<?php 
}