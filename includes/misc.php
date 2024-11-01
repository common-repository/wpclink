<?php
/**
 * CLink Miscellaneous Functions 
 *
 * CLink miscellaneous and links functions
 *
 * @package CLink
 * @subpackage System
 */
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Linked Site Address and Pass
 * 
 * @return mixed url | NULL
 */
function wpclink_get_url_referent_site_selected(){
	$get_primary_site = wpclink_get_option('primary_linked_site');
	
	if($get_primary_site == 'super'){
		$cl_saved_option = wpclink_get_option( 'preferences_general' );
		$site_url_link = $cl_saved_option['sync_site'];
		return $site_url_link;
		
	}elseif($get_primary_site < 0){
		
		global $wpdb;
		$new_primary_site = abs($get_primary_site);
		
		$results = wpclink_get_license_linked($new_primary_site);
		$link_url = $results['site_url'];
		
		return $link_url;
	}elseif($get_primary_site == 'clink_group'){
		
		global $wpdb;
		$clink_groups_invited = $wpdb->prefix . 'clink_groups_invited';
		
		$group_invite = wpclink_get_option('wpclink_group_invite_link');
		
		if ( $group_invite = wpclink_get_option('wpclink_group_invite_link') ) {
			// Invite Link
			$invited_link = wpclink_get_option('wpclink_group_invite_link');
			
					
					$request_query = array();
		
					$mywebsite = get_bloginfo('url').'/';
					$mywebsite_title = get_bloginfo('name');
					
					// Prepare
					$request_query['cl_group_site'] = urlencode($mywebsite);
					$request_query['cl_site_name'] = urlencode($mywebsite_title);
					$request_query['cl_group_action'] = 'connect';
					
					// Finally Build Query
					$build_query = build_query( $request_query );
					
					$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
					$xml=file_get_contents($invited_link.'&'.$build_query,false,$context);
					
					$xml = simplexml_load_string($xml);
					
					$link_url = (string)$xml->channel->access->site_address;
					
					return $link_url;
		}
		
	}else{
	
	}
}
// Register clink linked site address and pass function
add_filter('cl_site_link','wpclink_get_url_referent_site_selected');
/**
 * CLink Care Authorization for Reuse Popup
 * 
 */
function wpclink_care_reauth(){
	if(isset($_GET['care_reauth'])){
		
		if($_GET['token'] == $_COOKIE['care_authorize']){
			
			$full_name = urldecode($_GET['name']);
			$site_url = urldecode($_GET['url']);
			
			echo "<html><head><title>Verifying Credentials</title></head><body style='margin:0; padding:0; background-color:#F5F5F5;'><div style='width:100%; height:100%; position:absolute; padding:20% 0;text-align: center;box-sizing: border-box; text-align:center;font-family: Arial; font-size:15px;'><p style='padding:0; margin:0;'> <div class='lds-css ng-scope'><div style='width:100%;height:100%' class='lds-rolling'><div></div></div><style type='text/css'>@keyframes lds-rolling{0%{-webkit-transform:translate(-50%, -50%) rotate(0deg);transform:translate(-50%, -50%) rotate(0deg)}100%{-webkit-transform:translate(-50%, -50%) rotate(360deg);transform:translate(-50%, -50%) rotate(360deg)}}@-webkit-keyframes lds-rolling{0%{-webkit-transform:translate(-50%, -50%) rotate(0deg);transform:translate(-50%, -50%) rotate(0deg)}100%{-webkit-transform:translate(-50%, -50%) rotate(360deg);transform:translate(-50%, -50%) rotate(360deg)}}.lds-rolling{position:relative}.lds-rolling div, .lds-rolling div:after{position:absolute;width:160px;height:160px;border:20px solid #fcb711;border-top-color:transparent;border-radius:50%}.lds-rolling div{-webkit-animation:lds-rolling 1s linear infinite;animation:lds-rolling 1s linear infinite;top:100px;left:100px}.lds-rolling div:after{-webkit-transform:rotate(90deg);transform:rotate(90deg)}.lds-rolling{margin:7px auto; width:32px !important;height:32px !important;-webkit-transform:translate(-16px, -16px) scale(0.16) translate(16px, 16px);transform:translate(-16px, -16px) scale(0.16) translate(16px, 16px)}</style></div> Verifying Credentials</p></div></div></body></html>";
						
			// TEMP DATA
			setcookie( 'auto_fill_popup', $site_url.','.$full_name, time()+3600, '/', COOKIE_DOMAIN );
		}else{
			
		}
		
		 
		 die();
		 
	}
}
// Register care authorization on redirect template
add_action('template_redirect','wpclink_care_reauth');
/**
 * CLink Donate Button
 *
 * Render Donate Button
 */
