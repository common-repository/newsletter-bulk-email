<?php

$id = isset( $_GET['ID'] ) ? (int) $_GET['ID'] : null;

$currentpage = isset( $_GET['tab'] ) ? $_GET['tab'] : 'structure';

$is_new = isset( $_GET['new'] );

if ( ! $is_new ) {
	if ( ! ( $form = $this->get( $id, true ) ) ) {
		echo '<h2>' . esc_html__( 'This form does not exist or has been deleted!', 'bulkmail' ) . '</h2>';
		return;
	}
} else {

	if ( ! current_user_can( 'bulkmail_add_forms' ) ) {
		echo '<h2>' . esc_html__( 'You don\'t have the right permission to add new forms', 'bulkmail' ) . '</h2>';
		return;
	}

	$form         = $this->get_empty();
	$form->submit = bulkmail_text( 'submitbutton' );
	$form->fields = array(
		(object) array(
			'field_id'  => 'email',
			'error_msg' => '',
			'name'      => bulkmail_text( 'email' ),
			'required'  => true,
		),
	);
	if ( isset( $_POST['bulkmail_data'] ) ) {
		$form = (object) wp_parse_args( $_POST['bulkmail_data'], (array) $form );
	}
}
$timeformat   = bulkmail( 'helper' )->timeformat();
$timeoffset   = bulkmail( 'helper' )->gmt_offset( true );
$customfields = bulkmail()->get_custom_fields();

$now = time();

$tabindex = 1;

$defaultfields = array(
	'email'     => bulkmail_text( 'email' ),
	'firstname' => bulkmail_text( 'firstname' ),
	'lastname'  => bulkmail_text( 'lastname' ),
);
if ( $customfields ) {
	foreach ( $customfields as $field => $data ) {
		$defaultfields[ $field ] = $data['name'];
	}
}

?>
<div class="wrap<?php echo ( $is_new ) ? ' new' : ''; ?>">
<form id="form_form" action="<?php echo add_query_arg( array( 'ID' => $id ) ); ?>" method="post">
<?php wp_nonce_field( 'bulkmail_nonce' ); ?>
<div style="height:0px; width:0px; overflow:hidden;"><input type="submit" name="save" value="1"></div>
<?php if ( $currentpage != 'use' ) : ?>
<p class="alignright">
	<?php if ( ! $is_new && current_user_can( 'bulkmail_delete_forms' ) ) : ?>
		<input type="submit" name="delete" class="button button-large" value="<?php esc_attr_e( 'Delete Form', 'bulkmail' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this form?', 'bulkmail' ); ?>');">
	<?php endif; ?>
	<input type="submit" name="save" class="button button-primary" value="<?php esc_attr_e( 'Save', 'bulkmail' ); ?>">
</p>
<?php endif; ?>
<h1>
<?php
if ( $is_new ) :
	esc_html_e( 'Add new Form', 'bulkmail' );
else :
	esc_html_e( 'Edit Form', 'bulkmail' );
	?>
<input type="hidden" id="ID" name="bulkmail_data[ID]" value="<?php echo (int) $form->ID; ?>">
	<?php if ( current_user_can( 'bulkmail_add_forms' ) ) : ?>
		<a href="edit.php?post_type=newsletter&page=bulkmail_forms&new" class="page-title-action"><?php esc_html_e( 'Add New', 'bulkmail' ); ?></a>
	<?php endif; ?>
<?php endif; ?>
<?php if ( ! $is_new ) : ?>
	<a href="#TB_inline?&width=1200&height=600&inlineId=useitbox" class="page-title-action" id="use-it"><?php esc_html_e( 'Use it!', 'bulkmail' ); ?></a>
