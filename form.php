<?php

do_action( 'bulkmail_form_header' );

?><!DOCTYPE html>
<!--[if IE 8]><html class="lt-ie10 ie8" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 9]><html class="lt-ie10 ie9" <?php language_attributes(); ?>><![endif]-->
<!--[if gt IE 9]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
<html <?php language_attributes(); ?> class="bulkmail-embeded-form">
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
	<meta name='robots' content='noindex,nofollow'>
	<?php do_action( 'bulkmail_form_head' ); ?>

</head>
<body>
	<div class="bulkmail-form-body">
		<div class="bulkmail-form-wrap">
			<div class="bulkmail-form-inner">
			<?php do_action( 'bulkmail_form_body' ); ?>
			</div>
		</div>
	</div>
<?php do_action( 'bulkmail_form_footer' ); ?>
</body>
</html>
