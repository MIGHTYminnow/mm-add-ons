<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Slider
 *
 * @package mm-components
 * @since   1.0.0
 */

/**
 * Build and return the Slider component.
 *
 * @since   1.0.0
 *
 * @param   array  $args  The args.
 *
 * @return  string        The HTML.
 */
function mm_slider( $args ) {

	$component  = 'mm-slider';

	// Set our defaults and use them as needed.
	$defaults = array(
		'image_ids'  => '',
		'loop'       => '',
		'autoplay'   => '',
		'duration'   => '',
		'navigation' => '',
	);
	$args = wp_parse_args( (array)$args, $defaults );

	// Bail if we don't have any image IDs.
	if ( empty( $args['image_ids'] ) ) {
		return;
	}

	// Get clean param values.
	$image_ids = ( is_array( $args['image_ids'] ) ) ? $args['image_ids'] : explode( ',', str_replace( ' ', '', $args['image_ids'] ) );

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );

	ob_start() ?>

	<div class="<?php echo esc_attr( $mm_classes ); ?>">
		<ul>
			<?php
				foreach ( $image_ids as $image_id ) {

					$image = wp_get_attachment_image_src( $image_id, 'full' );

					if( is_wp_error( $image ) && is_array( $image ) ) {
						continue;
					}

					printf(
						'<li class="mm-slider-image">%s</li>',
						// esc_url( $image[0] )
						wp_get_attachment_image( $image_id, 'full' )
					);
				}
			?>
		</ul>
	</div>

	<?php

	return ob_get_clean();
}

add_shortcode( 'mm_slider', 'mm_slider_shortcode' );
/**
 * Slider shortcode.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_slider_shortcode( $atts ) {

	return mm_slider( $atts );
}

add_action( 'vc_before_init', 'mm_vc_slider' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_slider() {

	vc_map( array(
		'name'     => __( 'Slider', 'mm-components' ),
		'base'     => 'mm_slider',
		'icon'     => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params'   => array(
			array(
				'type'        => 'attach_images',
				'heading'     => __( 'Images', 'mm-components' ),
				'param_name'  => 'image_ids',
				'description' => __( 'The bigger the image size, the better.', 'mm-components' ),
				'value'       => '',
			),
		)
	) );
}

add_action( 'register_shortcode_ui', 'mm_components_mm_slider_shortcode_ui' );
/**
 * Register UI for Shortcake.
 *
 * @since  1.0.0
 */
function mm_components_mm_slider_shortcode_ui() {

	if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
		return;
	}

	shortcode_ui_register_for_shortcode(
		'mm_slider',
		array(
			'label'         => esc_html__( 'Mm Slider', 'mm-components' ),
			'listItemImage' => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
			'attrs'         => array(
			),
		)
	);
}
