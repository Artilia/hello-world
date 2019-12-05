<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Popup_Maker_Advanced_Theme_Builder' ) ) :

/**
 * Main Popup_Maker_Advanced_Theme_Builder Class
 *
 * @since 1.0
 */
final class Popup_Maker_Advanced_Theme_Builder {
	/** Singleton *************************************************************/

	/**
	 * @var Popup_Maker_Advanced_Theme_Builder The one true Popup_Maker_Advanced_Theme_Builder
	 * @since 1.0
	 */
	private static $instance;
	public  static $license;

	/**
	 * Main Popup_Maker_Advanced_Theme_Builder Instance
	 *
	 * Insures that only one instance of Popup_Maker_Advanced_Theme_Builder exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @uses Popup_Maker_Advanced_Theme_Builder::setup_constants() Setup the constants needed
	 * @uses Popup_Maker_Advanced_Theme_Builder::includes() Include the required files
	 * @uses Popup_Maker_Advanced_Theme_Builder::load_textdomain() load the language files
	 * @see PopMake()
	 * @return The one true Popup_Maker_Advanced_Theme_Builder
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Popup_Maker_Advanced_Theme_Builder ) ) {
			self::$instance = new Popup_Maker_Advanced_Theme_Builder;
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->load_textdomain();

			if ( class_exists( 'PopMake_License' ) && is_admin() ) {
			  self::$license = new PopMake_License( __FILE__, POPMAKE_ATB_NAME, POPMAKE_ATB_VERSION, 'Daniel Iser' );
			}
		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'popup-maker-advanced-theme-builder' ), '3' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'popup-maker-advanced-theme-builder' ), '3' );
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {

		if ( !defined('POPMAKE_ATB') ) {
			define('POPMAKE_ATB', __FILE__);	
		}

		if ( !defined('POPMAKE_ATB_NAME') ) {
			define('POPMAKE_ATB_NAME', 'Advanced Theme Builder');	
		}

		if ( !defined('POPMAKE_ATB_SLUG') ) {
			define('POPMAKE_ATB_SLUG', trim(dirname(plugin_basename(__FILE__)), '/'));	
		}

		if ( !defined('POPMAKE_ATB_DIR') ) {
			define('POPMAKE_ATB_DIR', PUM_ATB::$DIR . 'deprecated/');
		}

		if ( !defined('POPMAKE_ATB_URL') ) {
			define('POPMAKE_ATB_URL', PUM_ATB::$URL . 'deprecated/' );
		}

		if ( !defined('POPMAKE_ATB_NONCE') ) {
			define('POPMAKE_ATB_NONCE', POPMAKE_ATB_SLUG.'_nonce' );	
		}

		if ( !defined('POPMAKE_ATB_VERSION') ) {
			define('POPMAKE_ATB_VERSION', PUM_ATB::$VER );
		}

	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {


		require_once POPMAKE_ATB_DIR . 'includes/scripts.php';
		require_once POPMAKE_ATB_DIR . 'includes/defaults.php';
		require_once POPMAKE_ATB_DIR . 'includes/input-options.php';
		require_once POPMAKE_ATB_DIR . 'includes/css-functions.php';

		if ( is_admin() ) {
			require_once POPMAKE_ATB_DIR . 'includes/admin/admin-setup.php';
			require_once POPMAKE_ATB_DIR . 'includes/admin/themes/metabox.php';
			require_once POPMAKE_ATB_DIR . 'includes/admin/themes/metabox-overlay-fields.php';
			require_once POPMAKE_ATB_DIR . 'includes/admin/themes/metabox-container-fields.php';
			require_once POPMAKE_ATB_DIR . 'includes/admin/themes/metabox-close-fields.php';
		}
	}

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {
		// Set filter for plugin's languages directory
		$popmake_atb_lang_dir = dirname( plugin_basename( POPMAKE_ATB ) ) . '/languages/';
		$popmake_atb_lang_dir = apply_filters( 'popmake_atb_languages_directory', $popmake_atb_lang_dir );

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'popup-maker-advanced-theme-builder' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'popup-maker-advanced-theme-builder', $locale );

		// Setup paths to current locale file
		$mofile_local  = $popmake_atb_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/popup-maker/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/popup-maker folder
			load_textdomain( 'popup-maker-advanced-theme-builder', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/popup-maker/languages/ folder
			load_textdomain( 'popup-maker-advanced-theme-builder', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'popup-maker-advanced-theme-builder', false, $popmake_atb_lang_dir );
		}
	}
}

endif; // End if class_exists check


/**
 * The main function responsible for returning the one true Popup_Maker_Advanced_Theme_Builder
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $popmake_atb = PopMakeAdvancedThemeBuilder(); ?>
 *
 * @since 1.0
 * @return object The one true Popup_Maker_Advanced_Theme_Builder Instance
 */

function PopMakeAdvancedThemeBuilder() {
	return Popup_Maker_Advanced_Theme_Builder::instance();
}


function popmake_atb_initialize() {
	PopMakeAdvancedThemeBuilder();
}
add_action('popmake_initialize', 'popmake_atb_initialize');