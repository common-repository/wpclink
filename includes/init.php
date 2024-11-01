<?php
/**
 * CLink Initialization Functions
 *
 * CLink initialization and foundation functions 
 *
 * @package CLink
 * @subpackage System
 */

 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Add extra query veriable cl_post_status on post update
add_filter('redirect_post_location', function($location)
{
    global $post;
    if (
        (isset($_POST['publish']) || isset($_POST['save'])) &&
        preg_match("/post=([0-9]*)/", $location, $match) &&
        $post &&
        $post->ID == $match[1] &&
        (isset($_POST['publish']) || $post->post_status == 'publish') && // Publishing draft or updating published post
        $pl = get_permalink($post->ID)
    ) {
        if (isset($_POST['publish'])) {
			
			return add_query_arg( 'cl_post_status', 'publish', $location );
           
        } elseif ($ref = wp_get_original_referer()) {
           
		   	return add_query_arg( 'cl_post_status', 'referer', $location );
        } else {
           return add_query_arg( 'cl_post_status', '0', $location );
        }
    }
    return $location;
});
function wpclink_action_do(){
		
	if(isset($_GET['page']) and $_GET['page'] == 'content_link_post.php'){
	 
	  // Content Link Post Actions
		if(wpclink_register_linked_creations_bulk('post')) 	wpclink_notif_print('Linked Posts has been published.','success');
		if(wpclink_register_linked_creation('post')) wpclink_notif_print('Linked Post has been published','success');
		
		
	  
	}elseif(isset($_GET['page']) and $_GET['page'] == 'content_link_page.php'){
		
		if(wpclink_register_linked_creations_bulk('page'))	wpclink_notif_print('Linked Pages has been published.','success');
		if(wpclink_register_linked_creation('page')) wpclink_notif_print('Linked Page has been published','success');
			
				
    }elseif(isset($_GET['page']) and $_GET['page'] == 'content_link_media.php'){
		
		if(wpclink_register_linked_creation('attachment')) wpclink_notif_print('Linked Page has been published','success');
		
		//if(wpclink_register_linked_creations_bulk('attachment'))	wpclink_notif_print('Linked Pages has been published.','success');
		
		
	}elseif(isset($_GET['page']) and $_GET['page'] == 'wpclink_post_skip.php'){
		
				
	}elseif(isset($_GET['page']) and $_GET['page'] == 'cl_page_skip.php'){
		
	
	}elseif(isset($_GET['page']) and $_GET['page'] == 'cl_notifications'){
		if(wpclink_update_post())				wpclink_notif_print('Post has been updated','success');
		if(wpclink_update_page())				wpclink_notif_print('Page has been updated','success');
	}
	
	
	
}
add_action( 'admin_init', 'wpclink_action_do');
/**
 * CLink Update Basic Options
 * 
 */
function wpclink_update_basic_options(){
	// Admin Side
	if(is_admin()){
		if ( current_user_can('manage_options') ) {
			if(isset($_POST['teriority_code'])){
				
				// Clink rules
				if(wpclink_is_acl_r_page()){
					wpclink_notif_print('Action cannot be perform.','error');
				}else{
				
				$teriority_code = $_POST['teriority_code'];
				
				// Update
				wpclink_update_option('territory_code',$teriority_code);
				wpclink_notif_print('Changes have been saved.','success');
	
				}
			}
	
		}
	}
}
// Register clink update options function 
add_action('admin_init','wpclink_update_basic_options');
// Auto assign the user for linked post content
add_action('admin_init','wpclink_auto_assign_save');
/**
 * Auto Assign the User When Linked Post Content is Create
 * 
 */
function wpclink_auto_assign_save(){
$post_data = $_POST;	
if(isset($post_data['post_author_assign']) and isset($post_data['post_cat_assign'])){
// Author		
  $new_value_author = $post_data['post_author_assign'];
  if ( wpclink_get_option( 'post_author_assign' ) !== false ) {
	  wpclink_update_option( 'post_author_assign', $new_value_author );
  
  } else {
	  $deprecated = null;
	  $autoload = 'no';
	  wpclink_add_option( 'post_author_assign', $new_value_author, $deprecated, $autoload );
  }
// Cat
  $new_value_cat = $post_data['post_cat_assign'];
  if ( wpclink_get_option( 'post_cat_assign' ) !== false ) {
	  wpclink_update_option( 'post_cat_assign', $new_value_cat );
  
  } else {
	  $deprecated = null;
	  $autoload = 'no';
	  wpclink_add_option( 'post_cat_assign', $new_value_cat, $deprecated, $autoload );
  }
}
}
/**
 * Auto Assign the User When Linked Page Content is Create
 * 
 */
function wpclink_auto_assign_save_page(){
$post_data = $_POST;	
if(isset($post_data['page_author_assign'])){
// Author		
  $new_value_author = $post_data['page_author_assign'];
  if ( wpclink_get_option( 'page_author_assign' ) !== false ) {
	  wpclink_update_option( 'page_author_assign', $new_value_author );
  
  } else {
	  $deprecated = null;
	  $autoload = 'no';
	  wpclink_add_option( 'page_author_assign', $new_value_author, $deprecated, $autoload );
  }
}
}
// Auto assign the user for linked page content
add_action('admin_init','wpclink_auto_assign_save_page');
/**
 * CLink Welcome Message Update
 * 
 */
function wpclink_clink_welcome_status_update(){
	if(isset($_GET['startup']) and $_GET['startup'] == 1){
		wpclink_update_option('wpclink_welcome_status',$_GET['startup']);
	}	
}
add_action('init','wpclink_clink_welcome_status_update');
/**
 * CLink Accept Terms and Condition First Time Update
 * 
 */
function wpclink_accept_terms_form_first(){
	
	if(isset($_POST['cl_user_accept_first']) and $_POST['cl_user_accept_first'] == 1){
		
		$current_user_id = get_current_user_id();
		// Auto Action #1
		//update_user_meta($current_user_id,'wpclink_user_approve_mode',1);
		// Accepts Terms #2
		update_user_meta($current_user_id,'wpclink_terms_accept',2);
	}
}
// Register first time accept update function
add_action( 'admin_init', 'wpclink_accept_terms_form_first');
/**
 * CLink User Accept Terms and Condition HTML Form
 * 
 */
