<?php

function mm_image_card( $args ) {

    $component = 'mm-image-card';

    // Set our defaults and use them as needed.
    $defaults = array(
        'title'                => '',
        'image'                => '',
        'image_card_style'     => 'button-bottom',
        'image_text'           => '',
        'image_text_color'     => '',
        'link_image'           => '',
        'link_title'           => '',
        'link_target'          => '_self',
        'content_align'        => 'default',
        'button_link'          => '',
        'button_link_target'   => '_self',
        'button_text'          => '',
        'button_style'         => '',
        'button_border_weight' => 'default',
        'button_corner_style'  => '',
        'button_color'         => '',
    );
    $args = wp_parse_args( (array)$args, $defaults );

    // Get clean param values.
    $title                = $args['title'];
    $image                = $args['image'];
    $image_card_style     = $args['image_card_style'];
    $image_text           = $args['image_text'];
    $image_text_color     = $args['image_text_color'];
    $link                 = $args['link'];
    $link_image           = $args['link_image'];
    $link_title           = $args['link_title'];
    $content_align        = $args['content_align'];
    $button_link          = $args['button_link'];
    $button_link_target   = $args['button_link_target'];
    $button_text          = $args['button_text'];
    $button_style         = $args['button_style'];
    $button_border_weight = $args['button_border_weight'];
    $button_corner_style  = $args['button_corner_style'];
    $button_color         = $args['button_color'];

    $button_output = '';
    $image_text_output = '';

    // Get Mm classes.
    $mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );
    $mm_classes .= ' mm-text-align-' . $content_align;

    $link_url    = $args['link'];
    $link_target = $args['link_target'];

    // Handle a VC link array.
    if ( 'url' === substr( $args['link'], 0, 3 ) && function_exists( 'vc_build_link' ) ) {
        $link_array  = vc_build_link( $args['link'] );
        $link_url    = $link_array['url'];
        $link_title  = $link_array['title'];
        $link_target = $link_array['target'];
    } else {
        $link_target = $button_link_target;
    }

    // Support the image being an ID or a URL.
    if ( is_numeric( $image ) ) {
        $image_array = wp_get_attachment_image_src( $image, 'full' );
        $image_url   = $image_array[0];
    } else {
        $image_url = esc_url( $image );
    }

    if( $image_text ) {
        $image_text_output = sprintf(
            '<span class="%s %s">%s</span>',
            'mm-image-card-text',
            esc_attr( 'mm-text-color-' . $image_text_color ),
            esc_attr( $image_text )
        );
    }

    if ( $link_image ) {
        $content_output = sprintf(
            '%s<a href="%s" title="%s" target="%s"><img class="%s" src="%s"></a>',
            $image_text_output,
            esc_url( $link_url ),
            esc_attr( $link_title ),
            esc_attr( $link_target ),
            'mm-image-card-image',
            esc_attr( $image_url )
        );
    } else {
        $content_output = sprintf(
            '%s<img class="%s" src="%s">',
            $image_text_output,
            'mm-image-card-image',
            esc_attr( $image_url )
        );

    }

    // Build the button output.
    if ( $button_text ) {

        $button_args = array(
            'link'          => $link,
            'link_title'    => $link_title,
            'link_target'   => $button_link_target,
            'button_text'   => $button_text,
            'style'         => $button_style,
            'corner_style'  => 'pointed',
            'border_weight' => $button_border_weight,
            'color'         => $button_color,
            'alignment'     => $content_align,
        );

        if ( 'button-bottom' === $image_card_style ) {
            $button_output = mm_button( $button_args );
        }

    }

    ob_start(); ?>

    <div class="<?php echo esc_attr( $mm_classes ); ?>">

        <div class="mm-image-card-wrap">

        <?php

        if ( ! empty( $title ) ) {
            printf(
                '<h2>%s</h2>',
                esc_html( $title )
            );
        }

        echo wp_kses_post( $content_output );

        echo wp_kses_post( $button_output );

        ?>

        </div>

    </div>

    <?php

    return ob_get_clean();
}

