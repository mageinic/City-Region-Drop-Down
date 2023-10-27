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

namespace MageINIC\CityRegionPostcode\Block\Customer\Address\Edit;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

/**
 * CityRegionPostcode Customer Address Edit Js Class
 */
class Js extends Template
{
    public const CUSTOMER_ADDRESS_EDIT_BLOCK_NAME = 'customer_address_edit';

    /**
     * @var LayoutInterface
     */
    private LayoutInterface $currentLayout;

    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->currentLayout = $context->getLayout();
    }

    /**
     * This method should not be overridden. You can override _toHtml() method in descendants if needed.
     *
     * @return string
     */
    public function toHtml()
    {
        return parent::_toHtml();
    }

    /**
     * Get CityId
     *
     * @return int
     */
    public function getCityId()
    {
        $customerAddress = $this->getCurrentAddress();
        if (!$customerAddress || !$customerAddress->getId()) {
            return 0;
        }

        return $customerAddress->getCustomAttribute('city_id')
            ? $customerAddress->getCustomAttribute('city_id')->getValue()
            : 0;
    }

    /**
     * Get Current Address
     *
     * @return false
     */
    private function getCurrentAddress()
    {
        $customerAddressBlock = $this->currentLayout->getBlock(self::CUSTOMER_ADDRESS_EDIT_BLOCK_NAME);
        if (!$customerAddressBlock) {
            return false;
        }
        return $customerAddressBlock->getAddress();
    }

    /**
     * Get PostcodeId
     *
     * @return int
     */
    public function getPostcodeId()
    {
        $customerAddress = $this->getCurrentAddress();
        if (!$customerAddress || !$customerAddress->getId()) {
            return 0;
        }

        return $customerAddress->getCustomAttribute('postcode_id')
            ? $customerAddress->getCustomAttribute('postcode_id')->getValue()
            : 0;
    }
}
