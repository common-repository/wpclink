<?php
/**
 * CLink XML Functions
 *
 * CLink XML creation functions
 *
 * @package CLink
 * @subpackage Content Manager
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Get Client IP
 * 
 * @return string
 */
function wpclink_get_client_ip() {
    $ipaddress = '';
	
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = '';
 
	$ip_in_array = explode(',',$ipaddress);
	
    return $ip_in_array[0];
}
/**
 * CLink License Agreement Template Generate
 * 
 * @param integer $content_id  content id
 * 
 * @return string license template
 */
function wpclink_prepare_license_offer($content_id = 0, $current_date = true){
	
	// Info
	if($current_date){
		$time = current_time('H:i');
		$date = current_time('M d, Y');
	}else{
		$time = '[license_time]';
		$date = '[license_date]';
	}
	
	$site  = get_bloginfo('url').'/';
	
	// Party
	$party_id = wpclink_get_option('authorized_contact');
	$party_info = get_userdata($party_id);
	$party_display_name = $party_info->display_name;
	$party_first_name = $party_info->first_name;
	$party_last_name = $party_info->last_name;
	
	// Creator
	$creator_array = wpclink_get_option('authorized_creators');
	$creator_id =  get_post_meta($content_id,'wpclink_rights_holder_user_id',true);
	$creator_info = get_userdata($creator_id);
	$creator_display_name = $creator_info->display_name;
	$creatot_first_name = $creator_info->first_name;
	$creator_last_name = $creator_info->last_name;
	$creator_email = $creator_info->user_email;
	
	// Copyright Owner 
	$right_holder = wpclink_get_option('rights_holder');
	if($right_holder == 'party'){
		$rights_holder_firstname = $party_info->first_name;
		$rights_holder_lastname = $party_info->last_name;
		$right_holder_display = $party_info->display_name;
		$right_holder_email = $party_info->user_email;
		
		$copyright_identifier = get_user_meta($party_id,'wpclink_party_ID',true);
		
	}else if($right_holder == 'creator'){
		$rights_holder_firstname = $creator_info->first_name;
		$rights_holder_lastname = $creator_info->last_name;
		$right_holder_display = $creator_info->display_name;
		$right_holder_email = $creator_info->user_email;
		
		$copyright_identifier = get_user_meta($creator_id,'wpclink_party_ID',true);
	}
	
	
	$post_url = get_permalink($content_id);
	$post_creation_identifier = get_post_meta($content_id,'wpclink_creation_ID',true);
	$creator_identifier = get_user_meta($creator_id,'wpclink_party_ID',true);
	
	
	if($taxonomy_permission = get_post_meta($content_id,'wpclink_programmatic_right_categories',true)){
		
		if(get_post_type($content_id) == 'attachment'){
			
			$taxonomy_permission_array = explode(",",$taxonomy_permission);
			
		if(in_array('ModifyHeadline',$taxonomy_permission_array)){
			
			$find_taxo = '[right-type-ModifyHeadline]';
			$replace_taxo = '<img src="[path]images/checked.png" /> Modify Headline';
		}else{
			$find_taxo = '[right-type-ModifyHeadline]';
			$replace_taxo = '<img src="[path]images/unchecked.png" /> Modify Headline';
			
		}
			
		if(in_array('ModifyDescription',$taxonomy_permission_array)){
			
			$find_taxo2 = '[right-type-ModifyDescription]';
			$replace_taxo2 = '<img src="[path]images/checked.png" /> Modify Description';
		}else{
			$find_taxo2 = '[right-type-ModifyDescription]';
			$replace_taxo2 = '<img src="[path]images/unchecked.png" /> Modify Description';
			
		}
			
			
		if(in_array('ModifyKeywords',$taxonomy_permission_array)){
			
			$find_taxo3 = '[right-type-ModifyKeywords]';
			$replace_taxo3 = '<img src="[path]images/checked.png" /> Modify Keywords';
		}else{
			$find_taxo3 = '[right-type-ModifyKeywords]';
			$replace_taxo3 = '<img src="[path]images/unchecked.png" /> Modify Keywords';
			
		}
	
			
		}else{
		
		if($taxonomy_permission == 'AddToTaxonomy'){
			
			$find_taxo = '[taxonomy-AddToTaxonomy]';
			$replace_taxo = '<img src="[path]images/checked.png" /> Add to Taxonomy';
			$find_taxo2 = '[taxonomy-ModifyTaxonomy]';
			$replace_taxo2 = '<img src="[path]images/unchecked.png" /> Modify Taxonomy';
			$find_taxo3 = '[taxonomy-uneditable]';
			$replace_taxo3 = '<img src="[path]images/unchecked.png" /> NA';
			
		
		}else if($taxonomy_permission == 'ModifyTaxonomy'){
			
			$find_taxo = '[taxonomy-ModifyTaxonomy]';
			$replace_taxo = '<img src="[path]images/checked.png" /> Modify Taxonomy';
			$find_taxo2 = '[taxonomy-AddToTaxonomy]';
			$replace_taxo2 = '<img src="[path]images/unchecked.png" /> Add to Taxonomy';
			$find_taxo3 = '[taxonomy-uneditable]';
			$replace_taxo3 = '<img src="[path]images/unchecked.png" /> NA';
		
		}else if($taxonomy_permission == 'non-editable'){
			
			$find_taxo = '[taxonomy-uneditable]';
			$replace_taxo = '<img src="[path]images/checked.png" /> NA';
			$find_taxo2 = '[taxonomy-AddToTaxonomy]';
			$replace_taxo2 = '<img src="[path]images/unchecked.png" /> Add to Taxonomy';
			$find_taxo3 = '[taxonomy-ModifyTaxonomy]';
			$replace_taxo3 = '<img src="[path]images/unchecked.png" /> Modify Taxonomy';
		}
			
		}
		
	}else{
			$find_taxo = '';
			$replace_taxo = '';
	}
	
	$tick = '[tick]';
	$replace_tick = '<img src="[path]images/checked.png" />';
	
	$search = array('[current_date]',
					'[current_time]',
					'[my_site]',
					'[party_display]',
					'[party_firstname]',
					'[party_lastname]',
					'[creator_display_name]',
					'[creator_firstname]',
					'[creator_lastname]',
					'[rights_holder_firstname]',
					'[rights_holder_lastname]',
					'[right_holder_display_name]',
					'[license_date]',
					'[creation_url]',
					'[creation_ID]',
					'[display_name]',
					'[creator_ID]',
					'[post_rights_holder]',
					'[rights_holder_user_email]',
					'[creator_email]',
					$find_taxo,
					$find_taxo2,
					$find_taxo3,
					$tick,
				    '[text-ModifyTaxonomy]',
					'[text-uneditable]',
					'[media-ModifyTaxonomy]',
					'[media-uneditable]'
				   );
	
	
	$replace = array($date,
					 $time,
					 $site,
					 $party_display_name,
					 $party_first_name,
					 $party_last_name,
					 $creator_display_name,
					 $creatot_first_name,
					 $creator_last_name,
					 $rights_holder_firstname,
					 $rights_holder_lastname,
					 $right_holder_display,
					 $date,
					 $post_url,
					 wpclink_do_icon($post_creation_identifier),
					 $right_holder_display,
					 wpclink_do_icon($creator_identifier),
					 $right_holder_display,
					 $right_holder_email,
					 $creator_email,
					 $replace_taxo,
					 $replace_taxo2,
					 $replace_taxo3,
					 $replace_tick,
					'<img src="[path]images/unchecked.png" /> Modify Taxonomy', '<img src="[path]images/checked.png" /> NA','<img src="[path]images/unchecked.png" /> Modify Taxonomy','<img src="[path]images/checked.png" /> NA');
	
	// License By Post
	if($content_id > 0){
		if($license_class = get_post_meta($content_id,'wpclink_creation_license_class',true)){
			if($license_class == 'personal' || $license_class == 'business' || $license_class == 'marketplace'){
				$clink_licenses = wpclink_get_license_template($content_id, false, $license_class);
				$agreement_template = $clink_licenses;
			}
		}
	}
	
	$agreement_template_final = str_replace($search,$replace,$agreement_template);
	
	return $agreement_template_final;
}
/**
 * CLink Generate Content XML
 * 
 * CLink generate the content for linked sites
 * Content will generate from following security checks:
 *
 * 1. Content is in referent list.
 * 2. Content request site has token and link establish without canonical voilation.
 * 3. Content request pass key should be match the plugin activation pass key.
 * 4. Content request ip should be match to ip recorded when link is created.
 * 5. Content request should be only allowed query variabes listed in code.
 *
 */
