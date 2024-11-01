<?php
/**
 * CLink Core Functions
 *
 * CLink basic functions 
 *
 * @package CLink
 * @subpackage System
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Delete CLink Option
 * 
 * @param integer $option option id
 * 
 * @return integer
 */
function wpclink_delete_option( $option ) {
    global $wpdb;
	
	// Table Prefix
    $cl_table_name = $wpdb->prefix . 'wpclink_options'; 
 
    $option = trim( $option );
    if ( empty( $option ) )
        return false;
 
    wp_protect_special_option( $option );
 
    // Get the ID, if no ID then return
    $row = $wpdb->get_row( $wpdb->prepare( "SELECT autoload FROM $cl_table_name WHERE option_name = %s", $option ) );
    if ( is_null( $row ) )
        return false;
 
    /**
     * Fires immediately before an option is deleted.
     *
     * @since 2.9.0
     *
     * @param string $option Name of the option to delete.
     */
    do_action( 'delete_option', $option );
 
    $result = $wpdb->delete( $cl_table_name, array( 'option_name' => $option ) );
    if ( ! wp_installing() ) {
        if ( 'yes' == $row->autoload ) {
            $alloptions = wp_load_alloptions();
            if ( is_array( $alloptions ) && isset( $alloptions[$option] ) ) {
                unset( $alloptions[$option] );
                wp_cache_set( 'alloptions', $alloptions, 'options' );
            }
        } else {
            wp_cache_delete( $option, 'options' );
        }
    }
    if ( $result ) {
 
        /**
         * Fires after a specific option has been deleted.
         *
         * The dynamic portion of the hook name, `$option`, refers to the option name.
         *
         * @since 3.0.0
         *
         * @param string $option Name of the deleted option.
         */
        do_action( "delete_option_{$option}", $option );
 
        /**
         * Fires after an option has been deleted.
         *
         * @since 2.9.0
         *
         * @param string $option Name of the deleted option.
         */
        do_action( 'deleted_option', $option );
        return true;
    }
    return false;
}
/**
 * Update CLink Option
 * 
 * @param string $option option name
 * @param string $value option value
 * @param string $autoload autoload yes/no
 * 
 * @return boolean
 */
