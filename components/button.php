<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Mm Button
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

add_shortcode( 'mm_button', 'mm_button_shortcode' );
/**
 * Output Mm Button.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_button_shortcode( $atts, $content = null, $tag ) {

	$atts = shortcode_atts( array(
		'link'         => '',
		'class'        => '',
		'style'        => 'default',
		'border_style' => '',
		'color'        => '',
		'size'         => '',
		'alignment'    => 'left',
	), $atts );

	// Get link array [url, title, target].
	$link_array = vc_build_link( $atts['link'] );

	// Build the alignment class.
	$alignment = 'text-align-' . $atts['alignment'];

	// Setup button classes.
	$class = 'button';
	$class .= ' ' . $atts['class'];
	$class .= ' ' . $atts['style'];
	$class .= ' ' . $atts['border_style'];
	$class .= ' ' . $atts['color'];
	$class .= ' ' . $atts['size'];

	// Get Mm classes.
	$mm_classes = 'button-container';
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );

	// Build the output.
	ob_start(); ?>

	<div class="<?php echo esc_attr( $mm_classes . ' ' . $alignment ); ?>">
		<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $link_array['url'] ) ?>" title="<?php echo esc_attr( $link_array['title'] ); ?>" target="<?php echo esc_attr( $link_array['target'] ); ?>"><?php do_shortcode( $content ) ?></a>
	</div>

	<?php

	$output = ob_get_clean();

	return do_shortcode( $output );
}

add_action( 'vc_before_init', 'mm_vc_button' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_button() {

	vc_map( array(
		'name' => __( 'Button', 'mm-add-ons' ),
		'base' => 'mm_button',
		'class' => '',
		'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-add-ons' ),
		'params' => array(
			array(
				'type'       => 'vc_link',
				'heading'    => __( 'Button URL', 'mm-add-ons' ),
				'param_name' => 'link',
				'value'      => '',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Style', 'mm-add-ons' ),
				'param_name' => 'style',
				'value'      => array(
					__( 'Default', 'mm-add-ons ') => 'default',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Border Style', 'mm-add-ons' ),
				'param_name' => 'border_style',
				'value'      => array(
					__( 'None', 'mm-add-ons' )  => 'none',
					__( 'Thin', 'mm-add-ons ')  => 'thin',
					__( 'Thick', 'mm-add-ons ') => 'thick',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Color', 'mm-add-ons' ),
				'param_name' => 'color',
				'value'      => array(
					__( 'Gray', 'mm-add-ons ') => 'gray',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Size', 'mm-add-ons' ),
				'param_name' => 'size',
				'value'      => array(
					__( 'Normal', 'mm-add-ons ') => 'normal-size',
					__( 'Large', 'mm-add-ons ')  => 'large',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Alignment', 'mm-add-ons' ),
				'param_name' => 'alignment',
				'value'      => array(
					__( 'Default', 'mm-add-ons ') => 'default',
					__( 'Left', 'mm-add-ons ')    => 'left',
					__( 'Center', 'mm-add-ons ')  => 'center',
					__( 'Right ', 'mm-add-ons ')  => 'right',
				),
			),
			array(
				'type'        => 'textarea_html',
				'heading'     => __( 'Button Text', 'mm-add-ons' ),
				'param_name'  => 'content',
				'admin_label' => true,
				'value'       => '',
			),
		)
	) );
}