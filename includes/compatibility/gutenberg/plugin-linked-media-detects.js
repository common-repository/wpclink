/* Linked Media License Detector */


var cl_license_button_clock = 0;

function refreshData()
{
	if(cl_license_button_clock == 0){
	
		jQuery(".license-slot").css('pointer-events','none');
		jQuery(".license-slot").css('opacity','0.5');
		
    	setTimeout(refreshData, 1);
		
	}
}


( () => {

wp.data.subscribe(function () {
  var isSavingPost = wp.data.select('core/editor').isSavingPost();
  var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();

  if (isSavingPost && !isAutosavingPost) {
    
	  
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
})
	
} )();


( () => {
    let contentState = wp.data.select( 'core/editor' ).getEditedPostContent();
	
	
	
	
	
	
    wp.data.subscribe( _.debounce( ()=> {
        newContentState = wp.data.select( 'core/editor' ).getEditedPostContent();
		
		
		
		
        if ( contentState !== newContentState ) {
			
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
										jQuery( "img[src='"+target_src+"']" ).css('outline','5px dashed #ee7700');
										jQuery( "img[src='"+target_src+"']" ).addClass('cl-linked-img');
									}
									
								
								
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
        // Update reference.
        contentState = newContentState;
    }, 1 ) );
} )();
 
 