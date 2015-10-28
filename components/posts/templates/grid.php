<?php
/**
 * Grid template for Mm Posts.
 *
 * @since  1.0.0
 */
add_filter( 'mm_posts_templates', 'mm_posts_grid_template', 0 );
/**
 * Register this template with Mm Posts.
 */
function mm_posts_grid_template( $templates ) {
	$template_name = __( 'Grid', 'mm-components' );
	$templates[ $template_name ] = 'grid';
	return $templates;
}