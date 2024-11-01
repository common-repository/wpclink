<?php
/**
 * CLink Meta Data
 *
 * CLink media IPTC metadata 
 *
 * @package CLink
 * @subpackage System
 */
// Direct Access Not Allowed
defined( 'ABSPATH' )or die( 'No script kiddies please!' );
/**
 * CLink Get Image URL
 *
 *
 */
function wpclink_get_image_URL( $attachment_id = 0 ) {
	// No Attachment ID
	if ( $attachment_id == 0 ) return false;
	$scaled_image = wp_get_attachment_url( $attachment_id );
	
	return $scaled_image;
	
}
/**
 * CLink Get All Image Sizes
 *
 */
function wpclink_get_all_image_sizes( $attachment_id = 0 ) {
	$image_urls = array();
	$upload_dir = wp_get_upload_dir();
	$image_data = wp_get_attachment_metadata( $attachment_id );
	$image_upload_dir = dirname( $image_data[ 'file' ] );
	$image_file = get_attached_file( $attachment_id, false );
	
	foreach ( $image_data[ 'sizes' ] as $key => $single_size ) {
			
		$image_urls[] = path_join( dirname( $image_file ), $single_size[ 'file' ] );
	}
	$original_image_path = wp_get_original_image_path( $attachment_id );
	$scaled_path = get_attached_file( $attachment_id );
	if ( $original_image_path == $scaled_path ) {
		$image_urls[] = $original_image_path;
	} else {
		$image_urls[] = $original_image_path;
		$image_urls[] = $scaled_path;
	}
	
	
		// Response Debug
	wpclink_debug_log( 'IMAGE URLS:' . print_r( $image_urls, true ));
	return $image_urls;
}
function wpclink__get_scaled_image_only( $attachment_id = 0 ) {
	$image_urls = array();
	
	$scaled_path = get_attached_file( $attachment_id );
	$image_urls[] = $scaled_path;
	
	// Response Debug
	wpclink_debug_log( 'IMAGE SCALED IMAGE:' . print_r( $image_urls, true ));
	
	return $image_urls;
}
/**
 * CLink Get IPTC Image Path.
 *
 * Get server path of IPTC.
 *
 *
 * @param int attachment_id ID of post.
 * @param string size Thumbnail size
 * @return string path of image.
 */
function wpclink_iptc_image_path( $attachment_id, $size = 'thumbnail' ) {
	$file = get_attached_file( $attachment_id, true );
	if ( empty( $size ) || $size === 'full' ) {
		// for the original size get_attached_file is fine
		return realpath( $file );
	}
	if ( !wp_attachment_is_image( $attachment_id ) ) {
		return false; // the id is not referring to a media
	}
	$info = image_get_intermediate_size( $attachment_id, $size );
	if ( !is_array( $info ) || !isset( $info[ 'file' ] ) ) {
		return false; // probably a bad size argument
	}
	return realpath( str_replace( wp_basename( $file ), $info[ 'file' ], $file ) );
}
/**
 * CLink Match Creator Names
 *
 * Update IPTC Image Data
 *
 * @param array iptc_metada_creator IPTC Creator Names
 * @param array creator_user Creator Names
 * @return boolen 
 */
function wpclink_match_creator_names( $iptc_metada_creator = array(), $creator_user = array(), $creator_display_name = array() ) {
	if ( !empty( $creator_display_name ) ) {
		$creator_user = array_merge( $creator_user, $creator_display_name );
	}
	// Match
	foreach ( $iptc_metada_creator as $name ) {
		// Firstname or Last Name not as Creator names
		if ( !in_array( strtolower( $name ), $creator_user ) ) {
			return false;
		}
	}
	return true;
}
/**
 * CLink Creator Can Register 
 *
 * Check the current user creator match to the image creator
 *
 * @param integer post_id post id
 * @return boolen 
 */
