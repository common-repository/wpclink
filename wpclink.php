<?php 
/**
* wpCLink
*
* @package CLink
* @copyright Copyright (C) 2022, CLink Media, Inc. - support@clink.media
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 or higher
*
* @wpCLink
* Plugin Name: wpCLink
* Version: 0.9.8.133
* Plugin URI: https://clink.media
* Description: Content Link Beyond Hyperlink: formed under license; registered rights information; content delivered directly to the licensees site.
* Author: Team CLink
* Author URI: https://clink.media/team-clink
* Text Domain: cl_text
* License: GPL v2
* WC requires at least: 5.0
* WC tested up to: 5.9
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*/
// this is only needed for systems that the .htaccess won't work on
defined('ABSPATH') or die('No script kiddies please!');

if ( !defined( 'WPCLINK_VERSION' ) ) {
 define( 'WPCLINK_VERSION', '0.9.8.133' );
}
if ( !defined( 'WPCLINK_UI_VERSION' ) ) {
 define( 'WPCLINK_UI_VERSION', 'cui-0-9' );
}
if ( !defined( 'WPCLINK_DATABASE_NAME' ) ) {
 define( 'WPCLINK_DATABASE_NAME', '0.9' );
}
if ( !defined( 'WPCLINK_BASENAME' ) ) {
 define( 'WPCLINK_BASENAME', plugin_basename( __FILE__ ) );
}
if ( !defined( 'WPCLINK_MAIN_FILE' ) ) {
 define( 'WPCLINK_MAIN_FILE',  __FILE__  );
}
if ( !defined( 'WPCLINK_ID_URL' ) ) {
 define( 'WPCLINK_ID_URL', 'https://clink.id' );
}
if ( !defined( 'WPCLINK_CARE_SERVER' ) ) {
 define( 'WPCLINK_CARE_SERVER', 'https://us-customers.clink.id' );
}
if ( !defined( 'WPCLINK_PARTY_API' ) ) {
 define( 'WPCLINK_PARTY_API', 'https://us-customers.clink.id/?wpclink-contact=1' );
}
if ( !defined( 'WPCLINK_RIGHT_TRANSACTION_API' ) ) {
 define( 'WPCLINK_RIGHT_TRANSACTION_API', 'https://us-customers.clink.id/?wpclink-right-transaction=1' );
}
if ( !defined( 'WPCLINK_PARTY_APPROVE_API' ) ) {
 define( 'WPCLINK_PARTY_APPROVE_API', 'https://us-customers.clink.id/?wpclink-approve-contact=1' );
}
if ( !defined( 'WPCLINK_CREATOR_API' ) ) {
 define( 'WPCLINK_CREATOR_API', 'https://us-customers.clink.id/?wpclink-creator=1' );
}
if ( !defined( 'WPCLINK_CREATOR_APPROVE_API' ) ) {
 define( 'WPCLINK_CREATOR_APPROVE_API', 'https://us-customers.clink.id/?wpclink-approve-creator=1' );
}
if ( !defined( 'WPCLINK_CREATION_API' ) ) {
 define( 'WPCLINK_CREATION_API', 'https://us-customers.clink.id/?wpclink-content=1' );
}
if ( !defined( 'WPCLINK_ISCC_API' ) ) {
 define( 'WPCLINK_ISCC_API', 'https://us-customers.clink.id/?wpclink-iscc=1' );
}
if ( !defined( 'WPCLINK_MEDIA_API' ) ) {
 define( 'WPCLINK_MEDIA_API', 'https://us-customers.clink.id/?wpclink-media=1' );
}
if ( !defined( 'WPCLINK_API_TIMEOUT' ) ) {
 define( 'WPCLINK_API_TIMEOUT', '45' );
}
if ( !defined( 'WPCLINK_DEBUG' ) ) {
 define( 'WPCLINK_DEBUG', true );
}
if ( !defined( 'WPCLINK_ERROR_REPORTING' ) ) {
	error_reporting(0);
}
define('WPCLINK_LOGO_BASE64','data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAyMDAxMDkwNC8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMHB4IiBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCBtZWV0Ij4KIDxnIGZpbGw9IiNmZmYiPgogIDxwYXRoIGQ9Ik0xMzkuNSA0MDcgYy0xOC43IC0xLjYgLTM3LjUgLTcgLTU0LjkgLTE1LjcgLTI4LjIgLTE0LjEgLTUyLjggLTM4LjcgLTY3LjEgLTY3LjMgLTEwLjkgLTIxLjcgLTE1LjYgLTQxLjggLTE1LjYgLTY3LjEgMCAtMzAuOSA4LjQgLTU4LjIgMjYuMiAtODQuOSA4IC0xMi4xIDI2LjIgLTMwLjQgMzguNCAtMzguNiA0MC43IC0yNy43IDg5LjcgLTM0LjIgMTM1LjUgLTE4LjIgMTEuNCA0LjEgMTkuNCA4LjIgMjEuOSAxMS40IDUuMiA2LjYgNC41IDE0LjcgLTEuOCAyMC42IC01LjggNS41IC05LjggNS40IC0yMy40IC0wLjIgLTIzLjQgLTkuOCAtNTAuMiAtMTIuMSAtNzQuNCAtNi41IC0yMyA1LjMgLTQ3LjYgMjAuNCAtNjMuMSAzOC42IC01NCA2My40IC0yOSAxNjAuNSA0OC45IDE4OS45IDE0IDUuMiAyNi4xIDcuMyA0Mi40IDcuMyA1OC4yIC0wLjIgMTA3IC00MS42IDExNy4xIC05OS4zIDEuMyAtNy42IDEuNSAtMTMuMiAxLjEgLTI3LjkgLTAuNyAtMTkuNyAtMC40IC0yMS4zIDQuNSAtMjUuOCA2LjggLTYuMyAxOC45IC0zLjkgMjMuMyA0LjYgNC4xIDguMSA0LjUgNDAuOCAwLjUgNTkuMyAtMTAuMiA0OC4zIC00My40IDg4LjggLTg4LjcgMTA4LjMgLTIxIDkuMSAtNDggMTMuNSAtNzAuOCAxMS41eiIvPgogIDxwYXRoIGQ9Ik0zNDUuNSA0MDYuOSBjLTEyLjQgLTEgLTIyIC0zIC0zNS4xIC03LjQgLTEyLjggLTQuMiAtMjEuNiAtOC42IC0yNC4zIC0xMi4xIC04LjIgLTEwLjUgLTAuNiAtMjUgMTMgLTI0LjYgMS40IDAgNi44IDEuOSAxMiA0LjEgMTguNSA3LjggMzguNiAxMSA1Ny43IDkuMSAxMy45IC0xLjQgMjEuMSAtMy4xIDMzLjUgLTggMzcuMiAtMTQuNSA2NC4zIC00Ni45IDczIC04NyAyLjggLTEzLjIgMi44IC0zNC43IDAgLTQ4IC04LjcgLTQwLjkgLTM2LjYgLTczLjMgLTc1LjMgLTg3LjkgLTE0LjUgLTUuNCAtMjUuNiAtNy40IC00MiAtNy40IC01OC42IDAuMSAtMTA3LjQgNDEuMyAtMTE3LjYgOTkuMyAtMS4zIDcuNyAtMS41IDEzLjEgLTEgMjguMSAwLjYgMTguMiAwLjUgMTguNyAtMS43IDIxLjkgLTYuMiA5LjEgLTE5LjMgOS4yIC0yNS4yIDAuMSAtNS4xIC03LjcgLTUuOSAtMzkuNyAtMS41IC02MC4zIDExLjYgLTU0LjYgNTIuNSAtOTguOSAxMDUuNSAtMTE0LjMgMTMuNyAtMy45IDI2LjUgLTUuNyA0MiAtNS43IDE3LjIgMCAzMCAyIDQ1LjYgNy4yIDIzLjggNy45IDQyIDE5LjEgNTkuOSAzNyAyOSAyOC44IDQ0LjIgNjUuNCA0NC4xIDEwNiAwIDMxLjcgLTguMSA1Ny44IC0yNi4yIDg1IC04IDEyLjEgLTI2LjIgMzAuNCAtMzguNCAzOC42IC0yOS4zIDE5LjkgLTYzLjUgMjkuMSAtOTggMjYuM3oiLz4KIDwvZz4KPC9zdmc+');
/**
 * CLink Quota Get from Care.Clink.Meda (Current Edition is Free so no Quota is Required Default Quota is 200)
 * 
 * Current Edition is Free so No Quota is Require 
 * 
 * @return integer 200
 */
