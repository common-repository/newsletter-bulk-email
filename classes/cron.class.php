<?php

class BulkmailCron {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	public function init() {

		add_filter( 'cron_schedules', array( &$this, 'filter_cron_schedules' ) );
		add_action( 'bulkmail_cron', array( &$this, 'hourly_cronjob' ) );
		add_action( 'bulkmail_cron_worker', array( &$this, 'handler' ), -1 );

		add_action( 'bulkmail_campaign_pause', array( &$this, 'update' ) );
		add_action( 'bulkmail_campaign_start', array( &$this, 'update' ) );
		add_action( 'bulkmail_campaign_duplicate', array( &$this, 'update' ) );

		if ( ! wp_next_scheduled( 'bulkmail_cron' ) ) {
			$this->update( true );
		}

		add_action( 'wp_ajax_bulkmail_cron', array( &$this, 'cron_worker' ) );
		add_action( 'wp_ajax_nopriv_bulkmail_cron', array( &$this, 'cron_worker' ) );
		add_action( 'template_redirect', array( &$this, 'template_redirect' ), 1 );

	}



	/**
	 * Checks for new newsletter in the queue to start new cronjob
	 */
	public function hourly_cronjob() {

		// check for bounced emails
		do_action( 'bulkmail_check_bounces' );

		// send confirmations again
		do_action( 'bulkmail_resend_confirmations' );

		$this->update();

	}

	/**
	 *
	 *
	 * @return unknown
	 */
	public function handler() {

		if ( defined( 'BULKEMAIL_DOING_CRON' ) || defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) || defined( 'WP_INSTALLING' ) || defined( 'BULKEMAIL_DO_UPDATE' ) ) {
			return false;
		}

		define( 'BULKEMAIL_DOING_CRON', microtime( true ) );

