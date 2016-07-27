<?php
/**
 * Mm Components Functions.
 *
 * @since 1.0.0
 *
 * @package mm-components
 */

/**
 * Parse args with defaults, allowing for unexpected args.
 *
 * @since   1.0.0
 *
 * @param   array  $defaults  Default values.
 * @param   array  $atts      Args to be parsed.
 *
 * @return  array             Updated atts
 */
function mm_shortcode_atts( $defaults = array(), $atts = array() ) {
	return wp_parse_args( $atts, $defaults );
}

/**
 * Possibly wrap content in a link.
 *
 * @since   1.0.0
 *
 * @param   mixed   $content     Content to go in link.
 * @param   array   $link_array  Array of link data: url|title|target
 *
 * @return  string               HTML output.
 */
function mm_maybe_wrap_in_link( $content, $link_array = array() ) {

	if ( empty( $link_array['url'] ) ) {
		return $content;
	}

	return sprintf( '<a href="%s" title="%s" target="%s">%s</a>',
		$link_array['url'],
		$link_array['title'],
		$link_array['target'],
		$content
	);
}

/**
 * Return true or false based on the passed in value.
 *
 * @since   1.0.0
 *
 * @param   mixed  $value  The value to be tested.
 * @return  bool
 */
function mm_true_or_false( $value ) {

	if ( ! isset( $value ) ) {
		return false;
	}

	if ( true === $value || 'true' === $value || 1 === $value || '1' === $value || 'yes' === $value || 'on' === $value ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Check for a base64 encoded string.
 *
 * @since   1.0.0
 *
 * @return  bool
 */
function mm_is_base64( $string ) {

	$decoded = base64_decode( $string, true );

	// Check if there are invalid characters in the string.
	if ( ! preg_match( '/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string ) ) {
		return false;
	}

	// Decode the string in strict mode and check whether it is recognized as invalid.
	if ( ! base64_decode( $string, true ) ) {
		return false;
	}

	// Reencode and compare it to the original value.
	if ( base64_encode( $decoded ) != $string ) {
		return false;
	}

	return true;
};

/**
 * Check if a user has a specific role.
 *
 * @since   1.0.0
 *
 * @param   string  $role     The role we want to check.
 * @param   int     $user_id  The current user's ID.
 *
 * @return  bool
 */
function mm_check_user_role( $role, $user_id = null ) {

	if ( is_numeric( $user_id ) ) {
		$user = get_userdata( $user_id );
	} else {
		$user = wp_get_current_user();
	}

	if ( empty( $user ) ) {
		return false;
	}

	return in_array( $role, (array)$user->roles );
}

/**
 * Return an array of all public post types.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of formatted post types.
 */
function mm_get_post_types( $context = '' ) {

	$post_type_args = array(
		'public'   => true,
		'_builtin' => false
	);

	$custom_post_types = get_post_types( $post_type_args, 'objects', 'and' );

	$formatted_cpts = array();

	foreach( $custom_post_types as $post_type ) {
		$formatted_cpts[ $post_type->name ] = $post_type->labels->singular_name;
	}

	// Manually add 'post' and 'page' types.
	$default_post_types = array(
		'post' => __( 'Post', 'mm-components' ),
		'page' => __( 'Page', 'mm-components' ),
	);

	$post_types = $default_post_types + $formatted_cpts;

	return apply_filters( 'mm_post_types', $post_types, $context );
}

/**
 * Return an array of post types for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of formatted post types.
 */
function mm_get_post_types_for_vc( $context = '' ) {

	return array_flip( mm_get_post_types( $context ) );
}

/**
 * Return an array of registered taxonomies.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of formatted taxonomies.
 */
function mm_get_taxonomies( $context = '' ) {

	$taxonomy_args = array(
		'public'   => true,
		'_builtin' => false
	);

	$custom_taxonomies = get_taxonomies( $taxonomy_args, 'objects', 'and' );

	// Manually add 'category' and 'tag'.
	$taxonomies = array(
		'category' => __( 'Category', 'mm-components' ),
		'post_tag' => __( 'Tag', 'mm-components' ),
	);

	// Format the taxonomies.
	foreach ( $custom_taxonomies as $taxonomy ) {

		$taxonomies[ $taxonomy->name ] = $taxonomy->labels->singular_name;
	}

	return apply_filters( 'mm_taxonomies', $taxonomies, $context );
}

/**
 * Return an array of registered taxonomies for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of formatted taxonomies.
 */
function mm_get_taxonomies_for_vc( $context = '' ) {

	// Add an empty first option.
	$empty_option = array(
		__( 'Select a Taxonomy', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_taxonomies( $context ) );
}

/**
 * Return an array of post/page titles for use in a Visual Composer autocomplete param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of formatted titles.
 */
function mm_get_post_titles_for_vc( $context = '' ) {

	// Grab the posts.
	$posts = get_posts(
		array(
			'posts_per_page' => -1,
			'post_type'      => mm_get_post_types( 'mm-components' ),
		)
	);

	$post_titles = array();

	foreach ( $posts as $post )	{

		$post_label = sprintf(
			'<strong>%s</strong> %s' ,
			ucwords( $post->post_type ),
			$post->post_title
		);

		$post_titles[] = array(
			'value' => $post->ID,
			'label' => $post_label,
		);
	}

	return apply_filters( 'mm_post_titles', $post_titles, $context );
}

/**
 * Return an array of query types for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of formatted query types.
 */
function mm_get_query_types_for_vc( $context = '' ) {

	$query_types = array(
		'Collection' => 'collection',
		'Specific'   => 'specific',
	);

	return apply_filters( 'mm_query_types', $query_types, $context );
}

/**
 * Return an array of registered image sizes.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of formatted image sizes.
 */
function mm_get_image_sizes( $context = '' ) {

	$image_sizes = get_intermediate_image_sizes();
	$formatted_image_sizes = array();

	foreach ( $image_sizes as $image_size ) {

		$formatted_image_size = ucwords( str_replace( '_', ' ', str_replace( '-', ' ', $image_size ) ) );
		$formatted_image_sizes[ $image_size ] = $formatted_image_size;
	}

	// Manually add in the 'Full' size.
	$formatted_image_sizes['full'] = __( 'Full', 'mm-components' );

	return apply_filters( 'mm_image_sizes', $formatted_image_sizes, $context );
}

/**
 * Return an array of registered image sizes for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of formatted image sizes.
 */
function mm_get_image_sizes_for_vc( $context = '' ) {

	// Add an empty first option.
	$empty_option = array(
		__( 'Default', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_image_sizes( $context ) );
}

/**
 * Return custom VC image upload description.
 *
 * @since   1.0.0
 *
 * @param   string  $image_size  Image size slug.
 *
 * @return  string               Image upload description.
 */
function mm_custom_image_field_description( $image_size = '' ) {

	$default_message = __( 'Upload an image that is large enough to be output without stretching.', 'mm-components' );

	// Do default image message if no specific image_size is passed.
	if ( ! $image_size ) {
		return $default_message;
	}

	// Get dimensions of image.
	$image_dimensions = mm_get_image_size_dimensions( $image_size );

	// Do default message if the specified image size doesn't exists.
	if ( ! $image_dimensions ) {
		return $default_message;
	}

	$width = $image_dimensions['width'] * 2;
	$height = $image_dimensions['height'] * 2;

	return sprintf( __( 'Upload an image that is at least <b>%dpx</b> × <b>%dpx</b> to ensure that it is not stretched.', 'mm-components' ), $width, $height );
}

/**
 * Get the dimensions of WP default and add-on image sizes.
 *
 * @since   1.0.0
 *
 * @param   string  $image_size  Image size slug.
 *
 * @return  array                Array of image width/height.
 */
function mm_get_image_size_dimensions( $image_size = '' ) {

	global $_wp_additional_image_sizes;

	if ( in_array( $image_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

		$image_dimensions['width'] = get_option( $image_size . '_size_w' );
		$image_dimensions['height'] = get_option( $image_size . '_size_h' );

	} elseif ( isset( $_wp_additional_image_sizes[ $image_size ] ) ) {

		$image_dimensions = array(
			'width' => $_wp_additional_image_sizes[ $image_size ]['width'],
			'height' => $_wp_additional_image_sizes[ $image_size ]['height'],
		);

	} else {
		return false;
	}

	return $image_dimensions;
}

/**
 * Return an array of Mm Posts templates.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of template names.
 */
function mm_get_mm_posts_templates( $context = '' ) {

	// All core and custom templates should be registered using this filter.
	return apply_filters( 'mm_posts_templates', array(), $context );
}

/**
 * Return an array of Mm Posts templates for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of template names.
 */
function mm_get_mm_posts_templates_for_vc( $context = '' ) {

	// Add an empty first option.
	$empty_option = array(
		__( 'Default', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_mm_posts_templates( $context ) );
}
/**
 * Return an array of Mm Blockquote templates.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of template names.
 */
function mm_get_mm_blockquote_templates( $context = '' ) {

	$templates = array(
		''           => __( 'Default', 'mm-components' ),
		'image-left' => __( 'Image Left', 'mm-components' ),
	);

	return $templates + apply_filters( 'mm_blockquote_templates', array(), $context );
}

/**
 * Return an array of Mm Blockquote templates for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of template names.
 */
function mm_get_mm_blockquote_templates_for_vc( $context = '' ) {

	return array_flip( mm_get_mm_blockquote_templates( $context ) );
}

/**
 * Return an array of Mm Users templates.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of template names.
 */
function mm_get_mm_users_templates( $context = '' ) {

	// All core and custom templates should be registered using this filter.
	return apply_filters( 'mm_users_templates', array(), $context );
}

/**
 * Return an array of Mm Users templates for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of template names.
 */
function mm_get_mm_users_templates_for_vc( $context = '' ) {

	// Add an empty first option.
	$empty_option = array(
		__( 'Default', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_mm_users_templates( $context ) );
}

/**
 * Return an array of HTML wrap elements.
 *
 * @since  1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array  The array of wrap elements.
 */
function mm_get_wrap_elements( $context = '' ) {

	$wrap_elements = array(
		'article' => 'article',
		'div'     => 'div',
		'ul'      => 'ul',
		'ol'      => 'ol',
		'span'    => 'span',
	);

	return apply_filters( 'mm_components_wrap_elements', $wrap_elements, $context );
}

/**
 * Return an array of HTML wrap elements for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of wrap elements.
 */
function mm_get_wrap_elements_for_vc( $context = '' ) {

	// Add an empty first option.
	$empty_option = array(
		__( 'Default', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_wrap_elements( $context ) );
}

/**
 * Return an array of icon styles.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of icon styles.
 */
function mm_get_icon_styles( $context = '' ) {

	// Add an empty first option.
	$icon_style = array(
		''               => __( 'Icon Only', 'mm-components' ),
		'circle'         => __( 'Circle', 'mm-components' ),
		'square'         =>	__( 'Square', 'mm-components' ),
		'rounded-square' =>	__( 'Rounded Square', 'mm-components' ),
	);

	return apply_filters( 'mm_get_icon_styles', $icon_style, $context );
}

/**
 * Return an array of icon styles for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of social networks.
 */
function mm_get_icon_styles_for_vc( $context = '' ) {

	return array_flip( mm_get_icon_styles( $context ) );
}

/**
 * Return an array of Mm Social Icons types for use in Visual Composer.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of type names.
 */
function mm_get_social_icons_types_for_vc( $context = '' ) {

	return apply_filters( 'mm_social_icons_types', array(), $context );
}

/**
 * Return an array of Mm Social Icons types.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of type names.
 */
function mm_get_social_icons_types( $context = '' ) {

	return array_flip( mm_get_social_icons_types_for_vc( $context ) );
}

/**
 * Return an array of registered social networks.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of social networks.
 */
function mm_get_social_networks( $context = '' ) {

	$social_networks = array(
		'facebook'  => __( 'Facebook', 'mm-components' ),
		'twitter'   => __( 'Twitter', 'mm-components' ),
		'instagram' => __( 'Instagram', 'mm-components' ),
		'pinterest' => __( 'Pinterest', 'mm-components' ),
		'youtube'   => __( 'Youtube', 'mm-components' ),
	);

	return apply_filters( 'mm_social_networks', $social_networks, $context );
}

/**
 * Return an array of registered social networks for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of social networks.
 */
function mm_get_social_networks_for_vc( $context = '' ) {

	return array_flip( mm_get_social_networks( $context ) );
}

/**
 * Return an array of image card styles.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of image card styles.
 */
function mm_get_image_card_styles( $context = '' ) {

	// Add an empty first option.
	$image_card_styles = array(
		'button-bottom' => __( 'Button Bottom', 'mm-components' ),
		'text-inside'   => __( 'Text Inside', 'mm-components' ),
	);

	return apply_filters( 'mm_get_image_card_styles', $image_card_styles, $context );
}

/**
 * Return an array of image card styles for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of image card styles.
 */
function mm_get_image_card_styles_for_vc( $context = '' ) {

	return array_flip( mm_get_image_card_styles( $context ) );
}

/**
 * Return an array of timezones.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of timezones.
 */
function mm_get_timezones( $context = '' ) {

	$timezones = array(
		'GMT-1200' => __( '(GMT -12:00) Eniwetok, Kwajalein', 'mm-components' ),
		'GMT-1100' => __( '(GMT -11:00) Midway Island, Samoa', 'mm-components' ),
		'GMT-1000' => __( '(GMT -10:00) Hawaii', 'mm-components' ),
		'GMT-0900' => __( '(GMT -9:00) Alaska', 'mm-components' ),
		'GMT-0800' => __( '(GMT -8:00) Pacific Time (US & Canada)', 'mm-components' ),
		'GMT-0700' => __( '(GMT -7:00) Mountain Time (US & Canada)', 'mm-components' ),
		'GMT-0600' => __( '(GMT -6:00) Central Time (US & Canada), Mexico City', 'mm-components' ),
		'GMT-0500' => __( '(GMT -5:00) Eastern Time (US & Canada), Bogota, Lima', 'mm-components' ),
		'GMT-0430' => __( '(GMT -4:30) Caracas', 'mm-components' ),
		'GMT-0400' => __( '(GMT -4:00) Atlantic Time (Canada), La Paz, Santiago', 'mm-components' ),
		'GMT-0330' => __( '(GMT -3:30) Newfoundland', 'mm-components' ),
		'GMT-0300' => __( '(GMT -3:00) Brazil, Buenos Aires, Georgetown', 'mm-components' ),
		'GMT-0200' => __( '(GMT -2:00) Mid-Atlantic', 'mm-components' ),
		'GMT-0100' => __( '(GMT -1:00 hour) Azores, Cape Verde Islands', 'mm-components' ),
		'GMT'      => __( '(GMT) Western Europe Time, London, Lisbon, Casablanca, Greenwich', 'mm-components' ),
		'GMT+0100' => __( '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris', 'mm-components' ),
		'GMT+0200' => __( '(GMT +2:00) Kaliningrad, South Africa, Cairo', 'mm-components' ),
		'GMT+0300' => __( '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg', 'mm-components' ),
		'GMT+0330' => __( '(GMT +3:30) Tehran', 'mm-components' ),
		'GMT+0400' => __( '(GMT +4:00) Abu Dhabi, Muscat, Yerevan, Baku, Tbilisi', 'mm-components' ),
		'GMT+0430' => __( '(GMT +4:30) Kabul', 'mm-components' ),
		'GMT+0500' => __( '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent', 'mm-components' ),
		'GMT+0530' => __( '(GMT +5:30) Mumbai, Kolkata, Chennai, New Delhi', 'mm-components' ),
		'GMT+0545' => __( '(GMT +5:45) Kathmandu', 'mm-components' ),
		'GMT+0600' => __( '(GMT +6:00) Almaty, Dhaka, Colombo', 'mm-components' ),
		'GMT+0630' => __( '(GMT +6:30) Yangon, Cocos Islands', 'mm-components' ),
		'GMT+0700' => __( '(GMT +7:00) Bangkok, Hanoi, Jakarta', 'mm-components' ),
		'GMT+0800' => __( '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong', 'mm-components' ),
		'GMT+0900' => __( '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk', 'mm-components' ),
		'GMT+0930' => __( '(GMT +9:30) Adelaide, Darwin', 'mm-components' ),
		'GMT+1000' => __( '(GMT +10:00) Eastern Australia, Guam, Vladivostok', 'mm-components' ),
		'GMT+1100' => __( '(GMT +11:00) Magadan, Solomon Islands, New Caledonia', 'mm-components' ),
		'GMT+1200' => __( '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka', 'mm-components' ),
	);

	return apply_filters( 'mm_timezones', $timezones, $context );
}

/**
 * Return an array of timezones for use in a visual composer element.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of timezones.
 */
function mm_get_timezones_for_vc( $context = '' ) {

	$empty_option = array(
		__( 'Select A Timezone', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_timezones( $context ) );
}

/**
 * Return an array of registered user roles.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of user roles.
 */
function mm_get_user_roles( $context = '' ) {

	global $wp_roles;

	$user_roles = array();

	foreach ( $wp_roles->roles as $role => $role_params ) {

		$role_name = ( isset( $role_params['name'] ) ) ? $role_params['name'] : $role;

		$user_roles[ $role ] = $role_name;
	}

	return apply_filters( 'mm_user_roles', $user_roles, $context );
}

/**
 * Return an array of registered user roles for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of user roles.
 */
function mm_get_user_roles_for_vc( $context = '' ) {

	return array_flip( mm_get_user_roles( $context ) );
}

/**
 * Return an array of registered color names.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of colors.
 */
function mm_get_colors( $context = '' ) {

	$colors = array(
		'default' => __( 'Default', 'mm-components' ),
		'light'   => __( 'Light', 'mm-components' ),
		'medium'  => __( 'Medium', 'mm-components' ),
		'dark'    => __( 'Dark', 'mm-components' ),
	);

	return apply_filters( 'mm_colors', $colors, $context );
}

/**
 * Return an array of color names for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of colors.
 */
function mm_get_colors_for_vc( $context = '' ) {

	return array_flip( mm_get_colors( $context ) );
}

/**
 * Return an array of background position values.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of background position values.
 */
function mm_get_background_position( $context = '' ) {

	$position = array(
		'center center' => __( 'Center Center', 'mm-components' ),
		'center top'    => __( 'Center Top', 'mm-components' ),
		'center bottom' => __( 'Center Bottom', 'mm-components' ),
		'left center'   => __( 'Left Center', 'mm-components' ),
		'left top'      => __( 'Left Top', 'mm-components' ),
		'left bottom'   => __( 'Left Bottom', 'mm-components' ),
		'right center'  => __( 'Right Center', 'mm-components' ),
		'right top'     => __( 'Right Top', 'mm-components' ),
		'right bottom'  => __( 'Right Bottom', 'mm-components' ),
	);

	return apply_filters( 'mm_background_position', $position, $context );
}

/**
 * Return an array of background position for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of colors.
 */
function mm_get_background_position_for_vc( $context = '' ) {

	return array_flip( mm_get_background_position( $context ) );
}

/**
 * Return an array of slider types.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of slider types.
 */
function mm_get_slider_types( $context = '' ) {

	$types = array(
		'content-as-slides' => __( 'Content As Slides', 'mm-components' ),
		'static-content'    => __( 'Static Content', 'mm-components' ),
	);

	return apply_filters( 'mm_slider_types', $types, $context );
}

/**
 * Return an array of slider types for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of slide types.
 */
function mm_get_slider_types_for_vc( $context = '' ) {

	return array_flip( mm_get_slider_types( $context ) );
}

/**
 * Return an array of slider sizing options.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of slider size values.
 */
function mm_get_slider_heights( $context = '' ) {

	$heights = array(
		'custom-height'   => __( 'Custom Height', 'mm-components' ),
		'full-height'     => __( 'Full Height', 'mm-components' ),
		'adaptive-height' => __( 'Adaptive Height', 'mm-components' ),
	);

	return apply_filters( 'mm_slider_height', $heights, $context );
}

/**
 * Return an array of slider sizes for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of sizes.
 */
function mm_get_slider_heights_for_vc( $context = '' ) {

	return array_flip( mm_get_slider_heights( $context ) );
}

/**
 * Return an array of registered overlay color names.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of overlay colors.
 */
function mm_get_overlay_colors( $context = '' ) {

	$colors = array(
		''      => __( 'None', 'mm-components' ),
		'white' => __( 'White', 'mm-components' ),
		'black' => __( 'Black', 'mm-components' ),
	);

	return apply_filters( 'mm_overlay_colors', $colors, $context );
}

/**
 * Return an array of overlay color names for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of overlay colors.
 */
function mm_get_overlay_colors_for_vc( $context = '' ) {

	return array_flip( mm_get_overlay_colors( $context ) );
}

/**
 * Return an array of overlay opacity values.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of overlay values.
 */
function mm_get_overlay_opacity_values( $context = '' ) {

	$values = array(
		'0.1' => __( '0.1', 'mm-components' ),
		'0.2' => __( '0.2', 'mm-components' ),
		'0.3' => __( '0.3', 'mm-components' ),
		'0.4' => __( '0.4', 'mm-components' ),
		'0.5' => __( '0.5', 'mm-components' ),
		'0.6' => __( '0.6', 'mm-components' ),
		'0.7' => __( '0.7', 'mm-components' ),
		'0.8' => __( '0.8', 'mm-components' ),
		'0.9' => __( '0.9', 'mm-components' ),
		'1'   => __( '1', 'mm-components' ),
	);

	return apply_filters( 'mm_overlay_opacity_values', $values, $context );
}

/**
 * Return an array of overlay opacity values for visual composer.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array   The array of overlay values.
 */
function mm_get_overlay_opacity_values_for_vc( $context = '' ) {

	return array_flip( mm_get_overlay_opacity_values( $context ) );
}

/**
 * Return an array of wrapper elements.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of heading levels.
 */
function mm_get_wrapper_elements( $context = '' ) {

	$wrapper_elements = mm_get_heading_levels( $context );

	$wrapper_elements['p'] = __( 'p', 'mm-components' );

	return apply_filters( 'mm_wrapper_elements', $wrapper_elements, $context );
}

/**
 * Return an array of heading levels for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of heading levels.
 */
function mm_get_wrapper_elements_for_vc( $context = '' ) {

	$empty_option = array(
		__( 'Select an element', 'mm-components' ),
	);

	return $empty_option + array_flip( mm_get_wrapper_elements( $context ) );
}

/**
 * Return an array of heading levels.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of heading levels.
 */
function mm_get_heading_levels( $context = '' ) {

	$heading_levels = array(
		'h1' => __( 'h1', 'mm-components' ),
		'h2' => __( 'h2', 'mm-components' ),
		'h3' => __( 'h3', 'mm-components' ),
		'h4' => __( 'h4', 'mm-components' ),
		'h5' => __( 'h5', 'mm-components' ),
		'h6' => __( 'h6', 'mm-components' ),
	);

	return apply_filters( 'mm_heading_levels', $heading_levels, $context );
}

/**
 * Return an array of heading levels for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of heading levels.
 */
function mm_get_heading_levels_for_vc( $context = '' ) {

	$empty_option = array(
		__( 'Select a heading level', 'mm-components' ),
	);

	return $empty_option + array_flip( mm_get_heading_levels( $context ) );
}

/**
 * Return an array of text alignment options.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of text alignment options.
 */
function mm_get_text_alignment( $context = '' ) {

	$text_alignment = array(
		'default' => __( 'Default', 'mm-components' ),
		'left'    => __( 'Left', 'mm-components' ),
		'center'  => __( 'Center', 'mm-components' ),
		'right'   => __( 'Right', 'mm-components' ),
	);

	return apply_filters( 'mm_text_alignment', $text_alignment, $context );
}

/**
 * Return an array of text alignments for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of text alignment options.
 */
function mm_get_text_alignment_for_vc( $context = '' ) {

	return array_flip( mm_get_text_alignment( $context ) );
}

/**
 * Return an array of available typeface choices.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of fonts.
 */
function mm_get_fonts( $context = '' ) {

	$fonts = array(
		'default' => __( 'Default', 'mm-components' ),
	);

	return apply_filters( 'mm_fonts', $fonts, $context );
}

/**
 * Return an array of available typeface choices for VC.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of fonts.
 */
function mm_get_fonts_for_vc( $context = '' ) {

	return array_flip( mm_get_fonts( $context ) );
}

/**
 * Return an array of font weights.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of font weights.
 */
function mm_get_font_weights( $context = '' ) {

	$font_weights = array(
		'normal'    => __( 'Normal', 'mm-components' ),
		'light'     => __( 'Light', 'mm-components' ),
		'semi-bold' => __( 'Semi-bold', 'mm-components' ),
		'bold'      => __( 'Bold', 'mm-components' ),
	);

	return apply_filters( 'mm_font_weights', $font_weights, $context );
}

/**
 * Return an array of font weights for VC.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of font weights.
 */
function mm_get_font_weights_for_vc( $context = '' ) {

	$empty_option = array(
		__( 'Default' ) => '',
	);

	return $empty_option + array_flip( mm_get_font_weights( $context ) );
}

/**
 * Return an array of button style options.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of button style options.
 */
function mm_get_button_styles( $context = '' ) {

	$button_style = array(
		'default'        => __( 'Default', 'mm-components' ),
		'ghost'          => __( 'Ghost', 'mm-components' ),
		'solid-to-ghost' => __( 'Solid to Ghost', 'mm-components' ),
		'three-d'        => __( '3D', 'mm-components' ),
		'gradient'       => __( 'Gradient', 'mm-components' ),
	);

	return apply_filters( 'mm_button_styles', $button_style, $context );
}

/**
 * Return an array of button style options for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of text alignment options.
 */
function mm_get_button_styles_for_vc( $context = '' ) {

	return array_flip( mm_get_button_styles( $context ) );
}

/**
 * Return an array of button border weight options.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of button border weight options.
 */
function mm_get_button_border_weights( $context = '' ) {

	$button_border_weights = array(
		'default' => __( 'Default', 'mm-components' ),
		'thin'    => __( 'Thin', 'mm-components' ),
		'thick'   => __( 'Thick', 'mm-components' ),
	);

	return apply_filters( 'mm_button_border_weights', $button_border_weights, $context );
}

/**
 * Return an array of button border weight options for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of button border weight options.
 */
function mm_get_button_border_weights_for_vc( $context = '' ) {

	return array_flip( mm_get_button_border_weights( $context ) );
}

/**
 * Return an array of button corner style options.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of button corner style options.
 */
function mm_get_button_corner_styles( $context = '' ) {

	$button_corner_styles = array(
		'pointed' => __( 'Pointed', 'mm-components' ),
		'rounded' => __( 'Rounded', 'mm-components' ),
		'pill'    => __( 'Pill', 'mm-components' ),
	);

	return apply_filters( 'mm_button_corner_styles', $button_corner_styles, $context );
}

/**
 * Return an array of button corner style options for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of button corner style options.
 */
function mm_get_button_corner_styles_for_vc( $context = '' ) {

	return array_flip( mm_get_button_corner_styles( $context ) );
}

/**
 * Return an array of link targets.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of link targets.
 */
function mm_get_link_targets( $context = '' ) {

	$link_targets = array(
		'_self'   => __( 'Same window', 'mm-components' ),
		'_blank'  => __( 'New window', 'mm-components' ),
		'_parent' => __( 'Parent window', 'mm-components' ),
		'_top'    => __( 'Top window', 'mm-components' ),
	);

	return apply_filters( 'mm_link_targets', $link_targets, $context );
}

/**
 * Return an array of link targets for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  $context  The context to pass to our filter.
 *
 * @return  array             The array of link targets.
 */
function mm_get_link_targets_for_vc( $context = '' ) {

	return array_flip( mm_get_link_targets( $context ) );
}

/**
 * Output an opening <table> element.
 *
 * @since  1.0.0
 *
 * @param  string  $classes  The classes for the table.
 */
function mm_output_table_element_open( $classes = '' ) {

	if ( '' !== $classes ) {
		printf(
			'<table class="%s">',
			esc_attr( $classes )
		);
	} else {
		echo '<table>';
	}
}

/**
 * Output a closing </table> element.
 *
 * @since  1.0.0
 */
function mm_output_table_element_close() {

	echo '</table>';
}

/**
 * Output an opening <thead> element.
 *
 * @since  1.0.0
 */
function mm_output_thead_element_open() {

	echo '<thead>';
}

/**
 * Output a closing </thead> element.
 *
 * @since  1.0.0
 */
function mm_output_thead_element_close() {

	echo '</thead>';
}

/**
 * Output an opening <tbody> element.
 *
 * @since  1.0.0
 */
function mm_output_tbody_element_open() {

	echo '<tbody>';
}

/**
 * Output a closing </tbody> element.
 *
 * @since  1.0.0
 */
function mm_output_tbody_element_close() {

	echo '</tbody>';
}
