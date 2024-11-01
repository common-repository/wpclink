<?php
/**
 * CLink Linked Media
 *
 * CLink linked media admin page.
 *
 * @package CLink
 * @subpackage Content Manager
 */
 
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Register clink linked post admin menu
add_action('admin_menu', 'wpclink_register_menu_creation_linked_media');
/**
  * CLink Linked Post Admin Menu
  * 
  */ 
function wpclink_register_menu_creation_linked_media() {

	// Current User ID
	$current_user_id = get_current_user_id();
	
	// Party ID
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	if(wpclink_user_has_creator_list( $current_user_id ) || $current_user_id == $clink_party_id || current_user_can('administrator')){
		
			 add_submenu_page( 
			  'cl_mainpage.php',
			  'Media',
			  'Creations',
			  'edit_posts',
			  'content_link_media.php',
			  'wpclink_display_creation_linked_media'
		  );
		
		}
}
/**
  * CLink Linked Post Admin Page Render
  * 
  */ 
function wpclink_display_creation_linked_media() {
echo '<div class="wrap">';
$cl_options = wpclink_get_option( 'preferences_general' );
	
	
	
if(isset($cl_options['sync_site'])){
	$get_sync_site = $cl_options['sync_site'];
}else{
	$get_sync_site = '';
}
	
// Site URL
$link_site_url = apply_filters('cl_site_link',$get_sync_site);
$cl_main_page = menu_page_url( 'cl-promote-keys', false ); 
echo '<div class="wrap">';
// Main Page URL
$main_page = menu_page_url( 'cl_mainpage.php', false );
$primary_site_get = wpclink_get_option('primary_linked_site');
// If No Connection
if(!isset($primary_site_get) || $primary_site_get == false){
	
wpclink_display_tabs_creations();
	
echo '<div class="cl_subtabs_box"><div class="cl_notice_new maximize">You have not linked with any sites.</div></div>';
	
// End of the CLink Admin page
do_action( 'wpclink_after_admin_page');
echo '</div>';
return false;
}
// Tabs	
wpclink_display_tabs_creations(); ?>
<div class="cl_subtabs_box"><?php 
// License ID
$primary_site_license_id = wpclink_get_option('primary_linked_site');
$primary_site_license_id_got = abs($primary_site_license_id);
$download_id = wpclink_get_license_meta($primary_site_license_id_got,'license_download_id',true);
	
$licensee_linked_variables = wpclink_get_license_meta($primary_site_license_id_got,'licensee_linked_variables',true);
	
$post_found_qc = 0;
	
// Debug
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
print_r($_POST);
}
$running_query = $_SERVER['QUERY_STRING'];
	
	
$request_query = array();
$request_query['post_per_page'] = '10';
$post_type = array('c-attachment');
$request_query['post_type'] = implode(",",$post_type);
if(isset($_GET['posts_per_page'])){
	$request_query['posts_per_page'] = $_GET['posts_per_page'];	
}
if(isset($_GET['paged'])){
	$request_query['paged'] = $_GET['paged'];	
}
if(isset($_GET['search'])){
	$request_query['search'] = $_GET['search'];	
}
if(isset($_GET['author_id'])){
	$request_query['author_id'] = $_GET['author_id'];	
}
if(isset($_GET['tag'])){
	$request_query['tag'] = $_GET['tag'];	
}
if(isset($_GET['category_name'])){
	$request_query['category_name'] = $_GET['category_name'];	
}
if(isset($_GET['order'])){
	$request_query['order'] = $_GET['order'];	
}
if(isset($_GET['orderby'])){
	$request_query['orderby'] = $_GET['orderby'];	
}
if(isset($_GET['author_name'])){
	$request_query['author_name'] = $_GET['author_name'];	
}
	
$request_query['referent_site_address'] = urlencode(get_bloginfo('url'));	
	
$menu_page = menu_page_url( 'content_link_media.php', false );
$menu_page_skip = menu_page_url( 'wpclink_attachment_skip.php', false ); 
$skip_request = wpclink_get_option('content_link_skip_attachments');
if(!empty($skip_request)){
	$skip_req_array = $skip_request[$link_site_url];
	$request_query['post__not_in'] = implode(",",$skip_req_array);
}
	

// Debug
//print_r($request_query); 
	
// Finally Build Query
$build_query = build_query( $request_query );
$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
$xml = simplexml_load_string($xml);
	
// * IP Warning
$ip_warning = (string)$xml->channel->ip_site_warning;
if($ip_warning == '1'){
	$ip_warning_flag = 1;
}
	
// Debug
//var_dump(wpclink_linked_site().'&'.$build_query);
	
$numbers_posts = (string)$xml->channel->item_count;
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
echo '<pre>';
print_r($xml);
echo '</pre>'; 
echo $build_query;
}

