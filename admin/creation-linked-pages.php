<?php 
/**
 * CLink Linked Pages
 *
 * CLink linked pages admin page.
 *
 * @package CLink
 * @subpackage Content Manager
 */
// Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// Register clink linked page admin menu
add_action('admin_menu', 'wpclink_register_menu_creation_linked_page');
 
/**
  * CLink Linked Page Admin Menu
  * 
  */ 
function wpclink_register_menu_creation_linked_page() {
	$creator_array = wpclink_get_option('authorized_creators');
	$current_user_id = get_current_user_id();
	
	// Party
	$clink_party_id = wpclink_get_option('authorized_contact');
	
	
	if(wpclink_user_has_creator_list($current_user_id)){
		
		if(wpclink_import_mode() || wpclink_both_mode()){
		  add_submenu_page( 
			  'cl_mainpage.php',
			  'Creations',
			  'Pages',
			  'edit_posts',
			  'content_link_page.php',
			  'wpclink_display_creation_linked_page');
			  
		}
			  
		}elseif($clink_party_id == $current_user_id){
			if(wpclink_import_mode() || wpclink_both_mode()){
			 add_submenu_page( 
			  'cl_mainpage.php',
			  'Creations',
			  'Pages',
			  'edit_posts',
			  'content_link_page.php',
			  'wpclink_display_creation_linked_page');
			}
		}
	
	
}
/**
  * CLink Linked Page Admin Page Render
  * 
  */ 
function wpclink_display_creation_linked_page() { 
echo '<div class="wrap">';
// Main Page URL
$main_page = menu_page_url( 'cl_mainpage.php', false );
$primary_site_get = wpclink_get_option('primary_linked_site');
// If No Connection
if(!isset($primary_site_get) || $primary_site_get == false){
	wpclink_display_tabs_creations();
	echo '<div class="cl_subtabs_box"><div class="cl_notice_new maximize">You have not linked with any sites. </div></div>';
	// End of the CLink Admin page
	do_action( 'wpclink_after_admin_page');
	echo '</div>';
	return false;
}
// Tabs
wpclink_display_tabs_creations();
// License ID
$primary_site_license_id = wpclink_get_option('primary_linked_site');
$primary_site_license_id_got = abs($primary_site_license_id);
	
$licensee_linked_variables = wpclink_get_license_meta($primary_site_license_id_got,'licensee_linked_variables',true);
	
$post_found_qc = 0;

// License Class
$license_class = wpclink_get_license_class_by_license_id($primary_site_license_id_got);
echo '<div class="cl_subtabs_box">';
$cl_main_page = menu_page_url( 'cl-promote-keys', false ); 
// Check Working Path
if(wpclink_check_path() == false){
	if(wpclink_get_option('primary_linked_site') != 'super'){
		
		$main_page = menu_page_url( 'cl_mainpage.php', false );
		
		wpclink_notif_print('CLink Connection Failed.! <a href="'.$main_page.'">Check Settings</a>.','error');
	}else{
		wpclink_notif_print('CLink Connection Failed.! <a href="'.$main_page.'">Check Settings</a>.','error');
	}
	echo '</div>';
}
$cl_options = wpclink_get_option( 'preferences_general' );
	
if(isset($cl_options['sync_site'])){
	$get_sync_site = $cl_options['sync_site'];
}else{
	$get_sync_site = '';
}
	
	
// Site URL
$link_site_url = apply_filters('cl_site_link',$get_sync_site);
	
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
print_r($_POST);
}
$running_query = $_SERVER['QUERY_STRING'];
	
$request_query = array();
$request_query['post_per_page'] = '10';
$post_type = array('c-page');
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
	
$menu_page = menu_page_url( 'content_link_page.php', false );
$menu_page_skip = menu_page_url( 'cl_page_skip.php', false ); 
$skip_request = wpclink_get_option('content_link_skip_pages');
if(!empty($skip_request)){
	$skip_req_array = $skip_request[$link_site_url];
	$request_query['post__not_in'] = implode(",",$skip_req_array);
}
// Finally Build Query
$build_query = build_query( $request_query );
$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$xml=file_get_contents(wpclink_linked_site().'&'.$build_query,false,$context);
$xml = simplexml_load_string($xml);
	
