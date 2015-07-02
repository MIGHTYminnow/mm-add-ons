<?php
/**
 * Plugin Name: MIGHTYminnow Add-ons
 * Plugin URI: http://mightyminnow.com
 * Description: Custom components and functionality for WordPress.
 * Version: 1.0.0
 * Author: MIGHTYminnow Web Studio
 * Author URI: http://mightyminnow.com
 * License: GPL2+
 * Text Domain: mm-add-ons
 * Domain Path: /languages
 */

define( 'MM_PLUG_PATH', plugin_dir_path( __FILE__ ) );
define( 'MM_PLUG_INCLUDES_PATH', MM_PLUG_PATH . 'includes/' );
define( 'MM_PLUG_ASSETS_URL', plugins_url( 'assets/', __FILE__ ) );

add_action( 'plugins_loaded', 'mm_ao_startup' );
/**
 * Start the plugin.
 *
 * @since    1.0.0
 */
function mm_ao_startup() {

	// Set up text domain.
	load_plugin_textdomain( 'mm-add-ons', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

	// Load front-end scripts and styles (priority 20 to load after Trestle).
	add_action( 'wp_enqueue_scripts', 'mm_ao_scripts_and_styles', 20 );

	// Include the components.
	require_once MM_PLUG_PATH . 'components/blockquote.php';
	require_once MM_PLUG_PATH . 'components/button.php';
	require_once MM_PLUG_PATH . 'components/countdown/countdown.php';
	require_once MM_PLUG_PATH . 'components/custom-heading.php';
	require_once MM_PLUG_PATH . 'components/hero-banner.php';
	require_once MM_PLUG_PATH . 'components/highlight-box.php';
	require_once MM_PLUG_PATH . 'components/icon-box.php';
	require_once MM_PLUG_PATH . 'components/image-grid.php';
	require_once MM_PLUG_PATH . 'components/logo-strip.php';
	require_once MM_PLUG_PATH . 'components/polaroid.php';
	require_once MM_PLUG_PATH . 'components/polaroid-2.php';
	require_once MM_PLUG_PATH . 'components/twitter-feed.php';

	// Include Visual Composer Integration if VC is activated.
	if ( defined( 'WPB_VC_VERSION' ) ) {
		require_once MM_PLUG_PATH . 'vc-functions.php';
	}

}

/**
 * Enqueue front-end scripts and styles.
 *
 * @since  1.0.0
 */
function mm_ao_scripts_and_styles() {

	// General styles
	wp_enqueue_style( 'mm-add-ons', plugins_url( '/css/style.css', __FILE__ ) );

	// General scripts
	wp_enqueue_script( 'mm-add-ons', plugins_url( '/js/scripts.js', __FILE__ ) );

}
