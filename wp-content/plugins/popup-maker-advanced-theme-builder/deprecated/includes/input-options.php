<?php
function popmake_atb_background_position_options($options) {
	return array_merge($options, array(
		__( 'Top Left', 'popup-maker-advanced-theme-builder' )		=> 'left top',
		__( 'Top Center', 'popup-maker-advanced-theme-builder' )		=> 'top',
		__( 'Top Right', 'popup-maker-advanced-theme-builder' )		=> 'right top',
		__( 'Middle Left', 'popup-maker-advanced-theme-builder' )		=> 'left',
		__( 'Middle Center', 'popup-maker-advanced-theme-builder' )	=> 'center ',
		__( 'Middle Right', 'popup-maker-advanced-theme-builder' )	=> 'right',
		__( 'Bottom Left', 'popup-maker-advanced-theme-builder' )		=> 'left bottom',
		__( 'Bottom Center', 'popup-maker-advanced-theme-builder' )	=> 'bottom',
		__( 'Bottom Right', 'popup-maker-advanced-theme-builder' )	=> 'right bottom',
	));
}
add_filter('popmake_atb_background_position_options', 'popmake_atb_background_position_options');

function popmake_atb_background_repeat_options($options) {
	return array_merge($options, array(
		__( 'None', 'popup-maker-advanced-theme-builder' )				=> 'no-repeat',
		__( 'Tile Horizontally', 'popup-maker-advanced-theme-builder' )	=> 'repeat-x',
		__( 'Tile Vertically', 'popup-maker-advanced-theme-builder' )		=> 'repeat-y',
		__( 'Tile Both', 'popup-maker-advanced-theme-builder' )			=> 'repeat',
	));
}
add_filter('popmake_atb_background_repeat_options', 'popmake_atb_background_repeat_options');