function wpclink_accept_terms_form(){
	
	if(isset($_POST['cl_user_accept']) and $_POST['cl_user_accept'] == 1){
		
		$current_user_id = get_current_user_id();
		
		$action = get_user_meta($current_user_id ,'wpclink_user_aprrove_status',true);
		$type = get_user_meta($current_user_id ,'wpclink_user_approve_type',true);
		
		if($type == 'party' and $action == 'create'){
			
		// CREATE PARTY
		wpclink_quick_create_party($current_user_id);
					
		}
		if($type == 'party' and $action == 'update'){
		
		$previous_party_id = get_user_meta($current_user_id ,'wpclink_previous_contact_id',true);
		
		// UPDATE PARTY
		wpclink_quick_update_party($current_user_id, $previous_party_id);
		// REMOVE ACTION
		update_user_meta($previous_party_id,'wpclink_user_status_contact','unsaved');
			
		}
		if($type == 'creator' and $action == 'create'){
		$previous_creator_id = get_user_meta($current_user_id ,'wpclink_previous_creator_id',true);			
		// CREATE CREATOR
		wpclink_quick_create_creator($current_user_id, $previous_creator_id);
			
		}
		if($type == 'creator' and $action == 'update'){
			
		$previous_creator_id = get_user_meta($current_user_id ,'wpclink_previous_creator_id',true);
		// UPDATE CREATOR
		wpclink_quick_update_creator($current_user_id,$previous_creator_id);
		// REMOVE ACTION
		update_user_meta($previous_creator_id,'wpclink_user_status_creator','unsaved');
			
		}
	}
	
}
// Register accept terms and condition form
add_action( 'admin_init', 'wpclink_accept_terms_form');
/**
 * CLink Auto Apply Action Register Creator | Party After Accepted Terms and Conditions
 * 
 * @param boolean $forced default true
 */
function wpclink_register_user($forced = true){
	
	// Not Run on the Users Page
	if(isset($_GET['page']) and $forced == false){
		if($_GET['page'] == 'cl_users'){
			return false;	
		}
	}
	
	$user_id = get_current_user_id();
	
	$auto_action = get_user_meta($user_id,'wpclink_user_approve_mode',true);
	
	if($auto_action == 1){	
	}else{
		
	if(wpclink_user_has_creator_list($user_id)){
	
		// Creator ID
		$creators_id = $user_id;
		
	}
	$creator_auto_action = get_user_meta($creators_id,'wpclink_user_approve_mode',true);
	
	
	// Party ID
	$clink_party_id = wpclink_get_option('authorized_contact');
	$party_auto_action = get_user_meta($clink_party_id,'wpclink_user_approve_mode',true);
	
	
		// IF CREATOR OR PARTY SELECTED
		if($creator_auto_action == 1){
			$user_id = $creators_id;
		}else if($party_auto_action == 1){
			$user_id = $clink_party_id;
		}
		
		// Double Check has auto action selected
		$auto_action = get_user_meta($user_id,'wpclink_user_approve_mode',true);
	}
	
	
	
	
	if($auto_action == 1){
		
		$action = get_user_meta($user_id ,'wpclink_user_aprrove_status',true);
		$type = get_user_meta($user_id ,'wpclink_user_approve_type',true);
		
		if($type == 'party' and $action == 'create'){
			
		// CREATE PARTY
		wpclink_quick_create_party($user_id);
		
		// De-Active
		update_user_meta($user_id,'wpclink_user_approve_mode',false);
			
					
		}
		if($type == 'party' and $action == 'update'){
		
		$previous_party_id = get_user_meta($user_id ,'wpclink_previous_contact_id',true);
		
		// UPDATE PARTY
		wpclink_quick_update_party($user_id, $previous_party_id);
		// REMOVE ACTION
		update_user_meta($previous_party_id,'wpclink_user_status_contact','unsaved');
		// De-Active
		update_user_meta($user_id,'wpclink_user_approve_mode',false);
		}
		if($type == 'creator' and $action == 'create'){
		$previous_creator_id = get_user_meta($user_id ,'wpclink_previous_creator_id',true);			
		// CREATE CREATOR
		wpclink_quick_create_creator($user_id, $previous_creator_id);
			// De-Active
		update_user_meta($user_id,'wpclink_user_approve_mode',false);
			
			
		}
		if($type == 'creator' and $action == 'update'){
			
		$previous_creator_id = get_user_meta($user_id ,'wpclink_previous_creator_id',true);
		// UPDATE CREATOR
		wpclink_quick_update_creator($user_id,$previous_creator_id);
		// REMOVE ACTION
		update_user_meta($previous_creator_id,'wpclink_user_status_creator','unsaved');
		// De-Active
		update_user_meta($user_id,'wpclink_user_approve_mode',false);
			
			
		}
		
	}
}
// Register auto apply action function for admin
add_action( 'admin_init', 'wpclink_register_user');
/**
 * CLink Referent List Post Save and Redirection
 * 
 */
