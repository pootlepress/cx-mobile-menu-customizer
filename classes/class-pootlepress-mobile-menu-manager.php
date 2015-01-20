<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Pootlepress_Mobile_Menu_manager Class
 *
 * Base class for the Pootlepress Mobile Menu Manager.
 *
 * @package WordPress
 * @subpackage Pootlepress_Mobile_Menu_manager
 * @category Core
 * @author Pootlepress
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * public $token
 * public $version
 * 
 * - __construct()
 * - add_theme_options()
 * - get_menu_styles()
 * - load_stylesheet()
 * - load_script()
 * - load_localisation()
 * - check_plugin()
 * - load_plugin_textdomain()
 * - activation()
 * - register_plugin_version()
 * - get_header()
 * - woo_nav_custom()
 */
class Pootlepress_Mobile_Menu_manager {
	public $token = 'pootlepress-mobile-menu-manager';
	public $version;
	public $file;

    private $navToggleLogo;
    private $navToggleLogoAlign;

    private $navToggleIconPos;
    private $navToggleIconClass;
    private $navToggleIconColor;
    private $navToggleIconSize;
    private $navWordText;
    private $navWordFont;
    private $navOpacity;
    private $navBgColor;
//    private $navMarginTop;
//    private $navMarginBottom;
    private $navPaddingTop;
    private $navPaddingBottom;

    private $panelLogo;
    private $panelAppearPos;
    private $panelBgColor;
    private $panelMenuItemBgColor;
    private $panelMenuItemAlign;
    private $panelMenuItemFont;
    private $panelSelectedMenuItemBgColor;
    private $panelSelectedMenuItemFont;
    private $panelMenuTitleRemove;
    private $panelBorderTop;
    private $panelBorderBottom;
    private $panelSearchBoxEnable;
    private $panelHomeIconRemove;
    private $panelShopIconRemove;
    private $panelSubscribeIconRemove;
    private $panelHomeIconClass;
    private $panelCloseIconClass;
    private $panelCloseIconRight;
    private $panelShopIconClass;
    private $panelSubscribeIconClass;
    private $panelIconSize;
    private $panelIconColor;
    private $panelIconBgColor;
    private $panelIconBorderRadius;
    private $panelSearchBoxFont;
    private $panelSearchBoxBgColor;
    private $panelSearchIconColor;
    private $panelPhoneNumber;
    private $panelPhoneNumberFont;
    private $panelPhoneNumberPos;

    private $optionSidebarEnable;
    private $optionSliderEnable;
    private $optionSearchBoxRemove;
    private $optionHideTopNav;

    private $options;

