<div class="wrap" id="bulkmail-setup">

<?php wp_nonce_field( 'bulkmail_nonce', 'bulkmail_nonce', false ); ?>

<?php

$timeformat = bulkmail( 'helper' )->timeformat();
$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

$is_verified        = bulkmail()->is_verified();
$active_plugins     = get_option( 'active_plugins', array() );
$active_pluginslugs = preg_replace( '/^(.*)\/.*$/', '$1', $active_plugins );
$plugins            = array_keys( get_plugins() );
$pluginslugs        = preg_replace( '/^(.*)\/.*$/', '$1', $plugins );

$utm = array(
	'utm_campaign' => 'Bulkmail Setup',
	'utm_source'   => preg_replace( '/^https?:\/\//', '', get_bloginfo( 'url' ) ),
	'utm_medium'   => 'link',
);

?>
	<ol class="bulkmail-setup-steps-nav">
		<li><a href="#basics"><?php esc_html_e( 'Basics', 'bulkmail' ); ?></a></li>
		<li><a href="#homepage"><?php esc_html_e( 'Homepage', 'bulkmail' ); ?></a></li>
		<li><a href="#delivery"><?php esc_html_e( 'Delivery', 'bulkmail' ); ?></a></li>
		<li><a href="#privacy"><?php esc_html_e( 'Privacy', 'bulkmail' ); ?></a></li>
		<li><a href="#validation"><?php esc_html_e( 'Validation', 'bulkmail' ); ?></a></li>
		<li class="not-hidden"><a href="#finish"><?php esc_html_e( 'Ready!', 'bulkmail' ); ?></a></li>
	</ol>

	<input style="display:none"><input type="password" style="display:none">

	<div class="bulkmail-setup-steps">

		<div class="bulkmail-setup-step" id="step_start">

			<h2><?php esc_html_e( 'Welcome to Bulkmail', 'bulkmail' ); ?></h2>

			<div class="bulkmail-setup-step-body">

			<form class="bulkmail-setup-step-form">

			<p><?php esc_html_e( 'Before you can start sending your campaigns Bulkmail needs some info to get started.', 'bulkmail' ); ?></p>

			<p><?php esc_html_e( 'This wizard helps you to setup Bulkmail. All options available can be found later in the settings. You can always skip each step and adjust your settings later if you\'re not sure.', 'bulkmail' ); ?></p>

			<p><?php printf( esc_html__( 'The wizard is separated into %d different steps:', 'bulkmail' ), 5 ); ?></p>

			<dl>
				<dt><?php esc_html_e( 'Basic Information', 'bulkmail' ); ?></dt>
				<dd><?php esc_html_e( 'Bulkmail needs some essential informations like your personal information and also some legal stuff.', 'bulkmail' ); ?></dd>
			</dl>
			<dl>
				<dt><?php esc_html_e( 'Newsletter Homepage Setup', 'bulkmail' ); ?></dt>
				<dd><?php esc_html_e( 'This is where your subscribers signup, manage or cancel their subscriptions.', 'bulkmail' ); ?></dd>
			</dl>
			<dl>
				<dt><?php esc_html_e( 'Delivery Options', 'bulkmail' ); ?></dt>
				<dd><?php esc_html_e( 'How Bulkmail should delivery you campaigns.', 'bulkmail' ); ?></dd>
			</dl>
			<dl>
				<dt><?php esc_html_e( 'Privacy', 'bulkmail' ); ?></dt>
				<dd><?php esc_html_e( 'Bulkmail takes the privacy of your subscribers information seriously. Define which information Bulkmail should save.', 'bulkmail' ); ?></dd>
			</dl>
			<dl>
				<dt><?php esc_html_e( 'Validation', 'bulkmail' ); ?></dt>
				<dd><?php esc_html_e( 'Updates are important and if you have a valid license for Bulkmail you can automatically update directly from WordPress.', 'bulkmail' ); ?></dd>
			</dl>

			<p><a class="button button-hero button-primary next-step" href="#basics"><?php esc_html_e( 'Start Wizard', 'bulkmail' ); ?></a> <?php esc_html_e( 'or', 'bulkmail' ); ?> <a href="admin.php?page=bulkmail_dashboard&bulkmail_setup_complete=<?php echo wp_create_nonce( 'bulkmail_setup_complete' ); ?>"><?php esc_html_e( 'skip it', 'bulkmail' ); ?></a></p>

			</form>

			</div>

			<div class="bulkmail-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-primary next-step" href="#basics"><?php esc_html_e( 'Start Wizard', 'bulkmail' ); ?></a>

			</div>


		</div>

		<div class="bulkmail-setup-step" id="step_basics">

			<h2><?php esc_html_e( 'Basic Information', 'bulkmail' ); ?></h2>

			<div class="bulkmail-setup-step-body">

			<form class="bulkmail-setup-step-form">

			<p><?php esc_html_e( 'Please provide some basic information which is used for your newsletter campaigns. Bulkmail already pre-filled the fields with the default values but you should check them for correctness.', 'bulkmail' ); ?></p>
			<table class="form-table">

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'From Name', 'bulkmail' ); ?></th>
					<td><input type="text" name="bulkmail_options[from_name]" value="<?php echo esc_attr( bulkmail_option( 'from_name' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'The sender name which is displayed in the from field', 'bulkmail' ); ?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'From Address', 'bulkmail' ); ?></th>
					<td><input type="text" name="bulkmail_options[from]" value="<?php echo esc_attr( bulkmail_option( 'from' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'The sender email address. Force your receivers to whitelabel this email address.', 'bulkmail' ); ?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Reply To Address', 'bulkmail' ); ?></th>
					<td><input type="text" name="bulkmail_options[reply_to]" value="<?php echo esc_attr( bulkmail_option( 'reply_to' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'The address users can reply to', 'bulkmail' ); ?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Logo', 'bulkmail' ); ?>
					</th>
					<td>
					<?php bulkmail( 'helper' )->media_editor_link( bulkmail_option( 'logo', get_theme_mod( 'custom_logo' ) ), 'bulkmail_options[logo]', 'full' ); ?>
					<p class="description"><label><input type="hidden" name="bulkmail_options[logo_high_dpi]" value=""><input type="checkbox" name="bulkmail_options[logo_high_dpi]" value="1" <?php checked( bulkmail_option( 'logo_high_dpi' ) ); ?>> <?php esc_html_e( 'Use High DPI version if available.', 'bulkmail' ); ?></label></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Logo Link', 'bulkmail' ); ?></th>
					<td><input type="text" name="bulkmail_options[logo_link]" value="<?php echo esc_attr( bulkmail_option( 'logo_link' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'A link for your logo.', 'bulkmail' ); ?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Module Thumbnails', 'bulkmail' ); ?></th>
					<td><label><input type="hidden" name="bulkmail_options[module_thumbnails]" value=""><input type="checkbox" name="bulkmail_options[module_thumbnails]" value="1" <?php checked( bulkmail_option( 'module_thumbnails' ) ); ?>> <?php esc_html_e( 'Show thumbnails of modules in the editor if available', 'bulkmail' ); ?> *</label>
						<p class="description">* <?php esc_html_e( 'this option will send the HTML of your template files to our screen shot server which generates the thumbnails for you.', 'bulkmail' ); ?></p>
					</td>
				</tr>

			</table>
			<?php $tags = bulkmail_option( 'tags' ); ?>

			<p><?php esc_html_e( 'Some information is used in the footer of your campaign. Some information is required by law so please ask your lawyer about correct use.', 'bulkmail' ); ?></p>

			<table class="form-table">

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Company', 'bulkmail' ); ?></th>
					<td><input type="text" name="bulkmail_options[tags][company]" value="<?php echo esc_attr( $tags['company'] ); ?>" class="regular-text"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Copyright', 'bulkmail' ); ?></th>
					<td><input type="text" name="bulkmail_options[tags][copyright]" value="<?php echo esc_attr( $tags['copyright'] ); ?>" class="regular-text"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Homepage', 'bulkmail' ); ?></th>
					<td><input type="text" name="bulkmail_options[tags][homepage]" value="<?php echo esc_attr( $tags['homepage'] ); ?>" class="regular-text"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Address', 'bulkmail' ); ?></th>
					<td><textarea name="bulkmail_options[tags][address]" class="large-text" rows="5"><?php echo esc_attr( $tags['address'] ); ?></textarea></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'CAN-SPAM', 'bulkmail' ); ?></th>
					<td><input type="text" name="bulkmail_options[tags][can-spam]" value="<?php echo esc_attr( $tags['can-spam'] ); ?>" class="large-text"> <p class="description"><?php esc_html_e( 'This line is required in most countries. Your subscribers need to know why and where they have subscribed.', 'bulkmail' ); ?></p></td>
				</tr>

			</table>

			<p><?php printf( esc_html__( 'Wonder what these {placeholders} are for? Read more about tags %s.', 'bulkmail' ), '<a href="' . add_query_arg( $utm, 'https://emailmarketing.run/' ) . '" class="external">' . esc_html__( 'here', 'bulkmail' ) . '</a>' ); ?></p>

			</div>

			</form>

			<div class="bulkmail-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step" href="#homepage"><?php esc_html_e( 'Skip this Step', 'bulkmail' ); ?></a>
				<a class="button button-large button-primary next-step" href="#homepage"><?php esc_html_e( 'Next Step', 'bulkmail' ); ?></a>

			</div>

		</div>

		<div class="bulkmail-setup-step" id="step_homepage">

			<h2><?php esc_html_e( 'Newsletter Homepage', 'bulkmail' ); ?></h2>

			<div class="bulkmail-setup-step-body">

			<form class="bulkmail-setup-step-form">

			<p><?php esc_html_e( 'Bulkmail needs a Newsletter Homepage were users can subscribe, update and unsubscribe their subscription. It\'s a regular page with some required shortcodes.', 'bulkmail' ); ?></p>

			<?php

			$buttontext = esc_html__( 'Update Newsletter Homepage', 'bulkmail' );

			if ( ! ( $homepage = (array) get_post( bulkmail_option( 'homepage' ) ) ) ) {
				include BULKEMAIL_DIR . 'includes/static.php';

				$buttontext = esc_html__( 'Create Newsletter Homepage', 'bulkmail' );
				$homepage   = $bulkmail_homepage;

			}
			?>
			<p>
			<label><strong><?php esc_html_e( 'Page Title', 'bulkmail' ); ?>:</strong>
			<input id="homepage_title" type="text" name="post_title" size="30" value="<?php echo esc_attr( $homepage['post_title'] ); ?>" id="title" spellcheck="true" autocomplete="off"></label>

			<?php if ( bulkmail( 'helper' )->using_permalinks() ) : ?>

				<?php $url = trailingslashit( get_bloginfo( 'url' ) ); ?>
				<label><?php echo esc_html_x( 'Location', 'the URL not the place', 'bulkmail' ); ?>:</label>
				<span>
					<a href="<?php echo $url . sanitize_title( $homepage['post_name'] ); ?>" class="external"><?php echo $url; ?><strong><?php echo sanitize_title( $homepage['post_name'] ); ?></strong>/</a>
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'bulkmail' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $url; ?><input type="text" name="post_name" value="<?php echo sanitize_title( $homepage['post_name'] ); ?>" class="regular-text">/
				</span>

			<?php endif; ?>

			</p>

			<p><?php echo wp_editor( $homepage['post_content'], 'post_content' ); ?></p>

			</form>

			</div>

			<div class="bulkmail-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step" href="#delivery"><?php esc_html_e( 'Skip this Step', 'bulkmail' ); ?></a>
				<a class="button button-large button-primary next-step" href="#delivery"><?php echo esc_html( $buttontext ); ?></a>

			</div>

		</div>

		<div class="bulkmail-setup-step" id="step_delivery">

			<h2><?php esc_html_e( 'Delivery', 'bulkmail' ); ?></h2>

			<div class="bulkmail-setup-step-body">

			<form class="bulkmail-setup-step-form">

			<p><?php esc_html_e( 'Choose how Bulkmail should send your campaigns. It\'s recommend to go with a dedicate ESP to prevent rejections and server blocking.', 'bulkmail' ); ?></p>

			<?php $method = bulkmail_option( 'deliverymethod', 'simple' ); ?>

			<div id="deliverynav" class="nav-tab-wrapper hide-if-no-js">
				<a class="nav-tab<?php echo 'simple' == $method ? ' nav-tab-active' : ''; ?>" href="#simple"><?php esc_html_e( 'Simple', 'bulkmail' ); ?></a>
				<a class="nav-tab<?php echo 'smtp' == $method ? ' nav-tab-active' : ''; ?>" href="#smtp">SMTP</a>
