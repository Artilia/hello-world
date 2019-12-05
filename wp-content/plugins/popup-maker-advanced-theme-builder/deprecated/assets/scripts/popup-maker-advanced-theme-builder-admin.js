(function () {
    "use strict";
    var PopMakedvancedThemeBuilderAdmin = {
        init: function () {
            if (jQuery('body.post-type-popup_theme form#post').length) {
                PopMakedvancedThemeBuilderAdmin.initialize_theme_page();
            }
        },
        initialize_theme_page: function () {
            jQuery('#popup_theme_overlay_background_image_button').on('click', function (event) {
                event.preventDefault();
                window.send_to_editor = function (html) {
                    var image_url = jQuery('img', html).attr('src');
                    jQuery('#popup_theme_overlay_background_image').val(image_url);
                    PopMakeAdmin.update_theme();
                    tb_remove();
                };
                tb_show('Select a background image for the overlay.', 'media-upload.php?referer=popup-maker-advanced-theme-builder&type=image&TB_iframe=true&post_id=0', false);
                return false;
            });
            jQuery('#popup_theme_container_background_image_button').on('click', function (event) {
                event.preventDefault();
                window.send_to_editor = function (html) {
                    var image_url = jQuery('img', html).attr('src');
                    jQuery('#popup_theme_container_background_image').val(image_url);
                    PopMakeAdmin.update_theme();
                    tb_remove();
                };
                tb_show('Select a background image for the container.', 'media-upload.php?referer=popup-maker-advanced-theme-builder&type=image&TB_iframe=true&post_id=0', false);
                return false;
            });
            jQuery('#popup_theme_close_background_image_button').on('click', function (event) {
                event.preventDefault();
                window.send_to_editor = function (html) {
                    var image_url = jQuery('img', html).attr('src');
                    jQuery('#popup_theme_close_background_image').val(image_url);
                    PopMakeAdmin.update_theme();
                    tb_remove();
                };
                tb_show('Select a background image for the close button.', 'media-upload.php?referer=popup-maker-advanced-theme-builder&type=image&TB_iframe=true&post_id=0', false);
                return false;
            });
            jQuery(document).on('popmake-admin-retheme', function (event, theme) {
                var $overlay = jQuery('.empreview .example-popup-overlay, #popmake-overlay'),
                    $container = jQuery('.empreview .example-popup, #popmake-preview'),
                    $close = jQuery('.close-popup', $container);

                $overlay.css({
                    backgroundPosition: theme.overlay_background_position,
                    backgroundImage: "url(" + theme.overlay_background_image + ")",
                    backgroundRepeat: theme.overlay_background_repeat
                });
                $container.css({
                    backgroundPosition: theme.container_background_position,
                    backgroundImage: "url(" + theme.container_background_image + ")",
                    backgroundRepeat: theme.container_background_repeat
                });
                $close.css({
                    backgroundPosition: theme.close_background_position,
                    backgroundImage: "url(" + theme.close_background_image + ")",
                    backgroundRepeat: theme.close_background_repeat
                });
            });
            PopMakeAdmin.update_theme();
        }
    };
    jQuery(document).ready(function () {
        PopMakedvancedThemeBuilderAdmin.init();
    });
}());