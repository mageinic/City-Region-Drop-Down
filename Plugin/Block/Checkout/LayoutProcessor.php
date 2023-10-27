<?php
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

namespace MageINIC\CityRegionPostcode\Plugin\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor as CoreLayoutProcessor;
use MageINIC\CityRegionPostcode\Helper\Data as HelperData;
use MageINIC\CityRegionPostcode\Api\Data\CityInterface;
use MageINIC\CityRegionPostcode\Api\Data\PostcodeInterface;
use Magento\Checkout\Helper\Data as CheckoutDataHelper;

/**
 * CityRegionPostcode LayoutProcessor Plugin Class
 */
class LayoutProcessor
{
    /**
     * @var CheckoutDataHelper
     */
    protected CheckoutDataHelper $checkoutHelper;

    /**
     * @var helperData
     */
    protected HelperData $helperData;

    /**
     * @var int|null
     */
    private ?int $citySortOrder = null;

    /**
     * @var int|null
     */
    private ?int $postcodeSortOrder = null;

    /**
     * @param CheckoutDataHelper $checkoutDataHelper
     * @param HelperData $helperData
     */
    public function __construct(
        CheckoutDataHelper $checkoutDataHelper,
        HelperData $helperData
    ) {
        $this->checkoutHelper = $checkoutDataHelper;
        $this->helperData = $helperData;
    }

