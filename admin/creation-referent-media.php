<?php
/**
 * CLink Creation Media 
 *
 * CLink creation of media 
 *
 * @package CLink
 * @subpackage System
 */

// Direct Access Not Allowed
defined( 'ABSPATH' )or die( 'No script kiddies please!' );
// Register clink referent post list to admin menu function
add_action( 'admin_menu', 'wpclink_register_creations_referent_media_menu' );
/**
 * CLink Register Creation Media Menu
 * 
 */
function wpclink_register_creations_referent_media_menu() {

	// Get Creator and Party
	$creator_array = wpclink_get_option( 'authorized_creators' );
	$current_user_id = get_current_user_id();
	$clink_party_id = wpclink_get_option( 'authorized_contact' );

	if ( wpclink_user_has_creator_list( $current_user_id ) ) {

		if ( wpclink_export_mode() || wpclink_both_mode() ) {
			add_submenu_page( 'cl_mainpage.php', 'Creations', 'Creations', 'edit_posts', 'wpclink-creation-referent-media', 'wpclink_display_creations_referent_media' );
		}
	} elseif ( $current_user_id == $clink_party_id ) {
		if ( wpclink_export_mode() || wpclink_both_mode() ) {
			add_submenu_page( 'cl_mainpage.php', 'Creations', 'Creations', 'edit_posts', 'wpclink-creation-referent-media', 'wpclink_display_creations_referent_media' );
		}
	} else {
		if ( wpclink_export_mode() || wpclink_both_mode() ) {
			add_submenu_page( 'cl_mainpage.php', 'Creation', 'Creation', 'manage_options', 'wpclink-referent-creation-media', 'wpclink_display_creations_referent_media' );
		}
	}
}
/**
 * CLink Display Creation Media Page
 * 
 */
