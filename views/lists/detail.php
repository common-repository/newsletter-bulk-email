<?php

$id = isset( $_GET['ID'] ) ? (int) $_GET['ID'] : null;

$is_new = isset( $_GET['new'] );

if ( ! $is_new ) {
	if ( ! ( $list = $this->get( $id, null, true ) ) ) {
		echo '<h2>' . esc_html__( 'This list does not exist or has been deleted!', 'bulkmail' ) . '</h2>';
		return;
	}
} else {

	if ( ! current_user_can( 'bulkmail_add_subscribers' ) ) {
		echo '<h2>' . esc_html__( 'You don\'t have the right permission to add new lists', 'bulkmail' ) . '</h2>';
		return;
	}

	$list = $this->get_empty();
	if ( isset( $_POST['bulkmail_data'] ) ) {

		$list = (object) wp_parse_args( $_POST['bulkmail_data'], (array) $list );

	}
}

$timeformat = bulkmail( 'helper' )->timeformat();
$timeoffset = bulkmail( 'helper' )->gmt_offset( true );

$now = time();

$tabindex = 1;

?>
<div class="wrap<?php echo( $is_new ) ? ' new' : ''; ?>">
<form id="subscriber_form" action="edit.php?post_type=newsletter&page=bulkmail_lists<?php echo ( $is_new ) ? '&new' : '&ID=' . $id; ?>" method="post">
<input type="hidden" id="ID" name="bulkmail_data[ID]" value="<?php echo $list->ID; ?>">
<?php wp_nonce_field( 'bulkmail_nonce' ); ?>
<div style="height:0px; width:0px; overflow:hidden;"><input type="submit" name="save" value="1"></div>
<h1>
<?php
if ( $is_new ) :
	esc_html_e( 'Add new List', 'bulkmail' );
else :
	if ( $list->parent_id && $parent = $this->get( $list->parent_id ) ) {
		echo '<div class="parent_list"><strong><a href="edit.php?post_type=newsletter&page=bulkmail_lists&ID=' . $parent->ID . '">' . $parent->name . '</a></strong> &rsaquo; </div>';
	}
	printf( esc_html__( 'Edit List %s', 'bulkmail' ), '<strong>' . $list->name . '</strong>' );
	?>
	<?php if ( current_user_can( 'bulkmail_add_subscribers' ) ) : ?>
		<a href="edit.php?post_type=newsletter&page=bulkmail_lists&new" class="page-title-action"><?php esc_html_e( 'Add New', 'bulkmail' ); ?></a>
	<?php endif; ?>

<?php endif; ?>
<span class="alignright">
	<?php if ( ! $is_new && current_user_can( 'bulkmail_delete_lists' ) ) : ?>
		<input type="submit" name="delete" class="button button-large" value="<?php esc_attr_e( 'Delete List', 'bulkmail' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this list?', 'bulkmail' ); ?>');">
	<?php endif; ?>
	<?php if ( ! $is_new && current_user_can( 'bulkmail_delete_lists' ) && current_user_can( 'bulkmail_delete_subscribers' ) ) : ?>
		<input type="submit" name="delete_subscribers" class="button button-large" value="<?php esc_attr_e( 'Delete List with Subscribers', 'bulkmail' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this list with all subscribers?', 'bulkmail' ); ?>');">
	<?php endif; ?>
	<input type="submit" name="save" class="button button-primary button-large" value="<?php esc_attr_e( 'Save', 'bulkmail' ); ?>">
</span>
</h1>
<table class="form-table">
	<tr>
		<th scope="row"><h3><?php esc_html_e( 'Name', 'bulkmail' ); ?></h3></th>
		<td>
			<h3 class="detail">
				<ul class="click-to-edit">
					<li><?php echo esc_attr( $list->name ); ?>&nbsp;</li>
					<li><input id="name" class="widefat" type="text" name="bulkmail_data[name]" value="<?php echo esc_attr( $list->name ); ?>" placeholder="<?php esc_attr_e( 'Name of the List', 'bulkmail' ); ?>" autofocus></li>
				</ul>
			</h3>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Description', 'bulkmail' ); ?></th>
		<td>
			<div class="detail">
				<ul class="click-to-edit">
					<li><?php echo $list->description ? esc_attr( $list->description ) : '<span class="description">' . esc_html__( 'no description', 'bulkmail' ) . '</span>'; ?></li>
					<li><textarea id="description" class="widefat" type="text" name="bulkmail_data[description]"><?php echo esc_textarea( $list->description ); ?></textarea></li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Subscribers', 'bulkmail' ); ?></th>
		<td>
			<?php echo '<a href="' . add_query_arg( array( 'lists' => array( $list->ID ) ), 'edit.php?post_type=newsletter&page=bulkmail_subscribers' ) . '">' . sprintf( esc_html__( _n( '%s Subscriber', '%s Subscribers', $list->subscribers, 'bulkmail' ) ), '<strong>' . number_format_i18n( $list->subscribers ) . '</strong>' ) . '</a>'; ?>
		</td>
	</tr>
</table>

