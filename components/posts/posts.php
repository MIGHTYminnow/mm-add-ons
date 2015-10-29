<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Posts
 *
 * @package mm-components
 * @since   1.0.0
 */

/**
 * Build and return the Posts component.
 *
 * @since   1.0.0
 *
 * @param   array  $args  The args.
 *
 * @return  string        The HTML.
 */
function mm_posts( $args ) {

	$component  = 'mm-posts';

	// Set our defaults and use them as needed.
	$defaults = array(
		'post_id'             => '',
		'post_type'           => 'post',
		'taxonomy'            => '',
		'term'                => '',
		'per_page'            => 10,
		'pagination'          => '',
		'template'            => '',
		'show_featured_image' => '',
		'featured_image_size' => '',
		'show_post_info'      => '',
		'show_post_meta'      => '',
		'use_post_content'    => '',
		'masonry'             => '',
	);
	$args = wp_parse_args( (array)$args, $defaults );

	// Get clean param values.
	$post_id    = (int)$args['post_id'];
	$post_type  = sanitize_text_field( $args['post_type'] );
	$taxonomy   = sanitize_text_field( $args['taxonomy'] );
	$term       = sanitize_text_field( $args['term'] );
	$per_page   = (int)$args['per_page'];
	$pagination = sanitize_text_field( $args['pagination'] );
	$template   = sanitize_text_field( $args['template'] );
	$masonry    = mm_true_or_false( $args['masonry'] );

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );

	// Maybe add template class.
	if ( $template ) {
		$mm_classes = "$mm_classes $template";
	}

	// Maybe set up masonry.
	if ( $masonry ) {
		wp_enqueue_script( 'mm-isotope' );
		$mm_classes .= ' mm-masonry';
	}

	// Set up the context we're in.
	global $post;
	$global_post_id = (int)$post->ID;

	// Set up a generic query.
	$query_args = array(
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
	);

	// Exclude the page we're on from the query to prevent an infinite loop.
	$query_args['post__not_in'] = array(
		$global_post_id
	);

	// Add to our query if additional params have been passed.
	if ( $post_id ) {

		$query_args['p'] = $post_id;

	} elseif ( $taxonomy && $term ) {

		// First try the term by ID, then try by slug.
		if ( is_int( $term ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $term
				),
			);
		} else {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $term
				),
			);
		}
	}

	// Allow the query to be filtered.
	$query_args = apply_filters( 'mm_posts_query_args', $query_args, $args );

	// Do the query.
	$query = new WP_Query( $query_args );

	// Store the global post object as the context we'll pass to our hooks.
	$context = $post;

	do_action( 'mm_posts_register_hooks', $context, $args );

	ob_start(); ?>

	<div class="<?php echo esc_attr( $mm_classes ); ?>">

		<?php do_action( 'mm_posts_before', $query, $context, $args ); ?>

		<?php while ( $query->have_posts() ) : $query->the_post(); ?>

			<?php setup_postdata( $query->post ); ?>

			<article id="post-<?php the_ID( $query->post->ID ); ?>" <?php post_class( 'mm-post' ); ?> itemscope itemtype="http://schema.org/BlogPosting" itemprop="blogPost">

				<?php do_action( 'mm_posts_header', $query->post, $context, $args ); ?>

				<?php do_action( 'mm_posts_content', $query->post, $context, $args ); ?>

				<?php do_action( 'mm_posts_footer', $query->post, $context, $args ); ?>

			</article>

		<?php endwhile; ?>

		<?php do_action( 'mm_posts_after', $query, $context, $args ); ?>

	</div>

	<?php

	wp_reset_postdata();

	do_action( 'mm_posts_reset_hooks' );

	return ob_get_clean();
}

add_shortcode( 'mm_posts', 'mm_posts_shortcode' );
/**
 * Posts shortcode.
 *
 * @since   1.0.0
 *
 * @param   array   $atts  Shortcode attributes.
 *
 * @return  string         Shortcode output.
 */
function mm_posts_shortcode( $atts = array() ) {

	return mm_posts( $atts );
}

add_action( 'mm_posts_register_hooks', 'mm_posts_register_default_hooks', 9, 2 );
/**
 * Set up our default hooks.
 *
 * @since  1.0.0
 *
 * @param  object  $context  The global post object.
 * @param  array   $args     The instance args.
 */
