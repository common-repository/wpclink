<?php
/**
 * CLink Structure Functions 
 *
 * CLink structure and layout functions
 *
 * @package CLink
 * @subpackage System
 */
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Referent Creator Column Value
 * 
 * @param string $column_name column name
 * @param integer $post_ID post id
 * 
 */
function wpclink_ref_creator_column($column_name, $post_ID) {
	if($column_name == 'cl_ref_creator'){
	
		$contentID_ref_creator = get_post_meta( $post_ID, 'wpclink_referent_creator_display_name', true );	
		if ( !empty( $contentID_ref_creator ) ) {
			echo $contentID_ref_creator;
		}else{
			echo 'N/A';	
		}
	}
}
// Register register column on wordpress post olumns value
add_action('manage_posts_custom_column', 'wpclink_ref_creator_column', 1, 2);
add_action('manage_pages_custom_column', 'wpclink_ref_creator_column', 1, 2);
/**
 * CLink Referent Creator Column on WordPress Post Columns
 * 
 * @param string $defaults default column
 * 
 * @return string
 */
function wpclink_ref_creator_column_head($defaults) {
	
	
    $defaults['cl_ref_creator'] = 'Referent Creator';
    return $defaults;
}
// Register clink referent creator column on wordpress post column
add_filter('manage_posts_columns', 'wpclink_ref_creator_column_head');
add_filter('manage_pages_columns', 'wpclink_ref_creator_column_head');
/**
 * CLink Versions on WordPress Post, Page amd Media List
 * 
 * @param string $defaults default column name
 * 
 */
function wpclink_register_column_versions($defaults) {
	
	
    $defaults['clink_versions'] = 'Versions';
    return $defaults;
}
// Register refernet content id function
add_filter('manage_posts_columns', 'wpclink_register_column_versions',9,1);
add_filter('manage_pages_columns', 'wpclink_register_column_versions',9,1);
add_filter('manage_media_columns', 'wpclink_register_column_versions',9,1);
/**
 * CLink Versions  on WordPress Post, Page and Media List
 * 
 * @param string $column_name default column
 * @param integer $post_ID post id
 * 
 */
function wpclink_print_versions_value($column_name, $post_ID) {
	if($column_name == 'clink_versions'){
		
		// Version CLink ID(s)
		if($clink_versions = get_post_meta( $post_ID, 'wpclink_versions', true )){
			
			$post_type = get_post_type($post_ID);
			
			if($clink_versions > 0)	echo '<a class="wpclink_versions_list" data-post-id="'.$post_ID.'"><span class="cl-versions-ico '.$post_type.'"></span> '.count($clink_versions).'</a>';
		}else{
			echo 'N/A';
		}
	}
}
// Register referent content id value function
add_action('manage_posts_custom_column', 'wpclink_print_versions_value', 10, 2);
add_action('manage_pages_custom_column', 'wpclink_print_versions_value', 10, 2);
add_action('manage_media_custom_column', 'wpclink_print_versions_value', 10, 2);
/**
 * CLink Referent Content ID on WordPress Post List
 * 
 * @param string $defaults default column name
 * 
 */
function wpclink_register_column_referent_clink_ID_post_page($defaults) {
	
	
    $defaults['clink_id_ref'] = '<span class="clinkico-24-text"></span>';
    return $defaults;
}
// Register refernet content id function
add_filter('manage_posts_columns', 'wpclink_register_column_referent_clink_ID_post_page');
add_filter('manage_pages_columns', 'wpclink_register_column_referent_clink_ID_post_page');
add_filter('manage_media_columns', 'wpclink_register_column_referent_clink_ID_post_page');
/**
 * CLink Referent Content ID Value on WordPress Post List
 * 
 * @param string $column_name default column
 * @param integer $post_ID post id
 * 
 */
function wpclink_print_referent_clink_ID_posts_pages($column_name, $post_ID) {
	if($column_name == 'clink_id_ref'){
	$contentID_ref = get_post_meta( $post_ID, 'wpclink_referent_creation_ID', true );
	if ( !empty( $contentID_ref ) ) {
		echo '<a href="'.WPCLINK_ID_URL.'/#objects/'.$contentID_ref.'" target="_blank">'.wpclink_strip_prefix_clink_ID($contentID_ref).'</a>';
	}else{
		echo 'N/A';	
	}
	}
}
// Register referent content id value function
add_action('manage_posts_custom_column', 'wpclink_print_referent_clink_ID_posts_pages', 10, 2);
add_action('manage_pages_custom_column', 'wpclink_print_referent_clink_ID_posts_pages', 10, 2);
add_action('manage_media_custom_column', 'wpclink_print_referent_clink_ID_posts_pages', 10, 2);
/**
 * CLink Version List Popup
 * 
 */
function wpclink_version_list_popup(){
	
	// Screen
	$current_screen = get_current_screen();
	if($current_screen->id == 'edit-post' || $current_screen->id == 'post' || $current_screen->id == 'edit-page' || $current_screen->id == 'page' || $current_screen->id == 'upload' || $current_screen->id == 'attachment' ){ ?>
<div id="cl_version_list" class="cl_popup version_list" style="display: none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<span class="dashicons dashicons-move move-pbox"></span>
<div class="inner"><p>No Version</p></div></div>
	<?php }
}
// Register publish content popup on footer of admin 
add_action('admin_footer','wpclink_version_list_popup');
/**
 * CLink Register Media Box
 * 
 */
function wpclink_media_publish_box(){
	
	if(!isset($_GET['post'])) return false;
		
	// Global Post ID
	global $post;
	global $wpCLink_domain_quota;
    $post_id = $post->ID;
	
	
	// Only Jpeg
	 $type_media = get_post_mime_type($post->ID);
	 if($type_media == 'image/jpeg' || $type_media == 'image/jpg'){
	 }else{
		 return false;
	 }
	
	$continue_to_image = get_post_meta( $post->ID, 'wpclink_continue_to_image', true );
	if ( $continue_to_image == 1 ) {
		return false;
	} 
	
	// Is media is linked?
	$linked_media = get_post_meta($post->ID,'wpclink_referent_post_link',true);
	if(!empty($linked_media)){
		$linked_flag = true;
		$readonly = 'readonly="readonly"';
	}else{
		$linked_flag = false;
		$readonly = '';
	}
	
	
	// Party ID
	$current_user_id = get_current_user_id();
	$party_user_id = wpclink_get_option('authorized_contact');
	
	// Right Holder ID
	$right_holder = wpclink_get_option('rights_holder');
	$right_holder_id = $current_user_id;
	
	// Author ID
	$post_author_id = get_post_field( 'post_author', $post_id );
	
	
	// Only for Attachments
	 $current_screen = get_current_screen();
	 if($current_screen->id == 'attachment'){
	 }else{
		return false; 
	 }
	
	// Image Path
	$attachment_url =  wpclink_iptc_image_path($post_id,'full');
	$attachment_meta = wp_get_attachment_metadata($post_id);
	
	
	// Only for Creator
	$current_user_id = get_current_user_id();
	$reg_creators = wpclink_get_option('authorized_creators');
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$creator_user_id = $current_user_id;
	}
	
		
	// Creator
	$creator_user_info = get_userdata($current_user_id);
	
	// Current Creator Names
	$current_creator_array = array(
		strtolower($creator_user_info->first_name),
		strtolower($creator_user_info->last_name)
	);
	
	$display_name = $creator_user_info->display_name;
	$display_name_array = explode(' ',$display_name);
	$display_name_array = array_map( 'strtolower', $display_name_array );
	
	// Meta data Creator Names
	$creator_name_metadata = wpclink_get_image_metadata_value($attachment_url,'IPTC:By-line');
	
	// Just initiate
	$license_link_script = '';
	
	// Registration
	$registration_disallow = get_post_meta($post_id,'wpclink_registration_disallow',true);
	if($registration_disallow == 1){
		return false;
	}
	
	if($linked_flag){
		// Linked Content
	}else{
		if(!empty($creator_name_metadata)){
			$metadata_creator_names = explode(' ',$creator_name_metadata);
			
			
			$creator_name_match = wpclink_match_creator_names(
				$metadata_creator_names,
				$current_creator_array,
				$display_name_array);
			
			
			/*
			* Apply the Creator Name Match
			*
			* - 'creator_name_match' is the value being filtered. */
			$creator_name_match_final = apply_filters( 'wpclink_match_creator_names_filter', $creator_name_match);
			
			if( $creator_name_match_final || current_user_can('editor') || current_user_can('administrator')  ){
			}else{
				
				return false;
			}
			
		}
	}
	
	
	if($linked_flag){
		// Linked Content
	}else{
		
		// Referent Creation
		if(wpclink_check_license_by_post_id($post_id) > 0){ 
			
			// CLink ID
			$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
				
			echo  '<script id="clink-registration" type="text/javascript">jQuery(document).ready(function(){ 	
					jQuery("<div class=\"cl-registry-disabled\"><strong>Referent Creation</strong> <br> <a class=\"whynotregister\" target=\"_blank\" href=\"https://docs.clink.media/wordpress-plugins/creations/referent-creation#ReferentCreation-REFERENTCREATIONISUNEDITABLEONCEITISLINKED!\">Why is it uneditable? </a></div>").insertBefore( ".misc-pub-section.curtime.misc-pub-curtime" ); });jQuery(document).ready(function(){jQuery( "<div class=\"misc-pub-section clink_id\"><span class=\"clinkid\"><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$contentID.'\" target=\"_blank\">'.wpclink_do_icon_clink_ID($contentID,true).'</a></span></div>" ).insertAfter( ".misc-pub-section.misc-pub-attachment" );});</script>';
				
				
				
				return false;
			
		}
	}
	
	// CLink ID
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	$all_versions = '';
	
		
	// Version CLink ID(s)
	if($clink_versions = get_post_meta( $post_id, 'wpclink_versions', true )){
		
		$clink_versions = array_reverse($clink_versions);
		
		foreach($clink_versions as $single_version){
			
			$archive_icon = apply_filters('wpclink_archive_version_button','',$post_id,$single_version);
			
			$all_versions .='<li><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$single_version.'\" target=\"_blank\">'.wpclink_do_icon_clink_ID($single_version,true).'</a> '.$archive_icon.'</li>';
		
		}
	
		
	}else{
		$clink_versions = array();
	}
	// SHOW REGISTER
	$content_register_restrict = get_post_meta($post_id,'wpclink_post_register_status',true);
	
	$imatag_status = '';
	
	// ISCC LABEL
	$iscc_status = get_post_meta($post_id,'wpclink_iscc_status',true);
	
	
	if($content_register_restrict == false) $content_register_restrict = 0;
	
	
	if($iscc_status == 1) $iscc_label = "Update"; else $iscc_label = "Add";
	
	
	
	if($content_register_restrict == '1') $restrict_status = 'Disabled'; else $restrict_status = 'Enabled';
	
	if($imatag_status == 1){	
		$imatag_label = "Add";
		$imatag_disable = 'disabled=\"disabled\"';
		$imatag_action = '';
		$imatag_show = 'show';
	}else{
		$imatag_label = "Add";
		$imatag_disable = '';
		$imatag_action = 'onclick=\"wpclink_generate_imatag()\"';
		$imatag_show = 'hide';
	}
	if(count($clink_versions) > 0){
		$versions_view_button = '<a class=\"cl-small show_version \"><span class=\"dashicons dashicons-plus\"></span> See more</a>';
	}else{
		$versions_view_button = '<a class=\"cl-small show_version hide\"><span class=\"dashicons dashicons-plus\"></span> See more</a>';
	}
	
	
	// Has CLink ID
	if($contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true )){
		
	$clink_id_section = '<div class=\"misc-pub-section clink_id\"><span class=\"clinkid\"><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$contentID.'\" target=\"_blank\">'.wpclink_do_icon_clink_ID($contentID,true).'</a></span></div>';
		
	$license_link_script = '';
	
	
	if($linked_flag){
		
		$post_taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true);
		
		if($post_taxonomy_permission == 'un-editable'){
			// Nothing
			$disabled_version = 'disabled=\"disabled\"';
			$version_button_class = 'disabled_version';
		}else{
			$version_button_class = 'make_version';
			$disabled_version = '';
		}
		
		$clink_version_section = '<div class=\"misc-pub-section clink_versions\"><span class=\"version_label\">Versions</span><a class=\"button cl-small '.$version_button_class.'\" '.$disabled_version.'>Add</a> '.$versions_view_button.'<input type=\"hidden\" id=\"cl_version\" name=\"cl_version\" value=\"0\" /><div class=\"all_versions media\" style=\"display:none;\">'.$all_versions.'</div> </div>';
		
		$referent_license_data = wpclink_get_license_by_linked_post_id($post_id);
		$license_id = $referent_license_data['license_id'];
		$download_id = wpclink_get_license_meta($license_id,'license_download_id',true);
		
		$license_external_link = '<a class="external-link" target="_blank" href="'.get_bloginfo("url").'?license_my_show_id='.$license_id.'&download_id='.$download_id.'" style="color:#333;"><span class="dashicons dashicons-migrate"></span></a>';
		
		$license_link_script = "jQuery(document).ready(function(){  jQuery('#wpclink_media_license > h2').append(' ".$license_external_link."'); }); ";
		
	}else{
		
		
		 if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator') ){
			 $make_version = 'make_version';
			 $disabled = '';
			 $iscc_generate = 'onclick=\"wpclink_generate_iscc()\"';
		 }else{
			 $make_version = '';
			 $disabled = 'disabled=\"disabled\"';
			 $iscc_generate = '';
		 }
		
		$clink_version_section = '<div class=\"misc-pub-section clink_versions\"><span class=\"version_label\">Versions</span><a '.$disabled.' class=\"button cl-small '.$make_version.' \">Add</a> '.$versions_view_button.'<input type=\"hidden\" id=\"cl_version\" name=\"cl_version\" value=\"0\" /><div class=\"all_versions media\" style=\"display:none;\">'.$all_versions.'</div> </div>';
		
		$license_link_script = '';
	}
		
	
			$iscc_disabled = '';
			$iscc_generate_linked = 'generate_iscc';
		
		
	$other_section = $clink_version_section.'<div class=\"misc-pub-section clink_iscc\"><span class=\"iscc_label\">ISCC</span><a class=\"button iscc-07 cl-small '.$iscc_generate_linked.' \" '.$iscc_disabled.' >'.$iscc_label.'</a><input type=\"hidden\" id=\"generate_iscc\" name=\"generate_iscc\" value=\"0\" /></div>';
		
		
		
		
		
		
	}else{
		$clink_id_section = '';
		$other_section = '';
	}
	
	 $get_apply_filename = wpclink_get_option( 'wpclink_attachment_apply_filename' );
	
	
	 $wpclink_media_buttons = apply_filters('wpclink_media_publish_box_buttons',' ',$post_id);
	
	
	 $wpclink_version_scripts = 'jQuery(".make_version").click(function(){
	 
	 jQuery(".loading-circle").hide();
	
	jQuery(".ui-dialog-buttonpane").css("visibility","visible");
	
		
	jQuery("#dialog-confirm").html("<p class=\"confirm-version\">Versions are like snapshots. Those are immutable, in other words un-editable. It is made from the current saved version of the content. It creates a new registry entry and shows the relationship with the current and previous versions.</p>");
			
	jQuery( "#dialog-confirm" ).dialog({
		  resizable: false,
		  height: "auto",
		  title: "CLink.ID Version",
		  width: 400,
		  dialogClass: "clink-dialog",
		  modal: true,
		  open: function(event) {
 			jQuery(".ui-dialog-buttonpane").find("button:contains(\"Continue\")").addClass("button-primary");
		  },
		  buttons: {
			"Continue": function() {
			jQuery("#cl_version").attr("value","1");
			
			var form = jQuery("#post");
			var url = form.attr("action");
			jQuery(".ui-dialog .ui-button").prop("disabled", true);
			
			wpclink_clink_action_btn("disable");
			
			var post_id_value = jQuery("#post_ID").val();
			
			jQuery(".confirm-version").hide();
			jQuery(".warning-message").hide();
			jQuery(".loading-circle").show();
			
			var dialogeconfirm = jQuery(this);
			dialogeconfirm.dialog( "close" );
			
			
			
			
			jQuery.ajax({
			url: ajaxurl, 
			data: {
				"action": "wpclink_update_version_ajax",
				"post_id" : post_id_value,
			},
			beforeSend: function() {
			
			jQuery("#delete-action").hide();
			jQuery("#publishing-action .loader_spinner_cover").remove();
			jQuery("#publishing-action").append("<div class=\"loader_spinner_cover\"><div class=\"loader_spinner\"></div><div class=\"cl_loader_status_mini\">Make a version...</div></div>");
			jQuery("#publishing-action #publish").prop( "disabled", true );
			metadataChanged = false;
				
			},
			success:function(data) {
			
					var error_trigger_version = false;
					
					console.log(data);
			
			
					if(typeof data === "object" && data !== null){
						error_trigger_version = wpclink_error_dialoge_box(data,"media_version");
							
					}else{
						data = jQuery.parseJSON(data);
						error_trigger_version = wpclink_error_dialoge_box(data,"media_version");
					}
					
					if(error_trigger_version == true){
						wpclink_hide_background_status();
						wpclink_clink_action_btn("enable");
							
					}else{	
					wpclink_get_versions_ajax(post_id_value);
					jQuery(".show_version").removeClass("hide");
					jQuery(".show_version").addClass("show");
					jQuery("#cl_version").attr("value","0");
					console.log(data);
					
					wpclink_clink_action_btn("enable");
					
					wpclink_hide_background_status();
					}
			
			
			
			
			
			
				
			}
		});
			
			},
			"Cancel": function() {
				jQuery("#cl_version").attr("value","0");
				jQuery( this ).dialog( "close" );
				wpclink_clink_action_btn("enable");
			}
		  }
		});
	});';
	
	
	
	$wpclink_panel_scripts = apply_filters('wpclink_clink_panel_vesion_script',$wpclink_version_scripts);
	
	$wpclink_after_media_update_action = 'wpclink_reload_media("media_updated","1");';
	
	$wpclink_after_media_update = apply_filters('wpclink_after_media_update',$wpclink_after_media_update_action,$post_id);
	
	$wpclink_after_license_update_action = apply_filters('wpclink_after_license_update_action','wpclink_reload_media("media_updated","1");',$post_id);
	
	
	if(empty(wpclink_user_has_creator_list($current_user_id)) and empty($contentID)){
		$wpclink_not_creator = '<span class=\"non_creator\">You are not the Creator. <br><a href=\"#\">Learn about Creators <span class=\"dashicons dashicons-external\"></span></a></span';
	}else{
		$wpclink_not_creator = '';
	}
	
	if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator')){
	
		if(empty(wpclink_user_has_creator_list($current_user_id)) and empty($contentID)){
			
			$registration_on = '<div class=\"misc-pub-section clink_quota_id\"><input id=\"cl_register_process\" name=\"cl_register_process\" disabled=\"disabled\" class=\"components-checkbox-control__input\" value=\"0\" type=\"checkbox\"  /><label class=\"disabled\">Allow registration</label>'.$wpclink_not_creator.'<input type=\"hidden\" id=\"exifdata\" name=\"exifdata\" value=\"0\" /></div>';
			
		}else{
			$registration_on = '<div class=\"misc-pub-section clink_quota_id\"><input id=\"cl_register_process\" name=\"cl_register_process\" class=\"components-checkbox-control__input\" value=\"0\" type=\"checkbox\" '.checked( $content_register_restrict, 0, false ).' /><label>Allow registration</label>'.$wpclink_not_creator.'<input type=\"hidden\" id=\"exifdata\" name=\"exifdata\" value=\"0\" /></div>';
		}
		
	}else{
		$registration_on = '<div class=\"misc-pub-section clink_quota_id\"><input id=\"cl_register_process\" name=\"cl_register_process\" disabled=\"disabled\" class=\"components-checkbox-control__input\" value=\"0\" type=\"checkbox\"  /><label class=\"disabled\">Allow registration</label>'.$wpclink_not_creator.'<input type=\"hidden\" id=\"exifdata\" name=\"exifdata\" value=\"0\" /></div>';
	}
	
	 $scripts= 'jQuery(document).ready(function(){
	 jQuery( "'.$clink_id_section.$registration_on.$other_section.$wpclink_media_buttons.'" ).insertAfter( ".misc-pub-section.misc-pub-attachment" );
	 
	 jQuery("#cl_register_process").change(function(){
		 if(jQuery(this).is(":checked")) {
		 	no_popup = false;
			wpclink_allow_register = 0;
		 }else{
		 	no_popup = true;
			wpclink_allow_register = 1;
		 }
	});
			 
			
	jQuery(".show_version").click(function(){
	
	var see_version_btn = jQuery(this);
	
			jQuery(".all_versions").slideToggle("fast",function() {
			
		if(jQuery(".all_versions").css("display") == "none"){
			
			see_version_btn.contents().last().replaceWith(" See more");
				see_version_btn.find(".dashicons").removeClass("dashicons-minus");
				see_version_btn.find(".dashicons").addClass("dashicons-plus");
				
		}else{
			
				see_version_btn.contents().last().replaceWith(" See less");
				see_version_btn.find(".dashicons").removeClass("dashicons-plus");
				see_version_btn.find(".dashicons").addClass("dashicons-minus");
				
			
		}
    			
  		});
		});
	
	
	
	
	function wpclink_generate_iscc(){
	
	// Post ID
	var post_id_value = jQuery("#post_ID").val();
	
					// Generate Ajax Request
					jQuery.ajax({
						url: ajaxurl, 
						data: {
							"action": "wpclink_generate_iscc_ajax",
							"post_id" : post_id_value,
							"update_iscc" : "1"
						},
					
						beforeSend: function() {
							jQuery("#delete-action").hide();
							jQuery("#publishing-action .loader_spinner_cover").remove();
							jQuery("#publishing-action").append("<div class=\"loader_spinner_cover\"><div class=\"loader_spinner\"></div><div class=\"cl_loader_status_mini\">Generating ISCC...</div></div>");
							jQuery("#publishing-action #publish").prop( "disabled", true );
							metadataChanged = false;
							jQuery(".generate_iscc").addClass("disabled");
							wpclink_clink_action_btn("disable");
						},
						success:function(data) {
							
							
							
							if(wpclink_error_dialoge_box(data,"new_popup")){
								// Error
								jQuery(".generate_iscc").prop("disabled", false);
								jQuery(".generate_iscc").text("Error");
								
								wpclink_hide_background_status();
								wpclink_clink_action_btn("enable");
							}else{
								
								jQuery(".generate_iscc").removeClass("disabled");
								
								jQuery(".generate_iscc").text("Update");
								
								wpclink_hide_background_status();
								wpclink_clink_action_btn("enable");
							}
							
						},
						complete:function(data){
							if(wpclink_error_dialoge_box(data,"new_popup")){
								// Error
								
								jQuery(".generate_iscc").text("Error");
								jQuery(".generate_iscc").removeClass("disabled");
								
								wpclink_hide_background_status();
								wpclink_clink_action_btn("enable");
							}else{
							
								jQuery(".generate_iscc").text("Update");
								
								jQuery(".generate_iscc").removeClass("disabled");
								
								wpclink_hide_background_status();
								wpclink_clink_action_btn("enable");
							}
							
					    },
						
						
						error: function(errorThrown){
							console.log(errorThrown);
						},
					}); 	
}
	
	jQuery(".generate_iscc").click(function(){
		wpclink_generate_iscc();
	});
	
	
	function wpclink_get_versions_ajax(post_id){
	jQuery.ajax({
		url: ajaxurl, 
		data: {
			"action": "wpclink_get_ajax_versions_ajax_request",
			"post_id" : post_id,
			"version" : "1"
		},
		success:function(data) {
			
			// Show version list
			jQuery(".all_versions").html(data);
			if(data.length > 0){
				jQuery(".show_version").removeClass("hide");
				jQuery(".show_version").addClass("show");
			}
		},
		error: function(errorThrown){
			console.log(errorThrown);
		},
	});  
}
	
	'.$wpclink_panel_scripts.'
	
	
			
});
'.$license_link_script.'
var metadataChanged = false;
var submitdata = "";
var no_popup = false;
var now_status = "";
var stop_popup_error = false;
jQuery(document).ready(function(){
	jQuery("#post").on("keyup change paste", "input, select, textarea", function(){
		metadataChanged = true;
		
		
	});
});
function loader_status_label(status){
	switch(status) {
	
	  case "updatingdatabase":
		 return "Updating database..."
		break;
	  case "exifwrites":
		 return "Wrting to Image..."
		break;
	   case "registrywrites":
		 return "Writing to registry..."
		break;
	   case "exifremove":
		 return "Filtering EXIF metadata..."
	   break;
	   case "updatingembeded":
		 return "Updating image..."
		break;
	   case "sync_cloudinary":
		 return "Syncing to Cloudinary..."
		break;
		case "reloading":
		 return "Reloading..."
		break;
		case "error":
		 return "Error"
		break;
	  default:
	   return ""
	}
}
function wpclink_after_license_assign_action(){
'.$wpclink_after_license_update_action.' 
wpclink_smart_loader("license","Applying license...");
}
function check_loader_status(now_status) {
	
	var post_id_value = jQuery("#post_ID").val();
	var cl_stop_loader = false;
	var error_trigger = false;
	
	 jQuery.ajax({
			url: ajaxurl, 
			data: {
				"action": "wpclink_get_loader_status_request",
				"post_id" : post_id_value,
				"status" : "get"
			},
			success:function(data) {
				
				if(data != 0){
				
					if(typeof data === "object" && data !== null){
						error_trigger = wpclink_error_dialoge_box(data,"media_popup");
							
					}else{
						data = jQuery.parseJSON(data);
						error_trigger = wpclink_error_dialoge_box(data,"media_popup");
					}
					
					if(error_trigger == true){
						cl_stop_loader = true;
						stop_popup_error = true;		
					}
					if(now_status != data.status){
						if(data.status != "0"){
							if(data.status == "reloading" && metadataChanged == true){
								// Metadata is change not need to show reload status
							}else{
							
								jQuery(".cl_loader_status_mini").text(loader_status_label(data.status));
							}
						}
					}
					
					if(cl_stop_loader == false){
					
							check_loader_status(data.status);
						
						
					}
					
				}
				
				
				
			}
		});
	}
