<?php

class BulkmailBounce {

	private $mailbox;

	/**
	 *
	 *
	 * @param unknown $service (optional)
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	public function init() {

		add_action( 'bulkmail_cron_bounce', array( &$this, 'check' ), 1 );
		add_action( 'bulkmail_check_bounces', array( &$this, 'check' ), 99 );

	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function bounce_lock( $bool = true ) {

		set_transient( 'bulkmail_check_bounces_lock', $bool, bulkmail_option( 'bounce_check', 5 ) * 60 );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function is_bounce_lock() {

		return get_transient( 'bulkmail_check_bounces_lock' );

	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function send_test() {

		$identifier = 'bulkmail_' . md5( uniqid() );

		$mail          = bulkmail( 'mail' );
		$mail->to      = bulkmail_option( 'bounce' );
		$mail->subject = 'Bulkmail Bounce Test ' . $identifier;

		$replace = array(
			'preheader'    => 'You can delete this message!',
			'notification' => 'This message was sent from your WordPress blog to test your bounce server. You can delete this message!',
		);

		if ( $mail->send_notification( $identifier, $mail->subject, $replace ) ) {
			return $identifier;
		}

		return false;
	}


	/**
	 *
	 *
	 * @param unknown $server  (optional)
	 * @param unknown $user    (optional)
	 * @param unknown $pwd     (optional)
	 * @param unknown $port    (optional)
	 * @param unknown $secure  (optional)
	 * @param unknown $service (optional)
	 * @return unknown
	 */
	public function get_handler( $server = null, $user = null, $pwd = null, $port = null, $secure = null, $service = null ) {

		$server  = ! is_null( $server ) ? $server : bulkmail_option( 'bounce_server' );
		$user    = ! is_null( $user ) ? $user : bulkmail_option( 'bounce_user' );
		$pwd     = ! is_null( $pwd ) ? $pwd : bulkmail_option( 'bounce_pwd' );
		$port    = ! is_null( $port ) ? $port : bulkmail_option( 'bounce_port', 110 );
		$secure  = ! is_null( $secure ) ? $secure : bulkmail_option( 'bounce_secure' );
		$service = ! is_null( $service ) ? $service : bulkmail_option( 'bounce_service' );

		require_once BULKEMAIL_DIR . 'classes/libs/bouncehandler.class.php';

		switch ( $service ) {
			case 'pop3':
			case 'imap':
			case 'nntp':
				$handler = new BulkmailBounceHandler( $service );
				break;
			default:
				$handler = new BulkmailBounceLegacyHandler();
				break;
		}

		$connect = $handler->connect( $server, $user, $pwd, $port, $secure, $service, 10 );

		if ( is_wp_error( $connect ) ) {

			return $connect;

		}

		return $handler;

	}


	/**
	 *
	 *
	 * @param unknown $identifier
	 * @return unknown
	 */
	public function test( $identifier ) {

		$handler = $this->get_handler();

		if ( is_wp_error( $handler ) ) {

			return $handler;

		}

		return $handler->check_bounce_message( $identifier );

	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function check( $force = false ) {

		if ( ! bulkmail_option( 'bounce_active' ) ) {
			return false;
		}

		if ( $this->is_bounce_lock() && ! $force ) {
			return false;
		}

		$handler = $this->get_handler();

		if ( is_wp_error( $handler ) ) {

			bulkmail_notice( sprintf( esc_html__( 'It looks like your bounce server setting is incorrect! Last error: %s', 'bulkmail' ), '<br><strong>' . $handler->get_error_message() . '</strong>' ), 'error', true, 'bounce_server' );

			return;
		}

		return $handler->process_bounces();

	}


}
