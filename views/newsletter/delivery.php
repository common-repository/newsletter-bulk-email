<?php

$now = time();

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );
if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$is_autoresponder = 'autoresponder' == $post->post_status || $this->post_data['autoresponder'];

$timestamp = ( ! empty( $this->post_data['timestamp'] ) ) ? $this->post_data['timestamp'] : $now + ( 60 * bulkmail_option( 'send_offset' ) );

$timestamp = ( ! $this->post_data['active'] ) ? max( $now + ( 60 * bulkmail_option( 'send_offset' ) ), $timestamp ) : $timestamp;

$timeformat = bulkmail( 'helper' )->timeformat();
$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

$current_user = wp_get_current_user();

$sent = $this->get_sent( $post->ID );

?>
<?php if ( $editable ) : ?>

	<?php if ( current_user_can( 'bulkmail_edit_autoresponders' ) ) : ?>
		<ul class="category-tabs">
			<li<?php echo ! $is_autoresponder ? ' class="tabs"' : ''; ?>>
				<a href="#regular-campaign"><?php esc_html_e( 'Regular Campaign', 'bulkmail' ); ?></a>
			</li>
			<li<?php echo $is_autoresponder ? ' class="tabs"' : ''; ?>>
				<a href="#autoresponder"><?php esc_html_e( 'Auto Responder', 'bulkmail' ); ?></a>
			</li>
		</ul>
		<div id="regular-campaign" class="tabs-panel" <?php echo ( $is_autoresponder ) ? ' style="display:none"' : ''; ?>>
	<?php endif; ?>
	<p class="howto" title="<?php echo date( $timeformat, $now ); ?>">
	<?php
		printf(
			esc_html__( 'Server time: %1$s %2$s', 'bulkmail' ),
			'<span title="' . date( $timeformat, $now + $timeoffset ) . '">' . date( 'Y-m-d', $now + $timeoffset ) . '</span>',
			'<span class="time" data-timestamp="' . ( $now + $timeoffset ) . '">' . date( 'H:i', $now + $timeoffset ) . '</span>'
		);

	elseif ( 'finished' == $post->post_status ) :

		printf(
			esc_html__( 'This campaign has been sent on %s.', 'bulkmail' ),
			'<strong>' . date( $timeformat, $this->post_data['finished'] + $timeoffset ) . '</strong>'
		);

	endif;
	?>
	</p>
<?php if ( $editable ) : ?>
<label>
	<input name="bulkmail_data[active]" id="bulkmail_data_active" value="1" type="checkbox" <?php echo ( $this->post_data['active'] && ! $is_autoresponder ) ? 'checked' : ''; ?> <?php echo ( ! $editable ) ? ' disabled' : ''; ?>>
	<?php esc_html_e( 'send this campaign', 'bulkmail' ); ?>
</label>

	<div class="active_wrap<?php echo $this->post_data['timezone'] ? ' timezone-enabled' : ''; ?><?php echo $this->post_data['active'] && ! $is_autoresponder ? ' disabled' : ''; ?>">
		<div class="active_overlay"></div>
		<?php
		printf(
			esc_html_x( 'on %1$s @ %2$s', 'send campaign "on" (date) "at" (time)', 'bulkmail' ),
			'<input name="bulkmail_data[date]" class="datepicker deliverydate inactive" type="text" value="' . date( 'Y-m-d', $timestamp + $timeoffset ) . '" maxlength="10" readonly' . ( ( ( ! $this->post_data['active'] && ! $is_autoresponder ) || $editable ) ? ' disabled' : '' ) . '>',
			'<input name="bulkmail_data[time]" maxlength="5" class="deliverytime inactive" type="text" value="' . date( 'H:i', $timestamp + $timeoffset ) . '" ' . ( ( ( ! $this->post_data['active'] && ! $is_autoresponder ) || ! $editable ) ? ' disabled' : '' ) . '> <span class="utcoffset">' . ( ( $timeoffset > 0 ) ? 'UTC + ' . ( $timeoffset / 3600 ) : '' ) . '</span>'
		);
		?>
		<?php if ( bulkmail_option( 'track_location' ) ) : ?>
			<br><label title="<?php esc_attr_e( 'Send this campaign based on the subscribers timezone if known', 'bulkmail' ); ?>">
			<input type="checkbox" class="timezone" name="bulkmail_data[timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php esc_html_e( 'Use Subscribers timezone', 'bulkmail' ); ?>
			</label>
		<?php endif; ?>
	</div>
	<?php
	if ( $sent && ! $is_autoresponder ) :

		$totals = $this->get_totals( $post->ID );
		$p      = round( $this->get_sent_rate( $post->ID ) * 100 );
		$pg     = sprintf( esc_html__( '%1$s of %2$s sent', 'bulkmail' ), number_format_i18n( $sent ), number_format_i18n( $totals ) );
		?>
		<p>
			<div class="progress paused"><span class="bar" style="width:<?php echo $p; ?>%"><span>&nbsp;<?php echo $pg; ?></span></span><span>&nbsp;<?php echo $pg; ?></span><var><?php echo $p; ?>%</var></div>
		</p>
	<?php endif; ?>

	<?php if ( current_user_can( 'bulkmail_edit_autoresponders' ) ) : ?>
