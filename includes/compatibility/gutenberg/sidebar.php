<?php
/**
 * CLink Sidebar Register
 *
 * Register scripts for sidebar
 *
 * @package CLink
 * @subpackage System
 */

/**
 * CLink Sidebar Enqueue JS
 *
 */
function wpclink_register_clink_panel_script() {
    
    // Do not run on full site editor WP 5.9
    if(isset($_GET['postId'])) return false;
    
	  wp_register_script(
        'wpclink-plugin-sidebar-js',
        plugins_url( 'plugin-sidebar.js?ver='.WPCLINK_VERSION, __FILE__ ),
        array(
            'wp-plugins',
            'wp-edit-post',
            'wp-element',
            'wp-components'
        )
    );
	
	
	 wp_register_script(
        'wpclink-linked-media-detects-js',
        plugins_url( 'plugin-linked-media-detects.js?ver='.WPCLINK_VERSION, __FILE__ ),
        array(
            'wp-plugins',
            'wp-edit-post',
            'wp-element',
            'wp-components'
        )
    );
	
    wp_enqueue_script( 'wpclink-plugin-sidebar-js' );
	wp_enqueue_script( 'wpclink-linked-media-detects-js' );
	
}
add_action( 'enqueue_block_editor_assets', 'wpclink_register_clink_panel_script' );

/**
 * CLink Sidebar Enqueue CSS
 *
 */
function wpclink_register_clink_panel_style() {
	
if ( ! is_admin() ) {

 
} else {
    wp_register_style(
        'wpclink-plugin-sidebar-css',
        plugins_url( 'plugin-sidebar.css?uqid='.uniqid('_'), __FILE__ )
    );
}
	
	
	
    wp_enqueue_style( 'wpclink-plugin-sidebar-css' );
}
add_action( 'enqueue_block_assets', 'wpclink_register_clink_panel_style' );