$query_extract = $build_query;
parse_str($query_extract, $get_array);
$get_array['post_type'] = explode(",",$get_array['post_type']);
	
// Debug
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
	echo '<pre>';
	print_r($get_array);
	echo '</pre>'; 
}
$referent_categories = array();
$referent_cat_names = array();
$running_array = array();
parse_str($running_query,$running_array);
$catched_id_list = array();
$catched_id_list_see = array();
$all_sync_posts = get_posts( array( 'meta_key' => 'wpclink_referent_post_link','posts_per_page' => -1, 'post_type' => 'attachment') );
	

foreach($all_sync_posts as $single_sync){
	$post_sync_id = $single_sync->ID;
	$get_origin_id = get_post_meta($post_sync_id,'wpclink_referent_post_link', true);
	$catched_id = $get_origin_id[$link_site_url]['origin_id'];
	$catched_id_list[] = $catched_id;
	$catched_id_list_see[$catched_id] = $post_sync_id;
}
// Debug
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
	print_r($catched_id_list_see);
}
do_action('cl_before_post_page');
?>
<?php if(!empty($request_query['search'])){ ?>
<div class="search-keyword"> <span class="subtitle">Search results for “<?php echo $request_query['search']; ?>”</span> </div>
<?php } ?>
<div class="small-navigate">
  <ul class="subsubsub">
    <li class="all"><a href="<?php echo $menu_page; ?>" class="current"> All <span class="count">(<?php echo $numbers_posts; ?>)</span></a></li>    
  </ul>
</div>
<form id="posts-filter" method="get" action="<?php echo $menu_page; ?>">
  <input name="page" class="page-name" value="content_link_post.php" type="hidden">
  <div class="alignleft actions">
    <label class="screen-reader-text" for="orderby">Order By</label>
    <select name="orderby" id="orderby" class="postform">
	<?php if(!isset($request_query['orderby'])) $request_query['orderby'] = 'none'; ?>
      <option <?php selected( $request_query['orderby'], 'none' ); ?> value="none">None</option>
      <option <?php selected( $request_query['orderby'], 'date' ); ?> value="date">Date</option>
      <option <?php selected( $request_query['orderby'], 'title' ); ?> value="title">Title</option>
      <option <?php selected( $request_query['orderby'], 'author' ); ?> value="author">Author</option>
      <option <?php selected( $request_query['orderby'], 'ID' ); ?> value="ID">ID</option>
    </select>
    <label class="screen-reader-text" for="order">Order</label>
    <select name="order" id="order" class="postform">
		<?php if(!isset($request_query['order'])) $request_query['order'] = 'none'; ?>
      <option <?php selected( $request_query['order'], 'ASC' ); ?> value="ASC">Ascending</option>
      <option <?php selected( $request_query['order'], 'DESC' ); ?> value="DESC">Descending</option>
    </select>
    <select name="posts_per_page" id="posts_per_page" class="posts_per_page">
<?php if(!isset($request_query['posts_per_page'])) $request_query['posts_per_page'] = '10'; ?>
      <option class="level-0" <?php selected( $request_query['posts_per_page'], 10 ); ?> value="10">10</option>
      <option class="level-0" <?php selected( $request_query['posts_per_page'], 25 ); ?> value="25">25</option>
      <option class="level-0" <?php selected( $request_query['posts_per_page'], 50 ); ?> value="50">50</option>
      <option class="level-0" <?php selected( $request_query['posts_per_page'], 100 ); ?> value="100">100</option>
      <option class="level-0" <?php selected( $request_query['posts_per_page'], 250 ); ?> value="250">250</option>
    </select>
    <input name="filter_action" id="post-query-submit" class="button" value="Filter" type="submit">
  </div>
</form>
<form id="posts-filter" method="get" action="<?php echo $menu_page; ?>">
  <p class="search-box">
    <label class="screen-reader-text" for="post-search-input">Search Media:</label>
    <input id="post-search-input" name="search" value="" type="search">
    <input id="search-submit" class="button" value="Search Media" type="submit">
  </p>
  <input name="page" class="page-name" value="content_link_post.php" type="hidden">