<?php endif; ?>
</h1>
<?php if ( ! $is_new ) : ?>
<h1 class="nav-tab-wrapper">

	<a class="nav-tab <?php echo ( 'structure' == $currentpage ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=newsletter&page=bulkmail_forms&ID=<?php echo $id; ?>"><?php esc_html_e( 'Fields', 'bulkmail' ); ?></a>

	<a class="nav-tab <?php echo ( 'design' == $currentpage ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=newsletter&page=bulkmail_forms&ID=<?php echo $id; ?>&tab=design"><?php esc_html_e( 'Design', 'bulkmail' ); ?></a>

	<a class="nav-tab <?php echo ( 'settings' == $currentpage ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=newsletter&page=bulkmail_forms&ID=<?php echo $id; ?>&tab=settings"><?php esc_html_e( 'Settings', 'bulkmail' ); ?></a>

</h1>
<?php endif; ?>
<div id="titlewrap">
	<input type="text" class="widefat" name="bulkmail_data[name]" size="30" value="<?php echo esc_attr( $form->name ); ?>" id="title" spellcheck="true" autocomplete="off" autofocus placeholder="<?php esc_attr_e( 'Enter Form Name', 'bulkmail' ); ?>">

</div>
<?php if ( 'structure' == $currentpage ) : ?>
	<?php if ( ! $is_new ) : ?>
<p class="section-nav"><span class="alignright"><input type="submit" name="design" value="<?php esc_attr_e( 'Design', 'bulkmail' ); ?> &raquo;" class="button-primary button-small"></span></p>
	<?php endif; ?>

<p class="description"><?php esc_html_e( 'Define the structure of your form below. Drag available fields in the left area to add them to your form. Rearrange fields by dragging fields around', 'bulkmail' ); ?></p>
<div id="form-builder">
	<fieldset id="form-structure">
		<legend><?php esc_html_e( 'Form Fields', 'bulkmail' ); ?></legend>

		<ul class="form-order sortable">
		<?php foreach ( $form->fields as $field ) : ?>
			<li class="field-<?php echo $field->field_id; ?> form-field">
				<label><?php echo strip_tags( $field->name ); ?></label>
				<div>
					<span class="label"><?php esc_html_e( 'Label', 'bulkmail' ); ?>:</span>
					<input class="label widefat" type="text" name="bulkmail_structure[fields][<?php echo $field->field_id; ?>]" data-name="bulkmail_structure[fields][<?php echo $field->field_id; ?>]" value="<?php echo esc_attr( $field->name ); ?>" title="<?php esc_attr_e( 'define a label for this field', 'bulkmail' ); ?>" placeholder="<?php echo esc_attr( $field->name ); ?>">
					<span class="alignright required-field"><input type="checkbox" name="bulkmail_structure[required][<?php echo $field->field_id; ?>]" data-name="bulkmail_structure[required][<?php echo $field->field_id; ?>]" class="form-order-check-required" value="1" <?php checked( $field->required ); ?> <?php disabled( $field->field_id == 'email' ); ?>> <?php esc_html_e( 'required', 'bulkmail' ); ?>
						<a class="field-remove" title="<?php esc_attr_e( 'remove field', 'bulkmail' ); ?>">&#10005;</a>
					</span>
				</div>
				<div>
					<span class="label"><?php esc_html_e( 'Error Message', 'bulkmail' ); ?>:</span>
					<input class="label widefat error-msg" type="text" name="bulkmail_structure[error_msg][<?php echo $field->field_id; ?>]" data-name="bulkmail_structure[error_msg][<?php echo $field->field_id; ?>]" value="<?php echo esc_attr( $field->error_msg ); ?>" title="<?php esc_attr_e( 'define an error message for this field', 'bulkmail' ); ?>" placeholder="<?php esc_attr_e( 'Error Message (optional)', 'bulkmail' ); ?>">
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
		<h4><label><?php esc_html_e( 'Button Label', 'bulkmail' ); ?>: <input type="text" name="bulkmail_data[submit]" class="widefat regular-text" value="<?php echo esc_attr( $form->submit ); ?>" placeholder="<?php echo bulkmail_text( 'submitbutton' ); ?>" ></label></h4>
	</fieldset>

	<fieldset id="form-fields">

	<legend><?php esc_html_e( 'Available Fields', 'bulkmail' ); ?></legend>

		<ul class="form-order sortable">
		<?php

		$used = wp_list_pluck( $form->fields, 'field_id' );

		$fields = array_intersect_key( $defaultfields, array_flip( array_keys( array_diff_key( $defaultfields, array_flip( $used ) ) ) ) );

		foreach ( $fields as $field_id => $name ) :
			?>

			<li class="field-<?php echo $field_id; ?> form-field">
				<label><?php esc_html_e( strip_tags( $name ) ); ?></label>
				<div>
				<span class="label"><?php esc_html_e( 'Label', 'bulkmail' ); ?>:</span>
				<input class="label widefat" type="text" data-name="bulkmail_structure[fields][<?php echo $field_id; ?>]" value="<?php echo esc_attr( $name ); ?>" title="<?php esc_attr_e( 'define a label for this field', 'bulkmail' ); ?>" placeholder="<?php echo esc_attr( $name ); ?>">
					<span class="alignright required-field"><input type="checkbox" data-name="bulkmail_structure[required][<?php echo $field_id; ?>]" class="form-order-check-required" value="1"> <?php esc_html_e( 'required', 'bulkmail' ); ?>
					<a class="field-remove" title="<?php esc_attr_e( 'remove field', 'bulkmail' ); ?>">&#10005;</a>
					</span>
				</div>
				<div>
				<span class="label"><?php esc_html_e( 'Error Message', 'bulkmail' ); ?>:</span>
				<input class="label widefat error-msg" type="text" name="bulkmail_structure[error_msg][<?php echo $field_id; ?>]" data-name="bulkmail_structure[error_msg][<?php echo $field_id; ?>]" value="" title="<?php esc_attr_e( 'define an error message for this field', 'bulkmail' ); ?>" placeholder="<?php esc_attr_e( 'Error Message (optional)', 'bulkmail' ); ?>">
				</div>
			</li>
		<?php endforeach; ?>
		</ul>
		<p class="description"><?php printf( esc_html__( 'Add more custom fields on the %s.', 'bulkmail' ), '<a href="edit.php?post_type=newsletter&page=bulkmail_settings#subscribers">' . esc_html__( 'Settings Page', 'bulkmail' ) . '</a>' ); ?></p>

	</fieldset>
