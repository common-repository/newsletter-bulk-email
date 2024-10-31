<div class="wrap">
<h1><?php esc_html_e( 'Forms', 'bulkmail' ); ?>
<?php if ( current_user_can( 'bulkmail_add_forms' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=bulkmail_forms&new" class="page-title-action"><?php esc_html_e( 'Add New', 'bulkmail' ); ?></a>
<?php endif; ?>
<?php if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) : ?>
	<span class="subtitle"><?php printf( esc_html__( 'Search result for %s', 'bulkmail' ), '&quot;' . esc_html( stripslashes( $_GET['s'] ) ) . '&quot;' ); ?></span>
<?php endif; ?>
</h1>
<?php

$table = new Bulkmail_Forms_Table();

$table->prepare_items();
$table->search_box( esc_html__( 'Search Forms', 'bulkmail' ), 's' );
$table->views();
?>
<form method="post" action="" id="forms-overview-form">
<?php
$table->display();
?>
</form>
</div>