function wpclink_creator_can_register( $post_id = 0 ) {
	// Is media is linked?
	$linked_media = get_post_meta( $post_id, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_media ) ) {
		$linked_flag = true;
	} else {
		$linked_flag = false;
	}
	// Current User
	$current_user_id = get_current_user_id();
	// Creator
	$creator_user_info = get_userdata( $current_user_id );
	// Current Creator Names
	$current_creator_array = array(
		strtolower( $creator_user_info->first_name ),
		strtolower( $creator_user_info->last_name )
	);
	$display_name = $creator_user_info->display_name;
	$display_name_array = explode( ' ', $display_name );
	$display_name_array = array_map( 'strtolower', $display_name_array );
	// Image Path
	$attachment_url = wpclink_iptc_image_path( $post_id, 'full' );
	// Meta data Creator Names
	$creator_name_metadata = wpclink_get_image_metadata_value( $attachment_url, 'IPTC:By-line' );
	if ( $linked_flag ) {
		// Linked Content
		return true;
	} else {
		if ( !empty( $creator_name_metadata ) ) {
			$metadata_creator_names = explode( ' ', $creator_name_metadata );
			
			$creator_name_match = wpclink_match_creator_names(
					$metadata_creator_names,
					$current_creator_array,
					$display_name_array );
			
			/*
			 * Apply the Creator Name Match
			 *
			 * - 'creator_name_match' is the value being filtered. */
			$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
			if ( $creator_name_match_final ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
}
/**
 * CLink Get Image Date Time
 *
 *
 * @param int post_id ID of post.
 */
function wpclink_get_image_datetime($post_id = 0){
	
	if($post_id == 0) return false;
	
	$get_post_modified_date  = get_the_modified_time('Y-m-d',$post_id);
	$get_post_modified_date .= 'T';
	$get_post_modified_date .= get_the_modified_time('G:i:s',$post_id);
	$get_post_modified_date .= 'Z';
	
	if($media_metadata = get_post_meta($post_id,'_wp_attachment_metadata',true)){
		
		if(isset($media_metadata['image_meta']['created_timestamp'])){
			
			$created_timestamp = $media_metadata['image_meta']['created_timestamp'];
			
			if($created_timestamp != 0){
				$get_post_date = date('Y-m-d', $created_timestamp );
				$get_post_date .= 'T';
				$get_post_date .= date('G:i:s',$created_timestamp);
				$get_post_date .= 'Z';
			}else{
				$get_post_date  = get_the_date('Y-m-d',$post_id);
				$get_post_date .= 'T';
				$get_post_date .= get_the_date('G:i:s',$post_id);
				$get_post_date .= 'Z';
			}
		}else{
			
				$get_post_date  = get_the_date('Y-m-d',$post_id);
				$get_post_date .= 'T';
				$get_post_date .= get_the_date('G:i:s',$post_id);
				$get_post_date .= 'Z';
			
		}
	}
	
	$time_array = array('created' 	=> $get_post_date,
			    		'modified'	=> $get_post_modified_date);
	
	return $time_array;
	
}
/**
 * CLink Push Meta Data to Image
 *
 * Update Image Data
 *
 *
 * @param int post_id ID of post.
 * @param array data metadata array
 */
function wpclink_push_metadata($post_id = 0,$data = array()){
	
	if($data_array = get_post_meta($post_id,'wpclink_rest_images_metadata',true)){
		$data_array = array_merge($data_array,$data);
		$data_array = array_unique($data_array);
		update_post_meta($post_id,'wpclink_rest_images_metadata',$data_array);
		return true;
	}else{
		update_post_meta($post_id,'wpclink_rest_images_metadata',$data);
		return true;
	}
	
}
/**
 * CLink Update Image Meta Data
 *
 * Update Image Data
 * 
 *
 * @param int attachment_id ID of post.
 */
function wpclink_update_image_metadata( $attachment_id, $check_allow_registration = false, $nonces = '' ) {
	
	update_post_meta( $attachment_id, 'wpclink_loader_status', '0' );
	
	$error_exif = array(
			'complete' => 'failed',
			'status' => '',
			'code' => 'EXIF',
			'error_type' => 'exiftool',
			'error_headline' => __( 'ExifTool Error', 'cl_text' ),
			'error_text' => __( 'Please try again later! <br> If the problem persist please contact support.', 'cl_text' ),
			'clink_error_status' => '1104',
			'clink_internal_error_code' => '',
			'clink_internal_error_location' => '',
			'message' => 'ExifTool error',
			'data' => array()
		);
	// Only Jpeg
	$type_media = get_post_mime_type( $attachment_id );
	if ( $type_media == 'image/jpeg' || $type_media == 'image/jpg' ) {} else {
		return false;
	}
	
	$continue_to_image = get_post_meta( $attachment_id, 'wpclink_continue_to_image', true );
	
		
	/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
	
	
	if ( $continue_to_image_final == 1 ) {
		return false;
	}
	
	// Verify Nonces
	if(empty($nonces)){
		$wp_nonce = $_REQUEST[ 'attachments' ][ $attachment_id ][ 'wpclink_metadata_nonce' ];
	}else{
		$wp_nonce = $nonces;
	}
	
	if( ! wp_verify_nonce( $wp_nonce, 'wpclink_metadata_update') ){
		return false;
	}
	// Removed from Grid View
	if ( $_REQUEST[ 'action' ] == 'query-attachments' ) {
		return false;
	}
	
	
		
	// GUID
	$guid = get_the_guid($attachment_id);
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));  
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];
	// Is media is linked?
	$linked_media = get_post_meta( $attachment_id, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_media ) ) {
		$linked_flag = true;
	} else {
		$linked_flag = false;
	}
	// Media is in Referent
	if ( wpclink_check_license_by_post_id( $attachment_id ) > 0 ) return false;
	// Media Permission
	$media_permission = get_post_meta( $attachment_id, 'wpclink_programmatic_right_categories', true );
	$media_permission_array = explode( ",", $media_permission );
	// IPTC Fields and Data
	$iptc_metadata = array(
		'wpclink_image_metadata_headline',
		'wpclink_image_metadata_creator',
		'wpclink_image_metadata_description',
		'wpclink_image_metadata_keywords',
		'wpclink_image_metadata_subject_code',
		'wpclink_image_metadata_description_writer',
		'wpclink_image_metadata_date_created',
		'wpclink_image_metadata_intellectual_genre',
		'wpclink_image_metadata_title',
		'wpclink_image_metadata_job_id',
		'wpclink_image_metadata_instruction',
		'wpclink_image_metadata_credit',
		'wpclink_image_metadata_source',
		'wpclink_image_metadata_copyright_notice'
	);
	// Party
	$clink_party_id = wpclink_get_option( 'authorized_contact' );
	$clink_party_id = get_user_meta( $clink_party_id, 'wpclink_party_ID', true );
	// Image Meta Data
	$attachment_url = wpclink_iptc_image_path( $attachment_id, 'full' );
	$attachment_meta = wp_get_attachment_metadata( $attachment_id );
	// Creator
	$current_user_id = get_current_user_id();
	$creator_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option( 'authorized_creators' );
	
		// Creator
	if(wpclink_user_has_creator_list($current_user_id)){
		
	}else{
		return false;
	}
	
	
	// Origin Creator for Register Media
	if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
		if($right_holder_id = get_post_meta( $attachment_id, 'wpclink_rights_holder_user_id', true )){
			$current_user_id = $right_holder_id;
			$creator_user_id = $right_holder_id;
		}
		
	}
	
		
	// If current user is not creator.
	if ( $current_user_id < 1 ) return false;
	if($check_allow_registration == false){
	
	// Register Process
	if ( isset( $_REQUEST[ 'cl_register_process' ] ) ) {
		if ( $_REQUEST[ 'cl_register_process' ] == 1 ) {
			update_post_meta( $attachment_id, 'wpclink_post_register_status', 1 );
		} else {
			update_post_meta( $attachment_id, 'wpclink_post_register_status', '0' );
		}
	} else {
		update_post_meta( $attachment_id, 'wpclink_post_register_status', '1' );
	}
	if ( $content_register_restrict = get_post_meta( $attachment_id, 'wpclink_post_register_status', true ) ) {
		if ( $content_register_restrict == '1' ) {
			return false;
		}
	}
		
	}
	// Registration
	$registration_disallow = get_post_meta( $attachment_id, 'wpclink_registration_disallow', true );
	if ( $registration_disallow == 1 ) {
		return false;
	}
	// Creator
	$creator_user_info = get_userdata( $current_user_id );
	// Current Creator Names
	$current_creator_array = array(
		strtolower( $creator_user_info->first_name ),
		strtolower( $creator_user_info->last_name )
	);
	$display_name = $creator_user_info->display_name;
	$display_name_array = explode( ' ', $display_name );
	$display_name_array = array_map( 'strtolower', $display_name_array );
	
	
	$options = wpclink_get_option( 'preferences_general' ); 
	$exif_metdata_option = $options['embeded_metadata'];
	
	$check_exif_data = get_post_meta( $attachment_id, 'wpclink_check_exif_data', true );
	
	
	
	/* Check the Exif Data is Exists?
		- Modify Date 				- IFD0:ModifyDate
		- Exif Version 				- ExifIFD:ExifVersion
		- Date/Time Original 		- ExifIFD:DateTimeOriginal
		- Create Date 				- ExifIFD:CreateDate
		- Offset Time Original 		- ExifIFD:OffsetTimeOriginal 	
		- Offset Time Digitized 	- ExifIFD:OffsetTimeDigitized
		- Exif Image Size		 	- File:ImageWidth * File:ImageHeight
		- Offset Time 				- ExifIFD:OffsetTime
	*/
	
	if($linked_flag == false){
	
		if ((isset($_REQUEST[ 'exifdata' ]) and $_REQUEST[ 'exifdata' ] == 1 and $check_exif_data == 1 )  ||  
			($exif_metdata_option  == 'overwrite' and $check_exif_data == 1) ) {
		
		// All Image Sizes
		$all_sizes = wpclink_get_all_image_sizes($attachment_id);
		
		foreach($all_sizes as $single_image_path){
			
			update_post_meta( $attachment_id, 'wpclink_loader_status', 'exifremove' );
			
			// Meta data Creator Names	
			$xmp_metadata_array_exif = wpclink_get_image_metadata_value( $single_image_path, array( 
				'IFD0:ModifyDate',
				'ExifIFD:ExifVersion',
				'ExifIFD:DateTimeOriginal',
				'ExifIFD:CreateDate',
				'ExifIFD:OffsetTimeOriginal',
				'ExifIFD:OffsetTimeDigitized',
				'File:ImageWidth',
				'File:ImageHeight',
				'ExifIFD:OffsetTime' ) );
			
			// Response Debug
			wpclink_debug_log( 'EXIF DATA:' . print_r( $xmp_metadata_array_exif, true ) );
			try{	
				// Get Data of Image from Path
				$single_image_data = imagecreatefromjpeg($single_image_path);
				// Re-Generate Image
				imagejpeg($single_image_data,$single_image_path,100);
				// Free up memory
				imagedestroy($single_image_data);
			}catch (Exception $e){
				$error_exif['message'] = $e->getMessage();
				update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $attachment_id, 'wpclink_loader_error_data', $error_exif );
				return false;
			}	
			
		try{
		// Remove some other fields
		$other_exififd = array('ExifIFD:ComponentsConfiguration' => '', 'ExifIFD:FlashpixVersion' => '','ExifIFD:ColorSpace' => '');
			
		if(is_array($xmp_metadata_array_exif)){
			$xmp_metadata_array_exif = array_merge($other_exififd,$xmp_metadata_array_exif);
		}
		
		// Push
		wpclink_push_metadata($attachment_id,$xmp_metadata_array_exif);
			
		// Write
		wpclink_metadata_writter( $single_image_path, $xmp_metadata_array_exif, false, true, $attachment_id );
		
		}catch (Exception $e){
		
		$error_exif['message'] = $e->getMessage();
				
		update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
		update_post_meta( $attachment_id, 'wpclink_loader_error_data', $error_exif );
		return false;
		
		
		}
			
		
			
			
		}
		
		
		
	
	}
	
	}
	try{
		// Meta data Creator Names	
		$metadata_image_array = wpclink_get_image_metadata_value( $attachment_url, array( 'IPTC:By-line', 'IPTC:CopyrightNotice', 'IPTC:Credit', 'IPTC:ObjectName', 'IPTC:Keywords' ) );
		
		
		
		
	}catch (Exception $e){
		
		$error_exif['message'] = $e->getMessage();
	
		update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
		update_post_meta( $attachment_id, 'wpclink_loader_error_data', $error_exif );
		return false;
		
		
	}
	
	$creator_name_metadata = (isset($metadata_image_array['IPTC:By-line'])) ? $metadata_image_array['IPTC:By-line'] : '';
	
	
	update_user_meta($current_user_id,'wpclink_media_creator_warning','1');
	if ( $linked_flag ) {
		// Not for linked Posts
		$metadata_line = array(
			'headline' => 'IPTC:Headline',
			'creator' => 'IPTC:By-line',
			'description' => 'IPTC:Caption-Abstract',
			'keywords' => 'IPTC:Keywords',
			'title' => 'IPTC:ObjectName',
			'credit' => 'IPTC:Credit',
			'copyright_notice' => 'IPTC:CopyrightNotice'
		);
		$xmp_metadata_array = array();
		foreach ( $_REQUEST[ 'attachments' ][ $attachment_id ] as $data_key => $data_val ) {
			if ( in_array( $data_key, $iptc_metadata ) ) {
				if ( in_array( "ModifyKeywords", $media_permission_array ) ) {
					if ( $data_key == 'wpclink_image_metadata_keywords' ) {
						// Store in XMP
						$xmp_metadata_key = str_replace( "wpclink_image_metadata_", "", $data_key );
						$xmp_metadata_array[ $xmp_metadata_key ] = $data_val;
						$metadata_image_array[ $xmp_metadata_key ] = $data_val;
					}
				}
			}
		}
		// Title and Description
		$get_attachment_title = get_the_title( $attachment_id );
		$get_description = wp_get_attachment_caption( $attachment_id );
		// XMP
		if ( in_array( "ModifyDescription", $media_permission_array ) ) {
			$xmp_metadata_array[ 'description' ] = $get_description;
		}
		if ( in_array( "ModifyHeadline", $media_permission_array ) ) {
			$xmp_metadata_array[ 'image_title' ] = $get_attachment_title;
		}
	} else {
		// Stop writing creator if not match
		if ( isset( $_REQUEST[ 'attachments' ][ $attachment_id ][ 'wpclink_image_metadata_creator' ] ) and !empty( $_REQUEST[ 'attachments' ][ $attachment_id ][ 'wpclink_image_metadata_creator' ] ) ) {
			
			$submit_creator = $_REQUEST[ 'attachments' ][ $attachment_id ][ 'wpclink_image_metadata_creator' ];
			
			$submit_creator_names = explode( ' ', $submit_creator );
			
			$creator_name_match = wpclink_match_creator_names(
					$submit_creator_names,
					$current_creator_array,
					$display_name_array );
			
			/*
			 * Apply the Creator Name Match
			 *
			 * - 'creator_name_match' is the value being filtered. */
			$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
			if ( $creator_name_match_final || current_user_can('editor') || current_user_can('administrator') ) {
				
			} else {
				update_post_meta( $attachment_id, 'wpclink_creator_not_match_warning', '1' );
				return false;
			}
		}
		// Stop if creator not match to IPTC Creator
		if ( !empty( $creator_name_metadata ) ) {
			
			$metadata_creator_names = explode( ' ', $creator_name_metadata );
			
			$creator_name_match = wpclink_match_creator_names(
					$metadata_creator_names,
					$current_creator_array,
					$display_name_array );
			
			
			
			/*
			 * Apply the Creator Name Match
			 *
			 * - 'creator_name_match' is the value being filtered. */
			$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
			
			
			if ( $creator_name_match_final || current_user_can('editor') || current_user_can('administrator') ) {} else {
				return false;
			}
		} else {
		}
	
		$metadata_line = array(
			'headline' => 'IPTC:Headline',
			'creator' => 'IPTC:By-line',
			'description' => 'IPTC:Caption-Abstract',
			'keywords' => 'IPTC:Keywords',
			'title' => 'IPTC:ObjectName',
			'credit' => 'IPTC:Credit',
			'copyright_notice' => 'IPTC:CopyrightNotice'
		);
		$xmp_metadata_array = array();
		foreach ( $_REQUEST[ 'attachments' ][ $attachment_id ] as $data_key => $data_val ) {
			if ( in_array( $data_key, $iptc_metadata ) ) {
				if ( $data_key == 'wpclink_image_metadata_web_statement_rights' ) {} else {
					// Store in XMP
					$xmp_metadata_key = str_replace( "wpclink_image_metadata_", "", $data_key );
					$xmp_metadata_array[ $xmp_metadata_key ] = $data_val;
					$metadata_image_array[ $metadata_line[ $xmp_metadata_key ] ] = $data_val;
				}
			}
		}
	}
	// Response Debug
	wpclink_debug_log( 'WRITE 1:' . print_r( $xmp_metadata_array, true ) );
	try{
	
	// Push
	wpclink_push_metadata($attachment_id,$xmp_metadata_array);
		
	// Write
	wpclink_metadata_writter( $attachment_url, $xmp_metadata_array, $attachment_id, true, $attachment_id );
		
		}catch (Exception $e){
		
		$error_exif['message'] = $e->getMessage();
				
		update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
		update_post_meta( $attachment_id, 'wpclink_loader_error_data', $error_exif );
		return false;
		
		
	}
	try{
	// Meta data Creator Names	
	$metadata_image_array = wpclink_get_image_metadata_value( $attachment_url, array( 'IPTC:By-line', 'IPTC:CopyrightNotice', 'IPTC:Credit', 'IPTC:ObjectName', 'IPTC:Keywords', 'XMP-iptcExt:RegistryItemID' ) );
		
	}catch (Exception $e){
		
		$error_exif['message'] = $e->getMessage();
		
		
		update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
		update_post_meta( $attachment_id, 'wpclink_loader_error_data', $error_exif );
		return false;
		
		
	}
	// Title and Description
	$get_attachment_title = get_the_title( $attachment_id );
	$get_description = wp_get_attachment_caption( $attachment_id );
	// XMP
	$xmp_metadata_array[ 'description' ] = $get_description;
	$xmp_metadata_array[ 'image_title' ] = $get_attachment_title;
	// Creator
	$creator_user_info = get_userdata( $current_user_id );
	// Creator ID
	$creatorID = get_user_meta( $current_user_id, 'wpclink_party_ID', true );
	if ( $linked_flag ) {
	} else {
		// Creator ID and Names
		$xmp_metadata_array[ 'image_creator_ID' ] = WPCLINK_ID_URL . '/#objects/' . $creatorID;
		$xmp_metadata_array[ 'image_creator_name' ] = $creator_user_info->first_name . ' ' . $creator_user_info->last_name;
		// Caption Writter
		$xmp_metadata_array[ 'caption_writer' ] = WPCLINK_ID_URL . '/#objects/' . $creatorID;
	}
	// Set Values
	if ( $linked_flag ) {
		// Not for linked Posts
	} else {
		// Exiftool Extension
		if ( function_exists( 'wpclink_metadata_writter' ) ) {
			// License Selected Type
			$wpclink_license_selected_type = get_post_meta( $attachment_id, 'wpclink_license_selected_type', true );
			if ( $wpclink_license_selected_type == 'wpclink_personal' || $wpclink_license_selected_type == 'wpclink_business' || $wpclink_license_selected_type == 'wpclink_marketplace' ) {
				// Right ID
				$custom_web_statement_rights = get_post_meta( $attachment_id, 'wpclink_right_ID', true );
				if ( wpclink_attachment_in_attachment_referent_list( $attachment_id ) ) {
					// Right ID
					$custom_web_statement_rights = get_post_meta( $attachment_id, 'wpclink_right_ID', true );
					if ( !empty( $custom_web_statement_rights ) ) {
						$xmp_metadata_array[ 'webstatement' ] = WPCLINK_ID_URL . '/#objects/' . $custom_web_statement_rights;
					}

                    
				}
				if ( $linked_flag ) {
					$referent_creator_party_ID = get_post_meta( $attachment_id, 'wpclink_referent_creator_party_ID', true );
					$custom_url = $referent_creator_party_ID;
					$xmp_metadata_array[ 'licensor_url' ] = WPCLINK_ID_URL . '/#objects/' . $custom_url;
				} else {
                    
                    
                     if($wpclink_license_selected_type == 'wpclink_marketplace'){
                            $xmp_metadata_array[ 'termsandcondition_url' ] = 'https://licenses.clink.id/personal/0-9im/';
                        }else{
                            $xmp_metadata_array[ 'termsandcondition_url' ] = 'https://licenses.clink.id/personal/0-9i/';
                      }
                    
                    
					if ( wpclink_attachment_in_attachment_referent_list( $attachment_id ) ) {
						// Author
						$author_id = get_post_field( 'post_author', $attachment_id );
						$creator_identifier = get_user_meta( $author_id, 'wpclink_party_ID', true );
						$custom_url = $creator_identifier;
						if ( wpclink_is_reuse_guid() ) {
							$xmp_metadata_array[ 'licensor_url' ] = wpclink_reuse_guid_url( $attachment_id );
						} else {
							$xmp_metadata_array[ 'licensor_url' ] = WPCLINK_ID_URL . '/#objects/' . $custom_url;
						}
					}
				}
			} elseif ( $wpclink_license_selected_type == 'custom' ) {
				// Custom Licensor URL
				$custom_url = get_post_meta( $attachment_id, 'wpclink_custom_url', true );
				$xmp_metadata_array[ 'licensor_url' ] = $custom_url;
				// Web Statement of Rights Custom / Programatic
				$right_object_id = get_post_meta( $attachment_id, 'wpclink_right_object', true );
				if ( $right_object_id == 1 ) {
					$xmp_metadata_array[ 'webstatement' ] = get_post_meta( $attachment_id, 'wpclink_right_ID', true );
				} else {
					if ( !empty( $custom_web_statement_rights ) ) {
						$xmp_metadata_array[ 'webstatement' ] = $custom_web_statement_rights;
					}
				}
				$creator_user_info = get_userdata( $current_user_id );
				$licensor_display_name = $creator_user_info->first_name . ' ' . $creator_user_info->last_name;
				$xmp_metadata_array[ 'licensor_display_name' ] = $licensor_display_name;
				$xmp_metadata_array[ 'licensor_email' ] = $creator_user_info->user_email;
				$clink_creatorID = get_user_meta( $current_user_id, 'wpclink_party_ID', true );
				$xmp_metadata_array[ 'licensor_ID' ] = WPCLINK_ID_URL . '/#objects/' . $clink_creatorID;
				if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
					// Image Creation ID
					$xmp_metadata_array[ 'licensor_image_ID' ] = WPCLINK_ID_URL . '/#objects/' . $creationID;
				}
				// Source
				$xmp_metadata_array[ 'photoshop_source' ] = $licensor_display_name;
			} else {
				// Nothing
				if ( $linked_flag ) {
					$referent_creator_party_ID = get_post_meta( $attachment_id, 'wpclink_referent_creator_party_ID', true );
					$custom_url = $referent_creator_party_ID;
					$xmp_metadata_array[ 'licensor_url' ] = WPCLINK_ID_URL . '/#objects/' . $custom_url;
					// Right ID
					$custom_web_statement_rights = get_post_meta( $attachment_id, 'wpclink_right_ID', true );
					if ( !empty( $custom_web_statement_rights ) ) {
						$xmp_metadata_array[ 'webstatement' ] = WPCLINK_ID_URL . '/#objects/' . $custom_web_statement_rights;
					}
				} else {
				}
			}
			// Response Debug
			wpclink_debug_log( 'WRITE 2:' . print_r( $xmp_metadata_array, true ) );
			// Write
			/* wpclink_metadata_writter( $attachment_url, $xmp_metadata_array, $attachment_id ); */
		}
	}
	// Set Values
	if ( $linked_flag ) {
		// Not for linked Posts
	} else {
		$xmp_metadata_array[ 'image_title' ] = $get_attachment_title;
	}
	if ( $linked_flag ) {
		if ( in_array( "ModifyDescription", $media_permission_array ) ) {
			$xmp_metadata_array[ 'description' ] = $get_description;
		}
	} else {
		$xmp_metadata_array[ 'description' ] = $get_description;
	}
	
	
	// ---> Writing to Registry
 	update_post_meta( $attachment_id, 'wpclink_loader_status', 'registrywrites' );
	/* -- REGISTRATION -- */
	// Title
	$attachment_title = get_the_title( $attachment_id );
	
	// URL
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
	$copyright_notice = (isset($metadata_image_array['IPTC:CopyrightNotice' ])) ? $metadata_image_array['IPTC:CopyrightNotice' ] : '';
	// Credit Line
	$creditline = (isset($metadata_image_array['IPTC:Credit'])) ? $metadata_image_array['IPTC:Credit'] : '';
	//Creator ID
	$creator_id = get_user_meta( $creator_user_id, 'wpclink_party_ID', true );
	// IPTC Title
	$iptc_title = (isset($metadata_image_array['IPTC:ObjectName'])) ? $metadata_image_array['IPTC:ObjectName'] : '';
	// Keywords
	$keywords = (isset($metadata_image_array['IPTC:Keywords' ])) ? $metadata_image_array['IPTC:Keywords' ] : '';
	
	
	
	
	
	// CLink Language
	$clink_language = wpclink_get_current_site_lang();
	// Terriotory
	$clink_terriory_code = wpclink_get_current_terriority_name();
	if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
		$action = 'update';
		$time = get_the_date( 'Y-m-d', $attachment_id );
		$time .= 'T';
		$time .= get_the_date( 'G:i:s', $attachment_id );
		$time .= 'Z';
	} else {
		$action = 'create';
		$creationID = '';
		$time = get_the_date( 'Y-m-d', $attachment_id );
		$time .= 'T';
		$time .= get_the_date( 'G:i:s', $attachment_id );
		$time .= 'Z';
	}
	
		
	// Set Values
	if ( $linked_flag ) {
		
		$time_of_creation  = get_post_meta($attachment_id,'wpclink_time_of_creation',true);
		$time_of_modification  = get_the_modified_time('Y-m-d',$attachment_id);
		$time_of_modification .= 'T';
		$time_of_modification .= get_the_modified_time('G:i:s',$attachment_id);
		$time_of_modification .= 'Z';
	} else {
		$time_data = wpclink_get_image_datetime($attachment_id);
		$time_of_creation = $time_data['created'];
		$time_of_modification = $time_data['modified'];
	}
	
	if (isset($_REQUEST[ 'cl_version' ]) and $_REQUEST[ 'cl_version' ] == 1 ) {
		$action = 'version';
	}
	$creation_access_key = get_post_meta( $attachment_id, 'wpclink_creation_access_key', true );
	if (isset($_REQUEST[ 'generate_iscc' ]) and $_REQUEST[ 'generate_iscc' ] == 1 ) {
		$iscc = '1';
		$url_iscc = WPCLINK_ISCC_API;
		// Register to CLink.ID
		$response = wp_remote_post(
			$url_iscc,
			array(
				'body' => array(
					'CLinkContentID' => $creationID,
					'domain_access_key' => $domain_access_key,
					'site_address' => get_bloginfo( 'url' ),
					'clink_creation_uri' => $file_url,
					'iscc' => $iscc,
					'clink_iscc_update' => '1',
					'creation_access_key' => $creation_access_key
				), 'timeout' => WPCLINK_API_TIMEOUT, 'method' => 'POST'
			)
		);
		$response_json = $response[ 'body' ];
		$resposne_Array = json_decode( $response_json, true );
		if ( is_wp_error( $response ) ) {
			$resposne_Array = is_wp_error( $response );
			// Response Debug
			wpclink_debug_log( 'PUBLISH MEDIA ISCC ERROR' . print_r( $response, true ) );
		} else {
			// Response Debug
			wpclink_debug_log( 'PUBLISH MEDIA ISCC' . print_r( $response, true ) );
			if ( $resposne_Array[ 'status' ] == 'iscc_updated' ) {
				update_post_meta( $attachment_id, 'wpclink_iscc_status', 1 );
			}
		}
	} else {
		$iscc = '0';
	}
	$post_guid = get_the_guid( $attachment_id );
	$archive_link = wpclink_get_last_archive($attachment_id);
	
	if ( $programattic_rights_category = get_post_meta( $attachment_id, 'wpclink_programmatic_right_categories', true ) ) {
		// Reuse GUID
		$media_license_url = get_bloginfo( 'url' ).'/';
		$media_license_url = add_query_arg( 'clink_media_license', '', $media_license_url );
		$reuse_GUID = add_query_arg( 'id', $attachment_id, $media_license_url );
	} else {
		$reuse_GUID = '';
	}
	if ( $clink_taxonomy_permission = get_post_meta( $attachment_id, 'wpclink_programmatic_right_categories', true ) ) {} else {
		$clink_taxonomy_permission = '';
	}
	if ( $clink_referent_creation_ID = get_post_meta( $attachment_id, 'wpclink_referent_creation_ID', true ) ) {} else {
		$clink_referent_creation_ID = '';
	}
	if ( $clink_referent_creator_ID = get_post_meta( $attachment_id, 'wpclink_referent_creator_party_ID', true ) ) {} else {
		$clink_referent_creator_ID = '';
	}
	if ( $clink_referent_right_holder_display_name = get_post_meta( $attachment_id, 'wpclink_referent_rights_holder_display_name', true ) ) {
	} else {
		$clink_referent_right_holder_display_name = '';
	}
	
	$image_path 	= 	wpclink_iptc_image_path( $attachment_id, 'full' );
	$file_data 		= 	wpclink_get_jpeg_image_data($image_path );
	$sha256_hash 	= 	hash('sha256', $file_data, false );
	
	$url_media = WPCLINK_MEDIA_API;
	
	$before_media = array(
			'body' => array(
				'CLinkContentID' => $creationID,
				'post_title' => html_entity_decode( $attachment_title ),
				'iptc_title' => $iptc_title,
				'clink_referent_creation_identifier' => $clink_referent_creation_ID,
				'referent_clink_creatorID' => $clink_referent_creator_ID,
				'referent_creator_display_name' => $clink_referent_right_holder_display_name,
				'referent_creation_rights_holder_display_name' => $clink_referent_right_holder_display_name,
				'keywords' => $keywords,
				'creator_uri' => $creator_user_info->user_url,
				'reuseGUID' => $reuse_GUID,
				'clink_taxonomy_permission' => $clink_taxonomy_permission,
				'creator_display_name' => $creator_user_info->display_name,
				'creator_email' => $creator_user_info->user_email,
				'post_excerpts' => $attachment_excerpt,
				'time_of_creation' => $time_of_creation,
				'time_of_modification' => $time_of_modification,
				'domain_access_key' => $domain_access_key,
				'site_address' => get_bloginfo( 'url' ),
				'iscc' => $iscc,
				'clink_sha256_hash' => $sha256_hash,
				'creditline' => $creditline,
				'clink_creatorID' => $creator_id,
				'creation_access_key' => $creation_access_key,
				'copyright_notice' => $copyright_notice,
				'clink_language' => $clink_language,
				'clink_territory_code' => $clink_terriory_code,
				'archive_web_url' => $archive_link,
				'action' => $action
			), 'timeout' => 45, 'method' => 'POST'
		);
	
	if($image_register_url = wpclink_get_option('image_register_url')){
		$before_media['body']['attachment_post_url'] 	= preg_replace("/^http:/i", "https:", wpclink_get_image_URL( $attachment_id ));
		$before_media['body']['creation_GUID'] 			= get_the_guid( $attachment_id );
	}
	
	
	$before_media = apply_filters( 'wpclink_update_media_filter', $before_media, $attachment_id, $attachment_url);
	
	// Register to CLink.ID
	$response = wp_remote_post(
		$url_media,
		$before_media 
	);
	if ( is_wp_error( $response ) ) {
		$wp_error = is_wp_error( $response );
		// Response Debug
		wpclink_debug_log( 'PUBLISH MEDIA ' . print_r( $response, true ) );
		
		
		if($wp_error == 1){
			$response_check = wpclink_return_wp_error($response);			
			update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
			update_post_meta( $attachment_id, 'wpclink_loader_error_data', $response_check );
			return false;
		}
		
		
		
	} else {
		// Response Debug
		wpclink_debug_log( 'PUBLISH MEDIA ' . print_r( $response, true ) );
		
		$response_json = $response[ 'body' ];
		$resposne_Array = json_decode( $response_json, true );
		
		$return_response = wpclink_return_api_reponse($response);
		if($return_response === true){
			
			/* Check response has is_error */
			$response_check = wpclink_response_check($resposne_Array);
			
			if($response_check == false){
				
				
				
			}else{
				update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $attachment_id, 'wpclink_loader_error_data', $response_check );
				return false;
			}
			
		}else{	
				update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
				update_post_meta( $attachment_id, 'wpclink_loader_error_data', $return_response );
				return false;
		}
		
		// ---> Updating Databse
		update_post_meta( $attachment_id, 'wpclink_loader_status', 'updatingdatabase' );
		
		if ( $resposne_Array[ 'status' ] == 'create' ) {
			if ( !empty( $resposne_Array[ 'data' ][ 'clink_creationID' ] ) ) {
				// Update Creation ID
				update_post_meta( $attachment_id, 'wpclink_creation_ID', $resposne_Array[ 'data' ][ 'clink_creationID' ] );
				// Quick Fix
				update_post_meta( $attachment_id, 'wpclink_license_selected_type', 'wpclink_personal' );
				if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_identifier' ] ) ) {
					// Update Rights ID
					update_post_meta( $attachment_id, 'wpclink_right_ID', $resposne_Array[ 'data' ][ 'clink_right_identifier' ] );
				}
			}
			// Encrypted
			if ( !empty( $resposne_Array[ 'data' ][ 'creation_access_key' ] ) ) {
				// Encrypt
				update_post_meta( $attachment_id, 'wpclink_creation_access_key', $resposne_Array[ 'data' ][ 'creation_access_key' ] );
			}
			
			// Right Holder User ID
			update_post_meta($attachment_id,'wpclink_rights_holder_user_id',$current_user_id);
			
		} else if ( $resposne_Array[ 'status' ] == 'update' ) {
			if ( !empty( $resposne_Array[ 'data' ][ 'clink_creationID' ] ) ) {
				// Update Creation ID
				update_post_meta( $attachment_id, 'wpclink_creation_ID', $resposne_Array[ 'data' ][ 'clink_creationID' ] );
				if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_identifier' ] ) ) {
					// Update Rights ID
					update_post_meta( $attachment_id, 'wpclink_right_ID', $resposne_Array[ 'data' ][ 'clink_right_identifier' ] );
				}
			}
			// Encrypted
			if ( !empty( $resposne_Array[ 'data' ][ 'creation_access_key' ] ) ) {
				// Encrypt
				update_post_meta( $attachment_id, 'wpclink_creation_access_key', $resposne_Array[ 'data' ][ 'creation_access_key' ] );
			}
			//wpclink_creation_ID
		} elseif ( $resposne_Array[ 'status' ] == 'version' ) {
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
				$version_id = $resposne_Array[ 'data' ][ 'id' ];
				$version_ready = array(
					'status' => 'ready',
					'version_identifier' => $version_id,
					'version_identifier_encrypt' => $resposne_Array[ 'data' ][ 'encrypt_creation_id' ]
				);
				update_post_meta( $attachment_id, 'wpclink_version_ready', $version_ready );
				
				// Version Time
				if($version_modified_time = get_post_meta($attachment_id,'wpclink_versions_time',true)){
					$version_modified_time[$resposne_Array['data']['id' ]] = $time_of_modification;
					update_post_meta($attachment_id,'wpclink_versions_time',$version_modified_time);
				}else{
					$version_modified_time = array();
					$version_modified_time[$resposne_Array['data']['id']] = $time_of_modification;
					update_post_meta($attachment_id,'wpclink_versions_time',$version_modified_time);
				}
				
				
				do_action('wpclink_add_archive_version_list',$attachment_id,$version_id);
				
				
			}
		}
	}
	if ( $linked_flag ) {} else {
		
		
			if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
				
			// Registry
			$regid_1 = WPCLINK_ID_URL . '/#objects/' . $creationID;	
			
			// SHA256
			$xmp_metadata_array['registry_item_IDat3'] = $sha256_hash;
			
			// ISCC
			$xmp_metadata_array['registry_item_IDat1'] = $regid_1;
			if(isset($metadata_image_array[ 'XMP-iptcExt:RegistryItemID' ])){
				$regids = $metadata_image_array[ 'XMP-iptcExt:RegistryItemID' ];
				$regid_array = explode(' ; ',$regids);
				if(isset($regid_array[2])){
					$regid_2 = $regid_array[2];
					if(!empty($regid_2)){
						$xmp_metadata_array['registry_item_IDat2'] = $regid_2;
						
					}
				}
			}			
			
			
		}
	}
	wpclink_debug_log( 'WRITE 3:' . print_r( $xmp_metadata_array, true ) );
	
	// ---> Updating Embeded Metadata
	update_post_meta( $attachment_id, 'wpclink_loader_status', 'updatingembeded' );
	
	// @Hook at the end of metadata media update
	do_action('wpclink_media_update_end',$attachment_id);
	
	try{
		
		
		
	
		
	// Push
	wpclink_push_metadata($attachment_id,$xmp_metadata_array);
		
	// Write
	wpclink_metadata_writter( $attachment_url, $xmp_metadata_array, $attachment_id, true, $attachment_id );
		
	delete_post_meta( $attachment_id, 'wpclink_check_exif_data');
	
	}catch (Exception $e){
		
		$error_exif['message'] = $e->getMessage();
		
	
		update_post_meta( $attachment_id, 'wpclink_loader_status', 'error' );
		update_post_meta( $attachment_id, 'wpclink_loader_error_data', $error_exif );
		return false;
		
		
	}
	
	// ---> Updating Embeded Metadata
	update_post_meta( $attachment_id, 'wpclink_loader_status', 'reloading' );
}
add_action( 'edit_attachment', 'wpclink_update_image_metadata' );
/**
 * CLink Update License Media
 *
 * Update Media License
 *
 *
 * @param int attachment_id ID of post.
 */
