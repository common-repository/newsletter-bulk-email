<?php

class BulkmailDashboard {

	private $metaboxes = array();

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'init' ) );
		add_action( 'admin_menu', array( &$this, 'menu' ), -1 );

	}


	public function init() {

		add_filter( 'dashboard_glance_items', array( &$this, 'dashboard_glance_items' ), 99 );
		add_action( 'wp_dashboard_setup', array( &$this, 'add_widgets' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'remove_menu_entry' ), 10, 4 );
		add_filter( 'postbox_classes_newsletter_page_bulkmail_dashboard_bulkmail-mb-bulkmail', array( &$this, 'post_box_classes_for_bulkmail' ) );

	}


	public function init_page() {

		if ( isset( $_GET['bulkmail_setup_complete'] ) && wp_verify_nonce( $_GET['bulkmail_setup_complete'], 'bulkmail_setup_complete' ) ) {

			if ( ! get_option( 'bulkmail_setup' ) ) {
				update_option( 'bulkmail_setup', time() );
			}
			wp_redirect( admin_url( 'admin.php?page=bulkmail_dashboard' ) );
			exit;

		}

		if ( isset( $_GET['reset_license'] ) && wp_verify_nonce( $_GET['reset_license'], 'bulkmail_reset_license' ) && current_user_can( 'bulkmail_manage_licenses' ) ) {

			$result = bulkmail()->reset_license();

			if ( is_wp_error( $result ) ) {
				bulkmail_notice( esc_html__( 'There was an Error while processing your request!', 'bulkmail' ) . '<br>' . $result->get_error_message(), 'error', true );
			} else {
				update_option( 'bulkmail_license', '' );
				bulkmail_notice( esc_html__( 'Your License has been reset!', 'bulkmail' ), '', true );
			}

			wp_redirect( admin_url( 'admin.php?page=bulkmail_dashboard' ) );
			exit;
		}

		if ( ! get_option( 'bulkmail_setup' ) ) {
			wp_redirect( admin_url( 'admin.php?page=bulkmail_setup' ) );
			exit;
		}

	}


	public function remove_menu_entry() {

		if ( current_user_can( 'bulkmail_dashboard' ) ) {
			wp_add_inline_style( 'bulkmail-admin', '@media only screen and (min-width: 783px){#menu-posts-newsletter .wp-first-item{display: none;}}' );
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function menu() {

		global $submenu;

		if ( ! current_user_can( 'bulkmail_dashboard' ) ) {
			return;
		}

		$slug = 'edit.php?post_type=newsletter';

		$page = add_submenu_page( $slug, esc_html__( 'Bulkmail Dashboard', 'bulkmail' ), esc_html__( 'Dashboard', 'bulkmail' ), 'bulkmail_dashboard', 'bulkmail_dashboard', array( &$this, 'dashboard' ) );
		add_action( 'load-' . $page, array( &$this, 'init_page' ) );
		add_action( 'load-' . $page, array( &$this, 'scripts_styles' ) );
		add_action( 'load-' . $page, array( &$this, 'register_meta_boxes' ) );

		if ( isset( $submenu[ $slug ][11] ) ) {
			$submenu[ $slug ][0] = $submenu[ $slug ][11];
			unset( $submenu[ $slug ][11] );
			ksort( $submenu[ $slug ] );
		}

	}


	public function dashboard() {

		$this->update       = bulkmail()->has_update();
		$this->verified     = bulkmail()->is_verified();
		$this->plugin_info  = bulkmail()->plugin_info();
		$this->is_dashboard = false;

		$this->screen = get_current_screen();

		include BULKEMAIL_DIR . 'views/dashboard.php';
	}


	public function widget() {
		$this->is_dashboard = true;
		include BULKEMAIL_DIR . 'views/dashboard/widget.php';
	}


	public function quick_links() {
		include BULKEMAIL_DIR . 'views/dashboard/mb-quicklinks.php';
	}


	public function campaigns() {
		include BULKEMAIL_DIR . 'views/dashboard/mb-campaigns.php';
	}


	public function bulkmail() {
		include BULKEMAIL_DIR . 'views/dashboard/mb-bulkmail.php';
	}


	public function subscribers() {
		include BULKEMAIL_DIR . 'views/dashboard/mb-subscribers.php';
	}


	public function lists() {
		include BULKEMAIL_DIR . 'views/dashboard/mb-lists.php';
	}


	public function register_meta_boxes() {

		$this->register_meta_box( 'quick-links', esc_html__( 'Quick Links', 'bulkmail' ), array( &$this, 'quick_links' ) );
		$this->register_meta_box( 'campaigns', esc_html__( 'My Campaigns', 'bulkmail' ), array( &$this, 'campaigns' ) );
//		if ( current_user_can( 'bulkmail_manage_licenses' ) ) {
//			$this->register_meta_box( 'bulkmail', esc_html__( 'My Bulkmail', 'bulkmail' ), array( &$this, 'bulkmail' ), 'side', 'high' );
//		}
		$this->register_meta_box( 'subscribers', esc_html__( 'My Subscribers', 'bulkmail' ), array( &$this, 'subscribers' ), 'side' );
		$this->register_meta_box( 'lists', esc_html__( 'My Lists', 'bulkmail' ), array( &$this, 'lists' ), 'side' );

	}


	/**
	 *
	 *
	 * @param unknown $classes
	 * @return unknown
	 */
	public function post_box_classes_for_bulkmail( $classes ) {

		if ( $this->verified ) {
			$classes[] = 'verified';
		}
		if ( bulkmail()->has_update() ) {
			$classes[] = 'has-update';
		} elseif ( bulkmail( 'translations' )->translation_available() ) {
			$classes[] = 'has-translation-update';
		}

		return $classes;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $title
	 * @param unknown $callback
	 * @param unknown $context       (optional)
	 * @param unknown $priority      (optional)
	 * @param unknown $callback_args (optional)
	 */
	public function register_meta_box( $id, $title, $callback, $context = 'normal', $priority = 'default', $callback_args = null ) {

		$id     = 'bulkmail-mb-' . sanitize_key( $id );
		$screen = get_current_screen();

		add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $context (optional)
	 */
	public function unregister_meta_box( $id, $context = 'normal' ) {

		$id     = 'bulkmail-mb-' . sanitize_key( $id );
		$screen = get_current_screen();

		remove_meta_box( $id, $screen, $context );

	}


	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-touch-punch' );

		wp_enqueue_script( 'easy-pie-chart', BULKEMAIL_URI . 'assets/js/libs/easy-pie-chart' . $suffix . '.js', array( 'jquery' ), BULKEMAIL_VERSION, true );
		wp_enqueue_style( 'easy-pie-chart', BULKEMAIL_URI . 'assets/css/libs/easy-pie-chart' . $suffix . '.css', array(), BULKEMAIL_VERSION );
		wp_enqueue_script( 'bulkmail-chartjs', BULKEMAIL_URI . 'assets/js/libs/chart' . $suffix . '.js', array( 'easy-pie-chart' ), BULKEMAIL_VERSION, true );

		wp_enqueue_script( 'bulkmail-dashboard-script', BULKEMAIL_URI . 'assets/js/dashboard-script' . $suffix . '.js', array( 'bulkmail-script' ), BULKEMAIL_VERSION, true );
		wp_enqueue_style( 'bulkmail-dashboard-style', BULKEMAIL_URI . 'assets/css/dashboard-style' . $suffix . '.css', array(), BULKEMAIL_VERSION );

		bulkmail_localize_script(
			'dashboard',
			array(
				'subscribers'   => esc_html__( '%s Subscribers', 'bulkmail' ),
				'reset_license' => esc_html__( 'You can reset your license up to three times!', 'bulkmail' ) . "\n" . esc_html__( 'Do you really like to reset your license for this site?', 'bulkmail' ),
				'check_again'   => esc_html__( 'Check Again', 'bulkmail' ),
				'checking'      => esc_html__( 'Checking...', 'bulkmail' ),
				'downloading'   => esc_html__( 'Downloading...', 'bulkmail' ),
				'reload_page'   => esc_html__( 'Complete. Reload page!', 'bulkmail' ),
			)
		);
	}




	public function add_widgets() {

		if ( ! current_user_can( 'bulkmail_dashboard_widget' ) ) {
			return;
		}

		add_meta_box( 'dashboard_bulkmail', esc_html__( 'Newsletter', 'bulkmail' ), array( &$this, 'widget' ), 'dashboard', 'side', 'high' );

		add_action( 'admin_enqueue_scripts', array( &$this, 'scripts_styles' ), 10, 1 );

	}


	/**
	 *
	 *
	 * @param unknown $elements
	 * @return unknown
	 */
	public function dashboard_glance_items( $elements ) {

		$autoresponder = count( bulkmail_get_autoresponder_campaigns() );
		$elements[]    = '</ul><br><ul>';

		if ( $campaigns = count( bulkmail_get_campaigns() ) ) {
			$elements[] = '<a class="bulkmail-campaigns" href="edit.php?post_type=newsletter">' . number_format_i18n( $campaigns - $autoresponder ) . ' ' . esc_html__( _nx( 'Campaign', 'Campaigns', $campaigns - $autoresponder, 'number of', 'bulkmail' ) ) . '</a>';
		}

		if ( $autoresponder ) {
			$elements[] = '<a class="bulkmail-campaigns" href="edit.php?post_status=autoresponder&post_type=newsletter">' . number_format_i18n( $autoresponder ) . ' ' . esc_html__( _nx( 'Autoresponder', 'Autoresponders', $autoresponder, 'number of', 'bulkmail' ) ) . '</a>';
		}

		if ( $subscribers = bulkmail( 'subscribers' )->get_totals( 1 ) ) {
			$elements[] = '<a class="bulkmail-subscribers" href="edit.php?post_type=newsletter&page=bulkmail_subscribers">' . number_format_i18n( $subscribers ) . ' ' . esc_html__( _nx( 'Subscriber', 'Subscribers', $subscribers, 'number of', 'bulkmail' ) ) . '</a>';
		}

		return $elements;
	}


}
