<?php

/**
 * Class GFMUDebug
 */
class GFMUDebug {

	/**
	 * @var array
	 */
	public $always_load_plugins = array( 'gravityforms/gravityforms.php', 'gravityformsdebug/debug.php' );

	/**
	 * @var null
	 */
	public $wp_active_plugins   = null; // plugins that are "actually" active

	/**
	 * @var null
	 */
	private static $instance = null;

	/**
	 * @return GFMUDebug|null
	 */
	public static function get_instance() {

		if( null == self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * GFMUDebug constructor.
	 */
	private function __construct() {

		$this->init();

	}

	/**
	 *
	 */
	public function init() {

		if( isset( $_GET['gf_disable_conflict_tester'] ) && $_GET['gf_disable_conflict_tester'] ) {
			$this->disable_conflict_tester();
			return;
		}

		if( ! $this->is_enabled() ) {
			return;
		}

		if( isset( $_COOKIE['gravityformsdebug_ct_error_check'] ) && $_COOKIE['gravityformsdebug_ct_error_check'] ) {
			header( sprintf( 'Location: %s?gf_disable_conflict_tester=1', home_url() ), true );
			ob_start();
			add_action( 'admin_footer', array( $this, 'cancel_disable_redirect' ), 100 );
			add_action( 'wp_footer',    array( $this, 'cancel_disable_redirect' ), 100 );
		}

		add_filter( 'option_active_plugins', array( $this, 'filter_plugins' ) );
		add_filter( 'template',              array( $this, 'filter_template' ) );
		add_filter( 'stylesheet',            array( $this, 'filter_stylesheet' ) );

	}

	/**
	 * @return bool
	 */
	private function is_enabled() {
		return isset( $_COOKIE['gravityformsdebug_ct_enabled'] ) && $_COOKIE['gravityformsdebug_ct_enabled'] == true;
	}

	/**
	 *
	 */
	private function disable_conflict_tester() {
		unset( $_COOKIE['gravityformsdebug_ct_enabled'] );
		unset( $_COOKIE['gravityformsdebug_ct_user_id'] );
		setcookie( 'gravityformsdebug_ct_enabled', null, null, '/' );
		setcookie( 'gravityformsdebug_ct_user_id', null, null, '/' );
		header( sprintf( 'Location: %s', admin_url( 'admin.php?page=gravityformsdebug&error=true&disabled=1' ) ) );
		exit;
	}

	/**
	 * @param $user_id
	 *
	 * @return mixed
	 */
	private function get_active_plugins( $user_id ) {
		return get_user_meta( $user_id, 'gravityformsdebug_ct_active_plugins', true );
	}

	/**
	 * @param $plugins
	 *
	 * @return array
	 */
	public function filter_plugins( $plugins ) {

		if( $this->wp_active_plugins === null ) {
			$this->wp_active_plugins = $plugins;
		}

		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		$plugins = get_plugins();

		$active_plugins = $this->get_active_plugins( $this->get_current_user_id() );
		if( ! is_array( $active_plugins ) ) {
			$active_plugins = array();
		}

		$valid_plugins = array();

		foreach( $plugins as $plugin => $plugin_data ) {
			if( in_array( $plugin, $active_plugins ) || in_array( $plugin, $this->always_load_plugins ) ) {
				$valid_plugins[] = $plugin;
			}
		}

		return $valid_plugins;
	}

	/**
	 * @param $template
	 *
	 * @return mixed
	 */
	public function filter_template( $template ) {

		$default_theme = gravity_forms_debug()->get_default_theme();

		return ! $default_theme ? $template : $default_theme->template;
	}

	/**
	 * @param $stylesheet
	 *
	 * @return mixed
	 */
	public function filter_stylesheet( $stylesheet ) {
		return $this->filter_template( $stylesheet );
	}

	/**
	 * @return bool
	 */
	public function get_current_user_id() {
		return isset( $_COOKIE['gravityformsdebug_ct_user_id'] ) && $_COOKIE['gravityformsdebug_ct_user_id'] ? $_COOKIE['gravityformsdebug_ct_user_id'] : false;
	}

	/**
	 *
	 */
	public function cancel_disable_redirect() {
		header( sprintf( 'Location: %s', admin_url( 'admin.php?page=gravityformsdebug' ) ), true );
		setcookie( 'gravityformsdebug_ct_error_check', null, null, '/' );
		ob_get_clean();
		exit;
	}

}

/**
 * @return GFMUDebug|null
 */
function gf_mu_debug() {
	return GFMUDebug::get_instance();
}

// Runs the MU plugin required by the main Gravity Forms Debug plugin.
gf_mu_debug();