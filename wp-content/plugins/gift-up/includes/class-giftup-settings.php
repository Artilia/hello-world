<?php

class giftup_settings
{
    public static $plugin;
    public static $plugin_directory;
    
    /**
     * Settings class constructor
     *
     * @param  string   $plugin_directory   name of the plugin directory
     *
     * @return void
     */
    public function __construct( $plugin, $plugin_directory )
    {
        self::$plugin = $plugin;
        self::$plugin_directory = $plugin_directory;
      
        add_action( 'init', array( __CLASS__, 'set_up_menu' ) );
    }

    /**
     * Method that is called to set up the settings menu
     *
     * @return void
     */
    public static function set_up_menu()
    {
        // Add Gift Up! settings page in the menu
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_menu' ) );
        
        // Add Gift Up! settings page in the plugin list
        add_filter( 'plugin_action_links_' . self::$plugin, array( __CLASS__, 'add_settings_link' ) );

        // Add Gift Up! notification globally
        add_action( 'admin_notices', array( __CLASS__, 'check_connected' ) );
    }
    
    /**
     * Add Gift Up! settings page in the menu
     *
     * @return void
     */
    public static function add_settings_menu() {
        add_options_page( __( 'Gift Up!' ), __( 'Gift Up!' ), 'manage_options', 'giftup-settings', array( __CLASS__, 'show_settings_page' ));
    }

    /**
     * Add Gift Up! settings page in the plugin list
     *
     * @param  mixed   $links   links
     *
     * @return mixed            links
     */
    public static function add_settings_link( $links )
    {
        $settings_link = '<a href="options-general.php?page=giftup-settings">Settings</a>';
        array_unshift( $links, $settings_link );
        
        return $links;
    }
    
    /**
     * Method that is called to warn if gift up is not connected
     *
     * @return void
     */
    public static function check_connected() {
        if (giftup_tools::get_company_id() == false) {
            echo '<div class="update-nag" id="giftup-nag"><p>' . __( 'Please <a href="/wp-admin/options-general.php?page=giftup-settings">connect/create your Gift Up! account</a> to your WordPress account to sell gift cards online' ) . '</p></div>';
        }
        if ( giftup_tools::different_roots_enabled() ) {
            echo '<div class="update-nag" id="giftup-nag-2"><p>You are pointing to a different Gift Up! environment.</p></div>';
        }
    }

    /**
     * Display Gift Up! settings page content
     *
     * @return void
     */
    public static function show_settings_page()
    {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            $response = self::do_post( $_POST );
            if (null !== $response) {
                $message  = $response['message'];
                $status   = $response['status'];
            }
        }

        $giftup_company = giftup_tools::get_company();
        $giftup_api_key = giftup_tools::get_api_key();
        $giftup_company_id = giftup_tools::get_company_id();

        $current_user = wp_get_current_user();
        $giftup_email_address = $current_user->user_email;

        $woocommerce_installed = false;
        $woocommerce_version_str = 'unknown';
        $woocommerce_api = false;
        $woocommerce_version = 0;
        $woocommerce_integration_enabled = false;
        $woocommerce_integration_can_connect = false;
        $woocommerce_integration_can_create_coupons = false;
        $woocommerce_integration_can_has_webhooks = false;
        
        foreach( get_plugins() as $plugin )
        {
            if ( $plugin['Name'] === 'WooCommerce' ) 
            {
                $woocommerce_installed = true;
                $woocommerce_version_str = $plugin['Version'];
            }
        }
        
        if ( strlen( $giftup_company_id ) > 0  && $woocommerce_installed ) {
            $host = get_site_url(null, '', 'https');
            $return_url = urlencode( get_site_url(null, '/wp-admin/options-general.php?page=giftup-settings', 'https') );
            $callback_url = urlencode( giftup_tools::api_root() . '/integrations/woocommerce/authorize?companyId=' . $giftup_company_id . '&host=' . $host );
            $woocommerce_connect = '/wc-auth/v1/authorize?app_name=Gift Up!&scope=read_write&user_id=' . $giftup_company_id . '&return_url=' . $return_url . '&callback_url=' . $callback_url;
            
            if ( strlen( $woocommerce_version_str ) > 0 ) 
            {
                $woocommerce_version = floatval( $woocommerce_version_str );
            }

            if ( giftup_tools::woocommerce_api_accessible( $woocommerce_connect ) ) 
            {
                $woocommerce_api = true;
            }

            $giftup_test = giftup_tools::giftup_can_connect_to_woocommerce();

            if ($giftup_test !== null) {
                $woocommerce_integration_enabled = $giftup_test['isConnected'];
                $woocommerce_integration_can_connect = $giftup_test['canConnect'];
                $woocommerce_integration_can_create_coupons = $giftup_test['canCreateCoupons'];
                $woocommerce_integration_can_has_webhooks = $giftup_test['hasWebhooks'];
            } 

            // Handle the deny/accept button from the authorize WooCommerce component
            if ( isset( $_GET['success'] )) 
            {
                if ( $_GET['success'] == '0' )
                {
                    $message = 'Looks like you denied Gift Up! access to your WooCommerce store. Please try again.';
                    $status = 'notice notice-warning';
                }
                if ( $_GET['success'] == '1' )
                {
                    if ( $woocommerce_integration_enabled ) {
                        $message = 'WooCommerce integration enabled.';
                        $status = 'notice notice-success';
                    } else {
                        $message = 'There has been a problem connecting to WooCommerce, please try again.';
                        $status = 'notice notice-warning';
                    }
                } 
            }
        }

        require_once self::$plugin_directory . 'view/giftup-settings.php';
    }

    /**
     * Routes processing of request parameters depending on the source section of the settings page
     *
     * @param  mixed   $params    array of parameters from $_POST
     *
     * @return mixed              response array from the save or send functions
     */
    private static function do_post( $params ) {
        $api_key = '';
        $disconnect_woocommerce = '';

        if ( isset( $params['giftup_api_key'] )) {
            $api_key = $params['giftup_api_key'];
        }
        if ( isset( $params['woocommerce'] )) {
            $disconnect_woocommerce = $params['woocommerce'];
        }

        if (strlen($disconnect_woocommerce) > 0) {
            giftup_tools::giftup_disconnect_woocommerce();

            return array(
                'message' => 'Gift Up! account disconnected from WooCommerce',
                'status' => 'notice notice-success'
            );

        } else if (strlen($api_key) == 0) {
            giftup_tools::giftup_disconnect_woocommerce();
            giftup_tools::set_api_key( '' );
            giftup_tools::set_company_id( '' );
            
            return array(
                'message' => 'Gift Up! account disconnected',
                'status' => 'error'
            );
        } else {
            $company = giftup_tools::api( '/company', 'GET', $api_key );

            if ( NULL !== $company ) {
                giftup_tools::set_api_key( $api_key );
                giftup_tools::set_company_id( $company['id'] );
    
                return;
            } else {
                return array(
                    'message' => 'Please enter a valid Gift Up! API key',
                    'status' => 'error'
                );
            }
        }
    }
}
