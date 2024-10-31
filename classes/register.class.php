<?php

class BulkmailRegister {

	public function __construct() {

		add_action( 'bulkmail_register_bulkmail', array( &$this, 'on_register' ), 10, 3 );
		add_action( 'bulkmail_remove_notice_verify', array( &$this, 'verified_notice_closed' ) );
		add_action( 'wp_version_check', array( &$this, 'verified_notice' ) );

		bulkmail_localize_script(
			'register',
			array(
				'error' => esc_html__( 'There was an error while processing your request!', 'bulkmail' ),
				'help'  => esc_html__( 'Help me!', 'bulkmail' ),
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $args     (optional)
	 */
	public function form( $args = array() ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'bulkmail-register-style', BULKEMAIL_URI . 'assets/css/register-style' . $suffix . '.css', array(), BULKEMAIL_VERSION );

		wp_enqueue_script( 'bulkmail-register-script', BULKEMAIL_URI . 'assets/js/register-script' . $suffix . '.js', array( 'bulkmail-script' ), BULKEMAIL_VERSION, true );

		$slug     = 'bulkmail';
		$verified = bulkmail()->is_verified();

		$page = isset( $_GET['page'] ) ? str_replace( 'bulkmail_', '', $_GET['page'] ) : 'dashboard';

		$args = wp_parse_args(
			$args,
			array(
				'pretext'      => sprintf( esc_html__( 'Enter Your Purchase Code To Register (Don\'t have one for this site? %s)', 'bulkmail' ), '<a href="' . esc_url( 'https://bulkmail.co/go/buy/?utm_campaign=plugin&utm_medium=' . $page . '&utm_source=bulkmail_plugin' ) . '" class="external">' . esc_html__( 'Buy Now!', 'bulkmail' ) . '</a>' ),
				'purchasecode' => bulkmail()->license(),
			)
		);

		$user_id = get_current_user_id();
		$user    = get_userdata( $user_id );

		$username  = bulkmail()->username( '' );
		$useremail = bulkmail()->email( '' );

		wp_print_styles( 'bulkmail-register-style' );

		?>

		<div class="register_form_wrap register_form_wrap-<?php echo esc_attr( $slug ); ?> loading<?php echo $verified ? ' step-3' : ' step-1'; ?>">
			<input type="hidden" class="register-form-slug" name="slug" value="<?php echo esc_attr( $slug ); ?>">
			<div class="register-form-info">
				<span class="step-1"><?php esc_html_e( 'Verifying Purchase Code', 'bulkmail' ); ?>&hellip;</span>
				<span class="step-2"><?php esc_html_e( 'Finishing Registration', 'bulkmail' ); ?>&hellip;</span>
			</div>
			<form class="register_form" action="" method="POST">
<!--				<div class="howto">--><?php //echo esc_html($args['pretext']); ?><!--</div>-->
				<div class="error-msg">&nbsp;</div>
				<input type="text" class="widefat register-form-purchasecode" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" name="purchasecode" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" maxlength="36" value="<?php echo esc_attr( $args['purchasecode'] ); ?>">
				<input type="submit" class="button button-hero button-primary dashboard-register" value="<?php esc_attr_e( 'Verify Purchase Code', 'bulkmail' ); ?>">
<!--				<div class="howto">-->
<!--					<a href="https://static.bulkmail.co/images/purchasecode.gif" class="howto-purchasecode">--><?php //esc_html_e( 'Where can I find my item purchase code?', 'bulkmail' ); ?><!--</a>-->
<!--				</div>-->
			</form>
			<form class="register_form_2" action="" method="POST">
				<div class="error-msg">&nbsp;</div>
				<input type="text" class="widefat username" placeholder="<?php esc_attr_e( 'Username', 'bulkmail' ); ?>" name="username" value="<?php echo esc_attr( $username ); ?>">
				<input type="email" class="widefat email" placeholder="Email" name="email" value="<?php echo esc_attr( $useremail ); ?>">
<!--				<div class="howto tos-field"><input type="checkbox" name="tos" class="tos" value="--><?php //echo esc_attr(time()); ?><!--"> --><?php //printf( esc_html__( 'I agree to the %1$s and the %2$s by completing the registration.', 'bulkmail' ), '<a href="https://bulkmail.co/legal/tos/" class="external">' . esc_html__( 'Terms of service', 'bulkmail' ) . '</a>', '<a href="https://bulkmail.co/legal/privacy-policy/" class="external">' . esc_html__( 'Privacy Policy', 'bulkmail' ) . '</a>' ); ?><!--</div>-->
				<input type="submit" class="button button-hero button-primary" value="<?php esc_attr_e( 'Complete Registration', 'bulkmail' ); ?>">
			</form>
			<form class="registration_complete">
				<div class="registration_complete_check"></div>
				<div class="registration_complete_text"><?php esc_html_e( 'All Set!', 'bulkmail' ); ?></div>
			</form>
		</div>
		<?php
		bulkmail( 'helper' )->dialog(
//			'<img src="https://static.bulkmail.co/images/purchasecode.gif">',
			array(
				'id'      => 'registration-dialog',
				'buttons' => array(
					array(
						'label'   => esc_html__( 'OK got it', 'bulkmail' ),
						'classes' => 'button button-primary right notification-dialog-dismiss',
					),
				),
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $username
	 * @param unknown $email
	 * @param unknown $purchasecode
	 */
	public function on_register( $username, $email, $purchasecode ) {

		update_option( 'bulkmail_license', $purchasecode );
		delete_transient( 'bulkmail_verified' );
		bulkmail_remove_notice( 'verify' );

	}


	public function verified_notice_closed() {

		set_transient( 'bulkmail_skip_verifcation_notices', true, WEEK_IN_SECONDS );

	}


	public function verified_notice() {

		if ( bulkmail_is_local() ) {
			return;
		}

		if ( get_transient( 'bulkmail_skip_verifcation_notices' ) ) {
			return;
		}

		if ( ! bulkmail()->is_verified() ) {
			if ( time() - get_option( 'bulkmail' ) > WEEK_IN_SECONDS
				&& get_option( 'bulkmail_setup' ) ) {
				bulkmail_notice( sprintf( esc_html__( 'Hey! Would you like automatic updates and premium support? Please %s of Bulkmail', 'bulkmail' ), '<a href="admin.php?page=bulkmail_dashboard">' . esc_html__( 'activate your copy', 'bulkmail' ) . '</a>' ), 'error', false, 'verify', 'bulkmail_manage_licenses' );
			}
		} else {
			bulkmail_remove_notice( 'verify' );
		}

		if ( bulkmail()->is_outdated() ) {
//			bulkmail_notice( sprintf( esc_html__( 'Hey! Looks like you have an outdated version of Bulkmail! It\'s recommended to keep the plugin up to date for security reasons and new features. Check the %s for the most recent version.', 'bulkmail' ), '<a href="https://bulkmail.co/changelog?v=' . BULKEMAIL_VERSION . '">' . esc_html__( 'changelog page', 'bulkmail' ) . '</a>' ), 'error', false, 'outdated', 'bulkmail_manage_licenses' );
		} else {
			bulkmail_remove_notice( 'outdated' );
		}
	}


}
