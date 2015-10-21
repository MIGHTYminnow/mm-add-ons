<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Polaroid 2
 *
 * @package mm-components
 * @since   1.0.0
 */

add_shortcode( 'mm_polaroid_2', 'mm_polaroid_2_shortcode' );
/**
 * Output Polaroid 2.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_polaroid_2_shortcode( $atts, $content = null, $tag ) {

	extract( mm_shortcode_atts( array(
		'title'         => '',
		'image'         => '',
		'caption'       => '',
		'caption_color' =>'',
		'link'          => '',
	), $atts ) );

	// Get link array [url, title, target]
	$link_array = vc_build_link( $link );

	// Get Mm classes
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $tag, $atts );

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">

		<?php if ( isset( $link_array['url'] ) && ! empty( $link_array['url'] ) ) : ?>
			<a href="<?php echo $link_array['url']; ?>" title="<?php echo $link_array['title']; ?>">
		<?php endif; ?>

		<?php if ( $title ) : ?>
			<h3><?php echo $title; ?></h3>
		<?php endif; ?>

		<div class="polaroid-wrap">
			<?php if ( $image ) : ?>
				<?php echo wp_get_attachment_image( $image, 'polaroid' ); ?>
			<?php endif; ?>

			<?php if ( $caption ) : ?>
				<div class="caption <?php echo $caption_color; ?>"><?php echo $caption; ?></h3></div>
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

add_action( 'vc_before_init', 'mm_vc_polaroid_2' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_polaroid_2() {

	vc_map( array(
		'name' => __( 'Polaroid 2', 'mm-components' ),
		'base' => 'mm_polaroid_2',
		'class' => '',
		'icon' => MM_COMPONENTS_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => __( 'Title', 'mm-components' ),
				'param_name' => 'title',
				'admin_label' => true,
				'value' => '',
			),
			array(
				'type' => 'attach_image',
				'heading' => __( 'Main Image', 'mm-components' ),
				'param_name' => 'image',
				'value' => '',
				'mm_image_size_for_desc' => 'polaroid',
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Caption', 'mm-components' ),
				'param_name' => 'caption',
				'value' => '',
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Caption Color', 'mm-components' ),
				'param_name' => 'caption_color',
				'value' => array(
					__( 'Light', 'mm-components ') => 'light-text',
					__( 'Dark', 'mm-components ') => 'dark-text',
				),
			),
			array(
				'type' => 'vc_link',
				'heading' => __( 'Link URL', 'mm-components' ),
				'param_name' => 'link',
				'value' => '',
			),
		)
	) );
}
