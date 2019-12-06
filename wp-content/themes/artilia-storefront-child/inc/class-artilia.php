<?php
/**
 * Artilia Storefront Class
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Artilia_Storefront' ) ) :

    /**
     * The Artilia Storefront Customization class
     */
    class Artilia_Storefront {

        /**
         * Setup class.
         */
        public function __construct() {
            add_action( 'after_setup_theme', array( $this, 'setup' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'artilia_scripts' ), 20 );
        }

        /**
         * Sets up theme defaults and registers support.
         *
         * Note that this function is hooked into the after_setup_theme hook, which
         * runs before the init hook. The init hook is too late for some features, such
         * as indicating support for post thumbnails.
         *
         * @return void
         */
        public function setup() {

        }


        /**
         * Assign styles to individual theme mod.
         *
         * @deprecated 2.3.1
         * @since 2.1.0
         * @return void
         */
        public function set_storefront_style_theme_mods() {
            if ( function_exists( 'wc_deprecated_function' ) ) {
                wc_deprecated_function( __FUNCTION__, '2.3.1' );
            } else {
                _deprecated_function( __FUNCTION__, '2.3.1' );
            }
        }


        /**
         * Artilia specific scripts & stylesheets
         *
         */
        public function artilia_scripts() {
            wp_enqueue_style( 'artilia-storefront-style', get_stylesheet_directory_uri(). '/assets/css/artilia.css', array());
            wp_style_add_data( 'artilia-storefront-style', 'rtl', 'replace' );

            wp_register_script( 'artilia-storefront-scripts', get_stylesheet_directory_uri(). '/assets/js/artilia.js', array('jquery'));
            wp_enqueue_script( 'artilia-storefront-scripts' );
        }

    }

endif;

return new Artilia_Storefront();
