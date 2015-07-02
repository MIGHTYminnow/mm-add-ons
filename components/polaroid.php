<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Polaroid
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

add_shortcode( 'polaroid', 'mm_polaroid_shortcode' );
/**
 * Output Polaroid.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_polaroid_shortcode( $atts, $content = null, $tag ) {

	extract( shortcode_atts( array(
		'title'        => '',
		'image'        => '',
		'author_image' => '',
		'link'         => '',
		'link_text'    => __( 'Visit campaign', 'mm-add-ons' ),
		'banner_text'  => '',
		'class'        => '',
	), $atts ) );

	// Clean up content - this is necessary
	$content = wpb_js_remove_wpautop( $content, true );

	// Get link array [url, title, target]
	$link_array = vc_build_link( $link );

	// Get Mm classes
	$mm_classes = str_replace( '_', '-', $tag );
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );
	$mm_classes .= ' ' . $class;

	/**
	 * Parse images.
	 *
	 * These can be passed either as an attachment ID (VC method), or manually
	 * as a URL.
	 */

	// Main image.
	if ( is_numeric( $image ) ) {
		$image_array = wp_get_attachment_image_src( $image, 'polaroid' );
		$image = $image_array[0];
	}

	// Author image.
	if ( is_numeric( $author_image ) ) {
		$author_image_array = wp_get_attachment_image_src( $author_image, 'thumbnail' );
		$author_image = $author_image_array[0];
	}

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">

		<?php if ( $title ) : ?>
			<h4><?php echo $title; ?></h4>
		<?php endif; ?>

		<div class="polaroid-wrap">
			<?php if ( $image ) : ?>
				<?php if ( isset( $link_array['url'] ) && ! empty( $link_array['url'] ) ) : ?>
					<a href="<?php echo $link_array['url']; ?>" title="<?php echo $link_array['title']; ?>">
				<?php endif; ?>
				<img src="<?php echo $image; ?>" class="main-image" alt="<?php _e( 'Campaign image'); ?>"/>
				<?php if ( isset( $link_array['url'] ) && ! empty( $link_array['url'] ) ) : ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>

			<div class="text-wrap">
				<?php if ( $author_image ) : ?>
					<img src="<?php echo $author_image; ?>" class="author-image" alt="<?php _e( 'Campaign author image'); ?>"/>
				<?php endif; ?>

				<?php if ( $content ) : ?>
					<?php echo $content; ?>
				<?php endif; ?>

				<?php if ( isset( $link_array['url'] ) && ! empty( $link_array['url'] ) ) : ?>
					<a class="more-link" href="<?php echo $link_array['url']; ?>" title="<?php echo $link_array['title']; ?>"><?php echo $link_text; ?></a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $banner_text ) : ?>
			<div class="banner-front"><?php echo $banner_text; ?></div>
			<div class="banner-back"><div class="banner-back-left"></div><div class="banner-back-right"></div></div>
		<?php endif; ?>

	</div>

	<?php

	$output = ob_get_clean();

	return $output;
}

add_action( 'vc_before_init', 'mm_vc_polaroid' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_polaroid() {

	vc_map( array(
		'name' => __( 'Polaroid', 'mm-add-ons' ),
		'base' => 'polaroid',
		'class' => '',
		'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-add-ons' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => __( 'Title', 'mm-add-ons' ),
				'param_name' => 'title',
				'admin_label' => true,
				'value' => '',
			),
			array(
				'type' => 'attach_image',
				'heading' => __( 'Main Image', 'mm-add-ons' ),
				'param_name' => 'image',
				'value' => '',
				'mm_image_size_for_desc' => 'polaroid',
			),
			array(
				'type' => 'attach_image',
				'heading' => __( 'Author Image', 'mm-add-ons' ),
				'param_name' => 'author_image',
				'value' => '',
				'mm_image_size_for_desc' => 'thumbnail',
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Link Text', 'mm-add-ons' ),
				'param_name' => 'link_text',
				'value' => '',
				'description' => __( 'Defaults to "Visit campaign".', 'mm-add-ons' )
			),
			array(
				'type' => 'vc_link',
				'heading' => __( 'Link URL', 'mm-add-ons' ),
				'param_name' => 'link',
				'value' => '',
			),
			array(
				'type' => 'textarea_html',
				'heading' => __( 'Text', 'mm-add-ons' ),
				'param_name' => 'content',
				'value' => '',
			)
		)
	) );
}
