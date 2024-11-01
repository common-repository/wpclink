<?php
/**
 * CLink Script Functions 
 *
 * CLink javascript and css script functions
 *
 * @package CLink
 * @subpackage System
 */
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CLink Register the Scripts and Styles for Plugin Admin Pages
 * 
 */
function wpclink_admin_scripts() {
	
	// Screen
	$current_screen = get_current_screen(); 
	
	// Load on CLink Pages and WP Post and WP Page
    wp_register_style( 'cl-style', plugins_url( 'admin/css/main.css', dirname(__FILE__)));
	wp_enqueue_style( 'wpclink_admin_style', plugins_url('admin/css/admin.css?'.uniqid('_'),dirname(__FILE__)  ) );
	
	// Selection
	wp_enqueue_script( 'cl-selection', plugins_url('admin/js/select.js?'.uniqid('_'),dirname(__FILE__)  ),array( 'jquery' ) );
	
	
	if($current_screen->id == 'attachment'){
	
	 if(isset($_GET['post'])){
		 	$post_id = $_GET['post'];
		 
		 // Only Jpeg
		$media_type = get_post_mime_type($post_id);
		if($media_type == 'image/jpeg' || $media_type == 'image/jpg'){
			
			$continue_to_image = get_post_meta($post_id, 'wpclink_continue_to_image', true );
			if ( $continue_to_image == 1 ) {
			}else{
				
			 wp_localize_script('jquery', 'ajax', array(
				'url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('wpclink_select_license'),
			));
		
			// Attachment
			wp_enqueue_script( 'cl-attachment', plugins_url('admin/js/attachment.js?'.uniqid('_'),dirname(__FILE__)  ),array( 'jquery' ) );
                
            // Metadata Level 3 - CSS 
            wp_enqueue_style( 'cl-checkout', plugins_url('public/css/checkout.css?12456'.uniqid(), dirname(__FILE__) ) );

            // Metadata Level 3 - JS 
             wp_enqueue_script( 'cl-licensable-media-level3', plugins_url( 'public/js/licensable-level3.js?'.uniqid(), dirname(__FILE__)  )  , array('jquery'), '1.0.0', true ); 
                
            // Metadata Level 3 - JS Localize
            wp_localize_script('cl-licensable-media-level3', 'wpclink_vars', array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wpclink-ajax-nonce')
            ));
                
            // Metadata Level 3 Support CSS
            wp_enqueue_style('cl-jquery-ui-css', plugins_url('public/css/jquery-ui.css?12456'.uniqid(), dirname(__FILE__))   ,
                        false,
                        '1.0',
                        false);
            wp_enqueue_script( 'jquery-ui-resizable' );
				
			}

		}
		 
	 }
	
		// Draggable
		wp_enqueue_script("jquery-ui-draggable");
		
	}
	if($current_screen->id == 'admin_page_wpclink-matches-media'){
		
	// Lazy Load under GPL-2.0 license
	wp_enqueue_script( 'lozad', plugins_url('admin/js/jquery.lazy.min.js',dirname(__FILE__)), array( 'jquery' ), null, true );
	
		
		
	}
	
	
	
	// Read only for other users
	if(wpclink_is_acl_r_page()){
		wp_enqueue_script( 'cl-readonly', plugins_url('admin/js/readonly.js?'.uniqid('_'),dirname(__FILE__)  ),array( 'jquery' ) );
	}
	
	wp_enqueue_style( 'cl-style' );
	wp_enqueue_script( 'jquery-ui-tooltip' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_style( 'wp-jquery-ui-dialog' );
	wp_enqueue_script('jquery-ui-accordion');
	wp_enqueue_script( 'jquery-ui-autocomplete' );
	
	}
// Register clink scripts
add_action( 'admin_enqueue_scripts', 'wpclink_admin_scripts' );
/**
 * CLink Report a Bug Script 
 * 
 * @param string $hook 
 * 
 */