// IP WARNING
$ip_warning = (string)$xml->channel->ip_site_warning;
if($ip_warning == '1'){
	$ip_warning_flag = 1;
}
$numbers_posts = (string)$xml->channel->item_count;
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
echo $build_query;
}
$query_extract = $build_query;
parse_str($query_extract, $get_array);
$get_array['post_type'] = explode(",",$get_array['post_type']);
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
echo '<pre>';
print_r($get_array);
echo '</pre>'; 
}
$running_array = array();
parse_str($running_query,$running_array);
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
echo "SERVER Q->";
print_r($running_array);
}
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
echo "<h5>";
print_r(wpclink_get_option('content_link_skip_posts'));
echo "</h5>";
}

	
$catched_id_list = array();
$catched_id_list_see = array();
$all_sync_posts = get_posts( array( 'meta_key' => 'wpclink_referent_post_link','posts_per_page' => -1, 'post_type' => 'page') );
foreach($all_sync_posts as $single_sync){
	$post_sync_id = $single_sync->ID;
	$get_origin_id = get_post_meta($post_sync_id,'wpclink_referent_post_link', true);
	$catched_id = $get_origin_id[$link_site_url]['origin_id'];
	$catched_id_list[] = $catched_id;
	$catched_id_list_see[$catched_id] = $post_sync_id;
}
if($_SERVER['HTTP_USER_AGENT'] == 'clink'){
print_r($catched_id_list);
}

do_action('cl_before_page_page');
	

?>
<?php if(!empty($request_query['search'])){ ?>
<div class="search-keyword"> <span class="subtitle">Search results for “<?php echo $request_query['search']; ?>”</span> </div>
<?php } ?>
<div class="small-navigate">
  <ul class="subsubsub">
    <li class="all"><a href="<?php echo $menu_page; ?>" class="current"> All <span class="count">(<?php echo $numbers_posts; ?>)</span></a> </li>
    
  </ul>
</div>
<form id="posts-filter" method="get" action="<?php echo $menu_page; ?>">
  <input name="page" class="page-name" value="content_link_page.php" type="hidden">
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
    <label class="screen-reader-text" for="post-search-input">Search Posts:</label>
    <input id="post-search-input" name="search" value="" type="search">
    <input id="search-submit" class="button" value="Search Posts" type="submit">
  </p>
  <input name="page" class="page-name" value="content_link_page.php" type="hidden">
 
</form>
<form id="posts-list" method="post">
  <?php wp_nonce_field( 'all_post_list_update', 'all_post_list' ); ?>
  <h2 class="screen-reader-text">Posts list</h2>
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
    <th scope="col" id="title" class="manage-column column-title column-primary sortable <?php echo strtolower($order); ?>"> <a href="<?php echo $menu_page.'&post_type=c-page&orderby=title&order='.$order; ?>"> <span>Title</span><span class="sorting-indicator"></span></a></th>
	<th scope="col" id="ref_creator" class="manage-column column-ref_creator">Referent Creator</th>
    <th scope="col" id="author" class="manage-column column-author">Author</th>
    <th scope="col" id="type" class="manage-column column-type">License Class </th>
    <?php 
		 $order = (isset($_GET['order'])) ? $_GET['order'] : '';
	   	  if($order == 'DESC'){ $order = 'ASC'; }else{ $order = 'DESC'; }
		  ?>
    <th scope="col" id="date" class="manage-column column-date sortable <?php echo strtolower($order); ?>"><a href="<?php echo $menu_page.'&post_type=c-page&orderby=date&order='.$order; ?>"><span>Date</span><span class="sorting-indicator"></span></a></th>
    <th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24-text">Referent</span></th>
    <th scope="col" id="clink_id" class="manage-column column-clink_id"><span class="clinkico-24-text"></span></th>
  </tr>
