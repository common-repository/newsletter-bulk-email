<table class="form-table">
	<tr valign="top" class="settings-row settings-row-from-name">
		<th scope="row"><?php esc_html_e( 'From Name', 'bulkmail' ); ?> *</th>
		<td><input type="text" name="bulkmail_options[from_name]" value="<?php echo esc_attr( bulkmail_option( 'from_name' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The sender name which is displayed in the from field', 'bulkmail' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-from-email">
		<th scope="row"><?php esc_html_e( 'From Email', 'bulkmail' ); ?> *</th>
		<td><input type="text" name="bulkmail_options[from]" value="<?php echo esc_attr( bulkmail_option( 'from' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The sender email address. Force your receivers to whitelabel this email address.', 'bulkmail' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-reply-to-email">
		<th scope="row"><?php esc_html_e( 'Reply-to Email', 'bulkmail' ); ?> *</th>
		<td><input type="text" name="bulkmail_options[reply_to]" value="<?php echo esc_attr( bulkmail_option( 'reply_to' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The address users can reply to', 'bulkmail' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-send-delay">
		<th scope="row"><?php esc_html_e( 'Send delay', 'bulkmail' ); ?> *</th>
		<td><input type="text" name="bulkmail_options[send_offset]" value="<?php echo esc_attr( bulkmail_option( 'send_offset' ) ); ?>" class="small-text"> <span class="description"><?php esc_html_e( 'The default delay in minutes for sending campaigns.', 'bulkmail' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-delivery-by-time-zone">
		<th scope="row"><?php esc_html_e( 'Delivery by Time Zone', 'bulkmail' ); ?> *</th>
		<td><label><input type="hidden" name="bulkmail_options[timezone]" value=""><input type="checkbox" name="bulkmail_options[timezone]" value="1" <?php checked( bulkmail_option( 'timezone' ) ); ?>> <?php esc_html_e( 'Send Campaigns based on the subscribers timezone if known', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-embed-images">
		<th scope="row"><?php esc_html_e( 'Embed Images', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[embed_images]" value=""><input type="checkbox" name="bulkmail_options[embed_images]" value="1" <?php checked( bulkmail_option( 'embed_images' ) ); ?>> <?php esc_html_e( 'Embed images in the mail', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-module-thumbnails">
		<th scope="row"><?php esc_html_e( 'Module Thumbnails', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[module_thumbnails]" value=""><input type="checkbox" name="bulkmail_options[module_thumbnails]" value="1" <?php checked( bulkmail_option( 'module_thumbnails' ) ); ?>> <?php esc_html_e( 'Show thumbnails of modules in the editor if available', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-post-list-count">
		<th scope="row"><?php esc_html_e( 'Post List Count', 'bulkmail' ); ?></th>
		<td><input type="text" name="bulkmail_options[post_count]" value="<?php echo esc_attr( bulkmail_option( 'post_count' ) ); ?>" class="small-text"> <span class="description"><?php esc_html_e( 'Number of posts or images displayed at once in the editbar.', 'bulkmail' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-auto-update">
		<th scope="row"><?php esc_html_e( 'Auto Update', 'bulkmail' ); ?></th>
		<td>
		<?php
		$is    = bulkmail_option( 'autoupdate', 'minor' );
		$types = array(
			'1'     => esc_html__( 'enabled', 'bulkmail' ),
			'0'     => esc_html__( 'disabled', 'bulkmail' ),
			'minor' => esc_html__( 'only minor updates', 'bulkmail' ),
		);
		?>
		<select name="bulkmail_options[autoupdate]">
			<?php foreach ( $types as $value => $name ) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $is == $value ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select>
		<p class="description"><?php esc_html_e( 'auto updates are recommended for important fixes.', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-system-mails">
		<th scope="row"><?php esc_html_e( 'System Mails', 'bulkmail' ); ?><a class="infolink external" href="https://emailmarketing.run/"></a>
		<p class="description"><?php esc_html_e( 'Decide how Bulkmail uses the wp_mail function.', 'bulkmail' ); ?></p>
		</th>
		<td>
		<p><label><input type="radio" name="bulkmail_options[system_mail]" class="system_mail" value="0" <?php checked( ! bulkmail_option( 'system_mail' ) ); ?>> <?php esc_html_e( 'Do not use Bulkmail for outgoing WordPress mails', 'bulkmail' ); ?></label></p>
		<p><label><input type="radio" name="bulkmail_options[system_mail]" class="system_mail" value="1" <?php checked( bulkmail_option( 'system_mail' ) == 1 ); ?>> <?php esc_html_e( 'Use Bulkmail for all outgoing WordPress mails', 'bulkmail' ); ?></label><br>
			<label><input type="radio" name="bulkmail_options[system_mail]" class="system_mail" value="template" <?php checked( bulkmail_option( 'system_mail' ) == 'template' ); ?>> <?php esc_html_e( 'Use only the template for all outgoing WordPress mails', 'bulkmail' ); ?></label></p>
		<p>&nbsp;&nbsp;<?php esc_html_e( 'use', 'bulkmail' ); ?>
		<?php
		bulkmail( 'helper' )->notifcation_template_dropdown( bulkmail_option( 'system_mail_template', 'notification.html' ), 'bulkmail_options[system_mail_template]', ! bulkmail_option( 'system_mail' ) );
		esc_html_e( 'and', 'bulkmail' );
		?>
			<select name="bulkmail_options[respect_content_type]"<?php echo ! bulkmail_option( 'system_mail' ) ? ' disabled' : ''; ?>>
				<option value="0" <?php selected( ! bulkmail_option( 'respect_content_type' ) ); ?>><?php esc_html_e( 'ignore', 'bulkmail' ); ?></option>
				<option value="1" <?php selected( bulkmail_option( 'respect_content_type' ) ); ?>><?php esc_html_e( 'respect', 'bulkmail' ); ?></option>
			</select>
			<?php esc_html_e( 'third party content type settings.', 'bulkmail' ); ?>
		</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-charset-encoding">
		<th scope="row"><?php esc_html_e( 'CharSet', 'bulkmail' ); ?> / <?php esc_html_e( 'Encoding', 'bulkmail' ); ?></th>
		<td>
		<?php
		$is       = bulkmail_option( 'charset', 'UTF-8' );
		$charsets = array(
			'UTF-8'       => 'Unicode 8',
			'ISO-8859-1'  => 'Western European',
			'ISO-8859-2'  => 'Central European',
			'ISO-8859-3'  => 'South European',
			'ISO-8859-4'  => 'North European',
			'ISO-8859-5'  => 'Latin/Cyrillic',
			'ISO-8859-6'  => 'Latin/Arabic',
			'ISO-8859-7'  => 'Latin/Greek',
			'ISO-8859-8'  => 'Latin/Hebrew',
			'ISO-8859-9'  => 'Turkish',
			'ISO-8859-10' => 'Nordic',
			'ISO-8859-11' => 'Latin/Thai',
			'ISO-8859-13' => 'Baltic Rim',
			'ISO-8859-14' => 'Celtic',
			'ISO-8859-15' => 'Western European revision',
			'ISO-8859-16' => 'South-Eastern European',
		)
		?>
		<select name="bulkmail_options[charset]">
			<?php foreach ( $charsets as $code => $region ) : ?>
			<option value="<?php echo $code; ?>" <?php selected( $is == $code ); ?>><?php echo $code; ?> - <?php echo $region; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
		$is       = bulkmail_option( 'encoding', '8bit' );
		$encoding = array(
			'8bit'             => '8bit',
			'7bit'             => '7bit',
			'binary'           => 'binary',
			'base64'           => 'base64',
			'quoted-printable' => 'quoted-printable',
		)
		?>
		 /
		<select name="bulkmail_options[encoding]">
			<?php foreach ( $encoding as $code ) : ?>
			<option value="<?php echo $code; ?>" <?php selected( $is == $code ); ?>><?php echo $code; ?></option>
			<?php endforeach; ?>
		</select>
		<p class="description"><?php esc_html_e( 'change Charset and encoding of your mails if you have problems with some characters', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-google-api-key">
		<th scope="row"><?php esc_html_e( 'Google API Key', 'bulkmail' ); ?>
		</th>
		<td><input type="password" name="bulkmail_options[google_api_key]" value="<?php echo esc_attr( bulkmail_option( 'google_api_key' ) ); ?>" class="regular-text" autocomplete="new-password">
		<p class="description">
		<?php esc_html_e( 'The Google API key is used to display Maps for Bulkmail on the back end.', 'bulkmail' ); ?><br>
		<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" class="external"><?php esc_html_e( 'Get your Google API Key.', 'bulkmail' ); ?></a></p>
		</td>
	</tr>
</table>
<p class="description">* <?php esc_html_e( 'can be changed in each campaign', 'bulkmail' ); ?></p>