add_shortcode( 'mm_image_card', 'mm_image_card_shortcode' );
/**
 * Image Card shortcode.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_image_card_shortcode( $atts = array(), $content = null ) {

    if ( $content ) {
        $atts['content'] = $content;
    }

    return mm_image_card( $atts );
}

add_action( 'vc_before_init', 'mm_vc_image_card' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_image_card() {

    $text_alignments        = mm_get_text_alignment_for_vc( 'mm-image-card' );
    $image_card_styles      = mm_get_image_card_styles_for_vc( 'mm-image-card' );
    $image_text_colors      = mm_get_colors_for_vc( 'mm-image-card' );
    $button_styles          = mm_get_button_styles_for_vc( 'mm-button' );
    $button_border_weights  = mm_get_button_border_weights_for_vc( 'mm-button' );
    $button_corner_styles   = mm_get_button_corner_styles_for_vc( 'mm-button' );
    $button_colors          = mm_get_colors_for_vc( 'mm-button' );

    vc_map( array(
        'name'     => __( 'Image Card', 'mm-components' ),
        'base'     => 'mm_image_card',
        'icon'     => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
        'category' => __( 'Content', 'mm-components' ),
        'params'   => array(
            array(
                'type'       => 'attach_image',
                'heading'    => __( 'Image Card Image', 'mm-components' ),
                'param_name' => 'image',
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => __( 'Image Card Style', 'mm-components' ),
                'param_name' => 'image_card_style',
                'value'      => $image_card_styles,
            ),
            array(
                'type'       => 'textfield',
                'heading'    => __( 'Image Text', 'mm-components' ),
                'param_name' => 'image_text',
                'dependency' => array(
                    'element' => 'image_card_style',
                    'value'   => 'text-inside',
                ),
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => __( 'Image Text Color', 'mm-components' ),
                'param_name' => 'image_text_color',
                'value'      => $image_text_colors,
                'dependency' => array(
                    'element' => 'image_card_style',
                    'value'   => 'text-inside',
                ),
            ),
            array(
                'type'       => 'checkbox',
                'heading'    => __( 'Link Image?', 'mm-components' ),
                'param_name' => 'link_image',
            ),
            array(
                'type'       => 'vc_link',
                'heading'    => __( 'Image Card URL', 'mm-components' ),
                'param_name' => 'link',
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => __( 'Content Alignment', 'mm-components' ),
                'param_name' => 'content_align',
                'value'      => $text_alignments,
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __( 'Button Text', 'mm-components' ),
                'param_name'  => 'button_text',
                'dependency' => array(
                    'element' => 'image_card_style',
                    'value'   => 'button-bottom',
                ),
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => __( 'Button Style', 'mm-components' ),
                'param_name' => 'button_style',
                'value'      => $button_styles,
                    'dependency' => array(
                    'element' => 'image_card_style',
                    'value'   => 'button-bottom',
                ),
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => __( 'Button Border Weight', 'mm-components' ),
                'param_name' => 'button_border_weight',
                'value'      => $button_border_weights,
                'dependency' => array(
                    'element' => 'button_style',
                    'value'   => array(
                        'ghost',
                        'solid-to-ghost',
                    )
                ),
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => __( 'Button Color', 'mm-components' ),
                'param_name' => 'button_color',
                'value'      => $button_colors,
                'dependency' => array(
                    'element' => 'image_card_style',
                    'value'   => 'button',
                ),
            ),
        ),
    ) );
}

add_action( 'widgets_init', 'mm_components_register_image_card_widget' );
/**
 * Register the widget.
 *
 * @since  1.0.0
 */
function mm_components_register_image_card_widget() {

    register_widget( 'mm_image_card_widget' );
}

/**
 * Image Card widget.
 *
 * @since  1.0.0
 */
