<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Image Grid
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

add_shortcode( 'image_grid', 'mm_image_grid_shortcode' );
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

	extract( shortcode_atts( array(
		'title' => '',
		'style' => 'style-full-image',
	), $atts ) );

	// Set global style variable to pass to nest Image Grid Image components
	global $mm_image_grid_style;
	$mm_image_grid_style = $style;

	// Clean up content - this is necessary
	$content = wpb_js_remove_wpautop( $content, true );

	// Get Mm classes
	$mm_classes = str_replace( '_', '-', $tag );
	$mm_classes .= ' ' . $style;
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">

		<?php if ( $title ) : ?>
			<h4><?php echo $title; ?></h4>
		<?php endif; ?>

		<?php if ( $content ) : ?>
			<?php echo $content; ?>
		<?php endif; ?>

	</div>

	<?php

	$output = ob_get_clean();

	// Reset global style variable in case of multiple Image Grids on page
	$mm_image_grid_style = '';

	return $output;
}

add_shortcode( 'image_grid_image', 'mm_image_grid_image_shortcode' );
/**
 * [image_grid_image title="" image="" text="" link="" author_img=""]
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_image_grid_image_shortcode( $atts, $content = null, $tag ) {

	// Global style variable passed from parent Image Grid component
	global $mm_image_grid_style;

	extract( shortcode_atts( array(
		'title'    => '',
		'subtitle' => '',
		'image'    => '',
		'link'     => '',
	), $atts ) );

   	// Clean up content - this is necessary
	$content = wpb_js_remove_wpautop( $content, true );

   	// Get link array [url, title, target]
	$link_array = vc_build_link( $link );

	// Get image size based on style of parent Image Grid component
	$image_size = ( 'style-thumbnail-text-card' == $mm_image_grid_style ) ? '300 Cropped' : 'Image Grid';

	// Get Mm classes
	$mm_classes = str_replace( '_', '-', $tag );
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">

		<?php if ( isset( $link_array['url'] ) && ! empty( $link_array['url'] ) ) : ?>
			<a href="<?php echo $link_array['url']; ?>" title="<?php echo $link_array['title']; ?>">
		<?php endif; ?>

		<?php if ( $image ) : ?>
			<?php echo wp_get_attachment_image( $image, $image_size ); ?>
		<?php endif; ?>

		<div class="caption">
			<?php if ( $title ) : ?>
				<h4><?php echo $title; ?></h4>
			<?php endif; ?>

			<?php if ( $content ) : ?>
				<?php echo $content; ?>
			<?php endif; ?>
		</div>

		<?php if ( isset( $link_array['url'] ) && ! empty( $link_array['url'] ) ) : ?>
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

	// Image Grid Container
	vc_map( array(
		'name' => __( 'Image Grid', 'mm-add-ons' ),
		'base' => 'image_grid',
		'as_parent' => array( 'only' => 'image_grid_image' ), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
		'content_element' => true,
		'class' => 'image-grid',
      	'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'show_settings_on_create' => false,
		'params' => array(
		// add params same as with any other content element
			array(
				'type' => 'textfield',
				'heading' => __( 'Title', 'mm-add-ons' ),
				'param_name' => 'title',
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Extra class name', 'mm-add-ons' ),
				'param_name' => 'el_class',
				'description' => __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'mm-add-ons'),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Style', 'mm-add-ons' ),
				'param_name' => 'style',
				'value' => array(
					__( 'Full Image', 'mm-add-ons ') => 'style-full-image',
					__( 'Thumbnail/Text Card', 'mm-add-ons ') => 'style-thumbnail-text-card',
				),
			),
		),
		'js_view' => 'VcColumnView'
	) );

	vc_map( array(
		'name' => __( 'Image Grid Image', 'mm-add-ons' ),
		'base' => 'image_grid_image',
		'content_element' => true,
      	'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'as_child' => array( 'only' => 'image_grid' ), // Use only|except attributes to limit parent (separate multiple values with comma)
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => __( 'Caption/Title', 'mm-add-ons' ),
				'admin_label' => true,
				'param_name' => 'title',
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Subtitle', 'mm-add-ons' ),
				'param_name' => 'subtitle',
			),
			array(
				'type' => 'attach_image',
				'heading' => __( 'Image', 'mm-add-ons' ),
				'param_name' => 'image',
				'mm_image_size_for_desc' => 'Image Grid',
			),
			array(
				'type' => 'vc_link',
				'heading' => __( 'Link', 'mm-add-ons' ),
				'param_name' => 'link',
			),
			array(
				'type' => '',
				'heading' => __( 'Style', 'mm-add-ons' ),
				'param_name' => 'style',
				'description' => __( 'You cannot set styles for individual Image Grid Images. Instead, set the style for the parent Image Grid container (the Visual Composer component that contains this image).', 'mm-add-ons' ),
			),
		)
	) );

 	// Your 'container' content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_Image_Grid extends WPBakeryShortCodesContainer {
		}
	}

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_Image_Grid_Image extends WPBakeryShortCode {
		}
	}

}
