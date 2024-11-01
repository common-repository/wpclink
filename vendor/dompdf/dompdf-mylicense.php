<?php
/**
 * CLink My License DOMPDF extention
 *
 * @package CLink
 * @subpackage Content Manager
 */
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// include autoloader
require_once 'autoload.inc.php';
// reference the Dompdf namespace
use Dompdf\Dompdf;
// License ID
$license_id = (string)$_GET['license_my_show_id'];
$license_data = wpclink_get_license_linked($license_id);
$html = wpclink_get_license_meta($license_id,'license',true);
// Watermark
$watermark_image = esc_url( plugins_url( 'wpclink/public/images/clinks-logo-alp.jpg', dirname(WPCLINK_MAIN_FILE) ) );
$image_path = esc_url( plugins_url( 'wpclink/admin/', dirname(WPCLINK_MAIN_FILE) ) );
$html = str_replace(array('[esign_watermark]','[path]'),array($watermark_image,$image_path),$html);
// instantiate and use the dompdf class
$dompdf = new Dompdf(array('enable_remote' => true));
$dompdf->loadHtml(stripslashes(htmlspecialchars_decode('<div>'.wpclink_do_icon($html,'0 0 -1px').'</div>',ENT_NOQUOTES)));
//(Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');
// Render the HTML as PDF
$dompdf->render();
if(isset($_GET['download'])){
	$dompdf->stream("License.pdf");	
}else{
// Output the generated PDF to Browser
$dompdf->stream("License.pdf", array("Attachment" => false));
}
