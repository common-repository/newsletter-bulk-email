<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );
if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$is_autoresponder = 'autoresponder' == $post->post_status || $this->post_data['autoresponder'];

?>
<?php if ( $editable ) : ?>
	<ul class="bulkmail-attachments is-editable">

		<li class="bulkmail-attachment">
			<a href="" class="delete-attachment" title="<?php echo esc_attr__( 'Remove Attachment', 'bulkmail' ); ?>">&#10005;</a>
			<img width="48" height="64" src="" class="attachment-thumbnail size-thumbnail">
			<div class="bulkmail-attachment-label"></div>
			<input value="" type="hidden" >
		</li>

	<?php if ( ! empty( $this->post_data['attachments'] ) ) : ?>
		<?php foreach ( $this->post_data['attachments'] as $attachment_id ) : ?>
			<?php $file = get_attached_file( $attachment_id ); ?>
			<li class="bulkmail-attachment">
				<a href="" class="delete-attachment" title="<?php echo esc_attr__( 'Remove Attachment', 'bulkmail' ); ?>">&#10005;</a>
				<?php echo wp_get_attachment_image( $attachment_id, 'thumbnail', true ); ?>
				<div class="bulkmail-attachment-label"><?php echo esc_html( basename( $file ) ); ?></div>
				<input name="bulkmail_data[attachments][]" value="<?php echo (int) $attachment_id; ?>" type="hidden" >
			</li>
		<?php endforeach; ?>
	<?php endif; ?>

	</ul>

	<p class="description">
		<?php esc_html_e( 'Add an attachment to your campaign.', 'bulkmail' ); ?>
	</p>
	<p class="description">
		<?php esc_html_e( 'Note: Each attachment will increase your overall mail size, consider to share a link instead of attaching a file to prevent your email getting too big.', 'bulkmail' ); ?>
	</p>

	<a href="" class="add-attachment"><?php esc_html_e( 'Add Attachment', 'bulkmail' ); ?></a>

<?php else : ?>

	<?php if ( ! empty( $this->post_data['attachments'] ) ) : ?>

	<ul class="bulkmail-attachments">
		<?php foreach ( $this->post_data['attachments'] as $attachment_id ) : ?>
			<?php $file = get_attached_file( $attachment_id ); ?>

			<li class="bulkmail-attachment">
				<a href="" class="delete-attachment" title="<?php echo esc_attr__( 'Remove Attachment', 'bulkmail' ); ?>">&#10005;</a>
				<?php echo wp_get_attachment_image( $attachment_id, 'thumbnail', true ); ?>
				<div class="bulkmail-attachment-label"><?php echo esc_html( basename( $file ) ); ?></div>
				<input name="bulkmail_data[attachments][]" value="<?php echo (int) $attachment_id; ?>" type="hidden" >
			</li>
		<?php endforeach; ?>
	</ul>
	<?php else : ?>

	<p class="description">
		<?php esc_html_e( 'This campaign doesn\'t have any attachment.', 'bulkmail' ); ?>
	</p>
	<?php endif; ?>

<?php endif; ?>
