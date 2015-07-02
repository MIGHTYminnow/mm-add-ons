<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Countdown
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

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

add_shortcode( 'countdown', 'mm_countdown_shortcode' );
/**
 * Output Countdown.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_countdown_shortcode( $atts, $content = null, $tag ) {

	extract( shortcode_atts( array(
		'date' => '',
		'time' => '',
		'timezone' => ''
	), $atts ) );

	// Enqueue pre-registerd 3rd party countdown script.
	wp_enqueue_script( 'mm-countdown' );

	// Get Mm classes
	$mm_classes = str_replace( '_', '-', $tag );
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );

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

	ob_start();

	printf( '<div class="%s" data-year="%s" data-month="%s" data-day="%s" data-hour="%s" data-minute="%s" data-second="%s" data-timezone-offset="%s"></div>',
		$mm_classes,
		$year,
		$month,
		$day,
		$hour,
		$minute,
		$second,
		$timezone_offset
	);

	$output = ob_get_clean();

	return $output;
}

add_action( 'vc_before_init', 'mm_vc_countdown' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_countdown() {

	// Add a custom param for selecting the date
	add_shortcode_param( 'date', 'mm_date_param' );

	// Add a custom param for selecting the TimeZone
	add_shortcode_param( 'timezone', 'mm_timezone_param' );

	vc_map( array(
		'name' => __( 'Countdown', 'mm-add-ons' ),
		'base' => 'countdown',
		'class' => '',
		'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-add-ons' ),
		'params' => array(
			array(
				'type' => 'date',
				'class' => '',
				'heading' => __( 'Date', 'mm-add-ons' ),
				'param_name' => 'date',
				'admin_label' => true,
				'value' => '',
				'description' => __( 'Must be in the format MM/DD/YYYY. Example: 12/25/2015 would be Christmas of 2015.', 'mm-add-ons' ),
				),
			array(
				'type' => 'textfield',
				'class' => '',
				'heading' => __( 'Time', 'mm-add-ons' ),
				'param_name' => 'time',
				'value' => '',
				'description' => __( 'Must be in the format HH:MM:SS. Example: 18:30:00 would be 6:30 PM.', 'mm-add-ons' ),
				),
			array(
				'type' => 'timezone',
				'class' => '',
				'heading' => __( 'Time Zone', 'mm-add-ons' ),
				'param_name' => 'timezone',
				'value' => '',
				),
			)
	) );
}

/**
 * Visual Composer Custom Date Param.
 *
 * @since  1.0.0
 */
function mm_date_param( $settings, $value ) {

	$output = sprintf( '<input type="date" class="countdown-date wpb_vc_param_value" value="%s" name="%s" />',
		$value,
		esc_attr( $settings['param_name'] )
	);

	return $output;
}

/**
 * Visual Composer Custom Timezone Param.
 *
 * @since  1.0.0
 */
function mm_timezone_param( $settings, $value ) {

	$output = '<select name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value">
	<option value="GMT-1200" ' . selected( $value, 'GMT-1200' ) . '>(GMT -12:00) Eniwetok, Kwajalein</option>
	<option value="GMT-1100" ' . selected( $value, 'GMT-1100' ) . '>(GMT -11:00) Midway Island, Samoa</option>
	<option value="GMT-1000" ' . selected( $value, 'GMT-1000' ) . '>(GMT -10:00) Hawaii</option>
	<option value="GMT-0900" ' . selected( $value, 'GMT-0900' ) . '>(GMT -9:00) Alaska</option>
	<option value="GMT-0800" ' . selected( $value, 'GMT-0800' ) . '>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
	<option value="GMT-0700" ' . selected( $value, 'GMT-0700' ) . '>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
	<option value="GMT-0600" ' . selected( $value, 'GMT-0600' ) . '>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
	<option value="GMT-0500" ' . selected( $value, 'GMT-0500' ) . '>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
	<option value="GMT-0430" ' . selected( $value, 'GMT-0430' ) . '>(GMT -4:30) Caracas</option>
	<option value="GMT-0400" ' . selected( $value, 'GMT-0400' ) . '>(GMT -4:00) Atlantic Time (Canada), La Paz, Santiago</option>
	<option value="GMT-0330" ' . selected( $value, 'GMT-0330' ) . '>(GMT -3:30) Newfoundland</option>
	<option value="GMT-0300" ' . selected( $value, 'GMT-0300' ) . '>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
	<option value="GMT-0200" ' . selected( $value, 'GMT-0200' ) . '>(GMT -2:00) Mid-Atlantic</option>
	<option value="GMT-0100" ' . selected( $value, 'GMT-0100' ) . '>(GMT -1:00 hour) Azores, Cape Verde Islands</option>
	<option value="GMT" ' . selected( $value, 'GMT' ) . '>(GMT) Western Europe Time, London, Lisbon, Casablanca, Greenwich</option>
	<option value="GMT+0100" ' . selected( $value, 'GMT+0100' ) . '>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
	<option value="GMT+0200" ' . selected( $value, 'GMT+0200' ) . '>(GMT +2:00) Kaliningrad, South Africa, Cairo</option>
	<option value="GMT+0300" ' . selected( $value, 'GMT+0300' ) . '>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
	<option value="GMT+0330" ' . selected( $value, 'GMT+0330' ) . '>(GMT +3:30) Tehran</option>
	<option value="GMT+0400" ' . selected( $value, 'GMT+0400' ) . '>(GMT +4:00) Abu Dhabi, Muscat, Yerevan, Baku, Tbilisi</option>
	<option value="GMT+0430" ' . selected( $value, 'GMT+0430' ) . '>(GMT +4:30) Kabul</option>
	<option value="GMT+0500" ' . selected( $value, 'GMT+0500' ) . '>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
	<option value="GMT+0530" ' . selected( $value, 'GMT+0530' ) . '>(GMT +5:30) Mumbai, Kolkata, Chennai, New Delhi</option>
	<option value="GMT+0545" ' . selected( $value, 'GMT+0545' ) . '>(GMT +5:45) Kathmandu</option>
	<option value="GMT+0600" ' . selected( $value, 'GMT+0600' ) . '>(GMT +6:00) Almaty, Dhaka, Colombo</option>
	<option value="GMT+0630" ' . selected( $value, 'GMT+0630' ) . '>(GMT +6:30) Yangon, Cocos Islands</option>
	<option value="GMT+0700" ' . selected( $value, 'GMT+0700' ) . '>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
	<option value="GMT+0800" ' . selected( $value, 'GMT+0800' ) . '>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
	<option value="GMT+0900" ' . selected( $value, 'GMT+0900' ) . '>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
	<option value="GMT+0930" ' . selected( $value, 'GMT+0930' ) . '>(GMT +9:30) Adelaide, Darwin</option>
	<option value="GMT+1000" ' . selected( $value, 'GMT+1000' ) . '>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
	<option value="GMT+1100" ' . selected( $value, 'GMT+1100' ) . '>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
	<option value="GMT+1200" ' . selected( $value, 'GMT+1200' ) . '>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
	</select>';

	return $output;
}

