<?php
/**
 * CLink Canonical Functions
 *
 * CLink canonical verifications and execution functions
 *
 * @package Clink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Create Canonical URL for Post
 * 
 * @param string $url url of the post 
 * 
 * @return string
 */
function wpclink_show_cononical($url = false){
$cl_options = wpclink_get_option( 'preferences_general' );
// Site URL
$link_site_url = apply_filters('cl_site_link',$cl_options['sync_site']);;
	
	global $post;
	$page_id = $post->ID;
	$license_data = wpclink_get_license_by_linked_post_id($page_id);
	$link_site_url = $license_data['site_url'];
	$sync_key = get_post_meta( $page_id, 'wpclink_referent_post_link', true );
	if ( ! empty( $sync_key ) ) {
		$canonical = $sync_key[$link_site_url]['canonical'];
		return $canonical;
	}else{
		return $url;
	}
}
/**
 * CLink Create HTML Canonical URL
 */
function wpclink_do_canonical_tag(){
		echo '<link rel="canonical" href="'.wpclink_show_cononical().'" />';
}
/**
 * CLink Canonical URL
 */
function wpclink_do_canonical_tag_get(){
		return wpclink_show_cononical();
}
/**
 * CLink Overwrite WP SEO (YOAST) URL and Rank Math
 * 
 */
function wpclink_remove_canonical() {
	global $post;
	if(isset($post->ID)){
	$page_id = $post->ID;
		// Disable for 'search' page
		$sync_key = get_post_meta( $page_id, 'wpclink_referent_post_link', true );
		if ( ! empty( $sync_key ) ) {
					
			// WordPress Canonical
			remove_action('wp_head', 'rel_canonical');
			
			$active_plugins = get_option( 'active_plugins', array());
			
			// check for plugin using plugin name
			if ( in_array( 'wordpress-seo/wp-seo.php', (array)$active_plugins ) ||
				 in_array( 'wp-seopress/seopress.php', (array)$active_plugins ) ||
				 in_array( 'all-in-one-seo-pack/all_in_one_seo_pack.php', (array)$active_plugins ) ||
				 in_array( 'seo-by-rank-math/rank-math.php', (array)$active_plugins ) ||
				 in_array( 'autodescription/autodescription.php', (array)$active_plugins ) ||
				 in_array( 'squirrly-seo/squirrly.php', (array)$active_plugins ) ) {
				
							
				// Rank Math
				add_filter('rank_math/frontend/canonical',			'wpclink_do_canonical_tag_get', 10, 1 );
				add_filter('wpseo_canonical',						'wpclink_do_canonical_tag_get', 10, 1 );
				add_filter('aioseo_canonical_url',					'wpclink_do_canonical_tag_get', 10, 1 );
				add_filter('the_seo_framework_rel_canonical_output','wpclink_do_canonical_tag_get', 10, 1 );
				add_filter('sq_canonical',							'wpclink_do_canonical_tag_get', 99, 1);
				add_filter('seopress_titles_canonical',				'wpclink_do_canonical_tag', 10, 1 );
			
				
			}else{ 
				
				
				// CLink Canonical
				add_action('wp_head','wpclink_do_canonical_tag');
			}
			
			
			
		}
	}
}
// CLink canonical url overwrite
add_action('wp', 'wpclink_remove_canonical');
/**
 * Get Liecense Voilation by Site URL
 * 
 * @param string $site_url url of site 
 * 
 * @return mixed string|boolean
 */
function wpclink_first_license_violation($site_url = false){
	global $wpdb;
	
	// CLINK  TABLE
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses';
	
	$clink_site_url = urldecode($site_url);
	
	$mylink = $wpdb->get_row( "SELECT * FROM $clink_sites_table WHERE site_url LIKE '%$clink_site_url%' AND mode = 'referent' AND verification_status = 'fail' ", ARRAY_A );
	
	
	if ( null !== $mylink ) {
		
		$linked_post_id = $mylink['post_id'];
		
		return get_permalink($linked_post_id);
	}else{
		 return false;
	}
	
}
/**
 * CLink Linked Site Canonical Verification
 * 
 * @param strting $single_url url of linked site
 * @param string $site_name name of linked site
 * 
 * @return boolean
 */
