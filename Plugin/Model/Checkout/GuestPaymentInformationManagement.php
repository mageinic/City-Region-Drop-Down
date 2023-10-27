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

use Magento\Checkout\Model\GuestPaymentInformationManagement as CoreGuestPaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * CityRegionPostcode Checkout GuestPaymentInformationManagement Class
 */
class GuestPaymentInformationManagement
{
    /**
     * Around savePaymentInformationAndPlaceOrder
     *
     * @param CoreGuestPaymentInformationManagement $subject
     * @param \Closure $proceed
     * @param string $cartId
     * @param string $email
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @return mixed
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        CoreGuestPaymentInformationManagement $subject,
        \Closure $proceed,
        string $cartId,
        string $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        if ($billingAddress) {
            $extensionAttributes = $billingAddress->getExtensionAttributes();
            if ($extensionAttributes) {
                $cityId = (int) $extensionAttributes->getCityId();
                if ($cityId) {
                    $billingAddress->setCityId($cityId);
                }
                $postcodeId = (int) $extensionAttributes->getPostcodeId();
                if ($postcodeId) {
                    $billingAddress->setPostcodeId($postcodeId);
                }
            }
        }
        return $proceed($cartId, $email, $paymentMethod, $billingAddress);
    }
}
