<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Custom Heading
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

add_shortcode( 'custom_heading', 'mm_custom_heading_shortcode' );
/**
 * Output Custom Heading.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_custom_heading_shortcode( $atts, $content = null, $tag ) {

	extract( shortcode_atts( array(
		'heading'			=> 'h2',
		'font_family'		=> '',
		'font_size'			=> '',
		'margin_bottom'		=> '',
		'color'				=> '',
		'text_transform'	=> '',
		'text_align'		=> '',
		'link'				=> '',
	), $atts ) );

	// Clean up content - this is necessary
	//$content = wpb_js_remove_wpautop( $content, true );

	// Process margin_bottom into a px value, if it was set
	if ( '' !== $margin_bottom ) {
		$margin_bottom = 'margin-bottom: ' . (int)$margin_bottom . 'px;';
	}

	// Get link array [url, title, target]
	$link_array = vc_build_link( $link );

	if ( isset( $link_array['url'] ) && ! empty( $link_array['url'] ) ) {
		$content = '<a href="' . $link_array['url'] . '" title="' . $link_array['title'] . '">' . $content . '</a>';
	}

	// Get Mm classes
	$mm_classes = str_replace( '_', '-', $tag );
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );

	// Create heading classes
	$class = implode( ' ', array(
		'font-family-' . $font_family,
		'font-size-' . $font_size,
		'color-' . $color,
		'text-transform-' . $text_transform,
		'text-align-' . $text_align,
	) );

	$output = sprintf( '<%s class="%s" style="%s">%s</%s>',
		$heading,
		$mm_classes . ' ' . $class,
		$margin_bottom,
		$content,
		$heading
	);

	return $output;

}

add_action( 'vc_before_init', 'mm_vc_custom_heading' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_custom_heading() {

	vc_map( array(
		'name' => __( 'Custom Heading', 'mm-add-ons' ),
		'base' => 'custom_heading',
		'class' => '',
		'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-add-ons' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => __( 'Heading Text', 'mm-add-ons' ),
				'param_name' => 'content',
				'admin_label' => true,
				'value' => '',
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Heading Level', 'mm-add-ons' ),
				'param_name' => 'heading',
				'std' => 'h2', // Default
				'value' => array(
					__( 'h1', 'mm-add-ons ') => 'h1',
					__( 'h2', 'mm-add-ons ') => 'h2',
					__( 'h3', 'mm-add-ons ') => 'h3',
					__( 'h4', 'mm-add-ons ') => 'h4',
					__( 'h5', 'mm-add-ons ') => 'h5',
					__( 'h6', 'mm-add-ons ') => 'h6',
				),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Margin Bottom', 'mm-add-ons' ),
				'param_name' => 'margin_bottom',
				'value' => '',
				'description' => __( 'Leave blank for default or use a number value (number of pixels). Example: 16', 'mm-add-ons' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Font Family', 'mm-add-ons' ),
				'param_name' => 'font_family',
				'value' => array(
					__( 'Default', 'mm-add-ons ') => 'default',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Font Size', 'mm-add-ons' ),
				'param_name' => 'font_size',
				'value' => array(
					__( 'Default', 'mm-add-ons ') => 'default',
					__( '50px', 'mm-add-ons ') => '50px',
					__( '48px', 'mm-add-ons ') => '48px',
					__( '40px', 'mm-add-ons ') => '40px',
					__( '36px', 'mm-add-ons ') => '36px',
					__( '30px', 'mm-add-ons ') => '30px',
					__( '24px', 'mm-add-ons ') => '24px',
					__( '16px', 'mm-add-ons ') => '16px',
					__( '14px', 'mm-add-ons ') => '14px',
					__( '12px', 'mm-add-ons ') => '12px',
				),
			//'description' => __( 'See documentation for more details.', 'mm-add-ons' )
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Color', 'mm-add-ons' ),
				'param_name' => 'color',
				'value' => array(
					__( 'Default', 'mm-add-ons ') => 'default',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Text Transform', 'mm-add-ons' ),
				'param_name' => 'text_transform',
				'value' => array(
					__( 'None', 'mm-add-ons ') => 'none',
					__( 'Uppercase', 'mm-add-ons ') => 'uppercase',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Text Align', 'mm-add-ons' ),
				'param_name' => 'text_align',
				'value' => array(
					__( 'Default', 'mm-add-ons ') => 'default',
					__( 'Left', 'mm-add-ons ') => 'left',
					__( 'Center', 'mm-add-ons ') => 'center',
					__( 'Right ', 'mm-add-ons ') => 'right',
				),
			),
			array(
				'type' => 'vc_link',
				'heading' => __( 'Heading Link', 'mm-add-ons' ),
				'param_name' => 'link',
				'value' => '',
			),
		),
	) );
}
