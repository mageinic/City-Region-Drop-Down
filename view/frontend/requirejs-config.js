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

var config = {
	map: {
		'*': {
			cityUpdater: 'MageINIC_CityRegionPostcode/js/city-updater',
            postcodeUpdater: 'MageINIC_CityRegionPostcode/js/postcode-updater',
			select2: 'MageINIC_CityRegionPostcode/js/select2.min',

		}
	},
	config: {
		mixins: {
            'Magento_Checkout/js/action/create-shipping-address': {
                'MageINIC_CityRegionPostcode/js/action/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/select-shipping-address' : {
                'MageINIC_CityRegionPostcode/js/action/select-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
				'MageINIC_CityRegionPostcode/js/action/set-shipping-information-mixin': true
			},
            'Magento_Checkout/js/action/select-billing-address' : {
                'MageINIC_CityRegionPostcode/js/action/select-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'MageINIC_CityRegionPostcode/js/action/set-payment-information-mixin': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'MageINIC_CityRegionPostcode/js/view/billing-address-mixin': true
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'MageINIC_CityRegionPostcode/js/view/shipping-address/address-renderer/default-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'MageINIC_CityRegionPostcode/js/view/shipping-information/address-renderer/default-mixin': true
            }

		}
	}
};
