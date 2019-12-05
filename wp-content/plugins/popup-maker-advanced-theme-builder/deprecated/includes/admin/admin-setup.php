<?php

function popmake_atb_plugins_loaded() {
	remove_action('popmake_popup_theme_overlay_meta_box_fields', 'popmake_popup_theme_overlay_meta_box_field_atb_extension_promotion', 20);
	remove_action('popmake_popup_theme_container_meta_box_fields', 'popmake_popup_theme_container_meta_box_field_atb_extension_promotion', 30);
	remove_action('popmake_popup_theme_close_meta_box_fields', 'popmake_popup_theme_close_meta_box_field_atb_extension_promotion', 60);	
}
add_action('plugins_loaded', 'popmake_atb_plugins_loaded');


function popmake_atb_form_nonce() {
	wp_nonce_field(POPMAKE_ATB_NONCE, POPMAKE_ATB_NONCE);
}
add_action('popmake_form_nonce', 'popmake_atb_form_nonce', 5);


function popmake_atb_admin_init() {
	global $pagenow;
	if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
		// Now we'll replace the 'Insert into Post Button' inside Thickbox
		add_filter( 'gettext', 'popmake_atb_replace_thickbox_text', 20, 3 );
	}
}
add_action("admin_init", 'popmake_atb_admin_init');

function popmake_atb_replace_thickbox_text($translated_text, $text, $domain) {
    if ( 'Insert into Post' == $text && strpos($_SERVER['HTTP_REFERER'], 'popup-maker-advanced-theme-builder') !== false ) {
		return __( 'Use as background image.', 'popup-maker-advanced-theme-builder' );
	}
	return $translated_text;
}