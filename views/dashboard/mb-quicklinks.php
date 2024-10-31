<?php

$autoresponder = count( bulkmail_get_autoresponder_campaigns() );

$campaigns = count( bulkmail_get_campaigns() ) - $autoresponder;

$subscribers = bulkmail( 'subscribers' )->get_totals( 1 );

$lists = count( bulkmail( 'lists' )->get() );

$forms = count( bulkmail( 'forms' )->get_all() );

?>
<dl class="bulkmail-icon bulkmail-icon-translate">
    <dt><a style="color: #2440b3;text-decoration:underline;" href="https://emailmarketing.run" target="_blank"><?php esc_html_e( 'Homepage', 'bulkmail' ); ?></a></dt>
</dl>
<dl class="bulkmail-icon bulkmail-icon-star-empty">
	<dt><a href="edit.php?post_type=newsletter"><?php esc_html_e( 'Campaigns', 'bulkmail' ); ?></a></dt>
	<dd><span class="version">
		<?php echo number_format_i18n( $campaigns ) . ' ' . esc_html__( _nx( 'Campaign', 'Campaigns', $campaigns, 'number of', 'bulkmail' ) ); ?>
		<?php echo $autoresponder ? ', ' . number_format_i18n( $autoresponder ) . ' ' . esc_html__( _nx( 'Autoresponder', 'Autoresponders', $autoresponder, 'number of', 'bulkmail' ) ) : ''; ?></span>
	</dd>
	<dd>
		<a href="edit.php?post_type=newsletter"><?php esc_html_e( 'View', 'bulkmail' ); ?></a> |
		<a href="post-new.php?post_type=newsletter"><?php esc_html_e( 'Create Campaign', 'bulkmail' ); ?></a> |
		<a href="post-new.php?post_type=newsletter&post_status=autoresponder"><?php esc_html_e( 'Create Autoresponder', 'bulkmail' ); ?></a>
	</dd>
</dl>
<dl class="bulkmail-icon bulkmail-icon-users">
	<dt><a href="edit.php?post_type=newsletter&page=bulkmail_subscribers"><?php esc_html_e( 'Subscribers', 'bulkmail' ); ?></a></dt>
	<dd><span class="version"><?php echo number_format_i18n( $subscribers ) . ' ' . esc_html__( _nx( 'Subscriber', 'Subscribers', $subscribers, 'number of', 'bulkmail' ) ); ?></span></dd>
	<dd>
		<a href="edit.php?post_type=newsletter&page=bulkmail_subscribers"><?php esc_html_e( 'View', 'bulkmail' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=bulkmail_manage_subscribers&tab=import"><?php esc_html_e( 'Import', 'bulkmail' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=bulkmail_manage_subscribers&tab=export"><?php esc_html_e( 'Export', 'bulkmail' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=bulkmail_subscribers&new"><?php esc_html_e( 'Add Subscriber', 'bulkmail' ); ?></a>
	</dd>
</dl>
<dl class="bulkmail-icon bulkmail-icon-list">
	<dt><a href="edit.php?post_type=newsletter&page=bulkmail_lists"><?php esc_html_e( 'Lists', 'bulkmail' ); ?></a></dt>
	<dd><span class="version"><?php echo number_format_i18n( $lists ) . ' ' . esc_html__( _nx( 'List', 'Lists', $lists, 'number of', 'bulkmail' ) ); ?></span></dd>
	<dd>
		<a href="edit.php?post_type=newsletter&page=bulkmail_lists"><?php esc_html_e( 'View', 'bulkmail' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=bulkmail_lists&new"><?php esc_html_e( 'Add List', 'bulkmail' ); ?></a>
	</dd>
</dl>
<dl class="bulkmail-icon bulkmail-icon-forms">
	<dt><a href="edit.php?post_type=newsletter&page=bulkmail_forms"><?php esc_html_e( 'Forms', 'bulkmail' ); ?></a></dt>
	<dd><span class="version"><?php echo number_format_i18n( $forms ) . ' ' . esc_html__( _nx( 'Form', 'Forms', $forms, 'number of', 'bulkmail' ) ); ?></span></dd>
	<dd>
		<a href="edit.php?post_type=newsletter&page=bulkmail_forms"><?php esc_html_e( 'View', 'bulkmail' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=bulkmail_forms&new"><?php esc_html_e( 'Add Form', 'bulkmail' ); ?></a>
	</dd>
</dl>