</form>
<form id="posts-list" method="post">
  <?php wp_nonce_field( 'all_post_list_update', 'all_post_list' ); ?>
  <h2 class="screen-reader-text">Media list</h2>
  <div class="tablenav top">
    <div class="alignleft actions bulkactions">
      <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
      <select name="action" id="bulk-action-selector-top">
        <option value="-1">Bulk Actions</option>
        <option value="sync">Publish</option>
      </select>
      <input id="doaction_bulk" class="button action" value="Apply" type="submit">
    </div>
  </div>
  <table class="wp-list-table widefat fixed striped posts">
    <thead>
      <tr>
        <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label>
          <input id="cb-select-all-1" type="checkbox"></td>
        <?php 
		  $order = (isset($_GET['order'])) ? $_GET['order'] : '';
	   	  if($order == 'DESC'){ $order = 'ASC'; }else{ $order = 'DESC'; }
		  ?>
    <th scope="col" id="title" class="manage-column column-title column-primary sortable <?php echo strtolower($order); ?>"> <a href="<?php echo $menu_page.'&post_type=c-post&orderby=title&order='.$order; ?>"> <span>Title</span><span class="sorting-indicator"></span></a></th>
    <th scope="col" id="ref_creator" class="manage-column column-ref_creator">Referent Creator</th>
    <th scope="col" id="author" class="manage-column column-author">Creator</th>
    <th scope="col" id="type" class="manage-column column-type">License Class </th>
    <?php 
		  $order = (isset($_GET['order'])) ? $_GET['order'] : '';
	   	  if($order == 'DESC'){ $order = 'ASC'; }else{ $order = 'DESC'; }
		  ?>
    <th scope="col" id="date" class="manage-column column-date sortable <?php echo strtolower($order); ?>"><a href="<?php echo $menu_page.'&post_type=c-post&orderby=date&order='.$order; ?>"><span>Date</span><span class="sorting-indicator"></span></a></th>
    <th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24-text">Referent</span></th>
    <th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24-text"></span></th>
  </tr>
</thead>
<tbody id="the-list">
  <?php
      $skipped_posts = wpclink_get_option('content_link_skip_attachments');
	  $skipped_list = $skipped_posts[$link_site_url];
	  
	  $post_found = 0;
	  $sync_status_qc = false;
	  $count = 0;
	
	  foreach($xml->channel->item as $single){
		  
      $post_found++; // Post Count
	  
	  $post_id = (string)$single->post_id;
	  
	  if(in_array($post_id,$catched_id_list)){
		$sync_status = true;  
	  }else{
		$sync_status = false;
	  }
	  
	  $orgin_id_show = (isset($catched_id_list_see[$post_id])) ? $catched_id_list_see[$post_id] : '';
	  
	  $ContentID = get_post_meta( $orgin_id_show, 'wpclink_creation_ID', true );
		    
	  $license_id = wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url);
	  $license_taxonomy_permission = wpclink_get_license_meta($license_id,'programmatic_right_categories');
		  
		  // Email
	  $licensor_email = wpclink_get_license_meta($license_id,'licensor_email');
		  
		 // Class
	  $license_class_post = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url));
		  
	  
		  
		  if(isset($_GET['clink_content'])){
			  
				// Quick Creation Post
			  if($post_id == $_GET['clink_content']){
				  
				  $post_found_qc = 1;
				  
				  // qc = Quick Creation
				  $license_id_qc = wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url);
				  $license_taxonomy_permission_qc = wpclink_get_license_meta($license_id_qc,'programmatic_right_categories');
				  
				  	// Class
	  				$license_class_post_qc = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url));
				  
				  if(in_array($post_id,$catched_id_list)){
					$sync_status_qc = true;  
				  }else{
					$sync_status_qc = false;
				  }
	  
				  
			  }
		  }
	  
		 // Linked
	  if($sync_status == false){ ?>
<?php  
 } ?>
<?php if($single->post_ip_warning == '1'){
	if($sync_status){
		// No For Sync
	}else{?>
	<tr class="no-items ip_warning"><td class="colspanchange" colspan="8">
	<p class="ip_warning_popup"><span class="dashicons dashicons-warning"></span><?php _e('The IP address of your site has been changed since the licensee has accepted the license offer for this content. The licensor needs to approve that change. You may contact the licensor by sending an email to <strong>'.$licensor_email.'</strong>','cl_text'); ?></p>
	</tr>
<?php 
	 }
 } ?>
