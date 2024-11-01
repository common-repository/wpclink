/**
 * CLink Licensable Media
 *
 * Shows CLink License Options
 *
 * @author TeamCLink
 */
  

// More Links Media
var click_morelinks;

// Links
jQuery(document).click(function() {
    jQuery(this).find('.ul-links').fadeOut();
});



// Media Licensable
jQuery(document).ready(function() {
  
   jQuery('body').click(function(event){  
	   
	 if(jQuery(event.target).is(".cl-info-menu, .cl-info-menu *")) return;
	   
	 // Hide All
	jQuery('.cl-subitem').fadeOut();
	jQuery('.cl-darkmode-icon').fadeOut();	

  });
	
	
    jQuery("img").each(function() {
		
	if (typeof jQuery(this).data("use-mouse-over") !== "undefined") {
			
			jQuery(this).click(function(){
				jQuery(this).parent().find('.cl-subitem').fadeOut();
				jQuery(this).parent().find('.cl-darkmode-icon').fadeOut();		   
			});
	}
		
	if (typeof jQuery(this).data("clink-media-license") !== "undefined") {
			
			jQuery(this).click(function(){
				jQuery(this).parent().parent().find('.cl-subitem').fadeOut();
				jQuery(this).parent().parent().find('.cl-darkmode-icon').fadeOut();		   
			});
	}
	});
	
    jQuery('.clink-imagebox .cl-info-icon, .clink-contentbox .cl-info-icon, .clink-templatetag-box .cl-info-icon').click(function(event) {
		
        jQuery(this).parent().find('.cl-subitem').fadeToggle();
		jQuery(this).parent().find('.cl-darkmode-icon').fadeToggle();
		
		event.stopPropagation(); // This is the preferred method.
		return false;        // This should not be used unless you do not want
                         // any click events registering inside the div
		
    });
	
		jQuery('.cl-darkmode-icon').click(function(event){
			jQuery(this).parent().toggleClass('night');
			jQuery(this).toggleClass('night');
			
			event.stopPropagation(); 
			return false;        
                         
		
		});


});

function wpclink_expand_auth_popup(target = '', ptype = "" ){
	
	
	
		// Toggle
	jQuery(".cl-expend-btn-"+target+ptype).click(function(){ 
		
		
			
		var see_btn = jQuery(this);
		var postid_btn = jQuery(this).data('postid');
	
	jQuery("#cl-"+target+"-list-more-"+postid_btn+ptype).slideUp("1000","swing",function() {
		jQuery('#'+target+'-load-more-'+postid_btn+ptype).contents().last().replaceWith("More");
	});
	
		//alert("#cl-"+target+"-list-"+postid_btn+ptype);

	jQuery("#cl-"+target+"-list-"+postid_btn+ptype).slideToggle("fast",function() {
		
		
			
		if(jQuery("#cl-"+target+"-list-"+postid_btn+ptype).css("display") == "none"){
				see_btn.contents().last().replaceWith("+");			
		}else{	
				see_btn.contents().last().replaceWith("&#8722;");
	
		}
			
  		});
			
	});

	
}

function wpclink_load_more_auth_popup(target = '', ptype = '' ){
	
	

	
	jQuery("."+target+"-load-more"+ptype).click(function(){ 
		
			//alert("."+target+"-load-more"+ptype);
		
			var see_more = jQuery(this);
			var postid_btn = jQuery(this).data('postid');

			jQuery("#cl-"+target+"-list-more-"+postid_btn+ptype).slideToggle("1000","swing",function() {

			if(jQuery("#cl-"+target+"-list-more-"+postid_btn+ptype).css("display") == "none"){
				
					see_more.contents().last().replaceWith("More");
			}else{
					see_more.contents().last().replaceWith("Less");
			}

			});
	});
	
	
}


// Media Licensable
jQuery(document).ready(function() {

	/* Reuse */
	wpclink_expand_auth_popup('version','.reuse');
	wpclink_load_more_auth_popup('version','.reuse');

	wpclink_expand_auth_popup('archive','.reuse');
	wpclink_load_more_auth_popup('archive','.reuse');

	/* Template */
	wpclink_expand_auth_popup('version','.template');
	wpclink_load_more_auth_popup('version','.template');

	wpclink_expand_auth_popup('archive','.template');
	wpclink_load_more_auth_popup('archive','.template');

	/* Checkout */
	wpclink_expand_auth_popup('version','.checkout');
	wpclink_load_more_auth_popup('version','.checkout');

	wpclink_expand_auth_popup('archive','.checkout');
	wpclink_load_more_auth_popup('archive','.checkout');

	/* Media */
	wpclink_expand_auth_popup('version','.media');
	wpclink_load_more_auth_popup('version','.media');

	wpclink_expand_auth_popup('archive','.media');
	wpclink_load_more_auth_popup('archive','.media');
	
});


// Media Licensable
jQuery(document).ready(function() {
	
	jQuery('.btn-clink').click(function(){
		
		
		jQuery(this).parent().addClass('active');
		jQuery('.btn-c2pa').parent().removeClass('active');
		
		jQuery(this).parent().parent().parent().find('.clink-section').show();
		jQuery(this).parent().parent().parent().find('.section-c2pa').hide();
	});
	
	jQuery('.btn-c2pa').click(function(){
		
		jQuery(this).parent().addClass('active');
		jQuery('.btn-clink').parent().removeClass('active');
		
		jQuery(this).parent().parent().parent().find('.clink-section').hide();
		jQuery(this).parent().parent().parent().find('.section-c2pa').show();
	});
	
	
});
