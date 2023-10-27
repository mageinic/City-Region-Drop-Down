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

namespace MageINIC\CityRegionPostcode\Plugin\Model\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement as CoreShippingInformationManagement;

/**
 * CityRegionPostcode Checkout ShippingInformationManagement Class
 */
class ShippingInformationManagement
{
    /**
     * Before SaveAddressInformation
     *
     * @param CoreShippingInformationManagement $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     */
    public function beforeSaveAddressInformation(
        CoreShippingInformationManagement $subject,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ) {

        $shippingAddress = $addressInformation->getShippingAddress();
        $extensionAttributes = $shippingAddress->getExtensionAttributes();
        if ($extensionAttributes) {
            $cityId = (int) $extensionAttributes->getCityId();
            if ($cityId) {
                $shippingAddress->setCityId($cityId);
            }
            $postcodeId = (int) $extensionAttributes->getPostcodeId();
            if ($postcodeId) {
                $shippingAddress->setPostcodeId($postcodeId);
            }
        }
        return [$cartId, $addressInformation];
    }
}
