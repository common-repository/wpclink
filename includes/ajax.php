<?php
/**
 * CLink Ajax Functions
 *
 * CLink creation, license selection ajax
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Define WP Ajax URL
add_action('wp_head', 'wpclink_ajax_url');
/**
 * Clink Ajax URL Script
 */
function wpclink_ajax_url() {
	// AMP
	if(wpclink_is_amp_inactive()){
   		echo '<script type="text/javascript">
           var cl_ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
	}
}
/**
 * CLink Token Submission
 * 
 */
function wpclink_send_token_to_hub(){
	
	// The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
        $weburl = rawurldecode($_REQUEST['weburl']);
		$token = $_REQUEST['token'];
		
		$response = wpclink_reuse_api_request($weburl,$token);
		
		
		if(strlen($response) > 4){
			echo $response;
		}else if($response == '400'){
			echo '400';
		}else if($response == '404'){
			echo '404';
		}
		
	}
	die();
}
// Register Reuse Popup Ajax Request
add_action( 'wp_ajax_wpclink_send_token_to_hub', 'wpclink_send_token_to_hub' );
add_action( 'wp_ajax_nopriv_wpclink_send_token_to_hub', 'wpclink_send_token_to_hub' );
/**
 * CLink Reuse Popup Ajax Request
 * 
 * Verifictions
 * - WordPress Based
 * - Registed on us-customers.clink.id
 */
function wpclink_verify_site_url() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
        $weburl = rawurldecode($_REQUEST['weburl']);
		$content_type = $_REQUEST['content_type'];
		$content_url = rawurldecode($_REQUEST['content_url']);
		$found = false;
		$html = wpclink_fetch_content_by_curl($weburl);
		
		
		
         
		//parsing begins here:
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		
		$link_nodes = $doc->getElementsByTagName('link');
		
		for ($link = 0; $link < $link_nodes->length; $link++){
			$mylink = $link_nodes->item($link);
			if($mylink->getAttribute('rel') == 'stylesheet' ){
				$full_url = $mylink->getAttribute('href');
				
				// Find WP
				if (strpos($full_url, 'wp-content') !== false) {
 			   		$found = true;
					break;
				}
			}
				
		}
		
		
		// Another Try
		$meta_node = $doc->getElementsByTagName('meta');	
		for ($meta = 0; $meta < $meta_node->length; $meta++){
			$my_meta = $meta_node->item($meta);
			
			if($my_meta->getAttribute('name') == 'generator' ){
				$meta_content = $my_meta->getAttribute('content');
				// Find WP
				if (strpos($meta_content, 'WordPress') !== false) {
 			   		$found = true;
					break;
				}
			}
		}
		
		if($content_type == 'group'){
			
			$group_found = wpclink_get_option('wpclink_group');
			echo '<div id="cl_step1"  class="cl_step">
						
			<h2>Paste this URL into</h2>
			<p>Setup &rarr; Group &rarr; Join</p>
			<center><img src="' . plugins_url( 'admin/images/menu.png', dirname(__FILE__) ) . '" /></center>
			<input readonly="readonly" type="text" value="'.$group_found['url'].'" /></div>';
			
			
			
			
		}else{
			
		
		if(wpclink_is_site_url_verified_hub($weburl)){
			$clink_plugin_found = true;
		}else{
			$clink_plugin_found = false;	
		}
		
		if($black_list = wpclink_get_option('wpclink_site_blacklist')){
			if(in_array($weburl,$black_list)){
				$not_in_black_list = false;
			}else{
				$not_in_black_list = true;
			}
				
		}else{
			$not_in_black_list = true;	
		}
		
		
		if($found and $clink_plugin_found and $not_in_black_list){
			
			// TOKEN
			wpclink_generate_token_link($weburl,$content_type,$content_url);
			// SUCCESS RESPONSE
			echo '<input type="hidden" id="response_return" value="success" />';
		}else{
			
			//echo $found .'_'.$clink_plugin_found.'_'.$not_in_black_list;
			
		
				
				
			if($found == false) echo '<p class="message error">The site address you have provided is not a WordPress site.</p>';
			if($clink_plugin_found == false) echo '<p class="message error">CLink plugin has not been registered<span class="red">!</span> <br /> Please register its users</p>';
			
			
			if($not_in_black_list == false){
			
			$url = wpclink_first_license_violation($weburl);
			$linkurl = '<a href="'.$url.'" target="_blank">linked content</a>';
			
				
				echo '<p class="message error">Your '.$linkurl.' violated the license agreement and has been backlisted form acquiring further content from this site. Please cure the violation and try again.</p>';
			}
			
			echo '<input type="hidden" id="response_return" value="error" />';
		}
		
		setcookie( 'auto_fill_popup','0', time()+3600, '/', COOKIE_DOMAIN );
		
		}
     
    }
     
    // Always die in functions echoing ajax content
   die();
}
 
// Register Reuse Popup Ajax Request
add_action( 'wp_ajax_wpclink_verify_site_url', 'wpclink_verify_site_url' );
add_action( 'wp_ajax_nopriv_wpclink_verify_site_url', 'wpclink_verify_site_url' );
/**
 * CLink Reuse Popup Ajax Request on Auto Check
 * 
 * Verifictions
 * - WordPress Based
 * - Registed on us-customers.clink.id
 */
function wpclink_verify_site_url_real_time() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
        $weburl = rawurldecode($_REQUEST['weburl']);
		$found = false;
		//echo $weburl;
		$html = wpclink_fetch_content_by_curl($weburl);
         
		//parsing begins here:
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		
		$link_nodes = $doc->getElementsByTagName('link');
		
		for ($link = 0; $link < $link_nodes->length; $link++){
			$mylink = $link_nodes->item($link);
			if($mylink->getAttribute('rel') == 'stylesheet' ){
				$full_url = $mylink->getAttribute('href');
				
				// Find WP
				if (strpos($full_url, 'wp-content') !== false) {
 			   		$found = true;
					break;
				}
			}
				
		}
		
		// Another Try
		$meta_node = $doc->getElementsByTagName('meta');	
		for ($meta = 0; $meta < $meta_node->length; $meta++){
			$my_meta = $meta_node->item($meta);
			
			if($my_meta->getAttribute('name') == 'generator' ){
				$meta_content = $my_meta->getAttribute('content');
				// Find WP
				if (strpos($meta_content, 'WordPress') !== false) {
 			   		$found = true;
					break;
				}
			}
		}
		
		
		if(wpclink_is_site_url_verified_hub($weburl)){
			$clink_plugin_found = true;
		}else{
			$clink_plugin_found = false;	
		}
		
		
		if($clink_plugin_found){
			$clink_plugin = '<p class="message success">&#x2714; Your website has been verified </p>';
		}else{
			
		}
		if($found){
			
			$site_in_wp = '<p class="message success">&#x2714; Looks like you have a site based on WordPress</p>';
			$site_in_wp = apply_filters( 'cl_site_in_wp', $site_in_wp );
			echo $clink_plugin.=$site_in_wp;
			
		}else{
			
			
		}
     
    }
     
    // Always die in functions echoing ajax content
   die();
}
// Register Reuse Popup Ajax Request Auto Check
add_action( 'wp_ajax_wpclink_verify_site_url_real_time', 'wpclink_verify_site_url_real_time' );
add_action( 'wp_ajax_nopriv_wpclink_verify_site_url_real_time', 'wpclink_verify_site_url_real_time' );
/**
 * CLink Linked Content Quick Edit Ajax Request
 * 
 */
function wpclink_register_linked_creation_by_quick_edit() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
		
$cl_options = wpclink_get_option( 'preferences_general' );
// Site URL
$link_site_url = apply_filters('cl_site_link',$cl_options['sync_site']);
		
