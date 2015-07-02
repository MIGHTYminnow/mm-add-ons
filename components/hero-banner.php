<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Hero Banner
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

add_shortcode( 'hero_banner', 'mm_hero_banner_shortcode' );
/**
 * Output Hero Banner.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_hero_banner_shortcode( $atts, $content = null, $tag ) {

	extract( shortcode_atts( array(
		'background_image'    => '',
		'background_position' => 'center center',
		'overlay_color'       => '',
		'overlay_opacity'     => '',
		'heading'             => '',
		'text_position'       => 'left',
		'button_type'         => '',
		'button_link'         => '',
		'button_video_url'    => '',
		'button_text'         => __( 'Read More', 'mm-add-ons' ),
		'button_style'        => '',
		'button_color'        => '',
		'secondary_cta'       => '',
	), $atts ) );

	// Get link array [url, title, target].
	$button_link_array = vc_build_link( $button_link );
	$button_url = ( isset( $button_link_array['url'] ) && ! empty( $button_link_array['url'] ) ) ? $button_link_array['url'] : '';
	$button_title = ( isset( $button_link_array['title'] ) && ! empty( $button_link_array['title'] ) ) ? $button_link_array['title'] : '';
	$button_target = ( isset( $button_link_array['target'] ) && ! empty( $button_link_array['target'] ) ) ? $button_link_array['target'] : '';

	// Get button classes.
	$button_classes = '';
	$button_classes .= ' ' . $button_style;
	$button_classes .= ' ' . $button_color;

	// Get CSS classes.
	$css_classes = str_replace( '_', '-', $tag );
	$css_classes .= ' full-width';
	$css_classes .= ' ' . $text_position;
	$css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_classes, $tag, $atts );

	/**
	 * Parse images.
	 *
	 * These can be passed either as an attachment ID (VC method), or manually
	 * as a URL.
	 */

	// Main image.
	if ( is_numeric( $background_image ) ) {
		$image_array = wp_get_attachment_image_src( $background_image, 'full' );
		$image = $image_array[0];
	} else {
		$image = $background_image;
	}

	// Compose style tag.
	$style = "background-image: url($image);";
	$style .= " background-position: $background_position;";

	ob_start(); ?>

	<div class="<?php echo $css_classes; ?>" style="<?php echo $style; ?>">
		<?php
		// Do background overlay.
		if ( $overlay_color && $overlay_opacity ) {
			$styles_array = array();
			$styles_array[] = "background-color: $overlay_color;";
			$styles_array[] = "opacity: $overlay_opacity;";

			$overlay_opacity_ie = $overlay_opacity * 100;
			$styles_array[] = "filter: alpha(opacity=$overlay_opacity_ie);";
			$styles = implode( ' ', $styles_array );

			printf( '<div class="color-overlay" style="%s"></div>',
				$styles
			);
		}
		?>

		<div class="hero-text-wrapper">
			<div class="wrapper">
				<?php if ( $heading ) : ?>
					<h2><?php echo $heading; ?></h2>
				<?php endif; ?>
				<?php if ( $content ) : ?>
					<p><?php echo $content; ?></p>
				<?php endif; ?>

				<?php
				if ( 'standard' == $button_type && $button_url ) {

					echo do_shortcode(
						sprintf( '[button href="%s" title="%s" target="%s" class="%s"]%s[/button]',
							$button_url,
							$button_title,
							$button_target,
							$button_classes,
							$button_text
						)
					);

				} elseif ( 'video' == $button_type && $button_video_url ) {

					$video_oEmbed = apply_filters( 'the_content', $button_video_url );

					if ( $video_oEmbed ) {

						echo do_shortcode(
							sprintf( '[mm-lightbox link_text="%s" class="button %s" lightbox_class="width-wide %s" lightbox_wrap_class="borderless-lightbox"]%s[/mm-lightbox]',
								$button_text,
								$button_classes,
								null,
								do_shortcode( $video_oEmbed )
							)
						);

					}

				}
				?>
				<?php
				if ( $secondary_cta ) :
					/**
					 * This ridiculous function is modified from Visual Composer
					 * core (vc-raw-html.php), with the main htmlentities()
					 * wrapper function removed to allow for including HTML.
					 */
				?>
					<p class="secondary-cta"><?php echo rawurldecode( base64_decode( $secondary_cta ) ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php

	$output = ob_get_clean();

	return $output;
}

