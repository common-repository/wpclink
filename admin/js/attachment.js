/**
 * CLink IPTC UI
 *
 * CLink IPT UI fields
 *
 * @author TeamCLink
 */
var cl_use_template_creator;
var cl_use_template_credit;
var cl_use_template_copynotice;
/**
 * CLink Admin Notice Core
 *
 */
function wpclink_admin_notice_core( msg, link_to = '', link_text = '', custom_class = 'custom-notice' ) {
 
    /* create notice div */
     
    var div = document.createElement( 'div' );
    div.classList.add( 'notice', 'notice-success', custom_class );
     
    /* create paragraph element to hold message */
     
    var p = document.createElement( 'p' );
     
    /* Add message text */
     
    p.appendChild( document.createTextNode( msg ) );
 
    // Optionally add a link here
	
	var a = document.createElement( 'a' );
	a.setAttribute( 'href', link_to );
	a.setAttribute( 'target', '_blank' );
	a.appendChild( document.createTextNode( link_text ) );
	/* spaces */
	p.appendChild( document.createTextNode( '\xa0' ) );
	p.appendChild( a );
    /* Add the whole message to notice div */
 
    div.appendChild( p );
 
    /* Create Dismiss icon */
     
    var b = document.createElement( 'button' );
    b.setAttribute( 'type', 'button' );
    b.classList.add( 'notice-dismiss' );
 
    /* Add screen reader text to Dismiss icon */
 
    var bSpan = document.createElement( 'span' );
    bSpan.classList.add( 'screen-reader-text' );
    bSpan.appendChild( document.createTextNode( 'Dismiss this notice' ) );
    b.appendChild( bSpan );
 
    /* Add Dismiss icon to notice */
 
    div.appendChild( b );
 
    /* Insert notice after the first h1 */
     
    var h1 = document.getElementsByTagName( 'h1' )[0];
    h1.parentNode.insertBefore( div, h1.nextSibling);
 
 
    /* Make the notice dismissable when the Dismiss icon is clicked */
 
    b.addEventListener( 'click', function () {
        div.parentNode.removeChild( div );
    });
 
     
}
/**
 * CLink Get Pre Authorized Date
 *
 */
