<?php
/**
 * CLink Reuse Functions 
 *
 * CLink reuse popup functions
 *
 * @package CLink
 * @subpackage Link Manager
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Popup Agreement URL Display
 * 
 * @param string $weburl site address
 * 
 */
function wpclink_offer_popup_link($weburl){
	
	global $wpdb;
	
	// GET IP
	$url = parse_url($weburl);
	$host_url = $url['host'];
	
	// PICK IP
	$ip = gethostbyname($host_url);
	
	// Table Prefix
    $table_name_token = $wpdb->prefix . 'wpclink_tokens'; 
	
	
	/* INSERT TOKEN IN DATABASE */
	$token_key = wpclink_go_live_link(8);
	$web_address = $weburl;
	$ip_address = $ip;
	
	$record_found = $wpdb->get_row( 'SELECT * FROM '.$table_name_token.' WHERE linked_site_url = "'.$web_address.'" AND token_expiration <> 1' );
	
	if ( $record_found !== NULL ) {
		$token_key = $record_found->token;
	}else{
		// INSERT
		$wpdb->insert($table_name_token,
				array(	'linked_site_url' => $web_address,
						'linked_site_domain' => $host_url,
						'token' => $token_key,
						'linked_site_IP' => $ip_address,
						'license_delivery_date' => '',
						'token_expiration' => '0'));
	}
	
		echo '<div id="cl_step2" class="cl_step">
			<h2>CLink Agreement URL</h2>
			<h3>Put this url in your clink to start </h3>
			<input id="cl_weburl" required="required" autofocus="autofocus" value="'.wpclink_get_token_url($token_key).'" type="url">
			</div>';
}
add_action('cl_offer_popup','wpclink_offer_popup_link',10,1);
/**
 * CLink Reuse Popup Agreement URL Display
 * 
 * @param string $weburl site address
 * @param string $content_type content type uc-ut-um | uc-at-um
 * @param string $content_url url of the current post
 * 
 */
function wpclink_generate_token_link($weburl, $content_type = false, $content_url = false){
	
	global $wpdb;
	
	// GET IP
	$url = parse_url($weburl);
	$host_url = $url['host'];
	
	// PICK IP
	$ip = gethostbyname($host_url);
	
	// Table Prefix
    $table_name_token = $wpdb->prefix . 'wpclink_tokens'; 
	
	// CONTENT TYPE
	$content_type = $content_type;
	
	// CONTENT URL TO POST ID
	$get_media_id = parse_url($content_url);
	if(isset($get_media_id['query'])){
		parse_str($get_media_id['query'], $media_query_id);
		if(isset($media_query_id['id'])){
		   $postid = $media_query_id['id'];
		}else{
		   $content_url = str_replace('&clink=offer','',$content_url);
			$postid = url_to_postid( $content_url );
		}
	}else{
		$content_url = str_replace('&clink=offer','',$content_url);
		$postid = url_to_postid( $content_url );
	}
	
	
	
	/* INSERT TOKEN IN DATABASE */
	$token_key = wpclink_go_live_link(8);
	$web_address = $weburl;
	$ip_address = $ip;
	
	$record_found = $wpdb->get_row( 'SELECT * FROM '.$table_name_token.' WHERE post_id = "'.$postid.'" AND token_expiration <> 1' );
	
	if ( $record_found !== NULL ) {
		$token_key = $record_found->token;
	}else{
		// INSERT
		$wpdb->insert($table_name_token,
				array(	'linked_site_url' => $web_address,
						'linked_site_domain' => $host_url,
						'token' => $token_key,
						'linked_site_IP' => $ip_address,
						'token_type' => $content_type,
						'post_id' => $postid,
						'license_delivery_date' => '',
						'token_expiration' => '0',
						'creation_uri' => $content_url));
	}
	
echo '<input id="reuse_token_key" class="startlink"  value="'.wpclink_get_token_url($token_key).'" type="hidden">';
	
}
add_action('cl_offer_popup_clinkid','wpclink_generate_token_link',10,3);
/**
 * CLink Reuse Next Button
 *
 */
function wpclink_offer_popup_next_func(){
	echo '<a id="next_step2" class="cl_download" href="#">Next</a>';
}
// Register reuse next button
add_action('cl_offer_popup_next_link','wpclink_offer_popup_next_func');
/**
 * CLink Reuse Button Redirect to orgin of content url
 * 
 */
