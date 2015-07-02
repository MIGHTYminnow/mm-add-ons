<?php
/**
 * MIGHTYminnow Add-ons
 *
 * Component: Twitter Feed
 *
 * @package mm-add-ons
 * @since   1.0.0
 */

add_shortcode( 'twitter_feed', 'mm_twitter_feed_shortcode' );
/**
 * Output Twitter Feed.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_twitter_feed_shortcode( $atts, $content = null, $tag ) {

	// Only run if Fetch Tweets plugin is active.
	if ( ! shortcode_exists( 'fetch_tweets' ) ) {
		return;
	}

	extract( shortcode_atts( array(
		'heading'    => '',
		'user_names' => '',
		'count'      => 10,
		'template'   => ''
	), $atts ) );

	// Clean up usernames if necessary.
	$user_names = str_replace( ' ', '', $user_names );
	$user_names = str_replace( '@', '', $user_names );

	// Clean up count as needed.
	$count = absint( $count );

	// Get Mm classes
	$mm_classes = str_replace( '_', '-', $tag );

	$template_slug = trestle_get_ft_template_slug_from_path( $template );
	$mm_classes .= " template-{$template_slug}";
	$mm_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $mm_classes, $tag, $atts );

	ob_start(); ?>

	<div class="<?php echo $mm_classes; ?>">
		<?php
		if ( $user_names ) {

			// Output component heading.
			if ( $heading ) {
				echo '<h3>' . $heading . '</h3>';
			}

			// Output tweets.
			printf( '[fetch_tweets screen_name="%s" count="%d" template="%s"]',
				$user_names,
				$count,
				$template
			);
		}
		?>
	</div>

	<?php

	$output = ob_get_clean();

	return do_shortcode( $output );
}

add_action( 'vc_before_init', 'mm_vc_mm_twitter_feed' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_mm_twitter_feed() {

	// Only run if Fetch Tweets plugin is active.
	if ( ! class_exists( 'FetchTweets_Commons_Base' ) ) {
		return;
	}

	// Complie VC-compatible array of available templates.
	$fetch_tweets_object = new FetchTweets_Fetch;
	$fetch_tweets_templates = $fetch_tweets_object->oOption->aOptions['arrTemplates'];

	// Setup default option.
	$default_template_object = new FetchTweets_Template();
	$proper_default_template_name = trestle_get_ft_template_name_from_path( $default_template_object->getSlug() );
	$vc_template_array = array(
		sprintf( __( 'Default (%s) - set via Fetch Tweets settings', 'mm-add-ons' ), $proper_default_template_name ) => 'default',
	);

	// Append each template.
	foreach( $fetch_tweets_templates as $template_path => $template ) {
		$proper_template_name = trestle_get_ft_template_name_from_path( $template_path );
		$vc_template_array[ $proper_template_name ] = $template_path;
	}

	vc_map( array(
		'name' => __( 'Twitter Feed', 'mm-add-ons' ),
		'base' => 'twitter_feed',
		'class' => '',
		'icon' => MM_PLUG_ASSETS_URL . 'component_icon.png',
		'category' => __( 'Content', 'mm-add-ons' ),
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => __( 'Heading', 'mm-add-ons' ),
				'param_name' => 'heading',
				'value' => '',
				'description' => __( 'Heading to output above tweets', 'mm-add-ons' ),
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Users', 'mm-add-ons' ),
				'param_name' => 'user_names',
				'admin_label' => true,
				'value' => '',
				'description' => __( 'Comma separated list of user names (without the @ symbol). E.g. Mm, MmLife', 'mm-add-ons' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Output', 'mm-add-ons' ),
				'param_name' => 'template',
				'value' => $vc_template_array,
			),
			array(
				'type' => 'textfield',
				'heading' => __( 'Number of tweets to show', 'mm-add-ons' ),
				'param_name' => 'count',
				'description' => __( 'Defaults to 10', 'mm-add-ons' ),
			),
		)
	) );

}