function wpclink_update_media_license( $attachment_id ) {
	
	// Default
	$iscc = 0;
	// Only Jpeg
	$media_type = get_post_mime_type( $attachment_id );
	if ( $media_type == 'image/jpeg' || $media_type == 'image/jpg' ) {} else {
		return false;
	}
	
	// Disable CLink Feature due to error
	$continue_to_image = get_post_meta( $attachment_id, 'wpclink_continue_to_image', true );
	
	/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
	
	
	if ( $continue_to_image_final == 1 ) {
		return false;
	} 
	
	
	
	// GUID
	$guid = get_the_guid($attachment_id);
	
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));  
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];
	
	// Is media is linked?
	$linked_media = get_post_meta( $attachment_id, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_media ) ) {
		$linked_flag = true;
	} else {
		$linked_flag = false;
	}
	// Party
	$clink_party_id = wpclink_get_option( 'authorized_contact' );
	$clink_party_id = get_user_meta( $clink_party_id, 'wpclink_party_ID', true );
	// Image Meta Data
	$attachment_url = wpclink_iptc_image_path( $attachment_id, 'full' );
	$attachment_meta = wp_get_attachment_metadata( $attachment_id );
	// Creator
	$current_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option( 'authorized_creators' );
	// If current user is not creator.
	if ( $current_user_id < 1 ) return false;
	if ( wpclink_check_license_by_post_id( $attachment_id ) > 0 ) return false;
	// Origin Creator for Register Media
	if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
		if($right_holder_id = get_post_meta( $attachment_id, 'wpclink_rights_holder_user_id', true )){
			$current_user_id = $right_holder_id;
			$creator_user_id = $right_holder_id;
		}
		
	}
	// Creator
	$creator_user_info = get_userdata( $creator_user_id );
	// Current Creator Names
	$current_creator_array = array(
		strtolower( $creator_user_info->first_name ),
		strtolower( $creator_user_info->last_name )
	);
	$display_name = $creator_user_info->display_name;
	$display_name_array = explode( ' ', $display_name );
	$display_name_array = array_map( 'strtolower', $display_name_array );
	// Meta data Creator Names
	$metadata_image_array = wpclink_get_image_metadata_value( $attachment_url,
		array( 'IPTC:By-line', 'IPTC:CopyrightNotice', 'IPTC:Credit', 'IPTC:ObjectName', 'IPTC:Keywords' ) );
	
	
	
	// Creator
	$creator_name_metadata = (isset($metadata_image_array['IPTC:By-line' ])) ? $metadata_image_array['IPTC:By-line' ] : '';
	// Title and Description
	$get_attachment_title = get_the_title( $attachment_id );
	$get_description = wp_get_attachment_caption( $attachment_id );
	// XMP
	$xmp_metadata_array = array();
	$xmp_metadata_array[ 'description' ] = $get_description;
	$xmp_metadata_array[ 'image_title' ] = $get_attachment_title;
	// Set Values
	if ( $linked_flag ) {
		// Not for linked Posts
	} else {
		if ( function_exists( 'wpclink_metadata_writter' ) ) {
			// License Selected Type
			$wpclink_license_selected_type = get_post_meta( $attachment_id, 'wpclink_license_selected_type', true );
			if ( $wpclink_license_selected_type == 'wpclink_personal' || $wpclink_license_selected_type == 'wpclink_business' || $wpclink_license_selected_type == 'wpclink_marketplace' ) {
				// Right ID
				$custom_web_statement_rights = get_post_meta( $attachment_id, 'wpclink_right_ID', true );
				if ( !empty( $custom_web_statement_rights ) ) {
					$xmp_metadata_array[ 'webstatement' ] = WPCLINK_ID_URL . '/#objects/' . $custom_web_statement_rights;
				}
				if ( $linked_flag ) {
					// Referent Creator ID
					$referent_creator_party_ID = get_post_meta( $attachment_id, 'wpclink_referent_creator_party_ID', true );
					$custom_url = $referent_creator_party_ID;
					$xmp_metadata_array[ 'licensor_url' ] = WPCLINK_ID_URL . '/#objects/' . $custom_url;
				} else {
					// Author
					$author_id = get_post_field( 'post_author', $attachment_id );
					$creator_identifier = get_user_meta( $author_id, 'wpclink_party_ID', true );
					$custom_url = $creator_identifier;
					if ( wpclink_is_reuse_guid() ) {
						$xmp_metadata_array[ 'licensor_url' ] = wpclink_reuse_guid_url( $attachment_id );
					} else {
						$xmp_metadata_array[ 'licensor_url' ] = WPCLINK_ID_URL . '/#objects/' . $custom_url;
					}
				}
			} elseif ( $wpclink_license_selected_type == 'custom' ) {
				// Custom Web Statement Rights
				$custom_web_statement_rights = get_post_meta( $attachment_id, 'wpclink_custom_web_statement_rights', true );
				if ( !empty( $custom_web_statement_rights ) ) {
					$xmp_metadata_array[ 'webstatement' ] = $custom_web_statement_rights;
				}
				// Custom URL
				$custom_url = get_post_meta( $attachment_id, 'wpclink_custom_url', true );
				$xmp_metadata_array[ 'licensor_url' ] = WPCLINK_ID_URL . '/#objects/' . $custom_url;
			} else {
				// Nothing
				if ( $linked_flag ) {
					$referent_creator_party_ID = get_post_meta( $attachment_id, 'wpclink_referent_creator_party_ID', true );
					$custom_url = $referent_creator_party_ID;
					$xmp_metadata_array[ 'licensor_url' ] = WPCLINK_ID_URL . '/#objects/' . $custom_url;
					// Right ID
					$custom_web_statement_rights = get_post_meta( $attachment_id, 'wpclink_right_ID', true );
					if ( !empty( $custom_web_statement_rights ) ) {
						$xmp_metadata_array[ 'webstatement' ] = WPCLINK_ID_URL . '/#objects/' . $custom_web_statement_rights;
					}
				} else {
				}
			}
		}
	}
	if ( !empty( $creator_name_metadata ) ) {
		$metadata_creator_names = explode( ' ', $creator_name_metadata );
		
		$creator_name_match = wpclink_match_creator_names(
				$metadata_creator_names,
				$current_creator_array,
				$display_name_array );
		
		$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
		
		
		if ( $creator_name_match_final || current_user_can('editor') || current_user_can('administrator') ) {} else {
			return false;
		}
	} else {
	}
	/* -- REGISTRATION SECTION -- */
	// Title
	$attachment_title = get_the_title( $attachment_id );
	// Title
	$attachment_post_url = wpclink_get_image_URL( $attachment_id );
	// IPTC title
	$iptc_title = (isset($metadata_image_array['IPTC:ObjectName'])) ? $metadata_image_array['IPTC:ObjectName'] : '';
	// File URL
	$file_url = wpclink_get_image_URL( $attachment_id );
	// Excerpt
	$attachment_excerpt = wp_get_attachment_caption( $attachment_id );
	// Creator
	$creator_user_info = get_userdata( $creator_user_id );
	// Domain ID
	$domain_access_key = wpclink_get_option( 'domain_access_key' );
	// Copyright Notice
	$copyright_notice = (isset($metadata_image_array['IPTC:CopyrightNotice'])) ? $metadata_image_array['IPTC:CopyrightNotice'] : '';
	
	// Credit Line
	$creditline = (isset($metadata_image_array['IPTC:Credit'])) ? $metadata_image_array['IPTC:Credit'] : '';
	
	// Keywords 
	$keywords = (isset($metadata_image_array['IPTC:Keywords'])) ? $metadata_image_array['IPTC:Keywords'] : '';
	
	// CLink Language
	$clink_language = wpclink_get_current_site_lang();
	// Terriotory
	$clink_terriory_code = wpclink_get_current_terriority_name();
	//Creator ID
	$creator_id = get_user_meta( $creator_user_id, 'wpclink_party_ID', true );
	if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
		$action = 'update';
		$time = get_the_date( 'Y-m-d', $attachment_id );
		$time .= 'T';
		$time .= get_the_date( 'G:i:s', $attachment_id );
		$time .= 'Z';
	}
	
	$time_data = wpclink_get_image_datetime($attachment_id);
	$time_of_creation = $time_data['created'];
	$time_of_modification = $time_data['modified'];
	
	$creation_access_key = get_post_meta( $attachment_id, 'wpclink_creation_access_key', true );
	$post_guid = get_the_guid( $attachment_id );
	$archive_link = wpclink_get_last_archive($attachment_id);
	
	// clink_taxonomy_permission
	if ( $clink_taxonomy_permission = get_post_meta( $attachment_id, 'wpclink_programmatic_right_categories', true ) ) {
	} else {
		$clink_taxonomy_permission = '';
	}
	
	$image_path 	= 	wpclink_iptc_image_path( $attachment_id, 'full' );
	$file_data 		= 	wpclink_get_jpeg_image_data($image_path );
	$sha256_hash 	= 	hash('sha256', $file_data, false );
	
	
	// Reuse GUID
	$media_license_url = get_bloginfo( 'url' ).'/';
	$media_license_url = add_query_arg( 'clink_media_license', '', $media_license_url );
	$reuse_GUID = add_query_arg( 'id', $attachment_id, $media_license_url );
	$url_media = WPCLINK_MEDIA_API;
	
	
	$before_media = array(
			'body' => array(
				'CLinkContentID' => $creationID,
				'post_title' => html_entity_decode( $attachment_title ),
				'iptc_title' => $iptc_title,
				'keywords' => $keywords,
				'creator_uri' => $creator_user_info->user_url,
				'reuseGUID' => $reuse_GUID,
				'clink_taxonomy_permission' => $clink_taxonomy_permission,
				'creator_display_name' => $creator_user_info->display_name,
				'creator_email' => $creator_user_info->user_email,
				'post_excerpts' => $attachment_excerpt,
				'time_of_creation' => $time_of_creation,
				'time_of_modification' => $time_of_modification,
				'domain_access_key' => $domain_access_key,
				'site_address' => get_bloginfo( 'url' ),
				'iscc' => $iscc,
				'clink_sha256_hash' => $sha256_hash,
				'creditline' => $creditline,
				'clink_creatorID' => $creator_id,
				'creation_access_key' => $creation_access_key,
				'copyright_notice' => $copyright_notice,
				'clink_language' => $clink_language,
				'clink_territory_code' => $clink_terriory_code,
				'archive_web_url' => $archive_link,
                
                'license_type' => $wpclink_license_selected_type,
				'action' => $action
			), 'timeout' => WPCLINK_API_TIMEOUT, 'method' => 'POST'
		);
	
	if($image_register_url = wpclink_get_option('image_register_url')){
		$before_media['body']['attachment_post_url'] 	= preg_replace("/^http:/i", "https:", wpclink_get_image_URL( $attachment_id ));
		$before_media['body']['creation_GUID'] 			= get_the_guid( $attachment_id );
	}
	
	$before_media = apply_filters( 'wpclink_update_media_filter', $before_media, $attachment_id, $attachment_url);
		
	// Register to CLink.ID
	$response = wp_remote_post(
		$url_media,
		$before_media
	);
	if ( is_wp_error( $response ) ) {
		$wp_error = is_wp_error( $response );
		// Response Debug
		wpclink_debug_log( 'PUBLISH MEDIA LICENSE ' . print_r( $response, true ) );
		
		
		if($wp_error == 1){
			$response_check = wpclink_return_wp_error($response);			
			return $response_check;
		}
		
	} else {
		
		// Response Debug
		wpclink_debug_log( 'PUBLISH MEDIA ' . print_r( $response, true ) );
		
		$response_json = $response[ 'body' ];
		$resposne_Array = json_decode( $response_json, true );
		
		
		
		
		$return_response = wpclink_return_api_reponse($response);
		if($return_response === true){
			
			/* Check response has is_error */
			$response_check = wpclink_response_check($resposne_Array);
			
			if($response_check == false){
				
				
				
			}else{
				return $response_check;
			}
			
		}else{	
				return $return_response;
		}
		
		
		if ( $resposne_Array[ 'status' ] == 'create' ) {
			
			if ( !empty( $resposne_Array[ 'data' ][ 'clink_creationID' ] ) ) {
				// Update Creation ID
				update_post_meta( $attachment_id, 'wpclink_creation_ID', $resposne_Array[ 'data' ][ 'clink_creationID' ] );
			}
			if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_identifier' ] ) ) {
				// Update Rights ID
				update_post_meta( $attachment_id, 'wpclink_right_ID', $resposne_Array[ 'data' ][ 'clink_right_identifier' ] );
			}
			
			// Encrypted
			if ( !empty( $resposne_Array[ 'data' ][ 'creation_access_key' ] ) ) {
				// Encrypt
				update_post_meta( $attachment_id, 'wpclink_creation_access_key', $resposne_Array[ 'data' ][ 'creation_access_key' ] );
			}
		} else if ( $resposne_Array[ 'status' ] == 'update' ) {
			
			if ( !empty( $resposne_Array[ 'data' ][ 'clink_creationID' ] ) ) {
				// Update Creation ID
				update_post_meta( $attachment_id, 'wpclink_creation_ID', $resposne_Array[ 'data' ][ 'clink_creationID' ] );
			}
			if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_identifier' ] ) ) {
				// Update Rights ID
				update_post_meta( $attachment_id, 'wpclink_right_ID', $resposne_Array[ 'data' ][ 'clink_right_identifier' ] );
				$xmp_metadata_array[ 'webstatement' ] = WPCLINK_ID_URL . '/#objects/' . $resposne_Array[ 'data' ][ 'clink_right_identifier' ];
			}
			
			
			
			
			// Right Create Time
			if ( !empty( $resposne_Array[ 'data' ][ 'clink_right_create_date' ] ) ) {
				
				$right_time =  explode('.',$resposne_Array[ 'data' ][ 'clink_right_create_date' ]);
				
				// Update Rights Create Time ID
				update_post_meta( $attachment_id, 'wpclink_right_created_time', $right_time[0].'Z'  );
				
			}
			
			// Encrypted
			if ( !empty( $resposne_Array[ 'data' ][ 'creation_access_key' ] ) ) {
				// Encrypt
				update_post_meta( $attachment_id, 'wpclink_creation_access_key', $resposne_Array[ 'data' ][ 'creation_access_key' ] );
			}
		}
	}
	// License Selected Type
	$wpclink_license_selected_type = get_post_meta( $attachment_id, 'wpclink_license_selected_type', true );
	if ( $wpclink_license_selected_type == 'wpclink_personal' || $wpclink_license_selected_type == 'wpclink_business' || $wpclink_license_selected_type == 'wpclink_marketplace' ) {
		$creator_user_info = get_userdata( $current_user_id );
		$licensor_display_name = $creator_user_info->first_name . ' ' . $creator_user_info->last_name;
		$xmp_metadata_array[ 'licensor_display_name' ] = $licensor_display_name;
		$xmp_metadata_array[ 'licensor_email' ] = $creator_user_info->user_email;
		$clink_creatorID = get_user_meta( $current_user_id, 'wpclink_party_ID', true );
		$xmp_metadata_array[ 'licensor_ID' ] = WPCLINK_ID_URL . '/#objects/' . $clink_creatorID;
        
        if($wpclink_license_selected_type == 'wpclink_marketplace'){
		$xmp_metadata_array[ 'termsandcondition_url' ] = 'https://licenses.clink.id/personal/0-9im/';
            
        }else{
            
        $xmp_metadata_array[ 'termsandcondition_url' ] = 'https://licenses.clink.id/personal/0-9i/';
            
        }
		//$xmp_metadata_array['imagefileconstraints'] = "Maintain Metadata";
		if ( $linked_flag ) {
			// Media Constrains
			$xmp_metadata_array[ 'mediaconstraints' ] = 'WordPress';
			// Image File Constraints
			$xmp_metadata_array[ 'imagefileconstraints' ] = 'http://ns.useplus.org/ldf/vocab/IF-MMD';
			// Credit Line Required
			$xmp_metadata_array[ 'creditlinerequired' ] = 'http://ns.useplus.org/ldf/vocab/CR-COI';
			// Media Summary Code
			$xmp_metadata_array[ 'mediasummarycode' ] = '|PLUS|V0121|U001|1IAK1UNA2BFU3PTZ4SKG5VUY6QUL7DWM8RAA8IPO8LAA9ENE| ';
		} else {
			// Source
			$xmp_metadata_array[ 'photoshop_source' ] = $licensor_display_name;
		}
		if ( $creationID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true ) ) {
			// Image Creation ID
			$xmp_metadata_array[ 'licensor_image_ID' ] = WPCLINK_ID_URL . '/#objects/' . $creationID;
		}
	}
	
	// Push Changes
	wpclink_push_metadata($attachment_id,$xmp_metadata_array);
	
	// Write
	wpclink_metadata_writter( $attachment_url, $xmp_metadata_array, false, true, $attachment_id );
	
	
	// @Hook at the end of license update
	do_action('wpclink_license_update_end',$attachment_id);
	// Response Debug
	wpclink_debug_log( 'DATA FOR XMP LICENSE UPDATE' . print_r( $xmp_metadata_array, true ) );
}
/**
 * CLink Display IPTC Metadata
 * 
 * @param array $form_fields
 * @param object $post
 * @return array
 */
