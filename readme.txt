=== wpCLink ===
Contributors: gahmed, nhamza, wddavis, jfarkas
Donate link: https://clink.media
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl.html
Tags: content, google images, license, metadata, registry
Requires at least: 5.0
Tested up to: 5.9
Stable tag: 0.9.8.133
Requires PHP: 7.0

Content Link Beyond Hyperlink: formed under license; registered rights information; content delivered directly to the licensees site.
       
== Description ==
wpCLink plugin is the client component of a distributed system which first time integrates rights data; registry of persistent identifiers with metadata; peer-to-peer content licensing and delivery into a Content Management System. It is for personal use, on a non-business-related website. There are no charges for registering content and rights data. The plugin allows one registration a day. During the beta phase this daily limit is lifted. This is a beta software, it may contain errors, bugs and/or inaccuracies.

###CLink is Formed Only with the Consent of Parties
* Licenses are signed and stored electronically
* Content delivered directly to the licensees site
* Compliance verification (e.g. canonical URL)
   
###CLink is Tracked in a Digital Object Registry
* Registered content and rights information
* Relationship tracking between original and licensed content
* Persistent and content-based identifiers with revisable metadata

###IPTC and PLUS Metadata Support
* Over 50 properties supported and mapped to the registry
* Metadata automatically updated during the lifecycle of the image
* Support for Image License Metadata in Google Images

###Bug reports
Bug reports for wpCLink can be reported through the WordPress backend in its horizontal menu bar of the plugin. Please note that is not for seeking support. If you need help using the plugin, check out our Documentation or submit a support request on WordPress.org

== Installation ==

#### System Requirements
* PHP directives **allow_url_fopen**, **proc_close**, **proc_open** must be enabled on the host.
* HTTPS is a system requirement for the wpCLink plugin!  It will not activate nor operate if the site address does not start with https.
* The site must be accessible to verify its ownership. The plugin places a site verification code in the HTML head to protect the security of the users.

#### From within WordPress
1. Visit 'Plugins > Add New'
1. Search for 'wpCLink'
1. Activate wpCLink from your Plugins page.
1. Go to "after activation" below.
#### Manually 
1. Upload the `wpCLink` folder to the `/wp-content/plugins/` directory
1. Activate the wpCLink plugin through the 'Plugins' menu in WordPress
1. Go to "after activation" below.
#### After activation 
1. Select mode of operation
1. Select territory code
1. Select Contact
1. Select Creator
For explanation of the terms above see https://docs.clink.media/wordpress-plugins/ 

== Frequently Asked Questions ==
For live demonstration visit https://what.clink.is/overview
Please visit https://docs.clink.media/wordpress-plugins/ to find answers to many of your questions you may have.

== Screenshots ==
1. Content Dissemination
2. Programmatic and Preset Permissions/Prohibitions
3. Content Processed According to Granted License
4. Attribution Handling at the Republished Post
5. Attribution Handling at the Registry
6. Electronic Records of the Transactions
7. Audit Trails

