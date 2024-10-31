<?php wp_nonce_field( 'bulkmail_nonce', 'bulkmail_nonce', false ); ?>
<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
<?php


$classes = array( 'wrap', 'bulkmail-dashboard' );
if ( $this->update ) {
	$classes[] = 'has-update';
}

?>
<div class="<?php echo implode( ' ', $classes ); ?>">
<h1><?php esc_html_e( 'Dashboard', 'bulkmail' ); ?></h1>

<?php if ( ! $this->verified && current_user_can( 'bulkmail_manage_licenses' ) ) : ?>
	<div id="bulkmail-register-panel" class="welcome-panel" style="display:block !important">
		<div class="welcome-panel-content">
			<p class="about-description"></p>
			<div class="welcome-panel-column-container">

			<h2 class="welcome-header"><?php esc_html_e( 'Register for News, Support and Updates related to Bulkmail.', 'bulkmail' ); ?></h2>

				<?php bulkmail( 'register' )->form(); ?>

			</div>

		</div>
	</div>
<?php elseif ( ! bulkmail_option( 'usage_tracking' ) && bulkmail_option( 'ask_usage_tracking' ) && ( time() - get_option( 'bulkmail_updated' ) ) > HOUR_IN_SECONDS && current_user_can( 'manage_options' ) ) : ?>
	<div class="info notice">
		<h2><?php esc_html_e( 'Help us improve Bulkmail automatically.', 'bulkmail' ); ?></h2>
		<p style="max-width: 800px;"><?php esc_html_e( 'If you enable this option we are able to track the usage of Bulkmail on your site. We don\'t record any sensitive data but only information regarding the WordPress environment and plugin settings, which we use to make improvements to the plugin. Tracking is completely optional and can be disabled anytime.', 'bulkmail' ); ?><br><a href="https://emailmarketing.run/" class="external"><?php esc_html_e( 'Read more about what we collect if you enable this option.', 'bulkmail' ); ?></a>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo wp_nonce_url( add_query_arg( 'bulkmail_allow_usage_tracking', 1 ), 'bulkmail_allow_usage_tracking', '_wpnonce' ); ?>"><?php esc_html_e( 'Yes, let me help you by enabling this option!', 'bulkmail' ); ?></a>
			<a class="button" href="<?php echo wp_nonce_url( add_query_arg( 'bulkmail_allow_usage_tracking', 0 ), 'bulkmail_allow_usage_tracking', '_wpnonce' ); ?>"><?php esc_html_e( 'No, I\'m not interested.', 'bulkmail' ); ?></a>
		</p>
	</div>
<?php endif; ?>
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="postbox-container-1" class="postbox-container" data-id="normal">
				<?php do_meta_boxes( $this->screen->id, 'normal', '' ); ?>
			</div>
			<div id="postbox-container-2" class="postbox-container" data-id="side">
				<?php do_meta_boxes( $this->screen->id, 'side', '' ); ?>
			</div>
			<div id="postbox-container-3" class="postbox-container" data-id="column3">
				<?php do_meta_boxes( $this->screen->id, 'column3', '' ); ?>
			</div>
			<div id="postbox-container-4" class="postbox-container" data-id="column4">
				<?php do_meta_boxes( $this->screen->id, 'column4', '' ); ?>
			</div>
		</div>
	</div>

<?php $addons = bulkmail( 'helper' )->get_addons(); ?>
<?php if ( $addons && ! is_wp_error( $addons ) ) : ?>
	<div id="addons-panel" class="welcome-panel">
		<div class="welcome-panel-content">
			<p class="about-description"></p>
			<div class="welcome-panel-column-container">

					<h2><?php esc_html_e( 'Supercharge Bulkmail!', 'bulkmail' ); ?></h2>
					<h3><?php printf( esc_html__( 'Bulkmail comes with %1$s extensions and supports %2$s premium templates. Get the most out of your email campaigns and start utilizing the vast amount of add ons.', 'bulkmail' ), count( $addons ), '80+' ); ?></h3>

					<div class="cta-buttons">
						<a class="button button-primary button-hero" href="edit.php?post_type=newsletter&page=bulkmail_addons"><?php esc_html_e( 'Browse Addons', 'bulkmail' ); ?></a>
						<a class="button button-primary button-hero" href="edit.php?post_type=newsletter&page=bulkmail_templates&more"><?php esc_html_e( 'Browse Templates', 'bulkmail' ); ?></a>
					</div>

			</div>
		</div>
	</div>
<?php endif; ?>

<div id="ajax-response"></div>
<br class="clear">
</div>
