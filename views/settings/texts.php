<table class="form-table">
	<tr valign="top" class="settings-row settings-row-subscription-form">
		<th scope="row"><?php esc_html_e( 'Subscription Form', 'bulkmail' ); ?>
		<p class="description"><?php esc_html_e( 'Define messages for the subscription form', 'bulkmail' ); ?>.<br>
		<?php if ( bulkmail_option( 'homepage' ) ) : ?>
			<?php printf( esc_html__( 'Some text can get defined on the %s as well', 'bulkmail' ), '<a href="post.php?post=' . bulkmail_option( 'homepage' ) . '&action=edit">Newsletter Homepage</a>' ); ?>
		<?php endif; ?>
		</p>
		</th>
		<td>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Confirmation', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[confirmation]" value="<?php echo esc_attr( bulkmail_text( 'confirmation' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Successful', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[success]" value="<?php echo esc_attr( bulkmail_text( 'success' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Error Message', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[error]" value="<?php echo esc_attr( bulkmail_text( 'error' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Unsubscribe', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[unsubscribe]" value="<?php echo esc_attr( bulkmail_text( 'unsubscribe' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Unsubscribe Error', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[unsubscribeerror]" value="<?php echo esc_attr( bulkmail_text( 'unsubscribeerror' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Profile Update', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[profile_update]" value="<?php echo esc_attr( bulkmail_text( 'profile_update' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Newsletter Sign up', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[newsletter_signup]" value="<?php echo esc_attr( bulkmail_text( 'newsletter_signup' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-field-labels">
		<th scope="row"><?php esc_html_e( 'Field Labels', 'bulkmail' ); ?><p class="description"><?php esc_html_e( 'Define texts for the labels of forms. Custom field labels can be defined on the Subscribers tab', 'bulkmail' ); ?></p></th>
		<td>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Email', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[email]" value="<?php echo esc_attr( bulkmail_text( 'email' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'First Name', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[firstname]" value="<?php echo esc_attr( bulkmail_text( 'firstname' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Last Name', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[lastname]" value="<?php echo esc_attr( bulkmail_text( 'lastname' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Lists', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[lists]" value="<?php echo esc_attr( bulkmail_text( 'lists' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Submit Button', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[submitbutton]" value="<?php echo esc_attr( bulkmail_text( 'submitbutton' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Profile Button', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[profilebutton]" value="<?php echo esc_attr( bulkmail_text( 'profilebutton' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Unsubscribe Button', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[unsubscribebutton]" value="<?php echo esc_attr( bulkmail_text( 'unsubscribebutton' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-mail">
		<th scope="row"><?php esc_html_e( 'Mail', 'bulkmail' ); ?><p class="description"><?php esc_html_e( 'Define texts for the mails', 'bulkmail' ); ?></p></th>
		<td>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Unsubscribe Link', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[unsubscribelink]" value="<?php echo esc_attr( bulkmail_text( 'unsubscribelink' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Webversion Link', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[webversion]" value="<?php echo esc_attr( bulkmail_text( 'webversion' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Forward Link', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[forward]" value="<?php echo esc_attr( bulkmail_text( 'forward' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Profile Link', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[profile]" value="<?php echo esc_attr( bulkmail_text( 'profile' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-order">
		<th scope="row"><?php esc_html_e( 'Other', 'bulkmail' ); ?></th>
		<td>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Already registered', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[already_registered]" value="<?php echo esc_attr( bulkmail_text( 'already_registered' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'New confirmation message', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[new_confirmation_sent]" value="<?php echo esc_attr( bulkmail_text( 'new_confirmation_sent' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Enter your email', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[enter_email]" value="<?php echo esc_attr( bulkmail_text( 'enter_email' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-gdpr">
		<th scope="row"><?php esc_html_e( 'GDPR', 'bulkmail' ); ?></th>
		<td>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Terms confirmation text', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[gdpr_text]" value="<?php echo esc_attr( bulkmail_text( 'gdpr_text' ) ); ?>" class="regular-text"></div>
		<div class="bulkmail_text"><label><?php esc_html_e( 'Error text', 'bulkmail' ); ?>:</label> <input type="text" name="bulkmail_texts[gdpr_error]" value="<?php echo esc_attr( bulkmail_text( 'gdpr_error' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<?php

$dir    = defined( 'WP_LANG_DIR' ) ? WP_LANG_DIR : BULKEMAIL_DIR . '/languages/';
$files  = array();
$locale = get_locale();

if ( is_dir( $dir ) ) {
	$files = list_files( $dir );
	$files = preg_grep( '/bulkmail-(.*)\.po$/', $files );
}
?>
<?php if ( ! empty( $files ) ) : ?>
<table class="form-table language-switcher-field">
	<tr valign="top" class="settings-row settings-row-change-language">
		<th scope="row"><?php esc_html_e( 'Change Language', 'bulkmail' ); ?></th>
		<td>
			<p class="description">
			<?php esc_html_e( 'change language of texts if available to', 'bulkmail' ); ?>
			<select name="language-file">
				<option<?php selected( preg_match( '#^en_#', $locale ) ); ?> value="en_US"><?php esc_html_e( 'English', 'bulkmail' ); ?> (en_US)</option>
				<?php
				foreach ( $files as $file ) {
					$lang = str_replace( array( '.po', 'bulkmail-' ), '', basename( $file ) );
					?>
				<option<?php selected( $lang == $locale ); ?> value="<?php echo $lang; ?>"><?php echo $lang; ?></option>
				<?php } ?>
			</select>
			<button name="change-language" class="button"><?php esc_html_e( 'change language', 'bulkmail' ); ?></button>
			<br class="clearfix">
			</p>
		</td>
	</tr>
</table>
<?php endif; ?>