function wpclink_do_api_content_xml(){
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
// Random Pass Key
$pass_key = wpclink_get_option('DYNAMIC_URL_POSTFIX_PASS');
// Get Option
$options = wpclink_get_option( 'preferences_general' );
// GET URL RESET
$get_wpsync = $_GET;
$get_pass_key = $_GET['pass_key'];
if($get_pass_key == $pass_key){
	
	// Prepare WP Variables
	$date = date('Y-m-d H:i:s');
	$curr_date_time = strtotime($date);
	$site_url = network_site_url( '/' );
	
}else{
	die('0');
}
	
	
?><?php echo'<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>
<channel>
	<title><?php bloginfo('name'); ?></title>
    <link><?php bloginfo('url'); ?></link>
    <description><?php bloginfo('description'); ?></description>
    <pubDate><?php echo $curr_date_time; ?></pubDate>
    <language><?php bloginfo('language'); ?></language>
    <wp:wxr_version>1.2</wp:wxr_version>
    <wp:base_site_url><?php echo $site_url; ?></wp:base_site_url>
    <wp:base_blog_url><?php echo site_url(); ?></wp:base_blog_url>
    <blog_local_time><?php echo date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 )); ?></blog_local_time>
    <generator>https://wordpress.org/?v=4.6.1</generator>
    <cl_status><?php echo 'active'; ?></cl_status>
    <?php 
// IP Verification
$host_ip_response = wpclink_host_ip_match_to_link_ip(urldecode($_GET['referent_site_address']));
if($host_ip_response == 'not_match'){
	echo '<ip_site_warning>1</ip_site_warning>';
}else{
	echo '<ip_site_warning>0</ip_site_warning>';
}
	
	
// WP_Query arguments
$args = array();
if(isset($get_wpsync['posts_per_page'])){
	$args['posts_per_page'] = $get_wpsync['posts_per_page'];
}
if(isset($get_wpsync['order'])){
	$args['order'] = $get_wpsync['order'];
}
if(isset($get_wpsync['orderby'])){
	$args['orderby'] = $get_wpsync['orderby'];
}
if(isset($get_wpsync['nopaging'])){
	$args['nopaging'] = $get_wpsync['nopaging'];
}
if(isset($get_wpsync['post_type'])){	
	// Remove c- 
	$get_wpsync['post_type'] = str_replace("c-","",$get_wpsync['post_type']);
	
	$my_post_types = explode(",",$get_wpsync['post_type']);
	$args['post_type'] =  $my_post_types;
	$post_type = str_replace("c-","",$get_wpsync['post_type']);
}
	
