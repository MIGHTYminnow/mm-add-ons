<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Icon Box
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

add_shortcode( 'mm_icon_box', 'mm_icon_box_shortcode' );
/**
 * Output Icon Box.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_icon_box_shortcode( $atts, $content = null, $tag ) {

	$atts = shortcode_atts( array(
		'icon' => '',
		'heading_text' => '',
	), $atts );

	// Clean up content - this is necessary
	$content = wpb_js_remove_wpautop( $content, true );

	// Get Mm classes
	$mm_classes = str_replace( '_', '-', $tag );
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">

		<i class="main-icon <?php echo $atts['icon'] ?>"></i>

		<?php if ( ! empty( $atts['heading_text'] ) ) : ?>
			<h3><?php echo $atts['heading_text']; ?></h3>
		<?php endif; ?>

		<?php if ( $content ) : ?>
			<?php echo $content; ?>
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

add_action( 'vc_before_init', 'mm_vc_icon_box' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_icon_box() {

	vc_map( array(
		'name' => __( 'Icon Box', 'mm-add-ons' ),
		'base' => 'mm_icon_box',
		'class' => '',
		'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-add-ons' ),
		'params' => array(
			array(
				'type' => 'iconpicker',
				'heading' => __( 'Icon', 'js_composer' ),
				'param_name' => 'icon',
				'value' => 'fa fa-comments-o', // default value to backend editor admin_label
				'settings' => array(
					'emptyIcon' => false,
					// default true, display an "EMPTY" icon?
					'iconsPerPage' => 4000,
					// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
				),
				'description' => __( 'Select icon from library.', 'js_composer' ),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Heading', 'mm-add-ons' ),
				'param_name' => 'heading_text',
				'admin_label' => true,
			),
			array(
				'type' => 'textarea_html',
				'heading' => __( 'Paragraph Text', 'mm-add-ons' ),
				'param_name' => 'content',
			),
		)
	) );
}
