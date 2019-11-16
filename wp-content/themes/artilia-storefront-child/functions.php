<?php
//add_action( 'admin_enqueue_scripts', 'my_enqueue' );
//function my_enqueue( $hook ) {
//    if( 'myplugin_settings.php' != $hook ) return;
//    wp_enqueue_script( 'ajax-script',
//        plugins_url( '/js/myjquery.js', __FILE__ ),
//        array( 'jquery' )
//    );
//}

//function wp_register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
//    $wp_scripts = wp_scripts();
//    _wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );
//
//    $registered = $wp_scripts->add( $handle, $src, $deps, $ver );
//    if ( $in_footer ) {
//        $wp_scripts->add_data( $handle, 'group', 1 );
//    }
//
//    return $registered;
//}


//wp_register_script( 'someScript-js', 'https://domain.com/someScript.js' , '', '', true );
//wp_enqueue_script( 'someScript-js' );

//add_action("wp_enqueue_scripts", "myscripts");
//function myscripts() {
//    wp_register_script('myfirstscript',
//        get_template_directory_uri() .'/myscript.js',   //
//        array ('jquery', 'jquery-ui'),                  //depends on these, however, they are registered by core already, so no need to enqueue them.
//        false, false);
//    wp_enqueue_script('myfirstscript');
//
//}


////JSON
//function my_ajax_handler() {
//    check_ajax_referer( 'title_example' );woocommerce-swatches-template-functions.php
//    update_user_meta( get_current_user_id(), 'title_preference', $_POST['title'] );
//    $args = array(
//        'tag' => $_POST['title'],
//    );
//    $the_query = new WP_Query( $args );
//    wp_send_json( $_POST['title'] . ' (' . $the_query->post_count . ') ' );
//}

add_action('wp_footer','artilia_jquery_single_product_selected_option');
function artilia_jquery_single_product_selected_option(){
//    wp_die();
    if ( is_product() ):
        ?>
        <script type="text/javascript">
          // Ready state
          (function($){
            $( document.body ).on( 'added_to_cart', function(){
              console.log('EVENT: added_to_cart');
            });

          })(jQuery);
        </script>
    <?php
    endif;
}

/**
 * Disable sidebar for Artilia pages
 *
 * @param bool $is_active_sidebar
 * @param int|string $index
 *
 * @return bool
 */
function artilia_remove_sidebar( $is_active_sidebar, $index ) {
//    if( $index !== "sidebar-1" ) {
//        return $is_active_sidebar;
//    }
//    if( ! is_product() ) {
//        return $is_active_sidebar;
//    }

    return false;
}

add_filter( 'is_active_sidebar', 'artilia_remove_sidebar', 10, 2 );