if($_REQUEST['action_type'] == 'post'){
	
       
// Validate
if(!isset($_REQUEST['inline_post_id']) and $_REQUEST['action'] != 'clink_quick_edit_ajax_request') return false;	
		
/* ====== PREPARING REQUEST ======= */
$request_query = array();
$request_query['post__in'] = $_REQUEST['inline_post_id'];
$request_query['post_type'] = 'c-post';
$request_query['get_type'] = 'content';
	
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
	
$origin_data = array();
// Quick Parameters
$quick_month = $_REQUEST['mm'];
$quick_day = $_REQUEST['jj'];
$quick_year = $_REQUEST['aa'];
$quick_hour = $_REQUEST['hh'];
$quick_min = $_REQUEST['mn'];
$quick_password = $_REQUEST['post_password'];
$quick_status = $_REQUEST['_status'];
$quick_sticky = $_REQUEST['sticky'];
$quick_comment_status = $_REQUEST['comment_status'];
$quick_ping_status = $_REQUEST['ping_status'];
if($quick_comment_status == 'open'){
	$quick_comment_status_go = 'open';
}else{
	$quick_comment_status_go = 'closed';
}
if($quick_ping_status == 'open'){
	$quick_ping_status_go = 'open';
}else{
	$quick_ping_status_go = 'closed';
}
$quick_tags = $_REQUEST['tax_input_tags'];
$quick_category = $_REQUEST['post_category'];
$quick_category_referent = $_REQUEST['post_category_referent'];
$quick_ref =  $_REQUEST['ref_identifier'];
$quick_date = $quick_year.'-'.$quick_month.'-'.$quick_day.' '.$quick_hour.':'.$quick_min.':'.date('s');
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
// Post Author
$ns_dc = $single->children('http://purl.org/dc/elements/1.1/');
$post_author = $ns_dc->creator_id;
// Post Creator
$post_author_creator = (string)$ns_dc->creator;
// Post Creator Display
$post_creator = (string)$single->post_creator;
// Post Creator ID
$post_creator_clinkid = (string)$single->post_creator_clink_id;
// Post Content 
$post_content = (string)$single->children("content", true);
// Post Excerpt 
$post_excerpt = (string)$single->children("excerpt", true);
// Image Attached Data
$post_attached_meta = (string)$single->post_attached_data;
// License Class
$post_license_class = (string)$single->post_license_class;
// License Class
$post_taxonomy_permission = (string)$single->post_taxonomy_permission;
// License URL
$post_license_url = (string)$single->post_license_url;
// Post Copyright Owner
$post_rights_holder = (string)$single->post_rights_holder;
// Language
$language = (string)$single->language;
// NEW
$referent_creator_party_ID = (string)$single->referent_creator_party_ID;
$referent_creator_display_name = (string)$single->referent_creator_display_name;
$referent_rights_holder_party_ID = (string)$single->referent_rights_holder_party_ID;
$referent_rights_holder_display_name = (string)$single->referent_rights_holder_display_name;
// Post Date
$post_publish_date = (string)$single->pubDate;
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
	
	$time = current_time( 'Y-m-d', $gmt = 0 );
	$origin_data = array();
	$found_id = array();
		
	// Editable License
	 if($post_taxonomy_permission == 'ModifyTaxonomy'){
		
		 
		 foreach($quick_category_referent as $cat_single){
	
			$term = term_exists($cat_single, 'category');
			if ($term !== 0 && $term !== null) {
				$found_id[]= $term['term_id'];
			}else{
				$found_id[] = wp_create_category( $cat_single, '0' );
			}
	
		}
		 
		 	// Found and Available Categories Merge
			 $cat_merged = array_merge($found_id,$quick_category);
	
	 }else{
			  
	// Get Referent Category and Creating		  
	 foreach($assign_cat as $cat_single){
	
		$term = term_exists($cat_single, 'category');
		if ($term !== 0 && $term !== null) {
			$found_id[]= $term['term_id'];
		}else{
			$found_id[] = wp_create_category( $cat_single, '0' );
		}
	
	}
			  
			  
			  // XML found and Available Categories Merge
				$cat_merged = array_merge($found_id,$quick_category);
		  }
	
	
	// Unique Array
	$final_cat = array_unique($cat_merged);
	
	// Categories List to Create
	$found_id_create = $final_cat;
	
		// Set the post ID so that we know the post was created successfully
		if($post_id = wp_insert_post(
			array(
				'comment_status'	=>	$quick_comment_status_go,
				'ping_status'		=>	$quick_ping_status_go,
				'post_author'		=>	$author_id,
				'post_title'		=>	$post_title,
				'post_status'		=>	$quick_status,
				'post_content'  => $post_content,
				'post_excerpt' => $post_excerpt,
				'post_type'		=>	'post',
				'post_password' => $quick_password,
				'post_date' => $quick_date,
				'post_category' => $found_id_create
				
			),true
		)){
		  // Origin
		  $time = current_time( 'Y-m-d', true );
		  $origin_data[$link_site_url] = array('origin_id' => $post_id_old, 'last_sync' => $time, 'sync_status' => true,'canonical'	=>	$post_link);
		  
		  // Sticky
		  if(!empty($quick_sticky)){
			  stick_post($post_id);
		  }
		  
		  // Origin Url_shorten(
		  update_post_meta($post_id, 'wpclink_referent_post_uri', $post_link);
		  update_post_meta($post_id, 'wpclink_link_flag', 1);
		 
			
	
			
		   if($post_license_class == 'personal'){
			 // Referent Copyright Owner
			   
		 	 update_post_meta($post_id, 'wpclink_referent_rights_holder_party_ID', $post_rights_holder);
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
		  
		  // Post Creator
		  update_post_meta($post_id, 'wpclink_referent_creator_display_name', $post_creator);
		  update_post_meta($post_id, 'wpclink_referent_creator_party_ID', $post_creator_clinkid);
		  
		  		  
		  // License Class
		  update_post_meta($post_id, 'wpclink_creation_license_class', $post_license_class);
		  update_post_meta($post_id, 'wpclink_programmatic_right_categories', $post_taxonomy_permission);
		  
		  // Referent Categories
		  update_post_meta($post_id, 'wpclink_referent_categories', $found_id);
		  
		  if($post_license_class == 'personal'){
			 // Referent Copyright Owner
		 	 update_post_meta($post_id, 'wpclink_referent_rights_holder_party_ID', $post_rights_holder); 
		  }
			  
		  $content = apply_filters('the_content', get_post_field('post_content', $post_id));
		  $content_without_html = strip_tags($content);
		  $content_word_count = str_word_count($content_without_html);
			  
		  $license_class_post = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id. $link_site_url));
		 
		  
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
		  
			$quick_tags_array = explode(',',$quick_tags);
		  
		  $referent_tags = $tags_catch;
			  
			  
		 // Editable License
		  if($post_taxonomy_permission == 'ModifyTaxonomy'){
			  $all_tags_to_add = $quick_tags_array;
		  }else{
		  	  $all_tags_to_add = array_merge($quick_tags_array,$referent_tags);
		  }
		  
		  // Unique
		  $final_tags = array_unique($all_tags_to_add);
		 
		  
		  foreach($final_tags as $tag_single){
	
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
		  update_post_meta($post_id, 'wpclink_referent_tags', $referent_tags);
		
			
	 if(get_post_status ( $post_id ) == 'future'){
	 }else{
		 
	// CORDRA
		 
    // RULE #1
	$current_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option('authorized_creators');
	
	
	$right_holder = wpclink_get_option('rights_holder');
		
	
	// RULE #2
	// Copyright Owner or Creator
	if(wpclink_user_has_creator_list($current_user_id)){
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
	
	
	// Party user id
	$clink_party_user_id = wpclink_get_option('authorized_contact');
	
	
	
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
	
	$clink_party_id = get_user_meta($clink_party_user_id,'wpclink_party_ID',true);
		 
	// Customer ID
	$party_access_key = get_user_meta($author_id,'wpclink_party_access_key',true);
	
	// Taxonomy Permission
	if($taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true)){
	}else{
		$taxonomy_permission = '';
	}
		 
	
	$data_license = wpclink_get_license_by_referent_post_id_and_site_url($post_id_old,$link_site_url);
			
			
			
	if(is_array($data_license)){
		if(!empty($data_license)){
			$right_transaction_ID = $data_license['rights_transaction_ID'];
		}
	}
	
		 
	$right_holder = wpclink_get_option('rights_holder');
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
		 
		/* ====== Creation Right Holder ========= */
	$creation_right_holder_data = get_userdata($right_holder_id);
	$creator_user_display = $creation_right_holder_data->display_name;
	$creation_right_holder_display_name = $creation_right_holder_data->display_name;
	$creation_right_holder_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true);
	
		 
	if($right_holder_user_id = get_user_meta($right_holder_id,'wpclink_party_ID',true)){
		// CREATOR
	}elseif($right_holder_user_id = get_user_meta($right_holder_id,'wpclink_party_ID',true)){
		// PARTY
	}
	
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
		
	$creation_date = $time;
	$modified_date = get_the_modified_time('U',$post_id);
		 
		 
	$get_post_date  = get_the_date('Y-m-d',$post_id);
	$get_post_date .= 'T';
	$get_post_date .= get_the_date('G:i:s',$post_id);
	$get_post_date .= 'Z';
			
	$get_post_modified_date  = get_the_modified_time('Y-m-d',$post_id);
	$get_post_modified_date .= 'T';
	$get_post_modified_date .= get_the_modified_time('G:i:s',$post_id);
	$get_post_modified_date .= 'Z';
	
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
	
	$sending =  array(
                    'body' => array(
                        'post_title'  => html_entity_decode($post_title),
                        'guid'	=> $guid,
						'creation_uri' => $permalink,
						'reuseGUID' => $reuse_guid_link,
						'clink_creatorID' => $creator_id,
						'post_type' => strtolower($post_type),
						'post_excerpts' => html_entity_decode($post_excerpts),
						'post_charcter' => $post_character,
						'clink_word_count' => $content_word_count,
						'clink_partyID' => $clink_party_id,
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
						'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
						'clink_party_link_role' => '',
						'clink_right_transaction_ID' => $right_transaction_ID,
						'clink_creation_link_role' => $clink_creation_link_role,
						
						// Creation Right Holder Identifier
						'creation_rights_holder_party_ID' => $creation_right_holder_identifier,
						// Creation Right Holder Identifier
						'creator_display_name' => $creator_user_display,
											
						
						// Creator ID							
						'referent_clink_creatorID' => $referent_creator_party_ID,
						'creation_rights_holder_display_name' => $creation_right_holder_display_name,
						// Creation Right Holder Display Name
						'referent_creation_rights_holder_display_name' => $referent_rights_holder_display_name, 
						// Creation Right Holder Identifier
						'referent_creation_rights_holder_party_ID' => $referent_rights_holder_party_ID,
						// Creation Right Holder Identifier
						'referent_creator_display_name' => $referent_creator_display_name,
						// Referent Creation Identifier
						'referent_creation_ID'=> $clink_referent_creation_identifier,		
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
				wpclink_debug_log('QUICK EDIT POST PUBLISH'.print_r($response,true));
			  
 
            }else {
				
			// Response Debug
				wpclink_debug_log('QUICK EDIT POST PUBLISH'.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
  
		
			   
			  
	if($resposne_Array['status'] == 'create'){
		// RESPONSE
		
		update_post_meta($post_id,'wpclink_creation_ID',$resposne_Array['data']['clink_contentID']);
		// REFERENT CONTENT ID
		update_post_meta($post_id,'wpclink_referent_creation_ID',$post_contentID);
		update_post_meta($post_id,'wpclink_referent_creator_party_ID',$post_creator_clinkid);
		update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
		update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
		
		// Encrypted
		if(!empty($resposne_Array['data']['creation_access_key'])){
			// Encrypt
			update_post_meta($post_id,'wpclink_creation_access_key',$resposne_Array['data']['creation_access_key']);
		}
		
		
		update_post_meta($post_id,'wpclink_rights_holder_user_id', $author_id);
		
		
	// Now Send to Referent Site
		$send_referent = wpclink_send_linked_creation_ID($post_id_old,$resposne_Array['data']['clink_contentID']);
		
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
		
		
		echo '<td colspan="10"><p class="notice error quick-edit">You have registered a Creation at <strong>'.$resposne_Array['data']['last_registration'].' (UTC)</strong> Personal Edition allows one registration per 24h.  Please try after <strong>'.$after_24h_format.' UTC</strong></p></td>';
		
		
		// Always die in functions echoing ajax content
		die();
		
		
	}
	
	
			}
	
			
	 }
		  	
			  }else{
		  }
	}
	
}	// Return Post ID
	//echo $result_update;
	
	$menu_page = menu_page_url( 'content_link_post.php', false );
	
	$author_id =  get_post_field( 'post_author', $post_id );
	$user_info_author = get_userdata($author_id);
	
	$ContentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	$ref_creator = get_post_meta($post_id,'wpclink_referent_creator_display_name',true);
	
	echo '<th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-63">'.get_the_title($post_id).'</label><input id="cb-select-'.$post_id.'" name="post[]" value="'.$post_id.'" disabled="disabled" type="checkbox"><div class="locked-indicator">
				<span class="locked-indicator-icon" aria-hidden="true"></span>
				<span class="screen-reader-text">“'.get_the_title($post_id).'” is locked</span>
			</div>
		</th>';
	
	echo '<td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
  <strong><a href="post.php?post='.$post_id.'&action=edit">'.get_the_title($post_id).'</a></strong>';
    echo '<div class="row-actions"><span class="edit"><a href="post.php?post='.$post_id.'&action=edit" aria-label="Edit “'.get_the_title($post_id).'”">Edit</a> | </span><span class="trash"><a href="'.wp_nonce_url($menu_page.'&action=delete&p_sync='.$post_id, 'content_delete_post', '_action_nonce').'" class="submitdelete" aria-label="Move “'.get_the_title($post_id).'” to the Trash">Trash</a> | </span><span class="view"><a href="'.get_permalink($post_id).'" rel="bookmark" aria-label="View “'.get_the_title($post_id).'”">View</a></span></div>
  <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>';
  
  echo '<td class="ref_creator column-ref_creator" data-colname="ref_creator">'.$ref_creator.'</td>';
  echo '<td class="author column-author" data-colname="Author">'.$user_info_author->display_name.'</td>';
  echo '<td class="categories column-categories" data-colname="Categories">';
  the_category( ', ','', $post_id);
  echo '</td><td class="tags column-tags" data-colname="Tags">';
  if(get_the_tag_list('','','',$post_id)) {
		echo get_the_tag_list('<p>',', ','</p>',$post_id);
	}else{
		echo '—';
	}
	echo '</td><td class="license_class column-license_class">'.wpclink_get_license_class_label($post_license_class).'</td>';
	echo '<td class="date column-date" data-colname="Date">';
	echo 'Linked<br>
          <abbr>'.get_the_date( '', $post_id ).'</abbr><br />
          <br />Published<br>
    <abbr>'.$post_publish_date.'</abbr></td>';
	echo '<td class="clink_id column-clink_id" data-colname=""><p><a href="'.WPCLINK_ID_URL.'/#objects/'.$quick_ref.'" target="_blank">'.$quick_ref.'</a></p></td>';
	
	echo '<td class="clink_id column-clink_id" data-colname=""><p><br><br><br><a href="'.WPCLINK_ID_URL.'/#objects/'.$ContentID.'" target="_blank">'.$ContentID.'</a></p></td>';
     
    
	
	// Always die in functions echoing ajax content
   die();
}else if($_REQUEST['action_type'] == 'page'){
	
		
		
// Validate
if(!isset($_REQUEST['inline_post_id']) and $_REQUEST['action'] != 'clink_quick_edit_page_ajax_request') return false;	
/* ====== PREPARING REQUEST ======= */
$request_query = array();
$request_query['post__in'] = $_REQUEST['inline_post_id'];
$request_query['post_type'] = 'c-page';
$request_query['get_type'] = 'content';
	
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
	
$origin_data = array();
// Quick Parameters
$quick_month = $_REQUEST['mm'];
$quick_day = $_REQUEST['jj'];
$quick_year = $_REQUEST['aa'];
$quick_hour = $_REQUEST['hh'];
$quick_min = $_REQUEST['mn'];
$quick_password = $_REQUEST['post_password'];
$quick_status = $_REQUEST['_status'];
$quick_sticky = $_REQUEST['sticky'];
$quick_comment_status = $_REQUEST['comment_status'];
$quick_ping_status = $_REQUEST['ping_status'];
$quick_parent_id = $_REQUEST['parent_id'];
$quick_template = $_REQUEST['page_template'];
$quick_order = $_REQUEST['menu_order'];
if($quick_comment_status == 'open'){
	$quick_comment_status_go = 'open';
}else{
	$quick_comment_status_go = 'closed';
}
if($quick_ping_status == 'open'){
	$quick_ping_status_go = 'open';
}else{
	$quick_ping_status_go = 'closed';
}
$quick_ref =  $_REQUEST['ref_identifier'];
$quick_date = $quick_year.'-'.$quick_month.'-'.$quick_day.' '.$quick_hour.':'.$quick_min.':'.date('s');
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
// Post Author
$ns_dc = $single->children('http://purl.org/dc/elements/1.1/');
$post_author = $ns_dc->creator_id;
// Post Content 
$post_content = (string)$single->children("content", true);
// Post Excerpt 
$post_excerpt = (string)$single->children("excerpt", true);
// Image Attached Data
$post_attached_meta = (string)$single->post_attached_data;
// License Class
$post_license_class = (string)$single->post_license_class;
// License Class
$post_taxonomy_permission = (string)$single->post_taxonomy_permission;
// License Name
$post_license_name = (string)$single->post_license_name;
// License URL
$post_license_url = (string)$single->post_license_url;
// Language
$language = (string)$single->language;
// Post Copyright Owner
$post_rights_holder = (string)$single->post_rights_holder;
// Post Creator
$post_creator = (string)$single->post_creator;
// Post Creator ID
$post_creator_clinkid = (string)$single->post_creator_clink_id;
// NEW
$referent_creator_party_ID = (string)$single->referent_creator_party_ID;
$referent_creator_display_name = (string)$single->referent_creator_display_name;
$referent_rights_holder_party_ID = (string)$single->referent_rights_holder_party_ID;
$referent_rights_holder_display_name = (string)$single->referent_rights_holder_display_name;
	
// ContentID
$post_contentID = (string)$single->content_id;
	
// Post Date
$post_publish_date = (string)$single->pubDate;
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
	
	$time = current_time( 'Y-m-d', $gmt = 0 );
	$origin_data = array();
	
		// Set the post ID so that we know the post was created successfully
		if($post_id = wp_insert_post(
			array(
				'comment_status'	=>	$quick_comment_status_go,
				'ping_status'		=>	$quick_ping_status_go,
				'post_author'		=>	$author_id,
				'post_title'		=>	$post_title,
				'post_status'		=>	$quick_status,
				'post_content'  => $post_content,
				'post_excerpt' => $post_excerpt,
				'post_type'		=>	'page',
				'post_password' => $quick_password,
				'post_date' => $quick_date,
				'post_parent'	=> $quick_parent_id,
				'page_template'  => $quick_template,
				'menu_order'     => $quick_order,
				
			),true
		)){
		  // Origin
		  $time = current_time( 'Y-m-d', $gmt = 0 );
		  $origin_data[$link_site_url] = array(	'origin_id' => $post_id_old, 
		  																'last_sync' => $time,
																		'sync_status' => true,
																		'sync_parent' => $post_parent,
																		'canonical'   => $post_link);
																		
		 // Sticky
		  if(!empty($quick_sticky)){
			  stick_post($post_id);
		  }
		  
		  // Origin Url_shorten(
		  update_post_meta($post_id, 'wpclink_referent_post_uri', $post_link);
		  update_post_meta($post_id, 'wpclink_link_flag', 1);
		  
		  // License Class
		  update_post_meta($post_id, 'wpclink_creation_license_class', $post_license_class);
		  update_post_meta($post_id, 'wpclink_programmatic_right_categories', $post_taxonomy_permission);
			
		
		  
		   // Post Creator
		  update_post_meta($post_id, 'wpclink_referent_creator_display_name', $post_creator);
		  update_post_meta($post_id, 'wpclink_referent_creator_party_ID', $post_creator_clinkid);
			
			
		 if(!empty($post_attached_meta)){
			  update_post_meta($post_id,'wpclink_referent_media_attributes',json_decode($post_attached_meta,true));
		  }
		  
		  
		   if($post_license_class == 'personal'){
			 // Referent Copyright Owner
		 	 update_post_meta($post_id, 'wpclink_referent_rights_holder_party_ID', $post_rights_holder); 
		  }
			
		  
		  $content = apply_filters('the_content', get_post_field('post_content', $post_id));
		  $content_without_html = strip_tags($content);
		  $content_word_count = str_word_count($content_without_html);
			
		  $license_class_post = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url));
		  
		  
		  
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
		if(get_post_status ( $post_id ) == 'future'){
	 }else{
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
	if($taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true)){
	}else{
		$taxonomy_permission = '';
	}
	
	
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
			
			
	$data_license = wpclink_get_license_by_referent_post_id_and_site_url($post_id_old,$link_site_url);	
			
	if(is_array($data_license)){
		if(!empty($data_license)){
			$right_transaction_ID = $data_license['rights_transaction_ID'];
		}
	}
	
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
		
	$creation_date = $time;
	$modified_date = get_the_modified_time('U',$post_id);
	
	$domain_access_key = wpclink_get_option('domain_access_key');
	
	
	$post_categories = wp_get_post_categories($post_id);
			
	// Create GUID
	$guid = get_bloginfo('url').'/?psge_id='.$post_id;
	
	
	$cats = array();	
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
						'reuseGUID' => $reuse_guid_link,
						'clink_creatorID' => $creator_id,
						'post_type' => strtolower($post_type),
						'post_excerpts' => html_entity_decode($post_excerpts),
						'post_charcter' => $post_character,
						'clink_word_count' => $content_word_count,
						'clink_partyID' => $clink_party_id,
						'post_year' => $post_year,
						'clink_category' => $cats,
						'clink_tag' => $tags,
						'clink_territory_code' => $clink_terriory_code,
						'clink_referent_creation_identifier'=> $clink_referent_creation_identifier,
						'creation_rights_holder_display_name' => $creation_right_holder_identifier,
						'clink_party_link_role' => '',
						'clink_creation_link_role' => $clink_creation_link_role,
						'clink_contentID' => $content_id,
						'party_access_key' => $party_access_key,
						'clink_license_class' => wpclink_get_license_class_label($post_license_class),
						'clink_license_version' => '0.9',
						'clink_taxonomy_permission' => $taxonomy_permission,
						'clink_right_transaction_ID' => $right_transaction_ID,
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
						'clink_language' => $language,
						'clink_post_creation_date' =>$creation_date,
						'clink_post_modification_date'=> $modified_date,
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
				wpclink_debug_log('QUICK EDIT PAGE UPDATE '.print_r($response,true));
			  
            }else {
				
				// Response Debug
				wpclink_debug_log('QUICK EDIT PAGE UPDATE '.print_r($response,true));
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
  
			   
	
			  
	if($resposne_Array['status'] == 'create'){
		// RESPONSE
		
	update_post_meta($post_id,'wpclink_creation_ID',$resposne_Array['data']['clink_contentID']);
	// REFERENT CONTENT ID
	update_post_meta($post_id,'wpclink_referent_creation_ID',$post_contentID);
	update_post_meta($post_id,'wpclink_referent_creator_party_ID',$post_creator_clinkid);
	update_post_meta($post_id,'wpclink_creation_creation_date',$resposne_Array['data']['clink_creation_date']);
	update_post_meta($post_id,'wpclink_creation_modification_date',$resposne_Array['data']['clink_modification_date']);
		
		
	update_post_meta($post_id,'wpclink_rights_holder_user_id', $author_id);
		
		
		// Now Send to Referent Site
		$send_referent = wpclink_send_linked_creation_ID($post_id_old,$resposne_Array['data']['clink_contentID']);
		
		
		// Save Post ID
		wpclink_add_post_id_linked($post_id,$post_id_old,$link_site_url);
		
		// Response Debug
		wpclink_debug_log('SEND REFERENT POST '.print_r($send_referent ,true));
		
		
	// Encrypted
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
		
		
		echo '<td colspan="10"><p class="notice error quick-edit">You have registered a Creation at <strong>'.$resposne_Array['data']['last_registration'].' (UTC)</strong> Personal Edition allows one registration per 24h.  Please try after <strong>'.$after_24h_format.' (UTC)</strong></p></td>';
		
		
		// Always die in functions echoing ajax content
		die();
		
		
	}
	
	
			}
	 }
		  	
			  }else{
		  
		  }
	}
	
}	// Return Post ID	// Return Post ID
	//echo $result_update;
	
	$menu_page = menu_page_url( 'content_link_post.php', false );
	
	$author_id =  get_post_field( 'post_author', $post_id );
	$user_info_author = get_userdata($author_id);
	
	$ContentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	$ref_creator = get_post_meta($post_id,'wpclink_referent_creator_display_name',true);
	
	echo '<th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-63">'.get_the_title($post_id).'</label><input id="cb-select-'.$post_id.'" name="post[]" value="'.$post_id.'" disabled="disabled" type="checkbox"><div class="locked-indicator">
				<span class="locked-indicator-icon" aria-hidden="true"></span>
				<span class="screen-reader-text">“'.get_the_title($post_id).'” is locked</span>
			</div>
		</th>';
	
	echo '<td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
  <strong><a href="post.php?post='.$post_id.'&action=edit">'.get_the_title($post_id).'</a></strong>';
  echo '<div class="row-actions"><span class="edit"><a href="post.php?post='.$post_id.'&action=edit" aria-label="Edit “'.get_the_title($post_id).'”">Edit</a> | </span><span class="trash"><a href="'.wp_nonce_url($menu_page.'&action=delete&p_sync='.$post_id, 'content_delete_post', '_action_nonce').'" class="submitdelete" aria-label="Move “'.get_the_title($post_id).'” to the Trash">Trash</a> | </span><span class="view"><a href="'.get_permalink($post_id).'" rel="bookmark" aria-label="View “'.get_the_title($post_id).'”">View</a></span></div>
  <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>';
  
  echo '<td class="ref_creator column-ref_creator" data-colname="ref_creator">'.$ref_creator.'</td>';
  echo '<td class="author column-author" data-colname="Author">'.$user_info_author->display_name.'</td>';
  echo '<td class="license_class column-license_class">'.wpclink_get_license_class_label($post_license_class).'</td>';
  
	echo '<td class="date column-date" data-colname="Date">';
	echo 'Linked<br>
          <abbr>'.get_the_date( '', $post_id ).'</abbr><br />
          <br />Published<br>
    <abbr>'.$post_publish_date.'</abbr></td>';
	echo '<td class="clink_id column-clink_id" data-colname=""><p><a href="'.WPCLINK_ID_URL.'/#objects/'.$quick_ref.'" target="_blank">'.$quick_ref.'</a></p></td>';
	
	echo '<td class="clink_id column-clink_id" data-colname="">';
	if(!empty($ContentID)){ 
		echo '<p><br><br><br><a href="'.WPCLINK_ID_URL.'/#objects/'.$ContentID.'" target="_blank">'.$ContentID.'</a></p>';
	}
	echo '</td>';
     
    
     
 
		
	
	// Always die in functions echoing ajax content
   die();
}
	
	}
}
// Register linked content quick ajax request
add_action( 'wp_ajax_wpclink_register_linked_creation_by_quick_edit', 'wpclink_register_linked_creation_by_quick_edit' );  
/**
 * CLink Save License Class Ajax Request
 * 
 */
