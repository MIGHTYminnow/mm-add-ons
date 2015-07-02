<?php
/**
 * Mm Custom Visual Composer Functionality.
 *
 * @since 1.0.0
 *
 * @package mm-add-ons
 */

add_action( 'init', 'mm_vc_custom_component_atts', 15 );
/**
 * Add shared Mm parameters/atts to all VC components.
 *
 * Uses priority 5 to ensure params are added before VC auto mapping,
 * which occurs on priority 10 in VC v4.5.2+.
 *
 * @since  1.0.0
 */
function mm_vc_custom_component_atts() {

	// Get all available VC components
	$components = WPBMap::getShortCodes();

	// Create custom group title.
	$custom_group = __( 'Mm Custom Settings', 'mm-add-ons' );

	// Text color
	$atts[] = array(
		'type' => 'dropdown',
		'heading' => __( 'Text Color Scheme', 'mm-add-ons' ),
		'param_name' => 'mm_class_text_color',
		'value' => array(
			__( 'Default', 'mm-add-ons ') => 'text-color-default',
            __( 'Dark', 'mm-add-ons ') => 'dark-text',
            __( 'Light', 'mm-add-ons ') => 'light-text',
            __( 'Medium', 'mm-add-ons ') => 'medium-text',
		),
		'group' => $custom_group,
	);

	// Text alignment
	$atts[] = array(
		'type' => 'dropdown',
		'heading' => __( 'Text Alignment', 'mm-add-ons' ),
		'param_name' => 'mm_class_text_align',
		'value' => array(
			__( 'Default', 'mm-add-ons ') => 'text-align-default',
            __( 'Left', 'mm-add-ons ') => 'text-align-left',
            __( 'Center', 'mm-add-ons ') => 'text-align-center',
            __( 'Right', 'mm-add-ons ') => 'text-align-right',
		),
		'group' => $custom_group,
	);

	// Add each param to each VC component
	foreach ( $atts as $att ) {
		foreach ( $components as $component ) {
			vc_add_param( $component['base'], $att );
		}
	}

	add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'mm_custom_shortcode_classes', 10, 3 );
	/**
	 * Add custom shortcode classes.
	 *
	 * @since   1.0.0
	 *
	 * @param   string  $classes  Mm classes.
	 * @param   string  $tag      VC component tag (e.g. vc_row).
	 * @param   array   $atts     VC component attributes.
	 *
	 * @return  string            Modified Mm classes.
	 */
	function mm_custom_shortcode_classes( $classes, $tag, $atts ) {

		// Background image
		if ( isset( $atts['css'] ) && false !== strpos( $atts['css'], 'url(' ) ) {
			$classes .= ' has-bg-image';
		}

		// Custom attribute classes (all begin with mm_class_*)
		$mm_custom_atts = array();
		foreach ( $atts as $key => $value ) {

			if ( false !== strpos( $key, 'mm_class_' ) ) {
				$mm_custom_atts[ $key ] = str_replace( ',', ' ', $value );
			}
		}

		$classes .= ' ' . implode( ' ', $mm_custom_atts );

		return $classes;

	}

}

add_filter( 'vc_single_param_edit', 'mm_filter_vc_field_descriptions', 10, 2 );
/**
 * Add custom image upload description to VC fields.
 *
 * Note: makes use of the 'mm_image_size_for_desc' param for any image upload
 * fields, attempting to calculate 2x the image size being used and output
 * this in the field's description.
 *
 * @since     1.2.0
 *
 * @param     array    $param    Visual composer field array.
 * @param     mixed    $value    Field value.
 *
 * @return    array    $param    Updated field.
 */
function mm_filter_vc_field_descriptions( $param, $value ) {

	// Append custom description to image upload field.
	if ( 'attach_image' == $param['type'] || 'attach_images' == $param['type'] ) {
		$image_size = isset( $param['mm_image_size_for_desc'] ) ? $param['mm_image_size_for_desc'] : '';
		$custom_description = mm_custom_image_field_description( $image_size );
		$param['description'] = isset( $param['description'] ) ? $param['description'] . ' ' . $custom_description : $custom_description;
	}

	return $param;

}

/**
 * Return custom VC image upload description.
 *
 * @since     1.2.0
 *
 * @param     string    $image_size    Image size slug.
 *
 * @return    string    Image upload description.
 */
function mm_custom_image_field_description( $image_size = '' ) {

	$default_message = __( 'Upload an image that is large enough to be output without stretching.', 'mm-add-ons' );

	// Do default image message if no specific image_size is passed.
	if ( ! $image_size ) {
		return $default_message;
	}

	// Get dimensions of image.
	$image_dimensions = mm_get_image_size_dimensions( $image_size );

	// Do default message if the specified image size doesn't exists.
	if ( ! $image_dimensions ) {
		return $default_message;
	}

	$width = $image_dimensions['width'] * 2;
	$height = $image_dimensions['height'] * 2;

	return sprintf( __( 'Upload an image that is at least <b>%dpx</b> × <b>%dpx</b> to ensure that it is not stretched.', 'mm-add-ons' ), $width, $height );

}

/**
 * Get the dimensions of WP default and add-on image sizes.
 *
 * @since     1.2.0
 *
 * @param     string    $image_size          Image size slug.
 *
 * @return    array     $image_dimensions    Array of image width/height.
 */
function mm_get_image_size_dimensions( $image_size = '' ) {

	global $_wp_additional_image_sizes;

	if ( in_array( $image_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

		$image_dimensions['width'] = get_option( $image_size . '_size_w' );
		$image_dimensions['height'] = get_option( $image_size . '_size_h' );

	} elseif ( isset( $_wp_additional_image_sizes[ $image_size ] ) ) {

		$image_dimensions = array(
			'width' => $_wp_additional_image_sizes[ $image_size ]['width'],
			'height' => $_wp_additional_image_sizes[ $image_size ]['height'],
		);

	} else {
		return false;
	}

	return $image_dimensions;

}