<?php
if ( ! $is_new ) :

	$actions = bulkmail( 'actions' )->get_by_list( $list->ID, null, true );

	$sent         = $actions['sent'];
	$opens        = $actions['opens'];
	$clicks       = $actions['clicks'];
	$unsubscribes = $actions['unsubscribes'];
	$bounces      = $actions['bounces'];

	$openrate        = ( $sent ) ? $opens / $sent * 100 : 0;
	$clickrate       = ( $opens ) ? $clicks / $opens * 100 : 0;
	$unsubscriberate = ( $opens ) ? $unsubscribes / $opens * 100 : 0;
	$bouncerate      = ( $sent ) ? $bounces / $sent * 100 : 0;

	?>
		<div class="stats-wrap">
			<table id="stats">
				<tr>
				<td><span class="verybold"><?php echo number_format_i18n( $sent ); ?></span> <?php echo esc_html__( _n( 'Mail sent', 'Mails sent', $sent, 'bulkmail' ) ); ?></td>
				<td width="60">
					<div id="stats_open" class="piechart" data-percent="<?php echo $openrate; ?>"><span>0</span>%</div>
				</td><td><span class="verybold"></span> <?php esc_html_e( 'open rate', 'bulkmail' ); ?></td>
				<td width="60">
					<div id="stats_click" class="piechart" data-percent="<?php echo $clickrate; ?>"><span>0</span>%</div>
				</td><td><span class="verybold"></span> <?php esc_html_e( 'click rate', 'bulkmail' ); ?></td>
				<td width="60">
					<div id="stats_unsub" class="piechart" data-percent="<?php echo $unsubscriberate; ?>"><span>0</span>%</div>
				</td><td><span class="verybold"></span> <?php esc_html_e( 'unsubscribe rate', 'bulkmail' ); ?></td>
				<td width="60">
					<div id="stats_bounce" class="piechart" data-percent="<?php echo $bouncerate; ?>"><span>0</span>%</div>
				</td><td><span class="verybold"></span> <?php esc_html_e( 'bounce rate', 'bulkmail' ); ?></td>
				</tr>
			</table>
		</div>

		<div class="activity-wrap">
			<?php if ( $activities = $this->get_activity( $list->ID ) ) : ?>

				<h3><?php esc_html_e( 'Activity', 'bulkmail' ); ?></h3>

				<table class="wp-list-table widefat">
					<thead>
						<tr><th><?php esc_html_e( 'Date', 'bulkmail' ); ?></th><th></th><th><?php esc_html_e( 'Action', 'bulkmail' ); ?></th><th><?php esc_html_e( 'Campaign', 'bulkmail' ); ?></th><th></th></tr>
					</thead>
					<tbody>
				<?php foreach ( $activities as $i => $activity ) : ?>
						<tr class="<?php echo ! ( $i % 2 ) ? ' alternate' : ''; ?>">
							<td><?php echo $now - $activity->timestamp < 3600 ? sprintf( esc_html__( '%s ago', 'bulkmail' ), human_time_diff( $now, $activity->timestamp ) ) : date( $timeformat, $activity->timestamp + $timeoffset ); ?></td>
							<td>
							<?php
							switch ( $activity->type ) {
								case 1:
									echo '<span class="bulkmail-icon bulkmail-icon-progress"></span></td><td>';
									printf( esc_html__( 'Campaign %s has start sending', 'bulkmail' ), '<a href="' . admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) . '">' . $activity->campaign_title . '</a>' );
									break;
								case 2:
										echo '<span class="bulkmail-icon bulkmail-icon-open"></span></td><td>';
										printf( esc_html__( 'First open in Campaign %s', 'bulkmail' ), '<a href="' . admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) . '">' . $activity->campaign_title . '</a>' );
									break;
								case 3:
										echo '<span class="bulkmail-icon bulkmail-icon-click"></span></td><td>';
										printf( esc_html__( '%1$s in Campaign %2$s clicked', 'bulkmail' ), '<a href="' . $activity->link . '">' . esc_html__( 'Link', 'bulkmail' ) . '</a>', '<a href="' . admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) . '">' . $activity->campaign_title . '</a>' );
									break;
								case 4:
										echo '<span class="bulkmail-icon bulkmail-icon-unsubscribe"></span></td><td>';
										echo esc_html__( 'First subscription canceled', 'bulkmail' );
									break;
								case 5:
										echo '<span class="bulkmail-icon bulkmail-icon-bounce"></span></td><td>';
										printf( esc_html__( 'Soft bounce (%d tries)', 'bulkmail' ), $activity->count );
									break;
								case 6:
										echo '<span class="bulkmail-icon bulkmail-icon-bounce hard"></span></td><td>';
										echo esc_html__( 'Hard bounce', 'bulkmail' );
									break;
								default:
										echo '</td><td>';
									break;
							}
							?>

							</td>
							<td><a href="<?php echo admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ); ?>"><?php echo $activity->campaign_title; ?></a></td>
							<td width="50%">
							<?php if ( $activity->link ) : ?>
								<a href="<?php echo esc_url( $activity->link ); ?>"><?php echo esc_url( $activity->link ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
			<?php endforeach; ?>
				</tbody>
			</table>
<?php else : ?>
		<p class="description"><?php esc_html_e( 'no activity yet', 'bulkmail' ); ?></p>
<?php endif; ?>
	</div>
<?php endif; ?>
</form>
</div>
