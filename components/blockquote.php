<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Blockquote
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

add_shortcode( 'mm_blockquote', 'mm_blockquote_shortcode' );
/**
 * Output Blockquote.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_blockquote_shortcode( $atts, $content = null, $tag ) {

	$atts = shortcode_atts( array(
		'quote'    => '',
		'citation' => '',
	), $atts );

	// Get Mm classes
	$mm_classes = str_replace( '_', '-', $tag );
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );

	// Get param values.
	$quote = ! empty( $atts['quote'] ) ? '<p>' . $atts['quote'] . '</p>' : '';
	$citation = ! empty( $atts['citation'] ) ? $atts['citation'] : '';

	ob_start() ?>

	<blockquote>

		<?php echo $quote; ?>

		<?php if ( $citation ) : ?>
			<cite>- <?php echo $citation; ?></cite>
		<?php endif; ?>

	</blockquote>

	<?php

	$output = ob_get_clean();

	return $output;
}

add_action( 'vc_before_init', 'mm_vc_blockquote' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_blockquote() {

	vc_map( array(
		'name' => __( 'Blockquote', 'mm-add-ons' ),
		'base' => 'mm_blockquote',
		'class' => '',
		'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-add-ons' ),
		'params' => array(
			array(
				'type' => 'textarea',
				'heading' => __( 'Quote', 'mm-add-ons' ),
				'param_name' => 'quote',
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Citation', 'mm-add-ons' ),
				'param_name' => 'citation',
			),
		)
	) );
}