function wpclink_is_canonical_valid($single_url = false, $site_name = false){
	
	 $weburl = $single_url;
		$found = false;
		$html = wpclink_fetch_content_by_curl($weburl);
         
		//parsing begins here:
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		
		$link_nodes = $doc->getElementsByTagName('link');
		
		for ($link = 0; $link < $link_nodes->length; $link++){
			$mylink = $link_nodes->item($link);
			if($mylink->getAttribute('rel') == 'canonical' ){
				$full_url = $mylink->getAttribute('href');
				
				// Find WP
				if (strpos($full_url, $site_name) !== false) {
 			   		$found = true;
					break;
				}
			}
				
		}
		if($found){
			return true;
		}
		return false;
}
/**
 * CLink Linked Content Canonical Verification
 * 
 * @param strting $single_url url of linked site
 * @param string $site_name name of linked site
 * 
 * @return boolean
 */
function wpclink_is_canonical_valid_linked_content($single_url = false, $site_name = false){
	 	$weburl = $single_url;
		$found = false;
		$html = wpclink_fetch_content_by_curl($weburl);
		//parsing begins here:
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$link_nodes = $doc->getElementsByTagName('link');
		
		for ($link = 0; $link < $link_nodes->length; $link++){
			$mylink = $link_nodes->item($link);
			if($mylink->getAttribute('rel') == 'canonical' ){
				$full_url = $mylink->getAttribute('href');
			
				// Find WP
				if (strpos($full_url, $site_name) !== false) {
					// Found in [0]link
 			   		$found = true;
					break;
				}else{
				
					// Not Found in [0]link
 			   		$found = false;
					break;
					
				}
			}
			
		}
		if($found){
			return true;
		}
		return false;
}
/**
 * CLink Canonical Linked Content Verification Cycle
 * 
 * Record How many times linked content voilate the cononical url
 * 
 */
function wpclink_verify_canonical_all_links(){
	
	// Not again.
	if(isset($_GET['canonical_flag']) and $_GET['canonical_flag'] == '1'){
		return false;
	}
	
	$all_links = wpclink_get_all_license_linked();
	foreach($all_links as $single_link){
		// Referent Site
		$site_url = $single_link['site_url'];
		
		// Linked Post ID
		$reused_post_id = $single_link['post_id'];
		
		if ( get_post_status ( $reused_post_id ) ) {
		}else{
			continue;
		}
		
		// Check Post Type
		$post_type = get_post_type( $reused_post_id);
		if (  $post_type == 'post' || 
			  $post_type == 'page' ) {	
		}else{
			continue;
		}
		// Has Linked Data?
		if($reused_data = get_post_meta($reused_post_id,'wpclink_referent_post_link', true)){
		
		// Site URL
		$site_post_url = add_query_arg( 'canonical_flag', '1', get_the_guid($reused_post_id));
			
		
		// cURL fetch content
		$match = wpclink_is_canonical_valid_linked_content($site_post_url,$site_url);
			
		
					
			// Not found
			if($match == false) update_post_meta($reused_post_id,'wpclink_canonical_violation',1);
			
			// Found
			if($match == true)	delete_post_meta($reused_post_id,'wpclink_canonical_violation');
		}
	
	}
}
/**
 * CLink Canonical Linked Content Voilation Warning
 * 
 */
function wpclink_do_canonical_warning_linked() {
	
	$class = 'notice notice-error is-dismissible maganit';
	
	
	$screen = get_current_screen();
			
			$all_violation = get_posts( array( 'meta_key' => 'wpclink_canonical_violation','posts_per_page' => -1 ) ); 
			$violations_count = count($all_violation);		
	
			foreach($all_violation as $single){
			if($post_canonical_violation = get_post_meta($single->ID,'wpclink_canonical_violation')){
				
				if(get_post_type( $single->ID ) == 'post'){
					
					$passed_time = wpclink_get_option('canonical_notification_date');
					
					if((wpclink_is_canonical_notification_time_passed() == true) || empty($passed_time)){
					
					$all_posts_url = admin_url( 'edit.php?s&post_status=all&post_type=post&action=-1&slug=0');
					$all_post_link = '<a href='.$all_posts_url.'>linked posts</a>';
					$all_post_link = '<a href='.$all_posts_url.'>linked posts</a>';
				
					
					$message = __( 'The canonical URL of a '.$all_post_link.' has been modified and the License has been violated.<br /> Licensor has the right to revoke the license and blacklist your site from obtaining future content. <form action="" method="post" class="noform"><input type="hidden" name="wpclink_reminder_later" value="1" /><input type="submit" name="submit" value="Remind me later" class="button nobutton"></form> ', 'cl_text' );
					printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ),  $message ); 
						
					}
					
					break;
					
				}
				
				}
			}
	
}
// Register canonical voilation warnining on WordPress Admin notices
add_action( 'admin_notices', 'wpclink_do_canonical_warning_linked' );
/**
 * CLink Canonical Dismiss Time
 * 
 */
