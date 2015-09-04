<?php
/**
 * Mm Components Visual Composer Functionality.
 *
 * @since 1.0.0
 *
 * @package mm-components
 */

/**
 * Apply custom classes to VC components.
 *
 * @since  1.0.0
 */
add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'mm_shortcode_custom_classes', 10, 3 );

add_action( 'init', 'mm_vc_custom_component_atts', 15 );
/**
 * Add shared Mm parameters/atts to all VC components.
 *
 * Uses priority 5 to ensure params are added before VC auto mapping,
 * which occurs on priority 10 in VC v4.5.2+.
 *
 * @since  1.0.0
 */
function mm_vc_custom_component_atts() {

	// Get all available VC components.
	$components = WPBMap::getShortCodes();

	// Create custom group title.
	$custom_group = __( 'Mm Custom Settings', 'mm-components' );

	// Text color.
	$atts[] = array(
		'type'       => 'dropdown',
		'heading'    => __( 'Text Color Scheme', 'mm-components' ),
		'param_name' => 'mm_class_text_color',
		'group' => $custom_group,
		'value' => array(
			__( 'Default', 'mm-components ') => '',
			__( 'Dark', 'mm-components ')    => 'dark',
			__( 'Light', 'mm-components ')   => 'light',
			__( 'Medium', 'mm-components ')  => 'medium',
		),
	);

	// Text alignment.
	$atts[] = array(
		'type'       => 'dropdown',
		'heading'    => __( 'Text Alignment', 'mm-components' ),
		'param_name' => 'mm_class_text_align',
		'group'      => $custom_group,
		'value' => array(
			__( 'Default', 'mm-components ') => '',
			__( 'Left', 'mm-components ')    => 'left',
			__( 'Center', 'mm-components ')  => 'center',
			__( 'Right', 'mm-components ')   => 'right',
		),
	);

	// Custom Class.
	$atts[] = array(
		'type'       => 'textfield',
		'heading'    => __( 'Custom Class', 'mm-components' ),
		'param_name' => 'mm_custom_class',
		'group'      => $custom_group,
	);

	// Add each param to each VC component.
	foreach ( $atts as $att ) {
		foreach ( $components as $component ) {
			vc_add_param( $component['base'], $att );
		}
	}
}

add_filter( 'vc_single_param_edit', 'mm_filter_vc_field_descriptions', 10, 2 );
/**
 * Add custom image upload description to VC fields.
 *
 * Note: makes use of the 'mm_image_size_for_desc' param for any image upload
 * fields, attempting to calculate 2x the image size being used and output
 * this in the field's description.
 *
 * @since     1.0.0
 *
 * @param     array    $param    Visual composer field array.
 * @param     mixed    $value    Field value.
 *
 * @return    array    $param    Updated field.
 */
function mm_filter_vc_field_descriptions( $param, $value ) {

	// Append custom description to image upload field.
	if ( 'attach_image' == $param['type'] || 'attach_images' == $param['type'] ) {
		$image_size = isset( $param['mm_image_size_for_desc'] ) ? $param['mm_image_size_for_desc'] : '';
		$custom_description = mm_custom_image_field_description( $image_size );
		$param['description'] = isset( $param['description'] ) ? $param['description'] . ' ' . $custom_description : $custom_description;
	}

	return $param;
}

add_action( 'vc_load_default_templates_action', 'mm_vc_register_demo_template' );
/**
 * Register a demo template that will include all of our Mm VC elements.
 *
 * @since  1.0.0
 */
function mm_vc_register_demo_template() {

	$data                 = array();
	$data['name']         = __( 'Mm Components Demo', 'mm-components' );
	$data['weight']       = 99;
  	$data['image_path']   = MM_COMPONENTS_ASSETS_URL . 'template_icon.png';
	$data['custom_class'] = 'mm_components_demo_template';
	$data['content']      = <<<CONTENT
		[vc_row][vc_column][vc_text_separator title="Blockquote"][mm_blockquote image_id="56" quote="This is a blockquote. This blockquote should have an image and a citation." citation="Smart Person"][mm_blockquote quote="This is a blockquote. This blockquote should have no image but should have a citation." citation="Smart Person"][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Button"][mm_button link="" size="large" alignment="center"]This is the button text[/mm_button][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Countdown"][mm_countdown date="2015-12-25" time="18:00:00" timezone="GMT-0800"][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Custom Heading"][mm_custom_heading font_size="36px" text_transform="uppercase" text_align="center"]This is a custom heading[/mm_custom_heading][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Expandable Content"][mm_expandable_content link_style="button" link_text="Click to Expand" link_alignment="center"][mm_polaroid title="Just a Polaroid here" image="57" link_text="Link Text" link=""]This is the text for the polaroid.[/mm_polaroid][/mm_expandable_content][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Hero Banner"][mm_hero_banner overlay_color="#000" overlay_opacity="0.2" text_position="text-center" background_image="59" background_position="cover" heading="Hero Banner Heading" button_text="Button Text" secondary_cta="VGhpcyUyMGlzJTIwYSUyMHNlY29uZGFyeSUyMGNhbGwlMjB0byUyMGFjdGlvbiUyMG9uJTIwdGhlJTIwSGVybyUyMEJhbm5lci4="]This is a hero banner. It should have a black overlay with an opacity of 0.2, and the background image should always cover the whole banner area.[/mm_hero_banner][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Highlight Box"][mm_highlight_box heading_text="Highlight Box Heading" paragraph_text="Highlight box paragraph text. Highlight box paragraph text." link_text="Link Text" link=""][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Icon Box"][mm_icon_box icon_fontawesome="fa fa-bicycle" heading_text="Icon Box Heading" link="" link_text="Link Text"]Icon box text. Icon box text. Icon box text. Icon box text. Icon box text. Icon box text.

Icon box text. Icon box text. Icon box text. Icon box text.[/mm_icon_box][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Image Grid"][mm_image_grid][mm_image_grid_image title="Image Grid Image #1" subtitle="Just a subtitle" image="57" link=""][mm_image_grid_image title="Image Grid Image #2" subtitle="Just another subtitle" image="57" link=""][/mm_image_grid][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Logo Strip"][mm_logo_strip title="Just some logos here" images="52,53,54"][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Polaroid"][mm_polaroid title="Polaroid" image="57" link_text="Link Text" link=""]Just some text content for the polaroid.[/mm_polaroid][/vc_column][/vc_row][vc_row][vc_column][vc_text_separator title="Polaroid 2"][mm_polaroid_2 title="Polaroid 2" image="57" caption="Just a polaroid caption here" caption_color="dark-text" link=""][/vc_column][/vc_row]
CONTENT;
  
	vc_add_default_templates( $data );
}