/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'domReady',
    'priceUtils'
], function ($, domReady, utils) {
    'use strict';

    var globalOptions = {
        config: {},
        parentSelector: '.column.main',
        galleryPlaceholderSelector: '[data-gallery-role=gallery-placeholder]',
        galleryLoadedEventName: 'gallery:loaded amasty_gallery:loaded',
        galleryIdentifier: [
            '.gallery-placeholder .fotorama .fotorama__stage .fotorama__stage__shaft',
            '.gallery-placeholder #amasty-gallery #amasty-main-container'
        ],
        optionTypes: {
            dropdown: 'select.product-custom-option',
            radio: 'input[type="radio"].product-custom-option',
            checkbox: 'input[type="checkbox"].product-custom-option'
        }
    };

    $.widget('mage.productOptionsBadge', {
        options: globalOptions,

        _create: function createProductOptionsBadge() {
        },

        _init: function initProductOptionsBadge() {
            var self = this;

            $(self.options.galleryPlaceholderSelector, self.options.parentSelector).on(self.options.galleryLoadedEventName, function() {
                self.handleAll();

                var observer = new MutationObserver(function(mutations) {
                    $.each(mutations, function(key, mutation) {
                        $.each(mutation.addedNodes, function(key, addedNode) {
                            if ($(addedNode).attr('id') === 'amasty-main-image') {
                                self.handleAll();
                            }
                        });
                    });
                });

                var amastyContainer = document.querySelector('#amasty-main-container');
                if (amastyContainer) {
                    observer.observe(document.querySelector('#amasty-main-container'), {
                        subtree: true,
                        childList: true,
                    });
                }
            });

            domReady(function() {
                $.each(self.options.optionTypes, function(type, selector) {
                    $(selector, self.element).on('change', function() {
                        self.handleOptionChange(type, $(this));
                    })
                });
            });
        },

        handleAll: function handleAll() {
            var self = this;

            $.each(self.options.optionTypes, function(type, selector) {
                $(selector, self.element).on('change', function() {
                    self.handleOptionChange(type, $(this));
                })
            });
        },

        handleOptionChange: function handleOptionChange(type, inputElement) {
            var self = this;

            if (type === 'dropdown') {
                self.handleDropdown(inputElement);
            } else if (type === 'radio') {
                self.handleRadio(inputElement);
            } else if (type === 'checkbox') {
                self.handleCheckbox(inputElement);
            }
        },

        handleDropdown: function handleDropdown(selectElement) {
            var self = this;

            var optionId = utils.findOptionId(selectElement);

            if (optionId) {
                var isMultiSelect = selectElement.attr('multiple');

                if (isMultiSelect) {
                    var optionValueIds = selectElement.val();

                    if (! Array.isArray(optionValueIds)) {
                        optionValueIds = [optionValueIds];
                    }

                    var currentOverlayElements =
                        $('.product_custom_option_overlay[data-option-id="' + optionId + '"]');

                    currentOverlayElements.each(function() {
                        var currentOptionValueId = $(this).data('option-value-id');

                        if ($.inArray(currentOptionValueId, optionValueIds) === -1) {
                            self.remove(optionId, currentOptionValueId);
                        }
                    });

                    $.each(optionValueIds, function(index, optionValueId) {
                        self.add(optionId, optionValueId, true);
                    });
                } else {
                    self.remove(optionId);

                    var optionValueId = selectElement.val();

                    if (optionValueId) {
                        self.add(optionId, optionValueId, false);
                    }
                }
            }

            self.adjust();
        },

        handleRadio: function handleRadio(inputElement) {
            var self = this;

            if (inputElement.is(':checked')) {
                var optionId = utils.findOptionId(inputElement);

                if (optionId) {
                    self.remove(optionId);

                    var optionValueId = inputElement.val();

                    if (optionValueId) {
                        self.add(optionId, optionValueId, false);
                    }
                }

                self.adjust();
            }
        },

        handleCheckbox: function handleRadio(inputElement) {
            var self = this;

            var optionId = utils.findOptionId(inputElement);

            if (optionId) {
                var optionValueId = inputElement.val();

                if (optionValueId) {
                    if (inputElement.is(':checked')) {
                        self.add(optionId, optionValueId, true);
                    } else {
                        self.remove(optionId, optionValueId);
                    }

                    self.adjust();
                }
            }
        },

        add: function addProductOptionsBadge(optionId, optionValueId, isMultiSelect) {
            var self = this;

            var optionConfig = self.options.config[optionId];

            if (optionConfig) {
                var optionValuesConfig = optionConfig.values;

                if (optionValuesConfig) {
                    var optionValueBadgeUrl = optionValuesConfig[optionValueId];

                    if (optionValueBadgeUrl) {
                        $.each(self.getGalleryIdentifier(), function(key, identifier) {
                            $(identifier, self.options.parentSelector).each(function() {
                                var imageElement = $(this);

                                var overlayElementId = isMultiSelect ?
                                    'product-custom-option-overlay-' + optionId + '-' + optionValueId :
                                    'product-custom-option-overlay-' + optionId;

                                var overlayElement = $('<div>', {
                                    id: overlayElementId,
                                    class: 'product_custom_option_overlay',
                                    'data-option-id': optionId,
                                    'data-option-value-id': optionValueId
                                });
                                overlayElement.appendTo(imageElement);

                                var overlayImageElement = $('<img>', {
                                    src: optionValueBadgeUrl,
                                    class: 'product_custom_option_overlay_image'
                                });
                                overlayImageElement.appendTo(overlayElement);
                            });
                        });
                    }
                }
            }
        },

        getGalleryIdentifier: function getGalleryIdentifier() {
            return this.options.galleryIdentifier;
        },

        remove: function removeProductOptionsBadge(optionId, optionValueId) {
            if (optionValueId) {
                $('#product-custom-option-overlay-' + optionId + '-' + optionValueId).each(function() {
                    $(this).remove();
                });
            } else {
                $('#product-custom-option-overlay-' + optionId).each(function() {
                    $(this).remove();
                });
            }
        },

        adjust: function adjustProductOptionsBadge() {
            var overlayCount = 0;

            $.each($('.product_custom_option_overlay').get().reverse(), function() {
                var overlayElement = $(this);
                overlayElement.removeClass('product_custom_option_overlay-1');
                overlayElement.removeClass('product_custom_option_overlay-2');
                overlayElement.removeClass('product_custom_option_overlay-3');
                overlayElement.removeClass('product_custom_option_overlay-4');
                overlayElement.removeClass('product_custom_option_overlay-5');

                overlayCount++;

                if (overlayCount <= 5) {
                    overlayElement.addClass('product_custom_option_overlay-' + overlayCount);
                    overlayElement.show();
                } else {
                    overlayElement.hide();
                }
            });
        }
    });

    return $.mage.productOptionsBadge;
});