function wpclink_set_referent_posts() {
	
	$current_page = menu_page_url( 'cl-mycontent-posts', false );

	if ( !isset( $_GET[ 'page' ] ) ) return false;

	if ( $_GET[ 'page' ] == 'cl-restriction' || $_GET[ 'page' ] == 'cl-restriction-reuse' || $_GET[ 'page' ] == 'wpclink-creation-referent-media' ) {

	} else {
		return false;
	}
	if ( wpclink_is_acl_r_page() ) {
		return false;
	}
	if ( isset( $_GET[ 'paged' ] ) ) {
		$current_page .= '&paged=' . $_GET[ 'paged' ];
	}
	if ( $_GET[ 'page' ] == 'cl-restriction' ) {

		$current_page = menu_page_url( 'cl-restriction', false );
		if ( isset( $_POST[ 'cl_include_ref_form' ] ) ) {
			if ( $_POST[ 'referent_list' ] == 1 ) {
				wpclink_update_option( 'exclude_referent', $_POST[ 'referent_list' ] );
			} else {
				wpclink_update_option( 'exclude_referent', 0 );
			}
		}
		if ( wp_verify_nonce( $_POST['wpclink_select_license_field'], 'wpclink_select_license') and isset( $_REQUEST[ 'post' ] ) and ( isset( $_REQUEST[ 'action' ] ) || isset( $_REQUEST[ 'action2' ] ) ) ) {
			$post_list = $_REQUEST[ 'post' ];
			$license_class = $_REQUEST[ 'license_class' ];
			$license_version = $_REQUEST[ 'license_version' ];
			$taxonomy_permission = $_REQUEST[ 'taxonomy_permission_slot' ];


			if ( $restrict = wpclink_get_option( 'referent_posts' ) ) {

				// Update CLink.ID
				wpclink_register_make_referent( $single_post, $license_class, $current_page );

				$posts = array_merge( $restrict, $post_list );
				wpclink_update_option( 'referent_posts', array_unique( $posts ) );

				foreach ( $post_list as $single_post ) {
					if ( $license_class == 'personal' || $license_class == '0' ) {
						update_post_meta( $single_post, 'wpclink_creation_license_class', $license_class );
						update_post_meta( $single_post, 'wpclink_programmatic_right_categories', $taxonomy_permission );
					}

					// License Version            
					update_post_meta( $single_post, 'wpclink_post_license_version', $license_version );
					$right_holder_id = get_post_field( 'post_author', $single_post );

					// Copyright Owner ID
					update_post_meta( $single_post, 'wpclink_rights_holder_user_id', $right_holder_id );

					// Creation Pre Auth Efeect Date
					wpclink_add_date_effect_pre_auth( $single_post );
				}

				$current_page = add_query_arg( 'updated_post', '1', $current_page );
				wp_redirect( $current_page, 301 );
				exit;
			} else {
				wpclink_update_option( 'referent_posts', array_unique( $post_list ) );

				foreach ( $post_list as $single_post ) {
					if ( $license_class == 'personal' || $license_class == '0' ) {
						// Update CLink.ID
						wpclink_register_make_referent( $single_post, $license_class, $current_page  );
						
						update_post_meta( $single_post, 'wpclink_creation_license_class', $license_class );
						update_post_meta( $single_post, 'wpclink_programmatic_right_categories', $taxonomy_permission );
						
					}

					// Creation Pre Auth Efeect Date
					wpclink_add_date_effect_pre_auth( $single_post );
					$right_holder_id = get_post_field( 'post_author', $single_post );

					// Copyright Owner ID
					update_post_meta( $single_post, 'wpclink_rights_holder_user_id', $right_holder_id );
				}

				$current_page = add_query_arg( 'updated_post', '1', $current_page );
				wp_redirect( $current_page, 301 );
				exit;
			}
		} elseif ( wp_verify_nonce( $_POST['wpclink_select_license_field'], 'wpclink_select_license') and isset( $_REQUEST[ 'referent' ] ) and $_REQUEST[ 'referent' ] > 0 ) {

			$referent_id = array( $_REQUEST[ 'referent' ] );
			$referent_id_only = $_REQUEST[ 'referent' ];
			$license_class = 'personal';
			$taxonomy_permission = $_REQUEST[ 'taxonomy_permission_slot' ];
			$license_version = $_REQUEST[ 'license_version' ];
			$license_data = $_REQUEST[ 'e_sign' ];

			if ( $restrict = wpclink_get_option( 'referent_posts' ) ) {
				
				// Update CLink.ID
				wpclink_register_make_referent( $referent_id_only, $license_class, $current_page );
				
				$referent_ids = array_merge( $restrict, $referent_id );
				wpclink_update_option( 'referent_posts', array_unique( $referent_ids ) );

				// License Class          
				if ( $license_class == 'personal' || $license_class == '0' ) {
					update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );

					// Taxonomy Permission
					update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );

					
				}

				// Creation Pre Auth Efeect Date
				wpclink_add_date_effect_pre_auth( $referent_id_only );
				$right_holder_id = get_post_field( 'post_author', $referent_id_only );

				// Copyright Owner ID
				update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', $right_holder_id );
				$current_page = add_query_arg( 'updated_post', '1', $current_page );
				wp_redirect( $current_page, 301 );
				exit;

			} else {
				// License Class          
				if ( $license_class == 'personal' || $license_class == '0' ) {
					// Update CLink.ID
					wpclink_register_make_referent( $referent_id_only, $license_class, $current_page );
					
					update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );
					// Taxonomy Permission
					update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );
					
				}
				wpclink_update_option( 'referent_posts', array_unique( $referent_id ) );

				// Creation Pre Auth Efeect Date
				wpclink_add_date_effect_pre_auth( $referent_id_only );

				$right_holder_id = get_post_field( 'post_author', $referent_id_only );

				// Copyright Owner ID
				update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', $right_holder_id );

				$current_page = add_query_arg( 'updated_post', '1', $current_page );
				wp_redirect( $current_page, 301 );
				exit;
			}
		} elseif ( wp_verify_nonce( $_POST['nonces'], 'remove_referent_nonce') and isset( $_REQUEST[ 'remove_referent' ] )and $_REQUEST[ 'remove_referent' ] > 0 ) {

			$remove_referent_id = array( $_GET[ 'remove_referent' ] );
			$remove_id_int = $_GET[ 'remove_referent' ];

			if ( $restrict = wpclink_get_option( 'referent_posts' ) ) {
				
				// Remove from CLink.ID
				wpclink_register_remove_referent( $remove_id_int, $current_page );
				
				$remove_referent_ids = array_diff( $restrict, $remove_referent_id );
				wpclink_update_option( 'referent_posts', array_unique( $remove_referent_ids ) );

				// Remove License Class
				delete_post_meta( $remove_id_int, 'wpclink_creation_license_class' );

				

				// Remove License Programmatic
				delete_post_meta( $remove_id_int, 'wpclink_programmatic_right_categories' );

				$current_page = add_query_arg( 'removed_post', '1', $current_page );
				wp_redirect( $current_page, 301 );
				exit;
			} else {
				wp_redirect( $current_page, 301 );
				exit;
			}
		}

	} elseif ( $_GET[ 'page' ] == 'cl-restriction-reuse' ) {


		$current_page = menu_page_url( 'cl-restriction-reuse', false );
		if ( isset( $_GET[ 'post' ] )and( isset( $_GET[ 'action' ] ) || isset( $_GET[ 'action2' ] ) ) ) {
			$post_list = $_GET[ 'post' ];
			if ( $restrict = wpclink_get_option( 'referent_posts' ) ) {

				

				foreach ( $post_list as $single_post ) {
					// Remove from CLink.ID
					wpclink_register_remove_referent( $single_post, $current_page );

					// Remove License Class
					delete_post_meta( $single_post, 'wpclink_creation_license_class' );

					// Remove License Programmatic
					delete_post_meta( $single_post, 'wpclink_programmatic_right_categories' );
				}
				
				$posts = array_diff( $restrict, $post_list );
				wpclink_update_option( 'referent_posts', array_unique( $posts ) );

				$current_page = add_query_arg( 'removed_post', '1', $current_page );
				wp_redirect( $current_page, 301 );
				exit;
			} else {
				wp_redirect( $current_page, 301 );
				exit;
			}
		} elseif ( wp_verify_nonce($_GET['nonces'], 'remove_referent_nonce') and isset( $_GET[ 'remove_referent' ] )and $_GET[ 'remove_referent' ] > 0 ) {

			$remove_referent_id = array( $_GET[ 'remove_referent' ] );
			$remove_id_int = $_GET[ 'remove_referent' ];

			if ( $restrict = wpclink_get_option( 'referent_posts' ) ) {
				
				// Remove from CLink.ID
				wpclink_register_remove_referent( $remove_id_int, $current_page );
				
				$remove_referent_ids = array_diff( $restrict, $remove_referent_id );
				wpclink_update_option( 'referent_posts', array_unique( $remove_referent_ids ) );
				$current_page = add_query_arg( 'removed_post', '1', $current_page );

				// Remove License Class
				delete_post_meta( $remove_id_int, 'wpclink_creation_license_class' );

				// Remove License Programmatic
				delete_post_meta( $remove_id_int, 'wpclink_programmatic_right_categories' );

				

				wp_redirect( $current_page, 301 );
				exit;
			} else {
				wp_redirect( $current_page, 301 );
				exit;
			}
		}

	} else if ( $_GET[ 'page' ] == 'wpclink-creation-referent-media' ) {

		$current_page = menu_page_url( 'wpclink-creation-referent-media', false );

		if ( isset( $_GET[ 'remove_referent' ] ) ) {

			$remove_referent_id = array( $_GET[ 'remove_referent' ] );
			$remove_id_int = $_GET[ 'remove_referent' ];

			if ( $restrict = wpclink_get_option( 'referent_attachments' ) ) {
				$remove_referent_ids = array_diff( $restrict, $remove_referent_id );
				wpclink_update_option( 'referent_attachments', array_unique( $remove_referent_ids ) );

				// Remove License Class
				delete_post_meta( $remove_id_int, 'wpclink_creation_license_class' );

				// Remove from CLink.ID
				wpclink_register_remove_referent_media( $remove_id_int );

				// Remove License Programmatic
				delete_post_meta( $remove_id_int, 'wpclink_programmatic_right_categories' );

				$current_page = add_query_arg( 'removed_post', '1', $current_page );
				wp_redirect( $current_page, 301 );
				exit;

			} else {
				wp_redirect( $current_page, 301 );
				exit;
			}

		}

	}
}
// Register referent post list and redirection function
add_action('admin_init','wpclink_set_referent_posts');
/**
 * CLink Referent List Page Save and Redirection
 * 
 */
