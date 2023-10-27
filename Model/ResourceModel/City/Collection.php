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

namespace MageINIC\CityRegionPostcode\Model\ResourceModel\City;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

/**
 * CityRegionPostcode ResourceModel City Collection class
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'city_id';

    /**
     * Event prefix City Collection
     *
     * @var string
     */
    protected $_eventPrefix = 'city_city_collection';
    /**
     * Event object for City Collection
     *
     * @var string
     */
    protected $_eventObject = 'city_collection';
    /**
     * Locale region name table name
     *
     * @var string
     */
    protected string $cityTableName;
    /**
     * @var ResolverInterface
     */
    protected ResolverInterface $localeResolver;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ResolverInterface $localeResolver
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ResolverInterface $localeResolver,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->localeResolver = $localeResolver;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \MageINIC\CityRegionPostcode\Model\City::class,
            \MageINIC\CityRegionPostcode\Model\ResourceModel\City::class
        );
        $this->cityTableName = $this->getTable('directory_country_region_city_name');
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->addFilterToMap('city_id', 'main_table.city_id');

        parent::_initSelect();
        $locale = $this->localeResolver->getLocale();

        $this->addBindParam(':locale', $locale);
        $this->getSelect()->joinLeft(
            ['city_tbl' => $this->cityTableName],
            'main_table.city_id = city_tbl.city_id AND city_tbl.locale = :locale',
            ['name']
        );

        return $this;
    }

    /**
     * Add country filter
     *
     * @param array $countryIds
     * @return $this
     */
    public function addCountryFilter($countryIds)
    {
        $this->addFieldToFilter('country_id', $countryIds);
        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $propertyMap = [
            'value'         => 'city_id',
            'title'         => 'default_name',
            'country_id'    => 'country_id',
            'region_id'     => 'region_id'
        ];

        foreach ($this as $item) {
            $option = [];
            foreach ($propertyMap as $code => $field) {
                $option[$code] = $item->getData($field);
            }
            $option['label'] = $item->getName();
            $options[] = $option;
        }

        if (count($options) > 0) {
            array_unshift(
                $options,
                ['title' => '', 'value' => '', 'label' => __('Please select a region, state or province.')]
            );
        }
        return $options;
    }
}