function wpclink_admin_page_donate(){
	
	$plugin_data = wpclink_get_plugin_data();
	echo '<div id="wpclink_admin_bottom"><p class="footer-version">wpCLink | Version: '.$plugin_data['Version'].'</p><p>Would you like to support the advancement of this plugin?<a class="donate-style" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal-crm@clink.media&lc=US&item_name=Support%20for%20Project%20CLink&no_note=0&cn=&currency_code=USD&bn=PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">Yes</a></p></div>';
}
add_action('wpclink_after_admin_page','wpclink_admin_page_donate');
/**
 * Get License Templates
 *
 * @param int post_id post id
 * @return string template text
 */
function wpclink_get_license_template($post_id = 0, $type = false, $license_type = 'personal'){
	
	
	
	if($type != false){
		$post_type = $type;
	}else{
		$post_type = get_post_type($post_id);
	}
	
	if($post_type == "post"){
		$template_data = file_get_contents(dirname(WPCLINK_MAIN_FILE).'/includes/license-template/template-post.txt');
	}else if($post_type == "page"){
		$template_data = file_get_contents(dirname(WPCLINK_MAIN_FILE).'/includes/license-template/template-post.txt');
	}else if($post_type == "attachment"){
        
        if( $license_type == 'marketplace'){
            
        $template_data = file_get_contents(dirname(WPCLINK_MAIN_FILE).'/includes/license-template/template-marketplace.txt');
            
            
        }else{
		$template_data = file_get_contents(dirname(WPCLINK_MAIN_FILE).'/includes/license-template/template-attachment.txt');
            
        }
	}else{
		$template_data = file_get_contents(dirname(WPCLINK_MAIN_FILE).'/includes/license-template/template-post.txt');
	}
	
	return nl2br($template_data);
}
/**
 * CLink Debug 
 */
function wpclink_debug_log($data){
	
	if($debug_enable = wpclink_get_option('debug_enable')){
		if(WPCLINK_DEBUG == true){
			// Filename
			$clink_debug_filename = wpclink_get_option('debug_filename');
			// Timestamp
			$data=date("Y-m-d h:i:sa").' '.$data;
			file_put_contents(dirname(WPCLINK_MAIN_FILE).'/log/'.$clink_debug_filename, $data.PHP_EOL,FILE_APPEND);
		}
	}
	
}
/**
 * Array sort by column
 * 
 * @param array $arr array of data
 * @param array $col array of data
 * @param integer $dir sort order
 * 
 * @return array
 */
function wpclink_array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }
    array_multisort($sort_col, $dir, $arr);
}
/**
 * Full URL to filename only
 * 
 * @param string $file_url File URL
 * 
 * @return string
 */
function wpclink_onlyfilename($file_url = false){
		
		$file = $file_url;
		$info = pathinfo($file);
		$file_name =  basename($file,'.'.$info['extension']);
		
		return $file_name; 
}
/**
 * Post thumbnail generator
 * 
 * @param integer $post_id  post to generate thumbnail
 * @param string $image_url_add  image url to add the thumbnail
 * 
 * @return integer post id
 */
