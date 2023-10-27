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

namespace MageINIC\CityRegionPostcode\Model\ResourceModel\Address\Attribute\Backend;

use MageINIC\CityRegionPostcode\Model\CityFactory;
use Magento\Directory\Model\Region;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;

/**
 * Address City attribute backend
 */
class City extends AbstractBackend
{
    /**
     * @var CityFactory
     */
    protected CityFactory $cityFactory;

    /**
     * @param CityFactory $cityFactory
     */
    public function __construct(
        CityFactory $cityFactory
    ) {
        $this->cityFactory = $cityFactory;
    }

    /**
     * Prepare object for save
     *
     * @param DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $cityId = $object->getData('city_id');
        if (is_numeric($cityId)) {
            $cityModel = $this->_createCityInstance();
            $cityModel->load($cityId);
            if ($cityModel->getId() && $object->getRegionId() == $cityModel->getRegionId()) {
                $object->setCityId($cityModel->getId())->setCity($cityModel->getName());
            }
        }
        return $this;
    }

    /**
     * City instance
     *
     * @return Region
     */
    protected function _createCityInstance()
    {
        return $this->cityFactory->create();
    }
}
