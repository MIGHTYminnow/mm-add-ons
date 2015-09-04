<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Posts
 *
 * @package mm-components
 * @since   1.0.0
 */

add_shortcode( 'mm_posts', 'mm_posts_shortcode' );
/**
 * Output [mm_posts]
 *
 * @since   1.0.0
 *
 * @param   array   $atts  Shortcode attributes.
 * @return  string         Shortcode output.
 */
function mm_posts_shortcode( $atts = array(), $content = null, $tag ) {

	$atts = mm_shortcode_atts( array(
		'post_id'             => '',
		'post_type'           => '',
		'taxonomy'            => '',
		'term'                => '',
		'limit'               => '',
		'show_featured_image' => '',
		'featured_image_size' => '',
		'show_post_info'      => '',
		'show_post_meta'      => '',
		'use_post_content'    => '',
	), $atts );

	// Set up our defaults.
	$post_id   = ( 0 !== (int)$atts['post_id'] ) ? (int)$atts['post_id'] : '';
	$post_type = ( '' !== $atts['post_type'] ) ? $atts['post_type'] : 'post';
	$taxonomy  = ( '' !== $atts['taxonomy'] ) ? $atts['taxonomy'] : '';
	$term      = ( '' !== $atts['term'] ) ? $atts['term'] : '';
	$limit     = ( '' !== $atts['limit'] ) ? (int)$atts['limit'] : 10;

	// Get Mm classes.
	$mm_classes = str_replace( '_', '-', $tag );
	$mm_classes = apply_filters( 'mm_shortcode_custom_classes', $mm_classes, $tag, $atts );

	// Set up the context we're in.
	global $post;
	$current_post_id = (int)$post->ID;

	// Set up a generic query.
	$query_args = array(
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
	);

	// Exclude the page we're on from the query to prevent an infinite loop.
	$query_args['post__not_in'] = array(
		$current_post_id
	);

	// Add to our query if additional params have been passed.
	if ( '' !== $post_id ) {

		$query_args['p'] = $post_id;

	} elseif ( '' !== $taxonomy && '' !== $term ) {

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
	$query_args = apply_filters( 'mm_posts_query_args', $query_args, $atts );

	// Do the query.
	$query = new WP_Query( $query_args );

	// Store the global post object as the context we'll pass to our hooks.
	$context = $post;

	ob_start(); ?>

	<?php do_action( 'mm_posts_register_hooks', $context, $atts ); ?>

	<div class="<?php echo esc_attr( $mm_classes ); ?>">

		<?php do_action( 'mm_posts_before', $context, $atts ); ?>

		<?php while ( $query->have_posts() ) : $query->the_post(); ?>

			<?php setup_postdata( $query->post ); ?>

			<article id="post-<?php the_ID( $query->post->ID ); ?>" <?php post_class( 'mm-post' ); ?> itemscope itemtype="http://schema.org/BlogPosting" itemprop="blogPost">

				<?php do_action( 'mm_posts_header', $query->post, $context, $atts ); ?>

				<?php do_action( 'mm_posts_content', $query->post, $context, $atts ); ?>

				<?php do_action( 'mm_posts_footer', $query->post, $context, $atts ); ?>

			</article>

		<?php endwhile; ?>

		<?php do_action( 'mm_posts_after', $context, $atts ); ?>

	</div>

	<?php wp_reset_postdata(); ?>

	<?php do_action( 'mm_posts_reset_hooks' ); ?>

	<?php $output = ob_get_clean();

	return $output;
}

add_action( 'mm_posts_register_hooks', 'mm_posts_register_default_hooks', 9, 2 );
/**
 * Set up our default hooks.
 *
 * @since  1.0.0
 *
 * @param  object  $context  The global post object for the current page.
 * @param  array   $atts     The params passed to the shortcode.
 */
function mm_posts_register_default_hooks( $context, $atts ) {

	add_action( 'mm_posts_header', 'mm_posts_output_post_header', 10, 3 );

	if ( 1 === (int)$atts['show_featured_image'] ) {
		add_action( 'mm_posts_content', 'mm_posts_output_post_image', 8, 3 );
	}

	add_action( 'mm_posts_content', 'mm_posts_output_post_content', 10, 3 );

	if ( 1 === (int)$atts['show_post_meta'] ) {
		add_action( 'mm_posts_footer', 'mm_posts_output_post_meta', 10, 3 );
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
}

/**
 * Default post header output.
 *
 * @since  1.0.0
 *
 * @param  object  $post     The current post object.
 * @param  object  $context  The global post object.
 * @param  array   $atts     The array of shortcode atts.
 */
function mm_posts_output_post_header( $post, $context, $atts ) {

	$custom_output = apply_filters( 'mm_posts_post_header', '', $post, $context, $atts );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	echo '<header class="entry-header">';

	mm_posts_output_post_title( $post, $context, $atts );

	if ( 1 === (int)$atts['show_post_info'] ) {
		mm_posts_output_post_info( $post, $context, $atts );
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
 * @param  array   $atts     The array of shortcode atts.
 */
function mm_posts_output_post_title( $post, $context, $atts ) {

	$custom_output = apply_filters( 'mm_posts_post_title', '', $post, $context, $atts );

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
 * @param  array   $atts     The array of shortcode atts.
 */
function mm_posts_output_post_info( $post, $context, $atts ) {

	$custom_output = apply_filters( 'mm_posts_post_info', '', $post, $context, $atts );

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
			$time   = get_the_modified_date( $format );

			printf(
				'<time class="%s" itemprop="datePublished">%s</time>',
				'entry-time',
				$time
			);

			echo ' by ';

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
 * @param  array   $atts     The array of shortcode atts.
 */
function mm_posts_output_post_image( $post, $context, $atts ) {

	$custom_output = apply_filters( 'mm_posts_post_image', '', $post, $context, $atts );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	// Default to using the 'post-thumbnail' size.
	if ( '' !== $atts['featured_image_size'] ) {
		$image_size = esc_attr( $atts['featured_image_size'] );
	} else {
		$image_size = 'post-thumbnail';
	}

	if ( has_post_thumbnail( $post->ID ) ) {

		printf(
			'<div class="entry-image"><a href="%s">%s</a></div>',
			get_the_permalink( $post->ID ),
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
 * @param  array   $atts     The array of shortcode atts.
 */
function mm_posts_output_post_content( $post, $context, $atts ) {

	$custom_output = apply_filters( 'mm_posts_post_content', '', $post, $context, $atts );

	if ( '' !== $custom_output ) {
		echo $custom_output;
		return;
	}

	echo '<div class="entry-content" itemprop="text">';

	if ( 1 === (int)$atts['use_post_content'] ) {

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
 * @param  array   $atts     The array of shortcode atts.
 */
function mm_posts_output_post_meta( $post, $context, $atts ) {

	$custom_output = apply_filters( 'mm_posts_post_meta', '', $post, $context, $atts );

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
 * Output a specific postmeta value in a standard format.
 *
 * @since  1.0.0
 *
 * @param  int     $post_id  The post ID.
 * @param  string  $key      The postmeta key.
 */
function mm_posts_output_postmeta_value( $post_id, $key ) {

	$value = get_post_meta( $post_id, $key, true );

	if ( $value ) {
		printf(
			'<div class="%s">%s</div>',
			'entry-' . esc_attr( $key ),
			esc_html( $value )
		);
	}
}

add_action( 'init', 'mm_vc_posts', 12 );
/**
 * Visual Composer component.
 *
 * We're firing a bit later than usual because we want to come after all
 * custom post types and taxonomies have been registered.
 *
 * @since  1.0.0
 */
function mm_vc_posts() {

	// Only proceed if Visual Composer is active.

	if ( function_exists( 'vc_map' ) ) :

	$post_types = mm_get_post_types_for_vc();
	$taxonomies = mm_get_taxonomies_for_vc();
	$image_sizes = mm_get_image_sizes_for_vc();

	vc_map( array(
		'name' => __( 'Posts', 'mm-components' ),
		'base' => 'mm_posts',
		'class' => '',
		'icon' => MM_COMPONENTS_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params' => array(
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
				'heading'     => __( 'Number of Posts', 'mm-components' ),
				'param_name'  => 'limit',
				'description' => __( 'Specify the number of posts to show', 'mm-components' ),
				'value'       => '10',
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Show the Featured Image', 'mm-components' ),
				'param_name'  => 'show_featured_image',
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Featured Image Size', 'mm-components' ),
				'param_name' => 'featured_image_size',
				'dependency' => array(
					'element'   => 'show_featured_image',
					'not_empty' => true,
				),
				'value' => $image_sizes,
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Show post info', 'mm-components' ),
				'param_name'  => 'show_post_info',
				'description' => __( 'Default post info output includes post date and author.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Show post meta', 'mm-components' ),
				'param_name'  => 'show_post_meta',
				'description' => __( 'Default post meta output includes category and tag links.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Use full post content.', 'mm-components' ),
				'param_name'  => 'use_post_content',
				'description' => __( 'By default the excerpt will be used. Check this to output the full post content.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
		)
	) );

	endif;

}
