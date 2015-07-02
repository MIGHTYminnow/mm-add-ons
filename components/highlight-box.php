<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Highlight Box
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

add_shortcode( 'mm_highlight_box', 'mm_highlight_box_shortcode' );
/**
 * Output Highlight Box.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_highlight_box_shortcode( $atts, $content = null, $tag ) {

	$atts = shortcode_atts( array(
		'heading_text'   => '',
		'paragraph_text' => '',
		'link_text'      => '',
		'link'           => '',
	), $atts );

	// Clean up content - this is necessary
	$content = wpb_js_remove_wpautop( $content, true );

	// Get link array [url, title, target]
	if ( ! empty( $atts['link_text'] ) && ! empty( $atts['link'] ) ) {
		$link_array = vc_build_link( $atts['link'] );
	}

	// Get Mm classes
	$mm_classes = str_replace( '_', '-', $tag );
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">

		<?php if ( ! empty( $atts['heading_text'] ) ) : ?>
			<h3><?php echo $atts['heading_text']; ?></h3>
		<?php endif; ?>

		<?php if ( ! empty( $atts['paragraph_text'] ) ) : ?>
			<p><?php echo $atts['paragraph_text']; ?></p>
		<?php endif; ?>

		<?php
		if ( ! empty( $atts['link_text'] ) && ! empty( $link_array['url'] ) ) {
			printf( '<a href="%s" title="%s" target="%s">%s</a>',
				$link_array['url'],
				$link_array['title'],
				$link_array['target'],
				$atts['link_text']
			);
		}
		?>

	</div>

	<?php

	$output = ob_get_clean();

	return $output;
}

add_action( 'vc_before_init', 'mm_vc_highlight_box' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_highlight_box() {

	vc_map( array(
		'name' => __( 'Highlight Box', 'mm-add-ons' ),
		'base' => 'mm_highlight_box',
		'class' => '',
		'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-add-ons' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => __( 'Heading', 'mm-add-ons' ),
				'param_name' => 'heading_text',
				'admin_label' => true,
			),
			array(
				'type' => 'textarea',
				'heading' => __( 'Paragraph Text', 'mm-add-ons' ),
				'param_name' => 'paragraph_text',
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Link Text', 'mm-add-ons' ),
				'param_name' => 'link_text',
			),
			array(
				'type' => 'vc_link',
				'heading' => __( 'Link URL', 'mm-add-ons' ),
				'param_name' => 'link',
			),
		)
	) );
}