function wpclink_clink_domain_quota(){
	
	// Personal Edition
	return '200';
	
}
/**
 * CLink Get Plugin Data
 * 
 * @return array plugin data
 */
function wpclink_get_plugin_data() {
    $plugin_data = get_plugin_data( __FILE__ );
    return $plugin_data;
}
// Quota Response
global $wpCLink_quota_response;
// CLink DB Version
global $wpCLink_db_version;
// Quota
global $wpCLink_domain_quota;
// CLink Database Version
$wpCLink_db_version = '0.8';
$wpCLink_domain_quota = wpclink_clink_domain_quota();
// Core and main functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/core.php' );
// Initiaization functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/init.php' );
// Intallation functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/install.php' );
// Connectionn to registry functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/connection.php' );
// Encryption and decryption functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/crypt.php' );
// Canonica Verification
require_once( plugin_dir_path( __FILE__ ) . 'includes/canonical.php' );
// Ajax requests functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/ajax.php' );
// Conditional and database quries
require_once( plugin_dir_path( __FILE__ ) . 'includes/query.php' );
// Activation functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/activation.php' );
// License functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/license.php' );
// List of linked content
require_once( plugin_dir_path( __FILE__ ) . 'includes/linklist.php' );
// Menu and buttons functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/menu.php' );
// Error Handler
require_once( plugin_dir_path( __FILE__ ) . 'includes/error-handler.php' );
// Miscellaneous functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/misc.php' );
// Notification and messages functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/notification.php' );
// Reuse and Popup functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/reuse.php' );
// Reuse and media poup
require_once( plugin_dir_path( __FILE__ ) . 'includes/media-popup.php' );
// Reuse and licensable media
require_once( plugin_dir_path( __FILE__ ) . 'includes/licensable.php' );
// CLink party and creator users functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/user.php' );
// Process functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/process.php' );
// Content creation and link functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/creation.php' );
// Schedule of canonical verification functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/schedule.php' );
// Structure functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/structure.php' );
// API XML functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/xml.php' );
// Canonical verification functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/verification.php' );
// Token functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/token.php' );
// Electronic signature functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/esign.php' );
// Media
require_once( plugin_dir_path( __FILE__ ) . 'includes/media.php' );
// Scripts render functions
require_once( plugin_dir_path( __FILE__ ) . 'includes/script.php' );
// License Media
require_once( plugin_dir_path( __FILE__ ) . 'includes/license-media.php' );
// Uninstall
require_once( plugin_dir_path( __FILE__ ) . 'uninstall.php' );
// Gutenberg Compatible
require_once( plugin_dir_path( __FILE__ ) . 'includes/compatibility/gutenberg/sidebar.php' );
// Template Tag
require_once( plugin_dir_path( __FILE__ ) . 'includes/template-tags.php' );
// Admin Pages
// Preferences general
require_once( plugin_dir_path( __FILE__ ) . 'admin/preferences-general.php' );
// Preferences uders
require_once( plugin_dir_path( __FILE__ ) . 'admin/preferences-users.php' );
// Preferences license template
require_once( plugin_dir_path( __FILE__ ) . 'admin/preferences-licensetemplates.php' );
// Content referent posts
require_once( plugin_dir_path( __FILE__ ) . 'admin/creation-referent-posts.php' );
// Content registered posts
require_once( plugin_dir_path( __FILE__ ) . 'admin/creation-registered-posts.php' );
// Content registered pages
require_once( plugin_dir_path( __FILE__ ) . 'admin/creation-registered-pages.php' );
// Content registered media
require_once( plugin_dir_path( __FILE__ ) . 'admin/creation-registered-media.php' );
// Content referent pages
require_once( plugin_dir_path( __FILE__ ) . 'admin/creation-referent-pages.php' );
// Content referent media
require_once( plugin_dir_path( __FILE__ ) . 'admin/creation-referent-media.php' );
// Content linked pages
require_once( plugin_dir_path( __FILE__ ) . 'admin/creation-linked-pages.php' );
// Content linked posts
require_once( plugin_dir_path( __FILE__ ) . 'admin/creation-linked-posts.php' );
// Content linked posts
require_once( plugin_dir_path( __FILE__ ) . 'admin/creation-linked-media.php' );
// Links referent 
require_once( plugin_dir_path( __FILE__ ) . 'admin/links-inbound.php' );
// Links linked
require_once( plugin_dir_path( __FILE__ ) . 'admin/links-outbound.php' );
// Links linked add new offer
require_once( plugin_dir_path( __FILE__ ) . 'admin/links-outbound-new.php' );
// Links referent canonical
require_once( plugin_dir_path( __FILE__ ) . 'admin/links-inbound-canonical-verification.php' );
// Links referent audit trail
require_once( plugin_dir_path( __FILE__ ) . 'admin/links-inbound-audittrail.php' );
// Exiftool Extension
require_once( plugin_dir_path( __FILE__ ) . 'vendor/write.php' );
// JPEG Toolkit
require_once( plugin_dir_path( __FILE__ ) . 'vendor/JPEG.php' );