function wpclink_report_bug_script($hook) {
	
$current_page = (isset($_GET['page'])) ? $_GET['page'] : '';		
	
	
	// Load only following pages
	if(	$current_page == 'cl_mainpage.php' ||
		$current_page == 'cl_users' || 
		$current_page == 'cl_templates' ||
		$current_page == 'cl-restriction' ||
		$current_page == 'cl-restriction-page-available' ||
		$current_page == 'cl-restriction-reuse' ||
		$current_page == 'cl-restriction-page' ||
		$current_page == 'content_link_post.php' ||
		$current_page == 'content_link_page.php' ||
		$current_page == 'clink-links-outbound' ||
		$current_page == 'clink-links-inbound' ||
		$current_page == 'cl-canonical' ||
		$current_page == 'cl-audit-trail' ||
		$current_page == 'cl-clink' || 
	   $current_page ==  'wpclink_extensions' || 
	   $current_page ==  'wpclink_extensions_activation' ||
	   $current_page ==  'wpclink_extensions_presets' ||
	   $current_page ==  'wpclink_archive_settings' || 
	   $current_page ==  'wpclink_imatag_settings' || 
	   $current_page ==  'wpclink_ngg_settings' || 
	   	wpclink_is_domain_access_key_notexists()){
			
			// Report a Bug
			wp_enqueue_script( 'cl_report_bug', 'https://support.clink.media/s/d549cdcb3e46ecadefe9a4c027e1f949-T/-mpg7c2/805001/6411e0087192541a09d88223fb51a6a0/3.1.0/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs.js?locale=en-US&collectorId=3e58e4a0' );
			// Provide Feedback
			wp_enqueue_script( 'cl_provide_feedback', 'https://support.clink.media/s/d549cdcb3e46ecadefe9a4c027e1f949-T/-mpg7c2/805001/6411e0087192541a09d88223fb51a6a0/3.1.0/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs.js?locale=en-US&collectorId=54324eb7' );
		}
   
}
// Register clink report a bug scripts in admin enqueue
add_action( 'admin_enqueue_scripts', 'wpclink_report_bug_script' );
/**
 * CLink Report a Bug Script Call Function
 * 
 */
function wpclink_report_bug_script_call(){
	
	$current_page = (isset($_GET['page'])) ? $_GET['page'] : '';
	
	// Load only following pages
	if(	$current_page == 'cl_mainpage.php' ||
		$current_page == 'cl_users' || 
		$current_page == 'cl_templates' ||
		$current_page == 'cl-restriction' ||
		$current_page == 'cl-restriction-page-available' ||
		$current_page == 'cl-restriction-reuse' ||
		$current_page == 'cl-restriction-page' ||
		$current_page == 'content_link_post.php' ||
		$current_page == 'content_link_page.php' ||
		$current_page == 'clink-links-outbound' ||
		$current_page == 'clink-links-inbound' ||
		$current_page == 'cl-canonical' ||
		$current_page == 'cl-audit-trail' ||
		$current_page == 'cl-clink' || 
	    $current_page ==  'wpclink_extensions' || 
	   $current_page ==  'wpclink_extensions_activation' ||
	   $current_page ==  'wpclink_extensions_presets' ||
	   $current_page ==  'wpclink_archive_settings' || 
	   $current_page ==  'wpclink_imatag_settings' || 
	   $current_page ==  'wpclink_ngg_settings' || 
	    wpclink_is_domain_access_key_notexists()){
		
		if(wpclink_is_domain_access_key_notexists()){
			$script = 'jQuery(".wpclink-support").click(function(e){ e.preventDefault(); showCollectorDialog(); jQuery(".atlwdg-loading").html("<div class=\"loading-circle\"><div class=\"loading-wrapper\"><span class=\"cl_loader\"></span></div>");
    });';
		}else{
			$script = '';
		}
	
	echo '<script type="text/javascript">
	jQuery(document).ready(function() 
{
 window.ATL_JQ_PAGE_PROPS =  jQuery.extend(window.ATL_JQ_PAGE_PROPS,
 {
  // Report a Bug 
  "3e58e4a0":
  {        
   triggerFunction: function(showCollectorDialog)
   {
    jQuery(".bug_action").click(function(e) 
    {
     e.preventDefault();
     showCollectorDialog();
	 jQuery(".atlwdg-loading").html("<div class=\"loading-circle\"><div class=\"loading-wrapper\"><span class=\"cl_loader\"></span></div>");
    }); '.$script.'
   },
   
  },
  // Provide Feedback
  "54324eb7":
  {        
   triggerFunction: function(showCollectorDialog)
   {
    jQuery(".feedback_action").click(function(e) 
    {
     e.preventDefault();
     showCollectorDialog();
	  jQuery(".atlwdg-loading").html("<div class=\"loading-circle\"><div class=\"loading-wrapper\"><span class=\"cl_loader\"></span></div>");

    });
   },
  }
 });
});
</script>';
		}
}
// Register report a bug script call on admin footer
add_action('admin_footer','wpclink_report_bug_script_call');
/**
 * CLink Disabled Editing on the Referent Content Scripts
 * 
 */
