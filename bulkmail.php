<?php
/*
Plugin Name: Newsletter & Bulk Email Sender
Plugin URI: https://emailmarketing.run
Description: The Ultimate Newsletter Plugin.
Version: 2.0.1
Author: HappyBox
Text Domain: emailmarketing
*/

if ( defined( 'BULKEMAIL_VERSION' ) || ! defined( 'ABSPATH' ) ) {
	return;
}

define( 'BULKEMAIL_VERSION', '1.0.1' );
define( 'BULKEMAIL_BUILT', 1597830123 );
define( 'BULKEMAIL_DBVERSION', 20200724 );
define( 'BULKEMAIL_DIR', plugin_dir_path( __FILE__ ) );
define( 'BULKEMAIL_URI', plugin_dir_url( __FILE__ ) );
define( 'BULKEMAIL_FILE', __FILE__ );
define( 'BULKEMAIL_SLUG', basename( BULKEMAIL_DIR ) . '/' . basename( __FILE__ ) );

$upload_folder = wp_upload_dir();

if ( ! defined( 'BULKEMAIL_UPLOAD_DIR' ) ) {
	define( 'BULKEMAIL_UPLOAD_DIR', trailingslashit( $upload_folder['basedir'] ) . 'bulkmail' );
}
if ( ! defined( 'BULKEMAIL_UPLOAD_URI' ) ) {
	define( 'BULKEMAIL_UPLOAD_URI', trailingslashit( $upload_folder['baseurl'] ) . 'bulkmail' );
}

require_once BULKEMAIL_DIR . 'includes/check.php';
require_once BULKEMAIL_DIR . 'includes/functions.php';
require_once BULKEMAIL_DIR . 'includes/deprecated.php';
require_once BULKEMAIL_DIR . 'includes/3rdparty.php';
require_once BULKEMAIL_DIR . 'classes/bulkmail.class.php';

global $bulkmail;

$bulkmail = new bulkmail();

if ( ! $bulkmail->wp_mail && bulkmail_option( 'system_mail' ) == 1 ) {

	function wp_mail( $to, $subject, $message, $headers = '', $attachments = array(), $file = null, $template = null ) {
		return bulkmail()->wp_mail( $to, $subject, $message, $headers, $attachments, $file, $template );
	}
}