if(isset($get_wpsync['post__in'])){
	$my_not_posts_show = explode(",",$get_wpsync['post__in']);
	$args['post__in'] = wpclink_get_all_posts($my_not_posts_show,$post_type,urldecode($get_wpsync['referent_site_address']));
	
}else{
		$args['post__in'] = wpclink_posts_show($post_type,urldecode($get_wpsync['referent_site_address']));
}
// Not for attachments
if($post_type != 'attachment'){
	if(isset($get_wpsync['search'])){
		$args['s'] = $get_wpsync['search'];
	}
	if(isset($get_wpsync['author_id'])){
		$args['author'] = $get_wpsync['author_id'];
	}
	if(isset($get_wpsync['author_name'])){
		$args['author_name'] = $get_wpsync['author_name'];
	}
	if(isset($get_wpsync['tag'])){
		$args['tag'] = $get_wpsync['tag'];
	}
	if(isset($get_wpsync['category_name'])){
		$args['category_name'] = $get_wpsync['category_name'];
	}
}
// Only for attachments
if($post_type == 'attachment'){
	$all_mimes = get_allowed_mime_types();
	$args['post_mime_type'] = $all_mimes;
	$args['post_status'] = 'inherit';
	
}
$args['ignore_sticky_posts'] = true;
$args['meta_key'] = 'wpclink_creation_ID';
$args['meta_value'] = ' ';
$args['meta_compare'] = '!=';
$paged = ( isset($get_wpsync['paged']) ) ? $get_wpsync['paged'] : 1;
if(isset($get_wpsync['paged'])){
	$paged = ( $get_wpsync['paged'] ) ? $get_wpsync['paged'] : 1;
	$args['paged'] = $get_wpsync['paged'];
	$paged = $args['paged'];
}
$content_show = false;
if((isset($get_wpsync['get_type'])) and ($get_wpsync['get_type'] == 'content')){
	$content_show = true;
}
	
if(isset($get_wpsync['linked_creation_ID']) and !empty($get_wpsync['linked_creation_ID'])){
	$linked_creation_ID = $get_wpsync['linked_creation_ID'];
	
	$ref_site_address = urldecode($get_wpsync['referent_site_address']);
	$ref_publish_date = urldecode($get_wpsync['referent_publish_date']);
	$linked_post_id = urldecode($get_wpsync['linked_post_id']);
	
	$ref_publish_date = date_i18n( 'Y-m-d H:i:s', strtotime( $ref_publish_date ) ); 
}
	
// The Query
$export_query = new WP_Query( $args );
	
	
wpclink_debug_log(print_r($export_query,true));
	
// Total Number of Posts
echo '<item_count>'.$export_query->found_posts.'</item_count>';
	
