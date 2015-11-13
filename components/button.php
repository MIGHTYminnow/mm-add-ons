<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Mm Button
 *
 * @package mm-components
 * @since   1.0.0
 */

/**
 * Build and return the Button component.
 *
 * @since   1.0.0
 *
 * @param   array  $args  The args.
 *
 * @return  string        The HTML.
 */
function mm_button( $args ) {

	$component  = 'mm-button';

	// Set our defaults and use them as needed.
	$defaults = array(
		'link'          => '',
		'link_title'    => '',
		'link_target'   => '',
		'button_text'   => '',
		'class'         => '',
		'style'         => '',
		'corner_style'  => '',
		'border_weight' => 'thin',
		'color'         => '',
		'size'          => '',
		'full_width'    => '',
		'alignment'     => 'left',
	);
	$args = wp_parse_args( (array)$args, $defaults );

	// Handle a raw link or a VC link array.
	$link_url    = '';
	$link_title  = '';
	$link_target = '';

	if ( ! empty( $args['link'] ) ) {

		if ( 'url' === substr( $args['link'], 0, 3 ) ) {

			if ( function_exists( 'vc_build_link' ) ) {

				$link_array  = vc_build_link( $args['link'] );
				$link_url    = $link_array['url'];
				$link_title  = $link_array['title'];
				$link_target = $link_array['target'];
			}

		} else {

			$link_url    = $args['link'];
			$link_title  = $args['link_title'];
			$link_target = $args['link_target'];
		}
	}

	// Build the alignment class.
	$alignment = 'mm-text-align-' . $args['alignment'];

	// Setup button classes.
	$classes = array();
	$classes[] = 'mm-button';
	if ( ! empty( $args['class'] ) ) {
		$classes[] = $args['class'];
	}
	if ( ! empty( $args['style'] ) ) {
		$classes[] = $args['style'];
	}
	if ( ! empty( $args['corner_style'] ) ) {
		$classes[] = $args['corner_style'];
	}
	if ( ! empty( $args['border_weight'] ) ) {
		$classes[] = $args['border_weight'];
	}
	if ( ! empty( $args['color'] ) ) {
		$classes[] = $args['color'];
	}
	if ( ! empty( $args['size'] ) ) {
		$classes[] = $args['size'];
	}
	if ( ! empty( $args['full_width'] ) && mm_true_or_false( 1 ) ) {
		$classes[] = 'full-width';
	}

	$classes = implode( ' ', $classes );

	// Remove any paragraphs and extra whitespace in the button text.
	$button_text = wp_kses( trim( $args['button_text'] ), '<p>' );

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );

	// Use wrapper class on main wrapper.
	$mm_classes = str_replace( 'mm-button', 'mm-button-wrapper', $mm_classes );

	// Build the output.
	ob_start(); ?>

	<div class="<?php echo esc_attr( $mm_classes . ' ' . $alignment ); ?>">
		<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $link_url ) ?>" title="<?php echo esc_attr( $link_title ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo do_shortcode( $button_text ) ?></a>
	</div>

	<?php

	return ob_get_clean();
}