function wpclink_set_referent_pages(){
	
if(!isset(($_GET['page']))) return false;
   
$current_page = menu_page_url( 'cl-mycontent-posts', false );
if($_GET['page'] == 'cl-restriction-page' || $_GET['page'] == 'cl-restriction-page-available'){
	
}else{
	return false;	
}
	if(wpclink_is_acl_r_page()){
		return false;
	}
if(isset($_GET['paged'])){
	$current_page.='&paged='.$_GET['paged'];
}
if($_GET['page'] == 'cl-restriction-page'){
	
$current_page = menu_page_url( 'cl-restriction-page', false );
if(isset($_GET['post']) and (isset($_GET['action']) || isset($_GET['action2']))){
	  $post_list = $_GET['post'];
	  
	  if($restrict = wpclink_get_option('referent_pages')){
		  $posts = array_diff($restrict,$post_list);
		  wpclink_update_option('referent_pages',array_unique($posts));
		  
		   foreach($post_list as $single_post){
			  // Remove from CLink.ID
		  	wpclink_register_remove_referent($single_post);
			   
			// Remove License Class
		  	delete_post_meta( $single_post, 'wpclink_creation_license_class' );
			   
			// Remove License Programmatic
		  delete_post_meta( $remove_id_int, 'wpclink_programmatic_right_categories');
			   
		  }
		  
		  $current_page = add_query_arg('updated_post','1',$current_page);
		  wp_redirect( $current_page, 301);
		   exit;
	  }else{
		 // wpclink_update_option('referent_pages',array_unique($post_list));
		  $current_page = add_query_arg('updated_post','1',$current_page);
		   wp_redirect( $current_page, 301);
		   exit;
	  }
  }elseif( wp_verify_nonce($_GET['nonces'], 'remove_referent_nonce') and isset($_GET['remove_referent']) and $_GET['remove_referent'] > 0){
	  
	$remove_referent_id = array($_GET['remove_referent']);
	$remove_id_int = $_GET['remove_referent'];
	
	  if($restrict = wpclink_get_option('referent_pages')){
		  $referent_ids = array_diff($restrict,$remove_referent_id);
		  wpclink_update_option('referent_pages',array_unique($referent_ids));
		  $current_page = add_query_arg('removed_post','1',$current_page);
		  
		  // Remove License Class
		  delete_post_meta( $remove_id_int, 'wpclink_creation_license_class' );
		  
		  // Remove License Programmatic
		  delete_post_meta( $remove_id_int, 'wpclink_programmatic_right_categories');
		  
		  // Remove from CLink.ID
		  wpclink_register_remove_referent($remove_id_int);
		  
		  wp_redirect( $current_page, 301);
		   exit;
	  }else{
		  
		  wp_redirect( $current_page, 301);
		   exit;
	  }
}
}elseif($_GET['page'] == 'cl-restriction-page-available'){
	
$current_page = menu_page_url( 'cl-restriction-page-available', false );
if(isset($_POST['cl_include_ref_form_page'])){
	if($_POST['referent_list_page'] == 1){
		wpclink_update_option('exclude_referent_page',$_POST['referent_list_page']);
	}else{
		wpclink_update_option('exclude_referent_page',0);
	}
}
if(isset($_REQUEST['post']) and (isset($_REQUEST['action']) || isset($_REQUEST['action2']))){
	   $post_list = $_REQUEST['post'];
	  $license_class = $_REQUEST['license_class'];
	  $license_version = $_REQUEST['license_version'];
	  $taxonomy_permission = $_REQUEST['taxonomy_permission_slot'];
	  
	  if($restrict = wpclink_get_option('referent_pages')){
		  
		  $posts = array_merge($restrict,$post_list);
		  wpclink_update_option('referent_pages',array_unique($posts));
		  
		   foreach($post_list as $single_post){
			if ( $license_class == 'personal' || $license_class == '0' ) {
				
			  update_post_meta( $single_post, 'wpclink_creation_license_class', $license_class );
				
			  update_post_meta( $single_post, 'wpclink_programmatic_right_categories', $taxonomy_permission );
				
			     // Update CLink.ID
			  wpclink_register_make_referent( $single_post, $license_class);
			  
			}
			   
			     $right_holder_id = get_post_field( 'post_author', $single_post );
			
				// License Version			
			  update_post_meta( $single_post, 'wpclink_post_license_version', $license_version );
			  
			  
			  	// Copyright Owner ID
			 update_post_meta( $single_post, 'wpclink_rights_holder_user_id', $right_holder_id );
			 
			  	// Creation Pre Auth Efeect Date
			wpclink_add_date_effect_pre_auth($single_post);
			
			  
			  
		  }
		  
		  
		 
		  $current_page = add_query_arg('updated_post','1',$current_page);
		  wp_redirect( $current_page, 301);
		   exit;
		  
	  }else{
		  
		  wpclink_update_option('referent_pages',$post_list);
		  
		  foreach($post_list as $single_post){
			if ( $license_class == 'personal' || $license_class == '0' ) {
				
			  update_post_meta( $single_post, 'wpclink_creation_license_class', $license_class );
				
			  update_post_meta( $single_post, 'wpclink_programmatic_right_categories', $taxonomy_permission );
			     // Update CLink.ID
			  wpclink_register_make_referent( $single_post, $license_class);
			  
			}
		  
			    $right_holder_id = get_post_field( 'post_author', $single_post );
			  
			// Copyright Owner ID
			 update_post_meta( $single_post, 'wpclink_rights_holder_user_id', $right_holder_id );
			  
			   // Creation Pre Auth Efeect Date
			wpclink_add_date_effect_pre_auth($single_post);
			
			// Pre Authorize Popup Accept
		   	wpclink_accept_pre_auth_popups($single_post,'wpclink_reuse_pre_auth_accept_date');
		  }
		  $current_page = add_query_arg('updated_post','1',$current_page);
		   wp_redirect( $current_page, 301);
		   exit;
	  }
}elseif(isset($_REQUEST['referent']) and $_REQUEST['referent'] > 0){
	  
	$referent_id = array($_REQUEST['referent']);
	$referent_id_only = $_REQUEST['referent'];
	$license_class = $_REQUEST['license_class'];
	$license_version = $_REQUEST['license_version'];
	$taxonomy_permission = $_REQUEST['taxonomy_permission_slot'];
	$license_data = $_REQUEST['e_sign'];
	
	  if($restrict = wpclink_get_option('referent_pages')){
		  $referent_ids = array_merge($restrict,$referent_id);
		  wpclink_update_option('referent_pages',array_unique($referent_ids));
		  
		  // License Class		  
			if ( $license_class == 'personal' || $license_class == '0' ) {
			  update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );
			   // Update CLink.ID
			  wpclink_register_make_referent( $referent_id_only, $license_class);
			}
			
			    update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );
			  
		  $right_holder_id = get_post_field( 'post_author', $referent_id_only );
		  
			  // Copyright Owner ID
			 update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', $right_holder_id );
			  
			   // Creation Pre Auth Efeect Date
			wpclink_add_date_effect_pre_auth($referent_id_only);
			
			// Pre Authorize Popup Accept
		   	wpclink_accept_pre_auth_popups($referent_id_only,'wpclink_reuse_pre_auth_accept_date');
			  
		  $current_page = add_query_arg('updated_post','1',$current_page);
		  wp_redirect( $current_page, 301);
		   exit;
	  }else{
		  // License Class		  
			if ( $license_class == 'personal' || $license_class == '0' ) {
			  update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );
			  // Update CLink.ID
			  wpclink_register_make_referent( $referent_id_only, $license_class);
			}
		  wpclink_update_option('referent_pages',$referent_id);
		  
		   update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );
		  
		   $right_holder_id = get_post_field( 'post_author', $referent_id_only );
		  
		  // Copyright Owner ID
		  update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', $right_holder_id );
		  
		   // Creation Pre Auth Efeect Date
			wpclink_add_date_effect_pre_auth($referent_id_only);
			
			// Pre Authorize Popup Accept
		   	wpclink_accept_pre_auth_popups($referent_id_only,'wpclink_reuse_pre_auth_accept_date');
		   
		   $current_page = add_query_arg('updated_post','1',$current_page);
		  wp_redirect( $current_page, 301);
		   exit;
	  }
}elseif( wp_verify_nonce( $_POST['nonces'], 'remove_referent_nonce') and isset($_REQUEST['remove_referent']) and $_REQUEST['remove_referent'] > 0){
	  
	$remove_referent_id = array($_GET['remove_referent']);
	$remove_id_int = $_GET['remove_referent'];
	
	  if($restrict = wpclink_get_option('referent_pages')){
		  $referent_ids = array_diff($restrict,$remove_referent_id);
		  wpclink_update_option('referent_pages',array_unique($referent_ids));
		  $current_page = add_query_arg('removed_post','1',$current_page);
		  
		  // Remove License Class
		  delete_post_meta( $remove_id_int, 'wpclink_creation_license_class' );
		  
		  // Remove from CLink.ID
		  wpclink_register_remove_referent($remove_id_int);
		  
		    // Remove License Programmatic
		  delete_post_meta( $remove_id_int, 'wpclink_programmatic_right_categories');
		  
		  
		  wp_redirect( $current_page, 301);
		   exit;
	  }else{
		  
		  wp_redirect( $current_page, 301);
		   exit;
	  }
	}
}
}
// Register referent page list and redirection function
add_action('admin_init','wpclink_set_referent_pages');
/**
 * CLink Delete Token
 * 
 */
