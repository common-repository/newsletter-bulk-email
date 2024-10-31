<table class="form-table">
	<tr valign="top" class="settings-row settings-row-sync-wordpress-users">
		<th scope="row"><?php esc_html_e( 'Sync WordPress Users', 'bulkmail' ); ?></th>
		<td>
		<label><input type="hidden" name="bulkmail_options[sync]" value=""><input type="checkbox" name="bulkmail_options[sync]" value="1" <?php checked( bulkmail_option( 'sync' ) ); ?> id="sync_list_check"> <?php esc_html_e( 'Sync WordPress Users with Subscribers', 'bulkmail' ); ?></label>
		<p class="description"><?php esc_html_e( 'keep WordPress User data and Subscriber data synchronized. Only affects existing Subscribers', 'bulkmail' ); ?></p>
		</td>
	</tr>
</table>
<div id="sync_list"<?php echo ! bulkmail_option( 'sync' ) ? ' style="display:none"' : ''; ?>>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-meta-data-list">
		<th scope="row"><?php esc_html_e( 'Meta Data List', 'bulkmail' ); ?><p class="description"><?php esc_html_e( 'select the custom field which should sync with a certain meta field', 'bulkmail' ); ?></p></th>
		<td>
		<?php
		$synclist    = bulkmail_option( 'synclist', array() );
		$synclist    = array( '_' => '_' ) + $synclist;
		$meta_values = wp_parse_args( bulkmail( 'helper' )->get_wpuser_meta_fields(), array( 'user_login', 'user_nicename', 'user_email', 'user_url', 'display_name', 'first_name', 'last_name', 'nickname' ) );

		$i = 0;

		foreach ( $synclist as $field => $metavalue ) :
			$customfield_dropdown = '<option value="-1">--</option><optgroup label="' . esc_html__( 'Custom Fields', 'bulkmail' ) . '">';
			foreach ( array(
				'email'     => esc_html__( 'Email', 'bulkmail' ),
				'firstname' => esc_html__( 'Firstname', 'bulkmail' ),
				'lastname'  => esc_html__( 'Lastname', 'bulkmail' ),
			) as $key => $name ) {
				$customfield_dropdown .= '<option value="' . $key . '" ' . selected( $field, $key, false ) . '>' . $name . '</option>';
			}
			foreach ( $customfields as $key => $data ) {
				$customfield_dropdown .= '<option value="' . $key . '" ' . selected( $field, $key, false ) . '>' . $data['name'] . '</option>';
			}
			$customfield_dropdown .= '</optgroup>';
			$meta_value_dropdown   = '<option value="-1">--</option><optgroup label="' . esc_html__( 'Meta Fields', 'bulkmail' ) . '">';
			foreach ( $meta_values as $key ) {
				$meta_value_dropdown .= '<option value="' . $key . '" ' . selected( $metavalue, $key, false ) . '>' . $key . '</option>';
			}
			$meta_value_dropdown .= '</optgroup>';
			?>
			<div class="bulkmail_syncitem" title="<?php echo esc_attr( sprintf( esc_html__( '%1$s syncs with %2$s', 'bulkmail' ), $field, $metavalue ) ); ?>">
				<select name="bulkmail_options[synclist][<?php echo $i; ?>][meta]"><?php echo $meta_value_dropdown; ?>:</select> &#10234;
				<select name="bulkmail_options[synclist][<?php echo $i; ?>][field]"><?php echo $customfield_dropdown; ?>:</select>
				<a class="remove-sync-item">&#10005;</a>
			</div>
			<?php $i++; ?>
		<?php endforeach; ?>
			<a class="button" id="add_sync_item"><?php esc_html_e( 'add', 'bulkmail' ); ?></a>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-manually-sync">
		<th scope="row"><p class="description"><?php esc_html_e( 'manually sync all existing users based on the above settings. (save required)', 'bulkmail' ); ?></p></th>
		<td>
		<p>
			<button class="button sync-button" id="sync_subscribers_wp"><?php esc_html_e( 'Subscribers', 'bulkmail' ); ?> &#x21D2; <?php esc_html_e( 'WordPress Users', 'bulkmail' ); ?></button>
			<button class="button sync-button" id="sync_wp_subscribers"><?php esc_html_e( 'WordPress Users', 'bulkmail' ); ?> &#x21D2; <?php esc_html_e( 'Subscribers', 'bulkmail' ); ?></button>
			<span class="loading sync-ajax-loading"></span>
		</p>
		</td>
	</tr>
