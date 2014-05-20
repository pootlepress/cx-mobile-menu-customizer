<?php
/*
Plugin Name: Canvas Extension - Mobile Menu Manager
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to customize mobile menu.
Version: 1.0.4
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-mobile-menu-manager-functions.php' );
require_once( 'classes/class-pootlepress-mobile-menu-manager.php' );

$GLOBALS['pootlepress_mobile_menu_manager'] = new Pootlepress_Mobile_Menu_manager( __FILE__ );
$GLOBALS['pootlepress_mobile_menu_manager']->version = '1.0.4';

?>
