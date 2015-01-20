<?php

if ( ! class_exists( 'MMM_Font_Utility' ) ) :
	class MMM_Font_Utility {
		/**
		 * Instance of this class.
		 * 
		 * @var      object
		 * @since    1.2
		 *
		 */
		protected static $instance = null;

		/**
		 * Slug of the plugin screen.
		 * 
		 * @var      string
		 * @since    1.2
		 *
		 */
		protected $plugin_screen_hook_suffix = null;

		
		/**
		 * Constructor Function
		 * 
		 * Initialize the plugin by loading admin scripts & styles and adding a
		 * settings page and menu.
		 *
		 * @since 1.2
		 * @version 1.3.1
		 * 
		 */
		function __construct() {
			$this->register_actions();		
			$this->register_filters();
		}	

		/**
		 * Return an instance of this class.
		 * 
		 * @return    object    A single instance of this class.
		 *
		 * @since 1.2
		 * @version 1.3.1
		 * 
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Register Custom Actions
		 *
		 * Add any custom actions in this function.
		 * 
		 * @since 1.2
		 * @version 1.3.1
		 * 
		 */
		public function register_actions() {
		}

		/**
		 * Register Custom Filters
		 *
		 * Add any custom filters in this function.
		 * 
		 * @since 1.2
		 * @version 1.3.1
		 * 
		 */
		public function register_filters() {

		}

		public static function get_all_fonts() {

            if (function_exists('wf_get_system_fonts') &&
                function_exists('wf_get_google_fonts') &&
                function_exists('wf_get_system_fonts_test_cases')
            ) {

                $font_faces = wf_get_system_fonts();
                $google_fonts = wf_get_google_fonts();
                if ( 0 < count( $google_fonts ) ) {
//                    $font_faces[''] = __( '-- Google WebFonts --', 'woothemes' );
                    $google_fonts_array = array();
                    foreach ( $google_fonts as $k => $v ) {
                        $google_fonts_array[$v['name']] = $v['name'];
                    }
                    asort( $google_fonts_array );
                    $font_faces = array_merge( $font_faces, $google_fonts_array );
                }

                return $font_faces;

            } else {
                // Declare default font list
                $font_list = array(
                        'Arial'               => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Century Gothic'      => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Courier New'         => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Georgia'             => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Helvetica'           => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
						'Helvetica Neue'      => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Impact'              => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Lucida Console'      => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Lucida Sans Unicode' => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Palatino Linotype'   => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'sans-serif'          => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'serif'               => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Tahoma'              => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Trebuchet MS'        => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                        'Verdana'             => array( 'weights' => array( '100', '100italic', '400', '400italic', '700', '700italic' ) ),
                );

                $font_faces = array();
                foreach ($font_list as $k => $v) {
                    $font_faces[$k] = $k;
                }

                return $font_faces;
            }

//            //Build font list to return
//            $fonts = array();
//            foreach ( $font_list as $font => $attributes ) {
//
//                $urls = array();
//
//                // Get font properties from json array.
//                foreach ( $attributes['weights'] as $variant ) {
//                    $urls[ $variant ] = "";
//                }
//
//                // Create a font array containing it's properties and add it to the $fonts array
//                $atts = array(
//                        'name'         => $font,
//                        'font_type'    => 'default',
//                        'font_weights' => $attributes['weights'],
//                        'subsets'      => array(),
//                        'files'        => array(),
//                        'urls'         => $urls,
//                );
//
//                // Add this font to all of the fonts
//                $id           = $font; //strtolower( str_replace( ' ', '_', $font ) );
//                $fonts[ $id ] = $atts;
//            }
//
//            // Filter to allow us to modify the fonts array before saving the transient
//            $fonts = apply_filters( 'tt_font_default_fonts_array', $fonts );
//
//			// Return the font list
//			return apply_filters( 'tt_font_get_default_fonts', $fonts );
		}

//		/**
//		 * Get All Fonts
//		 *
//		 * Merges the default system fonts and the google fonts
//		 * into a single array and returns it
//		 *
//		 * @return array All fonts with their properties
//		 *
//		 * @since 1.2
//		 * @version 1.3.1
//		 *
//		 */
//		public static function get_all_fonts() {
//			$default_fonts = self::get_default_fonts();
//			$google_fonts  = array();//self::get_google_fonts();
//
//			if ( ! $default_fonts ) {
//				$default_fonts = array();
//			}
//
//			if ( ! $google_fonts ) {
//				$google_fonts = array();
//			}
//
//			return array_merge( $default_fonts, $google_fonts );
//		}


		/**
		 * Get Individual Fonts
		 *
		 * Takes an id and returns the corresponding font.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/apply_filters  	apply_filters()
		 *
		 * Custom Filters:
		 *     - 'tt_font_get_font'
		 *  
		 * @return array $fonts - All websafe fonts with their properties
		 *
		 * @since 1.2
		 * @version 1.3.1
		 * 
		 */
		public static function get_font( $id = '' ) {
			// Get all fonts
			$default_fonts = self::get_default_fonts();
			$google_fonts  = array(); //self::get_google_fonts();

			// Check if it is set and return if found
			if ( isset( $default_fonts[ $id ] ) ) {
				
				// Return default font from array if set
				return apply_filters( 'tt_font_get_font', $default_fonts[ $id ] );

			} else if ( isset( $google_fonts[ $id ] ) ) {

				// Return google font from array if set
				return apply_filters( 'tt_font_get_font', $google_fonts[ $id ] );

			} else {
				return false;
			}			
		}
	}
endif;