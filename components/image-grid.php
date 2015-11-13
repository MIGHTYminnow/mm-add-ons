<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Image Grid
 *
 * @package mm-components
 * @since   1.0.0
 */

add_shortcode( 'mm_image_grid', 'mm_image_grid_shortcode' );
/**
 * Output Image Grid.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_image_grid_shortcode( $atts, $content = null, $tag ) {

	$atts = mm_shortcode_atts( array(
		'title'      => '',
		'style'      => '',
		'max_in_row' => '',
		'el_class'   => '',
	), $atts );

	$title = wp_kses_post( $atts['title'] );
	$style = ( '' !== $atts['style'] ) ? esc_attr( $atts['style'] ) : 'style-full-image';
	$max_in_row = ( '' !== $atts['max_in_row'] ) ? (int) $atts['max_in_row'] : 3;
	$class = ( '' !== $atts['el_class'] ) ? (string)$atts['el_class'] : '';

	// Set a global style variable to pass to the nested Image Grid Image components.
	global $mm_image_grid_style;

	$mm_image_grid_style = $style;

	// Fix wpautop issues in $content.
	if ( function_exists( 'wpb_js_remove_wpautop' ) ) {
		$content = wpb_js_remove_wpautop( $content, true );
	}

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $tag, $atts );
	$mm_classes .= ' ' . $style;
	$mm_classes .= ( 0 !== $max_in_row ) ? ' max-in-row-' . $max_in_row : '';
	$mm_classes .= ( '' !== $class ) ? ' ' . $class : '';

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">

		<?php if ( $title ) : ?>
			<h4><?php echo esc_html( $title ); ?></h4>
		<?php endif; ?>

		<?php if ( $content ) : ?>
			<?php echo wp_kses_post( do_shortcode( $content ) ); ?>
		<?php endif; ?>

	</div>

	<?php

	$output = ob_get_clean();

	// Reset global style variable in case of multiple Image Grids on a single page.
	$mm_image_grid_style = '';

	return $output;
}

add_shortcode( 'mm_image_grid_image', 'mm_image_grid_image_shortcode' );
/**
 * [mm_image_grid_image title="" image="" text="" link="" author_img=""]
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_image_grid_image_shortcode( $atts, $content = null, $tag ) {

	// Global style variable passed from parent Image Grid component.
	global $mm_image_grid_style;

	$atts = mm_shortcode_atts( array(
		'title'       => '',
		'subtitle'    => '',
		'image'       => '',
		'link'        => '',
		'link_title'  => '',
		'link_target' => '',
	), $atts );

	$title    = $atts['title'];
	$subtitle = $atts['subtitle'];
	$image    = (int)$atts['image'];
	$link     = $atts['link'];

	// Fix wpautop issues in $content.
	if ( function_exists( 'wpb_js_remove_wpautop' ) ) {
		$content = wpb_js_remove_wpautop( $content, true );
	}

	// Handle a raw link or a VC link array.
	if ( ! empty( $atts['link'] ) ) {

		if ( 'url' === substr( $atts['link'], 0, 3 ) ) {

			if ( function_exists( 'vc_build_link' ) ) {

				$link_array  = vc_build_link( $atts['link'] );
				$link_url    = $link_array['url'];
				$link_title  = $link_array['title'];
				$link_target = $link_array['target'];

			} else {

				$link_url    = '';
				$link_title  = '';
				$link_target = '';
			}

		} else {

			$link_url    = $atts['link'];
			$link_title  = $atts['link_title'];
			$link_target = $atts['link_target'];
		}

	} else {

		$link_url    = '';
		$link_title  = '';
		$link_target = '';
	}

	// Get image size based on style of parent Image Grid component.
	$image_size = ( 'style-thumbnail-text-card' == $mm_image_grid_style ) ? '300 Cropped' : 'Image Grid';

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $tag, $atts );

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">

		<?php if ( ! empty( $link_url ) ) : ?>
			<a href="<?php echo esc_attr( $link_url ); ?>" title="<?php echo esc_attr( $link_title ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
		<?php endif; ?>

		<?php if ( $image ) : ?>
			<?php echo wp_get_attachment_image( $image, $image_size ); ?>
		<?php endif; ?>

		<div class="caption">
			<?php if ( $title ) : ?>
				<h4><?php echo esc_html( $title ); ?></h4>
			<?php endif; ?>

			<?php if ( $subtitle ) : ?>
				<h5><?php echo esc_html( $subtitle ); ?></h5>
			<?php endif; ?>

			<?php if ( $content ) : ?>
				<?php echo wp_kses_post( $content ); ?>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $link_url ) ) : ?>
			</a>
		<?php endif; ?>

	</div>

	<?php

	$output = ob_get_clean();

	return $output;
}

add_action( 'vc_before_init', 'mm_vc_image_grid' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_image_grid() {

	// Image grid container.
	vc_map( array(
		'name'                    => __( 'Image Grid', 'mm-components' ),
		'base'                    => 'mm_image_grid',
		'as_parent'               => array( 'only' => 'mm_image_grid_image' ),
		'content_element'         => true,
		'class'                   => 'image-grid',
		  'icon'                    => MM_COMPONENTS_ASSETS_URL . 'image-grid_icon.png',
		'show_settings_on_create' => false,
		'params' => array(
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Title', 'mm-components' ),
				'param_name' => 'title',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Style', 'mm-components' ),
				'param_name' => 'style',
				'value'      => array(
					__( 'Select an Image Style', 'mm-components' ) => '',
					__( 'Full Image', 'mm-components ')            => 'style-full-image',
					__( 'Thumbnail/Text Card', 'mm-components ')   => 'style-thumbnail-text-card',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Max in Row', 'mm-components' ),
				'param_name' => 'max_in_row',
				'description' => __( 'Select the max number of images that should show in a single row', 'mm-components' ),
				'value'      => array(
					__( 'Select an Number', 'mm-components' ), '1', '2', '3', '4', '5', '6',
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Extra class name', 'mm-components' ),
				'param_name'  => 'el_class',
				'description' => __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'mm-components'),
			),
		),
		'js_view' => 'VcColumnView'
	) );

	// Image grid image.
	vc_map( array(
		'name'            => __( 'Image Grid Image', 'mm-components' ),
		'base'            => 'mm_image_grid_image',
		'content_element' => true,
		  'icon'            => MM_COMPONENTS_ASSETS_URL . 'component_icon.png',
		'as_child'        => array( 'only' => 'mm_image_grid' ),
		'params'          => array(
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Caption/Title', 'mm-components' ),
				'admin_label' => true,
				'param_name'  => 'title',
			),
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Subtitle', 'mm-components' ),
				'param_name' => 'subtitle',
			),
			array(
				'type'                   => 'attach_image',
				'heading'                => __( 'Image', 'mm-components' ),
				'param_name'             => 'image',
				'mm_image_size_for_desc' => 'Image Grid',
			),
			array(
				'type'       => 'vc_link',
				'heading'    => __( 'Link', 'mm-components' ),
				'param_name' => 'link',
			),
			array(
				'type'        => '',
				'heading'     => __( 'Style', 'mm-components' ),
				'param_name'  => 'style',
				'description' => __( 'You cannot set styles for individual Image Grid Images. Instead, set the style for the parent Image Grid container (the Visual Composer component that contains this image).', 'mm-components' ),
			),
		)
	) );

	// These are necessary to get the element nesting to work.
	 if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_Mm_Image_Grid extends WPBakeryShortCodesContainer {
		}
	}

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_Mm_Image_Grid_Image extends WPBakeryShortCode {
		}
	}

}
