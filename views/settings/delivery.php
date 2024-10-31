<table class="form-table">
	<tr valign="top" class="settings-row settings-row-number-of-mails-sent">
		<th scope="row"><?php esc_html_e( 'Number of mails sent', 'bulkmail' ); ?></th>
		<td><p><?php printf( esc_html__( 'Send max %1$s emails at once and max %2$s within %3$s hours', 'bulkmail' ), '<input type="text" name="bulkmail_options[send_at_once]" value="' . bulkmail_option( 'send_at_once' ) . '" class="small-text">', '<input type="text" name="bulkmail_options[send_limit]" value="' . bulkmail_option( 'send_limit' ) . '" class="small-text">', '<input type="text" name="bulkmail_options[send_period]" value="' . bulkmail_option( 'send_period' ) . '" class="small-text">' ); ?></p>
		<p class="description"><?php esc_html_e( 'Depending on your hosting provider you can increase these values', 'bulkmail' ); ?></p>
		<?php
		global $wp_locale;

		$sent_this_period = get_transient( '_bulkmail_send_period', 0 );
		$mails_left       = max( 0, bulkmail_option( 'send_limit' ) - $sent_this_period );
		$next_reset       = get_option( '_transient_timeout__bulkmail_send_period_timeout' );
		$timeoffset       = bulkmail( 'helper' )->gmt_offset( true );
		$timestamp        = current_time( 'timestamp' );

		if ( ! $next_reset || $next_reset < time() ) {
			$next_reset = time() + bulkmail_option( 'send_period' ) * 3600;
			$mails_left = bulkmail_option( 'send_limit' );
		}
		?>

	<p class="description"><?php printf( esc_html__( 'You can still send %1$s mails within the next %2$s', 'bulkmail' ), '<strong>' . number_format_i18n( $mails_left ) . '</strong>', '<strong title="' . date_i18n( $timeformat, $next_reset + $timeoffset, true ) . '">' . human_time_diff( $next_reset ) . '</strong>' ); ?> &ndash; <a href="edit.php?post_type=newsletter&page=bulkmail_settings&reset-limits=1&_wpnonce=<?php echo wp_create_nonce( 'bulkmail-reset-limits' ); ?>"><?php esc_html_e( 'reset these limits', 'bulkmail' ); ?></a></p>

	</tr>
	<tr valign="top" class="settings-row settings-row-time-frame">
		<th scope="row"><?php esc_html_e( 'Time Frame', 'bulkmail' ); ?><br>
		<p class="howto"><?php printf( esc_html__( 'It\'s %1$s, %2$s', 'bulkmail' ), $wp_locale->weekday[ date( 'w', $timestamp ) ], date( 'H:i', $timestamp ) ); ?><br>
		<?php esc_html_e( 'Status', 'bulkmail' ); ?> : <?php bulkmail( 'helper' )->in_timeframe() ? esc_html_e( 'active', 'bulkmail' ) : esc_html_e( 'paused', 'bulkmail' ); ?></p>
		</th>
		<td><p><?php esc_html_e( 'send mails only between', 'bulkmail' ); ?>
			<?php $selected = bulkmail_option( 'time_frame_from' ); ?>
			<select name="bulkmail_options[time_frame_from]">
			<?php for ( $i = 0; $i < 24; $i++ ) : ?>
				<option value="<?php echo $i; ?>" <?php selected( $selected, $i ); ?>><?php echo ( $i < 10 ) ? '0' . $i : $i; ?>:00</option>
			<?php endfor; ?>
			</select>
			<?php esc_html_e( 'and', 'bulkmail' ); ?>
			<?php $selected = bulkmail_option( 'time_frame_to' ); ?>
			<select name="bulkmail_options[time_frame_to]">
			<?php for ( $i = 0; $i < 24; $i++ ) : ?>
				<option value="<?php echo $i; ?>" <?php selected( $selected, $i ); ?>><?php echo ( $i < 10 ) ? '0' . $i : $i; ?>:00</option>
			<?php endfor; ?>
			</select>
			 <span class="utcoffset"><?php echo ( ( $timeoffset > 0 ) ? 'UTC + ' . ( $timeoffset / 3600 ) : '' ); ?></span></p>
			<p><?php esc_html_e( 'only on', 'bulkmail' ); ?>
			<?php
			$start_at       = get_option( 'start_of_week' );
			$time_frame_day = bulkmail_option( 'time_frame_day', array() );

			for ( $i = $start_at; $i < 7 + $start_at; $i++ ) {
				$j = $i;
				if ( ! isset( $wp_locale->weekday[ $j ] ) ) {
					$j = $j - 7;
				}

				echo '<label title="' . $wp_locale->weekday[ $j ] . '" class="weekday"><input name="bulkmail_options[time_frame_day][]" type="checkbox" value="' . $j . '" ' . checked( ( in_array( $j, $time_frame_day ) || ! $time_frame_day ), true, false ) . '>' . $wp_locale->weekday[ $j ] . '&nbsp;</label> ';
			}
			?>
			</p>
			<p class="description"><?php esc_html_e( 'Only affects Campaigns and Auto responders but not transactional emails.', 'bulkmail' ); ?></p>
	</tr>
	<tr valign="top" class="settings-row settings-row-split-campaigns">
		<th scope="row"><?php esc_html_e( 'Split campaigns', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[split_campaigns]" value=""><input type="checkbox" name="bulkmail_options[split_campaigns]" value="1" <?php checked( bulkmail_option( 'split_campaigns' ) ); ?>> <?php esc_html_e( 'send campaigns simultaneously instead of one after the other', 'bulkmail' ); ?></label> </td>
	</tr>
	<tr valign="top" class="settings-row settings-row-pause-campaigns">
		<th scope="row"><?php esc_html_e( 'Pause campaigns', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[pause_campaigns]" value=""><input type="checkbox" name="bulkmail_options[pause_campaigns]" value="1" <?php checked( bulkmail_option( 'pause_campaigns' ) ); ?>> <?php esc_html_e( 'pause campaigns if an error occurs', 'bulkmail' ); ?></label><p class="description"><?php esc_html_e( 'Bulkmail will change the status to "pause" if an error occur otherwise it tries to finish the campaign', 'bulkmail' ); ?></p></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-time-between-mails">
		<th scope="row"><?php esc_html_e( 'Time between mails', 'bulkmail' ); ?></th>
		<td><p><input type="text" name="bulkmail_options[send_delay]" value="<?php echo bulkmail_option( 'send_delay' ); ?>" class="small-text"> <?php esc_html_e( 'milliseconds', 'bulkmail' ); ?></p><p class="description"><?php esc_html_e( 'define a delay between mails in milliseconds if you have problems with sending two many mails at once', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-max-execution-time">
		<th scope="row"><?php esc_html_e( 'Max. Execution Time', 'bulkmail' ); ?></th>
		<td><p><input type="text" name="bulkmail_options[max_execution_time]" value="<?php echo bulkmail_option( 'max_execution_time', 0 ); ?>" class="small-text"> <?php esc_html_e( 'seconds', 'bulkmail' ); ?></p><p class="description"><?php esc_html_e( 'define a maximum execution time to prevent server timeouts. If set to zero, no time limit is imposed.', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-send-test">
		<th scope="row"><?php esc_html_e( 'Send Test', 'bulkmail' ); ?></th>
		<td>
		<div class="bulkmail-testmail">
			<input type="text" value="<?php echo esc_attr( $test_email ); ?>" autocomplete="off" class="form-input-tip bulkmail-testmail-email">
			<input type="button" value="<?php esc_attr_e( 'Send Test', 'bulkmail' ); ?>" class="button bulkmail_sendtest" data-role="basic">
			<div class="loading test-ajax-loading"></div>
		</div>
		</td>
	</tr>
</table>

	<?php

	$deliverymethods = array(
		'simple' => esc_html__( 'Simple', 'bulkmail' ),
		'smtp'   => 'SMTP',
	);
	$deliverymethods = apply_filters( 'mymail_delivery_methods', apply_filters( 'bulkmail_delivery_methods', $deliverymethods ) );

	$method = bulkmail_option( 'deliverymethod', 'simple' );

	?>

<h3><?php esc_html_e( 'Delivery Method', 'bulkmail' ); ?></h3>
<div class="updated inline"><p><?php printf( esc_html__( 'You are currently sending with the %s delivery method', 'bulkmail' ), '<strong>' . $deliverymethods[ $method ] . '</strong>' ); ?></p></div>

<div id="deliverynav" class="nav-tab-wrapper hide-if-no-js">
<?php
foreach ( $deliverymethods as $id => $name ) {

	$classes = array( 'nav-tab' );
	if ( $method == $id ) {
		$classes[] = 'nav-tab-active';
	}

	?>
	<a class="<?php echo implode( ' ', $classes ); ?>" href="#<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $name ); ?></a>
	<?php } ?>
	<a href="https://emailmarketing.run/delivery-methods.html" class="alignright"><?php esc_html_e( 'search for more delivery methods', 'bulkmail' ); ?></a>
</div>

<input type="hidden" name="bulkmail_options[deliverymethod]" id="deliverymethod" value="<?php echo esc_attr( $method ); ?>" class="regular-text">

<?php foreach ( $deliverymethods as $id => $name ) : ?>
<div class="subtab" id="subtab-<?php echo $id; ?>"<?php echo $method == $id ? ' style="display:block"' : ''; ?>>
	<?php do_action( 'bulkmail_deliverymethod_tab', $id ); ?>
	<?php do_action( 'bulkmail_deliverymethod_tab_' . $id ); ?>
</div>
<?php endforeach; ?>
