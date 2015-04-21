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

        $logo = get_option('pootlepress-mmm-nav-toggle-logo', '');
        $image = "";
        if ($logo != '') {
            $image = "<img src='" . esc_attr($logo) . "' />";
        }

        $navIconClass = get_option('pootlepress-mmm-nav-toggle-icon', 'fa-align-justify');
        $navText = get_option('pootlepress-mmm-nav-word-text', 'Navigation');

        $s = ($image == '' ? esc_html($navText) : $image);
        ?>
        <h3 class="nav-toggle icon"><i class="fa <?php esc_attr_e($navIconClass) ?>"></i><a href="#navigation"><?php echo $s; ?></a></h3>
    <?php
    } // End woo_nav_toggle()
}

if ( ! function_exists( 'woo_nav_primary' ) ) {
    function woo_nav_primary()
    {
        $homeIconClass = get_option('pootlepress-mmm-panel-home-icon-class', 'fa-home');

        ?>
        <div class="primary-nav-container">
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
        ?>
        </div>
    <?php
    } // End woo_nav_primary()
}

if ( ! function_exists( 'woo_nav' ) ) {

    function woo_nav() {
        global $woo_options;
        woo_nav_before();

        $closeIconClass = get_option('pootlepress-mmm-panel-close-icon-class', 'fa-times');
        ?>
        <nav id="navigation" class="col-full" role="navigation">

            <?php
            $menu_class = 'menus';
            $number_icons = 0;

            $icons = array(
                'woo_nav_rss',
                'woo_nav_search',
                'woo_header_cart_link'
            );

            foreach ( $icons as $icon ) {
                if ( isset( $woo_options[ $icon ] ) && 'true' == $woo_options[ $icon ] ) {
                    $number_icons++;
                }
            }

            if ( isset( $woo_options[ 'woo_subscribe_email' ] ) && '' != $woo_options[ 'woo_subscribe_email' ] ) {
                $number_icons++;
            }

            if ( 0 < $number_icons ) {
                $menu_class .= ' nav-icons nav-icons-' . $number_icons;

                if ( isset( $woo_options[ 'woo_header_cart_link' ] ) && 'true' == $woo_options['woo_header_cart_link'] ) {
                    if ( isset( $woo_options[ 'woo_header_cart_total' ] ) && 'true' == $woo_options[ 'woo_header_cart_total' ] ) {
                        $menu_class .= ' cart-extended';
                    }
                }
            }
            ?>

            <section class="<?php echo $menu_class; ?>">

                <?php woo_nav_inside(); ?>

            </section><!-- /.menus -->

            <a href="#top" class="nav-close"><i class="fa <?php esc_attr_e($closeIconClass) ?>"></i><span><?php _e( 'Return to Content', 'woothemes' ); ?></span></a>

        </nav>
        <?php
        woo_nav_after();
    } // End woo_nav()
//        woo_nav_after();
//    } // End woo_nav()
}

if ( ! function_exists( 'woo_add_nav_cart_link' ) ) {

    function woo_add_nav_cart_link () {
        global $woocommerce;

        $shopIconClass = get_option('pootlepress-mmm-panel-shop-icon-class', 'fa-shopping-cart');

        $iconClasses = 'fa ' . $shopIconClass;

        $settings = array('header_cart_link' => 'false', 'nav_rss' => 'false', 'header_cart_total' => 'false');
        $settings = woo_get_dynamic_values($settings);

        $class = 'cart fr';
        if ('false' == $settings['nav_rss']) {
            $class .= ' no-rss-link';
        }
        if (is_woocommerce_activated() && 'true' == $settings['header_cart_link']) {
            ?>
            <ul class="<?php echo esc_attr($class); ?>">
                <li>
                    <a class="cart-contents" href="<?php echo esc_url($woocommerce->cart->get_cart_url()); ?>"
                       title="<?php esc_attr_e('View your shopping cart', 'woothemes'); ?>" >
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
                    <ul>
                        <li>
                            <?php
                            if (version_compare(WOOCOMMERCE_VERSION, "2.0.0") >= 0) {
                                the_widget('WC_Widget_Cart', 'title=');
                            } else {
                                the_widget('WooCommerce_Widget_Cart', 'title=');
                            } ?>
                        </li>
                    </ul>
                </li>
            </ul>
        <?php
        }
    } // End woo_add_nav_cart_link()
}


if ( ! function_exists( 'woo_nav_subscribe' ) ) {
    function woo_nav_subscribe() {
        global $woo_options;

        $subscribeIconClass = get_option('pootlepress-mmm-panel-subscribe-icon-class', 'fa-rss');

        $iconClasses = 'fa ' . $subscribeIconClass;

        $class = '';
        if ( isset( $woo_options['woo_header_cart_link'] ) && 'true' == $woo_options['woo_header_cart_link'] )
            $class = ' cart-enabled';

        if ( ( isset( $woo_options['woo_nav_rss'] ) ) && ( $woo_options['woo_nav_rss'] == 'true' ) || ( isset( $woo_options['woo_subscribe_email'] ) ) && ( $woo_options['woo_subscribe_email'] ) ) { ?>
            <ul class="rss fr<?php echo $class; ?>">
                <?php if ( ( isset( $woo_options['woo_subscribe_email'] ) ) && ( $woo_options['woo_subscribe_email'] ) ) { ?>
                    <li class="sub-email"><a href="<?php echo esc_url( $woo_options['woo_subscribe_email'] ); ?>"><i class="<?php echo $iconClasses ?>"></i></a></li>
                <?php } ?>
                <?php if ( isset( $woo_options['woo_nav_rss'] ) && ( $woo_options['woo_nav_rss'] == 'true' ) ) { ?>
                    <li class="sub-rss"><a href="<?php if ( ! empty( $woo_options['woo_feed_url'] ) ) { echo esc_url( $woo_options['woo_feed_url'] ); } else { echo esc_url( get_bloginfo_rss( 'rss2_url' ) ); } ?>"><i class="<?php echo $iconClasses ?>"></i></a></li>
                <?php } ?>
            </ul>
        <?php }
    } // End woo_nav_subscribe()
}