if ( $export_query->have_posts() ) {
	
	
	// The Loop
	while ( $export_query->have_posts() ) {
	$export_query->the_post();
	$post_id = get_the_ID();
		
		
		
	// Update Linked Creation Identifier
	if(isset($get_wpsync['save_creation_ID']) and $get_wpsync['save_creation_ID'] == 1){
			
	$license_data = wpclink_get_license_by_linked_post_id_and_site_url($post_id,$ref_site_address);
			
	if($license_data['license_id'] > 0){
		
		
		 // Global
		global $wpdb;
		// Table Prefix
		$clink_license_table = $wpdb->prefix . 'wpclink_licenses';
		
		if($linked_pid = wpclink_get_license_meta($license_data['license_id'],'linked_post_id',true)){
			wpclink_update_license_meta($license_data['license_id'],'linked_post_id',$linked_post_id);
		}else{
			
			wpclink_add_license_meta($license_data['license_id'],'linked_post_id',$linked_post_id);
		}
	
					
		if($get_linked_creation = wpclink_get_license_meta($license_data['license_id'],'linked_creation_ID',true)){
			// Linked Creation ID			
			wpclink_update_license_meta($license_data['license_id'],'linked_creation_ID',$linked_creation_ID);
		}else{
			// Linked Creation ID			
			wpclink_add_license_meta($license_data['license_id'],'linked_creation_ID',$linked_creation_ID);
		}
		if($get_linked_creation_date = wpclink_get_license_meta($license_data['license_id'],'linked_creation_date',true)){
			// Linked Publish Date			
			wpclink_update_license_meta($license_data['license_id'],'linked_creation_date',$ref_publish_date);
		}else{
			// Linked Publish Date
			wpclink_add_license_meta($license_data['license_id'],'linked_creation_date',$ref_publish_date);
		}
	}
		// Update Links
		
		
		break;
	}		
		
	$post_warning = wpclink_verify_site_IP_by_post_id($post_id);
		
	// Update
	wpclink_verify_linked_site_ip($post_id,$get_wpsync['referent_site_address']);
	
	
	
	// NO LOOP
	$not_post_loop = false;
	if(isset($get_wpsync['post__in'])){
		if($post_warning){
			$not_post_loop = true;
			$content_show = false;
		}
	}
	
	if($not_post_loop){
		/// NO LOOP 
	}else{
		if($reuse_content = get_post_meta($post_id,'wpclink_link_flag',true)){
			// NO LOOP 
		}else{
			
		$author_id = get_the_author_meta('ID');
		$post_type = get_post_type($post_id);
			/* ====== Creation Right Holder ========= */
	$creation_right_holder_user_id = get_post_meta($post_id,'wpclink_rights_holder_user_id',true);
	$creation_right_holder_data = get_userdata($creation_right_holder_user_id);
	$creation_right_holder_display_name = $creation_right_holder_data->display_name;
	$creation_right_holder_identifier = get_user_meta($creation_right_holder_user_id,'wpclink_party_ID',true);
	/* ====== Creator Display Name ========== */
	$creator_user_id =  get_userdata($author_id);
	$creator_user_data = get_userdata($author_id);
	$creator_user_display = $creator_user_data->display_name;
	$creatorID = get_user_meta($author_id,'wpclink_party_ID',true);
	
	
	?>
		<item>
		  <title><?php echo get_the_title(); ?></title>
		  <link><?php echo get_permalink(); ?></link>
		  <pubDate><?php echo get_the_date('Y/m/d'); ?></pubDate>
		  <dc:creator_id><?php echo $author_id; ?></dc:creator_id>
		  <dc:creator><![CDATA[<?php echo the_author_meta('user_login'); ?>]]></dc:creator>
		  <guid isPermaLink="false"><?php 
			if(get_post_type($post_id) == 'attachment'){ 
				echo wpclink_get_image_URL($post_id);
			}else{
				echo get_the_guid($post_id);
			} ?></guid>
		  <description><?php echo wp_get_attachment_caption($post_id); ?></description>
<licensor_url><?php if(get_post_type($post_id) == 'attachment'){ 
$wpclink_license_selected_type = get_post_meta($post_id,'wpclink_license_selected_type',true);
if($wpclink_license_selected_type == 'wpclink_personal' || $wpclink_license_selected_type == 'wpclink_business' || $wpclink_license_selected_type == 'wpclink_marketplace' ){
	echo esc_html(get_bloginfo('url').'?clink_media_license&id='.$post_id);
}else{
	$custom_url = get_post_meta($post_id,'wpclink_custom_url',true);
	echo esc_html($custom_url);
}
 } ?></licensor_url>
		  <?php 
			if($post_warning){
				// Has warning
			}else{
			if($content_show){
				// Update Status Content Delivered
			wpclink_update_license_delivery_status($post_id,$get_wpsync['referent_site_address']); ?>
			<?php if(get_post_type($post_id) != 'attachment'){ ?>
		    <content:encoded><![CDATA[<?php 
			$post_content = get_the_content();
			// Clean for linked site
			$post_content = preg_replace('/"id":(.*?),/', "", $post_content);
			// Clean attachment class
			$post_content=preg_replace('/wp-image-[0-9]+/', '', $post_content);
			echo $post_content; ?>]]></content:encoded>
			<?php } ?>
		  <excerpt:encoded><![CDATA[<?php echo get_the_excerpt(); ?>]]></excerpt:encoded>
		  <?php }
			} ?>
			
		  <?php if(get_post_type($post_id) != 'attachment'){
					if(has_post_thumbnail()){ ?>
		  <wp:post_thumbnail><post_thumbnail_url><?php the_post_thumbnail_url(); ?></post_thumbnail_url></wp:post_thumbnail>
		  <?php 	}
				} ?>
		   <?php if(get_post_type($post_id) != 'attachment'){
		if($post_attached_data = get_post_meta( $post_id, 'wpclink_media_attributes', true )){ ?><post_attached_data><?php echo json_encode($post_attached_data); ?></post_attached_data><?php } ?>
		  <?php }?>
		  <wp:post_id><?php echo $post_id; ?></wp:post_id>
		  <post_id><?php echo $post_id; ?></post_id>
		  <post_ip_warning><?php if($post_warning){
			echo '1';
			}else{
			echo '0';
		} ?></post_ip_warning>
		<language><?php echo wpclink_get_current_site_lang(); ?></language>
		  <post_license_class><?php 
		  if($get_license_class = get_post_meta($post_id, 'wpclink_creation_license_class', true)){
			  echo $get_license_class;
		  }else{
			  echo wpclink_get_option( 'wpclink_license_class' ); 
		  } ?></post_license_class>
		<post_taxonomy_permission><?php  
			if($taxonomy_permission = get_post_meta($post_id, 'wpclink_programmatic_right_categories', true)){
				echo $taxonomy_permission;
			} ?></post_taxonomy_permission>
		  <post_creator_clink_id><?php 
		  echo get_user_meta($author_id,'wpclink_party_ID',true);
		  ?></post_creator_clink_id>
		  <post_creator><?php 
		  $user_info = get_userdata($author_id);
		  echo $user_info->display_name;
		  ?></post_creator>
		  <post_rights_holder><?php 
		  $right_holder = wpclink_get_option('rights_holder');
			
			$right_holder_id = $author_id;
		$right_holder_user_id = $right_holder_id;
		$right_holder_user_id = get_user_meta($right_holder_user_id,'wpclink_party_ID',true);
		echo $right_holder_user_id;
		  ?></post_rights_holder>
	<referent_creator_party_ID><?php echo $creatorID; ?></referent_creator_party_ID>
	<referent_creator_display_name><?php echo $creator_user_display; ?></referent_creator_display_name>
	<referent_rights_holder_party_ID><?php echo $creation_right_holder_identifier; ?></referent_rights_holder_party_ID>
	<referent_creation_rights_holder_party_ID><?php echo $creation_right_holder_identifier; ?></referent_creation_rights_holder_party_ID>
	<referent_rights_holder_display_name><?php echo $creation_right_holder_display_name; ?></referent_rights_holder_display_name>
		  <post_license_version><?php echo get_post_meta($post_id, 'wpclink_post_license_version', true); ?></post_license_version>
		  <?php if($post_type == 'page'){ ?><wp:post_parent><?php echo wp_get_post_parent_id( $post_id ); ?></wp:post_parent><?php } ?>
		  <wp:post_type><![CDATA[<?php echo get_post_type($post_id); ?>]]></wp:post_type>
		  <wp:status><![CDATA[<?php echo get_post_status($post_id); ?>]]></wp:status>
		  <created_time><?php echo get_the_date( 'Y/m/d' ).' '.get_the_time('H:i:s'); ?></created_time>
		  <modified_time><?php echo get_the_modified_date( 'Y-m-d H:i:s' ); ?></modified_time>
		  <?php if($content_id = get_post_meta( $post_id, 'wpclink_creation_ID', true )){ ?>
		  <content_id><?php echo $content_id; ?></content_id>
		  <?php } ?>
		  <?php 
	if(get_post_type($post_id) != 'attachment'){
		  if(get_post_type($post_id) == 'post'){
		  $post_categories = wp_get_post_categories( $post_id );
		  $cats = array();
			foreach($post_categories as $c){
				$cat = get_category( $c );
				?>
				<category domain="category" nicename="<?php echo $cat->slug; ?>"><![CDATA[<?php echo $cat->name; ?>]]></category>
				<?php
			}
			$t = wp_get_post_tags($post_id);
			$tags = $t;
			foreach($tags as $tag){ 
			?>
			<category domain="post_tag" nicename="<?php echo $tag->slug;?>"><![CDATA[<?php echo $tag->name;?>]]></category>
			<?php	
			}
		  }
	} 
			if(get_post_type($post_id) == 'attachment'){
				if($content_show){
				
			// IPTC Fields and Data
			$iptc_metadata = array(
				'metadata_iptc_headline' => 'IPTC:Headline',
				'metadata_iptc_creator' => 'IPTC:By-line',
				'metadata_iptc_description' => 'IPTC:Caption-Abstract',
				'metadata_iptc_keywords' => 'IPTC:Keywords',
				'metadata_iptc_title' => 'IPTC:ObjectName',
				'metadata_iptc_credit' => 'IPTC:Credit',
				'metadata_iptc_copyright_notice' => 'IPTC:CopyrightNotice',
				
				'metadata_copyrightownerid' => 'XMP-plus:CopyrightOwnerID',
				'metadata_copyrightownername' => 'XMP-plus:CopyrightOwnerName',
				'metadata_copyrightownerimageid' => 'XMP-plus:CopyrightOwnerImageID',
				'metadata_imagecreatorid' => 'XMP-plus:ImageCreatorID',
				'metadata_imagecreatorname' => 'XMP-plus:ImageCreatorName',
				'metadata_imagecreatorimageid' => 'XMP-plus:ImageCreatorImageID',
				'metadata_licensoremail' => 'XMP-plus:LicensorEmail',
				'metadata_licensorid' => 'XMP-plus:LicensorID',
				'metadata_licensorname' => 'XMP-plus:LicensorName',
				'metadata_licensorurl' => 'XMP-plus:LicensorURL',
				'metadata_licensorimageid' => 'XMP-plus:LicensorImageID',
				'metadata_termsandconditionsurl' => 'XMP-plus:TermsAndConditionsURL',
				'metadata_plusversion' => 'XMP-plus:PLUSVersion',
				'metadata_xmp-xmprights_webstatement' => 'XMP-xmpRights:WebStatement',
				'metadata_copyrightstatus' => 'XMP-plus:CopyrightStatus',
				'metadata_photoshop_source' => 'XMP-photoshop:Source'
 			);
			
			$attachment_path = wpclink_iptc_image_path( $post_id, 'full' );
				
			$image_meta_fields = array('IPTC:Headline','IPTC:By-line','IPTC:Caption-Abstract','IPTC:Keywords','IPTC:ObjectName','IPTC:Credit','IPTC:CopyrightNotice','XMP-plus:CopyrightOwnerID','XMP-plus:CopyrightOwnerName','XMP-plus:CopyrightOwnerImageID','XMP-plus:ImageCreatorID','XMP-plus:ImageCreatorName','XMP-plus:ImageCreatorImageID','XMP-plus:LicensorEmail','XMP-plus:LicensorID','XMP-plus:LicensorName','XMP-plus:LicensorURL','XMP-plus:LicensorImageID','XMP-plus:TermsAndConditionsURL','XMP-plus:PLUSVersion','XMP-xmpRights:WebStatement','XMP-plus:CopyrightStatus','XMP-photoshop:Source');
				
			$iptc_tag_value = wpclink_get_image_metadata_value($attachment_path,$image_meta_fields);
			
						
			foreach($iptc_metadata as $key => $iptc_xml_tag){
				
				if($key == "metadata_licensorurl"){
					$value = urlencode($iptc_tag_value[$iptc_xml_tag]);
				}else{
					$value = $iptc_tag_value[$iptc_xml_tag];
				}
				
				echo '<'.$key.'>'.$value.'</'.$key.'>';
				
			}
				
				$time_data = wpclink_get_image_datetime($post_id);
				$time_of_creation = $time_data['created'];
				echo '<time_of_creation>'.$time_of_creation.'</time_of_creation>';
					
				echo '<time_of_right>123</time_of_right>';
	  
				}
			}else{
				if($content_show){
					
					$get_post_date  = get_the_date('Y-m-d',$post_id);
					$get_post_date .= 'T';
					$get_post_date .= get_the_date('G:i:s',$post_id);
					$get_post_date .= 'Z';
					
					echo '<time_of_right>123</time_of_right>';
					echo '<time_of_creation>'.$get_post_date.'</time_of_creation>';
				}
			}
			
		  ?></item>
	<?php }
		
	}
?>
<wp:pagination>
<?php
if (($export_query->max_num_pages > 1) and ($paged < $export_query->max_num_pages)) { ?>
<pg_max><?php echo $export_query->max_num_pages; ?></pg_max>
<pg_next><?php echo $paged+1; ?></pg_next>
<pg_prev><?php  if($paged > 1){ echo $paged-1; } ?></pg_prev>
<?php } ?>
<pg_current><?php echo $paged; ?></pg_current>
<?php if($paged > 1){ ?><pg_prev><?php echo $paged-1; ?></pg_prev><?php } ?>
</wp:pagination>
<?php wp_reset_postdata();
}
} ?></channel></rss>
<?php }
/**
 * CLink Start Site Agreements
 * 
 * Use for create the site agreemnet link
 * 
 */
