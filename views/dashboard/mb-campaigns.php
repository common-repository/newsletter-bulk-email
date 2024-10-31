<?php

if ( $campaigns = bulkmail( 'campaigns' )->get_campaigns(
	array(
		'posts_per_page' => 25,
		'post_status'    => array(
			'finished',
			'active',
		),
	)
) ) : ?>
<div class="bulkmail-mb-campaigns bulkmail-loading">
	<div class="bulkmail-mb-heading">
		<select class="bulkmail-mb-select">
		<?php foreach ( $campaigns as $campaign ) : ?>
			<option value="<?php echo esc_attr( $campaign->ID ); ?>"><?php echo esc_html( $campaign->post_title ); ?></option>
		<?php endforeach; ?>
		</select>
		<span class="bulkmail-mb-label"><?php esc_html_e( 'Campaign', 'bulkmail' ); ?>:</span> <a class="bulkmail-mb-link" href="post.php?post=%d&action=edit" title="<?php esc_attr_e( 'edit', 'bulkmail' ); ?>"><?php echo esc_html( $campaign->post_title ); ?></a>
	</div>
	<div class="bulkmail-mb-stats">
		<ul class="campaign-charts">
			<li><div class="stats-total"></div></li>
			<li><div class="stats-open piechart" data-percent="0"><span>0</span>%</div></li>
			<li><div class="stats-clicks piechart" data-percent="0"><span>0</span>%</div></li>
			<li><div class="stats-unsubscribes piechart" data-percent="0"><span>0</span>%</div></li>
			<li><div class="stats-bounces piechart" data-percent="0"><span>0</span>%</div></li>
		</ul>
		<ul class="labels">
			<li><label><?php echo esc_html_x( 'total', 'in pie chart', 'bulkmail' ); ?></label></li>
			<li><label><?php echo esc_html_x( 'opens', 'in pie chart', 'bulkmail' ); ?></label></li>
			<li><label><?php echo esc_html_x( 'clicks', 'in pie chart', 'bulkmail' ); ?></label></li>
			<li><label><?php echo esc_html_x( 'unsubscribes', 'in pie chart', 'bulkmail' ); ?></label></li>
			<li><label><?php echo esc_html_x( 'bounces', 'in pie chart', 'bulkmail' ); ?></label></li>
		</ul>
	</div>
	<span class="loader"></span>
</div>
<?php else : ?>

<div class="bulkmail-welcome-panel">
	<h4><?php esc_html_e( 'Woha! Looks like you havn\'t sent any campaign yet!', 'bulkmail' ); ?></h4>
	<ul>
		<li><a href="post-new.php?post_type=newsletter"><?php esc_html_e( 'Create a new Campaign', 'bulkmail' ); ?></a></li>
		<li><a href="edit.php?post_type=newsletter"><?php esc_html_e( 'Check out existing Campaigns', 'bulkmail' ); ?></a></li>
	</ul>
</div>
<?php endif; ?>

<?php if ( ! $this->is_dashboard ) : ?>
<p class="alignright">
	<a class="button" href="post-new.php?post_type=newsletter&post_status=autoresponder"><?php esc_html_e( 'Create Autoresponder', 'bulkmail' ); ?></a>
	<a class="button button-primary" href="post-new.php?post_type=newsletter"><?php esc_html_e( 'Create Campaign', 'bulkmail' ); ?></a>
</p>
<?php endif; ?>
