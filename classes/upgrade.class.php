<?php

class BulkmailUpgrade {

	private $performance = 1;
	private $starttime;


	public function __construct() {

		add_action( 'admin_init', array( &$this, 'init' ) );
		add_action( 'wp_ajax_bulkmail_batch_update', array( &$this, 'run_update' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

		register_activation_hook( 'myMail/myMail.php', array( &$this, 'maybe_deactivate_mymail' ) );

	}

	public function init() {

		global $pagenow;

		$old_version   = get_option( 'bulkmail_version' );
		$version_match = $old_version == BULKEMAIL_VERSION;

		if ( ! $version_match ) {

			if ( ! $old_version ) {
				$old_version = get_option( 'mymail_version' );
			}

			// update db structure
			if ( BULKEMAIL_DBVERSION != get_option( 'bulkmail_dbversion' ) ) {
				bulkmail()->dbstructure();
			}
		}

		if ( bulkmail_option( 'db_update_required' ) ) {

			$db_version = get_option( 'bulkmail_dbversion' );

			$redirectto = admin_url( 'admin.php?page=bulkmail_update' );
			$update_msg = '<p><strong>' . esc_html__( 'An additional update is required for Bulkmail!', 'bulkmail' ) . '</strong></p><a class="button button-primary" href="' . $redirectto . '" target="_top">' . esc_html__( 'Progress Update now', 'bulkmail' ) . '</a>';

			if ( 'update.php' == $pagenow ) {

				if ( isset( $_GET['success'] )
					&& isset( $_GET['action'] ) && 'activate-plugin' == $_GET['action']
					&& isset( $_GET['plugin'] ) && BULKEMAIL_SLUG == $_GET['plugin'] ) {

					echo $update_msg;

				}
			} else {

				if ( isset( $_GET['page'] ) && $_GET['page'] == 'bulkmail_update' ) {
				} else {
					if ( ! is_network_admin() && isset( $_GET['post_type'] ) && $_GET['post_type'] = 'newsletter' ) {
						wp_redirect( $redirectto );
						exit;
					} else {
						bulkmail_remove_notice( 'no_homepage' );
						bulkmail_notice( $update_msg, 'error', true, 'db_update_required' );
					}
				}
			}
		} elseif ( ! $version_match ) {

			if ( version_compare( $old_version, BULKEMAIL_VERSION, '<' ) ) {
				include BULKEMAIL_DIR . 'includes/updates.php';
			}

			update_option( 'bulkmail_version', BULKEMAIL_VERSION );
			update_option( 'bulkmail_dbversion', BULKEMAIL_DBVERSION );

		} elseif ( bulkmail_option( 'setup' ) ) {

			if ( ! is_network_admin() &&
				( ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'bulkmail_' ) !== false ) && 'bulkmail_setup' != $_GET['page'] ) ) {
				wp_redirect( 'admin.php?page=bulkmail_setup', 302 );
				exit;
			}
		} elseif ( bulkmail_option( 'welcome' ) ) {

			if ( ! is_network_admin() &&
				( ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'bulkmail_' ) !== false ) && 'bulkmail_welcome' != $_GET['page'] ) ) {
				wp_redirect( 'admin.php?page=bulkmail_welcome', 302 );
				exit;
			}
		}

	}


	public function maybe_deactivate_mymail() {

		add_action( 'update_option_active_plugins', array( &$this, 'deactivate_mymail' ) );

	}


	public function deactivate_mymail( $info = true ) {
		if ( is_plugin_active( 'myMail/myMail.php' ) ) {
			if ( $info ) {
				bulkmail_notice( 'MyMail is now Bulkmail - Plugin deactivated', 'error', true );
			}
			deactivate_plugins( 'myMail/myMail.php' );
		}
	}


	public function run_update() {

		// cron look
		set_transient( 'bulkmail_cron_lock', microtime( true ), 360 );

		global $bulkmail_batch_update_output;

		$this->starttime = microtime();

		$return['success'] = false;

		$id                = $_POST['id'];
		$this->performance = isset( $_POST['performance'] ) ? (int) $_POST['performance'] : $this->performance;

		if ( method_exists( $this, 'do_' . $id ) ) {
			$return['success'] = true;
			ob_start();
			$return[ $id ] = $this->{'do_' . $id}();
			$output        = ob_get_contents();
			ob_end_clean();
			if ( ! empty( $output ) ) {
				$return['output']  = "===========================================================\n";
				$return['output'] .= "* OUTPUT for $id (" . date( 'H:i:s', current_time( 'timestamp' ) ) . ') - ' . size_format( memory_get_peak_usage( true ), 2 ) . "\n";
				$return['output'] .= "===========================================================\n";
				$return['output'] .= strip_tags( $output ) . "\n";
			}
		}

		@header( 'Content-type: application/json' );
		echo json_encode( $return );
		exit;

	}


	/**
	 *
	 *
	 * @param unknown $args
	 */
	public function admin_menu( $args ) {

		$page = add_submenu_page( true, 'Bulkmail Update', 'Bulkmail Update', 'manage_options', 'bulkmail_update', array( &$this, 'page' ) );
		add_action( 'load-' . $page, array( &$this, 'scripts_styles' ) );

	}

	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'bulkmail-update-script', BULKEMAIL_URI . 'assets/js/upgrade-script' . $suffix . '.js', array( 'bulkmail-script' ), BULKEMAIL_VERSION, true );

		$db_version = get_option( 'bulkmail_dbversion', BULKEMAIL_DBVERSION );

		$autostart = true;

		$actions = array();

		// pre - Bulkmail time
		if ( get_option( 'mymail' ) || isset( $_GET['mymail'] ) ) {

			$autostart = false;
			$actions   = wp_parse_args(
				array(
					'pre_bulkmail_updateslug'      => 'Update Plugin Slug',
					'pre_bulkmail_backuptables'    => 'Backup old Tables',
					'pre_bulkmail_form_prepare'    => 'Checking Forms',
					'pre_bulkmail_copytables'      => 'Copy Database Tables',
					'pre_bulkmail_options'         => 'Copy Options',
					'pre_bulkmail_updatedpostmeta' => 'Update Post Meta',
					'pre_bulkmail_movefiles'       => 'Moving Files and Folders',
					'pre_bulkmail_removeoldtables' => 'Remove old Tables',
					'pre_bulkmail_removemymail'    => 'Remove old Options',
					'pre_bulkmail_legacy'          => 'Prepare Legacy mode',
				),
				$actions
			);

			$db_version = get_option( 'mymail_dbversion', 0 );

		} else {

			if ( ! get_option( 'bulkmail' ) ) {
				$actions = wp_parse_args(
					array(
						'maybe_install' => 'Installing Bulkmail',
					),
					$actions
				);

			} else {
				$actions = wp_parse_args(
					array(
						'db_structure' => 'Checking DB structure',
					),
					$actions
				);

			}
		}

		if ( isset( $_GET['hard'] ) ) {
			$db_version = 0;
			$actions    = wp_parse_args( $actions, array( 'remove_db_structure' => 'Removing DB structure' ) );
		}
		if ( isset( $_GET['redo'] ) ) {
			$db_version = 0;
		}

		if ( $db_version < 20140924 || false ) {
			$actions = wp_parse_args(
				array(
					'update_lists'           => 'updating Lists',
					'update_forms'           => 'updating Forms',
					'update_campaign'        => 'updating Campaigns',
					'update_subscriber'      => 'updating Subscriber',
					'update_list_subscriber' => 'update Lists <=> Subscribers',
					'update_actions'         => 'updating Actions',
					'update_pending'         => 'updating Pending Subscribers',
					'update_autoresponder'   => 'updating Autoresponder',
					'update_settings'        => 'updating Settings',
				),
				$actions
			);
		}

		if ( $db_version < 20150924 || false ) {
			$actions = wp_parse_args(
				array(
					'update_forms' => 'updating Forms',
				),
				$actions
			);
		}

		if ( $db_version < 20151218 || false ) {
			$actions = wp_parse_args(
				array(
					'update_db_structure' => 'Changes in DB structure',
				),
				$actions
			);
		}

		if ( $db_version < 20160105 || false ) {
			$actions = wp_parse_args(
				array(
					'remove_old_data' => 'Removing MyMail 1.x data',
				),
				$actions
			);
		}

		if ( $db_version < 20170201 || false ) {
			$actions = wp_parse_args( array(), $actions );
		}

		$actions = wp_parse_args(
			array(
				'db_check' => 'Database integrity',
				'cleanup'  => 'Cleanup',
			),
			$actions
		);

		wp_localize_script( 'bulkmail-update-script', 'bulkmail_updates', $actions );
		wp_localize_script(
			'bulkmail-update-script',
			'bulkmail_updates_options',
			array(
				'autostart' => $autostart,
			)
		);
		$performance = isset( $_GET['performance'] ) ? max( 1, (int) $_GET['performance'] ) : 1;
		wp_localize_script( 'bulkmail-update-script', 'bulkmail_updates_performance', array( $performance ) );

		remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

	}

	public function page() {

		?>
	<div class="wrap">
		<h2>Bulkmail Batch Update</h2>
		<?php wp_nonce_field( 'bulkmail_nonce', 'bulkmail_nonce', false ); ?>

		<p><strong>Some additional updates are required! Please keep this browser tab open until all updates are finished!</strong></p>
		<div id="bulkmail-update-info" style="display: none;">
			<div class="notice-error error inline"><p>Make sure to create a backup before upgrading MyMail to Bulkmail. If you experience any issues upgrading please reach out to us via our member area <a href="https://emailmarketing.run/bulk-email-sender/public/users/register" class="external">here</a>.<br>
			<strong>Important: No data can get lost thanks to our smart upgrade process.</strong></p></div>
			<p>
				<a class="button button-primary button-hero" id="bulkmail-start-upgrade">Ok, I've got a backup. Start the Update Process</a>
			</p>
		</div>
		<div id="bulkmail-update-process" style="display: none;">

			<div class="alignleft" style="width:54%">
				<div id="output"></div>
				<div id="error-list"></div>
				<form id="bulkmail-post-upgrade" action="" method="get" style="display: none;">
				<input type="hidden" name="post_type" value="newsletter">
				<input type="hidden" name="page" value="bulkmail_update">
					<input type="submit" class="hidden button button-small" name="redo" value="redo update" onclick="return confirm('Do you really like to redo the update?');">
				</form>
			</div>

			<div class="alignright" style="width:45%">
				<textarea id="textoutput" class="widefat" rows="30" style="width:100%;font-size:12px;font-family:monospace;background:none"></textarea>
			</div>

		</div>

	</div>
		<?php
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_remove_db_structure() {

		global $wpdb;

		$tables = bulkmail()->get_tables();

		foreach ( $tables as $table ) {
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', "{$wpdb->prefix}bulkmail_$table" ) );
		}

		return true;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_remove_old_data() {

		global $wpdb;

		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'bulkmail-campaign' LIMIT 1000" ) ) {
			echo 'old Campaign Data removed' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'bulkmail-campaigns' LIMIT 1000" ) ) {
			echo 'old Campaign related User Data removed' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'bulkmail-userdata' LIMIT 10000" ) ) {
			echo 'old User Data removed' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'bulkmail-data' LIMIT 1000" ) ) {
			echo 'old User Data removed' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE m FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->postmeta} AS m ON p.ID = m.post_id WHERE p.post_type = 'subscriber' AND m.post_id" ) ) {
			echo 'old User related data removed' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE a,b,c FROM {$wpdb->term_taxonomy} AS a LEFT JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id JOIN {$wpdb->term_taxonomy} AS c ON c.term_taxonomy_id = a.term_taxonomy_id WHERE a.taxonomy = 'newsletter_lists'" ) ) {
			echo 'old Lists removed' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'subscriber' LIMIT 10000" ) ) {
			echo esc_html($count) . ' old User removed' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name = 'bulkmail_confirms'" ) ) {
			echo esc_html($count) . ' old Pending User removed' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name = 'bulkmail_autoresponders'" ) ) {
			echo esc_html($count) . ' old Autoresponder Data' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name = 'bulkmail_subscribers_count'" ) ) {
			echo esc_html($count) . ' old Cache' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'bulkmail_bulk_%'" ) ) {
			echo esc_html($count) . ' old import data' . "\n";
			return false;
		}
		if ( $count = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name IN ('bulkmail_countries', 'bulkmail_cities')" ) ) {
			echo esc_html($count) . ' old data' . "\n";
			return false;
		}

		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_updateslug() {

		$this->deactivate_mymail( false );

		$active_plugins = get_option( 'active_plugins', array() );

		$slug = 'bulkmail/bulkmail.php';

		if ( in_array( $slug, $active_plugins ) ) {
			return true;
		}

		global $wp_filesystem;
		bulkmail_require_filesystem();

		$old_location = BULKEMAIL_DIR . '/myMail.php';
		$new_location = BULKEMAIL_DIR . '/bulkmail.php';

		if ( ! $wp_filesystem->move( $old_location, $new_location, true ) ) {
			@rename( $old_location, $new_location );
		}

		$old_location = BULKEMAIL_DIR;
		$new_location = dirname( BULKEMAIL_DIR ) . '/bulkmail';

		if ( ! $wp_filesystem->move( $old_location, $new_location, true ) ) {
			@rename( $old_location, $new_location );
		}

		deactivate_plugins( array( BULKEMAIL_SLUG ), false, true );
		activate_plugin( $slug, '', false, true );

		$active_plugins = get_option( 'active_plugins', array() );

		if ( in_array( $slug, $active_plugins ) ) {
			return true;
		}

		return false;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_form_prepare() {

		global $wpdb;

		if ( $formstructure = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}mymail_forms WHERE ID = 0" ) ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}mymail_forms WHERE ID = 0" );
			update_option( '_bulkmail_formstructure', $formstructure );
		}

		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_options() {

		global $wpdb;

		echo 'Converting Options' . "\n";

		$options = $wpdb->get_results( "SELECT option_name, option_value, autoload FROM {$wpdb->options} WHERE option_name LIKE '%mymail%'" );

		foreach ( $options as $option ) {
			$option->option_name = str_replace( 'mymail', 'bulkmail', $option->option_name );
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->options} (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s ) ON DUPLICATE KEY UPDATE option_value = values(option_value)", $option->option_name, $option->option_value, $option->autoload ) );
		}

		$tables = bulkmail()->get_tables( true );

		set_transient( '_bulkmail_mymail', true, MONTH_IN_SECONDS );
		update_option( 'bulkmail', time() );
		update_option( 'bulkmail_setup', time() );
		update_option( 'bulkmail_templates', '' );
		$wpdb->query( "UPDATE {$wpdb->options} SET autoload = 'no' WHERE option_name IN ('bulkmail_templates', 'bulkmail_cron_lasthit')" );

		bulkmail_update_option( 'webversion_bar', true );

		if ( wp_next_scheduled( 'mymail_cron_worker' ) ) {
			wp_clear_scheduled_hook( 'mymail_cron_worker' );
		}
		if ( wp_next_scheduled( 'mymail_cron' ) ) {
			wp_clear_scheduled_hook( 'mymail_cron' );
		}

		$post_notice = '';

		if ( bulkmail_option( 'cron_service' ) == 'cron' ) {
			$post_notice .= '<p><strong>The URL to the cron has changed!</strong></p><a class="button button-primary" href="edit.php?post_type=newsletter&page=bulkmail_settings#cron">Get the new URL</a>';
		}
		if ( defined( 'MYMAIL_MU_CRON' ) && MYMAIL_MU_CRON ) {
			$post_notice .= '<p><strong>The MyMail - MU Cron in the mu folder is no longer in use!</strong></p><a class="button button-primary" href="plugins.php?plugin_status=mustuse">You can remove it!</a>';
		}

		if ( ! empty( $post_notice ) ) {
			bulkmail_notice( $post_notice, 'error', false, 'update_post_notice' );
		}

		sleep( 1 );
		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_backuptables() {

		global $wpdb;

		$tables = bulkmail()->get_tables();

		foreach ( $tables as $table ) {

			if ( ! $this->table_exists( "{$wpdb->prefix}mymail_bak_{$table}" ) ) {

				if ( $count = $wpdb->query( "CREATE TABLE {$wpdb->prefix}mymail_bak_{$table} LIKE {$wpdb->prefix}mymail_{$table}" ) ) {
					echo 'Backup table ' . esc_html($table) . '' . "\n";
					if ( $count = $wpdb->query( "INSERT {$wpdb->prefix}mymail_bak_{$table} SELECT * FROM {$wpdb->prefix}mymail_{$table}" ) ) {
						echo 'Backup data ' . $table . '' . "\n";
					}
					return false;
				}
			}
		}

		sleep( 1 );
		return true;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_copytables() {

		global $wpdb;

		$wpdb->suppress_errors();

		$tables = bulkmail()->get_tables();

		foreach ( $tables as $table ) {

			if ( $this->table_exists( "{$wpdb->prefix}mymail_{$table}" ) ) {

				if ( ! $this->table_exists( "{$wpdb->prefix}bulkmail_{$table}" ) ) {
					if ( $count = $wpdb->query( "CREATE TABLE {$wpdb->prefix}bulkmail_{$table} LIKE {$wpdb->prefix}mymail_{$table}" ) ) {
						echo 'Copy table structure ' . $table . '' . "\n";
						return false;
					}
				}
				if ( $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}bulkmail_{$table}" ) ) {
					echo 'Clean ' . $table . '' . "\n";
				}
				if ( $wpdb->query( "INSERT {$wpdb->prefix}bulkmail_{$table} SELECT * FROM {$wpdb->prefix}mymail_{$table}" ) ) {
					echo 'Copy data ' . $table . '' . "\n";
				}
			}
		}

		sleep( 1 );
		return true;

	}

	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_updatedpostmeta() {

		global $wpdb;

		if ( $formstructure = get_option( '_bulkmail_formstructure' ) ) {
			unset( $formstructure->ID );
			$wpdb->insert( "{$wpdb->prefix}bulkmail_forms", (array) $formstructure );
			delete_option( '_bulkmail_formstructure' );
			$form_id = $wpdb->insert_id;

			if ( is_numeric( $form_id ) ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}bulkmail_form_fields SET form_id = %d WHERE form_id = 0", $form_id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}bulkmail_forms_lists SET form_id = %d WHERE form_id = 0", $form_id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET `post_content` = replace(post_content, %s, %s)", '[newsletter_signup_form id=0]', '[newsletter_signup_form id=' . $form_id . ']' ) );

				$old_profile_form = bulkmail_option( 'profile_form' );
				if ( 0 == $old_profile_form ) {
					bulkmail_update_option( 'profile_form', $form_id );
				}
			}
		}

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET `meta_key` = replace(meta_key, %s, %s)", 'mymail', 'bulkmail' ) );
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET `post_content` = replace(post_content, %s, %s)", 'mymail_image_placeholder', 'bulkmail_image_placeholder' ) );

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}bulkmail_forms SET `style` = replace(style, %s, %s)", 'mymail', 'bulkmail' ) );
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}bulkmail_forms SET `custom_style` = replace(custom_style, %s, %s)", 'mymail', 'bulkmail' ) );

		$autoresponder_data = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_bulkmail_autoresponder' AND meta_value LIKE '%mymail%'" );

		foreach ( $autoresponder_data as $data ) {

			$meta_value = maybe_unserialize( $data->meta_value );

			if ( isset( $meta_value['action'] ) ) {
				$meta_value['action'] = str_replace( 'mymail', 'bulkmail', $meta_value['action'] );
			}
			update_post_meta( $data->post_id, $data->meta_key, $meta_value );
		}

		return true;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_movefiles() {

		global $wpdb, $wp_filesystem;

		bulkmail_require_filesystem();

		$new_location = BULKEMAIL_UPLOAD_DIR;
		$old_location = dirname( BULKEMAIL_UPLOAD_DIR ) . '/myMail';

		if ( is_dir( $new_location ) ) {
			if ( ! $wp_filesystem->move( $new_location, $new_location . '_bak', true ) ) {
				@rename( $new_location, $new_location . '_bak' );
			}
		}

		if ( is_dir( $old_location ) && ! is_dir( $new_location ) ) {

			if ( ! $wp_filesystem->move( $old_location, $new_location, true ) ) {
				@rename( $old_location, $new_location );
			}
		}

		$new_location_url = preg_replace( '/https?:/', '', BULKEMAIL_UPLOAD_URI );
		$old_location_url = preg_replace( '/https?:/', '', dirname( BULKEMAIL_UPLOAD_URI ) . '/myMail' );

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET `post_content` = replace(post_content, %s, %s)", $old_location_url, $new_location_url ) );

		$new_location = trailingslashit( BULKEMAIL_UPLOAD_DIR . '/backgrounds' );
		$old_location = trailingslashit( BULKEMAIL_DIR . 'assets/img/bg' );

		$new_location_url = preg_replace( '/https?:/', '', BULKEMAIL_UPLOAD_URI . '/backgrounds/' );
		$old_location_url = preg_replace( '/https?:/', '', dirname( BULKEMAIL_URI ) . '/myMail/assets/img/bg/' );

		if ( ! is_dir( $new_location ) ) {
			wp_mkdir_p( $new_location );
		}

		$to_copy = list_files( $old_location, 1 );
		foreach ( $to_copy as $file ) {
			if ( ! $wp_filesystem->copy( $file, $new_location . basename( $file ), false ) ) {
				@copy( $file, $new_location . basename( $file ) );
			}
		}

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET `post_content` = replace(post_content, %s, %s)", $old_location_url, $new_location_url ) );

		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_removeoldtables() {

		global $wpdb;

		$tables = bulkmail()->get_tables();

		foreach ( $tables as $table ) {

			if ( $this->table_exists( "{$wpdb->prefix}mymail_{$table}" ) ) {

				if ( $count = $wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', "{$wpdb->prefix}mymail_{$table}" ) ) ) {
					echo 'old ' . $table . ' table removed' . "\n";
					return false;
				}
			}
		}

		sleep( 1 );
		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_removebackup() {

		global $wpdb;

		$tables = bulkmail()->get_tables();

		foreach ( $tables as $table ) {

			if ( $this->table_exists( "{$wpdb->prefix}mymail_bak_{$table}" ) ) {

				if ( $count = $wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', "{$wpdb->prefix}mymail_bak_{$table}" ) ) ) {
					echo 'Backup table ' . $table . ' removed' . "\n";
					return false;
				}
			}
		}

		sleep( 1 );
		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_removemymail() {

		global $wpdb;

		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_mymail_%'" );
		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_timeout_mymail_%'" );
		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient__mymail_%'" );
		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_timeout__mymail_%'" );
		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_timeout__mymail_%'" );
		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE 'mymail_%'" );
		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` = 'mymail'" );

		sleep( 1 );
		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_pre_bulkmail_checkhooks() {

		global $wp_filter;
		$hooks = array_values( preg_grep( '/^mymail/', array_keys( $wp_filter ) ) );

		if ( ! empty( $hooks ) ) {
			$msg = '<p>Following deprecated MyMail hooks were found and should get replaced:</p><ul>';
			foreach ( $hooks as $hook ) {
				echo 'Hook ' . $hook . ' found!' . "\n";
				$msg .= '<li><code>' . $hook . '</code> => <code>' . str_replace( 'mymail', 'bulkmail', $hook ) . '</code></li>';
			}
			$msg .= '</ul>';

			bulkmail_notice( $msg, 'error', false, 'old_hooks' );

		}

		sleep( 1 );
		return true;

	}




	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_maybe_install() {
		bulkmail()->install();
		return true;
	}

	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_db_structure() {
		bulkmail()->dbstructure( true, true, true, true );
		return true;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_db_check() {

		global $wpdb;

		ob_start();

		bulkmail()->dbstructure( true, true, true, false );

		$output = ob_get_contents();

		ob_end_clean();

		if ( false === bulkmail( 'subscribers' )->wp_id() ) {
			$status = $wpdb->get_row( $wpdb->prepare( 'SHOW TABLE STATUS LIKE %s', $wpdb->users ) );
			if ( isset( $status->Collation ) ) {
				$tables = bulkmail()->get_tables( true );

				foreach ( $tables as $table ) {
					$sql = $wpdb->prepare( 'ALTER TABLE %s CONVERT TO CHARACTER SET utf8mb4 COLLATE %s', $table, $status->Collation );
					if ( false !== $wpdb->query( $sql ) ) {
						echo "'$table' converted to {$status->Collation}.\n";
					}
				}
			}
		}

		if ( ! $output ) {
			echo 'No DB structure problem found' . "\n";
		}

		if ( function_exists( 'maybe_convert_table_to_utf8mb4' ) ) {
			$tables = bulkmail()->get_tables( true );

			foreach ( $tables as $table ) {
				maybe_convert_table_to_utf8mb4( $table );
			}
		}

		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_db_structure() {

		global $wpdb;

		$wpdb->query( "ALTER TABLE {$wpdb->prefix}bulkmail_queue CHANGE subscriber_id  subscriber_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE campaign_id campaign_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE requeued requeued TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE added added INT( 11 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE timestamp timestamp INT( 11 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE sent sent INT( 11 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE priority priority TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE count count TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE error error TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE ignore_status ignore_status TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE options options VARCHAR( 191 ) NOT NULL DEFAULT ''" );

		$wpdb->query( "ALTER TABLE {$wpdb->prefix}bulkmail_actions CHANGE subscriber_id  subscriber_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE campaign_id campaign_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE timestamp timestamp INT( 11 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE count count TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE type type TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0', CHANGE link_id link_id BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0'" );

		return true;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_lists() {

		global $wpdb;

		$now = time();

		$limit = ceil( 25 * $this->performance );

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->terms} AS a LEFT JOIN {$wpdb->term_taxonomy} as b ON b.term_id = a.term_id LEFT JOIN {$wpdb->prefix}bulkmail_lists AS c ON c.ID = a.term_id WHERE b.taxonomy = 'newsletter_lists' AND c.ID IS NULL" );

		echo $count . ' lists left' . "\n";

		$sql = "SELECT a.term_id AS ID, a.name, a.slug, b.description FROM {$wpdb->terms} AS a LEFT JOIN {$wpdb->term_taxonomy} as b ON b.term_id = a.term_id LEFT JOIN {$wpdb->prefix}bulkmail_lists AS c ON c.ID = a.term_id WHERE b.taxonomy = 'newsletter_lists' AND c.ID IS NULL LIMIT $limit";

		$lists = $wpdb->get_results( $sql );
		if ( ! count( $lists ) ) {
			return true;
		}

		foreach ( $lists as $list ) {
			$sql = "INSERT INTO {$wpdb->prefix}bulkmail_lists (ID, parent_id, name, slug, description, added, updated) VALUES (%d, '0', %s, %s, %s, %d, %d)";

			if ( false !== $wpdb->query( $wpdb->prepare( $sql, $list->ID, $list->name, $list->slug, $list->description, $now, $now ) ) ) {
				echo 'added list ' . $list->name . "\n";
			}
		}

		return false;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_forms() {

		global $wpdb;

		$now = time();

		$forms = bulkmail_option( 'forms' );

		if ( empty( $forms ) ) {
			return true;
		}

		$ids = wp_list_pluck( $forms, 'id' );

		$form_css = bulkmail_option( 'form_css' );

		foreach ( $forms as $id => $form ) {

			if ( $wpdb->query( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}bulkmail_forms WHERE ID = %d", $id ) ) ) {
				continue;
			}

			$sql = "INSERT INTO {$wpdb->prefix}bulkmail_forms
                (ID, name, submit, asterisk, userschoice, precheck, dropdown, prefill, inline, addlists, style, custom_style, doubleoptin, subject, headline, link, content, resend, resend_count, resend_time, template, vcard, vcard_content, confirmredirect, redirect, added, updated) VALUES
                (%d, %s, %s, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %s, %s, %s, %d, %d)

                ON DUPLICATE KEY UPDATE updated=%d";

			$sql = $wpdb->prepare( $sql, $id, $form['name'], $form['submitbutton'], isset( $form['asterisk'] ), isset( $form['userschoice'] ), isset( $form['precheck'] ), isset( $form['dropdown'] ), isset( $form['prefill'] ), isset( $form['inline'] ), isset( $form['addlists'] ), '', str_replace( '.bulkmail-form ', '.bulkmail-form-' . $id . ' ', $form_css ), isset( $form['double_opt_in'] ), $form['text']['subscription_subject'], $form['text']['subscription_headline'], $form['text']['subscription_link'], $form['text']['subscription_text'], isset( $form['subscription_resend'] ), $form['subscription_resend_count'], $form['subscription_resend_time'], $form['template'], isset( $form['vcard'] ), $form['vcard_content'], $form['confirmredirect'], $form['redirect'], $now, $now, $now );

			if ( $wpdb->query( $sql ) ) {
				if ( $wpdb->insert_id != $id ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}bulkmail_forms SET `ID` = %d WHERE {$wpdb->prefix}bulkmail_forms.ID = %d;", $id, $wpdb->insert_id ) );
				}

				foreach ( $form['order'] as $position => $field_id ) {

					$sql = "INSERT INTO {$wpdb->prefix}bulkmail_form_fields (form_id, field_id, name, required, position) VALUES (%d, %s, %s, %d, %d)";
					$wpdb->query( $wpdb->prepare( $sql, $id, $field_id, $form['labels'][ $field_id ], in_array( $field_id, $form['required'] ), $position ) );
				}

				echo 'updated form ' . $form['name'] . " \n";
				if ( bulkmail( 'forms' )->assign_lists( $id, $form['lists'], false ) ) {
					echo 'assigned lists to form ' . $form['name'] . " \n";
				}
			}
		}

		$wpdb->query( $wpdb->prepare( "ALTER TABLE {$wpdb->prefix}bulkmail_forms AUTO_INCREMENT = %d", count( $forms ) ) );

		$wpdb->query( "UPDATE {$wpdb->posts} SET `post_content` = replace(post_content, '[newsletter_signup_form]', '[newsletter_signup_form id=0]')" );

		return true;

	}



	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_campaign() {

		global $wpdb;

		$limit = ceil( 25 * $this->performance );

		$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} AS m LEFT JOIN {$wpdb->posts} AS p ON p.ID = m.post_id LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id LEFT JOIN {$wpdb->postmeta} AS b ON b.post_id = p.ID AND b.meta_key = '_bulkmail_timestamp' WHERE m.meta_key = 'bulkmail-data' AND c.meta_key = 'bulkmail-campaign' AND p.post_type = 'newsletter' AND b.meta_key IS NULL" );

		echo $count . ' campaigns left' . "\n";

		$sql = "SELECT p.ID, p.post_title, p.post_status, m.meta_value as meta, c.meta_value AS campaign FROM {$wpdb->postmeta} AS m LEFT JOIN {$wpdb->posts} AS p ON p.ID = m.post_id LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id LEFT JOIN {$wpdb->postmeta} AS b ON b.post_id = p.ID AND b.meta_key = '_bulkmail_timestamp' WHERE m.meta_key = 'bulkmail-data' AND c.meta_key = 'bulkmail-campaign' AND p.post_type = 'newsletter' AND b.meta_key IS NULL LIMIT $limit";

		$campaigns = $wpdb->get_results( $sql );

		// no campaigns left => update ok
		if ( ! count( $campaigns ) ) {
			return true;
		}

		foreach ( $campaigns as $data ) {

			$meta = bulkmail( 'helper' )->unserialize( $data->meta );

			$campaign = wp_parse_args(
				array(
					'original_campaign' => '',
					'finished'          => '',
					'timestamp'         => '',
					'totalerrors'       => '',
				),
				bulkmail( 'helper' )->unserialize( $data->campaign )
			);

			$lists = $wpdb->get_results( $wpdb->prepare( "SELECT b.* FROM {$wpdb->term_relationships} AS a LEFT JOIN {$wpdb->term_taxonomy} AS b ON b.term_taxonomy_id = a.term_taxonomy_id WHERE object_id = %d", $data->ID ) );

			$listids = wp_list_pluck( $lists, 'term_id' );

			if ( $data->post_status == 'autoresponder' ) {
				$autoresponder = $meta['autoresponder'];
				$active        = isset( $meta['active_autoresponder'] ) && $meta['active_autoresponder'];
				$timestamp     = isset( $autoresponder['timestamp'] ) ? $autoresponder['timestamp'] : strtotime( $autoresponder['date'] . ' ' . $autoresponder['time'] );

			} else {
				$autoresponder = '';
				$active        = isset( $meta['active'] ) && $meta['active'] && ! $campaign['finished'];
				$timestamp     = isset( $meta['timestamp'] ) ? $meta['timestamp'] : time();
			}

			$timestamp = $timestamp - $timeoffset;

			if ( $data->post_status == 'finished' ) {
				$campaign['finished'] = $campaign['finished'] ? $campaign['finished'] - $timeoffset : $timestamp;
			}

			$values = array(
				'parent_id'     => $campaign['original_campaign'],
				'timestamp'     => $timestamp,
				'finished'      => $campaign['finished'],
				'active'        => $active, // all campaigns inactive
				'from_name'     => $meta['from_name'],
				'from_email'    => $meta['from'],
				'reply_to'      => $meta['reply_to'],
				'subject'       => $meta['subject'],
				'preheader'     => $meta['preheader'],
				'template'      => $meta['template'],
				'file'          => $meta['file'],
				'lists'         => array_unique( $listids ),
				'ignore_lists'  => 0,
				'autoresponder' => $autoresponder,
				'head'          => trim( $meta['head'] ),
				'background'    => $meta['background'],
				'colors'        => ( $meta['newsletter_color'] ),
			);

			if ( $data->post_status == 'active' ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_status = 'queued' WHERE ID = %d AND post_type = 'newsletter'", $data->ID ) );
			}

			bulkmail( 'campaigns' )->update_meta( $data->ID, $values );

			echo 'updated campaign ' . $data->post_title . "\n";

		}

		return false;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_subscriber() {

		global $wpdb;

		$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

		$limit = ceil( 500 * $this->performance );

		$now = time();

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->prefix}bulkmail_subscribers AS s ON s.ID = p.ID LEFT JOIN {$wpdb->prefix}bulkmail_subscribers AS s2 ON s2.email = p.post_title LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id AND c.meta_key = 'bulkmail-campaigns' LEFT JOIN {$wpdb->postmeta} AS u ON p.ID = u.post_id AND u.meta_key = 'bulkmail-userdata' WHERE p.post_type = 'subscriber' AND post_status IN ('subscribed', 'unsubscribed', 'hardbounced', 'error') AND s.ID IS NULL AND (s2.email != p.post_title OR s2.email IS NULL)" );

		echo $count . ' subscribers left' . "\n\n";

		$sql = "SELECT p.ID, p.post_title AS email, p.post_status AS status, p.post_name AS hash, c.meta_value as campaign, u.meta_value as userdata FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->prefix}bulkmail_subscribers AS s ON s.ID = p.ID LEFT JOIN {$wpdb->prefix}bulkmail_subscribers AS s2 ON s2.email = p.post_title LEFT JOIN {$wpdb->postmeta} AS c ON p.ID = c.post_id AND c.meta_key = 'bulkmail-campaigns' LEFT JOIN {$wpdb->postmeta} AS u ON p.ID = u.post_id AND u.meta_key = 'bulkmail-userdata' WHERE p.post_type = 'subscriber' AND post_status IN ('subscribed', 'unsubscribed', 'hardbounced', 'error') AND s.ID IS NULL AND (s2.email != p.post_title OR s2.email IS NULL) GROUP BY p.ID ORDER BY p.post_title ASC LIMIT $limit";

		$users = $wpdb->get_results( $sql );

		$count = count( $users );

		// no users left => update ok
		if ( ! $count ) {
			return true;
		}

		foreach ( $users as $data ) {
			$userdata = bulkmail( 'helper' )->unserialize( $data->userdata );

			$meta = array(
				'confirmtime' => 0,
				'signuptime'  => 0,
				'signupip'    => '',
				'confirmip'   => '',
			);

			if ( is_array( $userdata ) && isset( $userdata['_meta'] ) ) {
				$meta = wp_parse_args( $userdata['_meta'], $meta );
				unset( $userdata['_meta'] );
			}

			$status = bulkmail( 'subscribers' )->get_status_by_name( $data->status );

			$values = array(
				'ID'         => $data->ID,
				'email'      => addcslashes( $data->email, "'" ),
				'hash'       => $data->hash,
				'status'     => $status,
				'added'      => isset( $meta['imported'] ) ? $meta['imported'] : ( isset( $meta['confirmtime'] ) ? $meta['confirmtime'] : $now ),
				'updated'    => $now,
				'signup'     => $meta['signuptime'],
				'confirm'    => $meta['confirmtime'],
				'ip_signup'  => $meta['signupip'],
				'ip_confirm' => $meta['confirmip'],
			);

			$campaign_data = bulkmail( 'helper' )->unserialize( $data->campaign );

			$sql = "INSERT INTO {$wpdb->prefix}bulkmail_subscribers (" . implode( ',', array_keys( $values ) ) . ") VALUES ('" . implode( "','", array_values( $values ) ) . "') ON DUPLICATE KEY UPDATE updated = values(updated);";

			if ( false !== $wpdb->query( $sql ) ) {

				echo 'added ' . $data->email . "\n";
				$this->update_customfields( $data->ID );
				echo "\n";

			}
		}

		// not finished yet (but successfull)
		return false;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_list_subscriber() {

		global $wpdb;

		$limit = ceil( 500 * $this->performance );

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->term_relationships} AS a LEFT JOIN {$wpdb->term_taxonomy} AS b ON a.term_taxonomy_id = b.term_taxonomy_id LEFT JOIN {$wpdb->prefix}bulkmail_lists_subscribers AS c ON c.subscriber_id = a.object_id AND c.list_id = b.term_id WHERE b.taxonomy = 'newsletter_lists' AND c.subscriber_id IS NULL" );

		echo $count . ' list - subscriber connections left' . "\n\n";

		$sql = "SELECT a.object_id AS subscriber_id, b.term_id AS list_id FROM {$wpdb->term_relationships} AS a LEFT JOIN {$wpdb->term_taxonomy} AS b ON a.term_taxonomy_id = b.term_taxonomy_id LEFT JOIN {$wpdb->prefix}bulkmail_lists_subscribers AS c ON c.subscriber_id = a.object_id AND c.list_id = b.term_id WHERE b.taxonomy = 'newsletter_lists' AND c.subscriber_id IS NULL LIMIT $limit";

		$connections = $wpdb->get_results( $sql );
		if ( ! count( $connections ) ) {
			return true;
		}

		$inserts = array();

		$now = time();

		$sql = "INSERT INTO {$wpdb->prefix}bulkmail_lists_subscribers (list_id, subscriber_id, added) VALUES";

		foreach ( $connections as $connection ) {
			$inserts[] = $wpdb->prepare( '(%d, %d, %d)', $connection->list_id, $connection->subscriber_id, $now );
		}

		if ( empty( $inserts ) ) {
			return true;
		}

		$sql .= implode( ',', $inserts );

		$wpdb->query( $sql );

		return false;
	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	private function update_customfields( $id ) {
		global $wpdb;

		$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

		$now = time();

		$id = (int) $id;

		$sql = "SELECT a.meta_value AS meta FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}bulkmail_subscriber_fields AS b ON b.subscriber_id = a.post_id WHERE a.meta_key = 'bulkmail-userdata' AND b.subscriber_id IS NULL AND a.post_id = %d LIMIT 1";

		if ( $usermeta = $wpdb->get_var( $wpdb->prepare( $sql, $id ) ) ) {

			$userdata = bulkmail( 'helper' )->unserialize( $usermeta );
			if ( ! is_array( $userdata ) ) {
				'ERROR: Corrupt data: "' . $userdata . '"';
				return;
			}

			$meta = array();
			if ( isset( $userdata['_meta'] ) ) {
				$meta = $userdata['_meta'];
				unset( $userdata['_meta'] );
			}

			foreach ( $userdata as $field => $value ) {
				if ( $value == '' ) {
					continue;
				}

				$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}bulkmail_subscriber_fields (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $id, trim( $field ), trim( $value ) );

				if ( false !== $wpdb->query( $sql ) ) {
					echo "added field '$field' => '$value' \n";
				}
			}

			foreach ( $meta as $field => $value ) {
				if ( $value == '' || ! in_array( $field, array( 'ip', 'lang' ) ) ) {
					continue;
				}

				$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}bulkmail_subscriber_meta (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $id, trim( $field ), trim( $value ) );

				if ( false !== $wpdb->query( $sql ) ) {
					echo "added meta field '$field' => '$value' \n";
				}
			}
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_customfields() {

		global $wpdb;

		$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

		$limit = ceil( 2500 * $this->performance );

		$now = time();

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}bulkmail_subscriber_fields AS b ON b.subscriber_id = a.post_id WHERE a.meta_key = 'bulkmail-userdata' AND b.subscriber_id IS NULL" );

		echo $count . ' customfields left' . "\n\n";

		$sql = "SELECT a.post_id AS ID, a.meta_value AS meta FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}bulkmail_subscriber_fields AS b ON b.subscriber_id = a.post_id WHERE a.meta_key = 'bulkmail-userdata' AND b.subscriber_id IS NULL LIMIT $limit";

		$usermeta = $wpdb->get_results( $sql );

		// no usermeta left => update ok
		if ( ! count( $usermeta ) ) {
			return true;
		}

		foreach ( $usermeta as $data ) {
			$userdata = bulkmail( 'helper' )->unserialize( $data->meta );
			$meta     = array();
			if ( isset( $userdata['_meta'] ) ) {
				$meta = $userdata['_meta'];
				unset( $userdata['_meta'] );
			}

			if ( empty( $userdata ) ) {
				$sql = "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = 'bulkmail-userdata'";
				$wpdb->query( $wpdb->prepare( $sql, $data->ID ) );
			}

			foreach ( $userdata as $field => $value ) {
				if ( $value == '' ) {
					continue;
				}

				$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}bulkmail_subscriber_fields (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s)", $data->ID, trim( $field ), trim( $value ) );

				$wpdb->query( $sql );

			}
			foreach ( $meta as $field => $value ) {
				if ( $value == '' || ! in_array( $field, array( 'ip', 'lang' ) ) ) {
					continue;
				}

				$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}bulkmail_subscriber_meta (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $data->ID, trim( $field ), trim( $value ) );

				$wpdb->query( $sql );

			}
			echo 'added fields for ' . $data->ID . "\n";

		}

		// not finished yet (but successful)
		return false;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_actions() {

		global $wpdb;

		$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

		$limit = ceil( 500 * $this->performance );

		$offset = get_transient( 'bulkmail_do_update_actions' );

		if ( ! $offset ) {
			$offset = 0;
		}

		$now = time();

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}bulkmail_actions AS b ON a.post_id = b.subscriber_id AND a.meta_key = 'bulkmail-campaigns' WHERE b.subscriber_id IS NULL AND a.meta_key = 'bulkmail-campaigns' AND a.meta_value != 'a:0:{}' ORDER BY a.post_id ASC" );

		echo $count . ' actions left' . "\n\n";

		$sql = "SELECT a.post_id AS ID, a.meta_value AS meta FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->prefix}bulkmail_actions AS b ON a.post_id = b.subscriber_id AND a.meta_key = 'bulkmail-campaigns' WHERE b.subscriber_id IS NULL AND a.meta_key = 'bulkmail-campaigns' AND a.meta_value != 'a:0:{}' GROUP BY a.post_id ORDER BY a.post_id ASC LIMIT $limit";

		$campaignmeta = $wpdb->get_results( $sql );

		// nothing left
		if ( ! count( $campaignmeta ) ) {
			delete_transient( 'bulkmail_do_update_actions' );
			return true;
		}

		$bounce_attempts = bulkmail_option( 'bounce_attempts' );

		$old_unsubscribelink = add_query_arg( array( 'unsubscribe' => '' ), get_permalink( bulkmail_option( 'homepage' ) ) );
		$new_unsubscribelink = bulkmail()->get_unsubscribe_link();

		foreach ( $campaignmeta as $data ) {

			$userdata = bulkmail( 'helper' )->unserialize( $data->meta );

			foreach ( $userdata as $campaign_id => $infos ) {

				$default = array(
					'subscriber_id' => $data->ID,
					'campaign_id'   => $campaign_id,
					'count'         => 1,
				);
				foreach ( $infos as $info_key => $info_value ) {

					echo 'added action ' . $info_key . ' => ' . $info_value . "\n";
					switch ( $info_key ) {
						case 'sent':
							if ( gettype( $info_value ) == 'boolean' && ! $info_value ) {
								$info_value = $now;
							}

							if ( $info_value ) {
								$values = wp_parse_args(
									array(
										'timestamp' => $info_value,
										'type'      => 1,
									),
									$default
								);

								$wpdb->query( "INSERT INTO {$wpdb->prefix}bulkmail_actions (" . implode( ',', array_keys( $values ) ) . ") VALUES ('" . implode( "','", array_values( $values ) ) . "') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)" );
							} else {

								$values = wp_parse_args(
									array(
										'timestamp' => $now,
										'sent'      => $info_value,
										'priority'  => 10,
									),
									$default
								);

								$wpdb->query( "INSERT INTO {$wpdb->prefix}bulkmail_queue (" . implode( ',', array_keys( $values ) ) . ") VALUES ('" . implode( "','", array_values( $values ) ) . "') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)" );
							}

							break;
						case 'open':
							$values = wp_parse_args(
								array(
									'timestamp' => $info_value,
									'type'      => 2,
								),
								$default
							);

								$wpdb->query( "INSERT INTO {$wpdb->prefix}bulkmail_actions (" . implode( ',', array_keys( $values ) ) . ") VALUES ('" . implode( "','", array_values( $values ) ) . "') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)" );
							break;

						case 'clicks':
							foreach ( $info_value as $link => $count ) {

								// new unsubscribe links
								if ( $link == $old_unsubscribelink ) {
									$link = $new_unsubscribelink;
								}

								$values = wp_parse_args(
									array(
										'timestamp' => $infos['firstclick'],
										'type'      => 3,
										'link_id'   => bulkmail( 'actions' )->get_link_id( $link, 0 ),
										'count'     => $count,
									),
									$default
								);

								$wpdb->query( "INSERT INTO {$wpdb->prefix}bulkmail_actions (" . implode( ',', array_keys( $values ) ) . ") VALUES ('" . implode( "','", array_values( $values ) ) . "') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)" );

							}
							break;

						case 'unsubscribe':
							$values = wp_parse_args(
								array(
									'timestamp' => $info_value,
									'type'      => 4,
								),
								$default
							);

								$wpdb->query( "INSERT INTO {$wpdb->prefix}bulkmail_actions (" . implode( ',', array_keys( $values ) ) . ") VALUES ('" . implode( "','", array_values( $values ) ) . "') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)" );

							break;

						case 'bounces':
							$values = wp_parse_args(
								array(
									'timestamp' => $now,
									'type'      => $info_value >= $bounce_attempts ? 6 : 5,
									'count'     => $info_value >= $bounce_attempts ? $bounce_attempts : 1,
								),
								$default
							);

								$wpdb->query( "INSERT INTO {$wpdb->prefix}bulkmail_actions (" . implode( ',', array_keys( $values ) ) . ") VALUES ('" . implode( "','", array_values( $values ) ) . "') ON DUPLICATE KEY UPDATE timestamp = values(timestamp)" );

							break;

					}
				}
			}
		}

		set_transient( 'bulkmail_do_update_actions', $offset + $limit );

		// not finished yet (but successful)
		return false;

		return new WP_Error( 'update_error', 'An error occured during batch update' );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_pending() {

		global $wpdb;

		$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

		$now = time();

		$limit = ceil( 25 * $this->performance );

		$pending = get_option( 'bulkmail_confirms', array() );

		$i = 0;

		foreach ( $pending as $hash => $user ) {

			$userdata = $user['userdata'];
			$meta     = array();
			if ( isset( $userdata['_meta'] ) ) {
				$meta = $userdata['_meta'];
				unset( $userdata['_meta'] );
			}

			$values = array(
				'email'     => $userdata['email'],
				'hash'      => $hash,
				'status'    => 0,
				'added'     => $user['timestamp'],
				'updated'   => $now,
				'signup'    => $user['timestamp'],
				'ip_signup' => $meta['signupip'],
			);

			$sql = "INSERT INTO {$wpdb->prefix}bulkmail_subscribers (" . implode( ',', array_keys( $values ) ) . ") VALUES ('" . implode( "','", array_values( $values ) ) . "')";

			if ( false !== $wpdb->query( $sql ) ) {

				$subscriber_id = $wpdb->insert_id;

				unset( $userdata['email'] );

				foreach ( $userdata as $field => $value ) {
					if ( $value == '' ) {
						continue;
					}

					$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}bulkmail_subscriber_fields (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $subscriber_id, trim( $field ), trim( $value ) );

					if ( false !== $wpdb->query( $sql ) ) {
						echo "added field '$field' => '$value' \n";
					}
				}

				foreach ( $meta as $field => $value ) {
					if ( $value == '' || ! in_array( $field, array( 'ip', 'lang' ) ) ) {
						continue;
					}

					$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}bulkmail_subscriber_meta (subscriber_id, meta_key, meta_value) VALUES (%d, %s, %s) ON DUPLICATE KEY UPDATE subscriber_id = values(subscriber_id)", $subscriber_id, trim( $field ), trim( $value ) );

					if ( false !== $wpdb->query( $sql ) ) {
						echo "added meta field '$field' => '$value' \n";
					}
				}

				echo 'added pending user ' . $values['email'] . "\n";

			}
		}

		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_autoresponder() {

		global $wpdb;

		$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

		$now = time();

		$limit = ceil( 25 * $this->performance );

		$cron = get_option( 'cron', array() );

		foreach ( $cron as $timestamp => $jobs ) {
			if ( ! is_array( $jobs ) ) {
				continue;
			}

			foreach ( $jobs as $id => $data ) {
				if ( $id != 'bulkmail_autoresponder' ) {
					continue;
				}

				foreach ( $data as $crondata ) {
					$args = $crondata['args'];

					$values = array(
						'subscriber_id' => $args['args'][0],
						'campaign_id'   => $args['campaign_id'],
						'added'         => $now,
						'timestamp'     => $timestamp,
						'sent'          => 0,
						'priority'      => 15,
						'count'         => $args['try'],
						'ignore_status' => $args['action'] == 'bulkmail_subscriber_unsubscribed',
					);

					$wpdb->query( "INSERT INTO {$wpdb->prefix}bulkmail_queue (" . implode( ',', array_keys( $values ) ) . ") VALUES ('" . implode( "','", array_values( $values ) ) . "')" );

				}
			}
		}

		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_update_settings() {

		global $wpdb;

		$forms = bulkmail_option( 'forms' );

		if ( empty( $forms ) ) {
			return true;
		}

		foreach ( $forms as $id => $form ) {

			// Stop if all list items are numbers (Bulkmail 2 already)
			if ( ! isset( $form['lists'] ) || ! is_array( $form['lists'] ) ) {
				continue;
			}

			if ( count( array_filter( $form['lists'], 'is_numeric' ) ) == count( $form['lists'] ) ) {
				continue;
			}

			$sql = "SELECT a.ID FROM {$wpdb->prefix}bulkmail_lists AS a WHERE a.slug IN ('" . implode( "','", $form['lists'] ) . "')";

			$lists = $wpdb->get_col( $sql );

			$forms[ $id ]['lists'] = $lists;

			echo 'updated form ' . $form['name'] . "\n";

		}

		bulkmail_update_option( 'forms', $forms );

		$texts = bulkmail_option( 'text' );

		$texts['profile_update'] = ! empty( $texts['profile_update'] ) ? $texts['profile_update'] : esc_html__( 'Profile Updated!', 'bulkmail' );
		$texts['profilebutton']  = ! empty( $texts['profilebutton'] ) ? $texts['profilebutton'] : esc_html__( 'Update Profile', 'bulkmail' );
		$texts['forward']        = ! empty( $texts['forward'] ) ? $texts['forward'] : esc_html__( 'forward to a friend', 'bulkmail' );
		$texts['profile']        = ! empty( $texts['profile'] ) ? $texts['profile'] : esc_html__( 'update profile', 'bulkmail' );

		echo "updated texts\n";

		bulkmail_update_option( 'text', $texts );

		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function do_cleanup() {

		global $wpdb;

		delete_transient( 'bulkmail_cron_lock' );

		update_option( 'bulkmail_dbversion', BULKEMAIL_DBVERSION );
		bulkmail_update_option( 'db_update_required', false );

		if ( $count = $wpdb->query( "DELETE a FROM {$wpdb->prefix}bulkmail_actions AS a JOIN (SELECT b.campaign_id, b.subscriber_id FROM {$wpdb->prefix}bulkmail_actions AS b LEFT JOIN {$wpdb->posts} AS p ON p.ID = b.campaign_id WHERE p.ID IS NULL ORDER BY b.campaign_id LIMIT 1000) AS ab ON (a.campaign_id = ab.campaign_id AND a.subscriber_id = ab.subscriber_id)" ) ) {
			echo "removed actions where's no campaign\n";
			return false;
		}

		if ( $count = $wpdb->query( "DELETE a FROM {$wpdb->postmeta} AS a LEFT JOIN {$wpdb->posts} AS p ON p.ID = a.post_id WHERE p.ID IS NULL AND a.meta_key LIKE '_bulkmail_%'" ) ) {
			echo "removed meta where's no campaign\n";
			return false;
		}

		if ( $count = $wpdb->query( "DELETE a FROM {$wpdb->prefix}bulkmail_subscriber_meta AS a WHERE a.meta_value = '' OR a.subscriber_id = 0" ) ) {
			echo "removed unassigned subscriber meta\n";
			return false;
		}

		if ( $count = bulkmail( 'subscribers' )->wp_id() ) {
			echo "assign $count WP users\n";
			return false;
		}

		if ( $this->table_exists( "{$wpdb->prefix}bulkmail_temp_import" ) ) {
			if ( $count = $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bulkmail_temp_import" ) ) {
				echo "removed temporary import table\n";
				return false;
			}
		}

		if ( $count = $wpdb->query( "DELETE a FROM {$wpdb->options} AS a WHERE a.option_name LIKE 'bulkmail_bulk_import%'" ) ) {
			echo "removed temporary import data\n";
			return false;
		}

		$wpdb->query( "UPDATE {$wpdb->prefix}bulkmail_subscribers SET ip_signup = '' WHERE ip_signup = 0" );
		$wpdb->query( "UPDATE {$wpdb->prefix}bulkmail_subscribers SET ip_confirm = '' WHERE ip_confirm = 0" );

		delete_option( 'updatecenter_plugins' );
		do_action( 'updatecenterplugin_check' );

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $table
	 * @return unknown
	 */
	private function table_exists( $table ) {

		global $wpdb;
		return $wpdb->query( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
	}


	/**
	 *
	 *
	 * @param unknown $content (optional)
	 */
	private function output( $content = '' ) {

		global $bulkmail_batch_update_output;

		$bulkmail_batch_update_output[] = $content;

	}


}