<tr id="post-<?php echo $post_id; ?>" class="iedit author-self level-0 post-<?php echo $post_id; ?> type-post status-publish format-standard has-post-thumbnail hentry category-other tag-cereals tag-food <?php if($sync_status) echo "sync";  ?> <?php if($single->post_ip_warning == '1'){ if($sync_status){ }else{	echo 'post_warning_class'; } } ?>">
  <th scope="row" class="check-column"> <label class="screen-reader-text" for="cb-select-136">Select Hot Cereals Breakfast</label>
    <input id="cb-select-<?php echo $post_id; ?>" name="post[]" value="<?php echo $post_id; ?>" type="checkbox" <?php if(in_array($post_id,$catched_id_list)) echo 'disabled="disabled"'; ?>>
    <div class="locked-indicator"></div>
  </th>
  <td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><strong>
    <?php if($sync_status){ ?>
    <a class="row-title" href="<?php echo 'post.php?post='.$orgin_id_show.'&action=edit'; ?>"><?php echo $single->title; ?></a>
	  
    <?php }else{ ?>
    <a class="row-title" href="<?php echo $single->link;?>"><?php echo $single->title; ?></a>
    <?php 
		  } ?>
    </strong>
    <div class="row-actions">
      <?php if($sync_status){ 
		if($single->post_ip_warning == '1'){
			// Post has warning 
		?>
		<?php }else{ ?>
      <span class="view"><a href="<?php echo 'post.php?post='.$orgin_id_show.'&action=edit'; ?>" rel="permalink">Edit</a></span> | <span class="view"><a target="_blank" href="<?php bloginfo('url'); ?>?license_my_show_id=<?php echo $primary_site_license_id_got; ?>&download_id=<?php echo $download_id; ?>">View License</a> | <span class="view"><a href="<?php echo $single->link;?>" target="_blank" rel="permalink">View Referent</a></span></span>
      <?php 
		}
		
	  }else{
		
		  if($single->post_ip_warning == '1'){
			// Post has warning 
		?>
		<?php }else{ ?>
     
      <span class="edit"> <a data-postid="<?php echo $post_id; ?>" data-tpermission="<?php echo $license_taxonomy_permission; ?>" data-ltype="<?php echo $license_class_post; ?>"  class="cl_publish" >Publish</a></span> | <span><a target="_blank" href="<?php bloginfo('url'); ?>?license_my_show_id=<?php echo $primary_site_license_id_got; ?>&download_id=<?php echo $download_id; ?>">View License</a> </span> | <span class="view"><a href="<?php echo $single->link;?>" target="_blank" rel="permalink">View Referent</a></span></div>
    <?php }
	  	} ?>
    <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>
  <td class="ref_creator column-ref_creator" data-colname="ref_creator"><?php
          if($sync_status){ 
			if($referent_creator = get_post_meta( $orgin_id_show, 'wpclink_referent_creator_display_name', true )){
				echo $referent_creator;
			}else{
			  echo 'N/A';  
			}
		  }else{
		$ns_dc = $single->children('http://purl.org/dc/elements/1.1/'); ?>
          <a href="<?php echo $menu_page.'&post_type=c-post&author_id='.$ns_dc->creator_id; ?>"><?php echo $single->post_creator; ?></a>
          <?php } ?></td>
  <td class="author column-author" data-colname="Author"><?php 
		if($sync_status){
			$author_id =  get_post_field( 'post_author', $orgin_id_show );
			$user_info_author = get_userdata($author_id);
		echo $user_info_author->display_name;
        }else{
			echo 'N/A';  
		} ?></td>
  <td class="type column-type" data-colname="Type"><?php echo wpclink_get_license_class_label($license_class_post); ?></td>
  <td class="date column-date" data-colname="Date"><?php if($sync_status){ ?>
    Linked<br>
    <abbr><?php echo get_the_date( 'Y/m/d', $orgin_id_show );?></abbr><br />
    <br />
    <?php } ?>
    Published<br>
    <abbr><?php echo $single->pubDate;?></abbr></td>
  <td class="clink_id column-clink_id" data-colname=""><?php
		  if($sync_status){ 
		   if(!empty($single->content_id)){
		  echo '<p><a href="'.WPCLINK_ID_URL.'/#objects/'.$single->content_id.'" target="_blank">'.wpclink_strip_prefix_clink_ID($single->content_id).'</a></p>'; ?>
    <?php }
		   }else{
			  if(!empty($single->content_id)){
		 		 echo '<p><a href="'.WPCLINK_ID_URL.'/#objects/'.$single->content_id.'" target="_blank">'.wpclink_strip_prefix_clink_ID($single->content_id).'</a></p>';
			  }
			   
		   }?></td>
	<td class="clink_id column-clink_id" data-colname=""><?php 
		  if($sync_status and !empty($ContentID)){
			  echo '<br /><br /><br /><p><a href="'.WPCLINK_ID_URL.'/#objects/'.$ContentID.'" target="_blank">'.wpclink_strip_prefix_clink_ID($ContentID).'</a></p>';
		
		  }elseif($sync_status){
		
          } ?></td>
</tr>
<?php $count++; ?>
<?php }
	  if($post_found == 0){
		  
	  } ?>
</tbody>
<tfoot>
  <tr>
    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label>
      <input id="cb-select-all-2" type="checkbox"></td>
    <th scope="col" class="manage-column column-title column-primary sortable desc"><a href="http://shaukatmirza.com/rosa/wp-admin/edit.php?orderby=title&amp;order=asc"><span>Title</span><span class="sorting-indicator"></span></a></th>
    <th scope="col" class="manage-column column-ref_creator">Referent Creator</th>
    <th scope="col" class="manage-column column-author">Creator</th>
    <th scope="col" class="manage-column column-type">License Class</th>
    <th scope="col" class="manage-column column-date sortable asc"><a href="#desc"><span>Date</span><span class="sorting-indicator"></span></a></th>
    <th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24-text">Referent </span></th>
    <th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24-text"></span></th>
  </tr>