function wpclink_hide_background_status(){
		jQuery("#delete-action").show();
		jQuery("#publishing-action .loader_spinner_cover").remove();
		jQuery("#publishing-action #publish").prop( "disabled", false );
		jQuery(".spinner").hide();
		metadataChanged = false;
}
	
function cl_metadata_warning(){
jQuery( "#dialog-metadata-warning" ).dialog({
		  resizable: false,
		  height: "auto",
		  title: "Prerequisites",
		  width: 400,
		  modal: true,
		  open: function(event) {
				 jQuery(".ui-dialog-buttonpane").find("button:contains(\"Continue\")").addClass("button-primary");
				 jQuery(".ui-dialog-titlebar").show();
				 jQuery(".ui-dialog-titlebar-close").show();
				 
				 ';
	if($media_creator_warning = get_user_meta($current_user_id,'wpclink_media_creator_warning',true)){
		 $scripts.=' jQuery(".ui-dialog-buttonpane").find("button:contains(\"Continue\")").trigger( "click" );';
	}
	
		 $scripts.='
			 },
			 
		  buttons: {
			"Continue": function() {
			
				var form = jQuery("#post");
				var url = form.attr("action");
				
				// Default
				event.preventDefault();
				
				
			jQuery("#delete-action").hide();
			
			jQuery("#publishing-action .loader_spinner_cover").remove();
			
			jQuery("#publishing-action").append("<div class=\"loader_spinner_cover\"><div class=\"loader_spinner\"></div><div class=\"cl_loader_status_mini\">Updating...</div></div>");
			
			jQuery("#publishing-action #publish").prop( "disabled", true );
			
			
			metadataChanged = false;
			
			
				
	';
$options = wpclink_get_option( 'preferences_general' ); 
$exif_metdata_option = $options['embeded_metadata'];
	
$exif_data = get_post_meta( $post_id, 'wpclink_check_exif_data', true );
	
if($exif_data == 1 and $exif_metdata_option == 'ask'){
$scripts.=' 
// Background Process
jQuery( this ).dialog( "close" );
jQuery( "#dialog-metadata-exifdata" ).dialog({
			  resizable: false,
			  height: "auto",
			  title: "EXIF Metadata",
			  width: 400,
			  modal: true,
			  open: function(event) {
					 jQuery(".ui-dialog-buttonpane").find("button:contains(\"Dates\")").addClass("button-primary");
					 jQuery(".ui-dialog-titlebar").show();
					 jQuery(".ui-dialog-titlebar-close").show();	
				 },
			  buttons: {
				"Dates": function() {
				
				jQuery("#exifdata").attr("value","1");
	
				check_loader_status();
				
				jQuery(".warning-message").hide();
				jQuery(".loading-circle").show();
				jQuery(".ui-dialog-buttonpane button").prop( "disabled", true );
				
				jQuery(".ui-dialog-titlebar").hide();
				jQuery(".ui-dialog-titlebar-close").hide();				
				
				
				jQuery.ajax({
				   type: "POST",
				   url: url,
				   dataType: "html",
				   cache: false,
				   crossDomain: true,
				   data: form.serialize(),
				   beforeSend: function() {
					jQuery(".cl_loader_status_mini").text("Preparing...");
					},
				   success: function(data){
				    
				   },
				   error: function(xhr, textStatus, errorThrown) {
				   if(stop_popup_error == false){
				   
				   		if(metadataChanged == false){
                	'.$wpclink_after_media_update.'
						}else{
						
						jQuery("#publish").prop("disabled",false);
						jQuery("#publish").val("Save & Reload");
						
						wpclink_hide_background_status();
						
						}
					
					}
             	  }
				 }).done(function( data ) {
				 	if(stop_popup_error == false){
					
						if(metadataChanged == false){
						'.$wpclink_after_media_update.'
						
						}else{
						
						jQuery("#publish").prop("disabled",false);
						jQuery("#publish").val("Save & Reload");
						
						wpclink_hide_background_status();
						
						}
					}
				  });
				
				// Background Process
				jQuery( this ).dialog( "close" );
				},
				"All": function() {
				
								
				check_loader_status();
				
				jQuery(".warning-message").hide();
				jQuery(".loading-circle").show();
				jQuery(".ui-dialog-buttonpane button").prop( "disabled", true );
				
				jQuery(".ui-dialog-titlebar").hide();
				jQuery(".ui-dialog-titlebar-close").hide();				
				
				
				jQuery("#exifdata").attr("value","0");
				jQuery.ajax({
				   type: "POST",
				   url: url,
				   dataType: "html",
				   cache: false,
				   crossDomain: true,
				   data: form.serialize(),
				   beforeSend: function() {
					jQuery(".cl_loader_status_mini").text("Preparing...");
					},
				   success: function(data){
				    
				   },
				   error: function(xhr, textStatus, errorThrown) {
				   if(stop_popup_error == false){
				   
				   if(metadataChanged == false){
                	'.$wpclink_after_media_update.'
					
					
					}else{
						
						jQuery("#publish").prop("disabled",false);
						jQuery("#publish").val("Save & Reload");
						
						wpclink_hide_background_status();
						
						}
					}
             	  }
				 }).done(function( data ) {
				 	if(stop_popup_error == false){
					
					if(metadataChanged == false){
						'.$wpclink_after_media_update.'
						
					}else{
						
						jQuery("#publish").prop("disabled",false);
						jQuery("#publish").val("Save & Reload");
						
						wpclink_hide_background_status();
						
						}
					}
				  });
				// Background Process
				jQuery( this ).dialog( "close" );
				}
			  }
			});';
	
	}else{
	
	$scripts.='
	
	 
	
	
	check_loader_status();
	jQuery(".warning-message").hide();
	jQuery(".loading-circle").show();
	jQuery(".ui-dialog-buttonpane button").prop( "disabled", true );
	jQuery(".ui-dialog-titlebar").hide();
	jQuery(".ui-dialog-titlebar-close").hide();				
				
	
	jQuery.ajax({
				   type: "POST",
				   url: url,
				   dataType: "html",
				   cache: false,
				   crossDomain: true,
				   data: form.serialize(),
				   beforeSend: function() {
					jQuery(".cl_loader_status_mini").text("Preparing...");
					},
				   success: function(data){
				    
				   },
				   error: function(xhr, textStatus, errorThrown) {
				   if(stop_popup_error == false){
				   
				   		if(metadataChanged == false){
                	'.$wpclink_after_media_update.'
					
						}else{
						
						jQuery("#publish").prop("disabled",false);
						jQuery("#publish").val("Save & Reload");
						
						wpclink_hide_background_status();
						
						}
					}
             	  }
				 }).done(function( data ) {
				 	if(stop_popup_error == false){
					
						if(metadataChanged == false){
						'.$wpclink_after_media_update.'
						
						}else{
						
						jQuery("#publish").prop("disabled",false);
						jQuery("#publish").val("Save & Reload");
						
						wpclink_hide_background_status();
						
						}
					}
				  }); 
				  
				  
				// Background Process
				jQuery( this ).dialog( "close" );
				  ';
}
$scripts.=' return false;
			},
			"Cancel": function() {
				jQuery("#exifdata").attr("value","0");
				jQuery( this ).dialog( "close" );
				return false;
			}
		  }
		});
}
function submit_by_ajax_call(){
jQuery(".loading-circle").hide();
jQuery(".warning-message").show();
cl_metadata_warning();
';
	
$scripts.='
}
jQuery(document).ready(function(){
	var apply_filename_check = "'.$get_apply_filename.'";
	if(apply_filename_check == "yes"){
		jQuery("#apply_filename").prop( "checked", true );
	}else{
		jQuery("#apply_filename").prop( "checked", false );
	}
});
';
// No Popup
if($content_register_restrict == '1'){
	$scripts.='no_popup = true;';
}
if($linked_flag == false){
	$scripts.=' jQuery(document).ready(function(){
		jQuery( "#publish" ).on("click", function(event){
		if(no_popup == true){
		}else{
			event.preventDefault();
			submit_by_ajax_call(event);
			return false;	
		}
		});	
	});';
}
	
 	// Print Script only Attachments
	echo  '<script id="mef" type="text/javascript">'.$scripts.'</script>';
}
// Register clink id on publish widget of post function
add_action( 'admin_head', 'wpclink_media_publish_box' );
/**
 * CLink Content ID Table Header on WordPress Post List
 * 
 * @param string $defaults default column
 * 
 */
function wpclink_register_column_clink_ID_post_page($defaults) {
	
	
    $defaults['clink_id'] = '<span class="clinkico-24-text"></span>';
    return $defaults;
}
// Register content id table header on wordpress post column
add_filter('manage_posts_columns', 'wpclink_register_column_clink_ID_post_page');
// Register content id table header on wordpress page column
add_filter('manage_pages_columns', 'wpclink_register_column_clink_ID_post_page');
// Register content id table header on wordpress page column
add_filter('manage_media_columns', 'wpclink_register_column_clink_ID_post_page');
 /**
  * CLink Content ID Column Value on WordPress Post List
  * 
  * @param string $column_name default column
  * @param integer $post_ID post id
  * 
  */
function wpclink_print_clink_ID_posts_pages($column_name, $post_ID) {
	if($column_name == 'clink_id'){
	$contentID = get_post_meta( $post_ID, 'wpclink_creation_ID', true );
	if ( !empty( $contentID ) ) {
		echo '<a href="'.WPCLINK_ID_URL.'/#objects/'.$contentID.'" target="_blank">'.wpclink_strip_prefix_clink_ID($contentID).'</a>';
	}else{
		echo 'N/A';	
	}
	}
}
// Register content id column value function
add_action('manage_posts_custom_column', 'wpclink_print_clink_ID_posts_pages', 10, 2);
add_action('manage_pages_custom_column', 'wpclink_print_clink_ID_posts_pages', 10, 2);
add_action('manage_media_custom_column', 'wpclink_print_clink_ID_posts_pages', 10, 2);
/**
 * 
 * CLink Re-order Column
 *
 * Re-order Column CLink Referent Creator Column Should be Before Author Column
 *
 * @param string $columns default column
 * 
 * @return string
 */
function wpclink_reorder_columns($columns) {
  $cl_columns = array();
  $ref_creator = 'cl_ref_creator'; 
  $author = 'author'; 
  foreach($columns as $key => $value) {
    if ($key==$author){
      $cl_columns[$ref_creator] = $ref_creator;
    }
      $cl_columns[$key] = $value;
  }
  return $cl_columns;
}
// Register re-order oolumn function
add_filter('manage_posts_columns', 'wpclink_reorder_columns');
add_filter('manage_pages_columns', 'wpclink_reorder_columns');
/**
 * CLink Display CLink ID on the Publish Widget of Post
 * 
 */
