<?php

/**
 * Artilia Storefront theme
 *
*/


$artilia = require 'inc/class-artilia.php';

if ( ! function_exists( 'artilia_jquery_single_product_selected_option' ) ) {

    function artilia_jquery_single_product_selected_option()
    {
//    wp_die();
        if (is_product()):
            ?>
            <script type="text/javascript">
              // Ready state
              (function ($) {
                $(document.body).on('added_to_cart', function () {
                  console.log('EVENT: added_to_cart');
                });

              })(jQuery);
            </script>
        <?php
        endif;
    }

}

add_action('wp_footer','artilia_jquery_single_product_selected_option');



if ( ! function_exists( 'artilia_remove_sidebar' ) ) {

    /**
     * Disable sidebar for Artilia pages
     *
     * @param bool $is_active_sidebar
     * @param int|string $index
     *
     * @return bool
     */
    function artilia_remove_sidebar($is_active_sidebar, $index)
    {

//    if( $index !== "sidebar-1" ) {
//        return $is_active_sidebar;
//    }
//    if( ! is_product() ) {
//        return $is_active_sidebar;
//    }

        return false;
    }
}

add_filter( 'is_active_sidebar', 'artilia_remove_sidebar', 10, 2 );



if ( ! function_exists( 'display_artilia_suggestion_attributes' ) ) {

    /**
     * Display Artilia product suggestion attributes
     *
     * @since 1.4.0
     */
    function display_artilia_suggestion_attributes()
    {
        global $product;

//        wp_enqueue_script( 'wc-add-to-cart-variation' );

        $get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

        wc_get_template(
            'single-product/add-to-cart/artilia-suggestions.php',
            array(
                'available_variations' => $get_variations ? $product->get_available_variations() : false,
                'attributes'           => $product->get_variation_attributes(),
                'selected_attributes'  => $product->get_default_attributes(),
            )
        );
        ?>
        <?php
    }
}

//add_action( 'woocommerce_after_single_product', 'display_artilia_suggestion_attributes');



if ( ! function_exists( 'woo_remove_product_tabs' ) ) {

    /**
     * Remove product data tabs
     */

    function woo_remove_product_tabs($tabs)
    {


//    unset( $tabs['description'] );      	// Remove the description tab
        unset($tabs['reviews']);            // Remove the reviews tab
        unset($tabs['additional_information']);    // Remove the additional information tab

        return $tabs;
    }

}

add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);

