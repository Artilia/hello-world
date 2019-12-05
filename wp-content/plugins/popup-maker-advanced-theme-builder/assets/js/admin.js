var PUM_ATB;
(function ($, document, pum_atb) {
    "use strict";
    var I10n = pum_atb.I10n;

    PUM_ATB = {
        _singleImageSelector: null,
        _init: function () {
            PUM_ATB._event_listeners();
            $(document)
                .ready(function () {
                    var $color_pickers = $('.color-picker.initialized');

                    $('select[data-toggle]').trigger('change');
                    $color_pickers.trigger('change.update');

                    if (undefined !== pum_atb.user_palette) {
                        $color_pickers.iris('option', 'palettes', pum_atb.user_palette);
                    }
                });
        },
        _event_listeners: function () {
            $(document)
                .on('popmake-admin-retheme', PUM_ATB._process_css)
                .on('change', 'select[data-toggle]', PUM_ATB._settingsSelectChanged)
                .on('click', '.pum-image-field .pum-image-select', PUM_ATB._selectSingleImage)
                .on('click', '.pum-image-field .pum-image-edit', PUM_ATB._selectSingleImage)
                .on('click', '.pum-image-field .pum-image-replace', PUM_ATB._selectSingleImage)
                .on('click', '.pum-image-field .pum-image-remove', PUM_ATB._singleImageRemoved)
                .on('change.update change.clear', '.color-picker.initialized', PUM_ATB._update_color_opacity);
        },
        _update_color_opacity: function () {
            var $this = $(this),
                $this_opacity = $this.parents('tr').eq(0).next('tr');

            if ($this.val() !== '') {
                $this_opacity.show();
            } else {
                $this_opacity.hide();
            }
        },
        _process_css: function (e, theme) {
            var sheet = $('#pum-atb-theme-styles'),
                css = '',
                elements = {
                    'overlay': '#PopMake-Preview .example-popup-overlay',
                    'container': '#PopMake-Preview .example-popup',
                    'close': '#PopMake-Preview .close-popup'
                },
                styles,
                selector;

            css += '#PopMake-Preview .example-popup-overlay::after, #PopMake-Preview .example-popup::after, #PopMake-Preview .close-popup::after { content: ""; display: block; position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: -1; }';
            css += '#PopMake-Preview .example-popup-overlay::after { z-index: 0; }';

            for (var element in elements) {
                if (elements.hasOwnProperty(element)) {
                    selector = elements[element];
                    styles = '';
                    switch (theme[element + '_bg_type']) {
                    case 'color':
                        styles += selector + ' {';
                        styles += 'background: ' + PUMUtils.convert_hex(theme[element + '_bg_color'], theme[element + '_bg_opacity']) + '!important;';
                        styles += '}';
                        break;
                    case 'image':
                        styles += selector + ' {';
                        styles += 'background-color: ' + PUMUtils.convert_hex(theme[element + '_bg_color'], theme[element + '_bg_opacity']) + '!important;';
                        styles += 'background-position: ' + theme[element + '_bg_position'] + '!important;';
                        styles += 'background-image: url(' + theme[element + '_bg_image_src'] + ')!important;';
                        styles += 'background-repeat: ' + theme[element + '_bg_repeat'] + '!important;';
                        styles += 'background-size: ' + theme[element + '_bg_size'] + '!important;';
                        styles += 'background-attachment: ' + theme[element + '_bg_attachment'] + '!important;';
                        styles += '}';
                        styles += selector + '::after {';
                        styles += 'background-color: ' + PUMUtils.convert_hex(theme[element + '_bg_overlay_color'], theme[element + '_bg_overlay_opacity']) + '!important;';
                        styles += '}';
                        break;
                    case 'none':
                        styles += selector + ' {';
                        styles += 'background: none!important;';
                        styles += '}';
                        break;
                    }

                    css += styles;
                }
            }

            if (sheet.length) {
                sheet.replaceWith($('<style id="pum-atb-theme-styles">' + css + '</style>'));
            } else {
                $('<style id="pum-atb-theme-styles">' + css + '</style>').appendTo('head');
            }
        },
        /**
         * Callback for when a settings form select has been changed.
         * If toggle data is present, other fields will be toggled
         * when this select changes.
         *
         * @since 1.0
         * @access private
         * @method _settingsSelectChanged
         */
        _settingsSelectChanged: function () {
            var select = $(this),
                toggle = select.data('toggle'),
                val = select.val(),
                i = 0,
                k = 0;

            // TOGGLE sections, fields or tabs.
            if (typeof toggle !== 'undefined') {

                if (typeof toggle === 'string') {
                    toggle = JSON.parse(toggle);
                }

                for (i in toggle) {
                    if (toggle.hasOwnProperty(i)) {
                        for (k in toggle[i].sections) {
                            if (toggle[i].sections.hasOwnProperty(k)) {
                                $('.' + toggle[i].sections[k]).hide();
                            }
                        }
                    }
                }

                if (typeof toggle[val] !== 'undefined') {
                    for (k in toggle[val].sections) {
                        if (toggle[val].sections.hasOwnProperty(k)) {
                            $('.' + toggle[val].sections[k]).show();
                        }
                    }
                }
            }
        },
        /**
         * @since 1.1.0
         * @access private
         * @method _settingsSelectToggle
         * @param {Array} inputArray
         * @param {Function} func
         * @param {String} prefix
         * @param {String} suffix
         */
        _settingsSelectToggle: function (inputArray, func, prefix, suffix) {
            var i = 0;

            suffix = 'undefined' == typeof suffix ? '' : suffix;

            if (typeof inputArray !== 'undefined') {
                for (; i < inputArray.length; i++) {
                    $(prefix + inputArray[i] + suffix)[func]();
                }
            }
        },

        /* Single Image Fields
         ----------------------------------------------------------*/

        /**
         * Shows the single image selector.
         *
         * @since 1.0
         * @access private
         * @method _selectSingleImage
         */
        _selectSingleImage: function () {
            if (PUM_ATB._singleImageSelector === null) {
                PUM_ATB._singleImageSelector = wp.media({
                    title: I10n.selectImage,
                    button: {
                        text: I10n.selectImage
                    },
                    library: {
                        type: 'image'
                    },
                    multiple: false
                });
            }

            PUM_ATB._singleImageSelector.once('open', $.proxy(PUM_ATB._singleImageOpened, this));
            PUM_ATB._singleImageSelector.once('select', $.proxy(PUM_ATB._singleImageSelected, this));
            PUM_ATB._singleImageSelector.open();
        },

        /**
         * Callback for when the single image selector is shown.
         *
         * @since 1.0
         * @access private
         * @method _singleImageOpened
         */
        _singleImageOpened: function () {
            var selection = PUM_ATB._singleImageSelector.state().get('selection'),
                wrap = $(this).closest('.pum-image-field'),
                imageField = wrap.find('input[type=hidden]'),
                image = imageField.val(),
                attachment = null;

            if ($(this).hasClass('pum-image-replace')) {
                selection.reset();
                wrap.addClass('pum-image-empty');
                imageField.val('');
            } else if (image !== '') {
                attachment = wp.media.attachment(image);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            } else {
                selection.reset();
            }
        },

        /**
         * Callback for when a single image is selected.
         *
         * @since 1.0
         * @access private
         * @method _singleImageSelected
         */
        _singleImageSelected: function () {
            var image = PUM_ATB._singleImageSelector.state().get('selection').first().toJSON(),
                wrap = $(this).closest('.pum-image-field'),
                imageField = wrap.find('input[type=hidden]'),
                preview = wrap.find('.pum-image-preview img'),
                srcSelect = wrap.find('select');

            imageField.val(image.id);
            preview.attr('src', PUM_ATB._getImageSrc(image));
            wrap.removeClass('pum-image-empty');
            wrap.find('label.error').remove();
            srcSelect.show();
            srcSelect.html(PUM_ATB._getImageSizeOptions(image));
            srcSelect.trigger('change');
        },

        /**
         * Clears a image that has been selected in a single image field.
         *
         * @since 1.6.4.3
         * @access private
         * @method _singleImageRemoved
         */
        _singleImageRemoved: function () {
            var selection = PUM_ATB._singleImageSelector.state().get('selection'),
                wrap = $(this).closest('.pum-image-field'),
                imageField = wrap.find('input[type=hidden]'),
                srcSelect = wrap.find('select');

            selection.reset();
            wrap.addClass('pum-image-empty');
            imageField.val('');
            srcSelect.html('');
            srcSelect.trigger('change');
        },

        /**
         * Returns the src URL for a image.
         *
         * @since 1.0
         * @access private
         * @method _getImageSrc
         * @param {Object} image A image data object.
         * @return {String} The src URL for a image.
         */
        _getImageSrc: function (image) {
            if (typeof image.sizes === 'undefined') {
                return image.url;
            }
            else if (typeof image.sizes.thumbnail !== 'undefined') {
                return image.sizes.thumbnail.url;
            }
            else {
                return image.sizes.full.url;
            }
        },

        /**
         * Builds the options for a image size select.
         *
         * @since 1.0
         * @access private
         * @method _getImageSizeOptions
         * @param {Object} image A image data object.
         * @return {String} The HTML for the image size options.
         */
        _getImageSizeOptions: function (image) {
            var html = '',
                size = null,
                current = null,
                selected = null,
                title = '',
                titles = {
                    full: I10n.fullSize,
                    large: I10n.large,
                    medium: I10n.medium,
                    thumbnail: I10n.thumbnail
                },
                fullsize = false;

            if (typeof image.sizes !== 'undefined') {
                for (size in image.sizes) {
                    if (image.sizes.hasOwnProperty(size)) {
                        if ('undefined' != typeof titles[size]) {
                            title = titles[size] + ' - ';
                        }
                        else if (pum_atb !== undefined && pum_atb.config !== undefined && pum_atb.config.customImageSizeTitles[size] !== undefined) {
                            title = pum_atb.config.customImageSizeTitles[size] + ' - ';
                        }
                        else {
                            title = '';
                        }

                        if (size === 'full') {
                            fullsize = true;
                        }

                        selected = size === current ? ' selected="selected"' : '';
                        html += '<option value="' + image.sizes[size].url + '"' + selected + '>' + title + image.sizes[size].width + ' x ' + image.sizes[size].height + '</option>';
                    }
                }
            }

            if ( ! fullsize ) {
                selected = ! current || current === 'full' ? ' selected="selected"' : '';
                html += '<option value="' + image.url + '"' + selected + '>' + I10n.fullSize + '</option>';
            }

            return html;
        }
    };

    PUM_ATB._init();
}(jQuery, document, pum_atb));