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
		'background_image'     => '',
		'background_position'  => 'center center',
		'full_height'          => 0,
		'min_height'           => 360,
		'overlay_color'        => '',
		'overlay_opacity'      => '0.1',
		'text_color'           => 'default',
		'heading_level'        => 'h2',
		'heading_text'         => '',
		'content'              => '',
		'secondary_content'    => '',
		'content_align'        => 'left',
		'button_link'          => '',
		'button_link_target'   => '_self',
		'button_text'          => '',
		'button_style'         => '',
		'button_border_weight' => '',
		'button_corner_style'  => '',
		'button_color'         => '',
	);
	$args = wp_parse_args( (array)$args, $defaults );

	// Get clean param values.
	$background_image     = $args['background_image'];
	$background_position  = $args['background_position'];
	$full_height          = $args['full_height'];
	$min_height           = $args['min_height'];
	$overlay_color        = $args['overlay_color'];
	$overlay_opacity      = $args['overlay_opacity'];
	$text_color           = $args['text_color'];
	$heading_level        = $args['heading_level'];
	$heading_text         = $args['heading_text'];
	$content              = $args['content'];
	$secondary_content    = $args['secondary_content'];
	$content_align        = $args['content_align'];
	$button_link          = $args['button_link'];
	$button_link_target   = $args['button_link_target'];
	$button_text          = $args['button_text'];
	$button_style         = $args['button_style'];
	$button_border_weight = $args['button_border_weight'];
	$button_corner_style  = $args['button_corner_style'];
	$button_color         = $args['button_color'];

	$heading_output           = '';
	$content_output           = '';
	$overlay_output           = '';
	$button_shortcode         = '';
	$secondary_content_output = '';

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );
	$mm_classes .= ' mm-text-align-' . $args['content_align'];

	if ( mm_true_or_false( $full_height ) ) {
		$mm_classes .= ' mm-full-window-height';
	}

	// Support the background image being an ID or a URL.
	if ( is_numeric( $background_image ) ) {
		$image_size  = apply_filters( 'mm_hero_banner_image_size', 'full' );
		$image_array = wp_get_attachment_image_src( $background_image, $image_size );
		$image_url   = $image_array[0];
	} else {
		$image_url = esc_url( $background_image );
	}

	// Build wrapper styles.
	$wrap_styles = array();

	if ( $image_url ) {
		$wrap_styles[] = "background-image: url($image_url);";
	}

	if ( $background_position ) {
		$wrap_styles[] = "background-position: $background_position;";
	}

	if ( 1 < (int)$min_height ) {
		$wrap_styles[] = 'min-height: ' . (int)$min_height . 'px;';
	}

	$wrap_styles = implode( ' ', $wrap_styles );

	// Build background overlay.
	if ( $overlay_color ) {

		$overlay_styles = array();

		$overlay_styles[] = 'background-color: ' . $overlay_color . ';';
		$overlay_styles[] = 'opacity: ' . (float)$overlay_opacity . ';';
		$overlay_styles[] = 'filter: alpha(opacity=' . ( (float)$overlay_opacity * 100 ) . ');';

		$overlay_styles = implode( ' ', $overlay_styles );

		$overlay_output = '<div class="hero-overlay" style="' . esc_attr( $overlay_styles ) . '"></div>';
	}

	// Build the heading.
	if ( $heading_text ) {
		$heading_output = sprintf(
			'<%s class="hero-heading %s">%s</%s>',
			esc_html( $heading_level ),
			esc_attr( 'mm-text-color-' . $text_color ),
			esc_html( $heading_text ),
			esc_html( $heading_level )
		);
	}

	// Build the content.
	if ( strpos( $content, '<' ) ) {

		/* We have HTML */
		$content_output = ( function_exists( 'wpb_js_remove_wpautop' ) ) ? wpb_js_remove_wpautop( $content, true ) : $content;

	} elseif ( mm_is_base64( $content ) ) {

		/* We have a base64 encoded string */
		$content_output = rawurldecode( base64_decode( $content ) );

	} else {

		/* We have a non-HTML string */
		$content_output = $content;
	}

	if ( $content ) {
		$content_output = sprintf(
			'<div class="hero-content %s">%s</div>',
			esc_attr( 'mm-text-color-' . $text_color ),
			wp_kses_post( $content_output )
		);
	}

	// Build the button shortcode.
	if ( $button_text ) {

		$button_args = array(
			'link'          => $button_link,
			'link_title'    => $button_text,
			'link_target'   => $button_link_target,
			'button_text'   => $button_text,
			'style'         => $button_style,
			'corner_style'  => $button_corner_style,
			'border_weight' => $button_border_weight,
			'color'         => $button_color,
			'alignment'     => $content_align,
		);

		$button_output = mm_button( $button_args );
	}

	// Handle $secondary_content being HTML, plain text, or a base64 encoded string.
	if ( strpos( $secondary_content, '<' ) ) {

		/* We have HTML */
		$secondary_content_output = ( function_exists( 'wpb_js_remove_wpautop' ) ) ? wpb_js_remove_wpautop( $secondary_content, true ) : $secondary_content;

	} elseif ( mm_is_base64( $secondary_content ) ) {

		/* We have a base64 encoded string */
		$secondary_content_output = rawurldecode( base64_decode( $secondary_content ) );

	} else {

		/* We have a non-HTML string */
		$secondary_content_output = $secondary_content;
	}

	if ( $secondary_content ) {

		$secondary_content_output = sprintf(
			'<div class="hero-secondary-content %s">%s</div>',
			esc_attr( 'mm-text-color-' . $text_color ),
			wp_kses_post( $secondary_content_output )
		);
	}

	ob_start(); ?>

	<div class="<?php echo esc_attr( $mm_classes ); ?>" style="<?php echo esc_attr( $wrap_styles ); ?>">

		<?php echo $overlay_output; ?>

		<div class="hero-content-wrap">

			<?php echo wp_kses_post( $heading_output ); ?>

			<?php echo wp_kses_post( do_shortcode( $content_output ) ); ?>

			<?php echo wp_kses_post( $button_output ); ?>

			<?php echo wp_kses_post( do_shortcode( $secondary_content_output ) ); ?>

		</div>
	</div>

	<?php

	return ob_get_clean();
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
function mm_hero_banner_shortcode( $atts = array(), $content = null ) {

	if ( $content ) {
		$atts['content'] = $content;
	}

	return mm_hero_banner( $atts );
}

