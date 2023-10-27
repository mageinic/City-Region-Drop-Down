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

namespace MageINIC\CityRegionPostcode\ViewModel;

use Magento\Framework\App\Cache\Type\Config as CacheConfigType;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * CityRegionPostcode CityViewModel Class
 */
class CityViewModel implements ArgumentInterface
{
    public const IS_ENABLE      ='mageinic_cityregionpostcode/city/enable';
    public const IS_SEARCHABLE  ='mageinic_cityregionpostcode/city/searchable';

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var CacheConfigType
     */
    private CacheConfigType $configCacheType;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    /**
     * @var string|null
     */
    private ?string $cityJson = null ;

    /**
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param CacheConfigType $configCacheType
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        CacheConfigType $configCacheType,
        SerializerInterface $serializer,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager      = $storeManager;
        $this->configCacheType   = $configCacheType;
        $this->serializer        = $serializer;
        $this->scopeConfig       = $scopeConfig;
    }

    /**
     * Is Active
     *
     * @return mixed
     */
    public function isActive()
    {
        return $this->scopeConfig->getValue(
            self::IS_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Searchable
     *
     * @return mixed
     */
    public function isSearchable()
    {
        return $this->scopeConfig->getValue(
            self::IS_SEARCHABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get CityInformation
     *
     * @return array
     */
    public function getCityInformation()
    {
        $collection = $this->collectionFactory->create();
        $collection->getSelect()->order(
            new \Zend_Db_Expr('main_table.country_id, main_table.region_id, main_table.default_name ASC')
        );
        $cities = [];
        foreach ($collection as $city) {
            if (!$city->getRegionId() || !$city->getCityId()) {
                continue;
            }
            $cities[$city->getRegionId()][$city->getCityId()] = [
                'code' => $city->getCode(),
                'name' => (string)__($city->getName()),
                'country_code' => $city->getCountryId()
            ];
        }
        return $cities;
    }

    /**
     * Get CitySerializeValues
     *
     * @return bool|string
     * @throws NoSuchEntityException
     */
    public function getCitySerializeValues()
    {
        if (!$this->cityJson) {
            $cacheKey = 'MAGEINIC_CITYREGIONPOSTCODE_CITY_JSON_STORE' . $this->storeManager->getStore()->getId();
            $json = $this->configCacheType->load($cacheKey);
            if (empty($json)) {
                $cityData = $this->getCityInformation();
                $json = $this->serializer->serialize($cityData);
                if ($json === false) {
                    $json = 'false';
                }
                $this->configCacheType->save($json, $cacheKey);
            }
            $this->cityJson = $json;
        }
        return $this->cityJson;
    }
}