function wpclink_is_canonical_notification_time_passed(){
	
				if($passed_time = wpclink_get_option('canonical_notification_date')){
					
				if(!empty($passed_time)){
				
					// Current Time
					$current_time_gmt = gmdate('Y-m-d h:i:s');
					$current_time = new DateTime($current_time_gmt);
					// Passed Object
					$passed_time = new DateTime($passed_time);
					$interval = $current_time->diff($passed_time);
					// All Difference
					$diff = $interval->format("%Y %m %d %h %i %s");
					// Date
					$diff_years = $interval->format("%Y");
					$diff_months = $interval->format("%m");
					$diff_days = $interval->format("%d");
					// Time
					$diff_hours = $interval->format("%h");
					$diff_minutes = $interval->format("%i");
					$diff_secounds = $interval->format("%s");
					$has_time = true;
					if($diff_years < 1){
						if($diff_months < 1){
							if($diff_days < 1){
								// Less Then 24 Hours
								if($diff_hours < 24){
									
										$has_time = false;
									
								}
							 }
						}
					}
					
					
					return $has_time;
					
					
					}else{
						return false;
					}
				}else{
					return false;
				}
}
/**
 * CLink Dimiss Canonial Notification
 * 
 */
function wpclink_dismiss_notification_update(){
	
	if(isset($_POST['wpclink_reminder_later']) and $_POST['wpclink_reminder_later'] == 1){
		
			$current_time_gmt = gmdate('Y-m-d h:i:s');
			wpclink_update_option('canonical_notification_date',$current_time_gmt);
		
	}
}
add_action('admin_init','wpclink_dismiss_notification_update');
/**
 * CLink Linked Content Voilated Show In WordPress Post List
 * 
 */
function wpclink_post_list_filter_class(){
    global $typenow; // current post type
	global $post;
    add_filter( 'post_class', function( $classes, $class, $postID ) {
		
		if($post_canonical_violation = get_post_meta($postID,'wpclink_canonical_violation')){
        	$classes[] = "violated";
		}
        return $classes;
    },10,3);
}
// Register Voilated Canonical Function on Edit Post 
add_action( 'load-edit.php','wpclink_post_list_filter_class');
/**
 * CLink Filter Voilated Post
 * 
 */
function wpclink_filter_violated_posts() {
  global $typenow;
  global $wp_query;
    if ( $typenow == 'post' || $typenow == 'page' ) { // Your custom post type slug
      $plugins = array( 'Violated' ); // Options for the filter select field
      $current_plugin = 'all';
      if( isset( $_GET['slug'] ) ) {
        $current_plugin = $_GET['slug']; // Check if option has been selected
      } ?>
      <select name="slug" id="slug">
        <option value="all" <?php selected( 'all', $current_plugin ); ?>><?php _e( 'None', 'wisdom-plugin' ); ?></option>
        <?php foreach( $plugins as $key=>$value ) { ?>
          <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $current_plugin ); ?>><?php echo esc_attr( $value ); ?></option>
        <?php } ?>
      </select>
  <?php }
}
// Register Filter Voilated Post on WordPress Post List
add_action( 'restrict_manage_posts', 'wpclink_filter_violated_posts' );
add_action( 'restrict_manage_pages', 'wpclink_filter_violated_pages' );
/**
 * Clink Filter Voilated Post Query
 * 
 * @param string $query default query
 * 
 */
function wpclink_filter_violated_posts_slug( $query ) {
  global $pagenow;
  // Get the post type
  $post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
  if ( is_admin() && $pagenow=='edit.php' && ($post_type == 'post' || $post_type == 'page')  && isset( $_GET['slug'] ) && $_GET['slug'] !='all' ) {
    $query->query_vars['meta_key'] = 'wpclink_canonical_violation';
    $query->query_vars['meta_value'] = 1;
    $query->query_vars['meta_compare'] = '=';
  }
}
add_filter( 'parse_query', 'wpclink_filter_violated_posts_slug' );
