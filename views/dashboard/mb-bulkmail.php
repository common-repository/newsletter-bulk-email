<?php
	$plugin_info = bulkmail()->plugin_info();
	$dateformat  = bulkmail( 'helper' )->dateformat();

	$license_email = '';
	$license_user  = '';

if ( bulkmail()->is_verified() ) {
	$license_user  = bulkmail()->username( '' );
	$license_email = bulkmail()->email( '' );
}

?>
<div class="locked">
	<h2><span class="not-valid"><?php esc_html_e( 'Please Validate', 'bulkmail' ); ?></span><span class="valid"><?php esc_html_e( 'Validated!', 'bulkmail' ); ?></span>
	</h2>
</div>
<dl class="bulkmail-icon bulkmail-icon-finished valid">
	<dt><?php esc_html_e( 'Verified License', 'bulkmail' ); ?></dt>
	<dd><?php printf( esc_html__( 'User: %1$s - %2$s', 'bulkmail' ), '<span class="bulkmail-username">' . esc_html( $license_user ) . '</span>', '<span class="bulkmail-email lighter">' . esc_html( $license_email ) . '</span>' ); ?></dd>
	<?php if ( ! bulkmail()->is_email_verified() ) : ?>
		<dd style="color:#D54E21"><?php esc_html_e( 'Please verify your Bulkmail account!', 'bulkmail' ); ?></dd>
	<?php endif; ?>
	<dd>
		<?php if ( current_user_can( 'bulkmail_manage_licenses' ) ) : ?>
<!--		<a href="https://bulkmail.co/manage-licenses/?utm_campaign=plugin&utm_medium=dashboard&utm_source=bulkmail_plugin" class="external">--><?php //esc_html_e( 'Manage Licenses', 'bulkmail' ); ?><!--</a> |-->
		<a href="<?php echo admin_url( 'admin.php?page=bulkmail_dashboard&reset_license=' . wp_create_nonce( 'bulkmail_reset_license' ) ); ?>" class="reset-license"><?php esc_html_e( 'Reset License', 'bulkmail' ); ?></a> |
		<?php endif; ?>
		<a href="https://emailmarketing.run/" class="external"><?php esc_html_e( 'Buy new License', 'bulkmail' ); ?></a>
	</dd>
</dl>
<dl class="bulkmail-icon bulkmail-icon-delete not-valid">
	<dt><?php esc_html_e( 'Not Verified', 'bulkmail' ); ?></dt>
	<dd><?php esc_html_e( 'Your license has not been verified', 'bulkmail' ); ?></dd>
	<dd>
		<?php if ( current_user_can( 'bulkmail_manage_licenses' ) ) : ?>
<!--		<a href="https://bulkmail.co/manage-licenses/" class="external">--><?php //esc_html_e( 'Manage Licenses', 'bulkmail' ); ?><!--</a> |-->
		<?php endif; ?>
		<a href="https://emailmarketing.run/" class="external"><?php esc_html_e( 'Buy new License', 'bulkmail' ); ?></a>
	</dd>
</dl>
<dl class="bulkmail-icon bulkmail-icon-reload update-not-available">
	<dt><?php printf( esc_html__( 'Installed Version %s', 'bulkmail' ), BULKEMAIL_VERSION ); ?></dt>
	<dd><?php esc_html_e( 'You have the latest version', 'bulkmail' ); ?></dd>
	<dd><span class="lighter"><?php echo isset( $plugin_info->last_update ) ? sprintf( esc_html__( 'checked %s ago', 'bulkmail' ), '<span class="update-last-check">' . human_time_diff( $plugin_info->last_update ) . '</span>' ) . ' &ndash; ' : ''; ?></span> <span class="lighter"><a href="" class="check-for-update"><?php esc_html_e( 'Check Again', 'bulkmail' ); ?></a></span>
	</dd>