<!--				<a class="nav-tab--><?php //echo 'gmail' == $method ? ' nav-tab-active' : ''; ?><!--" href="#gmail">Gmail</a>-->
<!--				<a class="nav-tab--><?php //echo 'amazonses' == $method ? ' nav-tab-active' : ''; ?><!--" href="#amazonses">AmazonSES</a>-->
<!--				<a class="nav-tab--><?php //echo 'sparkpost' == $method ? ' nav-tab-active' : ''; ?><!--" href="#sparkpost">SparkPost</a>-->
<!--				<a class="nav-tab--><?php //echo 'mailgun' == $method ? ' nav-tab-active' : ''; ?><!--" href="#mailgun">Mailgun</a>-->
<!--				<a class="nav-tab--><?php //echo 'sendgrid' == $method ? ' nav-tab-active' : ''; ?><!--" href="#sendgrid">SendGrid</a>-->
<!--				<a class="nav-tab--><?php //echo 'mandrill' == $method ? ' nav-tab-active' : ''; ?><!--" href="#mandrill">Mandrill</a>-->
<!--				<a class="nav-tab--><?php //echo 'dummymailer' == $method ? ' nav-tab-active' : ''; ?><!--" href="#dummymailer">DummyMailer</a>-->
			</div>

			<input type="hidden" name="bulkmail_options[deliverymethod]" id="deliverymethod" value="<?php echo esc_attr( $method ); ?>" class="regular-text">

			<div class="deliverytab" id="deliverytab-simple"<?php echo 'simple' == $method ? ' style="display:block"' : ''; ?>>
				<?php do_action( 'bulkmail_deliverymethod_tab_simple' ); ?>
			</div>
			<div class="deliverytab" id="deliverytab-smtp"<?php echo 'smtp' == $method ? ' style="display:block"' : ''; ?>>
				<?php do_action( 'bulkmail_deliverymethod_tab_smtp' ); ?>
			</div>
			<div class="deliverytab" id="deliverytab-gmail"<?php echo 'gmail' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'bulkmail-gmail', $active_pluginslugs ) ) :
					do_action( 'bulkmail_deliverymethod_tab_gmail' );
				else :
					?>
