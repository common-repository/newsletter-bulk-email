<table class="form-table">
	<tr valign="top" class="settings-row settings-row-notification">
		<th scope="row"><?php esc_html_e( 'Notification', 'bulkmail' ); ?></th>
		<td>
		<p>
			<label><input type="hidden" name="bulkmail_options[subscriber_notification]" value=""><input type="checkbox" name="bulkmail_options[subscriber_notification]" value="1" <?php checked( bulkmail_option( 'subscriber_notification' ) ); ?>> <?php esc_html_e( 'Send a notification of new subscribers to following receivers (comma separated)', 'bulkmail' ); ?> <input type="text" name="bulkmail_options[subscriber_notification_receviers]" value="<?php echo esc_attr( bulkmail_option( 'subscriber_notification_receviers' ) ); ?>" class="regular-text"></label>
			<br>&nbsp;&nbsp;<?php esc_html_e( 'use', 'bulkmail' ); ?>
			<?php bulkmail( 'helper' )->notifcation_template_dropdown( bulkmail_option( 'subscriber_notification_template', 'notification.html' ), 'bulkmail_options[subscriber_notification_template]' ); ?>
			<br>&nbsp;&nbsp;<?php esc_html_e( 'send', 'bulkmail' ); ?>
			<select name="bulkmail_options[subscriber_notification_delay]">
			<?php $selected = bulkmail_option( 'subscriber_notification_delay' ); ?>
				<option value="0"<?php selected( ! $selected ); ?>><?php esc_html_e( 'immediately', 'bulkmail' ); ?></option>
				<option value="day"<?php selected( 'day' == $selected ); ?>><?php esc_html_e( 'daily', 'bulkmail' ); ?></option>
				<option value="week"<?php selected( 'week' == $selected ); ?>><?php esc_html_e( 'weekly', 'bulkmail' ); ?></option>
				<option value="month"<?php selected( 'month' == $selected ); ?>><?php esc_html_e( 'monthly', 'bulkmail' ); ?></option>
			</select>
		</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-notification">
		<th scope="row">&nbsp;</th>
		<td>
		<p>
			<label><input type="hidden" name="bulkmail_options[unsubscribe_notification]" value=""><input type="checkbox" name="bulkmail_options[unsubscribe_notification]" value="1" <?php checked( bulkmail_option( 'unsubscribe_notification' ) ); ?>> <?php esc_html_e( 'Send a notification if subscribers cancel their subscription to following receivers (comma separated)', 'bulkmail' ); ?> <input type="text" name="bulkmail_options[unsubscribe_notification_receviers]" value="<?php echo esc_attr( bulkmail_option( 'unsubscribe_notification_receviers' ) ); ?>" class="regular-text"></label>
			<br>&nbsp;&nbsp;<?php esc_html_e( 'use', 'bulkmail' ); ?>
			<?php bulkmail( 'helper' )->notifcation_template_dropdown( bulkmail_option( 'unsubscribe_notification_template', 'notification.html' ), 'bulkmail_options[unsubscribe_notification_template]' ); ?>

			<br>&nbsp;&nbsp;<?php esc_html_e( 'send', 'bulkmail' ); ?>
			<select name="bulkmail_options[unsubscribe_notification_delay]">
			<?php $selected = bulkmail_option( 'unsubscribe_notification_delay' ); ?>
				<option value="0"<?php selected( ! $selected ); ?>><?php esc_html_e( 'immediately', 'bulkmail' ); ?></option>
				<option value="day"<?php selected( 'day' == $selected ); ?>><?php esc_html_e( 'daily', 'bulkmail' ); ?></option>
				<option value="week"<?php selected( 'week' == $selected ); ?>><?php esc_html_e( 'weekly', 'bulkmail' ); ?></option>
				<option value="month"<?php selected( 'month' == $selected ); ?>><?php esc_html_e( 'monthly', 'bulkmail' ); ?></option>
			</select>
		</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-list-based-subscription">
		<th scope="row"><?php esc_html_e( 'List Based Subscription', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[list_based_opt_in]" value=""><input type="checkbox" name="bulkmail_options[list_based_opt_in]" value="1" <?php checked( bulkmail_option( 'list_based_opt_in' ) ); ?>> <?php esc_html_e( 'Subscribers sign up on a per list basis instead of globally.', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-single-opt-out">
		<th scope="row"><?php esc_html_e( 'Single-Opt-Out', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[single_opt_out]" value=""><input type="checkbox" name="bulkmail_options[single_opt_out]" value="1" <?php checked( bulkmail_option( 'single_opt_out' ) ); ?>> <?php esc_html_e( 'Subscribers instantly signed out after clicking the unsubscribe link in mails', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-mail-app-unsubscribe">
		<th scope="row"><?php esc_html_e( 'Mail App Unsubscribe', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[mail_opt_out]" value=""><input type="checkbox" name="bulkmail_options[mail_opt_out]" value="1" <?php checked( bulkmail_option( 'mail_opt_out' ) ); ?>> <?php esc_html_e( 'Allow Subscribers to opt out from their mail application if applicable.', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-name-order">
		<th scope="row"><?php esc_html_e( 'Name Order', 'bulkmail' ); ?></th>
		<td>
		<select name="bulkmail_options[name_order]">
			<option value="0"<?php selected( ! bulkmail_option( 'name_order' ) ); ?>><?php esc_html_e( 'Firstname', 'bulkmail' ); ?> <?php esc_html_e( 'Lastname', 'bulkmail' ); ?></option>
			<option value="1"<?php selected( bulkmail_option( 'name_order' ) ); ?>><?php esc_html_e( 'Lastname', 'bulkmail' ); ?> <?php esc_html_e( 'Firstname', 'bulkmail' ); ?></option>
		</select>
		<p class="description"><?php printf( esc_html__( 'Define in which order names appear in your language or country. This is used for the %s tag.', 'bulkmail' ), '<code>{fullname}</code>' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-custom-fields">
		<th scope="row"><?php esc_html_e( 'Custom Fields', 'bulkmail' ); ?>:
			<p class="description"><?php esc_html_e( 'Custom field tags are individual tags for each subscriber. You can ask for them on subscription and/or make it a required field.', 'bulkmail' ); ?></p>
			<p class="description"><?php esc_html_e( 'You have to enable Custom fields for each form:', 'bulkmail' ); ?><br><a href="edit.php?post_type=newsletter&page=bulkmail_forms"><?php esc_html_e( 'Forms', 'bulkmail' ); ?></a></p>
		</th>
		<td>
		<input type="hidden" name="bulkmail_options[custom_field][0]" value="empty">
			<div class="customfields">
		<?php if ( $customfields ) : ?>
			<?php
			$types = array(
				'textfield' => esc_html__( 'Textfield', 'bulkmail' ),
				'textarea'  => esc_html__( 'Textarea', 'bulkmail' ),
				'dropdown'  => esc_html__( 'Dropdown Menu', 'bulkmail' ),
				'radio'     => esc_html__( 'Radio Buttons', 'bulkmail' ),
				'checkbox'  => esc_html__( 'Checkbox', 'bulkmail' ),
				'date'      => esc_html__( 'Date', 'bulkmail' ),
			);
			?>
			<?php foreach ( $customfields as $id => $data ) : ?>
				<div class="customfield">
					<a class="customfield-move-up" title="<?php esc_attr_e( 'move up', 'bulkmail' ); ?>">&#9650;</a>
					<a class="customfield-move-down" title="<?php esc_attr_e( 'move down', 'bulkmail' ); ?>">&#9660;</a>
					<div><span class="label"><?php esc_html_e( 'Field Name', 'bulkmail' ); ?>:</span><label><input type="text" name="bulkmail_options[custom_field][<?php echo $id; ?>][name]" value="<?php echo esc_attr( $data['name'] ); ?>" class="regular-text customfield-name"></label></div>
					<div><span class="label"><?php esc_html_e( 'Tag', 'bulkmail' ); ?>:</span><span><code>{</code><input type="text" name="bulkmail_options[custom_field][<?php echo $id; ?>][id]" value="<?php echo sanitize_key( $id ); ?>" class="code"><code>}</code></span></div>
					<div><span class="label"><?php esc_html_e( 'Type', 'bulkmail' ); ?>:</span><select class="customfield-type" name="bulkmail_options[custom_field][<?php echo $id; ?>][type]">
					<?php
					foreach ( $types as $value => $name ) {
						echo '<option value="' . $value . '" ' . selected( $data['type'], $value, false ) . '>' . esc_attr( $name ) . '</option>';
					}
					?>
					</select>
				</div>
				<ul class="customfield-additional customfield-dropdown customfield-radio"<?php echo in_array( $data['type'], array( 'dropdown', 'radio' ) ) ? ' style="display:block"' : ''; ?>>
					<li>
					<ul class="customfield-values">
						<?php $values = ! empty( $data['values'] ) ? $data['values'] : array( '' ); ?>
						<?php foreach ( $values as $value ) : ?>
						<li>
							<span>&nbsp;</span>
							<span class="customfield-value-box"><input type="text" name="bulkmail_options[custom_field][<?php echo $id; ?>][values][]" class="regular-text customfield-value" value="<?php echo $value; ?>">
								<label><input type="radio" name="bulkmail_options[custom_field][<?php echo $id; ?>][default]" value="<?php echo $value; ?>" title="<?php esc_attr_e( 'this field is selected by default', 'bulkmail' ); ?>" <?php checked( isset( $data['default'] ) && $data['default'], true ); ?><?php disabled( ! in_array( $data['type'], array( 'dropdown', 'radio' ) ) ); ?>>
										<?php esc_html_e( 'default', 'bulkmail' ); ?>
								</label> &nbsp; <a class="customfield-value-remove" title="<?php esc_attr_e( 'remove field', 'bulkmail' ); ?>">&#10005;</a>
							</span>
						</li>
						<?php endforeach; ?>
					</ul>
					<span>&nbsp;</span> <a class="customfield-value-add"><?php esc_html_e( 'add field', 'bulkmail' ); ?></a>
					</li>
				</ul>
				<?php if ( 'checkbox' == $data['type'] ) : ?>
					<div class="customfield-additional customfield-checkbox" style="display:block">
						<span>&nbsp;</span>
						<label><input type="hidden" name="bulkmail_options[custom_field][<?php echo $id; ?>][default]" value=""><input type="checkbox" name="bulkmail_options[custom_field][<?php echo $id; ?>][default]" value="1" title="<?php esc_attr_e( 'this field is selected by default', 'bulkmail' ); ?>"<?php checked( isset( $data['default'] ) && $data['default'], true ); ?>>
						<?php esc_html_e( 'checked by default', 'bulkmail' ); ?>
						</label>
					</div>
				<?php endif; ?>
					<a class="customfield-remove"><?php esc_html_e( 'remove field', 'bulkmail' ); ?></a>
					<br>
				</div>
		<?php endforeach; ?>
	<?php endif; ?>
			</div>
			<input type="button" value="<?php esc_attr_e( 'add', 'bulkmail' ); ?>" class="button" id="bulkmail_add_field">
		</td>
	</tr>
</table>
