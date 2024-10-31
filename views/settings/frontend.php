<table class="form-table">
	<tr valign="top" class="settings-row settings-row-newsletter-homepage">
		<?php $bulkmail_homepage = bulkmail_option( 'homepage' ); ?>
		<th scope="row"><?php esc_html_e( 'Newsletter Homepage', 'bulkmail' ); ?></th>
		<td>
		<?php if ( array_sum( (array) wp_count_posts( 'page' ) ) > 100 ) : ?>
			<p><?php esc_html_e( 'Page ID:', 'bulkmail' ); ?> <input type="text" name="bulkmail_options[homepage]" value="<?php echo $bulkmail_homepage; ?>" class="small-text"> <span class="description"><?php esc_html_e( 'Find your Page ID in the address bar of the edit screen of this page.', 'bulkmail' ); ?></span></p>
		<?php else : ?>
			<?php
			$pages = get_posts(
				array(
					'post_type'      => 'page',
					'post_status'    => 'publish,private,draft',
					'posts_per_page' => -1,
				)
			);
			?>
			<select name="bulkmail_options[homepage]" class="postform">
				<option value="0"><?php esc_html_e( 'Choose', 'bulkmail' ); ?></option>
			<?php foreach ( $pages as $page ) { ?>
				<option value="<?php echo $page->ID; ?>"<?php selected( $bulkmail_homepage, $page->ID ); ?>>
				<?php
				echo esc_html( $page->post_title );
				if ( $page->post_status != 'publish' ) {
					echo ' (' . $wp_post_statuses[ $page->post_status ]->label . ')';
				}
				?>
				</option>
			<?php } ?>
			</select>
		<?php endif; ?>

		<?php if ( $bulkmail_homepage ) : ?>
			<span class="description">
				<a href="post.php?post=<?php echo (int) $bulkmail_homepage; ?>&action=edit"><?php esc_html_e( 'edit', 'bulkmail' ); ?></a>
				<?php esc_html_e( 'or', 'bulkmail' ); ?>
				<a href="<?php echo get_permalink( $bulkmail_homepage ); ?>" class="external"><?php esc_html_e( 'visit', 'bulkmail' ); ?></a>
			</span>
		<?php else : ?>
			<span class="description"><a href="<?php echo add_query_arg( 'bulkmail_create_homepage', wp_create_nonce( 'bulkmail_create_homepage' ), admin_url() ); ?>"><?php esc_html_e( 'create it right now', 'bulkmail' ); ?></a>
			</span>
		<?php endif; ?>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-search-engine-visibility">
		<th scope="row"><?php esc_html_e( 'Search Engine Visibility', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[frontpage_public]" value=""><input type="checkbox" name="bulkmail_options[frontpage_public]" value="1" <?php checked( bulkmail_option( 'frontpage_public' ) ); ?>> <?php esc_html_e( 'Discourage search engines from indexing your campaigns', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-webversion-bar">
		<th scope="row"><?php esc_html_e( 'Webversion Bar', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[webversion_bar]" value=""><input type="checkbox" class="webversion-bar-checkbox" name="bulkmail_options[webversion_bar]" value="1" <?php checked( bulkmail_option( 'webversion_bar' ) ); ?>> <?php esc_html_e( 'Show the top bar on the web version', 'bulkmail' ); ?></label>
		</td>
	</tr>
</table>
<div id="webversion-bar-options"<?php echo ! bulkmail_option( 'webversion_bar' ) ? ' style="display:none"' : ''; ?>>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-pagination">
		<th scope="row"><?php esc_html_e( 'Pagination', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[frontpage_pagination]" value=""><input type="checkbox" name="bulkmail_options[frontpage_pagination]" value="1" <?php checked( bulkmail_option( 'frontpage_pagination' ) ); ?>> <?php esc_html_e( 'Allow users to view the next/last newsletters', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-share-button">
		<th scope="row"><?php esc_html_e( 'Share Button', 'bulkmail' ); ?></th>
		<td><label><input type="hidden" name="bulkmail_options[share_button]" value=""><input type="checkbox" name="bulkmail_options[share_button]" value="1" <?php checked( bulkmail_option( 'share_button' ) ); ?>> <?php esc_html_e( 'Offer share option for your customers', 'bulkmail' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-services">
		<th scope="row"><?php esc_html_e( 'Services', 'bulkmail' ); ?></th>
		<td><ul class="frontpage-social-services">
		<?php

		$social_services = bulkmail( 'helper' )->social_services();

		$services = bulkmail_option( 'share_services', array() );
		?>
		<?php foreach ( $social_services as $service => $data ) : ?>
			<li class="<?php echo $service; ?>"><label><input type="checkbox" name="bulkmail_options[share_services][]" value="<?php echo esc_attr( $service ); ?>" <?php checked( in_array( $service, $services ) ); ?>> <?php echo $data['name']; ?></label></li>
		<?php endforeach; ?>
		</ul></td>
	</tr>
</table>
</div>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-campaign-slug">
		<th scope="row"><?php esc_html_e( 'Campaign slug', 'bulkmail' ); ?></th>
		<td><p>
		<?php if ( bulkmail( 'helper' )->using_permalinks() ) : ?>
		<span class="description"><?php echo get_bloginfo( 'url' ); ?>/</span><input type="text" name="bulkmail_options[slug]" value="<?php echo esc_attr( bulkmail_option( 'slug', 'newsletter' ) ); ?>" class="small-text" style="width:80px"><span class="description">/my-campaign</span><br><span class="description"><?php esc_html_e( 'changing the slug may cause broken links in previous sent campaigns!', 'bulkmail' ); ?></span>
		<?php else : ?>
		<span class="description"><?php printf( esc_html_x( 'Define a %s to enable custom slugs', 'Campaign slug', 'bulkmail' ), '<a href="options-permalink.php">' . esc_html__( 'Permalink Structure', 'bulkmail' ) . '</a>' ); ?></span>
		<input type="hidden" name="bulkmail_options[slug]" value="<?php echo esc_attr( bulkmail_option( 'slug', 'newsletter' ) ); ?>">
		<?php endif; ?>
		</p>
		</td>
	</tr>
	<?php
	$slugs = bulkmail_option(
		'slugs',
		array(
			'confirm'     => 'confirm',
			'subscribe'   => 'subscribe',
			'unsubscribe' => 'unsubscribe',
			'profile'     => 'profile',
		)
	);

	if ( bulkmail( 'helper' )->using_permalinks() && bulkmail_option( 'homepage' ) ) :
		$homepage = trailingslashit( get_permalink( bulkmail_option( 'homepage' ) ) );
		?>
		<tr valign="top" class="settings-row settings-row-homepage-slugs">
			<th scope="row"><?php esc_html_e( 'Homepage slugs', 'bulkmail' ); ?></th>
			<td class="homepage-slugs">
			<p>
			<label><?php esc_html_e( 'Confirm Slug', 'bulkmail' ); ?>:</label><br>
				<span>
					<?php echo $homepage; ?><strong><?php echo $slugs['confirm']; ?></strong>/
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'bulkmail' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $homepage; ?><input type="text" name="bulkmail_options[slugs][confirm]" value="<?php echo esc_attr( $slugs['confirm'] ); ?>" class="small-text">/
				</span>
			</p>
			<p>
			<label><?php esc_html_e( 'Subscribe Slug', 'bulkmail' ); ?>:</label><br>
				<span>
					<?php echo $homepage; ?><strong><?php echo $slugs['subscribe']; ?></strong>/
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'bulkmail' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $homepage; ?><input type="text" name="bulkmail_options[slugs][subscribe]" value="<?php echo esc_attr( $slugs['subscribe'] ); ?>" class="small-text">/
				</span>
			</p>
			<p>
			<label><?php esc_html_e( 'Unsubscribe Slug', 'bulkmail' ); ?>:</label><br>
				<span>
					<a href="<?php echo $homepage . esc_attr( $slugs['unsubscribe'] ); ?>" class="external"><?php echo $homepage; ?><strong><?php echo $slugs['unsubscribe']; ?></strong>/</a>
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'bulkmail' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $homepage; ?><input type="text" name="bulkmail_options[slugs][unsubscribe]" value="<?php echo esc_attr( $slugs['unsubscribe'] ); ?>" class="small-text">/
				</span>
			</p>
			<p>
			<label><?php esc_html_e( 'Profile Slug', 'bulkmail' ); ?>:</label><br>
				<span>
					<a href="<?php echo $homepage . esc_attr( $slugs['profile'] ); ?>" class="external"><?php echo $homepage; ?><strong><?php echo $slugs['profile']; ?></strong>/</a>
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'bulkmail' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $homepage; ?><input type="text" name="bulkmail_options[slugs][profile]" value="<?php echo esc_attr( $slugs['profile'] ); ?>" class="small-text">/
				</span>
			</p>
			</td>
		</tr>
	<?php else : ?>

		<input type="hidden" name="bulkmail_options[slugs][confirm]" value="<?php echo esc_attr( $slugs['confirm'] ); ?>">
		<input type="hidden" name="bulkmail_options[slugs][subscribe]" value="<?php echo esc_attr( $slugs['subscribe'] ); ?>">
		<input type="hidden" name="bulkmail_options[slugs][unsubscribe]" value="<?php echo esc_attr( $slugs['unsubscribe'] ); ?>">
		<input type="hidden" name="bulkmail_options[slugs][profile]" value="<?php echo esc_attr( $slugs['profile'] ); ?>">

	<?php endif; ?>

	<?php if ( bulkmail( 'helper' )->using_permalinks() ) : ?>
		<tr valign="top" class="settings-row settings-row-archive">
			<th scope="row"><?php esc_html_e( 'Archive', 'bulkmail' ); ?></th>
			<td class="homepage-slugs"><p><label><input type="hidden" name="bulkmail_options[hasarchive]" value=""><input type="checkbox" name="bulkmail_options[hasarchive]" class="has-archive-check" value="1" <?php checked( bulkmail_option( 'hasarchive' ) ); ?>> <?php esc_html_e( 'enable archive function to display your newsletters in a reverse chronological order', 'bulkmail' ); ?></label>
				</p>
			<div class="archive-slug"<?php echo ! bulkmail_option( 'hasarchive' ) ? ' style="display:none"' : ''; ?>>
				<p>
				<label><?php esc_html_e( 'Archive Slug', 'bulkmail' ); ?>:</label><br>
				<?php
					$homepage = home_url( '/' );
					$slug     = bulkmail_option( 'archive_slug', 'newsletter' );
				?>
				<span>
					<a href="<?php echo $homepage . esc_attr( $slug ); ?>" class="external"><?php echo $homepage; ?><strong><?php echo $slug; ?></strong>/</a>
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'bulkmail' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo esc_html( $homepage ); ?><input type="text" name="bulkmail_options[archive_slug]" value="<?php echo esc_attr( $slug ); ?>" class="small-text">/
				</span>
				</p>
				<p>
					<label>
					<?php esc_html_e( 'show only', 'bulkmail' ); ?>:
					</label>
					<?php $archive_types = bulkmail_option( 'archive_types', array( 'finished', 'active' ) ); ?>
					<label> <input type="checkbox" name="bulkmail_options[archive_types][]" value="finished" <?php checked( in_array( 'finished', $archive_types ) ); ?>> <?php esc_html_e( 'finished', 'bulkmail' ); ?> </label>
					<label> <input type="checkbox" name="bulkmail_options[archive_types][]" value="active" <?php checked( in_array( 'active', $archive_types ) ); ?>> <?php esc_html_e( 'active', 'bulkmail' ); ?> </label>
					<label> <input type="checkbox" name="bulkmail_options[archive_types][]" value="paused" <?php checked( in_array( 'paused', $archive_types ) ); ?>> <?php esc_html_e( 'paused', 'bulkmail' ); ?> </label>
					<label> <input type="checkbox" name="bulkmail_options[archive_types][]" value="queued" <?php checked( in_array( 'queued', $archive_types ) ); ?>> <?php esc_html_e( 'queued', 'bulkmail' ); ?> </label>
				</p>
			</div>
			</td>
		</tr>
	<?php else : ?>
		<input type="hidden" name="bulkmail_options[hasarchive]" value="<?php echo esc_attr( bulkmail_option( 'hasarchive' ) ); ?>">
		<input type="hidden" name="bulkmail_options[archive_slug]" value="<?php echo esc_attr( bulkmail_option( 'archive_slug', 'newsletter' ) ); ?>">
	<?php endif; ?>

</table>
