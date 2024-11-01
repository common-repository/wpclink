<?php
/**
 * CLink Delete Unique Url On Plugin Deactivation
 * 
 */
function wpclink_delete_instance_keys(){
	
	// Delete Keys
	wpclink_delete_option( 'DYNAMIC_URL_POSTFIX' );
	wpclink_delete_option( 'DYNAMIC_URL_POSTFIX_PASS' );
	wpclink_delete_option( 'DYNAMIC_URL_POSTFIX_SECRET_KEY' );
	wpclink_delete_option( 'DYNAMIC_URL_POSTFIX_DATA_KEY' );
}
// Register delete unique url on plugin deactivation function
register_deactivation_hook( WPCLINK_MAIN_FILE, 'wpclink_delete_instance_keys' );
/**
 * CLink Deactivation Delete Welcome Status
 */
function wpclink_deactivate_plugin() {
	
	// Delete
	wpclink_delete_option('wpclink_welcome_status');
}
register_deactivation_hook( WPCLINK_MAIN_FILE, 'wpclink_deactivate_plugin' );