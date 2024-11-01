<?php
/**
 * CLink Content Registered Pages
 *
 * CLink content registered pages admin page
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Register clink referent page list to admin menu function
add_action('admin_menu', 'wpclink_register_creations_referent_pages_menu');
/**
 * CLink Referent List Page Admin Menu
 * 
 */
function wpclink_register_creations_referent_pages_menu() {
  
	$creator_array = wpclink_get_option('authorized_creators');
	$current_user_id = get_current_user_id();
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	if(wpclink_user_has_creator_list($current_user_id)){
	
	if(wpclink_export_mode() || wpclink_both_mode()){
		add_submenu_page( 'cl_mainpage.php', 'Creations','Creations','edit_posts', 'cl-restriction-page', 'wpclink_display_creations_referent_pages');
		add_submenu_page( 'cl_mainpage.php', 'Creations','Creations','edit_posts', 'cl-restriction-page-available', 'wpclink_display_creations_registered_pages');
	}
	
	}elseif($current_user_id == $clink_party_id){
		if(wpclink_export_mode() || wpclink_both_mode()){
		add_submenu_page( 'cl_mainpage.php', 'Creations','Creations','edit_posts', 'cl-restriction-page', 'wpclink_display_creations_referent_pages');
		add_submenu_page( 'cl_mainpage.php', 'Creations','Creations','edit_posts', 'cl-restriction-page-available', 'wpclink_display_creations_registered_pages');
	}	
	}else{
	if(wpclink_export_mode() || wpclink_both_mode()){
		add_submenu_page( 'cl_mainpage.php', 'Creations','Creations','manage_options', 'cl-restriction-page', 'wpclink_display_creations_referent_pages');
		add_submenu_page( 'cl_mainpage.php', 'Creations','Creations','manage_options', 'cl-restriction-page-available', 'wpclink_display_creations_registered_pages');
	}
		
	}
}
 /**
  * CLink Register Page List Admin Page Render
  * 
  */
function wpclink_display_creations_registered_pages() { 
	$current_user_id = get_current_user_id();
	
		
	
	$updated = (isset($_GET['updated_post'])) ? $_GET['updated_post'] : '';
	$removed = (isset($_GET['removed_post'])) ? $_GET['removed_post'] : '';
	
	if($updated == '1'){
		wpclink_notif_print('Page(s) is/are added into referent list','success');
	}elseif($removed == '1'){
		wpclink_notif_print('Page(s) is/are removed from referent list','success');
	}
?>
<div id="cl_main" class="wrap all_list_post">
  <?php wpclink_display_tabs_creations(); ?>
    <?php settings_fields( 'cl_rest_options' ); ?>
    <?php 
if(isset($_GET['order']) and $_GET['order'] == 'asc'){
	$order = 'desc';	
}else{
	$order = 'asc';	
}
?>
    <ul class="wp-tab-bar">
      <li><a href="<?php echo menu_page_url( 'cl-restriction', false ); ?>">Posts</a></li>
	<li><a href="<?php echo menu_page_url( 'wpclink-creation-media', false ); ?>">Media</a></li>
      <li class="wp-tab-active"><a href="#tabs-2">Pages</a></li>      
    </ul>
    <div class="wp-tab-panel" id="tabs-1">
  <?php
  $current_page = menu_page_url( 'cl-restriction-page-available', false );
  $restrict_list = wpclink_get_option('referent_pages');
  $exclude_referent_list_page = wpclink_get_option('exclude_referent_page');
$paged = ( isset($_GET['paged']) ) ? abs($_GET['paged'] ) : 1;
$args = array('post_type' => 'page','paged' => $paged, 'posts_per_page' => 10);
if($exclude_referent_list_page == "1"){
	$args['post__not_in'] = $restrict_list;
}else{
	
}
$args['meta_query']	= array(
        'relation' => 'AND',
        array(
            'key' => 'wpclink_creation_ID',
            'value' => ' ',
            'compare' => '!='
        ),
        array(
            'key' => 'wpclink_referent_creation_ID',
            'compare' => 'NOT EXISTS'
        ));
if(isset($_GET['order'])) $args['order'] = $_GET['order'];
if(isset($_GET['orderby'])) $args['orderby'] = $_GET['orderby'];
  
$the_query = new WP_Query( $args ); ?>
<div class="include_form">
    <form id="cl_referentlist_form_page" method="post">
      <input name="cl_include_ref_form_page" class="page" value="1" type="hidden">
      <label><input id="referent_list_page" type="checkbox" value="1" <?php checked( $exclude_referent_list_page, '1', true ); ?> name="referent_list_page" /> <?php _e('Exclude Referent Pages','cl_text'); ?></label>
    </form>
    </div>
  
  <div class="tablenav-pages">
      <span class="pagination-links">
      <?php 	  
$big = 999999999; // need an unlikely integer
echo paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format' => '?paged=%#%',
	'current' => max( 1,(isset($_GET['paged'])) ? $_GET['paged'] : '' ),
	'total' => $the_query->max_num_pages,
	'prev_text'          => __('«'),
	'next_text'          => __('»'),
) ); 
?>
</span>
</div>
  <form id="posts-filter" method="get">
  <input name="post_type" class="post_type_page" value="post" type="hidden">
  <input name="page" class="page" value="cl-restriction-page-available" type="hidden">
  <input name="paged" class="paged" value="<?php echo $paged ?>" type="hidden">
  <div class="top">
        <div class="alignleft actions bulkactions">
          <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
          <select name="action" id="bulk-action-selector-top">
            <option value="-1">Bulk Actions</option>
            <option value="make_referent" class="hide-if-no-js">Make Referent</option>
          </select>
          <input class="button action doaction_bulks top" value="Apply" type="button">
        </div>
      </div>
	  
   <div id="cl-bulk-selection" class="cl_bulk_popup" style="display:none;">
		<span class="dashicons dashicons-no-alt close-pbox"></span>
		<div class="inner">
		<h3>License</h3>
