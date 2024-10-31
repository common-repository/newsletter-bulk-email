<?php

/*
This runs if an update was done.
*/

global $wpdb;

$bulkmail_options = bulkmail_options();
$bulkmail_texts   = bulkmail_texts();

$new_version = BULKEMAIL_VERSION;

$texts              = isset( $bulkmail_options['text'] ) && ! empty( $bulkmail_options['text'] ) ? $bulkmail_options['text'] : $bulkmail_texts;
$show_update_notice = false;

$default_options = bulkmail( 'settings' )->get_defaults();
$default_texts   = bulkmail( 'settings' )->get_default_texts();

if ( $old_version ) {

	// remove any branch version from the string.
	$old_version_sanitized = preg_replace( '#^([^a-z]+)(\.|-)([a-z_]+)(.*?)$#i', '$1', $old_version );

	switch ( $old_version_sanitized ) {
		case '1.0':
		case '2.0.1':
			bulkmail_notice( '[1.1.0] Capabilities are now available. Please check the <a href="edit.php?post_type=newsletter&page=bulkmail_settings#capabilities">settings page</a>' );
			bulkmail_notice( '[1.1.0] Custom Fields now support dropbox and radio button. Please check the <a href="edit.php?post_type=newsletter&page=bulkmail_settings#subscribers">settings page</a>' );

			$texts['firstname'] = esc_html__( 'First Name', 'bulkmail' );
			$texts['lastname']  = esc_html__( 'Last Name', 'bulkmail' );

		case '1.1.0':
			$texts['email']             = esc_html__( 'Email', 'bulkmail' );
			$texts['submitbutton']      = esc_html__( 'Subscribe', 'bulkmail' );
			$texts['unsubscribebutton'] = esc_html__( 'Yes, unsubscribe me', 'bulkmail' );
			$texts['unsubscribelink']   = esc_html__( 'unsubscribe', 'bulkmail' );
			$texts['webversion']        = esc_html__( 'webversion', 'bulkmail' );

		case '1.1.1.1':
			$texts['lists'] = esc_html__( 'Lists', 'bulkmail' );

			bulkmail_notice( '[1.2.0] Auto responders are now available! Please set the <a href="edit.php?post_type=newsletter&page=bulkmail_settings#capabilities">capabilities</a> to get access' );

		case '1.2.0':
			$bulkmail_options['send_limit']  = 10000;
			$bulkmail_options['send_period'] = 24;
			$bulkmail_options['ajax_form']   = true;

			$texts['unsubscribeerror'] = esc_html__( 'An error occurred! Please try again later!', 'bulkmail' );

			bulkmail_notice( '[1.2.1] New capabilities available! Please update them in the <a href="edit.php?post_type=newsletter&page=bulkmail_settings#capabilities">settings</a>' );

		case '1.2.1':
		case '1.2.1.1':
		case '1.2.1.2':
		case '1.2.1.3':
		case '1.2.1.4':
			bulkmail_notice( '[1.2.2] New capability: "manage capabilities". Please check the <a href="edit.php?post_type=newsletter&page=bulkmail_settings#capabilities">settings page</a>' );
		case '1.2.2':
		case '1.2.2.1':
			$bulkmail_options['post_count'] = 30;
			bulkmail_notice( '[1.3.0] Track your visitors cities! Activate the option on the <a href="edit.php?post_type=newsletter&page=bulkmail_settings#general">settings page</a>' );

			$texts['forward'] = esc_html__( 'forward to a friend', 'bulkmail' );


		case '1.3.0':
			$bulkmail_options['frontpage_pagination'] = true;
			$bulkmail_options['basicmethod']          = 'sendmail';
			$bulkmail_options['deliverymethod']       = ( bulkmail_option( 'smtp' ) ) ? 'smtp' : 'simple';
			$bulkmail_options['bounce_active']        = ( bulkmail_option( 'bounce_server' ) && bulkmail_option( 'bounce_user' ) && bulkmail_option( 'bounce_pwd' ) );

			$bulkmail_options['spf_domain']   = $bulkmail_options['dkim_domain'];
			$bulkmail_options['send_offset']  = $bulkmail_options['send_delay'];
			$bulkmail_options['send_delay']   = 0;
			$bulkmail_options['smtp_timeout'] = 10;


			bulkmail_notice( '[1.3.1] DKIM is now better supported but you have to check  <a href="edit.php?post_type=newsletter&page=bulkmail_settings#general">settings page</a>' );

		case '1.3.1':
		case '1.3.1.1':
		case '1.3.1.2':
		case '1.3.1.3':
		case '1.3.2':
		case '1.3.2.1':
		case '1.3.2.2':
		case '1.3.2.3':
		case '1.3.2.4':
			delete_option( 'bulkmail_bulk_imports' );
			$forms                     = $bulkmail_options['forms'];
			$bulkmail_options['forms'] = array();
			foreach ( $forms as $form ) {
				$form['prefill']             = true;
				$bulkmail_options['forms'][] = $form;
			}

			bulkmail_notice( '[1.3.3] New capability: "manage subscribers". Please check the <a href="edit.php?post_type=newsletter&page=bulkmail_settings#capabilities">capabilities settings page</a>' );
		case '1.3.3':
		case '1.3.3.1':
		case '1.3.3.2':
			$bulkmail_options['subscription_resend_count'] = 2;
			$bulkmail_options['subscription_resend_time']  = 48;


		case '1.3.4':
			$bulkmail_options['sendmail_path'] = '/usr/sbin/sendmail';
		case '1.3.4.1':
		case '1.3.4.2':
		case '1.3.4.3':
			$forms        = $bulkmail_options['forms'];
			$customfields = bulkmail_option( 'custom_field', array() );

			$bulkmail_options['forms'] = array();
			foreach ( $forms as $form ) {
				$order = array( 'email' );
				if ( isset( $bulkmail_options['firstname'] ) ) {
					$order[] = 'firstname';
				}
				if ( isset( $bulkmail_options['lastname'] ) ) {
					$order[] = 'lastname';
				}
				$required = array( 'email' );
				if ( isset( $bulkmail_options['require_firstname'] ) ) {
					$required[] = 'firstname';
				}
				if ( isset( $bulkmail_options['require_lastname'] ) ) {
					$required[] = 'lastname';
				}

				foreach ( $customfields as $field => $data ) {
					if ( isset( $data['ask'] ) ) {
						$order[] = $field;
					}
					if ( isset( $data['required'] ) ) {
						$required[] = $field;
					}
				}
				$form['order']               = $order;
				$form['required']            = $required;
				$bulkmail_options['forms'][] = $form;
			}

		case '1.3.4.4':
		case '1.3.4.5':
		case '1.3.5':
		case '1.3.6':
		case '1.3.6.1':
			add_action( 'shutdown', array( $bulkmail_templates, 'renew_default_template' ) );

		case '1.4.0':
		case '1.4.0.1':
			$lists                                    = isset( $bulkmail_options['newusers'] ) ? $bulkmail_options['newusers'] : array();
			$bulkmail_options['register_other_lists'] = $bulkmail_options['register_comment_form_lists'] = $bulkmail_options['register_signup_lists'] = $lists;
			$bulkmail_options['register_comment_form_status'] = array( '1', '0' );
			if ( ! empty( $lists ) ) {
				$bulkmail_options['register_other'] = true;
			}

			$texts['newsletter_signup'] = esc_html__( 'Sign up to our newsletter', 'bulkmail' );

			bulkmail_notice( '[1.4.1] New option for WordPress Users! Please <a href="edit.php?post_type=newsletter&page=bulkmail_settings#subscribers">update your settings</a>!' );
			bulkmail_notice( '[1.4.1] New text for newsletter sign up Please <a href="edit.php?post_type=newsletter&page=bulkmail_settings#texts">update your settings</a>!' );

		case '1.4.1':
		case '1.5.0':
		case '1.5.1':
		case '1.5.1.1':
		case '1.5.1.2':
			set_transient( 'bulkmail_dkim_records', array(), 1 );

			bulkmail_notice( '[1.5.2] Since Twitter dropped support for API 1.0 you have to create a new app if you would like to use the <code>{tweet:username}</code> tag. Enter your credentials <a href="edit.php?post_type=newsletter&page=bulkmail_settings#tags">here</a>!' );

		case '1.5.2':
			update_option( 'envato_plugins', '' );

		case '1.5.3':
		case '1.5.3.1':
		case '1.5.3.2':
			$bulkmail_options['charset']  = 'UTF-8';
			$bulkmail_options['encoding'] = '8bit';

			$forms = $bulkmail_options['forms'];

			$bulkmail_options['forms'] = array();
			foreach ( $forms as $form ) {
				$form['asterisk']            = true;
				$bulkmail_options['forms'][] = $form;
			}

		case '1.5.4':
		case '1.5.4.1':
		case '1.5.5':
		case '1.5.5.1':
		case '1.5.6':
		case '1.5.7':
		case '1.5.7.1':
			$forms = $bulkmail_options['forms'];

			$bulkmail_options['forms'] = array();
			foreach ( $forms as $form ) {
				$form['submitbutton']        = bulkmail_text( 'submitbutton' );
				$bulkmail_options['forms'][] = $form;
			}

		case '1.5.8':
			$forms = $bulkmail_options['forms'];

			$bulkmail_options['forms'] = array();
			foreach ( $forms as $form ) {
				if ( is_numeric( $form['submitbutton'] ) ) {
					$form['submitbutton'] = '';
				}
				$bulkmail_options['forms'][] = $form;
			}

		case '1.5.8.1':
		case '1.6.0':
			$bulkmail_options['slug'] = 'newsletter';

		case '1.6.1':
			if ( ! isset( $bulkmail_options['slug'] ) ) {
				$bulkmail_options['slug'] = 'newsletter';
			}


		case '1.6.2':
		case '1.6.2.1':
		case '1.6.2.2':
			// just a random ID for better bounces
			$bulkmail_options['ID']           = md5( uniqid() );
			$bulkmail_options['bounce_check'] = 5;
			$bulkmail_options['bounce_delay'] = 60;

		case '1.6.3':
		case '1.6.3.1':
		case '1.6.4':
		case '1.6.4.1':
		case '1.6.4.2':
			$forms = $bulkmail_options['forms'];

			$bulkmail_options['forms'] = array();
			foreach ( $forms as $form ) {
				if ( ! isset( $form['text'] ) ) {
					$form['precheck']                  = true;
					$form['double_opt_in']             = bulkmail_option( 'double_opt_in' );
					$form['text']                      = bulkmail_option( 'text' );
					$form['subscription_resend']       = bulkmail_option( 'subscription_resend' );
					$form['subscription_resend_count'] = bulkmail_option( 'subscription_resend_count' );
					$form['subscription_resend_time']  = bulkmail_option( 'subscription_resend_time' );
					$form['vcard']                     = bulkmail_option( 'vcard' );
					$form['vcard_filename']            = bulkmail_option( 'vcard_filename' );
					$form['vcard_content']             = bulkmail_option( 'vcard_content' );
				}
				$bulkmail_options['forms'][] = $form;
			}

			bulkmail_notice( '[1.6.5] Double-Opt-In options are now form specific. Please <a href="edit.php?post_type=newsletter&page=bulkmail_forms">check your forms</a> if everything has been converted correctly!', '', false, 'update165' );

		case '1.6.5':
		case '1.6.5.1':
		case '1.6.5.2':
		case '1.6.5.3':
		case '1.6.6':
		case '1.6.6.1':
		case '1.6.6.2':
		case '1.6.6.3':
		case '2.0 beta 1':
		case '2.0 beta 1.1':
			$campaigns = bulkmail( 'campaigns' )->get_autoresponder();

			foreach ( $campaigns as $campaign ) {

				$meta = bulkmail( 'campaigns' )->meta( $campaign->ID );

				if ( $meta['active'] ) {

					bulkmail( 'campaigns' )->update_meta( $campaign->ID, 'active', false );
					bulkmail_notice( 'Autoresponders have been disabled cause of some internal change. Please <a href="edit.php?post_status=autoresponder&post_type=newsletter&bulkmail_remove_notice=autorespondersdisabled">update them to reactivate them</a>', '', false, 'autorespondersdisabled' );

				}
			}



		case '2.0 beta 2':
		case '2.0 beta 2.1':
		case '2.0 beta 3':
			$bulkmail_options['autoupdate'] = 'minor';

		case '2.0RC 1':
		case '2.0RC 2':
			delete_option( 'envato_plugins' );
			delete_option( 'updatecenter_plugins' );

		case '2.0':
		case '2.0.1':
		case '2.0.2':
		case '2.0.3':
		case '2.0.4':
		case '2.0.5':
		case '2.0.6':
		case '2.0.7':
			$bulkmail_options['pause_campaigns'] = true;
		case '2.0.8':
		case '2.0.9':
			$bulkmail_options['slugs'] = array(
				'confirm'     => 'confirm',
				'subscribe'   => 'subscribe',
				'unsubscribe' => 'unsubscribe',
				'profile'     => 'profile',
			);

			$bulkmail_options['_flush_rewrite_rules'] = true;
		case '2.0.10':
		case '2.0.11':
		case '2.0.12':
			$bulkmail_options['_flush_rewrite_rules'] = true;
		case '2.0.13':
			$forms = $bulkmail_options['forms'];
			$optin = isset( $forms[0] ) && isset( $forms[0]['double_opt_in'] );
			$bulkmail_options['register_comment_form_confirmation'] = $optin;
			$bulkmail_options['register_signup_confirmation']       = $optin;

		case '2.0.14':
			global $wp_roles;

			if ( $wp_roles ) {
				$roles                                    = $wp_roles->get_names();
				$bulkmail_options['register_other_roles'] = array_keys( $roles );
			}

		case '2.0.15':
		case '2.0.16':
		case '2.0.17':
		case '2.0.18':
		case '2.0.19':
		case '2.0.20':
		case '2.0.21':
		case '2.0.22':
		case '2.0.23':
		case '2.0.24':
		case '2.0.25':
		case '2.0.26':
		case '2.0.27':
		case '2.0.28':
		case '2.0.29':
		case '2.0.30':
		case '2.0.31':
		case '2.0.32':
		case '2.0.33':
		case '2.0.34':
			bulkmail_notice( 'Please clear your cache if you are using page cache on your site', '', false, 'bulkmailpagecache' );
			$bulkmail_options['welcome'] = true;

		case '2.1':
		case '2.1.1':
			if ( $bulkmail_options['php_mailer'] ) {
				$bulkmail_options['php_mailer'] = '5.2.14';
			}
			$bulkmail_options['archive_slug']         = $bulkmail_options['slug'];
			$bulkmail_options['archive_types']        = array( 'finished', 'active' );
			$bulkmail_options['module_thumbnails']    = true;
			$bulkmail_options['_flush_rewrite_rules'] = true;

		case '2.1.2':
		case '2.1.3':
		case '2.1.4':
		case '2.1.5':
		case '2.1.6':
			$bulkmail_options['got_url_rewrite'] = bulkmail( 'helper' )->got_url_rewrite();

		case '2.1.7':
		case '2.1.8':
			$bulkmail_options['_flush_rewrite_rules'] = true;

		case '2.1.9':
			$texts = wp_parse_args( $texts, $default_texts );

			$t = bulkmail( 'translations' )->get_translation_data();

			if ( ! empty( $t ) ) {
				bulkmail_notice( sprintf( 'An important change to localizations in Bulkmail has been made. <a href="%s">read more</a>', 'https://emailmarketing.run/' ), '', false, 'bulkmailtranslation' );
			}

			unset( $bulkmail_options['texts'] );
			$show_update_notice = true;

		case '2.1.10':
		case '2.1.11':
		case '2.1.12':
		case '2.1.13':
		case '2.1.14':
		case '2.1.15':
		case '2.1.16':
		case '2.1.16.1':
		case '2.1.17':
		case '2.1.18':
			bulkmail( 'cron' )->unlock( 0 );

		case '2.1.19':
		case '2.1.20':
		case '2.1.21':
		case '2.1.22':
		case '2.1.23':
		case '2.1.24':
		case '2.1.25':
			if ( isset( $bulkmail_options['smtp_auth'] ) ) {
				$bulkmail_options['smtp_auth'] = 'LOGIN';
			}
			if ( $bulkmail_options['php_mailer'] == '5.2.7' ) {
				$bulkmail_options['php_mailer'] = false;
			}

		case '2.1.26':
		case '2.1.27':
		case '2.1.28':
			if ( isset( $bulkmail_options['dkim'] ) && isset( $bulkmail_options['dkim_private_key'] ) && empty( $bulkmail_options['dkim_private_hash'] ) ) {
				$bulkmail_options['dkim_private_hash'] = md5( $bulkmail_options['dkim_private_key'] );
			}

		case '2.1.29':
		case '2.1.30':
			if ( isset( $bulkmail_options['php_mailer'] ) && $bulkmail_options['php_mailer'] ) {
				bulkmail_notice( sprintf( 'PHPMailer has been updated to 5.2.21. <a href="%s">read more</a>', 'https://github.com/PHPMailer/PHPMailer/releases/tag/v5.2.20' ), '', false, 'phpmailer' );
				$bulkmail_options['php_mailer'] = 'latest';
			}

		case '2.1.31':
		case '2.1.32':
		case '2.1.33':
			$bulkmail_options['tags']['address'] = '';
			$bulkmail_options['high_dpi']        = true;
			update_option( 'bulkmail', time() );
			update_option( 'bulkmail_setup', time() );
			update_option( 'bulkmail_templates', '' );
			update_option( 'bulkmail_cron_lasthit', '' );
			delete_option( 'bulkmail_purchasecode_disabled' );
			$bulkmail_options['welcome']              = true;
			$bulkmail_options['legacy_hooks']         = true;
			$bulkmail_options['_flush_rewrite_rules'] = true;
			update_option( 'bulkmail_license', $bulkmail_options['purchasecode'] );

		case '2.2':
		case '2.2.1':
		case '2.2.2':
			$bulkmail_options['_flush_rewrite_rules'] = true;

		case '2.2.3':
		case '2.2.4':
			$wpdb->query( "UPDATE {$wpdb->options} SET autoload = 'no' WHERE option_name IN ('bulkmail_templates', 'bulkmail_cron_lasthit')" );

		case '2.2.5':
		case '2.2.6':
			$wpdb->query( "UPDATE {$wpdb->options} SET autoload = 'yes' WHERE option_name IN ('bulkmail_username', 'bulkmail_email')" );

		case '2.2.7':
		case '2.2.8':
		case '2.2.9':
		case '2.2.10':
			update_option( 'bulkmail_hooks', get_option( 'bulkmail_hooks', '' ) );

		case '2.2.11':
		case '2.2.12':
		case '2.2.13':
		case '2.2.14':
		case '2.2.15':
		case '2.2.16':
		case '2.2.17':
		case '2.2.18':
			// since 2.3
			$bulkmail_options['webversion_bar'] = true;
			$bulkmail_options['track_opens']    = true;
			$bulkmail_options['track_clicks']   = true;

			update_option( 'bulkmail_cron_lasthit', '' );

			// allow NULL values on two columns
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}bulkmail_actions CHANGE `subscriber_id` `subscriber_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}bulkmail_actions CHANGE `campaign_id` `campaign_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL" );

			$bulkmail_options['welcome']              = true;
			$bulkmail_options['_flush_rewrite_rules'] = true;
			$show_update_notice                       = true;

		case '2.3':
		case '2.3.1':
		case '2.3.2':
		case '2.3.3':
		case '2.3.4':
		case '2.3.5':
			$bulkmail_options['track_location'] = $bulkmail_options['trackcountries'];

		case '2.3.6':
			$bulkmail_options['gdpr_link']  = $default_options['gdpr_link'];
			$bulkmail_options['gdpr_text']  = $default_options['gdpr_text'];
			$bulkmail_options['gdpr_error'] = $default_options['gdpr_error'];

		case '2.3.7':
			bulkmail( 'helper' )->mkdir( '', true );
			bulkmail( 'helper' )->mkdir( 'templates', true );
			bulkmail( 'helper' )->mkdir( 'screenshots', true );
			bulkmail( 'helper' )->mkdir( 'backgrounds', true );

		case '2.3.8':
		case '2.3.9':
		case '2.3.10':
		case '2.3.11':
		case '2.3.12':
		case '2.3.13':
			// allow NULL values on one column
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}bulkmail_subscriber_meta CHANGE `subscriber_id` `subscriber_id` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL" );
			$bulkmail_options['_flush_rewrite_rules'] = true;
		case '2.3.14':
			// remove entries caused by wrong tracking
			$wpdb->query( "DELETE FROM {$wpdb->prefix}bulkmail_actions WHERE subscriber_id = 0" );

		case '2.3.15':
		case '2.3.16':
			$bulkmail_options['ask_usage_tracking'] = true;

		case '2.3.17':
			if ( isset( $bulkmail_options['bounce_ssl'] ) && $bulkmail_options['bounce_ssl'] ) {
				$bulkmail_options['bounce_secure'] = 'ssl';
			}

		case '2.3.18':
		case '2.3.19':
			// no longer in use
			delete_option( 'bulkmail_template_licenses' );
			$bulkmail_options['welcome'] = true;

		case '2.4':
		case '2.4.1':
			// changes dummy image server
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET `post_content` = replace(post_content, %s, %s) WHERE post_type = 'newsletter'", '//dummy.newsletter-plugin.com/', '//dummy.bulkmail.co/' ) );

		case '2.4.2':
			$bulkmail_options['_flush_rewrite_rules'] = true;

		case '2.4.3':
			if ( get_option( 'bulkmail' ) && get_option( 'bulkmail' ) < strtotime( '2018-01-01 00:00' ) ) {
				$bulkmail_options['legacy_hooks'] = true;
			}
			// prefix bulkmail entries with "_"
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->usermeta} SET `meta_key` = replace(meta_key, %s, %s) WHERE meta_key LIKE 'bulkmail_%'", 'bulkmail_', '_bulkmail_' ) );

		case '2.4.4':
		case '2.4.5':
		case '2.4.5.1':
			// remove white space from these fields
			$wpdb->query( "UPDATE {$wpdb->prefix}bulkmail_forms SET `redirect` = TRIM(redirect)" );
			$wpdb->query( "UPDATE {$wpdb->prefix}bulkmail_forms SET `confirmredirect` = TRIM(confirmredirect)" );

		case '2.4.6':
		case '2.4.7':
		case '2.4.8':
			$texts['gdpr_text']  = $bulkmail_options['gdpr_text'];
			$texts['gdpr_error'] = $bulkmail_options['gdpr_error'];

			update_option( 'bulkmail_templates', '' );

		case '2.4.9':
			delete_option( 'bulkmail_recent_feeds' );

		case '2.4.10':
			$options['mail_opt_out'] = isset( $options['bounce'] ) && $options['bounce'];

			if ( ! is_plugin_active( 'bulkmail-gmail/bulkmail-gmail.php' ) && 'gmail' == $bulkmail_options['deliverymethod'] ) {

				if ( $bulkmail_option['gmail_user'] && $bulkmail_option['gmail_pwd'] ) {
					$bulkmail_options['smtp_host']    = 'smtp.googlemail.com';
					$bulkmail_options['smtp_port']    = 587;
					$bulkmail_options['smtp_timeout'] = 10;
					$bulkmail_options['smtp_auth']    = true;
					$bulkmail_options['smtp_user']    = bulkmail_option( 'gmail_user' );
					$bulkmail_options['smtp_pwd']     = bulkmail_option( 'gmail_pwd' );
					$bulkmail_options['smtp_secure']  = 'tls';
					$bulkmail_options['gmail_user']   = '';
					$bulkmail_options['gmail_pwd']    = '';

				}

				bulkmail_notice( sprintf( esc_html__( 'The Gmail Sending Method is deprecated and will soon not work anymore! Please update to the new plugin %1$s and follow our setup guide %2$s.', 'bulkmail-gmail' ), '<a href="' . admin_url( 'plugin-install.php?s=bulkmail-gmail+everpress&tab=search&type=term' ) . '">Bulkmail Gmail Integration</a>', '<a href="https://emailmarketing.run/" class="external">' . esc_html__( 'here', 'bulkmail' ) . '</a>' ), 'error', false, 'gmail_deprecated' );
			}

		case '2.4.11':
		case '2.4.12':
			delete_transient( 'bulkmail_verified' );

		default:
			// reset translations
			update_option( 'bulkmail_translation', '' );

			do_action( 'bulkmail_update', $old_version_sanitized, $new_version );
			do_action( 'bulkmail_update_' . $old_version_sanitized, $new_version );

	}

	update_option( 'bulkmail_version_old', $old_version );
	update_option( 'bulkmail_updated', time() );

}

// do stuff every update
$bulkmail_texts = $texts;

// update options
update_option( 'bulkmail_options', $bulkmail_options );
// update texts
update_option( 'bulkmail_texts', $bulkmail_texts );

// update caps
bulkmail( 'settings' )->update_capabilities();

// clear cache
bulkmail_clear_cache();

// delete plugin hash
delete_transient( 'bulkmail_hash' );


// bulkmail_update_option('welcome', true);
add_action( 'shutdown', array( 'UpdateCenterPlugin', 'clear_options' ) );

if ( $old_version && $show_update_notice ) {
	bulkmail_notice(
		array(
			'key' => 'update_info',
			'cb'  => 'bulkmail_update_notice',
		)
	);
}
