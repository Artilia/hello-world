<?php
/**
 * Plugin Name: Popup Maker - Advanced Theme Builder
 * Plugin URI: https://wppopupmaker.com/extensions/advanced-theme-builder/
 * Description:
 * Author: WP Popup Maker
 * Version: 1.1.6
 * Author URI: https://wppopupmaker.com/
 * Text Domain: popup-maker-advanced-theme-builder
 *
 * @package     PUM\Theme\Extras
 * @author      WP Popup Maker
 * @copyright   Copyright (c) 2016, WP Popup Maker
 * @since       1.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class PUM_ATB
 *
 * @since 1.1.0
 */
class PUM_ATB {

	/**
	 * @var string Plugin Version
	 */
	public static $VER = '1.1.6';

	/**
	 * @var int DB Version
	 */
	public static $DB_VER = 2;

	/**
	 * @var string Text Domain
	 */
	public static $DOMAIN = 'popup-maker-advanced-theme-builder';

	/**
	 * @var string Plugin Directory
	 */
	public static $DIR;

	/**
	 * @var string Plugin URL
	 */
	public static $URL;

	/**
	 * @var string Plugin FILE
	 */
	public static $FILE;

	/**
	 * @var string Global PUM_ATB styles added to the pum_styles hook.
	 */
	private static $extra_styles = '';

	/**
	 * Set up plugin variables.
	 */
	public static function setup_vars() {
		PUM_ATB::$FILE = __FILE__;
		PUM_ATB::$DIR  = plugin_dir_path( __FILE__ );
		PUM_ATB::$URL  = plugin_dir_url( __FILE__ );
	}

