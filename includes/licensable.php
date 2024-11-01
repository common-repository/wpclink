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
 * CLink Find Image by URL
 * 
 * @param string $image_url  Image URL
 * 
 * @return boolean if found true else false
 */
function wpclink_find_image_id_by_url($image_url = ''){
	
	$upload_dir = wp_get_upload_dir();
	
		$postslist = get_posts( array(
			'posts_per_page' => -1,
			'order'          => 'ASC',
			'orderby'        => 'title',
			'post_type'		 => array('attachment'),
			'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash') 
	) );
	if ( $postslist ) {
		foreach ( $postslist as $post ) :
		
		$image_data = wp_get_attachment_metadata($post->ID);
		
		$image_guid = get_the_guid($post->ID);
		$image_scale = wp_get_attachment_url($post->ID);
		
		
		$image_url = strtok((string)$image_url, '?');
		
		if(!empty($image_url)){
			if(strpos($image_guid, $image_url) !== false){
				return $post->ID;
			}
		}
		
		if(!empty($image_url)){
			if(strpos($image_scale, $image_url) !== false){
				return $post->ID;
			}
		}
		
		
		
			
		/* Set up an empty array for the links. */
	$links = array();
	/* Get the intermediate image sizes and add the full size to the array. */
	$sizes = get_intermediate_image_sizes($post->ID);
	$sizes[] = 'full';
	/* Loop through each of the image sizes. */
	foreach ( $sizes as $size ) {
		/* Get the image source, width, height, and whether it's intermediate. */
		$image = wp_get_attachment_image_src( $post->ID , $size );
		/* Add the link to the array if there's an image and if $is_intermediate (4th array value) is true or full size. */
		if ( !empty( $image ) && ( true == $image[3] || 'full' == $size ) )
			$links[] = "$image[0]";
		
		
		if(!empty($image_url)){
		if(strpos((string)$image[0], (string)$image_url) !== false){
				return $post->ID;
			} else{
			}
		}
		
		
	}
		
	
	
	$found_image_id = apply_filters( 'wpclink_image_find_by_src', 0, $post->ID, $image_url);
	if($found_image_id > 0 ){
		return $found_image_id;
	}
		
	
		//print_r($image_urls);
		
		endforeach;
		
			
		
	}
}
/**
 * CLink Get Image Attributes
 * 
 * @param integer $attachment  attachment id
 * 
 * @return array attributes
 */
