<?php
/**
 * CLink Preferences License Template
 *
 * clink preferences license template admin page.
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Register clink license template admin menu
add_action('admin_menu', 'wpclink_register_menu_preferences_licenese_template');
 
 /**
  * CLink License Template Admin Menu
  * 
  */ 
function wpclink_register_menu_preferences_licenese_template(){
	
	// Rights Holder
	$right_holder = wpclink_get_option('rights_holder');
	$creator_array = wpclink_get_option('authorized_creators');
	$current_user_id = get_current_user_id();
	
	$party_id = wpclink_get_option('authorized_contact');
	
		
	if(wpclink_user_has_creator_list($current_user_id) ){
		
		
		add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'edit_posts', 'cl_templates', 'wpclink_display_preferences_license_template' );
	
		
	}elseif($party_id == $current_user_id){
		add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'edit_posts', 'cl_templates', 'wpclink_display_preferences_license_template' );
	}else{
	  	add_submenu_page('cl_mainpage.php', 'Preferences', 'Preferences', 'manage_options', 'cl_templates', 'wpclink_display_preferences_license_template' );
	}
}
/**
  * CLink License Template Admin Page Render
  * 
  */ 
function wpclink_display_preferences_license_template(){ 
	$current_page = menu_page_url( 'cl_templates', false );
	
	$template_type = (isset($_GET['template_type'])) ? $_GET['template_type'] : '';
	
?>
<div id="cl_main" class="wrap agreement_area">
  <?php wpclink_display_tabs_preferences_general();  ?>
	 <ul class="wp-tab-bar">
    <li <?php if($template_type == 'post' || $template_type == '') echo 'class="wp-tab-active"'; ?></li><a <?php if($template_type != 'post' || $template_type != '') echo 'href="'.add_query_arg('template_type','post',$current_page).'"'; ?>>Post / Page</li>
	<li <?php if($template_type == 'attachment') echo 'class="wp-tab-active"'; ?>><a <?php if($template_type != 'attachment') echo  'href="'.add_query_arg('template_type','attachment',$current_page).'"';  ?>">Image</a></li>
  </ul>
  <div class="cl_subtabs_box">
    <?php $updated = false;
	if(wpclink_is_acl_r_page()){
		}else{
	
	}
	$license_class = (isset($_GET['cl_license_class'])) ? $_GET['cl_license_class'] : '';
	$current_page = menu_page_url( 'cl_templates', false );
	$license_ver_array = wpclink_get_option('license_versions');

	?> 
    <form method="post" action="<?php echo esc_url(add_query_arg('cl_license_class','uc-ut-um',$current_page)); ?>">
        <h2><?php _e('PERSONAL USE LICENSE','cl_text'); ?></h2>
        <div>
          <?php settings_fields( 'cl_agreement_options' ); ?>
          <?php
			if($template_type == 'post'){
				$poup_text = wpclink_get_license_template('','post');
			}else if($template_type == 'page') {
				$poup_text = wpclink_get_license_template('','page');
			}else if($template_type == 'attachment') {
				$poup_text = wpclink_get_license_template('','attachment');
			}else{
				$poup_text = wpclink_get_license_template('','post');
			}
			?>
    <div id="license_show_box"><div class="license_text"><?php echo $poup_text; ?><br /><h3 align="right" style="text-decoration:underline; margin-right:50px;"><?php _e('Electronic  signature  of  Licensor'); ?></h3><br /><br /></div></div>
          <br />
        </div>
    </form>
    <script>
	// Call Tooltip Info
jQuery( function() {
   jQuery('.cl_subtabs_box').tooltip();
	 var icons = {
      header: "ui-icon-circle-arrow-e",
      activeHeader: "ui-icon-circle-arrow-s"
    };
 	jQuery('.party_form_btn').click(function(){ jQuery('.party_form').slideToggle('fast'); });
	jQuery('.creator_form_btn').click(function(){ jQuery('.creator_form').slideToggle('fast'); });
	jQuery('.copyright_form_btn').click(function(){ jQuery('.copyright_form').slideToggle('fast'); });
} );
</script> 
  </div>
	<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
</div>
<?php }