    /**
     * Process js Layout of block
     *
     * @param CoreLayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        CoreLayoutProcessor $subject,
        array $jsLayout
    ) {

        // Add/update shipping address fields
        $jsLayout = $this->addShippingAddressCityIdField($jsLayout);
        $jsLayout = $this->addShippingAddressPostcodeIdField($jsLayout);
        $jsLayout = $this->addShippingAddressCountryTemplate($jsLayout);
        $jsLayout = $this->addShippingAddressRegionTemplate($jsLayout);
        $jsLayout = $this->addShippingAddressCityTemplate($jsLayout);
        $jsLayout = $this->addShippingAddressPostCodeSearchableFields($jsLayout);
        $jsLayout = $this->addShippingAddressCityVisibility($jsLayout);
        $jsLayout = $this->addShippingAddressPostcodeVisibility($jsLayout);

        // Add/update billing address fields
        if ($this->checkoutHelper->isDisplayBillingOnPaymentMethodAvailable()) {
            $paymentForms = $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['payments-list']['children'];

            foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {
                $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);
                if (! isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                    ['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form'])) {
                    continue;
                }
                $componentConfig = [
                    'scope' => 'billingAddress' . $paymentMethodCode,
                    'customScope' => 'billingAddress' . $paymentMethodCode . '.custom_attributes',
                    'paymentNode' => 'payments-list',
                    'formNode' => $paymentMethodCode . '-form',
                ];
                $jsLayout = $this->addBillingAddressCityIdField($jsLayout, $componentConfig);
                $jsLayout = $this->addBillingAddressPostcodeIdField($jsLayout, $componentConfig);
                $jsLayout = $this->addBillingAddressCountryTemplate($jsLayout, $componentConfig);
                $jsLayout = $this->addBillingAddressRegionTemplate($jsLayout, $componentConfig);
                $jsLayout = $this->addBillingAddressCityTemplate($jsLayout, $componentConfig);
                $jsLayout = $this->addBillingAddressPostCodeSearchableFields($jsLayout, $componentConfig);
                $jsLayout = $this->addBillingAddressCityVisibility($jsLayout, $componentConfig);
                $jsLayout = $this->addBillingAddressPostcodeVisibility($jsLayout, $componentConfig);
            }
        } else {
            $componentConfig = [
                'scope' => 'billingAddressshared',
                'customScope' => 'billingAddressshared',
                'paymentNode' => 'afterMethods',
                'formNode' => 'billing-address-form',
            ];
            $jsLayout = $this->addBillingAddressCityIdField($jsLayout, $componentConfig);
            $jsLayout = $this->addBillingAddressPostcodeIdField($jsLayout, $componentConfig);
            $jsLayout = $this->addBillingAddressCountryTemplate($jsLayout, $componentConfig);
            $jsLayout = $this->addBillingAddressRegionTemplate($jsLayout, $componentConfig);
            $jsLayout = $this->addBillingAddressCityTemplate($jsLayout, $componentConfig);
            $jsLayout = $this->addBillingAddressPostCodeSearchableFields($jsLayout, $componentConfig);
            $jsLayout = $this->addBillingAddressCityVisibility($jsLayout, $componentConfig);
            $jsLayout = $this->addBillingAddressPostcodeVisibility($jsLayout, $componentConfig);
        }

        return $jsLayout;
    }

    /**
     * Shipping AddressCityIdField
     *
     * @param array $jsLayout
     * @return array
     */
    private function addShippingAddressCityIdField(array $jsLayout): array
    {
        $cityIdField = [
            'component' => 'MageINIC_CityRegionPostcode/js/form/element/city',
            'config'    => [
                'customScope'   => 'shippingAddress',
                'customEntry'   => 'shippingAddress.city',
                'template'      => 'ui/form/field',
                'elementTmpl'   => 'ui/form/element/select',
            ],
            'label' => __('City'),
            //'value' => '',
            'dataScope' => 'shippingAddress.' . CityInterface::ID,
            'provider' => 'checkoutProvider',
            'sortOrder' => $this->getCityFieldsSortOrder(),
            'customEntry' => null,
            'visible' => true,
            'options' => [],
            'validation' => [
                'required-entry' => true,
            ],
            'filterBy'  => [
                'target' => '${ $.provider }:shippingAddress.region_id',
                'field'  => 'region_id'
            ],
            'imports'   => [
                'initialOptions'    => 'index = checkoutProvider:dictionaries.' . CityInterface::ID,
                'setOptions'        => 'index = checkoutProvider:dictionaries.' . CityInterface::ID
            ]
        ];
        if ($this->helperData->isCitySearchable()) {
            $cityIdField['config']['elementTmpl'] = 'MageINIC_CityRegionPostcode/form/element/select2';
        }

        $jsLayout['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['shippingAddress']['children']
        ['shipping-address-fieldset']['children'][CityInterface::ID] = $cityIdField;
        return $jsLayout;
    }

    /**
     * Add Shipping AddressPostcodeIdField
     *
     * @param array $jsLayout
     * @return array
     */
    private function addShippingAddressPostcodeIdField(array $jsLayout): array
    {
        $postcodeField = [
            'component' => 'MageINIC_CityRegionPostcode/js/form/element/postcode',
            'config'    => [
                'customScope'   => 'shippingAddress',
                'customEntry'   => 'shippingAddress.postcode',
                'template'      => 'ui/form/field',
                'elementTmpl'   => 'ui/form/element/select',
            ],
            'label' => __('ZIp Code'),
            //'value' => '',
            'dataScope' => 'shippingAddress.' . 'postcode_id',
            'provider' => 'checkoutProvider',
            'sortOrder' => $this->getPostcodeFieldsSortOrder(),
            'customEntry' => null,
            'visible' => true,
            'options' => [],
            'validation' => [
                'required-entry' => true,
            ],
            'filterBy'  => [
                'target' => '${ $.provider }:shippingAddress.city_id',
                'field'  => 'city_id'
            ],
            'imports'   => [
                'initialOptions'    => 'index = checkoutProvider:dictionaries.' . 'postcode_id',
                'setOptions'        => 'index = checkoutProvider:dictionaries.' . 'postcode_id'
            ]
        ];
        if ($this->helperData->isPostcodeSearchable()) {
            $postcodeField['config']['elementTmpl'] = 'MageINIC_CityRegionPostcode/form/element/select2';
        }

        $jsLayout['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['shippingAddress']['children']
        ['shipping-address-fieldset']['children']['postcode_id'] = $postcodeField;
        return $jsLayout;
    }

    /**
     * Add Shipping Address City Visibility
     *
     * @param array $jsLayout
     * @return array
     */
    private function addShippingAddressCityVisibility(array $jsLayout): array
    {
        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']
        )) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['city']['visible'] = false;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['city']['sortOrder'] = $this->getCityFieldsSortOrder();
        }
        return $jsLayout;
    }

    /**
     * Add Shipping Address Postcode Visibility
     *
     * @param array $jsLayout
     * @return array
     */
    private function addShippingAddressPostcodeVisibility(array $jsLayout): array
    {
        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']
        )) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['postcode']['visible'] = false;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['postcode']['sortOrder'] = $this->getPostcodeFieldsSortOrder();
        }
        return $jsLayout;
    }

    /**
     * Add Shipping Address Country Template
     *
     * @param array $jsLayout
     * @return array
     */
    private function addShippingAddressCountryTemplate(array $jsLayout): array
    {
        if ($this->helperData->isCountrySearchable()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['component']
                = 'MageINIC_CityRegionPostcode/js/form/element/region';
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['config']
            ['elementTmpl'] = 'MageINIC_CityRegionPostcode/form/element/select2';
        }
        return $jsLayout;
    }

    /**
     * Add Shipping Address Region Template
     *
     * @param array $jsLayout
     * @return array
     */
    private function addShippingAddressRegionTemplate(array $jsLayout): array
    {
        if ($this->helperData->isRegionSearchable()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['component']
                = 'MageINIC_CityRegionPostcode/js/form/element/region';
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['config']
            ['elementTmpl'] = 'MageINIC_CityRegionPostcode/form/element/select2';
        }
        return $jsLayout;
    }

    /**
     * Add shipping address City Template
     *
     * @param array $jsLayout
     * @return array
     */
    private function addShippingAddressCityTemplate(array $jsLayout): array
    {
        if ($this->helperData->isCitySearchable()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city_id']['component']
                = 'MageINIC_CityRegionPostcode/js/form/element/city';
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city_id']['config']
            ['elementTmpl'] = 'MageINIC_CityRegionPostcode/form/element/select2';
        }
        return $jsLayout;
    }
    /**
     * Add Shipping Address PostCode Searchable Fields
     *
     * @param array $jsLayout
     * @return array
     */
    private function addShippingAddressPostCodeSearchableFields(array $jsLayout): array
    {
        if ($this->helperData->isPostcodeSearchable()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode_id']['component']
                = 'MageINIC_CityRegionPostcode/js/form/element/postcode';
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode_id']['config']
            ['elementTmpl'] = 'MageINIC_CityRegionPostcode/form/element/select2';
        }
        return $jsLayout;
    }

    /**
     * Add Billing Address CityId Field
     *
     * @param array $jsLayout
     * @param array $componentConfig
     * @return array
     */
    private function addBillingAddressCityIdField(array $jsLayout, array $componentConfig): array
    {
        $cityField = [
            'component' => 'MageINIC_CityRegionPostcode/js/form/element/city',
            'config' => [
                'customScope'   => $componentConfig['customScope'],
                'customEntry'   => $componentConfig['scope'] . '.city',
                'template'      => 'ui/form/field',
                'elementTmpl'   => 'ui/form/element/select'
            ],
            'label' => __('City'),
            //'value' => '',
            'dataScope' =>  $componentConfig['customScope'] . '.' . CityInterface::ID,
            'provider' => 'checkoutProvider',
            'sortOrder' => $this->getCityFieldsSortOrder(),
            'customEntry' => null,
            'visible' => true,
            'options' => [],
            'validation' => [
                'required-entry' => true,
            ],
            'filterBy' => [
                'target'    => '${ $.provider }:${ $.parentScope }.region_id',
                'field'     => 'region_id'
            ],
            'imports'   => [
                'initialOptions'    => 'index = checkoutProvider:dictionaries' . '.' . CityInterface::ID,
                'setOptions'        => 'index = checkoutProvider:dictionaries' . '.' . CityInterface::ID
            ]
        ];

        if ($this->helperData->isCitySearchable()) {
            $cityField['config']['elementTmpl'] = 'MageINIC_CityRegionPostcode/form/element/select2';
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]['children']
        ['form-fields']['children'][CityInterface::ID] = $cityField;
        return $jsLayout;
    }

    /**
     * Add Billing Address PostcodeId Field
     *
     * @param array $jsLayout
     * @param array $componentConfig
     * @return array
     */
    private function addBillingAddressPostcodeIdField(array $jsLayout, array $componentConfig): array
    {
        $postcodeField = [
            'component' => 'MageINIC_CityRegionPostcode/js/form/element/postcode',
            'config' => [
                'customScope'   => $componentConfig['customScope'],
                'customEntry'   => $componentConfig['scope'] . '.postcode',
                'template'      => 'ui/form/field',
                'elementTmpl'   => 'ui/form/element/select'
            ],
            'label' => __('Postcode'),
            //'value' => '',
            'dataScope' =>  $componentConfig['customScope'] . '.' . PostcodeInterface::ID,
            'provider' => 'checkoutProvider',
            'sortOrder' => $this->getPostcodeFieldsSortOrder(),
            'customEntry' => null,
            'visible' => true,
            'options' => [],
            'validation' => [
                'required-entry' => true,
            ],
            'filterBy' => [
                'target'    => '${ $.provider }:${ $.parentScope }.city_id',
                'field'     => 'city_id'
            ],
            'imports'   => [
                'initialOptions'    => 'index = checkoutProvider:dictionaries' . '.' . PostcodeInterface::ID,
                'setOptions'        => 'index = checkoutProvider:dictionaries' . '.' . PostcodeInterface::ID
            ]
        ];

        if ($this->helperData->isPostcodeSearchable()) {
            $postcodeField['config']['elementTmpl'] = 'MageINIC_CityRegionPostcode/form/element/select2';
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]['children']
        ['form-fields']['children'][PostcodeInterface::ID] = $postcodeField;
        return $jsLayout;
    }

    /**
     * Add Billing Address City Visibility
     *
     * @param array $jsLayout
     * @param array $componentConfig
     * @return array
     */
    private function addBillingAddressCityVisibility(array $jsLayout, array $componentConfig): array
    {
        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children'][$componentConfig['paymentNode']]['children']
            [$componentConfig['formNode']]['children']['form-fields']['children']['city']
        )) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['city']['visible'] = false;

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['city']['sortOrder'] = $this->getCityFieldsSortOrder();
        }
        return $jsLayout;
    }

    /**
     * Add Billing Address Postcode Visibility
     *
     * @param array $jsLayout
     * @param array $componentConfig
     * @return array
     */
    private function addBillingAddressPostcodeVisibility(array $jsLayout, array $componentConfig): array
    {
        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children'][$componentConfig['paymentNode']]['children']
            [$componentConfig['formNode']]['children']['form-fields']['children']['postcode']
        )) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['postcode']['visible'] = false;

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['postcode']['sortOrder'] = $this->getPostcodeFieldsSortOrder();
        }
        return $jsLayout;
    }

    /**
     * Add Billing Address Country Template
     *
     * @param array $jsLayout
     * @param array $componentConfig
     * @return array
     */
    private function addBillingAddressCountryTemplate(array $jsLayout, array $componentConfig): array
    {
        if ($this->helperData->isCountrySearchable()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['country_id']['component']
                = 'MageINIC_CityRegionPostcode/js/form/element/region';

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['country_id']['config']['elementTmpl']
                = 'MageINIC_CityRegionPostcode/form/element/select2';
        }

        return $jsLayout;
    }

    /**
     * Add Billing Address Region Template
     *
     * @param array $jsLayout
     * @param array $componentConfig
     * @return array
     */
    private function addBillingAddressRegionTemplate(array $jsLayout, array $componentConfig): array
    {
        if ($this->helperData->isRegionSearchable()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['region_id']['component']
                = 'MageINIC_CityRegionPostcode/js/form/element/region';

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['region_id']['config']['elementTmpl']
                = 'MageINIC_CityRegionPostcode/form/element/select2';
        }

        return $jsLayout;
    }

    /**
     * Add Billing Address City Template
     *
     * @param array $jsLayout
     * @param array $componentConfig
     * @return array
     */
    private function addBillingAddressCityTemplate(array $jsLayout, array $componentConfig): array
    {
        if ($this->helperData->isCitySearchable()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['city_id']['component']
                = 'MageINIC_CityRegionPostcode/js/form/element/city';

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['city_id']['config']['elementTmpl']
                = 'MageINIC_CityRegionPostcode/form/element/select2';
        }

        return $jsLayout;
    }

    /**
     * Add Billing Address PostCode Searchable Field's
     *
     * @param array $jsLayout
     * @param array $componentConfig
     * @return array
     */
    private function addBillingAddressPostCodeSearchableFields(array $jsLayout, array $componentConfig): array
    {
        if ($this->helperData->isPostcodeSearchable()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['postcode_id']['component']
                = 'MageINIC_CityRegionPostcode/js/form/element/postcode';

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'][$componentConfig['paymentNode']]['children'][$componentConfig['formNode']]
            ['children']['form-fields']['children']['postcode_id']['config']['elementTmpl']
                = 'MageINIC_CityRegionPostcode/form/element/select2';
        }

        return $jsLayout;
    }

    /**
     * Get City Field SortOrder
     *
     * @return int|mixed
     */
    private function getCityFieldsSortOrder()
    {
        if ($this->citySortOrder) {
            return $this->citySortOrder;
        }

        $sortOrder = $this->helperData->isCitySortOrder();
        if (!$sortOrder) {
            $sortOrder = 91;
        }
        $this->citySortOrder = $sortOrder;
        return $this->citySortOrder;
    }

    /**
     * Get PostCode Field SortOrder
     *
     * @return int|mixed
     */
    private function getPostCodeFieldsSortOrder()
    {
        if ($this->postcodeSortOrder) {
            return $this->postcodeSortOrder;
        }

        $sortOrder = $this->helperData->isPostcodeSortOrder();
        if (!$sortOrder) {
            $sortOrder = 95;
        }
        $this->postcodeSortOrder = $sortOrder;
        return $this->postcodeSortOrder;
    }
}
