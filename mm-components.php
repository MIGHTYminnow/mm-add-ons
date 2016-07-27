<?php
/**
 * Plugin Name: MIGHTYminnow Components
 * Plugin URI: http://mightyminnow.com
 * Description: Custom components and functionality for WordPress.
 * Version: 1.0.0
 * Author: MIGHTYminnow Web Studio
 * Author URI: http://mightyminnow.com
 * License: GPL2+
 * Text Domain: mm-components
 * Domain Path: /languages
 */

define( 'MM_COMPONENTS_VERSION', '1.0.0' );
define( 'MM_COMPONENTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'MM_COMPONENTS_URL', plugin_dir_url( __FILE__ ) );
define( 'MM_COMPONENTS_ASSETS_URL', MM_COMPONENTS_URL . 'assets/' );

// Include general functionality.
require_once MM_COMPONENTS_PATH . 'functions.php';

// Include widget base class.
require_once MM_COMPONENTS_PATH . 'classes/class-mm-components-widget.php';

// Maybe include admin class.
if ( is_admin() ) {

	require_once MM_COMPONENTS_PATH . 'classes/class-mm-components-admin.php';

	new Mm_Components_Admin();
}

add_action( 'plugins_loaded', 'mm_components_load_textdomain' );
/**
 * Load the plugin textdomain.
 *
 * @since  1.0.0
 */
function mm_components_load_textdomain() {

	load_plugin_textdomain( 'mm-components', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'init', 'mm_components_init_components', 0 );
/**
 * Initialize the components.
 *
 * @since    1.0.0
 */
function mm_components_init_components() {

	// Set up array of all the components.
	$mm_components = array(
		'blockquote'         => __( 'Blockquote', 'mm-components' ),
		'button'             => __( 'Button', 'mm-components' ),
		'count-up'           => __( 'Count Up', 'mm-components' ),
		'countdown'          => __( 'Countdown', 'mm-components' ),
		'custom-content'     => __( 'Custom Content', 'mm-components' ),
		'custom-heading'     => __( 'Custom Heading', 'mm-components' ),
		'expandable-content' => __( 'Expandable Content', 'mm-components' ),
		'hero-banner'        => __( 'Hero Banner', 'mm-components' ),
		'highlight-box'      => __( 'Highlight Box', 'mm-components' ),
		'icon-box'           => __( 'Icon Box', 'mm-components' ),
		'image-card'         => __( 'Image Card', 'mm-components'),
		'logo-strip'         => __( 'Logo Strip', 'mm-components' ),
		'posts'              => __( 'Posts', 'mm-components' ),
		'restricted-content' => __( 'Restricted Content', 'mm-components' ),
		'slider'             => __( 'Slider', 'mm-components' ),
		'social-icons'       => __( 'Social Icons', 'mm-components' ),
		'users'              => __( 'Users', 'mm-components' ),
	);

	// Allow the theme to turn off specific components.
	$mm_active_components = apply_filters( 'mm_components_active_components', $mm_components );

	// Store array of active components as a global.
	$GLOBALS['mm_active_components'] = $mm_active_components;

	// Include active components.
	if ( array_key_exists( 'blockquote', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/blockquote/blockquote.php';
	}
	if ( array_key_exists( 'button', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/button/button.php';
	}
	if ( array_key_exists( 'count-up', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/count-up/count-up.php';
	}
	if ( array_key_exists( 'countdown', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/countdown/countdown.php';
	}
	if ( array_key_exists( 'custom-content', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/custom-content/custom-content.php';
	}
	if ( array_key_exists( 'custom-heading', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/custom-heading/custom-heading.php';
	}
	if ( array_key_exists( 'expandable-content', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/expandable-content/expandable-content.php';
	}
	if ( array_key_exists( 'hero-banner', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/hero-banner/hero-banner.php';
	}
	if ( array_key_exists( 'highlight-box', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/highlight-box/highlight-box.php';
	}
	if ( array_key_exists( 'icon-box', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/icon-box/icon-box.php';
	}
	if ( array_key_exists( 'image-card', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/image-card/image-card.php';
	}
	if ( array_key_exists( 'logo-strip', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/logo-strip/logo-strip.php';
	}
	if ( array_key_exists( 'posts', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/posts/posts.php';
		require_once MM_COMPONENTS_PATH . 'components/posts/templates/image-grid.php';
		require_once MM_COMPONENTS_PATH . 'components/posts/templates/simple-list.php';
		require_once MM_COMPONENTS_PATH . 'components/posts/templates/simple-image-content.php';
	}
	if ( array_key_exists( 'restricted-content', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/restricted-content/restricted-content.php';
	}
	if ( array_key_exists( 'slider', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/slider/slider.php';
	}
	if ( array_key_exists( 'social-icons', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/social-icons/social-icons.php';
	}
	if ( array_key_exists( 'users', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/users/users.php';
		require_once MM_COMPONENTS_PATH . 'components/users/templates/user-table.php';
	}

	// Include Visual Composer Integration if VC is activated.
	if ( defined( 'WPB_VC_VERSION' ) ) {
		require_once MM_COMPONENTS_PATH . 'integrations/visual-composer/vc-functions.php';
		require_once MM_COMPONENTS_PATH . 'integrations/visual-composer/vc-params.php';
		require_once MM_COMPONENTS_PATH . 'integrations/visual-composer/vc-templates.php';
	}

	// Maybe include our demo component.
	if ( true === WP_DEBUG ) {
		require_once MM_COMPONENTS_PATH . 'components/demo/demo.php';
	}
}

add_action( 'init', 'mm_components_load_bfa', 12 );
/**
 * Initialize the Better Font Awesome Library.
 *
 * @since  1.0.0
 */
function mm_components_load_bfa() {

	if ( ! class_exists( 'Better_Font_Awesome_Library' ) ) {
		require_once ( MM_COMPONENTS_PATH . 'lib/better-font-awesome-library/better-font-awesome-library.php' );
	}

	$args = array(
		'version'             => 'latest',
		'minified'            => true,
		'remove_existing_fa'  => false,
		'load_styles'         => true,
		'load_admin_styles'   => true,
		'load_shortcode'      => true,
		'load_tinymce_plugin' => true,
	);

	Better_Font_Awesome_Library::get_instance( $args );
}

add_action( 'wp_enqueue_scripts', 'mm_components_scripts_and_styles' );
/**
 * Enqueue front-end scripts and styles.
 *
 * @since  1.0.0
 */
function mm_components_scripts_and_styles() {

	// Register imagesLoaded
	wp_register_script(
		'mm-images-loaded',
		MM_COMPONENTS_URL . 'lib/images-loaded/imagesloaded.pkgd.min.js',
		array(),
		MM_COMPONENTS_VERSION,
		true
	);

	// Register isotope.
	wp_register_script(
		'mm-isotope',
		MM_COMPONENTS_URL . 'lib/isotope/isotope.pkgd.min.js',
		array( 'mm-images-loaded' ),
		MM_COMPONENTS_VERSION,
		true
	);

	// Register jQuery countdown.
	wp_register_script(
		'mm-jquery-countdown',
		MM_COMPONENTS_URL . 'lib/jquery-countdown/jquery.countdown.js',
		array( 'jquery' ),
		MM_COMPONENTS_VERSION,
		true
	);

	// Register flickity.
	wp_register_script(
		'mm-flickity',
		MM_COMPONENTS_URL . 'lib/flickity/flickity.pkgd.min.js',
		array(),
		MM_COMPONENTS_VERSION,
		true
	);

	wp_enqueue_style(
		'mm-flickity',
		MM_COMPONENTS_URL . 'lib/flickity/flickity.min.css',
		array(),
		MM_COMPONENTS_VERSION
	);

	// General styles.
	wp_enqueue_style(
		'mm-components',
		MM_COMPONENTS_URL . 'css/mm-components-public.css',
		array(),
		MM_COMPONENTS_VERSION
	);

	// General scripts.
	wp_enqueue_script(
		'mm-components',
		MM_COMPONENTS_URL . 'js/mm-components-public.js',
		array( 'jquery' ),
		MM_COMPONENTS_VERSION,
		true
	);
}

add_action( 'admin_init', 'mm_components_editor_style' );
/**
 * Add our stylesheet to TinyMCE previews.
 *
 * @since  1.0.0
 */
function mm_components_editor_style() {

	add_editor_style( MM_COMPONENTS_URL . 'css/mm-components-public.css' );
}

add_filter( 'mm_components_custom_classes', 'mm_components_custom_classes', 10, 3 );
/**
 * Add custom component classes.
 *
 * The following things are parsed into classes by this function:
 *
 * 1. The component name.
 * 2. Atts whose key begins with: mm_class_*.
 * 3. The custom class att defined by $custom_class_key below.
 *
 * @since   1.0.0
 *
 * @param   string  $classes    Initial classes.
 * @param   string  $component  The component name.
 * @param   array   $atts       The component atts.
 *
 * @return  string              The custom classes.
 */
function mm_components_custom_classes( $classes, $component, $atts ) {

	// Add the component name class.
	$component_class = str_replace( ' ', '-', str_replace( '_', '-', $component ) );
	$classes = ( $classes ) ? "{$component_class} {$classes}" : "{$component_class}";

	// Define attribute key identifiers.
	$custom_class_prefix = 'mm_class_';
	$new_custom_class_prefix = 'mm-';
	$custom_class_key = 'mm_custom_class';

	// Set up the classes array.
	$class_array = explode( ' ', $classes );

	// Loop through each att and add class as needed.
	foreach ( $atts as $key => $value ) {

		// Add class in the following format: $key-$class
		// Exclude custom class att as this needs to be unprefixed.
		if ( false !== strpos( $key, $custom_class_prefix ) && $value ) {

			// Replace custom class prefix with simpler 'mm-' prefix and format appropriately.
			$key = str_replace( $custom_class_prefix, $new_custom_class_prefix, $key );
			$key = str_replace( '_', '-', $key );
			$class_array[] = "{$key}-{$value}";
		}
	}

	// Add mm_custom_class att as an unprefixed class.
	if ( ! empty ( $atts[ $custom_class_key ] ) ) {
		$class_array[] = $atts[ $custom_class_key ];
	}

	// Add custom classes to existing classes.
	$classes = implode( ' ', $class_array );

	return $classes;
}
