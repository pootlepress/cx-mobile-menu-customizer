<?php

$health = 'ok';

if (!function_exists('check_main_heading')) {
    function check_main_heading() {
        global $health;
        if (!function_exists('woo_options_add') ) {
            function woo_options_add($options) {
                $cx_heading = array( 'name' => __('Canvas Extensions', 'pootlepress-canvas-extensions' ),
                    'icon' => 'favorite', 'type' => 'heading' );
                if (!in_array($cx_heading, $options))
                    $options[] = $cx_heading;
                return $options;
            }
        } else {	// another ( unknown ) child-theme or plugin has defined woo_options_add
            $health = 'ng';
        }
    }
}

add_action( 'admin_init', 'poo_commit_suicide' );

if(!function_exists('poo_commit_suicide')) {
    function poo_commit_suicide() {
        global $health;
        $pluginFile = str_replace('-functions', '', __FILE__);
        $plugin = plugin_basename($pluginFile);
        $plugin_data = get_plugin_data( $pluginFile, false );
        if ( $health == 'ng' && is_plugin_active($plugin) ) {
            deactivate_plugins( $plugin );
            wp_die( "ERROR: <strong>woo_options_add</strong> function already defined by another plugin. " .
                $plugin_data['Name']. " is unable to continue and has been deactivated. " .
                "<br /><br />Please contact PootlePress at <a href=\"mailto:support@pootlepress.com?subject=Woo_Options_Add Conflict\"> support@pootlepress.com</a> for additional information / assistance." .
                "<br /><br />Back to the WordPress <a href='".get_admin_url(null, 'plugins.php')."'>Plugins page</a>." );
        }
    }
}


if ( ! function_exists( 'woo_nav_toggle' ) ) {
    function woo_nav_toggle () {

        $navIconClass = get_option('pootlepress-mmm-nav-toggle-icon', 'fa-align-justify');
        $navText = get_option('pootlepress-mmm-nav-word-text', 'Navigation');

        ?>
        <h3 class="nav-toggle icon"><i class="fa <?php esc_attr_e($navIconClass) ?>"></i><a href="#navigation"><?php esc_html_e($navText) ?></a></h3>
    <?php
    } // End woo_nav_toggle()
}

if ( ! function_exists( 'woo_nav_primary' ) ) {
    function woo_nav_primary()
    {
        $homeIconClass = get_option('pootlepress-mmm-panel-home-icon-class', 'fa-home');

        ?>
        <a href="<?php echo home_url(); ?>" class="nav-home"><i class="fa <?php esc_attr_e($homeIconClass) ?>"></i><span><?php _e('Home', 'woothemes'); ?></span></a>

        <?php
        if (function_exists('has_nav_menu') && has_nav_menu('primary-menu')) {
            echo '<h3>' . woo_get_menu_name('primary-menu') . '</h3>';
            wp_nav_menu(array('sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu'));
        } else {
            ?>
            <ul id="main-nav" class="nav fl">
                <?php
                if (get_option('woo_custom_nav_menu') == 'true') {
                    if (function_exists('woo_custom_navigation_output')) {
                        woo_custom_navigation_output('name=Woo Menu 1');
                    }
                } else {
                    ?>

                    <?php if (is_page()) {
                        $highlight = 'page_item';
                    } else {
                        $highlight = 'page_item current_page_item';
                    } ?>
                    <li class="<?php echo esc_attr($highlight); ?>"><a
                            href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'woothemes'); ?></a></li>
                    <?php wp_list_pages('sort_column=menu_order&depth=6&title_li=&exclude='); ?>
                <?php } ?>
            </ul><!-- /#nav -->
        <?php
        }

    } // End woo_nav_primary()
}

if ( ! function_exists( 'woo_nav' ) ) {
    function woo_nav() {
        global $woo_options;
        woo_nav_before();

        $closeIconClass = get_option('pootlepress-mmm-panel-close-icon-class', 'fa-times');

        ?>
        <nav id="navigation" class="col-full" role="navigation">

            <section class="menus">

                <?php woo_nav_inside(); ?>

            </section><!-- /.menus -->

            <a href="#top" class="nav-close"><i class="fa <?php esc_attr_e($closeIconClass) ?>"></i><span><?php _e('Return to Content', 'woothemes' ); ?></span></a>

        </nav>
        <?php
        woo_nav_after();
    } // End woo_nav()
}