</thead>
<tbody id="the-list">
  <?php
      $skipped_posts = wpclink_get_option('content_link_skip_posts');
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
	
	  $license_class_post = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url));

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
	  				$license_class_post_qc = wpclink_get_license_class_by_license_id(wpclink_get_linked_license_id_by_referent_post_id($post_id, $link_site_url ));
				  
				  if(in_array($post_id,$catched_id_list)){
					$sync_status_qc = true;  
				  }else{
					$sync_status_qc = false;
				  }
	  
				  
			  }
		  }
	  

	?>
  <?php if($sync_status == false){ ?>
  <tr id="edit-<?php echo $post_id; ?>" class="inline-edit-row inline-edit-row-post quick-edit-row quick-edit-row-post inline-edit-post inline-editor" style="display:none;">
    <td colspan="8" class="colspanchange">
<form action="" method="post" class="quick-edit-form">
  <fieldset class="inline-edit-col-left quick-edit-col1">
    <legend class="inline-edit-legend">Quick Edit</legend>
    <div class="inline-edit-col">
      <fieldset class="inline-edit-date">
        <legend><span class="title">Date</span></legend>
        <div class="timestamp-wrap">
          <label><span class="screen-reader-text">Month</span>
            <select name="mm">
              <option value="01" data-text="Jan" <?php if(current_time('m') == "01") echo 'selected="selected"'; ?>>01-Jan</option>
              <option value="02" data-text="Feb" <?php if(current_time('m') == "02") echo 'selected="selected"'; ?>>02-Feb</option>
              <option value="03" data-text="Mar" <?php if(current_time('m') == "03") echo 'selected="selected"'; ?>>03-Mar</option>
              <option value="04" data-text="Apr" <?php if(current_time('m') == "04") echo 'selected="selected"'; ?>>04-Apr</option>
              <option value="05" data-text="May" <?php if(current_time('m') == "05") echo 'selected="selected"'; ?>>05-May</option>
              <option value="06" data-text="Jun" <?php if(current_time('m') == "06") echo 'selected="selected"'; ?>>06-Jun</option>
              <option value="07" data-text="Jul" <?php if(current_time('m') == "07") echo 'selected="selected"'; ?>>07-Jul</option>
              <option value="08" data-text="Aug" <?php if(current_time('m') == "08") echo 'selected="selected"'; ?>>08-Aug</option>
              <option value="09" data-text="Sep" <?php if(current_time('m') == "09") echo 'selected="selected"'; ?>>09-Sep</option>
              <option value="10" data-text="Oct" <?php if(current_time('m') == "10") echo 'selected="selected"'; ?>>10-Oct</option>
              <option value="11" data-text="Nov" <?php if(current_time('m') == "11") echo 'selected="selected"'; ?>>11-Nov</option>
              <option value="12" data-text="Dec" <?php if(current_time('m') == "12") echo 'selected="selected"'; ?>>12-Dec</option>
            </select>
          </label>
          <label><span class="screen-reader-text">Day</span>
            <input name="jj" value="<?php echo current_time('j'); ?>" size="2" maxlength="2" autocomplete="off" type="text">
          </label>
          ,
          <label><span class="screen-reader-text">Year</span>
            <input name="aa" value="<?php echo current_time('Y'); ?>" size="4" maxlength="4" autocomplete="off" type="text">
          </label>
          @
          <label><span class="screen-reader-text">Hour</span>
            <input name="hh" value="<?php echo current_time('H'); ?>" size="2" maxlength="2" autocomplete="off" type="text">
          </label>
          :
          <label><span class="screen-reader-text">Minute</span>
            <input name="mn" value="<?php echo current_time('i'); ?>" size="2" maxlength="2" autocomplete="off" type="text">
          </label>
        </div>
        <input id="ss" name="ss" value="27" type="hidden">
      </fieldset>
      <br class="clear">
      <div class="inline-edit-group wp-clearfix">
        <label class="alignleft"> <span class="title">Password</span> <span class="input-text-wrap">
          <input name="post_password" class="inline-edit-password-input" value="" type="text">
          </span> </label>
      </div>
    </div>
  </fieldset>
  <fieldset class="inline-edit-col-right quick-edit-col2"><br />
    <div class="inline-edit-col">
      <div class="inline-edit-group wp-clearfix">
        <label class="alignleft">
          <input name="comment_status" value="open" type="checkbox">
          <span class="checkbox-title">Allow Comments</span> </label>
        <label class="alignleft">
          <input name="ping_status" value="open" type="checkbox">
          <span class="checkbox-title">Allow Pings</span> </label>
      </div>
    </div>
  </fieldset>
	<fieldset class="inline-edit-col-right">
		<br>
		<div class="inline-edit-col">
			<input type="hidden" name="_status" value="publish" />
        <?php $dropdown_args = array(
			'post_type'        => 'page',
			'name'             => 'parent_id',
			'show_option_none' => __('(no parent)'),
			'sort_column'      => 'menu_order, post_title',
			'echo'             => 0,
			'id'			   => 'post_parent'
		);
		$dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post );
		$pages = wp_dropdown_pages( $dropdown_args );
		if ( ! empty($pages) ) : ?>