function wpclink_delete_token_page(){
// DELETE TOKEN
	if(isset($_GET['delete_token'])){
		if($_GET['delete_token'] == 1){
			
			$url_token =  wpclink_get_option('reuse_connect_date');
			
			 wpclink_delete_option('reuse_connect_date');
			 
			 $current_page = menu_page_url( 'cl-clink', false );
			 $current_page = add_query_arg('deleted_token_url',urlencode($url_token),$current_page);
			 wp_redirect($current_page, 301 );
        	 exit;
		}
	}
}
// Register clink delete token function on admin initilization
add_action('admin_init','wpclink_delete_token_page');
/**
 * CLink Warning Update IP Redirect
 * 
 */
function wpclink_warning_ip_update_redirection(){
	
	if(isset($_GET['license_id']) and isset($_GET['update_ip'])){
		
		if ( is_user_logged_in() ) {
			
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
		
		$url_param = array();
		$url_param['update_ip_done'] = 1;
		$url_param['type'] = $link_type;
					
		$menu_page = menu_page_url( 'clink-links-inbound', false );
		$complete_url = add_query_arg($url_param,$menu_page);
		
		// Update
		wpclink_update_new_ip($_GET['license_id']);
		
		wp_redirect( $complete_url, 301);
		exit;
			
		}
	}
}
add_action('admin_init','wpclink_warning_ip_update_redirection');
/**
 * CLink Canonical Verification Actions and Redictions
 * 
 */