function mm_posts_register_default_hooks( $context, $args ) {

	if ( mm_true_or_false( $args['masonry'] ) ) {
		add_action( 'mm_posts_before', 'mm_posts_output_masonry_sizers', 12, 3 );
	}

	add_action( 'mm_posts_header', 'mm_posts_output_post_header', 10, 3 );

	if ( mm_true_or_false( $args['show_featured_image'] ) ) {
		add_action( 'mm_posts_content', 'mm_posts_output_post_image', 8, 3 );
	}

	add_action( 'mm_posts_content', 'mm_posts_output_post_content', 10, 3 );

	if ( mm_true_or_false( $args['show_post_meta'] ) ) {
		add_action( 'mm_posts_footer', 'mm_posts_output_post_meta', 10, 3 );
	}

	if ( ! empty( $args['pagination'] ) ) {
		add_action( 'mm_posts_after', 'mm_posts_output_pagination', 12, 3 );
	}
}

add_action( 'mm_posts_reset_hooks', 'mm_posts_reset_default_hooks' );
/**
 * Reset all the hooks.
 *
 * @since  1.0.0
 */
function mm_posts_reset_default_hooks() {

	remove_all_actions( 'mm_posts_before' );
	remove_all_actions( 'mm_posts_header' );
	remove_all_actions( 'mm_posts_content' );
	remove_all_actions( 'mm_posts_footer' );
	remove_all_actions( 'mm_posts_after' );

	remove_all_filters( 'mm_posts_post_header' );
	remove_all_filters( 'mm_posts_post_title' );
	remove_all_filters( 'mm_posts_post_info' );
	remove_all_filters( 'mm_posts_post_image' );
	remove_all_filters( 'mm_posts_post_content' );
	remove_all_filters( 'mm_posts_post_meta' );
}

/**
 * Output masonry sizers.
 *
 * @since  1.0.0
 */
function mm_posts_output_masonry_sizers() {

	echo '<div class="mm-posts-masonry-gutter"></div>';
}

/**
 * Default post header output.
 *
 * @since  1.0.0
 *
 * @param  object  $post     The current post object.
 * @param  object  $context  The global post object.
 * @param  array   $args     The instance args.
 */
function mm_posts_output_post_header( $post, $context, $args ) {

	$custom_output = apply_filters( 'mm_posts_post_header', '', $post, $context, $args );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	echo '<header class="entry-header">';

	mm_posts_output_post_title( $post, $context, $args );

	if ( 1 === (int)$args['show_post_info'] ) {
		mm_posts_output_post_info( $post, $context, $args );
	}

	echo '</header>';
}

/**
 * Default post title output.
 *
 * @since  1.0.0
 *
 * @param  object  $post     The current post object.
 * @param  object  $context  The global post object.
 * @param  array   $args     The instance args.
 */
function mm_posts_output_post_title( $post, $context, $args ) {

	$custom_output = apply_filters( 'mm_posts_post_title', '', $post, $context, $args );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	printf(
		'<h1 class="entry-title" itemprop="headline"><a href="%s" title="%s" rel="bookmark">%s</a></h1>',
		get_permalink( $post->ID ),
		get_the_title( $post->ID ),
		get_the_title( $post->ID )
	);
}

/**
 * Default post info output.
 *
 * @since  1.0.0
 *
 * @param  object  $post     The current post object.
 * @param  object  $context  The global post object.
 * @param  array   $args     The instance args.
 */
function mm_posts_output_post_info( $post, $context, $args ) {

	$custom_output = apply_filters( 'mm_posts_post_info', '', $post, $context, $args );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	echo '<span class="entry-info-wrap">';

		// If the site is running Genesis, use the Genesis post info.
		if ( function_exists( 'genesis_post_info' ) ) {

			genesis_post_info();

		} else {

			echo '<span class="entry-info">';

			$format = get_option( 'date_format' );
			$time   = get_the_date( $format );

			printf(
				'<time class="%s" itemprop="datePublished">%s</time>',
				'entry-time',
				$time
			);

			printf(
				' %s ',
				__( 'by', 'mm-components' )
			);

			printf(
				'<a class="%s" href="%s">%s</a>',
				'entry-author',
				get_author_posts_url( get_the_author_meta( 'ID' ) ),
				get_the_author()
			);

			echo '</span>';
		}

	echo '</span>';
}

