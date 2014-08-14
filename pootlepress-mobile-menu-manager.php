<?php
/*
Plugin Name: Canvas Extension - Mobile Menu Manager
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to customize mobile menu.
Version: 2.0
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-mobile-menu-manager-functions.php' );
require_once( 'classes/class-pootlepress-mobile-menu-manager.php' );
require_once( 'classes/class-pootlepress-updater.php');

$GLOBALS['pootlepress_mobile_menu_manager'] = new Pootlepress_Mobile_Menu_manager( __FILE__ );
$GLOBALS['pootlepress_mobile_menu_manager']->version = '2.0';

add_action('init', 'pp_mmm_updater');
function pp_mmm_updater()
{
    if (!function_exists('get_plugin_data')) {
        include(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $data = get_plugin_data(__FILE__);
    $wptuts_plugin_current_version = $data['Version'];
    $wptuts_plugin_remote_path = 'http://www.pootlepress.com/?updater=1';
    $wptuts_plugin_slug = plugin_basename(__FILE__);
    new Pootlepress_Updater ($wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug);
}
?>