<label for="parent_id">
	<span class="title">Parent</span>
	<?php echo $pages; ?>
</label>
<?php	endif; // end empty pages check ?>
<label for="page_template">
	<span class="title"><?php _e( 'Template' ); ?></span>
	<select name="page_template" id="page_template">
<?php
$default_title = apply_filters( 'default_page_template_title',  __( 'Default Template' ), 'meta-box' );
?>
<option value="default"><?php echo esc_html( $default_title ); ?></option>
<?php page_template_dropdown( $template, 'page' ); ?>
</select>
	</label>
<?php if ( post_type_supports( 'page', 'page-attributes' ) ) : ?>
<label for="menu_order">
	<span class="title"><?php _e( 'Order' ); ?></span>
	<input name="menu_order" type="text" size="4" id="menu_order" value="0" />
</label>
<?php endif; ?>
      </div>
	</fieldset>
  <div class="submit inline-edit-save">
    <button type="button" class="button cancel alignleft" data-postid="<?php echo $post_id; ?>">Cancel</button>
    <input id="_inline_edit" name="_inline_edit" value="c095d0cc63" type="hidden">
    <input id="inline_post_id" name="inline_post_id" value="<?php echo $post_id; ?>" type="hidden">
    <input id="ref_identifier" name="ref_identifier" value="<?php echo $single->content_id; ?>" type="hidden">
	  <input id="action_type" name="action_type" value="page" type="hidden">
    <button type="submit" class="button button-primary save alignright">Publish</button>
    <span class="spinner"></span>
    <input name="post_view" value="list" type="hidden">
    <input name="screen" value="edit-post" type="hidden">
    <input type="hidden" name="action" value="wpclink_register_linked_creation_by_quick_edit" />
    <br class="clear">
    <div class="notice notice-error notice-alt inline hidden">
      <p class="error"></p>
    </div>
  </div>
</form>
</td>
</tr>
<?php  
 } ?>
<?php if($single->post_ip_warning == '1'){
	if($sync_status){
		// No For Sync
	}else{?>
	<tr class="no-items ip_warning"><td class="colspanchange" colspan="10">
	<p class="ip_warning_popup"><span class="dashicons dashicons-warning"></span><?php _e('The IP address of your site has been changed since the licensee has accepted the license offer for this content. The licensor needs to approve that change. You may contact the licensor by sending an email to <strong>'.$licensor_email.'</strong>','cl_text'); ?></p>
	</tr>
<?php 
	 }
 } ?>

