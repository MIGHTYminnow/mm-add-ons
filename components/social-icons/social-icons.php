<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Social Icons
 *
 * @package mm-components
 * @since   1.0.0
 */

/**
 * Build and return the Social Icons component.
 *
 * @since   1.0.0
 *
 * @param   array  $args  The args.
 *
 * @return  string        The HTML.
 */
function mm_social_icons( $args ) {

	$component  = 'mm-social-icons';

	// Set our defaults and use them as needed.
	$defaults = array(
		'icon_type'  => 'fontawesome',
		'image_size' => 'thumbnail',
		'alignment'  => 'left',
		'style'      => 'icon-only',
		'ghost'      => '',
		'color'      => '',
		'size'       => 'normal-size',
	);
	$args = wp_parse_args( (array)$args, $defaults );

	// Allow custom output to be used.
	$custom_output = apply_filters( 'mm_social_icons_custom_output', '', $args );

	if ( '' !== $custom_output ) {
		return $custom_output;
	}

	// Get clean param values.
	$icon_type       = $args['icon_type'];
	$image_size      = $args['image_size'];
	$alignment       = $args['alignment'];
	$style           = $args['style'];
	$ghost           = $args['ghost'];
	$color           = $args['color'];
	$size            = $args['size'];
	$social_networks = mm_get_social_networks( 'mm-social-icons' );

	// Set up the icon classes.
	$classes = array();

	if ( 'images' == $icon_type ) {
		$classes[] = 'images';
		$alignment = 'mm-image-align-' .$args['alignment'];
	} else {
		$classes[] = 'icons';
		$alignment = 'mm-text-align-' . $args['alignment'];
	}
	if ( ! empty( $style ) ) {
		$classes[] = $style;
	}
	if ( mm_true_or_false( $ghost ) ) {
		$classes[] = 'ghost';
	}
	if ( ! empty( $color ) ) {
		$classes[] = $color;
	}
	if ( ! empty( $size ) ) {
		$classes[] = $size;
	}

	$classes = implode( ' ', $classes );

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );

	// Combine Mm classes with icon classes.
	$mm_classes = $mm_classes . ' ' . $classes . ' ' . $alignment;

	ob_start() ?>

	<div class="<?php echo esc_attr( $mm_classes ); ?>">

		<?php foreach ( $social_networks as $social_network => $social_network_name ) {

			$link  = ( isset( $args[ $social_network . '_link' ] ) ) ? $args[ $social_network . '_link' ] : '';
			$image = ( isset( $args[ $social_network . '_image' ] ) ) ? $args[ $social_network . '_image' ] : '';

			if ( $link ) {

				if ( 'images' == $icon_type && (int)$image ) {
					$icon = wp_get_attachment_image( (int)$image, $image_size );
				} else {
					$icon_class = 'fa fa-' . $social_network;
					$icon_class = apply_filters( 'mm_social_icons_icon_class', $icon_class, $social_network, $args );
					$icon       = sprintf(
						'<i class="icon %s"></i>',
						esc_attr( $icon_class )
					);
				}

				printf(
					'<a href="%s" class="%s" aria-label="%s link">%s</a>',
					esc_url( $link ),
					esc_attr( $social_network . '-link' ),
					esc_attr( $social_network ),
					$icon
				);
			}
		} ?>

	</div>

	<?php

	return ob_get_clean();
}

add_filter( 'mm_social_icons_icon_class', 'mm_social_icons_modify_some_icons', 10, 3 );
/**
 * Modify some of the icon classes to use the best icons available
 * in Font Awesome for each social network.
 *
 * @since   1.0.0
 *
 * @param   string  $icon_class      The original icon class.
 * @param   string  $social_network  The social network.
 * @param   array   $args            The array of component args.
 *
 * @return  string                   The new icon class.
 */
function mm_social_icons_modify_some_icons( $icon_class, $social_network, $args ) {

	if ( 'pinterest' == $social_network ) {
		$icon_class = 'fa fa-pinterest-p';
	}

	if ( 'youtube' == $social_network ) {
		$icon_class = 'fa fa-youtube-play';
	}

	return $icon_class;
}

