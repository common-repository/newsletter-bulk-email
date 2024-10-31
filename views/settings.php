<?php

global $current_user, $wp_post_statuses, $wp_roles;

$customfields = bulkmail()->get_custom_fields();
$roles        = $wp_roles->get_names();

?>
<form id="bulkmail-settings-form" method="post" action="options.php" autocomplete="off" enctype="multipart/form-data">
<input style="display:none" autocomplete="off" readonly ><input type="password" style="display:none" autocomplete="off" readonly>
<div class="wrap">
	<p class="alignright">
		<input type="submit" class="submit-form button-primary" value="<?php esc_attr_e( 'Save Changes', 'bulkmail' ); ?>" disabled />
	</p>
<h1><?php esc_html_e( 'Newsletter Settings', 'bulkmail' ); ?></h1>
<?php

$timeformat = bulkmail( 'helper' )->timeformat();
$timeoffset = bulkmail( 'helper' )->gmt_offset( true );
if ( ! ( $test_email = get_user_meta( $current_user->ID, '_bulkmail_test_email', true ) ) ) {
	$test_email = $current_user->user_email;
}
$test_email = apply_filters( 'bulkmail_test_email', $test_email );


?>
<?php wp_nonce_field( 'bulkmail_nonce', 'bulkmail_nonce', false ); ?>
<?php settings_fields( 'bulkmail_settings' ); ?>
<?php settings_errors(); ?>
<?php do_settings_sections( 'bulkmail_settings' ); ?>

<?php
$sections = array(
	'general'         => esc_html__( 'General', 'bulkmail' ),
	'template'        => esc_html__( 'Template', 'bulkmail' ),
	'frontend'        => esc_html__( 'Front End', 'bulkmail' ),
	'privacy'         => esc_html__( 'Privacy', 'bulkmail' ),
	'subscribers'     => esc_html__( 'Subscribers', 'bulkmail' ),
	'wordpress-users' => esc_html__( 'WordPress Users', 'bulkmail' ),
	'texts'           => esc_html__( 'Text Strings', 'bulkmail' ),
	'tags'            => esc_html__( 'Tags', 'bulkmail' ),
	'delivery'        => esc_html__( 'Delivery', 'bulkmail' ),
	'cron'            => esc_html__( 'Cron', 'bulkmail' ),
	'capabilities'    => esc_html__( 'Capabilities', 'bulkmail' ),
	'bounce'          => esc_html__( 'Bouncing', 'bulkmail' ),
	'authentication'  => esc_html__( 'Authentication', 'bulkmail' ),
	'advanced'        => esc_html__( 'Advanced', 'bulkmail' ),
	'system_info'     => esc_html__( 'System Info', 'bulkmail' ),
	'manage-settings' => esc_html__( 'Manage Settings', 'bulkmail' ),
);
$sections = apply_filters( 'mymail_setting_sections', apply_filters( 'bulkmail_setting_sections', $sections ) );

if ( ! current_user_can( 'bulkmail_manage_capabilities' ) && ! current_user_can( 'manage_options' ) ) {
	unset( $sections['capabilities'] );
}

if ( ! current_user_can( 'manage_options' ) ) {
	unset( $sections['manage_settings'] );
}

?>

	<div class="settings-wrap">
		<div class="settings-nav">
			<div class="mainnav contextual-help-tabs hide-if-no-js">
			<ul>
			<?php foreach ( $sections as $id => $name ) { ?>
				<li><a href="#<?php echo $id; ?>" class="nav-<?php echo $id; ?>"><?php echo $name; ?></a></li>
			<?php } ?>
			<?php do_action( 'bulkmail_settings_tabs' ); ?>
			</ul>
			</div>
		</div>

		<div class="settings-tabs"> <div class="tab"><h3>&nbsp;</h3></div>

		<?php foreach ( $sections as $id => $name ) : ?>
			<div id="tab-<?php echo esc_attr( $id ); ?>" class="tab">
				<h3><?php echo esc_html( strip_tags( $name ) ); ?></h3>
				<?php do_action( 'bulkmail_section_tab', $id ); ?>
				<?php do_action( 'bulkmail_section_tab_' . $id ); ?>

				<?php
				if ( file_exists( BULKEMAIL_DIR . 'views/settings/' . $id . '.php' ) ) :
					include BULKEMAIL_DIR . 'views/settings/' . $id . '.php';
				endif;
				?>

			</div>
		<?php endforeach; ?>

	<?php $extra_sections = apply_filters( 'mymail_extra_setting_sections', apply_filters( 'bulkmail_extra_setting_sections', array() ) ); ?>

	<?php foreach ( $extra_sections as $id => $name ) : ?>
			<div id="tab-<?php echo esc_attr( $id ); ?>" class="tab">
				<h3><?php echo esc_html( strip_tags( $name ) ); ?></h3>
				<?php do_action( 'bulkmail_section_tab', $id ); ?>
				<?php do_action( 'bulkmail_section_tab_' . $id ); ?>
			</div>
	<?php endforeach; ?>
			<p class="submitbutton">
				<input type="submit" class="submit-form button-primary" value="<?php esc_attr_e( 'Save Changes', 'bulkmail' ); ?>" disabled />
			</p>
		</div>

	</div>

	<?php do_action( 'bulkmail_settings' ); ?>

	<input type="text" class="hidden" name="bulkmail_options[profile_form]" value="<?php echo esc_attr( bulkmail_option( 'profile_form', 1 ) ); ?>">
	<input type="text" class="hidden" name="bulkmail_options[ID]" value="<?php echo esc_attr( bulkmail_option( 'ID' ) ); ?>">

	<br class="clearfix">
<span id="settingsloaded"></span>
</div>
</form>
