<?php
/*
Plugin Name: Canvas Extension - Mobile Menu Customizer
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to customize mobile menu.
Version: 2.1.0
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-mobile-menu-manager-functions.php' );
require_once( 'classes/class-pootlepress-mobile-menu-manager.php' );

$GLOBALS['pootlepress_mobile_menu_manager'] = new Pootlepress_Mobile_Menu_manager( __FILE__ );
$GLOBALS['pootlepress_mobile_menu_manager']->version = '2.1.0';

//CX API
require 'pp-cx/class-pp-cx-init.php';
new PP_Canvas_Extensions_Init(
	array(
		'key'          => 'mobile-menu-customizer',
		'label'        => 'Mobile Menu Customizer',
		'url'          => 'http://www.pootlepress.com/shop/mobile-menu-manager-woothemes-canvas/',
		'description'  => "Page Customizer has a huge amount of options that can be set on a per post and page level and also many site-wide options.",
		'img'          => 'http://www.pootlepress.com/wp-content/uploads/2014/04/mobile-menu-manager-icon.png',
		'installed'    => true,
		'settings_url' => admin_url( 'customize.php?autofocus[panel]=mmm_panel' ),
	),
	__FILE__
);