</div>

	<?php if ( ! $is_new ) : ?>

	<p class="section-nav"><span class="alignright"><input type="submit" name="design" value="<?php esc_attr_e( 'Design', 'bulkmail' ); ?> &raquo;" class="button-primary button-small"></span></p>

	<?php endif; ?>

<?php elseif ( 'design' == $currentpage ) : ?>

	<?php $style = $form->style; ?>

<p class="section-nav"><span class="alignleft"><input type="submit" name="structure" value="&laquo; <?php esc_html_e( 'Back to Fields', 'bulkmail' ); ?>" class="button-primary button-small"></span><span class="alignright"><input type="submit" name="settings" value="<?php esc_attr_e( 'Define the Options', 'bulkmail' ); ?> &raquo;" class="button-primary button-small"></span></p>

<div id="form-preview">

	<fieldset id="design">
		<legend><?php esc_html_e( 'Form Design', 'bulkmail' ); ?></legend>

	<p><label><input type="checkbox" id="themestyle" checked> <?php esc_html_e( 'Include your Theme\'s style.css', 'bulkmail' ); ?></label></p>
	<p class="description clear"><?php esc_html_e( 'Your form may look different depending on the place you are using it!', 'bulkmail' ); ?></p>
	<div id="form-design">
		<?php
		$url = $this->url(
			array(
				'id'   => $id,
				'edit' => wp_create_nonce( 'bulkmailiframeform' ),
				's'    => 1,
			)
		);
		?>
		<iframe id="form-design-iframe" width="100%" height="500" allowTransparency="true" frameborder="0" scrolling="no" src="<?php echo esc_url( $url ); ?>" data-no-lazy=""></iframe>
	</div>
	<div id="form-design-options">
	<div class="form-design-options-nav">
		<div class="designnav contextual-help-tabs hide-if-no-js">
		<ul>
			<li><a href="#tab-global" class="nav"><?php esc_html_e( 'Global', 'bulkmail' ); ?></a></li>
			<li><a href="#tab-buttons" class="nav"><?php esc_html_e( 'Button', 'bulkmail' ); ?></a></li>
			<li><a href="#tab-fields" class="nav"><?php esc_html_e( 'Fields', 'bulkmail' ); ?></a></li>
			<li><a href="#tab-messages" class="nav"><?php esc_html_e( 'Info Messages', 'bulkmail' ); ?></a></li>
		</ul>
		</div>
	</div>
	<div class="form-design-options-tabs">

		<div class="designtab" id="tab-global">
			<ul>
			<li><label><?php esc_html_e( 'Label Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-wrapper label', 'color' ); ?>" data-selector=".bulkmail-wrapper label" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Input Text Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-wrapper .input', 'color' ); ?>" data-selector=".bulkmail-wrapper .input" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Input Background Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-wrapper .input', 'background-color' ); ?>" data-selector=".bulkmail-wrapper .input" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Input Focus Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-wrapper .input:focus', 'background-color' ); ?>" data-selector=".bulkmail-wrapper .input:focus" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Required Asterisk', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, 'label span.bulkmail-required', 'color' ); ?>" data-selector="label span.bulkmail-required" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			</ul>
		</div>

		<div class="designtab" id="tab-buttons">
		<ul>
			<li><label><?php esc_html_e( 'Background Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.submit-button', 'background-color' ); ?>" data-selector=".submit-button" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Text Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.submit-button', 'color' ); ?>" data-selector=".submit-button" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Hover Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.submit-button:hover', 'background-color' ); ?>" data-selector=".submit-button:hover" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Hover Text Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.submit-button:hover', 'color' ); ?>" data-selector=".submit-button:hover" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
		</ul>
		</div>

		<div class="designtab" id="tab-fields">
		<ul>
			<?php foreach ( $form->fields as $field_id => $field ) : ?>
				<li><strong><?php echo $field->name; ?></strong><ul>
				<li><label><?php esc_html_e( 'Label', 'bulkmail' ); ?></label>
					<input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-' . $field_id . '-wrapper label', 'color' ); ?>" data-selector=".bulkmail-<?php echo $field_id; ?>-wrapper label" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a>
				</li>
				<li><label><?php esc_html_e( 'Input', 'bulkmail' ); ?></label>
					<input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-' . $field_id . '-wrapper .input', 'color' ); ?>" data-selector=".bulkmail-<?php echo $field_id; ?>-wrapper .input" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a>
				</li>
				<li><label><?php esc_html_e( 'Input Background', 'bulkmail' ); ?></label>
					<input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-' . $field_id . '-wrapper .input', 'background-color' ); ?>" data-selector=".bulkmail-<?php echo $field_id; ?>-wrapper .input" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a>
				</li>
				</ul></li>
			<?php endforeach; ?>

		</ul>
		</div>

		<div class="designtab" id="tab-messages">
		<ul>
			<li><label><?php esc_html_e( 'Success message Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-form-info.success', 'color' ); ?>" data-selector=".bulkmail-form-info.success" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Success message Background', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-form-info.success', 'background-color' ); ?>" data-selector=".bulkmail-form-info.success" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Error message Color', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-form-info.error', 'color' ); ?>" data-selector=".bulkmail-form-info.error" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
			<li><label><?php esc_html_e( 'Error message Background', 'bulkmail' ); ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.bulkmail-form-info.error', 'background-color' ); ?>" data-selector=".bulkmail-form-info.error" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'bulkmail' ); ?></a></li>
		</ul>
		</div>

	</div>
	</div>

	</fieldset>

	<input type="hidden" name="bulkmail_design[style]" value="<?php echo esc_attr( json_encode( $form->style ) ); ?>" id="style">
	<div class="clear"></div>

	<fieldset>
		<legend><?php esc_html_e( 'Custom Style', 'bulkmail' ); ?></legend>
		<p class="description"><?php esc_html_e( 'Add custom CSS to your form', 'bulkmail' ); ?></p>
		<div id="custom-style-wrap" class="wrapper">
			<div class="wrapper-left">
				<textarea id="custom-style" class="code" name="bulkmail_design[custom]"><?php echo esc_textarea( $form->custom_style ); ?></textarea>
			</div>
			<div class="wrapper-right">
			<input type="text" class="widefat" placeholder="<?php esc_attr_e( 'Selector Prefix', 'bulkmail' ); ?>" id="custom-style-prefix">
			<select id="custom-style-samples" multiple>
				<option value=""><?php esc_html_e( 'Form selector', 'bulkmail' ); ?></option>
				<option value=" .bulkmail-wrapper"><?php esc_html_e( 'Field wrapper', 'bulkmail' ); ?></option>
				<optgroup label="<?php esc_attr_e( 'Custom Field Wrapper divs', 'bulkmail' ); ?>">
				<?php foreach ( $defaultfields as $key => $field ) : ?>
				<option value=" .bulkmail-<?php echo esc_attr( $key ); ?>-wrapper"><?php echo $field; ?></option>
				<?php endforeach; ?>
				</optgroup>
				<optgroup label="<?php esc_attr_e( 'Custom Field Inputs', 'bulkmail' ); ?>">
				<?php foreach ( $defaultfields as $key => $field ) : ?>
				<option value=" .bulkmail-<?php echo esc_attr( $key ); ?>-wrapper input.input"><?php echo $field; ?></option>
				<?php endforeach; ?>
				</optgroup>
				<optgroup label="<?php esc_attr_e( 'Other', 'bulkmail' ); ?>">
				<option value=" label .bulkmail-required"><?php esc_html_e( 'Required Asterisk', 'bulkmail' ); ?></option>
				<option value=" .bulkmail-submit-wrapper .submit-button"><?php esc_html_e( 'Submit Button', 'bulkmail' ); ?></option>
				</optgroup>
			</select>
			</div>
		</div>
	</fieldset>