	/**
	 * Constructor.
	 * @param string $file The base file of the plugin.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->load_plugin_textdomain();
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $file, array( &$this, 'activation' ) );

		// Add the custom theme options.
//		add_filter( 'option_woo_template', array( &$this, 'add_theme_options' ) );

        add_action('wp_head', array(&$this, 'option_css'), 200);
        add_action('wp_enqueue_scripts', array($this, 'front_end_scripts'), 1000);

        // woo_nav_primary is hooked to it at 10, so hook this at 8
        add_action('woo_nav_inside', array($this, 'panel_logo'), 8);

        add_action('after_setup_theme', array($this, 'after_setup_theme'), 100);
        add_action( 'wp_head', array($this, 'google_webfonts'), 150);
        add_action('customize_register', array($this, 'register') );

        add_action( 'customize_controls_enqueue_scripts', array($this, 'customizer_script' ));

        add_action('wp_head', array($this, 'load_saved_options'), 100);

        add_action('init', array($this, 'woocommerce_setup'));

        $this->init_options();

	} // End __construct()

    public function woocommerce_setup() {

        remove_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment', 10);
        add_filter('add_to_cart_fragments', array($this, 'add_to_cart_fragment'));
    }

    public function customizer_script() {
        wp_dequeue_script('accordion');
        wp_enqueue_script('pootlepress-accordion', plugin_dir_url($this->file) . 'scripts/accordion.js', array('jquery'), false, true);
    }

    private function get_font_option($optionName, $defaults) {
        $fontFamily = get_option($optionName . '_id', $defaults['face']);

        if ($fontFamily == '') {
            $fontFamily = $defaults['face'];
        }

        $fontSize = get_option($optionName . '_size', $defaults['size']);
        $fontSizeUnit = get_option($optionName . '_size_unit', $defaults['unit']);
        $fontColor = get_option($optionName . '_color', $defaults['color']);
        $fontStyle = get_option($optionName . '_weight_style', $this->convert_canvas_font_style_to_mmm($defaults['style']));
        $fontStyle = $this->convert_mmm_font_style_to_canvas($fontStyle);

        return array('face' => $fontFamily, 'size' => $fontSize, 'unit' => $fontSizeUnit, 'color' => $fontColor, 'style' => $fontStyle);
    }

    private function get_border_option($optionName, $defaults) {
        $borderWidth = get_option($optionName . '_width', $defaults['width']);
        $borderStyle = get_option($optionName . '_style', $defaults['style']);
        $borderColor = get_option($optionName . '_color', $defaults['color']);

        return array('width' => $borderWidth, 'style' => $borderStyle, 'color' => $borderColor);
    }

    private function get_checkbox_option($optionName, $default) {
        $value = get_option($optionName, $default === 'true' ? '1' : '');
        if ($value == '1') {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function load_saved_options() {
        // hook phone number at 9 or 11
        $this->panelPhoneNumberPos = get_option('pootlepress-mmm-panel-phone-number-pos', 'Above menu');
        if ($this->panelPhoneNumberPos == 'Above menu') {
            add_action('woo_nav_inside', array($this, 'panel_phone_number'), 9);
        } else {
            add_action('woo_nav_inside', array($this, 'panel_phone_number'), 11);
        }

        $this->navToggleLogo = get_option('pootlepress-mmm-nav-toggle-logo', '');
        $this->navToggleLogoAlign = get_option('pootlepress-mmm-nav-toggle-logo-align', 'Left');

        $this->navToggleIconPos = get_option('pootlepress-mmm-nav-toggle-icon-pos', 'Left');
        $this->navToggleIconClass = get_option('pootlepress-mmm-nav-toggle-icon-class', 'icon-align-justify');
        $this->navToggleIconColor = get_option('pootlepress-mmm-nav-toggle-icon-color', '#ffffff');
        $this->navToggleIconSize = get_option('pootlepress-mmm-nav-toggle-icon-size', '1em');
        $this->navWordText = get_option('pootlepress-mmm-nav-word-text', 'Navigation');
        $this->navWordFont = $this->get_font_option('pootlepress-mmm-nav-word-font',
            array('size' => '1', 'unit' => 'em', 'face' => '"Helvetica Neue", Helvetica, sans-serif', 'style' => 'bold', 'color' => '#ffffff'));

        $this->navOpacity = get_option('pootlepress-mmm-nav-opacity', '100');
        $this->navBgColor = get_option('pootlepress-mmm-nav-bg-color', '#000000');
//        $this->navMarginTop = get_option('pootlepress-mmm-nav-margin-top', '0');
//        $this->navMarginBottom = get_option('pootlepress-mmm-nav-margin-bottom', '0');
        $this->navPaddingTop = get_option('pootlepress-mmm-nav-padding-top', '0');
        $this->navPaddingBottom = get_option('pootlepress-mmm-nav-padding-bottom', '0');

        $this->panelLogo = get_option('pootlepress-mmm-panel-logo', '');
        $this->panelAppearPos = get_option('pootlepress-mmm-panel-appear-pos', 'Left');
        $this->panelBgColor = get_option('pootlepress-mmm-panel-bg-color', '');
        $this->panelMenuItemBgColor = get_option('pootlepress-mmm-panel-menu-item-bg-color', '');
        $this->panelMenuItemAlign = get_option('pootlepress-mmm-panel-menu-item-align', 'Left');
        $this->panelMenuItemFont = $this->get_font_option('pootlepress-mmm-panel-menu-item-font',
            array('size' => '14','unit' => 'px', 'face' => '"Helvetica Neue", sans-serif','style' => 'normal','color' => '#666666'));
        $this->panelSelectedMenuItemBgColor = get_option('pootlepress-mmm-panel-selected-menu-item-bg-color', '');
        $this->panelSelectedMenuItemFont = $this->get_font_option('pootlepress-mmm-panel-selected-menu-item-font',
            array('size' => '14','unit' => 'px', 'face' => '"Helvetica Neue", sans-serif','style' => 'normal','color' => '#3088ff'));
        $this->panelMenuTitleRemove = $this->get_checkbox_option('pootlepress-mmm-panel-menu-title-remove', 'false');
        $this->panelBorderTop = $this->get_border_option('pootlepress-mmm-panel-border-top',
            array('width' => '0','style' => 'solid','color' => '#000000'));
        $this->panelBorderBottom = $this->get_border_option('pootlepress-mmm-panel-border-bottom',
            array('width' => '0','style' => 'solid','color' => '#000000'));
        $this->panelSearchBoxEnable = $this->get_checkbox_option('pootlepress-mmm-panel-search-box-enable', 'false');
        $this->panelHomeIconRemove = $this->get_checkbox_option('pootlepress-mmm-panel-home-icon-remove', 'false');
        $this->panelShopIconRemove = $this->get_checkbox_option('pootlepress-mmm-panel-shop-icon-remove', 'false');
        $this->panelSubscribeIconRemove = $this->get_checkbox_option('pootlepress-mmm-panel-subscribe-icon-remove', 'false');

        $this->panelHomeIconClass = get_option('pootlepress-mmm-panel-home-icon-class', 'fa-home');
        $this->panelCloseIconClass = get_option('pootlepress-mmm-panel-close-icon-class', 'fa-times');
        $this->panelShopIconClass = get_option('pootlepress-mmm-panel-shop-icon-class', 'fa-shopping-cart');
        $this->panelSubscribeIconClass = get_option('pootlepress-mmm-panel-subscribe-icon-class', 'fa-rss');

        $this->panelIconSize = get_option('pootlepress-mmm-panel-icon-size', '1em');
        $this->panelIconColor = get_option('pootlepress-mmm-panel-icon-color', '#ffffff');
        $this->panelIconBgColor = get_option('pootlepress-mmm-panel-icon-bg-color', '#999999');
        $this->panelIconBorderRadius = get_option('pootlepress-mmm-panel-icon-border-radius', '3px');

        $this->panelCloseIconRight = $this->get_checkbox_option('pootlepress-mmm-panel-close-icon-right', 'false');
        $this->panelSearchBoxFont = $this->get_font_option('pootlepress-mmm-panel-search-box-font',
            array('size' => '1','unit' => 'px', 'face' => '"Helvetica Neue", Helvetica, sans-serif','style' => 'normal','color' => '#777777')
        );
        $this->panelSearchBoxBgColor = get_option('pootlepress-mmm-panel-search-box-bg-color', '#e6e6e6');
        $this->panelSearchIconColor = get_option('pootlepress-mmm-panel-search-icon-color', '#000000');

        $this->panelPhoneNumber = get_option('pootlepress-mmm-panel-phone-number', '');
        $this->panelPhoneNumberFont = $this->get_font_option('pootlepress-mmm-panel-phone-number-font',
            array('size' => '1','unit' => 'em', 'face' => '"Helvetica Neue", Helvetica, sans-serif','style' => 'normal','color' => '#777777'));

        // mobile options
        $this->optionSidebarEnable = $this->get_checkbox_option('pootlepress-mmm-option-side-bar-enable', 'true');
        $this->optionSliderEnable = $this->get_checkbox_option('pootlepress-mmm-option-slider-enable', 'true');
        $this->optionSearchBoxRemove = $this->get_checkbox_option('pootlepress-mmm-option-search-box-remove', 'false');
        $this->optionHideTopNav = $this->get_checkbox_option('pootlepress-mmm-option-hide-top-nav', 'false');

    }

    public function google_webfonts() {

        if (!function_exists('wf_get_google_fonts')) {
            return;
        }

        $google_fonts = wf_get_google_fonts();

        $fonts_to_load = array();
        $output = '';

        // Go through the options
        if ( ! empty( $this->options ) && ! empty( $google_fonts ) ) {
            foreach ( $this->options as $key => $option ) {

                if ( is_array( $option ) && $option['type'] == 'font' ) {

                    $fontFamilySettingId = $option['settings']['font_id'];
                    $fontFamilyDefault = $option['defaults']['font_id'];
                    $fontFamily = get_option($fontFamilySettingId, $fontFamilyDefault);

                    // Go through the google font array
                    foreach ( $google_fonts as $font ) {
                        // Check if the google font name exists in the current "face" option
                        if ( $fontFamily == $font['name'] && ! in_array( $font['name'], array_keys( $fonts_to_load ) ) ) {
                            // Add google font to output
                            $variant = '';
                            if ( isset( $font['variant'] ) ) $variant = $font['variant'];
                            $fonts_to_load[$font['name']] = $variant;
                        }
                    }
                }
            }

            // Output google font css in header
            if ( 0 < count( $fonts_to_load ) ) {
                $fonts_and_variants = array();
                foreach ( $fonts_to_load as $k => $v ) {
                    $fonts_and_variants[] = $k . $v;
                }
                $fonts_and_variants = array_map( 'urlencode', $fonts_and_variants );
                $fonts = join( '|', $fonts_and_variants );

                $output .= "\n<!-- Google Webfonts -->\n";
                $output .= '<link href="http'. ( is_ssl() ? 's' : '' ) .'://fonts.googleapis.com/css?family=' . $fonts .'" rel="stylesheet" type="text/css" />'."\n";

                echo $output;
            }
        }
    }

    public function init_options() {

        $options = array();
        $options = $this->add_theme_options($options);

        $sections = array(
            'mmm_mobile_nav_bar_section',
            'mmm_mobile_menu_panel_section',
            'mmm_mobile_options_section'
        );

        $this->options = array();

        $currentSectionIndex = -1;
        $currentSection = null;
        $currentPriority = 0;
        foreach ($options as $option) {
            if ($option['type'] == 'subheading') {
                ++$currentSectionIndex;
                $currentSection = $sections[$currentSectionIndex];
                $currentPriority = 10;
            } else if ($option['type'] == 'upload') {

                $settingId = $option['id'];
                $optionType = $option['type'];
                $optionLabel = $option['name'];
                $optionDefault = $option['std'];
                $optionSection = $currentSection;
                $optionPriority = $currentPriority;

                $this->options[$settingId] = array(
                    'id' => $settingId,
                    'type' => $optionType,
                    'label' => $optionLabel,
                    'section' => $optionSection,
                    'setting' => $settingId,
                    'default' => $optionDefault,
                    'priority' => $optionPriority
                );

                ++$currentPriority;

            } else if ($option['type'] == 'text' || $option['type'] == 'color') {

                $settingId = $option['id'];
                $optionType = $option['type'];
                $optionLabel = $option['name'];
                $optionDefault = $option['std'];
                $optionSection = $currentSection;
                $optionPriority = $currentPriority;

                $this->options[$settingId] = array(
                    'id' => $settingId,
                    'type' => $optionType,
                    'label' => $optionLabel,
                    'section' => $optionSection,
                    'setting' => $settingId,
                    'default' => $optionDefault,
                    'priority' => $optionPriority
                );

                ++$currentPriority;

            } else if ($option['type'] == 'checkbox') {

                $settingId = $option['id'];
                $optionType = $option['type'];
                $optionLabel = $option['name'];
                $optionDefault = $option['std'] === 'true';
                $optionSection = $currentSection;
                $optionPriority = $currentPriority;

                $this->options[$settingId] = array(
                    'id' => $settingId,
                    'type' => $optionType,
                    'label' => $optionLabel,
                    'section' => $optionSection,
                    'setting' => $settingId,
                    'default' => $optionDefault,
                    'priority' => $optionPriority
                );

                ++$currentPriority;

            } else if ($option['type'] == 'select') {

                $settingId = $option['id'];
                $optionType = $option['type'];
                $optionLabel = $option['name'];

                $optionSection = $currentSection;
                $optionPriority = $currentPriority;

                $optionChoices = array();
                $firstChoice = null;
                foreach ($option['options'] as $choice) {
                    $optionChoices[$choice] = $choice;
                    if ($firstChoice == null) {
                        $firstChoice = $choice;
                    }
                }

                $optionDefault = isset($option['std']) ? $option['std'] : $firstChoice;

                $this->options[$settingId] = array(
                    'id' => $settingId,
                    'type' => $optionType,
                    'label' => $optionLabel,
                    'section' => $optionSection,
                    'setting' => $settingId,
                    'default' => $optionDefault,
                    'choices' => $optionChoices,
                    'priority' => $optionPriority
                );

                ++$currentPriority;
            } else if ($option['type'] == 'typography') {

                $optionId = $option['id'];
                $optionType = 'font';
                $optionLabel = $option['name'];
                $optionSection = $currentSection;
                $optionSettingIds = array(
                    'font_id' => $optionId . '_id',
                    'font_size' => $optionId . '_size',
                    'font_size_unit' => $optionId . '_size_unit',
                    'font_color' => $optionId . '_color',
                    'font_weight_style' => $optionId . '_weight_style',
                );
                $optionDefaults = array(
                    'font_id' => $option['std']['face'],
                    'font_size' => $option['std']['size'],
                    'font_size_unit' => $option['std']['unit'],
                    'font_color' => $option['std']['color'],
                    'font_weight_style' => $this->convert_canvas_font_style_to_mmm($option['std']['style']),
                );
                $optionPriority = $currentPriority;

                $this->options[$optionId] = array(
                    'id' => $optionId,
                    'type' => $optionType,
                    'label' => $optionLabel,
                    'section' => $optionSection,
                    'settings' => $optionSettingIds,
                    'defaults' => $optionDefaults,
                    'priority' => $optionPriority
                );

                ++$currentPriority;

            } else if ($option['type'] == 'border') {

                $optionId = $option['id'];
                $optionType = 'border';
                $optionLabel = $option['name'];
                $optionSection = $currentSection;
                $optionSettingIds = array(
                    'border_width' => $optionId . '_width',
                    'border_style' => $optionId . '_style',
                    'border_color' => $optionId . '_color',
                );
                $optionDefaults = array(
                    'border_width' => $option['std']['width'],
                    'border_style' => $option['std']['style'],
                    'border_color' => $option['std']['color'],
                );
                $optionPriority = $currentPriority;

                $this->options[$optionId] = array(
                    'id' => $optionId,
                    'type' => $optionType,
                    'label' => $optionLabel,
                    'section' => $optionSection,
                    'settings' => $optionSettingIds,
                    'defaults' => $optionDefaults,
                    'priority' => $optionPriority
                );

                ++$currentPriority;

            } else if (is_array($option['type']) && count($option['type']) == 2 &&
                is_array($option['type'][0]) && $option['type'][0]['type'] == 'text' &&
                is_array($option['type'][1]) && $option['type'][1]['type'] == 'text'
            ) {
                $optionId = $option['id'];
                $optionType = 'padding';
                $optionLabel = $option['name'];
                $optionSection = $currentSection;
                $optionSettingIds = array(
                    'width1' => $option['type'][0]['id'],
                    'width2' => $option['type'][1]['id'],
                );
                $optionDefaults = array(
                    'width1' => $option['type'][0]['std'] === '' ? 0 : $option['type'][0]['std'],
                    'width2' => $option['type'][1]['std'] === '' ? 0 : $option['type'][1]['std'],
                );
                $optionLabel1 = $option['type'][0]['meta'];
                $optionLabel2 = $option['type'][1]['meta'];
                $optionPriority = $currentPriority;

                $this->options[$optionId] = array(
                    'id' => $optionId,
                    'type' => $optionType,
                    'label' => $optionLabel,
                    'section' => $optionSection,
                    'settings' => $optionSettingIds,
                    'defaults' => $optionDefaults,
                    'label1' => $optionLabel1,
                    'label2' => $optionLabel2,
                    'priority' => $optionPriority
                );

                ++$currentPriority;
            }
        }

    }


    private function convert_mmm_font_style_to_canvas($style) {
        if ($style == '100') {
            return '300';
        } else if ($style == '100italic') {
            return '300 italic';
        } else if ($style == '400') {
            return 'normal';
        } else if ($style == '400italic') {
            return 'italic';
        } else if ($style == '700') {
            return 'bold';
        } else if ($style == '700italic') {
            return 'bold italic';
        } else {
            return '';
        }
    }

    private function convert_canvas_font_style_to_mmm($style) {
        if ($style == '300') {
            return '100';
        } else if ($style == '300 italic') {
            return '100italic';
        } else if ($style == 'normal') {
            return '400';
        } else if ($style == 'italic') {
            return '400italic';
        } else if ($style == 'bold') {
            return '700';
        } else if ($style == 'bolditalic') {
            return '700italic';
        } else {
            return '';
        }
    }

    /**
     * Add theme options to the WooFramework.
     * @access public
     * @since  1.0.0
     * @param array $o The array of options, as stored in the database.
     */
    private function add_theme_options ( $o ) {

        //
        // NAV bar
        //
        $o[] = array(
            'name' => __( 'Mobile NavBar', 'pootlepress-mmm' ),
            'type' => 'subheading'
        );
        $o[] = array(
            "id" => "pootlepress-mmm-nav-toggle-logo",
            "name" => __( 'Nav bar Logo', 'pootlepress-mmm' ),
            "desc" => __( 'Nav bar Logo', 'pootlepress-mmm' ),
            "type" => "upload",
            'std' => ''
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-nav-toggle-logo-align',
            'name' => 'Align nav bar logo',
            'desc' => 'Align nav bar logo',
            'type' => 'select',
            'options' => array('Left', 'Center', 'Right')
        );
        $o[] = array(
            "id" => "pootlepress-mmm-nav-toggle-icon-pos",
            "name" => __( 'Toggle icon position', 'pootlepress-mmm' ),
            "desc" => __( 'Toggle icon position.', 'pootlepress-mmm' ),
            "type" => "select",
            "options" => array(
                "Left",
                "Right")
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-nav-toggle-icon',
            'name' => __('Toggle icon FontAwesome class', 'pootlepress-mmm'),
            'desc' => __('Toggle icon FontAwesome class.', 'pootlepress-mmm'),
            'type' => 'text',
            'std' => 'fa-align-justify'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-nav-toggle-icon-color',
            'name' => __('Toggle icon color', 'pootlepress-mmm'),
            'desc' => __('Toggle icon color', 'pootlepress-mmm'),
            'type' => 'color',
            'std' => '#ffffff'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-nav-toggle-icon-size',
            'name' => __('Toggle icon size', 'pootlepress-mmm'),
            'desc' => __('Toggle icon size.', 'pootlepress-mmm'),
            'type' => 'text',
            'std' => '1em'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-nav-word-text',
            'name' => __('Navigation word text', 'pootlepress-mmm'),
            'desc' => __('Navigation word text.', 'pootlepress-mmm'),
            'type' => 'text',
            'std' => 'Navigation'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-nav-word-font',
            'name' => __('Navigation word font style', 'pootlepress-mmm'),
            'desc' => __('Navigation word font style.', 'pootlepress-mmm'),
            "std" => array('size' => '1','unit' => 'em', 'face' => '"Helvetica Neue", Helvetica, sans-serif','style' => 'bold','color' => '#ffffff'),
            "type" => "typography"
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-nav-opacity',
            'name' => __('Opacity', 'pootlepress-mmm'),
            'desc' => __('Opacity (%).', 'pootlepress-mmm'),
            'type' => 'text',
            'std' => '100'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-nav-bg-color',
            'name' => __('Navigation bar background color', 'pootlepress-mmm'),
            'desc' => __('Navigation bar background color.', 'pootlepress-mmm'),
            'type' => 'color',
            'std' => '#000000'
        );
//        $o[] = array(
//            "id" => "pootlepress-mmm_nav-margin-top-bottom",
//            "name" => __( 'Navigation Bar Margin Top/Bottom', 'pootlepress-mmm' ),
//            "desc" => __( 'Enter an integer value i.e. 20 for the desired margin.', 'pootlepress-mmm' ),
//
//            "std" => "",
//            "type" => array(
//                array(  'id' => 'pootlepress-mmm-nav-margin-top',
//                    'type' => 'text',
//                    'std' => '',
//                    'meta' => __( 'Top', 'pootlepress-mmm' ) ),
//                array(  'id' => 'pootlepress-mmm-nav-margin-bottom',
//                    'type' => 'text',
//                    'std' => '',
//                    'meta' => __( 'Bottom', 'pootlepress-mmm' ) )
//            )
//        );

        $o[] = array(
            "id" => "pootlepress-mmm_nav-padding-top-bottom",
            "name" => __( 'Navigation Bar Padding Top/Bottom', 'pootlepress-mmm' ),
            "desc" => __( 'Enter an integer value i.e. 20 for the desired padding.', 'pootlepress-mmm' ),

            "std" => "",
            "type" => array(
                array(  'id' => 'pootlepress-mmm-nav-padding-top',
                    'type' => 'text',
                    'std' => '',
                    'meta' => __( 'Top', 'pootlepress-mmm' ) ),
                array(  'id' => 'pootlepress-mmm-nav-padding-bottom',
                    'type' => 'text',
                    'std' => '',
                    'meta' => __( 'Bottom', 'pootlepress-mmm' ) )
            )
        );

        //
        // Panel
        //
        $o[] = array(
            'name' => __( 'Mobile Menu Panel', 'pootlepress-mmm' ),
            'type' => 'subheading'
        );
        $o[] = array(
            'name' => 'Custom Menu',
            'desc' => '',
            'id' => 'pootlepress-mmm-panel-notice',
            'std' => 'To use a custom menu for your mobile menu, please select this option in Appearance > Menus',
            'type' => 'info'
        );
        $o[] = array(
            "id" => "pootlepress-mmm-panel-logo",
            "name" => __( 'Panel Logo', 'pootlepress-mmm' ),
            "desc" => __( 'Panel Logo', 'pootlepress-mmm' ),
            "type" => "upload",
            'std' => ''
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-appear-pos',
            'name' => 'Appear from left or right',
            'desc' => 'Appear from left or right',
            'type' => 'select',
            'options' => array( 'Left', 'Right')
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-bg-color',
            'name' => 'Menu panel background color',
            'desc' => 'Menu panel background color',
            'type' => 'color',
            'std' => ''
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-menu-item-bg-color',
            'name' => 'Menu item background color',
            'desc' => 'Menu item background color',
            'type' => 'color',
            'std' => ''
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-selected-menu-item-bg-color',
            'name' => 'Menu item background color when page selected',
            'desc' => 'Menu item background color when page selected',
            'type' => 'color',
            'std' => ''
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-menu-item-align',
            'name' => 'Align menu items',
            'desc' => 'Align menu items',
            'type' => 'select',
            'options' => array('Left', 'Center', 'Right')
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-menu-item-font',
            'name' => 'Menu items font',
            'desc' => 'Menu items font',
            'type' => 'typography',
            'std' => array('size' => '14','unit' => 'px', 'face' => '"Helvetica Neue", sans-serif','style' => 'normal','color' => '#666666')
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-selected-menu-item-font',
            'name' => 'Menu items font when page selected',
            'desc' => 'Menu items font when page selected',
            'type' => 'typography',
            'std' => array('size' => '14','unit' => 'px', 'face' => '"Helvetica Neue", sans-serif','style' => 'normal','color' => '#3088ff')
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-menu-title-remove',
            'name' => 'Remove menu title',
            'desc' => 'Remove menu title',
            'type' => 'checkbox',
            'std' => 'false'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-border-top',
            'name' => 'Border Top',
            'desc' => 'Menu item border top',
            'type' => 'border',
            'std' => array('width' => '0','style' => 'solid','color' => '#000000')
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-border-bottom',
            'name' => 'Border Bottom',
            'desc' => 'Menu item border bottom',
            'type' => 'border',
            'std' => array('width' => '0','style' => 'solid','color' => '#000000')
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-home-icon-remove',
            'name' => 'Remove home icon',
            'desc' => 'Remove home icon',
            'type' => 'checkbox',
            'std' => 'false'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-shop-icon-remove',
            'name' => 'Remove shop icon',
            'desc' => 'Remove shop icon (if have WooCommerce installed)',
            'type' => 'checkbox',
            'std' => 'false'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-subscribe-icon-remove',
            'name' => 'Remove subscribe icon',
            'desc' => 'Remove subscribe icon',
            'type' => 'checkbox',
            'std' => 'false'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-home-icon-class',
            'name' => 'Home icon FontAwesome class',
            'desc' => 'Home icon FontAwesome class (e.g. fa-globe)',
            'type' => 'text',
            'std' => 'fa-home'
        );

        $o[] = array(
            'id' => 'pootlepress-mmm-panel-close-icon-class',
            'name' => 'Close icon FontAwesome class',
            'desc' => 'Close icon FontAwesome class (e.g. fa-globe)',
            'type' => 'text',
            'std' => 'fa-times'
        );

        $o[] = array(
            'id' => 'pootlepress-mmm-panel-close-icon-right',
            'name' => 'Move close icon to right',
            'desc' => 'Move close icon to right',
            'type' => 'checkbox',
            'std' => 'false'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-shop-icon-class',
            'name' => 'Shop icon FontAwesome class',
            'desc' => 'Shop icon FontAwesome class (e.g. fa-globe)',
            'type' => 'text',
            'std' => 'fa-shopping-cart'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-subscribe-icon-class',
            'name' => 'Subscribe icon FontAwesome class',
            'desc' => 'Subscribe icon FontAwesome class (e.g. fa-globe)',
            'type' => 'text',
            'std' => 'fa-rss'
        );

        $o[] = array(
            'id' => 'pootlepress-mmm-panel-icon-size',
            'name' => 'Icon size',
            'desc' => 'Icon size (e.g. 1em, 14px)',
            'type' => 'text',
            'std' => '1em'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-icon-color',
            'name' => 'Icon color',
            'desc' => 'Icon color',
            'type' => 'color',
            'std' => '#ffffff'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-icon-bg-color',
            'name' => 'Icon background color',
            'desc' => 'Icon background color',
            'type' => 'color',
            'std' => '#999999'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-icon-border-radius',
            'name' => 'Icon rounded corner radius',
            'desc' => 'Icon rounded corner radius (e.g. 3px)',
            'type' => 'text',
            'std' => '3px'
        );

        $o[] = array(
            'id' => 'pootlepress-mmm-panel-search-box-font',
            'name' => 'Search box font',
            'desc' => 'Search box font',
            'type' => 'typography',
            'std' => array('size' => '1','unit' => 'em', 'face' => '"Helvetica Neue", Helvetica, sans-serif','style' => 'normal','color' => '#777777')
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-search-box-bg-color',
            'name' => 'Search box color',
            'desc' => 'Search box color',
            'type' => 'color',
            'std' => '#e6e6e6'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-search-icon-color',
            'name' => 'Search icon color',
            'desc' => 'Search icon color',
            'type' => 'color',
            'std' => '#000000'
        );

        // phone number
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-phone-number',
            'name' => 'Phone number',
            'desc' => 'Phone number',
            'type' => 'text',
            'std' => ''
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-phone-number-font',
            'name' => 'Phone number font',
            'desc' => 'Phone number font',
            'type' => 'typography',
            'std' => array('size' => '1','unit' => 'em', 'face' => '"Helvetica Neue", Helvetica, sans-serif','style' => 'normal','color' => '#777777')
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-panel-phone-number-pos',
            'name' => 'Phone number position',
            'desc' => 'Phone number position',
            'type' => 'select',
            'options' => array('Above menu', 'Below menu')
        );

