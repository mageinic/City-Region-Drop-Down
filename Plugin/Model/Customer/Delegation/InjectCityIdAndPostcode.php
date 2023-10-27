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

namespace MageINIC\CityRegionPostcode\Plugin\Model\Customer\Delegation;

use MageINIC\CityRegionPostcode\Helper\Data as HelperData;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Delegation\Data\NewOperation;
use Magento\Customer\Model\Delegation\Storage;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\CustomAttributesDataInterface;
use MageINIC\CityRegionPostcode\Api\Data\CityInterface;

/**
 * CityRegionPostcode Customer Delegation InjectCityIdAndPostcode Class
 */
class InjectCityIdAndPostcode
{
    /**
     * Put city_id data in correct place after object restore.
     *
     * @param Storage $subject
     * @param NewOperation|null $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterConsumeNewOperation(Storage $subject, $result)
    {
        if (!$result instanceof NewOperation) {
            return $result;
        }

        $customer = $result->getCustomer();

        $this->normalizeDataObjects($customer);
        foreach ($customer->getAddresses() as $address) {
            $this->normalizeDataObjects($address);
        }

        return $result;
    }

    /**
     * Normalize DataObjects
     *
     * @param AddressInterface $address
     * @return void
     */
    private function normalizeDataObjects(AddressInterface $address)
    {
        if (!$address instanceof AbstractSimpleObject || !$address instanceof CustomAttributesDataInterface) {
            return;
        }

        $data = $address->__toArray();
        if (array_key_exists('city_id', $data)) {
            $address->setCustomAttribute('city_id', $data['city_id']);
        }
        if (array_key_exists('postcode_id', $data)) {
            $address->setCustomAttribute('postcode_id', $data['postcode_id']);
        }
    }
}