function wpclink_save_license_class_post_editor() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
	  
	$referent_id = array($_REQUEST['post_id']);
	$referent_id_only = $_REQUEST['post_id'];
	$license_class = $_REQUEST['license_class'];
	$taxonomy_permission = $_REQUEST['taxonomy_permission'];
	$marketplace_cat = $_REQUEST['marketplace_cat'];
	$license_version = $_REQUEST['license_version'];
	$price = $_REQUEST['price_value'];
	$license_data = (isset($_REQUEST['e_sign' ])) ? $_REQUEST['e_sign' ] : '';
		
	// GUID
	$guid = get_the_guid($referent_id_only);
		
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));  
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];
		
	
		
	// Get Post Type
	$post_type = get_post_type($referent_id_only);
	if($post_type == 'attachment'){
		
	  if($restrict = wpclink_get_option('referent_attachments')){
		  
		   if($license_class == 'personal'){
				 $license_type = 'wpclink_personal';
		   }
				
			$license_type = apply_filters( 'wpclink_license_class_type', $license_type, $license_class );
		  
		  			  
			  update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );
			  update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );
		  
			  if(!empty($marketplace_cat)){
				   do_action('wpclink_extension_commerce_marketplace_cat',$referent_id_only,$marketplace_cat);
			  }
			  // Update CLink.ID
			  //wpclink_register_make_referent( $referent_id_only, $license_class);
				
				// License Selected Type
		  		update_post_meta( $referent_id_only, 'wpclink_license_selected_type', $license_type );
		  
		  		// Update Price
		  
		  		if(!empty($price)){
										
					do_action('wpclink_extension_commerce_price',$referent_id_only,$price);
					
				}
		  
		  		
				
		  
		  		$license_update = wpclink_update_media_license($referent_id_only);
		  
		  // Apply License
				do_action('wpclink_apply_license_attachment',$referent_id_only, $license_type);
		  
		  		if(is_array($license_update)){
					
					// Revert
					delete_post_meta( $referent_id_only, 'wpclink_creation_license_class');
			  		delete_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories');
					delete_post_meta($referent_id_only, 'wpclink_license_selected_type');
					
					
					echo json_encode($license_update);
					die();
				}
			
		  		$referent_ids = array_merge($restrict,$referent_id);
		  		wpclink_update_option('referent_attachments',array_unique($referent_ids));
			  
			   // Creation Pre Auth Efeect Date
				wpclink_add_date_effect_pre_auth($referent_id_only);
			  
			  // Copyright Owner ID
			  //update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', wpclink_get_rights_holder_id() );
		  
		  		
		  		if($license_class == 'personal' || $license_class == 'business' ){
					$license_link = '<a class="button cl-small" href="admin.php?page=cl_templates">View</a>';
				}
		  
		  
		  		echo '<div>'.ucfirst($license_class).' | Version: 0.9i</div><div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div><span class="small-label category onwire"><a onclick="cl_present_popup_media()">Right Categories</a></span></div><div class="license-date-slot"></div>';
		  
		  
		  
		  			
	  
	  }else{
		  
		   if($license_class == 'personal'){
				 $license_type = 'wpclink_personal';
		   }
				
			$license_type = apply_filters( 'wpclink_license_class_type', $license_type, $license_class );
		  
		 
		  
			  update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );
			  				
				// License Selected Type
		  		update_post_meta( $referent_id_only, 'wpclink_license_selected_type', $license_type );
		  
		  
				if(!empty($marketplace_cat)){
				   do_action('wpclink_extension_commerce_marketplace_cat',$referent_id_only,$marketplace_cat);
			  }
				
			
		  
		   update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );
		  	
		  // Update Price
		  
		  		if(!empty($price)){
										
					do_action('wpclink_extension_commerce_price',$referent_id_only,$price);
					
				}
		  
		  $license_update = wpclink_update_media_license($referent_id_only);
		  
		  		  // Apply License
		  do_action('wpclink_apply_license_attachment',$referent_id_only, $license_type);
		  
		  		if(is_array($license_update)){
					
					// Revert
					delete_post_meta( $referent_id_only, 'wpclink_creation_license_class');
			  		delete_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories');
					delete_post_meta($referent_id_only, 'wpclink_license_selected_type');
					
					
					echo json_encode($license_update);
					die();
				}
		  
		   // Add Media in Referent List
		  	wpclink_update_option('referent_attachments',array_unique($referent_id));
		   // Creation Pre Auth Efeect Date
			wpclink_add_date_effect_pre_auth($referent_id_only);
		   
		    // Copyright Owner ID
			//update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', wpclink_get_rights_holder_id() );
		  
		  
		  
		 
		  
		  
		  
		  	if($license_class == 'personal' || $license_class == 'business'){
					$license_link = '<a class=\"button cl-small\" href=\"admin.php?page=cl_templates\">View</a>';
			}
		
		     $license_return = array('complete' => 'success',
							'data' => '<div>'.ucfirst($license_class).' | Version: 0.9i</div><div class=\"small-label\"><a 	href=\"https://licenses.clink.id/\" target=\"_blank\">Learn about licenses <span class=\"dashicons dashicons-external\"></span></a></div><span class=\"small-label category onwire\"><a href=\"#\" onclick=\"cl_present_popup()\">Right Categories</a></span></div>');
		  
		  	echo json_encode($license_return);
			die();
		   
		    
	  }
	
		
	}elseif($post_type == 'post'){
	  if($restrict = wpclink_get_option('referent_posts')){
		
		  
		// License Class		  
		if ( $license_class == 'personal' ) {
		// Update CLink.ID
		wpclink_register_make_referent( $referent_id_only, $license_class);
		update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );
		update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );
		}
		  
		if(!empty($marketplace_cat)){
			update_post_meta( $referent_id_only, 'wpclink_marketplace_categories', $marketplace_cat );
		}
		$referent_ids = array_merge($restrict,$referent_id);
		wpclink_update_option('referent_posts',array_unique($referent_ids));
		// Creation Pre Auth Efeect Date
		wpclink_add_date_effect_pre_auth($referent_id_only);
		// Copyright Owner ID
		if($creation_right_holder_user_id = get_post_meta($referent_id_only,'wpclink_rights_holder_user_id',true)){
		}else{
			update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', wpclink_get_rights_holder_id() );
		}
		
		
		if($license_class == 'personal'){
			$license_link = '<a class="button cl-small" href="admin.php?page=cl_templates">View</a>';
		}
		$pre_auth_effect_date = get_post_meta($referent_id_only,'wpclink_reuse_pre_auth_effective_date',true);	
		$pre_auth_html = '<div class="license-date-slot">';
			if(empty($pre_auth_effect_date)){
				$pre_auth_html .= 'N/A'; 
			}else{
				$pre_auth_html .= '<span class="incolor"><strong>Date: <span class="icon-box" title="Pre-Authorized Date"></span> </strong>  '.wpclink_convert_date_to_iso($pre_auth_effect_date).'</span>'; 
			}
		$pre_auth_html .= '</div>';
		$license_return = array('complete' => 'success',
					'data' => '<div>Personal | Version: 0.9</div><div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div><span class="small-label category onwire"><a href="#" onclick="cl_present_popup()">Right Categories</a></span></div>'.$pre_auth_html);
		echo json_encode($license_return);
		die();
		   
	  
	  }else{
		  // License Class		  
			if ( $license_class == 'personal' || $license_class == '0' ) {
			 
			// Update CLink.ID
			wpclink_register_make_referent( $referent_id_only, $license_class);
			update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );
			update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );
				
			}
		  
		  	if(!empty($marketplace_cat)){
				update_post_meta( $referent_id_only, 'wpclink_marketplace_categories', $marketplace_cat );
			}
			wpclink_update_option('referent_posts',array_unique($referent_id));
			// Creation Pre Auth Efeect Date
			wpclink_add_date_effect_pre_auth($referent_id_only);
			// Copyright Owner ID
		// Copyright Owner ID
		if($creation_right_holder_user_id = get_post_meta($referent_id_only,'wpclink_rights_holder_user_id',true)){
		}else{
			update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', wpclink_get_rights_holder_id() );
		}
			if($license_class == 'personal'){
				$license_link = '<a class="button cl-small" href="admin.php?page=cl_templates">View</a>';
			}
		  
		  	$pre_auth_effect_date = get_post_meta($referent_id_only,'wpclink_reuse_pre_auth_effective_date',true);	
			$pre_auth_html = '<div class="license-date-slot">';
				if(empty($pre_auth_effect_date)){
					$pre_auth_html .= 'N/A'; 
				}else{
					$pre_auth_html .= '<span class="incolor"><strong>Date: <span class="icon-box" title="Pre-Authorized Date"></span> </strong>  '.wpclink_convert_date_to_iso($pre_auth_effect_date).'</span>'; 
				}
			$pre_auth_html .= '</div>';
		  
		   $license_return = array('complete' => 'success',
							'data' => '<div>Personal | Version: 0.9</div><div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div><span class="small-label category onwire"><a href="#" onclick="cl_present_popup()">Right Categories</a></span></div>'.$pre_auth_html);
		  	echo json_encode($license_return);
			die();
		   
		   
		    
	  }
	}elseif($post_type == 'page'){
		
		if($restrict = wpclink_get_option('referent_pages')){
			
			// Update CLink.ID
			wpclink_register_make_referent( $referent_id_only, $license_class);
			$posts = array_merge($restrict,$referent_id);
			wpclink_update_option('referent_pages',array_unique($posts));
			update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );
			update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );
			if(!empty($marketplace_cat)){
			update_post_meta( $referent_id_only, 'wpclink_marketplace_categories', $marketplace_cat );
			}
			// Copyright Owner ID
			if($creation_right_holder_user_id = get_post_meta($referent_id_only,'wpclink_rights_holder_user_id',true)){
			}else{
				update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', wpclink_get_rights_holder_id() );
			}
			// Creation Pre Auth Efeect Date
			wpclink_add_date_effect_pre_auth($referent_id_only);
			// Pre Authorize Popup Accept
			wpclink_accept_pre_auth_popups($referent_id_only,'wpclink_reuse_pre_auth_accept_date');
			if($license_class == 'personal'){
			$license_link = '<a class="button cl-small" href="admin.php?page=cl_templates">View</a>';
			}
		  
		  		
			$pre_auth_effect_date = get_post_meta($referent_id_only,'wpclink_reuse_pre_auth_effective_date',true);	
			$pre_auth_html = '<div class="license-date-slot">';
				if(empty($pre_auth_effect_date)){
					$pre_auth_html .= 'N/A'; 
				}else{
					$pre_auth_html .= '<span class="incolor"><strong>Date: <span class="icon-box" title="Pre-Authorized Date"></span> </strong>  '.wpclink_convert_date_to_iso($pre_auth_effect_date).'</span>'; 
				}
			$pre_auth_html .= '</div>';
		  
			
			
			 $license_return = array('complete' => 'success',
							'data' => '<div>Personal | Version: 0.9</div><div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div><span class="small-label category onwire"><a href="#" onclick="cl_present_popup()">Right Categories</a></span></div>'.$pre_auth_html);
		  	echo json_encode($license_return);
			die();
		
		  
		  
	  
	  }else{
			
			  // Update CLink.ID
			  wpclink_register_make_referent( $referent_id_only, $license_class);
			
		  	  wpclink_update_option('referent_pages',$referent_id);
		  
		   
			if ( $license_class == 'personal' || $license_class == '0' ) {
			  update_post_meta( $referent_id_only, 'wpclink_creation_license_class', $license_class );
			  update_post_meta( $referent_id_only, 'wpclink_programmatic_right_categories', $taxonomy_permission );
			  
			}
			
			if(!empty($marketplace_cat)){
			update_post_meta( $referent_id_only, 'wpclink_marketplace_categories', $marketplace_cat );
			}
			
			// Copyright Owner ID
			if($creation_right_holder_user_id = get_post_meta($referent_id_only,'wpclink_rights_holder_user_id',true)){
			}else{
				update_post_meta( $referent_id_only, 'wpclink_rights_holder_user_id', wpclink_get_rights_holder_id() );
			}
			   // Creation Pre Auth Efeect Date
			wpclink_add_date_effect_pre_auth($referent_id_only);
			
			// Pre Authorize Popup Accept
		   	wpclink_accept_pre_auth_popups($referent_id_only,'wpclink_reuse_pre_auth_accept_date');
			
			
			
			$pre_auth_effect_date = get_post_meta($referent_id_only,'wpclink_reuse_pre_auth_effective_date',true);	
			$pre_auth_html = '<div class="license-date-slot">';
				if(empty($pre_auth_effect_date)){
					$pre_auth_html .= 'N/A'; 
				}else{
					$pre_auth_html .= '<span class="incolor"><strong>Date: <span class="icon-box" title="Pre-Authorized Date"></span> </strong>   '.wpclink_convert_date_to_iso($pre_auth_effect_date).'</span>'; 
				}
			$pre_auth_html .= '</div>';
		  
			
				
			
			$license_return = array('complete' => 'success',
							'data' => '<div>Personal | Version: 0.9</div><div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div><span class="small-label category onwire"><a href="#" onclick="cl_present_popup()">Right Categories</a></span></div>'.$pre_auth_html);
		  	echo json_encode($license_return);
			die();
		  
		  
		  
	  }
		
		
	}
	
  
    }
     
    // Always die in functions echoing ajax content
   die();
}
 // Register save license type ajax request function
