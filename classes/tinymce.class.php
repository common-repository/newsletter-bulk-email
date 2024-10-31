<?php

class BulkmailTinymce {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	public function init() {

		if ( is_admin() ) {
			add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_plugin' ), 10, 3 );
		}

	}


	/**
	 *
	 *
	 * @param unknown $plugin_array
	 * @return unknown
	 */
	public function add_tinymce_plugin( $plugin_array ) {

		global $post;

		if ( isset( $post ) ) {

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			if ( 'newsletter' == $post->post_type ) {

				$plugin_array['bulkmail_mce_button'] = BULKEMAIL_URI . 'assets/js/tinymce-editbar-button' . $suffix . '.js';

				add_action( 'before_wp_tiny_mce', array( &$this, 'editbar_translations' ) );
				add_filter( 'mce_buttons', array( &$this, 'register_mce_button' ) );

			} else {
				$plugin_array['bulkmail_mce_button'] = BULKEMAIL_URI . 'assets/js/tinymce-button' . $suffix . '.js';

				add_action( 'before_wp_tiny_mce', array( &$this, 'translations' ) );
				add_filter( 'mce_buttons', array( &$this, 'register_mce_button' ) );

			}
		}

		return $plugin_array;

	}


	/**
	 *
	 *
	 * @param unknown $buttons
	 * @return unknown
	 */
	public function register_mce_button( $buttons ) {
		array_push( $buttons, 'bulkmail_mce_button' );
		return $buttons;
	}


	/**
	 *
	 *
	 * @param unknown $settings
	 */
	public function editbar_translations( $settings = null ) {

		global $bulkmail_tags;

		if ( ! did_action( 'bulkmail_add_tag' ) ) {
			do_action( 'bulkmail_add_tag' );
		}

		$user = array(
			'firstname'    => esc_html__( 'First Name', 'bulkmail' ),
			'lastname'     => esc_html__( 'Last Name', 'bulkmail' ),
			'fullname'     => esc_html__( 'Full Name', 'bulkmail' ),
			'emailaddress' => esc_html__( 'Email address', 'bulkmail' ),
			'profile'      => esc_html__( 'Profile Link', 'bulkmail' ),
		);

		$customfields = bulkmail()->get_custom_fields();

		foreach ( $customfields as $key => $data ) {
			$user[ $key ] = strip_tags( $data['name'] );
		}

		$tags = array();

		$tags['user'] = array(
			'name' => esc_html__( 'User', 'bulkmail' ),
			'tags' => $user,
		);

		$tags['campaign'] = array(
			'name' => esc_html__( 'Campaign related', 'bulkmail' ),
			'tags' => array(
				'webversion' => esc_html__( 'Webversion', 'bulkmail' ),
				'unsub'      => esc_html__( 'Unsubscribe Link', 'bulkmail' ),
				'forward'    => esc_html__( 'Forward', 'bulkmail' ),
				'subject'    => esc_html__( 'Subject', 'bulkmail' ),
				'preheader'  => esc_html__( 'Preheader', 'bulkmail' ),
			),
		);

		$custom = bulkmail_option( 'custom_tags', array() );
		if ( ! empty( $bulkmail_tags ) ) {
			$custom += $bulkmail_tags;
		}
		if ( ! empty( $custom ) ) {
			$tags['custom'] = array(
				'name' => esc_html__( 'Custom Tags', 'bulkmail' ),
				'tags' => $this->transform_array( $custom ),
			);

		};

		if ( $permanent = bulkmail_option( 'tags' ) ) {
			$tags['permanent'] = array(
				'name' => esc_html__( 'Permanent Tags', 'bulkmail' ),
				'tags' => $this->transform_array( $permanent ),
			);

		};

		$tags['date'] = array(
			'name' => esc_html__( 'Date', 'bulkmail' ),
			'tags' => array(
				'year'  => esc_html__( 'Current Year', 'bulkmail' ),
				'month' => esc_html__( 'Current Month', 'bulkmail' ),
				'day'   => esc_html__( 'Current Day', 'bulkmail' ),
			),
		);

		echo '<script type="text/javascript">';
		echo 'bulkmail_mce_button = ' . json_encode(
			array(
				'l10n' => array(
					'tags'   => array(
						'title' => esc_html__( 'Bulkmail Tags', 'bulkmail' ),
						'tag'   => esc_html__( 'Tag', 'bulkmail' ),
						'tags'  => esc_html__( 'Tags', 'bulkmail' ),
					),
					'remove' => array(
						'title' => esc_html__( 'Remove Block', 'bulkmail' ),
					),
				),
				'tags' => $tags,
			)
		);
		echo '</script>';

	}


	/**
	 *
	 *
	 * @return unknown
	 * @param unknown $settings
	 */
	public function translations( $settings ) {

		$forms = bulkmail( 'forms' )->get_list();

		echo '<script type="text/javascript">';
		echo 'bulkmail_mce_button = ' . json_encode(
			array(
				'l10n'    => array(
					'title'    => 'Bulkmail',
					'homepage' => array(
						'menulabel'    => esc_html__( 'Newsletter Homepage', 'bulkmail' ),
						'title'        => esc_html__( 'Insert Newsletter Homepage Shortcodes', 'bulkmail' ),
						'prelabel'     => esc_html__( 'Text', 'bulkmail' ),
						'pre'          => esc_html__( 'Signup for the newsletter', 'bulkmail' ),
						'confirmlabel' => esc_html__( 'Confirm Text', 'bulkmail' ),
						'confirm'      => esc_html__( 'Thanks for your interest!', 'bulkmail' ),
						'unsublabel'   => esc_html__( 'Unsubscribe Text', 'bulkmail' ),
						'unsub'        => esc_html__( 'Do you really want to unsubscribe?', 'bulkmail' ),
					),
					'button'   => array(
						'menulabel'  => esc_html__( 'Subscriber Button', 'bulkmail' ),
						'title'      => esc_html__( 'Insert Subscriber Button Shortcode', 'bulkmail' ),
						'labellabel' => esc_html__( 'Label', 'bulkmail' ),
						'label'      => esc_html__( 'Subscribe', 'bulkmail' ),
						'count'      => esc_html__( 'Display subscriber count', 'bulkmail' ),
						'countabove' => esc_html__( 'Count above Button', 'bulkmail' ),
						'design'     => esc_html__( 'Design', 'bulkmail' ),
					),
					'form'     => esc_html__( 'Form', 'bulkmail' ),
					'forms'    => esc_html__( 'Forms', 'bulkmail' ),
				),
				'forms'   => $forms,
				'designs' => array(
					'default' => 'Default',
					'twitter' => 'Twitter',
					'wp'      => 'WordPress',
					'flat'    => 'Flat',
					'minimal' => 'Minimal',
				),
			)
		);
		echo '</script>';

	}


	/**
	 *
	 *
	 * @param unknown $array
	 * @return unknown
	 */
	private function transform_array( $array ) {

		$return = array();

		foreach ( $array as $tag => $data ) {
			$return[ $tag ] = ucwords( str_replace( array( '-', '_' ), ' ', strip_tags( $tag ) ) );
		}

		return $return;

	}


}