</div>
<div id="autoresponder" class="tabs-panel"<?php echo ! $is_autoresponder ? ' style="display:none"' : ''; ?>>
		<?php
		$autoresponderdata = wp_parse_args(
			$this->post_data['autoresponder'],
			array(
				'operator'          => '',
				'action'            => 'bulkmail_subscriber_insert',
				'unit'              => '',
				'before_after'      => 1,
				'userunit'          => 'day',
				'uservalue'         => '',
				'userexactdate'     => false,
				'timestamp'         => $now,
				'endtimestamp'      => $now,
				'weekdays'          => array(),
				'post_type'         => 'post',
				'time_post_type'    => 'post',
				'time_post_count'   => 1,
				'post_count'        => 0,
				'post_count_status' => 0,
				'issue'             => 1,
				'since'             => false,
				'interval'          => 1,
				'time_frame'        => 'day',
				'timezone'          => false,
				'hook'              => '',
				'priority'          => 10,
				'once'              => false,
				'multiple'          => false,
				'followup_action'   => 1,
			)
		);

		include_once BULKEMAIL_DIR . 'includes/autoresponder.php';
		?>
	<label>
		<input name="bulkmail_data[active_autoresponder]" id="bulkmail_data_autoresponder_active" value="1" type="checkbox" <?php checked( ( $this->post_data['active'] && $is_autoresponder ), true ); ?> <?php echo ( ! $editable ) ? ' disabled' : ''; ?>> <?php esc_html_e( 'send this auto responder', 'bulkmail' ); ?>
	</label>

	<div id="autoresponder_wrap" class="autoresponder-<?php echo $autoresponderdata['action']; ?>">
		<div class="autoresponder_active_wrap<?php echo $this->post_data['active'] && $is_autoresponder ? ' disabled' : ''; ?>">
			<div class="autoresponder_active_overlay"></div>
		<p class="autoresponder_time">
		<input type="text" class="small-text" name="bulkmail_data[autoresponder][amount]" value="<?php echo isset( $autoresponderdata['amount'] ) ? $autoresponderdata['amount'] : 1; ?>">
			<select name="bulkmail_data[autoresponder][unit]">
			<?php
			foreach ( $bulkmail_autoresponder_info['units'] as $value => $name ) {
				echo '<option value="' . $value . '"' . selected( $autoresponderdata['unit'], $value, false ) . '>' . $name . '</option>';
			}
			?>
			</select>
			<span class="autoresponder_after"><?php esc_html_e( 'after', 'bulkmail' ); ?></span>
			<span class="autoresponder_before"><?php esc_html_e( 'before', 'bulkmail' ); ?></span>
			<select class="autoresponder_before_after" name="bulkmail_data[autoresponder][before_after]">
				<option value="1" <?php selected( $autoresponderdata['before_after'], 1 ); ?>><?php esc_html_e( 'after', 'bulkmail' ); ?></option>
				<option value="-1" <?php selected( $autoresponderdata['before_after'], -1 ); ?>><?php esc_html_e( 'before', 'bulkmail' ); ?></option>
			</select>
		</p>
		<p>
			<select class="widefat" name="bulkmail_data[autoresponder][action]" id="bulkmail_autoresponder_action">
			<?php
			foreach ( $bulkmail_autoresponder_info['actions'] as $id => $action ) {
				echo '<option value="' . $id . '"' . selected( $autoresponderdata['action'], $id, false ) . '>' . $action['label'] . '</option>';
			}
			?>
			</select>
		</p>

		<div class="bulkmail_autoresponder_more autoresponderfield-bulkmail_subscriber_insert autoresponderfield-bulkmail_subscriber_unsubscribed">
			<p>
			<span class="bulkmail_autoresponder_more autoresponderfield-bulkmail_subscriber_insert">
			<?php esc_html_e( 'only for subscribers who signed up', 'bulkmail' ); ?>
			</span>
			<span class="bulkmail_autoresponder_more autoresponderfield-bulkmail_subscriber_unsubscribed">
			<?php esc_html_e( 'only for subscribers who canceled their subscription', 'bulkmail' ); ?>
			</span>
			<?php
			esc_html_e( 'after', 'bulkmail' );
			$timestamp = $this->post_data['timestamp'] ? $this->post_data['timestamp'] : $now;

			printf(
				esc_html_x( '%1$s @ %2$s', 'send campaign "on" (date) "at" (time)', 'bulkmail' ),
				'<input name="bulkmail_data[autoresponder_signup_date]" class="datepicker deliverydate inactive nolimit" type="text" value="' . date( 'Y-m-d', $timestamp + $timeoffset ) . '" maxlength="10" readonly>',
				'<input name="bulkmail_data[autoresponder_signup_time]" maxlength="5" class="deliverytime inactive" type="text" value="' . date( 'H:i', $timestamp + $timeoffset ) . '"> <span class="utcoffset">UTC ' . ( $timeoffset ? '+' : '' ) . ( $timeoffset / 3600 ) . '</span>'
			);
			?>
			</p>
		</div>
		<div class="bulkmail_autoresponder_more autoresponderfield-bulkmail_subscriber_unsubscribed">
			<p class="description">
				<?php esc_html_e( 'Keep in mind it is bad practice to send campaigns after subscribers opt-out so use this option for "Thank you" messages or surveys.', 'bulkmail' ); ?>
			</p>
		</div>

			<?php $pts = bulkmail( 'helper' )->get_post_types( true, 'object' ); ?>

		<div class="bulkmail_autoresponder_more autoresponderfield-bulkmail_post_published">
			<p>
				<?php
				$count = '<input type="number" name="bulkmail_data[autoresponder][post_count]" class="small-text" value="' . $autoresponderdata['post_count'] . '">';
				$type  = '<select id="autoresponder-post_type" name="bulkmail_data[autoresponder][post_type]">';
				foreach ( $pts as $pt => $data ) {
					$type .= '<option value="' . $pt . '"' . selected( $autoresponderdata['post_type'], $pt, false ) . '>' . $data->labels->singular_name . '</option>';
				}
				$type .= '<option value="rss"' . selected( $autoresponderdata['post_type'], 'rss', false ) . '>' . esc_html__( 'RSS Feed', 'bulkmail' ) . '</option>';
				$type .= '</select>';
				printf( esc_html__( 'create a new campaign every time a new %s has been published', 'bulkmail' ), $type );
				?>
			</p>
			<p>
			<?php if ( bulkmail_option( 'track_location' ) ) : ?>
				<label title="<?php esc_attr_e( 'Send this campaign based on the subscribers timezone if known', 'bulkmail' ); ?>">
				<input type="checkbox" class="autoresponder-timezone" name="bulkmail_data[autoresponder][post_published_timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php esc_html_e( 'Use Subscribers timezone', 'bulkmail' ); ?>
				</label>
			<?php endif; ?>
			</p>
			<div id="autoresponderfield-bulkmail_post_published_advanced">
				<div id="autoresponder-taxonomies">
				<?php
				$taxes = bulkmail( 'helper' )->get_post_term_dropdown( $autoresponderdata['post_type'], false, true, isset( $autoresponderdata['terms'] ) ? $autoresponderdata['terms'] : array() );
				if ( $taxes ) {
					printf( esc_html__( 'only if in %s', 'bulkmail' ), $taxes );
				}
				?>
				</div>
				<p>
					<?php
					printf( esc_html__( _n( 'always skip %s release', 'always skip %s releases', $autoresponderdata['post_count'], 'bulkmail' ) ), $count );
					?>
				</p>
			</div>
		</div>

		<div class="bulkmail_autoresponder_more autoresponderfield-bulkmail_autoresponder_timebased<?php echo $this->post_data['timezone'] ? ' timezone-enabled' : ''; ?>">
			<p>
				<?php
				$timestamp = $this->post_data['timestamp'] ? $this->post_data['timestamp'] : $now;

				$interval   = '<br><input type="number" name="bulkmail_data[autoresponder][interval]" class="small-text" value="' . $autoresponderdata['interval'] . '">';
				$time_frame = '<select name="bulkmail_data[autoresponder][time_frame]">';
				$values     = array(
					'hour'  => esc_html__( 'hour(s)', 'bulkmail' ),
					'day'   => esc_html__( 'day(s)', 'bulkmail' ),
					'week'  => esc_html__( 'week(s)', 'bulkmail' ),
					'month' => esc_html__( 'month(s)', 'bulkmail' ),
				);
				foreach ( $values as $i => $value ) {
					$time_frame .= '<option value="' . $i . '"' . selected( $autoresponderdata['time_frame'], $i, false ) . '>' . $value . '</option>';
				}
				$time_frame .= '</select>';
				printf( esc_html_x( 'create a new campaign every %1$s%2$s', 'every [x] [timeframe] starting [startdate]', 'bulkmail' ), $interval, $time_frame );
				?>
			</p>
				<?php
				echo '<h4>' . esc_html__( 'next schedule', 'bulkmail' ) . '</h4>';
				?>
			<p>
			<?php
				printf(
					esc_html_x( 'on %1$s @ %2$s', 'send campaign "on" (date) "at" (time)', 'bulkmail' ),
					'<input name="bulkmail_data[autoresponder_date]" class="datepicker deliverydate inactive" type="text" value="' . date( 'Y-m-d', $timestamp + $timeoffset ) . '" maxlength="10" readonly>',
					'<input name="bulkmail_data[autoresponder_time]" maxlength="5" class="deliverytime inactive" type="text" value="' . date( 'H:i', $timestamp + $timeoffset ) . '"> <span class="utcoffset">UTC ' . ( $timeoffset ? '+' : '' ) . ( $timeoffset / 3600 ) . '</span>'
				);

				$autoresponderdata['endschedule'] = isset( $autoresponderdata['endschedule'] );
			?>
			<?php if ( bulkmail_option( 'track_location' ) ) : ?>
				<label title="<?php esc_attr_e( 'Send this campaign based on the subscribers timezone if known', 'bulkmail' ); ?>">
				<input type="checkbox" class="autoresponder-timezone" name="bulkmail_data[autoresponder][timebased_timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php esc_html_e( 'Use Subscribers timezone', 'bulkmail' ); ?>
				</label>
			<?php endif; ?>
			</p>
			<p>
			<label><input type="checkbox" name="bulkmail_data[autoresponder][endschedule]" class="bulkmail_autoresponder_timebased-end-schedule" <?php checked( $autoresponderdata['endschedule'] ); ?> value="1"> <?php esc_html_e( 'end schedule', 'bulkmail' ); ?></label>
				<div class="bulkmail_autoresponder_timebased-end-schedule-field"<?php echo ! $autoresponderdata['endschedule'] ? ' style="display:none"' : ''; ?>>
					<?php
					$timestamp = max( $timestamp, $autoresponderdata['endtimestamp'] );

					printf(
						esc_html_x( 'on %1$s @ %2$s', 'send campaign "on" (date) "at" (time)', 'bulkmail' ),
						'<input name="bulkmail_data[autoresponder_enddate]" class="datepicker deliverydate inactive" type="text" value="' . date( 'Y-m-d', $timestamp + $timeoffset ) . '" maxlength="10" readonly>',
						'<input name="bulkmail_data[autoresponder_endtime]" maxlength="5" class="deliverytime inactive" type="text" value="' . date( 'H:i', $timestamp + $timeoffset ) . '"> <span class="utcoffset">UTC ' . ( $timeoffset ? '+' : '' ) . ( $timeoffset / 3600 ) . '</span>'
					);
					?>
					<span class="description"><?php esc_html_e( 'set an end date for your campaign', 'bulkmail' ); ?></span>
				</div>
			</p>
			<p>
				<?php

				global $wp_locale;

				esc_html_e( 'send campaigns only on these weekdays', 'bulkmail' );
				echo '<br>';
				$start_at = get_option( 'start_of_week' );

				for ( $i = $start_at; $i < 7 + $start_at; $i++ ) {
					$j = $i;
					if ( ! isset( $wp_locale->weekday[ $j ] ) ) {
						$j = $j - 7;
					}

					echo '<label title="' . $wp_locale->weekday[ $j ] . '" class="weekday"><input name="bulkmail_data[autoresponder][weekdays][]" type="checkbox" value="' . $j . '" ' . checked( ( in_array( $j, $autoresponderdata['weekdays'] ) || ! $autoresponderdata['weekdays'] ), true, false ) . '>' . $wp_locale->weekday_initial[ $wp_locale->weekday[ $j ] ] . '&nbsp;</label> ';
				}
				?>
			</p>
			<p><label><input type="checkbox" name="bulkmail_data[autoresponder][time_conditions]" id="time_extra" value="1" <?php checked( isset( $autoresponderdata['time_conditions'] ) ); ?>> <?php esc_html_e( 'only if', 'bulkmail' ); ?></label></p>
			<div id="autoresponderfield-bulkmail_timebased_advanced"<?php echo ! isset( $autoresponderdata['time_conditions'] ) ? ' style="display:none"' : ''; ?>>
				<p>
				<?php
				$count = '<input type="number" name="bulkmail_data[autoresponder][time_post_count]" class="small-text" value="' . $autoresponderdata['time_post_count'] . '">';
				$type  = '<select id="autoresponder-post_type_time" name="bulkmail_data[autoresponder][time_post_type]">';
				foreach ( $pts as $pt => $data ) {
					if ( in_array( $pt, array( 'attachment', 'newsletter' ) ) ) {
						continue;
					}
					$type .= '<option value="' . $pt . '"' . selected( $autoresponderdata['time_post_type'], $pt, false ) . '>' . $data->labels->name . '</option>';
				}
				$type .= '<option value="rss"' . selected( $autoresponderdata['time_post_type'], 'rss', false ) . '>' . esc_html__( 'RSS Feeds', 'bulkmail' ) . '</option>';
				$type .= '</select><br>';
				printf( esc_html__( '%1$s %2$s have been published', 'bulkmail' ), $count, $type );
				?>
				</p>
			</div>
			<p><label><input type="checkbox" name="bulkmail_data[autoresponder][since]" value="<?php echo esc_attr( $autoresponderdata['since'] ); ?>" <?php checked( ! ! $autoresponderdata['since'] ); ?>> <?php esc_html_e( 'only if new content is available.', 'bulkmail' ); ?></label></p>
		</div>

		<div class="bulkmail_autoresponder_more autoresponderfield-bulkmail_post_published autoresponderfield-bulkmail_autoresponder_timebased">
				<p>
				<?php
				$issue = '<input type="number" id="bulkmail_autoresponder_issue" name="bulkmail_data[autoresponder][issue]" class="small-text" value="' . $autoresponderdata['issue'] . '">';
				printf( esc_html__( 'Next issue: %s', 'bulkmail' ), $issue );
				?>
				</p>
				<p class="description">
				<?php printf( esc_html__( 'Use the %s tag to display the current issue in the campaign', 'bulkmail' ), '<code>{issue}</code>' ); ?>
				</p>
		</div>

		<div class="bulkmail_autoresponder_more autoresponderfield-bulkmail_post_published<?php echo isset( $autoresponderdata['time_conditions'] ) ? ' autoresponderfield-bulkmail_autoresponder_timebased' : ''; ?>">
			<p class="description">
				<?php
				$post_type = ( 'bulkmail_autoresponder_timebased' == $autoresponderdata['action'] )
					? $autoresponderdata['time_post_type']
					: $autoresponderdata['post_type'];

				if ( 'rss' == $post_type ) {
					$post_type_label = ( 1 == $autoresponderdata['post_count_status'] ? esc_html__( 'RSS Feed', 'bulkmail' ) : esc_html__( 'RSS Feeds', 'bulkmail' ) );
				} else {
					$post_type_label = '<a href="' . admin_url( 'edit.php?post_type=' . $post_type ) . '">' . ( 1 == $autoresponderdata['post_count_status'] ? $pts[ $post_type ]->labels->singular_name : $pts[ $post_type ]->labels->name ) . '</a>';
				}

				printf(
					_n( '%1$s matching %2$s has been published', '%1$s matching %2$s have been published', $autoresponderdata['post_count_status'], 'bulkmail' ),
					'<strong>' . $autoresponderdata['post_count_status'] . '</strong>',
					'<strong>' . $post_type_label . '</strong>'
				);
				if ( $autoresponderdata['since'] ) {
					printf(
						'<br><span title="' . esc_attr( 'The time which is used in this campaign. All posts must have been published after this date.', 'bulkmail' ) . '">' . esc_html__( 'Only %1$s after %2$s count.', 'bulkmail' ) . '</span>',
						strip_tags( $post_type_label ),
						date( $timeformat, $autoresponderdata['since'] + $timeoffset )
					);
				}
				?>
				<br><label><input type="checkbox" name="post_count_status_reset" value="1"> <?php esc_html_e( 'reset counter', 'bulkmail' ); ?></label>
			</p>
			<input type="hidden" name="bulkmail_data[autoresponder][post_count_status]" value="<?php echo $autoresponderdata['post_count_status']; ?>">

		</div>

		<div class="bulkmail_autoresponder_more autoresponderfield-bulkmail_autoresponder_usertime">
			<p>
				<?php
				if ( $customfields = bulkmail()->get_custom_date_fields() ) :

					$amount = '<input type="number" class="small-text" name="bulkmail_data[autoresponder][useramount]" value="' . ( isset( $autoresponderdata['useramount'] ) ? $autoresponderdata['useramount'] : 1 ) . '">';

					$unit   = '<select name="bulkmail_data[autoresponder][userunit]">';
					$values = array(
						'day'   => esc_html__( 'day(s)', 'bulkmail' ),
						'week'  => esc_html__( 'week(s)', 'bulkmail' ),
						'month' => esc_html__( 'month(s)', 'bulkmail' ),
						'year'  => esc_html__( 'year(s)', 'bulkmail' ),
					);
					foreach ( $values as $key => $value ) {
						$unit .= '<option value="' . $key . '"' . selected( $autoresponderdata['userunit'], $key, false ) . '>' . $value . '</option>';
					}
					$unit .= '</select>';

					$uservalue  = '<select name="bulkmail_data[autoresponder][uservalue]">';
					$uservalue .= '<option value="-1">--</option>';

					foreach ( $customfields as $key => $data ) {
						$uservalue .= '<option value="' . $key . '"' . selected( $autoresponderdata['uservalue'], $key, false ) . '>' . $data['name'] . '</option>';
					}
					$uservalue .= '</select>';
					?>
			</p>
			<p id="userexactdate">
				<label>
					<input type="radio" class="userexactdate" name="bulkmail_data[autoresponder][userexactdate]" value="0" <?php checked( ! $autoresponderdata['userexactdate'] ); ?>>
					<span
					<?php
					if ( $autoresponderdata['userexactdate'] ) {
						echo ' class="disabled"'; }
					?>
					><?php printf( esc_html__( 'every %1$s %2$s', 'bulkmail' ), $amount, $unit ); ?></span>
				</label><br>
				<label>
					<input type="radio" class="userexactdate" name="bulkmail_data[autoresponder][userexactdate]" value="1" <?php checked( $autoresponderdata['userexactdate'] ); ?>>
					<span
					<?php
					if ( ! $autoresponderdata['userexactdate'] ) {
						echo ' class="disabled"'; }
					?>
					><?php esc_html_e( 'on the exact date', 'bulkmail' ); ?></span>
				</label>
			</p>
			<p>
					<?php
					printf( esc_html__( 'of the users %1$s value', 'bulkmail' ), $uservalue );
				else :
					esc_html_e( 'No custom date fields found!', 'bulkmail' );
					if ( current_user_can( 'manage_options' ) ) {
						echo '<br><a href="edit.php?post_type=newsletter&page=bulkmail_settings&settings-updated=true#subscribers">' . esc_html__( 'add new fields', 'bulkmail' ) . '</a>';
					}
				endif;
				?>
			</p>
			<p>
				<?php if ( bulkmail_option( 'track_location' ) ) : ?>
				<label title="<?php esc_attr_e( 'Send this campaign based on the subscribers timezone if known', 'bulkmail' ); ?>">
					<input type="checkbox" class="autoresponder-timezone" name="bulkmail_data[autoresponder][usertime_timezone]" value="1" <?php checked( $this->post_data['timezone'] ); ?>> <?php esc_html_e( 'Use Subscribers timezone', 'bulkmail' ); ?>
				</label>
				<?php endif; ?>
			</p>
			<p>
				<label>
					<input type="checkbox" name="bulkmail_data[autoresponder][usertime_once]" value="1" <?php checked( $autoresponderdata['once'] ); ?>> <?php esc_html_e( 'send campaign only once', 'bulkmail' ); ?>
				</label>
			</p>
		</div>
		<div class="bulkmail_autoresponder_more autoresponderfield-bulkmail_autoresponder_followup">
				<?php
				if ( $all_campaigns = $this->get_campaigns(
					array(
						'post__not_in' => array( $post->ID ),
						'orderby'      => 'post_title',
					)
				) ) :

					// bypass post_status sort limitation.
					$all_campaigns_stati = wp_list_pluck( $all_campaigns, 'post_status' );
					asort( $all_campaigns_stati );

					?>
				<p>
					<select name="bulkmail_data[autoresponder][followup_action]">
						<option value="1" <?php selected( $autoresponderdata['followup_action'], 1 ); ?>><?php esc_html_e( 'has been sent', 'bulkmail' ); ?></option>
						<option value="2" <?php selected( $autoresponderdata['followup_action'], 2 ); ?>><?php esc_html_e( 'has been opened', 'bulkmail' ); ?></option>
						<option value="3" <?php selected( $autoresponderdata['followup_action'], 3 ); ?>><?php esc_html_e( 'has been clicked', 'bulkmail' ); ?></option>
					</select>
				</p>
				<fieldset>
					<label><?php esc_html_e( 'Campaign', 'bulkmail' ); ?>
					<select name="parent_id" id="parent_id" class="widefat">
					<option value="0">--</option>
					<?php
					global $wp_post_statuses;
					$status = '';
					foreach ( $all_campaigns_stati as $i => $c ) :
						$c = $all_campaigns[ $i ];
						if ( $status != $c->post_status ) {
							if ( $status ) {
								echo '</optgroup>';
							}
							echo '<optgroup label="' . $wp_post_statuses[ $c->post_status ]->label . '">';
							$status = $c->post_status;
						}
						?>
					<option value="<?php echo $c->ID; ?>" <?php selected( $post->post_parent, $c->ID ); ?>><?php echo $c->post_title ? $c->post_title : '[' . esc_html__( 'no title', 'bulkmail' ) . ']'; ?></option>
						<?php
					endforeach;
					?>
					</optgroup></select></label>
				</fieldset>
			<?php else : ?>
				<p><?php esc_html_e( 'No campaigns available', 'bulkmail' ); ?></p>
			<?php endif; ?>
		</div>

		<div class="bulkmail_autoresponder_more autoresponderfield-bulkmail_autoresponder_hook">
			<p>
				<label>
					<?php esc_html_e( 'Action used to trigger campaign', 'bulkmail' ); ?> (<abbr title="<?php esc_attr_e( 'use `do_action("hook_name")`, or `do_action("hook_name", $subscriber_id)` to trigger this campaign', 'bulkmail' ); ?>">?</abbr>)
				</label>
			</p>
				<?php
				$hooks = apply_filters( 'bulkmail_action_hooks', array() );
				if ( $autoresponderdata['hook'] && ! isset( $hooks[ $autoresponderdata['hook'] ] ) ) {
					$hooks[ $autoresponderdata['hook'] ] = $autoresponderdata['hook'];
				}
				?>
			<?php if ( $hooks ) : ?>
			<p>
				<label>
					<select class="widefat bulkmail-action-hooks">
						<option value=""><?php esc_html_e( 'Choose', 'bulkmail' ); ?></option>
						<?php foreach ( $hooks as $hook => $name ) : ?>
							<option value="<?php echo esc_attr( $hook ); ?>" <?php selected( $hook, $autoresponderdata['hook'] ); ?>><?php echo esc_html( $name ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</p>
				<?php endif; ?>
			<p>
				<input type="text" class="widefat code bulkmail-action-hook" name="bulkmail_data[autoresponder][hook]" value="<?php echo $autoresponderdata['hook']; ?>" placeholder="hook_name">
			</p>
			<div>
				<p><label>
				<?php esc_html_e( 'Priority', 'bulkmail' ); ?>:
					<select name="bulkmail_data[autoresponder][priority]">
						<option value="5" <?php selected( $autoresponderdata['priority'], 5 ); ?>><?php esc_html_e( 'High', 'bulkmail' ); ?></option>
						<option value="10" <?php selected( $autoresponderdata['priority'], 10 ); ?>><?php esc_html_e( 'Normal', 'bulkmail' ); ?></option>
						<option value="15" <?php selected( $autoresponderdata['priority'], 15 ); ?>><?php esc_html_e( 'Low', 'bulkmail' ); ?></option>
					</select>
				</label></p>
			</div>
			<div>
				<p><label>
					<input type="checkbox" name="bulkmail_data[autoresponder][hook_once]" value="1" <?php checked( $autoresponderdata['once'] ); ?>> <?php esc_html_e( 'send campaign only once', 'bulkmail' ); ?>
				</label></p>
			</div>
			<div>
				<label>
					<input type="checkbox" name="bulkmail_data[autoresponder][multiple]" value="1" <?php checked( $autoresponderdata['multiple'] ); ?>> <?php esc_html_e( 'allow multiple triggers', 'bulkmail' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Hooks can get triggered multiple times and cause multiple emails.', 'bulkmail' ); ?></p>
			</div>
		</div>

			<?php do_action( 'bulkmail_autoresponder_more' ); ?>

	</div>
	</div>
	</div>
	<?php endif; ?>
	<div>
		<?php
		if ( ! ( $test_email = get_user_meta( $current_user->ID, '_bulkmail_test_email', true ) ) ) {
			$test_email = $current_user->user_email;
		}
		$test_email = apply_filters( 'bulkmail_test_email', $test_email );
		?>
		<input type="text" value="<?php echo esc_attr( $test_email ); ?>" placeholder="<?php echo esc_attr( $current_user->user_email ); ?>" autocomplete="off" id="bulkmail_testmail" class="widefat" aria-label="<?php esc_attr_e( 'Send Test', 'bulkmail' ); ?>">
		<button type="button" class="button bulkmail_spamscore" title="<?php esc_attr_e( 'check your spam score', 'bulkmail' ); ?> (beta)">Spam Score</button>
		<span class="spinner" id="delivery-ajax-loading"></span>
		<input type="button" value="<?php esc_attr_e( 'Send Test', 'bulkmail' ); ?>" class="button bulkmail_sendtest">

		<div id="spam_score_progress">
		<div class="progress"><span class="bar" style="width:1%"></span></div>
		<div class="score"></div>
		</div>
	</div>

<?php elseif ( 'active' == $post->post_status ) : ?>
	<p>
	<?php
		printf(
			esc_html__( 'This campaign has been started on %1$s, %2$s ago', 'bulkmail' ),
			'<br><strong>' . date( $timeformat, $this->post_data['timestamp'] + $timeoffset ),
			human_time_diff( $now, $this->post_data['timestamp'] ) . '</strong>'
		);
	?>
	</p>
	<?php
	if ( $sent && ! $is_autoresponder ) :

		$totals = $this->get_totals( $post->ID );
		$p      = round( $this->get_sent_rate( $post->ID ) * 100 );
		$pg     = sprintf( esc_html__( '%1$s of %2$s sent', 'bulkmail' ), number_format_i18n( $sent ), number_format_i18n( $totals ) );
		?>
		<div class="progress">
			<span class="bar" style="width:<?php echo $p; ?>%"><span>&nbsp;<?php echo $pg; ?></span></span><span>&nbsp;<?php echo $pg; ?></span><var><?php echo $p; ?>%</var>
		</div>

		<?php if ( $p ) : ?>
		<p>
			<?php
			$timepast = $now - $this->post_data['timestamp'];
			$timeleft = human_time_diff( $now + ( 100 - $p ) * ( $timepast / $p ) );
			printf( esc_html__( 'finished in approx. %s', 'bulkmail' ), '<strong>' . $timeleft . '</strong>' );
			?>
		</p>
		<?php endif; ?>
	<?php endif; ?>

<?php elseif ( $is_autoresponder ) : ?>
	<p>
	<?php printf( esc_html__( 'You have to %s to change the delivery settings', 'bulkmail' ), '<a href="post.php?post=' . $post_id . '&action=edit">' . esc_html__( 'switch to the edit mode', 'bulkmail' ) . '</a>' ); ?>
	</p>
<?php elseif ( 'finished' != $post->post_status ) : ?>
	<?php
		$totals = $this->get_totals( $post->ID );
		$p      = round( $this->get_sent_rate( $post->ID ) * 100 );
		$pg     = sprintf( esc_html__( '%1$s of %2$s sent', 'bulkmail' ), number_format_i18n( $sent ), number_format_i18n( $totals ) );
	?>
	<div class="progress paused">
		<span class="bar" style="width:<?php echo $p; ?>%"><span>&nbsp;<?php echo $pg; ?></span></span><span>&nbsp;<?php echo $pg; ?></span><var><?php echo $p; ?>%</var>
	</div>
<?php endif; ?>

<?php if ( $this->post_data['parent_id'] && current_user_can( 'edit_newsletter', $post->ID ) && current_user_can( 'edit_others_newsletters', $this->post_data['parent_id'] ) ) : ?>
	<p>
	<?php
		printf(
			esc_html__( 'This campaign is based on an %s', 'bulkmail' ),
			'<a href="post.php?post=' . $this->post_data['parent_id'] . '&action=edit&showstats=1">' . esc_html__( 'auto responder campaign', 'bulkmail' ) . '</a>'
		);
	?>
	</p>
<?php endif; ?>
<input type="hidden" id="bulkmail_is_autoresponder" name="bulkmail_data[is_autoresponder]" value="<?php echo $is_autoresponder; ?>">