</tfoot>
</table>
<div class="tablenav bottom">
  <div class="alignleft actions bulkactions">
    <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
    <select name="action2" id="bulk-action-selector-bottom">
      <option value="-1">Bulk Actions</option>
      <option value="sync">Publish</option>
    </select>
    <input id="doaction2" class="button action" value="Apply" type="submit">
  </div>
  <?php
	if($post_found == 0){
	}else{
		
	$pg_max = $xml->channel->children('wp', true)->pagination->children()->pg_max; 
	$pg_current = $xml->channel->children('wp', true)->pagination->children()->pg_current;
	$pg_next 	= $xml->channel->children('wp', true)->pagination->children()->pg_next;
	$pg_prev 	= $xml->channel->children('wp', true)->pagination->children()->pg_prev;
	
	if(empty($pg_max)){
		$pg_max = $pg_current;
	}
	
	?>
    <div class="alignleft actions"> </div>
    <div class="tablenav-pages"><span class="displaying-num"><?php echo $count; ?> items</span> <span class="pagination-links">
      <?php // Pagination No Action
	unset($running_array['action']);
	unset($running_array['p_sync']);  ?>
      <?php if($pg_current > 1){
		$running_array['paged'] = 1;
		$first_page = build_query($running_array);
		 ?>
      <a class="prev-page" href="<?php echo $menu_page.'&'.$first_page; ?>">«</a>
      <?php }else{ ?>
      <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
      <?php } ?>
      <?php if($pg_prev != 0){
		
		$running_array['paged'] = (string)$pg_prev;
		$prev_link = build_query($running_array);
		 ?><a class="prev-page" href="<?php echo $menu_page.'&'.$prev_link; ?>">‹</a>
      <?php }else{ ?>
      <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
      <?php } ?>
      <span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text"><?php echo $pg_current; ?> of <span class="total-pages"><?php echo $pg_max; ?></span></span></span>
      <?php if($pg_next != 0){ 
			$running_array['paged'] = (string)$pg_next;
			$next_link = build_query($running_array);		
	?>
      <a class="next-page" href="<?php echo $menu_page.'&'.$next_link ?>">›</a>
      <?php }else{ ?>
      <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
      <?php } ?>
      <?php if(($pg_max > 1) and ((string)$pg_current != (string)$pg_max)){ 
			$running_array['paged'] = (string)$pg_max;
			$pg_last = build_query($running_array);
	
	?>
      <a class="next-page" href="<?php echo $menu_page.'&'.$pg_last ?>">»</a>
      <?php }else{ ?>
      <span class="tablenav-pages-navspan" aria-hidden="true">»</span></span></div>
    <?php
	  
    } 
	} ?>
    <br class="clear">
  </div>
</form>
<script>
	
	
var cl_reload_page = true;
	
// Quick Edit Call
jQuery( ".editinline" ).click(function() {
	var postid = jQuery(this).data('postid');
	jQuery('.iedit').show();
	jQuery('.quick-edit-row-post').hide();
	jQuery( "#edit-"+postid ).show();
	jQuery( "#post-"+postid ).hide();
});
// Quick Edit Cancel Button
jQuery(".button.cancel").click(function(){
	var postid = jQuery(this).data('postid');
	jQuery( "#edit-"+postid ).hide();
	jQuery( "#post-"+postid ).show();
});
// Preloader Starts
jQuery(document).ajaxStart(function(){
    jQuery(".spinner").css("visibility", "visible");
});
// Preloader Complete
jQuery(document).ajaxComplete(function(){
    jQuery(".spinner").css("visibility", "hidden");
});
// Close Popup
jQuery(document).ready(function() {
  jQuery('.close-pbox').click(function(){
		jQuery('.cl_popup').hide();
});  
});
jQuery(function () {
  
});
// Quick Edit Form Function
function cl_edit_action(event){
	var post_id = event.data.post_my_id; 
	jQuery('.cl_popup').hide();
	jQuery('.iedit').show();
	jQuery('.quick-edit-row-post').hide();
	jQuery( "#edit-"+post_id ).show();
	jQuery('#posts-list').addClass('quick-edit-form');
	  jQuery('.quick-edit-form').on('submit', function (e) {
 
    // We'll pass this variable to the PHP function example_ajax_request
  
	var postid = jQuery(this).find( "#inline_post_id" ).val();
     
    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
        data:  jQuery(this).serialize(),
        success:function(data) {
            // This outputs the result of the ajax request
            console.log(data);
			jQuery( "#edit-"+postid ).hide();
			jQuery( "#post-"+postid ).show();
			jQuery( "#post-"+postid ).addClass('sync');
			jQuery( "#post-"+postid ).html(data);
			
        },
        error: function(errorThrown){
            console.log(errorThrown);
        },
    });  
	
	e.preventDefault();
              
});
	jQuery( "#post-"+post_id ).hide();
	jQuery('html, body').animate({
        scrollTop: jQuery("#edit-"+post_id).offset().top-60}, 2000);
}
/**
 * CLink Error Toggle
 *
 */
