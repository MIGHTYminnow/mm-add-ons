<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Expandable Content
 *
 * @package mm-components
 * @since   1.0.0
 */

add_shortcode( 'mm_expandable_content', 'mm_expandable_content_shortcode' );
/**
 * Output Expandable Content.
 *
 * @since  1.0.0
 *
 * @param   array    $atts     Shortcode attributes.
 * @param   string   $content  Shortcode content.
 * @param   string   $tag      Shortcode tag.
 *
 * @return  string             Shortcode output.
 */
function mm_expandable_content_shortcode( $atts = array(), $content = null, $tag = '' ) {

	$atts = mm_shortcode_atts( array(
		'link_style'     => '',
		'link_text'      => '',
		'link_alignment' => '',
		'fade'           => '',
		'class'          => '',
	), $atts );

	// Fix wpautop issues in $content.
	if ( function_exists( 'wpb_js_remove_wpautop' ) ) {
		$content = wpb_js_remove_wpautop( $content, true );
	}

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $tag, $atts );

	// Add our extra classes.
	$mm_classes .= esc_attr( $atts['class'] );

	$link_alignment = ( 'left' == $atts['link_alignment'] || 'center' == $atts['link_alignment'] || 'right' == $atts['link_alignment'] ) ? 'mm-text-align-' . $atts['link_alignment'] : '';

	$link_style = ( 'button' == $atts['link_style'] || 'link' == $atts['link_style'] ) ? $atts['link_style'] : '';

	$fade = ( mm_true_or_false( $atts['fade'] ) ) ? 'fade': '';

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">
		<div class="mm-expandable-content-trigger <?php echo $link_alignment . ' ' . $fade; ?>">
			<a class="mm-expandable-content-trigger-link <?php echo $link_style; ?>" title="<?php echo esc_attr( $atts['link_text'] ); ?>"><?php echo esc_html( $atts['link_text'] ); ?></a>
		</div>
		<div class="mm-expandable-content-target">
			<?php echo do_shortcode( $content ); ?>
		</div>
	</div>

	<?php

	$output = ob_get_clean();

	return $output;

}

add_action( 'vc_before_init', 'mm_vc_expandable_content' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_expandable_content() {

	/**
	 * Expandable Content.
	 */
	vc_map( array(
		'name'         => __( 'Expandable Content', 'mm-components' ),
		'base'         => 'mm_expandable_content',
		'icon'         => MM_COMPONENTS_ASSETS_URL . 'expandable-content_icon.png',
		'as_parent'    => array( 'except' => '' ),
		'is_container' => true,
		'params' => array(
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Button or Link?', 'mm-components' ),
				'param_name'  => 'link_style',
				'description' => __( 'Should the trigger be a button or a link?', 'mm-components' ),
				'value' => array(
					__( 'Select Button or Link', 'mm-components' ),
					__( 'Button', 'mm-components' ) => 'button',
					__( 'Link', 'mm-components' )   => 'link',
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Button/Link Text', 'mm-components' ),
				'param_name'  => 'link_text',
				'description' => __( 'The text for the button/link', 'mm-components' ),
				'default'     => '',
				'value'       => '',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button/Link Alignment', 'mm-components' ),
				'param_name' => 'link_alignment',
				'value' => array(
					__( 'Select Left, Center, or Right', 'mm-components' ),
					__( 'Left', 'mm-components' )   => 'left',
					__( 'Center', 'mm-components' ) => 'center',
					__( 'Right', 'mm-components' )  => 'right',
				),
			),
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Fade in?', 'mm-components' ),
				'param_name' => 'fade',
				'value' => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Extra class name', 'mm-components' ),
				'param_name'  => 'class',
				'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'mm-components' )
			),
		),
		'js_view' => 'VcColumnView'
	) );

}

// This is necessary to make any element that wraps other elements work.
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_MM_Expandable_Content extends WPBakeryShortCodesContainer {
	}
}