add_shortcode( 'mm_social_icons', 'mm_social_icons_shortcode' );
/**
 * Social Icons shortcode.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_social_icons_shortcode( $atts ) {

	return mm_social_icons( $atts );
}

add_filter( 'mm_social_icons_types', 'mm_social_icons_register_core_types', 0 );
/**
 * Register our core types.
 *
 * @since  1.0.0
 *
 * @param   array  The old array of types.
 *
 * @return  array  The new array of types.
 */
function mm_social_icons_register_core_types( $types ) {

	$types = (array)$types;

	$types[ __( 'Font Awesome', 'mm-components' ) ] = 'fontawesome';
	$types[ __( 'Images', 'mm-components' ) ] = 'images';

	return $types;
}

add_action( 'vc_before_init', 'mm_vc_social_icons' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_social_icons() {

	$social_icons_types = mm_get_social_icons_types_for_vc( 'mm-social-icons' );
	$icon_styles        = mm_get_icon_styles_for_vc( 'mm-social-icons' );
	$image_sizes        = mm_get_image_sizes_for_vc( 'mm-social-icons' );
	$text_alignment     = mm_get_text_alignment_for_vc( 'mm-social-icons' );
	$colors             = mm_get_colors_for_vc( 'mm-social-icons' );
	$social_networks    = mm_get_social_networks_for_vc( 'mm-social-icons' );

	// Add our brand colors option.
	$brand_colors = array(
		__( 'Brand Colors', 'mm-components' ) => 'brand-colors'
	);
	$colors = array_merge( $colors, $brand_colors );

	vc_map( array(
		'name'     => __( 'Social Icons', 'mm-components' ),
		'base'     => 'mm_social_icons',
		'icon'     => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params'   => array(
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Type', 'mm-components' ),
				'param_name' => 'icon_type',
				'value'      => $social_icons_types,
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Image Size', 'mm-components' ),
				'param_name' => 'image_size',
				'value'      => $image_sizes,
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'images',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Alignment', 'mm-components' ),
				'param_name' => 'alignment',
				'value'      => $text_alignment,
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Style', 'mm-components' ),
				'param_name' => 'style',
				'value'      => $icon_styles,
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'fontawesome',
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Ghost Mode?', 'mm-components' ),
				'param_name'  => 'ghost',
				'description' => __( 'Colored icon and icon border with a transparent background', 'mm-components' ),
				'value'       => array(
					__( 'Yes' ) => 1,
				),
				'dependency'  => array(
					'element' => 'style',
					'value'   => array(
						'circle',
						'square',
						'rounded-square',
					),
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Color', 'mm-components' ),
				'param_name' => 'color',
				'value'      => $colors,
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'fontawesome',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Size', 'mm-components' ),
				'param_name' => 'size',
				'value'      => array(
					__( 'Normal', 'mm-components' ) => '',
					__( 'Small', 'mm-components' )  => 'small',
					__( 'Large', 'mm-components' )  => 'large',
				),
			),
		)
	) );

	$social_network_params = array();

	foreach ( $social_networks as $social_network_label => $social_network ) {

		$social_network_params[] = array(
			'type'       => 'textfield',
			'heading'    => $social_network_label . ' ' . __( 'Link', 'mm-components' ),
			'param_name' => $social_network . '_link',
			'value'      => '',
		);

		$social_network_params[] = array(
			'type'        => 'attach_image',
			'heading'     => $social_network_label . ' ' . __( 'Image', 'mm-components' ),
			'param_name'  => $social_network . '_image',
			'dependency'  => array(
				'element' => 'icon_type',
				'value'   => 'images',
			)
		);
	}

	vc_add_params( 'mm_social_icons', $social_network_params );
}

