<?php

if ( ! class_exists( 'MMM_Shadow_Control' ) ) :
    if (!class_exists( 'WP_Customize_Control' )) {
        require_once(ABSPATH . '/wp-includes/class-wp-customize-control.php');
    }

	class MMM_Shadow_Control extends WP_Customize_Control {

        public $option_name;
        public $type = 'shadow';

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

        public function get_shadow_width_control() {

            // Variables used in view
            $value          = $this->value('shadow_width');
            $step           = 1;
            $min_range      = -100;
            $max_range      = 100;
            $default_amount = $this->default['shadow_width'];
            $default_unit   = 'px';

            $current_amount = isset( $value ) ? $value : $default_amount;
            $current_unit   = $default_unit;

            // Get control view
            ?>
            <label><?php _e( 'Drop Shadow', 'scratch' ); ?>

                <input class='pp-shadow-width-number' type="number" min="<?php echo $min_range ?>"
                       max="<?php echo $max_range ?>" step="<?php echo $step ?>" value="<?php echo $current_amount ?>"
                       default="<?php echo $default_amount ?>"
                    <?php $this->link('shadow_width') ?>
                    />
                px

            </label>

        <?php
        }

        public function get_shadow_color_control() {
            // Variables used in view
            $value         = $this->value('shadow_color');
            $default_color = $this->default['shadow_color'];
            $current_color = isset( $value ) ? $value : $default_color;

            // Get control view
            ?>
            <label><?php _e( 'Shadow Color', 'scratch' ); ?>
                <input class="color-picker-hex pp-shadow-color-text-box" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value' ); ?>"
                       value="<?php echo $current_color; ?>" data-default-color="<?php echo $default_color ?>"
                    <?php $this->link('shadow_color') ?>
                    />
            </label>

        <?php
        }

        public function get_shadow_blur_control() {

            // Variables used in view
            $value          = $this->value('shadow_blur');
            $step           = 1;
            $min_range      = 0;
            $max_range      = 100;
            $default_amount = $this->default['shadow_blur'];
            $default_unit   = 'px';

            $current_amount = isset( $value ) ? $value : $default_amount;
            $current_unit   = $default_unit;

            // Get control view
            ?>
            <label><?php _e( 'Blur Distance', 'scratch' ); ?>

                <input class='pp-shadow-blur-number' type="number" min="<?php echo $min_range ?>"
                       max="<?php echo $max_range ?>" step="<?php echo $step ?>" value="<?php echo $current_amount ?>"
                       default="<?php echo $default_amount ?>"
                    <?php $this->link('shadow_blur') ?>
                    />
                px

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

                    <?php $this->get_shadow_width_control(); ?>

                    <div class="separator"></div>

                    <?php $this->get_shadow_color_control(); ?>

                    <div class="separator"></div>

                    <?php $this->get_shadow_blur_control(); ?>

                </div>
            </label>
            <?php
		}
	}
endif;