add_action( 'vc_before_init', 'mm_vc_hero_banner' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_hero_banner() {

	$background_positions   = mm_get_background_position_for_vc( 'mm-hero-banner' );
	$overlay_colors         = mm_get_overlay_colors_for_vc( 'mm-hero-banner' );
	$overlay_opacity_values = mm_get_overlay_opacity_values_for_vc( 'mm-hero-banner' );
	$text_colors            = mm_get_available_colors_for_vc( 'mm-hero-banner' );
	$heading_levels         = mm_get_heading_levels_for_vc( 'mm-hero-banner' );
	$text_alignments        = mm_get_text_alignment_for_vc( 'mm-hero-banner' );
	$button_styles          = mm_get_button_styles_for_vc( 'mm-hero-banner' );
	$button_border_weights  = mm_get_button_border_weights_for_vc( 'mm-hero-banner' );
	$button_corner_styles   = mm_get_button_corner_styles_for_vc( 'mm-hero-banner' );
	$button_colors          = mm_get_available_colors_for_vc( 'mm-hero-banner' );

	vc_map( array(
		'name'     => __( 'Hero Banner', 'mm-components' ),
		'base'     => 'mm_hero_banner',
		'icon'     => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params'   => array(
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Background Image', 'mm-components' ),
				'param_name' => 'background_image',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Background Position', 'mm-components' ),
				'param_name' => 'background_position',
				'value'      => $background_positions,
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Full Height', 'mm-components' ),
				'param_name'  => 'full_height',
				'description' => __( 'Check this to make the Hero Banner as tall as the window.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Minimum Height', 'mm-components' ),
				'param_name'  => 'min_height',
				'description' => __( 'Specify a number of pixels. Default is 360.', 'mm-components' ),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Overlay Color', 'mm-components' ),
				'param_name' => 'overlay_color',
				'value'      => $overlay_colors,
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Overlay Opacity', 'mm-components' ),
				'param_name' => 'overlay_opacity',
				'value'      => $overlay_opacity_values,
				'dependency' => array(
					'element'   => 'overlay_color',
					'not_empty' => true,
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Text Color', 'mm-components' ),
				'param_name' => 'text_color',
				'value'      => $text_colors,
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Heading Text', 'mm-components' ),
				'param_name'  => 'heading_text',
				'admin_label' => true,
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Heading Level', 'mm-components' ),
				'param_name' => 'heading_level',
				'value'      => $heading_levels,
			),
			array(
				'type'       => 'textarea_html',
				'heading'    => __( 'Content', 'mm-components' ),
				'param_name' => 'content',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Content Alignment', 'mm-components' ),
				'param_name' => 'content_align',
				'value'      => $text_alignments,
			),
			array(
				'type'       => 'vc_link',
				'heading'    => __( 'Button URL', 'mm-components' ),
				'param_name' => 'button_link',
			),
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Button Text', 'mm-components' ),
				'param_name' => 'button_text',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Style', 'mm-components' ),
				'param_name' => 'button_style',
				'value'      => $button_styles,
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Border Weight', 'mm-components' ),
				'param_name' => 'button_border_weight',
				'value'      => $button_border_weights,
				'dependency' => array(
					'element' => 'button_style',
					'value'   => array(
						'ghost',
						'solid-to-ghost',
					)
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Corner Style', 'mm-components' ),
				'param_name' => 'button_corner_style',
				'value'      => $button_corner_styles,
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Color', 'mm-components' ),
				'param_name' => 'button_color',
				'value'      => $button_colors,
			),
			array(
				'type'        => 'textarea_raw_html',
				'heading'     => __( 'Secondary Content', 'mm-components' ),
				'param_name'  => 'secondary_content',
				'description' => __( 'Outputs below the main button. Can include HTML markup.', 'mm-components' ),
			),
		),
	) );
}