<tr id="post-<?php echo $post_id; ?>" class="iedit author-self level-0 post-<?php echo $post_id; ?> type-post status-publish format-standard has-post-thumbnail hentry category-other tag-cereals tag-food <?php if($sync_status) echo "sync";  ?>  <?php if($single->post_ip_warning == '1'){ if($sync_status){ }else{	echo 'post_warning_class'; } } ?>">
  <th scope="row" class="check-column"> <label class="screen-reader-text" for="cb-select-136">Select Hot Cereals Breakfast</label>
    <input id="cb-select-<?php echo $post_id; ?>" name="post[]" value="<?php echo $post_id; ?>" type="checkbox" <?php if(in_array($post_id,$catched_id_list)) echo 'disabled="disabled"'; ?>>
    <div class="locked-indicator"></div>
  </th>
  <td class="title column-title has-row-actions column-primary page-title" data-colname="Title"><strong>
    <?php if($sync_status){ ?>
    <a class="row-title" href="<?php echo 'post.php?post='.$orgin_id_show.'&action=edit'; ?>"><?php echo $single->title; ?></a>
    <?php }else{ ?>
    <a class="row-title" href="<?php echo $single->link;?>"><?php echo $single->title; ?></a>
    <?php }  ?>
    </strong>
    <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
    <div class="row-actions">
           <?php if($sync_status){ 
		if($single->post_ip_warning == '1'){
			// Post has warning 
		?>
		<?php }else{ ?>
      <span class="view"><a href="<?php echo 'post.php?post='.$orgin_id_show.'&action=edit'; ?>" rel="permalink">Edit Post</a></span> | <span class="view"><a target="_blank" href="<?php bloginfo('url'); ?>?license_my_show_id=<?php echo $primary_site_license_id_got; ?>&download_id=<?php echo $download_id; ?>">View License</a> | <span class="view"><a href="<?php echo $single->link;?>" target="_blank" rel="permalink">View Referent</a></span></span>
      <?php 
		}
		
	  }else{
		
	  if($single->post_ip_warning == '1'){
		// Post has warning 
		}else{ ?>
     
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
		$ns_dc = $single->children('http://purl.org/dc/elements/1.1/'); ?>
    <a href="<?php echo $menu_page.'&post_type=c-page&author_id='.$ns_dc->creator_id; ?>"><?php echo $single->post_creator; ?></a>
    <?php } ?></td>
  <td class="type column-type" data-colname="License_Class"><?php if($license_class == 'uc-ut-um'){
		echo 'UC-UT-UM';
	}elseif($license_class == 'uc-at-um'){
		echo 'UC-AT-UM';	
	}else{
		echo 'N/A';	
	}?></td>
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
		 
	  }
	   ?>
</tbody>
<tfoot>
  <tr>
    <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label>
      <input id="cb-select-all-2" type="checkbox"></td>
    <th scope="col" class="manage-column column-title column-primary sortable desc"><a href="#asc"><span>Title</span><span class="sorting-indicator"></span></a></th>
	<th scope="col" class="manage-column column-ref_creator">Referent Creator</th>
    <th scope="col" class="manage-column column-author">Author</th>
    <th scope="col"  class="manage-column column-type">License Class </th>
    <?php 
		  $order = (isset($_GET['order'])) ? $_GET['order'] : '';
	   	  if($order == 'DESC'){ $order = 'ASC'; }else{ $order = 'DESC'; }
		  ?>
    <th scope="col" class="manage-column column-date sortable <?php echo strtolower($order); ?>"><a href="<?php echo $menu_page.'&post_type=c-page&orderby=date&order='.$order; ?>"><span>Date</span><span class="sorting-indicator"></span></a></th>
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
		 ?>
      <a class="prev-page" href="<?php echo $menu_page.'&'.$prev_link; ?>">‹</a>
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
	  }
	 ?>
    <br class="clear">
  </div>
