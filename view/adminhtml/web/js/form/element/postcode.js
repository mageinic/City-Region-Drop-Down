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
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            customName: '${ $.parentName }.postcode',
            imports: {
                update: '${ $.parentName }.city_id:value'
            }
        },

        /**
         * Creates input from template, renders it via renderer.
         *
         * @returns {Object} Chainable.
         */
        initInput: function () {
            return this;
        },

        /**
         * Updates on city change
         *
         * @param {String} value
         */
        update: function (value) {
            var source = this.initialOptions,
                field = this.filterBy.field,
                result,
                initValue;

            result = _.filter(source, function (item) {
                return item[field] === value;
            });

            if (result.length > 0 && value != undefined) {
                this.setVisible(true);
                this.toggleInput(false);

                this.setOptions(result);
                let currentValue = this.initialValue;
                initValue = _.filter(result, function (item) {
                    return item.value === currentValue;
                });
                if (initValue.length > 0) {
                    this.value(currentValue);
                }
            } else {
                this.setVisible(false);
                this.toggleValue('');
                this.toggleInput(true);
                this.setOptions([]);
                this.value('');
            }
        },

        /**
         * @inheritDoc
         */
        filter: function (value, field) {
            var city = registry.get(this.parentName + '.' + 'city_id'),
                value = (value == undefined) ? '' : value;
            if (city) {
                this._super(value, field);
            } else if (value == '' && this.provider == 'checkoutProvider') {
                this.setVisible(false);
                this.toggleValue('');
                this.toggleInput(true);
                this.setOptions([]);
                this.value('');
            }
        },

        /**
         * Callback that fires when 'value' property is updated.
         */
        onUpdate: function () {
            this._super();
            var value = this.value(),
                result;
            result = this.indexedOptions[value];
            if (result != undefined) {
                this.toggleValue(result.label);
            } else {
                this.toggleValue('');
            }
        },

        /**
         * Change value for input.
         */
        toggleValue: function (value) {
            registry.get(this.customName, function (input) {
                input.value(value);
            });
        }
    });
});
