<?php
/*
 * Plugin Name: WooCommerce - Show only lowest prices in variable products
 * Plugin URI: https://servicios.ayudawp.com/
 * Description: Shows only the lowest price and sale in variable WooCommerce products.
 * Author: Fernando Tellado
 * Version: 0.9.12
 * Author URI: https://tellado.es
 * Text Domain: show-only-lowest-prices-in-woocommerce-variable-products
 * Domain Path: /languages
 * WC requires at least: 3.0
 * WC tested up to: 3.8
 * License: GPLv2+
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
function show_only_lowest_prices_in_woocommerce_variable_products_load_plugin_textdomain() {
    load_plugin_textdomain( 'show-only-lowest-prices-in-woocommerce-variable-products', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'show_only_lowest_prices_in_woocommerce_variable_products_load_plugin_textdomain' );
//Simple products
function wc_wc20_variation_price_format( $price, $product ) {
    // Main prices
    $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
    $price = $prices[0] !== $prices[1] ? sprintf( __( '<span class="woofrom">From: </span>%1$s', 'show-only-lowest-prices-in-woocommerce-variable-products' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
    // Sale price
    $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
    sort( $prices );
    $saleprice = $prices[0] !== $prices[1] ? sprintf( __( '<span class="woofrom">From: </span>%1$s', 'show-only-lowest-prices-in-woocommerce-variable-products' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
    if ( $price !== $saleprice ) {
        $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
    }
    return $price;
}
add_filter( 'woocommerce_variable_sale_price_html', 'wc_wc20_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_wc20_variation_price_format', 10, 2 );
//Grouped products
// Show product prices in WooCommerce 2.0 format
add_filter( 'woocommerce_grouped_price_html', 'wc_wc20_grouped_price_format', 10, 2 );
function wc_wc20_grouped_price_format( $price, $product ) {
	$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
	$child_prices     = array();
	foreach ( $product->get_children() as $child_id ) {
		$child_prices[] = get_post_meta( $child_id, '_price', true );
	}
	$child_prices     = array_unique( $child_prices );
	$get_price_method = 'get_price_' . $tax_display_mode . 'uding_tax';
	if ( ! empty( $child_prices ) ) {
		$min_price = min( $child_prices );
		$max_price = max( $child_prices );
	} else {
		$min_price = '';
		$max_price = '';
	}
	if ( $min_price == $max_price ) {
		$display_price = wc_price( $product->$get_price_method( 1, $min_price ) );
	} else {
		$from          = wc_price( $product->$get_price_method( 1, $min_price ) );
		$display_price = sprintf( __( 'From: %1$s', 'show-only-lowest-prices-in-woocommerce-variable-products' ), $from );
	}
	return $display_price;
}