	/**
	 * Initialize the plugin.
	 */
	public static function init() {

		require_once PUM_ATB::$DIR . 'includes/pum-atb-functions.php';

		add_action( 'init', array( __CLASS__, 'textdomain' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts' ) );

		add_filter( 'popmake_generate_theme_styles', array( __CLASS__, 'theme_css' ), 10, 3 );

		remove_action( 'popmake_popup_theme_overlay_meta_box_fields', 'popmake_popup_theme_overlay_meta_box_field_atb_extension_promotion', 20 );
		remove_action( 'popmake_popup_theme_container_meta_box_fields', 'popmake_popup_theme_container_meta_box_field_atb_extension_promotion', 30 );
		remove_action( 'popmake_popup_theme_close_meta_box_fields', 'popmake_popup_theme_close_meta_box_field_atb_extension_promotion', 70 );

		remove_action( 'popmake_popup_theme_overlay_meta_box_fields', 'popmake_popup_theme_overlay_meta_box_field_background', 10 );
		remove_action( 'popmake_popup_theme_container_meta_box_fields', 'popmake_popup_theme_container_meta_box_field_background', 20 );
		remove_action( 'popmake_popup_theme_close_meta_box_fields', 'popmake_popup_theme_close_meta_box_field_background', 60 );

		add_action( 'popmake_popup_theme_overlay_meta_box_fields', array( __CLASS__, 'background_fields' ), 10 );
		add_action( 'popmake_popup_theme_container_meta_box_fields', array( __CLASS__, 'background_fields' ), 20 );
		add_action( 'popmake_popup_theme_close_meta_box_fields', array( __CLASS__, 'background_fields' ), 60 );

		add_filter( 'popmake_popup_theme_fields', array( __CLASS__, 'theme_meta_fields' ) );

		add_filter( 'popmake_popup_theme_overlay_defaults', array( __CLASS__, 'theme_meta_defaults' ) );
		add_filter( 'popmake_popup_theme_container_defaults', array( __CLASS__, 'theme_meta_defaults' ) );
		add_filter( 'popmake_popup_theme_close_defaults', array( __CLASS__, 'theme_meta_defaults' ) );

		add_action( 'pum_styles', array( __CLASS__, 'extra_styles' ) );

		add_filter( 'popmake_settings_extensions', array( __CLASS__, 'register_settings' ) );

		PUM_ATB::maybe_update();

		// Handle licensing
		if ( class_exists( 'PopMake_License' ) ) {
			new PopMake_License( __FILE__, 'Advanced Theme Builder', PUM_ATB::$VER, 'WP Popup Maker' );
		}
	}

	/**
	 *
	 */
	public static function maybe_update() {

		$current_ver = get_option( 'pum_atb_ver', false );

		if ( ! $current_ver ) {
			$deprecated_ver = get_site_option( 'popmake_atb_version', false );
			$current_ver    = $deprecated_ver ? $deprecated_ver : PUM_ATB::$VER;
			add_option( 'pum_atb_ver', PUM_ATB::$VER );
		}

		if ( version_compare( $current_ver, PUM_ATB::$VER, '<' ) ) {
			// Save Upgraded From option
			update_option( 'pum_atb_ver_upgraded_from', $current_ver );
			update_option( 'pum_atb_ver', PUM_ATB::$VER );
		}

		$current_db_version = get_option( 'pum_atb_db_ver', false );

		if ( ! $current_db_version ) {
			$updated_from = get_option( 'pum_atb_ver_upgraded_from', false );

			// Since no versions prior to 1.1.0 had a ver stored we default to 1.
			if ( ! $updated_from ) {
				$current_db_version = 1;
			} else {
				if ( version_compare( '1.1.0', $updated_from, '>=' ) ) {
					$current_db_version = 2;
				} else {
					$current_db_version = 1;
				}
			}

			update_option( 'pum_atb_db_ver', $current_db_version );

		}

		if ( $current_db_version < PUM_ATB::$DB_VER ) {
			if ( $current_db_version < 2 ) {
				include_once PUM_ATB::$DIR . 'includes/upgrades/class-pum-atb-upgrade-routine-2.php';
				PUM_ATB_Upgrade_Routine_2::run();
				$current_db_version = 2;
			}

			update_option( 'pum_atb_db_ver', $current_db_version );
		}

	}

	/**
	 * Register global settings.
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	public static function register_settings( $settings ) {
		return array_merge( $settings, array(
			'pum_atb_heading'      => array(
				'id'   => 'pum_atb_heading',
				'name' => __( 'Advanced Theme Builder', 'popup-maker-advanced-theme-builder' ),
				'type' => 'header',
			),
			'pum_atb_user_presets' => array(
				'id'   => 'pum_atb_user_presets',
				'name' => __( 'Enable user preset colors?', 'popup-maker-advanced-theme-builder' ),
				'type' => 'checkbox',
			),
			'pum_atb_preset1'      => array(
				'id'   => 'pum_atb_preset1',
				'name' => __( 'Color Preset #1', 'popup-maker-advanced-theme-builder' ),
				'type' => 'color',
			),
			'pum_atb_preset2'      => array(
				'id'   => 'pum_atb_preset2',
				'name' => __( 'Color Preset #2', 'popup-maker-advanced-theme-builder' ),
				'type' => 'color',
			),
			'pum_atb_preset3'      => array(
				'id'   => 'pum_atb_preset3',
				'name' => __( 'Color Preset #3', 'popup-maker-advanced-theme-builder' ),
				'type' => 'color',
			),
			'pum_atb_preset4'      => array(
				'id'   => 'pum_atb_preset4',
				'name' => __( 'Color Preset #4', 'popup-maker-advanced-theme-builder' ),
				'type' => 'color',
			),
			'pum_atb_preset5'      => array(
				'id'   => 'pum_atb_preset5',
				'name' => __( 'Color Preset #5', 'popup-maker-advanced-theme-builder' ),
				'type' => 'color',
			),
			'pum_atb_preset6'      => array(
				'id'   => 'pum_atb_preset6',
				'name' => __( 'Color Preset #6', 'popup-maker-advanced-theme-builder' ),
				'type' => 'color',
			),
		) );
	}

	/**
	 * Register theme meta defaults
	 *
	 * @param $defaults
	 *
	 * @return array
	 */
	public static function theme_meta_defaults( $defaults = array() ) {

		$_defaults = array(
			'bg_type'       => 'color',
			// BG Color
			'bg_color'      => '#ffffff',
			'bg_opacity'    => 100,
			// BG Image
			'bg_image'      => 0,
			'bg_image_src'  => '',
			'bg_repeat'     => 'no-repeat',
			'bg_position'   => 'left top',
			'bg_attachment' => 'scroll',
			'bg_size'       => 'cover',
			// BG Parallax
			// 'bg_parallax_image'     => 0,
			// 'bg_parallax_image_src' => '',
			// 'bg_parallax_speed'     => 'fast',
		);

		if ( current_filter() != 'popmake_popup_theme_close_defaults' ) {
			$_defaults = array_merge( $_defaults, array(
				// BG Overlay
				'bg_overlay_color'   => '',
				'bg_overlay_opacity' => 25,
			) );
		}

		return array_merge( $defaults, $_defaults );
	}

	/**
	 * Theme meta fields.
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public static function theme_meta_fields( $fields ) {

		foreach ( array( 'overlay', 'container', 'close' ) as $element ) {
			$_fields = array(
				'bg_type'       => array(
					'label'       => '',
					'description' => '',
					'type'        => 'select',
					'std'         => 'color',
					'options'     => array(
						__( 'None', 'popup-maker-advanced-theme-builder' )  => 'none',
						__( 'Color', 'popup-maker-advanced-theme-builder' ) => 'color',
						__( 'Image', 'popup-maker-advanced-theme-builder' ) => 'image',
					),
				),
				// BG Color
				'bg_color'      => array(
					'label' => __( 'Color', 'popup-maker-advanced-theme-builder' ),
					'type'  => 'color',
					'std'   => '#ffffff',
				),
				'bg_opacity'    => array(
					'label' => __( 'Opacity', 'popup-maker-advanced-theme-builder' ),
					'type'  => 'rangeslider',
					'std'   => 100,
					'step'  => 1,
					'min'   => 0,
					'max'   => 100,
					'unit'  => __( '%', 'popup-maker-advanced-theme-builder' ),
				),
				// BG Image
				'bg_image'      => array(
					'type' => 'image',
					'std'  => 0,
				),
				'bg_image_src'  => array(
					'type' => 'imagesrc',
					'std'  => '',
				),
				'bg_repeat'     => array(
					'type'    => 'select',
					'std'     => '',
					'options' => array(),
				),
				'bg_position'   => array(
					'type'    => 'select',
					'std'     => '',
					'options' => array(),
				),
				'bg_attachment' => array(
					'type'    => 'select',
					'std'     => '',
					'options' => array(),
				),
				'bg_size'       => array(
					'type'    => 'select',
					'std'     => '',
					'options' => array(),
				),
				// BG Parallax
				// 'bg_parallax_image',
				// 'bg_parallax_image_src',
				// 'bg_parallax_speed',
			);

			if ( $element != 'close' ) {
				$_fields = array_merge( $_fields, array(
					// BG Overlay
					'bg_overlay_color'   => array(
						'label' => __( 'Color', 'popup-maker-advanced-theme-builder' ),
						'type'  => 'color',
						'std'   => '#ffffff',
					),
					'bg_overlay_opacity' => array(
						'label' => __( 'Opacity', 'popup-maker-advanced-theme-builder' ),
						'type'  => 'rangeslider',
						'std'   => 25,
						'step'  => 1,
						'min'   => 0,
						'max'   => 100,
						'unit'  => __( '%', 'popup-maker-advanced-theme-builder' ),
					),
				) );
			}

			$fields[ $element ] = array_merge( $fields[ $element ], $_fields );
		}

		return $fields;
	}

	/**
	 * Remaps old values to new and removes the old.
	 *
	 * This should migrate settings over time as themes are updated.
	 *
	 * @param array $values
	 *
	 * @return array
	 */
	public static function remap_values( $values = array() ) {
		
		if ( ! isset( $values['bg_color'] ) && ! empty( $values['background_color'] ) ) {
			$values['bg_color'] = $values['background_color'];
			unset( $values['background_color'] );
		}

		if ( ! isset( $values['bg_opacity'] ) && ! empty( $values['background_opacity'] ) ) {
			$values['bg_opacity'] = $values['background_opacity'];
			unset( $values['background_opacity'] );
		}

		if ( ! isset( $values['bg_image_src'] ) && ! empty( $values['background_image'] ) ) {
			$values['bg_image_src'] = $values['background_image'];
			unset( $values['background_image'] );
		}

		if ( ! isset( $values['bg_image'] ) && ! empty( $values['background_image'] ) ) {
			$values['bg_image'] = pum_image_id_from_url( $values['background_image'] );
			unset( $values['background_image'] );
		}

		if ( ! isset( $values['bg_repeat'] ) && ! empty( $values['background_repeat'] ) ) {
			$values['bg_repeat'] = $values['background_repeat'];
			unset( $values['background_repeat'] );
		}

		if ( ! isset( $values['bg_position'] ) && ! empty( $values['background_position'] ) ) {
			$values['bg_position'] = $values['background_position'];
			unset( $values['background_position'] );
		}

		return $values;
	}

	/**
	 * Render additional theme css.
	 *
	 * @param $styles
	 * @param $popup_theme_id
	 * @param $theme
	 *
	 * @return mixed
	 */
	public static function theme_css( $styles, $popup_theme_id, $theme ) {
		//extract( $theme );

		$extra_styles = '';

		foreach ( array( 'overlay', 'container', 'close' ) as $el ) {
			$el_vars = isset( $theme[ $el ] ) ? $theme[ $el ] : null;

			if ( ! $el_vars ) {
				continue;
			}

			$el_vars = PUM_ATB::remap_values( $el_vars );

			switch ( $el ) {
				case 'overlay':
					$after_el = ".pum-theme-{$popup_theme_id}::after";
					break;
				case 'container':
					$after_el = ".pum-theme-{$popup_theme_id} .pum-container::after";
					break;
			}

			if ( empty ( $el_vars['bg_type'] ) ) {
				$el_vars['bg_type'] = 'color';
			}

			switch ( $el_vars['bg_type'] ) {
				case 'color':
					$styles[ $el ]['background-color'] = popmake_get_rgba_value( $el_vars['bg_color'], $el_vars['bg_opacity'] );
					break;
				case 'image':
					$styles[ $el ]['background-color']      = popmake_get_rgba_value( $el_vars['bg_color'], $el_vars['bg_opacity'] );
					$styles[ $el ]['background-image']      = "url('{$el_vars['bg_image_src']}')";
					$styles[ $el ]['background-repeat']     = $el_vars['bg_repeat'];
					$styles[ $el ]['background-position']   = $el_vars['bg_position'];
					$styles[ $el ]['background-attachment'] = $el_vars['bg_attachment'];
					$styles[ $el ]['background-size']       = $el_vars['bg_size'];
					if ( $el != 'close' && ! empty( $after_el ) ) {
						$extra_styles .= "$after_el { background-color: " . popmake_get_rgba_value( $el_vars['bg_overlay_color'], $el_vars['bg_overlay_opacity'] ) . " }\r\n";
					}
					break;
				case 'none':
					$styles[ $el ]['background'] = "none";
					break;
			}
		}

		if ( ! empty( $extra_styles ) ) {
			PUM_ATB::$extra_styles .= "/* Popup Theme " . $popup_theme_id . " Additional Styles */\r\n";
			PUM_ATB::$extra_styles .= $extra_styles;
		}

		return $styles;
	}

	/**
	 * Render extra styles.
	 */
	public static function extra_styles() {
		if ( ! empty( PUM_ATB::$extra_styles ) ) {
			echo "
/* Advanced Theme Builder Additional Styles */
.pum-overlay::after,
.pum-overlay .pum-container::after { content: ''; display: block; position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 0; }
.pum-overlay::after { position: fixed; margin-right: 1.075em; }
html.pum-open-fixed .pum-overlay::after { margin-right: 0; }";
			echo PUM_ATB::$extra_styles;
		}
	}

	/**
	 * Load the textdomain for gettext translation.
	 */
	public static function textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), PUM_ATB::$DOMAIN );
		// wp-content/languages/plugin-name/plugin-name-de_DE.mo
		load_textdomain( PUM_ATB::$DOMAIN, trailingslashit( WP_LANG_DIR ) . PUM_ATB::$DOMAIN . '/' . PUM_ATB::$DOMAIN . '-' . $locale . '.mo' );
		// wp-content/plugins/plugin-name/languages/plugin-name-de_DE.mo
		load_plugin_textdomain( PUM_ATB::$DOMAIN, false, PUM_ATB::$DIR . 'languages/' );
	}

	/**
	 * Enqueue the needed scripts.
	 */
	public static function scripts() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		if ( is_admin() && popmake_is_admin_popup_theme_page() ) {
			wp_enqueue_media();
			wp_enqueue_script( 'pum-atb-admin', PUM_ATB::$URL . 'assets/js/admin' . $suffix . '.js', array( 'popup-maker-admin' ), PUM_ATB::$VER, true );

			$localize = array(
				'I10n' => array(
					'selectImage' => __( 'Select Image', 'popup-maker-advanced-theme-builder' ),
					'fullSize'    => __( 'Full', 'popup-maker-advanced-theme-builder' ),
					'large'       => __( 'Large', 'popup-maker-advanced-theme-builder' ),
					'medium'      => __( 'Medium', 'popup-maker-advanced-theme-builder' ),
					'thumbnail'   => __( 'Thumbnail', 'popup-maker-advanced-theme-builder' ),
				),
			);

			if ( popmake_get_option( 'pum_atb_user_presets' ) ) {
				$localize['user_palette'] = array(
					popmake_get_option( 'pum_atb_preset1', '#000000' ),
					popmake_get_option( 'pum_atb_preset2', '#000000' ),
					popmake_get_option( 'pum_atb_preset3', '#000000' ),
					popmake_get_option( 'pum_atb_preset4', '#000000' ),
					popmake_get_option( 'pum_atb_preset5', '#000000' ),
					popmake_get_option( 'pum_atb_preset6', '#000000' ),
				);
			}

			wp_localize_script( 'pum-atb-admin', 'pum_atb', $localize );
		}
	}

	/**
	 * Render the background fields.
	 *
	 * @param $post_id
	 */
	public static function background_fields( $post_id ) {

		switch ( current_action() ) {
			case 'popmake_popup_theme_overlay_meta_box_fields':
				$which  = 'overlay';
				$values = popmake_get_popup_theme_overlay( $post_id );
				break;
			case 'popmake_popup_theme_container_meta_box_fields':
				$which  = 'container';
				$values = popmake_get_popup_theme_container( $post_id );
				break;
			case 'popmake_popup_theme_close_meta_box_fields':
				$which  = 'close';
				$values = popmake_get_popup_theme_close( $post_id );
				break;
			default:
				return;
				break;
		}

		$field_name = "popup_theme_$which";

		// Map Old Fields
		$values = PUM_ATB::remap_values( $values );

		$values = wp_parse_args( $values, array(
			'bg_type'               => 'color',
			// BG Color
			'bg_color'              => '#ffffff',
			'bg_opacity'            => 100,
			// BG Overlay
			'bg_overlay_color'      => '',
			'bg_overlay_opacity'    => 25,
			// BG Image
			'bg_image'              => 0,
			'bg_image_src'          => '',
			'bg_repeat'             => 'no-repeat',
			'bg_position'           => 'left top',
			'bg_attachment'         => 'scroll',
			'bg_size'               => 'cover',
			// BG Parallax
			'bg_parallax_image'     => 0,
			'bg_parallax_image_src' => '',
			'bg_parallax_speed'     => 'fast',
		) ); ?>


		<?php #region bg_type ?>
		<tr class="title-divider">
			<th colspan="2">
				<h3 class="title"><?php _e( 'Background', 'popup-maker-advanced-theme-builder' ); ?></h3>
			</th>
		</tr>
		<tr>
			<th scope="row">
				<label for="<?php echo esc_attr( $field_name ); ?>_bg_type">
					<?php _e( 'Type', 'popup-maker-advanced-theme-builder' ); ?>
				</label>
			</th>
			<td><?php
				$select_option_sections = array(
					'color'     => array(
						'sections' => array(
							$which . '-bg_color',
						),
					),
					'image'     => array(
						'sections' => array(
							$which . '-bg_color',
							$which . '-bg_image',
						),
					),
					'video'     => array(
						'sections' => array(
							$which . '-bg_color',
							$which . '-bg_video',
							$which . '-bg_overlay',
						),
					),
					'slideshow' => array(
						'sections' => array(
							$which . '-bg_color',
							$which . '-bg_slideshow',
							$which . '-bg_overlay',
						),
					),
					'parallax'  => array(
						'sections' => array(
							$which . '-bg_color',
							$which . '-bg_parallax',
							$which . '-bg_overlay',
						),
					),
				);

				if ( $which != 'close' ) {
					$select_option_sections['image']['sections'][] = $which . '-bg_overlay';
				}


				$bg_type_options = apply_filters( 'pum_atb_bg_type_options', array(
					'none'  => __( 'None', 'popup-maker-advanced-theme-builder' ),
					'color' => __( 'Color', 'popup-maker-advanced-theme-builder' ),
					'image' => __( 'Image', 'popup-maker-advanced-theme-builder' ),
					//'video'     => __( 'Video', 'popup-maker-advanced-theme-builder' ),
					//'slideshow' => __( 'Slideshow', 'popup-maker-advanced-theme-builder' ),
					//'parallax'  => __( 'Parallax', 'popup-maker-advanced-theme-builder' ),
				), $which );

				?>
				<select id="<?php echo esc_attr( $field_name ); ?>_bg_type" name="<?php echo esc_attr( $field_name ); ?>_bg_type" data-toggle='<?php echo wp_json_encode( $select_option_sections ); ?>'>
					<?php foreach ( $bg_type_options as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $values['bg_type'] ); ?>><?php esc_html_e( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		</tbody></table>
		<?php #endregion BgType ?>

		<?php #region bg_image ?>
		<div class="<?php echo $which; ?>-bg_image">
			<table class="form-table">
				<tbody>
				<tr class="title-divider">
					<th colspan="2">
						<h3 class="title"><?php _e( 'Background Image', 'popup-maker-advanced-theme-builder' ); ?></h3>
					</th>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_image">
							<?php _e( 'Image', 'popup-maker-advanced-theme-builder' ); ?>
						</label>
					</th>
					<td>
						<div class="pum-image-field <?php echo $values['bg_image'] > 0 ? '' : 'pum-image-empty'; ?>">
							<a class="pum-image-select" href="javascript:void(0);" onclick="return false;"><?php _e( 'Select Image', 'popup-maker-advanced-theme-builder' ); ?></a>
							<div class="pum-image-preview">
								<div class="pum-image-preview-img"><?php
									$img_src = PUM_ATB::$URL . 'assets/img/spacer.png';
									if ( ! empty( $values['bg_image'] ) ) {
										$img_src = wp_get_attachment_thumb_url( $values['bg_image'] );
									} ?>
									<img src="<?php echo esc_attr( $img_src ); ?>" height="100" />
								</div>
								<select name="<?php echo esc_attr( $field_name ); ?>_bg_image_src">
									<?php foreach ( PUM_ATB::get_image_sizes() as $size => $atts ) :
										$url = wp_get_attachment_image_src( $values['bg_image'], $size );
										$label = $size == 'full' ? __( 'Full Size', 'popup-maker-advanced-theme-builder' ) : ucwords( str_replace( array(
												'_',
												'-',
											), ' ', $size ) ) . ' (' . implode( 'x', $atts ) . ')'; ?>
										<option value="<?php echo esc_attr( $url[0] ); ?>" <?php selected( $values['bg_image_src'], $url[0] ); ?>><?php esc_html_e( $label ); ?></option>
									<?php endforeach; ?>
								</select>
								<br />
								<a class="pum-image-edit" href="javascript:void(0);" onclick="return false;"><?php _e( 'Edit', 'popup-maker-advanced-theme-builder' ); ?></a>
								<a class="pum-image-replace" href="javascript:void(0);" onclick="return false;"><?php _e( 'Replace', 'popup-maker-advanced-theme-builder' ); ?></a>
								<div class="pum-clear"></div>
							</div>
							<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>_bg_image" id="<?php echo esc_attr( $field_name ); ?>_bg_image" value="<?php echo esc_attr( $values['bg_image'] ) ?>" />
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_repeat"><?php _e( 'Repeat', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td><?php
						$bg_repeat_options = apply_filters( 'pum_atb_bg_repeat_options', array(
							'no-repeat' => __( 'None', 'popup-maker-advanced-theme-builder' ),
							'repeat'    => __( 'Tile', 'popup-maker-advanced-theme-builder' ),
							'repeat-x'  => __( 'Horizontally', 'popup-maker-advanced-theme-builder' ),
							'repeat-y'  => __( 'Vertically', 'popup-maker-advanced-theme-builder' ),
						), $which ); ?>
						<select name="<?php echo esc_attr( $field_name ); ?>_bg_repeat" id="<?php echo esc_attr( $field_name ); ?>_bg_repeat">
							<?php foreach ( $bg_repeat_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $values['bg_repeat'] ); ?>><?php esc_html_e( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_position"><?php _e( 'Position', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td><?php
						$bg_position_options = apply_filters( 'pum_atb_bg_position_options', array(
							'left top'      => __( 'Left Top', 'popup-maker-advanced-theme-builder' ),
							'left center'   => __( 'Left Center', 'popup-maker-advanced-theme-builder' ),
							'left bottom'   => __( 'Left Bottom', 'popup-maker-advanced-theme-builder' ),
							'center top'    => __( 'Center Top', 'popup-maker-advanced-theme-builder' ),
							'center center' => __( 'Center', 'popup-maker-advanced-theme-builder' ),
							'center bottom' => __( 'Center Bottom', 'popup-maker-advanced-theme-builder' ),
							'right top'     => __( 'Right Top', 'popup-maker-advanced-theme-builder' ),
							'right center'  => __( 'Right Center', 'popup-maker-advanced-theme-builder' ),
							'right bottom'  => __( 'Right Bottom', 'popup-maker-advanced-theme-builder' ),
						), $which ); ?>
						<select name="<?php echo esc_attr( $field_name ); ?>_bg_position" id="<?php echo esc_attr( $field_name ); ?>_bg_position">
							<?php foreach ( $bg_position_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $values['bg_position'] ); ?>><?php esc_html_e( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_attachment"><?php _e( 'Attachment', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td><?php
						$bg_attachment_options = apply_filters( 'pum_atb_bg_attachment_options', array(
							'scroll' => __( 'Scroll', 'popup-maker-advanced-theme-builder' ),
							'fixed'  => __( 'Fixed', 'popup-maker-advanced-theme-builder' ),
						), $which ); ?>
						<select name="<?php echo esc_attr( $field_name ); ?>_bg_attachment" id="<?php echo esc_attr( $field_name ); ?>_bg_attachment">
							<?php foreach ( $bg_attachment_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $values['bg_attachment'] ); ?>><?php esc_html_e( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_size"><?php _e( 'Size', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td><?php
						$bg_size_options = apply_filters( 'pum_atb_bg_size_options', array(
							''        => __( 'None', 'popup-maker-advanced-theme-builder' ),
							'contain' => __( 'Fit', 'popup-maker-advanced-theme-builder' ),
							'cover'   => __( 'Fill', 'popup-maker-advanced-theme-builder' ),
						), $which ); ?>
						<select name="<?php echo esc_attr( $field_name ); ?>_bg_size" id="<?php echo esc_attr( $field_name ); ?>_bg_size">
							<?php foreach ( $bg_size_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $values['bg_size'] ); ?>><?php esc_html_e( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<?php #endregion ?>

		<?php #region bg_color ?>
		<div class="<?php echo $which; ?>-bg_color">
			<table class="form-table">
				<tbody>
				<tr class="title-divider">
					<th colspan="2">
						<h3 class="title"><?php _e( 'Background Color', 'popup-maker-advanced-theme-builder' ); ?></h3>
					</th>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_color"><?php _e( 'Color', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $field_name ); ?>_bg_color" id="<?php echo esc_attr( $field_name ); ?>_bg_color" value="<?php echo esc_attr( $values['bg_color'] ) ?>" class="pum-color-picker color-picker background-color bg-color" />
					</td>
				</tr>
				<tr class="bg_opacity">
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_opacity"><?php _e( 'Opacity', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td>
						<input type="text" readonly value="<?php echo esc_attr( $values['bg_opacity'] ) ?>" name="<?php echo esc_attr( $field_name ); ?>_bg_opacity" id="<?php echo esc_attr( $field_name ); ?>_bg_opacity" class="pum-range-manual popmake-range-manual" step="1" min="0" max="100" data-force-minmax=true />
						<span class="range-value-unit regular-text"><?php _e( '%' ); ?></span>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<?php #endregion ?>

		<?php #region bg_overlay ?>
		<div class="<?php echo $which; ?>-bg_overlay">
			<table class="form-table">
				<tbody>
				<tr class="title-divider">
					<th colspan="2">
						<h3 class="title"><?php _e( 'Background Overlay', 'popup-maker-advanced-theme-builder' ); ?></h3>
					</th>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_overlay_color"><?php _e( 'Color', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $field_name ); ?>_bg_overlay_color" id="<?php echo esc_attr( $field_name ); ?>_bg_overlay_color" value="<?php echo esc_attr( $values['bg_overlay_color'] ) ?>" class="pum-color-picker color-picker background-color bg-color" />
					</td>
				</tr>
				<tr class="bg_overlay_opacity">
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_overlay_opacity"><?php _e( 'Opacity', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td>
						<input type="text" readonly value="<?php echo esc_attr( $values['bg_overlay_opacity'] ) ?>" name="<?php echo esc_attr( $field_name ); ?>_bg_overlay_opacity" id="<?php echo esc_attr( $field_name ); ?>_bg_overlay_opacity" class="pum-range-manual popmake-range-manual popmake-range-manual" step="1" min="0" max="100" data-force-minmax=true />
						<span class="range-value-unit regular-text"><?php _e( '%' ); ?></span>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<?php #endregion ?>

		<?php #region bg_video ?><?php /*
		<div class="<?php echo $which; ?>-bg_video">
			<table class="form-table">
				<tbody>
				<tr class="title-divider">
					<th colspan="2">
						<h3 class="title"><?php _e( 'Background Video', 'popup-maker-advanced-theme-builder' ); ?></h3>
					</th>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_video"><?php _e( 'Video (MP4)', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td>
						<div class="pum-video-field pum-video-empty">
							<a class="pum-video-select" href="javascript:void(0);" onclick="return false;"><?php _e( 'Select Video', 'popup-maker-advanced-theme-builder' ); ?></a>
							<div class="pum-video-preview">
								<div class="pum-video-preview-img">
									<img src="<?php echo includes_url( 'images/media/video.png' ); ?>" />
								</div>
								<span class="pum-video-preview-filename"></span>
								<br>
								<a class="pum-video-replace" href="javascript:void(0);" onclick="return false;"><?php _e( 'Replace Video', 'popup-maker-advanced-theme-builder' ); ?></a>
								<div class="pum-clear"></div>
							</div>
							<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>_bg_video" id="<?php echo esc_attr( $field_name ); ?>_bg_video" value="<?php echo esc_attr( $values['bg_video'] ) ?>" />
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_video_webm"><?php _e( 'Video (WebM)', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td>
						<div class="pum-video-field pum-video-empty">
							<a class="pum-video-select" href="javascript:void(0);" onclick="return false;"><?php _e( 'Select Video', 'popup-maker-advanced-theme-builder' ); ?></a>
							<div class="pum-video-preview">
								<div class="pum-video-preview-img">
									<img src="<?php echo PUM_ATB::$URL; ?>assets/img/spacer.png">
								</div>
								<span class="pum-video-preview-filename"></span>
								<br>
								<a class="pum-video-replace" href="javascript:void(0);" onclick="return false;"><?php _e( 'Replace Video', 'popup-maker-advanced-theme-builder' ); ?></a>
								<div class="pum-clear"></div>
							</div>
							<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>_bg_video_webm" id="<?php echo esc_attr( $field_name ); ?>_bg_video_webm" value="<?php echo esc_attr( $values['bg_video_webm'] ) ?>" />
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_video_fallback">
							<?php _e( 'Image', 'popup-maker-advanced-theme-builder' ); ?>
						</label>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $field_name ); ?>_bg_video_fallback" id="<?php echo esc_attr( $field_name ); ?>_bg_video_fallback" value="<?php echo esc_attr( $values['bg_video_fallback'] ) ?>" />
						<input id="<?php echo esc_attr( $field_name ); ?>_bg_video_fallback_button" type="button" class="button" value="<?php _e( 'Select Image', 'popup-maker-advanced-theme-builder' ); ?>" />
						<span class="description"><?php _e( 'Select a background image.', 'popup-maker-advanced-theme-builder' ); ?></span>
					</td>
				</tr>

				</tbody>
			</table>
		</div>
        */ ?><?php #endregion ?>

		<?php #region bg_parallax ?><?php /*
		<div class="<?php echo $which; ?>-bg_parallax">
			<table class="form-table">
				<tbody>
				<tr class="title-divider">
					<th colspan="2">
						<h3 class="title"><?php _e( 'Background Parallax', 'popup-maker-advanced-theme-builder' ); ?></h3>
					</th>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_parallax_image">
							<?php _e( 'Image', 'popup-maker-advanced-theme-builder' ); ?>
						</label>
					</th>
					<td>
						<div class="pum-image-field pum-image-empty">
							<a class="pum-image-select" href="javascript:void(0);" onclick="return false;"><?php _e( 'Select Image', 'popup-maker-advanced-theme-builder' ); ?></a>
							<div class="pum-image-preview">
								<div class="pum-image-preview-img"><?php
									$img_src = PUM_ATB::$URL . 'assets/img/spacer.png';
									if ( ! empty( $values['bg_parallax_image'] ) ) {
										$img_src = wp_get_attachment_thumb_url( $values['bg_parallax_image'] );
									} ?>
									<img src="<?php echo esc_attr( $img_src ); ?>" height="100" />
								</div>
								<select name="<?php echo esc_attr( $field_name ); ?>_bg_parallax_image_src">
									<?php foreach( PUM_ATB::get_image_sizes() as $size => $atts ) :
										$label = ucwords( str_replace( array( '_', '-' ), ' ', $size ) ) . ' (' . implode( 'x', $atts ) . ')'; ?>
										<option value="<?php echo esc_attr( $size ); ?>" <?php selected( $values['bg_parallax_image_src'], $size ); ?>><?php esc_html_e( $label ); ?></option>
									<?php endforeach; ?>
								</select>
								<br />
								<a class="pum-image-edit" href="javascript:void(0);" onclick="return false;"><?php _e( 'Edit', 'popup-maker-advanced-theme-builder' ); ?></a>
								<a class="pum-image-replace" href="javascript:void(0);" onclick="return false;"><?php _e( 'Replace', 'popup-maker-advanced-theme-builder' ); ?></a>
								<div class="pum-clear"></div>
							</div>
							<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>_bg_parallax_image" id="<?php echo esc_attr( $field_name ); ?>_bg_parallax_image" value="<?php echo esc_attr( $values['bg_parallax_image'] ) ?>" />
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $field_name ); ?>_bg_parallax_speed"><?php _e( 'Speed', 'popup-maker-advanced-theme-builder' ); ?></label>
					</th>
					<td><?php
						$bg_parallax_speed_options = apply_filters( 'pum_atb_bg_parallax_speed_options', array(
							2 => __( 'Fast', 'popup-maker-advanced-theme-builder' ),
							5 => __( 'Medium', 'popup-maker-advanced-theme-builder' ),
							8 => __( 'Slow', 'popup-maker-advanced-theme-builder' ),
						), $which ); ?>
						<select name="<?php echo esc_attr( $field_name ); ?>_bg_parallax_speed" id="<?php echo esc_attr( $field_name ); ?>_bg_parallax_speed">
							<?php foreach ( $bg_parallax_speed_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $values['bg_parallax_speed'] ); ?>><?php esc_html_e( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
        */ ?><?php #endregion ?>

		<table class="form-table">
			<tbody><?php
	}

	/**
	 * Get a list of image sizes available.
	 *
	 * @return array
	 */
	public static function get_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = array();
		$fullsize = false;

		foreach ( get_intermediate_image_sizes() as $size ) {

			// Hidden size added in 4.4 for responsive images. We don't need it.
			if ( 'medium_large' == $size ) {
				continue;
			}

			if ( 'full' == $size ) {
				$fullsize = true;
			}

			$sizes[ $size ] = array( 0, 0 );

			if ( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $size ][0] = get_option( $size . '_size_w' );
				$sizes[ $size ][1] = get_option( $size . '_size_h' );
			} else if ( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $size ] ) ) {
				$sizes[ $size ] = array(
					$_wp_additional_image_sizes[ $size ]['width'],
					$_wp_additional_image_sizes[ $size ]['height'],
				);
			}
		}

		if ( ! $fullsize ) {
			$sizes['full'] = array(null, null);
		}

		return $sizes;
	}

}


/**
 * Get the ball rolling. Fire up the correct version.
 *
 * @since       1.1.0
 */
function pum_atb_init() {
	if ( ! class_exists( 'Popup_Maker' ) && ! class_exists( 'PUM' ) ) {
		if ( ! class_exists( 'PUM_Extension_Activation' ) ) {
			require_once 'includes/pum-sdk/class-pum-extension-activation.php';
		}

		$activation = new PUM_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation->run();
	} else {

		// Set up variables for use in all versions.
		PUM_ATB::setup_vars();

		if ( function_exists( 'pum_is_v1_4_compatible' ) && pum_is_v1_4_compatible() ) {
			PUM_ATB::init();
		} else {
			// Here for backward compatibility with older versions of Popup Maker.
			require_once 'deprecated/class-popmake-advanced-theme-builder.php';
			Popup_Maker_Advanced_Theme_Builder::instance();
		}

	}
}

add_action( 'plugins_loaded', 'pum_atb_init' );