function wpclink_disable_editing_referent_creation(){
	// Only Post and Pages
    if ( get_current_screen()->post_type === 'post' ||
		 get_current_screen()->post_type === 'page'
	 ) {
		 
		 if(isset($_GET['post'])){
			 if(is_numeric($_GET['post'])){
		 
			 // Post ID
			$post_id = $_GET['post'];
				
			
			
			if(wpclink_check_license_by_post_id($post_id) > 0){ ?>
				<style type="text/css">
					#poststuff #post-body-content,
					#poststuff input,
					#poststuff textarea,
					.editor-post-switch-to-draft,
					.edit-post-visual-editor{
						pointer-events : none;
						opacity: 0.5;
					}
					#poststuff #delete-action{
						display: none;
					}
					.edit-post-header-toolbar,
					.components-panel .plugin-sidebar-content-status,
					.components-panel .plugin-sidebar-content-version,
					.components-panel .editor-post-format,
					.components-panel .edit-post-post-schedule,
					.components-panel .editor-post-format__content,
					.components-panel .components-panel__row,
					.components-panel .components-button,
					.components-panel .editor-post-last-revision__title,
					.components-panel .edit-post-post-link__link,
					.components-panel .editor-post-taxonomies__hierarchical-terms-list,
					.components-panel .components-form-token-field__input,
					.components-panel .editor-post-featured-image__toggle,
					.components-panel .components-textarea-control__input,
					.components-panel .edit-post-post-link__preview-label,
					.components-panel .components-form-token-field,
					.components-panel .components-checkbox-control__label{
						pointer-events : none;
						opacity: 0.5;
					}
				</style>
				<script>
					jQuery(document).ready(function(){
						jQuery('#poststuff input[type="checkbox"]').attr('disabled',true);
		
					});
			</script>
				
			<?php }else if(wpclink_is_domain_access_key_notexists()){ 
				?>
				<style type="text/css">
					.generate_iscc,
					.make_version,
					#quick-license-type,
					.select-license{
						pointer-events : none;
						opacity: 0.5;
					}
				</style>
				
				
			<?php 
				
			}
			 }
		 }
	 }else if(get_current_screen()->post_type === 'attachment'){
		
		if(isset($_GET['post'])){
			 if(is_numeric($_GET['post'])){
		 
			 // Post ID
			$post_id = $_GET['post'];
				
			
			
			if(wpclink_check_license_by_post_id($post_id) > 0){ ?>
				<style type="text/css">
					
					#post-body #title,
					#post-body textarea,
					#post-body button,
					.compat-attachment-fields input,
					.editor-post-publish-button,
					.edit-post-visual-editor{
						pointer-events : none;
						opacity: 0.7;
					}
					.button.copy-attachment-url.edit-media{
						pointer-events:all !important;
						opacity:1 !important;
					}
					#delete-action{
						display: none;
					}
					
				</style>
				
				
			<?php }else if(wpclink_is_domain_access_key_notexists()){ ?>
				<style type="text/css">
					
					#post-body #title,
					#post-body input,
					#post-body textarea,
					#post-body button,
					#post-body .button,
					.editor-post-publish-button,
					.edit-post-visual-editor{
						pointer-events : none;
						opacity: 0.7;
					}
					.button.copy-attachment-url.edit-media{
						pointer-events:all !important;
						opacity:1 !important;
					}
					#delete-action{
						display: none;
					}
					
				</style>
				
				
			<?php }
			 }
		 }
		
	}
}
// Register the clink disabled editing on the referent content function
add_action('admin_footer','wpclink_disable_editing_referent_creation');
/**
 * CLink Metadata Level 3 Popup for Admin Area
 * 
 * @param string html
 * 
 */