function wpclink_display_iptc_image_metadata( $form_fields, $post ) {
	if ( empty( $post->ID ) ) return $form_fields;
	// Only Jpeg
	$media_type = get_post_mime_type( $post->ID );
	if ( $media_type == 'image/jpeg' || $media_type == 'image/jpg' ) {} else {
		return $form_fields;
	}
	
	
		
	
	
	// Disable CLink Feature due to error
	$continue_to_image = get_post_meta( $post->ID, 'wpclink_continue_to_image', true );
	
	/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
	
	if ( $continue_to_image_final == 1 ) {
		return false;
	}
	
	
	// GUID
	$guid = get_the_guid($post->ID);
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));  
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];
	
	// Current User
	$current_user_id = get_current_user_id();
	
	// No Status
	update_post_meta( $post->ID, 'wpclink_loader_status', '0' );
	// Removed Image Grid View
	if ( $_REQUEST[ 'action' ] == 'query-attachments' ) {
		return $form_fields;
	}
	$disallow = false;
	if ( $content_register_restrict = get_post_meta( $post->ID, 'wpclink_post_register_status', true ) ) {
		if ( $content_register_restrict == '1' ) {
			$linked_media = get_post_meta( $post->ID, 'wpclink_referent_post_link', true );
			if ( !empty( $linked_media ) ) {
				$linked_flag = true;
			} else {
				$linked_flag = false;
			}
			if ( $linked_flag ) {
				$clink_taxonomy_permission = str_replace( ",", "_", $clink_taxonomy_permission ) . '-linked';
			}
			$registration_disallow = get_post_meta( $post->ID, 'wpclink_registration_disallow', true );
			if ( $registration_disallow == 1 ) {
				$clink_taxonomy_permission = 'disallow';
			}
			// Disallow
			if ( $disallow ) {
				$clink_taxonomy_permission = 'disallow';
			}
			if ( wpclink_check_license_by_post_id( $post->ID ) > 0 ) {
				$clink_taxonomy_permission = 'referent_creation';
			}
			if(!isset($clink_taxonomy_permission)){
				$clink_taxonomy_permission = '';
			}
			// Taxonomy
			$form_fields[ "wpclink_image_taxonomy_permission" ] = array(
				'label' => '',
				'input' => 'html',
				'html' => '<input type="hidden" id="attachments-' . $post->ID . '-wpclink_image_taxonomy_permission" name="attachments[' . $post->ID . '][wpclink_image_taxonomy_permission]" value="' . $clink_taxonomy_permission . '" />',
				'value' => $clink_taxonomy_permission
			);
			return $form_fields;
		}
	}
	$right_holder_not_match = false;
	// Is media is linked?
	$linked_media = get_post_meta( $post->ID, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_media ) ) {
		$linked_flag = true;
		$readonly = 'readonly="readonly"';
		$readonly_keywords = $readonly;
		$readonly_title = $readonly;
		$media_permission = get_post_meta( $post->ID, 'wpclink_programmatic_right_categories', true );
		$media_permission_array = explode( ",", $media_permission );
	} else {
		$linked_flag = false;
		$readonly = '';
		$readonly_byline = '';
		$readonly_copy = '';
		$readonly_credit = '';
		if ( wpclink_creator_can_register( $post->ID ) == false ) {
			$readonly = 'readonly="readonly"';
			$readonly_keywords = $readonly;
			$readonly_title = $readonly;
			$readonly_byline = $readonly;
			$readonly_copy = $readonly;
			$readonly_credit = $readonly;
		}
		$registration_disallow = get_post_meta( $post->ID, 'wpclink_registration_disallow', true );
		if ( $registration_disallow == 1 ) {
			$readonly = 'readonly="readonly"';
			$readonly_keywords = $readonly;
			$readonly_title = $readonly;
			$readonly_byline = $readonly;
			$readonly_copy = $readonly;
			$readonly_credit = $readonly;
		}
		
			// Origin Creator for Register Media
			if ( $creationID = get_post_meta( $post->ID, 'wpclink_creation_ID', true ) ) {
				if($right_holder_id = get_post_meta( $post->ID, 'wpclink_rights_holder_user_id', true )){
					if($right_holder_id != $current_user_id){
						$readonly_byline = 'readonly="readonly"';
						$readonly_copy = 'readonly="readonly"';
						$readonly_credit = 'readonly="readonly"';
						
						$right_holder_not_match = true;
					}
				}
			}
		
		
	}
	$clink_taxonomy_permission = get_post_meta( $post->ID, 'wpclink_programmatic_right_categories', true );
	$attachment_url = wpclink_get_image_URL( $post->ID );
	$attachment_url = wpclink_iptc_image_path( $post->ID, 'full' );
	
	
	
	
	if($metadata_image_array = get_post_meta($post->ID,'wpclink_media_cache_metadata',true)){
		// Cache Data
	}else{
		$metadata_image_array = wpclink_get_image_metadata_value( $attachment_url, array( 'IPTC:By-line', 'IPTC:CopyrightNotice', 'IPTC:Credit', 'IPTC:ObjectName', 'IPTC:Keywords', 'XMP-xmpRights:WebStatement', 'XMP-plus:LicensorURL' ) );
	}
	
	
	$scaled_image = get_the_guid( $post->ID );
	
	$metadata_iptc_title = (isset($metadata_image_array['IPTC:ObjectName'])) ? $metadata_image_array['IPTC:ObjectName'] : '';
	if ( $linked_flag ) {} else {
		if ( empty( $metadata_iptc_title ) ) {
			$get_apply_filename = wpclink_get_option( 'wpclink_attachment_apply_filename' );
			if ( $get_apply_filename == "yes" ) {
				$scaled_image = get_the_guid( $post->ID );
				$metadata_iptc_title = basename( $scaled_image );
			}
		}
	}
	// Title
	$form_fields[ "wpclink_image_metadata_title" ] = array(
		'label' => 'IPTC Title',
		'input' => 'html',
		'html' => '<input type="text" id="attachments-' . $post->ID . '-wpclink_image_metadata_title" name="attachments[' . $post->ID . '][wpclink_image_metadata_title]" ' . $readonly . ' value="' . $metadata_iptc_title . '" />',
		'value' => $metadata_iptc_title
	);
	
	if(!isset($media_permission_array)){
		$media_permission_array = array();
	}
	if ( in_array( "ModifyKeywords", $media_permission_array ) ) {
		$readonly_keywords = "";
	}
	
	if(!isset($readonly_keywords)) $readonly_keywords = '';
	
	if(!isset($metadata_image_array[ 'IPTC:Keywords' ] )) $metadata_image_array[ 'IPTC:Keywords' ] = '';
	// Title
	$form_fields[ "wpclink_image_metadata_keywords" ] = array(
		'label' => 'IPTC Keywords',
		'input' => 'html',
		'html' => '<input type="text" id="attachments-' . $post->ID . '-wpclink_image_metadata_keywords" name="attachments[' . $post->ID . '][wpclink_image_metadata_keywords]" ' . $readonly_keywords . ' value="' . (isset($metadata_image_array['IPTC:Keywords']) ? $metadata_image_array['IPTC:Keywords'] : '') . '" />',
		'value' => (isset($metadata_image_array['IPTC:Keywords']) ? $metadata_image_array['IPTC:Keywords'] : '')
	);
	// Author
	$author_id = get_post_field( 'post_author', $post->ID );
	$template_use_enable = get_user_meta( $author_id, 'wpclink_image_metadata_template_use_enable', true );
	if ( wpclink_check_license_by_post_id( $post->ID ) > 0 || $right_holder_not_match ) {
		$template_use_html = NULL;
	} else {
		$template_use_html = "<label><input id='template_use' " . checked( $template_use_enable, "1", false ) . " type='checkbox' value='1' />Use Template</label> <span class='template_action_btn'><a class='template_use_save'>Save</a> <a class='template_use_update'>Update</a></span>";
	}
	if ( $linked_flag ) {
		$template_use_html = '';
	} else {
	
		$creator_name_metadata = (isset($metadata_image_array['IPTC:By-line'])) ? $metadata_image_array['IPTC:By-line'] : '';
		
		$creator_user_id = get_current_user_id();
		// Creator
		$creator_user_info = get_userdata( $creator_user_id );
		// Current Creator Names
		$current_creator_array = array(
			strtolower( $creator_user_info->first_name ),
			strtolower( $creator_user_info->last_name )
		);
		$display_name = $creator_user_info->display_name;
		$display_name_array = explode( ' ', $display_name );
		$display_name_array = array_map( 'strtolower', $display_name_array );
		if ( !empty( $creator_name_metadata ) ) {
			$metadata_creator_names = explode( ' ', $creator_name_metadata );
			
			$creator_name_match = wpclink_match_creator_names(
				$metadata_creator_names,
				$current_creator_array,
				$display_name_array );
		
			$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
		
			
			if ( $creator_name_match_final || current_user_can('editor') || current_user_can('administrator') ) {
				
			} else {
				$disallow = true;
				$template_use_html = '';
			}
		} else {}
	}
	
	$time = time();
	
	// Title IPTC
	$form_fields[ "wpclink_image_metadata_divhtml" ][ "tr" ] = "<tr class='compat-field-wpclink_image_metadata_headline'><td colspan='2'><h3>IPTC Metadata for Google Images <a class='iptc_link' target='_blank' href='https://getpmd.iptc.org/getpmd/html/isearch1/ipmd/?imgurl=" . preg_replace("/^http:/i", "https:", $scaled_image)  . "?view=" . time() . "'>Get PMD at IPTC <span class='dashicons dashicons-external'></span></a> </h3>" . $template_use_html . "</td></tr>";
	// Preview
	$form_fields[ 'wpclink_image_preview' ] = array(
		'label' => '',
		'input' => 'html',
		'html' => wpclink_media_preview_meta_box( $post ),
		'value' => ' ',
		'helps' => ' ',
	);
	
	$metadata_creator_field = (isset($metadata_image_array['IPTC:By-line'])) ? $metadata_image_array['IPTC:By-line'] : '';
	
	$metadata_credit_field = (isset($metadata_image_array['IPTC:Credit'])) ? $metadata_image_array['IPTC:Credit'] : '';
	
	
	// Origin Creator for Register Media
	if ( $creationID = get_post_meta( $post->ID , 'wpclink_creation_ID', true ) ) {
		if($right_holder_id = get_post_meta( $post->ID, 'wpclink_rights_holder_user_id', true )){
			$creator_user_id = $right_holder_id;
		}else{
			$creator_user_id = $current_user_id;
		}
		
	}else{
		$creator_user_id = $current_user_id;
	}
	
	$creator_user_copy_info = get_userdata( $creator_user_id );
	$creator_user_copy_info_display = $creator_user_info->display_name;
	
	$metadata_copyright_field = (isset($metadata_image_array['IPTC:CopyrightNotice'])) ? $metadata_image_array['IPTC:CopyrightNotice'] : 'Copyright '.date('Y').' '.$creator_user_copy_info_display;
	
	if ( $linked_flag == false ) {
		if ( $metadata_creator_field == NULL ) {
			if ( $template_use_enable == "1" ) {
				if ( $template_use_data = get_user_meta( $author_id, 'wpclink_image_metadata_template_use', true ) ) {
					//$metadata_creator_field = $template_use_data[ 'creator' ];
					$metadata_credit_field = $template_use_data[ 'credit_line' ];
					$metadata_copyright_field = $template_use_data[ 'copyright' ];
				}
			}
		}
		if ( $metadata_creator_field == NULL ) {
			$current_user_id = get_current_user_id();
			$creator_user_info = get_userdata( $current_user_id );
			$metadata_creator_field = $creator_user_info->display_name;
		}
	}
	
	
	// Creator
	$form_fields[ "wpclink_image_metadata_creator" ] = array(
		'label' => 'Creator',
		'input' => 'html',
		'html' => '<input type="text" id="attachments-' . $post->ID . '-wpclink_image_metadata_creator" name="attachments[' . $post->ID . '][wpclink_image_metadata_creator]" ' . $readonly_byline . ' value="' . $metadata_creator_field . '" />',
		'value' => $metadata_creator_field
	);
	// Credit
	$form_fields[ "wpclink_image_metadata_credit" ] = array(
		'label' => 'Credit Line',
		'input' => 'html',
		'html' => '<input type="text" id="attachments-' . $post->ID . '-wpclink_image_metadata_credit" name="attachments[' . $post->ID . '][wpclink_image_metadata_credit]" ' . $readonly_credit . ' value="' . $metadata_credit_field . '" />',
		'value' => $metadata_credit_field
	);
	
	// Right Holder Copyright Notice
	
	
	
	// Copyright Notice
	$form_fields[ "wpclink_image_metadata_copyright_notice" ] = array(
		'label' => 'Copyright Notice',
		'input' => 'html',
		'html' => '<input type="text" id="attachments-' . $post->ID . '-wpclink_image_metadata_copyright_notice" name="attachments[' . $post->ID . '][wpclink_image_metadata_copyright_notice]" ' . $readonly_copy . ' value="' . $metadata_copyright_field . '" />',
		'value' => $metadata_copyright_field
	);
	// License Selected Type
	$wpclink_license_selected_type = get_post_meta( $post->ID, 'wpclink_license_selected_type', true );
	// Programatic Rights
	$clink_taxonomy_permission = get_post_meta( $post->ID, 'wpclink_programmatic_right_categories', true );
	if ( $wpclink_license_selected_type == 'wpclink_personal' || $wpclink_license_selected_type == 'wpclink_business' || $wpclink_license_selected_type == 'wpclink_marketplace' ) {
		$clink_taxonomy_permission = get_post_meta( $post->ID, 'wpclink_programmatic_right_categories', true );
		if ( !empty( $clink_taxonomy_permission ) ) {
			if ( $linked_flag ) {
				$custom_url = '<a href="' . $metadata_image_array[ 'XMP-plus:LicensorURL' ] . '" target="_blank">' . $metadata_image_array[ 'XMP-plus:LicensorURL' ] . "</a>";
				// Right ID
				$custom_web_statement_rights = get_post_meta( $post->ID, 'wpclink_right_ID', true );
				$custom_web_statement_rights = "<a target='_blank' href='" . WPCLINK_ID_URL . "/#objects/" . $custom_web_statement_rights . "'>" . $custom_web_statement_rights . "</a>";
			} else {
				// Author
				$author_id = get_post_field( 'post_author', $post->ID );
				$creator_identifier = get_user_meta( $author_id, 'wpclink_party_ID', true );
				$custom_url = '<a href="' . WPCLINK_ID_URL . '/#objects/' . $creator_identifier . '" target="_blank">' . $creator_identifier . "</a>";
				if ( wpclink_is_reuse_guid() ) {
					$custom_url = wpclink_reuse_guid_url( $post->ID );
					$custom_url = "<a target='_blank' href='" . $custom_url . "'>" . $custom_url . "</a>";
				} else {
				}
			}
			$custom_web_statement_rights = get_post_meta( $post->ID, 'wpclink_right_ID', true );
			$custom_web_statement_rights = "<a target='_blank' href='" . WPCLINK_ID_URL . "/#objects/" . $custom_web_statement_rights . "'>" . $custom_web_statement_rights . "</a>";
		}
	} else if ( $wpclink_license_selected_type == 'custom' ) {
		// Custom URL
		$custom_url = get_post_meta( $post->ID, 'wpclink_custom_url', true );
		$custom_url = "<a target='_blank' href='" . $custom_url . "'>" . $custom_url . "</a>";
		// Custom Web Statement Rights
		$custom_web_statement_rights = get_post_meta( $post->ID, 'wpclink_custom_web_statement_rights', true );
		$custom_web_statement_rights = "<a target='_blank' href='" . $custom_web_statement_rights . "'>" . str_replace( WPCLINK_ID_URL . '/#objects/', '', $custom_web_statement_rights ) . "</a>";
	} else {
		if ( $linked_flag ) {
			$referent_creator_party_ID = get_post_meta( $post->ID, 'wpclink_referent_creator_party_ID', true );
			$custom_url = $referent_creator_party_ID;
			$custom_url = '<a href="' . $metadata_image_array[ 'XMP-plus:LicensorURL' ] . '" target="_blank">' . $metadata_image_array[ 'XMP-plus:LicensorURL' ] . "</a>";
			// Right ID
			$custom_web_statement_rights = get_post_meta( $post->ID, 'wpclink_right_ID', true );
			$custom_web_statement_rights = "<a target='_blank' href='" . WPCLINK_ID_URL . "/#objects/" . $custom_web_statement_rights . "'>" . $custom_web_statement_rights . "</a>";
		} else {
			$registration_disallow = get_post_meta( $post->ID, 'wpclink_registration_disallow', true );
			if ( $registration_disallow == 1 ) {
				// Metadata
				$webstatement_rights = $metadata_image_array[ 'XMP-xmpRights:WebStatement' ];
				$custom_url = $metadata_image_array[ 'XMP-plus:LicensorURL' ];
				// URL Match
				$url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
				// Webstatement
				$custom_web_statement_rights = preg_replace( $url, '<a href="$0" target="_blank">$0</a>', $webstatement_rights );
				// Licensor URL
				$custom_url = preg_replace( $url, '<a href="$0" target="_blank">$0</a>', $custom_url );
			} else {
				$custom_url = '';
				$custom_web_statement_rights = '';
			}
		}
	}
	
	if ( !empty( $custom_web_statement_rights ) ) {
		// Horizontal Line
		$form_fields["horizontal_line"]["tr"] = "<tr><td colspan='2'><hr /></td></tr>";
	}
	
	if ( !empty( $custom_web_statement_rights ) ) {
		// Web statements of rights
		$form_fields[ "wpclink_image_metadata_web_statement_rights" ] = array(
			'label' => 'Web Statement of Rights',
			'input' => 'html',
			'html' => "<a target='_blank' class='iptc_ico dashicons dashicons-visibility' href='https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#web-statement-of-rights'></a>" . $custom_web_statement_rights,
			'value' => ''
		);
	}
	if ( !empty( $custom_url ) ) {
		// Custom URL
		$form_fields[ "wpclink_custom_url" ] = array(
			'label' => 'Licensor URL',
			'input' => 'html',
			'html' => "<a target='_blank' class='iptc_ico dashicons dashicons-visibility' href='http://ns.useplus.org/LDF/ldf-XMPSpecification#LicensorURL'></a>" . $custom_url,
			'value' => ''
		);
	}
	
	if ( $linked_flag ) {
		$clink_taxonomy_permission = str_replace( ",", "_", $clink_taxonomy_permission ) . '-linked';
	}
	$registration_disallow = get_post_meta( $post->ID, 'wpclink_registration_disallow', true );
	if ( $registration_disallow == 1 ) {
		$clink_taxonomy_permission = 'disallow';
	}
	// Disallow
	if ( $disallow ) {
		$clink_taxonomy_permission = 'disallow';
	}
	if ( wpclink_check_license_by_post_id( $post->ID ) > 0 ) {
		$clink_taxonomy_permission = 'referent_creation';
	}
	// Taxonomy
	$form_fields[ "wpclink_image_taxonomy_permission" ] = array(
		'label' => '',
		'input' => 'html',
		'html' => '<input type="hidden" id="attachments-' . $post->ID . '-wpclink_image_taxonomy_permission" name="attachments[' . $post->ID . '][wpclink_image_taxonomy_permission]" value="' . $clink_taxonomy_permission . '" />',
		'value' => $clink_taxonomy_permission
	);
	
	$nonce = wp_create_nonce( 'wpclink_metadata_update' );
	
	// Nonce
	$form_fields[ "wpclink_metadata_nonce" ] = array(
		'label' => '',
		'input' => 'html',
		'html' => '<input type="hidden" id="attachments-' . $post->ID . '-wpclink_metadata_nonce" name="attachments[' . $post->ID . '][wpclink_metadata_nonce]" value="' . $nonce . '" />',
		'value' => $nonce
	);
	return $form_fields;
}
// attach our function to the correct hook
add_filter( "attachment_fields_to_edit", "wpclink_display_iptc_image_metadata", NULL, 2 );
function wpclink_custom_license_form($post_id = 0){
	
	// Custom URL
	$custom_url = get_post_meta( $post_id, 'wpclink_custom_url', true );
	
	// Web Statement of Rights
	$custom_web_statement_rights = get_post_meta( $post_id, 'wpclink_custom_web_statement_rights', true );
	
	// Button Label
	$wpclink_license_button = get_post_meta( $post_id, 'wpclink_license_button', true );
	
	// Right Objec ID
	$right_object_id = get_post_meta( $post_id, 'wpclink_right_object', true );
	
	// License Selected Type
	$wpclink_license_selected_type = get_post_meta( $post_id, 'wpclink_license_selected_type', true );
	
	if($wpclink_license_selected_type == "custom"){
		$license_button = "Update";
	}else{
		$license_button = "Apply";
	}
	
	
	?>
<div id="wpclink-custom-license">
<input type="hidden" name="wpclink_right_object_wrapper" value="1"/>
	<p><input type="text" class="widefat" value="<?php if(!empty($custom_web_statement_rights)) echo $custom_web_statement_rights; ?>" id="wpclink_custom_web_statement_rights" placeholder="Web Statement of Rights" name="wpclink_custom_web_statement_rights"/><label class="wpclink_right_object" for="wpclink_right_object"><input type="checkbox" name="wpclink_right_object" id="wpclink_right_object" <?php checked( $right_object_id, 1 );  ?> value="1" /> Right Object URL</label></p>
	
	<p><input id="custom_url" type="text" value="<?php if(!empty($custom_url)) echo $custom_url; ?>" placeholder="Licensor URL" name="wpclink_custom_url" class="widefat"/></p>
	
	<p><label> Reuse button label <select name="wpclink_license_button" id="wpclink_license_button" class="widefat">
	<option value="na" <?php selected( $wpclink_license_button, 'na' ); ?>>N/A</option>
	<option value="getrights" <?php selected( $wpclink_license_button, 'getrights' ); ?>>Get rights</option>
	<option value="getimage" <?php selected( $wpclink_license_button, 'getimage' ); ?>>Get image</option></select></label></p>
	</p>
	<div class="cl-footer-widget">
		<div class="licenses-action">
		<div class="status-mini-bar"><div class="loader_spinner_cover"><div class="loader_spinner"></div><div class="cl_loader_status_mini_license">Applying license...</div></div></div>
		<input name="save" type="button" class="button" id="apply-custom-license" value="<?php echo $license_button; ?>">
</div>
			</div>
</div>
<?php
}
/**
 * CLink Media License Box Form
 *
 * @param post $post The post object
 */
