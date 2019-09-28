<?php
/*
Plugin Name: Gravity Forms Debug Add-On
Plugin URI: https://www.gravityforms.com
Description: Helps debug issues with Gravity Forms.
Version: 1.0.beta11
Author: rocketgenius
Author URI: https://www.rocketgenius.com
License: GPL-2.0+
Text Domain: gravityformsdebug
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2012-2019 Rocketgenius Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

// If the add-on framework isn't there, bail.
if( ! is_callable( array( 'GFForms', 'include_addon_framework' ) ) ) {
	return;
}

// Include the addon framework.
GFForms::include_addon_framework();


/**
 * Class GFDebug
 */
class GFDebug extends GFAddOn {

	/**
	 * @var string
	 */
    protected $_version = '1.0.beta11';

	/**
	 * @var string
	 */
    protected $_min_gravityforms_version = '2.2';

	/**
	 * @var string
	 */
    protected $_slug = 'gravityformsdebug';

	/**
	 * @var string
	 */
    protected $_path = 'gravityformsdebug/debug.php';

	/**
	 * @var string
	 */
    protected $_full_path = __FILE__;

	/**
	 * @var string
	 */
    protected $_title = 'Gravity Forms Debug Add-On';

	/**
	 * @var string
	 */
    protected $_short_title = 'Debug';

	/**
	 * @var string
	 */
    protected $_capabilities_plugin_page = 'activate_plugins';

	/**
	 * @var string
	 */
    public $_mu_plugin_file = 'gf_mu_debug.php';

	/**
	 * @var array
	 */
    public $_gf_plugins = array();

	/**
	 * @var array
	 */
    public $_immune_plugins = array(
        'Gravity Forms',
        'Gravity Forms Debug Add-On'
    );

	/**
	 * @var array
	 */
	public $_default_themes = array(
		'Twenty Nineteen'  => 'twentynineteen',
		'Twenty Seventeen' => 'twentyseventeen',
		'Twenty Sixteen'   => 'twentysixteen',
		'Twenty Fifteen'   => 'twentyfifteen',
		'Twenty Fourteen'  => 'twentyfourteen',
		'Twenty Thirteen'  => 'twentythirteen',
		'Twenty Twelve'    => 'twentytwelve',
		'Twenty Eleven'    => 'twentyeleven',
		'Twenty Ten'       => 'twentyten'
	);

	/**
	 * @var null
	 */
    private static $instance = null;

	/**
	 * @return GFDebug|null
	 */
    public static function get_instance() {

        if ( self::$instance == null ) {
            self::$instance = new self;
		}

        return self::$instance;
    }

	/**
	 * GFDebug constructor.
	 */
    public function __construct() {

        add_action( 'init', array( $this, 'maybe_handle_settings_submission' ), 1 );

        parent::__construct();

    }

	/**
     * Prevent the add-on being listed on the logging tab.
     *
     * @param array $plugins Add-ons which support logging.
     *
     * @return array
     */
    public function set_logging_supported( $plugins ) {
        return $plugins;
    }

	/**
	 *
	 */
    public function plugin_page() {

        $this->page_header();

        $func = $this->get_current_subview() . '_page';

        if( is_callable( array( $this, $func ) ) ) {
            call_user_func( array( $this, $func ) );
        } else {
            die( __( 'This subview does not exist.', 'gravityformsdebug' ) );
        }

        $this->page_footer();

    }

