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
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';
    return function (setBillingAddressAction) {
        return wrapper.wrap(setBillingAddressAction, function (originalAction, billingAddress) {
            if (billingAddress['extension_attributes'] === undefined) {
                billingAddress['extension_attributes'] = {};
            }

            billingAddress['extension_attributes']['city_id'] = 0;
            billingAddress['extension_attributes']['postcode_id'] = 0;
            if (billingAddress.customAttributes !== undefined) {
                $.each(billingAddress.customAttributes, function(index, attribute) {
                    if (attribute.attribute_code !== undefined && attribute.attribute_code === 'city_id') {
                        // in case of new address
                        billingAddress['extension_attributes']['city_id'] = attribute.value;
                    } else if (index == 'city_id') {
                        // in case of old address
                        billingAddress['extension_attributes']['city_id'] = attribute;
                    }
                    if (attribute.attribute_code !== undefined && attribute.attribute_code === 'postcode_id') {
                        // in case of new address
                        billingAddress['extension_attributes']['postcode_id'] = attribute.value;
                    } else if (index == 'postcode_id') {
                        // in case of old address
                        billingAddress['extension_attributes']['postcode_id'] = attribute;
                    }
                });
            }
            // pass execution to original action
            return originalAction(billingAddress);
        });
    };
});
