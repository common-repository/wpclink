<?php
/**
 * CLink Add New Offer
 *
 * CLink add new license offer admin page
 *
 * @package CLink
 * @subpackage System
 */
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Register CLink Agreement Sign Admin Page Menu
add_action('admin_menu', 'wpclink_register_license_offer_menu');
/**
 * CLink Already Token Check
 *
 * @return boolean 
 */
function wpclink_already_token(){
	
		$reuse_response = wpclink_reuse_show_license();
		$token_url = $reuse_response['token'];
		$token_url_part = parse_url($token_url);
		parse_str($token_url_part['query'], $token_url_part);
		$token_now = $token_url_part['token'];
	
		$token_array = wpclink_get_all_license_metas();
		if(in_array($token_now,$token_array)){
			return true;
		}else{
			return false;
		}
	
}
/**
 * CLink Site Agreement Sign Admin Menu
 * 
 */
function wpclink_register_license_offer_menu(){
	
	// Users
	$creator_array = wpclink_get_option('authorized_creators');
	$current_user_id = get_current_user_id();
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	if(wpclink_user_has_creator_list($current_user_id)  || current_user_can('administrator')){
		if(wpclink_import_mode() || wpclink_both_mode()){
			
		if(wpclink_is_acl_r_page()){
			// Nothing
		}else{
		add_submenu_page('cl_mainpage.php','Links','Links','edit_posts','cl-clink','wpclink_display_license_offer');
		}
		}
		
	}
}
/**
 * CLink Reuse Show Licence
 * 
 * CLink reuse content license show to screen
 *
 * @return response array
 */
function wpclink_reuse_show_license(){
	
	 // Current Site 
	 $site_url = get_bloginfo('url');
	
	  $domain_access_key = wpclink_get_option('domain_access_key');
	
	  // Encode
	  $site_url_ready = urlencode($site_url);
	
		
	  global $wp_version;
	  $args = array(
		  'redirection' => 5,
		  'httpversion' => '1.0',
		  'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
		  'blocking'    => true,
		  'headers'     => array(),
		  'cookies'     => array(),
		  'body'        => null,
		  'compress'    => false,
		  'decompress'  => false,
		  'sslverify'   => false,
		  'stream'      => false,
		  'timeout'     => 60,
		  'filename'    => null
	  ); 
	  $response = wp_remote_get( WPCLINK_CARE_SERVER.'/?get_token=1&weburl='.$site_url_ready.'&domain_access_key='.$domain_access_key,$args);
 
	if ( is_array( $response ) ) {
		$body = $response['body']; // use the content
		$reuse_response = json_decode($body,true);
		if($reuse_response['response'] == 'success'){
			return $reuse_response;
		}else if($reuse_response['response'] == 'error'){
			return $reuse_response;
		}
	}
  
  return false;
}
/**
 * CLink Reuse Expire License
 * 
 * CLink reuse expire license on certain period of time
 *
 * @return reuse_response array
 */
function wpclink_reuse_expire(){
	
	  // Current Site 
	  $site_url = get_bloginfo('url');
	
	  $domain_access_key = wpclink_get_option('domain_access_key');
	
	  // Encode
	  $site_url_ready = urlencode($site_url);
	
		
	  global $wp_version;
	  $args = array(
		  'redirection' => 5,
		  'httpversion' => '1.0',
		  'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
		  'blocking'    => true,
		  'headers'     => array(),
		  'cookies'     => array(),
		  'body'        => null,
		  'compress'    => false,
		  'decompress'  => false,
		  'sslverify'   => false,
		  'timeout'     => 60,
		  'stream'      => false,
		  'filename'    => null
	  ); 
	  $response = wp_remote_get( WPCLINK_CARE_SERVER.'/?expire_token=1&weburl='.$site_url_ready.'&domain_ID='.$domain_access_key,$args);
 
	if ( is_array( $response ) ) {
		$body = $response['body']; // use the content
		$reuse_response = json_decode($body,true);
		if($reuse_response['response'] == 'done'){
			return $reuse_response;
		}else if($reuse_response['response'] == 'error'){
			return $reuse_response;
		}
	}
  
  return false;
}
/**
 * CLink Sign Agreement Admin Page Render
 * 
 */
