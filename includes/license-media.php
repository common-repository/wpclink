<?php
/**
 * CLink License Media
 *
 * CLink license media
 *
 * @package CLink
 * @subpackage System
 */
 
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Exif Read XMP C2PA
 * 
 * @param string image_path path of image 
 * 
 */
function wpclink_exif_read_xmp_metadata_c2pa_new( $image_path = ''){
	
	
	$exiftool_file = dirname (WPCLINK_MAIN_FILE) . '/vendor/phpexiftool/exiftool/exiftool';
	$output = shell_exec('perl '.$exiftool_file.' -struct -b -j '.$image_path);
	
	if(empty($output)) return false;
	
	$data = array();
	
	$metadata_array = json_decode($output, true);
	
	if($metadata_array == false) return false;
	
	foreach ($metadata_array as $metadata_single){
	
		if(!empty($metadata_single['Claim_generator'])){
			$data['Claim_generator'] = $metadata_single['Claim_generator'];
		}
		if(!empty($metadata_single['Item1']['temp_signing_time'])){
			$data['temp_signing_time'] = $metadata_single['Item1']['temp_signing_time'];
		}
		
	}
	
	
	return $data;
}
/**
 * CLink Display Showcase for Image
 *
 * @param integer attachment_id attachment id
 * @return string html
 */
function wpclink_display_image_showcase($attachment_id = 0){
	$image_url = wp_get_attachment_url($attachment_id);
	return '<img src="'.$image_url.'" class="img showcase" data-clink-media-license="1" />';
}
/**
 * CLink Display Showcase for all Medias
 *
 * @param integer attachment_id attachment id
 * @return string html
 */
function wpclink_display_media_showcase($attachment_id = 0){
	$type = get_post_mime_type($attachment_id);
	switch ($type) {
    case 'image/jpeg':
		return wpclink_display_image_showcase($attachment_id);
    case 'image/png':
    case 'image/gif':
    case 'video/mpeg':
    case 'video/mp4': 
    case 'video/quicktime':
    case 'text/csv':
    case 'text/plain': 
    case 'text/xml':
    default:
  }
}
/**
 * CLink Reuse Offer Popup
 * 
 * @param integer attachment_id attachment id
 */
function wpclink_do_popup_link_media($attachment_id = 0) {
	// If import mode only
	if(wpclink_import_mode()) return false;
	// if content is not in linked list
	if(wpclink_is_post_referent($attachment_id)){
		// OK
	}else{
		return false;
	}
	$token = uniqid();
	
	// Reuse for media
	$reuse_popup = '<div id="CLModal" class="cl_modal"><div class="cl_modal-content"><span class="cl_close">&times;</span><form id="cl_new_offer_clinkid" action="" style="display:block"><div id="cl_offer_response" style="display:block"><p class="style4" >'. __('The reuse of this content is enabled for WordPress powered sites using ','cl_text').'<a class="cl_hyperlink" target="_blank" href="https://wordpress.org/plugins/wpclink/">wpCLink</a> plugin.</p><p class="description style2">'.__('Please Log Into your WordPress Instance','cl_text').'<input id="cl_weburl" required="required" value="" autofocus="autofocus" placeholder="https://mydomain.com" type="url"><p class="style1">'.__('The content will be accessible at the Creations &rarr; Linked menu  in your WordPress instance after you accept the Terms and Conditions of the License.','cl_text').'</p><input type="hidden" id="cl_popup_id" value="'.$token.'" /><div class="quick-response"></div><input style="visibility:hidden" class="content_type" type="checkbox" name="content_type" checked="checked" value="single" /><input type="hidden" name="cl_auto_code" id="cl_auto_code" value="" /><div id="wait"></div><input id="cl_modal_submit" value="Get a license" type="submit"></div></form><input type="hidden" name="auto_content" id="auto_content" value="'.$attachment_id.'" /><p class="style3"><a target="_blank" href="https://licenses.clink.id/personal/0-9i/">'.__('License Terms and Conditions','cl_text').'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M9 3h8v8l-2-1V6.92l-5.6 5.59-1.41-1.41L14.08 5H10zm3 12v-3l2-2v7H3V6h8L9 8H5v7h7z"/></g></svg></a></p></div></div>';
	
	
	$wpclink_reuse_popup = apply_filters( 'wpclink_reuse_popup', $reuse_popup, $token, $attachment_id );
	
	// Print
	echo $wpclink_reuse_popup;
	
	
}
// Register clink reuse offer popup function
add_action( 'wpclink_media_footer', 'wpclink_do_popup_link_media',10,1 );
/**
 * CLink Load License
 * 
 */