== Changelog ==
= 0.9.8.133 =
Release Date: Febuary 17 2022
Enhancements
Fixes for WP 5.9
= 0.9.8.132 =
Release Date: January 25 2022
Enhancements
Fixes L2 C2PA UX on post and pages
Fixes TermsAndConditionsURL entires in clink.id and embedded data when Marketplace license is used
Minor styling changes for L2 C2PA Dark Mode UX
= 0.9.8.131 =
Release Date: January 23 2022
Enhancements
Implement Marketplace features and license
= 0.9.8.130 =
Release Date: January 16 2022
Enhancements
Refines C2PA UX
= 0.9.8.129 =
Release Date: Dec 29 2021
Enhancements
Implements C2PA UX for images
= 0.9.8.128 =
Release Date: Oct 19 2021
Enhancements
Updates ExifTool Libraries to version 12.33
= 0.9.8.127 =
Release Date: Oct 13 2021
Enhancements
Fix Linked Creation & Licensed Rights registry enty
= 0.9.8.126 =
Release Date: Oct 05 2021
Enhancements
Fixes bug for SHA256 hashes with PHP 8.x
= 0.9.8.125 =
Release Date: Oct 04 2021
Enhancements
Implements SHA256 Hash for JPEG Images
= 0.9.8.124 =
Release Date: Oct 02 2021
Enhancements
Updates ExifTool Libraries to version 12.32
Adds support for CBOR-format metadata in JUMBF (note that JUMBF support is still experimental)
= 0.9.8.123 =
Release Date: Sep 24 2021
Enhancements
Improve Marketplace Features
= 0.9.8.122 =
Release Date: Sep 18 2021
Enhancements
Implements gateway for US territory
= 0.9.8.121 =
Release Date: Sep 04 2021
Enhancements
Updated ExifTool Libraries to version 12.30
= 0.9.8.120 =
Release Date: Sep 02 2021
Enhancements
Enhances UX for provenance pup-up on linked sites
= 0.9.8.119 =
Release Date: Aug 31 2021
Enhancements
Enhances UX for provenance pup-up on referent sites
= 0.9.8.118 =
Release Date: Aug 26 2021
Enhancements
Creates hooks for Marketplace licenses used in the upcoming Business Edition
= 0.9.8.117 =
Release Date: Aug 18 2021
Enhancements
Bug Fixes for compatibility with the upcoming Business Edition
= 0.9.8.116 =
Release Date: Aug 10 2021
Enhancements
Creates hooks and filters for the Co-Authors Plus plugin Connector for the upcoming Business Edition
= 0.9.8.115 =
Release Date: July 28 2021
Enhancements
Creates hooks and filters for upcoming Business Edition
= 0.9.8.114 =
Release Date: July 16 2021
Enhancements
Improves and simplifies reuse pop-ups
Improves authenticity pop-ups at the bottom of the posts/pages
Minor UX improvements
= 0.9.8.113 =
Release Date: July 14 2021
Enhancements
Refines authenticity pop-ups and UX
= 0.9.8.112 =
Release Date: July 02 2021
Enhancements
Improves speed during updating Media
Improves UX for authenticity indicators
= 0.9.8.111 =
Release Date: June 18 2021
Enhancements
Runs derived images of the Media Library in the background
Minor UI enhancements
= 0.9.8.110 =
Release Date: June 10 2021
Bug Fixes
Fixes the error message is displayed when applying license (when the archive was generated previously by the Extension utility)
Fixes disabled ISCC buttons on linked creations
Fixes the reload of admin pages after ISCC is generated first time
Enhancements
Minor UI improvements
= 0.9.8.109 =
Release Date: June 07 2021
Enhancements
Updates ExifTool Library to Version 12.26
= 0.9.8.108 =
Release Date: June 04 2021
Bug Fixes
Fixes hang during generation of ISCC 
Fixes incompatibility with Extensions to insert issue collector
= 0.9.8.107 =
Release Date: May 21 2021
Enhancements
Implement SSL Expiration Check
= 0.9.8.106 =
Release Date: May 09 2021
Enhancements
Adds Roles to IPTC Image Registry fields
= 0.9.8.105 =
Release Date: May 06 2021
Enhancements
Minor bug fixes impacting UI
= 0.9.8.104 =
Release Date: May 03 2021
Enhancements
Implements Versions column in the list view UI for Post/Media/Pages
Minor UI enhancements
Bug Fixes
Fixes lack of blocking caption for licensed images without rights to change description when switching from code editor to visual editor 
Fixes error management for registration Media in case there is an interruption in the registration service
= 0.9.8.103 =
Release Date: April 19 2021
Enhancements
Improves UX allowing processing wpCLink features in the background
= 0.9.8.102 =
Release Date: April 15 2021
Enhancements
Improves Media UI during processing wpCLink features; allows editing while processing in the background.
= 0.9.8.101 =
Release Date: April 11 2021
Enhancements
Implements WordPress Nonces for Posts/Media/Pages
= 0.9.8.100 =
Release Date: April 1 2021
Bug Fixes:
Corrects AMP incompatibility for dynamic media license pages
Enhancements:
Implements error management for crunching linked images
Changes logos to SVG format
Minor UI improvements
= 0.9.8.99 =
Release Date: March 29 2021
Enhancements:
Optimizes ExifTool library
= 0.9.8.98 =
Release Date: March 22 2021
Enhancements:
Makes reuse pop-ups compatible with AMP
= 0.9.8.97 =
Release Date: March 20 2021
Enhancements:
Provides options to remove all EXIF fields for privacy or retain dates only
UI improvements for the "i" icon in posts/pages
= 0.9.8.96 =
Release Date: March 11 2021
Enhancements:
Improves UI for provenance and rights information
= 0.9.8.95 =
Release Date: March 02 2021
Enhancements:
Makes embedded licensable images compatible with Automattic AMP plugin
= 0.9.8.94 =
Release Date: February 20 2021
Enhancements:
Adds hooks and filters for a future extension compatible with Cloudinary SDK
Changes "Content/Image details" label to "Provenance and rights"
Allows ISCC generation on licensed posts
= 0.9.8.93 =
Release Date: February 09 2021
Enhancements:
Fixes bug for unable to update IP when IP verification fails
= 0.9.8.92 =
Release Date: February 06 2021
Enhancements:
Corrects styling when using the classic editor
= 0.9.8.91 =
Release Date: February 04 2021
Enhancements:
Updates ExifTool library to version 12.16
= 0.9.8.90 =
Release Date: January 31 2021
Enhancements:
Improves the UI for Media Library
= 0.9.8.89 =
Release Date: January 30 2021
Enhancements:
Implements error management when removing referent from CLink > Creations menu
Blocks editing Captions under embedded licenses image when "Modify Description" is not enabled
= 0.9.8.88 =
Release Date: January 25 2021
Enhancements:
Makes ACL for Media compatible with CLink rules
Improves UI used for Image Credit templates
= 0.9.8.87 =
Release Date: January 20 2021
Enhancements:
Implements template tag for themes to display content authenticity info
= 0.9.8.86 =
Release Date: January 17 2021
Enhancements:
Displays proper messages for plugin test/scan sites not compatible with the system requirements of wpCLink
= 0.9.8.85 =
Release Date: January 16 2021
Enhancements:
Reduces the number of files used at the front end and could impact page load times
Minimizes javascript, CSS files used on the front end
= 0.9.8.84 =
Release Date: January 13 2021
Bug Fixes:
Fixes bug causing javascript error on Gutenberg panel for licensed post/pages
= 0.9.8.83 =
Release Date: January 12 2021
Enhancements:
Streamlines the access level control for posts and pages with the WordPress core
= 0.9.8.82 =
Release Date: January 3 2021
Enhancements:
Improves compatibility with Cloudflare Flexible SSL
Makes versioning of registered object compatible with the upcoming premium edition
= 0.9.8.81 =
Release Date: December 29 2020
Enhancements:
Makes canonical verification feature compatible with the following SEO Plugins:
Yoast SEO			https://wordpress.org/plugins/wordpress-seo/
All in One SEO		https://wordpress.org/plugins/all-in-one-seo-pack/
Rank Math - SEO		https://wordpress.org/plugins/seo-by-rank-math/
The SEO Framework	https://wordpress.org/plugins/autodescription/
SEOPress			https://wordpress.org/plugins/wp-seopress/
SEO 2020 			https://wordpress.org/plugins/squirrly-seo/
= 0.9.8.80 =
Release Date: December 28 2020
Enhancements:
Aligns the behaviors of the CLink Gutenberg panel with the core Document panel
Optimizes for CLink Panel license widget 
Optimizes preloader after accepting license offer
Optimizes process for removing license on post/page when a licensed image sublicense permission is inserted 
= 0.9.8.79 =
Release Date: December 23 2020
Enhancements:
Improves processing of linked images inserted to a post
= 0.9.8.78 =
Release Date: December 20 2020
Enhancements:
Implement Menu for Selecting Reuse Button Label for Custom Licenses
= 0.9.8.77 =
Release Date: December 19 2020
Enhancements:
Implements error management for posts and pages
= 0.9.8.76 =
Release Date: December 17 2020
Enhancements:
Implements error management for apply licenses
= 0.9.8.75 =
Release Date: December 15 2020
Enhancements:
Implements error management for linked images
= 0.9.8.74 =
Release Date: December 13 2020
Bug Fixes:
Update Code for interfacing SEO Plugins.
= 0.9.8.73 =
Release Date: December 12 2020
Bug Fixes:
Fixes erroneous canonical verification errors when using RankMath SEO plugin.
= 0.9.8.72 =
Release Date: December 10 2020
Enhancements
Maintenance release
= 0.9.8.71 =
Release Date: December 03 2020
Enhancements
Implements error management for images
= 0.9.8.70 =
Release Date: November 25 2020
Enhancements
Implements Licensing for Images Embedded in Posts/Pages with Custom Licenses
= 0.9.8.69 =
Release Date: November 23 2020
Enhancements
Implement error management for ISCC
Bug Fixes
Fixes CSS issues related to mouse-over effects applied to embedded images
Minor UI changes
= 0.9.8.68 =
Release Date: November 21 2020
Enhancements:
Bug Fixes:
Corrects settings on variables causing PHP notices in debug mode (functionalities are not impacted)
= 0.9.8.67 =
Release Date: November 20 2020
Enhancements:
Streamlines ExifTool libraries
Removes exiftool.INFO messages from log files
Minor UI improvements
= 0.9.8.66 =
Release Date: November 12 2020
Enhancements:
Adds ISCC into the Embedded Metadata for Images
Bug Fixes:
Corrects URL used for generating the ISCC for Images
= 0.9.8.65 =
Enhancements:
Corrects the improper use of the term GUID and related processes in image licensing
UI improvements
= 0.9.8.64 =
Enhancements:
UI improvements
= 0.9.8.63 =
Release Date: October 29 2020
Bugfixes:
Fixes inconsistent operation of Allow  registration checkbox
Fixes incorrectly registering a version instead of updating the object  when an update is requested after adding a version
Enhancements:
Implements a rule below a certain image size for using icons (only) for provenance signals
Minor UI improvements
= 0.9.8.62 =
Release Date: October 23 2020
Bugfixes:
Fixes ExifTool library permission errors happened on rare occasions after updating the plugin
Enhancements:
Improves UI for small images for displaying provenance information
Add Referent (Origin) links to image provenance information
= 0.9.8.61 =
Release Date: October 20 2020
Enhancements:
Fixes CSS compatibility with NextGEN gallery plugin
= 0.9.8.60 =
Release Date: October 16 2020
Enhancements:
Fixes compatibility with jetpack share feature
= 0.9.8.59 =
Release Date: October 15 2020
Enhancements:
Implements image licensing feature from Posts/Pages
= 0.9.8.58 =
Release Date: October 9 2020
Bugfixes:
Fixes bug of providing incorrect URL for the "Get PMD feature for licensed images
Enhancements:
Improves UI for image checkout pages
Removes lock from "Copy URL" in the Save widget of the Edit Media Pages when content is licensed
Disallows content licensing when the domain access key is missing
= 0.9.8.57 =
Release Date: October 7 2020
Implements notification when the domain in the GUID does not match the domain of the site
= 0.9.8.56 =
Release Date: October 4 2020
Implements notification when domain key cannot be verified
Minor UI Improvements
= 0.9.8.55 =
Release Date: October 2 2020
Enhancements:
Fixes hang of the reuse pop-up when the URL entered is not registered
= 0.9.8.54 =
Release Date: September 17 2020
Enhancements:
Implements Hooks and Filters for future extensions
= 0.9.8.53 =
Release Date: September 10 2020
Bugfixes:
Fixes Gutenberg compatibility on linked sites for AddToTaxonomy right category
Minor UI improvement
= 0.9.8.52 =
Release Date: September 05 2020
Bugfixes:
Fixes Rights Holders were not visible in Linked Post
Fixes Versions were not visible until the page is refreshed in Linked Posts
Fixes static ISCC animation bar
Enhancements:
Updates preview layout for Google Images according to the August 31, 2020 release
Minor UI improvements
= 0.9.8.51 =
Release Date: August 25 2020
Enhancements:
Bug Fixes for triggering PHP Notices
= 0.9.8.50 =
Release Date: August 22 2020
Enhancements:
Fixes compatibility with Gutenberg CLink panel for WordPress 5.5
Improves UI
= 0.9.8.49 =
Release Date: August 1 2020
Enhancements:
Fixes Versions on linked images
Fixes styling for Licenses and Links widgets on linked images
= 0.9.8.48 =
Release Date: July 31 2020
Enhancements:
Includes all Exiftool Image libraries with the English language
Invokes scripts only after the image has been uploaded to eliminate interference.
Improves UI for warnings and errors for images
= 0.9.8.47 =
Release Date: July 20 2020
Enhancements:
Improvement on custom licenses and date of creation
= 0.9.8.46 =
Release Date: July 15 2020
Enhancements:
UI Improvements
= 0.9.8.45 =
Release Date: July 9 2020
Enhancements:
Bugfix for error caused by missing php method transliterator_transliterate
= 0.9.8.44 =
Release Date: July 1 2020
Enhancements:
UI Improvements 
= 0.9.8.43 =
Release Date: June 29 2020
Enhancements:
UI Improvements 
= 0.9.8.42 =
Release Date: June 27 2020
Enhancements:
Improve Preview For Google Images
= 0.9.8.41 =
Release Date: June 25 2020
Enhancements:
Streamline Pre-Loader UI
= 0.9.8.40 =
Release Date: June 20 2020
Enhancements:
Fixes Missing License and Link widget for Media Pages on Linked Sites
Improves UI
= 0.9.8.39 =
Release Date: June 19 2020
Enhancements:
Fixes Critical Error for non JPEG files in Media Library 
= 0.9.8.38 =
Release Date: June 18 2020
Enhancements:
Fixes Linked Creation ID tracking for Pages
Improves UI
= 0.9.8.37 =
Release Date: June 18 2020
Enhancements:
Fixes Bugs in Media Library Grid View
Improves UI
= 0.9.8.36 =
Release Date: June 15 2020
Enhancements:
Fixes Conflict with Rank Math plugin
Improves UI
= 0.9.8.35 =
Release Date: June 15 2020
Enhancements:
Image license, IPTC, PLUS Metadata and Google Images Support 
= 0.9.8.34 =
Release Date: April 21 2020
Enhancements:
Added PLUS ID fields
= 0.9.8.33 =
Release Date: April 05 2020
Enhancements:
Improves UI consistency
Fixes bugs with ISNI and ORCID fields
= 0.9.8.32 =
Release Date: March 28 2020
Enhancements:
Improves CLink Panel UI
Fixes Errors Created by the Canonical Script
= 0.9.8.31 =
Release Date: March 14 2020
Enhancements:
Implements ORCID and ISNI
= 0.9.8.30 =
Release Date: February 24 2020
Enhancements:
Fixes Linked Site Deleted Post
= 0.9.8.29 =
Release Date: February 20 2020
Enhancements:
Streamline the Canonical Function Names
= 0.9.8.28 =
Release Date: February 19 2020
Enhancements:
Fixes intermittent versioning bug
Fixes intermittent ISCC generation bug
Fixes taxonomy transfer for Linked Post when edited before publishing
= 0.9.8.27 =
Release Date: February 15 2020
Enhancements:
Streamline Function Names
= 0.9.8.26 =
Release Date: February 13 2020
Enhancements:
Streamline the License in Function Names
= 0.9.8.25 =
Release Date: February 11 2020
Enhancements:
Streamline the Term Agreement in Function Names
= 0.9.8.24 =
Release Date: February 9 2020
Enhancements:
Streamlines function names related to users
= 0.9.8.23 =
Release Date: February 6 2020
Enhancements:
Fixes bugs in ISCC generation for linked creations
= 0.9.8.22 =
Release Date: February 4 2020
Enhancements:
Adds support to the International Standard Content Code (ISCC); proposed open standard.
= 0.9.8.21 =
Release Date: February 1 2020
Enhancements:
Streamline Boolean Functions
= 0.9.8.20 =
Release Date: January 22 2020
Enhancements:
Streamline function names
= 0.9.8.19 =
Release Date: January 19 2020
Enhancements:
Correct Terms Content and Creation in the Functions and File Names
= 0.9.8.18 =
Release Date: January 18 2020
Enhancements:
Streamline function names including clink_id
= 0.9.8.17 =
Release Date: January 17 2020
Enhancements:
Streamline Filenames with CLink Terminologies
= 0.9.8.16 =
Release Date: January 15 2020
Enhancements:
Streamlines the use of  referent/linked terminology in filenames and functions
Updates screenshots
= 0.9.8.15 =
Release Date: January 14 2020
Enhancements:
Fixes bug for Linked Mode
Improves UI for notifications
= 0.9.8.14 =
Release Date: January 12 2020
Enhancements:
Fixes bug in the canonical URL verification algorithm (edited) 
Fixes bug related to reuses of Creations by multiple sites
= 0.9.8.13 =
Release Date: January 9 2020
Enhancements:
Improves features of General Preferences
Improves UI
= 0.9.8.12 =
Release Date: January 8 2020
Enhancements:
Additional verification for secured connection
Streamlines postmeta database fields specific to the plugin
Improves Audit Trail
Improves UI
= 0.9.8.11 =
Release Date: January 7 2020
Enhancements:
Streamlines database fields related to licenses
= 0.9.8.10 =
Release Date: December 27 2019
Enhancements:
Improves UI
Bug fixes
= 0.9.8.9 =
Release Date: December 26 2019
Enhancements:
Improves UI
Bug fixes
= 0.9.8.8 =
Release Date: December 21 2019
Enhancements:
Improves UI for Links
Implements Content Delivery Tracking
= 0.9.8.7 =
Release Date: December 18 2019
Enhancements:
Fixes UI bugs
Added download log file button
= 0.9.8.6 =
Release Date: December 17 2019
Enhancements:
Fixes bug and update info circles and notices
= 0.9.8.5 =
Release Date: December 16 2019
Enhancements:
Improves publishing flow for Linked Creations
= 0.9.8.4 =
Release Date: December 12 2019
Enhancements:
Fixes bug populating license documents
= 0.9.8.3 =
Release Date: December 7 2019
Enhancements:
Fixes API bugs
CSS adjustments
= 0.9.8.2 =
Release Date: December 5 2019
Enhancements:
Fixes CSS bugs
= 0.9.8.1 =
Release Date: December 4 2019
Enhancements:
Fix obtaining party access key
= 0.9.8 =
Release Date: December 3, 2019
Enhancements:
* Implements registration with interoperable registry schemas
* Implements programmatic licenses
* Improves UI
= 0.9.7 =
Release Date: May 24, 2019
Enhancements:
* Maintenance release
= 0.9.6 =
Release Date: May 16, 2019
Enhancements:
* Added Gutenberg Compatibility
= 0.9.5 =
Release Date: February 22, 2019
Enhancements:
* Simplifies the reuse process
= 0.9.4 =
Release Date: January 16, 2019
Enhancements:
* Implements user profile versioning
* Improves CLink Menu
* Removes unnecessary files
= 0.9.3 =
Release Date: January 3, 2019
Enhancements:
* File structure follows WordPress.org guidelines
= 0.9.2 =
Release Date: December 25, 2018
Enhancements:
* Database prefix update to wpclink 
= 0.9.1 =
Release Date: December 15, 2018
Enhancements:
* Implements edit option for linked post with UC-UT-UM license
* Implements automatic UC-UT-UM license type to Referent pages
* Implements Page Attributes and Order options to linked pages
* Adds donate button
= 0.9.0 =
Release Date: November 21, 2018

== Upgrade Notice ==
= 0.9.6 =
This version has Gutenberg Compatibility.  Upgrade immediately.