function wpclink_post_thumbnail_generator($post_id = 0, $image_url_add = ''){
// Add Featured Image to Post
$image_url       = $image_url_add;
$image_name       = basename($image_url);
$upload_dir       = wp_upload_dir(); // Set upload folder
$image_data       = file_get_contents($image_url); // Get image data
$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
$filename         = basename( $unique_file_name ); // Create image file name
// Check folder permission and define file location
if( wp_mkdir_p( $upload_dir['path'] ) ) {
    $file = $upload_dir['path'] . '/' . $filename;
} else {
    $file = $upload_dir['basedir'] . '/' . $filename;
}
// Create the image  file on the server
file_put_contents( $file, $image_data );
// Check image file type
$wp_filetype = wp_check_filetype( $filename, null );
// Set attachment data
$attachment = array(
    'post_mime_type' => $wp_filetype['type'],
    'post_title'     => sanitize_file_name( $filename ),
    'post_content'   => '',
    'post_status'    => 'inherit'
);
// Create the attachment
$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
// Include image.php
require_once(ABSPATH . 'wp-admin/includes/image.php');
// Define attachment metadata
$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
// Assign metadata to attachment
wp_update_attachment_metadata( $attach_id, $attach_data );
// And finally assign featured image to post
return set_post_thumbnail( $post_id, $attach_id );	
	
}
/**
 * Post thumbnail generator
 * 
 * @param integer $post_id  post to generate thumbnail
 * @param string $image_url_add  image url to add the thumbnail
 * 
 * @return integer post id
 */
function wpclink_attachment_generator( $image_url_add = '', $data_arr = array()){
// Add Featured Image to Post
// Error Message
$error_exif = array(
			'complete' => 'failed',
			'status' => '',
			'code' => 'EXIF',
			'error_type' => 'exiftool',
			'error_headline' => __( 'Crunching Error', 'cl_text' ),
			'error_text' => __( 'Please try again later! <br> If the problem persist please contact support.', 'cl_text' ),
			'clink_error_status' => '1104',
			'clink_internal_error_code' => '',
			'clink_internal_error_location' => '',
			'message' => 'ExifTool error',
			'data' => array()
		);
	
// Fetch Image Status
wpclink_update_option('wpclink_loader_status_linked','fetch_image');
$image_url       = $image_url_add;
$image_name       = basename($image_url);
$upload_dir       = wp_upload_dir(); // Set upload folder
$image_data       = file_get_contents($image_url); // Get image data
$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
$filename         = basename( $unique_file_name ); // Create image file name
// Check folder permission and define file location
if( wp_mkdir_p( $upload_dir['path'] ) ) {
    $file = $upload_dir['path'] . '/' . $filename;
} else {
    $file = $upload_dir['basedir'] . '/' . $filename;
}
// Create the image  file on the server
file_put_contents( $file, $image_data );
// Check image file type
$wp_filetype = wp_check_filetype( $filename, null );
// Set attachment data
$attachment = array(
	'guid'           => $upload_dir['url'] . '/' .  $filename, 
    'post_mime_type' => $wp_filetype['type'],
    'post_title'     => $data_arr['title'],
    'post_content'   => '',
	'post_excerpt'   => $data_arr['excerpt'],
    'post_status'    => 'inherit'
);
// Fetch Image Status
wpclink_update_option('wpclink_loader_status_linked','crunching');
	
// Create the attachment
$attach_id = wp_insert_attachment( $attachment, $file );
// Include image.php
	
	if ( ! function_exists( 'wp_crop_image' ) ) {
    include( ABSPATH . 'wp-admin/includes/image.php' );
}
wpclink_debug_log('SAVE:'. $attach_id.":".$file);
	
apply_filters('wp_handle_upload', array('file' => $file, 'url' => $image_url, 'type' => $wp_filetype['type']), 'upload');
	
	
wpclink_debug_log('Generating Metadata:'. $attach_id.':'.$file);
// Define attachment metadata
	
try{
	
	// Timout in 60s
	set_time_limit(60);
	
	// Crunching
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	
}catch (Exception $e){
		
	$error_exif['message'] = $e->getMessage();
	wpclink_update_option(  'wpclink_loader_status_linked', 'error' );
	wpclink_update_option(  'wpclink_loader_linked_error_data', $error_exif );
	
	if( wp_delete_attachment( $attach_id, true )) {
			// Delete
	}
	return false;
		
		
}
wpclink_debug_log('Updating Metadata:'. $attach_id.':'.$attach_data);
// Assign metadata to attachment
wp_update_attachment_metadata( $attach_id, $attach_data );
wpclink_debug_log('Updated Metadata:');
	
return $attach_id;
	
}
/**
 * CLink Check If Referent Content is Updated
 * 
 * @param string $old_time old time of the post
 * @param string $new_time new time of the post
 * 
 * @return boolean
 */