		register_shutdown_function( array( &$this, 'shutdown_function' ) );

	}


	public function shutdown_function() {

		if ( ! defined( 'BULKEMAIL_DOING_CRON' ) ) {
			return;
		}

		$error = error_get_last();

		if ( ! is_null( $error ) && $error['type'] == 1 && 0 === strpos( $error['file'], BULKEMAIL_DIR ) ) {

			$msg = sprintf( esc_html__( 'It looks like your last cronjob hasn\'t been finished! Increase the %1$s, add %2$s to your wp-config.php or reduce the %3$s in the settings.', 'bulkmail' ), "'max_execution_time'", '<code>define("WP_MEMORY_LIMIT", "256M");</code>', '<a href="' . add_query_arg( array( 'bulkmail_remove_notice' => 'cron_unfinished' ), admin_url( 'edit.php?post_type=newsletter&page=bulkmail_settings#delivery' ) ) . '">' . esc_html__( 'Number of mails sent', 'bulkmail' ) . '</a>' );

			$msg .= '<pre><code>' . esc_html( $error['message'] ) . '</code></pre>';

			bulkmail_notice( $msg, 'error', false, 'cron_unfinished' );

		} else {

			bulkmail_remove_notice( 'cron_unfinished' );

		}

	}


	/**
	 *
	 *
	 * @param unknown $hourly_only (optional)
	 * @return unknown
	 */
	public function update( $hourly_only = false ) {

		if ( ! wp_next_scheduled( 'bulkmail_cron' ) ) {

			// main schedule always 5 minutes before full hour
			wp_schedule_event( strtotime( 'midnight' ) - 300, 'hourly', 'bulkmail_cron' );
			// stop here cause bulkmail_cron triggers the worker if required
			return true;
		} elseif ( $hourly_only ) {
			return false;
		}

		// remove the WordPress cron if "normal" cron is used
		if ( bulkmail_option( 'cron_service' ) != 'wp_cron' ) {
			$this->unschedule();
			return false;
		}

		$this->schedule();

		return false;

	}


	public function schedule( $unschedule = false ) {

		if ( $unschedule ) {
			$this->unschedule();
		}

		// add worker only once
		if ( ! wp_next_scheduled( 'bulkmail_cron_autoresponder' ) ) {
			wp_schedule_event( floor( time() / 300 ) * 300 - 30, 'bulkmail_cron_interval', 'bulkmail_cron_autoresponder' );
		}
		if ( ! wp_next_scheduled( 'bulkmail_cron_bounce' ) ) {
			wp_schedule_event( floor( time() / 300 ) * 300 - 30, 'bulkmail_cron_interval', 'bulkmail_cron_bounce' );
		}
		if ( ! wp_next_scheduled( 'bulkmail_cron_worker' ) ) {
			wp_schedule_event( floor( time() / 300 ) * 300, 'bulkmail_cron_interval', 'bulkmail_cron_worker' );
		}
		if ( ! wp_next_scheduled( 'bulkmail_cron_cleanup' ) ) {
			wp_schedule_event( strtotime( 'midnight' ) - 180, 'hourly', 'bulkmail_cron_cleanup' );
		}
	}


	public function unschedule() {
		wp_clear_scheduled_hook( 'bulkmail_cron_autoresponder' );
		wp_clear_scheduled_hook( 'bulkmail_cron_bounce' );
		wp_clear_scheduled_hook( 'bulkmail_cron_worker' );
		wp_clear_scheduled_hook( 'bulkmail_cron_cleanup' );
	}


	/**
	 * add custom time to cron
	 *
	 * @param unknown $cron_schedules
	 * @return unknown
	 */
	public function filter_cron_schedules( $cron_schedules ) {

		$cron_schedules['bulkmail_cron_interval'] = array(
			'interval' => bulkmail_option( 'interval', 5 ) * 60, // seconds
			'display'  => 'Bulkmail Cronjob Interval',
		);

		return $cron_schedules;
	}


	/**
	 *
	 *
	 * @param unknown $general (optional)
	 */
	public function remove_crons( $general = false ) {
		wp_clear_scheduled_hook( 'bulkmail_cron_worker' );
		if ( $general ) {
			wp_clear_scheduled_hook( 'bulkmail_cron' );
		}

	}


	public function check( $strict = false ) {

		global $wpdb;

		$now          = time();
		$cron_service = bulkmail_option( 'cron_service' );

		if ( ! bulkmail( 'queue' )->size() && ! $strict ) :

			bulkmail_remove_notice( 'check_cron' );

			return true;

		else :

			$interval = bulkmail_option( 'interval' ) * 60;
			$last_hit = get_option( 'bulkmail_cron_lasthit' );

			if ( ! $last_hit ) {
				if ( is_array( $last_hit ) ) {
					return new WP_Error( 'cron_error', sprintf( esc_html__( 'Your Cron page hasn\'t get triggered recently. This is required to send campaigns. Please check the %s', 'bulkmail' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=bulkmail_settings#cron' ) . '"><strong>' . esc_html__( 'settings page', 'bulkmail' ) . '</strong></a>.' ) );
				}

				return new WP_Error( 'cron_error', sprintf( esc_html__( 'The Cron Process is not setup correctly. This is required to send campaigns. Please check the %s', 'bulkmail' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=bulkmail_settings#cron' ) . '"><strong>' . esc_html__( 'settings page', 'bulkmail' ) . '</strong></a>.' ) );
			}

			// get real delay...
			$real_delay    = max( $interval, $last_hit['timestamp'] - $last_hit['oldtimestamp'] );
			$current_delay = $now - $last_hit['timestamp'];

			// ..and compare it with the interval (3 times) - also something in the queue
			if ( ( $current_delay > $real_delay * 3 || ! $real_delay && ! $current_delay ) ) :

				$this->update();

				return new WP_Error( 'cron_warning', sprintf( esc_html__( 'Are your campaigns not sending? You may have to check your %1$s', 'bulkmail' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=bulkmail_settings#cron' ) . '"><strong>' . esc_html__( 'cron settings', 'bulkmail' ) . '</strong></a>' ) );

			else :

				bulkmail_remove_notice( 'check_cron' );
				return true;

			endif;

		endif;

	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function lock( $key = 0 ) {

		if ( bulkmail_option( 'cron_lock' ) == 'db' ) {

			$this->pid = get_option( 'bulkmail_cron_lock_' . $key, false );

			if ( $this->pid ) {
				if ( $this->is_locked( $key ) ) {
					return $this->pid;
				} else {
				}
			}

			$this->pid = @getmypid();
			update_option( 'bulkmail_cron_lock_' . $key, $this->pid, false );
			return true;

		} else {

			$lockfile = BULKEMAIL_UPLOAD_DIR . '/CRON_' . $key . '.lockfile';

			if ( file_exists( $lockfile ) ) {
				// Is running?
				$this->pid = file_get_contents( $lockfile );
				if ( $this->is_locked( $key ) ) {
					return $this->pid;
				} else {
				}
			}

			$this->pid = @getmypid();
			register_shutdown_function( array( $this, 'unlock' ), $key );
			file_put_contents( $lockfile, $this->pid );
			return true;

		}

	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function unlock( $key = 0 ) {

		if ( bulkmail_option( 'cron_lock' ) == 'db' ) {

			update_option( 'bulkmail_cron_lock_' . $key, false, false );

		} else {
			$lockfile = BULKEMAIL_UPLOAD_DIR . '/CRON_' . $key . '.lockfile';

			if ( file_exists( $lockfile ) ) {

				unlink( $lockfile );
			}
		}

		return true;
	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function is_locked( $key = null ) {

		global $wpdb;

		$exec = is_callable( 'shell_exec' ) && false === stripos( ini_get( 'disable_functions' ), 'shell_exec' );

		if ( is_integer( $key ) && $exec ) {
			$pids = explode( PHP_EOL, `ps -e | awk '{print $1}'` );
			if ( in_array( $this->pid, $pids ) || empty( $pids[0] ) ) {
				return true;
			}

			return false;

		} else {

			if ( ! is_integer( $key ) ) {
				$key = '';
			}
		}

		if ( bulkmail_option( 'cron_lock' ) == 'db' ) {

			$sql = "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value != ''";
			$res = $wpdb->get_var( $wpdb->prepare( $sql, 'bulkmail_cron_lock_' . $key . '%' ) );

			return ! ! $res;

		} else {

			$lockfiles = glob( BULKEMAIL_UPLOAD_DIR . '/CRON_' . $key . '*.lockfile' );

			return ! empty( $lockfiles );

		}

	}



	/**
	 *
	 *
	 * @return unknown
	 */
	public function url( $alternative = false ) {

		if ( ! $alternative ) {

			if ( bulkmail_option( 'got_url_rewrite' ) ) {
				return apply_filters( 'bulkmail_cron_url', get_home_url( null, 'bulkmail/' . bulkmail_option( 'cron_secret' ) ), $alternative );
			} else {
				return apply_filters(
					'bulkmail_cron_url',
					add_query_arg(
						array(
							'secret' => bulkmail_option( 'cron_secret' ),
						),
						BULKEMAIL_URI . 'cron.php'
					),
					$alternative
				);

			}
		} else {
			return apply_filters(
				'bulkmail_cron_url',
				add_query_arg(
					array(
						'action' => 'bulkmail_cron',
						'secret' => bulkmail_option( 'cron_secret' ),
					),
					admin_url( 'admin-ajax.php' )
				),
				$alternative
			);

		}

	}


	public function path( $arguments = false ) {

		$path = BULKEMAIL_DIR . 'cron.php';

		if ( $arguments ) {
			$path .= ' ' . bulkmail_option( 'cron_secret' );
		}

		return $path;

	}


	public function template_redirect() {

		if ( $secret = get_query_var( '_bulkmail_cron' ) ) {
			$this->cron_page( $secret );
		}

	}


	public function cron_worker() {

		$secret = isset( $_GET['secret'] ) ? $_GET['secret'] : false;
		$this->cron_page( $secret );

	}

	public function cron_page( $secret ) {

		if ( ! defined( 'BULKEMAIL_CRON_SECRET' ) ) {
			define( 'BULKEMAIL_CRON_SECRET', $secret );
		}

		include BULKEMAIL_DIR . 'cron.php';
		exit();

	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		$this->update();

		if ( $new ) {
			add_option( 'bulkmail_cron_lasthit', false, '', 'no' );
		}

	}


	public function on_deactivate() {

		$this->remove_crons( true );

	}


}
