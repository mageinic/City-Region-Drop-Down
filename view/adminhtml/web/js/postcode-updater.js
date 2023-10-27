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
    'prototype',
    'mage/adminhtml/events',
    'mage/adminhtml/form'
], function (jQuery, Prototype, Events, Form) {
    window.PostcodeUpdater = Class.create();
    PostcodeUpdater.prototype = {
        initialize: function (
            regionSelectEl,
            cityTextEl,
            citySelectEl,
            postcodeTextEl,
            postcodeSelectEl,
            postcodesConfig,
            disableAction
        ) {

            this.regionSelectEl = $(regionSelectEl);
            this.cityTextEl = $(cityTextEl);
            this.citySelectEl = $(citySelectEl);
            this.postcodeTextEl = $(postcodeTextEl);
            this.postcodeSelectEl = $(postcodeSelectEl);
            this.postcodesConfig = postcodesConfig;
            this.sameAsBillingConfig = jQuery('#order-shipping_same_as_billing');
            this.disableAction = typeof disableAction === 'undefined' ? 'hide' : disableAction;

            if (this.postcodeSelectEl.options.length <= 1) {
                this.update();
                this.postcodeUpdate();
            } else {
                this.lastCitySelectId = this.citySelectEl.value;
            }

            this.cityTextEl.changeUpdater = this.update.bind(this);
            this.citySelectEl.changeUpdater = this.update.bind(this);
            if(this.sameAsBillingConfig.length){
                this.sameAsBillingConfig = this.update.bind(this);
            }
            this.sameAsBillingConfig.changeUpdater = this.update.bind(this);
            if(this.sameAsBillingConfig.length) {
                Event.observe(this.sameAsBillingConfig, 'change', this.postcodeUpdate.bind(this));
            }
            Event.observe(this.citySelectEl, 'change', this.update.bind(this));
            /** Field change the value fill **/
            Event.observe(this.postcodeSelectEl, 'change', this.postcodeUpdate.bind(this));
            Event.observe(this.postcodeTextEl, 'change', this.postcodeUpdate.bind(this));

        },
        update: function () {
            var option, postcode, def, postcodeId;

            //typeof this.config == 'undefined'
            console.log(this.citySelectEl.value)
            if (this.postcodesConfig[this.citySelectEl.value] == undefined) {
                console.log('mp')

                if (this.disableAction == 'hide') { //eslint-disable-line eqeqeq
                    if (this.postcodeTextEl) {
                        this.postcodeTextEl.style.display = '';
                        this.postcodeTextEl.style.disabled = false;
                    }
                    this.postcodeSelectEl.style.display = 'none';
                    this.postcodeSelectEl.disabled = true;


                } else if (this.disableAction == 'disable') { //eslint-disable-line eqeqeq
                    if (this.postcodeTextEl) {
                        this.postcodeTextEl.disabled = false;
                    }
                    this.postcodeSelectEl.disabled = true;


                } else if (this.disableAction == 'nullify') { //eslint-disable-line eqeqeq
                    this.postcodeSelectEl.options.length = 1;
                    this.postcodeSelectEl.value = '';
                    this.postcodeSelectEl.selectedIndex = 0;
                    this.lastCitySelectId = '';
                }
                this.setMarkDisplay(this.postcodeSelectEl, false);

            } else {
                if (this.lastCitySelectId != this.citySelectEl.value) {
                    def = this.postcodeSelectEl.getAttribute('defaultValue');
                    if (this.postcodeTextEl) {
                        if (!def) {
                            def = this.postcodeTextEl.value.toLowerCase();
                        }
                        this.postcodeTextEl.value = '';
                    }

                    this.postcodeSelectEl.options.length = 1;

                    for (postcodeId in this.postcodesConfig[this.citySelectEl.value]) {

                        postcode = this.postcodesConfig[this.citySelectEl.value][cityId];

                        option = document.createElement('OPTION');
                        option.value = postcodeId;
                        option.text = postcode.name.stripTags();
                        option.title = postcode.name;
                        if (this.postcodeSelectEl.options.add) {
                            this.postcodeSelectEl.options.add(option);
                        } else {
                            this.postcodeSelectEl.appendChild(option);
                        }
                        if (postcodeId == def
                            || postcode.name.toLowerCase() == def
                            || (postcode.code && postcode.code.toLowerCase() == def)
                        ) {
                            this.postcodeSelectEl.value = postcodeId;
                        }
                    }
                    //disable city text
                    /* OLD CODE
                    this.cityTextEl.style.display = 'none';
                    this.citySelectEl.style.display = 'block';
                    this._makeCitySelectRequired();
                    */
                }

                if (this.disableAction == 'hide') { //eslint-disable-line eqeqeq
                    if (this.postcodeTextEl) {
                        this.postcodeTextEl.style.display = 'none';
                        this.postcodeTextEl.style.disabled = true;
                    }
                    this.postcodeSelectEl.style.display = '';
                    this.postcodeSelectEl.disabled = false;
                } else if (this.disableAction == 'disable') { //eslint-disable-line eqeqeq
                    if (this.postcodeTextEl) {
                        this.postcodeTextEl.disabled = true;
                    }
                    this.postcodeSelectEl.disabled = false;
                }

                this.setMarkDisplay(this.postcodeSelectEl, true);
                this.lastCitySelectId = this.citySelectEl.value;

            }
            this._checkCityRequired();
        },
        _makePostcodeTextRequired: function () {
            this.postcodeTextEl.addClassName('required-entry');
            this.postcodeSelectEl.style.display = 'none';

            if (this.postcodeSelectEl.hasClassName('validate-select')) {
                this.postcodeSelectEl.removeClassName('validate-select');
            }
            if (this.postcodeSelectEl.hasClassName('required-entry')) {
                this.postcodeSelectEl.removeClassName('required-entry');
            }

        },
        _makePostcodeSelectRequired: function () {
            this.postcodeSelectEl.addClassName('required-entry').addClassName('validate-select');
            if (this.postcodeTextEl.hasClassName('required-entry')) {
                this.postcodeTextEl.removeClassName('required-entry');
            }
        },
        /**
         * @param {HTMLElement} elem
         * @param {*} display
         */
        setMarkDisplay: function (elem, display) {
            var marks;

            if (elem.parentNode.parentNode) {
                marks = Element.select(elem.parentNode.parentNode, '.required');

                if (marks[0]) {
                    display ? marks[0].show() : marks[0].hide();
                }
            }
        },
        _checkCityRequired: function () {
            var label, wildCard, elements, that, postcodeRequired = true;
            elements = [this.postcodeTextEl, this.postcodeSelectEl];
            that = this;
            elements.each(function (currentElement) {
                var form, validationInstance, field, topElement;
                if (!currentElement) {
                    return;
                }
                form = currentElement.form;
                validationInstance = form ? jQuery(form).data('validation') : null;
                field = currentElement.up('.field') || new Element('div');

                if (validationInstance) {
                    validationInstance.clearError(currentElement);
                }
                label = $$('label[for="' + currentElement.id + '"]')[0];
                if (label) {
                    wildCard = label.down('em') || label.down('span.required');
                    topElement = label.up('tr') || label.up('li');
                }
                if (label && wildCard) {
                    wildCard.show();
                }
                if (!currentElement.visible()) {
                    if (field.hasClassName('required')) {
                        field.removeClassName('required');
                    }

                    if (currentElement.hasClassName('required-entry')) {
                        currentElement.removeClassName('required-entry');
                    }

                    if (currentElement.tagName.toLowerCase() == 'select' && //eslint-disable-line eqeqeq
                        currentElement.hasClassName('validate-select')
                    ) {
                        currentElement.removeClassName('validate-select');
                    }
                } else {
                    if (!field.hasClassName('required')) {
                        field.addClassName('required');
                    }

                    if (!currentElement.hasClassName('required-entry')) {
                        currentElement.addClassName('required-entry');
                    }

                    if (currentElement.tagName.toLowerCase() == 'select' && //eslint-disable-line eqeqeq
                        !currentElement.hasClassName('validate-select')
                    ) {
                        currentElement.addClassName('validate-select');
                    }
                }
            });
        },
        postcodeUpdate: function () {
            if (this.postcodeSelectEl.visible()) {
                if (this.postcodeSelectEl.options[this.postcodeSelectEl.selectedIndex].value !== '') {
                    this.postcodeTextEl.value = this.postcodeSelectEl.options[this.postcodeSelectEl.selectedIndex].text;
                } else {
                    this.postcodeTextEl.value = '';
                }

            } else {
                this.postcodeSelectEl.selected = false;

            }
        }
    }
});
