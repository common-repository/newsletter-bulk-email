<div class="bulkmail-dashboard">
<?php
	require BULKEMAIL_DIR . 'views/dashboard/mb-campaigns.php';
	require BULKEMAIL_DIR . 'views/dashboard/mb-subscribers.php';
?>
	<div class="versions">
		<span class="textleft">Bulkmail <?php echo esc_html( BULKEMAIL_VERSION ); ?></span>

		<?php
		if ( current_user_can( 'update_plugins' ) && ! is_plugin_active_for_network( BULKEMAIL_SLUG ) ) :
			$plugin_info = bulkmail()->plugin_info();
			$plugins     = get_site_transient( 'update_plugins' );
			if ( isset( $plugin_info->update ) && $plugin_info->update ) {
				?>
				<a href="update.php?action=upgrade-plugin&plugin=<?php echo urlencode( BULKEMAIL_SLUG ); ?>&_wpnonce=<?php echo wp_create_nonce( 'upgrade-plugin_' . BULKEMAIL_SLUG ); ?>" class="button button-primary alignright"><?php printf( esc_html__( 'Update to %s', 'bulkmail' ), $plugin_info->new_version ); ?></a>
				<?php
			}
		endif;
		?>
		<br class="clear">
	</div>
	<?php wp_nonce_field( 'bulkmail_nonce', 'bulkmail_nonce', false ); ?>
</div>
