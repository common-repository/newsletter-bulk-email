<h2 class="dialog-label"><?php esc_html_e( 'Bulkmail Deactivation', 'bulkmail' ); ?></h2>

<form id="bulkmail-deactivation-survey" method="POST" action="<?php echo add_query_arg( 'bulkmail_deactivation_survey', true ); ?>">
	<?php wp_nonce_field( 'bulkmail_deactivation_survey', 'bulkmail_nonce', false ); ?>
	<input type="hidden" name="bulkmail_surey_extra" value=''>
	<p><?php esc_html_e( 'If you have a moment, please let us know why you are deactivating Bulkmail. We only use this feedback to improve the plugin.', 'bulkmail' ); ?></p>
	<div>
		<label><input type="radio" name="bulkmail_surey_reason" value="It's a temporary deactivation." required> <?php esc_html_e( 'It\'s a temporary deactivation.', 'bulkmail' ); ?></label>
	</div>
	<div>
		<label><input type="radio" name="bulkmail_surey_reason" value="I no longer need the plugin." required> <?php esc_html_e( 'I no longer need the plugin.', 'bulkmail' ); ?></label>
	</div>
	<div>
		<label><input type="radio" name="bulkmail_surey_reason" value="The plugin didn't work." required> <?php esc_html_e( 'The plugin didn\'t work.', 'bulkmail' ); ?></label>
		<div class="bulkmail-survey-extra">
			<p><?php sprintf( esc_html__( 'We\'re sorry about that. Please get in touch with our %s.', 'bulkmail' ), '<a href="https://evp.to/support">' . esc_html__( 'support', 'bulkmail' ) . '</a>' ); ?></p>
			<textarea disabled name="bulkmail_surey_extra" class="widefat" rows="5"></textarea>
		</div>
	</div>
	<div>
		<label><input type="radio" name="bulkmail_surey_reason" value="The plugin broke my site." required> <?php esc_html_e( 'The plugin broke my site.', 'bulkmail' ); ?></label>
		<div class="bulkmail-survey-extra">
			<p><?php sprintf( esc_html__( 'We\'re sorry about that. Please get in touch with our %s.', 'bulkmail' ), '<a href="https://evp.to/support">' . esc_html__( 'support', 'bulkmail' ) . '</a>' ); ?></p>
			<textarea disabled name="bulkmail_surey_extra" class="widefat" rows="5"></textarea>
		</div>
	</div>
	<div>
		<label><input type="radio" name="bulkmail_surey_reason" value="I found a better plugin." required> <?php esc_html_e( 'I found a better plugin.', 'bulkmail' ); ?></label>
		<div class="bulkmail-survey-extra">
			<p><?php esc_html_e( 'What is the name of the plugin?', 'bulkmail' ); ?></p>
			<textarea disabled name="bulkmail_surey_extra" class="widefat" rows="5"></textarea>
		</div>
	</div>
	<div>
		<label><input type="radio" name="bulkmail_surey_reason" value="Other" required> <?php esc_html_e( 'Other', 'bulkmail' ); ?></label>
		<div class="bulkmail-survey-extra">
			<p><?php esc_html_e( 'Please describe why you\'re deactivating Bulkmail.', 'bulkmail' ); ?></p>
			<textarea disabled name="bulkmail_surey_extra" class="widefat" rows="5"></textarea>
		</div>
	</div>

	<div class="bulkmail-delete-data">
		<p>
			<label><input type="checkbox" name="delete_data" value="1"> <?php esc_html_e( 'Would you like to delete all data?', 'bulkmail' ); ?></label>
		</p>
		<p>
			 <label><input type="checkbox" name="delete_campaigns" value="1" disabled> <?php esc_html_e( 'Delete Campaigns', 'bulkmail' ); ?></label><br>
			 <label><input type="checkbox" name="delete_capabilities" value="1" disabled> <?php esc_html_e( 'Delete Capabilities', 'bulkmail' ); ?></label><br>
			 <label><input type="checkbox" name="delete_tables" value="1" disabled> <?php esc_html_e( 'Delete Tables', 'bulkmail' ); ?></label><br>
			 <label><input type="checkbox" name="delete_options" value="1" disabled> <?php esc_html_e( 'Delete Options', 'bulkmail' ); ?></label><br>
			 <label><input type="checkbox" name="delete_files" value="1" disabled> <?php esc_html_e( 'Delete Files', 'bulkmail' ); ?></label>
		</p>
		<p><?php esc_html_e( 'Bulkmail does not delete any data on plugin deactivation by default. If you like to start with a fresh setup you can check this option and Bulkmail will remove all campaigns, subscribers, actions and other data in your database.', 'bulkmail' ); ?><br><strong><?php esc_html_e( 'Note: This will permanently delete all Bulkmail data from your database.', 'bulkmail' ); ?></strong>
		</p>
	</div>
</form>