        // Mobile Options
        $o[] = array(
            'name' => __( 'Mobile Options', 'pootlepress-mmm' ),
            'type' => 'subheading'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-option-side-bar-enable',
            'name' => 'Enable side bar in mobile view',
            'desc' => 'Enable side bar in mobile view',
            'type' => 'checkbox',
            'std' => 'true'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-option-slider-enable',
            'name' => 'Enable slider in mobile view',
            'desc' => 'Enable slider in mobile view',
            'type' => 'checkbox',
            'std' => 'true'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-option-search-box-remove',
            'name' => 'Remove Search Box',
            'desc' => 'Remove Search Box',
            'type' => 'checkbox',
            'std' => 'false'
        );
        $o[] = array(
            'id' => 'pootlepress-mmm-option-hide-top-nav',
            'name' => 'Hide top nav',
            'desc' => 'Hide top nav',
            'type' => 'checkbox',
            'std' => 'false'
        );
        return $o;
    } // End add_theme_options()

    public function register(WP_Customize_Manager $customizeManager)
    {
        require_once dirname(__FILE__) . '/class-mmm-font-control.php';
        require_once dirname(__FILE__) . '/class-mmm-border-control.php';
        require_once dirname(__FILE__) . '/class-mmm-padding-control.php';
        require_once dirname(__FILE__) . '/class-mmm-shadow-control.php';
        require_once dirname(__FILE__) . '/class-mmm-image-control.php';
        require_once dirname(__FILE__) . '/class-mmm-common-control.php';

        // panel
        $customizeManager->add_panel('mmm_panel', array(
            'title' => 'Mobile Menu',
            'priority' => 10
        ));

        // sections
        $customizeManager->add_section('mmm_mobile_nav_bar_section', array(
            'title' => 'Mobile NavBar',
            'panel' => 'mmm_panel',
            'priority' => 10
        ));

        $customizeManager->add_section('mmm_mobile_menu_panel_section', array(
            'title' => 'Mobile Menu Panel',
            'panel' => 'mmm_panel',
            'priority' => 11
        ));

        $customizeManager->add_section('mmm_mobile_options_section', array(
            'title' => 'Misc Mobile Options',
            'panel' => 'mmm_panel',
            'priority' => 12
        ));

        $customizeManager->add_section('mmm_mobile_utilities_section', array(
            'title' => 'Utilities',
            'panel' => 'mmm_panel',
            'priority' => 13
        ));

        $customizeManager->add_setting('mmm_reset', array(
            'default' => false,
            'type' => 'option'
        ));

        $customizeManager->add_control(new WP_Customize_Control($customizeManager, 'mmm_reset', array(
            'settings' => 'mmm_reset',
            'type' => 'hidden',
            'label' => '',
            'section' => 'mmm_mobile_utilities_section'
        )));

        foreach ($this->options as $ki => $option) {

            if (!isset($option['type'])) {
                continue;
            }

            if ($option['type'] == 'color') {

                $customizeManager->add_setting($option['id'], array(
                    'default' => $option['default'],
                    'type' => 'option' // use option instead of theme_mod
                ));

                $customizeManager->add_control(new WP_Customize_Color_Control($customizeManager, $option['id'], array(
                    'label' => $option['label'],
                    'section' => $option['section'],
                    'settings' => $option['id'],
                    'priority' => $option['priority']
                )));

            } else if ($option['type'] == 'border') {
                foreach ($option['settings'] as $key => $settingID) {
                    $defaultValue = $option['defaults'][$key];
                    $customizeManager->add_setting($settingID, array(
                        'default' => $defaultValue,
                        'type' => 'option'
                    ));
                }

                $customizeManager->add_control(new MMM_Border_Control($customizeManager, $option['id'], $option));

            } else if ($option['type'] == 'padding') {

                foreach ($option['settings'] as $key => $settingID) {
                    $defaultValue = $option['defaults'][$key];
                    $customizeManager->add_setting($settingID, array(
                        'default' => $defaultValue,
                        'type' => 'option'
                    ));
                }

                $customizeManager->add_control(new MMM_Padding_Control($customizeManager, $option['id'], $option));

            } else if ($option['type'] == 'font') {

                foreach ($option['settings'] as $key => $settingID) {
                    $defaultValue = $option['defaults'][$key];
                    $customizeManager->add_setting($settingID, array(
                        'default' => $defaultValue,
                        'type' => 'option'
                    ));
                }

                $customizeManager->add_control(new MMM_Font_Control($customizeManager, $option['id'], $option));
            } else if ($option['type'] == 'shadow') {

                foreach ($option['settings'] as $key => $settingID) {
                    $defaultValue = $option['defaults'][$key];
                    $customizeManager->add_setting($settingID, array(
                        'default' => $defaultValue,
                        'type' => 'option'
                    ));
                }

                $customizeManager->add_control(new MMM_Shadow_Control($customizeManager, $option['id'], $option));

            } else if ($option['type'] == 'select') {

                $customizeManager->add_setting($option['id'], array(
                    'default' => $option['default'],
                    'type' => 'option'
                ));

                $customizeManager->add_control(new MMM_Common_Control($customizeManager, $option['id'], $option));
            } else if ($option['type'] == 'checkbox' || $option['type'] == 'text') {

                $customizeManager->add_setting($option['id'], array(
                    'default' => $option['default'],
                    'type' => 'option'
                ));

                $customizeManager->add_control(new MMM_Common_Control($customizeManager, $option['id'], $option));

            } else if ($option['type'] == 'upload') {
                $customizeManager->add_setting( $option['id'], array(
                    'default' => $option['default'],
                    'type' => 'option'
                ) );

//                $customizeManager->add_setting( new WP_Customize_Background_Image_Setting( $customizeManager, 'background_image_thumb', array(
//                    'theme_supports' => 'custom-background',
//                ) ) );

                $customizeManager->add_control( new MMM_Image_Control( $customizeManager, $option['id'], array(
                    'label'    => $option['label'],
                    'section'  => $option['section'],
                    'priority' => $option['priority'],

                    'setting' => $option['id'],
//                    'settings' => array(
//                        'default' => $option['id'],
//                        'data'    => $option['id'] . '_data',
//                    ),
//                    'context'  => 'custom-header',
//                    'removed'  => 'remove-header',
//                    'get_url'  => 'get_header_image',

//                    'context'  => 'custom-background',
//                    'get_url'  => 'get_background_image',
                ) ) );
            }

        }
    }

    public function after_setup_theme() {
        register_nav_menus(
            array(
                'mobile-menu' 	=> __( 'Mobile Menu', 'pootlepress-mmm' )
            )
        );

        add_action( 'woo_nav_inside', array($this, 'woo_nav_mobile'), 10);

    }

    public function add_to_cart_fragment( $fragments ) {

        $shopIconClass = get_option('pootlepress-mmm-panel-shop-icon-class', 'fa-shopping-cart');

        $iconClasses = 'fa ' . $shopIconClass;

        global $woocommerce;
        $settings = array( 'header_cart_link' => 'false', 'nav_rss' => 'false', 'header_cart_total' => 'false' );
        $settings = woo_get_dynamic_values( $settings );

        ob_start();
        ?>
        <a class="cart-contents" href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>">

            <i class="<?php echo $iconClasses ?>" ></i>
            <span class="text">
            <?php if ($settings['header_cart_total'] == 'true') {
                if ($woocommerce->cart->get_cart_contents_count() > 1) {
                    $s = '<span class="count">%d</span> items';
                } else {
                    $s = '<span class="count">%d</span> item';
                }
                $s = str_replace('%d', $woocommerce->cart->get_cart_contents_count(), $s);
                echo $s . ' - ' . $woocommerce->cart->get_cart_subtotal();
            } ?>
            </span>
        </a>
        <?php

        $fragments['a.cart-contents'] = ob_get_clean();

        return $fragments;
    } // End woocommerce_header_add_to_cart_fragment()

    public function front_end_scripts() {
        // hack to set the html for shopping cart
        // because somehow the theme set its own html in javascript
//        wp_enqueue_script('pootlepress-mmm-front-end', plugin_dir_url($this->file) . 'scripts/pp-mmm.js', array('jquery'));

        ob_start();
        woo_add_nav_cart_link();
        $s = ob_get_clean();

        $shopIconClass = get_option('pootlepress-mmm-panel-shop-icon-class', 'fa-shopping-cart');

        wp_localize_script('pootlepress-mmm-front-end', 'MMM', array('cartHtml' => $s, 'shopIconClass' => $shopIconClass));
    }

    public function woo_nav_mobile()
    {
        $homeIconClass = get_option('pootlepress-mmm-panel-home-icon-class', 'fa-home');
        if (function_exists('has_nav_menu') && has_nav_menu('mobile-menu')) {
        ?>
            <div class="mobile-nav-container">
            <a href="<?php echo home_url(); ?>" class="nav-home"><i class="fa <?php esc_attr_e($homeIconClass) ?>"></i><span><?php _e('Home', 'woothemes'); ?></span></a>

            <?php

            echo '<h3>' . woo_get_menu_name('mobile-menu') . '</h3>';

            if (class_exists('Pootlepress_FA_Main_Nav_Walker')) {
                wp_nav_menu(array('sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'mobile-nav',
                    'menu_class' => 'nav fl', 'theme_location' => 'mobile-menu', 'link_before' => '<span>', 'link_after' => '</span>',
                    'walker' => new Pootlepress_FA_Main_Nav_Walker()));
            } else {
                wp_nav_menu(array('sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'mobile-nav',
                    'menu_class' => 'nav fl', 'theme_location' => 'mobile-menu'));
            }

            ?></div><?php
        }
    } // End woo_nav_primary()


    public function panel_logo() {
        if ($this->panelLogo !== '') {
            ?>
            <div class='panel-logo'><img alt='logo' src="<?php esc_attr_e($this->panelLogo) ?>" /></div>
        <?php
        }
    }

    public function panel_phone_number() {
        if ($this->panelPhoneNumber !== '') {
            ?>
            <div class="panel-phone-number"><?php esc_html_e($this->panelPhoneNumber) ?></div>
            <?php
        }
    }

    public function option_css() {

        $css = '';

        $css .= <<<OPTIONCSS
.nav-toggle {
    height: 36px;
    line-height: 36px;
}

.nav-toggle:before {
    content: "";
    font-family: "FontAwesome";
    font-weight: normal;
    color: #fff;
    margin-left: 0;
    text-shadow: none;
    border-right: none;
    display: inline-block;
    padding: 0;
}

.nav-toggle i {
    font-family: "FontAwesome";
    font-weight: normal;
    color: #fff;
    display: inline-block;
    line-height: inherit;
}
OPTIONCSS;

        $css .= ".nav-toggle img { vertical-align: top; position: relative; z-index: 1; }\n";

        if ($this->navToggleLogoAlign == "Left") {
            $css .= ".nav-toggle { text-align: left; }\n";
        } else if ($this->navToggleLogoAlign == 'Center') {
            $css .= ".nav-toggle { text-align: center; }\n";
        } else if ($this->navToggleLogoAlign == 'Right') {
            $css .= ".nav-toggle { text-align: right; }\n";
        }

        if ($this->navToggleIconPos == 'Left') {
            $css .= ".nav-toggle i { float: left; padding: 0 1em 0 0.5em; margin-left: 0.5em; border-right: 1px solid rgba(255, 255, 255, 0.1); }\n";
        } else {
            $css .= ".nav-toggle i { float: right; padding: 0 0.5em 0 1em; margin-right: 0.5em; border-left: 1px solid rgba(255, 255, 255, 0.1); }\n";
        }

        // icon color
        $css .= ".nav-toggle i {\n";
        $css .= "\t" . 'color: ' . $this->navToggleIconColor . ";\n";
        $css .= "}\n";

        // font size
        $css .= ".nav-toggle i {\n";
        $css .= "\t" . 'font-size: ' . $this->navToggleIconSize . ";\n";
        $css .= "}\n";

        // font style
        $c = $this->generate_font_css($this->navWordFont);
        $css .= ".nav-toggle a { " . $c . " }\n";

        // opacity
        $css .= ".nav-toggle {\n";
        $css .= "\t" . "opacity: " . ($this->navOpacity / 100) . ";\n";
        $css .= "}\n";

        // bg color
        if ($this->navBgColor != '#000000') {

            $rHex = substr($this->navBgColor, 1, 2);
            $gHex = substr($this->navBgColor, 3, 2);
            $bHex = substr($this->navBgColor, 5, 2);

            $r = hexdec($rHex);
            $g = hexdec($gHex);
            $b = hexdec($bHex);

            $css .= <<<NAVBGCOLOR
.nav-toggle {
    background: rgb($r, $g, $b);
}
.nav-toggle a {
    text-shadow: none;
}
NAVBGCOLOR;
        }


        // nav top and bottom margin
//        $css .= ".nav-toggle {\n";
//        if ($this->navMarginTop != '') {
//            $css .= "\t" . 'margin-top: ' . $this->navMarginTop . "px !important;\n";
//        }
//        if ($this->navMarginBottom != '') {
//            $css .= "\t" . 'margin-bottom: ' . $this->navMarginBottom . "px !important;\n";
//        }
//        $css .= "}\n";

        // nav top and bottom margin
        $css .= ".nav-toggle {\n";
        if ($this->navPaddingTop != '') {
            $css .= "\t" . 'padding-top: ' . $this->navPaddingTop . "px !important;\n";
        }
        if ($this->navPaddingBottom != '') {
            $css .= "\t" . 'padding-bottom: ' . $this->navPaddingBottom . "px !important;\n";
        }
        $css .= "}\n";


        $css .= ".nav-toggle a { line-height: 36px !important; }\n";

        //
        // Panel
        //

        //inner-wrapper when clicked -80%
        //#navigation left:100%, translate3D 0%, when clicked, same 0%

        // begin media query for mobile
        $css .= "@media only screen and (max-width: 767px) {\n";


        if ($this->panelAppearPos == 'Right') {
            $css .= <<<PANELTRANSFORM

    #navigation .nav-home span {
        display: none;
    }

    .csstransforms3d.csstransitions .show-nav #inner-wrapper {
//        -webkit-transform: translate3d(-80%, 0, 0);
//        -moz-transform: translate3d(-80%, 0, 0);
//        -ms-transform: translate3d(-80%, 0, 0);
//        -o-transform: translate3d(-80%, 0, 0);
//        transform: translate3d(-80%, 0, 0);
        left: -80%;
    }

    .csstransforms3d.csstransitions #navigation {
        left: 100% !important;
        -webkit-transform: translate3d(0%, 0, 0);
        -moz-transform: translate3d(0%, 0, 0);
        -ms-transform: translate3d(0%, 0, 0);
        -o-transform: translate3d(0%, 0, 0);
        transform: translate3d(0%, 0, 0);
    }

   .csstransforms3d.csstransitions .show-nav #navigation {
        -webkit-transform: translate3d(0%, 0, 0);
        -moz-transform: translate3d(0%, 0, 0);
        -ms-transform: translate3d(0%, 0, 0);
        -o-transform: translate3d(0%, 0, 0);
        transform: translate3d(0%, 0, 0);
    }

