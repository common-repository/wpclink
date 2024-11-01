<?php
/**
 * CLink Registry Linked
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

 if(!empty($clink_id)){
					// CLink ID
				 
				 
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

				?>
			jQuery( ".clinksidebar" ).html( '<?php echo '<span class=\"clinkid\"><a href=\"'.WPCLINK_ID_URL.'/#objects/'.$clink_id.'\" target=\"_blank\">'.wpclink_do_icon_clink_ID($clink_id).'</a></span>'; ?>' ); <?php
				
				 
				 
				 // Get Versiion by Ajax Request
				echo "wpclink_get_versions_ajax('".$post_id."');";
				 
				 
				 $post_taxonomy_permission = get_post_meta($post_id,'wpclink_programmatic_right_categories',true);
        
				if($post_taxonomy_permission == 'non-editable'){
					// Nothing
					$disabled_version = 'disabled=\"disabled\"';
					$version_button_class = 'disabled_version';
				}else{
					$version_button_class = 'make_version';
					$disabled_version = '';

				}
	 
	 			 if(wpclink_user_has_creator_list($current_user_id) || current_user_can('editor') || current_user_can('administrator')){
					 $make_version = 'make_version';
					 $disabled = '';
					 $iscc_generate = 'onclick=\"wpclink_generate_iscc()\"';
				 }else{
					 $make_version = '';
					 $disabled = 'disabled=\"disabled\"';
					 $iscc_generate = '';
				 }

				 
				 // Hook
			 	$archive_section = apply_filters('wpclink_clink_panel_button_section','',$post_id);
				
				echo 'jQuery( ".version-slot" ).html( "<div class=\"clink_post_id\"><span class=\"clinkico\"></span> <span class=\"version_label\">Versions</span><a '.$disabled.' class=\"button cl-small '.$version_button_class.' \">Add</a> <a class=\"cl-small show_version '.$show_version_button.' \"><span class=\"dashicons dashicons-plus\"></span> See more</a><div class=\"all_versions\" style=\"display:none;\">'.$html.'</div><div class=\"small-label category iscc_wrap\"><span class=\"iscc_label\">ISCC</span><a class=\"button cl-small generate_iscc iscc-02 components-button \" '.$disabled.' '.$iscc_generate.'>'.$iscc_label.'</a></div></div>'.$archive_section.'<input type=\"hidden\" id=\"cl_version\" name=\"cl_version\" value=\"0\" /> " );';
				 
				 
				if($post_taxonomy_permission == 'non-editable'){
					// Non Editable
				}else{
				 
				 // Show Register Post Option
				$content_register_restrict = get_post_meta($post_id,'wpclink_post_register_status',true);
				if($content_register_restrict == false) $content_register_restrict = 0;

				if($content_register_restrict == '1'){
					$restrict_status = 'Disabled';
				}else{
					$restrict_status = 'Enabled';	
				}

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
			 
				}
			 
			 }
			 