function wpclink_redirect_to_origin(){
	if(isset($_GET['clink']) and $_GET['clink'] == 'offer'){
		
		global $post;
		$post_id = $post->ID;
		if($origin_url = get_post_meta($post_id,'wpclink_referent_post_uri',true)){			
			$rediect_me = add_query_arg( 'clink', 'offer', $origin_url );
			 wp_redirect( $rediect_me );
        	 die;
		}
	}
}
// Register reuse button redirect to origin content on redirection template
add_action( 'template_redirect', 'wpclink_redirect_to_origin' );
/**
 * CLink Get Global ID of Post
 * 
 * @return integer post id
 */
function wpclink_global_id() {
	// For Media
  if(isset($_GET['clink_media_license']) and isset($_GET['id']) and is_numeric($_GET['id'])){  
	  return $_GET['id'];
  }
	
  if ( in_the_loop() ) {
    $post_id = get_the_ID();
  } else {
    /** @var $wp_query wp_query */
    global $wp_query;
    $post_id = $wp_query->get_queried_object_id();
    }
  return $post_id;
}
/**
 * CLink Content is Refernet List Published
 * 
 * 
 * @return boolean
 */
function wpclink_is_post_referent($post_id = 0){
	if($post_id == 0){
		$post_id = wpclink_global_id();
	}
	
	if($contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true )){
		
		$registered_list_post = wpclink_get_option('referent_posts');
		$registered_list_page = wpclink_get_option('referent_pages');
		$registered_list_attachment = wpclink_get_option('referent_attachments');
		
		
		if(!is_array($registered_list_post)) $registered_list_post = array();
			if(!is_array($registered_list_page)) $registered_list_page = array();
				if(!is_array($registered_list_attachment)) $registered_list_attachment = array();
			
		
		$registered_complete = array();
		
		if(is_array($registered_list_post)){
			$registered_complete = array_merge($registered_list_post,$registered_complete);
		}
		if(is_array($registered_list_page)){
			$registered_complete = array_merge($registered_list_page,$registered_complete);
		} 
		if(is_array($registered_list_attachment)){
			$registered_complete = array_merge($registered_list_attachment,$registered_complete);
		}
			
		if(in_array($post_id, $registered_complete)){
			return true;
		}
		
		
		
	}
	return false;
}
/**
 * CLink Reuse Offer Popup
 * 
 */
function wpclink_do_popup_link() {
	// If import mode only
	if(wpclink_import_mode()) return false;
	// if content is not in linked list
	if(!wpclink_is_post_referent()) return false;
	// Not for media checkout page
	if(isset($_GET['clink_media_license'])) return false;
	
	$token = uniqid();
	
	
	
?>
<div id="CLModal" class="cl_modal">
  <div class="cl_modal-content">
    <span class="cl_close">&times;</span>
<form id="cl_new_offer_clinkid" action="" style="display:block">
<div id="cl_offer_response" style="display:block">
<p class="style4" ><?php _e('The reuse of this content is enabled for WordPress powered sites using ','cl_text'); ?><a class="cl_hyperlink" target="_blank" href="https://wordpress.org/plugins/wpclink/">wpCLink</a> plugin.</p>


<p class="description style2"><?php _e('Please Log Into your WordPress Instance','cl_text'); ?></p>
<input id="cl_weburl" required="required" value="" autofocus="autofocus" placeholder="https://mydomain.com" type="url">
<p class="style1"><?php _e('The content will be accessible at the Creations &rarr; Linked menu  in your WordPress instance after you accept the Terms and Conditions of the License.','cl_text'); ?></p>
<input type="hidden" id="cl_popup_id" value="<?php echo $token; ?>" />
<div class="quick-response"></div>
<?php /* <p class="divider-or"><span>or</span></p>
 <p><a class="go_care"><span class="cico"></span>Sign In with CLink</a></p> */ ?>
<input style="visibility:hidden" class="content_type" type="checkbox" name="content_type" checked="checked" value="single" />
<input type="hidden" name="cl_auto_code" id="cl_auto_code" value="" />

<?php if ( $group_found = wpclink_get_option('wpclink_group') ) {
if(!empty($group_found['url'])){ ?>
<p class="style3"><a class="additional-btn" href="#"><strong>+</strong> <?php _e('Available Options','cl_text'); ?></a></p>
<div class="additional_option">
<ul>
  <li>
    <label class="container_check"><?php _e('Join Group','cl_text'); ?>
  <input class="content_type" type="checkbox" name="content_type" value="group">
  <span class="checkmark"></span>
</label>
  </li>
</ul>
</div>
<?php }
} ?>
<div id="wait"></div>
<input id="cl_modal_submit" value="Get a license" type="submit">
</div>
</form>		
    <input type="hidden" name="auto_content" id="auto_content" value="<?php echo wpclink_global_id() ?>" />
	<p class="style3"><a target="_blank" href="https://licenses.clink.id/personal/0-9i/"><?php _e('License Terms and Conditions','cl_text'); ?> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M9 3h8v8l-2-1V6.92l-5.6 5.59-1.41-1.41L14.08 5H10zm3 12v-3l2-2v7H3V6h8L9 8H5v7h7z"/></g></svg></a></p>
</div>
</div>


<?php }
// Register clink reuse offer popup function
add_action( 'wp_footer', 'wpclink_do_popup_link' );
/**
 * CLink Reuse Content Button After Post and Page Content
 * 
 * @param string $content default content
 * 
 */
