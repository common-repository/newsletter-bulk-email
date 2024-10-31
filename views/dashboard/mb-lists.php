<?php

if ( $lists = bulkmail( 'lists' )->get() ) : ?>
<div class="bulkmail-mb-lists bulkmail-loading">
	<div class="bulkmail-mb-heading">
		<select class="bulkmail-mb-select">
		<?php foreach ( $lists as $list ) : ?>
			<option value="<?php echo (int) $list->ID; ?>"><?php echo esc_html( $list->name ); ?></option>
		<?php endforeach; ?>
		</select>
		<span class="bulkmail-mb-label"><?php esc_html_e( 'List', 'bulkmail' ); ?>:</span> <a class="bulkmail-mb-link" href="edit.php?post_type=newsletter&page=bulkmail_lists&ID=%d" title="<?php esc_attr_e( 'edit', 'bulkmail' ); ?>"><?php echo esc_html( $list->name ); ?></a>
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
	<h4><?php esc_html_e( 'Sorry, no Lists found!', 'bulkmail' ); ?></h4>
	<ul>
		<li><a href="edit.php?post_type=newsletter&page=bulkmail_lists&new"><?php esc_html_e( 'Create a new List', 'bulkmail' ); ?></a></li>
	</ul>
</div>
<?php endif; ?>

<p class="alignright">
<a class="button button-primary" href="edit.php?post_type=newsletter&page=bulkmail_lists&new"><?php esc_html_e( 'Create List', 'bulkmail' ); ?></a>
</p>
