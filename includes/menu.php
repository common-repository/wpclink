<?php
/**
 * CLink Admin Menu Functions
 *
 * CLink admin menus and structure functions
 *
 * @package CLink
 * @subpackage System
 */
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Clink Tab Menu for Links Pages
 * 
 */
function wpclink_display_tabs_links(){
	?>
<h1 class="wp-heading-inline"><?php echo get_admin_page_title(); ?></h1>
<div id="logo-mark"><img src="<?php echo plugins_url( 'admin/images/clink-logo.svg', dirname(__FILE__) ) ; ?>"></div>
<nav class="cl_menu blue"> 
  <ul class="menu">
   <?php if(wpclink_export_mode() || wpclink_both_mode()) { ?>
    <li  class="link <?php if($_GET['page'] == 'clink-links-inbound' || $_GET['page'] == 'cl-canonical') echo 'active'; ?>"><a href="<?php echo menu_page_url( 'clink-links-inbound', false );?>">Inbound</a>
   </li>
   <?php } ?>
  <?php if(wpclink_import_mode() || wpclink_both_mode()) { ?>
    <li class="link <?php if($_GET['page'] == 'cl-select-link' || $_GET['page'] == 'All Links' || $_GET['page'] == 'links_form.php' || $_GET['page'] == 'cl-clink' || $_GET['page'] == 'clink-links-outbound' || $_GET['page'] == 'links_form') echo 'active'; ?>"><a href="<?php echo menu_page_url( 'clink-links-outbound', false );?>">Outbound</a></li>
    <?php } ?>
   
 <li class="link cl_support"><a href="#"><span class="dashicons dashicons-info"></span> <?php _e('Need Help ?','cl_text'); ?></a></li>
  <li class="link cl_bugs"><a class="feedback_action"  href="#"><span class="small-feedback"></span> <?php _e('Provide Feedback','cl_text'); ?></a></li>
 <li class="link cl_bugs"><a class="bug_action"  href="#"><span class="small-bug"></span> <?php _e('Report a Bug','cl_text'); ?></a></li>
      </ul>
</nav>
<div id="help-panel">
<div class="tabs">
<ul>
<li><a target="_blank" href="https://docs.clink.media/">Documentation</a></li><li><a target="_blank" href="https://wordpress.org/support/plugin/wpclink">Get Support</a></li></ul>
</div>
<div class="content-tab">
<div id="faq-page"></div>
<div id="support-page"></div>
</div>
</div>

<?php	
}
/**
 * Clink Tab Menu for Links Inbound
 * 
 */
function wpclink_display_tabs_links_inbound(){
	
	$links_inbound = menu_page_url( 'clink-links-inbound', false );
	?>
<h1 class="wp-heading-inline"><?php echo get_admin_page_title(); ?></h1>
<div id="logo-mark"><img src="<?php echo plugins_url( 'admin/images/clink-logo.svg', dirname(__FILE__) ) ; ?>"></div>
<nav class="cl_menu blue"> 
  <ul class="menu">
   <?php if(wpclink_export_mode() || wpclink_both_mode()) { ?>
    <li  class="link <?php if($_GET['page'] == 'clink-links-inbound' || $_GET['page'] == 'cl-canonical') echo 'active'; ?>"><a href="<?php echo menu_page_url( 'clink-links-inbound', false );?>">Inbound</a>
   </li>
   <?php } ?>
  <?php if(wpclink_import_mode() || wpclink_both_mode()) { ?>
    <li class="link <?php if($_GET['page'] == 'cl-select-link' || $_GET['page'] == 'All Links' || $_GET['page'] == 'links_form.php' || $_GET['page'] == 'cl-clink' || $_GET['page'] == 'clink-links-outbound' || $_GET['page'] == 'links_form') echo 'active'; ?>"><a href="<?php echo menu_page_url( 'clink-links-outbound', false );?>">Outbound</a></li>
    <?php } ?>
   
 <li class="link cl_support"><a href="#"><span class="dashicons dashicons-info"></span> <?php _e('Need Help ?','cl_text'); ?></a></li>
  <li class="link cl_bugs"><a class="feedback_action"  href="#"><span class="small-feedback"></span> <?php _e('Provide Feedback','cl_text'); ?></a></li>
 <li class="link cl_bugs"><a class="bug_action"  href="#"><span class="small-bug"></span> <?php _e('Report a Bug','cl_text'); ?></a></li>
      </ul>
</nav>
<div id="help-panel">
<div class="tabs">
<ul>
<li><a target="_blank" href="https://docs.clink.media/">Documentation</a></li><li><a target="_blank" href="https://wordpress.org/support/plugin/wpclink">Get Support</a></li></ul>
</div>
<div class="content-tab">
<div id="faq-page"></div>
<div id="support-page"></div>
</div>
</div>
<div class="small-tabs">
<a class="cl_subtabs <?php if(($_GET['page'] == 'clink-links-inbound' and !isset($_GET['type'])) || ($_GET['page'] == 'clink-links-inbound' and ($_GET['type'] == 'post') )) echo "active";	 ?>" href="<?php echo add_query_arg( 'type', 'post', $links_inbound); ?>"> <?php _e('Posts','cl_text'); ?></a><a class="cl_subtabs <?php if($_GET['page'] == 'clink-links-inbound' and $_GET['type'] == 'attachment') echo "active"; ?>" href="<?php echo add_query_arg( 'type', 'attachment', $links_inbound); ?>"> <?php _e('Media','cl_text'); ?></a><a class="cl_subtabs <?php if($_GET['page'] == 'clink-links-inbound' and $_GET['type'] == 'page') echo "active"; ?>" href="<?php echo add_query_arg( 'type', 'page', $links_inbound); ?>"> <?php _e('Pages','cl_text'); ?></a> 
 </div>
<?php	
}
/**
 * Clink Tab Menu for Content Pages
 * 
 */