	/**
	 *
	 */
    public function conflict_tester_page() {
        ?>

        <form method="post" action="" id="gdebug-form">

            <h2><?php _e( 'Conflict Tester', 'gravityformsdebug' ); ?></h2>

            <input type="hidden" name="<?php echo $this->_slug; ?>_conflict_tester_settings" value="1" />
            <input type="hidden" name="action" id="gdebug-action" value="" />
            <?php wp_nonce_field( $this->prefix( 'ct_settings_submission' ), $this->prefix( 'ct_settings_submission' ) ); ?>

            <?php $this->conflict_tester_settings_ui(); ?>

        </form>

        <style type="text/css">
            #tab_conflict_tester h2 { margin: 0 0 24px; }
            #gdebug-form { }
            #gdebug-form .button { vertical-align: middle; }
            .gdebug-ct-plugins-select-all { padding: 0 20px; margin: 0 0 20px; }
            .gdebug-ct-plugins { overflow: hidden; border: 1px solid #eee; padding: 20px; margin: 0 0 20px; background-color: #fff; }
            .gdebug-ct-plugins li { width: 50%; float: left; }
            .gdebug-ct-plugins li.active label { font-weight: bold !important; }
            .gdebug-ct-plugin-actions { margin: 0 0 20px; }
            .gdebug-ct-plugin-actions li { }
            .gdebug-ct-plugins .gdebug-select-all { width: 100%; border-bottom: 1px dotted #eee; margin: 0 0 10px; padding: 0 0 10px; }
            .gdebug-plugin-actions-row { }
            .gdebug-plugin-actions-row .gdebug-selected-label { margin-left: 10px; }
            .gdebug-theme-file { color: #999; }
            .wp-active label:after {
                font-family: 'FontAwesome', sans-serif;
                content: "\f069";
                padding-left: 5px;
                opacity: 0.3;
            }
            .wp-active-note { color: #999; float: right; }
            .wp-active-note i { color: #000; opacity: 0.3; }
        </style>

        <script type="text/javascript">

            jQuery( document ).ready( function( $ ){


	            var $pluginInputs = $( '.gdebug-ct-plugins input' );

	            $pluginInputs.click( function() {
                    $( 'button#gdebug-save-active-plugins' ).prop( 'disabled', false );
                } );

	            $pluginInputs.not( '.gdebug-select-all input' ).click( function() {
                    if( ! $( this ).is( ':checked' ) )
                        $( this ).parents( '.gdebug-ct-plugins' ).find( '.gdebug-select-all input' ).prop( 'checked', false );
                } );

            } );

            ( function( $, GDebug ){

                GDebug.setActionSubmit = function( action ) {

                    var actionInput = $( 'input#gdebug-action' ),
                        form        = $( 'form#gdebug-form' );

                    actionInput.val( action );
                    form.submit();

                }

            })( jQuery, window.GDebug = window.GDebug || {} );

        </script>

        <?php

    }

	/**
	 *
	 */
    private function conflict_tester_settings_ui() {

        if( $this->is_conflict_tester_enabled() ) {

            $is_installed = $this->install_mu_plugin();
            $plugins = get_plugins();
            $default_theme = $this->get_default_theme();

            if( ! $is_installed ): ?>
                <div id="message" class="error"><p><?php _e( 'There was an issue installing the conflict tester.', 'gravityformdebug' ); ?></p></div>
            <?php endif;

        }

        ?>

        <table class="form-table">

            <tbody>

                <tr valign="top">
                    <th scope="row"><label for=""><?php _e( 'Conflict Tester Status', 'gravityformsdebug' ); ?></label></th>
                    <td>
                        <?php if( $this->is_conflict_tester_enabled() ): ?>
                            <button class="button" type="button" onclick="GDebug.setActionSubmit( 'disable' );"><?php _e( 'Disable Conflict Tester', 'gravityformsdebug' ); ?></button>
                        <?php else: ?>
                            <button class="button" type="button" onclick="GDebug.setActionSubmit( 'enable' );"><?php _e( 'Enable Conflict Tester', 'gravityformsdebug' ); ?></button>
	                        <?php if( rgget( 'error' ) && rgget( 'disabled' ) ): ?>
	                            <?php $this->disable_conflict_tester( false ); ?>
		                        <span id="enable-ct-message" class="gf_keystatus_invalid_text" style="margin-left:10px;"><i class="fa fa-times"></i> Conflict Tester cannot be enabled on this site.</i></span>
	                        <?php endif; ?>
	                        <script type="text/javascript"> setTimeout( function() { jQuery( '#enable-ct-message' ).fadeOut(); }, 5000 ); </script>
                            <p class="description"><?php _e( 'All plugins will be disabled and a default WordPress theme will be used when enabled. This only applies to the currently logged in user. The currently active plugins and theme will load for all other users and visitors.' ); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>

                <?php if( $this->is_conflict_tester_enabled() ): ?>

	                <tr valign="top" class="gdebug-theme-row">
	                    <th><label for=""><?php _e( 'Default Theme', 'gravityformsdebug' ); ?></label></th>
	                    <td>

	                        <?php if( ! $default_theme ): ?>

	                            <?php

	                            $theme_names = array_keys( $this->_default_themes );
	                            $theme_name = rgar( $theme_names, 0 );

	                            printf(
	                                __( 'You do not have any WordPress default themes installed. Please install %1$s%3$s%2$s to complete a full conflict test.' ),
	                                '<a href="' . admin_url( 'theme-install.php?search=' . urlencode( $theme_name ) ) . '">',
	                                '</a>',
	                                $theme_name
	                            );

	                            ?>

	                        <?php else: ?>

	                            <?php printf(
	                                '%s <span class="gdebug-theme-file">(%s)</span>',
	                                $default_theme->name, $default_theme->template
	                            ); ?>

	                        <?php endif; ?>

	                    </td>
	                </tr>

	                <tr valign="top" class="gdebug-plugin-actions-row">
		                <th><label for=""><?php _e( 'Plugins', 'gravityformsdebug' ); ?></label></th>
		                <td>
		                    <button disabled="disabled" class="button" id="gdebug-save-active-plugins" onclick="GDebug.setActionSubmit( 'activate_plugins' );">
		                        <?php _e( 'Save Active Plugins', 'gravityformsdebug' ); ?>
		                    </button>
			                <?php if( rgget( 'action' ) == 'active-plugins-updated' ): ?>
				                <span id="active-plugins-message" class="gf_keystatus_valid_text" style="margin-left:10px;"><i class="fa fa-check"></i> Active plugins saved.</i></span>
			                <?php elseif( rgget( 'error' ) ): ?>
				                <span id="active-plugins-message" class="gf_keystatus_invalid_text" style="margin-left:10px;"><i class="fa fa-times"></i> One or more of the plugins generated an error.</i></span>
			                <?php endif; ?>
			                <script type="text/javascript"> setTimeout( function() { jQuery( '#active-plugins-message' ).fadeOut(); }, 5000 ); </script>
		                </td>
		            </tr>

	                <tr>
	                    <td colspan="2">

	                        <h4><?php _e( 'Gravity Forms Plugins', 'gravityformdebug' ); ?></h4>
	                        <ul class="gdebug-ct-plugins gdebug-ct-gf-plugins">

	                            <li class="gdebug-select-all">
	                                <input type="checkbox" id="gdebug-select-all-gf" onclick="jQuery( '.gdebug-ct-gf-plugins input' ).prop( 'checked', jQuery( this ).is( ':checked' ) );" />
	                                <label for="gdebug-select-all-gf"><?php _e( 'Select All', 'gravityformsdebug' ); ?></label>
                                    <span class="wp-active-note">
                                        <i class="fa fa-asterisk"></i> <?php _e( 'Denotes Orginally Active Plugin', 'gravityformsdebug' ); ?>
                                    </span>
	                            </li>

	                            <?php foreach( $plugins as $slug => $plugin ) {

	                                if( ! in_array( $plugin['Name'], $this->get_gf_plugins() ) || in_array( $plugin['Name'], $this->_immune_plugins ) )
	                                    continue;

                                    $is_active = $this->is_plugin_active( $slug );

                                    $classes = array();
                                    if( $is_active ) {
                                        $classes[] = 'active';
                                    }

                                    // is plugin active outside of debugger?
                                    if( $this->is_plugin_wp_active( $slug ) ) {
                                        $classes[] = 'wp-active';
                                    }

                                    printf(
                                        '<li class="%2$s"><input type="checkbox" name="plugins[]" id="%4$s" %3$s value="%4$s" /><label for="%4$s">%1$s</label></li>',
                                        $plugin['Name'],
                                        implode( ' ', $classes ),
                                        $is_active ? 'checked="checked"' : '',
                                        $slug
                                    );

	                            } ?>
	                        </ul>

	                        <h4><?php _e( 'Other Plugins', 'gravityformdebug' ); ?></h4>
	                        <ul class="gdebug-ct-plugins gdebug-ct-other-plugins">

	                            <li class="gdebug-select-all">
	                                <input type="checkbox" id="gdebug-select-all-other" onclick="jQuery( '.gdebug-ct-other-plugins input' ).prop( 'checked', jQuery( this ).is( ':checked' ) );" />
	                                <label for="gdebug-select-all-other"><?php _e( 'Select All', 'gravityformsdebug' ); ?></label>
                                    <span class="wp-active-note">
                                        <i class="fa fa-asterisk"></i> <?php _e( 'Denotes Orginally Active Plugin', 'gravityformsdebug' ); ?>
                                    </span>
	                            </li>

	                            <?php foreach( $plugins as $slug => $plugin ) {

	                                if( in_array( $plugin['Name'], $this->get_gf_plugins() ) || in_array( $plugin['Name'], $this->_immune_plugins ) )
	                                    continue;

	                                $is_active = $this->is_plugin_active( $slug );

                                    $classes = array();
                                    if( $is_active ) {
                                        $classes[] = 'active';
                                    }

                                    // is plugin active outside of debugger?
                                    if( $this->is_plugin_wp_active( $slug ) ) {
                                        $classes[] = 'wp-active';
                                    }

	                                printf(
	                                    '<li class="%2$s"><input type="checkbox" name="plugins[]" id="%4$s" %3$s value="%4$s" /><label for="%4$s">%1$s</label></li>',
	                                    $plugin['Name'],
	                                    implode( ' ', $classes ),
	                                    $is_active ? 'checked="checked"' : '',
	                                    $slug
	                                );

	                            } ?>

	                        </ul>

	                    </td>
	                </tr>

                <?php endif; ?>

                <tr valign="top" class="gdebug-ct-disable-link">
	                <th><label for=""><?php _e( 'Disable Link', 'gravityformsdebug' ); ?></label></th>
	                <td>
		                <input type="text" value="<?php echo $this->get_disable_url(); ?>" style="width:90%;" onclick="jQuery( this ).select();" />
		                <p class="description"><?php _e( 'To manually disable the Conflict Tester, use the above URL.' ); ?></p>
	                </td>
                </tr>

            </tbody>
        </table>

        <?php

    }

    /**
     * Get the default WordPress theme available.
     *
     * @access public
     * @return string|bool
     */
    public function get_default_theme() {

        // Get installed themes.
        $themes = wp_get_themes();

		// Loop through WordPress default themes.
        foreach( $this->_default_themes as $default_theme ) {
	        
	        // Loop through installed themes.
            foreach( $themes as $theme ) {
	            
	            // If installed theme is a default theme, return it.
                if ( $theme->template == $default_theme ) {
                    return $theme;
				}

            }

        }

        return false;

    }

    /**
     * Get list of official Gravity Forms plugins.
     *
     * @access public
     * @return array
     */
    public function get_gf_plugins() {

		// If list of Gravity Forms plugins has already been populated, return it.
		if ( ! empty( $this->_gf_plugins ) ) {
			return $this->_gf_plugins;
		}

		// Get the list of plugins from the Gravity Manager.
		$plugins_list = wp_remote_get( GRAVITY_MANAGER_URL . '/api.php?op=get_plugins' );

		// If plugins list response is an error, return the Gravity Forms plugins property.
		if ( is_wp_error( $plugins_list ) ) {
			return $this->_gf_plugins;
		}

		// Unserialize the plugins list.
		$plugins_list = maybe_unserialize( $plugins_list['body'] );

		// Loop through the plugins.
		foreach ( $plugins_list as $plugin ) {

			// Get the plugin title.
			$plugin = rgar( $plugin, 'title' );

			// If the plugin title is empty, skip it.
			if ( rgblank( $plugin ) ) {
				continue;
			}

			// If the plugin title does not start with Gravity Forms, add it.
			if ( strpos( $plugin, 'Gravity Forms' ) !== 0 ) {
				$plugin = 'Gravity Forms ' . $plugin;
			}

			// Add plugin to list.
			$this->_gf_plugins[] = $plugin;

			// If this is the PayPal Add-On, add PayPal Standard to the plugins list.
			if ( strpos( $plugin, 'PayPal Add-On' ) !== false ) {
				$this->_gf_plugins[] = 'Gravity Forms PayPal Standard Add-On';
			}

		}

	    return $this->_gf_plugins;

	}

    /**
     * Check if a specific plugin is active.
     * 
     * @access public
     * @param  string $slug Plugin slug.
     * @return bool
     */
    public function is_plugin_active( $slug ) {

        $active_plugins = get_user_meta( get_current_user_id(), $this->prefix( 'ct_active_plugins' ), true );
        if( ! is_array( $active_plugins ) )
            return false;

        foreach( $active_plugins as $plugin ) {
            if( $plugin == $slug )
                return true;
        }

        return false;
    }

	/**
	 * @param $slug
	 *
	 * @return bool
	 */
    public function is_plugin_wp_active( $slug ) {

        if( function_exists( 'gf_mu_debug' ) && is_array( gf_mu_debug()->wp_active_plugins ) ) {
            $is_active = in_array( $slug, gf_mu_debug()->wp_active_plugins );
        } else {
            $is_active = is_plugin_active( $slug );
        }

        return $is_active;
    }

	/**
	 *
	 */
    public function maybe_handle_settings_submission() {

        if( $this->is_settings_submission() ) {
	        $this->handle_settings_submission();
        }

    }

	/**
	 *
	 */
    protected function handle_settings_submission() {

        $action = rgpost( 'action' );
        if( ! $action )
            return;

        switch( $action ) {
            case 'enable':
                $this->enable_conflict_tester();
                wp_redirect( remove_query_arg( null ) );
            break;
            case 'disable':
                $this->disable_conflict_tester();
                wp_redirect( remove_query_arg( null ) );
            break;
            case 'activate_plugins':
                $plugins = rgpost( 'plugins' );
                $this->update_active_plugins( $plugins, self_admin_url( 'admin.php?page=gravityformsdebug' ) );
            break;
        }

    }

	/**
	 *
	 */
    public function enable_conflict_tester() {

	    setcookie( 'gravityformsdebug_ct_enabled', true, null, '/' );
	    setcookie( 'gravityformsdebug_ct_user_id', get_current_user_id(), null, '/' );
	    setcookie( 'gravityformsdebug_ct_error_check', true, null, '/' );

	    $is_installed = $this->install_mu_plugin();

    }

	/**
	 * @param bool $delete_cookies
	 *
	 * @return bool
	 */
    public function disable_conflict_tester( $delete_cookies = true ) {

        $is_uninstalled = $this->uninstall_mu_plugin();
        $is_active_plugins_cleared = delete_user_meta( get_current_user_id(), $this->prefix( 'ct_active_plugins' ) );

	    if( $delete_cookies ) {

		    unset( $_COOKIE['gravityformsdebug_ct_enabled'] );
		    unset( $_COOKIE['gravityformsdebug_ct_user_id'] );

		    setcookie( 'gravityformsdebug_ct_enabled', null, null, '/' );
		    setcookie( 'gravityformsdebug_ct_user_id', null, null, '/' );

	    }

        return $is_uninstalled && $is_active_plugins_cleared;
    }

	/**
	 * @return mixed|null|string
	 */
    public function is_conflict_tester_enabled() {
        return rgar( $_COOKIE, 'gravityformsdebug_ct_enabled' );
    }

	/**
	 * @param $plugins
	 * @param $redirect
	 *
	 * @return bool|int
	 */
    protected function update_active_plugins( $plugins, $redirect ) {

	    if( ! $plugins ) {
		    $plugins = array();
	    }

	    // make sure including any of the plugins will not generate a fatal error
	    foreach( $plugins as $plugin ) {

		    wp_redirect( add_query_arg( array(
			    '_error_nonce' => wp_create_nonce( 'plugin-activation-error_' . $plugin ),
			    'error'        => true,
			    'plugin'       => $plugin
		    ), $redirect ) );

		    ob_start();
		    wp_register_plugin_realpath( WP_PLUGIN_DIR . '/' . $plugin );
		    include_once( WP_PLUGIN_DIR . '/' . $plugin );
		    ob_get_clean();

	    }

	    wp_redirect( admin_url( 'admin.php?page=gravityformsdebug&action=active-plugins-updated' ) );

	    return update_user_meta( get_current_user_id(), $this->prefix( 'ct_active_plugins' ), $plugins );
    }

	/**
	 * @return bool
	 */
    protected function is_settings_submission() {

        $is_submit = rgpost( $this->prefix( 'conflict_tester_settings' ) ) == true;

        return $is_submit && check_admin_referer( $this->prefix( 'ct_settings_submission' ), $this->prefix( 'ct_settings_submission' ) );
    }

	/**
	 * @return bool
	 */
    private function install_mu_plugin() {

        if( $this->mu_plugin_file_exists() )
            return true;

        if( ! $this->mu_plugins_folder_exists() && ! $this->create_mu_plugins_folder() )
            return false;

        $source_path = $this->get_base_path() . "/includes/{$this->_mu_plugin_file}";
        $target_path = $this->get_mu_plugins_dir() . "/{$this->_mu_plugin_file}";

        $result = copy( $source_path, $target_path );

        chmod( $target_path, 0775 );

        return $result;
    }

	/**
	 * @return bool
	 */
    private function uninstall_mu_plugin() {

        $target_path = $this->get_mu_plugins_dir() . "/{$this->_mu_plugin_file}";
        if( ! file_exists( $target_path ) )
            return true;

        return unlink( $target_path );
    }

	/**
	 * @param $name
	 *
	 * @return string
	 */
    final public function prefix( $name ) {
        return "{$this->_slug}_{$name}";
    }

	/**
	 * @return bool
	 */
    public function mu_plugin_file_exists() {
        return file_exists( $this->get_mu_plugins_dir() . "/{$this->_mu_plugin_file}" );
    }

	/**
	 * @return bool
	 */
    public function mu_plugins_folder_exists() {
        return file_exists( $this->get_mu_plugins_dir() );
    }

	/**
	 * @return bool
	 */
    public function create_mu_plugins_folder() {
        return mkdir( $this->get_mu_plugins_dir(), 0775, true );
    }

	/**
	 * @return string
	 */
    public function get_mu_plugins_dir() {
        return WP_CONTENT_DIR . '/mu-plugins';
    }

	/**
	 * @param string $title
	 */
    public function page_header( $title = '' ){

        // register admin styles
        wp_register_style( 'gform_admin', GFCommon::get_base_url() . '/css/admin.css' );
        wp_print_styles( array( 'jquery-ui-styles', 'gform_admin', 'wp-pointer' ) );

        // get view details
        $subviews = $this->get_subviews();

        ?>

        <div class="wrap <?php echo GFCommon::get_browser_class() ?>">

            <?php GFCommon::display_admin_message(); ?>

            <div id="gform_tab_group" class="gform_tab_group vertical_tabs">

                <ul id="gform_tabs" class="gform_tabs">
                    <?php foreach( $subviews as $view ):
                        $query = array( 'subview' => $view['name'] );
                        if( isset( $view['query'] ) )
                            $query = array_merge( $query, $view['query'] );
                        ?>
                        <li <?php echo $this->get_current_subview() == $view['name'] ? 'class="active"' : '' ?>>
                            <a href="<?php echo add_query_arg( $query ); ?>"><?php echo $view['label'] ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div id="gform_tab_container" class="gform_tab_container">
                    <div class="gform_tab_content" id="tab_<?php echo $this->get_current_subview() ?>">

        <?php
    }

    /**
     *
     */
    public function page_footer() {
        ?>

                    </div> <!-- / gform_tab_content -->
                </div> <!-- / gform_tab_container -->
            </div> <!-- / gform_tab_group -->

            <br class="clear" style="clear: both;" />

        </div> <!-- / wrap -->

        <script type="text/javascript">
            jQuery(document).ready( function( $ ) {
                $( '.gform_tab_container' ).css( 'minHeight', jQuery( '#gform_tabs' ).height() + 100 );
            } );
        </script>

        <?php
    }

	/**
	 * @return array
	 */
    public function get_subviews() {

        $subviews = array(
            '10' => array(
                'name' => 'conflict_tester',
                'label' => __( 'Conflict Tester', 'gravityformsdebug' )
            )
        );

        ksort( $subviews, SORT_NUMERIC );

        return $subviews;
    }

	/**
	 * @return string
	 */
    public function get_current_subview() {
        return rgempty( 'subview', $_GET ) ? 'conflict_tester' : rgget( 'subview' );
    }

	/**
	 * @return string
	 */
	public function get_disable_url() {
		return add_query_arg( array( 'gf_disable_conflict_tester' => 1 ), home_url() . '/' );
	}

}

/**
 * @return GFDebug|null
 */
function gravity_forms_debug() {
    return GFDebug::get_instance();
}

// Runs the debugging plugin.
gravity_forms_debug();