add_shortcode( 'mm_button', 'mm_button_shortcode' );
/**
 * Button shortcode.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_button_shortcode( $atts = array(), $content = null ) {

	if ( $content ) {
		$atts['button_text'] = $content;
	}

	return mm_button( $atts );
}

add_action( 'vc_before_init', 'mm_vc_button' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_button() {

	$colors = mm_get_available_colors_for_vc();
	$text_alignment = mm_get_text_alignment_for_vc();

	vc_map( array(
		'name'     => __( 'Button', 'mm-components' ),
		'base'     => 'mm_button',
		'class'    => '',
		'icon'     => MM_COMPONENTS_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params'   => array(
			array(
				'type'       => 'vc_link',
				'heading'    => __( 'Button URL', 'mm-components' ),
				'param_name' => 'link',
				'value'      => '',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Style', 'mm-components' ),
				'param_name' => 'style',
				'value'      => array(
					__( 'Default', 'mm-components' )        => 'default',
					__( 'Ghost', 'mm-components' )          => 'ghost',
					__( 'Solid to Ghost', 'mm-components' ) => 'solid-to-ghost',
					__( '3D', 'mm-components' )             => 'three-d',
					__( 'Gradient', 'mm-components' )       => 'gradient',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Border Weight', 'mm-components' ),
				'param_name' => 'border_weight',
				'value'      => array(
					__( 'Thin', 'mm-components' )   => 'thin',
					__( 'Thick', 'mm-components' )  => 'thick',
				),
				'dependency' => array(
					'element' => 'style',
					'value'   => array(
						'ghost',
						'solid-to-ghost',
					)
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Corner Style', 'mm-components' ),
				'param_name' => 'corner_style',
				'value'      => array(
					__( 'Pointed', 'mm-components' ) => 'pointed',
					__( 'Rounded', 'mm-components' ) => 'rounded',
					__( 'Pill', 'mm-components' )    => 'pill',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Color', 'mm-components' ),
				'param_name' => 'color',
				'value'      => $colors,
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Size', 'mm-components' ),
				'param_name' => 'size',
				'value'      => array(
					__( 'Normal', 'mm-components' )      => 'normal-size',
					__( 'Small', 'mm-components' )       => 'small',
					__( 'Large', 'mm-components' )       => 'large',
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Full Width Button', 'mm-components' ),
				'param_name'  => 'full_width',
				'description' => __( 'Choosing full-width will make the button take up the width of its container.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 'full-width',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Button Alignment', 'mm-components' ),
				'param_name' => 'alignment',
				'value'      => $text_alignment,
			),
			array(
				'type'        => 'textarea_html',
				'heading'     => __( 'Button Text', 'mm-components' ),
				'param_name'  => 'content',
				'admin_label' => true,
				'value'       => '',
			),
		)
	) );
}

add_action( 'widgets_init', 'mm_components_register_button' );
/**
 * Register the button widget.
 *
 * @since 1.0.0
 */
function mm_components_register_button() {

	register_widget( 'mm_button_widget' );
}

/**
 * Button widget.
 *
 * @since 1.0.0
 */
class Mm_Button_Widget extends Mm_Components_Widget {

	/**
	 * Global options for this widget.
	 *
	 * @since 1.0.0
	 */
	protected $options;

	/**
	 * Initalize an instance of the widget.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Set up the options to pass to the WP_Widget constructor.
		$this->options = array(
			'classname'   => 'mm-button',
			'description' => __( 'A Button', 'mm-components' ),
		);

		parent::__construct(
			'mm_button_widget',
			__( 'Mm Button', 'mm-components' ),
			$this->options
		);
	}

	/**
	 * Output the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $args      The global options for the widget.
	 * @param  array  $instance  The options for the widget instance.
	 */
	public function widget( $args, $instance ) {

		// At this point, all instance options have been sanitized.
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo mm_button( $instance );

		echo $args['after_widget'];
	}

