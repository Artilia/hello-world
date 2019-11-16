<?php
/**
 * Plugin Name: Gift Up!
 * Plugin URI: https://www.giftupapp.com/
 * Description: The simplest way to sell your own gift cards/certificates/vouchers from inside your WordPress website easily with no monthly fee. Redeemable in your WooCommerce shopping cart.
 * Version: 1.4.1
 * Author: Gift Up!
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Developer: Gift Up!
 * Developer URI: https://www.giftupapp.com/
 * Author URI: https://www.giftupapp.com/
 * WC requires at least: 3.0.0
 * WC tested up to: 3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

require_once plugin_dir_path( __FILE__ ) . 'view/giftup-render.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-giftup-tools.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-giftup-settings.php';

function giftup_shortcodes_init() { 
	add_shortcode( 'giftup', 'giftup_func' );
}
add_action( 'init', 'giftup_shortcodes_init' );

function giftup_uninstall() { 
	remove_shortcode( 'giftup' );
	giftup_tools::giftup_disconnect_woocommerce();
	giftup_tools::remove_all_options();
}
register_uninstall_hook( __FILE__, 'giftup_uninstall' );

// Initialize Gift Up! Settings
new giftup_settings( plugin_basename( __FILE__ ), plugin_dir_path( __FILE__ ) );