function wpclink_display_license_offer(){
	
	global $wpdb;

	// Get Back Variables;
	global $wpclink_offer_updated;
	$updated_me = $wpclink_offer_updated;
	
	global $wpclink_offer_fail_token;
	$fail_token = $wpclink_offer_fail_token;
	
	global $wpclink_offer_domain_id_missing;
	$domain_id_missing = $wpclink_offer_domain_id_missing;
	
	global $wpclink_offer_license_display_expired;
	$license_display_expired = $wpclink_offer_license_display_expired;
	
	global $wpclink_offer_connect_site;
	$cl_connect_site = $wpclink_offer_connect_site;
	
	global $wpclink_offer_license_display;
	$license_display = $wpclink_offer_license_display;
	
	global $wpclink_paid_offer;
	$paid_offer = $wpclink_paid_offer;
	
	
	$cl_connect_site = wpclink_get_option('reuse_connect_date');
	$query_parts = parse_url($cl_connect_site);
	
	if(isset($query_parts['query'])){
		$query_parts_query = $query_parts['query'];
	}else{
		$query_parts_query = '';
	}
	parse_str($query_parts_query, $query_parts);
	if(isset($query_parts['token'])){
		$saved_token = $query_parts['token'];
	}else{
		$saved_token = '';
	}
	
	
	var_dump($GLOBALS['wpclink_offer_connect_site']);
	
	// Debug
	//var_dump($cl_connect_site);
	?>
<div class="wrap agreement_area">
<?php wpclink_display_tabs_links(); ?>
  <div class="cl_subtabs_box">
  
  <?php if ($updated_me ) : ?>
  <div class="updated fade">
    <p><strong>
      <?php _e( 'Link is added and activated', 'cl_text' ); ?>
      </strong></p>
  </div>
  <?php endif; ?>
  <?php if(isset($fail_token)){ ?><div class="updated error"><p><?php _e('Link cannot be formed with the referent content on the same site','cl_text'); ?></p></div><?php } ?>
  <?php if(isset($_GET['deleted_token_url'])){ ?>
   <div class="updated fade">
    <p><?php _e( 'Link <strong>'.urldecode($_GET['deleted_token_url']).'</strong> has been removed', 'cl_text' ); ?></p>
  </div>
  <?php } ?>
  <?php 
	
	// Expired Token
	if(wpclink_already_token()){ ?>
		 <center>
<h2 class="clhstyle1"><?php _e('License Already Exists','cl_text'); ?></h2>
<p class="description clshstyle1"><?php _e('License already exists, please try another license','cl_text'); ?></p>
	  </center>
	  
	<?php }else if($domain_id_missing == 1){ ?>
	  <center>
<h2 class="clhstyle1"><?php _e('Error!','cl_text'); ?></h2>
<p class="description clshstyle1"><?php _e('wpCLink features are not available, because the domain access key of your site cannot be verified. Please <a href="#" class="wpclink-support">contact</a> support.','cl_text'); ?></p>
	  </center>
	  
	<?php }else if($license_display_expired == 1){ ?>
	  <center>
<h2 class="clhstyle1"><?php _e('License Expired','cl_text'); ?></h2>
<p class="description clshstyle1"><?php _e('You do not accepted the license within 5 minutes. Please reuse process again.','cl_text'); ?></p>
	  </center>
		
	<?php }else if(isset($cl_connect_site) and !empty($cl_connect_site)){
	  
	$cl_agree_sign = wpclink_get_option( 'wpclink_agree_sign' );
	  
	$request_query = array();
	$build_query = build_query( $request_query );
	
	/* ====== QUERY REQUEST ======= */
	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	$xml=file_get_contents($cl_connect_site,false,$context);
	$xml = simplexml_load_string($xml);
	// Agreement
	$agreement = (string)$xml->channel->agreement;
	// Esign
	$esign_copyrigh_owner = (string)$xml->channel->esign_right_holder;
	$esign_reason = (string)$xml->channel->esign_reason;
	$esign_time = (string)$xml->channel->esign_time;
	$esign_email = (string)$xml->channel->esign_email;
	$esign_copyright_identifier = (string)$xml->channel->esign_copyright_identifier;
	
	
	
				// Current User
				$current_user = wp_get_current_user();
				$linked_firstname = $current_user->user_firstname;
				$linked_lastname =  $current_user->user_lastname;
				$linked_displayname = $current_user->display_name;
				$linked_email = $current_user->user_email;
				$linked_creator_identifier = get_user_meta($current_user->ID,'wpclink_party_ID',true);
				
				
				// License Blanks
				$search = array('[client_site]',' [linked_creator_firstname]','[linked_creator_lastname]','[linked_creator_display_name]','[linked_creator_ID]','[linked_creator_email]','[path]');
				$replace = array(get_bloginfo('url'),$linked_firstname,$linked_lastname,$linked_displayname,wpclink_do_icon($linked_creator_identifier),$linked_email,plugin_dir_url( __FILE__ ));
				
				$agreement = str_replace($search,$replace,$agreement);
	
	// License Class
	$license_class = (string)$xml->channel->license_class;
	if($license_class == 'personal'){
		$license_class_label = 'Personal';
	}
	
	// Permission
	$taxonomy_permission = (string)$xml->channel->post_taxonomy_permission;
		
		
	// Content Type
	$request_content_type = (string)$xml->channel->request_content_type;
	
	$get_response = $xml->channel->response;
	if($get_response == 'invalid'){
			echo '<div class="updated error"><p><strong>Site Token is Expired</strong></p></div>';
			
			// Flush
			wpclink_delete_option( 'reuse_connect_date' );
			wpclink_delete_option( 'wpclink_agree_sign' );
			
			echo '<a class="button-primary" href="'.menu_page_url( 'cl-clink', false ).'"">Back</a>';
	}else{	  
	   ?>

  <form method="post" action="">
    <?php settings_fields( 'cl_agreement_options' ); ?>
	  
	<?php 
	
	$esign_display = wpclink_print_esign_linked($esign_copyrigh_owner,$esign_reason,$esign_time,$esign_copyright_identifier);
	$watermark_image = esc_url(plugins_url( 'admin/images/clinks-logo-alp.jpg', dirname(__FILE__) ) );
		
	$license_offer_template = '<h2 class="clhstyle1">'.__('License Offer','cl_text').'</h2><p class="description clshstyle1">'.__('You must accept the License Offer to reuse this content. Checkbox to accept is at the bottom of this page','cl_text').'</p><div class="outer_agreement"><div id="cl_show_agreement">'.nl2br(wpclink_do_icon($agreement)).str_replace('[esign_watermark]',$watermark_image,wpclink_do_icon($esign_display) ).'</div></div>';
		
	$license_offer_template_return = apply_filters( 'wpclink_license_offer', $license_offer_template, $paid_offer );	
		
	echo $license_offer_template_return; ?>
		
	
    <input type="hidden" value="<?php echo htmlspecialchars($agreement.wpclink_print_esign_linked($esign_copyrigh_owner,$esign_reason,$esign_time,$esign_copyright_identifier)); ?>" name="cl_agreement_content" />
    <input type="hidden" name="esign_html" value="" />
	<input type="hidden" name="requested_content_type" value="<?php echo $request_content_type; ?>">
    <input type="hidden" name="esign_right_holder" value="<?php echo $esign_copyrigh_owner; ?>" />
    <input type="hidden" name="esign_reason" value="<?php echo $esign_reason; ?>" />
    <input type="hidden" name="esign_time" value="<?php echo $esign_time; ?>" />
    <input type="hidden" name="esign_email" value="<?php echo $esign_email; ?>" />
    <input type="hidden" name="esign_copyright_identifier" value="<?php echo $esign_copyright_identifier; ?>" />
    
    
	<?php if($paid_offer == 1){ ?>
	  <input type="hidden" class="cl_agree_sign" name="cl_agree_sign" value="1"  />
	<?php }else{ ?>
	  <h3>License Class: <strong style="color:#0073aa"><?php echo $license_class_label; ?></strong></h3>
    <div id="cl_agreement_checkbox">
      <label>
        <input type="checkbox" class="cl_agree_sign" name="cl_agree_sign" value="1" <?php checked( $cl_agree_sign, 1 ); ?> />
        <?php _e('I’ve read the License and accept it with its terms and conditions','cl_text'); ?>
      </label>
    </div>
	<p class="submit">
    <a href="<?php echo menu_page_url( 'cl-clink', false ); ?>&delete_token=1" class="button">Delete</a>
      <input id="connect_btn" type="submit" class="button-primary" disabled="disabled" value="<?php _e( 'Link', 'cl_text' ); ?>" />
    </p>
	<?php } ?>
    
  </form>
<div class="loading-circle-quick" style="display: none"><div class="loading-wrapper"><span class="cl_loader"></span></div></div>
  <script>
	jQuery(".cl_agree_sign").change(function() {
		if(this.checked) {
	
	
	// Save the accept datetime 
	jQuery.ajax({
        url: ajaxurl, 
        data: {
            'action': 'wpclink_insert_date_accept_licensee',
            'record' : 'now'
		},
        success:function(data) {
            jQuery( "#connect_btn" ).prop( "disabled", false );
			
			jQuery("#connect_btn").click(function(){
				jQuery(".loading-circle-quick").show();
			});
			
        },
        error: function(errorThrown){
            console.log(errorThrown);
        },
    });  
			
		}else{
			jQuery( "#connect_btn" ).prop( "disabled", true );
		}
	});
	  
	  $(document).ready(function () {
    // Warning
    $(window).on('beforeunload', function(){
        return "Any changes will be lost";
    });
		  
    // Form Submit
    $(document).on("submit", "form", function(event){
        // disable warning
        $(window).off('beforeunload');
    });
});
	  
	  
	 idleTimer = null;
idleState = false;
idleWait = 270000;
(function ($) {
    $(document).ready(function () {
        $('*').bind('mousemove keydown scroll', function () {
        
            clearTimeout(idleTimer);
                    
            if (idleState == true) { 
                
                // Reactivated event
                //$("body").append("<p>Welcome Back.</p>");            
            }
            
            idleState = false;
            
            idleTimer = setTimeout(function () { 
                
                alert('No Activity, are you still there?');
				
				location.reload();
                idleState = true; }, idleWait);
        });
        
        $("body").trigger("mousemove");
    
    });
}) (jQuery)
  
	</script>
  <?php } ?>
  <?php }else if(isset($license_display) and $license_display == 'notfound'){ ?>
<center>
	<h2 class="clhstyle1"><?php _e('No Reuse Request Found','cl_text'); ?></h2>
	<p class="description clshstyle1"><?php _e('Please refresh the screen, if you add the reuse','cl_text'); ?></p>
</center>
	<?php } ?>
</div>
	<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
</div>
<?php }