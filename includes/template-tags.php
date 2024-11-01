<?php
/**
 * CLink Template Tag Functions 
 *
 * CLink template tag and shortcodes functions
 *
 * @package CLink
 * @subpackage Link Manager
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Info Template Tag Enqueue Style
 * 
 */
function wpclink_info_template_tag_style(){
	// Template Tag Style
	wp_enqueue_style( 'wpclink-template-tag', plugins_url('public/css/'.wpclink_compress_static_files('tamplate-tag.css'), dirname(__FILE__) ) );
}
add_action( 'wp_enqueue_scripts', 'wpclink_info_template_tag_style' );
/**
 * CLink Info Template Tag
 * 
 * @return string template tag
 * 
 */
function wpclink_info_template_tag($the_post_id = 0, $post_meta = '', $location = ''){
	if($location == 'single-bottom') return '';
	
	// Inside Loop
	$post_id = get_the_ID();
	
	if(empty($post_id)){
		// Outside Loop
		global $post;
		$post_id = $post->ID;
	}
	// If Registered
if($contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true )){
			
			// Unique ID
			$unique_id = uniqid();
			// Blank
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
				
				// Time
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
	// 
			if(isset($image_attributes['created_timestamp'])){
				$created_time = $image_attributes['created_timestamp']; 
			}
	
			if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
				$created_time_right = $get_post_date;
			}else{
				if($right_created_time = get_post_meta( $post_id, 'wpclink_right_created_time', true )){
					$created_time_right = $right_created_time;
				}
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
							
				
					if(count($clink_versions) > 3){
						$version_list_popup_more_btn = '<a id="version-load-more-'.$post_id.'" class="version-load-more template" data-postid="'.$post_id.'">More</a>';
						$version_list_popup_more.=wpclink_get_version_list_popup($post_id,0,3);
					}



					$versions = '<tr><td width="40%"><span class="cl-expend-btn-version template" data-postid="'.$post_id.'">+</span> Versions</td><td class="cl-value" width="60%">'.count($clink_versions).'</td></tr>';

					$version_see_more = '<div class="cl-version-list">		
					<div id="cl-version-list-'.$post_id.'" style="display:none" class="cl-version-more template">'.wpclink_get_version_list_popup($post_id,3,0,$version_list_popup_more_btn).'</div><div id="cl-version-list-more-'.$post_id.'" style="display:none" class="cl-version-more template">'.$version_list_popup_more.'</div>
					</div>';

			
						
					}
				}
	
	
			if($right_created_time = get_post_meta( $post_id, 'wpclink_right_created_time', true )){
				$right_ID = $image_attributes['right_ID'];
				$right_tr = '<div class="cl-title">
            <h3>Rights</h3>
          </div>
          <table cellspacing="0" cellpadding="0">
            <tbody><tr>
		<td class="cl-strong" width="40%">Declared at</td>
		<td class="cl-value" width="60%">'.$right_created_time.' <a target="_blank" href="'.$right_ID.'" class="external"></a></td>
		</tr></tbody>
          </table>';
			}
	
		// Get Last Archive if has
		$archive_link = wpclink_get_last_archive($post_id);
	
		if(!empty($archive_link)){
			$archives_count = wpclink_archive_count_by_post_id($post_id);
			if($archives_count > 0){
				
					
$thumbnail_src =  wp_get_attachment_image_src( $post_id, 'medium' );
	
$archive_count = wpclink_archive_count_by_post_id($post_id);
	
if (function_exists('wpclink_show_archive_list_popup')) {
	$archive_list_popup = wpclink_show_archive_list_popup($post_id,3);
	if($archive_count > 3){
		$archive_list_popup_more_btn = '<a id="archive-load-more-'.$post_id.'" class="archive-load-more template" data-postid="'.$post_id.'">More</a>';
		$archive_list_popup_more.=wpclink_show_archive_list_popup($post_id,0,3);
	}
}else{
	$archive_list_popup = '';
	$archive_list_popup_more = '';
	$archive_list_popup_more_btn = '';
}

			

$archive_tr = '<table class="archive-list-table table-archive"><tbody>

<tr><td width="40%"><span class="cl-expend-btn-archive template" data-postid="'.$post_id.'">+</span> Archives</td><td class="cl-value" width="60%">'.$archive_count.'</td></tr>

<tr><td></td><td><div class="cl-archive-list">

<div id="cl-archive-list-'.$post_id.'" style="display:none" class="cl-archive-more template">'.$archive_list_popup.$archive_list_popup_more_btn.'</div><div id="cl-archive-list-more-'.$post_id.'" style="display:none" class="cl-archive-more template">'.$archive_list_popup_more.'</div>

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
			$linked_tr = '
			<tr>
			</tr>
			<tr>
			<td></td>
			<td></td>
			</tr>';
			$label_publish = 'Originated at';
			
			
			
			$linked_tr = '<table cellspacing="0" cellpadding="0" class="table-linked">
            <tbody><tr><td class="cl-strong cl-space-popup" width="40%">'.$label_publish.'</td>
		<td class="cl-value" width="60%">'.$wpclink_time_of_creation.' <a target="_blank" href="'.$orign_creation_url.'" class="external"></a></td>
		</tr></tbody></table>';
		}
	
	
	if($recorded_url = get_post_meta( $post_id, 'wpclink_creation_ID', true )){
		$recorded_url_full = WPCLINK_ID_URL.'/#objects/'.$recorded_url;
	}
?>
<div id="reuse" class="clink-templatetag-box">
  <ul class="cl-info-menu">
    <li class="cl-info" id="mode-<?php echo $unique_id; ?>"> <span class="cl-darkmode-icon" id="nightmode_btn-<?php echo $unique_id; ?>" <?php if(wpclink_is_amp_active()){ ?> on="tap:mode-<?php echo $unique_id; ?>.toggleClass(class='night'),nightmode_btn-<?php echo $unique_id; ?>.toggleClass(class='night')" <?php } ?> role="button" tabindex="0">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <title/>
        <path d="M20.21,15.32A8.56,8.56,0,1,1,11.29,3.5a.5.5,0,0,1,.51.28.49.49,0,0,1-.09.57A6.46,6.46,0,0,0,9.8,9a6.57,6.57,0,0,0,9.71,5.72.52.52,0,0,1,.58.07A.52.52,0,0,1,20.21,15.32Z" fill="#6d6d6d"/>
      </svg>
      </span> <span class="cl-info-icon" <?php if(wpclink_is_amp_active()){ ?> on="tap:cl-media-popup-<?php echo $unique_id; ?>.toggleClass(class='show'),nightmode_btn-<?php echo $unique_id; ?>.toggleClass(class='show')" <?php } ?> role="button" tabindex="0">
      <svg id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" style="background:0 0">
        <g>
          <g>
            <path style="fill:#6d6d6d" d="M437.02 74.98C388.667 26.629 324.38.0 256 0S123.333 26.629 74.98 74.98C26.629 123.333.0 187.62.0 256s26.629 132.667 74.98 181.02C123.333 485.371 187.62 512 256 512s132.667-26.629 181.02-74.98C485.371 388.667 512 324.38 512 256S485.371 123.333 437.02 74.98zM256 101c24.813.0 45 20.187 45 45s-20.187 45-45 45-45-20.187-45-45S231.187 101 256 101zm64 3e2H190v-30h21V251h-20v-30h110v150h19v30z"/>
          </g></g><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/>
      </svg>
      </span>
      <ul class="cl-subitem" id="cl-media-popup-<?php echo $unique_id; ?>">
        <div class="cl-media-popup scrollbar style-1">
        <div class="cl-wrapper">
        <div class="cl-body">
			<table cellspacing="0" cellpadding="0" class="signer-info">
<tbody><tr><td>Authenticated by </td><td class="cl-value sign-logo-wrapper" width="60%"><span class="sign-logo"></span> CLink Media</td></tr><tr><td>Authenticated at</td><td class="cl-value" width="60%"><?php echo $get_post_date; ?><a target="_blank" href="<?php echo $recorded_url_full; ?>" class="external"></a></td></tr>
</tbody>
</table>
			<?php if(!empty ($archive_link) || !empty($linked_tr) || !empty($versions)){ ?>
          <div class="cl-title">
            <h3>Provenance</h3>
          </div>
         <table  class="table-version"  cellspacing="0" cellpadding="0">
            <tbody>			
            <?php echo $versions; ?>
			</tbody>
		</table>
			<?php echo $version_see_more; ?>
            <?php echo $archive_tr; ?>
			<?php echo $linked_tr; ?>  
			<?php } ?>
            <?php echo $right_tr; ?>
        </div>
			</div>
		  </div>
      </ul>
    </li>
  </ul>
</div>
<?php 
														   
																   
} 
}
// Template Tag - do_action( 'wpclink_display_content_authenticity' ); 
add_action('wpclink_display_content_authenticity_info','wpclink_info_template_tag',10);
add_action('twentytwenty_start_of_post_meta_list','wpclink_info_template_tag',10,3);
/**
 * CLink Short Code Creation Info Function
 * 
 * @return string template tag
 * 
 */
function wpclink_creation_info_func( $atts ) {
    	ob_start();
        wpclink_info_template_tag();
        return ob_get_clean();
}
// Shortcode ['wpclink-creation-info'] for creation template tag 
add_shortcode( 'wpclink-creation-info', 'wpclink_creation_info_func' );