function wpclink_display_versions_creation() {
	
	 $current_screen = get_current_screen();
	 if($current_screen->id == 'post' || $current_screen->id == 'page'){
	 }else{
		 return false;
	 }
	
	global $post;
    $post_id= $post->ID;
	
	$current_user_id = get_current_user_id();
	$party_user_id = wpclink_get_option('authorized_contact');
	
	// CREATOR
	$creator_array = wpclink_get_option('authorized_creators');
	
	
	$post_author_id = get_post_field( 'post_author', $post_id );
	
	$right_holder = wpclink_get_option('rights_holder');
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
	
	// ONLY FOR CREATOR
	if((wpclink_user_has_creator_list($current_user_id) and $current_user_id == $post_author_id) || ($current_user_id == $right_holder_id and $current_user_id == $post_author_id) ){
		
	
	
	
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	
	if($clink_versions = get_post_meta( $post_id, 'wpclink_versions', true )){
		
		$show_version_button = 'show';
		
		if(count($clink_versions) == 1){
			$version_count = 'Version: <strong>'.count($clink_versions).'</strong>';
		}else{
			$version_count = 'Versions: <strong>'.count($clink_versions).'</strong>';
		}
	}else{
		
		$show_version_button = 'hide';
		
		$version_count = 'Version: <strong>N/A</strong>';
	}
		
	$all_versions = '';
	
	if($clink_versions = get_post_meta( $post_id, 'wpclink_versions', true )){
	
		$all_versions = '';
		
		foreach($clink_versions as $single_version){
			
			$archive_icon = apply_filters('wpclink_archive_version_button','',$post_id,$single_version);
			
			$all_versions .='<a href=\"'.WPCLINK_ID_URL.'/#objects/'.$single_version.'\" target=\"_blank\">'.$single_version.'</a> '.$archive_icon.'<br/>';
		
		}
	
	}
	
	
	if ( !empty( $contentID ) ) {
	}else{
		return false;
	}
	
	 $current_screen = get_current_screen();
	 if($current_screen->id == 'post' || $current_screen->id == 'page'){
		 
		  if($iscc_status = get_post_meta($post_id,'wpclink_iscc_status',true)){
			 $iscc_label = 'Update';
		 }else{
			 $iscc_label = 'Add'; 
		 }
		 
		 
		 $archive_section = apply_filters('wpclink_clink_panel_button_section','',$post_id);
		 
		 $current_user_id = get_current_user_id();
		 
		  if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator')){
				 $make_version = 'make_version';
				 $disabled = '';
				 $iscc_generate = 'onclick=\"wpclink_generate_iscc()\"';
			 }else{
				 $make_version = '';
				 $disabled = 'disabled=\"disabled\"';
				 $iscc_generate = '';
			 }
		 
		 
		 echo '<script type="text/javascript">
		 jQuery(document).ready(function(){
			 jQuery( "<div class=\"misc-pub-section clink_post_id\"><span class=\"clinkico\"></span>'.$version_count.' <a  '.$disabled.' class=\"button cl-small '.$make_version.' \">Add</a> <a class=\"cl-small show_version '.$show_version_button.' \"><span class=\"dashicons dashicons-plus\"></span> See more</a><div class=\"all_versions\" style=\"display:none;\">'.$all_versions.'</div> <span class=\"clinkid\"><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$contentID.'\" target=\"_blank\">'.$contentID.'</a></span></div><div class=\"small-label category iscc_wrap\"><span class=\"iscc_label\">ISCC</span><a '.$disabled.' class=\"button cl-small generate_iscc  iscc-05 \" '.$iscc_generate.'>'.$iscc_label.'</a></div>'.$archive_section.'<input type=\"hidden\" id=\"cl_version\" name=\"cl_version\" value=\"0\" /> " ).insertAfter( ".misc-pub-section.curtime.misc-pub-curtime" );
	
		jQuery(".show_version").click(function(){ 
		var see_version_btn = jQuery(this);
			jQuery(".all_versions").slideToggle("fast",function() {
			
		if(jQuery(".all_versions").css("display") == "none"){
			
			
			see_version_btn.contents().last().replaceWith(" See more");
				see_version_btn.find(".dashicons").removeClass("dashicons-minus");
				see_version_btn.find(".dashicons").addClass("dashicons-plus");
				
				
		}else{
			
		
				see_version_btn.contents().last().replaceWith(" See less");
				see_version_btn.find(".dashicons").removeClass("dashicons-plus");
				see_version_btn.find(".dashicons").addClass("dashicons-minus");
				
			
			
		}
    			
  		});
		});
			 
jQuery(".make_version").click(function(){
			
jQuery( "#dialog-confirm" ).dialog({
      resizable: false,
      height: "auto",
	  dialogClass: "clink-dialog",
      width: 400,
      modal: true,
	   open: function(event) {
				 jQuery(".ui-dialog-buttonpane").find("button:contains(\"Continue\")").addClass("button-primary");
			 },
      buttons: {
        "Continue": function() {
			jQuery("#cl_version").attr("value","1");
			jQuery("#publish").trigger("click");
			jQuery( this ).dialog( "close" );
			
			jQuery("#delete-action").hide();
			jQuery("#publishing-action .loader_spinner_cover").remove();
			jQuery("#publishing-action").append("<div class=\"loader_spinner_cover\"><div class=\"loader_spinner\"></div><div class=\"cl_loader_status_mini\">Updating...</div></div>");
			jQuery("#publishing-action #publish").prop( "disabled", true );
			
			
			metadataChanged = false;
			
        },
        "Cancel": function() {
			jQuery("#cl_version").attr("value","0");
          	jQuery( this ).dialog( "close" );
        }
      }
    });
	
});
		 });</script>';
	 }
	 
	 }else{
		
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	
	if($clink_versions = get_post_meta( $post_id, 'clink_versions', true )){
		
		if(count($clink_versions) == 1){
			$version_count = 'Version: <strong>'.count($clink_versions).'</strong>';
		}else{
			$version_count = 'Versions: <strong>'.count($clink_versions).'</strong>';
		}
	}else{
		$version_count = 'Version: <strong>N/A</strong>';
	}
	
	if($clink_versions = get_post_meta( $post_id, 'wpclink_versions', true )){
	
		$all_versions = '';
		
		foreach($clink_versions as $single_version){
			$all_versions .='<a href=\"'.WPCLINK_ID_URL.'/#objects/'.$single_version.'\" target=\"_blank\">'.$single_version.'</a><br/>';
		
		}
	
	}
	
	
	if ( !empty( $contentID ) ) {
	}else{
		return false;
	}
	
	 $current_screen = get_current_screen();
	 if($current_screen->id == 'post' || $current_screen->id == 'page'){
		 
		 
		 echo '<script type="text/javascript">
		 jQuery(document).ready(function(){
			 jQuery( "<div class=\"misc-pub-section clink_post_id\"><span class=\"clinkico\"></span>'.$version_count;
			 if(!empty($clink_versions) and $clink_versions >= 1){
				 echo ' <a class=\"cl-small show_version \"><span class=\"dashicons dashicons-plus\"></span> See more</a>';
			 }
			 echo '<div class=\"all_versions\" style=\"display:none;\">'.$all_versions.'</div> <span class=\"clinkid\"><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$contentID.'\" target=\"_blank\">'.$contentID.'</a></span></div>" ).insertAfter( ".misc-pub-section.curtime.misc-pub-curtime" );
			 jQuery(".show_version").click(function(){ jQuery(".all_versions").slideToggle("fast"); });
		 });</script>';
	 }
	}
	
}
// CLink display clink id register function
//add_action( 'admin_head', 'wpclink_display_versions_creation' );
/**
 * CLink Create Version of Post Confirmation Popup
 * 
 */
function wpclink_media_metadata_copyright_warning() {
	 $current_screen = get_current_screen();
	 if( $current_screen->id == 'attachment'){
		 
	echo '<div id="dialog-metadata-warning" title="Prerequisites" style="display:none;"><div class="warning-message"><p><u>You must be the creator and must have exclusive rights to the image.</u></p><p>You must not register an image that been released under Creative Commons or other free licenses.</p></div><div class="loading-circle" style="display:none;"><div class="loading-wrapper"><span class="cl_loader"></span><span class="cl_loader_status">Updating...</span></div></div></div>
	<div id="dialog-metadata-exifdata" title="Prerequisites" style="display:none;"><div class="warning-message">What do you want to keep?</div><div class="loading-circle" style="display:none;"><div class="loading-wrapper"><span class="cl_loader"></span><span class="cl_loader_status">Updating...</span></div></div></div></div>';
	 
	 }
}
// Register clink version confirmation popup on admin footer
add_action('admin_footer', 'wpclink_media_metadata_copyright_warning');
/**
 * CLink Media Creator Warning
 * 
 */
function wpclink_media_creator_warning() {
	
	
	
	 $current_screen = get_current_screen();
	 if( $current_screen->id == 'attachment'){
		 
		 global $post;
    	$post_id= $post->ID;
		 
		 $current_user_id = get_current_user_id();
		 $current_user_info = get_userdata($current_user_id);
		 $full_name = $current_user_info->first_name. ' '.$current_user_info->last_name;
		 
	echo '<div id="dialog-creator-warning" title="Why not register" style="display:none;"><p>The embedded meta-data of the image shows that the Creator is ['.$full_name.']. It does not match your name in the WordPress user profile. </p></div>';
		 
	$registration_disallow = get_post_meta($post_id,'wpclink_registration_disallow',true);
		 
	if($registration_disallow == 1){
		 
		echo '<div id="dialog-rights-warning" title="Why not register" style="display:none;"><p>The image has the Licensor URL and/or the Web Statement of Rights embedded in its meta-data.</p></div>';
		
	}
	 
	 }
}
// Register clink version confirmation popup on admin footer
add_action('admin_footer', 'wpclink_media_creator_warning');
/**
 * CLink Create Version of Post Confirmation Popup
 * 
 */
function wpclink_confirm_popup() {
	 $current_screen = get_current_screen();
	 if($current_screen->id == 'post' || $current_screen->id == 'page' ||  $current_screen->id == 'attachment'){
		 
	echo '<div id="dialog-confirm" title="CLink.ID Version" style="display:none;"><p>Versions are like snapshots.  Those are immutable, in other words un-editable. It is made from the current saved version of the content. It creates a new registry entry and shows the relationship with the current and previous versions. </p></div>';
		 
	 
	 }
}
// Register clink version confirmation popup on admin footer
add_action('admin_footer', 'wpclink_confirm_popup');
/**
 * CLink Register Content CLink.ID From on Publish Widget of Post
 * 
 */
function wpclink_clinkid_submit_button(){
	
	 $current_screen = get_current_screen();
	 if($current_screen->id == 'post' || $current_screen->id == 'page'){
	 }else{
		return false; 
	 }
	
	global $post;
	global $wpCLink_domain_quota;
    $post_id= $post->ID;
	
	
	$current_user_id = get_current_user_id();
	$party_user_id = wpclink_get_option('authorized_contact');
	
	// CREATOR
	$creator_array = wpclink_get_option('authorized_creators');
	$right_holder = wpclink_get_option('rights_holder');
	
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
	
	$post_author_id = get_post_field( 'post_author', $post_id );
	
	// SHOW ONLY COPY+CREATOR
	if((wpclink_user_has_creator_list($current_user_id) and $current_user_id == $post_author_id) || ($current_user_id == $right_holder_id and $current_user_id == $post_author_id)){
		
	}else{
		return false;
	}
	
	// if our current user can't edit this post, bail
    //if( !current_user_can( 'edit_post' ) ) return;
	
	$scripts = '';
	
	$linked = get_post_meta($post_id,'wpclink_referent_post_uri',true);
	
	
	if($linked){
		// Linked Content
		
		
		
		echo  '<script id="clink-registration" type="text/javascript">jQuery(document).ready(function(){ 	
					jQuery("<div class=\"cl-registry-disabled\"><strong>Linked Creation</strong> <br> <a class=\"whynotregister\" target=\"_blank\" href=\"https://docs.clink.media/wordpress-plugins/creations/linked-creation#LinkedCreation-LINKEDCREATIONCANBEEDITEDONLYACCORDINGTOITSLICENSE\">Why is it uneditable? </a></div>").insertBefore( "#minor-publishing-actions" ); });</script>';
		
		
	}else{
		
		// Referent Creation
		if(wpclink_check_license_by_post_id($post_id) > 0){ 
			
			// CLink ID
			$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
				
			echo  '<script id="clink-registration" type="text/javascript">jQuery(document).ready(function(){ 	
					jQuery("<div class=\"cl-registry-disabled\"><strong>Referent Creation</strong> <br> <a class=\"whynotregister\" target=\"_blank\" href=\"https://docs.clink.media/wordpress-plugins/creations/referent-creation#ReferentCreation-REFERENTCREATIONISUNEDITABLEONCEITISLINKED!\">Why is it uneditable? </a></div>").insertBefore( "#minor-publishing-actions" ); });</script>';
				
			
		}
	}
	
	
	 
	$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	
	 
	 // SHOW REGISTER
	 $content_register_restrict = get_post_meta($post_id,'wpclink_post_register_status',true);
		 
	if($content_register_restrict == false) $content_register_restrict = 0;
		 
	if($contentID == false){
	
	$clink_id_section = '';
	
	}else{
		
	// CLink ID
	$clink_id_section = '<div class=\"misc-pub-section clink_id\"><span class=\"clinkid\"><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$contentID.'\" target=\"_blank\">'.wpclink_do_icon_clink_ID($contentID,true).'</a></span></div>';
		
	}
		 
	$all_versions = '';
	// CLink Checkbox
	if($content_register_restrict == '1') $restrict_status = 'Disabled'; else $restrict_status = 'Enabled';
	// Version CLink ID(s)
	if($clink_versions = get_post_meta( $post_id, 'wpclink_versions', true )){
		$clink_versions = array_reverse($clink_versions);
		foreach($clink_versions as $single_version){
			$archive_icon = apply_filters('wpclink_archive_version_button','',$post_id,$single_version);
			$all_versions .='<li><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$single_version.'\" target=\"_blank\">'.wpclink_do_icon_clink_ID($single_version,true).'</a> '.$archive_icon.'</li>';
		}
	}else{
		$clink_versions = array();
	}
	if(count($clink_versions) > 0){
			$versions_view_button = '<a class=\"cl-small show_version \"><span class=\"dashicons dashicons-plus\"></span> See more</a>';
	}else{
			$versions_view_button = 'N/A';
	}
		 
	// CLink Version
	$clink_version_section = '<div class=\"misc-pub-section clink_versions\"><span class=\"version_label\">Versions</span>'.$versions_view_button.'<input type=\"hidden\" id=\"cl_version\" name=\"cl_version\" value=\"0\" /><div class=\"all_versions media\" style=\"display:none;\">'.$all_versions.'</div> </div>';	 
		 
	
		 
		 
	 $scripts.= ' jQuery(document).ready(function(){
	
	// Print
	jQuery( "'.$clink_id_section.$clink_version_section.'" ).insertAfter( ".misc-pub-section.misc-pub-curtime" );
			 
			
		jQuery("#cl_register_process").change(function(){
		 if(jQuery(this).is(":checked")) {
			wpclink_allow_register = 0;
			
		 }else{
			wpclink_allow_register = 1;
			
		 }
		});
		
			jQuery(".show_version").click(function(){ 
		var see_version_btn = jQuery(this);
			jQuery(".all_versions").slideToggle("fast",function() {
			
		if(jQuery(".all_versions").css("display") == "none"){
			
			
			see_version_btn.contents().last().replaceWith(" See more");
				see_version_btn.find(".dashicons").removeClass("dashicons-minus");
				see_version_btn.find(".dashicons").addClass("dashicons-plus");
				
				
		}else{
			
		
				see_version_btn.contents().last().replaceWith(" See less");
				see_version_btn.find(".dashicons").removeClass("dashicons-plus");
				see_version_btn.find(".dashicons").addClass("dashicons-minus");
				
				
			
		}
    			
  		});
		});
			});';
			
	 
	 
	 echo  '<script type="text/javascript">'.$scripts.'</script>';
}
// Register clink id on publish widget of post function
add_action( 'admin_head', 'wpclink_clinkid_submit_button' );
 /**
  * CLink User Profile Form Fields
  * 
  * @param object $user  
  * 
  */ 
function wpclink_custom_user_field( $user )
{
	
	
	$clink_social_display = get_user_meta($user->ID,'wpclink_social_display',true);
	$get_clink_display_email = get_user_meta($user->ID,'wpclink_email_display_status',true);
	$creator_identifier = get_user_meta($user->ID,'wpclink_party_ID',true);
	
	
	// ORCID
	if($wpclink_orcid = get_user_meta($user->ID,'wpclink_orcid',true)){
	}else{
		$wpclink_orcid ='';
	}
    
	// ISNI
	if($wpclink_isni = get_user_meta($user->ID,'wpclink_isni',true)){
	}else{
		$wpclink_isni ='';
	}
    
	// PLUS ID
	if($wpclink_plusid = get_user_meta($user->ID,'wpclink_plusid',true)){
	}else{
		$wpclink_plusid ='';
	}
    
    // DID
	if($wpclink_did = get_user_meta($user->ID,'wpclink_did',true)){
	}else{
		$wpclink_did ='';
	}
    
	
	$party_id = wpclink_get_option('authorized_contact');
	
	$creator_array = wpclink_get_option('authorized_creators');
	
	
	// NO PARTY AND CREATOR
	if($party_id == $user->ID || wpclink_user_has_creator_list($user->ID) || !empty($creator_identifier)){
		
	}else{
		return false;	
	}
	
	$submit_to_clinkid = get_user_meta($user->ID,'submit_to_clinkid',true);
	$make_version = get_user_meta($user->ID,'make_version',true);
	// Submit to CLinkID
	if($submit_to_clinkid == 1){
		
		if($make_version == 1){
			$make_version = true;
		}
		
		
		
		// Party and Creator
		if($user->ID == $party_id and wpclink_user_has_creator_list($user->ID)){
			
			//var_dump($user->ID);
			
			/* wpclink_creator_user_update($user->ID,$make_version); */
			wpclink_party_user_update($user->ID,$make_version);
			delete_user_meta($user->ID,'submit_to_clinkid');
			delete_user_meta($user->ID,'make_version');
			
		// Party
		}elseif($user->ID == $party_id){
			
			wpclink_party_user_update($user->ID,$make_version);
			delete_user_meta($user->ID,'submit_to_clinkid');
			delete_user_meta($user->ID,'make_version');
		
		// Creator
		}elseif(wpclink_user_has_creator_list($user->ID)){
			wpclink_creator_user_update($user->ID,$make_version);
			delete_user_meta($user->ID,'submit_to_clinkid');
			delete_user_meta($user->ID,'make_version');
			
		}
		
		
	}
	
	
	echo '<h3 class="heading">CLink.ID</h3>';
    ?>
    <table class="form-table clink_user_fields">
   
    <?php 
	$disabled = '';
	
    if($has_identifier_creator = get_user_meta($user->ID,'wpclink_party_ID',true) and $user->ID != $party_id){
		if(wpclink_user_has_creator_list($user->ID)){
			$my_user_status = '<span class="clid-active">&#9679; Authorized</span>';	
		}else{
			$disabled = 'disabled="disabled"';
			$my_user_status = '<span class="clid-inactive">&#9679; Inactive</span>';
			echo '<tr><th>CLink Creator Identifier </th><td><a href="'.WPCLINK_ID_URL.'/#objects/'.$has_identifier_creator.'" target="_blank">'.$has_identifier_creator.'</a> <span class="clid-active">'.$my_user_status.'</span></td></tr>';	
		}
	}else if($has_identifier_party = get_user_meta($party_id,'wpclink_party_ID',true)){
		if($user->ID == $party_id){
			$my_user_status = '<span class="clid-active">&#9679; Authorized</span>';	
		}
	}
	
	
	if($user->ID == $party_id || wpclink_user_has_creator_list($user->ID)){
	}else{
		$disabled = 'disabled="disabled"';
	}
	
	 ?>
    <?php if($user->ID == $party_id){ ?>
	<?php if($party_identifier = get_user_meta($user->ID,'wpclink_party_ID',true)){ ?>
	<tr>
    <th>CLink Party ID </th> 
    <td><a class="cl_primary_id" target="_blank" href="<?php echo WPCLINK_ID_URL; ?>/#objects/<?php echo $party_identifier;  ?>" target="_blank"><?php echo $party_identifier; ?></a> <?php echo $my_user_status; ?> <input class="button version_toggle creator" type="button" value="Versions" /></td>
    </tr>
	<?php } ?>
	<?php }elseif(wpclink_user_has_creator_list($user->ID)){ ?>
    <?php if($creator_identifier = get_user_meta($user->ID,'wpclink_party_ID',true)){ ?>
    <tr>
	<th>CLink Party ID  </th> 
    <td><a  class="cl_primary_id" target="_blank" href="<?php echo WPCLINK_ID_URL; ?>/#objects/<?php echo $creator_identifier; ?>" target="_blank"><?php echo $creator_identifier; ?></a> <input class="button version_toggle creator" type="button" value="Versions" /></td>
    </tr>
		
    <?php } ?>
	<?php }	?>	
  </table> 
<table class="form-table clink_user_fields">  
 <tr>
<?php
	if ($party_versions = array_reverse(get_user_meta($user->ID,'wpclink_versions',true))){ ?>
<ul class="versions_users party partyidtoggle">
		<p>
			<?php
foreach ($party_versions as $single_party_version){
		if(!empty($single_party_version['id'])){ ?>
		<li><a class="version_identifier" target="_blank" href="<?php echo WPCLINK_ID_URL; ?>/#objects/<?php echo $single_party_version['id']; ?>"><?php echo $single_party_version['id']; ?></a> <span class="version_time"><span class="dashicons dashicons-clock"></span> <?php echo date( 'Y-m-d H:i:s',$single_party_version['time']); ?></span></li></p>
			<?php  }
	 	} ?>
</ul>			
	<?php } ?>
	</tr>
</table>
<table class="form-table clink_user_fields">
    <tr style="display:none;">
    <th>Display Social Accounts in CLink.ID</th> 
    <td><label for="clink_field_1"><input type="checkbox" id="clink_social_display" name="clink_social_display" value="1" <?php checked( $clink_social_display, 1 ); ?> <?php echo $disabled; ?>> Enable</label></td>
    </tr>
	<th>Display Email in CLink.ID </th> 
    <td><label for="user_display_email"><input type="checkbox" id="user_display_email" name="user_display_email" value="1" <?php checked( $get_clink_display_email, 'yes' ); ?> <?php echo $disabled; ?>> Enable</label></td>
    </tr>
   </table>
<table class="form-table isni-orcid">
	<tr>
		<th><label for="wpclink_isni">ISNI</label></th>
		<td><input type="text" class="regular-text ltr" name="wpclink_isni" id="isni" value="<?php echo $wpclink_isni; ?>" /></td> 
	</tr>
	<tr>
        <th><label for="wpclink_orcid">ORCID</label></th> 
	    <td><input type="text" class="regular-text ltr" name="wpclink_orcid" id="orcid" value="<?php echo $wpclink_orcid; ?>" /></td>
	</tr>
	<tr>
        <th><label for="wpclink_plusid">PLUS-ID</label></th> 
	    <td><input type="text" class="regular-text ltr" name="wpclink_plusid" id="plusid" value="<?php echo $wpclink_plusid; ?>" /></td>
	</tr>
    <tr>
        <th><label for="wpclink_did">DID</label></th> 
	    <td><input type="text" class="regular-text ltr" name="wpclink_did" id="did" value="<?php echo $wpclink_did; ?>" /></td>
	</tr>
    </table>
	<input type="hidden" id="make_version" name="make_version" value="1" />
    <input type="hidden" id="submit_to_clinkid" name="submit_to_clinkid" value="0" /><br />
<script>
jQuery(document).ready(function(e) {
    
  jQuery('#submit').click(function(event){
	  jQuery('.cl_user_popup').show();
	  event.preventDefault();
  });
  
  jQuery('#submit2').click(function(event){
	  jQuery('#submit_to_clinkid').attr('value','1');
  });
	
	
jQuery('#make_version_checkbox').change(function() {
  if (jQuery(this).is(':checked')) {
     jQuery('#make_version').attr('value','1');
  } else {
   	 jQuery('#make_version').attr('value','0');
  }
});
	
	jQuery(".version_toggle.creator").click(function(){ jQuery(".versions_users.creator").slideToggle("fast"); });
	jQuery(".version_toggle.party").click(function(){ jQuery(".versions_users.party").slideToggle("fast"); });
        
  jQuery(document).tooltip();
});
	
	jQuery(".version_toggle.creator").click(function(){ jQuery(".partyidtoggle").slideToggle("fast"); });
	jQuery(".version_toggle.party").click(function(){ jQuery(".partyidtoggle").slideToggle("fast"); });
	
 
  </script>
   
   <div class="cl_user_popup">
   <div class="content">
  <p>Do you want to update the user information also in CLink.ID</p>
	   <label><input type="checkbox" id="make_version_checkbox" name="make_version_checkbox" value="1" checked /> Make a version of the existing profile <span class="icon-box" title="Your current profile will be kept as a version in the CLink Registry"></span> </label><br><br>
  <center><input name="submit2" id="submit2" class="button " value="Yes" type="submit"> <input name="submit3" id="submit3" class="button " value="No" type="submit"></center>
  </div>
</div>
    <?php
}
// Register clink user profile form on edit profile
add_action( 'edit_user_profile', 'wpclink_custom_user_field' );	
// Register clink user profile form on show profile
add_action( 'show_user_profile', 'wpclink_custom_user_field' );
/**
 * CLink User Fields Update
 * 
 * @param integer $user_id  user id
 * 
 */