/**
 * Default featured image output.
 *
 * @since  1.0.0
 *
 * @param  object  $post     The current post object.
 * @param  object  $context  The global post object.
 * @param  array   $args     The instance args.
 */
function mm_posts_output_post_image( $post, $context, $args ) {

	$custom_output = apply_filters( 'mm_posts_post_image', '', $post, $context, $args );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	// Default to using the 'post-thumbnail' size.
	if ( '' !== $args['featured_image_size'] ) {
		$image_size = esc_attr( $args['featured_image_size'] );
	} else {
		$image_size = 'post-thumbnail';
	}

	if ( has_post_thumbnail( $post->ID ) ) {

		printf(
			'<div class="entry-image"><a href="%s">%s</a></div>',
			get_permalink( $post->ID ),
			get_the_post_thumbnail( $post->ID, $image_size )
		);
	}
}

/**
 * Default post content output.
 *
 * @since  1.0.0
 *
 * @param  object  $post     The current post object.
 * @param  object  $context  The global post object.
 * @param  array   $args     The instance args.
 */
function mm_posts_output_post_content( $post, $context, $args ) {

	$custom_output = apply_filters( 'mm_posts_post_content', '', $post, $context, $args );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	echo '<div class="entry-content" itemprop="text">';

	if ( 1 === (int)$args['use_post_content'] ) {

		the_content();

	} else {

		the_excerpt();
	}

	echo '</div>';
}

/**
 * Default post meta output.
 *
 * @since  1.0.0
 *
 * @param  object  $post     The current post object.
 * @param  object  $context  The global post object.
 * @param  array   $args     The instance args.
 */
function mm_posts_output_post_meta( $post, $context, $args ) {

	$custom_output = apply_filters( 'mm_posts_post_meta', '', $post, $context, $args );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	echo '<div class="entry-meta-wrap">';

	// If the site is running Genesis, use the Genesis post meta.
	if ( function_exists( 'genesis_post_meta' ) ) {

		genesis_post_meta();

	} else {

		$cats = get_the_category_list();
		$tags = get_the_tag_list( '<ul class="post-tags"><li>', '</li><li>', '</li></ul>' );

		echo '<div class="entry-meta">';

		if ( $cats ) {
			echo $cats;
		}

		if ( $tags ) {
			echo $tags;
		}

		echo '</div>';
	}

	echo '</div>';
}

/**
 * Output the pagination links.
 *
 * @since  1.0.0
 *
 * @param  object  $query    The query object.
 * @param  object  $context  The global post object.
 * @param  array   $args     The instance args.
 */
