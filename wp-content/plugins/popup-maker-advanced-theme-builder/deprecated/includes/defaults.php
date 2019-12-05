<?php
function popmake_atb_popup_theme_background_image_defaults( $defaults ) {
	return array_merge( $defaults, array(
		'background_image'		=> '',
		'background_repeat'		=> 'no-repeat',
		'background_position'	=> 'top left',
		'bg_type'               => 'color',
		// BG Color
		'bg_color'              => '#ffffff',
		'bg_opacity'            => 100,
		// BG Overlay
		'bg_overlay_color'      => '#ffffff',
		'bg_overlay_opacity'    => 100,
		// BG Image
		'bg_image'              => 0,
		'bg_image_src'          => '',
		'bg_repeat'             => 'no-repeat',
		'bg_position'           => 'left top',
		'bg_attachment'         => 'scroll',
		'bg_size'              => 'cover',
		// BG Parallax
		'bg_parallax_image'     => 0,
		'bg_parallax_image_src' => '',
		'bg_parallax_speed'     => 'fast',
	));
}
add_filter('popmake_popup_theme_overlay_defaults', 'popmake_atb_popup_theme_background_image_defaults');
add_filter('popmake_popup_theme_container_defaults', 'popmake_atb_popup_theme_background_image_defaults');
add_filter('popmake_popup_theme_close_defaults', 'popmake_atb_popup_theme_background_image_defaults');