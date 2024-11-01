<?php
/**
 * CLink Registry Section
 *
 * CLink panel registry section 
 *
 * @package CLink
 * @subpackage Link Manager
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$current_screen = get_current_screen();
$current_user_id = get_current_user_id();
// Global Post IO
if(isset($_GET['post'])){
    $post_id = $_GET['post'];
}else{
    $post_id = '';
}
        $script_registry = '<div class="accordion registry-box"><h3>Creation</h3><div id="cl-registry-tab"><div class="clinkid-slot"><span class="clinksidebar"></span></div><div class="status-slot"></div><div class="version-slot"></div><span class="cl-loading-bar registry"><div class=\"loader_spinner\"></div><div class=\"cl_loader_status_mini\">Updating...</div></span></div></div>';
                
        // Print CLink ID
        echo "jQuery( '.plugin-sidebar-content-identifier' ).html('".$script_registry."');";
	 	$clink_id = get_post_meta($post_id,'wpclink_creation_ID',true);
		 
			// CLink Versions
			echo "var version_clinkid; ";
		 
			if(!empty($clink_id)){
				$version_clink_id = '<span class=\"clinkid\"><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$clink_id.'\" target=\"_blank\">'.wpclink_do_icon_clink_ID($clink_id).'</a></span>';
				echo "version_clinkid = '".$version_clink_id."'; ";
			}else{
				echo "version_clinkid = '';";
			}
		 
		 if($sync_origin = get_post_meta( $post_id, 'wpclink_referent_post_uri', true )){
			 
			 //  Linked Creation
		 	include('linked/creation.php');
		 }else if(wpclink_check_license_by_post_id($post_id) > 0){ 
		 
		 	 //  Referent Creation
		 	include('referent/creation.php');
			 
		 
		 }else{
			 // Register
	
			
		// Print Verions
		if(!empty($clink_id)){
			// Hook
			$archive_section = apply_filters('wpclink_clink_panel_button_section','',$post_id);
			
			
		if($iscc_status = get_post_meta($post_id,'wpclink_iscc_status',true)){
			 $iscc_label = 'Update';
		 }else{
			 $iscc_label = 'Add'; 
		 }
		 
		  if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator') ){
			 $make_version = 'make_version';
			 $disabled = '';
			 $iscc_generate = 'onclick=\"wpclink_generate_iscc()\"';
		 }else{
			 $make_version = '';
			 $disabled = 'disabled=\"disabled\"';
			 $iscc_generate = '';
		 }
			echo 'jQuery( ".version-slot" ).html( "<div class=\"clink_post_id\"><span class=\"clinkico\"></span> <span class=\"version_label\">Versions</span><a '.$disabled.' class=\"button cl-small '.$make_version.' \">Add</a> <a class=\"cl-small show_version '.$show_version_button.' \"><span class=\"dashicons dashicons-plus\"></span> See more</a><div class=\"all_versions\" style=\"display:none;\">'.$html.'</div><div class=\"small-label category iscc_wrap\"><span class=\"iscc_label\">ISCC</span><a '.$disabled.' class=\"button cl-small generate_iscc components-button iscc-01 \" '.$iscc_generate.'>'.$iscc_label.'</a></div></div>'.$archive_section.'<input type=\"hidden\" id=\"cl_version\" name=\"cl_version\" value=\"0\" /> " );';
		}
	
		// Get Versiion by Ajax Request
		echo "wpclink_get_versions_ajax('".$post_id."');";
	
		// Get Clink ID
		echo 'wpclink_get_clinkid_ajax();';
			
		
		echo 'wpclink_get_post_status_editor();';
			
		// Get Registry Disabled?
		echo 'wpclink_get_wpclink_post_status_is_disabled();';
			 
		
		 ?>
		
	 
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
	
	<?php 
			 
// Author ID
$author_id = get_post_field ('post_author', $post_id);
			 
// RULE: # 1 CREATOR
if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator')){
	
// Show Register Post Option		 
$content_register_restrict = get_post_meta($post_id,'wpclink_post_register_status',true);
if($content_register_restrict == false) $content_register_restrict = 0;
			 
if($content_register_restrict == '1'){
	$restrict_status = 'Disabled';
}else{
	$restrict_status = 'Enabled';	
}
			 
	// Print Verions
	if(!empty($clink_id)){
		
	$scripts= '';
	$scripts.= ' jQuery(".status-slot").html("<div class=\" clink_quota_id\"><span class=\"clinkico\"></span><span class=\"components-checkbox-control__input-container\"><input id=\"cl_register_process\" name=\"cl_register_process\" class=\"components-checkbox-control__input\" value=\"0\" type=\"checkbox\" '.checked( $content_register_restrict, 0, false ).' /><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"-2 -2 24 24\" width=\"24\" height=\"24\" role=\"img\" class=\"components-checkbox-control__checked\" aria-hidden=\"true\" focusable=\"false\"><path d=\"M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2\"></path></svg></span><label>Allow registration</label></div>" );
	
	if(typeof wpclink_allow_register !== "undefined")
	{
		var wpclink_allowed_check = '.$content_register_restrict.';
		if(wpclink_allowed_check == wpclink_allow_register){
		}else{
			if(wpclink_allow_register == 0){
				jQuery("#cl_register_process").prop("checked", true);
			}else{
				jQuery("#cl_register_process").prop("checked", false);
			}
		}
	
	}
	
		 var post_id_value;
		jQuery("#cl_register_process").change(function(){
			 post_id_value = jQuery("#post_ID").val();
			 if(jQuery(this).is(":checked")) {
			 	wpclink_allow_register = 0;
				jQuery(".res_status").text("Enabled");
				jQuery(".non-editable").hide();
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
			 	wpclink_allow_register = 1;
				jQuery(".res_status").text("Disabled");
				jQuery(".non-editable").show();
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
		});';
		
	echo $scripts;
		
	}else{
	
		// Need to Update
		
	if(wpclink_user_has_creator_list($current_user_id)){
		
		// PUBLISH + AUTHOR
		if ( get_post_status ( $post_id ) == 'publish' and $author_id == $current_user_id ) {
			
			$scripts= '';
	$scripts.= ' jQuery(".status-slot").html("<div class=\" clink_quota_id\"><span class=\"clinkico\"></span><span class=\"components-checkbox-control__input-container\"><input id=\"cl_register_process\" name=\"cl_register_process\" class=\"components-checkbox-control__input\" value=\"0\" type=\"checkbox\" '.checked( $content_register_restrict, 0, false ).' /><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"-2 -2 24 24\" width=\"24\" height=\"24\" role=\"img\" class=\"components-checkbox-control__checked\" aria-hidden=\"true\" focusable=\"false\"><path d=\"M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2\"></path></svg></span><label>Allow registration</label></div>" );
	
	if(typeof wpclink_allow_register !== "undefined")
	{
		var wpclink_allowed_check = '.$content_register_restrict.';
		if(wpclink_allowed_check == wpclink_allow_register){
		}else{
			if(wpclink_allow_register == 0){
				jQuery("#cl_register_process").prop("checked", true);
			}else{
				jQuery("#cl_register_process").prop("checked", false);
			}
		}
	
	}
	
		 var post_id_value;
		jQuery("#cl_register_process").change(function(){
			 post_id_value = jQuery("#post_ID").val();
			 if(jQuery(this).is(":checked")) {
			 	wpclink_allow_register = 0;
				jQuery(".res_status").text("Enabled");
				jQuery(".non-editable").hide();
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
			 	wpclink_allow_register = 1;
				jQuery(".res_status").text("Disabled");
				jQuery(".non-editable").show();
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
		});';
		
	echo $scripts;
		
		// PUBLISH + NOT AUTHOR
		}else if(get_post_status ( $post_id ) == 'publish' and $author_id != $current_user_id){
			
		
	
			// Hook
			$message_text = apply_filters('wpclink_clink_panel_button_section',$message_text,$post_id);

			$message= '<span class=\"components-checkbox-control__input-container\"><input id=\"cl_register_process\" name=\"cl_register_process\" class=\"components-checkbox-control__input disabled\" value=\"0\" type=\"checkbox\" disabled=\"disabled\" /><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"-2 -2 24 24\" width=\"24\" height=\"24\" role=\"img\" class=\"components-checkbox-control__checked\" aria-hidden=\"true\" focusable=\"false\"><path d=\"M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2\"></path></svg></span><label class=\"disabled\">Allow registration</label></span>';

			$scripts.= ' jQuery(".status-slot").html("<div class=\" clink_quota_id\"><p>'.$message.'</p></div>" );';
			
			echo $scripts;
			
		
		// NOT PUBLISH
		}else{
			
			
			
			$scripts= '';
	$scripts.= ' jQuery(".status-slot").html("<div class=\" clink_quota_id\"><span class=\"clinkico\"></span><span class=\"components-checkbox-control__input-container\"><input id=\"cl_register_process\" name=\"cl_register_process\" class=\"components-checkbox-control__input\" value=\"0\" type=\"checkbox\" '.checked( $content_register_restrict, 0, false ).' /><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"-2 -2 24 24\" width=\"24\" height=\"24\" role=\"img\" class=\"components-checkbox-control__checked\" aria-hidden=\"true\" focusable=\"false\"><path d=\"M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2\"></path></svg></span><label>Allow registration</label></div>" );
	
	if(typeof wpclink_allow_register !== "undefined")
	{
		var wpclink_allowed_check = '.$content_register_restrict.';
		if(wpclink_allowed_check == wpclink_allow_register){
		}else{
			if(wpclink_allow_register == 0){
				jQuery("#cl_register_process").prop("checked", true);
			}else{
				jQuery("#cl_register_process").prop("checked", false);
			}
		}
	
	}
	
		 var post_id_value;
		jQuery("#cl_register_process").change(function(){
			 post_id_value = jQuery("#post_ID").val();
			 if(jQuery(this).is(":checked")) {
			 	wpclink_allow_register = 0;
				jQuery(".res_status").text("Enabled");
				jQuery(".non-editable").hide();
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
			 	wpclink_allow_register = 1;
				jQuery(".res_status").text("Disabled");
				jQuery(".non-editable").show();
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
		});';
		
	echo $scripts;
		
		// PUBLISH + NOT AUTHOR
		
			
		}
		
	
		
	
		
	}else{
		
	$message_text = 'You are not the Creator';
	
	// Hook
	$message_text = apply_filters('wpclink_clink_panel_button_section',$message_text,$post_id);
	
	$message= '<span class=\"components-checkbox-control__input-container\"><input id=\"cl_register_process\" name=\"cl_register_process\" class=\"components-checkbox-control__input disabled\" value=\"0\" type=\"checkbox\" disabled=\"disabled\" /><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"-2 -2 24 24\" width=\"24\" height=\"24\" role=\"img\" class=\"components-checkbox-control__checked\" aria-hidden=\"true\" focusable=\"false\"><path d=\"M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2\"></path></svg></span><label class=\"disabled\">Allow registration</label> <span class=\"non_creator\">'.$message_text.' <br><a href=\"#\">Learn about Creators <span class=\"dashicons dashicons-external\"></span></a></span>';
	
	$scripts.= ' jQuery(".status-slot").html("<div class=\" clink_quota_id\"><p>'.$message.'</p></div>" );';
	
	echo $scripts;
		
	}
		
	}
}else{
	
	$message_text = 'You are not the Creator';
	
	// Hook
	$message_text = apply_filters('wpclink_clink_panel_button_section',$message_text,$post_id);
	
	$message= '<span class=\"components-checkbox-control__input-container\"><input id=\"cl_register_process\" name=\"cl_register_process\" class=\"components-checkbox-control__input disabled\" value=\"0\" type=\"checkbox\" disabled=\"disabled\" /><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"-2 -2 24 24\" width=\"24\" height=\"24\" role=\"img\" class=\"components-checkbox-control__checked\" aria-hidden=\"true\" focusable=\"false\"><path d=\"M15.3 5.3l-6.8 6.8-2.8-2.8-1.4 1.4 4.2 4.2 8.2-8.2\"></path></svg></span><label class=\"disabled\">Allow registration</label> <span class=\"non_creator\">'.$message_text.' <br><a href=\"#\">Learn about Creators <span class=\"dashicons dashicons-external\"></span></a></span>';
	
	$scripts.= ' jQuery(".status-slot").html("<div class=\" clink_quota_id\"><p>'.$message.'</p></div>" );';
	
echo $scripts;
	
}
			 
		 
		 }
			 
		 