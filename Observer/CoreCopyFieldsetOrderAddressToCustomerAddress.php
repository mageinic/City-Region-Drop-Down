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

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;
use \Magento\Framework\Api\AttributeValueFactory;
use MageINIC\CityRegionPostcode\Api\Data\CityInterface;
use MageINIC\CityRegionPostcode\Api\Data\PostcodeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Address;

/**
 * CityRegionPostcode Observer CoreCopyFieldsetOrderAddressToCustomerAddress Class
 */
class CoreCopyFieldsetOrderAddressToCustomerAddress implements ObserverInterface
{
    /**
     * @var AttributeValueFactory
     */
    private AttributeValueFactory $attributeValueFactory;

    /**
     * @param AttributeValueFactory $attributeValueFactory
     */
    public function __construct(
        AttributeValueFactory $attributeValueFactory
    ) {
        $this->attributeValueFactory = $attributeValueFactory;
    }

    /**
     * Convert order address's city_id to the customer address
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $source = $observer->getEvent()->getSource();
        $target = $observer->getEvent()->getTarget();

        $this->transferCityData($source, $target);
        $this->transferPostcodeData($source, $target);

        return $this;
    }

    /**
     * Transfer CityData
     *
     * @param Address $source
     * @param DataObject $target
     * @return void
     */
    private function transferCityData(Address $source, DataObject $target)
    {
        $attributeValue = $this->attributeValueFactory->create();
        $attributeValue->setAttributeCode(CityInterface::ID)
            ->setValue($source->getCityId());

        $target->setData(CityInterface::ID, $source->getCityId());
        $target->setData(
            CustomAttributesDataInterface::CUSTOM_ATTRIBUTES,
            [
                CityInterface::ID => $attributeValue
            ]
        );
    }

    /**
     * Transfer postcodeData
     *
     * @param Address $source
     * @param DataObject $target
     * @return void
     */
    private function transferPostcodeData(Address $source, DataObject $target)
    {
        $attributeValue = $this->attributeValueFactory->create();
        $attributeValue->setAttributeCode(PostcodeInterface::ID)
            ->setValue($source->getCityId());

        $target->setData(PostcodeInterface::ID, $source->getPostcodeId());
        $target->setData(
            CustomAttributesDataInterface::CUSTOM_ATTRIBUTES,
            [
                PostcodeInterface::ID => $attributeValue
            ]
        );
    }
}
