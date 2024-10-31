<?php

$template = $this->get_template();
$file     = $this->get_file();

?>
<div id="optionbar" class="optionbar">
	<ul class="alignleft">
		<li class="no-border-left"><a class="bulkmail-icon undo disabled" title="<?php esc_attr_e( 'undo', 'bulkmail' ); ?>" aria-label="<?php esc_attr_e( 'undo', 'bulkmail' ); ?>">&nbsp;</a></li>
		<li><a class="bulkmail-icon redo disabled" title="<?php esc_attr_e( 'redo', 'bulkmail' ); ?>" aria-label="<?php esc_attr_e( 'redo', 'bulkmail' ); ?>">&nbsp;</a></li>
		<?php if ( ! empty( $module_list ) ) : ?>
		<li><a class="bulkmail-icon clear-modules" title="<?php esc_attr_e( 'remove modules', 'bulkmail' ); ?>" aria-label="<?php esc_attr_e( 'remove modules', 'bulkmail' ); ?>">&nbsp;</a></li>
		<?php endif; ?>
		<?php if ( current_user_can( 'bulkmail_see_codeview' ) ) : ?>
		<li><a class="bulkmail-icon code" title="<?php esc_attr_e( 'toggle HTML/code view', 'bulkmail' ); ?>" aria-label="<?php esc_attr_e( 'toggle HTML/code view', 'bulkmail' ); ?>">&nbsp;</a></li>
		<?php endif; ?>
		<?php if ( current_user_can( 'bulkmail_change_plaintext' ) ) : ?>
		<li><a class="bulkmail-icon plaintext" title="<?php esc_attr_e( 'toggle HTML/Plain-Text view', 'bulkmail' ); ?>" aria-label="<?php esc_attr_e( 'toggle HTML/Plain-Text view', 'bulkmail' ); ?>">&nbsp;</a></li>
		<?php endif; ?>
		<li class="no-border-right"><a class="bulkmail-icon preview" title="<?php esc_attr_e( 'preview', 'bulkmail' ); ?>" aria-label="<?php esc_attr_e( 'preview', 'bulkmail' ); ?>">&nbsp;</a></li>
	</ul>
	<ul class="alignright">
		<li><a class="bulkmail-icon dfw" title="<?php esc_attr_e( 'Distraction-free edit mode', 'bulkmail' ); ?>" aria-label="<?php esc_attr_e( 'Distraction-free edit mode', 'bulkmail' ); ?>">&nbsp;</a></li>
		<?php if ( $templates && current_user_can( 'bulkmail_save_template' ) ) : ?>
		<li><a class="bulkmail-icon save-template" title="<?php esc_attr_e( 'save template', 'bulkmail' ); ?>" aria-label="<?php esc_attr_e( 'save template', 'bulkmail' ); ?>">&nbsp;</a></li>
		<?php endif; ?>
		<?php
		if ( $templates && current_user_can( 'bulkmail_change_template' ) ) :
			$single          = count( $templates ) == 1;
			$currenttemplate = array( $template => $templates[ $template ] );
			unset( $templates[ $template ] );
			$templates = $currenttemplate + $templates;

			?>
			<li class="current_template<?php echo $single ? ' single' : ''; ?> ">
				<span class="change_template" title="<?php echo esc_attr( sprintf( esc_html__( 'Your currently working with %s', 'bulkmail' ), '"' . $all_files[ $template ][ $file ]['label'] . '"' ) ); ?>">
				<?php echo esc_html( $all_files[ $template ][ $file ]['label'] ); ?>
				</span>
				<div class="dropdown">
					<div class="ddarrow"></div>
					<div class="inner">
						<h4><?php esc_html_e( 'Change Template', 'bulkmail' ); ?></h4>
						<ul>
							<?php $current = $template . '/' . $file; ?>
							<?php foreach ( $templates as $slug => $data ) : ?>
								<li>
								<?php if ( ! $single ) : ?>
									<a class="template"><?php echo esc_html( $data['name'] ); ?><i class="version"><?php echo esc_html( $data['version'] ); ?></i></a>
								<?php endif; ?>
									<ul <?php echo ( $template == $slug ) ? ' style="display:block"' : ''; ?>>
									<?php
									foreach ( $all_files[ $slug ] as $name => $data ) :
										$value      = $slug . '/' . $name;
										$is_current = ( $current == $value );
										$url        = ! $is_current ? add_query_arg(
											array(
												'template' => $slug,
												'file'     => $name,
												'message'  => 2,
											),
											admin_url( 'post.php?post=' . $post->ID . '&action=edit' )
										) : '#';
										?>
									<li><a class="file<?php echo ( $is_current ) ? ' active' : ''; ?>" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $data['label'] ); ?></a></li>
									<?php endforeach; ?>
									</ul>
								</li>
							<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</li>
		<?php endif; ?>
	</ul>
</div>
<div id="bulkmail_template_save" style="display:none;">
	<div class="bulkmail_template_save">
			<div class="inner">
				<p>
					<label><?php esc_html_e( 'Name', 'bulkmail' ); ?><br><input type="text" class="widefat" id="new_template_name" placeholder="<?php esc_attr_e( 'template name', 'bulkmail' ); ?>" value="<?php echo $all_files[ $template ][ $file ]['label']; ?>"></label>
				</p>
				<p>
					<label><input type="radio" name="new_template_overwrite" checked value="0"> <?php esc_html_e( 'save as a new template file', 'bulkmail' ); ?></label><br>
					<label><input type="radio" name="new_template_overwrite" value="1"> <?php esc_html_e( 'overwrite', 'bulkmail' ); ?>
					<select id="new_template_saveas_dropdown">
					<?php
					$options = '';
					foreach ( $all_files[ $template ] as $name => $data ) {
						$value    = $template . '/' . $name;
						$options .= '<option value="' . esc_attr( $value ) . '" ' . selected( $current, $value, false ) . '>' . esc_attr( $data['label'] . ' (' . $name . ')' ) . '</option>';
					}
					echo $options;
					?>
					</select>
					</label>
				</p>
				<?php if ( ! empty( $module_list ) ) : ?>
				<p>
					<label><input type="checkbox" id="new_template_modules" value="1"> <?php printf( esc_html__( 'include original modules from %s', 'bulkmail' ), '&quot;' . $all_files[ $template ][ $file ]['label'] . '&quot;' ); ?></label>
					<span class="help" title="<?php esc_attr_e( 'will append the existing modules to your custom ones', 'bulkmail' ); ?>">(?)</span><br>
					<label><input type="checkbox" id="new_template_active_modules" value="1"> <?php esc_html_e( 'show custom modules by default', 'bulkmail' ); ?></label><br>
				</p>
				<?php endif; ?>

			</div>
			<div class="foot">
				<p class="description alignleft">&nbsp;<?php printf( esc_html__( 'based on %1$s from %2$s', 'bulkmail' ), '<strong>&quot;' . $all_files[ $template ][ $file ]['label'] . '&quot;</strong>', '<strong>&quot;' . $all_files[ $template ][ $file ]['name'] . '&quot;</strong>' ); ?>
				</p>
				<button class="button button-primary save-template"><?php esc_html_e( 'Save', 'bulkmail' ); ?></button>
				<button class="button save-template-cancel"><?php esc_html_e( 'Cancel', 'bulkmail' ); ?></button>
				<span class="spinner" id="new_template-ajax-loading"></span>
			</div>
	</div>
</div>
