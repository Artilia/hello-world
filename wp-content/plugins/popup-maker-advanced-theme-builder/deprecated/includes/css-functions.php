<?php

function popmake_atb_generate_theme_styles( $styles, $popup_theme_id, $theme ) {
	extract( $theme );

	if ( ! empty( $overlay['background_image'] ) ) {
		$styles['overlay']['background-image'] = "url('{$overlay['background_image']}')";
		$styles['overlay']['background-repeat'] = $overlay['background_repeat'];
		$styles['overlay']['background-position'] = $overlay['background_position'];
	}

	if ( ! empty( $container['background_image'] ) ) {
		$styles['container']['background-image'] = "url('{$container['background_image']}')";
		$styles['container']['background-repeat'] = $container['background_repeat'];
		$styles['container']['background-position'] = $container['background_position'];
	}

	if ( ! empty( $close['background_image'] ) ) {
		$styles['close']['background-image'] = "url('{$close['background_image']}')";
		$styles['close']['background-repeat'] = $close['background_repeat'];
		$styles['close']['background-position'] = $close['background_position'];
	}

	return $styles;
}
