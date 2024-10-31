<?php

$pages = apply_filters(
	'bulkmail_help_pages',
	array(
		// newsletter edit page
		'newsletter'                                  => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
		'newsletter_page_bulkmail_subscribers'        => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
		'newsletter_page_bulkmail_lists'              => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
		'newsletter_page_bulkmail_manage_subscribers' => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
		'newsletter_page_bulkmail_dashboard'          => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
	)
);