PANELTRANSFORM;
        }

        if ($this->panelLogo !== '') {
            $css .= "#navigation .panel-logo {\n";
            $css .= "\t" . "padding: 1em; text-align: center; \n";
            $css .= "}\n";

            $css .= "#navigation .panel-logo img {\n";
            $css .= "\t" . "max-width: 100%; height: auto; \n";
            $css .= "}\n";
        }

        if (function_exists('has_nav_menu') && has_nav_menu('mobile-menu')) {
            $css .= "#navigation .primary-nav-container { display: none; }\n";
            $css .= "#navigation .top-menu, #navigation #top-nav { display: none; }\n";
        }

        // panel bg color
        if ($this->panelBgColor !== '') {
            $css .= "#navigation {\n";
            $css .= "\t" . 'background-color: ' . $this->panelBgColor . ";\n";
            $css .= "}\n";
        }

        // panel menu item bg color
        if ($this->panelMenuItemBgColor !== '') {
            $css .= "#navigation ul li.menu-item:not(.current-menu-item) {\n";
            $css .= "\t" . 'background-color: ' . $this->panelMenuItemBgColor . ";\n";
            $css .= "}\n";
        }

        // panel menu item align text
        $align = strtolower($this->panelMenuItemAlign);
        $css .= "#navigation ul li.menu-item {\n";
        $css .= "\t" . 'text-align: ' . $align . ";\n";
        $css .= "}\n";

        // panel menu item font
        $menuItemFontCss = $this->generate_font_css($this->panelMenuItemFont);
        $css .= "#navigation ul li.menu-item:not(.current-menu-item) a {\n";
        $css .= "\t" . $menuItemFontCss . "\n";
        $css .= "}\n";

        // panel selected menu item bg color
        if ($this->panelSelectedMenuItemBgColor !== '') {
            $css .= "#navigation ul li.menu-item.current-menu-item a, #navigation ul li.menu-item.current-menu-ancestor a {\n";
            $css .= "\t" . 'background-color: ' . $this->panelSelectedMenuItemBgColor . " !important;\n";
            $css .= "}\n";
        }

        // panel selected menu item font
        $css .= "#navigation ul li.menu-item.current-menu-item a {\n";
        $css .= "\t" . $this->generate_font_css($this->panelSelectedMenuItemFont) . "\n";
        $css .= "}\n";

        // panel menu title remove
        if ($this->panelMenuTitleRemove === 'true') {
            $css .= "#navigation h3 {\n";
            $css .= "\t" . 'display: none;' . "\n";
            $css .= "}\n";
        }

        // menu item border top and bottom
        $panelBorderTop = 'border-top:'. $this->panelBorderTop["width"].'px '.$this->panelBorderTop["style"].' '.$this->panelBorderTop["color"].' !important;';
        $panelBorderBottom = 'border-bottom:'. $this->panelBorderBottom["width"].'px '.$this->panelBorderBottom["style"].' '.$this->panelBorderBottom["color"].' !important;';
        $css .= "#navigation ul li.menu-item a{\n";
        $css .= "\t" . $panelBorderTop . "\n";
        $css .= "\t" . $panelBorderBottom . "\n";
        $css .= "}\n";

        // home icon remove
        if ($this->panelHomeIconRemove === 'true') {
            $css .= "#navigation .nav-home {\n";
            $css .= "\t" . 'display: none !important;' . "\n";
            $css .= "}\n";
        }
        // shop icon remove
        if ($this->panelShopIconRemove === 'true') {
            $css .= "#navigation .cart .cart-contents {\n";
            $css .= "\t" . 'display: none !important;' . "\n";
            $css .= "}\n";
        }
        // subscribe icon remove
        if ($this->panelSubscribeIconRemove === 'true') {
            $css .= "#navigation .rss {\n";
            $css .= "\t" . 'display: none !important;' . "\n";
            $css .= "}\n";
        }

        // home icon styling
        // remove default home icon styling
        $css .= "#navigation .nav-home:before {\n";
        $css .= "\t" . 'content: ""; display: none;' . "\n";
        $css .= "}\n";

        // home icon css

        $homeCss = '';
        $homeCss .= 'color: ' . $this->panelIconColor . ' !important; ';
        $homeCss .= 'background-color: ' . $this->panelIconBgColor . ' !important; ';
        $homeCss .= 'border-radius: ' . $this->panelIconBorderRadius . ' !important; ';
        $homeCss .= 'width: ' . $this->panelIconSize . ' !important; ';
        $homeCss .= 'height: ' . $this->panelIconSize . ' !important; ';

        $homeIconCss = '';
        $homeIconCss .= 'display: block !important; ';
        $homeIconCss .= 'text-align: center !important; text-indent: 0 !important; text-decoration: none !important; ';
        $homeIconCss .= 'font-size: ' . $this->panelIconSize . ' !important; ';

        $css .= "#navigation .nav-home{\n";
        $css .= "\t" . $homeCss . "\n";
        $css .= "}\n";

        $css .= "#navigation .nav-home:hover {\n";
        $css .= "\t" . 'text-decoration: none;' . "\n";
        $css .= "}\n";

        $css .= "#navigation .nav-home span{\n";
        $css .= "\t" . 'display: none;' . "\n";
        $css .= "}\n";

        $css .= "#navigation .nav-home i{\n";
        $css .= "\t" . $homeIconCss . "\n";
        $css .= "}\n";

        // close icon styling
        // remove default close icon styling
        $css .= "#navigation .nav-close:before {\n";
        $css .= "\t" . 'content: ""; display: none;' . "\n";
        $css .= "}\n";

        // close icon css

