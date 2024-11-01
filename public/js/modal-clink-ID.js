/**
 * CLink Reuse Modal Offer
 *
 * Resuse Popup Offer Ajax Request and Render
 *
 * @author TeamCLink
 */
  
/**
 * CLink Add Class
 *
 *
 * @fires   popup show
 *
 * @param object    el           DOM Object
 * @param object    className    Class of Element
 */
function cl_addClass(el, className) {
  if (el.classList)
    el.classList.add(className)
  else if (!hasClass(el, className)) el.className += " " + className
}
/**
 * CLink Remove Class Old Function
 *
 *
 * @fires   popup hide
 *
 * @param object    el           DOM Object
 * @param object    className    Class of Element
 */
function cl_removeClass(el, className) {
  if (el.classList)
    el.classList.remove(className)
  else if (hasClass(el, className)) {
    var reg = new RegExp('(\\s|^)' + className + '(\\s|$)')
    el.className=el.className.replace(reg, ' ')
  }
}
/**
 * CLink Get URL Location
 *
 *
 * @fires   document.ready
 *
 * @param object    name         site address
 */
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};
/**
 * CLink Care Authorize Popup Set Cookie
 *
 *
 * @fires   .go_care.click
 *
 * @param object    cname         cookie name
 * @param object    cvalue        cookie value
 * @param object    exdays        expiration period
 */
function popup_setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
// Get Body
//var mybody = document.getElementsByTagName("body")[0];
// Get the modal
//var modal = document.getElementById('CLModal');
// Get the button that opens the modal
//var btn = document.getElementById("myBtn");
// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("cl_close")[0];
// When the user clicks on <span> (x), close the modal

