<?php
$update         = isset( $bulkmail_templates[ $slug ] ) && $bulkmail_templates[ $slug ]['update'] && current_user_can( 'bulkmail_update_templates' );
$envato_item_id = isset( $bulkmail_templates[ $slug ]['envato_item_id'] ) ? $bulkmail_templates[ $slug ]['envato_item_id'] : null;

$is_free = isset( $bulkmail_templates[ $slug ] ) && isset( $bulkmail_templates[ $slug ]['is_free'] ) && $bulkmail_templates[ $slug ]['is_free'];

$class = array( 'bulkmail-box' );
$badge = '';
if ( $update ) {
	$class[] = 'update';
}
if ( $default == $slug ) {
	$class[] = 'is-default';
	$badge   = esc_attr__( 'Default', 'bulkmail' );
}
if ( $new == $slug ) {
	$class[] = 'is-new';
	$badge   = esc_attr__( 'New', 'bulkmail' );
}

?>
<li class="<?php echo implode( ' ', $class ); ?>" id="template-<?php echo esc_attr( $slug ); ?>" name="bulkmail_template_<?php echo $i; ?>" data-id="<?php echo $i++; ?>" data-badge="<?php echo esc_attr( $badge ); ?>">
	<?php if ( isset( $updates[ $slug ] ) ) : ?>
		<span class="update-badge"><?php echo $updates[ $slug ]; ?></span>
	<?php endif; ?>
	<div class="screenshot" style="background-image:url(<?php echo $t->get_screenshot( $slug ); ?>)" title="<?php echo esc_attr( $data['name'] . ' ' . $data['version'] ); ?> <?php esc_html_e( 'by', 'bulkmail' ); ?> <?php echo $data['author']; ?>">
		<a class="thickbox-preview" href="<?php echo esc_url( $t->url . '/' . $slug . '/index.html' ); ?>" data-slug="<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Preview', 'bulkmail' ); ?></a>
		<a class="" href="<?php echo admin_url( 'post-new.php?post_type=newsletter&template=' . $slug . '' ); ?>" data-slug="<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Start new Campaign', 'bulkmail' ); ?></a>
	</div>
	<div class="meta">
		<h3><?php echo esc_html( $data['name'] ); ?> <span class="version"><?php echo esc_html( $data['version'] ); ?></span>
			<?php if ( $update ) : ?>
				<?php if ( ! $is_free && $envato_item_id ) : ?>

					<?php
					$url = add_query_arg(
						array(
							'auth'     => wp_create_nonce( 'envato-activate' ),
							'item_id'  => $bulkmail_templates[ $slug ]['envato_item_id'],
							'slug'     => $slug,
							'returnto' => urlencode( admin_url( 'edit.php?post_type=newsletter&page=bulkmail_templates' ) ),
						),
						$bulkmail_templates[ $slug ]['endpoint']
					);
					?>

					<a title="<?php esc_attr_e( 'update via Envato', 'bulkmail' ); ?>" class="update envato-activate button button-primary button-small alignright" href="<?php echo esc_url( $url ); ?>" data-slug="<?php echo esc_attr( $slug ); ?>">
						<?php printf( esc_html__( 'Update to %s', 'bulkmail' ), $bulkmail_templates[ $slug ]['new_version'] ); ?>
					</a>

				<?php else : ?>

					<a title="<?php esc_attr_e( 'update template', 'bulkmail' ); ?>" class="update button button-primary button-small alignright" href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=bulkmail_templates&action=update&template=' . $slug . '&_wpnonce=' . wp_create_nonce( 'download-' . $slug ) ); ?>" data-slug="<?php echo esc_attr( $slug ); ?>">
						<?php printf( esc_html__( 'Update to %s', 'bulkmail' ), $bulkmail_templates[ $slug ]['new_version'] ); ?>
					</a>

				<?php endif; ?>
			<?php endif; ?>
		</h3>
		<?php if ( $data['author'] ) : ?>
		<div>
			<?php esc_html_e( 'by', 'bulkmail' ); ?>
			<?php if ( ! empty( $data['author_uri'] ) ) : ?>
				<a class="external" href="<?php echo esc_html( $data['author_uri'] ); ?>"><?php echo esc_html( $data['author'] ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $data['author'] ); ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php if ( ! empty( $data['description'] ) ) : ?>
		<p class="description"><?php echo $data['description']; ?></p>
	<?php elseif ( ! empty( $bulkmail_templates[ $slug ]['description'] ) ) : ?>
		<p class="description"><?php echo $bulkmail_templates[ $slug ]['description']; ?></p>
	<?php endif; ?>
	<div class="action-links">
		<ul>
			<?php if ( $default != $slug ) : ?>
			<li><a title="Set &quot;<?php echo $data['name']; ?>&quot; as default" class="activatelink button" href="edit.php?post_type=newsletter&amp;page=bulkmail_templates&amp;action=activate&amp;template=<?php echo $slug; ?>&amp;_wpnonce=<?php echo wp_create_nonce( 'activate-' . $slug ); ?>"><?php esc_html_e( 'Use as default', 'bulkmail' ); ?></a></li>
			<?php endif; ?>
			<?php if ( current_user_can( 'bulkmail_edit_templates' ) ) : ?>
				<?php $writeable = wp_is_writable( $t->path . '/' . $slug . '/index.html' ); ?>
				<li>
					<a title="<?php printf( esc_attr__( 'Edit %s', 'bulkmail' ), '"' . $data['name'] . '"' ); ?>" class="edit <?php echo ! $writeable ? 'disabled' : ''; ?> button" data-slug="<?php echo esc_attr( $slug ); ?>" href="<?php echo $slug . '/index.html'; ?>"
										 <?php
											if ( ! $writeable ) :
												?>
					onclick="alert('<?php esc_html_e( 'This file is not writeable! Please change the file permission', 'bulkmail' ); ?>');return false;"<?php endif; ?>><?php esc_html_e( 'Edit HTML', 'bulkmail' ); ?></a>
				</li>
			<?php endif; ?>
		</ul>
		<?php if ( $slug != bulkmail_option( 'default_template' ) && current_user_can( 'bulkmail_delete_templates' ) ) : ?>
			<div class="delete-theme">
				<a data-name="<?php echo esc_attr( $data['name'] ); ?>" href="edit.php?post_type=newsletter&amp;page=bulkmail_templates&amp;action=delete&amp;template=<?php echo $slug; ?>&amp;_wpnonce=<?php echo wp_create_nonce( 'delete-' . $slug ); ?>" class="submitdelete deletion"><?php esc_html_e( 'Delete', 'bulkmail' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
	<div class="loader"></div>
</li>