</table>
</div>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-delete-subscribers">
		<th scope="row"><?php esc_html_e( 'Delete Subscriber', 'bulkmail' ); ?></th>
		<td>
		<label><input type="hidden" name="bulkmail_options[delete_wp_subscriber]" value=""><input type="checkbox" name="bulkmail_options[delete_wp_subscriber]" value="1" <?php checked( bulkmail_option( 'delete_wp_subscriber' ) ); ?>> <?php esc_html_e( 'Delete Subscriber if the WordPress User gets deleted', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-delete-wordpress-users">
		<th scope="row"><?php esc_html_e( 'Delete WordPress User', 'bulkmail' ); ?></th>
		<td>
		<label>
		<?php if ( ! current_user_can( 'delete_users' ) ) : ?>
		<input type="hidden" name="bulkmail_options[delete_wp_user]" value="<?php echo ! ! bulkmail_option( 'delete_wp_user' ); ?>">
		<input type="hidden" name="bulkmail_options[delete_wp_user]" value=""><input type="checkbox" name="bulkmail_options[delete_wp_user]" value="1" <?php checked( bulkmail_option( 'delete_wp_user' ) ); ?> disabled readonly>
		<?php else : ?>
		<input type="hidden" name="bulkmail_options[delete_wp_user]" value=""><input type="checkbox" name="bulkmail_options[delete_wp_user]" value="1" <?php checked( bulkmail_option( 'delete_wp_user' ) ); ?>>
		<?php endif; ?>

		<?php esc_html_e( 'Delete WordPress User if the Subscriber gets deleted', 'bulkmail' ); ?></label>
			<p class="description"><?php esc_html_e( 'Attention! This option will remove assigned WordPress Users without further notice. You must have the capability to delete WordPress Users. Administrators and the current user can not get deleted with this option', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-registered-users">
		<th scope="row"><?php esc_html_e( 'Registered Users', 'bulkmail' ); ?></th>
		<td>
		<?php if ( get_option( 'users_can_register' ) ) : ?>
		<label><input type="hidden" name="bulkmail_options[register_signup]" value=""><input type="checkbox" name="bulkmail_options[register_signup]" value="1" <?php checked( bulkmail_option( 'register_signup' ) ); ?> class="users-register" data-section="users-register_signup"> <?php esc_html_e( 'new WordPress users can choose to sign up on the register page', 'bulkmail' ); ?></label>
		<?php else : ?>
		<p class="description"><?php printf( esc_html__( 'Allow %s to your blog to enable this option', 'bulkmail' ), '<a href="options-general.php">' . esc_html__( 'users to subscribe', 'bulkmail' ) . '</a>' ); ?></p>
		<?php endif; ?>
		</td>
	</tr>
</table>
<div id="users-register_signup"<?php echo ! get_option( 'users_can_register' ) || ! bulkmail_option( 'register_signup' ) ? ' style="display:none"' : ''; ?>>
	<table class="form-table">
		<tr valign="top" class="settings-row settings-row-user-register-signup">
			<th scope="row"></th>
			<td>
			<label><input type="hidden" name="bulkmail_options[register_signup_checked]" value=""><input type="checkbox" name="bulkmail_options[register_signup_checked]" value="1" <?php checked( bulkmail_option( 'register_signup_checked' ) ); ?>> <?php esc_html_e( 'checked by default', 'bulkmail' ); ?></label>
			<br><label><input type="hidden" name="bulkmail_options[register_signup_confirmation]" value=""><input type="checkbox" name="bulkmail_options[register_signup_confirmation]" value="1" <?php checked( bulkmail_option( 'register_signup_confirmation' ) ); ?>> <?php esc_html_e( 'send confirmation (double-opt-in)', 'bulkmail' ); ?></label>
			<p class="description"><?php esc_html_e( 'Subscribe them to these lists:', 'bulkmail' ); ?></p>
			<?php bulkmail( 'lists' )->print_it( null, null, 'bulkmail_options[register_signup_lists]', false, bulkmail_option( 'register_signup_lists' ) ); ?>
			</td>
		</tr>
	</table>
</div>

<table class="form-table">
	<tr valign="top" class="settings-row settings-row-new-comments">
		<th scope="row"><?php esc_html_e( 'New Comments', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[register_comment_form]" value=""><input type="checkbox" name="bulkmail_options[register_comment_form]" value="1" <?php checked( bulkmail_option( 'register_comment_form' ) ); ?> class="users-register" data-section="users-register_comment_form"> <?php esc_html_e( 'Allow users to signup on the comment form if they are currently not subscribed to any list', 'bulkmail' ); ?></label>
		</td>
	</tr>
</table>
<div id="users-register_comment_form"<?php echo ! bulkmail_option( 'register_comment_form' ) ? ' style="display:none"' : ''; ?>>
	<table class="form-table">
		<tr valign="top" class="settings-row settings-row-register-comment-form-options">
			<th scope="row"></th>
			<td>
			<p><label><input type="hidden" name="bulkmail_options[register_comment_form_checked]" value=""><input type="checkbox" name="bulkmail_options[register_comment_form_checked]" value="1" <?php checked( bulkmail_option( 'register_comment_form_checked' ) ); ?>> <?php esc_html_e( 'checked by default', 'bulkmail' ); ?></label></p>
			<p><?php esc_html_e( 'sign up only if comment is', 'bulkmail' ); ?><br>&nbsp;&nbsp;

			<label><input type="hidden" name="bulkmail_options[register_comment_form_status][]" value=""><input type="checkbox" name="bulkmail_options[register_comment_form_status][]" value="1" <?php checked( in_array( '1', bulkmail_option( 'register_comment_form_status', array() ) ), true ); ?>> <?php esc_html_e( 'approved', 'bulkmail' ); ?></label>
			<label><input type="checkbox" name="bulkmail_options[register_comment_form_status][]" value="0" <?php checked( in_array( '0', bulkmail_option( 'register_comment_form_status', array() ) ), true ); ?>> <?php esc_html_e( 'not approved', 'bulkmail' ); ?></label>
			<label><input type="checkbox" name="bulkmail_options[register_comment_form_status][]" value="spam" <?php checked( in_array( 'spam', bulkmail_option( 'register_comment_form_status', array() ) ), true ); ?>> <?php esc_html_e( 'spam', 'bulkmail' ); ?></label>
			</p>
			<br><label><input type="hidden" name="bulkmail_options[register_comment_form_confirmation]" value=""><input type="checkbox" name="bulkmail_options[register_comment_form_confirmation]" value="1" <?php checked( bulkmail_option( 'register_comment_form_confirmation' ) ); ?>> <?php esc_html_e( 'send confirmation (double-opt-in)', 'bulkmail' ); ?></label>
			<p class="description"><?php esc_html_e( 'Subscribe them to these lists:', 'bulkmail' ); ?></p>
			<?php bulkmail( 'lists' )->print_it( null, null, 'bulkmail_options[register_comment_form_lists]', false, bulkmail_option( 'register_comment_form_lists' ) ); ?>
		</td>
		</tr>
	</table>
</div>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-others">
		<th scope="row"><?php esc_html_e( 'Others', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[register_other]" value=""><input type="checkbox" name="bulkmail_options[register_other]" value="1" <?php checked( bulkmail_option( 'register_other' ) ); ?> class="users-register" data-section="users-register_other"> <?php esc_html_e( 'Add people who are added via the backend or any third party plugin', 'bulkmail' ); ?></label>
		</td>
	</tr>
</table>
<div id="users-register_other"<?php echo ! bulkmail_option( 'register_other' ) ? ' style="display:none"' : ''; ?>>
	<table class="form-table">
		<tr valign="top" class="settings-row settings-row-others-options">
			<th scope="row"></th>
			<td>
			<p><label><input type="hidden" name="bulkmail_options[register_other_confirmation]" value=""><input type="checkbox" name="bulkmail_options[register_other_confirmation]" value="1" <?php checked( bulkmail_option( 'register_other_confirmation' ) ); ?>> <?php esc_html_e( 'send confirmation (double-opt-in)', 'bulkmail' ); ?></label></p>
			<p class="description"><?php esc_html_e( 'Subscribe them to these lists:', 'bulkmail' ); ?></p>
			<?php bulkmail( 'lists' )->print_it( null, null, 'bulkmail_options[register_other_lists]', false, bulkmail_option( 'register_other_lists' ) ); ?>
			<p class="description"><?php esc_html_e( 'only with these user roles:', 'bulkmail' ); ?></p>
			<ul>
			<?php
			$set = bulkmail_option( 'register_other_roles', array() );
			foreach ( $roles as $role => $name ) :
				echo '<li><input type="checkbox" name="bulkmail_options[register_other_roles][]" value="' . esc_attr( $role ) . '" ' . checked( in_array( $role, $set ), true, false ) . '> ' . esc_html( $name ) . '</li>';
			endforeach;
			?>
			</ul>
			</td>
		</tr>
	</table>
</div>