add_action( 'wp_ajax_wpclink_save_license_class_post_editor', 'wpclink_save_license_class_post_editor' );
/**
 * CLink Save License Class Ajax Request
 * 
 */
function wpclink_save_license_class() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
        $license_class = $_REQUEST['license_class'];
        $post_id = $_REQUEST['post_id'];
		
        // Let's take the data that was sent and do something with it
		if($post_id >= 1){
			if ( $license_class == 'uc-ut-um' || $license_class == 'uc-at-um' || $license_class == '0' ) {
			   $return_id = update_post_meta( $post_id, 'wpclink_creation_license_class', $license_class );
			}
		}
     
        // Now we'll return it to the javascript function
        // Anything outputted will be returned in the response
        echo $return_id;
         
        // If you're debugging, it might be useful to see what was sent in the $_REQUEST
        // print_r($_REQUEST);
     
    }
     
    // Always die in functions echoing ajax content
   die();
}
 // Register save license type ajax request function
add_action( 'wp_ajax_wpclink_save_license_class', 'wpclink_save_license_class' );
/**
 * CLink Print License 
 * 
 */
function wpclink_do_clink_panel_license() {
 	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {
		 
			$post_id = $_REQUEST['post_id'];
			
			if($clink_id = get_post_meta($post_id,'wpclink_creation_ID',true)){
			
			
				$posts_list = wpclink_get_option('referent_posts');
				$pages_list = wpclink_get_option('referent_pages');
			
				$all_list = array_merge($posts_list,$pages_list);
			
				$current_user_id = get_current_user_id();
			
				if(in_array($post_id,$all_list)){
					
					
					
					$post_type = get_post_type($post_id);
	
					if($post_type == 'post'){
					
					echo '<div>Personal | Version: 0.9</div><div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div><span class="small-label  category onwire"><a href="#" onclick="cl_present_popup()">Right Categories</a></span>';
						
					}else if($post_type == 'page'){
						
						echo '<div>Personal | Version: 0.9</div><div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div><span class="small-label  category onwire"><a href="#" onclick="cl_present_popup()">Right Categories</a></span>';
						
					}
					
						
				if(wpclink_post_in_post_referent_list($post_id)){
					
					$pre_auth_effect_date = get_post_meta($post_id,'wpclink_reuse_pre_auth_effective_date',true);	
					
					echo '<div class="license-date-slot">';
					
						if(empty($pre_auth_effect_date)){
							echo 'N/A'; 
						}else{
							echo '<span class="incolor"><strong>Date: <span class="icon-box" title="Pre-Authorized Date"></span> </strong>  '.wpclink_convert_date_to_iso($pre_auth_effect_date).'</span>'; 
						}
					echo '</div>';
					
				}
					
					
					
			
				}else{
				
				if(wpclink_user_has_creator_list($current_user_id)){
					
					$post_type = get_post_type($post_id);
					
					if($post_type == "post"){
						$add_taxonomy = '<option value="AddToTaxonomy"> Add to Taxonomy</option>';
					}else{
						$add_taxonomy = '';
					}
					
					
					$license_select = 'Personal | Version: 0.9<div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a></div><div class="none-slot"><span class="small-label clink_id category">Right Categories <span class="icon-box align-license" title="Creation will be available for reuse under the selected Right Categories when the license gets applied"></span></span></div><div class="small-label"><div class="small-label"><span class="underline">Programmatic </span></div><div><span class="fix-90">Right Type </span> <select id="quick-license-type"><option value="non-editable"> None</option>'.$add_taxonomy.'<option value="ModifyTaxonomy"> Modify Taxonomy</option></select> </label></div><div class="small-label"><a class="present" onclick="cl_present_popup()" href="#">Presets</a></div> <input type="hidden" value="personal" name="license_class" class="license_class" /><div class="none-slot"><span class="cl-loading-bar license"><span class="loader_spinner"></span><span class="cl_loader_status_mini">Updating...</span></span><input onclick="wpclink_ls_trigger()" type="button" class="select-license button" value="Apply" /></div></div>';
					
					
					echo $license_select;
					
					}else{
						echo "0";
					}
					
				}
				
			}else{
				echo "0";
			}
			
    	}
	}   
    // Always die in functions echoing ajax content
   die();
}
 // Register make post to referent list step 2 function
add_action( 'wp_ajax_wpclink_do_clink_panel_license', 'wpclink_do_clink_panel_license' );
/**
 * CLink Make Post to Referent List Step 2 Ajax Request
 * 
 */
function wpclink_make_ref_post_step2() {
 	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {
		 
		 	// License_class
			$license_class = $_REQUEST['license_class'];
			$taxonomy_permission = (isset($_REQUEST['taxonomy_permission' ])) ? $_REQUEST['taxonomy_permission' ] : '';
			$referent_id = (isset($_REQUEST['referent_id' ])) ? $_REQUEST['referent_id' ] : '';
			
			//Licenses
			
			$clink_licenses = wpclink_get_license_template($referent_id,false,$license_class);
				
			if($referent_id > 0){
				
				$html = '';
				
				if($license_class == 'personal'){
					$show_license = $clink_licenses;
				}else{
                    
                   $show_license = $clink_licenses; 
                    
                }
				
				// Pre Authorize Popup Accept
		   		wpclink_accept_pre_auth_popups($referent_id,'wpclink_reuse_pre_auth_view_date');
				
				
							
			
				
				$html.= '<div id="cl-license-show-box" style="display:none;"><div class="license_in_popup">'.$show_license.'<br /><h3 align="right" style="text-decoration:underline; margin-right:50px;">Electronic  signature  of  Licensor</h3><br /><br /></div></div>';
				
				echo $html;
			}
			
    	}
	}   
    // Always die in functions echoing ajax content
   die();
}
 // Register make post to referent list step 2 function
add_action( 'wp_ajax_wpclink_make_ref_post_step2', 'wpclink_make_ref_post_step2' );
/**
 * CLink Display License
 * 
 */
function wpclink_do_popup_license_template(){
	
	if(is_user_logged_in()){
		
    	if ( isset($_REQUEST) ) {
			
			// License_class
			$license_class = $_REQUEST['license_class'];
			$referent_id = $_REQUEST['referent_id'];
			
			if($referent_id > 0){
			
			// Current User
			$user_id = get_current_user_id();
			$user_info = get_userdata($user_id);
			$view_date = current_time("Y-m-d H:i:s",true);
			
			
			
			if($license_class == 'personal'){
					$type = 'personal';
					$type_label = "Personal License Template";
			}
			
			//Licenses
			$clink_licenses = wpclink_get_license_template($referent_id);
			
				if($license_view = get_user_meta($user_id,'wpclink_license_'.$type.'_view_date',true)){
					echo "0";
					// Secound Time
				}else{
					// First time
					$show_license = $clink_licenses;
					
					$accept_html = '<p><label><input type="checkbox" id="accept_license_check" name="accept_license_check" value="1">I&#39;ve read the license terms and conditions<label></p><p align="right"><input type="button" id="license_next" class="button button-primary" value="Next" disabled /></p>';
					
					$html.= '<div id="cl-license-show-screen" title="'.$type_label.'"><div id="cl-license-show-box" style="display:block;"><div class="license_in_popup"><div class="cl_notice info_license">You must read and accept the license terms and conditions. Checkbox to accept is at the bottom of this page. <span class="icon-box" title="You need to do this only once per license class"></span></div>'.$show_license.'<br /><h3 align="right" style="text-decoration:underline; margin-right:50px;">Electronic  signature  of  Licensor</h3><br />'.$accept_html.'</div></div></div>';
					
					// Viewed
					update_user_meta($user_id,'wpclink_license_'.$type.'_view_date',$view_date);
					
					
					
					echo $html;
				}
				
			}
			
		}
	}
	
die();
}
 // Register display license on sreen function
add_action( 'wp_ajax_wpclink_do_popup_license_template', 'wpclink_do_popup_license_template' );
/**
 * CLink Accept Pre Auth Popops Ajax
 * 
 */
function wpclink_pre_auth_accept_ajax(){
	
	if(is_user_logged_in()){
		
    	if ( isset($_REQUEST) ) {
			
			// License_class
			$action = $_REQUEST['cl_action'];
			$referent_id = $_REQUEST['referent_id'];
			
			if($referent_id > 0){
				
				if($action == 'add'){ 
				
				$added_id = wpclink_accept_pre_auth_popups($referent_id,'wpclink_reuse_pre_auth_accept_date');
				
				echo $added_id;
				
				}elseif($action == 'remove'){
					
				$deleted_id = delete_post_meta($referent_id,'wpclink_reuse_pre_auth_accept_date');
				
				echo $deleted_id;
					
				}
				
			}
		}
	}
	die();
}
 // Register display license on sreen function
add_action( 'wp_ajax_wpclink_pre_auth_accept_ajax', 'wpclink_pre_auth_accept_ajax' );
/**
 * CLink Make Post to Referent List Step 1 Ajax Request
 * 
 */
function wpclink_esign_stamp_make_ref() {
 	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {
		 
		 	// Signing the E-Signature
			$sign_here = $_REQUEST['sign_here'];		
			if($sign_here == 1){
			
				$current_user_id = get_current_user_id();
				if(wpclink_user_has_creator_list($current_user_id)){
					$right_holder_id = $current_user_id;
				}
			
			
				// User Info
				$user_info = get_userdata($right_holder_id);
				
				// Connect Site URL
				$cl_connect_site = wpclink_get_option('reuse_connect_date');
				
				$view_time = current_time("Y-m-d H:i:s",true);
				
				
					
				
					$signed_by = $user_info->display_name;
					$stamp_time = $view_time;
					$reason = 'Licensing  digital  content';
					$ip = wpclink_get_client_ip();
					$email = $user_info->user_email;
									
				
				}
				
				
									
				$esign_html = '<div id="esign-stamp" style=" position:relative; -moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; -o-user-select: none;user-select: none; width:440px; display: inline-block;padding: 15px;"><div style="position:relative; z-index:5; width:50%; float: left; "><h2 style="font-family: Times New Roman, Times, serif;font-size: 30px;font-style: italic;font-weight: normal;margin: 25px 0;text-align: center;">'.$signed_by.'</h2></div><div style="width:50%; float: right; position:relative; z-index:5; "><h5 style="margin: 0;line-height: 24px;font-size: 12px; font-family:arial; ">Electronically  signed  by: '.$signed_by.'</h5><h5 style="margin: 0;line-height: 24px;font-size: 12px; font-family:arial; ">Reason: '.$reason.' </h5><h5 style="margin: 0;line-height: 24px;font-size: 12px; font-family:arial; ">Date: '.$stamp_time.'</h5></div><img style="position:absolute; z-index:1; top:10px; left:160px;"  src="[site_url]wp-content/plugins/clink-personal/images/clinks-logo-alp.jpg" /></div>';
				
				
				$html = str_replace('[site_url]',get_bloginfo('url').'/',$esign_html);
				
				echo $html;
				
			
    	}
	}   
    // Always die in functions echoing ajax content
   die();
}
  // Register make post to referent list step 1 function
add_action( 'wp_ajax_wpclink_esign_stamp_make_ref', 'wpclink_esign_stamp_make_ref' );
/**
 * CLink Care Authorization Cookie Set
 * 
 */
function wpclink_care_authorize_ajax_request() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
        if($_REQUEST['generate'] == 1){
			$token = uniqid();
		
			setcookie( 'care_authorize', $token, time()+3600, '/', COOKIE_DOMAIN );
			echo $token;
		}
     
    }
     
    // Always die in functions echoing ajax content
   die();
}
 // Register clink care authorization cookie set
add_action( 'wp_ajax_wpclink_care_authorize_ajax_request', 'wpclink_care_authorize_ajax_request' );
add_action( 'wp_ajax_nopriv_wpclink_care_authorize_ajax_request', 'wpclink_care_authorize_ajax_request' );
/**
 * CLink Care Auto Check Login Ajax Request
 * 
 */
function wpclink_care_auto_check_ajax_request() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {   
        if($_REQUEST['cl_popup_info_check'] == 1){
			if(isset($_COOKIE['auto_fill_popup']) and $_COOKIE['auto_fill_popup'] != '0'){
				echo $_COOKIE['auto_fill_popup'];
			}else{
			 	echo 'false';	
			}
		}     
    }
  
    // Always die in functions echoing ajax content
   die();
}
 // Register auto check login ajax request function
add_action( 'wp_ajax_wpclink_care_auto_check_ajax_request', 'wpclink_care_auto_check_ajax_request' );
add_action( 'wp_ajax_nopriv_wpclink_care_auto_check_ajax_request', 'wpclink_care_auto_check_ajax_request' );
/**
 * CLink Record Licensee Accept Time
 * 
 */