function wpclink_do_clink_ID_with_reuse_button($content){
	
global $post;
$post_id = $post->ID;
	
if (is_single() || is_archive() || is_home() || is_page()) {  
    
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	$post_orign_creator = get_post_meta( $post_id, 'wpclink_referent_creator_display_name', true );
	$post_orign_creator_id = get_post_meta( $post_id, 'wpclink_referent_creator_party_ID', true );
	
	$post_orign_creator_id_space_removed = str_replace(' ', '', $post_orign_creator_id);
	
	$referent_creator = get_post_meta( $post_id, 'wpclink_referent_creator_party_ID', true );
	
	$registered_list_post = wpclink_get_option('referent_posts');
	$registered_list_page = wpclink_get_option('referent_pages');
	$registered_list_attachment = wpclink_get_option('referent_attachments');
		
	// Fixes
	if(is_array($registered_list_attachment)){
	}else{
		$registered_list_attachment = array();
	}
	
	$reuse_button_left_offset = wpclink_get_option('reuse_button_left_offset');
	$reuse_button_top_offset = wpclink_get_option('reuse_button_top_offset');
	
	if(!empty($reuse_button_left_offset)){
		if(!empty($reuse_button_top_offset)){
			$style_reuse = 'style="margin-right: '.$reuse_button_left_offset.'px; margin-top: '.$reuse_button_top_offset.'px;"';
		}else{
			$style_reuse = 'style="margin-right: '.$reuse_button_left_offset.'px;"';	
		}
	}else{
		if(!empty($reuse_button_top_offset)){
			$style_reuse = 'style="margin-top: '.$reuse_button_top_offset.'px;"';
		}else{
			$style_reuse = '';
		}
	}
	
	
	
	if ( !empty( $contentID ) ) {
		
	if(is_single() || is_page()){
		$content.='<div class="clink-contentbox">';
		
		if(is_array($registered_list_post) and is_array($registered_list_page) and is_array($registered_list_attachment)){
			$registered_complete = array_merge($registered_list_page,$registered_list_post,$registered_list_attachment);
			if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
				// Linked
					$content .= '<p>Originally posted by <a  target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$post_orign_creator_id.'">'.$post_orign_creator.'</a></p>';
					if(get_post_type($post_id) == "attachment"){
					}else{
						$content .= '<a class="cl-resuse_origin" href="'.add_query_arg( 'clink', 'offer', $sync_origin).'">Reuse this content</a>';
					}
				
			}else{
				
			// Referent
			if(in_array($post_id, $registered_complete)){
				
				if(wpclink_is_amp_inactive()){
					$content.= '<a '.$style_reuse.'  class="cl-resuse">Reuse this content</a>';
				}else{
					$content.= '<a '.$style_reuse.' href="'.add_query_arg( 'clink', 'offer', get_permalink($post_id)).'"  class="cl-resuse_origin">Reuse this content</a>';
				}
			}else{
				// Linked
			if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
					$content .= '<p>Originally posted by <a  target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$post_orign_creator_id.'">'.$post_orign_creator.'</a></p>';
					if(get_post_type($post_id) == "attachment"){
					}else{
						$content .= '<a class="cl-resuse_origin href="'.add_query_arg( 'clink', 'offer', $sync_origin).'">Reuse this content</a>';
					}
				}
		}
				
				
			}
		}
		
		if(is_array($registered_list_post) and is_array($registered_list_page) and is_array($registered_list_attachment)){
			$registered_complete = array_merge($registered_list_page,$registered_list_post,$registered_list_attachment);
			if(in_array($post_id, $registered_complete) || !empty($sync_origin)){
				
			// Unique		
			if(empty($unique_id)) $unique_id = uniqid();
				
			// Blank by default
			$created_time = '';
			$orign_creation_url = '';
			$origin_right_holder = '';
			$origin_versions = '';
			$linked_tr = '';
				
				// Linked
				if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
				// Custom URL
				$custom_url = get_post_meta( $post_id, 'wpclink_custom_url', true );
				$image_attributes['reuse'] = 1;
				$custom_url_reuse = add_query_arg( 'clink', 'offer', $custom_url );
				$image_attributes['reuse_url'] = urlencode($custom_url_reuse);
				// Publish At
				if($referent_creation_ID = get_post_meta( $post_id, 'wpclink_referent_creation_ID', true )){
					$image_attributes['orign_creation_ID'] = $referent_creation_ID;
					$image_attributes['orign_creation_url'] = WPCLINK_ID_URL.'/#objects/'.$referent_creation_ID;
					$image_attributes['origin_rights_holder_ID_url'] = 'ABC.com';	;
				}
				// Publish At Time
				if($wpclink_time_of_creation =  get_post_meta( $post_id, 'wpclink_time_of_creation', true )){
						$image_attributes['created_timestamp'] = $wpclink_time_of_creation;
				}
				// Republish At
				if($contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true )){
					$image_attributes['republish_creation_ID'] = $contentID;
					$image_attributes['republish_creation_ID_url'] = WPCLINK_ID_URL.'/#objects/'.$contentID;	
					if($wpclink_rights_holder_user_id =  get_post_meta( $post_id, 'wpclink_rights_holder_user_id', true )){
						$creator_user_data = get_userdata($wpclink_rights_holder_user_id);
						$creator_user_display = $creator_user_data->display_name;
						$image_attributes['republish_rights_holder'] = $creator_user_display;
					}
					$image_attributes['republish_rights_holder_url'] = WPCLINK_ID_URL.'/#objects/'.$contentID;	
				}
				// Rights Holder URL
				if($wpclink_right_ID = get_post_meta( $post_id, 'wpclink_right_ID', true )){
						$image_attributes['right_ID'] = WPCLINK_ID_URL.'/#objects/'.$wpclink_right_ID;
				}
				if($original_rights_holder =  get_post_meta( $post_id, 'wpclink_referent_rights_holder_display_name', true )){
						$image_attributes['origin_rights_holder_display_name'] = $original_rights_holder;
				}
				$get_post_date  = get_the_date('Y-m-d',$post_id);
				$get_post_date .= 'T';
				$get_post_date .= get_the_date('G:i:s',$post_id);
				$get_post_date .= 'Z';
				$image_attributes['republish_created_timestamp'] = $get_post_date;
			}else{
				$get_post_date  = get_the_date('Y-m-d',$post_id);
				$get_post_date .= 'T';
				$get_post_date .= get_the_date('G:i:s',$post_id);
				$get_post_date .= 'Z';
				$image_attributes['created_timestamp'] = $get_post_date;
				// CLink ID
				if($orign_contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true )){
					$clink_array['orign_creation_ID'] = $orign_contentID;
					$image_attributes['orign_creation_url'] = WPCLINK_ID_URL.'/#objects/'.$orign_contentID;		
				}
				if($rights_holder_id = get_post_meta( $post_id, 'wpclink_rights_holder_user_id', true )){
					// Display Name
					$creator_user_data = get_userdata($rights_holder_id);
					$creator_user_display = $creator_user_data->display_name;
					$image_attributes['origin_rights_holder_display_name'] = $creator_user_display;
					// URL
					$origin_rights_holder_ID = get_user_meta($rights_holder_id,'wpclink_party_ID',true);
					$image_attributes['origin_rights_holder_ID_url'] = WPCLINK_ID_URL.'/#objects/'.$origin_rights_holder_ID;	;
				}
				
				if($wpclink_right_ID = get_post_meta( $post_id, 'wpclink_right_ID', true )){
						$image_attributes['right_ID'] = WPCLINK_ID_URL.'/#objects/'.$wpclink_right_ID;
				}
			}
				
				
			if(isset($image_attributes['created_timestamp'])){
				$created_time = $image_attributes['created_timestamp']; 
			}
			if(isset($image_attributes['orign_creation_url'])){
				$orign_creation_url = $image_attributes['orign_creation_url']; 
			}
			if(isset($image_attributes['origin_rights_holder_display_name'])){
				$origin_right_holder = $image_attributes['origin_rights_holder_display_name'];
			}
				
			// Version CLink ID(s)
				if($clink_versions = get_post_meta( $post_id, 'wpclink_versions', true )){
					if(is_array($clink_versions)){
						
				$origin_versions = $image_attributes['origin_versions'];
				
				
				$versions = '<tr><td width="40%">Versions</td><td width="60%" class="cl-value">'.$origin_versions.'</td></tr>';
				
				
				
				if(count($clink_versions) > 3){
					$version_list_popup_more_btn = '<a id="version-load-more-'.$post_id.'" class="version-load-more reuse" data-postid="'.$post_id.'">More</a>';
					$version_list_popup_more.=wpclink_get_version_list_popup($post_id,0,3);
				}



				$versions = '<tr><td width="40%"><span class="cl-expend-btn-version reuse" data-postid="'.$post_id.'">+</span> Versions</td><td class="cl-value" width="60%">'.count($clink_versions).'</td></tr>';

				$version_see_more = '<div class="cl-version-list">		
				<div id="cl-version-list-'.$post_id.'" style="display:none" class="cl-version-more reuse">'.wpclink_get_version_list_popup($post_id,3,0,$version_list_popup_more_btn).'</div><div id="cl-version-list-more-'.$post_id.'" style="display:none" class="cl-version-more reuse">'.$version_list_popup_more.'</div>
				</div>';
			
					}
				}	

			if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
				$created_time_right = $get_post_date;
			}else{
				$created_time_right = $created_time;
			}
				
				
			if($time_right = get_post_meta( $post_id, 'wpclink_right_created_time', true )){
				
			}
				
	
