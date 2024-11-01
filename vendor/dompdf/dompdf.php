<?php
/**
 * CLink License DOMPDF extention
 *
 * @package CLink
 * @subpackage Content Manager
 */
// Direct Access Not Allowed
defined( 'ABSPATH' )or die( 'No script kiddies please!' );
// include autoloader
require_once 'autoload.inc.php';
// reference the Dompdf namespace
use Dompdf\ Dompdf;
// License ID
$license_id = $_GET[ 'license_show_id' ];
global $wpdb;
$table_license = $wpdb->prefix . 'wpclink_licenses';
$license_data = $wpdb->get_row( "SELECT * FROM $table_license WHERE license_id = '$license_id' AND mode = 'referent' ", ARRAY_A );
// User Data
$licensee_linked_variables = wpclink_get_license_meta( $license_id, 'licensee_linked_variables', true );
$licensee_linked_variables = unserialize( $licensee_linked_variables );
$firstname = $licensee_linked_variables[ 'licensee_first_name' ];
$lastname = $licensee_linked_variables[ 'licensee_last_name' ];
$displayname = $licensee_linked_variables[ 'licensee_display_name' ];
$email = $licensee_linked_variables[ 'licensee_email' ];
$identifier = $licensee_linked_variables[ 'licensee_identifier' ];
$esign_data = wpclink_get_esign_by_license_id( $license_id );
$esign_template = $esign_data[ 'esign_html' ];
$client_site = $license_data[ 'site_url' ];
$license_date = $license_data[ 'license_date' ];
$license_date_formate = date_create($license_date);
$license_date_final = date_format($license_date_formate, 'M d, Y');
$watermark_image = esc_url( plugins_url( 'wpclink/public/images/clinks-logo-alp.jpg', dirname( WPCLINK_MAIN_FILE ) ) );
$image_path = esc_url( plugins_url( 'wpclink/admin/', dirname(WPCLINK_MAIN_FILE) ) );
$search = array( '[client_site]', '[linked_creator_firstname]', '[linked_creator_lastname]', '[linked_creator_display_name]', '[linked_creator_email]', '[linked_creator_ID]', '[esign_watermark]','[license_date]','[path]' );
$replace = array( $client_site, $firstname, $lastname, $displayname, $email, wpclink_do_icon($identifier), $watermark_image, $license_date_final,$image_path );
$license_data = wpclink_get_license_referent( $license_id );
$content_id = $license_data[ 'post_id' ];
$esign_ready = str_replace( '[esign_watermark]', $watermark_image, $esign_template );
$html = str_replace( $search, $replace, nl2br( wpclink_prepare_license_offer( $content_id, false ) . wpclink_do_icon($esign_ready,'0 0 -1px') ) );
// instantiate and use the dompdf class
$dompdf_clink = new Dompdf( array( 'enable_remote' => true ) );
$dompdf_clink->loadHtml( $html );
// (Optional) Setup the paper size and orientation
$dompdf_clink->setPaper( 'A4', 'portrait' );
// Render the HTML as PDF
$dompdf_clink->render();
if ( isset( $_GET[ 'download' ] ) ) {
	$dompdf_clink->stream( "License.pdf" );
} else {
	// Output the generated PDF to Browser
	$dompdf_clink->stream( "License.pdf", array( "Attachment" => false ) );
}