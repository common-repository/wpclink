<?php
/**
 * CLink Link List Functions
 *
 * CLink linked content list for verification of canonical
 *
 * @package CLink
 * @subpackage Link Manager
 */
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Complete List of Linked Content Reversion 3
 * 
 * List of the content which are linked to the sites
 * 
 * @param string $content_id  content id
 * @param string $site_url  url of site
 * 
 * @return <type>
 */
function wpclink_generate_linked_post_list($content_id = 0, $site_url = false){
	
	global $post;
	global $wpdb;
	// Table Prefix
	$table_license = $wpdb->prefix . 'wpclink_licenses';
	$license_list = $wpdb->get_results( "SELECT * FROM $table_license WHERE mode = 'linked' ", ARRAY_A );
	
	$ref_posts = array();
	
	foreach($license_list as $list_single){
		
		$referent_post_id = wpclink_get_license_meta($list_single['license_id'],'referent_post_id',true);
		
		if(!empty($referent_post_id)){
			
			if($referent_post_id == $content_id){
			$ref_posts[] = $referent_post_id;
				
			}
		}
		
	}
	
	
	
	
	
	if ( !empty($ref_posts) ) {
	
	$linked_contents = $ref_posts;
	$all_post_sync_id = array();
	$origin_id = array();
	$display_post = array();
	$correct_id = array();
	
		
	
	  
	$all_sync_posts = get_posts( array( 'meta_key' => 'wpclink_referent_post_link','posts_per_page' => -1 ) ); 	
		
		
		
  	foreach($all_sync_posts as $single_sync){
		
		$post_sync_id = $single_sync->ID;
		$get_origin_id = get_post_meta($post_sync_id,'wpclink_referent_post_link', true);
		$catched_id = $get_origin_id[$site_url]['origin_id'];
		// Store ORIGIM
		$origin_id[] = $catched_id;
		// Strore Origin to link
		$origin_id_to_link[$catched_id] = $post_sync_id;
	}
		
		
	
	
	
	foreach($linked_contents as $linked_content_id){
			
			
			if(isset($origin_id_to_link[$linked_content_id])){
				$found_ids[] = $origin_id_to_link[$linked_content_id];
			}
	}
	
	
	// If post not found 404
	if(empty($found_ids)){
		echo '<item><status_code>404</status_code></item>';
		return false;
	}
		
	$cl_query = new WP_Query( array( 
        'post__in' => $found_ids,
		'ignore_sticky_posts' => 1,
        'orderby' => 'ID'
    )  ); 
		
		/*
		echo '<pre>';
		print_r($cl_query);
		echo '</pre>(Rocket)';
		
		*/
		
	if ( $cl_query->have_posts() ) {
		while ( $cl_query->have_posts() ) {
				$cl_query->the_post();
			
				
		$origin_id_show = get_post_meta(get_the_ID(),'wpclink_referent_post_link', true);
		$origin_id_show_now = $origin_id_show[$site_url]['origin_id'];
	?><item><link><?php echo get_permalink(); ?></link><post_id><?php echo get_the_ID(); ?></post_id><ref_id><?php echo $origin_id_show_now; ?></ref_id></item><?php
		}
		wp_reset_postdata();
	}else{ 

// If post not found
?><item><status_code><?php echo '404'; ?></status_code></item><?php 
	}
	}else{
		return false;
	}
		
}
/**
 * CLink Content is in Referent Linked List
 * 
 * @return boolean
 */
function wpclink_is_linked_post_published(){
	if(is_single() || is_page()){
		$post_id = wpclink_global_id();
		if($ok_sync = get_post_meta($post_id,'wpclink_referent_post_link', true)){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