function wpclink_media_license_meta_box( $post ) {
	// make sure the form request comes from WordPress
	//wp_nonce_field( basename( __FILE__ ), 'wpclink_media_license_meta_box_nonce' );
	
	$current_user_id = get_current_user_id();
	
	
	// Only Jpeg
	$type = get_post_mime_type( $post->ID );
	if ( $type == 'image/jpeg' || $type == 'image/jpg' ) {} else {
		return false;
	}
	
	// Disable CLink Feature due to error
	$continue_to_image = get_post_meta( $post->ID, 'wpclink_continue_to_image', true );
	
	/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
	
	if ( $continue_to_image_final == 1 ) {
		return false;
	} 
	
	// GUID
	$guid = get_the_guid($post->ID);
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));  
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];
	// Is media is linked?
	$linked_media = get_post_meta( $post->ID, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_media ) ) {
		$linked_flag = true;
	} else {
		$linked_flag = false;
	}
	// Right Transaction
	if ( $get_right_transaction_ID = get_post_meta( $post->ID, 'wpclink_right_ID', true ) ) {
		$clink_get_right_id = WPCLINK_ID_URL . '/#objects/' . $get_right_transaction_ID;
	}
	// Pre Authorized License Date	
	$pre_auth_effect_date = get_post_meta( $post->ID, 'wpclink_reuse_pre_auth_effective_date', true );
	// License Selected Type
	$wpclink_license_selected_type = get_post_meta( $post->ID, 'wpclink_license_selected_type', true );
	// Custom URL
	$custom_url = get_post_meta( $post->ID, 'wpclink_custom_url', true );
	// Custom Web Statement Rights
	$custom_web_statement_rights = get_post_meta( $post->ID, 'wpclink_custom_web_statement_rights', true );
	
	
	if($linked_flag == false){
		if($wpclink_license_selected_type == "wpclink_personal" and  wpclink_attachment_in_attachment_referent_list( $post->ID ) || $wpclink_license_selected_type == "wpclink_business" and  wpclink_attachment_in_attachment_referent_list( $post->ID ) || $wpclink_license_selected_type == "wpclink_marketplace" and  wpclink_attachment_in_attachment_referent_list( $post->ID )   ){ 
			if ( $creationID = get_post_meta( $post->ID, 'wpclink_creation_ID', true ) ) {
				if($right_holder_id = get_post_meta( $post->ID, 'wpclink_rights_holder_user_id', true )){
					if($right_holder_id != $current_user_id){
						
if($wpclink_license_selected_type == 'wpclink_personal'){
	$license_type = 'Personal';
    $license_ver = '0.9i';
}else if($wpclink_license_selected_type == 'wpclink_business'){
	$license_type = 'Business';
    $license_ver = '0.9i';
}else if($wpclink_license_selected_type == 'wpclink_marketplace'){
	$license_type = 'Markeplace';
    $license_ver = '0.9im';
}else{
	$license_type = 'Personal';
    $license_ver = '0.9i';
}
?><div class='inside'>
		<div class="license-slot">
			<div><?php echo $license_type; ?>| Version: <?php echo $license_ver; ?></div>
			<div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a>
			</div><span class="small-label category onwire"><a onclick="cl_present_popup_media()">Right Categories</a> <span class="icon-box" title="Creation will be available for reuse under the selected Right Categories when the license gets applied"></span></span>
		</div>
		<div class="license-date-slot">
			<?php echo wpclink_convert_date_to_iso($pre_auth_effect_date); ?>
	</div></div>
		<?php
			return;
		
					}
				}
			}
		}
	}
	
	?>
	<div class='inside'>
		<?php 
	
	
	if($linked_flag){
		
		// Right Transation
		$referent_license_data = wpclink_get_license_by_linked_post_id($post->ID);
		$license_id = $referent_license_data['license_id'];
		$license_data_new = wpclink_get_license_linked($license_id);
		$license_rights_transaction_ID = $license_data_new['rights_transaction_ID']; 
		
		?>
		<div class="license-version-slot">
			<?php if($sync_origin = get_post_meta( $post->ID, 'wpclink_referent_post_uri', true )){ echo '<a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$license_rights_transaction_ID.'">'.wpclink_do_icon_clink_ID($license_rights_transaction_ID).'</a>'; } ?>
		</div>
		<?php
		if ( $sync_origin = get_post_meta( $post->ID, 'wpclink_referent_post_uri', true ) ) {
			echo '<a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a>';
		}
		} else {
			// Registration
			$registration_disallow = get_post_meta( $post->ID, 'wpclink_registration_disallow', true );
			if ( $registration_disallow == 1 ) {
				return false;
			}
			$attachment_url = wpclink_iptc_image_path( $post->ID, 'full' );
		
			// Meta data Creator Names	
			if($metadata_image_array = get_post_meta($post->ID,'wpclink_media_cache_metadata',true)){
				// Cache Data
			}else{
				$metadata_image_array = wpclink_get_image_metadata_value( $attachment_url, array( 'IPTC:By-line', 'IPTC:CopyrightNotice', 'IPTC:Credit', 'IPTC:ObjectName', 'IPTC:Keywords' ) );
			}
		
			$creator_name_metadata = (isset($metadata_image_array['IPTC:By-line'])) ? $metadata_image_array['IPTC:By-line'] : '';
			$current_user_id = get_current_user_id();
			// Creator
			$creator_user_info = get_userdata( $current_user_id );
			// Current Creator Names
			$current_creator_array = array(
				strtolower( $creator_user_info->first_name ),
				strtolower( $creator_user_info->last_name )
			);
			$display_name = $creator_user_info->display_name;
			$display_name_array = explode( ' ', $display_name );
			$display_name_array = array_map( 'strtolower', $display_name_array );
			if ( !empty( $creator_name_metadata ) ) {
				
			$metadata_creator_names = explode( ' ', $creator_name_metadata );
				
			$creator_name_match = wpclink_match_creator_names(
						$metadata_creator_names,
						$current_creator_array,
						$display_name_array );
				
				/*
			 * Apply the Creator Name Match
			 *
			 * - 'creator_name_match' is the value being filtered. */
			$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
				
				
				if ( $creator_name_match_final ) {} else {
					return false;
				}
			} else {
			}
			// If not selected license and post is not in referent list
			if ( 
				(empty( $wpclink_license_selected_type ) and !wpclink_attachment_in_attachment_referent_list( $post->ID )) || 
				( $wpclink_license_selected_type  == 'wpclink_personal' and !wpclink_attachment_in_attachment_referent_list( $post->ID))) {
				// Select Dropdown ?>
		<select id="wpclink_license_selected_type" name="wpclink_license_selected_type" class="widefat">
			<?php do_action('wpclink_license_select_dropdown_before'); ?>
			<option value="wpclink_personal" <?php selected( $wpclink_license_selected_type, 'wpclink_personal' ); ?>>
				<?php _e('CLink Personal'); ?>
			</option>
			<option value="custom" <?php selected( $wpclink_license_selected_type, 'custom' ); ?>>
				<?php _e('Custom'); ?>
			</option>
		</select>
		<?php
		}
		// None Selected
		if ( empty( $wpclink_license_selected_type ) and
		  !wpclink_attachment_in_attachment_referent_list( $post->ID )) {
			
			// Programmatic Form
			wpclink_programmatic_license_form();
			// Custom Form
			wpclink_custom_license_form($post->ID);
			// License Form
			do_action('wpclink_license_form_html');
			
		// Programattic - Not Selected
	   }else if($wpclink_license_selected_type == "wpclink_personal" and 
			   !wpclink_attachment_in_attachment_referent_list( $post->ID )){
			
			// Programmatic Form
			wpclink_programmatic_license_form();
			// Custom Form
			wpclink_custom_license_form($post->ID);
			// License Form
			do_action('wpclink_license_form_html');
			
		// Programattic - Selected
	   }else if(($wpclink_license_selected_type == "wpclink_personal" || $wpclink_license_selected_type == "wpclink_business" || $wpclink_license_selected_type == "wpclink_marketplace" ) and 
			   wpclink_attachment_in_attachment_referent_list( $post->ID )){
		
	
if($wpclink_license_selected_type == 'wpclink_personal'){
	$license_type = 'Personal';
    $license_ver = '0.9i';
}else if($wpclink_license_selected_type == 'wpclink_business'){
	$license_type = 'Business';
    $license_ver = '0.9i';
}else if($wpclink_license_selected_type == 'wpclink_marketplace'){
	$license_type = 'Markeplace';
    $license_ver = '0.9im';
}else{
	$license_type = 'Personal';
    $license_ver = '0.9i';
}
		
		
		?>
		<div class="license-slot">
			<div><?php echo $license_type; ?> | License: <?php echo $license_ver; ?></div>
			<div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a>
			</div><span class="small-label category onwire"><a onclick="cl_present_popup_media()">Right Categories</a> <span class="icon-box" title="Creation will be available for reuse under the selected Right Categories when the license gets applied"></span></span>
		</div>
		<div class="license-date-slot">
			<?php echo wpclink_convert_date_to_iso($pre_auth_effect_date); ?>
		</div>
		<?php
		// Custom - Selected
		} else if ( $wpclink_license_selected_type == "custom" ) {
			wpclink_custom_license_form($post->ID);
		}
	} ?>
	</div>
	<?php
}
function wpclink_programmatic_license_form(){
	?><div id="wpclink-programmatic-license"><div class="license-slot">
			Personal | Version: 0.9i
			<div class="small-label"><a href="https://licenses.clink.id/" target="_blank">Learn about licenses <span class="dashicons dashicons-external"></span></a>
			</div>
			<div class="none-slot"><span class="small-label clink_id category">Right Categories <span class="icon-box" title="Creation will be available for reuse under the selected Right Categories when the license gets applied"></span></span>
			</div>
			<div class="small-label">
				<div class="small-label"><span class="underline">Programmatic </span>
				</div>
				<div><span class="fix-90">Right Type</span>
					<div class="clink_permission_wrapper">
						<label><input type="checkbox" name="select_license_type" value="ModifyHeadline"> Modify Headline</label><br>
						<label><input id="quick-license-type"  type="checkbox" name="select_license_type" value="ModifyDescription"> Modify Description </label><br>
						<label>
			<input type="checkbox" name="select_license_type" value="ModifyKeywords"> Modify Keywords </label></div>
				</div>
				<div class="small-label"><a class="present" onclick="cl_present_popup_media()">Presets</a>
				</div>
				<input type="hidden" value="personal" name="license_class" class="license_class">
				
			</div>
			<div class="license-version-slot"></div>
			<div class="canonical-slot"></div>
			<div class="license-date-slot"></div>
		
			<div class="status-slot cl-footer-widget">
				
				<div class="licenses-action">
					<div class="status-mini-bar"><div class="loader_spinner_cover"><div class="loader_spinner"></div><div class="cl_loader_status_mini_license">Applying license...</div></div></div>
					 <input type="button" class="select-license button" value="Apply">
					
				</div>
				</div>
		</div>
</div>
<?php
}
/**
 * CLink IPTC Metadata for Google Image Search Preview
 *
 * @param post $post The post object
 */
