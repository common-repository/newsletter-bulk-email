<table class="form-table">
	<tr valign="top" class="settings-row settings-row-default-template">
		<th scope="row"><?php esc_html_e( 'Default Template', 'bulkmail' ); ?></th>
		<td><p><select name="bulkmail_options[default_template]" class="postform">
		<?php
		$templates = bulkmail( 'templates' )->get_templates();
		$selected  = bulkmail_option( 'default_template' );
		?>
		<?php foreach ( $templates as $slug => $data ) : ?>
			<option value="<?php echo $slug; ?>"<?php selected( $selected, $slug ); ?>><?php echo esc_html( $data['name'] ); ?></option>
		<?php endforeach; ?>
		</select> <a href="edit.php?post_type=newsletter&page=bulkmail_templates"><?php esc_html_e( 'show Templates', 'bulkmail' ); ?></a> | <a href="edit.php?post_type=newsletter&page=bulkmail_templates&more"><?php esc_html_e( 'get more', 'bulkmail' ); ?></a>
		</p></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-logo">
		<th scope="row"><?php esc_html_e( 'Logo', 'bulkmail' ); ?> *
		<p class="description"><?php esc_html_e( 'Use a logo for new created campaigns', 'bulkmail' ); ?></p>
		</th>
		<td>
		<?php bulkmail( 'helper' )->media_editor_link( bulkmail_option( 'logo', get_theme_mod( 'custom_logo' ) ), 'bulkmail_options[logo]', 'full' ); ?>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-logo-link">
		<th scope="row"><?php esc_html_e( 'Logo Link', 'bulkmail' ); ?> *</th>
		<td><input type="text" name="bulkmail_options[logo_link]" value="<?php echo esc_attr( bulkmail_option( 'logo_link' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'A link for your logo.', 'bulkmail' ); ?></span></td>
	</tr>
	<tr valign="top" class="settings-row settings-row-social-services">
		<th scope="row"><?php esc_html_e( 'Social Services', 'bulkmail' ); ?> *
		<p class="description"><?php esc_html_e( 'Use links to your social account in your campaigns', 'bulkmail' ); ?></p>
		</th>
		<td>
		<?php
			$social_links = bulkmail( 'helper' )->get_social_links( '%s', true );
			$services     = bulkmail_option( 'services', array() );
			$services     = array( '0' => '' ) + $services;
		?>
			<ul id="social-services">
		<?php foreach ( $services as $service => $username ) : ?>
				<li>
					<a href="" class="social-service-remove" title="<?php esc_attr_e( 'remove', 'bulkmail' ); ?>">&#10005;</a>
					<select class="social-service-dropdown">
						<option value="0"><?php esc_html_e( 'choose', 'bulkmail' ); ?></option>
					<?php foreach ( $social_links as $social_link_service => $link ) : ?>
						<option value="<?php echo esc_attr( $social_link_service ); ?>" data-url="<?php echo esc_attr( $link ); ?>" <?php selected( $service, $social_link_service ); ?>><?php echo esc_html( $social_link_service ); ?></option>
					<?php endforeach; ?>
					</select>
					<span class="social-service-url-field">
					<?php if ( $service ) : ?>
						<label><span class="description"><?php echo str_replace( '%s', '<input type="text" name="bulkmail_options[services][' . esc_attr( $service ) . ']" value="' . esc_attr( $username ) . '" class="regular-text">', $social_links[ $service ] ); ?></span></label>
					<?php endif; ?>
					</span>
				</li>
		<?php endforeach; ?>
			</ul>
			<a class="button social-service-add"><?php esc_html_e( 'add', 'bulkmail' ); ?></a>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-high-dpi">
		<th scope="row"><?php esc_html_e( 'High DPI', 'bulkmail' ); ?> *
		</th>
		<td>
			<p class="description"><label><input type="hidden" name="bulkmail_options[high_dpi]" value=""><input type="checkbox" name="bulkmail_options[high_dpi]" value="1" <?php checked( bulkmail_option( 'high_dpi' ) ); ?>> <?php esc_html_e( 'Use High DPI or retina ready images if available.', 'bulkmail' ); ?></label></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-template-notice">
		<th scope="row">&nbsp;</th>
		<td>
			<p class="description">* <?php esc_html_e( 'Depending on your used template these features may not work!', 'bulkmail' ); ?></p>
		</td>
	</tr>
</table>