function wpclink_check_update($old_time = false, $new_time = false){
// Timestamp
$ts_old = strtotime($old_time); 
$ts_new = strtotime($new_time);
if($ts_new > $ts_old)
{
	return true;
}
else
{
	return false;
}
}
/**
 * CLink Formate the timestamp
 * 
 * @param string $now key of time
 * @param string $datetime formate of time
 * @param boolean $full show full formate 
 * 
 * @return string formated time
 */
function wpclink_time_ago($now, $datetime, $full = false) {
    $now = new DateTime($now);
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
/**
 * CLink Site Selector Dropdown
 * 
 */
function wpclink_more_site_func(){
	
	global $wpdb;
	
	// CLINK  TABLE
	$agreements = wpclink_sort_site_url_referent();
	
	$get_primary_site = wpclink_get_option('primary_linked_site');
	
	$active_license = wpclink_get_license_linked(abs($get_primary_site));
	$active_site_url = $active_license['site_url'];
	
	foreach($agreements as $single_agg){
		
		if($single_agg['site_url'] == $active_site_url){
			$selected = 'selected="selected"';
		}else{
			$selected = '';
		}
		
		echo '<option value="-'.$single_agg['license_id'].'" '.$selected.'>'.$single_agg['site_url'].'</option>';
		
		
	}
		
}
// Register Site Selector Dropdown
add_action('cl_more_sites_link','wpclink_more_site_func');
/**
 * Create Agreement Link Token
 * 
 * @param string $token token
 * 
 * @return string
 */
function wpclink_get_token_url($token){
	
	$link = get_bloginfo('url').'/?s_agree=1&token='.$token;
	return $link;	
}
/**
 * CLink Language Code for CLink.ID
 * 
 * @param string $name language code
 * 
 * @return string langague name
 */
function wpclink_language_code_to_display($name){
    $languageCodes = array(
    "af" => "Afrikaans",
    "ar" => "Arabic",
    "ary" => "Moroccan Arabic",
    "as" => "Assamese",
    "az" => "Azerbaijani",
    "azb" => "South Azerbaijani",
    "bel" => "Belarusian",
    "bg-BG" => "Bulgarian",
    "bn-BD" => "Bengali",
    "bo" => "Tibetan",
    "bs-BA" => "Bosnian",
    "ca" => "Catalan",
    "ceb" => "Cebuano",
    "ckb" => "Kurdish (Sorani)",
    "cs-CZ" => "Chech",
    "cy" => "Welsh",
    "da" => "Danish",
    "de" => "German",
	"de-CH" => "German (Switzerland)",
	"de-DE" => "German (Germany)",
    "dzo" => "Dzongkha",
    "el" => "Greek",
    "en" => "English",
	"en-AU" => "English (Australia)",
	"en-CA" => "English (Canada)",
	"en-GB" => "English (UK)",
	"en-NZ" => "English (New Zealand)",
	"en-US" => "English (United States)",
	"en-ZA" => "English (South Africa)",									
    "eo" => "Esperanto",
    "es" => "Spanish",
	"es-AR" => "Spanish (Argentina)",
	"es-CL" => "Spanish (Chile)",
	"es-CO" => "Spanish (Colombia)",
	"es-CR" => "Spanish (Costa Rice)",
	"es-GT" => "Spanish (Guatemala)",
	"es-MX" => "Spanish (Mexico)",
	"es-VE" => "Spanish (Venezuela)",
    "et" => "Estonian",
    "eu" => "Basque",
    "fa-IR" => "Persian",
    "fi" => "Finnish",
    "fr" => "French",
	"fr-BE" => "French (Belgium)",
	"fr-CA" => "French (Canada)",
	"fr-FR" => "French (France)",
    "fur" => "Friulian",
    "gd" => "Scottish Gaelic",
    "gl-ES" => "Galician",
    "gu" => "Gujarati",
    "haz" => "Hazaragi",
    "he-IL" => "Hebrew",
    "hi-IN" => "Hindi",
    "hr" => "Croatian",
    "hu" => "Hungarian",
    "hy" => "Armenian",
    "id-ID" => "Indonesian",
    "is-IS" => "Icelandic",
    "it-IT" => "Italian",
    "ja" => "Japanese",
    "jv-IV" => "Javanese",
    "ka-GE" => "Georgian",
    "kab" => "Kabyle",
    "kk" => "Kazakh",
    "km" => "Khmer",
    "ko-KR" => "Korean",
    "lo" => "Lao",
    "lt-LT" => "Lithuanian",
    "lv" => "Latvian",
    "mk-MK" => "Macedonian",
    "ml-IN" => "Malayalam",
    "mn" => "Mongolian",
    "mr" => "Marathi",
    "ms-MY" => "Malay",
    "my-MM" => "Myanmar (Burmese)",
    "nb-NO" => "Norwegian (Bokmål)",
    "ne-NP" => "Nepali",
    "nl" => "Dutch",
	"nl-BE" => "Dutch (Belgium)",
	"nl-NL" => "Dutch (Netherlands)",
    "nn-NO" => "Norwegian (Nynorsk)",
    "oci" => "Occitan",
    "pa-IN" => "Panjabi",
    "pl-PL" => "Polish",
    "ps" => "Pashto",
    "pt" => "Portuguese",
	"pt-BR" => "Portuguese (Brazil)",
	"pt-PT" => "Portuguese (Portugal)",
    "rhg" => "Rohingya",
    "ro-RO" => "Romanian",
    "ru-RU" => "Russian",
    "sah" => "Sakha ",
    "si-LK" => "Sinhala",
    "sk-SK" => "Slovak",
    "sl-SI" => "Slovenian",
    "sg" => "Albanian",
    "sr-RS" => "Serbian",
    "sv-SE" => "Swedish",
    "szl" => "Silesian",
    "ta-IN" => "Tamil",
    "nah" => "Tahitian",
    "te" => "Telugu",
    "th" => "Thai",
    "tl" => "Tagalog",
    "tr-TR" => "Turkish",
    "ts" => "Tsonga",
    "tt" => "Tatar",
    "ug-CN" => "Uighur",
    "uk" => "Ukrainian",
    "ur" => "Urdu",
    "uz-UZ" => "Uzbek",
    "vi" => "Vietnamese",
    "zh-CN" => "Chinese (China)",
    "zh-HK" => "Chinese (Hong Kong)",
    "zh_TW" => "Chinese (Taiwan)"
    );
    return $languageCodes[$name];
}
function wpclink_is_reuse_guid(){
	  $options = wpclink_get_option( 'preferences_general' );
	  $licensor_url = $options['licensor_url'];
	
	  if($licensor_url == 'party_object_id'){
		  return false;
	  }else if($licensor_url == 'reuse_guid'){
		  return true;
		  
	  }
	return false;
}
function wpclink_reuse_guid_url($attachment_id = 0){
		// Reuse GUID
		$media_license_url = get_bloginfo('url');
		$media_license_url = add_query_arg( 'clink_media_license', '', $media_license_url);
		$reuse_GUID = add_query_arg( 'id', $attachment_id, $media_license_url);
	
	return $reuse_GUID;
}
/**
 * CLink Mode Add Class on Admin Body
 * 
 * @param string $classes default class
 * 
 * @return string
 */
function wpclink_admin_body_class( $classes ) {
	
  $options = wpclink_get_option( 'preferences_general' );
  if ( is_admin()) {
	  $classes .= 'cl_'.$options['cl_mode'].'_mode';
  }
	return $classes;
}
// Register clink mode class
add_filter( 'admin_body_class', 'wpclink_admin_body_class' ); 
/**
 * CLink User Level Add Class on Admin Body
 * 
 * @param string $classes  default class
 * 
 * @return string
 */
function wpclink_do_class_user_role_body( $classes ) {
	
	if(current_user_can('editor') and !current_user_can('administrator')){
		$classes.=' editor';
	}elseif(current_user_can('administrator')){
		$classes.=' admin';
	}
	
	return $classes;
	
}
// Register clink user level class
add_filter( 'admin_body_class', 'wpclink_do_class_user_role_body' ); 
/**
 * CLink Party and Creator Add Class on Admin Body
 * 
 * @param string $classes default class
 * 
 * @return string
 */
function wpclink_admin_body_class_users( $classes ) {
	
	
	$creator_array = wpclink_get_option('authorized_creators');
	
	
	$current_user_id = get_current_user_id();
	// Party
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	$right_holder = wpclink_get_option('rights_holder');
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
	if ( is_admin()) {
	
	if($current_user_id == $right_holder_id){
	
		 $classes .= ' right_holder_creator';
	
	}else if(wpclink_user_has_creator_list($current_user_id)){
		// Creator
		 $classes .= ' creator';
	}else if($current_user_id == $clink_party_id){
		// Party
		 $classes .= ' party';
	}
	
	}
	  
	return $classes;
}
// Register clink party and creator on admin body
add_filter( 'admin_body_class', 'wpclink_admin_body_class_users' ); 
/**
 * CLink Standard Email Template
 * 
 * @param string $body_text body text of  email template
 *
 */
function wpclink_standard_email_template($body_text){
	
	$html = '<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Simple Transactional Email</title>
    <style>
@media only screen and (max-width: 620px) {
  table[class=body] h1 {
    font-size: 28px !important;
    margin-bottom: 10px !important;
  }
  table[class=body] p,
table[class=body] ul,
table[class=body] ol,
table[class=body] td,
table[class=body] span,
table[class=body] a {
    font-size: 16px !important;
  }
  table[class=body] .wrapper,
table[class=body] .article {
    padding: 10px !important;
  }
  table[class=body] .content {
    padding: 0 !important;
  }
  table[class=body] .container {
    padding: 0 !important;
    width: 100% !important;
  }
  table[class=body] .main {
    border-left-width: 0 !important;
    border-radius: 0 !important;
    border-right-width: 0 !important;
  }
  table[class=body] .btn table {
    width: 100% !important;
  }
  table[class=body] .btn a {
    width: 100% !important;
  }
  table[class=body] .img-responsive {
    height: auto !important;
    max-width: 100% !important;
    width: auto !important;
  }
}
@media all {
  .ExternalClass {
    width: 100%;
  }
  .ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
    line-height: 100%;
  }
  .apple-link a {
    color: inherit !important;
    font-family: inherit !important;
    font-size: inherit !important;
    font-weight: inherit !important;
    line-height: inherit !important;
    text-decoration: none !important;
  }
  .btn-primary table td:hover {
    background-color: #34495e !important;
  }
  .btn-primary a:hover {
    background-color: #34495e !important;
    border-color: #34495e !important;
  }
}
</style>
  </head>
  <body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
  <center><img src="https://care.clink.media/wp-content/uploads/2017/10/Clink-Logo-259x259.png" width="128" height="128" /></center>
    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" width="100%" bgcolor="#f6f6f6">
      <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top"></td>
        <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; Margin: 0 auto;" width="580" valign="top">
          <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">
            <!-- START CENTERED WHITE CONTAINER -->
            <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">This is preheader text. Some clients will show this text as a preview.</span>
            <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #ffffff; border-radius: 3px; width: 100%;" width="100%">
              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;" valign="top">
                  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                    <tr>
                      <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">';
					  
					  $html.=$body_text;
					  
					  $html.= '
                        </td>
                    </tr>
                  </table>
                </td>
              </tr>
            <!-- END MAIN CONTENT AREA -->
            </table>
            <!-- START FOOTER -->
            <div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                <tr>
                  <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #999999; font-size: 12px; text-align: center;" valign="top" align="center">
                    <span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;">Tools for Rightful for Digital Content</span><br> 
                  </td>
                </tr>
                <tr>
                  
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->
          <!-- END CENTERED WHITE CONTAINER -->
          </div>
        </td>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">&nbsp;</td>
      </tr>
    </table>
  </body>
</html>';
	return $html;
}
/**
 * CLink Update User Information to Care 
 * 
 * @param integer $user_id user id
 * 
 */