</dl>
<dl class="bulkmail-icon bulkmail-icon-reload update-available">
	<dt><?php printf( esc_html__( 'Installed Version %s', 'bulkmail' ), BULKEMAIL_VERSION ); ?></dt>
	<dd><?php esc_html_e( 'A new Version is available', 'bulkmail' ); ?></dd>
	<dd><a class="thickbox" href="<?php echo network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=bulkmail&amp;section=changelog&amp;TB_iframe=true&amp;width=772&amp;height=745' ); ?>"><?php esc_html_e( 'view changelog', 'bulkmail' ); ?></a> <?php esc_html_e( 'or', 'bulkmail' ); ?> <a href="update.php?action=upgrade-plugin&plugin=<?php echo urlencode( BULKEMAIL_SLUG ); ?>&_wpnonce=<?php echo wp_create_nonce( 'upgrade-plugin_' . BULKEMAIL_SLUG ); ?>" class="update-button"><?php printf( esc_html__( 'update to %s now', 'bulkmail' ), '<span class="update-version">' . $plugin_info->new_version . '</span>' ); ?></a>
	</dd>
</dl>
<dl class="bulkmail-icon bulkmail-icon-support">
	<dt><?php esc_html_e( 'Support', 'bulkmail' ); ?></dt>
	<?php if ( bulkmail()->support() ) : ?>
		<?php if ( bulkmail()->has_support() ) : ?>
		<dd><span class="lighter"><?php printf( esc_html__( 'Your support expires on %s.', 'bulkmail' ), '<span class="">' . esc_html( date( $dateformat, bulkmail()->support() ) ) . '</span>' ); ?></span></dd>
		<?php else : ?>
		<dd><strong><?php printf( esc_html__( 'Your support expired %s ago!', 'bulkmail' ), '<span class="bulkmail-username">' . esc_html( human_time_diff( bulkmail()->support() ) ) . '</span>' ); ?></strong> &ndash; </dd>
		<?php endif; ?>
	<?php endif; ?>
	<dd>
		<a href="https://emailmarketing.run/" class="external"><?php esc_html_e( 'Documentation', 'bulkmail' ); ?></a> |
		<a href="https://emailmarketing.run/" class="external"><?php esc_html_e( 'Knowledge Base', 'bulkmail' ); ?></a> |
	<?php if ( bulkmail()->has_support() || ! bulkmail()->support() ) : ?>
		<a href="https://emailmarketing.run/contacts" class="external"><?php esc_html_e( 'Support', 'bulkmail' ); ?></a>
	<?php endif; ?>
		<a href="<?php echo admin_url( 'admin.php?page=bulkmail_tests' ); ?>"><?php esc_html_e( 'Self Test', 'bulkmail' ); ?></a>
	</dd>
</dl>
<?php if ( current_user_can( 'install_languages' ) && $set = bulkmail( 'translations' )->get_translation_set() ) : ?>
<dl class="bulkmail-icon bulkmail-dash bulkmail-icon-translate">
	<dt><?php esc_html_e( 'Translation', 'bulkmail' ); ?> </dt>
	<?php if ( bulkmail( 'translations' )->translation_installed() ) : ?>
		<?php $name = ( esc_html_x( 'Thanks for using Bulkmail in %s!', 'Your language', 'bulkmail' ) == 'Thanks for using Bulkmail in %s!' ) ? $set->name : $set->native_name; ?>
	<dd><?php printf( esc_html_x( 'Thanks for using Bulkmail in %s!', 'Your language', 'bulkmail' ), '<strong>' . esc_html( $name ) . '</strong>' ); ?></dd>
		<?php if ( bulkmail( 'translations' )->translation_available() ) : ?>
	<dd><a href="" class="load-language"><strong><?php esc_html_e( 'Update Translation', 'bulkmail' ); ?></strong></a></dd>
		<?php endif; ?>
	<?php elseif ( bulkmail( 'translations' )->translation_available() ) : ?>
	<dd><?php printf( esc_html__( 'Bulkmail is available in %s!', 'bulkmail' ), '<strong>' . esc_html( $set->name ) . '</strong>' ); ?></dd>
	<dd><a href="" class="load-language"><strong><?php esc_html_e( 'Download Translation', 'bulkmail' ); ?></strong></a></dd>
	<?php endif; ?>
	<dd><span class="lighter"><?php printf( esc_html__( 'Currently %s translated.', 'bulkmail' ), '<strong>' . $set->percent_translated . '%</strong>' ); ?></span></dd>
</dl>
<?php endif; ?>