function wpclink_insert_date_accept_licensee(){
	
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 
		 	// License_class
			$record = $_REQUEST['record'];
			if($record == 'now'){
				
				// Time
				$accept_time = current_time("Y-m-d H:i:s",true);
				
				// Record
				$updated = wpclink_update_option('last_license_offer_date',$accept_time);
				
				echo $updated;
			}
			
		}
	}
}
// Register clink license accept datetime function ajax request
add_action( 'wp_ajax_wpclink_insert_date_accept_licensee', 'wpclink_insert_date_accept_licensee' );
/**
 * CLink Print Post Status
 * 
 */
function wpclink_post_status_is_disabled(){
	
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			
			if($post_id > 0){
				
				$content_register_restrict = get_post_meta($post_id,'wpclink_post_register_status',true);
				if($content_register_restrict == '1'){
					echo '1';
				}else{
					echo '-1';
				}
			}
		}
	}
	die();
}
// Register clink post status print function ajax request
add_action( 'wp_ajax_wpclink_post_status_is_disabled', 'wpclink_post_status_is_disabled' );
/**
 * CLink Print Post Status
 * 
 */
function wpclink_post_status_print_editor(){
	
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			
			if($post_id > 0){
				
			// Creator ID
			$current_user_id = get_current_user_id();
				
				
			if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator')){
				$disabled = '';
				$content_register_restrict = get_post_meta($post_id,'wpclink_post_register_status',true);
				if($content_register_restrict == false) $content_register_restrict = 0;
				if($content_register_restrict == '1'){
					$restrict_status = 'Disabled';
				}else{
					$restrict_status = 'Enabled';	
				}
				$clink_id = get_post_meta($post_id,'wpclink_creation_ID',true);
					if(empty($clink_id)){
					
					
					// Need to Update
		
					if(wpclink_user_has_creator_list($current_user_id)){
						
					// Author ID
					$author_id = get_post_field ('post_author', $post_id);
						
						
						// PUBLISH + AUTHOR
					if ( get_post_status ( $post_id ) == 'publish' and $author_id == $current_user_id ) {
						
						echo '<div class=" clink_quota_id"><span class="clinkico"></span> <span class="components-checkbox-control__input-container"><input id="cl_register_process" name="cl_register_process" class="components-checkbox-control__input" '.$disabled.' value="0" type="checkbox" '.checked( $content_register_restrict, 0, false ).' /><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" role="img" class="components-checkbox-control__checked" aria-hidden="true" focusable="false"><path d="M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"></path></svg></span><label>Allow registration</label></div>';
						
						// PUBLISH + NOT AUTHOR
					}else if(get_post_status ( $post_id ) == 'publish' and $author_id != $current_user_id){
						
						$message = '<span class="components-checkbox-control__input-container"><input id="cl_register_process" name="cl_register_process" class="components-checkbox-control__input" value="0" type="checkbox" disabled="disabled" /><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" role="img" class="components-checkbox-control__checked disabled" aria-hidden="true" focusable="false"><path d="M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"></path></svg></span><label class="disabled">Allow registration</label>';
						
						echo $message;
						
					// NOT PUBLISH
					}else{
						
						
						echo '<div class=" clink_quota_id"><span class="clinkico"></span> <span class="components-checkbox-control__input-container"><input id="cl_register_process" name="cl_register_process" class="components-checkbox-control__input" '.$disabled.' value="0" type="checkbox" '.checked( $content_register_restrict, 0, false ).' /><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" role="img" class="components-checkbox-control__checked" aria-hidden="true" focusable="false"><path d="M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"></path></svg></span><label>Allow registration</label></div>';
						
					}
			
						
						
						
					}else{
					
						$message_text = 'You are not the Creator.';
						// Hook
						$message_text = apply_filters('wpclink_not_creator_message',$message_text,$post_id);
						$message = '<span class="components-checkbox-control__input-container"><input id="cl_register_process" name="cl_register_process" class="components-checkbox-control__input" value="0" type="checkbox" disabled="disabled" /><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" role="img" class="components-checkbox-control__checked disabled" aria-hidden="true" focusable="false"><path d="M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"></path></svg></span><label class="disabled">Allow registration</label> <span class="non_creator">'.$message_text.' <br><a href="#">Learn about Creators <span class="dashicons dashicons-external"></span></a></span>';
						echo $message;
					}
				
				
				}else{
						echo '<div class=" clink_quota_id"><span class="clinkico"></span> <span class="components-checkbox-control__input-container"><input id="cl_register_process" name="cl_register_process" class="components-checkbox-control__input" '.$disabled.' value="0" type="checkbox" '.checked( $content_register_restrict, 0, false ).' /><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" role="img" class="components-checkbox-control__checked" aria-hidden="true" focusable="false"><path d="M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"></path></svg></span><label>Allow registration</label></div>';
				}
			}else{
				
				$message_text = 'You are not the Creator.';
				
				// Hook
				$message_text = apply_filters('wpclink_not_creator_message',$message_text,$post_id);
				$message = '<span class="components-checkbox-control__input-container"><input id="cl_register_process" name="cl_register_process" class="components-checkbox-control__input" value="0" type="checkbox" disabled="disabled" /><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" role="img" class="components-checkbox-control__checked disabled" aria-hidden="true" focusable="false"><path d="M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"></path></svg></span><label class="disabled">Allow registration</label> <span class="non_creator">'.$message_text.' <br><a href="#">Learn about Creators <span class="dashicons dashicons-external"></span></a></span>';
				
				echo $message;
			}
				
			}
			
		}
	}
	die();
}
// Register clink post status print function ajax request
add_action( 'wp_ajax_wpclink_post_status_print_editor', 'wpclink_post_status_print_editor' );
/**
 * CLink Record Licensee Accept Time
 * 
 */
function wpclink_post_exclude_save(){
	
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			$state = $_REQUEST['cl_state'];
			
			if($state == "1"){
				$state = "1";
			}else{
				$state = 0;
			}
			if($post_id > 0){
				
				// Record
				$updated = update_post_meta($post_id,'wpclink_post_register_status',$state);
				echo $state.'|'.$updated;
			}
			
		}
	}
}
// Register clink license accept datetime function ajax request
add_action( 'wp_ajax_wpclink_post_exclude_save', 'wpclink_post_exclude_save' );
/**
 * CLink Get Versions by Ajax Request
 * 
 */
function wpclink_get_ajax_versions_ajax_request(){
	
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			
			
				if($has_version = get_post_meta($post_id,'wpclink_versions',true)){
					$versions = $has_version;
					$versions = array_reverse($versions);
					$html = '';
					
					foreach($versions as $single_ver){
						
						$archive_icon = apply_filters('wpclink_archive_version_button','',$post_id,$single_ver);
						
						
						$html .= '<li><a href='.WPCLINK_ID_URL.'/#objects/'.$single_ver.'>'.wpclink_do_icon_clink_ID($single_ver).' </a>'.$archive_icon.'</li>';
					}
					
					echo $html;
				}else{
					echo '';
				}
		}
	}
	die();
}
// Register CLink get versions ajax request
add_action( 'wp_ajax_wpclink_get_ajax_versions_ajax_request', 'wpclink_get_ajax_versions_ajax_request' );
/**
 * CLink Publish Content and Make Version
 * 
 */
function wpclink_make_version_creation(){
	
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			
			$post_id_object = array('ID' => $post_id);
			
			// CLink Media Call
			wpclink_register_creation((object)$post_id_object,'');
			
				if($has_version = get_post_meta($post_id,'wpclink_versions',true)){
					$versions = $has_version;
					$html = '';
					foreach($versions as $single_ver){
						
						
				$archive_icon = apply_filters('wpclink_archive_version_button','',$post_id,$single_ver);
						$html .= '<li><a href="'.WPCLINK_ID_URL.'/#objects/'.$single_ver.'">'.wpclink_do_icon_clink_ID($single_ver).'</a> '.$archive_icon.'</li>';
					}
					
					echo $html;
				}
		}
	}
	die();
}
// Register CLink get versions ajax request
add_action( 'wp_ajax_wpclink_make_version_creation', 'wpclink_make_version_creation' );
/**
 * CLink Update Version by Ajax
 * 
 */
function wpclink_update_version_ajax(){
	
		
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {	
	
	
	$attachment_id = $_REQUEST['post_id'];
	$archive_flag = $_REQUEST['archive_flag'];
	
	// Only Jpeg
	 $type_media = get_post_mime_type($attachment_id);
	 if($type_media == 'image/jpeg' || $type_media == 'image/jpg'){
	 }else{
		 return false;
	 }
	
	update_post_meta( $attachment_id, 'wpclink_loader_status', 'wpupdates' );
	
	
	// Is media is linked?
	$linked_media = get_post_meta($attachment_id,'wpclink_referent_post_link',true);
	if(!empty($linked_media)){
		$linked_flag = true;
	}else{
		$linked_flag = false;
	}
	
	if(wpclink_check_license_by_post_id($attachment_id) > 0) return false;
	
		// Media Permission
	$media_permission = get_post_meta($attachment_id,'wpclink_programmatic_right_categories',true);
	$media_permission_array = explode(",",$media_permission);
	
	// Party
	$clink_party_id = wpclink_get_option( 'authorized_contact' );
	$clink_party_id = get_user_meta( $clink_party_id, 'wpclink_party_ID', true );
	// Image Meta Data
	$attachment_url = wpclink_iptc_image_path( $attachment_id, 'full' );
	
	// Creator
	// Origin Creator for Register Media
	if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
		if($right_holder_id = get_post_meta( $attachment_id, 'wpclink_rights_holder_user_id', true )){
			$current_user_id = $right_holder_id;
			$creator_user_id = $right_holder_id;
		}
		
	}
			
	$reg_creators = wpclink_get_option( 'authorized_creators' );
	
	// If current user is not creator.
	if ( $current_user_id < 1 ) return false;
	
	// Register Process
	if ( isset( $_REQUEST[ 'cl_register_process' ] ) ) {
		if ( $_REQUEST[ 'cl_register_process' ] == 1 ) {
			update_post_meta( $attachment_id, 'wpclink_post_register_status', 1 );
		} else {
			update_post_meta( $attachment_id, 'wpclink_post_register_status', '0' );
		}
	} else {
		update_post_meta( $attachment_id, 'wpclink_post_register_status', '0' );
	}
	if ( $content_register_restrict = get_post_meta( $attachment_id, 'wpclink_post_register_status', true ) ) {
		if ( $content_register_restrict == '1' ) {
			return false;
		}
	}
	
	
	// Registration
	$registration_disallow = get_post_meta($attachment_id,'wpclink_registration_disallow',true);
	if($registration_disallow == 1){
		return false;
	}
			
	// Creator
	$creator_user_info = get_userdata( $current_user_id );
	// Current Creator Names
	$current_creator_array = array(
		strtolower($creator_user_info->first_name),
		strtolower($creator_user_info->last_name)
	);
	
	$display_name = $creator_user_info->display_name;
	$display_name_array = explode(' ',$display_name);
	$display_name_array = array_map( 'strtolower', $display_name_array );
	
	// Meta data Creator Names	
	$metadata_image_array = wpclink_get_image_metadata_value($attachment_url,array('IPTC:By-line','IPTC:CopyrightNotice','IPTC:Credit','IPTC:ObjectName','IPTC:Keywords'));
	
	$creator_name_metadata = $metadata_image_array['IPTC:By-line'];
	
	if($linked_flag == false){
	
	if ( !empty( $creator_name_metadata ) ) {
		$metadata_creator_names = explode( ' ', $creator_name_metadata );
		
			$creator_name_match = wpclink_match_creator_names(
				$metadata_creator_names,
				$current_creator_array,
				$display_name_array);
		
			/*
			 * Apply the Creator Name Match
			 *
			 * - 'creator_name_match' is the value being filtered. */
			$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
		
		if ( $creator_name_match_final ) {
		} else {
			return false;
		}
	}else{
		
	}
		
	}
	
	
	update_post_meta( $attachment_id, 'wpclink_loader_status', 'exifwrites' );
	
	
	// Title and Description
	$get_attachment_title = get_the_title( $attachment_id ); 
	$get_description = wp_get_attachment_caption( $attachment_id );
	
	
	// Creator
	$creator_user_info = get_userdata( $current_user_id );
	
	// Creator ID
	$creatorID = get_user_meta( $current_user_id, 'wpclink_party_ID', true );
	
	
	$creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true );
	
	
	
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
	
	// CLink Language
	$clink_language = wpclink_get_current_site_lang();
	// Terriotory
	$clink_terriory_code = wpclink_get_current_terriority_name();
	
	$action = 'version';
	
		
		$post_guid = wp_get_original_image_url($attachment_id);
		$archive_link = wpclink_get_last_archive($attachment_id);
	
	if($programattic_rights_category = get_post_meta($attachment_id,'wpclink_programmatic_right_categories',true)){
		// Reuse GUID
		$media_license_url = get_bloginfo('url').'/';
		$media_license_url = add_query_arg( 'clink_media_license', '', $media_license_url);
		$reuse_GUID = add_query_arg( 'id', $attachment_id, $media_license_url);
	}else{
		$reuse_GUID = '';
	}
	
	// Create
	$get_post_date  = get_the_date('Y-m-d',$attachment_id);
	$get_post_date .= 'T';
	$get_post_date .= get_the_date('G:i:s',$attachment_id);
	$get_post_date .= 'Z';		
			
			
	if($linked_flag){
		$get_post_date  = get_post_meta($attachment_id,'wpclink_time_of_creation',true);
	}
		
	// Modify		
	$get_post_modified_date  = get_the_modified_time('Y-m-d',$attachment_id);
	$get_post_modified_date .= 'T';
	$get_post_modified_date .= get_the_modified_time('G:i:s',$attachment_id);
	$get_post_modified_date .= 'Z';
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
	
	if($clink_referent_right_holder_display_name = get_post_meta( $attachment_id, 'wpclink_referent_rights_holder_display_name', true )){
		
	}else{
		$clink_referent_right_holder_display_name = '';
	}
			
	if($referent_creator_display_name = get_post_meta( $attachment_id, 'wpclink_referent_creator_display_name', true )){
	}else{
			$referent_creator_display_name = '';
	}
			
	$before_send = array(
			'body' => array(
				'CLinkContentID' => $creationID,
				'post_title' => html_entity_decode( $attachment_title ),
				'iptc_title' => $iptc_title,
				'clink_referent_creation_identifier' => $clink_referent_creation_ID,
				'referent_clink_creatorID' => $clink_referent_creator_ID,
				'referent_creator_display_name' => $referent_creator_display_name,
				'referent_creation_rights_holder_display_name' => $clink_referent_right_holder_display_name,
				'keywords' => $keywords,
				'creator_uri' => $creator_user_info->user_url,
				'attachment_post_url' => preg_replace("/^http:/i", "https:", $attachment_post_url),
				'creation_GUID' => get_the_guid($attachment_id),
				'reuseGUID' => $reuse_GUID,
				'clink_taxonomy_permission' => $clink_taxonomy_permission,
				'creator_display_name' => $creator_user_info->display_name,
				'creator_email' => $creator_user_info->user_email,
				'post_excerpts' => $attachment_excerpt,
				'time_of_creation' => $get_post_date,
				'time_of_modification' => $get_post_modified_date,
				'domain_access_key' => $domain_access_key,
				'site_address' => get_bloginfo( 'url' ),
				'iscc' => $iscc,
				'creditline' => $creditline,
				'clink_creatorID' => $creator_id,
				'creation_access_key' => $creation_access_key,
				'copyright_notice' => $copyright_notice,
				'clink_language' => $clink_language,
				'clink_territory_code' => $clink_terriory_code,
				'archive_web_url' => $archive_link,
				'action' => $action
			), 'timeout' => WPCLINK_API_TIMEOUT, 'method' => 'POST'
		);
			
	
	
	$url_media = WPCLINK_MEDIA_API;
	// Register to CLink.ID
	$response = wp_remote_post(
		$url_media,
		$before_send
	);
	if ( is_wp_error( $response ) ) {
		$wp_error = is_wp_error( $response );
		// Response Debug
		wpclink_debug_log( 'PUBLISH MEDIA ERROR' . print_r( $response, true ) );
		
		
		if($wp_error == 1){
			echo json_encode(wpclink_return_wp_error($response));
			die();
		}
	} else {
		// Response Debug
		wpclink_debug_log( 'PUBLISH MEDIA ' . print_r( $response, true ) );
		$response_json = $response[ 'body' ];
		$resposne_Array = json_decode( $response_json, true );
		
		
			
		
			$return_response = wpclink_return_api_reponse($response);
				
				// Response Code
				$server_status_code = $response['response']['code'];
				// Response Message
				$server_status_message = $response['response']['message'];
				if($return_response === true){
					$response_json = $response['body'];
					$response_check = wpclink_response_check($resposne_Array);
					
					if($response_check == false){
						
			if ( $resposne_Array[ 'status' ] == 'version' ) {
			// RESPONSE
			if ( !empty( $resposne_Array[ 'data' ][ 'id' ] ) ) {
				if ( $prev_data = get_post_meta( $attachment_id, 'wpclink_versions', true ) ) {
					$version_id = array( $resposne_Array[ 'data' ][ 'id' ] );
					$final_array = array_merge( $prev_data, $version_id );
					update_post_meta( $attachment_id, 'wpclink_versions', $final_array );
				} else {
					$version_id = array( $resposne_Array[ 'data' ][ 'id' ] );
					update_post_meta( $attachment_id, 'wpclink_versions', $version_id );
				}
				
$version_id = $resposne_Array['data']['id'];
$version_ready = array(
	'status' => 'ready',
	'version_identifier' => $version_id,
	'version_identifier_encrypt' => $resposne_Array['data']['encrypt_creation_id']
);
update_post_meta($attachment_id,'wpclink_version_ready',$version_ready);
				
			// If Archive
			if($archive_flag == 1){
				do_action('wpclink_add_archive_version_list',$attachment_id,$version_id);
			}
				
				// Version Time
				if($version_modified_time = get_post_meta($attachment_id,'wpclink_versions_time',true)){
					$version_modified_time[$resposne_Array['data']['id' ]] = $get_post_modified_date;
					update_post_meta($attachment_id,'wpclink_versions_time',$version_modified_time);
				}else{
					$version_modified_time = array();
					$version_modified_time[$resposne_Array['data']['id']] = $get_post_modified_date;
					update_post_meta($attachment_id,'wpclink_versions_time',$version_modified_time);
				}
				
		
			}
				
						$response_back = array(
							'complete' => 'success',
							'status' => $resposne_Array['status'],
							'code' => $server_status_code,
							'message' => $server_status_message,
							'data' => array()
						);
				
				
				echo json_encode($response_back);
						die();
						
				
				
		}
						
					}else{
			
							
						echo json_encode($response_check);
						die();
						}
						
					}else{
						echo json_encode($return_response);
						die();
					}
			
			
		
		
		
	}
	
	
	update_post_meta( $attachment_id, 'wpclink_loader_status', 'registrywrites' );
			
		}
	}
	
	die();
	
}
// Register CLink get CLink.ID ajax request
add_action( 'wp_ajax_wpclink_update_version_ajax', 'wpclink_update_version_ajax' );
/**
 * CLink Get CLink.ID by Ajax Request
 * 
 */
