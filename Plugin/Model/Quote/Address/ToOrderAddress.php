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

namespace MageINIC\CityRegionPostcode\Plugin\Model\Quote\Address;

use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Quote\Model\Quote\Address\ToOrderAddress as CoreToOrderAddress;

/**
 * CityRegionPostcode Quote Address To Order Address Class
 */
class ToOrderAddress
{
    /**
     * Around Convert
     *
     * @param CoreToOrderAddress $subject
     * @param \Closure $proceed
     * @param QuoteAddressInterface $quoteAddress
     * @param array $data
     * @return mixed
     */
    public function aroundConvert(
        CoreToOrderAddress $subject,
        \Closure $proceed,
        QuoteAddressInterface $quoteAddress,
        $data = []
    ) {
        $cityId = $quoteAddress->getData('city_id');
        $orderAddress = $proceed($quoteAddress, $data);
        if ($cityId) {
            $orderAddress->setData(
                'city_id',
                $cityId
            );
        }
        $postcodeId = $quoteAddress->getData('postcode_id');
        $orderAddress = $proceed($quoteAddress, $data);
        if ($postcodeId) {
            $orderAddress->setData(
                'postcode_id',
                $postcodeId
            );
        }
        return $orderAddress;
    }
}