add_action( 'widgets_init', 'mm_components_register_hero_banner_widget' );
/**
 * Register the widget.
 *
 * @since  1.0.0
 */
function mm_components_register_hero_banner_widget() {

	register_widget( 'mm_hero_banner_widget' );
}

/**
 * Hero Banner widget.
 *
 * @since  1.0.0
 */
class Mm_Hero_Banner_Widget extends Mm_Components_Widget {

	/**
	 * Global options for this widget.
	 *
	 * @since  1.0.0
	 */
	protected $options;

	/**
	 * Initialize an instance of the widget.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		// Set up the options to pass to the WP_Widget constructor.
		$this->options = array(
			'classname'   => 'mm-hero-banner-widget',
			'description' => __( 'A Hero Banner', 'mm-components' ),
		);

		parent::__construct(
			'mm_hero_banner_widget',
			__( 'Mm Hero Banner', 'mm-components' ),
			$this->options
		);
	}

	/**
	 * Output the widget.
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $args      The global options for the widget.
	 * @param  array  $instance  The options for the widget instance.
	 */
	public function widget( $args, $instance ) {

		$defaults = array(
			'background_image'     => '',
			'background_position'  => 'center center',
			'full_height'          => 0,
			'min_height'           => '360',
			'overlay_color'        => '',
			'overlay_opacity'      => '0.1',
			'text_color'           => 'default',
			'heading_level'        => 'h2',
			'heading_text'         => '',
			'content'              => '',
			'secondary_content'    => '',
			'content_align'        => 'left',
			'button_link'          => '',
			'button_link_target'   => '_self',
			'button_text'          => '',
			'button_style'         => '',
			'button_border_weight' => '',
			'button_corner_style'  => '',
			'button_color'         => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		echo $args['before_widget'];

		echo mm_hero_banner( $instance );

		echo $args['after_widget'];
	}

	/**
	 * Output the Widget settings form.
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $instance  The options for the widget instance.
	 */
	public function form( $instance ) {

		$defaults = array(
			'background_image'     => '',
			'background_position'  => 'center center',
			'full_height'          => 0,
			'min_height'           => '360',
			'overlay_color'        => '',
			'overlay_opacity'      => '0.1',
			'text_color'           => 'default',
			'heading_level'        => 'h2',
			'heading_text'         => '',
			'content'              => '',
			'secondary_content'    => '',
			'content_align'        => 'left',
			'button_link'          => '',
			'button_link_target'   => '_self',
			'button_text'          => '',
			'button_style'         => '',
			'button_border_weight' => '',
			'button_corner_style'  => '',
			'button_color'         => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		$background_image     = $instance['background_image'];
		$background_position  = $instance['background_position'];
		$full_height          = $instance['full_height'];
		$min_height           = $instance['min_height'];
		$overlay_color        = $instance['overlay_color'];
		$overlay_opacity      = $instance['overlay_opacity'];
		$text_color           = $instance['text_color'];
		$heading_level        = $instance['heading_level'];
		$heading_text         = $instance['heading_text'];
		$content              = $instance['content'];
		$secondary_content    = $instance['secondary_content'];
		$content_align        = $instance['content_align'];
		$button_link          = $instance['button_link'];
		$button_link_target   = $instance['button_link_target'];
		$button_text          = $instance['button_text'];
		$button_style         = $instance['button_style'];
		$button_border_weight = $instance['button_border_weight'];
		$button_corner_style  = $instance['button_corner_style'];
		$button_color         = $instance['button_color'];
		$classname            = $this->options['classname'];

		// Background image.
		$this->field_single_media(
			__( 'Background Image:', 'mm-components' ),
			__( 'Upload an image that is large enough to be output without stretching.', 'mm-components' ),
			$classname . '-background-image widefat',
			'background_image',
			$background_image
		);

		// Background position.
		$this->field_select(
			__( 'Background Position', 'mm-components' ),
			__( '', 'mm-components' ),
			$classname . '-background-position widefat',
			'background_position',
			$background_position,
			mm_get_background_position( 'mm-hero-banner' )
		);

		// Full height.
		$this->field_checkbox(
			__( 'Full Height', 'mm-components' ),
			__( 'Check this to make the Hero Banner as tall as the window.', 'mm-components' ),
			$classname . '-full-height',
			'full_height',
			$full_height
		);

		// Minimum height.
		$this->field_text(
			__( 'Minimum Height', 'mm-components' ),
			__( 'Specify a number of pixels. Default is 360.', 'mm-components' ),
			$classname . '-min-height widefat',
			'min_height',
			$min_height
		);

		// Overlay color.
		$this->field_select(
			__( 'Overlay Color', 'mm-components' ),
			'',
			$classname . '-overlay-color widefat',
			'overlay_color',
			$overlay_color,
			mm_get_overlay_colors( 'mm-hero-banner' )
		);

		// Overlay opacity.
		$this->field_select(
			__( 'Overlay Opacity', 'mm-components' ),
			'',
			$classname . '-overlay-opacity widefat',
			'overlay_opacity',
			$overlay_opacity,
			mm_get_overlay_opacity_values( 'mm-hero-banner' )
		);

		// Text color.
		$this->field_select(
			__( 'Text Color', 'mm-components' ),
			'',
			$classname . '-text-color widefat',
			'text_color',
			$text_color,
			mm_get_available_colors( 'mm-hero-banner' )
		);

		// Heading text.
		$this->field_text(
			__( 'Hero Banner Heading', 'mm-components' ),
			'',
			$classname . '-heading widefat',
			'heading_text',
			$heading_text
		);

		// Heading level.
		$this->field_select(
			__( 'Heading Level', 'mm-components' ),
			'',
			$classname . '-heading-level widefat',
			'heading_level',
			$heading_level,
			mm_get_heading_levels( 'mm-hero-banner' )
		);

		// Content.
		$this->field_textarea(
			__( 'Hero Banner Content', 'mm-components' ),
			'',
			$classname . '-content widefat',
			'content',
			$content
		);

		// Content align.
		$this->field_select(
			__( 'Content Alignment', 'mm-components' ),
			'',
			$classname . '-content-align widefat',
			'content_align',
			$content_align,
			mm_get_text_alignment( 'mm-hero-banner' )
		);

		// Button text.
		$this->field_text(
			__( 'Button Text', 'mm-components' ),
			'',
			$classname . '-button-text widefat',
			'button_text',
			$button_text
		);

		// Button link.
		$this->field_text(
			__( 'Button Link', 'mm-components' ),
			'',
			$classname . '-button-link widefat',
			'button_link',
			$button_link
		);

		// Button link target.
		$this->field_select(
			__( 'Button Link Target', 'mm-components' ),
			'',
			$classname . '-link-target widefat',
			'button_link_target',
			$button_link_target,
			mm_get_link_targets( 'mm-hero-banner' )
		);

		// Button style.
		$this->field_select(
			__( 'Button Style', 'mm-components' ),
			'',
			$classname . '-button-style widefat',
			'button_style',
			$button_style,
			mm_get_button_styles( 'mm-hero-banner' )
		);

		// Button border weight.
		$this->field_select(
			__( 'Button Border Weight', 'mm-components' ),
			'',
			$classname . '-button-border-weight widefat',
			'button_border_weight',
			$button_border_weight,
			mm_get_button_border_weights( 'mm-hero-banner' )
		);

		// Button corner style.
		$this->field_select(
			__( 'Button Corner Style', 'mm-components' ),
			'',
			$classname . '-button-corner-style widefat',
			'button_corner_style',
			$button_corner_style,
			mm_get_button_corner_styles( 'mm-hero-banner' )
		);

		// Button color.
		$this->field_select(
			__( 'Button Color', 'mm-components' ),
			'',
			$classname . '-button-color widefat',
			'button_color',
			$button_color,
			mm_get_available_colors( 'mm-hero-banner' )
		);

		// Secondary content.
		$this->field_textarea(
			__( 'Secondary Content', 'mm-components' ),
			__( 'Outputs below the main button. Can include HTML markup.', 'mm-components' ),
			$classname . '-secondary-content widefat',
			'secondary_content',
			$secondary_content
		);
	}

	/**
	 * Update the widget settings.
	 *
	 * @since  1.0.0
	 *
	 * @param   array  $new_instance  The new settings for the widget instance.
	 * @param   array  $old_instance  The old settings for the widget instance.
	 *
	 * @return  array                 The sanitized settings.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['background_image']     = sanitize_text_field( $new_instance['background_image'] );
		$instance['background_position']  = sanitize_text_field( $new_instance['background_position'] );
		$instance['full_height']          = ( isset( $new_instance['full_height'] ) ) ? sanitize_text_field( $new_instance['full_height'] ) : '';
		$instance['min_height']           = intval( $new_instance['min_height'] );
		$instance['overlay_color']        = wp_kses_post( $new_instance['overlay_color'] );
		$instance['overlay_opacity']      = wp_kses_post( $new_instance['overlay_opacity'] );
		$instance['text_color']           = sanitize_text_field( $new_instance['text_color'] );
		$instance['heading_level']        = sanitize_text_field( $new_instance['heading_level'] );
		$instance['heading_text']         = sanitize_text_field( $new_instance['heading_text'] );
		$instance['content']              = wp_kses_post( $new_instance['content'] );
		$instance['secondary_content']    = wp_kses_post( $new_instance['secondary_content'] );
		$instance['content_align']        = sanitize_text_field( $new_instance['content_align'] );
		$instance['button_link']          = sanitize_text_field( $new_instance['button_link'] );
		$instance['button_link_target']   = sanitize_text_field( $new_instance['button_link_target'] );
		$instance['button_text']          = sanitize_text_field( $new_instance['button_text'] );
		$instance['button_style']         = sanitize_text_field( $new_instance['button_style'] );
		$instance['button_border_weight'] = sanitize_text_field( $new_instance['button_border_weight'] );
		$instance['button_corner_style']  = sanitize_text_field( $new_instance['button_corner_style'] );
		$instance['button_color']         = sanitize_text_field( $new_instance['button_color'] );

		return $instance;
	}
}