<!--				<div class="wp-plugin">-->
<!--				<a href="https://wordpress.org/plugins/bulkmail-gmail/" class="external">-->
<!--					<img src="//ps.w.org/bulkmail-gmail/assets/banner-772x250.png?v=--><?php //echo BULKEMAIL_VERSION; ?><!--" width="772" height="250">-->
<!--					<span>Bulkmail Gmail Integration</span>-->
<!--				</a>-->
<!--				</div>-->
				<a class="button button-primary quick-install" data-plugin="bulkmail-gmail" data-method="gmail">
					<?php echo in_array( 'bulkmail-gmail', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'bulkmail' ) : sprintf( esc_html__( 'Install %s Extension', 'bulkmail' ), 'Gmail' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-amazonses"<?php echo 'amazonses' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'bulkmail-amazonses', $active_pluginslugs ) ) :
					do_action( 'bulkmail_deliverymethod_tab_amazonses' );
				else :
					?>
<!--				<div class="wp-plugin">-->
<!--				<a href="https://wordpress.org/plugins/bulkmail-amazonses/" class="external">-->
<!--					<img src="//ps.w.org/bulkmail-amazonses/assets/banner-772x250.png?v=--><?php //echo BULKEMAIL_VERSION; ?><!--" width="772" height="250">-->
<!--					<span>Bulkmail Amazon SES Integration</span>-->
<!--				</a>-->
<!--				</div>-->
				<a class="button button-primary quick-install" data-plugin="bulkmail-amazonses" data-method="amazonses">
					<?php echo in_array( 'bulkmail-amazonses', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'bulkmail' ) : sprintf( esc_html__( 'Install %s Extension', 'bulkmail' ), 'Amazon SES' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-sparkpost"<?php echo 'sparkpost' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'bulkmail-sparkpost', $active_pluginslugs ) ) :
					do_action( 'bulkmail_deliverymethod_tab_sparkpost' );
				else :
					?>
<!--				<div class="wp-plugin">-->
<!--				<a href="https://wordpress.org/plugins/bulkmail-sparkpost/" class="external">-->
<!--					<img src="//ps.w.org/bulkmail-sparkpost/assets/banner-772x250.png?v=--><?php //echo BULKEMAIL_VERSION; ?><!--" width="772" height="250">-->
<!--					<span>Bulkmail SparkPost Integration</span>-->
<!--				</a>-->
<!--				</div>-->
				<a class="button button-primary quick-install" data-plugin="bulkmail-sparkpost" data-method="sparkpost">
					<?php echo in_array( 'bulkmail-sparkpost', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'bulkmail' ) : sprintf( esc_html__( 'Install %s Extension', 'bulkmail' ), 'SparkPost' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-mailgun"<?php echo 'mailgun' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'bulkmail-mailgun', $active_pluginslugs ) ) :
					do_action( 'bulkmail_deliverymethod_tab_mailgun' );
				else :
					?>
<!--				<div class="wp-plugin">-->
<!--				<a href="https://wordpress.org/plugins/bulkmail-mailgun/" class="external">-->
<!--					<img src="//ps.w.org/bulkmail-mailgun/assets/banner-772x250.png?v=--><?php //echo BULKEMAIL_VERSION; ?><!--" width="772" height="250">-->
<!--					<span>Bulkmail Mailgun Integration</span>-->
<!--				</a>-->
<!--				</div>-->
				<a class="button button-primary quick-install" data-plugin="bulkmail-mailgun" data-method="mailgun">
					<?php echo in_array( 'bulkmail-mailgun', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'bulkmail' ) : sprintf( esc_html__( 'Install %s Extension', 'bulkmail' ), 'Mailgun' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-sendgrid"<?php echo 'sendgrid' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'bulkmail-sendgrid', $active_pluginslugs ) ) :
					do_action( 'bulkmail_deliverymethod_tab_sendgrid' );
				else :
					?>
<!--				<div class="wp-plugin">-->
<!--				<a href="https://wordpress.org/plugins/bulkmail-sendgrid/" class="external">-->
<!--					<img src="//ps.w.org/bulkmail-sendgrid/assets/banner-772x250.png?v=--><?php //echo BULKEMAIL_VERSION; ?><!--" width="772" height="250">-->
<!--					<span>Bulkmail SendGrid Integration</span>-->
<!--				</a>-->
<!--				</div>-->
				<a class="button button-primary quick-install" data-plugin="bulkmail-sendgrid" data-method="sendgrid">
					<?php echo in_array( 'bulkmail-sendgrid', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'bulkmail' ) : sprintf( esc_html__( 'Install %s Extension', 'bulkmail' ), 'SendGrid' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-mandrill"<?php echo 'mandrill' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'bulkmail-mandrill', $active_pluginslugs ) ) :
					do_action( 'bulkmail_deliverymethod_tab_mandrill' );
				else :
					?>
<!--				<div class="wp-plugin">-->
<!--				<a href="https://wordpress.org/plugins/bulkmail-mandrill/" class="external">-->
<!--					<img src="//ps.w.org/bulkmail-mandrill/assets/banner-772x250.png?v=--><?php //echo BULKEMAIL_VERSION; ?><!--" width="772" height="250">-->
<!--					<span>Bulkmail Mandrill Integration</span>-->
<!--				</a>-->
<!--				</div>-->
				<a class="button button-primary quick-install" data-plugin="bulkmail-mandrill" data-method="mandrill">
					<?php echo in_array( 'bulkmail-mandrill', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'bulkmail' ) : sprintf( esc_html__( 'Install %s Extension', 'bulkmail' ), 'Mandrill' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-dummymailer"<?php echo 'dummymailer' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'bulkmail-dummy-mailer', $active_pluginslugs ) ) :
					do_action( 'bulkmail_deliverymethod_tab_dummymailer' );
				else :
					?>
<!--				<div class="wp-plugin">-->
<!--				<a href="https://wordpress.org/plugins/bulkmail-dummy-mailer/" class="external">-->
<!--					<img src="//ps.w.org/bulkmail-dummy-mailer/assets/banner-772x250.png?v=--><?php //echo BULKEMAIL_VERSION; ?><!--" width="772" height="250">-->
<!--					<span>Bulkmail Dummy Mailer</span>-->
<!--				</a>-->
<!--				</div>-->
				<a class="button button-primary quick-install" data-plugin="bulkmail-dummy-mailer" data-method="dummymailer">
					<?php echo in_array( 'bulkmail-dummy-mailer', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'bulkmail' ) : sprintf( esc_html__( 'Install %s Extension', 'bulkmail' ), 'Dummy Mailer' ); ?>
				</a>
				<?php endif; ?>
			</div>

			</form>

			</div>

			<div class="bulkmail-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step" href="#privacy"><?php esc_html_e( 'Skip this Step', 'bulkmail' ); ?></a>
				<a class="button button-large button-primary next-step delivery-next-step" href="#privacy"><?php esc_html_e( 'Next Step', 'bulkmail' ); ?></a>

			</div>

		</div>

		<div class="bulkmail-setup-step" id="step_privacy">

			<h2><?php esc_html_e( 'Privacy', 'bulkmail' ); ?></h2>

			<div class="bulkmail-setup-step-body">

			<form class="bulkmail-setup-step-form">

			<p><?php esc_html_e( 'Bulkmail can track specific behaviors and the location of your subscribers to target your audience better. In most countries you must get the consent of the subscriber if you sent them marketing emails. Please get in touch with your lawyer for legal advice in your country.', 'bulkmail' ); ?></p>
			<p><?php esc_html_e( 'If you have users in the European Union you have to comply with the General Data Protection Regulation (GDPR). Please check our knowledge base on how Bulkmail can help you.', 'bulkmail' ); ?></p>
			<p><a href="https://emailmarketing.run/" class="external button button-primary"><?php esc_html_e( 'Knowledge Base', 'bulkmail' ); ?></a></p>

			<?php require BULKEMAIL_DIR . '/views/settings/privacy.php'; ?>

			</div>

			</form>

			<div class="bulkmail-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step" href="#validation"><?php esc_html_e( 'Skip this Step', 'bulkmail' ); ?></a>
				<a class="button button-large button-primary next-step" href="#validation"><?php esc_html_e( 'Next Step', 'bulkmail' ); ?></a>

			</div>

		</div>

		<div class="bulkmail-setup-step" id="step_validation">

			<h2><?php esc_html_e( 'Validation', 'bulkmail' ); ?></h2>

			<div class="bulkmail-setup-step-body">

			<p><?php esc_html_e( 'Updates are important to get new features and security fixes. An outdated version of your plugins can always bring the risk of getting compromised.', 'bulkmail' ); ?></p>

			<?php bulkmail( 'register' )->form(); ?>

			</div>

			<div class="bulkmail-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step validation-skip-step<?php echo $is_verified ? ' disabled' : ''; ?>" href="#finish"><?php esc_html_e( 'Remind me later', 'bulkmail' ); ?></a>
				<a class="button button-large button-primary next-step validation-next-step<?php echo ! $is_verified ? ' disabled' : ''; ?>" href="#finish"><?php esc_html_e( 'Next Step', 'bulkmail' ); ?></a>

			</div>

		</div>

		<div class="bulkmail-setup-step" id="step_finish">

			<form class="bulkmail-setup-step-form">

			<h2><?php esc_html_e( 'Great, you\'re done!', 'bulkmail' ); ?></h2>

			<div class="bulkmail-setup-step-body">

			<p><?php esc_html_e( 'Now you can continue to customize Bulkmail to your needs.', 'bulkmail' ); ?></p>

			<div class="feature-section two-col">
				<div class="col">
				<ol>
					<li><a href="edit.php?post_type=newsletter&page=bulkmail_settings"><?php esc_html_e( 'Complete your settings', 'bulkmail' ); ?></a></li>
					<li><a href="post-new.php?post_type=newsletter"><?php esc_html_e( 'Create your first campaign', 'bulkmail' ); ?></a></li>
					<li><a href="edit.php?post_type=newsletter&page=bulkmail_forms"><?php esc_html_e( 'Update your forms', 'bulkmail' ); ?></a></li>
					<li><a href="edit.php?post_type=newsletter&page=bulkmail_manage_subscribers"><?php esc_html_e( 'Import your existing subscribers', 'bulkmail' ); ?></a></li>
					<li><a href="edit.php?post_type=newsletter&page=bulkmail_templates"><?php esc_html_e( 'Check out the templates', 'bulkmail' ); ?></a></li>
					<li><a href="edit.php?post_type=newsletter&page=bulkmail_addons"><?php esc_html_e( 'Extend Bulkmail', 'bulkmail' ); ?></a></li>
				</ol>
				</div>
				<div class="col">
				<h3><?php esc_html_e( 'External Resources', 'bulkmail' ); ?></h3>
				<ol>
					<li><a href="<?php echo add_query_arg( $utm, 'https://emailmarketing.run/?id=1' ); ?>" class="external"><?php esc_html_e( 'Create a welcome message for new subscribers', 'bulkmail' ); ?></a></li>
					<li><a href="<?php echo add_query_arg( $utm, 'https://emailmarketing.run/?id=2' ); ?>" class="external"><?php esc_html_e( 'Customize the notification template', 'bulkmail' ); ?></a></li>
					<li><a href="<?php echo add_query_arg( $utm, 'https://emailmarketing.run/?id=3' ); ?>" class="external"><?php esc_html_e( 'Send your latest posts automatically', 'bulkmail' ); ?></a></li>
					<li><a href="<?php echo add_query_arg( $utm, 'https://emailmarketing.run/?id=4' ); ?>" class="external"><?php esc_html_e( 'Creating a series or drip campaign', 'bulkmail' ); ?></a></li>
					<li><a href="<?php echo add_query_arg( $utm, 'https://emailmarketing.run/?id=5' ); ?>" class="external"><?php esc_html_e( 'Learn more about segmentation', 'bulkmail' ); ?></a></li>
				</ol>
				</div>
			</div>
			<p><?php printf( esc_html__( 'Still need help? Go ask on the %s further questions.', 'bulkmail' ), '<a href="' . add_query_arg( $utm, 'https://emailmarketing.run/' ) . '" class="external">' . esc_html__( 'knowledge base', 'bulkmail' ) . '</a>' ); ?></p>

			<div class="social-media-buttons">
				<div id="fb-root"></div>
					<a href="https://twitter.com/bulkmail?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-size="large" data-show-count="false">Follow @bulkmail</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0&appId=1656804244418051&autoLogAppEvents=1';
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));</script>
				<div class="fb-like" data-href="https://www.facebook.com/bulkmail/" data-layout="button" data-action="like" data-size="large" data-show-faces="true" data-share="true"></div>
				</div>
			</div>

			<div class="bulkmail-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large button-primary" href="admin.php?page=bulkmail_dashboard&bulkmail_setup_complete=<?php echo wp_create_nonce( 'bulkmail_setup_complete' ); ?>"><?php esc_html_e( 'Ok, got it!', 'bulkmail' ); ?></a>

			</div>

		</div>

	</div>

<div id="ajax-response"></div>
<br class="clear">
</div>