function wpclink_update_extra_profile_fields($user_id) {
   if ( current_user_can('edit_user',$user_id) )
	   
	   
	   
	   if($_POST['submit_to_clinkid'] == '1'){
		   update_user_meta($user_id, 'submit_to_clinkid', $_POST['submit_to_clinkid']);
	   	
	   }
	
		if($_POST['make_version'] == '1'){
		   update_user_meta($user_id, 'make_version', $_POST['make_version']);
	   	
	    }else{
			delete_user_meta($user_id,'make_version');
		}
}
// Register update user infomation to care function
add_action('personal_options_update', 'wpclink_update_extra_profile_fields');
add_action('edit_user_profile_update', 'wpclink_update_extra_profile_fields');
/**
 * CLink Covert Date to ISO 8601 formate
 * 
 * @param string $time time
 * 
 */
function wpclink_convert_date_to_iso($time = 0){
	
	// Create Date
	$time = date_create($time);
	
	$format = date_format($time,'Y-m-d');
	$format .= 'T';
	$format .= date_format($time,'H:i:s');
	$format .= 'Z';
	
	return $format;
	
}
/**
 * Get Last Archive URL
 *
 * @param int post_id post id
 * @return string last URL
 */
function wpclink_get_last_archive($post_id = 0){
	
	if(empty($post_id)) return '';
	$archive_link = apply_filters( 'wpclink_get_last_archive_url', '', $post_id);
	return $archive_link;
}
/**
 * Get Archive count
 *
 * @param int post_id post id
 * @return string last URL
 */
