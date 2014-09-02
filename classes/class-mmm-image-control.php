<?php
/**
 * Created by Alan on 6/6/2014.
 */

if (!class_exists('WP_Customize_Image_Control')) {
    require_once(ABSPATH . 'wp-includes/class-wp-customize-control.php');
}

class MMM_Image_Control extends WP_Customize_Image_Control {

    /**
     * Constructor.
     *
     * @since 3.4.0
     * @uses WP_Customize_Image_Control::__construct()
     *
     * @param WP_Customize_Manager $manager
     */
    public function __construct( $manager, $id, $args) {
        parent::__construct( $manager, $id, $args);

        if ( $this->setting->default )
            $this->add_tab( 'default',  __('Default'),  array( $this, 'tab_default_background' ) );
    }

//    public function __construct( $manager, $id, $args ) {
//        parent::__construct( $manager, $id, $args);
//    }



    public function to_json() {
        parent::to_json();
    }

    public function enqueue() {
        $file = $GLOBALS['pootlepress_mobile_menu_manager']->file;

        wp_enqueue_style('mmm-customize-controls', plugin_dir_url($file) . 'styles/customize-controls.css');
/*
        wp_enqueue_media();
        wp_enqueue_script( 'customize-views' );

        $this->prepare_control();

        wp_localize_script( 'customize-views', '_wpCustomizeHeader', array(
            'data' => array(
                'width' => absint( get_theme_support( 'custom-header', 'width' ) ),
                'height' => absint( get_theme_support( 'custom-header', 'height' ) ),
                'flex-width' => absint( get_theme_support( 'custom-header', 'flex-width' ) ),
                'flex-height' => absint( get_theme_support( 'custom-header', 'flex-height' ) ),
                'currentImgSrc' => $this->get_current_image_src(),
            ),
            'nonces' => array(
                'add' => wp_create_nonce( 'header-add' ),
                'remove' => wp_create_nonce( 'header-remove' ),
            ),
            'uploads' => $this->uploaded_headers,
            'defaults' => $this->default_headers
        ) );
*/
        parent::enqueue();
    }



    /**
     * @since 3.4.0
     */

    public function tab_uploaded() {
        $backgrounds = get_posts( array(
            'post_type'  => 'attachment',
            'meta_key'   => '_wp_attachment_is_custom_background',
            'meta_value' => $this->manager->get_stylesheet(),
            'orderby'    => 'none',
            'nopaging'   => true,
        ) );

        ?><div class="uploaded-target"></div><?php

        if ( empty( $backgrounds ) )
            return;

        foreach ( (array) $backgrounds as $background )
            $this->print_tab_image( esc_url_raw( $background->guid ) );
    }

    /**
     * @since 3.4.0
     * @uses WP_Customize_Image_Control::print_tab_image()
     */
    public function tab_default_background() {
        $this->print_tab_image( $this->setting->default );
    }
}