function wpclink_update_option( $option, $value, $autoload = 'no' ) {
    global $wpdb;
	
	// Table Prefix
    $cl_table_name = $wpdb->prefix . 'wpclink_options'; 
 
    $option = trim($option);
    if ( empty($option) )
        return false;
 
    wp_protect_special_option( $option );
 
    if ( is_object( $value ) )
        $value = clone $value;
 
    $value = sanitize_option( $option, $value );
    $old_value = wpclink_get_option( $option );
 
    /**
     * Filters a specific option before its value is (maybe) serialized and updated.
     *
     * The dynamic portion of the hook name, `$option`, refers to the option name.
     *
     *
     * @param mixed  $value     The new, unserialized option value.
     * @param mixed  $old_value The old option value.
     * @param string $option    Option name.
     */
    $value = apply_filters( "pre_update_option_{$option}", $value, $old_value, $option );
 
    /**
     * Filters an option before its value is (maybe) serialized and updated.
     *
     *
     * @param mixed  $value     The new, unserialized option value.
     * @param string $option    Name of the option.
     * @param mixed  $old_value The old option value.
     */
    $value = apply_filters( 'pre_update_option', $value, $option, $old_value );
 
    /*
     * If the new and old values are the same, no need to update.
     *
     * Unserialized values will be adequate in most cases. If the unserialized
     * data differs, the (maybe) serialized data is checked to avoid
     * unnecessary database calls for otherwise identical object instances.
     *
     * See https://core.trac.wordpress.org/ticket/38903
     */
    if ( $value === $old_value || maybe_serialize( $value ) === maybe_serialize( $old_value ) ) {
        return false;
    }
 
    /** This filter is documented in wp-includes/option.php */
    if ( apply_filters( "default_option_{$option}", false, $option, false ) === $old_value ) {
        // Default setting for new options is 'yes'.
        if ( null === $autoload ) {
            $autoload = 'yes';
        }
 
        return wpclink_add_option( $option, $value, '', $autoload );
    }
 
    $serialized_value = maybe_serialize( $value );
 
    /**
     * Fires immediately before an option value is updated.
     *
     *
     * @param string $option    Name of the option to update.
     * @param mixed  $old_value The old option value.
     * @param mixed  $value     The new option value.
     */
	 
    do_action( 'update_option', $option, $old_value, $value );
 
    $update_args = array(
        'option_value' => $serialized_value,
    );
 
    if ( null !== $autoload ) {
        $update_args['autoload'] = ( 'no' === $autoload || false === $autoload ) ? 'no' : 'yes';
    }
 
    $result = $wpdb->update( $cl_table_name, $update_args, array( 'option_name' => $option ) );
    if ( ! $result )
        return false;
 
    $notoptions = wp_cache_get( 'notoptions', 'options' );
    if ( is_array( $notoptions ) && isset( $notoptions[$option] ) ) {
        unset( $notoptions[$option] );
        wp_cache_set( 'notoptions', $notoptions, 'options' );
    }
 
    if ( ! wp_installing() ) {
        $alloptions = wp_load_alloptions();
        if ( isset( $alloptions[$option] ) ) {
            $alloptions[ $option ] = $serialized_value;
            wp_cache_set( 'alloptions', $alloptions, 'options' );
        } else {
            wp_cache_set( $option, $serialized_value, 'options' );
        }
    }
 
    /**
     * Fires after the value of a specific option has been successfully updated.
     *
     * The dynamic portion of the hook name, `$option`, refers to the option name.
     *
     *
     * @param mixed  $old_value The old option value.
     * @param mixed  $value     The new option value.
     * @param string $option    Option name.
     */
    do_action( "update_option_{$option}", $old_value, $value, $option );
 
    /**
     * Fires after the value of an option has been successfully updated.
     *
     *
     * @param string $option    Name of the updated option.
     * @param mixed  $old_value The old option value.
     * @param mixed  $value     The new option value.
     */
    do_action( 'updated_option', $option, $old_value, $value );
    return true;
}
function wpclink_add_option( $option, $value = '', $deprecated = '', $autoload = 'yes' ) {
    global $wpdb;
	
	// Table Prefix
    $cl_table_name = $wpdb->prefix . 'wpclink_options'; 
 
    if ( !empty( $deprecated ) )
        _deprecated_argument( __FUNCTION__, '2.3.0' );
 
    $option = trim($option);
    if ( empty($option) )
        return false;
 
    wp_protect_special_option( $option );
 
    if ( is_object($value) )
        $value = clone $value;
 
    $value = sanitize_option( $option, $value );
 
    // Make sure the option doesn't already exist. We can check the 'notoptions' cache before we ask for a db query
    $notoptions = wp_cache_get( 'notoptions', 'options' );
    if ( !is_array( $notoptions ) || !isset( $notoptions[$option] ) )
        /** This filter is documented in wp-includes/option.php */
        if ( apply_filters( "default_option_{$option}", false, $option, false ) !== wpclink_get_option( $option ) )
            return false;
 
    $serialized_value = maybe_serialize( $value );
    $autoload = ( 'no' === $autoload || false === $autoload ) ? 'no' : 'yes';
 
    /**
     * Fires before an option is added.
     *
     *
     * @param string $option Name of the option to add.
     * @param mixed  $value  Value of the option.
     */
    do_action( 'add_option', $option, $value );
 
    $result = $wpdb->query( $wpdb->prepare( "INSERT INTO `$cl_table_name` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", $option, $serialized_value, $autoload ) );
    if ( ! $result )
        return false;
 
    if ( ! wp_installing() ) {
        if ( 'yes' == $autoload ) {
            $alloptions = wp_load_alloptions();
            $alloptions[ $option ] = $serialized_value;
            wp_cache_set( 'alloptions', $alloptions, 'options' );
        } else {
            wp_cache_set( $option, $serialized_value, 'options' );
        }
    }
 
    // This option exists now
    $notoptions = wp_cache_get( 'notoptions', 'options' ); // yes, again... we need it to be fresh
    if ( is_array( $notoptions ) && isset( $notoptions[$option] ) ) {
        unset( $notoptions[$option] );
        wp_cache_set( 'notoptions', $notoptions, 'options' );
    }
 
    /**
     * Fires after a specific option has been added.
     *
     * The dynamic portion of the hook name, `$option`, refers to the option name.
     *
     *
     * @param string $option Name of the option to add.
     * @param mixed  $value  Value of the option.
     */
    do_action( "add_option_{$option}", $option, $value );
 
    /**
     * Fires after an option has been added.
     *
     *
     * @param string $option Name of the added option.
     * @param mixed  $value  Value of the option.
     */
    do_action( 'added_option', $option, $value );
    return true;
}
/**
 * Get CLink Option
 * 
 * @param string $option option name
 * @param boolean $default  default true/false
 * 
 * @return mixed
 */
