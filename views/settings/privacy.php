<table class="form-table">
	<tr valign="top" class="settings-row settings-row-tracking">
		<th scope="row"><?php esc_html_e( 'Tracking', 'bulkmail' ); ?></th>
		<td>
		<p><label><input type="hidden" name="bulkmail_options[track_opens]" value=""><input type="checkbox" name="bulkmail_options[track_opens]" value="1" <?php checked( bulkmail_option( 'track_opens' ) ); ?>> <?php esc_html_e( 'Track opens in your campaigns', 'bulkmail' ); ?></label></p>
		<p><label><input type="hidden" name="bulkmail_options[track_clicks]" value=""><input type="checkbox" name="bulkmail_options[track_clicks]" value="1" <?php checked( bulkmail_option( 'track_clicks' ) ); ?>> <?php esc_html_e( 'Track clicks in your campaigns', 'bulkmail' ); ?></label></p>

		<?php
			$geoip                 = isset( $_GET['nogeo'] ) ? false : bulkmail_option( 'track_location' );
			$geo_db_file_countries = bulkmail( 'geo' )->get_file_path( 'country' );
			$geo_db_file_cities    = bulkmail( 'geo' )->get_file_path( 'city' );
		?>

		<p><label><input type="hidden" name="bulkmail_options[track_location]" value=""><input type="checkbox" id="bulkmail_geoip" name="bulkmail_options[track_location]" value="1" <?php checked( $geoip ); ?>> <?php esc_html_e( 'Track location in campaigns', 'bulkmail' ); ?>*</label>
			<br>&nbsp;&#x2514;&nbsp;<label><input type="hidden" name="bulkmail_options[track_location_update]" value=""><input type="checkbox" name="bulkmail_options[track_location_update]" value="1" <?php checked( bulkmail_option( 'track_location_update' ) ); ?>> <?php esc_html_e( 'Update location database automatically', 'bulkmail' ); ?></label>
		</p>

	<?php if ( ! bulkmail()->is( 'setup' ) && $geoip && is_file( $geo_db_file_cities ) ) : ?>
		<p class="description"><?php esc_html_e( 'If you don\'t find your country down below the geo database is missing or corrupt', 'bulkmail' ); ?></p>
		<p>
	<strong><?php esc_html_e( 'Your IP', 'bulkmail' ); ?>:</strong> <?php echo bulkmail_get_ip(); ?><?php if ( bulkmail_is_local() ) : ?>
	<strong><?php esc_html_e( 'Geolocation is not available on localhost!', 'bulkmail' ); ?></strong>
	<?php endif; ?><br>
		<strong><?php esc_html_e( 'Your country', 'bulkmail' ); ?>:</strong> <?php echo bulkmail_ip2Country( '', 'name' ); ?><br>
		<?php if ( is_file( $geo_db_file_cities ) ) : ?>
		<strong><?php esc_html_e( 'Your city', 'bulkmail' ); ?>:</strong> <?php echo bulkmail_ip2City( '', 'city' ); ?>
	<?php endif; ?>
		</p>
		<p><button id="load_location_db" class="button-primary" <?php disabled( ! $geoip ); ?>><?php esc_html_e( 'Update Location Database', 'bulkmail' ); ?></button>&nbsp;<span class="loading geo-ajax-loading"></span>
			<em id="location_last_update"><?php esc_html_e( 'Last update', 'bulkmail' ); ?>: <?php printf( esc_html__( '%s ago', 'bulkmail' ), human_time_diff( filemtime( $geo_db_file_cities ) ) ); ?></em>
		</p>
	<?php elseif ( $geoip ) : ?>
		<div class="error inline"><p><?php esc_html_e( 'Looks like the location database hasn\'t been loaded yet!', 'bulkmail' ); ?></p></div>
		<p><button id="load_location_db" class="button-primary"><?php esc_html_e( 'Load Location Database manually', 'bulkmail' ); ?></button>&nbsp;<span class="loading geo-ajax-loading"></span>
			<em id="location_last_update"></em>
		</p>
	<?php endif; ?>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-save-subscribers-ip">
		<th scope="row"><?php esc_html_e( 'Save Subscriber IP', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[track_users]" value=""><input type="checkbox" name="bulkmail_options[track_users]" value="1" <?php checked( bulkmail_option( 'track_users' ) ); ?>> <?php esc_html_e( 'Save IP address and time of new subscribers', 'bulkmail' ); ?></label>
		<p class="description"><?php esc_html_e( 'In some countries it\'s required to save the IP address and the sign up time for legal reasons. Please add a note in your privacy policy if you save users data', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-do-not-track">
		<th scope="row">Do Not Track</th>
		<td><label><input type="hidden" name="bulkmail_options[do_not_track]" value=""><input type="checkbox" name="bulkmail_options[do_not_track]" value="1" <?php checked( bulkmail_option( 'do_not_track' ) ); ?>> <?php esc_html_e( 'Respect users "Do Not Track" option', 'bulkmail' ); ?></label>
		<p class="description"><?php printf( esc_html__( 'If enabled Bulkmail will respect users option for not getting tracked. Read more on the %s', 'bulkmail' ), '<a href="http://donottrack.us/" class="external">' . esc_html__( 'official website', 'bulkmail' ) . '</a>' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-custom-tags-in-web-version">
		<th scope="row"><?php esc_html_e( 'Custom Tags in web version', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[tags_webversion]" value=""><input type="checkbox" name="bulkmail_options[tags_webversion]" value="1" <?php checked( bulkmail_option( 'tags_webversion' ) ); ?>> <?php esc_html_e( 'Show subscribers tags in web version.', 'bulkmail' ); ?></label>
		<p class="description"><?php esc_html_e( 'Bulkmail can display custom tags from subscribers on the web version of your campaigns. They will only get displayed if they click a link in the newsletter.', 'bulkmail' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-gdpr-compliance-forms">
		<th scope="row"><?php esc_html_e( 'GDPR Compliance Forms', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[gdpr_forms]" value=""><input type="checkbox" name="bulkmail_options[gdpr_forms]" value="1" <?php checked( bulkmail_option( 'gdpr_forms' ) ); ?>> <?php esc_html_e( 'Add a checkbox on your forms for user consent.', 'bulkmail' ); ?></label>
		<p class="description"><?php esc_html_e( 'Users must check this checkbox to submit the form.', 'bulkmail' ); ?></p>
		<p class="description"><?php printf( esc_html__( 'You can define Texts on the %s settings tab.', 'bulkmail' ), '<strong>' . esc_html__( 'Text Strings', 'bulkmail' ) . '</strong>' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-link-to-privacy-page">
		<th scope="row"></th>
		<td>
		<p><?php esc_html_e( 'Link to your privacy policy page.', 'bulkmail' ); ?>
			<input type="text" name="bulkmail_options[gdpr_link]" value="<?php echo esc_attr( bulkmail_option( 'gdpr_link' ) ); ?>" class="large-text">
		</p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-maxmind-copy">
		<th scope="row"></th>
		<td><p class="description">* This product includes GeoLite data created by MaxMind, available from <a href="https://www.maxmind.com" class="external">maxmind.com</a></p>
		</td>
	</tr>
</table>