<h5 class="view_lstype"><a href="<?php echo menu_page_url( 'cl_templates', false ); ?>"><?php echo "View License Template"; ?></a></h5>
<input type="hidden" id="taxonomy_permission" name="taxonomy_permission_slot" value="non-editable" />
<p><?php _e('Selected page to be published with Personal License','cl_text'); ?></p>		
		
        <input type="hidden" value="cl-restriction-page-available" id="page_name" name="page" />
		<input type="hidden" name="license_class" id="license_class"  value="personal" />
        <p>
        <input id="doaction" class="button button-primary" value="Next" type="submit">
        </div>
       </div>
  <table class="wp-list-table widefat fixed striped posts">
    <thead>
      <tr>
        <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label>
          <input id="cb-select-all-1" type="checkbox"></td>
        <th scope="col" id="title" class="manage-column column-title column-primary sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order; ?>"><span>Title</span><span class="sorting-indicator"></span></a></th>
        <th scope="col" id="author" class="manage-column column-author">Creator</th>
        
        <th scope="col" id="date" class="manage-column column-date sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order.'&orderby=date'; ?>"><span>Date</span><span class="sorting-indicator"></span></a></th>
        <th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24"></span></th>
        
      </tr>
    </thead>
    <tbody id="the-list">
    <?php if ( $the_query->have_posts() ) : ?>
    <?php while ( $the_query->have_posts() ) : $the_query->the_post();
	$post_id = get_the_ID();
	// License Class
	$license_class = get_post_meta($post_id,'wpclink_creation_license_class',true); ?>
    <?php if(in_array($post_id,$restrict_list)){ ?>
      <tr id="post-<?php echo $post_id; ?>" class="iedit author-self level-0 post-<?php echo $post_id; ?> type-post status-draft format-standard hentry category-uncategorized sync">
        <th scope="row" class="check-column"> <label class="screen-reader-text" for="cb-select-<?php echo $post_id; ?>">Select JF Test on buttons</label>
          <input id="cb-select-930" name="post[]" value="<?php echo $post_id; ?>" type="checkbox" disabled="disabled">
          <div class="locked-indicator"> <span class="locked-indicator-icon" aria-hidden="true"></span> <span class="screen-reader-text">“JF Test on buttons” is locked</span> </div>
        </th>
        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
          <strong><a href="<?php echo 'post.php?post='.$post_id.'&action=edit'; ?>" class="row-title"><?php the_title(); ?></a></strong>
           <?php if(wpclink_user_has_creator_list($current_user_id)){?>
           <?php if(wpclink_is_licensed_by_post_id($post_id)){ ?>
          <div class="row-actions"><span class="trash"><a href="<?php print wp_nonce_url($current_page.'&remove_referent='.$post_id, 'remove_referent_nonce', 'nonces'); ?>" rel="bookmark"><?php _e('Remove Referent'); ?></a></span></div>
          <?php } ?>
          <?php }else{ ?>
          <div class="row-actions"><span class="trash"><?php _e('Remove Referent'); ?></span></div>
          <?php } ?>
          </td>
          </td>
        <td class="author column-author" data-colname="Author">
        <?php $author_id =  get_post_field( 'post_author', $post_id );
			$user_info_author = get_userdata($author_id);
		echo $user_info_author->display_name; ?>
        </td>
       <td class="date column-date" data-colname="Date">  Published <br  /><abbr><?php echo get_the_date( 'Y/m/d', $post_id );?></abbr><br /></td>
        <td class="clink_id column-clink_id" data-colname=""><?php
        $contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	if ( !empty( $contentID ) ) {
		echo '<a href="'.WPCLINK_ID_URL.'/#objects/'.$contentID.'" target="_blank">'.wpclink_strip_prefix_clink_ID($contentID).'</a>'.get_post_mime_type( $post_id );
	}else{
		echo 'N/A';	
	}
		?></td>
      </tr>
    <?php }else{ ?>
    <tr id="post-<?php echo $post_id; ?>" class="iedit author-self level-0 post-<?php echo $post_id; ?> type-post status-draft format-standard hentry category-uncategorized">
        <th scope="row" class="check-column"> <label class="screen-reader-text" for="cb-select-<?php echo $post_id; ?>">Select Test on buttons</label>
          <input id="cb-select-930" name="post[]" value="<?php echo $post_id; ?>" type="checkbox">
          <div class="locked-indicator"> <span class="locked-indicator-icon" aria-hidden="true"></span> <span class="screen-reader-text">“JF Test on buttons” is locked</span> </div>
        </th>
        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
          <strong><a href="<?php echo 'post.php?post='.$post_id.'&action=edit'; ?>" class="row-title"><?php the_title(); ?></a></strong>
          <?php if(wpclink_user_has_creator_list($current_user_id)){ ?>
          <div class="row-actions"><span class="view">
          <a class="make-ref" data-postid="<?php echo $post_id; ?>" rel="bookmark"><?php _e('Make Referent'); ?></a>
          </span></div>
          <?php }else{ ?>
           <div class="row-actions"><span class="view">Make Referent</span></div>
          <?php } ?>
          </td>
        <td class="author column-author" data-colname="Author">
        <?php $author_id =  get_post_field( 'post_author', $post_id );
			$user_info_author = get_userdata($author_id);
		echo $user_info_author->display_name; ?>
        </td>
        <td class="date column-date" data-colname="Date">  Published <br  /><abbr><?php echo get_the_date( 'Y/m/d', $post_id );?></abbr><br /></td>
        <td class="clink_id column-clink_id" data-colname=""><?php
        $contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
	if ( !empty( $contentID ) ) {
		echo '<a href="'.WPCLINK_ID_URL.'/#objects/'.$contentID.'" target="_blank">'.wpclink_strip_prefix_clink_ID($contentID).'</a>'.get_post_mime_type( $post_id );
	}else{
		echo 'N/A';	
	}
		?></td>
      </tr>
    <?php } ?>
    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
    <?php else : ?>
    <tr><td colspan="5"><?php esc_html_e( 'Sorry, no page is registered in CLink.ID.' ); ?></td></tr>
