<?php

$bulkmail_autoresponder_info = array(

	'units'   => array(
		'minute' => esc_html__( 'minute(s)', 'bulkmail' ),
		'hour'   => esc_html__( 'hour(s)', 'bulkmail' ),
		'day'    => esc_html__( 'day(s)', 'bulkmail' ),
		'week'   => esc_html__( 'week(s)', 'bulkmail' ),
		'month'  => esc_html__( 'month(s)', 'bulkmail' ),
		'year'   => esc_html__( 'year(s)', 'bulkmail' ),
	),

	'actions' => array(
		'bulkmail_subscriber_insert'       => array(
			'label' => esc_html__( 'user signed up', 'bulkmail' ),
			'hook'  => 'bulkmail_subscriber_insert',
		),
		'bulkmail_subscriber_unsubscribed' => array(
			'label' => esc_html__( 'user unsubscribed', 'bulkmail' ),
			'hook'  => 'bulkmail_subscriber_unsubscribed',
		),
		'bulkmail_post_published'          => array(
			'label' => esc_html__( 'something has been published', 'bulkmail' ),
			'hook'  => 'transition_post_status',
		),
		'bulkmail_autoresponder_timebased' => array(
			'label' => esc_html__( 'at a specific time', 'bulkmail' ),
			'hook'  => 'bulkmail_autoresponder_timebased',
		),
		'bulkmail_autoresponder_usertime'  => array(
			'label' => esc_html__( 'a specific user time', 'bulkmail' ),
			'hook'  => 'bulkmail_autoresponder_usertime',
		),
		'bulkmail_autoresponder_followup'  => array(
			'label' => esc_html__( 'a specific campaign', 'bulkmail' ),
			'hook'  => 'bulkmail_autoresponder_followup',
		),
		'bulkmail_autoresponder_hook'      => array(
			'label' => esc_html__( 'a specific action hook', 'bulkmail' ),
			'hook'  => 'bulkmail_autoresponder_hook',
		),
	),

);

$bulkmail_autoresponder_info['units']   = apply_filters( 'mymail_autoresponder_units', apply_filters( 'bulkmail_autoresponder_units', $bulkmail_autoresponder_info['units'] ) );
$bulkmail_autoresponder_info['actions'] = apply_filters( 'mymail_autoresponder_actions', apply_filters( 'bulkmail_autoresponder_actions', $bulkmail_autoresponder_info['actions'] ) );
