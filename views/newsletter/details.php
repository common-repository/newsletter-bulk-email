<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );
if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$timeformat = bulkmail( 'helper' )->timeformat();
$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

?>

<?php if ( $editable ) : ?>
<table class="form-table">
		<tbody>

		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Subject', 'bulkmail' ); ?></th>
			<td>
				<div class="emoji-selector">
					<input type="text" class="widefat" value="<?php echo esc_attr( $this->post_data['subject'] ); ?>" name="bulkmail_data[subject]" id="bulkmail_subject" aria-label="<?php esc_attr_e( 'Subject', 'bulkmail' ); ?>">
					<button class="button emoji" data-input="bulkmail_subject">&#128578;</button>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Preheader', 'bulkmail' ); ?></th>
			<td>
				<div class="emoji-selector">
					<input type="text" class="widefat" value="<?php echo esc_attr( $this->post_data['preheader'] ); ?>" name="bulkmail_data[preheader]" id="bulkmail_preheader" aria-label="<?php esc_attr_e( 'Preheader', 'bulkmail' ); ?>">
					<button class="button emoji" data-input="bulkmail_preheader">&#128578;</button>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'From Name', 'bulkmail' ); ?> <a class="default-value bulkmail-icon" data-for="bulkmail_from-name" data-value="<?php echo esc_attr( bulkmail_option( 'from_name' ) ); ?>" title="<?php esc_attr_e( 'restore default', 'bulkmail' ); ?>"></a></th>
			<td>
				<div class="emoji-selector">
					<input type="text" class="widefat" value="<?php echo esc_attr( $this->post_data['from_name'] ); ?>" name="bulkmail_data[from_name]" id="bulkmail_from-name" aria-label="<?php esc_attr_e( 'From Name', 'bulkmail' ); ?>">
					<button class="button emoji" data-input="bulkmail_from-name">&#128578;</button>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'From Email', 'bulkmail' ); ?> <a class="default-value bulkmail-icon" data-for="bulkmail_from" data-value="<?php echo esc_attr( bulkmail_option( 'from' ) ); ?>" title="<?php esc_attr_e( 'restore default', 'bulkmail' ); ?>"></a></th>
			<td><input type="email" class="widefat" value="<?php echo esc_attr( $this->post_data['from_email'] ); ?>" name="bulkmail_data[from_email]" id="bulkmail_from" aria-label="<?php esc_attr_e( 'From Email', 'bulkmail' ); ?>"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Reply-to Email', 'bulkmail' ); ?> <a class="default-value bulkmail-icon" data-for="bulkmail_reply_to" data-value="<?php echo esc_attr( bulkmail_option( 'reply_to' ) ); ?>" title="<?php esc_attr_e( 'restore default', 'bulkmail' ); ?>"></a></th>
			<td><input type="email" class="widefat" value="<?php echo esc_attr( $this->post_data['reply_to'] ); ?>" name="bulkmail_data[reply_to]" id="bulkmail_reply_to" aria-label="<?php esc_attr_e( 'reply-to email', 'bulkmail' ); ?>"></td>
		</tr>
	 </tbody>
</table>
<input type="hidden" value="<?php echo esc_attr( $this->get_template() ); ?>" name="bulkmail_data[template]" id="bulkmail_template_name">
<input type="hidden" value="<?php echo esc_attr( $this->get_file() ); ?>" name="bulkmail_data[file]" id="bulkmail_template_file">


<?php else : ?>
	<?php
	$sent    = $this->get_sent( $post->ID );
	$totals  = 'autoresponder' != $post->post_status ? $this->get_totals( $post->ID ) : $sent;
	$deleted = $this->get_deleted( $post->ID );

	$errors = $this->get_errors( $post->ID );

	$opens        = $this->get_opens( $post->ID );
	$opens_total  = $this->get_opens( $post->ID, true );
	$clicks       = $this->get_clicks( $post->ID );
	$clicks_total = $this->get_clicks( $post->ID, true );
	$unsubscribes = $this->get_unsubscribes( $post->ID );
	$bounces      = $this->get_bounces( $post->ID );
	?>

