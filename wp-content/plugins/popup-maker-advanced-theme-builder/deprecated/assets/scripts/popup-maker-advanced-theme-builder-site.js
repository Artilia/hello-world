(function () {
    "use strict";
    jQuery('.popmake').each(function () {
        jQuery(this)
            .on('popmakeAfterRetheme', function (event, theme) {
                var $this = jQuery(this),
                    settings = $this.data('popmake'),
                    $overlay = jQuery('#' + settings.overlay.attr.id),
                    $container = $this,
                    $close = jQuery('> .' + settings.close.attr.class, $container),
                    overlay_image,
                    container_image,
                    close_image;

                if (theme.overlay.background_image !== undefined) {
                    overlay_image = theme.overlay.background_image.replace(/.*?:\/\//g, "//");
                    $overlay.css({
                        backgroundPosition: theme.overlay.background_position,
                        backgroundImage: "url(" + overlay_image + ")",
                        backgroundRepeat: theme.overlay.background_repeat
                    });
                }
                if (theme.container.background_image !== undefined) {
                    container_image = theme.container.background_image.replace(/.*?:\/\//g, "//");
                    $container.css({
                        backgroundPosition: theme.container.background_position,
                        backgroundImage: "url(" + container_image + ")",
                        backgroundRepeat: theme.container.background_repeat
                    });
                }
                if (theme.close.background_image !== undefined) {
                    close_image = theme.close.background_image.replace(/.*?:\/\//g, "//");
                    $close.css({
                        backgroundPosition: theme.close.background_position,
                        backgroundImage: "url(" + close_image + ")",
                        backgroundRepeat: theme.close.background_repeat
                    });
                }
            });
    });
}());