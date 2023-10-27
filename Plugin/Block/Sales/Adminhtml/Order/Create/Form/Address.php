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

namespace MageINIC\CityRegionPostcode\Plugin\Block\Sales\Adminhtml\Order\Create\Form;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Adminhtml\Order\Create\Form\Address as FormAddress;
use MageINIC\CityRegionPostcode\Block\Adminhtml\Sales\Order\Address\Form\Renderer\CityId as CityIdRenderer;
use MageINIC\CityRegionPostcode\Block\Adminhtml\Sales\Order\Address\Form\Renderer\PostcodeId as PostcodeIdRenderer;

/**
 * CityRegionPostcode Order Create Form Address Class
 */
class Address
{
    /**
     * After GetForm Method
     *
     * @param FormAddress $subject
     * @param $result
     * @return mixed
     * @throws LocalizedException
     */
    public function afterGetForm(
        FormAddress $subject,
        $result
    ) {
        $cityIdElement = $result->getElement('city_id');
        $postcodeIdElement = $result->getElement('postcode_id');
        $cityElement = $result->getElement('city');
        $postcodeElement = $result->getElement('postcode');
        if ($cityIdElement) {
            $cityIdElement->setNoDisplay(true);
            $cityElement->setRenderer($subject->getLayout()->createBlock(
                CityIdRenderer::class
            ));
        }
        if ($postcodeIdElement) {
            $postcodeIdElement->setNoDisplay(true);
            $postcodeElement->setRenderer($subject->getLayout()->createBlock(
                PostcodeIdRenderer::class
            ));
        }
        return $result;
    }
}