</div>

<p class="section-nav"><span class="alignleft"><input type="submit" name="structure" value="&laquo; <?php esc_html_e( 'Back to Fields', 'bulkmail' ); ?>" class="button-primary button-small"></span><span class="alignright"><input type="submit" name="settings" value="<?php esc_attr_e( 'Define the Options', 'bulkmail' ); ?> &raquo;" class="button-primary button-small"></span></p>

<?php elseif ( 'settings' == $currentpage ) : ?>

	<?php $is_profile = bulkmail_option( 'profile_form', 0 ) == $form->ID; ?>

<p class="section-nav"><span class="alignleft"><input type="submit" name="design" value="&laquo; <?php esc_html_e( 'Back to Design', 'bulkmail' ); ?>" class="button-primary button-small"></span></p>

<div id="form-options">
		<div class="subtab form" id="form-tab-<?php echo esc_attr( $id ); ?>">

		<fieldset>
			<legend><?php esc_html_e( 'Form Options', 'bulkmail' ); ?></legend>
				<p><label><input type="hidden" name="bulkmail_data[asterisk]" value="0"><input type="checkbox" name="bulkmail_data[asterisk]" value="1" <?php checked( $form->asterisk ); ?>> <?php esc_html_e( 'Show asterisk on required fields', 'bulkmail' ); ?></label>
				</p>

				<p><label><input type="hidden" name="bulkmail_data[inline]" value="0"><input type="checkbox" name="bulkmail_data[inline]" value="1" <?php checked( $form->inline ); ?>> <?php esc_html_e( 'Place labels inside input fields', 'bulkmail' ); ?></label>
				</p>

				<p><label><input type="hidden" name="bulkmail_data[prefill]" value="0"><input type="checkbox" name="bulkmail_data[prefill]" value="1" <?php checked( $form->prefill ); ?>> <?php esc_html_e( 'Fill fields with known data if user is logged in', 'bulkmail' ); ?></label>
				</p>

				<p><label><input type="hidden" name="bulkmail_data[redirect]" value=""><input id="redirect-cb" type="checkbox" <?php checked( ! empty( $form->redirect ) ); ?>> <?php esc_html_e( 'Redirect after submit', 'bulkmail' ); ?></label>
				<input type="url" id="redirect-tf" name="bulkmail_data[redirect]" class="widefat regular-text" value="<?php echo esc_attr( $form->redirect ); ?>" placeholder="https://www.example.com" >
				</p>

				<p><label><input type="hidden" name="bulkmail_data[overwrite]" value="0"><input type="checkbox" name="bulkmail_data[overwrite]" value="1" <?php checked( $form->overwrite ); ?>> <?php esc_html_e( 'Allow users to update their data with this form', 'bulkmail' ); ?></label>
				</p>
		</fieldset>

		<fieldset>
			<legend><?php esc_html_e( 'Profile', 'bulkmail' ); ?></legend>
				<p><label>
					<input type="hidden" name="profile_form" value="0"><input type="checkbox" name="profile_form" value="1" <?php checked( $is_profile ); ?> <?php disabled( $is_profile ); ?>> <?php esc_html_e( 'Use this form as user profile.', 'bulkmail' ); ?>
				</label>
				</p>
					<?php
					if ( ! $is_profile ) :
						if ( $profile_form = bulkmail( 'forms' )->get( bulkmail_option( 'profile_form', 0 ), false, false ) ) :
							?>
							<p class="description"><?php printf( esc_html__( 'Currently %s is your profile form', 'bulkmail' ), '<a href="edit.php?post_type=newsletter&page=bulkmail_forms&ID=' . $profile_form->ID . '&tab=settings">' . $profile_form->name . '</a>' ); ?></p>
						<?php endif; ?>
					<?php endif; ?>

		</fieldset>

		<fieldset>
			<legend><?php esc_html_e( 'List Options', 'bulkmail' ); ?></legend>
				<p>
					<label><input type="hidden" name="bulkmail_data[userschoice]" value="0"><input type="checkbox" name="bulkmail_data[userschoice]" class="bulkmail_userschoice" value="1" <?php checked( $form->userschoice ); ?>> <?php esc_html_e( 'Users decide which list they subscribe to', 'bulkmail' ); ?></label>
					<br> &nbsp; <label><input type="hidden" name="bulkmail_data[dropdown]" value="0"><input type="checkbox" name="bulkmail_data[dropdown]" class="bulkmail_dropdown" value="1" <?php checked( $form->dropdown ); ?><?php disabled( ! $form->userschoice ); ?>> <?php esc_html_e( 'Show drop down instead of check boxes', 'bulkmail' ); ?></label>
				</p>
				<fieldset>
				<legend class="bulkmail_userschoice_td"<?php echo $form->userschoice ? ' style="display:none"' : ''; ?>><?php esc_html_e( 'Subscribe new users to', 'bulkmail' ); ?></legend>
				<legend class="bulkmail_userschoice_td"<?php echo ! $form->userschoice ? ' style="display:none"' : ''; ?>><?php esc_html_e( 'Users can subscribe to', 'bulkmail' ); ?></legend>

				<?php bulkmail( 'lists' )->print_it( null, null, 'bulkmail_data[lists]', false, $form->lists ); ?>

				<p><label><input type="hidden" name="bulkmail_data[precheck]" value="0"><input type="checkbox" name="bulkmail_data[precheck]" value="1" <?php checked( $form->precheck ); ?>> <?php esc_html_e( 'checked by default', 'bulkmail' ); ?></label>
				</p>
				</fieldset>
				<p><label><input type="hidden" name="bulkmail_data[addlists]" value="0"><input type="checkbox" name="bulkmail_data[addlists]" value="1" <?php checked( $form->addlists ); ?>> <?php esc_html_e( 'Assign new lists automatically to this form', 'bulkmail' ); ?></label>
				</p>

		</fieldset>

		<fieldset>
			<legend><?php esc_html_e( 'Double Opt In', 'bulkmail' ); ?></legend>

				<p><label><input type="radio" name="bulkmail_data[doubleoptin]" class="double-opt-in" data-id="<?php echo $id; ?>" value="0" <?php checked( ! $form->doubleoptin ); ?>> [Single-Opt-In] <?php esc_html_e( 'new subscribers are subscribed instantly without confirmation.', 'bulkmail' ); ?></label>
				</p>
				<p><label><input type="radio" name="bulkmail_data[doubleoptin]" class="double-opt-in" data-id="<?php echo $id; ?>" value="1" <?php checked( $form->doubleoptin ); ?>> [Double-Opt-In] <?php esc_html_e( 'new subscribers must confirm their subscription.', 'bulkmail' ); ?></label>
				</p>
				<div id="double-opt-in-field" class="double-opt-in-field"<?php echo ! $form->doubleoptin ? ' style="display:none"' : ''; ?>>
					<fieldset>
						<legend><?php esc_html_e( 'Confirmation Settings', 'bulkmail' ); ?></legend>
						<table class="nested">
						<tr>
							<td colspan="2">
							<table class="form-table">
								<tr valign="top">
									<td scope="row" width="200"><label for="bulkmail_text_subject"><?php esc_html_e( 'Subject', 'bulkmail' ); ?>: <code>{subject}</code></label></td>
									<td><input type="text" id="bulkmail_text_subject" name="bulkmail_data[subject]" value="<?php echo esc_attr( $form->subject ); ?>" class="regular-text"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="bulkmail_text_headline"><?php esc_html_e( 'Headline', 'bulkmail' ); ?>: <code>{headline}</code></label></td>
									<td><input type="text" id="bulkmail_text_headline" name="bulkmail_data[headline]" value="<?php echo esc_attr( $form->headline ); ?>" class="regular-text"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="bulkmail_text_link"><?php esc_html_e( 'Linktext', 'bulkmail' ); ?>:</label> <code>{link}</code></td>
									<td><input type="text" id="bulkmail_text_link" name="bulkmail_data[link]" value="<?php echo esc_attr( $form->link ); ?>" class="regular-text"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="bulkmail_text_content"><?php esc_html_e( 'Text', 'bulkmail' ); ?>: <code>{content}</code></label><p class="description"><?php printf( esc_html__( 'The text new subscribers get when Double-Opt-In is selected. Use %s for the link placeholder. Basic HTML is allowed', 'bulkmail' ), '<code>{link}</code>' ); ?></p></td>
									<td><textarea id="bulkmail_text_content" name="bulkmail_data[content]" rows="10" cols="50" class="large-text"><?php echo esc_attr( $form->content ); ?></textarea></td>
								</tr>
								<tr><td><?php esc_html_e( 'Used template file', 'bulkmail' ); ?></td><td>
									<?php bulkmail( 'helper' )->notifcation_template_dropdown( $form->template, 'bulkmail_data[template]' ); ?>
									</td>
								</tr>

								<tr>
									<td><?php esc_html_e( 'Resend Confirmation', 'bulkmail' ); ?></td>
									<td><div><input type="hidden" name="bulkmail_data[resend]" value="0"><input type="checkbox" name="bulkmail_data[resend]" value="1" <?php checked( $form->resend ); ?>> <?php printf( esc_html__( 'Resend confirmation %1$s times with a delay of %2$s hours if user hasn\'t confirmed the subscription', 'bulkmail' ), '<input type="text" name="bulkmail_data[resend_count]" value="' . esc_attr( $form->resend_count ) . '" class="small-text">', '<input type="text" name="bulkmail_data[resend_time]" value="' . esc_attr( $form->resend_time ) . '" class="small-text">' ); ?></div></td>
								</tr>

								<tr><td><?php esc_html_e( 'Redirect after confirm', 'bulkmail' ); ?></td><td><input type="url" name="bulkmail_data[confirmredirect]" class="widefat" value="<?php echo isset( $form->confirmredirect ) ? esc_attr( $form->confirmredirect ) : ''; ?>" placeholder="http://www.example.com" ></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><label><input type="hidden" name="bulkmail_data[vcard]" class="vcard" value="0"><input type="checkbox" name="bulkmail_data[vcard]" class="vcard" value="1" <?php checked( $form->vcard ); ?> data-id="<?php echo $id; ?>"> <?php esc_html_e( 'Attach vCard to all confirmation mails', 'bulkmail' ); ?></label>
									<div id="vcard-field"<?php echo ! $form->vcard ? ' style="display:none"' : ''; ?> class="vcard-field">
									<p class="description"><?php printf( esc_html__( 'Paste in your vCard content. You can use %s to generate your personal vcard', 'bulkmail' ), '<a href="http://vcardmaker.com/" class="external">vcardmaker.com</a>' ); ?></p>
									<?php $vcard = $form->vcard_content ? $form->vcard_content : $this->get_vcard(); ?><textarea name="bulkmail_data[vcard_content]" rows="10" cols="50" class="large-text code"><?php echo esc_textarea( $vcard ); ?></textarea>
									</div>

									</td>
								</tr>

							</table>
							</td>
						</tr>
					</table>
					</fieldset>
				</div>
		</fieldset>

		</div>