function wpclink_get_image_attributes($attachment_id = 0){
	
	// Data
	$clink_array = array();
	
	// CLink ID
	if($contentID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true )){
		$clink_array['creation_ID'] = $contentID;
		$clink_array['creation_ID_url'] = WPCLINK_ID_URL.'/#objects/'.$contentID;		
	}
		
	// Reuse
	$registered_list_attachment = wpclink_get_option('referent_attachments');
	if(!is_array($registered_list_attachment)) $registered_list_attachment = array();
	
	if(in_array($attachment_id, $registered_list_attachment)){
		$clink_array['reuse'] = 1;
		$clink_array['reuse_domain'] = 'same_domain';
		
		if($sync_origin = get_post_meta( $attachment_id, 'wpclink_referent_post_uri', true )){
			if(empty($sync_origin)){
				
				$media_license_url = get_bloginfo( 'url' );
				$media_license_url = add_query_arg( 'clink_media_license', '', $media_license_url );
				$media_license_url = add_query_arg( 'id', $attachment_id, $media_license_url );
				$reuse_GUID = add_query_arg( 'clink', 'offer', $media_license_url );
				
				$clink_array['reuse_url'] = urlencode($reuse_GUID);
				
			}else{
				$clink_array['reuse_url'] = urlencode($sync_origin);
			}
		}else{
			
				$media_license_url = get_bloginfo( 'url' );
				$media_license_url = add_query_arg( 'clink_media_license', '', $media_license_url );
				$media_license_url = add_query_arg( 'id', $attachment_id, $media_license_url );
				$reuse_GUID = add_query_arg( 'clink', 'offer', $media_license_url );
			
				$clink_array['reuse_url'] = urlencode($reuse_GUID);
		}
		
		
		
		$clink_array['license_terms'] = 'https://licenses.clink.id/personal/0-9i';
		
	}else if($sync_origin = get_post_meta( $attachment_id, 'wpclink_referent_post_uri', true )){
		if(!empty($sync_origin)){
			
			// Custom URL
			$custom_url = get_post_meta( $attachment_id, 'wpclink_custom_url', true );
			
			$clink_array['reuse'] = 1;
			$custom_url_reuse = add_query_arg( 'clink', 'offer', $custom_url );
			$clink_array['reuse_url'] = urlencode($custom_url_reuse);
			$clink_array['orgin_url'] = urlencode($custom_url);
			
			
			
		}else{
		
		}
	}else{
		
		
		
	}
	
	// Linked
	
	if($sync_origin = get_post_meta( $attachment_id, 'wpclink_referent_post_uri', true )){
		
		// Custom URL
		$custom_url = get_post_meta( $attachment_id, 'wpclink_custom_url', true );

		$clink_array['reuse'] = 1;
		$custom_url_reuse = add_query_arg( 'clink', 'offer', $custom_url );
		$clink_array['reuse_url'] = urlencode($custom_url_reuse);
		
			
		// Publish At
		if($referent_creation_ID = get_post_meta( $attachment_id, 'wpclink_referent_creation_ID', true )){
			$clink_array['orign_creation_ID'] = $referent_creation_ID;
			$clink_array['orign_creation_url'] = WPCLINK_ID_URL.'/#objects/'.$referent_creation_ID;
			
			$clink_array['origin_rights_holder_ID_url'] = 'ABC.com';	;
			
		}
		
		
		// Publish At Time
		if($wpclink_time_of_creation =  get_post_meta( $attachment_id, 'wpclink_time_of_creation', true )){
				$clink_array['created_timestamp'] = $wpclink_time_of_creation;
		}
		
		
		// Republish At
		if($contentID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true )){
			$clink_array['republish_creation_ID'] = $contentID;
			$clink_array['republish_creation_ID_url'] = WPCLINK_ID_URL.'/#objects/'.$contentID;	
			
			
			if($wpclink_rights_holder_user_id =  get_post_meta( $attachment_id, 'wpclink_rights_holder_user_id', true )){
				$creator_user_data = get_userdata($wpclink_rights_holder_user_id);
				$creator_user_display = $creator_user_data->display_name;
				$clink_array['republish_rights_holder'] = $creator_user_display;
			}
			
			
			$clink_array['republish_rights_holder_url'] = WPCLINK_ID_URL.'/#objects/'.$contentID;	
		}
		
		
		
		// Rights Holder URL
		if($wpclink_right_ID = get_post_meta( $attachment_id, 'wpclink_right_ID', true )){
				$clink_array['right_ID'] = WPCLINK_ID_URL.'/#objects/'.$wpclink_right_ID;
			
		}
		if($original_rights_holder =  get_post_meta( $attachment_id, 'wpclink_referent_rights_holder_display_name', true )){
				$clink_array['origin_rights_holder_display_name'] = $original_rights_holder;
		}
		
		$get_post_date  = get_the_date('Y-m-d',$attachment_id);
		$get_post_date .= 'T';
		$get_post_date .= get_the_date('G:i:s',$attachment_id);
		$get_post_date .= 'Z';
		
		$clink_array['republish_created_timestamp'] = $get_post_date;
		
		
		
		
		
	}else{
		
		// Referent
		
		$get_post_date  = get_the_date('Y-m-d',$attachment_id);
		$get_post_date .= 'T';
		$get_post_date .= get_the_date('G:i:s',$attachment_id);
		$get_post_date .= 'Z';
		
		$clink_array['created_timestamp'] = $get_post_date;
		
		// CLink ID
		if($orign_contentID = get_post_meta( $attachment_id, 'wpclink_creation_ID', true )){
			$clink_array['orign_creation_ID'] = $orign_contentID;
			$clink_array['orign_creation_url'] = WPCLINK_ID_URL.'/#objects/'.$orign_contentID;		
		}
		
		if($rights_holder_id = get_post_meta( $attachment_id, 'wpclink_rights_holder_user_id', true )){
			
			// Display Name
			$creator_user_data = get_userdata($rights_holder_id);
			$creator_user_display = $creator_user_data->display_name;
			$clink_array['origin_rights_holder_display_name'] = $creator_user_display;
			
			// URL
			$origin_rights_holder_ID = get_user_meta($rights_holder_id,'wpclink_party_ID',true);
			$clink_array['origin_rights_holder_ID_url'] = WPCLINK_ID_URL.'/#objects/'.$origin_rights_holder_ID;	;
		}
		
		// Version CLink ID(s)
		if($clink_versions = get_post_meta( $attachment_id, 'wpclink_versions', true )){
			if(is_array($clink_versions)){
				$clink_array['origin_versions'] = count($clink_versions);
			}
		}
		// Right ID
		if($wpclink_right_ID = get_post_meta( $attachment_id, 'wpclink_right_ID', true )){
				$clink_array['right_ID'] = WPCLINK_ID_URL.'/#objects/'.$wpclink_right_ID;
		}
		
		
		
	}
	
	
	
	
	
	$license_type = get_post_meta( $attachment_id, 'wpclink_license_selected_type', true );
	if($license_type == 'custom'){
		$custom_license_value = get_post_meta( $attachment_id, 'wpclink_custom_url', true );
		$clink_array['custom-license'] = $custom_license_value;
	}
	
	$clink_array['use_mouse_over'] = 1;
	
	return $clink_array;
	
}
/**
 * CLink Licensable DOM HTML
 * 
 * @param string $html Content
 * 
 * @return string Content
 */