function wpclink_error_details(){
	 jQuery(".cl_error_msg").slideToggle("fast");
}
	
/**
 * CLink Error Dialog Box
 *
 */	
function wpclink_error_dialoge_box(get_response,popup_type){
	
	var response;
	
	if(typeof get_response === 'object' && get_response !== null){
		response = get_response;
	}else{
		response = jQuery.parseJSON(get_response);
	}
	
	// dialog-confirm
	
	if(response.complete == 'failed'){
		if(popup_type == 'publish_linked_media'){
			
					
			
			jQuery( "#cl_request_error" ).dialog({
			  resizable: false,
			  height: "auto",
			  title: response.error_headline,
			  width: 400,
			  modal: true,
			  open: function(event) {
				  
				 
				  
				  jQuery(".ui-dialog-titlebar").show();
				  jQuery(".ui-dialog-titlebar-close").show();	
				  
				  jQuery(".ui-dialog-buttonpane").find("button:contains(\"Continue\")").addClass("button-primary");
				  
				  jQuery('.error-message-request').html('<p>'+response.error_text+'</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">'+response.code+' : '+response.message+' </p></div>');
				  
				  // Hide to show error
				  jQuery('#cl_publish_popup').hide();
				  
				  // Set to default
				  jQuery('#cl_publish_popup h3').show();
				  jQuery('#cl_publish_popup .cl_message').show();

				 },

			  buttons: {
				"Continue": function() {
					// Close
					jQuery( this ).dialog( "close" );
				}
			  }
			});
			
			
			
			
			 jQuery(".loading-circle-quick").hide(200);
			 cl_reload_page = false;
			
		
			
		}else if(popup_type == 'new_popup'){
			
	
			jQuery( "#cl_request_error" ).dialog({
			  resizable: false,
			  height: "auto",
			  title: response.error_headline,
			  width: 400,
			  modal: true,
			  open: function(event) {
				  
				  jQuery(".ui-dialog-buttonpane").find("button:contains(\"Try Again\")").addClass("button-primary");
				  
				  jQuery('.error-message-request').html('<p>'+response.error_text+'</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">'+response.code+' : '+response.message+' </p></div>');

				 },

			  buttons: {
				"Try Again": function() {
					wpclink_generate_iscc();
					jQuery( this ).dialog( "close" );
					return false;
				}
			  }
			});
		
		
			
			
		}else if(popup_type == 'media_popup'){
			/* in Future */
			
			
			/*jQuery('.loading-circle').html('<p>'+response.error_text+'</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">'+response.code+' : '+response.message+' </p></div>');*/
			
			
			jQuery( "#cl_request_error" ).dialog({
			  resizable: false,
			  height: "auto",
			  title: response.error_headline,
			  width: 400,
			  modal: true,
			  open: function(event) {
				  
				  jQuery( "#dialog-metadata-warning").dialog('close');
				  
				  jQuery(".ui-dialog-titlebar").show();
				  jQuery(".ui-dialog-titlebar-close").show();	
				  
				  jQuery(".ui-dialog-buttonpane").find("button:contains(\"Close\")").addClass("button-primary");
				  
				  jQuery('.error-message-request').html('<p>'+response.error_text+'</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">'+response.code+' : '+response.message+' </p></div>');
				  
				  

				 },

			  buttons: {
				"Close": function() {
					jQuery( this ).dialog( "close" );
					
				}
			  }
			});
			
			
			
			
			 jQuery(".loading-circle-quick").hide(200);
			
		}else if(popup_type == 'license_popup'){
				 
			/* in Future */
			
			
			/*jQuery('.loading-circle').html('<p>'+response.error_text+'</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">'+response.code+' : '+response.message+' </p></div>');*/
			
			
			jQuery( "#cl_request_error" ).dialog({
			  resizable: false,
			  height: "auto",
			  title: response.error_headline,
			  width: 400,
			  modal: true,
			  open: function(event) {
				  
				  
				  
				  jQuery(".ui-dialog-titlebar").show();
				  jQuery(".ui-dialog-titlebar-close").show();	
				  
				  jQuery(".ui-dialog-buttonpane").find("button:contains(\"Close\")").addClass("button-primary");
				  
				  jQuery('.error-message-request').html('<p>'+response.error_text+'</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">'+response.code+' : '+response.message+' </p></div>');
				  
				  

				 },

			  buttons: {
				"Close": function() {
					jQuery( this ).dialog( "close" );
					
				}
			  }
			});
			
			
			 jQuery(".loading-circle-quick").hide(200);
			
		
			
		}else if(popup_type == 'media_version'){
			
			/* in Future */

			
			
			jQuery( "#cl_request_error" ).dialog({
			  resizable: false,
			  height: "auto",
			  title: response.error_headline,
			  width: 400,
			  modal: true,
			  open: function(event) {
				  
				  
				  jQuery( "#dialog-confirm").dialog('close');
				  
				  jQuery(".ui-dialog-titlebar").show();
				  jQuery(".ui-dialog-titlebar-close").show();	
				  
				  jQuery(".ui-dialog-buttonpane").find("button:contains(\"Close\")").addClass("button-primary");
				  
				  jQuery('.error-message-request').html('<p>'+response.error_text+'</p><div class="cl_error_container"><p><a onclick="wpclink_error_details()" class="cl_error_more">See More <span class="dashicons dashicons-plus"></span></a></p><p class="cl_error_msg">'+response.code+' : '+response.message+' </p></div>');
				  
				  
				  jQuery(".loading-circle-quick").hide(200);

				 },

			  buttons: {
				"Close": function() {
					jQuery( this ).dialog( "close" );
				}
			  }
			});
			
		
		}
		
		return true;
		
	}else{
		return false;
	}
	
}	
function loader_status_label(status){
		switch(status) {
		case "fetch_image":
		return "Fetching Image.."
		break;
				
		case "crunching":
		return "Crunching..."
		break;
				
		case "writesimage":
		return "Writing embedded metadata...."
		break;
				
		case "writesregistry":
		return "Writing to regisry..."
		break;

		case "updatingdatabase":
		return "Updating Database..."
		break;
				
		case "exifwrites":
		return "Wrting to Image..."
		break;
				
		case "crunching":
		return "Crunching..."
		break;
				
		case "registrywrites":
		return "Writing to registry..."
		break;
				
		case "updatingembeded":
		return "Updating embedded metadata..."
		break;
				
		case "reloading":
		return "Reloading..."
		break;
				
		case "error":
		return "Error"
		break;
				
		default:
		return ""
		}

	}
	
	function check_loader_status(now_status) {
	
	
	var cl_stop_loader = false;
	var error_trigger = false;
	
	 jQuery.ajax({
			url: ajaxurl, 
			data: {
				"action": "wpclink_get_loader_status_linked_request",
				"status" : "get"
			},
			success:function(data) {
				
				if(data != 0){
				
					if(typeof data === "object" && data !== null){
						error_trigger = wpclink_error_dialoge_box(data,"publish_linked_media");
							
					}else{
						data = jQuery.parseJSON(data);
						error_trigger = wpclink_error_dialoge_box(data,"publish_linked_media");
					}
					
					if(error_trigger == true){
						cl_stop_loader = true;
						stop_popup_error = true;		
					}else{
						cl_reload_page = true;
					}

					if(now_status != data.status){
						if(data.status != "0"){
							jQuery(".cl_loader_status").text(loader_status_label(data.status));
						}
					}
					
					if(cl_stop_loader == false){
					
							check_loader_status(data.status);
						
						
					}
					
				}
				
				
				
			}
		});
	}
	
	
	jQuery('.cl_publish').click(function(){
		
		var post_id = jQuery(this).data('postid');
		var license_class = jQuery(this).data("ltype");
		var taxonomy_permission = jQuery(this).data("tpermission");
		
		
		var edit_html,license_msg;
		
		edit_html = "";

		if(taxonomy_permission == 'ModifyDescription'){
			license_msg = "You can modify description before publishing it.";
		}else if(taxonomy_permission == 'non-editable'){
			license_msg = "You can edit before publishing it";	 
		}else{
			license_msg = "";
		}
		
		license_msg += '<a href="https://docs.clink.media/wordpress-plugins/creations/linked-creation" target="_blank">Learn more about publishing Linked Creation <span class="dashicons dashicons-external"></span></a>';
		
		
		var hyperlink_post = '<?php print wp_nonce_url($menu_page.'&action=sync', 'content_get_post', '_action_nonce_get');  ?>';
		jQuery('.cl_popup').show();
		jQuery('.cl_popup .close-pbox').show();
		jQuery('.cl_popup .pub_later').hide();
		
		jQuery('.cl_message').html(license_msg);
		
		jQuery('.cl_actions').html(edit_html+"<a class='button button-primary publish_now' >Publish</a>");
		
		
		jQuery('.publish_now').click(function(){
			
			
			jQuery('#cl_publish_popup h3').hide();
			jQuery('#cl_publish_popup .cl_message').hide();
			jQuery('#cl_publish_popup .close-pbox').hide();
			
			jQuery('.cl_actions').html('<div class="loading-wrapper"><span class="cl_loader"></span><span class="cl_loader_status">Preparing...</span></div>');
			
			
			check_loader_status('');
			
		 	jQuery.get(
                  hyperlink_post,
                  { p_sync: post_id },
                  function(data) {
					  
					  if(cl_reload_page == true){
						jQuery(".cl_loader_status").text(loader_status_label('reloading'));
						
                     	window.location.href = "<?php echo $menu_page; ?>";
						  
					  }
                  }
               );
			
		});
		
		
		jQuery('.edit_content_go').click({post_my_id: post_id},cl_edit_action);
	});
	
	

	
	function quick_create_content(){
		
<?php 
	if(isset($_GET['clink_content'])){
		$content_id_new = $_GET['clink_content'];
	}else{
		$content_id_new = '0';
	} ?>
	
		var post_id = '<?php echo $content_id_new  ; ?>';
		var license_class = '<?php echo (isset($license_class_post_qc) ? $license_class_post_qc : ''); ?>';
		var taxonomy_permission = '<?php echo (isset($license_taxonomy_permission_qc) ? $license_taxonomy_permission_qc : ''); ?>';
		
		
		var edit_html,license_msg;
			
		if(taxonomy_permission == 'ModifyDescription'){
			edit_html = "";
		}else {
			edit_html = "";
		}
		
		
		license_msg = '<a href="https://docs.clink.media/wordpress-plugins/creations/linked-creation" target="_blank">Learn more about publishing Linked Creation <span class="dashicons dashicons-external"></span></a>';
				
		var hyperlink_post = '<?php print wp_nonce_url($menu_page.'&action=sync', 'content_get_post', '_action_nonce_get');  ?>';
		
		jQuery('.cl_popup').show(200);
		jQuery('.cl_popup .close-pbox').hide();
		jQuery('.cl_popup .pub_later').show();
		
				
		jQuery('.cl_message').html(license_msg);
		
		jQuery('.cl_actions').html(edit_html+"<a class='button button-primary publish_now'>Publish</a> <a class='button  pub_later'>Later</a>");
		
		
		jQuery('.publish_now').click(function(){
			
			
			jQuery('#cl_publish_popup h3').hide();
			jQuery('#cl_publish_popup .cl_message').hide();
			jQuery('#cl_publish_popup .close-pbox').hide();
			
			
		
			jQuery('.cl_actions').html('<div class="loading-wrapper"><span class="cl_loader"></span><span class="cl_loader_status">Preparing...</span></div>');
			
			check_loader_status('');
			
		 	jQuery.get(
                  hyperlink_post,
                  { p_sync: post_id },
                  function(data) {
					  if(cl_reload_page == true){
						jQuery(".cl_loader_status").text(loader_status_label('reloading'));
                     	window.location.href = "<?php echo $menu_page; ?>";
						 
					  }
                  }
               );
			
			
			
			
			
		});
		
		
		
		jQuery('.edit_content_go').click({post_my_id: post_id},cl_edit_action);
		jQuery('.pub_later').click(function(){
			jQuery('.cl_popup').hide(200);
		});
		
	
		
	}	
