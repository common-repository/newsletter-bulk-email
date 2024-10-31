<?php

function bulkmail_do_depcreated_mymail_action( $tag ) {
	$args = func_get_args();
	$tag  = array_shift( $args );
	do_action_ref_array( $tag, $args );
}

add_action(
	'bulkmail_campaign_pause',
	function( $id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_campaign_pause', $id );
	},
	10,
	1
);

add_action(
	'bulkmail_campaign_start',
	function( $id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_campaign_start', $id );
	},
	10,
	1
);

add_action(
	'bulkmail_finish_campaign',
	function( $id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_finish_campaign', $id );
	},
	10,
	1
);

add_action(
	'bulkmail_campaign_duplicate',
	function( $id, $new_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_campaign_duplicate', $id, $new_id );
	},
	10,
	2
);

add_action(
	'bulkmail_send',
	function( $subscriber_id, $campaign_id, $result ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_send', $subscriber_id, $campaign_id, $result );
	},
	10,
	3
);

add_action(
	'bulkmail_subscriber_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_subscriber_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'bulkmail_system_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_system_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'bulkmail_campaign_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_campaign_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'bulkmail_autoresponder_post_published',
	function( $campaign_id, $new_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_autoresponder_post_published', $campaign_id, $new_id );
	},
	10,
	2
);

add_action(
	'bulkmail_check_bounces',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_check_bounces' );
	}
);

add_action(
	'bulkmail_resend_confirmations',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_resend_confirmations' );
	}
);

add_action(
	'bulkmail_form_head_button',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_head_button' );
	}
);

add_action(
	'bulkmail_form_head_embeded',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_head_embeded' );
	}
);

add_action(
	'bulkmail_form_head_iframe',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_head_iframe' );
	}
);

add_action(
	'bulkmail_form_body_button',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_body_button' );
	}
);

add_action(
	'bulkmail_form_body_iframe',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_body_iframe' );
	}
);

add_action(
	'bulkmail_form_footer_button',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_footer_button' );
	}
);

add_action(
	'bulkmail_form_footer_embeded',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_footer_embeded' );
	}
);

add_action(
	'bulkmail_form_footer_iframe',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_footer_iframe' );
	}
);

add_action(
	'bulkmail_form_delete',
	function( $form_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_delete', $form_id );
	},
	10,
	1
);

add_action(
	'bulkmail_update_form',
	function( $form_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_update_form', $form_id );
	},
	10,
	1
);

add_action(
	'bulkmail_add_form',
	function( $form_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_add_form', $form_id );
	},
	10,
	1
);

add_action(
	'bulkmail_unassign_form_lists',
	function( $form_ids, $lists, $not_list ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_unassign_form_lists', $form_ids, $lists, $not_list );
	},
	10,
	3
);

add_action(
	'bulkmail_click',
	function( $subscriber_id, $campaign_id, $target, $index ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_click', $subscriber_id, $campaign_id, $target, $index );
	},
	10,
	4
);

add_action(
	'bulkmail_open',
	function( $subscriber_id, $campaign_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_open', $subscriber_id, $campaign_id );
	},
	10,
	2
);

add_action(
	'bulkmail_homepage_subscribe',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_homepage_subscribe' );
	}
);

add_action(
	'bulkmail_homepage_unsubscribe',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_homepage_unsubscribe' );
	}
);

add_action(
	'bulkmail_homepage_profile',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_homepage_profile' );
	}
);

add_action(
	'bulkmail_homepage_confirm',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_homepage_confirm' );
	}
);

add_action(
	'bulkmail_subscriber_subscribed',
	function( $subscriber_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_subscriber_subscribed', $subscriber_id );
	},
	10,
	1
);

add_action(
	'bulkmail_subscriber_insert',
	function( $subscriber_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_subscriber_insert', $subscriber_id );
	},
	10,
	1
);

add_action(
	'bulkmail_list_save',
	function( $list_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_list_save', $list_id );
	},
	10,
	1
);

add_action(
	'bulkmail_list_delete',
	function( $list_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_list_delete', $list_id );
	},
	10,
	1
);

add_action(
	'bulkmail_update_list',
	function( $list_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_update_list', $list_id );
	},
	10,
	1
);

add_action(
	'bulkmail_initsend',
	function( $obj ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_initsend', $obj );
	},
	10,
	1
);