function wpclink_display_tabs_creations(){
	
	// Current
	$current_user_id = get_current_user_id();
	// Party
	$clink_party_id = wpclink_get_option('authorized_contact');
	// Creator
	$creator_array = wpclink_get_option('authorized_creators');
	
	// Copyright Owner
	$right_holder = wpclink_get_option('rights_holder');
	
?>
<h1 class="wp-heading-inline"><?php echo get_admin_page_title(); ?></h1>
<div id="logo-mark"><img src="<?php echo plugins_url( 'admin/images/clink-logo.svg', dirname(__FILE__) ) ; ?>"></div>
<nav class="cl_menu blue">
  <ul class="menu">
 <?php if(wpclink_export_mode() || wpclink_both_mode()) { ?>
    <li  class="link <?php if($_GET['page'] == 'cl-restriction' || $_GET['page'] == 'cl-restriction' || $_GET['page'] == 'cl-restriction-page-available' || $_GET['page'] == 'wpclink-creation-media') echo 'active'; ?>"><a href="<?php echo menu_page_url( 'cl-restriction', false );?>">Registered</a>
   </li>
   <?php } ?>
   <?php if(wpclink_export_mode() || wpclink_both_mode()) { ?>
    <li  class="link <?php if($_GET['page'] == 'cl-restriction-reuse' || $_GET['page'] == 'cl-restriction-page' || $_GET['page'] == 'wpclink-creation-referent-media') echo 'active'; ?>"><a href="<?php echo menu_page_url( 'cl-restriction-reuse', false );?>">Referent</a>
   </li>
   <?php } ?>
   <?php if(wpclink_import_mode() || wpclink_both_mode()) {
	  if(wpclink_user_has_creator_list($current_user_id) || $clink_party_id == $current_user_id || current_user_can('administrator') ){
	   ?>
    <li class="link <?php if($_GET['page'] == 'content_link_post.php' || $_GET['page'] == 'content_link_media.php' || $_GET['page'] == 'content_link_page.php') echo 'active'; ?>"><a href="?page=content_link_post.php">Linked</a></li>
   <?php }
  } ?>
  
<li class="link cl_support"><a href="#"><span class="dashicons dashicons-info"></span> <?php _e('Need Help ?','cl_text'); ?></a></li>
<li class="link cl_bugs"><a class="feedback_action"  href="#"><span class="small-feedback"></span> <?php _e('Provide Feedback','cl_text'); ?></a></li>
 <li class="link cl_bugs"><a class="bug_action" href="#"><span class="small-bug"></span> <?php _e('Report a Bug','cl_text'); ?></a></li>
      </ul>
</nav>
<div id="help-panel">
<div class="tabs">
<ul>
<li><a target="_blank" href="https://docs.clink.media/">Documentation</a></li><li><a target="_blank" href="https://wordpress.org/support/plugin/wpclink">Get Support</a></li></ul>
</div>
<div class="content-tab">
<div id="faq-page"></div>
<div id="support-page"></div>
</div>
</div><?php if($_GET['page'] == 'content_link_post.php' || $_GET['page'] == 'content_link_page.php' || $_GET['page'] == 'content_link_media.php') { ?>
<div class="small-tabs">
<a class="cl_subtabs <?php if($_GET['page'] == 'content_link_post.php') echo "active"; ?>" href="<?php echo menu_page_url( 'content_link_post.php', false );?>"> <?php _e('Posts','cl_text'); ?></a><a class="cl_subtabs <?php if($_GET['page'] == 'content_link_media.php') echo "active"; ?>" href="<?php echo menu_page_url( 'content_link_media.php', false );?>"> <?php _e('Media','cl_text'); ?></a><a class="cl_subtabs <?php if($_GET['page'] == 'content_link_page.php') echo "active"; ?>" href="<?php echo menu_page_url( 'content_link_page.php', false );?>"> <?php _e('Pages','cl_text'); ?></a> 
 </div><?php	
}	
}
/**
 * Clink Tab Menu for Preferences Page
 * 
 */
