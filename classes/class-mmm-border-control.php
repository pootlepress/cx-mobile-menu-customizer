<?php

if ( ! class_exists( 'MMM_Border_Control' ) ) :
    if (!class_exists( 'WP_Customize_Control' )) {
        require_once(ABSPATH . '/wp-includes/class-wp-customize-control.php');
    }

	class MMM_Border_Control extends WP_Customize_Control {

        public $option_name;
        public $type = 'border';

        public $default;
		/**
		 * Constructor.
		 *
		 * If $args['settings'] is not defined, use the $id as the setting ID.
		 *
		 * @since 3.4.0
		 * @uses WP_Customize_Upload_Control::__construct()
		 *
		 * @param WP_Customize_Manager $manager
		 * @param string $id
		 * @param array $args
		 */
		public function __construct( $manager, $id, $args = array() ) {

            $this->default = $args['defaults'];

			parent::__construct( $manager, $id, $args );

		}


        public function enqueue() {

            $file = $GLOBALS['pootlepress_mobile_menu_manager']->file;

            wp_enqueue_script( 'wp-color-picker' );

            // load in footer, so will appear after WP customize-base.js and customize-controls.js
            wp_enqueue_script('mmm-customize-controls', plugin_dir_url($file) . 'scripts/customize-controls.js', array('jquery'), false, true);


            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style('mmm-customize-controls', plugin_dir_url($file) . 'styles/customize-controls.css');

            parent::enqueue();
        }

        public function get_border_width_control() {

            // Variables used in view
            $value          = $this->value('border_width');
            $step           = 1;
            $min_range      = 0;
            $max_range      = 20;
            $default_amount = $this->default['border_width'];
            $default_unit   = 'px';//$this->defaults['font_size']['unit'];

            $current_amount = isset( $value ) ? $value : $default_amount;
            $current_unit   = $default_unit;

            // Get control view
            ?>
            <label><?php _e( 'Border Width', 'scratch' ); ?>

                <input class='border-width-number' type="number" min="<?php echo $min_range ?>"
                       max="<?php echo $max_range ?>" step="<?php echo $step ?>" value="<?php echo $current_amount ?>"
                       default="<?php echo $default_amount ?>"
                    <?php $this->link('border_width') ?>
                    />
                px

            </label>

        <?php
        }

        public function get_border_style_control() {

            // Get defaults and current value
            $this_value      = $this->value('border_style');
            $default_value   = $this->default['border_style'];
            $current_value   = empty( $this_value ) ? $default_value : $this_value;

            $default_styles = array(
                'solid' => 'Solid',
                'dashed' => 'Dashed',
                'dotted' => 'Dotted'
            );

            // Get control view
            ?>
            <label><?php _e( 'Border Style', 'scratch' ); ?>
                <select class='border-style-list' <?php $this->link('border_style') ?> data-default-value="<?php echo $default_value ?>" autocomplete="off">


                    <?php foreach ( $default_styles as $id => $text ) : ?>
                        <option value="<?php echo $id; ?>" data-font-type="default" <?php selected( $current_value, $id ); ?>><?php esc_html_e($text) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php
        }

        public function get_border_color_control() {
            // Variables used in view
            $value         = $this->value('border_color');
            $default_color = $this->default['border_color'];
            $current_color = isset( $value ) ? $value : $default_color;

            // Get control view
            ?>
            <label><?php _e( 'Border Color', 'scratch' ); ?>
                <input class="color-picker-hex font-color-text-box" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value' ); ?>"
                       value="<?php echo $current_color; ?>" data-default-color="<?php echo $default_color ?>"
                    <?php $this->link('border_color') ?>
                    />
            </label>

        <?php
        }


        /**
         * Render Control Content
         *
         * Renders the control in the WordPress Customizer.
         * Each section of the control has been split up
         * in functions in order to make them easier to
         * manage and update.
         *
         * @since 1.2
         * @version 1.3.1
         *
         */
        public function render_content() {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <div class="customize-control-content">

                    <?php $this->get_border_width_control(); ?>
                    <!---->
                    <!--                    <div class="separator"></div>-->
                    <!---->
                    <!--                    --><?php //$this->get_border_style_control(); ?>

                    <div class="separator"></div>

                    <?php $this->get_border_color_control(); ?>

                </div>
            </label>
        <?php
        }
	}
endif;