add_action( 'register_shortcode_ui', 'mm_components_mm_social_icons_shortcode_ui' );
/**
 * Register UI for Shortcake.
 *
 * @since  1.0.0
*/
function mm_components_mm_social_icons_shortcode_ui() {

	if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
		return;
	}

	$social_icons_types = mm_get_social_icons_types( 'mm-social-icons' );
	$image_sizes        = mm_get_image_sizes( 'mm-social-icons' );
	$text_alignment     = mm_get_text_alignment( 'mm-social-icons' );
	$icon_styles        = mm_get_icon_styles( 'mm-social-icons' );
	$colors             = mm_get_colors( 'mm-social-icons' );
	$social_networks    = mm_get_social_networks( 'mm-social-icons' );

	$attrs = array(
		array(
			'label'       => esc_html__( 'Icon Type', 'mm-components' ),
			'attr'        => 'icon_type',
			'type'        => 'select',
			'options'     => $social_icons_types,
		),
		array(
			'label'       => esc_html__( 'Image Size', 'mm-components' ),
			'attr'        => 'image_size',
			'type'        => 'select',
			'options'     => $image_sizes,
		),
		array(
			'label'       => esc_html__( 'Icon Alignment', 'mm-components' ),
			'attr'        => 'alignment',
			'type'        => 'select',
			'options'     => $text_alignment,
		),
		array(
			'label'       => esc_html__( 'Icon Style', 'mm-components' ),
			'attr'        => 'style',
			'type'        => 'select',
			'options'     => $icon_styles,
		),
		array(
			'label'       => esc_html__( 'Ghost Mode?', 'mm-button' ),
			'description' => esc_html__( 'Colored icon and icon border with a transparent background', 'mm-components' ),
			'attr'        => 'ghost',
			'type'        => 'checkbox',
		),
		array(
			'label'       => esc_html__( 'Icon Color', 'mm-components' ),
			'attr'        => 'color',
			'type'        => 'select',
			'options'     => $colors,
		),
		array(
			'label'       => esc_html__( 'Icon Size', 'mm-components' ),
			'attr'        => 'size',
			'type'        => 'select',
			'options'     => array(
				''      => esc_html__( 'Normal', 'mm-components' ),
				'small' => esc_html__( 'Small', 'mm-components' ),
				'large' => esc_html__( 'Large', 'mm-components' ),
			),
		),
	);

	foreach ( $social_networks as $social_network_label => $social_network ) {

		$attrs[] = array(
				'label'       => esc_html( $social_network_label . ' Link', 'mm-components' ),
				'attr'        => $social_network . '_link',
				'type'        => 'text',
		);

		$attrs[] = array(
				'label'       => esc_html__( $social_network_label . ' Image', 'mm-components' ),
				'attr'        => $social_network . '_image',
				'type'        => 'attachment',
				'libraryType' => array( 'image' ),
				'addButton'   => esc_html__( 'Select '. $social_network_label . ' Image', 'mm-components' ),
				'frameTitle'  => esc_html__( 'Select '. $social_network_label . ' Image', 'mm-components' ),
		);
	}

	shortcode_ui_register_for_shortcode(
		'mm_social_icons',
		array(
			'label'         => esc_html__( 'Mm Social Icons', 'mm-components' ),
			'listItemImage' => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
			'attrs'         => $attrs,
		)
	);
}

add_action( 'widgets_init', 'mm_components_register_social_icons_widget' );
/**
 * Register the widget.
 *
 * @since  1.0.0
 */
function mm_components_register_social_icons_widget() {

	register_widget( 'mm_social_icons_widget' );
}

/**
 * Social Icons widget.
 *
 * @since  1.0.0
 */
