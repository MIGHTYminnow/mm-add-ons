<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Hero Banner
 *
 * @package mm-components
 * @since   1.0.0
 */

add_shortcode( 'mm_hero_banner', 'mm_hero_banner_shortcode' );
/**
 * Output Hero Banner.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_hero_banner_shortcode( $atts, $content = null, $tag ) {

    extract( mm_shortcode_atts( array(
        'background_image'    => '',
        'background_position' => 'center center',
        'overlay_color'       => '',
        'overlay_opacity'     => '',
        'heading'             => '',
        'text_position'       => 'left',
        'button_type'         => '',
        'button_link'         => '',
        'button_video_url'    => '',
        'button_text'         => __( 'Read More', 'mm-components' ),
        'button_style'        => '',
        'button_color'        => '',
        'secondary_cta'       => '',
    ), $atts ) );

    // Get link array [url, title, target].
    $button_link_array = vc_build_link( $button_link );
    $button_url = ( isset( $button_link_array['url'] ) && ! empty( $button_link_array['url'] ) ) ? $button_link_array['url'] : '';
    $button_title = ( isset( $button_link_array['title'] ) && ! empty( $button_link_array['title'] ) ) ? $button_link_array['title'] : '';
    $button_target = ( isset( $button_link_array['target'] ) && ! empty( $button_link_array['target'] ) ) ? $button_link_array['target'] : '';

    // Get button classes.
    $button_classes = '';
    $button_classes .= ' ' . $button_style;
    $button_classes .= ' ' . $button_color;

    // Get CSS classes.
    $css_classes = str_replace( '_', '-', $tag );
    $css_classes .= ' full-width';
    $css_classes .= ' ' . $text_position;
    $css_classes = apply_filters( 'mm_shortcode_custom_classes', $css_classes, $tag, $atts );

    /**
     * Parse images.
     *
     * These can be passed either as an attachment ID (VC method), or manually
     * as a URL.
     */

    // Main image.
    if ( is_numeric( $background_image ) ) {
        $image_array = wp_get_attachment_image_src( $background_image, 'full' );
        $image = $image_array[0];
    } else {
        $image = $background_image;
    }

    // Compose style tag.
    $style = "background-image: url($image);";
    $style .= " background-position: $background_position;";

    ob_start(); ?>

    <div class="<?php echo $css_classes; ?>" style="<?php echo $style; ?>">
        <?php
        // Do background overlay.
        if ( $overlay_color && $overlay_opacity ) {
            $styles_array = array();
            $styles_array[] = "background-color: $overlay_color;";
            $styles_array[] = "opacity: $overlay_opacity;";

            $overlay_opacity_ie = $overlay_opacity * 100;
            $styles_array[] = "filter: alpha(opacity=$overlay_opacity_ie);";
            $styles = implode( ' ', $styles_array );

            printf( '<div class="color-overlay" style="%s"></div>',
                $styles
            );
        }
        ?>

        <div class="hero-text-wrapper">
            <div class="wrapper">
                <?php if ( $heading ) : ?>
                    <h2><?php echo $heading; ?></h2>
                <?php endif; ?>
                <?php if ( $content ) : ?>
                    <p><?php echo $content; ?></p>
                <?php endif; ?>

                <?php
                if ( 'standard' == $button_type && $button_url ) {

                    echo do_shortcode(
                        sprintf( '[button href="%s" title="%s" target="%s" class="%s"]%s[/button]',
                            $button_url,
                            $button_title,
                            $button_target,
                            $button_classes,
                            $button_text
                        )
                    );

                } elseif ( 'video' == $button_type && $button_video_url ) {

                    $video_oEmbed = apply_filters( 'the_content', $button_video_url );

                    if ( $video_oEmbed ) {

                        echo do_shortcode(
                            sprintf( '[mm-lightbox link_text="%s" class="button %s" lightbox_class="width-wide %s" lightbox_wrap_class="borderless-lightbox"]%s[/mm-lightbox]',
                                $button_text,
                                $button_classes,
                                null,
                                do_shortcode( $video_oEmbed )
                            )
                        );

                    }

                }
                ?>
                <?php
                if ( $secondary_cta ) :
                    /**
                     * This ridiculous function is modified from Visual Composer
                     * core (vc-raw-html.php), with the main htmlentities()
                     * wrapper function removed to allow for including HTML.
                     */
                ?>
                    <p class="secondary-cta"><?php echo rawurldecode( base64_decode( $secondary_cta ) ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php

    $output = ob_get_clean();

    return $output;
}