function wpclink_media_metadata_box( $post ) {
	// Only Jpeg
	$media_type = get_post_mime_type( $post->ID );
	if ( $media_type == 'image/jpeg' || $media_type == 'image/jpg' ) {} else {
		return false;
	}
	
	// Disable CLink Feature due to error
	$continue_to_image = get_post_meta( $post->ID, 'wpclink_continue_to_image', true );
	
	/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
	
	if ( $continue_to_image_final == 1 ) {
		return false;
	} 
	
	// CLink ID
	if($contentID = get_post_meta( $post->ID, 'wpclink_creation_ID', true )){
		$content_url = WPCLINK_ID_URL.'/#objects/'.$contentID;		
	}
	// Created DAte
	$get_post_date  = get_the_date('Y-m-d',$post->ID);
	$get_post_date .= 'T';
	$get_post_date .= get_the_date('G:i:s',$post->ID);
	$get_post_date .= 'Z';
	$created_time = $get_post_date;
    
     if(wpclink_is_c2pa_data_exists($post->ID)){ 
         $image_url = wp_get_attachment_image_src( $post->ID, 'full' );
         echo wpclink_show_metadata_tree($image_url[0], $created_time, $content_url, $post->ID);
     }else{
		echo '<p>No C2PA Data</p>';
     }
	
}
/**
 * CLink IPTC Metadata for Google Image Search Preview
 *
 * @param post $post The post object
 */
