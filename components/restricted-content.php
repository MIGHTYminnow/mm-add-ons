<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Restricted Content
 *
 * @package mm-components
 * @since   1.0.0
 */

/**
 * Build and return the Restricted Content component.
 *
 * @since   1.0.0
 *
 * @param   array  $args  The args.
 *
 * @return  string        The HTML.
 */
function mm_restricted_content( $args ) {

	$component  = 'mm-restricted-content';

	// Set our defaults and use them as needed.
	$defaults = array(
		'title'              => '',
		'roles'              => '',
		'restricted_content' => '',
		'other_content'      => '',
	);
	$args = wp_parse_args( (array)$args, $defaults);

	// Get clean param values.
	$title              = $args['title'];
	$roles              = ( strpos( $args['roles'], ',' ) ) ? explode( ',', $args['roles'] ) : (array)$args['roles'];
	$restricted_content = $args['restricted_content'];
	$other_content      = $args['other_content'];

	$valid_user = false;

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );

	foreach ( $roles as $role ) {
		if ( mm_check_user_role( $role ) ) {
			$valid_user = true;
			break;
		}
	}

	$content     = ( $valid_user ) ? $restricted_content : $other_content;
	$mm_classes .= ( $valid_user ) ? ' valid-user' : ' invalid-user';

	if ( strpos( $content, '<' ) ) {

		/* We have HTML */
		$inner_content = ( function_exists( 'wpb_js_remove_wpautop' ) ) ? wpb_js_remove_wpautop( $content, true ) : $content;

	} elseif ( mm_is_base64( $content ) ) {

		/* We have a base64 encoded string */
		$inner_output = rawurldecode( base64_decode( $content ) );

	} else {

		/* We have a non-HTML string */
		$inner_output = $content;
	}

	if ( !$valid_user && $other_content === '' ) {

		ob_start(); ?>

		<div class="<?php echo esc_attr( $mm_classes ); ?>">
			<div class="mm-restricted-content-inner">
				<?php echo do_shortcode( $inner_output ); ?>
			</div>
		</div>

	<?php

		} else {

		ob_start(); ?>

		<div class="<?php echo esc_attr( $mm_classes ); ?>">
			<div class="mm-restricted-content-inner">
				<?php echo do_shortcode( $inner_output ); ?>
			</div>
		</div>

	<?php

	}

	return ob_get_clean();
}

add_shortcode( 'mm_restricted_content', 'mm_restricted_content_shortcode' );
/**
 * Restricted Content shortcode.
 *
 * @since   1.0.0
 *
 * @param   array   $atts     Shortcode attributes.
 * @param   string  $content  Shortcode content.
 *
 * @return  string            Shortcode output.
 */
function mm_restricted_content_shortcode( $atts = array(), $content = null ) {

	if ( $content ) {
		$atts['restricted_content'] = $content;
	}

	return mm_restricted_content( $atts );
}

add_action( 'vc_before_init', 'mm_vc_restricted_content' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_restricted_content() {

	$roles = mm_get_user_roles_for_vc();

	/**
	 * Restricted Content.
	 */
	vc_map( array(
		'name'         => __( 'Restricted Content', 'mm-components' ),
		'base'         => 'mm_restricted_content',
		'icon'         => MM_COMPONENTS_ASSETS_URL . 'component_icon.png',
		'as_parent'    => array( 'except' => '' ),
		'is_container' => true,
		'params' => array(
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Allowed User Roles', 'mm-components' ),
				'param_name'  => 'roles',
				'description' => __( 'Which user role should be allowed to view this content?', 'mm-components' ),
				'value'       => $roles,
			),
			array(
				'type'        => 'textarea_raw_html',
				'heading'     => __( 'Invalid User Message', 'mm-components' ),
				'param_name'  => 'other_content',
				'description' => __( 'This message will be shown to users who do not have the specified role.', 'mm-components' ),
				'value'       => '',
			),
		),
		'js_view' => 'VcColumnView'
	) );
}

// This is necessary to make any element that wraps other elements work.
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_MM_Restricted_Content extends WPBakeryShortCodesContainer {
    }
}

add_action( 'widgets_init', 'mm_components_register_restricted_content_widget' );
/**
 * Register the widget.
 *
 * @since  1.0.0
 */
function mm_components_register_restricted_content_widget() {

	register_widget( 'mm_restricted_content_widget' );
}

/**
 * Restricted Content widget.
 *
 * @since  1.0.0
 */
class Mm_Restricted_Content_Widget extends Mm_Components_Widget {

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
			'classname'   => 'mm-restricted-content',
			'description' => __( 'A Restricted Content Container', 'mm-components' ),
		);

		parent::__construct(
			'mm_restricted_content_widget',
			__( 'Mm Restricted Content', 'mm-components' ),
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
			'title'              => '',
			'roles'              => '',
			'restricted_content' => '',
			'other_content'      => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		// At this point all instance options have been sanitized.
		$title              = apply_filters( 'widget_title', $instance['title'] );
		$roles              = $instance['roles'];
		$restricted_content = $instance['restricted_content'];
		$other_content      = $instance['other_content'];

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo mm_restricted_content( $instance );

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
			'title'              => '',
			'roles'              => '',
			'restricted_content' => '',
			'other_content'      => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		$title              = $instance['title'];
		$roles              = $instance['roles'];
		$restricted_content = $instance['restricted_content'];
		$other_content      = $instance['other_content'];
		$classname          = $this->options['classname'];

		// Title.
		$this->field_text(
			__( 'Title:', 'mm-components' ),
			$classname . '-title widefat',
			'title',
			$title
		);

		// User roles.
		$this->field_multi_checkbox(
			__( 'User roles to show restricted content to:', 'mm-components' ),
			$classname . '-roles widefat',
			'roles',
			$roles,
			mm_get_user_roles()
		);

		// Restricted content.
		$this->field_textarea(
			__( 'Restricted content for users with the selected role(s):', 'mm-components' ),
			$classname . '-content widefat',
			'restricted_content',
			$restricted_content
		);

		// Other content.
		$this->field_textarea(
			__( 'Optional content for all other users:', 'mm-components' ),
			$classname . '-invalid-message widefat',
			'other_content',
			$other_content
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
		$instance['title']              = wp_kses_post( $new_instance['title'] );
		$instance['roles']              = sanitize_text_field( $new_instance['roles'] );
		$instance['restricted_content'] = wp_kses_post( $new_instance['restricted_content'] );
		$instance['other_content']      = wp_kses_post( $new_instance['other_content'] );

		return $instance;
	}
}