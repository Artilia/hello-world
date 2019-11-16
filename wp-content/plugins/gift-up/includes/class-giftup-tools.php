<?php

class giftup_tools
{
    /**
    * Execute an API call to Gift Up!
    *
    * @return  string
    */
    public static function api( $endpoint, $method = "GET", $api_key = null, $data = null ) {
        $root = self::api_root();
        $url = esc_url_raw( $root . $endpoint );
        $response = false;
        $json = "";
        
        if ($data !== NULL) {
            $json = json_encode( $data );
        }

        if ($api_key === NULL) {
            $api_key = self::get_api_key();
        }

        $plugin_data = get_plugin_data( realpath(dirname(__FILE__) . '/..') . '/giftup.php' );
        $version = $plugin_data['Version'];

        $args = array(
            'timeout' => 60,
            'body' => $json,
            'headers' => array(
                'Authorization' => 'bearer ' . $api_key,
                'Content-Type' => 'application/json',
                'X-WordPress-Plugin-Version' => $version
            )
        );

        if ($data !== NULL) {
            $json = json_encode( $data );
            array_push( $args, 'body: ' . strlen($json) );
        }
        
        if ($method === "GET") {
            $response = wp_remote_get( $url, $args );
        }
        else if ($method === "POST") {
            $response = wp_remote_post( $url, $args );
        }
        else {
            $args = array(
                'method' => $method
            );
            $response = wp_remote_request( $url, $args );
        }
        
        $code = wp_remote_retrieve_response_code( $response );

        
        if (is_wp_error($response)) {
            $error = $response->get_error_message();
            
            echo '<div id="message" class="notice notice-error">';
            echo '<p>';
            echo '<strong>';
            echo 'Error talking to Gift Up! at ' . $url . ' - ' . $error . '<br>';
            if (strpos($error, 'tls') !== false){
                echo '<br>The Gift Up! plugin requires that your PHP version is 5.6+ and cURL supports TLS1.2.<br>';
                echo 'Please conduct a TLS 1.2 Compatibility Test via <a href="https://wordpress.org/plugins/tls-1-2-compatibility-test/" target="_blank">this plugin</a>';
            }
            echo '</strong>';
            echo '</p>';
            echo '</div>';
        }
        else if ( is_array( $response ) && $code >= 200 && $code < 300 ) {
            return json_decode( wp_remote_retrieve_body( $response ), true );
        }
        
        return null;
    }

    public static function giftup_can_connect_to_woocommerce()
    {
        $response = self::api( '/integrations/woocommerce/test' );

        if ($response !== NULL) {
            return $response;
        }
        
        return null;
    }

    public static function giftup_disconnect_woocommerce()
    {
        $response = self::api( '/integrations/woocommerce/disconnect', 'POST' );
    }

    public static function woocommerce_api_accessible( $url )
    {
        if ( strlen($url) == 0 ) {
            $url = '/wp-json/wc/v2';
        }
        
        $url = get_site_url(null, $url, 'https');
        $args = array(
            'timeout' => 60
        );

        $response = wp_remote_get( $url, $args );
        $code = wp_remote_retrieve_response_code( $response );
        if ( $code >= 200 && $code < 300 ) {
            return true;
        }

        return false;
    }

    public static function api_root()
    {
        if (isset( $_COOKIE['giftup_api_root'] )) {
            return $_COOKIE['giftup_api_root'];
        }

        return 'https://giftup.app/api';
    }

    public static function dashboard_root()
    {
        if (isset( $_COOKIE['giftup_dashboard_root'] )) {
            return $_COOKIE['giftup_dashboard_root'];
        }

        return 'https://giftup.app';
    }

    public static function different_roots_enabled()
    {
        if (isset( $_COOKIE['giftup_dashboard_root'] ) or isset( $_COOKIE['giftup_api_root'] )) {
            return true;
        }

        return false;
    }
    
    /**
    * Get the company name
    *
    * @return  string
    */
    public static function get_company() {
        $company = self::api( '/company' );
        
        if ($company !== NULL) {
            return $company;
        }
        
        return null;
    }
    
    /**
    * Get the company id
    *
    * @return  string
    */
    public static function get_company_id() {
        return get_option( "company_id" );
    }
    
    /**
    * Set the company id
    *
    * @return  string
    */
    public static function set_company_id( $value ) {
        return update_option( "company_id", $value );
    }

    /**
    * Get the api key
    *
    * @return  string
    */
    public static function get_api_key() {
        return get_option( "api_key" );
    }
    
    /**
    * Set the api key
    *
    * @return  string
    */
    public static function set_api_key( $value ) {
        return update_option( "api_key", $value );
    }

    /**
    * Returns a giftup plugin option
    *
    * @return  string
    */
    private static function get_option( $option, $default = false ) {
        return get_option( "giftup_$option", $default );
    }
    
    /**
    * Updates a giftup plugin option
    *
    * @return  string
    */
    private static function update_option( $option, $value ) {
        return update_option( "giftup_$option", $value );
    }
    
    /**
    * Removes all giftup plugin options (invoked from uninstall)
    *
    * @return  string
    */
    public static function remove_all_options() {
        delete_option( "giftup_company_id" );
        delete_option( "giftup_api_key" );
    }
}