<table>
	<tr><th width="16.666%"><?php esc_html_e( 'Subject', 'bulkmail' ); ?></th><td><strong><?php echo $this->post_data['subject']; ?></strong></td></tr>
	<?php if ( 'autoresponder' != $post->post_status ) : ?>
	<tr><th><?php esc_html_e( 'Date', 'bulkmail' ); ?></th><td>
		<?php echo date( $timeformat, $this->post_data['timestamp'] + $timeoffset ); ?>
		<?php
		if ( 'finished' == $post->post_status ) :
			echo ' &ndash; ' . date( $timeformat, $this->post_data['finished'] + $timeoffset );
			echo ' (' . sprintf( esc_html__( 'took %s', 'bulkmail' ), human_time_diff( $this->post_data['timestamp'], $this->post_data['finished'] ) ) . ')';
			endif;
		?>
	</td></tr>
	<?php endif; ?>
	<tr><th><?php esc_html_e( 'Preheader', 'bulkmail' ); ?></th><td><?php echo $this->post_data['preheader'] ? $this->post_data['preheader'] : '<span class="description">' . esc_html__( 'no preheader', 'bulkmail' ) . '</span>'; ?></td></tr>
</table>

<ul id="stats">
	<li class="receivers">
		<label class="recipients-limit"><span class="verybold hb-sent"><?php echo number_format_i18n( $sent ); ?></span> <?php echo ( 'autoresponder' == $post->post_status ) ? esc_html__( 'sent', 'bulkmail' ) : _nx( 'receiver', 'receivers', $sent, 'in pie chart', 'bulkmail' ); ?></label>
	</li>
	<?php if ( $this->post_data['track_opens'] ) : ?>
	<li>
		<div id="stats_open" class="piechart" data-percent="<?php echo $this->get_open_rate( $post->ID ) * 100; ?>"><span>0</span>%</div>
		<label class="show-open"><span class="verybold hb-opens"><?php echo number_format_i18n( $opens ); ?></span> <?php echo _nx( 'opened', 'opens', $opens, 'in pie chart', 'bulkmail' ); ?></label>
	</li>
	<?php endif; ?>
	<?php if ( $this->post_data['track_clicks'] ) : ?>
	<li>
		<div id="stats_click" class="piechart" data-percent="<?php echo $this->get_click_rate( $post->ID ) * 100; ?>"><span>0</span>%</div>
		<label class="show-click"><span class="verybold hb-clicks"><?php echo number_format_i18n( $clicks ); ?></span> <?php echo _nx( 'click', 'clicks', $clicks, 'in pie chart', 'bulkmail' ); ?></label>
	</li>
	<?php endif; ?>
	<li>
		<div id="stats_unsubscribes" class="piechart" data-percent="<?php echo $this->get_unsubscribe_rate( $post->ID ) * 100; ?>"><span>0</span>%</div>
		<label class="show-unsubscribes"><span class="verybold hb-unsubs"><?php echo number_format_i18n( $unsubscribes ); ?></span> <?php echo _nx( 'unsubscribe', 'unsubscribes', $unsubscribes, 'in pie chart', 'bulkmail' ); ?></label>
	</li>
	<li>
		<div id="stats_bounces" class="piechart" data-percent="<?php echo $this->get_bounce_rate( $post->ID ) * 100; ?>"><span>0</span>%</div>
		<label class="show-bounces"><span class="verybold hb-bounces"><?php echo number_format_i18n( $bounces ); ?></span> <?php echo _nx( 'bounce', 'bounces', $bounces, 'in pie chart', 'bulkmail' ); ?></label>
	</li>
