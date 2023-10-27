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

namespace MageINIC\CityRegionPostcode\Model\ResourceModel;

use Magento\Framework\AppInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use MageINIC\CityRegionPostcode\Model\Config\Source\Locale;
use MageINIC\CityRegionPostcode\Model\City as CityModel;

/**
 * CityRegionPostcode ResourceModel City Class
 */
class City extends AbstractDb
{
    /**
     * @var Resolver
     */
    protected Resolver $localeResolver;
    /**
     * @var Locale
     */
    protected Locale $locale;
    /**
     * @var string|null
     */
    private ?string $cityLocaleTableName = null;
    /**
     * @var string|null
     */
    private ?string $cityTableName = null;
    /**
     * @var string|null
     */
    private ?string $regionLocaleTableName = null;

    /**
     * @param Context $context
     * @param Resolver $localeResolver
     * @param Locale $locale
     * @param string $connectionName
     */
    public function __construct(
        Context  $context,
        Resolver $localeResolver,
        Locale   $locale,
        $connectionName = null
    ) {
        $this->localeResolver = $localeResolver;
        $this->locale  = $locale;
        parent::__construct($context, $connectionName);
    }

    /**
     * Load By CityCode
     *
     * @param CityModel $cityObject
     * @param int $regionId
     * @param string $cityCode
     * @return $this
     * @throws LocalizedException
     */
    public function loadByCityCode(
        CityModel $cityObject,
        int $regionId,
        string $cityCode
    ) {
        return $this->_loadByRegion($cityObject, $regionId, $cityCode, 'code');
    }

    /**
     * Lode By Region
     *
     * @param CityModel $object
     * @param int $regionId
     * @param string $value
     * @param string $fieldName
     * @return $this
     * @throws LocalizedException
     */
    private function _loadByRegion($object, $regionId, $value, $fieldName)
    {
        $connection = $this->getConnection();
        $locale = $this->localeResolver->getLocale();

        $condition = $connection->quoteInto('cname.city_id = city.city_id AND cname = ?', $locale);
        $select = $connection->select()->from(
            ['city' => $this->getMainTable()]
        )->joinLeft(
            ['cname' => $this->cityTableName],
            $condition,
            ['name']
        )->where(
            'city.region_id = ?',
            $regionId
        )->where(
            "city.{$fieldName}",
            $value
        );

        $data = $connection->fetchRow($select);
        if ($data) {
            $object->setData($object);
        }

        $this->_afterLoad($object);
        return $this;
    }

    /**
     * Loads city by city code and region id
     *
     * @param CityModel $city
     * @param string $cityCode
     * @param string $regionId
     *
     * @return $this
     */
    public function loadByCode(CityModel $city, string $cityCode, string $regionId)
    {
        return $this->_loadByRegion($city, $regionId, $cityCode, 'code');
    }

    /**
     * Load data by region id and default region name
     *
     * @param CityModel $city
     * @param string $cityName
     * @param string $regionId
     * @return $this
     */
    public function loadByName(CityModel $city, string $cityName, string $regionId)
    {
        return $this->_loadByRegion($city, $regionId, $cityName, 'default_name');
    }

    /**
     * Save RegionLocales
     *
     * @param string $regionId
     * @param array $postedLocales
     * @return $this|void
     * @throws LocalizedException
     */
    public function saveRegionLocales(string $regionId, array $postedLocales)
    {
        try {

            if (!is_array($postedLocales) || empty($postedLocales)) {
                return;
            }

            $postedLocales = array_filter($postedLocales, function ($localeName) {
                return strlen($localeName);
            });
            $oldRegionLocales = $this->getRegionLocales($regionId);
            $insert = array_diff_key($postedLocales, $oldRegionLocales);
            $delete = array_diff_key($oldRegionLocales, $postedLocales);

            $update = array_intersect_key($postedLocales, $oldRegionLocales);
            $update = array_diff_assoc($update, $oldRegionLocales);

            $connection = $this->getConnection();
            if (!empty($delete)) {
                $cond = ['locale IN (?)' => array_keys($delete), 'region_id = ?' => $regionId];
                $connection->delete($this->getRegionLocaleTableName(), $cond);
            }
            if (!empty($insert)) {
                $data = [];
                foreach ($insert as $locale => $name) {
                    $data[] = [
                        'region_id' => (int)$regionId,
                        'locale' => $locale,
                        'name' => $name,
                    ];
                }
                $connection->insertMultiple($this->getRegionLocaleTableName(), $data);
            }

            if (!empty($update)) {
                foreach ($update as $locale => $name) {
                    $bind = ['name' => $name];
                    $where = ['region_id = ?' => (int)$regionId, 'locale = ?' => $locale];
                    $connection->update($this->getRegionLocaleTableName(), $bind, $where);
                }
            }
        } catch (\Exception $exception) {
            throw new LocalizedException(
                __('Unable to save region locales')
            );
        }

        return $this;
    }