function wpclink_get_clink_ID(){
	
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			
			// User ID
			$current_user_id = get_current_user_id();
			
			// CLink.ID
			$clink_id = get_post_meta($post_id,'wpclink_creation_ID',true);
			
			if(empty($clink_id)){
				$clink_id = '';
			}else{
				$clink_id = '<a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$clink_id.'">'.wpclink_do_icon_clink_ID($clink_id).'</a>';
			}
			
			if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator')){
				if(empty($clink_id)){
					
					if(wpclink_user_has_creator_list($current_user_id)){
						$message = '0';
					}else{
					
						$message_text = 'You are not the Creator.';
						// Hook
						$message_text = apply_filters('wpclink_not_creator_message',$message_text,$post_id);
						$message = '<span class="components-checkbox-control__input-container"><input id="cl_register_process" name="cl_register_process" class="components-checkbox-control__input disabled" value="0" type="checkbox" disabled="disabled" /><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" role="img" class="components-checkbox-control__checked" aria-hidden="true" focusable="false"><path d="M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"></path></svg></span><label class="disabled">Allow registration</label> <span class="non_creator">'.$message_text.' <br ><a href="#">Learn about Creators <span class="dashicons dashicons-external"></span></a></span>';
						
					}
					
				}else{
					$message = '0';
				}
			}else{
				$message_text = 'You are not the Creator.';
				
				// Hook
				$message_text = apply_filters('wpclink_not_creator_message',$message_text,$post_id);
				
				$message = '<span class="components-checkbox-control__input-container"><input id="cl_register_process" name="cl_register_process" class="components-checkbox-control__input disabled" value="0" type="checkbox" disabled="disabled" /><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" role="img" class="components-checkbox-control__checked" aria-hidden="true" focusable="false"><path d="M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2"></path></svg></span><label class="disabled">Allow registration</label> <span class="non_creator">'.$message_text.' <br ><a href="#">Learn about Creators <span class="dashicons dashicons-external"></span></a></span>';
				
				
				
			}
			
			// Error
			if($status = get_post_meta($post_id,'wpclink_loader_status',true)){
				if($status == 'error'){
					
					// Error Array
					$error_array =  get_post_meta($post_id,'wpclink_loader_error_data', true);
					$error_array = array_merge($error_array,array('clinkid' => $clink_id));
					
					// Delete
					delete_post_meta($post_id,'wpclink_loader_status');
					delete_post_meta($post_id,'wpclink_loader_error_data');
					
					// JSON
					echo json_encode($error_array);
					die();
				}
			}
			
			// Promotion
			if($trail = wpclink_get_option('promotion_trial')){
				$get_status_array = array('status' => 'promotion_disallow','complete' => 'success', 'clinkid' => $clink_id );
				echo json_encode($get_status_array);
				
				// Delete
				wpclink_delete_option('promotion_trial');
				die();
			}
			
			// CLink ID
			$get_status_array = array('status' => $clink_id, 'complete' => 'success', 'clinkid' => $clink_id, 'message' => $message );
			echo json_encode($get_status_array);
			die();
	
			
			
			
				
		}
	}
	die();
	
}
// Register CLink get CLink.ID ajax request
add_action( 'wp_ajax_wpclink_get_clink_ID', 'wpclink_get_clink_ID' );
/**
 * CLink Save Version by Ajax Request
 * 
 */
function wpclink_post_version_save_ajax(){
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			$version = $_REQUEST['version'];
			if(isset($_REQUEST['archive_version'])){
				$archive_version = $_REQUEST['archive_version'];
			}
			
			if($version == "1"){
				$version = "1";
			}else{
				$version = 0;
			}
			if($post_id > 0){
				
				// Record
				$updated = update_post_meta($post_id,'wpclink_post_version_execute',$version);
				
				if($archive_version == 1){
					$last_version = update_post_meta($post_id,'wpclink_post_record_last_version','1');
				}
				echo $version.'|'.$updated.'|'.$last_version;
			}
			
		}
	}
	die();
}
// Register CLink save post versions ajax request
add_action( 'wp_ajax_wpclink_post_version_save_ajax', 'wpclink_post_version_save_ajax' );
/**
 * CLink Generate ISCC Hash Code for Creation
 * 
 */
function wpclink_generate_iscc_ajax(){
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			$update_iscc = $_REQUEST['update_iscc'];
			
			if($update_iscc == 1){
				
	$post_type = get_post_type($post_id);
				
	if($post_type == 'attachment'){
		$creation_uri = wpclink_get_image_URL( $post_id );
	}else{
		$creation_uri = get_permalink($post_id);
	}
				
	$clink_creationID = get_post_meta($post_id,'wpclink_creation_ID',true);
	$creation_access_key = get_post_meta($post_id,'wpclink_creation_access_key',true);
	// API Request
	$data = array(
		'CLinkContentID' => $clink_creationID,
		'creation_access_key' => $creation_access_key,
		'clink_creation_uri' => $creation_uri,
		'clink_iscc_update' => '1'
	);
				
	$url_iscc = WPCLINK_ISCC_API;
			
	$response = wp_remote_post(
                $url_iscc,
                array(
                    'body' => $data,'timeout' => '45','method' => 'POST'
                )
            );
	
            if ( is_wp_error( $response ) ) {
 
               $wp_error = is_wp_error( $response );			   
			   // Response Debug
				
				
				wpclink_debug_log('ISCC ERROR'.print_r($wp_error,true));
				wpclink_debug_log('ISCC ERROR RES'.print_r($response,true));
				
				
				wpclink_debug_log('ISCC ERROR PRINT'.print_r($response->errors['http_request_failed'],true));
				
				
				if($wp_error == 1){
					echo json_encode(wpclink_return_wp_error($response));
					die();
				}
				
			}else {
				// Response Debug
				wpclink_debug_log('ISCC '.print_r($response,true));
				
				
 
               $response_json = $response['body'];
			   $resposne_Array=json_decode($response_json,true);
				
				
				
				$return_response = wpclink_return_api_reponse($response);
				
				// Response Code
				$server_status_code = $response['response']['code'];
				// Response Message
				$server_status_message = $response['response']['message'];
				
								
				if($return_response === true){
					
					
					$response_json = $response['body'];
					$response_check = wpclink_response_check($resposne_Array);
					
					if($response_check == false){
					if($resposne_Array['status'] == 'iscc_updated'){
						$response_back = array(
							'complete' => 'success',
							'status' => $resposne_Array['status'],
							'code' => $server_status_code,
							'message' => $server_status_message,
							'data' => array()
						);
						
						// Updated
						update_post_meta( $post_id, 'wpclink_iscc_status', 1 );
						
						if($post_type == 'attachment'){
							if ( $creationID = get_post_meta( $post_id, 'wpclink_creation_ID', true ) ) {
								
								$image_path 	= 	wpclink_iptc_image_path( $post_id, 'full' );
								$file_data 		= 	wpclink_get_jpeg_image_data($image_path );
								$sha256_hash 	= 	hash('sha256', $file_data, false );
								
								
								$attachment_path = wpclink_iptc_image_path( $post_id, 'full' );
								$regid_1 =  WPCLINK_ID_URL . '/#objects/' . $creationID;
								$regid_2 = $resposne_Array['data']['iscc'];
								// registry_item_IDat1
								$xmp_metadata_array = array();
								$xmp_metadata_array['registry_item_IDat1'] = $regid_1;
								$xmp_metadata_array['registry_item_IDat3'] = $sha256_hash;
								$xmp_metadata_array['registry_item_IDat2'] = $regid_2;
								
								
								// Write
								wpclink_metadata_writter( $attachment_path , $xmp_metadata_array, $post_id, false, $post_id );
							}
						
						}else if($resposne_Array['status'] == 'creation_key_not_match'){
							echo 'creation_key_not_match';
						}else{
						}
						
						echo json_encode($response_back);
						die();
					
				} else if($resposne_Array['status'] == 'creation_key_not_match'){
			
							$response_back = array(
							'complete' => 'failed',
							'status' => $resposne_Array['status'],
							'code' => '1102',
							'error_type' => 'mismatch',
							'error_headline' => __( 'Request cannot be completed', 'cl_text' ),
							'error_text' => __( 'Secured key associated with the content is missing or not matching with the registry records. Please contact support.', 'cl_text' ),
							'clink_error_status' => '',
							'clink_internal_error_code' => '',
							'clink_internal_error_location' => '',
							'message' => 'CLink Error : Creation Access Key Mismatch',
							'data' => array()
						);
						echo json_encode($response_back);
						die();
						}
						
					}else{
						echo json_encode($response_check);
						die();
					}
			
			
		
				}else{
					echo json_encode($return_response);
					die();
				}
				
				
					
			}
				
			}
			
			
		}
	}
	die();
}
// Register CLink save post versions ajax request
add_action( 'wp_ajax_wpclink_generate_iscc_ajax', 'wpclink_generate_iscc_ajax' );
/**
 * CLink Get Pre Authorized Date by Ajax Request
 * 
 */
function wpclink_get_pre_authorized_date_ajax_request(){
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			
				// Record
				$record = get_post_meta($post_id,'wpclink_reuse_pre_auth_effective_date', true);
			
				echo wpclink_convert_date_to_iso($record);
			
		}
	}
	die();
}
// Register get pre authorized date ajax request
add_action('wp_ajax_wpclink_get_pre_authorized_date_ajax_request','wpclink_get_pre_authorized_date_ajax_request');
/**
 * CLink Get License Class by Ajax Request
 * 
 */
function wpclink_get_license_class(){
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			
				// Record
				$license = get_post_meta($post_id,'wpclink_creation_license_class', true);
				$taxonomy = get_post_meta($post_id,'wpclink_programmatic_right_categories', true);
			
				echo '<p><strong>Class:</strong>';
				wpclink_get_license_class_label($license);
				echo '</p>';
			
				echo '<p><strong>Taxonomy Permission:</strong>';
				wpclink_programmatic_right_categories_label($taxonomy);
				echo '</p>';
			
		}
	}
	die();
}
// Register get license type ajax request
add_action('wp_ajax_wpclink_get_license_class','wpclink_get_license_class');
/**
 * CLink Get License Taxonomy Permission by Ajax Request
 * 
 */
function wpclink_get_taxonomy_permission_ajax_request(){
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			
				// Record
				$taxonomy = get_post_meta($post_id,'wpclink_creation_license_class', true);
				wpclink_programmatic_right_categories_label($taxonomy);
			
		}
	}
	die();
}
// Register get license type ajax request
add_action('wp_ajax_wpclink_get_taxonomy_permission_ajax_request','wpclink_get_taxonomy_permission_ajax_request');
/**
 * CLink Copyright Owner Change Ajax Request
 * 
 */
function wpclink_copyright_change_ajax_request() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
        $change_copyright = $_REQUEST['change_copyright'];
		$form_hit = $_REQUEST['form_hit'];
		
		if(!empty($change_copyright)){
			
			$right_holder = wpclink_get_option('rights_holder');
			$party_checked = NULL; $creator_checked = NULL;
			if($right_holder == 'party'){
				$party_checked = 'checked="checked"';
			}elseif($right_holder == 'creator'){
				$creator_checked = 'checked="checked"';
			}	
		echo '<h2>Select Right Holder <span class="copyright-icon-box" title="" aria-describedby="ui-id-5"></span></h2>
		<p>
		<label><input type="radio" name="right_holder_change_radio" '.$party_checked.'  value="party">Contact</label><br>
		<label><input type="radio" name="right_holder_change_radio" '.$creator_checked.' value="creator">Creator<label>
		</p>';
			
			if($form_hit == "copyright"){
				echo '<input type="hidden" name="form_hit_action" value="1" />'; 
				}else{
				echo '<input type="hidden" name="form_hit_action" value="0" />'; 
			}
		}
	}
	die();
}
add_action( 'wp_ajax_wpclink_copyright_change_ajax_request', 'wpclink_copyright_change_ajax_request' );
/**
 * CLink Change Copyright Submission
 * 
 */