add_action( 'vc_before_init', 'mm_vc_hero_banner' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_hero_banner() {

    vc_map( array(
        'name' => __( 'Hero Banner', 'mm-components' ),
        'base' => 'mm_hero_banner',
        'icon' => MM_COMPONENTS_ASSETS_URL . 'component_icon.png',
        'category' => __( 'Content', 'mm-components' ),
        'params' => array(
            array(
                'type' => 'attach_image',
                'heading' => __( 'Background Image', 'mm-components' ),
                'param_name' => 'background_image',
            ),
            array(
                'type' => 'textfield',
                'heading' => __( 'Background Position', 'mm-components' ),
                'param_name' => 'background_position',
                'description' => sprintf(
                    __( 'CSS background position value (%sread more%s). Defaults to: center center.', 'mm-components' ),
                    '<a href="http://www.w3schools.com/cssref/pr_background-position.asp" target="_blank">',
                    '</a>'
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => __( 'Overlay Color', 'mm-components' ),
                'param_name' => 'overlay_color',
                'value' => array(
                    __( 'None', 'mm-components' ) => '',
                    __( 'Black', 'mm-components' ) => '#000',
                    __( 'White', 'mm-components' ) => '#fff',
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => __( 'Overlay Opacity', 'mm-components' ),
                'param_name' => 'overlay_opacity',
                'value' => range( 0.1, 1, 0.1 ),
                'dependency' => array(
                    'element' => 'overlay_color',
                    'value' => array(
                        '#fff',
                        '#000'
                    ),
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => __( 'Heading', 'mm-components' ),
                'param_name' => 'heading',
                'admin_label' => true,
            ),
            array(
                'type' => 'textarea_html',
                'heading' => __( 'Paragraph Text', 'mm-components' ),
                'param_name' => 'content',
            ),
            array(
                'type' => 'dropdown',
                'heading' => __( 'Text Position', 'mm-components' ),
                'param_name' => 'text_position',
                'value' => array(
                    __( 'Left', 'mm-components' ) => 'text-left',
                    __( 'Center', 'mm-components' ) => 'text-center',
                    __( 'Right', 'mm-components' ) => 'text-right',
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => __( 'Button Type', 'mm-components' ),
                'param_name' => 'button_type',
                'value' => array(
                    __( 'Standard', 'mm-components ') => 'standard',
                    __( 'Video', 'mm-components ') => 'video',
                ),
            ),
            array(
                'type' => 'vc_link',
                'heading' => __( 'Button URL', 'mm-components' ),
                'param_name' => 'button_link',
                'dependency' => array(
                    'element' => 'button_type',
                    'value' => array(
                        'standard',
                    ),
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => __( 'Video URL', 'mm-components' ),
                'param_name' => 'button_video_url',
                'dependency' => array(
                    'element' => 'button_type',
                    'value' => array(
                        'video',
                    ),
                ),
            ),
            array(
                'type' => 'textfield',
                'heading' => __( 'Button Text', 'mm-components' ),
                'param_name' => 'button_text',
            ),
            array(
                'type' => 'dropdown',
                'heading' => __( 'Button Style', 'mm-components' ),
                'param_name' => 'button_style',
                'value' => array(
                    __( 'Default (solid)', 'mm-components ') => 'default',
                    __( 'Ghost (transparent background, white border)', 'mm-components ') => 'ghost',
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => __( 'Button Color', 'mm-components' ),
                'param_name' => 'button_color',
                'value' => array(
                    __( 'Default', 'mm-components ') => 'default',
                    __( 'Pink', 'mm-components ') => 'pink',
                    __( 'White', 'mm-components ') => 'white',
                    __( 'Gray', 'mm-components ') => 'gray',
                ),
                'dependency' => array(
                    'element' => 'button_style',
                    'value' => array(
                        'ghost',
                    ),
                ),
            ),
            array(
                'type' => 'textarea_raw_html',
                'heading' => __( 'Secondary Call to Action', 'mm-components' ),
                'param_name' => 'secondary_cta',
                'description' => __( 'Outputs below the main button, can include HTML markup.', 'mm-components' ),
            ),
        ),
    ) );
}

add_action( 'widgets_init', 'mm_components_register_hero_banner_widget' );
/**
 * Register the widget.
 *
 * @since  1.0.0
 */
function mm_components_register_hero_banner_widget() {
    register_widget( 'mm_hero_banner_widget' );
}
/**
 * Hero Banner Widget.
 *
 * @since  1.0.0
 */
class Mm_hero_banner_Widget extends Mm_Components_Widget {
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
            'classname'   => 'mm-hero-banner-strip',
            'description' => __( 'A Hero Banner', 'mm-components' ),
        );
        parent::__construct(
            'mm_hero_banner_widget',
            __( 'Mm Hero Banner', 'mm-components' ),
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
        // At this point all instance options have been sanitized.
        $background_image     = $instance['background_image'];
        $background_position  = $instance['background_position'];
        $overlay_color        = $instance['overlay_color'];
        $heading              = $instance['heading'];
        $paragraph_text       = $instance['paragraph_text'];
        $text_position        = $instance['text_position'];
        $button_type          = $instance['button_type'];
        $button_url           = $instance['button_url'];
        $video_url            = $instance['video_url'];
        $button_text          = $instance['button_text'];
        $button_style         = $instance['button_style'];
        $button_color         = $instance['button_color'];
        $secondary_cta        = $instance['secondary_cta'];
        $shortcode = sprintf(
            '[mm_hero_banner title="%s" title_alignment="%s" images="%s" image_size="%s"]',
                $background_image,
                $background_position,
                $overlay_color,
                $heading,
                $paragraph_text,
                $text_position,
                $button_type,
                $button_url,
                $video_url,
                $button_text,
                $button_style,
                $button_color,
                $secondary_cta
        );
        echo $args['before_widget'];
        echo do_shortcode( $shortcode );
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
            'background_image'    => '',
            'background_position' => '',
            'overlay_color'       => '',
            'heading'             => '',
            'paragraph_text'      => '',
            'text_position'       => '',
            'button_type'         => '',
            'button_url'          => '',
            'video_url'           => '',
            'button_text'         => '',
            'button_style'        => '',
            'button_color'        => '',
            'secondary_cta'       => ''
        );
        // Use our instance args if they are there, otherwise use the defaults.
        $instance = wp_parse_args( $instance, $defaults );
        $background_image     = $instance['background_image'];
        $background_position  = $instance['background_position'];
        $overlay_color        = $instance['overlay_color'];
        $heading              = $instance['heading'];
        $paragraph_text       = $instance['paragraph_text'];
        $text_position        = $instance['text_position'];
        $button_type          = $instance['button_type'];
        $button_url           = $instance['button_url'];
        $video_url            = $instance['video_url'];
        $button_text          = $instance['button_text'];
        $button_style         = $instance['button_style'];
        $button_color         = $instance['button_color'];
        $secondary_cta        = $instance['secondary_cta'];
        $classname            = $this->options['classname'];
        // Background Image.
        $this->field_text(
            __( 'Background Image', 'mm-components' ),
            $classname . '-background-image widefat',
            'background_image',
            $background_image
        );
        // Background Position.
        $this->field_select(
            __( 'Background Position', 'mm-components' ),
            $classname . '-background-position widefat',
            'background_position',
            $background_position,
            array(
                'left'   => __( 'Left', 'mm-components' ),
                'center' => __( 'Center', 'mm-components' ),
                'right'  => __( 'Right', 'mm-components' ),
            )
        );
        // Overlay Color.
        $this->field_select(
            __( 'Overlay Color', 'mm-components' ),
            $classname . '-overlay-color widefat',
            'overlay_color',
            $overlay_color,
            array(
                'none'   => __( 'None', 'mm-components' ),
                'black' => __( 'Black', 'mm-components' ),
                'white'  => __( 'White', 'mm-components' ),
            )
        );
        // Heading.
        $this->field_text(
            __( 'Heading', 'mm-components' ),
            $classname . '-heading widefat',
            'heading',
            $heading
        );
        // Paragraph Text.
        $this->field_textarea(
            __( 'Paragraph Text', 'mm-components' ),
            $classname . '-paragraph-text widefat',
            'paragraph_text',
            $paragraph_text
        );
        // Text Position.
        $this->field_select(
            __( 'Text Position', 'mm-components' ),
            $classname . '-text-position widefat',
            'text_position',
            $text_position,
            array(
                'left'   => __( 'Left', 'mm-components' ),
                'center' => __( 'Center', 'mm-components' ),
                'right' => __( 'Right', 'mm-components' ),
            )
        );
        // Button Type.
        $this->field_select(
            __( 'Button Type', 'mm-components' ),
            $classname . '-button-type widefat',
            'button_type',
            $button_type,
            array(
                'standard'   => __( 'Standard', 'mm-components' ),
                'video' => __( 'Video', 'mm-components' ),
            )
        );
        // Button Url.
        $this->field_text(
            __( 'Button URL', 'mm-components' ),
            $classname . '-button-url widefat',
            'button_url',
            $button_url
        );
        // Video Url.
        $this->field_text(
            __( 'Video URL', 'mm-components' ),
            $classname . '-video-url widefat',
            'video_url',
            $video_url
        );
        // Button Text.
        $this->field_text(
            __( 'Button Text', 'mm-components' ),
            $classname . '-button-text widefat',
            'button_text',
            $button_text
        );
        // Button Style.
        $this->field_select(
            __( 'Button Style', 'mm-components' ),
            $classname . '-button-style widefat',
            'button_style',
            $button_style,
            array(
                'default'   => __( 'Default (solid)', 'mm-components' ),
                'ghost' => __( 'Ghost (transparent background, white border)', 'mm-components' ),
            )
        );
        // Button Color.
        $this->field_select(
            __( 'Button Color', 'mm-components' ),
            $classname . '-button-color widefat',
            'button_color',
            $button_color,
            array(
                'default'   => __( 'Default', 'mm-components' ),
                'pink' => __( 'Pink', 'mm-components' ),
                'white' => __( 'White', 'mm-components' ),
                'gray' => __( 'Gray', 'mm-components' ),
            )
        );
        // Secondary CTA.
        $this->field_textarea(
            __( 'Secondary Call to Action', 'mm-components' ),
            $classname . '-secondary-cta widefat',
            'secondary_cta',
            $secondary_cta
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
     * @return  array  The sanitized settings.
     */
    public function update( $new_instance, $old_instance ) {
        $instance                          = $old_instance;
        $instance['background_image']      = wp_kses_post( $new_instance['background_image'] );
        $instance['background_position']   = $new_instance['background_position'];
        $instance['overlay_color']         = $new_instance['overlay_color'];
        $instance['heading']               = sanitize_text_field( $new_instance['heading'] );
        $instance['paragraph_text']        = sanitize_text_field( $new_instance['paragraph_text'] );
        $instance['text_position']         = $new_instance['text_position'];
        $instance['button_type']           = $new_instance['button_type'];
        $instance['button_url']            = sanitize_text_field( $new_instance['button_url'] );
        $instance['video_url']             = sanitize_text_field( $new_instance['video_url'] );
        $instance['button_text']           = sanitize_text_field( $new_instance['button_text'] );
        $instance['button_style']          = $new_instance['button_style'];
        $instance['button_color']          = $new_instance['button_color'];
        $instance['secondary_cta']         = sanitize_text_field( $new_instance['secondary_cta'] );
        return $instance;
    }
}
