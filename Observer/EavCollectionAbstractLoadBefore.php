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

namespace MageINIC\CityRegionPostcode\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\ResourceModel\Address\Collection as AddressCollection;
use Magento\Framework\Exception\LocalizedException;

/**
 * CityRegionPostcode Observer EavCollectionAbstractLoadBefore Class
 */
class EavCollectionAbstractLoadBefore implements ObserverInterface
{
    /**
     * Add city_id to the address collection
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($collection instanceof AddressCollection) {
            $collection->addAttributeToSelect('city_id');
        }
    }
}