function wpclink_copyright_change_submission_request() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
		$copyright_id = $_REQUEST['cl_copyright_id'];
		
		if(!empty($copyright_id)){
			
			
			if($update_id = wpclink_update_option('rights_holder',$copyright_id)){
				
				echo $update_id;
				
			}
			
		}
	}
	die();
}
add_action( 'wp_ajax_wpclink_copyright_change_submission_request', 'wpclink_copyright_change_submission_request' );
/**
 * CLink Image Metadata Template Use
 * 
 */
function wpclink_image_metadata_get_template_use() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
		$attachment_id = $_REQUEST['post_id'];
		$template_action = $_REQUEST['template_action'];
		
		if(!empty($attachment_id)){
			
			// Author
			$author_id = get_post_field ('post_author', $attachment_id);
			
			if($template_action == "save"){
			update_user_meta($author_id,'wpclink_image_metadata_template_use_enable', "1");
				
				
			}else if($template_action == "notsave"){
			update_user_meta($author_id,'wpclink_image_metadata_template_use_enable', "0");
			}
			
			
			if($template_use = get_user_meta($author_id,'wpclink_image_metadata_template_use', true)){
				$data = array(
				'status' => 'exists',
				'data' => $template_use
				);
			}else{
				$data = array(
				'status' => 'notexists',
				'data' => $template_use
				);
			}
			
			echo json_encode($data);
		}
	}
	die();
}
add_action( 'wp_ajax_wpclink_image_metadata_get_template_use', 'wpclink_image_metadata_get_template_use' );
/**
 * CLink Image Metadata Template Use
 * 
 */
function wpclink_image_metadata_save_template_use() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
		$attachment_id = $_REQUEST['post_id'];
		$metadata_creator = $_REQUEST['metadata_creator'];
		$metadata_credit_line = $_REQUEST['metadata_credit_line'];
		$metadata_copyright = $_REQUEST['metadata_copyright'];
		
		if(!empty($attachment_id)){
			
			
			$data_metadata = array(
				'creator' => $metadata_creator,
				'credit_line' => $metadata_credit_line,
				'copyright' => $metadata_copyright
			);
			
			// Author
			$author_id = get_post_field ('post_author', $attachment_id);
			
			if($template_use = update_user_meta($author_id,'wpclink_image_metadata_template_use', $data_metadata)){
				
				$data = array(
				'status' => 'updated',
				'data' => ''
				);
			}else{
				$data = array(
				'status' => 'notupdated',
				'data' => ''
				);
			}
			
			echo json_encode($data);
		}
	}
	die();
}
add_action( 'wp_ajax_wpclink_image_metadata_save_template_use', 'wpclink_image_metadata_save_template_use' );
/**
 * CLink Change Copyright Submission
 * 
 */
function wpclink_get_filename_attachment_request() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
		$post_id = $_REQUEST['post_id'];
		$apply_filename_enable = $_REQUEST['apply_filename_enable'];
		$apply_filename = $_REQUEST['apply_filename'];
		
		if(!empty($post_id)){
			
					
			if($apply_filename == "yes"){
				
				if($get_apply_filename = wpclink_get_option( 'wpclink_attachment_apply_filename' )){
					wpclink_update_option('wpclink_attachment_apply_filename',"yes");
				}else{
					wpclink_add_option('wpclink_attachment_apply_filename',"yes");
				}
				
				
				
				$scaled_image = wp_get_attachment_url( $post_id );
				echo basename($scaled_image);
			}else{
				if($get_apply_filename = wpclink_get_option( 'wpclink_attachment_apply_filename' )){
					wpclink_update_option('wpclink_attachment_apply_filename',"no");
				}else{
					wpclink_add_option('wpclink_attachment_apply_filename',"no");
				}
				echo "0";
			}
			
		}else{
			echo "0";
		}
	}
	die();
}
add_action( 'wp_ajax_wpclink_get_filename_attachment_request', 'wpclink_get_filename_attachment_request' );
/**
 * CLink Change Copyright Submission
 * 
 */
function wpclink_get_loader_status_request() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
		$post_id = $_REQUEST['post_id'];
		$status = $_REQUEST['status'];
		
		if(!empty($post_id)){
			
			if($status == "get"){
				$get_status =  get_post_meta($post_id,'wpclink_loader_status', true);
				
				
				if($get_status == 'error'){
					$error_array =  get_post_meta($post_id,'wpclink_loader_error_data', true);
					echo json_encode($error_array);
					die();
				}
				$get_status_array = array('status' => $get_status );
				echo json_encode($get_status_array);	
				die();
			}
			
		}else{
			echo "0";
		}
	}
	die();
}
add_action( 'wp_ajax_wpclink_get_loader_status_request', 'wpclink_get_loader_status_request' );
/**
 * Show Rights Holder in Advanced panel
 * 
 */
function wpclink_post_show_right_holder_ajax_request() {
	
	 // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
if($_REQUEST['show'] == 1){
	$post_id = $_REQUEST['post_id'];
	$user_id = get_post_meta($post_id,'wpclink_rights_holder_user_id', true);
	$identifier = get_user_meta($user_id,'wpclink_party_ID',true);
	
	
	// Registered
	if($clink_id = get_post_meta($post_id,'wpclink_creation_ID',true)){
		
		$user_info = get_userdata($user_id);
	
		$html = '<p>'.$user_info->display_name.'</p><p><a href="'.WPCLINK_ID_URL.'/#objects/'.$identifier.'">'.wpclink_do_icon_clink_ID($identifier).'</a></p>';
		
	}else{
		$html = '0';
	}
		
		echo $html;
			
}
	
	}
	die();
	
}
add_action( 'wp_ajax_wpclink_post_show_right_holder_ajax', 'wpclink_post_show_right_holder_ajax_request' );
/**
 * CLink Change Copyright Submission
 * 
 */
function wpclink_get_loader_status_linked_request() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
		$status = $_REQUEST['status'];
		
		
			if($status == "get"){
				
				$get_status =  wpclink_get_option('wpclink_loader_status_linked');
				
				
				if($get_status == 'error'){
					
					$error_array =  wpclink_get_option('wpclink_loader_linked_error_data');
					
					echo json_encode($error_array);
					die();
				}
				
				$get_status_array = array('status' => $get_status );
				echo json_encode($get_status_array);	
				die();
			}
					
		
	}
	die();
}
add_action( 'wp_ajax_wpclink_get_loader_status_linked_request', 'wpclink_get_loader_status_linked_request' );
/**
 * CLink Apply Custom License
 * 
 */
function wpclink_custom_license_apply_request() {
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
		$post_id = $_REQUEST['post_id'];
		
		$custom_url = $_REQUEST['custom_url'];
		$wpclink_right_object = $_REQUEST['right_object'];
		$wpclink_custom_web_statement_rights = $_REQUEST['web_statement_rights'];
		
		$button_label = $_REQUEST['button_label'];
					
		if($post_id > 0){
			
		// Check the user's permissions.
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		// Only Jpeg
		$media_type = get_post_mime_type( $post_id );
		if ( $media_type == 'image/jpeg' || $media_type == 'image/jpg' ) {} else {
			return false;
		}
		// License Selected Type
		update_post_meta( $post_id, 'wpclink_license_selected_type', 'custom' );
			
		// Web Statement of Rights
		if ( isset( $_REQUEST[ 'button_label' ] ) ) {
			if ( !empty($button_label) ) {
				// Show Button
				update_post_meta( $post_id, 'wpclink_license_button', $button_label );
			} else {
				update_post_meta( $post_id, 'wpclink_license_button', 'getrights' );
			}
		}
		// Web Statement of Rights
		if ( isset( $_REQUEST[ 'web_statement_rights' ] ) ) {
		if ( $wpclink_right_object == 1 ) {
			
			
			// Right Objec ID
			$right_object_id = update_post_meta( $post_id, 'wpclink_right_object', 1 );
			
			// Right ID
			$get_right_id = WPCLINK_ID_URL . '/#objects/' . get_post_meta( $post_id, 'wpclink_right_ID', true );
			
			update_post_meta( $post_id, 'wpclink_custom_web_statement_rights', sanitize_text_field( $get_right_id ) );
			$xmp_metadata_array[ 'webstatement' ] = $get_right_id;
			
		} else {
			update_post_meta( $post_id, 'wpclink_custom_web_statement_rights', sanitize_text_field($_REQUEST[ 'web_statement_rights' ]) );
			
			update_post_meta( $post_id, 'wpclink_right_object', 0 );
			
			$xmp_metadata_array[ 'webstatement' ] = $_REQUEST[ 'web_statement_rights' ];
		}
	}
	// Licesor URL
	if ( isset( $_REQUEST[ 'custom_url' ] ) ) {
	update_post_meta( $post_id, 'wpclink_custom_url',sanitize_text_field($_REQUEST[ 'custom_url' ]));
	$xmp_metadata_array[ 'licensor_url' ] = $_REQUEST[ 'custom_url' ];
		
	}
	$current_user_id = get_current_user_id();
	$creator_user_info = get_userdata( $current_user_id );
	$licensor_display_name = $creator_user_info->first_name . ' ' . $creator_user_info->last_name;
	$xmp_metadata_array[ 'licensor_display_name' ] = $licensor_display_name;
	$xmp_metadata_array[ 'licensor_email' ] = $creator_user_info->user_email;
	// Source
	$xmp_metadata_array[ 'photoshop_source' ] = $licensor_display_name;
	$clink_creatorID = get_user_meta( $current_user_id, 'wpclink_party_ID', true );
	$xmp_metadata_array[ 'licensor_ID' ] = WPCLINK_ID_URL . '/#objects/' . $clink_creatorID;
	if ( $creationID = get_post_meta( $post_id, 'wpclink_creation_ID', true ) ) {
		// Image Creation ID
		$xmp_metadata_array[ 'licensor_image_ID' ] = WPCLINK_ID_URL . '/#objects/' . $creationID;
	}
	$attachment_url = wpclink_iptc_image_path( $post_id, 'full' );
			
			
	// Push Changes
	wpclink_push_metadata($post_id,$xmp_metadata_array);
			
	// Write
	wpclink_metadata_writter( $attachment_url, $xmp_metadata_array, $post_id, false, $post_id );
			
	// @Hook at the end of metadata media update
	do_action('wpclink_media_custome_license_update_end',$post_id);
			
	echo "complete";
	
		}
	}
	die();
}
add_action( 'wp_ajax_wpclink_custom_license_apply_request', 'wpclink_custom_license_apply_request' );
/**
 * CLink Check Content License Media Ajax
 * 
 */
function wpclink_check_content_license_ajax() {
 	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {
			
				if($_REQUEST['cl_action'] == 'check'){
	
					// Post ID
					$post_id = $_REQUEST['post_id'];
					// Images
					$images = $_REQUEST['images'];
					
					$attachment_ids = array();
					$flag_found = false;
					
					
					if($contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true )){
					
					
					$images_array = json_decode(stripslashes($images));
					
					foreach($images_array as $single_image){
						
						// Attachment ID
						$attach_id = wpclink_find_image_id_by_url((string)$single_image);
						
						
						if($attach_id > 0){
							if($linked_media = get_post_meta($attach_id,'wpclink_referent_post_link',true)){
								if(!empty($linked_media)){
									
									$title = get_the_title($attach_id);
									
									if($permissions = get_post_meta($attach_id,'wpclink_programmatic_right_categories',true)){
										
									}else{
										$permissions = '';
									}
									
									$flag_found = true;
									
									$attachment_ids[$single_image] = array('id' => $attach_id,
															   				'title' => $title,
																		    'permissions' => $permissions,
															   				'src' => $single_image);
									
									
								}
							}
							
						}
					}
					
						if($flag_found){
							$response = array('status' => 'found',
											  'data' => array('attachments' => $attachment_ids)
											  );
							
							echo json_encode($response);
							die();
						}
					
						
					}
					$response = array('status' => 'notfound');
					echo json_encode($response);
					die();
					
				}
			
		}
	}
	
	$response = array('status' => 'notallowed');
	echo json_encode($response);
	die();
}
add_action( 'wp_ajax_wpclink_check_content_license_ajax', 'wpclink_check_content_license_ajax' );
/**
 * CLink Get Versions by Ajax Request
 * 
 */
function wpclink_get_version_list_ajax(){
	
	if(is_user_logged_in()){
    	if ( isset($_REQUEST) ) {			
		 	// License_class
			$post_id = $_REQUEST['post_id'];
			$action_show_list = $_REQUEST['action_show_list'];
			if($action_show_list == 1){
			
			
				if($has_version = get_post_meta($post_id,'wpclink_versions',true)){
					$versions = $has_version;
					$versions = array_reverse($versions);
					
					$label = 'Version';
					if(count($versions) > 1) $label = 'Versions';
				
					$html = '<table width="100%" class="table-archive" border="0"><tbody><tr><th width="100%">'.$label .'</th></tr></tbody><tbody>';
					
					
					foreach($versions as $single_ver){
						
						$archive_icon = apply_filters('wpclink_archive_version_button','',$post_id,$single_ver);
						$html .= '<tr><td><a href='.WPCLINK_ID_URL.'/#objects/'.$single_ver.'>'.wpclink_do_icon_clink_ID($single_ver).' </a>'.$archive_icon.'</td></tr>';
					}
					
					$html.='</tbody></table>';
					
					echo $html;
				}else{
					echo '';
				}
			}
		}
	}
	die();
}
// Register CLink get versions ajax request
add_action( 'wp_ajax_wpclink_get_version_list_ajax', 'wpclink_get_version_list_ajax' );
/**
 * CLink Process Rest Media
 * 
 */
function wpclink_process_rest_media() {
 
    if ( isset($_REQUEST) ) {    
		$post_id = $_REQUEST['post_id'];
		$action = $_REQUEST['cl_action'];
		
		if($action == 'rest_images'){
		
			if($data_array = get_post_meta($post_id,'wpclink_rest_images_metadata',true)){
				// Write
				wpclink_metadata_writter('',$data_array,$post_id,false);
				// Delete
				delete_post_meta($post_id,'wpclink_rest_images_metadata');
				
				echo 1;
			}
		
		}
		
	}
	die();
}
add_action( 'wp_ajax_wpclink_process_rest_media', 'wpclink_process_rest_media' );
/**
 * CLink Archive Link Fix
 * 
 */
function wpclink_archive_link_fix($url = ''){
    if(empty($url)) return false;
    $url = explode("/",$url );
    $url[4] = $url[4].'mp_';
    $url = implode('/',$url);
	return preg_replace('/\?.*/', '', $url);
}
/**
 * CLink Get Box Metadata Ajax
 * 
 */