</div>

<p class="section-nav"><span class="alignleft"><input type="submit" name="design" value="&laquo; <?php esc_html_e( 'Back to Design', 'bulkmail' ); ?>" class="button-primary button-small"></span></p>

<?php endif; ?>
<?php if ( ! $is_new ) : ?>
<div class="clear" id="useitbox" style="display:none">

	<?php

		$form = bulkmail( 'form' )->id( $id );

		$form_use_it_tabs = array(
			'intro'             => esc_html__( 'Use your form as', 'bulkmail' ) . '&hellip;',
			'code'              => esc_html__( 'Shortcode or PHP', 'bulkmail' ),
			'subscriber-button' => esc_html__( 'Subscriber Button', 'bulkmail' ),
			'form-html'         => esc_html__( 'Form HTML', 'bulkmail' ),
		);

		$form_use_it_tabs = apply_filters( 'bulkmail_form_use_it_tabs', $form_use_it_tabs );

		?>
	<div class="useit-wrap">
		<div class="useit-nav">
			<div class="mainnav contextual-help-tabs hide-if-no-js">
				<ul>
				<?php foreach ( $form_use_it_tabs as $key => $name ) : ?>
					<li><a href="#<?php echo esc_attr( $key ); ?>" class="nav-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $name ); ?></a></li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="useit-tabs">
			<?php foreach ( $form_use_it_tabs as $key => $name ) : ?>

			<div id="tab-<?php echo esc_attr( $key ); ?>" class="useit-tab">
				<h3><?php echo esc_html( $name ); ?></h3>
				<?php do_action( 'bulkmail_use_it_form_tab', $form ); ?>
				<?php do_action( 'bulkmail_use_it_form_tab_' . $key, $form ); ?>
				<?php do_action( 'bulkmail_after_use_it_form_tab_' . $key, $form ); ?>
			</div>

			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<hr>

<p class="alignright">
	<input type="submit" name="save" class="button button-primary" value="<?php esc_attr_e( 'Save', 'bulkmail' ); ?>">
</p>

</form>
</div>