function mm_posts_output_pagination( $query, $context, $args ) {

	$custom_output = apply_filters( 'mm_posts_output_pagination', '', $query, $context, $args );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	// Get the page query arg from the URL.
	$page = ( get_query_var( 'page' ) ) ? (int)get_query_var( 'page' ) : 1;

	// Bail if we don't have any additional pages to show.
	if ( 1 >= $query->max_num_pages ) {
		return;
	}

	// Default to next/prev links.
	if ( ! $args['pagination'] ) {
		$args['pagination'] = 'next-prev';
	}

	echo '<div class="pagination-wrap pagination-' . esc_attr( $args['pagination'] ) . '">';

	switch ( $args['pagination'] ) {

		case 'next-prev':

			if ( 1 < $page ) {
				printf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					'?page=' . ( $page - 1 ),
					'pagination-link prev button',
					__( 'Previous', 'mm-components' ),
					__( 'Previous', 'mm-components' )
				);
			}

			if ( $query->max_num_pages > $page ) {
				printf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					'?page=' . ( $page + 1 ),
					'pagination-link next button',
					__( 'Next', 'mm-components' ),
					__( 'Next', 'mm-components' )
				);
			}

			break;

		case 'page-numbers':

			if ( 5 >= $query->max_num_pages ) {

				// We have 5 or less total pages.
				for ( $i = 1; $i <= $query->max_num_pages; $i++ ) {
					if ( $i == $page ) {
						$link_classes = 'pagination-link page-' . $i . ' button selected';
					} else {
						$link_classes = 'pagination-link page-' . $i . ' button';
					}
					printf(
						'<a href="%s" class="%s" title="%s">%s</a>',
						'?page=' . $i,
						$link_classes,
						$i,
						$i
					);
				}

			} elseif ( 3 <= $page && ( $query->max_num_pages - 2 ) >= $page ) {

				// We have 6 or more total pages and we're showing a page between 3 and (total - 2).
				printf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					'?page=1',
					'pagination-link page-1 button',
					'1',
					'1'
				);

				if ( 3 != $page ) {
					echo '<span>&hellip;</span>';
				}

				printf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					'?page=' . ( $page - 1 ),
					'pagination-link page-' . ( $page - 1 ) . ' button',
					$page - 1,
					$page - 1
				);
				printf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					'?page=' . $page,
					'pagination-link page-' . $page . ' button selected',
					$page,
					$page
				);
				printf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					'?page=' . ( $page + 1 ),
					'pagination-link page-' . ( $page + 1 ) . ' button',
					$page + 1,
					$page + 1
				);

				if ( $page != ( $query->max_num_pages - 2 ) ) {
					echo '<span>&hellip;</span>';
				}

				printf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					'?page=' . $query->max_num_pages,
					'pagination-link page-' . $query->max_num_pages . ' button',
					$query->max_num_pages,
					$query->max_num_pages
				);

			} elseif ( 3 > $page ) {

				// We have more than 6 pages and we're showing page 1 or 2.
				for ( $i = 1; $i <= 3; $i++ ) {
					if ( $i == $page ) {
						$link_classes = 'pagination-link page-' . $i . ' button selected';
					} else {
						$link_classes = 'pagination-link page-' . $i . ' button';
					}
					printf(
						'<a href="%s" class="%s" title="%s">%s</a>',
						'?page=' . $i,
						$link_classes,
						$i,
						$i
					);
				}

				echo '<span>&hellip;</span>';

				printf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					'?page=' . $query->max_num_pages,
					'pagination-link page-' . $query->max_num_pages . ' button',
					$query->max_num_pages,
					$query->max_num_pages
				);

			} else {

				// We have more than 6 pages and we're showing the last or second to last page.
				printf(
					'<a href="%s" class="%s" title="%s">%s</a>',
					'?page=1',
					'pagination-link page-1 button',
					'1',
					'1'
				);

				echo '<span>&hellip;</span>';

				for ( $i = ( $query->max_num_pages - 2 ); $i <= $query->max_num_pages; $i++ ) {
					if ( $i == $page ) {
						$link_classes = 'pagination-link page-' . $i . ' button selected';
					} else {
						$link_classes = 'pagination-link page-' . $i . ' button';
					}
					printf(
						'<a href="%s" class="%s" title="%s">%s</a>',
						'?page=' . $i,
						$link_classes,
						$i,
						$i
					);
				}
			}
			break;
	}

	echo '</div>';
}

/**
 * Output multiple postmeta values in a standard format.
 *
 * @since  1.0.0
 *
 * @param  int     $post_id   The post ID.
 * @param  array   $keys      The postmeta keys.
 * @param  string  $outer_el  The outer wrapper element.
 * @param  string  $inner_el  The inner wrapper element.
 */
function mm_posts_output_postmeta_values( $post_id, $keys, $outer_el = 'ul', $inner_el = 'li' ) {

	if ( ! is_array( $keys ) || empty( $keys ) ) {
		return;
	}

	printf(
		'<%s class="%s">',
		$outer_el,
		'entry-meta-wrap'
	);

	foreach ( $keys as $key ) {
		mm_posts_output_postmeta_value( $post_id, $key, $inner_el );
	}

	printf(
		'</%s>',
		$outer_el
	);
}

/**
 * Output a specific postmeta value in a standard format.
 *
 * @since  1.0.0
 *
 * @param  int     $post_id  The post ID.
 * @param  string  $key      The postmeta key.
 * @param  string  $element  The wrapper element.
 */
function mm_posts_output_postmeta_value( $post_id, $key, $element = 'div' ) {

	$value = get_post_meta( $post_id, $key, true );

	if ( $value ) {
		printf(
			'<%s class="%s">%s</%s>',
			$element,
			'entry-' . esc_attr( $key ),
			esc_html( $value ),
			$element
		);
	}
}

add_filter( 'mm_posts_query_args', 'mm_posts_filter_from_query_args', 10, 2 );
/**
 * Use specific query args present in the URL to alter the mm_posts query.
 *
 * @since   1.0.0
 *
 * @param   array  $query_args  The original query args.
 * @param   array  $args        The instance args.
 *
 * @return  array  $query_args  The updated query args.
 */
