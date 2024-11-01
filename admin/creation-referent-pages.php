<?php
/**
 * CLink Content Referent Pages
 *
 * CLink content referent pages admin page.
 *
 * @package CLink
 * @subpackage System
 */
 /**
  * CLink Referent Page List Admin Page Render
  * 
  */
function wpclink_display_creations_referent_pages() {
	
	$current_user_id = get_current_user_id();

	
	
	$updated = (isset($_GET['updated_post'])) ? $_GET['updated_post'] : '';
	$removed = (isset($_GET['removed_post'])) ? $_GET['removed_post'] : '';
	$error = (isset($_GET['post_error'])) ? $_GET['post_error'] : '';
	
	
	// Notifcations 
	if($updated == '1'){
		wpclink_notif_print('Page(s) is/are added into referent list','success');
	}elseif($removed == '1'){
		wpclink_notif_print('Page(s) is/are removed from referent list','success');
	}elseif($error == '1'){
		
		if(isset($_GET['post_id'])){
			if($error_data = get_post_meta($_GET['post_id'],'wpclink_loader_error_data',true)){
				wpclink_notif_print($error_data['error_headline']. ' "'.$error_data['code'].' '.$error_data['message'].'" '.wp_strip_all_tags($error_data['error_text']),'error');

				delete_post_meta($_GET['post_id'],'wpclink_loader_status');
				delete_post_meta($_GET['post_id'],'wpclink_loader_error_data');
			}
		}
		
		
	}
	
	 ?>
<div id="cl_main" class="wrap all_list_post">
  <?php wpclink_display_tabs_creations(); ?>
    <?php settings_fields( 'cl_rest_options' ); ?>
    <ul class="wp-tab-bar">
      <li><a href="<?php echo menu_page_url( 'cl-restriction', false ); ?>">Posts</a></li>
		<li><a href="<?php echo menu_page_url( 'wpclink-creation-referent-media', false ); ?>"><?php _e('Media','cl_text'); ?></a></li>
      <li class="wp-tab-active"><a href="#tabs-2">Pages</a></li>
    </ul>
    <div class="wp-tab-panel" id="tabs-1">
  
  <?php
if(isset($_GET['order']) and $_GET['order'] == 'asc'){
	$order = 'desc';	
}else{
	$order = 'asc';	
}
  
  $current_page = menu_page_url( 'cl-restriction-page', false );
  if($restrict_list = wpclink_get_option('referent_pages')){
	  if(empty($restrict_list)){
		  $restrict_list = array('0');
	  }
  }else{
	 $restrict_list = array('0');
  }
$paged = ( isset($_GET['paged']) ) ? abs($_GET['paged'] ) : 1;
$args = array('post_type' => 'page','paged' => $paged, 'posts_per_page' => 10);
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
$args['post__in'] = $restrict_list;
if(isset($_GET['order'])) $args['order'] = $_GET['order'];
if(isset($_GET['orderby'])) $args['orderby'] = $_GET['orderby'];
  
  $the_query = new WP_Query( $args ); ?>
  
    
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
  <input name="page" class="page" value="cl-restriction-page" type="hidden">
  <input name="paged" class="paged" value="<?php echo $paged ?>" type="hidden">
  <div class="top">
				<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
<option value="-1">Bulk Actions</option>
	<option value="remove_referent" class="hide-if-no-js">Remove Referent</option>
</select>
<input id="doaction" class="button action" value="Apply" type="submit">
		</div>
   </div>
  <table class="wp-list-table widefat fixed striped posts">
    <thead>
      <tr>
        <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label>
          <input id="cb-select-all-1" type="checkbox"></td>
        <th scope="col" id="title" class="manage-column column-title column-primary sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order; ?>"><span>Title</span><span class="sorting-indicator"></span></a></th>
        <th scope="col" id="author" class="manage-column column-author">Creator</th>
        <th scope="col" id="license_class" class="manage-column column-type">License Class</th>
        <th scope="col" id="date" class="manage-column column-date sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order.'&orderby=date'; ?>"><span>Date</span><span class="sorting-indicator"></span></a></th>
        <th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24"></span></th>
        
      </tr>
    </thead>
    <tbody id="the-list">
    <?php if ( $the_query->have_posts() ) : ?>
    <?php while ( $the_query->have_posts() ) : $the_query->the_post();
	$post_id = get_the_ID();
	
	// License Class
	$license_class = get_post_meta($post_id,'wpclink_creation_license_class',true);
	 ?>
      <tr id="post-<?php echo $post_id; ?>" class="iedit referent_list author-self level-0 post-<?php echo $post_id; ?> type-post status-draft format-standard hentry category-uncategorized">
        <th scope="row" class="check-column"> <label class="screen-reader-text" for="cb-select-<?php echo $post_id; ?>">Select JF Test on buttons</label>
			 <?php if(wpclink_is_licensed_by_post_id($post_id)){ ?>
          <input id="cb-select-<?php echo $post_id; ?>"  name="post[]" value="<?php echo $post_id; ?>" type="checkbox">
			<?php }else{ ?>
		 <input id="cb-select-<?php echo $post_id; ?>" disabled name="post[]"  type="checkbox">
			<?php } ?>
          <div class="locked-indicator"> <span class="locked-indicator-icon" aria-hidden="true"></span> <span class="screen-reader-text">“<?php the_title(); ?>” is locked</span> </div>
        </th>
        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
          <strong><a href="<?php echo 'post.php?post='.$post_id.'&action=edit'; ?>" class="row-title"><?php the_title(); ?></a></strong>
          <?php if(wpclink_user_has_creator_list($current_user_id)){ ?>
           <?php if(wpclink_is_licensed_by_post_id($post_id)){ ?>
          <div class="row-actions"><span class="trash"><a href="<?php print wp_nonce_url($current_page.'&remove_referent='.$post_id, 'remove_referent_nonce', 'nonces'); ?>" rel="bookmark"><?php _e('Remove Referent'); ?></a></span></div>
          <?php }
		  }else{ ?><div class="row-actions"><span class="trash">Remove Referent</span></div><?php } ?>
          </td>
        <td class="author column-author" data-colname="Author">
        <?php $author_id =  get_post_field( 'post_author', $post_id );
			$user_info_author = get_userdata($author_id);
		echo $user_info_author->display_name; ?>
        </td>
       <td class="categories column-type" data-colname="Type"><?php echo wpclink_get_license_class_label($license_class); ?></td>
        
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
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php else : ?>
    <tr><td colspan="5"><?php esc_html_e( 'Sorry, no post is registered in CLink.ID.' ); ?></td></tr>
<?php endif; ?>
    </tbody>
    <tfoot>
      <tr>
        <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label>
          <input id="cb-select-all-2" type="checkbox"></td>
        <th scope="col" class="manage-column column-title column-primary sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order; ?>"><span>Title</span><span class="sorting-indicator"></span></a></th>
        <th scope="col" class="manage-column column-author">Creator</th>       
        <th scope="col" class="manage-column column-type">License Class</th>
        <th scope="col" class="manage-column column-date sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order.'&orderby=date'; ?>"><span>Date</span><span class="sorting-indicator"></span></a></th>
        <th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24"></span></th>
        </tr>
    </tfoot>
  </table>
  <div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label><select name="action2" id="bulk-action-selector-bottom">
<option value="-1">Bulk Actions</option>
	<option value="remove_referent" class="hide-if-no-js">Remove Referent</option>
</select>
<input id="doaction2" class="button action" value="Apply" type="submit">
		</div>
  <div class="tablenav-pages">
      <span class="pagination-links">
      <?php 	  
$big = 999999999; // need an unlikely integer
echo paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format' => '?paged=%#%',
	'current' => max( 1,(isset($_GET['paged'])) ? $_GET['paged'] : ''),
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
	
	// Save the License Class
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
	</script>
   </div>
    <div class="wp-tab-panel" id="tabs-2" style="display: none;"></div>
	<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
</div>
<?php }
