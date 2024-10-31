<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );

if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$ignore_lists = isset( $this->post_data['ignore_lists'] ) ? ! ! $this->post_data['ignore_lists'] : false;

?>
<?php if ( $editable ) : ?>
<div>
	<div id="receivers-dialog" style="display:none;">
		<div class="bulkmail-conditions-thickbox">
			<div class="inner">
				<?php bulkmail( 'conditions' )->view( $this->post_data['list_conditions'] ); ?>
			</div>
			<div class="foot">
				<div class="alignleft"><?php esc_html_e( 'Total receivers', 'bulkmail' ); ?>: <span class="bulkmail-total">&ndash;</span></div>
				<div class="alignright">
				<button class="button button-primary close-conditions"><?php esc_html_e( 'Close', 'bulkmail' ); ?></button>
				<span class="spinner" id="conditions-ajax-loading"></span>
				</div>
			</div>
		</div>
	</div>

	<div>
		<div class="lists">

			<?php $checked = wp_parse_args( isset( $_GET['lists'] ) ? $_GET['lists'] : array(), $this->post_data['lists'] ); ?>

			<div id="list-checkboxes"<?php echo $ignore_lists ? ' style="display:none"' : ''; ?>>
				<?php bulkmail( 'lists' )->print_it( null, null, 'bulkmail_data[lists]', true, $checked ); ?>
				<label><input type="checkbox" id="all_lists"> <?php esc_html_e( 'toggle all', 'bulkmail' ); ?></label>
			</div>
			<ul>
				<li><label><input id="ignore_lists" type="checkbox" name="bulkmail_data[ignore_lists]" value="1" <?php checked( $ignore_lists ); ?>> <?php esc_html_e( 'List doesn\'t matter', 'bulkmail' ); ?> </label></li>
			</ul>

		</div>
		<div><strong><?php esc_html_e( 'Conditions', 'bulkmail' ); ?>:</strong>
			<div id="bulkmail_conditions_render">
			<?php bulkmail( 'conditions' )->render( $this->post_data['list_conditions'] ); ?>
			</div>
		</div>
	</div>
	<p>
		<button class="button edit-conditions"><?php esc_html_e( 'Edit Conditions', 'bulkmail' ); ?></button> <?php esc_html_e( 'or', 'bulkmail' ); ?> <a class="remove-conditions" href="#"><?php esc_html_e( 'remove all', 'bulkmail' ); ?></a>
	</p>

</div>
<p class="totals"><?php esc_html_e( 'Total receivers', 'bulkmail' ); ?>: <span class="bulkmail-total">&ndash;</span></p>
	<?php else : ?>
	<p>
		<?php
		if ( $ignore_lists ) :

			esc_html_e( 'Any List', 'bulkmail' );

		else :
			$list  = array();
			$lists = bulkmail( 'lists' )->get();

			if ( ! empty( $this->post_data['lists'] ) ) {
				esc_html_e( 'Lists', 'bulkmail' );
				foreach ( $lists as $i => $list ) {
					if ( in_array( $list->ID, $this->post_data['lists'] ) ) {
						if ( $i ) {
							echo ', ';
						}
						echo ' <strong><a href="edit.php?post_type=newsletter&page=bulkmail_lists&ID=' . $list->ID . '">' . $list->name . '</a></strong>';
					}
				}
			} else {
				esc_html_e( 'no lists selected', 'bulkmail' );
			}

		endif;
		?>
	</p>
		<?php if ( isset( $this->post_data['list_conditions'] ) ) : ?>
		<p><strong><?php esc_html_e( 'only if', 'bulkmail' ); ?>:</strong>
			<?php bulkmail( 'conditions' )->render( $this->post_data['list_conditions'] ); ?>
		</p>
		<?php endif; ?>
	<?php endif; ?>



<?php if ( ! $editable && 'autoresponder' != $post->post_status && current_user_can( 'bulkmail_edit_lists' ) ) : ?>

	<a class="create-new-list button" href="#"><?php esc_html_e( 'create new list', 'bulkmail' ); ?></a>
	<div class="create-new-list-wrap">
		<h4><?php esc_html_e( 'create a new list with all', 'bulkmail' ); ?></h4>
		<p>
		<select class="create-list-type">
		<?php
		$options = array(
			'sent'           => esc_html__( 'who have received', 'bulkmail' ),
			'not_sent'       => esc_html__( 'who have not received', 'bulkmail' ),
			'open'           => esc_html__( 'who have opened', 'bulkmail' ),
			'open_not_click' => esc_html__( 'who have opened but not clicked', 'bulkmail' ),
			'click'          => esc_html__( 'who have opened and clicked', 'bulkmail' ),
			'not_open'       => esc_html__( 'who have not opened', 'bulkmail' ),
		);
		foreach ( $options as $id => $option ) {
			?>
			<option value="<?php echo $id; ?>"><?php echo $option; ?></option>
		<?php } ?>
		</select>
		</p>
		<p>
			<a class="create-list button"><?php esc_html_e( 'create list', 'bulkmail' ); ?></a>
		</p>
		<p class="totals">
			<?php esc_html_e( 'Total receivers', 'bulkmail' ); ?>: <span class="bulkmail-total">-</span>
		</p>
	</div>
<?php endif; ?>
