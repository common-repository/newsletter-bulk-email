<?php

$block_open  = function_exists( 'has_blocks' ) ? '<!-- wp:shortcode -->' : '';
$block_close = function_exists( 'has_blocks' ) ? '<!-- /wp:shortcode -->' : '';

$bulkmail_homepage = array(
	'post_title'   => esc_html__( 'Newsletter', 'bulkmail' ),
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_name'    => esc_html_x( 'newsletter-signup', 'Newsletter Homepage page slug', 'bulkmail' ),
	'post_content' => $block_open . '[newsletter_signup]' . esc_html__( 'Signup for the newsletter', 'bulkmail' ) . '[newsletter_signup_form id=1][/newsletter_signup]' . $block_close . $block_open . '[newsletter_confirm]' . esc_html__( 'Thanks for your interest!', 'bulkmail' ) . '[/newsletter_confirm]' . $block_close . $block_open . '[newsletter_unsubscribe]' . esc_html__( 'Do you really want to unsubscribe?', 'bulkmail' ) . '[/newsletter_unsubscribe]' . $block_close,
);