function wpclink_media_preview_meta_box( $post ) {
	// Only Jpeg
	$media_type = get_post_mime_type( $post->ID );
	if ( $media_type == 'image/jpeg' || $media_type == 'image/jpg' ) {} else {
		return false;
	}
	
	// Disable CLink Feature due to error
	$continue_to_image = get_post_meta( $post->ID, 'wpclink_continue_to_image', true );
	
	/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
	
	if ( $continue_to_image_final == 1 ) {
		return false;
	} 
	
	// GUID
	$guid = get_the_guid($post->ID);
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));  
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];
	
	// Is media is linked?
	$linked_media = get_post_meta( $post->ID, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_media ) ) {
		$linked_flag = true;
	} else {
		$linked_flag = false;
	}
/*
	if ( $content_register_restrict = get_post_meta( $post->ID, 'wpclink_post_register_status', true ) ) {
		if ( $content_register_restrict == '1' ) {
			return false;
		}
	}
*/
	$attachment_url = wpclink_iptc_image_path( $post->ID, 'full' );
	
	
	if($metadata_image_array = get_post_meta($post->ID,'wpclink_media_cache_metadata',true)){
		// Cache Data
	}else{
		$metadata_image_array = wpclink_get_image_metadata_value( $attachment_url, array( 'IPTC:By-line', 'IPTC:CopyrightNotice', 'IPTC:Credit', 'IPTC:ObjectName', 'IPTC:Keywords', 'XMP-xmpRights:WebStatement', 'XMP-plus:LicensorURL' ) );
	}
	
	
	// Image URL
	$img_url = wpclink_get_image_URL( $post->ID );
	// Title
	$img_title = get_the_title( $post->ID );
	// Excerpt
	$img_caption = wp_get_attachment_caption( $post->ID );
	// Creator
	$img_creator = (isset($metadata_image_array['IPTC:By-line'])) ? $metadata_image_array['IPTC:By-line'] : ''; 
	// Credit
	$img_credit = (isset($metadata_image_array['IPTC:Credit'])) ? $metadata_image_array['IPTC:Credit'] : ''; 
	// Copyright Notice	
	$copyright_notice = (isset($metadata_image_array['IPTC:CopyrightNotice'])) ? $metadata_image_array['IPTC:CopyrightNotice'] : ''; 
	// Licensor URL
	$img_licensor = 'http://www.google.com';
	// Web Statement of Rights
	$img_web_statement = 'http://www.google.com/details';
	// Seprator
	$sep = '|';
	
	
	
	if ( $linked_flag ) {
		$site_name = get_post_meta( $post->ID, 'wpclink_referent_site_name', true );
		
		$licensor_url = (isset($metadata_image_array['XMP-plus:LicensorURL'])) ? $metadata_image_array['XMP-plus:LicensorURL'] : '';
		$visit_link = $licensor_url;
		$custom_url = '<a class="gfield-licensor-link gfield" href="' . $licensor_url . '" target="_blank">' . $site_name . "</a>";
		
		$custom_web_statement_rights = (isset($metadata_image_array['XMP-xmpRights:WebStatement' ])) ? $metadata_image_array['XMP-xmpRights:WebStatement' ] : '';
	} else {
		$site_name = get_bloginfo( 'name' );
		// License Selected Type
		$wpclink_license_selected_type = get_post_meta( $post->ID, 'wpclink_license_selected_type', true );
		if ( $wpclink_license_selected_type == 'wpclink_personal' || $wpclink_license_selected_type == 'wpclink_business' || $wpclink_license_selected_type == 'wpclink_marketplace' ) {
			// Author
			$author_id = get_post_field( 'post_author', $post->ID );
			$creator_identifier = get_user_meta( $author_id, 'wpclink_party_ID', true );
			$custom_url = '<a class="gfield-licensor-link gfield" href="' . WPCLINK_ID_URL . '/#objects/' . $creator_identifier . '" target="_blank">' . $site_name . "</a>";
			$visit_link = WPCLINK_ID_URL . '/#objects/' . $creator_identifier;
			if ( wpclink_is_reuse_guid() ) {
				$custom_url = wpclink_reuse_guid_url( $post->ID );
				$visit_link = $custom_url;
				$custom_url = "<a class='gfield-licensor-link gfield'  target='_blank' href='" . $custom_url . "'>" . $site_name . "</a>";
			} else {}
			$custom_web_statement_rights = get_post_meta( $post->ID, 'wpclink_right_ID', true );
			$custom_web_statement_rights = WPCLINK_ID_URL . "/#objects/" . $custom_web_statement_rights;
		} else if ( $wpclink_license_selected_type == 'custom' ) {
			// Custom URL
			$custom_url = get_post_meta( $post->ID, 'wpclink_custom_url', true );
			$visit_link = $custom_url;
			$custom_url = '<a class="gfield-licensor-link gfield" href="' . $custom_url . '" target="_blank">' . $site_name . "</a>";
			// Custom Web Statement Rights
			$custom_web_statement_rights = get_post_meta( $post->ID, 'wpclink_custom_web_statement_rights', true );
			$custom_web_statement_rights = $custom_web_statement_rights;
		} else {
			$registration_disallow = get_post_meta( $post->ID, 'wpclink_registration_disallow', true );
			if ( $registration_disallow == 1 ) {
				
				// Metadata
							
				$webstatement_rights = (isset($metadata_image_array['XMP-xmpRights:WebStatement'])) ? $metadata_image_array['XMP-xmpRights:WebStatement'] : '';
				
				$custom_url = (isset($metadata_image_array['XMP-plus:LicensorURL'])) ? $metadata_image_array['XMP-plus:LicensorURL'] : '';
				
				$visit_link = $custom_url;
				// URL Match
				$url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
				// Webstatement
				$custom_web_statement_rights = $webstatement_rights;
				// Licensor URL
				$custom_url = preg_replace( $url, '<a href="$0" class="gfield-licensor-link gfield" target="_blank">' . $site_name . '</a>', $custom_url );
				
				
			} else {
				$custom_url = '';
				$custom_web_statement_rights = '';
			}
		}
	}
	// Licensor Link
	$img_licensor_link = $custom_url;
	// Image Web Statement
	$img_web_statement_link = '<a target="_blank" class="gfield-web-statement gfield" href="' . $custom_web_statement_rights . '">License details</a>';
	$img_credits_text = ''; 
	$img_license_text = '';
	$img_license_text_cc = '';
	if ( !empty( $img_creator ) )$img_credits_text = 'Creator: <span class="gfield-creator gfield">' . $img_creator . '</span> ' . $sep . ' ';
	if ( !empty( $img_credit ) )$img_credits_text .= 'Credit: <span class="gfield-credit gfield">' . $img_credit . '</span>';
	
	$wpclink_license_selected_type = (isset($wpclink_license_selected_type)) ? $wpclink_license_selected_type : '';
	if ( !empty( $img_licensor ) || !empty( $img_web_statement ) ) {
		if ( wpclink_attachment_in_attachment_referent_list( $post->ID ) || $wpclink_license_selected_type == 'custom' || $linked_flag || $registration_disallow ) {
			$img_license_text .= 'Get this image on: ';
			$img_license_text .= $img_licensor_link . ' ' . $sep . ' ';
			$img_license_text .= $img_web_statement_link;
		} else {
			
		}
		
		if(!empty($copyright_notice)){
			$img_license_text_cc = '<p>Copyright: ' . $copyright_notice.'</p>';
		}
	}
	$caption_image_html = "";
	if ( wpclink_attachment_in_attachment_referent_list( $post->ID ) || $wpclink_license_selected_type == 'custom' || $linked_flag || $registration_disallow ) {
		$learnmore = '<a target="_blank" href="https://support.google.com/websearch/answer/9789430?p=image_info&visit_id=637349060582785814-4243710723&rd=1">Learn more</a>';
		$img_sample_text = 'Want to know where this information comes from? ' . $learnmore;
	} else {
		$img_sample_text = 'Information extracted from <a href="https://iptc.org/" target="_blank">IPTC</a> Photo Metadata';
	}
	$creationID = get_post_meta( $post->ID, 'wpclink_creation_ID', true );
	
		$creator_name_metadata = $img_creator;
		
		$creator_user_id = get_current_user_id();
		// Creator
		$creator_user_info = get_userdata( $creator_user_id );
		// Current Creator Names
		$current_creator_array = array(
			strtolower( $creator_user_info->first_name ),
			strtolower( $creator_user_info->last_name )
		);
		$display_name = $creator_user_info->display_name;
		$display_name_array = explode( ' ', $display_name );
		$display_name_array = array_map( 'strtolower', $display_name_array );
		if ( !empty( $creator_name_metadata ) ) {
			$metadata_creator_names = explode( ' ', $creator_name_metadata );
			
			$creator_name_match = wpclink_match_creator_names(
				$metadata_creator_names,
				$current_creator_array,
				$display_name_array );
		
			
			if ( $creator_name_match_final ) {
				
			}else{
				$not_creator = true;
			}
		} 
	
	if($registration_disallow){
		$image_metadata = '<p>' . $img_license_text . '</p><p>' . $img_credits_text . '</p>'.$img_license_text_cc.'<p>' . $img_sample_text . '</p>';
	}else if(empty( $creationID) and $not_creator ){
		$image_metadata = '<p>' . $img_license_text . '</p><p>' . $img_credits_text . '</p>'.$img_license_text_cc.'<p>' . $img_sample_text . '</p>';
	}else if ( empty( $creationID) ) {
		$image_metadata = '<p>Images may be subject to copyright. <a href="https://iptc.org/standards/photo-metadata" target="_blank">Learn More</a></p>';
	} else {
		$image_metadata = '<p>' . $img_license_text . '</p><p>' . $img_credits_text . '</p>'.$img_license_text_cc.'<p>' . $img_sample_text . '</p>';
	}
	if ( wpclink_attachment_in_attachment_referent_list( $post->ID ) ) {
		$visit_hlink = wpclink_reuse_guid_url( $post->ID );
	} else {
		$visit_hlink = get_attachment_link( $post->ID );
	}
	$html = '<div class="inside">
		<div id="wpclink-preview-box">
			<div id="image-layer">
				<div class="shadow">
				<svg viewBox="0 0 24 24" focusable="false" class="actions-icons icon-share" height="24px" width="24px"><path d="M0 0h24v24H0z" fill="none"></path><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"></path></svg>
				<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" class="actions-icons icon-bookmark"><path d="M34 6H14c-2.21 0-3.98 1.79-3.98 4L10 42l14-6 14 6V10c0-2.21-1.79-4-4-4zm0 30l-10-4.35L14 36V10h20v26z"></path></svg></div>
				
				<img src="' . $img_url . '" /></div>
			<div class="image-details"><div class="image-title"><h2 class="title">' . $img_title . '</h2>
				<div class="image-hyperlink"><svg viewBox="0 0 24 24" focusable="false" class="icon-world"><path d="M0 0h24v24H0z" fill="none"></path><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"></path></svg><span class="site-name"><a href="' . $visit_hlink . '">Visit</a></span></div></div>
			<div class="image-metadata">' . $image_metadata . '</div></div>
		</div>
	</div>';
	return $html;
}
/**
 * CLink Add Media License Boxes
 *
 * @param post $post The post object
 */