function wpclink_custom_user_field_save( $user_id )
{
        
	if ( !current_user_can( 'edit_user', $user_id ) )
	return FALSE;
	
	
	if(isset($_POST['clink_social_display'])){
    $clink_social_display = $_POST['clink_social_display'];
    update_user_meta( $user_id, 'wpclink_social_display', $clink_social_display );
	}
	
	// ISNI
	if(isset($_POST['wpclink_isni'])){
    $wpclink_isni = $_POST['wpclink_isni'];
    update_user_meta( $user_id, 'wpclink_isni', $wpclink_isni );
	}
	
	// ORCID
	if(isset($_POST['wpclink_orcid'])){
    $wpclink_orcid = $_POST['wpclink_orcid'];
    update_user_meta( $user_id, 'wpclink_orcid', $wpclink_orcid );
	}
	
	// PLUSID
	if(isset($_POST['wpclink_plusid'])){
    $wpclink_plusid = $_POST['wpclink_plusid'];
    update_user_meta( $user_id, 'wpclink_plusid', $wpclink_plusid );
	}
    
    // DID
	if(isset($_POST['wpclink_did'])){
    $wpclink_did = $_POST['wpclink_did'];
    update_user_meta( $user_id, 'wpclink_did', $wpclink_did );
	}
	
	if(isset($_POST['user_display_email'])){
	$user_display_email = $_POST['user_display_email'];
		if($user_display_email == '1'){
			update_user_meta( $user_id, 'wpclink_email_display_status','yes');
		}else{
			update_user_meta( $user_id, 'wpclink_email_display_status','no');
		}
	}else{
		update_user_meta( $user_id, 'wpclink_email_display_status','no');	
	}
	
}
// Register clink user fileds save on profile update
add_action( 'edit_user_profile_update', 'wpclink_custom_user_field_save' );
// Register clink user fileds save on personal option update
add_action( 'personal_options_update', 'wpclink_custom_user_field_save' );
/**
 * CLink Author Creator Column
 * 
 * @param string $columns default column
 * 
 */
function wpclink_author_creator_column( $columns ) {  
    //print_r($columns);  
    $columns[ 'author' ] = 'Author / Creator';  
    return $columns;  
}
// Register clink author creator column function
add_filter('manage_edit-post_columns','wpclink_author_creator_column');
add_filter('manage_edit-page_columns','wpclink_author_creator_column');
/**
 * CLink Publish Content Popup
 * 
 * To show license of linked content allow to UC-UT-UM | UC-AT-UM
 * 
 */
function wpclink_publish_linked_creation_popup(){
	if(isset($_GET['page']) and ($_GET['page'] == 'content_link_post.php' || $_GET['page'] == 'content_link_page.php' || $_GET['page'] == 'content_link_media.php' )){ ?>
<div id="cl_publish_popup" class="cl_popup <?php if($_GET['page'] == 'content_link_media.php') echo 'media'; ?>" style="display:none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<div class="inner">
<h3><center>Publish <?php if($_GET['page'] == 'content_link_media.php'){ echo "Media"; }else{ echo "Creation";
	} ?></center></h3>
     <p class="cl_message"></p>
     <div class="cl_actions"><a class="button button-primary">Publish</a></div>
</div>
</div>
	<?php }
}
// Register publish content popup on footer of admin 
add_action('admin_footer','wpclink_publish_linked_creation_popup');
/**
 * CLink Request Error Popup
 * 
 * To show error when server is down
 * 
 */
function wpclink_request_error_popup(){
	$current_screen = get_current_screen();
	if($current_screen->id == "post" ||   $current_screen->id == "page" ||   $current_screen->id == "attachment"  || $current_screen->id == "clink_page_content_link_media"){
		echo '<div id="cl_request_error" class="cl_error_dialog" title="Error" style="display:none;"><div class="error-message-request"><p>Error Occur</p></div></div></div>';
	}
}
// Register publish content popup on footer of admin 
add_action('admin_footer','wpclink_request_error_popup');
/**
 * CLink Publish Content Popup
 * 
 * To show license of linked content allow to UC-UT-UM | UC-AT-UM
 * 
 */
function wpclink_ip_warning_popup(){
	if(isset($_GET['page']) and ($_GET['page'] == 'clink-links-inbound' )){ ?>
<div id="cl_ip_warning_popup" class="cl_popup" style="display:none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<div class="inner">
	<p><?php _e('There may be a security risk. Your creation is still secure because wpCLink stopped the connection before any data was exchanged.','cl_text') ?> <a href="https://docs.clink.media/wordpress-plugins/links/inbound-link/security" target="_blank"><?php _e('Learn More','cl_text'); ?></a></a></p>
	<p><?php _e(' wpCLink recommends you contact the licensee and verify this change. The email address of the licensee','cl_text'); ?> <span class="fill_licensee_email"></span></p>
	<p><a href="#" class="warning_update_ip"><?php _e('  Update the IP address for <span class="fill_licensor_site"></span> (unsafe)','cl_text'); ?></a></p>
</div>
</div>
	<?php }
}
// Register publish content popup on footer of admin 
add_action('admin_footer','wpclink_ip_warning_popup');
/**
 * CLink Publish Content Popup
 * 
 * To show license of linked content allow to UC-UT-UM | UC-AT-UM
 * 
 */
function wpclink_present_popup(){
	
	$current_screen = get_current_screen();
	if($current_screen->id == "post" ||   $current_screen->id == "page" ||   $current_screen->id == "attachment" ){ ?>
<div id="cl_present" class="cl_popup present" style="display: none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<span class="dashicons dashicons-move move-pbox"></span>
<div class="inner">
<table width="100%" border="0">
  <tbody>
	  
    <tr>
      <td width="50%"><strong>Right Origin</strong></td>
      <td width="50%">Licensed Right</td>
    </tr>
    <tr>
      <td><strong>Right Status</strong></td>
      <td>Active Right</td>
    </tr>
    <tr class="line">
      <td><strong>Right Type </strong></td>
      <td>Copy</td>
    </tr>
    <tr class="line">
      <td>&nbsp;</td>
      <td>Publish</td>
    </tr>
     <?php  
																		  
 	// Post ID
		$post_id = (isset($_GET['post'])) ? $_GET['post'] : '';
		
		if($taxonomy_permission = get_post_meta( $post_id, 'wpclink_programmatic_right_categories', true )){
		
			if($taxonomy_permission != 'non-editable'){
				echo '<tr class="line"><td>&nbsp;</td><td>';
				wpclink_programmatic_right_categories_label($taxonomy_permission);
				echo '</td></tr>';
			}
		}else{
			echo '<tr class="fill_class"><td>&nbsp;</td><td class=""><span class="taxo_class"></span></td></tr>';
			
		}
			
	  ?>
	  <tr>
      <td width="50%"><strong>Purpose</strong></td>
      <td width="50%">Non-commercial</td>
    </tr>
	   <tr>
      <td width="50%">&nbsp;</td>
      <td width="50%">Personal</td>
    </tr>
<?php 
$license_type = get_post_meta( $post_id, 'wpclink_license_selected_type', true);
      if($license_type != 'wpclink_marketplace') { ?>        
    <tr>
      <td><strong>CMS Type</strong></td>
      <td>WordPress</td>
    </tr>
<?php } ?>
	  <tr>
      <td><strong>Constraint Type</strong></td>
      <td>Non-exclusive</td>
    </tr>
	  <tr>
      <td><strong></strong></td>
      <td>Non-transferable</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Revocable</td>
    </tr>
    <tr>
      <td><strong>Territory</strong></td>
      <td>World</td>
    </tr>
  </tbody>
</table>
</div>
</div>
	<?php }
}
// Register publish content popup on footer of admin 
add_action('admin_footer','wpclink_present_popup');
/**
 * CLink Pre-Authorization Popup
 * 
 */
function wpclink_pre_auth_popups(){
	
	$current_screen = get_current_screen();
	
	if(isset($_GET['page'])){
		$page = $_GET['page'];
	}else{
		$page = '';
	}
	
	if((isset($_GET['page']) and ($page == 'cl-restriction')) || ($page == 'cl-restriction-page-available' ||  $current_screen->id == "post" ||   $current_screen->id == "page" ||  $current_screen->id == "attachment")){ 
	
	
			// Pre Author
			$clink_popup_pre_auth = wpclink_get_option('pre_auth_license');
			
			// E-Sign
			$clink_esign_pre_auth = wpclink_get_option('pre_auth_esign');
			
			
				$html = '<div id="cl_show_pre_auth" title="CONSENT TO PRE-AUTHORIZED" style="display:none;">'.$clink_popup_pre_auth.'</div>'; 
				
				$html.= '<div id="cl_show_esign_pre" title="CONSENT TO PRE-AUTHORIZED" style="display:none;">'.$clink_esign_pre_auth.'</div>'; 
				
				
				echo $html;
	
	
	 }
}
// Register pre-authorization popup on admin footer
add_action('admin_footer','wpclink_pre_auth_popups');
/**
 *  CLink Select License Popup
 * 
 */
function wpclink_do_popup_license_select(){
	
	
	// Versions
	$license_ver = wpclink_get_option('license_versions');
	
	$current_screen = get_current_screen();
	
	// if exists
	$page = (isset($_GET['page'])) ? $_GET['page'] : '';
	
	
	if($current_screen->id == "post" || 
	   $current_screen->id == "attachment" || 
	   $current_screen->id == "page"){  ?>
 <div class="loading-circle-quick" style="display: none"><div class="loading-wrapper"><span class="cl_loader"></span></div></div>
<div id="cl-license-selection_step2" class="cl_popup" style="display:none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<div class="inner"><div class="pre-auth-div"><input type="hidden" value="<?php if(isset($_GET['page']) and $_GET['page'] == 'cl-restriction'){ echo 'cl-restriction'; }elseif(isset($_GET['page']) and $_GET['page'] == 'cl-restriction-page-available'){ echo 'cl-restriction-page-available'; } ?>" id="page_name" name="page" />
<p><label><input name="pre_authorize" class="pre_auth_check" type="checkbox" value="1" /> I consent to <a id="cl_pop1">Pre-Authorized Lincensing of Referent Creations</a> and <a id="cl_pop2">Pre-Authorized application of E-Signature to License</a></label></p>
<input type="hidden" value="" id="referent_id_s2" name="referent" />
<input type="hidden" value="" id="license_version_s2" name="license_version" />
<input type="hidden" value="" id="license_class_s2" name="license_class" />
<input type="hidden" value="" id="price_value" name="price_value" />
<input type="hidden" value="" id="taxonomy_permission_slot" name="taxonomy_permission_slot" />
<input type="hidden" value="" id="marketplace_cat_slot" name="marketplace_cat_slot" />
<p class="pre-auth-btn">
<input type="button" class="button button button-large show-lc-btn" value="View License"  />
<input type="submit" class="button button-primary button-large make-ref-btn_s2" disabled="disabled" value="Confirm and Continue"  />
</p>
</div>
</div>
</div>
<div id="cl-license-show" title="License" style="display:none;">
<div class="inner">
<div class="license_text">
<div id="license_show_box"></div>
</div>
</div>
</div>
<?php if($page == 'cl-restriction') { ?>
<div id="cl-license-selection" class="cl_popup" style="display:none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<div class="inner">
<h3>Please select programmatic Right Categories </h3>
<h5 class="view_lstype"><a href="<?php echo menu_page_url( 'cl_templates', false ); ?>"><?php echo "View License Templates"; ?></a></h5>
<input type="hidden" value="<?php if($page == 'cl-restriction'){ echo 'cl-restriction'; }elseif($page == 'cl-restriction-page-available'){ echo 'cl-restriction-page-available'; } ?>" id="page_name" name="page" />
<select id="taxonomy_permission" name="taxonomy_permission" class="license_class_form" >
<option value="non-editable"> None</option>
<option value="AddToTaxonomy">Add to Taxonomy</option>
<option value="ModifyTaxonomy">Modify Taxonomy</option>
</select>
<input type="hidden" name="license_class" id="license_class"  value="personal" />
<input type="hidden" value="" id="referent_id" name="referent" />
<p><input type="submit" class="button button-primary button-large make-ref-btn" value="Select"  /></p>
</div>
</div>
<?php }else{ ?>
<div id="cl-license-selection" class="cl_popup" style="display:none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<div class="inner">
<h3>License</h3>
<h5 class="view_lstype"><a href="<?php echo menu_page_url( 'cl_templates', false ); ?>"><?php echo "View License Template"; ?></a></h5>
<input type="hidden" id="taxonomy_permission" name="taxonomy_permission" value="non-editable" />
<p><?php _e('Selected page to be published with Personal License','cl_text'); ?></p>	
<input type="hidden" value="<?php if($page == 'cl-restriction'){ echo 'cl-restriction'; }elseif($page == 'cl-restriction-page-available'){ echo 'cl-restriction-page-available'; } ?>" id="page_name" name="page" />
<input id="license_class" type="hidden" name="license_class" value="personal" />
<input type="hidden" value="" id="referent_id" name="referent" />
<p><input type="submit" class="button button-primary button-large make-ref-btn" value="Next"  /></p>
</div>
</div>
<?php }
		
	}else if((isset($page) and $page == 'cl-restriction') ||
	    $page == 'cl-restriction-page-available' ){ ?>
<div id="cl-license-selection_step2" class="cl_popup" style="display:none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<div class="inner">
<form class="pre-auth-form" action="" method="post">
<input type="hidden" value="<?php if($page == 'cl-restriction'){ echo 'cl-restriction'; }elseif($page == 'cl-restriction-page-available'){ echo 'cl-restriction-page-available'; } ?>" id="page_name" name="page" />
<p><label><input name="pre_authorize" class="pre_auth_check" type="checkbox" value="1" /> I consent to <a id="cl_pop1" href="#">Pre-Authorized Lincensing of Referent Creations</a> and <a id="cl_pop2" href="#">Pre-Authorized application of E-Signature to License</a></label></p>
<input type="hidden" value="" id="referent_id_s2" name="referent" />
<input type="hidden" value="" id="license_version_s2" name="license_version" />
<input type="hidden" value="" id="license_class_s2" name="license_class" />
<input type="hidden" value="" id="price_value" name="price_value" />
<input type="hidden" value="" id="taxonomy_permission_slot" name="taxonomy_permission_slot" />
<input type="hidden" value="" id="marketplace_cat_slot" name="marketplace_cat_slot" />
<p class="pre-auth-btn">
<input type="button" class="button button button-large show-lc-btn" value="View License"  />
<input type="submit" class="button button-primary button-large make-ref-btn_s2" disabled="disabled" value="Confirm and Continue"  />
<?php wp_nonce_field( 'wpclink_select_license', 'wpclink_select_license_field' ); ?>
</p>
</form>
</div>
</div>
<div id="cl-license-show" title="License" style="display:none;">
<div class="inner">
<div class="license_text">
<div id="license_show_box"></div>
</div>
</div>
</div>
<?php if($page == 'cl-restriction') { ?>
<div id="cl-license-selection" class="cl_popup" style="display:none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<div class="inner">
<h3>Please select programmatic Right Categories </h3>
<h5 class="view_lstype"><a href="<?php echo menu_page_url( 'cl_templates', false ); ?>"><?php echo "View License Templates"; ?></a></h5>
<input type="hidden" value="<?php if($page == 'cl-restriction'){ echo 'cl-restriction'; }elseif($page == 'cl-restriction-page-available'){ echo 'cl-restriction-page-available'; } ?>" id="page_name" name="page" />
<select id="taxonomy_permission" name="taxonomy_permission" class="license_class_form" >
<option value="non-editable"> None</option>
<option value="AddToTaxonomy">Add to Taxonomy</option>
<option value="ModifyTaxonomy">Modify Taxonomy</option>
</select>
<input type="hidden" value="personal" id="license_class" name="license_class" />
<input type="hidden" value="" id="referent_id" name="referent" />
<p><input type="submit" class="button button-primary button-large make-ref-btn" value="Select"  /></p>
</div>
</div>
<?php }else{ ?>
<div id="cl-license-selection" class="cl_popup" style="display:none;">
<span class="dashicons dashicons-no-alt close-pbox"></span>
<div class="inner">
<h3>License </h3>
<h5 class="view_lstype"><a href="<?php echo menu_page_url( 'cl_templates', false ); ?>&cl_license_class=permission"><?php echo "View License Template"; ?></a></h5>
<input type="hidden" id="taxonomy_permission" name="taxonomy_permission" value="non-editable" />
<p><?php _e('Selected page to be published with Personal License','cl_text'); ?></p>	
<input type="hidden" value="<?php if($page == 'cl-restriction'){ echo 'cl-restriction'; }elseif($page == 'cl-restriction-page-available'){ echo 'cl-restriction-page-available'; } ?>" id="page_name" name="page" /> 
<input id="license_class" type="hidden" name="license_class" value="personal" />
<input type="hidden" value="" id="referent_id" name="referent" />
<p><input type="submit" class="button button-primary button-large make-ref-btn" value="Next"  /></p>
</div>
</div>	
<?php } ?>
	<?php }
}
// Register select the license popup on admin footer
add_action('admin_footer', 'wpclink_do_popup_license_select');
/**
 * CLink Accept Pre Auth Popops
 * 
 */
