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

namespace MageINIC\CityRegionPostcode\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * CityRegionPostcode Helper Data Class
 */
class Data extends AbstractHelper
{
    public const MODULE_ENABLE       = 'mageinic_cityregionpostcode/general/enabled';
    public const COUNTRY_SEARCHABLE  = 'mageinic_cityregionpostcode/country/searchable';
    public const REGION_SEARCHABLE   = 'mageinic_cityregionpostcode/region/searchable';
    public const IS_CITY_ACTIVE      = 'mageinic_cityregionpostcode/city/enable';
    public const CITY_SEARCHABLE     = 'mageinic_cityregionpostcode/city/searchable';
    public const CITY_SORT_ORDER     = 'mageinic_cityregionpostcode/city/sort_order';
    public const IS_POSTCODE_ACTIVE  = 'mageinic_cityregionpostcode/postcode/enable';
    public const POSTCODE_SEARCHABLE = 'mageinic_cityregionpostcode/postcode/searchable';
    public const POSTCODE_SORT_ORDER = 'mageinic_cityregionpostcode/postcode/sort_order';
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get BaseUrl
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            UrlInterface::URL_TYPE_WEB,
            true
        );
    }

    /**
     * Is Country Searchable
     *
     * @return mixed
     */
    public function isCountrySearchable()
    {
        return $this->scopeConfig->getValue(
            self::COUNTRY_SEARCHABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Region Searchable
     *
     * @return mixed
     */
    public function isRegionSearchable()
    {
        return $this->scopeConfig->getValue(
            self::REGION_SEARCHABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is city Searchable
     *
     * @return mixed
     */
    public function isCitySearchable()
    {
        return $this->scopeConfig->getValue(
            self::CITY_SEARCHABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is postcode searchable
     *
     * @return mixed
     */
    public function isPostcodeSearchable()
    {
        return $this->scopeConfig->getValue(
            self::POSTCODE_SEARCHABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is city sortOrder
     *
     * @return mixed
     */
    public function isCitySortOrder()
    {
        return $this->scopeConfig->getValue(
            self::CITY_SORT_ORDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is postcode sortOrder
     *
     * @return mixed
     */
    public function isPostcodeSortOrder()
    {
        return $this->scopeConfig->getValue(
            self::POSTCODE_SORT_ORDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is city active
     *
     * @return mixed
     */
    public function isCityActive()
    {
        return $this->scopeConfig->getValue(
            self::IS_CITY_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is postcode active
     *
     * @return mixed
     */
    public function isPostcodeActive()
    {
        return $this->scopeConfig->getValue(
            self::IS_POSTCODE_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is active
     *
     * @return mixed
     */
    public function isActive()
    {
        return $this->scopeConfig->getValue(
            self::MODULE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
