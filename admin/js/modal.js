/**
 * CLink Reuse Modal 
 *
 * Resuse Popup Ajax Request and Render
 *
 * @author TeamCLink
 */
  
/**
 * CLink Add Class Old Function
 *
 *
 * @fires   popup show
 *
 * @param object    el           DOM Object
 * @param object    className    Class of Element
 */
function cl_addClass_old(el, className) {
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
// Get Body
var mybody = document.getElementsByTagName("body")[0];
// Get the modal
var modal = document.getElementById('CLModal');
// Get the button that opens the modal
var btn = document.getElementById("myBtn");
// Get the <span> element that closes the modal
var span = document.getElementsByClassName("cl_close")[0];
// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    
	modal.style.animation = "fadeOut";
	modal.style.animationDuration = "1s";
	modal.style.animationDelay = "0";
	
	cl_removeClass_old(modal,'show-now');
	cl_removeClass_old(mybody,'show-now-blur');
	
	setTimeout(function() {
			modal.style.display = "none";
		},1000);
	
}
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
		
		modal.style.animation = "fadeOut";
		modal.style.animationDuration = "1s";
		modal.style.animationDelay = "0";
		
		cl_removeClass_old(modal,'show-now');
		cl_removeClass_old(mybody,'show-now-blur');
		
		setTimeout(function() {
			modal.style.display = "none";
		},1000);
		
    }
	
}
setTimeout(function() {
     modal.style.display = "block";
	 cl_addClass_old(modal,'show-now');
	 cl_addClass_old(mybody,'show-now-blur');
}, 1000);
// CLink offer submit
jQuery(document).ready(function($) {
 
 $( "#cl_new_offer" ).submit(function(e) {
 
    // We'll pass this variable to the PHP function example_ajax_request
    var weburl = $('#cl_weburl').val();
	
	e.preventDefault();
     
    // This does the ajax request
    $.ajax({
        url: cl_ajaxurl,
        data: {
            'action':'wpclink_is_wpsite_ajax_request',
            'weburl' : encodeURI(weburl)
        },
        success:function(data) {
            // This outputs the result of the ajax request
           $('#cl_offer_response').html(data);
		   
			$("#next_step2").click(function(e) {
			   $("#cl_step1").hide();
			   $("#cl_step2").show();
			});
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
	
	
	
 });
 	$(document).ajaxStart(function(){
        $("#wait").css("display", "block");
    });
    $(document).ajaxComplete(function(){
        $("#wait").css("display", "none");
    });
           
});
// Clink Reuse Popup Submit
jQuery(document).ready(function($) {
 
 $( "#cl_new_offer_clinkid" ).submit(function(e) {
 
    // We'll pass this variable to the PHP function example_ajax_request
    var weburl = $('#cl_weburl').val();
	
	e.preventDefault();
    $.ajax({
        url: cl_ajaxurl,
        data: {
            'action':'wpclink_verify_site_url',
            'weburl' : encodeURI(weburl)
        },
        success:function(data) {
            // This outputs the result of the ajax request
           $('#cl_offer_response').html(data);
		   
			$("#next_step2").click(function(e) {
			   $("#cl_step1").hide();
			   $("#cl_step2").show();
			});
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
	
 });
 
 	$(document).ajaxStart(function(){
        $("#wait").css("display", "block");
    });
    $(document).ajaxComplete(function(){
        $("#wait").css("display", "none");
    });
              
});