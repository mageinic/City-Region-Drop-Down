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

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Directory\Model\ResourceModel\Region as CoreRegion;

/**
 * CityRegionPostcode Region ResourceModel Class
 */
class Region extends CoreRegion
{
    /**
     * Perform operations before object save
     *
     * @param AbstractModel $object
     * @return $this
     * @throws AlreadyExistsException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($this->checkRegionExistenceByName($object)) {
            throw new AlreadyExistsException(
                __('Region with name "%1" already exists in the country.', $object->getData('default_name'))
            );
        }

        if ($this->checkRegionExistenceByCode($object)) {
            throw new AlreadyExistsException(
                __('Region with code "%1" already exists in the country.', $object->getData('code'))
            );
        }
    }

    /**
     * Check RegionExistenceByName
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function checkRegionExistenceByName($object)
    {
        return $this->checkRegionExistenceByField($object, 'default_name');
    }

    /**
     * Check RegionExistenceByCode
     *
     * @param AbstractModel $object
     * @return bool
     */
    protected function checkRegionExistenceByCode($object)
    {
        return $this->checkRegionExistenceByField($object, 'code');
    }

    /**
     * Check RegionExistenceByField
     *
     * @param AbstractModel $object
     * @param string $field
     * @return bool
     * @throws LocalizedException
     */
    protected function checkRegionExistenceByField(AbstractModel $object, $field)
    {
        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getMainTable()])
            ->where('main_table.country_id = ?', $object->getData('country_id'))
            ->where('main_table.' . $field . ' = ?', $object->getData($field));
        if ($object->getData('region_id')) {
            $select->where('main_table.region_id <> ?', $object->getData('region_id'));
        }
        if ($this->getConnection()->fetchRow($select)) {
            return true;
        }
        return false;
    }
}