<?php endif; ?>
    </tbody>
    <tfoot>
      <tr>
        <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label>
          <input id="cb-select-all-2" type="checkbox"></td>
        <th scope="col" class="manage-column column-title column-primary sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order; ?>"><span>Title</span><span class="sorting-indicator"></span></a></th>
        <th scope="col" class="manage-column column-author">Creator</th>
          
        <th scope="col" class="manage-column column-date sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order.'&orderby=date'; ?>"><span>Date</span><span class="sorting-indicator"></span></a></th>
        <th scope="col" class="manage-column column-clink_id"><span class="clinkico-24"></span></th>
        </tr>
    </tfoot>
  </table>
  <div class="alignleft actions bulkactions">
        <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
        <select name="action2" id="bulk-action-selector-bottom">
          <option value="-1">Bulk Actions</option>
          <option value="make_referent" class="hide-if-no-js">Make Referent</option>
        </select>
        <input id="doaction2" class="button action doaction_bulks bottom" value="Apply" type="button">
      </div>
  <div class="tablenav-pages">
      <span class="pagination-links">
      <?php 	  
$big = 999999999; // need an unlikely integer
echo paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format' => '?paged=%#%',
	'current' => max( 1,(isset($_GET['paged'])) ? $_GET['paged'] : '' ),
	'total' => $the_query->max_num_pages,
	'prev_text'          => __('«'),
	'next_text'          => __('»'),
) ); 
?>
</span>
</div>
  </form>
  <script>
	jQuery( ".license_class_form" ).change(function() {
		var selected_val = jQuery( this ).val();
		var postid = jQuery(this).data('postid');
	
	jQuery.ajax({
        url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
        data: {
            'action': 'wpclink_save_license_class',
            'post_id' : postid,
			'license_class' : selected_val
		},
        success:function(data) {
            // This outputs the result of the ajax request
			
			if(data >= 1){
				jQuery('#post-'+postid).addClass( "updated" );
				jQuery('#post-'+postid).children( ".page-title" ).append('<p class="lctype">License Class Updated!</p>');
			}
			
        },
        error: function(errorThrown){
            console.log(errorThrown);
        },
    });  
	
	
	});
	
	// Make Referent Popup
	jQuery('.make-ref').click(function(){
		var postid = jQuery(this).data('postid');
		jQuery('#cl-license-selection').show(200);
		jQuery('#referent_id').val(postid);
		
		
		
		jQuery('.close-pbox').click(function(){
			jQuery('#cl-license-selection').hide(200);
		});
		
		jQuery('.show-lc-btn').click(function(){
			jQuery('#cl-license-show').show(200);
			jQuery('#cl-license-show-box').show();
			
			jQuery('#cl-license-show .close-pbox').click(function(){
				jQuery('#cl-license-show').hide(200);
			});
			
		});
		
		// Make Referent Popup by click on button
		jQuery('.make-ref-btn').click(function(){
			var get_license_class = jQuery('#license_class').val();
			var get_referent_id = jQuery('#referent_id').val();
			var get_taxonomy_permission = jQuery('#taxonomy_permission').val();
			
			
			// Clean HTML
			jQuery.ajax({
				cache: false,
				url: ajaxurl,
				data: {
					'action': 'wpclink_make_ref_post_step2',
					'license_class' : get_license_class,
					'referent_id' : get_referent_id,
					'taxonomy_permission': get_taxonomy_permission
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
					
						jQuery('body').append(data);
						jQuery( "#cl-license-show-screen" ).dialog({maxWidth:"auto",maxHeight: 500,width: 800,height:500,fluid: true,closeOnEscape: false,close: function(event, ui){ jQuery(this).dialog('close');  jQuery(this).remove();} });
						jQuery("#cl-license-show-screen").scrollTop(0);
					
						jQuery('#cl-license-selection').hide();
					
						if(data == "0"){
							jQuery('#cl-license-selection_step2').show();
						}else{
							jQuery('#cl-license-selection_step2').hide();
						}
						jQuery("#accept_license_check").change(function() {
						if(this.checked) {
							jQuery( "#license_next" ).prop( "disabled", false );
						}else{
							jQuery( "#license_next" ).prop( "disabled", true );
						}
						});
						jQuery("#license_next").click(function(){
							jQuery(this).parents(".ui-dialog-content").dialog('close');
							jQuery("#cl-license-selection_step2").show(200);
						});
					
				}});
										
					jQuery('#cl-license-selection_step2 .close-pbox').click(function(){
						jQuery('#cl-license-selection_step2').hide(200);
					});
					
					
					// This outputs the result of the ajax request
					jQuery('#license_show_box').html(data);
					jQuery('#license_class_s2').val(get_license_class);
					jQuery('#referent_id_s2').val(get_referent_id);
					jQuery('#taxonomy_permission_slot').val(get_taxonomy_permission);
					
					
					jQuery('.cl_show_license').click(function(){
						jQuery('#cl_show_pre_auth').hide();
						jQuery('#cl_show_current_license').show();
					});
					
					jQuery('.back_to_pre').click(function(){
						jQuery('#cl_show_pre_auth').show();
						jQuery('#cl_show_current_license').hide();
					});
					
					
					jQuery('#cl_show_esign_pre .close-pbox').click(function(){
						jQuery('#cl_show_esign_pre').hide(200);
					});
					
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
			
			
		});
		
	});
	// Bulk Action Top
	jQuery('.doaction_bulks.top').click(function(){
		
		var bulk_value = jQuery('#bulk-action-selector-top').val();
		
		if(bulk_value == 'make_referent'){
			jQuery('.cl_bulk_popup').show(200);
			jQuery('.close-pbox').click(function(){
				jQuery('#cl-bulk-selection').hide(200);
			});
		}
	});
	// Bulk Action Bottom
	jQuery('.doaction_bulks.bottom').click(function(){
		
		var bulk_value = jQuery('#bulk-action-selector-bottom').val();
		
		if(bulk_value == 'make_referent'){
			jQuery('.cl_bulk_popup').show(200);
			jQuery('.close-pbox').click(function(){
				jQuery('#cl-bulk-selection').hide(200);
			});
		}
	});
	
	jQuery(document).ready(function(){
	    jQuery("#referent_list_page").change(function(){
        	jQuery("#cl_referentlist_form_page").submit();
    	});
		jQuery(document).tooltip();
	});
	</script>
   </div>
<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
</div>
<?php }