</ul>
<table>

	<tr>
	<th><?php ( 'autoresponder' == $post->post_status ) ? esc_html_e( 'Total Sent', 'bulkmail' ) : esc_html_e( 'Total Recipients', 'bulkmail' ); ?></th>
	<td class="nopadding"> <span class="big hb-totals"><?php echo number_format_i18n( $totals ); ?></span>
	<?php
	if ( ! in_array( $post->post_status, array( 'finished', 'autoresponder' ) ) ) :
		echo '<span class="hb-sent">' . number_format_i18n( $sent ) . '</span> ' . esc_html__( 'sent', 'bulkmail' ) . '';
	endif;
	?>
	<?php
	if ( $deleted ) :
		echo '&ndash; <span class="hb-deleted">' . number_format_i18n( $deleted ) . '</span> ' . esc_html__( 'deleted', 'bulkmail' ) . '';
	endif;
	?>
	<?php if ( ! empty( $sent ) ) : ?>
		<a href="#" id="show_recipients" class="alignright bulkmail-icon showdetails"><?php esc_html_e( 'details', 'bulkmail' ); ?></a>
		<span class="spinner" id="recipients-ajax-loading"></span><div class="ajax-list" id="recipients-list"></div>
	<?php endif; ?>
	</td></tr>
	<?php if ( ! empty( $errors ) ) : ?>
	<tr><th><?php esc_html_e( 'Total Errors', 'bulkmail' ); ?></th><td class="nopadding"> <span class="big hb-errors"><?php echo number_format_i18n( $errors ); ?></span>
		<?php if ( ! empty( $errors ) ) : ?>
		<a href="#" id="show_errors" class="alignright bulkmail-icon showdetails"><?php esc_html_e( 'details', 'bulkmail' ); ?></a>
		<span class="spinner" id="error-ajax-loading"></span><div class="ajax-list" id="error-list"></div>
	<?php endif; ?>
	</td></tr>
	<?php endif; ?>
	<?php if ( $this->post_data['track_clicks'] ) : ?>
	<tr><th><?php esc_html_e( 'Total Clicks', 'bulkmail' ); ?></th><td class="nopadding"> <span class="big hb-clicks_total"><?php echo number_format_i18n( $clicks_total ); ?></span>
		<?php if ( ! empty( $clicks_total ) ) : ?>
		<a href="#" id="show_clicks" class="alignright bulkmail-icon showdetails"><?php esc_html_e( 'details', 'bulkmail' ); ?></a>
		<span class="spinner" id="clicks-ajax-loading"></span><div class="ajax-list" id="clicks-list"></div>
	<?php endif; ?>
	</td></tr>
<?php endif; ?>
	<?php
	if ( $environment = $this->get_environment( $post->ID ) ) :
		$types = array(
			'desktop' => esc_html__( 'Desktop', 'bulkmail' ),
			'mobile'  => esc_html__( 'Mobile', 'bulkmail' ),
			'webmail' => esc_html__( 'Web Client', 'bulkmail' ),
		);
		?>
	<tr class="environment"><th><?php esc_html_e( 'Environment', 'bulkmail' ); ?></th><td class="nopadding">
		<?php foreach ( $environment as $type => $data ) { ?>
		<label><span class="big"><span class="hb-<?php echo $type; ?>"><?php echo round( $data['percentage'] * 100, 2 ); ?>%</span> <span class="bulkmail-icon client-<?php echo $type; ?>"></span></span> <?php echo isset( $types[ $type ] ) ? $types[ $type ] : esc_html__( 'unknown', 'bulkmail' ); ?></label>
		<?php } ?>
		<a href="#" id="show_environment" class="alignright bulkmail-icon showdetails"><?php esc_html_e( 'details', 'bulkmail' ); ?></a>
		<span class="spinner" id="environment-ajax-loading"></span><div class="ajax-list" id="environment-list"></div>
	</td></tr>
	<?php endif; ?>
	<?php
	if ( $geo_data = $this->get_geo_data( $post->ID ) ) :

		$unknown_cities = array();
		$countrycodes   = array();

		foreach ( $geo_data as $countrycode => $data ) {
			$x = wp_list_pluck( $data, 3 );
			if ( $x ) {
				$countrycodes[ $countrycode ] = array_sum( $x );
			}

			if ( $data[0][3] ) {
				$unknown_cities[ $countrycode ] = $data[0][3];
			}
		}

		arsort( $countrycodes );
		$total = array_sum( $countrycodes );
		?>
	<tr class="geolocation"><th><?php esc_html_e( 'Geo Location', 'bulkmail' ); ?></th><td class="nopadding">
	<span class="hb-geo_location">
		<?php
		$i = 0;
		foreach ( $countrycodes as $countrycode => $count ) {
			?>
			<label title="<?php echo bulkmail( 'geo' )->code2Country( $countrycode ); ?>"><span class="big"><span class="bulkmail-flag-24 flag-<?php echo strtolower( $countrycode ); ?>"></span> <?php echo round( $count / $opens * 100, 2 ); ?>%</span></label>
			<?php
			if ( ++$i >= 5 ) {
				break;
			}
		}
		?>
		</span>
		<a href="#" id="show_geolocation" class="alignright bulkmail-icon showdetails"><?php esc_html_e( 'details', 'bulkmail' ); ?></a>
		<span class="spinner" id="geolocation-ajax-loading"></span>
		</td></tr><tr><td colspan="2" class="nopadding">
		<div class="ajax-list countries" id="geolocation-list"></div>
	</td></tr>

	<?php endif; ?>

</table>

<br class="clear">
<?php endif; ?>
<input type="hidden" value="<?php echo ! $editable; ?>" id="bulkmail_disabled" readonly>
