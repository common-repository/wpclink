/**
 * CLink Read Only
 *
 * CLink read only for the users which has not access to page
 *
 * @author TeamCLink
 */
jQuery( '#wpbody-content' ).ready(function() {
	jQuery('.cl_subtabs_box').find('input, textarea, button, select').attr('disabled','disabled');
	jQuery('#cl_main').find('input, textarea, button, select').attr('disabled','disabled');
	jQuery('.cl_subtabs_box').find('a.button, a.button-primary').addClass('disabled').removeAttr("href");
	jQuery('#cl_main').find('a.button, a.button-primary').addClass('disabled').removeAttr("href");
});