function wpclink_accept_pre_auth_popups($post_id = 0, $meta_key = NULL){
								
	// View Time
	$view_time = current_time("Y-m-d H:i:s", true);
	
	// Audit
	$return_id = update_post_meta($post_id,$meta_key,$view_time);
	
	return 	$return_id;			
}
/**
 * CLink Remove Quick Edit on the Referent Linked Contents
 * 
 * @param array $unset_actions post actions
 * @param object $post post object 
 * 
 * @return array unset actions
 */
function wpclink_remove_quick_edit_referent_linked_content( $unset_actions, $post ) {
	
	// Screen
  	global $current_screen;
	// Post and Page
	if ( $current_screen->post_type == 'post'  || $current_screen->post_type == 'page' ){
	
	// Post ID
	$post_id = $post->ID;
	
		// Referent Content
		if(wpclink_check_license_by_post_id($post_id) > 0){
			
			// Quick Edit
			unset( $unset_actions[ 'inline hide-if-no-js' ] );
		}
	
	}
	return $unset_actions;
}
// Register clink remove quick edit on the referent link post functions
add_filter( 'post_row_actions', 'wpclink_remove_quick_edit_referent_linked_content', 10, 2 );
add_filter( 'page_row_actions', 'wpclink_remove_quick_edit_referent_linked_content', 10, 2 );
/**
 * CLink Remove Quick Edit on Linked Content
 * 
 * @param array $unset_actions actions link of row
 * @param object $post post object
 * 
 */
function wpclink_remove_quick_edit_linked_content( $unset_actions, $post ) {
	
	// Screen
  	global $current_screen;
	// Post and Page
	if ( $current_screen->post_type == 'post'  || $current_screen->post_type == 'page' ){
	
	// Post ID
	$post_id = $post->ID;
	
		// Linked Content
		if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
			
			// Quick Edit
			unset( $unset_actions[ 'inline hide-if-no-js' ] );
		}
	
	}
	return $unset_actions;
}
add_filter( 'post_row_actions', 'wpclink_remove_quick_edit_linked_content', 10, 2 );
add_filter( 'page_row_actions', 'wpclink_remove_quick_edit_linked_content', 10, 2 );
function wpclink_disable_editing_linked_creation( $post ){
	
	if (  get_current_screen()->post_type === 'post' || get_current_screen()->post_type === 'page') {
	
	 
		 if(isset($_GET['post'])){
			 if(is_numeric($_GET['post'])){
		 
			 // Post ID
			$post_id = $_GET['post'];
			
			$license_class = get_post_meta( $post_id, 'wpclink_creation_license_class', true );
			$taxonomy_permission = get_post_meta( $post_id, 'wpclink_programmatic_right_categories', true );
				 
				// Only Linked Content
				if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
						if($taxonomy_permission == 'ModifyTaxonomy'){ ?>
						<style>
							#poststuff input,
							#poststuff textarea,
							.edit-post-visual-editor,
							.editor-post-excerpt__textarea .components-textarea-control__input{
								pointer-events : none;
								opacity: 0.5;
							}
							#poststuff #categorydiv input,
							#poststuff #tagsdiv-post_tag input,
							#poststuff input#publish{
								pointer-events :all;
								opacity:1;
							}
					</style>
<script>
	jQuery('body,html').on('keypress click', function() {
	jQuery('.editor-post-excerpt__textarea .components-textarea-control__input').attr('readonly', true);
	jQuery('.editor-post-excerpt__textarea .components-textarea-control__input').attr('disabled',true);
	});
</script>
					<?php	}else if($taxonomy_permission == 'non-editable'){ ?>
					
					<style>
						#poststuff input,
						#poststuff textarea,
						#cl_linked_tags_meta,
						#cl_linked_categories_meta,
						.edit-post-visual-editor,
						.edit-post-header-toolbar,
						.editor-post-excerpt__textarea .components-textarea-control__input,
					.components-panel .plugin-sidebar-content-status,
					.components-panel .plugin-sidebar-content-version,
					.components-panel .editor-post-format,
					.components-panel .edit-post-post-schedule,
					.components-panel .editor-post-format__content,
					.components-panel .components-panel__row,
					.components-panel .components-button,
					.components-panel .editor-post-link,
					.components-panel .editor-post-last-revision__title,
					.components-panel .edit-post-post-link__link,
					.components-panel .editor-post-taxonomies__hierarchical-terms-list,
					.components-panel .components-form-token-field__input,
					.components-panel .editor-post-featured-image__toggle,
					.components-panel .components-textarea-control__input,
					.components-panel .edit-post-post-link__preview-label,
					.components-panel .components-form-token-field,
					.components-panel .components-checkbox-control__label{
						pointer-events : none;
						opacity: 0.5;
					}
					</style>
					<script>
	jQuery('body,html').on('keypress click', function() {
	jQuery('#cl_linked_tags_meta,#cl_linked_categories_meta,.edit-post-visual-editor,.edit-post-header-toolbar,.components-panel .plugin-sidebar-content-status,.components-panel .plugin-sidebar-content-version,.components-panel .editor-post-format,.components-panel .edit-post-post-schedule,.components-panel .editor-post-format__content,.components-panel .components-panel__row,.components-panel .components-button,.components-panel .editor-post-link,.components-panel .editor-post-last-revision__title,.components-panel .edit-post-post-link__link,.components-panel .editor-post-taxonomies__hierarchical-terms-list,.components-panel .components-form-token-field__input,	.components-panel .editor-post-featured-image__toggle,.components-panel .components-textarea-control__input,.components-panel .edit-post-post-link__preview-label,.components-panel .components-form-token-field,.components-panel .components-checkbox-control__label').attr('readonly', true);
	jQuery('#cl_linked_tags_meta,#cl_linked_categories_meta,.editor-post-taxonomies__hierarchical-terms-input').attr('disabled',true);
	});
</script>
<?php	
						}else if($taxonomy_permission == 'AddToTaxonomy'){ ?>
					
					<style>
					
						.editor-post-taxonomies__hierarchical-terms-list > div{
							display: none;
						}
						#editor-post-taxonomies__hierarchical-terms-filter-1,
						#editor-post-taxonomies__hierarchical-terms-filter-0{
							display: none;
						}
						.editor-post-taxonomies__hierarchical-terms-add,
						label[for="editor-post-taxonomies__hierarchical-terms-filter-0"]{
							display: none;
						}
						.editor-post-taxonomies__hierarchical-terms-list:before{
							content: "Add to Taxonomy Categories can be selected in the Advanced Panel";
						}
						#poststuff input,
						#poststuff textarea
						{
							pointer-events : none;
							opacity: 0.5;
						}
						#cl_linked_tags_meta input,
						#cl_linked_categories_meta input,
						#poststuff input#publish{
							pointer-events :all;
							opacity:1;
						
						}
						
						.edit-post-visual-editor,
						.edit-post-header-toolbar,
					.editor-post-excerpt__textarea .components-textarea-control__input,
					.components-panel .plugin-sidebar-content-status,
					.components-panel .plugin-sidebar-content-version,
					.components-panel .editor-post-format,
					.components-panel .edit-post-post-schedule,
					.components-panel .editor-post-format__content,
					.components-panel .components-panel__row,
					.components-panel .components-button,
					.components-panel .editor-post-link,
					.components-panel .editor-post-last-revision__title,
					.components-panel .edit-post-post-link__link,
					.components-panel .editor-post-taxonomies__hierarchical-terms-list > div,
					.components-panel .components-form-token-field__input,
					.components-panel .editor-post-featured-image__toggle,
					.components-panel .components-textarea-control__input,
					.components-panel .edit-post-post-link__preview-label,
					.components-panel .components-form-token-field,
					.components-panel .components-checkbox-control__label{
						pointer-events : none;
						opacity: 0.5;
					}
					</style>
<script>
	jQuery(document).ready(function(){
		jQuery('#poststuff input[type="checkbox"]').attr('disabled',true);
		jQuery('#poststuff #cl_linked_categories_meta input[type="checkbox"], #poststuff #cl_linked_tags_meta input[type="checkbox"]').attr('disabled',false);
	});
</script>
				<?php	
						}
				}
			 }
		 }
	}
}
add_action("admin_head","wpclink_disable_editing_linked_creation");
/**
 * CLink Disable Author Selection
 * 
 * 
 */
function wpclink_disable_author_selection(){
	
	// Global Post IO
	if(isset($_GET['post'])){
		$post_id = $_GET['post'];
	}else{
		$post_id = '';
	}
	
	// Author ID
	$author_id = get_post_field ('post_author', $post_id);
	$current_user_id = get_current_user_id();
	
	// New rule added
	if($author_id != $current_user_id){
		echo '<style>.components-panel__row .components-combobox-control .components-base-control__field { pointer-events : none; opacity: 0.5;}</style>';
	}
}
add_action("admin_head","wpclink_disable_author_selection");
/**
 * CLink Linked Content Supplementary  Meta Boxes
 * 
 * @param object $post post object
 * 
 */
function wpclink_add_taxonomy_to_advanced_panel( $post ){
	
	if (  get_current_screen()->post_type === 'post') {
	
	 
		 if(isset($_GET['post'])){
			 if(is_numeric($_GET['post'])){
		 
			 // Post ID
			$post_id = $_GET['post'];
			
				$license_class = get_post_meta( $post_id, 'wpclink_creation_license_class', true );
				
				 // Taxonomy Permission
				$taxonomy_permission = get_post_meta( $post_id, 'wpclink_programmatic_right_categories', true );
				 
				 if($taxonomy_permission == 'AddToTaxonomy'){
					 $label = 'Added';
					 $label2 = 'Added';
				 }else if($taxonomy_permission == 'ModifyTaxonomy'){
					  $label = 'Referent';
					  $label2 = 'Referent';
				 }else{
					 $label = '';
					 $label2 = '';
				 }
				 				 
				// Only Linked Content
				if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
					if($license_class == 'personal'){
						if($taxonomy_permission == 'AddToTaxonomy'){
		// Categories
		add_meta_box( 'cl_linked_categories_meta', __( $label.' Categories', 'cl_text' ), 'wpclink_display_added_categories_advanced_panel', 'post' );				
		// Tags
		add_meta_box( 'cl_linked_tags_meta', __( $label2.' Tags', 'cl_text' ), 'wpclink_display_added_tag_advanced_panel', 'post' );
		
							
						}
					}
		
				}
			 }
		 }
		 
	 }
			 
}
// Register Clink linked content supplementary meta boxes function
add_action( 'add_meta_boxes', 'wpclink_add_taxonomy_to_advanced_panel' );
/**
 * CLink Linked Content Copyright Owner Meta Boxes
 * 
 * @param object $post post object
 * 
 */
function wpclink_register_rights_holder_advanced_panel( $post ){
	
	if (  get_current_screen()->post_type === 'post' || get_current_screen()->post_type === 'page') {
	
	 
		 if(isset($_GET['post'])){
			 if(is_numeric($_GET['post'])){
		 
			 // Post ID
			$post_id = $_GET['post'];
			
				
						
		// Copyright Owner
		add_meta_box( 'cl_right_holder_meta', __( 'Rights Holder', 'cl_text' ), 'wpclink_display_rights_holder_advanced_panel', 'post' );
		
		
				
		
				
			 }
		 }
		 
	 }
			 
}
// Register Clink linked content supplementary meta boxes function
add_action( 'add_meta_boxes', 'wpclink_register_rights_holder_advanced_panel' );
function wpclink_display_rights_holder_advanced_panel($post){
	
	$post_id = $post->ID;
	$user_id = get_post_meta($post_id,'wpclink_rights_holder_user_id', true);
	$identifier = get_user_meta($user_id,'wpclink_party_ID',true);
	
	
	
	// Registered
	if($clink_id = get_post_meta($post_id,'wpclink_creation_ID',true)){
		
		$user_info = get_userdata($user_id);
	
		$html = '<p>'.$user_info->display_name.'</p><p><a href="'.WPCLINK_ID_URL.'/#objects/'.$identifier.'">'.wpclink_do_icon_clink_ID($identifier).'</a></p>';
		
	}else{
		$html = '<p>Rights Holder has not been registered</p>';
	}
	
	echo '<div class="wpclink_display_right_holder_box">'.$html.'</div>';
	
	
}
/**
 * Linked Content Supplementary Tags
 *
 * @param int $post_id The post ID.
 */
function wpclink_update_taxonomy_linked_post( $post_id ){
		
	// return if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
	}
	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}
	
	
	// store custom fields values
	if( isset( $_POST['cl_linked_categories'] ) ){
		$linked_categories = (array) $_POST['cl_linked_categories'];
		
		
		if($referent_cat = get_post_meta($post_id,'wpclink_referent_categories',true)){
			$linked_categories = array_unique(array_merge($referent_cat,$linked_categories));
		}
		
		// sinitize array
		$linked_categories = array_map( 'sanitize_text_field', $linked_categories );
		
		
		
		// save data
		wp_set_post_categories( $post_id, $linked_categories, false );
		
	}else if(isset( $_POST['append_cat'] )){
		
		if($referent_cat = get_post_meta($post_id,'wpclink_referent_categories',true)){
			$linked_categories = $referent_cat;
		}
		
		// sinitize array
		$linked_categories = array_map( 'sanitize_text_field', $linked_categories );
		
				
		// save data
		wp_set_post_categories( $post_id, $linked_categories, false );
		
	}
	
	
	
	
	$tags_name = array();
	
	
	// store custom fields values
	if( isset( $_POST['cl_linked_tags'] ) ){
		
		$linked_tags = (array) $_POST['cl_linked_tags'];
		// sinitize array
		$linked_tags = array_map( 'sanitize_text_field', $linked_tags );
		// save data
		
		foreach($linked_tags as $single_tag){
			$tag_data = get_term($single_tag,'post_tag');
			$tags_name[] = $tag_data->name;
		}
		
		
		
		
		if($referent_tags = get_post_meta($post_id,'wpclink_referent_tags',true)){
			
			$tags_name = array_unique(array_merge($referent_tags,$tags_name));
		}
		
		
		
		wp_set_post_tags( $post_id, $tags_name, false );
		
		
		
		// By Function
		$post_id_object = array('ID' => $post_id);
			
		// CLink Media Call
		wpclink_register_creation((object)$post_id_object,'');
				
		
	
	}else if(isset( $_POST['append_tag'] )){
		
	
		
		if($referent_tags = get_post_meta($post_id,'wpclink_referent_tags',true)){
			
			$tags_name = $referent_tags;
		}
		
		
		
		wp_set_post_tags( $post_id, $tags_name, false );
		
		
		
		// By Function
		$post_id_object = array('ID' => $post_id);
			
		// CLink Media Call
		wpclink_register_creation((object)$post_id_object,'');
				
		
	
	}
	
	
		
}
add_action( 'save_post', 'wpclink_update_taxonomy_linked_post'  );
/**
 * CLink Linked Supplementary Categories Metabox
 *
 * @param post $post The post object
 */
function wpclink_display_added_tag_advanced_panel( $post ){
	
	// make sure the form request comes from WordPress
	wp_nonce_field( basename( __FILE__ ), 'clink_linked_supplementary_tags' );
	
	
	// All categories
	$all_tags = get_tags( array(
	    'orderby' => 'name',
		'hide_empty' => false,
    	'order'   => 'ASC'
	) );
	
	
	
	// Post Categories
	$post_tags = wp_get_post_tags( $post->ID );
	$tgs = array();
	$tg_ids = array();
	$selected_tag = array();
	$tags_only_ids = array();
		 
	foreach($post_tags as $t){
		
		$selected_tag[] = $t->term_id;
	}
	
	foreach($all_tags as $single_tag){
		
		$tags_only_ids[] = $single_tag->term_id;
		
	}
	
	
	$referent_tags_ids = array();
	
	if($referent_tags = get_post_meta($post->ID,'wpclink_referent_tags',true)){
		
		foreach($referent_tags as $single_ref){
			$term = get_term_by('name',$single_ref, 'post_tag');
			$referent_tags_ids[] = $term->term_taxonomy_id;
			
		}
		
	}else{
		$referent_tags_ids = array();	
	}
	
	// Only Supplementary Categories
	$supplementary_tag = array_diff($tags_only_ids,$referent_tags_ids);
	
	$taxonomy_permission = get_post_meta( $post->ID, 'wpclink_programmatic_right_categories', true );
	
	if($taxonomy_permission == 'ModifyTaxonomy'){
		$disabled = '';
	}else{
		$disabled = 'disabled="disabled"';
	}
	
	?>
	<div class='inside'>
    <div class="supplementary_box">
		<input type="hidden" name="append_tag" value="1" />
		<p>
		<?php
			foreach ( $all_tags as $tag ) {
						
				
				if(in_array($tag->term_id,$supplementary_tag)){
					
					?>
				<label for="cl_linked_tags-<?php echo $tag->term_id; ?>"><input id="cl_linked_tags-<?php echo $tag->term_id; ?>" type="checkbox" name="cl_linked_tags[]"  value="<?php echo $tag->term_id; ?>" <?php if(in_array($tag->term_id,$selected_tag)){ echo "checked=checked"; } ?>  /><?php echo $tag->name; ?> </label><br />
			
				<?php
				}else{
				
					if($taxonomy_permission == 'ModifyTaxonomy'){
					}else{}
				
				}
			}
		?>
		</p>
        </div>
	</div>
	<?php
}
/**
 * CLink Linked Supplementary Categories Metabox
 *
 * @param post $post The post object
 */