jQuery('.cl_close').click(function() {
    
	jQuery( '#CLModal' ).css( "animation", "fadeOut" );
	jQuery( '#CLModal' ).css( "animationDuration", "1s" );
	jQuery( '#CLModal' ).css( "animationDelay", "0" );
		
	jQuery( '#CLModal' ).removeClass('show-now');
	jQuery( 'body' ).removeClass('show-now-blur');
	
	setTimeout(function() {
			jQuery( '#CLModal' ).hide();
		},1000);
	
});
jQuery(document).ready(function() {
	// When the user clicks anywhere outside of the modal, close it
	jQuery(window).click(function() {
		if (event.target == jQuery( '#CLModal' )) {

			jQuery( '#CLModal' ).css( "animation", "fadeOut" );
			jQuery( '#CLModal' ).css( "animationDuration", "1s" );
			jQuery( '#CLModal' ).css( "animationDelay", "0" );

			cl_removeClass(jQuery( '#CLModal' ),'show-now');
			cl_removeClass(jQuery( 'body' ),'show-now-blur');

			setTimeout(function() {
				jQuery( '#CLModal' ).hide();
				jQuery('.welcome-message').show();
				jQuery('#wordpress_confirm').hide();
			},1000);

		}

	});
	jQuery('.cl-resuse, #reuse .cl-get-license').click(function() {
		 jQuery('#cl_new_offer_clinkid').show();
		 jQuery( '#CLModal' ).show();
		 jQuery( '#CLModal' ).addClass('show-now');
		 jQuery( 'body' ).addClass('show-now-blur');
		 jQuery('#confirm-box').show();
	});
	jQuery('.continue').click(function(){
		jQuery('#cl_new_offer_clinkid').show()
		jQuery('#confirm-box').hide();
	});
	jQuery('.option-single').click(function(){
		jQuery('.clid_option').hide();
		jQuery('#content_options').hide();
		jQuery('.option1').show();
	});
	jQuery('.option-multiple').click(function(){
		jQuery('.clid_option').hide();
		jQuery('#content_options').hide();
		jQuery('.option2').show();
	});
	jQuery('.step-next').click(function(){
		jQuery('.welcome-message').hide();
		jQuery('#content_options').show();
		jQuery('#wordpress_confirm').show();
	});	
	jQuery('.additional-btn').click(function(){
		jQuery('.additional_option').slideToggle(300);
		jQuery(this).toggleClass( "active" )
	});
});
jQuery(document).ready(function($) {
if(getUrlParameter('clink') == 'offer'){
	// ACTION
	jQuery('#cl_new_offer_clinkid').show();
	jQuery( '#CLModal' ).show();
	jQuery( '#CLModal' ).show();
	jQuery( '#CLModal' ).addClass('show-now');
	jQuery( 'body' ).addClass('show-now-blur');
	jQuery('#confirm-box').show();
}
 
 $( "#cl_new_offer_clinkid" ).submit(function(e) {
	 
	  
	 
	 
	
 
    // We'll pass this variable to the PHP function example_ajax_request
    var weburl = $('#cl_weburl').val();
	var content_type = $('.content_type:checked').val();
	var current_url = window.location.href;
	current_url = current_url.replace("?clink=offer", "");
	
	e.preventDefault();
	 $('#cl_modal_submit').attr("disabled", "disabled");
	 
	 
	 $("#wait").css("display", "block");
	 
	 
	
     
    // This does the ajax request
    $.ajax({
        url: cl_ajaxurl,
        data: {
            'action':'wpclink_verify_site_url',
            'weburl' : encodeURI(weburl),
			'content_type' : content_type,
			'content_url' : encodeURI(current_url)
        },
        success:function(data) {
            // This outputs the result of the ajax request
			
			var response_check = $(data).filter("#response_return").val();
			var reuse_token = $(data).filter("#reuse_token_key").val();
			
			if(response_check == 'success'){
				
				
				//$('#cl_offer_response').html(data);	
				
				// Token submission
				$.ajax({
					url: cl_ajaxurl,
					data: {
						'action':'wpclink_send_token_to_hub',
						'weburl' : encodeURI(weburl),
						'token' : reuse_token
					},
					success: function (data, status, jqXHR) {
						
	
						
						
						if(data.length > 4){
						$('#cl_modal_submit').attr("disabled", "disabled");
						$('#CLModal').hide();
						window.location.replace(data);
							$('#cl_modal_submit').attr("disabled", "disabled");
						}else if(data == '404'){
							$('#cl_offer_response').html('<p class="message error">Domain is not found</p>');
							alert('Domain not found');
						}else if(data == '400'){
							$('#cl_offer_response').html('<p class="message error">Sorry, we cannot process your request, please try it later. If the problem persist, please report at <a href="#">CLink Request Error</a></p>');
							
						}else{
							$('#cl_offer_response').html('<p class="message error">Sorry, we cannot process your request, please try it later. If the problem persist, please report at <a href="#">CLink Request Error</a></p>'+data);
						}
					},
					error: function (jqXHR, status, err) {
       						$('#cl_offer_response').html('<p class="message error">Sorry, we cannot process your request, please try it later. If the problem persist, please report at <a href="#">CLink Request Error</a></p>'+data);
					}
				});
				
			}else{
				$('.quick-response').html(data);
				$('#cl_modal_submit').removeAttr("disabled"); 
				
			}
		
           
		   $("#wait").css("display", "none");
			
			
		  
		  
		  jQuery('#copybtn').click(function() {
				var copyText = jQuery('.startlink');
				copyText.select();
				document.execCommand("copy");
				jQuery('#copybtn').text("Copied").css('background-color','#FFFFFF').css('color','#000000');
			});
			
        },
        error: function(errorThrown){
            console.log(errorThrown);
			$("#wait").css("display", "none");
			$('#cl_modal_submit').removeAttr("disabled"); 
        }
		
		
		
    });
	
	
	
 });
 
  var care_auth_window;
 var full_url_popup;
 
 $( ".go_care" ).click(function() {
	 
	var current_url = window.location.href;
	current_url = current_url.replace("?clink=offer", "");
	 
	 var popup_id = $('#cl_popup_id').val();
	 
	 popup_setCookie('care_authorize',popup_id,7);
	 
	 care_auth_window = window.open('https://us-customers.clink.id/?care_auth&token='+popup_id+'&requested_site='+encodeURIComponent(current_url), 
                         'newwindow', 
                         'width=320,height=425');
	 full_url_popup = 'https://us-customers.clink.id/?care_auth&token='+popup_id+'&requested_site='+encodeURIComponent(current_url);
	 
	 care_auth_window.document.write("<html><head><title>Please Wait..</title></head><body style='margin:0; padding:0; background-color:#F5F5F5;'><div style='width:100%; height:100%; position:absolute; padding:20% 0;text-align: center;box-sizing: border-box; text-align:center;font-family: Arial; font-size:15px;'><p style='padding:0; margin:0;'><div class='lds-css ng-scope'><div style='width:100%;height:100%' class='lds-rolling'><div></div></div><style type='text/css'>@keyframes lds-rolling{0%{-webkit-transform:translate(-50%, -50%) rotate(0deg);transform:translate(-50%, -50%) rotate(0deg)}100%{-webkit-transform:translate(-50%, -50%) rotate(360deg);transform:translate(-50%, -50%) rotate(360deg)}}@-webkit-keyframes lds-rolling{0%{-webkit-transform:translate(-50%, -50%) rotate(0deg);transform:translate(-50%, -50%) rotate(0deg)}100%{-webkit-transform:translate(-50%, -50%) rotate(360deg);transform:translate(-50%, -50%) rotate(360deg)}}.lds-rolling{position:relative}.lds-rolling div, .lds-rolling div:after{position:absolute;width:160px;height:160px;border:20px solid #fcb711;border-top-color:transparent;border-radius:50%}.lds-rolling div{-webkit-animation:lds-rolling 1s linear infinite;animation:lds-rolling 1s linear infinite;top:100px;left:100px}.lds-rolling div:after{-webkit-transform:rotate(90deg);transform:rotate(90deg)}.lds-rolling{margin:7px auto; width:32px !important;height:32px !important;-webkit-transform:translate(-16px, -16px) scale(0.16) translate(16px, 16px);transform:translate(-16px, -16px) scale(0.16) translate(16px, 16px)}</style></div>Please Wait.. </p></div></div><script>window.location.href ='"+full_url_popup+"';</script></body></html>");
	 
	 
 });
 
	
	/* ANOTHER EVENT */
	
	var delay = (function(){
	  var timer = 0;
	  return function(callback, ms){
	  clearTimeout (timer);
	  timer = setTimeout(callback, ms);
	 };
	})();
	
	
	
	$('#cl_weburl').keyup(function() {
		if(jQuery(this).val() == ''){
		}else{
		  delay(function(){
    		
	var weburl = $('#cl_weburl').val();
	
    // This does the ajax request
    $.ajax({
        url: cl_ajaxurl,
        data: {
            'action':'wpclink_verify_site_url_real_time',
            'weburl' : encodeURI(weburl)
        },
        success:function(data) {
            // This outputs the result of the ajax request
           $('.quick-response').html(data);
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
			
			
			
			
		  }, 1000 );
		}
	});
	
	
var request_care_complete;
	
var cl_repeat = function () {
	
	var return_array_data = new Array();
	if(request_care_complete == true) return false;
	
	
	 // This does the ajax request
    $.ajax({
        url: cl_ajaxurl,
		cache: false,
        data: {
            'action':'wpclink_care_auto_check_ajax_request',
            'cl_popup_info_check' : 1
		},
        success:function(data) {
         if(data != 'false'){
			 return_array_data = data.split(',');
			 
			 jQuery('#cl_name').val(return_array_data[1]);
			 jQuery('#cl_weburl').val(return_array_data[0]);
			 
			 
			 
			 care_auth_window.close();
			 jQuery('#cl_new_offer_clinkid').submit();
			 
			 jQuery("#wait").css("display", "block");
			  
			request_care_complete = true;
			return false;
			 
			 
		 }else{
			console.log('none'); 
		 }
			
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
	
	
	
  // Do stuff
   setTimeout(cl_repeat, 8000);
};
setTimeout(cl_repeat, 8000);
              
});
