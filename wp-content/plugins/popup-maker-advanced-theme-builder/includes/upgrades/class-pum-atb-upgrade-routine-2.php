<?php
/**
 * Upgrade Routine 2
 *
 * @package     PUM
 * @subpackage  Admin/Upgrades
 * @copyright   Copyright (c) 2016, WP Popup Maker
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PUM_ATB_Upgrade_Routine_2
 */
final class PUM_ATB_Upgrade_Routine_2 {

	/**
	 * @return string|void
	 */
	public static function description() {
		return __( 'Update your popup themes advanced settings.', 'popup-maker-advanced-theme-builder' );
	}

	/**
	 *
	 */
	public static function run() {
		ignore_user_abort( true );

		if ( ! pum_is_func_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}

		PUM_ATB_Upgrade_Routine_2::process_themes();
	}

	/**
	 * Map old keys to new ones.
	 */
	public static function process_themes() {

		$themes = get_posts( array(
			'post_type'      => 'popup_theme',
			'post_status'    => array( 'any', 'trash' ),
			'posts_per_page' => -1,
		) );

		foreach ( $themes as $theme ) {
			$meta = popmake_get_popup_theme_data_attr( $theme->ID );

			foreach ( array( 'overlay', 'container', 'close' ) as $el ) {

				if ( ! isset( $meta[ $el ] ) ) {
					continue;
				}

				$values = $meta[ $el ];

				$values['bg_type'] = 'color';

				$values['bg_color'] =  isset( $values['background_color'] ) ? $values['background_color'] : '#ffffff';
				$values['bg_opacity'] =  isset( $values['background_opacity'] ) ? $values['background_opacity'] : '100';

				if ( isset( $values['background_image'] ) && empty( $values['bg_image_src'] ) ) {
					$id = pum_image_id_from_url( $values['background_image'] );

					$values['bg_type']       = 'image';
					$values['bg_image_src']  = $values['background_image'];
					$values['bg_image']      = $id > 0 ? $id : 0;
					$values['bg_repeat']     = isset( $values['background_repeat'] ) ? $values['background_repeat'] : 'no-repeat';
					$values['bg_position']   = isset( $values['background_position'] ) ? $values['background_position'] : 'left top';
					$values['bg_attachment'] = 'scroll';
					$values['bg_size']       = '';
				} elseif ( empty( $values['bg_image_src'] ) && empty( $values['background_image'] ) ) {
					$values = array_merge( $values, array(
						'bg_image'      => 0,
						'bg_image_src'  => '',
						'bg_repeat'     => 'no-repeat',
						'bg_position'   => 'left top',
						'bg_attachment' => 'scroll',
						'bg_size'       => '',
					) );
				}

				unset( $values['background_color'] );
				unset( $values['background_opacity'] );
				unset( $values['background_image'] );
				unset( $values['background_repeat'] );
				unset( $values['background_position'] );

				update_post_meta( $theme->ID, "popup_theme_{$el}", $values );
			}
		}

		pum_force_theme_css_refresh();

	}

}