function wpclink_display_added_categories_advanced_panel( $post ){
	
	// make sure the form request comes from WordPress
	wp_nonce_field( basename( __FILE__ ), 'clink_linked_supplementary_categories' );
	
	
	// All categories
	$categories = get_categories( array(
	    'orderby' => 'name',
		'hide_empty' => false,
    	'order'   => 'ASC'
	) );
	
	// Post Categories
	$post_categories = wp_get_post_categories( $post->ID );
	$cats = array();
	$category_ids = array();
		 
	foreach($post_categories as $c){
		$cat = get_category( $c );
		$cats[] = $cat->term_id;
	}
	
	foreach($categories as $single_category){
		
		$category_ids[] = $single_category->term_id;
		
	}
	
	if($referent_categories = get_post_meta($post->ID,'wpclink_referent_categories',true)){	
		
	}else{
		$referent_categories = array();	
	}
	// Only Supplementary Categories
	$supplementary_cat = array_diff($category_ids,$referent_categories);
	
	$taxonomy_permission = get_post_meta( $post->ID, 'wpclink_programmatic_right_categories', true );
	
	if($taxonomy_permission == 'ModifyTaxonomy'){
		$disabled = '';
	}else{
		$disabled = 'disabled="disabled"';
	}
	
	
	?>
<input type="hidden" name="append_cat" value="1" />
<input type="hidden" id="cl_taxonomy_permission" value="<?php echo $taxonomy_permission; ?>" />
	<div class='inside'>
    <div class="supplementary_box">
		<p>
		<?php
			foreach ( $categories as $category ) {
				
				if(in_array($category->term_id,$supplementary_cat)){
					
					?>
				<label for="cl_linked_categories-<?php echo $category->term_id; ?>"><input id="cl_linked_categories-<?php echo $category->term_id; ?>" type="checkbox" name="cl_linked_categories[]" class="checkhit" data-catid="<?php echo $category->term_id; ?>" value="<?php echo $category->term_id; ?>" <?php if(in_array($category->term_id,$cats)){ echo "checked=checked"; } ?>  /><?php echo $category->name; ?> </label><br />
				<?php
				}else{
				
					if($taxonomy_permission == 'ModifyTaxonomy'){
					}else{}
				
				}
			}
		?>
		</p>
		<script>
			
jQuery(".checkhit").change(function() {
	var category_id = jQuery(this).data("catid");
	
  if(this.checked) {
	jQuery('#editor-post-taxonomies-hierarchical-term-'+category_id).prop( "checked", true );
    }
  else {
    jQuery('#editor-post-taxonomies-hierarchical-term-'+category_id).prop( "checked", false );
  }
});
			
		</script>
        </div>
	</div>
	<?php
}
/**
 * CLink Sidebar Scripts
 *
 * Render additional features of the sidebar
 */
function wpclink_display_clink_panel() {
	
	 $current_screen = get_current_screen();
	 if($current_screen->id == 'post' || $current_screen->id == 'page' || $current_screen->id == 'attachment'){
		
	// Global Post IO
	if(isset($_GET['post'])){
		$post_id = $_GET['post'];
	}else{
		$post_id = '';
	}
	
	// Screen
	$current_screen = get_current_screen();
	
	// Show Linked Mode
	$show_linked_mode = wpclink_get_option('show_linked_mode');
	
	// License Data
		 
	$linked_creation_ID_list = array();
	$license_data_creation = wpclink_get_all_liceses_by_post_id($post_id);
	foreach($license_data_creation as $license_data_creation_single){
		
		if($license_linked_creation_ID = wpclink_get_license_meta($license_data_creation_single['license_id'],'linked_creation_ID',true)){
			
			$linked_creation_ID_list[] = $license_linked_creation_ID;
			
		}
		
	}
	// CLink.ID
	$clink_id = get_post_meta($post_id,'wpclink_creation_ID',true);
	if(empty($clink_id)){
		$clink_id = ''; 
	}else{
		$clink_id = '<a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$clink_id.'">'.wpclink_do_icon_clink_ID($clink_id).'</a>';
	}
		 
		 
	// All Links Page
	$inbound_menu =  menu_page_url( 'clink-links-inbound', false );
	$inbound_menu = add_query_arg(array('filter_by' => 'post','post_id' => $post_id),$inbound_menu );
	
		 
	// License Class
	$license_class = get_post_meta($post_id,'wpclink_creation_license_class',true);
	$taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories', true);
		 
		 
	$after_24h_format = '';
	$after_24h = '';
		 
	if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
		// Last Update
		if($promotion_last_registration = wpclink_get_option('last_linked_creation_date')){
			// Adding 24 Hours
			$after_24h = strtotime("+24 hours", strtotime($promotion_last_registration));
			$after_24h_format =  date('Y-m-d h:i:s', $after_24h);
		}
	}else{
		// Last Update
		if($promotion_last_registration = wpclink_get_option('last_registered_creation_date')){
			// Adding 24 Hours
			$after_24h = strtotime("+24 hours", strtotime($promotion_last_registration));
			$after_24h_format =  date('Y-m-d h:i:s', $after_24h);
		}
	}
		 
 	$external_hyperlink = '';	
	
	if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
			
			$referent_license_data = wpclink_get_license_by_linked_post_id($post_id);
			$license_id = $referent_license_data['license_id'];
			$download_id = wpclink_get_license_meta($license_id,'license_download_id',true);
			$external_hyperlink = get_bloginfo("url").'?license_my_show_id='.$license_id.'&download_id='.$download_id;
		
			// Right Transation
			$license_data_new = wpclink_get_license_linked($license_id);
			$license_rights_transaction_ID = $license_data_new['rights_transaction_ID'];
			
		
			$license_external_link = '<a class="external-link" target="_blank" href="'.get_bloginfo("url").'?license_my_show_id='.$license_id.'&download_id='.$download_id.'" style="color:#333;"><span class="dashicons dashicons-migrate"></span></a>';
		
			
	}else{
		
		// Right Transation
		$license_data_new = wpclink_get_license_by_post_id($post_id);
		$license_rights_transaction_ID = $license_data_new['rights_transaction_ID'];
		
		
		$license_external_link = '';
		 
		if($license_class == 'personal'){
			$license_identifier = '<a target="_blank" href="https://licenses.clink.id/uc-ut-um/nc-0.9"><span class="clink-typo"></span> uc-ut-um/nc-0.9 <span class="dashicons dashicons-external"></span></a>';
		}
	}
	
		 
		 $html = '';
		 
	// Versions	
	if($has_version = get_post_meta($post_id,'wpclink_versions',true)){
		$versions = $has_version;
		
		
		
		foreach($versions as $single_ver){
			$html .= "<li><a href='".WPCLINK_ID_URL."/#objects/".$single_ver."' target='_blank' >".wpclink_do_icon_clink_ID($single_ver,true)."</a></li>";
		}
	}
	// Version Count
	if(empty($has_version)){
		$version_count = 'N/A';
	}
	
	// License Versions
	$license_version = wpclink_get_option('license_versions');
	$license_version_select = $license_version;
	
	// Links
	$links_html = NULL;
	$links_count = 0;
		 
	$links = wpclink_get_all_liceses_by_post_id($post_id);
		 
		 
	if($linked_identifiers = get_post_meta($post_id,'linked_creation_IDs',true)){
		 
		if(is_array($linked_identifiers)){
			
			$links_count = count($linked_identifiers);
			
			
			$link_count = 0;
			
			foreach($linked_identifiers as $single){
				
					
					foreach($single as $single_small){
						$links_html = '<li><a target="_blank" href="'.WPCLINK_ID_URL.'/#objects/'.$single_small.'">'.wpclink_do_icon_clink_ID($single_small).'</a></li>';
						
						
					}
				
				
				$link_count++;
				if($link_count > 2)	break;
				
				}
				
				$links_html = '<ul class="listoflinks">'.$links_html.'</ul>';
			
				
				
				
		}
		
	}
	
?>
<script>
<?php $current_user_id = get_current_user_id();
	  $author_id = get_post_field ('post_author', $post_id);
		 
 	if(!wpclink_user_has_creator_list($current_user_id) and (current_user_can('editor') || current_user_can('administrator'))){
		
		if($current_user_id != $author_id){
			
			if(!empty($clink_id)){
				
		 $author_info = get_userdata($author_id);
		 $author_displayname = $author_info->display_name;
	?>
jQuery( document ).ready(function() {
	setTimeout(function(){
		wp.domReady(function() {
	( function( wp ) {
		wp.data.dispatch('core/notices').createNotice(
			'warning',
			"<?php echo $author_displayname; ?> claimed sole attribution and rights for this post/page and it is registered accordingly.  Attribution and rights cannot be changed with the current version of the wpCLink plugin.", 
			{
				isDismissible: true, 
			}
		);
	} )( window.wp );
		});
	},2000);
	
});
	
	
<?php
			}
		} 
 } ?>
	
function wpclink_ls_trigger(){
			//  Post ID
			var get_referent_id = jQuery("#post_ID").val();
			var get_taxonomy_permission = jQuery('#quick-license-type').val();
			
			// License Class
			var get_license_class = 'personal';
	
			jQuery.ajax({
				cache: false,
				url: ajaxurl,
				data: {
					'action': 'wpclink_make_ref_post_step2',
					'license_class' : get_license_class,
					'referent_id' : get_referent_id,
				},
				success:function(data) {
				
				// License Screen Show
				jQuery.ajax({
					cache: false,
					url: ajaxurl, 
					data: {
						'action': 'wpclink_do_popup_license_template',
						'referent_id' : get_referent_id,
						'license_class' : get_license_class
					},
					success:function(data) {
						jQuery('body').append(data);
						jQuery( "#cl-license-show-screen" ).dialog({maxWidth:"auto",maxHeight: 500,width: 800,height:500,fluid: true,closeOnEscape: false,close: function(event, ui){ jQuery(this).dialog('close');  jQuery(this).remove();} });
						// Scroll
						jQuery("#cl-license-show-screen").scrollTop(0);
						jQuery('#cl-license-selection').hide();
						// Validate
						if(data == "0"){
							jQuery('#cl-license-selection_step2').show();
						}else{
							jQuery('#cl-license-selection_step2').hide();
						}
						
						// Accept Button
						jQuery("#accept_license_check").change(function() {
						if(this.checked) {
							jQuery( "#license_next" ).prop( "disabled", false );
						}else{
							jQuery( "#license_next" ).prop( "disabled", true );
						}
						});
						jQuery("#license_next").click(function(){
							jQuery(this).parents(".ui-dialog-content").dialog('close');
							jQuery("#cl-license-selection_step2").show(200);
						});
				}});
						jQuery('#cl-license-selection_step2 .close-pbox').click(function(){
							jQuery('#cl-license-selection_step2').hide(200);
						});
						// This outputs the result of the ajax request
						jQuery('#license_show_box').html(data);
						jQuery('#license_class_s2').val(get_license_class);
						jQuery('#taxonomy_permission_slot').val(get_taxonomy_permission);
										
						
					
						jQuery('#referent_id_s2').val(get_referent_id);
						jQuery('.cl_show_license').click(function(){
							jQuery('#cl_show_pre_auth').hide();
							jQuery('#cl_show_current_license').show();
						});
						jQuery('.back_to_pre').click(function(){
							jQuery('#cl_show_pre_auth').show();
							jQuery('#cl_show_current_license').hide();
						});
						jQuery('#cl_show_esign_pre .close-pbox').click(function(){
							jQuery('#cl_show_esign_pre').hide(200);
						});
						jQuery('#cl_show_pre_auth .close-pbox').click(function(){
							jQuery('#cl_show_pre_auth').hide(200);
						});
						// Pre Authorize Checkbox Time Update
						jQuery(".pre_auth_check").change(function() {
							if(this.checked) {
								jQuery.ajax({
								url: ajaxurl,
								data: {
									'action': 'wpclink_pre_auth_accept_ajax',
									'cl_action' : 'add',
									'referent_id' : get_referent_id,
								},
								success:function(data) {
											jQuery( ".make-ref-btn_s2" ).prop( "disabled", false );
								}});
							}else{
								jQuery.ajax({
								url: ajaxurl,
								data: {
									'action': 'wpclink_pre_auth_accept_ajax',
									'cl_action' : 'remove',
									'referent_id' : get_referent_id,
								},
								success:function(data) {
									jQuery( ".make-ref-btn_s2" ).prop( "disabled", true );
								}});
							}
						});
					},
					error: function(errorThrown){
						console.log(errorThrown);
					},
				});
			
		
	}
var wpclink_checkExist = setInterval(function() {
   if (jQuery('.interface-pinned-items .components-button[aria-label="CLink"]').length) { 
	   // Found
	  jQuery('.interface-pinned-items .components-button[aria-label="CLink"]').click(function(){
		  // CLick
		  if(jQuery('.interface-pinned-items .components-button[aria-label="CLink"]').hasClass( "is-pressed" )){	  
			 // Already Pressed
		  }else{
			  // Trigger
			  wpclink_toolbox();
		  }
		});
	   
      clearInterval(wpclink_checkExist);
   }	   
 }, 500);
	
var wpclink_allow_register;
function wpclink_get_wpclink_post_status_is_disabled(){
	
	// Global Post ID
	var post_id_value = jQuery("#post_ID").val();
	
	// Ajax Request
		jQuery.ajax({
			url: ajaxurl, 
			data: {
				'action': 'wpclink_post_status_is_disabled',
				'post_id' : post_id_value
			},
			success:function(data) {
				
				// Validate Data
				if(data == '1'){
					jQuery('.set-registration').hide();
				}else if(data == '-1'){
					
					if(jQuery('.interface-pinned-items .components-button[aria-label="CLink"]').hasClass( "is-pressed" )){
	   					jQuery('.set-registration').hide();
					}else{
						jQuery('.set-registration').show();
					}
				}
			}
		});
	
}
	
function wpclink_get_post_status_editor(){
	
	// Global Post ID
	var post_id_value = jQuery("#post_ID").val();
	
	// Ajax Request
		jQuery.ajax({
			url: ajaxurl, 
			data: {
				'action': 'wpclink_post_status_print_editor',
				'post_id' : post_id_value
			},
			success:function(data) {
				
				// Validate Data
				if(data != '0'){
					
					jQuery(".status-slot").html(data);
					
				
					 var post_id_value;
					jQuery("#cl_register_process").change(function(){
						 post_id_value = jQuery("#post_ID").val();
						 if(jQuery(this).is(":checked")) {
							 wpclink_allow_register = 0;
							jQuery(".res_status").text("Enabled");
							jQuery('.set-registration').hide();
							jQuery.ajax({
								url: ajaxurl,
								data: {
									"action": "wpclink_post_exclude_save",
								   "post_id" : post_id_value,
									"cl_state" : "0"
								},
								success:function(data) {
								},
								error: function(errorThrown){
									console.log(errorThrown);
								}
							});  
						 }else{
							jQuery(".res_status").text("Disabled");
							 wpclink_allow_register = 1;
							 if(jQuery('.interface-pinned-items .components-button[aria-label="CLink"]').hasClass( "is-pressed" )){
	   					jQuery('.set-registration').hide();
					}else{
						jQuery('.set-registration').show();
					}
							 
							jQuery.ajax({
								url: ajaxurl,
								data: {
									"action": "wpclink_post_exclude_save",
								   "post_id" : post_id_value,
									"cl_state" : 1
								},
								success:function(data) {
								},
								error: function(errorThrown){
									console.log(errorThrown);
								}
							});  
						 }
					});
					
				}
				
			}
		});
}
	
function wpclink_setCookie(name, value, daysToLive) {
    // Encode value in order to escape semicolons, commas, and whitespace
    var cookie = name + "=" + encodeURIComponent(value);
    
    if(typeof daysToLive === "number") {
        /* Sets the max-age attribute so that the cookie expires
        after the specified number of days */
        cookie += "; max-age=" + (daysToLive*24*60*60);
        
        document.cookie = cookie;
    }
}
function wpclink_getCookie(name) {
    // Split cookie string and get all individual name=value pairs in an array
    var cookieArr = document.cookie.split(";");
    
    // Loop through the array elements
    for(var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split("=");
        
        /* Removing whitespace at the beginning of the cookie name
        and compare it with the given string */
        if(name == cookiePair[0].trim()) {
            // Decode the cookie value and return
            return decodeURIComponent(cookiePair[1]);
        }
    }
    
    // Return null if not found
    return 0;
}
/**
 * CLink Get CLink.ID by Ajax Request
 *
 */
