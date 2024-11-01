/**
 * CLink UI Scripts
 *
 * CLink UI scripts to control
 *
 * @author TeamCLink
 */
// Bulk Action Value
var bulk_value;
jQuery( document ).ready(function() {
	
// Default
bulk_value = jQuery('#bulk-action-selector-top').val();
jQuery('#bulk-action').val(bulk_value);
// Bulk Action Top Section Event
jQuery('#bulk-action-selector-top').change(function(){
	bulk_value = jQuery(this).val();
	jQuery('#bulk-action').val(bulk_value);
});
// Bulk Action CLink Post Link
jQuery('#doaction_bulk').click(function(){
	jQuery( "#posts-list" ).submit();
});
});
// CLink Need Help Event
jQuery( document ).ready(function() {
	jQuery('.cl_support a').click(function(){
		  jQuery( "#help-panel" ).slideToggle( "fast", function() {
		
		  });
	});
});
// Version List
jQuery( document ).ready(function() {
	jQuery('.wpclink_versions_list').click(function(){
	jQuery('#cl_imatag_list').hide(200);
	jQuery('#cl_archive_list').hide(200);
	jQuery('.upload-php .column-date').addClass('expand');
			
	// Post ID
	var post_id_value = jQuery(this).data("post-id");
	var wpclink_version_list = jQuery(this).offset();
	jQuery('#cl_version_list').css('top',wpclink_version_list.top-34+'px');
	jQuery('#cl_version_list').css('left',wpclink_version_list.left-264+'px');
	jQuery('#cl_version_list').css('margin-top','0');
	jQuery('#cl_version_list').css('margin-left','0');
	jQuery('#cl_version_list').css('position','absolute');
	jQuery('#cl_version_list').css('height','auto');
	jQuery('#cl_version_list').css('overflow','hidden');
	jQuery('#cl_version_list').hide();
		
					// Generate Ajax Request
					jQuery.ajax({
						url: ajaxurl, 
						data: {
							"action": "wpclink_get_version_list_ajax",
							"post_id" : post_id_value,
							"action_show_list" : "1"
						},
						beforeSend: function(){
							jQuery('#cl_version_list .inner').html('<span class="spinner"></span>');
							jQuery('#cl_version_list').slideDown(200);
							
						},
						success:function(data) {
							jQuery('#cl_version_list .inner').html(data);
						},
						error: function(errorThrown){
							console.log(errorThrown);
						},
					});
		
					// On close
					jQuery('#cl_version_list .close-pbox').click(function(){
						jQuery('#cl_version_list').hide(200);
						
						jQuery('.upload-php .column-date').removeClass('expand');
					});
					jQuery( "#cl_version_list" ).draggable({ handle: ".dashicons-move" });
		
		
	});
});
/* CLink Button Actions */
function wpclink_clink_action_btn(action = 'disable'){
	
	// Buttons
	var buttons = ".make_version, .generate_iscc, .generate_archive, .make_imatag";
	
	// Action
	if(action == "disable") {
		jQuery( buttons ).addClass( "disabled" );
	} else if(action == "enable"){
		jQuery( buttons ).removeClass( "disabled" );
	}
	
	
}