class Mm_Social_Icons_Widget extends Mm_Components_Widget {

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
			'classname'   => 'mm-social-icons-widget',
			'description' => __( 'A Row of Social Icons', 'mm-components' ),
		);

		parent::__construct(
			'mm_social_icons_widget',
			__( 'Mm Social Icons', 'mm-components' ),
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
			'title'           => '',
			'icon_type'       => 'fontawesome',
			'image_size'      => 'thumbnail',
			'alignment'       => 'left',
			'style'           => 'icon-only',
			'ghost'           => '',
			'color'           => '',
			'size'            => 'normal-size',
			'social_networks' => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		// Grab the title and run it through the right filter.
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo mm_social_icons( $instance );

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
			'title'           => '',
			'icon_type'       => 'fontawesome',
			'image_size'      => 'thumbnail',
			'alignment'       => 'left',
			'style'           => 'icon-only',
			'ghost'           => '',
			'color'           => '',
			'size'            => 'normal-size',
			'mm_custom_class' => '',
		);

		$colors = mm_get_colors( 'mm-social-icons' );

		// Add our brand colors option.
		$brand_colors = array(
			'brand-colors' => __( 'Brand Colors', 'mm-components' )
		);
		$colors = array_merge( $colors, $brand_colors );

		$social_networks = mm_get_social_networks( 'mm-social-icons' );

		foreach ( $social_networks as $social_network => $social_network_label ) {
			$defaults[ $social_network . '_link' ] = '';
			$defaults[ $social_network . '_image' ] = '';
		}

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		$title      = $instance['title'];
		$icon_type  = $instance['icon_type'];
		$image_size = $instance['image_size'];
		$alignment  = $instance['alignment'];
		$style      = $instance['style'];
		$ghost      = $instance['ghost'];
		$color      = $instance['color'];
		$size       = $instance['size'];
		$classname  = $this->options['classname'];

		// Title.
		$this->field_text(
			__( 'Title', 'mm-components' ),
			'',
			$classname . '-title widefat',
			'title',
			$title
		);

		// Icon Type.
		$this->field_select(
			__( 'Icon Type', 'mm-components' ),
			'',
			$classname . '-icon-type widefat',
			'icon_type',
			$icon_type,
			mm_get_social_icons_types( 'mm-social-icons' )
		);

		// Image Size.
		$this->field_select(
			__( 'Image Size', 'mm-components' ),
			'',
			$classname . '-image-size widefat',
			'image_size',
			$image_size,
			mm_get_image_sizes( 'mm-social-icons' )
		);

		// Icon Alignment.
		$this->field_select(
			__( 'Icon Alignment', 'mm-components' ),
			'',
			$classname . '-alignment widefat',
			'alignment',
			$alignment,
			mm_get_text_alignment( 'mm-social-icons' )
		);

		// Icon Style.
		$this->field_select(
			__( 'Icon Style', 'mm-components' ),
			'',
			$classname . '-style widefat',
			'style',
			$style,
			mm_get_icon_styles( 'mm-social-icons' )
		);

		// Ghost Mode.
		$this->field_multi_checkbox(
			__( 'Ghost Mode', 'mm-components' ),
			'',
			$classname . '-ghost-mode widefat',
			'ghost',
			$ghost,
			array(
				'yes' => __( 'Yes', 'mm-components' ),
			)
		);

		// Icon size.
		$this->field_select(
			__( 'Icon Size', 'mm-components' ),
			'',
			$classname . '-size widefat',
			'size',
			$size,
			array(
				''      => __( 'Normal', 'mm-components' ),
				'small' => __( 'Small', 'mm-components' ),
				'large'	=> __( 'Large', 'mm-components' ),
			)
		);

		// Icon color.
		$this->field_select(
			__( 'Icon Color', 'mm-components' ),
			'',
			$classname . '-color widefat',
			'color',
			$color,
			$colors
		);

		foreach ( $social_networks as $social_network => $social_network_label ) {

			// Social Network Link.
			$this->field_text(
				__( $social_network_label . ' URL', 'mm-components' ),
				'',
				$classname . '-social-network-link widefat',
				$social_network . '_link',
				$instance[ $social_network . '_link' ]
			);

			// Social Network Image.
			$this->field_single_media(
				__( $social_network_label . ' Image', 'mm-components' ),
				'',
				$classname . '-social-network-image widefat',
				$social_network . '_image',
				$instance[ $social_network . '_image' ]
			);
		}
	}

	/**
	 * Update the widget settings.
	 *
	 * @since   1.0.0
	 *
	 * @param   array  $new_instance  The new settings for the widget instance.
	 * @param   array  $old_instance  The old settings for the widget instance.
	 *
	 * @return  array                 The sanitized settings.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance                    = $old_instance;
		$instance['title']           = sanitize_text_field( $new_instance['title'] );
		$instance['icon_type']       = sanitize_text_field( $new_instance['icon_type'] );
		$instance['image_size']      = sanitize_text_field( $new_instance['image_size'] );
		$instance['alignment']       = sanitize_text_field( $new_instance['alignment'] );
		$instance['style']           = sanitize_text_field( $new_instance['style'] );
		$instance['ghost']           = sanitize_text_field( $new_instance['ghost'] );
		$instance['color']           = sanitize_text_field( $new_instance['color'] );
		$instance['size']            = sanitize_text_field( $new_instance['size'] );
		$instance['mm_custom_class'] = sanitize_text_field( $new_instance['mm_custom_class'] );
		$social_networks             = mm_get_social_networks( 'mm-social-icons' );

		foreach ( $social_networks as $social_network => $social_network_label ) {
			$instance[ $social_network . '_link' ]  = sanitize_text_field( $new_instance[ $social_network . '_link' ] );
			$instance[ $social_network . '_image' ] = sanitize_text_field( $new_instance[ $social_network . '_image' ] );
		}

		return $instance;
	}
}