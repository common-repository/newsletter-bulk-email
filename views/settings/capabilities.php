<p class="description"><?php esc_html_e( 'Define capabilities for each user role. To add new roles you can use a third party plugin. Administrator has always all privileges', 'bulkmail' ); ?></p>
<div id="current-cap"></div>
<table class="form-table">
<?php

unset( $roles['administrator'] );

?>
<tr valign="top" class="settings-row settings-row-capabilities">
	<td>
		<table id="capabilities-table">
			<thead>
				<tr>
				<th>&nbsp;</th>
				<?php foreach ( $roles as $role => $name ) : ?>
					<th><input type="hidden" name="bulkmail_options[roles][<?php echo esc_attr( $role ); ?>][]" value=""><?php echo esc_html( $name ); ?> <input type="checkbox" class="selectall" value="<?php echo esc_attr( $role ); ?>" title="<?php echo esc_html__( 'toggle all', 'bulkmail' ); ?>"></th>
				<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>

		<?php require BULKEMAIL_DIR . 'includes/capability.php'; ?>

		<?php foreach ( $bulkmail_capabilities as $capability => $data ) : ?>
			<tr><th><?php echo esc_html( $data['title'] ); ?></th>
			<?php foreach ( $roles as $role => $name ) : ?>
				<?php $r = get_role( $role ); ?>
				<td><label title="<?php printf( esc_html__( '%1$s can %2$s', 'bulkmail' ), $name, $data['title'] ); ?>"><input name="bulkmail_options[roles][<?php echo esc_attr( $role ); ?>][]" type="checkbox" class="cap-check-<?php echo esc_attr( $role ); ?>" value="<?php echo esc_attr( $capability ); ?>" <?php echo checked( ! empty( $r->capabilities[ $capability ] ), 1, false ); ?> <?php echo ( $role == 'administrator' ? 'readonly' : '' ); ?>></label></td>
			<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
			</tbody>
		</table>
	</td>
</tr>
</table>
<p>
<a onclick='return confirm("<?php esc_html_e( 'Do you really like to reset all capabilities? This cannot be undone!', 'bulkmail' ); ?>");' href="edit.php?post_type=newsletter&page=bulkmail_settings&reset-capabilities=1&_wpnonce=<?php echo wp_create_nonce( 'bulkmail-reset-capabilities' ); ?>"><?php esc_html_e( 'Reset all capabilities', 'bulkmail' ); ?></a>
</p>