function wpclink_get_box_metadata_ajax() {
 
    if ( isset($_REQUEST) ) { 
        
        if (is_numeric($_REQUEST['cl_box_number'])) {
            $box_number = $_REQUEST['cl_box_number'];
        }else{
            $box_number = '';
        }
		
		if(isset($_REQUEST['attach_id'])){
			$attach_id = $_REQUEST['attach_id'];
			$clink_action = $_REQUEST['cl_metadata_action'];
            
            
            // Is media is linked?
            $linked_media = get_post_meta( $attach_id, 'wpclink_referent_post_link', true );
            if ( !empty( $linked_media ) ) {
                $linked_flag = true;
            } else {
                $linked_flag = false;
            }

            
           
        
		
			if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'wpclink-ajax-nonce' ) ) {
				die('400');
			}else{
				if($clink_action == 'archive'){
					$archive_id = $_REQUEST['archive_id'];
                    
                    if($linked_flag){
                        if($creator_ID = get_post_meta($attach_id,'wpclink_referent_creator_party_ID',true)) {
                            $creator_ID_url = WPCLINK_ID_URL.'/#objects/'.$creator_ID;
                        }
                    }else{
                        if($creator_ID = get_post_meta($attach_id,'wpclink_creator_ID',true)) {
                            $creator_ID_url = WPCLINK_ID_URL.'/#objects/'.$creator_ID;
                        }
                    }
                    
						
					$creation_ID = get_post_meta($attach_id,'wpclink_creation_ID',true);
					$content_url = $creation_ID;
					
					$image_url = wp_get_attachment_image_src( $attach_id, 'full' );
										
					$time_of_modification = get_post_meta($attach_id,'wpclink_published_time',true);
                    
                    
                     if (function_exists('wpclink_show_archive_list_popup')) {
                    $connector_data = wpclink_get_archive_connector_id($attach_id);
                    if(!empty($connector_data['extension_id'])){
					   $extension_id = $connector_data['extension_id'];
					   if($archive_download_list = wpclink_get_extension_meta($extension_id,'archive_download_list',true)){
						
						$archive_download_list = unserialize($archive_download_list);
						
                        if(empty($archive_download_list)){
                            echo '<span class="archive_empty">No Archive</span>';
                        }else{
                           
						// Reverse for latest
						//$archive_download_list = array_reverse($archive_download_list);
						if(!empty($archive_download_list)){
					
								foreach($archive_download_list as $key => $value){
                                    
									$link_to_show = substr($key,0,14);
									$build_link = WPCLINK_PREMIUM_ARCHIVE_SERVER.'/'.WPCLINK_PREMIUM_ARCHIVE_COLLECTION.'/'.$link_to_show.'/'.$value[1];
									$archive_link = $build_link;
                                    
                                                                                                  
                                    if($archive_id == $value[0]){
                                        wpclink_metadata_level_3(wpclink_archive_link_fix($archive_link), $creator_ID_url, $content_url, $attach_id, 'archive', $value[0], $box_number );
                                        
                                        
								    }	
                                    
								}
						}else{
							echo '<span class="archive_empty">No Archive</span>';
						}
                            
                        }
					}
				}else{
					echo '<span class="archive_empty">No Archive</span>';
				}
	
                    
                }
				
				}elseif($clink_action == 'ingredients'){
                        if($linked_flag){
                            if($creator_ID = get_post_meta($attach_id,'wpclink_referent_creator_party_ID',true)) {
                                $creator_ID_url = WPCLINK_ID_URL.'/#objects/'.$creator_ID;
                            }
                        }else{
                            if($creator_ID = get_post_meta($attach_id,'wpclink_creator_ID',true)) {
                                $creator_ID_url = WPCLINK_ID_URL.'/#objects/'.$creator_ID;
                            }
                        }
							
						$creation_ID = get_post_meta($attach_id,'wpclink_creation_ID',true);
						$content_url = $creation_ID;
						
						$image_url = wp_get_attachment_image_src( $attach_id, 'full' );
						
						
						$time_of_modification = get_post_meta($attach_id,'wpclink_published_time',true);
						wpclink_metadata_level_3($image_url[0], $creator_ID_url, $content_url, $attach_id, 'ingredients', '', $box_number );
                    
                                        
                    
                    
					}
		}
	}
}
	die();
}
add_action( 'wp_ajax_wpclink_get_box_metadata_ajax', 'wpclink_get_box_metadata_ajax' );
add_action('wp_ajax_nopriv_wpclink_get_box_metadata_ajax', 'wpclink_get_box_metadata_ajax'); 
/**
 * CLink Get Metadata List Ajax
 * 
 */
function wpclink_get_metadata_list_ajax() {
 
    if ( isset($_REQUEST) ) { 
		
		if(isset($_REQUEST['attach_id'])){
			$attach_id = $_REQUEST['attach_id'];
			$clink_action = $_REQUEST['cl_metadata_action'];
            
            if(is_numeric($_REQUEST['cl_box_number'])){
                $box_number = $_REQUEST['cl_box_number'];
            }else{
                $box_number = ''; 
            }
		
			if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'wpclink-ajax-nonce' ) ) {
				die('400');
			}else{
				if($clink_action == 'archive'){
                    
                    $archive_id = $_REQUEST['archive_id'];
					if (function_exists('wpclink_show_archive_list_popup')) {
                    $connector_data = wpclink_get_archive_connector_id($attach_id);
                    if(!empty($connector_data['extension_id'])){
					   $extension_id = $connector_data['extension_id'];
					   if($archive_download_list = wpclink_get_extension_meta($extension_id,'archive_download_list',true)){
						
						$archive_download_list = unserialize($archive_download_list);
						
						// Reverse for latest
						//$archive_download_list = array_reverse($archive_download_list);
						if(!empty($archive_download_list)){
					
								foreach($archive_download_list as $key => $value){
                                    
									$link_to_show = substr($key,0,14);
									$build_link = WPCLINK_PREMIUM_ARCHIVE_SERVER.'/'.WPCLINK_PREMIUM_ARCHIVE_COLLECTION.'/'.$link_to_show.'/'.$value[1];
									$archive_link = $build_link;
                                    
                                                                                                  
                                    if($archive_id == $value[0]){
                                                                          
                                        echo wpclink_show_metadata_list(wpclink_archive_link_fix($archive_link ), $box_number);
								    }	
                                    
								}
						}else{
							echo '<span class="archive_empty">No Archive</span>';
						}
					}
				}else{
					echo '<span class="archive_empty">No Archive</span>';
				}
                        
                    }
											
										
				}elseif($clink_action == 'ingredients'){
						$image_url = wp_get_attachment_image_src( $attach_id, 'full' );
					
						echo wpclink_show_metadata_list($image_url[0], $box_number);
						
					}
		}
	}
}
	die();
}
add_action( 'wp_ajax_wpclink_get_metadata_list_ajax', 'wpclink_get_metadata_list_ajax' );
add_action('wp_ajax_nopriv_wpclink_get_metadata_list_ajax', 'wpclink_get_metadata_list_ajax'); 
/**
 * CLink Load Binary Metadata Ajax
 * 
 */
function wpclink_load_binary_metadata_ajax() {
 
    if ( isset($_REQUEST) ) { 
		
		if(isset($_REQUEST['attach_id'])){
			$attach_id = $_REQUEST['attach_id'];
			$clink_action = $_REQUEST['cl_metadata_action'];
            
            $target_metadata = $_REQUEST['target_metadata'];
		
			if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'wpclink-ajax-nonce' ) ) {
				die('400');
			}else{
                
				if($clink_action == 'archive'){
					$archive_id = $_REQUEST['archive_id'];
                    
                    if (function_exists('wpclink_show_archive_list_popup')) {
                    $connector_data = wpclink_get_archive_connector_id($attach_id);
                    if(!empty($connector_data['extension_id'])){
					   $extension_id = $connector_data['extension_id'];
					   if($archive_download_list = wpclink_get_extension_meta($extension_id,'archive_download_list',true)){
						
						$archive_download_list = unserialize($archive_download_list);
						
						// Reverse for latest
						//$archive_download_list = array_reverse($archive_download_list);
						if(!empty($archive_download_list)){
					
								foreach($archive_download_list as $key => $value){
                                    
									$link_to_show = substr($key,0,14);
									$build_link = WPCLINK_PREMIUM_ARCHIVE_SERVER.'/'.WPCLINK_PREMIUM_ARCHIVE_COLLECTION.'/'.$link_to_show.'/'.$value[1];
									$archive_link = $build_link;
                                    
                                                                                                  
                                    if($archive_id == $value[0]){
                                                                          
                                                                          
                                        echo wpclink_show_metadata_list_binary(wpclink_archive_link_fix($archive_link),$target_metadata);
								    }	
                                    
								}
						}else{
							$archive_link = '';
						}
					}
				}else{
					$archive_link = '';
				}
                        
                    }
				
				
				}elseif($clink_action == 'ingredients'){
						$image_url = wp_get_attachment_image_src( $attach_id, 'full' );
												
						echo wpclink_show_metadata_list_binary($image_url[0],$target_metadata);
						//wpclink_metadata_level_3($attachment_url, $creator_ID_url, $content_url, $attach_id );
					}
		}
	}
}
	die();
}
add_action( 'wp_ajax_wpclink_load_binary_metadata_ajax', 'wpclink_load_binary_metadata_ajax' );
add_action('wp_ajax_nopriv_wpclink_load_binary_metadata_ajax','wpclink_load_binary_metadata_ajax'); 
/**
 * CLink Metadata Level 3 Ajax
 * 
 */
function wpclink_metadata_level_3_ajax() {
 
    if ( isset($_REQUEST) ) { 
		
		if(isset($_REQUEST['attach_id'])){
			$attach_id = $_REQUEST['attach_id'];
            
            if(is_numeric($_REQUEST['box_number'])){
                $box_number = $_REQUEST['box_number'];
            }else{
                $box_number = ''; 
            }
            
            
             // Is media is linked?
            $linked_media = get_post_meta( $attach_id, 'wpclink_referent_post_link', true );
            if ( !empty( $linked_media ) ) {
                $linked_flag = true;
            } else {
                $linked_flag = false;
            }

		
			if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'wpclink-ajax-nonce' ) ) {
				die('400');
			}else{
				
                

                
                
                if($linked_flag){
                        if($creator_ID = get_post_meta($attach_id,'wpclink_referent_creator_party_ID',true)) {
                            $creator_ID_url = WPCLINK_ID_URL.'/#objects/'.$creator_ID;
                        }
                    }else{
                        $author_id = get_post_field( 'post_author', $attach_id );
                        if($creator_ID = get_user_meta( $author_id, 'wpclink_party_ID', true )){
                            $creator_ID_url = WPCLINK_ID_URL.'/#objects/'.$creator_ID;
                        }else{
                            $creator_ID_url = '';    
                        }
                }
                
					
				$creation_ID = get_post_meta($attach_id,'wpclink_creation_ID',true);
				$content_url = $creation_ID;
				
				$image_url = wp_get_attachment_image_src( $attach_id, 'full' );
                $image_url_large = wp_get_attachment_image_src( $attach_id, 'large' );
							
				$time_of_modification = get_post_meta($attach_id,'wpclink_published_time',true);
						
				echo '<div class="wpclink_metadata_level_3_box">';
				echo '<div class="wpclink-close-popup"><span class="close-icon"></span></div>';
				echo '<div class="wpclink_metadata_sidebar">';
				echo '<span class="sub-heading-box">Live <span class="infopopup"><span class="infdes">The overview of the file and the order of its ingredients</span></span></span>';
                
				wpclink_show_metadata_tree_short_new($image_url[0], $attach_id);
                
                if (function_exists('wpclink_show_archive_list_popup')) {
                    
                   
                    echo '<span class="sub-heading-box">Archive <span class="infopopup"><span class="infdes">The archive of the file and the order of its ingredients</span></span></span>';
                    
                    
                    $connector_data = wpclink_get_archive_connector_id($attach_id);
                    
                    
	
				if(!empty($connector_data['extension_id'])){
					$extension_id = $connector_data['extension_id'];
					if($archive_download_list = wpclink_get_extension_meta($extension_id,'archive_download_list',true)){
						
						$archive_download_list = unserialize($archive_download_list);
						
						// Reverse for latest
						//$archive_download_list = array_reverse($archive_download_list);
						if(!empty($archive_download_list)){
					
								foreach($archive_download_list as $key => $value){
									$link_to_show = substr($key,0,14);
									$build_link = WPCLINK_PREMIUM_ARCHIVE_SERVER.'/'.WPCLINK_PREMIUM_ARCHIVE_COLLECTION.'/'.$link_to_show.'/'.$value[1];
									$archive_link = $build_link;
      
                                    
                                wpclink_show_metadata_tree_short_new( wpclink_archive_link_fix($archive_link), $attach_id, $value[0], 'archive');
                                    
                                break;
                                 
                                    
								}
						}else{
							echo '<span class="archive_empty">No Archive</span>';
						}
					}
				}else{
					echo '<span class="archive_empty">No Archive</span>';
				}
	
                    
                }
				echo '<div class="compared_images"><a class="compare_button" data-status="inactive">Choose comparision</a></div>';
				
				echo '</div>';
				echo '<div class="wpclink_metadata_actions"><a class="backto_popup">&#8592; Back</a><a class="close_btn"></a><span class="fullscreen-btn metadata_actions" data-fullscreen="0"></span><a class="metadata_btn metadata_actions" data-metadata="0">&#x3C;Embedded/&#x3E;</a><a class="syncscroll" data-status="0">Scroll</a> <a class="synctags" data-status="0">Tags</a> <a class="sidebyside_btn" data-side="0">Side by Side</a> </div>';
				echo '<div class="wpclink_metadata_thumbnail" style="background-image:url('.$image_url_large[0].')"></div>';
				echo '<div class="wpclink_metadata_thumbnail_archive"></div>';
				echo '<div class="wpclink_metadata_level_3">';   
                
			
				wpclink_metadata_level_3($image_url[0], $creator_ID_url, $content_url, $attach_id, 'ingredients', '', $box_number );
                
                
				echo '</div>';
				echo '</div>';
			}
		}
	}
	die();
}
add_action( 'wp_ajax_wpclink_metadata_level_3_ajax', 'wpclink_metadata_level_3_ajax' );
add_action('wp_ajax_nopriv_wpclink_metadata_level_3_ajax', 'wpclink_metadata_level_3_ajax'); 
/**
 * CLink Metadata Level 3 Popup
 * 
 */
function wpclink_metadata_level_3_popup(){
	// Popup
	echo '<div class="wpclink_metadata_popup_wrapper"><div class="wpclink_metadata_popup"></div><div class="wpclink_metadata_compare_popup"><div class="sidebar-left"></div><div class="compare_center"><div class="ingredients-side ui-widget-content"></div><div class="archive-side"></div></div><div class="sidebar-right"></div></div></div>';
}
add_action('wp_footer','wpclink_metadata_level_3_popup');
/**
 * CLink Register C2PA Level 3 Scripts
 * 
 */
function wpclink_register_c2pa_level3_scripts(){
	// Reuse
	wp_enqueue_style( 'cl-checkout', plugins_url('public/css/'.wpclink_compress_static_files('checkout.css'), dirname(__FILE__) ) );
	// Licensable Media
	 wp_enqueue_script( 'cl-licensable-media-level3', plugins_url( 'public/js/'.wpclink_compress_static_files('licensable-level3.js'), dirname(__FILE__)  )  , array('jquery'), '1.0.0', true ); 
	wp_localize_script('cl-licensable-media-level3', 'wpclink_vars', array(
		'url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('wpclink-ajax-nonce')
	));
	wp_enqueue_style('cl-jquery-ui-css', plugins_url('public/css/jquery-ui.css', dirname(__FILE__)),false,'1.0',false);
	wp_enqueue_script( 'jquery-ui-resizable' );
}
// Register C2PA Level 3 Scripts
add_action( 'wp_enqueue_scripts', 'wpclink_register_c2pa_level3_scripts');