function wpclink_display_tabs_preferences_general(){
	// Current
	$current_user_id = get_current_user_id();
	// Party
	$clink_party_id = wpclink_get_option('authorized_contact');
	// Creator
	$creator_array = wpclink_get_option('authorized_creators');
	
	// Copyright Owner
	$right_holder = wpclink_get_option('rights_holder');
	
	
	if(wpclink_user_has_creator_list($current_user_id)){
		$right_holder_id = $current_user_id;
	}
	
?>
<h1 class="wp-heading-inline"><?php echo get_admin_page_title(); ?></h1>
<div id="logo-mark"><img src="<?php echo plugins_url( 'admin/images/clink-logo.svg', dirname(__FILE__) ) ; ?>"></div>
<nav class="cl_menu blue">
  <ul class="menu">
<?php if(current_user_can('administrator') || $current_user_id == $clink_party_id || current_user_can('editor')  ){ ?>
    <li class="link <?php if($_GET['page'] == 'cl_mainpage.php') echo "active"; ?>"><a href="<?php echo menu_page_url( 'cl_mainpage.php', false );?>"><?php _e('General','cl_text'); ?></a></li>
    <?php } ?>
	
 <?php if(current_user_can('administrator') || $current_user_id == $clink_party_id || current_user_can('editor')  ){ ?>
 <li class="link <?php if($_GET['page'] == 'cl_users') echo "active"; ?>">
 <a href="<?php echo menu_page_url( 'cl_users', false );?>"><?php _e('Users','cl_text'); ?></a>
 </li>
 <?php } ?>  
 <?php
 
 if(wpclink_both_mode() || wpclink_export_mode()){ 
  if(wpclink_user_has_creator_list($current_user_id) || current_user_can('administrator') || $clink_party_id ==  $current_user_id){ ?>
    <li class="link <?php if($_GET['page'] == 'cl_templates') echo "active"; ?>"><a href="<?php echo menu_page_url( 'cl_templates', false );?>"><?php _e('License Templates','cl_text'); ?></a></li>
    <?php } 
 } ?> 
<li class="link cl_support"><a href="#"><span class="dashicons dashicons-info"></span> <?php _e('Need Help ?','cl_text'); ?></a></li>
<li class="link cl_bugs"><a class="feedback_action"  href="#"><span class="small-feedback"></span> <?php _e('Provide Feedback','cl_text'); ?></a></li>
<li class="link cl_bugs"><a class="bug_action"  href="#"><span class="small-bug"></span> <?php _e('Report a Bug','cl_text'); ?></a></li>
      </ul>
</nav>
<div id="help-panel">
<div class="tabs">
<ul>
<li><a target="_blank" href="https://docs.clink.media/wordpress-plugins/">Documentation</a></li><li><a target="_blank" href="https://wordpress.org/support/plugin/wpclink">Get Support</a></li></ul>
</div>
<div class="content-tab">
<div id="faq-page"></div>
<div id="support-page"></div>
</div>
</div>
<?php	
}