function wpclink_display_creations_referent_media() {

	$updated = (isset($_GET['updated_post'])) ? $_GET['updated_post'] : '';
	$removed = (isset($_GET['removed_post'])) ? $_GET['removed_post'] : '';
	// Notifcations 
	if($updated == '1'){
		wpclink_notif_print('Media(s) is/are added into referent list','success');
	}elseif($removed == '1'){
		wpclink_notif_print('Media(s) is/are removed from referent list','success');
	}
	
	?>
	<div id="cl_main" class="wrap all_list_post">
		<?php wpclink_display_tabs_creations(); ?>
		<?php settings_fields( 'cl_rest_options' ); ?>

		<!-- Start tabs -->
		<ul class="wp-tab-bar">
			<li><a href="<?php echo menu_page_url( 'cl-restriction-reuse', false ); ?>">Posts</a>
			</li>
			<li class="wp-tab-active">
				<a href="#">
					<?php _e('Media','cl_text'); ?>
				</a>
			</li>
			<li><a href="<?php echo menu_page_url( 'cl-restriction-page', false ); ?>">Pages</a>
			</li>

		</ul>
		<div class="wp-tab-panel" id="tabs-1">

			<?php
	
	 
if(isset($_GET['order']) and $_GET['order'] == 'asc'){
	$order = 'desc';	
}else{
	$order = 'asc';	
}
  $current_page = menu_page_url( 'wpclink-creation-referent-media', false );
  
  if($restrict_list = wpclink_get_option('referent_attachments')){
	  
	  
	  
	  if(empty($restrict_list)){
		  $restrict_list = array('0');
	  }
  }else{
	 $restrict_list = array('0');
  }
$paged = ( isset($_GET['paged']) ) ? abs($_GET['paged'] ) : 1;
$args = array('post_type' => 'attachment','paged' => $paged, 'posts_per_page' => 10, 'post_status' => 'any');
$args['post__in'] = $restrict_list;
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
				<input name="page" class="page" value="cl-restriction-reuse" type="hidden">
				<input name="paged" class="paged" value="<?php echo $paged ?>" type="hidden">
				<table class="wp-list-table widefat fixed striped posts">
					<thead>
						<tr>
							<td id="cb" class="manage-column column-cb check-column">
								<label class="screen-reader-text" for="cb-select-all-1">
									<?php _e('Select All','cl_text'); ?>
								</label>
								<input id="cb-select-all-1" type="checkbox">
							</td>
							<th scope="col" id="title" class="manage-column column-title column-primary sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order; ?>"><span><?php _e('Title','cl_text'); ?></span><span class="sorting-indicator"></span></a>
							</th>
							<th scope="col" id="author" class="manage-column column-author">
								<?php _e('Creator','cl_text'); ?>
							</th>
							<th scope="col" id="type" class="manage-column column-type">
								<?php _e('Type','cl_text'); ?>
							</th>
							<th scope="col" id="date" class="manage-column column-date sortable <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order.'&orderby=date'; ?>"><span>Date</span><span class="sorting-indicator"></span></a>
							</th>
							<th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24"></span>
							</th>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php if ( $the_query->have_posts() ) : ?>
						<?php while ( $the_query->have_posts() ) : $the_query->the_post();
	$post_id = get_the_ID();
	// License Class
	$license_class = get_post_meta($post_id,'wpclink_creation_license_class',true); ?>
						<tr id="post-<?php echo $post_id; ?>" class="iedit referent_list author-self level-0 post-<?php echo $post_id; ?> type-post status-draft format-standard hentry category-uncategorized">
							<th scope="row" class="check-column"> <label class="screen-reader-text" for="cb-select-<?php echo $post_id; ?>">Select on buttons</label>
								<input id="cb-select-<?php echo $post_id; ?>" name="post[]" disabled type="checkbox">
								<div class="locked-indicator"> <span class="locked-indicator-icon" aria-hidden="true"></span> <span class="screen-reader-text"> buttons is locked</span> </div>
							</th>
							<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
								<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span>
								</div>
								<strong><a href="<?php echo 'post.php?post='.$post_id.'&action=edit'; ?>" class="row-title"><?php the_title(); ?></a></strong>

								
								
								 <?php if(wpclink_is_licensed_by_post_id($post_id)){ ?>
          <div class="row-actions"><span class="trash"><a href="<?php print wp_nonce_url($current_page.'&remove_referent='.$post_id, 'remove_referent_nonce', 'nonces');?>" rel="bookmark"><?php _e('Remove Referent'); ?></a></span></div>
          <?php }else{ ?>
          <div class="row-actions"><span class="trash"><?php _e('Remove Referent'); ?></span></div>
          <?php } ?>


								<button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e('Show more details'); ?></span></button>
							</td>
							<td class="author column-author" data-colname="Author">
								<?php $author_id =  get_post_field( 'post_author', $post_id );
			$user_info_author = get_userdata($author_id);
		echo $user_info_author->display_name; ?>
							</td>
							<td class="date column-type" data-colname="Type">
								<?php echo get_post_mime_type( $post_id ); ?>
							</td>
							<td class="date column-date" data-colname="Date">
								<?php _e('Published','cl_text'); ?> <br/>
								<abbr>
									<?php echo get_the_date( 'Y/m/d', $post_id );?>
								</abbr><br/>
							</td>
							<td class="clink_id column-clink_id" data-colname="">
								<?php
								$contentID = get_post_meta( $post_id, 'wpclink_creation_ID', true );
								if ( !empty( $contentID ) ) {
									echo '<a href="' . WPCLINK_ID_URL . '/#objects/' . $contentID . '" target="_blank" target="_blank">' . wpclink_strip_prefix_clink_ID( $contentID ) . '</a>';
								} else {
									echo 'N/A';
								}
								?>
							</td>

						</tr>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
						<?php else : ?>
						<tr>
							<td colspan="7">
								<?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?>
							</td>
						</tr>
						<?php endif; ?>
					</tbody>
					<tfoot>
						<tr>
							<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label>
								<input id="cb-select-all-2" type="checkbox">
							</td>
							<th scope="col" class="manage-column column-title column-primary sortable  <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order; ?>"><span><?php _e('Title','cl_text'); ?></span><span class="sorting-indicator"></span></a>
							</th>
							<th scope="col" class="manage-column column-author">
								<?php _e('Creator','cl_text'); ?>
							</th>
							<th scope="col" class="manage-column column-type sortable">Type</th>
							<th scope="col" class="manage-column column-date sortable  <?php echo $order; ?>"><a href="<?php echo $current_page.'&order='.$order.'&orderby=date'; ?>"><span><?php _e('Date','cl_text'); ?></span><span class="sorting-indicator"></span></a>
							</th>
							<th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24"></span>
							</th>
						</tr>
					</tfoot>
				</table>

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
) ); ?>
					</span>
				</div>
			</form>
		</div>
		<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
	</div>
	<?php
}