//        $closeCss = '';
//        $closeCss .= 'color: ' . $this->panelCloseIconColor . '; ';
//        $closeCss .= 'background-color: ' . $this->panelCloseIconBgColor . '; ';
//        $closeCss .= 'border-radius: ' . $this->panelCloseIconBorderRadius . ' !important; ';
//        $closeCss .= 'width: ' . $this->panelCloseIconSize . '; ';
//        $closeCss .= 'height: ' . $this->panelCloseIconSize . '; ';
//
//        $closeIconCss = '';
//        $closeIconCss .= 'display: block; ';
//        $closeIconCss .= 'text-align: center; text-indent: 0; text-decoration: none; ';
//        $closeIconCss .= 'font-size: ' . $this->panelCloseIconSize . '; ';

        $css .= "#navigation .nav-close{\n";
        $css .= "\t" . $homeCss . "\n";
        $css .= "}\n";

        $css .= "#navigation .nav-close:hover {\n";
        $css .= "\t" . 'text-decoration: none;' . "\n";
        $css .= "}\n";

        $css .= "#navigation .nav-close span{\n";
        $css .= "\t" . 'display: none;' . "\n";
        $css .= "}\n";

        $css .= "#navigation .nav-close i{\n";
        $css .= "\t" . $homeIconCss . "\n";
        $css .= "}\n";

        // shop icon
        $css .= "#navigation .cart > li > ul { display: none; }\n";

        $css .= "#navigation .cart .cart-contents .text { display: none; } \n";

        $css .= "#navigation .cart .cart-contents:before {\n";
        $css .= "\t" . "content: '' !important;\n";
        $css .= "}\n";

        $css .= "#navigation .cart .cart-contents {\n";
        $css .= "\t" . $homeCss . "\n";
        $css .= "}\n";

        $css .= "#navigation .cart .cart-contents i {\n";
        $css .= "\t" . "position: absolute; right: 7px; top: 7px; z-index: 1;\n";
        $css .= "\t" . $homeIconCss . "\n";
        $css .= "}\n";

        // subscribe icon
        $css .= "#navigation .rss a:before {\n";
        $css .= "\t" . "content: '' !important;\n";
        $css .= "}\n";

        $css .= "#navigation .rss a {\n";
        $css .= "\t" . $homeCss . "\n";
        $css .= "}\n";

        $css .= "#navigation .rss i {\n";
        $css .= "\t" . "z-index: 1; position: absolute; left: 9px; top: 7px; \n";
        $css .= "\t" . $homeIconCss . "\n";
        $css .= "}\n";

        // hide search icon
        $css .= "#navigation .nav-search .search-contents {\n";
        $css .= "\t" . 'display: none !important;' . "\n";
        $css .= "}\n";

        // move close icon to right overwriting home icon
        if ($this->panelCloseIconRight === 'true') {
            $css .= "#navigation .nav-home {\n";
            $css .= "\t" . 'display: none;' . "\n";
            $css .= "}\n";

            $css .= "#navigation .nav-close {\n";
            $css .= "\t" . 'left: auto; right: 1em;' . "\n";
            $css .= "}\n";

            $css .= "#navigation .nav-close span {\n";
            $css .= "\t" . "display: none;" . "\n";
            $css .= "}\n";
        }

        // panel search box font
        $css .= "#navigation .nav-search .search_main input[name=s] {\n";
        $css .= "\t" . $this->generate_font_css($this->panelSearchBoxFont) . "\n";
        $css .= "}\n";

        // panel search box bg color
        $css .= "#navigation .nav-search {\n";
        $css .= "\t" . 'background-color: ' . $this->panelSearchBoxBgColor . " !important;\n";
        $css .= "}\n";

        // panel search icon color
        $hexColor = $this->convert_color_hex_to_dec($this->panelSearchIconColor);
        $r = $hexColor['r'];
        $g = $hexColor['g'];
        $b = $hexColor['b'];
        $css .= "#navigation .nav-search button[name=submit]:before {\n";
        $css .= "\t" . "color: rgba($r, $g, $b, 0.5)" . " !important;\n";
        $css .= "}\n";

        // panel phone number
        $c = $this->generate_font_css($this->panelPhoneNumberFont);
        $css .= ".panel-phone-number { " . $c . " text-align: center; padding: 1em; }\n";

        // option enable/disable sidebar
        if ($this->optionSidebarEnable === 'false') {
            $css .= "#sidebar {\n";
            $css .= "\t" . 'display: none;' . "\n";
            $css .= "}\n";
        }

        if ($this->optionSliderEnable === 'false') {
            $css .= "#loopedSlider, .pagination-wrap.slider-pagination {\n";
            $css .= "\t" . 'display: none !important;' . "\n";
            $css .= "}\n";
        }

        if ($this->optionSearchBoxRemove === 'true') {
            $css .= "#navigation .nav-search {\n";
            $css .= "\t" . 'display: none;' . "\n";
            $css .= "}\n";
        }

        if ($this->optionHideTopNav === 'true') {
            $css .= "#top, #top .top-menu, #top #top-nav { display: none !important; }\n";
        } else {
            $css .= "#top, #top .top-menu, #top #top-nav { display: block !important; }\n";
        }

        $css .= "}\n"; // close media query

        // hide mobile menu, panel logo and phone number in desktop view
        $css .= "@media only screen and (min-width: 768px) {\n";

        $css .= "#navigation .panel-logo { display: none; }\n";
        $css .= "#navigation .panel-phone-number { display: none; }\n";
        $css .= "#navigation .mobile-nav-container { display: none; }\n";
        $css .= "#navigation .cart i { display: none; }\n";
        $css .= "#navigation { min-height: 0; }\n";

        $css .= "}\n";

        echo "<style>".$css."</style>";
    }

    private function convert_color_hex_to_dec($color) {
        $rHex = substr($color, 1, 2);
        $gHex = substr($color, 3, 2);
        $bHex = substr($color, 5, 2);

        $r = hexdec($rHex);
        $g = hexdec($gHex);
        $b = hexdec($bHex);

        return array('r' => $r, 'g' => $g, 'b' => $b);
    }

    private function generate_font_css( $option, $em = '1' ) {

        // Test if font-face is a Google font
//        global $google_fonts;
//
//        if (isset($google_fonts) && is_array($google_fonts) && count($google_fonts) > 0) {
//            foreach ($google_fonts as $google_font) {
//
//                // Add single quotation marks to font name and default arial sans-serif ending
//                if ($option['face'] == $google_font['name'])
//                    $option['face'] = "'" . $option['face'] . "', arial, sans-serif";
//
//            } // END foreach
//        }

        if ( !@$option['style'] && !@$option['size'] && !@$option['unit'] && !@$option['color'] )
            return 'font-family: '.stripslashes($option["face"]).' !important;';
        else {
            if (!isset($option['unit'])) {
                $option['unit'] = 'px';
            }
            return 'font:' . $option['style'] . ' ' . $option['size'] . $option['unit'] . '/' . $em . 'em ' . stripslashes($option['face']) . ' !important; color:' . $option['color'] . ' !important;';
        }
    }

    public function get_search_form() {
            echo '<div id="nav-search" class="nav-search">';
            get_template_part( 'search', 'form' );
            echo '</div><!--/#nav-search .nav-search-->';
    }

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( $this->token, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = $this->token;
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	 
	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( $this->token . '-version', $this->version );
		}
	} // End register_plugin_version()

} // End Class


