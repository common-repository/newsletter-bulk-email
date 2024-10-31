<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Bulkmail_Subscribers_Table extends WP_List_Table {

	public $total_items;
	public $total_pages;
	public $per_page;

	public function __construct() {

		parent::__construct(
			array(
				'singular' => esc_html__( 'Subscriber', 'bulkmail' ), // singular name of the listed records
				'plural'   => esc_html__( 'Subscribers', 'bulkmail' ), // plural name of the listed records
				'ajax'     => false, // does this table support ajax?
			)
		);

		add_action( 'admin_footer', array( &$this, 'script' ) );
		add_filter( 'manage_newsletter_page_bulkmail_subscribers_columns', array( &$this, 'get_columns' ) );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_views() {

		$counts        = bulkmail( 'subscribers' )->get_count_by_status();
		$statuses      = bulkmail( 'subscribers' )->get_status();
		$statuses_nice = bulkmail( 'subscribers' )->get_status( null, true );
		$link          = admin_url( 'edit.php?post_type=newsletter&page=bulkmail_subscribers' );
		$views         = array(
			'view-all' => '<a href="' . remove_query_arg( 'status', $link ) . '">' . esc_html__( 'All', 'bulkmail' ) . ' <span class="count">(' . number_format_i18n( array_sum( $counts ) ) . ')</span></a>',
		);

		foreach ( $counts as $id => $count ) {
			$views[ 'view-' . $statuses[ $id ] ] = '<a href="' . add_query_arg( array( 'status' => $id ), $link ) . '">' . $statuses_nice[ $id ] . ' <span class="count">(' . number_format_i18n( $count ) . ')</span></a>';
		}

		return $views;
	}


	public function script() {
	}


	public function no_items() {

		$status = isset( $_GET['status'] ) ? (int) $_GET['status'] : null;

		switch ( $status ) {
			case '0': // pending
				esc_html_e( 'No pending subscribers found', 'bulkmail' );
				break;
			case '2': // unsubscribed
				esc_html_e( 'No unsubscribed subscribers found', 'bulkmail' );
				break;
			case '3': // hardbounced
				esc_html_e( 'No hardbounced subscribers found', 'bulkmail' );
				break;
			case '4': // error
				esc_html_e( 'No subscriber with delivery errors found', 'bulkmail' );
				break;
			default:
				esc_html_e( 'No subscribers found', 'bulkmail' );

		}

		if ( current_user_can( 'bulkmail_add_subscribers' ) ) {
			echo ' <a href="edit.php?post_type=newsletter&page=bulkmail_subscribers&new">' . esc_html__( 'Add New', 'bulkmail' ) . '</a>';
		}

	}


	/**
	 *
	 *
	 * @param unknown $text
	 * @param unknown $input_id
	 */
	public function search_box( $text, $input_id ) {

		if ( ! count( $this->items ) && ! isset( $_GET['s'] ) ) {
			return;
		}

		if ( isset( $_GET['conditions'] ) ) {
			bulkmail( 'conditions' )->render( $_GET['conditions'] );
		}

		?>
	<form id="searchform" action method="get">
		<?php if ( isset( $_GET['post_type'] ) ) : ?>
			<input type="hidden" name="post_type" value="<?php echo esc_attr( $_GET['post_type'] ); ?>">
		<?php endif; ?>
		<?php if ( isset( $_GET['page'] ) ) : ?>
			<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>">
		<?php endif; ?>
		<?php if ( isset( $_GET['paged'] ) ) : ?>
			<input type="hidden" name="_paged" value="<?php echo esc_attr( $_GET['paged'] ); ?>">
		<?php endif; ?>
		<?php if ( isset( $_GET['status'] ) ) : ?>
			<input type="hidden" name="status" value="<?php echo esc_attr( $_GET['status'] ); ?>">
		<?php endif; ?>
		<?php if ( isset( $_GET['lists'] ) ) : ?>
			<?php foreach ( array_filter( (array) $_GET['lists'], 'is_numeric' ) as $list_id ) : ?>
				<input type="hidden" name="lists[]" value="<?php echo $list_id; ?>">
			<?php endforeach ?>
		<?php endif; ?>
	<p class="search-box">
		<label class="screen-reader-text" for="sa-search-input"><?php echo esc_html( $text ); ?></label>
		<input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php echo isset( $_GET['s'] ) ? esc_attr( $_GET['s'] ) : ''; ?>">
		<input type="submit" name="" id="search-submit" class="button" value="<?php echo esc_attr( $text ); ?>">
	</p>
	</form>
		<?php
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_columns() {
		return bulkmail( 'subscribers' )->get_columns();
	}


	/**
	 *
	 *
	 * @param unknown $item
	 * @param unknown $column_name
	 * @return unknown
	 */
	private function searchmark( $string, $search = null ) {

		if ( is_null( $search ) && isset( $_GET['s'] ) ) {
			$search = stripslashes( $_GET['s'] );
		}

		if ( empty( $search ) ) {
			return $string;
		}

		foreach ( explode( ' ', $search ) as $term ) {
			$term   = str_replace( array( '+', '-', '"', '*', '?' ), '', $term );
			$string = preg_replace( '/(' . preg_quote( $term ) . ')/i', '<span class="highlight wp-ui-text-highlight">$1</span>', $string );
		}

		return $string;

	}


	/**
	 *
	 *
	 * @param unknown $item
	 * @param unknown $column_name
	 * @return unknown
	 */
	public function column_default( $item, $column_name ) {

		$data = bulkmail( 'subscribers' )->get_custom_fields( $item->ID );

		switch ( $column_name ) {

			case 'name':
				if ( get_option( 'show_avatars' ) ) {
					$avatar = '<div class="bulkmail-avatar"><a href="' . admin_url( 'edit.php?post_type=newsletter&page=bulkmail_subscribers&ID=' . $item->ID ) . '"><span class="bulkmail-avatar-40' . ( $item->wp_id ? ' wp-user' : '' ) . '" style="background-image:url(' . bulkmail( 'subscribers' )->get_gravatar_uri( $item->email, 80 ) . ')"></span></a></div>';
				} else {
					$avatar = '';
				}

				if ( $data['fullname'] ) {
					$html = '<a class="name" href="' . admin_url( 'edit.php?post_type=newsletter&page=bulkmail_subscribers&ID=' . $item->ID ) . '">' . $this->searchmark( $data['fullname'] ) . '</a><br><a class="email" href="' . admin_url( 'edit.php?post_type=newsletter&page=bulkmail_subscribers&ID=' . $item->ID ) . '" title="' . esc_attr( $item->{'email'} ) . '">' . $this->searchmark( $item->{'email'} ) . '</a>';
				} else {
					$html = '<a class="name" href="' . admin_url( 'edit.php?post_type=newsletter&page=bulkmail_subscribers&ID=' . $item->ID ) . '" title="' . $item->{'email'} . '">' . $this->searchmark( $item->{'email'} ) . '</a><br><span class="email">&nbsp;</span>';
				}

				$stars = ( round( $item->rating / 10, 2 ) * 50 );
				$full  = max( 0, min( 5, floor( $stars ) ) );
				$half  = max( 0, min( 5, round( $stars - $full ) ) );
				$empty = max( 0, min( 5, 5 - $full - $half ) );

				$userrating = '<div class="userrating" title="' . ( $item->rating * 100 ) . '%">'
				. str_repeat( '<span class="bulkmail-icon bulkmail-icon-star"></span>', $full )
				. str_repeat( '<span class="bulkmail-icon bulkmail-icon-star-half"></span>', $half )
				. str_repeat( '<span class="bulkmail-icon bulkmail-icon-star-empty"></span>', $empty ) . '</div>';

				return '<div class="table-data">' . $avatar . '<div class="bulkmail-name">' . $html . $userrating . '</div></div>';

			case 'lists':
				$lists = bulkmail( 'subscribers' )->get_lists( $item->ID );

				$elements = array();

				foreach ( $lists as $i => $list ) {
					$elements[] = '<a href="edit.php?post_type=newsletter&page=bulkmail_lists&ID=' . $list->ID . '" title="' . ( $list->confirmed ? esc_attr__( 'confirmed', 'bulkmail' ) : esc_attr__( 'not confirmed', 'bulkmail' ) ) . '" class="' . ( $list->confirmed ? 'confirmed' : 'not-confirmed' ) . '">' . esc_html( $list->name ) . '</a>';
				}
				return '<div class="table-data">' . implode( ', ', $elements ) . '</div>';

			case 'emails':
				return '<div class="table-data">' . number_format_i18n( bulkmail( 'subscribers' )->get_sent( $item->ID, true ) ) . '</div>';

			case 'status':
				return '<div class="table-data"><span class="nowrap tiny">' . bulkmail( 'subscribers' )->get_status( $item->{$column_name}, true ) . '</span></div>';

			case 'signup':
				$timestring = ( ! $item->{$column_name} ) ? esc_html__( 'unknown', 'bulkmail' ) : date_i18n( bulkmail( 'helper' )->timeformat(), $item->{$column_name} + bulkmail( 'helper' )->gmt_offset( true ) );
				return '<div class="table-data">' . $this->searchmark( $timestring ) . '</div>';

			default:
				$custom_fields = bulkmail()->get_custom_fields();
				if ( in_array( $column_name, array_keys( $custom_fields ) ) ) {

					$value = bulkmail( 'subscribers' )->get_custom_fields( $item->ID, $column_name );

					switch ( $custom_fields[ $column_name ]['type'] ) {
						case 'checkbox':
							return '<div class="table-data">' . ( $value ? '&#10004;' : '&#10005;' ) . '</div>';
						break;
						case 'date':
							return '<div class="table-data">' . $this->searchmark( $value ? date_i18n( bulkmail( 'helper' )->dateformat(), strtotime( $value ) ) : '' ) . '</div>';
						break;
						default:
							return '<div class="table-data">' . $this->searchmark( $value ) . '</div>';
					}
				}
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes
		}
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name'   => array( 'name', false ),
			'status' => array( 'status', false ),
			'signup' => array( 'signup', false ),

		);
		$custom_fields = bulkmail()->get_custom_fields();
		foreach ( $custom_fields as $key => $field ) {
			$sortable_columns[ $key ] = array( $key, false );
		}
		return $sortable_columns;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete'        => esc_html__( 'Delete', 'bulkmail' ),
			'send_campaign' => esc_html__( 'Send new Campaign', 'bulkmail' ),
			'confirmation'  => esc_html__( 'Resend Confirmation', 'bulkmail' ),
			'verify'        => esc_html__( 'Verify', 'bulkmail' ),
		);

		if ( ! current_user_can( 'bulkmail_delete_subscribers' ) ) {
			unset( $actions['delete'] );
		}

		return $actions;
	}


	/**
	 *
	 *
	 * @param unknown $which (optional)
	 */
	public function bulk_actions( $which = '' ) {

		ob_start();
		parent::bulk_actions( $which );
		$actions = ob_get_contents();
		ob_end_clean();

		$status  = '<option value="pending">&#x2514; ' . esc_html__( 'pending', 'bulkmail' ) . '</option>';
		$status .= '<option value="subscribed">&#x2514; ' . esc_html__( 'subscribed', 'bulkmail' ) . '</option>';
		$status .= '<option value="unsubscribed">&#x2514; ' . esc_html__( 'unsubscribed', 'bulkmail' ) . '</option>';

		$actions = str_replace( '</select>', '<optgroup label="' . esc_html__( 'change status', 'bulkmail' ) . '">' . $status . '</optgroup></select>', $actions );

		$lists = bulkmail( 'lists' )->get();

		if ( empty( $lists ) ) {
			echo esc_html($actions);
			return;
		}

		$add       = '';
		$remove    = '';
		$confirm   = '<option value="confirm_list_all">&nbsp;' . esc_html__( 'all', 'bulkmail' ) . '</option>';
		$unconfirm = '<option value="unconfirm_list_all">&nbsp;' . esc_html__( 'all', 'bulkmail' ) . '</option>';
		foreach ( $lists as $list ) {
			$add       .= '<option value="add_list_' . $list->ID . '">' . ( $list->parent_id ? '&nbsp;' : '' ) . '&#x2514; ' . $list->name . '</option>';
			$remove    .= '<option value="remove_list_' . $list->ID . '">' . ( $list->parent_id ? '&nbsp;' : '' ) . '&#x2514; ' . $list->name . '</option>';
			$confirm   .= '<option value="confirm_list_' . $list->ID . '">' . ( $list->parent_id ? '&nbsp;' : '' ) . '&#x2514; ' . $list->name . '</option>';
			$unconfirm .= '<option value="unconfirm_list_' . $list->ID . '">' . ( $list->parent_id ? '&nbsp;' : '' ) . '&#x2514; ' . $list->name . '</option>';
		}

		echo str_replace( '</select>', '<optgroup label="' . esc_html__( 'add to list', 'bulkmail' ) . '">' . $add . '</optgroup><optgroup label="' . esc_html__( 'remove from list', 'bulkmail' ) . '">' . $remove . '</optgroup><optgroup label="' . esc_html__( 'confirm list', 'bulkmail' ) . '">' . $confirm . '</optgroup><optgroup label="' . esc_html__( 'unconfirm list', 'bulkmail' ) . '">' . $unconfirm . '</optgroup></select>', $actions );

	}


	/**
	 *
	 *
	 * @param unknown $which (optional)
	 */
	public function extra_tablenav( $which = '' ) {}


	/**
	 *
	 *
	 * @param unknown $item
	 * @return unknown
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="subscribers[]" value="%s" class="subscriber_cb" />',
			$item->ID
		);
	}


	/**
	 *
	 *
	 * @param unknown $current_mode
	 * @return unknown
	 */
	public function view_switcher( $current_mode ) {
		return '';
	}


	/**
	 *
	 *
	 * @param unknown $domain  (optional)
	 * @param unknown $post_id (optional)
	 */
	public function prepare_items( $domain = null, $post_id = null ) {

		global $wpdb;
		$screen        = get_current_screen();
		$columns       = $this->get_columns();
		$hidden        = get_hidden_columns( $screen );
		$sortable      = $this->get_sortable_columns();
		$custom_fields = bulkmail()->get_custom_fields();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args = array(
			'status'     => isset( $_GET['status'] ) ? (int) $_GET['status'] : false,
			's'          => isset( $_GET['s'] ) ? stripslashes( $_GET['s'] ) : null,
			'strict'     => isset( $_GET['strict'] ) ? boolval( $_GET['strict'] ) : false,
			'lists'      => isset( $_GET['lists'] ) ? ( $_GET['lists'] ) : false,
			'conditions' => isset( $_GET['conditions'] ) ? $_GET['conditions'] : array(),
		);

		// How many to display per page?
		if ( ! ( $this->per_page = (int) get_user_option( 'bulkmail_subscribers_per_page' ) ) ) {
			$this->per_page = 50;
		}

		$offset  = isset( $_GET['paged'] ) ? ( (int) $_GET['paged'] - 1 ) * $this->per_page : 0;
		$orderby = ! empty( $_GET['orderby'] ) ? esc_sql( $_GET['orderby'] ) : 'id';
		$order   = ! empty( $_GET['order'] ) ? esc_sql( $_GET['order'] ) : 'DESC';
		$fields  = array( 'ID', 'email', 'rating', 'wp_id', 'status', 'signup' );
		$since   = ! empty( $_GET['since'] ) ? strtotime( $_GET['since'] ) : null;

		if ( isset( $custom_fields[ $orderby ] ) ) {
			$fields[] = $orderby;
		}

		if ( $since ) {
			$args['conditions'][] = array(
				'field'    => 'signup',
				'operator' => '>',
				'value'    => $since,
			);
		}

		switch ( $orderby ) {
			case 'name':
			case 'lastname':
				$orderby  = array( 'lastname', 'firstname' );
				$fields[] = 'fullname';
				break;
			case 'firstname':
				$orderby  = array( 'firstname', 'lastname' );
				$fields[] = 'fullname';
				break;
		}

		$items = bulkmail( 'subscribers' )->query(
			wp_parse_args(
				$args,
				array(
					'calc_found_rows' => true,
					'orderby'         => $orderby,
					'order'           => $order,
					'fields'          => $fields,
					'limit'           => $this->per_page,
					'offset'          => $offset,
				)
			)
		);

		$this->items       = $items;
		$this->total_items = $wpdb->get_var( 'SELECT FOUND_ROWS();' );

		$item_ids = wp_list_pluck( $this->items, 'ID' );

		bulkmail( 'actions' )->get_by_subscriber( $item_ids );

		$this->total_pages = ceil( $this->total_items / $this->per_page );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_items,
				'total_pages' => $this->total_pages,
				'per_page'    => $this->per_page,
			)
		);

	}


}