function wpclink_metadata_level_3_popup_admin(){
    
    if(get_current_screen()->post_type === 'attachment'){
		
		if(isset($_GET['post'])){
			 if(is_numeric($_GET['post'])){
		 
			 // Post ID
			$post_id = $_GET['post'];
                 
                 
	// Popup
	echo '<div class="wpclink_metadata_popup_wrapper"><div class="wpclink_metadata_popup"></div><div class="wpclink_metadata_compare_popup"><div class="sidebar-left"></div><div class="compare_center"><div class="ingredients-side ui-widget-content"></div><div class="archive-side"></div></div><div class="sidebar-right"></div></div></div>';
                 
             }
        }
    }
}
add_action('admin_footer','wpclink_metadata_level_3_popup_admin');

/**
 * CLink Admin Style and Scripts
 * 
 * @param string $hook 
 * 
 */
function wpclink_admin_style($hook) {
        // Load only on ?page=mypluginname
        if($hook != 'cl-restriction') {
               // return;
        }
        wp_enqueue_style( 'wpclink_admin_style', plugins_url('admin/css/admin.css?'.uniqid('_'), dirname(__FILE__) ) );
	
	
		
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
}
// Register clink admin style and scripts
add_action( 'admin_enqueue_scripts', 'wpclink_admin_style' );
/**
 * CLink Compress Static Files
 * 
 * @param string file_url 
 * 
 */
function wpclink_compress_static_files($file_url = ''){
	
	if ( !defined( 'WPCLINK_ORIGN_FILE' ) ) {
		$find = array('.css','.js');
		$replace = array('.min.css','.min.js');
		$file_url_min = str_replace($find,$replace,$file_url);
		return add_query_arg('version',WPCLINK_VERSION,$file_url_min);
	}else{
		return add_query_arg('nocache',uniqid(),$file_url);
	}
	
}
/**
 * CLink Frontend Scripts
 * 
 */
function wpclink_do_popup_link_css_js() {
	
	// Only import mode and content is registerd on clink.id
	if(wpclink_import_mode() || !wpclink_is_post_referent()){
	
	}else{
		
    wp_enqueue_style( 'cl-modal-prepare-clinkid', plugins_url( 'public/css/'.wpclink_compress_static_files('modal.css'), dirname(__FILE__)  ) );
		// AMP
		if(wpclink_is_amp_inactive()){
			wp_localize_script( 'ajax-script', 'my_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    		wp_enqueue_script( 'cl-modal-script-clinkid', plugins_url( 'public/js/'.wpclink_compress_static_files('modal-clink-ID.js'), 
															  dirname(__FILE__)  ), array('jquery'), '1.0.0', true );
		}
	}
	
	// Reuse
	wp_enqueue_style( 'cl-modal-prepare', plugins_url('public/css/'.wpclink_compress_static_files('frontend.css'), dirname(__FILE__) ) );
	
	// Modal
	wp_enqueue_style( 'cl-modal-prepare-structure', plugins_url( 'public/css/'.wpclink_compress_static_files('modal.css'), dirname(__FILE__) ) );
	
	// AMP
	if(wpclink_is_amp_inactive()){
	
		// Licensable Media
		wp_enqueue_script( 'cl-licensable-media', plugins_url( 'public/js/'.wpclink_compress_static_files('licensable.js'), dirname(__FILE__)  ), array('jquery'), '1.0.0', true );

		wp_localize_script('cl-licensable-media', 'wpclink_vars', array(
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('wpclink-ajax-nonce')
		));
		
	}
	
	// Linked Content only
	if(wpclink_is_linked_post_published()){
		
		// AMP
		if(wpclink_is_amp_inactive()){
			
    		wp_enqueue_script( 'cl-modal-script-clinkid', plugins_url( 'public/js/'.wpclink_compress_static_files('modal-clink-ID.js'), 
															  dirname(__FILE__)  ), array('jquery'), '1.0.0', true );
		}
	}
	
}
// Refister clink reuse offer popup scripts function
add_action( 'wp_enqueue_scripts', 'wpclink_do_popup_link_css_js');