function wpclink_get_clinkid_ajax(){
	
		// Global Post ID
		var post_id_value = jQuery("#post_ID").val();
		var return_value = 0;
		
		// Ajax Request
		jQuery.ajax({
			url: ajaxurl, 
			data: {
				'action': 'wpclink_get_clink_ID',
				'post_id' : post_id_value
			},
			success:function(get_response) {
				
				console.log('TRIGGER');
				
				var response;
				var data;
				var clinkid;
				var clmessage;
				
				if(typeof get_response === 'object' && get_response !== null){
					response = get_response;
					data = response.status;
					clinkid = response.clinkid;
					clmessage = response.message;
				}else{
					response = jQuery.parseJSON(get_response);
					data = response.status;
					clinkid = response.clinkid;
					clmessage = response.message;
				}
				
				if(response.complete == 'failed'){
					
					jQuery('.components-notice-list  .components-notice.is-error').hide();					
					( function( wp ) {
    wp.data.dispatch('core/notices').createNotice(
        'error', // Can be one of: success, info, warning, error.
        response.error_headline+ '. "'+response.code+' '+response.message+'". '+response.error_text.replace(" <br>", ""), // Text string to display.
        {
            isDismissible: true, // Whether the user can dismiss the notice.
            // Any actions the user can perform.
        }
    );
} )( window.wp );
				
								
				// Validate Data
				}else if(data == 'guid_error'){ 
					
					jQuery('.components-notice-list  .components-notice.is-error').hide();					
					( function( wp ) {
    wp.data.dispatch('core/notices').createNotice(
        'error', // Can be one of: success, info, warning, error.
        'wpCLink features are not available because the domain in the GUID does not match the domain of the site.', // Text string to display.
        {
            isDismissible: false, // Whether the user can dismiss the notice.
            // Any actions the user can perform.
        }
    );
} )( window.wp );
					
				}else if(data == 'promotion_disallow'){ 
					
					( function( wp ) {
    wp.data.dispatch('core/notices').createNotice(
        'error', // Can be one of: success, info, warning, error.
        'You have registered a Creation <?php echo $promotion_last_registration; ?> (UTC) Personal Edition allows one registration per 24h. Please try after <?php echo $after_24h_format; ?>  (UTC)', // Text string to display.
        {
            isDismissible: true, // Whether the user can dismiss the notice.
            // Any actions the user can perform.
        }
    );
} )( window.wp );
					
}else if(clmessage != '0'){
	
	
	
	if(response.complete == 'failed'){
		
			jQuery('.components-notice-list  .components-notice.is-error').hide();		
					( function( wp ) {
    wp.data.dispatch('core/notices').createNotice(
        'error', // Can be one of: success, info, warning, error.
        response.error_headline+ '. "'+response.code+' '+response.message+'". '+response.error_text.replace(" <br>", ""), // Text string to display.
        {
            isDismissible: true, // Whether the user can dismiss the notice.
            // Any actions the user can perform.
        }
    );
} )( window.wp );
		
	}
	
	
		
	
	// You are not Creator
	jQuery('.status-slot').html(clmessage);
	
	// CLink ID
	jQuery('.clinkid-slot .clinksidebar').html(clinkid);
	
	// License
	<?php if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
		// Only Referent Content
		}else{ ?>
				
		// Ajax Request
		jQuery.ajax({
			url: ajaxurl, 
			data: {
				'action': 'wpclink_do_clink_panel_license',
				'post_id' : post_id_value
			},
			success:function(data) {
				
				// Validate Data
				if(data != '0'){
					
					jQuery('.license-slot').html(data);
					
					jQuery('.plugin-sidebar-content-type').show();
					
				}
			}
		});
<?php } ?>
	
					
}else if(data != '0'){
	
	
			
	
	if(response.complete == 'failed'){
		
			jQuery('.components-notice-list  .components-notice.is-error').hide();		
					( function( wp ) {
    wp.data.dispatch('core/notices').createNotice(
        'error', // Can be one of: success, info, warning, error.
        response.error_headline+ '. "'+response.code+' '+response.message+'". '+response.error_text.replace(" <br>", ""), // Text string to display.
        {
            isDismissible: true, // Whether the user can dismiss the notice.
            // Any actions the user can perform.
        }
    );
} )( window.wp );
		
	}
	
data = clinkid;
				
<?php if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
		// Only Referent Content
		}else{ ?>
				
		// Ajax Request
		jQuery.ajax({
			url: ajaxurl, 
			data: {
				'action': 'wpclink_do_clink_panel_license',
				'post_id' : post_id_value
			},
			success:function(data) {
				
				// Validate Data
				if(data != '0'){
					
					jQuery('.license-slot').html(data);
					
					jQuery('.plugin-sidebar-content-type').show();
					
				}
			}
		});
<?php } ?>
				
				// Implemention
				jQuery('.clinkid-slot .clinksidebar').html(data);
					
					// Verify lenght
					if(data.length > 1){
					<?php if(empty($license_class)){
						if(wpclink_can_perform_license($post_id)){
							
						$screen = get_current_screen(); 
		 
		
						?>
				
						
						//alert(2);
								
jQuery(".accordion.registry-box").accordion({
	active:parseInt(wpclink_getCookie('wpclink_registry_tab')),
	collapsible: true,
	heightStyle: "content",
	animate: false,
	activate: function (event, ui) {
		if(jQuery(this).accordion('option', 'active') === 0){
			wpclink_setCookie('wpclink_registry_tab', 0,365);
		}else if(jQuery(this).accordion('option', 'active') === false){
			wpclink_setCookie('wpclink_registry_tab', 1,365);	 
		}
	}
});
			
jQuery(".accordion.license-box").accordion({
	active:parseInt(wpclink_getCookie('wpclink_license_tab')),
	collapsible: true,
	heightStyle: "content",
	animate: false,
	activate: function (event, ui) {
		if(jQuery(this).accordion('option', 'active') === 0){
			wpclink_setCookie('wpclink_license_tab', 0,365);
		}else if(jQuery(this).accordion('option', 'active') === false){
			wpclink_setCookie('wpclink_license_tab', 1,365);	 
		}
	}
});
jQuery(".accordion.links-box").accordion({
	active:parseInt(wpclink_getCookie('wpclink_links_tab')),
	collapsible: true,
	heightStyle: "content",
	animate: false,
	activate: function (event, ui) {
		if(jQuery(this).accordion('option', 'active') === 0){
			wpclink_setCookie('wpclink_links_tab', 0,365);
		}else if(jQuery(this).accordion('option', 'active') === false){
			wpclink_setCookie('wpclink_links_tab', 1,365);	 
		}
	}
});
						
					// Change License Class
					jQuery(".select-license").click(function(){
						
						 var get_referent_id = jQuery("#post_ID").val();
						 var get_license_class = 'personal';
						 var get_taxonomy_permission = jQuery('#quick-license-type').val();
					
					// License Class Save
					jQuery.ajax({
						cache: false,
						url: ajaxurl,
						data: {
							'action': 'wpclink_make_ref_post_step2',
							'license_class' : get_license_class,
							'referent_id' : get_referent_id,
						},
						success:function(data) {
							
							// License Screen Show
							jQuery.ajax({
								cache: false,
								url: ajaxurl, 
								data: {
									'action': 'wpclink_do_popup_license_template',
									'referent_id' : get_referent_id,
									'license_class' : get_license_class
								},
								success:function(data) {
										// Insert the license template in for current document
										jQuery('body').append(data);
									
										// License Template dialogue
										jQuery( "#cl-license-show-screen" ).dialog({maxWidth:"auto",maxHeight: 500,width: 800,height:500,fluid: true,closeOnEscape: false,close: function(event, ui){
											
											// Remove Button
											jQuery(this).dialog('close');  jQuery(this).remove();} });
									
											// Scroll
											jQuery("#cl-license-show-screen").scrollTop(0);
											// Hide previous popup
											jQuery('#cl-license-selection').hide();
											// Validate
											if(data == "0"){
												jQuery('#cl-license-selection_step2').show();
											}else{
												jQuery('#cl-license-selection_step2').hide();
											}
											
											// Enable checkbox for accept conditions
											jQuery("#accept_license_check").change(function() {
												if(this.checked) {
													jQuery( "#license_next" ).prop( "disabled", false );
												}else{
													jQuery( "#license_next" ).prop( "disabled", true );
												}
											});
									
											// Shou License Template
											jQuery("#license_next").click(function(){
												jQuery(this).parents(".ui-dialog-content").dialog('close');
												jQuery("#cl-license-selection_step2").show(200);
											});
									}});
									
									// On close
									jQuery('#cl-license-selection_step2 .close-pbox').click(function(){
										jQuery('#cl-license-selection_step2').hide(200);
									});
							
									// Show license content
									jQuery('#license_show_box').html(data);
							
									// License type value
									jQuery('#license_class_s2').val(get_license_class);
									jQuery('#taxonomy_permission_slot').val(get_taxonomy_permission);
							
									
							
									// Post ID
									jQuery('#referent_id_s2').val(get_referent_id);
									// Show Pre Authorized Popup
									jQuery('.cl_show_license').click(function(){
										jQuery('#cl_show_pre_auth').hide();
										jQuery('#cl_show_current_license').show();
									});
							
							
				
							
							if(get_taxonomy_permission == 'ModifyTaxonomy'){
								jQuery('.fill_class').show();
								jQuery('.taxo_class').html('Modify Taxonomy');
							}else if(get_taxonomy_permission == 'AddToTaxonomy'){
								jQuery('.fill_class').show();
								jQuery('.taxo_class').html('Add to Taxonomy');	 
							}
							
							
							
							
									// Go to Back
									jQuery('.back_to_pre').click(function(){
										jQuery('#cl_show_pre_auth').show();
										jQuery('#cl_show_current_license').hide();
									});
									
									// Close button esign pre authorized popup
									jQuery('#cl_show_esign_pre .close-pbox').click(function(){
										jQuery('#cl_show_esign_pre').hide(200);
									});
							
									// Close button pre authorized popup
									jQuery('#cl_show_pre_auth .close-pbox').click(function(){
										jQuery('#cl_show_pre_auth').hide(200);
									});
									// Pre Authorize Checkbox Time Update
									jQuery(".pre_auth_check").change(function() {
										if(this.checked) {
											jQuery.ajax({
											url: ajaxurl,
											data: {
												'action': 'wpclink_pre_auth_accept_ajax',
												'cl_action' : 'add',
												'referent_id' : get_referent_id,
											},
											success:function(data) {
												jQuery( ".make-ref-btn_s2" ).prop( "disabled", false );
											}});
										}else{
											jQuery.ajax({
											url: ajaxurl,
											data: {
												'action': 'wpclink_pre_auth_accept_ajax',
												'cl_action' : 'remove',
												'referent_id' : get_referent_id,
											},
											success:function(data) {
												jQuery( ".make-ref-btn_s2" ).prop( "disabled", true );
											}});
										}
									});
						},
						error: function(errorThrown){
							console.log(errorThrown);
						},
					});
						
						
						
					});
						<?php 
						} 
	} ?>
						
						
						// Show Version by Ajax request
						wpclink_print_version();
					}
				}
			},
			error: function(errorThrown){
				console.log(errorThrown);
			},
		});  
	}
</script>
<?php 
/*
 *  Hook for clink panel script
 *
 * - 'wpclink_panel_display_script' is the action hook.
*/
	do_action( 'wpclink_panel_display_script', $post_id );
		
?>
<script>
/**
 * CLink Get Versions by Ajax Request
 *
 * @param integer var post_id  Current Post ID
 */
function wpclink_get_versions_ajax(post_id){
	jQuery.ajax({
		url: ajaxurl, 
		data: {
			'action': 'wpclink_get_ajax_versions_ajax_request',
			'post_id' : post_id,
			'version' : "1"
		},
		success:function(data) {
			
			// Show version list
			jQuery('.all_versions').html(data);
			if(data.length > 0){
				jQuery('.show_version').removeClass('hide');
				jQuery('.show_version').addClass('show');
			}else{
				jQuery('.show_version').addClass('hide');
				jQuery('.show_version').removeClass('show');
			}
		},
		error: function(errorThrown){
			console.log(errorThrown);
		},
	});  
}
/**
 * CLink Publish Content and Make Version
 *
 * @param integer var post_id  Current Post ID
 */
function wpclink_publish_content_and_make_version(post_id){
	jQuery.ajax({
		url: ajaxurl, 
		data: {
			'action': 'wpclink_make_version_creation',
			'post_id' : post_id,
			'version' : "1"
		},
		success:function(data) {
			
			// Show version list
			jQuery('.all_versions').html(data);
			if(data.length > 0){
				jQuery('.show_version').removeClass('hide');
				jQuery('.show_version').addClass('show');
				jQuery('.cl-loading-bar.registry').slideUp();
				wpclink_clink_action_btn("enable");
			}
		},
		error: function(errorThrown){
			console.log(errorThrown);
		},
	});  
}
/**
 * CLink Get Pre Authorized Date
 *
 */
function wpclink_get_pre_authorized(){
		
	// Post ID
	var post_id_value = jQuery("#post_ID").val();
	jQuery.ajax({
		url: ajaxurl, 
		data: {
			'action': 'wpclink_get_pre_authorized_date_ajax_request',
			'post_id' : post_id_value
		},
		success:function(data) {	
			// Show DAte
			jQuery('.license-date-slot').html('<strong>Date: <span class="icon-box" title="Pre-Authorized Date"></span> </strong> '+data); 
		},
		error: function(errorThrown){
			console.log(errorThrown);
		},
	});  		
}
/**
 * CLink Get License Class by ajax request
 *
 */
function wpclink_get_license_class(){
	var post_id_value = jQuery("#post_ID").val();
	jQuery.ajax({
		url: ajaxurl, 
		data: {
			'action': 'wpclink_get_license_class',
			'post_id' : post_id_value
		},
		success:function(data) {
			
			// Show License Class
			jQuery('.license-slot').html(data);
		},
		error: function(errorThrown){
			console.log(errorThrown);
		},
	});  
}
/**
 * CLink Print Versions
 *
 */
function wpclink_print_version(){
		// CLink ID Versions
		var version_clinkid;
	
		// Post ID
		var post_id_value = jQuery("#post_ID").val();
		version_clinkid = '<span class="clinkid"><a href="<?php echo WPCLINK_ID_URL; ?>/#objects/<?php echo $clink_id; ?>" target="_blank"><?php echo wpclink_do_icon_clink_ID($clink_id); ?></a></span>';
		
	
		<?php
		 if($iscc_status = get_post_meta($post_id,'wpclink_iscc_status',true)){
			 $iscc_label = 'Update';
		 }else{
			 $iscc_label = 'Add'; 
		 }
		 
		 if($clink_versions = get_post_meta( $post_id, 'wpclink_versions', true )){
			
			  $show_version_button = 'show';
			 
		 }else{
			 $show_version_button = 'hide';
		}
		 
		 
		 $generate_archive_html = apply_filters('wpclink_clink_panel_button_section_show','',$post_id);
		 
		 $clink_panel_version_2 = '// Make Version
		jQuery(".make_version").click(function(){
			
			
			// Dialoge Confirmation 
			jQuery( "#dialog-confirm" ).dialog({
			  resizable: false,
			  height: "auto",
			  dialogClass: "clink-dialog",
			  width: 400,
			  modal: true,
			  open: function(event) {
				 jQuery(".ui-dialog-buttonpane").find("button:contains(\"Continue\")").addClass("button-primary");
			 },
			  buttons: {
				"Continue": function() {
					jQuery("#cl_version").attr("value","1");
					var post_id_value = jQuery("#post_ID").val();
					// Save License Version
					jQuery.ajax({
						url: ajaxurl, 
						data: {
							"action": "wpclink_post_version_save_ajax",
							"post_id" : post_id_value,
							"version" : "1"
						},
						success:function(data) {
							console.log(data);
							var publish_button_status = jQuery(".editor-post-publish-button").attr("aria-disabled");
							
								
							
							if(publish_button_status == "true"){
								wpclink_smart_loader("registry","Making a version...");
								wpclink_publish_content_and_make_version(post_id_value);
							}else{
								wpclink_smart_loader("registry","Making a version...");
								jQuery(".editor-post-publish-button").trigger("click");
								
								
								setTimeout(function(){
									wpclink_get_versions_ajax(post_id_value); 
									jQuery(".cl-loading-bar.registry").slideUp();
								}, 3000);
								
							}
						},
						error: function(errorThrown){
							console.log(errorThrown);
						},
					}); 
					jQuery( this ).dialog( "close" );
				},
				"Cancel": function() {
					jQuery("#cl_version").attr("value","0");
					jQuery( this ).dialog( "close" );
				}
			  }
			});
			
		});';
		 
		 $clink_panel_version_2 = apply_filters('wpclink_clink_panel_version_action_2',$clink_panel_version_2);
		 
		 $current_user_id = get_current_user_id();
		 
		 if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator')){
			 $make_version = 'make_version';
			 $disabled = '';
			 $iscc_generate = 'onclick=\"wpclink_generate_iscc()\"';
		 }else{
			 $make_version = '';
			 $disabled = 'disabled=\"disabled\"';
			 $iscc_generate = '';
		 }
		 
		 if($iscc_status = get_post_meta($post_id,'wpclink_iscc_status',true)){
			 $iscc_label = 'Update';
		 }else{
			 $iscc_label = 'Add'; 
		 }
		 
		 ?>
		
		// Version Show
		jQuery( ".version-slot" ).html( "<div class=\"clink_post_id\"><span class=\"clinkico\"></span> <span class=\"version_label\">Versions</span><a  <?php echo $disabled; ?> class=\"button cl-small <?php echo $make_version; ?> \">Add</a> <a class=\"cl-small show_version <?php echo $show_version_button; ?> \"><span class=\"dashicons dashicons-plus\"></span> See more</a><div class=\"all_versions\" style=\"display:none;\"></div><div class=\"small-label category iscc_wrap\"><span class=\"iscc_label\">ISCC</span><a <?php echo $disabled; ?> class=\"button cl-small generate_iscc components-button iscc-08 \" <?php echo $iscc_generate; ?>><?php echo $iscc_label; ?></a></div><?php echo $generate_archive_html; ?></div><input type=\"hidden\" id=\"cl_version\" name=\"cl_version\" value=\"0\" /> " );
		
		// Version by Ajax Request
		wpclink_get_versions_ajax(post_id_value);
	
		// Toggle
		jQuery(".show_version").click(function(){ 
			
		var see_version_btn = jQuery(this);
			
		jQuery(".all_versions").slideToggle("fast",function() {
			
		if(jQuery(".all_versions").css("display") == "none"){
	
			see_version_btn.contents().last().replaceWith(" See more");
				see_version_btn.find(".dashicons").removeClass("dashicons-minus");
				see_version_btn.find(".dashicons").addClass("dashicons-plus");
				
		
		}else{
			
	
				see_version_btn.contents().last().replaceWith(" See less");
				see_version_btn.find(".dashicons").removeClass("dashicons-plus");
				see_version_btn.find(".dashicons").addClass("dashicons-minus");
				
		
			
		}
			
  		});
		
		
		
		
		
		});
	
	
		jQuery.ajax({
						url: ajaxurl, 
						data: {
							"action": "wpclink_post_show_right_holder_ajax",
							"post_id" : post_id_value,
							"show" : "1"
						},
						success:function(data) {
							
							if(data !="0"){
								jQuery('.wpclink_display_right_holder_box').html(data);
							}	
							
						},
						error: function(errorThrown){
							console.log(errorThrown);
						},
					}); 
		
	
	<?php echo $clink_panel_version_2; ?>
}
function wpclink_smart_loader(section = 'registry', message = ''){
	if(section == "registry"){
		jQuery(".cl-loading-bar.registry").slideDown();
		jQuery(".cl-loading-bar.registry .cl_loader_status_mini").text(message);
	}else if(section == "license"){
		jQuery(".cl-loading-bar.license").slideDown();
		jQuery(".cl-loading-bar.license .cl_loader_status_mini").text(message);
	}else if(section == "links"){
		
	}
}
/**
 * CLink Error Toggle
 *
 */
function wpclink_error_details(){
	 jQuery(".cl_error_msg").slideToggle("fast");
}
/**
 * CLink Error Dialog Box
 *
 */	
function wpclink_error_dialoge_box(get_response, popup_type) {
    var response;
    if (typeof get_response === 'object' && get_response !== null) {
        response = get_response;
    } else {
        response = jQuery.parseJSON(get_response);
    }
    // dialog-confirm
    if (response.complete == 'failed') {
        if (popup_type == 'post_editor_license') {
            jQuery('.components-notice-list  .components-notice.is-error').hide();
            (function(wp) {
                wp.data.dispatch('core/notices').createNotice(
                    'error', // Can be one of: success, info, warning, error.
                    response.error_headline + '. "' + response.code + ' ' + response.message + '". ' + response.error_text.replace(" <br>", ""), // Text string to display.
                    {
                        isDismissible: false, // Whether the user can dismiss the notice.
                        // Any actions the user can perform.
                    }
                );
            })(window.wp);
            jQuery("#cl-license-selection_step2").hide();
        } else if (popup_type == 'post_editor') {
            jQuery('.components-notice-list  .components-notice.is-error').hide();
            (function(wp) {
                wp.data.dispatch('core/notices').createNotice(
                    'error', // Can be one of: success, info, warning, error.
                    response.error_headline + '. "' + response.code + ' ' + response.message + '". ' + response.error_text.replace(" <br>", ""), // Text string to display.
                    {
                        isDismissible: false, // Whether the user can dismiss the notice.
                        // Any actions the user can perform.
                    }
                );
            })(window.wp);
        } else if (popup_type == 'new_popup') {
            jQuery("#cl_request_error").dialog({
                resizable: false,
                height: "auto",
                title: response.error_headline,
                width: 400,
                modal: true,
                open: function(event) {
                    jQuery(".ui-dialog-buttonpane").find("button:contains(\"Try Again\")").addClass("button-primary");
                    jQuery('.error-message-request').html('<p>' + response.error_text + '</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">' + response.code + ' : ' + response.message + ' </p></div>');
                },
                buttons: {
                    "Try Again": function() {
                        wpclink_generate_iscc();
                        jQuery(this).dialog("close");
                        return false;
                    }
                }
            });
        } else if (popup_type == 'media_popup') {
      
            jQuery("#cl_request_error").dialog({
                resizable: false,
                height: "auto",
                title: response.error_headline,
                width: 400,
                modal: true,
                open: function(event) {
                    jQuery("#dialog-metadata-warning").dialog('close');
                    jQuery(".ui-dialog-titlebar").show();
                    jQuery(".ui-dialog-titlebar-close").show();
                    jQuery(".ui-dialog-buttonpane").find("button:contains(\"Close\")").addClass("button-primary");
                    jQuery('.error-message-request').html('<p>' + response.error_text + '</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">' + response.code + ' : ' + response.message + ' </p></div>');
					wpclink_hide_background_status();
                },
                buttons: {
                    "Close": function() {
                        jQuery(this).dialog("close");
						wpclink_hide_background_status();
						wpclink_clink_action_btn("enable");
                    }
                }
            });
            jQuery(".loading-circle-quick").hide(200);
        } else if (popup_type == 'license_popup') {
           
            jQuery("#cl_request_error").dialog({
                resizable: false,
                height: "auto",
                title: response.error_headline,
                width: 400,
                modal: true,
                open: function(event) {
                    jQuery(".ui-dialog-titlebar").show();
                    jQuery(".ui-dialog-titlebar-close").show();
                    jQuery(".ui-dialog-buttonpane").find("button:contains(\"Close\")").addClass("button-primary");
                    jQuery('.error-message-request').html('<p>' + response.error_text + '</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">' + response.code + ' : ' + response.message + ' </p></div>');
                },
                buttons: {
                    "Close": function() {
                        jQuery(this).dialog("close");
                    }
                }
            });
            jQuery(".loading-circle-quick").hide(200);
        } else if (popup_type == 'media_version') {
                 jQuery("#cl_request_error").dialog({
                resizable: false,
                height: "auto",
                title: response.error_headline,
                width: 400,
                modal: true,
                open: function(event) {
                    jQuery("#dialog-confirm").dialog('close');
                    jQuery(".ui-dialog-titlebar").show();
                    jQuery(".ui-dialog-titlebar-close").show();
                    jQuery(".ui-dialog-buttonpane").find("button:contains(\"Close\")").addClass("button-primary");
                    jQuery('.error-message-request').html('<p>' + response.error_text + '</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">' + response.code + ' : ' + response.message + ' </p></div>');
                    jQuery(".loading-circle-quick").hide(200);
                },
                buttons: {
                    "Close": function() {
                        jQuery(this).dialog("close");
                    }
                }
            });
        }
        return true;
    } else {
        return false;
    }
}
		
