<?php

class BulkmailSettings {

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 70 );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_init', array( &$this, 'actions' ) );
		add_action( 'admin_init', array( &$this, 'maybe_create_homepage' ) );

	}


	public function admin_init() {

		add_action( 'bulkmail_deliverymethod_tab_simple', array( &$this, 'deliverytab_simple' ) );
		add_action( 'bulkmail_deliverymethod_tab_smtp', array( &$this, 'deliverytab_smtp' ) );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function maybe_create_homepage() {

		if ( isset( $_GET['bulkmail_create_homepage'] ) && wp_verify_nonce( $_GET['bulkmail_create_homepage'], 'bulkmail_create_homepage' ) ) {

			if ( $homepage = bulkmail_option( 'homepage' ) ) {

				bulkmail_notice( esc_html__( 'Homepage already created!', 'bulkmail' ), '', true );
				wp_redirect( 'post.php?post=' . $homepage . '&action=edit' );
				exit;

			} else {

				include BULKEMAIL_DIR . 'includes/static.php';

				if ( $id = wp_insert_post( $bulkmail_homepage ) ) {
					bulkmail_notice( esc_html__( 'Homepage created!', 'bulkmail' ), 'info', true );
					bulkmail_update_option( 'homepage', $id );
					bulkmail_remove_notice( 'no_homepage' );
					bulkmail_remove_notice( 'wrong_homepage_status' );
					wp_redirect( 'post.php?post=' . $id . '&action=edit&message=10' );
					exit;
				}
			}
		}
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_defaults() {

		$current_user = wp_get_current_user();
		$email        = $current_user->user_email ? $current_user->user_email : get_bloginfo( 'admin_email' );
		$from_name    = $current_user->first_name ? sprintf( esc_html_x( '%1$s from %2$s', '[Name] from [Blogname]', 'bulkmail' ), $current_user->first_name, get_bloginfo( 'name' ) ) : get_bloginfo( 'name' );

		$gdpr_link = '';
		if ( $wp_page_for_privacy_policy = (int) get_option( 'wp_page_for_privacy_policy' ) ) {
			$gdpr_link = get_permalink( $wp_page_for_privacy_policy );
		}

		global $wp_roles;

		$host = ! bulkmail_is_local() ? $_SERVER['HTTP_HOST'] : '';

		include BULKEMAIL_DIR . 'includes/static.php';

		return array(
			'from_name'                          => $from_name,
			'from'                               => $email,
			'reply_to'                           => $email,
			'send_offset'                        => 0,
			'respect_content_type'               => true,
			'timezone'                           => false,
			'embed_images'                       => false,
			'track_opens'                        => true,
			'track_clicks'                       => true,
			'track_location'                     => false,
			'tags_webversion'                    => false,
			'gdpr_forms'                         => false,
			'gdpr_link'                          => $gdpr_link,
			'module_thumbnails'                  => false,
			'charset'                            => 'UTF-8',
			'encoding'                           => '8bit',
			'post_count'                         => 30,
			'autoupdate'                         => 'minor',

			'system_mail'                        => false,

			'default_template'                   => 'mymail',
			'logo_link'                          => get_bloginfo( 'url' ),
			'high_dpi'                           => true,

			'homepage'                           => false,
			'frontpage_public'                   => false,
			'webversion_bar'                     => true,
			'frontpage_pagination'               => true,
			'share_button'                       => true,
			'share_services'                     => array(
				'twitter',
				'facebook',
			),
			'slug'                               => 'newsletter',
			'slugs'                              => array(
				'confirm'     => sanitize_title( esc_html_x( 'confirm', 'confirm slug', 'bulkmail' ), 'confirm' ),
				'subscribe'   => sanitize_title( esc_html_x( 'subscribe', 'subscribe slug', 'bulkmail' ), 'subscribe' ),
				'unsubscribe' => sanitize_title( esc_html_x( 'unsubscribe', 'unsubscribe slug', 'bulkmail' ), 'unsubscribe' ),
				'profile'     => sanitize_title( esc_html_x( 'profile', 'profile slug', 'bulkmail' ), 'profile' ),
			),
			'hasarchive'                         => false,
			'archive_slug'                       => 'newsletter',
			'archive_types'                      => array( 'finished', 'active' ),
			'subscriber_notification'            => true,
			'subscriber_notification_receviers'  => $email,
			'subscriber_notification_template'   => 'notification.html',
			'unsubscribe_notification'           => false,
			'unsubscribe_notification_receviers' => false,
			'unsubscribe_notification_receviers' => $email,
			'unsubscribe_notification_template'  => 'notification.html',
			'track_users'                        => false,
			'do_not_track'                       => false,
			'list_based_opt_in'                  => true,
			'single_opt_out'                     => false,
			'mail_opt_out'                       => true,
			'custom_field'                       => array(),
			'sync'                               => false,
			'synclist'                           => array(
				'firstname' => 'first_name',
				'lastname'  => 'last_name',
			),
			'delete_wp_subscriber'               => false,
			'delete_wp_user'                     => false,
			'register_comment_form'              => false,
			'register_comment_form_status'       => array( '1', '0' ),
			'register_comment_form_confirmation' => true,
			'register_comment_form_lists'        => array(),
			'register_signup_confirmation'       => true,
			'register_signup_lists'              => array(),
			'register_other'                     => false,
			'register_other_confirmation'        => true,
			'register_other_lists'               => array(),
			'register_other_roles'               => ( $wp_roles ) ? array_keys( $wp_roles->get_names() ) : array( 'administrator' ),
			'tags'                               => array(
				'can-spam'     => sprintf( esc_html__( 'You have received this email because you have subscribed to %s as {email}. If you no longer wish to receive emails please {unsub}.', 'bulkmail' ), '<a href="{homepage}">{company}</a>' ),
				'notification' => esc_html__( 'If you received this email by mistake, simply delete it. You won\'t be subscribed if you don\'t click the confirmation link', 'bulkmail' ),
				'copyright'    => '&copy; {year} {company}, ' . esc_html__( 'All rights reserved.', 'bulkmail' ),
				'company'      => get_bloginfo( 'name' ),
				'address'      => '',
				'homepage'     => get_bloginfo( 'url' ),
			),
			'custom_tags'                        => array(),

			'tweet_cache_time'                   => 60,

			'interval'                           => 5,
			'send_at_once'                       => 20,
			'send_limit'                         => 10000,
			'send_period'                        => 24,
			'time_frame_from'                    => 0,
			'time_frame_to'                      => 0,
			'time_frame_day'                     => null,
			'split_campaigns'                    => true,
			'pause_campaigns'                    => false,
			'send_delay'                         => 0,
			'max_execution_time'                 => 0,
			'cron_service'                       => 'wp_cron',
			'cron_secret'                        => md5( uniqid() ),
			'cron_lock'                          => 'db',

			'deliverymethod'                     => 'simple',
			'simplemethod'                       => 'mail',
			'sendmail_path'                      => '/usr/sbin/sendmail',
			'smtp'                               => false,
			'smtp_host'                          => '',
			'smtp_port'                          => 25,
			'smtp_timeout'                       => 10,
			'smtp_secure'                        => '',
			'smtp_auth'                          => false,
			'smtp_user'                          => '',
			'smtp_pwd'                           => '',

			'bounce'                             => false,
			'bounce_active'                      => false,
			'bounce_server'                      => '',
			'bounce_port'                        => 110,
			'bounce_user'                        => '',
			'bounce_pwd'                         => '',
			'bounce_attempts'                    => 3,
			'bounce_delete'                      => true,
			'bounce_check'                       => 5,
			'bounce_delay'                       => 60,

			'spf_domain'                         => $host,

			'dkim'                               => false,
			'dkim_selector'                      => 'bulkmail',
			'dkim_domain'                        => $host,
			'dkim_identity'                      => '',
			'dkim_passphrase'                    => '',

			'usage_tracking'                     => false,
			'ask_usage_tracking'                 => true,
			'disable_cache'                      => false,
			'shortcodes'                         => false,
			'remove_data'                        => false,
			'got_url_rewrite'                    => bulkmail( 'helper' )->got_url_rewrite(),
			'post_nonce'                         => wp_create_nonce( uniqid() ),

			'welcome'                            => false,
			'setup'                              => true,

			'ID'                                 => md5( uniqid() ),

		);

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_default_texts( $domain = 'bulkmail' ) {

		return array(
			'confirmation'          => esc_html__( 'Please confirm your subscription!', $domain ),
			'success'               => esc_html__( 'Thanks for your interest!', $domain ),
			'error'                 => esc_html__( 'Following fields are missing or incorrect', $domain ),
			'newsletter_signup'     => esc_html__( 'Sign up to our newsletter', $domain ),
			'unsubscribe'           => esc_html__( 'You have successfully unsubscribed!', $domain ),
			'unsubscribeerror'      => esc_html__( 'An error occurred! Please try again later!', $domain ),
			'profile_update'        => esc_html__( 'Profile updated!', $domain ),
			'email'                 => esc_html__( 'Email', $domain ),
			'firstname'             => esc_html__( 'First Name', $domain ),
			'lastname'              => esc_html__( 'Last Name', $domain ),
			'lists'                 => esc_html__( 'Lists', $domain ),
			'submitbutton'          => esc_html__( 'Subscribe', $domain ),
			'profilebutton'         => esc_html__( 'Update Profile', $domain ),
			'unsubscribebutton'     => esc_html__( 'Yes, unsubscribe me', $domain ),
			'unsubscribelink'       => esc_html_x( 'unsubscribe', 'unsubscribelink', $domain ),
			'webversion'            => esc_html__( 'webversion', $domain ),
			'forward'               => esc_html__( 'forward to a friend', $domain ),
			'profile'               => esc_html__( 'update profile', $domain ),
			'already_registered'    => esc_html__( 'You are already registered', $domain ),
			'new_confirmation_sent' => esc_html__( 'A new confirmation message has been sent', $domain ),
			'enter_email'           => esc_html__( 'Please enter your email address', $domain ),
			'gdpr_text'             => esc_html__( 'I agree to the privacy policy and terms.', $domain ),
			'gdpr_error'            => esc_html__( 'You have to agree to the privacy policy and terms!', $domain ),
		);

	}


	/**
	 *
	 *
	 * @param unknown $capabilities (optional)
	 */
	private function define_settings( $capabilities = true ) {

		$bulkmail_options = bulkmail_options();

		$options = $this->get_defaults();

		// merge options with Bulkmail options (don't override)
		$bulkmail_options = wp_parse_args( $bulkmail_options, $options );

		update_option( 'bulkmail_options', $bulkmail_options );

		if ( $capabilities ) {
			$this->set_capabilities();
		}

	}

	public function define_texts( $overwrite = false ) {

		$texts = $this->get_default_texts();

		if ( $overwrite ) {
			$bulkmail_texts = $texts;
		} else {
			$bulkmail_texts = wp_parse_args( bulkmail_texts(), $texts );
		}

		update_option( 'bulkmail_texts', $bulkmail_texts );

	}


	public function maybe_repair_options( $options ) {

		global $wpdb;

		if ( $options ) {
			return;
		}

		$serialized_string = $wpdb->get_var( "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'bulkmail_options'" );
		if ( ! $serialized_string ) {
			return;
		}

		$bulkmail_options = bulkmail( 'helper' )->unserialize( $serialized_string );
		if ( update_option( 'bulkmail_options', $bulkmail_options ) ) {
			bulkmail_notice( sprintf( esc_html__( 'There was a problem in your Bulkmail settings which has been automatically fixed! Either way it\'s good to check %s if everything is in place.', 'bulkmail' ), '<a href="edit.php?post_type=newsletter&page=bulkmail_settings&bulkmail_remove_notice=error_settings">' . esc_html__( 'the settings page', 'bulkmail' ) . '</a>' ), 'error', 1800, 'error_settings' );
		}

		return $bulkmail_options;

	}


	public function actions() {

		if ( isset( $_GET['reset-settings'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulkmail-reset-settings' ) ) {
			$this->reset_settings( true );
		}

		if ( isset( $_GET['reset-capabilities'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulkmail-reset-capabilities' ) ) {
			$this->reset_capabilities( true );
		}

		if ( isset( $_GET['reset-limits'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulkmail-reset-limits' ) ) {
			$this->reset_limits( true );
		}

		if ( isset( $_GET['release-cronlock'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulkmail-release-cronlock' ) ) {
			$this->release_cronlock( true );
		}

		if ( isset( $_GET['reset-lasthit'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulkmail-reset-lasthit' ) ) {
			$this->reset_lasthit( true );
		}

	}


	public function admin_menu() {

		global $submenu;

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Newsletter Settings', 'bulkmail' ), esc_html__( 'Settings', 'bulkmail' ), 'manage_options', 'bulkmail_settings', array( &$this, 'newsletter_settings' ) );

		add_action( 'load-' . $page, array( &$this, 'scripts_styles' ) );

		if ( current_user_can( 'manage_options' ) ) {
			$submenu['options-general.php'][] = array(
				esc_html__( 'Newsletter', 'bulkmail' ),
				'manage_options',
				'edit.php?post_type=newsletter&page=bulkmail_settings',
				esc_html__( 'Newsletter', 'bulkmail' ),
			);
		}

	}


	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );

		wp_enqueue_style( 'bulkmail-settings-style', BULKEMAIL_URI . 'assets/css/settings-style' . $suffix . '.css', array(), BULKEMAIL_VERSION );
		wp_enqueue_script( 'bulkmail-settings-script', BULKEMAIL_URI . 'assets/js/settings-script' . $suffix . '.js', array( 'bulkmail-script', 'bulkmail-clipboard-script' ), BULKEMAIL_VERSION, true );

		bulkmail_localize_script(
			'settings',
			array(
				'add'              => esc_html__( 'add', 'bulkmail' ),
				'fieldname'        => esc_html__( 'Field Name', 'bulkmail' ),
				'tag'              => esc_html__( 'Tag', 'bulkmail' ),
				'type'             => esc_html__( 'Type', 'bulkmail' ),
				'textfield'        => esc_html__( 'Textfield', 'bulkmail' ),
				'textarea'         => esc_html__( 'Textarea', 'bulkmail' ),
				'dropdown'         => esc_html__( 'Dropdown Menu', 'bulkmail' ),
				'radio'            => esc_html__( 'Radio Buttons', 'bulkmail' ),
				'checkbox'         => esc_html__( 'Checkbox', 'bulkmail' ),
				'datefield'        => esc_html__( 'Date', 'bulkmail' ),
				'default_field'    => esc_html__( 'default', 'bulkmail' ),
				'default_checked'  => esc_html__( 'checked by default', 'bulkmail' ),
				'default_selected' => esc_html__( 'this field is selected by default', 'bulkmail' ),
				'add_field'        => esc_html__( 'add field', 'bulkmail' ),
				'options'          => esc_html__( 'Options', 'bulkmail' ),
				'save_to_test'     => esc_html__( 'Save to Test', 'bulkmail' ),
				'loading'          => esc_html__( 'Loading', 'bulkmail' ),
				'remove_field'     => esc_html__( 'remove field', 'bulkmail' ),
				'move_up'          => esc_html__( 'move up', 'bulkmail' ),
				'move_down'        => esc_html__( 'move down', 'bulkmail' ),
				'reserved_tag'     => esc_html__( '%s is a reserved tag!', 'bulkmail' ),
				'create_new_keys'  => esc_html__( 'You are about to create new DKIM keys. The old ones will get deleted. Continue?', 'bulkmail' ),
				'import_data'      => esc_html__( 'You are about to overwrite your exists settings with new ones. The old ones will get deleted. Continue?', 'bulkmail' ),
				'reset_data'       => esc_html__( 'Do you really like to reset the options? This cannot be undone!', 'bulkmail' ),
				'sync_wp_user'     => esc_html__( 'You are about to overwrite all subscriber data with the matching WordPress User data. Continue?', 'bulkmail' ),
				'sync_subscriber'  => esc_html__( 'You are about to overwrite all WordPress User data with the matching subscriber data. Continue?', 'bulkmail' ),
			)
		);

	}


	public function register_settings() {

		// General
		register_setting( 'bulkmail_settings', 'bulkmail_options', array( &$this, 'verify' ) );
		register_setting( 'bulkmail_settings', 'bulkmail_texts', array( &$this, 'verify_texts' ) );

	}


	public function newsletter_settings() {

		$bulkmail_options = bulkmail_options();

		if ( ! $bulkmail_options ) : ?>
			<div class="wrap">
			<h2><?php esc_html_e( 'Ooops, looks like your settings are missing or broken :(', 'bulkmail' ); ?></h2>

			<p><a href="edit.php?post_type=newsletter&page=bulkmail_settings&reset-settings=1&_wpnonce=<?php echo wp_create_nonce( 'bulkmail-reset-settings' ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Reset all settings now', 'bulkmail' ); ?></a></p>
			</div>

			<?php
		else :

			include BULKEMAIL_DIR . 'views/settings.php';

		endif;
	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		if ( $new ) {
			$this->define_settings();
			$this->define_texts();
			bulkmail_update_option( 'got_url_rewrite', bulkmail( 'helper' )->got_url_rewrite() );
		}

		if ( bulkmail_option( 'cron_lock' ) == 'file' && ! is_dir( BULKEMAIL_UPLOAD_DIR ) || ! wp_is_writable( BULKEMAIL_UPLOAD_DIR ) ) {
			bulkmail_update_option( 'cron_lock', 'db' );
		}

	}


	/**
	 *
	 *
	 * @param unknown $redirect (optional)
	 */
	public function reset_settings( $redirect = false ) {

		if ( is_super_admin() ) {

			$bulkmail_options = $this->get_defaults();
			$bulkmail_texts   = $this->get_default_texts();

			$bulkmail_options['setup'] = false;

			if ( update_option( 'bulkmail_options', $bulkmail_options ) ) {
				bulkmail_notice( esc_html__( 'Options have been reset!', 'bulkmail' ), 'success', true );
			}
			if ( update_option( 'bulkmail_texts', $bulkmail_texts ) ) {
				bulkmail_notice( esc_html__( 'Texts have been reset!', 'bulkmail' ), 'success', true );
			}

			if ( $redirect ) {
				wp_redirect( 'edit.php?post_type=newsletter&page=bulkmail_settings' );
				exit;
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $redirect (optional)
	 */
	public function reset_limits( $redirect = false ) {

		update_option( '_transient_timeout__bulkmail_send_period_timeout', false );
		update_option( '_transient__bulkmail_send_period_timeout', false );
		update_option( '_transient__bulkmail_send_period', 0 );

		bulkmail_notice( esc_html__( 'Limits have been reset', 'bulkmail' ), '', true );
		bulkmail_remove_notice( 'dailylimit' );

		if ( $redirect ) {
			wp_redirect( 'edit.php?post_type=newsletter&page=bulkmail_settings#delivery' );
			exit;
		}

	}


	/**
	 *
	 *
	 * @param unknown $redirect (optional)
	 */
	public function reset_capabilities( $redirect = false ) {

		if ( current_user_can( 'bulkmail_manage_capabilities' ) ) {

			$this->remove_capabilities();
			$this->set_capabilities();

			if ( $redirect ) {
				wp_redirect( 'edit.php?post_type=newsletter&page=bulkmail_settings#capabilities' );
				exit;
			}
		}

	}


	public function release_cronlock( $redirect = false ) {

		bulkmail( 'cron' )->unlock();
		if ( $redirect ) {
			wp_redirect( 'edit.php?post_type=newsletter&page=bulkmail_settings#cron' );
			exit;
		}

	}

	public function reset_lasthit( $redirect = false ) {

		update_option( 'bulkmail_cron_lasthit', array() );
		if ( $redirect ) {
			wp_redirect( 'edit.php?post_type=newsletter&page=bulkmail_settings#cron' );
			exit;
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function update_capabilities() {

		global $wp_roles;

		if ( ! $wp_roles ) {
			return;
		}

		include_once BULKEMAIL_DIR . 'includes/capability.php';

		foreach ( $bulkmail_capabilities as $capability => $data ) {

			// admin has the cap so go on
			if ( isset( $wp_roles->roles['administrator']['capabilities'][ $capability ] ) ) {
				continue;
			}

			$wp_roles->add_cap( 'administrator', $capability );

			foreach ( $wp_roles->roles as $role => $d ) {
				if ( ! isset( $d['capabilities'][ $capability ] ) && in_array( $role, $data['roles'] ) ) {
					$wp_roles->add_cap( $role, $capability );
				}
			}
		}

		return true;
	}


	public function set_capabilities() {

		global $wp_roles;
		require BULKEMAIL_DIR . 'includes/capability.php';

		if ( ! $wp_roles ) {
			add_action( 'shutdown', array( &$this, 'set_capabilities' ) );
			return;
		}

		$roles  = $wp_roles->get_names();
		$newcap = array();

		foreach ( $roles as $role => $title ) {

			$newcap[ $role ] = array();
		}

		foreach ( $bulkmail_capabilities as $capability => $data ) {

			// give admin all rights
			array_unshift( $data['roles'], 'administrator' );

			foreach ( $data['roles'] as $role ) {
				$wp_roles->add_cap( $role, $capability );
				$newcap[ $role ][] = $capability;

			}
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function remove_capabilities() {

		global $wp_roles;
		require BULKEMAIL_DIR . 'includes/capability.php';

		if ( ! $wp_roles ) {
			return false;
		}

		$roles                 = array_keys( $wp_roles->roles );
		$bulkmail_capabilities = array_keys( $bulkmail_capabilities );

		foreach ( $roles as $role ) {
			$capabilities = $wp_roles->roles[ $role ]['capabilities'];
			foreach ( $capabilities as $capability => $has ) {
				if ( in_array( $capability, $bulkmail_capabilities ) ) {
					$wp_roles->remove_cap( $role, $capability );
				}
			}
		}

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $options
	 * @return unknown
	 */
	public function verify( $options ) {

		global $wpdb, $wp_rewrite;

		// merge old data
		if ( isset( $_POST['mymail_options'] ) ) {
			$options = wp_parse_args( $_POST['mymail_options'], $options );
		}

		$old_options = get_option( 'bulkmail_options', array() );

		// import data
		if ( isset( $_POST['bulkmail_import_data'] ) ) {

			if ( empty( $_POST['bulkmail_settings_data'] ) ) {

				$this->add_settings_error( esc_html__( 'No data to import', 'bulkmail' ), 'no_data' );

			} else {
				$settings = $this->import_settings( $_POST['bulkmail_settings_data'] );

				if ( is_wp_error( $settings ) ) {

					$this->add_settings_error( $settings->get_error_message(), 'import_settings' );

				} else {

					$options                 = $settings['options'];
					$_POST['bulkmail_texts'] = $settings['texts'];
					$this->add_settings_error( esc_html__( 'Settings imported!', 'bulkmail' ), 'import_settings', 'updated' );

				}
			}
		}

		// create dkim keys
		if ( isset( $_POST['bulkmail_generate_dkim_keys'] ) ) {

			try {

				$res = openssl_pkey_new(
					array(
						'private_key_bits' => ( isset( $options['dkim_bitsize'] ) ? (int) $options['dkim_bitsize'] : 512 ),
					)
				);

				if ( ! $res ) {
					throw new Exception( 'error executing openssl_pkey_new', 1 );
				}

				if ( ! openssl_pkey_export( $res, $dkim_private_key ) ) {
					throw new Exception( 'error executing openssl_pkey_export', 1 );
				}

				if ( $dkim_public_key = openssl_pkey_get_details( $res ) ) {
					$dkim_public_key             = $dkim_public_key['key'];
					$options['dkim_public_key']  = $dkim_public_key;
					$options['dkim_private_key'] = $dkim_private_key;

					$this->add_settings_error( esc_html__( 'New DKIM keys have been created!', 'bulkmail' ), 'new_dkim_keys', 'updated' );
				} else {

					throw new Exception( 'error executing openssl_pkey_get_details', 1 );
				}
			} catch ( Exception $e ) {

				$options['dkim_public_key']  = '';
				$options['dkim_private_key'] = '';
				$this->add_settings_error( esc_html__( 'Not able to create new DKIM keys!', 'bulkmail' ) . '<br>' . $e->getMessage(), 'new_dkim_keys' );

			}
		}

		$options['send_offset']  = max( 0, (int) $options['send_offset'] );
		$options['post_count']   = max( 1, (int) $options['post_count'] );
		$options['bounce_check'] = max( 1, (int) $options['bounce_check'] );
		$options['bounce_delay'] = max( 1, (int) $options['bounce_delay'] );

		if ( ! $options['send_at_once'] ) {
			$options['send_at_once'] = 10;
		}

		if ( ! $options['send_limit'] ) {
			$options['send_limit'] = 1000;
		}

		if ( ! $options['send_period'] ) {
			$options['send_period'] = 24;
		}

		if ( ! $options['send_delay'] ) {
			$options['send_delay'] = 0;
		}

		if ( ! $options['max_execution_time'] ) {
			$options['max_execution_time'] = 0;
		}

		if ( ! $options['interval'] ) {
			$options['interval'] = 5;
		}

		if ( ! $options['ID'] ) {
			$options['ID'] = md5( uniqid() );
		}

		foreach ( $options as $id => $value ) {

			// skip certain values
			if ( in_array( $id, array( 'dkim_private_hash' ) ) ) {
				continue;
			}

			$old = isset( $old_options[ $id ] ) ? $old_options[ $id ] : null;

			switch ( $id ) {

				case 'from':
				case 'reply_to':
				case 'bounce':
					if ( $value && ! bulkmail_is_email( $value ) ) {
						$this->add_settings_error( sprintf( esc_html__( '%s is not a valid email address', 'bulkmail' ), '"' . $value . '"' ), 'no_valid_email' );
						$value = $old;
					}

					break;

				case 'services':
					if ( $value ) {
						$value = array_map( 'trim', $value );
					}

					break;
				case 'track_location':
					if ( $value ) {
						if ( $value != $old ) {
							bulkmail( 'geo' )->update();
							if ( $options['track_location_update'] ) {
								bulkmail( 'geo' )->set_cron( 'daily' );
							}
						}
					} else {

						bulkmail( 'geo' )->clear_cron();
					}

					break;

				case 'track_location_update':
					if ( $value != $old ) {
						bulkmail( 'geo' )->clear_cron();

						if ( $value ) {
							bulkmail( 'geo' )->set_cron( 'daily' );
						} else {
							bulkmail( 'geo' )->set_cron();

						}
					}

					break;

				case 'homepage':
					if ( $old != $value ) {
						bulkmail_remove_notice( 'no_homepage' );
						$options['_flush_rewrite_rules'] = true;
					}
					if ( $wp_rewrite && ! get_permalink( $value ) ) {
						$this->add_settings_error( sprintf( esc_html__( 'Please define a homepage for the newsletter on the %s tab!', 'bulkmail' ), '<a href="#frontend">' . esc_html__( 'Front End', 'bulkmail' ) . '</a>' ), 'no_homepage' );
					}

					break;

				case 'slug':
					if ( empty( $value ) ) {
						$value = 'newsletter';
					}

					if ( $old != $value ) {
						$value                           = sanitize_title( $value );
						$options['_flush_rewrite_rules'] = true;
					}
					break;

				case 'slugs':
					if ( serialize( $old ) != serialize( $value ) ) {
						foreach ( $value as $key => $v ) {
							$v = sanitize_title( $v );
							if ( empty( $v ) ) {
								$v = $key;
							}
							$value[ $key ] = $v;
						}

						$options['_flush_rewrite_rules'] = true;
					}
					break;

				case 'hasarchive':
					$page = get_page_by_path( $options['archive_slug'] );
					if ( $options['hasarchive'] && $page ) {
						$this->add_settings_error( sprintf( esc_html__( 'Please change the slug or permalink of %s since it\'s used by the archive page', 'bulkmail' ), '<a href="post.php?post=' . $page->ID . '&action=edit">' . $page->post_title . '</a>' ), 'hasarchive' );
					}
					if ( $old != $value ) {
						$options['_flush_rewrite_rules'] = true;
					}
					break;

				case 'archive_slug':
					if ( empty( $value ) ) {
						$value = $options['slug'] ? $options['slug'] : 'newsletter';
					}

					$value = sanitize_title( $value );
					$page  = get_page_by_path( $value );
					if ( $options['hasarchive'] && $page ) {
						$this->add_settings_error( sprintf( esc_html__( 'Not able to set archive slug to %1$s. Used by %2$s', 'bulkmail' ), '&quot;<strong>' . $value . '</strong>&quot;', '<a href="post.php?post=' . $page->ID . '&action=edit">' . $page->post_title . '</a>' ), 'archive_slug' );
						$value = $old;
					}
					if ( $old != $value ) {
						if ( $options['hasarchive'] ) {
							$this->add_settings_error( sprintf( esc_html__( 'Your newsletter archive page is: %s', 'bulkmail' ), '<a href="' . home_url( $value ) . '" class="external">' . home_url( $value ) . '</a>' ), 'archive_slug', 'updated' );
						}

						$options['_flush_rewrite_rules'] = true;
					}
					break;

				case 'interval':
					$value = max( 0.1, $value );
					if ( $old != $value ) {
						if ( 'wp_cron' == $options['cron_service'] ) {
							bulkmail( 'cron' )->schedule( true );
						}
					}

					break;

				case 'cron_service':
					if ( $old != $value ) {
						update_option( 'bulkmail_cron_lasthit', false );
					}

					if ( 'wp_cron' == $value ) {
						bulkmail( 'cron' )->schedule();
					} else {
						bulkmail( 'cron' )->unschedule();
					}

					break;

				case 'cron_secret':
					if ( '' == $value ) {
						$value = md5( uniqid() );
					}
					if ( $old != $value ) {
						$options['_flush_rewrite_rules'] = true;
					}

					break;

				case 'cron_lock':
					if ( $old != $value ) {

						switch ( $old ) {
							case 'file':
								$lockfiles = glob( BULKEMAIL_UPLOAD_DIR . '/CRON_*.lockfile' );
								foreach ( $lockfiles as $lockfile ) {
									@unlink( $lockfile );
								}
								break;
							case 'db':
								$sql = "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'bulkmail_cron_lock_%'";
								$wpdb->query( $sql );
								break;

						}
					}

					break;

				case 'custom_field':
					if ( isset( $value[0] ) && 'empty' == $value[0] ) {
						unset( $value[0] );
					}

					if ( serialize( $old ) != serialize( $value ) ) {

						$new_value = array();

						foreach ( (array) $value as $key => $field ) {
							if ( isset( $field['id'] ) && $field['id'] != $key ) {

								$from = $key;
								$to   = trim( $field['id'] );
								if ( empty( $to ) ) {
									$to = $field['name'];
								}

								$to = sanitize_key( str_replace( ' ', '-', $to ) );

								$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}bulkmail_subscriber_fields SET meta_key = %s WHERE meta_key = %s", $to, $from ) );
								$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}bulkmail_form_fields SET field_id = %s WHERE field_id = %s", $to, $from ) );
								$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE (meta_key = '_bulkmail_list_conditions' OR meta_key = '_bulkmail_autoresponder')", 's:' . strlen( $from ) . ':"' . $from . '"', 's:' . strlen( $to ) . ':"' . $to . '"' ) );

								$key = $to;

								unset( $field['id'] );

							}
							if ( is_numeric( $key ) ) {
								$key = '_' . $key;
							}

							$new_value[ $key ] = $field;
						}

						$value = $new_value;

						if ( is_array( $old ) ) {
							$deleted = array_keys( array_diff_key( $old, $value ) );

							if ( ! empty( $deleted ) ) {

								$wpdb->query( "DELETE FROM {$wpdb->prefix}bulkmail_subscriber_fields WHERE meta_key IN ('" . implode( "','", $deleted ) . "')" );
								$wpdb->query( "DELETE FROM {$wpdb->prefix}bulkmail_form_fields WHERE field_id IN ('" . implode( "','", $deleted ) . "')" );
							}
						}
					}

					break;

				case 'subscriber_notification_delay':
					if ( $old != $value ) {

						if ( wp_next_scheduled( 'bulkmail_subscriber_notification' ) ) {
							wp_clear_scheduled_hook( 'bulkmail_subscriber_notification' );
						}

						if ( $value ) {

							$timestamp = bulkmail( 'helper' )->get_timestamp_by_string( $value );
							$timestamp = apply_filters( 'mymail_subscriber_notification_delay', apply_filters( 'bulkmail_subscriber_notification_delay', $timestamp ) );
							wp_schedule_single_event( $timestamp, 'bulkmail_subscriber_notification' );
						}
					}

					break;

				case 'unsubscribe_notification_delay':
					if ( $old != $value ) {

						if ( wp_next_scheduled( 'bulkmail_unsubscribe_notification' ) ) {
							wp_clear_scheduled_hook( 'bulkmail_unsubscribe_notification' );
						}

						if ( $value ) {

							$timestamp = bulkmail( 'helper' )->get_timestamp_by_string( $value );
							$timestamp = apply_filters( 'mymail_subscriber_unsubscribe_notification_delay', apply_filters( 'bulkmail_subscriber_unsubscribe_notification_delay', $timestamp ) );
							wp_schedule_single_event( $timestamp, 'bulkmail_unsubscribe_notification' );
						}
					}

					break;

				case 'synclist':
					if ( serialize( $old ) != serialize( $value ) ) {
						$data  = $value;
						$value = array();

						foreach ( $data as $key => $syncitem ) {
							if ( isset( $syncitem['field'] ) && $syncitem['field'] != -1 && $syncitem['meta'] != -1 ) {
								$value[ $syncitem['field'] ] = $syncitem['meta'];
							} elseif ( ! is_int( $key ) ) {
								$value[ $key ] = $syncitem;
							}
						}
					}

					break;

				case 'send_at_once':
					if ( $old != $value ) {
						// at least 1
						$value = max( (int) $value, 1 );
						if ( $value >= 200 ) {
							$this->add_settings_error( sprintf( esc_html__( 'sending %s emails at once can cause problems with statistics cause of a server timeout or to much memory usage! You should decrease it if you have problems!', 'bulkmail' ), number_format_i18n( $value ) ), 'send_at_once' );
						}
					}

					break;

				case 'send_delay':
				case 'max_execution_time':
					// at least 0
					$value = max( (int) $value, 0 );

					break;

				case 'send_period':
					if ( $old != $value ) {
						if ( $timestamp = get_option( '_transient_timeout__bulkmail_send_period_timeout' ) ) {
							$new = time() + $value * 3600;
							update_option( '_transient_timeout__bulkmail_send_period_timeout', $new );
						} else {
							update_option( '_transient__bulkmail_send_period_timeout', false );
						}
						bulkmail_remove_notice( 'dailylimit' );
					}

					break;

				case 'deliverymethod':
					if ( $old != $value ) {

					}

					break;

				case 'smtp_host':
					if ( $options['deliverymethod'] == 'smtp' ) {
						$this->check_smtp_host( $value );
					}

					if ( false & function_exists( 'fsockopen' ) && $options['deliverymethod'] == 'smtp' ) {
						$host = trim( $options['smtp_host'] );
						$port = (int) $options['smtp_port'];
						$conn = @fsockopen( $host, $port, $errno, $errstr, 5 );

						if ( is_resource( $conn ) ) {

							fclose( $conn );

						} else {

							$this->add_settings_error( sprintf( esc_html__( 'Not able to connected to %1$s via port %2$s! You may not be able to send mails cause of the locked port %3$s. Please contact your host or choose a different delivery method!', 'bulkmail' ), '"' . $host . '"', $port, $port ), 'smtp_host' );
						}
					}

					break;

				case 'roles':
					if ( serialize( $old ) != serialize( $value ) ) {
						require_once BULKEMAIL_DIR . 'includes/capability.php';

						global $wp_roles;

						if ( ! $wp_roles ) {
							break;
						}

						$newvalue = array();
						// give admin all rights
						$value['administrator'] = array();
						// foreach role
						foreach ( $value as $role => $capabilities ) {

							if ( ! isset( $newvalue[ $role ] ) ) {
								$newvalue[ $role ] = array();
							}

							foreach ( $bulkmail_capabilities as $capability => $data ) {
								if ( in_array( $capability, $capabilities ) || 'administrator' == $role ) {

									$wp_roles->add_cap( $role, $capability );
									$newvalue[ $role ][] = $capability;
								} else {
									$wp_roles->remove_cap( $role, $capability );
								}
							}
						}
						$value = $newvalue;
					}

					break;

				case 'usage_tracking':
					if ( ! $value ) {
						wp_clear_scheduled_hook( 'put_do_weekly_action' );
					}

					break;

				case 'tweet_cache_time':
					$value = (int) $value;
					if ( $value < 10 ) {
						$value = 10;
						$this->add_settings_error( sprintf( esc_html__( 'The caching time for tweets must be at least %d minutes', 'bulkmail' ), 10 ), 'tweet_cache_time' );
					}

					break;

				case 'dkim':
					if ( ! isset( $options['dkim_private_key'] ) || ! isset( $options['dkim_public_key'] ) ) {
						$this->add_settings_error( esc_html__( 'You have to generate DKIM Keys to use DKIM signed mails!', 'bulkmail' ), 'dkim' );
					}

					break;

				case 'dkim_domain':
				case 'dkim_selector':
				case 'dkim_identity':
					if ( $old != $value ) {
						$value = trim( $value );
					}
					break;

				case 'dkim_private_key':
					if ( ! isset( $options['dkim'] ) || ! $options['dkim'] ) {
						break;
					}

					$hash = md5( $value );

					$folder = BULKEMAIL_UPLOAD_DIR;
					$file   = BULKEMAIL_UPLOAD_DIR . '/dkim/' . $hash . '.pem';

					global $wp_filesystem;
					bulkmail_require_filesystem();

					// remove old
					if ( isset( $options['dkim_private_hash'] ) && is_file( $folder . '/' . $options['dkim_private_hash'] . '.pem' ) ) {
						if ( $hash != $options['dkim_private_hash'] ) {
							$wp_filesystem->delete( $folder . '/' . $options['dkim_private_hash'] . '.pem' );
						}
					}

					// create folder
					if ( ! is_dir( dirname( $file ) ) ) {
						wp_mkdir_p( dirname( $file ) );
						$wp_filesystem->put_contents( dirname( $file ) . '/index.php', '<?php //silence is golden ?>', FS_CHMOD_FILE );
					}

					if ( $wp_filesystem->put_contents( $file, $value ) ) {
						$options['dkim_private_hash'] = $hash;
					}

					break;

			}

			$options[ $id ] = $value;

		}

		// no need to save them
		if ( isset( $options['roles'] ) ) {
			unset( $options['roles'] );
		}

		// clear everything thats cached
		bulkmail_clear_cache();

		$options = apply_filters( 'mymail_verify_options', apply_filters( 'bulkmail_verify_options', $options ) );

		return $options;

	}


	/**
	 *
	 *
	 * @param unknown $texts
	 * @return unknown
	 */
	public function verify_texts( $texts ) {

		global $bulkmail_texts;

		// change language
		if ( isset( $_POST['change-language'] ) && isset( $_POST['language-file'] ) ) {

			$dir  = defined( 'WP_LANG_DIR' ) ? WP_LANG_DIR . '/plugins/' : BULKEMAIL_DIR . '/languages/';
			$file = $dir . 'bulkmail-' . esc_attr( $_POST['language-file'] ) . '.mo';

			unload_textdomain( 'bulkmail' );
			if ( file_exists( $file ) ) {
				load_textdomain( 'bulkmail', $file );
				$bulkmail_texts = $texts = $this->get_default_texts();
			} else {
				// load defaults with undefined textdomain
				$bulkmail_texts = $texts = $this->get_default_texts( 'bulkmail_en_US' );

			}

			load_plugin_textdomain( 'bulkmail', false, basename( BULKEMAIL_DIR ) . '/languages' );

		}

		return apply_filters( 'mymail_verify_texts', apply_filters( 'bulkmail_verify_texts', $texts ) );

	}


	/**
	 *
	 *
	 * @param unknown $message
	 * @param unknown $type    (optional)
	 */
	private function add_settings_error( $message, $code = null, $type = 'error' ) {

		if ( isset( $_POST['option_page'] ) && 'bulkmail_settings' == $_POST['option_page'] ) {
			if ( is_null( $code ) ) {
				$code = uniqid();
			}
			add_settings_error( 'bulkmail_settings', $code, $message, $type );

		}
	}




	public function check_smtp_host( $host ) {

		$message = esc_html__( 'Do you like to send your campaigns with %1$s? Please use our %2$s.', 'bulkmail' );
		$link    = '<a href="' . admin_url( 'plugin-install.php?s=%s&tab=search&type=term' ) . '">' . esc_html__( '%s Add-on', 'bulkmail' ) . '</a>';

		if ( false !== ( strpos( $host, '.amazonaws.com' ) ) ) {
			$service = 'Amazon SES';
			$link    = sprintf( $link, 'bulkmail-amazonses', $service );

		} elseif ( false !== ( strpos( $host, '.mailgun.org' ) ) ) {
			$service = 'Mailgun';
			$link    = sprintf( $link, 'bulkmail-mailgun', $service );

		} elseif ( false !== ( strpos( $host, 'smtp.sendgrid.net' ) ) ) {
			$service = 'SendGrid';
			$link    = sprintf( $link, 'bulkmail-sendgrid', $service );

		} elseif ( false !== ( strpos( $host, 'smtp.sparkpostmail.com' ) ) ) {
			$service = 'SparkPost';
			$link    = sprintf( $link, 'bulkmail-sparkpost', $service );

		} else {
			return;
		}

		$message = sprintf( $message, $service, $link );
		$this->add_settings_error( $message, 'deliverymethod_service' );

	}


	public function check_port( $host, $port ) {

		if ( ! function_exists( 'fsockopen' ) ) {
			return 'requires fsockopen to check ports.';
		}

		$conn = @fsockopen( $host, $port, $errno, $errstr, 5 );

		$return = ( is_resource( $conn ) ? '(' . getservbyport( $port, 'tcp' ) . ') open.' : 'closed [' . $errstr . ']' );

		is_resource( $conn ) ? fclose( $conn ) : '';

		return $return;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function export_settings( $encoded = true ) {

		$export = array(
			'version'    => BULKEMAIL_VERSION,
			'home'       => home_url(),
			'upload_dir' => BULKEMAIL_UPLOAD_DIR,
			'options'    => bulkmail_options(),
			'texts'      => bulkmail_texts(),
		);

		if ( ! $encoded ) {
			return $export;
		}

		$export = json_encode( $export );

		$export = base64_encode( $export );

		return $export;

	}


	/**
	 *
	 *
	 * @param unknown $raw
	 * @return unknown
	 */
	public function import_settings( $raw ) {

		if ( $this->export_settings() === $raw ) {
			 return new WP_Error( 'wrong_version', esc_html__( 'Settings are the same. Nothing to import!', 'bulkmail' ) );
		}

		$old_options = bulkmail_options();
		$old_texts   = bulkmail_texts();
		$import      = base64_decode( $raw, true );
		if ( base64_encode( $import ) != $raw ) {

			$import = json_decode( stripcslashes( $raw ), true );

			if ( 'array' != gettype( $import ) ) {
				return new WP_Error( 'wrong_version', esc_html__( 'The data does\'t look like valid settings!', 'bulkmail' ) );
			}
		} else {
			$import = json_decode( $import, true );
		}

		if ( false === $import ) {
			return new WP_Error( 'wrong_version', esc_html__( 'The data does\'t look like valid settings!', 'bulkmail' ) );
		}

		if ( ! isset( $import['options'] ) ) {
			$_import           = $import;
			$import            = $this->export_settings( false );
			$import['options'] = $_import;
		}

		if ( version_compare( $import['version'], '2.2' ) <= 0 ) {
			return new WP_Error( 'wrong_version', esc_html__( 'The version number of the import does not match!', 'bulkmail' ) );
		}

		$empty_values = array_fill_keys( array_keys( array_diff_key( $this->get_defaults(), $import['options'] ) ), null );
		$options      = wp_parse_args( $import['options'], $old_options );
		$texts        = wp_parse_args( $import['texts'], $old_texts );

		$templates = bulkmail( 'templates' )->get_templates();

		// template does not exists
		if ( ! in_array( $options['default_template'], array_keys( $templates ) ) ) {
			$options['default_template'] = $old_options['default_template'];
		}

		$templatefiles = bulkmail( 'templates' )->get_files( $options['default_template'] );
		if ( isset( $options['system_mail_template'] ) && ! in_array( $options['system_mail_template'], array_keys( $templatefiles ) ) ) {
			$options['system_mail_template'] = $old_options['system_mail_template'];
		}
		if ( isset( $options['subscriber_notification_template'] ) && ! in_array( $options['subscriber_notification_template'], array_keys( $templatefiles ) ) ) {
			$options['subscriber_notification_template'] = $old_options['subscriber_notification_template'];
		}
		if ( isset( $options['unsubscribe_notification_template'] ) && ! in_array( $options['unsubscribe_notification_template'], array_keys( $templatefiles ) ) ) {
			$options['unsubscribe_notification_template'] = $old_options['unsubscribe_notification_template'];
		}

		$options['ID'] = $old_options['ID'];
		if ( isset( $old_options['fallback_image'] ) ) {
			$options['fallback_image'] = $old_options['fallback_image'];
		};
		$options['cron_secret']     = $old_options['cron_secret'];
		$options['homepage']        = $old_options['homepage'];
		$options['got_url_rewrite'] = bulkmail( 'helper' )->got_url_rewrite();

		$options['_flush_rewrite_rules'] = true;

		return array(
			'options' => $options,
			'texts'   => $texts,
		);

	}


	/**
	 *
	 *
	 * @param unknown $space (optional)
	 * @return unknown
	 */
	public function get_system_info( $space = 30 ) {

		global $wpdb;

		$mail = bulkmail( 'mail' );

		$db_version = get_option( 'bulkmail_dbversion' ) == BULKEMAIL_DBVERSION
			? BULKEMAIL_DBVERSION
			: get_option( 'bulkmail_dbversion' ) . ' (should be ' . BULKEMAIL_DBVERSION . ')';

		$homepage = get_permalink( bulkmail_option( 'homepage' ) );

		$wp_id = bulkmail( 'subscribers' )->wp_id() === false ? 'ERROR: ' . $wpdb->last_error : 'OK';

		$settings = array(
			'SITE_URL'                 => site_url(),
			'HOME_URL'                 => home_url(),
			'--',
			'Bulkmail Version'         => BULKEMAIL_VERSION,
			'Updated From'             => get_option( 'bulkmail_version_old', 'N/A' ) . ' (' . date( 'r', get_option( 'bulkmail_updated' ) ) . ')',
			'Bulkmail Hash'            => bulkmail()->get_plugin_hash( true ),
			'WordPress Version'        => get_bloginfo( 'version' ),
			'Bulkmail DB Version'      => $db_version,
			'PHPMailer Version'        => $mail->mailer->Version,
			'Permalink Structure'      => get_option( 'permalink_structure' ),
			'--',
			'Newsletter Homepage'      => $homepage . ' (#' . bulkmail_option( 'homepage' ) . ')',
			'Track Opens'              => bulkmail_option( 'track_opens' ) ? 'Yes' : 'No',
			'Track Clicks'             => bulkmail_option( 'track_clicks' ) ? 'Yes' : 'No',
			'Track Location'           => bulkmail_option( 'track_location' ) ? 'Yes' : 'No',
			'--',
			'Cron Service'             => bulkmail_option( 'cron_service' ),
			'Cron URL'                 => bulkmail( 'cron' )->url(),
			'Alternative Cron URL'     => bulkmail( 'cron' )->url( true ),
			'Cron Interval'            => bulkmail_option( 'interval' ) . ' MIN',
			'--',
			'Delivery Method'          => bulkmail_option( 'deliverymethod' ),
			'Send at once'             => bulkmail_option( 'send_at_once' ),
			'Send limit'               => bulkmail_option( 'send_limit' ),
			'Send period'              => bulkmail_option( 'send_period' ),
			'--',
			'PHP Version'              => PHP_VERSION,
			'MySQL Version'            => $wpdb->db_version(),
			'Web Server Info'          => $_SERVER['SERVER_SOFTWARE'],
			'User Agent'               => $_SERVER['HTTP_USER_AGENT'],
			'Multi-site'               => is_multisite() ? 'Yes' : 'No',
			'--',
			'PHP Memory Limit'         => ini_get( 'memory_limit' ),
			'PHP Post Max Size'        => ini_get( 'post_max_size' ),
			'PHP Upload Max File size' => ini_get( 'upload_max_filesize' ),
			'PHP Time Limit'           => ini_get( 'max_execution_time' ) . ' sec',
			'PHP Max Input Vars'       => ini_get( 'max_input_vars' ),
			'--',
			'WP_DEBUG'                 => defined( 'WP_DEBUG' ) ? ( WP_DEBUG ? 'Enabled' : 'Disabled' ) : 'Not set',
			'DISPLAY ERRORS'           => ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A',
			'--',
			'WP Table Prefix'          => 'Length: ' . strlen( $wpdb->prefix ) . ' Status:' . ( strlen( $wpdb->prefix ) > 16 ? ' ERROR: Too Long' : ' Acceptable' ),
			'WP DB Charset/Collate'    => $wpdb->get_charset_collate(),
			'WP ID'                    => $wp_id,
			'--',
			'Session'                  => isset( $_SESSION ) ? 'Enabled' : 'Disabled',
			'Session Name'             => esc_html( ini_get( 'session.name' ) ),
			'Cookie Path'              => esc_html( ini_get( 'session.cookie_path' ) ),
			'Save Path'                => esc_html( ini_get( 'session.save_path' ) ),
			'Use Cookies'              => ini_get( 'session.use_cookies' ) ? 'On' : 'Off',
			'Use Only Cookies'         => ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off',
			'--',
			'WordPress Memory Limit'   => ( size_format( (int) WP_MEMORY_LIMIT * 1048576 ) ),
			'WordPress Upload Size'    => ( size_format( wp_max_upload_size() ) ),
			'Filesystem Method'        => get_filesystem_method(),
			'SSL SUPPORT'              => extension_loaded( 'openssl' ) ? 'SSL extension loaded' : 'SSL extension NOT loaded',
			'MB String'                => extension_loaded( 'mbstring' ) ? 'MB String extensions loaded' : 'MB String extensions NOT loaded',
			'--',
			'TEMPLATES'                => '',
			'--',
			'ACTIVE PLUGINS'           => '',
			'--',
			'CURRENT THEME'            => '',
		);

		$plugins        = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $plugin_path => $plugin ) :
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) ) {
				continue;
			}

			$settings['ACTIVE PLUGINS'] .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n" . str_repeat( ' ', $space );

		endforeach;

		$templates = bulkmail( 'templates' )->get_templates();
		$active    = bulkmail_option( 'default_template' );

		foreach ( $templates as $slug => $template ) :

			$settings['TEMPLATES'] .= $template['name'] . ': ' . $template['version'] . ' by ' . $template['author'] . ( $active == $slug ? ' (default)' : '' ) . "\n" . str_repeat( ' ', $space );

		endforeach;

		if ( function_exists( 'wp_get_theme' ) ) {
			$theme_data                = wp_get_theme();
			$settings['CURRENT THEME'] = $theme_data->Name . ': ' . $theme_data->Version . "\n" . str_repeat( ' ', $space ) . $theme_data->get( 'Author' ) . ' (' . $theme_data->get( 'AuthorURI' ) . ')';
		} else {
			$theme_data                = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$settings['CURRENT THEME'] = $theme_data['Name'] . ': ' . $theme_data['Version'] . "\n" . str_repeat( ' ', $space ) . $theme_data['Author'] . ' (' . $theme_data['AuthorURI'] . ')';
		}

		return apply_filters( 'mymail_system_info', apply_filters( 'bulkmail_system_info', $settings ) );

	}


	public function deliverytab_simple() {
		?>
		<div class="notice notice-error inline">
			<p><strong><?php esc_html_e( 'Sending via your host is not recommended. Please consider using a dedicate Email Service Provider instead.', 'bulkmail' ); ?></strong></p>
		</div>
		<?php $basicmethod = bulkmail_option( 'simplemethod' ); ?>
		<table class="form-table">
			<tr valign="top">
				<td><label><input type="radio" name="bulkmail_options[simplemethod]" value="sendmail" <?php checked( $basicmethod, 'sendmail' ); ?> id="sendmail"> Sendmail</label>
				<div class="sendmailpath">
					<label>Sendmail Path: <input type="text" value="<?php echo bulkmail_option( 'sendmail_path' ); ?>" class="form-input-tip" name="bulkmail_options[sendmail_path]"></label>
				</div>
				</td>
			</tr>
			<tr valign="top">
				<td><label><input type="radio" name="bulkmail_options[simplemethod]" value="mail" <?php checked( $basicmethod, 'mail' ); ?>> PHPs mail() function</label></td>
			</tr>
			<tr valign="top">
				<td><label><input type="radio" name="bulkmail_options[simplemethod]" value="qmail" <?php checked( $basicmethod, 'qmail' ); ?>> QMail</label></td>
			</tr>
		</table>
		<?php
	}


	public function deliverytab_smtp() {
		?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">SMTP Host : Port</th>
			<td><input type="text" name="bulkmail_options[smtp_host]" value="<?php echo esc_attr( bulkmail_option( 'smtp_host' ) ); ?>" class="regular-text ">:<input type="text" name="bulkmail_options[smtp_port]" id="bulkmail_smtp_port" value="<?php echo (int) bulkmail_option( 'smtp_port' ); ?>" class="small-text smtp"></td>
		</tr>
		<tr valign="top">
			<th scope="row">Timeout</th>
			<td><span><input type="text" name="bulkmail_options[smtp_timeout]" value="<?php echo bulkmail_option( 'smtp_timeout' ); ?>" class="small-text"> <?php esc_html_e( 'seconds', 'bulkmail' ); ?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Secure connection', 'bulkmail' ); ?></th>
			<?php $secure = bulkmail_option( 'smtp_secure' ); ?>
			<td>
			<label><input type="radio" name="bulkmail_options[smtp_secure]" value="" <?php checked( ! $secure ); ?> class="smtp secure" data-port="25"> <?php esc_html_e( 'none', 'bulkmail' ); ?></label>
			<label><input type="radio" name="bulkmail_options[smtp_secure]" value="ssl" <?php checked( $secure == 'ssl' ); ?> class="smtp secure" data-port="465"> SSL</label>
			<label><input type="radio" name="bulkmail_options[smtp_secure]" value="tls" <?php checked( $secure == 'tls' ); ?> class="smtp secure" data-port="465"> TLS</label>
			 </td>
		</tr>
		<tr valign="top">
			<th scope="row">SMTPAuth</th>
			<td>
			<?php $smtpauth = bulkmail_option( 'smtp_auth' ); ?>
			<label>
			<select name="bulkmail_options[smtp_auth]">
				<option value="0" <?php selected( ! $smtpauth ); ?>><?php esc_html_e( 'none', 'bulkmail' ); ?></option>
				<option value="PLAIN" <?php selected( 'PLAIN', $smtpauth ); ?>>Plain</option>
				<option value="LOGIN" <?php selected( 'LOGIN', $smtpauth ); ?>>Login</option>
				<option value="CRAM-MD5" <?php selected( 'CRAM-MD5', $smtpauth ); ?>>CRAM-MD5</option>
			</select></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Username', 'bulkmail' ); ?></th>
			<td><input type="text" name="bulkmail_options[smtp_user]" value="<?php echo esc_attr( bulkmail_option( 'smtp_user' ) ); ?>" class="regular-text"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Password', 'bulkmail' ); ?></th>
			<td><input type="password" name="bulkmail_options[smtp_pwd]" value="<?php echo esc_attr( bulkmail_option( 'smtp_pwd' ) ); ?>" class="regular-text" autocomplete="new-password"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Self Signed Certificates', 'bulkmail' ); ?></th>
			<td><label title="<?php esc_attr_e( 'Enabling this option may solve connection problems to SMTP servers', 'bulkmail' ); ?>"><input type="hidden" name="bulkmail_options[allow_self_signed]" value=""><input type="checkbox" name="bulkmail_options[allow_self_signed]" value="1" <?php checked( bulkmail_option( 'allow_self_signed' ) ); ?>> <?php esc_html_e( 'allow self signed certificates', 'bulkmail' ); ?></label>
			</td>
		</tr>
	</table>
		<?php
	}


}