	/**
	 * Output the Widgets settings form.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $instance  The options for the widget instance.
	 */
	public function form( $instance ) {

		$defaults = array(
			'title'         => '',
			'link'          => '',
			'button_text'   => '',
			'style'         => '',
			'corner_style'  => '',
			'border_weight' => '',
			'color'         => '',
			'size'          => '',
			'full_width'    => '',
			'alignment'     => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		$title          = $instance['title'];
		$link           = $instance['link'];
		$button_text    = $instance['button_text'];
		$style          = $instance['style'];
		$corner_style   = $instance['corner_style'];
		$border_weight  = $instance['border_weight'];
		$color          = $instance['color'];
		$size           = $instance['size'];
		$full_width     = mm_true_or_false( $instance['full_width'] );
		$alignment      = $instance['alignment'];
		$classname      = $this->options['classname'];
		$colors         = mm_get_available_colors();
		$text_alignment = mm_get_text_alignment();

		// Handle the case of a newly added widget that doesn't yet have button text set.
		$preview_instance = $instance;
		if ( '' == $preview_instance['button_text'] ) {
			$preview_instance['button_text'] = __( 'Button Text', 'mm-components' );
		}

		// Title.
		$this->field_text(
			__( 'Title', 'mm-components' ),
			'',
			$classname . '-title widefat',
			'title',
			$title
		);

		// Preview.
		$this->field_custom(
			__( 'Button Preview', 'mm-components' ),
			'',
			'<div class="mm-button-preview-wrap">' . mm_button( $preview_instance ) . '</div>'
		);

		// Link.
		$this->field_text(
			__( 'Button Link', 'mm-components' ),
			'',
			$classname . '-link widefat',
			'link',
			$link
		);

		// Link title.
		$this->field_text(
			__( 'Button Text', 'mm-components' ),
			'',
			$classname . '-button-text widefat',
			'button_text',
			$button_text
		);

		// Button style.
		$this->field_select(
			__( 'Button Style', 'mm-components' ),
			'',
			$classname . '-style widefat',
			'style',
			$style,
			array(
				'default'        => __( 'Default', 'mm-components' ),
				'ghost'          => __( 'Ghost', 'mm-components' ),
				'solid-to-ghost' => __( 'Solid to Ghost', 'mm-components' ),
				'three-d'        => __( '3D', 'mm-components' ),
				'gradient'       => __( 'Gradient', 'mm-components' ),
			)
		);

		// Corner style.
		$this->field_select(
			__( 'Corner Style', 'mm-components' ),
			'',
			$classname . '-corner-style widefat',
			'corner_style',
			$corner_style,
			array(
				'pointed' => __( 'Pointed', 'mm-components' ),
				'rounded' => __( 'Rounded', 'mm-components' ),
				'pill'    => __( 'Pill', 'mm-components' ),
			)
		);

		// Border weight.
		$this->field_select(
			__( 'Border Weight', 'mm-components' ),
			'',
			$classname . 'border-weight widefat',
			'border_weight',
			$border_weight,
			array(
				'thin'  => __( 'Thin', 'mm-components' ),
				'thick' => __( 'Thick', 'mm-components' ),
			)
		);

		// Color.
		$this->field_select(
			__( 'Color', 'mm-components' ),
			'',
			$classname . '-color widefat',
			'color',
			$color,
			$colors
		);

		// Size.
		$this->field_select(
			__( 'Size', 'mm-components' ),
			'',
			$classname . '-size widefat',
			'size',
			$size,
			array(
				'normal' => __( 'Normal', 'mm-components' ),
				'small'  => __( 'Small', 'mm-components' ),
				'large'  => __( 'Large', 'mm-components' ),
			)
		);

		// Full width.
		$this->field_checkbox(
			__( 'Full Width', 'mm-components' ),
			__( 'Choosing full-width will make the button take up the width of its container.', 'mm-components' ),
			$classname . '-full-width widefat',
			'full_width',
			$full_width
		);

		// Alignment.
		$this->field_select(
			__( 'Button Alignment', 'mm-components' ),
			'',
			$classname . '-alignment widefat',
			'alignment',
			$alignment,
			$text_alignment
		);
	}

	/**
	 * Update the widget settings.
	 *
	 * @since 1.0.0
	 * @param  array  $new_instnace  The new settings for the widget instance.
	 * @param  array  $old_instance  The old settings for the widget instance.
	 *
	 * @return  array  The sanitized settings.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance                  = $old_instance;
		$instance['title']         = wp_kses_post( $new_instance['title'] );
		$instance['link']          = ( '' !== $new_instance['link'] ) ? esc_url( $new_instance['link'] ) : '';
		$instance['button_text']   = wp_kses_post( $new_instance['button_text'] );
		$instance['style']         = sanitize_text_field( $new_instance['style'] );
		$instance['corner_style']  = sanitize_text_field( $new_instance['corner_style'] );
		$instance['border_weight'] = sanitize_text_field( $new_instance['border_weight'] );
		$instance['color']         = sanitize_text_field( $new_instance['color'] );
		$instance['size']          = sanitize_text_field( $new_instance['size'] );
		$instance['full_width']    = sanitize_text_field( $new_instance['full_width'] );
		$instance['alignment']     = sanitize_text_field( $new_instance['alignment'] );

		return $instance;
	}
}