</form>
<script>
// Call Quick Edit
jQuery( ".editinline" ).click(function() {
	var postid = jQuery(this).data('postid');
	jQuery('.iedit').show();
	jQuery('.quick-edit-row-post').hide();
	jQuery( "#edit-"+postid ).show();
	jQuery( "#post-"+postid ).hide();
	//post-883
});
// Cancel Quick Edit
jQuery(".button.cancel").click(function(){
	var postid = jQuery(this).data('postid');
	jQuery( "#edit-"+postid ).hide();
	jQuery( "#post-"+postid ).show();
});
// Preloader Start
jQuery(document).ajaxStart(function(){
    jQuery(".spinner").css("visibility", "visible");
});
// Preloader Ends
jQuery(document).ajaxComplete(function(){
    jQuery(".spinner").css("visibility", "hidden");
});
// Quick Edit Function
jQuery(function () {
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
});
// Close the Popup
jQuery(document).ready(function() {
  jQuery('.close-pbox').click(function(){
		jQuery('.cl_popup').hide();
});  
});
// Quick Edit Call Function
function cl_edit_action(event){
	var post_id = event.data.post_my_id; 
	jQuery('.cl_popup').hide();
	jQuery('.iedit').show();
	jQuery('.quick-edit-row-post').hide();
	jQuery( "#edit-"+post_id ).show();
	jQuery( "#post-"+post_id ).hide();
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
	
	jQuery('html, body').animate({
        scrollTop: jQuery("#edit-"+post_id).offset().top-60}, 2000);
}
	jQuery('.cl_publish').click(function(){
		var postid = jQuery(this).data('postid');
		var license_class = jQuery(this).data("ltype");
		var taxonomy_permission = jQuery(this).data("tpermission");

			
		var edit_html,license_msg;
			
		if(taxonomy_permission == 'AddToTaxonomy'){
			edit_html = "<a class='button button edit_content_go'>Edit</a>";
		}else if(taxonomy_permission == 'ModifyTaxonomy'){
			edit_html = "<a class='button button edit_content_go'>Edit</a>";
		}else if(taxonomy_permission == 'non-editable'){
			edit_html = "<a class='button button edit_content_go'>Edit</a>";
		}
		
		if(taxonomy_permission == 'AddToTaxonomy'){
			license_msg = "You can append categories and tags before publishing it";
		}else if(taxonomy_permission == 'ModifyTaxonomy'){
			license_msg = "You can edit categories and tags before publishing it";
		}else if(taxonomy_permission == 'non-editable'){
			license_msg = "You can edit date before publishing it";	 
		}
		
		
		var hyperlink_post = '<?php print wp_nonce_url($menu_page.'&action=sync', 'content_get_post', '_action_nonce_get');  ?>';
		jQuery('.cl_popup').show();
		jQuery('.cl_popup .close-pbox').show();
		jQuery('.cl_popup .pub_later').hide();
		
		jQuery('.cl_message').html(license_msg);
		
		jQuery('.cl_actions').html(edit_html+"<a class='button button-primary' href='"+hyperlink_post+"&p_sync="+postid+"'>Publish</a>");
		
		jQuery('.edit_content_go').click({post_my_id: postid},cl_edit_action);
		
	});
	
		function quick_create_content(){
		
	
		var postid = '<?php echo $_GET['clink_content']; ?>';
		var license_class = '<?php echo $license_class_post_qc; ?>';
		var taxonomy_permission = '<?php echo $license_taxonomy_permission_qc; ?>';
		
		
		var edit_html,license_msg;
			
		if(taxonomy_permission == 'AddToTaxonomy'){
			edit_html = "<a class='button button edit_content_go'>Edit</a>";
		}else if(taxonomy_permission == 'ModifyTaxonomy'){
			edit_html = "<a class='button button edit_content_go'>Edit</a>";
		}else if(taxonomy_permission == 'non-editable'){
			edit_html = "<a class='button button edit_content_go'>Edit</a>";
		}
		
		
		license_msg = '<a href="https://docs.clink.media/wordpress-plugins/creations/linked-creation" target="_blank">Learn more about publishing Linked Creation <span class="dashicons dashicons-external"></span></a>';
				
		var hyperlink_post = '<?php print wp_nonce_url($menu_page.'&action=sync', 'content_get_post', '_action_nonce_get');  ?>';
		
		jQuery('.cl_popup').show(200);
		jQuery('.cl_popup .close-pbox').hide();
		jQuery('.cl_popup .pub_later').show();
		
				
		jQuery('.cl_message').html(license_msg);
		
		jQuery('.cl_actions').html(edit_html+"<a class='button button-primary' href='"+hyperlink_post+"&p_sync="+postid+"'>Publish</a> <a class='button  pub_later'>Later</a>");
		jQuery('.edit_content_go').click({post_my_id: postid},cl_edit_action);
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
	
</script>
<?php 
echo '</div>'; ?>
<?php // End of the CLink Admin page
	do_action( 'wpclink_after_admin_page'); ?>
<?php echo '</div>';
}