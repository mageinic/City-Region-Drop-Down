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

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction, messageContainer) {
            var address = quote.shippingAddress();
            if (address !== null) {
                var cityIdElem = $("#shipping-new-address-form [name = 'city_id'] option:selected");
                var postcodeIdElem = $("#shipping-new-address-form [name = 'postcode_id'] option:selected");
                var city = cityIdElem.text();
                var postcode = postcodeIdElem.text();
                var cityId = cityIdElem.val();
                var postcodeId = postcodeIdElem.val();
                messageContainer.city = city;
                messageContainer.postcode = postcode;
                messageContainer.city_id = cityId;
                messageContainer.postcode_id = postcodeId;
            }
            // pass execution to original action
            return originalAction(messageContainer);
        });
    };
});