if(isset($image_attributes['right_ID'])){
				$right_ID = $image_attributes['right_ID'];
				$right_tr = '<div class="cl-title"><h3>Rights</h3></div><table cellspacing="0" cellpadding="0">
            <tbody><tr>
		<td class="cl-strong" width="40%">Declared at</td>
		<td class="cl-value" width="60%">'.$time_right.' <a target="_blank" href="'.$right_ID.'" class="external"></a></td>
	</tr></tbody></table>';
}
				
// Get Last Archive if has
$archive_link = wpclink_get_last_archive($post_id);

if(!empty($archive_link)){
	$archives_count = wpclink_archive_count_by_post_id($post_id);

if($archives_count > 0){
				
				
				
if (function_exists('wpclink_show_archive_list_popup')) {
	$archive_list_popup = wpclink_show_archive_list_popup($post_id,3);
	if($archive_count > 3){
		$archive_list_popup_more_btn = '<a id="archive-load-more-'.$post_id.'" class="archive-load-more reuse" data-postid="'.$post_id.'">More</a>';
		$archive_list_popup_more.=wpclink_show_archive_list_popup($post_id,0,3);
	}
}else{
	$archive_list_popup = '';
	$archive_list_popup_more = '';
	$archive_list_popup_more_btn = '';
}

			

$archive_tr = '<table class="archive-list-table table-archive"><tbody>

<tr><td width="40%"><span class="cl-expend-btn-archive reuse" data-postid="'.$post_id.'">+</span> Archives</td><td class="cl-value" width="60%">'.$archives_count.'</td></tr>

<tr><td></td><td><div class="cl-archive-list">

<div id="cl-archive-list-'.$post_id.'" style="display:none" class="cl-archive-more reuse">'.$archive_list_popup.$archive_list_popup_more_btn.'</div><div id="cl-archive-list-more-'.$post_id.'" style="display:none" class="cl-archive-more reuse">'.$archive_list_popup_more.'</div>

</div></td></tr></tbody></table>';
				
				
			}
		}
		$label_publish = 'Published at';
		if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
			if(isset($image_attributes['republish_creation_ID_url'])){
				$republish_creation_url = $image_attributes['republish_creation_ID_url'];
			}
			if(isset($image_attributes['republish_rights_holder'])){
				$republish_rights_holder = $image_attributes['republish_rights_holder'];
			}
			if(isset($image_attributes['republish_created_timestamp'])){
				$republish_created_timestamp = $image_attributes['republish_created_timestamp'];
			}
			
			$label_publish = 'Originated at';
			$linked_tr = '<table cellspacing="0" cellpadding="0" class="table-linked"><tbody><tr>
		<td class="cl-strong cl-space-popup" width="40%">'.$label_publish.'</td>
		<td class="cl-value" width="60%">'.$created_time.' <a target="_blank" href="'.$orign_creation_url.'" class="external"></a></td>
		</tr></tbody></table>';
			
		}
				
				
		$provenance = '';
		if( !empty($clink_versions) || !empty($archive_link) || !empty($linked_tr)){
			
			if(!empty($sync_origin)){
				$provenance_part = '';
			}
			$provenance = '<div class="cl-title">
			<h3>Provenance</h3>
			</div><table cellspacing="0" cellpadding="0">
			<tbody>'.$provenance_part.'</tbody>
			</table>
			  <table  class="table-version"  cellspacing="0" cellpadding="0">
            <tbody>'.$versions.'</tbody>
		</table>'.$version_see_more.$archive_tr.$linked_tr;
			
			
		}
				
	if($recorded_url = get_post_meta( $post_id, 'wpclink_creation_ID', true )){
		$recorded_url_full = WPCLINK_ID_URL.'/#objects/'.$recorded_url;
	}
		$content.= '<ul class="cl-info-menu content">
		<li class="cl-info" id="mode-'.$unique_id.'">
		'.wpclink_nightmode_button_return($unique_id,true).'
		<ul class="cl-subitem" id="cl-media-popup-'.$unique_id.'">
		<div class="cl-media-popup scrollbar style-1">
		<div class="cl-wrapper">
		<div class="cl-body">
		<table cellspacing="0" cellpadding="0" class="signer-info">
