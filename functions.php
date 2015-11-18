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
 * Checks for a base64 encoded string.
 *
 * @since   1.0.0
 *
 * @return  bool  Returns true or false.
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
 * @since  1.0.0
 *
 * @param  string  $role     The role we want to check.
 * @param  int     $user_id  The current user's ID.
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
 * @return  array  The array of formatted post types.
 */
function mm_get_post_types() {

	$post_type_args = array(
		'public' => true,
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

	return array_merge( $default_post_types, $formatted_cpts );
}

/**
 * Return an array of post types for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of formatted post types.
 */
function mm_get_post_types_for_vc() {

	return array_flip( mm_get_post_types() );
}

/**
 * Return an array of registered taxonomies.
 *
 * @since   1.0.0
 *
 * @return  array  The array of formatted taxonomies.
 */
function mm_get_taxonomies() {

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

	return $taxonomies;
}

/**
 * Return an array of registered taxonomies for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of formatted taxonomies.
 */
function mm_get_taxonomies_for_vc() {

	// Add an empty first option.
	$empty_option = array(
		__( 'Select a Taxonomy', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_taxonomies() );
}

/**
 * Return an array of registered image sizes.
 *
 * @since   1.0.0
 *
 * @return  array  The array of formatted image sizes.
 */
function mm_get_image_sizes() {

	$image_sizes = get_intermediate_image_sizes();
	$formatted_image_sizes = array();

	foreach ( $image_sizes as $image_size ) {

		$formatted_image_size = ucwords( str_replace( '_', ' ', str_replace( '-', ' ', $image_size ) ) );
		$formatted_image_sizes[ $image_size ] = $formatted_image_size;
	}

	// Manually add in the 'Full' size.
	$formatted_image_sizes['full'] = __( 'Full', 'mm-components' );

	return $formatted_image_sizes;
}

/**
 * Return an array of registered image sizes for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of formatted image sizes.
 */
function mm_get_image_sizes_for_vc() {

	// Add an empty first option.
	$empty_option = array(
		__( 'Default', 'mm-components' ) => '',
	);

	return $empty_option + mm_get_image_sizes();
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
 * @return  array  The array of template names.
 */
function mm_get_mm_posts_templates() {

	// All core and custom templates should be registered using this filter.
	return apply_filters( 'mm_posts_templates', array() );
}

/**
 * Return an array of Mm Posts templates for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of template names.
 */
function mm_get_mm_posts_templates_for_vc() {

	// Add an empty first option.
	$empty_option = array(
		__( 'Default', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_mm_posts_templates() );
}

/**
 * Return an array of Mm Users templates.
 *
 * @since   1.0.0
 *
 * @return  array  The array of template names.
 */
function mm_get_mm_users_templates() {

	// All core and custom templates should be registered using this filter.
	return apply_filters( 'mm_users_templates', array() );
}

/**
 * Return an array of Mm Users templates for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of template names.
 */
function mm_get_mm_users_templates_for_vc() {

	// Add an empty first option.
	$empty_option = array(
		__( 'Default', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_mm_users_templates() );
}

/**
 * Return an array of HTML wrap elements.
 *
 * @since  1.0.0
 *
 * @return  array  The array of wrap elements.
 */
function mm_get_wrap_elements() {

	$wrap_elements = array(
		'article' => 'article',
		'div'     => 'div',
		'ul'      => 'ul',
		'ol'      => 'ol',
		'span'    => 'span',
	);

	return apply_filters( 'mm_components_wrap_elements', $wrap_elements );
}

/**
 * Return an array of HTML wrap elements for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of wrap elements.
 */
function mm_get_wrap_elements_for_vc() {

	// Add an empty first option.
	$empty_option = array(
		__( 'Default', 'mm-components' ) => '',
	);

	return $empty_option + array_flip( mm_get_wrap_elements() );
}

/**
 * Return an array of Mm Social Icons types.
 *
 * @since   1.0.0
 *
 * @return  array  The array of type names.
 */
function mm_get_mm_social_icons_types() {

	return apply_filters( 'mm_social_icons_types', array() );
}

/**
 * Return an array of registered social networks.
 *
 * @since   1.0.0
 *
 * @return  array  The array of social networks.
 */
function mm_get_social_networks() {

	$social_networks = array(
		'facebook'  => __( 'Facebook', 'mm-components' ),
		'twitter'   => __( 'Twitter', 'mm-components' ),
		'instagram' => __( 'Instagram', 'mm-components' ),
		'pinterest' => __( 'Pinterest', 'mm-components' ),
		'youtube'   => __( 'Youtube', 'mm-components' ),
	);

	return apply_filters( 'mm_social_networks', $social_networks );
}

/**
 * Return an array of registered social networks for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of social networks.
 */
function mm_get_social_networks_for_vc() {

	return array_flip( mm_get_social_networks() );
}

/**
 * Return an array of registered user roles.
 *
 * @since   1.0.0
 *
 * @return  array  The array of user roles.
 */
function mm_get_user_roles() {

	global $wp_roles;

	$user_roles = array();

	foreach ( $wp_roles->roles as $role => $role_params ) {

		$role_name = ( isset( $role_params['name'] ) ) ? $role_params['name'] : $role;

		$user_roles[ $role ] = $role_name;
	}

	return $user_roles;
}

/**
 * Return an array of registered user roles for use in a Visual Composer param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of user roles.
 */
function mm_get_user_roles_for_vc() {

	return array_flip( mm_get_user_roles() );
}

/**
 * Return an array of registered color names.
 *
 * @since   1.0.0
 *
 * @return  array  The array of colors.
 */
function mm_get_available_colors() {

	$colors = array(
		'default' => __( 'Default', 'mm-components' ),
		'light'   => __( 'Light', 'mm-components' ),
		'medium'  => __( 'Medium', 'mm-components' ),
		'dark'    => __( 'Dark', 'mm-components' ),
	);

	return apply_filters( 'mm_get_available_colors', $colors );
}

/**
 * Return an array of color names for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of colors.
 */
function mm_get_available_colors_for_vc() {

	return array_flip( mm_get_available_colors() );
}

/**
 * Return an array of registered overlay color names.
 *
 * @since   1.0.0
 *
 * @param   string  The component calling this function.
 *
 * @return  array   The array of overlay colors.
 */
function mm_get_available_overlay_colors( $component = '' ) {

	$colors = array(
		'default' => __( 'Default', 'mm-components' ),
		'light'   => __( 'Light', 'mm-components' ),
		'medium'  => __( 'Medium', 'mm-components' ),
		'dark'    => __( 'Dark', 'mm-components' ),
	);

	return apply_filters( 'mm_get_available_overlay_colors', $colors, $component );
}

/**
 * Return an array of overlay color names for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @param   string  The component calling this function.
 *
 * @return  array  The array of overlay colors.
 */
function mm_get_available_overlay_colors_for_vc( $component = '' ) {

	return array_flip( mm_get_available_overlay_colors( $component ) );
}

/**
 * Return an array of text alignment options.
 *
 * @since   1.0.0
 *
 * @return  array  The array of options.
 */
function mm_get_text_alignment( $component = '' ) {

	$text_alignment = array(
		'default' => __( 'Default', 'mm-components' ),
		'left'    => __( 'Left', 'mm-components' ),
		'center'  => __( 'Center', 'mm-components' ),
		'right'   => __( 'Right', 'mm-components' ),
	);

	$text_alignment = apply_filters( 'mm_get_text_alignment', $text_alignment, $component );

	return $text_alignment;
}

/**
 * Return an array of text alignments for use in a Visual Composer dropdown param.
 *
 * @since   1.0.0
 *
 * @return  array  The array of text alignment options.
 */
function mm_get_text_alignment_for_vc() {

	return array_flip( mm_get_text_alignment() );
}

/**
 * Output <table>.
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
 * Output </table>.
 *
 * @since  1.0.0
 */
function mm_output_table_element_close() {

	echo '</table>';
}

/**
 * Output <thead>.
 *
 * @since  1.0.0
 */
function mm_output_thead_element_open() {

	echo '<thead>';
}

/**
 * Output </thead>.
 *
 * @since  1.0.0
 */
function mm_output_thead_element_close() {

	echo '</thead>';
}

/**
 * Output <tbody>.
 *
 * @since  1.0.0
 */
function mm_output_tbody_element_open() {

	echo '<tbody>';
}

/**
 * Output </tbody>.
 *
 * @since  1.0.0
 */
function mm_output_tbody_element_close() {

	echo '</tbody>';
}