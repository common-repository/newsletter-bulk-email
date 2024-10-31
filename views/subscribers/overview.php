<?php

$table = new Bulkmail_Subscribers_Table();

$table->prepare_items();

?>
<div class="wrap">
<h1>
<?php printf( esc_html__( _n( '%s Subscriber found', '%s Subscribers found', $table->total_items, 'bulkmail' ) ), number_format_i18n( $table->total_items ) ); ?>
<?php if ( current_user_can( 'bulkmail_add_subscribers' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=bulkmail_subscribers&new" class="page-title-action"><?php esc_html_e( 'Add New', 'bulkmail' ); ?></a>
<?php endif; ?>
<?php if ( current_user_can( 'bulkmail_import_subscribers' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=bulkmail_manage_subscribers&tab=import" class="page-title-action"><?php esc_html_e( 'Import', 'bulkmail' ); ?></a>
<?php endif; ?>
<?php if ( current_user_can( 'bulkmail_export_subscribers' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=bulkmail_manage_subscribers&tab=export" class="page-title-action"><?php esc_html_e( 'Export', 'bulkmail' ); ?></a>
<?php endif; ?>
<?php if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) : ?>
	<span class="subtitle"><?php printf( esc_html__( 'Search result for %s', 'bulkmail' ), '&quot;' . esc_html( stripslashes( $_GET['s'] ) ) . '&quot;' ); ?></span>
	<?php endif; ?>
</h1>
<?php
$table->search_box( esc_html__( 'Search Subscribers', 'bulkmail' ), 's' );
$table->views();

$text = sprintf( esc_html__( 'Do you like to select all %s subscribers?', 'bulkmail' ), number_format_i18n( $table->total_items ) );

?>
<form method="post" action="" id="subscribers-overview-form">
<input type="hidden" name="all_subscribers" id="all_subscribers" data-label="<?php echo esc_attr( $text ); ?>" data-count="<?php echo $table->total_items; ?>" value="0">
<?php $table->display(); ?>
</form>
</div>