function wpclink_load_license(){
	
$media_id = $_GET['id'];
	
if (is_numeric($media_id)) {
    if($media_id == 0){
			die('ERROR: 404 Not Found');
	}else{
	}
} else {
    die('ERROR: 400 Invalid Command');
}
// Style
$style_showcase = plugins_url('public/css/media-showcase.css?'.uniqid('_'),dirname(__FILE__));
$style_frontend = plugins_url('public/css/frontend.css?'.uniqid('_'),dirname(__FILE__));
$style_modal = plugins_url('public/css/modal.css?'.uniqid('_'),dirname(__FILE__));
	
$styles_list = array($style_showcase,
					 $style_frontend,
					 $style_modal);
$stylesheet = '';
foreach($styles_list  as $style_signle){
	$stylesheet .='<link href="'.$style_signle.'" rel="stylesheet">';	
}
	
// CLink ID
if($contentID = get_post_meta( $media_id, 'wpclink_creation_ID', true )){
	$content_url = WPCLINK_ID_URL.'/#objects/'.$contentID;		
}
	
$archive_tr = '';
// Created DAte
$get_post_date  = get_the_date('Y-m-d',$media_id);
$get_post_date .= 'T';
$get_post_date .= get_the_date('G:i:s',$media_id);
$get_post_date .= 'Z';
$created_time = $get_post_date;
	
// Get Last Archive if has
$archive_link = wpclink_get_last_archive($media_id);
	
if(!empty($archive_link)){
	
$thumbnail_src =  wp_get_attachment_image_src( $media_id, 'medium' );
	
$archive_count = wpclink_archive_count_by_post_id($media_id);
	
if (function_exists('wpclink_show_archive_list_popup')) {
	
	$archive_thumbnail = apply_filters('wpclink_archive_thumbnail',true,$media_id);
	
	$archive_list_popup = wpclink_show_archive_list_popup($media_id,3);
	if($archive_count > 3){
		$archive_list_popup_more_btn = '<a id="archive-load-more-'.$media_id.'" class="archive-load-more  checkout" data-postid="'.$media_id.'">More</a>';
		$archive_list_popup_more.=wpclink_show_archive_list_popup($media_id,0,3);
	}
}else{
	$archive_list_popup = '';
	$archive_list_popup_more = '';
	$archive_list_popup_more_btn = '';
}
	if($archive_thumbnail){
		$thumbnail_src =  wp_get_attachment_image_src( $media_id, 'medium' );
		$archive_tr = '<div class="cl-arhive-box"><a class="archive_thumbnail" target="_blank" href="'.$archive_link.'" style="background-image:url('.$thumbnail_src[0].')"><span class="archive-icon-big"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M13.65 2.88c3.93 2.01 5.48 6.84 3.47 10.77s-6.83 5.48-10.77 3.47c-1.87-.96-3.2-2.56-3.86-4.4l1.64-1.03c.45 1.57 1.52 2.95 3.08 3.76 3.01 1.54 6.69.35 8.23-2.66 1.55-3.01.36-6.69-2.65-8.24C9.78 3.01 6.1 4.2 4.56 7.21l1.88.97-4.95 3.08-.39-5.82 1.78.91C4.9 2.4 9.75.89 13.65 2.88zm-4.36 7.83C9.11 10.53 9 10.28 9 10c0-.07.03-.12.04-.19h-.01L10 5l.97 4.81L14 13l-4.5-2.12.02-.02c-.08-.04-.16-.09-.23-.15z"/></g></svg></a></span></div>';
	}else{
		$archive_tr = '<table class="archive-list-table table-archive"><tbody>
		<tr><td width="40%"><span class="cl-expend-btn-archive checkout" data-postid="'.$media_id.'">+</span> Archives</td><td class="cl-value" width="60%">'.$archive_count.'</td></tr>
		<tr><td></td><td><div class="cl-archive-list">
		<div id="cl-archive-list-'.$media_id.'" style="display:none" class="cl-archive-more checkout">'.$archive_list_popup.$archive_list_popup_more_btn.'</div><div id="cl-archive-list-more-'.$media_id.'" style="display:none" class="cl-archive-more checkout">'.$archive_list_popup_more.'</div>
		</div></td></tr></tbody></table>';
	}
	
}
// Version CLink ID(s)
if($clink_versions = get_post_meta( $media_id, 'wpclink_versions', true )){
	if(is_array($clink_versions)){
		
		
		if(count($clink_versions) > 3){
			$version_list_popup_more_btn = '<a id="version-load-more-'.$media_id.'" class="version-load-more checkout" data-postid="'.$media_id.'">More</a>';
			$version_list_popup_more.=wpclink_get_version_list_popup($media_id,0,3);
		}
		
		
		
		$versions = '<tr><td width="40%"><span class="cl-expend-btn-version checkout" data-postid="'.$media_id.'">+</span> Versions</td><td class="cl-value" width="60%">'.count($clink_versions).'</td></tr>';
		
		$version_see_more = '<div class="cl-version-list">		
		<div id="cl-version-list-'.$media_id.'" style="display:none" class="cl-version-more checkout">'.wpclink_get_version_list_popup($media_id,3,0,$version_list_popup_more_btn).'</div><div id="cl-version-list-more-'.$media_id.'" style="display:none" class="cl-version-more checkout">'.$version_list_popup_more.'</div>
		</div>';
	}
}
	
	
	
	
	
// Right Holder
if($rights_holder_id = get_post_meta( $media_id, 'wpclink_rights_holder_user_id', true )){
	// Display Name
	$creator_user_data = get_userdata($rights_holder_id);
	$creator_user_display = $creator_user_data->display_name;
}
// Right ID
if($wpclink_right_ID = get_post_meta( $media_id, 'wpclink_right_ID', true )){
$right_ID_url = WPCLINK_ID_URL.'/#objects/'.$wpclink_right_ID;
	
if($right_created_time = get_post_meta( $media_id, 'wpclink_right_created_time', true )){
	$created_time_right = $right_created_time;
}
if(!empty($created_time_right)){
	$right_tr = '<div class="cl-title">
	<h3>Rights</h3>
	</div>
	<table cellspacing="0" cellpadding="0"><tbody><tr><td class="cl-strong" width="40%">Declared at</td>
	<td class="cl-value" width="60%">'.$created_time_right.'<a href="'.$right_ID_url.'" class="external" target="_blank"></a></td>
	</tr><tr></tr></tbody></table>';
}
	
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
<?php echo $stylesheet; ?>
<?php wp_head(); ?>  
</head>
<body class="custom-background-image">
<div class="media-container">
<div id="media"><?php echo wpclink_display_media_showcase($media_id) ?></div>
<?php if(!empty($contentID)){ ?>
<div id="reuse" class="clink-imagebox"><ul class="cl-info-menu"><li class="cl-info" id="mode-60465a674f04c">
    
<?php if(wpclink_is_c2pa_data_exists($media_id)){  ?>
<span class="cl-info-icon" role="button" tabindex="0"><svg width="26px" height="26px" viewBox="0 0 26 26" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="L2-explorations" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="seal-white" transform="translate(2.000000, 2.000000)"><path d="M10.9988461,-0.774730682 L13.3955754,2.06128515 L16.8891924,0.802262531 L17.5413055,4.45773804 L21.1979192,5.11098928 L19.9377877,8.60338634 L22.7749121,11.0000907 L19.9377582,13.3956819 L21.1977961,16.8900959 L17.5413055,17.5433252 L16.8892034,21.1987392 L13.3946187,19.9397781 L10.9989368,22.7759013 L8.60325487,19.9397781 L5.10867021,21.1987392 L4.45654483,17.5431952 L0.801322295,16.8888511 L2.06021738,13.395588 L-0.774639986,11.0000141 L2.06120347,8.60343045 L0.801017596,5.10974441 L4.45763124,4.45763124 L5.10974441,0.801017596 L8.60334875,2.061174 L10.9988461,-0.774730682 Z" id="Fill-2" stroke-opacity="0.3" stroke="#000000" fill="#FFFFFF"></path><path d="M9.75,17 L12.25,17 L12.25,9.8 L9.75,9.8 L9.75,17 Z M9.75,7.4 L12.25,7.4 L12.25,5 L9.75,5 L9.75,7.4 Z" id="Fill-4" fill="#2C2C2C"></path></g></g></svg></span>
<?php }else{ ?>
    <span class="cl-info-icon" role="button" tabindex="0"><svg id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" style="background:0 0"><g><g><path style="fill:#fff" d="M437.02 74.98C388.667 26.629 324.38.0 256 0S123.333 26.629 74.98 74.98C26.629 123.333.0 187.62.0 256s26.629 132.667 74.98 181.02C123.333 485.371 187.62 512 256 512s132.667-26.629 181.02-74.98C485.371 388.667 512 324.38 512 256S485.371 123.333 437.02 74.98zM256 101c24.813.0 45 20.187 45 45s-20.187 45-45 45-45-20.187-45-45S231.187 101 256 101zm64 3e2H190v-30h21V251h-20v-30h110v150h19v30z"/></g></g><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/></svg></span>
<?php } ?>
    
<span class="cl-darkmode-icon" id="nightmode_btn-60465a674f04c" role="button" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title></title><path d="M20.21,15.32A8.56,8.56,0,1,1,11.29,3.5a.5.5,0,0,1,.51.28.49.49,0,0,1-.09.57A6.46,6.46,0,0,0,9.8,9a6.57,6.57,0,0,0,9.71,5.72.52.52,0,0,1,.58.07A.52.52,0,0,1,20.21,15.32Z" fill="#ffff"></path></svg></span>
<ul class="cl-subitem" id="cl-media-popup-60465a674f04c"><div class="cl-media-popup scrollbar style-1">
<div class="cl-wrapper">
<div class="cl-body">
    
<?php 
 if(wpclink_is_c2pa_data_exists($media_id)){ 
     $image_url = wp_get_attachment_image_src( $media_id, 'full' );
     echo wpclink_show_metadata_tree($image_url[0], $created_time, $content_url,$media_id);
}else{ ?>
   <table cellspacing="0" cellpadding="0" class="signer-info">
<tbody><tr><td>Authenticated by </td><td class="cl-value sign-logo-wrapper" width="60%"><span class="sign-logo"></span> CLink Media</td></tr><tr><td>Authenticated at</td><td class="cl-value" width="60%"><?php echo $created_time; ?><a href="<?php echo $content_url; ?>" class="external" target="_blank"></a></td></tr>
</tbody>
</table>
<?php if($origin_versions > 0 || !empty ($archive_link) || !empty($sync_origin) || !empty($clink_versions)){ ?>
<div class="cl-title">
<h3>Provenance</h3>
</div>
    <table class="table-version" cellspacing="0" cellpadding="0"><tbody><?php if(!empty($sync_origin)) { ?><tr><td class="cl-strong" width="40%">Published at</td>
<td class="cl-value" width="60%"><?php echo $created_time; ?> <a target="_blank" href="<?php echo $content_url; ?>" class="external"></a></td>
</tr><?php } ?> <?php echo $versions; ?></tbody></table><?php echo $version_see_more; ?><?php echo $archive_tr; ?><?php } ?><?php echo $right_tr; ?> 
<?php } ?>
</div>
</div>
</div>
</ul></li></ul><?php echo wpclink_media_reuse_button(); ?> </div> 
<?php } ?>
</div><?php do_action('wpclink_media_footer',$media_id); ?>
<?php wp_footer(); ?>  
</body>
</html><?php
}
/**
 * CLink Media License URL
 * 
 */
function wpclink_start_media_license(){
		if(isset($_GET['clink_media_license'])){
			wpclink_load_license();
			die();
		}
}
// Register generate xml url function on wordpress load
add_action('wp_loaded','wpclink_start_media_license');
