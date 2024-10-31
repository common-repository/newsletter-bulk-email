<?php
	$cron_status = bulkmail( 'cron' )->check();
if ( is_wp_error( $cron_status ) ) : ?>
	<div class="error inline"><p><strong><?php echo $cron_status->get_error_message(); ?></strong></p></div>
	<?php
	endif;
?>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-interval wp_cron">
		<th scope="row"><?php esc_html_e( 'Interval for sending emails', 'bulkmail' ); ?></th>
		<td><p><?php printf( esc_html__( 'Send emails at most every %1$s minutes', 'bulkmail' ), '<input type="text" name="bulkmail_options[interval]" value="' . bulkmail_option( 'interval' ) . '" class="small-text">' ); ?></p><p class="description"><?php esc_html_e( 'Optional if a real cron service is used', 'bulkmail' ); ?></p></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-cron-service">
		<th scope="row"><?php esc_html_e( 'Cron Service', 'bulkmail' ); ?></th>
		<td>
			<?php $cron = bulkmail_option( 'cron_service' ); ?>
			<label><input type="radio" class="cron_radio" name="bulkmail_options[cron_service]" value="wp_cron" <?php checked( $cron == 'wp_cron' ); ?> > <?php esc_html_e( 'Use the wp_cron function to send newsletters', 'bulkmail' ); ?></label><br>
			<label><input type="radio" class="cron_radio" name="bulkmail_options[cron_service]" value="cron" <?php checked( $cron == 'cron' ); ?> > <?php esc_html_e( 'Use a real cron to send newsletters', 'bulkmail' ); ?></label> <span class="description"><?php esc_html_e( 'recommended for many subscribers', 'bulkmail' ); ?></span>
			<?php if ( file_exists( BULKEMAIL_UPLOAD_DIR . '/CRON_LOCK' ) && ( time() - filemtime( BULKEMAIL_UPLOAD_DIR . '/CRON_LOCK' ) ) < 10 ) : ?>
			<div class="error inline"><p><strong><?php esc_html_e( 'Cron is currently running!', 'bulkmail' ); ?></strong></p></div>
			<?php endif; ?>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-cron-settings cron_opts cron"<?php echo $cron != 'cron' ? ' style="display:none"' : ''; ?>>
		<th scope="row"><?php esc_html_e( 'Cron Settings', 'bulkmail' ); ?>
			<p class="description">
				<?php printf( esc_html__( 'Use the alternative Cron URL if you have troubles with this one by clicking %s.', 'bulkmail' ), '<a class="switch-cron-url" href="#">' . esc_html__( 'here', 'bulkmail' ) . '</a>' ); ?>
			</p>
		</th>
		<td>
			<p>
			<input type="text" name="bulkmail_options[cron_secret]" value="<?php echo sanitize_key( bulkmail_option( 'cron_secret' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'a secret hash which is required to execute the cron', 'bulkmail' ); ?></span>
			</p>
			<?php $cron_url = bulkmail( 'cron' )->url(); ?>
			<?php $cron_url2 = bulkmail( 'cron' )->url( true ); ?>
			<?php $cron_path = bulkmail( 'cron' )->path( true ); ?>
			<p><?php esc_html_e( 'You can keep a browser window open with following URL', 'bulkmail' ); ?> (<a class="switch-cron-url" href="#"><?php esc_html_e( 'alternative Cron URL', 'bulkmail' ); ?></a>)</p>
			<div class="verified regular-cron-url"><a href="<?php echo $cron_url; ?>" class="external"><code id="copy-cronurl-1"><?php echo $cron_url; ?></code></a> <a class="clipboard" data-clipboard-target="#copy-cronurl-1"><?php esc_html_e( 'copy', 'bulkmail' ); ?></a></div>
			<div class="verified alternative-cron-url"><a href="<?php echo $cron_url2; ?>" class="external"><code id="copy-cronurl-2"><?php echo $cron_url2; ?></code></a> <a class="clipboard" data-clipboard-target="#copy-cronurl-2"><?php esc_html_e( 'copy', 'bulkmail' ); ?></a></div>
			<p><?php esc_html_e( 'or setup a crontab with one of the following commands:', 'bulkmail' ); ?></p>
			<ul>
			<li class="regular-cron-url"><code id="copy-cmd-1">wget -O- '<?php echo $cron_url; ?>' > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-1"><?php esc_html_e( 'copy', 'bulkmail' ); ?></a></li>
			<li class="alternative-cron-url"><code id="copy-cmd-2">wget -O- '<?php echo $cron_url2; ?>' > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-2"><?php esc_html_e( 'copy', 'bulkmail' ); ?></a></li>
			<li class="regular-cron-url"><code id="copy-cmd-3">curl --silent '<?php echo $cron_url; ?>'</code> <a class="clipboard" data-clipboard-target="#copy-cmd-3"><?php esc_html_e( 'copy', 'bulkmail' ); ?></a></li>
			<li class="alternative-cron-url"><code id="copy-cmd-4">curl --silent '<?php echo $cron_url2; ?>'</code> <a class="clipboard" data-clipboard-target="#copy-cmd-4"><?php esc_html_e( 'copy', 'bulkmail' ); ?></a></li>
			<li class="regular-cron-url"><code id="copy-cmd-5">GET '<?php echo $cron_url; ?>' > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-5"><?php esc_html_e( 'copy', 'bulkmail' ); ?></a></li>
			<li class="alternative-cron-url"><code id="copy-cmd-6">GET '<?php echo $cron_url2; ?>' > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-6"><?php esc_html_e( 'copy', 'bulkmail' ); ?></a></li>
			<li><code id="copy-cmd-7">php <?php echo $cron_path; ?> > /dev/null</code> <a class="clipboard" data-clipboard-target="#copy-cmd-7"><?php esc_html_e( 'copy', 'bulkmail' ); ?></a></li>
			</ul>
			<p class="description"><?php esc_html_e( 'You can setup an interval as low as one minute, but should consider a reasonable value of 5-15 minutes as well.', 'bulkmail' ); ?></p>
			<p class="description"><?php esc_html_e( 'If you need help setting up a cron job please refer to the documentation that your provider offers.', 'bulkmail' ); ?></p>
			<p class="description"><?php printf( esc_html__( 'You can also find additional help on our %s.', 'bulkmail' ), '<a href="https://emailmarketing.run/" class="external">' . esc_html__( 'knowledge base', 'bulkmail' ) . '</a>' ); ?></p>
		</td>
	</tr>
	<?php $last_hit = get_option( 'bulkmail_cron_lasthit' ); ?>
	<tr valign="top" class="settings-row settings-row-cron-lock">
		<th scope="row"><?php esc_html_e( 'Cron Lock', 'bulkmail' ); ?></th>
		<td>
			<?php if ( $last_hit && time() - $last_hit['timestamp'] > 720 && bulkmail( 'cron' )->is_locked() ) : ?>
				<div class="error inline">
				<p><?php printf( esc_html__( 'Looks like your Cron Lock is still in place after %1$s! Read more about why this can happen %2$s.', 'bulkmail' ), '<strong>' . human_time_diff( $last_hit['timestamp'] ) . '</strong>', '<a href="https://emailmarketing.run/" class="external">' . esc_html__( 'here', 'bulkmail' ) . '</a>' ); ?></p>
				</div>
			<?php endif; ?>
			<?php $cron_lock = bulkmail_option( 'cron_lock' ); ?>
			<select name="bulkmail_options[cron_lock]">
				<option value="file" <?php selected( $cron_lock, 'file' ); ?>><?php esc_html_e( 'File based', 'bulkmail' ); ?></option>
				<option value="db" <?php selected( $cron_lock, 'db' ); ?>><?php esc_html_e( 'Database based', 'bulkmail' ); ?></option>
			</select>
			<?php if ( bulkmail( 'cron' )->is_locked() ) : ?>
			<a href="edit.php?post_type=newsletter&page=bulkmail_settings&release-cronlock=1&_wpnonce=<?php echo wp_create_nonce( 'bulkmail-release-cronlock' ); ?>"><?php esc_html_e( 'Release Cron Lock', 'bulkmail' ); ?></a>
		<?php endif; ?>
			<p class="description"><?php esc_html_e( 'A Cron Lock ensures your cron is not overlapping and causing duplicate emails. Select which method you like to use.', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-last-hit">
		<th scope="row"><?php esc_html_e( 'Last hit', 'bulkmail' ); ?></th>
		<td>
		<ul class="lasthit highlight">
		<?php
		if ( $last_hit ) :
			?>
			<li>IP:
			<?php
			echo $last_hit['ip'];
			if ( $last_hit['ip'] == bulkmail_get_ip() ) {
				echo ' (' . esc_html__( 'probably you', 'bulkmail' ) . ')'; }
			?>
			</li>
			<li><?php echo $last_hit['user']; ?></li>
			<li><?php echo date( $timeformat, $last_hit['timestamp'] + $timeoffset ) . ', <strong>' . sprintf( esc_html__( '%s ago', 'bulkmail' ), human_time_diff( $last_hit['timestamp'] ) ) . '</strong>'; ?></li>
			<?php if ( $interv = round( ( $last_hit['timestamp'] - $last_hit['oldtimestamp'] ) / 60 ) ) : ?>
			<li><?php echo esc_html__( 'Interval', 'bulkmail' ) . ': <strong>' . $interv . ' ' . esc_html_x( 'min', 'short for minute', 'bulkmail' ) . '</strong>'; ?></li>
			<?php endif; ?>
			<?php if ( $last_hit['mail'] ) : ?>
				<?php $mails_per_sec = round( 1 / $last_hit['mail'], 2 ); ?>
			<li>
				<?php
				echo esc_html__( 'Throughput', 'bulkmail' ) . ': ' . round( $last_hit['mail'], 3 ) . ' ' . esc_html_x( 'sec', 'short for second', 'bulkmail' );
				echo '/' . esc_html__( 'mail', 'bulkmail' );
				?>
			 (<?php printf( esc_html__( _n( '%s mail per second', '%s mails per second', $mails_per_sec, 'bulkmail' ) ), $mails_per_sec ); ?>)</li>
			<?php endif; ?>
			<?php if ( $last_hit['timemax'] ) : ?>
			<li><?php echo esc_html__( 'Max Execution Time', 'bulkmail' ) . ': ' . round( $last_hit['timemax'], 3 ) . ' ' . esc_html_x( 'sec', 'short for second', 'bulkmail' ); ?></li>
			<?php endif; ?>
			<li><a href="edit.php?post_type=newsletter&page=bulkmail_settings&reset-lasthit=1&_wpnonce=<?php echo wp_create_nonce( 'bulkmail-reset-lasthit' ); ?>"><?php esc_html_e( 'Reset', 'bulkmail' ); ?></a></li>
		<?php else : ?>
			<li><strong><?php esc_html_e( 'never', 'bulkmail' ); ?></strong>
			(<a href="https://emailmarketing.run/" class="external"><?php esc_html_e( 'why?', 'bulkmail' ); ?></a>)</li>
		<?php endif; ?>
		</ul>
		</td>
	</tr>
</table>
