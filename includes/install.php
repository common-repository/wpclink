<?php
/**
 * CLink Installation Functions
 *
 * CLink installation functions first time activate 
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * CL Tables Install
 */
function wpclink_option_table_install(){
    global $wpdb;
    global $wpCLink_db_version;
	
	// Table Prefix
    $table_name = $wpdb->prefix . 'wpclink_options'; 
	// Query
    $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
      option_id bigint(20) NOT NULL AUTO_INCREMENT, 
      option_name VARCHAR(191) NOT NULL, 
	  option_value longtext NOT NULL, 
	  autoload VARCHAR(20) NOT NULL DEFAULT 'no', 
      PRIMARY KEY (option_id)
    );";
    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
	
	
	// Table Prefix
    $table_licensemeta = $wpdb->prefix . 'wpclink_licensemeta'; 
	// Query
    $sql_licensemeta = "CREATE TABLE IF NOT EXISTS " . $table_licensemeta . " (
      meta_id bigint(20) NOT NULL AUTO_INCREMENT, 
	  license_id bigint(20) NOT NULL, 
	  meta_key VARCHAR(255) NOT NULL, 
	  meta_value longtext NOT NULL, 
      PRIMARY KEY (meta_id)
    );";
	
	dbDelta($sql_licensemeta);
	
	// Table Prefix
    $table_license = $wpdb->prefix . 'wpclink_licenses';  
	// Query
    $sql_license = "CREATE TABLE IF NOT EXISTS " . $table_license . " (
      license_id bigint(20) NOT NULL AUTO_INCREMENT,
	  post_id VARCHAR(255) DEFAULT NULL, 
	  mode VARCHAR(255) NOT NULL, 
	  license_class VARCHAR(255) NOT NULL, 
	  license_version VARCHAR(255) DEFAULT NULL,
	  rights_transaction_ID VARCHAR(255) DEFAULT NULL, 
	  license_date datetime NULL DEFAULT NULL,  
	  site_url VARCHAR(255) NOT NULL, 
	  site_IP VARCHAR(255) NOT NULL, 
	  verification_status VARCHAR(255) DEFAULT NULL, 
      PRIMARY KEY (license_id)
    );";
	
	dbDelta($sql_license);
	
	
	// Table Prefix
    $table_esign = $wpdb->prefix . 'wpclink_esigns'; 
	// Query
    $table_esign = "CREATE TABLE IF NOT EXISTS " . $table_esign . " (
      esign_id bigint(20) NOT NULL AUTO_INCREMENT, 
	  license_id VARCHAR(255) DEFAULT '0',  
	  post_id VARCHAR(255) DEFAULT '0',  	  
	  view_date datetime NULL DEFAULT NULL,  
	  esign_date datetime NULL DEFAULT NULL,   	
	  esign_by VARCHAR(255) NOT NULL, 
	  esign_email VARCHAR(255) NOT NULL, 	 
	  esign_IP VARCHAR(255) NOT NULL, 	 
	  esign_reason VARCHAR(255) NOT NULL,	 
	  esign_html longtext DEFAULT '',   	   
  	  esign_key VARCHAR(255) NOT NULL, 	  
      PRIMARY KEY (esign_id)
    );";
	
	dbDelta($table_esign);
	
	
	
}
register_activation_hook(WPCLINK_MAIN_FILE, 'wpclink_option_table_install');
/**
 * CLink Activation Keys
 * 
 */
function wpclink_generate_instance_keys() {
	$active_cl = wpclink_get_option('DYNAMIC_URL_POSTFIX');
	$active_cl_pass = wpclink_get_option('DYNAMIC_URL_POSTFIX_PASS');
	
	if(!empty($active_cl) and !empty($active_cl_pass)){
	}else{
		$go_live_link = wpclink_go_live_link(16);
		$go_live_link_pass = wpclink_go_live_link(16);
		$go_live_link_secret = wpclink_go_live_link(32);
		wpclink_add_option( 'DYNAMIC_URL_POSTFIX', $go_live_link );
		wpclink_add_option( 'DYNAMIC_URL_POSTFIX_PASS', $go_live_link_pass );
		wpclink_add_option( 'DYNAMIC_URL_POSTFIX_SECRET_KEY', $go_live_link_secret );
		$link_1 = wpclink_get_option('DYNAMIC_URL_POSTFIX');
		$link_2 = wpclink_get_option('DYNAMIC_URL_POSTFIX_PASS');
		$secret_key = wpclink_get_option('DYNAMIC_URL_POSTFIX_SECRET_KEY');
		$data_key = '?go_live='.$link_1.'&pass_key='.$link_2;		
		$encrypted_string = wpclink_encrypt($data_key, $secret_key);
		wpclink_add_option( 'DYNAMIC_URL_POSTFIX_DATA_KEY', $encrypted_string );
	}
}
register_activation_hook( WPCLINK_MAIN_FILE, 'wpclink_generate_instance_keys' );
/**
 * CLink Create Tokens Table
 * 
 * Token table for reuse content
 * 
 */
function wpclink_add_db_tables(){
    global $wpdb;
    global $wpCLink_db_version;
	
		
	// Table Prefix
    $table_name_token = $wpdb->prefix . 'wpclink_tokens'; 
	
	
	$sql_3 = "CREATE TABLE IF NOT EXISTS " . $table_name_token . " (
      token_id int(11) NOT NULL AUTO_INCREMENT, 
  	  post_id VARCHAR(100) NOT NULL, 
	  creation_uri VARCHAR(100) NOT NULL, 
      linked_site_url tinytext NOT NULL, 
  	  linked_site_domain tinytext NOT NULL, 
	  linked_site_IP VARCHAR(100) NOT NULL, 
	  token_type VARCHAR(100) NOT NULL, 
	  token_date datetime NOT NULL DEFAULT NOW(), 	  
	  token VARCHAR(100) NOT NULL,  
	  token_expiration BOOL NOT NULL DEFAULT '0', 
	  license_delivery_date datetime NULL DEFAULT NULL,  
      PRIMARY KEY  (token_id) 
	  );";
	
	dbDelta($sql_3);
		
    // save current database version for later use (on upgrade)
    wpclink_add_option('db_version', $wpCLink_db_version);
    $installed_ver = wpclink_get_option('wpclink_db_ver');
    if ($installed_ver != $wpCLink_db_version) {
        
		
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
	
	// Table Prefix
    $table_name_token = $wpdb->prefix . 'wpclink_tokens'; 
	
	
	$sql_3 = "CREATE TABLE IF NOT EXISTS " . $table_name_token . " (
      token_id int(11) NOT NULL AUTO_INCREMENT, 
  	  post_id VARCHAR(100) NOT NULL, 
	  creation_uri VARCHAR(100) NOT NULL, 
      linked_site_url tinytext NOT NULL, 
  	  linked_site_domain tinytext NOT NULL, 
	  linked_site_IP VARCHAR(100) NOT NULL, 
	  token_type VARCHAR(100) NOT NULL, 
	  token_date datetime NOT NULL DEFAULT NOW(), 	  
	  token VARCHAR(100) NOT NULL,  
	  token_expiration BOOL NOT NULL DEFAULT '0', 
	  license_delivery_date datetime NULL DEFAULT NULL,  
      PRIMARY KEY  (token_id) 
	  );";
	
	dbDelta($sql_3);
	
	
	
        // notice that we are updating option, rather than adding it
        wpclink_update_option('db_version', $wpCLink_db_version);
    }
}
// Register clink token table on activation
register_activation_hook(WPCLINK_MAIN_FILE, 'wpclink_add_db_tables');
