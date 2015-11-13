<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Hero Banner
 *
 * @package mm-components
 * @since   1.0.0
 */

function mm_hero_banner( $args ) {

	$component = 'mm-hero-banner';

	// Set our defaults and use them as needed.
	$defaults = array(
		'background_image'    => '',
		'background_position' => 'center center',
		'overlay_color'       => '',
		'overlay_opacity'     => '',
		'heading'             => '',
		'text_position'       => 'left',
		'button_type'         => '',
		'button_link'         => '',
		'button_link_target'  => '',
		'button_video_url'    => '',
		'button_text'         => __( 'Read More', 'mm-components' ),
		'button_style'        => '',
		'button_color'        => '',
		'secondary_cta'       => '',
	);

	$args = wp_parse_args( (array)$args, $defaults );

	// Get clean param values.
	$background_image     = $args['background_image'];
	$background_position  = $args['background_position'];
	$overlay_color        = $args['overlay_color'];
	$overlay_opacity      = $args['overlay_opacity'];
	$heading              = $args['heading'];
	$text_position        = $args['text_position'];
	$button_type          = $args['button_type'];
	$button_link          = $args['button_link'];
	$button_link_target   = $args['button_link_target'];
	$button_video_url     = $args['button_video_url'];
	$button_text          = $args['button_text'];
	$button_style         = $args['button_style'];
	$button_color         = $args['button_color'];
	$secondary_cta        = $args['secondary_cta'];


	$button_url    = '';
	$button_title  = '';
	$button_target = '';

	if ( ! empty( $button_link ) ) {

		if ( 'url' === substr( $button_link, 0, 3 ) ) {

			if ( function_exists( 'vc_build_link' ) ) {

				$link_array  = vc_build_link( $button_link );
				$button_url    = $link_array['url'];
				$button_title  = $link_array['title'];
				$button_target = $link_array['target'];

			} else {

				$button_url    = $button_link;
				$button_title  = $button_text;
				$button_target = $button_link_target;
			}
		}

	}

	// Get button classes.
	$button_classes = '';
	$button_classes .= ' ' . $button_style;
	$button_classes .= ' ' . $button_color;

	// Get MM classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );

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

add_shortcode( 'mm_hero_banner', 'mm_hero_banner_shortcode' );
/**
 * Output Hero Banner.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_hero_banner_shortcode( $atts ) {

	return mm_hero_banner( $atts );

}

add_action( 'vc_before_init', 'mm_vc_hero_banner' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_hero_banner() {

	vc_map( array(
		'name' => __( 'Hero Banner', 'mm-components' ),
		'base' => 'mm_hero_banner',
		'icon' => MM_COMPONENTS_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params' => array(
			array(
				'type' => 'attach_image',
				'heading' => __( 'Background Image', 'mm-components' ),
				'param_name' => 'background_image',
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Background Position', 'mm-components' ),
				'param_name' => 'background_position',
				'description' => sprintf(
					__( 'CSS background position value (%sread more%s). Defaults to: center center.', 'mm-components' ),
					'<a href="http://www.w3schools.com/cssref/pr_background-position.asp" target="_blank">',
					'</a>'
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Overlay Color', 'mm-components' ),
				'param_name' => 'overlay_color',
				'value' => array(
					__( 'None', 'mm-components' ) => '',
					__( 'Black', 'mm-components' ) => '#000',
					__( 'White', 'mm-components' ) => '#fff',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Overlay Opacity', 'mm-components' ),
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
				'heading' => __( 'Heading', 'mm-components' ),
				'param_name' => 'heading',
				'admin_label' => true,
			),
			array(
				'type' => 'textarea_html',
				'heading' => __( 'Paragraph Text', 'mm-components' ),
				'param_name' => 'content',
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Text Position', 'mm-components' ),
				'param_name' => 'text_position',
				'value' => array(
					__( 'Left', 'mm-components' ) => 'text-left',
					__( 'Center', 'mm-components' ) => 'text-center',
					__( 'Right', 'mm-components' ) => 'text-right',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Button Type', 'mm-components' ),
				'param_name' => 'button_type',
				'value' => array(
					__( 'Standard', 'mm-components ') => 'standard',
					__( 'Video', 'mm-components ') => 'video',
				),
			),
			array(
				'type' => 'vc_link',
				'heading' => __( 'Button URL', 'mm-components' ),
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
				'heading' => __( 'Video URL', 'mm-components' ),
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
				'heading' => __( 'Button Text', 'mm-components' ),
				'param_name' => 'button_text',
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Button Style', 'mm-components' ),
				'param_name' => 'button_style',
				'value' => array(
					__( 'Default (solid)', 'mm-components ') => 'default',
					__( 'Ghost (transparent background, white border)', 'mm-components ') => 'ghost',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Button Color', 'mm-components' ),
				'param_name' => 'button_color',
				'value' => array(
					__( 'Default', 'mm-components ') => 'default',
					__( 'Pink', 'mm-components ') => 'pink',
					__( 'White', 'mm-components ') => 'white',
					__( 'Gray', 'mm-components ') => 'gray',
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
				'heading' => __( 'Secondary Call to Action', 'mm-components' ),
				'param_name' => 'secondary_cta',
				'description' => __( 'Outputs below the main button, can include HTML markup.', 'mm-components' ),
			),
		),
	) );
}

//add_action( 'widgets_init', 'mm_components_register_hero_banner_widget' );
/**
 * Register the widget.
 *
 * @since  1.0.0
 */
/**function mm_components_register_hero_banner_widget() {

	register_widget( 'mm_hero_banner_widget' );
}**/