    /**
     * Get RegionLocales
     *
     * @param string $regionId
     * @return array
     */
    public function getRegionLocales(string $regionId)
    {
        $locales = $this->locale->toOptionArray();
        $localeCodes = [];
        foreach ($locales as $locale) {
            $localeCodes[] = $locale['value'];
        }
        $select = $this->getConnection()->select()->from(
            $this->getRegionLocaleTableName(),
            ['locale', 'name']
        )->where(
            'region_id = :region_id'
        )->where(
            'locale IN (?)',
            $localeCodes
        );
        $bind = ['region_id' => (int)$regionId];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * Get RegionLocaleTableName
     *
     * @return string
     */
    public function getRegionLocaleTableName()
    {
        if (!$this->regionLocaleTableName) {
            $this->regionLocaleTableName = $this->getTable('directory_country_region_name');
        }
        return $this->regionLocaleTableName;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_init('directory_country_region_city', 'city_id');
        $this->cityTableName = $this->getTable('directory_country_region_city_name');
    }

    /**
     * Perform operations before object save
     *
     * @param AbstractModel $object
     * @return $this
     * @throws AlreadyExistsException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($this->checkCityExistenceByName($object)) {
            throw new AlreadyExistsException(
                __('City with name "%1" already exists in the region.', $object->getData('default_name'))
            );
        }

        if (strlen($object->getData('code')) && $this->checkCityExistenceByCode($object)) {
            throw new AlreadyExistsException(
                __('City with code "%1" already exists in the region.', $object->getData('code'))
            );
        }
    }

    /**
     * Check CityExistenceByName
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function checkCityExistenceByName($object)
    {
        return $this->checkCityExistenceByField($object, 'default_name');
    }

    /**
     * Check CityExistenceByField
     *
     * @param AbstractModel $object
     * @param string $field
     * @return bool
     * @throws LocalizedException
     */
    protected function checkCityExistenceByField(AbstractModel $object, string $field)
    {
        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getMainTable()])
            ->where('main_table.country_id = ?', $object->getData('country_id'))
            ->where('main_table.region_id = ?', $object->getData('region_id'))
            ->where('main_table.' . $field . ' = ?', $object->getData($field));
        if ($object->getData('city_id')) {
            $select->where('main_table.city_id <> ?', $object->getData('city_id'));
        }
        if ($this->getConnection()->fetchRow($select)) {
            return true;
        }
        return false;
    }

    /**
     * Check CityExistenceByCode
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function checkCityExistenceByCode($object)
    {
        return $this->checkCityExistenceByField($object, 'code');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param AbstractModel $object
     * @return Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $connection = $this->getConnection();
        $locale = $this->localeResolver->getLocale();
        $systemLocale = AppInterface::DISTRO_LOCALE_CODE;
        $cityField = $connection->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());
        $condition = $connection->quoteInto('lng.locale = ?', $locale);
        $select->joinLeft(
            ['lng' => $this->cityTableName],
            "{$cityField} = lng.city_id AND {$condition}",
            []
        );

        if ($locale != $systemLocale) {
            $nameExpr = $connection->getCheckSql('lng.city_id IS NULL', 'slng.name', 'lng.name');
            $condition = $connection->quoteInto('slng.locale = ?', $systemLocale);
            $select->joinLeft(
                ['slng' => $this->cityTableName],
                "{$cityField} = slng.city_id AND {$condition}",
                ['name' => $nameExpr]
            );
        } else {
            $select->columns(['name'], 'lng');
        }
        return $select;
    }

    /**
     * Processing object after save data
     *
     * @param AbstractModel $object
     * @return City
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveCityLocales($object);
        return parent::_afterSave($object);
    }

    /**
     * Save CityLocales
     *
     * @param AbstractModel $city
     * @return $this|void
     */
    private function saveCityLocales(AbstractModel $city)
    {
        $id = $city->getId();
        $localeNames = $city->getPostLocaleNames();
        if (!is_array($localeNames) || empty($localeNames)) {
            return;
        }

        $oldLocaleNames = $city->getLocaleNames();
        $insert = array_diff_key($localeNames, $oldLocaleNames);
        $delete = array_diff_key($oldLocaleNames, $localeNames);

        $update = array_intersect_key($localeNames, $oldLocaleNames);
        $update = array_diff_assoc($update, $oldLocaleNames);
        $connection = $this->getConnection();

        if (!empty($delete)) {
            $cond = ['locale IN (?)' => array_keys($delete), 'city_id = ?' => $id];
            $connection->delete($this->getCityLocaleTable(), $cond);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $locale => $name) {
                $data[] = [
                    'city_id' => (int)$id,
                    'locale' => $locale,
                    'name' => $name,
                ];
            }
            $connection->insertMultiple($this->getCityLocaleTable(), $data);
        }
        if (!empty($update)) {
            foreach ($update as $locale => $name) {
                $bind = ['name' => $name];
                $where = ['city_id = ?' => (int)$id, 'locale  = ?' => $locale];
                $connection->update($this->getCityLocaleTable(), $bind, $where);
            }
        }
        return $this;
    }

    /**
     * Get LocaleNames
     *
     * @param $city
     * @return array
     */
    public function getLocaleNames($city)
    {
        $select = $this->getConnection()->select()->from(
            $this->getCityLocaleTable(),
            ['locale', 'name']
        )->where(
            'city_id = :city_id'
        );
        $bind = ['city_id' => (int)$city->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * Get CityLocaleTable
     *
     * @return string
     */
    public function getCityLocaleTable()
    {
        if (!$this->cityLocaleTableName) {
            $this->cityLocaleTableName = $this->getTable('directory_country_region_city_name');
        }
        return $this->cityLocaleTableName;
    }
}
