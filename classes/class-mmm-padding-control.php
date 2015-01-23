<?php

if ( ! class_exists( 'MMM_Padding_Control' ) ) :
    if (!class_exists( 'WP_Customize_Control' )) {
        require_once(ABSPATH . '/wp-includes/class-wp-customize-control.php');
    }

	class MMM_Padding_Control extends WP_Customize_Control {

        public $option_name;
        public $type = 'padding';

        public $default;

        private $label1;
        private $label2;

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
            $this->label1 = $args['label1'];
            $this->label2 = $args['label2'];

			parent::__construct( $manager, $id, $args );

		}


        public function enqueue() {

            $file = $GLOBALS['pootlepress_mobile_menu_manager']->file;

            wp_enqueue_style('mmm-customize-controls', plugin_dir_url($file) . 'styles/customize-controls.css');

            parent::enqueue();
        }

        public function get_width_control($number) {

            // Variables used in view
            $value          = $this->value('width' . $number);
            $step           = 1;
            $min_range      = 0;
            $max_range      = 100;
            $default_amount = $this->default['width' . $number];

            $current_amount = isset( $value ) ? $value : $default_amount;

            // Get control view
            ?>
            <label><?php _e( $this->{'label' . $number}, 'scratch' ); ?>

                <input class='width-<?php echo $number ?>-number' type="number" min="<?php echo $min_range ?>"
                       max="<?php echo $max_range ?>" step="<?php echo $step ?>" value="<?php echo $current_amount ?>"
                       default="<?php echo $default_amount ?>"
                    <?php $this->link('width' . $number) ?>
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

                    <?php $this->get_width_control(1); ?>

                    <div class="separator"></div>

                    <?php $this->get_width_control(2); ?>

                </div>
            </label>
        <?php
        }
	}
endif;