function wpclink_do_canonical_job_manual(){
	if(isset($_GET['cl_site_id']) and isset($_GET['content_id']) and isset($_GET['canonical_run'])){
		
		$url_param = array();
		$url_param['cl_site_id'] = $_GET['cl_site_id'];
		$url_param['content_id'] = $_GET['content_id'];
		$url_param['canonical_run_done'] = 1;
					
		$menu_page = menu_page_url( 'cl-canonical', false );			
		$complete_url = add_query_arg($url_param,$menu_page);
		
		// START CANONICAL SCHEDULE MANUAL
		wpclink_do_canonical_verification();
		
		wp_redirect( $complete_url, 301);
		exit;
	}elseif(isset($_GET['cl_site_id']) and isset($_GET['content_id']) and isset($_GET['restore'])){
		
		global $wpdb;
		// CLINK  TABLE
		$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
		
		$url_param = array();
		$url_param['cl_site_id'] = $_GET['cl_site_id'];
		$url_param['content_id'] = $_GET['content_id'];
		$url_param['restore_done'] = 1;
					
		$menu_page = menu_page_url( 'cl-canonical', false );			
		$complete_url = add_query_arg($url_param,$menu_page);
		
		$license_id = $_GET['cl_site_id'];
		
		// ZERO FAIL
		wpclink_update_license_meta($license_id,'wpclink_license_attemps',0);
		wpclink_update_license_meta($license_id,'canonical_success_attemps',0);
		
		// RESTORE
		$wpdb->update($clink_sites_table,array('verification_status' => 'pass'), array('license_id' => $license_id,'mode' => 'referent'));
		
		wp_redirect( $complete_url, 301);
		exit;
	}
	
}
// Register canonical verification actions and redirection in admin initialization
add_action('admin_init','wpclink_do_canonical_job_manual');

/**
 * CLink Exif Tool Extension
 * 
 * Exif Tool required executable permission on uplugin updates
 *
 */
function wpclink_check_exif_tool_executable_updates(){
	
	if($version = wpclink_get_option('update_version')){
		
		if($version == WPCLINK_VERSION){
			// Version Match
			}else{

				$exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';

				if(file_exists($exiftool_file)){
					if(!is_executable($exiftool_file)){
						// Only executable permission as required by exiftool
						chmod($exiftool_file, 0744);

						// Updates Version
						wpclink_update_option('update_version',WPCLINK_VERSION);
					}else{

						// Updates Version
						wpclink_update_option('update_version',WPCLINK_VERSION);
					}
				}

			}
		
	}else{
		
		// Add Version
		wpclink_add_option('update_version',WPCLINK_VERSION);
	}
	
	
	
}
add_action('init','wpclink_check_exif_tool_executable_updates');



/**
 * CLink Exif Tool Extension
 * 
 * Exif Tool required executable permission on attachments, media and upload pages
 *
 */
function wpclink_check_exif_tool_executable_on_attachment(){
	
	$screen = get_current_screen();
	$screen_id = $screen->id;
	
	 if(isset($_GET['post']) || 
		$screen_id == 'media' || 
		$screen_id == 'attachment' || 
		$screen_id == 'upload'){
		 
		$exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
		 
		if(file_exists($exiftool_file)){
			if(!is_executable($exiftool_file)){
				// Only executable permission as required by exiftool
				chmod($exiftool_file, 0744);
			}
		}
		 
	 }
	
}
add_action('current_screen','wpclink_check_exif_tool_executable_on_attachment');

/**
 * CLink Continue To Image without clink features
 * 
 */
function wpclink_continue_to_image(){
	
	if(isset($_GET['continue_to_image']) and $_GET['continue_to_image'] == 1){
		
		if ( is_user_logged_in() ) {
		$post_id = $_GET['post_id'];
			
		// Continue To Image			
		update_post_meta( $post_id, 'wpclink_continue_to_image', 1 );
			
		}
	}
}
add_action('admin_init','wpclink_continue_to_image');


