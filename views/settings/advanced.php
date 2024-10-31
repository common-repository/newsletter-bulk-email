<p class="description"><?php esc_html_e( 'Some of these settings may affect your website. In normal circumstance it is not required to change anything on this page.', 'bulkmail' ); ?></p>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-usage-tracking">
		<th scope="row"><?php esc_html_e( 'Usage Tracking', 'bulkmail' ); ?></th>
		<td>
			<label><input type="hidden" name="bulkmail_options[usage_tracking]" value=""><input type="checkbox" name="bulkmail_options[usage_tracking]" value="1" <?php checked( bulkmail_option( 'usage_tracking' ) ); ?>> <?php esc_html_e( 'Enable usage tracking for this site.', 'bulkmail' ); ?></label> <p class="description"><?php esc_html_e( 'If you enable this option we are able to track the usage of Bulkmail on your site. We don\'t record any sensitive data but only information regarding the WordPress environment and plugin settings, which we use to make improvements to the plugin. Tracking is completely optional and can be disabled anytime.', 'bulkmail' ); ?><br><a href="https://emailmarketing.run/" class="external"><?php esc_html_e( 'Read more about what we collect if you enable this option.', 'bulkmail' ); ?></a></p>
			<input type="hidden" name="bulkmail_options[ask_usage_tracking]" value="<?php echo bulkmail_option( 'ask_usage_tracking' ); ?>">
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-cache">
		<th scope="row"><?php esc_html_e( 'Cache', 'bulkmail' ); ?></th>
		<td>
			<label><input type="hidden" name="bulkmail_options[disable_cache_frontpage]" value=""><input type="checkbox" name="bulkmail_options[disable_cache_frontpage]" value="1" <?php checked( bulkmail_option( 'disable_cache_frontpage' ) ); ?>> <?php esc_html_e( 'Disable Form Caching', 'bulkmail' ); ?></label> <p class="description"><?php esc_html_e( 'Enable this option if you have issue with the security nonce on Bulkmail forms.', 'bulkmail' ); ?></p>
			<br><label><input type="hidden" name="bulkmail_options[disable_cache]" value=""><input type="checkbox" name="bulkmail_options[disable_cache]" value="1" <?php checked( bulkmail_option( 'disable_cache' ) ); ?>> <?php esc_html_e( 'Disable Object Cache for Bulkmail', 'bulkmail' ); ?></label> <p class="description"><?php esc_html_e( 'If enabled Bulkmail doesn\'t use cache anymore. This causes an increase in page load time! This option is not recommended!', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-short-codes">
		<th scope="row"><?php esc_html_e( 'Short Codes', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[shortcodes]" value=""><input type="checkbox" name="bulkmail_options[shortcodes]" value="1" <?php checked( bulkmail_option( 'shortcodes' ) ); ?>> <?php esc_html_e( 'Process short codes in emails.', 'bulkmail' ); ?></label> <p class="description"><?php esc_html_e( 'Check this option to process short codes. This may cause unexpected results.', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-remove-data">
		<th scope="row"><?php esc_html_e( 'Remove Data', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[remove_data]" value=""><input type="checkbox" name="bulkmail_options[remove_data]" value="1" <?php checked( bulkmail_option( 'remove_data' ) ); ?>> <?php esc_html_e( 'Remove all data on plugin deletion', 'bulkmail' ); ?></label> <p class="description"><?php esc_html_e( 'Bulkmail will remove all it\'s data if you delete the plugin via the plugin page.', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-url-rewrite">
		<th scope="row"><?php esc_html_e( 'URL Rewrite', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[got_url_rewrite]" value=""><input type="checkbox" name="bulkmail_options[got_url_rewrite]" value="1" <?php checked( bulkmail_option( 'got_url_rewrite' ) ); ?>> <?php esc_html_e( 'Website supports URL rewrite', 'bulkmail' ); ?></label> <p class="description"><?php esc_html_e( 'Bulkmail detects this setting by default so change only if detection fails.', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-form-post-protection">
		<th scope="row"><?php esc_html_e( 'Form POST protection', 'bulkmail' ); ?></th>
		<td><input type="text" name="bulkmail_options[post_nonce]" value="<?php echo esc_attr( bulkmail_option( 'post_nonce' ) ); ?>" class="regular-text" style="width: 100px;"> <label><input type="hidden" name="bulkmail_options[use_post_nonce]" value=""><input type="checkbox" name="bulkmail_options[use_post_nonce]" value="1" <?php checked( bulkmail_option( 'use_post_nonce' ) ); ?>> <?php esc_html_e( 'Use on internal forms.', 'bulkmail' ); ?></label> <span class="description"><?php esc_html_e( 'Check if you have a heavy cached page and problems with invalid Security Nonce.', 'bulkmail' ); ?></span>
			<p class="description"><?php esc_html_e( 'A unique string to prevent form submissions via POST. Pass this value in a \'_nonce\' variable. Keep empty to disable test.', 'bulkmail' ); ?></p></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-legacy-hooks">
		<th scope="row"><?php esc_html_e( 'Legacy Hooks', 'bulkmail' ); ?></th>
		<td><label><input type="checkbox" name="bulkmail_options[legacy_hooks]" value="1" <?php checked( bulkmail_option( 'legacy_hooks' ) ); ?>> <?php esc_html_e( 'Enable legacy hooks', 'bulkmail' ); ?></label> <p class="description"><?php esc_html_e( 'If you still use deprecated MyMail hooks and filters you can keep them working by enabling this option.', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-php-mailer">
		<th scope="row"><?php esc_html_e( 'PHP Mailer', 'bulkmail' ); ?></th>
		<td>
		<?php $phpmailerversion = bulkmail_option( 'php_mailer' ); ?>
		<label><?php esc_html_e( 'Use version', 'bulkmail' ); ?>
		<select name="bulkmail_options[php_mailer]">
			<option value="0" <?php selected( ! $phpmailerversion ); ?>><?php esc_html_e( 'included in WordPress', 'bulkmail' ); ?></option>
			<option value="latest" <?php selected( 'latest', $phpmailerversion ); ?>><?php printf( esc_html__( 'latest (%s)', 'bulkmail' ), '5.2.26' ); ?></option>
		</select></label>
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
