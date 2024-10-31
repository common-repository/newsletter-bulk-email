<?php

if ( ! file_exists( BULKEMAIL_DIR . 'includes/functions.php' ) ) {
	return;
}
if ( version_compare( PHP_VERSION, '5.3' ) < 0 ) {
	if ( is_admin() ) {
		$text = sprintf( 'Bulkmail requires PHP version 5.3 or higher. Your current version is %s. Please update or ask your hosting provider to help you updating.', PHP_VERSION );
		die( '<div style="font-family:sans-serif;">' . $text . '</div>' );
	}
}