/**
 * CLink Update Database Creator Filed
 * 
 */
function wpclink_update_database_creators(){
	
	
	
	if($authorized_creator_single = wpclink_get_option('authorized_creator')){
	
		if($authorized_creators = wpclink_get_option('authorized_creators')){
			
			// Creators
			wpclink_update_option('authorized_creators',$authorized_creator_single);
			// Delete
			wpclink_delete_option('authorized_creator');
			
		}else{
			
			// Creators
			wpclink_add_option('authorized_creators',$authorized_creator_single);		
			// Delete
			wpclink_delete_option('authorized_creator');
		}
	
	}else{ 
	
	}
	
}
add_action('admin_init','wpclink_update_database_creators');


/**
 * CLink License offer acception init
 * 
 */
function wpclink_license_offer_init(){
	
	
	if(isset($_GET['page']) and $_GET['page'] == 'cl-clink'){
		
	}else{
		return false;
	}
	
	global $wpdb;
	
	$updated_me = false;
	$license_display_expired = 0;
	$domain_id_missing = 0;
	
	if(wpclink_is_domain_access_key_notexists()){
		$domain_id_missing = 1;
	}
/**
 * STEP 1
 * Add License Offer
*/
	
if(!isset($_POST['cl_agree_sign'])){
	
	
	$reuse_response = wpclink_reuse_show_license();
	
	// Expired
	if(is_array($reuse_response) and ($reuse_response['response'] == 'success') and ($reuse_response['expire'] == '1' || $reuse_response['expire'] == 1)){
		
		
		$license_display_expired = 1;
		wpclink_delete_option('wpclink_connect_time');	
		
		
	// Not Expired
	}else if(is_array($reuse_response) and ($reuse_response['response'] == 'success') and ($reuse_response['expire'] == '0' || $reuse_response['expire'] == 0)){
		
		
		if($reuse_response['paid'] == 1){
			$paid_offer = 1;
		}else{
			$paid_offer = 0;
		}
		
		
		$token_url = $reuse_response['token'];
		$token_url_part = parse_url($token_url);
		parse_str($token_url_part['query'], $token_url_part);
		$token_now = $token_url_part['token'];
		
		if(wpclink_is_token_valid($token_now)){
			$fail_token = true;
		}else{
			// Update Connect Site
			if($updated = wpclink_update_option('reuse_connect_date',$token_url)){
			}
			
			
			// if time exists
			if($time_exist = wpclink_get_option('wpclink_connect_time')){
				
				
				// Now
				$current_time = new DateTime();
				// Passed
				$passed_time = new DateTime($time_exist);
				
				$interval = $current_time->diff($passed_time);
				$diff = $interval->format("%i%");
			
				if($diff == 0){
					
					
				}else if($diff < 4){
					
				}else{
					$license_display_expired = 1;
					wpclink_reuse_expire();
					
					// Caretst Expired
					wpclink_delete_option('wpclink_connect_time');
								
				}
				
			
			}else{
				
				$update_current_time = date('Y-m-d h:i:s');
				if($updated_time = wpclink_update_option('wpclink_connect_time',$update_current_time)){
				}
			}
			
		}
	
	
	}else if(is_array($reuse_response) and ($reuse_response['response'] == 'error') and ($reuse_response['code'] == '404')){
		
		$license_display = 'notfound';
		
	}
	
}
/**
 * STEP 2
 * Add License Template
*/
if(isset($_POST['cl_agree_sign'])){
		
		
		
			// API Update
			//wpclink_reuse_accept();
			
			$cl_connect_site = wpclink_get_option('reuse_connect_date');
			
			
			$query_parts = parse_url($cl_connect_site);
			if(isset($query_parts['query'])){
				$query_parts_query = $query_parts['query'];
			}else{
				$query_parts_query = '';
			}
			parse_str($query_parts_query, $query_parts);
			$saved_token = $query_parts['token'];
			
			
			$current_user = wp_get_current_user();
			
			$right_holder = wpclink_get_option('rights_holder');
			
			if($right_holder == 'party'){
				$linked_identifier = get_user_meta($current_user->ID,'wpclink_party_ID',true);
			}else if($right_holder == 'creator'){
				$linked_identifier = get_user_meta($current_user->ID,'wpclink_party_ID',true);
			}
			
			// DOWNLOAD ID
			$clink_unique = uniqid(rand());
			
			// REQUESTED QUERY
			$request_query['client_site'] = urlencode(get_bloginfo('url'));
			$request_query['s_agree'] = 1;
			$request_query['cl_action'] = 'connect';
			$request_query['link_first'] = urlencode($current_user->user_firstname);
			$request_query['link_last'] =  urlencode($current_user->user_lastname);
			$request_query['link_display'] = urlencode($current_user->display_name);
			$request_query['link_email'] = urlencode($current_user->user_email);
			$request_query['link_identifier'] = urlencode($linked_identifier);
			$request_query['cl_unique_id'] = $clink_unique;
			
			// Esign
			$request_query['esign_by'] = urlencode($_POST['esign_right_holder']);
			$request_query['esign_reason'] = urlencode($_POST['esign_reason']);
			$request_query['esign_time'] = strtotime($_POST['esign_time']);
			$request_query['esign_email'] = urlencode($_POST['esign_email']);
			$request_query['esign_copyright_identifier'] = urlencode($_POST['esign_copyright_identifier']);
			
			
			// Time Difference
			$start_time = wpclink_get_option('last_license_offer_date');
			if(!empty($start_time)){				
				$start  = new DateTime($start_time);
				$end    = new DateTime(); //Current date time
				$diff   = $start->diff($end);
				$time_difference = $diff->format('%H:%i:%s'); 
				
				// urlencode
				$request_query['time_diff'] = strtotime($time_difference);
				
			}
			
			
			//$request_query['token'];
			$build_query = build_query( $request_query );
			
	
			// COMPLETE QUERY
			$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml', 'timeout' => 120)));
			$xml=file_get_contents($cl_connect_site.'&'.$build_query,false,$context);
		
		
			//var_dump($cl_connect_site.'&'.$build_query);
			
			$xml = simplexml_load_string($xml);
		
			$site_address = (string)$xml->channel->site_address;
			$secret_key = (string)$xml->channel->secret_key;		
			$auth_code = (string)$xml->channel->auth_code;
			$license_class = (string)$xml->channel->license_class;
			$taxonomy_permission = (string)$xml->channel->post_taxonomy_permission;
		
			// Content Type
			$request_content_type = (string)$xml->channel->request_content_type;
		
			$content_id = (string)$xml->channel->request_content_id;
			$content_url = (string)$xml->channel->request_content_url;
			
			$party_name = (string)$xml->channel->party_name;
			$party_email = (string)$xml->channel->party_email;
			
			$creator_name = (string)$xml->channel->creator_name;
			$creator_email = (string)$xml->channel->creator_email;
		
			$referent_right_assignID = (string)$xml->channel->right_assignID;
		
		
			
			$agreement_status = 'pass';
			
			// Debug
			//var_dump($cl_connect_site.'&'.$build_query);
			
			// Debug
			//var_dump($xml);
			
			if(!empty($site_address) and !empty($secret_key) and !empty($auth_code)){
				
				// OPTION LOAD
				$cl_saved_option = wpclink_get_option( 'preferences_general' );
				
				
				// GET IP
				$url = parse_url($site_address);
				$host_url = $url['host'];
				
				// PICK IP
				$ip = gethostbyname($host_url);
				
					
				$license_template = $_POST['cl_agreement_content'];
				
				
				// ShortCodes
				$linked_firstname = $current_user->user_firstname;
				$linked_lastname =  $current_user->user_lastname;
				$linked_displayname = $current_user->display_name;
				$linked_email = $current_user->user_email;
				$linked_creator_identifier = get_user_meta($current_user->ID,'wpclink_party_ID',true);
				
							
				$search = array('[client_site]','[licensee_firstname]','[licensee_lastname]','[linked_creator_display_name]','[linked_creator_ID]','[linked_creator_email]','[path]');
				$replace = array(get_bloginfo('url'),$linked_firstname,$linked_lastname,$linked_displayname,wpclink_do_icon($linked_creator_identifier),$linked_email,plugin_dir_url( __FILE__ ));
				
				$license_template = str_replace($search,$replace,$license_template);
				
				wpclink_debug_log('Parameter Check'.$site_address.'-'.$ip.'-'.$agreement_status.'-'.$secret_key.'-'.$auth_code.'-'.$license_template.'-'.$license_class.'-'.$content_id.'-'.$content_url.'-'.$saved_token.'-'.$clink_unique,$referent_right_assignID);
				
				
				$updated_id = wpclink_add_license_linked($site_address,$ip,$agreement_status,1,$secret_key,$auth_code,$license_template,$license_class,$content_id,$content_url,$saved_token, $clink_unique,$referent_right_assignID);
				
				$download_id = wpclink_get_license_meta($updated_id,'license_download_id',true);
				
				$deliver_time = date("Y-m-d H:i:s");			
				
				// Taxonomy Permission
				wpclink_add_license_meta($updated_id,'programmatic_right_categories',$taxonomy_permission);
				
				// Creator Email
				wpclink_add_license_meta($updated_id,'licensor_email',$creator_email);
				
				// LAST Update ID
				wpclink_update_option('primary_linked_site','-'.$updated_id);
				
				$updated_me = true;
				
			}else{
				$get_response = $xml->channel->response;
				if($get_response == 'invalid'){
					echo '<div class="updated error"><p><strong>Site Token is Expired</strong></p></div>';
				}
			}
			
			
			
			// Update			
			wpclink_update_option('wpclink_agree_sign',$_POST['cl_agree_sign']);
				
			// Flush
			wpclink_delete_option( 'reuse_connect_date' );
			wpclink_delete_option( 'wpclink_connect_time' );
			wpclink_delete_option( 'wpclink_agree_sign' );
				
			// Done
			if($updated_me == true){	
				
				/*
				wpclink_notif_print('Link is added and activated','success');
				wpclink_display_links_outbound_page();
				return false;
				*/
				
				if($request_content_type == 'post'){
					// Creation Post
					$redirect_to_creation =  menu_page_url( 'content_link_post.php', false );	
				}else if($request_content_type == 'page'){
					// Creation Page
					$redirect_to_creation =  menu_page_url( 'content_link_page.php', false );
				}else if($request_content_type == 'attachment'){
					// Creation Page
					$redirect_to_creation =  menu_page_url( 'content_link_media.php', false );
				}else{
					// Creation Post
					$redirect_to_creation =  menu_page_url( 'content_link_post.php', false );	
				}
				

				$final_redirect_creation = $redirect_to_creation.'&clink_content='.$content_id;
				
			
				
				
				
				
			}
		
	
	
}
	
	
	// Pass Variable to Global
	$GLOBALS['wpclink_offer_updated'] = $updated_me;
	$GLOBALS['wpclink_offer_fail_token'] = $fail_token;
	$GLOBALS['wpclink_offer_domain_id_missing'] = $domain_id_missing;
	$GLOBALS['wpclink_offer_license_display_expired'] = $license_display_expired;
	$GLOBALS['wpclink_offer_connect_site'] = $cl_connect_site;
	$GLOBALS['wpclink_offer_license_display'] = $license_display;
	$GLOBALS['wpclink_paid_offer'] = $paid_offer;
	
	if(!empty($final_redirect_creation)){
		if ( wp_redirect( $final_redirect_creation  ) ) {
			exit;
		}
	}
	

	
		
	
}
add_action('admin_init','wpclink_license_offer_init');
/**
 * CLink Disable AMP for post and pages
 * 
 * Disable AMP for reuse popup
 *
 */
function wpclink_disable_amp_for_reuse($skipper = false,$post_id = 0, $post){
	if(isset($_GET['clink']) and $_GET['clink'] == 'offer'){
		return true;
	}
	return false;
}
add_filter('amp_skip_post','wpclink_disable_amp_for_reuse',10,3);