<tbody><tr><td>Authenticated by </td><td class="cl-value sign-logo-wrapper" width="60%"><span class="sign-logo"></span> CLink Media</td></tr><tr><td>Authenticated at</td><td class="cl-value" width="60%">'.$get_post_date.' <a href="'.$recorded_url_full.'" class="external" target="_blank"></a></td></tr>
</tbody>
</table>'.$provenance.$right_tr.'
		</div>
		</div>
		</div>
		</ul>
		</li>
		</ul>';
			}else{
				$content .='<p><a class="cl-clinkid" target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$contentID.'">Provenance and rights</a></p>';
			}
		}
	
		$content.='</div>';
		
	}else{
		$content .='<p><a class="cl-clinkid" target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$contentID.'">Provenance and rights</a></p>';
	}
		
	  }
	
}
    return $content;
}
if(!isset($_GET['_locale'])){
    if ( !is_admin() ) {
// Register clink resue button after post and pages function
add_filter( "the_content", "wpclink_do_clink_ID_with_reuse_button" );
    }    
}
/**
 * CLink Reuse Media Button After Attachment 
 * 
 * @param string $content default content
 * 
 */
function wpclink_media_reuse_button($content = ''){
	
	$post_id = $_GET['id'];
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	// Attachments
	$registered_list_attachment = wpclink_get_option('referent_attachments');
	
	// Fixes
	if(is_array($registered_list_attachment)){
	}else{
		$registered_list_attachment = array();
	}
	
	if ( !empty( $contentID ) ) {	
		if(is_array($registered_list_attachment)){
			$registered_complete = $registered_list_attachment;
		if(in_array($post_id, $registered_complete)){
			
				if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
				}else{
					
					$content .= '<a class="cl-get-license" >Get a license</a>';
					
				}
		
		}
		}
	
		
		$content .= '<span class="media-title">'.get_the_title($post_id).'</span> ';
	  }
	
	return $content;
	
}