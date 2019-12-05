<?php


if ( ! function_exists( 'pum_image_id_from_url' ) ) {
	function pum_image_id_from_url( $image_url = '' ) {
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );

		return $attachment ? $attachment[0] : 0;
	}
}