function wpclink_archive_count_by_post_id($post_id = 0){
	
	if(empty($post_id)) return '';
	$count = apply_filters( 'wpclink_get_archive_count_by_post_id', '', $post_id);
	return $count;
}
/**
 * CLink Get Versions for Popup
 * 
 */
function wpclink_get_version_list_popup($post_id = 0, $limit = 0, $offset = 0,$version_btn = ''){
			
				if($has_version = get_post_meta($post_id,'wpclink_versions',true)){
					$versions = $has_version;
					$versions = array_reverse($versions);
					
					$label = 'Version';
					if(count($versions) > 1) $label = 'Versions';
				
					$html = '<table width="100%" class="table-version" border="0"><tbody>';
					
					$version_time = get_post_meta($post_id,'wpclink_versions_time',true);
					
					if($limit > 0){
						$versions = array_slice($versions, 0, $limit, true);
					}else if($offset > 0){
						$versions = array_slice($versions, $offset);
					}
										
					foreach($versions as $single_ver){
						
						if(!empty($version_time[$single_ver])){
							$archive_icon = apply_filters('wpclink_archive_version_button','',$post_id,$single_ver);
							$html .= '<tr><td width="40%"></td><td class="cl-value"><a href='.WPCLINK_ID_URL.'/#objects/'.$single_ver.' target="_blank">'.$version_time[$single_ver].' </a>'.$archive_icon.' <a target="blank" href="'.WPCLINK_ID_URL.'/#objects/'.$single_ver.'"  class="external"  target="_blank"></a></td></tr>';
						}
					}
					
					
					
					$html.='<tr><td></td><td>'.$version_btn.'</td></tr></tbody></table>';
					
					return $html;
				}else{
					return '';
				}
			
		
}
/**
 * CLink Get List of Versions
 * 
 * @return string json encoded array
 */
function wpclink_get_version_list_new($post_id = 0){
			
				if($versions = get_post_meta($post_id,'wpclink_versions',true)){
					$versions = array_reverse($versions);
					$version_time = get_post_meta($post_id,'wpclink_versions_time',true);
					
				
					$version_array = array();
					
					foreach($versions as $single_ver){
						if(!empty($version_time[$single_ver])){
							$version_array[$version_time[$single_ver]] = WPCLINK_ID_URL.'/#objects/'.$single_ver;
						}
					}
					
					
					return json_encode($version_array);
					
				}else{
					return '';
				}
}