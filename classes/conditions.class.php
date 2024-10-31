<?php

class BulkmailConditions {

	public function __construct( $conditions = array() ) {

	}


	public function __get( $name ) {

		if ( ! isset( $this->$name ) ) {
			$this->{$name} = $this->{'get_' . $name}();
		}

		return $this->{$name};

	}


	public function view( $conditions = array(), $inputname = null ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'bulkmail-conditions', BULKEMAIL_URI . 'assets/css/conditions-style' . $suffix . '.css', array(), BULKEMAIL_VERSION );
		wp_enqueue_script( 'bulkmail-conditions', BULKEMAIL_URI . 'assets/js/conditions-script' . $suffix . '.js', array( 'jquery' ), BULKEMAIL_VERSION, true );

		if ( is_null( $inputname ) ) {
			$inputname = 'bulkmail_data[conditions]';
		}

		if ( empty( $conditions ) ) {
			$conditions = array();
		}

		include BULKEMAIL_DIR . 'views/conditions/conditions.php';

	}

	public function render( $conditions = array(), $echo = true, $plain = false ) {

		if ( empty( $conditions ) ) {
			$conditions = array();
		}

		ob_start();
		include BULKEMAIL_DIR . 'views/conditions/render.php';
		$output = ob_get_contents();
		ob_end_clean();

		// remove any whitespace so :empty selector works
		if ( empty( $conditions ) ) {
			$output = preg_replace( '/>(\s+)<\//', '></', $output );
		}

		if ( $plain ) {
			$output = trim( strip_tags( $output ) );
			$output = preg_replace( '/\s*$^\s*/mu', "\n\n", $output );
			$output = preg_replace( '/[ \t]+/u', ' ', $output );
		}

		if ( ! $echo ) {
			return $output;
		}

		echo $output;

	}

	public function fielddropdown() {
		include BULKEMAIL_DIR . 'views/conditions/fielddropdown.php';
	}
	public function operatordropdown() {
		include BULKEMAIL_DIR . 'views/conditions/operatordropdown.php';
	}

	private function get_custom_fields() {
		$custom_fields = bulkmail()->get_custom_fields();
		$custom_fields = wp_parse_args(
			(array) $custom_fields,
			array(
				'email'     => array( 'name' => bulkmail_text( 'email' ) ),
				'firstname' => array( 'name' => bulkmail_text( 'firstname' ) ),
				'lastname'  => array( 'name' => bulkmail_text( 'lastname' ) ),
				'rating'    => array( 'name' => esc_html__( 'Rating', 'bulkmail' ) ),
			)
		);

		return $custom_fields;
	}

	private function get_custom_date_fields() {
		$custom_date_fields = bulkmail()->get_custom_date_fields( true );

		return $custom_date_fields;
	}

	private function get_fields() {
		$fields = array(
			'id'         => esc_html__( 'ID', 'bulkmail' ),
			'hash'       => esc_html__( 'Hash', 'bulkmail' ),
			'email'      => esc_html__( 'Email', 'bulkmail' ),
			'wp_id'      => esc_html__( 'WordPress User ID', 'bulkmail' ),
			'added'      => esc_html__( 'Added', 'bulkmail' ),
			'updated'    => esc_html__( 'Updated', 'bulkmail' ),
			'signup'     => esc_html__( 'Signup', 'bulkmail' ),
			'confirm'    => esc_html__( 'Confirm', 'bulkmail' ),
			'ip_signup'  => esc_html__( 'IP on Signup', 'bulkmail' ),
			'ip_confirm' => esc_html__( 'IP on confirmation', 'bulkmail' ),
		);

		return $fields;
	}

	private function get_time_fields() {
		$time_fields = array( 'added', 'updated', 'signup', 'confirm', 'gdpr' );
		$time_fields = array_merge( $time_fields, $this->custom_date_fields );

		return $time_fields;
	}

	private function get_meta_fields() {
		$meta_fields = array(
			'form'       => esc_html__( 'Form', 'bulkmail' ),
			'referer'    => esc_html__( 'Referer', 'bulkmail' ),
			'client'     => esc_html__( 'Client', 'bulkmail' ),
			'clienttype' => esc_html__( 'Clienttype', 'bulkmail' ),
			'geo'        => esc_html__( 'Location', 'bulkmail' ),
			'lang'       => esc_html__( 'Language', 'bulkmail' ),
			'gdpr'       => esc_html__( 'GDPR Consent given', 'bulkmail' ),
		);

		return $meta_fields;
	}

	private function get_wp_user_meta() {
		$wp_user_meta = wp_parse_args(
			bulkmail( 'helper' )->get_wpuser_meta_fields(),
			array(
				'wp_user_level'   => esc_html__( 'User Level', 'bulkmail' ),
				'wp_capabilities' => esc_html__( 'User Role', 'bulkmail' ),
			)
		);
		// removing custom fields from wp user meta to prevent conflicts
		$wp_user_meta = array_diff( $wp_user_meta, array_merge( array( 'email' ), array_keys( $this->custom_fields ) ) );

		return $wp_user_meta;
	}

	private function get_campaign_related() {
		return array(
			'_sent'               => esc_html__( 'has received', 'bulkmail' ),
			'_sent__not_in'       => esc_html__( 'has not received', 'bulkmail' ),
			'_open'               => esc_html__( 'has received and opened', 'bulkmail' ),
			'_open__not_in'       => esc_html__( 'has received but not opened', 'bulkmail' ),
			'_click'              => esc_html__( 'has received and clicked', 'bulkmail' ),
			'_click__not_in'      => esc_html__( 'has received and not clicked', 'bulkmail' ),
			'_click_link'         => esc_html__( 'clicked link', 'bulkmail' ),
			'_click_link__not_in' => esc_html__( 'didn\'t clicked link', 'bulkmail' ),
		);

	}
	private function get_list_related() {
		return array(
			'_lists__in'     => esc_html__( 'is in List', 'bulkmail' ),
			'_lists__not_in' => esc_html__( 'is not in List', 'bulkmail' ),
		);

	}
	private function get_operators() {
		return array(
			'is'               => esc_html__( 'is', 'bulkmail' ),
			'is_not'           => esc_html__( 'is not', 'bulkmail' ),
			'contains'         => esc_html__( 'contains', 'bulkmail' ),
			'contains_not'     => esc_html__( 'contains not', 'bulkmail' ),
			'begin_with'       => esc_html__( 'begins with', 'bulkmail' ),
			'end_with'         => esc_html__( 'ends with', 'bulkmail' ),
			'is_greater'       => esc_html__( 'is greater', 'bulkmail' ),
			'is_smaller'       => esc_html__( 'is smaller', 'bulkmail' ),
			'is_greater_equal' => esc_html__( 'is greater or equal', 'bulkmail' ),
			'is_smaller_equal' => esc_html__( 'is smaller or equal', 'bulkmail' ),
			'pattern'          => esc_html__( 'match regex pattern', 'bulkmail' ),
			'not_pattern'      => esc_html__( 'does not match regex pattern', 'bulkmail' ),
		);

	}
	private function get_simple_operators() {
		return array(
			'is'               => esc_html__( 'is', 'bulkmail' ),
			'is_not'           => esc_html__( 'is not', 'bulkmail' ),
			'is_greater'       => esc_html__( 'is greater', 'bulkmail' ),
			'is_smaller'       => esc_html__( 'is smaller', 'bulkmail' ),
			'is_greater_equal' => esc_html__( 'is greater or equal', 'bulkmail' ),
			'is_smaller_equal' => esc_html__( 'is smaller or equal', 'bulkmail' ),
		);

	}
	private function get_string_operators() {
		return array(
			'is'           => esc_html__( 'is', 'bulkmail' ),
			'is_not'       => esc_html__( 'is not', 'bulkmail' ),
			'contains'     => esc_html__( 'contains', 'bulkmail' ),
			'contains_not' => esc_html__( 'contains not', 'bulkmail' ),
			'begin_with'   => esc_html__( 'begins with', 'bulkmail' ),
			'end_with'     => esc_html__( 'ends with', 'bulkmail' ),
			'pattern'      => esc_html__( 'match regex pattern', 'bulkmail' ),
			'not_pattern'  => esc_html__( 'does not match regex pattern', 'bulkmail' ),
		);

	}
	private function get_bool_operators() {
		return array(
			'is'     => esc_html__( 'is', 'bulkmail' ),
			'is_not' => esc_html__( 'is not', 'bulkmail' ),
		);

	}
	private function get_date_operators() {
		return array(
			'is'               => esc_html__( 'is on the', 'bulkmail' ),
			'is_not'           => esc_html__( 'is not on the', 'bulkmail' ),
			'is_greater'       => esc_html__( 'is after', 'bulkmail' ),
			'is_smaller'       => esc_html__( 'is before', 'bulkmail' ),
			'is_greater_equal' => esc_html__( 'is after or on the', 'bulkmail' ),
			'is_smaller_equal' => esc_html__( 'is before or on the', 'bulkmail' ),
		);

	}
	private function get_special_campaigns() {
		return array(
			'_last_5'       => esc_html__( 'Any of the Last 5 Campaigns', 'bulkmail' ),
			'_last_7day'    => esc_html__( 'Any Campaigns within the last 7 days', 'bulkmail' ),
			'_last_1month'  => esc_html__( 'Any Campaigns within the last 1 month', 'bulkmail' ),
			'_last_3month'  => esc_html__( 'Any Campaigns within the last 3 months', 'bulkmail' ),
			'_last_6month'  => esc_html__( 'Any Campaigns within the last 6 months', 'bulkmail' ),
			'_last_12month' => esc_html__( 'Any Campaigns within the last 12 months', 'bulkmail' ),
		);

	}
	private function get_field_operator( $operator ) {
		$operator = esc_sql( stripslashes( $operator ) );

		switch ( $operator ) {
			case '=':
				return 'is';
			case '!=':
				return 'is_not';
			case '<>':
				return 'contains';
			case '!<>':
				return 'contains_not';
			case '^':
				return 'begin_with';
			case '$':
				return 'end_with';
			case '>=':
				return 'is_greater_equal';
			case '<=':
				return 'is_smaller_equal';
			case '>':
				return 'is_greater';
			case '<':
				return 'is_smaller';
			case '%':
				return 'pattern';
			case '!%':
				return 'not_pattern';
		}

		return $operator;

	}


	private function print_condition( $condition, $formated = true ) {

		$field    = isset( $condition['field'] ) ? $condition['field'] : $condition[0];
		$operator = isset( $condition['operator'] ) ? $condition['operator'] : $condition[1];
		$value    = stripslashes_deep( isset( $condition['value'] ) ? $condition['value'] : $condition[2] );

		$return        = array(
			'field'    => '<strong>' . $this->nice_name( $field, 'field', $field ) . '</strong>',
			'operator' => '',
			'value'    => '',
		);
		$opening_quote = esc_html_x( '&#8220;', 'opening curly double quote', 'bulkmail' );
		$closing_quote = esc_html_x( '&#8221;', 'closing curly double quote', 'bulkmail' );

		if ( isset( $this->campaign_related[ $field ] ) ) {
			$special_campaign_keys = array_keys( $this->special_campaigns );
			if ( ! is_array( $value ) ) {
				$value = array( $value );
			}
			$urls      = array();
			$campagins = array();
			if ( strpos( $field, '_click_link' ) !== false ) {
				foreach ( $value as $k => $v ) {
					if ( is_numeric( $v ) || in_array( $v, $special_campaign_keys ) ) {
						$campagins[] = $v;
					} else {
						$urls[] = $v;
					}
				}
				$return['value'] = implode( ' ' . esc_html__( 'or', 'bulkmail' ) . ' ', array_map( 'esc_url', $urls ) );
				if ( ! empty( $campagins ) ) {
					$return['value'] .= '<br> ' . esc_html__( 'in', 'bulkmail' ) . ' ' . $opening_quote . implode( $closing_quote . ' ' . esc_html__( 'or', 'bulkmail' ) . ' ' . $opening_quote, array_map( array( $this, 'get_campaign_title' ), $campagins ) ) . $closing_quote;
				}
			} else {
				$return['value'] = $opening_quote . implode( $closing_quote . ' ' . esc_html__( 'or', 'bulkmail' ) . ' ' . $opening_quote, array_map( array( $this, 'get_campaign_title' ), $value ) ) . $closing_quote;
			}
		} elseif ( isset( $this->list_related[ $field ] ) ) {
			if ( ! is_array( $value ) ) {
				$value = array( $value );
			}
			$return['value'] = $opening_quote . implode( $closing_quote . ' ' . esc_html__( 'or', 'bulkmail' ) . ' ' . $opening_quote, array_map( array( $this, 'get_list_title' ), $value ) ) . $closing_quote;
		} elseif ( 'geo' == $field ) {
			if ( ! is_array( $value ) ) {
				$value = array( $value );
			}
			$return['operator'] = '<em>' . $this->nice_name( $operator, 'operator', $field ) . '</em>';
			$return['value']    = $opening_quote . implode( $closing_quote . ' ' . esc_html__( 'or', 'bulkmail' ) . ' ' . $opening_quote, array_map( array( $this, 'get_country_name' ), $value ) ) . $closing_quote;
		} elseif ( 'rating' == $field ) {
			$stars              = ( round( $this->sanitize_rating( $value ) / 10, 2 ) * 50 );
			$full               = max( 0, min( 5, floor( $stars ) ) );
			$half               = max( 0, min( 5, round( $stars - $full ) ) );
			$empty              = max( 0, min( 5, 5 - $full - $half ) );
			$return['operator'] = '<em>' . $this->nice_name( $operator, 'operator', $field ) . '</em>';
			$return['value']    = '<span class="screen-reader-text">' . sprintf( esc_html__( '%d stars', 'bulkmail' ), $full ) . '</span>'
			. str_repeat( '<span class="bulkmail-icon bulkmail-icon-star"></span>', $full )
			. str_repeat( '<span class="bulkmail-icon bulkmail-icon-star-half"></span>', $half )
			. str_repeat( '<span class="bulkmail-icon bulkmail-icon-star-empty"></span>', $empty );

		} else {
			$return['operator'] = '<em>' . $this->nice_name( $operator, 'operator', $field ) . '</em>';
			$return['value']    = $opening_quote . '<strong>' . $this->nice_name( $value, 'value', $field ) . '</strong>' . $closing_quote;
		}

		return $formated ? $return : strip_tags( $return );

	}


	private function sanitize_rating( $value ) {
		if ( ! $value || ! (float) $value ) {
			return 0;
		}
		$value = str_replace( ',', '.', $value );
		if ( strpos( $value, '%' ) !== false || $value > 5 ) {
			$value = (float) $value / 100;
		} elseif ( $value > 1 ) {
			$value = (float) $value * 0.2;
		}
		return $value;
	}

	public function get_campaign_title( $post ) {

		if ( ! $post ) {
			return esc_html__( 'Any Campaign', 'bulkmail' );
		}

		if ( isset( $this->special_campaigns[ $post ] ) ) {
			return $this->special_campaigns[ $post ];
		}

		$title = get_the_title( $post );
		if ( empty( $title ) ) {
			$title = '#' . $post;
		}
		return $title;
	}

	public function get_list_title( $list_id ) {

		if ( $list = bulkmail( 'lists' )->get( $list_id ) ) {
			return $list->name;
		}
		return $list_id;
	}

	public function get_country_name( $code ) {

		return bulkmail( 'geo' )->code2Country( $code );
	}


	private function nice_name( $string, $type = null, $field = null ) {

		switch ( $type ) {
			case 'field':
				if ( isset( $this->fields[ $string ] ) ) {
					return $this->fields[ $string ];
				}
				if ( isset( $this->custom_fields[ $string ] ) ) {
					return $this->custom_fields[ $string ]['name'];
				}
				if ( isset( $this->campaign_related[ $string ] ) ) {
					return $this->campaign_related[ $string ];
				}
				if ( isset( $this->list_related[ $string ] ) ) {
					return $this->list_related[ $string ];
				}
				if ( isset( $this->meta_fields[ $string ] ) ) {
					return $this->meta_fields[ $string ];
				}
				if ( isset( $this->wp_user_meta[ $string ] ) ) {
					return $this->wp_user_meta[ $string ];
				}
				break;
			case 'operator':
				if ( in_array( $field, $this->time_fields ) && isset( $this->date_operators[ $string ] ) ) {
					return $this->date_operators[ $string ];
				}
				if ( isset( $this->operators[ $string ] ) ) {
					return $this->operators[ $string ];
				}
				if ( 'AND' == $string ) {
					return esc_html__( 'and', 'bulkmail' );
				}
				if ( 'OR' == $string ) {
					return esc_html__( 'or', 'bulkmail' );
				}
				break;
			case 'value':
				if ( in_array( $field, $this->time_fields ) ) {
					if ( $string ) {
						return date( bulkmail( 'helper' )->dateformat(), strtotime( $string ) );
					} else {
						return '';
					}
				}
				if ( 'form' == $field ) {
					if ( $form = bulkmail( 'forms' )->get( (int) $string, false, false ) ) {
						return $form->name;
					}
				} elseif ( 'wp_capabilities' == $field ) {
					global $wp_roles;
					if ( isset( $wp_roles->roles[ $string ] ) ) {
						return translate_user_role( $wp_roles->roles[ $string ]['name'] );
					}
				}

				break;

		}

		return $string;

	}

}
