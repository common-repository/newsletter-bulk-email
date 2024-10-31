<?php wp_nonce_field( 'bulkmail_nonce', 'bulkmail_nonce', false ); ?>
<?php $classes = array( 'wrap', 'bulkmail-tests' ); ?>

<?php
	$heading = 'Test @ ' . date( 'r' ) . ' from ' . site_url();

	$textoutput = str_repeat( '=', strlen( $heading ) ) . "\n" . $heading . "\n" . str_repeat( '=', strlen( $heading ) ) . "\n";
?>
<div class="<?php echo implode( ' ', $classes ); ?>">
<h1><?php esc_html_e( 'Bulkmail Tests', 'bulkmail' ); ?></h1>

<p><?php esc_html_e( 'Bulkmail will now run some tests to ensure everything is running smoothly. Please keep this browser window open until all tests are finished.', 'bulkmail' ); ?></p>

<div class="tests-wrap no-success">
	<a class="button button-primary button-hero start-test"><?php esc_html_e( 'Start Tests', 'bulkmail' ); ?></a>
	<input type="hidden" id="singletest" value="<?php echo isset( $_GET['test'] ) ? esc_attr( $_GET['test'] ) : ''; ?>">
	<div id="progress" class="progress"><span class="bar" style="width:0%"><span></span></span></div>
	<h4 class="test-info"><?php esc_html_e( 'Click the button to start test', 'bulkmail' ); ?></h4>
	<div id="outputnav" class="nav-tab-wrapper hide-if-no-js">
		<a class="nav-tab nav-tab-active" href="#selftest"><?php esc_html_e( 'Output', 'bulkmail' ); ?></a>
		<a class="nav-tab" href="#textoutput"><?php esc_html_e( 'Text Output', 'bulkmail' ); ?></a>
		<a class="nav-tab" href="#systeminfo"><?php esc_html_e( 'System Info', 'bulkmail' ); ?></a>
	</div>
	<div class="subtab" id="subtab-selftest">
		<p class="tests-toggles">
			<?php esc_html_e( 'Show', 'bulkmail' ); ?>:
			<label class="label-error" title="<?php esc_attr_e( 'Errors must be fixed in order to make Bulkmail work correctly.', 'bulkmail' ); ?>"> <input type="checkbox" name="" data-type="error" checked><i></i><?php esc_html_e( 'Errors', 'bulkmail' ); ?></label>
			<label class="label-warning" title="<?php esc_attr_e( 'Warnings are recommended to get fixed but not required to make Bulkmail work.', 'bulkmail' ); ?>"> <input type="checkbox" name="" data-type="warning" checked><i></i><?php esc_html_e( 'Warnings', 'bulkmail' ); ?></label>
			<label class="label-notice" title="<?php esc_attr_e( 'Notices normally don\'t require any action.', 'bulkmail' ); ?>"> <input type="checkbox" name="" data-type="notice" checked><i></i><?php esc_html_e( 'Notices', 'bulkmail' ); ?></label>
			<label class="label-success" title="<?php esc_attr_e( 'Best requirements for Bulkmail to work.', 'bulkmail' ); ?>"> <input type="checkbox" name="" data-type="success"><i></i><?php esc_html_e( 'Success', 'bulkmail' ); ?></label>
		</p>
		<div class="tests-output"></div>
	</div>
	<div class="subtab" id="subtab-textoutput">
		<div class="tests-textoutput-wrap"><textarea class="tests-textoutput code" data-pretext="<?php echo esc_attr( $textoutput ); ?>"></textarea></div>
		<a class="clipboard" data-clipboard-target=".tests-textoutput"><?php esc_html_e( 'Copy Info to Clipboard', 'bulkmail' ); ?></a>
	</div>
	<div class="subtab" id="subtab-systeminfo">
		<div class="tests-textoutput-wrap"><textarea id="system_info_content" readonly class="code">
		</textarea></div>
		<a class="clipboard" data-clipboard-target="#system_info_content"><?php esc_html_e( 'Copy Info to Clipboard', 'bulkmail' ); ?></a>
	</div>

</div>

<div id="ajax-response"></div>
<br class="clear">
</div>
