
<table class="form-table">

	<tr valign="top" class="settings-row settings-row-bounce-address">
		<th scope="row"><?php esc_html_e( 'Bounce Address', 'bulkmail' ); ?></th>
		<td><input type="text" name="bulkmail_options[bounce]" value="<?php echo esc_attr( bulkmail_option( 'bounce' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'Undeliverable emails will return to this address', 'bulkmail' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-enable-automatic-bounce-handling">
		<th scope="row">&nbsp;</th>
		<td><label><input type="hidden" name="bulkmail_options[bounce_active]" value=""><input type="checkbox" name="bulkmail_options[bounce_active]" id="bounce_active" value="1" <?php checked( bulkmail_option( 'bounce_active' ) ); ?>> <?php esc_html_e( 'Enable automatic bounce handling', 'bulkmail' ); ?></label>
		</td>
	</tr>

</table>
<div id="bounce-options"<?php echo ! bulkmail_option( 'bounce_active' ) ? ' style="display:none"' : ''; ?>>
	<table class="form-table">
		<tr valign="top" class="settings-row settings-row-if-you-would-like-to-enable-bouncing-you-have-to-setup-a-separate-mail-account">
			<th scope="row">&nbsp;</th>
			<td><p class="description"><?php esc_html_e( 'If you would like to enable bouncing you have to setup a separate mail account', 'bulkmail' ); ?></p></td>
		</tr>
	<?php if ( function_exists( 'imap_open' ) ) : ?>
		<tr valign="top" class="settings-row settings-row-service">
			<th scope="row"><?php esc_html_e( 'Service', 'bulkmail' ); ?></th>
			<td>
			<label><input type="radio" name="bulkmail_options[bounce_service]" value="pop3" <?php checked( bulkmail_option( 'bounce_service' ), 'pop3' ); ?>> POP3 </label>&nbsp;
			<label><input type="radio" name="bulkmail_options[bounce_service]" value="imap" <?php checked( bulkmail_option( 'bounce_service' ), 'imap' ); ?>> IMAP </label>&nbsp;
			<label><input type="radio" name="bulkmail_options[bounce_service]" value="nntp" <?php checked( bulkmail_option( 'bounce_service' ), 'nntp' ); ?>> NNTP </label>&nbsp;
			<label><input type="radio" name="bulkmail_options[bounce_service]" value="" <?php checked( ! bulkmail_option( 'bounce_service' ) ); ?>> POP3 (legacy)</label>
			</td>
		</tr>
	<?php endif; ?>
		<tr valign="top" class="settings-row settings-row-server-address-port">
			<th scope="row"><?php esc_html_e( 'Server Address : Port', 'bulkmail' ); ?></th>
			<td><input type="text" name="bulkmail_options[bounce_server]" value="<?php echo esc_attr( bulkmail_option( 'bounce_server' ) ); ?>" class="regular-text">:<input type="text" name="bulkmail_options[bounce_port]" id="bounce_port" value="<?php echo bulkmail_option( 'bounce_port' ); ?>" class="small-text"></td>
		</tr>
		<tr valign="top" class="settings-row settings-row-secure">
			<th scope="row"><?php esc_html_e( 'Secure', 'bulkmail' ); ?></th>
			<td>
			<label><input type="radio" name="bulkmail_options[bounce_secure]" value="" <?php checked( ! bulkmail_option( 'bounce_secure' ) ); ?>> <?php esc_html_e( 'none', 'bulkmail' ); ?></label>
			<label><input type="radio" name="bulkmail_options[bounce_secure]" value="ssl" <?php checked( bulkmail_option( 'bounce_secure' ), 'ssl' ); ?>> SSL </label>&nbsp;
			<label><input type="radio" name="bulkmail_options[bounce_secure]" value="tls" <?php checked( bulkmail_option( 'bounce_secure' ), 'tls' ); ?>> TLS </label>&nbsp;
			</td>
		</tr>
		<tr valign="top" class="settings-row settings-row-username">
			<th scope="row"><?php esc_html_e( 'Username', 'bulkmail' ); ?></th>
			<td><input type="text" name="bulkmail_options[bounce_user]" value="<?php echo esc_attr( bulkmail_option( 'bounce_user' ) ); ?>" class="regular-text"></td>
		</tr>
		<tr valign="top" class="settings-row settings-row-password">
			<th scope="row"><?php esc_html_e( 'Password', 'bulkmail' ); ?></th>
			<td><input type="password" name="bulkmail_options[bounce_pwd]" value="<?php echo esc_attr( bulkmail_option( 'bounce_pwd' ) ); ?>" class="regular-text" autocomplete="new-password"></td>
		</tr>
		<tr valign="top" class="settings-check-bounce-server wp_cron">
			<th scope="row"></th>
			<td><p><?php printf( esc_html__( 'Check bounce server every %s minutes for new messages', 'bulkmail' ), '<input type="text" name="bulkmail_options[bounce_check]" value="' . bulkmail_option( 'bounce_check' ) . '" class="small-text">' ); ?></p></td>
		</tr>
		<tr valign="top" class="settings-row settings-row-delete-message">
			<th scope="row"><?php esc_html_e( 'Delete messages', 'bulkmail' ); ?></th>
			<td><label><input type="hidden" name="bulkmail_options[bounce_delete]" value=""><input type="checkbox" name="bulkmail_options[bounce_delete]" value="1" <?php checked( bulkmail_option( 'bounce_delete' ) ); ?>> <?php esc_html_e( 'Delete messages without tracking code to keep postbox clear (recommended)', 'bulkmail' ); ?></label>
			</td>
		</tr>
		<tr valign="top" class="settings-soft-bounces wp_cron">
			<th scope="row"><?php esc_html_e( 'Soft Bounces', 'bulkmail' ); ?></th>
			<td><p><?php printf( esc_html__( 'Resend soft bounced mails after %s minutes', 'bulkmail' ), '<input type="text" name="bulkmail_options[bounce_delay]" value="' . bulkmail_option( 'bounce_delay' ) . '" class="small-text">' ); ?></p>
			<p>
			<?php
				$dropdown = '<select name="bulkmail_options[bounce_attempts]" class="postform">';
				$value    = bulkmail_option( 'bounce_attempts' );
			?>
			<?php
			for ( $i = 1; $i <= 10; $i++ ) {
				$selected  = ( $value == $i ) ? ' selected' : '';
				$dropdown .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
			}
			$dropdown .= '</select>';

			printf( esc_html__( '%s attempts to deliver message until hardbounce', 'bulkmail' ), $dropdown );
			?>
			</p>
			</td>
		</tr>
	</table>
	<table class="form-table">
		<tr valign="top" class="settings-row settings-row-test-bounce-settings">
			<th scope="row"></th>
			<td>
			<input type="button" value="<?php esc_attr_e( 'Test bounce settings', 'bulkmail' ); ?>" class="button bulkmail_bouncetest">
			<div class="loading bounce-ajax-loading"></div>
			<span class="bouncetest_status"></span>
			</td>
		</tr>
	</table>
</div>
