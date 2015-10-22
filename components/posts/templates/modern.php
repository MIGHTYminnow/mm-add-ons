<?php
/**
 * Modern template for Mm Posts.
 *
 * @since  1.0.0
 */
add_filter( 'mm_posts_templates', 'mm_posts_modern_template', 0 );
/**
 * Register this template with Mm Posts.
 */
function mm_posts_modern_template( $templates ) {
	$template_name = __( 'Modern', 'mm-components' );
	$templates[ $template_name ] = 'modern';
	return $templates;
}