add_action(
	'bulkmail_presend',
	function( $obj ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_presend', $obj );
	},
	10,
	1
);

add_action(
	'bulkmail_dosend',
	function( $obj ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_dosend', $obj );
	},
	10,
	1
);

add_action(
	'bulkmail_thirdpartystuff',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_thirdpartystuff' );
	},
	10,
	1
);

add_action(
	'bulkmail_autoresponder_post_published',
	function( $campaign_id, $new_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_autoresponder_post_published', $campaign_id, $new_id );
	},
	10,
	2
);

add_action(
	'bulkmail_autoresponder_timebased',
	function( $campaign_id, $new_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_autoresponder_timebased', $campaign_id, $new_id );
	},
	10,
	2
);

add_action(
	'bulkmail_autoresponder_usertime',
	function( $campaign_id, $subscriber_ids ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_autoresponder_usertime', $campaign_id, $subscriber_ids );
	},
	10,
	2
);

add_action(
	'bulkmail_subscriber_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_subscriber_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'bulkmail_notification_error',
	function( $subscriber_id, $error_message ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_notification_error', $subscriber_id, $error_message );
	},
	10,
	2
);

add_action(
	'bulkmail_campaign_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_campaign_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'bulkmail_cron_finished',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_cron_finished' );
	}
);

add_action(
	'bulkmail_subscriber_save',
	function( $subscriber_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_subscriber_save', $subscriber_id );
	},
	10,
	1
);

add_action(
	'bulkmail_subscriber_save',
	function( $subscriber_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_subscriber_save', $subscriber_id );
	},
	10,
	1
);

add_action(
	'bulkmail_subscriber_delete',
	function( $subscriber_id, $email ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_subscriber_delete', $subscriber_id, $email );
	},
	10,
	2
);

add_action(
	'bulkmail_update_subscriber',
	function( $subscriber_id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_update_subscriber', $subscriber_id );
	},
	10,
	1
);

add_action(
	'bulkmail_unassign_lists',
	function( $subscriber_ids, $lists, $not_list ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_unassign_lists', $subscriber_ids, $lists, $not_list );
	},
	10,
	3
);

add_action(
	'bulkmail_unsubscribe',
	function( $subscriber_id, $campaign_id, $status ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_unsubscribe', $subscriber_id, $campaign_id, $status );
	},
	10,
	3
);

add_action(
	'bulkmail_bounce',
	function( $subscriber_id, $campaign_id, $is_hard, $status ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_bounce', $subscriber_id, $campaign_id, $is_hard, $status );
	},
	10,
	4
);

add_action(
	'bulkmail_subscriber_change_status',
	function( $new_status, $old_status, $subscriber ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_subscriber_change_status', $new_status, $old_status, $subscriber );
	},
	10,
	3
);

add_action(
	'bulkmail_cron_worker',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_cron_worker' );
	}
);

add_action(
	'bulkmail_form_header',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_header' );
	}
);

add_action(
	'bulkmail_form_head',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_head' );
	}
);

add_action(
	'bulkmail_form_body',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_body' );
	}
);

add_action(
	'bulkmail_form_footer',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_form_footer' );
	}
);

add_action(
	'bulkmail_notice',
	function( $text, $type, $key ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_notice', $text, $type, $key );
	},
	10,
	3
);

add_action(
	'bulkmail_remove_notice',
	function( $key ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_remove_notice', $key );
	},
	10,
	1
);

add_action(
	'bulkmail_import_tab',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_import_tab' );
	}
);

add_action(
	'bulkmail_autoresponder_more',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_autoresponder_more' );
	}
);

add_action(
	'bulkmail_settings_tabs',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_settings_tabs' );
	}
);

add_action(
	'bulkmail_settings',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_settings' );
	}
);

add_action(
	'bulkmail_section_tab',
	function( $id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_section_tab', $id );
	},
	10,
	1
);

add_action(
	'bulkmail_deliverymethod_tab',
	function( $id ) {
		bulkmail_do_depcreated_mymail_action( 'mymail_deliverymethod_tab', $id );
	},
	10,
	1
);

add_action(
	'bulkmail_wphead',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_wphead' );
	}
);

add_action(
	'bulkmail_wpfooter',
	function() {
		bulkmail_do_depcreated_mymail_action( 'mymail_wpfooter' );
	}
);