function wpclink_do_api_license_xml(){
// XML CLEANUP
error_reporting(0);
header('Content-Type: application/xml; charset=utf-8');
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
// Prepare WP Variables
	$date = date();
	$curr_date_time = strtotime($date);
	$site_url = network_site_url( '/' );
if(($_GET['cl_action'] == 'connect') and !empty($_GET['client_site'])){
	
global $wpdb;
// Table Prefix
$table_name_token = $wpdb->prefix . 'wpclink_tokens'; 
$request_agree = $_GET;
$token = $request_agree['token'];
$record_found = $wpdb->get_row( 'SELECT * FROM '.$table_name_token.' WHERE token = "'.$token.'" AND token_expiration <> 1' );
if($record_found){
	
	// Action Begins
	$site_url =	$_GET['client_site'];
	$client_site = urldecode($site_url);
	
	// GET IP
	$url = parse_url($client_site);
	$host_url = $url['host'];
	
	// PICK IP
	//$ip = gethostbyname($host_url);
	$ip = wpclink_get_client_ip();
	
	global $wpdb;
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses'; 
	
	// Status
	$agreement_status = 'pass';
	
	// Linked Info
	$link_firstname = urldecode($request_agree['link_first']);
	$link_lastname = urldecode($request_agree['link_last']);
	$link_display = urldecode($request_agree['link_display']);
	$link_email = urldecode($request_agree['link_email']);
	$link_identifier = urldecode($request_agree['link_identifier']);
	$link_unique_id = urldecode($request_agree['cl_unique_id']);
	
	// Esign
	$esign_by = urldecode($request_agree['esign_by']);
	$esign_reason = urldecode($request_agree['esign_reason']);
	$esign_time = urldecode($request_agree['esign_time']);
	$esign_email = urldecode($request_agree['esign_email']);
	$esign_copyright_identifier = urldecode($request_agree['esign_copyright_identifier']);
	$esign_sign_date = date("Y-m-d H:i:s");
	
	$esign_time_formated = date("Y-m-d H:i:s",$esign_time);
	
	// Time Difference
	$time_difference = urldecode($request_agree['time_diff']);
	$time_difference_formate = date("H:i:s",$time_difference);
	
	
	$esign_html_stamp = wpclink_print_esign_referent($esign_by,$esign_reason,$esign_time_formated,$esign_copyright_identifier);
	
	
	// Array
	$extra_link = array('link_email' => $link_email, 'link_identifier' => $link_identifier,'licensee_accept_datetime' => $time_difference_formate);
	
	// Content ID by Token
	$content_id = $record_found->post_id;
	
	// Content URL by Token
	$content_url = $record_found->creation_uri;
	
	$ip_match = false;
	
	$ip_match = $wpdb->get_results( "SELECT * FROM $clink_sites_table WHERE mode = 'referent' ", ARRAY_A );
	
	
	$return_data = array();
	
	foreach($ip_match as $license_single){
		
		$content_id_match = $license_single['post_id'];
		
		if($content_id == $content_id_match){
			
			$ip_match[] = array(
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
	
	
	
	
	// IP Verification
	if($ip_match){
		
			
	
	
	// RSS
		echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?><rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
><channel>
    <site_address><?php echo bloginfo('url').'/'; ?></site_address>
    <secret_key><?php echo wpclink_get_option('DYNAMIC_URL_POSTFIX_SECRET_KEY'); ?></secret_key>
    <auth_code><?php echo wpclink_get_option('DYNAMIC_URL_POSTFIX_DATA_KEY'); ?></auth_code> 
    <license_class><?php
	
	if($license_class = get_post_meta($content_id,'wpclink_creation_license_class',true)){
		if($license_class == '0'){
			echo wpclink_get_option('wpclink_license_class');
		}else{
			echo $license_class;
		}
	}else{
		echo wpclink_get_option('wpclink_license_class');
	}
	
	// CREATOR
	$creator_array = wpclink_get_option('authorized_creators');		
	$author_id = get_post_field ('post_author', $content_id);
	$creator_info = get_userdata($author_id);
	
	// PARTY
	$party_id = wpclink_get_option('authorized_contact');
	$party_info = get_userdata($party_id);
	
	?></license_class>
	<post_taxonomy_permission><?php  
		if($taxonomy_permission = get_post_meta($content_id, 'wpclink_programmatic_right_categories', true)){
			echo $taxonomy_permission;
		} ?></post_taxonomy_permission>
	<request_content_type><?php echo get_post_type($content_id); ?></request_content_type>
    <request_content_id><?php echo $content_id; ?></request_content_id>
    <request_content_url ver="2.0"><?php  echo esc_html($content_url); ?></request_content_url>
    <party_name><?php echo $party_info->display_name; ?></party_name>
    <party_email><?php echo $party_info->user_email; ?></party_email>
    <creator_name><?php echo $creator_info->display_name; ?></creator_name>
    <creator_email><?php echo $creator_info->user_email; ?></creator_email><?php 
		
	$author_id = get_post_field ('post_author', $content_id);
	$right_holder_id = $author_id;
		
	$author_id = get_post_field ('post_author', $content_id);
	$creator_id = $author_id;
	
	if($right_holder_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true)){
	}else if($right_holder_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true)){
	}
		
		$license_class = get_post_meta($content_id,'wpclink_creation_license_class',true);
        $license_type = get_post_meta( $content_id, 'wpclink_license_selected_type', true );
        
		$time = current_time('Y-m-d',true);
		$time .= 'T';
		$time .= current_time('G:i:s',true);
		$time .= 'Z';
		$date_and_time = $time;
		$license_creation_identifier = get_post_meta( $content_id, 'wpclink_creation_ID', true );
		
		$data = array(
			'Licensee_identifier' => $link_identifier, // Creator Identifier
			'licensor_identifier' => $right_holder_identifier, // Right Holder Identifier
			'license_creation_identifier' => $license_creation_identifier, // Right Identifier
			'license_time' => $date_and_time,
			'domain_access_key' => wpclink_get_option('domain_access_key'),
			'license_class' => $license_class,
            
            'license_type' => $license_type,
		);
		
	// Right Assignment
	$right_assign_ID = wpclink_create_right_transaction($data);
	
	// Promoted
	$license_promoted_id = wpclink_add_license_referent($site_url,$ip,$agreement_status,1,$link_firstname,$link_lastname,$link_display,$content_id,$content_url,'personal',$extra_link,$token,$right_assign_ID);
	
	// Electronic Signature by Licensor
	wpclink_add_esign($esign_by,$esign_reason,$esign_sign_date,$esign_time_formated, $token, $esign_email,$ip, $content_id,$license_promoted_id,$esign_html_stamp);		
		
	?><right_assignID><?php echo $right_assign_ID; ?></right_assignID></channel></rss><?php }else{
		
		
	
	
	
	
	// Time
	$esign_found = wpclink_get_esign_by_post_id($content_id);
	$esign_added_id = $esign_found['esign_id'];
	$view_time = date("Y-m-d H:i:s");
		
		
		echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?><rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>
	<channel>
    <site_address><?php echo bloginfo('url').'/'; ?></site_address>
    <secret_key><?php echo wpclink_get_option('DYNAMIC_URL_POSTFIX_SECRET_KEY'); ?></secret_key>
    <auth_code><?php echo wpclink_get_option('DYNAMIC_URL_POSTFIX_DATA_KEY'); ?></auth_code> 
    <license_class><?php
	
	if($license_class = get_post_meta($content_id,'wpclink_creation_license_class',true)){
		if($license_class == '0'){
			echo wpclink_get_option('wpclink_license_class');
		}else{
			echo $license_class;
		}
	}else{
		echo wpclink_get_option('wpclink_license_class');
	}
		
		
	
	// CREATOR
	$creator_array = wpclink_get_option('authorized_creators');
	$author_id = get_post_field ('post_author', $content_id);
	$creator_id = $author_id;
	$creator_info = get_userdata($creator_id);
	
	
	// PARTY
	$party_id = wpclink_get_option('authorized_contact');
	$party_info = get_userdata($party_id);
	
	?></license_class>
	<post_taxonomy_permission><?php  
		if($taxonomy_permission = get_post_meta($content_id, 'wpclink_programmatic_right_categories', true)){
			echo $taxonomy_permission;
		} ?></post_taxonomy_permission>
    <request_content_id><?php echo $content_id; ?></request_content_id>
	<request_content_type><?php echo get_post_type($content_id); ?></request_content_type>
    <request_content_url ver="1.0"><?php echo esc_html($content_url); ?></request_content_url>
    <party_name><?php echo $party_info->display_name; ?></party_name>
    <party_email><?php echo $party_info->user_email; ?></party_email>
    <creator_name><?php echo $creator_info->display_name; ?></creator_name>
    <creator_email><?php echo $creator_info->user_email; ?></creator_email>
    <?php 
		
			
	$right_holder = wpclink_get_option('rights_holder');
			
	$right_holder_id = get_post_field ('post_author', $content_id);
	$creator_id = $author_id;
	
	if($right_holder_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true)){
	}else if($right_holder_identifier = get_user_meta($right_holder_id,'wpclink_party_ID',true)){
	}
		
		$license_class = get_post_meta($content_id,'wpclink_creation_license_class',true);
		$time = current_time('Y-m-d',true);
		$time .= 'T';
		$time .= current_time('G:i:s',true);
		$time .= 'Z';
		$date_and_time = $time;
		$license_creation_identifier = get_post_meta( $content_id, 'wpclink_creation_ID', true );
		
		$domain_access_key = wpclink_get_option('domain_access_key');
        
        $license_type = get_post_meta( $content_id, 'wpclink_license_selected_type', true );
		
		$data = array(
			'Licensee_identifier' => $link_identifier, // Creator Identifier
			'licensor_identifier' => $right_holder_identifier, // Right Holder Identifier
			'license_creation_identifier' => $license_creation_identifier, // Right Identifier
			'license_time' => $date_and_time,
			'license_class' => $license_class,
            
            'license_type' =>  $license_type,
			'domain_access_key' => $domain_access_key,
		);
		
		// Right Assignment
		$right_assign_ID = wpclink_create_right_transaction($data);
		
		$license_promoted_id = wpclink_add_license_referent($client_site,$ip,$agreement_status,1,$link_firstname,$link_lastname,$link_display,$content_id,$content_url,'personal',$extra_link,$token,$right_assign_ID);
	
		// Electronic Signature by Licensor
		wpclink_add_esign($esign_by,$esign_reason,$esign_sign_date,$esign_time_formated,$token,$esign_email,$ip,$content_id,$license_promoted_id,$esign_html_stamp);
		
		echo '<right_assignID>'.$right_assign_ID.'</right_assignID></channel></rss>';
		
		} ?><?php }else{
		// INVALID
		echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
		<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>
	<channel>
    <response><?php echo 'invalid'; ?></response> 
    </channel>
</rss>
		<?php 
	}
	die();
	
}elseif(($_GET['cl_action'] == 'cl_list') and !empty($_GET['client_site'])){
	
	echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
		<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
><channel><?php 
	$site_url =	$_GET['client_site'];
	$client_site = urldecode($site_url);
	$content_id = $_GET['content_id'];
		
	// List of All Links
	wpclink_generate_linked_post_list($content_id,$client_site); ?></channel>
</rss>
	<?php 
}elseif(($_GET['cl_action'] == 'disconnect') and !empty($_GET['client_site'])){
	
	$site_url =	$_GET['client_site'];
	$client_site = urldecode($site_url);
	
	// GET IP
	$url = parse_url($client_site);
	$host_url = $url['host'];
	
	// PICK IP
	//$ip = gethostbyname($host_url);
	$ip = wpclink_get_client_ip();
	
	
	global $wpdb;
	$clink_sites_table = $wpdb->prefix . 'wpclink_licenses'; 
	
	// Need Single
	$ip_match = $wpdb->get_row( "SELECT * FROM $clink_sites_table WHERE site_url = '$client_site' AND mode = 'referent' " );
	
	if($ip_match){
	
		// Cencel
		$deleted_agr = $wpdb->delete( $clink_sites_table, array( 'site_url' => $client_site ) );
		
		
		echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
		<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>
	<channel>
    <cencel_agree>1</cencel_agree>
    </channel>
</rss>
		<?php 
		
	}
	
}elseif(isset($_GET['token'])){
	
global $wpdb;
// Table Prefix
$table_name_token = $wpdb->prefix . 'wpclink_tokens'; 
$request_agree = $_GET;
$token = $request_agree['token'];
$record_found = $wpdb->get_row( 'SELECT * FROM '.$table_name_token.' WHERE token = "'.$token.'" AND token_expiration <> 1' );
if($record_found){
// Content ID
$content_id = $record_found->post_id;
$author_id = get_post_field ('post_author', $content_id);
// Get User from Post Meta
$user_id = get_post_meta($content_id,'wpclink_rights_holder_user_id',true);
// Update the Deliver Time
$deliver_time = date("Y-m-d H:i:s");
// Update
$wpdb->update( 
	$table_name_token, 
	array( 
		'license_delivery_date' => $deliver_time,
	), 
	array( 'token' => $token ));
// Copyright Owner Display Name
$user_info = get_userdata($user_id);
$display_name = $user_info->display_name;
$email_address = $user_info->user_email;
$creator_array = wpclink_get_option('authorized_creators');
$creator_id = $author_id;
$party_id = wpclink_get_option('authorized_contact');
if($user_id == $creator_id){
	$identifier = get_user_meta($user_id,'wpclink_party_ID',true);
}elseif($user_id == $party_id){
	$identifier = get_user_meta($user_id,'wpclink_party_ID',true);
}else{
	
	$creator_identifier_exists = get_user_meta($user_id,'wpclink_party_ID',true);
	$party_identifier_exists = get_user_meta($user_id,'wpclink_party_ID',true);
	
	if(!empty($creator_identifier_exists)){
		$identifier = $creator_identifier_exists;
	}else if(!empty($party_identifier_exists)){
		$identifier = $party_identifier_exists;
	}
}
echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>
<channel>
<title><?php bloginfo('name'); ?></title>
<link><?php bloginfo('url'); ?></link>
<description><?php bloginfo('description'); ?></description>
<pubDate><?php echo $curr_date_time; ?></pubDate>
<language><?php bloginfo('language'); ?></language>
<wp:wxr_version>1.2</wp:wxr_version>
<wp:base_site_url><?php echo $site_url; ?></wp:base_site_url>
<wp:base_blog_url><?php echo site_url(); ?></wp:base_blog_url>
<blog_local_time><?php echo date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 )); ?></blog_local_time>
<generator>https://wordpress.org/?v=4.6.1</generator>
<cl_status><?php echo 'active'; ?></cl_status>
<esign_right_holder><?php echo $display_name; ?></esign_right_holder>
<esign_copyright_identifier><?php echo $identifier; ?></esign_copyright_identifier>
<esign_reason><?php echo 'ldc'; ?></esign_reason>
<esign_time><?php echo date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 )); ?></esign_time>
<esign_email><?php echo $email_address; ?></esign_email>
<agreement><![CDATA[<?php echo wpclink_prepare_license_offer($content_id); ?>]]></agreement>
<request_content_type><?php echo get_post_type($content_id); ?></request_content_type>
<license_class><?php 
	if($license_class = get_post_meta($content_id,'wpclink_creation_license_class',true)){
		if($license_class == '0'){
			echo wpclink_get_option('wpclink_license_class');
		}else{
			echo $license_class;
		}
	}else{
		echo wpclink_get_option('wpclink_license_class');
	}
 ?></license_class>
	<post_taxonomy_permission><?php  
		if($taxonomy_permission = get_post_meta($content_id, 'wpclink_programmatic_right_categories', true)){
			echo $taxonomy_permission;
		} ?></post_taxonomy_permission>
</channel>
</rss>
<?php
		}else{
			// INVALID
		echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
		<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>
	<channel>
    <response><?php echo 'invalid'; ?></response> 
    </channel>
</rss>
		<?php 
		}
	}
}