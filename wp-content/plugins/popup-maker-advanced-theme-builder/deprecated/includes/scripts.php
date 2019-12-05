<?php
/**
 * Scripts
 *
 * @package		POPMAKE_ATB
 * @subpackage	Functions
 * @copyright	Copyright (c) 2014, Wizard Internet Solutions
 * @license		http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since		1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load Scripts
 *
 * Loads the Popup Maker Advanced Theme Builder scripts.
 *
 * @since 1.0
 * @return void
 */
function popmake_atb_load_site_scripts() {
	$js_dir = POPMAKE_ATB_URL . '/assets/scripts/';
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';
	if ( version_compare( POPMAKE_VERSION, '1.3', '<' ) ) {
		wp_enqueue_script('popup-maker-advanced-theme-builder-site', $js_dir . 'popup-maker-advanced-theme-builder-site' . $suffix . '?defer', array('popup-maker-site'), '1.0', true);
	}
}
add_action( 'wp_enqueue_scripts', 'popmake_atb_load_site_scripts' );


/**
 * Load Admin Styles
 *
 * Enqueues the required admin styles.
 *
 * @since 1.0
 * @param string $hook Page hook
 * @return void
 */
function popmake_atb_load_admin_styles( $hook ) {
	$css_dir = POPMAKE_ATB_URL . '/assets/styles/';
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.css' : '.min.css';
	if(popmake_is_admin_page()) {
		wp_enqueue_style('thickbox');
	}
}
add_action( 'admin_enqueue_scripts', 'popmake_atb_load_admin_styles', 100 );


/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 1.0
 * @param string $hook Page hook
 * @return void
 */
function popmake_atb_load_admin_scripts( $hook ) {
	$js_dir  = POPMAKE_ATB_URL . '/assets/scripts/';
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';
	if(popmake_is_admin_page()) {
		wp_enqueue_script('popup-maker-advanced-theme-builder-admin', $js_dir . 'popup-maker-advanced-theme-builder-admin' . $suffix,  array('popup-maker-admin', 'media-upload', 'thickbox'), '1.0');
	}
}
add_action( 'admin_enqueue_scripts', 'popmake_atb_load_admin_scripts', 100 );