class Mm_Image_Card_Widget extends Mm_Components_Widget {

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
            'classname'   => 'mm-image-card-widget',
            'description' => __( 'An Image Card', 'mm-components' ),
        );

        parent::__construct(
            'mm_image_card_widget',
            __( 'Mm Image Card', 'mm-components' ),
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
            'image'                => '',
            'image_card_style'     => 'button-bottom',
            'image_text'           => '',
            'image_text_color'     => '',
            'link_image'           => '',
            'link_target'          => '_self',
            'content_align'        => 'default',
            'button_link'          => '',
            'button_link_target'   => '_self',
            'button_text'          => '',
            'button_style'         => '',
            'button_border_weight' => 'default',
            'button_corner_style'  => '',
            'button_color'         => '',
        );

        // Use our instance args if they are there, otherwise use the defaults.
        $instance = wp_parse_args( $instance, $defaults );

        // Grab the title and run it through the right filter.
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];

        echo mm_image_card( $instance );

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
            'image'                => '',
            'image_card_style'     => 'button-bottom',
            'image_text'           => '',
            'image_text_color'     => '',
            'link_image'           => '',
            'link'                 => '',
            'link_target'          => '_self',
            'content_align'        => 'default',
            'button_link'          => '',
            'button_link_target'   => '',
            'button_text'          => '',
            'button_style'         => '',
            'button_border_weight' => 'default',
            'button_corner_style'  => '',
            'button_color'         => '',
        );

        // Use our instance args if they are there, otherwise use the defaults.
        $instance = wp_parse_args( $instance, $defaults );

        $image                = $instance['image'];
        $image_card_style     = $instance['image_card_style'];
        $image_text           = $instance['image_text'];
        $image_text_color     = $instance['image_text_color'];
        $link_image           = $instance['link_image'];
        $link                 = $instance['link'];
        $link_target          = $instance['link_target'];
        $content_align        = $instance['content_align'];
        $button_link          = $instance['button_link'];
        $button_link_target   = $instance['button_link_target'];
        $button_text          = $instance['button_text'];
        $button_style         = $instance['button_style'];
        $button_border_weight = $instance['button_border_weight'];
        $button_corner_style  = $instance['button_corner_style'];
        $button_color         = $instance['button_color'];
        $classname            = $this->options['classname'];

        // Image.
        $this->field_single_media(
            __( 'Image', 'mm-components' ),
            __( 'Upload an image that is large enough to be output without stretching', 'mm-components' ),
            $classname . '-image widefat',
            'image',
            $image
        );

        // Image Card Style.
        $this->field_select(
            __( 'Image Card Style', 'mm-components' ),
            '',
            $classname . '-image-card-style widefat',
            'image_card_style',
            $image_card_style,
            mm_get_image_card_styles( 'mm-image-card' )
        );

        // Link text.
        $this->field_text(
            __( 'Link', 'mm-components' ),
            '',
            $classname . '-link widefat',
            'link',
            $link
        );

        // Image Text.
        $this->field_text(
            __( 'Image Text', 'mm-components' ),
            '',
            $classname . '-image-text widefat',
            'image_text',
            $image_text
        );

        // Image Text Color.
        $this->field_select(
            __( 'Image Text Color', 'mm-components' ),
            '',
            $classname . '-image-text-color widefat',
            'image_text_color',
            $image_text_color,
            mm_get_colors( 'mm-image-card' )
        );

        // Link Image.
        $this->field_checkbox(
            __( 'Link Image?', 'mm-components' ),
            '',
            $classname . '-link-image widefat',
            'link_image',
            $link_image
        );

        // Button text.
        $this->field_text(
            __( 'Button Text', 'mm-components' ),
            '',
            $classname . '-heading widefat',
            'button_text',
            $button_text
        );

        // Content align.
        $this->field_select(
            __( 'Content Alignment', 'mm-components' ),
            '',
            $classname . '-content-align widefat',
            'content_align',
            $content_align,
            mm_get_text_alignment( 'mm-image-card' )
        );

        // Button link target.
        $this->field_select(
            __( 'Button Link Target', 'mm-components' ),
            '',
            $classname . '-link-target widefat',
            'button_link_target',
            $button_link_target,
            mm_get_link_targets( 'mm-button' )
        );

        // Button style.
        $this->field_select(
            __( 'Button Style', 'mm-components' ),
            '',
            $classname . '-button-style widefat',
            'button_style',
            $button_style,
            mm_get_button_styles( 'mm-button' )
        );

        // Button border weight.
        $this->field_select(
            __( 'Button Border Weight', 'mm-components' ),
            '',
            $classname . '-button-border-weight widefat',
            'button_border_weight',
            $button_border_weight,
            mm_get_button_border_weights( 'mm-button' )
        );

        // Button color.
        $this->field_select(
            __( 'Button Color', 'mm-components' ),
            '',
            $classname . '-button-color widefat',
            'button_color',
            $button_color,
            mm_get_colors( 'mm-button' )
        );
    }

    /**
     * Update the widget settings.
     *
     * @since   1.0.0
     *
     * @param   array  $new_instance  The new settings for the widget instance.
     * @param   array  $old_instance  The old settings for the widget instance.
     *
     * @return  array                 The sanitized settings.
     */
    public function update( $new_instance, $old_instance ) {

        $instance                         = $old_instance;
        $instance['image']                = sanitize_text_field( $new_instance['image'] );
        $instance['image_card_style']     = sanitize_text_field( $new_instance['image_card_style'] );
        $instance['image_text']           = sanitize_text_field( $new_instance['image_text'] );
        $instance['image_text_color']     = sanitize_text_field( $new_instance['image_text_color'] );
        $instance['link_image']           = sanitize_text_field( $new_instance['link_image'] );
        $instance['link']                 = sanitize_text_field( $new_instance['link'] );
        $instance['content_align']        = sanitize_text_field( $new_instance['content_align'] );
        $instance['button_link']          = sanitize_text_field( $new_instance['button_link'] );
        $instance['button_link_target']   = sanitize_text_field( $new_instance['button_link_target'] );
        $instance['button_text']          = sanitize_text_field( $new_instance['button_text'] );
        $instance['button_style']         = sanitize_text_field( $new_instance['button_style'] );
        $instance['button_border_weight'] = sanitize_text_field( $new_instance['button_border_weight'] );
        $instance['button_corner_style']  = sanitize_text_field( $new_instance['button_corner_style'] );
        $instance['button_color']         = sanitize_text_field( $new_instance['button_color'] );

        return $instance;
    }
}