/**
 * Generate ISCC Hash Code
 *
 */	
function wpclink_generate_iscc(){
	
	// Post ID
	var post_id_value = jQuery("#post_ID").val();
	
					// Generate Ajax Request
					jQuery.ajax({
						url: ajaxurl, 
						data: {
							"action": "wpclink_generate_iscc_ajax",
							"post_id" : post_id_value,
							"update_iscc" : "1"
						},
						beforeSend: function(){
							// Disable
							wpclink_clink_action_btn("disable");
							
							if(jQuery('.generate_iscc').text() == 'Add'){
								wpclink_smart_loader("registry","Generating ISCC...");
								
							}else if(jQuery('.generate_iscc').text() == 'Update'){
								wpclink_smart_loader("registry","Updating ISCC...");
							}
							
							jQuery(".generate_iscc").addClass("disabled");
						},
						success:function(data) {
							
							
							
							if(wpclink_error_dialoge_box(data,'post_editor')){
								// Error
								jQuery('.generate_iscc').text('Error');
								// Close Loader
								jQuery('.cl-loading-bar.registry').slideUp();
							}else{
								jQuery('.generate_iscc').removeClass("disabled");
								// Close Loader
								jQuery('.cl-loading-bar.registry').slideUp();
								
							}
							// Enable
							wpclink_clink_action_btn("enable");
							
							jQuery(".generate_iscc").prop("disabled", false);
							
						},
						complete:function(data){
								jQuery('.generate_iscc').removeClass("disabled");
							
								// Now Update
								jQuery(".generate_iscc").text("Update");
							
								// Close Loader
								jQuery('.cl-loading-bar.registry').slideUp();
								// Enable
								wpclink_clink_action_btn("enable");
					    },
						error: function(errorThrown){
							console.log(errorThrown);
						},
					}); 	
}
var cl_load_panel = 1;	
	
function wpclink_toolbox_init(){
	
		
	jQuery('.editor-post-publish-button').click(function(){
		cl_load_panel = 0;
	});
	
	// Wait to load Sidebar
		setTimeout(function(){
			cl_load_panel = 1;
		},3000);
	
	wpclink_toolbox();
}
	
/**
 * CLink Sidebar Toolbox
 *
 * Show the CLink content properties in the registered sidebar.
 *
 */
function wpclink_toolbox(){
	
	
	console.log('wpclink_toolbox');
	
		// Wait to load Sidebar
		setTimeout(function(){
			
		if(cl_load_panel == 1){
				   
		// Preloader		
		jQuery(".toolbar-loading-ajax").hide();
		jQuery(".sidebar-val").show();
			
		<?php
		 
		 // Registry
		 include('compatibility/gutenberg/panel/sections/registry/registry.php');
		 
		 
		 // Notification
		 include('compatibility/gutenberg/panel/sections/notify/notify.php');
		 
		 ?>	
		
		// Make Version - Linked Site
		jQuery(".make_version").click(function(){
			
		jQuery( "#dialog-confirm" ).dialog({
			  resizable: false,
			  height: "auto",
			  dialogClass: "clink-dialog",
			  width: 400,
			  modal: true,
			 open: function(event) {
				 jQuery(".ui-dialog-buttonpane").find("button:contains(\"Continue\")").addClass("button-primary");
			 },
			  buttons: {
				"Continue": function() {
					jQuery("#cl_version").attr("value","1");
					
					wpclink_clink_action_btn("disable");
					
					var post_id_value = jQuery("#post_ID").val();
					jQuery.ajax({
					url: ajaxurl, 
					data: {
						"action": "wpclink_post_version_save_ajax",
						"post_id" : post_id_value,
						"version" : "1"
					},
					success:function(data) {
						console.log(data);
						
						var publish_button_status = jQuery(".editor-post-publish-button").attr("aria-disabled");
						
					
						
						if(publish_button_status == 'true'){
								wpclink_publish_content_and_make_version(post_id_value);
							wpclink_smart_loader("registry","Making a version...");
						}else{
							jQuery('.editor-post-publish-button').trigger("click");
							wpclink_smart_loader("registry","Making a version...");
							setTimeout(function(){
									wpclink_get_versions_ajax(post_id_value);
									jQuery(".cl-loading-bar.registry").slideUp();
								}, 3000);
						}
					},
					error: function(errorThrown){
						console.log(errorThrown);
					},
				}); 
				jQuery( this ).dialog( "close" );
				},
				"Cancel": function() {
					jQuery("#cl_version").attr("value","0");
					jQuery( this ).dialog( "close" );
				}
			  }
			});
		});
			
			jQuery(".show_version").click(function(){ 
		var see_version_btn = jQuery(this);
			jQuery(".all_versions").slideToggle("fast",function() {
			
		if(jQuery(".all_versions").css("display") == "none"){
		
			see_version_btn.contents().last().replaceWith(" See more");
				see_version_btn.find(".dashicons").removeClass("dashicons-minus");
				see_version_btn.find(".dashicons").addClass("dashicons-plus");
			
		}else{
		
				see_version_btn.contents().last().replaceWith(" See less");
				see_version_btn.find(".dashicons").removeClass("dashicons-plus");
				see_version_btn.find(".dashicons").addClass("dashicons-minus");
				
			
		}
    			
  		});
		});
		
		
		
		<?php 
		 
		 //  License
		 include('compatibility/gutenberg/panel/sections/license/license.php');
		
		 //  Links
		 include('compatibility/gutenberg/panel/sections/links/links.php');
		 
		 ?>
		
		// License Class Change 
		jQuery(".select-license").click(function(){
			
			//  Post ID
			var get_referent_id = jQuery("#post_ID").val();
			var get_taxonomy_permission = jQuery('#quick-license-type').val();
			
			// License Class
			var get_license_class = jQuery(this).val();
			jQuery.ajax({
				cache: false,
				url: ajaxurl,
				data: {
					'action': 'wpclink_make_ref_post_step2',
					'license_class' : get_license_class,
					'referent_id' : get_referent_id,
				},
				success:function(data) {
				
				// License Screen Show
				jQuery.ajax({
					cache: false,
					url: ajaxurl, 
					data: {
						'action': 'wpclink_do_popup_license_template',
						'referent_id' : get_referent_id,
						'license_class' : get_license_class
					},
					success:function(data) {
						jQuery('body').append(data);
						jQuery( "#cl-license-show-screen" ).dialog({maxWidth:"auto",maxHeight: 500,width: 800,height:500,fluid: true,closeOnEscape: false,close: function(event, ui){ jQuery(this).dialog('close');  jQuery(this).remove();} });
						// Scroll
						jQuery("#cl-license-show-screen").scrollTop(0);
						jQuery('#cl-license-selection').hide();
						// Validate
						if(data == "0"){
							jQuery('#cl-license-selection_step2').show();
						}else{
							jQuery('#cl-license-selection_step2').hide();
						}
						
						// Accept Button
						jQuery("#accept_license_check").change(function() {
						if(this.checked) {
							jQuery( "#license_next" ).prop( "disabled", false );
						}else{
							jQuery( "#license_next" ).prop( "disabled", true );
						}
						});
						jQuery("#license_next").click(function(){
							jQuery(this).parents(".ui-dialog-content").dialog('close');
							jQuery("#cl-license-selection_step2").show(200);
						});
				}});
						jQuery('#cl-license-selection_step2 .close-pbox').click(function(){
							jQuery('#cl-license-selection_step2').hide(200);
						});
						// This outputs the result of the ajax request
						jQuery('#license_show_box').html(data);
						jQuery('#license_class_s2').val(get_license_class);
						jQuery('#taxonomy_permission_slot').val(get_taxonomy_permission);
					
					
						
					
						jQuery('#referent_id_s2').val(get_referent_id);
						jQuery('.cl_show_license').click(function(){
							jQuery('#cl_show_pre_auth').hide();
							jQuery('#cl_show_current_license').show();
						});
						jQuery('.back_to_pre').click(function(){
							jQuery('#cl_show_pre_auth').show();
							jQuery('#cl_show_current_license').hide();
						});
						jQuery('#cl_show_esign_pre .close-pbox').click(function(){
							jQuery('#cl_show_esign_pre').hide(200);
						});
						jQuery('#cl_show_pre_auth .close-pbox').click(function(){
							jQuery('#cl_show_pre_auth').hide(200);
						});
						// Pre Authorize Checkbox Time Update
						jQuery(".pre_auth_check").change(function() {
							if(this.checked) {
								jQuery.ajax({
								url: ajaxurl,
								data: {
									'action': 'wpclink_pre_auth_accept_ajax',
									'cl_action' : 'add',
									'referent_id' : get_referent_id,
								},
								success:function(data) {
											jQuery( ".make-ref-btn_s2" ).prop( "disabled", false );
								}});
							}else{
								jQuery.ajax({
								url: ajaxurl,
								data: {
									'action': 'wpclink_pre_auth_accept_ajax',
									'cl_action' : 'remove',
									'referent_id' : get_referent_id,
								},
								success:function(data) {
									jQuery( ".make-ref-btn_s2" ).prop( "disabled", true );
								}});
							}
						});
					},
					error: function(errorThrown){
						console.log(errorThrown);
					},
				});
			});
	
		// Show License Button
		jQuery('.show-lc-btn').click(function(){
			jQuery( "#cl-license-show" ).dialog({maxWidth:600,maxHeight: 500,width: 600,height:500});
			jQuery('#cl-license-show-box').show();
			
		});
			
			// Make Referent Button
		jQuery(".make-ref-btn_s2").click(function(){
			
				 post_id_value = jQuery("#post_ID").val();
				 license_class = jQuery('#license_class_s2').val();
				 get_taxonomy_permission = jQuery('#taxonomy_permission_slot').val();
				
				
				
					
					jQuery.ajax({
						url: ajaxurl,
						data: {
							"action": "wpclink_save_license_class_post_editor",
				            "post_id" : post_id_value,
							"license_class" : license_class,
							"taxonomy_permission" : get_taxonomy_permission,
							"license_version": "0.9"
							
						},
						 beforeSend: function() {
							wpclink_smart_loader("license","Applying license...");
						},
						success:function(data) {
							var datajson = jQuery.parseJSON(data);
							if(datajson.complete == 'failed'){
								wpclink_error_dialoge_box(data,"post_editor_license");
								return false;
							}
							
							
							
							// Here
							jQuery(".license-slot").addClass('applied-license');
							jQuery(".license-slot").html(datajson.data);
							jQuery(".license-version-slot").html("<strong>Version:</strong> 0.9");
							wpclink_get_pre_authorized();
							
							// Close Loader
							jQuery(".cl-loading-bar.license").slideUp();
							
							jQuery('#cl-license-selection_step2').hide();
							
        				},
        				error: function(errorThrown){
            				console.log(errorThrown);
        				}
					});  
			});
			
			// Popup 1 Show
			jQuery("#cl_pop1").click(function(){
				jQuery("#cl_show_pre_auth").show();
				jQuery( "#cl_show_pre_auth" ).dialog({
					maxWidth:"auto",maxHeight: 500,width: 800,height:500,fluid: true,closeOnEscape: false,close: function(event, ui){ 
						jQuery(this).dialog('close');
						jQuery(this).hide();
					} 
				});
				
			});
			// Popup 2 Show
			jQuery("#cl_pop2").click(function(){
				jQuery("#cl_show_esign_pre").show();
				jQuery( "#cl_show_esign_pre" ).dialog({maxWidth:"auto",maxHeight: 500,width: 800,height:500,fluid: true,closeOnEscape: false,close: function(event, ui){ jQuery(this).dialog('close');  jQuery(this).hide();} });
				
			});
					
			
		jQuery(document).ready(function($) {
			//alert(1);
			
			// Start Accordion
			
								
jQuery(".accordion.registry-box").accordion({
	active:parseInt(wpclink_getCookie('wpclink_registry_tab')),
	collapsible: true,
	heightStyle: "content",
	animate: false,
	activate: function (event, ui) {
		if(jQuery(this).accordion('option', 'active') === 0){
			wpclink_setCookie('wpclink_registry_tab', 0,365);
		}else if(jQuery(this).accordion('option', 'active') === false){
			wpclink_setCookie('wpclink_registry_tab', 1,365);	 
		}
	}
});
			
jQuery(".accordion.license-box").accordion({
	active:parseInt(wpclink_getCookie('wpclink_license_tab')),
	collapsible: true,
	heightStyle: "content",
	animate: false,
	activate: function (event, ui) {
		if(jQuery(this).accordion('option', 'active') === 0){
			wpclink_setCookie('wpclink_license_tab', 0,365);
		}else if(jQuery(this).accordion('option', 'active') === false){
			wpclink_setCookie('wpclink_license_tab', 1,365);	 
		}
	}
});
jQuery(".accordion.links-box").accordion({
	active:parseInt(wpclink_getCookie('wpclink_links_tab')),
	collapsible: true,
	heightStyle: "content",
	animate: false,
	activate: function (event, ui) {
		if(jQuery(this).accordion('option', 'active') === 0){
			wpclink_setCookie('wpclink_links_tab', 0,365);
		}else if(jQuery(this).accordion('option', 'active') === false){
			wpclink_setCookie('wpclink_links_tab', 1,365);	 
		}
	}
});
			
			jQuery('.external-link').click(function(){
				window.open('<?php echo $external_hyperlink; ?>','_blank');
			});
		});
	
		// Tooltip
		jQuery(document).tooltip();
if(jQuery('.interface-pinned-items .components-button[aria-label="CLink"]').hasClass( "is-pressed" )){
	   jQuery('.set-registration').hide();
}
		}
	 }, 500);		
}
	
	function cl_present_popup(){
		
		var license_type_position = jQuery(".plugin-sidebar-content-type").offset();
		
		jQuery('#cl_present').fadeIn('fast');
		
		jQuery('#cl_present').css('top',license_type_position.top+100+'px');
		jQuery('#cl_present').css('margin-top','0');
		
		jQuery('.present .close-pbox').click(function(){
			jQuery('#cl_present').fadeOut('fast');
		});
	
		jQuery( "#cl_present" ).draggable({ handle: ".dashicons-move" });
	}
	
var editor_changed, set_editor_clink;
	
function wpclink_check_editor_is_active(editor_changed){
	
	if(jQuery('.edit-post-visual-editor').length > 0){
		
		set_editor_clink = 'visual';
		
		
	} else {
		
		
		set_editor_clink = 'code';
		
	}
	
	if(set_editor_clink != editor_changed){
		console.log('nowchanged');
		
			
		
		
    	var newContentState;
	  
	 	newContentState = wp.data.select( 'core/editor' ).getEditedPostContent();
	  
			
			// Image Tag
			var regex = /<img.*?src="(.*?)"/gi, result, indices = [];
			
			// Post ID
			var post_id_value = jQuery("#post_ID").val();
			
			// Build Array
			while ((result = regex.exec(newContentState))) {
				indices.push(result[1]);
			}
			
			//var data_to_send = jQuery.serialize(indices);
			
			var jsonString = JSON.stringify(indices);
			
			if (indices === undefined || indices.length == 0) {
				// Not array
				
					cl_license_button_clock = 1;
					jQuery('.components-notice-list  .components-notice.is-warning').hide();
					
					jQuery(".license-slot").css('pointer-events','all');
					jQuery(".license-slot").css('opacity','1');
			
					
				
			}else{
			
				jQuery.ajax({
							url: ajaxurl, 
							data: {
								"action": "wpclink_check_content_license_ajax",
								"images" : jsonString,
								"post_id" : post_id_value,
								"cl_action" : "check"
							},
							beforeSend: function(){
							},
							success:function(response) {
								response_json = jQuery.parseJSON(response);
								
								if(response_json.status == "found"){
									
									jQuery('.components-notice-list  .components-notice.is-warning').hide();					
									
									
										var post_type = jQuery('#post_type').val();
										if(post_type == 'post'){
											notice_label = 'Post';
										}else if(post_type == 'page'){
											notice_label = 'Page';	 
										}else{
											notice_label = 'Post';	 
										}
				
								( function( wp ) {
									wp.data.dispatch('core/notices').createNotice(
										'warning', 
										 notice_label+' contains at least one licensed image. '+notice_label+' cannot be licensed because you have no permission to do that from the licensor of the image. The licensed image(s) is/are shown with colored dashed border.', 
										{
											isDismissible: false, 
										}
									);
								} )( window.wp );
									
									
									var target_src;
									
									for (var image_count in response_json.data.attachments) {
										target_src = response_json.data.attachments[image_count]['src'];
										permissions = response_json.data.attachments[image_count]['permissions'];
										
										//alert(permissions);
										
										if(permissions === undefined || permissions.length == 0){
											// Nothing
										}else{
											if(permissions.includes("ModifyDescription")){
												// has Caption permission
											}else{
												// not
												//alert("img[src='"+target_src+"']");
												jQuery( "img[src='"+target_src+"']" ).parent().parent().find('figcaption').hide();
											}
										}
										
										//.parent().parent().find('figcaption').hide();
										
										jQuery( "img[src='"+target_src+"']" ).css('outline','5px dashed #ee7700');
										jQuery( "img[src='"+target_src+"']" ).addClass('cl-linked-img');
									}
									
									// Pointing
									jQuery( "img[src='"+response_json.data.attachment_src+"']" ).css('outline','5px dashed #ee7700');
								
								cl_license_button_clock = 0;
								refreshData(); // execute function 
				
									
								}else if(response_json.status == "notfound"){
									jQuery('.components-notice-list  .components-notice.is-warning').hide();
									
									cl_license_button_clock = 1;
									
								
									jQuery(".license-slot").css('pointer-events','all');
									jQuery(".license-slot").css('opacity','1');
								}
								
							},
							complete:function(response){
								
							},
							error: function(errorThrown){
								console.log(errorThrown);
							},
						}); 
				console.log(indices);
				
			}
        
  
		
	}
	
	editor_changed = set_editor_clink;
	
	setTimeout(function(){
		wpclink_check_editor_is_active(editor_changed);
	},3000);
}
	
	
		if(jQuery('.edit-post-visual-editor').length > 0){
			wpclink_check_editor_is_active('visual');
		} else {
			wpclink_check_editor_is_active('code');
		}
	
	
</script>
<?php	
	 }
}
add_action('admin_footer', 'wpclink_display_clink_panel');
