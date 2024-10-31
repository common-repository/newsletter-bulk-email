<?php

class BulkmailPrivacy {

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'init' ) );
		add_action( 'save_post', array( &$this, 'maybe_update_privacy_link' ), 10, 3 );

	}


	public function init() {

		add_action( 'wp_privacy_personal_data_exporters', array( &$this, 'register_exporter' ) );
		add_action( 'wp_privacy_personal_data_erasers', array( &$this, 'register_eraser' ) );
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			wp_add_privacy_policy_content( 'Bulkmail', $this->privacy_content() );
		}

	}

	public function privacy_content() {

		$content  =
			'<h2 class="privacy-policy-tutorial">' . esc_html__( 'What data Bulkmail collects from your subscribers', 'bulkmail' ) . '</h2>';
		$content .=
			'<h3 class="wp-policy-help">' . esc_html__( 'Newsletter', 'bulkmail' ) . '</h3>';
		$content .=
			'<p class="wp-policy-help">' . esc_html__( 'If you have signed up for our newsletter you may receive emails from us. This includes but not limited to transactional emails and marketing emails.', 'bulkmail' ) . '</p>';
		$content .=
			'<p class="wp-policy-help">' . esc_html__( 'We\'ll only send emails which you have explicitly or implicitly (registration, product purchase etc.) signed up to.', 'bulkmail' ) . '</p>';

		$tracked_fields = array(
			esc_html__( 'your email address', 'bulkmail' ),
			esc_html__( 'your name', 'bulkmail' ),
		);

		if ( $custom_fields = bulkmail()->get_custom_fields() ) {
			$custom_fields  = wp_list_pluck( $custom_fields, 'name' );
			$tracked_fields = array_merge( $tracked_fields, $custom_fields );
		}

		if ( bulkmail_option( 'track_location' ) ) {
			$tracked_fields[] = esc_html__( 'your current location', 'bulkmail' );
		}
		if ( bulkmail_option( 'track_users' ) ) {
			$tracked_fields[] = esc_html__( 'your current IP address and timestamp of signup', 'bulkmail' );
			$tracked_fields[] = esc_html__( 'your IP address and timestamp when you have confirmed your subscription', 'bulkmail' );
		}

		$content .=
			'<p class="wp-policy-help">' . sprintf( esc_html__( 'On signup we collect %s and the current web address were you sign up.', 'bulkmail' ), implode( ', ', $tracked_fields ) ) . '</p>';
		$content .=
			'<p class="wp-policy-help">' . esc_html__( 'We send our emails via', 'bulkmail' ) . ' ';

		if ( 'simple' == ( $deliverymethod = bulkmail_option( 'deliverymethod' ) ) ) {
			$content .= esc_html__( 'our own server.', 'bulkmail' );
		} elseif ( 'smtp' == $deliverymethod ) {
			$content .= sprintf( esc_html__( 'via SMTP host %s', 'bulkmail' ), bulkmail_option( 'smtp_host' ) );
		} elseif ( 'gmail' == $deliverymethod ) {
			$content .= sprintf( esc_html__( 'a service called %s', 'bulkmail' ), 'Gmail by Google' );
		} else {
			$content .= sprintf( esc_html__( 'a service called %s', 'bulkmail' ), $deliverymethod );
		}
		$content .=
			'</p>';

		$tracking = array();

		if ( bulkmail_option( 'track_opens' ) ) {
			$tracking[] = esc_html__( 'if you open the email in your email client', 'bulkmail' );
		}
		if ( bulkmail_option( 'track_clicks' ) ) {
			$tracking[] = esc_html__( 'if you click a link in the email', 'bulkmail' );
		}
		if ( bulkmail_option( 'track_location' ) ) {
			$tracking[] = esc_html__( 'your current location', 'bulkmail' );
		}
		if ( bulkmail_option( 'track_users' ) ) {
			$tracking[] = esc_html__( 'your current IP address', 'bulkmail' );
		}

		if ( ! empty( $tracking ) ) {
			$content .=
				'<p class="wp-policy-help">' . sprintf( esc_html__( 'Once you get an email from us we track %s.', 'bulkmail' ), implode( ', ', $tracking ) ) . '</p>';
		}

		if ( bulkmail_option( 'do_not_track' ) ) {
			$content .= '<p class="wp-policy-help">' . esc_html__( 'We respect your browsers "Do Not Track" feature which means we do not track your interaction with our emails.', 'bulkmail' ) . '</p>';
		}

		return apply_filters( 'bulkmail_privacy_content', $content );
	}

	public function register_exporter( $exporters ) {
		$exporters['bulkmail-exporter'] = array(
			'exporter_friendly_name' => esc_html__( 'Bulkmail Data', 'bulkmail' ),
			'callback'               => array( &$this, 'data_export' ),
		);
		return $exporters;
	}

	public function register_eraser( $eraser ) {
		$eraser['bulkmail-eraser'] = array(
			'eraser_friendly_name' => esc_html__( 'Bulkmail Data', 'bulkmail' ),
			'callback'             => array( &$this, 'data_erase' ),
		);
		return $eraser;
	}

	public function data_export( $email_address, $page = 1 ) {

		$export_items = array();

		if ( $subscriber = bulkmail( 'subscribers' )->get_by_mail( $email_address, true ) ) {

			$meta = bulkmail( 'subscribers' )->meta( $subscriber->ID );

			$data = array();

			// general data
			foreach ( $subscriber as $key => $value ) {
				if ( empty( $value ) ) {
					continue;
				}
				if ( in_array( $key, array( 'added', 'updated', 'signup', 'confirm' ) ) ) {
					$value = bulkmail( 'helper' )->do_timestamp( $value );
				}
				$data[] = array(
					'name'  => $key,
					'value' => $value,
				);
			}

			// meta data
			foreach ( $meta as $key => $value ) {
				if ( empty( $value ) ) {
					continue;
				}
				$data[] = array(
					'name'  => $key,
					'value' => $value,
				);
			}

			$export_items[] = array(
				'group_id'    => 'bulkmail',
				'group_label' => 'Bulkmail',
				'item_id'     => 'bulkmail-' . $subscriber->ID,
				'data'        => $data,
			);

			if ( $lists = bulkmail( 'subscribers' )->get_lists( $subscriber->ID ) ) {

				$data = array();
				// lists
				foreach ( $lists as $key => $value ) {
					$data[] = array(
						'name'  => esc_html__( 'List Name', 'bulkmail' ),
						'value' => $value->name,
					);
					$data[] = array(
						'name'  => esc_html__( 'Description', 'bulkmail' ),
						'value' => $value->description,
					);
					$data[] = array(
						'name'  => esc_html__( 'Added', 'bulkmail' ),
						'value' => bulkmail( 'helper' )->do_timestamp( $value->added ),
					);
					$data[] = array(
						'name'  => esc_html__( 'Confirmed', 'bulkmail' ),
						'value' => bulkmail( 'helper' )->do_timestamp( $value->confirmed ),
					);
				}

				$export_items[] = array(
					'group_id'    => 'bulkmail_lists',
					'group_label' => 'Bulkmail Lists',
					'item_id'     => 'bulkmail-lists-' . $subscriber->ID,
					'data'        => $data,
				);

			}

			if ( $activity = bulkmail( 'actions' )->get_activity( null, $subscriber->ID ) ) {
				$data      = array();
				$campaigns = array();

				// activity
				foreach ( $activity as $key => $value ) {

					if ( ! isset( $campaigns[ $value->campaign_id ] ) ) {
						$campaigns[ $value->campaign_id ] = array(
							'group_id'    => 'bulkmail_campaign_' . $value->campaign_id,
							'group_label' => 'Bulkmail Campaign "' . $value->campaign_title . '"',
							'item_id'     => 'bulkmail-campaign-' . $value->campaign_id,
							'data'        => array(),
						);
					}

					switch ( $value->type ) {
						case 1: // sent
							$campaigns[ $value->campaign_id ]['data'][] = array(
								'name'  => esc_html__( 'Sent', 'bulkmail' ),
								'value' => bulkmail( 'helper' )->do_timestamp( $value->timestamp ),
							);
							break;
						case 2: // opened
							$campaigns[ $value->campaign_id ]['data'][] = array(
								'name'  => esc_html__( 'Opened', 'bulkmail' ),
								'value' => bulkmail( 'helper' )->do_timestamp( $value->timestamp ),
							);
							break;
						case 3:  // clicked
							$campaigns[ $value->campaign_id ]['data'][] = array(
								'name'  => esc_html__( 'Clicked', 'bulkmail' ),
								'value' => bulkmail( 'helper' )->do_timestamp( $value->timestamp ) . ' (' . $value->link . ')',
							);
							break;
						case 4:  // clicked
							$campaigns[ $value->campaign_id ]['data'][] = array(
								'name'  => esc_html__( 'Unsubscribe', 'bulkmail' ),
								'value' => bulkmail( 'helper' )->do_timestamp( $value->timestamp ),
							);
							break;

					}
				}

				$export_items = array_merge( $export_items, array_values( $campaigns ) );

			}
		}

		return array(
			'data' => $export_items,
			'done' => true,
		);

	}

	public function data_erase( $email_address, $page = 1 ) {

		if ( empty( $email_address ) ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		$subscriber = bulkmail( 'subscribers' )->get_by_mail( $email_address );

		if ( ! $subscriber ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		$messages       = array();
		$items_removed  = false;
		$items_retained = false;

		if ( bulkmail( 'subscribers' )->remove( $subscriber->ID ) ) {
			$items_removed = true;
		} else {
			$items_retained = false;
		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => true,
		);
	}

	public function maybe_update_privacy_link( $post_id, $post, $update = null ) {

		// only on update
		if ( ! $update || ! $post_id || ! $post ) {
			return;
		}

		if ( $privacy_policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' ) ) {
			if ( $post_id == $privacy_policy_page_id ) {
				$link      = get_permalink( $post_id );
				$gdpr_link = bulkmail_option( 'gdpr_link' );
				if ( $gdpr_link && $link != $gdpr_link ) {
					if ( bulkmail_update_option( 'gdpr_link', $link ) && bulkmail_option( 'gdpr_forms' ) ) {
						bulkmail_notice( '[Bulkmail] ' . sprintf( esc_html__( 'The Privacy page link has been changed to %s', 'bulkmail' ), '<em>' . $link . '</em>' ), 'info', true );
					}
				}
			}
		}

	}

}