function wpclink_media_add_meta_boxes( $post ) {
	// Only Jpeg
	$type_media = get_post_mime_type( $post->ID );
	if ( $type_media == 'image/jpeg' || $type_media == 'image/jpg' ) {} else {
		return false;
	}
	
	// Disable CLink Feature due to error
	$continue_to_image = get_post_meta( $post->ID, 'wpclink_continue_to_image', true );
	
	/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
	if ( $continue_to_image_final == 1 ) {
		return false;
	} 
	
	// GUID
	$guid = get_the_guid($post->ID);
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));  
	
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];
	
	$creationID = get_post_meta( $post->ID, 'wpclink_creation_ID', true );
	// Excluded
	if ( $content_register_restrict = get_post_meta( $post->ID, 'wpclink_post_register_status', true ) ) {
		if ( $content_register_restrict == '1' ) {
			return false;
		}
	}
	// Is media is linked?
	$linked_media = get_post_meta( $post->ID, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_media ) ) {
		$linked_flag = true;
	} else {
		$linked_flag = false;
	}
	if ( $linked_flag ) {} else {
		$attachment_url = wpclink_iptc_image_path( $post->ID, 'full' );
		
		
		
	if($metadata_image_array = get_post_meta($post->ID,'wpclink_media_cache_metadata',true)){
		// Cache Data
	}else{
		$metadata_image_array = wpclink_get_image_metadata_value( $attachment_url, array( 'IPTC:By-line', 'IPTC:CopyrightNotice', 'IPTC:Credit', 'IPTC:ObjectName', 'IPTC:Keywords' ) );
	}
	
		
		$creator_name_metadata = (isset($metadata_image_array[ 'IPTC:By-line' ])) ? $metadata_image_array[ 'IPTC:By-line' ] : '';
		
		$current_user_id = get_current_user_id();
		// Creator
		$creator_user_info = get_userdata( $current_user_id );
		// Current Creator Names
		$current_creator_array = array(
			strtolower( $creator_user_info->first_name ),
			strtolower( $creator_user_info->last_name )
		);
		$display_name = $creator_user_info->display_name;
		$display_name_array = explode( ' ', $display_name );
		$display_name_array = array_map( 'strtolower', $display_name_array );
		if ( !empty( $creator_name_metadata ) ) {
			
			
			
			$metadata_creator_names = explode( ' ', $creator_name_metadata );
			
			$creator_name_match = wpclink_match_creator_names(
					$metadata_creator_names,
					$current_creator_array,
					$display_name_array );
			
			
			/*
			 * Apply the Creator Name Match
			 *
			 * - 'creator_name_match' is the value being filtered. */
			$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
			
			if ( $creator_name_match_final || current_user_can('editor') || current_user_can('administrator') ) {} else {
				return false;
			}
		} else {
		}
	}
	if ( !empty( $creationID ) ) {
		// Register
		add_meta_box( 'wpclink_media_license', __( 'License', 'cl_text' ), 'wpclink_media_license_meta_box', 'attachment', 'side', 'low' );
	}
	if ( $linked_flag ) {
		// Register
		add_meta_box( 'wpclink_media_links', __( 'Links', 'cl_text' ), 'wpclink_media_links_meta_box', 'attachment', 'side', 'low' );
	} else if ( wpclink_check_license_by_post_id( $post->ID ) > 0 ) {
		// Register
		add_meta_box( 'wpclink_media_links', __( 'Links', 'cl_text' ), 'wpclink_media_links_meta_box', 'attachment', 'side', 'low' );
	}
	// Metadata Box
		add_meta_box( 'wpclink_media_metadata_box', __( 'C2PA', 'cl_text' ), 'wpclink_media_metadata_box', 'attachment', 'side', 'low' );
         
     
	
	// Right Type
	$wpclink_license_selected_type = get_post_meta( $post->ID, 'wpclink_license_selected_type', true );
	
	if($linked_flag == false and ((empty( $wpclink_license_selected_type) || $wpclink_license_selected_type == 'wpclink_personal')  and !wpclink_attachment_in_attachment_referent_list( $post->ID ))){
		
		if ( $creationID = get_post_meta( $post->ID, 'wpclink_creation_ID', true ) ) {
			
				if($right_holder_id = get_post_meta( $post->ID, 'wpclink_rights_holder_user_id', true )){
					
					if($right_holder_id != $current_user_id){
						
						remove_meta_box( 'wpclink_media_license', 'attachment', 'side', 'low' );
					}
				}
		}
	}
}
add_action( 'add_meta_boxes_attachment', 'wpclink_media_add_meta_boxes' );
/**
* CLink Media License Box Form
*
* @param post $post The post object
*/
function wpclink_media_links_meta_box( $post ) {
	// Only Jpeg
	$type_media = get_post_mime_type( $post->ID );
	if ( $type_media == 'image/jpeg' || $type_media == 'image/jpg' ) {} else {
	return false;
	}
	// Disable CLink Feature due to error
	$continue_to_image = get_post_meta( $post->ID, 'wpclink_continue_to_image', true );
	
	/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
	
	if ( $continue_to_image_final == 1 ) {
	return false;
	} 
	
	// GUID
	$guid = get_the_guid($post->ID);
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));  
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];
	
	// make sure the form request comes from WordPress
	//wp_nonce_field( basename( __FILE__ ), 'wpclink_media_license_meta_box_nonce' );
	//wp_nonce_field( basename( __FILE__ ), 'wpclink_media_license_meta_box_nonce' );
	if ( wpclink_check_license_by_post_id( $post->ID ) > 0 ) {
	// License Data
	$linked_creation_ID_list = array();
	$license_data_creation = wpclink_get_all_liceses_by_post_id( $post->ID );
		foreach ( $license_data_creation as $license_data_creation_single ) {
			if ( $license_linked_creation_ID = wpclink_get_license_meta( $license_data_creation_single[ 'license_id' ], 'linked_creation_ID', true ) ) {
			$linked_creation_ID_list[] = $license_linked_creation_ID;
			}
		}
	$linked_creation_count = count( $linked_creation_ID_list );
	$creation_label = ( $linked_creation_count > 1 ) ? 'Creations' : 'Creation';
	$html_creation_link = '';
	$count_linkeds = 0;
	foreach ( $linked_creation_ID_list as $single_links_creation ) {
	$html_creation_link .= '<a target="_blank" class="refid-hyperlink"  href="' . WPCLINK_ID_URL . '/#objects/' . $single_links_creation . '">' . wpclink_do_icon_clink_ID( $single_links_creation ) . '</a>';
		if ( $count_linkeds >= 3 ) break;
		$count_linkeds++;
		}
	// All Links Page
	$inbound_menu = menu_page_url( 'clink-links-inbound', false );
	$inbound_menu = add_query_arg( array( 'filter_by' => 'post', 'post_id' => $post->ID, 'type' => 'attachment' ), $inbound_menu );
	?>
	<div class="ref-urls-slot"><span class="small-label underline">Linked <?php echo $creation_label; ?></span>
	<?php //echo $links_html;
	?>
	</div>
	<div class="ref-clinkid-slot">
	<?php echo $html_creation_link; ?>
	</div><span class="small-label underline seemore"><a href="<?php echo $inbound_menu; ?>">See More <span class="dashicons dashicons-plus"></span>
	</a>
	</span>
	<?
	}else{
	// Referent Creation ID
	if($referent_id = get_post_meta( $post->ID, 'wpclink_referent_creation_ID', true )){
	if(empty($referent_id)){
		$referent_clinkid = 'N/A';
	}else{
		$referent_clinkid = '<a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$referent_id.'">'.wpclink_do_icon_clink_ID($referent_id).'</a>';
	}
	if($sync_origin = get_post_meta( $post->ID, 'wpclink_referent_post_uri', true )){
		$referent_url = '<a target="_blank" href="'.$sync_origin.'">'.$sync_origin.' <span class="dashicons dashicons-external"></span></a>';
	}	
	}
	?>
	<div><span class="small-label underline">Referent Creation</span>
	<div class="ref-clinkid-slot">
	<?php echo $referent_clinkid; ?>
	</div>
	<div class="ref-urls-slot">
	<?php echo $referent_url; ?>
	</div>
	</div>
	<?php
	}
}
/**
* CLink Check Image Right Information
*
* @param post $post_id The post id
*/
function wpclink_check_image_right_info() {
$screen = get_current_screen();
$screen_id = $screen->id;
if(isset($_GET['post']) and 
$screen_id == 'attachment'){
 $post_id = $_GET['post'];
	// Disable CLink Feature due to error
	$continue_to_image = get_post_meta( $post_id, 'wpclink_continue_to_image', true );
	
	
	/*		 * Apply Continue to Image
			 *
			 * 'continue_to_image' is the value being filtered. */
			$continue_to_image_final = apply_filters( 'wpclink_continue_to_image_filter', $continue_to_image);
	
	if ( $continue_to_image_final == 1 ) {
		return false;
	} 
	
	// GUID
	$guid = get_the_guid($post_id);
	// SITE URL
	$short_url = parse_url(get_bloginfo('url'));
	
	$url_without_pt = $short_url['host'];
	if(isset($short_url['path'])) $url_without_pt .= $short_url['path'];
	if(isset($short_url['query'])) $url_without_pt .= $short_url['query'];
	
	
$type = get_post_mime_type( $post_id );
if ( $type == 'image/jpeg' || $type == 'image/jpg' ) {
	// Is media is linked?
	$linked_media = get_post_meta( $post_id, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_media ) ) {
		return false;
	}
$check_reg = get_post_meta($post_id,'wpclink_check_image_registration',true);
	
/*
* Apply Continue to Image
*
* 'continue_to_image' is the value being filtered. */
$check_reg_final = apply_filters( 'wpclink_check_image_registration_filter', $check_reg);
	
// Not Disallow First Time Added
if( $check_reg_final ){
		// Get Image Metadata
		$attachment_url = wpclink_iptc_image_path( $post_id, 'full' );
		$metadata_image_array = wpclink_get_image_metadata_value( $attachment_url,
			array( 'XMP-xmpRights:WebStatement',
				'XMP-plus:LicensorURL' ) );
		// CLink Registration Disallow for already license image
		if ( !empty( $metadata_image_array[ 'XMP-xmpRights:WebStatement' ] ) ||
			!empty( $metadata_image_array[ 'XMP-plus:LicensorURL' ] ) ) {
			update_post_meta( $post_id, 'wpclink_registration_disallow', 1 );
			update_post_meta( $post_id, 'wpclink_check_image_registration', 0 );
		} else {
			delete_post_meta( $post_id, 'wpclink_registration_disallow');
			update_post_meta( $post_id, 'wpclink_check_image_registration', 0 );
		}
}
}
}
}
add_action( 'current_screen', 'wpclink_check_image_right_info' );
/**
* CLink Convert Latin Characters Filenames to English 
*
* @param filename $filename Name of the file
*
* @return converted filename
*/
function wpclink_convert_latin_filename( $filename ) {
	if ( function_exists( 'transliterator_transliterate' ) ) {
		$info = pathinfo( $filename );
		$ext = ( isset( $info[ 'extension' ] ) && !empty( $info[ 'extension' ] ) ) ? '.' . $info[ 'extension' ] : '';
		$name = basename( $filename, $ext );
		$filter_latin = transliterator_transliterate( 'Any-Latin; Latin-ASCII', $name );
		
	if ( empty( $filter_latin ) || $filter_latin == false ) {
		$filter_latin = $name;
	}
	return $filter_latin . $ext;
	} else {
	return $filename;
	}
}
add_filter( 'sanitize_file_name', 'wpclink_convert_latin_filename', 10 );
/**
* CLink Check Image Right Information Initiate
*
* @param post $post_id The post id
*/
function wpclink_check_image_right_info_initiate( $post_id = 0 ) {
	$type = get_post_mime_type( $post_id );
	if ( $type == 'image/jpeg' || $type == 'image/jpg' ) {
	// Is media is linked?
	$linked_media = get_post_meta( $post_id, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_media ) ) {
		return false;
	}
	update_post_meta( $post_id, 'wpclink_check_image_registration', 1 );
	update_post_meta( $post_id, 'wpclink_check_exif_data', 1 );
		
	}
}
add_action( 'add_attachment', 'wpclink_check_image_right_info_initiate', 10, 1 );
/**
* CLink Media Liberary License Check
*
* @param $form_fields attachment fileds
* @param $post the post object
*/
function wpclink_media_liberary_license_check( $form_fields, $post ) {
	
	//Linked Media
	if($sync_origin = get_post_meta( $post->ID, 'wpclink_referent_post_uri', true )){
		
	$clink_taxonomy_permission = get_post_meta( $post->ID, 'wpclink_programmatic_right_categories', true );
		
	$clink_permissions = explode(',',$clink_taxonomy_permission);
		
	if(!in_array('ModifyHeadline',$clink_permissions)){
		$attachment_field[] = ' #attachment-details-title';
		
	}
	if(!in_array('ModifyDescription',$clink_permissions)){
		$attachment_field[] =  ' #attachment-details-caption';
	}
		
	if(!in_array('ModifyDescription',$clink_permissions)){
		$attachment_field[] =  ' #attachment-details-description';
	}
	
	
	if(!empty($attachment_field)){
	// Script only works when image is referent and licensed
	$script = 'jQuery( "'.implode(',',$attachment_field).'" ).prop( "disabled", true );';
	
	// Adding the tag field
    $form_fields['wpclink_license_check'] = array( 
        'label' => '',
        'input'  => 'html',
        'html' => '<script>'.$script.'</script>'
    );
		
	}
		
	}
	
	return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'wpclink_media_liberary_license_check', 10, 2 );