<?php if(isset($_GET['clink_content']) and $_GET['clink_content'] > 0){
		if($post_found_qc == 1) {
			if($sync_status_qc == false){?>
			jQuery(document).ready(function(){
				/* Start Quick Creation Popup */
				quick_create_content();
			});
			<?php }
			}
	} ?>	
// Quick Seach Tags
  jQuery( function() {
	  
	  var tempID = 0;
	  var cache;
	  var last;
	  var separator =  ',';
	  
    function split( val ) {
      return val.split( /,\s*/ );
    }
	
	function extractLast( term ) {
      return split( term ).pop();
    }
	function getLast( term ) {
		return split( term ).pop();
	}
 
    jQuery( ".tax_input_post_tag" )
      // don't navigate away from the field on tab when selecting an item
      .on( "keydown", function( event ) {
        if ( event.keyCode === jQuery.ui.keyCode.TAB &&
            jQuery( this ).autocomplete( "instance" ).menu.active ) {
          event.preventDefault();
        }
      })
	  
      .autocomplete({
        source: function( request, response ) {
			
			
			jQuery.ajax({
        url: ajaxurl, 
        data: {
            'action': 'ajax-tag-search',
            'tax' : 'post_tag',
			'q' : extractLast( request.term )
		},
        success:function(data) {
            
					var tagName;
					var tags = [];
					if ( data ) {
						data = data.split( '\n' );
						for ( tagName in data ) {
							var id = ++tempID;
							tags.push({
								id: id,
								label: data[tagName],
								value: data[tagName]
							});
						}
						cache = tags;
						response( tags );
						
						console.log(tags);
					} else {
						response( tags );
					}
				
			
        },
        error: function(errorThrown){
            console.log(errorThrown);
        },
    });  
			
          
        },
        search: function() {
          // custom minLength
          var term = extractLast( this.value );
          if ( term.length < 2 ) {
            return false;
          }
        },
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.value );
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        }
      });
  } );
  </script>
<?php echo '</div>'; ?>
<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
<?php echo '</div>';
}