add_action( 'vc_before_init', 'mm_vc_hero_banner' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_hero_banner() {

	vc_map( array(
		'name' => __( 'Hero Banner', 'mm-add-ons' ),
		'base' => 'hero_banner',
		'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-add-ons' ),
		'params' => array(
			array(
				'type' => 'attach_image',
				'heading' => __( 'Background Image', 'mm-add-ons' ),
				'param_name' => 'background_image',
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Background Position', 'mm-add-ons' ),
				'param_name' => 'background_position',
				'description' => sprintf(
					__( 'CSS background position value (%sread more%s). Defaults to: center center.', 'mm-add-ons' ),
					'<a href="http://www.w3schools.com/cssref/pr_background-position.asp" target="_blank">',
					'</a>'
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Overlay Color', 'mm-add-ons' ),
				'param_name' => 'overlay_color',
				'value' => array(
					__( 'None', 'mm-add-ons' ) => '',
					__( 'Black', 'mm-add-ons' ) => '#000',
					__( 'White', 'mm-add-ons' ) => '#fff',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Overlay Opacity', 'mm-add-ons' ),
				'param_name' => 'overlay_opacity',
				'value' => range( 0.1, 1, 0.1 ),
				'dependency' => array(
					'element' => 'overlay_color',
					'value' => array(
						'#fff',
						'#000'
					),
				),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Heading', 'mm-add-ons' ),
				'param_name' => 'heading',
				'admin_label' => true,
			),
			array(
				'type' => 'textarea_html',
				'heading' => __( 'Paragraph Text', 'mm-add-ons' ),
				'param_name' => 'content',
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Text Position', 'mm-add-ons' ),
				'param_name' => 'text_position',
				'value' => array(
					__( 'Left', 'mm-add-ons' ) => 'text-left',
					__( 'Center', 'mm-add-ons' ) => 'text-center',
					__( 'Right', 'mm-add-ons' ) => 'text-right',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Button Type', 'mm-add-ons' ),
				'param_name' => 'button_type',
				'value' => array(
					__( 'Standard', 'mm-add-ons ') => 'standard',
					__( 'Video', 'mm-add-ons ') => 'video',
				),
			),
			array(
				'type' => 'vc_link',
				'heading' => __( 'Button URL', 'mm-add-ons' ),
				'param_name' => 'button_link',
				'dependency' => array(
					'element' => 'button_type',
					'value' => array(
						'standard',
					),
				),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Video URL', 'mm-add-ons' ),
				'param_name' => 'button_video_url',
				'dependency' => array(
					'element' => 'button_type',
					'value' => array(
						'video',
					),
				),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Button Text', 'mm-add-ons' ),
				'param_name' => 'button_text',
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Button Style', 'mm-add-ons' ),
				'param_name' => 'button_style',
				'value' => array(
					__( 'Default (solid)', 'mm-add-ons ') => 'default',
					__( 'Ghost (transparent background, white border)', 'mm-add-ons ') => 'ghost',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Button Color', 'mm-add-ons' ),
				'param_name' => 'button_color',
				'value' => array(
					__( 'Default', 'mm-add-ons ') => 'default',
					__( 'Pink', 'mm-add-ons ') => 'pink',
					__( 'White', 'mm-add-ons ') => 'white',
					__( 'Gray', 'mm-add-ons ') => 'gray',
				),
				'dependency' => array(
					'element' => 'button_style',
					'value' => array(
						'ghost',
					),
				),
			),
			array(
				'type' => 'textarea_raw_html',
				'heading' => __( 'Secondary Call to Action', 'mm-add-ons' ),
				'param_name' => 'secondary_cta',
				'description' => __( 'Outputs below the main button, can include HTML markup.', 'mm-add-ons' ),
			),
		),
	) );
}
