<?php
function popmake_atb_popup_theme_meta_field_group_background_image( $fields ) {
	return array_merge( $fields, array(
		'background_image',
		'background_repeat',
		'background_position',
	));
}
add_filter('popmake_popup_theme_meta_field_group_overlay', 'popmake_atb_popup_theme_meta_field_group_background_image');
add_filter('popmake_popup_theme_meta_field_group_container', 'popmake_atb_popup_theme_meta_field_group_background_image');
add_filter('popmake_popup_theme_meta_field_group_close', 'popmake_atb_popup_theme_meta_field_group_background_image');