function wpclink_get_pre_authorized_media(){
		
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
function wpclink_reload_media(key, value) {
    key = encodeURIComponent(key);
    value = encodeURIComponent(value);

    // kvp looks like ['key1=value1', 'key2=value2', ...]
    var kvp = document.location.search.substr(1).split('&');
    let i=0;

    for(; i<kvp.length; i++){
        if (kvp[i].startsWith(key + '=')) {
            let pair = kvp[i].split('=');
            pair[1] = value;
            kvp[i] = pair.join('=');
            break;
        }
    }

    if(i >= kvp.length){
        kvp[kvp.length] = [key,value].join('=');
    }

    // can return this or...
    let params = kvp.join('&');

    // reload page with new params
    document.location.search = params;
}
function cl_present_popup_media(){
		
		var license_type_position = jQuery(".license-slot").offset();
		
		jQuery('#cl_present').fadeIn('fast');
		jQuery('#cl_present').css('position','absolute');
		jQuery('#cl_present').css('top',license_type_position.top+'px');
		jQuery('#cl_present').css('margin-top','0');
		
		jQuery('.present .close-pbox').click(function(){
			jQuery('#cl_present').fadeOut('fast');
		});
	
		jQuery( "#cl_present" ).draggable({ handle: ".dashicons-move" });
	}
jQuery( '#poststuff' ).ready(function() {
	
	// Post ID
	var post_id_value = jQuery("#post_ID").val();
	var taxonomy_permission_linked = jQuery("#attachments-"+post_id_value+"-wpclink_image_taxonomy_permission").val();
	
				
	if(taxonomy_permission_linked.includes("ModifyHeadline") &&  taxonomy_permission_linked.includes("linked")){
		
	}else if(taxonomy_permission_linked == ""){
		
	}else{
		if(taxonomy_permission_linked.includes("linked")){
		jQuery('#title').prop("readonly", true);
		}
	}
	
	
	if(!taxonomy_permission_linked.includes("ModifyDescription") &&  taxonomy_permission_linked.includes("linked")){
		jQuery('#attachment_caption').prop("readonly", true);
	}
	
	if(!taxonomy_permission_linked.includes("ModifyDescription") &&  taxonomy_permission_linked.includes("linked")){
		jQuery('#attachment_content').prop("readonly", true);
	}
	
});
jQuery( document ).ready(function() {
	// Post ID
	var post_id_value = jQuery("#post_ID").val();
	
	jQuery('.compat-attachment-fields').addClass('cl_metadata');
	jQuery('.compat-attachment-fields tr').each(function(){
		
		var myclass = jQuery(this).attr('class');
		if (typeof myclass !== typeof undefined && myclass !== false) {
 
		
		 
		
		var fieldname = myclass.replace('compat-field-wpclink_image_metadata_','');
		var href_link = '';
		var iptc_website = 'https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata';
		
// Fields Value
switch(fieldname){
	case 'headline':
		href_link = '#headline';
	break;
	case 'creator':
		href_link = '#creator';
	break;
	case 'description':
		href_link = '#description';
	break;
	case 'keywords':
		href_link = '#keywords';
	break;
	case 'subject_code':
		href_link = '#subject-code';
	break;
	case 'description_writer':
		href_link = '#description-writer';
	break;
	case 'date_created':
		href_link = '#date-created';
	break;
	case 'date_created':
		href_link = '#date-created';
	break;
	case 'scene_code':
		href_link = '#scene-mcode';
	break;
	case 'intellectual_genre':
		href_link = '#intellectual-genre';
	break;
	case 'job_id':
		href_link = '#job-id';
	break;
	case 'instruction':
		href_link = '#instruction';
	break;
	case 'credit':
		href_link = '#credit-line';
	break;
	case 'source':
		href_link = '#source';
	break;
	case 'copyright_notice':
		href_link = '#copyright-notice';
	break;
	case 'title':
		href_link = '#title';
	break;
}
		if(href_link !== ''){
			jQuery(this).append('');
			
			jQuery(this).find('.field').append('<a target="_blank" class="iptc_ico dashicons dashicons-visibility" href="'+iptc_website+href_link+'"></a>');
			
			if(fieldname == 'subject_code'){
				jQuery(this).find('.field').append('<a target="_blank" class="contvoc"  href="http://cv.iptc.org/newscodes/subjectcode">Controlled Vocabulary</a>');
			}else if(fieldname == 'scene_code'){
				jQuery(this).find('.field').append('<a target="_blank" class="contvoc" href="http://cv.iptc.org/newscodes/genre">Controlled Vocabulary</a>');	 
			}else if(fieldname == 'intellectual_genre'){
				jQuery(this).find('.field').append('<a target="_blank"  href="http://cv.iptc.org/newscodes/genre" class="contvoc">Controlled Vocabulary</a>');	 
			}else if(fieldname == 'title'){
				
				
				
				
				// ModifyDescription-linked
				
				
				var taxonomy_permission_linked = jQuery("#attachments-"+post_id_value+"-wpclink_image_taxonomy_permission").val();
				
				if(taxonomy_permission_linked.includes("ModifyHeadline") || taxonomy_permission_linked.includes("ModifyKeywords") || taxonomy_permission_linked.includes("ModifyDescription") || taxonomy_permission_linked == 'disallow'){
					
				}else{
					jQuery(this).find('.field').append('<label class="apply_filename_wrapper"><input id="apply_filename" type="checkbox" value="1"/>Apply File Name</label>');
				}
				
				
				
				
				jQuery('#apply_filename').change(function(){
					
					var post_id_value = jQuery("#post_ID").val();
					var apply_filename;
					
					if(this.checked) {
						apply_filename = 'yes';
					}else{
						apply_filename = 'no';
					}
					
					jQuery.ajax({
						url: ajaxurl,
						data: {
							"action": "wpclink_get_filename_attachment_request",
				            "post_id" : post_id_value,
							"apply_filename" : apply_filename,
							"apply_filename_enable" : "enable"
						},
						success:function(data) {
							if(data == "0"){
								
							}else{
								
								jQuery('#attachments-'+post_id_value+'-wpclink_image_metadata_title').val(data);
								
							}
							
        				},
        				error: function(errorThrown){
            				console.log(errorThrown);
        				}
					});
						
				});
				
			}
		
		}
			
		}
		
		  });
	});
jQuery( document ).ready(function() {
	
	// Caption and Description
	jQuery('label[for="attachment_caption"] strong').append(' / IPTC Description');
	
	var licensor_url = jQuery('#wpclink_license_selected_type').val();
	
	
	if(licensor_url == 'custom'){
		jQuery('#wpclink-custom-license').show();
		jQuery('#wpclink-programmatic-license').hide();
	}else if(licensor_url == "wpclink_personal") {
		jQuery('#wpclink-programmatic-license').show();
		jQuery('#wpclink-custom-license').hide();
	}else if (typeof licensor_url === "undefined") {
		
	}else{

	}
	
	jQuery('#wpclink_license_selected_type').change(function(){
		
		licensor_url = jQuery(this).val();
		
		if(licensor_url == 'custom'){
			jQuery('#wpclink-custom-license').show();
			jQuery('#wpclink-programmatic-license').hide();
		}else if(licensor_url == "wpclink_personal") {
			jQuery('#wpclink-programmatic-license').show();
			jQuery('#wpclink-custom-license').hide();
		}else{

		}
		
	});
	
	// Title
	jQuery("#title").change(function() {                  
			jQuery('#wpclink-preview-box .image-title h2').val(this.value);
			
		});
	jQuery( "#title" ).keyup(function() {
		jQuery('#wpclink-preview-box .image-title h2').text(this.value); 
	
	});
	
	// Attachment
	jQuery("#attachment_caption").change(function() {                  
			jQuery('#wpclink-preview-box .caption').text(this.value);              
		});
	jQuery("#attachment_caption").keyup(function() {                  
			jQuery('#wpclink-preview-box .caption').text(this.value);                
		});
	
	
	jQuery('.compat-field-wpclink_image_metadata_creator input[type="text"]').keyup(function() {           
		jQuery('.gfield-creator').text(this.value);
		if(cl_use_template_creator != this.value){
			jQuery(this).css('background-color','#fff');
		}else{
			jQuery(this).css('background-color','#e9f7ff');
		}
	});
	
	jQuery('.compat-field-wpclink_image_metadata_creator input[type="text"]').change(function() {           
		jQuery('.gfield-creator').text(this.value);
		if(cl_use_template_creator != this.value){
			jQuery(this).css('background-color','#fff');
		}else{
			jQuery(this).css('background-color','#e9f7ff');
		}
	});
	
	jQuery('.compat-field-wpclink_image_metadata_credit input[type="text"]').keyup(function() {           
		jQuery('.gfield-credit').text(this.value);
		if(cl_use_template_credit != this.value){
			jQuery(this).css('background-color','#fff');
		}else{
			jQuery(this).css('background-color','#e9f7ff');
		}
	});
	
	jQuery('.compat-field-wpclink_image_metadata_credit input[type="text"]').change(function() {           
		jQuery('.gfield-credit').text(this.value);
		if(cl_use_template_credit != this.value){
			jQuery(this).css('background-color','#fff');
		}else{
			jQuery(this).css('background-color','#e9f7ff');
		}
	});
	
	jQuery('.compat-field-wpclink_image_metadata_copyright_notice input[type="text"]').keyup(function() {           
		//jQuery('.gfield-credit').text(this.value);
		if(cl_use_template_copynotice != this.value){
			jQuery(this).css('background-color','#fff');
		}else{
			jQuery(this).css('background-color','#e9f7ff');
		}
	});
	
	jQuery('.compat-field-wpclink_image_metadata_copyright_notice input[type="text"]').change(function() {           
		//jQuery('.gfield-credit').text(this.value);
		if(cl_use_template_copynotice != this.value){
			jQuery(this).css('background-color','#fff');
		}else{
			jQuery(this).css('background-color','#e9f7ff');
		}
	});
	
	
	
	
});
		
// 
jQuery( document ).ready(function() {
	
	
if (window.location.href.indexOf("media_updated") > -1) {
	
	var post_id = jQuery("#post_ID").val();
	
// Rest Iamges
	jQuery.ajax({
		cache: false,
		url: ajaxurl,
		data: {
			'action': 'wpclink_process_rest_media',
			'post_id' : post_id,
			'cl_action' : 'rest_images'
		},
		beforeSend: function(){
	   },
		success:function(data) {			
			if(data == 1){
				jQuery('.cl-default-updated').fadeOut();
				wpclink_admin_notice_core('Media file updated.','','','cl-media-updated');
			}
		}
	});
		
	 }
	
	if(jQuery("#template_use").is(':checked')) {
		jQuery('.compat-field-wpclink_image_metadata_creator input[type="text"]').css('background-color','#e9f7ff');
		jQuery('.compat-field-wpclink_image_metadata_credit input[type="text"]').css('background-color','#e9f7ff');
		jQuery('.compat-field-wpclink_image_metadata_copyright_notice input[type="text"]').css('background-color','#e9f7ff');
	}
	
	// Change License Class
	jQuery("#apply-custom-license").click(function(){
		var custom_url;
		var web_statement_rights;
		var right_object;
		var button_label;
		
		var post_id = jQuery("#post_ID").val();
		
		custom_url = jQuery("#custom_url").val();
		
		web_statement_rights = jQuery("#wpclink_custom_web_statement_rights").val();
		
		if(jQuery("#wpclink_right_object").is(":checked")){
			right_object = 1;
		}else{
			right_object = 0;
		}
		
		// Button Label
		button_label = jQuery("#wpclink_license_button").val();
		
	// Save Custom License
	jQuery.ajax({
	cache: false,
	url: ajaxurl,
	data: {
		'action': 'wpclink_custom_license_apply_request',
		'post_id' : post_id,
		'custom_url' : custom_url,
		'web_statement_rights' : web_statement_rights,
		'right_object' : right_object,
		'button_label' : button_label
	},
	beforeSend: function(){
	 // Removed for Background Process
     //jQuery('.loading-circle-quick').fadeIn('100');
		
	jQuery('.status-mini-bar').show();
   },
   	success:function(data) {
		
		if(data == "complete"){
			wpclink_after_license_assign_action();
		}else{
			wpclink_after_license_assign_action();
		}			
	}
	});
		
	});
});
								
jQuery( document ).ready(function() {
// Change License Class
jQuery(".select-license").click(function(){
	//alert();
	 var get_referent_id = jQuery("#post_ID").val();
	 var get_license_class = 'personal';
	 var get_taxonomy_permission = jQuery('#quick-license-type').val();
	
	var permission_array = []; 
	jQuery(".clink_permission_wrapper input:checked").each(function() { 
		permission_array.push(jQuery(this).val());
	}); 
	
	if(permission_array.length == 0){
		permission_array = ["un-editable"];
	}
	
	console.log(permission_array);
	
	jQuery("#cl-license-selection_step2").show(200);
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
							//jQuery('#cl-license-selection_step2').show();
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
				jQuery('#taxonomy_permission_slot').val(permission_array.join(","));
				// Post ID
				jQuery('#referent_id_s2').val(get_referent_id);
				// Show Pre Authorized Popup
				jQuery('.cl_show_license').click(function(){
					jQuery('#cl_show_pre_auth').hide();
					jQuery('#cl_show_current_license').show();
				});
		if(get_taxonomy_permission == 'ModifyDescription'){
			jQuery('.fill_class').show();
			jQuery('.taxo_class').html('ModifyDescription');
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
	
	
// Show License Button
		jQuery('.show-lc-btn').click(function(){
			jQuery( "#cl-license-show" ).dialog({maxWidth:600,maxHeight: 500,width: 600,height:500});
			jQuery('#cl-license-show-box').show();
			
		});
});
	
	
	// Make Referent Button
			jQuery(".make-ref-btn_s2").click(function(){
			
				 post_id_value = jQuery("#post_ID").val();
				 license_class = jQuery('#license_class_s2').val();
				 get_taxonomy_permission = jQuery('#taxonomy_permission_slot').val();
				 get_marketplace_cat = jQuery('#marketplace_cat_slot').val();
				 get_price_value = jQuery('#price_value').val();
				
				console.log(get_taxonomy_permission);
				
				// Remnoved for Background Process
				//jQuery('.loading-circle-quick').fadeIn('100');
				
				jQuery('.status-mini-bar').show();
				jQuery('#cl-license-selection_step2').hide();
				jQuery('#cl-license-selection_step2 .close-pbox').hide();
				
				
					
					jQuery.ajax({
						url: ajaxurl,
						data: {
							"action": "wpclink_save_license_class_post_editor",
				            "post_id" : post_id_value,
							"license_class" : license_class,
							"taxonomy_permission" : get_taxonomy_permission,
							"marketplace_cat" : get_marketplace_cat,
							"license_version": "0.9",
							"price_value": get_price_value,
							"nonce": ajax.nonce
							
						},
						success:function(data) {
							
							var datajson;
							
							datajson = jQuery.parseJSON(JSON.stringify(data));
							
							
							
							if(datajson.complete == 'failed'){
								wpclink_error_dialoge_box(data,"license_popup");
							}else{
							
							// Here
							jQuery(".license-slot").html(datajson.data);
							jQuery(".license-version-slot").html("<strong>Version:</strong> 0.9");
							wpclink_get_pre_authorized_media();
							//wpclink_get_licenses_link();
							jQuery('#cl-license-selection_step2').hide();
							jQuery('#wpclink_license_selected_type').hide();
							wpclink_after_license_assign_action();
								
							}
							
							
							
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
	
	// Enable checkbox template use
	jQuery("#template_use").change(function() {
		var post_id_value = jQuery("#post_ID").val();
		
		if(this.checked) {
			
			jQuery.ajax({
						url: ajaxurl,
						data: {
							"action": "wpclink_image_metadata_get_template_use",
				            "post_id" : post_id_value,
							"template_action": "save"
						},
						success:function(data) {
							if(data == "0"){
								
							}else{
								var template_use_data = JSON.parse(data);
								if(template_use_data.status == 'exists'){
									jQuery('.template_use_save').hide();
									jQuery('.template_use_update').show();
									
									cl_use_template_creator = template_use_data.data['creator'];
									cl_use_template_credit = template_use_data.data['credit_line'];
									cl_use_template_copynotice = template_use_data.data['copyright'];
									
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_creator").val(template_use_data.data['creator']).css('background-color','#e9f7ff');
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_credit").val(template_use_data.data['credit_line']).css('background-color','#e9f7ff');
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_copyright_notice").val(template_use_data.data['copyright']).css('background-color','#e9f7ff');
									
								}else if(template_use_data.status == 'notexists'){
									jQuery('.template_use_update').hide();
									jQuery('.template_use_save').show();
									
									cl_use_template_creator = template_use_data.data['creator'];
									cl_use_template_credit = template_use_data.data['credit_line'];
									cl_use_template_copynotice = template_use_data.data['copyright'];
									
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_creator").val(template_use_data.data['creator']).css('background-color','#e9f7ff');
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_credit").val(template_use_data.data['credit_line']).css('background-color','#e9f7ff');
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_copyright_notice").val(template_use_data.data['copyright']).css('background-color','#e9f7ff');
								}
							}
							
        				},
        				error: function(errorThrown){
            				console.log(errorThrown);
        				}
					});  
		}else{
			
			jQuery.ajax({
						url: ajaxurl,
						data: {
							"action": "wpclink_image_metadata_get_template_use",
				            "post_id" : post_id_value,
							"template_action": "notsave"
						},
						success:function(data) {
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_creator").css('background-color','#fff');
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_credit").css('background-color','#fff');
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_copyright_notice").css('background-color','#fff');
        				},
        				error: function(errorThrown){
            				console.log(errorThrown);
        				}
					});  
			
			jQuery('.template_use_update').hide();
			jQuery('.template_use_save').hide();
		}
	});
	
	jQuery('.template_use_save, .template_use_update').click(function(){
		
		var post_id_value = jQuery("#post_ID").val();
		var metadata_creator = jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_creator").val();
		var metadata_credit_line = jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_credit").val();
		var metadata_copyright = jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_copyright_notice").val();
		
		var btn_clinked = jQuery(this);
		
		if(jQuery(this).text() == "Update"){
			jQuery(this).text("Updating...");
		}else if(jQuery(this).text() == "Save"){
			jQuery(this).text("Saving...");
		}
	
					jQuery.ajax({
						url: ajaxurl,
						data: {
							"action": "wpclink_image_metadata_save_template_use",
				            "post_id" : post_id_value,
							"metadata_creator" : metadata_creator,
							"metadata_credit_line" : metadata_credit_line,
							"metadata_copyright" : metadata_copyright
						},
						success:function(data) {
							if(data == "0"){
								
							}else{
								var template_use_data = JSON.parse(data);
								if(template_use_data.status == 'updated'){
									
									jQuery('.template_use_save').hide();
									jQuery('.template_use_update').show();
									
									jQuery('.template_use_save').text('Save');
									jQuery('.template_use_update').text('Update');
									
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_creator").css('background-color','#e9f7ff');
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_credit").css('background-color','#e9f7ff');
									jQuery("#attachments-"+post_id_value+"-wpclink_image_metadata_copyright_notice").css('background-color','#e9f7ff');
									
								}else if(template_use_data.status == 'notupdated'){
									jQuery('.template_use_update').show();
									jQuery('.template_use_save').hide();
									
									jQuery('.template_use_save').text('Save');
									jQuery('.template_use_update').text('Update');
								}
							}
							
        				},
        				error: function(errorThrown){
            				console.log(errorThrown);
        				}
					});
		
									
		
	});
	
});