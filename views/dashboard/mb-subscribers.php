<?php

if ( $subscribers = bulkmail( 'subscribers' )->get_totals( 1 ) ) : ?>
<div class="bulkmail-mb-subscribers bulkmail-loading">
	<div class="bulkmail-mb-heading">
		<select class="bulkmail-mb-select" id="bulkmail-subscriber-range">
			<option value="7 days"><?php esc_html_e( '7 days', 'bulkmail' ); ?></option>
			<option value="30 days"><?php esc_html_e( '30 days', 'bulkmail' ); ?></option>
			<option value="3 month"><?php esc_html_e( '3 month', 'bulkmail' ); ?></option>
			<option value="1 year"><?php esc_html_e( '1 year', 'bulkmail' ); ?></option>
		</select>
		<span class="alignright"><?php esc_html_e( 'Subscriber Grows', 'bulkmail' ); ?>:</span>
	<?php if ( ! $this->is_dashboard ) : ?>
		<?php printf( esc_html__( 'You have %s', 'bulkmail' ), '<a class="bulkmail-subscribers" href="edit.php?post_type=newsletter&page=bulkmail_subscribers&status=1">' . number_format_i18n( $subscribers ) . ' ' . esc_html__( _nx( 'Subscriber', 'Subscribers', $subscribers, 'number of', 'bulkmail' ) ) . '</a>' ); ?>
	<?php endif; ?>
	</div>
	<div class="bulkmail-db-wrap">
		<div id="subscriber-chart-wrap">
			<canvas class="subscriber-charts" id="subscriber-chart"></canvas>
		</div>
	</div>
</div>

	<?php if ( ! $this->is_dashboard ) : ?>
<p class="alignright">
	<a href="edit.php?post_type=newsletter&page=bulkmail_manage_subscribers&tab=import"><?php esc_html_e( 'Import', 'bulkmail' ); ?></a>,
	<a href="edit.php?post_type=newsletter&page=bulkmail_manage_subscribers&tab=export"><?php esc_html_e( 'Export', 'bulkmail' ); ?></a>
		<?php esc_html_e( 'or', 'bulkmail' ); ?>
	<a class="button button-primary" href="edit.php?post_type=newsletter&page=bulkmail_subscribers&new"><?php esc_html_e( 'Add Subscriber', 'bulkmail' ); ?></a>
</p>
	<?php endif; ?>

<?php else : ?>
<div class="bulkmail-welcome-panel">
		<h4><?php esc_html_e( 'You have no subscribers yet!', 'bulkmail' ); ?></h4>
		<ul>
			<li><a href="edit.php?post_type=newsletter&page=bulkmail_subscribers&new"><?php esc_html_e( 'Add a single Subscriber', 'bulkmail' ); ?></a></li>
			<li><a href="edit.php?post_type=newsletter&page=bulkmail_manage_subscribers"><?php esc_html_e( 'Import your existing Subscribers', 'bulkmail' ); ?></a></li>
			<li><a href="edit.php?post_type=newsletter&page=bulkmail_forms&new"><?php esc_html_e( 'Create a Form to engage new Subscribers', 'bulkmail' ); ?></a></li>
		</ul>
	</div>
<?php endif; ?>
