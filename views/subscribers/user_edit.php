<?php if ( current_user_can( 'bulkmail_edit_subscribers' ) && $subscriber = $this->get_by_wpid( $user->ID, true ) ) : ?>

	<h3>Bulkmail</h3>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><label for="user_login"><?php esc_html_e( 'Profile', 'bulkmail' ); ?></label></th>
			<td>
				<a class="button" href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=bulkmail_subscribers&ID=' . $subscriber->ID ); ?>">
					<?php IS_PROFILE_PAGE ? esc_html_e( 'Edit my Bulkmail Profile', 'bulkmail' ) : esc_html_e( 'Edit Users Bulkmail Profile', 'bulkmail' ); ?>
				</a>
			</td>
		</tr>
	</table>

<?php elseif ( current_user_can( 'bulkmail_add_subscribers' ) ) : ?>

	<h3>Bulkmail</h3>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><label for="user_login"><?php esc_html_e( 'Create', 'bulkmail' ); ?></label></th>
			<td>
				<a class="button" href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=bulkmail_subscribers&new&wp_user=' . $user->ID . '&_wpnonce=' . wp_create_nonce( 'bulkmail_nonce' ) ); ?>">
					<?php IS_PROFILE_PAGE ? esc_html_e( 'Create Bulkmail Subscriber', 'bulkmail' ) : esc_html_e( 'Create Bulkmail Subscriber', 'bulkmail' ); ?>
				</a>
			</td>
		</tr>
	</table>

<?php endif; ?>
