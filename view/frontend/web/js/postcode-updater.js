/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_CityRegionPostcode
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

define([
    'jquery',
    'mage/template',
    'underscore',
    'select2',
    'jquery/ui',
    'mage/validation',
    'Magento_Checkout/js/region-updater'
], function ($, mageTemplate, _, select2) {
    'use strict';
    $.widget('mage.postcodeUpdater', {
        options: {
            postcodeTemplate: '<option value="<%- data.value %>" <% if (data.isSelected) { %>selected="selected"<% } %>>' +
                '<%- data.title %>' +
                '</option>',
            isPostcodeRequired: true,
            currentPostcode: null
        },

        _create: function () {
            var cityList = $(this.options.cityListId),
                cityInput = $(this.options.cityInputId);

            this.currentPostcodeOption = this.options.currentPostcode;
            this.postcodeTmpl = mageTemplate(this.options.postcodeTemplate);

            if ($(cityList).is(":visible")) {
                this._updatePostcode($(cityList).find('option:selected').val());
            } else {
                this._updatePostcode(null);
            }

            $(this.options.postcodeListId).on('change', $.proxy(function (e) {
                    this.setOption = false;
                    this.currentPostcodeOption = $(e.target).val();
                    console.log(this.options.postcodeInputId);
                    if ($(e.target).val() != '') {
                        $(this.options.postcodeInputId).val($(e.target).find('option:selected').text());
                    }
                }, this)
            );

            $(this.options.postcodeInputId).on('focusout', $.proxy(function () {
                    this.setOption = true;
                }, this)
            );

            this._bindCountryElement();
            this._bindRegionElement();
        },

        _bindCountryElement: function () {
            $(this.options.countryListId).on('change', $.proxy(function (e) {
                $(this.options.postcodeListId).val('');
                $(this.options.postcodeInputId).val('');
                if ($(this.options.cityListId) !== 'undefined'
                    && $(this.options.cityListId).find('option:selected').val() != ''
                ) {
                    this._updatePostcode($(this.options.cityListId).find('option:selected').val());
                } else {
                    this._updatePostcode(null);
                }
            }, this));
        },

        _bindRegionElement: function () {
            $(this.options.cityListId).on('change', $.proxy(function (e) {
                $(this.options.postcodeListId).val('');
                $(this.options.postcodeInputId).val('');
                this._updatePostcode($(e.target).val());
            }, this));

            $(this.options.cityInputId).on('focusout', $.proxy(function () {
                this._updatePostcode(null);
            }, this));
        },

        _updatePostcode: function (cityId) {
            var postcodeList = $(this.options.postcodeListId),
                postcodeInput = $(this.options.postcodeInputId),
                label = postcodeList.parent().siblings('label'),
                requiredLabel = postcodeList.parents('div.field');

            this._clearError();

            // populate postcode dropdown list if available else use input box
            if (cityId && this.options.postcodeJson[cityId]) {
                this._removeSelectOptions(postcodeList);
                $.each(this.options.postcodeJson[cityId], $.proxy(function (key, value) {
                    this._renderSelectOption(postcodeList, key, value);
                }, this));

                if (this.currentPostcodeOption && postcodeList.find('option[value="' + this.currentPostcodeOption + '"]').length > 0) {
                    postcodeList.val(this.currentPostcodeOption);
                }

                if (this.setOption) {
                    postcodeList.find('option').filter(function () {
                        return this.text === postcodeInput.val();
                    }).attr('selected', true);
                }

                if (this.options.isPostcodeRequired) {
                    postcodeList.addClass('required-entry').removeAttr('disabled');
                    requiredLabel.addClass('required');
                } else {
                    postcodeList.removeClass('required-entry validate-select').removeAttr('data-validate');
                    requiredLabel.removeClass('required');
                }

                postcodeList.show();
                postcodeInput.removeClass('required-entry').hide();
                label.attr('for', postcodeList.attr('id'));

                if (this.options.isSearchable) {
                    $(this.options.postcodeListId).select2({
                        width: '100%'
                    });
                }
            } else {
                if (this.options.isPostcodeRequired) {
                    postcodeInput.addClass('required-entry').removeAttr('disabled');
                    requiredLabel.addClass('required');
                } else {
                    requiredLabel.removeClass('required');
                    postcodeInput.removeClass('required-entry');
                }

                postcodeList.removeClass('required-entry').prop('disabled', 'disabled').hide();
                postcodeInput.show();
                label.attr('for', postcodeInput.attr('id'));

                if (this.options.isSearchable) {
                    $(this.options.postcodeListId).data('select2') && $(this.options.postcodeListId).data('select2').destroy();
                }
            }
            postcodeList.attr('defaultvalue', this.options.defaultPostcodeId);
        },

        _removeSelectOptions: function (selectElement) {
            selectElement.find('option').each(function (index) {
                if (index) {
                    $(this).remove();
                }
            });
        },

        _renderSelectOption: function (selectElement, key, value) {
            selectElement.append($.proxy(function () {
                var name = value.postcode,
                    tmplData,
                    tmpl;

                if (value.code && $(name).is('span')) {
                    key = value.code;
                    value.postcode = $(name).text();
                }

                tmplData = {
                    value: key,
                    title: value.postcode,
                    isSelected: false
                };

                if (this.options.defaultPostcodeId === key) {
                    tmplData.isSelected = true;
                }

                tmpl = this.postcodeTmpl({
                    data: tmplData
                });

                return $(tmpl);
            }, this));
        },

        _clearError: function () {
            var args = ['clearError', this.options.postcodeListId, this.options.postcodeInputId];

            if (this.options.clearError && typeof this.options.clearError === 'function') {
                this.options.clearError.call(this);
            } else {
                if (!this.options.form) {
                    this.options.form = this.element.closest('form').length ? $(this.element.closest('form')[0]) : null;
                }

                this.options.form = $(this.options.form);
                this.options.form && this.options.form.data('validator') &&
                this.options.form.validation.apply(this.options.form, _.compact(args));

                // Clean up errors on city & zip fix
                $(this.options.postcodeInputId).removeClass('mage-error').parent().find('[generated]').remove();
                $(this.options.postcodeListId).removeClass('mage-error').parent().find('[generated]').remove();
            }
        }
    });
    return $.mage.postcodeUpdater;
});