function wpclink_licensble_images_start($html,$post_id = 0){
	
	// Enable error catch
	//libxml_use_internal_errors(TRUE);
	
	// UTF-8 Doc Document
	$html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.$html;
	
	$doc = new DOMDocument('1.0', 'UTF-8');
	$doc->substituteEntities = TRUE;
    // If faild to load HTML
	if(@$doc->loadHTML($html) === false){
		return $html;
	}
	
	// Catch LibXML errors
	//$errors = libxml_get_errors();
	
	$image_linked_array = array();
	
	
	//Create new wrapper div
	$new_div = $doc->createElement('div');
	$new_div->setAttribute('class','clink-imagebox');
	
	
	
	
		
    $images= $doc->getElementsByTagName('img');
    foreach ($images as $image) {
		
		// Unique ID
		$uniq_id = uniqid();
	
		if($image->hasAttribute('src')){
			
			
			//Clone our created div
    		$new_div_clone = $new_div->cloneNode();
			
			
			// Source Attribute
			$src_attr = $image->getAttribute('src');
			
			
			
			// Remove Query
			//$src_attr = strtok((string)$src_attr, '?');
			
			//var_dump($src_attr);
			
			// Attachment ID
			$attach_id = wpclink_find_image_id_by_url((string)$src_attr);
			
			// Remove Query
			$src_attr = strtok((string)$src_attr, '?');
			
			
			//var_dump($attach_id);
			
			// is Attachment
			if($attach_id > 0){
				
				
				$clink_attr = wpclink_get_image_attributes($attach_id);
				$show_button = get_post_meta( $attach_id, 'wpclink_license_button', true );
				
				if(isset($clink_attr['creation_ID'])){
					if(!empty($clink_attr['creation_ID'])){
						
						
						$image_linked_array[$src_attr]['creation_ID'] = $clink_attr['creation_ID'];
						
						$media_license_url = get_bloginfo( 'url' );
						$media_license_url = add_query_arg( 'clink_media_license', '', $media_license_url );
						$media_license_url = add_query_arg( 'id', $attach_id, $media_license_url );
					
						$image_linked_array[$src_attr]['orgin_url'] = urlencode($media_license_url);
                      
                      
						if($image->hasAttribute('data-creation-id')){
							$image->setAttribute('src', $clink_attr['creation_ID']);
						}else{
							
							$doc_attr = $doc->createAttribute('data-creation-id');
							$doc_attr->value = wpclink_strip_prefix_clink_ID($clink_attr['creation_ID']);
						
							// Append it to the document itself 
							$image->appendChild($doc_attr); 
						}
						
					
									
					if(!empty($clink_attr['orgin_url'])){		
						if($image->hasAttribute('data-referent-post-url')){
										$image->setAttribute('data-referent-post-url', $clink_attr['orgin_url']);
									}else{
										$doc_attr = $doc->createAttribute('data-referent-post-url');
										$doc_attr->value = $clink_attr['orgin_url'];
										// Append it to the document itself 
										$image->appendChild($doc_attr); 
									}
					}
				
			
                      
                      
					  $archive_link = wpclink_get_last_archive($attach_id);  
						
                      if(!empty($archive_link)){
                        
                        $image_linked_array[$src_attr]['archive_link'] = $archive_link;
                        
                      }
                      
                      
						if(!empty($clink_attr['creation_ID_url'])){
							
							$image_linked_array[$src_attr]['creation_ID_url'] = $clink_attr['creation_ID_url'];
							if($image->hasAttribute('data-creation-id-url')){
								$image->setAttribute('src', $clink_attr['creation_ID_url']);
							}else{
								$doc_attr = $doc->createAttribute('data-creation-id-url');
								$doc_attr->value = $clink_attr['creation_ID_url'];
								// Append it to the document itself 
								$image->appendChild($doc_attr); 
							}
							
						}
						
						
						
					if(isset($clink_attr['reuse'])){
							if(!empty($clink_attr['reuse'])){
								
								$image_linked_array[$src_attr]['reuse'] = $clink_attr['reuse'];
								
								$image_linked_array[$src_attr]['reuse_url'] = $clink_attr['reuse_url'];
								
								if($image->hasAttribute('data-reuse')){
									$image->setAttribute('data-reuse', $clink_attr['reuse']);
								}else{
									$doc_attr = $doc->createAttribute('data-reuse');
									$doc_attr->value = $clink_attr['reuse'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
								
								if($image->hasAttribute('data-reuse-url')){
									$image->setAttribute('data-reuse-url', $clink_attr['reuse_url']);
								}else{
									$doc_attr = $doc->createAttribute('data-reuse-url');
									$doc_attr->value = $clink_attr['reuse_url'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
							}
						}
						
					
					if(!empty($show_button) and $show_button != 'na'){
						
						
						if(isset($clink_attr['custom-license'])){
							if(!empty($clink_attr['custom-license'])){
								
								// Custom License
								$image_linked_array[$src_attr]['custom-license'] = $clink_attr['custom-license'];
								// Button Label
								$image_linked_array[$src_attr]['button-label'] = $show_button;
								
								// Custom License			
								if($image->hasAttribute('data-custom-license')){
									$image->setAttribute('data-custom-license', $clink_attr['custom-license']);
								}else{
									$doc_attr = $doc->createAttribute('data-custom-license');
									$doc_attr->value = $clink_attr['custom-license'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
								
								// Button Label
								if($image->hasAttribute('data-button-label')){
									$image->setAttribute('data-button-label', $show_button);
								}else{
									$doc_attr = $doc->createAttribute('data-button-label');
									$doc_attr->value = $show_button;
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
								
								
							}
						}
					}else{
						$image_linked_array[$src_attr]['custom-license'] = '';
						// Button Label
						$image_linked_array[$src_attr]['button-label'] = 'na';
					}
						
						if(isset($clink_attr['use_mouse_over'])){
							if(!empty($clink_attr['use_mouse_over'])){	
								
								$image_linked_array[$src_attr]['use_mouse_over'] = $clink_attr['use_mouse_over'];
								
								if($image->hasAttribute('data-use-mouse-over')){
									$image->setAttribute('data-use-mouse-over', $clink_attr['use_mouse_over']);
								}else{
									$doc_attr = $doc->createAttribute('data-use-mouse-over');
									$doc_attr->value = $clink_attr['use_mouse_over'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
							}
						}
					
						
					}
				}
				
				
				// Save the data for images contains in post
				$get_post_date  = get_the_date('Y-m-d',$attach_id);
				$get_post_date .= 'T';
				$get_post_date .= get_the_date('G:i:s',$attach_id);
				$get_post_date .= 'Z';

				$image_linked_array[$src_attr]['created_timestamp'] = $get_post_date;

				// CLink ID
				if($orign_contentID = get_post_meta( $attach_id, 'wpclink_creation_ID', true )){
					$image_linked_array[$src_attr]['orign_creation_ID'] = $orign_contentID;
					$image_linked_array[$src_attr]['orign_creation_url'] = WPCLINK_ID_URL.'/#objects/'.$orign_contentID;		
				}

				if($rights_holder_id = get_post_meta( $attach_id, 'wpclink_rights_holder_user_id', true )){

					// Display Name
					$creator_user_data = get_userdata($rights_holder_id);
					$creator_user_display = $creator_user_data->display_name;
					$image_linked_array[$src_attr]['origin_rights_holder_display_name'] = $creator_user_display;

					// URL
					$origin_rights_holder_ID = get_user_meta($rights_holder_id,'wpclink_party_ID',true);
					$image_linked_array[$src_attr]['origin_rights_holder_ID_url'] = WPCLINK_ID_URL.'/#objects/'.$origin_rights_holder_ID;	;
				}

				// Version CLink ID(s)
				if($clink_versions = get_post_meta( $attach_id, 'wpclink_versions', true )){
					if(is_array($clink_versions)){
						$image_linked_array[$src_attr]['origin_versions'] = count($clink_versions);
					}
				}
				// Right ID
				if($wpclink_right_ID = get_post_meta( $attach_id, 'wpclink_right_ID', true )){
						$image_linked_array[$src_attr]['right_ID'] = WPCLINK_ID_URL.'/#objects/'.$wpclink_right_ID;
				}
		
			}else{
				
				$class = $image->getAttribute('class');
				preg_match('/wp-image-[0-9]+/', $class, $content);
				
				$content = str_replace('wp-image-','',$content);
				
				if(is_numeric($content[0])){

				$attach_id =$content[0];
				
				$clink_attr = wpclink_get_image_attributes($attach_id);
				
				if(isset($clink_attr['creation_ID'])){
					if(!empty($clink_attr['creation_ID'])){
						
						
						$image_linked_array[$src_attr]['creation_ID'] = $clink_attr['creation_ID'];
						
						$media_license_url = get_bloginfo( 'url' );
						$media_license_url = add_query_arg( 'clink_media_license', '', $media_license_url );
						$media_license_url = add_query_arg( 'id', $attach_id, $media_license_url );
					
						$image_linked_array[$src_attr]['orgin_url'] = urlencode($media_license_url);
                      
                      
						if($image->hasAttribute('data-creation-id')){
							$image->setAttribute('src', $clink_attr['creation_ID']);
						}else{
							
							$doc_attr = $doc->createAttribute('data-creation-id');
							$doc_attr->value = wpclink_strip_prefix_clink_ID($clink_attr['creation_ID']);
						
							// Append it to the document itself 
							$image->appendChild($doc_attr); 
						}
                      
						// Archive
					  $archive_link = wpclink_get_last_archive($attach_id);  
                      if(!empty($archive_link)){
                        $image_linked_array[$src_attr]['archive_link'] = $archive_link;
                      }
                      
                      
						if(!empty($clink_attr['creation_ID_url'])){
							
							$image_linked_array[$src_attr]['creation_ID_url'] = $clink_attr['creation_ID_url'];
							if($image->hasAttribute('data-creation-id-url')){
								$image->setAttribute('src', $clink_attr['creation_ID_url']);
							}else{
								$doc_attr = $doc->createAttribute('data-creation-id-url');
								$doc_attr->value = $clink_attr['creation_ID_url'];
								// Append it to the document itself 
								$image->appendChild($doc_attr); 
							}
							
						}
						
						
						
					if(isset($clink_attr['reuse'])){
							if(!empty($clink_attr['reuse'])){
								
								$image_linked_array[$src_attr]['reuse'] = $clink_attr['reuse'];
								
								$image_linked_array[$src_attr]['reuse_url'] = $clink_attr['reuse_url'];
								
								if($image->hasAttribute('data-reuse')){
									$image->setAttribute('data-reuse', $clink_attr['reuse']);
								}else{
									$doc_attr = $doc->createAttribute('data-reuse');
									$doc_attr->value = $clink_attr['reuse'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
								
								if($image->hasAttribute('data-reuse-url')){
									$image->setAttribute('data-reuse-url', $clink_attr['reuse_url']);
								}else{
									$doc_attr = $doc->createAttribute('data-reuse-url');
									$doc_attr->value = $clink_attr['reuse_url'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
							}
						}
						
						if($show_button == 1){
							if(isset($clink_attr['custom-license'])){
								if(!empty($clink_attr['custom-license'])){

									$image_linked_array[$src_attr]['custom-license'] = $clink_attr['custom-license'];


									if($image->hasAttribute('data-custom-license')){
										$image->setAttribute('data-custom-license', $clink_attr['custom-license']);
									}else{
										$doc_attr = $doc->createAttribute('data-custom-license');
										$doc_attr->value = $clink_attr['custom-license'];
										// Append it to the document itself 
										$image->appendChild($doc_attr); 
									}

									}
							}
						}else{
							$image_linked_array[$src_attr]['custom-license'] = '';
						}
						
						
						if(isset($clink_attr['use_mouse_over'])){
							if(!empty($clink_attr['use_mouse_over'])){	
								
								$image_linked_array[$src_attr]['use_mouse_over'] = $clink_attr['use_mouse_over'];
								
								if($image->hasAttribute('data-use-mouse-over')){
									$image->setAttribute('data-use-mouse-over', $clink_attr['use_mouse_over']);
								}else{
									$doc_attr = $doc->createAttribute('data-use-mouse-over');
									$doc_attr->value = $clink_attr['use_mouse_over'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
							}
						}
					
						
					}
				}
				
				
			

				}
				
				/* Save data for post contains the image */
				$get_post_date  = get_the_date('Y-m-d',$attach_id);
				$get_post_date .= 'T';
				$get_post_date .= get_the_date('G:i:s',$attach_id);
				$get_post_date .= 'Z';

				$image_linked_array[$src_attr]['created_timestamp'] = $get_post_date;

				// CLink ID
				if($orign_contentID = get_post_meta( $attach_id, 'wpclink_creation_ID', true )){
					$image_linked_array[$src_attr]['orign_creation_ID'] = $orign_contentID;
					$image_linked_array[$src_attr]['orign_creation_url'] = WPCLINK_ID_URL.'/#objects/'.$orign_contentID;		
				}

				if($rights_holder_id = get_post_meta( $attach_id, 'wpclink_rights_holder_user_id', true )){

					// Display Name
					$creator_user_data = get_userdata($rights_holder_id);
					$creator_user_display = $creator_user_data->display_name;
					$image_linked_array[$src_attr]['origin_rights_holder_display_name'] = $creator_user_display;

					// URL
					$origin_rights_holder_ID = get_user_meta($rights_holder_id,'wpclink_party_ID',true);
					$image_linked_array[$src_attr]['origin_rights_holder_ID_url'] = WPCLINK_ID_URL.'/#objects/'.$origin_rights_holder_ID;	;
				}

				// Version CLink ID(s)
				if($clink_versions = get_post_meta( $attach_id, 'wpclink_versions', true )){
					if(is_array($clink_versions)){
						$image_linked_array[$src_attr]['origin_versions'] = count($clink_versions);
					}
				}

				if($wpclink_right_ID = get_post_meta( $attach_id, 'wpclink_right_ID', true )){
						$image_linked_array[$src_attr]['right_ID'] = WPCLINK_ID_URL.'/#objects/'.$wpclink_right_ID;
				}
		
				
			}
		
			
		// Image Attributes
		$clink_attr = wpclink_get_image_attributes($attach_id);		
			
		
			
		// If Registered	
			if(isset($clink_attr['creation_ID'])){
				if(!empty($clink_attr['creation_ID'])){	
					
				// Image wrap div
				$image->parentNode->replaceChild($new_div_clone,$image);

				//Append this image to wrapper div
				$new_div_clone->appendChild($image);

				// Image Panel 
				$image_panel = $doc->createElement("div");
				$image_panel->setAttribute('class','clink-imagepanel');
				$added_image_panel = $new_div_clone->appendChild($image_panel);
					
				$media_links = wpclink_licensable_media_popup($attach_id,$clink_attr);
					
				// Reuse URL 
				if(isset($clink_attr['reuse'])){
					if(!empty($clink_attr['reuse'])){
						$reuse_button = '<a class="cl-get-license" target="_blank" href="'.urldecode($clink_attr['reuse_url']).'">Get a license</a>';
						$media_links.= $reuse_button;
					}
				}
				// Custome License
				if(isset($clink_attr['custom-license'])){
						if(!empty($clink_attr['custom-license'])){

							if(!empty($show_button) and $show_button != 'na'){
								$button_label = $show_button;
							}else{
								$button_label = 'Get a license';
							}
							
							$reuse_button = '<a class="cl-get-license" target="_blank" href="'.urldecode($clink_attr['custom-license']).'">'.$button_label.'</a>';
							$media_links.= $reuse_button;
							
						}
					}
				
					// Add
					wpclink_appendHTML($added_image_panel, $media_links, $uniq_id);

				}
			}

		
	   
	}
	}
	
	if(!empty($image_linked_array)){
		update_post_meta($post_id,'wpclink_media_attributes',$image_linked_array);
	}
	
	return $doc->saveHTML();
	
	
}
/**
 * CLink Linked Licensable DOM HTML
 * 
 * @param string $html Content
 * 
 * @return string Content
 */
function wpclink_licensble_linked_images_start($html,$post_id = 0){
	
	// Enable error catch
	//libxml_use_internal_errors(TRUE);
	
	$html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.$html;
	
	$doc = new DOMDocument('1.0', 'UTF-8');
	$doc->substituteEntities = TRUE;
    // If faild to load HTML
	if(@$doc->loadHTML($html) === false){
		return $html;
	}
	
	
	
	// Catch LibXML errors
	//$errors = libxml_get_errors();
	
	//Create new wrapper div
	$new_div = $doc->createElement('div');
	$new_div->setAttribute('class','clink-imagebox');
	
		
    $images= $doc->getElementsByTagName('img');
    foreach ($images as $image) {
	
		if($image->hasAttribute('src')){
			
			// Unique ID
			$uniq_id = uniqid();
			
			// Source Attribute
			$src_attr = $image->getAttribute('src');
			
			// Remove Query
			$src_attr = strtok((string)$src_attr, '?');
			
			//var_dump($src_attr);
			
			//Clone our created div
    		$new_div_clone = $new_div->cloneNode();
			
			
			
			// is Attachment
			if($attached_image_data = get_post_meta($post_id,'wpclink_referent_media_attributes',true)){
				
				
				$clink_attr = $attached_image_data[$src_attr];
				
				if(isset($clink_attr['creation_ID'])){
					if(!empty($clink_attr['creation_ID'])){
						
						if($image->hasAttribute('data-creation-id')){
							$image->setAttribute('src', $clink_attr['creation_ID']);
						}else{
							
							$doc_attr = $doc->createAttribute('data-creation-id');
							$doc_attr->value = wpclink_strip_prefix_clink_ID($clink_attr['creation_ID']);
						
							// Append it to the document itself 
							$image->appendChild($doc_attr); 
						}
						
						
						if(!empty($clink_attr['creation_ID_url'])){
							
							
							if($image->hasAttribute('data-creation-id-url')){
								$image->setAttribute('src', $clink_attr['creation_ID_url']);
							}else{
								$doc_attr = $doc->createAttribute('data-creation-id-url');
								$doc_attr->value = $clink_attr['creation_ID_url'];
								// Append it to the document itself 
								$image->appendChild($doc_attr); 
							}
							
						}
						


						
						if($image->hasAttribute('data-referent-post-url')){
							$image->setAttribute('data-referent-post-url', $clink_attr['orgin_url']);
						}else{
							$doc_attr = $doc->createAttribute('data-referent-post-url');
							$doc_attr->value = $clink_attr['orgin_url'];
							// Append it to the document itself 
							$image->appendChild($doc_attr); 
						}
				
				
						
					if(isset($clink_attr['reuse'])){
							if(!empty($clink_attr['reuse'])){
								
								$image_linked_array[$src_attr]['reuse'] = $clink_attr['reuse'];
								
								if($image->hasAttribute('data-reuse')){
									$image->setAttribute('data-reuse', $clink_attr['reuse']);
								}else{
									$doc_attr = $doc->createAttribute('data-reuse');
									$doc_attr->value = $clink_attr['reuse'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
								
								if($image->hasAttribute('data-reuse-url')){
									$image->setAttribute('data-reuse-url', $clink_attr['reuse_url']);
								}else{
									$doc_attr = $doc->createAttribute('data-reuse-url');
									$doc_attr->value = $clink_attr['reuse_url'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
							}
						}
						
						
						
						if(isset($clink_attr['custom-license'])){
							if(!empty($clink_attr['custom-license'])){
								
								$image_linked_array[$src_attr]['custom-license'] = $clink_attr['custom-license'];
								
								if($image->hasAttribute('data-custom-license')){
									$image->setAttribute('data-custom-license', $clink_attr['reuse']);
								}else{
									$doc_attr = $doc->createAttribute('data-custom-license');
									$doc_attr->value = $clink_attr['custom-license'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
								
							}
						}
						
							if(isset($clink_attr['button-label'])){
							if(!empty($clink_attr['button-label'])){
								
								$image_linked_array[$src_attr]['button-label'] = $clink_attr['button-label'];
								
								if($image->hasAttribute('data-button-label')){
									$image->setAttribute('data-button-label', $clink_attr['button-label']);
								}else{
									$doc_attr = $doc->createAttribute('data-button-label');
									$doc_attr->value = $clink_attr['button-label'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
								
							}
						}
						
						
						if(isset($clink_attr['use_mouse_over'])){
							if(!empty($clink_attr['use_mouse_over'])){			
								if($image->hasAttribute('data-use-mouse-over')){
									$image->setAttribute('data-use-mouse-over', $clink_attr['use_mouse_over']);
								}else{
									$doc_attr = $doc->createAttribute('data-use-mouse-over');
									$doc_attr->value = $clink_attr['use_mouse_over'];
									// Append it to the document itself 
									$image->appendChild($doc_attr); 
								}
							}
						}
					
						
					}
				}

							
		// If Registered	
			if(isset($clink_attr['creation_ID'])){
				if(!empty($clink_attr['creation_ID'])){	

				$image->parentNode->replaceChild($new_div_clone,$image);

				//Append this image to wrapper div
				$new_div_clone->appendChild($image);

				/* Image Panel */
				$image_panel = $doc->createElement("div");
				$image_panel->setAttribute('class','clink-imagepanel');
				$added_image_panel = $new_div_clone->appendChild($image_panel);
					
				$media_links = wpclink_licensable_media_popup_linked_post($clink_attr,$uniq_id);
					
				/* Reuse URL */
				if(isset($clink_attr['reuse'])){
					if(!empty($clink_attr['reuse'])){
						$reuse_button = '<a class="cl-get-license" target="_blank" href="'.urldecode($clink_attr['reuse_url']).'">Get a license</a>';
						$media_links.= $reuse_button;
					}
				}
					
				if(isset($clink_attr['custom-license'])){
						if(!empty($clink_attr['custom-license'])){

							if(!empty($show_button) and $show_button != 'na'){
								$button_label = $show_button;
							}else{
								$button_label = 'Get a license';
							}
							
							$reuse_button = '<a class="cl-get-license" target="_blank" href="'.urldecode($clink_attr['custom-license']).'">'.$button_label.'</a>';
							$media_links.= $reuse_button;
							
						}
					}
				
				// Add
				wpclink_appendHTML($added_image_panel, $media_links);


					
				}
			}
		
		}		
	}
}
	
	return $doc->saveHTML();
	
}
add_filter( 'the_content', 'wpclink_licenable_images' );
/**
 * CLink Licensable DOM HTML After
 * 
 * @param string $html Content
 * 
 * @return string Content
 */
function wpclink_licenable_images($html){
	
	global $post;
	
	if ( ! is_admin() ) {
		
	if(!isset($post->ID)) return $html;
	
	// Is content is linked?
	$linked_content = get_post_meta( $post->ID, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_content ) ) {
		return $html;
	} 
		return wpclink_licensble_images_start($html,$post->ID);

	}else{
		return $html;	
	}
	
}
add_filter( 'the_content', 'wpclink_licenable_linked_images' );
/**
 * CLink Linked Licensable DOM HTML After
 * 
 * @param string $html Content
 * 
 * @return string Content
 */
function wpclink_licenable_linked_images($html){
	
	global $post;
	
	if ( ! is_admin() ) {
		
	if(!isset($post->ID)) return $html;
		
	// Is content is linked?
	$linked_content = get_post_meta( $post->ID, 'wpclink_referent_post_link', true );
	if ( !empty( $linked_content ) ) {
		if ( ! is_admin() ) {
			return wpclink_licensble_linked_images_start($html,$post->ID);
		}else{
			return $html;
		}
		
	}else{
		return $html;
	}
		
	}else{
		return $html;
	}
	
}

/**
 * CLink Check License When Post Update
 * 
 * @param string $html Content
 * 
 * @return string Content
 */
function wpclink_check_license_from_post_content($html,$post_id = 0){
	
	// Enable error catch
	//libxml_use_internal_errors(TRUE);
	
	// Referent Post
	if($linked_post = get_post_meta($post_id,'wpclink_referent_post_link',true)){
		if(!empty($linked_post)){
			return false;
		}
	}
	
	// Has License
	$all_referent_posts = wpclink_get_option('referent_posts');
	$all_referent_pages = wpclink_get_option('referent_pages');
	
	if(in_array($post_id,$all_referent_posts) || in_array($post_id,$all_referent_pages) ){
		
	}else{
		return false;
	}
	
	// Registered
	if($contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true )){
		
	}else{
		return false;
	}
	
	// UTF-8 Doc Document
	$html = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.$html;
	
	$doc = new DOMDocument('1.0', 'UTF-8');
	$doc->substituteEntities = TRUE;
    // If faild to load HTML
	if(@$doc->loadHTML($html) === false){
		return false;
	}
	
	// Catch LibXML errors
	//$errors = libxml_get_errors();
	
	$image_linked_array = array();
		
    $images= $doc->getElementsByTagName('img');
    foreach ($images as $image) {
	
		if($image->hasAttribute('src')){
			
			// Source Attribute
			$src_attr = $image->getAttribute('src');
			
			
			
			// Remove Query
			//$src_attr = strtok((string)$src_attr, '?');
			
			//var_dump($src_attr);
			
			// Attachment ID
			$attach_id = wpclink_find_image_id_by_url((string)$src_attr);
			
			// Remove Query
			$src_attr = strtok((string)$src_attr, '?');
			
			
			//var_dump($attach_id);
			
			// is Attachment
			if($attach_id > 0){
				
				if($linked_media = get_post_meta($attach_id,'wpclink_referent_post_link',true)){
					if(!empty($linked_media)){
						return $attach_id;
					}
				}
				
			}
			
			
		}

	}
	
	return false;
}
