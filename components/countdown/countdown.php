<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Countdown
 *
 * @package mm-components
 * @since   1.0.0
 */

/**
 * Build and return the Countdown component.
 *
 * @since   1.0.0
 *
 * @param   array  $args  The args.
 *
 * @return  string        The HTML.
 */

function mm_countdown( $args ) {

	$component = 'mm-countdown';

	// Set our defaults and use them as needed.
	$defaults = array(
		'date'      => '',
		'time'      => '',
		'timezone' => '',
	);
	$args = wp_parse_args( (array)$args, $defaults );

	// Get clean param values.
	$date      = $args['date'];
	$time      = $args['time'];
	$timezone = $args['timezone'];

	// Enqueue pre-registerd 3rd party countdown script.
	wp_enqueue_script( 'mm-countdown' );

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );

	// Create new date object.
	$date_obj = new DateTime( $date . ' ' . $time . ' ' . $timezone );
	$utc_date_ojb  = new DateTime( $date . ' ' . $time );

	// Get timezone offset.
	$timezone_offset = $date_obj->getTimezone()->getOffset( $utc_date_ojb ) / 3600;

	// Pass date as comma-separated list.
	$year   = $date_obj->format( 'Y' );
	$month  = $date_obj->format( 'n' );
	$day    = $date_obj->format( 'j' );
	$hour   = $date_obj->format( 'G' );
	$minute = $date_obj->format( 'i' );
	$second = $date_obj->format( 's' );

	$output = sprintf( '<div class="%s" data-year="%s" data-month="%s" data-day="%s" data-hour="%s" data-minute="%s" data-second="%s" data-timezone-offset="%s"></div>',
		$mm_classes,
		$year,
		$month,
		$day,
		$hour,
		$minute,
		$second,
		$timezone_offset
	);

	return $output;
}

add_shortcode( 'mm_countdown', 'mm_countdown_shortcode' );
/**
 * Output Countdown.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_countdown_shortcode( $atts = array(), $content = null ) {

	if ( $content ) {
		$atts['content'] = $content;
	}

	return mm_countdown( $atts );
}


add_action( 'vc_before_init', 'mm_vc_countdown' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_countdown() {

	// Add a custom param for selecting the date
	vc_add_shortcode_param( 'date', 'mm_vc_date_param' );

	$timezone = mm_get_timezone_for_vc(  'mm-countdown' );

	vc_map( array(
		'name'     => __( 'Countdown', 'mm-components' ),
		'base'     => 'mm_countdown',
		'icon'     => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params'   => array(
			array(
				'type'        => 'date',
				'heading'     => __( 'Date', 'mm-components' ),
				'param_name'  => 'date',
				'admin_label' => true,
				'value'       => '',
				'description' => __( 'Must be in the format MM/DD/YYYY. Example: 12/25/2015 would be Christmas of 2015.', 'mm-components' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Time', 'mm-components' ),
				'param_name'  => 'time',
				'value'       => '',
				'description' => __( 'Must be in the format HH:MM:SS. Example: 18:30:00 would be 6:30 PM.', 'mm-components' ),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Time Zone', 'mm-components' ),
				'param_name' => 'timezone',
				'value'      => $timezone,
			),
		)
	) );
}

add_action( 'wp_enqueue_scripts', 'mm_countdown_enqueue_scripts' );
/**
 * Enqueue the countdown script.
 */
function mm_countdown_enqueue_scripts() {

	/**
	 * 3rd party countdown script.
	 *
	 * @see  http://hilios.github.io/jQuery.countdown/
	 */
	wp_register_script( 'mm-countdown', plugins_url( '/js/jquery.countdown.js', __FILE__ ), array( 'jquery' ), null, true );
}

add_action( 'widgets_init', 'mm_components_register_countdown_widget' );
/**
 * Register the widget.
 *
 * @since  1.0.0
 */
function mm_components_register_countdown_widget() {

	register_widget( 'mm_countdown_widget' );
}

/**
 * Countdown widget.
 *
 * @since  1.0.0
 */
class Mm_Countdown_Widget extends Mm_Components_Widget {

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
			'classname'   => 'mm-countdown-widget',
			'description' => __( 'A Countdown', 'mm-components' ),
		);

		parent::__construct(
			'mm_countdown_widget',
			__( 'Mm Countdown', 'mm-components' ),
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
			'date'     => '',
			'time'     => '',
			'timezone' => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		echo $args['before_widget'];

		echo mm_countdown( $instance );

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
			'date'     => '',
			'time'     => 'center center',
			'timezone' => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		$date      = $instance['date'];
		$time      = $instance['time'];
		$timezone  = $instance['timezone'];
		$classname = $this->options['classname'];

		// Date.
		$this->field_date(
			__( 'Date', 'mm-components' ),
			__( 'Must be in the format MM/DD/YYYY. Example: 12/25/2015 would be Christmas of 2015.', 'mm-components' ),
			$classname . '-date widefat',
			'date',
			$date
		);

		// Time.
		$this->field_text(
			__( 'Time', 'mm-components' ),
			__( 'Must be in the format HH:MM:SS. Example: 18:30:00 would be 6:30 PM.', 'mm-components' ),
			$classname . '-time widefat',
			'time',
			$time
		);

		// Timezone.
		$this->field_select(
			__( 'Timezone', 'mm-components' ),
			__( '', 'mm-components' ),
			$classname . '-timezone widefat',
			'timezone',
			$timezone,
			mm_get_timezone( 'mm-countdown' )
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
		$instance['date']     = sanitize_text_field( $new_instance['date'] );
		$instance['time']     = sanitize_text_field( $new_instance['time'] );
		$instance['timezone'] = sanitize_text_field( $new_instance['timezone'] );

		return $instance;
	}
}