function wpclink_get_option( $option, $default = false ) {
    global $wpdb;
	
	// Table Prefix
    $cl_table_name = $wpdb->prefix . 'wpclink_options'; 
 
    $option = trim( $option );
    if ( empty( $option ) )
        return false;
 
    /**
     * Filters the value of an existing option before it is retrieved.
     *
     * The dynamic portion of the hook name, `$option`, refers to the option name.
     *
     * Passing a truthy value to the filter will short-circuit retrieving
     * the option value, returning the passed value instead.
     *
     *
     * @param bool|mixed $pre_option Value to return instead of the option value.
     *                               Default false to skip it.
     * @param string     $option     Option name.
     */
    $pre = apply_filters( "pre_option_{$option}", false, $option );
    if ( false !== $pre )
        return $pre;
 
    if ( defined( 'WP_SETUP_CONFIG' ) )
        return false;
 
    // Distinguish between `false` as a default, and not passing one.
    $passed_default = func_num_args() > 1;
 
    if ( ! wp_installing() ) {
        // prevent non-existent options from triggering multiple queries
        $notoptions = wp_cache_get( 'notoptions', 'options' );
        if ( isset( $notoptions[ $option ] ) ) {
            
            return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
        }
 
        $alloptions = wp_load_alloptions();
 
        if ( isset( $alloptions[$option] ) ) {
            $value = $alloptions[$option];
        } else {
            $value = wp_cache_get( $option, 'options' );
 
            if ( false === $value ) {
                $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $cl_table_name WHERE option_name = %s LIMIT 1", $option ) );
 
                // Has to be get_row instead of get_var because of funkiness with 0, false, null values
                if ( is_object( $row ) ) {
                    $value = $row->option_value;
                    wp_cache_add( $option, $value, 'options' );
                } else { // option does not exist, so we must cache its non-existence
                    if ( ! is_array( $notoptions ) ) {
                         $notoptions = array();
                    }
                    $notoptions[$option] = true;
                    wp_cache_set( 'notoptions', $notoptions, 'options' );
 
                    /** This filter is documented in wp-includes/option.php */
                    return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
                }
            }
        }
    } else {
        $suppress = $wpdb->suppress_errors();
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $cl_table_name WHERE option_name = %s LIMIT 1", $option ) );
        $wpdb->suppress_errors( $suppress );
        if ( is_object( $row ) ) {
            $value = $row->option_value;
        } else {
            /** This filter is documented in wp-includes/option.php */
            return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
        }
    }
 
    // If home is not set use siteurl.
    if ( 'home' == $option && '' == $value )
        return wpclink_get_option( 'siteurl' );
 
    if ( in_array( $option, array('siteurl', 'home', 'category_base', 'tag_base') ) )
        $value = untrailingslashit( $value );
 
    /**
     * Filters the value of an existing option.
     *
     * The dynamic portion of the hook name, `$option`, refers to the option name.
     *
     *
     * @param mixed  $value  Value of the option. If stored serialized, it will be
     *                       unserialized prior to being returned.
     * @param string $option Option name.
     */
    return apply_filters( "option_{$option}", maybe_unserialize( $value ), $option );
}
/**
 * Check AMP Plugin is Active
 * 
 * @return boolean
 */
function wpclink_is_amp_active(){
	
	if(in_array('amp/amp.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
    	return true;
	}
	
	return false;
}
/**
 * Check If AMP Plugin is Inactive
 * 
 * @return boolean
 */
function wpclink_is_amp_inactive(){
	
	if(wpclink_is_amp_active() == false || (isset($_GET['clink']) and $_GET['clink'] == 'offer') || isset($_GET['clink_media_license'])){
		return true;
	}
	return false;
}