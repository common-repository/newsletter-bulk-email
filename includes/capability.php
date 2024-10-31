<?php

$bulkmail_capabilities = array(

	'edit_newsletters'                    => array(
		'title' => esc_html__( 'edit campaigns', 'bulkmail' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
	),

	'publish_newsletters'                 => array(
		'title' => esc_html__( 'send campaigns', 'bulkmail' ),
		'roles' => array( 'author', 'editor' ),
	),

	'delete_newsletters'                  => array(
		'title' => esc_html__( 'delete campaigns', 'bulkmail' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
	),

	'edit_others_newsletters'             => array(
		'title' => esc_html__( 'edit others campaigns', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'delete_others_newsletters'           => array(
		'title' => esc_html__( 'delete others campaigns', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'duplicate_newsletters'               => array(
		'title' => esc_html__( 'duplicate campaigns', 'bulkmail' ),
		'roles' => array( 'author', 'editor' ),
	),

	'duplicate_others_newsletters'        => array(
		'title' => esc_html__( 'duplicate others campaigns', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_edit_autoresponders'        => array(
		'title' => esc_html__( 'edit autoresponders', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_edit_others_autoresponders' => array(
		'title' => esc_html__( 'edit others autoresponders', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),


	'bulkmail_change_template'            => array(
		'title' => esc_html__( 'change template', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),
	'bulkmail_save_template'              => array(
		'title' => esc_html__( 'save template', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_see_codeview'               => array(
		'title' => esc_html__( 'see codeview', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_change_plaintext'           => array(
		'title' => esc_html__( 'change text version', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),


	'bulkmail_edit_subscribers'           => array(
		'title' => esc_html__( 'edit subscribers', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_add_subscribers'            => array(
		'title' => esc_html__( 'add subscribers', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_delete_subscribers'         => array(
		'title' => esc_html__( 'delete subscribers', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_edit_forms'                 => array(
		'title' => esc_html__( 'edit forms', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_add_forms'                  => array(
		'title' => esc_html__( 'add forms', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_delete_forms'               => array(
		'title' => esc_html__( 'delete forms', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),


	'bulkmail_manage_subscribers'         => array(
		'title' => esc_html__( 'manage subscribers', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_import_subscribers'         => array(
		'title' => esc_html__( 'import subscribers', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_import_wordpress_users'     => array(
		'title' => esc_html__( 'import WordPress Users', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_export_subscribers'         => array(
		'title' => esc_html__( 'export subscribers', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_bulk_delete_subscribers'    => array(
		'title' => esc_html__( 'bulk delete subscribers', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_add_lists'                  => array(
		'title' => esc_html__( 'add lists', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_edit_lists'                 => array(
		'title' => esc_html__( 'edit lists', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_delete_lists'               => array(
		'title' => esc_html__( 'delete lists', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),



	'bulkmail_manage_addons'              => array(
		'title' => esc_html__( 'manage addons', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_manage_templates'           => array(
		'title' => esc_html__( 'manage templates', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_edit_templates'             => array(
		'title' => esc_html__( 'edit templates', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_delete_templates'           => array(
		'title' => esc_html__( 'delete templates', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_upload_templates'           => array(
		'title' => esc_html__( 'upload templates', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_update_templates'           => array(
		'title' => esc_html__( 'update templates', 'bulkmail' ),
		'roles' => array(),
	),


	'bulkmail_dashboard'                  => array(
		'title' => esc_html__( 'access dashboard', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_dashboard_widget'           => array(
		'title' => esc_html__( 'see dashboard widget', 'bulkmail' ),
		'roles' => array( 'editor' ),
	),

	'bulkmail_manage_capabilities'        => array(
		'title' => esc_html__( 'manage capabilities', 'bulkmail' ),
		'roles' => array(),
	),

	'bulkmail_manage_licenses'            => array(
		'title' => esc_html__( 'manage licenses', 'bulkmail' ),
		'roles' => array(),
	),

);

$bulkmail_capabilities = apply_filters( 'mymail_capabilities', apply_filters( 'bulkmail_capabilities', $bulkmail_capabilities ) );
