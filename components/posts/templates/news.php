<?php
/**
 * News template for Mm Posts.
 *
 * @since  1.0.0
 */
add_filter( 'mm_posts_templates', 'mm_posts_news_template', 0 );
/**
 * Register this template with Mm Posts.
 */
function mm_posts_news_template( $templates ) {
	$template_name = __( 'News', 'mm-components' );
	$templates[ $template_name ] = 'news';
	return $templates;
}