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
		'countdown'          => __( 'Countdown', 'mm-components' ),
		'custom-heading'     => __( 'Custom Heading', 'mm-components' ),
		'expandable-content' => __( 'Expandable Content', 'mm-components' ),
		'hero-banner'        => __( 'Hero Banner', 'mm-components' ),
		'highlight-box'      => __( 'Highlight Box', 'mm-components' ),
		'icon-box'           => __( 'Icon Box', 'mm-components' ),
		'image-grid'         => __( 'Image Grid', 'mm-components' ),
		'logo-strip'         => __( 'Logo Strip', 'mm-components' ),
		'polaroid'           => __( 'Polaroid', 'mm-components' ),
		'polaroid-2'         => __( 'Polaroid 2', 'mm-components' ),
		'posts'              => __( 'Posts', 'mm-components' ),
		'restricted-content' => __( 'Restricted Content', 'mm-components' ),
		'twitter-feed'       => __( 'Twitter Feed', 'mm-components' ),
	);

	// Allow the theme to turn off specific components.
	$mm_active_components = apply_filters( 'mm_components_active_components', $mm_components );

	// Store array of active components as a global.
	$GLOBALS['mm_active_components'] = $mm_active_components;

	// Include active components.
	if ( array_key_exists( 'blockquote', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/blockquote.php';
	}
	if ( array_key_exists( 'button', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/button.php';
	}
	if ( array_key_exists( 'countdown', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/countdown/countdown.php';
	}
	if ( array_key_exists( 'custom-heading', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/custom-heading.php';
	}
	if ( array_key_exists( 'expandable-content', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/expandable-content.php';
	}
	if ( array_key_exists( 'hero-banner', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/hero-banner.php';
	}
	if ( array_key_exists( 'highlight-box', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/highlight-box.php';
	}
	if ( array_key_exists( 'icon-box', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/icon-box.php';
	}
	if ( array_key_exists( 'image-grid', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/image-grid.php';
	}
	if ( array_key_exists( 'logo-strip', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/logo-strip.php';
	}
	if ( array_key_exists( 'polaroid', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/polaroid.php';
	}
	if ( array_key_exists( 'polaroid-2', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/polaroid-2.php';
	}
	if ( array_key_exists( 'posts', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/posts/posts.php';
		require_once MM_COMPONENTS_PATH . 'components/posts/templates/image-grid.php';
		require_once MM_COMPONENTS_PATH . 'components/posts/templates/simple-list.php';
	}
	if ( array_key_exists( 'restricted-content', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/restricted-content.php';
	}
	if ( array_key_exists( 'twitter-feed', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/twitter-feed.php';
	}

	// Include Visual Composer Integration if VC is activated.
	if ( defined( 'WPB_VC_VERSION' ) ) {
		require_once MM_COMPONENTS_PATH . 'integrations/visual-composer/vc-functions.php';
	}

	// Maybe include our demo component.
	if ( true === WP_DEBUG ) {
		require_once MM_COMPONENTS_PATH . 'components/demo.php';
	}
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