function mm_posts_filter_from_query_args( $query_args, $args ) {

	if ( isset( $_GET['per_page'] ) ) {
		$query_args['posts_per_page'] = (int)$_GET['per_page'];
	}

	if ( ! empty( $args['pagination'] ) && get_query_var( 'page' ) ) {
		$query_args['paged'] = (int)get_query_var( 'page' );
	}

	if ( isset( $_GET['author'] ) ) {
		$query_args['author'] = (int)$_GET['author'];
	}

	if ( isset( $_GET['cat'] ) ) {
		$query_args['cat'] = (int)$_GET['cat'];
	}

	if ( isset( $_GET['tag'] ) ) {
		$query_args['tag'] = sanitize_title_for_query( $_GET['tag'] );
	}

	if ( isset( $_GET['tag_id'] ) ) {
		$query_args['tag_id'] = (int)$_GET['tag_id'];
	}

	return $query_args;
}

add_action( 'init', 'mm_vc_posts', 12 );
/**
 * Visual Composer component.
 *
 * We're firing a bit late because we want to come after all
 * custom post types and taxonomies have been registered.
 *
 * @since  1.0.0
 */
function mm_vc_posts() {

	// Only proceed if we're in the admin and Visual Composer is active.
	if ( ! is_admin() ) {
		return;
	}

	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	$post_types  = mm_get_post_types_for_vc();
	$taxonomies  = mm_get_taxonomies_for_vc();
	$image_sizes = mm_get_image_sizes_for_vc();
	$templates   = mm_get_mm_posts_templates_for_vc();

	vc_map( array(
		'name'     => __( 'Posts', 'mm-components' ),
		'base'     => 'mm_posts',
		'class'    => '',
		'icon'     => MM_COMPONENTS_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params'   => array(
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Post ID', 'mm-components' ),
				'param_name'  => 'post_id',
				'description' => __( 'Enter a post ID to display a single post', 'mm-components' ),
				'value'       => '',
			),
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Post Type', 'mm-components' ),
				'param_name'  => 'post_type',
				'description' => __( 'Select a post type to display multiple posts', 'mm-components' ),
				'value'       => $post_types,
			),
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Taxonomy', 'mm-components' ),
				'param_name'  => 'taxonomy',
				'description' => __( 'Select a taxonomy and term to only include posts that have the term', 'mm-components' ),
				'value'       => $taxonomies,
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Term', 'mm-components' ),
				'param_name'  => 'term',
				'description' => __( 'Specify a term in the selected taxonomy to only include posts that have the term', 'mm-components' ),
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Posts Per Page', 'mm-components' ),
				'param_name'  => 'per_page',
				'description' => __( 'Specify the maximum number of posts to show at once', 'mm-components' ),
				'value'       => '10',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Pagination', 'mm-components' ),
				'param_name' => 'pagination',
				'value'      => array(
					__( 'None', 'mm-components' )         => '',
					__( 'Next/Prev', 'mm-components' )    => 'next-prev',
					__( 'Page Numbers', 'mm-components' ) => 'page-numbers',
				),
			),
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Template', 'mm-components' ),
				'param_name'  => 'template',
				'description' => __( 'Select a custom template for custom output', 'mm-components' ),
				'value'       => $templates,
			),
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Use Masonry?', 'mm-components' ),
				'param_name' => 'masonry',
				'value'      => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show the Featured Image?', 'mm-components' ),
				'param_name' => 'show_featured_image',
				'value'      => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Featured Image Size', 'mm-components' ),
				'param_name' => 'featured_image_size',
				'value'      => $image_sizes,
				'dependency' => array(
					'element'   => 'show_featured_image',
					'not_empty' => true,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Show post info?', 'mm-components' ),
				'param_name'  => 'show_post_info',
				'description' => __( 'Default post info output includes post date and author.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Show post meta?', 'mm-components' ),
				'param_name'  => 'show_post_meta',
				'description' => __( 'Default post meta output includes category and tag links.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Use full post content?', 'mm-components' ),
				'param_name'  => 'use_post_content',
				'description' => __( 'By default the excerpt will be used. Check this to output the full post content.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
		)
	) );
}
