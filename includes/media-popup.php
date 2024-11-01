<?php
/**
 * CLink Licensable Functions
 *
 * CLink licensable functions
 *
 * @package CLink
 * @subpackage Content Manager
 */
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink append HTML on DOMNode
 * 
 * @param object $parent  element to target
 * @param object $source  elements to append
 * 
 * @return object 
 */
function wpclink_appendHTML(DOMNode $parent, $source) {
    $tmpDoc = new DOMDocument();
    $tmpDoc->loadHTML($source);
    foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
        $node = $parent->ownerDocument->importNode($node, true);
        $parent->appendChild($node);
    }
}
/**
 * CLink Licensable Media Popup
 * 
 * @param int $attach_id  post id
 * @param array $image_attributes image attributes
 * @param string $unique_id unique id for attribute id
 * 
 * @return string 
 */
function wpclink_licensable_media_popup($attach_id = 0,$image_attributes = array(), $unique_id = ''){
	
	if(empty($unique_id)) $unique_id = uniqid();
	
	$created_time = '';
	$orign_creation_url = '';
	$origin_right_holder = '';
	$origin_versions = '';
	$linked_tr = '';
	
	if(isset($image_attributes['created_timestamp'])){
		$created_time = $image_attributes['created_timestamp'];
		$time_data = wpclink_get_image_datetime($attach_id);
		$time_of_modification = $time_data['modified'];
		
	}
	
	
	
	if(isset($image_attributes['orign_creation_url'])){
		$orign_creation_url = $image_attributes['orign_creation_url']; 
	}
	
	if(isset($image_attributes['origin_rights_holder_display_name'])){
		$origin_right_holder = $image_attributes['origin_rights_holder_display_name'];
	}
	
	if(isset($image_attributes['origin_versions']) and !empty($image_attributes['origin_versions'])){
		$origin_versions = $image_attributes['origin_versions'];
		
	
		
		
	}
	
	if(isset($image_attributes['right_ID'])){
		$right_ID = $image_attributes['right_ID'];
		
		if($right_created_time = get_post_meta( $attach_id, 'wpclink_right_created_time', true )){
		
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
	}
	
	
// Get Last Archive if has
$archive_link = wpclink_get_last_archive($attach_id);
if(!empty($archive_link)){
	
	
	
}
	
	
// Get Last Archive if has
$archive_link = wpclink_get_last_archive($attach_id);
if(!empty($archive_link)){
	
$thumbnail_src =  wp_get_attachment_image_src( $attach_id, 'medium' );
	
$archive_count = wpclink_archive_count_by_post_id($attach_id);
	
if (function_exists('wpclink_show_archive_list_popup')) {
	
	$archive_thumbnail = apply_filters('wpclink_archive_thumbnail',true,$attach_id);
	
	$archive_list_popup = wpclink_show_archive_list_popup($attach_id,3);
	if($archive_count > 3){
		$archive_list_popup_more_btn = '<a id="archive-load-more-'.$attach_id.'" class="archive-load-more  checkout" data-postid="'.$attach_id.'">More</a>';
		$archive_list_popup_more.=wpclink_show_archive_list_popup($attach_id,0,3);
	}
}else{
	$archive_list_popup = '';
	$archive_list_popup_more = '';
	$archive_list_popup_more_btn = '';
}
	if($archive_thumbnail){
		$thumbnail_src =  wp_get_attachment_image_src( $attach_id, 'medium' );
		$archive_tr = '<div class="cl-arhive-box"><a class="archive_thumbnail" target="_blank" href="'.$archive_link.'" style="background-image:url('.$thumbnail_src[0].')"><span class="archive-icon-big"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M13.65 2.88c3.93 2.01 5.48 6.84 3.47 10.77s-6.83 5.48-10.77 3.47c-1.87-.96-3.2-2.56-3.86-4.4l1.64-1.03c.45 1.57 1.52 2.95 3.08 3.76 3.01 1.54 6.69.35 8.23-2.66 1.55-3.01.36-6.69-2.65-8.24C9.78 3.01 6.1 4.2 4.56 7.21l1.88.97-4.95 3.08-.39-5.82 1.78.91C4.9 2.4 9.75.89 13.65 2.88zm-4.36 7.83C9.11 10.53 9 10.28 9 10c0-.07.03-.12.04-.19h-.01L10 5l.97 4.81L14 13l-4.5-2.12.02-.02c-.08-.04-.16-.09-.23-.15z"/></g></svg></a></span></div>';
	}else{
		$archive_tr = '<table class="archive-list-table table-archive"><tbody>
		<tr><td width="40%"><span class="cl-expend-btn-archive checkout" data-postid="'.$attach_id.'">+</span> Archives</td><td class="cl-value" width="60%">'.$archive_count.'</td></tr>
		<tr><td></td><td><div class="cl-archive-list">
		<div id="cl-archive-list-'.$attach_id.'" style="display:none" class="cl-archive-more checkout">'.$archive_list_popup.$archive_list_popup_more_btn.'</div><div id="cl-archive-list-more-'.$attach_id.'" style="display:none" class="cl-archive-more checkout">'.$archive_list_popup_more.'</div>
		</div></td></tr></tbody></table>';
	}
	
}
	
		
				// Version CLink ID(s)
		if($clink_versions = get_post_meta( $attach_id, 'wpclink_versions', true )){
			if(is_array($clink_versions)){
				if(count($clink_versions) > 3){
					$version_list_popup_more_btn = '<a id="version-load-more-'.$attach_id.'" class="version-load-more media" data-postid="'.$attach_id.'">More</a>';
					$version_list_popup_more.=wpclink_get_version_list_popup($attach_id,0,3);
				}
				$versions = '<tr><td width="40%"><span class="cl-expend-btn-version media" data-postid="'.$attach_id.'">+</span> Versions</td><td class="cl-value" width="60%">'.count($clink_versions).'</td></tr>';
				$version_see_more = '<div class="cl-version-list">		
				<div id="cl-version-list-'.$attach_id.'" style="display:none" class="cl-version-more media">'.wpclink_get_version_list_popup($attach_id,3,0,$version_list_popup_more_btn).'</div><div id="cl-version-list-more-'.$attach_id.'" style="display:none" class="cl-version-more media">'.$version_list_popup_more.'</div>
				</div>';
			}
		}
	
	
$label_publish = '';
$originate_at = '';
	
if($sync_origin = get_post_meta( $attach_id, 'wpclink_referent_post_uri', true )){
	
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
	
	$time_of_modification = $created_time;
	
	$originate_at = '';
	
	$linked_tr = '<table cellspacing="0" cellpadding="0" class="table-linked"><tbody><tr><td class="cl-strong cl-space-popup" width="40%">'.$label_publish.'</td>
		<td class="cl-value" width="60%">'.$created_time.' <a target="_blank" href="'.$orign_creation_url.'" class="external"></a></td>
		</tr></tbody></table>';
	
	
	
}
$provenance = '';
if(!empty($clink_versions)  || !empty($archive_link) || !empty($linked_tr)){
							$provenance = '<div class="cl-title">
							<h3>Provenance</h3>
							</div><table cellspacing="0" cellpadding="0"><tbody>'.$originate_at.'</tbody></table> <table  class="table-version"  cellspacing="0" cellpadding="0"><tbody>'.$versions.'</tbody></table>'.$version_see_more.$archive_tr.$linked_tr;
	
}
	
	
if($recorded_url = get_post_meta( $attach_id, 'wpclink_creation_ID', true )){
	$recorded_url_full = WPCLINK_ID_URL.'/#objects/'.$recorded_url;
}
// C2PA Exists?
if(wpclink_is_c2pa_data_exists($attach_id)){
$image_url = wp_get_attachment_image_src( $attach_id, 'full' );
ob_start();
wpclink_show_metadata_tree($image_url[0], $time_of_modification, $recorded_url_full,$attach_id);
$metadata_html = ob_get_contents();
ob_end_clean();
    
}else{
    $metadata_html = '<div class="cl-body"><table cellspacing="0" cellpadding="0" class="signer-info">
    <tbody><tr><td>Authenticated by </td><td class="cl-value sign-logo-wrapper" width="60%"><span class="sign-logo"></span> CLink Media</td></tr><tr><td>Authenticated at</td><td class="cl-value" width="60%">'.$time_of_modification.' <a  target="_blank" href="'.$recorded_url_full.'" class="external"></a></td></tr>
    </tbody>
    </table>'.$provenance.$right_tr.'</div>';
}
return '<ul class="cl-info-menu">
		<li class="cl-info" id="mode-'.$unique_id.'">'.wpclink_nightmode_button_return($unique_id).'<ul class="cl-subitem" id="cl-media-popup-'.$unique_id.'"><div class="cl-media-popup scrollbar style-1"><div class="cl-wrapper">'.$metadata_html.'</div></div></ul></li></ul>';
	
	
}
/**
 * CLink Night Mode Button
 * 
 * @param string $unique_id unique id for attribute id
 * @param boolean $re_arrange re-arrange of icons
 * 
 * @return string 
 */
function wpclink_nightmode_button($uniq_id = '',$re_arrange = false){
	
if($re_arrange){ ?><span class="cl-darkmode-icon" id="nightmode_btn-<?php echo $uniq_id; ?>" <?php if(wpclink_is_amp_active()){ ?> on="tap:mode-<?php echo $uniq_id; ?>.toggleClass(class='night'),nightmode_btn-<?php echo $uniq_id; ?>.toggleClass(class='night')" <?php } ?> role="button" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title/><path d="M20.21,15.32A8.56,8.56,0,1,1,11.29,3.5a.5.5,0,0,1,.51.28.49.49,0,0,1-.09.57A6.46,6.46,0,0,0,9.8,9a6.57,6.57,0,0,0,9.71,5.72.52.52,0,0,1,.58.07A.52.52,0,0,1,20.21,15.32Z" fill="#ffff"/></svg></span>
<span class="cl-info-icon" <?php if(wpclink_is_amp_active()){ ?> on="tap:cl-media-popup-<?php echo $uniq_id; ?>.toggleClass(class='show'),nightmode_btn-<?php echo $uniq_id; ?>.toggleClass(class='show')" <?php } ?> role="button" tabindex="0"><svg id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" style="background:0 0"><g><g><path style="fill:#fff" d="M437.02 74.98C388.667 26.629 324.38.0 256 0S123.333 26.629 74.98 74.98C26.629 123.333.0 187.62.0 256s26.629 132.667 74.98 181.02C123.333 485.371 187.62 512 256 512s132.667-26.629 181.02-74.98C485.371 388.667 512 324.38 512 256S485.371 123.333 437.02 74.98zM256 101c24.813.0 45 20.187 45 45s-20.187 45-45 45-45-20.187-45-45S231.187 101 256 101zm64 3e2H190v-30h21V251h-20v-30h110v150h19v30z"/></g></g><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/></svg></span>
<?php }else{ ?>
<span class="cl-info-icon" <?php if(wpclink_is_amp_active()){ ?> on="tap:cl-media-popup-<?php echo $uniq_id; ?>.toggleClass(class='show'),nightmode_btn-<?php echo $uniq_id; ?>.toggleClass(class='show')" <?php } ?> role="button" tabindex="0"><svg id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" style="background:0 0"><g><g><path style="fill:#fff" d="M437.02 74.98C388.667 26.629 324.38.0 256 0S123.333 26.629 74.98 74.98C26.629 123.333.0 187.62.0 256s26.629 132.667 74.98 181.02C123.333 485.371 187.62 512 256 512s132.667-26.629 181.02-74.98C485.371 388.667 512 324.38 512 256S485.371 123.333 437.02 74.98zM256 101c24.813.0 45 20.187 45 45s-20.187 45-45 45-45-20.187-45-45S231.187 101 256 101zm64 3e2H190v-30h21V251h-20v-30h110v150h19v30z"/></g></g><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/></svg></span>
<span class="cl-darkmode-icon" id="nightmode_btn-<?php echo $uniq_id; ?>" <?php if(wpclink_is_amp_active()){ ?> on="tap:mode-<?php echo $uniq_id; ?>.toggleClass(class='night'),nightmode_btn-<?php echo $uniq_id; ?>.toggleClass(class='night')" <?php } ?> role="button" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title/><path d="M20.21,15.32A8.56,8.56,0,1,1,11.29,3.5a.5.5,0,0,1,.51.28.49.49,0,0,1-.09.57A6.46,6.46,0,0,0,9.8,9a6.57,6.57,0,0,0,9.71,5.72.52.52,0,0,1,.58.07A.52.52,0,0,1,20.21,15.32Z" fill="#ffff"/></svg></span>
<?php }
}
/**
 * CLink Night Mode Button Return
 * 
 * @param string $unique_id unique id for attribute id
 * @param boolean $re_arrange re-arrange of icons
 * 
 * @return string 
 */
function wpclink_nightmode_button_return($uniq_id = '',$re_arrange = false){
	ob_start(); 
	wpclink_nightmode_button($uniq_id, $re_arrange);
	$output = ob_get_contents(); 
	ob_end_clean();
	return $output;
}
/**
 * CLink Licensable Media Popup for Linked Post Contains Image
 * 
 * @param array $image_attributes image attributes
 * @param string $unique_id unique id for attribute id
 * 
 * @return string 
 */
function wpclink_licensable_media_popup_linked_post($image_attributes = array(), $unique_id = ''){
	
	if(empty($unique_id)) $unique_id = uniqid();
	
	$created_time = '';
	$orign_creation_url = '';
	$origin_right_holder = '';
	$origin_versions = '';
	$linked_tr = '';
	
	if(isset($image_attributes['created_timestamp'])){
		$created_time = $image_attributes['created_timestamp']; 
		
	}
	if(isset($image_attributes['orign_creation_url'])){
		$orign_creation_url = $image_attributes['orign_creation_url']; 
	}
	if(isset($image_attributes['origin_rights_holder_display_name'])){
		$origin_right_holder = $image_attributes['origin_rights_holder_display_name'];
	}
	if(isset($image_attributes['origin_versions']) and !empty($image_attributes['origin_versions'])){
		$origin_versions = $image_attributes['origin_versions'];
		
		$versions = '<tr><td width="40%">Versions</td><td class="cl-value" width="60%">'.$origin_versions.'</td></tr>';
	}
	if(isset($image_attributes['right_ID'])){
		$right_ID = $image_attributes['right_ID'];
		$right_tr = '<div class="cl-title">
<h3>Rights</h3>
</div>
<table cellspacing="0" cellpadding="0">
<tbody><tr>
<td class="cl-strong" width="40%">Declared at</td>
<td class="cl-value" width="60%">'.$created_time.' <a target="_blank" href="'.$right_ID.'" class="external"></a></td>
</tr>
</tbody>
</table>';
	}
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
	
	$linked_tr = '<tr>
		<td class="cl-strong cl-space-popup" width="40%">'.$label_publish.'</td>
		<td class="cl-value" width="60%">'.$created_time.' <a target="_blank" href="'.$orign_creation_url.'" class="external"></a></td>
		</tr>';
	
	
$originate_at = '';
	
	
$provenance = '';
if($origin_versions > 0 || !empty($originate_at) || !empty($linked_tr)){
							$provenance = '<div class="cl-title">
							<h3>Provenance</h3>
							</div><table cellspacing="0" cellpadding="0"><tbody>'.$originate_at.$versions.$linked_tr.'</tbody></table>';
}
	
if(isset($image_attributes['creation_ID_url'])){
	$creation_ID_url = $image_attributes['creation_ID_url'];
}
return '<ul class="cl-info-menu">
<li class="cl-info" id="mode-'.$unique_id.'">
'.wpclink_nightmode_button_return($unique_id).'
<ul class="cl-subitem" id="cl-media-popup-'.$unique_id.'">
<div  class="cl-media-popup scrollbar style-1">
<div class="cl-wrapper">
<div  class="cl-body">
<table cellspacing="0" cellpadding="0" class="signer-info">
<tbody><tr><td>Recorded by </td><td class="cl-value sign-logo-wrapper" width="60%"><span class="sign-logo"></span> CLink Media</td></tr><tr><td>Recorded at</td><td class="cl-value" width="60%">'.$created_time.' <a target="_blank" href="'.$creation_ID_url.'" class="external"></a></td></tr>
</tbody>
</table>'.$provenance.'
'.$right_tr.'</div>
</div>
</div>					
</ul>
</li>
</ul>';
}
/**
 * CLink C2PA Data Exists
 * 
 * @param integer $media_id attachment id
 * 
 * @return boolean
 */
function wpclink_is_c2pa_data_exists($attach_id = 0){
     
    if($attach_id == 0) return false;
    
    $image_url = wp_get_attachment_image_src( $attach_id, 'full' );
    
    $full_metadata = wpclink_exif_full_metadata($image_url[0]);
    
    // Some of data to verify
    if(isset($full_metadata[0]['C2paThumbnailClaimJpegData']) || 
       isset($full_metadata[0]['C2paThumbnailIngredientJpegData']) || 
       isset($full_metadata[0]['C2paThumbnailIngredient_1JpegData']) || 
       isset($full_metadata[0]['C2paThumbnailIngredient_2JpegData'])){		
        return true;
    }
    
    return false;
}