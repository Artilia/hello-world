<?php
function popmake_atb_popup_theme_overlay_meta_box_fields( $popup_theme_id ) {
	?><tr>
		<th scope="row">
			<label for="popup_theme_overlay_background_image">
				<?php _e( 'Image', 'popup-maker-advanced-theme-builder' );?>
			</label>
		</th>
		<td>
			<input type="text" name="popup_theme_overlay_background_image" id="popup_theme_overlay_background_image" value="<?php esc_attr_e(popmake_get_popup_theme_overlay( $popup_theme_id, 'background_image'))?>" />
			<input id="popup_theme_overlay_background_image_button" type="button" class="button" value="<?php _e( 'Select Image', 'popup-maker-advanced-theme-builder' ); ?>" />
			<span class="description"><?php _e( 'Select a background image.', 'popup-maker-advanced-theme-builder' ); ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="popup_theme_overlay_background_repeat"><?php _e( 'Repeat', 'popup-maker-advanced-theme-builder' );?></label>
		</th>
		<td>
			<select name="popup_theme_overlay_background_repeat" id="popup_theme_overlay_background_repeat">
			<?php foreach(apply_filters('popmake_atb_background_repeat_options', array()) as $option => $value) : ?>
				<option
					value="<?php echo $value;?>"
					<?php echo $value == popmake_get_popup_theme_overlay( $popup_theme_id, 'background_repeat') ? ' selected="selected"' : '';?>
				><?php echo $option;?></option>
			<?php endforeach ?>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="popup_theme_overlay_background_position"><?php _e( 'Position', 'popup-maker-advanced-theme-builder' );?></label>
		</th>
		<td>
			<select name="popup_theme_overlay_background_position" id="popup_theme_overlay_background_position">
			<?php foreach(apply_filters('popmake_atb_background_position_options', array()) as $option => $value) : ?>
				<option
					value="<?php echo $value;?>"
					<?php echo $value == popmake_get_popup_theme_overlay( $popup_theme_id, 'background_position') ? ' selected="selected"' : '';?>
				><?php echo $option;?></option>
			<?php endforeach ?>
			</select>
		</td>
	</tr><?php
}
add_action('popmake_popup_theme_overlay_meta_box_fields', 'popmake_atb_popup_theme_overlay_meta_box_fields', 21);